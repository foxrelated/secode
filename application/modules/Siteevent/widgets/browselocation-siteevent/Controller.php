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
class Siteevent_Widget_BrowselocationSiteeventController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $this->view->statistics = $this->_getParam('eventInfo', array("likeCount", "memberCount"));
        $this->view->ratingType = $this->_getParam('ratingType', 'rating_avg');
        $this->view->locationDetection = $this->_getParam('locationDetection', 0);
        $this->view->showAllCategories = $this->_getParam('showAllCategories',1);
        // Make form
        $this->view->widgetSettings = $widgetSettings = array(
            'priceFieldType' => $this->_getParam('priceFieldType', 'slider'),
            'minPrice' => $this->_getParam('minPrice', 0),
            'maxPrice' => $this->_getParam('maxPrice', 999),
            'showAllCategories' => $this->view->showAllCategories,
            'locationDetection' => $this->view->locationDetection,
        );

        $siteeventLocationAddressEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventlocaddress.enabled', 1);
        // Make form
        $this->view->form = $form = new Siteevent_Form_Locationsearch(array('widgetSettings' => $widgetSettings));
        $siteeventBrowseLocation = Zend_Registry::isRegistered('siteeventBrowseLocation') ? Zend_Registry::get('siteeventBrowseLocation') : null;

        if (!empty($_POST)) {
            $this->view->is_ajax = $_POST['is_ajax'];
        }

        $front = Zend_Controller_Front::getInstance();
        $module = $front->getRequest()->getModuleName();
        $action = $front->getRequest()->getActionName();
        $controller = $front->getRequest()->getControllerName();
        if (!($module == 'siteevent' && $controller == 'index' && $action == 'map') && empty($this->view->is_ajax)) {
            return $this->setNoRender();
        }

        if (empty($siteeventLocationAddressEnabled)) {
            return $this->setNoRender();
        }

        if (empty($_POST['location'])) {
            $this->view->locationVariable = '1';
        }

        if (empty($_POST['is_ajax'])) {

            $values = $form->getValues();
            $customFieldValues = array_intersect_key($values, $form->getFieldElements());
            $this->view->is_ajax = $this->_getParam('is_ajax', 0);
        } else {
            $values = $_POST;
            $form->isValid($values);
            $parms = $form->getValues();
            $values = array_merge($values, $parms);
            $customFieldValues = array_intersect_key($values, $form->getFieldElements());
        }

        if (empty($siteeventBrowseLocation))
            return $this->setNoRender();

        unset($values['or']);
        $this->view->assign($values);
        if (@$values['show'] == 2) {
            $friendsIds = Engine_Api::_()->user()->getViewer()->membership()->getMembers();
            $ids = array();
            foreach ($friendsIds as $friendId) {
                $ids[] = $friendId->user_id;
            }
            $values['users'] = $ids;
        }
        
        $request = Zend_Controller_Front::getInstance()->getRequest();

        $values['type'] = 'browse';
        $values['type_location'] = 'browseLocation';
        //$values['action'] = 'upcoming';
        $values['action'] = $this->view->showEventType = $request->getParam('showEventType', 'upcoming');
        $this->view->truncationLocation = $this->_getParam('truncationLocation', 35);

        if (isset($values['show'])) {
            if ($form->show->getValue() == 3) {
                @$values['show'] = 3;
            }
        }

        $this->view->current_page = $page = $this->_getParam('page', 1);
        $this->view->current_totalpages = $page * 15;
        $this->view->enableLocation = $checkLocation = Engine_Api::_()->siteevent()->enableLocation();
        $this->view->enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.price.field', 1);

        //check for miles or street.
        if (isset($values['locationmiles']) && !empty($values['locationmiles'])) {

            if (isset($values['siteevent_street']) && !empty($values['siteevent_street'])) {
                $values['location'] = $values['siteevent_street'] . ',';
                unset($values['siteevent_street']);
            }

            if (isset($values['siteevent_city']) && !empty($values['siteevent_city'])) {
                $values['location'].= $values['siteevent_city'] . ',';
                unset($values['siteevent_city']);
            }

            if (isset($values['siteevent_state']) && !empty($values['siteevent_state'])) {
                $values['location'].= $values['siteevent_state'] . ',';
                unset($values['siteevent_state']);
            }

            if (isset($values['siteevent_country']) && !empty($values['siteevent_country'])) {
                $values['location'].= $values['siteevent_country'];
                unset($values['siteevent_country']);
            }
        }

        $result = Engine_Api::_()->getDbtable('events', 'siteevent')->getSiteeventsSelect($values, $customFieldValues);
        $this->view->paginator = $paginator = Zend_Paginator::factory($result);
        $paginator->setItemCountPerPage(15);
        $this->view->paginator = $paginator->setCurrentPageNumber($page);
        $this->view->totalresults = $paginator->getTotalItemCount();
        $this->view->mobile = Engine_Api::_()->seaocore()->isMobile();

        if (!empty($_POST['is_ajax'])) {

            $this->view->flageSponsored = 0;
            if (!empty($checkLocation) && $paginator->getTotalItemCount() > 0) {
                $ids = array();
                foreach ($paginator as $event) {
                    $id = $event->getIdentity();
                    $ids[] = $id;
                    $event_temp[$id] = $event;
                }

                $values['event_ids'] = $ids;
                $this->view->locations = $locations = Engine_Api::_()->getDbtable('locations', 'siteevent')->getLocation($values);

                foreach ($locations as $location) {
                    if ($event_temp[$location->event_id]->sponsored) {
                        $this->view->flageSponsored = 1;
                        break;
                    }
                }

                $this->view->list = $event_temp;
            } else {
                $this->view->enableLocation = 0;
            }
        } else {
            $categories = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategories(array('category_id', 'category_name', 'category_slug'), null, 0, 0, 1);
            $categories_slug[0] = "";
            if (count($categories) != 0) {
                foreach ($categories as $category) {
                    $categories_slug[$category->category_id] = $category->getCategorySlug();
                }
            }
            $this->view->categories_slug = $categories_slug;
        }
    }

}
