<?php
class Ynfundraising_Form_Admin_Search extends Engine_Form {
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

    //Search Title
    $this->addElement('Text', 'title', array(
      'label' => 'Keyword',
    ));

    $date_validate = new Zend_Validate_Date("YYYY-MM-dd");
    $date_validate->setMessage("Please pick a valid day (yyyy-mm-dd)", Zend_Validate_Date::FALSEFORMAT);

    $this->addElement('Text', 'start_date', array(
    		'label' => 'From Date',
    		'required' => false,
    		'style' =>      "width:70px;",
    		));
    $this->getElement('start_date')->addValidator($date_validate);

    $this->addElement('Text', 'end_date', array(
    		'label' => 'To Date',
    		'style' =>      "width:70px;",
    		// 'validator' => $date_validate,
    		'required' => false,
    		));
    $this->getElement('end_date')->addValidator($date_validate);

    //Type Filter
    if(Engine_Api::_ ()->getApi ( 'core', 'ynfundraising' )->checkIdeaboxPlugin ())
	{
	    $this->addElement('Select', 'type', array(
	      'label' => 'Type',
	      'multiOptions' => array(
	        ''  => 'All',
	        'idea' => 'Idea',
	        'trophy' => 'Trophy',
	        'user' => 'User',
	    ),
	      'value' => '',
	    ));
	}

    //Status Filter
    $this->addElement('Select', 'status', array(
      'label' => 'Status',
      'multiOptions' => array(
        ''  => 'All',
        'ongoing' => 'Ongoing',
        'closed' => 'Closed',
        'reached' => 'Reached',
        'expired' => 'Expired',
    ),
      'value' => '',
    ));

    //Featured Filter
    $this->addElement('Select', 'featured', array(
      'label' => 'Featured',
      'multiOptions' => array(
        ''  => 'All',
        '1' => 'Only Featured Campaigns',
        '0' => 'Only Non Featured Campaigns',
    ),
      'value' => 'all',
    ));

     // Element: order
    $this->addElement('Hidden', 'orderby', array(
      'order' => 101,
      'value' => 'campaign_id'
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