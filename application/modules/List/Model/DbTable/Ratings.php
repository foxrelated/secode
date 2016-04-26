<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Ratings.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Model_DbTable_Ratings extends Engine_Db_Table {

  protected $_rowClass = "List_Model_Rating";

  /**
   * Return rating data
   *
   * @param array params
   * @return Zend_Db_Table_Select
   */
  public function getAvgRating($listing_id) {

    //FETCH DATA
    $avg_rating = $this->select()
                    ->from($this->info('name'), array('AVG(rating) AS avg_rating'))
                    ->where('listing_id = ?', $listing_id)
                    ->group('listing_id')
                    ->query()
                    ->fetchColumn();

    //RETURN DATA
    return $avg_rating;
  }

	 /**
   * Do listing rating
   * @param int $listing_id : listing id
	 * @param int $user_id : user id
	 * @param int $rating : $rating id
   */
  public function setFaqRating($listing_id, $user_id, $rating) {

    //FETCH DATA
    $done_rating = $this->select()
                    ->from($this->info('name'), array('listing_id'))
                    ->where('listing_id = ?', $listing_id)
                    ->where('user_id = ?', $user_id)
                    ->query()
                    ->fetchColumn();

		//INSERT RATING ENTRIES IN TABLE
    if (empty($done_rating)) {
      $this->insert(array(
          'listing_id' => $listing_id,
          'user_id' => $user_id,
          'rating' => $rating
      ));
    }
  }

	/**
   * Get previous rated or not by user
   * @param int $listing_id : listing id
	 * @param int $user_id : user id
   */
  public function isRated($listing_id, $user_id) {

		//FETCH DATA
    $done_rating = $this->select()
                    ->from($this->info('name'), array('listing_id'))
                    ->where('listing_id = ?', $listing_id)
                    ->where('user_id = ?', $user_id)
                    ->query()
										->fetchColumn();

		//RETURN DATA
    if (!empty($done_rating))
      return true;
    
		return false;
  }
  
	/**
   * Get total rating
   * @param int $listing_id : listing id
	 * @return  total rating
   */
  public function countRating($listing_id) {

    //FETCH DATA
    $total_count = $this->select()
                    ->from($this->info('name'), array('COUNT(listing_id) AS total_count'))
                    ->where('listing_id = ?', $listing_id)
                    ->query()
                    ->fetchColumn();

    //RETURN DATA
    return $total_count;
  }

}