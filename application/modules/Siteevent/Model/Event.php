<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Event.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Model_Event extends Core_Model_Item_Abstract {

    // protected $_parent_type = 'user';
    protected $_searchTriggers = array('title', 'body', 'search', 'draft', 'approved', 'closed', 'location');
//  protected $_parent_is_owner = true;
    protected $_children_types = array('siteevent_occurrence');
    protected $_owner_type = 'user';
    protected $_package;
    protected $_statusChanged;
    
  public function isSearchable() {

   $condition1 = ( (!isset($this->search) || $this->search) && !empty($this->_searchTriggers) && is_array($this->_searchTriggers) && empty($this->closed) && $this->approved && empty($this->draft));
    $condition2 = 1;
    if (Engine_Api::_()->siteevent()->hasPackageEnable())
      $condition2 = ($this->expiration_date > date("Y-m-d H:i:s"));
    return ($condition1 && $condition2);

  }

    public function membership() {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('membership', 'siteevent'));
    }

    /**
     * Gets an absolute URL to the page to view this item
     *
     * @return string
     */
    public function getHref($params = array()) {

        //IF A EVENT DO NOT HAVE MORE THEN ONE OCCURRENCE WE WILL NOT APPEND OCCURENCE ID FOR THAT.
        if (!empty($this->repeat_params)) {
            $request = Zend_Controller_Front::getInstance()->getRequest();

            if (isset($params['occurrence_id']) && !empty($params['occurrence_id'])) {
                $occurrence_id = $params['occurrence_id'];
            } elseif (isset($request->occurrence_id) && !empty($request->occurrence_id))
                $occurrence_id = $request->occurrence_id;
            else
                $occurrence_id = Zend_Registry::isRegistered('occurrence_id') ? Zend_Registry::get('occurrence_id') : null;

            //ADDED THE CASE FOR ALL EVENT TYPE WIDGET SETTING
            if ((!isset($params['showEventType']) || (isset($params['showEventType']) && $params['showEventType'] != 'all')) && isset($this->occurrence_id)) {
                $occurrence_id = $this->occurrence_id;
            }

//            if (empty($occurrence_id)) {
//                $occurrence_id = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getAllOccurrenceDates($this->getIdentity(), 1);
//            }
//            if (empty($occurrence_id)) {
//                $occurrence_id = Engine_Api::_()->getDbTable('events', 'siteevent')->getNextOccurID($this->getIdentity());
//            }            

            if (!empty($occurrence_id))
                $params['occurrence_id'] = $occurrence_id;
        }



        if (isset($params['showEventType'])) {
            unset($params['showEventType']);
        }

        $slug = $this->getSlug();
        $params = array_merge(array(
            'route' => isset($params['occurrence_id']) ? "siteevent_entry_view_occurrence" : 'siteevent_entry_view',
            'reset' => true,
            'event_id' => $this->event_id,
            'slug' => $slug,
                ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);

        return Zend_Controller_Front::getInstance()->getRouter()
                        ->assemble($params, $route, $reset);
    }

    public function getLeaderList() {
        $table = Engine_Api::_()->getItemTable('siteevent_list');
        $select = $table->select()
                ->where('owner_id = ?', $this->getIdentity())
                ->where('title = ?', 'SITEEVENT_LEADERS')
                ->limit(1);

        $list = $table->fetchRow($select);

        if (null === $list) {
            $list = $table->createRow();
            $list->setFromArray(array(
                'owner_id' => $this->getIdentity(),
                'title' => 'SITEEVENT_LEADERS',
            ));
            $list->save();
        }

        return $list;
    }

    public function getLeadersIds() {
        $parent = $this->getParent();
        $settingsCoreApi = Engine_Api::_()->getApi('settings', 'core');
        $name = 'siteevent_event_leader_owner' . $parent->getType();
        if ($settingsCoreApi->$name && in_array($parent->getType(), array('sitestore_store', 'sitepage_page', 'sitebusiness_business', 'sitegroup_group'))) {
            $moduleName = strtolower($parent->getModuleName());
            $manageadminTable = Engine_Api::_()->getDbtable('manageadmins', $moduleName);
            $manageadminTableName = $manageadminTable->info('name');
            $id = str_replace('site', '', $moduleName) . "_id";
            $select = $manageadminTable->select()
                    ->from($manageadminTableName, 'user_id')
                    ->where("$id = ?", $parent->getIdentity());
            $selectLeaders = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
            $selectLeaders[] = $this->owner_id;
        } else {
            $listItemTable = Engine_Api::_()->getDbTable('listItems', 'siteevent');
            $listItemTableName = $listItemTable->info('name');

            $selectLeaders = $listItemTable->select()
                    ->from($listItemTableName, array('child_id'))
                    ->where("list_id = ?", $this->getLeaderList()->list_id)
                    ->query()
                    ->fetchAll(Zend_Db::FETCH_COLUMN);
            $selectLeaders[] = $this->owner_id;
        }
        return $selectLeaders;
    }

    //GET DEFAULT OCCURRENCE ID
    public function getOccurrenceId() {

        $siteeventTable = Engine_Api::_()->getDbTable('events', 'siteevent');
        $siteeventTableName = $siteeventTable->info('name');

        $occurrenceTable = Engine_Api::_()->getDbTable('occurrences', 'siteevent');
        $siteeventOccurTableName = $occurrenceTable->info('name');
        $select = $siteeventTable->select();
        $where = "$siteeventTableName.event_id = $this->event_id";

        $select = $select
                ->setIntegrityCheck(false)
                ->from($siteeventTableName, array());
        $select->join($siteeventOccurTableName, "$siteeventTableName.event_id = $siteeventOccurTableName.event_id", array('occurrence_id'))
                ->where($where)
                //->group("$siteeventOccurTableName.event_id")
                ->order("$siteeventOccurTableName.event_id ASC")
                ->limit(1);

        $occurrence_id = $select->query()->fetchColumn();
        return $occurrence_id;
    }

    public function getEventLedBys($showHostInfo) {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $itemTypeValue = $this->getParent()->getType();
        $eventOwnerLeader = 0;
        if ($itemTypeValue == 'sitereview_listing') {
            $item = Engine_Api::_()->getItem('sitereview_listing', $this->getParent()->getIdentity());
            $itemTypeValue = $itemTypeValue . $item->listingtype_id;
            $eventOwnerLeader = Engine_Api::_()->getApi('settings', 'core')->getSetting("siteevent.event.leader.owner.$itemTypeValue", 1);
        } elseif ($itemTypeValue != 'user') {
            $eventOwnerLeader = Engine_Api::_()->getApi('settings', 'core')->getSetting("siteevent.event.leader.owner.$itemTypeValue", 1);
        }

        if (!$eventOwnerLeader)
            $itemTypeValue = 'user';
        $eventHostId = 0;
        // CHECK EVENT HOST
        if ($showHostInfo && $itemTypeValue == 'user' && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.host', 1) && $this->host_type == 'user')
            $eventHostId = $this->host_id;


        $getLeaderList = $this->getLeaderList();
        $leaders = Engine_Api::_()->getItem('siteevent_list', $getLeaderList->getIdentity())->getAllChildren();

        $getCount = Count($leaders);

        $ledByString = $view->htmlLink($this->getOwner()->getHref(), $this->getOwner()->getTitle());

        if (!empty($eventHostId) && $this->getOwner()->getIdentity() == $eventHostId)
            $ledByString = '';

        $tempLeaderCount = 1;
        if (!empty($getCount)) {
            foreach ($leaders as $leader) {
                if (!empty($eventHostId) && $leader->getIdentity() == $eventHostId)
                    continue;

                if ($tempLeaderCount++ != 1)
                    $ledByString .= ', ' . $view->htmlLink($leader->getOwner()->getHref(), $leader->getOwner()->getTitle());
                else {
                    if (!empty($ledByString))
                        $ledByString .= ', ';
                    $ledByString .= $view->htmlLink($leader->getOwner()->getHref(), $leader->getOwner()->getTitle());
                }
            }
        }
        else {
            $parentTypeItem = Engine_Api::_()->getItem($this->getParent()->getType(), $this->getParent()->getIdentity());
            $ledByString = $view->htmlLink($parentTypeItem->getHref(), $parentTypeItem->getTitle());
        }

        return $ledByString;
    }

    public function getLedBys($isStringOnly = true) {
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        try{
            $itemTypeValue = $this->getParent()->getType();
        } catch (Exception $ex) {
            if (!$eventOwnerLeader)
            $itemTypeValue = 'user';
        }
        $eventOwnerLeader = 0;
        if ($itemTypeValue == 'sitereview_listing') {
            $item = Engine_Api::_()->getItem('sitereview_listing', $this->getParent()->getIdentity());
            $itemTypeValue = $itemTypeValue . $item->listingtype_id;
            $eventOwnerLeader = Engine_Api::_()->getApi('settings', 'core')->getSetting("siteevent.event.leader.owner.$itemTypeValue", 1);
        } elseif ($itemTypeValue != 'user') {
            $eventOwnerLeader = Engine_Api::_()->getApi('settings', 'core')->getSetting("siteevent.event.leader.owner.$itemTypeValue", 1);
        }

        if (!$eventOwnerLeader)
            $itemTypeValue = 'user';

        if ($itemTypeValue == 'user') {
            $getLeaderList = $this->getLeaderList();
            $leaders = Engine_Api::_()->getItem('siteevent_list', $getLeaderList->getIdentity())->getAllChildren();

            if (!$isStringOnly) {
                return $leaders;
            }

            $getCount = Count($leaders);
            $count = 0;

            $ledByString = $view->htmlLink($this->getOwner()->getHref(), $this->getOwner()->getTitle());

            if ($getCount > 0) {
                $ledByString .= ", ";
            }

            foreach ($leaders as $leader) {
                $count++;
                $ledByString .= $view->htmlLink($leader->getOwner()->getHref(), $leader->getOwner()->getTitle());
                if ($count < $getCount) {
                    $ledByString .= ", ";
                }
            }
        } else {
            $parentTypeItem = Engine_Api::_()->getItem($this->parent_type, $this->parent_id);
            $ledByString = $view->htmlLink($parentTypeItem->getHref(), $parentTypeItem->getTitle());
        }
        return $ledByString;
    }

    public function getHost() {
        if ($this->host_type && $this->host_id && Engine_Api::_()->hasItemType($this->host_type))
            return Engine_Api::_()->getItem($this->host_type, $this->host_id);
    }

    public function getHostName($isPhoto = false) {

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.host', 1))
            return false;

        $host = $this->getHost();
        if (!$host)
            return;

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        $hostHref = $view->htmlLink($host->getHref(), $host->getTitle());
       

       //IN SITEMOBILE ONLY HOSTNAME AND IMAGE WILL COME WITHOUT LINKS
       if(Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
        if (empty($isPhoto)){
        return $hostHref;}
          
        return array(
            'displayName' => $hostHref,
            'displayImage' => $view->htmlLink($host->getHref(), $view->itemPhoto($host, 'thumb.icon'), array('title' => $view->translate('Host: ') . $host->getTitle() )));
       }else{ 
         if (empty($isPhoto)){
         return $host->getTitle();}
          
         return array(
            'displayName' => $host->getTitle(),
            'displayImage' => $view->itemPhoto($host, 'thumb.icon'));
       }
    }

    /**
     * Return a truncate event description
     * */
    public function getDescription() {

        $tmpBody = strip_tags($this->body);
        return ( Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody );
    }

    // General
    public function getShortType($inflect = false) {

        if ($this->_identity) {

            if ($inflect) {
                return str_replace(' ', '', ucwords(str_replace('_', ' ', 'Event')));
            }

            return 'event';
        } else {
            return parent::getShortType($inflect);
        }
    }

    // General
    public function getMediaType() {

        return 'event';
    }

    /**
     * Return slug
     * */
    public function getSlug($str = null, $maxstrlen = 225) {

        if (null === $str) {
            $str = $this->title;
        }

        return Engine_Api::_()->seaocore()->getSlug($str, $maxstrlen);
    }

    /**
     * Return a category
     * */
    public function getCategory() {

        return Engine_Api::_()->getItem('siteevent_category', $this->category_id);
    }

    /**
     * Return Bottom Line
     * */
    public function getBottomLine() {

        $params = array();
        $reviewTable = Engine_Api::_()->getDbTable('reviews', 'siteevent');
        $params['resource_id'] = $this->event_id;
        $params['resource_type'] = $this->getType();
        $params['type'] = 'editor';
        $isEditorReviewed = $reviewTable->canPostReview($params);
        if (!empty($isEditorReviewed)) {
            $bottomLine = $reviewTable->getColumnValue($isEditorReviewed, 'title');

            if (!empty($bottomLine)) {
                return $bottomLine;
            }
        }

        return $this->getDescription();
    }

    /**
     * Return Editor Reviewed Date
     * */
    public function getEditorReviewedDate() {

        $params = array();
        $reviewTable = Engine_Api::_()->getDbTable('reviews', 'siteevent');
        $params['resource_id'] = $this->event_id;
        $params['resource_type'] = $this->getType();
        $params['type'] = 'editor';
        $isEditorReviewed = $reviewTable->canPostReview($params);
        if (!empty($isEditorReviewed)) {
            $editorReviewedDate = $reviewTable->getColumnValue($isEditorReviewed, 'creation_date');

            if (!empty($editorReviewedDate)) {
                return $editorReviewedDate;
            }
        }

        return date();
    }

    /**
     * Return keywords
     *
     * @param char separator 
     * @return keywords
     * */
    public function getKeywords($separator = ' ') {

        $keywords = array();
        foreach ($this->tags()->getTagMaps() as $tagmap) {
            $tag = $tagmap->getTag();
            $keywords[] = $tag->getTitle();
        }

        if (null == $separator) {
            return $keywords;
        }

        return join($separator, $keywords);
    }

//  public function getRichContent() {
//    
//    $photo_type = 'event';
//
//    if ($photo_type != 'user') {
//      return;
//    }
//
//    $view = Zend_Registry::get('Zend_View');
//    $view = clone $view;
//    $view->clearVars();
//    $view->addScriptPath('application/modules/Siteevent/views/scripts/');
//
//    $content = '';
//    // Render the thingy
//    $view->item = $this;
//    $content .= $view->render('activity-feed/_eventNonPhoto.tpl');
//    return $content;
//  }

    public function getPriceInfo($params = array()) {
        return array();
        //return Engine_Api::_()->getDbTable('priceinfo', 'siteevent')->getPriceDetails($this->event_id, $params);
    }

    public function getWheretoBuyMaxPrice() {
        return $this->price;
//    $max_price = 0;
//    $where_to_buy = Engine_Api::_()->getDbTable('priceinfo', 'siteevent')->getMaxPrice($this->event_id);
//    if (!empty($where_to_buy) && $where_to_buy > 0) {
//      $max_price = $where_to_buy;
//    }
//    return $max_price;
    }

    public function getWheretoBuyMinPrice() {
        return $this->price;
//    $min_price = 0;
//    $where_to_buy = Engine_Api::_()->getDbTable('priceinfo', 'siteevent')->getMinPrice($this->event_id);
//    if (!empty($where_to_buy) && $where_to_buy > 0) {
//      $min_price = $where_to_buy;
//    }
//    return $min_price;
    }

    public function getEditorReview() {

        $reveiwTable = Engine_Api::_()->getDbTable('reviews', 'siteevent');
        $select = $reveiwTable->getReviewsSelect(array('event_id' => $this->event_id, 'type' => 'editor', 'resource_type' => $this->getType(), 'resource_id' => $this->event_id));
        return $reveiwTable->fetchRow($select);
    }

    public function getNoOfReviews($type = null) {

        if ($type == 'user-review') {
            if (!empty($this->rating_editor))
                return $this->review_count - 1;
        }else {
            return $this->review_count;
        }
    }

    /**
     * Set event photo
     *
     * */
    public function setPhoto($photo) {

//         if (is_string($photo) && (strstr($photo, 'http')) || (strstr($photo, 'https'))) {
//             $file_exists = 1;
//         } else {
//             $file_exists = 0;
//         }

        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
        } else if (is_string($photo) && file_exists($photo) && empty($file_exists)) {
            $file = $photo;
        } /* elseif (is_string($photo) && fopen($photo, "r") && !empty($file_exists)) {
          $file = $photo;
          } */ else {
            throw new Engine_Exception('invalid argument passed to setPhoto');
        }

        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_type' => 'siteevent_event',
            'parent_id' => $this->getIdentity()
        );

        
        // Add autorotation for uploded images. It will work only for SocialEngine-4.8.9 Or more then.
        $hasVersion = Engine_Api::_()->seaocore()->usingLessVersion('core', '4.8.9');
        if(!empty($hasVersion)) {
          $image = Engine_Image::factory();
          $image->open($file)
                  ->resize(720, 720)
                  ->write($path . '/m_' . $name)
                  ->destroy();

          //RESIZE IMAGE (PROFILE)
          $image = Engine_Image::factory();
          $image->open($file)
                  ->resize(300, 500)
                  ->write($path . '/p_' . $name)
                  ->destroy();

          //RESIZE IMAGE (NORMAL)
          $image = Engine_Image::factory();
          $image->open($file)
                  ->resize(140, 160)
                  ->write($path . '/in_' . $name)
                  ->destroy();

          //RESIZE IMAGE (Midum)
          $image = Engine_Image::factory();
          $image->open($file)
                  ->resize(200, 200)
                  ->write($path . '/im_' . $name)
                  ->destroy();
        }else {          
          $image = Engine_Image::factory();
          $image->open($file)
                  ->autoRotate()
                  ->resize(720, 720)
                  ->write($path . '/m_' . $name)
                  ->destroy();

          //RESIZE IMAGE (PROFILE)
          $image = Engine_Image::factory();
          $image->open($file)
                  ->autoRotate()
                  ->resize(300, 500)
                  ->write($path . '/p_' . $name)
                  ->destroy();

          //RESIZE IMAGE (NORMAL)
          $image = Engine_Image::factory();
          $image->open($file)
                  ->autoRotate()
                  ->resize(140, 160)
                  ->write($path . '/in_' . $name)
                  ->destroy();

          //RESIZE IMAGE (Midum)
          $image = Engine_Image::factory();
          $image->open($file)
                  ->autoRotate()
                  ->resize(200, 200)
                  ->write($path . '/im_' . $name)
                  ->destroy();
        }        

        //RESIZE IMAGE (ICON)
        $image = Engine_Image::factory();
        $image->open($file);

        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)
                ->write($path . '/is_' . $name)
                ->destroy();

        //STORE
        $storage = Engine_Api::_()->storage();
        $iMain = $storage->create($path . '/m_' . $name, $params);
        $iProfile = $storage->create($path . '/p_' . $name, $params);
        $iIconNormal = $storage->create($path . '/in_' . $name, $params);
        $iIconNormalMidum = $storage->create($path . '/im_' . $name, $params);
        $iSquare = $storage->create($path . '/is_' . $name, $params);

        $iMain->bridge($iProfile, 'thumb.profile');
        $iMain->bridge($iIconNormalMidum, 'thumb.midum');
        $iMain->bridge($iIconNormal, 'thumb.normal');
        $iMain->bridge($iSquare, 'thumb.icon');

        //REMOVE TEMP FILES
        @unlink($path . '/p_' . $name);
        @unlink($path . '/m_' . $name);
        @unlink($path . '/in_' . $name);
        @unlink($path . '/is_' . $name);

        //ADD TO ALBUM
        $viewer = Engine_Api::_()->user()->getViewer();

        $photoTable = Engine_Api::_()->getItemTable('siteevent_photo');
        $rows = $photoTable->fetchRow($photoTable->select()->from($photoTable->info('name'), 'order')->order('order DESC')->limit(1));
        $order = 0;
        if (!empty($rows)) {
            $order = $rows->order + 1;
        }
        $siteeventAlbum = $this->getSingletonAlbum();
        $photoItem = $photoTable->createRow();
        $photoItem->setFromArray(array(
            'event_id' => $this->getIdentity(),
            'album_id' => $siteeventAlbum->getIdentity(),
            'user_id' => $viewer->getIdentity(),
            'file_id' => $iMain->getIdentity(),
            'collection_id' => $siteeventAlbum->getIdentity(),
            'order' => $order
        ));
        $photoItem->save();

        //UPDATE ROW
        $this->modified_date = date('Y-m-d H:i:s');
        $this->photo_id = $photoItem->file_id;
        $this->save();

        return $this;
    }

    /**
     * Set event location
     *
     */
    public function setLocation() {

        $id = $this->event_id;

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1)) {
            $siteevent = $this;
            if (!empty($siteevent))
                $location = $siteevent->location;

            if (!empty($location)) {
                $locationTable = Engine_Api::_()->getDbtable('locations', 'siteevent');
                $locationRow = $locationTable->getLocation(array('id' => $id));
                if (isset($_POST['locationParams']) && $_POST['locationParams']) {
                    if (is_string($_POST['locationParams']))
                        $_POST['locationParams'] = Zend_Json_Decoder::decode($_POST['locationParams']);
                    if ($_POST['locationParams']['location'] === $location) {
                        try {
                            $loctionV = $_POST['locationParams'];
                            $loctionV['event_id'] = $id;
                            $loctionV['zoom'] = 16;
                            if (empty($locationRow))
                                $locationRow = $locationTable->createRow();

                            $locationRow->setFromArray($loctionV);
                            $locationRow->save();
                        } catch (Exception $e) {
                            throw $e;
                        }
                        return;
                    }
                }
                $selectLocQuery = $locationTable->select()->where('location = ?', $location);
                $locationValue = $locationTable->fetchRow($selectLocQuery);

                $enableSocialengineaddon = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('seaocore');

                if (empty($locationValue)) {
                    $getSEALocation = array();
                    if (!empty($enableSocialengineaddon)) {
                        $getSEALocation = Engine_Api::_()->getDbtable('locations', 'seaocore')->getLocation(array('location' => $location));
                    }
                    if (empty($getSEALocation)) {

                        $locationLocal = $location;
                        $urladdress = urlencode($locationLocal);
                        $delay = 0;

                        //ITERATE THROUGH THE ROWS, GEOCODING EACH ADDRESS
                        $geocode_pending = true;
                        while ($geocode_pending) {

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
                                // Successful geocode
                                $geocode_pending = false;
                                $result = $json_resopnse['results'];

                                // Format: Longitude, Latitude, Altitude
                                $lat = $result[0]['geometry']['location']['lat'];
                                $lng = $result[0]['geometry']['location']['lng'];
                                $f_address = $result[0]['formatted_address'];
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
                                    }else if ($types_location == 'locality') {
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

                                try {
                                    $loctionV = array();
                                    $loctionV['event_id'] = $id;
                                    $loctionV['latitude'] = $lat;
                                    $loctionV['location'] = $locationLocal;
                                    $loctionV['longitude'] = $lng;
                                    $loctionV['formatted_address'] = $f_address;
                                    $loctionV['country'] = $country;
                                    $loctionV['state'] = $state;
                                    $loctionV['zipcode'] = $zip_code;
                                    $loctionV['city'] = $city;
                                    $loctionV['address'] = $address;
                                    $loctionV['zoom'] = 16;
                                    if (empty($locationRow))
                                        $locationRow = $locationTable->createRow();

                                    $locationRow->setFromArray($loctionV);
                                    $locationRow->save();
                                                     if (!empty($enableSocialengineaddon)) {
                   $location = Engine_Api::_()->getDbtable('locations', 'seaocore')->setLocation($loctionV);
                 }

                                } catch (Exception $e) {
                                    throw $e;
                                }
                            } else if (strcmp($status, "620") == 0) {
                                //SENT GEOCODE TO FAST
                                $delay += 100000;
                            } else {
                                // FAILURE TO GEOCODE
                                $geocode_pending = false;
                                echo "Address " . $locationLocal . " failed to geocoded. ";
                                echo "Received status " . $status . "\n";
                            }
                            usleep($delay);
                        }
                    } else {

                        try {
                            //CREATE EVENT LOCATION
                            $loctionV = array();
                            if (empty($locationRow))
                                $locationRow = $locationTable->createRow();
                            $value = $getSEALocation->toarray();
														unset($value['location_id']);
                            $value['event_id'] = $id;
                            $locationRow->setFromArray($value);
                            $locationRow->save();
                        } catch (Exception $e) {
                            throw $e;
                        }
                    }
                } else {

                    try {
                        //CREATE EVENT LOCATION
                        $loctionV = array();
                        if (empty($locationRow))
                            $locationRow = $locationTable->createRow();
                        $value = $locationValue->toarray();
                        unset($value['location_id']);
                        $value['event_id'] = $id;
                        $locationRow->setFromArray($value);
                        $locationRow->save();
                    } catch (Exception $e) {
                        throw $e;
                    }
                }
            }
        }
    }

    public function getPhoto($photo_id) {

        $photoTable = Engine_Api::_()->getItemTable('siteevent_photo');
        $select = $photoTable->select()
                ->where('file_id = ?', $photo_id)
                ->limit(1);

        $photo = $photoTable->fetchRow($select);
        return $photo;
    }

    public function getSingletonAlbum() {

        $table = Engine_Api::_()->getItemTable('siteevent_album');
        $select = $table->select()
                ->where('event_id = ?', $this->getIdentity())
                ->order('album_id ASC')
                ->limit(1);

        $album = $table->fetchRow($select);

        if (null == $album) {
            $album = $table->createRow();
            $album->setFromArray(array(
                'title' => $this->getTitle(),
                'event_id' => $this->getIdentity()
            ));
            $album->save();
        }

        return $album;
    }

    public function ratingBaseCategory($type = null) {

        $result = Engine_Api::_()->getDbtable('ratings', 'siteevent')->ratingbyCategory($this->getIdentity(), $type, $this->getType());
        $ratings = array();
        foreach ($result as $res) {
            $ratings[$res['ratingparam_id']] = $res['avg_rating'];
        }
        return $ratings;
    }

    public function getNumbersOfUserRating($type = null, $ratingparam_id = 0, $value = 0) {

        return $result = Engine_Api::_()->getDbtable('ratings', 'siteevent')->getNumbersOfUserRating($this->getIdentity(), $type, $ratingparam_id, $value, 0, $this->getType());
    }

    /**
     * Gets a url to the current photo representing this item. Return null if none
     * set
     *
     * @param string The photo type (null -> main, thumb, icon, etc);
     * @return string The photo url
     */
    public function getPhotoUrl($type = null) {

        $module = "Siteevent";
        $shorType = "event";

        if (empty($this->photo_id)) {

            $type = ( $type ? str_replace('.', '_', $type) : 'main' );
            return Zend_Registry::get('Zend_View')->layout()->staticBaseUrl . 'application/modules/' . $module . '/externals/images/nophoto_' . $shorType . '_'
                    . $type . '.png';
        } else {
            $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id, $type);
            if (!$file) {
                $type = ( $type ? str_replace('.', '_', $type) : 'main' );
                return Zend_Registry::get('Zend_View')->layout()->staticBaseUrl . 'application/modules/' . $module . '/externals/images/nophoto_' . $shorType . '_'
                        . $type . '.png';
            }

            return $file->map();
        }
    }

    public function updateAllCoverPhotos() {
        $photo = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id);
        if ($photo instanceof Storage_Model_File) {
            $file = $photo->temporary();
            $fileName = $photo->name;
            $name = basename($file);
            $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
            $params = array(
                'parent_type' => 'siteevent_event',
                'parent_id' => $this->getIdentity()
            );

            //STORE
            $storage = Engine_Api::_()->storage();
            $iMain = $photo;

            $thunmProfile = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id, 'thumb.profile');

            if (empty($thunmProfile) || empty($thunmProfile->parent_file_id)) {
                //RESIZE IMAGE (PROFILE)
                $image = Engine_Image::factory();
                $image->open($file)
                        ->resize(300, 500)
                        ->write($path . '/p_' . $name)
                        ->destroy();
                $iProfile = $storage->create($path . '/p_' . $name, $params);
                $iMain->bridge($iProfile, 'thumb.profile');
                @unlink($path . '/p_' . $name);
            }



            $thunmMidum = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id, 'thumb.midum');
            if (empty($thunmMidum) || empty($thunmMidum->parent_file_id)) {
                //RESIZE IMAGE (Midum)
                $image = Engine_Image::factory();
                $image->open($file)
                        ->resize(200, 200)
                        ->write($path . '/im_' . $name)
                        ->destroy();
                $iIconNormalMidum = $storage->create($path . '/im_' . $name, $params);
                $iMain->bridge($iIconNormalMidum, 'thumb.midum');
                //REMOVE TEMP FILES

                @unlink($path . '/m_' . $name);
            }
        }
    }

    /**
     * Gets a proxy object for the comment handler
     *
     * @return Engine_ProxyObject
     * */
    public function comments() {

        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
    }

    /**
     * Gets a proxy object for the like handler
     *
     * @return Engine_ProxyObject
     * */
    public function likes() {

        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
    }

    /**
     * Gets a proxy object for the tags handler
     *
     * @return Engine_ProxyObject
     * */
    public function tags() {

        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
    }

    /**
     * Insert global searching value
     *
     * */
    protected function _insert() {

        if (is_null($this->search)) {
            $this->search = 1;
        }

        parent::_insert();
    }

    /**
     * Delete the event and belongings
     * 
     */
    public function _delete() {

        $event_id = $this->event_id;
        $db = Engine_Db_Table::getDefaultAdapter();

        $db->beginTransaction();
        try {
            Engine_Api::_()->fields()->getTable('siteevent_event', 'search')->delete(array('item_id = ?' => $event_id));
            Engine_Api::_()->fields()->getTable('siteevent_event', 'values')->delete(array('item_id = ?' => $event_id));

            // Delete leader list
            $this->getLeaderList()->delete();

            // Delete all memberships
            $this->membership()->removeAllMembers();

            //DELETE EVENT DOCUMENTS
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventdocument')) {

                $documents = Engine_Api::_()->getItemTable('siteeventdocument_document')->getSiteeventDocuments($event_id, 0);

                foreach ($documents as $document) {
                    Engine_Api::_()->getItem('siteeventdocument_document', $document->document_id)->delete();
                }
            }

            //FETCH EVENT IDS
            $reviewTable = Engine_Api::_()->getDbtable('reviews', 'siteevent');
            $select = $reviewTable->select()
                    ->from($reviewTable->info('name'), 'review_id')
                    ->where('resource_id = ?', $this->event_id)
                    ->where('resource_type = ?', 'siteevent_event');
            $reviewDatas = $reviewTable->fetchAll($select);
            foreach ($reviewDatas as $reviewData) {
                Engine_Api::_()->getItem('siteevent_review', $reviewData->review_id)->delete();
            }

            //FETCH EVENT VIDEO IDS
            $reviewVideoTable = Engine_Api::_()->getDbtable('videos', 'siteevent');
            $selectVideoTable = $reviewVideoTable->select()
                    ->from($reviewVideoTable->info('name'), 'video_id')
                    ->where('event_id = ?', $this->event_id);
            $reviewVideoDatas = $reviewVideoTable->fetchAll($selectVideoTable);
            foreach ($reviewVideoDatas as $reviewVideoData) {
                Engine_Api::_()->getItem('siteevent_video', $reviewVideoData->video_id)->delete();
            }

            Engine_Api::_()->getDbtable('clasfvideos', 'siteevent')->delete(array('event_id = ?' => $event_id));
            Engine_Api::_()->getDbtable('albums', 'siteevent')->delete(array('event_id = ?' => $event_id));
            Engine_Api::_()->getDbtable('photos', 'siteevent')->delete(array('event_id = ?' => $event_id));
            Engine_Api::_()->getDbtable('vieweds', 'siteevent')->delete(array('event_id = ?' => $event_id));
            Engine_Api::_()->getDbtable('posts', 'siteevent')->delete(array('event_id = ?' => $event_id));
            Engine_Api::_()->getDbtable('topics', 'siteevent')->delete(array('event_id = ?' => $event_id));
            Engine_Api::_()->getDbtable('topicWatches', 'siteevent')->delete(array('resource_id = ?' => $event_id));
            Engine_Api::_()->getDbtable('locations', 'siteevent')->delete(array('event_id = ?' => $event_id));
            Engine_Api::_()->getDbtable('otherinfo', 'siteevent')->delete(array('event_id = ?' => $event_id));
            
            Engine_Api::_()->getDbtable('userreviews', 'siteevent')->delete(array('event_id = ?' => $event_id));
    
            //GET DIARIES HAVING THIS EVENT ID
            $diaryTable = Engine_Api::_()->getDbtable('diaries', 'siteevent');
            $diaryTableName = $diaryTable->info('name');
            $diaryMapTable = Engine_Api::_()->getDbtable('diarymaps', 'siteevent');
            $diaryMapTableName = $diaryMapTable->info('name');

            $diaryDatas = $diaryTable->select()
                    ->from($diaryTableName, 'diary_id')
                    ->where('event_id = ?', $event_id)
                    ->query()
                    ->fetchAll(Zend_Db::FETCH_COLUMN);

            if (!empty($diaryDatas)) {
                $select = $diaryMapTable->select()
                        ->from($diaryMapTableName)
                        ->where($diaryMapTableName . '.event_id != ?', $event_id)
                        ->where("diary_id IN (?)", (array) $diaryDatas)
                        ->order('date ASC')
                        ->group($diaryMapTableName . '.diary_id');

                $diaryMapDatas = $diaryMapTable->fetchAll($select);
                if (!empty($diaryMapDatas)) {

                    $diaryMapDatas = $diaryMapDatas->toArray();
                    foreach ($diaryMapDatas as $diaryMapData) {
                        $diaryTable->update(array('event_id' => $diaryMapData['event_id']), array('diary_id = ?' => $diaryMapData['diary_id'], 'event_id = ?' => $event_id));
                    }
                }
            }

            $diaryTable->update(array('event_id' => 0), array('event_id = ?' => $event_id));
            $diaryMapTable->delete(array('event_id = ?' => $event_id));

            //FETCH OCCURRENCES IDS
            $occurrenceTable = Engine_Api::_()->getDbtable('occurrences', 'siteevent');
            $select = $occurrenceTable->select()
                    ->from($occurrenceTable->info('name'), 'occurrence_id')
                    ->where('event_id = ?', $event_id);
            $occurrenceDatas = $occurrenceTable->fetchAll($select);
            foreach ($occurrenceDatas as $occurrenceData) {
                
                $waitlistTable = Engine_Api::_()->getDbtable('waitlists', 'siteevent');
                $select = $waitlistTable->select()
                        ->from($waitlistTable->info('name'))
                        ->where('occurrence_id = ?', $occurrenceData->occurrence_id);
                $wishlistDatas = $waitlistTable->fetchAll($select);
                foreach($wishlistDatas as $wishlistData) {
                    $wishlistData->delete();
                } 
            }
            
            //DELETE EVENT FROM OCCURRENCE TABLE
            $occurrenceTable->delete(array('event_id = ?' => $event_id));

            //DELETE EVENT FROM MEMBERSHIP TABLE
            Engine_Api::_()->getDbtable('membership', 'siteevent')->delete(array('resource_id = ?' => $event_id));

            //DELETE EVENT ENTRY FROM FOLLOW TABLE
            Engine_Api::_()->getDbtable('follows', 'seaocore')->delete(array('resource_id = ?' => $event_id, 'resource_type =?' => 'siteevent_event'));

            //DELETE EVENT ENTRY FROM RATING TABLE
            $ratingTable = Engine_Api::_()->getDbtable('ratings', 'siteevent');
            $ratingTable->delete(array('review_id =?' => 0, 'resource_id =?' => $event_id, 'resource_type =?' => 'siteevent_event'));

            
            //DELETE EVENT FROM TICKET TABLE
            if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventticket')){
                Engine_Api::_()->getDbtable('tickets', 'siteeventticket')->delete(array('event_id = ?' => $event_id));
                
                //DELETE NOTIFICATIONS FOR ORDERS
                $orderTable = Engine_Api::_()->getDbtable('orders', 'siteeventticket');
                $select = $orderTable->select()
                        ->from($orderTable->info('name'))
                        ->where('event_id = ?', $event_id);
                $orderDatas = $orderTable->fetchAll($select);
                foreach($orderDatas as $orderData) {
                    Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('type = ?' => 'siteeventticket_order_place', 'object_type = ?' => 'siteeventticket_order', 'object_id = ?' => $orderData->order_id));
                }                 
            }          
            
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        //DELETE EVENT
        parent::_delete();
    }

    public function getAttendingCount() {
        return $this->membership()->getMemberCount(true, Array('rsvp' => 2));
    }

    public function getMaybeCount() {
        return $this->membership()->getMemberCount(true, Array('rsvp' => 1));
    }

    public function getNotAttendingCount() {
        return $this->membership()->getMemberCount(true, Array('rsvp' => 0));
    }

    public function getAwaitingReplyCount() {
        return $this->membership()->getMemberCount(false, Array('rsvp' => 3));
    }

    //FUNCTION WHICH WILL RETURN THE START AND END DATE OF AN EVENT OCCURRENCE.
    public function getStartEndDate($occurrence_id = null) {

        $results = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getEventDate($this->event_id, $occurrence_id);
        $datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium');
        //CONVERT TO LOCAL TIME.
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $results['starttime'] = $view->locale()->toEventDateTime($results['starttime'], array('size' => $datetimeFormat));
        $results['endtime'] = $view->locale()->toEventDateTime($results['endtime'], array('size' => $datetimeFormat));
        return $results;
    }

    public function isViewableByNetwork() {
        $regName = 'view_network_privacy_' . $this->getGuid();
        if (!Zend_Registry::isRegistered($regName)) {
            $flage = true;
            $enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.network', 0);
            $viewPricavyEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.networkprofile.privacy', 0);
            $viewer = Engine_Api::_()->user()->getViewer();
            if ($enableNetwork && $viewPricavyEnable && !$this->isOwner($viewer)) {
                $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
                $viewerNetworkIds = $networkMembershipTable->getMembershipsOfIds($viewer);
                if (Engine_Api::_()->siteevent()->listBaseNetworkEnable()) {

                    if ($this->networks_privacy) {
                        if (!empty($viewerNetworkIds)) {
                            $commanIds = array_intersect($this->networks_privacy, $viewerNetworkIds);
                            if (empty($commanIds))
                                $flage = false;
                        } else {
                            $flage = false;
                        }
                    }
                } else {
                    if (!empty($viewerNetworkIds)) {
                        $ownerNetworkIds = $networkMembershipTable->getMembershipsOfIds($this->getOwner('user'));
                        if ($ownerNetworkIds) {
                            $commanIds = array_intersect($ownerNetworkIds, $viewerNetworkIds);
                            if (empty($commanIds))
                                $flage = false;
                        }
                    }
                }
            }

            if (!$flage) {
                $row = $this->membership()->getRow($viewer);
                $flage = null !== $row && $row->resource_approved && $row->user_approved;
            }

            Zend_Registry::set($regName, $flage);
        } else {
            $flage = Zend_Registry::get($regName);
        }
        return $flage;
    }

    public function countOrganizedEvent() {
        return Engine_Api::_()->getDbTable('events', 'siteevent')->getSiteeventsPaginator(array('host_type' => $this->host_type, 'host_id' => $this->host_id, 'action' => 'all'))->getTotalItemCount();
    }

    //FUNCTION WHICH WILL RETURN THE START AND END DATE OF AN EVENT OCCURRENCE.
