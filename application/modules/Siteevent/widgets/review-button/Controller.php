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
class Siteevent_Widget_ReviewButtonController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        $event_guid = $this->_getParam('event_guid', null);
        $identity = $this->_getParam('identity', 0);
        $this->view->seeAllReviews = $this->_getParam('seeAllReviews', 0);
        $this->view->event_profile_page = $this->_getParam('event_profile_page', 0);
        if (empty($event_guid) && !Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return $this->setNoRender();
        }

        if (empty($event_guid) && Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');
            $event_guid = $siteevent->getGuid();
            $this->view->event_profile_page = 1;
            $identity = Engine_Api::_()->siteevent()->existWidget('siteevent_reviews', 0);
        } else {
            $this->getElement()->removeDecorator('Title');
            $this->getElement()->removeDecorator('Container');
        }

        $this->view->siteevent = $siteevent = Engine_Api::_()->getItemByGuid($event_guid);
        $this->view->event_id = $event_id = $siteevent->event_id;

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) || Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 1) {
            return $this->setNoRender();
        }

        //GET VIEWER
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $this->view->level_id = $level_id = $viewer->level_id;
        } else {
            $this->view->level_id = $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $create_review = ($siteevent->owner_id == $viewer_id) ? Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.allowownerreview', 1) : 1;
        $creationAllowOwner = 1;
        if (empty($create_review) && $siteevent->rating_users <= 0) {
            $creationAllowOwner = 0;
        }

        //GET RATING TABLE
        $ratingTable = Engine_Api::_()->getDbTable('ratings', 'siteevent');

        //GET REVIEW TABLE
        $reviewTable = Engine_Api::_()->getDbTable('reviews', 'siteevent');
        if ($viewer_id) {
            $params = array();
            $params['resource_id'] = $siteevent->event_id;
            $params['resource_type'] = $siteevent->getType();
            $params['viewer_id'] = $viewer_id;
            $params['type'] = 'user';
            $this->view->review_id = $hasPosted = $reviewTable->canPostReview($params);
        } else {
            $this->view->review_id = $hasPosted = 0;
        }

        $autorizationApi = Engine_Api::_()->authorization();
        if (Engine_Api::_()->siteevent()->allowReviewCreate($siteevent) && $autorizationApi->getPermission($level_id, 'siteevent_event', "review_create") && empty($hasPosted)) {
            $this->view->createAllow = 1;
        } elseif ($autorizationApi->getPermission($level_id, 'siteevent_event', "review_update") && !empty($hasPosted)) {
            $this->view->createAllow = 2;
        } else {
            $this->view->createAllow = 0;
        }

        $this->view->update_permission = $autorizationApi->getPermission($level_id, 'siteevent_event', "review_update");
        $selectRatingTable = $ratingTable->select()
                ->from($ratingTable->info('name'), 'rating_id')
                ->where('resource_id = ?', $siteevent->event_id)
                ->where('resource_type = ?', $siteevent->getType())
                ->where('user_id = ?', $viewer_id);
        $this->view->rating_exist = $selectRatingTable->query()->fetchColumn();

        $show_rating = 0;
        if (!empty($this->view->rating_exist) && !Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.allowreview', 1))
            $show_rating = 1;

        $creationAllow = 1;
        if (empty($this->view->createAllow) && empty($show_rating))
            $creationAllow = 0;

        if (empty($creationAllow) || empty($creationAllowOwner)) {
            $this->view->createAllow = 0;
        }

        if (empty($this->view->seeAllReviews) && (empty($creationAllow) || empty($creationAllowOwner))) {
            return $this->setNoRender();
        }

        if ($this->view->event_profile_page) {
            $this->view->contentDetails = Engine_Api::_()->siteevent()->getWidgetInfo('siteevent.user-siteevent', $identity);
        }

        $this->view->tab = Engine_Api::_()->siteevent()->getTabId('siteevent.user-siteevent');
    }

}