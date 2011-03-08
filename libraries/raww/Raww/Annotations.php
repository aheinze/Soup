<?php

namespace Raww;

/*
    based on https://github.com/marcelog/Ding/blob/master/src/mg/Ding/Reflection/ReflectionFactory.php
*/
class Annotations {
    
    protected static $_cache = array();


    public static function getClass($class) {
        
        if (isset(self::$_cache[$class])) {
            return self::$_cache[$class];
        }

        $reflectionClass = new \ReflectionClass($class);

        $ret = array(
            'class' => array(),
            'properties' => array(),
            'methods' => array()
        );

        $ret['class'] = self::getAnnotations($reflectionClass->getDocComment());

        foreach ($reflectionClass->getProperties() as $property) {
            $propertyName = $property->getName();
            $ret['properties'][$propertyName] = self::getAnnotations($property->getDocComment());
        }

        foreach ($reflectionClass->getMethods() as $method) {
            $methodName = $method->getName();
            $ret['methods'][$methodName] = self::getAnnotations($method->getDocComment());
        }

        self::$_cache[$class] = $ret;

        return self::$_cache[$class];
    }

    public static function getAnnotations($text){

        $ret = array();

        if (preg_match_all('/@[\/a-zA-Z0-9=,\(\)\ ]+/', $text, $matches) > 0) {
            foreach ($matches[0] as $annotation) {
                $argsStart = strpos($annotation, '(');
                $arguments = array();
                if ($argsStart !== false) {
                    $name = trim(substr($annotation, 1, $argsStart - 1));
                    $args = substr($annotation, $argsStart + 1, -1);
                    // http://stackoverflow.com/questions/168171/regular-expression-for-parsing-name-value-pairs
                    $argsN = preg_match_all(
                        '/([^=,]*)=("[^"]*"|[^,"]*)/', $args, $matches
                    );
                    if ($argsN > 0) {
                        for ($i = 0; $i < $argsN; $i++) {
                            $key = trim($matches[1][$i]);
                            $value = trim($matches[2][$i]);
                            $arguments[$key] = $value;
                        }
                    }
                } else {
                    $stuff = explode(' ', $annotation);
                    $name = substr($stuff[0], 1);
                    $arguments[] = $stuff;
                }

                if(isset($rest[$name])){
                    $rest[$name] = array();
                }
                $ret[$name][] = $arguments;
            }
        }

        return $ret;
    }
}