<?php

class Groupbuy_Model_DbTable_SubscriptionContacts extends Engine_Db_Table {
	protected $_name = 'groupbuy_subscription_contacts';
	protected $_rowClass = 'Groupbuy_Model_SubscriptionContact';

	public function isEmail($email) {
		if($email) {
			return true;
		}
		return false;
	}
	
	/**
	 * generate random string
	 *
	 * @param  int $length
	 * @return string
	 */
	public function generateRandomCode($length = 32) {
		$seeks = '123456789qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
		$len = strlen($seeks) - 1;
		$ret = "";
		for($i = 0; $i < $length; ++$i) {
			$ret .= substr($seeks, mt_rand(0, $len), 1);
		}
		return $ret;
	}

	public function addContact($data) {

		$email = "";
		if(is_string($data)) {
			$email = $data;
		}else if(is_array($data) && isset($data['email']) && $data['email']) {
			$email = $data['email'];
		}else if(is_object($data) && isset($data -> email)) {
			$email = $data['email'];
		}

		if(!$this -> isEmail($email)) {
			throw new Exception('Can not add contact!');
		}

		$item = $this -> fetchRow($this -> select() -> where('email=?', $email));
		if(!is_object($item)) {
			$item = $this -> fetchNew();
			$item -> setFromArray((array)$data);
			$item -> email = $email;
			$item->verify_code = $this->generateRandomCode();
			$item -> save();
			  
		}
		return $item;

	}
	
	/**
	 * @param   array   $data
	 * @return  Groupbuy_Model_SubscriptionCondition
	 */
	public function addCondition($data) {
		// check to add more condition from array of message;
		$contact = $this -> addContact($data);
		return $contact -> addCondition($data);
	}

}
