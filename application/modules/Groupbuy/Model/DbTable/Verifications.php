<?php

class Groupbuy_Model_DbTable_Verifications extends Engine_Db_Table {
	protected $_rowClass = 'Groupbuy_Model_Verification';

	/**
	 * generate random string
	 *
	 * @param  int $length
	 * @return string
	 */
	public function generateRandomCode($length = 8) {
		$seeks = '123456789qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
		$len = strlen($seeks) - 1;
		$ret = "";
		for($i = 0; $i < $length; ++$i) {
			$ret .= substr($seeks, mt_rand(0, $len), 1);
		}
		return $ret;
	}

	/**
	 * @param  int      $item_id
	 * @param  string   $verify_action
	 * @param  string   $item_action
	 */
	public function addVerify($item_id, $verify_action, $day = null) {
		$item = $this -> fetchNew();
		$item -> verify_code = $this -> generateRandomCode();
		$item -> item_id = $item_id;
		$item -> verify_action = $verify_action;
		if($day == null) {
			$day = 30;
		}

		$item -> expired_date = date('Y-m-d H:i:s', time() + $day * 24 * 3600);
		$item -> save();
		return $item->verify_code;
	}

	/**
	 * @param    string $verify_code
	 * @param    string $verify_action
	 * @return   Groupbuy_Model_Verification
	 */
	public function getVerify($verify_code, $verify_action) {
		return $this -> fetchRow($this -> select() -> where('verify_code=?', $verify_code)->where('verify_action=?',$verify_action));
	}

}
