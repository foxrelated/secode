<?php

class Socialstore_Widget_MostLikeStoresController extends Socialstore_Content_Widget_StoreList {
	public function indexAction() {
		
		// initialzie
		parent::init();

		$Model = Engine_Api::_()->getDbtable('SocialStores', 'Socialstore');
		$modelName = $Model->info('name');
		$likeTable  = Engine_Api::_()->getDbtable('likes', 'core');
		$likeName = $likeTable->info('name');
		$select = $Model -> select()->from($modelName);
		$select -> joinLeft($likeName, "resource_id = store_id",'')
    			-> where("resource_type  LIKE 'social_store'") 
    			-> where('deleted=?', 0) 
    			-> where('approve_status=?', 'approved') 
    			-> where('view_status=?', 'show')
    			-> group('resource_id')
    			-> order('Count(resource_id) desc')->limit($this->_limit);
		
		$this -> view -> items = $items = $Model -> fetchAll($select);
		$this -> view -> totalItems = $totalItems = count($items);

		if(!$totalItems) {
			$this -> setNoRender();
		}

		//$this -> view -> title = $this->_getParam('title','Most Liked Stores');
		$this -> view -> viewer_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();		
	}
}
