<?php

class Socialstore_AdminRequestController extends Core_Controller_Action_Admin {

	public function init() {
		Zend_Registry::set('admin_active_menu', 'socialstore_admin_main_request');
	}

	protected function getBaseUrl() {
		$baseUrl = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('store.baseUrl', null);
		if(APPLICATION_ENV == 'development') {
			$request = Zend_Controller_Front::getInstance() -> getRequest();
			$baseUrl = sprintf('%s://%s', $request -> getScheme(), $request -> getHttpHost());
			Engine_Api::_() -> getApi('settings', 'core') -> setSetting('store.baseUrl', $baseUrl);
		}
		return $baseUrl;
	}

	public function indexAction() {
		$table = new Socialstore_Model_DbTable_Requests;
		$select = $table -> select() -> setIntegrityCheck(false) -> from(array('req' => 'engine4_socialstore_requests')) -> join(array('u' => 'engine4_users'), 'u.user_id=req.owner_id') -> order('req.request_date desc');

		$paginator = $this -> view -> paginator = Zend_Paginator::factory($select);
		$page = $this -> _getParam('page', 1);

		$paginator -> setCurrentPageNumber($page);
	}
	
	public function acceptAction(){
		$this->view->form  =  $form =  new Socialstore_Form_Payment_Admin_Request_Accept;
		$table = new Socialstore_Model_DbTable_Requests;
		$id = $this -> _getParam('id', 0);
		$this->view->request = $item = $table -> find($id) -> current();
		$gateway = 'paypal';
		$this->view->responseMessage = $item->response_message;
		$this->view->account = $account = $item->getAccount();
		$this->view->currency = $currency = Socialstore_Api_Core::getDefaultCurrency();
		$amount =  $item->request_amount;
		$baseUrl = $this->getBaseUrl();
		$router =  $this->getFrontController()->getRouter();
		$returnUrl = $this->view->returnUrl = $baseUrl . $router->assemble(array(
		'module'=>'socialstore',
		'controller'=>'request',
		'action'=>'index',
		'id'=>$item->getIdentity(),
		'owner-id'=>$item->owner_id,
		'store-id'=>$item->store_id,
		),'admin_default',true);
		
		$cancelUrl= $this->view->cancelUrl = $baseUrl . $router->assemble(array(
		'module'=>'socialstore',
		'controller'=>'request',
		'action'=>'index',
		'id'=>$item->getIdentity(),
		'owner-id'=>$item->owner_id,
		'store-id'=>$item->store_id,
		),'admin_default',true);
		
		$notifyUrl= $this->view->notifyUrl = $baseUrl . $router->assemble(array(
		'module'=>'socialstore',
		'controller'=>'request-callback',
		'action'=>'notify',
		'id'=>$item->getIdentity(),
		'owner-id'=>$item->owner_id,
		'store-id'=>$item->store_id
		),'default',true); 
		
		$this->view->sandboxMode = $sandboxMode = Socialstore_Api_Core::isSandboxMode();
		
		if($sandboxMode){
			$this->view->formUrl = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		}else{
			$this->view->formUrl = 'https://www.paypal.com/cgi-bin/webscr';
		}
	}

	public function acceptMasspayAction() {
		$gateway =  'paypal';
		$payment = Socialstore_Payment::factory($gateway);
		$request =  new Socialstore_Payment_Request('MassPay');
		$this->view->form  =  $form =  new Socialstore_Form_Payment_Admin_Request_Accept;
		
		$table = new Socialstore_Model_DbTable_Requests;
		$id = $this -> _getParam('id', 0);
		$item = $table -> find($id) -> current();
		
		if(!is_object($item)){
			// item not found
		}
		
		if(!$item->isWaitingToProcess()){
			// this item is processed before	
		}
		
		$account = $item->getAccount();
		
		$options = array(
			'currency'=>Socialstore_Api_Core::getDefaultCurrency(),
			'pay_items'=>array(
				array('email'=>$account->account_username,'amount'=>$item->request_amount),
			),
		);
		
		
		$request->setOptions($options);
		$payment->process($request);	
	}

	public function denyAction() {
		$this -> _helper -> layout -> setLayout('admin-simple');
		$this -> view -> form = $form = new Socialstore_Form_Admin_Payment_Request_Deny;

		$req = $this -> getRequest();

		$table = new Socialstore_Model_DbTable_Requests;
		$id = $this -> _getParam('id', 0);
		$item = $table -> find($id) -> current();

		if(!is_object($item)) {

		}

		if($req -> isGet()) {
			return ;
		}

		if($req -> isPost() && $form -> isValid($req -> getPost())) {
			$data = $form -> getValues();

			$errors = false;

			if($errors) {
				$form -> markAsError();
				return ;
			}
			// process request.
			$item -> request_status = 'denied';
			$item -> setFromArray($data);
			$item -> response_date = date('Y-m-d H:i:s');
			$item -> save();
			
			$sendTo = Engine_Api::_()->getItem('user', $item->owner_id);
			$params = $item->toArray();
      		Engine_Api::_()->getApi('mail','Socialstore')->send($sendTo, 'store_requestdeny',$params);
			
			// Send Email Deny to Request
		}

		$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array('Denied Successfully.')));
	}

}
