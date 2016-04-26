<?php
class Ynaffiliate_StatisticController extends Core_Controller_Action_Standard {
	protected $_periods = array(
        Zend_Date::DAY, //dd
        Zend_Date::WEEK, //ww
        Zend_Date::MONTH, //MM
        Zend_Date::YEAR, //y
    );
    
    protected $_allPeriods = array(
        Zend_Date::SECOND,
        Zend_Date::MINUTE,
        Zend_Date::HOUR,
        Zend_Date::DAY,
        Zend_Date::WEEK,
        Zend_Date::MONTH,
        Zend_Date::YEAR,
    );
    
    protected $_periodMap = array(
        Zend_Date::DAY => array(
            Zend_Date::SECOND => 0,
            Zend_Date::MINUTE => 0,
            Zend_Date::HOUR => 0,
        ),
        Zend_Date::WEEK => array(
            Zend_Date::SECOND => 0,
            Zend_Date::MINUTE => 0,
            Zend_Date::HOUR => 0,
            Zend_Date::WEEKDAY_8601 => 1,
        ),
        Zend_Date::MONTH => array(
            Zend_Date::SECOND => 0,
            Zend_Date::MINUTE => 0,
            Zend_Date::HOUR => 0,
            Zend_Date::DAY => 1,
        ),
        Zend_Date::YEAR => array(
            Zend_Date::SECOND => 0,
            Zend_Date::MINUTE => 0,
            Zend_Date::HOUR => 0,
            Zend_Date::DAY => 1,
            Zend_Date::MONTH => 1,
        ),
    );
	public function init() {
		if (!$this -> _helper -> requireUser() -> isValid()) {
			return;
		}
		$affiliate = new Ynaffiliate_Plugin_Menus;
		if (!$affiliate -> canView()) {
			$this -> _redirect('/affiliate/index');

		}
	}

	public function indexAction() {
		$this -> _helper -> content -> setEnabled();
		$this -> view -> form = $form = new Ynaffiliate_Form_Statistic();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$user_id = $viewer -> getIdentity();
		
		$commssionTable = Engine_Api::_()->getDbTable('commissions', 'ynaffiliate');
		$requestTable = Engine_Api::_()->getDbTable('requests', 'ynaffiliate');
		
		
		$this -> view -> subscriptions = $commssionTable -> countCommission(null, $user_id, array('ruleId' => 1, 'notStatus' => 'denied'));
		$this -> view -> purchases = $commssionTable -> countCommission(null, $user_id, array('notRule' => 1, 'notStatus' => 'denied'));
		
		$this -> view -> commissionPoints = round($commssionTable -> getTotalPoints(null, $user_id, array('notStatus' => 'denied')), 2);
		$this -> view -> approvedCommissionPoints = round($commssionTable -> getTotalPoints('approved', $user_id), 2);
		$this -> view -> delayingCommissionPoints = round($commssionTable -> getTotalPoints('delaying', $user_id), 2);
		$this -> view -> waitingCommissionPoints = round($commssionTable -> getTotalPoints('waiting', $user_id), 2);
		$this -> view -> requestedPoints = round($requestTable -> getRequestedPoints($user_id), 2);
	}
	
	public function chartAction() 
	{
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $viewer = Engine_Api::_()->user()->getViewer();
        // Get params
        $start  = $this->_getParam('start');
        $offset = $this->_getParam('offset', 0);
        $status   = $this->_getParam('approve_stat', 'all');
		$group_by   = $this->_getParam('group_by', 'commission_rule');
		$chart_type   = $this->_getParam('chart_type', 'line');
        $chunk  = $this->_getParam('chunk');
        $period = $this->_getParam('period');
        $periodCount = $this->_getParam('periodCount', 1);
		$user_id = $this->_getParam('userID', 0);
        
        // Validate chunk/period
        if( !$chunk || !in_array($chunk, $this->_periods) ) {
          $chunk = Zend_Date::DAY;
        }
        if( !$period || !in_array($period, $this->_periods) ) {
          $period = Zend_Date::MONTH;
        }
        if( array_search($chunk, $this->_periods) >= array_search($period, $this->_periods) ) {
          return;
        }
    
        // Validate start
        if( $start && !is_numeric($start) ) {
          $start = strtotime($start);
        }
        if( !$start ) {
          $start = time();
        }
    
        // Fixes issues with month view
        Zend_Date::setOptions(array(
          'extend_month' => true,
        ));
    
        // Get timezone
        $timezone = Engine_Api::_()->getApi('settings', 'core')
            ->getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
          $timezone = $viewer->timezone;
        }
    
        // Make start fit to period?
        $startObject = new Zend_Date($start);
        $startObject->setTimezone($timezone);
        $partMaps = $this->_periodMap[$period];
        foreach( $partMaps as $partType => $partValue ) {
          $startObject->set($partValue, $partType);
        }
    
        // Do offset
        if( $offset != 0 ) {
          $startObject->add($offset, $period);
        }
        // Get end time
        $endObject = new Zend_Date($startObject->getTimestamp());
        $endObject->setTimezone($timezone);
        $endObject->add($periodCount, $period);
        $endObject->sub(1, Zend_Date::SECOND); // Subtract one second
        
