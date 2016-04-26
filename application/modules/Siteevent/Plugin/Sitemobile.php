<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Plugin_Sitemobile extends Zend_Controller_Plugin_Abstract {

  protected $_pagesTable;
  protected $_contentTable;

  public function onIntegrated($pageTable, $contentTable) {
    $this->_pagesTable = $pageTable;
    $this->_contentTable = $contentTable;


    $this->addEventHomePage();
    $this->addEventBrowsePage();
    $this->addEventProfilePage();
    $this->addEventManagePage();
    $this->addCategroiesPage();
    $this->addEventCalendarPage();

    //Extra pages
    $this->addBrowseReviewPage();
    $this->addBrowseDiaryPage();
    $this->addDiaryProfilePage();
    $this->addReviewProfilePage();
    $this->addDiscussionTopicViewPage();
    $this->addEditorProfilePage();
    $this->addVideoViewPage();
    $this->addmemberProfilePageWidgets();  
    $this->addHostProfilePage();
    
    //Integrated module event widget add        
    $this->addIntegratedEventModule();
  }

  public function addIntegratedEventModule(){
    
      $integratedModArray = Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1));
      
      if(!empty($integratedModArray)){
      foreach($integratedModArray as $module){
        if ($module['item_module'] == 'sitereview') {
          $review_module_name = explode('_', $module['item_type']);       
          $pagename = "sitereview_index_view_listtype_".$review_module_name['2'];
        } else {
          $pagename = $module['item_module'] . "_index_view";
        }
        $this->addIntegratedModuleProfileWidget($pagename);
      }
      }
  }

  public function addEventHomePage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('siteevent_index_home');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'siteevent_index_home',
          'displayname' => 'Advanced Events - Events Home',
          'title' => 'Events Home',
          'description' => 'This is the events home page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
      ));

      //Insert search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"search":"2","title":"","location":1,"nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 3,
      ));
      
      if($this->_pagesTable == 'engine4_sitemobileapp_pages' || $this->_pagesTable == 'engine4_sitemobile_tabletapp_pages')  {
        // Insert content
        $db->insert($this->_contentTable, array(
            'type' => 'widget',
            'name' => 'siteevent.categories-home',
            'page_id' => $page_id,
            'parent_content_id' => $main_middle_id,
            'order' => 3,
            'params' => '{"eventtype_id":"-1"}',
        ));
      }
      
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.container-tabs-columns',
          'parent_content_id' => $main_middle_id,
          'order' => 5,
          'params' => '{"max":6}',
          'module' => 'sitemobile'
      ));
      $tab_id = $db->lastInsertId($this->_contentTable);

      $viewType = "gridview";
      if($this->_contentTable == 'engine4_sitemobileapp_content' || $this->_contentTable == 'engine4_sitemobileapp_tablet_content')   {  
        $layout_views = '["2"]';       
      }else{
        $layout_views = '["1", "2"]';
      }
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'siteevent.recently-popular-random-siteevent',
          'page_id' => $page_id,
          'parent_content_id' => $tab_id,
          'order' => 2,
          'module' => 'siteevent',
          'params' => '{"title":"Upcoming","titleCount":true,"detactLocation":"1","ajaxTabs":"upcoming","viewType":"'.$viewType.'","columnWidth":"215","contentType":"0","fea_spo":"","showEventType":"upcoming","columnHeight":"320","popularity":"event_id","interval":"overall","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventInfo":["hostName","startDate","location"],"itemCount":"10","truncationLocation":"40","truncation":"30","ratingType":"rating_avg","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.recently-popular-random-siteevent","layouts_views":'.$layout_views.'}',
      ));
      
       // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'siteevent.recently-popular-random-siteevent',
          'page_id' => $page_id,
          'parent_content_id' => $tab_id,
          'order' => 6,
          'module' => 'siteevent',
          'params' => '{"title":"This Month","titleCount":true,"detactLocation":"1","ajaxTabs":"thisZZZmonth","viewType":"'.$viewType.'","columnWidth":"215","contentType":"0","fea_spo":"","showEventType":"upcoming","columnHeight":"320","popularity":"event_id","interval":"overall","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventInfo":["hostName","startDate","location"],"itemCount":"10","truncationLocation":"40","truncation":"30","ratingType":"rating_avg","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.recently-popular-random-siteevent","layouts_views":'.$layout_views.'}',
      ));
      
       // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'siteevent.recently-popular-random-siteevent',
          'page_id' => $page_id,
          'parent_content_id' => $tab_id,
          'order' => 4,
          'module' => 'siteevent',
          'params' => '{"title":"This Week","titleCount":true,"detactLocation":"1","ajaxTabs":"thisZZZweek","viewType":"'.$viewType.'","columnWidth":"215","contentType":"0","fea_spo":"","showEventType":"upcoming","columnHeight":"320","popularity":"event_id","interval":"overall","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventInfo":["hostName","startDate","location"],"itemCount":"10","truncationLocation":"40","truncation":"30","ratingType":"rating_avg","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.recently-popular-random-siteevent","layouts_views":'.$layout_views.'}',
      ));
      
       // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'siteevent.recently-popular-random-siteevent',
          'page_id' => $page_id,
          'parent_content_id' => $tab_id,
          'order' => 5,
          'module' => 'siteevent',
          'params' => '{"title":"This Weekend","titleCount":true,"detactLocation":"1","ajaxTabs":"thisZZZweekend","viewType":"'.$viewType.'","columnWidth":"215","contentType":"0","fea_spo":"","showEventType":"upcoming","columnHeight":"320","popularity":"event_id","interval":"overall","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventInfo":["hostName","startDate","location"],"itemCount":"10","truncationLocation":"40","truncation":"30","ratingType":"rating_avg","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.recently-popular-random-siteevent","layouts_views":'.$layout_views.'}',
      ));
      
       // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'siteevent.recently-popular-random-siteevent',
          'page_id' => $page_id,
          'parent_content_id' => $tab_id,
          'order' => 3,
          'module' => 'siteevent',
          'params' => '{"title":"Today","titleCount":true,"detactLocation":"1","ajaxTabs":"today","viewType":"'.$viewType.'","columnWidth":"215","contentType":"0","fea_spo":"","showEventType":"upcoming","columnHeight":"320","popularity":"event_id","interval":"overall","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventInfo":["hostName","startDate","location"],"itemCount":"10","truncationLocation":"40","truncation":"30","ratingType":"rating_avg","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.recently-popular-random-siteevent","layouts_views":'.$layout_views.'}',
      ));
    }
  }

  public function addEventBrowsePage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('siteevent_index_index');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'siteevent_index_index',
          'displayname' => 'Advanced Events - Browse Events',
          'title' => 'Browse Events',
          'description' => 'This is the event browse page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
      ));
      //Insert search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 2,
      ));
      
      if($this->_contentTable == 'engine4_sitemobileapp_content' || $this->_contentTable == 'engine4_sitemobileapp_tablet_content') {         
        $layout_views = '["2"]';       
      }else{
        $layout_views = '["1","2"]';
          }
          
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'siteevent.browse-events-siteevent',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"title":"","titleCount":true,"layouts_views":'.$layout_views.',"layouts_order":"2","columnWidth":"199","truncationGrid":"30","detactLocation":"1","contentType":"All","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","columnHeight":"320","eventInfo":["hostName","startDate","location"],"orderby":"starttime","itemCount":"10","truncation":"30","ratingType":"rating_both","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.browse-events-siteevent"}',
          'order' => 3,
      ));
    }
  }

  public function addEventProfilePage() {

//GET DATABASE
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

    $page_id = $db->select()
            ->from($this->_pagesTable, 'page_id')
            ->where('name = ?', "siteevent_index_view")
            ->query()
            ->fetchColumn();

    if (empty($page_id)) {

      $containerCount = 0;
      $widgetCount = 0;

      $db->insert($this->_pagesTable, array(
          'name' => "siteevent_index_view",
          'displayname' => 'Advanced Events - Event Profile',
          'title' => '',
          'description' => 'This is Event profile page.',
          'custom' => 0
      ));
      $page_id = $db->lastInsertId($this->_pagesTable);

//MAIN CONTAINER
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'main',
          'order' => $containerCount++,
          'params' => '',
      ));
      $main_container_id = $db->lastInsertId($this->_contentTable);

