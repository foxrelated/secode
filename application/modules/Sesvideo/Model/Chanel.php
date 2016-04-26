<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Chanel.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
class Sesvideo_Model_Chanel extends Core_Model_Item_Abstract {
  public function getHref($params = array()) {
    if (empty($this->custom_url) && is_null($this->custom_url) && $this->custom_url == '') {
      $custom_url = $this->chanel_id;
    } else
      $custom_url = $this->custom_url;
    $params = array_merge(array(
        'route' => 'sesvideo_chanel_view',
        'reset' => true,
        'chanel_id' => $custom_url
            ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
                    ->assemble($params, $route, $reset);
  }
	public function getTitle(){
		return $this->title;	
	}
  public function membership() {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('membership', 'sesvideo'));
  }
  public function countVideos() {
    $videoTable = Engine_Api::_()->getItemTable('sesvideo_chanelvideo');
    return $videoTable->select()
                    ->from($videoTable, new Zend_Db_Expr('COUNT(chanelvideo_id)'))
                    ->where('chanel_id = ?', $this->chanel_id)
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
  }
  public function count() {
    $photoTable = Engine_Api::_()->getItemTable('sesvideo_chanelphoto');
    return $photoTable->select()
                    ->from($photoTable, new Zend_Db_Expr('COUNT(chanelphoto_id)'))
                    ->where('chanel_id = ?', $this->chanel_id)
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
  }
  function getPhotoUrl($type = null) {
    $thumbnail_id = $this->thumbnail_id;
    if (!$thumbnail_id) {
      return 'application/modules/Sesvideo/externals/images/nochanelthumb.png';
    }
    $file = Engine_Api::_()->getItemTable('storage_file')->getFile($thumbnail_id, $type);
    if (!$file) {
      return 'application/modules/Sesvideo/externals/images/nochanelthumb.png';
    }
    return $file->map();
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
  /**
   * Gets a proxy object for the tags handler
   *
   * @return Engine_ProxyObject
   * */
  public function tags() {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
  }
	public function setCoverPhoto($photo){
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
				$unlink = false;
			} else {
				throw new User_Model_Exception('invalid argument passed to setPhoto');
			}
			  $name = basename($file);
				$extension = ltrim(strrchr($fileName, '.'), '.');
				$base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
		
    if( !$fileName ) {
      $fileName = $file;
    }
		 $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_type' => $this->getType(),
      'parent_id' => $this->getIdentity(),
      'user_id' => $this->owner_id,
      'name' => $fileName,
    );
    // Resize image (main)
    $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(1200, 700)
      ->write($mainPath)
      ->destroy();
    // Store
    try {
      $iMain = $filesTable->createFile($mainPath, $params);      
    } catch( Exception $e ) {
			@unlink($file);
      // Remove temp files
      @unlink($mainPath);
      // Throw
      if( $e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE ) {
        throw new Sesalbum_Model_Exception($e->getMessage(), $e->getCode());
      } else {
        throw $e;
      }
    }
    	if(!isset($unlink))
				@unlink($file);
    // Remove temp files
      @unlink($mainPath);
    // Update row
    $this->modified_date = date('Y-m-d H:i:s');
    $this->cover_id = $iMain->file_id;
    $this->save();
    // Delete the old file?
    if( !empty($tmpRow) ) {
      $tmpRow->delete();
    }
    return $this;
	}
}