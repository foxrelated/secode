<?php
class Ynaffiliate_Model_DbTable_Requests extends Engine_Db_Table
{
 	protected $_rowClass = "Ynaffiliate_Model_Request";
 	
 	public function getRequestedPoints($user_id = null) 
 	{
 		$select = $this -> select() -> from($this -> info('name'), 'SUM(request_points) AS points') -> where('request_status = ?', 'completed' );
		if($user_id)
		{
			$select -> where('user_id = ?', $user_id);
		}
		return $select->query()->fetchColumn(0);
 	}
 	
 	public function getCurrentRequestPoints($user_id = null) {
 		$select = $this -> select() -> from($this -> info('name'), 'SUM(request_points) AS points') -> where('request_status in ("waiting", "pending")');
		if($user_id)
		{
			$select -> where('user_id = ?', $user_id);
		}
		return $select->query()->fetchColumn(0);
 	}
}
