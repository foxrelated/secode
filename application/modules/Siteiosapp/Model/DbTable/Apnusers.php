<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteiosapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Apnusers.php 2015-10-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteiosapp_Model_DbTable_Apnusers extends Engine_Db_Table {

    protected $_rowClass = 'Siteiosapp_Model_Apnuser';

    /**
     * Save the Apnuser registration ID and device ID to send push notification. Here registration ID could be change for the client because it's generate by client default but device ID is unique identification of client. Which will never change.
     * 
     * @param array $params: array of input values.
     * @return empty
     */
    public function addApnuser(array $params = null) {
        $params['status'] = false;
        $params['creation_date'] = date('Y-m-d H:i:s');

        if (!isset($params['user_id']) && empty($params['user_id']))
            $params['user_id'] = Engine_Api::_()->user()->getViewer()->getIdentity();

        $this->delete(array('device_uuid =?' => $params['device_uuid']));
        $this->delete(array('token =?' => $params['token']));
        $db = $this->getAdapter();
        $db->beginTransaction();
        try {
            $apnuser = $this->createRow();
            $apnuser->setFromArray($params);
            $apnuser->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
        }

        return;
    }

    /**
     * Delete the existing APNuser on the basis of "apnUserId", "token" and "device_uuid".
     * 
     * @param array $params: input value
     * @return empty
     */
    public function removeAPNUser(array $params = null) {
        $params['status'] = false;
        if ($params['device_uuid']) {
            $this->delete(array('device_uuid =?' => $params['device_uuid']));
        } elseif ($params['apnUserId']) {
            $this->delete(array('apnuser_id =?' => $params['apnUserId']));
        } elseif ($params['token']) {
            $this->delete(array('token =?' => $params['token']));
        }

        return;
    }

    /**
     * Get the existing APNusers registration id to send push notification.
     * 
     * @param array $params: input value
     * @return array
     */
    public function getUsers(array $params = null) {
        $select = $this->select();
        if (isset($params['user_ids']) && $params['user_ids'])
            $select->where('user_id IN(?)', (array) $params['user_ids']);

        if (isset($params['user_id']) && $params['user_id'])
            $select->where('user_id =?', $params['user_id']);

        $result = $this->fetchAll($select);

        $users = array();
        foreach ($result as $row) {
            $users[] = $row->token;
        }

        return $users;
    }

    /**
     * Get the existing APNusers registration id to send push notification on the basis of network.
     * 
     * @param array $params: input value
     * @return array
     */
    public function getNetworkBasedUsers(array $params = null) {
        $networkTable = Engine_Api::_()->getDbtable('membership', 'network');
        $select = $this->select()
                ->from($this->info('name'))
                ->join($networkTable->info('name'), $networkTable->info('name') . '.user_id = ' . $this->info('name') . '.user_id', null)
                ->where($networkTable->info('name') . '.resource_id IN(?)', $params)
        ;
        $result = $this->fetchAll($select);
        $users = array();
        foreach ($result as $row) {
            $users[] = $row->token;
        }

        return $users;
    }

    /**
     * Get the existing APNusers registration id to send push notification on the basis of member level.
     * 
     * @param array $params: input value
     * @return array
     */
    public function getLevelBasedUsers(array $params = null) {
        $userTable = Engine_Api::_()->getDbtable('users', 'user');
        $select = $this->select()
                ->from($this->info('name'))
                ->join($userTable->info('name'), $userTable->info('name') . '.user_id = ' . $this->info('name') . '.user_id', null)
                ->where($userTable->info('name') . '.level_id IN(?)', $params)
        ;
        $result = $this->fetchAll($select);
        $users = array();
        foreach ($result as $row) {
            $users[] = $row->token;
        }

        return $users;
    }

    /**
     * Get the existing APNusers object on the basis of search displayname.
     * 
     * @param array $params: input value
     * @return object
     */
    public function getAllApnBasedUsers($text, $limit) {

        $userTable = Engine_Api::_()->getDbtable('users', 'user');
        $select = $userTable->select()
                ->from($userTable->info('name'))
                ->join($this->info('name'), $userTable->info('name') . '.user_id = ' . $this->info('name') . '.user_id', null)
        ;
        if (null !== $text)
            $select->where('`' . $userTable->info('name') . '`.`displayname` LIKE ?', '%' . $text . '%');

        if (0 < ($limit))
            $select->limit($limit);

        $select->group($userTable->info('name') . '.user_id');

        return $userTable->fetchAll($select);
    }

}
