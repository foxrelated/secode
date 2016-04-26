<?php
class Spamcontrol_Form_Key extends Engine_Form
{
  public function init()
  {
    
   $this->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
      ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'))
      ;
      
      $this->addElement('text', 'spamcontrol_publickey', array(
          'label' => 'Public Key',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('spamcontrol.publickey', '')
      ));
      
      $this->addElement('text', 'spamcontrol_privatekey', array(
          'label' => 'Private Key',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('spamcontrol.privatekey', '')
      ));

   
    
    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}