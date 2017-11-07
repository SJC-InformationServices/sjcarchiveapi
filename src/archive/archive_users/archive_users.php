<?php
//Note: This Requires traits of archive_db,archive_events be included already

trait archive_users
{
	
	protected $permissions = array("assetTypes"=>"all","attributes"=>"all");
	protected $user = '';
	protected $salt = '';		
	
	protected function authenticate()
	{
		//No token detected
		$user = $_POST['username'];
		$pass = $_POST['password'];
		$token = $_POST['token'];
		
		$this->user=$user;
		$this->salt=file_get_contents( "archive.salt" );
		
		$sql = "select * from aas,aav where `aas`.`aasid` = `aav`.`aavaasid` and `aas`.`aasaatid` in 
		(SELECT `aas`.`aasaatid` FROM `aas`,`aat`,`aad`,`aav` where 
		`aas`.`aasaatid` = `aat`.`aatid` and 
		`aat`.`aatname` = 'archive_users' and 
		`aas`.`aasid` = `aav`.`aavaasid` and 
		`aav`.`aavaadid`= `aad`.`aadid` and 
		`aav`.`aavvalue` = '$user' AND
		`aad`.`aadname` = 'email') and 
		`aas`.`aasid` in (SELECT `aas`.`aasid` FROM `aas`,`aat`,`aad`,`aav` where `aas`.`aasaatid` = `aat`.`aatid` and
		`aat`.`aatname` = 'archive_users' and
		`aas`.`aasid` = `aav`.`aavaasid` and
		`aav`.`aavaadid`= `aad`.`aadid` and
		`aav`.`aavvalue` = md5(sha1(CONCAT('$pass','$this->salt'))) 
		 and `aad`.`aadname` = 'password')";
		
		$qry = $this->archiveQryDb($sql);
		if($qry)
		{
			$results = $this->archiveDbFetchArray(array('privileges'));			
		}		
	}

}
?>