<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Poke
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: AdminPokeusersController.php 2010-11-27 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
class Poke_PokeusersController extends Core_Controller_Action_Standard
{
	public function deletepokeAction()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		//Getting the poke entry which we want to delete.
		$poke_id = (int) $this->_getParam('pokedelete_id');
	  $poke_receiver_id = (int) $this->_getParam('poke_receiverid');
		//Getting the poke table.
		$table = Engine_Api::_()->getDbtable('pokeusers', 'poke');
		$table->update(array('isexpire'=> 2, 'isdeleted'=> 2), array('pokeuser_id = ?' => $poke_id));
		//notification delete
		Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('user_id = ?' => $viewer->user_id, 'type = ?' => 'Poke', 'object_id = ?' => $poke_receiver_id));
	}
	
	public function pokeuserAction()
  {

  	$site_title = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title;
		//Rendering the form.
    if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
			$this->view->form = $form = new Poke_Form_Pokeusers_Pokeuser();
		}
    else {
     $this->view->form = $form = new Poke_Form_Sitemobile_Pokeusers_Pokeuser();
    }

		//Getting the logged user information.
		$viewer = Engine_Api::_()->user()->getViewer();
		
		//Getting the username of the viewer.
		//$displayname = $viewer->displayname;
		
		//Getting the userid of the viewer.
		$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		
		//Getting the pokeuser id which user is poked by logged user.
		$poke_userid = $this->_getparam('pokeuser_id'); 
		
		//Getting the poke table.
		
		$table = Engine_Api::_()->getDbtable('pokeusers', 'poke');
		$select = $table->select()
						->where("(userid = $poke_userid AND resourceid = $viewer_id)  OR (userid = $viewer_id AND resourceid = $poke_userid)")
						->where("isdeleted=1")
						;
		//Fetching the row from the poke table.				
		$row = $table->fetchRow($select);
		//Getting the allinformaiton of the owner.
    $subject = Engine_Api::_()->getItem('user', $poke_userid);		
    if(!empty($row)) {
			$autoid = $row->pokeuser_id;
			//Userid means viewer id.
			$pokeviewerid = $row->userid;
			
			//Resource id means the logged in user id.
			$resourceid = $row->resourceid;
    }
		
		//Getting the information of the poked member
		$user = Engine_Api::_()->getItem('user', $poke_userid);
		
		//Making view to getting the photo of the poked user.
   	$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
			$userphoto = $view->itemPhoto($user, 'thumb.profile', $user->getTitle());
		} else {
      $userphoto = '<div class="fleft" style="margin-right:5px;">' . $view->itemPhoto($user, 'thumb.icon', $user->getTitle()) . '</div>';
    }
		//Here we inserting the row if somebody poke to the other user.
		if($row === null) {
			if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
				$values = $form->getValues();
			  $table = Engine_Api::_()->getDbTable('pokeusers', 'poke');
				$row = $table->createRow();
				$row->resourceid = $viewer_id;		
				$row->userid = $values['pokeuser_id'];
				$row->isdeleted = 1;
				$row->isexpire = 1;
				$row->created = time();
				$poke_ids = $row->save();
				$translate = Zend_Registry::get('Zend_Translate');
				$this->view->error = 2;
						
				Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $user, 'Poke', '{item:$subject} poked {item:$object}.');

				//Here showing the option of the update.Admin want to show or not.
				if(Engine_Api::_()->getApi('settings', 'core')->poke_updateoption) {	
					Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $viewer, 'Poke');
					Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('user_id = ?' => $viewer->user_id, 'type = ?' => 'Poke', 'object_id = ?' => $poke_userid));
				}
				
				$host = $_SERVER['HTTP_HOST'];
				$link =  Zend_Controller_Front::getInstance()->getBaseUrl() . '/members/home/';
				$created = time();
