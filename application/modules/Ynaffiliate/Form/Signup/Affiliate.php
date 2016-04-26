<?php


class Ynaffiliate_Form_Signup_Affiliate extends Engine_Form
{
  public $invalid_emails = array();
  public $already_members = array();
  public $emails_sent = 0;
  
 
  
  public function init()
  {
    $this
      ->setAttrib('enctype', 'multipart/form-data')
      ->setAttrib('id', 'SignupForm');	
	
    // Init settings object
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $translate = Zend_Registry::get('Zend_Translate');
	
	
    // Init form
    $this
      ->setTitle('Your Friends')
      ->setDescription('YNAFFILIATE_AFFILIATE_FORM_DESCRIPTION')
      ->setLegend('');

    $this->addElement('Hidden', 'nextStep', array(
      'order' => 3
    ));

    $this->addElement('Hidden', 'skip', array(
      'order' => 4
    ));

    // Element: done
    $this->addElement('Button', 'done', array(
      'label' => 'Continue',
      'type' => 'submit',
      'onclick' => 'javascript:finishForm();',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    // Element: skip
    $this->addElement('Cancel', 'skip-link', array(
      'label' => 'skip',
      'prependText' => ' or ',
      'link' => true,
      'href' => 'javascript:void(0);',
      'onclick' => 'skipForm(); return false;',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    // DisplayGroup: buttons
    $this->addDisplayGroup(array('done', 'skip-link'), 'buttons', array(

    ));
  }
  
}
