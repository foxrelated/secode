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
class Siteevent_Widget_ProfileReviewSiteeventController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF NOT AUTHORIZED
        if (!Engine_Api::_()->core()->hasSubject('siteevent_review')) {
            return $this->setNoRender();
        }

        //UNSET THE CAPTECHA WORD
        $session = new Zend_Session_Namespace();
        if (isset($session->setword)) {
            unset($session->setword);
        }

        //SET PARAMS
        $this->view->params = $params = $this->_getAllParams();

        //GET VIEWER DETAIL
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        $this->_mobileAppFile = true;
        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $this->view->level_id = $level_id = $viewer->level_id;
        } else {
            $this->view->level_id = $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        //GET REVIEW TABLE
        $reviewTable = Engine_Api::_()->getDbTable('reviews', 'siteevent');

        //GET RATING TABLE
        $ratingTable = Engine_Api::_()->getDbtable('ratings', 'siteevent');

        //GET SITEEVENT
        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject()->getParent();

        $this->view->event_id = $event_id = $siteevent->event_id;
        $resource_type = $siteevent->getType();



        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) || Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 1) {
            return $this->setNoRender();
        }

        //SET HAS POSTED
        if (empty($viewer_id)) {
            $hasPosted = $this->view->hasPosted = 0;
        } else {
            $params = array();
            $params['resource_id'] = $siteevent->event_id;
            $params['resource_type'] = $resource_type;
            $params['viewer_id'] = $viewer_id;
            $params['type'] = 'user';
            $hasPosted = $this->view->hasPosted = $reviewTable->canPostReview($params);
        }

        //GET WIDGET PARAMETERS
        $coreApi = Engine_Api::_()->getApi('settings', 'core');
        $this->view->getListType = true;
        $this->view->siteevent_proscons = $siteevent_proscons = $coreApi->getSetting('siteevent.proscons', 1);
        $this->view->siteevent_limit_proscons = $siteevent_limit_proscons = $coreApi->getSetting('siteevent.limit.proscons', 500);
        $this->view->siteevent_recommend = $siteevent_recommend = $coreApi->getSetting('siteevent.recommend', 1);
        $this->view->siteevent_report = $coreApi->getSetting('siteevent.report', 1);
        $this->view->siteevent_email = $coreApi->getSetting('siteevent.email', 1);
        $this->view->siteevent_share = $coreApi->getSetting('siteevent.share', 1);

        $this->view->create_level_allow = $create_level_allow = Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "review_create");

        $create_review = ($siteevent->owner_id == $viewer_id) ? Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.allowownerreview', 1) : 1;

        if (!$create_review || empty($create_level_allow)) {
            $this->view->can_create = 0;
        } else {
            $this->view->can_create = 1;
        }

        $this->view->can_delete = Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "review_delete");

        $this->view->can_reply = Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "review_reply");

        $this->view->can_update = $can_update = Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "review_update");

        //MAKE CREATE FORM
        if ($this->view->can_create && !$hasPosted) {

            //FATCH REVIEW CATEGORIES
            $categoryIdsArray = array();
            $categoryIdsArray[] = $siteevent->category_id;
            $categoryIdsArray[] = $siteevent->subcategory_id;
            $categoryIdsArray[] = $siteevent->subsubcategory_id;
            $profileTypeReview = Engine_Api::_()->getDbtable('categories', 'siteevent')->getProfileType($categoryIdsArray, 0, 'profile_type_review');

            $this->view->form = $form = new Siteevent_Form_Review_Create(array("settingsReview" => array('siteevent_proscons' => $this->view->siteevent_proscons, 'siteevent_limit_proscons' => $this->view->siteevent_limit_proscons, 'siteevent_recommend' => $this->view->siteevent_recommend), 'item' => $siteevent, 'profileTypeReview' => $profileTypeReview));
        }

        $this->view->review_id = $review_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('review_id');

        //UPDATE FORM
        if ($can_update && $hasPosted) {
            $this->view->update_form = new Siteevent_Form_Review_Update(array('item' => $siteevent));
        }

        //GET REVIEW ITEM
        $this->view->reviews = Engine_Api::_()->getItem('siteevent_review', $review_id);
        $this->view->tab_id = Engine_Api::_()->siteevent()->getTabId('siteevent.user-siteevent');
        $params = array();
        $params['resource_id'] = $event_id;
        $params['resource_type'] = $resource_type;
        $params['type'] = 'user';
        $this->view->totalReviews = $reviewTable->totalReviews($params);

        if ($this->view->reviews->profile_type_review) {
            //CUSTOM FIELDS
            $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Siteevent/View/Helper', 'Siteevent_View_Helper');
            $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($this->view->reviews);
        }

        //START TOP SECTION FOR OVERALL RATING AND IT'S PARAMETER
        $noReviewCheck = $reviewTable->getAvgRecommendation($params);
        if (!empty($noReviewCheck)) {
            $this->view->noReviewCheck = $noReviewCheck->toArray();
            if($this->view->noReviewCheck)
            $this->view->recommend_percentage = round($noReviewCheck[0]['avg_recommend'] * 100, 3);
            $this->view->ratingDataTopbox = $ratingTable->ratingbyCategory($event_id, 'user', $resource_type);
        }

        //FATCH REVIEW CATEGORIES
        $categoryIdsArray = array();
        $categoryIdsArray[] = $siteevent->category_id;
        $categoryIdsArray[] = $siteevent->subcategory_id;
        $categoryIdsArray[] = $siteevent->subsubcategory_id;
        $this->view->reviewCategory = Engine_Api::_()->getDbtable('ratingparams', 'siteevent')->reviewParams($categoryIdsArray, $resource_type);
        $this->view->total_reviewcats = Count($this->view->reviewCategory);
        $this->view->reviewRateData = $ratingTable->ratingsData($review_id);
        $this->view->reviewRateMyData = $ratingTable->ratingsData($hasPosted);
        $this->view->checkPage = "reviewProfile";
        $this->view->is_ajax = $is_ajax = $this->_getParam('is_ajax', '');

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.price', 0)) {
            $this->view->price = $siteevent->price;
        }
    }

}