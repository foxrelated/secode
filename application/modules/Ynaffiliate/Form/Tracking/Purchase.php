<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Ynaffiliate_Form_Tracking_Purchase extends Engine_Form {

    public function init() {

        $this->setAttribs(array(
            'id' => 'filter_form',
            'class' => "global_form f1",
            'method'=>'GET',
            'onSubmit' => "getdate()",
        ));

        $this->addElement('Text', 'client_name', array(
            'label' => 'Client Name',
            'maxlength' => '60',
        ));

        $temp = '<input id="sday" name="date_toggled" type="text" value="" class="date date_toggled text_calendar" />
        <img src="./application/modules/Ynaffiliate/externals/images/calendar-blue.png" class="date_toggler img_calendar" />';

        $this->addElement('Dummy', 'date1', array(
            'label' => 'From Date',
            'content' => $temp
        ));


        $temp = '<input id="eday" name="date_toggled" type="text" value="" class="date date_toggled text_calendar" />
        <img src="./application/modules/Ynaffiliate/externals/images/calendar-blue.png" class="date_toggler img_calendar"/>';

        $this->addElement('Dummy', 'date2', array(
            'label' => 'To Date',
            'content' => $temp
        ));

        $date_validate = new Zend_Validate_Date("YYYY-MM-dd");
        $date_validate->setMessage("Please pick a valid day (yyyy-mm-dd)", Zend_Validate_Date::FALSEFORMAT);

        //hidden element for From Date
        $hidden = new Zend_Form_Element_Hidden('From_Date');
        $hidden->clearDecorators();
        $hidden->addDecorators(array(
            array('ViewHelper'),
        ));
        $hidden->addValidator($date_validate);
        $hidden->setLabel('From Date');
        $this->addElement($hidden);

        //hidden element for To Date

        $hidden = new Zend_Form_Element_Hidden('To_Date');
        $hidden->clearDecorators();
        $hidden->addDecorators(array(
            array('ViewHelper'),
        ));
        $hidden->addValidator($date_validate);
        $hidden->setLabel('To Date');
        $this->addElement($hidden);
        $Rules = new Ynaffiliate_Model_DbTable_Rules;
        $rules = $Rules->fetchAll();
        $rulesMultiOptions = array();
        $rulesMultiOptions[] = 'All';
        foreach( $rules as $rule ) {
            $rulesMultiOptions[$rule->rule_name] = $rule->rule_title;
        }
        $this->addElement('select', 'type', array(
            'label' => 'Purchased Type',
            //  'value' => 'Product',
            'multiOptions' => $rulesMultiOptions,
        ));

        // Add approve status
        $approveStatus = array(
            '0' => 'All',
            'waiting' => 'Waiting',
            'delaying' => 'Delaying',
            'approved' => 'Approved',
            'denied' => 'Denied',
        );
        $this->addElement('select', 'approve_stat', array(
            'label' => 'Status',
            'multiOptions' => $approveStatus,
        ));

        $this->addElement('Hidden', 'order', array(
            'order' => 10004,
        ));

        // Element: direction
        $this->addElement('Hidden', 'direction', array(
            'order' => 10005,
        ));
        $this->addElement('Button', 'submit', array(
            'label' => 'Search',
            'type' => 'submit',
            'ignore' => true,
            //'onclick' =>'validate()',
            // 'decorators' => array('ViewHelper')
        ));
    }
}

?>
