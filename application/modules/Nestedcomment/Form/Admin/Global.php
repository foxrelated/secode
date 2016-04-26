<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Nestedcomment
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2014-11-07 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Nestedcomment_Form_Admin_Global extends Engine_Form {

    // IF YOU WANT TO SHOW CREATED ELEMENT ON PLUGIN ACTIVATION THEN INSERT THAT ELEMENT NAME IN THE BELOW ARRAY.
    public $_SHOWELEMENTSBEFOREACTIVATE = array(
        "submit_lsetting", "environment_mode"
    );

    public function init() {

        $this
                ->setTitle('Global Settings')
                ->setName('nestedcomment_global_settings')
                ->setDescription('These settings affect all members in your community.');

        $coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');

        // ELEMENT FOR LICENSE KEY
        $this->addElement('Text', 'nestedcomment_lsettings', array(
            'label' => 'Enter License key',
            'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
            'value' => $coreSettingsApi->getSetting('nestedcomment.lsettings'),
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

        $this->addElement('Radio', 'nestedcomment_comment_pressenter', array(
            'label' => 'Posting Comments on Enter',
            'description' => 'Do you want the comments to be posted with pressing of the "Enter" key?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettingsApi->getSetting('nestedcomment.comment.pressenter', 1),
        ));

        $this->addElement('Text', 'nestedcomment_comment_per_page', array(
            'label' => 'Comments per page',
            'description' => 'How many comments should be shown per page on content view page? (users will be able to click on "view more comments" to view more comments)',
            'allowEmpty' => false,
            'maxlength' => '3',
            'required' => true,
            'filters' => array(
                new Engine_Filter_Censor(),
                'StripTags',
                new Engine_Filter_StringLength(array('max' => '3'))
            ),
            'value' => $coreSettingsApi->getSetting('nestedcomment.comment.per.page', 10),
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
        ));

        $this->addElement('Text', 'nestedcomment_reply_per_page', array(
            'label' => 'Replies per comment',
            'description' => 'How many replies will be shown per comment on content view page? (users will be able to click on "view more replies" to view more replies)',
            'allowEmpty' => false,
            'maxlength' => '3',
            'required' => true,
            'filters' => array(
                new Engine_Filter_Censor(),
                'StripTags',
                new Engine_Filter_StringLength(array('max' => '3'))
            ),
            'value' => $coreSettingsApi->getSetting('nestedcomment.reply.per.page', 4),
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
        ));

        $this->addElement('Radio', 'nestedcomment_reply_link', array(
            'label' => 'Reply Link',
            'description' => 'Where do you want to show the "Reply" link? [Note: This setting will not work for "Photo Lightbox Viewer".]',
            'multiOptions' => array(
                1 => 'On both, comments and replies',
                0 => 'On comments only'
            ),
            'value' => $coreSettingsApi->getSetting('nestedcomment.reply.link', 1)
        ));

        $this->addElement('Button', 'save', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}
