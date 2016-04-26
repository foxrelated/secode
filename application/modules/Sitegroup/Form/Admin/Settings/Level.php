<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Level.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract {

    public function init() {
        parent::init();

        // My stuff
        $this
                ->setTitle('Member Level Settings')
                ->setDescription("These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below. (Note: If packages are enabled from global settings, then some member level settings will not be available as those feature settings for groups will now depend on packages.)");

        $isEnabledPackage = Engine_Api::_()->sitegroup()->hasPackageEnable();

        // Element: view
        $this->addElement('Radio', 'view', array(
            'label' => 'Allow Viewing of Groups?',
            'description' => 'Do you want to let members view groups? If set to no, some other settings on this group may not apply.',
            'multiOptions' => array(
                2 => 'Yes, allow viewing of all groups, even private ones.',
                1 => 'Yes, allow viewing of groups.',
                0 => 'No, do not allow groups to be viewed.',
            ),
            'value' => ( $this->isModerator() ? 2 : 1 ),
        ));
        if (!$this->isModerator()) {
            unset($this->view->options[2]);
        }

        if (!$this->isPublic()) {
            // Element: create
            $this->addElement('Radio', 'create', array(
                'label' => 'Allow Creation of Groups?',
                'description' => 'Do you want to let members create groups? If set to no, some other settings on this group may not apply. This is useful if you want members to be able to view groups, but only certain levels to be able to create groups.',
                'multiOptions' => array(
                    1 => 'Yes, allow creation of groups.',
                    0 => 'No, do not allow groups to be created.'
                ),
                'value' => 1,
            ));


            // Element: edit
            $this->addElement('Radio', 'edit', array(
                'label' => 'Allow Editing of Groups?',
                'description' => 'Do you want to let members edit groups? If set to no, some other settings on this group may not apply.',
                'multiOptions' => array(
                    2 => 'Yes, allow members to edit all groups.',
                    1 => 'Yes, allow members to edit their own groups.',
                    0 => 'No, do not allow members to edit their groups.',
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->edit->options[2]);
            }

            // Element: delete
            $this->addElement('Radio', 'delete', array(
                'label' => 'Allow Deletion of Groups?',
                'description' => 'Do you want to let members delete groups? If set to no, some other settings on this group may not apply.',
                'multiOptions' => array(
                    2 => 'Yes, allow members to delete all groups.',
                    1 => 'Yes, allow members to delete their own groups.',
                    0 => 'No, do not allow members to delete their groups.',
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->delete->options[2]);
            }

            // Element: comment
            $this->addElement('Radio', 'comment', array(
                'label' => 'Allow Commenting on Groups?',
                'description' => 'Do you want to let members of this level comment on groups?',
                'multiOptions' => array(
                    2 => 'Yes, allow members to comment on all groups, including private ones.',
                    1 => 'Yes, allow members to comment on groups.',
                    0 => 'No, do not allow members to comment on groups.',
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->comment->options[2]);
            }

            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.manageadmin', 1)) {
                $ownerTitle = "Group Admins";
            } else {
                $ownerTitle = "Just Me";
            }

            $privacyArray = array(
                'everyone' => 'Everyone',
                'registered' => 'All Registered Members',
                'owner_network' => 'Friends and Networks',
                'owner_member_member' => 'Friends of Friends',
                'owner_member' => 'Friends Only',
                    //	'owner' => $ownerTitle,
            );
            $privacyValueArray = array('everyone', 'owner_network', 'owner_member_member', 'owner_member');
            $sitegroupmemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
            if ($sitegroupmemberEnabled) {
                $privacyArray['member'] = 'Group Members Only';
                $privacyValueArray[] = 'member';
            }
            $privacyArray['owner'] = $ownerTitle;
            $privacyValueArray[] = 'owner';

            //START SUBGROUP WORK.      
            // Element:sub create
            $this->addElement('Radio', 'sspcreate', array(
                'label' => 'Allow Creation of Sub Groups?',
                'description' => 'Do you want to let members create sub groups? If set to no, some other settings on this group may not apply. This is useful if you want members to be able to create sub groups, but only certain levels to be able to create sub groups.',
                'multiOptions' => array(
                    1 => 'Yes, allow creation of sub groups.',
                    0 => 'No, do not allow sub groups to be created.'
                ),
                'value' => 1,
            ));

            $privacy_array = array(
                'registered' => 'All Registered Members',
                'owner_network' => 'Friends and Networks',
                'owner_member_member' => 'Friends of Friends',
                'owner_member' => 'Friends Only',
                    //	'owner' => $ownerTitle,
            );
            $privacy_value_array = array('everyone', 'owner_network', 'owner_member_member', 'owner_member');
            $sitegroupmemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
            if ($sitegroupmemberEnabled) {
                $privacy_array['member'] = 'Group Members Only';
                $privacy_value_array[] = 'member';
            }
            $privacy_array['like_member'] = 'Who Liked This Group';
            $privacy_value_array[] = 'like_member';

            $privacy_array['owner'] = $ownerTitle;
            $privacy_value_array[] = 'owner';

            // Element: auth_subgroup create
            $this->addElement('MultiCheckbox', 'auth_sspcreate', array(
                'label' => 'Sub-Group Creation Options',
                'description' => 'Your users can choose from any of the options checked below when they decide who can create the sub-groups in their groups. If you do not check any options, everyone will be allowed to create sub-groups.',
                'multiOptions' => $privacy_array
            ));
            //Element: subgroup
            // Element: auth_view
            $this->addElement('MultiCheckbox', 'auth_view', array(
                'label' => 'Group Privacy',
                'description' => 'Your members can choose from any of the options checked below when they decide who can see their groups. These options appear on your members\' "Create New Group" and "Edit Group" groups. If you do not check any options, everyone will be allowed to view groups.',
                'multiOptions' => $privacyArray,
                'value' => $privacyValueArray
            ));

            // Element: auth_comment
            $this->addElement('MultiCheckbox', 'auth_comment', array(
                'label' => 'Group Comment Options',
                'description' => 'Your members can choose from any of the options checked below when they decide who can post comments on their groups. These options appear on your members\' "Create New Group" and "Edit Group" groups. If you do not check any options, everyone will be allowed to post comments on groups.',
                'multiOptions' => $privacyArray,
                'value' => $privacyValueArray
            ));
        }

        if (!$this->isPublic() && empty($isEnabledPackage)) {

            $privacy_array = array(
                'registered' => 'All Registered Members',
                'owner_network' => 'Friends and Networks',
                'owner_member_member' => 'Friends of Friends',
                'owner_member' => 'Friends Only',
                    //	'owner' => $ownerTitle,
            );
            $privacy_value_array = array('everyone', 'owner_network', 'owner_member_member', 'owner_member');
            $sitegroupmemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
            if ($sitegroupmemberEnabled) {
                $privacy_array['member'] = 'Group Members Only';
                $privacy_value_array[] = 'member';
            }
            $privacy_array['like_member'] = 'Who Liked This Group';
            $privacy_value_array[] = 'like_member';

            $privacy_array['owner'] = $ownerTitle;
            $privacy_value_array[] = 'owner';


            //Element: approved
            $this->addElement('Radio', 'approved', array(
                'label' => 'Group Approval Moderation',
                'description' => 'Do you want new group to be automatically approved?',
                'multiOptions' => array(
                    1 => 'Yes, automatically approve group.',
                    0 => 'No, site admin approval will be required for all groups.'
                ),
                'value' => 1,
            ));

            //Element: sponsored
            $this->addElement('Radio', 'sponsored', array(
                'label' => 'Group Sponsored Moderation',
                'description' => 'Do you want new group to be automatically made sponsored?',
                'multiOptions' => array(
                    1 => 'Yes, automatically make group sponsored.',
                    0 => 'No, site admin will be making group sponsored.'
                ),
                'value' => 0,
            ));

            //Element: featured
            $this->addElement('Radio', 'featured', array(
                'label' => 'Group Featured Moderation',
                'description' => 'Do you want new group to be automatically made featured?',
                'multiOptions' => array(
                    1 => 'Yes, automatically make group featured.',
                    0 => 'No, site admin will be making group featured.'
                ),
                'value' => 0,
            ));

            $this->addElement('Radio', 'tfriend', array(
                'label' => 'Tell a friend',
                'description' => 'Do you want to show "Tell a friend" link on the Profile Group of groups created by members of this level? (Using this feature, viewers will be able to email the group to their friends.)',
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => 1,
            ));

            $this->addElement('Radio', 'print', array(
                'label' => 'Print',
                'description' => 'Do you want to show "Print Group" link on the Profile Group of groups created by members of this level? (If set to no, viewers will not to be able to print information of the groups.)',
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => 1,
            ));


            $this->addElement('Radio', 'overview', array(
                'label' => 'Overview',
                'description' => 'Do you want to enable Overview for groups created by members of this level? (If set to no, neither the overview widget will be shown on the Group Profile nor members will be able to compose or edit the overview of their groups.)',
                'multiOptions' => array(
                    //2 => 'Yes, show overview of the groups, including private ones.',
                    1 => 'Yes',
                    0 => 'No'
                ),
                //'value' => ( $this->isModerator() ? 2 : 1 ),
                'value' => 1,
            ));

            $this->addElement('Radio', 'map', array(
                'label' => 'Location Map',
                'description' => 'Do you want to enable Location Map for groups created by members of this level? (If set to no, neither the map widget will be shown on the Group Profile nor members will be able to specify location of their groups to be shown in the map.)',
                'multiOptions' => array(
                    //2 => 'Yes show map of the groups, including private ones.',
                    1 => 'Yes',
                    0 => 'No'
                ),
                //'value' => ( $this->isModerator() ? 2 : 1 ),
                'value' => 1,
            ));


            $this->addElement('Radio', 'insight', array(
                'label' => 'Insights',
                'description' => 'Do you want to allow members of this level to view insights of their groups? (Insights for groups show graphical statistics and other metrics such as views, likes, comments, active users, etc over different durations and time summaries. If set to no, neither insights will be shown nor the periodic, auto-generated emails containing Group insights will be send to the group admins who belong to this level.)',
                'multiOptions' => array(
                    //2 => 'Yes, allow them to view the insights of the groups, including private ones.',
                    1 => 'Yes',
                    0 => 'No'
                ),
                //'value' => ( $this->isModerator() ? 2 : 1 ),
                'value' => 1,
            ));


            $this->addElement('Radio', 'contact', array(
                'label' => 'Contact Details',
                'description' => 'Do you want to enable Contact Details for the groups created by members of this level? (If set to no, neither the contact details will be shown on the info and browse groups nor members will be able to mention them for their groups\' entity.)',
                'multiOptions' => array(
                    //2 => 'Yes, enable contact details for the groups, including private ones.',
                    1 => 'Yes',
                    0 => 'No'
                ),
                'onclick' => 'contactoption(this.value)',
                //'value' => ( $this->isModerator() ? 2 : 1 ),
                'value' => 1,
            ));


            $this->addElement('MultiCheckbox', 'contact_detail', array(
                'label' => 'Specific Contact Details',
                'description' => 'Which of the following contact details you want to be specified by members of this level in the "Contact Details" section of the Group Dashboard?',
                'multiOptions' => array(
                    'phone' => 'Phone',
                    'website' => 'Website',
                    'email' => 'Email',
                ),
                'value' => array('phone', 'website', 'email')
            ));

//      $this->addElement('Radio', 'foursquare', array(
//          'label' => 'Save To Foursquare Button',
//          'description' => "Do you want to enable 'Save to foursquare' buttons for the groups created by members of this level? (Using this, 'Save to foursquare' buttons will be shown on profiles of groups having location information. These buttons will enable group visitors to add the group's place or tip to their foursquare To-Do List. Group Admins will get this option in the \"Marketing\" section of their Dashboard.)",
//          'multiOptions' => array(
//              1 => 'Yes',
//              0 => 'No'
//          ),
//          'value' => 1,
//      ));
            // Element:Twitter
            $sitegrouptwitterEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouptwitter');
            if ($sitegrouptwitterEnabled) {
                $this->addElement('Radio', 'twitter', array(
                    'label' => 'Display Twitter Updates',
                    'description' => "Enable displaying of Twitter Updates for groups of this package. (Using this, group admins will be able to display their Twitter Updates on their Group profile. Group Admins will get the option for entering their Twitter username in the \"Marketing\" section of their Dashboard. From the Layout Editor, you can choose to place the Twitter Updates widget either in the Tabs container or in the sidebar on Group Profile.)",
                    'multiOptions' => array(
                        1 => 'Yes',
                        0 => 'No'
                    ),
                    'value' => 1,
                ));
            }

            $this->addElement('Radio', 'sendupdate', array(
                'label' => 'Send an Update',
                'description' => "Do you want to enable 'Send an Update' for the groups created by members of this level? (Using this, group admins will be able to send an update for their groups' entity. Group Admins will get this option in the \"Marketing\" section of their Dashboard.)",
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => 1,
            ));
            $sitegroupFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouplikebox');
            if ($sitegroupFormEnabled) {
                $this->addElement('Radio', 'likebox', array(
                    'label' => 'External Embeddable Badge / Like Box',
                    'description' => "Do you want group admins to be able to generate code for Embeddable Badges / Like Boxes for groups created by a member of this level? (If enabled, group admins of such groups will be able to generate code to embed their external group badges in other websites / blogs to promote their group from Marketing section of group dashboard. Group Admins will also have to belong to this member level to generate code.)",
                    'multiOptions' => array(
                        1 => 'Yes',
                        0 => 'No'
                    ),
                    'value' => 1,
                ));
            }
            //START SITEGROUPBADGES PLUGIN WORK
            $sitegroupFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupbadge');
            if ($sitegroupFormEnabled) {
                $this->addElement('Radio', 'badge', array(
                    'label' => 'Badge Requesting',
                    'description' => 'Do you want group admins to be able to request a badge for their group created by a member of this level? (If enabled, group admins of such groups will be able to request a badge from their group dashboard. You will be able to manage badge requests and assign badges from the admin panel of Badges Extension. Group Admins will also have to belong to this member level to request a badge.)',
                    'multiOptions' => array(
                        //2 => 'Yes, Private ones also',
                        1 => 'Yes',
                        0 => 'No'
                    ),
                    //'value' => ( $this->isModerator() ? 2 : 1 ),
                    'value' => 1,
                ));
            }

            //START SITEGROUPDOCUMENT PLUGIN WORK
            $sitegroupDocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument');
            if ($sitegroupDocumentEnabled) {
                $this->addElement('Radio', 'sdcreate', array(
                    'label' => 'Documents in Groups',
                    'description' => 'Do you want Documents to be available to Groups created by members of this level? This setting will also apply to ability of users of this level to create Documents in Groups.',
                    'multiOptions' => array(
                        //2 => 'Yes, allow members to create documents in all groups, including private ones.',
                        1 => 'Yes',
                        0 => 'No',
                    ),
                    //'value' => ( $this->isModerator() ? 2 : 1 ),
                    'value' => 1,
                ));

                $this->addElement('MultiCheckbox', 'auth_sdcreate', array(
                    'label' => 'Document Creation Options',
                    'description' => 'Your users can choose from any of the options checked below when they decide who can create the documents in their group. If you do not check any options, everyone will be allowed to create.',
                    'multiOptions' => $privacy_array
//                 array(
//                         'registered' => 'All Registered Members',
//                         'owner_network' => 'Friends and Networks',
//                         'owner_member_member' => 'Friends of Friends',
//                         'owner_member' => 'Friends Only',
//                         'owner' => $ownerTitle,
//                         'member' => $memberTitle,
//                 )
                ));
            }
            //END SITEGROUPDOCUMENT PLUGIN WORK
            //START SITEGROUPEVENT PLUGIN WORK
            $sitegroupEventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent');
            if ($sitegroupEventEnabled || (Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitegroup_group', 'item_module' => 'sitegroup')))) {
                $this->addElement('Radio', 'secreate', array(
                    'label' => 'Events in Groups',
                    'description' => 'Do you want Events to be available to Groups created by members of this level? This setting will also apply to ability of users of this level to create Events in Groups.',
                    'multiOptions' => array(
                        //2 => 'Yes, allow members to create events in all groups, including private ones.',
                        1 => 'Yes',
                        0 => 'No',
                    ),
                    //'value' => ( $this->isModerator() ? 2 : 1 ),
                    'value' => 1,
                ));

                //START SITEGROUPEVENT PLUGIN WORK
                $this->addElement('MultiCheckbox', 'auth_secreate', array(
                    'label' => 'Event Creation Options',
                    'description' => 'Your users can choose from any of the options checked below when they decide who can create the events in their group. If you do not check any options, everyone will be allowed to create.',
                    'multiOptions' => $privacy_array
//                 array(
//                         'registered' => 'All Registered Members',
//                         'owner_network' => 'Friends and Networks',
//                         'owner_member_member' => 'Friends of Friends',
//                         'owner_member' => 'Friends Only',
//                         'owner' => $ownerTitle,
//                         'member' => $memberTitle
//                 )
                ));
            }
            //END SITEGROUPEVENT PLUGIN WORK
            //START SITEGROUPOFFER PLUGIN WORK
            $sitegroupFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupform');
            if ($sitegroupFormEnabled) {
                $this->addElement('Radio', 'form', array(
                    'label' => 'Form',
                    'description' => 'Do you want Forms to be available to Groups created by members of this level? (The Form on a Group will contain questions added by group admins. If set to No, neither the form widget will be shown on the Group Profile nor the group admins will be able to add questions to the Form from Group Dashboard. Group Admins will also have to belong to this member level to manage form.)',
                    'multiOptions' => array(
                        //2 => 'Yes, Private ones also',
                        1 => 'Yes',
                        0 => 'No'
                    ),
                    //'value' => ( $this->isModerator() ? 2 : 1 ),
                    'value' => 1,
                ));
            }
            //END SITEGROUPOFFER PLUGIN WORK
            //START SITEGROUPINVITE PLUGIN WORK
            $sitegroupInviteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupinvite');
            if ($sitegroupInviteEnabled) {
                $this->addElement('Radio', 'invite', array(
                    'label' => 'Invite & Promote',
                    'description' => 'Do you want members of this level to be able to invite their friends to the groups? (If set to no, "Invite your Friends" link will not appear on the Group Profile of their groups.)',
                    'multiOptions' => array(
                        1 => 'Yes',
                        0 => 'No'
                    ),
                    'value' => 1,
                ));
            }
            //END SITEGROUPINVITE PLUGIN WORK
            //START SITEGROUPNOTE PLUGIN WORK
            $sitegroupNoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupnote');
            if ($sitegroupNoteEnabled) {
                $this->addElement('Radio', 'sncreate', array(
                    'label' => 'Notes in Groups',
                    'description' => 'Do you want Notes to be available to Groups created by members of this level? This setting will also apply to ability of users of this level to create Notes in Groups.',
                    'multiOptions' => array(
                        //2 => 'Yes, allow members to create notes in all groups, including private ones.',
                        1 => 'Yes',
                        0 => 'No',
                    ),
                    //'value' => ( $this->isModerator() ? 2 : 1 ),
                    'value' => 1,
                ));


                $this->addElement('MultiCheckbox', 'auth_sncreate', array(
                    'label' => 'Note Creation Options',
                    'description' => 'Your users can choose from any of the options checked below when they decide who can create the notes in their group. If you do not check any options, everyone will be allowed to create.',
                    'multiOptions' => $privacy_array
//                 array(
//                         'registered' => 'All Registered Members',
//                         'owner_network' => 'Friends and Networks',
//                         'owner_member_member' => 'Friends of Friends',
//                         'owner_member' => 'Friends Only',
//                         'owner' => $ownerTitle,
//                         'member' => $memberTitle,
//                 )
                ));
            }
            //END SITEGROUPNOTE PLUGIN WORK
            //START SITEGROUPOFFER PLUGIN WORK
            $sitegroupOfferEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupoffer');
            if ($sitegroupOfferEnabled) {
                $this->addElement('Radio', 'offer', array(
                    'label' => 'Offer',
                    'description' => 'Do you want to let members of this level to show offers for their groups? (If set to no, neither the offer widget will be shown on their Group Profiles nor they will be able to create them for their groups.)',
                    'multiOptions' => array(
                        //2 => 'Yes, allow them to create offers in the groups, including private ones.',
                        1 => 'Yes',
                        0 => 'No'
                    ),
                    //'value' => ( $this->isModerator() ? 2 : 1 ),
                    'value' => 1,
                ));
            }
            //END SITEGROUPOFFER PLUGIN WORK
            //START DISCUSSION PRIVACY WORK
            $sitegroupDiscussionEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion');
            if ($sitegroupDiscussionEnabled) {
                $this->addElement('Radio', 'sdicreate', array(
                    'label' => 'Discussion Topics in Groups',
                    'description' => 'Do you want Discussion Topics to be available to Groups created by members of this level? This setting will also apply to ability of users of this level to post discussion topics in Groups.',
                    'multiOptions' => array(
                        //2 => 'Yes, allow photo uploading to all groups, including private ones.',
                        1 => 'Yes',
                        0 => 'No'
                    ),
                    //'value' => ( $this->isModerator() ? 2 : 1 ),
                    'value' => 1,
                ));

                $this->addElement('MultiCheckbox', 'auth_sdicreate', array(
                    'label' => 'Discussion Topics Post Options',
                    'description' => 'Your users can choose from any of the options checked below when they decide who can post the discussion topics in their group. If you do not check any options, everyone will be allowed to post.',
                    'multiOptions' => $privacy_array
//                 array(
//                         'registered' => 'All Registered Members',
//                         'owner_network' => 'Friends and Networks',
//                         'owner_member_member' => 'Friends of Friends',
//                         'owner_member' => 'Friends Only',
//                         'owner' => $ownerTitle,
//                         'member' => $memberTitle,
//                 )
                ));
            }
            //END DISCUSSION PRIVACY WORK     
            //START PHOTO PRIVACY WORK
            $sitegroupAlbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum');
            if ($sitegroupAlbumEnabled) {
                $this->addElement('Radio', 'spcreate', array(
                    'label' => 'Photos in Groups',
                    'description' => 'Do you want Photos to be available to Groups created by members of this level? This setting will also apply to ability of users of this level to upload Photos in Groups.',
                    'multiOptions' => array(
                        //2 => 'Yes, allow photo uploading to all groups, including private ones.',
                        1 => 'Yes',
                        0 => 'No'
                    ),
                    //'value' => ( $this->isModerator() ? 2 : 1 ),
                    'value' => 1,
                ));


                $this->addElement('MultiCheckbox', 'auth_spcreate', array(
                    'label' => 'Photo Upload Options',
                    'description' => 'Your users can choose from any of the options checked below when they decide who can upload the photos in their group. If you do not check any options, everyone will be allowed to create.',
                    'multiOptions' => $privacy_array
//                 array(
//                         'registered' => 'All Registered Members',
//                         'owner_network' => 'Friends and Networks',
//                         'owner_member_member' => 'Friends of Friends',
//                         'owner_member' => 'Friends Only',
//                         'owner' => $ownerTitle,
//                         'member' => $memberTitle,
//                 )
                ));
            }
            //END PHOTO PRIVACY WORK
            //START SITEGROUPPOLL PLUGIN WORK
            $sitegroupPollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouppoll');
            if ($sitegroupPollEnabled) {
                $this->addElement('Radio', 'splcreate', array(
                    'label' => 'Polls in Groups',
                    'description' => 'Do you want Polls to be available to Groups created by members of this level? This setting will also apply to ability of users of this level to create Polls in Groups.',
                    'multiOptions' => array(
                        //2 => 'Yes, allow members to create polls in all groups, including private ones.',
                        1 => 'Yes',
                        0 => 'No',
                    ),
                    //'value' => ( $this->isModerator() ? 2 : 1 ),
                    'value' => 1,
                ));

                $this->addElement('MultiCheckbox', 'auth_splcreate', array(
                    'label' => 'Poll Creation Options',
                    'description' => 'Your users can choose from any of the options checked below when they decide who can create the polls in their group. If you do not check any options, everyone will be allowed to create.',
                    'multiOptions' => $privacy_array
//                 array(
//                         'registered' => 'All Registered Members',
//                         'owner_network' => 'Friends and Networks',
//                         'owner_member_member' => 'Friends of Friends',
//                         'owner_member' => 'Friends Only',
//                         'owner' => $ownerTitle,
//                         'member' => $memberTitle,
//                 )
                ));
            }
            //END SITEGROUPPOLL PLUGIN WORK
            //START SITEGROUPVIDEO PLUGIN WORK
            $sitegroupVideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo');
            if ($sitegroupVideoEnabled || (Engine_Api::_()->hasModuleBootstrap('sitevideo') && Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitegroup_group', 'item_module' => 'sitegroup')))) {
                $this->addElement('Radio', 'svcreate', array(
                    'label' => 'Videos in Groups',
                    'description' => 'Do you want Videos to be available to Groups created by members of this level? This setting will also apply to ability of users of this level to create Videos in Groups.',
                    'multiOptions' => array(
                        //2 => 'Yes, allow members to create videos in all groups, including private ones.',
                        1 => 'Yes',
                        0 => 'No',
                    ),
                    //'value' => ( $this->isModerator() ? 2 : 1 ),
                    'value' => 1,
                ));

                $this->addElement('MultiCheckbox', 'auth_svcreate', array(
                    'label' => 'Video Creation Options',
                    'description' => 'Your users can choose from any of the options checked below when they decide who can create the videos in their group. If you do not check any options, everyone will be allowed to create.',
                    'multiOptions' => $privacy_array
//                 array(
//                         'registered' => 'All Registered Members',
//                         'owner_network' => 'Friends and Networks',
//                         'owner_member_member' => 'Friends of Friends',
//                         'owner_member' => 'Friends Only',
//                         'owner' => $ownerTitle,
//                         'member' => $memberTitle,
//                 )
                ));
            }
            //END SITEGROUPVIDEO PLUGIN WORK
            // Element : profile
            $this->addElement('Radio', 'profile', array(
                'label' => 'Profile Creation',
                'description' => 'Do you want members of this level to create profiles for their groups? (Using this feature, members will be able to create a profile for their Group and fill the corresponding details which will be displayed on info groups. If set to no, "Profile Types" link will not be shown on the Group Dashboard.)',
                'multiOptions' => array(
                    '1' => 'Allow profile creation with all custom Fields.',
                    '2' => 'Allow profile creation with only below selected custom Fields.',
                    '0' => 'Do not allow the custom profile creation.',
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
        }
        //START SITEGROUPMUSIC PLUGIN WORK
        $sitegroupMusicEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic');
        if ($sitegroupMusicEnabled) {
            $this->addElement('Radio', 'smcreate', array(
                'label' => 'Music in Groups',
                'description' => 'Do you want Music to be available to Groups created by members of this level? This setting will also apply to ability of users of this level to create Music in Groups.',
                'multiOptions' => array(
                    //2 => 'Yes, allow members to create notes in all groups, including private ones.',
                    1 => 'Yes',
                    0 => 'No',
                ),
                //'value' => ( $this->isModerator() ? 2 : 1 ),
                'value' => 1,
            ));


            $this->addElement('MultiCheckbox', 'auth_smcreate', array(
                'label' => 'Music Creation Options',
                'description' => 'Your users can choose from any of the options checked below when they decide who can create the music in their group. If you do not check any options, everyone will be allowed to create.',
                'multiOptions' => $privacy_array
//                 array(
//                         'registered' => 'All Registered Members',
//                         'owner_network' => 'Friends and Networks',
//                         'owner_member_member' => 'Friends of Friends',
//                         'owner_member' => 'Friends Only',
//                         'owner' => $ownerTitle,
//                         'member' => $memberTitle,
//                 )
            ));
        }
        //END SITEGROUPMUSIC PLUGIN WORK
        //START SITEGROUPINTREGRATION PLUGIN WORK//
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupintegration')) {
            $mixSettingsResults = Engine_Api::_()->getDbtable('mixsettings', 'sitegroupintegration')->getIntegrationItems();
            foreach ($mixSettingsResults as $modNameValue) {

                $Params = Engine_Api::_()->sitegroupintegration()->integrationParams($modNameValue['resource_type'], $modNameValue['listingtype_id'], '', $modNameValue['item_title']);

                $title = $Params['level_setting_title'];
                $singular = $Params['singular'];
                $plugin_name = $Params['plugin_name'];

                $description = 'Do you want to let members of this level to add ' . $singular . ' from "' . $plugin_name . '" to Groups / Communities? (If set to Yes, then group admins will get this option in the “Apps” section of their dashboard.)';

                $description = Zend_Registry::get('Zend_Translate')->_($description);

                $this->addElement('Radio', $modNameValue['resource_type'] . '_' . $modNameValue['listingtype_id'], array(
                    'label' => $title,
                    'description' => $description,
                    'multiOptions' => array(
                        1 => 'Yes',
                        0 => 'No',
                    ),
                    'value' => 1,
                ));
            }
        }
        //END SITEGROUPINTREGRATION PLUGIN WORK//
        //START SITEGROUPMEMBER PLUGIN WORK
        $sitegroupMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
        if ($sitegroupMemberEnabled) {
            $this->addElement('Radio', 'smecreate', array(
                'label' => 'Member in Groups',
                'description' => 'Do you want Member to be available to Groups join by members of this level? This setting will also apply to ability of users of this level to join Member in Groups.',
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No',
                ),
                'value' => 1,
            ));
        }
        //START SITEGROUPMEMBER PLUGIN WORK
        }

        if (!$this->isPublic()) {
            // Element: style
            $this->addElement('Radio', 'style', array(
                'label' => 'Allow Custom CSS Styles?',
                'description' => 'If you enable this feature, your members will be able to customize the colors and fonts of their groups by altering their CSS styles.',
                'multiOptions' => array(
                    1 => 'Yes, enable custom CSS styles.',
                    0 => 'No, disable custom CSS styles.',
                ),
                'value' => 1,
            ));
        }
        // Element: claim
        $this->addElement('Radio', 'claim', array(
            'label' => 'Claim Groups',
            'description' => 'Do you want members of this level to be able to claim groups? (This will also depend on other settings for claiming like in global settings, manage claims, setting while creation of group, etc.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => 1,
        ));
        if (!$this->isPublic()) {
            // Element: max
            $this->addElement('Text', 'max', array(
                'label' => 'Maximum Allowed Groups',
                'description' => 'Enter the maximum number of directory items / groups that members of this level can create. This field must contain an integer; use zero for unlimited.',
                'validators' => array(
                    array('Int', true),
                    new Engine_Validate_AtLeast(0),
                ),
            ));
        }
    }

}

?>