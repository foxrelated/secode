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
class Siteevent_Widget_SponsoredSiteeventController extends Engine_Content_Widget_Abstract {

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

        $this->view->vertical = $values['viewType'] = $this->_getParam('viewType', 0);
        $values = array();

        $this->view->showEventType = $values['showEventType'] = $this->_getParam('showEventType', 'upcoming');
        $this->view->category_id = $values['category_id'] = $this->_getParam('hidden_category_id');
        $this->view->subcategory_id = $values['subcategory_id'] = $this->_getParam('hidden_subcategory_id');
        $this->view->subsubcategory_id = $values['subsubcategory_id'] = $this->_getParam('hidden_subsubcategory_id');
        $this->view->showPagination = $values['showPagination'] = $this->_getParam('showPagination', 1);
        $this->view->interval = $values['interval'] = $this->_getParam('interval', 300);
        $this->view->blockHeight = $values['blockHeight'] = $this->_getParam('blockHeight', 240);
        $this->view->blockWidth = $values['blockWidth'] = $this->_getParam('blockWidth', 150);
        $this->view->showOptions = $values['showOptions'] = $this->_getParam('eventInfo', array("category", "rating", "review"));
        $this->view->truncationLocation = $values['truncationLocation'] = $this->_getParam('truncationLocation', 35);
        $this->view->title_truncation = $values['truncation'] = $this->_getParam('truncation', 50);
        $this->view->ratingType = $values['ratingType'] = $this->_getParam('ratingType', 'rating_avg');
        $this->view->viewType = $values['viewType'] = $this->_getParam('viewType', 0);
        $this->view->limit = $values['limit'] = $this->_getParam('itemCount', 3);
        $this->view->popularity = $values['popularity'] = $this->_getParam('popularity', 'event_id');
        $this->view->fea_spo = $fea_spo = $values['fea_spo'] = $this->_getParam('fea_spo', null);
        if ($fea_spo == 'featured') {
            $values['featured'] = 1;
        } elseif ($fea_spo == 'newlabel') {
            $values['newlabel'] = 1;
        } elseif ($fea_spo == 'sponsored') {
            $values['sponsored'] = 1;
        } elseif ($fea_spo == 'fea_spo') {
            $values['sponsored_or_featured'] = 1;
        }

        $siteeventSponsoredEvents = Zend_Registry::isRegistered('siteeventSponsoredEvents') ? Zend_Registry::get('siteeventSponsoredEvents') : null;
        $this->view->detactLocation = $values['detactLocation'] = $this->_getParam('detactLocation', 0);
        if ($this->view->detactLocation) {
            $this->view->detactLocation = Engine_Api::_()->siteevent()->enableLocation();
        }
        $this->view->defaultLocationDistance = 1000;
        $this->view->latitude = 0;
        $this->view->longitude = 0;
        if ($this->view->detactLocation) {
            $this->view->defaultLocationDistance = $values['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
            $this->view->latitude = $values['latitude'] = $this->_getParam('latitude', 0);
            $this->view->longitude = $values['longitude'] = $this->_getParam('longitude', 0);
        }

        if (empty($siteeventSponsoredEvents))
            return $this->setNoRender();

        $this->view->params = $values;

        $values['eventType'] = $contentType = Zend_Controller_Front::getInstance()->getRequest()->getParam('eventType', null);
        if (empty($contentType)) {
            $values['eventType'] = $this->_getParam('eventType', 'All');
        }
        $this->view->contentType = $values['eventType'];

        //FETCH SPONSERED EVENTS
        $this->view->events = $event = Engine_Api::_()->getDbTable('events', 'siteevent')->getEvent('', $values);

        //GET LIST COUNT
        $this->view->totalCount = $event->getTotalItemCount();
        if (($this->view->totalCount <= 0)) {
            return $this->setNoRender();
        }
    }

}
