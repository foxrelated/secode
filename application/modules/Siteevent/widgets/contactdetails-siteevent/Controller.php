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
class Siteevent_Widget_ContactdetailsSiteeventController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return $this->setNoRender();
        }

        //GET SUBJECT
        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        $this->view->otherInfo = $otherInfo = Engine_Api::_()->getDbTable('otherinfo', 'siteevent')->getOtherinfo($siteevent->event_id);

        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $this->view->contactAllowed = Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "contact");
        $this->view->can_edit = $siteevent->authorization()->isAllowed($viewer, "edit");

        if (empty($otherInfo->phone) && empty($otherInfo->email) && empty($otherInfo->website) && (!$this->view->can_edit || !$this->view->contactAllowed)) {
            return $this->setNoRender();
        }

        //GET SETTINGS
        $pre_field = array("0" => "1", "1" => "2", "2" => "3");
        $contacts = $this->_getParam('contacts', $pre_field);

        if (empty($contacts)) {
            $this->setNoRender();
        } else {
            //INITIALIZATION
            $this->view->show_phone = $this->view->show_email = $this->view->show_website = 0;
            if (in_array(1, $contacts)) {
                $this->view->show_phone = 1;
            }
            if (in_array(2, $contacts)) {
                $this->view->show_email = 1;
            }
            if (in_array(3, $contacts)) {
                $this->view->show_website = 1;
            }
        }
    }

}