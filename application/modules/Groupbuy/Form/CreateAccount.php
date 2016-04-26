<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Groupbuy
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: CreateAccount.php
 * @author     Minh Nguyen
 */
class Groupbuy_Form_CreateAccount extends Engine_Form
{
  protected $_account;
  public function init()
  {
    // Init form
    $this
      ->setTitle('Create Account')
      ->setAttrib('id',      'form-account-create')
      ->setAttrib('name',    'account_create')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ;

    // Init username
    $this->addElement('Text', 'account_username', array(
      'label' => 'Seller Account',
      'maxlength' => '63',
      'required' => true,
      'description' => 'Paypal email account. ',
      'filters' => array(
        new Engine_Filter_StringLength(array('max' => '63')),
      )
    ));
    
    $this->account_username->getDecorator("Description")->setOption("placement", "append");
    $this->addElement('select', 'currency', array(
        'label' => 'Default Currency*',
        'description' => 'Select default currency',
        'required'=>true,
        'multiOptions' => Groupbuy_Model_DbTable_Currencies::getMultiOptions(),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.currency', 'USD'),
      ));
     $this->currency->getDecorator("Description")->setOption("placement", "append");
    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Add Account',
      'type'  => 'submit',
       'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));
     // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index'), 'groupbuy_account', true),
      'onclick' => '',
      'decorators' => array(
        'ViewHelper',
      ),
    ));
     // DisplayGroup: buttons
    $this->addDisplayGroup(array(
      'submit',
      'cancel',
    ), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper'
      ),
    ));
  }

  public function saveValues()
  {
      $values   = $this->getValues(); 
      if(trim($values['account_username']) == "")
      {
           $this->getElement('account_username')->addError('Please enter seller username!'); 
            return ;
      }
      else if(trim($values['account_username'] != ""))
      {
          $email = trim($values['account_username']);
          $regexp = "/^[A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/";                                                                                                            
        if(!preg_match($regexp, $email))
        {
            $is_validate=1;
            $this->getElement('account_username')->addError('Seller Account is not valid!'); 
            return ;
        }
      }
      return Groupbuy_Api_Account::insertAccount($values);   
  }
}
