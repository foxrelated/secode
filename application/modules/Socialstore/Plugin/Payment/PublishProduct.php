<?php

class Socialstore_Plugin_Payment_PublishProduct extends Socialstore_Plugin_Payment_Abstract{
	public function onSuccess(){
		$order = $this->getOrder();
		$order->payment_status   = 'completed';
		$order->save();
		$object = $this->getOrder()->getItem(0)->getObject();
		$user = Engine_Api::_()->getItem('user', $order->owner_id);
		$auto_approve = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('social_product', $user, 'product_approve');	
		if($auto_approve == 1) {
			$plugin =  new Socialstore_Plugin_Process_Product;
			$plugin->setProduct($object)->process('accept');
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
	
	public function addItem($product, $quatity =1, $params){
		$Items =  new Socialstore_Model_DbTable_OrderItems;
		$item =  $Items->fetchNew();
		
		$option =  $params['option'];
		if($option =='publish-product'){
			$item->name =  $item->description = 'publish product '.$product->getTitle();	
			$item->object_type = 'publish-product';
		}else{
			$item->name =  $item->description = 'feature product '.$product->getTitle();
			$item->object_type = 'feature-product';
		}
		
		$item->order_id = $this->getOrder()->getId();
		$item->object_id = $product->getIdentity();
		$orderItem ->store_id =  $item->store_id;
		$item->quantity = 1;
		$item->currency = $this->getOrder()->currency;
		$item->sku = 'product';
		
		$amount =  $params['amount'];
		$item->pretax_price = $item->price = $item->sub_amount = $item->sub_amount =  $item->total_amount =   $amount;
		$item->save();
	}
	
	public function getSuccessRedirectUrl(){
		$router = Zend_Controller_Front::getInstance()->getRouter();
		$url =  $router->assemble(array('module'=>'socialstore','controller'=>'my-store','action'=>'my-products'),'default',true);
		return $url;
	}
	
	public function getCancelRedirectUrl(){
		$router = Zend_Controller_Front::getInstance()->getRouter();
		$url =  $router->assemble(array('module'=>'socialstore','controller'=>'my-store','action'=>'my-products'),'default',true);
		return $url;
	}
}
