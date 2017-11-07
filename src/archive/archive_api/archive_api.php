<?php
foreach (glob("../archive_includes/*.php") as $filename)
{
    if(is_file("../archive_includes/$filename")){	
    	include $filename;
	}
}
include "../archive_db/archive_db.php";
include "../archive_events/archive_events.php";
include "../archive_users/archive_users.php";
include "../archive_log/archive_log.php";
abstract class archive_api
{
	use archive_db;
	use archive_events;
	use archive_users;
	use archive_log;
    /**
     * Property: method
     * The HTTP method this request was made in, either GET, POST, PUT or DELETE
     */
    public $urls = array();
    protected $method = '';
    /**
     * Property: endpoint
     * The Model requested in the URI. eg: /files
     */
    protected $endpoint = '';
    /**
     * Property: verb
     * An optional additional descriptor about the endpoint, used for things that can
     * not be handled by the basic methods. eg: /files/process
     */
    protected $verb = '';
    /**
     * Property: args
     * Any additional URI components after the endpoint and verb have been removed, in our
     * case, an integer ID for the resource. eg: /<endpoint>/<verb>/<arg0>/<arg1>
     * or /<endpoint>/<arg0>
     */
    protected $args = Array();
    /**
     * Property: file
     * Stores the input of the PUT request
     */
     protected $file = Null;

    /**
     * Constructor: __construct
     * Allow for CORS, assemble and pre-process the data
     */
    public function __construct($request,$configs) {
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");
        
        foreach($configs as $k =>$v){
			$this->set($k,$v,TRUE);
		}
		$this->archiveDbInit();
		$this->eventInit();
        
        $this->args = explode('/', rtrim($request, '/'));
		//print_r(str_getcsv(rtrim($request, '/'),"/",'"'));
		
		array_walk($this->args,function(&$v,$k){
			$v=urldecode($v);
			
			if(substr($v, 0,1) == "{" && substr($v, -1) == "}")	
			{try{
				$v = json_decode($v,TRUE);
			}catch(Exception $e){	
			
			}
			}
		});
		
        $this->endpoint = array_shift($this->args);
        if (array_key_exists(0, $this->args)) {
            $this->verb = array_shift($this->args);
        }

        $this->method = $_SERVER['REQUEST_METHOD'];
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->method = 'PUT';
            } else {
                throw new Exception("Unexpected Header");
            }
        }

        switch($this->method) {
        case 'DELETE':
        case 'POST':
			if(isset($_POST['data'])){
				$this->request = json_decode($_POST['data'],true);
				
			}else{				
				
			try{
				$this->file = file_get_contents("php://input","r");
				$this->file = json_decode(urldecode($this->file),TRUE);
				$this->request = $this->file;
				$this->verb=$this->file;
			}
			catch(Exception $e)
			{	
			
				
			 
			}
				
			}
            break;
        case 'GET':
			if(isset($_GET['data'])){
				$_GET['data'] = json_decode($_GET['data'],true);
			}
            $this->request = $this->_cleanInputs($_GET);
            break;
        case 'PUT':
			if(isset($_GET['data'])){
				$_GET['data'] = json_decode($_GET['data'],true);
			}
            $this->request = $this->_cleanInputs($_GET);
			
            $this->file = file_get_contents("php://input","r");
			try{
				$this->file = json_decode($this->file,TRUE);
			}catch(Exception $e){	
			
			}
			
            break;
        default:
            $this->_response('Invalid Method', 405);
            break;
        }
		if(isset($this->request['data'])){
			$this->verb = $this->request['data'];	
		}
		$this->archive_log_file = $this->endpoint."-".$this->archive_log_file."-".date("Y-m-d").".log";
		$this->archive_log_write(json_encode($this->request));
    }
    function set($name,$value,$silent)
	 {
		if($silent == TRUE){
			$this->$name = $value;
		}
		else{
			//TODO: Add Event Handling for change of attribute values
			$this->$name = $value;
		}
	}
	
	 function get($name){
		return $this->$name;
	}
     public function processAPI() {
        if (method_exists($this, $this->endpoint)) {
            return $this->_response($this->{$this->endpoint}($this->args));
        }
        return $this->_response("No Endpoint: $this->endpoint", 404);
    }

    private function _response($data, $status = 200) {
        header("HTTP/1.1 " . $status . " " . $this->_requestStatus($status));
        return json_encode($data);
    }

    private function _cleanInputs($data) {
        $clean_input = Array();
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $clean_input[$k] = $this->_cleanInputs($v);
            }
        } else {
            $clean_input = trim(strip_tags($data));
        }
        return $clean_input;
    }

    private function _requestStatus($code) {
        $status = array(  
            200 => 'OK',
            404 => 'Not Found',   
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        ); 
        return ($status[$code])?$status[$code]:$status[500];
	}
}
?>