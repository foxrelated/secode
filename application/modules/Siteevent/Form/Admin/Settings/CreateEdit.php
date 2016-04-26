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
class Siteevent_Form_Admin_Settings_CreateEdit extends Engine_Form {

    public function init() {

        $this->setTitle('Miscellaneous Settings')
                ->setDescription('The below settings govern various properties for events on your website. By choosing fewer fields for event creation, you can make creating events quicker on your website (Remaining fields can be configured from Event Dashboard).')
                ->setName('siteevent_global');

        $coreSettings = Engine_Api::_()->getApi('settings', 'core');

        $isTicketBasedEvent = Engine_Api::_()->siteevent()->isTicketBasedEvent();

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventrepeat')) {
            $this->addElement('Text', 'siteevent_occurrencecount', array(
                'label' => 'Default Event Occurrences',
                'allowEmpty' => false,
                'maxlength' => '3',
                'required' => true,
                'description' => 'For recurring events, how many occurrences do you want to be created at the time of event creation / editing? (After the completion of these number of occurrences, if the recurring event\'s termination limit is not reached, then this number or lesser occurrences of the event will be automatically created.)',
                'value' => $coreSettings->getSetting('siteevent.occurrencecount', 15),
                'validators' => array(
                    array('Int', true),
                    array('GreaterThan', true, array(4)),
                ),
            ));
        }

