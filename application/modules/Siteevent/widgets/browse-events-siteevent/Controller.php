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
class Siteevent_Widget_BrowseEventsSiteeventController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      //SITEMOBILE CODE
      $this->view->isajax = $this->_getParam('isajax', false);
      $ajax = $this->_getParam('ajax', false);
      if ($this->view->isajax || $ajax) {
        $this->getElement()->removeDecorator('Title');
        $this->getElement()->removeDecorator('Container');
      }
      $this->view->viewmore = $this->_getParam('viewmore', false);
//      $this->view->is_ajax_load = true;
    }
    $is_ajax_load = $params['is_ajax_load'] = $this->_getParam('is_ajax_load', false);
    $is_ajax_load = $params['is_ajax_load'] = $this->_getParam('is_ajax_load', false);
    $contentPage = $params['contentpage'] = $this->_getParam('contentpage', 1);
    
    if ($is_ajax_load) {
      $this->view->is_ajax_load = true;
      if ($contentPage > 1)
        $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    } else {
      if (!$detectLocation) {
        $this->view->is_ajax_load = true;
      } else {
        $this->getElement()->removeDecorator('Title');
      }
    }
    
    if(empty($this->view->is_ajax_load)) {
        $cookieLocation = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie(); 
        if(isset($cookieLocation['location']) && !empty($cookieLocation['location'])) {
            $this->view->is_ajax_load = 1;
        }
    }

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $params = $request->getParams();
    //GET VIEWER DETAILS
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer->getIdentity();

    //GET SETTINGS
    $this->view->is_ajax = $isAjax = $this->_getParam('is_ajax', 0);
    $this->view->paginationType = $this->_getParam('show_content', 2);
    $this->view->titlePosition = $params['titlePosition'] = $this->_getParam('titlePosition', 1);
    $this->view->allParams = $this->_getAllParams();
    $this->view->identity = $this->view->allParams['identity'] = $params['identity'] = $this->_getParam('identity', $this->view->identity);
    $ShowViewArray = $params['layouts_views'] = $this->_getParam('layouts_views', array("0" => "1", "1" => "2", "2" => "3"));
    $this->view->isajax = $params['isajax'] = $this->_getParam('isajax', 0);
    $this->view->viewType = $params['viewType'] = $this->_getParam('viewType', '');
    $this->view->statistics = $params['eventInfo'] = $this->_getParam('eventInfo', array("viewCount", "likeCount", "commentCount", "memberCount", "reviewCount"));
    $defaultOrder = $this->view->defaultOrder = $params['layouts_order'] = $this->_getParam('layouts_order', 2);
    if (empty($this->view->viewType)) {
      if ($defaultOrder == 1)
        $this->view->viewType = 'listview';
      else
        $this->view->viewType = 'gridview';
    }
    $this->view->ratingType = $params['ratingType'] = $this->_getParam('ratingType', 'rating_both');
    $this->view->title_truncation = $params['truncation'] = $this->_getParam('truncation', 25);
    $this->view->truncationLocation = $params['truncationLocation'] = $this->_getParam('truncationLocation', 35);
    $this->view->title_truncationGrid = $params['truncationGrid'] = $this->_getParam('truncationGrid', 90);
    $this->view->showContent = $params['showContent'] = $this->_getParam('showContent', array("price", "location"));
    $this->view->list_view = 0;
    $this->view->grid_view = 0;
    $this->view->map_view = 0;
    $this->view->defaultView = -1;
    $siteeventBrowseDefaultView = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventbrowse.defaultview', 1);
    if (empty($siteeventBrowseDefaultView))
      return $this->setNoRender();

    if (in_array("1", $ShowViewArray)) {
      $this->view->list_view = 1;
      if ($this->view->defaultView == -1 || $defaultOrder == 1)
        $this->view->defaultView = 0;
    }
    if (in_array("2", $ShowViewArray)) {
      $this->view->grid_view = 1;
      if ($this->view->defaultView == -1 || $defaultOrder == 2)
        $this->view->defaultView = 1;
    }
    if (in_array("3", $ShowViewArray)) {
      $this->view->map_view = 1;
      if ($this->view->defaultView == -1 || $defaultOrder == 3)
        $this->view->defaultView = 2;
    }

    if ($this->view->defaultView == -1) {
      return $this->setNoRender();
    }
    $customFieldValues = array();
    $values = array();

    $siteeventBrowseEvents = Zend_Registry::isRegistered('siteeventBrowseEvents') ? Zend_Registry::get('siteeventBrowseEvents') : null;

    $this->view->params = $params;
    if (!isset($params['category_id']))
      $params['category_id'] = 0;
    if (!isset($params['subcategory_id']))
      $params['subcategory_id'] = 0;
    if (!isset($params['subsubcategory_id']))
      $params['subsubcategory_id'] = 0;
    $this->view->category_id = $params['category_id'];
    $this->view->subcategory_id = $params['subcategory_id'];
    $this->view->subsubcategory_id = $params['subsubcategory_id'];

    //SHOW CATEGORY NAME
    $this->view->categoryName = '';
    if ($this->view->category_id) {
      $this->view->categoryName = Engine_Api::_()->getItem('siteevent_category', $this->view->category_id)->category_name;
      $this->view->categoryObject = Engine_Api::_()->getItem('siteevent_category', $this->view->category_id);

      if ($this->view->subcategory_id) {
        $this->view->categoryName = Engine_Api::_()->getItem('siteevent_category', $this->view->subcategory_id)->category_name;
        $this->view->categoryObject = Engine_Api::_()->getItem('siteevent_category', $this->view->subcategory_id);

        if ($this->view->subsubcategory_id) {
          $this->view->categoryName = Engine_Api::_()->getItem('siteevent_category', $this->view->subsubcategory_id)->category_name;
          $this->view->categoryObject = Engine_Api::_()->getItem('siteevent_category', $this->view->subsubcategory_id);
        }
      }
    }

    if (empty($siteeventBrowseEvents))
      return $this->setNoRender();

    if (!empty($this->view->statistics) && !Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) || Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 1) {
      $key = array_search('reviewCount', $this->view->statistics);
      if (!empty($key)) {
        unset($this->view->statistics[$key]);
      }
    }

    $this->view->current_page = $page = 1;
    if (isset($params['page']) && !empty($params['page'])) {
      $this->view->current_page = $page = $params['page'];
    }
    $this->view->allParams['page'] = $this->view->current_page;

    //GET VALUE BY POST TO GET DESIRED EVENTS
    if (!empty($params)) {
      $values = array_merge($values, $params);
    }

    //FORM GENERATION
    $form = new Siteevent_Form_Search(array('type' => 'siteevent_event'));

    if (!empty($params)) {
      $form->populate($params);
    }

    $this->view->formValues = $form->getValues();

    $values = array_merge($values, $form->getValues());

    $values['page'] = $page;

    //GET LISITNG FPR PUBLIC PAGE SET VALUE
    $values['type'] = 'browse';

    if (@$values['show'] == 2) {

      //GET AN ARRAY OF FRIEND IDS
      $friends = $viewer->membership()->getMembers();

      $ids = array();
      foreach ($friends as $friend) {
        $ids[] = $friend->user_id;
      }

      $values['users'] = $ids;
    }

    $this->view->assign($values);

    //CORE API
    $this->view->settings = $settings = Engine_Api::_()->getApi('settings', 'core');

    //CUSTOM FIELD WORK
    $customFieldValues = array_intersect_key($values, $form->getFieldElements());
    if ($form->show->getValue() == 3 && !isset($_GET['show'])) {
      @$values['show'] = 3;
    }

    $values['orderby'] = $orderBy = $request->getParam('orderby', null);
    if (empty($orderBy)) {
      $values['orderby'] = $params['orderby'] = $this->_getParam('orderby', 'starttime');
    }
    $this->view->allParams['orderby'] = $values['orderby'];

    $values['eventType'] = $contentType = $request->getParam('eventType', null);
    if (empty($contentType)) {
      $values['eventType'] = $params['eventType'] = $this->_getParam('eventType', 'All');
    }
    $this->view->contentType = $this->view->allParams['eventType'] = $values['eventType'];

    $this->view->limit = $values['limit'] = $itemCount = $params['itemCount'] = $this->_getParam('itemCount', 10);
    $this->view->bottomLine = $params['bottomLine'] = $this->_getParam('bottomLine', 1);
    $this->view->bottomLineGrid = $params['bottomLineGrid'] = $this->_getParam('bottomLineGrid', 2);
    $values['viewType'] = $this->view->viewType;

    $values['showClosed'] = $params['showClosed'] = $this->_getParam('showClosed', 1);

    if ($request->getParam('titleAjax')) {
      $values['search'] = $request->getParam('titleAjax');
    }

    $this->view->detactLocation = $values['detactLocation'] = $params['detactLocation'] = $this->_getParam('detactLocation', 0);
    if ($this->view->detactLocation) {
      $this->view->detactLocation = Engine_Api::_()->siteevent()->enableLocation();
    }
    if ($this->view->detactLocation) {
      $this->view->defaultLocationDistance = $values['defaultLocationDistance'] = $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
      $values['Latitude'] = $values['latitude'] = $params['latitude'] = $this->_getParam('latitude', 0);
      $values['Longitude'] = $values['longitude'] = $params['longitude'] = $this->_getParam('longitude', 0);
    }

    $this->view->showEventType = $values['action'] = $request->getParam('showEventType', 'upcoming');

    if (empty($values['category_id'])) {
      $this->view->category_id = $values['category_id'] = $params['hidden_category_id'] = $this->_getParam('hidden_category_id');
      $values['subcategory_id'] = $params['hidden_subcategory_id'] = $this->_getParam('hidden_subcategory_id');
      $values['subsubcategory_id'] = $params['hidden_subsubcategory_id'] = $this->_getParam('hidden_subsubcategory_id');
    }

    $values['orderbystarttime'] = 1;
    
    if(!$this->view->detactLocation && empty($_GET['location']) && isset($values['location'])) {
        unset($values['location']);
        
        if(empty($_GET['latitude']) && isset($values['latitude'])) {
            unset($values['latitude']);
        }
        
        if(empty($_GET['longitude']) && isset($values['longitude'])) {
            unset($values['longitude']);
        }        
        
        if(empty($_GET['Latitude']) && isset($values['Latitude'])) {
            unset($values['Latitude']);
        }
        
        if(empty($_GET['Longitude']) && isset($values['Longitude'])) {
            unset($values['Longitude']);
        }            
    }
    
    if(isset($values['starttimesearchsiteevent'])) {
        $values['starttime'] = $values['starttimesearchsiteevent'];
        unset($values['starttimesearchsiteevent']);
    }
    
    if(isset($values['endtimesearchsiteevent'])) {
        $values['endtime'] = $values['endtimesearchsiteevent'];
        unset($values['endtimesearchsiteevent']);
    }
    
    // GET EVENTS
    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('events', 'siteevent')->getSiteeventsPaginator($values, $customFieldValues);
    $paginator->setItemCountPerPage($itemCount);
    $this->view->paginator = $paginator->setCurrentPageNumber($values['page']);
    $this->view->totalResults = $paginator->getTotalItemCount();

    $this->view->enableLocation = $checkLocation = Engine_Api::_()->siteevent()->enableLocation();
    $this->view->flageSponsored = 0;
    $this->view->totalCount = $paginator->getTotalItemCount();
    if (!empty($checkLocation) && $paginator->getTotalItemCount() > 0) {
      $ids = array();
      $sponsored = array();
      foreach ($paginator as $event) {
        $id = $event->getIdentity();
        $ids[] = $id;
        $event_temp[$id] = $event;
      }
      $values['event_ids'] = $ids;
      $this->view->locations = $locations = Engine_Api::_()->getDbtable('locations', 'siteevent')->getLocation($values);

      foreach ($locations as $location) {
        if ($event_temp[$location->event_id]->sponsored) {
          $this->view->flageSponsored = 1;
          break;
        }
      }
      $this->view->siteevent = $event_temp;
    } else {
      $this->view->enableLocation = 0;
    }

    $this->view->search = 0;
    if (!empty($this->_getAllParams) && Count($this->_getAllParams) > 1) {
      $this->view->search = 1;
    }

    //SEND FORM VALUES TO TPL
    $this->view->formValues = $values;

    //CAN CREATE PAGES OR NOT
    $this->view->can_create = Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, "create");
    $this->view->ratingTable = Engine_Api::_()->getDbtable('ratings', 'siteevent');
    $this->view->columnWidth = $params['columnWidth'] = $this->_getParam('columnWidth', '180');
    $this->view->columnHeight = $params['columnHeight'] = $this->_getParam('columnHeight', '328');

    $this->view->paramsLocation = array_merge($_GET, $this->_getAllParams());
    $this->view->paramsLocation = array_merge($request->getParams(), $this->view->paramsLocation);

    $this->view->allParams['eventType'] = $this->view->eventType = $params['eventType'] = $this->_getParam('eventType', $this->view->viewType);
    $this->view->viewmore = $params['viewmore'] = $this->_getParam('viewmore', false);
    if (isset($_GET['search']) || isset($_POST['search'])) {
      $this->view->detactLocation = 0;
    } else {
      $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);
    }
    // = ;

    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      //SITEMOBILE CODE
      $this->view->isajax = $this->_getParam('isajax', 0);
      $this->view->identity = $values['identity'] = $this->_getParam('identity', $this->view->identity);
      $this->view->statistics = $this->_getParam('eventInfo', array("hostName", "location", "startDate"));
      $this->view->columnWidth = $this->_getParam('columnWidth', '180');
      $this->view->columnHeight = $this->_getParam('columnHeight', '225');
      $this->view->title_truncation = $this->_getParam('truncation', 25);
      $this->view->title_truncationGrid = $this->_getParam('truncationGrid', 25);
      $this->view->ratingType = $this->_getParam('ratingType', 'rating_both');
      $this->view->layouts_views = $values['layouts_views'] = $this->_getParam('layouts_views', array("1", "2"));
      //    $this->view->params = $values;
      $this->view->totalCount = $paginator->getTotalItemCount();
      $this->view->viewType = $this->_getParam('viewType', 'gridview');

      $this->view->view_selected = $this->_getParam('viewType', 'gridview');
      $reqview_selected = Zend_Controller_Front::getInstance()->getRequest()->getParam('view_selected');
      if ($reqview_selected && count($this->view->layouts_views) > 1) {
        $this->view->view_selected = $reqview_selected;
        $formValuesSM = $this->view->formValues;
        $formValuesSM['view_selected'] = $reqview_selected;
        $this->view->formValuesSM = $formValuesSM;
      }
    }
       //SCROLLING PARAMETERS SEND
    if(Engine_Api::_()->seaocore()->isSitemobileApp()) {  
      //SET SCROLLING PARAMETTER FOR AUTO LOADING.
      if (!Zend_Registry::isRegistered('scrollAutoloading')) {      
        Zend_Registry::set('scrollAutoloading', array('scrollingType' => 'up'));
      }
    }
    $this->view->page = $params['page'] = $this->_getParam('page', 1);
    $this->view->autoContentLoad = $isappajax = $params['isappajax'] = $this->_getParam('isappajax', false);
    $this->view->totalPages = ceil(($this->view->totalCount) /$itemCount);
    $this->view->params = $params;
    //END - SCROLLING WORK
    
    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $controller = $front->getRequest()->getControllerName();
    $action = $front->getRequest()->getActionName();
    $this->view->showTopBottomContent = 1; 
    if($module == 'siteadvsearch' && $controller == 'index' && $action == 'browse-page') {
      $this->view->showTopBottomContent = 0; 
    }
    
    $this->view->params['shareOptions'] = $this->_getParam('shareOptions', 0);
  }

}
