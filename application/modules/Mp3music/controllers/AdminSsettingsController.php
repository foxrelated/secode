<?php
class Mp3music_AdminSsettingsController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('mp3music_admin_main', array(), 'mp3music_admin_main_ssettings');
  }
  public function indexAction()
  {
    $group_members = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll()->toArray();
    if($this->getRequest()->getParam('save_change_group_setting')) 
    {
        $val = $this->getRequest()->getParam('val');
        Mp3music_Api_Cart::saveSettingsSelling($val,$val['select_group_member']);
        $default_group = $val['select_group_member'];
    }
    
    if ($this->getRequest()->getParam('save_change_global_setings'))
     {
          $val = $this->getRequest()->getParam('val');
          Mp3music_Api_Cart::saveSettingsSelling($val,0);             
     }
    $settings = Mp3music_Api_Cart::getSettingsSelling(0);   
    if (!isset($default_group))
        $default_group = 1;
     $this->view->group_members = $group_members;

     $this->view->default_view_group = $default_group;
     $this->view->settings = $settings;
     $this->view->currency = "USD";
    }
}