<?php

class Socialstore_Widget_ProfileLikeProductsController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		$this -> setScriptPath('application/modules/Socialstore/views/scripts/widgets/profile-main-product');
		
		$subject = Engine_Api::_()->core()->getSubject();
    	$viewer = Engine_Api::_()->user()->getViewer();
		
		if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
			return $this->setNoRender();
    	}
    	
    	$Model = Engine_Api::_()->getDbtable('SocialProducts', 'Socialstore');
    	$modelName = $Model->info('name');
		$likeTable  = Engine_Api::_()->getDbtable('likes', 'core');
		$likeName = $likeTable->info('name');
		$select = $Model -> select()->from($modelName);
		$select -> joinLeft($likeName, "$likeName.resource_id = $modelName.product_id",'')
    			-> where("resource_type  LIKE 'social_product'") 
    			-> where("poster_id = ?", $subject->getIdentity())
    			-> where('deleted=?', 0) 
    			-> where('approve_status=?', 'approved') 
    			-> where('view_status=?', 'show');
		$this -> view -> items = $items = $Model -> fetchAll($select);
		$this -> view -> totalItems = $totalItems = count($items);
    	

		if(!$totalItems) {
			$this -> setNoRender();
		}

		$this -> view -> title = $this->_getParam('title','Liked Products');
		$this -> view -> viewer_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
		$this->view->className = "layout_socialstore_profile_like_products";	
	}

}
