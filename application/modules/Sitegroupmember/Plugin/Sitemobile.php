<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupmember
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroupmember_Plugin_Sitemobile {
  
  protected $_pagesTable;
  protected $_contentTable;
  
  public function onIntegrated() {
    
    $this->_pagesTable =  Engine_Api::_()->getApi('modules', 'sitemobile')->_pagesTable;
    $this->_contentTable =  Engine_Api::_()->getApi('modules', 'sitemobile')->_contentTable;
    //GROUP MEMBER
    $this->addSitegroupMemberProfileContent();
    $this->addSitegroupMemberBrowseGroup();
    $this->addSitegroupmemberGroups();
    include APPLICATION_PATH . "/application/modules/Sitegroupmember/controllers/mobileLayoutCreation.php";
  }
  
  public function addSitegroupmemberGroups() {
    $this->setDefaultWidgetForSitegroupmember('content', 'groups');
    $this->setDefaultWidgetForSitegroupmember('tabletcontent', 'tabletgroups');
  }

  public function setDefaultWidgetForSitegroupmember($content, $groups) {
    // install content areas

    $db = Engine_Db_Table::getDefaultAdapter();
    $select = new Zend_Db_Select($db);

    // profile group
    $select
            ->from($this->_pagesTable)
            ->where('name = ?', 'user_profile_index')
            ->limit(1);
    $group_id = $select->query()->fetchObject()->page_id;


    // sitemobile.blog-profile-blogs
    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
            ->from($this->_contentTable)
            ->where('page_id = ?', $group_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitegroup.profile-joined-sitegroup')
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
							->where('page_id = ?', $group_id)
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
          'name' => 'sitegroup.profile-joined-sitegroup',
          'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
          'order' => 10,
          'params' => '{"title":"Joined / Owned Groups","titleCount":true, "groupAdminJoined":"2","textShow":"Verified","showMemberText":"1","category_id":"0"}',
          'module' => 'sitegroupmember'
      ));
    }
  }

  public function addSitegroupMemberProfileContent() {
    //install content areas
    $db = Engine_Db_Table::getDefaultAdapter();
    $group_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitegroup_index_view');

    if ($group_id) {
      //sitemobile.blog-profile-blogs
      //Check if it's already been placed
      $select = new Zend_Db_Select($db);
      $select
              ->from($this->_contentTable)
              ->where('page_id = ?', $group_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitegroupmember.sitemobile-profile-sitegroupmembers')
      ;
      $info = $select->query()->fetch();

      if (empty($info)) {

        //container_id (will always be there)
        $select = new Zend_Db_Select($db);
        $select
                ->from($this->_contentTable)
                ->where('page_id = ?', $group_id)
                ->where('type = ?', 'container')
                ->limit(1);
        $container_id = $select->query()->fetchObject()->content_id;

        //middle_id (will always be there)
        $select = new Zend_Db_Select($db);
        $select
                ->from($this->_contentTable)
                ->where('parent_content_id = ?', $container_id)
                ->where('page_id = ?', $group_id)
                ->where('type = ?', 'container')
                ->where('name = ?', 'middle')
                ->limit(1);
        $middle_id = $select->query()->fetchObject()->content_id;

        //tab_id (tab container) may not always be there
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

        //tab on profile
        $db->insert($this->_contentTable, array(
            'page_id' => $group_id,
            'type' => 'widget',
            'name' => 'sitegroupmember.profile-sitegroupmembers-announcements',
            'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
            'order' => 400,
            'params' => '{"title":"Announcements","titleCount":true}',
        ));

        //tab on profile
        $db->insert($this->_contentTable, array(
            'page_id' => $group_id,
            'type' => 'widget',
            'name' => 'sitegroupmember.sitemobile-profile-sitegroupmembers',
            'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
            'order' => 500,
            'params' => '{"title":"Members","titleCount":true}',
        ));
      }
    }
  }

  public function addSitegroupMemberBrowseGroup() {

    $db = Engine_Db_Table::getDefaultAdapter();

    $group_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitegroupmember_index_browse');
    //insert if it doesn't exist yet
    if (!$group_id) {
      //Insert group
      $db->insert($this->_pagesTable, array(
          'name' => 'sitegroupmember_index_browse',
          'displayname' => 'Groups / Communities - Browse Members',
          'title' => 'Browse Members',
          'description' => 'This is member browse page.',
          'custom' => 0,
      ));
      $group_id = $db->lastInsertId();

      //Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $group_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      //Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $group_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $group_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
      ));

      //Insert Advance search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $group_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 2,
      ));
      //Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitegroupmember.sitegroup-member',
          'page_id' => $group_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"itemCount":"10"}',
          'order' => 3,
      ));
    }
  }

}