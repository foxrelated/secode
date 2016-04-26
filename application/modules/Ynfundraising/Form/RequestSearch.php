<?php
class Ynfundraising_Form_RequestSearch extends Engine_Form {
	public function init() {
		$this->setAttribs ( array (
				'id' => 'filter_form',
				'class' => 'global_form_box',
				'method' => 'GET'
		) );

		// Search Title
		$this->addElement ( 'Text', 'title', array (
				'label' => 'Idea/Trophy Title'
		) );

		$date_validate = new Zend_Validate_Date ( "YYYY-MM-dd" );
		$date_validate->setMessage ( "Please pick a valid day (yyyy-mm-dd)", Zend_Validate_Date::FALSEFORMAT );

		$this->addElement ( 'Text', 'start_date', array (
				'label' => 'From Date',
				'required' => false,
		) );
		$this->getElement ( 'start_date' )->addValidator ( $date_validate );

		$this->addElement ( 'Text', 'end_date', array (
				'label' => 'To Date',
				// 'validator' => $date_validate,
				'required' => false
		) );

		$this->getElement ( 'end_date' )->addValidator ( $date_validate );

		// Status Filter
		$this->addElement ( 'Select', 'status', array (
				'label' => 'Status',
				'multiOptions' => array (
						'' => 'All',
						'waiting' => 'Waiting',
						'approved' => 'Approved',
						'denied' => 'Denied',
				),
				'value' => ''
		) );

		/*
		// Element: order
		$this->addElement ( 'Hidden', 'orderby', array (
				'order' => 101,
				'value' => 'request_id'
		) );

		// Element: direction
		$this->addElement ( 'Hidden', 'direction', array (
				'order' => 102,
				'value' => 'DESC'
		) );
		*/
		// Element: direction
		$this->addElement ( 'Hidden', 'page', array (
				'order' => 103
		) );

		// Buttons
		$this->addElement ( 'Button', 'button', array (
				'label' => 'Search',
				'type' => 'submit'
		) );
	}
}