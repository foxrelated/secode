<?php

class Socialstore_Plugin_User {
	public function onUserLoginAfter($event) {
		$payload = $event -> getPayload();
		if($payload instanceof User_Model_User) { Socialstore_Api_Cart::getInstance() -> setOwner($payload);	
		}
		
	}
	
	public function onUserLogoutBefore($event){
		unset($_SESSION['STORE_CART']);
	}

}
