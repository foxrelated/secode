<?php

class Ynaffiliate_Form_Admin_Commission_Deny extends Engine_Form {

    public function init() {

        $this->setTitle('Deny The Commission');

        $this -> addElement('Textarea', 'reason', array(
            'label' => 'Reason to deny',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor()
            ),
        ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Deny',
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