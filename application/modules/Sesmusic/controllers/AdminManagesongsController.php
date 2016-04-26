<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: AdminManagesongsController.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_AdminManagesongsController extends Core_Controller_Action_Admin {

  public function indexAction() {

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesmusic_admin_main', array(), 'sesmusic_admin_main_managesongs');

    $ratingTable = Engine_Api::_()->getDbtable('ratings', 'sesmusic');
    $favouriteTable = Engine_Api::_()->getDbtable('favourites', 'sesmusic');

    $this->view->formFilter = $formFilter = new Sesmusic_Form_Admin_FilterSongs();
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          Engine_Api::_()->getDbtable('playlistsongs', 'sesmusic')->delete(array('albumsong_id =?' => $value));
          $ratingTable->delete(array('resource_type =?' => 'sesmusic_albumsong', 'resource_id =?' => $value));
          $favouriteTable->delete(array('resource_type =?' => 'sesmusic_albumsong', 'resource_id =?' => $value));
          $musicAlbum = Engine_Api::_()->getItem('sesmusic_albumsong', $value);
          $musicAlbum->delete();
        }
      }
    }

    $values = array();
    if ($formFilter->isValid($this->_getAllParams()))
      $values = $formFilter->getValues();

    $values = array_merge(array(
        'order' => $_GET['order'],
        'order_direction' => $_GET['order_direction'],
            ), $values);
    $this->view->assign($values);

    $musicAlbumSongs = Engine_Api::_()->getDbTable('albumsongs', 'sesmusic');
    $musicAlbumSongsName = $musicAlbumSongs->info('name');
    $musicAlbums = Engine_Api::_()->getDbTable('albums', 'sesmusic');
    $musicAlbumsName = $musicAlbums->info('name');
    $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');

    $select = $musicAlbumSongs->select()->order((!empty($_GET['order']) ? $_GET['order'] : 'albumsong_id' ) . ' ' . (!empty($_GET['order_direction']) ? $_GET['order_direction'] : 'DESC' ));

    if (!empty($_GET['album_name'])) {
      $select->setIntegrityCheck(false)
              ->from($musicAlbumSongsName)
              ->joinLeft($musicAlbumsName, "$musicAlbumSongsName.album_id = $musicAlbumsName.album_id")->where($musicAlbumsName . '.title LIKE ?', '%' . $_GET['album_name'] . '%');
    }

    if (!empty($_GET['owner_name'])) {
      $select->setIntegrityCheck(false)
              ->from($musicAlbumSongsName)
              ->joinLeft($musicAlbumsName, "$musicAlbumSongsName.album_id = $musicAlbumsName.album_id", null)
              ->joinLeft($tableUserName, "$musicAlbumsName.owner_id = $tableUserName.user_id", null)
              ->where($tableUserName . '.displayname LIKE ?', '%' . $_GET['owner_name'] . '%');
    }

    if (!empty($_GET['category_id']))
      $select->where($musicAlbumSongsName . '.category_id =?', $_GET['category_id']);

    if (!empty($_GET['name']))
      $select->where($musicAlbumSongsName . '.title LIKE ?', '%' . $_GET['name'] . '%');

    if (!empty($_GET['lyrics']))
      $select->where($musicAlbumSongsName . '.lyrics LIKE ?', '%' . $_GET['lyrics'] . '%');

    if (isset($_GET['rating']) && $_GET['rating'] != '') {
      if (!empty($_GET['rating']))
        $select->where($musicAlbumSongsName . '.rating <> ?', 0);
      else
        $select->where($musicAlbumSongsName . '.rating = ?', $_GET['rating']);
    }

    if (isset($_GET['upcoming']) && $_GET['upcoming'] != '') {
      if (!empty($_GET['upcoming']))
        $select->where($musicAlbumSongsName . '.upcoming = ?', $_GET['upcoming']);
      else
        $select->where($musicAlbumSongsName . '.upcoming = ?', $_GET['upcoming']);
    }

    if (isset($_GET['track_id']) && $_GET['track_id'] != '') {
      if (!empty($_GET['track_id']))
        $select->where($musicAlbumSongsName . '.track_id <> ?', 0);
      else
        $select->where($musicAlbumSongsName . '.track_id = ?', $_GET['track_id']);
    }

    if (isset($_GET['hot']) && $_GET['hot'] != '') {
      if (!empty($_GET['hot']))
        $select->where($musicAlbumSongsName . '.hot = ?', $_GET['hot']);
      else
        $select->where($musicAlbumSongsName . '.hot = ?', $_GET['hot']);
    }

    if (!empty($_GET['creation_date']))
      $select->where($musicAlbumSongsName . '.creation_date LIKE ?', $_GET['creation_date'] . '%');

    if (isset($_GET['featured']) && $_GET['featured'] != '') {
      if (!empty($_GET['featured']))
        $select->where($musicAlbumSongsName . '.featured = ?', $_GET['featured']);
      else
        $select->where($musicAlbumSongsName . '.featured = ?', $_GET['featured']);
    }

    if (isset($_GET['sponsored']) && $_GET['sponsored'] != '') {
      if (!empty($_GET['sponsored']))
        $select->where($musicAlbumSongsName . '.sponsored = ?', $_GET['sponsored']);
      else
        $select->where($musicAlbumSongsName . '.sponsored = ?', $_GET['sponsored']);
    }

    if (isset($_GET['artists']) && $_GET['artists'] != '') {
      if (!empty($_GET['artists']))
        $select->where($musicAlbumSongsName . ".artists LIKE ? ", '%' . $_GET['artists'] . '%');
    }

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(100);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
  }

  //Featured Action
  public function featuredAction() {

    $albumsong_id = $this->_getParam('id');
    if (!empty($albumsong_id)) {
      $albumSong = Engine_Api::_()->getItem('sesmusic_albumsong', $albumsong_id);
      $albumSong->featured = !$albumSong->featured;
      $albumSong->save();
    }
    $this->_redirect('admin/sesmusic/managesongs');
  }

  //Sponsored Action
  public function sponsoredAction() {

    $albumsong_id = $this->_getParam('id');
    if (!empty($albumsong_id)) {
      $albumSong = Engine_Api::_()->getItem('sesmusic_albumsong', $albumsong_id);
      $albumSong->sponsored = !$albumSong->sponsored;
      $albumSong->save();
    }
    $this->_redirect('admin/sesmusic/managesongs');
  }

  //Hot Action
  public function hotAction() {

    $albumsong_id = $this->_getParam('id');
    if (!empty($albumsong_id)) {
      $albumSong = Engine_Api::_()->getItem('sesmusic_albumsong', $albumsong_id);
      $albumSong->hot = !$albumSong->hot;
      $albumSong->save();
    }
    $this->_redirect('admin/sesmusic/managesongs');
  }

  //Hot Action
  public function upcomingAction() {

    $albumsong_id = $this->_getParam('id');
    if (!empty($albumsong_id)) {
      $albumSong = Engine_Api::_()->getItem('sesmusic_albumsong', $albumsong_id);
      $albumSong->upcoming = !$albumSong->upcoming;
      $albumSong->save();
    }
    $this->_redirect('admin/sesmusic/managesongs');
  }

  public function deleteAction() {

    $this->_helper->layout->setLayout('admin-simple');
    $this->view->sesmusic_id = $id = $this->_getParam('id');

    $ratingTable = Engine_Api::_()->getDbtable('ratings', 'sesmusic');
    $favouriteTable = Engine_Api::_()->getDbtable('favourites', 'sesmusic');

    //Check post
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $musicAlbumSong = Engine_Api::_()->getItem('sesmusic_albumsong', $id);
        Engine_Api::_()->getDbtable('playlistsongs', 'sesmusic')->delete(array('albumsong_id =?' => $id));
        $ratingTable->delete(array('resource_type =?' => 'sesmusic_albumsong', 'resource_id =?' => $id));
        $favouriteTable->delete(array('resource_type =?' => 'sesmusic_albumsong', 'resource_id =?' => $id));
        $file = Engine_Api::_()->getItem('storage_file', $musicAlbumSong->file_id);
        if ($file)
          $file->remove();

        $musicAlbumSong->delete();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('You have successfully delete song.')
      ));
    }

    //Output
    $this->renderScript('admin-manage/delete.tpl');
  }

  public function viewAction() {
    $this->view->item = $albumsong = Engine_Api::_()->getItem('sesmusic_albumsong', $this->_getParam('id', null));
    $this->view->album = $this->view->item->getParent();
    //Artist Work
    if ($albumsong->artists) {
      $artists = json_decode($albumsong->artists);
      if ($artists)
        $this->view->artists_array = Engine_Api::_()->getDbTable('artists', 'sesmusic')->getArtists($artists);
    }
  }

}
