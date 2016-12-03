<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Form_Coupon_Create extends Engine_Form {

    public function init() {

        $user = Engine_Api::_()->user()->getViewer();

        $coupon_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('coupon_id', null);
        $event_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('event_id', null);
        if (!empty($coupon_id)) {
            $siteeventticketcoupon = Engine_Api::_()->getItem('siteeventticket_coupon', $coupon_id);
            $event_id = $siteeventticketcoupon->event_id;
        }

        $this->setTitle('Add a New Coupon')
                ->setAttrib('name', 'siteeventticket_add_coupon')
                ->setAttrib('id', 'siteeventticket_create_coupon')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                ->setDescription(Zend_Registry::get('Zend_Translate')->_("Enter your coupon's details below and click 'Add' to create your coupon."));

        $this->addElement('text', 'title', array(
            'label' => 'Coupon Title',
            'allowEmpty' => false,
            'required' => true,
            'maxLength' => 128,
            'filters' => array(
                new Engine_Filter_Html(),
                new Engine_Filter_Censor(),
            ),
        ));

        $this->addElement('textarea', 'description', array(
            'label' => 'Description',
            'description' => 'To make this coupon more relevant for users and to mention the terms and conditions of its use, enter its description below.',
            'filters' => array(
                'StripTags',
                new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_EnableLinks(),
                new Engine_Filter_Censor(),
            ),
        ));

        $this->addElement('Text', 'coupon_code', array(
            'label' => 'Coupon Code',
            'validators' => array(
                array('Alnum', true, array('allowWhiteSpace' => false)),
            ),
            'allowedEmpty' => false
        ));
        
        $ticketsTable = Engine_Api::_()->getDbtable('tickets', 'siteeventticket');
        $selectTickets = $ticketsTable->getTicketsSelect(array('event_id' => $event_id));
        $tickets = $ticketsTable->fetchAll($selectTickets);
        $ticketsArray = array();
        $disableTickets = array();
        foreach ($tickets as $ticket) {
            $ticketsArray[$ticket->ticket_id] = $ticket->title;
            if ($ticket->price <= 0) {
                $disableTickets[] = $ticket->ticket_id;
            }
        }

        if (Count($ticketsArray) > 0) {
            $this->addElement('MultiCheckbox', 'ticket_ids', array(
                'label' => "Select Tickets",
                'description' => "Select the type of tickets for which this coupon should be applicable.",
                'multiOptions' => $ticketsArray,
                'value' => array_keys($ticketsArray),
                'attribs' => array('disable' => $disableTickets)
            ));
        }        

        $this->addElement('Select', 'discount_type', array(
            'label' => 'Discount Type',
            'multiOptions' => array(
                1 => 'Fixed',
                0 => 'Percentage'
            ),
            'value' => 0,
        ));

        $this->addElement('Text', 'rate', array(
            'label' => 'Discount Rate (%)',
            'allowEmpty' => false,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
        ));

        $localeObject = Zend_Registry::get('Locale');
        $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);
        $this->addElement('Text', 'price', array(
            'label' => sprintf(Zend_Registry::get('Zend_Translate')->_('Discount (%s)'), $currencyName),
            'allowEmpty' => false,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
        ));

        $this->addElement('Text', 'min_amount', array(
            'label' => 'Minimum Purchase Amount',
            'description' => "This coupon will only be applicable if total amount of order will be equal to or more than the entered amount. Leave this empty or set to zero to apply coupon on any amount.",
            'validators' => array(
                array('Float', true),
            ),
        ));

        $this->addElement('Text', 'min_quantity', array(
            'label' => 'Minimum Purchase Quantity',
            'description' => "Minimum number of tickets that should be added to order, to avail this coupon. Leave this empty or set to zero to apply coupon on any numbers of tickets.",
            'validators' => array(
                array('Int', true),
            ),
        ));

        $this->addElement('File', 'photo', array(
            'label' => 'Coupon Picture',
            'description' => "<span id='loading_image' style='display:none;'></span> ",
            'onchange' => 'imageupload()',
        ));
        $this->photo->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

        $this->photo->addValidator('Extension', false, 'jpg,png,gif');

        $start = new Engine_Form_Element_CalendarDateTime('start_time');
        $start->setLabel("Start Date");
        $start->setAttrib('loadedbyAjax', 'TRUE');
        $start->setAllowEmpty(false);
        $this->addElement($start);
        if (!$this->_item) {
            $starttime = time();
            $oldTz = date_default_timezone_get();
            date_default_timezone_set($user->timezone);
            $start->setValue(date("Y-m-d H:i:s", ($starttime + 3600)));
            date_default_timezone_set($oldTz);
        }

        $this->addElement('Radio', 'end_settings', array(
            'id' => 'end_settings',
            'label' => 'End Date',
            'description' => 'When will this coupon end?',
            'onclick' => "updateTextFields(this.value)",
            'multiOptions' => array(
                "0" => "Never. This coupon does not have an end date.",
                "1" => "This coupon ends on a specific date. (Select the date by clicking on the calendar icon below.)",
            ),
            'value' => 0
        ));

        $end = new Engine_Form_Element_CalendarDateTime('end_time');
        $end->setAllowEmpty(false);
        $end->setAttrib('loadedbyAjax', 'TRUE');
        $this->addElement($end);
        if (!$this->_item) {
            $endtime = (time() + 4 * 3600);
            $oldTz = date_default_timezone_get();
            date_default_timezone_set($user->timezone);
            $end->setValue(date("Y-m-d H:i:s", $endtime));
            date_default_timezone_set($oldTz);
        }

        $this->addElement('Checkbox', 'status', array(
            'label' => "Enable this coupon",
            'value' => 1
        ));

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.couponprivate', 0)) {
            $this->addElement('Checkbox', 'public', array(
                'label' => 'Make this coupon public so that others can see it on my event.',
                'value' => 1,
            ));
        }

        $this->addElement('Button', 'submit', array(
            'label' => 'Add',
            'ignore' => true,
            'decorators' => array('ViewHelper'),
            'type' => 'submit'
        ));

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => $view->url(array('action' => 'manage', 'event_id' => $event_id), 'siteeventticket_coupon', 'true'),
            'decorators' => array('ViewHelper')
        ));

        $this->addDisplayGroup(array(
            'submit',
            'cancel',
                ), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper'
            ),
        ));
    }

}
