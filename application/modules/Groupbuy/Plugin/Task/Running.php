<?php
class Groupbuy_Plugin_Task_Running extends Groupbuy_Plugin_Task_Abstract {
	
	static $limit =  10;
	/**
	 * @return Groupbuy_Model_DbTable_Deals
	 */
	public function getDealsTable() {
		return Engine_Api::_() -> getDbTable('deals', 'groupbuy');
	}
	
	public function deleteExpiredRelations($db){
		
	}

	public function updateSubscriptionEmail($db, $table, $cur_time) {
		$this->deleteExpiredRelations($db);
		$sql = "
			INSERT IGNORE INTO engine4_groupbuy_subscription_relations( deal_id, contact_id,creation_date)
			select deals.deal_id as deal_id, conditions.contact_id as contact_id, '$cur_time' as creation_date
			from engine4_groupbuy_deals as deals
			join engine4_groupbuy_categories as categories on (deals.category_id = categories.category_id)
			join engine4_groupbuy_locations as locations on (deals.location_id = locations.location_id)
			join engine4_groupbuy_subscription_conditions as conditions on (
			 conditions.category_id in (
			 	select category_id 
				from engine4_groupbuy_categories as cat
				where cat.pleft <= categories.pleft and cat.pright >= categories.pright
				)
				and 
				conditions.location_id in (
			 	select location_id 
				from engine4_groupbuy_locations as loc
				where loc.pleft <= locations.pleft and loc.pright >= locations.pright
				)
				and
				conditions.within > (3959 * acos(cos(radians(conditions.lat)) * cos(radians(deals.latitude)) * cos(radians(deals.longitude) - radians(conditions.long)) + sin(radians(conditions.lat)) * sin(radians(deals.latitude))))
			)
			where 
				1 
				and deals.published = 20
				and deals.status in (20,30)
				and deals.start_time < '$cur_time' 
				and deals.end_time > '$cur_time'
				and deals.is_delete = 0
				and deals.stop = 0
			group by deals.deal_id, conditions.contact_id
		";
		
		return  $db->query($sql);
	}
	
	public function updateToRunning($db, $table,  $cur_time){
		$select = $table->select()->where('status !=30 and published=20 and is_delete = 0 and stop =0')->where('start_time < ?', $cur_time)->where('end_time>?',$cur_time)->limit(self::$limit);
		foreach($table->fetchAll($select) as $deal){
			try{
				$deal->updateToRunning();
			}catch (Exception $e){
				echo $e->getMessage();
			}
		}
		
	}

	public function updateToClose($db, $table, $cur_time){
		// find out howmany users closed by this item.
		$select = $table->select()->where('status=30 and published=20 and current_sold >= min_sold and is_delete =0 and stop =0')->where('end_time < ?', $cur_time)->limit(self::$limit);
		foreach($table->fetchAll($select) as $deal){
			try{
				$deal->updateToClose();
			}catch(Exception $e){
			}
		}		
	}
	
	//	
	public function updateToCancel($db, $table, $cur_time){
		$select = $table->select()->where('status=30 and published=20 and current_sold < min_sold and is_delete = 0 and stop=0')->where('end_time < ?', $cur_time)->limit(self::$limit);
		foreach($table->fetchAll($select) as $deal){
			try{
				$deal->updateToCancel();	
			}catch (Exception $e){
				echo $e->getMessage();
			}
		}
		
	}
	
	public function sendTopMails(){
		Engine_Api::_()->getApi('Mail','Groupbuy')->sendMailQueue();
	}
	
	//
	public function execute() {
		$table = $this -> getDealsTable();
		$db = $table -> getAdapter();
		$cur_time = date('Y-m-d H:i:s');
		
		try {
			$this->updateToCancel($db, $table, $cur_time);
			$this->updateToRunning($db, $table, $cur_time);			
			$this->updateToClose($db, $table, $cur_time);
			$this -> updateSubscriptionEmail($db, $table, $cur_time);
			$this->sendTopMails();
			
		} catch(Exception $e) {
			if(APPLICATION_ENV == 'development'){
				throw $e;	
			}
			$this -> writeLog(sprintf("%s: ERROR Groupbuy_Plugin_Task_Running::execute %s", date('Y-m-d H:i:s'), $e -> getMessage()));
		}
	}
}
