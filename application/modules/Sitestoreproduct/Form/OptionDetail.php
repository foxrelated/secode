<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Field.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_OptionDetail extends Engine_Form {

  protected $_stock_unlimited;
  protected $_in_stock;
  protected $_price;

  public function getStockUnlimited() {
    return $this->_stock_unlimited;
  }

  public function setStockUnlimited($stock_unlimited) {
    $this->_stock_unlimited = $stock_unlimited;
    return $this;
  }

  public function getInStock() {
    return $this->_in_stock;
  }

  public function setInStock($in_stock) {
    $this->_in_stock = $in_stock;
    return $this;
  }

  public function getPrice() {
    return $this->_price;
  }

  public function setPrice($price) {
    $this->_price = $price;
    return $this;
  }

  public function init() {
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    //$quantityCheck = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.quantity', 0);
    //$title = !empty($quantityCheck) ? 'Enter Price and Quantity For Configurations' : 'Enter Price For Configurations';
    $title = Zend_Registry::get('Zend_Translate')->_('Enter Price For Configurations');
    $this->setMethod('POST')
            ->setAttrib('class', 'global_form_smoothbox')
            ->setTitle(sprintf(Zend_Registry::get('Zend_Translate')->_('%s'), $title));
    
//    if(!empty($quantityCheck)){
//    if (!empty($this->_stock_unlimited)) {
//      $this->addElement('Radio', 'quantity_unlimited', array(
//          'label' => Zend_Registry::get('Zend_Translate')->_('Unlimited Quantity'),
//          'description' => Zend_Registry::get('Zend_Translate')->_('Do you have unlimited quantity of this attribute in your stock?'),
//          'multiOptions' => array(
//              "1" => "Yes", "0" => "No"
//          ),
//          'value' => 1,
//          'onclick' => 'showStock();',
//          'filters' => array(
//              'StripTags',
//              new Engine_Filter_Censor(),
//      )));
//      $this->addElement('Text', 'quantity', array(
//          'label' => Zend_Registry::get('Zend_Translate')->_('In Stock Quantity for this attribute value'),
//          'maxlength' => 5,
//          'description' => Zend_Registry::get('Zend_Translate')->_('You have unlimited quantities available for this product.'),
//          'validators' => array(
//              array('Int', false),
//              array('GreaterThan', true, array(-1))
//          ),
//          'filters' => array(
//              'StripTags',
//              new Engine_Filter_Censor(),
//      )));
//    } else {
//      $this->addElement('Text', 'quantity', array(
//          'label' => Zend_Registry::get('Zend_Translate')->_('In Stock Quantity for this attribute value'),
//          'maxlength' => 5,
//          'description' => sprintf(Zend_Registry::get('Zend_Translate')->_('You have %s total inventory stock available for this product.'), $this->_in_stock),
//          'required' => true,
//          'allowedEmpty' => false,
//          'validators' => array(
//              array('Int', false),
//              array('GreaterThan', true, array(-1))
//          ),
//          'filters' => array(
//              'StripTags',
//              new Engine_Filter_Censor(),
//      )));
//    }
//  }

     $this->addElement('Radio', 'price_increment', array(
        'label' => Zend_Registry::get('Zend_Translate')->_("Increment / Decrement Price for this Attribute Value"),
        'description' => Zend_Registry::get('Zend_Translate')->_('Do you want to increment or decrement the basic price of the product by the amount you will enter in the below "Price" field ?'),
        'multiOptions' => array(
            "1" => "Increment", "0" => "Decrement"
        ),
        'value' => 1,
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
    )));
     
    $localeObject = Zend_Registry::get('Locale');
    $currencyCode = $coreSettings->getSetting('payment.currency', 'USD');
    $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);
    $this->addElement('Text', 'price', array(
        'label' => sprintf(Zend_Registry::get('Zend_Translate')->_('Price (%s)'), $currencyName),
        'description' => sprintf(Zend_Registry::get('Zend_Translate')->_("The basic price of this product is %s . Enter the amount by which you want to increment or decrement the price of the product for this attribute."), $this->_price),
        'allowEmpty' => true,
        'maxlength' => 12,
        'value' => 0.00,
        'validators' => array(
            array('GreaterThan', false, array(-1))
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
    )));

    $this->addElement('Button', 'submit', array(
        'label' => Zend_Registry::get('Zend_Translate')->_('Save Settings'),
        'type' => 'submit',
        'decorators' => array(
            'ViewHelper',
        ),
        'order' => 10000,
        'ignore' => true,
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => Zend_Registry::get('Zend_Translate')->_('cancel'),
        'link' => true,
        'onclick' => 'parent.Smoothbox.close();',
        'prependText' => ' or ',
        'decorators' => array(
            'ViewHelper',
        ),
        'order' => 10001,
        'ignore' => true,
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
        'order' => 10002,
    ));
  }

}
