<?php
class Ynidea_Form_Admin_Decision extends Engine_Form {
  public function init()
  {
  
   $this->setTitle('Edit Decision');
    //Decision Filter
    $this->addElement('Select', 'decision', array(
      'label' => '',
      'multiOptions' => array(
        'realized' => 'Realized',
        'selected' => 'Selected',
		''		   => 'No feature'
    ),
      'value' => 'realized',
    ));

     // Buttons
    $this->addElement('Button', 'button', array(
      'label' => 'Save',
      'type' => 'submit',
      'decorators' => array('ViewHelper')
    ));
	
	$this -> addElement('Cancel', 'cancel', array(
		  'label' => 'cancel',
	      'link' => true,
	      'prependText' => 'or ',
	      'href' => '',
	      'onclick' => 'parent.Smoothbox.close()',
	      'decorators' => array(
	        'ViewHelper'
	      )
	));	

    $this->addDisplayGroup(array('button', 'cancel'), 'buttons');
	
  }
}