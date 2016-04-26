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
class Siteevent_Widget_MostDiscussedEventsController extends Engine_Content_Widget_Abstract {

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

        $params = array();
        $params['limit'] = $this->_getParam('itemCount', 3);
        $fea_spo = $this->_getParam('fea_spo', '');
        if ($fea_spo == 'featured') {
            $params['featured'] = 1;
        } elseif ($fea_spo == 'sponsored') {
            $params['sponsored'] = 1;
        } elseif ($fea_spo == 'newlabel') {
            $params['newlabel'] = 1;
        } elseif ($fea_spo == 'fea_spo') {
            $params['sponsored_or_featured'] = 1;
        }

        $this->view->statistics = $this->_getParam('eventInfo', array("categoryLink", "startEndDate", "ledBy", "price", "venueName", "location", "directionLink", "viewCount", "likeCount", "commentCount", "memberCount", "reviewCount"));

        if (!empty($this->view->statistics) && !Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) || Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 1) {
            $key = array_search('reviewCount', $this->view->statistics);
            if (!empty($key)) {
                unset($this->view->statistics[$key]);
            }
        }

        $params['ratingType'] = $this->view->ratingType = $this->_getParam('ratingType', 'rating_avg');

        $this->view->category_id = $params['category_id'] = $this->_getParam('hidden_category_id');
        $params['subcategory_id'] = $params['hidden_subcategory_id'] = $this->_getParam('hidden_subcategory_id');
        $params['subsubcategory_id'] = $params['hidden_subsubcategory_id'] = $this->_getParam('hidden_subsubcategory_id');

        $this->view->titlePosition = $this->_getParam('titlePosition', 1);
        $this->view->truncation = $params['truncation'] = $this->_getParam('truncation', 16);
        $this->view->columnWidth = $params['columnWidth'] = $this->_getParam('columnWidth', '180');
        $this->view->truncationLocation = $params['truncationLocation'] = $this->_getParam('truncationLocation', 35);
        $this->view->columnHeight = $params['columnHeight'] = $this->_getParam('columnHeight', '328');

        $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);
        if ($this->view->detactLocation) {
            $this->view->detactLocation = Engine_Api::_()->siteevent()->enableLocation();
        }
        if ($this->view->detactLocation) {
            $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
            $params['latitude'] = $this->_getParam('latitude', 0);
            $params['longitude'] = $this->_getParam('longitude', 0);
        }

        $params['showEventType'] = $this->view->showEventType = $this->_getParam('showEventType', 'upcoming');
        $this->view->params = $params;

        $params['eventType'] = $contentType = Zend_Controller_Front::getInstance()->getRequest()->getParam('eventType', null);
        if (empty($contentType)) {
            $params['eventType'] = $this->_getParam('eventType', 'All');
        }
        $this->view->contentType = $params['eventType'];

        //GET EVENTS
        $this->view->events = Engine_Api::_()->getDbTable('events', 'siteevent')->getDiscussedEvent($params);

        //DON'T RENDER IF RESULTS IS ZERO
        if (count($this->view->events) <= 0) {
            return $this->setNoRender();
        }
        $this->view->viewType = $this->_getParam('viewType', 'listview');
    }

}
