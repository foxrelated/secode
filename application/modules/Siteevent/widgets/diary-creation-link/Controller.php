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
class Siteevent_Widget_DiaryCreationlinkController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET VIEWER ID
        $viewer_id = $viewer->getIdentity();

        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        //GET LEVEL SETTING
        $siteeventDiaryCreateLink = Zend_Registry::isRegistered('siteeventDiaryCreateLink') ? Zend_Registry::get('siteeventDiaryCreateLink') : null;
        $can_create = Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_diary', "create");

        if (empty($siteeventDiaryCreateLink) || !$can_create) {
            return $this->setNoRender();
        }
    }

}
