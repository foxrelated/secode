<?php
class Socialstore_Form_Admin_Category_Change extends Engine_Form {

	public function init() {
		$this
      ->addPrefixPath('Socialstore_Form_Decorator', APPLICATION_PATH . '/application/modules/Socialstore/Form/Decorator', 'decorator')
      ->addPrefixPath('Socialstore_Form_Element', APPLICATION_PATH . '/application/modules/Socialstore/Form/Element', 'element')
      ->addElementPrefixPath('Socialstore_Form_Decorator', APPLICATION_PATH . '/application/modules/Socialstore/Form/Decorator', 'decorator');
  	
		
		$this -> setMethod('post');
		$this->setDescription("Change category for items that belongs to category which you are going to delete.");

		$route = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.pathname', "socialstore");
		
		$this->addElement('MultiLevel', 'category_id', array(
	        'label' => 'Category',
	        'required'=>true,
	        'allowEmpty'=>false,
	        'model'=>'Socialstore_Model_DbTable_Categories',
	        'onchange'=>"en4.store.changeCategory($(this),'category_id','Socialstore_Model_DbTable_Categories','$route')",
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
				

		$this -> addDisplayGroup(array('submit','cancel'), 'buttons');
		$button_group = $this -> getDisplayGroup('buttons');
	}

}
