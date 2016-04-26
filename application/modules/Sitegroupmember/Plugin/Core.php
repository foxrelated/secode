<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupmember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitegroupmember_Plugin_Core extends Zend_Controller_Plugin_Abstract {

  public function onActivityActionCreateAfter($event) {

		$payload = $event->getPayload();
		
    if ($payload->object_type == 'sitegroup_group' && ($payload->getTypeInfo()->type == 'sitegroup_post_self' || $payload->getTypeInfo()->type == 'sitegroup_post') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
		
			$db = Zend_Db_Table_Abstract::getDefaultAdapter();
			$viewer = Engine_Api::_()->user()->getViewer();
			$friendId = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();

			$group_id = $payload->getObject()->group_id;
			$subject = Engine_Api::_()->getItem('sitegroup_group', $group_id);
			
      //previous notification is delete posted by same user.
			Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('type =?' => "sitegroupmember_notificationpost", 'object_type = ?' => "sitegroup_group", 'object_id = ?' => $subject->getIdentity(), 'subject_id = ?' => $viewer->getIdentity()));
      $notificationposted = '%"notificationposted":"1"%';
      $notificationfriendposted = '%"notificationposted":"2"%';
			if (!empty($friendId)) {
				$db->query("INSERT IGNORE INTO `engine4_activity_notifications` (`user_id`, `subject_type`, `subject_id`, `object_type`, `object_id`, `type`,`params`, `date`) SELECT `engine4_sitegroup_membership`.`user_id` as `user_id` ,	'". $viewer->getType() ."' as `subject_type`, " . $viewer->getIdentity() . " as `subject_id`, '" . $subject->getType() . "' as `object_type`, " . $subject->getIdentity() . " as `object_id`, 'sitegroupmember_notificationpost' as `type`, 'null' as `params`, '" . date('Y-m-d H:i:s') . "' as ` date `  FROM `engine4_sitegroup_membership` WHERE (engine4_sitegroup_membership.group_id = ".$subject->group_id.") AND (engine4_sitegroup_membership.user_id <> ".$viewer->getIdentity().") AND (engine4_sitegroup_membership.notification = 1) AND (engine4_sitegroup_membership.action_notification LIKE '".$notificationposted."' or (engine4_sitegroup_membership.action_notification LIKE '".$notificationfriendposted."' and (engine4_sitegroup_membership .user_id IN (".join(",",$friendId)."))))");
			} else {
				$db->query("INSERT IGNORE INTO `engine4_activity_notifications` (`user_id`, `subject_type`, `subject_id`, `object_type`, `object_id`, `type`,`params`, `date`) SELECT `engine4_sitegroup_membership`.`user_id` as `user_id` ,	'". $viewer->getType() ."' as `subject_type`, " . $viewer->getIdentity() . " as `subject_id`, '" . $subject->getType() . "' as `object_type`, " . $subject->getIdentity() . " as `object_id`, 'sitegroupmember_notificationpost' as `type`, 'null' as `params`, '" . date('Y-m-d H:i:s') . "' as ` date `  FROM `engine4_sitegroup_membership` WHERE (engine4_sitegroup_membership.group_id = ".$subject->group_id.") AND (engine4_sitegroup_membership.user_id <> ".$viewer->getIdentity().") AND (engine4_sitegroup_membership.notification = 1) AND (engine4_sitegroup_membership.action_notification LIKE '".$notificationposted."')");
			}
		}
  }
}