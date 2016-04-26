<?php

class Socialstore_Widget_ProfilePostedProductsController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		$this -> setScriptPath('application/modules/Socialstore/views/scripts/widgets/profile-main-product');
		
		$subject = Engine_Api::_()->core()->getSubject();
    	$viewer = Engine_Api::_()->user()->getViewer();
		
		if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
			return $this->setNoRender();
    	}
    	
    	$Model = Engine_Api::_()->getDbtable('SocialProducts', 'Socialstore');
    	$modelName = $Model->info('name');
		$select = $Model -> select()->from($modelName);
		$select -> where("owner_id  =?", $subject->getIdentity()) 
    			-> where('deleted=?', 0) 
    			-> where('approve_status=?', 'approved') 
    			-> where('view_status=?', 'show')
    			-> order("creation_date desc");
		$this -> view -> items = $items = $Model -> fetchAll($select);
		$this -> view -> totalItems = $totalItems = count($items);

		if(!$totalItems) {
			$this -> setNoRender();
		}

		$this -> view -> title = $this->_getParam('title','');
		$this -> view -> viewer_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
		$this->view->className = "layout_socialstore_profile_posted_products";		
	}

}
