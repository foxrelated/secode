<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: PlaylistController.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
class Sesvideo_PlaylistController extends Core_Controller_Action_Standard {

  public function init() {

    //Get viewer info
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer->getIdentity();

    //Get subject
    if (null !== ($playlist_id = $this->_getParam('playlist_id')) && null !== ($playlist = Engine_Api::_()->getItem('sesvideo_playlist', $playlist_id)) && $playlist instanceof Sesvideo_Model_Playlist && !Engine_Api::_()->core()->hasSubject()) {
      Engine_Api::_()->core()->setSubject($playlist);
    }
  }

  public function browseAction() {
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

    $this->view->playlist = $playlist = Engine_Api::_()->core()->getSubject('sesvideo_playlist');
		if(!$viewer->isSelf($playlist->getOwner())){
			if($playlist->is_private){
				return $this->_forward('requireauth', 'error', 'core');
			}
		}
    //Increment view count
    if (!$viewer->isSelf($playlist->getOwner())) {
      $playlist->view_count++;
      $playlist->save();
    }
		 /* Insert data for recently viewed widget */
    if ($viewer->getIdentity() != 0 && isset($chanel->chanel_id)) {
      $dbObject = Engine_Db_Table::getDefaultAdapter();
      $dbObject->query('INSERT INTO engine4_sesvideo_recentlyviewitems (resource_id, resource_type,owner_id,creation_date ) VALUES ("' . $playlist->playlist_id . '", "sesvideo_playlist","' . $viewer->getIdentity() . '",NOW())	ON DUPLICATE KEY UPDATE	creation_date = NOW()');
    }
    //Render
    $this->_helper->content->setEnabled();
  }

  //Delete playlist songs Action
  public function deletePlaylistvideoAction() {

    //Get video/playlist
    $video = Engine_Api::_()->getItem('sesvideo_playlistvideo', $this->_getParam('playlistvideo_id'));

    $playlist = $video->getParent();

    //Check song/playlist
    if (!$video || !$playlist) {
      $this->view->success = false;
      $this->view->error = $this->view->translate('Invalid playlist');
      return;
    }

    //Get file
    $file = Engine_Api::_()->getItem('storage_file', $video->file_id);
    if (!$file) {
      $this->view->success = false;
      $this->view->error = $this->view->translate('Invalid playlist');
      return;
    }

    $db = $video->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      Engine_Api::_()->getDbtable('playlistvideos', 'sesvideo')->delete(array('playlistvideo_id =?' => $this->_getParam('playlistvideo_id')));
      $db->commit();
    } catch (Exception $e) {
      $db->rollback();
      $this->view->success = false;
      $this->view->error = $this->view->translate('Unknown database error');
      throw $e;
    }

