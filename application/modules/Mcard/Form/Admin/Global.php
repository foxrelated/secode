<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Mcard
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2010-10-13 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Mcard_Form_Admin_Global extends Engine_Form {

  public function init() {
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');

   	$this->addElement('Text', 'mcard_controllersettings', array(
      'label' => 'Enter License key',
      'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('mcard.controllersettings'),
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

    $this->addElement('Radio', 'mcard_visibility', array(      
    'label' => 'Membership Card Visibility',
    'description' => 'Who do you want to show membership cards to ?',
    'multiOptions' => array(
      2 => 'Only owners can view membership cards.',
      1 => 'Only members can view membership cards.',
      0 => 'Everyone can view membership cards.'             
    ),
    'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('mcard.visibility'),
    ));

    $this->addElement('Radio', 'mcard_print', array(      
    'label' => 'Printing Membership Cards',
    'description' => 'Who should be able to print membership cards ?',
    'multiOptions' => array(
      3 => 'Nobody, disable printing of membership cards.',
      2 => 'Only owners can print membership cards.',
      1 => 'Only members can print membership cards.' ,
      0 => 'Everyone can print membership cards.'
    ),
    'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('mcard.print'),
    ));

    // Add submit button
    $this->addElement('Button', 'submit', array(
	'label' => 'Save Changes',
	'type' => 'submit',
	'ignore' => true
    ));
  }
}