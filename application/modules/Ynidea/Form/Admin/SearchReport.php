<?php
class Ynidea_Form_Admin_SearchReport extends Engine_Form {
  public function init()
  {
    $this->clearDecorators()
         ->addDecorator('FormElements')
         ->addDecorator('Form')
         ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
         ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));

    $this->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'global_form_box',
                'method'=>'GET',
            ));

   
    //Feature Filter
    $this->addElement('Select', 'type', array(
      'label' => 'Type',
      'multiOptions' => array(
        ''=> 'All',
        'Spam content' => 'Bad behavior',
        'Wrong activity' => 'Problem',
        'Bad content' => 'Duplicate',
    ),
      'value' => '',
      'onchange' => 'this.form.submit();',
    ));

     // Element: order
    $this->addElement('Hidden', 'orderby', array(
      'order' => 101,
      'value' => 'creation_date'
    ));

    // Element: direction
    $this->addElement('Hidden', 'direction', array(
      'order' => 102,
      'value' => 'DESC',
    ));

     // Element: direction
    $this->addElement('Hidden', 'page', array(
      'order' => 103,
    ));

     // Buttons
    $this->addElement('Button', 'button', array(
      'label' => 'Search',
      'type' => 'submit',
    ));

    $this->button->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
            ->addDecorator('HtmlTag2', array('tag' => 'div'));
  }
}