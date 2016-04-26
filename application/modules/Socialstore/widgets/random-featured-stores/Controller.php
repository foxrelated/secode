<?php

class Socialstore_Widget_RandomFeaturedStoresController extends Socialstore_Content_Widget_StoreList{
	public function indexAction(){
		parent::init();
		
		
		//$select =  $Model->select()->where('deleted=?',0)->where('approve_status=?','approved')->where('view_status=?','show')->limit($this->_limit)->order('approved_date desc');
		$db = Engine_Db_Table::getDefaultAdapter();
		/*$select = "SELECT * FROM `engine4_stores` WHERE store_id >= (
														SELECT FLOOR( MAX( store_id ) * RAND( ) )
														FROM engine4_stores
														)
												  AND deleted = 0 AND approve_status = 'approved'
												  AND view_status = 'show'
												  AND featured = 1
												ORDER BY store_id
												LIMIT 5";*/
		$Model  = new Socialstore_Model_DbTable_SocialStores;
		$select =  $Model->select() -> where('deleted=?', 0) -> where('featured=?', 1) -> where('approve_status=?', 'approved') -> where('view_status=?', 'show') -> limit($this->_limit) -> order('rand()');
		
		$items = $db -> fetchAll($select);
		$objects = array();
		foreach ($items as $item) {
			$objects[] = Engine_Api::_()->getItem('social_store', $item['store_id']);
		}
		$this -> view -> items = $objects;
		$this -> view -> totalItems = $totalItems = count($objects);

		if(!$totalItems) {
			$this -> setNoRender();
		}

		//$this -> view -> title = $this->_getParam('title','Featured Stores');
		$this -> view -> viewer_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();		
		
	}
}
