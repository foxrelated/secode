<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Topics.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Model_DbTable_Topics extends Engine_Db_Table {

    protected $_rowClass = 'Siteevent_Model_Topic';

    public function getEventTopices($lisiibg_id) {

        //MAKE QUERY
        $select = $this->select()
                ->where('event_id = ?', $lisiibg_id)
                ->order('sticky DESC')
                ->order('modified_date DESC');

        //RETURN RESULTS
        return Zend_Paginator::factory($select);
    }

}