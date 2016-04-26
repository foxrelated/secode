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
class Siteevent_Widget_HostEventsController extends Seaocore_Content_Widget_Abstract {

    protected $_childCount;

    public function indexAction() {
        $this->view->typesOfViews = $this->_getParam('typesOfViews', array('listview', 'gridview', 'mapview'));
        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject() || empty($this->view->typesOfViews)) {
            return $this->setNoRender();
        }

        //GET EVENT SUBJECT
        $subject = Engine_Api::_()->core()->getSubject();

        //GET VIEWER DETAIL
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $this->view->moduleName = $moduleName = strtolower($subject->getModuleName());
        $this->view->user_layout = $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting($moduleName . '.layoutcreate', 0);
        $this->view->isajax = $this->_getParam('isajax', 0);
        if ($this->_getParam('isajax', 0)) {
            $this->getElement()->removeDecorator('Title');
            $this->getElement()->removeDecorator('Container');
        }

        $this->view->eventFilterTypes = $this->_getParam('eventFilterTypes', array('upcoming', 'past'));

        $this->view->ratingType = $this->_getParam('ratingType', 'rating_avg');
        $this->view->title_truncation = $this->_getParam('truncation', 35);
        $this->view->truncationLocation = $this->_getParam('truncationLocation', 35);
        $this->view->title_truncationGrid = $this->_getParam('truncationGrid', 90);
        $this->view->titlePosition = $this->_getParam('titlePosition', 1);
        $this->view->statistics = $this->_getParam('eventInfo', array("categoryLink", "startEndDate", "ledBy", "price", "venueName", "location", "directionLink", "viewCount", "likeCount", "commentCount", "memberCount", "reviewCount"));
        $this->view->limit = $itemCount = $this->_getParam('itemCount', 10);

        if (!empty($this->view->statistics) && !Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) || Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 1) {
            $key = array_search('reviewCount', $this->view->statistics);
            if (!empty($key)) {
                unset($this->view->statistics[$key]);
            }
        }

        $values = array();
        $values['type'] = 'home';
        $values['viewtype'] = $this->_getParam('viewEventType', null);
        if (empty($values['viewtype']) && isset($this->view->eventFilterTypes[0]) ) {
            $values['viewtype'] = $this->view->eventFilterTypes[0];
        }
        $this->view->viewEventType = $values['viewtype'];
        $values['action'] = 'upcoming';
        $values['orderby'] = 'starttime';
        $values['host_type'] = $subject->getType();
        $values['host_id'] = $subject->getIdentity();
        $values['category_id'] = $this->_getParam('hidden_category_id', 0);
        if ($values['category_id']) {
            $values['subcategory_id'] = $this->_getParam('hidden_subcategory_id', 0);
            if ($values['subcategory_id'])
                $values['subsubcategory_id'] = $this->_getParam('hidden_subsubcategory_id', 0);
        }

        $values['eventType'] = $contentType = Zend_Controller_Front::getInstance()->getRequest()->getParam('eventType', null);
        if (empty($contentType)) {
            $values['eventType'] = $this->_getParam('eventType', 'All');
        }
        $this->view->contentType = $values['eventType'];
				$values['networkBased'] = $this->_getParam('networkBased', '0');

        $forAllValues = $values;
        if (!$this->view->isajax) {

            if (count($this->view->eventFilterTypes) > 1) {
                $forAllValues['action'] = 'all';
            }
            $paginator = Engine_Api::_()->getDbTable('events', 'siteevent')->getSiteeventsPaginator($forAllValues);
            if ($paginator->getTotalItemCount() <= 0) {
                return $this->setNoRender();
            }
            if (count($this->view->eventFilterTypes) < 2 && $this->_getParam('titleCount', false)) {
                $this->_childCount = $paginator->getTotalItemCount();
            }
        }
        $allParams = $this->_getAllParams();
        $allParams['loaded_by_ajax'] = true;
        $this->view->allParams = $allParams;
        if ($this->_getParam('loaded_by_ajax', true) && Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode') ) {
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
        if (count($this->view->eventFilterTypes) > 1) {
            $forAllValues['action'] = 'upcoming';
            $forAllValues['viewtype'] = 'upcoming';
            $paginator = Engine_Api::_()->getDbTable('events', 'siteevent')->getSiteeventsPaginator($forAllValues);
            $this->view->totalUpcomingEventCount = $paginator->getTotalItemCount();
            $forAllValues['viewtype'] = 'past';
            $paginator = Engine_Api::_()->getDbTable('events', 'siteevent')->getSiteeventsPaginator($forAllValues);
            $this->view->totalPastEventCount = $paginator->getTotalItemCount();
        }
        $this->view->viewType = $this->_getParam('viewType', 'listview');
        $viewTypeAjax = $this->_getParam('viewTypeAjax');
        if ($viewTypeAjax) {
            $this->view->viewType = $viewTypeAjax;
        }
        if (in_array('mapview', $this->view->typesOfViews)) {
            $checkLocation = Engine_Api::_()->siteevent()->enableLocation();
            if (!$checkLocation) {
                $k = array_search('mapview', $this->view->typesOfViews);
                unset($this->view->typesOfViews[$k]);
            }
        }
        if (!in_array($this->view->viewType, $this->view->typesOfViews)) {
            $this->view->viewType = $this->view->typesOfViews[0];
        }
        if ($this->view->viewType === 'mapview') {
            $values['hasLocationBase'] = true;
        }
        //FETCH RESULTS
        $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('events', 'siteevent')->getSiteeventsPaginator($values);
        $paginator->setItemCountPerPage($itemCount);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        //DONT RENDER IF RESULTS IS ZERO
        if ($paginator->getTotalItemCount() <= 0 && count($this->view->eventFilterTypes) < 2) {
            if (!in_array('mapview', $this->view->typesOfViews) || count($this->view->typesOfViews) == 1 && $this->view->viewType === 'mapview')
                return $this->setNoRender();
        }
        //ADD EVENT COUNT
        if ($this->_getParam('titleCount', false) && count($this->view->eventFilterTypes) < 2) {
            $this->_childCount = $paginator->getTotalItemCount();
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
