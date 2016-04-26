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
class Socialstore_Form_Product_Search extends Engine_Form
{
  public function init()
  {
    $this
      ->addPrefixPath('Socialstore_Form_Decorator', APPLICATION_PATH . '/application/modules/Socialstore/Form/Decorator', 'decorator')
      ->addPrefixPath('Socialstore_Form_Element', APPLICATION_PATH . '/application/modules/Socialstore/Form/Element', 'element')
      ->addElementPrefixPath('Socialstore_Form_Decorator', APPLICATION_PATH . '/application/modules/Socialstore/Form/Decorator', 'decorator');
  	
  	$this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box',
      	'style' => 'margin-bottom: 15px'
      )) ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
      		'module'=>'socialstore',
         	'controller' => 'product',
          	'action' => 'listing',
       	 ), 'default', true));
       	 
    
    $this->addElement('Text', 'search', array(
      	'label' => 'Search Products',
    //  	'onchange' => 'this.form.submit();',
    ));

    $this->addElement('Text', 'from', array(
    	'label' => 'Price From',
    //	'onchange' => 'this.form.submit();',
    	));
    	
	$this->addElement('Text', 'to', array(
    	'label' => 'Price To',
    //	'onchange' => 'this.form.submit();',
    	));
	$route = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.pathname', "socialstore");
    	
    $this->addElement('MultiLevel', 'category_id', array(
        'label' => 'Category',
        'required'=>false,
        'model'=>'Socialstore_Model_DbTable_Storecategories',
        'onchange'=>"en4.store.changeCategory($(this),'category_id','Socialstore_Model_DbTable_Storecategories','$route')",
        'title' => '',
		'value' => 0
     ));
     
    $this->addElement('Select', 'orderby', array(
      'label' => 'Browse By',
      'multiOptions' => array(
        'creation_date' => 'Most Recent Products',
      	'featured' => 'Featured Products',
        'rate_ave' => 'Most Rated Products',
      ),
    //  'onchange' => 'this.form.submit();',
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Search',
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array(
      'submit',
      ), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper'
      ),
    ));

    $this->addElement('Hidden', 'page', array(
      'order' => 100
    ));

    $this->addElement('Hidden', 'start_time', array(
      'order' => 102
    ));

    $this->addElement('Hidden', 'end_time', array(
      'order' => 103
    ));
    
    // Populate
    if (Zend_Registry::isRegistered('product_search_params')) {
    	$values = Zend_Registry::get('product_search_params');
	    $this->populate($values);
    }
  }
  
}

