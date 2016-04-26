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
class Siteevent_Widget_PinboardBrowseController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $this->view->params = $this->_getAllParams();
        $this->view->params['defaultLoadingImage'] = $this->_getParam('defaultLoadingImage', 1);
        if (!isset($this->view->params['noOfTimes']) || empty($this->view->params['noOfTimes']))
            $this->view->params['noOfTimes'] = 1000;

        if ($this->_getParam('autoload', true)) {
            $this->view->autoload = true;
            if ($this->_getParam('is_ajax_load', false)) {
                $this->view->is_ajax_load = true;
                $this->view->autoload = false;
                if ($this->_getParam('contentpage', 1) > 1)
                    $this->getElement()->removeDecorator('Title');
                $this->getElement()->removeDecorator('Container');
            } else {
                //  $this->view->layoutColumn = $this->_getParam('layoutColumn', 'middle');
                $this->getElement()->removeDecorator('Title');
                //return;
            }
        } else {
            $this->view->is_ajax_load = $this->_getParam('is_ajax_load', false);
            if ($this->_getParam('contentpage', 1) > 1) {
                $this->getElement()->removeDecorator('Title');
                $this->getElement()->removeDecorator('Container');
            }
        }

        $params = $this->view->params;

        $params['limit'] = $this->_getParam('itemCount', 12);
//        $this->view->postedby = $this->_getParam('postedby', 1);
        $this->view->userComment = $this->_getParam('userComment', 1);
        $this->view->statistics = $this->_getParam('eventInfo', array("likeCount", "memberCount"));
        $this->view->truncationDescription = $this->_getParam('truncationDescription', 100);
        $this->view->truncationLocation = $this->_getParam('truncationLocation', 35);
        $params['ratingType'] = $this->view->ratingType = $this->_getParam('ratingType', 'rating_avg');
        $siteeventPinboardBrowse = Zend_Registry::isRegistered('siteeventPinboardBrowse') ? Zend_Registry::get('siteeventPinboardBrowse') : null;

        $this->view->category_id = $params['category_id'] = $this->_getParam('hidden_category_id');
        $siteeventPinboardLatitude = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventpinboard.latitude', 1);
        $params['subcategory_id'] = $this->_getParam('hidden_subcategory_id');
        $params['subsubcategory_id'] = $this->_getParam('hidden_subsubcategory_id');
        $params['paginator'] = 1;
        
        if(empty($siteeventPinboardBrowse) || empty($siteeventPinboardLatitude)){
          return $this->setNoRender();
        }        

        $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);
        if ($this->view->detactLocation) {
            $this->view->detactLocation = Engine_Api::_()->siteevent()->enableLocation();
        }
        if ($this->view->detactLocation) {
            $this->view->defaultLocationDistance = $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
            $params['latitude'] = $this->_getParam('latitude', 0);
            $params['longitude'] = $this->_getParam('longitude', 0);
        }

        $customFieldValues = array();
        $values = array();

        $request = Zend_Controller_Front::getInstance()->getRequest();

        //GET VIEWER DETAILS
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer->getIdentity();

        $getParams = $request->getParams();
        
        if(isset($getParams['showEventType'])) {
            unset($params['showEventType']);
        }

        $this->view->params = $params = array_merge($params, $getParams);
        
        $widgetSettings = array('locationDetection' => $this->view->detactLocation);
        if(isset($params['price'])) {
            $widgetSettings['priceFieldType'] = 'textBox';
        }
        
        //FORM GENERATION
        $form = new Siteevent_Form_Search(array('type' => 'siteevent_event', 'widgetSettings' => $widgetSettings));

        if (!empty($params)) {
            $form->populate($params);
        }

        $this->view->formValues = $form->getValues();

        $values = array_merge($values, $form->getValues());
        
        
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
            $values['orderby'] = $this->_getParam('orderby', 'starttime');
        }

        $this->view->limit = $values['limit'] = $itemCount = $this->_getParam('itemCount', 10);
        $values['showClosed'] = $this->_getParam('showClosed', 1);

        if ($request->getParam('titleAjax')) {
            $values['search'] = $request->getParam('titleAjax');
        }
        
        $values['action'] = $this->view->showEventType = $this->_getParam('showEventType', 'upcoming');

				$values['eventType'] = $contentType = Zend_Controller_Front::getInstance()->getRequest()->getParam('eventType', null);
				if (empty($contentType)) {
					$values['eventType'] = $this->_getParam('eventType', 'All');
				}
				$this->view->contentType = $values['eventType'];
                
        $values['action'] = $request->getParam('showEventType', 'upcoming');                

        //GET EVENTS
        $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('events', 'siteevent')->getSiteeventsPaginator($values, $customFieldValues);
        $this->view->totalCount = $paginator->getTotalItemCount();
        $paginator->setCurrentPageNumber($this->_getParam('contentpage', 1));
        $paginator->setItemCountPerPage($params['limit']);

        $this->view->enableLocation = $checkLocation = Engine_Api::_()->siteevent()->enableLocation();
        $this->view->flageSponsored = 0;
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

        $this->view->can_create = Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, "create");

        $this->view->countPage = $paginator->count();
        if ($this->view->params['noOfTimes'] > $this->view->countPage)
            $this->view->params['noOfTimes'] = $this->view->countPage;
        $this->view->show_buttons = $this->_getParam('show_buttons', array("diary", "comment", "like", 'share', 'facebook', 'twitter', 'pinit'));
    }

}
