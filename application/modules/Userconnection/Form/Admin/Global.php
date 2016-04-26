<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    User Connection
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2010-07-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Userconnection_Form_Admin_Global extends Engine_Form
{
	public function init()
  {
  	$this
      ->setTitle('Global Settings')
      ->setDescription('This page contains the general settings for the User Connections plugin');

   	$this->addElement('Text', 'user_licensekey', array(
      'label' => 'Enter License key',
      'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('user.licensekey'),
    ));

      if( APPLICATION_ENV == 'production' ) {
	      $this->addElement('Checkbox', 'environment_mode', array(
		      'label' => 'Your community is currently in "Production Mode". We recommend that you momentarily switch your site to "Development Mode" so that the CSS of this plugin renders fine as soon as the plugin is installed. After completely installing this plugin and visiting few pages of your site, you may again change the System Mode back to "Production Mode" from the Admin Panel Home. (In Production Mode, caching prevents CSS of new plugins to be rendered immediately after installation.)',
		      'description' => 'System Mode',
		      'value' => 1,
	      )); 
      }else {
	      $this->addElement('Hidden', 'environment_mode', array('order' => 990, 'value' => 0));
      }

      $this->addElement('Button', 'submit_lsetting', array(
	      'label' => 'Activate Your Plugin Now',
	      'type' => 'submit',
	      'ignore' => true
      ));
    
    $this->addElement('Radio', 'structure', array(
  	'decorators' => array(array('ViewScript', array(
    'viewScript' => '_formRadioButtonStructure.tpl',
    'class'      => 'form element'
  	)))));
  	
    $this->addElement('Radio', 'userconnection_message', array( 
     	'label' => 'No-connection Message Setting',
     	'description' => 'Select whether or not you want to show the message in the Connection Path widget if there is no connection between the profile viewer and the profile owner.',
      'multiOptions' => array(
        5 => 'Yes, show message.',
        6 => 'No, do not show message.'
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('userconnection.message'),
    ));    
    
    $this->addElement('Text', 'show_msg', array(
      'label' => 'Enter the no-connection message',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('show.msg'),
     ));
   
    $this->addElement('Radio', 'arrow', array(
  	'decorators' => array(array('ViewScript', array(
    'viewScript' => '_formRadioButtonArrow.tpl',
    'class'      => 'form element',    
     )))));
  
    $this->addElement('Radio', 'indicators', array(
  	'decorators' => array(array('ViewScript', array(
    'viewScript' => '_formRadioButton.tpl',
    'class'      => 'form element'
  	)))));

    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }    
}
?>