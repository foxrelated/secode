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
class Facebookse_Widget_FacebookseActivityController extends Engine_Content_Widget_Abstract
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
    $this->view->enable_fboldversion = $enable_fboldversion ;
    if (empty($enable_facebooksemodule)) {
			return $this->setNoRender();
    }
		$this->view->facebook_activity = $facebookse_activity = Zend_Registry::get('facebookse_activity');
		$siteurl =$_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() ; 
		//FETCHING THE CONTENT FOR THIS TYPE OF WIDGET.
		$permissionTable_widgetsetting = Engine_Api::_()->getDbtable('widgetsettings', 'facebookse');
		$select = $permissionTable_widgetsetting->select()
					->where('widget_type=?', 'activity_feed');
		$permissionTable_widgetsetting_array = $permissionTable_widgetsetting->fetchAll($select)->toarray();
		$this->view->siteurl = $siteurl;
		$this->view->permissionTable_widgetsetting_array = $permissionTable_widgetsetting_array[0];
		$this->view->activity_type = Zend_Registry::get('facebookse_activitytype');
		if (empty($facebookse_activity)) {
			return $this->setNoRender();
		}
	}
}