    $this->view->success = true;
  }

  //Edit Action
  public function editAction() {
    //Only members can upload video
    if (!$this->_helper->requireUser()->isValid())
      return;

    //Get playlist
    $this->view->playlist = $playlist = Engine_Api::_()->getItem('sesvideo_playlist', $this->_getParam('playlist_id'));

    //Make form
    $this->view->form = $form = new Sesvideo_Form_EditPlaylist();

    $form->populate($playlist->toarray());

    if (!$this->getRequest()->isPost())
      return;

    if (!$form->isValid($this->getRequest()->getPost()))
      return;

    $values = $form->getValues();

    unset($values['file']);

    $db = Engine_Api::_()->getDbTable('playlists', 'sesvideo')->getAdapter();
    $db->beginTransaction();
    try {
      $playlist->title = $values['title'];
      $playlist->description = $values['description'];
			$playlist->is_private = $values['is_private'];
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
    return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sesvideo_general', true);
  }

  //Delete Playlist Action
  public function deleteAction() {

    $playlist = Engine_Api::_()->getItem('sesvideo_playlist', $this->getRequest()->getParam('playlist_id'));

    //In smoothbox
    $this->_helper->layout->setLayout('default-simple');

    $this->view->form = $form = new Sesbasic_Form_Delete();
    $form->setTitle('Delete Playlist?');
    $form->setDescription('Are you sure that you want to delete this playlist? It will not be recoverable after being deleted. ');
    $form->submit->setLabel('Delete');


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
      //Delete all playlist videos which is related to this playlist
      Engine_Api::_()->getDbtable('playlistvideos', 'sesvideo')->delete(array('playlist_id =?' => $this->_getParam('playlist_id')));
      $playlist->delete();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('The selected playlist has been deleted.');
    return $this->_forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'sesvideo_general', true), 'messages' => Array($this->view->message)));
  }

  public function addAction() {

    //Check auth
    if (!$this->_helper->requireUser()->isValid())
      return;

    if (!$this->_helper->requireAuth()->setAuthParams('video', null, 'addplaylist_video')->isValid())
      return;

    //Set song
    $video = Engine_Api::_()->getItem('video', $this->_getParam('video_id'));
    $video_id = $video->video_id;
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //Get form
    $this->view->form = $form = new Sesvideo_Form_Append();
    if ($form->playlist_id) {
      $alreadyExistsResults = Engine_Api::_()->getDbtable('playlistvideos', 'sesvideo')->getPlaylistVideos(array('column_name' => 'playlist_id', 'file_id' => $video_id));

      $allPlaylistIds = array();
      foreach ($alreadyExistsResults as $alreadyExistsResult) {
        $allPlaylistIds[] = $alreadyExistsResult['playlist_id'];
      }

      //Populate form
      $playlistTable = Engine_Api::_()->getDbtable('playlists', 'sesvideo');
      $select = $playlistTable->select()
              ->from($playlistTable, array('playlist_id', 'title'));

      if ($allPlaylistIds) {
        $select->where($playlistTable->info('name') . '.playlist_id NOT IN(?)', $allPlaylistIds);
      }

      $select->where('owner_id = ?', $viewer->getIdentity());
      $playlists = $playlistTable->fetchAll($select);
      if ($playlists)
        $playlists = $playlists->toArray();
      foreach ($playlists as $playlist)
        $form->playlist_id->addMultiOption($playlist['playlist_id'], html_entity_decode($playlist['title']));
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
    $playlistVideoTable = Engine_Api::_()->getDbtable('playlists', 'sesvideo');
    $db = $playlistVideoTable->getAdapter();
    $db->beginTransaction();
    try {
      //Existing playlist
      if (!empty($values['playlist_id'])) {

        $playlist = Engine_Api::_()->getItem('sesvideo_playlist', $values['playlist_id']);

        //Already exists in playlist
        $alreadyExists = Engine_Api::_()->getDbtable('playlistvideos', 'sesvideo')->checkVideosAlready(array('column_name' => 'playlistvideo_id', 'playlist_id' => $playlist->getIdentity(), 'file_id' => $video->file_id, 'playlistvideo_id' => $video_id));

        if ($alreadyExists)
          return$form->addError($this->view->translate("This playlist already has this video."));
      }
      //New playlist
      else {
        $playlist = $playlistVideoTable->createRow();
        $playlist->title = trim($values['title']);
        $playlist->description = $values['description'];
        $playlist->owner_id = $viewer->getIdentity();
        $playlist->save();
      }
      $playlist->video_count++;
      $playlist->save();
      //Add song
      $playlist->addVideo($video->file_id, $video_id);
      $playlistID = $playlist->getIdentity();

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

      //Activity Feed work
      $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $video, "sesvideo_playlist_create", '', array('playlist' => array($playlist->getType(), $playlist->getIdentity()),
      ));
      if ($action) {
        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $video);
      }

      $db->commit();
      //Response
      $this->view->success = true;
      $this->view->message = $this->view->translate('Video has been successfully added to your playlist.');
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 300,
                  'messages' => array('Video has been successfully added to your playlist.')
      ));
    } catch (Sesvideo_Model_Exception $e) {
      $this->view->success = false;
      $this->view->error = $this->view->translate($e->getMessage());
      $form->addError($e->getMessage());
      $db->rollback();
    } catch (Exception $e) {
      $this->view->success = false;
      $db->rollback();
    }
  }

}
