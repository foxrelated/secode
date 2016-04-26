<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Groupbuy
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: AdminAccountmanageController.php
 * @author     Minh Nguyen
 */
class Groupbuy_AdminAccountController extends Core_Controller_Action_Admin
{
   protected $_paginate_params = array();
   public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('groupbuy_admin_main', array(), 'groupbuy_admin_main_accounts');
      $this->_paginate_params['page']   = $this->getRequest()->getParam('page', 1);
     $this->_paginate_params['limit']  = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.page', 10);
  }
  public function indexAction()
  {
        $params = array_merge($this->_paginate_params, array());  
        $accounts = Groupbuy_Api_Cart::getFinanceAccountsPag($params);
        $this->view->accounts = $accounts;  
  }
  public function plusamountAction()
  {
    $id = $this->_getParam('id', null);
    $this->view->acc = $acc = Groupbuy_Api_Cart::getFinanceAccount($id,2);;
    $this->view->form = $form = new Groupbuy_Form_Admin_Editamount();
    // Check method/valid
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $values = $form->getValues();

    $amounts = $values['label'];
    if(!is_numeric($amounts) || $amounts < 0)
      {
                $form->getElement('label')->addError('The price number is invalid! (Ex: 20)');
                $flag = 1;
      }
     $amounts = round($amounts,2);
      if($flag == 1)
        return;
    Groupbuy_Api_Account::updateAmount($acc['paymentaccount_id'],$amounts,1);
    //Save transaction tracking
    $viewer = Engine_Api::_()->user()->getViewer();     
    $tttable = Engine_Api::_()->getDbtable('transactionTrackings','groupbuy');
    $ttdb = $tttable->getAdapter();
    $ttdb->beginTransaction();
    try
    {
        $ttvalues = array('transaction_date' => date('Y-m-d H:i:s'),
                          'user_seller' => $id,
                          'user_buyer' => $viewer->getIdentity(),
                          'item_id' => '',
                          'amount' => $amounts,
                          'account_seller_id' => Groupbuy_Api_Cart::getFinanceAccount($id,2),
                          'account_buyer_id' => Groupbuy_Api_Cart::getFinanceAccount($viewer->getIdentity(),1),
                          'number' => 1,
                          'transaction_status' => '1',
                          'params' => 'Add virtual money',
        );
        $ttrow = $tttable->createRow();
        $ttrow->setFromArray($ttvalues);
        $ttrow->save();
        $tranid = $ttrow->transactiontracking_id;
        $ttdb->commit();
    }
    catch (exception $e) {
        $ttdb->rollBack();
        throw $e;
    }    
    // Forward
    return $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => true,
      'parentRefresh' => true,
      'format'=> 'smoothbox',
      'messages' => array('Your changes have been saved.')
    ));
  }
  public function examountAction()
  {
    $id = $this->_getParam('id', null);
    $this->view->acc = $acc = Groupbuy_Api_Cart::getFinanceAccount($id,2);;
    $this->view->form = $form = new Groupbuy_Form_Admin_Editamount();
    // Check method/valid
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $values = $form->getValues();

    $amounts = $values['label'];
    if(!is_numeric($amounts) || $amounts < 0)
      {
                $form->getElement('label')->addError('The price number is invalid! (Ex: 20)');
                $flag = 1;
      }
     $amounts = round($amounts,2);
      if($flag == 1)
        return;
    Groupbuy_Api_Account::updateAmount($acc['paymentaccount_id'],$amounts,2);
    //Save transaction tracking
    $viewer = Engine_Api::_()->user()->getViewer();     
    $tttable = Engine_Api::_()->getDbtable('transactionTrackings','groupbuy');
    $ttdb = $tttable->getAdapter();
    $ttdb->beginTransaction();
    try
    {
        $ttvalues = array('transaction_date' => date('Y-m-d H:i:s'),
                          'user_seller' => $id,
                          'user_buyer' => $viewer->getIdentity(),
                          'item_id' => '',
                          'amount' => $amounts,
                          'account_seller_id' => Groupbuy_Api_Cart::getFinanceAccount($id,2),
                          'account_buyer_id' => Groupbuy_Api_Cart::getFinanceAccount($viewer->getIdentity(),1),
                          'number' => 1,
                          'transaction_status' => '1',
                          'params' => 'Deduct virtual money',
        );
        $ttrow = $tttable->createRow();
        $ttrow->setFromArray($ttvalues);
        $ttrow->save();
        $tranid = $ttrow->transactiontracking_id;
        $ttdb->commit();
    }
    catch (exception $e) {
        $ttdb->rollBack();
        throw $e;
    }    
    // Forward
    return $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => true,
      'parentRefresh' => true,
      'format'=> 'smoothbox',
      'messages' => array('Your changes have been saved.')
    ));
  }
}