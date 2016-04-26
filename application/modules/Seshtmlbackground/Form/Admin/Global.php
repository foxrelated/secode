<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Seshtmlbackground
 * @package    Seshtmlbackground
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Global.php 2015-10-22 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Seshtmlbackground_Form_Admin_Global extends Engine_Form {

  public function init() {

    $this->setTitle('Global Settings')
            ->setDescription('These settings affect all members in your community.');
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->addElement('Text', "seshtmlbackground_licensekey", array(
        'label' => 'Enter License key',
        'description' => "Enter the your license key that is provided to you when you purchased this plugin. If you do not know your license key, please drop us a line from the Support Ticket section on SocialEngineSolutions website. (Key Format: XXXX-XXXX-XXXX-XXXX)",
        'allowEmpty' => false,
        'required' => true,
        'value' => $settings->getSetting('seshtmlbackground.licensekey'),
    ));
    
		if ($settings->getSetting('seshtmlbackground.pluginactivated')) {
			$this->addElement('Text', 'seshtmlbackground_ffmpeg_path', array(
          'label' => 'Path to FFMPEG',
          'description' => 'Please enter the full path to your FFMPEG installation. (Environment variables are not present)',
          'value' => $settings->getSetting('seshtmlbackground.ffmpeg.path', ''),
      ));
	    //Add submit button
	    $this->addElement('Button', 'submit', array(
	        'label' => 'Save Changes',
	        'type' => 'submit',
	        'ignore' => true
	    ));
    } else {
      //Add submit button
      $this->addElement('Button', 'submit', array(
          'label' => 'Activate your plugin',
          'type' => 'submit',
          'ignore' => true
      ));
    }
  }

}
