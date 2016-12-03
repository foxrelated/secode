<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    goals
 * @copyright  Copyright 2014 Stars Developer
 * @license    http://www.starsdeveloper.com 
 * @author     Stars Developer
 */

class Goal_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract
{
  public function init()
  {
    parent::init();

    // My stuff
    $this
      ->setTitle('Member Level Settings')
      ->setDescription('These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.');

    // Element: view
    $this->addElement('Radio', 'view', array(
      'label' => 'Allow Viewing of Goals?',
      'description' => 'Do you want to let users view goals? If set to no, some other settings on this page may not apply.',
      'multiOptions' => array(
        2 => 'Yes, allow members to view all goals, even private ones.',
        1 => 'Yes, allow viewing of goals.',
        0 => 'No, do not allow goals to be viewed for this level.',
      ),
      'value' => ( $this->isModerator() ? 2 : 1 ),
    ));
    if( !$this->isModerator() ) {
      unset($this->view->options[2]);
    }

    if( !$this->isPublic() ) {

      // Element: create
      $this->addElement('Radio', 'create', array(
        'label' => 'Allow Creation of Goals?',
        'description' => 'Do you want to let users of this level to create goals? If set to no, some other settings on this page may not apply.',
        'multiOptions' => array(
          1 => 'Yes, allow creation of goals.',
          0 => 'No, do not allow goals to be created.',
        ),
        'value' => 1,
      ));

      // Element: comment
      $this->addElement('Radio', 'comment', array(
        'label' => 'Allow Commenting on Goals?',
        'description' => 'Do you want to let members of this level comment on goals?',
        'multiOptions' => array(
          2 => 'Yes, allow members to comment on all goals, including private ones.',
          1 => 'Yes, allow members to comment on goals.',
          0 => 'No, do not allow members to comment on goals.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->comment->options[2]);
      }
      $availableLabels = array(
      'everyone'              => 'Everyone',
      'registered'            => 'All Registered Members',
      'owner_network'         => 'Friends and Networks',
      'owner_member_member'   => 'Friends of Friends',
      'owner_member'          => 'Friends Only',
      'owner'                 => 'Just Me'
    );

      // Element: auth_view
      $this->addElement('MultiCheckbox', 'auth_view', array(
        'label' => 'Goal Privacy',
        'description' => 'Your users of this level can choose from any of the options checked below when they decide who can see their goal. If you do not check any options, settings will default to the last saved configuration. If you select only one option, members of this level will not have a choice.',
        'multiOptions' => $availableLabels//array(
//          'everyone' => 'Everyone',
//          'registered' => 'Registered Members',
//          'owner' => 'Just Me'
//        )
      ));

      // Element: auth_comment
      $this->addElement('MultiCheckbox', 'auth_comment', array(
        'label' => 'Goal Posting Options',
        'description' => 'Your users of this level can choose from any of the options checked below when they decide who can post comments to their goal . If you do not check any options, settings will default to the last saved configuration. If you select only one option, members of this level will not have a choice.',
        'multiOptions' => $availableLabels//array(
//          'registered' => 'Registered Members',
//          'owner' => 'Just Me',
//        )
      ));
    }
  }
}