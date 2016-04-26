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
class Siteevent_Widget_ContenttypeEventsController extends Seaocore_Content_Widget_Abstract {

    protected $_childCount;

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }


        //GET VIEWER DETAILS
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer->getIdentity();
        //GET EVENT SUBJECT
        $subject = Engine_Api::_()->core()->getSubject();
        $this->view->moduleName = $moduleName = strtolower($subject->getModuleName());
        $this->view->getShortType = $getShortType = ucfirst($subject->getShortType());
        if ($moduleName == 'sitereview' && isset($subject->listingtype_id)) {
            if (!(Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitereview_listing_' . $subject->listingtype_id, 'item_module' => 'sitereview', 'checked' => 'enabled'))))
                return $this->setNoRender();
        } else {
            if (!(Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => $subject->getType(), 'item_module' => strtolower($subject->getModuleName()), 'checked' => 'enabled'))))
                return $this->setNoRender();
        }

        if ($moduleName == 'sitepage' || $moduleName == 'sitebusiness' || $moduleName == 'sitegroup' || $moduleName == 'sitestore') {
            $isModuleOwnerAllow = 'is' . $getShortType . 'OwnerAllow';

            //START PACKAGE WORK
            if (Engine_Api::_()->$moduleName()->hasPackageEnable()) {
                if (!Engine_Api::_()->$moduleName()->allowPackageContent($subject->package_id, "modules", $moduleName . 'event')) {
                    return $this->setNoRender();
                }
            } else {
                $isOwnerAllow = Engine_Api::_()->$moduleName()->$isModuleOwnerAllow($subject, 'secreate');
                if (empty($isOwnerAllow)) {
                    return $this->setNoRender();
                }
            }
            //END PACKAGE WORK

            $this->view->eventCount = Engine_Api::_()->$moduleName()->getTotalCount($subject->getIdentity(), 'siteevent', 'events');
            $this->view->canCreate = $canCreate = Engine_Api::_()->$moduleName()->isManageAdmin($subject, 'secreate');

            //START MANAGE-ADMIN CHECK
            $isManageAdmin = Engine_Api::_()->$moduleName()->isManageAdmin($subject, 'view');
            if (empty($isManageAdmin)) {
                return $this->setNoRender();
            }

            $isManageAdmin = Engine_Api::_()->$moduleName()->isManageAdmin($subject, 'edit');
            if (empty($isManageAdmin)) {
                $this->view->can_edit = $canEdit = 0;
            } else {
                $this->view->can_edit = $canEdit = 1;
            }
            if (empty($canCreate) && empty($this->view->eventCount) && empty($canEdit) && !(Engine_Api::_()->$moduleName()->showTabsWithoutContent())) {
                return $this->setNoRender();
            }
            $this->view->user_layout = $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting($moduleName . '.layoutcreate', 0);
            $this->view->widgets = $widgets = Engine_Api::_()->$moduleName()->getwidget($layout, $subject->getIdentity());
            $this->view->showtoptitle = $showtoptitle = Engine_Api::_()->$moduleName()->showtoptitle($layout, $subject->getIdentity());
            $this->view->integratedModule = 1;
        } else {
            $this->view->user_layout = 0;
            $this->view->showtoptitle = 0;
            $this->view->widgets = $widgets = 1;
            $this->view->can_edit = $canEdit = $subject->authorization()->isAllowed($viewer, "edit_listtype_$subject->listingtype_id");
            $this->view->canCreate = $canCreate = Engine_Api::_()->authorization()->isAllowed($subject, $viewer, "event_listtype_$subject->listingtype_id");
            $this->view->integratedModule = 0;
        }
        $this->view->module_tabid = $currenttabid = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);

        $isajax = $this->_getParam('is_ajax_load', null);
        $this->view->isajax = $isajax;
        $this->view->eventFilterTypes = $this->_getParam('eventFilterTypes', array('onlyOngoing', 'upcoming', 'past'));
        $this->view->eventFilterTypesCount = count($this->view->eventFilterTypes);

        if ($this->view->eventFilterTypes && in_array("onlyOngoing", $this->view->eventFilterTypes)) {
            if (isset($this->view->eventFilterTypes['upcoming'])) {
                $this->view->eventFilterTypes['onlyUpcoming'] = $this->view->eventFilterTypes['upcoming'];
            }
        }
        $this->view->eventOwnerType = $this->_getParam('eventOwnerType', array('lead', 'host'));

        $this->view->eventOwnerTypeCount = count($this->view->eventOwnerType);
        //GET SETTINGS
        $this->view->titlePosition = $this->_getParam('titlePosition', 1);
        $this->view->truncationLocation = $this->_getParam('truncationLocation', 35);
        $this->view->descriptionPosition = $this->_getParam('descriptionPosition', 0);
        $this->view->allParams = $this->_getAllParams();
        $this->view->identity = $this->view->allParams['identity'] = $this->_getParam('identity', $this->view->identity);
        $ShowViewArray = $this->_getParam('layouts_views', array("0" => "1", "1" => "2", "2" => "3"));
        $this->view->eventviewType = $this->_getParam('eventviewType', '');
        $this->view->statistics = $this->_getParam('eventInfo', array("startDate", "endDate", "ledBy", "price", "venueName", "commentCount", "reviewCount", "ratingStar"));


        $defaultOrder = $this->view->defaultOrder = $this->_getParam('layouts_order', 2);
        if (empty($this->view->eventviewType)) {
            if ($defaultOrder == 1)
                $this->view->eventviewType = 'listview';
            else
                $this->view->eventviewType = 'gridview';
        }
        $this->view->ratingType = $this->_getParam('ratingType', 'rating_both');
        $this->view->title_truncation = $this->_getParam('truncation', 25);
        $this->view->title_truncationGrid = $this->_getParam('truncationGrid', 90);
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

        $values['page'] = $page;

        //GET LISITNG FPR PUBLIC PAGE SET VALUE
        if (!$subject->isOwner($viewer))
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

        //CORE API
        $this->view->settings = $settings = Engine_Api::_()->getApi('settings', 'core');

        $values['viewtype'] = $this->_getParam('filterType', null);
        if (empty($values['viewtype']) && isset($this->view->eventFilterTypes[0])) {
            $values['viewtype'] = $this->view->eventFilterTypes[0];
        }
        $this->view->filterType = $values['showEventType'] = $values['viewtype'];
        $values['ownertype'] = $this->_getParam('ownertype', null);
        if (empty($values['ownertype']) && isset($this->view->eventOwnerType[0])) {
            $values['ownertype'] = $this->view->eventOwnerType[0];
        }
        $this->view->ownertype = $values['ownertype'];

        if ($isajax) {
            $this->getElement()->removeDecorator('Title');
            $this->getElement()->removeDecorator('Container');
        }

        $values['action'] = $values['viewtype'];
        $values['orderby'] = 'starttime';

        if ($values['ownertype'] == 'host') {
            $values['host_type'] = $subject->getType();
            $values['host_id'] = $subject->getIdentity();
        } else {
            $values['parent_type'] = $subject->getType();
            $values['parent_id'] = $subject->getIdentity();
        }
        $values['eventType'] = $contentType = $request->getParam('eventType', null);
        if (empty($contentType)) {
            $values['eventType'] = $this->_getParam('eventType', 'All');
        }
        $this->view->contentType = $this->view->allParams['eventType'] = $values['eventType'];

        $this->view->limit = $values['limit'] = $itemCount = $this->_getParam('itemCount', 10);

        $values['showClosed'] = $this->_getParam('showClosed', 1);

        if ($request->getParam('titleAjax')) {
            $values['search'] = $request->getParam('titleAjax');
        }

        $this->view->detactLocation = $values['detactLocation'] = $this->_getParam('detactLocation', 0);
        if ($this->view->detactLocation) {
            $this->view->detactLocation = Engine_Api::_()->siteevent()->enableLocation();
        }
        if ($this->view->detactLocation) {
            $this->view->defaultLocationDistance = $values['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
            $values['latitude'] = $this->_getParam('latitude', 0);
            $values['longitude'] = $this->_getParam('longitude', 0);
        }
        if (empty($values['category_id'])) {
            $this->view->category_id = $values['category_id'] = $this->_getParam('hidden_category_id');
            $values['subcategory_id'] = $this->_getParam('hidden_subcategory_id');
            $values['subsubcategory_id'] = $this->_getParam('hidden_subsubcategory_id');
        }
        // GET EVENTS

        $this->view->search = 0;
        if (!empty($this->_getAllParams) && Count($this->_getAllParams) > 1) {
            $this->view->search = 1;
        }

        $this->view->ratingTable = Engine_Api::_()->getDbtable('ratings', 'siteevent');
        $this->view->columnWidth = $this->_getParam('columnWidth', '180');
        $this->view->columnHeight = $this->_getParam('columnHeight', '328');

        $this->view->paramsLocation = array_merge($_GET, $this->_getAllParams());
        $this->view->paramsLocation = array_merge($request->getParams(), $this->view->paramsLocation);

        $this->view->paramsLocation = array_merge($this->view->paramsLocation, array('eventFilterTypesCount' => $this->view->eventFilterTypesCount));

        $this->view->paramsLocation = array_merge($this->view->paramsLocation, array('eventOwnerTypeCount' => $this->view->eventOwnerTypeCount));

        $this->view->paramsLocation = array_merge($this->view->paramsLocation, array('eventCount' => $this->view->eventCount));

        $this->view->allParams['eventType'] = $this->view->eventType = $this->_getParam('eventType', $this->view->eventviewType);
        $this->view->viewmore = $this->_getParam('viewmore', false);
        if (isset($_GET['search']) || isset($_POST['search'])) {
            $this->view->detactLocation = 0;
        } else {
            $this->view->detactLocation = $this->_getParam('detactLocation', 0);
        }

        if (!empty($isajax) || ($currenttabid == $this->view->identity) || ($widgets == 0)) {

            $this->view->identity_temp = Zend_Controller_Front::getInstance()->getRequest()->getParam('identity_temp', $currenttabid);
            $this->view->show_content = true;
            $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('events', 'siteevent')->getSiteeventsPaginator($values);
            $paginator->setItemCountPerPage($itemCount);
            $this->view->paginator = $paginator->setCurrentPageNumber($values['page']);
            $this->view->totalResults = $paginator->getTotalItemCount();
            $this->view->enableLocation = $checkLocation = Engine_Api::_()->siteevent()->enableLocation();
            $this->view->flageSponsored = 0;
            $this->view->statistics = $this->_getParam('eventInfo', array("startDate", "endDate", "ledBy", "price", "venueName", "commentCount", "reviewCount", "ratingStar"));
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
            $this->view->paramsLocation = array_merge($_GET, $this->_getAllParams());
            $this->view->paramsLocation = array_merge($request->getParams(), $this->view->paramsLocation);
        } else {

            $this->view->show_content = false;
            $this->view->identity_temp = $this->view->identity;
            $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('events', 'siteevent')->getSiteeventsPaginator($values);
            $paginator->setItemCountPerPage($itemCount);
            $this->view->paginator = $paginator->setCurrentPageNumber($values['page']);
            $this->view->totalResults = $paginator->getTotalItemCount();
            $this->view->enableLocation = $checkLocation = Engine_Api::_()->siteevent()->enableLocation();
            $this->view->flageSponsored = 0;
            $this->view->totalCount = $paginator->getTotalItemCount();
        }

        $this->_childCount = $this->view->totalCount;
    }

    public function getChildCount() {
        return $this->_childCount;
    }

}
