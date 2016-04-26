<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Groupbuy
 * @copyright  Copyright 2011 YouNet Company
 * @license    http://www.modules2buy.com/
 * @version    $Id: Global.php
 * @author     Minh Nguyen
 */
class Groupbuy_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    
    $currency = Groupbuy_Api_Core::getDefaultCurrency();
    $translate = Zend_Registry::get('Zend_Translate');

       $this->addElement('select', 'groupbuy_currency', array(
        'label' => 'Default Currency*',
        'description' => 'Select default currency if sellers has no permission to select ',
        'required'=>true,
        'multiOptions' => Groupbuy_Model_DbTable_Currencies::getMultiOptions(),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.currency', 'USD'),
      ));
      
      
    /*  $this->addElement('Radio', 'groupbuy_select_currency', array(
        'label' => 'Select Currency?',
        'description' => 'Allow sellers choose currency to sell deals? ',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.select.currency', 0),
      ));
      */
      
       $this->addElement('Radio', 'groupbuy_rate', array(
        'label' => 'Rate Deal?',
        'description' => 'Allow sellers to rate their own deals? ',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.rate', 0),
      ));
       /*
       $this->addElement('Radio', 'groupbuy_withdraw', array(
        'label' => 'Withdraw Wallet Amount',
        'description' => 'Allow sellers / buyers to withdraw their wallet amount? ',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.withdraw', 0),
      ));
       $this->addElement('Radio', 'groupbuy_approveRequest', array(
        'label' => 'Approve Withdraw Request',
        'description' => 'Automatically approve withdraw request of users? ',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.approveRequest', 0),
      )); */
       $this->addElement('Radio', 'groupbuy_approveAuto', array(
        'label' => 'Approve Deal?',
        'description' => 'Automatically approve deal? ',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.approveAuto', 0),
      ));
      $this->addElement('Radio', 'groupbuy_sendemail', array(
        'label' => 'Send Email To Users?',
        'description' => 'Automatically send email to users when a new deal is running? ',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.sendemail', 1),
      ));
      
       $this->addElement('Radio', 'groupbuy_sellermethod', array(
        'label' => 'Allow Sellers To Choose Payment Methods?',
        'description' => 'Allow sellers to choose which payment methods for their deals? ',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.sellermethod', 0),
      ));
      
       $this->addElement('Radio', 'groupbuy_virtualmoney', array(
        'label' => 'Buy Deals With Virtual Money?',
        'description' => 'Allow buyers to buy deals with Virtual Money? ',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.virtualmoney', 0),
      ));
      
       $this->addElement('select', 'groupbuy_adminmethod', array(
        'label' => 'Payment Method',
        'description' => 'Select Payment Method if sellers has no permission to select ',
        'required'=>true,
        'multiOptions' => array(
               0 => "All Methods",
               1 => "Paypal Only",
               2 => "Cash on Delivery Only",
               3 => "2Checkout Only"
       ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.adminmethod', '0'),
      ));
      
    $this->addElement('Text', 'groupbuy_minWithdrawSeller',array(
      'label'=>$translate->translate('Minimum  Withdraw Amount For Sellers (').$currency.')',
      'title' => $translate->translate('Minimum  Withdraw Amount For Sellers'),  
      'description' => '',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
     'value'=> Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.minWithdrawSeller', 5.00),
    ));
    $this->addElement('Text', 'groupbuy_maxWithdrawSeller',array(
      'label'=>$translate->translate('Maximum  Withdraw Amount For Sellers (').$currency.')',
      'title' => $translate->translate('Minimum  Withdraw Amount For Sellers'),  
      'description' => '',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
     'value'=> Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.maxWithdrawSeller', 100.00),
    ));
    /*
    $this->addElement('Text', 'groupbuy_minWithdrawBuyer',array(
      'label'=>'Minimum  Withdraw Amount For Buyers ($)',
      'title' => 'Minimum  Withdraw Amount For Buyers',  
      'description' => '',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
     'value'=> Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.minWithdrawBuyer', 5.00),
    ));
    $this->addElement('Text', 'groupbuy_maxWithdrawBuyer',array(
      'label'=>'Maximum  Withdraw Amount For Buyers ($)',
      'title' => 'Minimum  Withdraw Amount For Buyers',  
      'description' => '',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
     'value'=> Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.maxWithdrawBuyer', 50.00),
    ));
    */
   $this->addElement('Text', 'groupbuy_displayfee', array(
      'label' => $translate->translate('Fee For Publishing Deal (').$currency.')',
      'description' => 'Set fee to publish deal on Home Page, etc.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.displayfee', 10.00),
       ));
    $this->addElement('Text', 'groupbuy_fee', array(
      'label' => $translate->translate('Fee To Featuring Deal (').$currency.')',
      'description' => 'Set fee to feature deal on Home Page, etc.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.fee', 10.00),
    ));
/* $this->addElement('Text', 'groupbuy_commission', array(
      'label' => 'Commission (%)',
      'description' => '',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.commission', 5),
    )); 
     $this->addElement('Text', 'groupbuy_photos', array(
      'label' => 'Number Of Uploaded Photos',
      'description' => 'How many photos will be uploaded per deal? (Enter a number between 1 and 999)',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('deal.photos', 10),
    )); */
     $this->addElement('Text', 'groupbuy_transactions', array(
      'label' => 'Number Of Transactions Per Page',
      'description' => 'How many transactions will be shown per page? (Enter a number between 1 and 999)',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.transactions', 10),
    ));
    $this->addElement('Text', 'groupbuy_page', array(
      'label' => 'Number Of Deals Per Page',
      'description' => 'How many deals will be shown per page? (Enter a number between 1 and 999)',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.page', 10),
    ));

    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}