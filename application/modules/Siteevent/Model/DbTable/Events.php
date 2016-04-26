<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Events.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Model_DbTable_Events extends Engine_Db_Table {

    protected $_rowClass = "Siteevent_Model_Event";
    protected $_serializedColumns = array('networks_privacy');

    public function getOnlyViewableEventsId() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $events_ids = array();
        $cache = Zend_Registry::get('Zend_Cache');
        $cacheName = 'siteevent_ids_user_id_' . $viewer->getIdentity();
        $data = APPLICATION_ENV == 'development' ? ( Zend_Registry::isRegistered($cacheName) ? Zend_Registry::get($cacheName) : null ) : $cache->load($cacheName);
        if ($data && is_array($data)) {
            $events_ids = $data;
        } else {
            set_time_limit(0);
            $tableName = $this->info('name');
            $event_select = $this->select()
                    ->from($this->info('name'), array('event_id', 'owner_id', 'title', 'photo_id', 'networks_privacy'))
                    ->where("{$tableName}.search = ?", 1)
                    ->where("{$tableName}.closed = ?", 0)
                    ->where("{$tableName}.approved = ?", 1)
                    ->where("{$tableName}.draft = ?", 0);

            // Create new array filtering out private events
            $i = 0;
            foreach ($this->fetchAll($event_select) as $event) {
                if ($event->canView($viewer)) {
                    $events_ids[$i++] = $event->event_id;
                }
            }

            // Try to save to cache
            if (empty($events_ids))
                $events_ids = array(0);

            if (APPLICATION_ENV == 'development') {
                Zend_Registry::set($cacheName, $events_ids);
            } else {
                $cache->save($events_ids, $cacheName);
            }
        }

        return $events_ids;
    }

    public function addPrivacyEventsSQl($select, $tableName = null) {
        $privacybase = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.privacybase', 0);
        if (empty($privacybase))
            return $select;

        $column = $tableName ? "$tableName.event_id" : "event_id";

        return $select->where("$column IN(?)", $this->getOnlyViewableEventsId());
    }

    public function getSiteeventsPaginator($params = array(), $customParams = null) {

        $paginator = Zend_Paginator::factory($this->getSiteeventsSelect($params, $customParams));
        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }

        if (!empty($params['limit'])) {
            $paginator->setItemCountPerPage($params['limit']);
        }

        return $paginator;
    }

    //GET SITEEVENT SELECT QUERY
    public function getSiteeventsSelect($params = array(), $customParams = null) {

        //GET EVENT TABLE NAME
        $siteeventTableName = $this->info('name');
        $tempSelect = array();
        $isEventOccuranceTableJoin = false;

        //GET TAGMAP TABLE NAME
        $tagMapTableName = Engine_Api::_()->getDbtable('TagMaps', 'core')->info('name');

        //GET SEARCH TABLE
        $searchTable = Engine_Api::_()->fields()->getTable('siteevent_event', 'search')->info('name');

        //GET LOCATION TABLE
        $locationTable = Engine_Api::_()->getDbtable('locations', 'siteevent');
        $locationTableName = $locationTable->info('name');

        //GET API
        $settings = Engine_Api::_()->getApi('settings', 'core');

        //MAKE QUERY
        $select = $this->select();

        $select = $select
                ->setIntegrityCheck(false)
                ->from($siteeventTableName);

        $upcomingSelectCalled = false;
        if ((isset($params['showEventType']) && ($params['showEventType'] == 'upcoming' || $params['showEventType'] == 'onlyUpcoming' || $params['showEventType'] == 'past' || $params['showEventType'] == 'onlyOngoing')) || isset($params['action']) && ($params['action'] == 'upcoming' || $params['action'] == 'manage' || $params['action'] == 'onlyUpcoming' || $params['action'] == 'past')) {
            //GET THE UPCOMING EVENT SELECT
            $select = $this->getSiteeventsUpcomingSelect($select, $params);
            $upcomingSelectCalled = true;
            $isEventOccuranceTableJoin = true;
        } else if ((isset($params['action']) && $params['action'] == 'all') || isset($params['showEventType']) && $params['showEventType'] == 'all') {
            $select = $this->getSiteeventsAllSelect($select, array('action' => 'all'));
            $isEventOccuranceTableJoin = true;
        }

        if (isset($params['type']) && !empty($params['type'])) {

            if ($params['type'] == 'browse' || $params['type'] == 'home') {
                $select = $select
                        ->where($siteeventTableName . '.closed = ?', '0')
                        ->where($siteeventTableName . '.approved = ?', '1')
                        ->where($siteeventTableName . '.draft = ?', '0');

                if ($params['type'] == 'browse' && isset($params['showClosed']) && !$params['showClosed']) {
                    $select = $select->where($siteeventTableName . '.closed = ?', '0');
                }
            } elseif ($params['type'] == 'browse_home_zero') {
                $select = $select
                        ->where($siteeventTableName . '.closed = ?', '0')
                        ->where($siteeventTableName . '.approved = ?', '1')
                        ->where($siteeventTableName . '.draft = ?', '0');
            }
            if ($params['type'] != 'manage') {
                $select->where($siteeventTableName . ".search = ?", 1);
            }
        }

        if (isset($customParams) && !empty($customParams)) {

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

            $select = $select
                    ->setIntegrityCheck(false)
                    ->joinLeft($searchTable, "$searchTable.item_id = $siteeventTableName.event_id", null);

            $searchParts = Engine_Api::_()->fields()->getSearchQuery('siteevent_event', $customParams);
            foreach ($searchParts as $k => $v) {
                $select->where("`{$searchTable}`.{$k}", $v);
            }
        }
        
        if(isset($params['siteevent_city']) && !empty($params['siteevent_city']) && strstr(',', $params['siteevent_city'])){
            $siteevent_city = explode(',', $params['siteevent_city']);
            $params['siteevent_city'] = $siteevent_city[0];
        }

        
        if (isset($params['siteevent_street']) && !empty($params['siteevent_street']) || isset($params['siteevent_city']) && !empty($params['siteevent_city']) || isset($params['siteevent_state']) && !empty($params['siteevent_state']) || isset($params['siteevent_country']) && !empty($params['siteevent_country'])) {
            $select->join($locationTableName, "$siteeventTableName.event_id = $locationTableName.event_id   ", null);
        }
        
        
        if (isset($params['siteevent_street']) && !empty($params['siteevent_street'])) {
            $select->where($locationTableName . '.address   LIKE ? ', '%' . $params['siteevent_street'] . '%');
        } if (isset($params['siteevent_city']) && !empty($params['siteevent_city'])) {
            $select->where($locationTableName . '.city = ?', $params['siteevent_city']);
        } if (isset($params['siteevent_state']) && !empty($params['siteevent_state'])) {
            $select->where($locationTableName . '.state = ?', $params['siteevent_state']);
        } if (isset($params['siteevent_country']) && !empty($params['siteevent_country'])) {
            $select->where($locationTableName . '.country = ?', $params['siteevent_country']);
        }

        if (!isset($params['location']) && isset($params['locationSearch']) && !empty($params['locationSearch'])) {
            $params['location'] = $params['locationSearch'];

            if (isset($params['locationmilesSearch'])) {
                $params['locationmiles'] = $params['locationmilesSearch'];
            }
        }

        $addGroupBy = 1;
        if (!isset($params['location']) && (isset($params['detactLocation']) && $params['detactLocation'] && isset($params['latitude']) && $params['latitude'] && isset($params['longitude']) && $params['longitude'] && isset($params['defaultLocationDistance']) && $params['defaultLocationDistance'])) {
            $locationsTable = Engine_Api::_()->getDbtable('locations', 'siteevent');
            $locationTableName = $locationsTable->info('name');
            $radius = $params['defaultLocationDistance']; //in miles
            $latitude = $params['latitude'];
            $longitude = $params['longitude'];
            $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.proximity.search.kilometer', 0);
            if (!empty($flage)) {
                $radius = $radius * (0.621371192);
            }
            //$latitudeRadians = deg2rad($latitude);
            $latitudeSin = "sin(radians($latitude))";
            $latitudeCos = "cos(radians($latitude))";

            $select->join($locationTableName, "$siteeventTableName.event_id = $locationTableName.event_id", array("(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172) AS distance", $locationTableName . '.location AS locationName'));
            $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
            $sqlstring .= ")";
            $select->where($sqlstring);

            if (isset($params['orderby']) && $params['orderby'] == "distance") {
                $select->order("distance");
            }

            $select->group("$siteeventTableName.event_id");
            $addGroupBy = 0;
        }


        if ((isset($params['location']) && !empty($params['location'])) || (!empty($params['Latitude']) && !empty($params['Longitude']))) {
            $enable = $settings->getSetting('siteevent.proximitysearch', 1);
            $longitude = 0;
            $latitude = 0;
            $detactLatLng = false;
            if (isset($params['location']) && $params['location']) {
                $cookieLocation = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
                $detactLatLng = isset($cookieLocation['location']) && $cookieLocation['location'] != $params['location'];
            }
            if ((isset($params['locationmiles']) && (!empty($params['locationmiles']) && !empty($enable))) || $detactLatLng) {

                if($params['location']) {
                    $selectLocQuery = $locationTable->select()->where('location = ?', $params['location']);
                    $locationValue = $locationTable->fetchRow($selectLocQuery);
                }

                //check for zip code in location search.
                if ((empty($params['Latitude']) && empty($params['Longitude'])) || $detactLatLng) {
                    if (empty($locationValue)) {

                        $locationResults = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getLatLong(array('location' => $params['location'], 'module' => 'Advanced Events'));
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
                if ($latitude && $latitude && isset($params['location']) && $params['location']) {
                    $seaocore_myLocationDetails['latitude'] = $latitude;
                    $seaocore_myLocationDetails['longitude'] = $longitude;
                    $seaocore_myLocationDetails['location'] = $params['location'];
                    $seaocore_myLocationDetails['locationmiles'] = $params['locationmiles'];

                    Engine_Api::_()->seaocore()->setMyLocationDetailsCookie($seaocore_myLocationDetails);
                }
            }

            if (isset($params['locationmiles']) && (!empty($params['locationmiles']) && !empty($enable)) && $latitude && $longitude) {
                $radius = $params['locationmiles'];

                $flage = $settings->getSetting('siteevent.proximity.search.kilometer', 0);
                if (!empty($flage)) {
                    $radius = $radius * (0.621371192);
                }

                //$latitudeRadians = deg2rad($latitude);
                $latitudeSin = "sin(radians($latitude))";
                $latitudeCos = "cos(radians($latitude))";
                $select->join($locationTableName, "$siteeventTableName.event_id = $locationTableName.event_id", array("(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172) AS distance"));
                $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
                $sqlstring .= ")";
                $select->where($sqlstring);

                if (isset($params['orderby']) && $params['orderby'] == "distance") {
                    $select->order("distance");
                }
            } else {
                $select->join($locationTableName, "$siteeventTableName.event_id = $locationTableName.event_id", null);
                $select->where("`{$locationTableName}`.formatted_address LIKE ? or `{$locationTableName}`.location LIKE ? or `{$locationTableName}`.city LIKE ? or `{$locationTableName}`.state LIKE ?", "%" . $params['location'] . "%");
            }
        }

        if (isset($params['hasLocationBase']) && !empty($params['hasLocationBase'])) {
            $select->join($locationTableName, "$siteeventTableName.event_id = $locationTableName.event_id", array('latitude', 'longitude', 'formatted_address', 'country'));
        }
//         if (isset($params['minPrice']) && !empty($params['minPrice'])) {
//             $select->where($siteeventTableName . '.price >= ?', $params['minPrice']);
//         }

				
        if (isset($params['type']) && !empty($params['type']) && ($params['type'] == 'browse' || $params['type'] == 'home')) {
            //START NETWORK WORK

            if ($addGroupBy) {
                if(isset($params['networkBased'])) {
									$select = $this->getNetworkBaseSql($select, array('browse_network' => (isset($params['show']) && $params['show'] == "3"), 'networkBased' => $params['networkBased']));
								} else {
									$select = $this->getNetworkBaseSql($select, array('browse_network' => (isset($params['show']) && $params['show'] == "3")));
								}
            } else {
								if(isset($params['networkBased'])) {
									$select = $this->getNetworkBaseSql($select, array('browse_network' => (isset($params['show']) && $params['show'] == "3"), 'networkBased' => $params['networkBased'], 'not_groupBy' => 1));
								} else {
									$select = $this->getNetworkBaseSql($select, array('browse_network' => (isset($params['show']) && $params['show'] == "3"), 'not_groupBy' => 1));
								}
            }
            //END NETWORK WORK
        }

//         if (isset($params['price']['min']) && !empty($params['price']['min'])) {
//             $select->where($siteeventTableName . '.price >= ?', $params['price']['min']);
//         }
// 
//         if (isset($params['price']['max']) && !empty($params['price']['max'])) {
//             $select->where($siteeventTableName . '.price <= ?', $params['price']['max']);
//         }
        if (isset($params['host_type']) && !empty($params['host_type'])) {
            $select->where($siteeventTableName . '.host_type = ?', $params['host_type']);
            if (isset($params['host_id']) && !empty($params['host_id'])) {
                $select->where($siteeventTableName . '.host_id = ?', $params['host_id'] )
                       ->where($siteeventTableName.'.approved = ?', 1);
            }
            if (isset($params['host_ids']) && !empty($params['host_ids'])) {
                $select->where($siteeventTableName . '.host_id IN(?)', (array) $params['host_ids'])
                       ->where($siteeventTableName.'.approved = ?', 1);
            }
        }
        if (!empty($params['user_id']) && is_numeric($params['user_id']) && @$params['action'] != 'manage') {
            $select->where($siteeventTableName . '.owner_id = ?', $params['user_id']);
        }

        if (!empty($params['user']) && $params['user'] instanceof User_Model_User) {
            $select->where($siteeventTableName . '.owner_id = ?', $params['user_id']->getIdentity());
        }

        if (!empty($params['users'])) {
            $str = (string) ( is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users'] );
            $select->where($siteeventTableName . '.owner_id in (?)', new Zend_Db_Expr($str));
        }

        if (empty($params['users']) && isset($params['show']) && $params['show'] == '2') {
            $select->where($siteeventTableName . '.owner_id = ?', '0');
        }

        if ((isset($params['show']) && $params['show'] == "4")) {
            $likeTableName = Engine_Api::_()->getDbtable('likes', 'core')->info('name');
            $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
            $select->setIntegrityCheck(false)
                    ->join($likeTableName, "$likeTableName.resource_id = $siteeventTableName.event_id")
                    ->where($likeTableName . '.poster_type = ?', 'user')
                    ->where($likeTableName . '.poster_id = ?', $viewer_id)
                    ->where($likeTableName . '.resource_type = ?', 'siteevent_event');
        }
        $SiteEventOccuretable = Engine_Api::_()->getDbTable('occurrences', 'siteevent');
        $siteeventOccurTableName = $SiteEventOccuretable->info('name'); 
        if ((isset($params['event_time']) && ($params['event_time'] == "this_month" || $params['event_time'] == "this_week" || $params['event_time'] == "this_weekend" || $params['event_time'] == "tomorrow" || $params['event_time'] == "today"))) {

            
            if (empty($isEventOccuranceTableJoin)) {
                $siteeventTableName = $this->info('name');
                
                $select = $select->join($siteeventOccurTableName, "$siteeventTableName.event_id = $siteeventOccurTableName.event_id", array('starttime', 'endtime', 'occurrence_id'));
            }

            if ((isset($params['event_time']) && $params['event_time'] == "this_month")) {
                $select->where("(YEAR(starttime) <= YEAR(NOW()) AND (MONTH(starttime) <= MONTH(NOW()) OR (YEAR(starttime) > YEAR(NOW())))) AND ((MONTH(endtime) >= MONTH(NOW()) OR YEAR(endtime) > YEAR(NOW())) AND YEAR(endtime) >= YEAR(NOW()))");
            }

            if ((isset($params['event_time']) && $params['event_time'] == "this_week")) {
                $select = $select->where("(YEARWEEK(endtime) = YEARWEEK(CURRENT_DATE)) OR (YEARWEEK(starttime) = YEARWEEK(CURRENT_DATE)) OR (DATE(starttime) <= DATE(NOW()) AND DATE(endtime) >= DATE(NOW()))");
            }

            if ((isset($params['event_time']) && $params['event_time'] == "this_weekend")) {
                $select = $select->where("DATE(starttime) <= DATE(DATE_ADD(NOW(), INTERVAL(5 - WEEKDAY(NOW()))DAY)) AND DATE(endtime) >= DATE(DATE_ADD(NOW(), INTERVAL(5 - WEEKDAY(NOW())) DAY)) OR DATE(starttime) <= DATE(DATE_ADD(NOW(), INTERVAL(6 - WEEKDAY(NOW())) DAY)) AND DATE(endtime) >= DATE(DATE_ADD(NOW(), INTERVAL(6 - WEEKDAY(NOW())) DAY))");
            }

            if ((isset($params['event_time']) && $params['event_time'] == "tomorrow")) {
                $select = $select->where("DATE(endtime) >= DATE(DATE_ADD(NOW(), INTERVAL(1)DAY)) AND DATE(starttime) <= DATE(DATE_ADD(NOW(), INTERVAL(1)DAY))");
            }

            if ((isset($params['event_time']) && $params['event_time'] == "today")) {
                $select = $select->where("DATE(endtime) >= DATE(NOW()) AND DATE(starttime) <= DATE(NOW())");
            }
        }

        if (!empty($params['tag_id'])) {
            $select
                    ->setIntegrityCheck(false)
                    ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $siteeventTableName.event_id", array('tagmap_id', 'resource_type', 'resource_id', $tagMapTableName.'.tag_id'))
                    ->where($tagMapTableName . '.resource_type = ?', 'siteevent_event')
                    ->where($tagMapTableName . '.tag_id = ?', $params['tag_id']);
        }

        if (isset($params['category_id']) && !empty($params['category_id'])) {
            $select->where($siteeventTableName . '.category_id = ?', $params['category_id']);
        }

        if (isset($params['subcategory_id']) && !empty($params['subcategory_id'])) {
            $select->where($siteeventTableName . '.subcategory_id = ?', $params['subcategory_id']);
        }

        if (isset($params['subsubcategory_id']) && !empty($params['subsubcategory_id'])) {
            $select->where($siteeventTableName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
        }

        if (isset($params['closed']) && $params['closed'] != "") {
            $select->where($siteeventTableName . '.closed = ?', $params['closed']);
        }
        
        if (!empty($params['start_date'])) {
          $select->where($siteeventTableName.".creation_date > ?", date('Y-m-d', $params['start_date']));
        }

        if (!empty($params['end_date'])) {
          $select->where($siteeventTableName.".creation_date < ?", date('Y-m-d', $params['end_date']));
        }        

        // Could we use the search indexer for this?
        if (!empty($params['search'])) {

            $tagName = Engine_Api::_()->getDbtable('Tags', 'core')->info('name');
            $select
                    ->setIntegrityCheck(false)
                    ->joinLeft($tagMapTableName, "$tagMapTableName.resource_id = $siteeventTableName.event_id and " . $tagMapTableName . ".resource_type = 'siteevent_event'", array('tagmap_id', 'resource_type', 'resource_id', $tagMapTableName.'.tag_id'))
                    ->joinLeft($tagName, "$tagName.tag_id = $tagMapTableName.tag_id",array());

            $select->where($siteeventTableName . ".title LIKE ? OR " . $siteeventTableName . ".body LIKE ? OR " . $tagName . ".text LIKE ? ", '%' . $params['search'] . '%');
        }

        if (isset($params['venue_name']) && !empty($params['venue_name'])) {
            $select->where($siteeventTableName . ".venue_name LIKE ? ", '%' . $params['venue_name'] . '%');
        }

				if((!isset($params['has_free_price'])) || (isset($params['has_free_price']) && empty($params['has_free_price']))) {
					if (isset($params['price']['min']) && !empty($params['price']['min'])) {
							$select->where($siteeventTableName . '.price >= ?', $params['price']['min']);
					}

					if (isset($params['minPrice']) && !empty($params['minPrice'])) {
							$select->where($siteeventTableName . '.price >= ?', $params['minPrice']);
					}

					if (isset($params['price']['max']) && !empty($params['price']['max'])) {
							$select->where($siteeventTableName . '.price <= ?', $params['price']['max']);
					}

					if (isset($params['maxPrice']) && !empty($params['maxPrice'])) {
							$select->where($siteeventTableName . '.price <= ?', $params['maxPrice']);
					}
				}

        if (isset($params['has_free_price']) && !empty($params['has_free_price'])) {
           $select->where($siteeventTableName . '.price = ?', 0);
        }

        if (!empty($params['has_photo'])) {
            $select->where($siteeventTableName . ".photo_id > ?", 0);
        }

        if (!empty($params['has_review'])) {
            $has_review = $params['has_review'];
            $select->where($siteeventTableName . ".$has_review > ?", 0);
        }

        if (isset($params['eventType']) && !empty($params['eventType']) && $params['eventType'] != 'All') {
            if (strpos($params['eventType'], "sitereview_listing") !== false) {
                $explodedArray = explode("_", $params['eventType']);
                $listingTable = Engine_Api::_()->getDbtable('listings', 'sitereview');
                $listingTableName = $listingTable->info('name');
                $select->join($listingTableName, $listingTableName . '.listing_id = ' . $siteeventTableName . '.parent_id', array(""));
                $select->where($siteeventTableName . ".parent_type =?", 'sitereview_listing')
                        ->where($listingTableName . ".listingtype_id =?", $explodedArray[2]);
            } else {
                $select->where($siteeventTableName . ".parent_type =?", $params['eventType']);
            }
        }

        if (isset($params['parent_type']) && !empty($params['parent_type'])) {
            $select->where($siteeventTableName . ".parent_type =?", $params['parent_type']);
        }

        if (isset($params['parent_id']) && !empty($params['parent_id'])) {
            $select->where($siteeventTableName . ".parent_id =?", $params['parent_id']);
        }

        if (isset($params['most_rated'])) {
            $select->order($siteeventTableName . '.' . 'rating_avg' . ' DESC');
        }

        $orderbystarttime = 0;
        if (!$upcomingSelectCalled && !empty($params['orderby']) && $params['orderby'] == "starttime") {
            $select->order('starttime ASC');
            $orderbystarttime = 1;
        } elseif (!empty($params['orderby']) && $params['orderby'] == "priceLTH") {
            $select->order($siteeventTableName . '.price ASC');
        } elseif (!empty($params['orderby']) && $params['orderby'] == "priceHTL") {
            $select->order($siteeventTableName . '.price DESC');
        } elseif (!empty($params['orderby']) && $params['orderby'] == "title") {
            $select->order($siteeventTableName . '.' . $params['orderby']);
        } else if (!empty($params['orderby']) && $params['orderby'] == "fespfe") {
            $select->order($siteeventTableName . '.sponsored' . ' DESC')
                    ->order($siteeventTableName . '.featured' . ' DESC');
        } else if (!empty($params['orderby']) && $params['orderby'] == "spfesp") {
            $select->order($siteeventTableName . '.featured' . ' DESC')
                    ->order($siteeventTableName . '.sponsored' . ' DESC');
        }else if (!empty($params['orderby']) && $params['orderby'] == "viewcount") {
            $select->order($siteeventTableName . '.view_count' . ' DESC');
        } else if (!empty($params['orderby']) && $params['orderby'] != "starttime" && $params['orderby'] != 'distance') {
            $select->order($siteeventTableName . '.' . $params['orderby'] . ' DESC');
        }

        if(!$orderbystarttime && isset($params['orderbystarttime']) && !empty($params['orderbystarttime'])) {
            $select->order($siteeventOccurTableName . '.starttime ASC');
        }
        elseif (!isset($params['type']) || $params['type'] != 'manage') {
            $select->order($siteeventTableName . '.event_id DESC');
        }

        return $select;
    }

    //RETURN THE SELECT OF SQL FOR ALL EVENTS

    public function getSiteeventsAllSelect($select, $params) {
        if (empty($select) || $params['action'] != 'all')
            return;

        $siteeventTableName = $this->info('name');
        $siteeventOccurTableName = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->info('name');

        $select->join($siteeventOccurTableName, "$siteeventTableName.event_id = $siteeventOccurTableName.event_id", array('starttime', 'endtime', 'occurrence_id'));

        if (!empty($params['orderby']) && $params['orderby'] == "starttime") {
            $select->order('starttime ASC');
        }
        $select->group("$siteeventOccurTableName.event_id");

        return $select;
    }

    //RETURN THE SELECT OF SQL EITHER FOR UPCOMING OR PAST
    public function getSiteeventsUpcomingSelect($select, $params) {
        if (empty($select))
            return;

        $eventType = $params['action'];
        $siteeventTableName = $this->info('name');
        $SiteEventOccuretable = Engine_Api::_()->getDbTable('occurrences', 'siteevent');
        $siteeventOccurTableName = $SiteEventOccuretable->info('name');

        $coreTable = Engine_Api::_()->getDbTable('likes', 'core');
        $coreTableName = $coreTable->info('name');

        $select->join($siteeventOccurTableName, "$siteeventTableName.event_id = $siteeventOccurTableName.event_id", array('starttime', 'endtime', 'occurrence_id'));
        $upcomingSelectOrder = false;
        if($eventType == 'manage')
          $select = $this->getAllEventSql($select, $params);
        $setStartTime = 0;
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (isset($params['starttime']) && !empty($params['starttime']) && $params['starttime'] != '0000-00-00') {
            if ($viewer && $viewer->getIdentity()) {
                $oldTz = date_default_timezone_get();
                date_default_timezone_set($viewer->timezone);
                $starttime = strtotime($params['starttime']);
                date_default_timezone_set($oldTz);
                $params['starttime'] = date("Y-m-d H:i:s", $starttime);
            }
            $params['starttime'] = $params['starttime'] . ' 00:00:00';
            if (isset($params['endtime']) && !empty($params['endtime']) && $params['endtime'] != '0000-00-00') {
                $select->where("$siteeventOccurTableName.endtime >= ?", $params['starttime']);
            }
            else {
                $select->where("$siteeventOccurTableName.starttime >= ?", $params['starttime']);
            }
            $setStartTime = 1;
        }

        $setEndTime = 0;
        if (isset($params['endtime']) && !empty($params['endtime']) && $params['endtime'] != '0000-00-00') {
            if ($viewer && $viewer->getIdentity()) {
                $oldTz = date_default_timezone_get();
                date_default_timezone_set($viewer->timezone);
                $endtime = strtotime($params['endtime']);
                date_default_timezone_set($oldTz);
                $params['endtime'] = date("Y-m-d H:i:s", $endtime + (24 * 3600 - 1));
            }
            $params['endtime'] = $params['endtime'];
            if (isset($params['starttime']) && !empty($params['starttime']) && $params['starttime'] != '0000-00-00') {
                $select->where("$siteeventOccurTableName.starttime <= ?", $params['endtime']);
            }
            else {
                $select->where("$siteeventOccurTableName.endtime <= ?", $params['endtime']);
            }
            $setEndTime = 1;
        }

        if (!$upcomingSelectOrder && !empty($params['orderby']) && $params['orderby'] == "starttime") {
            $select->order('starttime ASC');
        }

        if (isset($params['host_type']) && !empty($params['host_type'])) {
            $select->where($siteeventTableName . '.host_type = ?', $params['host_type']);
            if (isset($params['host_id']) && !empty($params['host_id'])) {
                $select->where($siteeventTableName . '.host_id = ?', $params['host_id'])
                       ->where($siteeventTableName. '.approved = ?', 1);
            }
        }

        if ((empty($setStartTime) && (!isset($params['starttime']) || !empty($params['starttime']))) || (empty($setStartTime) && (!isset($params['starttime']) || !empty($params['starttime'])))) {
            if ($eventType == 'onlyOngoing' || (isset($params['viewtype']) && $params['viewtype'] == 'onlyOngoing')) {
                if (empty($setStartTime) && (!isset($params['starttime']) || !empty($params['starttime']))) {
                    $where = "$siteeventOccurTableName.starttime < NOW()";
                    $select->where("$where");
                }

                if (empty($setEndTime)) {
                    $select->having("MAX($siteeventOccurTableName.endtime) > NOW()");
                }
            } elseif ($eventType == 'past' || (isset($params['viewtype']) && $params['viewtype'] == 'past')) {
                if (empty($setEndTime)) {
                    $select->having("MAX($siteeventOccurTableName.endtime) < NOW()");
                }
                $select->order("$siteeventOccurTableName.endtime DESC");
            } elseif ($eventType == 'upcoming' || (isset($params['viewtype']) && $params['viewtype'] == 'upcoming')) {
                if (empty($setEndTime) && (!isset($params['endtime']) || !empty($params['endtime']))) {
                    $where = "$siteeventOccurTableName.endtime > NOW()";
                    $select->where("$where");
                }
            } elseif ($eventType == 'onlyUpcoming' || (isset($params['viewtype']) && $params['viewtype'] == 'onlyUpcoming')) {

                if (empty($setStartTime) && (!isset($params['starttime']) || !empty($params['starttime']))) {
                    $where = "$siteeventOccurTableName.starttime > NOW()";
                    $select->where("$where");
                }
            }
        }
        $select->group("$siteeventOccurTableName.event_id");

        return $select;
    }

    //RETURN SELECT BASIS ON THE FIXED TIME DURATION

    public function getSiteeventsTimebasis($select, $params) {

        $starttime = $params['starttime'];
        $endtime = $params['endtime'];
        $selecttype = $params['sql'];

        $siteeventTableName = $this->info('name');
        $SiteEventOccuretable = Engine_Api::_()->getDbTable('occurrences', 'siteevent');
        $siteeventOccurTableName = $SiteEventOccuretable->info('name');

        $columns_repeatevent_occure = array('starttime', 'occurrence_id');
        $select->setIntegrityCheck(false)
                ->join($siteeventOccurTableName, "$siteeventTableName.event_id = $siteeventOccurTableName.event_id", $columns_repeatevent_occure);

        if (is_numeric($starttime)) {
            $starttime = date("Y-m-d", $starttime) . ' 00:00:00';
            $endtime = date("Y-m-d", $endtime) . ' 23:59:59';
        }

        $select->where("`engine4_siteevent_occurrences`.starttime >= '$starttime' AND `engine4_siteevent_occurrences`.starttime <= '$endtime'");

        //IF THIS IS MANAGE LISTING PAGE THEN WE HAVE TO JOIN WITH MEMBERSHIP TABLE ALSO.
        
        if (isset($params['calendarlist']) && isset($params['ismanage']) && (!isset($params['siteevent_calendar_event_count_type']) || $params['siteevent_calendar_event_count_type'] == 'onlyjoined') && $params['sql'] != 'mycalendarlist' && isset($params['user_id'])) {
            $user_id = $params['user_id'];
            $SiteEventMembershiptable = Engine_Api::_()->getDbTable('membership', 'siteevent');
            $siteeventMembershipTableName = $SiteEventMembershiptable->info('name');
            $select->join($siteeventMembershipTableName, "$siteeventOccurTableName.occurrence_id = $siteeventMembershipTableName.occurrence_id AND $siteeventMembershipTableName.user_id = $user_id", array('rsvp', 'user_id AS membership_userid'));

            $where = "$siteeventTableName.owner_id = $user_id OR $siteeventMembershipTableName.user_id = $user_id";
            $select->where("$where");
        }
        else if(isset($params['user_id']) && ((isset($params['siteevent_calendar_event_count_type']) && $params['siteevent_calendar_event_count_type'] == 'all') || $params['sql'] == 'mycalendarlist'))  { 
          $params['rsvp'] = -1;
          $select = $this->getAllEventSql($select, $params);
        }

        if ($selecttype == 'count' && !isset($params['calendarlist'])) {
            $select->group("DATE_FORMAT(`engine4_siteevent_occurrences`.starttime, '%e')");
        } else {
            $select->group("engine4_siteevent_occurrences.occurrence_id");
            $select->order("$siteeventOccurTableName.starttime ASC");
        }

        return $select;
    }

    /**
     * Get event count based on category
     *
     * @param int $id
     * @param string $column_name
     * @param int $authorization
     * @return event count
     */
    public function getEventsCount($id, $column_name, $foruser = null) {

        //MAKE QUERY
        $select = $this->select()
                ->from($this->info('name'), array('COUNT(*) AS count'));

        if (!empty($foruser)) {
            $select->where('closed = ?', 0)
                    ->where('approved = ?', 1)
                    ->where('draft = ?', 0)
                    ->where('search = ?', 1);

            $select = $this->getNetworkBaseSql($select, array('not_groupBy' => 1));
        }

        if (!empty($column_name) && !empty($id)) {
            $select->where("$column_name = ?", $id);
        }

        $totalEvents = $select->query()->fetchColumn();

        //RETURN EVENTS COUNT
        return $totalEvents;
    }

    /**
     * Has Events 
     * @param int  : Event Type Id
     */
    public function hasEvents() {

        $select = $this->select()
                ->from($this->info('name'), 'event_id')
                ->where('closed = ?', 0)
                ->where('approved = ?', 1)
                ->where('draft = ?', 0)
                ->where('search = ?', 1);

        return $select->query()->fetchColumn();
    }

    /**
     * Get events based on category
     * @param string $title : search text
     * @param int $category_id : category id
     * @param char $popularity : result sorting based on views, reviews, likes, comments
     * @param char $interval : time interval
     * @param string $sqlTimeStr : Time durating string for where clause 
     */
    public function eventsBySettings($params = array()) {

        $groupBy = 1;
        $eventTableName = $this->info('name');

        $popularity = $params['popularity'];
        $interval = $params['interval'];

        //MAKE TIMING STRING
        $sqlTimeStr = '';
        $current_time = date("Y-m-d H:i:s");
        if ($interval == 'week') {
            $time_duration = date('Y-m-d H:i:s', strtotime('-7 days'));
            $sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'";
        } elseif ($interval == 'month') {
            $time_duration = date('Y-m-d H:i:s', strtotime('-1 months'));
            $sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'" . "";
        }

        $select = $this->select()->setIntegrityCheck(false);

        $select->from($eventTableName, array('event_id', 'title', 'photo_id', 'owner_id', 'category_id', 'view_count', 'review_count', 'like_count', 'comment_count', 'member_count', 'rating_avg', 'rating_editor', 'rating_users', 'sponsored', 'featured', 'newlabel', 'creation_date', 'body', 'location', 'price', 'venue_name', 'host_type', 'host_id', 'is_online', 'repeat_params', 'parent_id', 'parent_type', 'approval'));

        if ($interval != 'overall' && $popularity == 'review_count') {

            $popularityTable = Engine_Api::_()->getDbtable('reviews', 'siteevent');
            $popularityTableName = $popularityTable->info('name');
            $select = $select->joinLeft($popularityTableName, "($popularityTableName.resource_id = $eventTableName.event_id and $popularityTableName .resource_type ='siteevent_event')", array("COUNT(review_id) as total_count"))
                    ->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.creation_date is null')
                    ->order("total_count DESC");
        } elseif ($interval != 'overall' && ($popularity == 'rating_avg' || $popularity == 'rating_editor' || $popularity == 'rating_users')) {

            if ($interval == 'week') {
                $time_duration = date('Y-m-d H:i:s', strtotime('-7 days'));
                $sqlTimeStr = ".modified_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'";
            } elseif ($interval == 'month') {
                $time_duration = date('Y-m-d H:i:s', strtotime('-1 months'));
                $sqlTimeStr = ".modified_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'" . "";
            }

            $ratingTable = Engine_Api::_()->getDbTable('ratings', 'siteevent');
            $ratingTableName = $ratingTable->info('name');

            $popularityTable = Engine_Api::_()->getDbtable('reviews', 'siteevent');
            $popularityTableName = $popularityTable->info('name');
            $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $eventTableName . '.event_id', array(""))
                    ->join($ratingTableName, $ratingTableName . '.review_id = ' . $popularityTableName . '.review_id')
                    ->where($popularityTableName . '.resource_type = ?', 'siteevent_event')
                    ->where($ratingTableName . '.ratingparam_id = ?', 0);

            if ($popularity == 'rating_editor') {
                $select->where("$popularityTableName.type = ?", 'editor');
            } elseif ($popularity == 'rating_users') {
                $select->where("$popularityTableName.type = ?", 'user')
                        ->orWhere("$popularityTableName.type = ?", 'visitor');
            }

            $select->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.modified_date is null');
            $select->order("$eventTableName.$popularity DESC");
        } elseif ($interval != 'overall' && $popularity == 'like_count') {

            $popularityTable = Engine_Api::_()->getDbtable('likes', 'core');
            $popularityTableName = $popularityTable->info('name');

            $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $eventTableName . '.event_id', array("COUNT(like_id) as total_count"))
                    ->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.creation_date is null')
                    ->order("total_count DESC");
        } elseif ($interval != 'overall' && $popularity == 'comment_count') {

            $popularityTable = Engine_Api::_()->getDbtable('comments', 'core');
            $popularityTableName = $popularityTable->info('name');

            $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $eventTableName . '.event_id', array("COUNT(comment_id) as total_count"))
                    ->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.creation_date is null')
                    ->order("total_count DESC");
        } elseif ($popularity == 'most_discussed') {

            $popularityTable = Engine_Api::_()->getDbtable('posts', 'siteevent');
            $popularityTableName = $popularityTable->info('name');
            $select = $select->joinLeft($popularityTableName, $popularityTableName . '.event_id = ' . $eventTableName . '.event_id', array("COUNT(post_id) as total_count"))
                    ->order("total_count DESC");

            if ($interval != 'overall') {
                $select->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.creation_date is null');
            }
        } elseif ($popularity == 'view_count' || $popularity == 'event_id' || $popularity == 'modified_date' || $popularity == 'creation_date' || $popularity == 'member_count') {
            $select->order("$eventTableName.$popularity DESC");
        } elseif ($interval == 'overall' && ($popularity == 'review_count' || $popularity == 'like_count' || $popularity == 'comment_count' || $popularity == 'rating_avg' || $popularity == 'rating_editor' || $popularity == 'rating_users')) {
            $select->order("$eventTableName.$popularity DESC");
        }

        $select->group($eventTableName . '.event_id');
        $select->where($eventTableName . '.closed = ?', '0')
                ->where($eventTableName . '.approved = ?', '1')
                ->where($eventTableName . '.search = ?', '1')
                ->where($eventTableName . '.draft = ?', '0');

        if (isset($params['detactLocation']) && $params['detactLocation'] && isset($params['latitude']) && $params['latitude'] && isset($params['longitude']) && $params['longitude'] && isset($params['defaultLocationDistance']) && $params['defaultLocationDistance']) {
            $locationsTable = Engine_Api::_()->getDbtable('locations', 'siteevent');
            $locationTableName = $locationsTable->info('name');
            $radius = $params['defaultLocationDistance']; //in miles
            $latitude = $params['latitude'];
            $longitude = $params['longitude'];
            $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.proximity.search.kilometer', 0);
            if (!empty($flage)) {
                $radius = $radius * (0.621371192);
            }
            //$latitudeRadians = deg2rad($latitude);
            $latitudeSin = "sin(radians($latitude))";
            $latitudeCos = "cos(radians($latitude))";

            $select->join($locationTableName, "$eventTableName.event_id = $locationTableName.event_id", array("(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172) AS distance", $locationTableName . '.location AS locationName'));
            $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
            $sqlstring .= ")";
            $select->where($sqlstring);
            //$select->order("distance");
            //$select->group("$eventTableName.event_id");
        }

        if (isset($params['showEventType']) && ($params['showEventType'] == 'upcoming' || $params['showEventType'] == 'onlyUpcoming' || $params['showEventType'] == 'past')) {
            //$params['action'] = 'upcoming';
            $params['action'] = $params['showEventType'];
            $params['orderby'] = $popularity;
            $select = $this->getSiteeventsUpcomingSelect($select, $params);
        } else if (isset($params['showEventType']) && $params['showEventType'] == 'all') {
            $params['action'] = 'all';
            $params['orderby'] = $popularity;
            $select = $this->getSiteeventsAllSelect($select, $params);
        }

        if (isset($params['featured']) && !empty($params['featured'])) {
            $select->where('featured = ?', 1);
        }

        if (isset($params['sponsored']) && !empty($params['sponsored'])) {
            $select->where('sponsored = ?', 1);
        }

        if (isset($params['newlabel']) && !empty($params['newlabel'])) {
            $select->where('newlabel = ?', 1);
        }
        
        if(isset($params['sponsored_or_featured']) && !empty($params['sponsored_or_featured'])) {
            $select->where("$eventTableName.sponsored = 1 OR $eventTableName.featured = 1");
        }        

        if (isset($params['category_id']) && !empty($params['category_id'])) {
            $select->where($eventTableName . '.category_id = ?', $params['category_id']);
        }

        if (isset($params['subcategory_id']) && !empty($params['subcategory_id'])) {
            $select->where($eventTableName . '.subcategory_id = ?', $params['subcategory_id']);
        }

        if (isset($params['subsubcategory_id']) && !empty($params['subsubcategory_id'])) {
            $select->where($eventTableName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
        }

        if (isset($params['event_ids']) && !empty($params['event_ids'])) {
            $select->where("$eventTableName.event_id IN (?)", $params['event_ids']);
        }

        if (isset($params['eventType']) && !empty($params['eventType']) && $params['eventType'] != 'All') {
            if (strpos($params['eventType'], "sitereview_listing") !== false) {
                $explodedArray = explode("_", $params['eventType']);
                $listingTable = Engine_Api::_()->getDbtable('listings', 'sitereview');
                $listingTableName = $listingTable->info('name');
                $select->join($listingTableName, $listingTableName . '.listing_id = ' . $eventTableName . '.parent_id', array(""));
                $select->where($eventTableName . ".parent_type =?", 'sitereview_listing')
                        ->where($listingTableName . ".listingtype_id =?", $explodedArray[2]);
            } else {
                $select->where($eventTableName . ".parent_type =?", $params['eventType']);
            }
        }

        if (isset($params['popularity']) && !empty($params['popularity']) && $params['popularity'] != 'event_id' && $params['popularity'] != 'creation_date' && $params['popularity'] != 'random') {
            $select->order($eventTableName . ".event_id DESC");
        }

        if (isset($params['popularity']) && $params['popularity'] == 'random') {
            $select->order('RAND()');
        }
        
        if(!empty($params['eventsInWaitlist'])) {
            $waitlistTable = Engine_Api::_()->getDbTable('waitlists', 'siteevent');
            $waitlistTableName = $waitlistTable->info('name');
            
            $occurrenceTable = Engine_Api::_()->getDbTable('occurrences', 'siteevent');
            $occurrenceTableName = $occurrenceTable->info('name');
            
            $select->join($occurrenceTableName, "$occurrenceTableName.event_id = $eventTableName.event_id", null)
                ->join($waitlistTableName, "$waitlistTableName.occurrence_id = $occurrenceTableName.occurrence_id");
            
            if(!empty($params['user_id'])) {
                $select->where($waitlistTableName.".user_id = ?", $params['user_id']);
            }
            
            $select->order("$waitlistTableName.creation_date");
        }
        
        //Start Network work
        $select = $this->getNetworkBaseSql($select, array('not_groupBy' => $groupBy));
        //End Network work
        if (isset($params['paginator']) && !empty($params['paginator'])) {
            $paginator = Zend_Paginator::factory($select);
            if (isset($params['page']) && !empty($params['page'])) {
                $paginator->setCurrentPageNumber($params['page']);
            }

            if (isset($params['limit']) && !empty($params['limit'])) {
                $paginator->setItemCountPerPage($params['limit']);
            }

            return $paginator;
        }
        if (isset($params['limit']) && !empty($params['limit'])) {
            $select->limit($params['limit']);
        }

        return $this->fetchAll($select);
    }

    /**
     * Get pages to add as item of the day
     * @param string $title : search text
     * @param int $limit : result limit
     */
    public function getDayItems($title, $limit = 10) {

        //MAKE QUERY
        $select = $this->select()
                ->from($this->info('name'), array('event_id', 'owner_id', 'title', 'photo_id'))
                ->where('title  LIKE ? ', '%' . $title . '%')
                ->where('closed = ?', '0')
                ->where('approved = ?', '1')
                ->where('draft = ?', '0')
                ->where('search = ?', '1')
                ->order('title ASC')
                ->limit($limit);

        //RETURN RESULTS
        return $this->fetchAll($select);
    }

    /**
     * Return event data
     *
     * @param array params
     * @return Zend_Db_Table_Select
     */
    public function widgetEventsData($params = array()) {

        //GET TABLE NAME
        $tableEventName = $this->info('name');

        //MAKE QUERY
        $select = $this->select()->from($tableEventName, array("event_id", "title", "category_id", "subcategory_id", "subsubcategory_id", "view_count", "comment_count", "member_count", "like_count", "rating_avg", "rating_editor", "rating_users", "review_count", "owner_id", "photo_id", "price", 'featured', 'sponsored', 'newlabel', 'venue_name', 'location', 'host_type', 'host_id', 'is_online', 'parent_id', 'parent_type', 'repeat_params'));

        //SELECT ONLY AUTHENTICATE EVENTS
        $select = $select->where('approved = ?', 1)
                ->where('draft = ?', 0)
                ->where('closed = ?', 0)
                ->where('search = ?', 1);

        if (isset($params['showEventType']) && ($params['showEventType'] == 'upcoming' || $params['showEventType'] == 'onlyUpcoming' || $params['showEventType'] == 'past')) {
            $params['action'] = $params['showEventType'];
            $select = $select->setIntegrityCheck(false);
            $select = $this->getSiteeventsUpcomingSelect($select, $params);
        } else if (isset($params['showEventType']) && $params['showEventType'] == 'all') {
            $select = $select->setIntegrityCheck(false);
            $select = $this->getSiteeventsAllSelect($select, array('action' => 'all'));
        }

        if (isset($params['zero_count']) && !empty($params['zero_count'])) {
            $select->where($params['zero_count'] . ' != ?', 0);
        }

        if (isset($params['owner_id']) && !empty($params['owner_id'])) {
            $select->where('owner_id = ?', $params['owner_id']);
        }

        if (isset($params['event_id']) && !empty($params['event_id'])) {
            $select->where($tableEventName . '.event_id != ?', $params['event_id']);
        }

        if (isset($params['featured']) && !empty($params['featured'])) {
            $select->where('featured = ?', 1);
        }

        if ((isset($params['category_id']) && !empty($params['category_id']))) {
            $select->where('category_id = ?', $params['category_id']);
        }

        if (isset($params['detactLocation']) && $params['detactLocation'] && isset($params['latitude']) && $params['latitude'] && isset($params['longitude']) && $params['longitude'] && isset($params['defaultLocationDistance']) && $params['defaultLocationDistance']) {
            $locationsTable = Engine_Api::_()->getDbtable('locations', 'siteevent');
            $locationTableName = $locationsTable->info('name');
            $radius = $params['defaultLocationDistance']; //in miles
            $latitude = $params['latitude'];
            $longitude = $params['longitude'];
            $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.proximity.search.kilometer', 0);
            if (!empty($flage)) {
                $radius = $radius * (0.621371192);
            }
            // $latitudeRadians = deg2rad($latitude);
            $latitudeSin = "sin(radians($latitude))";
            $latitudeCos = "cos(radians($latitude))";

            $select->join($locationTableName, "$tableEventName.event_id = $locationTableName.event_id", array("(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172) AS distance", $locationTableName . '.location AS locationName'));
            $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
            $sqlstring .= ")";
            $select->where($sqlstring);
            //$select->order("distance");
            //$select->group("$tableEventName.event_id");
        }

        if (isset($params['tags']) && !empty($params['tags'])) {

            //GET TAG MAPS TABLE NAME
            $tableTagmapsName = Engine_Api::_()->getDbtable('TagMaps', 'core')->info('name');

            $select->setIntegrityCheck(false)
                    ->joinLeft($tableTagmapsName, "$tableTagmapsName.resource_id = $tableEventName.event_id")
                    ->where($tableTagmapsName . '.resource_type = ?', 'siteevent_event');

            foreach ($params['tags'] as $tag_id) {
                $tagSqlArray[] = "$tableTagmapsName.tag_id = $tag_id";
            }
            $select->where("(" . join(") or (", $tagSqlArray) . ")");
        }

        if (isset($params['eventType']) && !empty($params['eventType']) && $params['eventType'] != 'All') {
            if (strpos($params['eventType'], "sitereview_listing") !== false) {
                $explodedArray = explode("_", $params['eventType']);
                $listingTable = Engine_Api::_()->getDbtable('listings', 'sitereview');
                $listingTableName = $listingTable->info('name');
                $select->join($listingTableName, $listingTableName . '.listing_id = ' . $tableEventName . '.parent_id', array(""));
                $select->where($tableEventName . ".parent_type =?", 'sitereview_listing')
                        ->where($listingTableName . ".listingtype_id =?", $explodedArray[2]);
            } else {
                $select->where($tableEventName . ".parent_type =?", $params['eventType']);
            }
        }

        if (isset($params['orderby']) && !empty($params['orderby'])) {
            $select->order($params['orderby']);
        }

        $select->order("$tableEventName.event_id DESC");

        if (isset($params['limit']) && !empty($params['limit'])) {
            $select->limit($params['limit']);
        }
        //Start Network work
				if(isset($params['networkBased'])) {
					$select = $this->getNetworkBaseSql($select, array('not_groupBy' => 1, 'networkBased' => $params['networkBased']));
        } else {
					$select = $this->getNetworkBaseSql($select, array('not_groupBy' => 1));
				}
        //End Network work
        $select->group("$tableEventName.event_id");

        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            return Zend_Paginator::factory($select);
        }

        return $this->fetchAll($select);
    }

    public function getMappedSiteevent($category_id) {

        //RETURN IF CATEGORY ID IS NULL
        if (empty($category_id)) {
            return null;
        }

        //MAKE QUERY
        $select = $this->select()
                ->from($this->info('name'), 'event_id')
                ->where("category_id = $category_id OR subcategory_id = $category_id OR subsubcategory_id = $category_id");

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
    public function getPopularLocation($params = null) {

        //GET SITEEVENT TABLE NAME
        $siteeventTableName = $this->info('name');

        //GET LOCATION TABLE
        $locationTable = Engine_Api::_()->getDbtable('locations', 'siteevent');
        $locationTableName = $locationTable->info('name');

        //MAKE QUERY
        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($siteeventTableName, array("event_id"))
                ->where($siteeventTableName . '.approved = ?', '1')
                ->where($siteeventTableName . '.draft = ?', '0')
                ->where($siteeventTableName . ".search = ?", 1)
                ->where($siteeventTableName . '.closed = ?', '0');
        if (isset($params['category_id']) && !empty($params['category_id'])) {
            $select->where($siteeventTableName . 'category_id = ?', $params['category_id']);
        }

        if (isset($params['subcategory_id']) && !empty($params['subcategory_id'])) {
            $select->where($siteeventTableName . 'subcategory_id = ?', $params['subcategory_id']);
        }

        if (isset($params['subsubcategory_id']) && !empty($params['subsubcategory_id'])) {
            $select->where($siteeventTableName . 'subsubcategory_id = ?', $params['subsubcategory_id']);
        }

        if (isset($params['eventType']) && !empty($params['eventType'])) {
            $siteeventOccurTable = Engine_Api::_()->getDbTable('occurrences', 'siteevent');
            $siteeventOccurTableName = $siteeventOccurTable->info('name');
            $select->join($siteeventOccurTableName, "$siteeventTableName.event_id=$siteeventOccurTableName.event_id", array());
            if ($params['eventType'] == 'upcoming') {
                $select->where("$siteeventOccurTableName.endtime > NOW()");
            } elseif ($params['eventType'] == 'onlyUpcoming') {
                $select->where("$siteeventOccurTableName.starttime > NOW()");
            } elseif ($params['eventType'] == 'past') {
                $select->where("$siteeventOccurTableName.starttime < NOW()");
            }
        }
        $select->group("$siteeventTableName.event_id");
        $select = $this->getNetworkBaseSql($select, array('not_groupBy' => 1));

        $eventIds = $select
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);

        if (empty($eventIds)) {
            return;
        }
        $lselect = $locationTable->select()
                ->setIntegrityCheck(false)
                ->from($locationTableName, array("city", "count(city) as count_location", "state", "count(state) as count_location_state"))
                ->where("$locationTableName.event_id IN (?)", $eventIds)
                ->group("city")
                ->group("state")
                ->order("count_location DESC");
        if (isset($params['limit']) && !empty($params['limit'])) {
            $lselect->limit($params['limit']);
        }

        //RETURN RESULTS
        return $locationTable->fetchAll($lselect);
    }

    public function getNetworkBaseSql($select, $params = array()) {
        $select = $this->addPrivacyEventsSQl($select, $this->info('name'));
        if (empty($select))
            return;

        //GET SITEEVENT TABLE NAME
        $siteeventTableName = $this->info('name');
				$params['memberBased'] = 0;
				$enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.network', 0);
				if(isset($params['networkBased']) && !empty($params['networkBased'])) {
					$enableNetwork = 1;
					$params['memberBased']=1;
				}
        //START NETWORK WORK
        if (!empty($enableNetwork) || (isset($params['browse_network']) && !empty($params['browse_network']))) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
            if (!Zend_Registry::isRegistered('viewerNetworksIdsSR')) {
                $viewerNetworkIds = $networkMembershipTable->getMembershipsOfIds($viewer);
                Zend_Registry::set('viewerNetworksIdsSR', $viewerNetworkIds);
            } else {
                $viewerNetworkIds = Zend_Registry::get('viewerNetworksIdsSR');
            }

            if (!Engine_Api::_()->siteevent()->listBaseNetworkEnable()) {
                if (!empty($viewerNetworkIds)) {
                    if (isset($params['setIntegrity']) && !empty($params['setIntegrity'])) {
                        $select->setIntegrityCheck(false)
                                ->from($siteeventTableName);
                    }
                    $networkMembershipName = $networkMembershipTable->info('name');
                    $select
                            ->join($networkMembershipName, "`{$siteeventTableName}`.owner_id = `{$networkMembershipName}`.user_id  ", null)
                            ->where("`{$networkMembershipName}`.`resource_id`  IN (?) ", (array) $viewerNetworkIds);
                    if (!isset($params['not_groupBy']) || empty($params['not_groupBy'])) {
                        $select->group($siteeventTableName . ".event_id");
                    }
                }
            } else {
                // $viewerNetwork = $networkMembershipTable->getMembershipsOfInfo($viewer);
                $columnName = "`{$siteeventTableName}`.networks_privacy";
                $query = $columnName . " IS NULL";
                if ($viewer->getIdentity()) {
                    $query .= ' or ' . "`{$siteeventTableName}`.owner_id =" . $viewer->getIdentity();

//          $leadsEventIds = Engine_Api::_()->getDbtable('lists', 'siteevent')->getEventsUserLead($viewer->getIdentity());
//          if ($leadsEventIds) {
//            $query .= ' or ' . "`{$siteeventTableName}`.event_id IN($leadsEventIds)";
//          }
                    $eventMembershipTable = Engine_Api::_()->getDbtable('membership', 'siteevent');
                    $eventMembershipsOfIds = $eventMembershipTable->getMembershipsOfIds($viewer);
                    if ($eventMembershipsOfIds) {
                        $query .= ' or ' . "`{$siteeventTableName}`.event_id IN(" . (string) ( join(",", $eventMembershipsOfIds) ) . ")";
                    }
                }
                $str = array();
                foreach ($viewerNetworkIds as $networkId) {
                    $str[] = '\'%"' . $networkId . '"%\'';
                }
                if (!empty($str)) {
                    $likeNetworkVale = (string) ( join(" or $columnName  LIKE ", $str) );
                    $query .= ' or ' . $columnName . ' LIKE ' . $likeNetworkVale;
                }
                $select->where($query);
            }
        }
        //END NETWORK WORK
        //RETURN QUERY

        return $select;
    }

    public function recentlyViewed($params = array()) {

        //GET VIEWER ID
        $viewer_id = $params['viewer_id'];

        //GET VIEWER TABLE
        $viewTable = Engine_Api::_()->getDbtable('vieweds', 'siteevent');
        $viewTableName = $viewTable->info('name');

        //GET SITEEVENT TABLE NAME
        $siteeventTableName = $this->info('name');

        //MAKE QUERY
        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($siteeventTableName, array('event_id', 'title', 'owner_id', 'photo_id', 'review_count', 'view_count', 'like_count', 'comment_count', "member_count", 'category_id', 'rating_avg', 'rating_editor', 'rating_users', 'featured', 'sponsored', 'newlabel', 'venue_name', 'location', 'price', 'host_type', 'host_id', 'is_online', 'repeat_params', 'parent_id', 'parent_type'))
                ->joinInner($viewTableName, "$siteeventTableName . event_id = $viewTableName . event_id", array(''));
        $select = $this->getNetworkBaseSql($select);
        if ($params['show'] == 1) {

            //GET MEMBERSHIP TABLE
            $membership_table = Engine_Api::_()->getDbtable('membership', 'user');
            $ids = $membership_table->getMembershipsOfIds(Engine_Api::_()->user()->getViewer());
            if (empty($ids))
                $ids[] = -1;
            $select->where($viewTableName . '.viewer_id  In(?)', (array) $ids);
        } else {
            $select->where($viewTableName . '.viewer_id = ?', $viewer_id);
        }

        // FOR FETCHING ALL THE EVENTS
        if (isset($params['showEventType']) && $params['showEventType'] == 'all') {
            $this->getSiteeventsAllSelect($select, array('action' => 'all'));
        }

        $select->group($viewTableName . '.event_id')->order('date DESC');

        if (isset($params['featured']) && !empty($params['featured'])) {
            $select->where($siteeventTableName . '.featured = ?', 1);
        }

        if (isset($params['sponsored']) && !empty($params['sponsored'])) {
            $select->where($siteeventTableName . '.sponsored = ?', 1);
        }

        if (isset($params['newlabel']) && !empty($params['newlabel'])) {
            $select->where($siteeventTableName . '.newlabel = ?', 1);
        }
        
        if(isset($params['sponsored_or_featured']) && !empty($params['sponsored_or_featured'])) {
            $select->where("$siteeventTableName.sponsored = 1 OR $siteeventTableName.featured = 1");
        }        

        if (isset($params['category_id']) && !empty($params['category_id'])) {
            $select->where($siteeventTableName . '.category_id = ?', $params['category_id']);
        }

        if (isset($params['subcategory_id']) && !empty($params['subcategory_id'])) {
            $select->where($siteeventTableName . '.subcategory_id = ?', $params['subcategory_id']);
        }

        if (isset($params['subsubcategory_id']) && !empty($params['subsubcategory_id'])) {
            $select->where($siteeventTableName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
        }

        $select->where($siteeventTableName . '.closed = ?', '0')
                ->where($siteeventTableName . '.approved = ?', '1')
                ->where($siteeventTableName . '.draft = ?', '0')
                ->where($siteeventTableName . ".search = ?", '1');

        if (isset($params['eventType']) && !empty($params['eventType']) && $params['eventType'] != 'All') {
            if (strpos($params['eventType'], "sitereview_listing") !== false) {
                $explodedArray = explode("_", $params['eventType']);
                $listingTable = Engine_Api::_()->getDbtable('listings', 'sitereview');
                $listingTableName = $listingTable->info('name');
                $select->join($listingTableName, $listingTableName . '.listing_id = ' . $siteeventTableName . '.parent_id', array(""));
                $select->where($siteeventTableName . ".parent_type =?", 'sitereview_listing')
                        ->where($listingTableName . ".listingtype_id =?", $explodedArray[2]);
            } else {
                $select->where($siteeventTableName . ".parent_type =?", $params['eventType']);
            }
        }

        if (isset($params['paginator']) && !empty($params['paginator'])) {
            return $paginator = Zend_Paginator::factory($select);
        }

        if (isset($params['limit']) && !empty($params['limit'])) {
            $select->limit($params['limit']);
        }

        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            return Zend_Paginator::factory($select);
        }

        //RETURN RESULTS
        return $this->fetchAll($select);
    }

    public function getEvent($siteeventCondition = '', $params = array()) {

        $limit = 10;
        $isEventOccuranceTableJoin = false;
        $siteeventTableName = $this->info('name');

        $select = $this->select();
        if (isset($params['sql']) && $params['sql'] == 'count')
            $select->from($siteeventTableName, array());
        elseif (isset($params['sql']) && $params['sql'] == 'mycalendarlist')
            $select->from($siteeventTableName, array('event_id', 'title'));
        else
            $select->from($siteeventTableName, array('event_id', 'title', 'owner_id', 'photo_id', 'category_id', 'host_type', 'host_id', 'approved', 'featured', 'sponsored', 'newlabel', 'rating_avg', 'rating_editor', 'rating_users', 'location', 'price', 'review_count', 'view_count', 'comment_count', 'like_count', 'member_count', 'networks_privacy', 'venue_name', 'approval', 'parent_id', 'parent_type', 'is_online', 'repeat_params'));
        if (isset($params['calendarlist'])) {
            $select = $this->getSiteeventsTimebasis($select, $params);
        }
        if (!isset($params['ismanage'])) {
            $select->where($siteeventTableName . '.closed = ?', '0')
                    ->where($siteeventTableName . '.approved = ?', '1')
                    ->where($siteeventTableName . '.draft = ?', '0')
                    ->where($siteeventTableName . ".search = ?", 1);
        }
        if (isset($params['detactLocation']) && $params['detactLocation'] && isset($params['latitude']) && $params['latitude'] && isset($params['longitude']) && $params['longitude'] && isset($params['defaultLocationDistance']) && $params['defaultLocationDistance']) {
            $locationsTable = Engine_Api::_()->getDbtable('locations', 'siteevent');
            $locationTableName = $locationsTable->info('name');
            $radius = $params['defaultLocationDistance']; //in miles
            $latitude = $params['latitude'];
            $longitude = $params['longitude'];
            $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.proximity.search.kilometer', 0);
            if (!empty($flage)) {
                $radius = $radius * (0.621371192);
            }
            // $latitudeRadians = deg2rad($latitude);
            $latitudeSin = "sin(radians($latitude))";
            $latitudeCos = "cos(radians($latitude))";

            $select->setIntegrityCheck(false)->join($locationTableName, "$siteeventTableName.event_id = $locationTableName.event_id", array("(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172) AS distance", $locationTableName . '.location AS locationName'));
            $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
            $sqlstring .= ")";
            $select->where($sqlstring);
            //$select->order("distance");
            //$select->group("$siteeventTableName.event_id");
        }

        if ($siteeventCondition === 'upcoming') {
            /* isset($params['showEventType']) && ($params['showEventType'] == 'upcoming' || $params['showEventType'] == 'onlyUpcoming'; */
            $isEventOccuranceTableJoin = true;
            $select = $select->setIntegrityCheck(false);
            $select = $this->getSiteeventsUpcomingSelect($select, array('action' => 'onlyUpcoming', 'orderby' => isset($params['popularity']) ? $params['popularity'] : 'starttime'));
        } else if ((isset($params['showEventType']) && ($params['showEventType'] == 'upcoming' || $params['showEventType'] == 'onlyUpcoming' || $params['showEventType'] == 'past'))) {
            $isEventOccuranceTableJoin = true;
            $select = $select->setIntegrityCheck(false);
            $select = $this->getSiteeventsUpcomingSelect($select, array('action' => $params['showEventType'], 'orderby' => isset($params['popularity']) ? $params['popularity'] : ''));
        } else if (isset($params['showEventType']) && $params['showEventType'] == 'all') {
            $isEventOccuranceTableJoin = true;
            $select = $select->setIntegrityCheck(false);
            $select = $this->getSiteeventsAllSelect($select, array('action' => 'all', 'orderby' => isset($params['popularity']) ? $params['popularity'] : ''));
        }

        if (isset($params['event_id']) && !empty($params['event_id'])) {
            $select->where($siteeventTableName . '.event_id != ?', $params['event_id']);
        }

        if (isset($params['category_id']) && !empty($params['category_id'])) {
            $select->where($siteeventTableName . '.category_id = ?', $params['category_id']);
        }

        if (isset($params['subcategory_id']) && !empty($params['subcategory_id'])) {
            $select->where($siteeventTableName . '.subcategory_id = ?', $params['subcategory_id']);
        }

        if (isset($params['subsubcategory_id']) && !empty($params['subsubcategory_id'])) {
            $select->where($siteeventTableName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
        }

        if (isset($params['popularity']) && !empty($params['popularity'])) {
            $select->order($params['popularity'] . " DESC");
        }

        if (isset($params['featured']) && !empty($params['featured']) || $siteeventCondition == 'featured') {
            $select->where("$siteeventTableName.featured = ?", 1);
        }

        if (isset($params['sponsored']) && !empty($params['sponsored']) || $siteeventCondition == 'sponsored') {
            $select->where($siteeventTableName . '.sponsored = ?', '1');
        }

        if (isset($params['newlabel']) && !empty($params['newlabel']) || $siteeventCondition == 'newlabel') {
            $select->where($siteeventTableName . '.newlabel = ?', '1');
        }
        
        if(isset($params['sponsored_or_featured']) && !empty($params['sponsored_or_featured'])) {
            $select->where("$siteeventTableName.sponsored = 1 OR $siteeventTableName.featured = 1");
        }

        if (isset($params['similarItems']) && !empty($params['similarItems'])) {
            $select->where("$siteeventTableName.event_id IN (?)", (array) $params['similarItems']);
        }

        //Start Network work
        $select = $this->getNetworkBaseSql($select, array('not_groupBy' => 1));
        //End Network work

        if ($siteeventCondition == 'most_popular') {
            $select = $select->order($siteeventTableName . '.view_count DESC');
        }

        if ($siteeventCondition == 'most_joined') {
            $select = $select->order($siteeventTableName . '.member_count DESC');
        }

        if ($siteeventCondition == 'most_reviews' || $siteeventCondition == 'most_reviewed') {
            $select = $select->order($siteeventTableName . '.review_count DESC');
        }

        if (isset($params['event_occurrences']) && !empty($params['event_occurrences'])) {
            $params['action'] = 'manage';
            $params['rsvp'] = 3;
            $siteeventTableName = $this->info('name');
            $SiteEventOccuretable = Engine_Api::_()->getDbTable('occurrences', 'siteevent');
            $siteeventOccurTableName = $SiteEventOccuretable->info('name');
            $getEventOccurrencesInfo = $this->getEventOccurrencesInfo($params);

            if (empty($getEventOccurrencesInfo)) {
                $select->setIntegrityCheck(false)->join($siteeventOccurTableName, "$siteeventTableName.event_id = $siteeventOccurTableName.event_id", array('starttime', 'endtime', 'occurrence_id'));
                $select->where($siteeventTableName . '.event_id != ?', $params['event_id']);
            }
        }

        if ($siteeventCondition == 'this_month' || $siteeventCondition == 'this_week' || $siteeventCondition == 'this_weekend' || $siteeventCondition == 'today') {

            if (empty($isEventOccuranceTableJoin)) {
                $siteeventTableName = $this->info('name');
                $SiteEventOccuretable = Engine_Api::_()->getDbTable('occurrences', 'siteevent');
                $siteeventOccurTableName = $SiteEventOccuretable->info('name');

                $select->setIntegrityCheck(false)->join($siteeventOccurTableName, "$siteeventTableName.event_id = $siteeventOccurTableName.event_id", array('starttime', 'endtime', 'occurrence_id'));
            }

            if ($siteeventCondition == 'this_month') {
                $select->where("(YEAR(starttime) <= YEAR(NOW()) AND (MONTH(starttime) <= MONTH(NOW()) OR (YEAR(starttime) > YEAR(NOW())))) AND ((MONTH(endtime) >= MONTH(NOW()) OR YEAR(endtime) > YEAR(NOW())) AND YEAR(endtime) >= YEAR(NOW()))");
//                $select->where("(YEAR(endtime) = YEAR(NOW()) AND MONTH(endtime) = MONTH(NOW())) OR (YEAR(starttime) = YEAR(NOW()) AND MONTH(starttime) = MONTH(NOW())) OR (DATE(starttime) <= DATE(NOW()) AND DATE(endtime) >= DATE(NOW()))");
            }

            if ($siteeventCondition == 'this_week') {
                $select = $select->where("(YEARWEEK(endtime) = YEARWEEK(CURRENT_DATE)) OR (YEARWEEK(starttime) = YEARWEEK(CURRENT_DATE)) OR (DATE(starttime) <= DATE(NOW()) AND DATE(endtime) >= DATE(NOW()))");
            }

            if ($siteeventCondition == 'this_weekend') {
                $select = $select->where("DATE(starttime) <= DATE(DATE_ADD(NOW(), INTERVAL(5 - WEEKDAY(NOW()))DAY)) AND DATE(endtime) >= DATE(DATE_ADD(NOW(), INTERVAL(5 - WEEKDAY(NOW())) DAY)) OR DATE(starttime) <= DATE(DATE_ADD(NOW(), INTERVAL(6 - WEEKDAY(NOW())) DAY)) AND DATE(endtime) >= DATE(DATE_ADD(NOW(), INTERVAL(6 - WEEKDAY(NOW())) DAY))");
            }

             if ($siteeventCondition == 'today') {
                $dateinfo = Engine_Api::_()->siteevent()->dbToUserDateTime(array('starttime' => date('Y-m-d H:i:s', time())), 'time');
                $starttime = date('Y-m-d', $dateinfo['starttime']) . ' 00:00:00';
                $endtime = date('Y-m-d', $dateinfo['starttime']) . ' 23:59:59';
                $dateinfo = Engine_Api::_()->siteevent()->userToDbDateTime(array('starttime' => $starttime, 'endtime' => $endtime));
                $select = $select->where("starttime <= '" . $dateinfo['endtime'] . "' AND starttime >= '". $dateinfo['starttime'] . "'");

            }
            $select = $select->order('starttime');
        }

        if (isset($params['eventType']) && !empty($params['eventType']) && $params['eventType'] != 'All') {
            if (strpos($params['eventType'], "sitereview_listing") !== false) {
                $explodedArray = explode("_", $params['eventType']);
                $listingTable = Engine_Api::_()->getDbtable('listings', 'sitereview');
                $listingTableName = $listingTable->info('name');
                $select->join($listingTableName, $listingTableName . '.listing_id = ' . $siteeventTableName . '.parent_id', array(""));
                $select->where($siteeventTableName . ".parent_type =?", 'sitereview_listing')
                        ->where($listingTableName . ".listingtype_id =?", $explodedArray[2]);
            } else {
                $select->where($siteeventTableName . ".parent_type =?", $params['eventType']);
            }
        }

        if (!isset($params['sql']))
            $select->order($siteeventTableName . '.event_id DESC');

        if (isset($params['limit']) && !empty($params['limit'])) {
            $limit = $params['limit'];
        }

        if (!isset($params['sql']))
            $select->group($siteeventTableName . '.event_id');

//        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
//            return Zend_Paginator::factory($select);
//        }

        if (isset($params['user_id']) && !empty($params['user_id']) && is_numeric($params['user_id']) && !isset($params['ismanage'])) {
            $select->where('owner_id = ?', $params['user_id']);
        }

        if (isset($params['start_index']) && $params['start_index'] >= 0) {
            $select = $select->limit($limit, $params['start_index']);
            return $this->fetchAll($select);
        } elseif (isset($params['sql']) && ($params['sql'] == 'count' || $params['sql'] == 'mycalendarlist')) {
            return $select->query()->fetchAll();
        } else {
            $paginator = Zend_Paginator::factory($select);
            if (!empty($params['page'])) {
                $paginator->setCurrentPageNumber($params['page']);
            }

            if (!empty($params['limit'])) {
                $paginator->setItemCountPerPage($limit);
            }

            return $paginator;
        }
    }

    //GET DISCUSSED EVENTS
    public function getDiscussedEvent($params = array()) {

        //GET SITEEVENT TABLE NAME
        $siteeventTableName = $this->info('name');

        //GET TOPIC TABLE
        $topictable = Engine_Api::_()->getDbTable('topics', 'siteevent');
        $topic_tableName = $topictable->info('name');

        //MAKE QUERY
        $select = $this->select()->setIntegrityCheck(false)
                ->from($siteeventTableName, array('event_id', 'title', 'photo_id', 'owner_id', 'category_id', 'subcategory_id', 'subsubcategory_id', 'featured', 'sponsored', 'newlabel', 'rating_avg', 'rating_users', 'rating_editor', 'venue_name', 'location', 'price', "view_count", "comment_count", "member_count", "like_count", "review_count", "host_type", 'host_id', "is_online", "repeat_params", 'parent_id', 'parent_type'))
                ->join($topic_tableName, $topic_tableName . '.event_id = ' . $siteeventTableName . '.event_id', array('count(*) as counttopics', '(sum(post_count) - count(*) ) as total_count'))
                ->where($siteeventTableName . '.closed = ?', '0')
                ->where($siteeventTableName . '.approved = ?', '1')
                ->where($siteeventTableName . '.draft = ?', '0')
                ->where($siteeventTableName . ".search = ?", 1)
                ->where($topic_tableName . '.post_count > ?', '1')
                ->group($topic_tableName . '.event_id')
                ->order('total_count DESC')
                ->order('counttopics DESC')
                ->limit($params['limit']);

        if (isset($params['detactLocation']) && $params['detactLocation'] && isset($params['latitude']) && $params['latitude'] && isset($params['longitude']) && $params['longitude'] && isset($params['defaultLocationDistance']) && $params['defaultLocationDistance']) {
            $locationsTable = Engine_Api::_()->getDbtable('locations', 'siteevent');
            $locationTableName = $locationsTable->info('name');
            $radius = $params['defaultLocationDistance']; //in miles
            $latitude = $params['latitude'];
            $longitude = $params['longitude'];
            $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.proximity.search.kilometer', 0);
            if (!empty($flage)) {
                $radius = $radius * (0.621371192);
            }
           // $latitudeRadians = deg2rad($latitude);
        $latitudeSin = "sin(radians($latitude))";
        $latitudeCos = "cos(radians($latitude))";

            $select->join($locationTableName, "$siteeventTableName.event_id = $locationTableName.event_id", array("(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172) AS distance", $locationTableName . '.location AS locationName'));
            $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationTableName.latitude)) + $latitudeCos * cos(radians($locationTableName.latitude)) * cos(radians($longitude - $locationTableName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
            $sqlstring .= ")";
            $select->where($sqlstring);
            //$select->order("distance");
            //$select->group("$siteeventTableName.event_id");
        }

        if (isset($params['showEventType']) && ($params['showEventType'] == 'upcoming' || $params['showEventType'] == 'onlyUpcoming' || $params['showEventType'] == 'past')) {
            $params['action'] = $params['showEventType'];
            $select = $this->getSiteeventsUpcomingSelect($select, $params);
        } else if (isset($params['showEventType']) && $params['showEventType'] == 'all') {
            $select = $this->getSiteeventsAllSelect($select, array('action' => 'all'));
        }

        if (isset($params['featured']) && !empty($params['featured'])) {
            $select->where($siteeventTableName . '.featured = ?', 1);
        }
        
        if(isset($params['sponsored_or_featured']) && !empty($params['sponsored_or_featured'])) {
            $select->where("$siteeventTableName.sponsored = 1 OR $siteeventTableName.featured = 1");
        }        

        if (isset($params['category_id']) && (!empty($params['category_id']) && $params['category_id'] != -1)) {
            $select->where($siteeventTableName . '.category_id = ?', $params['category_id']);
        }

        if (isset($params['subcategory_id']) && (!empty($params['subcategory_id']) && $params['subcategory_id'] != -1)) {
            $select->where($siteeventTableName . '.subcategory_id = ?', $params['subcategory_id']);
        }

        if (isset($params['subsubcategory_id']) && (!empty($params['subsubcategory_id']) && $params['subsubcategory_id'] != -1)) {
            $select->where($siteeventTableName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
        }

        if (isset($params['eventType']) && !empty($params['eventType']) && $params['eventType'] != 'All') {
            if (strpos($params['eventType'], "sitereview_listing") !== false) {
                $explodedArray = explode("_", $params['eventType']);
                $listingTable = Engine_Api::_()->getDbtable('listings', 'sitereview');
                $listingTableName = $listingTable->info('name');
                $select->join($listingTableName, $listingTableName . '.listing_id = ' . $siteeventTableName . '.parent_id', array(""));
                $select->where($siteeventTableName . ".parent_type =?", 'sitereview_listing')
                        ->where($listingTableName . ".listingtype_id =?", $explodedArray[2]);
            } else {
                $select->where($siteeventTableName . ".parent_type =?", $params['eventType']);
            }
        }

        //START NETWORK WORK
        $select = $this->getNetworkBaseSql($select, array('not_groupBy' => 1));
        //END NETWORK WORK
        //FETCH RESULTS
        return $this->fetchAll($select);
    }

    // get siteevent siteevent relative to siteevent owner
    public function userEvent($params = array()) {

        //GET SITEEVENT TABLE NAME
        $siteeventTableName = $this->info('name');

        //MAKE QUERY
        $select = $this->select()
                ->from($siteeventTableName, array("event_id", "title", "category_id", "subcategory_id", "subsubcategory_id", "view_count", "comment_count", "member_count", "like_count", "rating_avg", "rating_editor", "rating_users", "review_count", "owner_id", "photo_id", "price", 'featured', 'sponsored', 'newlabel', 'venue_name', 'location', 'host_type', 'host_id', 'is_online', 'repeat_params', 'parent_id', 'parent_type'))
                ->where($siteeventTableName . '.closed = ?', '0')
                ->where($siteeventTableName . '.approved = ?', '1')
                ->where($siteeventTableName . '.draft = ?', '0')
                ->where($siteeventTableName . ".search = ?", 1);

        if (isset($params['event_id']) && !empty($params['event_id'])) {
            $select->where($siteeventTableName . '.event_id <> ?', $params['event_id']);
        }

        if (isset($params['owner_id']) && !empty($params['owner_id'])) {
            $select->where($siteeventTableName . '.owner_id = ?', $params['owner_id']);
        }

        if (isset($params['host_type']) && !empty($params['host_type'])) {
            $select->where($siteeventTableName . '.host_type = ?', $params['host_type']);
            if (isset($params['host_id']) && !empty($params['host_id'])) {
                $select->where($siteeventTableName . '.host_id = ?', $params['host_id'])
                       ->where($siteeventTableName.'.approved = ?', 1);
            }
            if (isset($params['host_ids']) && !empty($params['host_ids'])) {
                $select->where($siteeventTableName . '.host_id IN(?)', (array) $params['host_ids'])
                       ->where($siteeventTableName.'.approved = ?', 1);
            }
        }

        if (isset($params['category_id']) && (!empty($params['category_id']) && $params['category_id'] != -1)) {
            $select->where($siteeventTableName . '.category_id = ?', $params['category_id']);
        }

        if (isset($params['subcategory_id']) && (!empty($params['subcategory_id']) && $params['subcategory_id'] != -1)) {
            $select->where($siteeventTableName . '.subcategory_id = ?', $params['subcategory_id']);
        }

        if (isset($params['subsubcategory_id']) && (!empty($params['subsubcategory_id']) && $params['subsubcategory_id'] != -1)) {
            $select->where($siteeventTableName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
        }
        $select->setIntegrityCheck(false);
        if (isset($params['showEventType']) && ($params['showEventType'] == 'upcoming' || $params['showEventType'] == 'onlyUpcoming' || $params['showEventType'] == 'past')) {
            $select = $this->getSiteeventsUpcomingSelect($select, array('action' => $params['showEventType']));
        } else if (isset($params['showEventType']) && $params['showEventType'] == 'all') {
            $select = $this->getSiteeventsAllSelect($select, array('action' => 'all'));
        }

        if (isset($params['eventType']) && !empty($params['eventType']) && $params['eventType'] != 'All') {
            if (strpos($params['eventType'], "sitereview_listing") !== false) {
                $explodedArray = explode("_", $params['eventType']);
                $listingTable = Engine_Api::_()->getDbtable('listings', 'sitereview');
                $listingTableName = $listingTable->info('name');
                $select->join($listingTableName, $listingTableName . '.listing_id = ' . $siteeventTableName . '.parent_id', array(""));
                $select->where($siteeventTableName . ".parent_type =?", 'sitereview_listing')
                        ->where($listingTableName . ".listingtype_id =?", $explodedArray[2]);
            } else {
                $select->where($siteeventTableName . ".parent_type =?", $params['eventType']);
            }
        }

        if (isset($params['count']) && !empty($params['count'])) {
            $select->limit($params['count']);
        }

        //Start Network work
				if(isset($params['networkBased'])) {
					$select = $this->getNetworkBaseSql($select, array('networkBased' => $params['networkBased']));
        } else {
				  $select = $this->getNetworkBaseSql($select);
				}
        //End Network work

        return $this->fetchAll($select);
    }

    public function getEventOccurrencesInfo($params = array()) {
        if (empty($params))
            return;

        $eventType = $params['action'];
        $tempEventsViewCount = 2651;
        $tempOccurrencesViewCounts = 1872;
        $getEventFlag = $getEventStr = null;
        $siteeventTableName = $this->info('name');
        $eventAttemptBy = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
        $getInfoArray = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.getinfo.type', false);
        $getItemTypeInfo = (string) Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.itemtype.info', false);
        $getAttribName = (string) Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.attribs.name', false);
        $siteeventShowViewTypeSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting($getAttribName . '.getshow.viewtype', false);
        $getPositionType = (string) Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.getposition.type', false);

        $getInfoArray = @unserialize($getInfoArray);
        $getPositionType = @unserialize($getPositionType);
        $SiteEventOccuretable = Engine_Api::_()->getDbTable('occurrences', 'siteevent');
        $siteeventOccurTableName = $SiteEventOccuretable->info('name');

        $coreTable = Engine_Api::_()->getDbTable('likes', 'core');
        $coreTableName = $coreTable->info('name');

        if (!empty($eventAttemptBy))
            $getFlagStr = $eventAttemptBy . $getAttribName;

        for ($flagNum = 0; $flagNum < strlen($getFlagStr); $flagNum++) {
            $getEventFlag += ord($getFlagStr[$flagNum]);
        }
        $upcomingSelectOrder = false;
        if ($eventType == 'manage') {
            $tempOccurencesFlagValue = 892623;
            $tempOccurrencesCount = 183031251;
            $SiteEventMembershiptable = Engine_Api::_()->getDbTable('membership', 'siteevent');
            $siteeventMembershipTableName = $SiteEventMembershiptable->info('name');
            $user_id = $params['user_id'];
            if ($params['rsvp'] == -1) {
                $select->joinLeft($siteeventMembershipTableName, "$siteeventOccurTableName.occurrence_id = $siteeventMembershipTableName.occurrence_id", array('rsvp', 'user_id AS membership_userid'));
                $where_Liked = "OR ($coreTableName.poster_id = $user_id AND $coreTableName.resource_type = 'siteevent_event' )";
                $where_Host = "OR (($siteeventTableName.host_id = $user_id AND $siteeventTableName.host_type ='user' AND $siteeventTableName.approved = 1 AND $siteeventMembershipTableName.user_id = $user_id) OR ($siteeventTableName.host_id = $user_id AND $siteeventTableName.host_type ='user' AND $siteeventTableName.approved = 1))";
                $where_Joined = "OR ($siteeventMembershipTableName.user_id = $user_id AND $siteeventMembershipTableName.resource_approved = 1 AND $siteeventMembershipTableName.active = 1)";
                //get the list of events which the user lead events.
                $eventsUserLeads = Engine_Api::_()->getItemTable('siteevent_list')->getEventsUserLead($user_id);
                if (!empty($eventsUserLeads))
                    $where_LeadOwner = "($siteeventTableName.owner_id = $user_id OR `{$siteeventTableName}`.`event_id`  IN ($eventsUserLeads))";
                else
                    $where_LeadOwner = "$siteeventTableName.owner_id = $user_id";

                //CHECK IF THE EVENT LIST PAGE IS USER PROFIE PAGE OR USER MY EVENT PAGE.
                if (isset($params['events_type']) && $params['events_type'] == 'user_profile') {

                    if (in_array('liked', $params['eventFilterTypes']))
                        $select->joinLeft($coreTableName, "$siteeventTableName.event_id = $coreTableName.resource_id", null);
                    else
                        $where_Liked = '';

                    if (!in_array('host', $params['eventFilterTypes']))
                        $where_Host = '';
                    if (!in_array('host', $params['eventFilterTypes']))
                        $where_Host = '';

                    if (!in_array('joined', $params['eventFilterTypes']))
                        $where_Joined = '';
                    if (!in_array('ledOwner', $params['eventFilterTypes']))
                        $where_LeadOwner = '';
                }
                else
                    $select->joinLeft($coreTableName, "$siteeventTableName.event_id = $coreTableName.resource_id", null);

                $where = "$where_LeadOwner $where_Joined $where_Host $where_Liked";
                //IF "OR" IS COMING AT FIRST PLACE THEN REMOVE THAT
                $pos = strpos($where, "OR");
                if ($pos === 1) {
                    $COUNT = 1;
                    $where = preg_replace("/OR/", '', $where, (int) $COUNT);
                }
            } elseif ($params['rsvp'] == -2) { //GETTING THE ONLY EVENTS WHICH LOGGED IN USER HOSTING.
                $select->joinLeft($siteeventMembershipTableName, "$siteeventOccurTableName.occurrence_id = $siteeventMembershipTableName.occurrence_id", array('rsvp', 'user_id AS membership_userid'));
                $where = "(($siteeventTableName.host_id = $user_id AND $siteeventTableName.host_type ='user' AND $siteeventTableName.approved = 1 AND $siteeventMembershipTableName.user_id = $user_id) OR ($siteeventTableName.host_id = $user_id AND $siteeventTableName.host_type ='user' AND $siteeventTableName.approved = 1))";
            } elseif ($params['rsvp'] == 2 || $params['rsvp'] == 1 || $params['rsvp'] == 0) { //GETTING THE ONLY EVENTS WHICH LOGGED IN USER ATTENDING, MAYBE ATTENDING, NOT ATTENDING.
                $select->join($siteeventMembershipTableName, "$siteeventOccurTableName.occurrence_id = $siteeventMembershipTableName.occurrence_id", array('rsvp', 'user_id AS membership_userid'));
                $rsvp = $params['rsvp'];
                $where = "$siteeventMembershipTableName.rsvp = $rsvp AND $siteeventMembershipTableName.user_id = $user_id AND $siteeventMembershipTableName.resource_approved = 1 AND $siteeventMembershipTableName.active = 1";
            } elseif ($params['rsvp'] == -3) { //GETTING THE ONLY EVENTS WHICH LOGGED IN USER LIKED
                $select->joinLeft($siteeventMembershipTableName, "$siteeventOccurTableName.occurrence_id = $siteeventMembershipTableName.occurrence_id", array('rsvp', 'user_id AS membership_userid'));
                $select->join($coreTableName, "$siteeventTableName.event_id = $coreTableName.resource_id", null);
                $rsvp = $params['rsvp'];
                $where = "$coreTableName.poster_id = $user_id AND $coreTableName.resource_type = 'siteevent_event' ";
            } elseif ($params['rsvp'] == -4) { //GETTING THE ONLY EVENTS WHICH LOGGED IN USER Leading
                $select->joinLeft($siteeventMembershipTableName, "$siteeventOccurTableName.occurrence_id = $siteeventMembershipTableName.occurrence_id", array('rsvp', 'user_id AS membership_userid'));
                //get the list of events which the user lead events.
                $eventsUserLeads = Engine_Api::_()->getItemTable('siteevent_list')->getEventsUserLead($user_id);
                if (!empty($eventsUserLeads))
                    $where = "($siteeventTableName.owner_id = $user_id OR `{$siteeventTableName}`.`event_id`  IN ($eventsUserLeads))";
                else
                    $where = "$siteeventTableName.owner_id = $user_id";
            }else if ($params['rsvp'] == 3) {
                $select = 1;
                $getEventFlag = (int) $getEventFlag;
                $siteeventOccurrenceEmailViewType = Engine_Api::_()->siteevent()->isEnabled();
                $getEventFlag = $getEventFlag * ($tempEventsViewCount + $tempOccurrencesViewCounts);
                $getEventFlag = $getEventFlag + ($tempOccurrencesCount + $tempOccurencesFlagValue);
                $getEventKeyStr = (string) $getEventFlag;
                foreach ($getInfoArray as $value) {
                    $getEventStr .= $getItemTypeInfo[$value];
                }

                if (empty($siteeventShowViewTypeSettings) && !empty($siteeventOccurrenceEmailViewType)) {
                    if (!strstr($getEventKeyStr, $getEventStr)) {
                        foreach ($getPositionType as $value) {
                            Engine_Api::_()->getApi('settings', 'core')->setSetting($value, 0);
                        }
                        return false;
                    }
                }
                return true;
            } else {
                $select->where("$where");
                if (isset($params['viewtype']) && ($params['viewtype'] == 'upcoming' || $params['viewtype'] == 'onlyUpcoming')) {
                    $upcomingSelectOrder = true;
                    $select->order("$siteeventOccurTableName.starttime ASC");
                }
            }
        }
    }

    /**
     * Handle archive siteevent
     * @param array $results : document owner archive siteevent array
     * @return siteevent with detail.
     */
    public function getArchiveSiteevent($spec) {
        if (!($spec instanceof User_Model_User)) {
            return null;
        }

        $localeObject = Zend_Registry::get('Locale');
        if (!$localeObject) {
            $localeObject = new Zend_Locale();
        }

        $dates = $this->select()
                ->from($this->info('name'), 'creation_date')
                ->where('owner_id = ?', $spec->getIdentity())
                ->where('closed = ?', '0')
                ->where('approved = ?', '1')
                ->where('draft = ?', '0')
                ->where("search = ?", 1)
                ->order('event_id DESC');

        $dates = $dates
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);

        $time = time();

        $archive_siteevent = array();
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

            if (!isset($archive_siteevent[$date_start])) {
                $archive_siteevent[$date_start] = array(
                    'type' => $type,
                    'label' => $label,
                    'date' => $date,
                    'date_start' => $date_start,
                    'date_end' => $date_end,
                    'count' => 1
                );
            } else {
                $archive_siteevent[$date_start]['count']++;
            }
        }

        return $archive_siteevent;
    }

//    /**
//     * Get events 
//     *
//     * @param int $id
//     * @param string $column_name
//     * @param int $authorization
//     * @return event count
//     */
//    public function getEvents($params = array()) {
//
//        //MAKE QUERY
//        $select = $this->select()
//                // ->from($this->info('name'), array('COUNT(*) AS count'))
//                ->where('closed = ?', 0)
//                ->where('approved = ?', 1)
//                ->where('draft = ?', 0)
//                ->where('search = ?', 1);
//
//        if (isset($params['list_ids']) && !empty($params['list_ids'])) {
//            $select->where('event_id  IN(?)', (array) $params['list_ids']);
//        }
//        if (isset($params['category_id']) && !empty($params['category_id'])) {
//            $select->where('category_id  = ?', $params['category_id']);
//        }
//
//        return $this->fetchAll($select);
//    }

    /**
     * Get FAQs
     *
     * @param int $category_id
     * @param string $column_name
     * @param int $authorization
     * @param int $no_subcategory
     * @return FAQs
     */
    public function getLists($category_id, $column_name, $authorization, $no_subcategory, $limit, $faq_limit, $count_only) {

        //GET FAQ TABLE NAME
        $tableEventName = $this->info('name');

        //RETURN IF ID IS EMPTY
        if (empty($column_name) || empty($category_id)) {
            return;
        }

        //MAKE QUERY
        $select = $this->select();

        if (empty($count_only)) {
            $select = $select->from($tableEventName, array('event_id', 'title', 'category_id', 'subcategory_id', 'subsubcategory_id'));
        } else {
            $select = $select->from($tableEventName, array('COUNT(*) AS count'));
        }

        $select->where("$column_name = ?", $category_id);

        if (!empty($no_subcategory)) {

            //MAKE QUERY
            $categoryTable = Engine_Api::_()->getDbtable('categories', 'siteevent');
            $categoryTableName = $categoryTable->info('name');
            $selectSubcategories = $categoryTable->select()
                    ->from($categoryTableName, array('category_id'))
                    ->where('cat_dependency = ?', $category_id);
            $subcategories = $selectSubcategories->query()->fetchAll(Zend_Db::FETCH_COLUMN);
            if (Count($subcategories) > 0) {
                $str_arr = array();
                foreach ($subcategories as $value) {
                    $select = $select->where("subcategory_id  != ?", $value);
                }
            }
            $select = $select->where("subcategory_id = ?", 0);
        }

        //AUTHORIZATION CHECK
        if (!empty($authorization)) {
            $select = $select->where('closed = ?', 0)
                    ->where('approved = ?', 1)
                    ->where('draft = ?', 0)
                    ->where('search = ?', 1);
        }

        //LIMIT CHECK
        if (!empty($faq_limit)) {
            $select = $select->limit($faq_limit);
        }

        if (empty($count_only)) {
            $select = $select->order("$tableEventName.event_id DESC");
            return $this->fetchAll($select);
        } else {
            return $select->query()->fetchColumn();
        }
    }

    public function getSimilarItems($params = NULL) {

        $siteeventTableName = $this->info('name');
        $select = $this->select()
                ->from($siteeventTableName, array('event_id', 'photo_id', 'title'));

        if (isset($params['textSearch']) && !empty($params['textSearch'])) {
            $select->where("$siteeventTableName.title LIKE ? ", "%" . $params['textSearch'] . "%");
        }

        if (isset($params['event_id']) && !empty($params['event_id'])) {
            $select->where($siteeventTableName . '.event_id != ?', $params['event_id']);
        }

        if (isset($params['category_id']) && !empty($params['category_id']) && $params['category_id'] != -1) {
            $select->where($siteeventTableName . '.category_id = ?', $params['category_id']);
        }

        if (isset($params['subcategory_id']) && !empty($params['subcategory_id']) && $params['subcategory_id'] != -1) {
            $select->where($siteeventTableName . '.subcategory_id = ?', $params['subcategory_id']);
        }

        if (isset($params['limit']) && !empty($params['limit'])) {
            $select->limit($params['limit']);
        }

        if (isset($params['eventIds']) && !empty($params['eventIds'])) {
            $eventIds = join(',', $params['eventIds']);
            $select->where("event_id IN ($eventIds)");
        }

        if (isset($params['notEventIds']) && !empty($params['notEventIds'])) {
            $notEventIds = join(',', $params['notEventIds']);
            $select->where("event_id NOT IN ($notEventIds)");
        }

        return Zend_Paginator::factory($select);
    }

    /**
     * Return events which have this category and this mapping
     *
     * @param int category_id
     * @return Zend_Db_Table_Select
     */
    public function getCategoryList($category_id, $categoryType) {

        //RETURN IF CATEGORY ID IS NULL
        if (empty($category_id)) {
            return null;
        }

        //MAKE QUERY
        $select = $this->select()
                ->from($this->info('name'), 'event_id')
                ->where("$categoryType = ?", $category_id);

        //GET DATA
        return $this->fetchAll($select);
    }

    /**
     * Return top reviewers
     *
     * @param Array $params
     * @return top reviewers
     */
    public function topPosters($params = array()) {

        //GET USER TABLE INFO
        $tableUser = Engine_Api::_()->getDbtable('users', 'user');
        $tableUserName = $tableUser->info('name');

        //GET REVIEW TABLE NAME
        $siteeventTableName = $this->info('name');

        //MAKE QUERY
        $select = $tableUser->select()
                ->setIntegrityCheck(false)
                ->from($tableUserName, array('user_id', 'displayname', 'username', 'photo_id'))
                ->join($siteeventTableName, "$tableUserName.user_id = $siteeventTableName.owner_id", array('COUNT(engine4_siteevent_events.event_id) AS event_count'));

        $select->where($siteeventTableName . '.draft = ?', 0)
                ->where($siteeventTableName . '.search = ?', 1)
                ->where($siteeventTableName . '.closed = ?', 0)
                ->where($siteeventTableName . '.approved = ?', 1)
                ->group($tableUserName . ".user_id")
                ->order('event_count DESC')
                ->order('user_id DESC')
                ->limit($params['limit']);


        return $tableUser->fetchAll($select);
    }

    /**
     * Return users lists whose pages can be claimed
     *
     * @param int $text
     * @param int $limit
     * @return user lists
     */
    public function getMembers($text, $limit = 40) {


        //GET USER TABLE
        $tableUser = Engine_Api::_()->getDbtable('users', 'user');

        //SELECT
        $selectUsers = $tableUser->select()
                ->from($tableUser->info('name'), array('user_id', 'username', 'displayname', 'photo_id'))
                ->where('displayname  LIKE ? OR username LIKE ?', '%' . $text . '%');

        $selectUsers->where('approved = ?', 1)
                ->where('verified = ?', 1)
                ->where('enabled = ?', 1)
                ->order('displayname ASC')
                ->limit($limit);

        //FETCH
        return $tableUser->fetchAll($selectUsers);
    }

    public function getHostsSuggest($type, $text, $limit = 40) {
        $table = Engine_Api::_()->getItemTable($type);
        $select = $table->select();
        if ($type == 'user') {

            $select->from($table->info('name'), array('user_id', 'username', 'displayname', 'photo_id'))
                    ->where('displayname  LIKE ? OR username LIKE ?', '%' . $text . '%')->where('approved = ?', 1)
                    ->where('verified = ?', 1)
                    ->where('enabled = ?', 1)
                    ->order('displayname ASC');
        } else if ($type == 'siteevent_organizer') {
            $select->from($table->info('name'), array('creator_id', 'title', 'organizer_id', 'photo_id'))
                    ->where('title LIKE ?', '%' . $text . '%')
                    ->order('title ASC');
            $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
            if ($viewer_id)
                $select->where('creator_id = ?', $viewer_id);
        } else {
            if ($type == 'sitebusiness_business')
                $select = $table->getBusinessesSelectSql();
            elseif ($type == 'sitepage_page')
                $select = $table->getPagesSelectSql();
            elseif ($type == 'sitegroup_group')
                $select = $table->getGroupsSelectSql();
            elseif ($type == 'sitestore_store')
                $select = $table->getStoresSelectSql();

            $select->where('`' . $table->info('name') . '`.`title` LIKE ?', '%' . $text . '%');

            $select->order("{$table->info('name')}.title ASC");
        }
        $select->limit($limit);
        //FETCH
        return $table->fetchAll($select);
    }

    public function getOwnerPastHost($user_id) {
        $select = $this->select()
                ->from($this->info('name'), array('host_type', 'host_id'))
                ->where('owner_id = ?', $user_id)
                ->order('event_id DESC');
        return $this->fetchRow($select);
    }

    public function getEditEvent($event_id) {
        $siteEvent = $this->getItem($event_id, 'edit');
        return $siteEvent;
    }

    public function getNextOccurID($event_id) {
        $siteeventTableName = $this->info('name');

        //MAKE QUERY
        $select_temp = $select = $this->select();

        $select_temp = $select = $select
                ->setIntegrityCheck(false)
                ->from($siteeventTableName, array());
        $SiteEventOccuretable = Engine_Api::_()->getDbTable('occurrences', 'siteevent');
        $siteeventOccurTableName = $SiteEventOccuretable->info('name');
        $select->join($siteeventOccurTableName, "$siteeventTableName.event_id = $siteeventOccurTableName.event_id", array('occurrence_id'));
        $select->where($siteeventTableName . '.event_id = ?', $event_id);

        $select->limit(1);
        $select_temp = clone $select;
        $select->order("$siteeventOccurTableName.endtime ASC");
        $select->where("$siteeventOccurTableName .endtime > NOW()");
        //->orhaving("MAX($siteeventOccurTableName.endtime) <= NOW()");

        $occurrence_id = $select->query()->fetchColumn();
        if (empty($occurrence_id)) {
            $select_temp->order("$siteeventOccurTableName.endtime DESC");
            $select_temp->where("$siteeventOccurTableName .endtime <= NOW()");
            $occurrence_id = $select_temp->query()->fetchColumn();
        }

        return $occurrence_id;
    }

    public function getItem($event_id, $action = 'manage', $occurrence_id = null) {

        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $siteeventTableName = $this->info('name');

        //MAKE QUERY
        $select = $this->select();

        $select = $select
                ->setIntegrityCheck(false)
                ->from($siteeventTableName);
        $SiteEventOccuretable = Engine_Api::_()->getDbTable('occurrences', 'siteevent');
        $siteeventOccurTableName = $SiteEventOccuretable->info('name');
        $select->join($siteeventOccurTableName, "$siteeventTableName.event_id = $siteeventOccurTableName.event_id", array('starttime', 'endtime', 'occurrence_id'));

        if ($action == 'manage' || $action == 'feedtooltip') {
            $SiteEventMembershiptable = Engine_Api::_()->getDbTable('membership', 'siteevent');
            $siteeventMembershipTableName = $SiteEventMembershiptable->info('name');
            $select->joinLeft($siteeventMembershipTableName, "$siteeventOccurTableName.occurrence_id = $siteeventMembershipTableName.occurrence_id AND $siteeventMembershipTableName.user_id = $viewer_id", array('rsvp', 'user_id AS membership_userid'));
        }
        if ($action == 'manage') {
            $where = "$siteeventTableName.owner_id = $viewer_id || $siteeventMembershipTableName.user_id = $viewer_id";
            $select->where("$where");
        }

        if (!empty($occurrence_id))
            $select->where($siteeventOccurTableName . '.occurrence_id = ?', $occurrence_id);
        $select->where($siteeventTableName . '.event_id = ?', $event_id);
        $select->order("$siteeventOccurTableName.starttime ASC");
        $select->limit(1);
        $siteEvent = $this->fetchRow($select);
        return $siteEvent;
    }

    /**
     * Return page is existing or not.
     *
     * @return Zend_Db_Table_Select
     */
    public function checkEvent() {

        //MAKE QUERY
        $hasEvent = $this->select()
                ->from($this->info('name'), array('event_id'))
                ->query()
                ->fetchColumn();

        //RETURN RESULTS
        return $hasEvent;
    }

    public function countTotalGuest($params = array()) {
        //MAKE QUERY
        $select = $this->select()
                ->from($this->info('name'), array('SUM(member_count)'));

        if (isset($params['host_type']) && !empty($params['host_type'])) {
            $select->where($this->info('name') . '.host_type = ?', $params['host_type']);
            if (isset($params['host_id']) && !empty($params['host_id'])) {
                $select->where($this->info('name') . '.host_id = ?', $params['host_id'])
                       ->where($this->info('name').'.approved = ?', 1);
            }
            if (isset($params['host_ids']) && !empty($params['host_ids'])) {
                $select->where($this->info('name') . '.host_id IN(?)', (array) $params['host_ids'])
                       ->where($this->info('name').'.approved = ?', 1);
            }
        }

        //RETURN RESULTS
        return $select->query()
                        ->fetchColumn();
    }

    public function avgTotalRating($params = array()) {
        //MAKE QUERY
        $select = $this->select()
                ->from($this->info('name'), array('AVG(rating_avg)'));

        if (isset($params['host_type']) && !empty($params['host_type'])) {
            $select->where($this->info('name') . '.host_type = ?', $params['host_type']);
            if (isset($params['host_id']) && !empty($params['host_id'])) {
                $select->where($this->info('name') . '.host_id = ?', $params['host_id'])
                       ->where($this->info('name').'.approved = ?', 1);
            }
            if (isset($params['host_ids']) && !empty($params['host_ids'])) {
                $select->where($this->info('name') . '.host_id IN(?)', (array) $params['host_ids'])
                       ->where($this->info('name').'.approved = ?', 1);
            }
        }

        if (isset($params['more_than'])) {
            $select->where($this->info('name') . '.rating_avg > ?', $params['more_than']);
        }
        //RETURN RESULTS
        return $select->query()
                        ->fetchColumn();
    }

    /**
     * Return Location Base EVENTS
     * 
     * @return EVENTS
     */
    public function getLocationBaseContents($params = array()) {

        if (empty($params['search']))
            return;
        $limit = 5;
        if (isset($params['limit']) && !empty($params['limit'])) {
            $limit = $params['limit'];
        } else {
            $limit = 5;
        }
        $select = $this->getEventsSelectSql(array("limit" => $limit));
        //Start Network work
        $select = $this->getNetworkBaseSql($select);
        //End Network work
        $eventName = $this->info('name');
        $locationTable = Engine_Api::_()->getDbtable('locations', 'siteevent');
        $locationName = $locationTable->info('name');
        $select
                ->setIntegrityCheck(false)
                ->from($eventName, array('title', 'event_id', 'location', 'photo_id', 'category_id'))
                ->join($locationName, "$eventName.event_id = $locationName.event_id", array("latitude", "longitude", "formatted_address"));

        if (isset($params['search']) && !empty($params['search'])) {
            $select->where("`{$eventName}`.title LIKE ? or `{$eventName}`.location LIKE ? or `{$locationName}`.city LIKE ?", "%" . $params['search'] . "%");
        }

        if (isset($params['resource_id']) && !empty($params['resource_id'])) {
            $select->where($locationName . '.event_id not in (?)', new Zend_Db_Expr(trim($params['resource_id'], ',')));
        }

        $select->order('creation_date DESC');

        return $this->fetchAll($select);
    }

    /* Return Location Base EVENTS
     * 
     * @return EVENTS
     */

    public function getPreviousLocationBaseContents($params = array()) {

        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $select = $this->getEventsSelectSql(array("limit" => 5));

        //START NETWORK WORK
        $select = $this->getNetworkBaseSql($select);
        //END NETWORK WORK
        //GET EVENT TABLE NAME
        $eventTableName = $this->info('name');

        //LOCATION TABLE NAME
        $locationTableName = Engine_Api::_()->getDbtable('locations', 'siteevent')->info('name');

        //GET ADD LOCATION TABLE NAME
        $addlocationsTableName = Engine_Api::_()->getDbtable('addlocations', 'sitetagcheckin')->info('name');

        $select = $select
                ->setIntegrityCheck(false)
                ->from($eventTableName, array('title', 'event_id', 'location', 'photo_id', 'category_id'))
                ->join($addlocationsTableName, "$addlocationsTableName.object_id = $eventTableName.event_id", null)
                ->join($locationTableName, "$locationTableName.location_id = $addlocationsTableName.location_id", array("latitude", "longitude", "formatted_address"))
                ->where("$addlocationsTableName.object_type =?", "siteevent_event")
                ->where("$addlocationsTableName.owner_id =?", $viewer_id)
                ->group("$addlocationsTableName.object_id")
                ->order("$eventTableName.creation_date DESC");

        return $this->fetchAll($select);
    }

    /* Return SELECT
     * 
     * @return SELECT
     */

    public function getEventsSelectSql($params = array()) {
        $tableName = $this->info('name');
        $select = $this->select()
                ->where("{$tableName}.search = ?", 1)
                ->where("{$tableName}.closed = ?", '0')
                ->where("{$tableName}.approved = ?", '1')
                ->where("{$tableName}.draft = ?", '0');

        if (isset($params['limit']) && !empty($params['limit']))
            $select->limit($params['limit']);

        return $select;
    }
    
    public function getAllEventSql($select = '', $params = array()) {
      
        $siteeventTableName = $this->info('name');
        $SiteEventOccuretable = Engine_Api::_()->getDbTable('occurrences', 'siteevent');
        $siteeventOccurTableName = $SiteEventOccuretable->info('name');

        $coreTable = Engine_Api::_()->getDbTable('likes', 'core');
        $coreTableName = $coreTable->info('name');

     // if ($eventType == 'manage') {
            $SiteEventMembershiptable = Engine_Api::_()->getDbTable('membership', 'siteevent');
            $siteeventMembershipTableName = $SiteEventMembershiptable->info('name');
            $user_id = 0;
            if(isset($params['user_id']))
              $user_id = $params['user_id'];
            if (isset($params['rsvp']) && $params['rsvp'] < 0 && (!isset($params['events_type']) || (isset($params['events_type']) && $params['rsvp'] != -3 && $params['events_type'] == 'user_profile') ))
                $select->joinLeft($siteeventMembershipTableName, "$siteeventOccurTableName.occurrence_id = $siteeventMembershipTableName.occurrence_id AND $siteeventMembershipTableName.user_id = $user_id", array('rsvp', 'user_id AS membership_userid'));
            if ($params['rsvp'] == -1) {
                //$where_Liked = "OR ($coreTableName.poster_id = $user_id AND $coreTableName.resource_type = 'siteevent_event' )";
                $where_Host = "OR (($siteeventTableName.host_id = $user_id AND $siteeventTableName.host_type ='user' AND $siteeventTableName.approved = 1 AND $siteeventMembershipTableName.user_id = $user_id) OR ($siteeventTableName.host_id = $user_id AND $siteeventTableName.host_type ='user' AND $siteeventTableName.approved = 1))";
                $where_Joined = "OR ($siteeventMembershipTableName.user_id = $user_id AND $siteeventMembershipTableName.resource_approved = 1 AND $siteeventMembershipTableName.active = 1)";
                //get the list of events which the user lead events.
                $eventsUserLeads = Engine_Api::_()->getItemTable('siteevent_list')->getEventsUserLead($user_id);
                if (!empty($eventsUserLeads))
                    $where_LeadOwner = "(($siteeventTableName.owner_id = $user_id AND ($siteeventMembershipTableName.user_id = $user_id AND $siteeventMembershipTableName.resource_approved = 1 AND $siteeventMembershipTableName.active = 1)) OR $siteeventTableName.owner_id = $user_id OR `{$siteeventTableName}`.`event_id`  IN ($eventsUserLeads))";
                else
                    $where_LeadOwner = "(($siteeventTableName.owner_id = $user_id AND ($siteeventMembershipTableName.user_id = $user_id AND $siteeventMembershipTableName.resource_approved = 1 AND $siteeventMembershipTableName.active = 1)) OR ($siteeventTableName.owner_id = $user_id))";

                //CHECK IF THE EVENT LIST PAGE IS USER PROFIE PAGE OR USER MY EVENT PAGE.
                if (isset($params['events_type']) && $params['events_type'] == 'user_profile') {

                    if (!in_array('host', $params['eventFilterTypes']))
                        $where_Host = '';
                    if (!in_array('host', $params['eventFilterTypes']))
                        $where_Host = '';

                    if (!in_array('joined', $params['eventFilterTypes']))
                        $where_Joined = '';
                    if (!in_array('ledOwner', $params['eventFilterTypes']))
                        $where_LeadOwner = '';
                }
               $where = "$where_LeadOwner $where_Joined $where_Host";
                //IF "OR" IS COMING AT FIRST PLACE THEN REMOVE THAT
                $pos = strpos($where, "OR");
                if ($pos === 1) {
                    $COUNT = 1;
                    $where = preg_replace("/OR/", '', $where, (int) $COUNT);
                }
            } elseif ($params['rsvp'] == -2) { //GETTING THE ONLY EVENTS WHICH LOGGED IN USER HOSTING.
                $where = "(($siteeventTableName.host_id = $user_id AND $siteeventTableName.host_type ='user' AND $siteeventTableName.approved = 1 AND $siteeventMembershipTableName.user_id = $user_id) OR ($siteeventTableName.host_id = $user_id AND $siteeventTableName.host_type ='user' AND $siteeventTableName.approved = 1))";
            } elseif ($params['rsvp'] == 2 || $params['rsvp'] == 1 || $params['rsvp'] == 0) { //GETTING THE ONLY EVENTS WHICH LOGGED IN USER ATTENDING, MAYBE ATTENDING, NOT ATTENDING.
                $select->join($siteeventMembershipTableName, "$siteeventOccurTableName.occurrence_id = $siteeventMembershipTableName.occurrence_id", array('rsvp', 'user_id AS membership_userid'));
                $rsvp = $params['rsvp'];
                $where = "$siteeventMembershipTableName.rsvp = $rsvp AND $siteeventMembershipTableName.user_id = $user_id AND $siteeventMembershipTableName.resource_approved = 1 AND $siteeventMembershipTableName.active = 1";
            } elseif ($params['rsvp'] == -3) { //GETTING THE ONLY EVENTS WHICH LOGGED IN USER LIKED        
                $select->join($coreTableName, "$siteeventTableName.event_id = $coreTableName.resource_id", null);
                $rsvp = $params['rsvp'];
                $where = "$coreTableName.poster_id = $user_id AND $coreTableName.resource_type = 'siteevent_event' ";
            } elseif ($params['rsvp'] == -4) { //GETTING THE ONLY EVENTS WHICH LOGGED IN USER Leading       
                //get the list of events which the user lead events.
                $eventsUserLeads = Engine_Api::_()->getItemTable('siteevent_list')->getEventsUserLead($user_id);
                if (!empty($eventsUserLeads))
                    $where = "($siteeventTableName.owner_id = $user_id OR `{$siteeventTableName}`.`event_id`  IN ($eventsUserLeads))";
                else
                    $where = "$siteeventTableName.owner_id = $user_id";
            } elseif ($params['rsvp'] == -5) { //GETTING THE ONLY EVENTS WHICH LOGGED IN USER RATED BY OTHER USER.
							$userreviewsTable = Engine_Api::_()->getDbtable('userreviews', 'siteevent');
							$userreviewsTableName = $userreviewsTable->info('name');
							$select->join($userreviewsTableName, $userreviewsTableName . '.event_id = ' . $siteeventTableName . '.event_id', null);
							$where = "$userreviewsTableName.user_id = $user_id";
            }

            $select->where("$where");
            if (isset($params['viewtype']) && ($params['viewtype'] == 'upcoming' || $params['viewtype'] == 'onlyUpcoming')) {
                $upcomingSelectOrder = true;
                $select->order("$siteeventOccurTableName.starttime ASC");
            }
       // }
      return $select;
    }
    
    /**
    * Get event attribute
    * @param int $event_id
    * 
    * @return event attribute
    */
   public function getEventAttribute($event_id, $attributName) {
     $select = $this->select()->from($this->info('name'), $attributName)->where('event_id = ?', $event_id);
     return $select->query()->fetchColumn();
   }
   
   public function getEventId($owner_id)
  {
    $select = $this->select()
            ->from($this->info('name'), 'event_id')
            ->where('owner_id = ?', $owner_id);
    return $select->query()->fetchAll();
  }
  
  public function getEventName($event_id)
  {
    $select = $this->select()
            ->from($this->info('name'), 'title')
            ->where('event_id = ?', $event_id);
    return $select->query()->fetchColumn();
  }

}