<?php
class Socialstore_Form_Gda_RequestGda extends Engine_Form
{
   public function init()
  {
    // Init form
    $this
      ->setTitle('Deal Request')
      ->setAttribs(array(
      'class' => 'global_form_popup',
      'id' => 'socicalstore_request_gda'
      ))
      ;
    // Init quantity
    $this->addElement('Text', 'org_qty', array(
      'label' => 'Quantity',
      'maxlength' => '40',
      'required'=>true, 
      'validators' => array(
        array('Int', true)
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '63')),
      )
    ));
    // Init percentage 
    /*$this->addElement('Text', 'org_discount', array(
      'label' => 'Percentage ',
      'maxlength' => '40',
      'required'=>true, 
      'validators' => array(
        array('Float', true),
        array('Between', true, array(0, 100, true))
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '63')),
      )
    ));  */
    $this->addElement('Radio', 'org_discount', array(
      'label' => 'Percentage',
      'multiOptions' => array(
        '5' => '5%',
        '10' => '10%',
        '20' => '20%',
        '30' => '30%',
        '40' => '40%',
        '50' => '50%',
        '60' => '60%',
        '70' => '70%',
        '80' => '80%',
      ),
      'value' => '5'
    ));
  	// Init message
    $this->addElement('Textarea', 'org_message', array(
      'label' => 'Message',
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_EnableLinks(),
      ),
    ));
    // Init Deal Request checkbox
    $this->addElement('Checkbox', 'gda', array(
      'label' => "I agree to the ",
      'value' => 0, 
      'required'=>false, 
      'checked' => false,
    ));
    $default_buyer_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller'=>'help'), 'socialstore_extended', true);    
    $URL = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.buyerpolicy', $default_buyer_url);
    $this->addElement('Cancel', 'link', array(
      'label' => 'Term of Use and Privacy Statement',
      'link' => true,
      'onclick' => 'goto("'.$URL.'")', 
      'decorators' => array(
        'ViewHelper'
      )
    ));                                                    
    $this->addDisplayGroup(array('gda', 'link'), 'buttons1', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper'
      )));   
 
   	$this->addElement('Button', 'submit', array(
      'label' => 'Send Request',
      'type' => 'submit',
   	  'decorators' => array(
        'ViewHelper',
      ),
    ));
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'Cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close()',
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