<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteiosapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Settings.php 2015-10-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteiosapp_Form_Admin_Settings extends Engine_Form {

    // IF YOU WANT TO SHOW CREATED ELEMENT ON PLUGIN ACTIVATION THEN INSERT THAT ELEMENT NAME IN THE BELOW ARRAY.
    public $_SHOWELEMENTSBEFOREACTIVATE = array(
        "environment_mode",
        "submit_lsetting"
    );

    public function init() {
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $URL = $view->baseUrl() . "/admin/files";
        $click = '<a href="' . $URL . '" target="_blank">file manager</a>';
        $customBlocks = sprintf("The below chosen Apple APN certificate will be used to send push notifications from your server. The SocialEngineAddOns Support Team will upload the appropriate certificate from %s at the time of app building, and will choose it from below. [Note: Please do not change this value unless you are sure of it. This field is for the SocialEngineAddOns Support Team that will build your app.]", $click);
        $imageExtensions = array('pem');
        $it = new DirectoryIterator(APPLICATION_PATH . '/public/admin/');
        $apnPath = $settings->getSetting('siteiosapp.apple.server.apn.key', '');
        $tempApn = @explode("public/admin/", $apnPath);
        if (!empty($tempApn[1]))
            $apnVal = "public/admin/" . $tempApn[1];
        else
            $apnVal = "";
        foreach ($it as $file) {
            if ($file->isDot() || !$file->isFile())
                continue;
            $basename = basename($file->getFilename());
            if (!($pos = strrpos($basename, '.')))
                continue;
            $ext = strtolower(ltrim(substr($basename, $pos), '.'));
            if (!in_array($ext, $imageExtensions))
                continue;
            $logoOptions['public/admin/' . $basename] = $basename;
        }
        if (empty($logoOptions))
            $logoOptions['new'] = "no pem file";
        $this->setTitle('Global Settings')
                ->setDescription('These settings affect all members in your community.');
        $coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');
        // ELEMENT FOR LICENSE KEY
        $this->addElement('Text', 'siteiosapp_lsettings', array(
            'label' => 'Enter License key',
            'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('siteiosapp.lsettings'),
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
        if (isset($_REQUEST['updateCertificate']) && !empty($_REQUEST['updateCertificate'])) {
            $this->addElement('Select', 'apple_server_apn_key', array(
                'label' => 'Apple APN Server Certificate',
                'description' => $customBlocks,
                'multiOptions' => $logoOptions,
                'value' => $apnVal,
            ));
            $this->getElement('apple_server_apn_key')->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
            $this->addElement('Password', 'password', array(
                'label' => 'APN Certificate Password',
                'description' => 'The SocialEngineAddOns Support Team will set this password for you at the time of app building. [Note: Please do not change this value unless you are sure of it. This field is for the SocialEngineAddOns Support Team that will build your app.]',
                'required' => true,
                'allowEmpty' => false,
                'values' => $settings->getSetting('siteiosapp.password', '')
            ));
            $this->addElement('Radio', 'siteiosapp_apn_mode', array(
                'label' => 'Select Push Notification Mode',
                'description' => 'Select the push notifiction mode. The SocialEngineAddOns Support Team will set this password for you at the time of app building. [Note: Please do not change the password value]',
                'multiOptions' => array(
                    1 => 'Production',
                    0 => 'Development'
                ),
                'value' => $settings->getSetting('siteiosapp_apn_mode', 1)
            ));
        }

        $this->addElement('Radio', 'browse_as_guest', array(
            'label' => 'Enable Browse As Guest',
            'description' => 'Do you want to allow "Non-Logged In" users(guests) to browse your app? If disabled, only "Logged In" members will be able to use/browse your app.',
            'required' => true,
            'allowEmpty' => false,
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('siteiosapp.browse.guest', 1)
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
