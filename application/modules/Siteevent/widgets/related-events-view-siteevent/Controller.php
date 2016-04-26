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
class Siteevent_Widget_RelatedEventsViewSiteeventController extends Seaocore_Content_Widget_Abstract {

    protected $_childCount;

    public function indexAction() {

        //DONT RENDER IF NOT AUTHORIZED
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event') && !Engine_Api::_()->core()->hasSubject('siteevent_review')) {
            return $this->setNoRender();
        }

        $this->view->isajax = $this->_getParam('isajax', false);
        if ($this->view->isajax) {
            $this->getElement()->removeDecorator('Title');
            $this->getElement()->removeDecorator('Container');
        }
        $this->view->viewmore = $this->_getParam('viewmore', false);
        $this->view->is_ajax_load = true;
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

        //GET EVENT SUBJECT
        if (Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            $subject = Engine_Api::_()->core()->getSubject();
        } elseif (Engine_Api::_()->core()->hasSubject('siteevent_review')) {
            $subject = Engine_Api::_()->core()->getSubject()->getParent();
        }

        //GET VARIOUS WIDGET SETTINGS
        $this->view->titlePosition = $this->_getParam('titlePosition', 1);
        $this->view->truncationLocation = $this->_getParam('truncationLocation', 35);
        $this->view->title_truncation = $this->_getParam('truncation', 24);
        $this->view->related = $related = $this->_getParam('related', 'categories');
        $this->view->ratingType = $this->_getParam('ratingType', 'rating_avg');
//        $this->view->statistics = $this->_getParam('statistics', array("likeCount", "reviewCount", "memberCount"));
        $this->view->statistics = $this->_getParam('eventInfo', array("likeCount", "reviewCount", "memberCount"));

        if (!empty($this->view->statistics) && !Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) || Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 1) {
            $key = array_search('reviewCount', $this->view->statistics);
            if (!empty($key)) {
                unset($this->view->statistics[$key]);
            }
        }

        $params = array();

        If ($related == 'tags') {

            //GET TAGS
            $eventTags = $subject->tags()->getTagMaps();

            $params['tags'] = array();
            foreach ($eventTags as $tag) {
                $params['tags'][] = $tag->getTag()->tag_id;
            }

            if (empty($params['tags'])) {
                return $this->setNoRender();
            }
        } elseif ($related == 'categories' && $subject->category_id) {
            $params['category_id'] = $subject->category_id;
            $category = Engine_Api::_()->getItem('siteevent_category', $subject->category_id);
            $this->getElement()->setTitle(sprintf($this->getElement()->getTitle(), $this->view->htmlLink($category->getHref(), $category->getTitle(), array('title' => $category->getTitle()))));
        } else {
            return $this->setNoRender();
        }

        //FETCH EVENTS
        $params['event_id'] = $subject->event_id;
        $params['orderby'] = 'RAND()';
        $params['showEventType'] = $this->view->showEventType = $this->_getParam('showEventType', 'upcoming');
        $this->view->count = $limit = $params['limit'] = $this->_getParam('itemCount', 3);

        $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);
        if ($this->view->detactLocation) {
            $this->view->detactLocation = Engine_Api::_()->siteevent()->enableLocation();
        }
        if ($this->view->detactLocation) {
            $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
            $params['latitude'] = $this->_getParam('latitude', 0);
            $params['longitude'] = $this->_getParam('longitude', 0);
        }

        $params['eventType'] = $contentType = Zend_Controller_Front::getInstance()->getRequest()->getParam('eventType', null);
        if (empty($contentType)) {
            $params['eventType'] = $this->_getParam('eventType', 'All');
        }
        $this->view->contentType = $params['eventType'];
				$params['networkBased'] = $this->_getParam('networkBased', '0');

        $this->view->paginator = Engine_Api::_()->getDbtable('events', 'siteevent')->widgetEventsData($params);

        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $this->_childCount = count($this->view->paginator);
        } else {
            $this->view->paginator->setCurrentPageNumber($this->_getParam('page'));
            $this->view->paginator->setItemCountPerPage($limit);
            $this->_childCount = $this->view->paginator->getTotalItemCount();
        }

        if ($this->_childCount <= 0) {
            return $this->setNoRender();
        }

        $this->view->columnWidth = $params['columnWidth'] = $this->_getParam('columnWidth', '180');
        $this->view->columnHeight = $params['columnHeight'] = $this->_getParam('columnHeight', '328');
        $this->view->viewType = $params['viewType'] = $this->_getParam('viewType', 'listview');

        $this->view->params = $params;
    }

    public function getChildCount() {
        return $this->_childCount;
    }

}
