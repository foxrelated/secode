<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Invite.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Suggestion_Plugin_Signup_Invite extends Core_Plugin_FormSequence_Abstract
{
 	protected $_name = 'invite';
 
 	protected $_formClass = 'User_Form_Signup_Invite';
 
 	protected $_script = array('index/signupinvite.tpl', 'suggestion');
 
 	protected $_adminFormClass = 'Suggestion_Form_Admin_Signup_Invite';
 
 	protected $_adminScript = array('admin-signup/invite.tpl', 'suggestion');
 
 	protected $_skip;
 
 	public function onSubmit(Zend_Controller_Request_Abstract $request)
	{ 
	  $skip = $request->getParam("skip");
	  if ($skip != 'skipForm') {
	    $this->getSession()->active = true;
      $this->onSubmitNotIsValid();
      
      //IF THIS IS THE CASE IT MEANS THIS SUBMITED WHEN FACEBOOK INVITATION IS SENT. SO, WE HAVE TO STORE THE FACEBOOK USERS INVITED IDS IN THE SESSION.
      if ($request->get('ids')) {
        $session = new Zend_Session_Namespace();
    		$invite_sessionids = array();
    		$invite_sessionids['facebook'] = $request->get('ids');
    		
    		 if (!isset($session->suggestion_invites['facebook'])) { 
  			    $session->suggestion_invites['facebook'] = $request->get('ids');
  			  }
  			  else {
  			    $session->suggestion_invites['facebook'] = array_merge($session->suggestion_invites['facebook'], $invite_sessionids['facebook']);
  			    $session->suggestion_invites['facebook'] = array_unique($session->suggestion_invites['facebook']);
  			    
  			  }
      }
      
      return false;
	  }
	 
		parent::onSubmit($request);
	}

 
 	public function onView()
 	{
 
 	}
 
 	public function onProcess()
 	{
     // In this case, the step was placed before the account step.
     // Register a hook to this method for onUserCreateAfter
     if( !$this->_registry->user ) {
       // Register temporary hook
       Engine_Hooks_Dispatcher::getInstance()->addEvent('onUserCreateAfter', array(
         'callback' => array($this, 'onProcess'),
       ));
       return;
     }
     $user = $this->_registry->user;
     
     $data = $this->getSession()->data; 
     $form = $this->getForm();
     if( !$this->_skip && !$this->getSession()->skip ) {
       if( $form->isValid($data) ) {
         $values = $form->getValues();
         if (!empty($values['recipients'])) {
 					Engine_Api::_()->getDbtable('invites', 'invite')->sendInvites($user, @$values['recipients'], @$values['message']);
 				}
         $session = new Zend_Session_Namespace();
         if (!empty($session->suggestion_invites)) {
 					$recipients_email = array ();
          if (isset($session->suggestion_invites['google']))
            $recipients_email = $session->suggestion_invites['google'];
          if (isset($session->suggestion_invites['yahoo']))
            $recipients_email = array_merge($session->suggestion_invites['yahoo'], $recipients_email);
          if (isset($session->suggestion_invites['windowlive']))
            $recipients_email = array_merge($session->suggestion_invites['windowlive'], $recipients_email);
          if (isset($session->suggestion_invites['aol']))
            $recipients_email = array_merge($session->suggestion_invites['aol'], $recipients_email);
            
          if (isset($session->suggestion_invites['csv']))
            $recipients_email = array_merge($session->suggestion_invites['csv'], $recipients_email);
            
          $recipients_email = array_unique($recipients_email);
          if (!empty($recipients_email))        
					   $this->sendInvites($recipients_email, $user);
					   
					if (isset($session->suggestion_invites['linkedin'])) {
  					$recepients = array ();
      	    foreach ($session->suggestion_invites['linkedin'] as $friend) {
      	      
      	      $recepients[] = (string)$friend;
      	    }
      	   
      	    $Api_linkedin = Seaocore_Api_Linkedin_Api::sendInvite ($recepients, $user);
					}
					if (isset($session->suggestion_invites['twitter'])) {
  					$recepients = array ();
      	    foreach ($session->suggestion_invites['twitter'] as $friend) {
      	      
      	      $recepients[] = (string)$friend;
      	    }
      	   
      	    $Api_linkedin = Seaocore_Api_Twitter_Api::sendInvite ($recepients, $user);
					}
					
					if (isset($session->suggestion_invites['facebook'])) {
					  $facebookInvite = new Seaocore_Api_Facebook_Facebookinvite();
            $facebookInvite->seacoreInvite($session->suggestion_invites['facebook'], 'facebook', $user);
					  
					}
					
					unset($session->suggestion_invites);
 				}
       }
     }
 	}
 
 	public function onAdminProcess($form)
 	{
 		$settings = Engine_Api::_()->getApi('settings', 'core');
 
 		$step_table = Engine_Api::_()->getDbtable('signup', 'user');
 		$step_row = $step_table->fetchRow($step_table->select()->where('class = ?', 'Suggestion_Plugin_Signup_Invite'));
 		$step_row->enable = $form->getValue('enable') && ($settings->getSetting('suggestion.signup.invite') != 1);
 		$step_row->save();
 		$settings->setSetting('suggestion.signup.invite', $step_row->enable);
 		$form->addNotice('Your changes have been saved.');
 	}
 
   	public function sendInvites($recipients, $user_data)
   {
     if (!empty($user_data->user_id)) {
 			$user = $user_data;
 			$settings    = Engine_Api::_()->getApi('settings', 'core');
 			$translate   = Zend_Registry::get('Zend_Translate');
 			$message = $translate->_(Engine_Api::_()->getApi('settings', 'core')->invite_message);
 			$message     = trim($message);
 			$inviteOnlySetting = $settings->getSetting('user.signup.inviteonly', 0);
 			if (is_array($recipients) && !empty($recipients)) {
 				// Initiate objects to be used below
 				$table       = Engine_Api::_()->getDbtable('invites', 'invite');
 				$db = $table->getAdapter();
 				// Iterate through each recipient
 				//$already_members       = Engine_Api::_()->invite()->findIdsByEmail($recipients);
 				//$this->already_members = Engine_Api::_()->user()->getUserMulti($already_members);
 				foreach ($recipients as $recipient) {
 					// perform tests on each recipient before sending invite
 					$recipient = trim($recipient);
 					// watch out for poorly formatted emails
 					if (!empty($recipient)) {
 						// Passed the tests, lets start inserting database entry
 						// generate unique invite code and confirm it truly is unique
 						do {
 					$inviteCode = substr(md5(rand(0, 999) . $recipient), 10, 7);
 				} while( null !== $table->fetchRow(array('code = ?' => $inviteCode)) );
 				
 						$row = $table->createRow();
 						$row->user_id = $user->getIdentity();
 						$row->recipient = $recipient;
 						$row->code = $inviteCode;
 						$row->timestamp = new Zend_Db_Expr('NOW()');
 						$row->message = $message;
 						$row->save();
 						try {
 					
 							$coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
            	$coreversion = $coremodule->version;
            	if($coreversion < '4.1.8') {
             		$inviteUrl = ( _ENGINE_SSL ? 'https://' : 'http://' )
                      . $_SERVER['HTTP_HOST']
                      . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                          'module' => 'invite',
                          'controller' => 'signup',
                              ), 'default', true)
                      . '?'
                      . http_build_query(array('code' => $inviteCode, 'email' => $recipient))
                ;
            	} 
            	else {
           			$inviteUrl = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                          'module' => 'invite',
                          'controller' => 'signup',
                              ), 'default', true)
                      . '?'
                      . http_build_query(array('code' => $inviteCode, 'email' => $recipient))
                ;
            	}
 
 							$message = str_replace('%invite_url%', $inviteUrl, $message);
 						
 							// Send mail
 							$mailType = ( $inviteOnlySetting == 2 ? 'invite_code' : 'invite' );
 							$mailParams = array(
 								'host' => $_SERVER['HTTP_HOST'],
 								'email' => $recipient,
 								'date' => time(),
 								'sender_email' => $user->email,
 								'sender_title' => $user->getTitle(),
 								'sender_link' => $user->getHref(),
 								'sender_photo' => $user->getPhotoUrl('thumb.icon'),
 								'message' => $message,
 								'object_link' => $inviteUrl,
 								'code' => $inviteCode,
 								'queue' => true
 							);
 
 							Engine_Api::_()->getApi('mail', 'core')->sendSystem(
 								$recipient,
 								$mailType,
 								$mailParams
 							);
 							$db->commit();
 						} catch( Exception $e ) {
 								// Silence
 								if( APPLICATION_ENV == 'development' ) {
 									throw $e;
 								}
 								continue;
 							}
 					} // end if (!array_key_exists($recipient, $already_members))
 				} // end foreach ($recipients as $recipient)
 			} // end if (is_array($recipients) && !empty($recipients))
 			$user->save();
 			return ;
 		} // end public function sendInvites()
   }
}