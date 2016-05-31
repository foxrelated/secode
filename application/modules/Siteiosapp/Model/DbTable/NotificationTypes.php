<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteiosapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    NotificationType.php 2015-10-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteiosapp_Model_DbTable_NotificationTypes extends Activity_Model_DbTable_NotificationTypes {

    protected $_name = 'activity_notificationtypes';

    /**
     * All notification types
     *
     * @var Engine_Db_Table_Rowset
     */
    protected $_notificationTypes;

    /**
     * Gets all action type meta info
     *
     * @param string|null $type
     * @return Engine_Db_Rowset
     */
    public function getNotificationTypes() {
        if (null === $this->_notificationTypes) {
            // Only get enabled types
            //$this->_notificationTypes = $this->fetchAll();
            $getAvailableModules = Engine_Api::_()->getApi('Core', 'siteapi')->getAPIModulesName();
            $getEnabledModules = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
            $enabledModuleNames = array_intersect($getAvailableModules, $getEnabledModules);
            $enabledModuleNames[] = 'activity';
            $enabledModuleNames[] = 'messages';
            $enabledModuleNames[] = 'user';

            $select = $this->select()
                    ->where('module IN(?)', $enabledModuleNames)
            ;

            // Exclude disabled friend types
            $excludedTypes = array();
            $friend_verfication = (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.verification', true);
            $friend_direction = (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', true);
            if ($friend_direction) {
                $excludedTypes = array_merge($excludedTypes, array('friend_follow', 'friend_follow_accepted', 'friend_follow_request'));
            } else {
                $excludedTypes = array_merge($excludedTypes, array('friend_accepted', 'friend_request'));
            }
            if (!$friend_verfication) {
                $excludedTypes = array_merge($excludedTypes, array('friend_follow_request', 'friend_request'));
            }
            if (!empty($excludedTypes)) {
                $excludedTypes = array_unique($excludedTypes);
                $select->where('type NOT IN(?)', $excludedTypes);
            }

            // Gotta catch em' all
            $this->_notificationTypes = $this->fetchAll($select);
        }

        return $this->_notificationTypes;
    }

    public function getDefaultPushNotifications() {

        $select = $this->select()
                ->from($this->info('name'), 'type')
                ->where('`siteiosapp_enable_push` = ?', 1);

        // Exclude disabled friend types
        $excludedTypes = array();
        $friend_verfication = (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.verification', true);
        $friend_direction = (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', true);
        if ($friend_direction) {
            $excludedTypes = array_merge($excludedTypes, array('friend_follow', 'friend_follow_accepted', 'friend_follow_request'));
        } else {
            $excludedTypes = array_merge($excludedTypes, array('friend_accepted', 'friend_request'));
        }
        if (!$friend_verfication) {
            $excludedTypes = array_merge($excludedTypes, array('friend_follow_request', 'friend_request'));
        }

        if (!empty($excludedTypes)) {
            $excludedTypes = array_unique($excludedTypes);
            $select->where('type NOT IN(?)', $excludedTypes);
        }

        $types = $select
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN)
        ;

        return $types;
    }

    public function setDefaultPushNotifications($values) {
        if (!is_array($values)) {

            throw new Activity_Model_Exception('setDefaultPushNotifications requires an array of notifications');
        }

        $types = $this->select()
                ->from($this->info('name'), 'type')
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN)
        ;

        $defaults = array();
        foreach ($types as $value) {
            if (in_array($value, $values)) {
                $defaults[] = $value;
            }
        }

        if (!empty($defaults)) {

            $this->update(
                    array('siteiosapp_enable_push' => '1',), array('`type` IN(?)' => $defaults));

            $this->update(
                    array('siteiosapp_enable_push' => '0',), array('`type` NOT IN(?)' => $defaults));
        } else {
            $this->update(array('siteiosapp_enable_push' => '0'), array('`siteiosapp_enable_push`' => '1'));
        }
    }

}
