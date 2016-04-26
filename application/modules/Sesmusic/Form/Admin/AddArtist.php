<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: AddArtist.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Form_Admin_AddArtist extends Engine_Form {

  public function init() {

    $this->setTitle('Add a New Artist')
            ->setDescription('Enter the details of artist to be added to your website.');

    $this->addElement('Text', "name", array(
        'label' => 'Enter Artist Name.',
        'placeholder' => 'Enter Artist Name',
        'allowEmpty' => false,
        'required' => true,
    ));

    $this->addElement('Textarea', 'overview', array(
        'label' => 'Enter About Artist.',
        'placeholder' => 'Enter About Artist',
        'maxlength' => '3000',
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '3000')),
            new Engine_Filter_EnableLinks(),
        ),
    ));

    $this->addElement('File', 'artist_photo', array(
        'label' => 'Upload Artist Photo.',
    ));
    $this->artist_photo->addValidator('Extension', false, 'jpg,jpeg,png,gif,PNG,GIF,JPG,JPEG');

    $artist_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('artist_id');
    if ($artist_id) {
      $artist = Engine_Api::_()->getItem('sesmusic_artists', $artist_id);

      if ($artist && $artist->artist_photo) {
        $img_path = Engine_Api::_()->storage()->get($artist->artist_photo, '')->getPhotoUrl();
        $path = 'http://' . $_SERVER['HTTP_HOST'] . $img_path;
        if (isset($path) && !empty($path)) {
          $this->addElement('Image', 'artist_photo_preview', array(
              'src' => $path,
              'width' => 100,
              'height' => 100,
          ));
        }
      }
    }

    $this->addElement('Button', 'button', array(
        'label' => 'Add',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'Cancel',
        'link' => true,
        'prependText' => ' or ',
        'onclick' => 'javascript:parent.Smoothbox.close()',
        'decorators' => array(
            'ViewHelper',
        ),
    ));
    $this->addDisplayGroup(array('button', 'cancel'), 'buttons');
  }

}