//    public function getEventStartEndDate($occurrence_id = null) {
//
//        $results = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getEventDate($this->event_id, $occurrence_id);
//        //CONVERT TO LOCAL TIME.
////        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
////        $results['starttime'] = gmdate('M d,Y, g:i A e',strtotime($results['starttime']));//date('M d,Y h:i A', $results['starttime']);
////        $results['endtime'] = gmdate('M d,Y, g:i A e',strtotime($results['endtime']));//date('M d,Y h:i A', $results['endtime']);//$view->locale()->toEventDateTime($results['endtime']);
//        return $results;
//    }
//    public function authorization() {
//        $object = Engine_Api::_()->getDbtable('allow', 'authorization');
//        return new Engine_ProxyObject($this, $object);
//    }

    public function canDeletePrivacy($parent_type = null, $parent_id = null) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }
        if ($parent_type == 'sitepage_page' && Engine_Api::_()->hasItemType('sitepage_page')) {
            $sitepage = Engine_Api::_()->getItem('sitepage_page', $parent_id);
            //START MANAGE-ADMIN CHECK
            $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
            if ($viewer_id != $this->owner_id && !$isManageAdmin) {
                return false;
            } else {
                return true;
            }
        } elseif ($parent_type == 'sitebusiness_business' && Engine_Api::_()->hasItemType('sitebusiness_business')) {
            $sitebusiness = Engine_Api::_()->getItem('sitebusiness_business', $parent_id);
            //START MANAGE-ADMIN CHECK
            $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($sitebusiness, 'edit');
            if ($viewer_id != $this->owner_id && !$isManageAdmin) {
                return false;
            } else {
                return true;
            }
        } elseif ($parent_type == 'sitegroup_group' && Engine_Api::_()->hasItemType('sitegroup_group')) {
            $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $parent_id);
            //START MANAGE-ADMIN CHECK
            $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
            if ($viewer_id != $this->owner_id && !$isManageAdmin) {
                return false;
            } else {
                return true;
            }
        } elseif ($parent_type == 'sitestore_store' && Engine_Api::_()->hasItemType('sitestore_store')) {
            $sitestore = Engine_Api::_()->getItem('sitestore_store', $parent_id);
            //START MANAGE-ADMIN CHECK
            $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
            if ($viewer_id != $this->owner_id && !$isManageAdmin) {
                return false;
            } else {
                return true;
            }
        }

        $can_delete = Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "delete");
        //AUTHORIZATION CHECK
        if (!$can_delete) {
            return false;
        }

        return true;
    }

    public function canView($user = null) {
        if (!($user instanceof User_Model_User))
            $user = Engine_Api::_()->user()->getViewer();
        $can_view = $this->isViewableByNetwork();
        if ($can_view) {
            $can_view = Engine_Api::_()->authorization()->isAllowed($this->getType(), $user, 'view');
        } else {
            $can_view = Engine_Api::_()->authorization()->isAllowed($this->getType(), $user, 'edit');
        }
        return $can_view;
    }

    public function getCategoryName() {
        $category = $this->getCategory();

        return !empty($category) ? $category->category_name : '';
    }

    public function getNextOccurrenceDateTime() {
        $nextOccurrenceDateTime = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getNextOccurrenceDateTime($this->event_id);
        return $nextOccurrenceDateTime;
    }

		/**
		* Gets a proxy object for the follow handler
		*
		* @return Engine_ProxyObject
		* */
		public function follows() {
			return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('follows', 'seaocore'));
		}

    public function getPackage() {
    if (empty($this->package_id)) {
      return null;
    }
    if (null === $this->_package) {
      $this->_package = Engine_Api::_()->getItem('siteeventpaid_package', $this->package_id);
    }
    return $this->_package;
  }

  // Active
  public function setActive($flag = true, $deactivateOthers = null) {

    $this->approved = true;
    $this->pending = 0;
    $viewer = Engine_Api::_()->user()->getViewer();
    if (empty($this->approved_date)) {
      $this->approved_date = date('Y-m-d H:i:s');
      if ($this->draft == 0 && $this->search) { 
        //ATTACH ACTIVITY FEED
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $this, 'siteevent_new');

        if ($action != null) {
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $this);
        }
        
        //ON COMPLETION OF PAYMENT - SEND NOTIFICATION & EMAIL TO HOST.
        Engine_Api::_()->siteevent()->sendNotificationToHost($this->event_id);
      }
    }

    $this->save();
    return $this;
  }

  public function clearStatusChanged() {
    $this->_statusChanged = null;
    return $this;
  }

  public function didStatusChange() {
    return (bool) $this->_statusChanged;
  }

  public function cancel($is_upgrade_package = false) {

    $gateway_profile_id = Engine_Api::_()->getDbTable('otherinfo', 'siteevent')->getColumnValue($this->event_id, 'gateway_profile_id');
    $gateway_id = Engine_Api::_()->getDbTable('otherinfo', 'siteevent')->getColumnValue($this->event_id, 'gateway_id');
    
    // Try to cancel recurring payments in the gateway
    if (!empty($gateway_id) && !empty($gateway_profile_id)) {
      try {
        $gateway = Engine_Api::_()->getItem('siteeventpaid_gateway', $gateway_id);
        $gatewayPlugin = $gateway->getPlugin();
        if (method_exists($gatewayPlugin, 'cancelEvent')) {
          $gatewayPlugin->cancelEvent($gateway_profile_id);
        }
      } catch (Exception $e) {
        // Silence?
      }
    }
    // Cancel this row
    if($is_upgrade_package){
      $this->approved = false; // Need to do this to prevent clearing the user's session
    }
    $this->onCancel();
    return $this;
  }

  public function onCancel() {
    $this->_statusChanged = false;
    if (in_array($this->status, array('initial', 'trial', 'pending', 'active', 'overdue', 'cancelled'))) {
      // Change status
      if ($this->status != 'cancelled') {
        $this->status = 'cancelled';
        $this->_statusChanged = true;
      }
    }
    $this->save();
    return $this;
  }

  public function onPaymentSuccess() {
    $this->_statusChanged = false;
    if (in_array($this->status, array('initial', 'trial', 'pending', 'active', 'overdue', 'expired'))) {

      if (in_array($this->status, array('initial', 'pending', 'overdue'))) {
        //ON COMPLETION OF PAYMENT - ADD ACTIVITY FEED & SEND NOTIFICATION TO HOST.
        $this->setActive(true);
      }

      // Update expiration to expiration + recurrence or to now + recurrence?
      $package = $this->getPackage();
      $expiration = $package->getExpirationDate();
      $diff_days = 0;
      if ($package->isOneTime() && !empty($this->expiration_date) && $this->expiration_date !== '0000-00-00 00:00:00') {
        $diff_days = round((strtotime($this->expiration_date) - strtotime(date('Y-m-d H:i:s'))) / 86400);
      }
      if ($expiration) {
        $date = date('Y-m-d H:i:s', $expiration);

        if ($diff_days >= 1) {

          $diff_days_expiry = round((strtotime($date) - strtotime(date('Y-m-d H:i:s'))) / 86400);
          $incrmnt_date = date('d', time()) + $diff_days_expiry + $diff_days;
          $incrmnt_date = date('Y-m-d H:i:s', mktime(date("H"), date("i"), date("s"), date("m"), $incrmnt_date));
        } else {
          $incrmnt_date = $date;
        }
        $this->expiration_date = $incrmnt_date;
      } else {
        $this->expiration_date = '2250-01-01 00:00:00';
      }

      // Change status
      if ($this->status != 'active') {
        $this->status = 'active';
        $this->_statusChanged = true;
      }
    }
    $this->save();
    return $this;
  }

  public function onPaymentFailure() {
    $this->_statusChanged = false;
    if (in_array($this->status, array('initial', 'trial', 'pending', 'active', 'overdue', 'expired'))) {
      // Change status
      if ($this->status != 'overdue') {
        $this->status = 'overdue';
        $this->_statusChanged = true;
      }
    }
    $this->save();
    return $this;
  }

  public function onPaymentPending() {
    $this->_statusChanged = false;
    if (in_array($this->status, array('initial', 'trial', 'pending', 'active', 'overdue', 'expired'))) {
      // Change status
      if ($this->status != 'pending') {
        $this->status = 'pending';
        $this->_statusChanged = true;
      }
    }
    $this->save();
    return $this;
  }

  public function onExpiration() {
    $this->_statusChanged = false;
    if (in_array($this->status, array('initial', 'trial', 'pending', 'active', 'expired'))) {
      // Change status
      if ($this->status != 'expired') {
        $this->status = 'expired';
        $this->_statusChanged = true;
      }
    }
    $this->save();
    return $this;
  }

  public function onRefund() {
    $this->_statusChanged = false;
    if (in_array($this->status, array('initial', 'trial', 'pending', 'active', 'refunded'))) {
      // Change status
      if ($this->status != 'refunded') {
        $this->status = 'refunded';
        $this->_statusChanged = true;
      }
    }
    $this->save();
    return $this;
  }

  /**
   * Process ipn of page transaction
   *
   * @param Payment_Model_Order $order
   * @param Engine_Payment_Ipn $ipn
   */
  public function onPaymentIpn(Payment_Model_Order $order, Engine_Payment_Ipn $ipn) {
    $gateway = Engine_Api::_()->getItem('siteeventpaid_gateway', $order->gateway_id);
    $gateway->getPlugin()->onPageTransactionIpn($order, $ipn);
    return true;
  }

  /**
   * Get status of event
   * $params object $event
   * @return string
   * */
  public function getEventStatus() {

    $translate = Zend_Registry::get('Zend_Translate');
    if (!empty($this->declined)) {
      return "<span style='color: red;'>" . $translate->translate("Declined") . "</span>";
    }

    if (!empty($this->pending)) {
      return $translate->translate("Approval Pending");
    }
    if (!empty($this->approved)) {
      return $translate->translate("Approved");
    }


    if (empty($this->approved)) {
      return $translate->translate("Dis-Approved");
    }

    return "Approved";
  }

  /**
   * Get expiry date for event
   * $params object $event
   * @return date
   * */
  public function getExpiryDate() {
    $translate = Zend_Registry::get('Zend_Translate');
    if ($this->expiration_date === "2250-01-01 00:00:00")
      return $translate->translate('Never Expires');
    else {
      if (strtotime($this->expiration_date) < time())
        return "Expired";

      return date("M d,Y g:i A", strtotime($this->expiration_date));
    }
  } 
  
  public function isEventFull($params = array()) {
      
        //WHEN CAPACITY IS NOT FETCHED IN QUERY
        $thisArray = $this->toArray();
        if(!isset($this->capacity) || !isset($thisArray['capacity'])) {
            $capacity = Engine_Api::_()->getDbTable('events', 'siteevent')->getEventAttribute($this->event_id, 'capacity');
        }
        else {
            $capacity = $this->capacity;
        }
      
        if(empty($capacity)) {
            return 0;
        }
        
        if(empty($params['occurrence_id'])) {
            $params['occurrence_id'] = Zend_Registry::isRegistered('occurrence_id') ? Zend_Registry::get('occurrence_id') : null;
        }
        
        if(!isset($params['doNotCheckCapacityFlag']) && !empty($params['occurrence_id']) && ((Engine_Api::_()->siteevent()->isTicketBasedEvent() && !$this->isRepeatEvent()) || !Engine_Api::_()->siteevent()->isTicketBasedEvent())) {
            $occurrence = Engine_Api::_()->getItem('siteevent_occurrence', $params['occurrence_id']);
            if(!empty($occurrence) && !empty($occurrence->waitlist_flag)) {
                return true;
            }
        }
        
        if(Engine_Api::_()->siteevent()->isTicketBasedEvent()) {
            
            $totalTickets = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->totalSoldTickets(array('occurrence_id' => $params['occurrence_id']));
            
            return ($totalTickets >= $this->capacity) ? $totalTickets : 0;
        }
        else {

            $totalMembers = Engine_Api::_()->getDbTable('membership', 'siteevent')->getOccurrenceMemberCount(array('occurrence_id' => $params['occurrence_id'], 'rsvp' => 2));
            
            return ($totalMembers >= $capacity) ? $totalMembers : 0;
        }
  }
  
  public function isRepeatEvent() {
      
      if(Engine_Api::_()->hasModuleBootstrap('siteeventrepeat')) {
          
        //WHEN REPEAT PARAMS IS NOT FETCHED IN QUERY
        $thisArray = $this->toArray();
        if(!isset($this->repeat_params) || !isset($thisArray['repeat_params'])) {
            $repeat_params = Engine_Api::_()->getDbTable('events', 'siteevent')->getEventAttribute($this->event_id, 'repeat_params');
        }      
        else {
            $repeat_params = $this->repeat_params;
        }          
          
        if(!empty($repeat_params)) {
          return true;
        }
      }
      
      return false;
  }
}