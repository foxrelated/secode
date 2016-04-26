<?php
class Ynresponsivemetro_Widget_MetroMainMenuController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  	if(YNRESPONSIVE_ACTIVE != 'ynresponsive-metro')
	{
		return $this -> setNoRender(true);
	}	
    //Logo
    $this->view->logo = $this->_getParam('logo', false);
	$this->view->logo_link = $this->_getParam('logo_link', false);
	$this->view->site_name = $this->_getParam('site_name', false);
	$this->view->site_link = $this->_getParam('site_link', false);
	
  	$viewer = Engine_Api::_()->user()->getViewer();
	if(!$viewer -> getIdentity())
	{
		$this->view-> number_items = 5;
	}
	else 
	{
		$this->view-> number_items = $this->_getParam('number_menu_items', 7);
	}
	$this->view->fix_menu_position = $this->_getParam('fix_menu_position', 1);
	$this->view->menu_type = $this->_getParam('menu_type', 1);
	
    $this->view->navigationMain = $navigation = Engine_Api::_() -> getApi('menus', 'ynresponsive1')
      ->getNavigation('core_main');
    
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.browse', 1);
    
    if(!$require_check && !$viewer->getIdentity()){
      $navigation->removePage($navigation->findOneBy('route','user_general'));
    }
  }
  public function getCacheKey()
  {
  }
}