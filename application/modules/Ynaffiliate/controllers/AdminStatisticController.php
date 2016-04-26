<?php

class Ynaffiliate_AdminStatisticController extends Core_Controller_Action_Admin{
	
	public function init() {
		Zend_Registry::set('admin_active_menu', 'ynaffiliate_admin_main_statistics');
	}
	public function indexAction()
	{
		$this -> view -> form = $form = new Ynaffiliate_Form_Statistic();
		$form -> removeElement('group_by');
		
		$commssionTable = Engine_Api::_()->getDbTable('commissions', 'ynaffiliate');
		$accountTable = Engine_Api::_()->getDbTable('accounts', 'ynaffiliate');
		$assocTable = Engine_Api::_()->getDbTable('assoc', 'ynaffiliate');
		$requestTable = Engine_Api::_()->getDbTable('requests', 'ynaffiliate');
		$ruleTable = Engine_Api::_()->getDbTable('rules', 'ynaffiliate');
		
		$this->view->totalAffiliates = floor($accountTable->countAffiliate());
		$this->view->totalClients = floor($assocTable->countClient());
		$this -> view -> rules = $ruleTable -> getRuleEnabled();
		
		$this->view->totalCommissions = floor($commssionTable -> getTotalPoints(null, null, array('notStatus' => 'denied')));
		$this->view->totalRequested = floor($requestTable->getRequestedPoints());
	}
}
