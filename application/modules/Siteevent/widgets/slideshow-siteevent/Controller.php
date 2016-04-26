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
class Siteevent_Widget_SlideshowSiteeventController extends Engine_Content_Widget_Abstract {

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

        $values = array();
        $values['limit'] = $this->_getParam('count', 10);
//    $this->view->statistics = $values['statistics'] = $this->_getParam('statistics', array("viewCount", "likeCount", "commentCount", "memberCount", "reviewCount"));
        $this->view->statistics = $values['statistics'] = $this->_getParam('eventInfo', array("viewCount", "likeCount", "commentCount", "memberCount", "reviewCount"));
        $siteeventSlideshowEvents = Zend_Registry::isRegistered('siteeventSlideshowEvents') ? Zend_Registry::get('siteeventSlideshowEvents') : null;

        $this->view->category_id = $values['category_id'] = $this->_getParam('hidden_category_id', 0);
        if ($values['category_id']) {
            $values['subcategory_id'] = $this->_getParam('hidden_subcategory_id', 0);
            if ($values['subcategory_id'])
                $values['subsubcategory_id'] = $this->_getParam('hidden_subsubcategory_id', 0);
        }

        if (empty($siteeventSlideshowEvents))
            return $this->setNoRender();

        $values['showEventType'] = $this->view->showEventType = $this->_getParam('showEventType', 'upcoming');
        $this->view->title_truncation = $values['truncation'] = $this->_getParam('truncation', 45);
        $this->view->truncationLocation = $values['truncationLocation'] = $this->_getParam('truncationLocation', 35);
        $this->view->ratingType = $values['ratingType'] = $this->_getParam('ratingType', 'rating_avg');
        $this->view->popularity = $values['popularity'] = $this->_getParam('popularity', 'event_id');
        $this->view->fea_spo = $fea_spo = $values['fea_spo'] = $this->_getParam('fea_spo', '');
        $this->view->truncationDescription = $values['truncationDescription'] = $this->_getParam('truncationDescription', 150);
//    $this->view->sponsoredIcon = $values['sponsoredIcon'] = $this->_getParam('sponsoredIcon', 1);
//    $this->view->featuredIcon = $values['featuredIcon'] = $this->_getParam('featuredIcon', 1);
//    $this->view->newIcon = $values['newIcon'] = $this->_getParam('newIcon', 1);
        if ($fea_spo == 'featured') {
            $values['featured'] = 1;
        } elseif ($fea_spo == 'sponsored') {
            $values['sponsored'] = 1;
        } elseif ($fea_spo == 'newlabel') {
            $values['newlabel'] = 1;
        } elseif ($fea_spo == 'fea_spo') {
            $values['sponsored_or_featured'] = 1;
        }
        $values['interval'] = $interval = $this->_getParam('interval', 'overall');
        $this->view->blockHeight = $values['blockHeight'] = $this->_getParam('blockHeight', 195);

        $this->view->detactLocation = $values['detactLocation'] = $this->_getParam('detactLocation', 0);
        if ($this->view->detactLocation) {
            $this->view->detactLocation = Engine_Api::_()->siteevent()->enableLocation();
        }
        if ($this->view->detactLocation) {
            $values['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
            $values['latitude'] = $this->_getParam('latitude', 0);
            $values['longitude'] = $this->_getParam('longitude', 0);
        }

        $this->view->params = $values;
        $values['eventType'] = $contentType = Zend_Controller_Front::getInstance()->getRequest()->getParam('eventType', null);
        if (empty($contentType)) {
            $values['eventType'] = $this->_getParam('eventType', 'All');
        }
        $this->view->contentType = $values['eventType'];

        //FETCH FEATURED EVENTS
        $this->view->show_slideshow_object = Engine_Api::_()->getDbTable('events', 'siteevent')->eventsBySettings($values);

        //RESULTS COUNT
        $this->view->num_of_slideshow = count($this->view->show_slideshow_object) > $values['limit'] ? $values['limit'] : count($this->view->show_slideshow_object);
        if (($this->view->num_of_slideshow <= 0)) {
            return $this->setNoRender();
        }

        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    }

}
