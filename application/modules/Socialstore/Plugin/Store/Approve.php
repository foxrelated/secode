<?php

class Socialstore_Plugin_Store_Approve{
	protected $_store;
	
	public function setStore($store){
		$this->_store =  $store;
		return $this;
	}
	
	public function getStore(){
		return $this->_store;
	}
	
	public function approve(){
		$store = $this->getStore();
		$store->approve_status = 'approved';
		$store->save();
	}
}
