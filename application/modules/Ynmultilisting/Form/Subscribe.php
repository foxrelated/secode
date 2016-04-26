<?php
class Ynmultilisting_Form_Subscribe extends Fields_Form_Search
{
	
  public function init()
  {
	$view = Zend_Registry::get('Zend_View');
	$this
      ->setAttribs(array( 'id' => 'filter_form',
                          'class' => 'global_form_box search_form',
                           'method' => 'GET'
                    ));
					
	//category
	 $this->addElement('Select', 'category_id_subscribe', array(
      'label' => 'Category',
      'multiOptions' => array(
	  	'all' => 'All',
	  ),
    ));
	
	//Adress map
	$this -> addElement('Dummy', 'location_map', array(
		'decorators' => array( array(
			'ViewScript',
			array(
				'viewScript' => '_location_search_subscribe.tpl',
				'class' => 'form element',
			)
		)), 
	));
	
	$this -> addElement('Text', 'within_subscribe', array(
		'label' => 'Radius (mile)',
		'placeholder' => $view->translate('Radius (mile)..'),
		'maxlength' => '60',
		'value' => 50,
	));
		
	$this -> addElement('hidden', 'location_address_subscribe', array(
		'value' => '0',
		'order' => '97'
	));

	$this -> addElement('hidden', 'lat_subscribe', array(
		'value' => '0',
		'order' => '98'
	));
	
	$this -> addElement('hidden', 'long_subscribe', array(
		'value' => '0',
		'order' => '99'
	));
	
			
	$this->addElement('Text', 'email_subscribe', array(
        'label' => 'Email Address',
    ));
	
    // Buttons
    $this->addElement('Button', 'submit_button', array(
	      'value' => 'submit_button',
	      'label' => 'Subscribe Now !',
	      'type' => 'button',
	      'onClick' => 'checkValidate()',
	      'ignore' => true,
    ));
	
  }
}
