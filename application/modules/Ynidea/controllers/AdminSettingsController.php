<?php
/**
 * @category   Application_Extensions
 * @package    Idea
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    www.younetco.com
 */
class Ynidea_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynidea_admin_main', array(), 'ynidea_admin_main_settings');

    $this->view->form  = $form = new Ynidea_Form_Admin_Global();
    
    if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) )
    {
      $values = $form->getValues();
      
      foreach ($values as $key => $value){
        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
      }
      $form->addNotice('Your changes have been saved.');
    }
  }
}