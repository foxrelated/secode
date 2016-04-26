<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: SongSettings.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Form_Admin_SongSettings extends Engine_Form {

  public function init() {

    $this->setTitle('Song Settings')
            ->setDescription('Below settings will affect all the songs uploaded on your website. Here, you can also enable / disable various features for songs on your website.');

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->addElement('MultiCheckbox', 'sesmusic_songlink', array(
        'label' => 'Allow Report & Share',
        'description' => 'Do you want members of your website to Report and Share Songs on your website?',
        'multiOptions' => array('report' => 'Yes, allow to Report.', 'share' => 'Yes, allow to Share.'),
        'value' => unserialize($settings->getSetting('sesmusic.songlink', 'a:7:{i:0;s:6:"report";i:1;s:5:"share";}')),
    ));

    $this->addElement('Radio', 'sesmusic_albumsong_rating', array(
        'label' => 'Allow Rating',
        'description' => 'Do you want to allow users to give ratings on songs on your website?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'onclick' => 'ratingAlbumSongs(this.value)',
        'value' => $settings->getSetting('sesmusic.albumsong.rating', 1),
    ));

    $this->addElement('Radio', 'sesmusic_ratealbumsong_own', array(
        'label' => 'Allow Rating on Own Albums',
        'description' => 'Do you want to allow users to give ratings on their own songs on your website?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sesmusic.ratealbumsong.own', 1),
    ));
    
    $this->addElement('Radio', 'sesmusic_ratealbumsong_again', array(
        'label' => 'Allow to Edit Rating',
        'description' => 'Do you want to allow users to edit their ratings on songs on your website?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sesmusic.ratealbumsong.again', 1),
    ));

    $this->addElement('Radio', 'sesmusic_ratealbumsong_show', array(
        'label' => 'Show Earlier Rating',
        'description' => 'Do you want to show earlier ratings on songs on your website?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sesmusic.ratealbumsong.show', 1),
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

    if (count($banner_options) > 1) {
      $this->addElement('Select', 'sesmusic_songdefaultphoto', array(
          'label' => 'Songs Default Photo',
          'description' => 'Choose a default photo for the songs on your website. [Note: You can add a new photo from the "File & Media Manager" section from here: File & Media Manager. Leave the field blank if you do not want to change songs default photo.]',
          'multiOptions' => $banner_options,
          'value' => $settings->getSetting('sesmusic.songdefaultphoto'),
      ));
    } else {

      $description = "<div class='tip'><span>" . Zend_Registry::get('Zend_Translate')->_("There are currently no photo to add for album song default photo. Image to be chosen for album default photo should be first uploaded from the \"Layout\" >> \"File & Media Manager\" section.") . "</span></div>";

      //Add Element: Dummy
      $this->addElement('Dummy', 'albumsong_default', array(
          'label' => 'Choose Album Song Default Photo',
          'description' => $description,
      ));
      $this->albumsong_default->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    }

    $this->addElement('Radio', 'sesmusic_show_songcover', array(
        'label' => 'Song Cover Photo',
        'description' => 'Do you want to enable the cover photo for songs on your website?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No',
        ),
        'onchange' => 'songCover(this.value)',
        'value' => $settings->getSetting('sesmusic.show.songcover', 1),
    ));


    if (count($banner_options) > 1) {
      $this->addElement('Select', 'sesmusic_songcover_photo', array(
          'label' => 'Songs Default Cover Photo',
          'description' => 'Choose a default cover photo for the songs on your website. [Note: You can add a new cover photo from the "File & Media Manager" section from here: File & Media Manager. Leave the field blank if you do not want to change songs default cover photo.]',
          'multiOptions' => $banner_options,
          'value' => $settings->getSetting('sesmusic.songcover.photo'),
      ));
    } else {

      $description = "<div class='tip'><span>" . Zend_Registry::get('Zend_Translate')->_("There are currently no photo to add for album song cover. Image to be chosen for album cover should be first uploaded from the \"Layout\" >> \"File & Media Manager\" section.") . "</span></div>";

      //Add Element: Dummy
      $this->addElement('Dummy', 'albumsong_cover', array(
          'label' => 'Songs Default Cover Photo',
          'description' => $description,
      ));
      $this->albumsong_cover->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    }

    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}
