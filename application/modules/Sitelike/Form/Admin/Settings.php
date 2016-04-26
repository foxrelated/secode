<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Settings.php 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelike_Form_Admin_Settings extends Engine_Form {

  public function init() {

    $core_settings = Engine_Api::_()->getApi('settings', 'core');

    // My stuff
    $this
            ->setTitle('Global Settings')
            ->setDescription("These settings affect all members in your community.");

    $this->addElement('Radio', 'like_browse_auth', array(
        'label' => ' Visibility of Likes listing pages and widgets',
        'description' => "Do you want to allow the public (visitors that are not logged-in) to view the Likes listing pages and the widgets ?",
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $core_settings->getSetting('like.browse.auth'),
    ));

    $this->addElement('Radio', 'like_link_position', array(
        'label' => 'Link for Browse Liked Items page',
        'description' => "Where do you want the link for Browse Liked Items page to be placed ?",
        'multiOptions' => array(
            3 => 'Main Navigation Menu',
            2 => 'Mini Navigation Menu',
            1 => 'Footer Menu',
            0 => 'Member Home Page Left side Navigation'
        ),
        'value' => $core_settings->getSetting('like.link.position'),
    ));

    $this->addElement('Radio', 'like_setting_button', array(
        'label' => ' Unlike Button Setting',
        'description' => 'Do you want Unlike Buttons to be shown in the various widgets? (Users will also be able to Unlike items from their main pages.)',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $core_settings->getSetting('like.setting.button'),
    ));

    $this->addElement('Text', 'like_title_turncation', array(
        'label' => 'Title Truncation Limit',
        'description' => 'What maximum limit should be applied to the number of characters in the titles of items in the widgets? (Enter a number between 1 and 999. Titles having more characters than this limit will be truncated. Complete titles will be shown on mouseover.)',
        'value' => $core_settings->getSetting('like.title.turncation', 16),
    ));

    // Add submit button
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}