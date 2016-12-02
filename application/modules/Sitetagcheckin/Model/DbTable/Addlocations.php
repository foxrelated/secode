<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Addlocations.php 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitetagcheckin_Model_DbTable_Addlocations extends Engine_Db_Table {

  protected $_rowClass = 'Sitetagcheckin_Model_Addlocation';
  protected $_serializedColumns = array('params');

  /**
   * Return row 
   *
   * @param array $params
   */
  public function saveLocation($params = array()) {

    $select = $this->select()
            ->from($this->info('name'), array('*'));
    $current_date = date('Y-m-d');
    if (isset($params['params']['checkin']) &&  isset($params['params']['checkin']['type']) && ($params['params']['checkin']['type'] == 'place')) {
      $select = $select
              ->where('location_id =?', $params['location_id']);
      $this->update(array('current_checkin' => 0), array('type=?' => $params['type'], 'owner_id=?' => $params['owner_id'], 'location_id <>?' => $params['location_id'], 'event_date =?' => $current_date));
    } else {
      $this->update(array('current_checkin' => 0), array('type=?' => $params['type'], 'owner_id=?' => $params['owner_id'], 'object_type <>?' => $params['object_type'], 'event_date =?' => $current_date));
      $select = $select->where('object_type =?', $params['object_type'])
              ->where('object_id =?', $params['object_id']);
    }

    //INITIALISING PARENT ID
    $parent_id = 0;

    //CHECK THERE IS TAGGING OR CHECKIN
    if ($params['type'] == 'tagging') {
      //FECTH ROW
      $addLocationTableRow = $this->fetchRow($select);

      //PARAMS EMPTY THEN DELETE AND ADDLOCAITON ROW IS EXISTING
      if (empty($params['params']) && empty($addLocationTableRow)) {
        return;
      } else if (empty($params['params']) && !empty($addLocationTableRow)) {
        $addLocationTableRow->delete();
        return;
      } else if (!empty($params['params']) && empty($addLocationTableRow)) {
        $addLocationTableRow = $this->createRow();
      }
      $params['params'] = array('checkin' => $params['params']);
    } elseif ($params['type'] == 'checkin') {
      //GET CURRENT TIME STAMP
      $current_time_stamp = time();

      //GET CHECKIN TIME STAMP
      $checkin_timestamp = Engine_Api::_()->getApi('settings', 'core')->sitetagcheckin_max_status_time;

      //GET CHECKIN DATE
      $checkin_date = date('Y-m-d H:i:s', ($current_time_stamp - $checkin_timestamp));

      //SELECT
      $select->where('parent_id =?', $parent_id)->where('event_date =?', $params['event_date'])->where('owner_id =?', $params['owner_id'])->order('creation_date DESC');

      $current_date = date('Y-m-d');
      if ($current_date == $params['event_date']) {
        $select->where('current_checkin =?', 1)->where('creation_date >=?', $checkin_date);
      }

      //FECTH ROW
      $addLocationTableRow = $this->fetchRow($select);

      //SET PARENR ID
      if (!empty($addLocationTableRow)) {
        $parent_id = $addLocationTableRow->addlocation_id;
      }

      //CREATE ROW
      $addLocationTableRow = $this->createRow();
    }

    //SET ROW DATAS
    $addLocationTableRow->setFromArray($params);
    $addLocationTableRow->params = $params['params'];
    $addLocationTableRow->modified_date = date('Y-m-d H:i:s');
    $addLocationTableRow->parent_id = $parent_id;
    $addLocationTableRow->current_checkin = 1;
    if(isset($addLocationTableRow->sync_album)) {
			$addLocationTableRow->sync_album = 1;
    }
    $addLocation = $addLocationTableRow->save();

    //RETURN ROW
    return $addLocationTableRow;
  }

  /**
   * Return location_id 
   *
   * @param int $location
   */
  public function getLocationId($location, $contentProfile = null) {
    $addlocation = array();
    $addlocation['location'] = $location;
    $locationTable = Engine_Api::_()->getDbtable('locations', 'seaocore');
    $flag = $locationTable->hasLocation($addlocation);
    if (!empty($flag)) {
      $locationRow = $locationTable->getLocation($addlocation);
      $addlocation['location_id'] = $location_id = $locationRow->location_id;
      $addlocation['latitude'] = $locationRow->latitude;
      $addlocation['longitude'] = $locationRow->longitude;
      $addlocation['formatted_address'] = $locationRow->formatted_address;
      $addlocation['country'] = $locationRow->country;
      $addlocation['state'] = $locationRow->state;
      $addlocation['zipcode'] = $locationRow->zipcode;
      $addlocation['city'] = $locationRow->city;
      $addlocation['address'] = $locationRow->address;
      $addlocation['zoom'] = $locationRow->zoom;
    } else {
      $urladdress = urlencode($location);
      $delay = 0;

      //Iterate through the rows, geocoding each address
      $geocode_pending = true;
      while ($geocode_pending) {
        $language = $_COOKIE['en4_language'];
        $request_url = "https://maps.googleapis.com/maps/api/geocode/json?address=$urladdress";
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        ob_start();
        curl_exec($ch);
        curl_close($ch);
        $json_resopnse = Zend_Json::decode(ob_get_contents());
        ob_end_clean();
        $status = $json_resopnse['status'];
        if (strcmp($status, "OK") == 0) {
          //Successful geocode
          $geocode_pending = false;
          $result = $json_resopnse['results'];
          //Format: Longitude, Latitude, Altitude
          $latitude = $result[0]['geometry']['location']['lat'];
          $longitude = $result[0]['geometry']['location']['lng'];
          $formatted_address = $result[0]['formatted_address'];
          $len_add = count($result[0]['address_components']);
          $address = '';
          $country = '';
          $state = '';
          $zip_code = '';
          $city = '';
          for ($i = 0; $i < $len_add; $i++) {
            $types_location = $result[0]['address_components'][$i]['types'][0];

            if ($types_location == 'country') {
              $country = $result[0]['address_components'][$i]['long_name'];
            } else if ($types_location == 'administrative_area_level_1') {
              $state = $result[0]['address_components'][$i]['long_name'];
            } else if ($types_location == 'administrative_area_level_2') {
              $city = $result[0]['address_components'][$i]['long_name'];
            } else if ($types_location == 'postal_code' || $types_location == 'zip_code') {
              $zip_code = $result[0]['address_components'][$i]['long_name'];
            } else if ($types_location == 'street_address') {
              if ($address == '')
                $address = $result[0]['address_components'][$i]['long_name'];
              else
                $address = $address . ',' . $result[0]['address_components'][$i]['long_name'];
            } else if ($types_location == 'locality') {
              if ($address == '')
                $address = $result[0]['address_components'][$i]['long_name'];
              else
                $address = $address . ',' . $result[0]['address_components'][$i]['long_name'];
            }else if ($types_location == 'route') {
              if ($address == '')
                $address = $result[0]['address_components'][$i]['long_name'];
              else
                $address = $address . ',' . $result[0]['address_components'][$i]['long_name'];
            }else if ($types_location == 'sublocality') {
              if ($address == '')
                $address = $result[0]['address_components'][$i]['long_name'];
              else
                $address = $address . ',' . $result[0]['address_components'][$i]['long_name'];
            }
          }
          $addlocation['location'] = $location;
          $addlocation['latitude'] = $latitude;
          $addlocation['longitude'] = $longitude;
          $addlocation['formatted_address'] = $formatted_address;
          $addlocation['country'] = $country;
          $addlocation['state'] = $state;
          $addlocation['zipcode'] = $zip_code;
          $addlocation['city'] = $city;
          $addlocation['address'] = $address;
          $addlocation['zoom'] = 16;
        } else if (strcmp($status, "620") == 0) {
          //sent geocodes too fast
          $delay += 100000;
        } else {
          //failure to geocode
          $geocode_pending = false;
          echo "Address " . $location . " failed to geocoded. ";
          echo "Received status " . $status . "\n";
        }
        usleep($delay);
      }
      $location_id = $locationTable->setLocation($addlocation);
    }

    if ($contentProfile) {
      return $addlocation;
    }

    return $location_id;
  }

  /**
   * Return action_ids 
   *
   * @param array $about
   * @param array $user
   * @param array $params
   */
  public function getActivity($about, User_Model_User $user, array $params = array(), $content = null) {

    //PREPARE MAIN QUERY
    $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
    $db = $streamTable->getAdapter();
    $union = new Zend_Db_Select($db);

    //PREPARE ACTION TYPES
    $masterActionTypes = Engine_Api::_()->getDbtable('actionTypes', 'activity')->getActionTypes();
    if (empty($content)) {
      $masterActionTypes = $objectActionTypes = $subjectActionTypes = array('sitetagcheckin_content', 'sitetagcheckin_post_self', 'sitetagcheckin_status', 'sitetagcheckin_post', 'sitetagcheckin_location', 'sitetagcheckin_add_to_map', 'sitetagcheckin_checkin', 'sitetagcheckin_sbal_photo_new', 'sitetagcheckin_spal_photo_new', 'sitetagcheckin_album_photo_new', 'sitepage_post', 'sitebusiness_post', 'sitetagcheckin_lct_add_to_map', 'sitegroup_post', 'sitetagcheckin_sgal_photo_new', 'sitestore_post', 'sitetagcheckin_ssal_photo_new', 'siteevent_post', 'sitetagcheckin_seal_photo_new');
    } else {
      $masterActionTypes = $objectActionTypes = $subjectActionTypes = array('sitetagcheckin_content', 'sitetagcheckin_add_to_map', 'sitetagcheckin_lct_add_to_map');
    }

    //FILTER TYPES BASED ON USER REQUEST
    if (isset($showTypes) && is_array($showTypes) && !empty($showTypes)) {
      $subjectActionTypes = array_intersect($subjectActionTypes, $showTypes);
      $objectActionTypes = array_intersect($objectActionTypes, $showTypes);
    } else if (isset($hideTypes) && is_array($hideTypes) && !empty($hideTypes)) {
      $subjectActionTypes = array_diff($subjectActionTypes, $hideTypes);
      $objectActionTypes = array_diff($objectActionTypes, $hideTypes);
    }

    //NOTHING TO SHOW
    if (empty($subjectActionTypes) && empty($objectActionTypes)) {
      return null;
    }

    if (empty($subjectActionTypes)) {
      $subjectActionTypes = null;
    } else {
      $subjectActionTypes = "'" . join("', '", $subjectActionTypes) . "'";
    }

    if (empty($objectActionTypes)) {
      $objectActionTypes = null;
    } else {
      $objectActionTypes = "'" . join("', '", $objectActionTypes) . "'";
    }

    //PREPARE SUB QUERIES
    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('getActivity', array(
        'for' => $user,
        'about' => $about,
            ));
    $responses = (array) $event->getResponses();

    if (empty($responses)) {
      return null;
    }

    foreach ($responses as $response) {
      if (empty($response))
        continue;

      //TARGET INFO
      $select = $streamTable->select()
              ->from($streamTable->info('name'), 'action_id')
              ->where('target_type = ?', $response['type'])
      ;

      if (empty($response['data'])) {
        //SIMPLE
        $select->where('target_id = ?', 0);
      } else if (is_scalar($response['data']) || count($response['data']) === 1) {
        //SINGLE
        if (is_array($response['data'])) {
          list($response['data']) = $response['data'];
        }
        $select->where('target_id = ?', $response['data']);
      } else if (is_array($response['data'])) {
        //ARRAY
        $select->where('target_id IN(?)', (array) $response['data']);
      } else {
        //UNKNOWN
        continue;
      }

      //ADD ORDER/LIMIT
      $select
              ->order('action_id DESC');

      //ADD SUBJECT TO MAIN QUERY
      $selectSubject = clone $select;
      if ($subjectActionTypes !== null) {
        if ($subjectActionTypes !== true) {
          $selectSubject->where('type IN(' . $subjectActionTypes . ')');
        }
        $selectSubject
                ->where('subject_type = ?', $about->getType())
                ->where('subject_id = ?', $about->getIdentity());
        $union->union(array('(' . $selectSubject->__toString() . ')')); //(string) not work before PHP 5.2.0
      }

      //ADD OBJECT TO MAIN QUERY
      $selectObject = clone $select;
      if ($objectActionTypes !== null) {
        if ($objectActionTypes !== true) {
          $selectObject->where('type IN(' . $objectActionTypes . ')');
        }
        $selectObject
                ->where('object_type = ?', $about->getType())
                ->where('object_id = ?', $about->getIdentity());
        $union->union(array('(' . $selectObject->__toString() . ')')); //(string) not work before PHP 5.2.0
      }
    }

    //FINISH MAIN QUERY
    $union
            ->order('action_id DESC');

    //GET ACTIONS
    $actions = $db->fetchAll($union);

    //PROCESS IDS
    $ids = array();
    foreach ($actions as $data) {
      $ids[] = $data['action_id'];
    }
    $ids = array_unique($ids);

    if (empty($content)) {
      //GET TAGMAP TABLE
      $tagmapsTable = Engine_Api::_()->getDbtable('tagMaps', 'core');

      //GET TAGMAP TABLE NAME
      $tagmapsTableName = $tagmapsTable->info('name');

      //GET ADDLOCATION TABLE NAME
      $addlocationsTableName = $this->info('name');

			//GET ACTIVITY TABLE
			 if (Engine_Api::_()->hasModuleBootstrap('advancedactivity')) {
				$actionsTable = Engine_Api::_()->getDbtable('actions', 'advancedactivity');
			} else {
				$actionsTable = Engine_Api::_()->getDbtable('actions', 'activity');
			}

      //GET ACTION TABLE NAME
      $actionsTableName = $actionsTable->info('name');

      //SELECT THE ACTION IDS
      $select = $this->select()
              ->setIntegrityCheck(false)
              ->from($addlocationsTableName, null)
              ->join($tagmapsTableName, "$tagmapsTableName.resource_id = $addlocationsTableName.item_id and " . $tagmapsTableName . ".resource_type =   $addlocationsTableName.item_type", null)
              ->join($actionsTableName, "$actionsTableName.action_id = $addlocationsTableName.action_id", array('action_id'))
              ->where($actionsTableName . '.type <>?', 'sitepage_post_self')
              ->where($actionsTableName . '.type <>?', 'sitebusiness_post_self')
              ->where($actionsTableName . '.type <>?', 'sitegroup_post_self')
              ->where($actionsTableName . '.type <>?', 'sitestore_post_self') 
              ->group("$actionsTableName.action_id");

      //FETCH RESULTS
      $results = $select->query()->fetchAll();
      foreach ($results as $key => $value) {
        $action_ids[] = $value['action_id'];
      }

      //MAKE UNIQUE IDS ARRAY
      if (!empty($action_ids))
        $ids = array_unique(array_merge($ids, $action_ids));
    }

    return $ids;
  }

  /**
   * Return all locations 
   *
   * @param array $subject
   * @param array $type
   * @param array $parent_id
   * @param int $category
   */
  public function getAllLocations($subject, $type, $parent_id = null, $category = null) {

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //GET ADDLOCATION TABLE NAME
    $addlocationsTableName = $this->info('name');

    //GET LOCATION TABLE
    $locationTable = Engine_Api::_()->getDbtable('locations', 'seaocore');

    //GET LOCATION TABLE NAME
    $locationTableName = $locationTable->info('name');

    //GET ACTION IDS
    $action_ids = $this->getActivity($subject, $viewer);

    //IF THERE IS NO ACTION ID THEN RETURN
    if (empty($action_ids))
      return;

    //SELECT LOCAITONS 
    $select = $locationTable->select()->setIntegrityCheck(false)->from($locationTableName, array('location', 'latitude', 'longitude', 'location_id', 'count(*) as count'));
    $select->join($addlocationsTableName, "$addlocationsTableName.location_id = $locationTableName.location_id", null)
            ->where("$addlocationsTableName.action_id in (?)", (array) $action_ids)
            ->where("$addlocationsTableName.location_id !=?", 0)
            ->where("$addlocationsTableName.owner_id =?", $subject->getIdentity())
            ->group("$addlocationsTableName.location_id")
            ->order("$addlocationsTableName.modified_date DESC");

    //CHECK TYPE CHECKIN OR TAGGING
    if ($type == 'checkin') {
      $select->where("$addlocationsTableName.type =?", $type);
    } elseif ($type == 'tagging') {
      //THIS IS USE BECAUSE IF USER HAS ADDED THE PHOTO WHEN POSTING THE STATUS BOX FEED.
      $select
              ->where("$addlocationsTableName.object_type in (?)", Engine_Api::_()->sitetagcheckin()->makeObjectPhotoArray());
    }

    //CHECK PARENT ID 
    if (!empty($parent_id)) {
      $select->where("$addlocationsTableName.parent_id =?", 0);
    }

    //RETURN LOCATIONS
    return $locationTable->fetchAll($select);
  }

  /**
   * Return count of locations
   *
   * @param array $subject
   * @param array $type
   * @param array $parent_id
   */
  public function getFilterBasedCount($subject, $type, $parent_id = null) {

    //GET ACTION TABLE
    $actionsTable = Engine_Api::_()->getDbtable('actions', 'activity');

    //GET ACTION TABLE NAME
    $actionsTableName = $actionsTable->info('name');

    //GET ADDLOCATION TABLE NAME
    $addlocationsTableName = $this->info('name');

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //GET ACTION IDS
    $action_ids = $this->getActivity($subject, $viewer);

    //IF THERE IS NO ACTION ID THEN RETURN
    if (empty($action_ids))
      return 0;

    //SELECT LOCATION ID
    $select = $actionsTable->select()->from($actionsTableName, array("$addlocationsTableName.location_id"))->setIntegrityCheck(false)
            ->join($addlocationsTableName, $actionsTableName . '.action_id = ' . $addlocationsTableName . '.action_id', null)
            ->where("$addlocationsTableName.action_id in (?)", (array) $action_ids)
            ->where("$addlocationsTableName.location_id !=?", 0)
            ->where("$addlocationsTableName.owner_id =?", $subject->getIdentity());

    //CHECK TYPE CHECKIN OR TAGGING
    if ($type == 'checkin') {
      $select->where("$addlocationsTableName.type =?", $type);
    } elseif ($type == 'tagging') {
      //THIS IS USE BECAUSE IF USER HAS ADDED THE PHOTO WHEN POSTING THE STATUS BOX FEED.
      $select
              ->where("$addlocationsTableName.object_type in (?)", Engine_Api::_()->sitetagcheckin()->makeObjectPhotoArray());
    }

    //CHECK PARENT ID
    if (!empty($parent_id)) {
      $select->where("$addlocationsTableName.parent_id =?", 0);
      $select->group("$addlocationsTableName.action_id");
    }

    //FECTH COUNT RESULTS
    $count = count($select->query()->fetchAll(Zend_Db::FETCH_COLUMN));

    //RETURN COUNT
    return $count;
  }

  /**
   * Return feed items
   *
   * @param array $subject
   * @param array $type
   * @param array $location_id
   * @param int $category
   */
  public function getFeedItems($subject, $type = null, $location_id = null, $category=null, $contentFeeds = null) {

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
    $params = array();

    //GET ACTION IDS
    if (empty($contentFeeds)) {
      $action_ids = $this->getActivity($subject, $viewer);
    } else {
      $action_ids = $this->getActivity($subject, $viewer, $params, $contentFeeds);
    }

    //IF THERE IS NO ACTION ID THEN RETURN
    if (empty($action_ids))
      return;

    //GET ACTION TABLE
    $actionsTable = Engine_Api::_()->getDbtable('actions', 'activity');

    //GET ACTION TABLE NAME
    $actionsTableName = $actionsTable->info('name');

    //GET ADDLOCATION TABLE NAME
    $addlocationsTableName = $this->info('name');

    //SELCET ACTION IDS
    $select = $actionsTable->select()->setIntegrityCheck(false)
            ->from($actionsTableName, array('*'))
            ->join($addlocationsTableName, $addlocationsTableName . '.action_id = ' . $actionsTableName . '.action_id', array('resource_type', 'resource_id', 'location_id', 'type as types', 'item_type', 'item_id', 'params as locationparams'))
            ->where("$addlocationsTableName.action_id in (?)", (array) $action_ids)
            ->order("$addlocationsTableName.modified_date DESC");

    if (empty($contentFeeds)) {
      $select->where("$addlocationsTableName.owner_id =?", $subject->getIdentity());
    }

    //CEHCK FILTER CATEGORY
    if ($category == 2) {
      $select
              ->where("$addlocationsTableName.parent_id =?", 0)
              ->group("$addlocationsTableName.action_id");
    } elseif ($category == 3) {
      //THIS IS USE BECAUSE IF USER HAS ADDED THE PHOTO WHEN POSTING THE STATUS BOX FEED.
      $select
              ->where("$addlocationsTableName.object_type in (?)", Engine_Api::_()->sitetagcheckin()->makeObjectPhotoArray());
    }

    //CHECK LOCATION ID
    if (!empty($location_id)) {
      $select->where("$addlocationsTableName.location_id =?", $location_id);
    }

    //CHECK TYPE CHECKIN OR TAGGING
    if ($type == 'checkin' && ($category == 2 || $category == 4)) {
      $select->where("$addlocationsTableName.type =?", $type);
    }

    //RETURN SELECT
    return Zend_Paginator::factory($select);
  }

  /**
   * Return checkin count
   *
   * @param array $subject
   * @param int $resource_id
   * @param char $resource_type
   * @param char $type
   * @param int $parent_id
   */
  public function getCheckinCount($subject, $resource_id, $resource_type, $type, $parent_id) {

    //GET ACTION TABLE
    $actionsTable = Engine_Api::_()->getDbtable('actions', 'activity');

    //GET ACTION TABLE NAME
    $actionsTableName = $actionsTable->info('name');

    //GET ADDLOCATION TABLE NAME
    $addlocationsTableName = $this->info('name');

    //SELECT COUNT
    $select = $this->select()->from($this->info('name'), array('count(*) as count'))->setIntegrityCheck(false)
            ->join($actionsTableName, $actionsTableName . '.action_id = ' . $addlocationsTableName . '.action_id', null);

    //GET SUBJECT ID
    if (!empty($subject)) {
      $subject_id = $subject->getIdentity();
      $select->where("$addlocationsTableName.owner_id =?", $subject_id);
    }

    //SELECT ACCORDING TO RESOURCE ID
    if (!empty($resource_id)) {
      $select->where("$addlocationsTableName.object_id =?", $resource_id);
    }

    //SELECT ACCORDING TO RESOURCE TYPE
    if (!empty($resource_type)) {
      $select->where("$addlocationsTableName.object_type =?", $resource_type);
    }

    //CHECK TYPE CHECKIN OR TAGGING
    if ($type == 'checkin') {
      $select->where("$addlocationsTableName.type =?", $type);
    } elseif ($type == 'tagging') {
      //THIS IS USE BECAUSE IF USER HAS ADDED THE PHOTO WHEN POSTING THE STATUS BOX FEED.
      $select
              ->where("$addlocationsTableName.object_type in (?)", Engine_Api::_()->sitetagcheckin()->makeObjectPhotoArray());
    }

    //CHECK ACCORIDN TO PARENT ID
    if (!empty($parent_id)) {
      $select->where("$addlocationsTableName.parent_id =?", 0);
    }

    //COUNT
    $checkinCount = $select->query()->fetchColumn();
    return $checkinCount;
  }

  /**
   * Return checkin users
   *
   * @param array $subject
   * @param int $resource_id
   * @param char $resource_type
   * @param int $checkedin_status
   * @param char $search
   * @param int $call_status
   * @param int $getCount
   */
  public function getCheckinUsers($subject, $checkedin_status, $search, $call_status, $getCount = null) {

    //GET ADDLOCATION TABLE NAME
    $addLocationTableName = $this->info('name');

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //GET ACTION IDS
    $action_ids = $this->getActivity($subject, $viewer);

    //IF THERE IS NO ACTION ID THEN RETURN
    if (empty($action_ids))
      return;

    //GET USER TABLE
    $userTable = Engine_Api::_()->getItemTable('user');

    //GET USER TABLE NAME
    $userTableName = $userTable->info('name');

    //SELECT CHECKIN
    $select = $userTable->select()
            ->setIntegrityCheck(false)
            ->from($addLocationTableName, array("MAX($addLocationTableName.modified_date) as location_modified_date", 'owner_id'))
            ->where("$addLocationTableName.action_id in (?)", (array) $action_ids)
            ->where($addLocationTableName . '.object_type = ?', $subject->getType())
            ->where($addLocationTableName . '.object_id = ?', $subject->getIdentity())
            ->group($addLocationTableName . '.owner_id');

    if (!empty($search)) {
      $select->where($userTableName . '.displayname LIKE ?', '%' . $search . '%');
    }

    if ($call_status == 'friend') {
      //GET MEMBERSHIP TABLE NAME
      $memberName = Engine_Api::_()->getDbtable('membership', 'user')->info('name');
      $select->joinInner($memberName, "$memberName . resource_id = $addLocationTableName . owner_id", NULL)
              ->joinInner($userTableName, "$userTableName . user_id = $memberName . resource_id")
              ->where($memberName . '.user_id = ?', $viewer->getIdentity())
              ->where($memberName . '.active = ?', 1)
              ->where($addLocationTableName . '.owner_id != ?', $viewer->getIdentity());
    } else if ($call_status == 'public') {
      $select->joinInner($userTableName, "$userTableName . user_id = $addLocationTableName . owner_id", array('*'));
    }

    //SELECT ONLY CHECK-IN CONTENT
    if ($checkedin_status == 1) {
      $current_time_stamp = time();
      $current_date = date('Y-m-d');
      $checkin_timestamp = Engine_Api::_()->getApi('settings', 'core')->sitetagcheckin_max_status_time;
      $checkin_date = date('Y-m-d H:i:s', ($current_time_stamp - $checkin_timestamp));
      $select->where($addLocationTableName . '.creation_date >= ?', $checkin_date)
              ->where($addLocationTableName . '.event_date = ?', $current_date)
              ->where($addLocationTableName . '.current_checkin = ?', 1);
    }
    $select->where($addLocationTableName . '.type = ?', 'checkin');
    $select->order("location_modified_date DESC");
    $paginator = Zend_Paginator::factory($select);

    //GET TOTAL CHECKIN COUNT
    if ($getCount == 1) {
      return $paginator->getTotalItemCount();
    }

    return $paginator;
  }

  /**
   * Return params 
   *
   * @param char $object_type
   * @param int $object_id
   */
  public function getCheckinParams($object_type, $object_id) {

    //GET ADDLOCATION TABLE NAME
    $addlocationsTableName = $this->info('name');

    //INITIALISING PARMAS 
    $params = 0;

    //SELECT PARAMS
    $select = $this->select()->from($addlocationsTableName, array('params', 'action_id'));

    //CHECK OBJECT TYPE
    if ($object_type == 'album_photo') {
      $select->where($addlocationsTableName . '.resource_type = ?', $object_type)
              ->where($addlocationsTableName . '.resource_id = ?', $object_id);
    } elseif ($object_type == 'advalbum_photo') {
      $select->where($addlocationsTableName . '.resource_type = ?', $object_type)
              ->where($addlocationsTableName . '.resource_id = ?', $object_id);
    }else {
      $select->where($addlocationsTableName . '.object_type = ?', $object_type)
              ->where($addlocationsTableName . '.object_id = ?', $object_id);
    }
    $select->order("$addlocationsTableName.addlocation_id DESC");

    $row = $this->fetchRow($select);
    if (empty($row))
      return;

    return $row;
  }

  /**
   * Return $existingRows 
   *
   * @param array $params
   */
  public function getCheckinsDetails($params = array()) {

    //GET EXISTIN ROWS
    $existingRows = $this->select()->from($this->info('name'), array('*'));

    if (isset($params['action_id'])) {
      $existingRows->where('action_id =?', $params['action_id']);
    }

    if (isset($params['object_type'])) {
      $existingRows->where('object_type =?', $params['object_type']);
    }

    if (isset($params['object_id'])) {
      $existingRows->where('object_id =?', $params['object_id']);
    }

    if (isset($params['item_type'])) {
      $existingRows->where('item_type =?', $params['item_type']);
    }

    if (isset($params['item_id'])) {
      $existingRows->where('item_id =?', $params['item_id']);
    }

    if (isset($params['checkowner'])) {
      switch ($params['checkowner']) {
        case '0':
          $existingRows->where('owner_id <>?', $params['owner_id']);
          return $this->fetchRow($existingRows);
          break;
        case '1':
          $existingRows->where('owner_id =?', $params['owner_id']);
          return $existingRows->query()->fetchAll();
          break;
        case '2':
          $existingRows->where('owner_id =?', $params['owner_id']);
          return $this->fetchRow($existingRows);
          break;
      }
    }
  }

  /**
   * Return $count 
   */
  public function getPagesBusinessCheckinCount() {

    //GET ADDLOCATION TABLE NAME
    $addlocationsTableName = $this->info('name');

    //SET ITEM TYPES
    $itemTypes = array("sitepage_page", "sitebusiness_business", "sitegroup_group", "sitestore_store", "siteevent_event");
    return $this->select()->from($addlocationsTableName, array('count(*) as count'))
                    ->where("$addlocationsTableName.object_type in (?)", new Zend_Db_Expr("'" . join("', '", $itemTypes) . "'"))->query()
                    ->fetchColumn();
  }

  /**
   * Return object_ids 
   *
   * @param char $object_type
   */
  public function getObjectIds($object_type) {

    //GET ADDLOCATION TABLE NAME
    $addlocationsTableName = $this->info('name');

    $object_ids = $this->select()->from($addlocationsTableName, 'object_id')->where('object_type =?', $object_type)->query()->fetchAll(Zend_Db::FETCH_COLUMN);
    return $object_ids;
  }


  public function getEventsData($subject) {

    $locationsTable = Engine_Api::_()->getDbtable('locations', 'seaocore');
    $locationsTableName = $locationsTable->info('name');

    $moduleCore = Engine_Api::_()->getDbtable('modules', 'core');
    
    $data = array();
		$eventdata=array();
		$pageeventdata=array();
		$businesseventdata=array();
    $groupeventdata=array();
		$siteeventdata=array();
    //GET EVENTS ATTENDED
    $getEnableModuleEvent = $moduleCore->isModuleEnabled('event');
    $getEnableModuleYnEvent = $moduleCore->isModuleEnabled('ynevent');
    if($getEnableModuleEvent) {
			$eventTable = Engine_Api::_()->getDbtable('events', 'event');
			$eventTableName = $eventTable->info('name');
			$eventMembershipTable = Engine_Api::_()->getDbtable('membership', 'event');
			$eventMembershipTableName = $eventMembershipTable->info('name');
			$select = $eventTable->select()
							  ->setIntegrityCheck(false)
								->from($eventTableName, null)
								->join($eventMembershipTableName, "$eventMembershipTableName.resource_id = $eventTableName.event_id", null)
								->join($locationsTableName, "$locationsTableName.location_id	= $eventTableName.seao_locationid", array('*'))
								->where($eventMembershipTableName . '.rsvp =?', 2)
								->where($eventMembershipTableName . '.user_id =?', $subject->user_id)
								->where('starttime <= ?', date('Y-m-d H:i:s'));
			$eventLocations = $eventTable->fetchAll($select);
			
			foreach ($eventLocations as $value) {
				$content_array = array();
				$content_array['location'] = $value->location;
				$content_array['latitude'] = $value->latitude;
				$content_array['longitude'] = $value->longitude;
				$content_array['location_id'] = $value->location_id;
				$eventdata[] = $content_array;
			}
    }

    elseif($getEnableModuleYnEvent) {
			$eventTable = Engine_Api::_()->getItemTable('event');
			$eventTableName = $eventTable->info('name');
			$eventMembershipTable = Engine_Api::_()->getDbTable('membership', 'ynevent');
			$eventMembershipTableName = $eventMembershipTable->info('name');
			$select = $eventTable->select()
							  ->setIntegrityCheck(false)
								->from($eventTableName, null)
								->join($eventMembershipTableName, "$eventMembershipTableName.resource_id = $eventTableName.event_id", null)
								->join($locationsTableName, "$locationsTableName.location_id	= $eventTableName.seao_locationid", array('*'))
								->where($eventMembershipTableName . '.rsvp =?', 2)
								->where($eventMembershipTableName . '.user_id =?', $subject->user_id)
								->where('starttime <= ?', date('Y-m-d H:i:s'));
			$eventLocations = $eventTable->fetchAll($select);
			
			foreach ($eventLocations as $value) {
				$content_array = array();
				$content_array['location'] = $value->location;
				$content_array['latitude'] = $value->latitude;
				$content_array['longitude'] = $value->longitude;
				$content_array['location_id'] = $value->location_id;
				$eventdata[] = $content_array;
			}
    }
    
  
   //CHECK IF SITEMOBILE PLUGIN IS ENABLED AND SITE IS IN MOBILE MODE:
		$checkMobMode = !Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode');
    if ($checkMobMode) return $eventdata;
    //GET PAGEEVENTS ATTENDED
    $getEnableModulePageEvent = $moduleCore->isModuleEnabled('sitepageevent');
    if($getEnableModulePageEvent) {
			$pageeventTable = Engine_Api::_()->getDbtable('events', 'sitepageevent');
			$pageeventTableName = $pageeventTable->info('name');
			$pageeventMembershipTable = Engine_Api::_()->getDbtable('membership', 'sitepageevent');
			$pageeventMembershipTableName = $pageeventMembershipTable->info('name');

			//SELECT THE ACTION IDS
			$select = $pageeventTable->select()
							->setIntegrityCheck(false)
								->from($pageeventTableName, null)
								->join($pageeventMembershipTableName, "$pageeventMembershipTableName.resource_id = $pageeventTableName.event_id", null)
								->join($locationsTableName, "$locationsTableName.location_id	= $pageeventTableName.seao_locationid", array('*'))
								->where($pageeventMembershipTableName . '.rsvp =?', 2)
								->where($pageeventMembershipTableName . '.user_id =?', $subject->user_id)
								->where('starttime <= ?', date('Y-m-d H:i:s'));
			$pageeventlocations = $pageeventTable->fetchAll($select);
			foreach ($pageeventlocations as $value) {
				$content_array = array();
				$content_array['location'] = $value->location;
				$content_array['latitude'] = $value->latitude;
				$content_array['longitude'] = $value->longitude;
				$content_array['location_id'] = $value->location_id;
				$pageeventdata[] = $content_array;
			}
    }

    //GET BUSINESSEVENTS ATTENDED
    $getEnableModuleBusinessEvent = $moduleCore->isModuleEnabled('sitebusinessevent');
    if($getEnableModuleBusinessEvent) {
			$businesseventTable = Engine_Api::_()->getDbtable('events', 'sitebusinessevent');
			$businesseventTableName = $businesseventTable->info('name');
			$businesseventMembershipTable = Engine_Api::_()->getDbtable('membership', 'sitebusinessevent');
			$businesseventMembershipTableName = $businesseventMembershipTable->info('name');
			$select = $businesseventTable->select()
							->setIntegrityCheck(false)
								->from($businesseventTableName, null)
								->join($businesseventMembershipTableName, "$businesseventMembershipTableName.resource_id = $businesseventTableName.event_id", null)
								->join($locationsTableName, "$locationsTableName.location_id	= $businesseventTableName.seao_locationid", array('*'))
								->where($businesseventMembershipTableName . '.rsvp =?', 2)
								->where($businesseventMembershipTableName . '.user_id =?', $subject->user_id)
								->where('starttime <= ?', date('Y-m-d H:i:s'));
			$businesseventlocations = $businesseventTable->fetchAll($select);
			foreach ($businesseventlocations as $value) {
				$content_array = array();
				$content_array['location'] = $value->location;
				$content_array['latitude'] = $value->latitude;
				$content_array['longitude'] = $value->longitude;
				$content_array['location_id'] = $value->location_id;
				$businesseventdata[] = $content_array;
			}
    }

		//GET GROUPEVENTS ATTENDED
		$getEnableModuleGroupEvent = $moduleCore->isModuleEnabled('sitegroupevent');
		if($getEnableModuleGroupEvent) {
			$groupeventTable = Engine_Api::_()->getDbtable('events', 'sitegroupevent');
			$groupeventTableName = $groupeventTable->info('name');
			$groupeventMembershipTable = Engine_Api::_()->getDbtable('membership', 'sitegroupevent');
			$groupeventMembershipTableName = $groupeventMembershipTable->info('name');
			$select = $groupeventTable->select()
				->setIntegrityCheck(false)
				->from($groupeventTableName, null)
				->join($groupeventMembershipTableName, "$groupeventMembershipTableName.resource_id = $groupeventTableName.event_id", null)
				->join($locationsTableName, "$locationsTableName.location_id	= $groupeventTableName.seao_locationid", array('*'))
				->where($groupeventMembershipTableName . '.rsvp =?', 2)
				->where($groupeventMembershipTableName . '.user_id =?', $subject->user_id)
				->where('starttime <= ?', date('Y-m-d H:i:s'));
			$groupeventlocations = $groupeventTable->fetchAll($select);
			foreach ($groupeventlocations as $value) {
				$content_array = array();
				$content_array['location'] = $value->location;
				$content_array['latitude'] = $value->latitude;
				$content_array['longitude'] = $value->longitude;
				$content_array['location_id'] = $value->location_id;
				$groupeventdata[] = $content_array;
			}
		} 

		//GET SITEEVENTS ATTENDED
		$getsiteeventEnableModuleEvent = $moduleCore->isModuleEnabled('siteevent');
		if($getsiteeventEnableModuleEvent) {
			$siteeventTable = Engine_Api::_()->getDbtable('events', 'siteevent');
			$siteeventTableName = $siteeventTable->info('name');
			$siteeventMembershipTable = Engine_Api::_()->getDbtable('membership', 'siteevent');
			$siteeventMembershipTableName = $siteeventMembershipTable->info('name');
			$siteeventOccurrencesTable = Engine_Api::_()->getDbtable('occurrences', 'siteevent');
			$siteeventOccurrencesTableName = $siteeventOccurrencesTable->info('name');
			$siteeventlocationsTable = Engine_Api::_()->getDbtable('locations', 'siteevent');
			$siteeventlocationsTableName = $siteeventlocationsTable->info('name');
			$select = $siteeventTable->select()
				->setIntegrityCheck(false)
				->from($siteeventTableName, null)
				->join($siteeventMembershipTableName, "$siteeventMembershipTableName.resource_id = $siteeventTableName.event_id", null)
				->join($siteeventOccurrencesTableName, "$siteeventOccurrencesTableName.event_id = $siteeventTableName.event_id", null)
				->join($siteeventlocationsTableName, "$siteeventlocationsTableName.event_id = $siteeventTableName.event_id", array('*'))
				//->join($locationsTableName, "$locationsTableName.location_id	= $siteeventlocationsTableName.location_id", array('*'))
				->where($siteeventMembershipTableName . '.rsvp =?', 2)
				->where($siteeventMembershipTableName . '.user_id =?', $subject->user_id)
				->where("$siteeventOccurrencesTableName.starttime <= ?", date('Y-m-d H:i:s'));
			$siteeventlocations = $siteeventTable->fetchAll($select);
			foreach ($siteeventlocations as $value) {
				$content_array = array();
				$content_array['location'] = $value->location;
				$content_array['latitude'] = $value->latitude;
				$content_array['longitude'] = $value->longitude;
				$content_array['location_id'] = $value->location_id;
				$siteeventdata[] = $content_array;
			}
		} 
    $totaleventdata = array_merge($eventdata, $pageeventdata);
    $totaleventdata = array_merge($totaleventdata, $businesseventdata);
		$totaleventdata = array_merge($totaleventdata, $groupeventdata);
    $data = array_merge($totaleventdata, $siteeventdata);
    return $data;
  }

}