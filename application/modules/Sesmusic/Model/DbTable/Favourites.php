<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Favourites.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Model_DbTable_Favourites extends Engine_Db_Table {

  protected $_rowClass = "Sesmusic_Model_Favourite";

  public function isFavourite($params = array()) {

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    return $this->select()
                    ->where('resource_type = ?', $params['resource_type'])
                    ->where('resource_id = ?', $params['resource_id'])
                    ->where('user_id = ?', $viewer_id)
                    ->query()
                    ->fetchColumn();
  }

  public function getFavouriteSelect(Core_Model_Item_Abstract $resource) {

    return $this->select()
                    ->where('resource_type = ?', $resource->getType())
                    ->where('resource_id = ?', $resource->getIdentity())
                    ->order('favourite_id ASC');
  }

  public function getFavourite(Core_Model_Item_Abstract $resource, Core_Model_Item_Abstract $poster) {

    $select = $this->getFavouriteSelect($resource)
            ->where('user_id = ?', $poster->getIdentity())
            ->limit(1);
    return $this->fetchRow($select);
  }

  public function addFavourite(Core_Model_Item_Abstract $resource, Core_Model_Item_Abstract $poster) {

    $row = $this->getFavourite($resource, $poster);
    if (null !== $row)
      throw new Core_Model_Exception('Already favourite');

    $row = $this->createRow();
    if (isset($row->resource_type))
      $row->resource_type = $resource->getType();
    $row->resource_id = $resource->getIdentity();
    $row->user_id = $poster->getIdentity();
    $row->save();
    if (isset($resource->favourite_count)) {
      $resource->favourite_count++;
      $resource->save();
    }
    return $row;
  }

  public function removeFavourite(Core_Model_Item_Abstract $resource, Core_Model_Item_Abstract $poster) {

    $row = $this->getFavourite($resource, $poster);
    if (null === $row)
      throw new Core_Model_Exception('No favourite to remove');

    $row->delete();
    if (isset($resource->favourite_count)) {
      $resource->favourite_count--;
      $resource->save();
    }
    return $this;
  }

  public function getFavourites($params = array()) {

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $select = $this->select()
            ->from($this->info('name'))
            ->where('resource_type =?', $params['resource_type'])
            ->where('user_id =?', $viewer_id);
    return Zend_Paginator::factory($select);
  }

}