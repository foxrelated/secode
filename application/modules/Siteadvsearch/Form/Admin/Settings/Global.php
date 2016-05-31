<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteadvsearch_Form_Admin_Settings_Global extends Engine_Form {

  // IF YOU WANT TO SHOW CREATED ELEMENT ON PLUGIN ACTIVATION THEN INSERT THAT ELEMENT NAME IN THE BELOW ARRAY.
  public $_SHOWELEMENTSBEFOREACTIVATE = array(
      "submit_lsetting", "environment_mode"
  );

  public function init() {
    $this
            ->setTitle('Global Settings')
            ->setDescription('These settings affect all members in your community.');

    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    // ELEMENT FOR LICENSE KEY
    $this->addElement('Text', 'siteadvsearch_lsettings', array(
        'label' => 'Enter License key',
        'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
        'value' => $coreSettings->getSetting('siteadvsearch.lsettings'),
    ));

    if (APPLICATION_ENV == 'production') {
      $this->addElement('Checkbox', 'environment_mode', array(
          'label' => 'Your community is currently in "Production Mode". We recommend that you momentarily switch your site to "Development Mode" so that the CSS of this plugin renders fine as soon as the plugin is installed. After completely installing this plugin and visiting few stores of your site, you may again change the System Mode back to "Production Mode" from the Admin Panel Home. (In Production Mode, caching prevents CSS of new plugins to be rendered immediately after installation.)',
          'description' => 'System Mode',
          'value' => 1,
      ));
    } else {
      $this->addElement('Hidden', 'environment_mode', array('order' => 990, 'value' => 0));
    }

    //Add submit button
    $this->addElement('Button', 'submit_lsetting', array(
        'label' => 'Activate Your Plugin Now',
        'type' => 'submit',
        'ignore' => true
    ));

    $this->addElement('Radio', 'siteadvsearch_result_type', array(
        'label' => 'Modules for Search Results',
        'description' => 'Do you want to show search results from all the installed modules, or from only selected modules? [If you choose "Selected Modules", then you will be able to enable / disable modules for advanced search from “Manage Modules” section of this plugin.]',
        'multiOptions' => array(
            1 => 'All Modules',
            0 => 'Selected Modules'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('siteadvsearch.result.type', 1),
    ));

    $this->addElement('Radio', 'siteadvsearch_show_search_box', array(
        'label' => 'Advanced Search Box Visibility',
        'description' => 'Do you want the Advanced Search Box to be visible to non-logged-in users?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('siteadvsearch.show.search.box', 1),
    ));

    $this->addElement('Text', 'siteadvsearch_showmore', array(
        'label' => 'Maximum Results Limit in Advanced Search Box',
        'allowEmpty' => false,
        'maxlength' => '3',
        'required' => true,
        'description' => 'Enter the maximum limit for auto-suggest search results that come in the global search field of mini-menu. [Note: Recommended value of this limit is 5. If you enter a limit more than this, then there might be UI issues.]',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('siteadvsearch.showmore', 5),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // Add submit button
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}