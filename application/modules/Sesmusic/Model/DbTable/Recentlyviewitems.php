<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Recentlyviewitems.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Model_DbTable_Recentlyviewitems extends Engine_Db_Table {

  protected $_name = 'sesmusic_recentlyviewitems';
  protected $_rowClass = 'Sesmusic_Model_Recentlyviewitem';

  public function getitem($params = array()) {

    if ($params['type'] == 'sesmusic_albumsong') {
      $itemTable = Engine_Api::_()->getItemTable('sesmusic_albumsong');
      $itemTableName = $itemTable->info('name');
      $fieldName = 'albumsong_id';
    } elseif ($params['type'] == 'sesmusic_album') {
      $itemTable = Engine_Api::_()->getItemTable('sesmusic_album');
      $itemTableName = $itemTable->info('name');
      $fieldName = 'album_id';
    }

    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($this->info('name'), array('*'))
            ->where('resource_type = ?', $params['type'])
           // ->where($itemTableName . '.photo_id != ?', '')
            ->order('creation_date DESC')
            ->limit($params['limit']);

    if ($params['criteria'] == 'by_me') {
      $select->where($this->info('name') . '.owner_id =?', Engine_Api::_()->user()->getViewer()->getIdentity());
    } else if ($params['criteria'] == 'by_myfriend') {
      /* friends array */
      $friendIds = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();
      if (count($friendIds) == 0)
        return array();
      $select->where($this->info('name') . ".owner_id IN ('" . implode(',', $friendIds) . "')");
    }

    $select->joinLeft($itemTableName, $itemTableName . ".$fieldName =  " . $this->info('name') . '.resource_id', null);

    return $this->fetchAll($select);
  }

}