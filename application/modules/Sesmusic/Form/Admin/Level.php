<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Level.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Form_Admin_Level extends Authorization_Form_Admin_Level_Abstract {

  public function init() {

    parent::init();

    $this->setTitle('Member Level Settings')
            ->setDescription('These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.');

    //Element: view
    $this->addElement('Radio', 'view', array(
        'label' => 'Allow Viewing of Music Albums?',
        'description' => 'Do you want to let members view music albums? If set to no, some other settings on this page may not apply.',
        'multiOptions' => array(
            2 => 'Yes, allow viewing of all music albums, even private ones.',
            1 => 'Yes, allow viewing of music albums.',
            0 => 'No, do not allow music albums to be viewed.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
    ));
    if (!$this->isModerator()) {
      unset($this->view->options[2]);
    }

    if (!$this->isPublic()) {

      //Element: create
      $this->addElement('Radio', 'create', array(
          'label' => 'Allow Creation of Music Albums',
          'description' => 'Do you want to allow members to create music albums on your website? [Note: Members would be able to upload songs, if they are allowed to create Music Albums]',
          'multiOptions' => array(
              1 => 'Yes, allow members to create music albums.',
              0 => 'No, do not allow members to create music albums.',
          ),
          'value' => 1,
      ));

      //Element: edit
      $this->addElement('Radio', 'edit', array(
          'label' => 'Allow Editing of Music Albums?',
          'description' => 'Do you want to let members edit their music albums? If set to no, some other settings on this page may not apply.',
          'multiOptions' => array(
              2 => 'Yes, allow members to edit all music albums, even private ones.',
              1 => 'Yes, allow members to edit their own music albums.',
              0 => 'No, do not allow music album to be edited.',
          ),
          'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if (!$this->isModerator()) {
        unset($this->edit->options[2]);
      }

      //Element: edit
      $this->addElement('Radio', 'edit_song', array(
          'label' => 'Allow Editing of Songs?',
          'description' => 'Do you want to let members edit their songs?',
          'multiOptions' => array(
              2 => 'Yes, allow members to edit all songs, even private ones.',
              1 => 'Yes, allow members to edit their own songs.',
              0 => 'No, do not allow songs to be edited.',
          ),
          'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if (!$this->isModerator()) {
        unset($this->edit_song->options[2]);
      }

      //Element: delete
      $this->addElement('Radio', 'delete', array(
          'label' => 'Allow Deletion of Music Albums?',
          'description' => 'Do you want to let members delete music albums? If set to no, some other settings on this page may not apply.',
          'multiOptions' => array(
              2 => 'Yes, allow members to delete all music albums.',
              1 => 'Yes, allow members to delete their own music albums.',
              0 => 'No, do not allow members to delete their music albums.',
          ),
          'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if (!$this->isModerator()) {
        unset($this->delete->options[2]);
      }

      //Element: edit
      $this->addElement('Radio', 'delete_song', array(
          'label' => 'Allow Deletion of Songs?',
          'description' => 'Do you want to let members delete songs? If set to no, some other settings on this page may not apply.',
          'multiOptions' => array(
              2 => 'Yes, allow members to delete all songs.',
              1 => 'Yes, allow members to delete their own songs.',
              0 => 'No, do not allow members to delete their songs.',
          ),
          'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if (!$this->isModerator()) {
        unset($this->delete_song->options[2]);
      }

      $this->addElement('Radio', 'rating_album', array(
          'label' => 'Allow Rating on Music Albums?',
          'description' => 'Do you want to let members rate Music Albums?',
          'multiOptions' => array(
              1 => 'Yes, allow rating on music albums.',
              0 => 'No, do not allow rating on music albums.'
          ),
          'value' => 1,
      ));


      $this->addElement('Radio', 'rating_albumsong', array(
          'label' => 'Allow Rating on Songs?',
          'description' => 'Do you want to let members rate Songs?',
          'multiOptions' => array(
              1 => 'Yes, allow rating on songs.',
              0 => 'No, do not allow rating on songs.'
          ),
          'value' => 1,
      ));

      $this->addElement('Radio', 'rating_artist', array(
          'label' => 'Allow Rating on Artists?',
          'description' => 'Do you want to let members rate Artists?',
          'multiOptions' => array(
              1 => 'Yes, allow rating on artists.',
              0 => 'No, do not allow rating on artists.'
          ),
          'value' => 1,
      ));


      $this->addElement('Radio', 'addplaylist_album', array(
          'label' => 'Allow Adding Music Albums to Playlist?',
          'description' => 'Do you want to let members add music albums to their playlists?',
          'multiOptions' => array(
              1 => 'Yes, allow members to add music albums to their playlists.',
              0 => 'No, do not allow members to add music albums to their playlists.'
          ),
          'value' => 1,
      ));

      $this->addElement('Radio', 'addplaylist_albumsong', array(
          'label' => 'Allow Adding Songs to Playlist?',
          'description' => 'Do you want to let members add songs to their playlists?',
          'multiOptions' => array(
              1 => 'Yes, allow members to add songs to their playlists.',
              0 => 'No, do not allow members to add songs to their playlists.'
          ),
          'value' => 1,
      ));

      $this->addElement('Radio', 'addfavourite_album', array(
          'label' => 'Allow Adding Music Albums to Favorite?',
          'description' => 'Do you want to let members add music albums to their favorite list.',
          'multiOptions' => array(
              1 => 'Yes, allow adding of music albums to favorite lists.',
              0 => 'No, do not allow adding music albums to favorite lists.'
          ),
          'value' => 1,
      ));

      $this->addElement('Radio', 'addfavourite_albumsong', array(
          'label' => 'Allow Adding Songs to Favourite?',
          'description' => 'Do you want to let members add songs to their favorite list.',
          'multiOptions' => array(
              1 => 'Yes, allow adding of songs to favorite lists.',
              0 => 'No, do not allow adding songs to favorite lists.'
          ),
          'value' => 1,
      ));

      $this->addElement('Radio', 'download_albumsong', array(
          'label' => 'Allow Downloading of Songs?',
          'description' => 'Do you want to let members download Songs?',
          'multiOptions' => array(
              1 => 'Yes, allow downloading of songs.',
              0 => 'No, do not allow downloading of songs.'
          ),
          'value' => 1,
      ));

      //Element: comment
      $this->addElement('Radio', 'comment', array(
          'label' => 'Allow Commenting on Music Albums?',
          'description' => 'Do you want to let members comment on Music Albums?',
          'multiOptions' => array(
              2 => 'Yes, allow members to comment on all music albums, even private ones.',
              1 => 'Yes, allow members to comment on music albums.',
              0 => 'No, do not allow commenting on music albums.',
          ),
          'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if (!$this->isModerator()) {
        unset($this->comment->options[2]);
      }

      //Element: auth_view
      $this->addElement('MultiCheckbox', 'auth_view', array(
          'label' => 'Music Album View Privacy',
          'description' => 'Your users can choose from any of the options checked below when they decide who can see their music albums. These options appear on your users "Create Music Album" and "Edit Music Albums" pages. If you do not check any options, settings will default to the last saved configuration. If you select only one option, members of this level will not have a choice. [Note: View privacy of music albums will apply on its songs.]',
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

      //Element: auth_comment
      $this->addElement('MultiCheckbox', 'auth_comment', array(
          'label' => 'Music Album Comment Options',
          'description' => 'Your users can choose from any of the options checked below when they decide who can post comments on their music albums. If you do not check any options, settings will default to the last saved configuration. If you select only one option, members of this level will not have a choice. [Note: Comment privacy of music albums will apply on its songs.]',
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

      //Element: max
      $this->addElement('Text', 'addalbum_max', array(
          'label' => 'Maximum Allowed Music Albums',
          'description' => 'Enter the maximum number of music albums a member can create. The field must contain an integer, use zero for unlimited.',
          'validators' => array(
              array('Int', true),
              new Engine_Validate_AtLeast(0),
          ),
      ));

      //Element: max
      $this->addElement('Text', 'addplaylist_max', array(
          'label' => 'Maximum Allowed Playlists',
          'description' => 'Enter the maximum number of playlists a member can create. The field must contain an integer, use zero for unlimited.',
          'validators' => array(
              array('Int', true),
              new Engine_Validate_AtLeast(0),
          ),
      ));
    }
  }

}