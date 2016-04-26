<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Groupbuy
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Search.php
 * @author     Minh Nguyen
 */
class Groupbuy_Form_Statistic_Search extends Engine_Form {

  public function init() {
    $this
            //->clearDecorators()
            //->addDecorator('FormElements')
            //->addDecorator('Form')
            //->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
            //->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'))
     		->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'global_form_box',
                'method'=>'GET',
            ));
	
	$from = new Engine_Form_Element_Date('fromDate');
    $from->setLabel("From");
    $from->setAllowEmpty(false);
    $this->addElement($from);
    
    $to = new Engine_Form_Element_Date('toDate');
    $to->setLabel("To");
    $to->setAllowEmpty(false);
    $this->addElement($to);   
	
	$this->addElement('text','buyer_name', array(
		'label'=>'Buyer Name',
		
	));
	
    $this->addElement('text','code', array(
		'label'=>'Coupon Code',
	));


   
    $submit = new Zend_Form_Element_Button('search', array('type' => 'submit','name'=>'minh'));
    $submit
            ->setLabel('Search')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
            ->addDecorator('HtmlTag2', array('tag' => 'div'));

		
    // Element: order
    $this->addElements(array(
        $submit
    ));

  }

}