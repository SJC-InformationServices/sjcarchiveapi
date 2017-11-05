<?php

namespace storedd;
use \Monolog\Logger;
use \Monolog\Handler\StreamHandler;
class log{
    public $errorlogfile;
    public $eventlogfile;

    public function __construct(string $errorlog=null,string $eventlogfile=null){
        if(!is_null($errorlog)){
            $this->errorlogfile = $errorlog;
        }else{
            $this->errorlogfile = dirname(__FILE__)."/errorlogs/error.log";
        }
        if(!is_null($eventlogfile)){
            $this->eventlogfile = $eventlogfile;
        }else{
            $this->eventlogfile = dirname(__FILE__)."/eventlogs/event.log";
        }
    }

}

?>  