<?php
class Mp3music_AdminManageaccountsController extends Core_Controller_Action_Admin
{
   protected $_paginate_params = array();
   public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('mp3music_admin_main', array(), 'mp3music_admin_main_manageaccounts');
      $this->_paginate_params['page']   = $this->getRequest()->getParam('page', 1);
     $this->_paginate_params['limit']  = Engine_Api::_()->getApi('settings', 'core')->getSetting('mp3music.songsPerPage', 10);
  }
  public function indexAction()
  {
        $params = array_merge($this->_paginate_params, array());  
        $accounts = Mp3music_Api_Cart::getFinanceAccountsPag($params);
        $this->view->accounts = $accounts;
        $this->view->currency = "USD";    
    }
}