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
class Siteevent_Widget_ProfileAnnouncementsSiteeventController extends Seaocore_Content_Widget_Abstract {

    protected $_childCount;

    public function indexAction() {

        //DONT RENDER THIS IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event') || !Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.announcement', 1)) {
            return $this->setNoRender();
        }

        //GET VIEWER INFORMATION
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET SITEEVENT SUBJECT
        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        $this->view->content_id = Engine_Api::_()->siteevent()->existWidget('siteevent.profile-announcements-siteevent');

        $limit = $this->_getParam('itemCount', 3);
        $this->view->showTitle = $this->_getParam('showTitle', 1);

        $fetchColumns = array('announcement_id', 'title', 'body');
        $this->view->announcements = Engine_Api::_()->getDbtable('announcements', 'siteevent')->announcements($siteevent->event_id, 0, $limit, $fetchColumns);
        $this->_childCount = count($this->view->announcements);

        if ($this->_childCount <= 0) {
            return $this->setNoRender();
        }
    }

    public function getChildCount() {

        return $this->_childCount;
    }

}