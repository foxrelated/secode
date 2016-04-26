<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Add.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_Diary_Add extends Engine_Form {

    public function init() {

        $this->setTitle('Add To Diary')
                ->setAttrib('id', 'form-upload-diary')
                ->setAttrib('enctype', 'multipart/form-data');

        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        $diaryDatas = Engine_Api::_()->getDbtable('diaries', 'siteevent')->getUserDiaries($viewer_id);
        $diaryDatasCount = Count($diaryDatas);
        $event_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('event_id', null);
        $event = Engine_Api::_()->getItem('siteevent_event', $event_id);

        if ($diaryDatasCount >= 1) {
            $this->setDescription("Please select the diaries in which you want to add this event.");
        }

        $diaryIdsDatas = Engine_Api::_()->getDbtable('diarymaps', 'siteevent')->pageDiaries($event_id, $viewer_id);

        if (!empty($diaryIdsDatas)) {
            $diaryIdsDatas = $diaryIdsDatas->toArray();
            $diaryIds = array();
            foreach ($diaryIdsDatas as $diaryIdsData) {
                $diaryIds[] = $diaryIdsData['diary_id'];
            }
        }

        foreach ($diaryDatas as $diaryData) {
            if (in_array($diaryData->diary_id, $diaryIds)) {
                $this->addElement('Checkbox', 'inDiary_' . $diaryData->diary_id, array(
                    'label' => $diaryData->title,
                    'value' => 1,
                ));
            } else {
                $this->addElement('Checkbox', 'diary_' . $diaryData->diary_id, array(
                    'label' => $diaryData->title,
                    'value' => 0,
                ));
            }
        }

        if ($diaryDatasCount >= 1) {
            $this->addElement('dummy', 'dummy_text', array('label' => "You can also add this event in a new diary below:"));
        } else {
            $this->addElement('dummy', 'dummy_text', array('label' => "You have not created any diary yet. Get started by creating a diary and adding events to it."));
        }

        if ($diaryDatasCount) {
            $this->addElement('Text', 'title', array(
                'label' => 'Diary Name',
                'maxlength' => '63',
                'filters' => array(
                    new Engine_Filter_Censor(),
                    new Engine_Filter_StringLength(array('max' => '63')),
                )
            ));
        } else {
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
        }

        $this->addElement('Textarea', 'body', array(
            'label' => 'Description',
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
            'label' => 'Save',
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