<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_Diary_Create extends Engine_Form {

    public function init() {

        $this->setTitle('Create New Event Diary')
                ->setAttrib('id', 'form-upload-diary')
                ->setAttrib('enctype', 'multipart/form-data');

        $this->addElement('Text', 'title', array(
            'label' => 'Diary Name',
            'maxlength' => '63',
            'required' => true,
            'allowEmpty' => false,
            'filters' => array(
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '63')),
            )
        ));

        $this->addElement('Textarea', 'body', array(
            'label' => 'Diary Note',
            'maxlength' => '512',
            'filters' => array(
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '512')),
            )
        ));

        $availableLabels = array(
            'everyone' => 'Everyone',
            'registered' => 'All Registered Members',
            'owner_network' => 'Friends and Networks',
            'owner_member_member' => 'Friends of Friends',
            'owner_member' => 'Friends Only',
            'owner' => 'Just Me'
        );

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('siteevent_diary', $viewer, 'auth_view');
        $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
        $viewOptionsReverse = array_reverse($viewOptions);
        $orderPrivacyHiddenFields = 786590;
        if (count($viewOptions) == 1) {
            $this->addElement('hidden', 'auth_view', array('value' => key($viewOptions), 'order' => ++$orderPrivacyHiddenFields));
        } elseif (count($viewOptions) < 1) {
            $this->addElement('hidden', 'auth_view', array('value' => 'owner', 'order' => ++$orderPrivacyHiddenFields));
        } else {
            $this->addElement('Select', 'auth_view', array(
                'label' => 'View Privacy',
                'description' => 'Who may see this diary?',
                'multiOptions' => $viewOptions,
                'value' => key($viewOptionsReverse),
            ));
            $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
        }

        $this->addElement('Button', 'submit', array(
            'label' => 'Create',
            'ignore' => true,
            'decorators' => array('ViewHelper'),
            'type' => 'submit'
        ));

        $this->addElement('Cancel', 'cancel', array(
            'prependText' => ' or ',
            'label' => 'cancel',
            'link' => true,
            'onclick' => "javascript:parent.Smoothbox.close();",
            'decorators' => array(
                'ViewHelper'
            ),
        ));

        $this->addDisplayGroup(array(
            'submit',
            'cancel'
                ), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper',
            ),
        ));
    }

}