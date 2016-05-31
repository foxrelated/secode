<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Ratings.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Model_DbTable_Ratings extends Engine_Db_Table {

  protected $_rowClass = "Sitealbum_Model_Rating";

  /**
   * Get no of rating  of viewing album 
   *
   * @param array  $params
   * @return float  $total;
   */
  public function ratingCount($params = array()) {

    $ratingTableName = $this->info('name');

    return $this->select()
                    ->from($ratingTableName, array('COUNT(*) AS count'))
                    ->where($ratingTableName . '.resource_id = ?', $params['resource_id'])
                    ->where($ratingTableName . '.resource_type = ?', $params['resource_type'])
                    ->query()
                    ->fetchColumn();
  }

  /**
   * check that album has been rated or not?
   *
   * @param int  $resource_id
   * @param string  $resource_type
   * @return bool ;
   */
  public function checkRated($params = array()) {

    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $checkRated = $this->select()
            ->from($this->info('name'), array('rating_id'))
            ->where('resource_id = ?', $params['resource_id'])
            ->where('resource_type = ?', $params['resource_type'])
            ->where('user_id = ?', $user_id)
            ->query()
            ->fetchColumn();

    if ($checkRated)
      return true;
    else
      return false;
  }

  /**
   * set rating entry of album
   *
   * @param int  $resource_id
   * @param string  $resource_type
   * @param int  $rating
   * @return bool ;
   */
  public function setRating($resource_id, $resource_type, $rating) {

    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $ratingTableName = $this->info('name');
    $select = $this->select()
            ->from($ratingTableName)
            ->where($ratingTableName . '.resource_id = ?', $resource_id)
            ->where($ratingTableName . '.resource_type = ?', $resource_type)
            ->where($ratingTableName . '.user_id = ?', $user_id);
    $row = $this->fetchRow($select);
    if (empty($row)) {
      // create rating
      $this->insert(array(
          'resource_id' => $resource_id,
          'resource_type' => $resource_type,
          'user_id' => $user_id,
          'rating' => $rating
      ));
    } else {
      $this->update(array('rating' => $rating), array('resource_id=?' => $resource_id, 'resource_type=?' => $resource_type, 'user_id=?' => $user_id));
    }
  }

  /**
   * get average rating of album
   *
   * @param int  $resource_id
   * @param string  $resource_type
   * @return $rating ;
   */
  public function getRating($resource_id, $resource_type) {

    $rating_sum = $this->select()
            ->from($this->info('name'), new Zend_Db_Expr('SUM(rating)'))
            ->group('resource_id')
            ->where('resource_id = ?', $resource_id)
            ->where('resource_type = ?', $resource_type)
            ->query()
            ->fetchColumn(0);

    $total = $this->ratingCount(array('resource_id' => $resource_id, 'resource_type' => $resource_type));
    if ($total)
      $rating = $rating_sum / $this->ratingCount(array('resource_id' => $resource_id, 'resource_type' => $resource_type));
    else
      $rating = 0;
    return $rating;
  }

}