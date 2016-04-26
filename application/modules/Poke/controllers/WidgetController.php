<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Poke
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: WidgetController.php 2010-11-27 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
class Poke_WidgetController extends Core_Controller_Action_Standard
{
	// Call from "notification page" if loggden user get Poke.
  public function userpokeAction()
  {
  	//Getting the logged in user information.
  	$viewer = Engine_Api::_()->user()->getViewer();
  	$this->view->notification = $notification = $this->_getParam('notification')->object_id;
  	$sender_name = Engine_Api::_()->getItem('user', $this->view->notification);
 		$this->view->sender_id   = $sender_id = $sender_name->user_id;
  	$this->view->sendername = $sender_name;
  	$table = Engine_Api::_()->getDbtable('pokeusers', 'poke');
		$select = $table->select()
							->where("(userid = $sender_id AND resourceid = $viewer->user_id)  OR (userid = $viewer->user_id AND resourceid = $sender_id)")
							->where("isdeleted = 1")
							->limit(1);
		$row = $table->fetchRow($select); 
  	$poke_id = $row->pokeuser_id;
  	$this->view->poke_id = $poke_id;
  	$this->view->viewer_user_id = $viewer->user_id;
  }
}
?>