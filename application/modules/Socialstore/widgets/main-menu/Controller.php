<?php

class Socialstore_Widget_MainMenuController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  	if(Zend_Registry::isRegistered('active_menu')){
  		$active_menu =  Zend_Registry::get('active_menu');
  	}else{
  		$active_menu = null;
  	}
  	$cart = Socialstore_Api_Cart::getInstance();
	$count = $cart -> countAllQty();
	$this->view->count = $count;
	$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('socialstore_main',array(), $active_menu);
  }
}