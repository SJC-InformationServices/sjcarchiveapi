<?php
/* todo: add ssl for secure database*/
trait archive_db{
	/*Default Attributes*/
	var $archivedbserver;
	var $archivedbuser;
	var $archivedbpass;
	var $archivedb;
	
	var $archiveQry;
	var $archiveResults;
	var $dbError = NULL;
		
	 function archivedberror($errno, $errstr, $errfile, $errline,$context)
	 {	//todo:add logging to capture the errors;
		$this->dbError=array("num"=>$this->archivedb->errno, "message"=>$errstr, "file"=>$errfile, "line"=>$errline,"content"=>$context);
		$this->archivedb->rollback();
	}		
	function archiveDbconnect()
	{		
		$dbconn = new mysqli($this->archivedbserver, $this->archivedbuser, $this->archivedbpass,$this->archivedb);
		if($dbconn->connect_errno)
		{
			$this->archiveDbError($dbconn->errno(),"Failed To Connect to DB","archive_db.php","15","Db Connection Fails");return FALSE;	
		}
		else{
			$dbconn->select_db($this->archivedb);
			$dbconn->set_charset("utf8");
			$dbconn->autocommit(FALSE);
			$this->archivedb = $dbconn;
			return TRUE;
		}
	}			
	function archiveDbClose()
	{
		$this->archivedb->close();
		$this->archivedb=NULL;
	}
	function archiveDbInit()
	{
		//todo: check configs ensure requirements are set to allow connection	
		return $this->archiveDbConnect();	
	}
	function archiveCreateInsertQry($tbl,$keyval,$dupkey=array())
	{
		//todo: Add duplicate functions for list of fields to update on duplicate key
		$keys = array();
		$vals = array();
		foreach($keyval as $k=>$v)
		{
			array_push($keys,$this->archivedb->real_escape_string($k));
			array_push($vals,$this->archivedb->real_escape_string($v));
		}
		$qry = "insert into `$tbl` (`".implode('`,`',$keys)."`) values ('".implode("','",$vals)."')";
		if(count($dupkey) > 0){
			$qry .= " on duplicate update ";
			foreach($dupkey as $k=>$v)
			{
				$field = $this->archivedb->real_escape_string($k);
				$val = $this->archivedb->real_escape_string($v); 			
				$qry .= " $field='$val'";
			}
		}	
		return $qry;
	}
	 function archiveQryDb($qry)
	{
		if(!isset($this->archivedb) || is_null($this->archivedb)){$this->archiveDbConnect();}
		//TODO: set up caching of queries 
		if(!$this->currentQry = $this->archivedb->query($qry))
		{
		 $dbError = $this->archivedb->error;
		 $this->archivedberror('',"DB Query Failed","archive_db.php","39","Query:$qry\nError: $dbError\n");
		 return FALSE;
		}
		return TRUE; 				
	}
	function archiveDbFetchArray($jsonfields)
	{
		$jsonfields = (isset($jsonfields) && is_array($jsonfields))?$jsonfields:array();
			$rec = array();
				while($d = $this->currentQry->fetch_array(MYSQLI_ASSOC)){
					foreach($d as $k=>$v)
					{
						if(in_array($k, $jsonfields)){
							$d[$k]=json_decode($v,TRUE);
						}
					}
					array_push($rec,$d);
				}		
				$this->dbResults=$rec;return $rec;
	}
	function archiveRollback(){
		$this->archivedb->rollback();
	}
	function archiveCommit(){
		$this->archivedb->commit();
	}			 	
}

?>