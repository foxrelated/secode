<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Chanelphoto.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_Model_Chanelphoto extends Core_Model_Item_Abstract {

  protected $_type = 'sesvideo_chanelphoto';

  //get view page href
  public function getHref($params = array()) {
    $params = array_merge(array(
        'route' => 'sesvideo_chanel',
        'reset' => true,
        'controller' => 'chanel',
        'action' => 'view',
        'chanel_id' => $this->chanel_id,
        'photo_id' => $this->getIdentity(),
            ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
                    ->assemble($params, $route, $reset);
  }

  public function getPhotoIndex() {
    return $this->getTable()
                    ->select()
                    ->from($this->getTable(), new Zend_Db_Expr('COUNT(chanelphoto_id)'))
                    ->where('chanel_id = ?', $this->chanel_id)
                    ->where('`order` < ?', $this->order)
                    ->order('order ASC')
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
  }

  public function isOwner(Core_Model_Item_Abstract $owner) {
    if (empty($this->album_id)) {
      //return (($this->owner_id == $owner->getIdentity()) && ($this->owner_type == $owner->getType()));
    }
    return parent::isOwner($owner);
  }

  public function getChanel() {
    return Engine_Api::_()->getItem('sesvideo_chanel', $this->chanel_id);
  }

  //get next photo
  public function getNextPhoto() {
    $table = $this->getTable();
    $select = $table->select()
            ->where('chanel_id = ?', $this->chanel_id)
            ->where('`order` > ?', $this->order)
            ->order('order ASC')
            ->limit(1);
    $photo = $table->fetchRow($select);

    if (!$photo) {
      // Get first photo instead
      $select = $table->select()
              ->where('chanel_id = ?', $this->chanel_id)
              ->order('order ASC')
              ->limit(1);
      $photo = $table->fetchRow($select);
    }

    return $photo;
  }

  //get previous photo
  public function getPreviousPhoto() {
    $table = $this->getTable();
    $select = $table->select()
            ->where('chanel_id = ?', $this->chanel_id)
            ->where('`order` < ?', $this->order)
            ->order('order DESC')
            ->limit(1);
    $photo = $table->fetchRow($select);

    if (!$photo) {
      // Get last photo instead
      $select = $table->select()
              ->where('chanel_id = ?', $this->chanel_id)
              ->order('order DESC')
              ->limit(1);
      $photo = $table->fetchRow($select);
    }

    return $photo;
  }

  //upload photo
  public function setPhoto($photo, $isURL = false) {
    if (!$isURL) {
      if ($photo instanceof Zend_Form_Element_File) {
        $file = $photo->getFileName();
        $fileName = $file;
      } else if ($photo instanceof Storage_Model_File) {
        $file = $photo->temporary();
        $fileName = $photo->name;
      } else if ($photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id)) {
        $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
        $file = $tmpRow->temporary();
        $fileName = $tmpRow->name;
      } else if (is_array($photo) && !empty($photo['tmp_name'])) {
        $file = $photo['tmp_name'];
        $fileName = $photo['name'];
      } else if (is_string($photo) && file_exists($photo)) {
        $file = $photo;
        $fileName = $photo;
      } else {
        throw new User_Model_Exception('invalid argument passed to setPhoto');
      }
      $name = basename($file);
      $extension = ltrim(strrchr($fileName, '.'), '.');
      $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
    } else {
      $fileName = time() . '_sesalbum';
      $PhotoExtension = '.' . pathinfo($photo, PATHINFO_EXTENSION);
      $filenameInsert = $fileName . $PhotoExtension;
      $copySuccess = @copy($photo, APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary/' . $filenameInsert);
      if ($copySuccess)
        $file = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' . DIRECTORY_SEPARATOR . $filenameInsert;
      else
        return false;
      $name = basename($photo);
      $extension = ltrim(strrchr($name, '.'), '.');
      $base = rtrim(substr(basename($name), 0, strrpos(basename($name), '.')), '.');
    }
    if (!$fileName) {
      $fileName = $file;
    }
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
        'parent_type' => $this->getType(),
        'parent_id' => $this->getIdentity(),
        'user_id' => $this->owner_id,
        'name' => $fileName,
    );
    // Save
    $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
    /* setting of image dimentions from core settings */
    $core_settings = Engine_Api::_()->getApi('settings', 'core');
    $main_height = $core_settings->getSetting('sesalbum.mainheight', 1600);
    $main_width = $core_settings->getSetting('sesalbum.mainwidth', 1600);
    $normal_height = $core_settings->getSetting('sesalbum.normalheight', 500);
    $normal_width = $core_settings->getSetting('sesalbum.normalwidth', 500);
    // Resize image (main)
    $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
            ->resize($main_width, $main_height)
            ->write($mainPath)
            ->destroy();
    // Resize image (normal) make same image for activity feed so it open in pop up with out jump effect.
    $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
            ->resize($normal_width, $normal_height)
            ->write($normalPath)
            ->destroy();
    // normal main  image resize
    $normalMainPath = $path . DIRECTORY_SEPARATOR . $base . '_nm.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
            ->resize($normal_width, $normal_height)
            ->write($normalMainPath)
            ->destroy();

    // Resize image (icon)
    $squarePath = $path . DIRECTORY_SEPARATOR . $base . '_is.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file);

    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;

    $image->resample($x, $y, $size, $size, 100, 100)
            ->write($squarePath)
            ->destroy();

    // Store
    try {
      $iSquare = $filesTable->createFile($squarePath, $params);
      $iMain = $filesTable->createFile($mainPath, $params);
      $iIconNormal = $filesTable->createFile($normalPath, $params);
      $iNormalMain = $filesTable->createFile($normalMainPath, $params);
      $iMain->bridge($iNormalMain, 'thumb.normalmain');
      $iMain->bridge($iIconNormal, 'thumb.normal');
      $iMain->bridge($iSquare, 'thumb.icon');
    } catch (Exception $e) {
      @unlink($file);
      // Remove temp files
      @unlink($mainPath);
      @unlink($normalPath);
      @unlink($squarePath);
      @unlink($normalMainPath);
      // Throw
      if ($e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE) {
        throw new Sesalbum_Model_Exception($e->getMessage(), $e->getCode());
      } else {
        throw $e;
      }
    }
    @unlink($file);
    // Remove temp files
    @unlink($mainPath);
    @unlink($normalPath);
    @unlink($squarePath);
    @unlink($normalMainPath);
    // Update row
    $this->modified_date = date('Y-m-d H:i:s');
    $this->file_id = $iMain->file_id;
    $this->ip_address = $_SERVER['REMOTE_ADDR'];
    $this->save();
    // Delete the old file?
    if (!empty($tmpRow)) {
      $tmpRow->delete();
    }
    return $this;
  }

  public function getPhotoUrl($type = null) {
    $photo_id = $this->file_id;
    if (!$photo_id) {
      return 'application/modules/Sesalbum/externals/images/nophoto_album_thumb_normal.png';
    }

    $file = Engine_Api::_()->getItemTable('storage_file')->getFile($photo_id, $type);
    if (!$file) {
      return 'application/modules/Sesalbum/externals/images/nophoto_album_thumb_normal.png';
    }

    return $file->map();
  }

  public function getType() {
    return 'sesvideo_chanelphoto';
  }

  // Interfaces

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

}
