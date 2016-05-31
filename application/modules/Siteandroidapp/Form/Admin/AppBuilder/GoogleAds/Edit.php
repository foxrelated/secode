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
class Siteandroidapp_Form_Admin_AppBuilder_GoogleAds_Edit extends Engine_Form {

    public function init() {
        $this
                ->setTitle('Edit Ads Settings')
        ;                

        $this->addElement('Text', 'google_ad_show_after', array(
            'label' => 'Content Count for Displaying Ads',
            'description' => 'For advertising on the content browse page of this module, enter the count of content items after which ads should be shown. Thus, an ad will be shown after every such successive count of content on the browse page of this module.',
            'value' => 8
        ));

//        $this->addElement('Select', 'google_ad_type', array(
//            'label' => 'Ad Type',
//            'description' => 'Choose the type of ads that you want to be shown on this module’s browse page. The “App Based Ads” are ads of other Android apps. The “Content Based Ads” are ads of other advertised content types.',
//            'required' => true,
//            'allowEmpty' => false,
//            'multiOptions' => array(
//                'content' => 'Content Based Ads',
//                'appinstall' => 'App Based Ads'
//            ),
//            'value' => 'content'
//        ));

        $this->addElement('Radio', 'enabled_google_ad', array(
            'label' => 'Advertising Status',
            'multiOptions' => array(
                1 => 'Enable Advertising in this module',
                0 => 'Disable Advertising in this module'
            ),
            'value' => 0
        ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Cancel', 'close', array(
            'label' => 'Close',
            'prependText' => ' or ',
            'type' => 'cancel',
//            'ignore' => true,
            'link' => true,
            'onclick' => 'parent.Smoothbox.close()',
//            'class' => 'smoothbox',
//            'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'download')),
            'decorators' => array('ViewHelper'),
        ));

        // DisplayGroup: buttons
        $this->addDisplayGroup(array('submit', 'close'), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper',
            )
        ));
    }

}
