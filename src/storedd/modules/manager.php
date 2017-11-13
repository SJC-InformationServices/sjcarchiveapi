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
                switch($k){

                case "name":
                    $b->label= strtoupper(preg_replace('/[^a-z0-9]+\Z/i', '',$v));
                    $b->$k=$v;   
                 break;
                 case 'parents';
                 $plist = [];
                    foreach($rec['parents'] as $p){
                        $pb = \R::findOne('entitydefinition','`name` = ? ',[$p]);
                        array_push($plist,$pb);
                    }
                    $b->ownEntitydefinitionList[]=$pb;
                 break;
                 case 'children':
                 if(isset($rec['children'])){
                    foreach($rec['children'] as $c){
                        $cb = \R::findOne('entitydefinition','`name` = ? ',[$c]);
                        $b->sharedEntitydefinitionList[]=$cb;
                    }
                }
                 break;
                default :
                    $b->$k=$v;   
                break;    
                }}
                \R::store($b);
            }else{
                $b=NULL;
            }
            
			break;
            case 'PUT':				
                $b = \R::dispense('entitydefinition');
                $rec = $this->file;
                $b->name=$rec['name'];
                $b->label=strtoupper(preg_replace('/[^a-z0-9]+\Z/i', '',$rec['name']));
                $options = isset($rec['options'])?\json_encode($rec['options']):null;
                if(isset($rec['parents'])){
                    foreach($rec['parents'] as $p){
                        $pb = \R::findOne('entitydefinition','`name` = ? ',[$p]);
                        $b->ownEntitydefinitionList[]=$pb;
                    }
                }
                if(isset($rec['children'])){
                    foreach($rec['children'] as $c){
                        $cb = \R::findOne('entitydefinition','`name` = ? ',[$c]);
                        $b->sharedEntitydefinitionList[]=$cb;
                    }
                }
                \R::store($b);
			break;
			case 'DELETE':
            if(!is_null($this->verb) && $this->verb != ''){
                if(is_numeric($this->verb)){
                    $b = \R::load('entitydefinition',$this->verb);
                }else{
                    $b = \R::findOne('entitydefinition','`name` = ? ',[$this->verb]);
                }
                \R::trash($b);
				$b=null;
			break;
        }
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