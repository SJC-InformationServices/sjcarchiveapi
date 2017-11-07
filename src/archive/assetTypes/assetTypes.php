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
	protected function assetTypes(){
		//TODO: Args to contain filtering and sort options
		switch($this->method)
		{
			case 'GET':
				
				$ev = 'read';
				$evObj = array('name'=>$this->verb);
			break;
			case 'POST':
				
				$ev = 'update';
				$evObj = array('name'=>$this->verb,'args'=>$this->args);
			break;
			case 'PUT':
				
				$ev = 'create';
				$evObj = array('name'=>$this->verb,'args'=>$this->args);
			break;
			case 'DELETE':
				
				$ev = 'delete';
				$evObj = array('name'=>$this->verb,'args'=>$this->args);
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
		$sql = "insert into `aat` (`aatname`) values ('$verb')";
				
		$qry = $this->archiveQryDb($sql);
		if($qry){
			$this->archiveCommit();
			
			if(is_array($this->args[0]))
			{
				$args = $this->args[0];
				
				if((isset($args['parents']) && is_array($args['parents'])) || (isset($args['children']) && is_array($args['children'])))
				{
					$p = is_array($args['parents']) ? $args['parents'] : array();
					$c = is_array($args['children']) ? $args['children'] : array();
					$assignparents = $this->assign($this->verb, $p, $c);
				}				
			}	
			return $this->read();
		}
		else{
			return array('error'=>'DB Error');
		}		
	}
	
	protected function read()
	{
				
		$verb = $this->verb;
		$args = $this->args;		
		$fields = "`aatid` as 'assetTypeId',`aatname` as 'name',`aadid` as 'attributeId',`aadname` as 'attribute',`aadattributes` as 'attributeConfig'";
		if(!is_null($verb) && $verb !='' && !is_numeric($verb)) 
		{
		$verb = $this->archivedb->real_escape_string($verb);
		$sql = "select $fields from `aattoaadview` where `aatname`='$verb' order by `aatname`,`aadname`";		
		}
		elseif(is_numeric($verb))
		{
		$sql = "select $fields from `aattoaadview` where `aatid`='$verb' order by `aatname`,`aadname`";	
		}
		else
		{
		$sql = "select $fields from `aattoaadview` order by `aatname`,`aadname`";			
		}	
		
		$qry = $this->archiveQryDb($sql);
		
		if($qry)
		{
			$results = $this->archiveDbFetchArray(array('attributeConfig'));
		}else{
			$args = array($this->dbError);
			return $args;			
		}
		$tmp = array();		
		foreach($results as $k=>$v)
		{
			$id = $v['assetTypeId'];
			if(!isset($tmp[$id]))
			{
				$tmp[$id] = $v;
				$tmp[$id]['attributes'] = array();
				$children = $this->getChildren($v['name']);
				$parent = $this->getParents($v['name']);
				$tmp[$id]['children'] = $children;
				$tmp[$id]['parents']= $parent;
				unset($tmp[$id]['attribute']);
				unset($tmp[$id]['attributeConfig']);
				unset($tmp[$id]['attributeId']);				
			}
			$attrib = array("attribute"=>$v['attribute'],"attributeId"=>$v['attributeId'],"attributeConfig"=>$v['attributeConfig']);
			array_push($tmp[$id]['attributes'],$attrib);
			
		}
		array_push($tmp,$this->args);
		array_push($tmp,$this->verb);
		return $tmp;
		return array_values($tmp);
	}
	
	protected function update(){
		$verb = $this->archivedb->real_escape_string($this->verb);
		$args = $this->archivedb->real_escape_string($this->args[0]);
		
		$v = $this->verb;
		$a = $this->args[0];		
		
		if($v != $a){
		$sql = "update `aat` set `aatname` = '$args' where `aatname` = '$verb' limit 1";
		$qry = $this->archiveQryDb($sql);
		if($qry){
			$this->archiveCommit();
			$this->set('verb',$a,true);
			
		}
		else{
			return array('error'=>'Update Failed');
		}}
		
		if(is_array($this->args[1]))
			{
				$args = $this->args[1];
				
				if((isset($args['parents']) && is_array($args['parents'])) || (isset($args['children']) && is_array($args['children'])))
				{
					$p = is_array($args['parents']) ? $args['parents'] : array();
					$c = is_array($args['children']) ? $args['children'] : array();
					$assignparents = $this->assign($this->verb, $p, $c);
				}				
								
				if(isset($args['attributes']) && is_array($args['attributes']))
				{
					//TODO: Curl attributes variable to api to create attributes
				}				
			}	
			return $this->read();				
	}	
	protected function delete()
	{
		$verb = $this->archivedb->real_escape_string($this->verb);
		$sql = "delete from `aat` where `aatname` = '$verb' limit 1";
		$rec = $this->read();
		$qry = $this->archiveQryDb($sql);
		if($qry){
			$this->archiveCommit();
			return array('delete'=>true,'record'=>$rec);
		}
		else{
			return array('error'=>'Delete Failed');
		}
	}
	protected function assign($name,$parents,$children){
		if(is_array($children) && is_array($parents))
		{
		foreach ($children as $k => $v) {
			$children[$k]=$this->archivedb->real_escape_string($v);
		}
		foreach ($parents as $k => $v) {
			$parents[$k]=$this->archivedb->real_escape_string($v);
		}
		$name=$this->archivedb->real_escape_string($name);
		$children = "'".implode("','",$children)."'";
		$parents = "'".implode("','",$parents)."'";
		
		$sqla = "insert into `aattoaat` (`aatidparent`,`aatidchild`) SELECT a.`aatid`,b.`aatid` from `aat` a, `aat` b where a.`aatname` = '$name' and b.`aatname` in ($children)";

		$sqlb = "insert into `aattoaat` (`aatidparent`,`aatidchild`) SELECT a.`aatid`,b.`aatid` from `aat` a, `aat` b where a.`aatname` in ($parents) and b.`aatname` = '$name'";
				
		$qrya = $this->archiveQryDb($sqla);
		if($qrya){
			$this->archiveCommit();
			//TODO: after parents assign attributes from parents
		}else{
			
		} 
		$qryb = $this->archiveQryDb($sqlb);
		if($qryb){
			$this->archiveCommit();
		}
		else{
			print_r($this->dbError);
		}
		return;		
		}		
	}
	protected function getParents($child){
		$child = $this->archivedb->real_escape_string($child);
		$sql = "select b.aatname from `aat` a,`aattoaat`, `aat` b where 
		a.`aatname` = '$child' and `aatidchild` = a.`aatid` and `aatidparent` = b.`aatid`";
		
		$qry = $this->archiveQryDb($sql);
		if($qry){
			$parents = array();
			foreach($this->archiveDbFetchArray(array('aadattributes')) as $k){
				array_push($parents,$k['aatname']);
			}
			return $parents; 
		}else{
			return false;
		}
		
	}
	protected function getChildren($parent){
		$parent = $this->archivedb->real_escape_string($parent);
		$sql = "select b.aatname from `aat` a,`aattoaat`, `aat` b where 
		a.`aatname` = '$parent' and 
        `aatidparent` = a.`aatid` and 
        `aatidchild` = b.`aatid`";
		
		$qry = $this->archiveQryDb($sql);
		if($qry){
			$children = array();
			foreach($this->archiveDbFetchArray(array('aadattributes')) as $k){
				array_push($children,$k['aatname']);
			}
			return $children;
		}else{
			return false;
		}
	}
	
	
}

?>