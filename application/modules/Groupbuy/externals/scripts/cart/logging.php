<?php
  /**
 * Logging class:
 * - contains lopen and lwrite methods
 * - lwrite will write message to the log file
 * - first call of the lwrite will open log file implicitly
 * - message is written with the following format: hh:mm:ss (script name) message
 */

class Logging{
	
	protected $_path;
	
    // define file pointer
    protected $_fp = null;
	
	public function setPath($path){
		$this->_path =  $path;
		return $this;
	}
		
	public function getPath(){
		if($this->_path == null){
			$this->_path = APPLICATION_PATH . '/temporary/log/deals-'. date('Y-m-d-H'). '-log.txt';
		}
		return $this->_path;
	}
		
	public function getFp(){
		if($this->_fp == null){
			$this->_fp = fopen($this->getPath(),'a');	
		}
		return $this->_fp;
	}
	
    // write message to the log file
    public function write($message){
        // if file pointer doesn't exist, then open log file        
        // define script name
        $script_name = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
        // define current time
        $time = date('H:i:s');
        // write current time, script name and message to the log file
        fwrite($this->getFp(), "$time ($script_name) \n,$message");
    }
	
	public function close(){
		if($this->_fp){
			fclose($this->_fp);
			$this->_fp = null;
		}
		return $this;
	}
	
	
}
