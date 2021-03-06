<?php

namespace Soup\Connection;


class Pdo {

    public $pdo = null;
    public $log = array();
    
    public function __construct($config){
      
        $config = array_merge(array(
            'dns' => '',
            'user' => '',
            'password' => '',
            'options' => array()
        ), $config);

      extract($config);
        
      extract($config);
      
      $this->pdo = new \PDO($dns,$user,$password,$options);
      
    }
    
    public function create($table,$data){    
      
        $fields = array();
        $values = array();

        foreach($data as $col=>$value){
            
            if(!is_null($value) && (is_array($value) || is_object($value))){
              $value = json_encode($value, JSON_NUMERIC_CHECK);
            }

            $fields[] = $col;
            $values[] = (is_null($value) ? 'NULL':$this->pdo->quote($value));
        }
        
        $fields = implode(',', $fields);
        $values = implode(',', $values);

        $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$values})";

        $this->log['queries'][] = $sql;

        $res = $this->pdo->exec($sql);

        if($res){
            return $this->pdo->lastInsertId();
        }else{
            trigger_error('SQL Error: '.implode(', ',$this->pdo->errorInfo()).":\n".$sql);
            return false;
        }
    }
    
    public function update($table,$data,$conditions=array()){    

      $conditions = $this->buildConditions($conditions);
      
      if(strlen(trim($conditions))>0) $conditions = "WHERE ".$conditions;
      
      $fields = array();
      
      foreach($data as $col=>$value){
        
        if(!is_null($value) && (is_array($value) || is_object($value))){
          $value = json_encode($value, JSON_NUMERIC_CHECK);
        }

        $fields[] = $col.'='.(is_null($value) ? 'NULL':$this->pdo->quote($value));
      }
      
      $fields = implode(',', $fields);
      
      $sql = "UPDATE ".$table." SET {$fields} {$conditions}";
      
      $this->log['queries'][] = $sql;
      
      if($this->pdo->exec($sql)){
      
      }else{
        $errorInfo = $this->pdo->errorInfo();
        if($errorInfo[0]!='00000'){
            trigger_error('SQL Error: '.implode(', ',$errorInfo).":\n".$sql);
            return false;
        }
      }
      
    }
    
    public function count($table, $conditions = array()) {
        
        $count = $this->read(array(
                'fields' => 'COUNT(*) AS C',
                'table'  => $table,
                'conditions' => $conditions
        ));
     
        return isset($count[0]['C']) ? $count[0]['C']:0;
    }
    
    public function sum($table, $field, $conditions = array()) {
        
        $sum = $this->read(array(
                'fields' => "SUM($field) AS S",
                'table'  => $table,
                'conditions' => $conditions
        ));
     
        return isset($sum[0]['S']) ? $sum[0]['S']:0;
    }
    
    public function max($table, $field, $conditions = array()) {
        
        $sum = $this->read(array(
                'fields' => "Max($field) AS m",
                'table'  => $table,
                'conditions' => $conditions
        ));
     
		    return isset($sum[0]['m']) ? $sum[0]['m']:0;
    }

    public function field($table, $field, $conditions = array()) {
        
        $r = $this->read(array(
                'fields' => $field,
                'table'  => $table,
                'conditions' => $conditions
        ));
     
        return isset($r[$table][$field]) ? $r[$table][$field]:null;
    }
    
    public function read($options = array()) {
        
        $options['limit'] = 1;
        $result =  $this->find($options);

        return count($result) ? $result[0]:false;
    }
    
    public function find($options){  
                
        $options = array_merge(array(
          'conditions' => array(), //array of conditions
          'having'     => array(), //array of conditions
          'table'      => array(),
          'joins'      => array(),
          'fields'     => array('*'), //array of field names
          'order'      => array(), //string or array defining order
          'group'      => array(), //fields to GROUP BY
          'limit'      => null, //int
          'page'       => null, //int
          'offset'     => null
        ),$options);
        
        extract($options);

        if(is_array($fields)) $fields = implode(', ', $fields);
        if(is_array($table))  $table  = implode(', ', $table);
        if(is_array($group))  $group  = implode(', ', $group);
        if(is_array($order))  $order  = implode(', ', $order);

        $conditions = $this->buildConditions($conditions);
        $having     = $this->buildConditions($having);
        
        switch(strtolower($this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME))) {
            case 'mysql':
                $mysql_limit = function() use($limit, $offset){
                  if ($limit) {
                    $rt = '';
                    if (!strpos(strtolower($limit), 'limit') || strpos(strtolower($limit), 'limit') === 0) {
                      $rt = ' LIMIT';
                    }
                    if ($offset) { $rt .= ' ' . $offset . ','; }
                    $rt .= ' ' . $limit;
                    
                    return $rt;
                  }
                  return null;
                };
                
                $limit = $mysql_limit();
                break;
                
            case 'sqlite':
            
                $sqlite_limit = function() use($limit, $offset){
                  if ($limit) {
                    $rt = '';
                    if (!strpos(strtolower($limit), 'limit') || strpos(strtolower($limit), 'limit') === 0) {
                      $rt = ' LIMIT';
                    }
                    $rt .= ' ' . $limit;
                    if ($offset) { $rt .= ' OFFSET ' . $offset; }
                    
                    return $rt;
                  }
                  return null;
                };
                
                $limit = $sqlite_limit();
                break;
        }

        //build joins
        
        $_joins     = array();
        
        if(is_string($joins)){
          $_joins = array($joins);
        }else{
          if(count($joins)){
             foreach($joins as $j){
               if(is_string($j)){
                 $_joins = $j;
               }else{
                $_joins[] = strtoupper($j['type']).' JOIN '.$j['table'].' '.$j['alias'].' ON('.implode(' AND ', $j['conditions']).')';
               }
             }
          }
        }
        
        $joins = implode(' ', $_joins);
        
       if(strlen(trim($conditions))>0) $conditions = "WHERE ".$conditions;
       if(strlen(trim($group))>0) $group = "GROUP BY ".$group;
       if(strlen(trim($having))>0) $having = "HAVING ".$conditions;
       if(strlen(trim($fields))==0) $fields = "*";
       if(strlen(trim($order))>0) $order = "ORDER BY ".$order;
        
       $sql = "SELECT {$fields} FROM {$table} {$joins} {$conditions} {$group} {$having} {$order} {$limit}";
       
       $this->log['queries'][] = $sql;
       
       return $this->fetchAll($sql);

    }
    
    public function delete($table,$conditions){    
      
      
      $conditions = $this->buildConditions($conditions);
      
      if(strlen(trim($conditions))>0) $conditions = "WHERE ".$conditions;
      
      $sql = "DELETE FROM {$table} {$conditions}";
      
      $this->log['queries'][] = $sql;
      
      $res = $this->pdo->exec($sql);
      
      if($res || $res===0){
        return true;
      }else{
        trigger_error('SQL Error: '.implode(', ',$this->pdo->errorInfo()).":\n".$sql);
        return false;
      }
    }
    
    public function fetchAll($sql){
      
      $ret_result = array();
      
      if($stmt = $this->pdo->query($sql)){
      
        $meta = array();

        foreach(range(0, $stmt->columnCount() - 1) as $column_index){
          $meta[] = $stmt->getColumnMeta($column_index);
        }
        
        $rows = $stmt->fetchAll(\PDO::FETCH_NUM);

        foreach($rows as &$r){  
          $rec = array();
          for($i=0,$max=count($r);$i<$max;$i++){            
            
            //check if is json
            if(substr($r[$i], 0,1)=="{" && substr($r[$i], -1,1)=="}"){
              if($dec = json_decode($r[$i], true)){
                $r[$i] = $dec;
              }
            }



            $tabeleName = (strlen($meta[$i]['table'])!=0) ? $meta[$i]['table']:0;
            
            $rec[$tabeleName][$meta[$i]['name']] = $r[$i]; 
          }
          $ret_result[] = $rec;
        }
      }else{
         trigger_error('SQL Error: '.implode(', ',$this->pdo->errorInfo()).":\n".$sql,E_USER_ERROR);
      }

      return $ret_result;
    }

    public function fetchOne($sql){
        
        $result = $this->fetchAll($sql);

        return isset($result[0]) ? $result[0]:null;

    }
  
    protected function buildConditions($conditions){
        
        if(is_string($conditions)) $conditions = array($conditions);
        
        $_conditions = array();
        
        if(count($conditions)){
          
          $_bindParams = array();

          foreach($conditions as $c){
            
            $sql = '';
            
            if(is_array($c)){
              
              $sql = $c[0];
              
              foreach($c[1] as $key=>$value){
                $sql = str_replace(':'.$key,$this->pdo->quote($value),$sql);
              }
            }else{
              $sql= $c;
            }

            if(count($_conditions) > 0  && strtoupper(substr($sql,0,4))!='AND ' && strtoupper(substr($sql,0,3))!='OR '){
              $sql = 'AND '.$sql;
            }
            
            $_conditions[] = $sql;
            
          }
          
        }
        
       $conditions = implode(' ', $_conditions);
       
       return $conditions;
    }

    protected function formatSql($sql) {
      return preg_replace('/(select|from|(left |right |natural |inner |outer |cross |straight_)*join|where|limit|update|set|insert|values)/i', "\n$1\n\t",  preg_replace('/\s+/', ' ', $sql));
    }
}