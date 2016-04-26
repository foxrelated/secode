<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Ynaffiliate_Form_Admin_Exchangerates extends Engine_Form {

   public function init() {
      $table = Engine_Api::_()->getDbTable('exchangerates', 'ynaffiliate');
      $select = $table->select();
      $data = $table->fetchAll($select);
      $currencyOptions = array();
      foreach ($data as $curr) {
         $currencyOptions[$curr->exchangerate_id] = $curr->exchangerate_id;
      }


      // Init form
      $this
              ->setTitle('Exchange rates')
              // ->setDescription('Enter your affiliate information ')
              ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
      ;

      $this->addElement('select', 'exchangerate_id', array(
          'label' => 'Currency:',
          'onchange' => 'javascript:fetchExchangerate(this.value);',
          'multiOptions' => $currencyOptions
      ));

      $this->addElement('Text', 'exchange_rate', array(
          'label' => 'Exchange Rate',
          'allowEmpty' => false,
          'description' => '',
          'required' => true,
          'validators' => array(
              array('NotEmpty', true),
              array('Float', true),
          ),
          'filters' => array(
              new Engine_Filter_Censor(),
          ),
          'value' => '',
      ));

      $this->addElement('Button', 'submit', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true
      ));
   }

}

?>