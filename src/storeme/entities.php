<?php
/*
Create Inital Interface for Registering Entities
*/
namespace storeMe;
class entities extends defineEntity
{
    public function __construct($configs)
    {
        parent::__construct($configs);
    }
    /*Create a Entity records*/
    public function register($sm){}
    /*delete a Entity record*/
    public function unRegister($sm){}
    /*update a Entity record*/
    public function update($sm){}
    /*Read Entity records*/
    public function getEntity($sm=[]){}

    /*Create hirerarchy structure for valid parent child relations*/    
    public function assignParents($sm,$parents){}
    public function assignChildren($sm,$children){}
}

?>