<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: BadgeController.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_BadgeController extends Core_Controller_Action_Standard {

    public function init() {
        $this->view->badgeEnable = $badge_enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.badge', 1);

        if (empty($badge_enable) || !Engine_Api::_()->hasModuleBootstrap('siteeventinvite')) {
            $action = $this->_getParam('action', null);
            if ($action == 'index') {
                $this->_helper->layout->disableLayout();
            }
            return $this->_forward('notfound', 'error', 'core');
        }

//    if (!$this->_helper->requireAuth()->setAuthParams('event', null, 'view')->isValid())
//      return;
        //RETURN IF SUBJECT IS SET
        if (Engine_Api::_()->core()->hasSubject())
            return;

        //SET POST OR TOPIC SUBJECT
        if (0 != ($event_id = (int) $this->_getParam('event_id')) &&
                null != ($event = Engine_Api::_()->getItem('siteevent_event', $event_id))) {
            Engine_Api::_()->core()->setSubject($event);
        }
    }

    public function indexAction() {
        // $this->_helper->layout->setLayout('default-simple');
        $this->_helper->layout->disableLayout();
        extract($_GET);
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.badge', 1))
            $this->view->siteevent = $subject = Engine_Api::_()->core()->getSubject('siteevent_event');

        $this->view->options = $options;
        $this->view->background_color = $background_color;
        $this->view->border_color = $border_color;
        $this->view->text_color = $text_color;
        $this->view->link_color = $link_color;

        $this->view->occurrence_id = Engine_Api::_()->getDbTable('events', 'siteevent')->getNextOccurID($subject->event_id);
    }

    public function createAction() {
        //USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;
				$viewer = Engine_Api::_()->user()->getViewer();
        $this->view->siteevent = $subject = Engine_Api::_()->core()->getSubject('siteevent_event');
        if (!$subject || !Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.badge', 1))
            return $this->_forward('notfound', 'error', 'core');
        $this->view->canEdit = $subject->authorization()->isAllowed($viewer, "edit");
        if (!$this->view->canEdit)
            return $this->_forward('notfound', 'error', 'core');

        $auth = Engine_Api::_()->authorization()->context;
        $this->view->forEvenyOne = $auth->isAllowed($subject, 'everyone', 'view');
        $this->view->occurrence_id = Engine_Api::_()->getDbTable('events', 'siteevent')->getNextOccurID($subject->event_id);
        if ($this->view->forEvenyOne) {
            $viewPricavyEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.networkprofile.privacy', 0);
            $enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.network', 0);
            if ($enableNetwork && $viewPricavyEnable) {
                if (Engine_Api::_()->siteevent()->listBaseNetworkEnable()) {
                    if ($subject->networks_privacy) {
                        $this->view->forEvenyOne = FALSE;
                    }
                } else {
                    $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
                    $ownerNetworkIds = $networkMembershipTable->getMembershipsOfIds($subject->getOwner('user'));
                    if ($ownerNetworkIds) {
                        $this->view->forEvenyOne = FALSE;
                    }
                }
            }
        }
    }

}