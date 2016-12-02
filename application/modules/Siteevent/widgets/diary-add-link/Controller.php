<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Widget_DiaryAddLinkController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return $this->setNoRender();
        }

        //GET SUBJECT
        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');
        $siteeventDiaryAddLink = Zend_Registry::isRegistered('siteeventDiaryAddLink') ? Zend_Registry::get('siteeventDiaryAddLink') : null;

        //AUTHORIZATION CHECK
        if (empty($siteeventDiaryAddLink) || !empty($siteevent->draft) || empty($siteevent->search) || empty($siteevent->approved)) {
            return $this->setNoRender();
        }

        //GET VIEWER ID
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        //GET LEVEL SETTING
        $can_create = Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_diary', "create");

        if (empty($can_create) || !Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.diary', 1)) {
            return $this->setNoRender();
        }

        $this->view->diaryAddCount = $this->_getParam('diaryAddCount', 1);
        $this->view->totalDiaryAddCount = 0;
        if ($this->view->diaryAddCount) {
            $this->view->totalDiaryAddCount = Engine_Api::_()->getDbTable('diarymaps', 'siteevent')->getDiariesEventCount($siteevent->event_id);
        }
    }

}