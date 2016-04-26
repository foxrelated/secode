<?php
class Groupbuy_Form_Admin_Currency extends Engine_Form
{
  public function init()
  {
        //Set Method
    $this->setMethod('post');
        //Curency Name - Required
    $label = new Zend_Form_Element_Text('label');
    $label -> setLabel('Currency Name')
           -> addValidator('NotEmpty')
           -> setRequired(true)
           -> setAttrib('class', 'text');

        //Currency Symbol - Required
    $symbol = new Zend_Form_Element_Text('symbol');
    $symbol ->  setLabel('Symbol')
            ->  addValidator('NotEmpty')
            ->  setRequired(true)
            ->  setAttrib('class', 'text');

        //Curency Precision - Required and The Range of Value is 0 to 5
    $precision = new Zend_Form_Element_Text('precision');
    $precision  -> setLabel('Precision')
                -> setRequired(true)
                -> addValidator(new Zend_Validate_Between(0,5));

       //Currency Status
    $status = new Zend_Form_Element_Select('status');
    $status -> setLabel('Status')
            -> setMultiOptions(array('Enable'  => 'Enable',
                                     'Disable' => 'Disable'));

        //Currency Display
    $display = new Zend_Form_Element_Select('display');
    $display -> setLabel('Display')
             -> setMultiOptions(array(1 => 'No Symbol',
                                      2 => 'Use Symbol',
                                      3 => 'Use Shortname',
                                      4 => 'Use Name')
                             );
        //Currency Code Id
    $code = new Zend_Form_Element_Hidden('code');

        //Add Elements To The Form
    $this->addElements(array(
      $label,
      $symbol,
      $precision,
      $status,
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