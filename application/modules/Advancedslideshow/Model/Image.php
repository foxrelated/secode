<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Image.php 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedslideshow_Model_Image extends Core_Model_Item_Collectible {

  protected $_parent_type = 'advancedslideshow_image';
  protected $_owner_type = 'user';
  protected $_searchTriggers = false;
  protected $_collection_type = 'advancedslideshow_image';

//  /**
//   * Gets a url to the current photo representing this item
//   *
//   * @param string type: The photo type (null -> main, thumb, icon, etc);
//   * @return string The photo url
//   */
//  public function getPhotoUrl($type = null) {
//    if (empty($this->file_id)) {
//      return null;
//    }
//
//    $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id, $type);
//    if (!$file) {
//      return null;
//    }
//
//    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('storage');
//    $coreversion = $coremodule->version;
//    if ($coreversion <= '4.1.1') {
//      return $file->map();
//    } else {
//      return Zend_Controller_Front::getInstance()->getBaseUrl() . $file->storage_path;
//    }
//  }
  
  public function getPhotoUrl($type = null)
  {
    if( empty($this->file_id) ) {
      return null;
    }

    $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id, $type);
    if( !$file ) {
      return null;
    }

    return $file->map();
  }  

  /**
   * Return a truncate text
   *
   * @return truncate text
   * */
  public function truncate60Url() {
    $tmpBody = strip_tags($this->url);
    return ( Engine_String::strlen($tmpBody) > 60 ? Engine_String::substr($tmpBody, 0, 60) . '..' : $tmpBody );
  }

  /**
   * Delete photo belongings
   *
   * */
  protected function _postDelete() {
    if ($this->_disableHooks)
      return;

    try {
      $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id);
      if (!empty($file))
        $file->remove();
      $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id, 'thumb.normal');
      if (!empty($file))
        $file->remove();
    } catch (Exception $e) {

    }
  }
  
    public function setNoobThumb($photo, $advancedslideshow_id = null) {
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
            throw new Sitearticle_Model_Exception('invalid argument passed to setPhoto');
        }
        if (!$fileName) {
            $fileName = basename($file);
        }
        $extension = ltrim(strrchr(basename($fileName), '.'), '.');
        $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_type' => 'advanceslideshow_noobslides',
            'parent_id' => $this->getIdentity(),
        );
        
        $advancedslideshow_id = !empty($advancedslideshow_id)?$advancedslideshow_id:$this->getIdentity();
         $advancedslideshow = Engine_Api::_()->getItem('advancedslideshow', $advancedslideshow_id);

      //GET SLIDESHOW HEIGHT
      $height = $advancedslideshow->height;

      //GET SLIDESHOW WIDTH
      $width = $advancedslideshow->width;
        // Save
        $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
        
        // Resize image (main)
        $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(720, 720)
                ->write($mainPath)
                ->destroy();
        // Resize image (profile)
//        $profilePath = $path . DIRECTORY_SEPARATOR . $base . '_p.' . $extension;
//        $image = Engine_Image::factory();
//        $image->open($file)
//                ->resize(200, 400)
//                ->write($profilePath)
//                ->destroy();
        // Resize image (normal)
        $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize($width, $height)
                ->write($normalPath)
                ->destroy();
        // Resize image (icon)
        $squarePath = $path . DIRECTORY_SEPARATOR . $base . '_is.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file);
        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)
                ->write($squarePath)
                ->destroy();
        // Store
        $iMain = $filesTable->createFile($mainPath, $params);
//        $iProfile = $filesTable->createFile($profilePath, $params);
        $iIconNormal = $filesTable->createFile($normalPath, $params);
        $iSquare = $filesTable->createFile($squarePath, $params);
//        $iMain->bridge($iProfile, 'thumb.profile');
        $iMain->bridge($iIconNormal, 'thumb.normal');
        $iMain->bridge($iSquare, 'thumb.icon');
        // Remove temp files
        @unlink($mainPath);
//        @unlink($profilePath);
        @unlink($normalPath);
        @unlink($squarePath);
        
        return $iMain->file_id;
    }
}
?>