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
class Socialstore_Form_Admin_Product_Search extends Engine_Form {

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
                'class' => 'global_form_box',
                'method'=>'GET',
            ));
	
	// search by title
    //$title = new Zend_Form_Element_Text('title');
	
	$this->addElement('text','title', array(
		'label'=>'Name',
		'decorators'=>array(
			array('ViewHelper'), 
			array('Label', array('tag'=>null,'placement'=>'PREPEND')),
			array('HtmlTag', array('tag'=>'div'))
		)
	));
	
	
	   // location
    /*$this->addElement('Select', 'category_id', array(
      'label' => 'Category',
      'multiOptions' => (array)Engine_Api::_()->getDbTable('categories','Socialstore')->getMultiOptions('..','All'),
      //'onchange' => 'this.form.submit();',
    ));*/
		
    $submit = new Zend_Form_Element_Button('search', array('type' => 'submit','name'=>'checksub'));
    $submit
            ->setLabel('Search')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
            ->addDecorator('HtmlTag2', array('tag' => 'div'));

   /* $this->addElement('Select', 'featured', array(
      'label' => 'Featured',
      'multiOptions' => array(
        ' ' => 'All',
        '0' => 'Unfeatured',
        '1' => 'Featured',
      ),
       'onchange' => 'this.form.submit();',
    )); */
 
		
     $this->addElement('Select', 'approve_status', array(
      'label' => 'Approve Status',
      'multiOptions' => array(
        '' => 'All',
        'new' => 'New',
        'waiting' => 'Waiting',
        'approved' => 'Approved',
        'denied' => 'Denied',  
      ),
    //   'onchange' => 'this.form.submit();',
    ));
     $this->addElement('Select', 'view_status', array(
      'label' => 'View Status',
      'multiOptions' => array(
        '' => 'All',
        'show' => 'Show',
        'hide' => 'Hide',
      ),
   //    'onchange' => 'this.form.submit();',
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