<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Ynaffiliate_Form_Tracking_Click extends Engine_Form {

   public function init() {

      $this->setAttribs(array(
           'id' => 'filter_form',
          'class' => "global_form f1",
          'onSubmit' => "getdate()",
      ));


      $temp = '<input id="sday" name="date_toggled" type="text" value="" class="date date_toggled text_calendar" />
      <img src="./application/modules/Ynaffiliate/externals/images/calendar-blue.png" class="date_toggler img_calendar"/>';

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
      $hidden->setRequired(false);
      $hidden->addValidator($date_validate);
      $hidden->setLabel('From Date');
      $this->addElement($hidden);

      //hidden element for To Date

      $hidden = new Zend_Form_Element_Hidden('To_Date');
      $hidden->clearDecorators();
      $hidden->addDecorators(array(
          array('ViewHelper'),
      ));
      $hidden->setRequired(false);
      $hidden->addValidator($date_validate);
      $hidden->setLabel('To Date');
      $this->addElement($hidden);

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
