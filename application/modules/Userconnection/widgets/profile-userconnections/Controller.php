<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    User Connection
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2010-07-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Userconnection_Widget_ProfileUserconnectionsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
		// Send Base URL in tpl.
		$this->view->base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
		$userconnections_profile_type =	Zend_Registry::get( 'userconnection_profile_type' );
		$viewer = Engine_Api::_()->user()->getViewer();
		$this->view->user_id = $user_id = $viewer->getIdentity();//Current user Id
		$this->view->owner_id = $owner_id = Engine_Api::_()->core()->getSubject()->getIdentity(); // Get Owner ID.
		$this->view->owner = $subject = Engine_Api::_()->core()->getSubject()->getTitle(); // Get Owner Name.			
		if( empty($userconnections_profile_type) ) {
			return $this->setNoRender();
		}

		// Get subject and check auth
		$subject = Engine_Api::_()->core()->getSubject('user');
		if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
		  return $this->setNoRender();
		}
		
		if($user_id != 0 && $user_id != $owner_id)
		{

			$getViewerConnection = Engine_Api::_()->getItemtable('userconnection')->getViewerConnection();
			if( empty($getViewerConnection) ) {
				return $this->setNoRender();
			}

			$user_level_info = Engine_Api::_()->user()->getViewer()->level_id;  	
			$permissionTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
			$select = $permissionTable->select()->where('level_id = ?', $user_level_info)->where('type = ?', 'userconnection');
			$user_level_array = $permissionTable->fetchAll($select);
			$level = 2;
			if(!empty($user_level_array))
			{
				foreach( $user_level_array as $row_user_level ):
				{
					if($row_user_level->name == "level"){
						$level = $row_user_level->params+1;}
				}
				endforeach;
			}
			
			// Fetch valule for the structure.
			$this->view->tree_structure = Engine_Api::_()->getApi('settings', 'core')->getSetting('userconnection.structure');
			// Fetch valule for the message that message will show or not.
			$admin_set_message = Engine_Api::_()->getApi('settings', 'core')->getSetting('userconnection.message');
			// Fetch valule for the message body.
      $showMsg = Engine_Api::_()->getApi('settings', 'core')->getSetting('show.msg', 'There is no connection path to this user.');
			$level_message = '<div class="tip"><span>' . $this->view->translate($showMsg) . '</span></div>';
			// Fetch valule for the indicators.
			$this->view->indicators_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('userconnection.indicators');
			// Fetch valule for the arrow.
			$this->view->arrow_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('userconnection.arrow');
			// Get User ID.
			$userconnection_combind_path_contacts_array = Engine_Api::_()->userconnection()->user_connection_path($user_id,$owner_id,$level,'profile');
			// Condition for undefined offset.
			if(!empty($userconnection_combind_path_contacts_array))
			{ 	
				$path_array = $userconnection_combind_path_contacts_array['0'];
			}
			else {
				$path_array = array();
			}
			$this->view->count_path_array = $count_path_array = count($path_array); 		
			if((!empty($count_path_array)) && ($count_path_array <= $level) && !empty($path_array[$count_path_array-1]) && ($path_array[$count_path_array-1] == $owner_id))
			{
				$this->view->path_information = $path_information = Engine_Api::_()->userconnection()->userconnection_users_information($path_array);
				//THIS FUNCTION RETURN TYPE OF FRIEND.
				$this->view->userconnection_friend_type = $userconnection_friend_type = Engine_Api::_()->userconnection()->userconnection_friend_type($path_array);
				$this->view->userconnection_depth = $userconnection_depth	= $count_path_array-1;
				if ($userconnection_depth == 1) 
				{
					$userconnection_depth_extension = $this->view->translate("st");
				}
				elseif ($userconnection_depth == 2) 
				{
					$userconnection_depth_extension = $this->view->translate("nd");
				}
				elseif ($userconnection_depth == 3) 
				{
					$userconnection_depth_extension = $this->view->translate("rd");
				}
				else 
				{
					$userconnection_depth_extension = $this->view->translate("th");
				}
				$this->view->userconnection_depth_extention = $userconnection_depth_extension;
				
			}
			elseif($admin_set_message==6){return $this->setNoRender();}
			else 
			{ 
				$this->view->level_message = $level_message;
			}
		}
		else
		{
			return $this->setNoRender();
		}
  }
}