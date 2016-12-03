<?php

class Store_Widget_ProductProfileController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		if (Engine_Api::_()->core()->hasSubject('user')) {
			$subject = Engine_Api::_() -> core() -> getSubject();
		} else {
			return $this->setNoRender();
		}
		$params = array();
		$params['owner'] = true;
		$params['user_id'] = $subject->getIdentity();
	    $params['order'] = 'DESC';
		$params['quantity'] = true;
	    $table = Engine_Api::_()->getDbTable('products', 'store');
		$select = $table->getSelect($params);
		$products = $table->fetchAll($select);
		$viewer = Engine_Api::_()->user()->getViewer();
		if (($viewer->getIdentity() != $subject->getIdentity()) && (count($products) == 0)) {
			$this->setNoRender();
		}
	    $this->view->products = $products;
		$settings = Engine_Api::_()->getDbTable('settings', 'core');
    	$this->view->currency = $currency = $settings->getSetting('payment.currency', 'USD');
	}

}
