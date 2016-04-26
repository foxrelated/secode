<?php
class Ynidea_Form_Search extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box',
      ))
      ->setMethod('GET')
      ;
	$request = Zend_Controller_Front::getInstance()->getRequest();
	if($request->getControllerName() == 'my-ideas')
		$this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'ynidea_myideas', true));
	else {
		$this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(),'ynidea_viewallideas', true));
	}
    $this->addElement('Text', 'search', array(
      'label' => 'Search Ideas',
    ));
	/*
	 $this->addElement('Text', 'tags', array(
      'label' => 'Tags',
      'onchange' => 'this.form.submit();',
    ));
   */
   $arr_category_id = array('all' => 'All');
	//Industry
	 $this->addElement('Select', 'category_id', array(
      'label' => 'Category',
      'multiOptions' => $arr_category_id,
    ));
	
    $this->addElement('Select', 'award', array(
      'label' => 'With Awards',
      'multiOptions' => array(
        '' => 'All',
        '0' => 'No',
        '1' => 'Yes',
      ),
    ));

    $this->addElement('Hidden', 'page', array(
      'order' => 100
    ));

    $this->addElement('Hidden', 'tag', array(
      'order' => 101
    ));
	
	$this->addElement('Hidden', 'orderby', array(
      'order' => 101
    ));
	
	// Buttons
    $this->addElement('Button', 'submit_button', array(
	      'value' => 'submit_button',
	      'label' => 'Search',
	      'type' => 'submit',
	      'ignore' => true,
    ));
  }
}