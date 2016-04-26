<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Widget_RecentlyPopularRandomSiteeventController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      //SITEMOBILE CODE
      $this->view->isajax = $this->_getParam('isajax', false);
      if ($this->view->isajax) {
        $this->getElement()->removeDecorator('Title');
        $this->getElement()->removeDecorator('Container');
      }
      $this->view->viewmore = $this->_getParam('viewmore', false);
      $this->view->is_ajax_load = true;
      
      if ($this->_getParam('detactLocation', 0)){
        $this->view->is_mobile_ajax = true;
      }else{      
        $this->view->is_mobile_ajax = false;
      }
      $this->view->renderDefault = $this->_getParam('renderDefault', 1);
    }
    
    if ($this->_getParam('is_ajax_load', false)) {
      $this->view->is_ajax_load = true;
      if (!$this->_getParam('detactLocation', 0) || $this->_getParam('contentpage', 1) > 1)
        $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    } else {
      if ($this->_getParam('detactLocation', 0))
        $this->getElement()->removeDecorator('Title');

      $this->view->is_ajax_load = !$this->_getParam('loaded_by_ajax', true);
    }

    $this->view->titlePosition = $this->_getParam('titlePosition', 1);
    $this->view->showViewMore = $this->_getParam('showViewMore', 1);
    $this->view->titleLink = $this->_getParam('titleLink', '');
    $this->view->params = $params = $this->_getAllParams();
    $params['limit'] = $limit = $this->_getParam('limit', 12);
    $this->view->statistics = $params['statistics'] = $this->_getParam('eventInfo', array("viewCount", "likeCount", "commentCount", "memberCount", "reviewCount"));
