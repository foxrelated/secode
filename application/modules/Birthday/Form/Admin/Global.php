<?php

/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Birthday
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Global.php 6590 2010-17-11 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class Birthday_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');

    $this->addElement('Radio', 'widget', array(
          'decorators' => array(array('ViewScript', array(
    'viewScript' => '_formRadioButtonStructure.tpl',
    'class'      => 'form element'
          )))));

    $this->addElement('Radio', 'birthday_daystart', array(
      'label' => 'Calender Format',
      'description' => "Select a format for the calender in the Birthdays widget.",
      'multiOptions' => array(
        1 => 'First day of week as Sunday, and last as Saturday',
        0 => 'First day of week as Monday, and last as Sunday'
      ),
    'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('birthday.daystart', 1),
    ));
    
    $this->addElement('Text', 'birthday_entries', array(
      'label' => 'Birthday entries in widget',
      'description' => 'Enter the number of members to be shown in the Birthday widget.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('birthday.entries', 3),
    ));

    $this->addElement('Radio', 'birthday_link', array(
      'label' => 'Birthdays Link',
      'description' => "Do you want to show the Birthdays link on the Member Homepage in the left-side menu? This link will point to the birthdays listing page.",
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No'
      ),
    'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('birthday.link', 1),
    ));

		$this->addElement('Radio', 'birthday_listformat', array(
      'label' => 'Birth Date Format',
      'description' => 'Select a date format for birth dates on birthday listing pages.',
			'multiOptions' => array(
        0 => 'Day, Month Date (Ex. Sunday, March 27)',
        1 => 'Day, Date Month (Ex. Sunday, 27 March)',
				2 => 'Month Date, Day (Ex. March 27, Sunday)',
				3 => 'Date Month, Day (Ex. 27 March, Sunday)'
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('birthday.listformat', 0),
    ));

    $this->addElement('Text', 'birthday_listing', array(
      'label' => 'Listings Per Page',
      'description' => 'How many birthday listings will be shown per page? (Enter a number between 1 and 999)',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('birthday.listing', 20),
    ));

    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}