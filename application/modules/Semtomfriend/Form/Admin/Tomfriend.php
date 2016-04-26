<?php

class Semtomfriend_Form_Admin_Tomfriend extends Engine_Form
{

  public function init()
  {
    
    $this
      ->setTitle('Tom Friend')
      ->setDescription('Autofriend new users.');


    $this->addElement('Radio', 'tom_enabled', array(
      'label' => 'Autofriend',
      'description' => "Would you like to automatically add selected friends to new members?",
      'multiOptions' => array(
        1 => 'Yes, enable friends on signup.',
        0 => 'No, disable friends on signup.',
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('semtomfriend.tom.enabled', 0)
    ));

    $this->addElement('Text', 'tom_befriend', array(
      'label' => 'Friends',
      'description' => "Select friends you would like the new member to have after signup. Type in usernames or user ID's, separated with a comma (,).",
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('semtomfriend.tom.befriend', '')
    ));
    //$this->tom_befriend->getDecorator('Description')->setOption('placement', 'append');


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

    // check friend user
    if($values['tom_enabled']) {
      
      $befriendUsers = explode(',', $values['tom_befriend']);
      if( empty($befriendUsers) ) {
        $this->addError("Friend Users are wrong or not found.");
        return;
      }
      
      foreach($befriendUsers as $befriendUser) {

        $user = Engine_Api::_()->user()->getUser($befriendUser);

        if( (!$user instanceof User_Model_User) || !$user->getIdentity() ) {
          $message = sprintf("The user %s is invalid", $befriendUser);
          $this->addError($message);
          return;
        }

      }
      
      //$befriendUsers = Engine_Api::_()->getItemTable('user')->find($befriendUserIds);
      //if( count($befriendUsers) == 0) {
      //  $this->addError("Friend Users are wrong or not found.");
      //  return;
      //}
      
    }


    // Save settings
    Engine_Api::_()->getApi('settings', 'core')->semtomfriend = $this->getValues();

    Engine_Api::_()->getApi('settings', 'core')->reloadSettings();

    $this->addNotice('Settings were successfully saved.');
    
  }
  
  
}