<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Birthdayemail
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Email.php 6590 2010-17-11 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class Birthdayemail_Form_Admin_Email extends Engine_Form
{
  public function init()
  {
     // create an object for view
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
   
    //check if Sitemailtemplates Plugin is enabled
    $sitemailtemplates = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemailtemplates');

    $this
      ->setTitle('Email Settings')
      ->setDescription('This page contains settings for the Automatic Birthday Wish and the Automatic Birthday Reminder emails. Below, you can enable / disable these emails and configure the design of their templates. You can also send sample emails to yourself to see the content.');

    $this->addElement('Radio', 'birthdayemail_wish', array(
      'label' => 'Automatic Birthday Wishes',
      'description' => "Do you want the site to send automatic wishes to members on their birthdays? (The design of the email template for this can be customized below, and the content of this email template can be customized from the Mail Templates section in Settings.)",
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No'
      ),
    ));
    
    // Get Image
    $image_id = Engine_Api::_()->getApi('settings', 'core')->getSetting('birthdayemail.wish.image', 0);
    if(!empty($image_id)) {
      $path = Engine_Api::_()->storage()->get($image_id, '')->getPhotoUrl();
    }
    else {
      // By Default image
      $path = 'http://' . $_SERVER['HTTP_HOST']. $view->baseUrl(). '/application/modules/Birthdayemail/externals/images/ChocolateBirthdayCake.jpg';
    }

    $this->addElement('File', 'birthdayemail_wish_image', array(
      'label' => 'Birthday Wish Image',
      'description' => 'Upload the birthday wish image. This image gets embedded in the birthday wish email that goes out. The existing image that is set can be seen below.',
    ));
    $this->birthdayemail_wish_image->addValidator('Extension', false, 'jpg,jpeg,png,gif,JPG,JPEG,PNG,GIF');

    $this->addElement('Image', 'birthdayemail_wish_image_display', array(
      'src' => $path,
      'height' => 150,
      'width' => 150,
    ));

    $this->addElement('Radio', 'birthdayemail_reminder', array(
      'label' => 'Automatic Birthday Reminders',
      'description' => "Do you want the site to send automatic birthday reminders to members for their friends' birthdays? (The design of the email template for this can be customized below, and the content of this email template can be customized from the Mail Templates section in Settings.)",
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No'
      ),
    ));

    $this->addElement('Select', 'birthdayemail_reminder_options', array(
      'label' => 'Duration before birthday for reminder',
      'description' => 'Select the duration before a friend’s birthday when the member should get its reminder.',
      'multiOptions' => array(
        '0' => '1 day',
        '1' => '1 week',
	'2' => '1 month',
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('birthdayemail.reminder.time', 1),
    ));
    
    if(!$sitemailtemplates) {
			$this->addElement('Text', 'birthdayemail_site_title', array(
				'label' => 'Site Title',
				'filters' => array(
					new Engine_Filter_Censor(),
					'StripTags',
					new Engine_Filter_StringLength(array('max' => '63'))
				),
				'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('birthdayemail.site.title', Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 1))
			));
			
			$this->addElement('Text', 'birthdayemail_color', array(
				'decorators' => array(array('ViewScript', array(
					'viewScript' => '_formImagerainbow1.tpl',
					'class'      => 'form element'
				)))
			));
			
			$this->addElement('Text', 'birthdayemail_title_color', array(
				'decorators' => array(array('ViewScript', array(
					'viewScript' => '_formImagerainbow2.tpl',
					'class'      => 'form element'
				)))
			));
    }

    $this->addElement('Checkbox', 'birthdayemail_demo', array(
      'label' => 'Send me test emails for birthday reminder and birthday wish to check the above settings.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('birthdayemail.demo', 1),
    ));

    $this->addElement('Text', 'birthdayemail_admin', array(
      'label' => 'Email ID for Testing',
			'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
        array('EmailAddress', true),
			 ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('birthdayemail.admin', Engine_API::_()->seaocore()->getSuperAdminEmailAddress()),
    ));
		$this->birthdayemail_admin->getValidator('NotEmpty')->setMessage('Please enter a valid email address.', 'isEmpty');

    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}
