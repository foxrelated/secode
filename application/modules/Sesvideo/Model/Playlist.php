<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Playlist.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_Model_Playlist extends Core_Model_Item_Abstract {

  public function getParent($recurseType = NULL) {
    return $this->getOwner();
  }

  public function addVideo($file_id, $video_id = null) {
    $playlist_video = Engine_Api::_()->getDbtable('playlistvideos', 'sesvideo')->createRow();
    $playlist_video->playlist_id = $this->getIdentity();
    $playlist_video->file_id = $video_id;
    $playlist_video->order = 0;
    $playlist_video->save();
    return $playlist_video;
  }

  public function getVideos($params = array(), $paginator = true) {
    $viewer = Engine_Api::_()->user()->getViewer();
    $user_id = $viewer->getIdentity();
    $playlistVideos = Engine_Api::_()->getDbtable('playlistvideos', 'sesvideo');
		$playlistVideosName = $playlistVideos->info('name');
		$videoTableName = Engine_Api::_()->getDbTable('videos', 'sesvideo')->info('name');
    $select = $playlistVideos->select()
							->from($playlistVideos->info('name'))
            ->where('playlist_id = ?', $this->getIdentity())
						 ->joinLeft($videoTableName, "$videoTableName.video_id = $playlistVideosName.file_id", null)
						 ->where($videoTableName.'.video_id IS NOT NULL');
	  $select = $select->setIntegrityCheck(false);
		if (Engine_Api::_()->getApi('settings', 'core')->getSetting('video.enable.watchlater', 1)) {
      $watchLaterTable = Engine_Api::_()->getDbTable('watchlaters', 'sesvideo')->info('name');
     
      $select = $select->joinLeft($watchLaterTable, '(' . $watchLaterTable . '.video_id = ' . $playlistVideos->info('name') . '.file_id AND ' . $watchLaterTable . '.owner_id = ' . $user_id . ')', array('watchlater_id'));
    }
    if (!isset($params) && !$params['order'])
      $select->order('order ASC');
    if ($paginator)
      return Zend_Paginator::factory($select);
    if (!empty($params['limit'])) {
      $select->limit($params['limit'])
              ->order('RAND() DESC');
    }
    return $playlistVideos->fetchAll($select);
  }

  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array()) {

    $params = array_merge(array(
        'route' => 'sesvideo_playlist_view',
        'reset' => true,
        'playlist_id' => $this->playlist_id,
        'slug' => $this->getSlug(),
            ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, $reset);
  }

  public function getPhotoUrl($type = NULL) {

    $photo_id = $this->photo_id;
    if ($photo_id) {
      $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id, '');
      if ($file)
        return $file->map();
    }
			return	"application/modules/Sesvideo/externals/images/nophoto_playlist_thumb_profile.png";
  }

  public function countVideos() {
    $videoTable = Engine_Api::_()->getItemTable('sesvideo_playlistvideo');
    return $videoTable->select()
                    ->from($videoTable, new Zend_Db_Expr('COUNT(playlistvideo_id)'))
                    ->where('playlist_id = ?', $this->playlist_id)
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
  }

  public function setPhoto($photo, $param = null) {

    if ($photo instanceof Zend_Form_Element_File)
      $file = $photo->getFileName();
    else if (is_array($photo) && !empty($photo['tmp_name']))
      $file = $photo['tmp_name'];
    else if (is_string($photo) && file_exists($photo))
      $file = $photo;
    else
      throw new Sesvideo_Model_Exception('Invalid argument passed to setPhoto: ' . print_r($photo, 1));

    $name = basename($file);
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
        'parent_type' => 'sesvideo_playlist',
        'parent_id' => $this->getIdentity()
    );

    //Save
    $storage = Engine_Api::_()->storage();

    $image = Engine_Image::factory();
    $image->open($file)
            ->resize(500, 500)
            ->write($path . '/m_' . $name)
            ->destroy();

    //Resize image (icon)
    $image = Engine_Image::factory();
    $image->open($file);

    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;

    $image->resample($x, $y, $size, $size, 48, 48)
            ->write($path . '/is_' . $name)
            ->destroy();

    //Store
    $iMain = $storage->create($path . '/m_' . $name, $params);
    $iSquare = $storage->create($path . '/is_' . $name, $params);
    $iMain->bridge($iMain, 'thumb.profile');
    $iMain->bridge($iSquare, 'thumb.icon');


    //Remove temp files
    @unlink($path . '/m_' . $name);
    @unlink($path . '/is_' . $name);

    if ($param == 'mainPhoto')
      $this->photo_id = $iMain->getIdentity();
    else
      $this->song_cover = $iMain->getIdentity();

    $this->save();

    return $this;
  }

}
