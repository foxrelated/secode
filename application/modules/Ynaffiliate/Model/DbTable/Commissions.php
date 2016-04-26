<?php

class Ynaffiliate_Model_DbTable_Commissions extends Engine_Db_Table
{
	protected $_rowClass = "Ynaffiliate_Model_Commission";

	public function addCommission($params)
	{
		try
		{
			$commission = $this -> fetchNew();
			$user_id = $params['affiliate_id'];
			$rule_name = $params['rule_name'];
			$level = $params['level'];
			$Rules = new Ynaffiliate_Model_DbTable_Rules;
			$rule = $Rules -> getRuleByName($rule_name);
			if ($rule)
			{
				$rule_id = $rule -> rule_id;
			}
			else
			{
				return;
			}
			$RulemapDetails = new Ynaffiliate_Model_DbTable_Rulemapdetails;
			$rulemap = $RulemapDetails -> getRuleMapDetail('', $user_id, $rule_id, '', $level);
			if (count($rulemap) > 0)
			{
				// get the correct rule map by level
				$rulemap_id = $rulemap -> rule_map;
				$rule_value = $rulemap -> rule_value;
				$rule_type = $rulemap -> rule_type;
				$rule_map = $rulemap -> rule_map;
				$rule_map_detail = $rulemap -> rulemapdetail_id;
				$total_amount = $params['total_amount'];
				if ($rule_type == 0)
				{
					$commission_rate = $rule_value;
					$commission_amount = round($total_amount * $commission_rate / 100, 2);
					$commission_type = 0;
				}
				else
				{
					$commission_rate = 0;
					$commission_amount = $rule_value;
					$commission_type = 1;
				}
				$point_convert_rate = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynaffiliate.pointrate', 1);
				$purchase_currency = $params['currency'];
				$base_currency = Engine_Api::_() -> getApi('settings', 'core') -> payment['currency'];
				if ($purchase_currency != $base_currency)
				{
					$ExchangeRates = new Ynaffiliate_Model_DbTable_Exchangerates;
					$new_commission_amount = $ExchangeRates -> calculatePoints($purchase_currency, $commission_amount);
					if ($new_commission_amount)
					{
						$commission_point = $point_convert_rate * $new_commission_amount;
					}
					else
					{
						$commission_point = 0;
					}
				}
				else
				{
					$commission_point = $point_convert_rate * $commission_amount;
				}
				$commission -> rule_id = $rule -> rule_id;
				$commission -> rulemap_id = $rule_map;
				$commission -> rulemapdetail_id = $rule_map_detail;
				$commission -> module = $params['module'];
				$commission -> user_id = $user_id;
				$commission -> from_user_id = $params['user_id'];
				$commission -> purchase_currency = $params['currency'];
				$commission -> purchase_total_amount = $params['total_amount'];
				$commission -> commission_amount = $commission_amount;
				$commission -> commission_rate = $commission_rate;
				$commission -> commission_type = $commission_type;
				$commission -> commission_points = $commission_point;
				$commission -> creation_date = date('Y-m-d H:i:s');
				$commission -> save();
			}
			else
			{
				return;
			}

		}
		catch(Exception $e)
		{
			throw $e;
		}
	}

	public function getTotalPoints($approve_stat = null, $user_id = null, $params = array())
	{
		$select = $this -> select() -> from($this -> info('name'), 'SUM(commission_points) AS points');
		if($user_id)
		{
			$select -> where('user_id = ?', $user_id);
		}
		if($approve_stat)
		{
			$select -> where('approve_stat = ?', $approve_stat);
		}
		if(isset($params['notStatus']))
		{
			$select -> where('approve_stat <> ?', $params['notStatus']);
		}
		// rule id
		if(isset($params['ruleId']))
		{
			$select -> where('rule_id = ?', $params['ruleId']);
		}
		// not rule
		if(isset($params['notRule']))
		{
			$select -> where('rule_id <> ?', $params['notRule']);
		}
		return $select->query()->fetchColumn(0);
	}
	
	public function getAvailablePoints($user_id)
	{
		$total_points = $this -> getTotalPoints('approved', $user_id);
		$requestTable = Engine_Api::_()->getDbTable('requests', 'ynaffiliate');
		$requested_points = $requestTable -> getRequestedPoints($user_id);
		$current_request = $requestTable -> getCurrentRequestPoints($user_id);
		$available_points = round($total_points - $requested_points - $current_request, 2);
		return $available_points;
	}

	public function convertPoints($purchase_currency, $exchange_rate)
	{
		$select = $this -> select() -> where('purchase_currency = ?', $purchase_currency) -> where('commission_points = 0');
		$results = $this -> fetchAll($select);
		$point_convert_rate = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynaffiliate.pointrate', 1);
		if ($results)
		{
			foreach ($results as $result)
			{
				$new_commission_amount = $result -> commission_amount / $exchange_rate;
				$result -> commission_points = $point_convert_rate * $new_commission_amount;
				$result -> save();
			}
		}
	}

	public function countCommission($approve_stat = null, $user_id = null, $params = array())
	{
		$select = $this -> select() -> from($this -> info('name'), 'COUNT(*) AS count');
		if($user_id)
			$select -> where('user_id = ?', $user_id);
		// all commission
		if ($approve_stat) 
		{
			$select -> where('approve_stat = ?', $approve_stat);
		}
		if(isset($params['notStatus']))
		{
			$select -> where('approve_stat <> ?', $params['notStatus']);
		}
		// rule id
		if(isset($params['ruleId']))
		{
			$select -> where('rule_id = ?', $params['ruleId']);
		}
		// not rule
		if(isset($params['notRule']))
		{
			$select -> where('rule_id <> ?', $params['notRule']);
		}
		return $select->query()->fetchColumn(0);
	}
}
