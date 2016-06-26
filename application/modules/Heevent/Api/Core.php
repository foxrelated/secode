<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Heevent_Api_Core extends Event_Api_Core
{

public function setPhoto($category,$photo){

  if( $photo instanceof Zend_Form_Element_File ) {
    $file = $photo->getFileName();
    $fileName = $file;
  } else if( $photo instanceof Storage_Model_File ) {
    $file = $photo->temporary();
    $fileName = $photo->name;
  } else if( $photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id) ) {
    $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
    $file = $tmpRow->temporary();
    $fileName = $tmpRow->name;
  } else if( is_array($photo) && !empty($photo['tmp_name']) ) {
    $file = $photo['tmp_name'];
    $fileName = $photo['name'];
  } else if( is_string($photo) && file_exists($photo) ) {
    $file = $photo;
    $fileName = $photo;
  } else {
    throw new Exception('invalid argument passed to setPhoto');
  }

  if( !$fileName ) {
    $fileName = basename($file);
  }

  $extension = ltrim(strrchr(basename($fileName), '.'), '.');
  $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
  $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

  $params = array(
    'parent_type' => $category->getType(),
    'parent_id' => $category->getIdentity(),
    'user_id' => '1',
    'name' => $fileName,
  );

  // Save
  $filesTable = Engine_Api::_()->getItemTable('storage_file');
// Resize image (main)
  $mainPath = $path . DIRECTORY_SEPARATOR . $base . 'cover.' . $extension;
  $image = Engine_Image::factory();
  $image->open($file)
    ->resize(400, 140, false)
    ->write($mainPath)
    ->destroy();

  // Resize image (normal)
  $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
  $image = Engine_Image::factory();
  $image->open($file)
    ->resize(40, 40)
    ->write($normalPath)
    ->destroy();

    $pinPath = $path . DIRECTORY_SEPARATOR . $base . '_pin.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
        ->resize(700, 700)
        ->write($normalPath)
        ->destroy();
  // Heevent
  $photo_normal = $this->getCategoryPhoto($category);
  if($photo_normal){
    $photo_icon = $this->getCategoryPhoto($category, 'thumb.icon');
    @unlink($photo_normal->storage_path);
    @unlink($photo_icon->storage_path);
    $photo_normal->delete();
    $photo_icon->delete();
  }
  $iMain = $filesTable->createFile($mainPath, $params);
  $iIconNormal = $filesTable->createFile($normalPath, $params);
  $pinNormal = $filesTable->createFile($pinPath, $params);

  $iMain->bridge($iIconNormal, 'thumb.icon');
  $iMain->bridge($iMain, 'thumb.normal');
  $iMain->bridge($pinNormal, 'thumb.pin');


  // Remove temp files
  @unlink($mainPath);
  @unlink($normalPath);

  //$category
  /**
   * @var $photosTable Heevent_Model_DbTable_Categoryphoto
   */


  return $this;
}

  /**
   * @param Event_Model_Category $category
   * @param string $type
   * @return null|Storage_Model_File
   */
  public function getCategoryPhoto(Event_Model_Category $category, $type = "thumb.normal")
  {
    $select = $this->getFilesTable()->select()->where('parent_id = ?', $category->getIdentity())->where('parent_type = ?', 'event_category')->where('type = ?', $type);
    return $this->getFilesTable()->fetchRow($select);
  }
  public function getGateway($gateway_id)
  {
    return $this->getPlugin($gateway_id)->getGateway();
  }
  public function getPlugin($gateway_id)
  {
    if (null === $this->_plugin) {
      /**
       * @var $gatewayTb Payment_Model_Gateway
       */
      if (null == ($gateway = Engine_Api::_()->getItem('payment_gateway', $gateway_id))) {
        return null;
      }

      Engine_Loader::loadClass($gateway->plugin);
      if (!class_exists($gateway->plugin)) {
        return null;
      }

      $class = str_replace('Payment', 'Heevent', $gateway->plugin);

      Engine_Loader::loadClass($class);
      if (!class_exists($class)) {
        return null;
      }

      $plugin = new $class($gateway);

      if (!($plugin instanceof Engine_Payment_Plugin_Abstract)) {
        throw new Engine_Exception(sprintf('Payment plugin "%1$s" must ' . 'implement Engine_Payment_Plugin_Abstract', $class));
      }
      $this->_plugin = $plugin;
    }

    return $this->_plugin;
  }
  /**
   * @param Event_Model_Category $category
   * @return Zend_Db_Table_Rowset_Abstract
   */
  public function getCategoryPhotos(Event_Model_Category $category)
  {
    $select = $this->getFilesTable()->select()->where('parent_id = ?', $category->getIdentity())->where('parent_type = ?', 'event_category');
    return $this->getFilesTable()->fetchAll($select);
  }

  public function deleteCategoryPhotos(Event_Model_Category $category)
  {
    $photos = $this->getCategoryPhotos($category);
    foreach($photos as $photo)
    {
      if($photo->storage_path){
        unlink($photo->storage_path);
        $photo->delete();
      }
    }
  }
  /**
   * @return Storage_Model_DbTable_Files
   */
  public function getFilesTable()
  {
    return Engine_Api::_()->getDbTable('files', 'storage');
  }

  public function getCategoryCovers(){
    $categories = Engine_Api::_()->getDbtable('categories', 'event')->getCategoriesAssoc();
    $categoryPhotosTbl = Engine_Api::_()->getDbtable('categoryphotos', 'heevent');
    $categoryPhotos = array();
    asort($categories, SORT_LOCALE_STRING);
    foreach ($categories as $k => $v) {
      if($cover = $categoryPhotosTbl->getCover($k))
        $categoryPhotos[$k] = $cover->getPhotoUrl();
    }
    return $categoryPhotos;
  }

}