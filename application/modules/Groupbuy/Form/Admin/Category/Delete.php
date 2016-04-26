<?php

class Groupbuy_Form_Admin_Category_Delete extends Engine_Form{
	public function init(){
		$this->setAttribs(array(
			'class'=>'global_form_popup',
			'method' =>'post',
		
		))
		->setTitle('Delete Deal Category?')
		->setDescription('Are you sure that you want to delete this category? It will not be recoverable after being deleted.');
    
	$this->addElement('select','node_id', array(
		'label'=>'Category',
		'description'=>'Move item in this category to the selected categoy.'
		
	));
	// Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Delete Category Item',
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
