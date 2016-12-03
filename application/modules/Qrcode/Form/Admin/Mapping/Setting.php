<?php

class Qrcode_Form_Admin_Mapping_Setting extends Engine_Form
{

	public function init()
	{


		$this
		->setTitle(Zend_Registry::get('Zend_Translate')->translate('Custom Fields Settings'))
		->setDescription(Zend_Registry::get('Zend_Translate')->translate('Set Setting for QR Code'));

		$fields = Engine_Api::_()->fields()->getFieldsMeta('user');

		$userFields = array();
		foreach ($fields as $field){
			if(!($field->type=='profile_type' || $field->type=='heading')){
				$userFields[$field->field_id] = $field->label;
			}
		}


		$this->addElement('Select', 'website', array(
                  'label' => Zend_Registry::get('Zend_Translate')->translate('Website'),
		          'description' => Zend_Registry::get('Zend_Translate')->translate('Select Website'),
				   'required' => true,
		           'allowEmpty' => false,
                   'multiOptions'=>$userFields
		));

		$this->addElement('Select', 'phone', array(
          'label' => Zend_Registry::get('Zend_Translate')->translate('Phone'),
		  'description' => Zend_Registry::get('Zend_Translate')->translate('Select Phone No.'),
     	  'required' => true,
		  'allowEmpty' => false,
          'multiOptions'=>$userFields
		));
		$this->addElement('Multiselect', 'contact', array(
          'label' => Zend_Registry::get('Zend_Translate')->translate('Contact'),
		  'description' =>Zend_Registry::get('Zend_Translate')->translate('Hold down the CTRL key to select or de-select specific Profile Fields for Contact'),
     	  'required' => true,
     	  'required' => true,
		  'allowEmpty' => false,
          'multiOptions'=>$userFields
		));
	 // init submit
		$this->addElement('Button', 'submit', array(
      'label' => Zend_Registry::get('Zend_Translate')->translate('Save'),
      'order' => 5,
      'type' => 'submit',
      'ignore' => true
		));
	}

}