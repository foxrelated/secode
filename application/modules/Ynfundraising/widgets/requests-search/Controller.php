<?php
class Ynfundraising_Widget_RequestsSearchController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		// Data preload
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$params = array ();

		// Get search form
		$this->view->form = $form = new Ynfundraising_Form_RequestSearch ();

		$request = Zend_Controller_Front::getInstance ()->getRequest ();

		$params = $request->getParams ();

		$form->populate ( $params );
	}
}