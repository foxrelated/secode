<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteandroidapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Type.php 2015-10-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteandroidapp_Form_Admin_PushNotification_Type extends Engine_Form {

    public function init() {
        $this
                ->setTitle('Push Notification Type Settings')
                ->setDescription('Here you can change settings of individual push notification types, including the type of device alert that you want. Start by selecting a push notification type and then edit its settings.')
        ;

        $this->addElement('Select', 'type', array(
            'onchange' => 'javascript:pushnotificationTypeSettings(this.value);',
            'label' => 'Push Notification Type',
        ));

        $this->addElement('MultiCheckbox', 'siteandroidapp_pushtype', array(
            'label' => 'Alert Type',
            'description' => 'Select the type of alert for this push notification.',
            'multiOptions' => array(
                4 => 'Alert Message',
                2 => 'Badge Count Increment',
                1 => 'Sound',
            ),
        ));
        
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper'),
        ));

        // Element: cancel
        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'prependText' => ' or ',
            'ignore' => true,
            'link' => true,
            'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index')),
            'decorators' => array('ViewHelper'),
        ));

        // DisplayGroup: buttons
        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper',
            )
        ));
        
    }

}
