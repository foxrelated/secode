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
class Siteevent_Widget_ProfileRsvpController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {
        // Don't render this if not authorized
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }

        // Get subject and check auth
        $subject = Engine_Api::_()->core()->getSubject('siteevent_event');
        if (!$subject->canView($viewer)) {
            return $this->setNoRender();
        }
        
        if (Engine_Api::_()->siteevent()->isTicketBasedEvent()) {
            return $this->setNoRender();
        }        

        // Must be a member
        if (!$subject->membership()->isMember($viewer, true)) {
            return $this->setNoRender();
        }

        $siteeventProfileRSVP = Zend_Registry::isRegistered('siteeventProfileRSVP') ? Zend_Registry::get('siteeventProfileRSVP') : null;
        if (empty($siteeventProfileRSVP))
            return $this->setNoRender();

        // Build form
        $this->view->form = new Siteevent_Form_Rsvp();
        $row = $subject->membership()->getRow($viewer);
        $this->view->viewer_id = $viewer->getIdentity();

        if (!$row) {
            return $this->setNoRender();
        }
        $this->view->occurrence_id = Zend_Registry::isRegistered('occurrence_id') ? Zend_Registry::get('occurrence_id') : null;
        $this->view->rsvp = $row->rsvp;
    }

}