<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Overview.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_Waitlist_Capacity extends Engine_Form {

    public function init() {

        $this->setTitle("Capacity & Waitlist")
            ->setDescription(Zend_Registry::get('Zend_Translate')->_("Leave the 'Capacity' field empty if you do not want to put restriction on capacity for this event."))
            ->setAttrib('name', 'siteevents_capacity');

        $this->addElement('Text', 'capacity', array(
            'label' => 'Capacity',
            'description' => 'Enter the value of maximum members who can join this event. After this capacity is reached, members will be able to apply for the waitlist of this event, which you will be able to manage from the below section.',
            'validators' => array(
                array('Int', true),
            ),
        ));
        $this->capacity->getDecorator('Description')->setOption("placement", "append");

        $this->addElement('Button', 'save', array(
            'label' => 'Save',
            'type' => 'submit',
        ));
    }

}
