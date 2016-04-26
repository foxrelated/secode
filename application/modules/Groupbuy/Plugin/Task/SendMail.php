<?php
class Groupbuy_Plugin_Task_SendMail extends Groupbuy_Plugin_Task_Abstract {

	public function execute() {
		try {
			Engine_Api::_()->getApi('Mail','Groupbuy')->sendMailQueue();
			$this -> writeLog(sprintf("%s: run send mail", date('Y-m-d H:i:s')));
		} catch(Exception $e) {
			$this -> writeLog(sprintf("%s: ERROR %s",date('Y-m-d H:i:s'), $e -> getMessage()));
			throw $e;				
			if(APPLICATION_ENV == 'development'){
				throw $e;
			}			
		}
	}
}