        // Get data
        $commissionTbl = Engine_Api::_()->getDbtable('commissions', 'ynaffiliate');
        $select = $commissionTbl -> select();
        $select
              ->where('creation_date >= ?', gmdate('Y-m-d H:i:s', $startObject->getTimestamp()))
              ->where('creation_date < ?', gmdate('Y-m-d H:i:s', $endObject->getTimestamp()))
              ->order('creation_date ASC');
        if($user_id)
		{
			 $select  ->where('user_id = ?', $user_id);
		}    
        if($status != "all")
        {
            $select -> where('approve_stat = ?', $status);
        }
		else {
			$select -> where('approve_stat <> ?', 'denied');
		}
		
		$comissionData = array();
		$dataCommssion = array();
		$groupData = array();
		$dataLabels = array();
		
		if($group_by == 'commission_rule')
		{
			// get all comission rules
			$ruleTable = Engine_Api::_()->getDbTable('rules', 'ynaffiliate');
			$rules = $ruleTable -> getRuleEnabled();
			foreach($rules as $rule)
			{
				$selectTemp = clone $select;
				$selectTemp -> where('rule_id = ?', $rule -> rule_id);
				$commssions = $commissionTbl -> fetchAll($selectTemp);
				if(count($commssions))
				{
					$comissionData[$rule -> rule_id] = $commssions;
					$groupData[] = $rule -> rule_id;
					$dataLabels[$rule -> rule_id] = $this -> view -> translate($rule -> rule_title);
				}
			}
		}
		else 
		{
			// get all user network levels of this user
			$accounts = Engine_Api::_() -> getDbTable('assoc', 'ynaffiliate') -> getAllClients($user_id);
			$max_commission_level = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.max.commission.level', 5);
			for($i = 1; $i <= $max_commission_level; $i ++)
			{
				$selectTemp = clone $select;
				// get all users is client level $i
				if($accounts[$i])
				{
					$selectTemp -> where('from_user_id IN (?)', $accounts[$i]);
					$commssions = $commissionTbl -> fetchAll($selectTemp);
					if(count($commssions))
					{
						$comissionData[$i] = $commssions;
						$groupData[] = $i;
						$dataLabels[$i] = $this -> view -> translate("Level %s", $i);
					}
				}
			}
		}
		
		// If empty => set default
		if(!count($comissionData))
		{
			$comissionData[1] = array();
			$groupData[] = 1;
			$dataLabels[1] = '';
		}
		if($chart_type == 'line')
		{

	        // Now create data structure
	        $currentObject = clone $startObject;
	        $nextObject = clone $startObject;
			
			$dateFormat = 'M j';
			if($chunk == 'MM')
			{
				$dateFormat = 'M';
			}
			if($period == 'MM' && $chunk == 'dd')
			{
				$dateFormat = 'j';
			}
			$oldTz = date_default_timezone_get();
			date_default_timezone_set($timezone);
			$count = 1;
	        do {
	            $nextObject -> add(1, $chunk);
	            $currentObjectTimestamp = $currentObject -> getTimestamp();
	            $nextObjectTimestamp = $nextObject -> getTimestamp();
				$tick = date($dateFormat, $currentObjectTimestamp);
				if($period == 'y' && $chunk == 'ww')
				{
					$tick = $count ++;
				}
				foreach ($groupData as $groupId) 
				{
					// Get everything that matches
					$currentPeriodCount = 0;
		            foreach ($comissionData[$groupId] as $rawDatum) 
		            {
		                $rawDatumDate = strtotime($rawDatum -> creation_date);
		                if ($rawDatumDate >= $currentObjectTimestamp && $rawDatumDate < $nextObjectTimestamp) 
		                {
		                    $currentPeriodCount += $rawDatum -> commission_points;
		                }
		            }
					$dataCommssion[$groupId][$tick] = $currentPeriodCount;
				}
	            $currentObject -> add(1, $chunk);
	        } while( $currentObject->getTimestamp() < $endObject->getTimestamp() );
	    }
		else
		{
			$total = 0;
			foreach ($groupData as $groupId) 
			{
				// Get everything that matches
				$currentPeriodCount = 0;
	            foreach ($comissionData[$groupId] as $rawDatum) 
	            {
                    $currentPeriodCount += $rawDatum -> commission_points;
	            }
				$total += $currentPeriodCount;
				$dataCommssion[$groupId] = $currentPeriodCount;
			}
			if($total)
			{
				$maxValue = max($dataCommssion);
				$idMax = 0;
				$totalMin = 0;
				foreach ($groupData as $groupId) 
				{
					$percent = round($dataCommssion[$groupId]*100/$total);
					if($maxValue == $dataCommssion[$groupId])
					{
						$idMax = $groupId;
					}
					else 
					{
						$totalMin += $percent;
						$dataLabels[$groupId] = $dataLabels[$groupId].': '.$percent.'%';
					}
				}
				$dataLabels[$idMax] = $dataLabels[$idMax].': '. (100 - $totalMin) .'%';
			}
		}
        $title = date('F j, Y', $startObject -> getTimestamp()) . $this -> view -> translate(' to ') . date('F j, Y', $endObject -> getTimestamp());
        date_default_timezone_set($oldTz);
        echo Zend_Json::encode(array('json' => $dataCommssion, 'title' => $title, 'dataLabels' => $dataLabels));
        return true;
    }

}
