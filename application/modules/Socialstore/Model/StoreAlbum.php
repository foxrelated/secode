<?php
class Socialstore_Model_StoreAlbum extends Core_Model_Item_Collection
{
  protected $_parent_type = 'social_store';

  protected $_owner_type = 'social_store';

  protected $_children_types = array('socialstore_store_photo');

  protected $_collectible_type = 'socialstore_store_photo';

  protected $_searchTriggers = false;
  
  public function getHref($params = array())
  {
    return $this->getStore()->getHref($params);
  }

  public function getStore()
  {
    return $this->getOwner();
  }

  public function getAuthorizationItem()
  {
    return $this->getParent('social_store');
  }

  protected function _delete()
  {
    // Delete all child posts
    $photoTable = Engine_Api::_()->getItemTable('socialstore_store_photo');
    $photoSelect = $photoTable->select()->where('album_id = ?', $this->getIdentity());
    foreach( $photoTable->fetchAll($photoSelect) as $storePhoto ) {
      $storePhoto->delete();
    }

    parent::_delete();
  }
}