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
	protected function assets()
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
			array_push($filters,"`aasid`='$verb'");
		}
		elseif(strlen($this->verb) > 0){
			$verb = $this->archivedb->real_escape_string($this->verb);
			array_push($filters,"`aatname`='$verb'");
			$args = count($this->args) > 0 ? $this->args[0]:array();
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
			array_push($filters,"(`aasid`) in (select `aavaasid` from `aav`,`aad` where (`aavaadid` = `aadid` and `aadname`='$attrib') and (lower(`aavvalue`) $operator lower('$value')))");
			}
		}	
		$args['filters'] = $filters;
		
		return $this->readAssets($args);										
	}	
	protected function update()
	{
	return array();			
	}	
	protected function delete()
	{
	return array();	
	}
	protected function assign($name,$parents,$children)
	{
	return array();			
	}
	protected function unassign($id,$parents,$children)
	{
		return array();
	}
	protected function getParents($child)
	{
	return array();			
	}
	protected function getChildren($parent)
	{
	return array();	
	}
	protected function parseAttribute($value,$config)
	{
		$dbtype = "";
		switch($config['dbtype'])
		{
			case 'json':
			case 'array':
			case 'list':
			case 'object':
			$value = json_decode($value,TRUE);
			break;
			case 'number':
			$decimal_place = isset($config['decimals_places']) ? $config['decimals_places'] : 0;
			$dec = isset($config['decimal']) ? $config['decimal'] : "."; 	
			$value = number_format($value,$decmial_place,"$dec");
			break;
			case 'text':
			case 'varchar';
			$value = strval($value);
			break;
				
		}
		//NOTE: For future to present the data differently then is stored in db
		/*
		switch($config['formatter'])
		 * {
			
		}*/
		
		return $value;
	}
	protected function validateAttribute($value,$config){}
	protected function readAssets($args){
				
		$sql = "select `aasid` as 'assetid',`aatname` as 'assettype',`aadname` as 'attribute',`aavvalue` as 'value',`aadattributes` as 'attributeConfig' from `aat`,`aas`,`aav`,`aad` 
		where (`aasid` = `aavaasid` and `aasaatid` = `aatid` and `aavaadid` = `aadid`)";
			
		if(count($args['filters']) > 0){
			$sql .= " and ".implode(" and",$args['filters']);
		}
		//echo $sql;
		$qry = $this->archiveQryDb($sql);
		
		if($qry)
		{
			$tmp = array();
			$results = $this->archiveDbFetchArray(array('attributeConfig'));
			foreach($results as $r){
				$attribConfig = $r['attributeConfig'];
				if(!isset($tmp[$r['assetid']]))
				{
					
					if(isset($args['attributes']) && count($args['attributes']) > 0)
					{
						$tmp[$r['assetid']] = array();
						if(in_array("assetid", $args['attributes'])){
							
							$tmp[$r['assetid']]['assetid']=$r['assetid'];
						}
						if(in_array("assettype", $args['attributes'])){
							$tmp[$r['assetid']]['assettype']=$r['assettype'];
						}
						
					}
					else{
						$tmp[$r['assetid']] = array('assetid'=>$r['assetid'],'assettype'=>$r['assettype']);	
					}
				}
				
				if(isset($args['attributes']) && count($args['attributes']) > 0)
				{
					if(in_array($r['attribute'], $args['attributes'])){	
						$tmp[$r['assetid']][$r['attribute']]=$r['value'];
					}						
				}
				else
				{
					$tmp[$r['assetid']][$r['attribute']]=$r['value'];
				}
				
				
				
				
			}
			if(isset($args['groupby']) && $args['groupby'] == TRUE)
			{
				$results = array_values(array_map("unserialize", array_unique(array_map("serialize", $tmp))));
			}else
			{			
				$results= array_values($tmp);
			}
			
			if(isset($args['sort']) && count($args['sort'])){
				$sortArgs = array($results);
				foreach($args['sort'] as $s)
				{
					array_push($sortArgs,$s[0]);
										
					switch(strtolower($s[1]))
					{
						case "asc":
							array_push($sortArgs,SORT_ASC);	
						break;
						case "desc":
							array_push($sortArgs,SORT_DESC);
						break;
					}					
				};
				$results = call_user_func_array('array_orderby', $sortArgs);
			}
			$this->archive_log_write($sql);
			return $results;
			
		}else{
			$args = array($this->dbError);
			return $args;			
		}
	}
		
}

?>