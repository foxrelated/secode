<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Chanelphotos.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_Model_DbTable_Chanelphotos extends Engine_Db_Table {

  protected $_rowClass = "Sesvideo_Model_Chanelphoto";
  protected $_name = 'video_chanelphotos';

  public function getPhotoSelect($params = array()) {
    $select = $this->select();
    if (!isset($params['order'])) {
      $select->order('order ASC');
    } else if (is_string($params['order'])) {
      $select->order($params['order']);
    }
    $select->limit($params['limit_data']);
    $paginator = $this->fetchAll($select);
    return $paginator;
  }

  public function chanelphotos($resource_id) {
    $tableName = $this->info('name');
    $vtName = Engine_Api::_()->getDbtable('chanels', 'sesvideo');
    $vtmName = $vtName->info('name');
    $select = $this->select()
            ->from($tableName)
            ->where($tableName . '.chanel_id =?', $resource_id)
            ->where($vtmName . '.chanel_id !=?', '')
            ->setIntegrityCheck(false)
            ->joinLeft($vtmName, "$vtmName.chanel_id = $tableName.chanel_id", null)
            ->order('order ASC');
    return Zend_Paginator::factory($select);
  }

  public function getPhotoCustom($photo = '', $params = array(), $nextPreviousCondition = '<', $getallphotos = false) {
    //status blank means no custom param given to apply, so get the photo as per album and photo id given.
    $status = '';
    //getSEVersion for lower version of SE
    $getmodule = Engine_Api::_()->getDbTable('modules', 'core')->getModule('core');
    if (!empty($getmodule->version) && version_compare($getmodule->version, '4.8.6') < 0) {
      $toArray = true;
    } else
      $toArray = false;
    $GetTableNameChanel = Engine_Api::_()->getItemTable('sesvideo_chanel');
    $tableNameChanel = $GetTableNameChanel->info('name');
    $GetTableNamePhoto = Engine_Api::_()->getItemTable('sesvideo_chanelphoto');
    $tableNamePhoto = $GetTableNamePhoto->info('name');
    $select = $GetTableNamePhoto->select()
            ->from($GetTableNamePhoto)
            ->where($tableNamePhoto . '.chanel_id != ?', '0')
            ->where($tableNameChanel . '.chanel_id != ?', '');
    $select->setIntegrityCheck(false);
    $select->joinLeft($tableNameChanel, $tableNameChanel . '.chanel_id = ' . $tableNamePhoto . '.chanel_id', null);
    $select->where("$tableNamePhoto.chanel_id =  ?", $photo->chanel_id);
    if ($getallphotos) {
      $select->order('order ASC');
      return Zend_Paginator::factory($select);
    }
   // $select = $select->where("$tableNamePhoto.chanelphoto_id $nextPreviousCondition $photo->chanelphoto_id ");
		$select->limit('1');     
			if ($nextPreviousCondition == '<'){
				$select->order('chanelphoto_id DESC');
				 $select->where("$tableNamePhoto.chanelphoto_id < $photo->chanelphoto_id");
			}else{
				$select->order('chanelphoto_id ASC');
				 $select->where("$tableNamePhoto.chanelphoto_id > $photo->chanelphoto_id");
			}
    if ($toArray) {
      $photo = $GetTableNamePhoto->fetchAll($select);
      if (!empty($photo))
        $photo = $photo->toArray();
      else
        $photo = '';
    }else {
      $photo = $GetTableNamePhoto->fetchRow($select);
    }
    return $photo;
  }

}
