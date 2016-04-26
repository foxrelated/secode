<?php
class Socialstore_Form_Product_Attribute_Preset_Create extends Engine_Form
{
  public function init()
  {
     //Set Method
   $this->setMethod('post');
   $this
      ->addPrefixPath('Socialstore_Form_Decorator', APPLICATION_PATH . '/application/modules/Socialstore/Form/Decorator', 'decorator')
      ->addPrefixPath('Socialstore_Form_Element', APPLICATION_PATH . '/application/modules/Socialstore/Form/Element', 'element')
      ->addElementPrefixPath('Socialstore_Form_Decorator', APPLICATION_PATH . '/application/modules/Socialstore/Form/Decorator', 'decorator');
   $this->setTitle('Save as Attribute Preset');
   
   
   $this->addElement('Hidden','product_id');

     //VAT Name - Required
   $this->addElement('Text','preset_name',array(
      'label'     => 'Attribute Preset Name',
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