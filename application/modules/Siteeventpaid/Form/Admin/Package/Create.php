<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventpaid
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventpaid_Form_Admin_Package_Create extends Engine_Form {

    public function init() {

        $this->setTitle('Create Event Package')
                ->setDescription('Create a new event package over here. Below, you can configure various settings for this package like videos, overview, etc. Please note that payment parameters (Price, Duration) cannot be edited after creation. If you wish to change these, you will have to create a new package and disable the existing one.');

        // Element: title
        $this->addElement('Text', 'title', array(
            'label' => 'Package Name',
            'required' => true,
            'allowEmpty' => false,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '128')),
            ),
        ));

        // Element: description
        $this->addElement('Textarea', 'description', array(
            'label' => 'Package Description',
            'validators' => array(
                array('StringLength', true, array(0, 150)),
            )
        ));

        // Element: level_id
        $multiOptions = array('0' => 'All Levels');
        foreach (Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll() as $level) {
            if ($level->type == 'public') {
                continue;
            }
            $multiOptions[$level->getIdentity()] = $level->getTitle();
        }
        $this->addElement('Multiselect', 'level_id', array(
            'label' => 'Member Levels',
            'description' => 'Select the Member Levels to which this Package should be available. Only users belonging to the selected Member Levels will be able to create event of this package.',
            'attribs' => array('style' => 'max-height:100px; '),
            'multiOptions' => $multiOptions,
            'value' => array('0')
        ));

        // Element: price
        $this->addElement('Text', 'price', array(
            'label' => 'Price',
            'description' => 'The amount to charge from the event owner. Setting this to zero will make this a free package.',
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('Float', true),
                new Engine_Validate_AtLeast(0),
            ),
            'value' => '0.00',
        ));

        // Element: recurrence @ todo

        $this->addElement('Duration', 'recurrence', array(
            'label' => 'Billing Cycle',
            'description' => 'How often should Events of this package be billed? (You can choose the payment for this package to be one-time or recurring.)',
            'required' => true,
            'allowEmpty' => false,
            'value' => array(1, 'month'),
        ));

        // Element: duration
        $this->addElement('Duration', 'duration', array(
            'label' => 'Billing Duration',
            'description' => 'When should this package expire? For one-time packages, the package will expire after the period of time set here. For recurring plans, the user will be billed at the above billing cycle for the period of time specified here.',
            'required' => true,
            'allowEmpty' => false,
            'value' => array('0', 'forever'),
        ));

        // renew
        $this->addElement('Checkbox', 'renew', array(
            'description' => 'Renew Link',
            'label' => 'Event creators will be able to renew their events of this package before expiry. (Note: Renewal link after expiry will only be shown for events of paid packages, i.e., packages having a non-zero value of Price above.)',
            'value' => 0,
            'onclick' => 'javascript:setRenewBefore();',
        ));

        $this->addElement('Text', 'renew_before', array(
            'label' => 'Renewal Frame before Event Expiry',
            'description' => 'Show event renewal link these many days before expiry.',
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('Int', true),
                new Engine_Validate_AtLeast(0),
            ),
            'value' => '0',
        ));

        // auto aprove
        $this->addElement('Checkbox', 'approved', array(
            'description' => "Auto-Approve",
            'label' => 'Auto-Approve events of this package. These events will not need admin moderation approval before going live.',
            'value' => 0,
        ));

        // Element:sponsored
        $this->addElement('Checkbox', 'sponsored', array(
            'description' => "Sponsored",
            'label' => 'Make events of this package as Sponsored. (Note: A change in this setting later on will only apply on new events that are created in this package.)',
            'value' => 0,
        ));

        // Element:featured
        $this->addElement('Checkbox', 'featured', array(
            'description' => "Featured",
            'label' => 'Make events of this package as Featured. (Note: A change in this setting later on will only apply on new events that are created in this package.)',
            'value' => 0,
        ));

        // Element: overview
        $this->addElement('Checkbox', 'overview', array(
            'description' => "Overview",
            'label' => 'Enable Overview for events of this package. (Using this, users will be able to create rich profiles for their events using WYSIWYG editor.)',
            'value' => 0,
        ));

        // Element : video
        $this->addElement('Radio', 'video', array(
            'label' => 'Videos',
            'description' => 'Enable Videos for events of this package.',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No',
            ),
            'value' => 1,
            'onclick' => 'showVideoOption(this.value)',
        ));

        // Element : video_count
        $this->addElement('text', 'video_count', array(
            'label' => 'Maximum Allowed Videos',
            'value' => 10,
            'description' => 'Please enter the number of videos which you want to be uploaded in the events of this package. (Note: Enter 0 for unlimited videos.)'
        ));

        // Element : photo
        $this->addElement('Radio', 'photo', array(
            'label' => 'Photos',
            'description' => 'Enable Photos for events of this package.',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No',
            ),
            'value' => 1,
            'onclick' => 'showPhotoOption(this.value)',
        ));

        // Element : photo_count
        $this->addElement('text', 'photo_count', array(
            'label' => 'Maximum Allowed Photos',
            'value' => 10,
            'description' => 'Please enter the number of photos which you want to be uploaded in the events of this package. (Note: Enter 0 for unlimited photos.)'
        ));


        // Element : profile
        $this->addElement('Radio', 'profile', array(
            'label' => 'Profile Information',
            'description' => '',
            'multiOptions' => array(
                '1' => 'Yes, allow profile information with ALL available fields.',
                '0' => 'No, do not allow profile information for events of this package.',
                '2' => 'Yes, allow profile information with RESTRICTED fields. (Below, you can choose the profile fields that should be available. With this configuration, you can give access to more profile fields to packages of higher cost.)',
            ),
            'value' => 1,
            'onclick' => 'showprofileOption(this.value)',
        ));

        //Add Dummy element for using the tables
        $this->addElement('Dummy', 'profilefield', array(
            'ignore' => true,
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_profilefield.tpl',
                        'class' => 'form element'
                    )))
        ));

        $this->addElement('Checkbox', 'update_list', array(
            'description' => 'Show in "Other available Packages" List',
            'label' => "Show this package in the list of 'Other available Packages' which gets displayed to the users for upgrading the package of a Event at Event dashboard. (This will be useful in case you are creating a free package or a test package and you want it to be used by the users only once for a limited period of time and do not want to show it during package upgrdation.)",
            'value' => 1,
        ));


        //ADDING SETTINGS IF SITEEVENTTICKET MODULE IS ENABLED.
        if (Engine_Api::_()->siteevent()->hasTicketEnable()) {
            
            $this->addElement('Radio', 'ticket_type', array(
                'label' => 'Ticket Types',
                'description' => 'Select type of tickets which can be created for event of this package.',
                'multiOptions' => array(
                    0 => 'Free Tickets',
                    1 => 'Both Free and Paid Tickets'
                ),
                'value' => 0,
            ));            

            $this->addElement('Select', 'commission_handling', array(
                'label' => 'Commission Type',
                'description' => 'Select the type of commission. This commission will be applied on all the orders placed for tickets from the events of this package.',
                'multiOptions' => array(
                    1 => 'Percent',
                    0 => 'Fixed'
                ),
                'value' => 1,
                'onchange' => 'showcommissionType();'
            ));

            $localeObject = Zend_Registry::get('Locale');
            $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
            $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);
            $this->addElement('Text', 'commission_fee', array(
                'label' => 'Commission Value (' . $currencyName . ')',
                'description' => 'Enter the value of the commission. (If you do not want to apply any commission, then simply enter 0.)',
                'allowEmpty' => false,
                'value' => 1,
            ));

            $this->addElement('Text', 'commission_rate', array(
                'label' => 'Commission Value (%)',
                'description' => 'Enter the value of the commission. (Do not add any symbol. For 10% commission, enter commission value as 10. You can only enter commission percentage between 0 and 100.)',
                'validators' => array(
                    array('Between', true, array('min' => 0, 'max' => 100, 'inclusive' => true)),
                ),
                'value' => 1,
            ));

            // Element: transfer_threshold
            if(Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.payment.to.siteadmin', '0')) {            
                $this->addElement('Text', 'transfer_threshold', array(
                    'label' => "Payment Threshold Amount ($currencyName)",
                    'description' => 'Enter the payment threshold amount. Event owners of events of this package will be able to request you for their payments when the total amount of their Event Ticket sale becomes more than this threshold amount.',
                    'allowEmpty' => false,
                    'required' => true,
                    'value' => 100,
                ));
            }
        }

        // Element: enabled
        $this->addElement('hidden', 'enabled', array(
            'value' => 1,
        ));

        // Element: execute
        $this->addElement('Button', 'execute', array(
            'label' => 'Create Package',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper'),
        ));

        // Element: cancel
        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'prependText' => ' or ',
            'ignore' => true,
            'link' => true,
            'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index', 'package_id' => null)),
            'decorators' => array('ViewHelper'),
        ));

        // DisplayGroup: buttons
        $this->addDisplayGroup(array('execute', 'cancel'), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper',
            )
        ));
    }

}
