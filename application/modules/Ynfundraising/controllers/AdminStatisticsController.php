<?php
class Ynfundraising_AdminStatisticsController extends Core_Controller_Action_Admin
{
	/**
	 * init check exist Ynidea plugin enable
	 *
	 */
	public function init()
  	{
	}
	public function indexAction()
	{
		// Get navigation bar

		//$this->view->paginator = $paginator = Engine_Api::_ ()->yndonation ()->getCampaignPaginator ( array () );
		//$paginator->setCurrentPageNumber ( $this->_getParam ( 'page', 1 ) );
		//$this->view->messages = $this->_helper->flashMessenger->getMessages ();
		$this->view->form = $form = new Ynfundraising_Form_Admin_SearchStatistics ();
		$form->isValid ( $this->_getAllParams () );
		$params = $form->getValues ();
		if (empty ( $params ['orderby'] ))
			$params ['orderby'] = 'donation_date';
		if (empty ( $params ['direction'] ))
			$params ['direction'] = 'DESC';

		$this->view->formValues = $params;

		// Filter type of search

		// Get Campaign Paginator
		$paginator = Engine_Api::_ ()->ynfundraising ()->getDonationPaginator ( $params );
		 $items_per_page = Engine_Api::_ ()->getApi ( 'settings', 'core')->getSetting ( 'ynfundraising.page', 10 );
		$paginator->setItemCountPerPage ( $items_per_page );
		$this->view->paginator = $paginator;
	}

}