<?php
class Groupbuy_Form_Admin_Vat extends Engine_Form
{
  public function init()
  {
     //Set Method
   $this->setMethod('post');

     //VAT Id
    $this->addElement('Hidden','vat_id');

     //VAT Name - Required
   $this->addElement('Text','name',array(
      'label'     => 'VAT Name',
      'required'  => true,
      'allowEmpty'=> false,
    ));

     //VAT Value - Required
    $value = new Engine_Form_Element_Text('value');
    $value-> setLabel('Value')
          //-> addValidator(new Zend_Validate_Float())
          -> setRequired(true)
          -> setValue('0.00');
    $this->addElement($value);

     //Submit Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Add VAT',
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