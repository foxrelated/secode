<?php

class Ynaffiliate_MyAccountController extends Core_Controller_Action_Standard
{
	public function init()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		$affiliate = new Ynaffiliate_Plugin_Menus;
		if (!$affiliate -> canView())
		{
			$this -> _redirect('/affiliate/index');
		}
	}

	public function editAction()
	{
		$this -> _helper -> content -> setEnabled();
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$user_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
		$Accounts = new Ynaffiliate_Model_DbTable_Accounts;
		$info_account = $Accounts -> getPaymentAccount($user_id);
		$this -> view -> form = $form = new Ynaffiliate_Form_MyAccount_Edit();

		$form -> populate($info_account -> toArray());
		$req = $this -> getRequest();
		if ($req -> isPost() && $form -> isValid($req -> getPost()))
		{
			$data = $form -> getValues();

			if (is_object($info_account))
			{
				$info_account -> paypal_displayname = $data['paypal_displayname'];
				$info_account -> paypal_email = $data['paypal_email'];
				$info_account -> selected_currency = $data['selected_currency'];
			}
			$info_account -> save();
			$form -> addNotice('Save Changed.');
		}
		$this -> renderScript('my-request/edit.tpl');
	}

	public function thresholdAction()
	{
		$user_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
		$this -> _helper -> layout -> setLayout('default-simple');
		$this -> view -> form = $form = new Ynaffiliate_Form_MyAccount_Request();
		$commissionTable = Engine_Api::_() -> getDbTable("commissions", 'ynaffiliate');
		$requestTable = Engine_Api::_() -> getDbTable("requests", 'ynaffiliate');

		$available_points = $commissionTable -> getAvailablePoints($user_id);
		$form -> setDescription($this -> view -> translate("Your available point is %s. Please enter the point you want to exchange to real money", $available_points));
		$form -> setAction($this -> getFrontController() -> getRouter() -> assemble(array()));

		$settingCore = Engine_Api::_() -> getApi('settings', 'core');
		$minPointsRequest = $settingCore -> getSetting('ynaffiliate.minrequest', 10);
		$maxPointsRequest = $settingCore -> getSetting('ynaffiliate.maxrequest', 100);

		if ($available_points < $minPointsRequest)
		{
			$form -> removeElement('submit');
			$form -> addError(sprintf("Your available amount is not enough to make request. It has to be large than %s", $minPointsRequest));
		}

		$max = ($maxPointsRequest <= $available_points) ? $maxPointsRequest : $available_points;
		if ($max == 0)
		{
			$max = $maxPointsRequest;
		}
		$form -> request_points -> addValidator('Between', false, array(
			$minPointsRequest,
			$max
		));
		$view = Zend_Registry::get('Zend_View');
		$form -> request_points -> setDescription($this->view->translate('Your request has to be between %s and %s', $minPointsRequest, $max));
		$form -> request_points -> getDecorator('description') -> setOption("placement", "append");


		$user_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
		$Accounts = new Ynaffiliate_Model_DbTable_Accounts;
		$info_account = $Accounts -> getPaymentAccount($user_id);

		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost()))
		{
			$data = $form -> getValues();
			$request_points = $data['request_points'];

			$min = $minPointsRequest;

			if ($available_points < $minPointsRequest)
			{
				$form -> addError(sprintf("Your available amount is not enough to make request. It has to be large than %s", $minPointsRequest));
			}
			if ($request_points < $min)
			{
				$form -> addError('Your request amount has to be at least ' . $min . ' and less than ' . $max);
			}
			if ($request_points > $max)
			{
				$form -> addError('Your request amount has to be at least ' . $min . ' and less than ' . $max);
			}
			$item = $requestTable -> fetchNew();
			$item -> setFromArray($data);
			$item -> user_id = $user_id;
			$item -> currency = $info_account -> selected_currency;
			$point_convert_rate = $settingCore -> getSetting('ynaffiliate.pointrate', 1);
			$exchange_rate = Engine_Api::_()->getDbTable('exchangerates', 'ynaffiliate')->getExchangerateById($info_account->selected_currency)->exchange_rate;
			if(!$exchange_rate)
			{
				$exchange_rate = 1;
			}
			$item -> request_amount = round(($request_points * $exchange_rate)/$point_convert_rate, 2);
			$item -> request_date = date('Y-m-d H:i:s');
			$item -> save();
			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => 10,
				'parentRefresh' => 10,
				'messages' => array($this -> view -> translate("Request money successfully!"))
			));
		}
		$this -> renderScript('my-request/form.tpl');
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author
	 */
	public function cancelRequestAction()
	{
		$requestTable = Engine_Api::_() -> getDbTable("requests", 'ynaffiliate');
		$id = $this -> _getParam('requestId', 0);
		$item = $requestTable -> find($id) -> current();
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');
		$this -> view -> form = $form = new Ynaffiliate_Form_MyAccount_Cancel();
		$this -> renderScript('my-request/form.tpl');
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if ($item)
		{
			$item -> delete();
			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => 10,
				'parentRefresh' => 10,
				'messages' => array($this -> view -> translate("Cancel request successfully!"))
			));
		}
	}

}
