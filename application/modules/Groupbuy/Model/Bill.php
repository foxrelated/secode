<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Groupbuy
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Bill.php
 * @author     Minh Nguyen
 */
class Groupbuy_Model_Bill extends Core_Model_Item_Abstract
{
	
	/**
	 * @param  $spliter  [OPTIONAL]
	 * @return string coupon code [dddd-ddd-dddd-dddd]
	 */
	public function getCoupons($spliter = null){
		$table =  new Groupbuy_Model_DbTable_Coupons;
		$select = $table->select()->where('bill_id=?', $this->getIdentity());
		$rows = $table->fetchAll($select);
		if($spliter === null){
			return $rows;
		}
		
		$result =  array();
		foreach($rows as $row){
			$result[] =  $row->code;
		}
		return implode(' - ', $result);
	}
	
	/**
	 * @param   identity   $gift_id
	 * @return  Groupbuy_Model_Gift|NULL
	 */
	public function getGift($gift_id = null){
		$table =  new Groupbuy_Model_DbTable_Gifts;
		
		// if gift id is null
		if($gift_id === null){
			$select = $table->select()->where('bill_id=?', $this->getIdentity());
			return $table->fetchRow($select);			
		}
		
		// get gift by gift id.
		return  $table->find($gift_id)->current();

	}
}