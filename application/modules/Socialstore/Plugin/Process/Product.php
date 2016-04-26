<?php

class Socialstore_Plugin_Process_Product{
	protected $_product;
	
	public function getProduct(){
		return $this->_product;
	}	
	
	public function setProduct($product){
		$this->_product = $product;
		return $this;
	}
	
	public function process($cmd, $params = null){
		$method_name =  'process'. ucfirst( substr($cmd,0,1)) . substr($cmd, 1);
		if(method_exists($this, $method_name)){
			return $this->{$method_name}($params);
		}
		throw new Exception("method $method_name does not exist in trapped call ".__CLASS__);
	}
public function getLog($filename='store.notify.log'){
		$writer =  new Zend_Log_Writer_Stream(APPLICATION_PATH .'/temporary/log/'.$filename);
		return new Zend_Log($writer);
	}
	public function processAccept(){
		$product = $this->getProduct();
		$product->approve_status = 'approved';
		$date = date('Y-m-d H:i:s');
        $product->modified_date = $date;
		$product->approved_date  = $date;
        $product->save();
        
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
			$params['user_id'] = $product->owner_id;
			$params['rule_name'] = 'publish_product';
			$params['total_amount'] = $product->getTotalProductFee();
			$params['currency'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.currency', 'USD');
        	Engine_Hooks_Dispatcher::getInstance()->callEvent('onPaymentAfter', $params);
		}
        
        /**
         * End Call Event from Affiliate
         */
        
        
         //add activity feed.
        $table = Engine_Api::_()->getItemTable('social_product');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
          	$user =  Engine_Api::_()->getItem('user', $product->owner_id); 
            $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $product, 'social_product_new');
            if( $action != null ) {
              	Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $product);
            }
 		    $db->commit();
        }
        catch( Exception $e )
        {
            $db->rollBack();
            throw $e;
        }
        $owner = Engine_Api::_()->getItem('user', $product->owner_id);
		if($owner -> getIdentity())
		{
      		$email = $owner->email;
        	$params = $product->toArray();
  			Engine_Api::_()->getApi('mail','Socialstore')->send($email, 'store_approveproduct',$params);
		}
            // Send email to Store follower
    	$Follow = new Socialstore_Model_DbTable_Follows;
		$select = $Follow->select()->where('store_id = ?', $product->store_id);
		$results = $Follow->fetchAll($select);
		if (count($results) > 0) {
				   // send mail to follower
			$store = Engine_Api::_()->getItem('social_store', $product->store_id);
			$params['product_title'] =  $product->title;
			$params['store_title'] =  $store->title;
			$params['store_link'] =  $store->getHref();
			$params['product_link'] = $product->getHref();
			foreach($results as $result){
				if ($result->user_id != 0) 
				{
					$useremail = Engine_Api::_()->getItem('user', $result->user_id);
					if($useremail -> getIdentity())
					{
						$usmail = $useremail->email;
						Engine_Api::_()->getApi('mail','Socialstore')->send($useremail, 'store_follownotice',$params);
					}
				}
		    }
		}		
		return $product->save();
	}
	public function processDenied(){
		$product = $this->getProduct();
		$product->approve_status =  'denied';
		return $product->save();
	}
	
}
