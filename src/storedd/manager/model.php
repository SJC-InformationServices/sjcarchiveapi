<?php

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
    }}
?>