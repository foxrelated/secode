<?php

class Ynaffiliate_Form_Admin_Commission_Rule extends Engine_Form {

    public function init() {

        $this->setTitle('Commission Rule');

        $MAX_COMMISSION_LEVEL = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.max.commission.level', 5);

        for ($i = 1; $i <= $MAX_COMMISSION_LEVEL; $i++) {

            $labelText = Zend_Registry::get('Zend_View')->translate('Level %s (%%)', $i);
            $this->addElement('Text', "level_$i", array(
                'label' => $labelText,
                'allowEmpty' => false,
                'required' => true,
                'validators' => array(
                    array('NotEmpty', true),
                    array('Float', true),
                    // array('Between', true, array($min_payout, $maxvalue, true)),
                ),
                'filters' => array(
                    new Engine_Filter_Censor(),
                ),
                'value' => '',
            ));
        }

        $this->addElement('Button', 'submit', array(
            'label' => 'Submit',
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