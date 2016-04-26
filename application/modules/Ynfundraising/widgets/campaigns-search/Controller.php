<?php
class Ynfundraising_Widget_CampaignsSearchController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		// Data preload
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$params = array ();

		// Get search form
		$this->view->form = $form = new Ynfundraising_Form_CampaignSearch ();

		// Get search action type
		if (Engine_Api::_ ()->core ()->hasSubject ( 'user' )) {
			$form->removeElement ( 'status' );
			$form->removeElement ( 'type' );
		}
		else {
			//$form->setAction($this->view->url(array(),'default')."fundraising/list");
			$form->removeElement ( 'show' );
		}

		$request = Zend_Controller_Front::getInstance()->getRequest();
		$module = $request->getParam('module');
		$controller = $request->getParam('controller');
		$action = $request->getParam('action');
		$forwardListing = true;
		if ($module == 'ynfundraising') {
			if ( ($controller == 'index' && $action == 'past-campaigns')
					|| ($controller == 'campaign' && $action == 'index')
				) {
				$forwardListing = false;
				if ($controller == 'index' && $action == 'past-campaigns') {
					unset($form->status->options['ongoing']);
				}
			}
		}
		if ($forwardListing === true) {
			$form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'list'), 'ynfundraising_general', true));
		}

		$request = Zend_Controller_Front::getInstance ()->getRequest ();

		$params = $request->getParams ();

		$form->populate ( $params );
	}
}