<?php

class Socialstore_MyFollowStoreController extends Core_Controller_Action_Standard{
	public function init(){
		// private page

		Zend_Registry::set('active_menu','socialstore_main_myfollowstore');
	}
	
	public function indexAction(){
		
		if(!$this -> _helper -> requireUser() -> isValid()){
			return ;
		}
		$this->_helper->content
         ->setNoRender()
           ->setEnabled()
            ;    
	}
	
	public function followAction(){
		$viewer = Engine_Api::_()->user()->getViewer();
		$store_id = $this->_getParam('store_id');
		if ($store_id != '') {
			Zend_Registry::set('tempStoreID', $store_id);
		}
		if ($viewer->getIdentity() == 0) {
			$this->view->signin = 0;
		}
		$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		$store = Engine_Api::_()->getItem('social_store', Zend_Registry::get('tempStoreID'));
		if ($store->isFollowed($user_id)) { Socialstore_Api_Following::getInstance()->deleteFollower($user_id, $store_id);
			$this->view->follow = 0;
			$this->view->text = Zend_Registry::get('Zend_Translate')->_('Follow');
		}
		else { Socialstore_Api_Following::getInstance()->addFollower($user_id, $store_id);
			$this->view->follow = 1;
			$this->view->text = Zend_Registry::get('Zend_Translate')->_('Unfollow');
		}
	}
	

	
}
