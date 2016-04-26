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

class Facebookse_Widget_FacebookseLikeboxController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    //CHECK IF Facebookse MODULE IS ENABLE OR NOT.WE ARE DOING THIS FOR INTEGRATING FACEBOOK LIKE TO SITE LIKE.
		$enable_facebooksemodule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse');
		$facebookse_likeboxtype = Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.likeboxtype');

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
		if(empty($facebookse_likeboxtype)) {
			return $this->setNoRender();
		}
		
		$facebook_plateform_status = Zend_Registry::get('facebookse_platform');
		$this->view->facebook_profilepage_type = Zend_Registry::get('facebookse_type');
   
		if( empty($facebook_plateform_status) ) {
			return $this->setNoRender();
		}
		if(empty($this->view->facebook_profilepage_type)) {
			return;
		}
		//FETCHING THE CONTENT FOR THIS TYPE OF WIDGET.
		$permissionTable_widgetsetting = Engine_Api::_()->getDbtable('widgetsettings', 'facebookse');
		$select = $permissionTable_widgetsetting->select()
			->where('widget_type=?', 'likebox');
		$permissionTable_widgetsetting_array = $permissionTable_widgetsetting->fetchAll($select)->toarray();
		$this->view->permissionTable_widgetsetting_array = $permissionTable_widgetsetting_array[0];		

	}
}