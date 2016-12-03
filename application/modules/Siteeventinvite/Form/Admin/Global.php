<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventinvite
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventinvite_Form_Admin_Global extends Engine_Form {

    public function init() {
        $this
                ->setTitle('Invite Settings')
                ->setDescription('These settings affect all members in your community.');

        $this->addElement('Radio', 'siteevent_badge', array(
            'label' => 'Allow to Promote Event',
            'description' => 'Do you want to enable Event Owners to promote their events via the Event Badge? (If selected, then Event Owners will be able to show off their events on external blogs or websites by using the Event Badge. Multiple configuration options will enable them to create attractive badges.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.badge', 1),
        ));

        $this->addElement('Dummy', 'yahoo_settings_temp', array(
            'label' => '',
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_formcontactimport.tpl',
                        'class' => 'form element'
                    )))
        ));

        $this->addElement('Radio', 'eventinvite_friend_invite_enable', array(
            'label' => 'Web Accounts for Import & Invite',
            'description' => "Do you want users of your site to be able to invite their friends to your site using all the available web accounts on your site? (If you select 'Yes' over here, then you will be able to choose below the various web accounts.)",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('eventinvite.friend.invite.enable', 1),
        ));

        $webmail_values = Engine_Api::_()->getApi('settings', 'core')->getSetting('eventinvite.show.webmail', 0);
        if (!empty($webmail_values)) {
            $webmail_values = unserialize($webmail_values);
        }
        $this->addElement('MultiCheckbox', 'eventinvite_show_webmail', array(
            'label' => 'Web Account services',
            'description' => "Select the web account services that you want to be available to users of your site for inviting their friends.",
            'multiOptions' => array(
                'gmail' => 'Gmail',
                'yahoo' => 'Yahoo',
                'window_mail' => 'Window Live',
                'aol' => 'AOL',
                'facebook_mail' => 'Facebook',
                'linkedin_mail' => 'Linkedin',
                'twitter_mail' => 'Twitter [This will only work when you have filled bitly keys at "bitly Short URL" section above.]',
                'csvFileImport' => "CSV File Import"
            ),
            'value' => $webmail_values
        ));

        $this->addElement('Text', 'siteeventinvite_manifestUrl', array(
            'label' => 'Event Invites URL alternate text for "event-invites"',
            'allowEmpty' => false,
            'required' => true,
            'description' => 'Please enter the text below which you want to display in place of "eventinvites" in the URLs of this plugin.',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventinvite.manifestUrl', "event-invites"),
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