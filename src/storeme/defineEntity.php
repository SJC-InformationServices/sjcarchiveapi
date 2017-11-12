<?php
/*
Create Inital Entities Definitions
*/
namespace storeMe;
class defineEntity extends base
{
    public function __construct($cfgs) {
        parent::__construct($cfgs);
    }
    /*Create a Entity records*/
    private function define($sm)
    {}
    /*delete a Entity record*/
    public function unDefine($sm)
    {

    }
    /*update a Entity record*/
    public function reDefine($sm)
    {

    }
    /*Read Entity records*/
    public function definedEntities($sm=null)
    {
     
      if(!is_null($sm)){
          $sql = "call getStoreMe(?)";    
          $stmt=$this->db->prepare($sql);
          $stmt->bindValue(1,$sm);          
      }
      else 
      {
        $sql = "call getStoreMe()";
        $stmt=$this->db->prepare($sql);
      }      
      $stmt->execute();
      return $stmt->fetchAll();
    }
    /*Create hirerarchy structure for valid parent child relations*/    
    public function assignParents($sm,$parents)
    {



    }
    public function assignChildren($sm,$children){

    }
}

?>