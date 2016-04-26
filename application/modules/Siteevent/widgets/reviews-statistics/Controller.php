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
class Siteevent_Widget_ReviewsStatisticsController extends Engine_Content_Widget_Abstract {

    public function indexAction() {


        $reviewTable = Engine_Api::_()->getDbtable('reviews', 'siteevent');
        $paginator = $reviewTable->getReviewsPaginator(array('type' => 'user', 'resource_type' => 'siteevent_event',));

        $this->view->totalReviews = $paginator->getTotalItemCount();
        $recommendpaginator = $reviewTable->getReviewsPaginator(array('type' => 'user', 'recommend' => 1, 'resource_type' => 'siteevent_event',));

        $this->view->totalRecommend = $recommendpaginator->getTotalItemCount();
        $ratingTable = Engine_Api::_()->getDbtable('ratings', 'siteevent');
        $ratingCount = array();

        for ($i = 5; $i > 0; $i--) {
            $ratingCount[$i] = $ratingTable->getNumbersOfUserRating(0, 'user', 0, $i, 0, 'siteevent_event', array());
        }

        $this->view->ratingCount = $ratingCount;
    }

}
