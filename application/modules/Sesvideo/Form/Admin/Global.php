<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Global.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_Form_Admin_Global extends Engine_Form {

  public function init() {

    $this->setTitle('Global Settings')
            ->setDescription('These settings affect all members in your community.');
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->addElement('Text', "sesvideo_licensekey", array(
        'label' => 'Enter License key',
        'description' => "Enter the your license key that is provided to you when you purchased this plugin. If you do not know your license key, please drop us a line from the Support Ticket section on SocialEngineSolutions website. (Key Format: XXXX-XXXX-XXXX-XXXX)",
        'allowEmpty' => false,
        'required' => true,
        'value' => $settings->getSetting('sesvideo.licensekey'),
    ));

    if ($settings->getSetting('sesvideo.pluginactivated')) {

      if (!$settings->getSetting('sesvideoset.landingpage', 0)) {
        $this->addElement('Radio', 'sesvideoset_landingpage', array(
            'label' => 'Set Welcome Page as Landing Page',
            'description' => 'Do you want to set the Default Welcome Page of this plugin as Landing page of your website?  [This is a one time setting, so if you choose ‘Yes’ and save changes, then later you can manually make changes in the Landing page from Layout Editor.]',
            'onclick' => 'confirmChangeLandingPage(this.value)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $settings->getSetting('sesvideoset.landingpage', 0),
        ));
      }


      $this->addElement('Radio', 'sesvideo_check_welcome', array(
          'label' => 'Who all users do you want to see this "Welcome Page"?',
          'description' => '',
          'multiOptions' => array(
              0 => 'Only logged in users',
              1 => 'Only non-logged in users',
              2 => 'Both, logged-in and non-logged in users',
          ),
          'value' => $settings->getSetting('sesvideo.check.welcome', 2),
      ));
      $this->addElement('Radio', 'sesvideo_enable_welcome', array(
          'label' => 'Video Menu Redirection',
          'description' => 'Choose from below where do you want to redirect users when Videos Menu item is clicked in the Main Navigation Bar.',
          'multiOptions' => array(
              1 => 'Video Welcome Page',
              0 => 'Video Home Page'
          ),
          'value' => $settings->getSetting('sesvideo.enable.welcome', 1),
      ));
      $this->addElement('Text', 'video_videos_manifest', array(
          'label' => 'Plural "videos" Text in URL',
          'description' => 'Enter the text which you want to show in place of "videos" in the URLs of this plugin.',
          'value' => $settings->getSetting('video.videos.manifest', 'videos'),
      ));
      $this->addElement('Text', 'video_video_manifest', array(
          'label' => 'Singular "video" Text in URL',
          'description' => 'Enter the text which you want to show in place of "video" in the URLs of this plugin.',
          'value' => $settings->getSetting('video.video.manifest', 'video'),
      ));
      $this->addElement('Select', 'video_enable_chanel', array(
          'label' => 'Enable Channels',
          'description' => 'Do you want to enable channels for videos on your website? [If you choose Yes, then members would be able to create their channels and add videos into them. Below, you can choose which all videos can be added to channels.]',
          'multiOptions' => array(
              '1' => 'Yes',
              '0' => 'No'
          ),
          'onchange' => 'checkChange(this.value)',
          'value' => $settings->getSetting('video.enable.chanel', 0),
      ));
      $this->addElement('Text', 'video_chanels_manifest', array(
          'label' => 'Plural "channels" Text in URL',
          'description' => 'Enter the text which you want to show in place of "channeld" in the URLs of this plugin.',
          'value' => $settings->getSetting('video.chanels.manifest', 'channels'),
      ));
      $this->addElement('Text', 'video_chanel_manifest', array(
          'label' => 'Singular "channel" Text in URL',
          'description' => 'Enter the text which you want to show in place of "channel" in the URLs of this plugin.',
          'value' => $settings->getSetting('video.chanel.manifest', 'channel'),
      ));
      $this->addElement('MultiCheckbox', 'video_enable_chaneloption', array(
          'label' => 'Allow Videos for Channels',
          'description' => 'Choose from below the videos which users can add to their channels?',
          'value' => $settings->getSetting('video.enable.chaneloption', false),
          'multiOptions' => array(
              'my_created' => 'My Created (videos Created by the user)',
              'liked_videos' => 'Liked Videos (videos Liked by the user)',
              'rated_videos' => 'Rated Videos (videos Rated by the user)',
              'watch_later' => 'Watch Later (videos added to watch later by the user)'
          )
      ));
      /* chanel rating */
      $this->addElement('Select', 'video_chanel_rating', array(
          'label' => 'Allow Rating on Channels',
          'description' => 'Do you want to allow users to give rating on channels on your website?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'onchange' => 'rating_chanel(this.value)',
          'value' => $settings->getSetting('video.chanel.rating', 1),
      ));
      $this->addElement('Select', 'video_ratechanel_own', array(
          'label' => 'Allow Rating on Own Channels',
          'description' => 'Do you want to allow users to give rating on own channels on your website?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $settings->getSetting('video.ratechanel.own', 1),
      ));
      $this->addElement('Select', 'video_ratechanel_again', array(
          'label' => 'Allow to Edit Rating on Channels',
          'description' => 'Do you want to allow users to edit their rating on channels on your website?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $settings->getSetting('video.ratechanel.again', 1),
      ));
      $this->addElement('Select', 'video_ratechanel_show', array(
          'label' => 'Show Earlier Rating on Channels',
          'description' => 'Do you want to show earlier rating on channels on your website?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $settings->getSetting('video.ratechanel.show', 1),
      ));
      $this->addElement('Select', 'videochanel_category_enable', array(
          'label' => 'Make Chanel Categories Mandatory',
          'description' => 'Do you want to make category field mandatory when users create or edit their chanels?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $settings->getSetting('videochanel.category.enable', 1),
      ));
      $this->addElement('Select', 'video_enable_subscription', array(
          'label' => 'Allow "Follow" for Channels',
          'description' => 'Do you want to enable follow for channles on your website. If you choose "Yes" then your site members follow channels.',
          'value' => $settings->getSetting('video.enable.subscription', 1),
          'multiOptions' => array(
              '1' => 'Yes',
              '0' => 'No',
          ),
      ));
			$this->addElement('Radio', 'sesvideo_enable_location', array(
        'label' => 'Enable Location in Video',
        'description' => 'Choose from below where do you want to enable location in Video.',
        'multiOptions' => array(
            '1' => 'Yes,Enable Location',
            '0' => 'No,Don\'t Enable Location',
        ),
        'value' => $settings->getSetting('sesvideo.enable.location', 1),
    ));
      /* Rating code */
      $this->addElement('Select', 'video_video_rating', array(
          'label' => 'Allow Rating on Videos',
          'description' => 'Do you want to allow users to give rating on videos on your website?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'onchange' => 'rating_video(this.value)',
          'value' => $settings->getSetting('video.video.rating', 1),
      ));
      $this->addElement('Select', 'video_ratevideo_own', array(
          'label' => 'Allow Rating on Own Videos',
          'description' => 'Do you want to allow users to give rating on own videos on your website?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $settings->getSetting('video.ratevideo.own', 1),
      ));
      $this->addElement('Select', 'video_ratevideo_again', array(
          'label' => 'Allow to Edit Rating on Videos',
          'description' => 'Do you want to allow users to edit their rating on videos on your website?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $settings->getSetting('video.ratevideo.again', 1),
      ));
      $this->addElement('Select', 'video_ratevideo_show', array(
          'label' => 'Show Earlier Rating on Videos',
          'description' => 'Do you want to show earlier rating on videos on your website?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $settings->getSetting('video.ratevideo.show', 1),
      ));
      /* End rating code */
      $this->addElement('Select', 'sesvideo_artist_rating', array(
          'label' => 'Allow Rating on Artists',
          'description' => 'Do you want to allow users to give rating on artists on your website?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'onclick' => 'rating_artist(this.value)',
          'value' => $settings->getSetting('sesvideo.artist.rating', 1),
      ));
      $this->addElement('Select', 'sesvideo_rateartist_again', array(
          'label' => 'Allow to Edit Rating on Artists',
          'description' => 'Do you want to allow users to edit their rating on artists on your website?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $settings->getSetting('sesvideo.rateartist.again', 1),
      ));
      $this->addElement('Select', 'sesvideo_rateartist_show', array(
          'label' => 'Show Earlier Rating on Artists',
          'description' => 'Do you want to show earlier rating on artists on your website?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $settings->getSetting('sesvideo.rateartist.show', 1),
      ));
      $this->addElement('MultiCheckbox', 'sesvideo_artistlink', array(
          'label' => 'Allow "Add to Favorite" for Artists',
          'description' => 'Do you want to allow members of your website to add artists to their favorites?',
          'multiOptions' => array('favourite' => 'Add to Favourite'),
          'value' => unserialize($settings->getSetting('sesvideo.artistlink', 'a:1:{i:0;s:9:"favourite";}')),
      ));
      $this->addElement('Select', 'video_category_enable', array(
          'label' => 'Make Video Categories Mandatory',
          'description' => 'Do you want to make category field mandatory when users create or edit their videos?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $settings->getSetting('video.category.enable', 1),
      ));
      $this->addElement('Select', 'video_enable_watchlater', array(
          'label' => 'Enable "Watch Later" for Videos',
          'description' => 'Do you want to enable watch later for videos on your website. If you choose "Yes" then your site members will be able to save videos to their watch later list.',
          'value' => $settings->getSetting('video.enable.watchlater', 1),
          'multiOptions' => array(
              '1' => 'Yes',
              '0' => 'No',
          ),
      ));
      $this->addElement('Select', 'video_enable_report', array(
          'label' => 'Allow Report for Videos',
          'description' => 'Do you want to allow users to report videos on your website?'
          . 'report.',
          'value' => $settings->getSetting('video.enable.report', 1),
          'multiOptions' => array(
              '1' => 'Yes',
              '0' => 'No',
          ),
      ));
      $this->addElement('Select', 'sesvideo_search_type', array(
          'label' => 'Proximity Search Unit',
          'description' => 'Choose the unit for proximity search of location of videos on your website.',
          'multiOptions' => array(
              1 => 'Miles',
              0 => 'Kilometres'
          ),
          'value' => $settings->getSetting('sesvideo.search.type', 1),
      ));
      $this->addElement('Select', 'video_embeds', array(
          'label' => 'Allow Embedding of Videos',
          'description' => 'Do you want to allow users to embed videos on your website? If you choose "Yes" then your site members embed videos on this site in other pages using an iframe (like YouTube).',
          'value' => $settings->getSetting('video.embeds', 1),
          'multiOptions' => array(
              '1' => 'Yes',
              '0' => 'No',
          ),
      ));
      $this->addElement('Select', 'video_uploadphoto', array(
          'label' => 'Allow to Choose Main Photo',
          'description' => 'Do you want to allow users to choose main photo for the videos while creating / editing their videos ?',
          'value' => $settings->getSetting('video.uploadphoto'),
          'multiOptions' => array(
              '0' => 'No',
              '1' => 'Yes'
          )
      ));
			$this->addElement('Select', 'sesvideo_direct_video', array(
          'label' => 'Allow to Upload Videos Without FFMPEG',
          'description' => 'Do you want to allow videos to be uploaded directly without converting them from one extension to another. (Note: This setting will only work for mp4 and flv video types. If you have enabled the "HTML5 Video Support" setting from admin panel, then the mp4 videos will be converted into mp4, otherwise mp4 videos will be saved as flv videos.)?',
          'value' => $settings->getSetting('sesvideo.direct.video'),
          'multiOptions' => array(
              '0' => 'No',
              '1' => 'Yes'
          )
      ));
      $this->addElement('Text', 'sesvideo_youtube_playlist', array(
          'label' => 'Youtube Playlist Video Limit',
          'description' => 'Enter the number of songs to be imported from Youtube Playlists. [We suggest you to choose less than 25 videos to be imported for a playlist as importing more videos may break the connection from Youtube and abort the process.]',
          'value' => $settings->getSetting('sesvideo.youtube.playlist', '25'),
      ));
      $description = 'While creating videos on your website, users can choose Youtube Playlist as a source. For this, create an Application Key through the <a href="https://console.developers.google.com" target="_blank">Google Developers Console</a> page. <br>For more information, see: <a href="https://developers.google.com/youtube/v3/getting-started" target="_blank">YouTube Data API</a>.';

      $this->addElement('Text', 'video_youtube_apikey', array(
          'label' => 'Youtube Playlist API Key',
          'description' => $description,
          'filters' => array(
              'StringTrim',
          ),
          'value' => $settings->getSetting('video.youtube.apikey'),
      ));
      /* $this->addElement('Text', 'sesvideo_google_key', array(
        'label' => 'Google Api Key for Youtube Video Playlist',
        'description' => '',
        'value' => $settings->getSetting('sesvideo.google.key', ''),
        )); */
      $this->addElement('Text', 'video_ffmpeg_path', array(
          'label' => 'Path to FFMPEG',
          'description' => 'Please enter the full path to your FFMPEG installation. (Environment variables are not present)',
          'value' => $settings->getSetting('video.ffmpeg.path', ''),
      ));
      $this->addElement('Checkbox', 'video_html5', array(
          'description' => 'HTML5 Video Support',
          'value' => $settings->getSetting('video.html5', false),
      ));
      $this->video_youtube_apikey->getDecorator('Description')->setOption('escape', false);
      $this->addElement('Select', 'sesvideo_taboptions', array(
          'label' => 'Menu Items Count',
          'description' => 'How many menu items do you want to show in the main navigation menu of this plugin?',
          'multiOptions' => array(
              0 => 0,
              1 => 1,
              2 => 2,
              3 => 3,
              4 => 4,
              5 => 5,
              6 => 6,
              7 => 7,
              8 => 8,
              9 => 9,
          ),
          'value' => $settings->getSetting('sesvideo.taboptions', 6),
      ));
      $this->addElement('Text', 'video_jobs', array(
          'label' => 'Encoding Jobs',
          'description' => 'How many jobs do you want to allow to run at the same time?',
          'value' => $settings->getSetting('video.jobs', 2),
      ));
      // Add submit button
      $this->addElement('Button', 'submit', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true
      ));
    } else {
      //Add submit button
      $this->addElement('Button', 'submit', array(
          'label' => 'Activate your plugin',
          'type' => 'submit',
          'ignore' => true
      ));
    }
  }

}
