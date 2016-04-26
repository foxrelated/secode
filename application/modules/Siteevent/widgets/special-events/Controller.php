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
class Siteevent_Widget_SpecialEventsController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $starttime = $this->_getParam('starttime');
        $endtime = $this->_getParam('endtime');
        $currenttime = date('Y-m-d H:i:s');

        if (!empty($starttime) && $currenttime < $starttime) {
            return $this->setNoRender();
        }

        if (!empty($endtime) && $currenttime > $endtime) {
            return $this->setNoRender();
        }

        $params = array();
        $params['event_ids'] = $this->_getParam('toValues', array());
        if (!empty($params['event_ids'])) {
            $params['event_ids'] = explode(',', $params['event_ids']);
            $params['event_ids'] = array_unique($params['event_ids']);

            if (!empty($params['event_ids']) && Count($params['event_ids']) <= 0) {
                return $this->setNoRender();
            }
        } else {
            return $this->setNoRender();
        }

        $params['limit'] = $params['itemCount'] = $this->_getParam('itemCount', 3);
        $params['popularity'] = 'random';
        $params['showEventType'] = $this->view->showEventType = 'all';
        $this->view->titlePosition = $this->_getParam('titlePosition', 1);

        $this->view->statistics = $params['statistics'] = $this->_getParam('eventInfo', array("likeCount", "memberCount"));
        $this->view->layouts_views = $params['layouts_views'] = $this->_getParam('layouts_views', array("listview", "gridview"));
        $this->view->postedby = $params['postedby'] = $this->_getParam('postedby', 1);
        $params['ratingType'] = $this->view->ratingType = $this->_getParam('ratingType', 'rating_avg');

        $this->view->showContent = $params['showContent'] = $this->_getParam('showContent', array("price", "location"));

        $siteeventSpecialEvents = Zend_Registry::isRegistered('siteeventSpecialEvents') ? Zend_Registry::get('siteeventSpecialEvents') : null;

        $this->view->bottomLine = $params['bottomLine'] = $this->_getParam('bottomLine', 2);
        $this->view->bottomLineGrid = $params['bottomLineGrid'] = $this->_getParam('bottomLineGrid', 2);
        $this->view->columnWidth = $params['columnWidth'] = $this->_getParam('columnWidth', '180');
        $this->view->truncationLocation = $params['truncationLocation'] = $this->_getParam('truncationLocation', 35);
        $this->view->title_truncationGrid = $params['truncationGrid'] = $this->_getParam('truncationGrid', 100);
        $this->view->columnHeight = $params['columnHeight'] = $this->_getParam('columnHeight', '328');
        $params['interval'] = $interval = $this->_getParam('interval', 'overall');
        if (empty($siteeventSpecialEvents))
            return $this->setNoRender();

        $this->view->enableLocation = $checkLocation = Engine_Api::_()->siteevent()->enableLocation();

        $params['page'] = $this->_getParam('page', 1);

        $this->view->identity = $params['identity'] = $this->_getParam('identity', $this->view->identity);

        $this->view->params = $params;

        //GET EVENTS
        $this->view->events = $paginator = Engine_Api::_()->getDbTable('events', 'siteevent')->eventsBySettings($params);

        if (@count($paginator) <= 0) {
            return $this->setNoRender();
        }

        $this->view->viewType = $params['viewType'] = $this->_getParam('viewType', 'gridview');
        $this->view->eventType = $params['eventType'] = $this->_getParam('eventType', 'gridview');
    }

}
