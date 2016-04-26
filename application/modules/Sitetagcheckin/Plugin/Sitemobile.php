<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitetagcheckin_Plugin_Sitemobile {

  protected $_pagesTable;
  protected $_contentTable;
  
  public function onIntegrated() {
    
    $this->_pagesTable =  Engine_Api::_()->getApi('modules', 'sitemobile')->_pagesTable;
    $this->_contentTable =  Engine_Api::_()->getApi('modules', 'sitemobile')->_contentTable;
    $this->addSitetagcheckinPages();
    $this->addSitetagcheckinProfileContent();
  }

//   public function addSitetagcheckinProfileTabletContent() {
// 
//     // install content areas
//     $db = Engine_Db_Table::getDefaultAdapter();
//     $select = new Zend_Db_Select($db);
// 
//     // profile page
//     $select
//             ->from($this->_pagesTable)
//             ->where('name = ?', 'sitepage_index_view')
//             ->limit(1);
//     $page_id = $select->query()->fetchObject()->page_id;
// 
// 
//     // sitemobile.blog-profile-blogs
//     // Check if it's already been placed
//     $select = new Zend_Db_Select($db);
//     $select
//             ->from($this->_contentTable)
//             ->where('page_id = ?', $page_id)
//             ->where('type = ?', 'widget')
//             ->where('name = ?', 'sitetagcheckin.sitemobile-checkinuser-sitetagcheckin')
//     ;
//     $info = $select->query()->fetch();
// 
//     if (empty($info)) {
// 
//       // container_id (will always be there)
//       $select = new Zend_Db_Select($db);
//       $select
//               ->from($this->_contentTable)
//               ->where('page_id = ?', $page_id)
//               ->where('name = ?', 'main')
//               ->where('type = ?', 'container')
//               ->limit(1);
//       $container_id = $select->query()->fetchObject()->content_id;
// 
//       // middle_id (will always be there)
//       $select = new Zend_Db_Select($db);
//       $select
//               ->from($this->_contentTable)
//               ->where('parent_content_id = ?', $container_id)
//               ->where('type = ?', 'container')
//               ->where('name = ?', 'right')
//               ->limit(1);
//       $right_id = $select->query()->fetchObject()->content_id;
// 
//       // tab on profile
//       $db->insert($this->_contentTable, array(
//           'page_id' => $page_id,
//           'type' => 'widget',
//           'name' => 'sitetagcheckin.sitemobilecheckinbutton-sitetagcheckin',
//           'parent_content_id' => $right_id,
//           'order' => 20,
//           'params' => '{"title":"","titleCount":true}',
//       ));
// 
//       // tab on profile
//       $db->insert($this->_contentTable, array(
//           'page_id' => $page_id,
//           'type' => 'widget',
//           'name' => 'sitetagcheckin.sitemobile-checkinuser-sitetagcheckin',
//           'parent_content_id' => $right_id,
//           'order' => 21,
//           'params' => '{"title":"Checked-in Users","titleCount":true}',
//       ));
//     }
//   }

  public function addSitetagcheckinProfileContent() {

    // install content areas
    $db = Engine_Db_Table::getDefaultAdapter();
    $select = new Zend_Db_Select($db);

    // profile page
    $select
            ->from($this->_pagesTable)
            ->where('name = ?', 'sitepage_index_view')
            ->limit(1);
    $page_id = $select->query()->fetchObject()->page_id;

    if (empty($page_id))
     return;
     
    // sitemobile.blog-profile-blogs
    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
            ->from($this->_contentTable)
            ->where('page_id = ?', $page_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitetagcheckin.sitemobile-checkinuser-sitetagcheckin')
    ;
    $info = $select->query()->fetch();

    if (empty($info)) {

      // container_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
              ->from($this->_contentTable)
              ->where('page_id = ?', $page_id)
              ->where('type = ?', 'container')
              ->where('name =?', 'main')
              ->limit(1);
      $container_id = $select->query()->fetchObject()->content_id;

      // middle_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
              ->from($this->_contentTable)
              ->where('parent_content_id = ?', $container_id)
              ->where('type = ?', 'container')
              ->where('name = ?', 'middle')
              ->limit(1);
      $middle_id = $select->query()->fetchObject()->content_id;

      // tab on profile
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitetagcheckin.sitemobilecheckinbutton-sitetagcheckin',
          'parent_content_id' => $middle_id,
          'order' => 3,
          'params' => '{"title":"","titleCount":true}',
      ));
    }

    // sitemobile.blog-profile-blogs
    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
            ->from($this->_contentTable)
            ->where('page_id = ?', $page_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitetagcheckin.sitemobile-checkinuser-sitetagcheckin')
    ;
    $info = $select->query()->fetch();

    if (empty($info)) {

      // container_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
              ->from($this->_contentTable)
              ->where('page_id = ?', $page_id)
              ->where('type = ?', 'container')
              ->where('name = ?', 'main')
              ->limit(1);
      $container_id = $select->query()->fetchObject()->content_id;

      // middle_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
              ->from($this->_contentTable)
              ->where('parent_content_id = ?', $container_id)
              ->where('type = ?', 'container')
              ->where('name = ?', 'middle')
              ->limit(1);
      $middle_id = $select->query()->fetchObject()->content_id;

      // tab_id (tab container) may not always be there
      $select
              ->reset('where')
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitemobile.container-tabs-columns')
              ->where('page_id = ?', $page_id)
              ->limit(1);
      $tab_id = $select->query()->fetchObject();
      if ($tab_id && @$tab_id->content_id) {
        $tab_id = $tab_id->content_id;
      } else {
        $tab_id = null;
      }

      // tab on profile
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitetagcheckin.sitemobile-checkinuser-sitetagcheckin',
          'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
          'order' => 9999,
          'params' => '{"title":"Checked-in Users","titleCount":true}',
      ));
    }

    $select = new Zend_Db_Select($db);

    // profile page
    $select
            ->from($this->_pagesTable)
            ->where('name = ?', 'sitebusiness_index_view')
            ->limit(1);
    $business_id = $select->query()->fetchObject()->page_id;

    if (empty($business_id))
     return;
     
    // sitemobile.blog-profile-blogs
    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
            ->from($this->_contentTable)
            ->where('page_id = ?', $business_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitetagcheckin.sitemobile-checkinuser-sitetagcheckin')
    ;
    $info = $select->query()->fetch();

    if (empty($info)) {

      // container_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
              ->from($this->_contentTable)
              ->where('page_id = ?', $business_id)
              ->where('type = ?', 'container')
              ->where('name =?', 'main')
              ->limit(1);
      $container_id = $select->query()->fetchObject()->content_id;

      // middle_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
              ->from($this->_contentTable)
              ->where('parent_content_id = ?', $container_id)
              ->where('type = ?', 'container')
              ->where('name = ?', 'middle')
              ->limit(1);
      $middle_id = $select->query()->fetchObject()->content_id;

      // tab on profile
      $db->insert($this->_contentTable, array(
          'page_id' => $business_id,
          'type' => 'widget',
          'name' => 'sitetagcheckin.sitemobilecheckinbutton-sitetagcheckin',
          'parent_content_id' => $middle_id,
          'order' => 3,
          'params' => '{"title":"","titleCount":true}',
      ));
    }

    // sitemobile.blog-profile-blogs
    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
            ->from($this->_contentTable)
            ->where('page_id = ?', $business_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitetagcheckin.sitemobile-checkinuser-sitetagcheckin')
    ;
    $info = $select->query()->fetch();

    if (empty($info)) {

      // container_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
              ->from($this->_contentTable)
              ->where('page_id = ?', $business_id)
              ->where('type = ?', 'container')
              ->where('name = ?', 'main')
              ->limit(1);
      $container_id = $select->query()->fetchObject()->content_id;

      // middle_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
              ->from($this->_contentTable)
              ->where('parent_content_id = ?', $container_id)
              ->where('type = ?', 'container')
              ->where('name = ?', 'middle')
              ->limit(1);
      $middle_id = $select->query()->fetchObject()->content_id;

      // tab_id (tab container) may not always be there
      $select
              ->reset('where')
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitemobile.container-tabs-columns')
              ->where('page_id = ?', $business_id)
              ->limit(1);
      $tab_id = $select->query()->fetchObject();
      if ($tab_id && @$tab_id->content_id) {
        $tab_id = $tab_id->content_id;
      } else {
        $tab_id = null;
      }

      // tab on profile
      $db->insert($this->_contentTable, array(
          'page_id' => $business_id,
          'type' => 'widget',
          'name' => 'sitetagcheckin.sitemobile-checkinuser-sitetagcheckin',
          'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
          'order' => 9999,
          'params' => '{"title":"Checked-in Users","titleCount":true}',
      ));
    }
  }

  public function addSitetagcheckinPages() {
    $this->setDefaultWidgetForSitetagcheckin('content', 'pages');
    $this->setDefaultWidgetForSitetagcheckin('tabletcontent', 'tabletpages');
  }

  public function setDefaultWidgetForSitetagcheckin($content, $pages) {
    // install content areas

    $db = Engine_Db_Table::getDefaultAdapter();
    $select = new Zend_Db_Select($db);

    // profile page
    $select
            ->from($this->_pagesTable)
            ->where('name = ?', 'user_profile_index')
            ->limit(1);
    $page_id = $select->query()->fetchObject()->page_id;


    // sitemobile.blog-profile-blogs
    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
            ->from($this->_contentTable)
            ->where('page_id = ?', $page_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitetagcheckin.sitemobile-map-sitetagcheckin')
    ;
    $info = $select->query()->fetch();

    if (empty($info)) {

      // container_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
              ->from($this->_contentTable)
              ->where('page_id = ?', $page_id)
              ->where('type = ?', 'container')
              ->limit(1);
      $container_id = $select->query()->fetchObject()->content_id;

      // middle_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
              ->from($this->_contentTable)
              ->where('parent_content_id = ?', $container_id)
              ->where('type = ?', 'container')
              ->where('name = ?', 'middle')
              ->limit(1);
      $middle_id = $select->query()->fetchObject()->content_id;

      // tab_id (tab container) may not always be there
      $select
              ->reset('where')
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitemobile.container-tabs-columns')
              ->where('page_id = ?', $page_id)
              ->limit(1);
      $tab_id = $select->query()->fetchObject();
      if ($tab_id && @$tab_id->content_id) {
        $tab_id = $tab_id->content_id;
      } else {
        $tab_id = null;
      }

      // tab on profile
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitetagcheckin.sitemobile-map-sitetagcheckin',
          'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
          'order' => 10,
          'params' => '{"title":"Map","titleCount":true}',
          'module' => 'sitetagcheckin'
      ));
    }
  }

}