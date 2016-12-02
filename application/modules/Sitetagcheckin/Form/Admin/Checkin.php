<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Checkin.php 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitetagcheckin_Form_Admin_Checkin extends Engine_Form {

  public function init() {

    //CHECKIN SETTING
    $this
            ->setTitle('Check-in Settings')
            ->setDescription('This plugin enables members of your site to check-in into various content of your site according to the configurations made by you. Checking-in into a content provides users a good way to highlight their visit to a content. You can allow users to check-in into a content by enabling check-ins into it from “Manage Modules” tab, placing the "Content Check-in button & stats" widget on the content profile page from the Layout Editor and configuring the appropriate settings for the widget. You can also configure the related action verb for the content, such that the action could be called something different from "checked-in", like "viewed", "listened to", etc. For each such action made by a member, activity feed is published. While doing an action, users will also be able to publish an update for it. Relevant check-in / action stats are also shown for the various content which can be taken as an indicator of their popularity. Below, you can configure some useful settings for these actions.');

    //CHECKOUT TIME SETTING
    $this->addElement('Select', 'sitetagcheckin_max_status_time', array(
        'label' => 'Maximum Time for Checked-in Status in a Content',
        'description' => "Select the maximum time duration for which users will be assumed to be checked-in into a content. (Note: Users will be automatically checked out from the content before the selected time duration, once they check-in into some other content. This setting is useful to prevent multiple consecutive check-in count increments into a content by users, and to maintain a genuine count of content check-in actions.)",
        'multiOptions' => array(
            300 => '5 Minutes',
            600 => '10 Minutes',
            900 => '15 Minutes',
            1800 => '30 Minutes',
            3600 => '1 Hour',
            7200 => '2 Hours',
            21600 => '6 Hours',
            43200 => '12 Hours',
            86400 => '24 Hours'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->sitetagcheckin_max_status_time,
    ));

    //VALUE FOR STATUS UPDATE BOX
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity') && Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedactivity.isActivate', 0)) {
      $description = sprintf(Zend_Registry::get('Zend_Translate')->_("Do you want to enable Check-in via the status update box? (Selecting 'Yes' over here will enable users to share their location while updating status from the status update box (this requires the '%1sAdvanced Activity Feeds / Wall Plugin%2s').From the 'Location Entities' tab, you can select the entities that should be select-able as locations.."), "<a href='http://www.socialengineaddons.com/socialengine-advanced-activity-feeds-wall-plugin' target='_blank'>", "</a>");
      $this->addElement('Radio', 'sitetagcheckin_status_update', array(
          'label' => 'Check-in via Status Update Box',
          'description' => $description,
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.status.update', 1),
      ));
      $this->sitetagcheckin_status_update->addDecorator('Description', array('placement' => 'PREPEND', 'class' => 'description', 'escape' => false));
    } else {
      $description = sprintf(Zend_Registry::get('Zend_Translate')->_("Check-in via the status update box is dependent on the '%1sAdvanced Activity Feeds / Wall Plugin%2s' and requires it to be installed and enabled on your site. Please install this plugin after downloading it from your Client Area on SocialEngineAddOns. You may purchase this plugin %3sover here%4s."), "<a
					href='http://www.socialengineaddons.com/socialengine-advanced-activity-feeds-wall-plugin' target='_blank'>", "</a>", "<a href='http://www.socialengineaddons.com/socialengine-advanced-activity-feeds-wall-plugin' target='_blank'>", "</a>");
      $this->addElement('Dummy', 'sitetagcheckin_update', array(
          'label' => 'Check-in via Status Update Box',
          'description' => "$description",
      ));
      $this->sitetagcheckin_update->addDecorator('Description', array('placement' => 'PREPEND', 'class' => 'description', 'escape' => false));
    }

    //SUBMIT BUTTON
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}