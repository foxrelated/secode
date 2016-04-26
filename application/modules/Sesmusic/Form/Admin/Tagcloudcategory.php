<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Tagcloudcategory.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Form_Admin_Tagcloudcategory extends Engine_Form {

  public function init() {


    $this->addElement('Select', 'contentType', array(
        'label' => 'Choose the content type belonging to which categories will be shown in this.',
        'multiOptions' => array(
            'album' => 'Music Album',
            'song' => 'Song',
        ),
        'value' => 'album',
    ));
    
    $this->addElement('Select', 'showType', array(
        'label' => 'View Type',
        'multiOptions' => array(
            'simple' => 'Hierarchy View',
            'tagcloud' => 'Cloud View',
        ),
        'value' => 'simple',
    ));

    $this->addElement('Select', 'image', array(
        'label' => 'Do you want to show category icon in this widget? [Display of icon depend on the View Type chosen by you from the above setting.]',
        'multiOptions' => array(
            '0' => 'Yes',
            '1' => 'No',
            //'2' => 'Both'
        ),
        'value' => 1,
    ));

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $URL = $view->baseUrl() . "/admin/sesbasic/settings/color-chooser";
    $click = '<a href="' . $URL . '" target="_blank">Click Here</a>';
    $changeColorText = sprintf('%s to choose the color of category text for "Cloud View".]', $click);
    
    $this->addElement('Text', "color", array(
        'label' => $changeColorText,
        'value' => '#00f',
    ));
    $this->getElement('color')->getDecorator('Label')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

//    $this->addElement('Text', "itemCountPerPage", array(
//        'label' => 'Limit data to show [This settings only work when you choose "Tag Cloud Categories" from the above settings.]',
//        'value' => '50',
//    ));

    $this->addElement('Text', "text_height", array(
        'label' => 'Choose height of category text for "Cloud View".]',
        'value' => '15',
    ));

    $this->addElement('Text', "height", array(
        'label' => 'Choose height of category container for “Cloud View” (in pixels).',
        'value' => '150',
    ));
  }

}
