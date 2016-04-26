<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Level.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract {

  public function init() {

    parent::init();

    // My stuff
    $this->setTitle('Videos Member Level Settings')
            ->setDescription('These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.');

    // Element: view
    $this->addElement('Radio', 'view', array(
        'label' => 'Allow Viewing of Videos?',
        'description' => 'Do you want to let members view videos? If set to no, some other settings on this page may not apply.',
        'multiOptions' => array(
            2 => 'Yes, allow viewing of all videos, even private ones.',
            1 => 'Yes, allow viewing of videos.',
            0 => 'No, do not allow videos to be viewed.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
    ));
    if (!$this->isModerator()) {
      unset($this->view->options[2]);
    }

    if (!$this->isPublic()) {

      // Element: create
      $this->addElement('Radio', 'create', array(
          'label' => 'Allow Creation of Videos?',
          'description' => 'Do you want to let members create videos? If set to no, some other settings on this page may not apply. This is useful if you want members to be able to view videos, but only want certain levels to be able to create videos.',
          'multiOptions' => array(
              1 => 'Yes, allow creation of videos.',
              0 => 'No, do not allow video to be created.'
          ),
          'value' => 1,
      ));

      // Element: edit
      $this->addElement('Radio', 'edit', array(
          'label' => 'Allow Editing of Videos?',
          'description' => 'Do you want to let members edit videos? If set to no, some other settings on this page may not apply.',
          'multiOptions' => array(
              2 => 'Yes, allow members to edit all videos.',
              1 => 'Yes, allow members to edit their own videos.',
              0 => 'No, do not allow members to edit their videos.',
          ),
          'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if (!$this->isModerator()) {
        unset($this->edit->options[2]);
      }

      // Element: delete
      $this->addElement('Radio', 'delete', array(
          'label' => 'Allow Deletion of Videos?',
          'description' => 'Do you want to let members delete videos? If set to no, some other settings on this page may not apply.',
          'multiOptions' => array(
              2 => 'Yes, allow members to delete all videos.',
              1 => 'Yes, allow members to delete their own videos.',
              0 => 'No, do not allow members to delete their videos.',
          ),
          'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if (!$this->isModerator()) {
        unset($this->delete->options[2]);
      }

      // Element: comment
      $this->addElement('Radio', 'comment', array(
          'label' => 'Allow Commenting on Videos?',
          'description' => 'Do you want to let members of this level comment on videos?',
          'multiOptions' => array(
              2 => 'Yes, allow members to comment on all videos, including private ones.',
              1 => 'Yes, allow members to comment on videos.',
              0 => 'No, do not allow members to comment on videos.',
          ),
          'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if (!$this->isModerator()) {
        unset($this->comment->options[2]);
      }

      // Element: locked
      $this->addElement('Radio', 'locked', array(
          'label' => 'Allow Viewing of Locked Videos?',
          'description' => 'Do you want to let members view locked videos without entering password?',
          'multiOptions' => array(
              1 => 'Yes, allow members to view locked videos.',
              0 => 'No, do not allow members to view locked videos without password.',
          ),
          'value' => ( $this->isModerator() ? 1 : 1 ),
      ));
      if (!$this->isModerator()) {
        unset($this->locked->options[2]);
      }
			// Element: locked video
      $this->addElement('Radio', 'video_locked', array(
          'label' => 'Allow User to Lock Videos?',
          'description' => 'Do you want to let members to locked videos?',
          'multiOptions' => array(
              1 => 'Yes, allow members to locked videos.',
              0 => 'No, do not allow members to locked videos.',
          ),
          'value' => ( $this->isModerator() ? 1 : 1 ),
      ));
      if (!$this->isModerator()) {
        unset($this->video_locked->options[2]);
      }
      // Element: upload
      $this->addElement('Radio', 'upload', array(
          'label' => 'Allow Video Upload?',
          'description' => 'Do you want to let members to upload their own videos? If set to no, some other settings on this page may not apply.',
          'multiOptions' => array(
              1 => 'Yes, allow video uploads.',
              0 => 'No, do not allow video uploads.',
          ),
          'value' => 1,
      ));
      // Element: rating on videos
      $this->addElement('Radio', 'rating', array(
          'label' => 'Allow Rating on Videos ?',
          'description' => 'Do you want to let members rate Videos?',
          'multiOptions' => array(
              1 => 'Yes, allow rating on videos.',
              0 => 'No, do not allow rating on videos.'
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

      

      // Element: auth_view
      $this->addElement('MultiCheckbox', 'auth_view', array(
          'label' => 'Video Privacy',
          'description' => 'Your members can choose from any of the options checked below when they decide who can see their video. If you do not check any options, settings will default to the last saved configuration. If you select only one option, members of this level will not have a choice.',
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
          'label' => 'Video Comment Options',
          'description' => 'Your members can choose from any of the options checked below when they decide who can post comments on their video. If you do not check any options, settings will default to the last saved configuration. If you select only one option, members of this level will not have a choice. ',
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

      // Element: max
      $this->addElement('Text', 'max', array(
          'label' => 'Maximum Allowed Videos',
          'description' => 'Enter the maximum number of allowed videos. The field must contain an integer, use zero for unlimited.',
          'validators' => array(
              array('Int', true),
              new Engine_Validate_AtLeast(0),
          ),
      ));


      //Element: auth_playlistadd
      $this->addElement('Radio', 'addplaylist_video', array(
          'label' => 'Allow Adding Video to Playlist?',
          'description' => 'Do you want to let members add videos to their playlists?',
          'multiOptions' => array(
              1 => 'Yes, allow members to add videos to their playlists.',
              0 => 'No, do not allow members to add videos to their playlists.'
          ),
          'value' => 1,
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
			$this->addElement('Select', 'video_approve', array(
          'description' => 'Do you want to auto-approve the videos uploaded on your website? [If you choose Yes, then you would be able to choose from below setting, from which all source the uploaded videos are to be auto-approve.]',
          'label' => 'Auto-approve Videos',
          'value' => 0,
					'onchange'=>'setVideoType(this.value);',
          'multiOptions' => array(
              1=>'No, do not auto-approve videos',
							0=>'Yes, auto-approve videos'
          )
      ));
		$this->addElement('MultiCheckbox', 'video_approve_type', array(
          'description' => 'Choose from below the video sources from which uplaoded videos will be auto-approved on your website. Videos from the unchecked video sources will not be auto-approve and you can approve them from the "Manage Videos" section of this plugin.',
          'label' => 'Options for Video Sources to be Auto-Approved',
          //'value' => array('youtube','youtubePlaylist','vimeo','dailymotion','url','embedcode','myComputer'),
          'multiOptions' => array(
              'youtube' => 'Youtube',
              'youtubePlaylist' => 'Youtube Playlists',
              'vimeo' => 'Vimeo',
              'dailymotion' => 'Dailymotion',
							'url' => 'From URL(if support html5 then mp4 video and if not then flv video upload direct and other need FFMPEG)',
							'embedcode'=>'From Embed Code',
              'myComputer' => 'My Computer'
          )
      ));
		$this->addElement('MultiCheckbox', 'video_upload_option', array(
          'description' => 'Choose from below the options using which users can upload videos on your website?',
          'label' => 'Option for Videos to be Uploaded',
          //'value' => array('youtube','youtubePlaylist','vimeo','dailymotion','url','embedcode'),
          'multiOptions' => array(
              'youtube' => 'Youtube [set API key in Global settings to make this setting work.]',
              'youtubePlaylist' => 'Youtube Playlists [set API key  in Global settings to make this setting work.]',
              'vimeo' => 'Vimeo',
              'dailymotion' => 'Dailymotion',
							'url' => 'From URL(if support html5 then mp4 video and if not then flv video upload direct and other need FFMPEG)',
							'embedcode'=>'From Embed Code',
              'myComputer' => 'My Computer'
          )
      ));
    }
		
		// Element: lightbox type
      $this->addElement('Radio', 'imageviewer', array(
          'label' => 'Lightbox Viewer Type',
          'description' => 'Choose the lighbox viewer type to be enabled for the members of this level.',
          'multiOptions' => array(
              0 => 'Basic Viewer',
              1 => 'Advanced Viewer'
          ),
          'value' => 1,
      ));
  }

}
