<?php
class Groupbuy_Form_Gift extends Engine_Form
{

  public function init()
  {
  	$this->setTitle('Purchase Gift for Friend')
      ->setDescription('Please enter valid information below');
    	$this->addElement('Text', 'buyer_name',array(
      'label'=>'Your Name*',
      'description' => '',
      'allowEmpty' => false,
      'required'=>true,
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
     'value'=>'',
    ));
  /*	$this->addElement('Text', 'email', array(
      'label' => 'Your Email',
      'description' => '',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
        array('EmailAddress', true),
      ),
    ));
     $this->addElement('Text', 'address',array(
      'label'=>'Your Address',
      'description' => '',
      'allowEmpty' => false,
      'required'=>true,
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
     'value'=>'',
    ));
     $this->addElement('Text', 'phone',array(
      'label'=>'Your Phone Number',
      'description' => '',
      'allowEmpty' => false,
      'required'=>true,
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
     'value'=>'',
    )); */
        	$this->addElement('Text', 'friend_name',array(
      'label'=>"Friend's Name*",
      'description' => '',
      'allowEmpty' => false,
      'required'=>true,
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
     'value'=>'',
    ));
  	$this->addElement('Text', 'friend_email', array(
      'label' => "Friend's Email*",
      'description' => '',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
        array('EmailAddress', true),
      ),
    ));
     $this->addElement('Text', 'friend_address',array(
      'label'=>"Friend's Address*",
      'description' => '',
      'allowEmpty' => false,
      'required'=>true,
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
     'value'=>'',
    ));
     $this->addElement('Text', 'friend_phone',array(
      'label'=>"Friend's Phone Number*",
      'description' => '',
      'allowEmpty' => false,
      'required'=>true,
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
     'value'=>'',
    ));
    $this->addElement('Textarea', 'note',array(
      'label'=>'Message*',
      'description' => '',
      'allowEmpty' => false,
      'required'=>true,
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

     // DisplayGroup: buttons
    $this->addDisplayGroup(array(
      'submit',
    ), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper'
      ),
    ));
    
  }
}