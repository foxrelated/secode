<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Videomaps.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Model_DbTable_Videomaps extends Engine_Db_Table {

    protected $_rowClass = 'Sitevideo_Model_Videomap';

    public function findVideoMaps($params) {
        $videoMapTableName = $this->info('name');
        $select = $this->select($videoMapTableName, '*');
        if ($params['channel_id']) {
            $select->where('channel_id = ?', $params['channel_id']);
        }
        return $this->fetchAll($select);
    }

}
