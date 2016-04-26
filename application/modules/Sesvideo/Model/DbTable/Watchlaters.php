<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Watchlaters.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_Model_DbTable_Watchlaters extends Engine_Db_Table {

  protected $_rowClass = "Sesvideo_Model_Watchlater";
  protected $_name = 'video_watchlaters';

  public function getWatchlaterItems($params = array()) {
    $tableName = $this->info('name');
    $viewer = Engine_Api::_()->user()->getViewer();
    $user_id = $viewer->getIdentity();
    $vtName = Engine_Api::_()->getDbtable('videos', 'sesvideo');
    $vtmName = $vtName->info('name');
    $select = $this->select()
            ->from($tableName, null)
            ->setIntegrityCheck(false)
            ->where($vtmName . '.video_id != ?', '')
            ->joinLeft($vtmName, "$vtmName.video_id = $tableName.video_id", '*')
            ->where($tableName . '.owner_id =?', $user_id);
    return Zend_Paginator::factory($select);
  }

  public function getChanelAssociateVideos($resource = array(), $params = array()) {
    if (count($resource) > 0) {
      $tableName = $this->info('name');
      $vtName = Engine_Api::_()->getDbtable('videos', 'sesvideo');
      $vtmName = $vtName->info('name');
      $select = $this->select()
              ->from($tableName, null)
              ->where($tableName . '.chanel_id = ?', $resource->chanel_id)
              ->setIntegrityCheck(false)
              ->where($vtmName . '.video_id != ?', '')
              ->joinLeft($vtmName, "$vtmName.video_id = $tableName.video_id", '*');

      $paginator = Zend_Paginator::factory($select);
      if (!empty($params['page'])) {
        $paginator->setCurrentPageNumber($params['page']);
      }
      if (!empty($params['limit'])) {
        $paginator->setItemCountPerPage($params['limit']);
      }
      return $paginator;
    }
    return array();
  }

}
