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
class Sitegroup_Plugin_Sitemobile {

  protected $_pagesTable;
  protected $_contentTable;

  
  public function onIntegrated() {

    $this->_pagesTable = Engine_Api::_()->getApi('modules', 'sitemobile')->_pagesTable;
    $this->_contentTable = Engine_Api::_()->getApi('modules', 'sitemobile')->_contentTable;
    //Group Plugin Main
    $this->addSitegroupCreateGroup();
    $this->addSitegroupHomeGroup();
    $this->addSitegroupBrowseGroup();
    $this->addSitegroupProfileGroup();
    $this->addSitegroupManageGroup();
    $this->addSitegroupManageAdminGroup();
    $this->addSitegroupManageLikeGroup();
    $this->addSitegroupManageJoinedGroup();
    $this->addSitegroupGroups();
    include APPLICATION_PATH . "/application/modules/Sitegroup/controllers/license/mobileLayoutCreation.php";
  }

  //Group plugin main groups
  public function addSitegroupProfileGroup() {
    $db = Engine_Db_Table::getDefaultAdapter();
    $select = new Zend_Db_Select($db);

    // Check if it's already been placed
    $select
            ->from($this->_pagesTable)
            ->where('name = ?', 'sitegroup_index_view')
            ->limit(1);

    $info = $select->query()->fetch();

    if (empty($info)) {
      $db->insert($this->_pagesTable, array(
          'name' => 'sitegroup_index_view',
          'displayname' => 'Groups / Communities - Group Profile',
          'title' => 'Group Profile',
          'description' => 'This is a group profile page.',
          'custom' => 0,
      ));
      $group_id = $db->lastInsertId($this->_pagesTable);

      // containers
      $db->insert($this->_contentTable, array(
          'page_id' => $group_id,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => null,
          'order' => 2,
          'params' => '',
      ));
      $container_id = $db->lastInsertId($this->_contentTable);

      $db->insert($this->_contentTable, array(
          'page_id' => $group_id,
          'type' => 'container',
          'name' => 'middle',
          'parent_content_id' => $container_id,
          'order' => 2,
          'params' => '',
      ));
      $middle_id = $db->lastInsertId($this->_contentTable);

			$db->insert($this->_contentTable, array(
					'page_id' => $group_id,
					'type' => 'widget',
					'name' => 'sitegroup.closegroup-sitegroup',
					'parent_content_id' => $middle_id,
					'order' => 1,
			));

			$db->insert($this->_contentTable, array(
					'page_id' => $group_id,
					'type' => 'widget',
					'name' => 'sitegroup.sitemobile-groupcover-photo-information',
					'parent_content_id' => $middle_id,
					'order' => 2,
					'params' => '{"title":"","titleCount":true,"showContent":["mainPhoto","title","sponsored","featured","category","subcategory","subsubcategory","likeButton","followButton","description","phone","email","website","location","tags","price"],"strachPhoto":"0"}',
			));

      $db->insert($this->_contentTable, array(
          'page_id' => $group_id,
          'type' => 'widget',
          'name' => 'sitemobile.container-tabs-columns',
          'parent_content_id' => $middle_id,
          'order' => 5,
          'params' => '{"max":6}',
      ));
      $tab_id = $db->lastInsertId($this->_contentTable);

      $db->insert($this->_contentTable, array(
          'page_id' => $group_id,
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advfeed',
          'parent_content_id' => $tab_id,
          'order' => 100,
          'params' => '{"title":"Updates"}',
      ));
      $db->insert($this->_contentTable, array(
          'page_id' => $group_id,
          'type' => 'widget',
          'name' => 'sitegroup.sitemobile-info-sitegroup',
          'parent_content_id' => $tab_id,
          'order' => 200,
          'params' => '{"title":"Info"}',
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $group_id,
          'type' => 'widget',
          'name' => 'sitegroup.sitemobile-overview-sitegroup',
          'parent_content_id' => $tab_id,
          'order' => 300,
          'params' => '{"title":"Overview","titleCount":true}',
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $group_id,
          'type' => 'widget',
          'name' => 'sitegroup.sitemobile-location-sitegroup',
          'parent_content_id' => $tab_id,
          'order' => 400,
          'params' => '{"title":"Map","titleCount":true}',
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $group_id,
          'type' => 'widget',
          'name' => 'seaocore.sitemobile-people-like',
          'parent_content_id' => $tab_id,
          'order' => 3000,
          'params' => '{"title":"Member Likes","titleCount":true}',
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $group_id,
          'type' => 'widget',
          'name' => 'seaocore.sitemobile-followers',
          'parent_content_id' => $tab_id,
          'order' => 3100,
          'params' => '{"title":"Followers","titleCount":true}',
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $group_id,
          'type' => 'widget',
          'name' => 'sitegroup.featuredowner-sitegroup',
          'parent_content_id' => $tab_id,
          'order' => 3200,
          'params' => '{"title":"Group Admins","titleCount":true}',
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $group_id,
          'type' => 'widget',
          'name' => 'sitegroup.favourite-group',
          'parent_content_id' => $tab_id,
          'order' => 3300,
          'params' => '{"title":"Linked Groups","titleCount":true}',
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $group_id,
          'type' => 'widget',
          'name' => 'sitegroup.subgroup-sitegroup',
          'parent_content_id' => $tab_id,
          'order' => 3400,
          'params' => '{"title":"Sub Groups of a Group","titleCount":true}',
      ));

      //tab on profile
      $db->insert($this->_contentTable, array(
          'page_id' => $group_id,
          'type' => 'widget',
          'name' => 'sitemobile.profile-links',
          'parent_content_id' => $tab_id,
          'order' => 3500,
          'params' => '{"title":"Links","titleCount":true}',
      ));
    }
  }

