<?php

class Ynaffiliate_HelpController extends Core_Controller_Action_Standard {

	public function init() {
		if (!$this -> _helper -> requireUser() -> isValid()) {
			return;
		}
		$affiliate = new Ynaffiliate_Plugin_Menus;
		if (!$affiliate -> canView()) {
			$this -> _redirect('/affiliate/index');
		}
		// set active menu item on home page.
		Zend_Registry::set('active_menu', 'ynaffiliate_menu_helps');
	}

	public function indexAction() {
		$this -> _forward('detail');
	}

	public function detailAction() 
	{
		$this -> _helper -> content -> setEnabled();
		$id = $this -> _getParam('id', 0);
		$helpTable = Engine_Api::_() -> getDbTable('helpPages', 'ynaffiliate');
		$item = $helpTable -> find($id) -> current();

		if (!is_object($item)) 
		{
			$select = $helpTable -> select() -> where('status = ?', 'show') -> order('ordering asc') -> limit(1);
			$item = $helpTable -> fetchRow($select);
		}
		$this -> view -> item = $item;
		if ($item) 
		{
			Zend_Registry::set('ACTIVE_HELP_PAGE', $item -> getIdentity());
		}
	}
}