// 				$email_allow = Engine_Api::_()->getDbtable('notificationSettings', 'activity');
// 			  $select_user_id = $email_allow->select()
// 		  			->where("`user_id` = $poke_userid")
// 		  			->limit(1);	  
// 			  
// 			  $row_email = $email_allow->fetchRow($select_user_id);
// 			  if($row_email === null) {
					if(Engine_Api::_()->getApi('settings', 'core')->poke_mailoption) {
			      $email = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;
			      Engine_Api::_()->getApi('mail', 'core')->sendSystem($user->email, 'Poke_User_Email', array(
			              'site_title' => $site_title,
			              'sender_title' => $viewer->getTitle(),
			              'date' => date("d M Y", $created),
			              'host' => $host,
			              'object_link' => $link, 
			              'email' => $email,
			              'queue' => false
			      ));	
			    }	
				//}
				
				$this->_forward('success', 'utility', 'core', array(
		      'smoothboxClose' => 500,
		      //'parentRefresh'=> 500,
		      'messages' => array($this->view->translate("You have successfully poked %s.", $subject->getTitle()))
		    ));							
			}
		} 
		//Here we updating the row if somebody poke to the other user.
		else if( $pokeviewerid == $viewer_id) {
			if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
				$values = $form->getValues();
				$table = Engine_Api::_()->getDbTable('pokeusers', 'poke');
			  $table->update(array('isexpire' => 2, 'isdeleted' => 2), array('resourceid = ?' => $values['pokeuser_id'], 'userid = ?' => $viewer_id, 'isdeleted = ?' => 1));
				$table = Engine_Api::_()->getDbTable('pokeusers', 'poke');
				$row = $table->createRow();
				$row->resourceid = $viewer_id;		
				$row->userid = $values['pokeuser_id'];
				$row->isdeleted = 1;
				$row->isexpire = 1;
				$row->created = time();
				$poke_ids = $row->save();
				$translate = Zend_Registry::get('Zend_Translate');
				$this->view->error = 2;
				$host =  $_SERVER['HTTP_HOST'];
				$link =  Zend_Controller_Front::getInstance()->getBaseUrl() . '/members/home/';
				$sender_title = $subject->getTitle();
				$created = time();
// 				$email_allow = Engine_Api::_()->getDbtable('notificationSettings', 'activity');
// 			  $select_user_id = $email_allow->select()
// 		  			->where("`user_id` = $poke_userid")
// 		  			->limit(1);	  
// 			  $row_email = $email_allow->fetchRow($select_user_id);
// 			  if($row_email === null) {
					if(Engine_Api::_()->getApi('settings', 'core')->poke_mailoption) {
						$email = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;
				      Engine_Api::_()->getApi('mail', 'core')->sendSystem($user->email, 'Poke_User_Email', array(
			              'site_title' => $site_title,
			              'sender_title' => $viewer->getTitle(),
			              'date' => date("d M Y", $created),
			              'host' => $host,
			              'object_link' => $link, 
			              'email' => $email,
			              'queue' => false
			      ));	
			    }	
				//}
				
				Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $user, 'Poke', '{item:$subject} poked {item:$object}.');

        //Here showing the option of the update.Admin want to show or not.
			  if(Engine_Api::_()->getApi('settings', 'core')->poke_updateoption) {	
					Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $viewer, 'Poke');
					Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('user_id = ?' => $viewer->user_id, 'type = ?' => 'Poke', 'object_id = ?' => $poke_userid));
				}
				$this->_forward('success', 'utility', 'core', array(
		      'smoothboxClose' => 500,
		     // 'parentRefresh'=> 500,
		      'messages' => array($this->view->translate("You have successfully poked %s.", $user->displayname))
		    ));							
			}
		}
		//Here we checking the row if someone poke the user.and he want to poke again the user.
		else {
      if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
				$this->view->error = 1;
				$alert = Zend_Registry::get('Zend_Translate')->_("<div class='global_form'><div><div><h3>Alert</h3>");
				$this->view->message = $alert;
				$poked_message = Zend_Registry::get('Zend_Translate')->_("<div class='form-elements'><div class='form-wrapper'>%s You have already poked %s. %s has not yet responded back to your poke.</div>");
				$poked_message = sprintf($poked_message, $userphoto, $user->displayname, $user->displayname);    
				$this->view->message .= $poked_message;
				$okay = Zend_Registry::get('Zend_Translate')->_("<div class='form-wrapper'><div class='form-element' style='float:right;text-align:right;'><button onclick='history.go(-1); return false;'>Okay</button></div></div></div></div></div>");
				$this->view->message .= $okay;				
      } else {
				$this->view->error = 1;
				$alert = Zend_Registry::get('Zend_Translate')->_("<div class='global_form'><div><div><h3>Alert</h3>");
				$this->view->message = $alert;
				$poked_message = Zend_Registry::get('Zend_Translate')->_("<div class='form-elements'><div class='form-wrapper'><div style='vertical-align:top;'>%s You have already poked %s. %s has not yet responded back to your poke.</div></div>");
				$poked_message = sprintf($poked_message, $userphoto, $user->displayname, $user->displayname);    
				$this->view->message .= $poked_message;
				$okay = Zend_Registry::get('Zend_Translate')->_("<div class='form-wrapper'><div class='form-element' style='float:right;text-align:right;'><button onclick='history.go(-1); return false;'>Okay</button></div></div></div></div></div>");
				$this->view->message .= $okay;	
      }
	  }
	}
	
	public function cancelpokeAction()
	{
		//Getting the logged in user information.
		$viewer = Engine_Api::_()->user()->getViewer();
	  //Getting the poke entry which we want to delete.
		$poke_id = (int) $this->_getParam('pokedelete_id');
		$poke_receiver_id = (int) $this->_getParam('poke_receiverid');
		// $display_sugg_str = (string) $this->_getParam('displayed_sugg');
		//Getting the poke table.
		$table = Engine_Api::_()->getDbtable('pokeusers', 'poke');
		$table->update(array('isexpire' =>2, 'isdeleted' =>2), array('pokeuser_id = ?' => $poke_id));
		//notification delete
		Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('user_id = ?' => $viewer->user_id, 'type = ?' => 'Poke', 'object_id = ?' => $poke_receiver_id));
		$this->_forward('success', 'utility', 'core', array(
			      'smoothboxClose' => 500,
			      'parentRefresh'=> 500,
			      'messages' => ''
			    ));							
	}
}
?>