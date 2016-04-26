<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    List
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Listing.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class List_Model_Listing extends Core_Model_Item_Abstract {

  protected $_parent_type = 'user';
  protected $_searchTriggers = array('title', 'body', 'search', 'draft', 'approved', 'closed', 'end_date');
  protected $_parent_is_owner = true;

  public function isSearchable() {

    return ( (!isset($this->search) || $this->search) && !empty($this->_searchTriggers) && is_array($this->_searchTriggers) && empty($this->closed) && $this->approved && !empty($this->draft) && ($this->end_date >= date("Y-m-d i:s:m", time()) || $this->end_date == ''));
  }

  public function getMediaType() {
    return 'listing';
  }

  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array()) {
    $slug = $this->getSlug();
    $params = array_merge(array(
        'route' => 'list_entry_view',
        'reset' => true,
        'user_id' => $this->owner_id,
        'listing_id' => $this->listing_id,
        'slug' => $slug,
            ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
                    ->assemble($params, $route, $reset);
  }

  /**
   * Return a truncate listing description
   * */
  public function getDescription() {
    $tmpBody = strip_tags($this->body);
    return ( Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody );
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

  /**
   * Set listing photo
   *
   * */
  public function setPhoto($photo) {
    if ($photo instanceof Zend_Form_Element_File) {
      $file = $photo->getFileName();
    } else if (is_array($photo) && !empty($photo['tmp_name'])) {
      $file = $photo['tmp_name'];
    } else if (is_string($photo) && file_exists($photo)) {
      $file = $photo;
    } else {
      throw new Engine_Exception('invalid argument passed to setPhoto');
    }

    $name = basename($file);
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
        'parent_type' => 'list_listing',
        'parent_id' => $this->getIdentity()
    );

    $image = Engine_Image::factory();
    $image->open($file)
            ->resize(720, 720)
            ->write($path . '/m_' . $name)
            ->destroy();

    //RESIZE IMAGE (PROFILE)
    $image = Engine_Image::factory();
    $image->open($file)
            ->resize(200, 400)
            ->write($path . '/p_' . $name)
            ->destroy();

    //RESIZE IMAGE (NORMAL)
    $image = Engine_Image::factory();
    $image->open($file)
            ->resize(140, 160)
            ->write($path . '/in_' . $name)
            ->destroy();

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
    $iSquare = $storage->create($path . '/is_' . $name, $params);

    $iMain->bridge($iProfile, 'thumb.profile');
    $iMain->bridge($iIconNormal, 'thumb.normal');
    $iMain->bridge($iSquare, 'thumb.icon');

    //REMOVE TEMP FILES
    @unlink($path . '/p_' . $name);
    @unlink($path . '/m_' . $name);
    @unlink($path . '/in_' . $name);
    @unlink($path . '/is_' . $name);

    //ADD TO ALBUM
    $viewer = Engine_Api::_()->user()->getViewer();
    $photoTable = Engine_Api::_()->getItemTable('list_photo');
    $listAlbum = $this->getSingletonAlbum();
    $photoItem = $photoTable->createRow();
    $photoItem->setFromArray(array(
        'listing_id' => $this->getIdentity(),
        'album_id' => $listAlbum->getIdentity(),
        'user_id' => $viewer->getIdentity(),
        'file_id' => $iMain->getIdentity(),
        'collection_id' => $listAlbum->getIdentity(),
    ));
    $photoItem->save();

    //UPDATE ROW
    $this->modified_date = date('Y-m-d H:i:s');
    $this->photo_id = $photoItem->file_id;
    $this->save();

    return $this;
  }

  /**
   * Set listing location
   *
   */
  public function setLocation() {
    $id = $this->listing_id;
    $locationFieldEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.locationfield', 1);
    if ($locationFieldEnable) {
      $list = $this;
      if (!empty($list))
        $location = $list->location;

      if (!empty($location)) {
        $locationTable = Engine_Api::_()->getDbtable('locations', 'list');
        $locationName = $locationTable->info('name');
        $locationRow = Engine_Api::_()->getDbtable('locations', 'list')->getLocation(array('id' => $id));
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
              $request_url = "https://maps.googleapis.com/maps/api/geocode/json?address=$urladdress&sensor=true";
              $ch = curl_init();
              $timeout = 5;
              curl_setopt($ch, CURLOPT_URL, $request_url);
              curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
              ob_start();
              curl_exec($ch);
              curl_close($ch);

              $get_value = ob_get_contents();
              if (empty($get_value)) {
                $get_value = @file_get_contents($request_url);
              }
              $json_resopnse = Zend_Json::decode($get_value);

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
                  $loctionV['listing_id'] = $id;
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
              //CREATE LISTING LOCATION
              $loctionV = array();
              if (empty($locationRow))
                $locationRow = $locationTable->createRow();
              $value = $getSEALocation->toarray();
							unset($value['location_id']);
              $value['listing_id'] = $id;
              $locationRow->setFromArray($value);
              $locationRow->save();
            } catch (Exception $e) {
              throw $e;
            }
          }
        } else {

          try {
            //CREATE LISTING LOCATION
            $loctionV = array();
            if (empty($locationRow))
              $locationRow = $locationTable->createRow();
            $value = $locationValue->toarray();
            unset($value['location_id']);
            $value['listing_id'] = $id;
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
    $photoTable = Engine_Api::_()->getItemTable('list_photo');
    $select = $photoTable->select()
            ->where('file_id = ?', $photo_id)
            ->limit(1);

    $photo = $photoTable->fetchRow($select);
    return $photo;
  }

  public function getSingletonAlbum() {
    $table = Engine_Api::_()->getItemTable('list_album');
    $select = $table->select()
            ->where('listing_id = ?', $this->getIdentity())
            ->order('album_id ASC')
            ->limit(1);

    $album = $table->fetchRow($select);

    if (null == $album) {
      $album = $table->createRow();
      $album->setFromArray(array(
          'title' => $this->getTitle(),
          'listing_id' => $this->getIdentity()
      ));
      $album->save();
    }

    return $album;
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
    if (null == $this->search) {
      $this->search = 1;
    }

    parent::_insert();
  }

  /**
   * Delete the listing and belongings
   * 
   */
  public function _delete() {

    $listing_id = $this->listing_id;
    $db = Engine_Db_Table::getDefaultAdapter();

    $db->beginTransaction();
    try {
      Engine_Api::_()->fields()->getTable('list_listing', 'search')->delete(array('item_id = ?' => $listing_id));
      Engine_Api::_()->fields()->getTable('list_listing', 'values')->delete(array('item_id = ?' => $listing_id));
      Engine_Api::_()->getDbtable('ratings', 'list')->delete(array('listing_id = ?' => $listing_id));
      Engine_Api::_()->getDbtable('reviews', 'list')->delete(array('listing_id = ?' => $listing_id));
      Engine_Api::_()->getDbtable('clasfvideos', 'list')->delete(array('listing_id = ?' => $listing_id));
      Engine_Api::_()->getDbtable('albums', 'list')->delete(array('listing_id = ?' => $listing_id));
      Engine_Api::_()->getDbtable('photos', 'list')->delete(array('listing_id = ?' => $listing_id));
      Engine_Api::_()->getDbtable('itemofthedays', 'list')->delete(array('listing_id = ?' => $listing_id));
      Engine_Api::_()->getDbtable('vieweds', 'list')->delete(array('listing_id = ?' => $listing_id));
      Engine_Api::_()->getDbtable('writes', 'list')->delete(array('listing_id = ?' => $listing_id));
      Engine_Api::_()->getDbtable('posts', 'list')->delete(array('listing_id = ?' => $listing_id));
      Engine_Api::_()->getDbtable('topics', 'list')->delete(array('listing_id = ?' => $listing_id));
      Engine_Api::_()->getDbtable('locations', 'list')->delete(array('listing_id = ?' => $listing_id));

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //DELETE LISTING
    parent::_delete();
  }

}