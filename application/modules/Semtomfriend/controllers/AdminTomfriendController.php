<?php

class Semtomfriend_AdminTomfriendController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('semtomfriend_admin_main', array(), 'semtomfriend_admin_main_tomfriend');

    $this->view->form = $form = new Semtomfriend_Form_Admin_Tomfriend();
    
    if( $this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost()))
    {
      $form->saveAdminSettings();
    }
  }



}