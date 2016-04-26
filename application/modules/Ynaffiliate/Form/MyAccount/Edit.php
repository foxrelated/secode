<?php
class Ynaffiliate_Form_MyAccount_Edit extends Engine_Form
{

  public function init()
  {
 	$this->setTitle('Add Paypal Account')
      	->setDescription('Add your Paypal account to request money. (*) is required.');
	  
		
		$this->addElement('Text','paypal_displayname',array(
			'label'=>'Paypal Display Name*',
			'description'=>'Please fill your Paypal Display Name in the text box below.',
			'required'=>true,
			'maxlength'=>128,
			'filters'=>array('StringTrim'),
		));
		
		$this->addElement('Text','paypal_email',array(
			'label'=>'Paypal Email Address*',
			'required'=>true,
			'maxlength'=>128,
			'description'=>'Please fill your Paypal Email Address in the text box below.',
			'filters'=>array('StringTrim'),
			'validators'=>array(
				'EmailAddress',
			),
		));
	  $currency = Engine_Api::_()->getDbTable('exchangerates','ynaffiliate')->getExchangerate();
	  $currOptions = array();
	  $base_currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
	  $currOptions[$base_currency] = $base_currency;
	  foreach( $currency as $curr ) {
		  $currOptions[$curr->exchangerate_id] = $curr->exchangerate_id;
	  }

	  $this->addElement('Select', 'selected_currency', array(
		  'label' => 'Currency',
		  'description'=>'Please select your Paypal currency.',
		  'multiOptions' => $currOptions,
		  'value'=>$base_currency,
	  ));
    
    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type'  => 'submit',
    'ignore' => true,
    'decorators' => array(
        'ViewHelper',
      ),
    ));
    
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index'), 'ynaffiliate_request', true),
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