        $redirect_array = array(
            1 => 'Event Profile page',
            0 => 'Event Dashboard',
            2 => 'Invite Guests Page'
        );
        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventinvite'))
            unset($redirect_array['2']);
        $this->addElement('Radio', 'siteevent_create_redirection', array(
            'label' => 'Redirection after Event Creation',
            'description' => 'Where do you want to redirect Event Owners after Event creation?',
            'multiOptions' => $redirect_array,
            'value' => $coreSettings->getSetting('siteevent.create.redirection', 1),
        ));
        $this->addElement('Radio', 'siteevent_host', array(
            'label' => 'Allow Host Info',
            'description' => 'Do you want to allow event creator to add an Event Host?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No',
            ),
            'value' => $coreSettings->getSetting('siteevent.host', 1),
            'onclick' => 'hideHostInfo(this.value);'
        ));

        $hostOptions = array(
            "user" => "Members",
            'siteevent_organizer' => "Other Individuals or Organizations (Users will be able to create a brief profile for these.)",
            'sitebusiness_business' => 'Businesses',
            'sitegroup_group' => 'Groups',
            'sitepage_page' => 'Pages',
            'sitestore_store' => 'Stores',
        );
        foreach ($hostOptions as $k => $v) {
            if (!Engine_Api::_()->hasItemType($k)) {
                unset($hostOptions[$k]);
            }
        }
        $this->addElement('MultiCheckbox', 'siteevent_hostOptions', array(
            'description' => 'Choose content types from which users will be able to choose or add entities as event hosts.',
            'multiOptions' => $hostOptions,
            'value' => $coreSettings->getSetting('siteevent.hostOptions', array('sitepage_page', 'sitebusiness_business', 'user', 'sitegroup_group', 'sitestore_store', 'siteevent_organizer')),
        ));

        $this->addElement('MultiCheckbox', 'siteevent_hostinfo', array(
            'description' => 'Choose information to be added about an Event Host. (This setting will only work if you choose ‘Other Individuals or Organizations’ from the above setting.)',
            'multiOptions' => array(
                'body' => 'Host Description',
                'sociallinks' => 'Social Links'
            ),
            'value' => $coreSettings->getSetting('siteevent.hostinfo', array('body', 'sociallinks')),
        ));

        $this->addElement('Radio', 'siteevent_leader', array(
            'label' => 'Allow Multiple Event Leaders',
            'description' => 'Do you want there to be multiple leaders for events on your site? (If enabled, then every Event will be able to have multiple leaders who will be able to manage that Event. These will have the authority to add other users as leaders of their Events.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No',
            ),
            'value' => $coreSettings->getSetting('siteevent.leader', 1),
        ));

        $this->addElement('Radio', 'siteevent_categoryedit', array(
            'label' => 'Edit Events Category',
            'description' => 'Do you want to allow event owners to edit category of their events?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('siteevent.categoryedit', 1),
        ));

        $this->addElement('Radio', 'siteevent_tags', array(
            'label' => 'Allow Tags',
            'description' => 'Do you want to enable event owners to add tags for their events?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('siteevent.tags', 1)
        ));

        $this->addElement('Radio', 'siteevent_onlineevent_allow', array(
            'label' => 'Allow Online Events',
            'description' => 'Do you want to enable event owners to run their events online on your site?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('siteevent.onlineevent.allow', 1)
        ));

        $this->addElement('Radio', 'siteevent_bodyallow', array(
            'label' => 'Allow Description',
            'description' => 'Do you want to allow event owners to write description for their events?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('siteevent.bodyallow', 1),
            'onclick' => 'showDescription(this.value)'
        ));

        $this->addElement('Radio', 'siteevent_bodyrequired', array(
            'label' => 'Description Required',
            'description' => 'Do you want to make Description a mandatory field for events?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('siteevent.bodyrequired', 1),
        ));

        $this->addElement('Radio', 'siteevent_overview', array(
            'label' => 'Allow Overview',
            'description' => 'Do you want to allow event owners to write overview for their events?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('siteevent.overview', 1),
        ));

        $this->addElement('MultiCheckbox', "siteevent_contactdetail", array(
            'label' => 'Contact Detail Options',
            'description' => 'Choose the contact details options from below that you want to be enabled for the events. (Users will be able to fill below chosen details for their events from their Event Dashboard. To disable contact details section from Event dashboard, simply uncheck all the options.)',
            'multiOptions' => array(
                'phone' => 'Phone',
                'website' => 'Website',
                'email' => 'Email',
            ),
            'value' => $coreSettings->getSetting('siteevent.contactdetail', array('phone', 'website', 'email')),
        ));

        if (!$isTicketBasedEvent) {
            // Either When people try to join this event, should they be allowed to join immediately, or should they be forced to wait for approval? or not.    
            $this->addElement('Radio', 'siteevent_invite_rsvp_option', array(
                'label' => 'Enable “When people try to join this event, should they be allowed to join immediately, or should they be forced to wait for approval?” Setting',
                'description' => 'Do you want to display the setting "When people try to join this event, should they be allowed to join immediately, or should they be forced to wait for approval?" in create event and edit event pages?',
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => $coreSettings->getSetting('siteevent.invite.rsvp.option', 1),
                'onclick' => 'showInviteRSVP(this.value)',
            ));

            $this->addElement('Radio', 'siteevent_invite_rsvp_automatically', array(
                'label' => 'Default “When people try to join this event, should they be allowed to join immediately, or should they be forced to wait for approval?” Option',
                'description' => 'What default value do you want for "When people try to join this event, should they be allowed to join immediately, or should they be forced to wait for approval?" setting for events on your website?',
                'multiOptions' => array(
                    '1' => 'Yes',
                    '0' => 'No',
                ),
                'value' => $coreSettings->getSetting('siteevent.invite.rsvp.automatically', 1)
            ));
        }
        $this->addElement('Radio', 'siteevent_invite_other_guests', array(
            'label' => 'Enable “Invited guests can invite other people as well” Setting',
            'description' => 'Do you want to display the setting "Invited guests can invite other people as well" in create event  and edit event pages?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('siteevent.invite.other.guests', 1),
            'onclick' => 'showInviteOtherGuest(this.value)',
        ));

        $this->addElement('Radio', 'siteevent_invite_other_automatically', array(
            'label' => 'Default “Invited guests can invite other people as well” Option',
            'description' => 'What default value do you want for "Invited guests can invite other people as well" setting for events on your website?',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No',
            ),
            'value' => $coreSettings->getSetting('siteevent.invite.other.automatically', 1),
        ));

        $this->addElement('Radio', 'siteevent_show_browse', array(
            'label' => 'Allow to Show Events',
            'description' => "Do you want to allow event owners to choose to show their events on Browse Events page and in various blocks? [This setting works for Event Creation page.]",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No',
            ),
            'value' => $coreSettings->getSetting('siteevent.show.browse', 1)
        ));

        $this->addElement('Radio', "siteevent_metakeyword", array(
            'label' => 'Meta Tags / Keywords',
            'description' => 'Do you want to enable event owners to add Meta Tags / Keywords for their events? (If enabled, then event owners will be able to add them from "Meta Keyword" section of their Event Dashboard.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('siteevent.metakeyword', 1),
        ));

        if (!$isTicketBasedEvent) {
            $this->addElement('Radio', 'siteevent_price', array(
                'label' => 'Allow Price',
                'description' => 'Do you want the Price field to be enabled for Events?',
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => $coreSettings->getSetting('siteevent.price', 0),
            ));
        }

        $this->addElement('Radio', 'siteevent_announcement', array(
            'label' => 'Announcements',
            'description' => 'Do you want announcements to be enabled for events? (If enabled, then event owner will be able to post announcements for their events from ‘Manage Announcements’ section of their Event Dashboard.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('siteevent.announcement', 1),
            'onclick' => 'showAnnouncements(this.value)'
        ));

        $this->addElement('Radio', 'siteevent_announcementeditor', array(
            'label' => 'TinyMCE Editor for Announcements',
            'description' => 'Do you want to allow tinymce editor for the announcements.',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('siteevent.announcementeditor', 1),
        ));

        $createFormFields = array(
            'venue' => 'Venue Name',
            'location' => 'Location',
            'tags' => 'Tags',
            'photo' => 'Photo',
            'description' => 'Description [Note: This setting will only work if description is not required.]',
            'overview' => 'Overview');
       
        if (!$isTicketBasedEvent) {
            $createFormFields = array_merge($createFormFields, array('price' => 'Price'));
        }
                
        $createFormFields = array_merge($createFormFields, array(
            'host' => 'Host',
            'postPrivacy' => 'Post Privacy',
            'viewPrivacy' => 'View Privacy',
            'commentPrivacy' => 'Comment Privacy',
            'discussionPrivacy' => 'Discussion Privacy',
            'photoPrivacy' => 'Photo Privacy',
            'videoPrivacy' => 'Video Privacy',
        ));

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventdocument')) {
            $createFormFields = array_merge($createFormFields, array('document' => 'Document Privacy'));
        }
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.guestconfimation', 0)) {
            $createFormFields = array_merge($createFormFields, array('guestLists' => 'Guests List Privacy'));
        }

        if (!$isTicketBasedEvent) {
            $createFormFields = array_merge($createFormFields, array(
                'rsvp' => '"When people try to join this event, should they be allowed to join immediately, or should they be forced to wait for approval?" Setting'
            ));
        }       

        $createFormFields = array_merge($createFormFields, array(
            'invite' => '"Invited guests can invite other people as well" Setting',
            'status' => 'Status',
            'search' => 'Show this event on browse page and in various blocks',
            'showHideAdvancedOptions' => 'Advanced Show / Hide Options'
        ));

        $this->addElement('MultiCheckbox', 'siteevent_createFormFields', array(
            'label' => 'Event Creation Fields',
            'description' => 'Choose the fields that you want to be available on the Event Creation page. Choosing less fields here could mean quicker event creation. Other fields that are enabled for events but not chosen here will appear in Event Dashboard.',
            'multiOptions' => $createFormFields,
            'value' => $coreSettings->getSetting('siteevent.createFormFields', array_keys($createFormFields)),
        ));

        $this->addElement('Hidden', 'is_remove_note', array('value' => 0, 'order' => 999));

        $this->addElement('Button', 'save', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}
