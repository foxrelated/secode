<?php
class Ynaffiliate_Model_Request extends Core_Model_Item_Abstract
{
	protected $_type = 'ynaffiliate_request';
	protected $_parent_type = 'user';

	public function getOwner() {
		return Engine_Api::_()->getItem('user', $this->user_id);
	}

	public function getHref() {
		$params = array(
			'route' => 'ynaffiliate_request',
			'reset' => true,
		);

		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance()->getRouter()
			->assemble($params, $route, $reset);
	}

	public function getTitle() {
		return '';
	}

	public function isWaitingToProcess(){
		return $this->request_status == 'waiting';
	}
	
	public function getAccount($gateway ='paypal'){
		return Socialstore_Api_Account::getAccount($this->owner_id, $this->store_id, $gateway);
	}
}