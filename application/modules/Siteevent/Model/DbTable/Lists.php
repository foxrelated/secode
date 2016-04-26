<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Lists.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Model_DbTable_Lists extends Engine_Db_Table {

    protected $_rowClass = 'Siteevent_Model_List';

    public function getEventsUserLead($user_id) {
        $table = Engine_Api::_()->getItemTable('siteevent_list_item');
        $siteeventListItemTableName = $table->info('name');
        $siteeventListTableName = $this->info('name');
        $select = $this->select();
        $select = $select
                ->setIntegrityCheck(false)
                ->from($siteeventListTableName, array('owner_id'));

        $select->join($siteeventListItemTableName, "$siteeventListTableName.list_id = $siteeventListItemTableName.list_id   ", array());
        $select->where('child_id = ?', $user_id);
        $eventsList = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        return implode(",", $eventsList);
    }

    public function getLeaders($list_id) {
        $table = Engine_Api::_()->getItemTable('siteevent_list_item');
        $siteeventListItemTableName = $table->info('name');

        $select = $table->select()
                ->from($siteeventListItemTableName, array('child_id'));
        $select->where('list_id = ?', $list_id);
        $eventsList = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        return implode(",", $eventsList);
    }

}