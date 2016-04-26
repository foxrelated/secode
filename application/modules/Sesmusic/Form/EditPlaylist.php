<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: EditPlaylist.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Form_EditPlaylist extends Engine_Form {

  public function init() {

    parent::init();

    $this->setTitle('Edit Playlist')
            ->setAttrib('id', 'form-upload-music')
            ->setAttrib('name', 'playlist_edit')
            ->setAttrib('enctype', 'multipart/form-data')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    $this->addElement('Text', 'title', array(
        'label' => 'Playlist Name',
        'placeholder' => 'Enter Playlist Name',
        'maxlength' => '63',
        'filters' => array(
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '63')),
        )
    ));

    //Init descriptions
    $this->addElement('Textarea', 'description', array(
        'label' => 'Playlist Description',
        'placeholder' => 'Enter Playlist Description',
        'maxlength' => '300',
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '300')),
            new Engine_Filter_EnableLinks(),
        ),
    ));

    //Init album art
    $this->addElement('File', 'mainphoto', array(
        'label' => 'Playlist Photo',
    ));
    $this->mainphoto->addValidator('Extension', false, 'jpg,png,gif,jpeg');

    $playlist_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('playlist_id');
    if ($playlist_id) {
      $photoId = Engine_Api::_()->getItem('sesmusic_playlist', $playlist_id)->photo_id;
      if ($photoId) {
        $img_path = Engine_Api::_()->storage()->get($photoId, '')->getPhotoUrl();
        $path = $img_path;
        if (isset($path) && !empty($path)) {
          $this->addElement('Image', 'playlist_mainphoto_preview', array(
              'label' => 'Playlist Photo Preview',
              'src' => $path,
              'width' => 100,
              'height' => 100,
          ));
        }
      }
    }

    if ($playlist_id) {
      $photoId = Engine_Api::_()->getItem('sesmusic_playlist', $playlist_id)->photo_id;
      if ($photoId) {
        $this->addElement('Checkbox', 'remove_photo', array(
            'label' => 'Yes, remove playlist photo.'
        ));
      }
    }

    //Init file uploader
    $fancyUpload = new Engine_Form_Element_FancyUpload('file');
    $fancyUpload->clearDecorators()
            ->addDecorator('FormFancyUpload')
            ->addDecorator('viewScript', array(
                'viewScript' => '_FancyUpload.tpl',
                'placement' => '',
    ));
    Engine_Form::addDefaultDecorators($fancyUpload);
    $this->addElement($fancyUpload);

    //Pre-fill form values
    $this->addElement('Hidden', 'playlist_id');
    $this->removeElement('fancyuploadfileids');

    //Element: execute
    $this->addElement('Button', 'execute', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'sesmusic_playlists', true),
        'onclick' => '',
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    // DisplayGroup: buttons
    $this->addDisplayGroup(array(
        'execute',
        'cancel',
            ), 'buttons', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper'
        ),
    ));
  }

}