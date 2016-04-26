<?php
class Socialstore_Model_ProductAlbum extends Core_Model_Item_Collection
{
  protected $_parent_type = 'social_product';

  protected $_owner_type = 'social_product';

  protected $_children_types = array('socialstore_product_photo');

  protected $_collectible_type = 'socialstore_product_photo';
  
  protected $_searchTriggers = false;

  public function getHref($params = array())
  {
    return $this->getProduct()->getHref($params);
  }

  public function getProduct()
  {
    return $this->getOwner();
  }

  public function getAuthorizationItem()
  {
    return $this->getParent('social_product');
  }

  protected function _delete()
  {
    // Delete all child posts
    $photoTable = Engine_Api::_()->getItemTable('socialstore_product_photo');
    $photoSelect = $photoTable->select()->where('album_id = ?', $this->getIdentity());
    foreach( $photoTable->fetchAll($photoSelect) as $productPhoto ) {
      $productPhoto->delete();
    }

    parent::_delete();
  }
}