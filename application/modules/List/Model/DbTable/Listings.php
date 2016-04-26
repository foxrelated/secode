<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    List
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Listings.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class List_Model_DbTable_Listings extends Engine_Db_Table {

  protected $_rowClass = "List_Model_Listing";

  public function expirySQL($select) {
    if (empty($select))
      return;
    $listApi = Engine_Api::_()->list();
    $expirySettings = $listApi->expirySettings();

    if ($expirySettings == 2) {
      $approveDate = $listApi->adminExpiryDuration();
      $select->where($this->info('name') . ".`approved_date` >= ?", $approveDate);
    } elseif ($expirySettings == 1) {
      $current_date = date("Y-m-d i:s:m", time());
      $select->where("(" . $this->info('name') . ".`end_date` IS NULL or " . $this->info('name') . ".`end_date` >= ?)", $current_date);
    }
    return $select;
  }

  public function getListsPaginator($params = array(), $customParams = null) {

    $paginator = Zend_Paginator::factory($this->getListsSelect($params, $customParams));
    if (!empty($params['page'])) {
      $paginator->setCurrentPageNumber($params['page']);
    }

    if (!empty($params['limit'])) {
      $paginator->setItemCountPerPage($params['limit']);
    }

    return $paginator;
  }

  // GET LIST SELECT QUERY
  public function getListsSelect($params = array(), $customParams = null) {

    //GET LISTING TABLE NAME
    $listTableName = $this->info('name');

    //GET TAGMAP TABLE NAME
    $tagMapTableName = Engine_Api::_()->getDbtable('TagMaps', 'core')->info('name');

    //GET SEARCH TABLE
    $searchTable = Engine_Api::_()->fields()->getTable('list_listing', 'search')->info('name');

    //GET LOCATION TABLE
    $locationTable = Engine_Api::_()->getDbtable('locations', 'list');
    $locationTableName = $locationTable->info('name');

    //GET API
    $settings = Engine_Api::_()->getApi('settings', 'core');

    //MAKE QUERY
    $select = $this->select();
    if (!empty($params['type']) && empty($params['orderby'])) {
      if ($params['type'] == 'browse') {
        $order = $settings->getSetting('list.browseorder', 1);
        switch ($order) {
          case "1":
            $select->order($listTableName . '.creation_date DESC');
            break;
          case "2":
            $select->order($listTableName . '.view_count DESC');
            break;
          case "3":
            $select->order($listTableName . '.title');
            break;
          case "4":
            $select->order($listTableName . '.sponsored' . ' DESC');
            break;
          case "5":
            $select->order($listTableName . '.featured' . ' DESC');
            break;
          case "6":
            $select->order($listTableName . '.sponsored' . ' DESC');
            $select->order($listTableName . '.featured' . ' DESC');
            break;
          case "7":
            $select->order($listTableName . '.featured' . ' DESC');
            $select->order($listTableName . '.sponsored' . ' DESC');
            break;
        }
      }
    }

    if (!empty($params['orderby']) && $params['orderby'] == "title") {
      $select->order($listTableName . '.' . $params['orderby']);
    } else if (!empty($params['orderby'])) {
      $select->order($listTableName . '.' . $params['orderby'] . ' DESC');
    }
 
    $select->order($listTableName . '.creation_date DESC');
    $select = $select
            ->setIntegrityCheck(false)
            ->from($listTableName)
            ->joinLeft($locationTableName, "$listTableName.listing_id = $locationTableName.listing_id   ", array($locationTableName . '.location'))
            ->group($listTableName . '.listing_id');

    if (!empty($params['type'])) {
      if ($params['type'] == 'browse' || $params['type'] == 'home') {
        $select = $select
                ->where($listTableName . '.approved = ?', '1')
                ->where($listTableName . '.draft = ?', '1');
        $select = $this->expirySQL($select);
        $stusShow = $settings->getSetting('list.status.show', 1);
        if ($stusShow == 0) {
          $select = $select
                  ->where($listTableName . '.closed = ?', '0');
        }
      } elseif ($params['type'] == 'browse_home_zero') {
        $select = $select
                ->where($listTableName . '.closed = ?', '0')
                ->where($listTableName . '.approved = ?', '1')
                ->where($listTableName . '.search = ?', '1')
                ->where($listTableName . '.draft = ?', '1');
        $select = $this->expirySQL($select);
      }
      if ($params['type'] != 'manage') {
        $select->where($listTableName . ".search = ?", 1);
      }
    }

    if (isset($customParams)) {

      $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
      $coreversion = $coremodule->version;
      if ($coreversion > '4.1.7') {

        //PROCESS OPTIONS
        $tmp = array();
        foreach ($customParams as $k => $v) {
          if (null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0)) {
            continue;
          } else if (false !== strpos($k, '_field_')) {
            list($null, $field) = explode('_field_', $k);
            $tmp['field_' . $field] = $v;
          } else if (false !== strpos($k, '_alias_')) {
            list($null, $alias) = explode('_alias_', $k);
            $tmp[$alias] = $v;
          } else {
            $tmp[$k] = $v;
          }
        }
        $customParams = $tmp;
      }

      $select = $select
              ->setIntegrityCheck(false)
              ->joinLeft($searchTable, "$searchTable.item_id = $listTableName.listing_id", null);

      $searchParts = Engine_Api::_()->fields()->getSearchQuery('list_listing', $customParams);
      foreach ($searchParts as $k => $v) {
        $select->where("`{$searchTable}`.{$k}", $v);
      }
    }

    if (isset($params['list_street']) && !empty($params['list_street'])) {
      $select->join($locationTableName, "$listTableName.listing_id = $locationTableName.listing_id   ", null);
				$select->where($locationTableName.'.address   LIKE ? ', '%' . $params['list_street'] . '%');
    } if (isset($params['list_city']) && !empty($params['list_city'])) {
      $select->join($locationTableName, "$listTableName.listing_id = $locationTableName.listing_id   ", null);
      $select->where($locationTableName . '.city = ?', $params['list_city']);
    } if (isset($params['list_state']) && !empty($params['list_state'])) {
      $select->join($locationTableName, "$listTableName.listing_id = $locationTableName.listing_id   ", null);
      $select->where($locationTableName . '.state = ?', $params['list_state']);
    } if (isset($params['list_country']) && !empty($params['list_country'])) {
      $select->join($locationTableName, "$listTableName.listing_id = $locationTableName.listing_id   ", null);
      $select->where($locationTableName . '.country = ?', $params['list_country']);
    }





//     if (isset($params['city']) && !empty($params['city'])) {
//       $valueTable = Engine_Api::_()->fields()->getTable('list_listing', 'values');
//       $valueName = $valueTable->info('name');
//       $metaName = Engine_Api::_()->fields()->getTable('list_listing', 'meta')->info('name');
// 
//       $select->join($valueName, "$listTableName.listing_id = $valueName.item_id", array())
//           ->join($metaName, $metaName . '.field_id = ' . $valueName . '.field_id', array())
//           ->where($locationTableName . ".city = ?", $params['city']);
//     }

    if ((isset($params['list_location']) && !empty($params['list_location'])) || (!empty($params['Latitude']) && !empty($params['Longitude']))) {
      $enable = $settings->getSetting('list.proximitysearch', 1);
      if (isset($params['locationmiles']) && (!empty($params['locationmiles']) && !empty($enable))) {
        $longitude = 0;
        $latitude = 0;
        $selectLocQuery = $locationTable->select()->where('location = ?', $params['list_location']);
        $locationValue = $locationTable->fetchRow($selectLocQuery);
        $enableSocialengineaddon = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('seaocore');

        //check for zip code in location search.
        if (empty($params['Latitude']) && empty($params['Longitude'])) {
          if (empty($locationValue)) {
            $locationResults = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getLatLong(array('location' => $params['list_location'], 'module' => 'Listing / Catalog Showcase'));
            if(!empty($locationResults['latitude']) && !empty($locationResults['longitude'])) {
                $latitude = $locationResults['latitude'];
                $longitude = $locationResults['longitude'];
            }
          } else {
            $latitude = (float) $locationValue->latitude;
            $longitude = (float) $locationValue->longitude;
          }
        } else {
          $latitude = (float) $params['Latitude'];
          $longitude = (float) $params['Longitude'];
        }

        $radius = $params['locationmiles'];

        $flage = $settings->getSetting('list.proximity.search.kilometer', 0);
        if (!empty($flage)) {
          $radius = $radius * (0.621371192);
        }
        //$latitudeRadians = deg2rad($latitude);
        $latitudeSin = "sin(radians($latitude))";
        $latitudeCos = "cos(radians($latitude))";
        $select->join($locationTableName, "$listTableName.listing_id = $locationTableName.listing_id   ", null);
        $sqlstring = "((degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
        $sqlstring .= ") OR (" . $locationTableName . ".latitude = '" . $latitude . "' AND  " . $locationTableName . ".longitude= '" . $longitude . "'))";
        $select->where($sqlstring);
      } else {
        $select->join($locationTableName, "$listTableName.listing_id = $locationTableName.listing_id", null);
        $select->where("`{$locationTableName}`.formatted_address LIKE ? or `{$locationTableName}`.location LIKE ? or `{$locationTableName}`.city LIKE ? or `{$locationTableName}`.state LIKE ?", "%" . $params['list_location'] . "%");
      }
    }

    //START NETWORK WORK
    if (!empty($params['type'])) {
      if ($params['type'] == 'browse' || $params['type'] == 'home') {

        $enableNetwork = $settings->getSetting('list.network', 0);
        if (!empty($enableNetwork) || (isset($params['show']) && $params['show'] == "3")) {
          $viewer = Engine_Api::_()->user()->getViewer();
          $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
          $viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer->getIdentity()));
          if (!empty($viewerNetwork)) {
            $networkMembershipName = $networkMembershipTable->info('name');
            $select
                    ->join($networkMembershipName, "`{$listTableName}`.owner_id = `{$networkMembershipName}`.user_id  ", null)
                    ->join($networkMembershipName, "`{$networkMembershipName}`.`resource_id`=`{$networkMembershipName}_2`.resource_id", null)
                    ->where("`{$networkMembershipName}_2`.user_id = ? ", $viewer->getIdentity())
            ;
          }
        }
      }
    }
    //END NETWORK WORK

    if (!empty($params['user_id']) && is_numeric($params['user_id'])) {
      $select->where($listTableName . '.owner_id = ?', $params['user_id']);
    }

    if (!empty($params['user']) && $params['user'] instanceof User_Model_User) {
      $select->where($listTableName . '.owner_id = ?', $params['user_id']->getIdentity());
    }

    if (!empty($params['users'])) {
      $str = (string) ( is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users'] );
      $select->where($listTableName . '.owner_id in (?)', new Zend_Db_Expr($str));
    }

    if (empty($params['users']) && isset($params['show']) && $params['show'] == '2') {
      $select->where($listTableName . '.owner_id = ?', '0');
    }

    if ((isset($params['show']) && $params['show'] == "4")) {
      $likeTableName = Engine_Api::_()->getDbtable('likes', 'core')->info('name');
      $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
      $select->setIntegrityCheck(false)
              ->join($likeTableName, "$likeTableName.resource_id = $listTableName.listing_id")
              ->where($likeTableName . '.poster_type = ?', 'user')
              ->where($likeTableName . '.poster_id = ?', $viewer_id)
              ->where($likeTableName . '.resource_type = ?', 'list_listing');
    }

    if (!empty($params['tag_id'])) {
      $select
              ->setIntegrityCheck(false)
              ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $listTableName.listing_id")
              ->where($tagMapTableName . '.resource_type = ?', 'list_listing')
              ->where($tagMapTableName . '.tag_id = ?', $params['tag_id']);
    }

    if (!empty($params['category'])) {
      $select->where($listTableName . '.category_id = ?', $params['category']);
    }

    if (!empty($params['subcategory'])) {
      $select->where($listTableName . '.subcategory_id = ?', $params['subcategory']);
    }

    if (!empty($params['subsubcategory'])) {
      $select->where($listTableName . '.subsubcategory_id = ?', $params['subsubcategory']);
    }

    if (isset($params['closed']) && $params['closed'] != "") {
      $select->where($listTableName . '.closed = ?', $params['closed']);
    }

    // Could we use the search indexer for this?
    if (!empty($params['search'])) {

      $tagName = Engine_Api::_()->getDbtable('Tags', 'core')->info('name');
      $select
              ->setIntegrityCheck(false)
              ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $listTableName.listing_id and " . $tagMapTableName . ".resource_type = 'list_listing'")
              ->joinLeft($tagName, "$tagName.tag_id = $tagMapTableName.tag_id");

      $select->where($listTableName . ".title LIKE ? OR " . $listTableName . ".body LIKE ? OR " . $tagName . ".text LIKE ? ", '%' . $params['search'] . '%');
    }

    if (!empty($params['start_date'])) {
      $select->where($listTableName . ".creation_date > ?", date('Y-m-d', $params['start_date']));
    }

    if (!empty($params['end_date'])) {
      $select->where($listTableName . ".creation_date < ?", date('Y-m-d', $params['end_date']));
    }

    if (!empty($params['has_photo'])) {
      $select->where($listTableName . ".photo_id > ?", 0);
    }

    if (!empty($params['has_review'])) {
      $select->where($listTableName . ".review_count > ?", 0);
    }
   //echo $select;die;
    // 
    return $select;
  }

  public function getTagCloud($limit = 100, $count_only = 1) {

    //GET TAG, TAGMAP AND LISTING TABLES
    $tableTagmaps = 'engine4_core_tagmaps';
    $tableTags = 'engine4_core_tags';
    $tableListName = $this->info('name');

    //MAKE QUERY
    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($tableListName, 'title')
            ->joinInner($tableTagmaps, "$tableListName.listing_id = $tableTagmaps.resource_id", array('COUNT(engine4_core_tagmaps.resource_id) AS Frequency'))
            ->joinInner($tableTags, "$tableTags.tag_id = $tableTagmaps.tag_id", array('text', 'tag_id'))
            ->where($tableListName . '.approved = ?', "1")
            ->where($tableListName . '.draft = ?', "1")
            ->where($tableListName . ".search = ?", 1)
            ->where($tableTagmaps . '.resource_type = ?', 'list_listing')
            ->group("$tableTags.text")
            ->order("Frequency DESC");
    $select = $this->expirySQL($select);

    $stusShow = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.status.show', 1);
    if ($stusShow == 0) {
      $select->where($tableListName . '.closed = ?', '0');
    }

    // Start Network work
    $enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.network', 0);
    if (!empty($enableNetwork)) {
      $viewer = Engine_Api::_()->user()->getViewer();
      $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
      $viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer->getIdentity()));

      if (!empty($viewerNetwork)) {
        $networkMembershipName = $networkMembershipTable->info('name');
        $select
                ->join($networkMembershipName, "`{$tableListName}`.owner_id = `{$networkMembershipName}`.user_id  ", null)
                ->join($networkMembershipName, "`{$networkMembershipName}`.`resource_id`=`{$networkMembershipName}_2`.resource_id", null)
                ->where("`{$networkMembershipName}_2`.user_id = ? ", $viewer->getIdentity());
      }
    }
    // End Network work

    if (!empty($count_only)) {
      $total_results = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
      return Count($total_results);
    }

    $select = $select->limit($limit);

    return $select->query()->fetchAll();
  }

  /**
   * Get listing count based on category
   *
   * @param int $id
   * @param string $column_name
   * @param int $authorization
   * @return listing count
   */
  public function getListingsCount($id, $column_name, $authorization) {

    //RETURN IF ID IS EMPTY
    if (empty($id)) {
      return 0;
    }

    //MAKE QUERY
    $select = $this->select()
            ->from($this->info('name'), array('COUNT(*) AS count'))
            ->where("$column_name = ?", $id)
            ->where('closed = ?', 0)
            ->where('approved = ?', 1)
            ->where('draft = ?', 1)
            ->where('search = ?', 1);
    $select = $this->expirySQL($select);
    $totalListings = $select->query()->fetchColumn();

    //RETURN LISTINGS COUNT
    return $totalListings;
  }

  /**
   * Get listings based on category
   * @param string $title : search text
   * @param int $category_id : category id
   * @param char $popularity : result sorting based on views, reviews, likes, comments
   * @param char $interval : time interval
   * @param string $sqlTimeStr : Time durating string for where clause 
   */
  public function listingsByCategory($category_id, $popularity, $interval, $sqlTimeStr, $totalPages) {
    $groupBy = 1;
    $listingTableName = $this->info('name');

    if ($interval == 'overall' || $popularity == 'view_count') {
      $groupBy = 0;
      $select = $this->select()
              ->from($listingTableName, array('listing_id', 'title', 'photo_id', 'owner_id', "$popularity AS populirityCount"))
              ->where($listingTableName . '.category_id = ?', $category_id)
              ->where($listingTableName . '.approved = ?', '1')
              ->where($listingTableName . '.draft = ?', '1')
              ->where($listingTableName . '.search = ?', '1')
              ->order("$popularity DESC")
              ->order("creation_date DESC")
              ->limit($totalPages);

			$stusShow = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.status.show', 1);
			if ($stusShow == 0) {
				$select->where($listingTableName . '.closed = ?', '0');
			}
              
    } elseif ($popularity == 'review_count' && $interval != 'overall') {

      $popularityTable = Engine_Api::_()->getDbtable('reviews', 'list');
      $popularityTableName = $popularityTable->info('name');

      $select = $this->select()
              ->setIntegrityCheck(false)
              ->from($listingTableName, array('listing_id', 'title', 'photo_id', 'owner_id', "$popularity AS populirityCount"))
              ->joinLeft($popularityTableName, $popularityTableName . '.listing_id = ' . $listingTableName . '.listing_id', array("COUNT(review_id) as total_count"))
              ->where($listingTableName . '.category_id = ?', $category_id)
              ->where($listingTableName . '.approved = ?', '1')
              ->where($listingTableName . '.draft = ?', '1')
              ->where($listingTableName . '.search = ?', '1')
              ->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.creation_date is null')
              ->group($listingTableName . '.listing_id')
              ->order("total_count DESC")
              ->order($listingTableName . ".creation_date DESC")
              ->limit($totalPages);

			$stusShow = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.status.show', 1);
			if ($stusShow == 0) {
				$select->where($listingTableName . '.closed = ?', '0');
			}
              
    } elseif ($popularity != 'view_count' && $popularity != 'review_count' && $interval != 'overall') {

      if ($popularity == 'like_count') {
        $popularityType = 'like';
      } else {
        $popularityType = 'comment';
      }

      $id = $popularityType . "_id";

      $popularityTable = Engine_Api::_()->getDbtable("$popularityType" . "s", 'core');
      $popularityTableName = $popularityTable->info('name');

      $select = $this->select()
              ->setIntegrityCheck(false)
              ->from($listingTableName, array('listing_id', 'title', 'photo_id', 'owner_id', "$popularity AS populirityCount"))
              ->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $listingTableName . '.listing_id', array("COUNT($id) as total_count"))
              ->where($listingTableName . '.category_id = ?', $category_id)
              ->where($listingTableName . '.approved = ?', '1')
              ->where($listingTableName . '.draft = ?', '1')
              ->where($listingTableName . '.search = ?', '1')
              ->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.creation_date is null')
              ->group($listingTableName . '.listing_id')
              ->order("total_count DESC")
              ->order($listingTableName . ".creation_date DESC")
              ->limit($totalPages);

			$stusShow = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.status.show', 1);
			if ($stusShow == 0) {
				$select->where($listingTableName . '.closed = ?', '0');
			}
              
    }
    $select = $this->expirySQL($select);
    //Start Network work
    $select = $this->getNetworkBaseSql($select, array('not_groupBy' => $groupBy));
    //End Network work

    return $this->fetchAll($select);
  }

  public function getItemOfDay() {

    //TODAY DATE
    $today = date('Y-m-d');

    //GET LIST TABLE NAME
    $listTableName = $this->info('name');

    //GET ITEM OF THE DAY TABLE NAME
    $itemTableName = Engine_Api::_()->getDbtable('itemofthedays', 'list')->info('name');

    //MAKE QUERY
    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($listTableName, array('listing_id', 'title', 'owner_id', 'photo_id'))
            ->join($itemTableName, $listTableName . '.listing_id = ' . $itemTableName . '.listing_id', array('date'))
            ->where($itemTableName . '.date <= ?', $today)
            ->where($itemTableName . '.endtime >= ?', $today)
            ->order('RAND()');
    $select = $this->expirySQL($select);

    //RETURN RESULTS
    return $this->fetchRow($select);
  }

  /**
   * Get pages to add as item of the day
   * @param string $title : search text
   * @param int $limit : result limit
   */
  public function getDayItems($title, $limit = 10) {

    //MAKE QUERY
    $select = $this->select()
            ->from($this->info('name'), array('listing_id', 'owner_id', 'title', 'photo_id'))
            ->where('title  LIKE ? ', '%' . $title . '%')
            ->where('closed = ?', '0')
            ->where('approved = ?', '1')
            ->where('draft = ?', '1')
            ->where('search = ?', '1')
            ->order('title ASC')
            ->limit($limit);
   $select = $this->expirySQL($select);

    //RETURN RESULTS
    return $this->fetchAll($select);
  }

  /**
   * Return sitefaq data
   *
   * @param array params
   * @return Zend_Db_Table_Select
   */
  public function widgetListingsData($params = array()) {

    //GET TABLE NAME
    $tableListingName = $this->info('name');

    //MAKE QUERY
    $select = $this->select()->from($tableListingName, array("listing_id", "title", "category_id", "subcategory_id", "subsubcategory_id", "view_count", "comment_count", "like_count", "rating", "view_count", "owner_id", "photo_id"));

    //SELECT ONLY AUTHENTICATE LISTINGS
    $select = $select->where('approved = ?', 1)->where('draft = ?', 1)->where('closed = ?', 0)->where('search = ?', 1);

    $select = $this->expirySQL($select);

    if (isset($params['zero_count']) && !empty($params['zero_count'])) {
      $select = $select->where($params['zero_count'] . ' != ?', 0);
    }

    if (isset($params['owner_id']) && !empty($params['owner_id'])) {
      $select = $select->where('owner_id = ?', $params['owner_id']);
    }

    if (isset($params['listing_id']) && !empty($params['listing_id'])) {
      $select = $select->where('listing_id != ?', $params['listing_id']);
    }

    if (isset($params['featured']) && !empty($params['featured'])) {
      $select = $select->where('featured = ?', 1);
    }

    if ((isset($params['category_id']) && !empty($params['category_id']))) {
      $select->where('category_id = ?', $params['category_id']);
    }

    if (isset($params['tags']) && !empty($params['tags'])) {

      //GET TAG MAPS TABLE NAME
      $tableTagmapsName = Engine_Api::_()->getDbtable('TagMaps', 'core')->info('name');

      $select->setIntegrityCheck(false)
              ->joinLeft($tableTagmapsName, "$tableTagmapsName.resource_id = $tableListingName.listing_id")
              ->where($tableTagmapsName . '.resource_type = ?', 'list_listing');

      foreach ($params['tags'] as $tag_id) {
        $tagSqlArray[] = "$tableTagmapsName.tag_id = $tag_id";
      }
      $select->where("(" . join(") or (", $tagSqlArray) . ")");
    }

    if (isset($params['orderby']) && !empty($params['orderby'])) {
      $select = $select->order($params['orderby']);
    }

    $select = $select->order('listing_id DESC');

    if (isset($params['limit']) && !empty($params['limit'])) {
      $select = $select->limit($params['limit']);
    }

    $select = $select->group('listing_id');

    return $this->fetchAll($select);
  }

  public function getRecentViewedListings() {

    //GET LISTING TABLE NAME
    $listTableName = $this->info('name');

    //GET LISTING VIEW TABLE
    $viewTable = Engine_Api::_()->getDbtable('vieweds', 'list');
    $viewName = $viewTable->info('name');

    //MAKE QUERY
    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($listTableName, array('listing_id', 'title', 'owner_id', 'photo_id', 'view_count', 'like_count'))
            ->join($viewName, $listTableName . '.listing_id = ' . $viewName . '.listing_id', array('max(date) as date'))
            ->where($listTableName . '.closed = ?', '0')
            ->where($listTableName . '.approved = ?', '1')
            ->where($listTableName . ".search = ?", 1)
            ->where($listTableName . '.draft = ?', '1')
            ->group($viewName . '.listing_id')
            ->order('date DESC')
            ->limit((int) Engine_Api::_()->getApi('settings', 'core')->getSetting('list.recently.view', 3));
    $select = $this->expirySQL($select);

    $enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.network', 0);
    if (!empty($enableNetwork)) {
      $viewer = Engine_Api::_()->user()->getViewer();
      $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
      $viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer->getIdentity()));

      if (!empty($viewerNetwork)) {

        $networkMembershipName = $networkMembershipTable->info('name');
        $select
                ->join($networkMembershipName, "`{$listTableName}`.owner_id = `{$networkMembershipName}`.user_id  ", null)
                ->join($networkMembershipName, "`{$networkMembershipName}`.`resource_id`=`{$networkMembershipName}_2`.resource_id", null)
                ->where("`{$networkMembershipName}_2`.user_id = ? ", $viewer->getIdentity());
      }
    }

    //RETURN RESULTS
    return $this->fetchAll($select);
  }

  /**
   * Return listings which have this category and this mapping
   *
   * @param int category_id
   * @return Zend_Db_Table_Select
   */
  public function getCategoryList($category_id) {

    //RETURN IF CATEGORY ID IS NULL
    if (empty($category_id)) {
      return null;
    }

    //MAKE QUERY
    $select = $this->select()
            ->from($this->info('name'), 'listing_id')
            ->where('category_id = ?', $category_id);

    //GET DATA
    $categoryData = $this->fetchAll($select);

    if (!empty($categoryData)) {
      return $categoryData->toArray();
    }

    return null;
  }

  /**
   * Get Popular location base on city and state
   *
   */
  public function getPopularLocation($limit = 5) {

    //GET LIST TABLE NAME
    $listTableName = $this->info('name');

    //GET LOCATION TABLE
    $locationTable = Engine_Api::_()->getDbtable('locations', 'list');
    $locationTableName = $locationTable->info('name');

    //MAKE QUERY
    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($listTableName, null)
            ->join($locationTableName, "$listTableName.listing_id = $locationTableName.listing_id", array("city", "count(city) as count_location", "state", "count(state) as count_location_state"))
            ->where($listTableName . '.approved = ?', '1')
            ->where($listTableName . '.draft = ?', '1')
            ->where($listTableName . ".search = ?", 1)
            ->group("city")
            //->limit($limit)
            ->group("state")
            ->order("count_location DESC");
    $select = $this->expirySQL($select);    

    //IF STATUS IS ALLOWED
    $stusShow = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.status.show', 1);
    if ($stusShow == 0) {
      $select->where($listTableName . '.closed = ?', '0');
    }

    $select = $this->getNetworkBaseSql($select, array('not_groupBy' => 1));

    $select->limit($limit);

    //RETURN RESULTS
    return $this->fetchAll($select);
  }

  public function getNetworkBaseSql($select, $params=array()) {

    if (empty($select))
      return;

    //GET LIST TABLE NAME
    $listTableName = $this->info('name');

    //START NETWORK WORK
    $enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.network', 0);
    if (!empty($enableNetwork) || (isset($params['browse_network']) && !empty($params['browse_network']))) {
      $viewer = Engine_Api::_()->user()->getViewer();
      $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
      if (!Engine_Api::_()->list()->pageBaseNetworkEnable()) {
        $viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer->getIdentity()));

        if (!empty($viewerNetwork)) {
          if (isset($params['setIntegrity']) && !empty($params['setIntegrity'])) {
            $select->setIntegrityCheck(false)
                    ->from($listTableName);
          }
          $networkMembershipName = $networkMembershipTable->info('name');
          $select
                  ->join($networkMembershipName, "`{$listTableName}`.owner_id = `{$networkMembershipName}`.user_id  ", null)
                  ->join($networkMembershipName, "`{$networkMembershipName}`.`resource_id`=`{$networkMembershipName}_2`.resource_id", null)
                  ->where("`{$networkMembershipName}_2`.user_id = ? ", $viewer->getIdentity());
          if (!isset($params['not_groupBy']) || empty($params['not_groupBy'])) {
            $select->group($listTableName . ".listing_id");
          }
          if (isset($params['extension_group']) && !empty($params['extension_group'])) {
            $select->group($params['extension_group']);
          }
        }
      } else {
        $viewerNetwork = $networkMembershipTable->getMembershipsOfInfo($viewer);
        $str = array();
        $columnName = "`{$listTableName}`.networks_privacy";
        foreach ($viewerNetwork as $networkvalue) {
          $network_id = $networkvalue->resource_id;
          $str[] = "'" . $network_id . "'";
          $str[] = "'" . $network_id . ",%'";
          $str[] = "'%," . $network_id . ",%'";
          $str[] = "'%," . $network_id . "'";
        }
        if (!empty($str)) {
          $likeNetworkVale = (string) ( join(" or $columnName  LIKE ", $str) );
          $select->where($columnName . ' LIKE ' . $likeNetworkVale . ' or ' . $columnName . " IS NULL");
        } else {
          $select->where($columnName . " IS NULL");
        }
      }
    }
    //END NETWORK WORK
    //RETURN QUERY
    return $select;
  }

  public function recent_friendlist() {

    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //GET VIEWER TABLE
    $viewTable = Engine_Api::_()->getDbtable('vieweds', 'list');
    $viewTableName = $viewTable->info('name');

    //GET MEMBERSHIP TABLE
    $membership_table = Engine_Api::_()->getDbtable('membership', 'user');
    $member_name = $membership_table->info('name');

    //GET LIST TABLE
    $listTable = Engine_Api::_()->getDbtable('listings', 'list');
    $listTableName = $listTable->info('name');

    //MAKE QUERY
    $select = $listTable->select()
            ->setIntegrityCheck(false)
            ->from($listTableName, array('listing_id', 'title', 'owner_id', 'photo_id', 'view_count', 'like_count'))
            ->joinInner($viewTableName, "$listTableName . listing_id = $viewTableName . listing_id", array('max(date) as date'))
            ->joinInner($member_name, "$member_name . user_id = $viewTableName . viewer_id", NULL)
            ->where($member_name . '.resource_id = ?', $viewer_id)
            ->where($viewTableName . '.viewer_id <> ?', $viewer_id)
            ->where($member_name . '.active = ?', 1)
            ->group($viewTableName . '.listing_id')
            ->order('date DESC')
            ->limit(Engine_Api::_()->getApi('settings', 'core')->getSetting('list.recentlyfriend_view', 3));
    $select = $this->expirySQL($select);

    //RETURN RESULTS
    return $listTable->fetchAll($select);
  }

  // get lising according to requerment
  public function getListing($listtype, $params =array()) {

    $limit = 10;
    $table = Engine_Api::_()->getDbtable('listings', 'list');
    $listTableName = $table->info('name');
    $coreTable = Engine_Api::_()->getDbtable('likes', 'core');
    $coreName = $coreTable->info('name');
    $select = $table->select()
            ->where($listTableName . '.closed = ?', '0')
            ->where($listTableName . '.approved = ?', '1')
            ->where($listTableName . '.draft = ?', '1')
            ->where($listTableName . ".search = ?", 1);
    $select = $this->expirySQL($select);
    $settings = Engine_Api::_()->getApi('settings', 'core');

    //START NETWORK WORK
    $enableNetwork = $settings->getSetting('list.network', 0);
    if (!empty($enableNetwork)) {
      $viewer = Engine_Api::_()->user()->getViewer();
      $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
      $viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer->getIdentity()));

      if (!empty($viewerNetwork)) {
        $select->setIntegrityCheck(false)
                ->from($listTableName);
        $networkMembershipName = $networkMembershipTable->info('name');
        $select
                ->join($networkMembershipName, "`{$listTableName}`.owner_id = `{$networkMembershipName}`.user_id  ", null)
                ->join($networkMembershipName, "`{$networkMembershipName}`.`resource_id`=`{$networkMembershipName}_2`.resource_id", null)
                ->where("`{$networkMembershipName}_2`.user_id = ? ", $viewer->getIdentity())
                ->group($listTableName . ".listing_id");
      }
    }
    //END NETWORK WORK

    if ($listtype == 'Most Viewed') {
      $select = $select->where($listTableName . '.view_count <> ?', '0')->order($listTableName . '.view_count DESC');
      $ShowViewArray = $settings->getSetting('list.ajax.widgets.layout', array("0" => "1", "1" => "2", "2" => "3"));
      $limit = 0;
      $temp_limit = 0;
      if (in_array("2", $ShowViewArray))
        $limit = (int) $settings->getSetting('list.popular.thums', 15);
      if (in_array("1", $ShowViewArray))
        $temp_limit = (int) $settings->getSetting('list.popular.widgets', 10);
      if ($limit < $temp_limit) {
        $limit = $temp_limit;
      }
    }
    if ($listtype == 'Most Viewed List') {
      $select = $select->where($listTableName . '.view_count <> ?', '0')->order($listTableName . '.view_count DESC');
      $limit = (int) $settings->getSetting('list.popular.widgets', 10);
    }
    if ($listtype == 'Recently Posted List') {
      $select = $select->order($listTableName . '.creation_date DESC');
      $limit = (int) $settings->getSetting('list.recent.widgets', 10);
    }

    if ($listtype == 'Random List') {
      $select->order('RAND() DESC ');
      $limit = (int) $settings->getSetting('list.random.widgets', 10);
    }
    if ($listtype == 'Most Commented') {
      $select = $select->where($listTableName . '.comment_count <> ?', '0')->order($listTableName . '.comment_count DESC');
      $limit = (int) $settings->getSetting('list.comment.widgets', 3);
    }

    if ($listtype == 'Most Rated') {
      $select = $select->where($listTableName . '.rating <> ?', '0')->order($listTableName . '.rating DESC');
      $limit = (int) $settings->getSetting('list.rate.widgets', 3);
    }

    if ($listtype == 'Recently Posted') {
      $select = $select->order($listTableName . '.creation_date DESC');
      $ShowViewArray = $settings->getSetting('list.ajax.widgets.layout', array("0" => "1", "1" => "2", "2" => "3"));
      $limit = 0;
      $temp_limit = 0;
      if (in_array("2", $ShowViewArray))
        $limit = (int) $settings->getSetting('list.recent.thumbs', 15);
      if (in_array("1", $ShowViewArray))
        $temp_limit = (int) $settings->getSetting('list.recent.widgets', 10);

      if ($limit < $temp_limit) {
        $limit = $temp_limit;
      }
    }

    if ($listtype == 'Featured') {
      $select = $select->where($listTableName . '.featured = ?', '1');
      $ShowViewArray = $settings->getSetting('list.ajax.widgets.layout', array("0" => "1", "1" => "2", "2" => "3"));
      $limit = 0;
      $temp_limit = 0;
      if (in_array("2", $ShowViewArray))
        $limit = (int) $settings->getSetting('list.featured.thumbs', 15);
      if (in_array("1", $ShowViewArray))
        $temp_limit = (int) $settings->getSetting('list.featured.list', 10);

      if ($limit < $temp_limit) {
        $limit = $temp_limit;
      }
    }
    if ($listtype == 'Sponosred') {
      $select = $select->where($listTableName . '.sponsored = ?', '1');
      $ShowViewArray = $settings->getSetting('list.ajax.widgets.layout', array("0" => "1", "1" => "2", "2" => "3"));
      $limit = 0;
      $temp_limit = 0;
      if (in_array("2", $ShowViewArray))
        $limit = (int) $settings->getSetting('list.sponsored.thumbs', 15);
      if (in_array("1", $ShowViewArray))
        $temp_limit = (int) $settings->getSetting('list.sponsored.list', 10);

      if ($limit < $temp_limit) {
        $limit = $temp_limit;
      }
    }

    if ($listtype == 'Sponsored List') {
      $select = $select->where($listTableName . '.sponsored = ?', '1');
      $limit = (int) $settings->getSetting('list.sponserdlist.widgets', 4);
    }

    if ($listtype == 'Total Sponsored List') {
      $select = $select->where($listTableName . '.sponsored = ?', '1');
    }

    if ($listtype == 'Sponsored List AJAX') {
      $select = $select->where($listTableName . '.sponsored = ?', '1');

      $limit = (int) $settings->getSetting('list.sponserdlist.widgets', 4) * 2;
    }
    if ($listtype == 'Featured Slideshow') {
      $select = $select->where($listTableName . '.featured = ?', '1');
      $limit = (int) $settings->getSetting('list.feature.widgets', 10);
    }

    if ($listtype == 'Most Likes') {
      $enableNetwork = $settings->getSetting('list.network', 0);

      $viewer = Engine_Api::_()->user()->getViewer();
      $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
      $viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer->getIdentity()));

      if (empty($viewerNetwork) || empty($enableNetwork)) {
        $select->setIntegrityCheck(false)
                ->from($listTableName);
      }

      $select->join($coreName, "$listTableName.listing_id = $coreName.resource_id   ", array('COUNT( ' . $coreName . '.resource_id ) as count_likes'))
              ->group($coreName . '.resource_id')
              ->where($coreName . '.resource_type = ?', 'list_listing');
      $select = $select->order('count_likes DESC');
      $limit = (int) $settings->getSetting('list.likes.widgets', 3);
    }

    if ($listtype == 'Random') {
      $select->order('RAND() DESC ');
      $ShowViewArray = $settings->getSetting('list.ajax.widgets.layout', array("0" => "1", "1" => "2", "2" => "3"));
      $limit = 0;
      $temp_limit = 0;
      if (in_array("2", $ShowViewArray))
        $limit = (int) $settings->getSetting('list.random.thumbs', 15);
      if (in_array("1", $ShowViewArray))
        $temp_limit = (int) $settings->getSetting('list.random.widgets', 10);
      if ($limit < $temp_limit) {
        $limit = $temp_limit;
      }
    } else if ($listtype == 'Featured Slideshow') {
      $select->order('RAND() DESC ');
    } else {
      $select->order($listTableName . '.listing_id DESC');
    }

    if (isset($params['limit']) && !empty($params['limit'])) {
      $limit = $params['limit'];
    }

    if (($listtype == 'Sponsored List AJAX' || $listtype == 'Sponsored List' ) && !empty($params['start_index'])) {
      $select = $select->limit($limit, $params['start_index']);
    } else {
      if ($listtype != 'Total Sponsored List') {
        $select = $select->limit($limit);
      }
    }

    if ($listtype == 'Recently Posted' || $listtype == 'Most Viewed' || $listtype == 'Random' || $listtype == 'Featured' || $listtype == 'Sponosred') {
      $locationTable = Engine_Api::_()->getDbtable('locations', 'list');
      $locationTableName = $locationTable->info('name');

      $enableNetwork = $settings->getSetting('list.network', 0);
      $viewer = Engine_Api::_()->user()->getViewer();
      $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
      $viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer->getIdentity()));

      if (empty($viewerNetwork) || empty($enableNetwork)) {
        $select->setIntegrityCheck(false)
                ->from($listTableName);
      }
      $select->joinLeft($locationTableName, "$listTableName.listing_id = $locationTableName.listing_id   ", array('location'))
              ->group("$listTableName.listing_id");
    }
    return $table->fetchAll($select);
  }

  // get lising according to Discussion
  public function getDiscussedListing() {

    //GET LIST TABLE NAME
    $listTableName = $this->info('name');

    //GET TOPIC TABLE
    $topictable = Engine_Api::_()->getDbTable('topics', 'list');
    $topic_tableName = $topictable->info('name');

    //MAKE QUERY
    $select = $this->select()->setIntegrityCheck(false)
            ->from($listTableName, array('listing_id', 'title', 'photo_id', 'owner_id'))
            ->join($topic_tableName, $topic_tableName . '.listing_id = ' . $listTableName . '.listing_id', array('count(*) as counttopics', '(sum(post_count) - count(*) ) as total_count'))
            ->where($listTableName . '.closed = ?', '0')
            ->where($listTableName . '.approved = ?', '1')
            ->where($listTableName . '.draft = ?', '1')
            ->where($listTableName . ".search = ?", 1)
            ->where($topic_tableName . '.post_count > ?', '1')
            ->group($topic_tableName . '.listing_id')
            ->order('total_count DESC')
            ->order('counttopics DESC')
            ->limit(Engine_Api::_()->getApi('settings', 'core')->getSetting('list.mostdiscussed.widgets', 10));
    $select = $this->expirySQL($select);  

    //START NETWORK WORK
    $enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.network', 0);
    if (!empty($enableNetwork)) {
      $viewer = Engine_Api::_()->user()->getViewer();
      $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
      $viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer->getIdentity()));

      if (!empty($viewerNetwork)) {
        $networkMembershipName = $networkMembershipTable->info('name');
        $select
                ->join($networkMembershipName, "`{$listTableName}`.owner_id = `{$networkMembershipName}`.user_id  ", null)
                ->join($networkMembershipName, "`{$networkMembershipName}`.`resource_id`=`{$networkMembershipName}_2`.resource_id", null)
                ->where("`{$networkMembershipName}_2`.user_id = ? ", $viewer->getIdentity());
      }
    }
    //END NETWORK WORK
    //FETCH RESULTS
    return $this->fetchAll($select);
  }

  // get list list relative to list owner
  public function userListing($owner_id, $listing_id) {

    //GET LIST TABLE NAME
    $listTableName = $this->info('name');

    //MAKE QUERY
    $select = $this->select()
            ->from($listTableName, array('listing_id', 'title', 'photo_id', 'view_count', 'like_count', 'owner_id'))
            ->where($listTableName . '.closed = ?', '0')
            ->where($listTableName . '.approved = ?', '1')
            ->where($listTableName . '.draft = ?', '1')
            ->where($listTableName . ".search = ?", 1)
            ->where($listTableName . '.listing_id <> ?', $listing_id)
            ->where($listTableName . '.owner_id = ?', $owner_id)
            ->limit(Engine_Api::_()->getApi('settings', 'core')->getSetting('list.userlist.widgets', 3));
    $select = $this->expirySQL($select);   

    //START NETWORK WORK
    $enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.network', 0);
    if (!empty($enableNetwork)) {
      $viewer = Engine_Api::_()->user()->getViewer();
      $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
      $viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer->getIdentity()));

      if (!empty($viewerNetwork)) {
        $select->setIntegrityCheck(false)
                ->from($listTableName);
        $networkMembershipName = $networkMembershipTable->info('name');
        $select
                ->join($networkMembershipName, "`{$listTableName}`.owner_id = `{$networkMembershipName}`.user_id  ", null)
                ->join($networkMembershipName, "`{$networkMembershipName}`.`resource_id`=`{$networkMembershipName}_2`.resource_id", null)
                ->where("`{$networkMembershipName}_2`.user_id = ? ", $viewer->getIdentity())
                ->group($listTableName . '.listing_id');
      }
    }
    //END NETWORK WORK
    //RETURN RESULTS
    return $this->fetchAll($select);
  }

  /**
   * Handle archive list
   * @param array $results : document owner archive list array
   * @return list with detail.
   */
  public function getArchiveList($spec) {
    if (!($spec instanceof User_Model_User)) {
      return null;
    }

    $localeObject = Zend_Registry::get('Locale');
    if (!$localeObject) {
      $localeObject = new Zend_Locale();
    }

    $dates = $this->select()
            ->from($this->info('name'), 'creation_date')
            ->where('owner_type = ?', 'user')
            ->where('owner_id = ?', $spec->getIdentity())
            ->where('closed = ?', '0')
            ->where('approved = ?', '1')
            ->where('draft = ?', '1')
            ->where("search = ?", 1)
            ->order('listing_id DESC')
            ->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN);
    $select = $this->expirySQL($select);
    $time = time();

    $archive_list = array();
    foreach ($dates as $date) {

      $date = strtotime($date);
      $ltime = localtime($date, true);
      $ltime["tm_mon"] = $ltime["tm_mon"] + 1;
      $ltime["tm_year"] = $ltime["tm_year"] + 1900;

      // LESS THAN A YEAR AGO - MONTHS
      if ($date + 31536000 > $time) {
        $date_start = mktime(0, 0, 0, $ltime["tm_mon"], 1, $ltime["tm_year"]);
        $date_end = mktime(0, 0, 0, $ltime["tm_mon"] + 1, 1, $ltime["tm_year"]);
        $type = 'month';

        $dateObject = new Zend_Date($date);
        $format = $localeObject->getTranslation('yMMMM', 'dateitem', $localeObject);
        $label = $dateObject->toString($format, $localeObject);
      }
      // MORE THAN A YEAR AGO - YEARS
      else {
        $date_start = mktime(0, 0, 0, 1, 1, $ltime["tm_year"]);
        $date_end = mktime(0, 0, 0, 1, 1, $ltime["tm_year"] + 1);
        $type = 'year';

        $dateObject = new Zend_Date($date);
        $format = $localeObject->getTranslation('yyyy', 'dateitem', $localeObject);
        if (!$format) {
          $format = $localeObject->getTranslation('y', 'dateitem', $localeObject);
        }
        $label = $dateObject->toString($format, $localeObject);
      }

      if (!isset($archive_list[$date_start])) {
        $archive_list[$date_start] = array(
            'type' => $type,
            'label' => $label,
            'date' => $date,
            'date_start' => $date_start,
            'date_end' => $date_end,
            'count' => 1
        );
      } else {
        $archive_list[$date_start]['count']++;
      }
    }

    return $archive_list;
  }

  public function getListingSelectSql($params=array()) {
    $tableName = $this->info('name');
    $select = $this->select()
            ->where("{$tableName}.search = ?", 1)
            ->where("{$tableName}.closed = ?", '0')
            ->where("{$tableName}.approved = ?", '1')          
            ->where("{$tableName}.draft = ?", '1');
   
    if (isset($params['limit']) && !empty($params['limit']))
      $select->limit($params['limit']);
    return $select;
  }

}
