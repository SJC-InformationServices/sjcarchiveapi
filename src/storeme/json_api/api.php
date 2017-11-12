<?php
namespace storeMe\api;
use \storeMe\entities;
abstract class api extends storeme\entities
{
    protected $method ='';
    protected $endpoint='';
    protected $verb = '';
    protected $args = '';
    protected $qry = '';
    protected $file = '';
    protected $cfgs = '';
    protected $action = '';
    
    protected $parms = '';

    function set($name,$value)
	{
		$this->$name = $value;
	}
	
	function get($name)
    {
		return $this->$name;
	}
    
    private function initConfigs($request,$configs)
    {
        $this->args = stristr($request,"?") ? explode('/', rtrim(substr($request,0,strpos($request,"?")), '/')) : explode('/', rtrim($request, '/'));
        $this->endpoint = array_shift($this->args);
        if (array_key_exists(0, $this->args)) {
            $this->verb = array_shift($this->args);
        }
        foreach($configs as $k =>$v){
			$this->set($k,$v,TRUE);
		}

        $this->method = $_SERVER['REQUEST_METHOD'];
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER))
        {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->method = 'PUT';
            } else {
                throw new Exception("Unexpected Header");
            }
        }
        
        switch($this->method){
            case 'POST':
            $this->set('action','create'); 
            break;
            case 'GET':
            $this->set('action','read'); 
            break;
            case 'PUT':
            case 'PATCH':
            $this->set('action','update'); 
            break;
            case 'DELETE':
            $this->set('action','delete'); 
            break;
        }
        if(isset($_REQUEST['qry']))
            {
                $this->set('qry',JSON_DECODE(URLDECODE($_REQUEST['qry']),true));
            }else{
                $this->set('qry',array());
            }       
    }
    public function _initDb(){
        $dbConfig = new \Doctrine\DBAL\Configuration();
        $dbparms = array(
            'dbname' => $this->dbname,
            'user' => $this->dbuser,
            'password' => $this->dbpass,
            'host' => $this->dbhost,
    'driver' => $this->dbdriver,'charset'=>$this->dbcharset);
    $this->db = \Doctrine\DBAL\DriverManager::getConnection($dbparms,$dbConfig);
    }

    public function __construct($request,$configs)
    {
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");

        $this->initConfigs($request,$configs);
        $this->_initDb();
    }

    private function _response($data,$status = 200){
        header("HTTP/1.1 " . $status ." " . $this->_requestStatus($status));
        return json_encode($data);
    }

    public function processAPI()
    {        
        if(method_exists($this,$this->endpoint))
        {
            //TODO: FireOff before event Request
            $results= $this->_response($this->{$this->endpoint}($this->verb,$this->args,$this->qry));
            //TODO: FireOff after event Request
            return $results;
        }
        
    }
    private function _requestStatus($code){
        $status = array(  
            200 => 'OK',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',   
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        ); 
        return ($status[$code])?$status[$code]:$status[500];
	}

     
}
?>