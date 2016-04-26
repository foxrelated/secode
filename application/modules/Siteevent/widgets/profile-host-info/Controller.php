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
class Siteevent_Widget_ProfileHostInfoController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {


        //DONT RENDER THIS IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return $this->setNoRender();
        }

        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.host', 1)) {
            return $this->setNoRender();
        }

        $this->view->host = $host = $siteevent->getHost();
        if (!$this->view->host) {
            return $this->setNoRender();
        }

        $this->view->showInfo = $this->_getParam('showInfo', array('totalevent', 'totalguest', 'totalrating', 'hostDescription', 'socialLinks', 'messageHost', 'viewHostProfile'));
        if (Engine_Api::_()->siteevent()->isTicketBasedEvent() && in_array('totalguest', $this->view->showInfo)) {
            $indexTotalGuest = array_search('totalguest', $this->view->showInfo);
            unset($this->view->showInfo[$indexTotalGuest]);
        }     
        
        if (in_array('totalguest', $this->view->showInfo)) {
            $this->view->totalGuest = Engine_Api::_()->getDbtable('events', 'siteevent')->countTotalGuest(
                    array('host_type' => $host->getType(), 'host_id' => $host->getIdentity()));
        }
        if (in_array('totalrating', $this->view->showInfo)) {
            $ratingEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2);
            if ($ratingEnable)
                $this->view->totalRating = Engine_Api::_()->getDbtable('events', 'siteevent')->avgTotalRating(
                        array('host_type' => $host->getType(), 'host_id' => $host->getIdentity(), 'more_than' => 0));
        }
        $this->view->allowedInfo = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.hostinfo', array('body', 'sociallinks'));

        // Don't render this if not authorized
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        //SHOW MESSAGE OWNER LINK TO USER IF MESSAGING IS ENABLED FOR THIS LEVEL
        $this->view->messageSettings = $messageSettings = Engine_Api::_()->authorization()->getPermission($level_id, 'messages', 'auth');
        if ($messageSettings != 'none') {
            $this->view->messageSettings = $messageSettings = 1;
        }

        $this->view->placeWidget = $this->_getParam('placeWidget', 'smallColumn');
        $this->view->getDescription = '';
        if (in_array('body', $this->view->allowedInfo) && in_array('hostDescription', $this->view->showInfo))
            $this->view->getDescription = $this->getDescription($host);
    }

    public function getDescription($subject) {
        $description = '';
        if ($subject instanceof User_Model_User) {
            $viewer = Engine_Api::_()->user()->getViewer();
            // Values
            $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($subject);
            $relationship = 'everyone';
            if ($viewer && $viewer->getIdentity()) {
                if ($viewer->getIdentity() == $subject->getIdentity()) {
                    $relationship = 'self';
                } else if ($viewer->membership()->isMember($subject, true)) {
                    $relationship = 'friends';
                } else {
                    $relationship = 'registered';
                }
            }
            foreach ($fieldStructure as $map) {
                $field = $map->getChild();
                if (!$field || $field->type == 'profile_type')
                    continue;
                if ($field->type == 'about_me') {
                    $value = $field->getValue($subject);
                    // Get first value object for reference
                    $firstValue = $value;
                    if (is_array($value) && !empty($value)) {
                        $firstValue = $value[0];
                    }

                    // Evaluate privacy
                    if (isset($firstValue->privacy)) {
                        if ($firstValue->privacy == 'self' && $relationship != 'self') {
                            $isHidden = true; //continue;
                        } else if ($firstValue->privacy == 'friends' && ($relationship != 'friends' && $relationship != 'self')) {
                            $isHidden = true; //continue;
                        } else if ($firstValue->privacy == 'registered' && $relationship == 'everyone') {
                            $isHidden = true; //continue;
                        } else {
                            $isHidden = false;
                        }
                    }
                    if (!empty($firstValue->value) && !$isHidden) {
                        $description = $firstValue->value;
                    }
                    break;
                }
            }
        } else {
            $description = $subject->getDescription();
        }
        return $description;
    }

}
