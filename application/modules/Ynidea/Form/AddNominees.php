<?php

class Ynidea_Form_AddNominees extends Engine_Form
{
  public function init()
  {   
        $this->setTitle("Add Nominees")
          ->setAttrib('name', 'ynidea_add_nominees')
		  ->setAttrib('class', '')
		  ->setDescription("Type the first letters of an idea to find it.");
        $translate = Zend_Registry::get('Zend_Translate');
                    
      //Nominees
      $this->addElement('Text', 'to',array(
		  'label' => 'Nominees',
          'autocomplete' => 'off',
          'filters' => array(
            new Engine_Filter_Censor(),
          ),
        ));
      // Init to Values
    $this->addElement('Hidden', 'toValues', array(
      'label' => 'Nominees',
      'required' => true,
      'allowEmpty' => false,
      'order' => 1,
      'validators' => array(
        'NotEmpty'
      ),
      'filters' => array(
        'HtmlEntities'
      ),
    ));
    Engine_Form::addDefaultDecorators($this->toValues); 
	
	$this->addElement('Hidden', 'trophy_id', array(
      'value' => Zend_Controller_Front::getInstance()->getRequest()->getParam('id'),
      'order' => 2,
    ));
  
    $this->addElement('Button', 'submit', array(
    'label' => 'Add',
    'type' => 'submit',
    'decorators' => array(
    'ViewHelper',
    ),
    ));
    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => 'javascript:;',
      'onclick' => 'parent.Smoothbox.close()',
      'decorators' => array(
        'ViewHelper',
      ),
    ));
     // DisplayGroup: buttons
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
