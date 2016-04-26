<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Events.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Model_DbTable_Repeatdates extends Engine_Db_Table {

    //protected $_rowClass = "Siteevent_Model_Event";
    //protected $_serializedColumns = array('main_video', 'networks_privacy');
    //GET A SINGLE EVENT REPEAT INFO  
    public function getRepeatEventInfo($event_id) {
        $repeatDatesTableName = $this->info('name');
        $select = $this->select();
        $select = $select
                ->from($repeatDatesTableName);
        $select->where("event_id =?", $event_id);
        $repeatEventInfo = $this->fetchAll($select);
        return $repeatEventInfo;
    }

    public function getEventType($event_id) {
        $repeatDatesTableName = $this->info('name');
        $select = $this->select();
        $select = $select
                ->from($repeatDatesTableName, array('eventrepeat_type'));
        $select->where("event_id =?", $event_id);
        $select->limit("1");
        $repeatEventType = $select->query()->fetchColumn();
        return $repeatEventType;
    }

    //DELETE THE ROWS FROM THIS TABLE FOR AN EVENT

    public function deleteRepeatEvent($event_id) {

        $this->delete(array('event_id = ?' => $event_id));
    }

}
