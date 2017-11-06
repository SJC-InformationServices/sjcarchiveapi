<?php

namespace storedd\modules;
use \Monolog\Logger;
use \Monolog\Handler\StreamHandler;
use \Monolog\Formatter\LineFormatter;
use \Monolog\Formatter\JsonFormatter;
class log{
    private $errorlogfile;
    private $securitylogfile;
    private $eventlogfile;
    
    protected $errorlog;
    protected $securitylog;
    protected $eventlog;

    public function __construct(){
        //TODO: CUstom log file paths and formatters
        $this->errorlogfile = dirname(__FILE__)."/error/error.log";
        $this->securitylogfile = dirname(__FILE__)."/security/security.log";
        $this->eventlogfile = dirname(__FILE__)."/event/event.log";
        
        //Todo enable multiple logging types
        
        $formatter = new JsonFormatter();
        $stream = new StreamHandler($this->errorLogfile,Logger::DEBUG);
        $stream->setFormatter($formatter);
        $this->errorLog=new Logger('debug');
        $this->errorLog->pushHandler($stream);

        
        $formatter = new JsonFormatter();
        $stream = new StreamHandler($this->securitylogfile,Logger::INFO);
        $stream->setFormatter($formatter);
        $this->securityLog=new Logger('security');
        $this->securityLog->pushHandler($stream);

        
        $formatter = new JsonFormatter();
        $stream = new StreamHandler($this->eventlogfile,Logger::INFO);
        $stream->setFormatter($formatter);
        $this->errorLog=new Logger('event');
        $this->errorLog->pushHandler($stream);

    }

}

?>  