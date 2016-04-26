<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Composer.php 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitetagcheckin_Plugin_Composer extends Core_Plugin_Abstract {

  public function onAAFComposerCheckin($data, $params) {

    $action = (empty($params)) ? null : $params['action'];
    $getMapInfo = Engine_Api::_()->sitetagcheckin()->getMapInfo();
    if (!$action || empty($data['checkin']) || empty($getMapInfo)) {
      return;
    }

    $checkinArray = array();
    parse_str($data['checkin'], $checkinArray);
    if (empty($checkinArray) || empty($getMapInfo)) {
      return;
    }

    //GET IDENTITY
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $locationparams = $action->params = array_merge((array) $action->params, array('checkin' => $checkinArray));
    $action_id = $action->save();
    $saveCheckin = Engine_Api::_()->sitetagcheckin()->saveCheckin($checkinArray, $action, $locationparams, $viewer_id);
    $type_name = Zend_Registry::get('Zend_Translate')->translate('post');
    $activityNotificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');
    if (is_array($type_name))
      $type_name = $type_name[0];
    if (isset($checkinArray) && !empty($checkinArray['resource_guid'])) {
      $tag = Engine_Api::_()->getItemByGuid($checkinArray['resource_guid']);
      if ($tag && ($tag instanceof Sitepage_Model_Page)) {
        foreach ($tag->getPageAdmins() as $owner) {
          if ($owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
            $activityNotificationTable->addNotification($owner, $viewer, $action, 'sitetagcheckin_page_tagged', array(
                'label' => $type_name,
            ));
          }
        }
      } else if ($tag && ($tag instanceof Sitebusiness_Model_Business)) {
        foreach ($tag->getBusinessAdmins() as $owner) {
          if ($owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
            $activityNotificationTable->addNotification($owner, $viewer, $action, 'sitetagcheckin_business_tagged', array(
                'label' => $type_name,
            ));
          }
        }
      } else if ($tag && ($tag instanceof Sitegroup_Model_Group)) {
        foreach ($tag->getGroupAdmins() as $owner) {
          if ($owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
            $activityNotificationTable->addNotification($owner, $viewer, $action, 'sitetagcheckin_group_tagged', array(
                'label' => $type_name,
            ));
          }
        }
      } else if ($tag && ($tag instanceof Sitestore_Model_Store)) {
        foreach ($tag->getStoreAdmins() as $owner) {
          if ($owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
            $activityNotificationTable->addNotification($owner, $viewer, $action, 'sitetagcheckin_store_tagged', array(
                'label' => $type_name,
            ));
          }
        }
      } /*else if ($tag && ($tag instanceof Siteevent_Model_Event) {
        foreach ($tag->getEventsAdmins() as $owner) {
          if ($owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
            $activityNotificationTable->addNotification($owner, $viewer, $action, 'sitetagcheckin_event_tagged', array(
                'label' => $type_name,
            ));
          }
        }
      }*/
    }
  }

}
