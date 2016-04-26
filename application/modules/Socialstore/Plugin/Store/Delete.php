<?php

class Socialstore_Plugin_Store_Delete{
	protected $_store;
	
	public function setStore($store){
		$this->_store =  $store;
		return $this;
	}
	
	public function getStore(){
		return $this->_store;
	}
	
	public function process(){
		
	}
}
