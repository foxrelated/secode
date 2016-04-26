<?php
class Ynfundraising_Form_addLocation extends Engine_Form
{
  public function init()
  {
  	$this->setAttrib('class','global_form_popup')->setAttrib ( 'name', 'ynfundraising_add_location');
	// Element: location
	$this->addElement ( 'Text', 'location', array (
				'label' => 'Location/Venue',
				'required' => true,
				'filters' => array (
						new Engine_Filter_Censor (),
						'StripTags',
						new Engine_Filter_StringLength ( array (
								'max' => '256' 
						) ) 
				) 
		) );
	// Element: address
	$this->addElement ( 'Text', 'address', array (
				'label' => 'Address',
				'required' => true,
				'filters' => array (
						new Engine_Filter_Censor (),
						'StripTags',
						new Engine_Filter_StringLength ( array (
								'max' => '256' 
						) ) 
				) 
		) );
	// Element: city
	$this->addElement ( 'Text', 'city', array (
				'label' => 'City',
				'required' => true,
				'filters' => array (
						new Engine_Filter_Censor (),
						'StripTags',
						new Engine_Filter_StringLength ( array (
								'max' => '256' 
						) ) 
				) 
		) );
	// Element: zip
	$this->addElement ( 'Text', 'zip_code', array (
				'label' => 'Zip/Postal Code',
				'required' => true,
				'filters' => array (
						new Engine_Filter_Censor (),
						'StripTags',
						new Engine_Filter_StringLength ( array (
								'max' => '256' 
						) ) 
				) 
		) );
	// Element: country	
	$this->addElement ( 'Select', 'country', array (
			'label' => 'Country',
			'multiOptions' => Ynfundraising_Model_DbTable_Countries::getMapMultiOptions(),
			'value' => 'Viet Nam' 
	) );
	// google map
	$this->addElement('Dummy', 'google_map', array(
  		  'label' => 'Save Changes',	
	      'decorators' => array(
		          array('ViewScript', array(
		                'viewScript' => '_googleMap.tpl',
		                'class'      => 'form element'
		          ))
		      ),
	));
  }
}