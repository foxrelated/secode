<?php
/**
 * SocialEnginePro
 *
 * @category   Application_Extensions
 * @package    E-money
 * @author     Azim
 */

/**
 * @category   Application_Extensions
 * @package    E-money
 */
class Money_Form_Paid extends Engine_Form
{
  public function init()
  {
    $this
      
      ->setMethod('POST')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ;
    
    

    // Buttons
    $buttons = array();

   


    $this->addElement('Button', 'submit', array(
      'label' => 'Paid',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));
    $buttons[] = 'submit';
    
   // $this->submit->getDecorator('label')->setOption('tagOptions', array('class'=>'sep_icon_purchase_16'));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
     
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $buttons[] = 'cancel';


    $this->addDisplayGroup($buttons, 'buttons');
    $button_group = $this->getDisplayGroup('buttons');

  }
}