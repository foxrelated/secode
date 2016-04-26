<?php

class Ynaffiliate_Form_Admin_Commission_Reject extends Engine_Form {

    public function init() {

        $this->setTitle('Reject The Commission');

        $this -> addElement('Textarea', 'reason', array(
            'label' => 'Reason to reject',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor()
            ),
        ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Reject',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));
//    
        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'onClick' => 'javascript:parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper',
            ),
        ));
        // DisplayGroup: buttons
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

    // }
}