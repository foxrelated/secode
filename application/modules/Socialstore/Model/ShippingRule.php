<?php

class Socialstore_Model_ShippingRule extends Core_Model_Item_Abstract{
	
	public function getCategories() {
		$ShippingCat = new Socialstore_Model_DbTable_ShippingCats;
		$select = $ShippingCat->select()->where('shippingrule_id = ?', $this->shippingrule_id);
		$results = $ShippingCat->fetchAll($select);
		$cats = array();
		$CustomCategories = new Socialstore_Model_DbTable_Customcategories;
		foreach ($results as $result) {
			//$cats[$result->category_id] = $CustomCategories->getName($result->category_id);
			$cats[] = $result->category_id;
		}
		return $cats;
	}
	
	public function getCountries() {
		$ShippingCountry = new Socialstore_Model_DbTable_ShippingCountries;
		$select = $ShippingCountry->select()->where('shippingrule_id = ?', $this->shippingrule_id);
		$results = $ShippingCountry->fetchAll($select);
		$Countries = new Socialstore_Model_DbTable_Countries;
		$countries = array();
		foreach ($results as $result) {
			//$countries[$result->country_id] = $Countries->getName($result->country_id);
			$countries[] = $result->country_id;
		}
		return $countries;
	}
	
	public function getCatsCouns() {
		$ShippingRules = new Socialstore_Model_DbTable_ShippingRules;
		$rulesname = $ShippingRules->info('name');
		$select = $ShippingRules->select()->from($rulesname)->setIntegrityCheck(false);
		$select->where("$rulesname.shippingrule_id = ?", $this->shippingrule_id);
		$select->join('engine4_socialstore_shippingcats', "$rulesname.shippingrule_id = engine4_socialstore_shippingcats.shippingrule_id",array('engine4_socialstore_shippingcats.category_id as category_id','engine4_socialstore_shippingcats.shippingcat_id as shippingcat_id'));
		$select->join('engine4_socialstore_shippingcountries', "$rulesname.shippingrule_id = engine4_socialstore_shippingcountries.shippingrule_id",array('engine4_socialstore_shippingcountries.country_id as country_id','engine4_socialstore_shippingcountries.shippingcountry_id as shippingcountry_id'));
		$select->where("$rulesname.shippingmethod_id = $this->shippingmethod_id");
		$results = $ShippingRules->fetchAll($select);
		return $results->toArray();
	}
	
}
