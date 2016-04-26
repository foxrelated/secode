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
class Siteevent_Widget_EditorReviewsSiteeventController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        //CHECK SUBJECT
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return $this->setNoRender();
        }

        //GET VIEWER ID
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET SUBJECT
        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject();

        //GET EDITOR REVIEW ID
        $params = array();
        $params['resource_id'] = $siteevent->event_id;
        $params['resource_type'] = $siteevent->getType();
        $params['viewer_id'] = 0;
        $params['type'] = 'editor';




        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 1 || Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 3) {
            $this->view->addEditorReview = $editor_review_id = Engine_Api::_()->getDbTable('reviews', 'siteevent')->canPostReview($params);
        } else {
            $this->view->addEditorReview = $editor_review_id = 0;
        }

        $params = $this->_getAllParams();
        $this->view->params = $params;

        $element = $this->getElement();
        $this->view->overview = Engine_Api::_()->getDbTable('otherinfo', 'siteevent')->getColumnValue($siteevent->event_id, 'overview');
        
        //START PACKAGE WORK
        $this->view->CanShowOverview = $CanShowOverview = 1;
        if (Engine_Api::_()->siteevent()->hasPackageEnable() && !Engine_Api::_()->siteeventpaid()->allowPackageContent($siteevent->package_id, "overview")) { 
           $this->view->CanShowOverview = $CanShowOverview = 0;
        }
        //END PACKAGE WORK
        
        $siteeventEditorReviews = Zend_Registry::isRegistered('siteeventEditorReviews') ? Zend_Registry::get('siteeventEditorReviews') : null;

        $titleWidget = null;
        if ($editor_review_id) {
            $titleWidget = $this->_getParam('titleEditor');
        } elseif ($this->view->overview && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.overview', 1)) {
            if(!empty($CanShowOverview))
            $titleWidget = $this->_getParam('titleOverview');
            else
            $titleWidget = $this->_getParam('titleDescription');
        } else {
            $titleWidget = $this->_getParam('titleDescription');
        }
        if (!empty($titleWidget))
            $element->setTitle($titleWidget);

        if (empty($siteeventEditorReviews))
            return $this->setNoRender();

        //GET SLIDESHOW WIDTH HEIGHT
        $this->view->slideshow_width = $this->_getParam('slideshow_width', 600);
        $this->view->slideshow_height = $this->_getParam('slideshow_height', 400);
        $this->view->showCaption = $this->_getParam('showCaption', 1);
        $this->view->captionTruncation = $this->_getParam('captionTruncation', 200);
        $this->view->showButtonSlide = $this->_getParam('showButtonSlide', 1);
        $this->view->mouseEnterEvent = $this->_getParam('mouseEnterEvent', 0);
        $this->view->thumbPosition = $this->_getParam('thumbPosition', 'bottom');
        $this->view->autoPlay = $this->_getParam('autoPlay', 0);
        $this->view->slidesLimit = $this->_getParam('slidesLimit', 20);
        $this->view->show_slideshow = $this->_getParam('show_slideshow', 1);
        if ($this->_getParam('loaded_by_ajax', false)) {
            $this->view->loaded_by_ajax = true;
            if ($this->_getParam('is_ajax_load', false)) {
                $this->view->is_ajax_load = true;
                $this->view->loaded_by_ajax = false;
                if (!$this->_getParam('onloadAdd', false))
                    $this->getElement()->removeDecorator('Title');
                $this->getElement()->removeDecorator('Container');
            } else {
                return;
            }
        }
        $this->view->showContent = true;

        //GET REVIEW
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->view->review = $review = Engine_Api::_()->getItem('siteevent_review', $editor_review_id);
        if (!empty($review)) {
            $this->view->editor = Engine_Api::_()->getDbTable('editors', 'siteevent')->getEditor($review->owner_id);

            $this->view->current = $page = $request->getParam('page', 1);

            $encoded_code = Zend_Json_Decoder::decode($review->body_pages);
            $this->view->body_pages = $encoded_code[$page - 1];
            $this->view->pageCount = $total_page = Count($encoded_code);
            $this->view->last = $total_page;
            $this->view->pagesInRange = array();
            for ($i = 1; $i <= $total_page; $i++) {
                $this->view->pagesInRange[] = $i;
            }

            if ($page > 1) {
                $this->view->previous = $page - 1;
            }

            if ($page < $total_page) {
                $this->view->next = $page + 1;
            }

            if ($total_page == $page) {
                $this->view->showconclusion = true;
            }

            //GET RATING TABLE
            $ratingTable = Engine_Api::_()->getDbTable('ratings', 'siteevent');
            $this->view->userRatingDataTopbox = $ratingTable->ratingbyCategory($siteevent->event_id, 'user', $siteevent->getType());
            if ($page == 1) {
                //if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.wheretobuy', 0)) {
                if (0) {
                    $this->view->min_price = $siteevent->getWheretoBuyMinPrice();
                    $this->view->max_price = $siteevent->getWheretoBuyMaxPrice();
                }
            }

            if (!empty($review->profile_type_review)) {
                //CUSTOM FIELDS
                $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Siteevent/View/Helper', 'Siteevent_View_Helper');
                $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($review);
            }
        }
        $this->view->isAjax = $isAjax = $request->getParam('isAjax', 0);
        $this->view->showComments = $this->_getParam('showComments', 1);
        if (!empty($isAjax)) {
            $this->getElement()->removeDecorator('Title');
            $this->getElement()->removeDecorator('Container');
        }
    }

}