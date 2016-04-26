<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Artists.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Model_DbTable_Artists extends Engine_Db_Table {

  protected $_rowClass = "Sesmusic_Model_Artist";

  public function getOfTheDayResults() {

    $select = $this->select()
            ->from($this->info('name'), array('*'))
            ->where('offtheday =?', 1)
            ->where('starttime <= DATE(NOW())')
            ->where('endtime >= DATE(NOW())')
            ->order('RAND()');
    return $this->fetchRow($select);
  }

  public function getArtists($params = array()) {

    $select = $this->select()
            ->from($this->info('name'), array('name', 'artist_id'))
            ->where('artist_id IN(?)', $params)
            ->query();

    $data = array();
    foreach ($select->fetchAll() as $designation) {
      $data[$designation['artist_id']] = $designation['name'];
    }

    return $data;
  }

  public function getArtistsPaginator($params = array()) {

    $tableName = $this->info('name');
    $select = $this->select()->from($this);

    //String Search
    if (isset($params['name']) && !empty($params['name'])) {
      $select->where("$tableName.name LIKE ?", "%{$params['name']}%");
    }

//    if (isset($params['widgteName']) && $params['widgteName'] == 'Browse Artists')
//      $select->order($tableName . '.order ASC');


    if (isset($params['popularity'])) {
      switch ($params['popularity']) {
        case "favourite_count":
          $select->order($tableName . '.favourite_count DESC')
                  ->order($tableName . '.artist_id DESC');
          break;
        case "rating":
          $select->order($tableName . '.rating DESC')
                  ->order($tableName . '.artist_id DESC');
          break;
        case "order":
          $select->order($tableName . '.order ASC');
          break;
      }
    }

    $paginator = Zend_Paginator::factory($select);
    if (!empty($params['page']))
      $paginator->setCurrentPageNumber($params['page']);

    if (!empty($params['limit']))
      $paginator->setItemCountPerPage($params['limit']);

    return $paginator;
  }

}
