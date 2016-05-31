<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteandroidapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Send.php 2015-10-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteandroidapp_Form_Admin_PushNotification_Send extends Engine_Form {

    public function init() {
        $this
                ->setTitle('Send Push Notifications')
        ;

        $availableLabels = array(
            'registered' => 'All Registered Members',
            'member_level' => 'Member levels',
            'network' => 'Networks',
            'specific_user' => 'Specific Users',
        );

        $this->addElement('Select', 'send_to', array(
            'label' => 'Send To',
            'description' => 'Select the user type to which you want to send Push Notification',
            'order' => 2,
            'multiOptions' => $availableLabels,
            'onchange' => 'javascript:hide_Others(this.value)'
        ));

        // MAKE NETWORK LIST
        $table = Engine_Api::_()->getDbtable('networks', 'network');
        $select = $table->select()
                ->from($table->info('name'), array('network_id', 'title'))
                ->order('title');
        $result = $table->fetchAll($select);

        foreach ($result as $value) {
            $networksOptions[$value->network_id] = $value->title;
        }
        if (count($networksOptions) > 0) {
            $this->addElement('Multiselect', 'network', array(
                'label' => 'Networks Selection',
                'description' => 'Select the networks, to which you want to send push notification. (Press Ctrl and click to select multiple networks.)',
                'multiOptions' => $networksOptions,
                'order' => 3,
                'value' => array(0)
            ));
        }

        // MAKE MEMBER LEVEL LIST
        $levelTable = Engine_Api::_()->getDbtable('levels', 'authorization');
        $query = $levelTable->select()
                ->from($levelTable->info('name'), array('level_id', 'title'))
                ->order('title');
        $resultArray = $table->fetchAll($query);

        foreach ($resultArray as $value) {
            $levelOptions[$value->level_id] = $value->title;
        }
        if (count($levelOptions) > 0) {
            $this->addElement('Multiselect', 'member_level', array(
                'label' => 'Member Levels Selection',
                'description' => 'Select the member levels, to which you want to send push notification. (Press Ctrl and click to select multiple member levels.)',
                'multiOptions' => $levelOptions,
                'order' => 5,
                'value' => array(0)
            ));
        }

        //SPECIFIC USERS SELECT
        $this->addElement('Text', 'to', array(
            'label' => 'Choose Users',
            'description' => 'Enter and choose the names of specific users to whom you want to send push notification. Note that you will only be able to choose from those users who have installed your Android App, and have logged into it once.',
            'order' => 7,
            'autocomplete' => 'off'));

        Engine_Form::addDefaultDecorators($this->to);

        // Init to Values
        $this->addElement('Hidden', 'toValues', array(
            'order' => 8,
            'validators' => array(
                'NotEmpty'
            ),
            'filters' => array(
                'HtmlEntities'
            ),
        ));
        Engine_Form::addDefaultDecorators($this->toValues);

        //PUSH NOTIFICATION TITLE
        $this->addElement('text', 'notification_title', array(
            'label' => 'Title',
            'required' => true,
            'allowEmpty' => false,
            'order' => 12,
        ));

        //PUSH NOTIFICATION SUBJECT
        $this->addElement('textarea', 'notification_subject', array(
            'label' => 'Message',
            'required' => true,
            'allowEmpty' => false,
            'order' => 15,
        ));

        //SUBMIT BUTTON
        $this->addElement('Button', 'submit', array(
            'label' => 'Send',
            'type' => 'submit',
            'order' => 999,
            'ignore' => true
        ));
    }

}
