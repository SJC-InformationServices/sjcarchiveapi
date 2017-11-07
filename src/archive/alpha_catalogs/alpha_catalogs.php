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
	protected function alpha_catalogs()
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
		
		if(is_array($this->file)){
			
				
				$keys = array_keys($this->file);
				$values = array_values($this->file);
				$org = array_combine($keys, $values);
				
				foreach($keys as $k=>$v){
					$keys[$k]=$this->archivedb->real_escape_string($v);
				}
				
				foreach($values as $k=>$v){
					$values[$k]=$this->archivedb->real_escape_string(strip_tags($v));
				}
				$newAry = array_combine($keys, $values);
				$sql = "insert into `masterdataview` (`".implode("`,`",$keys)."`) values ('".implode("','",$values)."')";
				//$sql = "call abDataPickup('".$newAry['style']."','','','".$newAry['season']."','".$newAry['catalog_name']."','usa',true)";
				$qry = $this->archiveQryDb($sql);		
		if($qry)
		{
			$this->archiveCommit();
			$this->archive_log_write($sql);
			return array("data"=>$org);
			/*$args = array();
			$filters = array();
			$keyval = array_merge($keys,$values);
			foreach($keyval as $k=>$v)
			{
					$attrib = $k;
					$value = $this->archivedb->real_escape_string($v);
					$operator = "=";
					//array_push($filters,"(`aasid`) in (select `aavaasid` from `aav`,`aad` where (`aavaadid` = `aadid` and `aadname`='$attrib') and (lower(`aavvalue`) $operator lower('$value')))");
					array_push($filters,"upper(`$attrib`) $operator upper('$value')");
			}
			$arg['filters']=$filters;
			return $this->readCatalogs($args);*/
			
		}else{
			$args = array("error"=>$this->dbError,"qry"=>$sql);
			return $args;			
		}
				
			
		}else{
			return array("error"=>$this->file);
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
			array_push($filters,"upper(`$attrib`) $operator upper('$value')");
			}
		}	
		$args['filters'] = $filters;
		
		return $this->readCatalogs($args);										
	}	
	protected function update()
	{
	$readArgs = array();	
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
		$args['filters'] = $filters;
		$readArgs['filters']=$filters;
		$res =  $this->updateCatalogs($args);
		return array('result'=>(isset($res['RESULTS']))?$res['RESULTS']:$res['ERROR'],'data'=>$this->readCatalogs($readArgs),'res'=>$res);		
	}	
	protected function delete()
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
		$args['filters'] = $filters;
	}
	protected function readCatalogs($args){
		
		
		if(isset($args['attributes'])){
			//array_walk($args['attributes'],'$this->archivedb->real_escape_string');
			$select = '`'.implode("`,`",$args['attributes']).'`';
		}		
		else{
			$select = '*';
		}				
		$sql = "select $select from `masterdataview`";
			
		if(count($args['filters']) > 0){
			$sql .= " where ".implode(" and ",$args['filters']);
		}
		
		if(isset($args['groupby']) && $args['groupby'] == TRUE && isset($args['attributes']))
		{
			
			$sql .= " group by `".implode("`,`",$args['attributes'])."`"; 	
						
		}
		
		if(isset($args['sort']) && count($args['sort']) > 0)
		{
			$sql .= " order by ";
			$sortAry = array();		
				foreach($args['sort'] as $s)
				{										
					switch(strtolower($s[1]))
					{
						case "asc":
						array_push($sortAry,"`".$s[0]."` asc");		
						break;
						case "desc":
						array_push($sortAry,"`".$s[0]."` desc");
						break;
					}					
				};
			$sql .= implode(", ", $sortAry);	
			}
		$this->archive_log_write($sql);
		$qry = $this->archiveQryDb($sql);
		
		if($qry)
		{
			$tmp = array();
			$results = $this->archiveDbFetchArray(array('attributeConfig'));
			//$results= array_values($tmp);
			/*$tmp = $results;
			if(isset($args['groupby']) && $args['groupby'] == TRUE)
			{
					
				$results = array_values(array_map("unserialize", array_unique(array_map("serialize", $tmp))));
				
			}else
			{			
				$results= array_values($tmp);
			}*/
			
			
			$this->archive_log_write($sql);
			return $results;
			
		}else{
			$args = array($this->dbError);
			return $args;			
		}
	}
protected function updateCatalogs($args){
									
		$sql = "update `masterdataview` set ";
		
		if(isset($args['attributes'])){
			//array_walk($args['attributes'],'$this->archivedb->real_escape_string');
			foreach($args['attributes'] as $attribs){
				$a = $this->archivedb->real_escape_string(trim($attribs['attribute']));
				$v = $this->archivedb->real_escape_string($attribs['value']);
				switch($attribs['attribute']){
					case 'short_description':
					case 'long_description':
					case 'sub_description':
					$sql .= "`$a` = REPLACE(REPLACE(REPLACE('$v','\n','\r'),'• ','• '),'• ','• '),";						
					break;
					default:
					$sql .= "`$a` = '$v',";	
					break;
				}				
			}
			$sql = substr($sql,0,-1);
		}
		else{
			return array("No Updates");
		}
			
		if(count($args['filters']) > 0)
		{
			$sql .= " where ".implode(" and ",$args['filters']);
		}
								
		$this->archive_log_write($sql);
		
		$qry = $this->archiveQryDb($sql);
		//return array("$qry"=>$sql);
		if($qry)
		{
			$this->archiveCommit();
			$this->archive_log_write($sql);
			return array('RESULTS'=>'Update Success','SQL'=>$sql);	
		}else{
			$args = array($this->dbError);
			return $args;			
		}
	}
		
}

?>