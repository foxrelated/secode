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
class Groupbuy_Form_Admin_Search extends Engine_Form {

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
	
	$this->addElement('text','title', array(
		'label'=>'Keywords',
		'decorators'=>array(
			array('ViewHelper'), 
			array('Label', array('tag'=>null,'placement'=>'PREPEND')),
			array('HtmlTag', array('tag'=>'div'))
		)
	));
	
	
	// categories
    $this->addElement('Select', 'category', array(
      'label' => 'Category',
      'multiOptions' => (array)Engine_Api::_()->getDbTable('categories','groupbuy')->getMultiOptions('..', 'All'),
       'onchange' => 'this.form.submit();',
    ));

   // location
    $this->addElement('Select', 'location', array(
      'label' => 'Location',
      'multiOptions' => (array)Engine_Api::_()->getDbTable('locations','groupbuy')->getMultiOptions('..','All'),
       'onchange' => 'this.form.submit();',
    ));
		
    $submit = new Zend_Form_Element_Button('search', array('type' => 'submit','name'=>'minh'));
    $submit
            ->setLabel('Search')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
            ->addDecorator('HtmlTag2', array('tag' => 'div'));

    $this->addElement('Select', 'featured', array(
      'label' => 'Featured',
      'multiOptions' => array(
        ' ' => 'All',
        '0' => 'Unfeatured',
        '1' => 'Featured',
      ),
       'onchange' => 'this.form.submit();',
    ));
 
		
     $this->addElement('Select', 'status', array(
      'label' => 'Status',
      'multiOptions' => array(
        '-2' => 'All',
        '0' => 'Created',
        '10' => 'Pending',
        '20' => 'Upcoming',
        '30' => 'Running',  
        '40' => 'Closed',  
        '50' => 'Canceled',  
      ),
       'onchange' => 'this.form.submit();',
    ));
     $this->addElement('Select', 'published', array(
      'label' => 'Published',
      'multiOptions' => array(
        ' ' => 'All',
        '0' => 'Not Published',
        '10' => 'Waiting',
        '20' => 'Approved',
        '30' => 'Denied',  
      ),
       'onchange' => 'this.form.submit();',
    ));
    
    // Element: order
    $this->addElement('Hidden', 'order', array(
      'order' => 10004,
    ));

    // Element: direction
    $this->addElement('Hidden', 'direction', array(
      'order' => 10005,
    ));
	
    $this->addElements(array(
        $submit
    ));

  }

}