<?php

/**
 * SocialEngine
 *
 * @category   Application_ExtensioshowEventUpcomingPastCountns
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Widget_ProfileSiteeventController extends Seaocore_Content_Widget_Abstract {

    protected $_childCount;

    public function indexAction() {

        $this->view->typesOfViews = $this->_getParam('typesOfViews', array('listview', 'gridview', 'mapview'));
        //GET VIEWER DETAIL
        $viewer = Engine_Api::_()->user()->getViewer();
        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject() || empty($this->view->typesOfViews)) {
            return $this->setNoRender();
        }
        //DON'T RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }

        //GET SUBJECT
        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
        if (!$subject->authorization()->isAllowed($viewer, 'view')) {
            return $this->setNoRender();
        }
        $siteeventProfileEvents = Zend_Registry::isRegistered('siteeventProfileEvents') ? Zend_Registry::get('siteeventProfileEvents') : null;
        if (empty($siteeventProfileEvents))
            return $this->setNoRender();
        
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        $allParams = $this->_getAllParams();
        unset($allParams['rsvp']);
        unset($allParams['page']);
        $allParams['loaded_by_ajax'] = true;
        $this->view->allParams = $allParams;
        $this->view->isajax = $this->_getParam('isajax', 0);
        
        if ($this->_getParam('isajax', 0)) {
            $this->getElement()->removeDecorator('Title');
            $this->getElement()->removeDecorator('Container');
        }
        //IF ADMIN HAS SET TO SHOW EVENTS COUNT IN EVENT TAB THEN WE WILL NOT RETURN ON SIMPAL PAGE LOAD.
        
        
        $this->view->showEventUpcomingPastCount = $this->_getParam('showEventUpcomingPastCount', false);
        if (!$this->_getParam('showEventCount', true)) {
          if ($this->_getParam('loaded_by_ajax', true)) {
            $this->view->loaded_by_ajax = true;
            if ($this->_getParam('is_ajax_load', false)) {
              $this->view->is_ajax_load = true;
              $this->view->loaded_by_ajax = false;
              if (!$this->_getParam('onloadAdd', false))
                $this->getElement()->removeDecorator('Title');
              $this->getElement()->removeDecorator('Container');
            } else {
              return;
            }
          }
        }
        $identity = $this->_getParam('idnetity', null);
        if ($identity)
            $this->view->identity = $identity;
        $this->view->rsvp = $rsvp = $this->_getParam('rsvp', -1);
        $this->view->is_filtering = $this->_getParam('is_filtering', false);
        $this->view->titlePosition = $this->_getParam('titlePosition', true);
        $this->view->viewmore = $this->_getParam('viewmore', false);
        $this->view->showEventFilter = $this->_getParam('showEventFilter', 1);
        $this->view->EventFilterTypes = $eventFilterTypes = $this->_getParam('eventFilterTypes', array('joined', 'ledOwner', 'host', 'liked', 'userreviews'));
        if ($rsvp == -1) {  
            $eventFilterTypes = array('joined', 'ledOwner', 'host');
            $eventstypeall = $this->_getParam('eventtypesall', 'ownerledbyjoinedhost');
            if($eventstypeall == 'ownerledby') {
              unset($eventFilterTypes[0]);
              unset($eventFilterTypes[2]);
            } 
            elseif($eventstypeall == 'ownerledbyjoined')
              unset($eventFilterTypes[2]);
         
        }
       

        $this->view->contentViewType = $this->_getParam('layoutViewType', 'listview');
        $contentViewType = $this->_getParam('contentViewType', null);
        if ($contentViewType)
            $this->view->contentViewType = $contentViewType;
        // $viewtype = $this->_getParam('viewType', 'upcoming');
        //$this->view->viewType = $this->_getParam('viewType', '');
        $this->view->ratingType = $this->_getParam('ratingType', 'rating_avg');
        $this->view->truncationLocation = $this->_getParam('truncationLocation', 35);
        $this->view->title_truncation = $this->_getParam('truncation', 35);
        $this->view->title_truncationGrid = $this->_getParam('truncationGrid', 90);
//    $this->view->statistics = $this->_getParam('statistics', array("viewCount", "likeCount", "commentCount", "memberCount", "reviewCount"));
        $this->view->statistics = $this->_getParam('eventInfo', array("categoryLink", "startEndDate", "ledBy", "price", "venueName", "location", "directionLink", "viewCount", "likeCount", "commentCount", "memberCount", "reviewCount"));
        $this->view->limit = $itemCount = $this->_getParam('itemCount', 10);

        if (!empty($this->view->statistics) && !Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) || Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 1) {
            $key = array_search('reviewCount', $this->view->statistics);
            if (!empty($key)) {
                unset($this->view->statistics[$key]);
            }
        }

        $this->view->showEventType = $this->_getParam('showEventType', 'upcoming');
        $this->view->viewType = $viewtype = $this->_getParam('viewType', 'upcoming');
        $values = array();
        $values['rsvp'] = $rsvp;
        $values['type'] = 'manage';
        $values['viewtype'] = $this->view->showEventType;
        if ($this->view->showEventType == 'all')
            $values['viewtype'] = $viewtype;
        $values['action'] = 'manage';
        $values['orderby'] = 'event_id';
        $values['events_type'] = 'user_profile';
        $values['eventFilterTypes'] = $eventFilterTypes;
        $this->view->category_id = $values['category_id'] = $this->_getParam('hidden_category_id', 0);
        if ($values['category_id']) {
            $values['subcategory_id'] = $this->_getParam('hidden_subcategory_id', 0);
            if ($values['subcategory_id'])
                $values['subsubcategory_id'] = $this->_getParam('hidden_subsubcategory_id', 0);
        }
        $this->view->viewType = $values['viewtype'];
        if ($this->view->showEventType == 'onlyUpcoming') {
            $this->view->viewType = 'upcoming';
        }
        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $level_id = Engine_Api::_()->user()->getViewer()->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }        

        $values['user_id'] = $subject->getIdentity();
        $values['eventType'] = $contentType = Zend_Controller_Front::getInstance()->getRequest()->getParam('eventType', null);
        if (empty($contentType)) {
            $values['eventType'] = $this->_getParam('eventType', 'All');
        }
        $this->view->contentType = $values['eventType'];
        if (in_array('mapview', $this->view->typesOfViews)) {
            $checkLocation = Engine_Api::_()->siteevent()->enableLocation();
            if (!$checkLocation) {
                $k = array_search('mapview', $this->view->typesOfViews);
                unset($this->view->typesOfViews[$k]);
            }
        }

        if (!in_array($this->view->contentViewType, $this->view->typesOfViews)) {
            $this->view->contentViewType = $this->view->typesOfViews[0];
        }
        if ($this->view->contentViewType === 'mapview') {
            $values['hasLocationBase'] = true;
        }
        //FETCH RESULTS
        $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('events', 'siteevent')->getSiteeventsPaginator($values);
        $paginator->setItemCountPerPage($itemCount);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        
        if($this->view->showEventUpcomingPastCount) {
            $this->view->totalCount = $paginator->getTotalItemCount();
        }

        $this->view->current_page = $this->_getParam('page', 1);
        $totalPastEventCount = 0;
        $totalUpcomingEventCount = 0;
        if($values['viewtype'] == 'upcoming') {
            if($this->view->showEventUpcomingPastCount) {
                $this->view->totalUpcomingEventCount = $totalUpcomingEventCount = $this->view->totalCount;
            }
          if ($this->view->showEventType == 'all') {
            $values['viewtype'] = 'past';
            $paginator = Engine_Api::_()->getDbTable('events', 'siteevent')->getSiteeventsPaginator($values);
            if($this->view->showEventUpcomingPastCount) {
                $this->view->totalPastEventCount = $totalPastEventCount = $paginator->getTotalItemCount();
            }
          }
        }
        else {
            if($this->view->showEventUpcomingPastCount) {
                $this->view->totalPastEventCount = $totalPastEventCount = $this->view->totalCount;
            }
          if ($this->view->showEventType == 'all') {
            $values['viewtype'] = 'upcoming';
            $paginator = Engine_Api::_()->getDbTable('events', 'siteevent')->getSiteeventsPaginator($values);
            if($this->view->showEventUpcomingPastCount) {
                $this->view->totalUpcomingEventCount = $totalUpcomingEventCount = $paginator->getTotalItemCount();
            }
          }
        }   
        // $this->view->allParams['page'] = $this->view->current_page;
        //DONT RENDER IF RESULTS IS ZERO
//    if ($paginator->getTotalItemCount() <= 0) {
//      return $this->setNoRender();
//    }
//    //DONT RENDER IF RESULTS IS ZERO
        if($this->view->showEventUpcomingPastCount) {
            if ( !$this->view->isajax && ($totalUpcomingEventCount + $totalPastEventCount) <= 0) {
                return $this->setNoRender();
            }
        }
        //ADD EVENT COUNT
        if ($this->_getParam('titleCount', false)) {
            if ($this->view->showEventType == 'all')
              $this->_childCount =  $totalUpcomingEventCount + $totalPastEventCount ;
            else
              $this->_childCount = $this->view->totalCount;
        }
        if ($this->_getParam('loaded_by_ajax', true)) {
            $this->view->loaded_by_ajax = true;
            if ($this->_getParam('is_ajax_load', false)) {
                $this->view->is_ajax_load = true;
                $this->view->loaded_by_ajax = false;
                if (!$this->_getParam('onloadAdd', false))
                    $this->getElement()->removeDecorator('Title');
                $this->getElement()->removeDecorator('Container');
            } else { 
                return;
            }
        }
        $this->view->columnWidth = $this->_getParam('columnWidth', '180');
        $this->view->columnHeight = $this->_getParam('columnHeight', '328');
        $this->view->showContent = true;
        $this->view->allParams['shareOptions'] = $this->_getParam('shareOptions', 0);
    }

    public function getChildCount() {
        return $this->_childCount;
    }

}
