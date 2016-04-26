<?php

class Socialstore_Api_Transaction {

	static private $_instance;

	private function __construct() {

	}
	
	static public function getInstance() {
		if(self::$_instance == NULL) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	static public function addTransaction($params1, $params2 = null, $params3 = null, $params4 = null, $params5 = null) {
		$Trans = new Socialstore_Model_DbTable_PayTrans;
		$trans = $Trans -> fetchNew();

		$trans -> setFromArray($params1);

		if(is_array($params2)) {
			$trans -> setFromArray($params2);
		}

		if(is_array($params3)) {
			$trans -> setFromArray($params3);
		}
		if(is_array($params4)) {
			$trans -> setFromArray($params4);
		}
		if(is_array($params5)) {
			$trans -> setFromArray($params5);
		}

		$trans -> save();
	}
	
	public function getTransactionsPaginator($params = array())
	{
	    $paginator = Zend_Paginator::factory($this->getTransactionsSelect($params));
	   
	    if( !empty($params['page']) )
	    {
	      $paginator->setCurrentPageNumber($params['page']);
	    }
	    if( !empty($params['limit']) )
	    {
	      $paginator->setItemCountPerPage($params['limit']);
	    }
	    return $paginator;
	}
	  
	public function getTransactionsSelect($params = array())
	{
	  	$table = new Socialstore_Model_DbTable_PayTrans();
	    $rName = $table->info('name');
	    $select = $table->select()->from($rName)->setIntegrityCheck(false);
		$userTable  = new User_Model_DbTable_Users;
		$userName = $userTable->info('name');
	    $select -> joinLeft($userName, "owner_id = user_id",'username as owner_name');
	    $select->where("payment_status = 'pending'");
		$select->orWhere("payment_status = 'completed'");
		
	    // by search
		

	   if(@$params['gateway'] && $params['gateway'] != '')
	   {
	      $select->where($rName.".gateway =?", $params['gateway']);
	   }
	   if(@$params['transaction_id'] && $params['transaction_id'] != '')
	   {
	      $select->where($rName.".transaction_id = ? ", $params['transaction_id']);
	   }
	   if( isset($params['payment_status']) && $params['payment_status'] != '')
	   {
	   	  $select->where($rName.".payment_status = ? ",$params['payment_status']);
	   }

	   if( isset($params['order_id']) && $params['order_id'] != '')
	   {
	   	  $select->where($rName.".order_id = ? ",$params['order_id']);
	   }
	   
	    // by Buyer
	    
   	    if(!empty($params['owner_name']) && $params['owner_name'] != "") {
	    	$select->where("$userName.username LIKE ?",'%'.$params['owner_name'].'%');
   	    }	
	    	

		
	    // by status
	    
		
	  	if(isset($params['status']) && $params['status'] != ''){
			$select->where("$rName.state = ?", $params['status']);
		}
	    
		if(isset($params['orderby']) && $params['orderby'])
	       	$select->order($params['orderby'].' DESC');
	    
	    elseif (!empty($params['order'])) {
			$select->order($params['order'].' '.$params['direction']);
		}
	    else
		{
	        $select->order("$rName.creation_date DESC");
	    }
	    
	    
		if(getenv('DEVMODE') == 'localdev'){
			print_r($params);
			echo $select;	
		}
		//echo $select; die;
	    return $select;
	  }

}
