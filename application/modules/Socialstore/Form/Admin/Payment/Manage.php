<?php
class Groupbuy_Form_Admin_Payment_Manage extends Engine_Form {

  public function init() {
  	$this
            ->clearDecorators()
            ->addDecorator('FormElements')
            ->addDecorator('Form')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
            ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'))
    ;
     $this   ->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'global_form_box',
                'method'=>'GET',
            ));
       $title = new Zend_Form_Element_Text('user');
    $title   ->setLabel('User Account')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
            ->addDecorator('HtmlTag', array('tag' => 'div'))
           ;
             $this->addElements(array(
        $title
    ));     
	$this->addElement('Select', 'option_select', array(
      'label' => 'Status',
      'multiOptions' => array(
        '-2' => 'All',
        '0' => 'Pending',
        '1' => 'Succ',
        '-1' => 'Failed',
      ),
    ));
    $submit = new Zend_Form_Element_Button('fitter', array('type' => 'submit','name'=>'fitter'));
     $submit
            ->setLabel('Filter')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
            ->addDecorator('HtmlTag2', array('tag' => 'div'));
	    $this->addElements(array(
        $submit
    ));
            
    
  }

}