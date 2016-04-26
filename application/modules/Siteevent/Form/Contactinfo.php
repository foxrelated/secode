<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Contactinfo.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_Contactinfo extends Engine_Form {

    public function init() {

        $siteevent = Engine_Api::_()->getItem('siteevent_event', Zend_Controller_Front::getInstance()->getRequest()->getParam('event_id', null));

        $contact_detail_array = (array) Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.contactdetail', array('phone', 'website', 'email'));
        //INITIALIZATION
        $show_phone = $show_email = $show_website = 0;

        if (in_array("phone", $contact_detail_array)) {
            $show_phone = 1;
        }
        if (in_array("email", $contact_detail_array)) {
            $show_email = 1;
        }
        if (in_array("website", $contact_detail_array)) {
            $show_website = 1;
        }

        if ($show_phone || $show_email || $show_website) {
            $this->setTitle('Contact Details')
                    ->setDescription("Contact information will be displayed on your event profile.")
                    ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                    ->setAttrib('name', 'contactinfo');

            if ($show_phone) {
                $this->addElement('Text', 'phone', array(
                    'label' => 'Phone:',
                    'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                        new Engine_Filter_StringLength(array('max' => '63')),
                )));
            }

            if ($show_email) {
                $this->addElement('Text', 'email', array(
                    'label' => 'Email:',
                    'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                        new Engine_Filter_StringLength(array('max' => '127')),
                )));
            }

            if ($show_website) {
                $this->addElement('Text', 'website', array(
                    'label' => 'Website:',
                    'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                        new Engine_Filter_StringLength(array('max' => '127')),
                )));
            }

            if ($show_phone || $show_email || $show_website)
                $this->addElement('Button', 'submit', array(
                    'label' => 'Save Details',
                    'type' => 'submit',
                    'ignore' => true,
                ));
        }
        else {

            $this->addElement('Dummy', 'option', array(
                'description' => '<div class="tip"><span>Admin has not choose any option to show contact detail.</span></div>',
            ));
            $this->getElement('option')->getDecorator('Description')->setOptions(array('placement', 'PREPEND', 'escape' => false));
        }
    }

}