<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Updates.php 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitemenu_Model_DbTable_Updates extends Engine_Db_Table {

    /**
     * Get a paginator for friend-request or friend-follow request
     *
     * @param User_Model_User $user
     * @return Zend_Paginator
     */
    public function getRequestsPaginator(User_Model_User $user) {
        $notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');
        $notificationTableName = $notificationTable->info('name');

        $notificationTypeTable = Engine_Api::_()->getDbtable('notificationTypes', 'activity');
        $notificationTypeTableName = $notificationTypeTable->info('name');

        $select = $notificationTable->select()
                ->from($notificationTableName)
                ->join($notificationTypeTableName, $notificationTypeTableName . '.type = ' . $notificationTableName . '.type', null)
                ->where('module = ?', 'user')
                ->where('user_id = ?', $user->getIdentity())
                ->where('is_request = ?', 1)
                ->where('mitigated = ?', 0)
                ->where("$notificationTypeTableName.type = 'friend_request' OR $notificationTypeTableName.type = 'friend_follow_request'")
                ->order('date DESC');

        return Zend_Paginator::factory($select);
    }

    /**
     * Get a paginator for notifications
     *
     * @param User_Model_User $user
     * @return Zend_Paginator
     */
    public function getNotificationsPaginatorSql(User_Model_User $user) {
        $notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');
        $notificationTypeTable = Engine_Api::_()->getDbtable('notificationTypes', 'activity');

        $enabledNotificationTypes = array();
        foreach ($notificationTypeTable->getNotificationTypes() as $type) {
            $enabledNotificationTypes[] = $type->type;
        }

        $messageKey = array_search('message_new', $enabledNotificationTypes);
        $friendRequestKey = array_search('friend_request', $enabledNotificationTypes);
        $friendFollowRequestKey = array_search('friend_follow_request', $enabledNotificationTypes);


        if (!empty($messageKey))
            unset($enabledNotificationTypes[$messageKey]);

        if (!empty($friendRequestKey))
            unset($enabledNotificationTypes[$friendRequestKey]);

        if (!empty($friendFollowRequestKey))
            unset($enabledNotificationTypes[$friendFollowRequestKey]);

        $select = $notificationTable->select()
                ->where('user_id = ?', $user->getIdentity())
                ->where('`type` IN(?)', $enabledNotificationTypes)
                ->order('date DESC')
                ->limit(100);

        return $select;
    }

    /**
     * Does the user have new updates, returns the number or 0
     *
     * @param User_Model_User $user
     * @param $params
     * @return int The number of new updates the user has
     */
    public function getNewUpdatesCount(User_Model_User $user, $params = array()) {
        $notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');
        $notificationTableName = $notificationTable->info('name');

        $select = $notificationTable->select()
                ->from($notificationTableName, "COUNT(notification_id)")
                ->where("$notificationTableName.user_id = ?", $user->getIdentity())
                ->where("$notificationTableName.show = ?", 0)
                ->where("$notificationTableName.read = ?", 0);

        if (isset($params['isNotification']) && !empty($params['isNotification']))
            $select->where("$notificationTableName.type != 'friend_request'")
                    ->where("$notificationTableName.type != 'message_new'")
                    ->where("$notificationTableName.type != 'friend_follow_request'");
        elseif (isset($params['type']) && !empty($params['type'])) {
            $notificationType = $params['type'];
            $select->where("$notificationTableName.type = 'friend_follow_request' OR $notificationTableName.type = '" . $notificationType . "'");
        }

        $newUpdatesCount = $select->query()->fetchColumn();
        return empty($newUpdatesCount) ? false : $newUpdatesCount;
    }

    /**
     * Does the user have new messages, returns the number or 0
     *
     * @param User_Model_User $user
     * @return int The number of new messages the user has
     */
    public function getUnreadMessageCount(User_Model_User $user) {
        $rName = Engine_Api::_()->getDbtable('recipients', 'messages')->info('name');
        $select = Engine_Api::_()->getDbtable('recipients', 'messages')->select()
                ->from($rName, new Zend_Db_Expr('COUNT(conversation_id) AS unread'))
                ->where($rName . '.user_id = ?', $user->getIdentity())
                ->where($rName . '.inbox_deleted = ?', 0)
                ->where($rName . '.inbox_read = ?', 0)
                ->where($rName . '.inbox_view = ?', 0)
                ->query()
                ->fetchColumn();

        return empty($select) ? false : $select;
    }

    /**
     * Mark all new unread notifications and messages as show
     *
     * @param User_Model_User $user
     * @param array $ids
     * @return object
     */
    public function markUpdatesAsShow(User_Model_User $user, array $ids = null) {
        if (is_array($ids) && empty($ids)) {
            return $this;
        }

        $where = array(
            '`user_id` = ?' => $user->getIdentity(),
            '`show` = ?' => 0,
            '`read` = ?' => 0
        );

        if (!empty($ids)) {
            $where['`notification_id` IN(?)'] = $ids;
        }

        Engine_Api::_()->getDbtable('notifications', 'activity')->update(array('show' => 1), $where);

        return $this;
    }

    /**
     * Mark all new unread messages as show
     *
     * @param User_Model_User $user
     * @param array $ids
     * @return object
     */
    public function markMessagesAsShow(User_Model_User $user, array $ids = null) {
        $where = array(
            '`user_id` = ?' => $user->getIdentity(),
            '`inbox_view` = ?' => 0,
            '`inbox_read` = ?' => 0
        );

        if (!empty($ids)) {
            $where['`conversation_id` IN(?)'] = $ids;
        }

        Engine_Api::_()->getDbtable('recipients', 'messages')->update(array('inbox_view' => 1), $where);

        return $this;
    }

    /**
     * Mark all new unread messages as show
     *
     * @param User_Model_User $user
     * @param array $ids
     * @return object
     */
    public function markNotificationsAsRead(User_Model_User $user) {
        $where = array(
            '`user_id` = ?' => $user->getIdentity(),
            '`type` !=?' => 'friend_request',
            '`type` !=?' => 'message_new'
        );

        Engine_Api::_()->getDbtable('notifications', 'activity')->update(array('read' => 1), $where);

        return $this;
    }

}