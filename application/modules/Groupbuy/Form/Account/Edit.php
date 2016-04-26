<?php
class Groupbuy_Form_Account_Edit extends Engine_Form
{
  protected $_field;

  public function init()
  {
 	$this
      ->setTitle('Edit Account')
      ->setDescription('Edit your personal information below');
      ;

    // Init username
	$this->addElement('Text', 'full_name',array(
      'label'=>'Full Name',
      'description' => '',
      'allowEmpty' => false,
      'required'=>true,
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));
    $this->full_name->getDecorator("Description")->setOption("placement", "append");
  	$this->addElement('Text', 'account_username', array(
      'label' => 'Seller Account',
       'description' => 'Paypal email account. ',    
      'required' => false,
      'allowEmpty' => true,
      'validators' => array(
        array('NotEmpty', true),
        array('EmailAddress', true),
      ),
    ));
    $this->account_username->getDecorator("Description")->setOption("placement", "append");
    $this->addElement('select', 'currency', array(
        'label' => 'Default Currency*',
        'description' => 'Select default currency',
        'required'=>true,
        'multiOptions' => Groupbuy_Model_DbTable_Currencies::getMultiOptions(),
      ));
    $this->currency->getDecorator("Description")->setOption("placement", "append");
    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type'  => 'submit',
    'decorators' => array(
        'ViewHelper',
      ),
    ));
    
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'Cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index'), 'groupbuy_account', true),
      'onclick' => '',
    'decorators' => array(
        'ViewHelper',
      ),
    ));
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
  
}