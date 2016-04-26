<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Levelchanelphoto.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_Form_Admin_Settings_Levelchanelphoto extends Authorization_Form_Admin_Level_Abstract {

  public function init() {
    parent::init();

    // My stuff
    $this
            ->setTitle('Channel Photos Member Level Settings')
            ->setDescription('These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.');

    // Element: view
    $this->addElement('Radio', 'view', array(
        'label' => 'Allow Viewing of Channel Photos?',
        'description' => 'Do you want to let members view channel photos? If set to no, some other settings on this page may not apply. This is useful if you want members to be able to view channel photos, but only want certain levels to be able to create channel photos.',
        'multiOptions' => array(
            2 => 'Yes, allow viewing of all channel photos, even private ones.',
            1 => 'Yes, allow viewing of channel photos.',
            0 => 'No, do not allow channel photos to be viewed.',
        ),
        'value' => 1,
    ));
    if (!$this->isModerator()) {
      unset($this->view->options[2]);
    }

    if (!$this->isPublic()) {

      // Element: channel create
      $this->addElement('Radio', 'create', array(
          'label' => 'Allow Creation of Channel Photos?',
          'description' => 'Do you want to let members create channel photos? If set to no, some other settings on this page may not apply. This is useful if you want members to be able to view channel photos, but only want certain levels to be able to create channel photos.',
          'multiOptions' => array(
              1 => 'Yes, allow creation of channel photos.',
              0 => 'No, do not allow channel photos to be created.'
          ),
          'value' => 1,
      ));
      // Element: channel edit
      $this->addElement('Radio', 'edit', array(
          'label' => 'Allow Editing of Channel Photos?',
          'description' => 'Do you want to let members edit channel photos? If set to no, some other settings on this page may not apply. This is useful if you want members to be able to edit channel photos, but only want certain levels to be able to edit channel photos.',
          'multiOptions' => array(
              2 => 'Yes, allow members to edit all channel photos.',
              1 => 'Yes, allow members to edit their own channel photos.',
              0 => 'No, do not allow members to edit their channel photos.',
          ),
          'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if (!$this->isModerator()) {
        unset($this->edit->options[2]);
      }
      // Element: channel delete
      $this->addElement('Radio', 'delete', array(
          'label' => 'Allow Deletion of Channel Photos?',
          'description' => 'Do you want to let members delete channel photos? If set to no, some other settings on this page may not apply. This is useful if you want members to be able to delete channel photos, but only want certain levels to be able to delete channel photos.',
          'multiOptions' => array(
              2 => 'Yes, allow members to delete all channel photos.',
              1 => 'Yes, allow members to delete their own channel photos.',
              0 => 'No, do not allow members to delete their channel photos.',
          ),
          'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if (!$this->isModerator()) {
        unset($this->delete->options[2]);
      }

      // Element: comment
      $this->addElement('Radio', 'comment', array(
          'label' => 'Allow Commenting on Channel Photos?',
          'description' => 'Do you want to let members of this level comment on channel photos?',
          'multiOptions' => array(
              2 => 'Yes, allow members to comment on all channel photos, including private ones.',
              1 => 'Yes, allow members to comment on channel photos.',
              0 => 'No, do not allow members to comment on channel photos.',
          ),
          'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if (!$this->isModerator()) {
        unset($this->comment->options[2]);
      }

      // Element: auth_view for channel
      $this->addElement('MultiCheckbox', 'auth_view', array(
          'label' => 'Channel Photos Privacy',
          'description' => 'Your members can choose from any of the options checked below when they decide who can see their channel photos. If you do not check any options, settings will default to the last saved configuration. If you select only one option, members of this level will not have a choice.',
          'multiOptions' => array(
              'everyone' => 'Everyone',
              'registered' => 'All Registered Members',
              'owner_network' => 'Friends and Networks',
              'owner_member_member' => 'Friends of Friends',
              'owner_member' => 'Friends Only',
              'owner' => 'Just Me',
          ),
          'value' => array('everyone', 'owner_network', 'owner_member_member', 'owner_member', 'owner'),
      ));

      // Element: auth_comment
      $this->addElement('MultiCheckbox', 'auth_comment', array(
          'label' => 'Channel Photos Comment Options',
          'description' => '',
          'multiOptions' => array(
              'everyone' => 'Everyone',
              'registered' => 'All Registered Members',
              'owner_network' => 'Friends and Networks',
              'owner_member_member' => 'Friends of Friends',
              'owner_member' => 'Friends Only',
              'owner' => 'Just Me'
          ),
          'value' => array('everyone', 'owner_network', 'owner_member_member', 'owner_member', 'owner'),
      ));
    }
  }

}
