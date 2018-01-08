<?php
namespace storedd\models{

class entdef extends \RedBean_SimpleModel
{   
    public function open() {
                
     }
     public function dispense() {
                
     }
     public function update() {
                
     }
     public function after_update() {
                 
     }
     public function delete() {
                 
     }
     public function after_delete() {
                 
}}}

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
            /*foreach($r as $k=>$v){
                $r[$k]['children'] = array_column(\R::getAll('select `name` from `entdef`,`entdef_entdef` where `entdef`.`id` = `entdef_entdef`.`child_entdef` and `entdef_entdef`.`parent_entdef` = :pid ',[':pid'=>$r[$k]['id']]),'name');
                $r[$k]['parents'] = array_column(\R::getAll('select `name` from `entdef`,`entdef_entdef` where `entdef`.`id` = `entdef_entdef`.`parent_entdef` and `entdef_entdef`.`child_entdef` = :pid ',[':pid'=>$r[$k]['id']]),'name');
            }*/
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
                
                if(isset($rec['parents'])){
                    $p = [];
                    foreach($rec['parents'] as $p){
                        $pb = \R::findOne('entdef','`name` = ? ',[$p]);
                        array_push($p,$pb);
                        //$relate = \R::dispense('entdef_entdef');
                        //$relate->parent_entdef = $pb->id;
                        //$relate->child_entdef =$id;
                        $b->ownEntdefList[] = $p;
                        //\R::store($pb);
                    }
                    
                }
                if(isset($rec['children'])){
                    /*foreach($rec['children'] as $c){
                        $cb = \R::findOne('entdef','`name` = ? ',[$c]);
                        $relate = \R::dispense('entdef_entdef');
                        $relate->parent_entdef =$b->id; 
                        $relate->child_entdef = $cb->id;
                        \R::store($relate);
                    }*/
                }
                $id = \R::store($b);
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
}
}
?>