//MIDDLE CONTAINER  
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'middle',
          'parent_content_id' => $main_container_id,
          'order' => $containerCount++,
          'params' => '',
      ));
      $main_middle_id = $db->lastInsertId($this->_contentTable);

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'siteevent.list-profile-breadcrumb',
          'parent_content_id' => $main_middle_id,
          'order' => $widgetCount++,
          'params' => '',
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'siteevent.list-information-profile',
          'parent_content_id' => $main_middle_id,
          'order' => $widgetCount++,
          'params' => '{"showContent":["postedDate","postedBy","startDate","endDate","viewCount","likeCount","commentCount","photo","tags","location","description","title","compare","wishlist","reviewCreate"]}'
      ));
      
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'siteevent.event-status',
          'parent_content_id' => $main_middle_id,
          'order' => $widgetCount++,
          'params' => '{"showButton":"1"}'
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.container-tabs-columns',
          'parent_content_id' => $main_middle_id,
          'order' => $widgetCount++,
          'params' => '{"max":"6"}',
      ));
      $tab_id = $db->lastInsertId($this->_contentTable);

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advfeed',
          'parent_content_id' => $tab_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Updates"}',
          'module' => 'advancedactivity'
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'siteevent.editor-reviews-siteevent',
          'parent_content_id' => $tab_id,
          'order' => $widgetCount++,
          'params' => '{"titleEditor":"Review","titleOverview":"Overview","titleDescription":"Description","titleCount":"","title":"","show_slideshow":"1","slideshow_height":"500","slideshow_width":"800","showCaption":"1","showButtonSlide":"1","mouseEnterEvent":"0","thumbPosition":"bottom","autoPlay":"0","slidesLimit":"20","captionTruncation":"200","showComments":"1","nomobile":"0","name":"siteevent.editor-reviews-siteevent"}'
      ));
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'siteevent.profile-members',
          'parent_content_id' => $tab_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Guests","titleCount":true}'
      ));
           
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'siteevent.overview-siteevent',
          'parent_content_id' => $tab_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Overview","titleCount":"true"}'
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'siteevent.profile-announcements-siteevent',
          'parent_content_id' => $tab_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Announcements","titleCount":true}'
      ));
      
      
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'siteevent.specification-siteevent',
          'parent_content_id' => $tab_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Information","titleCount":"true"}'
      ));
      
      
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'siteevent.photos-siteevent',
          'parent_content_id' => $tab_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Photos","titleCount":"true"}'
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'siteevent.video-siteevent',
          'parent_content_id' => $tab_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Videos","titleCount":"true"}'
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'siteevent.discussion-siteevent',
          'parent_content_id' => $tab_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Discussions","titleCount":"true"}'
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'siteevent.location-siteevent',
          'parent_content_id' => $tab_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Map","titleCount":"true"}'
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'siteevent.user-siteevent',
          'parent_content_id' => $tab_id,
          'order' => $widgetCount++,
          'params' => '{"title":"User Reviews","titleCount":"true"}'
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.profile-links',
          'parent_content_id' => $tab_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Links","titleCount":"true"}'
      ));
    }
  }

  public function addEventManagePage() {

    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('siteevent_index_manage');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'siteevent_index_manage',
          'displayname' => 'Advanced Events - Event Manage Page',
          'title' => 'My Events',
          'description' => 'This is the event manage page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
      ));
      //Insert search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 2,
      ));
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'siteevent.manage-events-siteevent',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"title":"","titleCount":true,"layouts_order":1,"orderby":"event_id","itemCount":"10","truncation":"30","nomobile":"0","name":"siteevent.manage-events-siteevent"}',
          'order' => 3,
      ));
    }

    return $this;
  }

  protected function addCategroiesPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('siteevent_index_categories');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'siteevent_index_categories',
          'displayname' => 'Advanced Events - Categories Home',
          'title' => 'Categories Home',
          'description' => 'This is the categories home page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
      ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'siteevent.categories-home',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 3,
          'params' => '{"eventtype_id":"-1"}',
      ));
    }
  }

  protected function addBrowseReviewPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('siteevent_review_browse');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'siteevent_review_browse',
          'displayname' => 'Advanced Events - Browse Reviews',
          'title' => 'Browse Reviews',
          'description' => 'This is the review browse page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
      ));
      // Insert Advance search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 2,
      ));
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 3,
      ));
    }
  }

  protected function addBrowseDiaryPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('siteevent_diary_browse');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'siteevent_diary_browse',
          'displayname' => 'Advanced Events - Browse Diaries',
          'title' => 'Browse Diaries',
          'description' => 'This is the diary browse page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
      ));
