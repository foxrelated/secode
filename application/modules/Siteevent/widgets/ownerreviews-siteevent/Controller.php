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
class Siteevent_Widget_OwnerreviewsSiteeventController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_review')) {
            return $this->setNoRender();
        }

        $this->view->review = $review = Engine_Api::_()->core()->getSubject('siteevent_review');

        if (empty($review->owner_id)) {
            return $this->setNoRender();
        }

        //FETCH REVIEW DATA
        $params = array();
        $this->view->statistics = $this->_getParam('statistics', array("likeCount", "replyCount", "commentCount"));
        $params['limit'] = $this->_getParam('itemCount', 3);
        $params['resource_type'] = $review->getParent()->getType();
        $params['order'] = $params['rating'] = 'rating';
        $params['review_id'] = $review->review_id;
        $params['owner_id'] = $review->owner_id;
        $this->view->reviews = $reviews = Engine_Api::_()->getDbtable('reviews', 'siteevent')->listReviews($params, array('review_id', 'type', 'owner_id', 'title', 'view_count', 'comment_count', 'like_count', 'helpful_count', 'reply_count'));

        if ($reviews->getTotalItemCount() <= 0) {
            return $this->setNoRender();
        }
    }

}