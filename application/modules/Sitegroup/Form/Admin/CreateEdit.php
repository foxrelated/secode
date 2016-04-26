<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Form_Admin_CreateEdit extends Engine_Form {

    public function init() {

        $this->setTitle('Miscellaneous Settings')
                ->setDescription('The below settings govern various properties for groups on your website. By choosing fewer fields for group creation, you can make creating groups quicker on your website (Remaining fields can be configured from Group Dashboard).')
                ->setName('sitegroup_global');

        $coreSettings = Engine_Api::_()->getApi('settings', 'core');

        $this->addElement('Radio', 'sitegroup_description_allow', array(
            'label' => 'Allow Description',
            'description' => 'Do you want to allow group owners to write description for their groups?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitegroup.description.allow', 1),
            'onclick' => 'showDescription(this.value)'
        ));

        //VALUE FOR DESCRIPTION
        $this->addElement('Radio', 'sitegroup_requried_description', array(
            'label' => 'Description Required',
            'description' => 'Do you want to make Description a mandatory field for directory items / groups?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitegroup.requried.description', 1),
        ));

        //VALUE FOR CAPTCHA
        $this->addElement('Radio', 'sitegroup_requried_photo', array(
            'label' => 'Profile Photo Required',
            'description' => 'Do you want to make Profile Photo a mandatory field for directory items / groups?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitegroup.requried.photo', 0),
        ));
        $this->addElement('Radio', 'sitegroup_status_show', array(
            'label' => 'Open / Closed status in Search',
            'description' => 'Do you want the Status field (Open / Closed) in the search form widget? (This widget appears on the "Groups Home", "My Groups" and "Browse Groups" groups, and enables users to search and filter groups.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitegroup.status.show', 1),
        ));

        $this->addElement('Radio', 'sitegroup_profile_fields', array(
            'label' => 'Profile Information Fields',
            'description' => 'Do you want to display Profile Information Fields associated with the selected category while creation of directory items / groups?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitegroup.profile.fields', 1),
        ));

        //VALUE FOR ENABLE /DISABLE PRICE FIELD
        $this->addElement('Radio', 'sitegroup_price_field', array(
            'label' => 'Price Field',
            'description' => 'Do you want the Price field to be enabled for directory items / groups?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettings->getSetting('sitegroup.price.field', 1),
        ));

        $createFormFields = array(
            'location' => 'Location',
            'tags' => 'Tags',
            'photo' => 'Photo [Note: This setting will only work if photo is not required.]',
            'description' => 'Description [Note: This setting will only work if description is not required.]',
            'price' => 'Price',
            'viewPrivacy' => 'View Privacy',
            'commentPrivacy' => 'Comment Privacy',
            'allPostPrivacy' => 'Post Updates'
        );

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion')) {
            $createFormFields = array_merge($createFormFields, array('discussionPrivacy' => 'Discussion Privacy'));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum')) {
            $createFormFields = array_merge($createFormFields, array('photoPrivacy' => 'Photo Privacy'));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo')) {
            $createFormFields = array_merge($createFormFields, array('videoPrivacy' => 'Video Privacy'));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument')) {
            $createFormFields = array_merge($createFormFields, array('documentPrivacy' => 'Document Privacy'));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouppoll')) {
            $createFormFields = array_merge($createFormFields, array('pollPrivacy' => 'Poll Privacy'));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupnote')) {
            $createFormFields = array_merge($createFormFields, array('notePrivacy' => 'Note Privacy'));
        }

        if ((Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitegroup_group', 'item_module' => 'sitegroup')))) {
            $createFormFields = array_merge($createFormFields, array('eventPrivacy' => 'Event Privacy'));
        } elseif (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent')) {
            $createFormFields = array_merge($createFormFields, array('eventPrivacy' => 'Event Privacy'));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic')) {
            $createFormFields = array_merge($createFormFields, array('musicPrivacy' => 'Music Privacy'));
        }
        
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
            $createFormFields = array_merge($createFormFields, array('memberTitle' => 'What will members be called setting?'));
            $createFormFields = array_merge($createFormFields, array('memberInvite' => 'Invite member'));
            $createFormFields = array_merge($createFormFields, array('memberApproval' => 'Approve members?'));
        }
        
        $createFormFields = array_merge($createFormFields, array(
            'subGroupPrivacy' => 'Sub Group Creation Privacy',
            'claimThisGroup' => 'Claim this Group',
            'status' => 'Status',
            'search' => 'Show this group on browse group and in various blocks',
            'showHideAdvancedOptions' => 'Advanced Show / Hide Options'
        ));

        $this->addElement('MultiCheckbox', 'sitegroup_createFormFields', array(
            'label' => 'Group Creation Fields',
            'description' => 'Choose the fields that you want to be available on the Group Creation group. Choosing less fields here could mean quicker group creation. Other fields that are enabled for groups but not chosen here will appear in Group Dashboard.',
            'multiOptions' => $createFormFields,
            'value' => $coreSettings->getSetting('sitegroup.createFormFields', array_keys($createFormFields)),
        ));

        $this->addElement('Hidden', 'is_remove_note', array('value' => 0, 'order' => 999));

        $this->addElement('Button', 'save', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}