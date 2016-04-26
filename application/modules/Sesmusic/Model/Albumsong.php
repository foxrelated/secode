<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Albumsong.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Model_Albumsong extends Core_Model_Item_Abstract {

  public function getShortType($inflect = false) {
    return 'song';
  }

  public function getMediaType() {
    return 'song';
  }

  public function getTitle() {
    if (!empty($this->title)) {
      return $this->title;
    } else {
      $translate = Zend_Registry::get('Zend_Translate');
      return $translate->translate('Untitled Song');
    }
  }

  public function setTitle($newTitle) {
    $this->title = $newTitle;
    $this->save();
    return $this;
  }

  public function getFilePath() {
    $file = Engine_Api::_()->getItem('storage_file', $this->file_id);
    if ($file)
      return $file->map();
  }

  public function getPhotoUrl($type = NULL) {

    $photo_id = $this->photo_id;
    $defaultphoto = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.songdefaultphoto');
    if (empty($photo_id) && $defaultphoto) {
      $path = Zend_Registry::get('Zend_View')->baseUrl() . '/' . $defaultphoto;
      return $path;
    } elseif ($photo_id) {
      $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id, $type);
      return $file->map();
    }
  }

  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array()) {

    $params = array_merge(array(
        'route' => 'sesmusic_albumsong_view',
        'reset' => true,
        'albumsong_id' => $this->albumsong_id,
        'slug' => $this->getSlug(),
            ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, $reset);
  }

  public function getParent($recurseType = NULL) {
    return Engine_Api::_()->getItem('sesmusic_albums', $this->album_id);
  }

  public function getOwner($recurseType = NULL) {
    $album = $this->getParent();
    $owner = Engine_Api::_()->getItem('user', $album->owner_id);
    return $owner;
  }

  public function getRichContent($view = false, $params = array()) {

    $musicEmbedded = '';
    if ($view == false) {
      $album = $this->getParent();
      $desc = strip_tags($album->description);
      $desc = "<div class='sesmusic_feed_desc'>" . (Engine_String::strlen($desc) > 255 ? Engine_String::substr($desc, 0, 255) . '...' : $desc) . "</div>";
      $view = Zend_Registry::get('Zend_View');
      $view->playlist = $album;
      $view->songs = array($this);
      $view->short_player = true;
      $musicEmbedded = $desc . $view->render('application/modules/Sesmusic/views/scripts/_Player.tpl');
    }
    return $musicEmbedded;
  }

  /**
   * Returns languagified play count
   */
  public function playCountLanguagefield() {
    return vsprintf(Zend_Registry::get('Zend_Translate')->_(array('%s play', '%s play', $this->play_count)), Zend_Locale_Format::toNumber($this->play_count)
    );
  }

  /**
   * Deletes songs from the Storage engine if no other playlists are
   * using the file, and from the playlist
   *
   * @return null
   */
  public function deleteUnused() {

    $file = Engine_Api::_()->getItem('storage_file', $this->file_id);
    if ($file) {
      $table = Engine_Api::_()->getDbtable('albumsongs', 'sesmusic');
      $count = $table->select()
              ->from($table->info('name'), 'count(*) as count')
              ->where('file_id = ?', $file->getIdentity())
              ->query()
              ->fetchColumn(0);
      if ($count <= 1) {
        try {
          $file->remove();
        } catch (Exception $e) {
          
        }
      }
    }
    $this->delete();
  }

  public function getAuthorizationItem() {
    return $this->getParent();
  }

  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   * 
   */
  public function comments() {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   * 
   */
  public function likes() {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

  public function setPhoto($photo, $param = null, $soundcloudImages = array()) {


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
        'parent_type' => 'sesmusic_albumsong',
        'parent_id' => $this->getIdentity()
    );

    //Save
    $storage = Engine_Api::_()->storage();

    if ($param == 'mainPhoto') {
      $image = Engine_Image::factory();
      $image->open($file)
              ->resize(500, 500)
              ->write($path . '/m_' . $name)
              ->destroy();
    } else {
      $image = Engine_Image::factory();
      $image->open($file)
              ->resize(1600, 1600)
              ->write($path . '/m_' . $name)
              ->destroy();
    }

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
    if ($soundcloudImages['image'] && $soundcloudImages['file_id']) {
      $image = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary/' . $soundcloudImages['file_id'] . ".jpg";
      @unlink($image);
    }
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