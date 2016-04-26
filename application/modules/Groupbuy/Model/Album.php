<?php
class Groupbuy_Model_Album extends Core_Model_Item_Collection
{
  protected $_parent_type = 'groupbuy';

  protected $_owner_type = 'groupbuy_deal';

  protected $_children_types = array('groupbuy_photo');
	
  protected $_searchTriggers = false;

  protected $_collectible_type = 'groupbuy_photo';

  public function getHref($params = array())
  {
    return $this->getDeal()->getHref($params);
  }

  public function getDeal()
  {
    return $this->getOwner();
  }

  public function getAuthorizationItem()
  {
    return $this->getParent('deal');
  }

  protected function _delete()
  {
    // Delete all child posts
    $photoTable = Engine_Api::_()->getItemTable('groupbuy_photo');
    $photoSelect = $photoTable->select()->where('album_id = ?', $this->getIdentity());
    foreach( $photoTable->fetchAll($photoSelect) as $dealPhoto ) {
      $dealPhoto->delete();
    }

    parent::_delete();
  }
}