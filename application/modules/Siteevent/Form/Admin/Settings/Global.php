<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_Admin_Settings_Global extends Engine_Form {

    // IF YOU WANT TO SHOW CREATED ELEMENT ON PLUGIN ACTIVATION THEN INSERT THAT ELEMENT NAME IN THE BELOW ARRAY.
    public $_SHOWELEMENTSBEFOREACTIVATE = array(
        "submit_lsetting", "environment_mode"
    );

    public function init() {

        $this->setTitle('Global Settings')
                ->setDescription('These settings affect all members in your community.')
                ->setName('review_global');

        $coreSettings = Engine_Api::_()->getApi('settings', 'core');

        $this->addElement('Text', 'siteevent_lsettings', array(
            'label' => 'Enter License key For Advanced Events Plugin',
            'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
            'value' => $coreSettings->getSetting('siteevent.lsettings'),
        ));
        
        $isSiteeventrepeatEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventticket');
        if (!empty($isSiteeventrepeatEnabled)) {
            $this->addElement('Text', 'siteeventticket_lsettings', array(
                'label' => 'Enter License key For Advanced Events - Paid Event and Ticket Selling Extension',
                'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
                'value' => $coreSettings->getSetting('siteeventticket.lsettings'),
            ));
        }

        $isSiteeventrepeatEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventrepeat');
        if (!empty($isSiteeventrepeatEnabled)) {
            $this->addElement('Text', 'siteeventrepeat_lsettings', array(
                'label' => 'Enter License key For Advanced Events - Recurring / Repeating Events Extension',
                'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
                'value' => $coreSettings->getSetting('siteeventrepeat.lsettings'),
            ));
        }

        $isSiteeventdocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventdocument');
        if (!empty($isSiteeventdocumentEnabled)) {
            $this->addElement('Text', 'siteeventdocument_lsettings', array(
                'label' => 'Enter License key For Advanced Events - Documents Extension',
                'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
                'value' => $coreSettings->getSetting('siteeventdocument.lsettings'),
            ));
        }

        $isSiteeventinviteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventinvite');
        if (!empty($isSiteeventinviteEnabled)) {
            $this->addElement('Text', 'siteeventinvite_lsettings', array(
                'label' => 'Enter License key For Advanced Events - Inviter and Promotion Extension',
                'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
                'value' => $coreSettings->getSetting('siteeventinvite.lsettings'),
            ));
        }

        $isSiteeventemailEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventemail');
        if (!empty($isSiteeventemailEnabled)) {
            $this->addElement('Text', 'siteeventemail_lsettings', array(
                'label' => 'Enter License key For Advanced Events - Event Reminders Extension',
                'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
                'value' => $coreSettings->getSetting('siteeventemail.lsettings'),
            ));
        }

        if (APPLICATION_ENV == 'production') {
            $this->addElement('Checkbox', 'environment_mode', array(
                'label' => 'Your community is currently in "Production Mode". We recommend that you momentarily switch your site to "Development Mode" so that the CSS of this plugin renders fine as soon as the plugin is installed. After completely installing this plugin and visiting few pages of your site, you may again change the System Mode back to "Production Mode" from the Admin Panel Home. (In Production Mode, caching prevents CSS of new plugins to be rendered immediately after installation.)',
                'description' => 'System Mode',
            ));
        } else {
            $this->addElement('Hidden', 'environment_mode', array('order' => 990, 'value' => 0));
        }

        //Add submit button
        $this->addElement('Button', 'submit_lsetting', array(
            'label' => 'Activate Your Plugin Now',
            'type' => 'submit',
            'ignore' => true
        ));

        $this->addElement('Text', 'siteevent_titlesingular', array(
            'label' => 'Singular Event Title',
            'description' => 'Please enter Singular Title for event. This text will come in places like feeds generated, widgets etc.',
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                // array('Alnum', true),
                array('StringLength', true, array(3, 32)),
                array('Regex', true, array('/^[a-zA-Z0-9-_\s]+$/')),
            ),
            'value' => $coreSettings->getSetting('siteevent.titlesingular', 'Event'),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            //new Engine_Filter_StringLength(array('max' => '32')),
        )));

        $this->addElement('Text', 'siteevent_titleplural', array(
            'label' => 'Plural Event Title',
            'description' => 'Please enter Plural Title for events. This text will come in places like Main Navigation Menu, Event Main Navigation Menu, widgets etc.',
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                // array('Alnum', true),
                array('StringLength', true, array(3, 32)),
                array('Regex', true, array('/^[a-zA-Z0-9-_\s]+$/')),
            ),
            'value' => $coreSettings->getSetting('siteevent.titleplural', 'Events'),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            //new Engine_Filter_StringLength(array('max' => '32')),
        )));

        $this->addElement('Text', 'siteevent_slugsingular', array(
            'label' => 'Events URL alternate text for "event"',
            'description' => 'Please enter the text below which you want to display in place of "event" in the URLs of this plugin.',
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                // array('Alnum', true),
                array('StringLength', true, array(3, 16)),
                array('Regex', true, array('/^[a-zA-Z0-9-_]+$/')),
            ),
            'value' => $coreSettings->getSetting('siteevent.slugsingular', 'event-item'),
        ));

        $this->addElement('Text', 'siteevent_slugplural', array(
            'label' => 'Events URL alternate text for "events"',
            'description' => 'Please enter the text below which you want to display in place of "events" in the URLs of this plugin.',
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                // array('Alnum', true),
                array('StringLength', true, array(3, 16)),
                array('Regex', true, array('/^[a-zA-Z0-9-_]+$/')),
            ),
            'value' => $coreSettings->getSetting('siteevent.slugplural', 'event-items'),
        ));
        
        $this->addElement('Radio', 'siteevent_redirection', array(
            'label' => 'Redirection of Events link',
            'description' => 'Please select the redirection page for Events, when user click on "Events" link at Main Navigation Menu.',
            "multiOptions" => array(
                'home' => 'Events Home Page',
                'index' => 'Events Browse Page'
            ),
            'value' => $coreSettings->getSetting('siteevent.redirection', 'home'),
        ));        

        $this->addElement('Radio', 'siteevent_cat_widgets', array(
            'label' => 'Category Home Page Link',
            'description' => "Do you want to redirect users to Category Home pages, when they click on category names? (Note: When users click on Categories available at Browse Events page, then they will be redirected to Browse Events page only.)",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('siteevent.cat.widgets', 1),
        ));

        $this->addElement('Radio', 'siteevent_editorprofile', array(
            'label' => 'Editor Profile Link',
            'description' => 'Where do you want to redirect users, when they click on Editors’ photo, name and view profile links?',
            'multiOptions' => array(
                1 => 'On Editor Profile',
                0 => 'On Member Profile',
            ),
            'value' => $coreSettings->getSetting('siteevent.editorprofile', 1),
        ));

        $this->addElement('Radio', 'siteevent_diary', array(
            'label' => 'Enable Adding Events to Diaries',
            'description' => 'Do you want to enable members of your site to add events to their diaries? (If enabled, then members will be able to create diaries and add events in them.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('siteevent.diary', 1),
        ));
        
        $this->addElement('Radio', 'siteevent_waitlist', array(
            'label' => 'Enable "Capacity & Waitlist" feature',
            'description' => 'Do you want to enable "Capacity & Waitlist" feature of events ?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('siteevent.waitlist', 1),
        ));         
        
        if (!Engine_Api::_()->siteevent()->isTicketBasedEvent()) {
            $this->addElement('Radio', 'siteevent_guestconfimation', array(
                'label' => 'Allow Event Owners to Confirm Guests',
                'description' => 'Do you want to allow event owners to confirm their guest to participate in the event?',
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => $coreSettings->getSetting('siteevent.guestconfimation', 0),
            ));        
        }
        
        $this->addElement('Radio', 'siteevent_privacybase', array(
            'label' => 'Events Visible on Browse pages & in Widgets',
            'description' => "With respect to Events Privacy, do you want all events to be shown to users on the Browse pages (Browse Events, Locations, Pinboard, etc.) and in widgets, or do you want only those events to appear in these for which the user has view privacy. If you choose ‘Yes’, then whenever a user clicks on an event for which he does not have view privacy, then he will be shown a “Private Page” message. If you choose ‘No’, then strict privacy checks will be applied on these Browse pages & widgets which might slightly affect their loading speeds (To minimize such delays, we are using caching based displays.).",
            'multiOptions' => array(
                0 => 'Yes, show all events irrespective of their view privacy.',
                1 => 'No, only show those events for which user has view privacy.'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.privacybase', 0),
        ));
        $this->addElement('Dummy', 'networkLocationHeading', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_formElementsHeading.tpl',
                        'heading' => 'Network and Location Settings',
                        'class' => 'form element'
                    ))),
        ));

        $this->addElement('Radio', 'siteevent_network', array(
            'label' => 'Browse by Networks',
            'description' => "Do you want to show events according to viewer's network if he has selected any? (If set to no, all the events will be shown.)",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'showDefaultNetwork(this.value)',
            'value' => $coreSettings->getSetting('siteevent.network', 0),
        ));

        $this->addElement('Radio', 'siteevent_default_show', array(
            'label' => 'Set Only My Networks as Default in search',
            'description' => 'Do you want to set "Only My Networks" option as default for Show field in the search form widget? (This widget appears on the events browse and home pages, and enables users to search and filter events.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onclick' => 'showDefaultNetworkType(this.value)',
            'value' => $coreSettings->getSetting('siteevent.default.show', 0),
        ));

        $this->addElement('Radio', 'siteevent_networks_type', array(
            'label' => 'Network selection for Events',
            'description' => "You have chosen that viewers should only see Events of their network(s). How should an Event's network(s) be decided?",
            'multiOptions' => array(
                0 => "Event Owner's network(s) [If selected, only members belonging to event owner's network(s) will see the Events.]",
                1 => "Selected Networks [If selected, event owner will be able to choose the networks of which members will be able to see their Event.]"
            ),
            'value' => $coreSettings->getSetting('siteevent.networks.type', 0),
        ));

        $this->addElement('Radio', 'siteevent_networkprofile_privacy', array(
            'label' => 'Network based Event Viewing',
            'description' => "Do you want to show Event Profile pages according to viewer’s network if he has selected any?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            // 'onclick' => 'showviewablewarning(this.value);',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.networkprofile.privacy', 0),
        ));
        $this->addElement('Radio', 'siteevent_location', array(
            'label' => 'Location Field',
            'description' => 'Do you want the Location field to be enabled for Events?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'onClick' => 'showLocationSettings(this.value)',
            'value' => $coreSettings->getSetting('siteevent.location', 1),
        ));

        $this->addElement('Radio', 'siteevent_veneuname', array(
            'label' => 'Allow Venue Name',
            'description' => 'Do you want to allow event owners to add venue name for their events?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('siteevent.veneuname', 1)
        ));
        
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $this->addElement('Dummy', "siteevent_seaocore", array(
            'label' => 'Default Location for Searching Events',
            'description' => "We have transferred some location related settings to other section. Please <a target='_blank' href='". $view->url(array('module' => 'seaocore', 'controller' => 'settings', 'action' => 'map'), 'admin_default', true) ."'>click here</a> to change the settings.",
            'ignore' => true,
        ));
        $this->siteevent_seaocore->addDecorator('Description', array('placement' => 'PREPEND', 'class' => 'description', 'escape' => false));        

        $this->addElement('Radio', 'siteevent_proximity_search_kilometer', array(
            'label' => 'Proximity Search',
            'description' => 'Do you want proximity search to be enabled for events? (Proximity search will enable users to search for events within a certain distance from a location.)',
            'multiOptions' => array(
                0 => 'Miles',
                1 => 'Kilometers'
            ),
            'value' => $coreSettings->getSetting('siteevent.proximity.search.kilometer', 0),
        ));

        $this->addElement('Text', 'siteevent_map_city', array(
            'label' => 'Centre Location for Map at Events Home and Browse Events',
            'description' => 'Enter the location which you want to be shown at centre of the map which is shown on Events Home and Browse Events when Map View is chosen to view Events.(To show the whole world on the map, enter the word "World" below.)',
            'required' => true,
            'value' => $coreSettings->getSetting('siteevent.map.city', "World"),
        ));

        $this->addElement('Select', 'siteevent_map_zoom', array(
            'label' => "Default Zoom Level for Map at Events Home and Browse Events",
            'description' => 'Select the default zoom level for the map which is shown on Events Home and Browse Events when Map View is chosen to view Events. (Note that as higher zoom level you will select, the more number of surrounding cities/locations you will be able to see.)',
            'multiOptions' => array(
                '1' => "1",
                "2" => "2",
                "4" => "4",
                "6" => "6",
                "8" => "8",
                "10" => "10",
                "12" => "12",
                "14" => "14",
                "16" => "16"
            ),
            'value' => $coreSettings->getSetting('siteevent.map.zoom', 1),
            'disableTranslator' => 'true'
        ));

        $this->addElement('Radio', 'siteevent_map_sponsored', array(
            'label' => 'Sponsored Events with a Bouncing Animation',
            'description' => 'Do you want the sponsored events to be shown with a bouncing animation in the Map?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('siteevent.map.sponsored', 1),
        ));

        $this->addElement('Dummy', 'otherHeading', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_formElementsHeading.tpl',
                        'heading' => 'Other Settings',
                        'class' => 'form element'
                    ))),
        ));

        $this->addElement('Radio', 'siteevent_profiletab', array(
            'label' => 'Tabs Design Type',
            'description' => 'Select the design type for the tabs available on the main pages of events.',
            'multiOptions' => array(
                1 => 'Advanced Events - New Tabs',
                0 => 'SocialEnigne - Default Tabs'
            ),
            'value' => $coreSettings->getSetting('siteevent.profiletab', 0),
        ));

        $this->addElement('Dummy', 'siteevent_calender_format', array(
            'label' => 'Calendar Format',
            'description' => 'Please <a target=\'_blank\' href=\'admin/seaocore/settings\'>click here</a> to select a format for the calendar.',
        ));
        $this->getElement('siteevent_calender_format')->getDecorator('Description')->setOptions(array('placement', 'APPEND', 'escape' => false));

