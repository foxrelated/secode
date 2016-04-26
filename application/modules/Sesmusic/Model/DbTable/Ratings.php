<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Ratings.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Model_DbTable_Ratings extends Engine_Db_Table {

  protected $_rowClass = "Sesmusic_Model_Rating";

  public function ratingCount($resource_id, $resource_type) {

    $select = $this->select()
            ->from($this->info('name'))
            ->where('resource_id = ?', $resource_id)
            ->where('resource_type = ?', $resource_type);
    $row = $this->fetchAll($select);
    $total = count($row);
    return $total;
  }

  public function checkRated($resource_id, $user_id, $resource_type) {

    $select = $this->select()
            ->where('resource_id = ?', $resource_id)
            ->where('resource_type = ?', $resource_type)
            ->where('user_id = ?', $user_id)
            ->limit(1);
    $row = $this->fetchAll($select);

    if (count($row) > 0)
      return true;

    return false;
  }

  public function setRating($resource_id, $user_id, $rating, $resource_type) {

    $select = $this->select()
            ->from($this->info('name'))
            ->where('resource_id = ?', $resource_id)
            ->where('resource_type = ?', $resource_type)
            ->where('user_id = ?', $user_id);
    $row = $this->fetchAll($select);

    if (count($row) == 0) {
      //Create rating
      Engine_Api::_()->getDbTable('ratings', 'sesmusic')->insert(array(
          'resource_id' => $resource_id,
          'resource_type' => $resource_type,
          'user_id' => $user_id,
          'rating' => $rating
      ));
    } else {
      Engine_Api::_()->getDbTable('ratings', 'sesmusic')->update(array(
          'rating' => $rating,
              ), array(
          'resource_id = ?' => $resource_id,
          'user_id = ?' => $user_id,
          'resource_type = ?' => $resource_type
      ));
    }
  }

  public function getRatings($album_id) {

    $select = $this->select()
            ->from($this->info('name'))
            ->where('album_id = ?', $album_id);
    return $this->fetchAll($select);
  }

  public function getRating($resource_id, $resource_type) {

    $rating_sum = $this->select()
            ->from($this->info('name'), new Zend_Db_Expr('SUM(rating)'))
            ->where('resource_id = ?', $resource_id)
            ->where('resource_type = ?', $resource_type)
            ->group('resource_id')
            ->group('resource_type')
            ->query()
            ->fetchColumn(0);

    $total = $this->ratingCount($resource_id, $resource_type);
    if ($total)
      $rating = $rating_sum / $this->ratingCount($resource_id, $resource_type);
    else
      $rating = 0;

    return $rating;
  }

  public function getSumRating($resource_id, $resource_type) {

    return $this->select()
                    ->from($this->info('name'), new Zend_Db_Expr('SUM(rating)'))
                    ->where('resource_id = ?', $resource_id)
                    ->where('resource_type = ?', $resource_type)
                    ->group('resource_id')
                    ->group('resource_type')
                    ->query()
                    ->fetchColumn(0);
  }

  public function getRatedItems($params = array()) {

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $select = $this->select()
            ->from($this->info('name'))
            ->where('resource_type =?', $params['resource_type'])
            ->where('user_id =?', $viewer_id);
    return Zend_Paginator::factory($select);
  }

}