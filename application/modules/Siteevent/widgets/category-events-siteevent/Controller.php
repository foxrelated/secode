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
class Siteevent_Widget_CategoryEventsSiteeventController extends Engine_Content_Widget_Abstract {

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

        //GET PARAMETERS FOR SORTING THE RESULTS
        $params = array();
        $itemCount = $params['itemCount'] = $this->_getParam('itemCount', 0);
        $params['showEventType'] = $this->view->showEventType = $this->_getParam('showEventType', 'upcoming');
        $params['popularity'] = $popularity = $this->_getParam('popularity', 'view_count');
        $params['interval'] = $interval = $this->_getParam('interval', 'overall');
        $params['limit'] = $totalPages = $this->_getParam('eventCount', 5);
        $this->view->title_truncation = $params['truncation'] = $this->_getParam('truncation', 25);
        $siteeventCategoriesEvents = Zend_Registry::isRegistered('siteeventCategoriesEvents') ? Zend_Registry::get('siteeventCategoriesEvents') : null;

        $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);
        if ($this->view->detactLocation) {
            $this->view->detactLocation = Engine_Api::_()->siteevent()->enableLocation();
        }
        if ($this->view->detactLocation) {
            $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
            $params['latitude'] = $this->_getParam('latitude', 0);
            $params['longitude'] = $this->_getParam('longitude', 0);
        }

        $this->view->params = $params;

        //GET CATEGORIES
        $categories = array();
        $category_info = Engine_Api::_()->getDbtable('categories', 'siteevent')->getCategorieshasevents(0, 'category_id', $itemCount, $params, array('category_id', 'category_name', 'cat_order'));

        foreach ($category_info as $value) {
            $category_events_array = array();

            $params['category_id'] = $value['category_id'];

            //GET PAGE RESULTS
            $category_events_info = $category_events_info = Engine_Api::_()->getDbtable('events', 'siteevent')->eventsBySettings($params);

            foreach ($category_events_info as $result_info) {
                $tmp_array = array('event_id' => $result_info->event_id,
                    'imageSrc' => $result_info->getPhotoUrl('thumb.icon'),
                    'event_title' => $result_info->title,
                    'owner_id' => $result_info->owner_id,
                    'populirityCount' => $result_info->$popularity,
                    'slug' => $result_info->getSlug());
                $category_events_array[] = $tmp_array;
            }
            $category_array = array('category_id' => $value->category_id,
                'category_name' => $value->category_name,
                'order' => $value->cat_order,
                'category_events' => $category_events_array
            );
            $categories[] = $category_array;
        }
        $this->view->categories = $categories;

        if (empty($siteeventCategoriesEvents))
            return $this->setNoRender();

        //SET NO RENDER
        if (!(count($this->view->categories) > 0)) {
            return $this->setNoRender();
        }
    }

}