//        $this->addElement('Radio', 'siteevent_calendar_daystart', array(
//            'label' => 'Calendar Format',
//            'description' => "Select a format for the calendar in the Calendar widget.",
//            'multiOptions' => array(
//                1 => 'First day of week as Sunday, and last as Saturday',
//                2 => 'First day of week as Monday, and last as Sunday',
//                3 => 'First day of week as Saturday, and last as Friday'
//            ),
//            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('', 1),
//        ));


        $this->addElement('Radio', 'siteevent_tinymceditor', array(
            'label' => 'TinyMCE Editor for Discussion',
            'description' => 'Allow TinyMCE editor for discussion message of Events.',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('siteevent.tinymceditor', 1),
        ));

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $localeObject = Zend_Registry::get('Locale');
        $currencyCode = $coreSettings->getSetting('payment.currency', 'USD');
        $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);
        $this->addElement('Dummy', 'siteevent_currency', array(
            'label' => 'Currency',
            'description' => "<b>" . $currencyName . "</b> <br class='clear' /> <a href='" . $view->url(array('module' => 'payment', 'controller' => 'settings'), 'admin_default', true) . "' target='_blank'>" . Zend_Registry::get('Zend_Translate')->_('edit currency') . "</a>",
        ));
        $this->getElement('siteevent_currency')->getDecorator('Description')->setOptions(array('placement', 'APPEND', 'escape' => false));


