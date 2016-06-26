<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitemenu_Widget_SearchboxSitemenuController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    // DON'T SHOW WIDGET, IF PLUGIN NOT ACTIVATED.
    $isPluginActivate = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemenu.isActivate', false);
    if(empty($isPluginActivate))
      return $this->setNoRender();
    
    $this->view->showLocationBasedContent = $this->_getParam('showLocationBasedContent', 0);
    $this->view->isMainMenu = $isMainMenu = $this->_getParam('isMainMenu', null);
    $this->view->searchbox_width = $this->_getParam('advsearch_search_box_width');
    $this->view->productSearch = $productSearch = $this->_getParam('advancedMenuProductSearch');
    $sitemenu_searchbox = Zend_Registry::isRegistered('sitemenu_searchbox') ? Zend_Registry::get('sitemenu_searchbox') : null;
    if(empty($sitemenu_searchbox))
      return $this->setNoRender();
    
    $viewer = Engine_Api::_()->user()->getViewer();
    if( $productSearch == 1) {
      $requestAllParams = Zend_Controller_Front::getInstance()->getRequest()->getParams();
      if(!empty($requestAllParams) && isset($requestAllParams['query'])){
        $this->view->searched_text = $requestAllParams['query'];
      }
      $require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_search;
      if(!$require_check){
        if( $viewer->getIdentity()){
          $this->view->search_check = true;
        }
        else{
          $this->view->search_check = false;
        }
      }
      else $this->view->search_check = true;
    } elseif($productSearch == 2) {
      $isStoreproductEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreproduct');
      if( empty($isStoreproductEnabled) ) {
        return $this->setNoRender();
      }
      $this->view->form = $form = new Sitemenu_Form_Searchbox(array('isMainMenu' => $isMainMenu));
    }    
  }

}
