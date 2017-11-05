<?php
namespace storedd\models;
class entityDefinition extends base{
    private $id;
    public $name;
    private $label;
    public $options=array();
    public $attributes=array();

    public function __construct(string $id=null,string $name=null){

        if(!is_null($id))
        {

        }
        elseif(!is_null($name)){

        }

    }
    protected function getRecordById(){}
    protected function getRecordByName(){}
        

}
?>