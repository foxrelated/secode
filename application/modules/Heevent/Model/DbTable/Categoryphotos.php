<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Categoryphotos.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Heevent_Model_DbTable_Categoryphotos extends Engine_Db_Table
{
  protected $_rowClass = 'Heevent_Model_Categoryphoto';
  protected $_primary = 'photo_id';
  protected $categotyPhotos = array();
  protected $_sizeDefault = array('w' => 1092, 'h' => 301);
  public function setCovers($photos, $category_id)
  {
    $order = count($this->getCovers($category_id));
    if(is_string($photos)){
      $photos = array($photos);
    }
    foreach ($photos as $photoPath) {
      $cover = $this->createRow();
      $cover->setFromArray(array(
        'category_id' => $category_id,
        'order' => $order
      ));
      $order ++;
      $this->setCover($photoPath, $cover);
      $cover->save();
    }
    $this->categotyPhotos[$category_id] = null;
  }
  public function getCovers($category_id, $cache = true){
    if($category_id instanceof Event_Model_Event)
      $category_id = $category_id->category_id;
    if($category_id){
      if(!$this->categotyPhotos[$category_id] || !$cache)
        $this->categotyPhotos[$category_id] = $this->fetchAll($this->getCoversSelect($category_id)->order('order'));
      return $this->categotyPhotos[$category_id];
    }
  }
  public function getCoversSelect($category_id){
    return $this->select()->where('category_id = ?', $category_id);
  }
  public function getCover($category_id){
    return $this->fetchRow($this->getCoversSelect($category_id)->order('order'));
  }
  private function setCover($photo, $item)
  {
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
    $viewer = Engine_Api::_()->user()->getViewer();

    if( !$fileName ) {
      $fileName = $file;
    }
    $name = basename($fileName);
//
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' . DIRECTORY_SEPARATOR;
    $image = Engine_Image::factory();
    $image->open($fileName);
    $imgSize = array(
      'w' => $image->getWidth(),
      'h' => $image->getHeight(),
    );
    $resize = !($imgSize['w'] == $this->_sizeDefault['w'] && $imgSize['h'] == $this->_sizeDefault['h']);
    $defRatio = 29/8;
    $imgRatio = $imgSize['w'] / $imgSize['h'];
    $resample =
        abs(($imgSize['w'] * 8) / ($imgSize['h'] * 29) - 1) < .3333 ||
        ($imgSize['w'] > $this->_sizeDefault['w'] &&
        $imgSize['h'] > $this->_sizeDefault['h'] &&
        $imgSize['w'] > $imgSize['h']) ||
        $imgRatio > $defRatio;
    if($resample){
      $size = $imgSize;
      $x = 0;
      $y = 0;
      if($defRatio < $imgRatio){
        $size['w'] = $imgSize['h'] * $defRatio;
        $x = ($imgSize['w'] - $size['w']) / 2;
      } else {
        $size['h'] = $imgSize['w'] / $defRatio;
        $y = ($imgSize['h'] - $size['h']) / 2;
      }
      $image->resample($x, $y, $size['w'], $size['h'], $this->_sizeDefault['w'], $this->_sizeDefault['h']);
    } else if($resize)
      $image
        ->resize($this->_sizeDefault['w'], $this->_sizeDefault['h']);
    $image->write($path . $name)
      ->destroy();
    $params = array(
      'parent_type' => $item->getType(),
      'parent_id' => $item->getIdentity(),
      'user_id' => $viewer->getIdentity(),
      'name' => $name,
    );

    // Save
    $filesTable = Engine_Api::_()->getDbtable('files', 'storage');


    // Heevent
    try {
      $coverFile = $filesTable->createFile($path . $name, $params);

    } catch( Exception $e ) {
      // Remove temp files
      @unlink($path . $name);
      @unlink($fileName);
      // Throw
      if( $e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE ) {
        throw new Album_Model_Exception($e->getMessage(), $e->getCode());
      } else {
        throw $e;
      }
    }

    // Remove temp files
    @unlink($path . $name);
    @unlink($fileName);

    // Update row
    $item->file_id = $coverFile->file_id;
    $item->save();

    // Delete the old file?
    if( !empty($tmpRow) ) {
      $tmpRow->delete();
    }

    return $item;
  }

}
