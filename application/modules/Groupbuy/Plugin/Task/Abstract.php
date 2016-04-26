<?php

abstract class Groupbuy_Plugin_Task_Abstract {

	public function writeLog($message) {

		$filename =  APPLICATION_PATH_TMP . '/log/groupbuy.txt';
		if($fp = @fopen($filename, 'a+')) {
			fwrite($fp, $message);
			fwrite($fp, "\n");
			fclose($fp);
		}
		
	}

	abstract function execute();
	// {
	// try {
	// $table = $this -> getDealsTable();
	//
	// $this -> writeLog(sprintf("%s: SUCCESS  Groupbuy_Plugin_Task_ToRunning::excecute", get_class($this), date('Y-m-d H:i:s')));
	// } catch(Exception $e) {
	// $this -> writeLog(sprintf("%s: ERROR  Groupbuy_Plugin_Task_ToRunning::excecute \n%s", get_class($this), date('Y-m-d H:i:s'), $e -> getMessage()));
	// }
	//}
}
