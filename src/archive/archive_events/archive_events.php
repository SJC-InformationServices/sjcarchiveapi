<?php
//Note: Trait that Requireds archive_db
trait archive_events
{
	public $events = array();
	public $eventsLog = array();
	
	protected function eventInit(){
		$sql = "select * from `aev`";
		$qry = $this->archiveQryDb($sql);
		if($qry){
		$values = $this->archiveDbFetchArray(array());
		foreach($values as $ev)
		{
			$evid = $ev['aevtid'];
			$evendpoint = $ev['aevtendpoint'];
			$evmethod = $ev['aevtmethod'];
			$timing = $ev['aevttiming'];
			$evname = "archive_event_".$ev['aevname'];
			
			if(!isset($this->events[$evendpoint]))
			{
				$this->events[$evendpoint]=array();	
			}
			if(!isset($this->events[$evendpoint][$evmethod]))
			{
				$this->events[$evendpoint][$evmethod]=array('before'=>array(),'after'=>array());
			}
			$ev = create_function('$endpoint,$method,$timing,$evobj,$archive',$ev['aevtcode']);
			
			$this->events[$evendpoint][$evmethod][$timing][$evid]=$ev; 
						
		}
		}
	}	 
	protected function fireEvent($endpoint,$method,$timing,$evobj)
	{
		$ev = $this->events;
		if(isset($ev[$endpoint][$method][$timing]) && is_array($ev[$endpoint][$method][$timing]))
		{
		$allev = $ev[$endpoint][$method][$timing];
			foreach($allev as $k => $v)
			{
			
			
			try{
				$evSuccess = $v($endpoint,$method,$timing,$evobj,$this);
				echo $evSuccess;
			}
			catch(Exception $e){
				print_r($e);
			}
			
			}		
		}
		
		return;				
	}
	
	public function setEvent($evendpoint,$evTiming){
		
	}
		
}
?>