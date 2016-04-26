<?php

class Ynaffiliate_Form_Admin_Deny extends Engine_Form{
    public function init()
    {
       //Set Form Informations
    $this -> setAttribs(array('class' => 'global_form_popup','method' => 'post'))
          -> setTitle('Deny This Affiliate?')
	  -> setDescription('Are you sure that you want to deny this affiliate?');

       //VAT Id
    $this->addElement('Hidden','account_id');
    
        // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Deny',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
		
	}

}
