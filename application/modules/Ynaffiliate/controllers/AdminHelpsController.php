<?php

class Ynaffiliate_AdminHelpsController extends Core_Controller_Action_Admin{
	
	public function init(){
		Zend_Registry::set('admin_active_menu', 'ynaffiliate_admin_main_helps');
     $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
              ->getNavigation('ynaffiliate_admin_main', array(), 'ynaffiliate_admin_main_helps');
	}
	
	public function indexAction(){
		$Model = new Ynaffiliate_Model_DbTable_HelpPages;
		
		
		$select = $Model->select();
		$select->order('ordering asc');
		
		$paginator = $this->view->paginator = Zend_Paginator::factory($select);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
	}
	
	public function editAction(){
		$form =  $this->view->form =  new Ynaffiliate_Form_HelpPage_Admin_Edit;
		
		$Model = new Ynaffiliate_Model_DbTable_HelpPages;
		
		$id = $this->_getParam('id',0);
		
		$item = $Model->find($id)->current();
		
		if(!is_object($item)){
			return $this->_redirect('/admin/ynaffiliate/helps/create');
		}
		
		$req = $this->getRequest();
		
		if($req->isGet()){
			
			$form->populate($item->toArray());
			return ;
		}
		
		if($req->isPost() && $form->isValid($req->getPost())){
			$item->setFromArray($form->getValues());
			$item->save();
			$this->_redirect('/admin/ynaffiliate/helps');
		}
	}
	
	public function deleteAction(){
		
		$this->_helper->layout->setLayout('admin-simple');
		$form =  $this->view->form =  new Ynaffiliate_Form_HelpPage_Admin_Delete;
		
		$Model = new Ynaffiliate_Model_DbTable_HelpPages;
		
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
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'format' => 'smoothbox', 'messages' => array('Deleted Successfully.')));
		}
	}
	
	public function createAction(){
		$form =  $this->view->form =  new Ynaffiliate_Form_HelpPage_Admin_Create;
		
		$Model = new Ynaffiliate_Model_DbTable_HelpPages;
		
		$req = $this->getRequest();
		
		if($req->isGet()){
			return ;
		}
		
		if($req->isPost() && $form->isValid($req->getPost())){
			$item = $Model->fetchNew();
			$item->setFromArray($form->getValues());
			$item->save();
			$this->_redirect('/admin/ynaffiliate/helps');
		}
	}
}
