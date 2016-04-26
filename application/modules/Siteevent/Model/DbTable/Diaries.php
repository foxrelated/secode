<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Diaries.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Model_DbTable_Diaries extends Engine_Db_Table {

    protected $_rowClass = 'Siteevent_Model_Diary';

    public function userDiaries($owner, $total_item = 10, $diary_id = 0, $recentDiaryId = 0) {

        //GET VIEWER DETAIL
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET DIARY TABLE
        $diaryTableName = $this->info('name');

        //MAKE QUERY
        $select = $this->select()->setIntegrityCheck(false);

        if (!empty($recentDiaryId)) {
            $select->from($diaryTableName, array('diary_id'));
        } else {
            $select->from($diaryTableName);
        }

        $select->where($diaryTableName . '.owner_id = ?', $owner->getIdentity())
                ->group($diaryTableName . '.diary_id')
                ->order('diary_id DESC');

        if (!empty($diary_id)) {
            $select->where($diaryTableName . '.diary_id != ?', $diary_id);
        }

        //LOGGED IN USER
        if (!empty($viewer_id) && $viewer_id != $owner->getIdentity()) {

            //GET AUTHORIZATION TABLE
            $authorizationTable = Engine_Api::_()->getDbtable('allow', 'authorization');
            $authorizationTableName = $authorizationTable->info('name');

            $authorizationAllow = array('everyone');
            $authorizationAllow[] = 'registered';

            //SAME AS OWNER NETWORK
            $owner_network = $authorizationTable->is_network($owner, $viewer);
            if (!empty($owner_network)) {
                $authorizationAllow[] = 'owner_network';
            }

            //OWNERS FRIEND
            $owner_member = $owner->membership()->isMember($viewer, true);
            if (!empty($owner_member)) {
                $authorizationAllow[] = 'owner_member';
            }

            //OWNERS FRIEND AND FRIREND OF OWNERS FRIEND
            $owner_member_member = $authorizationTable->is_owner_member_member($owner, $viewer);
            if (!empty($owner_member_member)) {
                $authorizationAllow[] = 'owner_member_member';
            }

            $select->join($authorizationTableName, "$authorizationTableName.resource_id = $diaryTableName.diary_id", array())
                    ->where("$authorizationTableName.resource_type = ?", 'siteevent_diary')
                    ->where("$authorizationTableName.role IN (?)", (array) $authorizationAllow);
        }

        if (!empty($recentDiaryId)) {
            return $select->query()
                            ->fetchColumn();
        } else {
            if (!empty($total_item)) {
                $select = $select->limit($total_item);
            }

            //RETURN RESULTS
            return $this->fetchAll($select);
        }
    }

    public function getUserDiaries($viewer_id) {

        //RETURN IF VIEWER ID IS EMPTY
        if (empty($viewer_id)) {
            return;
        }

        //MAKE QUERY
        $select = $this->select()->where('owner_id = ?', $viewer_id);

        //RETURN RESULTS
        return $this->fetchAll($select);
    }

    public function getBrowseDiaries($params = array()) {

        //GE DIARY PAGE TABLE
        $diaryEventTable = Engine_Api::_()->getDbtable('diarymaps', 'siteevent');
        $diaryEventTableName = $diaryEventTable->info('name');

        //GET DIARY TABLE
        $diaryTableName = $this->info('name');

        //MAKE QUERY
        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($diaryTableName)
                ->joinLeft($diaryEventTableName, "$diaryEventTableName.diary_id = $diaryTableName.diary_id", array("COUNT($diaryEventTableName.diary_id) AS total_item"));

        if (isset($params['search']) && !empty($params['search'])) {
            $search = $params['search'];
            $select->where("$diaryTableName.title LIKE '%$search%' OR $diaryTableName.body LIKE '%$search%'");
        }

        if (isset($params['text']) && !empty($params['text'])) {
            $text = $params['text'];
            $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');
            $select->joinLeft($tableUserName, "$tableUserName.user_id = $diaryTableName.owner_id", array("user_id"))
                    ->where("$tableUserName.username LIKE '%$text%' OR $tableUserName.displayname LIKE '%$text%' OR $tableUserName.email LIKE '$text'");
        } elseif (isset($params['member']) && !empty($params['member'])) {
            $text = $params['member'];
            $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');
            $select->joinLeft($tableUserName, "$tableUserName.user_id = $diaryTableName.owner_id", array("user_id"))
                    ->where("$tableUserName.username LIKE '%$text%' OR $tableUserName.displayname LIKE '%$text%' OR $tableUserName.email LIKE '$text'");
        } elseif (isset($params['search_diary']) && !empty($params['search_diary'])) {
            $search_diary = $params['search_diary'];
            $viewer = Engine_Api::_()->user()->getViewer();
            $viewer_id = $viewer->getIdentity();

            if ($search_diary == 'my_diaries') {
                $select->where("$diaryTableName.owner_id = ?", $viewer_id);
            } elseif ($search_diary == 'friends_diaries') {
                $friend_ids = $viewer->membership()->getMembershipsOfIds();
                if (empty($friend_ids)) {
                    $select->where("$diaryTableName.owner_id = ?", -1);
                } else {
                    $select->where("$diaryTableName.owner_id IN (?)", (array) $friend_ids);
                }
            }
        }

        if (isset($params['orderby']) && !empty($params['orderby'])) {
            $select->order($params['orderby'] . ' DESC');
        }

        if (isset($params['owner_ids']) && !empty($params['owner_ids'])) {
            $select->where($diaryTableName . '.owner_id in (?)', $params['owner_ids']);
        }

        if (!empty($params['limit']) && !empty($params['limit'])) {
            $select->limit($params['limit']);
        }

        $select->order($diaryTableName . '.diary_id DESC')->group($diaryTableName . '.diary_id');

        //GET PAGINATOR
        if (isset($params['pagination']) && !empty($params['pagination'])) {
            return Zend_Paginator::factory($select);
        } else {
            return $this->fetchAll($select);
        }
    }

    public function getRecentDiaryId($owner_id) {

        $max_diary_id = $this->select()
                ->from($this->info('name'), array("MAX(diary_id) AS max_diary_id"))
                ->where('owner_id = ?', $owner_id)
                ->query()
                ->fetchColumn();

        if (!empty($max_diary_id)) {
            return $max_diary_id;
        }

        return 0;
    }

    public function getDiaryCount() {

        $total_diaries = $this->select()
                ->from($this->info('name'), array("Count(diary_id) AS total_diaries"))
                ->query()
                ->fetchColumn();

        return $total_diaries;
    }

}