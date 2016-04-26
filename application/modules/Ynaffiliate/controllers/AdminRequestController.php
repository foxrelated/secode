<?php

class Ynaffiliate_AdminRequestController extends Core_Controller_Action_Admin
{

	public function init()
	{
		Zend_Registry::set('admin_active_menu', 'ynaffiliate_admin_main_request');
	}

	protected function getBaseUrl()
	{
		$baseUrl = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('store.baseUrl', null);
		if (APPLICATION_ENV == 'development')
		{
			$request = Zend_Controller_Front::getInstance() -> getRequest();
			$baseUrl = sprintf('%s://%s', $request -> getScheme(), $request -> getHttpHost());
			Engine_Api::_() -> getApi('settings', 'core') -> setSetting('store.baseUrl', $baseUrl);
		}
		return $baseUrl;
	}

	public function indexAction()
	{
		$page = $this -> _getParam('page', 1);
		$this -> view -> form = $form = new Ynaffiliate_Form_Admin_Manage_Request();
		$values = array();
		if ($form -> isValid($this -> _getAllParams()))
		{
			$values = $form -> getValues();
		}
		$limit = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynaffiliate.page', 10);
		$values['limit'] = $limit;

		$paginator = $this -> view -> paginator = Engine_Api::_() -> ynaffiliate() -> getRequestsPaginator($values);
		$this -> view -> paginator -> setCurrentPageNumber($page);
		$this -> view -> formValues = $values;
	}

	public function acceptAction()
	{
		$this -> view -> form = $form = new Ynaffiliate_Form_Admin_Request_Accept;
		$gateway = 'paypal';
		$requestTable = Engine_Api::_() -> getDbTable("requests", 'ynaffiliate');
		$id = $this -> _getParam('id', 0);
		$this -> view -> request = $item = $requestTable -> find($id) -> current();
		$accountTable = Engine_Api::_() -> getDbTable('accounts', 'ynaffiliate');
		$info_account = $accountTable -> getPaymentAccount($item -> user_id);
		$this -> view -> account_email = $info_account -> paypal_email;
		$this -> view -> account_name = $info_account -> paypal_displayname;
		$this -> view -> account = $info_account;
		$this -> view -> currency = $currency = $item -> currency;

		$baseUrl = $this -> getBaseUrl();
		$router = $this -> getFrontController() -> getRouter();
		$returnUrl = $this -> view -> returnUrl = $baseUrl . $router -> assemble(array(
			'module' => 'ynaffiliate',
			'controller' => 'request',
			'action' => 'index'
		), 'admin_default', true);

		$cancelUrl = $this -> view -> cancelUrl = $baseUrl . $router -> assemble(array(
			'module' => 'ynaffiliate',
			'controller' => 'request',
			'action' => 'index'
		), 'admin_default', true);

		$notifyUrl = $this -> view -> notifyUrl = $baseUrl . $router -> assemble(array(
			'module' => 'ynaffiliate',
			'controller' => 'request-callback',
			'action' => 'notify',
			'id' => $item -> getIdentity(),
			'owner-id' => $item -> user_id,
		), 'default', true);

		$this -> view -> sandboxMode = $sandboxMode = Ynaffiliate_Api_Core::isSandboxMode();
		if ($sandboxMode)
		{
			$this -> view -> formUrl = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		}
		else
		{
			$this -> view -> formUrl = 'https://www.paypal.com/cgi-bin/webscr';
		}
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author  
	 */
	public function saveResponseMessageAction() 
	{
		$this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
		$requestTable = Engine_Api::_() -> getDbTable("requests", 'ynaffiliate');
		$id = $this -> _getParam('id', 0);
		$message = $this -> _getParam('message', '');
		$item = $requestTable -> find($id) -> current();
		if($item)
		{
			$item -> response_message = $message;
			$item -> request_status = 'pending';
			$item -> save();
		}
	}
	
	public function denyAction()
	{
		$this -> _helper -> layout -> setLayout('admin-simple');
		$this -> view -> form = $form = new Ynaffiliate_Form_Admin_Request_Deny;
		$req = $this -> getRequest();
		$requestTable = Engine_Api::_() -> getDbTable("requests", 'ynaffiliate');
		$id = $this -> _getParam('id', 0);
		$item = $requestTable -> find($id) -> current();

		if (!is_object($item))
		{
		}

		if ($req -> isGet())
		{
			return;
		}

		if ($req -> isPost() && $form -> isValid($req -> getPost()))
		{
			$data = $form -> getValues();
			$errors = false;
			if ($errors)
			{
				$form -> markAsError();
				return;
			}
			// process request.
			$item -> request_status = 'denied';
			$item -> setFromArray($data);
			$item -> response_date = date('Y-m-d H:i:s');
			$item -> save();

			//add notification
			$owner = $item->getOwner();
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			$notifyApi -> addNotification($owner, $item, $item, 'ynaffiliate_request_denied');
		}

		$this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
			'parentRefresh' => true,
			'format' => 'smoothbox',
			'messages' => array('Denied Successfully.')
		));
	}

}
