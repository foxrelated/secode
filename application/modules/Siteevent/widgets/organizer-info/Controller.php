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
class Siteevent_Widget_OrganizerInfoController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_organizer')) {
            return $this->setNoRender();
        }

        //GET EVENT SUBJECT
        $this->view->organizer = $organizer = Engine_Api::_()->core()->getSubject('siteevent_organizer');

        $this->view->allowedInfo = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.hostinfo', array('body', 'sociallinks'));

        $this->view->showInfo = $this->_getParam('showInfo', array('title', 'description', 'links', 'photo', 'creator', 'options', 'totalevent', 'totalguest', 'totalrating'));
        $siteeventOrganizerInfo = Zend_Registry::isRegistered('siteeventOrganizerInfo') ? Zend_Registry::get('siteeventOrganizerInfo') : null;
        // Don't render this if not authorized
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        if ($viewer_id) {
            $this->view->level_id = $viewer->level_id;
        }
        $this->view->totalGuest = Engine_Api::_()->getDbtable('events', 'siteevent')->countTotalGuest(
                array('host_type' => $organizer->getType(), 'host_id' => $organizer->getIdentity()));
        if (in_array('totalrating', $this->view->showInfo)) {
            $ratingEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2);
            if ($ratingEnable)
                $this->view->totalRating = Engine_Api::_()->getDbtable('events', 'siteevent')->avgTotalRating(
                        array('host_type' => $organizer->getType(), 'host_id' => $organizer->getIdentity(), 'more_than' => 0));
        }

        if (empty($siteeventOrganizerInfo))
            return $this->setNoRender();
    }

}