  public function addSitegroupCreateGroup() {

    $db = Engine_Db_Table::getDefaultAdapter();

    $group_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitegroup_index_create');
    if (!$group_id) {

      // Insert group
      $db->insert($this->_pagesTable, array(
          'name' => 'sitegroup_index_create',
          'displayname' => 'Groups / Communities - Create Group',
          'title' => 'Create new Group',
          'description' => 'This is group create page.',
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

  public function addSitegroupHomeGroup() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $group_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitegroup_index_home');
    // insert if it doesn't exist yet
    if (!$group_id) {
      // Insert group
      $db->insert($this->_pagesTable, array(
          'name' => 'sitegroup_index_home',
          'displayname' => 'Groups / Communities - Groups Home',
          'title' => 'Groups Home',
          'description' => 'This is group home page.',
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
      //Insert search
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
          'name' => 'sitegroup.categories-sitegroup',
          'page_id' => $group_id,
          'params' => '{"title":"Categories","nomobile":"0","name":"sitegroup.categories-sitegroup"}',
          'parent_content_id' => $main_middle_id,
          'order' => 3,
      ));
            $db->insert($this->_contentTable, array(
                'page_id' => $group_id,
                'type' => 'widget',
                'name' => 'sitemobile.container-tabs-columns',
                'parent_content_id' => $main_middle_id,
                'order' => 5,
                'params' => '{"max":6}',
                'module' => 'sitemobile'
            ));
            $tab_id = $db->lastInsertId($this->_contentTable);
            
            if($this->_contentTable == 'engine4_sitemobileapp_content' || $this->_contentTable == 'engine4_sitemobileapp_tablet_content')   {  
              $viewType = "gridview";
              $layout_views = '["2"]';
              $content_display = '["featured","sponsored","date","owner","likeCount","followCount","memberCount","reviewCount","commentCount","viewCount","location"]';
            }else{
              $viewType = "listview";
              $layout_views = '["1","2"]';
              $content_display = '["ratings","date","owner","likeCount","followCount","memberCount","reviewCount","commentCount","viewCount","location","price"]';
            }
            // Insert content
            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'sitegroup.sitemobile-popular-groups',
                'page_id' => $group_id,
                'parent_content_id' => $tab_id,
                'order' => 1,
                'module' => 'sitegroup',
                'params' => '{"title":"Recently Posted","titleCount":true,"columnHeight":"260","category_id":"0","content_display":'.$content_display.',"name":"sitegroup.sitemobile-popular-groups","popularity":"Recently Posted","itemCount":"5","truncation":"16", "layouts_views":'.$layout_views.',"viewType":"'.$viewType.'"}',
            ));
            
            // Insert content
            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'sitegroup.sitemobile-popular-groups',
                'page_id' => $group_id,
                'parent_content_id' => $tab_id,
                'order' => 2,
                'module' => 'sitegroup',
                'params' => '{"title":"Most Viewed","titleCount":true,"columnHeight":"260","category_id":"0","content_display":'.$content_display.',"name":"sitegroup.sitemobile-popular-groups","popularity":"Most Viewed","itemCount":"5","truncation":"16", "layouts_views":'.$layout_views.',"viewType":"'.$viewType.'"}',                
            ));
            
            // Insert content
            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'sitegroup.sitemobile-popular-groups',
                'page_id' => $group_id,
                'parent_content_id' => $tab_id,
                'order' => 3,
                'module' => 'sitegroup',
                'params' => '{"title":"Featured","titleCount":true,"columnHeight":"260","category_id":"0","content_display":'.$content_display.',"name":"sitegroup.sitemobile-popular-groups","popularity":"Featured","itemCount":"5","truncation":"16","layouts_views":'.$layout_views.',"viewType":"'.$viewType.'"}',
                 ));
            
            // Insert content
            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'sitegroup.sitemobile-popular-groups',
                'page_id' => $group_id,
                'parent_content_id' => $tab_id,
                'order' => 4,
                'module' => 'sitegroup',
                'params' => '{"title":"Sponsored","titleCount":true,"columnHeight":"260","category_id":"0","content_display":'.$content_display.',"name":"sitegroup.sitemobile-popular-groups","popularity":"Sponsored",
                "itemCount":"5","truncation":"16","layouts_views":'.$layout_views.',"viewType":"'.$viewType.'"}',
                 ));
            
            $sitegroupmemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
            if ($sitegroupmemberEnabled) {
            // Insert content
            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'sitegroup.sitemobile-popular-groups',
                'page_id' => $group_id,
                'parent_content_id' => $tab_id,
                'order' => 5,
                'module' => 'sitegroup',
                'params' => '{"title":"Most Joined","titleCount":true,"columnHeight":"260","category_id":"0","content_display":'.$content_display.',"name":"sitegroup.sitemobile-popular-groups","popularity":"Most Joined",
                "itemCount":"5","truncation":"16","layouts_views":'.$layout_views.',"viewType":"'.$viewType.'"}',
                ));
            }
    }
  }

  public function addSitegroupBrowseGroup() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $group_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitegroup_index_index');
    // insert if it doesn't exist yet
    if (!$group_id) {
      // Insert group
      $db->insert($this->_pagesTable, array(
          'name' => 'sitegroup_index_index',
          'displayname' => 'Groups / Communities - Browse Groups',
          'title' => 'Browse Groups',
          'description' => 'This is group browse page.',
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
      //Insert search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $group_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 2,
      ));
      //WE WILL NOT ADD THE ALPHABETICAL WIDGET TAB ON APP AND TABLET APP.
     if($this->_pagesTable == 'engine4_sitemobile_pages' || $this->_pagesTable == 'engine4_sitemobile_tablet_pages')  {
        // Insert Alphabetic Filtering
        $db->insert($this->_contentTable, array(
            'type' => 'widget',
            'name' => 'sitegroup.alphabeticsearch-sitegroup',
            'page_id' => $group_id,
            'parent_content_id' => $main_middle_id,
            'order' => 2,
        ));
     }
 
       
          if($this->_contentTable == 'engine4_sitemobileapp_content' || $this->_contentTable == 'engine4_sitemobileapp_tablet_content') {
            //App Parameters for group listing
            $params = '{"title":"","titleCount":true,"layouts_views":["2"],"view_selected":"grid","layouts_oder":"2","columnHeight":"260","category_id":"0","content_display":["featured","sponsored","date","owner","likeCount","followCount","memberCount","reviewCount","location"],"name":"sitegroup.sitemobile-groups-sitegroup"}';        
          }else{
            //Mobile Browser Parameters for group listing
            $params = '{"title":"","titleCount":true,"layouts_views":["1","2"],"view_selected":"list","layouts_oder":"2","columnHeight":"260","category_id":"0","content_display":["ratings","likeCount","followCount","memberCount","reviewCount","location","price"],"name":"sitegroup.sitemobile-groups-sitegroup"}';   
          }
          
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitegroup.sitemobile-groups-sitegroup',
          'page_id' => $group_id,
          'parent_content_id' => $main_middle_id,
          'params' => $params,
          'order' => 3,
      ));
    }
  }

  public function addSitegroupManageGroup() {

    $db = Engine_Db_Table::getDefaultAdapter();

    // profile group
    $group_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitegroup_index_manage');
    // insert if it doesn't exist yet
    if (!$group_id) {
      // Insert group
      $db->insert($this->_pagesTable, array(
          'name' => 'sitegroup_index_manage',
          'displayname' => 'Groups / Communities - Manage Groups',
          'title' => 'My Groups',
          'description' => 'This page lists a user\'s Groups\'s.',
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
      //Insert search
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
          'name' => 'core.content',
          'page_id' => $group_id,
          'parent_content_id' => $main_middle_id,
          'order' => 3,
      ));
    }

    return $this;
  }

  //Groups i admin
  public function addSitegroupManageAdminGroup() {

    $db = Engine_Db_Table::getDefaultAdapter();

    // profile group
    $group_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitegroup_manageadmin_my-groups');
    // insert if it doesn't exist yet
    if (!$group_id) {
      // Insert group
      $db->insert($this->_pagesTable, array(
          'name' => 'sitegroup_manageadmin_my-groups',
          'displayname' => 'Groups / Communities - Manage Group (Groups I Admin)',
          'title' => 'Groups I Admin',
          'description' => 'This page lists a user\'s Groups\'s of which user\'s is admin.',
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

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $group_id,
          'parent_content_id' => $main_middle_id,
          'order' => 3,
      ));
    }

    return $this;
  }

  //Groups i like
  public function addSitegroupManageLikeGroup() {

    $db = Engine_Db_Table::getDefaultAdapter();

    // profile group
    $group_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitegroup_like_mylikes');
    // insert if it doesn't exist yet
    if (!$group_id) {
      // Insert group
      $db->insert($this->_pagesTable, array(
          'name' => 'sitegroup_like_mylikes',
          'displayname' => 'Groups / Communities - Manage Group (Groups I Like)',
          'title' => 'Groups I Like',
          'description' => 'This page lists a user\'s Groups\'s which user\'s likes.',
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

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $group_id,
          'parent_content_id' => $main_middle_id,
          'order' => 3,
      ));
    }

    return $this;
  }

  //Groups i joined
  public function addSitegroupManageJoinedGroup() {

    $db = Engine_Db_Table::getDefaultAdapter();

    // profile group
    $group_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitegroup_like_my-joined');
    // insert if it doesn't exist yet
    if (!$group_id) {
      // Insert group
      $db->insert($this->_pagesTable, array(
          'name' => 'sitegroup_like_my-joined',
          'displayname' => 'Groups / Communities - Manage Group (Groups I\'ve Joined)',
          'title' => "Groups I've Joined",
          'description' => 'This page lists a user\'s Groups\'s which user\'s have joined.',
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

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $group_id,
          'parent_content_id' => $main_middle_id,
          'order' => 3,
      ));
    }

    return $this;
  }

  public function addSitegroupGroups() {

//    $this->setDefaultWidgetForSitegroup('content', 'pages'); //::- NOT ADD Widget on USER Profile Page at installation
//    $this->setDefaultWidgetForSitegroup('tabletcontent', 'tabletpages'); //::- NOT ADD Widget on USER Profile Page at installation
  }

  public function setDefaultWidgetForSitegroup($content, $groups) {
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
            ->where('name = ?', 'sitegroup.profile-sitegroup')
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
          'name' => 'sitegroup.profile-sitegroup',
          'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
          'order' => 11,
          'params' => '{"title":"Groups","titleCount":true}',
          'module' => 'sitegroup'
      ));
    }
  }

}
