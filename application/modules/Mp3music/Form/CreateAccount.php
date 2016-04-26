<?php
class Mp3music_Form_CreateAccount extends Engine_Form
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
      'label' => 'Finance Account',
      'maxlength' => '63',
      'required' => true,
      'filters' => array(
        new Engine_Filter_StringLength(array('max' => '63')),
      )
    ));
 
    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Add Account',
      'type'  => 'submit',
    ));
  }

  public function saveValues()
  {
      $values   = $this->getValues(); 
      if(trim($values['account_username']) == "")
      {
           $this->getElement('account_username')->addError('Please enter finance username!'); 
            return ;
      }
      else if($values['account_username'] != "")
      {
          $email = $values['account_username'];
            $regexp = "/^[A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/";
            if(!preg_match($regexp, $email))
            {
                $is_validate=1;
                $this->getElement('account_username')->addError('Finance Account is not valid!'); 
                return ;
            }
      }
      return Mp3music_Api_Account::insertAccount($values);   
  }
}
