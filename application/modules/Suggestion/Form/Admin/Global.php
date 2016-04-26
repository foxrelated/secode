<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Suggestion_Form_Admin_Global extends Engine_Form {

  public function init() {
    $this
            ->setTitle('Global Settings')
            ->setDescription('This page contains the general settings for the Suggestion plugin');

    $this->addElement('Text', 'suggestion_controllersettings', array(
        'label' => 'License Key',
        'required' => true,
        'description' => 'Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('suggestion.controllersettings')
    ));


    if( APPLICATION_ENV == 'production' ) {
      $this->addElement('Checkbox', 'environment_mode', array(
          'label' => 'Your community is currently in "Production Mode". We recommend that you momentarily switch your site to "Development Mode" so that the CSS of this plugin renders fine as soon as the plugin is installed. After completely installing this plugin and visiting few pages of your site, you may again change the System Mode back to "Production Mode" from the Admin Panel Home. (In Production Mode, caching prevents CSS of new plugins to be rendered immediately after installation.)',
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


    $this->addElement('Dummy', 'yahoo_settings_temp', array(
        'label' => '',
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formcontactimport.tpl',
                    'class' => 'form element'
            )))
    ));
    
    $this->addElement('Radio', 'suggestion_friend_invite_enable', array(
        'label' => 'Web Accounts for Import & Invite',
        'description' => "Do you want users of your site to be able to invite their friends to your site using all the available web accounts on your site? (If you select 'Yes' over here, then you will be able to choose below the various web accounts.)",
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('suggestion.friend.invite.enable', 1),
        
    ));

    $webmail_values = Engine_Api::_()->getApi('settings', 'core')->getSetting('suggestion.show.webmail', 0);
    if (!empty($webmail_values)) {
      $webmail_values = unserialize($webmail_values);
    }
    $this->addElement('MultiCheckbox', 'suggestion_show_webmail', array(
        'label' => 'Web Account services',
        'description' => "Select the web account services that you want to be available to users of your site for inviting their friends.",
        'multiOptions' => array(
            'gmail' => 'Gmail',
            'yahoo' => 'Yahoo',
            'window_mail' => 'Window Live',
            //'aol' => 'AOL',
            'facebook_mail' => 'Facebook',
            'linkedin_mail' => 'Linkedin',
            'twitter_mail' => 'Twitter',
        ),
        'value' => $webmail_values
    ));

    $this->addElement('Radio', 'suggestion_friend_invite', array(
        'label' => 'Main User Navigation Invite Link',
        'description' => "Do you want the 'Invite' link at the main user navigation bar to point to the Invite Friends page of this plugin. (If set to no, the 'Invite' link will take the users to the Invite Friends page of core 'Invite' plugin.)",
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('suggestion.friend.invite', 0),
    ));

//     $this->addElement('Radio', 'suggestion_page_speed', array(
//         'label' => 'Page load speed',
//         'description' => "If your page load speed is low then activate page load speed and then check your page speed.",
//         'multiOptions' => array(
//             1 => 'Activate',
//             0 => 'De-activate'
//         ),
//         'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('suggestion.page.speed', 0),
//     ));

    $this->addElement('Text', 'sugg_truncate_limit', array(
        'label' => 'Title Truncation Limit',
        'description' => 'What maximum limit should be applied to the number of characters in the titles of items in the widgets? (Enter a number between 1 and 999. Titles having more characters than this limit will be truncated. Complete titles will be shown on mouseover.)',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sugg.truncate.limit')
    ));
    
    $this->addElement('Radio', 'seaocore_siteenginessl', array(
			'label' => 'Website Engine SSL',
			'description' => "If your site runs on \"HTTPS\" then enable this setting. This setting is used when site user use the invite Facebook friends funtionality on your site.",
			'multiOptions' => array(1 => 'Yes', 0 => 'No'),
			'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.siteenginessl', 0)
			
		));

    // Add submit button
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}
?>