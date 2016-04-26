<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Userreviews.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Model_DbTable_Userreviews extends Engine_Db_Table {

  protected $_rowClass = "Siteevent_Model_Userreview";

  /**
   * Return average user rating for user
   *
   * @param int $user_id
   * @param int $event_id
   * @return average user rating for user
   */
  public function averageUserRatings($params = array()) {

    $averageUserRatings = $this->select()
            ->from($this->info('name'), array('AVG(rating) AS avg_rating'))
            ->where("event_id = ?", $params['event_id'])
            ->where("user_id = ?", $params['user_id'])
            ->query()
            ->fetchColumn();
    return $averageUserRatings;
  }

  /**
   * Return user rating for viewer
   *
   * @param int $viewer_id
   * @param int $event_id
   * @param int $user_id
   * @return user rating for viewer
   */
  public function myRatings($params = array()) {

    $myRatings = $this->select()
            ->from($this->info('name'), array('rating'))
            ->where("event_id = ?", $params['event_id'])
            ->where("user_id = ?", $params['user_id'])
            ->where("viewer_id = ?", $params['viewer_id'])
            ->query()
            ->fetchColumn();
    return $myRatings;
  }

  /**
   * Return total ratings of user
   *
   * @param int $event_id
   * @param int $user_id
   * @return total ratings of user
   */
  public function totalReviews($event_id, $user_id) {

    $totalReviews = $this->select()
            ->from($this->info('name'), array('COUNT(*) AS count'))
            ->where("event_id = ?", $event_id)
            ->where("user_id = ?", $user_id)
            ->query()
            ->fetchColumn();
    return $totalReviews;
  }

  public function isGuestReviewAllowed($params = array()) {

    $isGuestReviewAllowed = $column = $this->select()
            ->from($this->info('name'), array('userreview_id'))
            ->where('event_id = ?', $params['event_id'])
            ->where('user_id = ?', $params['user_id'])
            ->where('viewer_id = ?', $params['viewer_id'])
            ->query()
            ->fetchColumn();
    return $isGuestReviewAllowed;
  }
  
  
  public function getUserReviesSelect($params = array()) {

    $select = $this->select()
            ->from($this->info('name'), array('viewer_id', 'rating', 'title', 'modified_date', 'description'))
            ->where('event_id = ?', $params['event_id'])
            ->where('user_id = ?', $params['user_id'])
            ->order('userreview_id DESC');
    return $select;
  }
}