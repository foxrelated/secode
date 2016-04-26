<?php

class Ynaffiliate_Form_HelpPage_Admin_Delete extends Engine_Form {

	public function init() {
		
	$this -> setTitle('Delete Help Page')
		  -> setAttribs(array(
		  	'class'=>'global_form_popup'
		  ))
		  ->setDescription("Are you sure that you want to delete this help page? It will not be recoverable after being deleted.");
	

	/**
	 * add button groups
	 */
	//Submit Buttons
    $this->addElement('Button', 'execute', array(
      'label' => 'Delete',
      'type' => 'button',
    'onclick' => 'this.form.submit(); removeSubmit()',
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
