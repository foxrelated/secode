<?php

class Socialstore_Plugin_Process_Store{
	protected $_store;
	
	/**
	 * @return Socialstore_Model_Store
	 */
	public function getStore(){
		return $this->_store;
	}	
	
	public function setStore($store){
		$this->_store = $store;
		return $this;
	}
	
	public function process($cmd, $params = null){
			
		$method_name =  'process'. Engine_Api::deflect($cmd);
		
		if(method_exists($this, $method_name)){
			return $this->{$method_name}($params);
		}
		throw new Exception("method $method_name does not exist in trapped call ".__CLASS__);
	}
	
	public function processAccept(){
		$store = $this->getStore();
		$store->approve_status = 'approved';
		$date = date('Y-m-d H:i:s');
        $store->modified_date = $date;
		$store->approved_date  = $date;
        $store->save();
		
        /**
         * Call Event from Affiliate
         */
        $module = 'ynaffiliate';
        $modulesTable = Engine_Api::_()->getDbtable('modules', 'core');
			$mselect = $modulesTable->select()
			->where('enabled = ?', 1)
			->where('name  = ?', $module);
		$module_result = $modulesTable->fetchRow($mselect);
		if(count($module_result) > 0)	{
			$params['module'] = 'socialstore';
			$params['user_id'] = $store->owner_id;
			$params['rule_name'] = 'publish_store';
			$params['total_amount'] = $store->getTotalStoreFee();
			$params['currency'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.currency', 'USD');
        	Engine_Hooks_Dispatcher::getInstance()->callEvent('onPaymentAfter', $params);
		}
        
        /**
         * End Call Event from Affiliate
         */
        
        // activity feed.
        
    	$user =  Engine_Api::_()->getItem('user', $store->owner_id); 
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $store, 'social_store_new');
        if( $action != null ) {
        	Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $store);
        }
        $owner = $store -> getOwner();
		if($owner -> getIdentity())
		{
        	$sendTo = $store->getOwner()->email;
        	$params = $store->toArray();
      		Engine_Api::_()->getApi('mail','Socialstore')->send($sendTo, 'store_approvestore',$params);
		}
		
		return $store->save();
	}
	
	public function processDenied(){
		$store = $this->getStore();
		$store->approve_status =  'denied';
		return $store->save();
	}
}
