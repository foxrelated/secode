<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Form_Admin_Settings_Global extends Engine_Form {

    public function init() {

        $this->setTitle('Ticket Settings')
                ->setDescription('Here, you can configure various settings for Ticket.')
                ->setName('siteeventticket_ticket_settings');

        $settings = Engine_Api::_()->getApi('settings', 'core');

        //VALUE FOR ENABLE/DISABLE TICKET
        $this->addElement('Radio', 'siteeventticket_ticket_enabled', array(
            'label' => 'Tickets',
            'description' => 'Do you want Tickets to be activated on your site? If enabled, event owners will be able to create tickets after event creation. They will be able to create and manage their event tickets from ‘Tickets’ section available in the event dashboard.',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'javascript:showOtherElements(this.value)',
            'value' => $settings->getSetting('siteeventticket.ticket.enabled', 1),
        ));

        $this->addElement('Text', 'siteeventticket_buylimitmax', array(
            'label' => 'Maximum Buying Limit',
            'allowEmpty' => false,
            'maxlength' => '2',
            'required' => true,
            'description' => "Maximum number of tickets that a user can purchase at a time. This quantity will be displayed by default in the ticket creation form, which event owner can
further modify according to it's requirement.",
            'value' => $settings->getSetting('siteeventticket.buylimitmax', 10),
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
        ));

        //TICKET PRINT FEATURE
        $this->addElement('Radio', 'siteeventticket_detail_step', array(
            'label' => 'Buyer Details Step',
            'description' => 'Do you want to collect user details during Tickets Purchasing ?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'javascript:showCheckboxElements(this.value)',
            'value' => $settings->getSetting('siteeventticket.detail.step', 1),
        ));

        $buyerInfoArray = array('fname' => 'First Name', 'lname' => 'Last Name', 'email' => 'Email');

        $this->addElement('MultiCheckbox', 'siteeventticket_buyer_details', array(
            'label' => 'Buyer Details',
            'description' => 'Select the information options that you want to display to site member while creating an event.',
            'multiOptions' => $buyerInfoArray,
            'value' => $settings->getSetting('siteeventticket.buyer.details', array_keys($buyerInfoArray)),
        ));

        $this->addElement('Button', 'save', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}
