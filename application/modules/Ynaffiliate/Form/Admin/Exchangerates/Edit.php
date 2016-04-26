<?php

class Ynaffiliate_Form_Admin_Exchangerates_Edit extends Engine_Form {
   public function init() {
      $this->addElement('Text', 'exchange_rate', array(
          'label' => '',
          'allowEmpty' => false,
          'required' => true,
          'validators' => array(
              array('NotEmpty', true),
              array('Float', true),
              array('GreaterThan', false, array(0)),
          ),
          'filters' => array(
              new Engine_Filter_Censor(),
          ),
      ));

       $this -> exchange_rate -> getDecorator("Description")->setOption("placement", "append");

      $this->addElement('Button', 'submit', array(
          'label' => 'Submit',
          'type' => 'submit',
          'ignore' => true,
          'decorators' => array(
              'ViewHelper',
          ),
      ));

      $this->addElement('Cancel', 'cancel', array(
          'label' => 'cancel',
          'link' => true,
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