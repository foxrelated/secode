<?php
class Money_Form_Admin_Package_Create extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Create Subscription Plan')
      ->setDescription('Please note that payment parameters (Price, ' .
          'Recurrence, Duration, Trial Duration) cannot be edited after ' .
          'creation. If you wish to change these, you will have to create a ' .
          'new plan and disable the current one.')
      ;

    // Element: title
    $this->addElement('Text', 'title', array(
      'label' => 'Title',
      'required' => true,
      'allowEmpty' => false,
      'filters' => array(
        'StringTrim',
      ),
    ));

    // Element: description
    $this->addElement('Textarea', 'description', array(
      'label' => 'Description',
      'validators' => array(
        array('StringLength', true, array(0, 250)),
      )
    ));

    
    
    // Element: price
    $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('money.currency');
    $this->addElement('Text', 'price', array(
      'label' => 'Price',
      'description' => 'The amount to charge the member. This will be charged ' .
          'once for one-time plans, and each billing cycle for recurring ' .
          'plans. Setting this to zero will make this a free plan.',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('Float', true),
        new Engine_Validate_AtLeast(0),
      ),
      'value' => '0.00',
    ));

    
    
        // Element: enabled
    $this->addElement('Radio', 'enabled', array(
      'label' => 'Enabled?',
      'description' => 'Can members choose this plan? Please note that disabling this plan will grandfather in existing plan members until they pick a new plan.',
      'multiOptions' => array(
        '1' => 'Yes, members may select this plan.',
        '0' => 'No, members may not select this plan.',
      ),
      'value' => 1,
    ));

    

 

    // Element: execute
    $this->addElement('Button', 'execute', array(
      'label' => 'Create Plan',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper'),
    ));

    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'prependText' => ' or ',
      'ignore' => true,
      'link' => true,
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index', 'package_id' => null)),
      'decorators' => array('ViewHelper'),
    ));

    // DisplayGroup: buttons
    $this->addDisplayGroup(array('execute', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      )
    ));
  }
}