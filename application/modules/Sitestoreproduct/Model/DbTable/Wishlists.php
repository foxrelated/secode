<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Wishlists.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Wishlists extends Engine_Db_Table {

  protected $_rowClass = 'Sitestoreproduct_Model_Wishlist';

  public function userWishlists($owner, $total_item = 10, $wishlist_id = 0, $recentWishlistId = 0) {

    //GET VIEWER DETAIL
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GET WISHLIST TABLE
    $wishlistTableName = $this->info('name');

    //MAKE QUERY
    $select = $this->select()->setIntegrityCheck(false);

    if (!empty($recentWishlistId)) {
      $select->from($wishlistTableName, array('wishlist_id'));
    } else {
      $select->from($wishlistTableName);
    }

    $select->where($wishlistTableName . '.owner_id = ?', $owner->getIdentity())
            ->group($wishlistTableName . '.wishlist_id')
            ->order('wishlist_id DESC');

    if (!empty($wishlist_id)) {
      $select->where($wishlistTableName . '.wishlist_id != ?', $wishlist_id);
    }

    //LOGGED IN USER
    if (!empty($viewer_id) && $viewer_id != $owner->getIdentity()) {

      //GET AUTHORIZATION TABLE
      $authorizationTable = Engine_Api::_()->getDbtable('allow', 'authorization');
      $authorizationTableName = $authorizationTable->info('name');

      $authorizationAllow = array('everyone');
      $authorizationAllow[] = 'registered';

      //SAME AS OWNER NETWORK
      $owner_network = $authorizationTable->is_network($owner, $viewer);
      if (!empty($owner_network)) {
        $authorizationAllow[] = 'owner_network';
      }

      //OWNERS FRIEND
      $owner_member = $owner->membership()->isMember($viewer, true);
      if (!empty($owner_member)) {
        $authorizationAllow[] = 'owner_member';
      }

      //OWNERS FRIEND AND FRIREND OF OWNERS FRIEND
      $owner_member_member = $authorizationTable->is_owner_member_member($owner, $viewer);
      if (!empty($owner_member_member)) {
        $authorizationAllow[] = 'owner_member_member';
      }

      $select->join($authorizationTableName, "$authorizationTableName.resource_id = $wishlistTableName.wishlist_id", array())
              ->where("$authorizationTableName.resource_type = ?", 'sitestoreproduct_wishlist')
              ->where("$authorizationTableName.role IN (?)", (array) $authorizationAllow);
    }

    if (!empty($recentWishlistId)) {
      return $select->query()
                      ->fetchColumn();
    } else {
      if (!empty($total_item)) {
        $select = $select->limit($total_item);
      }

      //RETURN RESULTS
      return $this->fetchAll($select);
    }
  }

  public function getUserWishlists($viewer_id) {

    //RETURN IF VIEWER ID IS EMPTY
    if (empty($viewer_id)) {
      return;
    }

    //MAKE QUERY
    $select = $this->select()->where('owner_id = ?', $viewer_id);

    //RETURN RESULTS
    return $this->fetchAll($select);
  }

  public function getBrowseWishlists($params = array()) {

    //GE WISHLIST PAGE TABLE
    $wishlistProductTable = Engine_Api::_()->getDbtable('wishlistmaps', 'sitestoreproduct');
    $wishlistProductTableName = $wishlistProductTable->info('name');

    //GET WISHLIST TABLE
    $wishlistTableName = $this->info('name');

    //MAKE QUERY
    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($wishlistTableName)
            ->joinLeft($wishlistProductTableName, "$wishlistProductTableName.wishlist_id = $wishlistTableName.wishlist_id", array("COUNT($wishlistProductTableName.wishlist_id) AS total_item"));

    if (isset($params['search']) && !empty($params['search'])) {
      $search = $params['search'];
      $select->where("$wishlistTableName.title LIKE '%$search%' OR $wishlistTableName.body LIKE '%$search%'");
    }
    
    if (isset($params['is_only_featured']) && !empty($params['is_only_featured'])) {
      $select->where("$wishlistTableName.featured = ?", 1);
    }

    if (isset($params['text']) && !empty($params['text'])) {
      $text = $params['text'];
      $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');
      $select->joinLeft($tableUserName, "$tableUserName.user_id = $wishlistTableName.owner_id", array("user_id"))
              ->where("$tableUserName.username LIKE '%$text%' OR $tableUserName.displayname LIKE '%$text%' OR $tableUserName.email LIKE '$text'");
    }
    elseif(isset($params['search_wishlist']) && !empty($params['search_wishlist'])) {
      $search_wishlist = $params['search_wishlist'];
      $viewer = Engine_Api::_()->user()->getViewer();
      $viewer_id = $viewer->getIdentity();
      
      if($search_wishlist == 'like_wishlists') {
        $likeTableName = Engine_Api::_()->getDbtable('likes', 'core')->info('name');
        $select
                ->join($likeTableName, "$likeTableName.resource_id = $wishlistTableName.wishlist_id")
                ->where($likeTableName . '.poster_type = ?', 'user')
                ->where($likeTableName . '.poster_id = ?', $viewer_id)
                ->where($likeTableName . '.resource_type = ?', 'sitestoreproduct_wishlist');        
      }
      elseif($search_wishlist == 'follow_wishlists') {
        $followTableName = Engine_Api::_()->getDbtable('follows', 'seaocore')->info('name');
        $select
                ->join($followTableName, "$followTableName.resource_id = $wishlistTableName.wishlist_id")
                ->where($followTableName . '.poster_type = ?', 'user')
                ->where($followTableName . '.poster_id = ?', $viewer_id)
                ->where($followTableName . '.resource_type = ?', 'sitestoreproduct_wishlist');            
        
      }
      elseif($search_wishlist == 'my_wishlists') {
        $select->where("$wishlistTableName.owner_id = ?", $viewer_id);
      }
      elseif($search_wishlist == 'friends_wishlists') {
        $friend_ids = $viewer->membership()->getMembershipsOfIds();
        if(empty($friend_ids)) {
          $select->where("$wishlistTableName.owner_id = ?", -1);
        }
        else {
          $select->where("$wishlistTableName.owner_id IN (?)", (array)$friend_ids);
        }
      }
    }
    
    if( isset($params['displayBy']) && !empty($params['displayBy']) && $params['displayBy'] != 'all' )
    {
      if( $params['displayBy'] == 'desc_creation' )
        $select->order('creation_date DESC');
      else if( $params['displayBy'] == 'desc_view' )
        $select->order('view_count DESC');
      else if( $params['displayBy'] == 'alphabetically' )
        $select->order('title');
      else if( $params['displayBy'] == 'featured_wishlist' )
        $select->order('featured DESC')->order('follow_count DESC')->order('creation_date DESC');
    }

    if (!empty($params['orderby']) && !empty($params['orderby'])) {
      $select->order($params['orderby'] . ' DESC');
    }

    if (isset($params['owner_ids']) && !empty($params['owner_ids'])) {
      $select->where($wishlistTableName . '.owner_id in (?)', $params['owner_ids']);
    }

    if (!empty($params['limit']) && !empty($params['limit'])) {
      $select->limit($params['limit']);
    }

    $select->order($wishlistTableName . '.wishlist_id DESC')->group($wishlistTableName . '.wishlist_id');

    //GET PAGINATOR
    if (isset($params['pagination']) && !empty($params['pagination'])) {
      return Zend_Paginator::factory($select);
    } else {
      return $this->fetchAll($select);
    }
  }

  public function getRecentWishlistId($owner_id) {

    $max_wishlist_id = $this->select()
            ->from($this->info('name'), array("MAX(wishlist_id) AS max_wishlist_id"))
            ->where('owner_id = ?', $owner_id)
            ->query()
            ->fetchColumn();

    if (!empty($max_wishlist_id)) {
      return $max_wishlist_id;
    }

    return 0;
  }

  public function getWishlistCount() {

    $total_wishlists = $this->select()
            ->from($this->info('name'), array("Count(wishlist_id) AS total_wishlists"))
            ->query()
            ->fetchColumn();

    return $total_wishlists;
  }

}