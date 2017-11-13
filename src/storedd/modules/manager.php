<?php

namespace storedd\modules;

class manager extends base_api
{
    public function __construct($request) {
        parent::__construct($request); 
        
    }
    public function em(){
        switch($this->method)
		{
            case 'GET':
            if(!is_null($this->verb) && $this->verb != ''){
                if(is_numeric($this->verb)){
                    $b = \R::load('entitydefinition',$this->verb);
                }else{
                    $b = \R::findOne('entitydefinition','`name` = ? ',[$this->verb]);
                }
            }else{
                   $b = \R::findAll('entitydefinition','`name` <> ?', ['']);
            }                                
			break;
            case 'POST':
            if(!is_null($this->verb) && $this->verb != ''){
                if(is_numeric($this->verb)){
                    $b = \R::load('entitydefinition',$this->verb);
                }else{
                    $b = \R::findOne('entitydefinition','`name` = ? ',[$this->verb]);
                }
                foreach($this->file as $k=>$v)
                {
                 $b->$k=$v;   
                }
                \R::store($b);
            }else{
                $b=NULL;
            }
            
			break;
			case 'PUT':				
            $evObj = array("assettype"=>$this->verb,"args"=>$this->args);
				
			break;
			case 'DELETE':
            $evObj = array("assettype"=>$this->verb,"args"=>$this->args);
				
			break;
        }
        if(!is_null($b)){
        $r = \R::exportAll($b,TRUE);
        }else{
            $r=array();
        }
        return $r;
    }
}

?>