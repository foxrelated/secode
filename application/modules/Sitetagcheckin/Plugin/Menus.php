<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitetagcheckin
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 6590 2010-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitetagcheckin_Plugin_Menus {

  //SHOWING THE EDIT LINK ON THE LEFT SITE MENU ON EVENT VIEW PAGE
  public function onMenuInitialize_SitetagcheckinGutterEditlocation($row) {
    
    //GETTING THE VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //GETTING THE SITETAGCHECKIN SUBJECT
    $subject = Engine_Api::_()->core()->getSubject();
    if ($viewer->getIdentity() == $subject->user_id) {
			//Return EDIT LINK
			return array(
					'label' => Zend_Registry::get('Zend_Translate')->_('Edit Event Location'),
					'icon' => 'application/modules/Sitetagcheckin/externals/images/icon/map-edit.png',
					'route' => 'sitetagcheckin_specific',
					'params' => array(
							'controller' => 'index',
							'action' => 'edit-location',
							'seao_locationid' => $subject->seao_locationid,
							'event_id' => $subject->getIdentity(),
							'resource_type' => 'event',
					)
			);
    }
  }
  
  //SHOWING THE EDIT LINK ON THE LEFT SITE MENU ON EVENT VIEW PAGE
  public function onMenuInitialize_SitetagcheckinGutterGroupeditlocation($row) {
    
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.groupsettings', 0)) {
      return false;
    }
    
    //GETTING THE VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //GETTING THE SITETAGCHECKIN SUBJECT
    $subject = Engine_Api::_()->core()->getSubject();

    if ($viewer->getIdentity() == $subject->user_id) {
			//Return EDIT LINK
			return array(
					'label' => Zend_Registry::get('Zend_Translate')->_('Edit Group Location'),
					'icon' => 'application/modules/Sitetagcheckin/externals/images/icon/map-edit.png',
					'route' => 'sitetagcheckin_groupspecific',
					'params' => array(
						'controller' => 'location',
						'action' => 'edit-location',
						'seao_locationid' => $subject->seao_locationid,
						'group_id' => $subject->getIdentity(),
						'resource_type' => 'group',
					)
			);
    }
  }
  
    //SHOWING THE EDIT LINK ON THE LEFT SITE MENU ON EVENT VIEW PAGE
  public function onMenuInitialize_SitetagcheckinGutterAdvgroupeditlocation($row) {
    
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.groupsettings', 0)) {
      return false;
    }

    //GETTING THE VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //GETTING THE SITETAGCHECKIN SUBJECT
    $subject = Engine_Api::_()->core()->getSubject();

    if ($viewer->getIdentity() == $subject->user_id) {
			//Return EDIT LINK
			return array(
					'label' => Zend_Registry::get('Zend_Translate')->_('Edit Group Location'),
					'icon' => 'application/modules/Sitetagcheckin/externals/images/icon/map-edit.png',
					'route' => 'sitetagcheckin_groupspecific',
					'params' => array(
						'controller' => 'location',
						'action' => 'edit-location',
						'seao_locationid' => $subject->seao_locationid,
						'group_id' => $subject->getIdentity(),
						'resource_type' => 'group',
					)
			);
    }
  }
  
  //SHOWING THE EDIT LINK ON THE LEFT SITE MENU ON EVENT VIEW PAGE
  public function onMenuInitialize_SitetagcheckinGutterUsereditlocation($row) {
  
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.usersettings', 0)) {
      return false;
    }
    
    //GETTING THE VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //GETTING THE SITETAGCHECKIN SUBJECT
    $subject = Engine_Api::_()->core()->getSubject();

    if ($viewer->getIdentity() == $subject->user_id) {
			//Return EDIT LINK
			return array(
					'label' => Zend_Registry::get('Zend_Translate')->_('Edit My Location'),
					'icon' => 'application/modules/Sitetagcheckin/externals/images/icon/map-edit.png',
					'route' => 'sitetagcheckin_userspecific',
					'params' => array(
						'controller' => 'location',
						'action' => 'edit-location',
						'seao_locationid' => $subject->seao_locationid,
						'user_id' => $subject->getIdentity(),
						'resource_type' => 'user',
					)
			);
    }
  }
}