//        $this->view->showContent = $params['showContent'] = $this->_getParam('showContent', array("price", "location"));
    $params['showEventType'] = $this->view->showEventType = $this->_getParam('showEventType', 'upcoming');

    //GET CORE API
    $this->view->settings = Engine_Api::_()->getApi('settings', 'core');
    $this->view->siteeventTabLocation = $siteeventTabLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventtab.location', 1);

    $this->view->is_ajax = $isAjax = $this->_getParam('is_ajax', 0);
    if (empty($isAjax)) {
      $showTabArray = $params['ajaxTabs'] = $this->_getParam('ajaxTabs', array("upcoming", "most_reviewed", "most_popular", "featured", "sponsored", "most_joined", "this_month", "this_week", "this_weekend", "today"));

      if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
        if ($showTabArray) {
          foreach ($showTabArray as $key => $value)
            $showTabArray[$key] = str_replace("ZZZ", "_", $value);
        } else {
          $showTabArray = array();
        }

        $this->view->tabs = $showTabArray;
        $this->view->tabCount = count($showTabArray);
        if (empty($this->view->tabCount)) {
          return $this->setNoRender();
        }
        $this->view->tabs = $showTabArray = $this->setTabsOrder($showTabArray);
      }
    } else {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }

    $layouts_views = $params['layouts_views'] = $this->_getParam('layouts_views', array("list_view", "grid_view", "map_view"));

    foreach ($layouts_views as $key => $value)
      $layouts_views[$key] = str_replace("ZZZ", "_", $value);

    $this->view->layouts_views = $layouts_views;
    $this->view->defaultLayout = str_replace("ZZZ", "_", $this->_getParam('defaultOrder', 'list_view'));
    //$this->_getParam('defaultOrder', 'list_view');

    $siteeventTable = Engine_Api::_()->getDbTable('events', 'siteevent');
    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

    $this->view->category_id = 0;
    $this->view->subcategory_id = 0;
    $this->view->subsubcategory_id = 0;

    $this->view->category_id = $params['category_id'] = $this->_getParam('hidden_category_id');
    $this->view->subcategory_id = $params['subcategory_id'] = $this->_getParam('hidden_subcategory_id');
    $this->view->subsubcategory_id = $params['subsubcategory_id'] = $this->_getParam('hidden_subsubcategory_id');

    $this->view->ratingType = $params['ratingType'] = $this->_getParam('ratingType', 'rating_avg');
    $paramsContentType = $this->_getParam('content_type', null);
    $this->view->content_type = $paramsContentType = $paramsContentType ? $paramsContentType : $showTabArray[0];

    $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);
    if ($this->view->detactLocation) {
      $this->view->detactLocation = Engine_Api::_()->siteevent()->enableLocation();
    }
    if ($this->view->detactLocation) {
      $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
      $params['latitude'] = $this->_getParam('latitude', 0);
      $params['longitude'] = $this->_getParam('longitude', 0);
    }

    if (empty($isAjax) && empty($this->view->detactLocation)) {
      //GET LISTS
      $eventCount = $siteeventTable->hasEvents();

      if (empty($eventCount)) {
        return $this->setNoRender();
      }
    }
    if (in_array('map_view', $layouts_views)) {
      if (!Engine_Api::_()->siteevent()->enableLocation()) {
        if (Count($layouts_views) == 1 && in_array('map_view', $layouts_views)) {
          return $this->setNoRender();
        }
        unset($layouts_views[array_search('map_view', $layouts_views)]);
        $this->view->layouts_views = $layouts_views;
      }
    }

    $params['eventType'] = $contentType = Zend_Controller_Front::getInstance()->getRequest()->getParam('eventType', null);
    if (empty($contentType)) {
      $params['eventType'] = $this->_getParam('eventType', 'All');
    }
    $this->view->contentType = $params['eventType'];

    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      $params['ajaxTabs'] = $this->_getParam('ajaxTabs', 'upcoming');
      $paramsContentType =  str_replace("ZZZ", "_", $params['ajaxTabs']);
    }

    if (empty($siteeventTabLocation))
      return $this->setNoRender();
    $this->view->columnWidth = $values['columnWidth'] = $this->_getParam('columnWidth', '180');
    $this->view->columnHeight = $values['columnHeight'] = $this->_getParam('columnHeight', '328');
    $this->view->title_truncationList = $values['truncationList'] = $this->_getParam('truncationList', 600);
    $this->view->title_truncationGrid = $values['truncationGrid'] = $this->_getParam('truncationGrid', 90);
    $this->view->truncationLocation = $values['truncationLocation'] = $this->_getParam('truncationLocation', 35);
    $this->view->listViewType = $values['listViewType'] = $this->_getParam('listViewType', 'list');
    $this->view->paramsLocation = array_merge($params, $values);
    $this->view->enableLocation = $checkLocation = Engine_Api::_()->siteevent()->enableLocation();
    $this->view->is_ajax_load = Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode') ? $this->view->is_ajax_load : true;
    if (!$this->view->is_ajax_load)
      return; 

    $this->view->paginator = $paginator = $siteeventRecently = $siteeventTable->getEvent($paramsContentType, $params);
    $this->view->totalCount = $paginator->getTotalItemCount();

    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      //SITEMOBILE CODE
      $this->view->isajax = $this->_getParam('isajax', 0);
      $this->view->identity = $params['identity'] = $this->_getParam('identity', $this->view->identity);
      $this->view->statistics = $params['statistics'] = $this->_getParam('eventInfo', array("hostName", "location", "startDate"));
      $this->view->columnWidth = $this->_getParam('columnWidth', '180');
      $this->view->columnHeight = $this->_getParam('columnHeight', '290');
      $this->view->title_truncation = $this->_getParam('truncation', 25);
      $this->view->title_truncationGrid = $this->_getParam('truncationGrid', 25);
      $this->view->ratingType = $this->_getParam('ratingType', 'rating_both');
      $this->view->layouts_views = $params['layouts_views'] = $this->_getParam('layouts_views', array("listview", "gridview"));

      $params['page'] = $this->_getParam('page', 1);
      $params['paginator'] = true;
      $params['totalpages'] = $this->view->totalCount;
      $paginator->setItemCountPerPage($limit);
      $this->view->paginator = $paginator->setCurrentPageNumber($params['page']);
      $this->view->params = $params;
      $this->view->viewType = $this->_getParam('view_selected', $this->_getParam('viewType', 'gridview'));
      $this->view->view_selected = $this->view->viewType;
      $reqview_selected = Zend_Controller_Front::getInstance()->getRequest()->getParam('view_selected');
      if ($reqview_selected && count($this->view->layouts_views) > 1) {
        $this->view->view_selected = $reqview_selected;
        //$this->view->formValuesSM['view_selected'] = $reqview_selected;
      }
    }

    $this->view->locations = array();
    if (in_array('map_view', $layouts_views)) {
      
      if ($checkLocation) {
        $event_ids = array();
        $locationEvent = array();
        $this->view->flagSponsored = $this->view->settings->getSetting('siteevent.map.sponsored', 1);
        foreach ($paginator as $item) {
          if ($item->location) {
            $event_ids[] = $item->event_id;
            $locationEvent[$item->event_id] = $item;
          }
        }

        if (count($event_ids) > 0) {
          $values['event_ids'] = $event_ids;
          $this->view->locations = $locations = Engine_Api::_()->getDbtable('locations', 'siteevent')->getLocation($values);
          $this->view->locationsEvent = $locationEvent;
        }
      }
    }
    $this->view->params['shareOptions'] = $this->_getParam('shareOptions', 0);
  }

  public function setTabsOrder($tabs) {

    $tabsOrder['upcoming'] = $this->_getParam('upcoming_order', 1);
    $tabsOrder['most_reviewed'] = $this->_getParam('reviews_order', 2);
    $tabsOrder['most_popular'] = $this->_getParam('popular_order', 3);
    $tabsOrder['featured'] = $this->_getParam('featured_order', 4);
    $tabsOrder['sponsored'] = $this->_getParam('sponosred_order', 5);
    $tabsOrder['most_joined'] = $this->_getParam('joined_order', 6);
    $tabsOrder['this_month'] = $this->_getParam('month_order', 7);
    $tabsOrder['this_week'] = $this->_getParam('week_order', 8);
    $tabsOrder['this_weekend'] = $this->_getParam('weekend_order', 9);
    $tabsOrder['today'] = $this->_getParam('today_order', 10);

    $tempTabs = array();
    foreach ($tabs as $tab) {
      $order = $tabsOrder[$tab];
      if (isset($tempTabs[$order]))
        $order++;
      $tempTabs[$order] = $tab;
    }
    ksort($tempTabs);
    $orderTabs = array();
    $i = 0;
    foreach ($tempTabs as $tab)
      $orderTabs[$i++] = $tab;

    return $orderTabs;
  }

}
