<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Level.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Admin_Settings_Video_Level extends Authorization_Form_Admin_Level_Abstract {

    public function init() {
        parent::init();

        // My stuff
        $this
                ->setTitle('Member Level Settings')
                ->setDescription('SITEVIDEO_FORM_ADMIN_LEVEL_DESCRIPTION');

        // Element: view
        $this->addElement('Radio', 'view', array(
            'label' => 'Allow Viewing of Videos?',
            'description' => 'Do you want to let members view videos? If set to no, some other settings on this page may not apply.',
            'multiOptions' => array(
                2 => 'Yes, allow members to view all videos, even private ones.',
                1 => 'Yes, allow viewing of  videos.',
                0 => 'No, do not allow  videos to be viewed.'
            ),
            'value' => ( $this->isModerator() ? 2 : 1 ),
        ));
        if (!$this->isModerator()) {
            unset($this->view->options[2]);
        }

        if (!$this->isPublic()) {

            // Element: create
            $this->addElement('Radio', 'create', array(
                'label' => 'Allow Posting of Videos?',
                'description' => 'Do you want to let members to post videos? If set to no, some other settings on this page may not apply. This is useful if you want members to be able to view videos, but only certain levels to be able to post videos.',
                'value' => 1,
                'multiOptions' => array(
                    1 => 'Yes, allow posting of videos.',
                    0 => 'No, do not allow videos to be posted.'
                ),
                'value' => 1,
            ));

            // Element: edit
            $this->addElement('Radio', 'edit', array(
                'label' => 'Allow Editing of Videos?',
                'description' => 'Do you want to let members of this level edit  videos?',
                'multiOptions' => array(
                    2 => 'Yes, allow members to edit all videos.',
                    1 => 'Yes, allow members to edit their own videos.',
                    0 => 'No, do not allow  videos to be edited.',
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->edit->options[2]);
            }

            // Element: delete
            $this->addElement('Radio', 'delete', array(
                'label' => 'Allow Deletion of Videos?',
                'description' => 'Do you want to let members of this level delete  videos?',
                'multiOptions' => array(
                    2 => 'Yes, allow members to delete all  videos.',
                    1 => 'Yes, allow members to delete their own  videos.',
                    0 => 'No, do not allow members to delete their  videos.',
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->delete->options[2]);
            }
            // Element: delete
            $this->addElement('Radio', 'download', array(
                'label' => 'Allow Download of Videos?',
                'description' => 'Do you want to let members of this level download  videos (Only for videos added from My computer)?',
                'multiOptions' => array(
                    2 => 'Yes, allow members to download all  videos.',
                    1 => 'Yes, allow members to download videos',
                    0 => 'No, do not allow members to download videos.',
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->download->options[2]);
            }

            // Element: comment
            $this->addElement('Radio', 'comment', array(
                'label' => 'Allow Commenting on Videos?',
                'description' => 'Do you want to let members of this level comment on  videos?',
                'multiOptions' => array(
                    2 => 'Yes, allow members to comment on all  videos, including private ones.',
                    1 => 'Yes, allow members to comment on videos.',
                    0 => 'No, do not allow members to comment on  videos.',
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if (!$this->isModerator()) {
                unset($this->comment->options[2]);
            }

            // Element: rate
            $this->addElement('Radio', 'rate', array(
                'label' => 'Allow Rating on  Videos?',
                'description' => 'Do you want to let members of this level rate on  videos?',
                'multiOptions' => array(
                    1 => 'Yes, allow members to rate on  videos.',
                    0 => 'No, do not allow members to rate on  videos.',
                ),
                'value' => 1,
            ));
            
            // Element: comment
            $this->addElement('Radio', 'video_password_protected', array(
                'label' => "Allow Password Protection on Videos?",
                'description' => "Do you want to let members of this level to protect their videos with password?",
                'multiOptions' => array(
                    1 => 'Yes, allow members to protect their videos with password.',
                    0 => 'No, do not allow members to protect their videos with password.',
                ),
                'value' => 1,
            ));

            // Element: auth_view
            $this->addElement('MultiCheckbox', 'auth_view', array(
                'label' => 'Video Privacy',
                'description' => 'SITEVIDEO_FORM_ADMIN_LEVEL_AUTHVIEW_DESCRIPTION',
                'multiOptions' => array(
                    'everyone' => 'Everyone',
                    'registered' => 'All Registered Members',
                    'owner_network' => 'Friends and Networks',
                    'owner_member_member' => 'Friends of Friends',
                    'owner_member' => 'Friends Only',
                    'owner' => 'Just Me'
                ),
                'value' => array('everyone', 'registered', 'owner_network', 'owner_member_member', 'owner_member', 'owner'),
            ));

            // Element: auth_comment
            $this->addElement('MultiCheckbox', 'auth_comment', array(
                'label' => 'Video Comment Options',
                'description' => 'SITEVIDEO_FORM_ADMIN_LEVEL_AUTHCOMMENT_DESCRIPTION',
                'multiOptions' => array(
                    'everyone' => 'Everyone',
                    'registered' => 'All Registered Members',
                    'owner_network' => 'Friends and Networks',
                    'owner_member_member' => 'Friends of Friends',
                    'owner_member' => 'Friends Only',
                    'owner' => 'Just Me'
                ),
                'value' => array('everyone', 'registered', 'owner_network', 'owner_member_member', 'owner_member', 'owner'),
            ));

            $this->addElement('Text', 'sitevideo_max_allowed_video', array(
                'label' => 'Maximum Allowed Videos',
                'description' => 'Enter the maximum number of videos that are allowed to be posted. This field must contain an integer, use zero for unlimited videos. ',
                'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.max.allowed.video', 0),
            ));
        }
    }

}
