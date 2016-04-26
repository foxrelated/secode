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
class Siteevent_Widget_ReviewOfTheDayController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $review_id = $this->_getParam('review_id');

        if (empty($review_id)) {
            return $this->setNoRender();
        }

        //GET REVIEW OF THE DAY
        $this->view->review = $review = Engine_Api::_()->getItem('siteevent_review', $review_id);

        if (empty($review) || $review->status != 1) {
            return $this->setNoRender();
        }

        //GET OVERALL RATING VALUE
        $this->view->overallRating = Engine_Api::_()->getDbTable('ratings', 'siteevent')->getOverallRating('siteevent_event', $review_id);

        $starttime = $this->_getParam('starttime');
        $endtime = $this->_getParam('endtime');
        $currenttime = date('Y-m-d H:i:s');

        if (!empty($starttime) && $currenttime < $starttime) {
            return $this->setNoRender();
        }

        if (!empty($endtime) && $currenttime > $endtime) {
            return $this->setNoRender();
        }
    }

}
