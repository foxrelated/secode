<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroupalbum_Plugin_Sitemobile {
  
  protected $_pagesTable;
  protected $_contentTable;
  
  public function onIntegrated() {
    
    $this->_pagesTable =  Engine_Api::_()->getApi('modules', 'sitemobile')->_pagesTable;
    $this->_contentTable =  Engine_Api::_()->getApi('modules', 'sitemobile')->_contentTable;
    //Group Album
    $this->addSitegroupAlbumProfileContent();
    $this->addSitegroupAlbumCreateGroup();
    $this->addSitegroupAlbumBrowseGroup();
    $this->addSitegroupAlbumViewGroup();
    $this->addSitegroupAlbumPhotoViewGroup();
    include APPLICATION_PATH . "/application/modules/Sitegroupalbum/controllers/mobileLayoutCreation.php";
  }
  
  //Site group Albums 
  public function addSitegroupAlbumProfileContent() {
    // install content areas
    $db = Engine_Db_Table::getDefaultAdapter();

    $group_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitegroup_index_view');


    if(empty($group_id)) 
      return false;

    // album.profile-albums
    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
            ->from($this->_contentTable)
            ->where('page_id = ?', $group_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitegroup.sitemobile-photos-sitegroup')
    ;

    $info = $select->query()->fetch();

    if (empty($info)) {
      // container_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
              ->from($this->_contentTable)
              ->where('page_id = ?', $group_id)
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
              ->where('page_id = ?', $group_id)
              ->limit(1);
      $tab_id = $select->query()->fetchObject();
      if ($tab_id && @$tab_id->content_id) {
        $tab_id = $tab_id->content_id;
      } else {
        $tab_id = null;
      }

      // tab on profile
      $db->insert($this->_contentTable, array(
          'page_id' => $group_id,
          'type' => 'widget',
          'name' => 'sitegroup.sitemobile-photos-sitegroup',
          'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
          'order' => 900,
          'params' => '{"title":"Photos","titleCount":true}',
      ));

      return $this;
    }
  }

  public function addSitegroupAlbumCreateGroup() {

    $db = Engine_Db_Table::getDefaultAdapter();

    $group_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitegroup_photo_upload-album');
    if (!$group_id) {

      // Insert group
      $db->insert($this->_pagesTable, array(
          'name' => 'sitegroup_photo_upload-album',
          'displayname' => 'Groups / Communities - Create Album',
          'title' => 'Create new Album',
          'description' => 'This is album create page.',
          'custom' => 0,
      ));
      $group_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $group_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $group_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $group_id,
          'parent_content_id' => $main_middle_id,
          'order' => 2,
      ));
    }
  }

  public function addSitegroupAlbumBrowseGroup() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $group_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitegroup_album_browse');
    // insert if it doesn't exist yet
    if (!$group_id) {
      // Insert group
      $db->insert($this->_pagesTable, array(
          'name' => 'sitegroup_album_browse',
          'displayname' => 'Groups / Communities - Browse Albums',
          'title' => 'Browse Albums',
          'description' => 'This is browse albums page.',
          'custom' => 0,
      ));
      $group_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $group_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $group_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $group_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
      ));
      // Insert Advance search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $group_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 2,
      ));
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitegroupalbum.sitegroup-album',
          'page_id' => $group_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"itemCount":"10"}',
          'order' => 3,
      ));
    }
  }

  public function addSitegroupAlbumViewGroup() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $group_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitegroup_album_view');
    // insert if it doesn't exist yet
    if (!$group_id) {
      // Insert group
      $db->insert($this->_pagesTable, array(
          'name' => 'sitegroup_album_view',
          'displayname' => 'Groups / Communities - Album View Page',
          'title' => 'Album View Page',
          'description' => 'This is album view page.',
          'custom' => 0,
      ));
      $group_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $group_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $group_id,
          'parent_content_id' => $main_id,
          'order' => 2,
      ));
      $middle_id = $db->lastInsertId();

//      $db->insert($this->_contentTable, array(
//          'page_id' => $group_id,
//          'type' => 'widget',
//          'name' => 'sitemobile.sitemobile-headingtitle',
//          'parent_content_id' => $middle_id,
//          'order' => 1,
//          'params' => '{"title":"","nonloggedin":"1","loggedin":"1","nomobile":"0","notablet":"0","name":"sitemobile.sitemobile-headingtitle"}',
//          'module' => 'sitemobile'
//      ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitegroupalbum.album-content',
          'page_id' => $group_id,
          'parent_content_id' => $middle_id,
          'order' => 1,
      ));
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.comments',
          'page_id' => $group_id,
          'parent_content_id' => $middle_id,
          'order' => 2,
      ));
    }
  }

  public function addSitegroupAlbumPhotoViewGroup() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $group_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitegroup_photo_view');

    // profile group
    if (!$group_id) {
      // Insert group
      $db->insert($this->_pagesTable, array(
          'name' => 'sitegroup_photo_view',
          'displayname' => 'Groups / Communities - Album Photo View Page',
          'title' => 'Album Photo View Page',
          'description' => 'This is album photo view page.',
          'provides' => 'subject=sitegroup_photo',
          'custom' => 0,
      ));
      $group_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $group_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $group_id,
          'parent_content_id' => $main_id,
          'order' => 2,
      ));
      $middle_id = $db->lastInsertId();

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $group_id,
          'parent_content_id' => $middle_id,
          'order' => 1,
      ));
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.comments',
          'page_id' => $group_id,
          'parent_content_id' => $middle_id,
          'order' => 2,
      ));
    }

    return $this;
  }
  
 }