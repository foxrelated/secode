<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupalbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroupalbum_Api_Core extends Core_Api_Abstract {

  /**
   * Delete the sitegroup album and photos
   * 
   * @param int $album_id
   */
  public function deleteContent($album_id) {

		$album = Engine_Api::_()->getItem('sitegroup_album', $album_id);

		if(empty($album)) {
			return;
		}

    //DELETE IMAGE
    $tablePhoto = Engine_Api::_()->getItemTable('sitegroup_photo');
    $select = $tablePhoto->select()->where('album_id = ?', $album_id);
    $rows = $tablePhoto->fetchAll($select);
    if (!empty($rows)) {
      foreach ($rows as $photo) {
        $photo->delete();
      }
    }

    //DELETE ALBUM
    $album->delete();
  }
  
  /**
   * Truncation of text
   * @params string $text
   * @params int $limit
   * @return truncate text
   */
  public function truncateText($text, $limit) {
    $tmpBody = strip_tags($text);
    return ( Engine_String::strlen($tmpBody) > $limit ? Engine_String::substr($tmpBody, 0, $limit) . '..' : $tmpBody );
  }

  /**
   * get the featured photos
   */
  public function getFeaturedPhotos($params=array()) {
    $parentTable = Engine_Api::_()->getItemTable('sitegroup_album');
    $parentTableName = $parentTable->info('name');
    $table = Engine_Api::_()->getItemTable('sitegroup_photo');
    $tableName = $table->info('name');
    $select = $table->select()
                    ->from($tableName);
    if (!Engine_Api::_()->sitegroup()->isLessThan417AlbumModule()) {
      $select
              ->joinLeft($parentTableName, $parentTableName . '.album_id=' . $tableName . '.album_id', null);
    } else {
      $select
              ->joinLeft($parentTableName, $parentTableName . '.album_id=' . $tableName . '.collection_id', null);
    }
    if (isset($params['category_id']) && !empty($params['category_id'])) {
        $tableGroup = Engine_Api::_()->getDbtable('groups', 'sitegroup');
				$tableGroupName = $tableGroup->info('name');
        $select->join($tableGroupName, "$tableGroupName.group_id = $tableName.group_id", array())
				->where($tableGroupName . '.	category_id =?', $params['category_id']);
		}
    $select->where($parentTableName .'.search = ?', true)
            ->where($tableName . '.featured = ?', 1)
            ->order($tableName . '.creation_date DESC');

    if (isset($params['limit']) && !empty($params['limit'])) {
      if (!isset($params['start_index']))
        $params['start_index'] = 0;
      $select->limit($params['limit'], $params['start_index']);
    }
    
    if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupalbum.hide.autogenerated', 1) ) {
			$select->where($parentTableName. '.default_value'.'= ?', 0);
			$select->where($parentTableName . ".type is Null");
    }     
    
    return $table->fetchAll($select);
  }

  /**
   * Return a truncate text
   *
   * @param text text 
   * @return truncate text
   * */
  public function truncation($string) {
    $length = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupalbum.truncation.limit', 13);
    $string = strip_tags($string);
    return Engine_String::strlen($string) > $length ? Engine_String::substr($string, 0, ($length - 3)) . '...' : $string;
  }
  
  public function enableComposer() {
    $subject = '';
    if (Engine_Api::_()->core()->hasSubject()) {
      $subject = Engine_Api::_()->core()->getSubject();
    }
    if ($subject && in_array($subject->getType(), array('sitegroup_group', 'sitegroupevent_event'))):
 

      if (in_array($subject->getType(), array('sitegroupevent_event'))):
        $subject = Engine_Api::_()->getItem('sitegroup_group', $subject->group_id);
      endif;
      //PACKAGE BASE PRIYACY START
      if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
        if (!Engine_Api::_()->sitegroup()->allowPackageContent($subject->package_id, "modules", "sitegroupalbum")) {
          return false;
        }
      } else {
        $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($subject, 'spcreate');
        if (empty($isGroupOwnerAllow)) {
          return false;
        }
      }
      if (!Engine_Api::_()->sitegroup()->isManageAdmin($subject, 'edit') && !Engine_Api::_()->sitegroup()->isManageAdmin($subject, 'spcreate')):
        return false;
      endif;

      return true;

    endif;
    return false;
  }
}

?>