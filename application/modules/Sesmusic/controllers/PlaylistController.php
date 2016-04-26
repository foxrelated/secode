<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: PlaylistController.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_PlaylistController extends Core_Controller_Action_Standard {

  public function init() {

    //Get viewer info
    $this->view->viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //Get subject
    if (null !== ($playlist_id = $this->_getParam('playlist_id')) && null !== ($playlist = Engine_Api::_()->getItem('sesmusic_playlist', $playlist_id)) && $playlist instanceof Sesmusic_Model_Playlist && !Engine_Api::_()->core()->hasSubject()) {
      Engine_Api::_()->core()->setSubject($playlist);
    }
  }

  public function browseAction() {
    $this->_helper->content->setEnabled();
  }

  //Manage Action
  public function manageAction() {

    //Only members can manage music
    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.checkmusic'))
      return $this->_forward('notfound', 'error', 'core');

    //Get paginator
    $values['user'] = $this->view->viewer_id;
    $values['action'] = 'manage';
    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('playlists', 'sesmusic')->getPlaylistPaginator($values);
    $paginator->setItemCountPerPage(20);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    //Render
    $this->_helper->content->setEnabled();
  }

  //View Action
  public function viewAction() {

    //Set layout
    if ($this->_getParam('popout')) {
      $this->view->popout = true;
      $this->_helper->layout->setLayout('default-simple');
    }

    //Check subject
    if (!$this->_helper->requireSubject()->isValid())
      return;

    //Get viewer/subject
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer->getIdentity();

    $this->view->playlist = $playlist = Engine_Api::_()->core()->getSubject('sesmusic_playlist');

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.checkmusic'))
      return $this->_forward('notfound', 'error', 'core');

    //Increment view count
    if (!$viewer->isSelf($playlist->getOwner())) {
      $playlist->view_count++;
      $playlist->save();
    }


//    $settings = Engine_Api::_()->getApi('settings', 'core');
//    $authorizationApi = Engine_Api::_()->authorization();
//
//    //Songs settings.
//    $this->view->songlink = unserialize($settings->getSetting('sesmusic.songlink'));
//
//    $this->view->canAddPlaylistAlbumSong = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'addplaylist_albumsong');
//
//    $this->view->downloadAlbumSong = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'download_albumsong');
//    
//    $this->view->canAddFavouriteAlbumSong = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'addfavourite_albumsong');
//
//    $allowShowRating = $settings->getSetting('sesmusic.ratealbumsong.show', 1);
//    $allowRating = $settings->getSetting('sesmusic.albumsong.rating', 1);
//    if ($allowRating == 0) {
//      if ($allowShowRating == 0)
//        $showRating = false;
//      else
//        $showRating = true;
//    }
//    else
//      $showRating = true;
//    $this->view->showAlbumSongRating = $showRating;

    //Render
    $this->_helper->content->setEnabled();
  }

  //Edit Action
  public function editAction() {

    //Only members can upload music
    if (!$this->_helper->requireUser()->isValid())
      return;

    if (!$this->_helper->requireSubject('sesmusic_playlist')->isValid())
      return;

    //Get playlist
    $this->view->playlist = $playlist = Engine_Api::_()->core()->getSubject('sesmusic_playlist');

    //Make form
    $this->view->form = $form = new Sesmusic_Form_EditPlaylist();

    $form->populate($playlist->toarray());

    if (!$this->getRequest()->isPost())
      return;

    if (!$form->isValid($this->getRequest()->getPost()))
      return;

    $values = $form->getValues();
    unset($values['file']);

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.checkmusic'))
      return $this->_forward('notfound', 'error', 'core');

    $db = Engine_Api::_()->getDbTable('playlists', 'sesmusic')->getAdapter();
    $db->beginTransaction();
    try {
      $playlist->title = $values['title'];
      $playlist->description = $values['description'];
      $playlist->save();

      //Photo upload for playlist
      if (!empty($values['mainphoto'])) {
        $previousPhoto = $playlist->photo_id;
        if ($previousPhoto) {
          $playlistPhoto = Engine_Api::_()->getItem('storage_file', $previousPhoto);
          $playlistPhoto->delete();
        }
        $playlist->setPhoto($form->mainphoto, 'mainPhoto');
      }

      if (isset($values['remove_photo']) && !empty($values['remove_photo'])) {
        $storage = Engine_Api::_()->getItem('storage_file', $playlist->photo_id);
        $playlist->photo_id = 0;
        $playlist->save();
        if ($storage)
          $storage->delete();
      }

      $db->commit();
    } catch (Exception $e) {
      $db->rollback();
      throw $e;
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sesmusic_playlists', true);
  }

  //Delete Playlist Action
  public function deleteAction() {

    $playlist = Engine_Api::_()->getItem('sesmusic_playlist', $this->getRequest()->getParam('playlist_id'));

    //In smoothbox
    $this->_helper->layout->setLayout('default-simple');

    $this->view->form = $form = new Sesmusic_Form_Delete();
    $form->setTitle('Delete Playlist?');
    $form->setDescription('Are you sure you want to delete this playlist?');
    $form->submit->setLabel('Delete Playlist');


    if (!$playlist) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Playlist doesn't exists or not authorized to delete");
      return;
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $db = $playlist->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      //Delete all playlist songs which is related to this playlist
      Engine_Api::_()->getDbtable('playlistsongs', 'sesmusic')->delete(array('playlist_id =?' => $this->_getParam('playlist_id')));
      $playlist->delete();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('The selected playlist has been deleted.');
    return $this->_forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'sesmusic_playlists', true), 'messages' => Array($this->view->message)));
  }

  //Delete playlist songs Action
  public function deletePlaylistsongAction() {

    //Get song/playlist
    $song = Engine_Api::_()->getItem('sesmusic_playlistsongs', $this->_getParam('playlistsong_id'));
    $playlist = $song->getParent();

    //Check song/playlist
    if (!$song || !$playlist) {
      $this->view->success = false;
      $this->view->error = $this->view->translate('Invalid playlist');
      return;
    }

    //Get file
    $file = Engine_Api::_()->getItem('storage_file', $song->file_id);
    if (!$file) {
      $this->view->success = false;
      $this->view->error = $this->view->translate('Invalid playlist');
      return;
    }

    $db = $song->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      Engine_Api::_()->getDbtable('playlistsongs', 'sesmusic')->delete(array('playlistsong_id =?' => $this->_getParam('playlistsong_id')));
      $db->commit();
    } catch (Exception $e) {
      $db->rollback();
      $this->view->success = false;
      $this->view->error = $this->view->translate('Unknown database error');
      throw $e;
    }

    $this->view->success = true;
  }

  public function deleteCookiesAction() {

    $this->_helper->layout->setLayout('default-simple');
    $deleteId = $this->_getParam('song_id');
    //$deleteId = 13;
    $html = new DOMDocument();
    $cookiesData = urldecode($_COOKIE["sesmusic_playlists"]);
    $html->loadHTML($cookiesData);
    $htmlData = $html->getElementsByTagName('li');
    foreach ($htmlData as $element) {
      $elementid = $element->getAttribute('id');
      $checkId = str_replace('sesmusic_playlist_', '', $elementid);
      if ($checkId == $deleteId) {
        $element->parentNode->removeChild($element);
      }
    }

    setcookie('sesmusic_playlists', '', -100, "/");
    $html = $html->saveHTML();
    $html = str_replace(array('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">', '<html><body>', '</body></html>'), array('', '', ''), $html);
    setcookie('sesmusic_playlists', trim($html), time() + 86400, "/");
    echo "Success";
    die;
  }

}