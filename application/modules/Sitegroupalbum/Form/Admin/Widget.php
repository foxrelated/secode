<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupalbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Widget.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroupalbum_Form_Admin_Widget extends Engine_Form {

  public function init() {

    $this
            ->setTitle('Widget Settings')
            ->setDescription('Configure the general settings for the various widgets available with this plugin.');

    $this->addElement('Text', 'sitegroup_album', array(
        'label' => 'Group Profile Albums',
        'maxlength' => '3',
        'description' => 'How many albums will be shown in the group profile albums widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.album', 10),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Text', 'sitegroup_photo', array(
        'label' => 'Photos in Group Profile Albums',
        //'maxlength' => '3',
        'description' => 'How many photos will be shown below albums in the group profile albums widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.photo', 100),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Text', 'sitegroup_mostliked_photos', array(
        'label' => 'Group Profile Most Liked Photos',
        'maxlength' => '3',
        'description' => 'How many photos will be shown in the group profile most liked photos widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.mostliked.photos', 4),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Text', 'sitegroup_mostcommented_photos', array(
        'label' => 'Group Profile Most Commented Photos',
        'maxlength' => '3',
        'description' => 'How many photos will be shown in the group profile most commented photos widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.mostcommented.photos', 4),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Text', 'sitegroup_mostrecent_photos', array(
        'label' => 'Group Profile Photos Strip',
        'maxlength' => '3',
        'description' => 'How many photos will be shown in the group profile photos strip widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.mostrecent.photos', 7),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Text', 'sitegroup_homerecentphotos_widgets', array(
        'label' => 'Recent Photos',
        'maxlength' => '3',
        'description' => 'How many photos should be shown in the recent photos widget (value cannot be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.homerecentphotos.widgets', 4),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Text', 'sitegroup_mostpopularphotos_widgets', array(
        'label' => 'Popular Photos',
        'maxlength' => '3',
        'description' => 'How many photos should be shown in the most popular photos widget (value cannot be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.mostpopularphotos.widgets', 4),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}

?>