<?php

class Socialstore_Model_ShippingMethod extends Core_Model_Item_Abstract{
	public function getShippingRule($active = null) {
		$ShippingRules = new Socialstore_Model_DbTable_ShippingRules;
		$rulesname = $ShippingRules->info('name');
		$select = $ShippingRules->select()->from($rulesname)->setIntegrityCheck(false);
		$select->join('engine4_socialstore_shippingmethods', "$rulesname.shippingmethod_id = engine4_socialstore_shippingmethods.shippingmethod_id",array('engine4_socialstore_shippingmethods.name as name','engine4_socialstore_shippingmethods.description as description'));
		$select->join('engine4_socialstore_shippingcats', "$rulesname.shippingrule_id = engine4_socialstore_shippingcats.shippingrule_id",array('engine4_socialstore_shippingcats.category_id as category_id','engine4_socialstore_shippingcats.shippingcat_id as shippingcat_id'));
		$select->join('engine4_socialstore_shippingcountries', "$rulesname.shippingrule_id = engine4_socialstore_shippingcountries.shippingrule_id",array('engine4_socialstore_shippingcountries.country_id as country_id','engine4_socialstore_shippingcountries.shippingcountry_id as shippingcountry_id'));
		$select->where("$rulesname.shippingmethod_id = $this->shippingmethod_id");
		if ($active) {
			$select->where("$rulesname.enabled = 1");
		}
		$results = $ShippingRules->fetchAll($select);
		return $results->toArray();
	}
}
