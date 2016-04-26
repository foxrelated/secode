<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Chanelvideos.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
class Sesvideo_Model_DbTable_Chanelvideos extends Engine_Db_Table {
  protected $_rowClass = "Sesvideo_Model_Chanelvideo";
  protected $_name = 'video_chanelvideos';
  public function getChanelAssociateVideos($resource = array(), $params = array()) {
    if (isset($resource->chanel_id))
      $resource_id = $resource->chanel_id;
    else
      $resource_id = $resource->chanel_id;
    if (count($resource) > 0) {
      $tableName = $this->info('name');
      $vtName = Engine_Api::_()->getDbtable('videos', 'sesvideo');
      $vtmName = $vtName->info('name');
      $select = $this->select()
              ->from($tableName, null)
              ->where($tableName . '.chanel_id = ?', $resource_id)
              ->setIntegrityCheck(false)
              ->where($vtmName . '.video_id != ?', '')
              ->joinLeft($vtmName, "$vtmName.video_id = $tableName.video_id", '*');
      $viewer = Engine_Api::_()->user()->getViewer();
		if (Engine_Api::_()->getApi('settings', 'core')->getSetting('video.enable.watchlater', 1)) {
      $user_id = $viewer->getIdentity();
      $watchLaterTable = Engine_Api::_()->getDbTable('watchlaters', 'sesvideo')->info('name');
      $select = $select->setIntegrityCheck(false);
      $select = $select->joinLeft($watchLaterTable, '(' . $watchLaterTable . '.video_id = ' . $tableName . '.video_id AND ' . $watchLaterTable . '.owner_id = ' . $user_id . ')', array('watchlater_id'));
		}
      if (isset($params['limit_data']))
        $select = $select->limit($params['limit_data']);

      if (empty($params['paginator']))
        $paginator = Zend_Paginator::factory($select);
      else
        $paginator = $this->fetchAll($select);

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
