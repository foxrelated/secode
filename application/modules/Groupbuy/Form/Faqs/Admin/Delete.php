<?php

class Groupbuy_Form_Faqs_Admin_Delete extends Engine_Form {

	public function init() {
		
	$this -> setTitle('Delete FAQ')
	->setAttribs(array('class'=>''));

	/**
	 * add button groups
	 */
	$this->addElement('Button', 'submit', array(
      'label' => 'Submit',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

	}

}
