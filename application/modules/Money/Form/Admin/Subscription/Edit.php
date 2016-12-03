<?php
class Money_Form_Admin_Subscription_Edit extends Engine_Form
{
  public function init()
  {
      $this->addElement('Text', 'title', array(
          'Label' => 'Title' 
      ));
      
      //$this->addElement('Select', '');
  }
}