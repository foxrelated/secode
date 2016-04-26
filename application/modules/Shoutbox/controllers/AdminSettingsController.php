<?php
/**
 * @author     George Coca
 * @website    geodeveloper.net <info@geodeveloper.net>   
 */
class Shoutbox_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
      
    // Make navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('shoutbox_admin_main', array(), 'shoutbox_admin_main_settings');

    $settings = Engine_Api::_()->getApi('settings', 'core');
      
    $this->view->form = $form = new Shoutbox_Form_Admin_Global();
    $form->shoutbox_shouts->setValue($settings->getSetting('shoutbox.shouts', 10));
    $form->shoutbox_autorefresh->setValue($settings->getSetting('shoutbox.autorefresh', 1));
    $form->shoutbox_timer->setValue($settings->getSetting('shoutbox.timer', 5000));
    if( $this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost()))
    {
      $values = $form->getValues();
       foreach ($values as $key => $value){
        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
      }
      $form->addNotice('Your changes have been saved.');
    }
  
  }
}
