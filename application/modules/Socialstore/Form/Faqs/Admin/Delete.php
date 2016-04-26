<?php

class Socialstore_Form_Faqs_Admin_Delete extends Engine_Form {

	public function init() {
		
	$this -> setTitle('Delete FAQs')
		  -> setAttribs(array(
		  	'class'=>'global_form_popup'
		  ))
		  ->setDescription("Are you sure that you want to delete this faqs? It will not be recoverable after being deleted.");
	

	/**
	 * add button groups
	 */
	//Submit Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Delete',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));
        //Cancel link
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onClick'=> 'javascript:parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));

	}

}
