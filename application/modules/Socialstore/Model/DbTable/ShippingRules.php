<?php

class Socialstore_Model_DbTable_ShippingRules extends Engine_Db_Table{
	
	protected $_name = 'socialstore_shippingrules';
	
	protected $_rowClass =  'Socialstore_Model_ShippingRule';
	
	public function getShippingRulesByMethod($shippingmethod_id) {
		$select = $this->select()->where('shippingmethod_id = ?', $shippingmethod_id);
		$results = $this->fetchAll($select);
		return $results;
	}
	public function getShippingRulesByMethods($shippingmethod_ids) {
		$select = $this->select()->where('shippingmethod_id IN (?)', $shippingmethod_ids);
		$results = $this->fetchAll($select);
		return $results;
	}
	
	public function getRuleById($rule_id) {
		$select = $this->select()->where('shippingrule_id = ?', $rule_id);
		$result = $this->fetchRow($select);
		return $result;
	}
	
	public function getRules($store_id, $country, $category, $order_id) {
		$rulesname = $this->info('name');
		$select = $this->select()->from($rulesname, array('shippingrule_id'))->setIntegrityCheck(false);
		$select->join('engine4_socialstore_shippingmethods', "$rulesname.shippingmethod_id = engine4_socialstore_shippingmethods.shippingmethod_id",array('engine4_socialstore_shippingmethods.name as name','engine4_socialstore_shippingmethods.description as description','engine4_socialstore_shippingmethods.free_shipping as free_shipping'));
		$select->join('engine4_socialstore_shippingcats', "$rulesname.shippingrule_id = engine4_socialstore_shippingcats.shippingrule_id",array(''));
		$select->join('engine4_socialstore_shippingcountries', "$rulesname.shippingrule_id = engine4_socialstore_shippingcountries.shippingrule_id",array(''));
		$select->where("engine4_socialstore_shippingmethods.store_id = $store_id");
		$select->where("$rulesname.enabled = 1");
		$select->where("engine4_socialstore_shippingcats.category_id = $category OR engine4_socialstore_shippingcats.category_id = 0");
		$select->where("engine4_socialstore_shippingcountries.country_id = '$country' OR engine4_socialstore_shippingcountries.country_id = '0'");
		$select->group("$rulesname.shippingrule_id");
		$results = $this->fetchAll($select);
		if (count($results) > 0) {
			$rules = $results->toArray();
			$return_rules = array();
			foreach ($rules as $rule) {
				if ($rule['free_shipping'] == 1) {
					$select = $this->select()->where('shippingrule_id = ?', $rule['shippingrule_id']);
					$result = $this->fetchRow($select);
					$minimumAmount = $result->order_minimum;
					$order = Socialstore_Model_DbTable_Orders::getByOrderId($order_id);
					$orderAmount = $order->getTotalAmountByStore($store_id);
					if ($orderAmount >= $minimumAmount) {
						$array = array();
						$temp_rule = array();
						$temp_rule['id'] = $rule['shippingrule_id'];
						$temp_rule['name'] = $rule['name'];
						$array[] = $temp_rule;
						return $array;
					}
				}
				else {
					$temp_rule = array();
					$temp_rule['id'] = $rule['shippingrule_id'];
					$temp_rule['name'] = $rule['name'];
					$return_rules[] = $temp_rule;
				}
			}
			return $return_rules;
		}
		else {
			return false;
		}
	}
}
