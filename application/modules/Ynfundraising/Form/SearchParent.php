<?php
class Ynfundraising_Form_SearchParent extends Engine_Form
{
  public function init()
  {
    $this
      ->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
      ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'))
      ;

    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box',
      ))
      ->setMethod('POST');
		  
	$view = new Zend_Form_Element_Select('view');
    $view
      ->setLabel('View')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'))
      ->setMultiOptions(array(
        '' => 'All',
        '0' => 'My Ideas/Trophies',
        '1' => 'Other Ideas/Trophies',
      ))
      ->setValue('');
	 
	$search = new Zend_Form_Element_Text('search');
    $search
      ->setLabel('Keyword')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));
	 
    
	
	$submit = new Zend_Form_Element_Button('search2', array('type' => 'submit'));
    $submit
      ->setLabel('Search')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
      ->addDecorator('HtmlTag2', array('tag' => 'div'));
	
	$this->addElements(array(
      $view, 
      $search,  
      $submit,
    ));
	
  }
}