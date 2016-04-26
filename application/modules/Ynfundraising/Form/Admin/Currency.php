<?php
class Ynfundraising_Form_Admin_Currency extends Engine_Form
{
  public function init()
  {
        //Set Method
    $this->setMethod('post')
		->setTitle('Edit Currency')
		//->setDescription('YNFUNDRAISING_FORM_ADMIN_CURRENCY_DESCRIPTION')
    	->setAttrib('class', 'global_form_popup');

        //Curency Name - Required
    $label = new Zend_Form_Element_Text('label');
    $label -> setLabel('Currency Name*')
           -> addValidator('NotEmpty')
           -> setRequired(true)
           -> setAttrib('class', 'text');

        //Currency Symbol - Required
    $symbol = new Zend_Form_Element_Text('symbol');
    $symbol ->  setLabel('Symbol*')
            ->  addValidator('NotEmpty')
            ->  setRequired(true)
            ->  setAttrib('class', 'text');

        //Curency Precision - Required and The Range of Value is 1 to 5
    $precision = new Zend_Form_Element_Text('precision');
    $precision  -> setLabel('Precision')
				-> setDescription('digit from 0 to 2')
                -> setRequired(true)
                -> addValidator(new Zend_Validate_Between(0,2));

    //Currency Code Id
    $code = new Zend_Form_Element_Hidden('code');

        //Add Elements To The Form
    $this->addElements(array(
      $label,
      $symbol,
      $precision,
      $display,
      $code
    ));

        //Submit Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Edit Currency',
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