<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Ynaffiliate_Form_Admin_Manage_AffiliateClient extends Engine_Form{
   
   public function init() {
     
       $this
            ->clearDecorators()
            ->addDecorator('FormElements')
            ->addDecorator('Form')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
            ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'))
     		->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'global_form f1',
                'method'=>'GET',
            'onSubmit' => "getdate()",
            ));
//      $this->setAttribs(array(
//          'class' => "global_form f1",
//          'onSubmit' => "getdate()",
//      ));
   
      $this->addElement('Text','affiliate_name', array(
          'label'=> 'Affiliate Name',
      ));
 $temp = '<input id="sday" name="date_toggled" type="text" value="" class="date date_toggled text_calendar" />
      <img src="./application/modules/Ynaffiliate/externals/images/calendar-blue.png" class="date_toggler img_calendar"/>';

      $this->addElement('Dummy', 'date1', array(         
          'label' => 'From Registered Date',
          'content' => $temp
      ));


      $temp = '<input id="eday" name="date_toggled" type="text" value="" class="date date_toggled text_calendar" />
      <img src="./application/modules/Ynaffiliate/externals/images/calendar-blue.png" class="date_toggler img_calendar"/>';

      $this->addElement('Dummy', 'date2', array(         
          'label' => 'To Registered Date',
          'content' => $temp
      ));

       $date_validate = new Zend_Validate_Date("YYYY-MM-dd");      
      $date_validate->setMessage("Please pick a valid day (yyyy-mm-dd)",  Zend_Validate_Date::FALSEFORMAT);      
     
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
