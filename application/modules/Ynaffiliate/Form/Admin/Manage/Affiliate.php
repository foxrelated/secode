<?php
class Ynaffiliate_Form_Admin_Manage_Affiliate extends Engine_Form {

  public function init() {
    $this
            ->clearDecorators()
            ->addDecorator('FormElements')
            ->addDecorator('Form')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
            ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'))
     		->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'global_form_box',
                'method'=>'GET',
            ));
	
	// search by title
    //$title = new Zend_Form_Element_Text('title');
	
	$this->addElement('text','name', array(
		'label'=>'Affiliate Name',
		'decorators'=>array(
			array('ViewHelper'), 
			array('Label', array('tag'=>null,'placement'=>'PREPEND')),
			array('HtmlTag', array('tag'=>'div'))
		)
	));
	
	
	// categories
    $this->addElement('Select', 'status', array(
      'label' => 'Approve Status',
        'multiOptions' => array(
            ''=>"All",
            0=>"Waiting",
            1=>"Approved",
            2=>"Denied",
            ),
      //'multiOptions' => (array)Engine_Api::_()->getDbTable('categories','groupbuy')->getMultiOptions('..', 'All'),
       //'onchange' => 'this.form.submit();',
    ));

    $this->addElement('Hidden', 'order', array(
      'order' => 10004,
    ));

    // Element: direction
    $this->addElement('Hidden', 'direction', array(
      'order' => 10005,
    ));
		
    $submit = new Zend_Form_Element_Button('search', array('type' => 'submit','name'=>'bt'));
    $submit
            ->setLabel('Search')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
            ->addDecorator('HtmlTag2', array('tag' => 'div'));	
   
    $this->addElements(array(
        $submit
    ));
  
  }

}