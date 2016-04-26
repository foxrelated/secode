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
class Siteevent_Widget_PinboardEventsSiteeventController extends Engine_Content_Widget_Abstract {

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

        $params = array();
        $siteeventPinboardEvents = Zend_Registry::isRegistered('siteeventPinboardEvents') ? Zend_Registry::get('siteeventPinboardEvents') : null;
        $params['popularity'] = $this->view->popularity = $this->_getParam('popularity', 'event_id');
        $params['limit'] = $this->_getParam('itemCount', 12);
        $fea_spo = $this->_getParam('fea_spo', '');
        if ($fea_spo == 'featured') {
            $params['featured'] = 1;
        } elseif ($fea_spo == 'newlabel') {
            $params['newlabel'] = 1;
        } elseif ($fea_spo == 'sponsored') {
            $params['sponsored'] = 1;
        } elseif ($fea_spo == 'fea_spo') {
            $params['sponsored_or_featured'] = 1;
        }

        if (empty($siteeventPinboardEvents))
            return $this->setNoRender();

//        $this->view->postedby = $this->_getParam('postedby', 1);
        $this->view->userComment = $this->_getParam('userComment', 1);
        $this->view->statistics = $this->_getParam('eventInfo', array("likeCount", "memberCount"));
        $this->view->truncationDescription = $this->_getParam('truncationDescription', 100);
        $this->view->truncationLocation = $this->_getParam('truncationLocation', 35);
        $params['ratingType'] = $this->view->ratingType = $this->_getParam('ratingType', 'rating_avg');
        $params['showEventType'] = $this->view->showEventType = $this->_getParam('showEventType', 'upcoming');

        $this->view->category_id = $params['category_id'] = $this->_getParam('hidden_category_id');
        $params['subcategory_id'] = $this->_getParam('hidden_subcategory_id');
        $params['subsubcategory_id'] = $this->_getParam('hidden_subsubcategory_id');


        $params['interval'] = $interval = $this->_getParam('interval', 'overall');
        $params['paginator'] = 1;

        $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);
        if ($this->view->detactLocation) {
            $this->view->detactLocation = Engine_Api::_()->siteevent()->enableLocation();
        }
        if ($this->view->detactLocation) {
            $this->view->defaultLocationDistance = $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
            $params['latitude'] = $this->_getParam('latitude', 0);
            $params['longitude'] = $this->_getParam('longitude', 0);
        }

        $params['eventType'] = $contentType = Zend_Controller_Front::getInstance()->getRequest()->getParam('eventType', null);
        if (empty($contentType)) {
            $params['eventType'] = $this->_getParam('eventType', 'All');
        }
        $this->view->contentType = $params['eventType'];

        //GET EVENTS
        $this->view->events = $paginator = Engine_Api::_()->getDbTable('events', 'siteevent')->eventsBySettings($params);
        $this->view->totalCount = $paginator->getTotalItemCount();

        $paginator->setCurrentPageNumber($this->_getParam('contentpage', 1));
        $paginator->setItemCountPerPage($params['limit']);
        //DON'T RENDER IF RESULTS IS ZERO
        if ($this->view->totalCount <= 0) {
            return $this->setNoRender();
        }

        $this->view->countPage = $paginator->count();
        if ($this->view->params['noOfTimes'] > $this->view->countPage)
            $this->view->params['noOfTimes'] = $this->view->countPage;

        $this->view->show_buttons = $this->_getParam('show_buttons', array("membership", "diary", "comment", "like", 'share', 'facebook', 'twitter', 'pinit'));
    }

}