//      // Insert Advance search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 2,
      ));
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'siteevent.diary-browse',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 3,
          'params' => '{"title":"","statisticsDiary":["entryCount","viewCount"],"listThumbsValue":"2","itemCount":"10"}',
      ));
    }
  }

  protected function addDiaryProfilePage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('siteevent_diary_profile');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'siteevent_diary_profile',
          'displayname' => 'Advanced Events - Diary Profile',
          'title' => 'Diary Profile',
          'description' => 'This is the diary profile page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'siteevent.diary-profile-items',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 3,
          'params' => '{"shareOptions":["friend","report"],"eventInfo":["likeCount","memberCount"],"statisticsDiary":["entryCount","viewCount"],"show_buttons":["diary","comment","like"]}',
      ));
    }
  }

  public function addDiscussionTopicViewPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('siteevent_topic_view');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'siteevent_topic_view',
          'displayname' => 'Advanced Event - Discussion Topic View Page',
          'title' => 'View Event Discussion Topic',
          'description' => 'This is the view page for a event discussion.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'siteevent.discussion-content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
      ));
    }
  }

  public function addEditorProfilePage() {
    $db = Engine_Db_Table::getDefaultAdapter();
    //EDITOR PROFILE PAGE
    $page_id = $db->select()
            ->from($this->_pagesTable, 'page_id')
            ->where('name = ?', "siteevent_editor_profile")
            ->limit(1)
            ->query()
            ->fetchColumn();

    if (!$page_id) {

      $containerCount = 0;
      $widgetCount = 0;

      $db->insert($this->_pagesTable, array(
          'name' => "siteevent_editor_profile",
          'displayname' => 'Advanced Events - Editor Profile',
          'title' => 'Editor Profile',
          'description' => 'This is the editor profile page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      //MAIN CONTAINER
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'main',
          'order' => $containerCount++,
          'params' => '',
      ));
      $main_container_id = $db->lastInsertId();

      //MIDDLE CONTAINER  
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'middle',
          'parent_content_id' => $main_container_id,
          'order' => $containerCount++,
          'params' => '',
      ));
      $main_middle_id = $db->lastInsertId();

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'siteevent.editor-photo-siteevent',
          'parent_content_id' => $main_middle_id,
          'order' => $widgetCount++,
          'params' => '',
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.container-tabs-columns',
          'parent_content_id' => $main_middle_id,
          'order' => $widgetCount++,
          'params' => '{"layoutContainer":"tab","title":""}',
      ));
      $tab_id = $db->lastInsertId();

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'siteevent.editor-profile-reviews-siteevent',
          'parent_content_id' => $tab_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Reviews As Editor","type":"editor"}',
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'siteevent.editor-profile-reviews-siteevent',
          'parent_content_id' => $tab_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Reviews As User","type":"user"}',
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'siteevent.editor-replies-siteevent',
          'parent_content_id' => $tab_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Comments"}',
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'siteevent.editors-siteevent',
          'parent_content_id' => $tab_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Similar Editors","nomobile":"1"}',
      ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $widgetCount++,
          'parent_content_id' => $main_middle_id,
          'order' => 2,
      ));
    }
  }

  public function addVideoViewPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('siteevent_video_view');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'siteevent_video_view',
          'displayname' => 'Advanced Events - Video View Page',
          'title' => 'Video Profile',
          'description' => 'This is the video view page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'siteevent.video-content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
      ));
    }
  }

  //Create Review Profile Page

  public function addReviewProfilePage() {
    //REVIEW PROFILE PAGE
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    $page_id = $db->select()
            ->from($this->_pagesTable, 'page_id')
            ->where('name = ?', "siteevent_review_view")
            ->limit(1)
            ->query()
            ->fetchColumn();

    //CREATE PAGE IF NOT EXIST
    if (!$page_id) {

      $containerCount = 0;
      $widgetCount = 0;

      $db->insert($this->_pagesTable, array(
          'name' => "siteevent_review_view",
          'displayname' => 'Advanced Events - Review Profile',
          'title' => 'Review Profile',
          'description' => 'This is the review profile page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      //TOP CONTAINER
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'top',
          'page_id' => $page_id,
          'order' => $containerCount++,
      ));
      $top_container_id = $db->lastInsertId();

      //MAIN CONTAINER
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => $containerCount++,
      ));
      $main_container_id = $db->lastInsertId();

      //INSERT TOP-MIDDLE
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $top_container_id,
          'order' => $containerCount++,
      ));
      $top_middle_id = $db->lastInsertId();


      //MAIN-MIDDLE CONTAINER
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_container_id,
          'order' => $containerCount++,
      ));
      $main_middle_id = $db->lastInsertId();

     //WE WILL NOT ADD THE WIDGET TAB ON APP AND TABLET APP.
     if($this->_pagesTable == 'engine4_sitemobile_pages' || $this->_pagesTable == 'engine4_sitemobile_tablet_pages')  {
        $db->insert($this->_contentTable, array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'siteevent.profile-review-breadcrumb-siteevent',
            'parent_content_id' => $top_middle_id,
            'order' => $widgetCount++,
            'params' => '{"nomobile":"1"}',
        ));
     }


      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'siteevent.profile-review-siteevent',
          'parent_content_id' => $main_middle_id,
          'order' => $widgetCount++,
          'params' => '{"title":"","titleCount":true,"name":"siteevent.profile-review-siteevent"}',
      ));
    }
  }

    public function addHostProfilePage() {
    $db = Engine_Db_Table::getDefaultAdapter();
    //EDITOR PROFILE PAGE
    $page_id = $db->select()
            ->from($this->_pagesTable, 'page_id')
            ->where('name = ?', "siteevent_organizer_view")
            ->limit(1)
            ->query()
            ->fetchColumn();

    if (!$page_id) {

      $containerCount = 0;
      $widgetCount = 0;

      $db->insert($this->_pagesTable, array(
          'name' => "siteevent_organizer_view",
          'displayname' => 'Advanced Events - Host Profile',
          'title' => 'Host Profile',
          'description' => 'This is the host profile page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      //MAIN CONTAINER
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'main',
          'order' => $containerCount++,
          'params' => '',
      ));
      $main_container_id = $db->lastInsertId();

      //MIDDLE CONTAINER  
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'middle',
          'parent_content_id' => $main_container_id,
          'order' => $containerCount++,
          'params' => '',
      ));
      $main_middle_id = $db->lastInsertId();

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'siteevent.organizer-info',
          'parent_content_id' => $main_middle_id,
          'order' => $widgetCount++,
          'params' => '{"showInfo":["title","description","photo","creator","options","totalevent","totalrating"],"title":"","nomobile":"0","name":"siteevent.organizer-info"}',
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.container-tabs-columns',
          'parent_content_id' => $main_middle_id,
          'order' => $widgetCount++,
          'params' => '{"layoutContainer":"tab","title":""}',
      ));
      $tab_id = $db->lastInsertId();

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'siteevent.host-events',
          'parent_content_id' => $tab_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Events","titleCount":true,"eventInfo":["hostName","location","startDate"],"contentType":null,"category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","truncation":"30","itemCount":"5","ratingType":"rating_avg","nomobile":"0","name":"siteevent.host-events"}',
      ));
    }
  }
  
  //MEMBER PROFILE PAGE WIDGETS
  public function addmemberProfilePageWidgets() {
    //GET DATABASE
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    //MEMBER PROFILE PAGE WIDGETS
    $page_id = $db->select()
            ->from($this->_pagesTable, array('page_id'))
            ->where('name =?', 'user_profile_index')
            ->limit(1)
            ->query()
            ->fetchColumn();

    if (!empty($page_id)) {

      $tab_id = $db->select()
              ->from($this->_contentTable, array('content_id'))
              ->where('page_id =?', $page_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitemobile.container-tabs-columns')
              ->limit(1)
              ->query()
              ->fetchColumn();

      if (!empty($tab_id)) {

        $content_id = $db->select()
                ->from($this->_contentTable, array('content_id'))
                ->where('page_id =?', $page_id)
                ->where('type = ?', 'widget')
                ->where('name = ?', 'siteevent.profile-siteevent')
                ->limit(1)
                ->query()
                ->fetchColumn();

        if (empty($content_id)) {
          $db->insert($this->_contentTable, array(
              'page_id' => $page_id,
              'type' => 'widget',
              'name' => 'siteevent.profile-siteevent',
              'parent_content_id' => $tab_id,
              'order' => 999,
              'params' => '{"title":"Events","titleCount":"true","eventInfo":["hostName","location","startDate"],"showEventType":"all"}',
          ));
        }
//::- NOT ADD Widget on USER Profile Page at installation
//        $content_id = $db->select()
//                ->from($this->_contentTable, array('content_id'))
//                ->where('page_id =?', $page_id)
//                ->where('type = ?', 'widget')
//                ->where('name = ?', 'siteevent.editor-profile-reviews-siteevent')
//                ->limit(1)
//                ->query()
//                ->fetchColumn();
//
//        if (empty($content_id)) {
//
//          $db->insert($this->_contentTable, array(
//              'page_id' => $page_id,
//              'type' => 'widget',
//              'name' => 'siteevent.editor-profile-reviews-siteevent',
//              'parent_content_id' => $tab_id,
//              'order' => 999,
//              'params' => '{"title":"Reviews As Editor","type":"editor"}',
//          ));
//
//          $db->insert($this->_contentTable, array(
//              'page_id' => $page_id,
//              'type' => 'widget',
//              'name' => 'siteevent.editor-profile-reviews-siteevent',
//              'parent_content_id' => $tab_id,
//              'order' => 999,
//              'params' => '{"title":"Reviews As User","type":"user", "onlyEventtypeEditorEvents":"1"}',
//          ));
//        }
      }
    }
  }
  
   //INTEGRATED MODULE PROFILE PAGE EVENT WIDGETS
  public function addIntegratedModuleProfileWidget($pagename) {
    //GET DATABASE
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    //MEMBER PROFILE PAGE WIDGETS
    $page_id = $db->select()
            ->from($this->_pagesTable, array('page_id'))
            ->where('name =?', $pagename)
            ->limit(1)
            ->query()
            ->fetchColumn();

    if (!empty($page_id)) {

      $tab_id = $db->select()
              ->from($this->_contentTable, array('content_id'))
              ->where('page_id =?', $page_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitemobile.container-tabs-columns')
              ->limit(1)
              ->query()
              ->fetchColumn();

      if (!empty($tab_id)) {

        $content_id = $db->select()
                ->from($this->_contentTable, array('content_id'))
                ->where('page_id =?', $page_id)
                ->where('type = ?', 'widget')
                ->where('name = ?', 'siteevent.contenttype-events')
                ->limit(1)
                ->query()
                ->fetchColumn();

        if (empty($content_id)) {
          $db->insert($this->_contentTable, array(
              'page_id' => $page_id,
              'type' => 'widget',
              'name' => 'siteevent.contenttype-events',
              'parent_content_id' => $tab_id,
              'order' => 999,
              'params' => '{"title":"Events","titleCount":"true","eventInfo":["hostName","location","startDate"],"itemCount":"5"}',
          ));
        }
      }
    }
  }

  public function addEventCalendarPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('siteevent_index_calendar');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'siteevent_index_calendar',
          'displayname' => 'Advanced Events - Calendar',
          'title' => 'Calendar',
          'description' => 'This is the event calendar page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
      ));

      //Insert search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'seaocore.change-my-location',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          //'params' => '{}',
          'order' => 2,
      ));
      
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'siteevent.calendarview-siteevent',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"title":"","titleCount":true,"detactLocation":"1","name":"siteevent.calendarview-siteevent"}',
          'order' => 3,
      ));
    }
  }
  
}
