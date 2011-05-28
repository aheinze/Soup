<?php

namespace Raww\Connection;

class Mongo {
    
    protected $conn;
    public $db;
    
    /**
    * ...
    *
    */ 
    public function __construct($config){
      
        $config = array_merge(array(
            'dns' => 'mongodb://localhost',
            'db'  => 'test',
            'options' => array("connect" => true),
            'persist' => false,
            'garbage' => false
        ), $config);

      extract($config);
      
      try {
        
        $this->conn = new \Mongo($dns,$options,$persist,$garbage);
        $this->db   = $this->conn->{$db};
        
      }catch( MongoException $Exception ) {
        trigger_error('MongoDB Connect failed: '.$Exception->getMessage(),E_USER_ERROR);
      }
    }
    
    /**
    * ...
    *
    */ 
    public function getTables() {
      
      $tables = array();
      
      foreach($this->db->listCollections() as $col){
        $tables[] = $col->getName();
      }
      
      return $tables;
    }
    
    /**
    * ...
    *
    */ 
    public function getColumns($table) {
      return array('_id'=>'string', 'created'=>'integer');
    }
    
    /**
    * ...
    *
    * @return ?
    */
    public function field($options){    
      return false;
    }
    
    /**
    * ...
    *
    * @return ?
    */
    public function create($table,$data){    
      
      $data['created'] = time();

      if($this->db->{$table}->insert($data)){
        return $data['_id']->__toString();
      }else{
        return false;
      }
    }
    
    /**
    * ...
    *
    * @return ?
    */
    public function delete($table,$conditions){    
      
        if(is_string($conditions) && strlen($conditions)==24){
            $conditions = array('_id' => (new \MongoId($conditions)));
        }
      
        if($this->db->{$table}->remove($conditions)){
            return true;
        }else{
            return false;
        }
    }
    
    /**
    * ...
    *
    * @return ?
    */
    public function update($table,$data,$conditions=array()){    
        
        if(is_string($conditions) && strlen($conditions)==24){
            $conditions = array('_id' => (new \MongoId($conditions)));
        }
        
        if($this->db->{$table}->update($conditions, array('$set' => $data))){
            return true;
        }else{
            return false;
        }
    }
    
    /**
    * ...
    *
    * @return ?
    */
    public function truncate($table){    
      return $this->db->{$table}->drop();
    }
    
    /**
    * ...
    *
    * @return ?
    */
    public function execute($data){    
      
    }
    
    /**
    * ...
    *
    * @return ?
    */
    public function find($options){  
                
        $options = array_merge(array(
          'conditions' => array(), //array of conditions
          'table'      => '',
          'joins'      => array(),
          'fields'     => array(), //array of field names
          'order'      => array(), //string or array defining order
          'group'      => array(), //fields to GROUP BY
          'limit'      => null, //int
          'page'       => null, //int
          'offset'     => null
        ),$options);
        
        extract($options);
        
        $cursor = $this->db->{$table}->find($conditions, $fields)->sort($order)->limit($limit)->skip($offset);
        
        $result = array();
        
        foreach(iterator_to_array($cursor) as $id => $record){
            $record['_id'] = $id;
            $result[]      = $record;
        }
        
        return $result;
    }
    
    public function read($table, $options=array()){  
        
        if(is_string($options) && strlen($options)==24){
            $options = array('_id' => (new \MongoId($options)));
        }

        $options['table'] = $table;
        $options['limit'] = 1;
        
        $result = $this->find($options);
        
        return count($result) ? $result[0]:null;
    }
    
    /**
    * ...
    *
    * @return ?
    */
    public function __get($name){
        return $this->db->{$name};
    }
}