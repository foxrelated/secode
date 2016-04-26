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
class Siteevent_Widget_InviteRsvpSiteeventController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        // Don't render this if not authorized
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }
        
        if (Engine_Api::_()->siteevent()->isTicketBasedEvent()) {
            return $this->setNoRender();
        }             

        // Get subject and check auth
        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('siteevent_event');
        if (!$subject->canView($viewer)) {
            return $this->setNoRender();
        }
        $this->view->rsvp = 3;
        // Must be a member
        if ($subject->membership()->isMember($viewer, true)) {
            // Build form
            $row = $subject->membership()->getRow($viewer);
            $this->view->viewer_id = $viewer->getIdentity();
            if ($row) {
                $this->view->rsvp = $row->rsvp;
            }
        }
    }

}