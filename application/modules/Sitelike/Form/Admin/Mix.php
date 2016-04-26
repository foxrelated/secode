<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Mix.php 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelike_Form_Admin_Mix extends Engine_Form {

  public function init() {
  
    $mixsettingsTable = Engine_Api::_()->getDbtable('mixsettings', 'sitelike');
    
    $this
            ->setTitle('Mixed Content Widgets')
            ->setDescription("Configure the settings for the mixed content widgets. These widgets show liked items of various content types as selected below. The content types available below
        are according to the ones chosen by you from the “Manage Modules” section.");

    $mixSettingsResults = $mixsettingsTable->getMixLikeItems();

    foreach ($mixSettingsResults as $modNameKey => $modNameValue) {
      if ($modNameValue == 'Members') {
        $this->addElement('Radio', 'user', array(
            'label' => 'Member Profiles',
            'description' => "Do you want Member Profiles to be part of the items shown in mixed content widgets ?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $mixsettingsTable->getSetting('user'),
        ));
      } else {
        $this->addElement('Radio', $modNameKey, array(
            'label' => $modNameValue,
            'description' => "Do you want " . $modNameValue . " to be part of the items shown in mixed content widgets ?",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $mixsettingsTable->getSetting($modNameKey),
        ));
      }
    }

    // Add submit button
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}