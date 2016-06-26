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

class Sitemenu_Widget_MenuMiniController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    // DON'T SHOW WIDGET, IF PLUGIN NOT ACTIVATED.
    $isPluginActivate = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemenu.isActivate', false);
    if(empty($isPluginActivate))
      return $this->setNoRender();
    
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    $this->view->sitemenuEnableLoginLightbox = $this->_getParam('sitemenu_enable_login_lightbox', 1);
    $this->view->disable_content = $this->_getParam("disable_content", null);
    Zend_Registry::set('is_sitemenu_mini_menu_widget_enabled', 1);
    Zend_Registry::set('sitemenu_on_logged_out_settings', $this->_getParam('sitemenu_on_logged_out', 1)); 
    $this->view->sitemenu_mini_menu_widget = $sitemenuMiniMenuWidgetExists = Zend_Registry::isRegistered('sitemenu_mini_menu_widget') ? Zend_Registry::get('sitemenu_mini_menu_widget') : null;
    if (!$viewer->getIdentity()) {
      $showOnLoggedOut = $this->_getParam('sitemenu_on_logged_out', 1);
      if(empty ($showOnLoggedOut))
        return $this->setNoRender();
    }elseif(empty ($sitemenuMiniMenuWidgetExists)) {
      Zend_Registry::set('sitemenu_mini_menu_widget', 'enable');
    }
    
    $userSignupTable = Engine_Api::_()->getDbtable('signup', 'user');
    $userSignupTableName = Engine_Api::_()->getDbtable('signup', 'user')->info('name');
    if(empty($viewer_id)){
      $tempClassArray = array(
          'Payment_Plugin_Signup_Subscription',
          'Sladvsubscription_Plugin_Signup_Subscription'
      );
      $db = Zend_Db_Table_Abstract::getDefaultAdapter();
      $subscriptionObj = $db->query('SELECT `class` FROM `engine4_user_signup` WHERE  `enable` = 1 ORDER BY `engine4_user_signup`.`order` ASC LIMIT 1')->fetch();      
      if(!empty($subscriptionObj) && isset($subscriptionObj['class']) && !empty($subscriptionObj['class']) && in_array($subscriptionObj['class'], $tempClassArray)){
        $isSubscriptionEnabled = true;
      }
    }
    $isSitemenuEnableSignupLightbox = $this->_getParam('sitemenu_enable_signup_lightbox', 1);
    if(empty($isSubscriptionEnabled) && !empty($isSitemenuEnableSignupLightbox))
    {
      $this->view->sitemenuEnableSignupLightbox = true;
    }
    $this->view->noOfUpdates = $this->_getParam('no_of_updates', 10);
    $this->view->searchPosition = $this->_getParam('search_position', 1);
    $this->view->miniSearchWidth = $this->_getParam('sitemenu_mini_search_width', 275);
    
    $temp_search_type = $this->_getParam('sitemenu_show_in_mini_options', null);
    //WORK FOR ADVANCED SEARCH PLUGIN
    if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteadvsearch') && $temp_search_type == null){
      $temp_search_type = 3;
    }elseif($temp_search_type == null){
      $temp_search_type = 1;
    }
    
    $this->view->changeMyLocation = $this->_getParam('changeMyLocation', 0);
    $this->view->showLocationBasedContent = $this->_getParam('showLocationBasedContent', 0);
    $this->view->changeMyLocationPosition = $this->_getParam('changeMyLocationPosition', 0);
    $this->view->searchbox_width = $this->_getParam('sitemenu_mini_search_width', '330');
    $this->view->searchType = $mini_menu_option = $temp_search_type;
    $this->view->show_suggestion = $this->_getParam('sitemenu_show_suggestion', 0);
    $this->view->sitestoreproductEnable = $isStoreproductEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreproduct');
    $hostType = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
    $this->view->sitemenu_show_icon = $this->_getParam('sitemenu_show_icon', 1);
    $this->view->menuItems = Engine_Api::_()->getApi('menus', 'core')->getNavigation('core_mini');
    $this->view->newMessageCount = Engine_Api::_()->getDbtable('updates', 'sitemenu')->getUnreadMessageCount($viewer);
    $tempHostType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemenu.global.view', 0);
    $sitemenu_check_mini_menu = Zend_Registry::isRegistered('sitemenu_check_mini_menu') ? Zend_Registry::get('sitemenu_check_mini_menu') : null;
    $sitemenuManageType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemenu.manage.type', 1);
    $sitemenuGlobalType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemenu.global.type', null);

    if( empty($isStoreproductEnabled) && $mini_menu_option == 2 ) {
      $this->view->searchType = 1;
    }
    if($isStoreproductEnabled) {
      $this->view->itemCount = Engine_Api::_()->getDbtable('carts', 'sitestoreproduct')->getProductCounts();
    }
    for( $check=0; $check<strlen($hostType); $check++ ) {
      $tempHostType += @ord($hostType[$check]);
    }
    if (!empty($viewer_id)) {
      $this->view->notificationCount = Engine_Api::_()->getDbtable('notifications', 'activity')->hasNotifications($viewer);
    }
    if(empty($sitemenuGlobalType) && (empty($sitemenu_check_mini_menu) || ($sitemenuManageType != $tempHostType)))
      return $this->setNoRender();
    
    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $action = $front->getRequest()->getActionName();
    $controller = $front->getRequest()->getControllerName();
    $this->view->isPost = $front->getRequest()->isPost();
    
    if (($module == 'user' && $controller == 'auth' && $action == 'login') || ($module == 'core' && $controller == 'error' && $action == 'requireuser')) {
      $this->view->isUserLoginPage = true;
    }
    if ($module == 'user' && $controller == 'signup' && $action == 'index') {
      $this->view->isUserSignupPage = true;
    }
    if ($module == 'core' && $controller == 'error' && $action == 'notfound') {
      $this->view->isUserSignupPage = true;
    } 
    
    $session = new Zend_Session_Namespace('User_Plugin_Signup_Account');
    if(isset($session) && isset($session->data) && !empty($session->data)) {
      $this->view->isUserSignupPage = true;
    }
  }

}