<?php

class Socialstore_Model_DbTable_SocialProducts extends Engine_Db_Table{
	protected $_name = 'socialstore_products';
	
	protected $_rowClass =  'Socialstore_Model_Product';
	
	public function getProduct($product_id) {
		$select = $this->select()->where('product_id = ?', $product_id);
		return $this->fetchRow($select);
	}
}
