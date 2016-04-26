<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Playlist.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Model_Playlist extends Core_Model_Item_Abstract {

  public function getMediaType() {
    return 'playlist';
  }

  public function getParent() {
    return $this->getOwner();
  }

  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array()) {

    $params = array_merge(array(
        'route' => 'sesmusic_playlist_view',
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

  public function getPhotoUrl() {

    $photo_id = $this->photo_id;
    $defaultphoto = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.playistdefaultphoto');
    if (empty($photo_id) && $defaultphoto) {
      $path = Zend_Registry::get('Zend_View')->baseUrl() . '/' . $defaultphoto;
      return $path;
    } elseif ($photo_id) {
      $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id, '');
      if ($file)
        return $file->map();
    }
  }

  public function getSongs($file_id = null, $params = array()) {

    $playlistSongs = Engine_Api::_()->getDbtable('playlistsongs', 'sesmusic');

    $select = $playlistSongs->select()
            ->where('playlist_id = ?', $this->getIdentity());

    if (!isset($params) && !$params['limit'])
      $select->order('order ASC');

    if (!empty($file_id))
      $select->where('file_id = ?', $file_id);

    if (!empty($params['limit'])) {
      $select->limit($params['limit'])
              ->order('RAND() DESC');
    }

    return $playlistSongs->fetchAll($select);
  }

  public function addSong($file_id, $albumsong_id = null) {

    if ($file_id instanceof Sesmusic_Model_PlaylistSong)
      $file_id = $file_id->file_id;

    if ($file_id instanceof Storage_Model_File)
      $file = $file_id;
    else
      $file = Engine_Api::_()->getItem('storage_file', $file_id);

    if ($file) {
      $playlist_song = Engine_Api::_()->getDbtable('playlistsongs', 'sesmusic')->createRow();
      $playlist_song->playlist_id = $this->getIdentity();
      $playlist_song->file_id = $file->getIdentity();
      $playlist_song->title = preg_replace('/\.(mp3|m4a|aac|mp4)$/i', '', $file->name);
      $playlist_song->order = 0;
      if ($albumsong_id)
        $playlist_song->albumsong_id = $albumsong_id;
      $playlist_song->save();
      return $playlist_song;
    }

    return false;
  }

  public function setPhoto($photo, $param = null) {

    if ($photo instanceof Zend_Form_Element_File)
      $file = $photo->getFileName();
    else if (is_array($photo) && !empty($photo['tmp_name']))
      $file = $photo['tmp_name'];
    else if (is_string($photo) && file_exists($photo))
      $file = $photo;
    else
      throw new Sesmusic_Model_Exception('Invalid argument passed to setPhoto: ' . print_r($photo, 1));

    $name = basename($file);
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
        'parent_type' => 'sesmusic_playlist',
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