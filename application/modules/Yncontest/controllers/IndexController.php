<?php

class Yncontest_IndexController extends Core_Controller_Action_Standard
{

	public function indexAction()
	{  
		// Render
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function listingAction()
	{
		$values = $this -> _getAllParams();
		Zend_Registry::set('contest_search_params', $values);
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}
	public function friendAction()
	{ 
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$values = $this -> _getAllParams();
		Zend_Registry::set('contest_search_params', $values);
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}
	
	public function displayPromoteAction(){
		$contest_id = $this->_getParam('contestId', null);
		$this->view->contest = Engine_Api::_() -> getItem('yncontest_contest', $contest_id);
	}
	public function promoteAction(){
		$headScript = new Zend_View_Helper_HeadScript(); 
		$headScript -> appendFile('application/modules/Yncontest/externals/scripts/MooClip.js');
		
		$contest_id = $this->_getParam('contestId', null);
		//require owner
		$this->view->contest = Engine_Api::_() -> getItem('yncontest_contest', $contest_id);
	}
	public function promoteEntryAction(){
		$headScript = new Zend_View_Helper_HeadScript(); 
		$headScript -> appendFile('application/modules/Yncontest/externals/scripts/MooClip.js');
		$contest_id = $this->_getParam('Id', null);
		//require owner
		$this->view->contest = Engine_Api::_() -> getItem('yncontest_entry', $contest_id);		
	}
	public function displayPromoteEntryAction(){
		$contest_id = $this->_getParam('Id', null);
		$this->view->contest = Engine_Api::_() -> getItem('yncontest_entry', $contest_id);
	}

	public function entriesAction()
	{
		$values = $this -> _getAllParams();
		Zend_Registry::set('entries_search_params', $values);
		// Render
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function listingCompareAction()
	{
		// Render
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function compareEntriesAction()
	{
		// Render
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function viewAllAction()
	{
		// Render
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	
	public function faventriesAction()
	{
		// Render
		if( !$this->_helper->requireUser()->isValid() ) return;
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function myEntriesAction()
	{
		
		// Render
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

}
