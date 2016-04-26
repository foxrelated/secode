<?php
class Socialstore_Form_Attribute_Set_Create extends Engine_Form
{
  public function init()
  {
     //Set Method
   $this->setMethod('post');
   $this
      ->addPrefixPath('Socialstore_Form_Decorator', APPLICATION_PATH . '/application/modules/Socialstore/Form/Decorator', 'decorator')
      ->addPrefixPath('Socialstore_Form_Element', APPLICATION_PATH . '/application/modules/Socialstore/Form/Element', 'element')
      ->addElementPrefixPath('Socialstore_Form_Decorator', APPLICATION_PATH . '/application/modules/Socialstore/Form/Decorator', 'decorator');
   $this->setTitle('Add Attribute Set');
  if (Zend_Registry::isRegistered('store_id')) {     
		$store_id = Zend_Registry::get('store_id');
	}
     //VAT Id
    $this->addElement('Hidden','set_id');

     //VAT Name - Required
   $this->addElement('Text','name',array(
      'label'     => 'Attribute Name',
      'required'  => true,
      'allowEmpty'=> false,
    ));

     //Submit Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Save',
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
     //Display Group of Buttons
    $this->addDisplayGroup(array(
      'submit',
      'cancel',
    ), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper'
      ),
    ));
  }
}
?>