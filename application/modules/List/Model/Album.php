<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Album.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Model_Album extends Core_Model_Item_Collection
{
	protected $_searchTriggers = false;
  protected $_modifiedTriggers = false;
  protected $_parent_type = 'list_listing';
  protected $_owner_type = 'list_listing';
  protected $_children_types = array('list_photo');
  protected $_collectible_type = 'list_photo';

  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array())
  {
		return $this->getOwner()->getHref($params);
  }

  public function getAuthorizationItem()
  {
    return $this->getParent('list_listing');
  }

  protected function _delete()
  {
    //DELTE ALL CHILD POST
    $photoTable = Engine_Api::_()->getItemTable('list_photo');
    $photoSelect = $photoTable->select()->where('album_id = ?', $this->getIdentity());
    foreach( $photoTable->fetchAll($photoSelect) as $listPhoto ) {
      $listPhoto->delete();
    }
    parent::_delete();
  }

  public function getCollectiblesSelect()
  {
    $table = Engine_Api::_()->getItemTable($this->_collectible_type);
    $orderCol = ( in_array('order', $table->info('cols')) ? 'order' : current($table->info('primary')) );
    $select = $table->select()
      ->where($this->getCollectionColumnName() . ' = ?', $this->getIdentity())
      ->where('type != ?', 'overview')
      ->order($orderCol.' ASC');
    return $select;
  }

  public function getCollectiblesPaginator()
  {
    return Zend_Paginator::factory($this->getCollectiblesSelect());
  }

  public function getCollectibleIndex($collectible, $reverse = false)
  {
    if( is_numeric($collectible) )
    {
      $collectible = Engine_Api::_()->getItem($this->_collectible_type, $collectible);
    }
    if( !($collectible instanceof Core_Model_Item_Collectible))
    {
      throw new Core_Model_Item_Exception('Improper argument passed to getNextCollectible');
    }

    if( isset($collectible->collection_index) )
    {
      return $collectible->collection_index;
    }

    //if( !isset($this->store()->collectible_index) || !isset($this->store()->collectible_index[$collectible->getIdentity()]) )
    //{
      $table = $collectible->getTable();
      $col = current($table->info("primary"));
      $select = $table->select()
        ->from($table->info('name'), $col)
        ->where($this->getCollectionColumnName() . ' = ?', $this->getIdentity())
         ->where('type' . ' != ?', 'overview')
        ;

      // Order supported
      if( isset($collectible->order) )
      {
        $select->order('order ASC');
      }
      // Identity
      else
      {
        $select->order($col.' ASC');
      }

      $i = 0;
      $index = 0;
      //$this->store()->collectible_index = array();
      foreach( $table->fetchAll($select) as $row )
      {
        if( $row->$col == $collectible->getIdentity() )
        {
          $index = $i;
        }
        $i++;
        //$this->store()->collectible_index[$row->$col] = $i++;
      }
    //}

    //$index = $this->store()->collectible_index[$collectible->getIdentity()];
    return ( $reverse ? $this->count() - $index : $index );
  }

}