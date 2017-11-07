<?php

include "../archive_api/archive_api.php";

class archive extends archive_api
{
	
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
	protected function alpha_colors()
	{
		//TODO: Args to contain filtering and sort options
		switch($this->method)
		{
			case 'GET':
				
				$ev = 'read';
				$evObj = array("assettype"=>$this->verb,"args"=>$this->args);
			break;
			case 'POST':
				
				$ev = 'update';
				$evObj = array('id'=>$this->verb,'args'=>$this->args);
			break;
			case 'PUT':				
				$ev = 'create';
				$evObj = array('args'=>$this->verb);
			break;
			case 'DELETE':
				
				$ev = 'delete';
				$evObj = array('id'=>$this->verb,'args'=>$this->args);
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
		$verb = $this->archivedb->real_escape_string($this->verb);
		if($verb == '' || is_null($verb))
		{
			return array('error'=>'Insuffient Information to create asset');
		}
		$at = $this->verb;
		$args = count($this->args) > 0 ? $this->args[0]:array();
		
		$sql = "";
				
		$qry = $this->archiveQryDb($sql);
		if($qry){
			$this->archiveCommit();
			return $this->read();
		}
		else{
			return array('error'=>'DB Error');
		}		
	}
	protected function read()
	{
		$filters = array();
			
		if(is_array($this->verb))
		{
			$args = $this->verb;			
		}
		elseif(is_numeric($this->verb))
		{
			$verb = $this->archivedb->real_escape_string($this->verb);
			$args = count($this->args) > 0 ? $this->args[0]:array();
			array_push($filters,"`abstyleid`='$verb'");
		}
		else{
			$args = array();
		}
		if(isset($args['filters']) && count($args['filters']) > 0)
		{
			foreach($args['filters'] as $filter)
			{
			$attrib = $filter['attribute'];
			$value = $this->archivedb->real_escape_string($filter['value']);
			$operator = $filter['operator'];
			//array_push($filters,"(`aasid`) in (select `aavaasid` from `aav`,`aad` where (`aavaadid` = `aadid` and `aadname`='$attrib') and (lower(`aavvalue`) $operator lower('$value')))");
			array_push($filters,"`$attrib` $operator '$value'");
			}
		}	
		$args['filters'] = $filters;
		
		return $this->readColors($args);										
	}	
	protected function update()
	{
	$filters = array();
			
		if(is_array($this->verb))
		{
			$args = $this->verb;			
		}
		elseif(is_numeric($this->verb))
		{
			$verb = $this->archivedb->real_escape_string($this->verb);
			$args = count($this->args) > 0 ? $this->args[0]:array();
			array_push($filters,"`abstyleid`='$verb'");
		}
		else{
			$args = array();
		}
		if(isset($args['filters']) && count($args['filters']) > 0)
		{
			foreach($args['filters'] as $filter)
			{
			$attrib = $filter['attribute'];
			$value = $this->archivedb->real_escape_string($filter['value']);
			$operator = $filter['operator'];
			//array_push($filters,"(`aasid`) in (select `aavaasid` from `aav`,`aad` where (`aavaadid` = `aadid` and `aadname`='$attrib') and (lower(`aavvalue`) $operator lower('$value')))");
			array_push($filters,"upper(`$attrib`) $operator upper('$value')");
			}
		}
		$this->archive_log_write(json_encode($this->verb)." ".json_encode($this->args));	
		$args['filters'] = $filters;
		return $this->updateColors($args);			
	}	
	protected function delete()
	{
	return array();	
	}
	
	protected function readColors($args){
				
		if(isset($args['attributes'])){

			$select = '`'.implode("`,`",$args['attributes']).'`';
		}		
		else{
			$select = '*';
		}				
		$sql = "select $select from `abcolors`";
			
		if(count($args['filters']) > 0){
			$sql .= " where ".implode(" and",$args['filters']);
		}
		
		if(isset($args['sort']) && count($args['sort']) > 0)
		{
			$sql .= " order by ";		
				foreach($args['sort'] as $s)
				{										
					switch(strtolower($s[1]))
					{
						case "asc":
						$sql .= $s[0]." asc";		
						break;
						case "desc":
						$sql .= $s[0]." desc";
						break;
					}					
				};
				
			}
		
		$qry = $this->archiveQryDb($sql);
		
		if($qry)
		{
			$tmp = array();
			$results = $this->archiveDbFetchArray(array('attributeConfig'));
			
			$tmp = $results;
			if(isset($args['groupby']) && $args['groupby'] == TRUE)
			{
				$results = array_values(array_map("unserialize", array_unique(array_map("serialize", $tmp))));
			}else
			{			
				$results= array_values($tmp);
			}
			$this->archive_log_write($sql);
			return $results;
			
		}else{
			$args = array($this->dbError);
			return $args;			
		}
	}
	protected function updateColors($args){
		$this->archive_log_write(json_encode($args));						
		$sql = "update `abcolors` set ";
		
		if(isset($args['attributes'])){
			//array_walk($args['attributes'],'$this->archivedb->real_escape_string');
			foreach($args['attributes'] as $attribs){
				$a = $this->archivedb->real_escape_string($attribs['attribute']);
				$v = $this->archivedb->real_escape_string($attribs['value']);
				$sql .= "`$a` = '$v',";
			}
			$sql = substr($sql,0,-1);
		}
		else{
			return array("No Updates");
		}
			
		if(count($args['filters']) > 0){
			$sql .= " where ".implode(" and ",$args['filters']);
		}
				
		$this->archive_log_write($sql);
		
		$qry = $this->archiveQryDb($sql);
		
		if($qry)
		{
			$this->archiveCommit();
			$this->archive_log_write($sql);
			return array('RESULTS'=>'Update Success');
			
		}else{
			$args = array($this->dbError);
			return $args;			
		}
	}
		

		
}

?>