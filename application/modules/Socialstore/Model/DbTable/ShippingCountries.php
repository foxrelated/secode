<?php

class Socialstore_Model_DbTable_ShippingCountries extends Engine_Db_Table{
	
	protected $_name = 'socialstore_shippingcountries';
	
	protected $_rowClass =  'Socialstore_Model_ShippingCountry';
	
	public function checkCountry($country_id,$shippingrule_id = null,$shippingmethod_id) {
		$select = $this->select()->where("country_id = '$country_id' OR country_id = '0'")->where('shippingmethod_id = ?', $shippingmethod_id);
		if ($shippingrule_id) {
			$select->where('shippingrule_id != ?', $shippingrule_id);
		}
		$result = $this->fetchRow($select);
		if (count($result) > 0) {
			return true;
		}
		return false;
	}
	public function checkFreeCountry($country_id,$shippingrule_id = null,$shippingmethod_ids) {
		$select = $this->select()->where("country_id = '$country_id' OR country_id = '0'")->where('shippingmethod_id IN (?)', $shippingmethod_ids);
		if ($shippingrule_id) {
			$select->where('shippingrule_id != ?', $shippingrule_id);
		}
		$result = $this->fetchRow($select);
		if (count($result) > 0) {
			return true;
		}
		return false;
	}
	
	public function getCountriesByCatId($catId,$shippingrule_id = null,$shippingmethod_id){
		$ShippingCats = new Socialstore_Model_DbTable_ShippingCats;
		$select = $ShippingCats->select()->where("category_id = $catId OR category_id ='0'")->where('shippingmethod_id = ?', $shippingmethod_id);
		if ($shippingrule_id) {
			$select->where('shippingrule_id != ?', $shippingrule_id);
		}
		$results = $ShippingCats->fetchAll($select);
		$rules = array();
		$countries = array();
		if (count($results) > 0) {
			foreach($results as $result) {
				$rules[] = $result->shippingrule_id;
			}
			foreach($rules as $rule) {
				$select = $this->select()->where("shippingrule_id = ?", $rule);
				$results = $this->fetchAll($select);
				foreach ($results as $result) {
					$countries[] = $result->country_id;
				}
			}
		}
		return $countries;
	}
	public function getFreeCountriesByCatId($catId,$shippingrule_id = null,$shippingmethod_ids){
		$ShippingCats = new Socialstore_Model_DbTable_ShippingCats;
		$select = $ShippingCats->select()->where("category_id = $catId OR category_id ='0'")->where('shippingmethod_id IN (?)', $shippingmethod_ids);
		if ($shippingrule_id) {
			$select->where('shippingrule_id != ?', $shippingrule_id);
		}
		$results = $ShippingCats->fetchAll($select);
		$rules = array();
		$countries = array();
		if (count($results) > 0) {
			foreach($results as $result) {
				$rules[] = $result->shippingrule_id;
			}
			foreach($rules as $rule) {
				$select = $this->select()->where("shippingrule_id = ?", $rule);
				$results = $this->fetchAll($select);
				foreach ($results as $result) {
					$countries[] = $result->country_id;
				}
			}
		}
		return $countries;
	}
	
	public function deleteCountry($rule_id) {
		$select = $this->select()->where("shippingrule_id = ?", $rule_id);
		$results = $this->fetchAll($select);
		foreach ($results as $result) {
			$result->delete();
		}
	}
	
}