//        $this->addElement('Radio', 'siteevent_wheretobuy', array(
//            'label' => "Allow 'Where to Buy'",
//            'description' => "Do you want the 'Where to Buy' field to be enabled for Events? (Below, you can choose to enable / disable the Price field while adding Where to Buy option.)",
//            'multiOptions' => array(
//                2 => 'Yes, allow Where To Buy, with Price field',
//                1 => 'Yes, allow Where To Buy, without Price field',
//                0 => 'No'
//            ),
//            'value' => $coreSettings->getSetting('siteevent.wheretobuy', 0),
//        ));

        $this->addElement('Text', 'siteevent_sponsoredcolor', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_formImagerainbowSponsred.tpl',
                        'class' => 'form element'
                    )))
        ));

        $this->addElement('Select', 'siteevent_datetime_format', array(
            'label' => 'Default DateTime Format',
            'description' => 'Choose from below the default DateTime format for the Events on your site. [Note: These DateTime format depends on the locale setting.]',
            'multiOptions' => array(
                'full' => 'Full (EEEE, MMMM d, y h:mm a zzzz) ',
                'long' => 'Long (MMMM d, y h:mm a z) ',
                'medium' => 'Medium (MMM d, y h:mm a) ',
                'short' => 'Short (M/d/yy h:mm a)'
            ),
            'onchange' => 'showTimezoneSetting(this.value)',
            'value' => $coreSettings->getSetting('siteevent.datetime.format', 'medium'),
        ));
        
        $this->addElement('Radio', 'siteevent_timezone', array(
            'label' => 'Display Timezone',
            'description' => 'Do you want to display Timezone along with the date and time?',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No',
            ),
            'value' => $coreSettings->getSetting('siteevent.timezone', 1),
        ));

        $this->addElement('Text', 'siteevent_navigationtabs', array(
            'label' => 'Tabs in Events navigation bar',
            'allowEmpty' => false,
            'maxlength' => '2',
            'required' => true,
            'description' => 'How many tabs do you want to show on Events main navigation bar by default? (Note: If number of tabs exceeds the limit entered by you then a "More" tab will appear, clicking on which will show the remaining hidden tabs. To choose the tab to be shown in this navigation menu, and their sequence, please visit: "Layout" > "Menu Editor")',
            'value' => $coreSettings->getSetting('siteevent.navigationtabs', 6),
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
        ));

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $field = 'siteevent_code_share';
        $this->addElement('Dummy', "$field", array(
            'label' => 'Social Share Widget Code',
            'description' => "<a class='smoothbox' href='". $view->url(array('module' => 'seaocore', 'controller' => 'settings', 'action' => 'social-share', 'field' => "$field"), 'admin_default', true) ."'>Click here</a> to add your social share code.",
            'ignore' => true,
        ));
        $this->$field->addDecorator('Description', array('placement' => 'PREPEND', 'class' => 'description', 'escape' => false));

        $this->addElement('Button', 'save', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}