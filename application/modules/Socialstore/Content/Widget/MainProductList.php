<?php
class Socialstore_Content_Widget_MainProductList extends Engine_Content_Widget_Abstract {

	/**
	 * @var int [0,20]
	 */
	protected $_limit = 5;

	public function init() {
		
		// set script path for all item
		$this -> setScriptPath('application/modules/Socialstore/views/scripts/widgets/main-product-list');

		$limit = (int)$this -> _getParam('max');
		$limit = $limit < 1 ? 5 : $limit;
		$limit = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('store.product.page', 10);
		
		// check some thing else
		$this -> _limit = $limit > 10 ? 5 : $limit;
		$this -> _limit = $limit = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('store.product.page', 10);
		$this->view->show_options = array(
			'creation'=>$this->_getParam('show_creation',0),
			'store'=>$this->_getParam('show_store',1),
			'author'=>$this->_getParam('show_author',1),
			'indexing'=>'creation',
		);
	}

}
