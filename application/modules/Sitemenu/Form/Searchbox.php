<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Searchbox.php 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitemenu_Form_Searchbox extends Engine_Form {

  protected $_isMainMenu;
  
  public function setIsMainMenu($id) {
    $this->_isMainMenu = $id;
    return $this;
  }
  
  public function init() {
    
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    
    if( empty($this->_isMainMenu)) {
      $formId = 'miniMenuProductSearchForm';
      $searchContainerId = 'miniMenuProductSearch';
      $placeHolderContent = $view->translate('Search Products...');
    }elseif(!empty ($this->_isMainMenu) && $this->_isMainMenu == 1) {
      $formId = 'mainMenuProductSearchForm';
      $searchContainerId = 'mainMenuProductSearch';
      $placeHolderContent = $view->translate('Search...');
    }else{
      $formId = 'footerMenuProductSearchForm';
      $searchContainerId = 'footerMenuProductSearch';
      $placeHolderContent = $view->translate('Search Products...');
    }

    $this->setAttribs(array(
        'id' => $formId,
        'method' => 'GET',
    ));
    $this->setAction($view->url(array('action' => 'index'), "sitestoreproduct_general", true))->getDecorator('HtmlTag');

    $this->addElement('Text', $searchContainerId, array(
        'label' => '',
        'placeholder' => $placeHolderContent,
        'autocomplete' => 'off',
        'style' => "width:300px;",
    ));

    $this->addElement('Button', 'submitButton', array(
        'label' => 'Search',
        'type' => 'submit',
        'ignore' => true,
        'onClick' => 'advancedMenuDoSearching(this)',
    ));
  }
}