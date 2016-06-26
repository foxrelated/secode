<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitemenu_IndexController extends Core_Controller_Action_Standard {

    //  ACTION FOR GETTING CONTENT TO SHOW IN MAIN MENU WIDGET
    public function getTabContentAction() {
      $module_id = $this->_getParam('moduleId', null);
      if (empty($module_id))
          return;

      $viewby_field = $this->_getParam('viewby_field', 0);
      $content_limit = $this->_getParam('content_limit', 6);
      $is_category = $this->_getParam('is_category', 0);
      $category_limit = $this->_getParam('category_limit', 5);
      $category_id = $this->_getParam('category_id', null);
      $this->view->truncation_limit_content = $this->_getParam('truncation_limit_content', 20);
      $this->view->truncation_limit_category = $this->_getParam('truncation_limit_category', 20);
      $this->view->content_height = $this->_getParam('content_height', 220);
      $this->view->is_title_inside = $this->_getParam('is_title_inside', 0);
      $hostType = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
      $module = Engine_Api::_()->getItem('sitemenu_module', $module_id);
      $tempHostType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemenu.global.view', 0);
      $sitemenuManageType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemenu.manage.type', 1);

      if (empty($module) || !Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($module->module_name) || (!empty($module) && empty($module->status))) {
          return;
      }

      $orderby = 0;
      //getting column names from sitemenu modules for given module 
      if (!empty($viewby_field)) {
        switch ($viewby_field){
          case 1:
            $orderby = $module->like_field;
            break;
          case 2:
            $orderby = $module->comment_field;
            break;
          case 3:
            $orderby = $module->date_field;
            break;
          case 4:
            $orderby = $module->featured_field;
            break;
          case 5:
            $orderby = $module->sponsored_field;
            break;
          default :
            $orderby = 0;
            break;
        }
      }
      
      for( $check=0; $check<strlen($hostType); $check++ ) {
        $tempHostType += @ord($hostType[$check]);
      }

      $sitemenuGlobalType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemenu.global.type', null);
      $this->view->image_option = !empty($module->image_option) ? $module->image_option : 0;
      $this->view->content_limit = $content_limit;
      $this->view->module_name = !empty($module->module_name)? $module->module_name : null;

      if(empty($sitemenuGlobalType) && ($sitemenuManageType != $tempHostType))
        return;
      
      if (!empty($module->item_type)) {
        if(strstr($module->item_type, "sitereview")){
          $sitereviewTableName = explode("sitereview_listing_", $module->item_type);
          $listingtypeId = $sitereviewTableName[1];
          $has_item_type = Engine_Api::_()->hasItemType("sitereview_listing");
          $tempTableName = "sitereview_listing";
        }else{
          $has_item_type = Engine_Api::_()->hasItemType($module->item_type);
          $tempTableName = $module->item_type;
        }

        if (!empty($has_item_type)) {
            
          $params = array();
          $params['module_name'] = $module->module_name;
          $params['orderby'] = $orderby;
          $params['tempTableName'] = $tempTableName;

          if (!empty($content_limit)) {
              $params['content_limit'] = $content_limit;
          }
          
          if(!empty($category_id)){
            $params['category_id'] = $category_id;
          }          
          
          if(!empty($listingtypeId)) {
              $params['listingtypeId'] = $listingtypeId;
          }
          
          $this->view->obj = Engine_Api::_()->seaocore()->getModuleContent($params);
          
          if (!empty($is_category) && !empty($module->category_name)) {
            
            $category_table = Engine_Api::_()->getItemTable($module->category_name);
            $category_select = $category_table->select();
            if (!empty($listingtypeId)) {
              $category_select = $category_select->where('listingtype_id =?', $listingtypeId);
            }
            $category_select->limit($category_limit);
            $this->view->category_obj = $category_table->fetchAll($category_select);
            $this->view->showCategory = $is_category;
            $this->view->is_category_name = 1;
            
          }  elseif(!empty($is_category) && empty ($module->category_name) && !empty ($module->module_name)) {
            $this->view->is_category_name = 0;
            switch ($module->module_name) {
              case 'video':
                $categoryTable = Engine_Api::_()->getDbtable('categories', 'video');
                $category_select = $categoryTable->select();
                $this->view->category_obj = $categoryTable->fetchAll($category_select);
                $this->view->showCategory = 1;
                break;
              
              case 'classified':
                $categoryTable = Engine_Api::_()->getDbtable('categories', 'classified');
                $category_select = $categoryTable->select();
                $this->view->category_obj = $categoryTable->fetchAll($category_select);
                $this->view->showCategory = 1;
                break;
              
              case 'sitepagedocument':
                $categoryTable = Engine_Api::_()->getDbtable('categories', 'sitepagedocument');
                $category_select = $categoryTable->select();
                $this->view->category_obj = $categoryTable->fetchAll($category_select);
                $this->view->showCategory = 1;
                break;
              
              case 'sitepageevent':
                $categoryTable = Engine_Api::_()->getDbtable('categories', 'sitepageevent');
                $category_select = $categoryTable->select();
                $this->view->category_obj = $categoryTable->fetchAll($category_select);
                $this->view->showCategory = 1;
                break;
              
              case 'sitepagenote':
                $categoryTable = Engine_Api::_()->getDbtable('categories', 'sitepagenote');
                $category_select = $categoryTable->select();
                $this->view->category_obj = $categoryTable->fetchAll($category_select);
                $this->view->showCategory = 1;
                break;

              default:
                break;
            }
            
          }
        }   
      }   
    }
    
    public function messageAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->noOfUpdates = $noOfUpdates = $this->_getParam('noOfUpdates', 10);
        $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('messages_conversation')->getInboxPaginator($viewer);
        $paginator->setItemCountPerPage($noOfUpdates);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Now mark them all as view
        $conversation_ids = array();
        foreach ($paginator as $conversation) {
            $conversation_ids[] = $conversation->conversation_id;
        }
        Engine_Api::_()->getDbtable('updates', 'sitemenu')->markMessagesAsShow($viewer, $conversation_ids);
    }

    public function settingAction() {
        $this->view->settings = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("user_settings", array());
        // Check last super admin
        $user = Engine_Api::_()->user()->getViewer();
        if ($user && $user->getIdentity()) {
            if (1 === count(Engine_Api::_()->user()->getSuperAdmins()) && 1 === $user->level_id) {
                foreach ($navigation as $page) {
                    if ($page instanceof Zend_Navigation_Page_Mvc &&
                            $page->getAction() == 'delete') {
                        $navigation->removePage($page);
                    }
                }
            }
        }
    }

    public function friendRequestAction() {
        $viewer = Engine_Api::_()->user()->getViewer();

        $this->view->noOfUpdates = $noOfUpdates = $this->_getParam('noOfUpdates', 10);

        $this->view->showSuggestion = $this->_getParam('showSuggestion', 0);
        $this->view->requests = $friendRequests = Engine_Api::_()->getDbtable('updates', 'sitemenu')->getRequestsPaginator($viewer);
        $friendRequests->setItemCountPerPage($noOfUpdates);

        // Force rendering now
        $this->_helper->viewRenderer->postDispatch();
        $this->_helper->viewRenderer->setNoRender(true);
        
        // Now mark them all as view
        $notificationIds = array();
        foreach ($friendRequests as $friendRequest) {
            $notificationIds[] = $friendRequest->notification_id;
        }
        Engine_Api::_()->getDbtable('updates', 'sitemenu')->markUpdatesAsShow($viewer, $notificationIds);
    }

    public function notificationAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    
    $this->view->noOfUpdates = $noOfUpdates = $this->_getParam('noOfUpdates', 10);

    $notifications_sql = Engine_Api::_()->getDbtable('updates', 'sitemenu')->getNotificationsPaginatorSql($viewer);
    $notificationsObj = Engine_Api::_()->getDbtable('notifications', 'activity')->fetchAll($notifications_sql);
    $this->view->notifications = $notifications = Zend_Paginator::factory($notifications_sql);
    $notifications->setItemCountPerPage($noOfUpdates);

    // Force rendering now
    $this->_helper->viewRenderer->postDispatch();
    $this->_helper->viewRenderer->setNoRender(true);

    $this->view->hasunread = false;
   
   // Now mark them all as view
    $notificationIds = array();
    
    $tempNotificationArray = $notificationsObj->toArray();
    foreach( $tempNotificationArray as $notification ) {      
      $notificationIds[] = $notification['notification_id'];
    }
    Engine_Api::_()->getDbtable('updates', 'sitemenu')->markUpdatesAsShow($viewer, $notificationIds);
  }

    public function markNotificationsAsReadAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        Engine_Api::_()->getDbtable('updates', 'sitemenu')->markNotificationsAsRead($viewer);
    }

    public function markMessageReadUnreadAction() {
        $message_id = $this->_getParam('messgae_id', null);
        $is_read = $this->_getParam('is_read', 0);
        if (empty($message_id))
            return;

        Engine_Api::_()->sitemenu()->markMessageReadUnread($message_id, $is_read);
    }

    public function checkNewUpdatesAction() {
        $viewer = Engine_Api::_()->user()->getViewer();

        $this->view->newMessage = Engine_Api::_()->getDbtable('updates', 'sitemenu')->getUnreadMessageCount($viewer);

        $this->view->newFriendRequest = Engine_Api::_()->getDbtable('updates', 'sitemenu')->getNewUpdatesCount($viewer, array('type' => 'friend_request'));

        $this->view->newNotification = Engine_Api::_()->getDbtable('updates', 'sitemenu')->getNewUpdatesCount($viewer, array('isNotification' => 'true'));
    }
    
  public function getCartItemCountAction()
  {
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $cartProductCounts = 0;

    if (empty($viewer_id)) {
      $session = new Zend_Session_Namespace('sitestoreproduct_viewer_cart');
      $tempUserCart = @unserialize($session->sitestoreproduct_guest_user_cart);
      if (empty($tempUserCart))
      {
        $this->view->cartProductCounts = $cartProductCounts;
        return;
      }
      
      foreach ($tempUserCart as $values) {
        if (isset($values['config']) && is_array($values['config']))
          foreach ($values['config'] as $quantity) {
            $cartProductCounts += $quantity['quantity'];
          }
        else
          $cartProductCounts += $values['quantity'];
      }
    } else {
      $getCartId = Engine_Api::_()->getDbtable('carts', 'sitestoreproduct')->getCartId($viewer_id);
      if (!empty($getCartId) )
        $cartProductCounts = Engine_Api::_()->getDbtable('carts', 'sitestoreproduct')->getProductCounts($getCartId);
    }
    
    $this->view->cartProductCounts = $cartProductCounts;
  }
  
  public function clearMenusCacheAction() {

        //REMOVE CACHEING OF MAIN MENU WHEN MENU EDITOR IS VISITED ON ADMIN SIDE CONTROL PANEL
        $cache = Zend_Registry::get('Zend_Cache');
        $cache->remove('footer_menu_cache');
        $levels = Engine_Api::_()->getDbtable('levels', 'authorization')->getLevelsAssoc();
        foreach ($levels as $level_id => $level_name) {
            $cache->remove('main_menu_html_for_' . $level_id);
            $cache->remove('main_menu_cache_level_' . $level_id);
        }

        return;
    }

}