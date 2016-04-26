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
class Siteevent_Widget_OverallRatingsController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        //SET NO RENDER IF NO SUBJECT
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return $this->setNoRender();
        }

        //GET SUBJECT
        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        if(Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviewbeforeeventend', 1)) {
            $endDate = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($siteevent->event_id);
            $siteeventOverallRatings = Zend_Registry::isRegistered('siteeventOverallRatings') ? Zend_Registry::get('siteeventOverallRatings') : null;
            $currentDate = date('Y-m-d H:i:s');
            $endDate = strtotime($endDate);
            $currentDate = strtotime($currentDate);
            if ($endDate > $currentDate || (empty($siteevent->rating_editor) && empty($siteevent->rating_users)) || empty($siteeventOverallRatings)) {
                return $this->setNoRender();
            }
        }

        //GET SETTING
        $this->view->show_rating = $show_rating = $this->_getParam('show_rating', 'both');
        $this->view->ratingParameter = $ratingParameter = $this->_getParam('ratingParameter', 1);
        //DO NOT RENDER THIS WIDGET IF BOTH TYPE OF REVIEWS ARE NOT ALLOWED
        $this->view->reviewsAllowed = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2);
        if (empty($this->view->reviewsAllowed)) {
            return $this->setNoRender();
        } elseif ($this->view->reviewsAllowed == 1) {
            $this->view->show_rating = $show_rating = 'editor';
        } elseif ($this->view->reviewsAllowed == 2) {
            $this->view->show_rating = $show_rating = 'avg';
        }

        $this->view->event_id = $event_id = $siteevent->getIdentity();

        //GET REVIEW TABLE
        $reviewTable = Engine_Api::_()->getDbTable('reviews', 'siteevent');
        //GET RATING TABLE
        $ratingTable = Engine_Api::_()->getDbTable('ratings', 'siteevent');

        if ($show_rating == 'both' || $show_rating == 'avg') {
            //START TOP SECTION FOR OVERALL RATING AND IT'S PARAMETER
            $params = array();
            $params['resource_id'] = $event_id;
            $params['resource_type'] = $siteevent->getType();
            $noReviewCheck = $reviewTable->getAvgRecommendation($params);
            if (!empty($noReviewCheck)) {
                $this->view->noReviewCheck = $noReviewCheck->toArray();
                if($this->view->noReviewCheck)
                $this->view->recommend_percentage = round($noReviewCheck[0]['avg_recommend'] * 100, 3);
            }
            $type = null;
            if ($show_rating == 'both') {
                $type = 'user';
            }
            $this->view->type = $type;
            $this->view->ratingData = $ratingTable->ratingbyCategory($event_id, $type, $siteevent->getType());
        }

        if ($show_rating == 'both' || $show_rating == 'editor') {
            $this->view->ratingEditorData = $ratingTable->ratingbyCategory($event_id, 'editor', $siteevent->getType());
            $this->view->editorReview = $siteevent->getEditorReview();
        }

        $identity = Engine_Api::_()->siteevent()->existWidget('siteevent_reviews', 0);
        $this->view->contentDetails = Engine_Api::_()->siteevent()->getWidgetInfo('siteevent.user-siteevent', $identity);
    }

}