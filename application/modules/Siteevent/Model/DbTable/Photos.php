<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Photos.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Model_DbTable_Photos extends Engine_Db_Table {

    protected $_rowClass = 'Siteevent_Model_Photo';

    public function getPhotoId($event_id = null, $file_id = null) {

        $photo_id = 0;
        $photo_id = $this->select()
                ->from($this->info('name'), array('photo_id'))
                ->where("event_id = ?", $event_id)
                ->where("file_id = ?", $file_id)
                ->query()
                ->fetchColumn();

        return $photo_id;
    }

    /**
     * Return photos
     *
     * @param string $event_id
     * @return photos
     */
    public function GetEventPhoto($event_id, $params = array()) {

        $select = $this->select()
                ->where('event_id = ?', $event_id);
        if (isset($params['show_slidishow']))
            $select->where('show_slidishow = ?', $params['show_slidishow']);

        if (isset($params['limit']) && !empty($params['limit']))
            $select->limit($params['limit']);

        if (isset($params['order']) && !empty($params['order']))
            $select->order($params['order']);
        return $this->fetchAll($select)->toArray();
    }

}