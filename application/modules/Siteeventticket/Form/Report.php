<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Report.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Form_Report extends Engine_Form {

    protected $_event_id;
    protected $_event_name;

    public function setEventId($id) {
        $this->_event_id = $id;
        return $this;
    }

    public function setEventName($name) {
        $this->_event_name = $name;
        return $this;
    }

    public function init() {
        $this
                ->setAttrib('id', 'event_report_form')
                ->setAttrib('name', 'event_report_form')
                ->setTitle("Sales Reports")
                ->setDescription("Here, you may view performance reports for sales of your event. You can also view the sales performance of your other events. Reports can be viewed over multiple durations and time intervals. Reports can also be viewed for any desired order status. You can also export and save the report.");
        $this->setMethod('get');

        $this->addElement('Hidden', 'event_id', array(
            'value' => $this->_event_id
        ));

        // SELECT EVENT
        $this->addElement('Select', 'select_event', array(
            'label' => 'Select Events',
            'multiOptions' => array(
                'current_event' => $this->_event_name,
                'specific_event' => 'My Selected Events',
                'all' => 'All my events',
            ),
            'value' => 'current_event',
            'onchange' => 'return oneventChange($(this))',
        ));

        $this->addElement('Text', 'event_name', array(
            'label' => 'Events',
            'description' => 'Start typing the name of events.',
            'autocomplete' => 'off'));
        Engine_Form::addDefaultDecorators($this->event_name);
        $this->event_name->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

        $this->addElement('Hidden', 'event_ids', array(
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
        Engine_Form::addDefaultDecorators($this->event_ids);


        // SELECT REPORT TYPE
        $this->addElement('Select', 'report_depend', array(
            'label' => 'Report Summarized By',
            'multiOptions' => array(
                'order' => 'Orders',
                'ticket' => 'Tickets',
            ),
            'value' => 'order',
            'onchange' => 'return onreportDependChange($(this))',
        ));

        // SELECT TICKET
        $this->addElement('Select', 'select_ticket', array(
            'label' => 'Select Tickets',
            'multiOptions' => array(
                'all' => 'All',
                'specific_ticket' => 'Specific Tickets',
            ),
            'value' => 'all',
            'onchange' => 'return onticketChange($(this))',
        ));

        $this->addElement('Text', 'ticket_name', array(
            'label' => 'Tickets',
            'description' => 'Start typing the name of the tickets.',
            'autocomplete' => 'off'));
        Engine_Form::addDefaultDecorators($this->ticket_name);
        $this->ticket_name->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

        $this->addElement('Hidden', 'ticket_ids', array(
            'required' => true,
            'allowEmpty' => false,
            'order' => 6,
            'validators' => array(
                'NotEmpty'
            ),
            'filters' => array(
                'HtmlEntities'
            ),
        ));
        Engine_Form::addDefaultDecorators($this->ticket_ids);

        // Order Status
        $this->addElement('Select', 'order_status', array(
            'label' => 'Ticket Order Status',
            'multiOptions' => array(
                'all' => 'All',
                '0' => 'Approval Pending',
                '1' => 'Payment Pending',
                '2' => 'Completed',
            ),
            'value' => 'all',
        ));

        // Init chunk
        $this->addElement('Select', 'time_summary', array(
            'label' => 'Time Summary',
            'multiOptions' => array(
                'Monthly' => 'Monthly',
                // 'Weekly' => 'Weekly',
                'Daily' => 'Daily',
            ),
            'onchange' => 'return onChangeTime($(this))',
            'value' => 'Daily',
        ));

        $this->addElement('Select', 'month_start', array(
            'label' => '',
            'multiOptions' => array(
                '01' => 'January',
                '02' => 'February',
                '03' => 'March',
                '04' => 'April',
                '05' => 'May',
                '06' => 'June',
                '07' => 'July',
                '08' => 'August',
                '09' => 'September',
                '10' => 'October',
                '11' => 'November',
                '12' => 'December',
            ),
            'value' => '01',
            'decorators' => array(
                'ViewHelper'),
        ));

        $this->addElement('Select', 'year_start', array(
            'multiOptions' => array(
            ),
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addDisplayGroup(array('month_start', 'year_start'), 'start_group');
        $button_group = $this->getDisplayGroup('start_group');
        $button_group->setDescription('From');
        $button_group->setDecorators(array(
            'FormElements',
            array('Description', array('placement' => 'PREPEND', 'tag' => 'div', 'class' => 'form-label')),
            array('HtmlTag', array('tag' => 'div', 'class' => 'form-wrapper', 'id' => 'start_group', 'style' => 'display:none;'))
        ));

        $this->addElement('Select', 'month_end', array(
            'multiOptions' => array(
                '01' => 'January',
                '02' => 'February',
                '03' => 'March',
                '04' => 'April',
                '05' => 'May',
                '06' => 'June',
                '07' => 'July',
                '08' => 'August',
                '09' => 'September',
                '10' => 'October',
                '11' => 'November',
                '12' => 'December',
            ),
            'value' => @date(m),
            'decorators' => array(
                'ViewHelper'),
        ));

        $this->addElement('Select', 'year_end', array(
            'multiOptions' => array(
            ),
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addDisplayGroup(array('month_end', 'year_end'), 'end_group');
        $button_group = $this->getDisplayGroup('end_group');

        $button_group->setDescription(' To ');
        $button_group->setDecorators(array(
            'FormElements',
            array('Description', array('placement' => 'PREPEND', 'tag' => 'div', 'class' => 'form-label')),
            array('HtmlTag', array('tag' => 'div', 'class' => 'form-wrapper', 'id' => 'end_group', 'style' => 'display:none;'))
        ));

        $start_cal = new Engine_Form_Element_CalendarDateTime('start_cal');
        $start_cal->setLabel("From");
        $start_cal->setValue(date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'))));

        $this->addElement($start_cal);

        $end_cal = new Engine_Form_Element_CalendarDateTime('end_cal');
        $end_cal->setLabel("To");
        $end_cal->setValue(date('Y-m-d H:i:s'));

        $this->addElement($end_cal);

        $this->addDisplayGroup(array('start_cal', 'end_cal'), 'grp2');
        $button_group = $this->getDisplayGroup('grp2');
        $button_group->setDecorators(array(
            'FormElements',
            'Fieldset',
            array('HtmlTag', array('tag' => 'div', 'id' => 'time_group2', 'style' => 'width:100%;'))
        ));

        $this->addElement('Select', 'format_report', array(
            'label' => 'Format',
            'multiOptions' => array(
                '0' => "Webpage (.html)",
                '1' => "Excel (.xls)",
            ),
            'value' => '0',
            'onchange' => 'return onchangeFormat($(this))',
        ));

        // Init submit
        $this->addElement('Button', 'generate_report', array(
            'label' => 'Generate Report',
            'type' => 'submit',
        ));
    }

}
