<?php
namespace storedd\models
{
class entdef extends \RedBean_SimpleModel
{   
    public function open() {
        global $lifeCycle;
        $lifeCycle .= "called open: ".$this->id;
        echo $lifeCycle;
     }
     public function dispense() {
         global $lifeCycle;
         $lifeCycle .= "called dispense() ".$this->bean;
         echo $lifeCycle;
     }
     public function update() {
         global $lifeCycle;
         $lifeCycle .= "called update() ".$this->bean;
         echo $lifeCycle;
     }
     public function after_update() {
         global $lifeCycle;
         $lifeCycle .= "called after_update() ".$this->bean;
         echo $lifeCycle;
     }
     public function delete() {
         global $lifeCycle;
         $lifeCycle .= "called delete() ".$this->bean;
         echo $lifeCycle;
     }
     public function after_delete() {
         global $lifeCycle;
         $lifeCycle .= "called after_delete() ".$this->bean;
         echo $lifeCycle;
     }
}}
namespace storedd\modules{

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
                    $b = \R::load('entdef',$this->verb);
                }else{
                    $b = \R::findOne('entdef','`name` = ? ',[$this->verb]);
                }
            }else{
                   $b = \R::findAll('entdef','`name` <> ?', ['']);
            }
            $r = \R::exportAll($b,TRUE);
            foreach($r as $k=>$v){
                $r[$k]['children'] = array_column(\R::getAll('select `name` from `entdef`,`entdef_entdef` where `entdef`.`id` = `entdef_entdef`.`child_entdef` and `entdef_entdef`.`parent_entdef` = :pid ',[':pid'=>$r[$k]['id']]),'name');
                $r[$k]['parents'] = array_column(\R::getAll('select `name` from `entdef`,`entdef_entdef` where `entdef`.`id` = `entdef_entdef`.`parent_entdef` and `entdef_entdef`.`child_entdef` = :pid ',[':pid'=>$r[$k]['id']]),'name');
            }
            return $r;
			break;
            case 'POST':
            if(!is_null($this->verb) && $this->verb != ''){
                if(is_numeric($this->verb)){
                    $b = \R::load('entdef',$this->verb);
                }else{
                    $b = \R::findOne('entdef','`name` = ? ',[$this->verb]);
                }
                foreach($this->file as $k=>$v)
                {
                switch($k){
                
                case "name":
                    $b->label= strtoupper(preg_replace('/[^a-z0-9]+\Z/i', '',$v));
                    $b->$k=$v;   
                 break;
                 case 'parents';
                    foreach($rec['parents'] as $p){
                        $pb = \R::findOne('entdef','`name` = ? ',[$p]);
                        $pb->sharedentdefList[]=[$b];
                        \R::store($pb);
                    }                    
                 break;
                 case 'children':
                 
                    foreach($rec['children'] as $c){
                        $cb = \R::findOne('entdef','`name` = ? ',[$c]);
                        $b->sharedentdefList[]=[$cb];
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
                $b = \R::dispense('entdef');
                $rec = $this->file;
                $b->name=$rec['name'];
                $b->label=strtoupper(preg_replace('/[^a-z0-9]+\Z/i', '',$rec['name']));
                $options = isset($rec['options'])?\json_encode($rec['options']):null;
                $id = \R::store($b);
                if(isset($rec['parents'])){
                    
                    foreach($rec['parents'] as $p){
                        $pb = \R::findOne('entdef','`name` = ? ',[$p]);
                        $relate = \R::dispense('entdef_entdef');
                        $relate->parent_entdef = $pb->id;
                        $relate->child_entdef =$id;
                        \R::store($relate);
                    }

                }
                if(isset($rec['children'])){
                    foreach($rec['children'] as $c){
                        $cb = \R::findOne('entdef','`name` = ? ',[$c]);
                        $relate = \R::dispense('entdef_entdef');
                        $relate->parent_entdef =$b->id; 
                        $relate->child_entdef = $cb->id;
                        \R::store($relate);
                    }
                }
			break;
			case 'DELETE':
            if(!is_null($this->verb) && $this->verb != ''){
                if(is_numeric($this->verb)){
                    $b = \R::load('entdef',$this->verb);
                }else{
                    $b = \R::findOne('entdef','`name` = ? ',[$this->verb]);
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
}}

?>