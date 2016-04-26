<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Groupbuy
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: AdminStatisticsController.php
 * @author     Minh Nguyen
 */
class Groupbuy_AdminStatisticController extends Core_Controller_Action_Admin
{
   protected $_paginate_params = array();
   public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('groupbuy_admin_main', array(), 'groupbuy_admin_main_statistics');
      $this->_paginate_params['page']   = $this->getRequest()->getParam('page', 1);
     $this->_paginate_params['limit']  = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.page', 10);
  }
  public function indexAction()
  {
     $this->view->deals_0 = Engine_Api::_()->groupbuy()->getDealStatistics(0);
     $this->view->deals_10 = Engine_Api::_()->groupbuy()->getDealStatistics(10);
     $this->view->deals_20 = Engine_Api::_()->groupbuy()->getDealStatistics(20);
     $this->view->deals_30 = Engine_Api::_()->groupbuy()->getDealStatistics(30);
     $this->view->deals_40 = Engine_Api::_()->groupbuy()->getDealStatistics(40);
     $this->view->deals_50 = Engine_Api::_()->groupbuy()->getDealStatistics(50);
     
     $this->view->amount_0 = Engine_Api::_()->groupbuy()->getAmounts('Paid amount to Buyer');
     $this->view->amount_1 = Engine_Api::_()->groupbuy()->getAmounts('Paid amount to Seller');
     $this->view->amount_2 = Engine_Api::_()->groupbuy()->getAmounts('');
     $this->view->amount_3 = Engine_Api::_()->groupbuy()->getAmounts('Paypal');
     $this->view->fee_0 = Engine_Api::_()->groupbuy()->getAmounts('Pay fee publish Deal');
     //$this->view->fee_1 = Engine_Api::_()->groupbuy()->getFees(1);
     //$this->view->fee_2 = Engine_Api::_()->groupbuy()->getFees(2);
     
     $this->view->requests_0 = Engine_Api::_()->groupbuy()->getPublished(20);
     $this->view->requests_1 = Engine_Api::_()->groupbuy()->getRequests("Paid amount to Seller");
     $this->view->requests_2 = Engine_Api::_()->groupbuy()->getRequests('Paid amount to Buyer');
     $this->view->requests_3 = Engine_Api::_()->groupbuy()->getRequests('');
  }
}