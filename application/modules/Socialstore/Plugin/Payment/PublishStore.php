<?php

class Socialstore_Plugin_Payment_PublishStore extends Socialstore_Plugin_Payment_Abstract{
	
	public function onSuccess(){
		$order = $this->getOrder();
		$order->payment_status   = 'completed';
		$order->save();
		$object = $this->getOrder()->getItem(0)->getObject();
		$user = Engine_Api::_()->getItem('user', $order->owner_id);
		$auto_approve = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('social_store', $user, 'store_approve');	

		if($auto_approve == 1) {
			$plugin =  new Socialstore_Plugin_Process_Store;
			$plugin->setStore($object)->process('accept');
	    }
	    else {
			$object->approve_status = 'waiting';
			$object->save();
	    }	    
	}
	
	public function onFailure(){
		$order = $this->getOrder();
		$order->payment_status = 'failure';
		$order->save();	
	}
	
	public function onPending(){
		$order = $this->getOrder();
		$order->payment_status =  'pending';
		$order->save();
	}
	
	public function onCancel(){
		$order = $this->getOrder();
		$order->payment_status =  'cancel';
		$order->save();
	}
	
	public function addItem($store, $quatity =1, $params){
		$Items =  new Socialstore_Model_DbTable_OrderItems;
		$item =  $Items->fetchNew();
		if($params['option']=='publish-store'){
			$item->name =  $item->description = 'publish store '. $store->getTitle();
			$item->object_type = 'publish-store';	
		}else{
			$item->name =  $item->description = 'feature store '. $store->getTitle();
			$item->object_type = 'feature-store';	
		}
		$item->sku =  'store';
		$item->store_id =  $store->store_id;
		$item->order_id = $this->getOrder()->getId();
		$item->object_id = $store->getIdentity();
		$item->currency = $this->getOrder()->currency;
		
		$item->quantity = 1;
		$amount =  $params['amount'];
		$item->pretax_price = $item->price = $item->sub_amount = $item->sub_amount =  $item->total_amount =   $amount;
		$item->save();
	}
	
	public function getSuccessRedirectUrl(){
		$router = Zend_Controller_Front::getInstance()->getRouter();
		$url =  $router->assemble(array('module'=>'socialstore','controller'=>'my-store'),'default',true);
		return $url;
	}
	
	public function getCancelRedirectUrl(){
		$router = Zend_Controller_Front::getInstance()->getRouter();
		$url =  $router->assemble(array('module'=>'socialstore','controller'=>'my-store'),'default',true);
		return $url;
	}
}
