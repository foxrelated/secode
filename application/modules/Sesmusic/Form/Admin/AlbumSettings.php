<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: AlbumSettings.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Form_Admin_AlbumSettings extends Engine_Form {

  public function init() {

    $this->setTitle('Music Album Settings')
            ->setDescription('Below settings will affect all the music albums created on your website. Here, you can also enable / disable various features for music albums on your website.');

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->addElement('MultiCheckbox', 'sesmusic_albumlink', array(
        'label' => 'Allow Report & Share',
        'description' => 'Do you want members of your website to Report and Share Music Albums on your website?',
        'multiOptions' => array('report' => 'Yes, allow to Report.', 'share' => 'Yes, allow to Share.'),
        'value' => unserialize($settings->getSetting('sesmusic.albumlink', 'a:6:{i:0;s:6:"report";i:1;s:5:"share";}')),
    ));

    $this->addElement('Radio', 'sesmusic_album_rating', array(
        'label' => 'Allow Rating',
        'description' => 'Do you want to allow users to give ratings on music albums on your website?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'onclick' => 'rating_album(this.value)',
        'value' => $settings->getSetting('sesmusic.album.rating', 1),
    ));

    $this->addElement('Radio', 'sesmusic_ratealbum_own', array(
        'label' => 'Allow Rating on Own Albums',
        'description' => 'Do you want to allow users to give ratings on own music albums on your website?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sesmusic.ratealbum.own', 1),
    ));

    $this->addElement('Radio', 'sesmusic_ratealbum_again', array(
        'label' => 'Allow to Edit Rating',
        'description' => 'Do you want to allow users to edit their ratings on music albums on your website?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sesmusic.ratealbum.again', 1),
    ));

    $this->addElement('Radio', 'sesmusic_ratealbum_show', array(
        'label' => 'Show Earlier Rating',
        'description' => 'Do you want to show earlier ratings on music albums on your website?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sesmusic.ratealbum.show', 1),
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
      $this->addElement('Select', 'sesmusic_albumdefaultphoto', array(
          'label' => 'Music Album Default Photo',
          'description' => 'Choose a default photo for the music albums on your website. [Note: You can add a new photo from the "File & Media Manager" section from here: <a href="' . $fileLink . '" target="_blank">File & Media Manager</a>. Leave the field blank if you do not want to change music album default photo.]',
          'multiOptions' => $banner_options,
          'value' => $settings->getSetting('sesmusic.albumdefaultphoto'),
      ));
      $this->sesmusic_albumdefaultphoto->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    } else {

      $description = "<div class='tip'><span>" . Zend_Registry::get('Zend_Translate')->_('There are currently no photo to add for album default photo. Image to be chosen for album cover should be first uploaded from the "Layout" >> "<a href="' . $fileLink . '" target="_blank">File & Media Manager</a>" section.') . "</span></div>";

      //Add Element: Dummy
      $this->addElement('Dummy', 'album_defaultphoto', array(
          'label' => 'Music Album Default Photo',
          'description' => $description,
      ));
      $this->album_defaultphoto->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    }

    $this->addElement('Radio', 'sesmusic_show_albumcover', array(
        'label' => 'Music Album Cover Photo',
        'description' => 'Do you want to enable the cover photo for music albums on your website?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No',
        ),
        'onchange' => 'albumCover(this.value)',
        'value' => $settings->getSetting('sesmusic.show.albumcover', 1),
    ));

    if (count($banner_options) > 1) {
      $this->addElement('Select', 'sesmusic_albumcover_photo', array(
          'label' => 'Music Album Default Cover Photo',
          'description' => 'Choose a default cover photo for the music albums on your website. [Note: You can add a new photo from the "File & Media Manager" section from here: File & Media Manager. Leave the field blank if you do not want to change music album default cover photo.]',
          'multiOptions' => $banner_options,
          'value' => $settings->getSetting('sesmusic.albumcover.photo'),
      ));
    } else {

      $description = "<div class='tip'><span>" . Zend_Registry::get('Zend_Translate')->_('There are currently no photo to add for album cover. Image to be chosen for album cover should be first uploaded from the "Layout" >> "<a href="' . $fileLink . '" target="_blank">File & Media Manager</a>" section.') . "</span></div>";

      //Add Element: Dummy
      $this->addElement('Dummy', 'album_cover', array(
          'label' => 'Music Album Default Cover Photo',
          'description' => $description,
      ));
      $this->album_cover->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    }

    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}
