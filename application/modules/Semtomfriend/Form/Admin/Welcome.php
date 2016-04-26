<?php

class Semtomfriend_Form_Admin_Welcome extends Engine_Form
{

  public function init()
  {
    
    $this
      ->setTitle('Welcome Message')
      ->setDescription('Automatically send a welcome message to new site members.');


    $this->addElement('Radio', 'wem_enabled', array(
      'label' => 'Welcome Message',
      'description' => "Would you like to automatically send a welcome message to new members?",
      'multiOptions' => array(
        1 => 'Yes, enable welcome message.',
        0 => 'No, disable welcome message.',
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('semtomfriend.wem.enabled', 0)
    ));

    $this->addElement('Text', 'wem_from', array(
      'label' => 'From',
      'description' => "The user message will be sent from (for example admin). Type in username or user ID",
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('semtomfriend.wem.from', '')
    ));

    $this->addElement('Text', 'wem_subject', array(
      'label' => 'Subject',
      //'description' => "Message Subject",
      //'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('semtomfriend.wem.subject', '')
      'value' => Engine_Api::_()->getDbTable('semtomfriend', 'semtomfriend')->getSetting('semtomfriend.wem.subject', Engine_Api::_()->getApi('settings', 'core')->getSetting('semtomfriend.wem.subject', ''))
    ));

    $this->addElement('Textarea', 'wem_message', array(
      'label' => 'Body',
      //'description' => "Message Body",
      //'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('semtomfriend.wem.message', ''),
      'value' => Engine_Api::_()->getDbTable('semtomfriend', 'semtomfriend')->getSetting('semtomfriend.wem.message', Engine_Api::_()->getApi('settings', 'core')->getSetting('semtomfriend.wem.message', '')),
      'filters' => array(
        //new Engine_Filter_HtmlSpecialChars(),
        //new Engine_Filter_Censor(),
        //new Engine_Filter_EnableLinks(),
      ),
    ));


    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    ));
    
  }
  
  
  public function saveAdminSettings()
  {

    $values = $this->getValues();
    
    // check from user
    if($values['wem_enabled']) {
      $from_user = Engine_Api::_()->user()->getUser($values['wem_from']);
  
      if( (!$from_user instanceof User_Model_User) || !$from_user->getIdentity() ) {
        $this->addError('The From user is invalid.');
        return;
      }
    }
    
    // Save settings
    //Engine_Api::_()->getApi('settings', 'core')->semtomfriend = $this->getValues();

    Engine_Api::_()->getApi('settings', 'core')->setSetting('semtomfriend.wem.enabled', $values['wem_enabled']);

    Engine_Api::_()->getDbTable('semtomfriend', 'semtomfriend')->setSetting('semtomfriend.wem.subject', $values['wem_subject']);
    Engine_Api::_()->getDbTable('semtomfriend', 'semtomfriend')->setSetting('semtomfriend.wem.message', $values['wem_message']);
    
    Engine_Api::_()->getApi('settings', 'core')->setSetting('semtomfriend.wem.from', $values['wem_from']);
    
    Engine_Api::_()->getApi('settings', 'core')->reloadSettings();

    $this->addNotice('Settings were successfully saved.');
    
  }
  
  
}