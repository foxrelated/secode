<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteadvsearch_Api_Core extends Core_Api_Abstract {

  /**
   * Get content listing according to requirement
   *
   * @param array $params
   * @return $items
   */
  public function getCoreSearchData($params = array()) {

    $resultType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteadvsearch.result.type', 1);
    $SearchTable = Engine_Api::_()->getDbtable('search', 'core');
    $searchTableName = $SearchTable->info('name');

    $advancedContentTableName = Engine_Api::_()->getDbtable('contents', 'siteadvsearch')->info('name');
    $items = array();
    $text = trim($params['text']);
    if (!empty($text)) {
      $select = $SearchTable->select()
              ->setIntegrityCheck(false)
              ->from($SearchTable->info('name'), array('type', 'id', 'description', 'keywords'));
      if (empty($resultType)) {
        $select->join($advancedContentTableName, "$advancedContentTableName.resource_type=$searchTableName.item_type", array('resource_type'));
        $select->where('enabled = ?', 1);
      }
      else
        $select->joinLeft($advancedContentTableName, "$advancedContentTableName.resource_type=$searchTableName.item_type", array('resource_type', 'order'));

      $select->where("(`title` LIKE  '%$text%' OR `description` LIKE  '%$text%' OR `keywords` LIKE  '%$text%' OR `hidden` LIKE  '%$text%')");

      if(!empty($params['searchLocation'])) {
          $searhLocation = explode(',', $params['searchLocation']);
          $select->where("`location` LIKE '%$searhLocation[0]%'");
      }

      if (!empty($params['resource_type']) && $params['resource_type'] != 'all')
        $select->where('type = ?', $params['resource_type']);

      $select->group('id');
      $select->group('resource_type');
      $select->order(new Zend_Db_Expr('- `order` DESC'));

      if (!empty($params['pagination']))
        return $items = Zend_Paginator::factory($select);
      else {
        $select = $select->limit($params['limit']);
        return $items = $SearchTable->fetchAll($select);
      }
    }
    else
      return $items;
  }

  /**
   * Make Widgetize Page
   *
   * @param array $params
   * @param int $content_id
   */
  public function makeWidgetizePage($params = array(), $content_id = 0) {
       $db = Engine_Db_Table::getDefaultAdapter();
    $advancedContentTableName = 'engine4_siteadvsearch_contents';
    if (isset($params['default_page'])) {
      $listingTypeId = $params['listingtype_id'];
      $resourceType = $params['resource_type'];
      $titlePluUc = $params['resource_title'];
    } else {
     
      $select = new Zend_Db_Select($db);
      $select->from($advancedContentTableName)
              ->where('content_id = ?', $content_id);
      $contentType = $select->query()->fetchObject();
      $resourceType = $contentType->resource_type;
      $listingTypeId = $contentType->listingtype_id;
      $titlePluUc = $contentType->resource_title;


      $db->query("UPDATE `engine4_siteadvsearch_contents` SET `widgetize` = '1', `content_tab` = '1' WHERE `engine4_siteadvsearch_contents`.`content_id` ='$content_id' LIMIT 1 ;");
    }

    switch ($resourceType) {
      case "sitereview_listingtype_$listingTypeId":

        $title_singular = strtolower(Engine_Api::_()->getDbTable('listingtypes', 'sitereview')->getListingTypeColumn($listingTypeId, 'title_singular'));
        switch ($title_singular) {
          case "product":
            $defaultParams = '{"title":"","titleCount":true,"layouts_views":["2"],"layouts_order":"2","statistics":["viewCount","likeCount","commentCount","reviewCount"],"columnWidth":"180","truncationGrid":"90","listingtype_id":"' . $listingTypeId . '","ratingType":"rating_both","detactLocation":"0","defaultLocationDistance":"1000","columnHeight":"328","showExpiry":"0","viewType":"1","bottomLine":"1","postedby":"1","orderby":"creation_date","itemCount":"10","truncation":"25","show_content":"3","nomobile":"0","name":"sitereview.browse-listings-sitereview"}';
            break;
          case "tourism":
            $defaultParams = '{"title":"","titleCount":true,"layouts_views":["1"],"layouts_order":"1","statistics":["viewCount","likeCount","commentCount","reviewCount"],"columnWidth":"199","truncationGrid":"90","listingtype_id":"' . $listingTypeId . '","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","columnHeight":"299","showExpiry":"0","viewType":"1","bottomLine":"0","postedby":"1","orderby":"featured","itemCount":"12","truncation":"25","show_content":"3","nomobile":"0","name":"sitereview.browse-listings-sitereview"}';
            break;
          case "fashion":
            $defaultParams = '{"title":"","titleCount":true,"layouts_views":["2"],"layouts_order":"2","statistics":["viewCount","likeCount","commentCount","reviewCount"],"columnWidth":"199","truncationGrid":"30","listingtype_id":"' . $listingTypeId . '","ratingType":"rating_both","detactLocation":"0","defaultLocationDistance":"1000","columnHeight":"310","showExpiry":"0","viewType":"0","bottomLine":"0","postedby":"0","orderby":"featured","itemCount":"12","truncation":"25","show_content":"3","nomobile":"0","name":"sitereview.browse-listings-sitereview"}';
            break;
          case "electronics":
            $defaultParams = '{"title":"","titleCount":true,"layouts_views":["1"],"layouts_order":"1","statistics":["viewCount","likeCount","commentCount","reviewCount"],"columnWidth":"180","truncationGrid":"90","listingtype_id":"' . $listingTypeId . '","ratingType":"rating_both","detactLocation":"0","defaultLocationDistance":"1000","columnHeight":"328","showExpiry":"0","viewType":"1","bottomLine":"0","postedby":"0","orderby":"spfesp","itemCount":"6","truncation":"45","show_content":"3","nomobile":"0","name":"sitereview.browse-listings-sitereview"}';
            break;
          case "sports":
            $defaultParams = '{"title":"","titleCount":true,"layouts_views":["2"],"layouts_order":"2","statistics":"","columnWidth":"196","truncationGrid":"90","listingtype_id":"' . $listingTypeId . '","ratingType":"rating_both","detactLocation":"0","defaultLocationDistance":"1000","columnHeight":"310","showExpiry":"0","viewType":"1","bottomLine":"1","postedby":"1","orderby":"fespfe","itemCount":"16","truncation":"25","show_content":"3","nomobile":"0","name":"sitereview.browse-listings-sitereview"}';
            break;
          case "blog":
            $defaultParams = '{"title":"","titleCount":true,"layouts_views":["2"],"layouts_order":"2","statistics":["viewCount","reviewCount"],"columnWidth":"199","truncationGrid":"45","listingtype_id":"' . $listingTypeId . '","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","columnHeight":"258","showExpiry":"0","viewType":"0","bottomLine":"0","postedby":"1","orderby":"fespfe","itemCount":"12","truncation":"40","show_content":"3","nomobile":"0","name":"sitereview.browse-listings-sitereview"}';
            break;
          case "property":
            $defaultParams = '{"title":"","titleCount":true,"layouts_views":["3"],"layouts_order":"3","statistics":["viewCount","likeCount","commentCount","reviewCount"],"columnWidth":"190","truncationGrid":"30","listingtype_id":"' . $listingTypeId . '","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","columnHeight":"328","showExpiry":"0","viewType":"1","bottomLine":"0","postedby":"1","orderby":"creation_date","itemCount":"10","truncation":"25","show_content":"3","nomobile":"0","name":"sitereview.browse-listings-sitereview"}';
            break;
          case "food":
            $defaultParams = '{"title":"","titleCount":true,"layouts_views":["2"],"layouts_order":"2","statistics":["viewCount","likeCount"],"columnWidth":"199","truncationGrid":"90","listingtype_id":"' . $listingTypeId . '","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","columnHeight":"299","showExpiry":"0","viewType":"0","bottomLine":"0","postedby":"1","orderby":"featured","itemCount":"12","truncation":"40","show_content":"3","nomobile":"0","name":"sitereview.browse-listings-sitereview"}';
            break;
          case "classified":
            $defaultParams = '{"title":"","titleCount":true,"layouts_views":["1"],"layouts_order":"1","statistics":"","columnWidth":"199","truncationGrid":"90","listingtype_id":"' . $listingTypeId . '","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","columnHeight":"328","showExpiry":"1","viewType":"0","bottomLine":"0","postedby":"0","orderby":"featured","itemCount":"6","truncation":"45","show_content":"3","nomobile":"0","name":"sitereview.browse-listings-sitereview"}';
            break;
          case "entertainment":
            $defaultParams = '{"title":"","titleCount":true,"layouts_views":["1"],"layouts_order":"1","statistics":"","columnWidth":"197","truncationGrid":"90","listingtype_id":"' . $listingTypeId . '","ratingType":"rating_both","detactLocation":"0","defaultLocationDistance":"1000","columnHeight":"320","showExpiry":"0","viewType":"1","bottomLine":"0","postedby":"0","orderby":"spfesp","itemCount":"6","truncation":"25","show_content":"3","nomobile":"0","name":"sitereview.browse-listings-sitereview"}';
            break;
          case "article":
            $defaultParams = '{"title":"","titleCount":true,"layouts_views":["1"],"layouts_order":"1","statistics":["viewCount","likeCount","reviewCount"],"columnWidth":"199","truncationGrid":"90","listingtype_id":"' . $listingTypeId . '","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","columnHeight":"267","showExpiry":"0","viewType":"1","bottomLine":"0","postedby":"1","orderby":"featured","itemCount":"6","truncation":"25","show_content":"2","nomobile":"0","name":"sitereview.browse-listings-sitereview"}';
            break;
          case "job":
            $defaultParams = '{"title":"","titleCount":true,"layouts_views":["1"],"layouts_order":"1","statistics":["viewCount","likeCount"],"columnWidth":"180","truncationGrid":"90","listingtype_id":"' . $listingTypeId . '","ratingType":"rating_both","detactLocation":"0","defaultLocationDistance":"1000","columnHeight":"328","showExpiry":"0","viewType":"0","bottomLine":"0","postedby":"0","orderby":"featured","itemCount":"6","truncation":"25","show_content":"3","nomobile":"0","name":"sitereview.browse-listings-sitereview"}';
            break;
          default:
            $defaultParams = '{"listingtype_id":"' . $listingTypeId . '","ratingType":"rating_both","statistics":["viewCount","likeCount","commentCount","reviewCount"],"layouts_views":["2"],"layouts_order":"2", "show_content":"3"}';
            break;
        }

        $name = "siteadvsearch_index_browse-page_listtype_$listingTypeId";
        $displayName = "Advanced Search - MLT - $titlePluUc";
        $NewWidgetName = 'sitereview.newlisting-sitereview';
        $searchWidgetName = 'sitereview.search-sitereview';
        $browseWidgetName = 'sitereview.browse-listings-sitereview';
        $browseWidgetParams = $defaultParams;
        $newLinkWidgetParams = '{"listingtype_id":"' . $listingTypeId . '","nomobile":"1"}';
        $searchWidgetParams = '{"listingtype_id":"' . $listingTypeId . '"}';
        $newLinkWidgetExist = 1;
        break;
      case "sitepage_page":
        $name = "siteadvsearch_index_browse-page_$resourceType";
        $displayName = "Advanced Search - SEAO - Directory / Pages";
        $NewWidgetName = 'sitepage.newpage-sitepage';
        $searchWidgetName = 'sitepage.search-sitepage';
        $browseWidgetName = 'sitepage.pages-sitepage';
        $browseWidgetParams = '{"title":"","titleCount":true,"layouts_views":["1"],"layouts_oder":"1","columnWidth":"195","statistics":["likeCount","followCount","viewCount","memberCount","memberApproval"],"columnHeight":"230","turncation":"20","showlikebutton":"0","showfeaturedLable":"0","showsponsoredLable":"0","showlocation":"0","showgetdirection":"0","showprice":"0","showpostedBy":"1","showdate":"0","showContactDetails":"0","category_id":"0","showProfileField":"0","custom_field_heading":"0","custom_field_title":"0","show_content":"3","customFieldCount":"2","nomobile":"0","name":"sitepage.pages-sitepage"}';
        $newLinkWidgetParams = '{"title":"","titleCount":true}';
        $searchWidgetParams = '{"title":"","titleCount":true}';
        $newLinkWidgetExist = 1;
        break;
      case "siteevent_event":
        $name = "siteadvsearch_index_browse-page_$resourceType";
        $displayName = "Advanced Search - SEAO - Advanced Events";
        $NewWidgetName = 'siteevent.newevent-siteevent';
        $searchWidgetName = 'siteevent.search-siteevent';
        $browseWidgetName = 'siteevent.browse-events-siteevent';
        $browseWidgetParams = '{"title":"","titleCount":true,"layouts_views":["2"],"layouts_order":"2","columnWidth":"199","truncationGrid":"100","eventType":"0","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","columnHeight":"225","eventInfo":["startDate","location","directionLink","memberCount"],"titlePosition":"1","orderby":"featured","itemCount":"12","truncationLocation":"50","truncation":"100","ratingType":"rating_both","detactLocation":"0","defaultLocationDistance":"1000","show_content":"3","nomobile":"0","name":"siteevent.browse-events-siteevent"}';
        $newLinkWidgetParams = '{"title":"","titleCount":true}';
        $searchWidgetParams = '{"title":"","titleCount":true, "locationDetection":"0"}';
        $newLinkWidgetExist = 1;
        break;
      case "sitebusiness_business":
        $name = "siteadvsearch_index_browse-page_$resourceType";
        $displayName = "Advanced Search - SEAO - Directory / Businesses";
        $NewWidgetName = 'sitebusiness.newbusiness-sitebusiness';
        $searchWidgetName = 'sitebusiness.search-sitebusiness';
        $browseWidgetName = 'sitebusiness.businesses-sitebusiness';
        $browseWidgetParams = '{"title":"","titleCount":true,"layouts_views":["2"],"layouts_order":"2","show_content":"3"}';
        $newLinkWidgetParams = '{"title":"","titleCount":true}';
        $searchWidgetParams = '{"title":"","titleCount":true}';
        $newLinkWidgetExist = 1;
        break;
      case "sitegroup_group":
        $name = "siteadvsearch_index_browse-page_$resourceType";
        $displayName = "Advanced Search - SEAO - Groups / Communities";
        $NewWidgetName = 'sitegroup.newgroup-sitegroup';
        $searchWidgetName = 'sitegroup.search-sitegroup';
        $browseWidgetName = 'sitegroup.groups-sitegroup';
        $browseWidgetParams = '{"title":"","titleCount":true,"layouts_views":["2"],"layouts_oder":"2","columnWidth":"199","statistics":["likeCount","followCount","memberApproval"],"columnHeight":"175","turncation":"40","showlikebutton":"1","showfeaturedLable":"1","showsponsoredLable":"0","showlocation":"0","showgetdirection":"0","showprice":"1","showpostedBy":"0","showdate":"0","showContactDetails":"1","category_id":"0","showProfileField":"0","custom_field_heading":"0","custom_field_title":"0","show_content":"3","customFieldCount":"2","nomobile":"0","name":"sitegroup.groups-sitegroup"}';
        $newLinkWidgetParams = '{"title":"","titleCount":true}';
        $searchWidgetParams = '{"title":"","titleCount":true}';
        $newLinkWidgetExist = 1;
        break;
      case "sitestore_store":
        $name = "siteadvsearch_index_browse-page_$resourceType";
        $displayName = "Advanced Search - SEAO - Stores";
        $NewWidgetName = 'sitestoreproduct.store-startup-link';
        $searchWidgetName = 'sitestore.search-sitestore';
        $browseWidgetName = 'sitestore.stores-sitestore';
        $browseWidgetParams = '{"title":"","titleCount":true,"layouts_views":["2"],"layouts_oder":"2","columnWidth":"197","statistics":["likeCount","followCount","viewCount"],"columnHeight":"204","turncation":"40","showlikebutton":"1","showfeaturedLable":"1","showsponsoredLable":"0","showlocation":"1","showprice":"0","showpostedBy":"0","showdate":"0","showContactDetails":"0","category_id":"0","show_content":"3","nomobile":"0","name":"sitestore.stores-sitestore"}';
        $newLinkWidgetParams = '{"title":"","titleCount":true}';
        $searchWidgetParams = '{"title":"","titleCount":true}';
        $newLinkWidgetExist = 1;
        break;
      case "sitestoreproduct_product":
        $name = "siteadvsearch_index_browse-page_$resourceType";
        $displayName = "Advanced Search - SEAO - Stores - Products";
        $searchWidgetName = 'sitestoreproduct.search-sitestoreproduct';
        $browseWidgetName = 'sitestoreproduct.browse-products-sitestoreproduct';
        $browseWidgetParams = '{"title":"","titleCount":true,"layouts_views":["2"],"layouts_order":"2","statistics":["viewCount","likeCount","reviewCount"],"columnWidth":"197","truncationGrid":"32","ratingType":"rating_avg","columnHeight":"280","bottomLine":"1","postedby":"0","add_to_cart":"1","in_stock":"1","orderby":"spfesp","itemCount":"20","truncation":"25","show_content":"3","nomobile":"0","name":"sitestoreproduct.browse-products-sitestoreproduct"}';
        $newLinkWidgetExist = 0;
        break;
      case "list_listing":
        $name = "siteadvsearch_index_browse-page_$resourceType";
        $displayName = "Advanced Search - SEAO - Listings";
        $NewWidgetName = 'list.newlisting-list';
        $searchWidgetName = 'list.search-list';
        $browseWidgetName = 'list.listings-list';
        $browseWidgetParams = '{"title":"","titleCount":true,"layouts_views":["1"],"layouts_oder":"1","statistics":["viewCount","likeCount"],"show_content":"3","nomobile":"0","name":"list.listings-list"}';
        $newLinkWidgetParams = '{"title":"","titleCount":true}';
        $searchWidgetParams = '{"title":"","titleCount":"true"}';
        $newLinkWidgetExist = 1;
        break;
      case "recipe":
        $name = "siteadvsearch_index_browse-page_$resourceType";
        $displayName = "Advanced Search - SEAO - Recipes";
        $NewWidgetName = 'recipe.newrecipe-recipe';
        $searchWidgetName = 'recipe.search-recipe';
        $browseWidgetName = 'recipe.recipes-recipe';
        $browseWidgetParams = '{"title":"","titleCount":true,"layouts_views":["1"],"layouts_oder":"1","statistics":["viewCount","likeCount"],"show_content":"3","nomobile":"0","name":"recipe.recipes-recipe"}';
        $newLinkWidgetParams = '{"title":"","titleCount":true}';
        $searchWidgetParams = '{"title":"","titleCount":"true"}';
        $newLinkWidgetExist = 1;
        break;
      case "sitefaq_faq":
        $name = "siteadvsearch_index_browse-page_$resourceType";
        $displayName = "Advanced Search - SEAO - FAQs";
        $searchWidgetName = 'sitefaq.search-sitefaqs';
        $browseWidgetName = 'sitefaq.browse-sitefaqs';
        $browseWidgetParams = '{"title":"","titleCount":true,"orderby":"faq_id","linked":"0","print":"1","statisticsRating":"0","statisticsHelpful":"1","statisticsComment":"1","statisticsView":"1","itemCount":"10","truncation":"0","scrollButton":"1","show_content":"3","nomobile":"","name":"sitefaq.browse-sitefaqs"}';
        $searchWidgetParams = '{"title":"","titleCount":"true"}';
        $newLinkWidgetExist = 0;
        break;
      case "sitetutorial_tutorial":
        $name = "siteadvsearch_index_browse-page_$resourceType";
        $displayName = "Advanced Search - SEAO - Tutorials";
        $searchWidgetName = 'sitetutorial.search-sitetutorials';
        $browseWidgetName = 'sitetutorial.browse-sitetutorials';
        $browseWidgetParams = '{"title":"","titleCount":true,"orderby":"faq_id","linked":"0","print":"1","statisticsRating":"0","statisticsHelpful":"1","statisticsComment":"1","statisticsView":"1","itemCount":"10","truncation":"0","scrollButton":"1","show_content":"3","nomobile":"","name":"sitetutorial.browse-sitetutorials"}';
        $searchWidgetParams = '{"title":"","titleCount":"true"}';
        $newLinkWidgetExist = 0;
        break;
      case "document":
        $name = "siteadvsearch_index_browse-page_$resourceType";
        $displayName = "Advanced Search - SEAO - Documents";
        $NewWidgetName = 'document.create-documents';
        $searchWidgetName = 'document.search-documents';
        $browseWidgetName = 'document.browse-documents';
        $browseWidgetParams = '{"title":"","titleCount":true,"orderby":"featured","itemCount":"6","show_content":"3","nomobile":"","name":"document.browse-documents"}';
        $newLinkWidgetParams = '{"title":"","titleCount":true}';
        $searchWidgetParams = '{"title":"","titleCount":"true"}';
        $newLinkWidgetExist = 1;
        break;
      case "feedback":
        $name = "siteadvsearch_index_browse-page_$resourceType";
        $displayName = "Advanced Search - SEAO - Feedbacks";
        $NewWidgetName = 'feedback.new-feedback';
        $searchWidgetName = 'feedback.search-feedback';
        $browseWidgetName = 'feedback.browse-feedbacks';
        $browseWidgetParams = '{"title":"","titleCount":true,"show_content":"3","nomobile":"0","name":"feedback.browse-feedbacks"}';
        $newLinkWidgetParams = '{"title":"","titleCount":true}';
        $searchWidgetParams = '{"title":"","titleCount":"true"}';
        $newLinkWidgetExist = 1;
        break;
      case "group":
        $name = "siteadvsearch_index_browse-group";
        $displayName = "Advanced Search - SE Core - Groups";
        $NewWidgetName = 'group.browse-menu-quick';
        $searchWidgetName = 'group.browse-search';
        $browseWidgetName = 'core.content';
        $browseWidgetParams = '{"title":"","titleCount":true,"show_content":"3"}';
        $newLinkWidgetParams = '{"title":"","titleCount":true}';
        $searchWidgetParams = '{"title":"","titleCount":"true"}';
        $newLinkWidgetExist = 1;
        break;
      case "poll":
        $name = "siteadvsearch_index_browse-poll";
        $displayName = "Advanced Search - SE Core - Polls";
        $NewWidgetName = 'poll.browse-menu-quick';
        $searchWidgetName = 'poll.browse-search';
        $browseWidgetName = 'core.content';
        $browseWidgetParams = '{"title":"","titleCount":true}';
        $newLinkWidgetParams = '{"title":"","titleCount":true}';
        $searchWidgetParams = '{"title":"","titleCount":"true"}';
        $newLinkWidgetExist = 1;
        break;
      case "video":
        $name = "siteadvsearch_index_browse-video";
        $displayName = "Advanced Search - SE Core - Videos";
        $NewWidgetName = 'video.browse-menu-quick';
        $searchWidgetName = 'video.browse-search';
        $browseWidgetName = 'core.content';
        $browseWidgetParams = '{"title":"","titleCount":true}';
        $newLinkWidgetParams = '{"title":"","titleCount":true}';
        $searchWidgetParams = '{"title":"","titleCount":"true"}';
        $newLinkWidgetExist = 1;
        break;
      case "classified":
        $name = "siteadvsearch_index_browse-classified";
        $displayName = "Advanced Search - SE Core - Classifieds";
        $NewWidgetName = 'classified.browse-menu-quick';
        $searchWidgetName = 'classified.browse-search';
        $browseWidgetName = 'core.content';
        $browseWidgetParams = '{"title":"","titleCount":true}';
        $newLinkWidgetParams = '{"title":"","titleCount":true}';
        $searchWidgetParams = '{"title":"","titleCount":"true"}';
        $newLinkWidgetExist = 1;
        break;
      case "blog":
        $name = "siteadvsearch_index_browse-blog";
        $displayName = "Advanced Search - SE Core - Blogs";
        $NewWidgetName = 'blog.browse-menu-quick';
        $searchWidgetName = 'blog.browse-search';
        $browseWidgetName = 'core.content';
        $browseWidgetParams = '{"title":"","titleCount":true}';
        $newLinkWidgetParams = '{"title":"","titleCount":true}';
        $searchWidgetParams = '{"title":"","titleCount":"true"}';
        $newLinkWidgetExist = 1;
        break;
      case "album":
        $name = "siteadvsearch_index_browse-album";
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_pages')
                ->where('name = ?', 'sitealbum_index_browse')
                ->limit(1);
        $info = $select->query()->fetch();
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitealbum') && !empty($info)) {
          $displayName = "Advanced Search - SEAO - Advanced Albums";
          $NewWidgetName = 'sitealbum.browse-menu-quick';
          $searchWidgetName = 'sitealbum.search-sitealbum';
          $browseWidgetName = 'sitealbum.browse-albums-sitealbum';
          $browseWidgetParams = '{"title":"","titleCount":true,"category_id":"0","subcategory_id":null,"hidden_category_id":null,"hidden_subcategory_id":"","margin_photo":"3","photoHeight":"199","photoWidth":"205","columnHeight":"251","albumInfo":["albumTitle","totalPhotos"],"customParams":"1","orderby":"featured","show_content":"3","truncationLocation":"50","albumTitleTruncation":"50","limit":"12","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitealbum.browse-albums-sitealbum"}';
          $searchWidgetParams = '{"title":"","titleCount":true,"viewType":"vertical","showAllCategories":"1","whatWhereWithinmile":"0","advancedSearch":"0","locationDetection":"0","nomobile":"0","name":"sitealbum.search-sitealbum"}';

          $db->query("UPDATE `engine4_siteadvsearch_contents` SET `module_name` = 'sitealbum' WHERE `engine4_siteadvsearch_contents`.`resource_type` ='album' LIMIT 1 ;");
        } else {
          $displayName = "Advanced Search - SE Core - Albums";
          $NewWidgetName = 'album.browse-menu-quick';
          $searchWidgetName = 'siteadvsearch.album-browse-search';
          $browseWidgetName = 'core.content';
          $browseWidgetParams = '{"title":"","titleCount":true}';
          $searchWidgetParams = '{"title":"","titleCount":"true"}';
        }
        $newLinkWidgetParams = '{"title":"","titleCount":true}';
        $newLinkWidgetExist = 1;
        break;
      case "user":
        $name = "siteadvsearch_index_browse-member";
        $displayName = "Advanced Search - SEAO - Advanced Members";
        $searchWidgetName = 'sitemember.search-sitemember';
        $searchWidgetParams = '{"title":"","titleCount":true,"viewType":"vertical","locationDetection":"0","whatWhereWithinmile":"1","advancedSearch":"0","nomobile":"0","name":"sitemember.search-sitemember"}';
        $browseWidgetName = 'sitemember.browse-members-sitemember';
        $browseWidgetParams = '{"title":"","titleCount":true,"layouts_views":["1"],"layouts_order":"1","columnWidth":"199","truncationGrid":"50","columnHeight":"190","has_photo":"1","links":["addfriend","messege","likebutton","poke","suggestion"],"showDetailLink":"1","memberInfo":["location","directionLink","mutualFriend","profileField","age"],"customParams":"5","custom_field_title":"0","custom_field_heading":"0","titlePosition":"1","orderby":"featured","show_content":"3","withoutStretch":"0","show_buttons":["facebook","twitter","pinit","like"],"pinboarditemWidth":"255","sitemember_map_sponsored":"1","itemCount":"8","truncation":"50","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitemember.browse-members-sitemember"}';
        $newLinkWidgetExist = 0;
        break;
      case "event":
        $name = "siteadvsearch_index_browse-event";
        $displayName = "Advanced Search - SE Core - Events";
        $NewWidgetName = 'event.browse-menu-quick';
        $searchWidgetName = 'event.browse-search';
        $browseWidgetName = 'core.content';
        $browseWidgetParams = '{"title":"","titleCount":true}';
        $newLinkWidgetParams = '{"title":"","titleCount":true}';
        $searchWidgetParams = '{"title":"","titleCount":"true"}';
        $newLinkWidgetExist = 1;
        break;
      case "music_playlist":
        $name = "siteadvsearch_index_browse-music";
        $displayName = " Advanced Search - SE Core - Music";
        $NewWidgetName = 'music.browse-menu-quick';
        $searchWidgetName = 'siteadvsearch.music-browse-search';
        $browseWidgetName = 'core.content';
        $browseWidgetParams = '{"title":"","titleCount":true}';
        $newLinkWidgetParams = '{"title":"","titleCount":true}';
        $searchWidgetParams = '{"title":"","titleCount":"true"}';
        $newLinkWidgetExist = 1;
        break;
    
       case "sitevideo_video":
        $name = "siteadvsearch_index_browse-page_sitevideo_video";
        $displayName = "Advanced Search - SEAO - Videos";
        $NewWidgetName = 'sitevideo.post-new-video';
        $searchWidgetName = 'sitevideo.search-video-sitevideo';
        $browseWidgetName = 'sitevideo.browse-videos-sitevideo';
        $browseWidgetParams = '{"title":"","titleCount":true,"viewType":["videoView","gridView","listView"],"defaultViewType":"videoView","videoOption":["title","owner","creationDate","view","like","comment","duration","rating","watchlater","favourite","facebook","twitter","linkedin","googleplus"],"videoType":null,"category_id":"0","subcategory_id":null,"hidden_category_id":null,"hidden_subcategory_id":"0","videoViewWidth":"425","videoViewHeight":"350","gridViewWidth":"250","gridViewHeight":"125","show_content":"2","orderby":"creationDate","detactLocation":"0","defaultLocationDistance":"0","titleTruncation":"100","titleTruncationGridNVideoView":"100","descriptionTruncation":"200","itemCountPerPage":"12","nomobile":"0","name":"sitevideo.browse-videos-sitevideo"}';
        $newLinkWidgetParams = '{"title":"","titleCount":true,"upload_button":"1","upload_button_title":"Post New Video","nomobile":"0","name":"sitevideo.post-new-video"}';
        $searchWidgetParams = '{"title":"","titleCount":true,"viewType":"vertical","showAllCategories":"1","locationDetection":"1","nomobile":"0","name":"sitevideo.search-video-sitevideo"}';
        $newLinkWidgetExist = 1;
        $db->query("UPDATE `engine4_siteadvsearch_contents` SET `module_name` = 'sitevideo', `resource_type` = 'sitevideo_video' WHERE `engine4_siteadvsearch_contents`.`resource_type` ='video' LIMIT 1 ;");
        break;
    
        case "sitevideo_channel":
        $name = "siteadvsearch_index_browse-page_sitevideo_channel";
        $displayName = "Advanced Search - SEAO - Channels";
        $NewWidgetName = 'sitevideo.create-new-channel';
        $searchWidgetName = 'sitevideo.search-sitevideo';
        $browseWidgetName = 'sitevideo.browse-channels-sitevideo';
        $browseWidgetParams = '{"title":"","titleCount":true,"viewType":"vertical","showAllCategories":"1","nomobile":"0","name":"sitevideo.search-sitevideo"}';
        $newLinkWidgetParams = '{"title":"","titleCount":true}';
        $searchWidgetParams = '{"title":"","titleCount":true,"viewType":"vertical","showAllCategories":"1","locationDetection":"1","nomobile":"0","name":"sitevideo.search-video-sitevideo"}';
        $newLinkWidgetExist = 1;
        break;    
    }

    //GET DATABASE
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

    $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', $name)
            ->limit(1)
            ->query()
            ->fetchColumn();

    if (!$page_id) {

      $containerCount = 0;
      $widgetCount = 0;

      $db->insert('engine4_core_pages', array(
          'name' => $name,
          'displayname' => $displayName,
          'title' => '',
          'description' => '',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      //TOP CONTAINER
      $db->insert('engine4_core_content', array(
          'type' => 'container',
          'name' => 'top',
          'page_id' => $page_id,
          'order' => $containerCount++,
      ));
      $top_container_id = $db->lastInsertId();

      //MAIN CONTAINER
      $db->insert('engine4_core_content', array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => $containerCount++,
      ));
      $main_container_id = $db->lastInsertId();

      //INSERT TOP-MIDDLE
      $db->insert('engine4_core_content', array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $top_container_id,
          'order' => $containerCount++,
      ));
      $top_middle_id = $db->lastInsertId();

      //RIGHT CONTAINER
      $db->insert('engine4_core_content', array(
          'type' => 'container',
          'name' => 'right',
          'page_id' => $page_id,
          'parent_content_id' => $main_container_id,
          'order' => $containerCount++,
      ));
      $right_container_id = $db->lastInsertId();

      //MAIN-MIDDLE CONTAINER
      $db->insert('engine4_core_content', array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_container_id,
          'order' => $containerCount++,
      ));
      $main_middle_id = $db->lastInsertId();

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => $searchWidgetName,
          'parent_content_id' => $right_container_id,
          'order' => $widgetCount++,
          'params' => $searchWidgetParams,
      ));

      if (!empty($newLinkWidgetExist)) {
        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => $NewWidgetName,
            'parent_content_id' => $right_container_id,
            'order' => $widgetCount++,
            'params' => $newLinkWidgetParams,
        ));
      }

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => $browseWidgetName,
          'parent_content_id' => $main_middle_id,
          'order' => $widgetCount++,
          'params' => $browseWidgetParams,
      ));
    }
  }

  /**
   * Get member attempt information.
   *
   * @return string $getValue
   */
  public function getMemberAttempt() {
    $getMobiAttemptStr = array();

    if (isset($_SERVER['HTTP_USER_AGENT'])) {
      $getMobiAttemptStr[] = $_SERVER['HTTP_USER_AGENT'];
    }

    if (false !== stripos($_SERVER['HTTP_USER_AGENT'], 'windows') &&
            false === stripos($_SERVER['HTTP_USER_AGENT'], 'windows phone')) {
      $getMobiAttemptStr[] = $_SERVER['HTTP_USER_AGENT'];
    }

    if (isset($_SERVER['HTTP_PROFILE']) ||
            isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
      $getMobiAttemptStr[] = $_SERVER['HTTP_X_WAP_PROFILE'];
    }

    if (isset($_SERVER['HTTP_ACCEPT']) &&
            false !== stripos($_SERVER['HTTP_ACCEPT'], 'application/vnd.wap.xhtml+xml')) {
      $getMobiAttemptStr[] = $_SERVER['HTTP_ACCEPT'];
    }

    if (isset($_SERVER['ALL_HTTP']) &&
            false !== stripos($_SERVER['ALL_HTTP'], 'OperaMini')) {
      $getMobiAttemptStr[] = $_SERVER['ALL_HTTP'];
    }

    if (isset($_SERVER['HTTP_HOST'])) {
      $getMobiAttemptStr[] = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
    }

    if (empty($getMobiAttemptStr) && !empty($mobileShowViewtype)) {
      $getValue = false;
    } else {
      $getValue = @end($getMobiAttemptStr);
    }

    return $getValue;
  }

  /**
   * Get modules version
   *
   * @param string $moduleName
   * @return string $pluginVersion;
   */
  public function getModuleVersion($moduleName = null) {

    $modules_table = Engine_Api::_()->getDbTable('modules', 'core');
    return $pluginVersion = $modules_table->select()
            ->from($modules_table->info('name'), array('version'))
            ->where('name = ?', $moduleName)
            ->query()
            ->fetchColumn();
  }

  //LICENSE WORK
  public function setContentItems($moduleName = null, $resourceType = null, $resourceId = null) {

    $coreApi = Engine_Api::_()->getApi('settings', 'core');
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $siteadvsearchGlobalType = $coreApi->getSetting('siteadvsearch.global.type', 0);
    $isSiteLocal = $coreApi->getSetting('siteadvsearch.is.quickview', 0);
    $getMemberLSettings = (string) $coreApi->getSetting('siteadvsearch.lsettings', false);

    if (!empty($resourceType) && !empty($resourceId)) {
      if ($coreApi->getSetting($moduleName . '.fs.markers', 1) && $contentItem->featured) {
        echo '<i title="' . $view->translate('Featured') . '" class="seaocore_list_featured_label">' . '</i>';
      }

      $this->getStatstics($contentItem, $moduleName);

      $url = $contentItem->getCategory()->getHref();
      echo '<div class="seaocore_browse_list_info_date seaocore_txt_light">' . '<a href="' . $url . '">' . $view->translate($contentItem->getCategory()->getTitle(true)) . '</a>' . '</div>';

      $user = Engine_Api::_()->user()->getUser($contentItem->owner_id);
      $view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed($resourceType, $user, 'contact_detail');
      $availableLabels = array('phone' => 'Phone', 'website' => 'Website', 'email' => 'Email');
      $options_create = array_intersect_key($availableLabels, array_flip($view_options));

      $isManageAdmin = Engine_Api::_()->$moduleName()->isManageAdmin($contentItem, 'contact');
      if (!empty($isManageAdmin)) {
        $contacts = '<div class="seaocore_browse_list_info_date">';
        $mailto = 'mailto:' . $contentItem->email;
        if (isset($options_create['phone']) && $options_create['phone'] == 'Phone') {
          if ($contentItem->phone) {
            $contacts .= $view->translate('Phone: ');
            $contacts .= $contentItem->phone;
          }
        }
        if (isset($options_create['email']) && $options_create['email'] == 'Email') {
          if ($contentItem->email) {
            if (!empty($contentItem->phone) && in_array("Phone", $options_create)) {
              $contacts .= ', ';
            }
            $contacts .= $view->translate('Email: ') . '<a href="' . $mailto . '">' . $contentItem->email . '</a>';
          }
        }
        if (isset($options_create['website']) && $options_create['website'] == 'Website') {
          if ($contentItem->website) {
            if (($contentItem->email && in_array("Email", $options_create)) || !empty($contentItem->phone) && in_array("Phone", $options_create)) {
              $contacts .= ',&nbsp';
            }
            $contacts .= $view->translate('Website: ');
            if (strstr($contentItem->website, 'http://') || strstr($contentItem->website, 'https://')) {
              $contacts .= '<a href="' . $contentItem->website . '" target="_blank">' . $contentItem->website . '</a>';
            } else {
              $contacts .= '<a href="http://"."' . $contentItem->website . '" target="_blank">' . $contentItem->website . '</a>';
            }
          }
        }
        echo $contacts . '</div>';
      }
    } else {
      $sitadvsearchExtType = @base64_decode("c2l0ZWFkdnNlYXJjaA==");
      $siteadvsearchExtInfoType = @base64_decode("MiwzLDUsOCwxMSwxMiwxNSwxNiwxOA==");
      $siteadvsearchExtInfoTypeArray = @explode(",", $siteadvsearchExtInfoType);
      $mobiAttempt = Engine_Api::_()->siteadvsearch()->getMemberAttempt();
      $getMobTypeInfo = $mobiAttempt . $sitadvsearchExtType;
      $getAppNumberFlag = 181449682 + 367983172 + 322465493;
      if (!empty($moduleName)) {
        $searchTable = Engine_Api::_()->getDbtable('search', 'core');
        $items = array();
        $text = $params['text'];
        if (!empty($text)) {
          $db = $searchTable->getAdapter();
          $select = $searchTable->select()
                  ->from($searchTable->info('name'), array('type', 'id', 'description'))
                  ->where("(`title` LIKE  '%$text%' OR `description` LIKE  '%$text%' OR `keywords` LIKE  '%$text%' OR `hidden` LIKE  '%$text%')");
          if (!empty($params['resource_type']) && $params['resource_type'] != 'all')
            $select->where('type = ?', $params['resource_type']);
          $select->order(new Zend_Db_Expr($db->quoteInto('MATCH(`title`, `description`, `keywords`, `hidden`) AGAINST (?) DESC', $text)));

          if (!empty($params['pagination'])) {
            return $items = Zend_Paginator::factory($select);
          } else {
            $select = $select->limit($params['limit']);
            return $items = $searchTable->fetchAll($select);
          }
        } else {
          return $items;
        }
      } else {
        if (empty($siteadvsearchGlobalType) && empty($isSiteLocal)) {
          $getExtTotalInfoFlag = 0;
          $extViewStr = null;
          foreach ($siteadvsearchExtInfoTypeArray as $extInfoType) {
            $extViewStr .= $getMemberLSettings[$extInfoType];
          }

          for ($mobExtTypeFlag = 0; $mobExtTypeFlag < strlen($getMobTypeInfo); $mobExtTypeFlag++) {
            $getExtTotalInfoFlag += @ord($getMobTypeInfo[$mobExtTypeFlag]);
          }

          $getExtTotalInfoFlag = (int) $getExtTotalInfoFlag;
          $getExtTotalInfoFlag = ($getExtTotalInfoFlag * (40 + 20 + 13)) + $getAppNumberFlag;
          $getExtTotalInfoFlag = (string) $getExtTotalInfoFlag;

          if ($extViewStr != $getExtTotalInfoFlag) {
            $getHostTypeArray = array();
            $requestListType = $coreApi->getSetting('siteadvsearch.request.types', false);
            if (!empty($requestListType)) {
              $getHostTypeArray = @unserialize($requestListType);
            }

            if (!array_key_exists($mobiAttempt, $getHostTypeArray)) {
              $getHostTypeArray[] = $mobiAttempt;
              $getHostTypeArray = @serialize(array_unique($getHostTypeArray));
              $coreApi->setSetting('siteadvsearch.request.types', $getHostTypeArray);
            }
            return true;
          }
        }
      }
    }
    return;
  }

  /**
   * Display Content Items
   *
   * @param object $contentItem
   * @param array $params
   */
  public function getCommonContentItem($contentItem, $params = array()) {

    $id = $params['content_type_id'];
    $moduleName = $params['module_name'];
    $coreApi = Engine_Api::_()->getApi('settings', 'core');
    $currency = $coreApi->getSetting('payment.currency', 'USD');
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    if ($coreApi->getSetting($moduleName . '.fs.markers', 1) && $contentItem->featured) {
      echo '<i title="' . $view->translate('Featured') . '" class="seaocore_list_featured_label">' . '</i>';
    }

    $this->getStatstics($contentItem, $moduleName);

    $showOptions = $this->getContentTypeOptions();
    if (in_array('category', $showOptions)) {
      $url = $contentItem->getCategory()->getHref();
      echo '<div class="seaocore_browse_list_info_date seaocore_txt_light">' . '<a href="' . $url . '">' . $view->translate($contentItem->getCategory()->getTitle(true)) . '</a>' . '</div>';
    }
    $user = Engine_Api::_()->user()->getUser($contentItem->owner_id);
    $view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed($params['resource_type'], $user, 'contact_detail');
    $availableLabels = array('phone' => 'Phone', 'website' => 'Website', 'email' => 'Email');
    $options_create = array_intersect_key($availableLabels, array_flip($view_options));

    $isManageAdmin = Engine_Api::_()->$moduleName()->isManageAdmin($contentItem, 'contact');
    if (!empty($isManageAdmin)) {
      $contacts = '<div class="seaocore_browse_list_info_date">';
      $mailto = 'mailto:' . $contentItem->email;
      if (isset($options_create['phone']) && $options_create['phone'] == 'Phone') {
        if ($contentItem->phone) {
          $contacts .= $view->translate('Phone: ');
          $contacts .= $contentItem->phone;
        }
      }
      if (isset($options_create['email']) && $options_create['email'] == 'Email') {
        if ($contentItem->email) {
          if (!empty($contentItem->phone) && in_array("Phone", $options_create)) {
            $contacts .= ', ';
          }
          $contacts .= $view->translate('Email: ') . '<a href="' . $mailto . '">' . $contentItem->email . '</a>';
        }
      }
      if (isset($options_create['website']) && $options_create['website'] == 'Website') {
        if ($contentItem->website) {
          if (($contentItem->email && in_array("Email", $options_create)) || !empty($contentItem->phone) && in_array("Phone", $options_create)) {
            $contacts .= ',&nbsp';
          }
          $contacts .= $view->translate('Website: ');
          if (strstr($contentItem->website, 'http://') || strstr($contentItem->website, 'https://')) {
            $contacts .= '<a href="' . $contentItem->website . '" target="_blank">' . $contentItem->website . '</a>';
          } else {
            $contacts .= '<a href="http://"."' . $contentItem->website . '" target="_blank">' . $contentItem->website . '</a>';
          }
        }
      }
      echo $contacts . '</div>';
    }

    if (!empty($contentItem->price) && $coreApi->getSetting($moduleName . '.price.field', 1)) {
      echo $view->translate("Price: ");
      echo $view->locale()->toCurrency($contentItem->price, $currency) . ' ';
    }
    if (in_array('location', $showOptions) && !empty($contentItem->location) && Engine_Api::_()->$moduleName()->enableLocation()) {
      $locationId = Engine_Api::_()->getDbTable('locations', $moduleName)->getLocationId($contentItem->$id, $contentItem->location);
      echo $view->translate("Location: ");
      echo $view->translate($contentItem->location);
      echo '&nbsp;- ' . '<b>' . $view->htmlLink(array('route' => 'seaocore_viewmap', 'id' => $contentItem->$id, 'resouce_type' => $params['resource_type'], 'location_id' => $locationId, 'flag' => 'map'), $view->translate("Get Directions"), array('onclick' => 'owner(this);return false')) . '</b>';
    }

    $view->addHelperPath(APPLICATION_PATH . '/application/modules/' . ucfirst($moduleName) . '/View/Helper', ucfirst($moduleName) . '_View_Helper');
    $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($contentItem);
    $params = array('custom_field_heading' => 0, 'custom_field_title' => 1, 'customFieldCount' => 5);
    if ($moduleName == 'sitepage') {
      $str = $view->pageProfileFieldValueLoop($contentItem, $fieldStructure, $params);

      if ($str) {
        echo $view->pageProfileFieldValueLoop($contentItem, $fieldStructure, $params);
      }
    } elseif ($moduleName == 'sitebusiness') {
      $str = $view->businessProfileFieldValueLoop($contentItem, $fieldStructure, $params);

      if ($str) {
        echo $view->businessProfileFieldValueLoop($contentItem, $fieldStructure, $params);
      }
    } elseif ($moduleName == 'sitegroup') {
      $str = $view->groupProfileFieldValueLoop($contentItem, $fieldStructure, $params);

      if ($str) {
        echo $view->groupProfileFieldValueLoop($contentItem, $fieldStructure, $params);
      }
    } elseif ($moduleName == 'sitestore') {
      $str = $view->profileFieldValueLoop($contentItem, $fieldStructure, $params);

      if ($str) {
        echo $view->profileFieldValueLoop($contentItem, $fieldStructure, $params);
      }
    }
  }

  /**
   * Get image type
   *
   * @param string $resourceType
   * @return $imageType
   */
  public function getDefaultPhoto($resourceType) {

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $shortType = $resourceType;
    if (strpos($shortType, '_') !== false) {
      list($null, $shortType) = explode('_', $shortType, 2);
    }
    $module = Engine_Api::_()->inflect(Engine_Api::_()->getItemModule($resourceType));
    $basePath = APPLICATION_PATH . "/application/modules/" . $module . "/externals/images/nophoto_" . $shortType;
    $file_path_profile = $basePath . '_' . 'thumb_profile' . '.png';
    $file_path_normal = $basePath . '_' . 'thumb_normal' . '.png';
    $file_path_icon = $basePath . '_' . 'thumb_icon' . '.png';
    if (@file_exists($file_path_profile))
      $imageType = 'thumb.profile';
    elseif (@file_exists($file_path_normal))
      $imageType = 'thumb.normal';
    elseif (@file_exists($file_path_icon))
      $imageType = 'thumb.icon';
    else
      $imageType = ' ';
    return $imageType;
  }

  /**
   * Display statstics of content type
   *
   * @param object $contentItem
   * @param string $moduleName
   */
  public function getStatstics($contentItem, $moduleName) {

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    $showStatics = '';
    $includedModules = $this->getIncludedModule();
    if (in_array($moduleName, $includedModules))
      $showStatics .= $this->getOwnerName($contentItem, $moduleName);

    $showStatics .= $this->getContentStatstics($contentItem);

    if ($moduleName == 'document') {
      $showOptions = $this->getContentTypeOptions();
      if (in_array('category', $showOptions)) {
        if ($contentItem->category_id) {
          $category = Engine_Api::_()->getDbtable('categories', 'document')->getCategory($contentItem->category_id);
          $showStatics .= $view->translate('Category: ') . "<a href='javascript:void(0);' onclick='javascript:categoryBrowseAction($contentItem->category_id);'>" . $category->category_name . '</a>';
        }
      }
    }

    $showStatics = rtrim($showStatics, ', ');
    $showStatics .= '</div>';
    echo $showStatics;
  }

  /**
   * Get module array
   *
   * @return module array
   */
  public function getIncludedModule() {

    return array('album', 'blog', 'classified', 'document', 'event', 'forum', 'poll', 'video', 'list', 'group', 'music', 'recipe', 'user', 'sitepage', 'sitepagenote', 'sitepagevideo', 'sitepagepoll', 'sitepagemusic', 'sitepagealbum', 'sitepageevent', 'sitepagereview', 'sitepagedocument', 'sitebusiness', 'sitebusinessalbum', 'sitebusinessdocument', 'sitebusinessevent', 'sitebusinessnote', 'sitebusinesspoll', 'sitebusinessmusic', 'sitebusinessvideo', 'sitebusinessreview', 'sitegroup', 'sitegroupalbum', 'sitegroupdocument', 'sitegroupevent', 'sitegroupnote', 'sitegrouppoll', 'sitegroupmusic', 'sitegroupvideo', 'sitegroupreview', 'sitestore', 'sitestoreproduct', 'feedback');
  }

  /**
   * Get content owner name
   *
   * @param object $contentItem
   * @param string $moduleName
   * @return $ownerTitle
   */
  public function getOwnerName($contentItem, $moduleName) {

    $showOptions = $this->getContentTypeOptions();
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    if ($moduleName != 'sitefaq' && $moduleName != 'sitepagealbum' && $moduleName != 'sitebusinessalbum' && $moduleName != 'sitegroupalbum') {
      if ($moduleName == 'document' || $moduleName == 'sitepagedocument' || $moduleName == 'sitebusinessdocument' || $moduleName == 'sitegroupdocument' || $moduleName == 'sitepagepoll' || $moduleName == 'sitebusinesspoll' || $moduleName == 'sitegroupppoll' || $moduleName == 'sitepagemusic' || $moduleName == 'sitebusinessmusic' || $moduleName == 'sitegroupmusic')
        $showPost = 'created by ';
      elseif ($moduleName == 'sitepageevent' || $moduleName == 'sitebusinessevent' || $moduleName == 'sitegroupevent')
        $showPost = $view->translate('Leb by ');
      else
        $showPost = $view->translate('posted by ');
      $ownerTitle = '<div class="seaocore_browse_list_info_date">';

      if (in_array('postedby', $showOptions) && isset($contentItem->creation_date)) {
        $ownerTitle .= $view->timestamp(strtotime($contentItem->creation_date)) . ' - ';
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting($moduleName . '.postedby', 1)) {
          $ownerTitle .= $view->translate($showPost) . $view->htmlLink($contentItem->getOwner()->getHref(), $contentItem->getOwner()->getTitle());
        };
        $ownerTitle .= ', ';
      }
    }
    return $ownerTitle;
  }

  /**
   * Get miscellaneous stastics of content type
   *
   * @param object $contentItem
   * @return $showStatics
   */
  public function getContentStatstics($contentItem) {

    $showOptions = $this->getContentTypeOptions();
    $showStatics = '';
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    if (in_array('commentcount', $showOptions) && isset($contentItem->comment_count)) {
      $showStatics .= $view->translate(array('%s comment', '%s comments', $contentItem->comment_count), $view->locale()->toNumber($contentItem->comment_count)) . ', ';
    }
    if (in_array('photocount', $showOptions) && isset($contentItem->photo_count)) {
      $showStatics .= $view->translate(array('%s photo', '%s photos', $contentItem->photo_count), $view->locale()->toNumber($contentItem->photo_count)) . ', ';
    }
    if (in_array('reviewcount', $showOptions) && isset($contentItem->review_count)) {
      $showStatics .= $view->translate(array('%s review', '%s reviews', $contentItem->review_count), $view->locale()->toNumber($contentItem->review_count)) . ', ';
    }
    if (in_array('viewcount', $showOptions) && isset($contentItem->view_count)) {
      $showStatics .= $view->translate(array('%s view', '%s views', $contentItem->view_count), $view->locale()->toNumber($contentItem->view_count)) . ', ';
    }
    if (isset($contentItem->event_id)) {
      $showStatics .= $view->translate(array('%s guest', '%s guests', $contentItem->membership()->getMemberCount()), $view->locale()->toNumber($contentItem->membership()->getMemberCount())) . ', ';
    }
    if (isset($contentItem->total_images)) {
      $showStatics .= $view->translate(array('%s picture', '%s pictures', $contentItem->total_images), $view->locale()->toNumber($contentItem->total_images)) . ', ';
    }
    if (isset($contentItem->vote_count)) {
      $showStatics .= $view->translate(array('%s vote', '%s votes', $contentItem->vote_count), $view->locale()->toNumber($contentItem->vote_count)) . ', ';
    }
    if (in_array('likecount', $showOptions) && isset($contentItem->like_count)) {
      $showStatics .= $view->translate(array('%s like', '%s likes', $contentItem->like_count), $view->locale()->toNumber($contentItem->like_count)) . ', ';
    }
    if (in_array('followercount', $showOptions) && isset($contentItem->follow_count)) {
      $showStatics .= $view->translate(array('%s follower', '%s followers', $contentItem->follow_count), $view->locale()->toNumber($contentItem->follow_count)) . ', ';
    }
    return $showStatics;
  }

  /**
   * Get pagination type for socialengine plugins
   *
   * @return $paginationType
   */
  public function getpaginationTypeValue() {

    $coreContent_table = Engine_Api::_()->getDbtable('content', 'core');
    $select = $coreContent_table->select()
            ->from('engine4_core_content', 'params')
            ->where('name = ?', 'siteadvsearch.search-contents');
    $params = $coreContent_table->fetchRow($select);
    if (isset($params['params']['show_content'])) {
      return $paginationType = $params['params']['show_content'];
    }
    else
      return $paginationType = 2;
  }

  /**
   * Get enabled content type items array
   *
   * @return $showStatstics
   */
  public function getContentTypeOptions() {

    $coreContent_table = Engine_Api::_()->getDbtable('content', 'core');
    $select = $coreContent_table->select()
            ->from('engine4_core_content', 'params')
            ->where('name = ?', 'siteadvsearch.search-contents');
    $params = $coreContent_table->fetchRow($select);
    if (isset($params['params']['show_statstics'])) {
      return $showStatstics = $params['params']['show_statstics'];
    }
    else
      return $showStatstics = '';
  }

  /**
   * Get widgetize page exist or not of advanced member
   *
   * @return int
   */
  public function getWidgetInfo() {

    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_pages')
            ->where('name = ?', 'siteadvsearch_index_browse-member')
            ->limit(1);
    $info = $select->query()->fetch();
    if (!empty($info))
      return 1;
    else
      return 0;
  }

  /**
   * Get content id
   *
   * @param array $params
   * @return $content_id
   */
  public function getContentId($params = array()) {

    $slug_plural = $params['slug_url'];
    $resourceTypekey = $params['resource_type_key'];
    $advancedContentTableName = 'engine4_siteadvsearch_contents';
    $db = Engine_Db_Table::getDefaultAdapter();
    $select = new Zend_Db_Select($db);
    if (empty($resourceTypekey)) {
      if (!empty($slug_plural)) {
        $listingtypeId = $select->from('engine4_sitereview_listingtypes', 'listingtype_id')
                ->where('slug_plural  = ?', $slug_plural)
                ->query()
                ->fetchcolumn();

        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_modules')
                ->where('name = ?', $params['module_name'])
                ->where('enabled = ?', 1);
        $isModuleEnabled = $select->query()->fetchObject();

        if (!empty($listingtypeId)) {
          $select = new Zend_Db_Select($db);
          return $content_id = $select->from($advancedContentTableName, 'content_id')
                  ->where('listingtype_id  = ?', $listingtypeId)
                  ->query()
                  ->fetchcolumn();
        } elseif ($isModuleEnabled) {
          $select = new Zend_Db_Select($db);
          return $content_id = $select->from($advancedContentTableName, 'content_id')
                  ->where('resource_type  = ?', $params['resource_type'])
                  ->query()
                  ->fetchcolumn();
        } else {
          return 0;
        }
      } else {
        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_modules')
                ->where('name = ?', $params['module_name'])
                ->where('enabled = ?', 1);
        $isModuleEnabled = $select->query()->fetchObject();

        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_modules')
                ->where('name = ?', $params['core_module_name'])
                ->where('enabled = ?', 1);
        $isCoreModuleEnabled = $select->query()->fetchObject();

        if (!empty($isModuleEnabled)) {
          if ($params['module_name'] == 'sitegroup')
            $resource_type = 'sitegroup_group';
          elseif ($params['module_name'] == 'siteevent')
            $resource_type = 'siteevent_event';
          $select = new Zend_Db_Select($db);
          return $content_id = $select->from($advancedContentTableName, 'content_id')
                  ->where('resource_type  = ?', $resource_type)
                  ->query()
                  ->fetchcolumn();
        }
        elseif ($isCoreModuleEnabled) {
          if ($params['core_module_name'] == 'group')
            $resource_type = 'group';
          elseif ($params['core_module_name'] == 'event')
            $resource_type = 'event';
          $select = new Zend_Db_Select($db);
          return $content_id = $select->from($advancedContentTableName, 'content_id')
                  ->where('resource_type  = ?', $resource_type)
                  ->query()
                  ->fetchcolumn();
        }
        else {
          return 0;
        }
      }
    } else {
      $select = new Zend_Db_Select($db);
      $select->from('engine4_core_modules')
              ->where('name = ?', $params['module_name'])
              ->where('enabled = ?', 1);
      $isModuleEnabled = $select->query()->fetchObject();

      if (!empty($isModuleEnabled)) {
        $select = new Zend_Db_Select($db);
        return $content_id = $select->from($advancedContentTableName, 'content_id')
                ->where('resource_type  = ?', $resourceTypekey)
                ->query()
                ->fetchcolumn();
      }
    }
  }

  /**
   * Check content type is searchable or not
   *
   */
  public function getSearchableContent() {

    $item = Engine_Api::_()->core()->getSubject();
    $searchTable = Engine_Api::_()->getDbtable('search', 'core');
    $itemSearchable = $item->isSearchable();

    if (empty($itemSearchable)) {
      Engine_Api::_()->getApi('search', 'core')->unindex($item);
      return;
    }

    $isInsert = 1;

    switch ($item->getType()) {

      case 'siteevent_event':
        $currentDate = date("Y-m-d H:i:s");
        $lastOccurrenceEndDate = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($item->event_id, 'DESC');
        if ($lastOccurrenceEndDate < $currentDate) {
          Engine_Api::_()->getApi('search', 'core')->unindex($item);
          $isInsert = 0;
        }
        break;
      case 'sitereview_listing':
        $expirySettings = Engine_Api::_()->sitereview()->expirySettings($item->listingtype_id);

        if ($expirySettings == 2) {
          $approveDate = Engine_Api::_()->sitereview()->adminExpiryDuration($item->listingtype_id);
          if ($item->approved_date < $approveDate)
            Engine_Api::_()->getApi('search', 'core')->unindex($item);
          $isInsert = 0;
        }
        break;
      case 'list_listing':
        $expirySettings = Engine_Api::_()->list()->expirySettings();

        if ($expirySettings == 2) {
          $approveDate = Engine_Api::_()->list()->adminExpiryDuration();
          if ($item->approved_date < $approveDate)
            Engine_Api::_()->getApi('search', 'core')->unindex($item);
          $isInsert = 0;
        }
        break;
      case 'blog':
        if (!empty($item->draft)) {
          Engine_Api::_()->getApi('search', 'core')->unindex($item);
          $isInsert = 0;
        }
        break;
      case 'classified':
        if (!empty($item->closed)) {
          Engine_Api::_()->getApi('search', 'core')->unindex($item);
          $isInsert = 0;
        }
        break;
      case 'event':
        if ($item->endtime < date("Y-m-d H:i:s")) {
          Engine_Api::_()->getApi('search', 'core')->unindex($item);
          $isInsert = 0;
        }
        break;
      case 'poll':
        if ($item->closed) {
          Engine_Api::_()->getApi('search', 'core')->unindex($item);
          $isInsert = 0;
        }
        break;
      case 'user':
        if (empty($item->approved) || empty($item->verified) || empty($item->enabled) || empty($item->search)) {
          Engine_Api::_()->getApi('search', 'core')->unindex($item);
          $isInsert = 0;
        }
        break;
    }

    if (!empty($isInsert))
      Engine_Api::_()->getApi('search', 'core')->index($item);
  }

  /**
   * Get modules version
   *
   * @return $finalModules
   */
  public function isModulesSupport() {
    $modArray = array(
        'list' => '4.8.6',
        'recipe' => '4.8.6',
        'siteevent' => '4.8.6',
        'sitefaq' => '4.8.6',
        'sitereview' => '4.8.6',
        'document' => '4.8.6',
        'feedback' => '4.8.6',
        'sitealbum' => '4.8.6',
        'sitemenu' => '4.8.6',
        'sitepage' => '4.8.6',
        'sitepagedocument' => '4.8.6',
        'sitepageoffer' => '4.8.6',
        'sitepagevideo' => '4.8.6',
        'sitepageevent' => '4.8.6',
        'sitepagepoll' => '4.8.6',
        'sitebusiness' => '4.8.6',
        'sitebusinessdocument' => '4.8.6',
        'sitebusinessoffer' => '4.8.6',
        'sitebusinessvideo' => '4.8.6',
        'sitebusinessevent' => '4.8.6',
        'sitebusinesspoll' => '4.8.6',
        'sitegroup' => '4.8.6',
        'sitegroupdocument' => '4.8.6',
        'sitegroupoffer' => '4.8.6',
        'sitegroupvideo' => '4.8.6',
        'sitegroupevent' => '4.8.6',
        'sitegrouppoll' => '4.8.6'
    );
    $finalModules = array();
    foreach ($modArray as $key => $value) {
      $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($key);
      if (!empty($isModEnabled)) {
        $getModVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule($key);
        $isModSupport = $this->checkVersion($getModVersion->version, $value);
        if (empty($isModSupport)) {
          $finalModules[] = $getModVersion->title;
        }
      }
    }
    return $finalModules;
  }
    public function checkVersion($databaseVersion, $checkDependancyVersion) {
        $f = $databaseVersion;
        $s = $checkDependancyVersion;
        if (strcasecmp($f, $s) == 0)
            return -1;

        $fArr = explode(".", $f);
        $sArr = explode('.', $s);
        if (count($fArr) <= count($sArr))
            $count = count($fArr);
        else
            $count = count($sArr);

        for ($i = 0; $i < $count; $i++) {
            $fValue = $fArr[$i];
            $sValue = $sArr[$i];
            if (is_numeric($fValue) && is_numeric($sValue)) {
                if ($fValue > $sValue)
                    return 1;
                elseif ($fValue < $sValue)
                    return 0;
                else {
                    if (($i + 1) == $count) {
                        return -1;
                    } else
                        continue;
                }
            }
            elseif (is_string($fValue) && is_numeric($sValue)) {
                $fsArr = explode("p", $fValue);

                if ($fsArr[0] > $sValue)
                    return 1;
                elseif ($fsArr[0] < $sValue)
                    return 0;
                else {
                    return 1;
                }
            } elseif (is_numeric($fValue) && is_string($sValue)) {
                $ssArr = explode("p", $sValue);

                if ($fValue > $ssArr[0])
                    return 1;
                elseif ($fValue < $ssArr[0])
                    return 0;
                else {
                    return 0;
                }
            } elseif (is_string($fValue) && is_string($sValue)) {
                $fsArr = explode("p", $fValue);
                $ssArr = explode("p", $sValue);
                if ($fsArr[0] > $ssArr[0])
                    return 1;
                elseif ($fsArr[0] < $ssArr[0])
                    return 0;
                else {
                    if ($fsArr[1] > $ssArr[1])
                        return 1;
                    elseif ($fsArr[1] < $ssArr[1])
                        return 0;
                    else {
                        return -1;
                    }
                }
            }
        }
    }
  /**
   * Check privacy of conten type
   *
   * @params string $resource_type
   * @params int $listingTypeId
   * @return int
   */
  public function canViewItemType($resource_type, $listingTypeId = 0) {

    if($resource_type == 'sitehashtag_hashtag' && Engine_Api::_()->hasModuleBootstrap('sitehashtag')) {
        return 1;
    }  
      
    if($resource_type == 'sitevideo_video')
        $resource_type = 'video';
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    //GET USER LEVEL ID
    if (!empty($viewer_id)) {
      $level_id = $viewer->level_id;
    } else {
      $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
    }

    if ($resource_type == "sitereview_listingtype_$listingTypeId") {
      $viewType = "view_listtype_$listingTypeId";
      $resourceType = 'sitereview_listing';
    } else {
      $viewType = 'view';
      $resourceType = $resource_type;
    }

    $create_level_allow = Engine_Api::_()->authorization()->getPermission($level_id, $resourceType, $viewType);

    if (!$create_level_allow)
      return 0;
    else
      return 1;
  }
    /**
     * Get Widgetized PageId
     * @param $params
     */
    public function getWidgetizedPageId($params = array()) {
        //GET CORE CONTENT TABLE
        $tableNamePages = Engine_Api::_()->getDbtable('pages', 'core');
        $page_id = $tableNamePages->select()
                ->from($tableNamePages->info('name'), 'page_id')
                ->where('name =?', $params['name'])
                ->query()
                ->fetchColumn();
        return $page_id;
    }
}