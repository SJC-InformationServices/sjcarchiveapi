<?php

namespace storedd\models;
class base extends \RedBean_SimpleModel
{
    /*function __get($property) {
        if(property_exists($this, $property)){
            if(method_exists($this,"get$property")){
                $method = "get$property";
                return $this->$method();
            }else{
            return $this->$property;
            }
        }
        else {
            return null;
        }
      }
    function __set($property, $value)
      {  
        if(property_exists($this, $property))
        {
            if(method_exists($this,"set$property")){
                $method = "set$property";
                return $this->$method($value);
            }else{
                $this->$property=$value;
            }
        }
        else{
            return false;
        }    
      }*/
      
}