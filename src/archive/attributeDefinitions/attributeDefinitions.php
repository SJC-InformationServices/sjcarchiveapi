<?php

include "../archive_api/archive_api.php";

class archive extends archive_api
{
private $defaultAttribsDef = array("iskey"=>false,"index"=>false,"required"=>false,"dbtype"=>"text","dblength"=>NULL,"defaultvalue"=>NULL);	
public function __construct($request, $origin,$configs) {
        parent::__construct($request,$configs);

        // Abstracted out for example
        /*
        $APIKey = new Models\APIKey();
        $User = new Models\User();

        if (!array_key_exists('apiKey', $this->request)) {
            throw new Exception('No API Key provided');
        } else if (!$APIKey->verifyKey($this->request['apiKey'], $origin)) {
            throw new Exception('Invalid API Key');
        } else if (array_key_exists('token', $this->request) &&
             !$User->get('token', $this->request['token'])) {

            throw new Exception('Invalid User Token');
        }

        $this->User = $User;*/
    }
	protected function attributeDefinitions(){
		//TODO: Args to contain filtering and sort options
		switch($this->method)
		{
			case 'GET':				
				$ev = 'read';
				$evObj = array('attributeDefinitions'=>$this->verb);
			break;
			case 'POST':				
				$ev = 'update';
				$evObj = array('attributeDefinitions'=>$this->verb,'args'=>$this->args);
			break;
			case 'PUT':				
				$ev = 'create';
				$evObj = array('attributeDefinitions'=>$this->verb,'args'=>$this->args);
			break;
			case 'DELETE':				
				$ev = 'delete';
				$evObj = array('attributeDefinitions'=>$this->verb,'args'=>$this->args);
			break;
		}
		
		$this->fireEvent($this->endpoint,$ev,"before",$evObj);
		$obj = $this->$ev();
		$evObj['results']=$obj;
		$this->fireEvent($this->endpoint,$ev,"after",$evObj);
		return $obj;
		
	}
	protected function create()
	{
		$name = $this->archivedb->real_escape_string($this->verb);
				
		if(isset($this->args[0])){
			$args = $this->args[0];
		}else{
			$args = array();
		}
		if(!isset($args['assetType']))
			{
			return array("error"=>'Asset Type Not Specified');
			}
		$ins_args = $this->archivedb->real_escape_string(json_encode(array_merge($this->defaultAttribsDef,$args)));	
				
		$sql = "insert into `aad` (`aadname`,`aadattributes`) values ('$name','$ins_args')";
		$qry = $this->archiveQryDb($sql);
		if($qry){
			$this->set('verb', $this->archivedb->insert_id,TRUE);
													
			
				$aadid = $this->archivedb->insert_id;
				$this->archiveCommit();
				$atname = $args['assetType'];
							
				if($this->assign($aadid, $atname)){
					
				}
			
			return $this->read();
		}
		else{
			return array('error'=>'Db Error',"msg"=>$this->dbError);
		}		
	}
	
	protected function read()
	{				
		$verb = $this->verb;
		$args = $this->args;
		
		if(!is_null($verb) && $verb !='' && !is_numeric($verb))
		{
		$verb = $this->archivedb->real_escape_string($verb);
		$sql = "select * from `aadtoaatview` where `aadname`='$verb' order by `aadname`,`aatname`";		
		}
		elseif(is_numeric($verb))
		{
		$verb = $this->archivedb->real_escape_string($verb);
		$sql = "select * from `aadtoaatview` where `aadid`='$verb' order by `aadname`,`aatname`";
		}
		else
		{
		$sql = "select * from `aadtoaatview` order by `aadname`,`aatname`";			
		}	
		
		$qry = $this->archiveQryDb($sql);
		
		if($qry)
		{
			$args = $this->archiveDbFetchArray(array('aadattributes'));
		}else{
			$args = array($this->dbError);			
		}		
		return $args;
	}
	
	protected function update()
	{
		$id = $this->archivedb->real_escape_string($this->verb);
		$results = $this->read();		
		if(isset($this->args[0]))
		{
		$args = $this->args[0];
				
		if(isset($args['name']))
		{
		//update name;
		$name = $this->archivedb->real_escape_string($args['name']);
		$sql = "update `aad` set `aadname` = '$name' where `aadid` = '$id' limit 1";	
		$qry = $this->archiveQryDb($sql);
		if($qry){
			$this->archiveCommit();			
		}else{
			//$this->archiveRollback();
			//Todo: general error log
		}
		}
		if(isset($args['assetType']))
		{
			//update assignment
			$atname = $args['assetType'];							
			if(!$this->assign($aadid, $atname))
			{
						
			}
		}
		if(isset($arg['attributes']))
		{
			//update attributes
			$ins_args = $this->archivedb->real_escape_string(json_encode(array_merge($this->defaultAttribsDef,$arg['attributes'])));	
			$sql = "update `aad` set `aadattributes` = '$ins_args' where `aadid` = '$id' limit 1";
			
			$qry = $this->archiveQryDb($sql);
		if($qry){
			$this->archiveCommit();			
		}else{
			//$this->archiveRollback();
			//Todo: general error log
		}
			
			
		}		
				
		}
		return $this->read();
	}	
	protected function delete()
	{
		$verb = $this->archivedb->real_escape_string($this->verb);
		$sql = "delete from `aad` where `aadid` = '$verb' limit 1";
		return false;
	}
	protected function assign($aadid,$aatname)
	{
		$aatname = $this->archivedb->real_escape_string($aatname);
		$sql = "insert ignore into `aattoaad` (`ataaatid`,`ataaadid`)
		select `aatid`, '$aadid' from `aat` where `aatname` = '$aatname'";
			
		$qry = $this->archiveQryDb($sql);
		if($qry){
			$this->archiveCommit();
			return true;
		}else{
			return false;
		}
	}
	
}

?>