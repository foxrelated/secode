<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Remainingbills.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Model_DbTable_Remainingbills extends Engine_Db_Table {

    protected $_name = 'siteeventticket_remaining_bills';

    /**
     * Return event_id
     *
     * Is event remaining bill exist or not
     * @param $event_id
     * @return object
     */
    public function isEventRemainingBillExist($event_id) {
        $select = $this->select()
                        ->from($this->info('name'), array("event_id"))
                        ->where('event_id =?', $event_id)
                        ->query()->fetchColumn();

        return $select;
    }

}
