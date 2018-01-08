<?php
namespace storedd\models;

class entities extends base{
    private $entdef;

    protected $id;
    protected $parents;
    protected $children;

    public $bean = null;
    public $rec = null;
    
    public function __construct(entityDefinition $entdef,int $id=null)
    {    
        $this->entdef = $entdef;

        if(!is_null($id))
        {    
            $this->bean = \R::load($this->entdef->name,$id);                    
        }        
        if(!is_null($this->bean)){
            $rec = $this->bean->export();
            $this->rec = $rec;
            foreach($rec as $k=>$v)
            {
                $this->$k=$v;
            }
        }
    }
    
    protected function setname(string $value){
        
    }
    protected function setoptions(array $value){
        
    }
    protected function assignAttribute(int $value){
        
    }
    protected function assignParent(int $value){}
        
}
?>