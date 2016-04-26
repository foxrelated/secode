<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Core.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_Api_Core extends Core_Api_Abstract {

  public function getRow($params = array()) {

    $table = Engine_Api::_()->getDbtable($params['table_name'], 'sesvideo');
    $tableName = $table->info('name');
    $select = $table->select()->order($params['id'] . " DESC");
    return $table->fetchRow($select);
  }

  public function getwidgetizePage($params = array()) {

    $corePages = Engine_Api::_()->getDbtable('pages', 'core');
    $corePagesName = $corePages->info('name');
    $select = $corePages->select()
            ->from($corePagesName, array('*'))
            ->where('name = ?', $params['name'])
            ->limit(1);
    return $corePages->fetchRow($select);
  }
  function tagCloudItemCore($fetchtype = '', $params = array()) {
    $tableTagmap = Engine_Api::_()->getDbtable('tagMaps', 'core');
    $tableTagName = $tableTagmap->info('name');
	 if ($params['type'] == 'video')
			$key = 'video_id';
	 else
			$key = 'chanel_id';
    $tableTag = Engine_Api::_()->getDbtable('tags', 'core');
    $tableMainTagName = $tableTag->info('name');
		$tableItem = Engine_Api::_()->getItemTable($params['type']);
    $tableItemName = $tableItem->info('name');
    $selecttagged_photo = $tableTagmap->select()
            ->from($tableTagName)
            ->setIntegrityCheck(false)
            ->where('tag_type =?', 'core_tag')
            ->joinLeft($tableMainTagName, $tableMainTagName . '.tag_id=' . $tableTagName . '.tag_id', array('text'))
						->joinLeft($tableItemName, $tableItemName . '.'.$key.'=' . $tableTagName . '.resource_id', array($key))
            ->group($tableTagName . '.tag_id');
		$selecttagged_photo->where($key.' != ""');
    if (isset($params['type']))
      $selecttagged_photo->where('resource_type =?', $params['type']);
    $selecttagged_photo->columns(array('itemCount' => ("COUNT($tableTagName.tagmap_id)")));
    if ($fetchtype == '')
      return Zend_Paginator::factory($selecttagged_photo);
    else
      return $tableTagmap->fetchAll($selecttagged_photo);
  }

  /**
   * Get Widget Identity
   *
   * @return $identity
   */
  public function getIdentityWidget($name, $type, $corePages) {
    $widgetTable = Engine_Api::_()->getDbTable('content', 'core');
    $widgetPages = Engine_Api::_()->getDbTable('pages', 'core')->info('name');
    $identity = $widgetTable->select()
            ->setIntegrityCheck(false)
            ->from($widgetTable, 'content_id')
            ->where($widgetTable->info('name') . '.type = ?', $type)
            ->where($widgetTable->info('name') . '.name = ?', $name)
            ->where($widgetPages . '.name = ?', $corePages)
            ->joinLeft($widgetPages, $widgetPages . '.page_id = ' . $widgetTable->info('name') . '.page_id')
            ->query()
            ->fetchColumn();
    return $identity;
  }

  public function getVideosPaginator($params = array()) {
    $paginator = Zend_Paginator::factory($this->getVideosSelect($params));
    if (!empty($params['page'])) {
      $paginator->setCurrentPageNumber($params['page']);
    }
    if (!empty($params['limit'])) {
      $paginator->setItemCountPerPage($params['limit']);
    }
    return $paginator;
  }

  //get next photo
  public function nextPhoto($photo = '', $params = array()) {
    return Engine_Api::_()->getDbTable('chanelphotos', 'sesvideo')->getPhotoCustom($photo, $params, '>');
  }

  //get previous photo
  public function previousPhoto($photo = '', $params = array()) {
    return Engine_Api::_()->getDbTable('chanelphotos', 'sesvideo')->getPhotoCustom($photo, $params, '<');
  }

  // get photo like status
  public function getLikeStatusVideo($video_id = '', $moduleName = '') {
    if ($moduleName == '')
      $moduleName = 'video';
    if ($video_id != '') {
      $userId = Engine_Api::_()->user()->getViewer()->getIdentity();
      if ($userId == 0)
        return false;
      $coreLikeTable = Engine_Api::_()->getDbtable('likes', 'core');
      $total_likes = $coreLikeTable->select()->from($coreLikeTable->info('name'), new Zend_Db_Expr('COUNT(like_id) as like_count'))->where('resource_type =?', $moduleName)->where('poster_id =?', $userId)->where('poster_type =?', 'user')->where('	resource_id =?', $video_id)->limit(1)->query()->fetchColumn();
      if ($total_likes > 0) {
        return true;
      } else {
        return false;
      }
    }
    return false;
  }

  //get photo href
  function getHrefPhoto($photoId = '', $chanelId = '') {
    $params = array_merge(array(
        'route' => 'sesvideo_chanel',
        'reset' => true,
        'controller' => 'chanel',
        'action' => 'view',
        'chanel_id' => $chanelId,
        'photo_id' => $photoId,
    ));
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
                    ->assemble($params, $route, $reset);
  }

  // get lightbox video URL
  function getVideoViewerHref($getImageViewerData, $paramsExtra = array()) {
    if (is_object($getImageViewerData)) {
      if (isset($getImageViewerData->video_id))
        $video_id = $getImageViewerData->video_id;
      else if (isset($getImageViewerData['video_id']))
        $video_id = $getImageViewerData['video_id'];

      if (isset($getImageViewerData->owner_id))
        $user_id = $getImageViewerData->owner_id;
      else if (isset($getImageViewerData['owner_id']))
        $user_id = $getImageViewerData['owner_id'];

      $params = array_merge(array(
          'route' => 'sesvideo_lightbox',
          'controller' => 'index',
          'action' => 'imageviewerdetail',
          'reset' => true,
          'video_id' => $video_id,
          'user_id' => $user_id,
          'slug' => $this->getSlug(Engine_Api::_()->getItem('video', $video_id)->getTitle()),
              ), $paramsExtra);
      $route = $params['route'];
      $reset = $params['reset'];
      unset($params['route']);
      unset($params['reset']);
      return Zend_Controller_Front::getInstance()->getRouter()
                      ->assemble($params, $route, $reset);
    }
    return '';
  }

	//get chanel photo
  function getChanelPhoto($chanelId = '', $limit = 3) {
    if ($chanelId != '') {
      $chanel = Engine_Api::_()->getItemTable('sesvideo_chanel');
      $chanelTableName = $chanel->info('name');
      $photos = Engine_Api::_()->getItemTable('sesvideo_chanelphoto');
      $photoTableName = $photos->info('name');
      $select = $photos->select()
              ->from($photoTableName)
              ->limit($limit)
              ->where($chanelTableName . '.chanel_id = ?', $chanelId)
              ->setIntegrityCheck(false)
              ->joinLeft($chanelTableName, $chanelTableName . '.chanel_id = ' . $photoTableName . '.chanel_id', null);
      $select = $select->order('rand()');
      return $photos->fetchAll($select);
    }
  }
  /**
   * Gets a url slug for this item, based on it's title
   *
   * @return string The slug
   */
  public function getSlug($str = null, $maxstrlen = 245) {
    if (null === $str) {
      $str = $this->getTitle();
    }
    if (strlen($str) > $maxstrlen) {
      $str = Engine_String::substr($str, 0, $maxstrlen);
    }

    $search = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
    $replace = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
    $str = str_replace($search, $replace, $str);

    $str = preg_replace('/([a-z])([A-Z])/', '$1 $2', $str);
    $str = strtolower($str);
    $str = preg_replace('/[^a-z0-9-]+/i', '-', $str);
    $str = preg_replace('/-+/', '-', $str);
    $str = trim($str, '-');
    if (!$str) {
      $str = '-';
    }
    return $str;
  }
	function getCustomLightboxHref($item,$params=array()){
		$itemType = $itemId = '';
		if(isset($params['type'])){
			$itemTable = Engine_Api::_()->getItemTable($params['type']);
			$itemTableName = $itemTable->info('name');
			$select = $itemTable->select();
			if($params['type'] == 'sesvideo_chanel'){
				$itemId = $item->chanel_id;
				$select->from($itemTableName,null)
              ->setIntegrityCheck(false);
				$tableName = Engine_Api::_()->getItemTable('sesvideo_chanelvideo')->info('name');
				 $select->joinLeft($tableName, $tableName . '.chanel_id = ' . $itemTableName . '.chanel_id', 'video_id');
      	$select->where($tableName . '.chanel_id = ?', $item->chanel_id);
				$select->order('chanelvideo_id DESC');
				$select->limit(1);
				$item = $select->query()->fetchColumn();
				$item = Engine_Api::_()->getItem('video', $item);
				$itemType = 'sesvideo_chanel';
			}else if($params['type'] == 'sesvideo_playlist'){
				$itemId = $item->playlist_id;
				$select->from($itemTableName,null)
              ->setIntegrityCheck(false);
				$tableName = Engine_Api::_()->getItemTable('sesvideo_playlistvideo')->info('name');
				 $select->joinLeft($tableName, $tableName . '.playlist_id = ' . $itemTableName . '.playlist_id', 'file_id');
      	$select->where($tableName . '.playlist_id = ?', $item->playlist_id);
				//$select->order('playlistvideo_id DESC');
				$select->limit(1);
				$item = $select->query()->fetchColumn();
				$item = Engine_Api::_()->getItem('video', $item);
				$itemType = 'sesvideo_playlist';
			}
		}
		$params = array_merge(array(
          'route' => 'sesvideo_lightbox',
          'controller' => 'index',
          'action' => 'imageviewerdetail',
          'reset' => true,
          'video_id' => $item->video_id,
          'user_id' => $item->owner_id,
					'item_id'=>$itemId,
					'type'=>$itemType,
          'slug' => $this->getSlug($item->getTitle()),
              ));
			$params = array_filter($params);
      $route = $params['route'];
      $reset = $params['reset'];
      unset($params['route']);
      unset($params['reset']);
      return Zend_Controller_Front::getInstance()->getRouter()
                      ->assemble($params, $route, $reset);
	}
  // get lightbox image URL
  function getImageViewerHref($getImageViewerData, $paramsExtra = array()) {
    if (is_object($getImageViewerData)) {
      if (isset($getImageViewerData->chanel_id))
        $chanel_id = $getImageViewerData->chanel_id;
      else if (isset($getImageViewerData['chanel_id']))
        $chanel_id = $getImageViewerData['chanel_id'];

      if (isset($getImageViewerData->photo_id))
        $photo_id = $getImageViewerData->photo_id;
      else if (isset($getImageViewerData['chanelphoto_id']))
        $photo_id = $getImageViewerData['chanelphoto_id'];

      $params = array_merge(array(
          'route' => 'sesvideo_chanel',
          'controller' => 'chanel',
          'action' => 'image-viewer-detail',
          'reset' => true,
          'chanel_id' => $chanel_id,
          'photo_id' => $photo_id,
              ), $paramsExtra);
      $route = $params['route'];
      $reset = $params['reset'];
      unset($params['route']);
      unset($params['reset']);
      return Zend_Controller_Front::getInstance()->getRouter()
                      ->assemble($params, $route, $reset);
    }
    return '';
  }

  public function getCustomFieldMapData($video) {
    if ($video) {
      $db = Engine_Db_Table::getDefaultAdapter();
      return $db->query("SELECT GROUP_CONCAT(value) AS `valuesMeta`,IFNULL(TRIM(TRAILING ', ' FROM GROUP_CONCAT(DISTINCT(engine4_video_fields_options.label) SEPARATOR ', ')),engine4_video_fields_values.value) AS `value`, `engine4_video_fields_meta`.`label`, `engine4_video_fields_meta`.`type` FROM `engine4_video_fields_values` LEFT JOIN `engine4_video_fields_meta` ON engine4_video_fields_meta.field_id = engine4_video_fields_values.field_id LEFT JOIN `engine4_video_fields_options` ON engine4_video_fields_values.value = engine4_video_fields_options.option_id AND `engine4_video_fields_meta`.`type` = 'multi_checkbox' WHERE (engine4_video_fields_values.item_id = ".$video->video_id.") AND (engine4_video_fields_values.field_id != 1) GROUP BY `engine4_video_fields_meta`.`field_id`,`engine4_video_fields_options`.`field_id`")->fetchAll();
    }
    return array();
  }

  public function addWatchlaterVideo($params = array()) {
    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    if (count($params['video_id'])) {
      $watchLater = Engine_Api::_()->getDbtable('watchlaters', 'sesvideo')->createRow();
      $watchLater->video_id = $params['video_id'];
      $watchLater->modified_date = date('Y-m-d H:i:s');
      $watchLater->owner_id = $user_id;
      $watchLater->save();
      return true;
    }
    return false;
  }

  public function getWatchLaterId($video_id = false) {
    if ($video_id) {
      $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
      $watchlater = Engine_Api::_()->getDbtable('watchlaters', 'sesvideo');
      $select = $watchlater->select('watchlater_id')
              ->where('video_id = ?', $video_id)
              ->where('owner_id = ?', $user_id);
      return $watchlater->fetchAll($select);
    }
  }

  public function deleteWatchlaterVideo($params = array()) {
    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    if (count($params['video_id'])) {
      $dbObject = Engine_Db_Table::getDefaultAdapter();
      $object = $dbObject->query('INSERT INTO engine4_video_watchlaters (video_id, owner_id,modified_date,creation_date ) VALUES ("' . $params['video_id'] . '", "' . $user_id . '",NOW(),NOW())	ON DUPLICATE KEY UPDATE	modified_date = modified_date');
      if ($object->rowCount() > 0) {
        return array('status' => 'insert', 'error' => false);
      } else {
        $watchlaterid = $this->getWatchLaterId($params['video_id']);
        if (count($watchlaterid) > 0) {
          Engine_Api::_()->getItem('watchlater', $watchlaterid[0]->watchlater_id)->delete();
          return array('status' => 'delete', 'error' => false);
        }
      }
    }
    return array('status' => 'error', 'error' => true);
  }

  public function getVideosSelect($params = array()) {
    $table = Engine_Api::_()->getDbtable('videos', 'sesvideo');
    $rName = $table->info('name');

    $tmTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
    $tmName = $tmTable->info('name');

    $select = $table->select()
            ->from($table->info('name'))
            ->order(!empty($params['orderby']) ? $params['orderby'] . ' DESC' : "$rName.creation_date DESC" );

    if (!empty($params['text'])) {
      $searchTable = Engine_Api::_()->getDbtable('search', 'core');
      $db = $searchTable->getAdapter();
      $sName = $searchTable->info('name');
      $select
              ->joinRight($sName, $sName . '.id=' . $rName . '.video_id', null)
              ->where($sName . '.type = ?', 'video')
              ->where(new Zend_Db_Expr($db->quoteInto('MATCH(' . $sName . '.`title`, ' . $sName . '.`description`, ' . $sName . '.`keywords`, ' . $sName . '.`hidden`) AGAINST (? IN BOOLEAN MODE)', $params['text'])))
      //->order(new Zend_Db_Expr($db->quoteInto('MATCH(' . $sName . '.`title`, ' . $sName . '.`description`, ' . $sName . '.`keywords`, ' . $sName . '.`hidden`) AGAINST (?) DESC', $params['text'])))
      ;
    }

    if (!empty($params['status']) && is_numeric($params['status'])) {
      $select->where($rName . '.status = ?', $params['status']);
    }
    if (!empty($params['search']) && is_numeric($params['search'])) {
      $select->where($rName . '.search = ?', $params['search']);
    }
    if (!empty($params['user_id']) && is_numeric($params['user_id'])) {
      $select->where($rName . '.owner_id = ?', $params['user_id']);
    }

    if (!empty($params['user']) && $params['user'] instanceof User_Model_User) {
      $select->where($rName . '.owner_id = ?', $params['user_id']->getIdentity());
    }

    if (!empty($params['category'])) {
      $select->where($rName . '.category_id = ?', $params['category']);
    }

    if (!empty($params['tag'])) {
      $select
              // ->setIntegrityCheck(false)
              // ->from($rName)
              ->joinLeft($tmName, "$tmName.resource_id = $rName.video_id", NULL)
              ->where($tmName . '.resource_type = ?', 'video')
              ->where($tmName . '.tag_id = ?', $params['tag']);
    }

    return $select;
  }

  // chanel data
  public function getChanelPaginator($params = array()) {
    $paginator = Zend_Paginator::factory($this->getChanelSelect($params));
    if (!empty($params['page'])) {
      $paginator->setCurrentPageNumber($params['page']);
    }
    if (!empty($params['limit'])) {
      $paginator->setItemCountPerPage($params['limit']);
    }
    return $paginator;
  }

  public function getChanelSelect($params = array()) {
    $table = Engine_Api::_()->getDbtable('chanels', 'sesvideo');
    $rName = $table->info('name');

    $tmTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
    $tmName = $tmTable->info('name');

    $select = $table->select()
            ->from($table->info('name'))
            ->order(!empty($params['orderby']) ? $params['orderby'] . ' DESC' : "$rName.creation_date DESC" );

    if (!empty($params['text'])) {
      $searchTable = Engine_Api::_()->getDbtable('search', 'core');
      $db = $searchTable->getAdapter();
      $sName = $searchTable->info('name');
      $select
              ->joinRight($sName, $sName . '.id=' . $rName . '.video_id', null)
              ->where($sName . '.type = ?', 'video')
              ->where(new Zend_Db_Expr($db->quoteInto('MATCH(' . $sName . '.`title`, ' . $sName . '.`description`, ' . $sName . '.`keywords`, ' . $sName . '.`hidden`) AGAINST (? IN BOOLEAN MODE)', $params['text'])))
      //->order(new Zend_Db_Expr($db->quoteInto('MATCH(' . $sName . '.`title`, ' . $sName . '.`description`, ' . $sName . '.`keywords`, ' . $sName . '.`hidden`) AGAINST (?) DESC', $params['text'])))
      ;
    }

    if (!empty($params['status']) && is_numeric($params['status'])) {
      $select->where($rName . '.status = ?', $params['status']);
    }
    if (!empty($params['search']) && is_numeric($params['search'])) {
      $select->where($rName . '.search = ?', $params['search']);
    }
    if (!empty($params['user_id']) && is_numeric($params['user_id'])) {
      $select->where($rName . '.owner_id = ?', $params['user_id']);
    }

    if (!empty($params['user']) && $params['user'] instanceof User_Model_User) {
      $select->where($rName . '.owner_id = ?', $params['user_id']->getIdentity());
    }

    if (!empty($params['category'])) {
      $select->where($rName . '.category_id = ?', $params['category']);
    }

    if (!empty($params['tag'])) {
      $select
              // ->setIntegrityCheck(false)
              // ->from($rName)
              ->joinLeft($tmName, "$tmName.resource_id = $rName.video_id", NULL)
              ->where($tmName . '.resource_type = ?', 'video')
              ->where($tmName . '.tag_id = ?', $params['tag']);
    }
    return $select;
  }

  public function getCategories() {
    $table = Engine_Api::_()->getDbTable('categories', 'sesvideo');
		return Engine_Api::_()->getDbtable('categories', 'sesvideo')->getCategory();
  }

  public function getCategory($category_id) {
    return Engine_Api::_()->getDbtable('categories', 'sesvideo')->find($category_id)->current();
  }

  // handle video upload
  public function createVideo($params, $file, $values,$video_date = false) {
    if ($file instanceof Storage_Model_File) {
      $params['file_id'] = $file->getIdentity();
    } else {
      // create video item
			if(!$video_date){
      	$video = Engine_Api::_()->getDbtable('videos', 'sesvideo')->createRow();
      	$file_ext = pathinfo($file['name']);
				$file_ext = $file_ext['extension'];
			}

      // Store video in temporary storage object for ffmpeg to handle
      $storage = Engine_Api::_()->getItemTable('storage_file');
			$params = array(
          'parent_id' => $video->getIdentity(),
          'parent_type' => $video->getType(),
          'user_id' => $video->owner_id,
      );
			if(!$video_date){
				$video->code = $file_ext;
      	$storageObject = $storage->createFile($file, $params);
				$video->file_id = $file_id = $storageObject->file_id;
			}
      // Remove temporary file
      @unlink($file['tmp_name']);
      $video->save();
			if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesvideo.direct.video', 0)){
				if($file_ext == 'mp4' || $file_ext == 'flv'){
					$video->status = 1;
					 $file = Engine_Api::_()->getItemTable('storage_file')->getFile($file_id, null);
					$file = (_ENGINE_SSL ? 'https://' : 'http://') 
						. $_SERVER['HTTP_HOST'].$file->map();
					$video->duration = $duration = $this->getVideoDuration($video,$file);
					if($duration){
						$thumb_splice = $duration / 2;
						$this->getVideoThumbnail($video,$thumb_splice,$file);
					}
					$video->save();
					return $video;	
				}
			}
			$video->status = 2;
			$video->save();
      // Add to jobs
      if (Engine_Api::_()->getApi('settings', 'core')->getSetting('video.html5', false)) {
        Engine_Api::_()->getDbtable('jobs', 'core')->addJob('video_encode', array(
            'video_id' => $video->getIdentity(),
            'type' => 'mp4',
        ));
      } else {				
        Engine_Api::_()->getDbtable('jobs', 'core')->addJob('video_encode', array(
            'video_id' => $video->getIdentity(),
            'type' => 'flv',
        ));
      }
    }
    return $video;
  }
	public function getVideoThumbnail($video,$thumb_splice,$file = false){
		$tmpDir = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' . DIRECTORY_SEPARATOR . 'video';
		$thumbImage = $tmpDir . DIRECTORY_SEPARATOR . $video -> getIdentity() . '_thumb_image.jpg';
		$ffmpeg_path = Engine_Api::_() -> getApi('settings', 'core') -> video_ffmpeg_path;
		if (!@file_exists($ffmpeg_path) || !@is_executable($ffmpeg_path))
		{
			$output = null;
			$return = null;
			exec($ffmpeg_path . ' -version', $output, $return);
			if ($return > 0)
			{
				return 0;
			}
		}
		if(!$file)
			$fileExe = $video->code;
		else
			$fileExe = $file;
		$output = PHP_EOL;
		$output .= $fileExe . PHP_EOL;
		$output .= $thumbImage . PHP_EOL;
		$thumbCommand = $ffmpeg_path . ' ' . '-i ' . escapeshellarg($fileExe) . ' ' . '-f image2' . ' ' . '-ss ' . $thumb_splice . ' ' . '-vframes ' . '1' . ' ' . '-v 2' . ' ' . '-y ' . escapeshellarg($thumbImage) . ' ' . '2>&1';
		// Process thumbnail
		$thumbOutput = $output . $thumbCommand . PHP_EOL . shell_exec($thumbCommand);
		// Check output message for success
		$thumbSuccess = true;
		if (preg_match('/video:0kB/i', $thumbOutput))
		{
			$thumbSuccess = false;
		}
		// Resize thumbnail
		if ($thumbSuccess && is_file($thumbImage))
		{
			try
			{
				$image = Engine_Image::factory();
				$image->open($thumbImage)->resize(500, 500)->write($thumbImage)->destroy();
				$thumbImageFile = Engine_Api::_()->storage()->create($thumbImage, array(
					'parent_id' => $video -> getIdentity(),
					'parent_type' => $video -> getType(),
					'user_id' => $video -> owner_id
					)
				);
				$video->photo_id = $thumbImageFile->file_id;
				$video->save();
				@unlink($thumbImage);
				return true;
			}
			catch (Exception $e)
			{
				throw $e;
				@unlink($thumbImage);
			}
		}
		 @unlink(@$thumbImage);
		 return false;
	}
	public function getVideoDuration($video,$file = false)
	{
		$duration = 0;
		if ($video)
		{
				$ffmpeg_path = Engine_Api::_() -> getApi('settings', 'core') -> video_ffmpeg_path;

				if (!@file_exists($ffmpeg_path) || !@is_executable($ffmpeg_path))
				{
					$output = null;
					$return = null;
					exec($ffmpeg_path . ' -version', $output, $return);
					if ($return > 0)
					{
						return 0;
					}
				}
				if(!$file)
					$fileExe = $video->code;
				else
					$fileExe = $file;
				// Prepare output header
				$fileCommand = $ffmpeg_path . ' ' . '-i ' . escapeshellarg($fileExe) . ' ' . '2>&1';
				// Process thumbnail
				$fileOutput = shell_exec($fileCommand);
				// Check output message for success
				$infoSuccess = true;
				if (preg_match('/video:0kB/i', $fileOutput))
				{
					$infoSuccess = false;
				}
				
				// Resize thumbnail
				if ($infoSuccess)
				{
					// Get duration of the video to caculate where to get the thumbnail
					if (preg_match('/Duration:\s+(.*?)[.]/i', $fileOutput, $matches))
					{
						list($hours, $minutes, $seconds) = preg_split('[:]', $matches[1]);
						$duration = ceil($seconds + ($minutes * 60) + ($hours * 3600));
					}
				}
			
		}
		return $duration;
	}
  public function deleteVideo($video) {

    // delete video ratings
    Engine_Api::_()->getDbtable('ratings', 'sesvideo')->delete(array(
        'resource_id = ?' => $video->video_id,
    ));

    // check to make sure the video did not fail, if it did we wont have files to remove
    if ($video->status == 1) {
      // delete storage files (video file and thumb)
      if ($video->type == 3)
        Engine_Api::_()->getItem('storage_file', $video->file_id)->remove();
      if ($video->photo_id)
        Engine_Api::_()->getItem('storage_file', $video->photo_id)->remove();
    }

    // delete activity feed and its comments/likes
    $item = Engine_Api::_()->getItem('video', $video->video_id);
    if ($item) {
      $item->delete();
    }
  }

  /* people like item widget paginator */

  public function likeItemCore($params = array()) {
    $parentTable = Engine_Api::_()->getItemTable('core_like');
    $parentTableName = $parentTable->info('name');
    $select = $parentTable->select()
            ->from($parentTableName)
            ->where('resource_type = ?', $params['type'])
            ->order('like_id DESC');
    if (isset($params['id']))
      $select = $select->where('resource_id = ?', $params['id']);
    if (isset($params['poster_id']))
      $select = $select->where('poster_id =?', $params['poster_id']);

    return Zend_Paginator::factory($select);
  }

  public function getLikesContents($params = array()) {
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $likeTable = Engine_Api::_()->getDbTable('likes', 'core');
    $likeTableName = $likeTable->info('name');
    $select = $likeTable->select()
            ->from($likeTableName)
            ->where('resource_type =?', $params['resource_type'])
            ->where('poster_id =?', $viewer_id)
            ->order($likeTableName . '.like_id DESC');
    $select = $select->setIntegrityCheck(false);
    if ($params['resource_type'] == 'video') {
      $videoTableName = Engine_Api::_()->getDbTable('videos', 'sesvideo')->info('name');
      $select = $select->joinLeft($videoTableName, "$likeTableName.resource_id=$videoTableName.video_id", NULL);
      $select = $select->where('video_id != ?', '');
    } else {
      $chanelTableName = Engine_Api::_()->getDbTable('chanels', 'sesvideo')->info('name');
      $select = $select->joinLeft($chanelTableName, "$likeTableName.resource_id=$chanelTableName.chanel_id", NULL);
      $select = $select->where('chanel_id != ?', '');
    }
    return Zend_Paginator::factory($select);
  }

}