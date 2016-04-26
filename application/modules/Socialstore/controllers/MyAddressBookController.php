<?php

class Socialstore_MyAddressBookController extends Core_Controller_Action_Standard{
	public function init(){
		// private page
		if(!$this -> _helper -> requireUser() -> isValid()){
			return ;
		}
		Zend_Registry::set('active_menu','socialstore_main_myaddressbook');
	}
	public function indexAction(){
		$viewer = Engine_Api::_()->user()->getViewer();
		$AddressBook = new Socialstore_Model_DbTable_Addressbooks;
		$addressBook = $AddressBook->getAddressBook($viewer->getIdentity());
		$this->view->addressBook = $addressBook;
		$this->view->form = $form = new Socialstore_Form_Addressbook_Create();
		
		$post = $this -> getRequest() -> getPost();
		$req = $this->getRequest();
		if($req -> isGet()) {
			return;
		}
		if(!$form -> isValid($post)) {
			$this->view->isPosted = 1;
			return ;
		}

		$data = $form -> getValues();
		$data_encode = Zend_Json::encode($data);
		$new_addressbook = $AddressBook->createRow();
		$new_addressbook->user_id = $viewer->getIdentity();
		$new_addressbook->value = $data_encode;
		$new_addressbook->save();
		$this -> _helper -> redirector -> gotoRoute(array('action' => 'index'));
	}
	
	public function editAction() {
		$this -> _helper -> layout -> setLayout('default-simple');

		$req =  $this->getRequest();
		
		$addressbook_id =  $this->_getParam('addressbook_id',0);
		
		$table = new Socialstore_Model_DbTable_Addressbooks;
		$form = $this -> view -> form = new Socialstore_Form_Addressbook_Edit();
		$item = $table->find($addressbook_id)->current();
		$data = Zend_Json::decode($item->value,true);
		$form->populate($data);
		if($req-> isPost() && $form -> isValid($req-> getPost())) {
			$data = $form->getValues();
			$data_encode = Zend_Json::encode($data);
			$item->value = $data_encode;
			$item->save();
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}
	}
	
	public function deleteAction() {
		$this -> _helper -> layout -> setLayout('admin-simple');
		$addressbook_id = $this->_getParam('addressbook_id',0);
		$form =  $this->view->form =  new Socialstore_Form_Addressbook_Delete;
		$Model = new Socialstore_Model_DbTable_Addressbooks;
		
		$item = $Model->find($addressbook_id)->current();
		
		if(!is_object($item)){
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array('Deleted Before.')));
		}
		
		$req = $this->getRequest();
		
		if($req->isGet()){
			$form->populate($item->toArray());
			return ;
		}
		
		if($req->isPost() && $form->isValid($req->getPost())){
			$item->delete();
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array(Zend_Registry::get('Zend_Translate')->_('Deleted Successfully.'))));
		}
	}
	
}
