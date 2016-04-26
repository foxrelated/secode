<?php
class Ynjobposting_Form_Admin_Transactions_Filter extends Engine_Form {
    public function init() {
        $this->clearDecorators()
             ->addDecorator('FormElements')
             ->addDecorator('Form')
             ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
             ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));
    	
		$this->setAttribs(array(
            'class' => 'global_form_box',
            'id' => 'filter_form',
            'method'=>'GET',
        ));
		
        $this->addElement('Select', 'gateway_id', array(
            'label' => 'Payment Method',
            'multiOptions' => array(
                'all'   => 'All',
            ),
            'value' => 'all',
        ));
        
        $this->addElement('Hidden', 'order', array(
            'order' => 101,
            'value' => 'transaction.transaction_id'
        ));
    
        // Element: direction
        $this->addElement('Hidden', 'direction', array(
            'order' => 102,
            'value' => 'DESC',
        ));
        
        $this->addElement('Button', 'submit_btn', array(
            'label' => 'Search',
            'type' => 'submit',
            'ignore' => true,
        ));
        
        $this->submit_btn->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
            ->addDecorator('HtmlTag2', array('tag' => 'div'));
    }
}