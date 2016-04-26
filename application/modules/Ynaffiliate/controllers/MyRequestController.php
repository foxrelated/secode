<?php

class Ynaffiliate_MyRequestController extends Core_Controller_Action_Standard {

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
		$this -> view -> headScript() -> appendFile($this -> view -> layout() -> staticbaseUrl . 'application/modules/Ynaffiliate/externals/scripts/ynaffiliate_date.js');
		$this -> view -> headScript() -> appendFile($this -> view -> layout() -> staticbaseUrl . 'application/modules/Ynaffiliate/externals/scripts/datepicker.js');
		$this -> view -> headLink() -> appendStylesheet($this -> view -> layout() -> staticbaseUrl . 'application/modules/Ynaffiliate/externals/styles/datepicker_jqui/datepicker_jqui.css');
		$this -> view -> form = $form = new Ynaffiliate_Form_MyRequest();
		$values = array();
		if ($form -> isValid($this -> _getAllParams())) {
			$values = $form -> getValues();
		}
		$limit = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynaffiliate.page', 10);
		$values['limit'] = $limit;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$values['user_id'] = $viewer -> getIdentity();
		$paginator = $this -> view -> paginator = Engine_Api::_() -> ynaffiliate() -> getRequestsPaginator($values);
		$this -> view -> paginator -> setCurrentPageNumber($page);
		$this -> view -> formValues = $values;
		
		$user_id = $viewer -> getIdentity();
		$accountTable = Engine_Api::_() -> getDbTable('accounts', 'ynaffiliate');
		$info_account = $accountTable -> getPaymentAccount($user_id);
		$this -> view -> account_email = $info_account -> paypal_email;
		$this -> view -> account_name = $info_account -> paypal_displayname;
		$this -> view -> selected_currency = $info_account -> selected_currency;

		$exchange_rate = Engine_Api::_()->getDbTable('exchangerates', 'ynaffiliate')->getExchangerateById($info_account->selected_currency)->exchange_rate;
		if(!$exchange_rate)
		{
			$exchange_rate = 1;
		}
		$this -> view ->exchange_rate = $exchange_rate;
		$level = Engine_Api::_() -> getItem('authorization_level', $viewer -> level_id);
		if (in_array($level -> type, array('admin')))
		{
			$isAdmin = 1;
		}
		else
		{
			$isAdmin = 0;
		}
		$this -> view -> minRequest = $minRequest = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynaffiliate.minrequest', 10);
		$this -> view -> maxRequest = $maxRequest = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynaffiliate.maxrequest', 10);
		$commissionTable = Engine_Api::_() -> getDbTable("commissions", 'ynaffiliate');
		$requestTable = Engine_Api::_() -> getDbTable("requests", 'ynaffiliate');
		
		$this -> view -> totalPoints = $totalPoints = round($commissionTable -> getTotalPoints(null, $user_id, array('notStatus' => 'denied')), 2);
		$this -> view -> delayingCommissionPoints = round($commissionTable -> getTotalPoints('delaying', $user_id), 2);
		
		$requested_points = round($requestTable -> getRequestedPoints($user_id), 2);
		$current_request = round($requestTable -> getCurrentRequestPoints($user_id), 2);
		$approvedCommissionPoints = round($commissionTable -> getTotalPoints('approved', $user_id), 2);
		$availablePoints = $approvedCommissionPoints - $requested_points - $current_request;
		if($availablePoints < 0)
		{
			$availablePoints = 0;
		}
		$this -> view -> requestedPoints = $requested_points;
		$this -> view -> availablePoints = round($availablePoints, 2);
		$this -> view -> currentRequestPoints = $current_request;
		$this -> view -> currency = Engine_Api::_() -> getApi('settings', 'core') -> payment['currency'];
	}

}
