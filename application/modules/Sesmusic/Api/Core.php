<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Core.php 2015-03-15 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Api_Core extends Core_Api_Abstract {

//Handle song upload
  public function createSong($file, $params = array()) {
    

    if (is_array($file)) {
      if (!is_uploaded_file($file['tmp_name']))
        throw new Sesmusic_Model_Exception('Invalid upload or file too large');

      $filename = $file['name'];
    } else if (is_string($file)) {
      $filename = $file;
    } else if ($file) {
      $name = $file->name;
      $filename = $file->storage_path;
      $file = $file->storage_path;
    } else {
      throw new Sesmusic_Model_Exception('Invalid upload or file too large');
    }

//Check file extension
    if (!preg_match('/\.(mp3|m4a|aac|mp4)$/iu', $filename))
      throw new Sesmusic_Model_Exception('Invalid file type');

//Upload to storage system
    if(isset($name) && !empty($name)) {
    $params = array_merge(array('type' => 'song', 'name' => $name, 'parent_type' => 'sesmusic_albumsongs', 'parent_id' => Engine_Api::_()->user()->getViewer()->getIdentity(), 'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(), 'extension' => substr($filename, strrpos($filename, '.') + 1)), $params);
    } else {
      $params = array_merge(array('type' => 'song', 'name' => $filename, 'parent_type' => 'sesmusic_albumsongs', 'parent_id' => Engine_Api::_()->user()->getViewer()->getIdentity(), 'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(), 'extension' => substr($filename, strrpos($filename, '.') + 1)), $params);
    }
    

    return Engine_Api::_()->storage()->create($file, $params);
  }

//Likes of albums and songs by all users and friends
  public function albumsSongsLikeResults($params = array()) {

    $likeTable = Engine_Api::_()->getItemTable('core_like');
    $select = $likeTable->select()
            ->from($likeTable->info('name'), array('poster_id'))
            ->where('resource_type = ?', $params['type'])
            ->where('resource_id = ?', $params['id'])
            ->order('like_id DESC');

    if (isset($params['limit']))
      $select = $select->limit($params['limit']);

    $friendsIds = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();
    if (!empty($friendsIds) && isset($params['showUsers']) && $params['showUsers'] == 'friends')
      $select = $select->where('poster_id IN (?)', (array) $friendsIds);

    return $select->query()->fetchAll();
  }

//Total likes according to viewer_id
  public function likeIds($params = array()) {

    $likeTable = Engine_Api::_()->getItemTable('core_like');
    return $likeTable->select()
                    ->from($likeTable->info('name'), array('resource_id'))
                    ->where('resource_type = ?', $params['type'])
                    ->where('poster_id = ?', $params['id'])
                    ->query()
                    ->fetchColumn();
  }

  public function getLikesContents($params = array()) {

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $likeTable = Engine_Api::_()->getDbTable('likes', 'core');
    $select = $likeTable->select()
            ->from($likeTable->info('name'))
            ->where('resource_type =?', $params['resource_type'])
            ->where('poster_id =?', $viewer_id);
    return Zend_Paginator::factory($select);
  }

}