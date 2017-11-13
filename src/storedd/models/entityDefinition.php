<?php
namespace storedd\models;
use base;
class entitydefinition extends base{
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
         $lifeCycle .= "Updating Entity Type";
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