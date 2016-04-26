<?php

class Ynidea_Form_AssignCoAuthor extends Engine_Form
{
  public function init()
  {   
        $this->setTitle("Assign Co-Authors")
          ->setAttrib('name', 'ynidea_assign_coauthors')
		  ->setAttrib('class', '');
        $translate = Zend_Registry::get('Zend_Translate');
                    
      //Authors
      $this->addElement('Text', 'to',array(
          'autocomplete' => 'off',
          'filters' => array(
            new Engine_Filter_Censor(),
          ),
        ));
       // Init to Values
    $this->addElement('Hidden', 'toValues', array(
      'label' => 'Co-Authors',
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
	
	$this->addElement('Hidden', 'idea_id', array(
      'value' => Zend_Controller_Front::getInstance()->getRequest()->getParam('id'),
      'order' => 2,
    ));
  
    $this->addElement('Button', 'submit', array(
    'label' => 'Assign',
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
