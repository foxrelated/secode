<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: SongController.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_SongController extends Core_Controller_Action_Standard {

  public function init() {

    //Check auth
    if (!$this->_helper->requireAuth()->setAuthParams('sesmusic_album', null, 'view')->isValid())
      return;

    //Get viewer info
    $this->view->viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //Get subject
    if (null !== ($song_id = $this->_getParam('albumsong_id')) && null !== ($song = Engine_Api::_()->getItem('sesmusic_albumsong', $song_id)) && $song instanceof Sesmusic_Model_Albumsong) {
      Engine_Api::_()->core()->setSubject($song);
    }
  }

  public function browseAction() {
    $this->_helper->content->setEnabled();
  }

  public function printAction() {

    $this->_helper->layout->setLayout('default-simple');
    $this->view->albumsong = Engine_Api::_()->getItem('sesmusic_albumsong', $this->_getParam('albumsong_id', null));
    if (empty($this->view->albumsong))
      return $this->_forward('notfound', 'error', 'core');
  }

  public function lyricsAction() {

//    $viewer = Engine_Api::_()->user()->getViewer();
//    $this->view->viewer_id = $viewer->getIdentity();
//
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
//    if (!$settings->getSetting('sesmusic.checkmusic'))
//      return $this->_forward('notfound', 'error', 'core');
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
//
//    $params = array();
//    $params['widgteName'] = 'Lyrics Action';
//    $params['popularity'] = $this->_getParam('popularity', 'creation_date');
//    $params['column'] = array('*');
//    $select = Engine_Api::_()->getDbtable('albumsongs', 'sesmusic')->widgetResults($params);
//    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
//    $paginator->setItemCountPerPage(20);
//    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    //Render
    $this->_helper->content->setEnabled();
  }

  public function favouriteSongsAction() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer->getIdentity();

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $authorizationApi = Engine_Api::_()->authorization();

    //Songs settings.
    $this->view->songlink = unserialize($settings->getSetting('sesmusic.songlink'));

    $this->view->canAddPlaylistAlbumSong = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'addplaylist_albumsong');

    $this->view->downloadAlbumSong = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'download_albumsong');

    $this->view->canAddFavouriteAlbumSong = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'addfavourite_albumsong');

    $allowShowRating = $settings->getSetting('sesmusic.ratealbumsong.show', 1);
    $allowRating = $settings->getSetting('sesmusic.albumsong.rating', 1);
    if ($allowRating == 0) {
      if ($allowShowRating == 0)
        $showRating = false;
      else
        $showRating = true;
    }
    else
      $showRating = true;
    $this->view->showAlbumSongRating = $showRating;

    if (!$settings->getSetting('sesmusic.checkmusic'))
      return $this->_forward('notfound', 'error', 'core');

    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('favourites', 'sesmusic')->getFavourites(array('resource_type' => 'sesmusic_albumsong'));
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    //Render
    $this->_helper->content->setEnabled();
  }

  public function likeSongsAction() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer->getIdentity();

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $authorizationApi = Engine_Api::_()->authorization();

    //Songs settings.
    $this->view->songlink = unserialize($settings->getSetting('sesmusic.songlink'));

    $this->view->canAddPlaylistAlbumSong = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'addplaylist_albumsong');

    $this->view->downloadAlbumSong = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'download_albumsong');
    $this->view->canAddFavouriteAlbumSong = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'addfavourite_albumsong');

    $allowShowRating = $settings->getSetting('sesmusic.ratealbumsong.show', 1);
    $allowRating = $settings->getSetting('sesmusic.albumsong.rating', 1);
    if ($allowRating == 0) {
      if ($allowShowRating == 0)
        $showRating = false;
      else
        $showRating = true;
    }
    else
      $showRating = true;
    $this->view->showAlbumSongRating = $showRating;

    if (!$settings->getSetting('sesmusic.checkmusic'))
      return $this->_forward('notfound', 'error', 'core');

    $this->view->paginator = $paginator = Engine_Api::_()->sesmusic()->getLikesContents(array('resource_type' => 'sesmusic_albumsong'));
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    //Render
    $this->_helper->content->setEnabled();
  }

  public function ratedSongsAction() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer->getIdentity();

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $authorizationApi = Engine_Api::_()->authorization();

    //Songs settings.
    $this->view->songlink = unserialize($settings->getSetting('sesmusic.songlink'));

    $this->view->canAddPlaylistAlbumSong = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'addplaylist_albumsong');

    $this->view->downloadAlbumSong = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'download_albumsong');
    $this->view->canAddFavouriteAlbumSong = $authorizationApi->isAllowed('sesmusic_album', $viewer, 'addfavourite_albumsong');

    $allowShowRating = $settings->getSetting('sesmusic.ratealbumsong.show', 1);
    $allowRating = $settings->getSetting('sesmusic.albumsong.rating', 1);
    if ($allowRating == 0) {
      if ($allowShowRating == 0)
        $showRating = false;
      else
        $showRating = true;
    }
    else
      $showRating = true;
    $this->view->showAlbumSongRating = $showRating;

    if (!$settings->getSetting('sesmusic.checkmusic'))
      return $this->_forward('notfound', 'error', 'core');

    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('ratings', 'sesmusic')->getRatedItems(array('resource_type' => 'sesmusic_albumsong'));
    $paginator->setItemCountPerPage(20);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    //Render
    $this->_helper->content->setEnabled();
  }

  //View Action
  public function viewAction() {

    //Render
    $this->_helper->content->setEnabled();

    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer->getIdentity();

    //Check subject
    if (!Engine_Api::_()->core()->hasSubject('sesmusic_albumsong')) {
      $this->view->success = false;
      $this->view->error = $this->view->translate('Not a valid song');
      return;
    }

    //Check subject
    if (!$this->_helper->requireSubject()->isValid())
      return;

    //Get song
    $this->view->albumsong = $albumsong = Engine_Api::_()->core()->getSubject('sesmusic_albumsong');
    $this->view->album = $album = $albumsong->getParent();


    /* Insert data for recently viewed widget */
    if ($viewer->getIdentity() != 0 && isset($album->album_id)) {
      $dbObject = Engine_Db_Table::getDefaultAdapter();
      $dbObject->query('INSERT INTO engine4_sesmusic_recentlyviewitems (resource_id, resource_type,owner_id,creation_date ) VALUES ("' . $albumsong->albumsong_id . '", "sesmusic_albumsong","' . $viewer->getIdentity() . '",NOW())	ON DUPLICATE KEY UPDATE	creation_date = NOW()');
    }

//    if (!$albumsong->description && !$albumsong->lyrics && !$albumsong->artists) {
//      return;
//    }

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $ratingTable = Engine_Api::_()->getDbTable('ratings', 'sesmusic');

    //Songs settings.
    $this->view->songlink = unserialize($settings->getSetting('sesmusic.songlink'));

    if (!$settings->getSetting('sesmusic.checkmusic'))
      return $this->_forward('notfound', 'error', 'core');

    //Favourite work
    $this->view->isFavourite = Engine_Api::_()->getDbTable('favourites', 'sesmusic')->isFavourite(array('resource_type' => "sesmusic_albumsong", 'resource_id' => $albumsong->getIdentity()));

    //Artist Work
    if ($albumsong->artists) {
      $artists = json_decode($albumsong->artists);
      if ($artists)
        $this->view->artists_array = Engine_Api::_()->getDbTable('artists', 'sesmusic')->getArtists($artists);
    }

    //Rating Work
    $this->view->rating_count = $ratingTable->ratingCount($albumsong->getIdentity(), 'sesmusic_albumsong');
    $this->view->rated = $ratingTable->checkRated($albumsong->getIdentity(), $viewer->getIdentity(), 'sesmusic_albumsong');

    //Increment view count of song
    if (!$viewer->isSelf($album->getOwner())) {
      $albumsong->view_count++;
      $albumsong->save();
    }

    //Check song/playlist
    if (!$albumsong || !$album) {
      $this->view->success = false;
      $this->view->error = $this->view->translate('Invalid playlist');
      return;
    }

    //Check auth
    if (!Engine_Api::_()->authorization()->isAllowed($album, null, 'edit')) {
      $this->view->success = false;
      $this->view->error = $this->view->translate('Not allowed to edit this playlist');
      return;
    }

    //Get file
    $file = Engine_Api::_()->getItem('storage_file', $albumsong->file_id);
    if (!$file) {
      $this->view->success = false;
      $this->view->error = $this->view->translate('Invalid playlist');
      return;
    }
  }

  //Edit Action
  public function editAction() {

    $albumsong_id = $this->_getParam('albumsong_id');
    $this->view->albumsong = $albumsong = Engine_Api::_()->getItem('sesmusic_albumsong', $albumsong_id);

    //Only members can upload music
    if (!$this->_helper->requireUser()->isValid())
      return;

    if (!$this->_helper->requireSubject('sesmusic_albumsong')->isValid())
      return;

    //Make form
    $this->view->form = $form = new Sesmusic_Form_SongEdit();

    if ($albumsong->subcat_id)
      $form->subcat_id->setValue($albumsong->subcat_id);

    if ($albumsong->subsubcat_id)
      $form->subsubcat_id->setValue($albumsong->subsubcat_id);

    $form->populate($albumsong->toarray());

    if ($albumsong->artists) {
      $artists_array = json_decode($albumsong->artists);
      if (count($artists_array) > 0)
        $form->artists->setValue(json_decode($albumsong->artists));
    }

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.checkmusic'))
      return $this->_forward('notfound', 'error', 'core');

    if (!$this->getRequest()->isPost())
      return;

    if (!$form->isValid($this->getRequest()->getPost()))
      return;

    $db = Engine_Api::_()->getDbTable('albumsongs', 'sesmusic')->getAdapter();
    $db->beginTransaction();
    try {
      $values = $form->getValues();
      if (!$values['song_cover'])
        unset($values['song_cover']);

      if (isset($values['artists']))
        $values['artists'] = json_encode($values['artists']);
      else
        $values['artists'] = json_encode(array());

      $albumsong->setFromArray($values);

      //Update title in playlistsong table
      Engine_Api::_()->getDbtable('playlistsongs', 'sesmusic')->update(array('title' => $values['title']), array('albumsong_id = ?' => $albumsong_id));
      $albumsong->save();

      //Photo upload for song
      if (!empty($values['file'])) {
        $previousPhoto = $albumsong->photo_id;
        if ($previousPhoto) {
          $songPhoto = Engine_Api::_()->getItem('storage_file', $previousPhoto);
          $songPhoto->delete();
        }
        $albumsong->setPhoto($form->file, 'mainPhoto');
      }

      if (isset($values['remove_photo']) && !empty($values['remove_photo'])) {
        $storage = Engine_Api::_()->getItem('storage_file', $albumsong->photo_id);
        $albumsong->photo_id = 0;
        $albumsong->save();
        if ($storage)
          $storage->delete();
      }

      //Photo upload for song cover
      if (!empty($values['song_cover'])) {
        $previousPhoto = $albumsong->song_cover;
        if ($previousPhoto) {
          $songPhoto = Engine_Api::_()->getItem('storage_file', $previousPhoto);
          if ($songPhoto)
            $songPhoto->delete();
        }
        $albumsong->setPhoto($form->song_cover, 'songCover');
      }

      if (isset($values['remove_song_cover']) && !empty($values['remove_song_cover'])) {
        $storage = Engine_Api::_()->getItem('storage_file', $albumsong->song_cover);
        $albumsong->song_cover = 0;
        $albumsong->save();
        if ($storage)
          $storage->delete();
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollback();
      throw $e;
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'view', 'albumsong_id' => $albumsong_id, 'slug' => $albumsong->getSlug()), 'sesmusic_albumsong_view', true);
  }

  //Delete Action
  public function deleteAction() {

    //Check subject
    if (!Engine_Api::_()->core()->hasSubject('sesmusic_albumsong')) {
      $this->view->success = false;
      $this->view->error = $this->view->translate('Not a valid song');
      return;
    }

    $albumsong = Engine_Api::_()->getItem('sesmusic_albumsong', $this->getRequest()->getParam('albumsong_id'));
    $album = Engine_Api::_()->getItem('sesmusic_album', $albumsong->album_id);

    if (!$this->_helper->requireAuth()->setAuthParams('sesmusic_album', null, 'delete')->isValid())
      return;

    //In smoothbox
    $this->_helper->layout->setLayout('default-simple');

    //Get From
    $this->view->form = $form = new Sesmusic_Form_Delete();
    $form->setTitle('Delete Song?');
    $form->setDescription('Are you sure you want to delete this song?');
    $form->submit->setLabel('Delete Song');

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.checkmusic'))
      return $this->_forward('notfound', 'error', 'core');

    if (!$albumsong) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Song doesn't exists or not authorized to delete");
      return;
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $db = $albumsong->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      //Delete ratings
      Engine_Api::_()->getDbtable('ratings', 'sesmusic')->delete(array('resource_id =?' => $this->_getParam('albumsong_id'), 'resource_type =?' => 'sesmusic_albumsong'));

      //Delete accociate playlistsong
      Engine_Api::_()->getDbtable('playlistsongs', 'sesmusic')->delete(array('albumsong_id =?' => $this->_getParam('albumsong_id')));

      $file = Engine_Api::_()->getItem('storage_file', $albumsong->file_id);
      if ($file)
        $file->remove();

      //Delete album song
      $albumsong->delete();
      $album->song_count--;
      $album->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('The selected song has been deleted.');
    return $this->_forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'sesmusic_general', true), 'messages' => Array($this->view->message)));
  }

  //Create New playlist Action
  public function appendSongsAction() {

    //Check auth
    if (!$this->_helper->requireUser()->isValid())
      return;

    if (!$this->_helper->requireAuth()->setAuthParams('sesmusic_album', null, 'addplaylist_album')->isValid())
      return;

    $album_id = $this->_getParam('album_id');
    $album = Engine_Api::_()->getItem('sesmusic_album', $album_id);
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //Get form
    $this->view->form = $form = new Sesmusic_Form_AppendSongs();

    $this->view->quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sesmusic_album', 'addplaylist_max');

    //Populate form
    $playlistTable = Engine_Api::_()->getDbtable('playlists', 'sesmusic');

    $this->view->current_count = $playlists = $playlistTable->getPlaylistsCount(array('viewer_id' => $viewer->getIdentity(), 'column_name' => array('playlist_id', 'title')));
    foreach ($playlists as $playlist) {
      if ($playlist['playlist_id'] != $album_id) {
        $form->playlist_id->addMultiOption($playlist['playlist_id'], html_entity_decode($playlist['title']));
      }
    }

    //Check method/data
    if (!$this->getRequest()->isPost())
      return;

    if (!$form->isValid($this->getRequest()->getPost()))
      return;

    //Get values
    $values = $form->getValues();

    if (empty($values['playlist_id']) && empty($values['title']))
      return $form->addError('Please enter a title or select a playlist.');

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.checkmusic'))
      return $this->_forward('notfound', 'error', 'core');

    //Process
    $playlistSongsTable = Engine_Api::_()->getDbtable('playlistsongs', 'sesmusic');
    $db = $playlistSongsTable->getAdapter();
    $db->beginTransaction();
    try {
      //Existing playlist
      if (!empty($values['playlist_id'])) {
        $playlist = Engine_Api::_()->getItem('sesmusic_playlist', $values['playlist_id']);
        $songs = $album->getSongs();
        $count = 0;
        foreach ($songs as $song) {

          //Already exists in playlist          
          $alreadyExists = Engine_Api::_()->getDbtable('playlistsongs', 'sesmusic')->checkSongsAlready(array('column_name' => 'albumsong_id', 'playlist_id' => $playlist->getIdentity(), 'file_id' => $song->file_id, 'albumsong_id' => $song->albumsong_id));
          if ($alreadyExists)
            $count++;
        }

        if (count($songs) == $count) {
          return$form->addError($this->view->translate("This playlist already has this song. So, you can go to album view page and add songs in playlist."));
        }
      }
      //New playlist
      else {
        $playlist = $playlistTable->createRow();
        $playlist->title = trim($values['title']);
        $playlist->description = $values['description'];
        $playlist->owner_type = 'user';
        $playlist->owner_id = $viewer->getIdentity();
        $playlist->save();
      }

      //Add all songs in the playlists
      $songs = $album->getSongs();
      foreach ($songs as $song) {
        //Add song
        $playlist->addSong($song->file_id, $song->albumsong_id);
        $playlist->song_count++;
        $playlist->save();
      }

      $playlistID = $playlist->getIdentity();

      //Activity Feed work
      $activityTable = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $activityTable->addActivity($viewer, $album, "sesmusic_addalbumplaylist", '', array('playlist' => array($playlist->getType(), $playlist->getIdentity()),
      ));

      if ($action) {
        Engine_Api::_()->getDbtable('stream', 'activity')->delete(array('action_id =?' => $action->action_id));
        $db->query("INSERT INTO `engine4_activity_stream` (`target_type`, `target_id`, `subject_type`, `subject_id`, `object_type`, `object_id`, `type`, `action_id`) VALUES
('everyone', 0, 'user', $viewer_id, 'sesmusic_playlist', $playlistID, 'sesmusic_addalbumplaylist', $action->action_id),
('members', $viewer_id, 'user', $viewer_id, 'sesmusic_playlist', $playlistID, 'sesmusic_addalbumplaylist', $action->action_id),
('owner', $viewer_id, 'user', $viewer_id, 'sesmusic_playlist', $playlistID, 'sesmusic_addalbumplaylist', $action->action_id),
('parent', $viewer_id, 'user', $viewer_id, 'sesmusic_playlist', $playlistID, 'sesmusic_addalbumplaylist', $action->action_id),
('registered', 0, 'user', $viewer_id, 'sesmusic_playlist', $playlistID, 'sesmusic_addalbumplaylist', $action->action_id);");
        $activityTable->attachActivity($action, $album);
      }

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
      $this->view->playlist = $playlist;
      $db->commit();
      //Response
      $this->view->success = true;
      $this->view->message = $this->view->translate('Song has been successfully added to your playlist.');
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 300,
                  // 'parentRefresh' => 300,
                  'messages' => array('Song has been successfully added to your playlist.')
      ));
    } catch (Sesmusic_Model_Exception $e) {
      $this->view->success = false;
      $this->view->error = $this->view->translate($e->getMessage());
      $form->addError($e->getMessage());
      $db->rollback();
    } catch (Exception $e) {
      $this->view->success = false;
      $db->rollback();
    }
  }

  //Create New playlist Action
  public function appendAction() {

    //Check auth
    if (!$this->_helper->requireUser()->isValid())
      return;

    if (!$this->_helper->requireAuth()->setAuthParams('sesmusic_album', null, 'addplaylist_albumsong')->isValid())
      return;

    if (!$this->_helper->requireSubject('sesmusic_albumsong')->isValid())
      return;

    //Set song
    $song = Engine_Api::_()->core()->getSubject('sesmusic_albumsong');
    $albumsong_id = $song->albumsong_id;
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //Get form
    $this->view->form = $form = new Sesmusic_Form_Append();
    //$this->view->quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sesmusic_album', 'addplaylist_max');

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.checkmusic'))
      return $this->_forward('notfound', 'error', 'core');

    $alreadyExistsResults = Engine_Api::_()->getDbtable('playlistsongs', 'sesmusic')->getPlaylistSongs(array('column_name' => 'playlist_id', 'albumsong_id' => $albumsong_id));

    $allPlaylistIds = array();
    foreach ($alreadyExistsResults as $alreadyExistsResult) {
      $allPlaylistIds[] = $alreadyExistsResult['playlist_id'];
    }

    //Populate form
    $playlistTable = Engine_Api::_()->getDbtable('playlists', 'sesmusic');
    $select = $playlistTable->select()
            ->from($playlistTable, array('playlist_id', 'title'))
            ->where('owner_type = ?', 'user');

    if ($allPlaylistIds) {
      $select->where($playlistTable->info('name') . '.playlist_id NOT IN(?)', $allPlaylistIds);
    }

    $select->where('owner_id = ?', $viewer->getIdentity());
    $playlists = $playlistTable->fetchAll($select);
    //$this->view->current_count = $playlists;
    foreach ($playlists as $playlist) {
      // if ($playlist['playlist_id'] != $albumsong_id) {
      $form->playlist_id->addMultiOption($playlist['playlist_id'], html_entity_decode($playlist['title']));
      // }
    }

    //Check method/data
    if (!$this->getRequest()->isPost())
      return;

    if (!$form->isValid($this->getRequest()->getPost()))
      return;

    //Get values
    $values = $form->getValues();
    if (empty($values['playlist_id']) && empty($values['title']))
      return $form->addError('Please enter a title or select a playlist.');

    //Process
    $playlistSongsTable = Engine_Api::_()->getDbtable('playlistsongs', 'sesmusic');
    $db = $playlistSongsTable->getAdapter();
    $db->beginTransaction();
    try {
      //Existing playlist
      if (!empty($values['playlist_id'])) {

        $playlist = Engine_Api::_()->getItem('sesmusic_playlist', $values['playlist_id']);

        //Already exists in playlist
        $alreadyExists = Engine_Api::_()->getDbtable('playlistsongs', 'sesmusic')->checkSongsAlready(array('column_name' => 'albumsong_id', 'playlist_id' => $playlist->getIdentity(), 'file_id' => $song->file_id, 'albumsong_id' => $albumsong_id));

        if ($alreadyExists)
          return$form->addError($this->view->translate("This playlist already has this song."));
      }
      //New playlist
      else {
        $playlist = $playlistTable->createRow();
        $playlist->title = trim($values['title']);
        $playlist->description = $values['description'];
        $playlist->owner_type = 'user';
        $playlist->owner_id = $viewer->getIdentity();
        $playlist->save();
      }
      $playlist->song_count++;
      $playlist->save();
      //Add song
      $playlist->addSong($song->file_id, $albumsong_id);
      $playlistID = $playlist->getIdentity();

      //Activity Feed work
      $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $activityApi->addActivity($viewer, $song, "sesmusic_addplaylist", '', array('playlist' => array($playlist->getType(), $playlist->getIdentity()),
      ));
      if ($action) {
        Engine_Api::_()->getDbtable('stream', 'activity')->delete(array('action_id =?' => $action->action_id));
        $db->query("INSERT INTO `engine4_activity_stream` (`target_type`, `target_id`, `subject_type`, `subject_id`, `object_type`, `object_id`, `type`, `action_id`) VALUES
('everyone', 0, 'user', $viewer_id, 'sesmusic_playlist', $playlistID, 'sesmusic_addplaylist', $action->action_id),
('members', $viewer_id, 'user', $viewer_id, 'sesmusic_playlist', $playlistID, 'sesmusic_addplaylist', $action->action_id),
('owner', $viewer_id, 'user', $viewer_id, 'sesmusic_playlist', $playlistID, 'sesmusic_addplaylist', $action->action_id),
('parent', $viewer_id, 'user', $viewer_id, 'sesmusic_playlist', $playlistID, 'sesmusic_addplaylist', $action->action_id),
('registered', 0, 'user', $viewer_id, 'sesmusic_playlist', $playlistID, 'sesmusic_addplaylist', $action->action_id);");
        $activityApi->attachActivity($action, $song);
      }

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
      $this->view->playlist = $playlist;
      $db->commit();
      //Response
      $this->view->success = true;
      $this->view->message = $this->view->translate('Song has been successfully added to your playlist.');
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 300,
                  //'parentRefresh' => 300,
                  'messages' => array('Song has been successfully added to your playlist.')
      ));
    } catch (Sesmusic_Model_Exception $e) {
      $this->view->success = false;
      $this->view->error = $this->view->translate($e->getMessage());
      $form->addError($e->getMessage());
      $db->rollback();
    } catch (Exception $e) {
      $this->view->success = false;
      $db->rollback();
    }
  }

  //Download song action
  public function downloadSongAction() {

    $albumSong = Engine_Api::_()->getItem('sesmusic_albumsongs', $this->_getParam('albumsong_id'));

    $storage = Engine_Api::_()->getItem('storage_file', $albumSong->file_id);

    if($storage->service_id == 2) {
      $servicesTable = Engine_Api::_()->getDbtable('services', 'storage');
      $result  = $servicesTable->select()
                  ->from($servicesTable->info('name'), 'config')
                  ->where('service_id = ?', $storage->service_id)
                  ->limit(1)
                  ->query()
                  ->fetchColumn();
      $serviceResults = Zend_Json_Decoder::decode($result);
      if($serviceResults['baseUrl']) {
        $path = 'http://' . $serviceResults['baseUrl'] . '/' . $storage->storage_path;
      } else {
        $path = 'http://' . $serviceResults['bucket'] . '.s3.amazonaws.com/' . $storage->storage_path;
      }
    } else {
      //Song file name and path
      $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . $storage->storage_path;
    }

    //KILL ZEND'S OB
    while (ob_get_level() > 0) {
      ob_end_clean();
    }

    $albumSong->download_count++;
    $albumSong->save();
    
    $baseName = $storage->name . '.' . $storage->extension;

    header("Content-Disposition: attachment; filename=" . urlencode(basename($baseName)), true);
    header("Content-Transfer-Encoding: Binary", true);
    header("Content-Type: application/force-download", true);
    header("Content-Type: application/octet-stream", true);
    header("Content-Type: application/download", true);
    header("Content-Description: File Transfer", true);
    header("Content-Length: " . filesize($path), true);
    readfile("$path");
    exit();
    return;
  }

  public function tallyAction() {

    //Check subject
    if (!Engine_Api::_()->core()->hasSubject('sesmusic_albumsong')) {
      $this->view->success = false;
      $this->view->error = $this->view->translate('Not a valid song');
      return;
    }

    //Get song/playlist
    $song = Engine_Api::_()->core()->getSubject('sesmusic_albumsong');

    //Check song
    if (!$song) {
      $this->view->success = false;
      $this->view->error = $this->view->translate('invalid song_id');
      return;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    $activityTable = Engine_Api::_()->getDbtable('actions', 'activity');

    //Process
    $db = $song->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      if($viewer_id) {
      $action = $activityTable->addActivity($viewer, $song, 'sesmusic_playedsong');
      if ($action) {
        Engine_Api::_()->getDbtable('stream', 'activity')->delete(array('action_id =?' => $action->action_id));
        $db->query("INSERT INTO `engine4_activity_stream` (`target_type`, `target_id`, `subject_type`, `subject_id`, `object_type`, `object_id`, `type`, `action_id`) VALUES
('everyone', 0, 'user', $viewer_id, 'sesmusic_albumsong', $song->albumsong_id, 'sesmusic_playedsong', $action->action_id),
('members', $viewer_id, 'user', $viewer_id, 'sesmusic_albumsong', $song->albumsong_id, 'sesmusic_playedsong', $action->action_id),
('owner', $viewer_id, 'user', $viewer_id, 'sesmusic_albumsong', $song->albumsong_id, 'sesmusic_playedsong', $action->action_id),
('parent', $viewer_id, 'user', $viewer_id, 'sesmusic_albumsong', $song->albumsong_id, 'sesmusic_playedsong', $action->action_id),
('registered', 0, 'user', $viewer_id, 'sesmusic_albumsong', $song->albumsong_id, 'sesmusic_playedsong', $action->action_id);");
        $activityTable->attachActivity($action, $song);
      }
      }
      $song->play_count++;
      $song->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollback();
      $this->view->success = false;
      return;
    }

    $this->view->success = true;
    $this->view->song = $song->toArray();
    $this->view->play_count = $song->playCountLanguagefield();
  }

  public function uploadAction() {

    //Only members can upload music
    if (!$this->_helper->requireUser()->checkRequire()) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Max file size limit exceeded or session expired.');
      return;
    }

    //Check method
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid request method');
      return;
    }

    //Check file
    $values = $this->getRequest()->getPost();
    if (empty($values['Filename']) || empty($_FILES['Filedata'])) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('No file');
      return;
    }


    //Process
    $db = Engine_Api::_()->getDbtable('albums', 'sesmusic')->getAdapter();
    $db->beginTransaction();

    try {
      $song = Engine_Api::_()->getApi('core', 'sesmusic')->createSong($_FILES['Filedata']);
      $this->view->status = true;
      $this->view->song = $song;
      $this->view->albumsong_id = $song->getIdentity();
      $this->view->song_url = $song->getHref();
      $db->commit();
    } catch (Sesmusic_Model_Exception $e) {
      $db->rollback();

      $this->view->status = false;
      $this->view->message = $this->view->translate($e->getMessage());
    } catch (Exception $e) {
      $db->rollback();

      $this->view->status = false;
      $this->view->message = $this->view->translate('Upload failed by database query');

      throw $e;
    }
  }

}

