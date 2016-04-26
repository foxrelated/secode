<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Controller.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Widget_CategoryListingsListController extends Engine_Content_Widget_Abstract {

  public function indexAction() {  	

		//GET PARAMETERS FOR SORTING THE RESULTS
    $current_time = date("Y-m-d H:i:s");
		$itemCount = $this->_getParam('itemCount', 0);
		$popularity = $this->_getParam('popularity', 'view_count');
		$interval = $this->_getParam('interval', 'overall');
		$totalPages = $this->_getParam('listingCount', 5);

		//MAKE TIMING STRING
		if($interval == 'week') {
			$time_duration = date('Y-m-d H:i:s', strtotime('-7 days'));
			$sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'" ;
		}
		elseif($interval == 'month') {
			$time_duration = date('Y-m-d H:i:s', strtotime('-1 months'));
			$sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'" . "";
		}
		else {
			$sqlTimeStr = '';
		}

		//GET CATEGORIES
    $categories = array();
		if(!empty($itemCount)) {
			$category_info = Engine_Api::_()->getDbtable('categories', 'list')->getAllCategories(0, 'category_id', 1, 'listing_id', 1, $itemCount, null, null);
		}
		else {
			$category_info = Engine_Api::_()->getDbtable('categories', 'list')->getAllCategories(0, 'category_id', 1, 'listing_id', 1, null, null);
		}

    foreach ($category_info as $value) {
      $category_listings_array = array();

			//GET PAGE RESULTS
			$category_listings_info = $category_listings_info = Engine_Api::_()->getDbtable('listings', 'list')->listingsByCategory($value['category_id'], $popularity, $interval, $sqlTimeStr, $totalPages);

      foreach ($category_listings_info as $result_info) {
        $tmp_array = array('listing_id' => $result_info->listing_id,
						'imageSrc' => $result_info->getPhotoUrl('thumb.icon'),
            'listing_title' => $result_info->title,
            'owner_id' => $result_info->owner_id,
						'populirityCount' => $result_info->populirityCount,
            'slug' => $result_info->getSlug());
        $category_listings_array[] = $tmp_array;
      }
      $category_array = array('category_id' => $value->category_id,
          'category_name' => $value->category_name,
          'order' => $value->cat_order,
          'count' => $value->count,
          'category_listings' => $category_listings_array
      );
      $categories[] = $category_array;
    }
    $this->view->categories = $categories;

    //SET NO RENDER
    if (!(count($this->view->categories) > 0)) {
      return $this->setNoRender();
    }
  }
}