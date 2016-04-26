<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Lkesettings.php 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelike_Form_Admin_Likesettings extends Engine_Form {

  public function init() {
  
    // create an object for view
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    // My stuff
    $this
            ->setTitle(' Like Button View')
            ->setDescription("Here, you can customize the Like button on your site.");

    // Get Image
    $image_id = Engine_Api::_()->getApi('settings', 'core')->getSetting('like.thumbsup.image', 0);
    if (!empty($image_id)) {
      $cdn_path = Engine_Api::_()->sitelike()->getCdnPath();
      $img_path = Engine_Api::_()->storage()->get($image_id, '')->getPhotoUrl();
      if ($cdn_path == "") {
        $path = 'http://' . $_SERVER['HTTP_HOST'] . $img_path;
      } else {
        $img_cdn_path = str_replace($cdn_path, '', $img_path);
        $path = $cdn_path . $img_cdn_path;
      }
    } else {
      // By Default image
      $path = $view->layout()->staticBaseUrl . 'application/modules/Sitelike/externals/images/thumb-up.png';
    }

    $this->addElement('File', 'like_thumbsup_image', array(
        'label' => 'Like Thumbs-up Image',
        'description' => 'Upload an image to customize the Like Button. (The dimensions of the image should be 13x13 px. The currently associated image is shown below this field.)'
    ));
    $this->like_thumbsup_image->addValidator('Extension', false, 'jpg,jpeg,png,gif,PNG,GIF,JPG,JPEG');

    $this->addElement('Image', 'like_thumbsup_display', array(
        'src' => $path,
        'width' => 13,
        'height' => 13
    ));


    $image_id = Engine_Api::_()->getApi('settings', 'core')->getSetting('like.thumbsdown.image', 0);
    if (!empty($image_id)) {
      $cdn_path = Engine_Api::_()->sitelike()->getCdnPath();
      $img_path = Engine_Api::_()->storage()->get($image_id, '')->getPhotoUrl();
      if ($cdn_path == "") {
        $path = 'http://' . $_SERVER['HTTP_HOST'] . $img_path;
      } else {
        $img_cdn_path = str_replace($cdn_path, '', $img_path);
        $path = $cdn_path . $img_cdn_path;
      }
    } else {
      // By Default image
      $path = $view->layout()->staticBaseUrl . 'application/modules/Sitelike/externals/images/thumb-down.png';
    }

    $this->addElement('File', 'like_thumbsdown_image', array(
        'label' => 'Unlike Thumbs-down Image',
        'description' => 'Upload an image to customize the Un-like Button. (The dimensions of the image should be 13x13 px. The currently associated image is shown below this field.)'
    ));
    $this->like_thumbsdown_image->addValidator('Extension', false, 'jpg,jpeg,png,gif');

    $this->addElement('Image', 'like_thumbsdown_display', array(
        'src' => $path,
        'width' => 13,
        'height' => 13
    ));

    $this->addElement('Text', 'like_background_color', array(
        'label' => 'Background Color',
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formImagerainbow1.tpl',
                    'class' => 'form element'
                )))
    ));

    $this->addElement('Text', 'like_background_haourcolor', array(
        'label' => 'Haour Color',
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formImagerainbow4.tpl',
                    'class' => 'form element'
                )))
    ));

    $this->addElement('Text', 'like_text_color', array(
        'label' => 'Text Color',
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formImagerainbow3.tpl',
                    'class' => 'form element'
                )))
    ));

    $this->addElement('Text', 'like_haour_color', array(
        'label' => 'Haour Color',
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formImagerainbow2.tpl',
                    'class' => 'form element'
                )))
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));
    $buttons[] = 'submit';

    $this->addElement('Button', 'default_settings', array(
        'label' => 'Reset to Default',
        'type' => 'submit',
        'prependText' => ' or ',
        'decorators' => array(
            'ViewHelper'
        )
    ));
    $buttons[] = 'default_settings';


    $this->addDisplayGroup($buttons, 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

}