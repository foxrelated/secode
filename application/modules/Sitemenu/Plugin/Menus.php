<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitemenu_Plugin_Menus {

  public function onMenuInitialize_SitemenuMiniNotification($row) {

    $isSitemenuMiniMenuExist = $this->isSitemenuMiniMenuExist();
    if (empty($isSitemenuMiniMenuExist))
      return;

    $viewer = Engine_Api::_()->user()->getViewer();
    if ($viewer->getIdentity()) {
      return array(
          'label' => $row->label,
          'name' => $row->name,
          'action' => 'notification',
          'newNotificationCount' => Engine_Api::_()->getDbtable('updates', 'sitemenu')->getNewUpdatesCount($viewer, array('isNotification' => 'true'))
      );
    }

    return false;
  }

  public function onMenuInitialize_SitemenuMiniFriendRequest($row) {
    $isSitemenuMiniMenuExist = $this->isSitemenuMiniMenuExist();
    if (empty($isSitemenuMiniMenuExist))
      return;

    $viewer = Engine_Api::_()->user()->getViewer();

    if ($viewer->getIdentity()) {
      return array(
          'label' => $row->label,
          'name' => $row->name,
          'action' => 'friend-request',
          'newFriendRequestCount' => Engine_Api::_()->getDbtable('updates', 'sitemenu')->getNewUpdatesCount($viewer, array('type' => 'friend_request'))
      );
    }

    return false;
  }

  public function onMenuInitialize_SitemenuMiniCart($row) {

    $isSitemenuMiniMenuExist = $this->isSitemenuMiniMenuExist();
    $isSiteStoreProductEnabled=Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreproduct');
    if (empty($isSitemenuMiniMenuExist)||empty ($isSiteStoreProductEnabled))
      return false;

    $viewer = Engine_Api::_()->user()->getViewer();
    if ($viewer->getIdentity())
      $level_id = $viewer->level_id;
    else
      $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;

    $isBuyAllow = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestore_store', "allow_buy");

    if (!empty($isBuyAllow)) {
      $update_cart_notification = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.cart.update', 1);

      if (!empty($update_cart_notification)) {
        $viewer_id = $viewer->getIdentity();
        $cartProductCounts = 0;

        if (empty($viewer_id)) {
          $session = new Zend_Session_Namespace('sitestoreproduct_viewer_cart');
          if (!empty($session->sitestoreproduct_guest_user_cart)) {
            $tempUserCart = array();
            $tempUserCart = @unserialize($session->sitestoreproduct_guest_user_cart);

            foreach ($tempUserCart as $values) {
              if (isset($values['config']) && is_array($values['config']))
                foreach ($values['config'] as $quantity) {
                  $cartProductCounts += $quantity['quantity'];
                }
              else
                $cartProductCounts += $values['quantity'];
            }
          }
        }
        else
          $cartProductCounts = Engine_Api::_()->getDbtable('carts', 'sitestoreproduct')->getProductCounts();

        return array(
            'label' => $row->label,
            'class' => 'no-dloader',
            'name' => $row->name,
            'action' => 'get-cart-products',
            'itemCount' => $cartProductCounts
        );
      }
      else
        return false;
    }
    return false;
  }

  //CHECKS THAT ADVANCED MINI MENU IS PLACED OR NOT IN THE HEADER
  private function isSitemenuMiniMenuExist() {

    $pagesTable = Engine_Api::_()->getDbtable('pages', 'core');
    $headerPageId = $pagesTable->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'header')
            ->limit(1)
            ->query()
            ->fetchColumn();

    if (!empty($headerPageId)) {
      $contentTable = Engine_Api::_()->getDbtable('content', 'core');
      $isSitemenuMiniMenuExist = $contentTable->select()
              ->from('engine4_core_content', 'content_id')
              ->where('page_id = ?', $headerPageId)
              ->where('name = ?', 'sitemenu.menu-mini')
              ->limit(1)
              ->query()
              ->fetchColumn();

      if (!empty($isSitemenuMiniMenuExist)) {
        return TRUE;
      }
    }

    return FALSE;
  }

}