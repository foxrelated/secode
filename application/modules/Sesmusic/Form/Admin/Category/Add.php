<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Add.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Form_Admin_Category_Add extends Engine_Form {

  public function init() {

    $this->setMethod('post');

    $this->addElement('Text', 'category_name', array(
        'allowEmpty' => false,
        'required' => true,
    ));

    $this->addElement('Hidden', 'id', array());

    $this->addElement('File', 'cat_icon', array(
        'label' => 'Category Icon',
        'description' => 'Upload an icon for the category. (The recommended dimension is 16x16 px.)'
    ));
    $this->cat_icon->addValidator('Extension', false, 'jpg,jpeg,png,gif,PNG,GIF,JPG,JPEG');

    $category_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('id', 0);
    if ($category_id) {
      $category = Engine_Api::_()->getItem('sesmusic_categories', $category_id);

      if ($category && $category->cat_icon) {
        $img_path = Engine_Api::_()->storage()->get($category->cat_icon, '')->getPhotoUrl();
        $path = 'http://' . $_SERVER['HTTP_HOST'] . $img_path;
        if (isset($path) && !empty($path)) {
          $this->addElement('Image', 'cat_icon_preview', array(
              'src' => $path,
              'width' => 16,
              'height' => 16,
          ));
        }
        $this->addElement('Checkbox', 'remove_cat_icon', array(
            'label' => 'Remove this icon.'
        ));
      }
    }

    $this->addElement('Button', 'submit', array(
        'label' => 'Add',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'Cancel',
        'link' => true,
        'prependText' => ' or ',
        'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index')),
        'onClick' => 'javascript:parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper'
        )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }

}