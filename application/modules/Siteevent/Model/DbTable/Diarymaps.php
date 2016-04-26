<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Diarymaps.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Model_DbTable_Diarymaps extends Engine_Db_Table {

    protected $_rowClass = 'Siteevent_Model_Diarymap';

    public function diaryEvents($diary_id, $params = null) {
        //RETURN IF DIARY ID IS EMPTY
        if (empty($diary_id)) {
            return;
        }

        //GET DIARY PAGE TABLE NAME
        $diaryEventTableName = $this->info('name');

        //GET EVENT TABLE
        $siteeventTable = Engine_Api::_()->getDbTable('events', 'siteevent');
        $siteeventTableName = $siteeventTable->info('name');

        //MAKE QUERY
        $select = $siteeventTable->select()
                ->setIntegrityCheck(false)
                ->from($siteeventTableName, array('title', 'event_id', 'owner_id', 'photo_id', 'rating_avg', 'rating_users', 'rating_editor', 'body', 'price', 'category_id', 'featured', 'newlabel', 'sponsored', 'like_count', 'view_count', 'comment_count', 'member_count', 'review_count', 'creation_date', 'location', 'venue_name', 'host_type', 'host_id', 'is_online', 'repeat_params', 'parent_type', 'parent_id'))
                ->join($diaryEventTableName, "$diaryEventTableName.event_id = $siteeventTableName.event_id", array('date', 'event_id'))
                ->where($diaryEventTableName . '.diary_id = ?', $diary_id);

        $select = $siteeventTable->getSiteeventsAllSelect($select, array('action' => 'all'));

        if (isset($params['category_id']) && !empty($params['category_id'])) {
            $select->where($siteeventTableName . '.category_id = ?', $params['category_id']);
        }

        if (isset($params['subcategory_id']) && !empty($params['subcategory_id'])) {
            $select->where($siteeventTableName . '.subcategory_id = ?', $params['subcategory_id']);
        }

        if (isset($params['subsubcategory_id']) && !empty($params['subsubcategory_id'])) {
            $select->where($siteeventTableName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
        }

        if (isset($params['category']) && !empty($params['category'])) {
            $select->where($siteeventTableName . '.category_id = ?', $params['category']);
        }

        if (isset($params['subcategory']) && !empty($params['subcategory'])) {
            $select->where($siteeventTableName . '.subcategory_id = ?', $params['subcategory']);
        }

        if (isset($params['subsubcategory']) && !empty($params['subsubcategory'])) {
            $select->where($siteeventTableName . '.subsubcategory_id = ?', $params['subsubcategory']);
        }

        if (!empty($params['eventType']) && $params['eventType'] != 'All') {
            $select->where($siteeventTableName . ".parent_type =?", $params['eventType']);
        }

        if (isset($params['search']) && !empty($params['search'])) {
            $select->where($siteeventTableName . ".title LIKE ? OR " . $siteeventTableName . ".body LIKE ? ", '%' . $params['search'] . '%');
        }
        if (isset($params['orderby']) && $params['orderby'] == 'random') {
            $select->order('RAND()');
        } else if (isset($params['orderby']) && !empty($params['orderby'])) {
            if ($params['orderby'] == 'date') {
                $select->order("$diaryEventTableName." . $params['orderby'] . " DESC");
            } else {
                $select->order("$siteeventTableName." . $params['orderby'] . " DESC");
            }
        } else {
            $select->order($siteeventTableName . '.event_id' . " DESC");
        }

        //RETURN RESULTS
        return Zend_Paginator::factory($select);
    }

    public function pageDiaries($event_id, $owner_id = 0) {

        //RETURN IF PAGE ID IS EMPTY
        if (empty($event_id)) {
            return;
        }

        //GET DIARY PAGE TABLE NAME
        $diaryTable = Engine_Api::_()->getDbTable('diaries', 'siteevent');
        $diaryTableName = $diaryTable->info('name');

        //GET DIARY PAGE TABLE NAME
        $diaryEventTableName = $this->info('name');

        //MAKE QUERY
        $select = $diaryTable->select()
                ->setIntegrityCheck(false)
                ->from($diaryTableName)
                ->join($diaryEventTableName, "$diaryEventTableName.diary_id = $diaryTableName.diary_id")
                ->where($diaryTableName . '.owner_id = ?', $owner_id)
                ->where($diaryEventTableName . '.event_id = ?', $event_id);

        //RETURN RESULTS
        return $diaryTable->fetchAll($select);
    }

    /**
     * Return diaries count
     *
     * @param int $diary_id 
     * @return diaries count
     * */
    public function itemCount($diary_id) {

        $diaryCount = $this->select()
                ->from($this->info('name'), array('COUNT(*) AS count'))
                ->where('diary_id = ?', $diary_id)
                ->query()
                ->fetchColumn();

        //RETURN DIARY COUNT
        return $diaryCount;
    }

    /**
     * Return diaries count
     *
     * @param int $diary_id 
     * @return diaries count
     * */
    public function getDiariesEventCount($event_id) {

        $diaryCount = $this->select()
                ->from($this->info('name'), array('COUNT(*) AS count'))
                ->where('event_id = ?', $event_id)
                ->query()
                ->fetchColumn();

        //RETURN DIARY COUNT
        return $diaryCount;
    }

}
