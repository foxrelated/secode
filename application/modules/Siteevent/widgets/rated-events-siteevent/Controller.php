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
class Siteevent_Widget_RatedEventsSiteeventController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        if ($this->_getParam('is_ajax_load', false)) {
            $this->view->is_ajax_load = true;
            if ($this->_getParam('contentpage', 1) > 1)
                $this->getElement()->removeDecorator('Title');
            $this->getElement()->removeDecorator('Container');
        } else {
            if (!$this->_getParam('detactLocation', 0)) {
                $this->view->is_ajax_load = true;
            } else {
                $this->getElement()->removeDecorator('Title');
            }
        }

        //GET VIEWER DETAILS
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer->getIdentity();

        //GET SETTINGS
        $this->view->allParams = $this->_getAllParams();
        $this->view->identity = $this->view->allParams['identity'] = $this->_getParam('identity', $this->view->identity);
        $ShowViewArray = $this->_getParam('layouts_views', array("0" => "1", "1" => "2", "2" => "3"));
        $this->view->isajax = $this->_getParam('isajax', 0);
        $this->view->viewType = $this->_getParam('viewType', '');
        $this->view->statistics = $this->_getParam('statistics', array("viewCount", "likeCount", "commentCount", "memberCount", "reviewCount"));
        $defaultOrder = $this->view->defaultOrder = $this->_getParam('layouts_order', 2);
        if (empty($this->view->viewType)) {
            if ($defaultOrder == 1)
                $this->view->viewType = 'listview';
            else
                $this->view->viewType = 'gridview';
        }

        $this->view->eventInfo = $this->_getParam('eventInfo', array('featuredLabel', 'sponsoredLabel', 'newLabel'));
        $this->view->ratingType = $this->_getParam('ratingType', 'rating_both');
        $this->view->title_truncation = $this->_getParam('truncation', 25);
        $this->view->title_truncationGrid = $this->_getParam('truncationGrid', 90);
        $this->view->postedby = $params['postedby'] = $this->_getParam('postedby', 1);
        $this->view->showContent = $params['showContent'] = $this->_getParam('showContent', array("price", "location"));
        $this->view->list_view = 0;
        $this->view->grid_view = 0;
        $this->view->map_view = 0;
        $this->view->defaultView = -1;
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

        $request = Zend_Controller_Front::getInstance()->getRequest();

        $this->view->params = $params = $request->getParams();
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



        if (!empty($this->view->statistics) && !Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) || Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 1) {
            $key = array_search('reviewCount', $this->view->statistics);
            if (!empty($key)) {
                unset($this->view->statistics[$key]);
            }
        }

        if (isset($params['tag']) && !empty($params['tag'])) {
            $tag = $params['tag'];
            $tag_id = $params['tag_id'];
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
            $values['orderby'] = $this->_getParam('orderby', 'event_id');
        }
        $this->view->allParams['orderby'] = $values['orderby'];
        $this->view->limit = $values['limit'] = $itemCount = $this->_getParam('itemCount', 10);
        $this->view->bottomLine = $this->_getParam('bottomLine', 1);
        $this->view->bottomLineGrid = $this->_getParam('bottomLineGrid', 2);
        $values['viewType'] = $this->view->viewType;
        $values['showClosed'] = $this->_getParam('showClosed', 1);

        $values['most_rated'] = 1;

        $this->view->detactLocation = $values['detactLocation'] = $this->_getParam('detactLocation', 0);
        if ($this->view->detactLocation) {
            $this->view->detactLocation = Engine_Api::_()->siteevent()->enableLocation();
        }
        if ($this->view->detactLocation) {
            $this->view->defaultLocationDistance = $values['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
            $values['latitude'] = $this->_getParam('latitude', 0);
            $values['longitude'] = $this->_getParam('longitude', 0);
        }

        $values['eventType'] = $contentType = Zend_Controller_Front::getInstance()->getRequest()->getParam('eventType', null);
        if (empty($contentType)) {
            $values['eventType'] = $this->_getParam('eventType', 'All');
        }
        $this->view->contentType = $values['eventType'];

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
        $this->view->columnWidth = $this->_getParam('columnWidth', '180');
        $this->view->columnHeight = $this->_getParam('columnHeight', '328');

        $this->view->paramsLocation = array_merge($_GET, $this->_getAllParams());
        $this->view->paramsLocation = array_merge($request->getParams(), $this->view->paramsLocation);
        $this->view->allParams['eventType'] = $this->view->eventType = $this->_getParam('eventType', $this->view->viewType);
        $this->view->viewmore = $this->_getParam('viewmore', false);
        if (isset($_GET['search']) || isset($_POST['search'])) {
            $this->view->detactLocation = 0;
        } else {
            $this->view->detactLocation = $this->_getParam('detactLocation', 0);
        }
        // = ;
    }

}