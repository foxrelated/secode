<?php
class Groupbuy_Form_Gift_Create extends Engine_Form
{

  public function init()
  {
  	$this->setTitle('Buy this deal as gift for your friends')
      ->setDescription('Please enter valid information below');
	$this->addElement('Text', 'name',array(
      'label'=>'Full Name*',
      'description' => '',
      'allowEmpty' => false,
      'required'=>true,
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
     'value'=>'',
    ));
  	$this->addElement('Text', 'email', array(
      'label' => 'Email Address*',
      'description' => '',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
        array('EmailAddress', true),
      ),
    ));
     $this->addElement('Text', 'address',array(
      'label'=>'Address*',
      'description' => '',
      'allowEmpty' => false,
      'required'=>true,
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
     'value'=>'',
    ));
     $this->addElement('Text', 'phone',array(
      'label'=>'Phone Number*',
      'description' => '',
      'allowEmpty' => false,
      'required'=>true,
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
     'value'=>'',
    ));
    $this->addElement('Textarea', 'note',array(
      'label'=>'Extra Note',
      'description' => '',
      'allowEmpty' => true,
      'required'=>false,
      'filters' => array(
        new Engine_Filter_HtmlSpecialChars(),
        new Engine_Filter_Censor(),
        new Engine_Filter_EnableLinks(),
      ),
     'value'=>'',
    ));
    $this->addElement('Button', 'submit', array(
      'label' => 'Purchase',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));
     $this->addElement('Hidden', 'deal', array(
      'order' => 100
    ));

    $this->addElement('Hidden', 'number_buy', array(
      'order' => 102
    ));

    $this->addElement('Hidden', 'total_amount', array(
      'order' => 103
    ));
    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage-buying'), 'groupbuy_general', true),
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
}