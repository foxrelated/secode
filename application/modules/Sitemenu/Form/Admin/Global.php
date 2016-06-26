<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitemenu_Form_Admin_Global extends Engine_Form {

  // IF YOU WANT TO SHOW CREATED ELEMENT ON PLUGIN ACTIVATION THEN INSERT THAT ELEMENT NAME IN THE BELOW ARRAY.
  public $_SHOWELEMENTSBEFOREACTIVATE = array(
      "submit_lsetting", "environment_mode"
  );
  
  public function init() {

    $this->setTitle('Global Settings')
         ->setDescription('These settings affect all members in your community.');


    // ELEMENT FOR LICENSE KEY
    $this->addElement('Text', 'sitemenu_lsettings', array(
        'label' => 'Enter License key',
        'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemenu.lsettings'),
    ));

    if( APPLICATION_ENV == 'production' ) {
      $this->addElement('Checkbox', 'environment_mode', array(
          'label' => 'Your community is currently in "Production Mode". We recommend that you momentarily switch your site to "Development Mode" so that the CSS of this plugin renders fine as soon as the plugin is installed. After completely installing this plugin and visiting few stores of your site, you may again change the System Mode back to "Production Mode" from the Admin Panel Home. (In Production Mode, caching prevents CSS of new plugins to be rendered immediately after installation.)',
          'description' => 'System Mode',
          'value' => 1,
      ));
    } else {
      $this->addElement('Hidden', 'environment_mode', array('order' => 990, 'value' => 0));
    }
    
    //CACHE SETTING
    $this->addElement('Radio', 'sitemenu_cache_enable', array(
        'label' => 'Menu Caching',
        'description' => 'Do you want to enable caching for Advanced menus?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemenu.cache.enable', 1),
        'onchange' => 'hideCacheLifetime();'
    ));
    
    //CACHE LIFETIME
    $this->addElement('Text', 'sitemenu_cache_lifetime', array(
            'label' => 'Cache Lifetime',
            'description' => 'Enter lifetime of cache in days.',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemenu.cache.lifetime', 7),
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            )
                )
        );

        //Add submit button
    $this->addElement('Button', 'submit_lsetting', array(
        'label' => 'Activate Your Plugin Now',
        'type' => 'submit',
        'ignore' => true
    ));

    // Element: submit
    $this->addElement('Button', 'save', array(
        'label' => 'Save Settings',
        'type' => 'submit',
        'ignore' => true
    ));
  }
}