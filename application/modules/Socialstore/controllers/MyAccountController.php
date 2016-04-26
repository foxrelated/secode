<?php
class Socialstore_MyAccountController extends Core_Controller_Action_Standard {

	protected $_mystore = null;

	public function getMyStore() {
		return $this -> _mystore;
	}

	public function setMyStore($store) {
		$this -> _mystore = $store;
	}
	
	public function init() {
		// private page
		if(!$this -> _helper -> requireUser() -> isValid()) {
			return ;
		}

		$viewer = Engine_Api::_() -> user() -> getViewer();

		$store = Engine_Api::_() -> getDbTable('SocialStores', 'Socialstore') -> getStoreByOwnerId($viewer -> getIdentity());
		if (!is_object($store)) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'index'), 'socialstore_mystore_general', true);
		}
		$this -> setMyStore($store);

		Zend_Registry::set('active_menu', 'socialstore_main_mystore');

		Zend_Registry::set('MYSTORE_ID', $store -> getIdentity());

		Zend_Registry::set('STOREMINIMENU_ACTIVE', 'my-account');
		
		
	}
	
	
	

	public function indexAction() {
		// recent request
		$store =  $this->getMyStore();
		$store_id = $store->store_id;
		$this->view->store =  $store;
		Zend_Registry::set('STOREMINIMENU_ACTIVE','my-account');
		$table = new Socialstore_Model_DbTable_Requests;
		$select = $table->select()->where('store_id=?', $store->getIdentity())->order('request_date desc');
		$paginator =  $this->view->recentRequests = Zend_Paginator::factory($select);
		
		$this->view->currency = $currency = Socialstore_Api_Core::getDefaultCurrency();
		$paginator->setCurrentPageNumber(1);
	}
	
	public function sendRequestAction(){
		// recent request
		$this->view->store = $store =  $this->getMyStore();
		Zend_Registry::set('STOREMINIMENU_ACTIVE','my-account');
		$table = new Socialstore_Model_DbTable_Requests;
		
		$this->view->form = $form = new Socialstore_Form_Payment_Seller_Request;
		
		$form->request_amount->addValidator('Between',false,array($store->getMinRequestAmount(), $store->getMaxRequestAmount()));
		
		$view =  Zend_Registry::get('Zend_View');
		$form->request_amount->setDescription(sprintf("Between from %s to %s",$view->currency($store->getMinRequestAmount()) , $view->currency($store->getMaxRequestAmount())));
		$form->request_amount->getDecorator('description')->setOption("placement", "append");
		
		if (!$store->ownerCanRequest()) {
			$form->removeElement('submit');
			$form->removeElement('cancel');
			return $form->addError('You do not have enough money to request!');
		}
		
		$req = $this->getRequest();
		
		if($req->isGet()){
			
		}
		
		if($req->isPost() && $form->isValid($req->getPost())){
			
			$errors = false;
			
			// validate this request.
			
			if($errors){
				$form->markAsError();
				return ;
			}
			
			// push request to here.
			$data = $form->getValues();
			$request_amount = $data['request_amount'];
			$min = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.minrequest', 100.00);
			$max = $store->getMaxRequestAmount();
			if ($request_amount < $min || $request_amount > $max) {
				return $form->addError('Your request amount has to be at least '.$min.' and less than '.$max);
			}
			$item = $table->fetchNew();
			$item->setFromArray($data);
			$item->owner_id =  $store->owner_id;
			$item->store_id =  $store->getIdentity();
			$item->request_date = date('Y-m-d H:i:s');
			$item->save();
			
			$this->_helper->redirector->gotoSimple('index');
			// forward.
		}
		
	}
	
	public function requestsAction(){
		
	}

	public function configureAction() {
		Zend_Registry::set('STOREMINIMENU_ACTIVE','my-account');
		$gateway = 'paypal';
		$form_class = 'Socialstore_Form_Payment_Seller_Configure_' . Engine_Api::inflect('paypal');
		
		$form = $this -> view -> form = new $form_class;
		
		$store = $this->getMyStore();
		
		$account =  Socialstore_Api_Account::getAccount($store->owner_id, $store->store_id,$gateway);
		
		
		$req = $this->getRequest();
		
		if($req->isGet()){
			if(is_object($account) && $account->config){
				$form->populate($account->toArray());
				$config =  Zend_Json::decode($account->config);
				$form->populate($config);	
			}
			return ;
		}
		
		if($req->isPost() && $form->isValid($req->getPost())){
			$data =  $form->getValues();
			
			if(!is_object($account)){
				$model =  new Socialstore_Model_DbTable_Accounts;
				$account =  $model->fetchNew();
				$account->owner_id =  $store->owner_id;
				$account->store_id  = $store->store_id;
				$account->gateway_id   = $gateway;
			}
			
			$account->setFromArray($data);
			$config =  Zend_Json::encode($data);
			$account->config =  $config;
			$account->save();
			
			$form->addNotice('Save Changed.');
		}
	}

	public function messageDetailAction() {

	}

	public function requestMoneyAction() {

	}

	public function requestHistoryAction() {

	}

}
