<?php
trait archive_log{
	public $archive_log_path = "../archive_log/logs";
	public $archive_log_file = "archive_log.php";
	
	function archive_log_read(){
				return file_get_contents($this->$archive_log_path."/".$this->archive_log_file);
	}
	
	function archive_log_write($str)
	{
		$file = fopen($this->archive_log_path."/".$this->archive_log_file,'ab');
		fwrite($file,date('Y-m-d H:i:s')."-------\n$str\nEnd\n");	
		fclose($file);
	}
	
}
?>