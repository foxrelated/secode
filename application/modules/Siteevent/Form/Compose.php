<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Compose.php 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_Compose extends Engine_Form {

    protected $_occurrenceid;

    public function setOccurrenceid($value) {
        $this->_occurrenceid = $value;
    }

    public function getOccurrenceid() {
        return $this->_occurrenceid;
    }

    public function init() {

        $this->setTitle('Compose Message');
        $this->setDescription('Create your new message with the form below. Your message can be addressed to up to 10 recipients.')
                ->setAttrib('id', 'messages_compose');

        $datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium');

        $user_level = Engine_Api::_()->user()->getViewer()->level_id;

        // init to
        $this->addElement('Text', 'to', array(
            'label' => 'Send To',
            'autocomplete' => 'off'));
        Engine_Form::addDefaultDecorators($this->to);

        // Init to Values
        $this->addElement('Hidden', 'toValues', array(
            'label' => 'Send To',
            'required' => true,
            'allowEmpty' => false,
            'order' => 2,
            'validators' => array(
                'NotEmpty'
            ),
            'filters' => array(
                'HtmlEntities'
            ),
        ));
        Engine_Form::addDefaultDecorators($this->toValues);

        //IF OCCURRENCE ID IS COMING THEN SHOW THE EVENT OCCURRENCE DROP DOWN.
        $occurrence_id = $this->getOccurrenceid();

        $event_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('event_id', null);
        $datesInfo = array();
        if (!empty($event_id))
            $datesInfo = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getAllOccurrenceDates($event_id);

        if (count($datesInfo) > 1) {

            $filter_dates = Engine_Api::_()->siteevent()->getAllOccurrenceDate($datesInfo);
            $this->addElement('Select', 'filter_occurrence_date', array(
                'label' => 'Select Event Occurrence Dates',
                'multiOptions' => $filter_dates,
                'value' => $occurrence_id,
                'onchange' => 'en4.siteevent.member.setOccurrenceMsgGuest(this.value);'
            ));
        }

        // Element : restriction
        if (Engine_Api::_()->siteevent()->isTicketBasedEvent()) {
            $this->addElement('Select', 'guests', array(
                'label' => 'Select Member',
                'multiOptions' => array(
                    '3' => 'All Members',
                    '4' => 'Particular Members',
                ),
                'value' => 3,
                'onclick' => 'selectGuestsType(this.value)',
                'onchange' => 'checkGuests()',
            ));
        }
        else {
            $this->addElement('Select', 'guests', array(
                'label' => 'Select Guests',
                'multiOptions' => array(
                    '3' => 'All Guests',
                    '2' => 'Attending',
                    '1' => 'Maybe Attending',
                    '0' => 'Not Attending',
                    '4' => 'Particular Guests',
                ),
                'value' => 3,
                'onclick' => 'selectGuestsType(this.value)',
                'onchange' => 'checkGuests()',
            ));            
        }

        // init to
        $description = 'Start typing the name of the guest...';
        $title = 'Guests to Message';
        if (Engine_Api::_()->siteevent()->isTicketBasedEvent()) {
            $description = 'Start typing the name of the member...';
            $title = 'Members to Message';
        }
        $this->addElement('Text', 'searchGuests', array(
            'label' => $title,
            'description' => $description,
            'autocomplete' => 'off'));
        Engine_Form::addDefaultDecorators($this->searchGuests);

        // Init to Values
        $this->addElement('Hidden', 'toValues', array(
            'label' => '',
            'order' => '5',
            'filters' => array(
                'HtmlEntities'
            ),
        ));
        Engine_Form::addDefaultDecorators($this->toValues);

        // init title
        $this->addElement('Text', 'title', array(
            'label' => 'Subject',
            'order' => 6,
            'filters' => array(
                new Engine_Filter_Censor(),
                new Engine_Filter_HtmlSpecialChars(),
            ),
        ));

        // init body - editor
        $editor = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('messages', $user_level, 'editor');

        if ($editor == 'editor') {
            $this->addElement('TinyMce', 'body', array(
                'disableLoadDefaultDecorators' => true,
                'order' => 7,
                'required' => true,
                'editorOptions' => array(
                    'bbcode' => true,
                    'html' => true,
                ),
                'allowEmpty' => false,
                'decorators' => array(
                    'ViewHelper',
                    'Label',
                    array('HtmlTag', array('style' => 'display: block;'))),
                'filters' => array(
                    new Engine_Filter_HtmlSpecialChars(),
                    new Engine_Filter_Censor(),
                    new Engine_Filter_EnableLinks(),
                ),
            ));
        } else {
            // init body - plain text
            $this->addElement('Textarea', 'body', array(
                'label' => 'Message',
                'order' => 7,
                'required' => true,
                'allowEmpty' => false,
                'filters' => array(
                    new Engine_Filter_HtmlSpecialChars(),
                    new Engine_Filter_Censor(),
                    new Engine_Filter_EnableLinks(),
                ),
            ));
        }
        // init submit
        $this->addElement('Button', 'submit', array(
            'label' => 'Send Message',
            'order' => 8,
            'type' => 'submit',
            'ignore' => true
        ));
    }

}