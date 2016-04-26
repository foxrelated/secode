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
class Siteevent_Widget_UserSiteeventController extends Seaocore_Content_Widget_Abstract {

    protected $_childCount;

    public function indexAction() {

        //CHECK SUBJECT
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return $this->setNoRender();
        }
        //GET THE DEFAULT OCCURRENCE ID
        $this->view->occurrence_id = $occurrence_id = Zend_Registry::isRegistered('occurrence_id') ? Zend_Registry::get('occurrence_id') : null;
        if (empty($occurrence_id)) {
            $this->view->occurrence_id = $occurrence_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('occurrence_id', null);
        }

        //GET EVENT SUBJECT
        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        if(Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviewbeforeeventend', 1)) {
            $endDate = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($siteevent->event_id);
            $currentDate = date('Y-m-d H:i:s');
            $endDate = strtotime($endDate);
            $currentDate = strtotime($currentDate);
            if ($endDate > $currentDate) {
                return $this->setNoRender();
            }
        }

        $this->view->event_id = $event_id = $siteevent->getIdentity();

        $siteeventUserEvents = Zend_Registry::isRegistered('siteeventUserEvents') ? Zend_Registry::get('siteeventUserEvents') : null;
        $siteeventLsettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.lsettings', false);

        if (empty($siteeventUserEvents) || !Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) || Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 1) {
            return $this->setNoRender();
        }

        //GET REVIEW TABLE
        $reviewTable = Engine_Api::_()->getDbTable('reviews', 'siteevent');

        //SET PARAMS
        $this->view->params = $this->_getAllParams();
        $this->view->params['occurrence_id'] = $occurrence_id;
        //UNSET CAPTCHA WORD
        $session = new Zend_Session_Namespace();
        if (isset($session->setword)) {
            unset($session->setword);
        }

        //LOADED BY AJAX
        if ($this->_getParam('loaded_by_ajax', false)) {
            $this->view->loaded_by_ajax = true;
            if ($this->_getParam('is_ajax_load', false)) {
                $this->view->is_ajax_load = true;
                $this->view->loaded_by_ajax = false;
                if (!$this->_getParam('onloadAdd', false))
                    $this->getElement()->removeDecorator('Title');
                $this->getElement()->removeDecorator('Container');
            } else {
                $params['resource_id'] = $event_id;
                $params['resource_type'] = $siteevent->getType();
                $params['type'] = 'user';
                $paginator = $reviewTable->listReviews($params);
                $this->_childCount = $paginator->getTotalItemCount();
                return;
            }
        }
        $this->view->showContent = true;

        //GET VIEWER ID
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $this->view->level_id = $level_id = $viewer->level_id;
        } else {
            $this->view->level_id = $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $autorizationApi = Engine_Api::_()->authorization();
        $this->view->create_level_allow = $create_level_allow = $autorizationApi->getPermission($level_id, 'siteevent_event', "review_create");

        $this->view->can_update = $can_update = $autorizationApi->getPermission($level_id, 'siteevent_event', "review_update");

        $create_review = ($siteevent->owner_id == $viewer_id) ? Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.allowownerreview', 1) : 1;

        if (!$create_review || empty($create_level_allow) || !Engine_Api::_()->siteevent()->allowReviewCreate($siteevent)) {
            $this->view->can_create = 0;
        } else {
            $this->view->can_create = 1;
        }

        //GET RATING TABLE
        $ratingTable = Engine_Api::_()->getDbTable('ratings', 'siteevent');
        $coreApi = Engine_Api::_()->getApi('settings', 'core');

        //GET WIDGET PARAMETERS
        $this->view->siteevent_proscons = $siteevent_proscons = $coreApi->getSetting('siteevent.proscons', 1);
        $siteevent_limit_proscons = $coreApi->getSetting('siteevent.limit.proscons', 500);
        $siteevent_recommend = $coreApi->getSetting('siteevent.recommend', 1);
        $this->view->siteevent_report = $coreApi->getSetting('siteevent.report', 1);
        $this->view->siteevent_email = $coreApi->getSetting('siteevent.email', 1);
        $this->view->siteevent_share = $coreApi->getSetting('siteevent.share', 1);

        //GET REVIEW ID
        if (!empty($viewer_id)) {
            $params = array();
            $params['resource_id'] = $siteevent->event_id;
            $params['resource_type'] = $siteevent->getType();
            $params['viewer_id'] = $viewer_id;
            $params['type'] = 'user';
            $review_id = $this->view->hasPosted = $reviewTable->canPostReview($params);
        } else {
            $review_id = $this->view->hasPosted = 0;
        }

        //CREATE FORM
        if ($this->view->can_create && !$review_id) {

            //FATCH REVIEW CATEGORIES
            $categoryIdsArray = array();
            $categoryIdsArray[] = $siteevent->category_id;
            $categoryIdsArray[] = $siteevent->subcategory_id;
            $categoryIdsArray[] = $siteevent->subsubcategory_id;
            $profileTypeReview = Engine_Api::_()->getDbtable('categories', 'siteevent')->getProfileType($categoryIdsArray, 0, 'profile_type_review');

            $this->view->form = new Siteevent_Form_Review_Create(array("settingsReview" => array('siteevent_proscons' => $siteevent_proscons, 'siteevent_limit_proscons' => $siteevent_limit_proscons, 'siteevent_recommend' => $siteevent_recommend), 'item' => $siteevent, 'profileTypeReview' => $profileTypeReview));
        }

        //UPDATE FORM
        if ($can_update && $review_id) {
            $this->view->update_form = $update_form = new Siteevent_Form_Review_Update(array('item' => $siteevent));
        }

        //START TOP SECTION FOR OVERALL RATING AND IT'S PARAMETER
        $params = array();
        $params['resource_id'] = $event_id;
        $params['resource_type'] = $siteevent->getType();
        $params['type'] = 'user';
        $noReviewCheck = $reviewTable->getAvgRecommendation($params);
        if (!empty($noReviewCheck)) {
            $this->view->noReviewCheck = $noReviewCheck->toArray();
            if ($this->view->noReviewCheck)
                $this->view->recommend_percentage = round($noReviewCheck[0]['avg_recommend'] * 100, 3);
        }
        $this->view->ratingDataTopbox = $ratingTable->ratingbyCategory($event_id, 'user', $siteevent->getType());

        $this->view->isajax = $this->_getParam('isajax', 0);

        //GET FILTER
        $option = $this->_getParam('option', 'fullreviews');
        $this->view->reviewOption = $params['option'] = $option;

        //SET ITEM PER PAGE
        if ($option == 'prosonly' || $option == 'consonly') {
            $this->view->itemProsConsCount = $setItemCountPerPage = $this->_getParam('itemProsConsCount', 20);
        } else {
            $this->view->itemReviewsCount = $setItemCountPerPage = $this->_getParam('itemReviewsCount', 5);
        }

        //GET SORTING ORDER
        $this->view->reviewOrder = $params['order'] = $this->_getParam('order', 'creationDate');
        $this->view->rating_value = $this->_getParam('rating_value', 0);

        $params['rating'] = 'rating';
        $params['rating_value'] = $this->view->rating_value;
        $params['resource_id'] = $event_id;
        $params['resource_type'] = $siteevent->getType();
        $params['type'] = 'user';
        $this->view->params = $params;
        $paginator = $reviewTable->listReviews($params);
        $this->view->paginator = $paginator->setItemCountPerPage($setItemCountPerPage);
        $this->view->current_page = $current_page = $this->_getParam('page', 1);
        $this->view->paginator = $paginator->setCurrentPageNumber($current_page);

        //GET TOTAL REVIEWS
        $this->_childCount = $this->view->totalReviews = $paginator->getTotalItemCount();

        //FATCH REVIEW CATEGORIES
        $categoryIdsArray = array();
        $categoryIdsArray[] = $siteevent->category_id;
        $categoryIdsArray[] = $siteevent->subcategory_id;
        $categoryIdsArray[] = $siteevent->subsubcategory_id;
        $this->view->reviewCategory = Engine_Api::_()->getDbtable('ratingparams', 'siteevent')->reviewParams($categoryIdsArray, $siteevent->getType());

        //COUNT REVIEW CATEGORY
        $this->view->total_reviewcats = Count($this->view->reviewCategory);

        //GET REVIEW RATE DATA
        $this->view->reviewRateMyData = $this->view->reviewRateData = $ratingTable->ratingsData($review_id);

        //CAN DELETE
        $this->view->can_delete = $autorizationApi->getPermission($level_id, 'siteevent_event', "review_delete");

        //CAN REPLY
        $this->view->can_reply = $autorizationApi->getPermission($level_id, 'siteevent_event', "review_reply");

        //CHECK PAGE
        $this->view->checkPage = "eventProfile";

        //CUSTOM FIELDS
        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Siteevent/View/Helper', 'Siteevent_View_Helper');
    }

    public function getChildCount() {
        return $this->_childCount;
    }

}
