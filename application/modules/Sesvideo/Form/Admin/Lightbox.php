<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Lightbox.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_Form_Admin_Lightbox extends Engine_Form {

  public function init() {

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this
            ->setTitle('Lightbox Viewer Settings');
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $this->addElement('Radio', 'sesvideo_enable_lightbox', array(
        'label' => 'Open Videos in Lightbox',
        'description' => 'Do you want to open videos in Lightbox Viewer? [You can choose the type of the lightbox viewer to be opened for members depending on their member levels from the <a target="_blank" href="' . $view->baseUrl() . "/admin/sesvideo/settings/level#imageviewer-wrapper" . '">Member Level Settings</a>.]',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sesvideo.enable.lightbox', 1),
    ));

    $this->sesvideo_enable_lightbox->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    $banner_options = array();
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
    if (count($banner_options) > 0) {
      $this->addElement('Select', 'sesvideo_private_photo', array(
          'label' => 'Private Photo for Private Videos',
          'description' => 'Choose below the photo to be shown for a private video when the video is shown in video lightbox. When a user upload a video and restrict its visibility to friend or network, then also the photo is showed in Activity Feed and certain widgets and browse pages. Below chosen photo will be shown for such private pages to users who does not have access. [Note: You can add a new photo from the "File & Media Manager" section from here: <a target="_blank" href="' . $fileLink . '">File & Media Manager</a>.]',
          'multiOptions' => $banner_options,
          'value' => $settings->getSetting('sesvideo.private.photo'),
      ));
    } else {
      $description = "<div class='tip'><span>" . Zend_Registry::get('Zend_Translate')->_('There are currently no photo for private. Photo to be chosen for private photo should be first uploaded from the "Layout" >> "<a target="_blank" href="' . $fileLink . '">File & Media Manager</a>" section. => There are currently no photo in the File & Media Manager for the private photo. Please upload the Photo to be chosen for private photo from the "Layout" >> "<a target="_blank" href="' . $fileLink . '">File & Media Manage</a>" section.') . "</span></div>";
      //Add Element: Dummy
      $this->addElement('Dummy', 'sesvideo_private_photo', array(
          'label' => 'Photo instead of Private Photo',
          'description' => $description,
      ));
    }
    $this->sesvideo_private_photo->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

    $this->addElement('Text', 'sesvideo_title_truncate', array(
        'label' => 'Video Title Truncate Limit',
        'description' => 'Enter the title truncation limit of the video when shown lightbox viewer.',
        'value' => $settings->getSetting('sesvideo.title.truncate', 45),
    ));
    $this->addElement('Text', 'sesvideo_description_truncate', array(
        'label' => 'Video Description Truncate Limit',
        'description' => 'Enter the description truncation limit of the video when shown lightbox viewer.',
        'value' => $settings->getSetting('sesvideo.description.truncate', 45),
    ));
    $this->addElement('Dummy', 'dummy', array(
        'content' => 'Choose from below the options to be available in the lightbox viewer for videos.',
    ));

    $this->addElement('Radio', 'sesvideo_add_delete', array(
        'label' => 'Delete',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sesvideo.add.delete', 1),
    ));

    $this->addElement('Radio', 'sesvideo_add_share', array(
        'label' => 'Share',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sesvideo.add.share', 1),
    ));

    $this->addElement('Radio', 'sesvideo_add_report', array(
        'label' => 'Report',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('sesvideo.add.report', 1),
    ));

    // Add submit button
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}
