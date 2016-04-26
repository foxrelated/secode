<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Locations.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Model_DbTable_Locations extends Engine_Db_Table {

    protected $_rowClass = "Siteevent_Model_Location";

    /**
     * Get location
     *
     * @param array $params
     * @return object
     */
    public function getLocation($params = array()) {


        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1)) {

            $locationName = $this->info('name');

            $select = $this->select();
            if (isset($params['id'])) {
                $select->where('event_id = ?', $params['id']);
                return $this->fetchRow($select);
            }

            if (isset($params['event_ids'])) {
                $select->where('event_id IN (?)', (array) $params['event_ids']);
                return $this->fetchAll($select);
            }
        }
    }

}