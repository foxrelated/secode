<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Levelchanel.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_Form_Admin_Settings_Levelchanel extends Authorization_Form_Admin_Level_Abstract {

  public function init() {

    parent::init();

    // My stuff
    $this->setTitle('Channels Member Level Settings')
            ->setDescription('These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.');

    // Element: view
    $this->addElement('Radio', 'view', array(
        'label' => 'Allow Viewing of Channels?',
        'description' => 'Do you want to let members view channel? If set to no, some other settings on this page may not apply. This is useful if you want members to be able to view channels, but only want certain levels to be able to create channels.',
        'multiOptions' => array(
            2 => 'Yes, allow viewing of all channels, even private ones.',
            1 => 'Yes, allow viewing of channels.',
            0 => 'No, do not allow channels to be viewed.',
        ),
        'value' => 1,
    ));
    if (!$this->isModerator()) {
      unset($this->view->options[2]);
    }

    if (!$this->isPublic()) {
      // Element: channel create
      $this->addElement('Radio', 'create', array(
          'label' => 'Allow Creation of Channels?',
          'description' => 'Do you want to let members create channels? If set to no, some other settings on this page may not apply. This is useful if you want members to be able to view channels, but only want certain levels to be able to create channels.',
          'multiOptions' => array(
              1 => 'Yes, allow creation of channels.',
              0 => 'No, do not allow channels to be created.'
          ),
          'value' => 1,
      ));

      // Element: channel edit
      $this->addElement('Radio', 'edit', array(
          'label' => 'Allow Editing of Channels?',
          'description' => 'Do you want to let members edit channels? If set to no, some other settings on this page may not apply. This is useful if you want members to be able to edit channels, but only want certain levels to be able to edit channels.',
          'multiOptions' => array(
              2 => 'Yes, allow members to edit all channels.',
              1 => 'Yes, allow members to edit their own channels.',
              0 => 'No, do not allow members to edit their channels.',
          ),
          'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if (!$this->isModerator()) {
        unset($this->edit->options[2]);
      }

      // Element: channel delete
      $this->addElement('Radio', 'delete', array(
          'label' => 'Allow Deletion of Channels?',
          'description' => 'Do you want to let members delete channels? If set to no, some other settings on this page may not apply. This is useful if you want members to be able to delete channels, but only want certain levels to be able to delete channels.',
          'multiOptions' => array(
              2 => 'Yes, allow members to delete all channels.',
              1 => 'Yes, allow members to delete their own channels.',
              0 => 'No, do not allow members to delete their channels.',
          ),
          'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if (!$this->isModerator()) {
        unset($this->delete->options[2]);
      }

      // Element: comment
      $this->addElement('Radio', 'comment', array(
          'label' => 'Allow Commenting on Channels?',
          'description' => 'Do you want to let members of this level comment on channels?',
          'multiOptions' => array(
              2 => 'Yes, allow members to comment on all channels, including private ones.',
              1 => 'Yes, allow members to comment on channels.',
              0 => 'No, do not allow members to comment on channels.',
          ),
          'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if (!$this->isModerator()) {
        unset($this->comment->options[2]);
      }

      // Element: auth_view for channel
      $this->addElement('MultiCheckbox', 'auth_view', array(
          'label' => 'Channel Privacy',
          'description' => 'Your members can choose from any of the options checked below when they decide who can see their channel. If you do not check any options, settings will default to the last saved configuration. If you select only one option, members of this level will not have a choice.',
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
          'label' => 'Channel Comment Options',
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
			$this->addElement('Radio', 'rating_chanel', array(
          'label' => 'Allow Rating on Channels?',
          'description' => 'Do you want to let members rate Chanels?',
          'multiOptions' => array(
              1 => 'Yes, allow rating on chanels.',
              0 => 'No, do not allow rating on chanels.'
          ),
          'value' => 1,
      ));
      // Element: max channel
      $this->addElement('Text', 'maxchannel', array(
          'label' => 'Maximum Allowed Channels',
          'description' => 'Enter the maximum number of allowed channels. The field must contain an integer, use zero for unlimited.',
          'validators' => array(
              array('Int', true),
              new Engine_Validate_AtLeast(0),
          ),
      ));
    }
  }

}
