<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Album.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Model_Album extends Core_Model_Item_Abstract {

//  public function getShortType() {
//    return 'music album';
//  }

  public function getMediaType() {
    return 'music album';
  }

  //Interfaces
  public function getTitle() {

    if ($this->special == 'wall')
      return Zend_Registry::get('Zend_Translate')->_('Profile Music Album');
    else if ($this->special == 'message')
      return Zend_Registry::get('Zend_Translate')->_('Message Music Album');
    else if (!empty($this->title))
      return $this->title;
  }

  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array()) {

    $slug = $this->getSlug();
    $params = array_merge(array(
        'route' => 'sesmusic_album_view',
        'reset' => true,
        'album_id' => $this->album_id,
        'slug' => $slug,
            ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, $reset);
  }

  public function getRichContent($view = false, $params = array()) {

    $videoEmbedded = '';
    if (!$view) {
      $desc = strip_tags($this->description);
      $desc = "<div class='sesmusic_feed_desc'>" . (Engine_String::strlen($desc) > 255 ? Engine_String::substr($desc, 0, 255) . '...' : $desc) . "</div>";
      $view = Zend_Registry::get('Zend_View');
      $view->playlist = $this;
      $view->songs = $this->getSongs();
      $view->short_player = true;
      $view->hideStats = true;
      $videoEmbedded = $desc . $view->render('application/modules/Sesmusic/views/scripts/_Player.tpl');
    }

    //Hide playlist if in production mode
    if (!count($view->songs) && 'production' == APPLICATION_ENV)
      throw new Exception('Empty playlists show not be shown');

    return $videoEmbedded;
  }

  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   * */
  public function comments() {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   * */
  public function likes() {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

  public function getCommentCount() {
    return $this->comments()->getCommentCount();
  }

  public function getParent($recurseType = NULL) {
    return $this->getOwner();
  }

  public function getSongs($file_id = null) {

    $albumSongsTable = Engine_Api::_()->getDbtable('albumsongs', 'sesmusic');
    $select = $albumSongsTable->select()
            ->where('album_id = ?', $this->getIdentity())
            ->order('order ASC');

    if (!empty($file_id))
      $select->where('file_id = ?', $file_id);

    return $albumSongsTable->fetchAll($select);
  }

  public function getSong($file_id) {

    return Engine_Api::_()->getDbtable('albumsongs', 'sesmusic')->fetchRow(array(
                'album_id = ?' => $this->getIdentity(),
                'file_id = ?' => $file_id,
    ));
  }

  public function addSong($file_id, $params = array()) {

    if ($file_id instanceof Sesmusic_Model_Albumsong)
      $file_id = $file_id->file_id;

    if ($file_id instanceof Storage_Model_File)
      $file = $file_id;
    else
      $file = Engine_Api::_()->getItem('storage_file', $file_id);

    if ($file) {
      $albumSongs = Engine_Api::_()->getDbtable('albumsongs', 'sesmusic')->createRow();
      $albumSongs->album_id = $this->getIdentity();
      $albumSongs->file_id = $file->getIdentity();
      if (count($params) == 0) {
        $albumSongs->title = preg_replace('/\.(mp3|m4a|aac|mp4)$/i', '', $file->name);
      } else {
        $albumSongs->title = $file->name;
        $albumSongs->track_id = $file->size;
        $albumSongs->song_url = $file->storage_path;
      }
      $albumSongs->order = count($this->getSongs());
      //Get visitor IP Address
      $ip = $_SERVER['REMOTE_ADDR'];
      if ($ip)
        $albumSongs->ip_address = $ip;
      $albumSongs->save();
      return $albumSongs;
    }
    return false;
  }

  public function getPhotoUrl($type = NULL) {

    $photo_id = $this->photo_id;
    $defaultphoto = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.albumdefaultphoto');
    if (empty($photo_id) && $defaultphoto) {
      $path = Zend_Registry::get('Zend_View')->baseUrl() . '/' . $defaultphoto;
      return $path;
    } elseif ($photo_id) {
      $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id, '');
      return $file->map();
    }
  }

  public function setProfile() {
    $table = Engine_Api::_()->getDbtable('albums', 'sesmusic')->update(array(
        'profile' => 0,
            ), array(
        'owner_id = ?' => $this->owner_id,
        'album_id != ' . $this->getIdentity(),
    ));
    $this->profile = !$this->profile;
    $this->save();
  }

  public function setPhoto($photo) {

    if ($photo instanceof Zend_Form_Element_File)
      $file = $photo->getFileName();
    else if (is_array($photo) && !empty($photo['tmp_name']))
      $file = $photo['tmp_name'];
    else if (is_string($photo) && file_exists($photo))
      $file = $photo;
    else if ($photo)
      $file = $photo->storage_path;
    else
      throw new Sesmusic_Model_Exception('Invalid argument passed to setPhoto: ' . print_r($photo, 1));

    $name = basename($file);
    
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
        'parent_type' => 'sesmusic_album',
        'parent_id' => $this->getIdentity()
    );

    //Save
    $storage = Engine_Api::_()->storage();

    //Resize image (main)
    $image = Engine_Image::factory();
    $image->open($file)
            ->resize(500, 500)
            ->write($path . '/m_' . $name)
            ->destroy();

    //Resize image (profile)
    $image = Engine_Image::factory();
    $image->open($file)
            ->resize(200, 400)
            ->write($path . '/p_' . $name)
            ->destroy();

    //Resize image (normal)
    $image = Engine_Image::factory();
    $image->open($file)
            ->resize(140, 160)
            ->write($path . '/in_' . $name)
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
    $iProfile = $storage->create($path . '/p_' . $name, $params);
    $iIconNormal = $storage->create($path . '/in_' . $name, $params);
    $iSquare = $storage->create($path . '/is_' . $name, $params);

    $iMain->bridge($iProfile, 'thumb.profile');
    $iMain->bridge($iIconNormal, 'thumb.normal');
    $iMain->bridge($iSquare, 'thumb.icon');

    //Remove temp files
    @unlink($path . '/p_' . $name);
    @unlink($path . '/m_' . $name);
    @unlink($path . '/in_' . $name);
    @unlink($path . '/is_' . $name);

    //Update row
    $this->modified_date = date('Y-m-d H:i:s');
    $this->photo_id = $iMain->getIdentity();
    $this->save();

    return $this;
  }

  function isViewable() {
    return $this->authorization()->isAllowed(null, 'view');
  }

  function isEditable() {
    return $this->authorization()->isAllowed(null, 'edit');
  }

  function isDeletable() {
    return $this->authorization()->isAllowed(null, 'delete');
  }

  public function setAlbumCover($photo) {

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
        'parent_type' => 'sesmusic_album',
        'parent_id' => $this->getIdentity()
    );

    //Save
    $storage = Engine_Api::_()->storage();

    $image = Engine_Image::factory();
    $image->open($file)
            ->resize(1600, 1600)
            ->write($path . '/m_' . $name)
            ->destroy();

    //Store
    $iMain = $storage->create($path . '/m_' . $name, $params);
    $iMain->bridge($iMain, 'thumb.profile');

    //Remove temp files
    @unlink($path . '/m_' . $name);

    $this->album_cover = $iMain->getIdentity();
    $this->save();

    return $this;
  }

}