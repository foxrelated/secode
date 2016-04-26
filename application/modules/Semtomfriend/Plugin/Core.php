<?php

class Semtomfriend_Plugin_Core
{
  
  // user creation
  public function onUserSignupAfter($event)
  {

    $payload = $event->getPayload();

    if( !$payload instanceof User_Model_User ) {
      return;
    }
    
    // friending  
    $this->befriend($payload);
    
    // welcoming
    $this->welcomeMessage($payload);

    
  }
 
  
  public function befriend($user) {

    if(Semods_Utils::getSetting('semtomfriend.tom.enabled',0) == 0) {
      return;
    }

    if(!Engine_Api::_()->getApi('settings', 'core')->user_friends_eligible) {
      return;
    }

    $befriendUserIds = explode(',', Semods_Utils::getSetting('semtomfriend.tom.befriend',''));
    if(empty($befriendUserIds)) {
      return;
    }

    $befriendUsers = array();
    foreach($befriendUserIds as $befriendUserId) {

      $befriendUser = Engine_Api::_()->user()->getUser($befriendUserId);

      if( ($user instanceof User_Model_User) && $user->getIdentity() ) {
        $befriendUsers[] = $befriendUser;
      }

    }

    foreach( $befriendUsers as $befriendUser ) {

      try {

      $user->membership()->addMember($befriendUser)->setUserApproved($befriendUser);
      
      // auto-approve
      //$befriendUser->membership()->setResourceApproved($user);
      
      $this->_handleNotification($user, $befriendUser);
      
      } catch(Exception $e) {
      }
      
    }
    
  }

  public function welcomeMessage($user) {

    if(Semods_Utils::getSetting('semtomfriend.wem.enabled',0) == 0) {
      return;
    }
    
    $from = Semods_Utils::getSetting('semtomfriend.wem.from',0);
    $from_user = Engine_Api::_()->user()->getUser($from);

    if( (!$from_user instanceof User_Model_User) || !$from_user->getIdentity() ) {
      return;
    }
    
    //$subject = Semods_Utils::getSetting('semtomfriend.wem.subject');
    //$message = Semods_Utils::getSetting('semtomfriend.wem.message');

    $subject = Engine_Api::_()->getDbTable('semtomfriend', 'semtomfriend')->getSetting('semtomfriend.wem.subject', Engine_Api::_()->getApi('settings', 'core')->getSetting('semtomfriend.wem.subject', ''));
    $message = Engine_Api::_()->getDbTable('semtomfriend', 'semtomfriend')->getSetting('semtomfriend.wem.message', Engine_Api::_()->getApi('settings', 'core')->getSetting('semtomfriend.wem.message', ''));


    $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
    $db->beginTransaction();

    try {
      
      // Create conversation
      $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send(
        $from_user,
        $user,
        $subject,
        $message
      );

      Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
        $user,
        $from_user,
        $conversation,
        'message_new'
      );

      // Increment messages counter
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

      // Commit
      $db->commit();
      
    } catch( Exception $e ) {

      // silence
      
    }    
    
    
  }




  // Invite_Plugin_Signup
  public function _handleNotification($user, $befriendUser){
    // if one way friendship and verification not required
    if(!$user->membership()->isUserApprovalRequired()&&!$user->membership()->isReciprocal()){
      // Add activity
      Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($befriendUser, $user, 'friends_follow', '{item:$object} is now following {item:$subject}.');

      // Add notification
      Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $befriendUser, $befriendUser, 'friend_follow');

      $message = Zend_Registry::get('Zend_Translate')->_("You are now following this member.");
    }

    // if two way friendship and verification not required
    else if(!$user->membership()->isUserApprovalRequired()&&$user->membership()->isReciprocal()){
      // Add activity
      Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $befriendUser, 'friends', '{item:$object} is now friends with {item:$subject}.');
      Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($befriendUser, $user, 'friends', '{item:$object} is now friends with {item:$subject}.');

      // Add notification
      Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $befriendUser, $user, 'friend_accepted');
      $message = Zend_Registry::get('Zend_Translate')->_("You are now friends with this member.");
    }

    // if one way friendship and verification required
    else if(!$user->membership()->isReciprocal()){
      // Add notification
      Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $befriendUser, $user, 'friend_follow_request');
      $message = Zend_Registry::get('Zend_Translate')->_("Your friend request has been sent.");
    }

    // if two way friendship and verification required
    else if($user->membership()->isReciprocal())
    {
      // Add notification
      Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $befriendUser, $user, 'friend_request');
      $message = Zend_Registry::get('Zend_Translate')->_("Your friend request has been sent.");
    }

  }
    
}