<?php

class Socialstore_Model_DbTable_Orders extends Engine_Db_Table{
	
	/**
	 * Model class name
	 * @var string
	 */
	protected $_rowClass = 'Socialstore_Model_Order';
	
	/**
	 * Model table name
	 * @var string
	 */
	protected $_name =  'socialstore_orders';
	
	public function fetchNew(){
		$item =  parent::fetchNew();
		$length = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.orderidlength', 8);
		$item->order_id = self::generateCode($length);
		$item->creation_date =  date('Y-m-d H:i:s');
		return $item;
	}
	
	/**
	 * @return $string
	 */
	static public function generateCode($length){
		$seeks =  '1234567890ZXCVBNMASDFGHJKLQWERTYUIOP';
		$max =  strlen($seeks)-1;
		$result =  '';
		for($i=0; $i<$length; ++$i){
			$result .= substr($seeks, mt_rand(0, $max),1);
		}
		return $result;
	}
	
	static public function getByOrderId($order_id){
		$self =  new self;
		return $self->find($order_id)->current();
	}
}
