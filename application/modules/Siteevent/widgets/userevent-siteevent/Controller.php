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
class Siteevent_Widget_UsereventSiteeventController extends Seaocore_Content_Widget_Abstract {

    protected $_childCount;

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return $this->setNoRender();
        }

        $this->view->statistics = $this->_getParam('eventInfo', array("likeCount", "reviewCount", "memberCount"));
        if (!empty($this->view->statistics) && !Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) || Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 1) {
            $key = array_search('reviewCount', $this->view->statistics);
            if (!empty($key)) {
                unset($this->view->statistics[$key]);
            }
        }

        //GET EVENT SUBJECT
        $this->view->event = $event = Engine_Api::_()->core()->getSubject('siteevent_event');

        $params = array();
        $params['event_id'] = $event->event_id;

        $this->view->show = $show = $this->_getParam('show', 'owner');
        if ($show == 'host') {
            if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.host', 1)) {
                return $this->setNoRender();
            }

            $host = $event->getHost();
            if (!$host) {
                return $this->setNoRender();
            } else {
                $params['host_type'] = $event->host_type;
                $params['host_id'] = $event->host_id;
            }
            $this->getElement()->setTitle(sprintf($this->view->translate($this->getElement()->getTitle(), $this->view->htmlLink($host->getHref(), $host->getTitle(), array('title' => $host->getTitle())))));
        } else {
            $params['owner_id'] = $event->owner_id;
            $this->getElement()->setTitle(sprintf($this->view->translate($this->getElement()->getTitle(), $this->view->htmlLink($event->getOwner()->getHref(), $event->getOwner()->getTitle(), array('title' => $event->getOwner()->getTitle())))));
        }

        $this->view->category_id = $params['category_id'] = $this->_getParam('hidden_category_id', 0);
        if ($params['category_id']) {
            $params['subcategory_id'] = $this->_getParam('hidden_subcategory_id', 0);
            if ($params['subcategory_id'])
                $params['subsubcategory_id'] = $this->_getParam('hidden_subsubcategory_id', 0);
        }

        $this->view->titlePosition = $this->_getParam('titlePosition', 1);
        $this->view->count = $params['count'] = $this->_getParam('count', 3);
        $this->view->ratingType = $this->_getParam('ratingType', 'rating_avg');
        $this->view->truncation = $this->_getParam('truncation', 40);
        $this->view->truncationLocation = $this->_getParam('truncationLocation', 35);
        $this->view->showEventType = $params['showEventType'] = $this->_getParam('showEventType', 'upcoming');

        $params['eventType'] = $contentType = Zend_Controller_Front::getInstance()->getRequest()->getParam('eventType', null);
        if (empty($contentType)) {
            $params['eventType'] = $this->_getParam('eventType', 'All');
        }
        $this->view->contentType = $params['eventType'];
        $params['networkBased'] = $this->_getParam('networkBased', '0');
        $this->view->events = Engine_Api::_()->getDbTable('events', 'siteevent')->userEvent($params);
        $this->_childCount = count($this->view->events);

        if ($this->_childCount <= 0) {
            return $this->setNoRender();
        }

        $this->view->viewType = $this->_getParam('viewType', 'listview');
        $this->view->columnWidth = $this->_getParam('columnWidth', '180');
        $this->view->columnHeight = $this->_getParam('columnHeight', '328');
    }

    public function getChildCount() {
        return $this->_childCount;
    }

}
