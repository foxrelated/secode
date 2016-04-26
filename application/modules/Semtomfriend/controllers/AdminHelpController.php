<?php

class Semtomfriend_AdminHelpController extends Core_Controller_Action_Admin
{


  public function indexAction()
  {

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('semtomfriend_admin_main', array(), 'semtomfriend_admin_main_help');

  }

  
}