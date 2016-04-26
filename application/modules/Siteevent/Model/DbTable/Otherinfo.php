<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Otherinfo.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Model_DbTable_Otherinfo extends Engine_Db_Table {

    protected $_rowClass = "Siteevent_Model_Otherinfo";
    protected $_serializedColumns = array('main_video');

    public function getOtherinfo($event_id) {

        $rName = $this->info('name');
        $select = $this->select()
                ->where($rName . '.event_id = ?', $event_id);

        $row = $this->fetchRow($select);

        if (empty($row))
            return;

        return $row;
    }

    public function getColumnValue($event_id, $column_name) {

        return $this->select()
                        ->from($this->info('name'), array("$column_name"))
                        ->where('event_id = ?', $event_id)
                        ->limit(1)
                        ->query()
                        ->fetchColumn();
    }
    
    public function getOtherinfoColumns($params){
      
      $select = $this->select()
                        ->from($this->info('name'), $params['columns'])
                        ->where('event_id = ?', $params['event_id']);
      
      $row = $this->fetchRow($select);

      if (empty($row))
          return;

      return $row;
      
    }

}