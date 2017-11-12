<?php
namespace storedd\models;

class entitydefinition extends \RedBean_SimpleModel{
    
    /*public function __construct(int $id=null,string $name=null)
    {     
        if(!is_null($id))
        {
            $this->bean = \R::load('entdef',$id);                    
        }
        elseif(!is_null($name)){
            $find = \R::find('entdef'," name = ? ",["$name"]);
            if(count($find) == 0){
                $this->name = $name;
            }
            else{
                $this->bean=$find[0];
            }
        }        
        if(!is_null($this->bean)){
            $rec = $this->bean->export();
            $this->rec = $rec;
            foreach($rec as $k=>$v)
            {
                $this->$k=$v;
            }
        }else{
            $this->bean = \R::dispense('entdef');
        }

    }
    
    function setname(string $value){
        $find = \R::find('entdef'," name = ? ",["$value"]);
        if(count($find)==0){
        $this->name = $value;
        $this->label = preg_replace('/\W+$/', '', $value);
        $this->bean->label = $this->label;
        $this->bean->name =$value;    
        }else{
            //TODO Log Entity Type Exists
            $this->bean=$find[0];
            $rec = $this->bean->export();
            $this->rec = $rec;
            foreach($rec as $k=>$v)
            {
                $this->$k=$v;
            }
            return false;
        }
    }
    function setoptions(array $value){
        
    }
    function assignAttribute(int $value){
        
    }
    public function commit(){
     $rec= \R::store($this->bean);
     $this->id=$rec;
    }*/
    public function open() {
        global $lifeCycle;
        $lifeCycle .= "called open: ".$this->id;
     }
     public function dispense() {
         global $lifeCycle;
         $lifeCycle .= "called dispense() ".$this->bean;
     }
     public function update() {
         global $lifeCycle;
         $lifeCycle .= "called update() ".$this->bean;
     }
     public function after_update() {
         global $lifeCycle;
         $lifeCycle .= "called after_update() ".$this->bean;
     }
     public function delete() {
         global $lifeCycle;
         $lifeCycle .= "called delete() ".$this->bean;
     }
     public function after_delete() {
         global $lifeCycle;
         $lifeCycle .= "called after_delete() ".$this->bean;
     }
    
}
?>