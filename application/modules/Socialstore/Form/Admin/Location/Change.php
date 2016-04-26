<?php
class Socialstore_Form_Admin_Location_Change extends Engine_Form {

	public function init() {
		$this
      ->addPrefixPath('Socialstore_Form_Decorator', APPLICATION_PATH . '/application/modules/Socialstore/Form/Decorator', 'decorator')
      ->addPrefixPath('Socialstore_Form_Element', APPLICATION_PATH . '/application/modules/Socialstore/Form/Element', 'element')
      ->addElementPrefixPath('Socialstore_Form_Decorator', APPLICATION_PATH . '/application/modules/Socialstore/Form/Decorator', 'decorator');
  	
		
		$this -> setMethod('post');
		$this->setDescription("Change location for items that belongs to location which you are going to delete.");

		$route = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.pathname', "socialstore");
		
		$this->addElement('MultiLevel', 'location_id', array(
	        'label' => 'Location*',
	        'required'=>true,
			'allowEmpty' => false,
	        'model'=>'Socialstore_Model_DbTable_Locations',
	        'onchange'=>"en4.store.changeCategory($(this),'location_id','Socialstore_Model_DbTable_Locations','$route')",
			'title' => '',
			'value' => ''
    	));
		
		// Buttons
		$this -> addElement(
			'Button', 'submit', 
				array('label' => 'Save', 'type' => 'submit',
				 'ignore' => true,
				  'decorators' => array('ViewHelper')));
				
		$this -> addElement('Cancel', 'cancel',
		 array('label' => 'cancel', 'link' => true, 
		 'prependText' => ' or ', 'href' => '', 'onClick' => 'javascript:parent.Smoothbox.close();', 'decorators' => array('ViewHelper')));
				

		$this -> addDisplayGroup(array('submit', 'cancel'), 'buttons');
		$button_group = $this -> getDisplayGroup('buttons');
	}

}
