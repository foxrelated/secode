<?php  
class Socialstore_Api_Shipping extends Core_Api_Abstract {
	public function getRuleCat($customcategory_id) {
		if ($customcategory_id == 0) {
			return Zend_Registry::get('Zend_Translate')->translate('All Categories');
		}
		$Customcategory = new Socialstore_Model_DbTable_Customcategories;
		$select = $Customcategory->select()->where('customcategory_id = ?', $customcategory_id);
		$result = $Customcategory->fetchRow($select);
		return $result->name;
	}
	public function getRuleCountry($country_id) {
		if ($country_id == '0') {
			return Zend_Registry::get('Zend_Translate')->translate('All Countries');
		}
		$sql = "select country from engine4_socialstore_countries where code = '$country_id'";
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchRow($sql);
		return $result['country'];
	}
	public function checkCatCounExisted($shippingmethod_id,$shippingrule_id = null,$allCats = null,$catIds = null,$allCountries = null,$countries = null){
		$ShippingRules = new Socialstore_Model_DbTable_ShippingRules;
		$shippingrules = $ShippingRules->getShippingRulesByMethod($shippingmethod_id);
		$ShippingCats = new Socialstore_Model_DbTable_ShippingCats;
		$ShippingCountries = new Socialstore_Model_DbTable_ShippingCountries;
		if ($allCats == 1)  {
			if ($allCountries == 1) {
				if (count($shippingrules) > 1) {
					return false;
				} 
			}
			else {
				foreach ($countries as $country) {
					$checkCountry = $ShippingCountries->checkCountry($country,$shippingrule_id,$shippingmethod_id);
					if ($checkCountry == true) {
						return false;
					}
				}
			}
		}
		else {
			if ($allCountries == 1) {
				foreach ($catIds as $catId) {
					$checkCat = $ShippingCats->checkCat($catId,$shippingrule_id,$shippingmethod_id);
					if ($checkCat == true) {
						return false;
					}
				}
			}
			else {
				$countries_array = array();
				foreach ($catIds as $catId) {
					if ($countries_array == null) {
						$countries_array = $ShippingCountries->getCountriesByCatId($catId,$shippingrule_id,$shippingmethod_id);
					}
					else {
						$countries_temp = $ShippingCountries->getCountriesByCatId($catId,$shippingrule_id,$shippingmethod_id);
						if (count($countries_temp) > 0) {
							$countries_array = array_merge($countries_array,$countries_temp);
						}
					}
				}
				if (count($countries_array)> 0) {
					if (in_array('0', $countries_array)) {
						return false;
					}
					$coun_intersect = array_intersect($countries_array, $countries);
					if (count($coun_intersect) > 0) {
						return false;
					}
				}
			}	
		}
		return true;
	}
	public function checkFreeCatCounExisted($store_id,$shippingrule_id = null,$allCats = null,$catIds = null,$allCountries = null,$countries = null,$method_id = null){
		$ShippingRules = new Socialstore_Model_DbTable_ShippingRules;
		$ShippingMethods = new Socialstore_Model_DbTable_ShippingMethods;
		$methods = $ShippingMethods->getFreeShippingMethods($store_id);
		$method_ids = array();
		foreach ($methods as $method) {
			if (!$method_id || $method_id != $method->shippingmethod_id) {
				$method_ids[] = $method->shippingmethod_id;
			}
		}
		$shippingrules = $ShippingRules->getShippingRulesByMethods($method_ids);
		$ShippingCats = new Socialstore_Model_DbTable_ShippingCats;
		$ShippingCountries = new Socialstore_Model_DbTable_ShippingCountries;
		if ($allCats == 1)  {
			if ($allCountries == 1) {
				if (count($shippingrules) > 0) {
					return false;
				} 
			}
			else {
				foreach ($countries as $country) {
					$checkCountry = $ShippingCountries->checkFreeCountry($country,null,$method_ids);
					if ($checkCountry == true) {
						return false;
					}
				}
			}
		}
		else {
			
			if ($allCountries == 1) {
				foreach ($catIds as $catId) {
					$checkCat = $ShippingCats->checkFreeCat($catId,null,$method_ids);
					if ($checkCat == true) {
						return false;
					}
				}
			}
			else {
						
				$countries_array = array();
				foreach ($catIds as $catId) {
					if ($countries_array == null) {
						$countries_array = $ShippingCountries->getFreeCountriesByCatId($catId,null,$method_ids);
					}
					else {
						$countries_temp = $ShippingCountries->getFreeCountriesByCatId($catId,null,$method_ids);
						if (count($countries_temp) > 0) {
							$countries_array = array_merge($countries_array,$countries_temp);
						}
					}
				}
				if (count($countries_array)> 0) {
					if (in_array('0', $countries_array)) {
						return false;
					}
					$coun_intersect = array_intersect($countries_array, $countries);
					if (count($coun_intersect) > 0) {
						return false;
					}
				}
			}	
		}
		return true;
	}
	
	public function getRules($store_id,$shippingaddress_id,$category_id,$order_id) {
		$ShippingAddress = new Socialstore_Model_DbTable_ShippingAddresses;
		$country = $ShippingAddress->getShippingCountry($shippingaddress_id);
		$ShippingRules = new Socialstore_Model_DbTable_ShippingRules;
		$rules = $ShippingRules->getRules($store_id, $country, $category_id, $order_id);
		return $rules;
	}
	
	public function getAddressString($value) {
		$address = Zend_Json::decode($value);
		$count = count((array)$address);
		$i = 0;
		$add = '';
		foreach ($address as $a) {
			$i++;
			if ($i == $count) {
				$add .= $a;
			}
			else {
				if ($a != '') {
					$a .= ', ';
					$add .= $a;
				}
			}
		}
		return $add;
	}
	
}
	