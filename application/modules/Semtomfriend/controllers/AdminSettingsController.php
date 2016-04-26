<?php

class Semtomfriend_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {

    // redirect
    $this->_helper->redirector->gotoRoute(array('module' => 'semtomfriend','controller'=>'tomfriend','action'=>'index'), 'admin_default',true);
    
  }



}