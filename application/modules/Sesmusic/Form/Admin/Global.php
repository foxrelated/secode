<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Global.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Form_Admin_Global extends Engine_Form {

  public function init() {

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->setTitle('Global Settings')
            ->setDescription('These settings affect all members in your community.');

    $this->addElement('Text', "sesmusic_licensekey", array(
        'label' => 'Enter License key',
        'description' => "Enter the your license key that is provided to you when you purchased this plugin. If you do not know your license key, please drop us a line from the Support Ticket section on SocialEngineSolutions website. (Key Format: XXXX-XXXX-XXXX-XXXX)",
        'allowEmpty' => false,
        'required' => true,
        'value' => $settings->getSetting('sesmusic.licensekey'),
    ));

    if ($settings->getSetting('sesmusic.pluginactivated')) {
      $guidlines = 'How to get SoundCloud Client ID and Client Secret?
        
        1. Login to your SoundCloud Account. Register a new account, if you do not have one.
        2. Now, go to the URL: https://soundcloud.com/you/apps
        3. Click on Register a new application button.
        4. Follow the easy steps by entering the name of your app and your website URL.
        5. Now, copy the Client ID and Client Secret and paste here.';

      $this->addElement('Radio', 'sesmusic_uploadoption', array(
          'label' => 'Choose Song Source',
          'description' => 'Choose the source of songs from which you want songs to be uploaded on your website.',
          'multiOptions' => array(
              'myComputer' => "My Computer",
              'soundCloud' => 'SoundCloud [enter the "SoundCloud Client Id" and "SoundCloud Client Secret" below.]',
              'both' => 'Both "My Computer" and "SoundCloud"',
          ),
          'escape' => false,
          'onchange' => 'checkUpload(this.value)',
          'value' => $settings->getSetting('sesmusic.uploadoption', 'myComputer'),
      ));

      $this->addElement('Text', "sesmusic_scclientid", array(
          'label' => 'SoundCloud Client Id',
          'description' => 'Enter the SoundCloud Client Id. [Note: If you remove this “Id”, then the current songs from SoundCloud will not show Play option.] ' . '<a href="javascript:void(0);" title="' . $guidlines . '"><img onclick="showPopUp();" src="application/modules/Sesbasic/externals/images/icons/question.png" alt="Question" /></a>',
          'value' => $settings->getSetting('sesmusic.scclientid'),
      ));
      $this->sesmusic_scclientid->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

      $this->addElement('Text', "sesmusic_scclientscreatid", array(
          'label' => 'SoundCloud Client Secret',
          'description' => 'Enter the SoundCloud Client Secret Key. [Note: If you remove this “Key”, then the current songs from SoundCloud will not show Play option.] ' . '<a href="javascript:void(0);" title="' . $guidlines . '"><img onclick="showPopUp();" src="application/modules/Sesbasic/externals/images/icons/question.png" alt="Question" /></a>',
          'value' => $settings->getSetting('sesmusic.scclientscreatid'),
      ));
      $this->sesmusic_scclientscreatid->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

      $this->addElement('Radio', 'sesmusic_showplayer', array(
          'label' => 'Choose Music Player',
          'description' => 'Choose the music player which you want to show on your website to play songs?',
          'multiOptions' => array(
              '1' => 'Mini Player',
              '0' => 'Full Width Player',
          ),
          'value' => $settings->getSetting('sesmusic.showplayer', 0),
      ));

      $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
      $banner_options[] = '';
      $path = new DirectoryIterator(APPLICATION_PATH . '/public/admin/');
      foreach ($path as $file) {
        if ($file->isDot() || !$file->isFile())
          continue;
        $base_name = basename($file->getFilename());
        if (!($pos = strrpos($base_name, '.')))
          continue;
        $extension = strtolower(ltrim(substr($base_name, $pos), '.'));
        if (!in_array($extension, array('gif', 'jpg', 'jpeg', 'png')))
          continue;
        $banner_options['public/admin/' . $base_name] = $base_name;
      }

      $fileLink = $view->baseUrl() . '/admin/files/';
      if (count($banner_options) > 1) {
        $this->addElement('Select', 'sesmusic_playistdefaultphoto', array(
            'label' => 'Playlist Default Photo',
            'description' => 'Choose a default photo for the playlists on your website. [Note: You can add a new photo from the "File & Media Manager" section from here: <a href="' . $fileLink . '" target="_blank">File & Media Manager</a>. Leave the field blank if you do not want to change playlist default photo.]',
            'multiOptions' => $banner_options,
            'value' => $settings->getSetting('sesmusic.playistdefaultphoto'),
        ));
        $this->sesmusic_playistdefaultphoto->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
      } else {
        $description = "<div class='tip'><span>" . Zend_Registry::get('Zend_Translate')->_("There are currently no photos added in the File & Media Manager. Please upload a photo from here: <a href='" . $fileLink . "' target='_blank'>File & Media Manager</a> and refresh the page to display new files.") . "</span></div>";

        //Add Element: Dummy
        $this->addElement('Dummy', 'playlist_defaultphoto', array(
            'label' => 'Playlist Default Photo',
            'description' => $description,
        ));
        $this->playlist_defaultphoto->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
      }

      $this->addElement('Radio', 'sesmusic_enable_addmusichortcut', array(
          'label' => 'Show Create New Music Album Icon',
          'description' => 'Do you want to show "Create New Music Album" Icon on all the pages of this plugin? This icon will redirect users to Create New Music Album page.',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => $settings->getSetting('sesmusic.enable.addmusichortcut', 1),
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
