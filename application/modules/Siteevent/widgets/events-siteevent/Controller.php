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
class Siteevent_Widget_EventsSiteeventController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        $this->view->titleLink = $this->_getParam('titleLink', null);
        $this->view->titleLinkPosition = $this->_getParam('titleLinkPosition', 'bottom');
        $this->view->photoHeight = $this->_getParam('photoHeight', 370);
        $this->view->photoWidth = $this->_getParam('photoWidth', 350);
        $this->view->isajax = $this->_getParam('isajax', false);
        if ($this->view->isajax) {
            $this->getElement()->removeDecorator('Title');
            $this->getElement()->removeDecorator('Container');
        }
        $this->view->viewmore = $this->_getParam('viewmore', false);

        if ($this->_getParam('is_ajax_load', false)) {
            $this->view->is_ajax_load = true;
            if ($this->_getParam('contentpage', 1) > 1 || $this->_getParam('page', 1) > 1)
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
        $params['popularity'] = $this->view->popularity = $this->_getParam('popularity', 'event_id');
        $params['limit'] = $params['itemCount'] = $this->_getParam('itemCount', 3);
        $fea_spo = $params['fea_spo'] = $this->_getParam('fea_spo', '');
        if ($fea_spo == 'featured') {
            $params['featured'] = 1;
        } elseif ($fea_spo == 'sponsored') {
            $params['sponsored'] = 1;
        } elseif ($fea_spo == 'newlabel') {
            $params['newlabel'] = 1;
        } elseif ($fea_spo == 'fea_spo') {
            $params['sponsored_or_featured'] = 1;
        }
        
        if(Engine_Api::_()->siteevent()->isTicketBasedEvent() && $params['popularity'] == 'member_count') {
            return $this->setNoRender();
        }

        $this->view->titlePosition = $this->_getParam('titlePosition', 1);
//        $this->view->statistics = $params['statistics'] = $this->_getParam('statistics', array("likeCount", "memberCount"));
        $this->view->statistics = $params['statistics'] = $this->_getParam('eventInfo', array("likeCount", "memberCount"));
        $this->view->layouts_views = $params['layouts_views'] = $this->_getParam('layouts_views', array("listview", "gridview"));
        $this->view->postedby = $params['postedby'] = $this->_getParam('postedby', 1);
        $params['ratingType'] = $this->view->ratingType = $this->_getParam('ratingType', 'rating_avg');
        $params['showEventType'] = $this->view->showEventType = $this->_getParam('showEventType', 'upcoming');

        $this->view->category_id = $params['category_id'] = $this->_getParam('hidden_category_id');
        $params['subcategory_id'] = $this->_getParam('hidden_subcategory_id');
        $params['subsubcategory_id'] = $this->_getParam('hidden_subsubcategory_id');

        $this->view->showContent = $params['showContent'] = $this->_getParam('showContent', array("price", "location"));

        $this->view->truncation = $params['truncation'] = $this->_getParam('truncation', 16);
        $this->view->bottomLine = $params['bottomLine'] = $this->_getParam('bottomLine', 2);
        $this->view->bottomLineGrid = $params['bottomLineGrid'] = $this->_getParam('bottomLineGrid', 2);
        $this->view->columnWidth = $params['columnWidth'] = $this->_getParam('columnWidth', '180');
        $this->view->truncationLocation = $params['truncationLocation'] = $this->_getParam('truncationLocation', 35);
        $this->view->title_truncationGrid = $params['truncationGrid'] = $this->_getParam('truncationGrid', 100);
        $this->view->columnHeight = $params['columnHeight'] = $this->_getParam('columnHeight', '328');
        $params['interval'] = $interval = $this->_getParam('interval', 'overall');
        $siteeventEvents = Zend_Registry::isRegistered('siteeventEvents') ? Zend_Registry::get('siteeventEvents') : null;
        $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);
        if ($this->view->detactLocation) {
            $this->view->detactLocation = Engine_Api::_()->siteevent()->enableLocation();
        }
        if ($this->view->detactLocation) {
            $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
            $params['latitude'] = $this->_getParam('latitude', 0);
            $params['longitude'] = $this->_getParam('longitude', 0);
        }
        $this->view->enableLocation = $checkLocation = Engine_Api::_()->siteevent()->enableLocation();
        $this->view->siteeventDistanceLocation = $siteeventDistanceLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventdistance.location', 1);
//        $params['paginator'] = 1;
        //$params['format'] = 'html';

        $params['page'] = $this->_getParam('page', 1);

        $this->view->identity = $params['identity'] = $this->_getParam('identity', $this->view->identity);

        $this->view->params = $params;

        $params['eventType'] = $contentType = Zend_Controller_Front::getInstance()->getRequest()->getParam('eventType', null);
        if (empty($contentType)) {
            $params['eventType'] = $this->_getParam('eventType', 'All');
        }
        $this->view->contentType = $params['eventType'];

        //GET EVENTS
        $this->view->events = $paginator = Engine_Api::_()->getDbTable('events', 'siteevent')->eventsBySettings($params);

//        $this->view->totalCount = $paginator->getTotalItemCount();
        //DON'T RENDER IF RESULTS IS ZERO
//        if (($this->view->totalCount <= 0)) {
//            return $this->setNoRender();
//        }
        if (@count($paginator) <= 0) {
            return $this->setNoRender();
        }

        if (empty($siteeventEvents) || empty($siteeventDistanceLocation)) {
            return $this->setNoRender();
        }

        $this->view->viewType = $params['viewType'] = $this->_getParam('viewType', 'gridview');
        $this->view->eventType = $params['eventType'] = $this->_getParam('eventType', 'gridview');
    }

}
 
