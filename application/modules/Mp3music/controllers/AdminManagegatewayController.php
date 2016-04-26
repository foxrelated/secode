<?php
class Mp3music_AdminManagegatewayController extends Core_Controller_Action_Admin
{
   protected $_paginate_params = array();
   public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('mp3music_admin_main', array(), 'mp3music_admin_main_managegateway');
      $this->_paginate_params['page']   = $this->getRequest()->getParam('page', 1);
     $this->_paginate_params['limit']  = Engine_Api::_()->getApi('settings', 'core')->getSetting('mp3music.songsPerPage', 10);
  }
  public function indexAction()
  {
        if ($this->getRequest()->getParam('save_api_paypal'))
         {
             $val = $this->getRequest()->getParam('val');
             $val['is_active'] = 1;
             $params['admin_account'] = $val['admin_account'];
             $params['is_active'] = $val['is_active'];
             $params['params'] = $val;
             $params['api_app_id'] ="";
             Mp3music_Api_Gateway::saveSettingGateway('paypal',$params);  
             $admin = Mp3music_Api_Cart::getFinanceAccount(null,1);  
             $admin['account_username'] = $val['admin_account'];
             $admin['payment_type'] = 1;
             $admin = Mp3music_Api_Cart::saveFinanceAccount($admin);
             $this->view->message = 'Save setting gateway successfully';
             
         }
         $paypal = Mp3music_Api_Gateway::getSettingGateway('paypal');
         $paypal['params'] = unserialize($paypal['params']);
         $this->view->paypal = $paypal;
    }
}