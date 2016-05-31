<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteandroidapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Settings.php 2015-10-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteandroidapp_Form_Admin_Settings extends Engine_Form {

    // IF YOU WANT TO SHOW CREATED ELEMENT ON PLUGIN ACTIVATION THEN INSERT THAT ELEMENT NAME IN THE BELOW ARRAY.
    public $_SHOWELEMENTSBEFOREACTIVATE = array(
        "environment_mode",
        "submit_lsetting"
    );

    public function init() {

        $this->setTitle('Global Settings')
                ->setDescription('These settings affect all members in your community.');


        $coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');
        // ELEMENT FOR LICENSE KEY
        $this->addElement('Text', 'siteandroidapp_lsettings', array(
            'label' => 'Enter License key',
            'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('siteandroidapp.lsettings'),
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

        $this->addElement('Button', 'submit_lsetting', array(
            'label' => 'Activate Your Plugin Now',
            'type' => 'submit',
            'ignore' => true
        ));


        if (!Engine_Api::_()->getApi('Core', 'siteapi')->isRootFileValid()) {
            $this->addElement('Radio', 'siteapi_valid_root_file', array(
                'label' => 'Modify Root File',
                'description' => 'Modify Root File',
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => 1
            ));
        }


        $linkApiKey = '<a href="http://developer.android.com/google/gcm/gs.html" class="buttonlink icon_help" target="_blank"></a>';
        $this->addElement('Text', 'siteandroidapp_google_server_api_key', array(
            'label' => 'Android API Key',
            'description' => "This API key will be used for push notifications. <a href='https://youtu.be/FgBQuQZUYtQ' target='_blank'>click here</a> to see how you can create this API Key for your app.",
            'required' => false,
            'allowEmpty' => true,
            'style' => 'width: 300px',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting("siteandroidapp.google.server.api.key")
        ));
        $this->getElement('siteandroidapp_google_server_api_key')->getDecorator('Description')->setEscape(false);

        $this->addElement('Radio', 'browse_as_guest', array(
            'label' => 'Enable Browse as Guest',
            'description' => 'Do you want to allow Non Logged-in Users (guests) to browse your app? If disabled, then only Logged-in members will be able to use / browse your app.',
            'required' => true,
            'allowEmpty' => false,
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting("siteandroidapp.browse.guest",1)
        ));

        $this->getElement('browse_as_guest')->getDecorator('Description')->setEscape(false);

        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
            'order' => 500,
        ));
    }

}
