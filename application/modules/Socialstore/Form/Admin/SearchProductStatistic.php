<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Search.php
 * @author     Long Le
 */
class Socialstore_Form_Admin_SearchProductStatistic extends Engine_Form {

  public function init() {
    $this 
      
      ->addPrefixPath('Socialstore_Form_Element', APPLICATION_PATH . '/application/modules/Socialstore/Form/Element', 'element')
            ->clearDecorators()
            ->addDecorator('FormElements')
            ->addDecorator('Form')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
            ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'))
     		->setAttribs(array(
                'id' => 'filter_form',
                'method'=>'GET',
                'class'=>'global_form_box',
            ));
			

	
	// search by title
    //$title = new Zend_Form_Element_Text('title');
	
	$this->addElement('text','search', array(
		'label'=>'Store Name',
		'decorators'=>array(
			array('ViewHelper'), 
			array('Label', array('tag'=>null,'placement'=>'PREPEND')),
			array('HtmlTag', array('tag'=>'div'))
		)
	));
	

		

   $this->addElement('Select', 'feature', array(
      'label' => 'Featured',
      'multiOptions' => array(
        ' ' => 'All',
        '0' => 'No',
        '1' => 'Yes',
      ),
    ));

       
    // Element: order
    $this->addElement('Hidden', 'order', array(
      'order' => 10004,
    ));

    // Element: direction
    $this->addElement('Hidden', 'direction', array(
      'order' => 10005,
    ));
	
    $submit = new Zend_Form_Element_Button('search', array('type' => 'submit','name'=>'checksub'));
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