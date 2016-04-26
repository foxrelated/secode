<?php

class Socialstore_Widget_ProductSlideshowController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		
		// set script path for all item
		$limit = (int)$this -> _getParam('max');
		$limit = $limit < 1 ? 5 : $limit;

		// check some thing else
		$limit > 20 ? 5 : $limit;

		$headScript = new Zend_View_Helper_HeadScript();
		$headScript -> appendFile('application/modules/Socialstore/externals/scripts/jquery-1.4.2.min.js');
		$headScript -> appendFile('application/modules/Socialstore/externals/scripts/jquery.divslideshow-1.2-min.js');
		
		$Model  = new Socialstore_Model_DbTable_Products;
		$select =  $Model->select()->where('deleted=?',0)->where('featured=?',1)->where('approve_status=?','approved')->where('view_status=?','show')->limit($limit)->order('rand()');

		$items =  $this->view->items = $Model->fetchAll($select);
		
		
		$this->view->show_options = array(
			'creation'=>$this->_getParam('show_creation',1),
			'store'=>$this->_getParam('show_store',1),
			'author'=>$this->_getParam('show_author',1),
			'indexing'=>'creation',
		);
		
	}

}
