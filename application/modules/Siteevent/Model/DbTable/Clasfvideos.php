<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Clasfvideos.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Model_DbTable_Clasfvideos extends Engine_Db_Table {

    protected $_rowClass = 'Siteevent_Model_Clasfvideo';

    public function getEventVideos($event_id = 0, $pagination = 0, $type_video = null) {

        if ($type_video) {
            //VIDEO TABLE NAME
            $videoTableName = $this->info('name');

            //GET CORE VIDEO TABLE
            $coreVideoTable = Engine_Api::_()->getDbtable('videos', 'video');
            $coreVideoTableName = $coreVideoTable->info('name');

            //MAKE QUERY
            $select = $coreVideoTable->select()
                    ->setIntegrityCheck(false)
                    ->from($coreVideoTableName)
                    ->join($videoTableName, $coreVideoTableName . '.video_id = ' . $videoTableName . '.video_id', array())
                    ->group($coreVideoTableName . '.video_id')
                    ->where($videoTableName . '.event_id = ?', $event_id);
        } else {
            //VIDEO TABLE NAME
            $videoTableName = $this->info('name');

            //GET CORE VIDEO TABLE
            $reviewVideoTable = Engine_Api::_()->getDbtable('videos', 'siteevent');
            $reviewVideoTableName = $reviewVideoTable->info('name');

            //MAKE QUERY
            $select = $reviewVideoTable->select()
                    ->from($reviewVideoTableName)
                    ->where($reviewVideoTableName . '.event_id = ?', $event_id);
        }
        //FETCH RESULTS
        if (!empty($pagination)) {
            return Zend_Paginator::factory($select);
        } elseif ($type_video) {
            return $row = $coreVideoTable->fetchAll($select);
        } else {
            return $row = $reviewVideoTable->fetchAll($select);
        }
    }

}