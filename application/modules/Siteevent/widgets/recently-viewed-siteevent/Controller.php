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
class Siteevent_Widget_RecentlyViewedSiteeventController extends Seaocore_Content_Widget_Abstract {

    protected $_childCount;

    public function indexAction() {

        //GET VIEWER ID
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        if (empty($viewer_id)) {
            return $this->setNoRender();
        }


        $params = array();

        $this->view->titlePosition = $this->_getParam('titlePosition', 1);
//    $this->view->statistics = $this->_getParam('statistics', array("likeCount", "memberCount"));
        $this->view->statistics = $this->_getParam('eventInfo', array("likeCount", "memberCount"));

        if (!empty($this->view->statistics) && !Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) || Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 1) {
            $key = array_search('reviewCount', $this->view->statistics);
            if (!empty($key)) {
                unset($this->view->statistics[$key]);
            }
        }

        $this->view->count = $params['limit'] = $this->_getParam('count', 3);
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
        $params['show'] = $this->_getParam('show', 1);
        $params['viewer_id'] = $viewer_id;
        $this->view->ratingType = $this->_getParam('ratingType', 'rating_avg');
        $this->view->title_truncation = $this->_getParam('truncation', 16);
        $this->view->truncationLocation = $this->_getParam('truncationLocation', 35);

        $this->view->category_id = $params['category_id'] = $this->_getParam('hidden_category_id', 0);
        if ($params['category_id']) {
            $params['subcategory_id'] = $this->_getParam('hidden_subcategory_id', 0);
            if ($params['subcategory_id'])
                $params['subsubcategory_id'] = $this->_getParam('hidden_subsubcategory_id', 0);
        }

        $params['eventType'] = $contentType = Zend_Controller_Front::getInstance()->getRequest()->getParam('eventType', null);
        if (empty($contentType)) {
            $params['eventType'] = $this->_getParam('eventType', 'All');
        }
        $this->view->contentType = $params['eventType'];

        //GET EVENTS
        $params['showEventType'] = $this->view->showEventType = 'all';
        $this->view->events = Engine_Api::_()->getDbTable('events', 'siteevent')->recentlyViewed($params);

        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $this->view->events->setCurrentPageNumber($this->_getParam('page'));
            $this->view->events->setItemCountPerPage($params['limit']);
            if ($this->view->events->getTotalItemCount() <= 0) {
                return $this->setNoRender();
            }
            $this->_childCount = $this->view->events->getTotalItemCount();
        } else {
            if (Count($this->view->events) <= 0) {
                return $this->setNoRender();
            }
            $this->_childCount = Count($this->view->events);
        }

        $this->view->columnWidth = $this->_getParam('columnWidth', '180');
        $this->view->columnHeight = $this->_getParam('columnHeight', '328');
        $this->view->viewType = $this->_getParam('viewType', 'listview');
    }

    public function getChildCount() {
        return $this->_childCount;
    }

}
