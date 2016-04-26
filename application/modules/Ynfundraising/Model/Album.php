<?php
class Ynfundraising_Model_Album extends Core_Model_Item_Collection
{
  protected $_searchTriggers = false;
  protected $_parent_type = 'campaign';

  protected $_owner_type = 'campaign';

  protected $_children_types = array('ynfundraising_photo');

  protected $_collectible_type = 'ynfundraising_photo';

  public function getHref($params = array())
  {
    return $this->getCampaign()->getHref($params);
  }

  public function getCampaign()
  {
    return $this->getOwner();
  }

  public function getAuthorizationItem()
  {
    return $this->getParent('campaign');
  }

  protected function _delete()
  {
    // Delete all child posts
    $photoTable = Engine_Api::_()->getItemTable('ynfundraising_photo');
    $photoSelect = $photoTable->select()->where('album_id = ?', $this->getIdentity());
    foreach( $photoTable->fetchAll($photoSelect) as $campaignPhoto ) {
      $campaignPhoto->delete();
    }

    parent::_delete();
  }
}