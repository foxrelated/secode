<?php
/**
 * Younet
 *
 * @category   Application_Extensions
 * @package    Ynfundraising
 * @author     trunglt
 */
class Ynfundraising_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract
{
  public function init()
  {
    parent::init();

    // My stuff
    $this
      ->setTitle('Member Level Settings')
      ->setDescription("YNFUNDRAISING_FORM_ADMIN_LEVEL_DESCRIPTION");

    // Element: view
    $this->addElement('Radio', 'view', array(
      'label' => 'Allow Viewing of Campaigns?',
      'description' => 'Do you want to let members view campaigns? If set to no, some other settings on this page may not apply.',
      'multiOptions' => array(
        2 => 'Yes, allow viewing of all campaigns, even private ones.',
        1 => 'Yes, allow viewing of campaigns.',
        0 => 'No, do not allow campaigns to be viewed.',
      ),
      'value' => ( $this->isModerator() ? 2 : 1 ),
    ));
    if( !$this->isModerator() ) {
      unset($this->view->options[2]);
    }

	if($this->isPublic())
	{
		// Element: donate
      $this->addElement('Radio', 'donate', array(
      		'label' => 'Allow Donating on Campaigns?',
      		'description' => 'Do you want to let guests donate on campaigns?',
      		'multiOptions' => array(
      				1 => 'Yes, allow guests to donate on campaigns.',
      				0 => 'No, do not allow guests to donate on campaigns.',
      		),
      		'value' => 0,
      ));
	}

    if( !$this->isPublic() ) {

      // Element: create
      $this->addElement('Radio', 'create', array(
        'label' => 'Allow Creation of Campaigns?',
        'description' => 'Do you want to let members create campaigns? If set to no, some other settings on this page may not apply. This is useful if you want members to be able to view campaigns, but only want certain levels to be able to create campaigns.',
        'multiOptions' => array(
          1 => 'Yes, allow creation of campaigns.',
          0 => 'No, do not allow campaigns to be created.'
        ),
        'value' => 1,
      ));

      // Element: edit
      $this->addElement('Radio', 'edit', array(
        'label' => 'Allow Editing of campaigns?',
        'description' => 'Do you want to let members edit campaigns? If set to no, some other settings on this page may not apply.',
        'multiOptions' => array(
          1 => 'Yes, allow members to edit their own campaigns.',
          0 => 'No, do not allow members to edit their campaigns.',
        ),
        'value' => 1,
      ));

      // Element: close
      $this->addElement('Radio', 'close', array(
        'label' => 'Allow Closing of Campaigns?',
        'description' => 'Do you want to let members close campaigns?',
        'multiOptions' => array(
          2 => 'Yes, allow members to close all campaigns.',
          1 => 'Yes, allow members to close their own campaigns.',
          0 => 'No, do not allow members to close their campaigns.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->close->options[2]);
      }

      // Element: comment
      $this->addElement('Radio', 'comment', array(
        'label' => 'Allow Commenting on Campaigns?',
        'description' => 'Do you want to let members of this level comment on campaigns?',
        'multiOptions' => array(
          2 => 'Yes, allow members to comment on all campaigns, including private ones.',
          1 => 'Yes, allow members to comment on campaigns.',
          0 => 'No, do not allow members to comment on campaigns.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->comment->options[2]);
      }

      // Element: donate
      $this->addElement('Radio', 'donate', array(
      		'label' => 'Allow Donating on Campaigns?',
      		'description' => 'Do you want to let members of this level donate on campaigns?',
      		'multiOptions' => array(
      				1 => 'Yes, allow members to donate on campaigns.',
      				0 => 'No, do not allow members to donate on campaigns.',
      		),
      		'value' => 1,
      ));

      // Element: auth_view
      $this->addElement('MultiCheckbox', 'auth_view', array(
        'label' => 'Campaign Privacy',
        'description' => 'Your members can choose from any of the options checked below when they decide who can see their campaigns. These options appear on your members\' "Add Campaign" and "Edit Campaign" pages. If you do not check any options, settings will default to the last saved configuration. If you select only one option, members of this level will not have a choice.',
        'multiOptions' => array(
          'everyone'            => 'Everyone',
          'registered'          => 'All Registered Members',
          'owner_network'       => 'Friends and Networks',
          'owner_member_member' => 'Friends of Friends',
          'owner_member'        => 'Friends Only',
          'owner'               => 'Just Me'
        ),
        'value' => array('everyone', 'owner_network', 'owner_member_member', 'owner_member', 'owner'),
      ));

      // Element: auth_comment
      $this->addElement('MultiCheckbox', 'auth_comment', array(
        'label' => 'Campaign Comment Options',
        'description' => 'Your members can choose from any of the options checked below when they decide who can post comments on their campaigns. If you do not check any options, settings will default to the last saved configuration. If you select only one option, members of this level will not have a choice.',
        'multiOptions' => array(
          'everyone'            => 'Everyone',
          'registered'          => 'All Registered Members',
          'owner_network'       => 'Friends and Networks',
          'owner_member_member' => 'Friends of Friends',
          'owner_member'        => 'Friends Only',
          'owner'               => 'Just Me'
        ),
        'value' => array('everyone', 'owner_network', 'owner_member_member', 'owner_member', 'owner'),
      ));

      // Element: auth_donate
      $this->addElement('MultiCheckbox', 'auth_donate', array(
        'label' => 'Campaign Donation Options',
        'description' => 'Your members can choose from any of the options checked below when they decide who can donate to their campaigns. If you do not check any options, settings will default to the last saved configuration. If you select only one option, members of this level will not have a choice.',
        'multiOptions' => array(
          'everyone'            => 'Everyone',
          'registered'          => 'All Registered Members',
          'owner_network'       => 'Friends and Networks',
          'owner_member_member' => 'Friends of Friends',
          'owner_member'        => 'Friends Only',
          'owner'               => 'Just Me'
        ),
        'value' => array('everyone', 'owner_network', 'owner_member_member', 'owner_member', 'owner'),
      ));
    }
  }
}