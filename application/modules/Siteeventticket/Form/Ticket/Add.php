<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Add.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Form_Ticket_Add extends Engine_Form {

    protected $_seaoSmoothbox;
    protected $_event_id;

    public function setSeaoSmoothbox($flage) {
        $this->_seaoSmoothbox = $flage;
        return $this;
    }

    public function setEvent_id($flage) {
        $this->_event_id = $flage;
    }

    public function getSeaoSmoothbox() {
        return $this->_seaoSmoothbox;
    }

    public function init() {
        $user = Engine_Api::_()->user()->getViewer();
        $this->setTitle('Ticket Details');
        $this
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                ->setAttrib('name', 'siteeventticket_create')
                ->setAttrib('id', 'siteeventticket_add_quick')
                ->setDescription('');

        $settings = Engine_Api::_()->getApi('settings', 'core');

        $this->addElement('Text', 'title', array(
            'label' => 'Title',
            'maxlength' => '63',
            'required' => true,
            'allowEmpty' => false,
            'filters' => array(
                new Engine_Filter_Censor(),
            )
        ));

        $this->addElement('Textarea', 'description', array(
            'label' => 'Description',
            'maxlength' => '512',
            'attribs' => array('rows' => 2, 'cols' => 180, 'style' => 'width:300px; max-width:400px;min-height:20px;'),
            'filters' => array(
                'StripTags',
                new Engine_Filter_EnableLinks(),
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '512')),
            )
        ));

        // Element: price
        $localeObject = Zend_Registry::get('Locale');
        $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);
        $this->addElement('Text', 'price', array(
            'label' => sprintf(Zend_Registry::get('Zend_Translate')->_('Price (%s)'), $currencyName),
            'description' => 'This price will be charged from those buying this ticket. Enter 0 to make it a free ticket.',
            'attribs' => array('class' => 'se_quick_advanced'),
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('Float', true),
                new Engine_Validate_AtLeast(0),
            ),
            'value' => '0.00',
        ));

        $this->addElement('Dummy', 'quantityHeading', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_formElementsHeading.tpl',
                        'heading' => Zend_Registry::get('Zend_Translate')->_("Tickets available & buying limits"),
                        'class' => 'form element'
                    ))),
        ));

        $this->addElement('Text', 'quantity', array(
            'label' => 'Total Tickets Available',
            'required' => true,
            'allowEmpty' => false,
            'value' => 100,
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
        ));

        $this->addElement('Text', 'buy_limit_min', array(
            'label' => 'Minimum Buying Limit',
            'maxlength' => '4',
            'required' => true,
            'allowEmpty' => false,
            'value' => 0,
            'validators' => array(
                array('Int', true),
            ),
        ));

        $this->addElement('Text', 'buy_limit_max', array(
            'label' => 'Maximum Buying Limit',
            'maxlength' => '4',
            'required' => true,
            'allowEmpty' => false,
            'value' => $settings->getSetting('siteeventticket.buylimitmax', 10),
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
        ));

        $this->addElement('Radio', 'is_claimed_display', array(
            'label' => "Display Tickets Sold",
            'description' => "Do you want to display the number of tickets sold / claimed? if selected yes, it will be displayed as: 15 of 100 tickets sold.",
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => '1'
        ));

        $this->addElement('Dummy', 'timeHeading', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_formElementsHeading.tpl',
                        'heading' => Zend_Registry::get('Zend_Translate')->_('Tickets availability duration'),
                        'class' => 'form element'
                    ))),
        ));

        //put the start and end date here.
        // Start time
        $start = new Engine_Form_Element_CalendarDateTime('sell_starttime');
        $start->setLabel("Start Date");
        if ($this->getSeaoSmoothbox()) {
            $start->setAttrib('loadedbyAjax', 'TRUE');
        }
        $start->setAllowEmpty(false);
        $this->addElement($start);
        if (!$this->_item) {
            $starttime = time();
            $oldTz = date_default_timezone_get();
            date_default_timezone_set($user->timezone);
            $start->setValue(date("Y-m-d H:i:s", ($starttime)));
            date_default_timezone_set($oldTz);
        }
        // End time

        $this->addElement('Radio', 'is_same_end_date', array(
            'label' => "End Date",
//     'description' => "Tickets will be available to buy depending upon the option you have selected below.",
            'multiOptions' => array(
                '1' => 'Just before the event start time',
                '0' => 'Set your custom time'
            ),
            'value' => '1',
            'onclick' => 'javascript:showCustomEndTimeOption(this.value)',
        ));

        //GET EVENT'S LAST OCCURRENCE START TIME & SET AS SELL ENDTIME 
        $lastOccurrenceEndtime = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($this->_event_id, 'DESC');
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $this->_event_id);
        if (Engine_Api::_()->hasModuleBootstrap('siteeventrepeat') && !empty($siteevent->repeat_params)) {
            $eventparams = json_decode($siteevent->repeat_params);
            if (!empty($eventparams) && isset($eventparams->endtime) && !empty($eventparams->endtime->date)) {
                $event_endtime = date('Y-m-d H:i:s', strtotime($eventparams->endtime->date));
                $lastOccurrenceEndtime = (strtotime($lastOccurrenceEndtime) > strtotime($event_endtime)) ? $lastOccurrenceEndtime : $event_endtime;
            }
        }        

        $end = new Engine_Form_Element_CalendarDateTime('sell_endtime');
        $end->setAllowEmpty(false);
        if ($this->getSeaoSmoothbox()) {
            $end->setAttrib('loadedbyAjax', 'TRUE');
        }

        $this->addElement($end);
        if (!$this->_item) {
            $oldTz = date_default_timezone_get();
            date_default_timezone_set($user->timezone);
            $end->setValue($lastOccurrenceEndtime);
            date_default_timezone_set($oldTz);
        }

        //HIDDEN - CURRENT DATE FIELD
        $this->addElement('Hidden', 'current_date', array(
            'order' => 100
        ));

        if (!$this->_item) {
            $oldTz = date_default_timezone_get();
            date_default_timezone_set($user->timezone);
            $this->current_date->setValue(date("m/d/Y", ($starttime + 3600)));
            date_default_timezone_set($oldTz);
        }

        //HIDDEN - EVENT END TIME
        $this->addElement('Hidden', 'event_endtime', array(
            'order' => 104
        ));


        if (!$this->_item) {
            $oldTz = date_default_timezone_get();
            date_default_timezone_set($user->timezone);
            $this->event_endtime->setValue(date("m/d/Y", strtotime($lastOccurrenceEndtime)));
            date_default_timezone_set($oldTz);
        }

        $this->addDisplayGroup(array('is_same_end_date', 'sell_endtime'), 'endtime_field');
        $this->getDisplayGroup('endtime_field');

        $userTimezone = array(
            'US/Pacific' => '(UTC-8) Pacific Time (US & Canada)',
            'US/Mountain' => '(UTC-7) Mountain Time (US & Canada)',
            'US/Central' => '(UTC-6) Central Time (US & Canada)',
            'US/Eastern' => '(UTC-5) Eastern Time (US & Canada)',
            'America/Halifax' => '(UTC-4)  Atlantic Time (Canada)',
            'America/Anchorage' => '(UTC-9)  Alaska (US & Canada)',
            'Pacific/Honolulu' => '(UTC-10) Hawaii (US)',
            'Pacific/Samoa' => '(UTC-11) Midway Island, Samoa',
            'Etc/GMT-12' => '(UTC-12) Eniwetok, Kwajalein',
            'Canada/Newfoundland' => '(UTC-3:30) Canada/Newfoundland',
            'America/Buenos_Aires' => '(UTC-3) Brasilia, Buenos Aires, Georgetown',
            'Atlantic/South_Georgia' => '(UTC-2) Mid-Atlantic',
            'Atlantic/Azores' => '(UTC-1) Azores, Cape Verde Is.',
            'Europe/London' => 'Greenwich Mean Time (Lisbon, London)',
            'Europe/Berlin' => '(UTC+1) Amsterdam, Berlin, Paris, Rome, Madrid',
            'Europe/Athens' => '(UTC+2) Athens, Helsinki, Istanbul, Cairo, E. Europe',
            'Europe/Moscow' => '(UTC+3) Baghdad, Kuwait, Nairobi, Moscow',
            'Iran' => '(UTC+3:30) Tehran',
            'Asia/Dubai' => '(UTC+4) Abu Dhabi, Kazan, Muscat',
            'Asia/Kabul' => '(UTC+4:30) Kabul',
            'Asia/Yekaterinburg' => '(UTC+5) Islamabad, Karachi, Tashkent',
            'Asia/Calcutta' => '(UTC+5:30) Bombay, Calcutta, New Delhi',
            'Asia/Katmandu' => '(UTC+5:45) Nepal',
            'Asia/Omsk' => '(UTC+6) Almaty, Dhaka',
            'India/Cocos' => '(UTC+6:30) Cocos Islands, Yangon',
            'Asia/Krasnoyarsk' => '(UTC+7) Bangkok, Jakarta, Hanoi',
            'Asia/Hong_Kong' => '(UTC+8) Beijing, Hong Kong, Singapore, Taipei',
            'Asia/Tokyo' => '(UTC+9) Tokyo, Osaka, Sapporto, Seoul, Yakutsk',
            'Australia/Adelaide' => '(UTC+9:30) Adelaide, Darwin',
            'Australia/Sydney' => '(UTC+10) Brisbane, Melbourne, Sydney, Guam',
            'Asia/Magadan' => '(UTC+11) Magadan, Soloman Is., New Caledonia',
            'Pacific/Auckland' => '(UTC+12) Fiji, Kamchatka, Marshall Is., Wellington',
        );
        $timezone = Engine_Api::_()->getApi('settings', 'core')->core_locale_timezone;
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($viewer->getIdentity()) {
            $timezone = $viewer->timezone;
        }

        $this->addElement('Dummy', 'showtimezone', array(
            'label' => '',
            'content' => $userTimezone[$timezone]
        ));

        $this->addElement('Select', 'status', array(
            'label' => 'Status',
            'value' => 'open',
            'multiOptions' => array(
                'open' => 'Open',
                'hidden' => 'Hidden',
                'closed' => 'Closed'
            )
        ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Add',
            'ignore' => true,
            'decorators' => array('ViewHelper'),
            'type' => 'submit'
        ));

        $this->addElement('Cancel', 'cancel', array(
            'prependText' => ' or ',
            'label' => 'cancel',
            'link' => true,
            'onclick' => $this->getSeaoSmoothbox() ? 'SmoothboxSEAO.close()' : '',
            'href' => $this->getSeaoSmoothbox() ? "javascript:void(0)" : Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'ticket', 'action' => 'manage', 'event_id' => $this->_event_id), "siteeventticket_ticket", true),
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
