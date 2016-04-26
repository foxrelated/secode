<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitetagcheckin_Api_Core extends Core_Api_Abstract {

  /**
   * Get page select query
   *
   * @param array $params
   * @param array $customParams
   * @return string $select;
   */
  public function getEventSelect($params = array()) {

    $table = Engine_Api::_()->getItemTable('event');
    $rName = $table->info('name');

    $locationTable = Engine_Api::_()->getDbtable('locationitems', 'seaocore');
    $locationName = $locationTable->info('name');

    $select = $table->select();
    $select = $select
            ->setIntegrityCheck(false)
            ->from($rName);

    //$select = $select->where($locationName . '.resource_type = ?', 'event');
    $select = $select->where($rName . '.search = ?', '1');

    if (isset($params['sitepage_street']) && !empty($params['sitepage_street'])) {
      $select->join($locationName, "$rName.seao_locationid = $locationName.locationitem_id   ");
      $select->where($locationName . '.formatted_address   LIKE ? ', '%' . $params['sitepage_street'] . '%');
    } if (isset($params['sitepage_city']) && !empty($params['sitepage_city'])) {
      $select->join($locationName, "$rName.seao_locationid = $locationName.locationitem_id   ");
      $select->where($locationName . '.city = ?', $params['sitepage_city']);
    } if (isset($params['sitepage_state']) && !empty($params['sitepage_state'])) {
      $select->join($locationName, "$rName.seao_locationid = $locationName.locationitem_id   ");
      $select->where($locationName . '.state = ?', $params['sitepage_state']);
    } if (isset($params['sitepage_country']) && !empty($params['sitepage_country'])) {
      $select->join($locationName, "$rName.seao_locationid = $locationName.locationitem_id   ");
      $select->where($locationName . '.country = ?', $params['sitepage_country']);
    }

    if ((isset($params['sitepage_location']) && !empty($params['sitepage_location']))) {
      if (isset($params['locationmiles']) && (!empty($params['locationmiles']))) {
        $longitude = 0;
        $latitude = 0;

        //check for zip code in location search.
        if (empty($params['Latitude']) && empty($params['Longitude'])) {
          $selectLocQuery = $locationTable->select()->where('location = ?', $params['sitepage_location']);
          $locationValue = $locationTable->fetchRow($selectLocQuery);

          if (empty($locationValue)) {
            $locationResults = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getLatLong(array('location' => $params['sitepage_location'], 'module' => 'Geo-Location, Geo-Tagging, Check-Ins & Proximity Search'));
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

        
        $radius = $params['locationmiles']; //in miles

        $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.proximity.search.kilometer', 0);
        if (!empty($flage)) {
          $radius = $radius * (0.621371192);
        }
        //$latitudeRadians = deg2rad($latitude);
        $latitudeSin = "sin(radians($latitude))";
        $latitudeCos = "cos(radians($latitude))";
        $select->join($locationName, "$rName.seao_locationid = $locationName.locationitem_id   ", array("latitude", "longitude", "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance"));
        $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
        $sqlstring .= ")";
        $select->where($sqlstring);
        $select->order("distance");
      } else {
        $select->join($locationName, "$rName.seao_locationid = $locationName.locationitem_id");
        $select->where("`{$locationName}`.formatted_address LIKE ? or `{$locationName}`.location LIKE ? or `{$locationName}`.city LIKE ? or `{$locationName}`.state LIKE ?", "%" . urldecode($params['sitepage_location']) . "%");
        //}
      }
    } elseif (!empty($params['latitude']) && !empty($params['longitude'])) {
      $radius = Engine_Api::_()->getApi('settings', 'core')->getSetting('sgl.geolocation.range', 100); // in miles
      $latitude = $params['latitude'];
      $longitude = $params['longitude'];
      $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.proximity.search.kilometer', 0);
      if (!empty($flage)) {
        $radius = $radius * (0.621371192);
      }
      $latitudeSin = "sin(radians($latitude))";
        $latitudeCos = "cos(radians($latitude))";
      $select->join($locationName, "$rName.seao_locationid = $locationName.locationitem_id", array("(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance"));
      $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
      $sqlstring .= ")";
      $select->where($sqlstring);
      $select->order("distance");
    } elseif (empty($params['sitepage_street']) && empty($params['sitepage_city']) && empty($params['sitepage_state']) && empty($params['sitepage_country'])) {
      $select->joinLeft($locationName, "$rName.seao_locationid = $locationName.locationitem_id");
    }

    if (!empty($params['category_id'])) {
      $select->where($rName . '.category_id = ?', $params['category_id']);
    }

    if (!empty($params['users']) && isset($params['users'])) {
      $str = (string) ( is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users'] );
      $select->where($rName . '.user_id in (?)', new Zend_Db_Expr($str));
    } elseif (empty($params['users']) && $params['view_view'] == '1' && isset($params['view_view'])) {
      $select->where($rName . '.user_id = ?', '0');
    }

    if (isset($params['view_view']) && !empty($params['view_view'])) {
      // Endtime
      if ($params['view_view'] == 3) {
        $select->where("endtime <= FROM_UNIXTIME(?)", time());
      } elseif ($params['view_view'] == 2) {
        $select->where("endtime > FROM_UNIXTIME(?)", time());
      }
    }

    if (isset($params['order']) && !empty($params['order'])) {
      switch ($params['order']) {
        case "1":
          $select->order($rName . '.starttime ASC');
          break;
        case "2":
          $select->order($rName . '.creation_date DESC');
          break;
        case "3":
          $select->order($rName . '.member_count DESC');
          break;
      }
    } /* else {
      $select->order($rName . '.creation_date DESC');
      } */


    //Could we use the search indexer for this?
    if (!empty($params['search'])) {
      $select->where($rName . ".title LIKE ? OR " . $rName . ".description LIKE ? ", '%' . $params['search'] . '%');
    }

    if (!empty($params['starttime']['date'])) {
      $startTime = date("Y-m-d", strtotime($params['starttime']['date']));
      $select->where($rName . '.starttime >= ?', $startTime);
    }

    if (!empty($params['endtime']['date'])) {
      $endTime = date("Y-m-d", strtotime($params['endtime']['date']));
      $select->where($rName . '.endtime <= ?', $endTime);
    }
    return $select;
  }

  /**
   * Return $suggestGooglePalces 
   *
   * @param char $keyword
   * @param int $latitude
   * @param int $longitude
   */
  public function getSuggestGooglePalces($keyword, $latitude = 0, $longitude = 0) {

    //GET API KEY
    $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();

    //GET LATITUDE
    $latitude = str_replace(',', '.', "$latitude");

    //GET LONGITUDE
    $longitude = str_replace(',', '.', "$longitude");

    //SET PARAMS
    $params = array(
        'key' => $apiKey,
        'sensor' => 'false',
        'input' => $keyword,
        'language' => $this->getGoogleMapLocale(),
    );

    //SET LOCATION
    if ($latitude != '0' && $longitude != '0') {
      $params['location'] = $latitude . ',' . $longitude;
    }

    //BUILD QUERY STRING
    $query = http_build_query($params, null, '&');

    //SET URL
    $url = 'https://maps.googleapis.com/maps/api/place/autocomplete/json?' . $query;

    //SEND CURL REQUEST
    $ch = curl_init();
    $timeout = 0;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    ob_start();
    curl_exec($ch);
    curl_close($ch);

    //GET CURL RESPONSE
    $response = Zend_Json::decode(ob_get_contents());

    //IF EMPTY REESPONSE THEN GET RESPONSE FROM FILE GET CONTENTS
    if (empty($response)) {
      $response = Zend_Json::decode(file_get_contents($url));
    }

    ob_end_clean();

    //IF STATUS IS NOT OK THE RETURN
    if (!isset($response['status']) || $response['status'] != 'OK') {
      return array();
    }

    //GET PREDICTIONS
    $results = isset($response['predictions']) ? $response['predictions'] : array();

    //MAKE SUGGEST ARRAY
    $suggestGooglePalces = array();
    foreach ($results as $place) {
      $suggestGooglePalces[] = array(
          'resource_guid' => 0,
          //'google_id' => $place['id'],
          'label' => $place['description'],
          'place_id' => $place['place_id'],
      );
    }

    return $suggestGooglePalces;
  }

  /**
   * Return $suggestItems FOR PAGE / BUSINESSES 
   *
   * @param char $keyword
   * @param int $table_name
   * @param int $resource_ids
   */
  public function getSuggestContent($keyword, $table_name, $resource_ids) {

    //GET ITEM TABLE
    $table = Engine_Api::_()->getItemTable($table_name);

    //GET LOCATIONS ITEMS
    $items = $table->getLocationBaseContents(array('search' => $keyword, 'resource_id' => $resource_ids));

    //GET VIEW OBJECT
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    //INITIALISE SUGGEST ARRAY
    $suggestItems = array();

    if (!empty($items)) {

      //MAKE SUGEGSTION ARRAY OF PAGE / BUSINESSES 
      foreach ($items as $item) {
        $suggestItems[] = array(
            'resource_guid' => $item->getGuid(),
            'google_id' => 0,
            'label' => $item->getTitle(),
            'place_id' => 0,
            'prefixadd' => 'at',
            'photo' => $view->itemPhoto($item, 'thumb.icon'),
            'type' => ucfirst($item->getShortType()),
            'category' => $item->getCategoryName(),
            'vicinity' => $item->formatted_address,
            'latitude' => $item->latitude,
            'longitude' => $item->longitude
        );
      }
    }

    return $suggestItems;
  }

  /**
   * Return $previoussuggestGooglePalces 
   *
   * @param char $keyword
   * @param int $latitude
   * @param int $longitude
   */
  public function getPreviousGooglePlacesResults() {

    //GET ADDLOCATION TABLE
    $addlocationTable = Engine_Api::_()->getDbtable('addlocations', 'sitetagcheckin');

    //GET ADD LOCATION TABLE NAME
    $addlocationsTableName = $addlocationTable->info('name');

    //GET LOCATION TABLE
    $locationTable = Engine_Api::_()->getDbtable('locations', 'seaocore');

    //GET LOCATION TABLE NAME
    $locationName = $locationTable->info('name');

    //VIEWER INFORMATION
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //SET LIMIT HOW MANY ITEM WE WANT TO GET
    $maxLimit = 20;

    //SET ITEM TYPES
    $itemTypes = array("sitepage_page", "sitebusiness_business", "sitegroup_group", "sitestore_store", "siteevent_event");

    //GET CHECKIN COUNT
    $count = $addlocationTable->getPagesBusinessCheckinCount();

    //SET MAX LIMIT
    if ($count > 0) {
      $maxLimit = $maxLimit - $count;
    }

    //MAKE SELECT QUERY FOR GETTING THE LOCATION
    $select = $locationTable->select()
            ->setIntegrityCheck(false)
            ->from($locationName, array('location', 'location_id', 'latitude', 'longitude', 'formatted_address'))
            ->join($addlocationsTableName, "$addlocationsTableName.location_id = $locationName.location_id", array('params'))
            ->where("$addlocationsTableName.owner_id =?", $viewer_id)
            ->where("$addlocationsTableName.object_type not in (?)", new Zend_Db_Expr("'" . join("', '", $itemTypes) . "'"))
            ->group("$addlocationsTableName.location_id")
            ->order("$addlocationsTableName.creation_date DESC")
            ->limit($maxLimit);

    //MAKE RESULTS
    $results = $locationTable->fetchAll($select);

    //INITIALISE SUGGEST PREVIOUS GOOGLE PLACES
    $previoussuggestGooglePalces = array();

    //MAKE PREVOUS GOOGLE PLACE ARRAY
    foreach ($results as $place) {
       
        $decodedParams =  Zend_Json_Decoder::decode($place['params']);
       
        if(isset($decodedParams['checkin']['reference'])) {
            $params = array('reference' => $decodedParams['checkin']['reference'], 'sensor' => true, 'key' => Engine_Api::_()->seaocore()->getGoogleMapApiKey());
            $query = http_build_query($params, null, '&');
            $url = 'https://maps.googleapis.com/maps/api/place/details/json?' . $query;
            //SEND CURL REQUEST
            $ch = curl_init();
            $timeout = 0;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            ob_start();
            curl_exec($ch);
            curl_close($ch);
            //GET CURL RESPONSE
            $response = Zend_Json::decode(ob_get_contents());
            //IF EMPTY REESPONSE THEN GET RESPONSE FROM FILE GET CONTENTS
            if (empty($response)) {
              $response = Zend_Json::decode(file_get_contents($url));
            }
            ob_end_clean();
            //IF STATUS IS NOT OK THE RETURN
            if (!isset($response['status']) || $response['status'] != 'OK') {
              return array();
            }
            
            $place_id = $response['result']['place_id'];
        } elseif(isset($decodedParams['checkin']['place_id'])) {
            $place_id = $decodedParams['checkin']['place_id'];
        }
        
        $previoussuggestGooglePalces[] = array(
          'resource_guid' => 0,
          //'google_id' => 0,
          'label' => $place['location'],
          'place_id' => $place_id,
          'vicinity' => $place['formatted_address'],
          'latitude' => $place['latitude'],
          'longitude' => $place['longitude']
      );
    }
//print_r($previoussuggestGooglePalces);die;
    return $previoussuggestGooglePalces;
  }

  /**
   * Return previous $suggestItems FOR PAGE / BUSINESSES 
   *
   * @param char $keyword
   * @param int $table_name
   */
  public function getPreviousSuggestContent($keyword, $table_name) {

    //GET ITEM TABLE
    $table = Engine_Api::_()->getItemTable($table_name);

    //GET PREVIOUS ITEMS
    $items = $table->getPreviousLocationBaseContents();

    //GET VIEW OBJECT
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    //INITIALISE PREVIOUS SUGGEST ARRAY
    $previoussuggestItems = array();

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.tagged.location', 1)) {
      return;
    }

    //MAKE PREVIOUS SUGGEST ARRAY
    foreach ($items as $item) {
      $previoussuggestItems[] = array(
          'resource_id' => $item->getIdentity(),
          'resource_guid' => $item->getGuid(),
        //  'google_id' => 0,
          'label' => $item->getTitle(),
          'place_id' => 0,
          'prefixadd' => 'at',
          'photo' => $view->itemPhoto($item, 'thumb.icon'),
          'type' => ucfirst($item->getShortType()),
          'category' => $item->getCategoryName(),
          'vicinity' => $item->formatted_address,
          'latitude' => $item->latitude,
          'longitude' => $item->longitude
      );
    }

    //RETURN PREVIOUS SUGGETS ITEMS
    return $previoussuggestItems;
  }

  /**
   * Return URL
   *
   * @param int $file_id
   * @param char $itemTable
   */
  public function getPhotoUrl($file_id, $itemTable) {

    //GET ITEM TABLE
    $table = Engine_Api::_()->getItemTable($itemTable);

    //GET PHOTO ID
    $photo_id = $table->select()
            ->from($table->info('name'), 'photo_id')
            ->where('file_id =?', $file_id)
            ->query()
            ->fetchColumn();

    //GET ITEM TABLE
    $getItem = Engine_Api::_()->getItem($itemTable, $photo_id);

    //RETURN HREF
    return $getItem->getHref();
  }

  /**
   * Added Widget On Page
   *
   * @return bool
   */
  public function getWidgetId($pageName, $widgetName, $params = array()) {

    //GET CORE PAGES TABLE
    $pagesTable = Engine_Api::_()->getDbtable('pages', 'core');

    //GET CORE PAGES TABLE NAME
    $pagesTableName = $pagesTable->info('name');

    //GET CORE CONTENT TABLE
    $contentsTable = Engine_Api::_()->getDbtable('content', 'core');

    //GET CORE CONTENT TABLE NAME
    $contentsTableName = $contentsTable->info('name');

    //INITIALISE CONTENT ID
    $content_id = 0;

    //MAKE QUERY FOR GETTING THE CONTENT ID
    $select = $contentsTable->select()
                    ->setIntegrityCheck(false)
                    ->from($contentsTableName, array($contentsTableName . '.name', $contentsTableName . '.content_id'))
                    ->join($pagesTableName, "`{$pagesTableName}`.page_id = `{$contentsTableName}`.page_id  ", null)
                    ->where($pagesTableName . '.name = ?', $pageName)->where($contentsTableName . '.name = ?', $widgetName);

    $row = $contentsTable->fetchRow($select);
    if (!empty($row))
      $content_id = $row->content_id;

    return $content_id;
  }

  /**
   * Return SAVE THE CHECKIN
   *
   * @param array $checkinArray
   * @param object $action
   * @param array $locationparams
   * @param int $viewer_id
   */
  public function saveCheckin($checkinArray, $action, $locationparams, $viewer_id) {

    //GET ACTION ID
    $action_id = $action->getIdentity();

    //SET CHECKIN LOCATION NULL
    $checkin_location = null;

    //CHECK RESOURCE GUID IS EMPTY OR NOT
    if (isset($checkinArray['resource_guid']) && !empty($checkinArray['resource_guid'])) {
      //GET ITEM FOR RESOURCE GUID
      $getItem = Engine_Api::_()->getItemByGuid($checkinArray['resource_guid']);
      //GET RESOURCE ID
      $resource_id = $getItem->getIdentity();
      //GET RESOURCE TYPE
      $resource_type = $getItem->getType();
      //GET LOCATION
      $location = $getItem->location;
    } else {
      //GET RESOURCE ID
      $resource_id = $action_id;
      //SET  RESOURCE TYPE
      $resource_type = 'activity_action';
      //GET LOCAITON
      $location = isset($checkinArray['label']) ? $checkinArray['label'] : '';
    }

    //SET ITEM RESOURCE ID FOR FEED
    $item_resource_id = $action_id;

    //GETTTING THE TYPE
    if (isset($checkinArray['type']) && !empty($checkinArray['type'])) {
      $type = $checkinArray['type'];
    }

    //GET ADDLOCATION TABLE
    $addLocationTable = Engine_Api::_()->getDbtable('addlocations', 'sitetagcheckin');

    //INITIALISE LOCATION ID
    $location_id = 0;

    //SET LOCATION ID
    if (!empty($location) && $type != 'just_use') {
      $location_id = $addLocationTable->getLocationId($location);
    }

    //GET ATTACHMENT COUNT 
    $attachmentCount = count($action->getAttachments());
    $contentArray = array(
        'location_id' => $location_id,
        'type' => 'checkin',
        'item_id' => $item_resource_id,
        'item_type' => 'activity_action',
        'params' => $locationparams,
        'action_id' => $action_id,
        'event_date' => date('Y-m-d H:i:s'),
        'owner_id' => $viewer_id);

    //SAVE THE CHECKIN LOCATION
    if (empty($attachmentCount)) {
      $content = array(
          'resource_id' => $resource_id,
          'resource_type' => $resource_type,
          'object_id' => $resource_id,
          'object_type' => $resource_type
      );
      $addLocationTable->saveLocation(array_merge($content, $contentArray));
    } else {
      //IF ATTACHMENT IS THERE THEN GET ATTACHMENT RESOURCE TYPE AND RESOURCE ID
      foreach ($action->getAttachments() as $attachment) {
        $attact_resource_type = $attachment->meta->type;
        $attach_resource_id = $attachment->meta->id;
      }
      $content_array = array(
          'resource_id' => $attach_resource_id,
          'resource_type' => $attact_resource_type,
          'object_id' => $attach_resource_id,
          'object_type' => $attact_resource_type
      );
      $addLocationTable->saveLocation(array_merge($content_array, $contentArray));
    }
  }

  /**
   * Return $locale 
   *
   * @param char $locale
   */
  public function getGoogleMapLocale($locale = false) {

    if (!$locale) {
      $locale = Zend_Registry::get('Zend_Translate')->getLocale();
    }

    $british_english = array('en_AU', 'en_BE', 'en_BW', 'en_BZ', 'en_GB', 'en_GU', 'en_HK', 'en_IE', 'en_IN',
        'en_MT', 'en_NA', 'en_NZ', 'en_PH', 'en_PK', 'en_SG', 'en_ZA', 'en_ZW', 'kw', 'kw_GB');

    $friulian = array('fur', 'fur_IT');

    $swiss_german = array('gsw', 'gsw_CH');

    $norwegian_bokma = array('nb', 'nb_NO');

    $portuguese = array('pt', 'pt_PT');

    $brazilian_portuguese = array('pt_BR');

    $chinese = array('zh', 'zh_CN');

    $sar_china = array('zh_HK', 'zh_MO', 'zh_SG');

    $taiwan = array('zh_TW');

    if (in_array($locale, $british_english)) {
      $locale = 'en-GB';
    } elseif (in_array($locale, $friulian)) {
      $locale = 'it';
    } elseif (in_array($locale, $swiss_german)) {
      $locale = 'de';
    } elseif (in_array($locale, $norwegian_bokma)) {
      $locale = 'no';
    } elseif (in_array($locale, $portuguese)) {
      $locale = 'pt-PT';
    } elseif (in_array($locale, $brazilian_portuguese)) {
      $locale = 'pt-BR';
    } elseif (in_array($locale, $chinese)) {
      $locale = 'zh-CN';
    } elseif (in_array($locale, $sar_china)) {
      $locale = 'zh-HK';
    } elseif (in_array($locale, $taiwan)) {
      $locale = 'zh-TW';
    } elseif ($locale) {
      $locale_arr = explode('_', $locale);
      $locale = ($locale_arr[0]) ? $locale_arr[0] : 'en';
    } else {
      $locale = 'en';
    }

    return $locale;
  }

  public function getMapInfo() {
    $locationStatus = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.location.status');
    $sitetagcheckinQueryInfo = Engine_Api::_()->getApi('settings', 'core')->setSetting('sitetagcheckin.query.info', 1);
    $sitetagcheckinMapNum = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.map.num');

    $sitetagcheckinQueryInfo = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.queue.info');
    $getQueryArray = unserialize($sitetagcheckinQueryInfo);
    $getNumFlag = $this->getNumber($getQueryArray[$locationStatus]);

    $sitetagcheckin_field = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.access.info', 0);
    if (!empty($sitetagcheckin_field)) {
      $sitetagcheckin_field_value = @convert_uudecode($sitetagcheckin_field);
    }
    $sitetagcheckin_field_str = $this->getNumber($sitetagcheckin_field_value);
    $getNumber = $getNumFlag * $sitetagcheckin_field_str;

    if (($sitetagcheckinMapNum == $getNumber) || !empty($sitetagcheckinQueryInfo)) {
      return true;
    }
    return false;
  }

  /**
   * Return $location 
   *
   * @param $subject
   */
  public function getCustomFieldLocation($subject) {
    $resource_type = $subject->getType();
    $location = "";
    //SET CUSTOM FILLED ARRAY
    $customFilledLocationArray = array("classified", "list_listing", "recipe");
    //GET LOCATION FOR CUSTOM FILLED
    if (in_array($resource_type, $customFilledLocationArray)) {

      //GET RESOURCE TABLE
      $resourceTable = Engine_Api::_()->getItemTable($resource_type);

      //GET RESOURCE TABLE NAME
      $resourceTableName = $resourceTable->info('name');

      //GET PRIMARY KEY NAME
      $primary = current($resourceTable->info("primary"));

      //GET FIELD VALUE TABLE
      $valueTable = Engine_Api::_()->fields()->getTable($resource_type, 'values');

      //GET FIELD VALUE TABLE NAME
      $valueTableName = $valueTable->info('name');

      //GET FIELD META TABLE NAME
      $metaName = Engine_Api::_()->fields()->getTable($resource_type, 'meta')->info('name');

      //GET LOCATION
      $location = $valueTable->select()
              ->setIntegrityCheck(false)
              ->from($valueTableName, array('value'))
              ->join($metaName, $metaName . '.field_id = ' . $valueTableName . '.field_id', null)
              ->join($resourceTableName, $resourceTableName . '.' . $primary . '=' . $valueTableName . '.item_id', null)
              ->where($valueTableName . '.item_id = ?', $subject->getIdentity())
              ->where($metaName . '.type = ?', 'Location')
              ->order($metaName . '.field_id')
              ->query()
              ->fetchColumn();
    }

    //GET LOCATION FOR NOT CUSTOM FILED
    if (isset($subject->location) && !empty($subject->location)) {
      $location = $subject->location;
    }
    return $location;
  }

  public function makeObjectPhotoArray() {

    return array('album', 'album_photo', 'advalbum_album', 'advalbum_photo', 'group_photo', 'event_photo', 'classified_photo', 'list_photo', 'recipe_photo', 'sitepage_album', 'sitepage_photo', 'sitepagenote_photo', 'sitepageevent_photo', 'sitebusiness_album', 'sitebusiness_photo', 'sitebusinessnote_photo', 'sitebusinessevent_photo', 'sitegroup_album', 'sitegroup_photo', 'sitegroupnote_photo', 'sitegroupevent_photo', 'sitestore_album', 'sitestore_photo', 'siteevent_photo');
  }

  public function getNumber($str) {
    $flag = 0;
    for ($check = 0; $check < strlen($str); $check++) {
      $flag += @ord($str[$check]);
    }
    return $flag;
  }

  public function insertPrivacyInStream($target_type, $target_id, $action) {

    if (empty($action))
      return;

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->query("INSERT IGNORE INTO `engine4_activity_stream` (`action_id`, `type`, `target_type`, `target_id`, `subject_type`, `subject_id`, `object_type`, `object_id`) VALUES
		('$action->action_id', '$action->type', '$target_type', '$target_id', '$action->subject_type', '$action->subject_id', '$action->object_type', '$action->object_id');");



//     $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
// 		$streamTable->insert(array(
// 			'action_id' => $action->action_id,
// 			'type' => $action->type,
// 			'target_type' => $target_type,
// 			'target_id' => $target_id,
// 			'subject_type' => $action->subject_type,
// 			'subject_id' => $action->subject_id,
// 			'object_type' => $action->object_type,
// 			'object_id' => $action->object_id,
// 		)); 
  }

  /**
   * Get page select query
   *
   * @param array $params
   * @param array $customParams
   * @return string $select;
   */
  public function getGroupsSelect($params = array()) {

    $table = Engine_Api::_()->getItemTable('group');
    $rName = $table->info('name');

    $locationTable = Engine_Api::_()->getDbtable('locationitems', 'seaocore');
    $locationName = $locationTable->info('name');

    $select = $table->select();
    $select = $select
            ->setIntegrityCheck(false)
            ->from($rName);

    //$select = $select->where($locationName . '.resource_type = ?', 'group');
    $select = $select
            ->where($rName . '.search = ?', '1');

    if (isset($params['sitepage_street']) && !empty($params['sitepage_street'])) {
      $select->join($locationName, "$rName.group_id = $locationName.resource_id   ", null);
      $select->where($locationName . '.formatted_address LIKE ? ', '%' . $params['sitepage_street'] . '%');
    } if (isset($params['sitepage_city']) && !empty($params['sitepage_city'])) {
      $select->join($locationName, "$rName.group_id = $locationName.resource_id   ", null);
      $select->where($locationName . '.city = ?', $params['sitepage_city']);
    } if (isset($params['sitepage_state']) && !empty($params['sitepage_state'])) {
      $select->join($locationName, "$rName.group_id = $locationName.resource_id   ", null);
      $select->where($locationName . '.state = ?', $params['sitepage_state']);
    } if (isset($params['sitepage_country']) && !empty($params['sitepage_country'])) {
      $select->join($locationName, "$rName.group_id = $locationName.resource_id   ", null);
      $select->where($locationName . '.country = ?', $params['sitepage_country']);
    }

    if ((isset($params['sitepage_location']) && !empty($params['sitepage_location']))) {
      if (isset($params['locationmiles']) && (!empty($params['locationmiles']))) {
        $longitude = 0;
        $latitude = 0;

        //check for zip code in location search.
        if (empty($params['Latitude']) && empty($params['Longitude'])) {
          $selectLocQuery = $locationTable->select()->where('location = ?', $params['sitepage_location']);
          $locationValue = $locationTable->fetchRow($selectLocQuery);

          if (empty($locationValue)) {
            $locationResults = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getLatLong(array('location' => $params['sitepage_location'], 'module' => 'Geo-Location, Geo-Tagging, Check-Ins & Proximity Search'));
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
        
        $radius = $params['locationmiles']; //in miles
        
        $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.proximity.search.kilometer', 0);
        if (!empty($flage)) {
          $radius = $radius * (0.621371192);
        }
       $latitudeSin = "sin(radians($latitude))";
        $latitudeCos = "cos(radians($latitude))";
        $select->join($locationName, "$rName.seao_locationid = $locationName.locationitem_id   ", array("latitude", "longitude", "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance"));
        $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
        $sqlstring .= ")";
        $select->where($sqlstring);
        $select->order("distance"); 
      } else {
        $select->join($locationName, "$rName.seao_locationid = $locationName.locationitem_id");
        $select->where("`{$locationName}`.formatted_address LIKE ? or `{$locationName}`.location LIKE ? or `{$locationName}`.city LIKE ? or `{$locationName}`.state LIKE ?", "%" . urldecode($params['sitepage_location']) . "%");
        //}
      }
    } elseif (!empty($params['latitude']) && !empty($params['longitude'])) {
      $radius = Engine_Api::_()->getApi('settings', 'core')->getSetting('sgl.geolocation.range', 100); // in miles
      $latitude = $params['latitude'];
      $longitude = $params['longitude'];
      $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.proximity.search.kilometer', 0);
      if (!empty($flage)) {
        $radius = $radius * (0.621371192);
      }
     $latitudeSin = "sin(radians($latitude))";
        $latitudeCos = "cos(radians($latitude))";
      $select->join($locationName, "$rName.seao_locationid = $locationName.locationitem_id", array("(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance"));
      $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
      $sqlstring .= ")";
      $select->where($sqlstring);
      $select->order("distance");
    } elseif (empty($params['sitepage_street']) && empty($params['sitepage_city']) && empty($params['sitepage_state']) && empty($params['sitepage_country'])) {
      $select->joinLeft($locationName, "$rName.seao_locationid = $locationName.locationitem_id");
    }

    if (!empty($params['category_id'])) {
      $select->where($rName . '.category_id = ?', $params['category_id']);
    }

    if (!empty($params['users']) && isset($params['users'])) {
      $str = (string) ( is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users'] );
      $select->where($rName . '.user_id in (?)', new Zend_Db_Expr($str));
    } elseif (empty($params['users']) && isset ($params['view_view']) && $params['view_view'] == '1') {
      $select->where($rName . '.user_id = ?', '0');
    }

    if (isset($params['order']) && !empty($params['order'])) {
      switch ($params['order']) {
        case "1":
          $select->order($rName . '.creation_date DESC');
          break;
        case "2":
          $select->order($rName . '.member_count DESC');
          break;
      }
    } /* else {
      $select->order($rName . '.creation_date DESC');
      } */

    //Could we use the search indexer for this?
    if (!empty($params['search'])) {
      $select->where($rName . ".title LIKE ? OR " . $rName . ".description LIKE ? ", '%' . $params['search'] . '%');
    }
    return $select;
  }

  /**
   * Get page select query
   *
   * @param array $params
   * @param array $customParams
   * @return string $select;
   */
  public function getUsersSelect($params = array(), $customParams = null) {

    $table = Engine_Api::_()->getItemTable('user');
    $rName = $table->info('name');

    $locationTable = Engine_Api::_()->getDbtable('locationitems', 'seaocore');
    $locationName = $locationTable->info('name');

    $searchTable = Engine_Api::_()->fields()->getTable('user', 'search')->info('name');
    $networkTableName = Engine_Api::_()->getDbtable('membership', 'network')->info('name');

    $select = $table->select();
    $select = $select
            ->setIntegrityCheck(false)
            ->from($rName);

    //$select = $select->where($locationName . '.resource_type = ?', 'user');
    $select = $select->where($rName . '.search = ?', '1')
            ->where($rName . '.enabled = ?', '1');

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
              ->joinLeft($searchTable, "$searchTable.item_id = $rName.user_id", null);

      $searchParts = Engine_Api::_()->fields()->getSearchQuery('user', $customParams);
      foreach ($searchParts as $k => $v) {
        //$v = str_replace("%2C%20",", ",$v);
        $select->where("`{$searchTable}`.{$k}", $v);
      }
    }

    if (!empty($params['profile_type'])) {
      $select->where($searchTable . '.profile_type = ?', $params['profile_type']);
    }

    if (isset($params['sitepage_street']) && !empty($params['sitepage_street'])) {
      $select->join($locationName, "$rName.user_id = $locationName.resource_id   ");
      $select->where($locationName . '.formatted_address LIKE ? ', '%' . $params['sitepage_street'] . '%');
    } if (isset($params['sitepage_city']) && !empty($params['sitepage_city'])) {
      $select->join($locationName, "$rName.user_id = $locationName.resource_id   ");
      $select->where($locationName . '.city = ?', $params['sitepage_city']);
    } if (isset($params['sitepage_state']) && !empty($params['sitepage_state'])) {
      $select->join($locationName, "$rName.user_id = $locationName.resource_id   ");
      $select->where($locationName . '.state = ?', $params['sitepage_state']);
    } if (isset($params['sitepage_country']) && !empty($params['sitepage_country'])) {
      $select->join($locationName, "$rName.user_id = $locationName.resource_id   ");
      $select->where($locationName . '.country = ?', $params['sitepage_country']);
    }

    if ((isset($params['sitepage_location']) && !empty($params['sitepage_location']))) {
      if (isset($params['locationmiles']) && (!empty($params['locationmiles']))) {
        $longitude = 0;
        $latitude = 0;

        //check for zip code in location search.
        if (empty($params['Latitude']) && empty($params['Longitude'])) {
          $selectLocQuery = $locationTable->select()->where('location = ?', $params['sitepage_location']);
          $locationValue = $locationTable->fetchRow($selectLocQuery);

          if (empty($locationValue)) {
            $locationResults = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getLatLong(array('location' => $params['sitepage_location'], 'module' => 'Geo-Location, Geo-Tagging, Check-Ins & Proximity Search'));
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

        $radius = $params['locationmiles']; //in miles

        $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.proximity.search.kilometer', 0);
        if (!empty($flage)) {
          $radius = $radius * (0.621371192);
        }
        $latitudeSin = "sin(radians($latitude))";
        $latitudeCos = "cos(radians($latitude))";
        $select->join($locationName, "$rName.seao_locationid = $locationName.locationitem_id   ", array("latitude", "longitude", "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance"));
        $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
        $sqlstring .= ")";
        $select->where($sqlstring);
 if (isset($params['orderby']) && (empty($params['orderby']) || $params['orderby'] == 'distance') && ($params['orderby'] != 'creation_date' || $params['orderby'] != 'view_count' || $params['orderby'] != 'member_count' || $params['orderby'] != 'title')) {
          $select->order("distance");
        }
      } else {
        $select->join($locationName, "$rName.seao_locationid = $locationName.locationitem_id");
        $select->where("`{$locationName}`.formatted_address LIKE ? or `{$locationName}`.location LIKE ? or `{$locationName}`.city LIKE ? or `{$locationName}`.state LIKE ?", "%" . urldecode($params['sitepage_location']) . "%");
        //}
      }
    } elseif (!empty($params['latitude']) && !empty($params['longitude'])) {
      $radius = Engine_Api::_()->getApi('settings', 'core')->getSetting('sgl.geolocation.range', 100); // in miles
      $latitude = $params['latitude'];
      $longitude = $params['longitude'];
      $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.proximity.search.kilometer', 0);
      if (!empty($flage)) {
        $radius = $radius * (0.621371192);
      }
     $latitudeSin = "sin(radians($latitude))";
        $latitudeCos = "cos(radians($latitude))";
      $select->join($locationName, "$rName.seao_locationid = $locationName.locationitem_id", array("(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance"));
      $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
      $sqlstring .= ")";
      $select->where($sqlstring);
      if (isset($params['orderby']) && (empty($params['orderby']) || $params['orderby'] == 'distance') && ($params['orderby'] != 'creation_date' || $params['orderby'] != 'view_count' || $params['orderby'] != 'member_count' || $params['orderby'] != 'title')) {
          $select->order("distance");
        }
    } elseif (empty($params['sitepage_street']) && empty($params['sitepage_city']) && empty($params['sitepage_state']) && empty($params['sitepage_country'])) {
      $select->joinLeft($locationName, "$rName.seao_locationid = $locationName.locationitem_id");
    }

    if (!empty($params['users']) && isset($params['users'])) {
      $str = (string) ( is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users'] );
      $select->where($rName . '.user_id in (?)', new Zend_Db_Expr($str));
    } elseif (empty($params['users']) && isset($params['view_view']) && $params['view_view'] == '2') {
      $select->where($rName . '.user_id = ?', '0');
    }

    if (isset($params['orderby']) && !empty($params['orderby']) && ($params['orderby'] == 'creation_date' || $params['orderby'] == 'view_count' || $params['orderby'] == 'member_count' || $params['orderby'] == 'title')) {
      switch ($params['orderby']) {
        case "creation_date":
          $select->order($rName . '.creation_date DESC');
          break;
        case "view_count":
          $select->order($rName . '.view_count DESC');
          break;
        case "member_count":
          $select->order($rName . '.member_count DESC');
          break;
        case "title":
          $select->order($rName . '.displayname ASC');
          break;
      }
    } /* else {
      $select->order($rName . '.creation_date DESC');
      } */



    // Build the photo and is online part of query
    if (isset($params['level_id']) && !empty($params['level_id'])) {
      $select->where($rName . '.level_id = ?', $params['level_id']);
    }


    // Build the photo and is online part of query
    if (isset($params['has_photo']) && !empty($params['has_photo'])) {
      $select->where($rName . '.photo_id != ?', "0");
    }

    if (isset($params['is_online']) && !empty($params['is_online'])) {
      $select
              ->joinRight("engine4_user_online", "engine4_user_online.user_id = `{$rName}`.user_id", null)
              ->group("engine4_user_online.user_id")
              ->where($rName . '.user_id != ?', "0");
    }

    if (isset($params['network_id']) && !empty($params['network_id'])) {
      $select->joinRight("engine4_network_membership", "engine4_network_membership.user_id = `{$rName}`.user_id", null);
      $select->where($networkTableName . '.resource_id = ?', $params['network_id']);
    }

    //Could we use the search indexer for this?
    if (!empty($params['displayname'])) {
      $select->where($rName . ".username LIKE ? OR " . $rName . ".displayname LIKE ? ", '%' . $params['displayname'] . '%');
    }

    return $select;
  }

  /**
   * Get member online
   *
   * @param int $user_id
   * @return int $flag;
   */
  public function isOnline($user_id) {

    // Get online users
    $table = Engine_Api::_()->getItemTable('user');
    $onlineTable = Engine_Api::_()->getDbtable('online', 'user');

    $tableName = $table->info('name');
    $onlineTableName = $onlineTable->info('name');

    $select = $table->select()
            //->from($onlineTableName, null)
            //->joinLeft($tableName, $onlineTable.'.user_id = '.$tableName.'.user_id', null)
            ->from($tableName)
            ->joinRight($onlineTableName, $onlineTableName . '.user_id = ' . $tableName . '.user_id', null)
            //->where($onlineTableName.'.user_id > ?', 0)
            ->where($onlineTableName . '.user_id = ?', $user_id)
            //->where($onlineTableName.'.active > ?', new Zend_Db_Expr('DATE_SUB(NOW(),INTERVAL 20 MINUTE)'))
            ->where($tableName . '.search = ?', 1)
            ->where($tableName . '.enabled = ?', 1)
            ->order($onlineTableName . '.active DESC')
            ->group($onlineTableName . '.user_id');
    $row = $table->fetchRow($select);

    $flag = false;
    if (!empty($row)) {
      $flag = true;
    }
    return $flag;
  }

  /**
   * Get page select query
   *
   * @param array $params
   * @param array $customParams
   * @return string $select;
   */
  public function getVideosSelect($params = array()) {

    $table = Engine_Api::_()->getItemTable('video');
    $rName = $table->info('name');

    $locationTable = Engine_Api::_()->getDbtable('locationitems', 'seaocore');
    $locationName = $locationTable->info('name');

    $select = $table->select();
    $select = $select
            ->setIntegrityCheck(false)
            ->from($rName)
            ->where($rName . '.search = ?', '1');

    if (isset($params['video_street']) && !empty($params['video_street'])) {
      $select->join($locationName, "$rName.video_id = $locationName.resource_id   ", null);
      $select->where($locationName . '.formatted_address LIKE ? ', '%' . $params['video_street'] . '%');
    } if (isset($params['video_city']) && !empty($params['video_city'])) {
      $select->join($locationName, "$rName.video_id = $locationName.resource_id   ", null);
      $select->where($locationName . '.city = ?', $params['video_city']);
    } if (isset($params['video_state']) && !empty($params['video_state'])) {
      $select->join($locationName, "$rName.video_id = $locationName.resource_id   ", null);
      $select->where($locationName . '.state = ?', $params['video_state']);
    } if (isset($params['video_country']) && !empty($params['video_country'])) {
      $select->join($locationName, "$rName.video_id = $locationName.resource_id   ", null);
      $select->where($locationName . '.country = ?', $params['video_country']);
    }

    if ((isset($params['video_location']) && !empty($params['video_location']))) {
      if (isset($params['locationmiles']) && (!empty($params['locationmiles']))) {
        $longitude = 0;
        $latitude = 0;

        //check for zip code in location search.
        if (empty($params['Latitude']) && empty($params['Longitude'])) {
          $selectLocQuery = $locationTable->select()->where('location = ?', $params['video_location']);
          $locationValue = $locationTable->fetchRow($selectLocQuery);

          if (empty($locationValue)) {
            $locationResults = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getLatLong(array('location' => $params['sitepage_location'], 'module' => 'Geo-Location, Geo-Tagging, Check-Ins & Proximity Search'));
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

        $radius = $params['locationmiles']; //in miles

        $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.proximity.search.kilometer', 0);
        if (!empty($flage)) {
          $radius = $radius * (0.621371192);
        }
       $latitudeSin = "sin(radians($latitude))";
        $latitudeCos = "cos(radians($latitude))";
        $select->join($locationName, "$rName.seao_locationid = $locationName.locationitem_id   ", array("latitude", "longitude", "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance"));
        $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
        $sqlstring .= ")";
        $select->where($sqlstring);
        $select->order("distance");
      } else {
        $select->join($locationName, "$rName.seao_locationid = $locationName.locationitem_id");
        $select->where("`{$locationName}`.formatted_address LIKE ? or `{$locationName}`.location LIKE ? or `{$locationName}`.city LIKE ? or `{$locationName}`.state LIKE ?", "%" . urldecode($params['video_location']) . "%");
        //}
      }
    } elseif (!empty($params['latitude']) && !empty($params['longitude'])) {
      $radius = Engine_Api::_()->getApi('settings', 'core')->getSetting('sgl.geolocation.range', 100); // in miles
      $latitude = $params['latitude'];
      $longitude = $params['longitude'];
      $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.proximity.search.kilometer', 0);
      if (!empty($flage)) {
        $radius = $radius * (0.621371192);
      }
    $latitudeSin = "sin(radians($latitude))";
        $latitudeCos = "cos(radians($latitude))";
      $select->join($locationName, "$rName.seao_locationid = $locationName.locationitem_id", array("(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance"));
      $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
      $sqlstring .= ")";
      $select->where($sqlstring);
      $select->order("distance");
    } elseif (empty($params['video_street']) && empty($params['video_city']) && empty($params['video_state']) && empty($params['video_country'])) {
      $select->joinLeft($locationName, "$rName.seao_locationid = $locationName.locationitem_id");
    }

    if (!empty($params['category_id'])) {
      $select->where($rName . '.category_id = ?', $params['category_id']);
    }

    if (!empty($params['users']) && isset($params['users'])) {
      $str = (string) ( is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users'] );
      $select->where($rName . '.owner_id in (?)', new Zend_Db_Expr($str));
    } elseif (empty($params['users']) && $params['view_view'] == '1') {
      $select->where($rName . '.owner_id = ?', '0');
    }

    if (isset($params['orderby']) && !empty($params['orderby'])) {
      switch ($params['orderby']) {
        case "creation_date":
          $select->order($rName . '.creation_date DESC');
          break;
        case "view_count":
          $select->order($rName . '.view_count DESC');
          break;
        case "comment_count":
          $select->order($rName . '.comment_count DESC');
          break;
        case "rating":
          $select->order($rName . '.rating DESC');
          break;
      }
    }

    if (!empty($params['search'])) {
      $select->where($rName . ".title LIKE ? OR " . $rName . ".description LIKE ? ", '%' . $params['search'] . '%');
    }
    return $select;
  }

  /**
   * Get page select query
   *
   * @param array $params
   * @param array $customParams
   * @return string $select;
   */
  public function getAlbumsSelect($params = array()) {

    $table = Engine_Api::_()->getItemTable('album');
    $rName = $table->info('name');

    $locationTable = Engine_Api::_()->getDbtable('locationitems', 'seaocore');
    $locationName = $locationTable->info('name');

    $select = $table->select();
    $select = $select
            ->setIntegrityCheck(false)
            ->from($rName)
            ->where($rName . '.search = ?', '1');

    if (isset($params['album_street']) && !empty($params['album_street'])) {
      $select->join($locationName, "$rName.album_id = $locationName.resource_id   ", null);
      $select->where($locationName . '.formatted_address LIKE ? ', '%' . $params['album_street'] . '%');
    } if (isset($params['album_city']) && !empty($params['album_city'])) {
      $select->join($locationName, "$rName.album_id = $locationName.resource_id   ", null);
      $select->where($locationName . '.city = ?', $params['album_city']);
    } if (isset($params['album_state']) && !empty($params['album_state'])) {
      $select->join($locationName, "$rName.album_id = $locationName.resource_id   ", null);
      $select->where($locationName . '.state = ?', $params['album_state']);
    } if (isset($params['album_country']) && !empty($params['album_country'])) {
      $select->join($locationName, "$rName.album_id = $locationName.resource_id   ", null);
      $select->where($locationName . '.country = ?', $params['album_country']);
    }

    if ((isset($params['album_location']) && !empty($params['album_location']))) {
      if (isset($params['locationmiles']) && (!empty($params['locationmiles']))) {
        $longitude = 0;
        $latitude = 0;

        //check for zip code in location search.
        if (empty($params['Latitude']) && empty($params['Longitude'])) {
          $selectLocQuery = $locationTable->select()->where('location = ?', $params['album_location']);
          $locationValue = $locationTable->fetchRow($selectLocQuery);

          if (empty($locationValue)) {
            $locationResults = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getLatLong(array('location' => $params['sitepage_location'], 'module' => 'Geo-Location, Geo-Tagging, Check-Ins & Proximity Search'));
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

        $radius = $params['locationmiles']; //in miles

        $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.proximity.search.kilometer', 0);
        if (!empty($flage)) {
          $radius = $radius * (0.621371192);
        }
       $latitudeSin = "sin(radians($latitude))";
        $latitudeCos = "cos(radians($latitude))";
        $select->join($locationName, "$rName.seao_locationid = $locationName.locationitem_id   ", array("latitude", "longitude", "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance"));
        $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
        $sqlstring .= ")";
        $select->where($sqlstring);
        $select->order("distance");
      } else {
        $select->join($locationName, "$rName.seao_locationid = $locationName.locationitem_id");
        $select->where("`{$locationName}`.formatted_address LIKE ? or `{$locationName}`.location LIKE ? or `{$locationName}`.city LIKE ? or `{$locationName}`.state LIKE ?", "%" . urldecode($params['album_location']) . "%");
        //}
      }
    } elseif (!empty($params['latitude']) && !empty($params['longitude'])) {
      $radius = Engine_Api::_()->getApi('settings', 'core')->getSetting('sgl.geolocation.range', 100); // in miles
      $latitude = $params['latitude'];
      $longitude = $params['longitude'];
      $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.proximity.search.kilometer', 0);
      if (!empty($flage)) {
        $radius = $radius * (0.621371192);
      }
    $latitudeSin = "sin(radians($latitude))";
        $latitudeCos = "cos(radians($latitude))";
      $select->join($locationName, "$rName.seao_locationid = $locationName.locationitem_id", array("(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance"));
      $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
      $sqlstring .= ")";
      $select->where($sqlstring);
      $select->order("distance");
    } elseif (empty($params['album_street']) && empty($params['album_city']) && empty($params['album_state']) && empty($params['album_country'])) {
      $select->joinLeft($locationName, "$rName.seao_locationid = $locationName.locationitem_id");
    }

    if (!empty($params['category_id'])) {
      $select->where($rName . '.category_id = ?', $params['category_id']);
    }

    if (!empty($params['users']) && isset($params['users'])) {
      $str = (string) ( is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users'] );
      $select->where($rName . '.owner_id in (?)', new Zend_Db_Expr($str));
    } elseif (empty($params['users']) && $params['view_view'] == '1') {
      $select->where($rName . '.owner_id = ?', '0');
    }

    if (isset($params['orderby']) && !empty($params['orderby'])) {
      switch ($params['orderby']) {
        case "creation_date":
          $select->order($rName . '.modified_date DESC');
          break;
        case "view_count":
          $select->order($rName . '.view_count DESC');
          break;
        case "comment_count":
          $select->order($rName . '.comment_count DESC');
          break;
      }
    }

    if (!empty($params['search'])) {
      $select->where($rName . ".title LIKE ? OR " . $rName . ".description LIKE ? ", '%' . $params['search'] . '%');
    }
    return $select;
  }
}