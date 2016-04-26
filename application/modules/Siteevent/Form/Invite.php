<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Invite.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_Invite extends Engine_Form {

    protected $_isLeader;

    public function getLeader() {
        return $this->_isLeader;
    }

    public function setIsLeader($isLeader) {
        $this->_isLeader = $isLeader;
        return $this;
    }

    public function init() {

        //GET EITHER VIEWER IS LEADER OR NOT
        $isLeader = $this->getLeader();
        if ($isLeader) {
            $this->setTitle(Zend_Registry::get('Zend_Translate')->_('Invite Members to join this Event'));
            //$this->setDescription(Zend_Registry::get('Zend_Translate')->_('Select the members you want to invite to join this event.'))
                    $this->setAttrib('id', 'event_form_invite');
            $Button = 'Add Members';
        } else {
            $this
                    ->setTitle('Invite Members')
                    ->setDescription('Choose the members you want to invite to this event.')
                    ->setAttrib('id', 'event_form_invite')
            ;
            $Button = 'Invite Members';
        }


        if ($isLeader) {
            // init to
            $this->addElement('Text', 'user_ids', array(
                'label' => 'Start typing the names of the members that you want to invite.',
                'autocomplete' => 'off'
            ));
            Engine_Form::addDefaultDecorators($this->user_ids);

            // Init to Values
            $this->addElement('Hidden', 'toValues', array(
                'label' => '',
                'order' => '5',
                'filters' => array(
                    'HtmlEntities'
                ),
            ));
            Engine_Form::addDefaultDecorators($this->toValues);
        } else {
            $this->addElement('Checkbox', 'all', array(
                'id' => 'selectall',
                'label' => 'Choose All Friends',
                'ignore' => true
            ));

            $this->addElement('MultiCheckbox', 'users', array(
                'label' => 'Members',
                'required' => true,
                'allowEmpty' => 'false',
            ));
        }
        $this->addElement('Button', 'submit', array(
            'label' => 'Send Invites',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'onclick' => 'parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    }

}
