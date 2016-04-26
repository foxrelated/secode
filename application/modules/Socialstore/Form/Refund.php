<?php

class Socialstore_Form_Refund extends Engine_Form{
    public function init()
    {
       //Set Form Informations
    $this -> setAttribs(array('class' => 'global_form_popup','method' => 'post'))
          -> setTitle('Refund this product?')
	  -> setDescription('Are you sure that you want to refund this product? It will not be able to change later.');

    $this->addElement('Hidden','orderitem_id');
    
        // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Refund',
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
