<?php 

class Socialstore_Widget_MenuMystoreMiniController extends Engine_Content_Widget_Abstract{
	public function indexAction(){
		
		$mystore_id =  NULL;
		
		if(Zend_Registry::isRegistered('MYSTORE_ID')){
			$mystore_id = Zend_Registry::get('MYSTORE_ID');	
		}	
		
		if(!$mystore_id){
			$this->setNoRender(true);
		}
		
		$store =  $this->view->store =  Engine_Api::_()->getItem('social_store',$mystore_id);
		
		$store_id = $this->view->store_id = $mystore_id;
		
		if(!is_object($store)){
			$this->setNoRender(true);
		}
		
		
		if(Zend_Registry::isRegistered('STOREMINIMENU_ACTIVE')){
			$active_menu = Zend_Registry::get('STOREMINIMENU_ACTIVE');
		}		
		
		$this->view->active_menu =  @$active_menu;
	}
}
