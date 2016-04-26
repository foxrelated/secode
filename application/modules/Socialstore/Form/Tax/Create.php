<?php
class Socialstore_Form_Tax_Create extends Engine_Form
{
  public function init()
  {
     //Set Method
   $this->setMethod('post');
   $this->setTitle('Add Tax');

     //VAT Id
    $this->addElement('Hidden','tax_id');

     //VAT Name - Required
   $this->addElement('Text','name',array(
      'label'     => 'Tax Name',
      'required'  => true,
      'allowEmpty'=> false,
    ));

     //VAT Value - Required
    $this->addElement('text','value',
	    array('label'=>'Value (%)',
    	'filters'=>array('StringTrim'),
    	'validators'=>array('Float'),
    	'required'=>true,
    	'value'=>'0.00'));

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