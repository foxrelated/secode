<?php
class Groupbuy_Model_DbTable_Coupons extends Engine_Db_Table {
	protected $_rowClass = 'Groupbuy_Model_Coupon';

	/**
	 * generate random string
	 *
	 * @param  int $length
	 * @return string
	 */
	public function generateRandomCode($length = 12) {
		$seeks = '123456789qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
		$len = strlen($seeks) - 1;
		$ret = "";
		for($i = 0; $i < $length; ++$i) {
			$ret .= substr($seeks, mt_rand(0, $len), 1);
		}
		return $ret;
	}
	
	public function addCoupon($user, $deal, $method_id, $method, $tranid) {
		$item = $this->fetchNew();
		$item->code = $this->generateRandomCode();
		$item->trans_id = $tranid;
		if ($method ==  "0") {
			$item->bill_id = $method_id;
		}
		else {
			$item->cod_id = $method_id;
		}
		$item->deal_id = $deal;
		$item->user_id = $user;
		$item->creation_date = date('Y-m-d H:i:s');
		$item->save();
		
	}
	
}