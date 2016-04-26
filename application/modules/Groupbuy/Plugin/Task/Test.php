<?php
class Groupbuy_Plugin_Task_Test extends Groupbuy_Plugin_Task_Abstract {

	/**
	 * @return Groupbuy_Model_DbTable_Deals
	 */
	public function getDealsTable() {
		return Engine_Api::_() -> getDbTable('deals', 'groupbuy');
	}
	
	public function testLocations(){
			$table  = new Groupbuy_Model_DbTable_Locations();
			$location = $table->getNodeRandom();
			echo $location;
			echo "\n";
			$table  = new Groupbuy_Model_DbTable_Deals();
			$deal  = $table->fetchRow();
			echo $deal->getLocation();
	}
	public function execute() {
		try {
			$table =  new Groupbuy_Model_DbTable_Deals;
			$mail =Engine_Api::_() -> getApi('Mail', 'Groupbuy'); 
				
			$deal  = $table->find(51)->current();
			$deal->updateToCancel();
			
			 
				
		} catch(Exception $e) {
			$this -> writeLog(sprintf("%s: ERROR %s",date('Y-m-d H:i:s'), $e -> getMessage()));
			echo $e->getMessage();
		}
	}

}
