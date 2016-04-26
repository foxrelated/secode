<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ListItems.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Model_DbTable_ListItems extends Engine_Db_Table {

    protected $_rowClass = 'Siteevent_Model_ListItem';

    public function checkLeader($siteevent) {
        $viewer = Engine_Api::_()->user()->getViewer();
        //GET THE LEADERS LIST AND CHECK IF THE VIEWER IS LEADER OR NORMAL USER.
        if ($siteevent->owner_id == $viewer->getIdentity()) {
            $isLeader = 1;
        } else {
            $isLeader = ( null !== $siteevent->getLeaderList()->get($viewer) );
        }
        return $isLeader;
    }

}