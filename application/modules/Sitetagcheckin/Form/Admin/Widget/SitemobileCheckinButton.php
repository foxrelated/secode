<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Startup.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitetagcheckin_Form_Admin_Widget_SitemobileCheckinButton extends Engine_Form {
  protected $_mode;

  public function getMode()
  {
    return $this->_mode;
  }

  public function setMode($mode)
  {
    $this->_mode = $mode;
    return $this;
  }

  public function init() {
   $coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');
   $this->addElement('Radio', 'checkin_use', array(
        'label' => '',
        'description' => 'Do you want users to be able to select a date while checking into this content? (Enabling this could be useful for content types that users could have visited in the past. If you disable this, then the current date is taken for the check-in action.)',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettingsApi->getSetting('checkin.use', 1)
    ));
   
   if ($this->getMode() == 'tablet') {
     
     $this->addElement('Radio', 'checkin_button_sidebar', array(
        'label' => '',
        'description' => 'How do you want to display the different phrases with statistics in this widget? (It is recommended to select the option \'In different lines\', if this widget is placed in the right/left columns of the page)',
        'multiOptions' => array(
            1 => 'In different lines',
            0 => 'In single line'
        ),
        'value' => $coreSettingsApi->getSetting('checkin.button.sidebar', 1)
    ));
   }
    
    $this->addElement('Radio', 'checkin_button', array(
        'label' => '',
        'description' => 'How do you want the "Check in here" button/link widget to be displayed? (You can change the button/link text below.)',
        'multiOptions' => array(
            1 => 'As button',
            0 => 'As link'
        ),
        'value' => $coreSettingsApi->getSetting('checkin.button', 1)
    ));
    
     $this->addElement('Text', 'checkin_button_link', array(
        'label' => '',
        'description' => "Enter the text of this button/link.",
        'value' => $coreSettingsApi->getSetting('checkin.button.link', 'Check-in here'),
    ));
     
     $this->addElement('Radio', 'checkin_icon', array(
        'label' => '',
        'description' => 'Choose the icon that you want to show in this check-in button/link.',
        'multiOptions' => array(
            1 => 'Tick-mark check-in icon',
            0 => 'Pin check-in icon'
        ),
        'value' => $coreSettingsApi->getSetting('checkin.icon', 1)
    ));
     
   $this->addElement('Text', 'checkin_verb', array(
        'label' => '',
        'description' => "Enter the text of the submit button in this.",
        'value' => $coreSettingsApi->getSetting('checkin.verb', 'Check-in'),
    ));
     
     $this->addElement('Text', 'checkedinto_verb', array(
        'label' => '',
        'description' => "Enter the action verb to be displayed in the feeds for check-ins using this button.",
        'value' => $coreSettingsApi->getSetting('checkedinto.verb', 'checked-into'),
    ));
     
     $this->addElement('Text', 'checkin_your', array(
        'label' => '',
        'description' => "Enter the text for showing the number of check-ins made by the viewer of this content. (Ex: You\'ve checked in here)",
        'value' => $coreSettingsApi->getSetting('checkin.your', 'You\'ve checked-in here'),
    ));
     
    $this->addElement('Text', 'checkin_total', array(
        'label' => '',
        'description' => "Enter the text for showing total number of check-ins made on this content. (Ex: Total check-ins here)",
        'value' => $coreSettingsApi->getSetting('checkin_total', 'Total check-ins here'),
    ));  
    
  }

}