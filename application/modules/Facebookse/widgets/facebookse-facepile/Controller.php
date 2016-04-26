<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Facebookse_Widget_FacebookseFacepileController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    //CHECK IF Facebookse MODULE IS ENABLE OR NOT.WE ARE DOING THIS FOR INTEGRATING FACEBOOK LIKE TO SITE LIKE.
		$enable_facebooksemodule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse');
     
    $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('socialdna');
		if (!empty($enable_fboldversion)) {
			$socialdnamodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('socialdna');
			$socialdnaversion = $socialdnamodule->version;
			if ($socialdnaversion >= '4.1.1') {
				$enable_fboldversion = 0;
			}
		}
    $this->view->enable_fboldversion = $enable_fboldversion;

    if (empty($enable_facebooksemodule)) {
			return $this->setNoRender();
    }
    $facebookInvite = new Seaocore_Api_Facebook_Facebookinvite();
    $facebook = $facebookInvite->getFBInstance();
		if ($facebook && $facebook->getUser()) {
			$session['uid'] = $facebook->getUser();	
   }
   else {
		$session = '';
   }
	 $this->view->fblogin = 1;
		if ($session) {
			try {
			 
				$uid = $facebook->getUser();
				$logged_user = $facebook->api('/me');
			} catch (Exception $e) {
			   $this->view->fblogin = 0;
			   
     	}
		}
		else {
			$this->view->fblogin = 0;
		}	 
		// Create our Application instance.
		$this->view->facepile = $facepile = Zend_Registry::get('facebookse_facepile');
		//FETCHING THE CONTENT FOR THIS TYPE OF WIDGET.
		$permissionTable_widgetsetting = Engine_Api::_()->getDbtable('widgetsettings', 'facebookse');
		$select = $permissionTable_widgetsetting->select()
					->where('widget_type=?', 'facepile');
		$permissionTable_widgetsetting_array = $permissionTable_widgetsetting->fetchAll($select)->toarray();
		$this->view->permissionTable_widgetsetting_array = $permissionTable_widgetsetting_array[0];
		if(empty($facepile)) {
			return $this->setNoRender();
		}
  }
}
?>
