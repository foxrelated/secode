<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: NotificationSettings.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_NotificationSettings extends Engine_Form {

    protected $_isLeader;

    public function getLeader() {
        return $this->_isLeader;
    }

    public function setIsLeader($isLeader) {
        $this->_isLeader = $isLeader;
        return $this;
    }

    public function init() {

        $isTicketBasedEvent = Engine_Api::_()->siteevent()->isTicketBasedEvent();
        
        $this->addElement('Checkbox', 'email', array(
            'description' => 'Email Notifications',
            'label' => "Send email notifications to me when people perform various actions on this event (Below you can individually activate emails for the actions).",
            'onclick' => 'showEmailAction()',
            'value' => 1,
        ));

        //GET EITHER VIEWER IS LEADER OR NOT
        $isLeader = $this->getLeader();
        if ($isLeader) {
            
                    if($isTicketBasedEvent) {
                        $optionsArray = array("posted" => "People post updates on this event", "created" => "People create various contents on this event");
                    }
                    else {
                        $optionsArray = array("posted" => "People post updates on this event", "created" => "People create various contents on this event", "joined" => "People join this event", "rsvp" => "Guests change RSVP for this event");
                    }
            
					$this->addElement('MultiCheckbox', 'action_email', array(
							'multiOptions' => $optionsArray,
							'value' => array("posted", "created", "joined", "rsvp")
					));
        } else {
					$this->addElement('MultiCheckbox', 'action_email', array(
							'multiOptions' => array("posted" => "People post updates on this event", "created" => "People create various contents on this event"),
							'value' => array("posted", "created")
					));
				}

        $this->addElement('Checkbox', 'notification', array(
            'description' => 'Site Notifications',
            'label' => "Send notification updates to me when people perform various actions on this event (Below you can individually activate notifications for the actions).",
            'onclick' => 'showNotificationAction()',
            'value' => 1,
        ));

				if ($isLeader) {
                    
                    if($isTicketBasedEvent) {
                        $optionsArray = array("posted" => "People post updates on this event", "created" => "People create various contents on this event", "comment" => "People post comments on this event", "like" => "People like this event", "follow" => "People follow this event", "title" => "Event owner change title of this event","location" => "Event owner change location of this event","time" => "Event owner change time of this event","venue" => "Event owner change venue of this event");
                    }
                    else {
                        $optionsArray = array("posted" => "People post updates on this event", "created" => "People create various contents on this event", "comment" => "People post comments on this event", "like" => "People like this event", "follow" => "People follow this event", "joined" => "People join this event", "rsvp" => "Guests change RSVP for this event", "title" => "Event owner change title of this event","location" => "Event owner change location of this event","time" => "Event owner change time of this event","venue" => "Event owner change venue of this event");
                    }
                    
					$this->addElement('MultiCheckbox', 'action_notification', array(
							'multiOptions' => $optionsArray,
							'value' => array("posted", "created", "comment", "like", "follow", "joined", "rsvp","title","location","time","venue")
					));
        } else {
					$this->addElement('MultiCheckbox', 'action_notification', array(
							'multiOptions' => array("posted" => "People post updates on this event", "created" => "People create various contents on this event", "comment" => "People post comments on this event", "title" => "Event owner change title of this event","location" => "Event owner change location of this event","time" => "Event owner change time of this event","venue" => "Event owner change venue of this event"),
							'value' => array("posted", "created", "comment","title","location","time","venue")
					));
				}

        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
        ));
    }

}