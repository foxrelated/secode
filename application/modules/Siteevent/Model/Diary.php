<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Diary.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class siteevent_Model_Diary extends Core_Model_Item_Abstract {

    protected $_searchTriggers = false;
    protected $_parent_type = 'user';
    protected $_parent_is_owner = true;

    /**
     * Gets an absolute URL to the page to view this item
     *
     * @return string
     */
    public function getHref($params = array()) {

        $params = array_merge(array(
            'route' => "siteevent_diary_view",
            'reset' => true,
            //'owner_id' => $this->owner_id,
            'diary_id' => $this->diary_id,
            'slug' => $this->getSlug(),
                ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance()->getRouter()
                        ->assemble($params, $route, $reset);
    }

    public function getDescription() {
        return $this->body;
    }

    /**
     * Return slug
     * */
    public function getSlug($str = null) {

        if (null === $str) {
            $str = $this->title;
        }

        return Engine_Api::_()->seaocore()->getSlug($str, 63);
    }

    public function getDiaryMap($params = array()) {
        $paginator = Engine_Api::_()->getDbTable('diarymaps', 'siteevent')->diaryEvents($this->diary_id, $params);
        if (isset($params['limit']) && $params['limit'] > 0)
            $paginator->setItemCountPerPage($params['limit']);
        return $paginator;
    }

    public function getCoverItem() {
        if (!empty($this->event_id)) {
            return Engine_Api::_()->getItem('siteevent_event', $this->event_id);
        } else {
            return $this;
        }
    }

    public function getPhotoUrl($type = null) {

        if (!empty($this->event_id)) {
            return Engine_Api::_()->getItem('siteevent_event', $this->event_id)->getPhotoUrl($type);
        }
    }

    /**
     * Delete the diary and belongings
     * 
     */
    public function _delete() {

        //DELETE ALL MAPPING VALUES FROM DIARYMAPS TABLES
        Engine_Api::_()->getDbtable('diarymaps', 'siteevent')->delete(array('diary_id = ?' => $this->diary_id));
        //DELETE ACTIVITY FEED
        $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
        $actionTableName = $actionTable->info('name');
        $diaryparams = '{"child_id":"'. $this->diary_id.'"}';
        
        $action_id = $actionTable->select()
                ->setIntegrityCheck(false)
                ->from($actionTableName, 'action_id')
                ->joinInner('engine4_activity_attachments', "engine4_activity_attachments.action_id = $actionTableName.action_id", array())
                ->where('engine4_activity_attachments.id = ?', $this->event_id)
                ->where($actionTableName . '.type = ?', "siteevent_diary_add_event")
                ->where($actionTableName . '.subject_type = ?', 'user')
                ->where($actionTableName . '.object_type = ?', 'siteevent_event')
                ->where($actionTableName . '.object_id = ?', $this->event_id)
                ->where($actionTableName . '.params like(?)', $diaryparams)
                ->query()
                ->fetchColumn();

        if (!empty($action_id)) {
            $activity = Engine_Api::_()->getItem('activity_action', $action_id);
            if (!empty($activity)) {
                $activity->delete();
            }
        }

        //DELETE DIARY
        parent::_delete();
    }

}
