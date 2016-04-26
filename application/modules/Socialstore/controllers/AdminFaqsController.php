<?php

class Socialstore_AdminFaqsController extends Core_Controller_Action_Admin{
	public function init(){
		Zend_Registry::set('admin_active_menu', 'socialstore_admin_main_faqs');
	}
	
	
	public function indexAction(){
		$Model = new Socialstore_Model_DbTable_Faqs;
		
		
		$select = $Model->select();
		$select->order('ordering asc');
		
		$paginator = $this->view->paginator = Zend_Paginator::factory($select);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
	}
	
	public function editAction(){
		$form =  $this->view->form =  new Socialstore_Form_Faqs_Admin_Edit;
		
		$Model = new Socialstore_Model_DbTable_Faqs;
		
		$id = $this->_getParam('id',0);
		
		$item = $Model->find($id)->current();
		
		if(!is_object($item)){
			return $this->_redirect('/admin/socialstore/faqs/create');
		}
		
		$req = $this->getRequest();
		
		if($req->isGet()){
			
			$form->populate($item->toArray());
			return ;
		}
		
		if($req->isPost() && $form->isValid($req->getPost())){
			$item->setFromArray($form->getValues());
			$item->save();
			$this->_redirect('/admin/socialstore/faqs');
		}
	}
	
	public function deleteAction(){
		
		$this->_helper->layout->setLayout('admin-simple');
		$form =  $this->view->form =  new Socialstore_Form_Faqs_Admin_Delete;
		
		$Model = new Socialstore_Model_DbTable_Faqs;
		
		$id = $this->_getParam('id',0);
		
		$item = $Model->find($id)->current();
		
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
	
	public function createAction(){
		$form =  $this->view->form =  new Socialstore_Form_Faqs_Admin_Create;
		
		$Model = new Socialstore_Model_DbTable_Faqs;
		
		$req = $this->getRequest();
		
		if($req->isGet()){
			return ;
		}
		
		if($req->isPost() && $form->isValid($req->getPost())){
			$item = $Model->fetchNew();
			$item->setFromArray($form->getValues());
			$item->save();
			$this->_redirect('/admin/socialstore/faqs');
		}
	}
}
