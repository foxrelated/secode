<?php

class Ynaffiliate_FaqsController extends Core_Controller_Action_Standard
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
		Zend_Registry::set('active_menu', 'ynaffiliate_menu_faqs');
	}

	public function indexAction()
	{
		$this -> _helper -> content -> setEnabled();
		$Table = Engine_Api::_() -> getDbTable('faqs', 'ynaffiliate');
		$select = $Table -> select() -> where('status = ?', 'show') -> order('ordering asc');
		$paginator = $this->view->paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        $paginator->setItemCountPerPage(10);
	}

}
