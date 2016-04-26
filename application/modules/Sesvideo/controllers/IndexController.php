<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: IndexController.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
class Sesvideo_IndexController extends Core_Controller_Action_Standard {
	protected $_leftvideo ;
	protected $_counterVideoUploaded;
  public function init() {
    // only show videos if authorized
    if (!$this->_helper->requireAuth()->setAuthParams('video', null, 'view')->isValid())
      return;
    $id = $this->_getParam('video_id', $this->_getParam('id', null));
    if ($id && intval($id)) {
      $video = Engine_Api::_()->getItem('video', $id);
      if ($video) {
        Engine_Api::_()->core()->setSubject($video);
      }
    }
  }
	public function welcomeAction(){
		// Render
    $this->_helper->content->setEnabled();	
	}
  public function locationsAction() {
    if (!$this->_helper->requireAuth()->setAuthParams('video', null, 'view')->isValid())
      return;
    //Render
    $this->_helper->content->setEnabled();
  }
  public function searchAction() {
    $text = $this->_getParam('text', null);
    $actonType = $this->_getParam('actonType', null);
    $sesvideo_commonsearch = $this->_getParam('sesvideo_commonsearch', 'video');
    if ($sesvideo_commonsearch && $actonType == 'browse') {
      $type = $sesvideo_commonsearch;
    } else {
      if (isset($_COOKIE['sesvideo_commonsearch']))
        $type = $_COOKIE['sesvideo_commonsearch'];
      else
        $type = 'video';
    }
    if ($type == 'video') {
      $table = Engine_Api::_()->getDbTable('videos', 'sesvideo');
      $tableName = $table->info('name');
      $id = 'video_id';
      $route = 'sesvideo_view';
      $label = 'title';
    } elseif ($type == 'sesvideo_chanel') {
      $table = Engine_Api::_()->getDbTable('chanels', 'sesvideo');
      $tableName = $table->info('name');
      $id = 'chanel_id';
      $route = 'sesvideo_chanel_view';
      $label = 'title';
    } elseif ($type == 'sesvideo_artist') {
      $table = Engine_Api::_()->getDbTable('artists', 'sesvideo');
      $tableName = $table->info('name');
      $id = 'artist_id';
      $route = 'sesvideo_artist';
      $label = 'name';
    } elseif ($type == 'sesvideo_playlist') {
      $table = Engine_Api::_()->getDbTable('playlists', 'sesvideo');
      $tableName = $table->info('name');
      $id = 'playlist_id';
      $route = 'sesvideo_playlist_view';
      $label = 'title';
    }
    $data = array();
    $select = $table->select()->from($tableName);
    if ($type == 'sesvideo_artist') {
      $select->where('name  LIKE ? ', '%' . $text . '%')->order('name ASC');
    } else {
      $select->where('title  LIKE ? ', '%' . $text . '%')->order('title ASC');
    }
    if ($type == 'video')
      $select->where('search = ?', 1);
    $select->limit('40');
    $results = Zend_Paginator::factory($select);
    foreach ($results as $result) {
       $url = $result->getHref();
			 $photo_icon_photo = $this->view->itemPhoto($result, 'thumb.icon');
      if ($actonType == 'browse') {
        $data[] = array(
            'id' => $result->$id,
            'label' => $result->$label,
						'photo' => $photo_icon_photo
        );
      } else {
        $data[] = array(
            'id' => $result->$id,
            'label' => $result->$label,
            'url' => $url,
						'photo' => $photo_icon_photo
        );
      }
    }
    return $this->_helper->json($data);
  }
  public function tagsAction() {
    if (!$this->_helper->requireAuth()->setAuthParams('video', null, 'view')->isValid())
      return;
    //Render
    $this->_helper->content->setEnabled();
  }
  //edit photo details from lightbox
  public function editDetailAction() {
    $status = true;
    $error = false;
    $video_id = $this->_getParam('video_id', false);
    $video = Engine_Api::_()->getItem('video', $video_id);
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid() || !$video_id || !$video) {
      $status = false;
      $error = true;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    if ($status && !$error) {
      $values['title'] = $_POST['title'];
      $values['description'] = $_POST['description'];
      $values['location'] = $_POST['location'];
      //update location data in sesbasic location table
      if ($_POST['lat'] != '' && $_POST['lng'] != '') {
        $dbGetInsert = Engine_Db_Table::getDefaultAdapter();
        $dbGetInsert->query('INSERT INTO engine4_sesbasic_locations (resource_id, lat, lng , resource_type) VALUES ("' . $_POST['video_id'] . '", "' . $_POST['lat'] . '","' . $_POST['lng'] . '","sesvideo_video")	ON DUPLICATE KEY UPDATE	lat = "' . $_POST['lat'] . '" , lng = "' . $_POST['lng'] . '"');
      }
      $db = $video->getTable()->getAdapter();
      $db->beginTransaction();
      try {
        $video->setFromArray($values);
        $video->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
    echo json_encode(array('status' => $status, 'error' => $error));die;
  }
  //location video 
  public function locationAction() {
    $this->view->type = $type = $this->_getParam('type', 'video');
    if ($type != 'video_location') {
      if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid())
        return;
    }
    $this->view->type = $this->_getParam('type', 'video');
    $this->view->video_id = $video_id = $this->_getParam('video_id');
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->video = $video = Engine_Api::_()->getItem('video', $video_id);
    if (!$video)
      return;
    $this->view->form = $form = new Sesvideo_Form_Location();
    $form->populate($video->toArray());
    if (!$this->getRequest()->isPost()) {
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
    $values = $form->getValues();
    //update location data in sesbasic location table
    if (isset($_POST['lat']) && isset($_POST['lng']) && $_POST['lat'] != '' && $_POST['lng'] != '') {
      $dbGetInsert = Engine_Db_Table::getDefaultAdapter();
      $dbGetInsert->query('INSERT INTO engine4_sesbasic_locations (resource_id, lat, lng , resource_type) VALUES ("' . $video_id . '", "' . $_POST['lat'] . '","' . $_POST['lng'] . '","sesvideo_video")	ON DUPLICATE KEY UPDATE	lat = "' . $_POST['lat'] . '" , lng = "' . $_POST['lng'] . '"');
    }
    $db = $video->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      $video->setFromArray($values);
      $video->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    return $this->_forward('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your location have been saved successfully.')),
                'layout' => 'default-simple',
                'parentRefresh' => false,
                'smoothboxClose' => true,
    ));
  }
  public function imageviewerdetailAction() {
    $this->view->video_id = $video_id = $this->getRequest()->getParam('video_id', '0');
    $this->view->user_id = $user_id = $this->getRequest()->getParam('user_id', '0');
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    // initialize extra next previous params
    $this->view->video = $video = Engine_Api::_()->core()->getSubject();
    $this->view->user = $user = Engine_Api::_()->getItem('user', $user_id);
		$privateImageURL = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesvideo.private.photo', 1);
		if (!is_file($privateImageURL))
			$privateImageURL = 'application/modules/Sesvideo/externals/images/private-video.jpg';
    if (!$video->authorization()->isAllowed($viewer, 'view')) {
      $this->view->imagePrivateURL = $privateImageURL;
    }
		$this->view->privateImageUrl = $privateImageURL;
    $this->view->canComment = $video->authorization()->isAllowed($viewer, 'comment');
    /* Insert data for recently viewed widget */
    if ($viewer->getIdentity() != 0 && isset($video->video_id)) {
      $dbObject = Engine_Db_Table::getDefaultAdapter();
      $dbObject->query('INSERT INTO engine4_sesvideo_recentlyviewitems (resource_id, resource_type,owner_id,creation_date ) VALUES ("' . $video->video_id . '", "sesvideo_video","' . $viewer->getIdentity() . '",NOW())	ON DUPLICATE KEY UPDATE	creation_date = NOW()');
    }
		$this->view->type = $type = $this->_getParam('type');
   
		$customParamsArray = array();
		if($this->_getParam('item_id',0)){
			switch($type){
				case 'sesvideo_chanel':
				$this->view->item = Engine_Api::_()->getItem('sesvideo_chanel', $this->_getParam('item_id'));
				$customParamsArray = array('type'=>'sesvideo_chanel','item_id'=>$this->_getParam('item_id'));
				break;
				case 'sesvideo_playlist':
				$this->view->item = Engine_Api::_()->getItem('sesvideo_playlist', $this->_getParam('item_id'));
				$customParamsArray = array('type'=>'sesvideo_playlist','item_id'=>$this->_getParam('item_id'));
				break;
			}
		}
		$this->view->customParamsArray = $customParamsArray;
		 // get next video URL
    $this->view->nextVideo = Engine_Api::_()->getDbTable('videos', 'sesvideo')->videoLightBox($video, '>','','',$type,$this->_getParam('item_id',''));
    // get previous video URL
    $this->view->previousVideo = Engine_Api::_()->getDbTable('videos', 'sesvideo')->videoLightBox($video, '<','','',$type,$this->_getParam('item_id',''));
    if (!$viewer || !$viewer->getIdentity() || !$video->isOwner($viewer)) {
      $video->view_count = new Zend_Db_Expr('view_count + 1');
      $video->save();
    }
    if ($video->type == 3 && $video->status == 1) {
      if (!empty($video->file_id)) {
        $storage_file = Engine_Api::_()->getItem('storage_file', $video->file_id);
        if ($storage_file) {
          $this->view->video_location = $storage_file->map();
          $this->view->video_extension = $storage_file->extension;
        }
      }
    }
    $embedded = $video->getRichContent(true);
    $this->view->videoEmbedded = $embedded;
    $this->view->canEdit = $canEdit = $video->authorization()->isAllowed($viewer, 'edit');
    $this->view->canDelete = $canDelete = $video->authorization()->isAllowed($viewer, 'delete');
    // Get tags
    $this->view->tags = $video->tags()->getTagMaps();
    //check dependent module sesprofile install or not
    if (Engine_Api::_()->getApi('core', 'sesbasic')->isModuleEnable(array('seslock'))) {
      //member level check for lock videos
      $viewer = Engine_Api::_()->user()->getViewer();
      if ($viewer->getIdentity() == 0)
        $level = Engine_Api::_()->getDbtable('levels', 'authorization')->getPublicLevel()->level_id;
      else
        $level = $viewer;
      if (!Engine_Api::_()->authorization()->getPermission($level, 'video', 'locked') && $video->is_locked) {
        $this->view->locked = true;
      } else {
        $this->view->locked = false;
      }
      $this->view->password = $video->password;
    } else
      $this->view->locked = false;
    // rating code
    $this->view->allowShowRating = $allowShowRating = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.ratevideo.show', 1);
    $this->view->allowRating = $allowRating = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.video.rating', 1);
    $this->view->getAllowRating = $allowRating;
    if ($allowRating == 0) {
      if ($allowShowRating == 0)
        $showRating = false;
      else
        $showRating = true;
    } else
      $showRating = true;
    $this->view->showRating = $showRating;
    if ($showRating != 0) {
      $this->view->canRate = $canRate = Engine_Api::_()->authorization()->isAllowed('video', $viewer, 'rating');
      $this->view->allowRateAgain = $allowRateAgain = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.ratevideo.again', 1);
      $this->view->allowRateOwn = $allowRateOwn = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.ratevideo.own', 1);
      if ($canRate == 0 || $allowRating == 0)
        $allowRating = false;
      else
        $allowRating = true;
      if ($allowRateOwn == 0 && $video->owner_id == $viewer->getIdentity())
        $allowMine = false;
      else
        $allowMine = true;
      $this->view->allowMine = $allowMine;
      $this->view->allowRating = $allowRating;
      $viewer = Engine_Api::_()->user()->getViewer();
      $this->view->viewer_id = $viewer->getIdentity();
      $this->view->rating_type = $rating_type = 'video';
      $this->view->rating_count = $rating_count = Engine_Api::_()->getDbTable('ratings', 'sesvideo')->ratingCount($video->getIdentity(), $rating_type);
      $this->view->rated = $rated = Engine_Api::_()->getDbTable('ratings', 'sesvideo')->checkRated($video->getIdentity(), $viewer->getIdentity(), $rating_type);
      $rating_sum = Engine_Api::_()->getDbTable('ratings', 'sesvideo')->getSumRating($video->getIdentity(), $rating_type);
      if ($rating_count != 0)
        $this->view->total_rating_average = $rating_sum / $rating_count;
      else
        $this->view->total_rating_average = 0;
      if (!$allowRateAgain && $rated)
        $rated = false;
      else
        $rated = true;
      $this->view->ratedAgain = $rated;
      // end rating code
    }
    $getmodule = Engine_Api::_()->getDbTable('modules', 'core')->getModule('core');
    if (!empty($getmodule->version) && version_compare($getmodule->version, '4.8.6') < 0)
      $this->view->toArray = true;
    else
      $this->view->toArray = false;
    $viewer = Engine_Api::_()->user()->getViewer();
    if ($viewer->getIdentity() == 0)
      $level = Engine_Api::_()->getDbtable('levels', 'authorization')->getPublicLevel()->level_id;
    else
      $level = $viewer;
    $type = Engine_Api::_()->authorization()->getPermission($level, 'video', 'imageviewer');
    if ($type == 0)
      $this->renderScript('video/image-viewer-detail-basic.tpl');
    else
      $this->renderScript('video/image-viewer-detail-advance.tpl');
  }
  //get search video
  public function getVideoAction() {
    $sesdata = array();
    $value['text'] = $this->_getParam('text', '');
    $value['search'] = 1;
    $videos = Engine_Api::_()->getDbtable('videos', 'sesvideo')->getVideo($value);
    foreach ($videos as $video) {
      $video_icon = $this->view->itemPhoto($video, 'thumb.icon');
      $sesdata[] = array(
          'id' => $video->video_id,
          'video_id' => $video->video_id,
          'label' => $video->title,
          'photo' => $video_icon
      );
    }
    return $this->_helper->json($sesdata);
  }
  //item liked as per item tye given
  function likeAction() {
    if (Engine_Api::_()->user()->getViewer()->getIdentity() == 0) {
      echo json_encode(array('status' => 'false', 'error' => 'Login'));
      die;
    }
    if ($this->_getParam('type') == 'sesvideo_chanel') {
      $type = 'sesvideo_chanel';
      $dbTable = 'chanels';
      $resorces_id = 'chanel_id';
      $notificationType = 'liked';
    } else if ($this->_getParam('type') == 'sesvideo_playlist') {
      $type = 'sesvideo_playlist';
      $dbTable = 'playlists';
      $resorces_id = 'playlist_id';
      $notificationType = 'liked';
    } else {
      $type = 'video';
      $dbTable = 'videos';
      $resorces_id = 'video_id';
      $notificationType = 'liked';
    }
    $item_id = $this->_getParam('id');
    if (intval($item_id) == 0) {
      echo json_encode(array('status' => 'false', 'error' => 'Invalid argument supplied.'));
      die;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $tableLike = Engine_Api::_()->getDbtable('likes', 'core');
    $tableMainLike = $tableLike->info('name');
    $itemTable = Engine_Api::_()->getDbtable($dbTable, 'sesvideo');
    $select = $tableLike->select()->from($tableMainLike)->where('resource_type =?', $type)->where('poster_id =?', Engine_Api::_()->user()->getViewer()->getIdentity())->where('poster_type =?', 'user')->where('resource_id =?', $item_id);
    $Like = $tableLike->fetchRow($select);
    if (count($Like) > 0) {
      //delete		
      $db = $Like->getTable()->getAdapter();
      $db->beginTransaction();
      try {
        $Like->delete();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $itemTable->update(array(
          'like_count' => new Zend_Db_Expr('like_count - 1'),
              ), array(
          $resorces_id . ' = ?' => $item_id,
      ));
      $item = Engine_Api::_()->getItem($type, $item_id);
      Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('type =?' => $notificationType, "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $item->getType(), "object_id = ?" => $item->getIdentity()));
      Engine_Api::_()->getDbtable('actions', 'activity')->delete(array('type =?' => $notificationType, "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $item->getType(), "object_id = ?" => $item->getIdentity()));
      Engine_Api::_()->getDbtable('actions', 'activity')->detachFromActivity($item);
      echo json_encode(array('status' => 'true', 'error' => '', 'condition' => 'reduced', 'count' => $item->like_count));
      die;
    } else {
      //update
      $db = Engine_Api::_()->getDbTable('likes', 'core')->getAdapter();
      $db->beginTransaction();
      try {
        $like = $tableLike->createRow();
        $like->poster_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $like->resource_type = $type;
        $like->resource_id = $item_id;
        $like->poster_type = 'user';
        $like->save();
        $itemTable->update(array(
            'like_count' => new Zend_Db_Expr('like_count + 1'),
                ), array(
            $resorces_id . '= ?' => $item_id,
        ));
        // Commit
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      //send notification and activity feed work.
      $item = Engine_Api::_()->getItem($type, $item_id);
      $subject = $item;
      $owner = $subject->getOwner();
      if ($owner->getType() == 'user' && $owner->getIdentity() != $viewer->getIdentity()) {
        $activityTable = Engine_Api::_()->getDbtable('actions', 'activity');
        Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('type =?' => $notificationType, "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $subject->getType(), "object_id = ?" => $subject->getIdentity()));
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $subject, $notificationType);
        $result = $activityTable->fetchRow(array('type =?' => $notificationType, "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $subject->getType(), "object_id = ?" => $subject->getIdentity()));
        if (!$result) {
          $action = $activityTable->addActivity($viewer, $subject, $notificationType);
          if ($action)
            $activityTable->attachActivity($action, $subject);
        }
      }
      echo json_encode(array('status' => 'true', 'error' => '', 'condition' => 'increment', 'count' => $item->like_count));
      die;
    }
  }
  //item favourite as per item tye given
  function favouriteAction() {
    if (Engine_Api::_()->user()->getViewer()->getIdentity() == 0) {
      echo json_encode(array('status' => 'false', 'error' => 'Login')); die;
    }
    if ($this->_getParam('type') == 'sesvideo_chanel') {
      $type = 'sesvideo_chanel';
      $dbTable = 'chanels';
      $resorces_id = 'chanel_id';
      $notificationType = 'sesvideo_favourite_chanel';
    } elseif ($this->_getParam('type') == 'sesvideo_playlist') {
      $type = 'sesvideo_playlist';
      $dbTable = 'playlists';
      $resorces_id = 'playlist_id';
      $notificationType = 'sesvideo_favourite_playlist';
    } elseif ($this->_getParam('type') == 'sesvideo_artist') {
      $type = 'sesvideo_artist';
      $dbTable = 'artists';
      $resorces_id = 'artist_id';
      $notificationType = 'sesvideo_favourite_artist';
    } else {
      $type = 'sesvideo_video';
      $dbTable = 'videos';
      $resorces_id = 'video_id';
      $notificationType = 'sesvideo_favourite_video';
    }
    $item_id = $this->_getParam('id');
    if (intval($item_id) == 0) {
      echo json_encode(array('status' => 'false', 'error' => 'Invalid argument supplied.'));die;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $Fav = Engine_Api::_()->getDbTable('favourites', 'sesvideo')->getItemfav($type, $item_id);
    $favItem = Engine_Api::_()->getDbtable($dbTable, 'sesvideo');
    if (count($Fav) > 0) {
      //delete		
      $db = $Fav->getTable()->getAdapter();
      $db->beginTransaction();
      try {
        $Fav->delete();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $favItem->update(array('favourite_count' => new Zend_Db_Expr('favourite_count - 1')), array($resorces_id . ' = ?' => $item_id));
      $item = Engine_Api::_()->getItem($type, $item_id);
      Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('type =?' => $notificationType, "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $item->getType(), "object_id = ?" => $item->getIdentity()));
      Engine_Api::_()->getDbtable('actions', 'activity')->delete(array('type =?' => $notificationType, "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $item->getType(), "object_id = ?" => $item->getIdentity()));
      Engine_Api::_()->getDbtable('actions', 'activity')->detachFromActivity($item);
      echo json_encode(array('status' => 'true', 'error' => '', 'condition' => 'reduced', 'count' => $item->favourite_count));
      $this->view->favourite_id = 0;
      die;
    } else {
      //update
      $db = Engine_Api::_()->getDbTable('favourites', 'sesvideo')->getAdapter();
      $db->beginTransaction();
      try {
        $fav = Engine_Api::_()->getDbTable('favourites', 'sesvideo')->createRow();
        $fav->user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $fav->resource_type = $type;
        $fav->resource_id = $item_id;
        $fav->save();
        $favItem->update(array('favourite_count' => new Zend_Db_Expr('favourite_count + 1'),
                ), array(
            $resorces_id . '= ?' => $item_id,
        ));
        // Commit
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      //send notification and activity feed work.
      $item = Engine_Api::_()->getItem(@$type, @$item_id);
      if ($this->_getParam('type') != 'sesvideo_artist') {
        $subject = $item;
        $owner = $subject->getOwner();
        if ($owner->getType() == 'user' && $owner->getIdentity() != $viewer->getIdentity()) {
          $activityTable = Engine_Api::_()->getDbtable('actions', 'activity');
          Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('type =?' => $notificationType, "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $subject->getType(), "object_id = ?" => $subject->getIdentity()));
          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $subject, $notificationType);
          $result = $activityTable->fetchRow(array('type =?' => $notificationType, "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $subject->getType(), "object_id = ?" => $subject->getIdentity()));
          if (!$result) {
            $action = $activityTable->addActivity($viewer, $subject, $notificationType);
            if ($action)
              $activityTable->attachActivity($action, $subject);
          }
        }
      }
      $this->view->favourite_id = 1;
      echo json_encode(array('status' => 'true', 'error' => '', 'condition' => 'increment', 'count' => $item->favourite_count, 'favourite_id' => 1));
      die;
    }
  }
  //get all photo as per view type in light box(advance)
  public function allVideosAction() {
    $this->view->video_id = $video_id = $this->getRequest()->getParam('video_id', '0');
    $viewer = Engine_Api::_()->user()->getViewer();
    $page = isset($_POST['page']) ? $_POST['page'] : 1;
    $is_ajax = isset($_POST['is_ajax']) ? $_POST['is_ajax'] : 0;
    $params['paginator'] = true;
		$customParamsArray = array();
    $video = Engine_Api::_()->core()->getSubject();
    //FETCH photos
			$type = $this->_getParam('type');
			if($this->_getParam('item_id',0)){
			switch($type){
				case 'sesvideo_chanel':
				$this->view->item = Engine_Api::_()->getItem('sesvideo_chanel', $this->_getParam('item_id'));
				$customParamsArray = array('type'=>'sesvideo_chanel','item_id'=>$this->_getParam('item_id'));
				break;
				case 'sesvideo_playlist':
				$this->view->item = Engine_Api::_()->getItem('sesvideo_playlist', $this->_getParam('item_id'));
				$customParamsArray = array('type'=>'sesvideo_playlist','item_id'=>$this->_getParam('item_id'));
				break;
			}
		}
		$this->view->customParamsArray = $customParamsArray;
    $paginator = $this->view->allVideos = Engine_Api::_()->getDbTable('videos', 'sesvideo')->videoLightBox($video, '', true, true,$type,$this->_getParam('item_id',''));
    $paginator->setItemCountPerPage(30);
    $this->view->limit = ($page - 1) * 30;
    $this->view->page = $page;
    $this->view->is_ajax = $is_ajax;
    $paginator->setCurrentPageNumber($page);
    $this->renderScript('video/all-videos.tpl');
  }
  public function homeAction() {
    //Render
    $this->_helper->content->setEnabled();
  }
  //get images as per album id (advance lightbox)
  public function correspondingImageAction() {
    $chanel_id = $this->_getParam('chanel_id', false);
    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('chanelphotos', 'sesvideo')->getPhotoSelect(array('chanel_id' => $chanel_id, 'limit_data' => 100));
  }
  public function viewAction() {
    $video = Engine_Api::_()->core()->getSubject('video');
    if (!$this->_helper->requireSubject()->isValid())
      return;
    $message_id = $this->getRequest()->getParam('message');
    $message_view = false;
    if ($message_id) {
      $conversation = Engine_Api::_()->getItem('messages_conversation', $message_id);
      if ($conversation->hasRecipient(Engine_Api::_()->user()->getViewer())) {
        $message_view = true;
      }
    }
    $this->view->message_view = $message_view;
    if (!$message_view &&
            !$this->_helper->requireAuth()->setAuthParams($video, null, 'view')->isValid()) {
      return;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    /* Insert data for recently viewed widget */
    if ($viewer->getIdentity() != 0 && isset($video->video_id)) {
      $dbObject = Engine_Db_Table::getDefaultAdapter();
      $dbObject->query('INSERT INTO engine4_sesvideo_recentlyviewitems (resource_id, resource_type,owner_id,creation_date ) VALUES ("' . $video->video_id . '", "sesvideo_video","' . $viewer->getIdentity() . '",NOW())	ON DUPLICATE KEY UPDATE	creation_date = NOW()');
    }
    // Render
    $this->_helper->content->setEnabled();
  }
  public function browsePinboardAction() {
    // Render
    $this->_helper->content->setEnabled();
  }
  public function browseAction() {
    // Render
    $this->_helper->content->setEnabled();
  }
  public function rateAction() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $user_id = $viewer->getIdentity();
    $rating = $this->_getParam('rating');
    $resource_id = $this->_getParam('resource_id');
    $resource_type = $this->_getParam('resource_type');
    $table = Engine_Api::_()->getDbtable('ratings', 'sesvideo');
    $db = $table->getAdapter();
    $db->beginTransaction();
    try {
      Engine_Api::_()->getDbtable('ratings', 'sesvideo')->setRating($resource_id, $user_id, $rating, $resource_type);
      if ($resource_type && $resource_type == 'video')
        $item = Engine_Api::_()->getItem('sesvideo_video', $resource_id);
      else if ($resource_type && $resource_type == 'sesvideo_artists')
        $item = Engine_Api::_()->getItem('sesvideo_artists', $resource_id);
			else if($resource_type && $resource_type == 'sesvideo_chanel')
				$item = Engine_Api::_()->getItem('sesvideo_chanel', $resource_id);
      $item->rating = Engine_Api::_()->getDbtable('ratings', 'sesvideo')->getRating($item->getIdentity(), $resource_type);
      $item->save();
      if ($resource_type == 'video') {
        $type = 'sesvideo_video_rating';
      } elseif ($resource_type == 'sesvideo_chanel') {
        $type = 'sesvideo_chanel_rating';
      } elseif ($resource_type == 'sesvideo_artists') {
        $type = 'sesvideo_artist_rating';
      }
      //Activity Feed / Notification
      if ($resource_type != 'sesvideo_artists') {
        $owner = $item->getOwner();
        if ($viewer->getIdentity() != $item->owner_id) {
          Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('type =?' => $type, "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $item->getType(), "object_id = ?" => $item->getIdentity()));

          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $item, $type);
        }
      }
      $result = Engine_Api::_()->getDbtable('actions', 'activity')->fetchRow(array('type =?' => $type, "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $item->getType(), "object_id = ?" => $item->getIdentity()));
      if (!$result) {
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $item, $type);
        if ($action)
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $item);
      }
//      $SUBJECT = $viewer->getTitle() . ' ' . $manageActions->verb . ' you.';
//      $BODY = $viewer->getTitle() . ' ' . $manageActions->verb . ' you.';
//      Engine_Api::_()->getApi('mail', 'core')->sendSystem($owner->email, 'SESVIDEO_FAV_RATE_FOLLOW', array(
//          'subject' => $SUBJECT,
//          'body' => $BODY,
//          'queue' => true
//      ));

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $total = Engine_Api::_()->getDbtable('ratings', 'sesvideo')->ratingCount($item->getIdentity(), $resource_type);
    $rating_sum = Engine_Api::_()->getDbtable('ratings', 'sesvideo')->getSumRating($item->getIdentity(), $resource_type);
    $data = array();
    $totalTxt = $this->view->translate(array('%s rating', '%s ratings', $total), $total);
    $data[] = array(
        'total' => $total,
        'rating' => $rating,
        'totalTxt' => str_replace($total, '', $totalTxt),
        'rating_sum' => $rating_sum
    );
    return $this->_helper->json($data);
  }
  public function shareAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;
    $type = $this->_getParam('type');
    $id = $this->_getParam('id');
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->attachment = $attachment = Engine_Api::_()->getItem($type, $id);
    if (empty($_POST['is_ajax']))
      $this->view->form = $form = new Activity_Form_Share();
    if (!$attachment) {
      // tell smoothbox to close
      $this->view->status = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('You cannot share this item because it has been removed.');
      $this->view->smoothboxClose = true;
      return $this->render('deletedItem');
    }
    // hide facebook and twitter option if not logged in
    $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
    if (!$facebookTable->isConnected() && empty($_POST['is_ajax'])) {
      $form->removeElement('post_to_facebook');
    }
    $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
    if (!$twitterTable->isConnected() && empty($_POST['is_ajax'])) {
      $form->removeElement('post_to_twitter');
    }
    if (empty($_POST['is_ajax']) && !$this->getRequest()->isPost()) {
      return;
    }
    if (empty($_POST['is_ajax']) && !$form->isValid($this->getRequest()->getPost())) {
      return;
    }
    // Process
    $db = Engine_Api::_()->getDbtable('actions', 'activity')->getAdapter();
    $db->beginTransaction();
    try {
      // Get body
      if (empty($_POST['is_ajax']))
        $body = $form->getValue('body');
      else
        $body = '';
      // Set Params for Attachment
      $params = array(
          'type' => '<a href="' . $attachment->getHref() . '">' . $attachment->getMediaType() . '</a>',
      );
      // Add activity
      $api = Engine_Api::_()->getDbtable('actions', 'activity');
      //$action = $api->addActivity($viewer, $viewer, 'post_self', $body);
      $action = $api->addActivity($viewer, $attachment->getOwner(), 'share', $body, $params);
      if ($action) {
        $api->attachActivity($action, $attachment);
      }
      $db->commit();
      // Notifications
      $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
      // Add notification for owner of activity (if user and not viewer)
      if ($action->subject_type == 'user' && $attachment->getOwner()->getIdentity() != $viewer->getIdentity()) {
        $notifyApi->addNotification($attachment->getOwner(), $viewer, $action, 'shared', array(
            'label' => $attachment->getMediaType(),
        ));
      }
      // Preprocess attachment parameters
      if (empty($_POST['is_ajax']))
        $publishMessage = html_entity_decode($form->getValue('body'));
      else
        $publishMessage = '';
      $publishUrl = null;
      $publishName = null;
      $publishDesc = null;
      $publishPicUrl = null;
      // Add attachment
      if ($attachment) {
        $publishUrl = $attachment->getHref();
        $publishName = $attachment->getTitle();
        $publishDesc = $attachment->getDescription();
        if (empty($publishName)) {
          $publishName = ucwords($attachment->getShortType());
        }
        if (($tmpPicUrl = $attachment->getPhotoUrl())) {
          $publishPicUrl = $tmpPicUrl;
        }
        // prevents OAuthException: (#100) FBCDN image is not allowed in stream
        if ($publishPicUrl &&
                preg_match('/fbcdn.net$/i', parse_url($publishPicUrl, PHP_URL_HOST))) {
          $publishPicUrl = null;
        }
      } else {
        $publishUrl = $action->getHref();
      }
      // Check to ensure proto/host
      if ($publishUrl &&
              false === stripos($publishUrl, 'http://') &&
              false === stripos($publishUrl, 'https://')) {
        $publishUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishUrl;
      }
      if ($publishPicUrl &&
              false === stripos($publishPicUrl, 'http://') &&
              false === stripos($publishPicUrl, 'https://')) {
        $publishPicUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishPicUrl;
      }
      // Add site title
      if ($publishName) {
        $publishName = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title
                . ": " . $publishName;
      } else {
        $publishName = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title;
      }
      // Publish to facebook, if checked & enabled
      if ($this->_getParam('post_to_facebook', false) &&
              'publish' == Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable) {
        try {
          $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
          $facebookApi = $facebook = $facebookTable->getApi();
          $fb_uid = $facebookTable->find($viewer->getIdentity())->current();
          if ($fb_uid &&
                  $fb_uid->facebook_uid &&
                  $facebookApi &&
                  $facebookApi->getUser() &&
                  $facebookApi->getUser() == $fb_uid->facebook_uid) {
            $fb_data = array(
                'message' => $publishMessage,
            );
            if ($publishUrl) {
              $fb_data['link'] = $publishUrl;
            }
            if ($publishName) {
              $fb_data['name'] = $publishName;
            }
            if ($publishDesc) {
              $fb_data['description'] = $publishDesc;
            }
            if ($publishPicUrl) {
              $fb_data['picture'] = $publishPicUrl;
            }
            $res = $facebookApi->api('/me/feed', 'POST', $fb_data);
          }
        } catch (Exception $e) {
          // Silence
        }
      } // end Facebook
      // Publish to twitter, if checked & enabled
      if ($this->_getParam('post_to_twitter', false) &&
              'publish' == Engine_Api::_()->getApi('settings', 'core')->core_twitter_enable) {
        try {
          $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
          if ($twitterTable->isConnected()) {
            // Get attachment info
            $title = $attachment->getTitle();
            $url = $attachment->getHref();
            $picUrl = $attachment->getPhotoUrl();
            // Check stuff
            if ($url && false === stripos($url, 'http://')) {
              $url = 'http://' . $_SERVER['HTTP_HOST'] . $url;
            }
            if ($picUrl && false === stripos($picUrl, 'http://')) {
              $picUrl = 'http://' . $_SERVER['HTTP_HOST'] . $picUrl;
            }
            // Try to keep full message
            // @todo url shortener?
            $message = html_entity_decode($form->getValue('body'));
            if (strlen($message) + strlen($title) + strlen($url) + strlen($picUrl) + 9 <= 140) {
              if ($title) {
                $message .= ' - ' . $title;
              }
              if ($url) {
                $message .= ' - ' . $url;
              }
              if ($picUrl) {
                $message .= ' - ' . $picUrl;
              }
            } else if (strlen($message) + strlen($title) + strlen($url) + 6 <= 140) {
              if ($title) {
                $message .= ' - ' . $title;
              }
              if ($url) {
                $message .= ' - ' . $url;
              }
            } else {
              if (strlen($title) > 24) {
                $title = Engine_String::substr($title, 0, 21) . '...';
              }
              // Sigh truncate I guess
              if (strlen($message) + strlen($title) + strlen($url) + 9 > 140) {
                $message = Engine_String::substr($message, 0, 140 - (strlen($title) + strlen($url) + 9)) - 3 . '...';
              }
              if ($title) {
                $message .= ' - ' . $title;
              }
              if ($url) {
                $message .= ' - ' . $url;
              }
            }
            $twitter = $twitterTable->getApi();
            $twitter->statuses->update($message);
          }
        } catch (Exception $e) {
          // Silence
        }
      }
      // Publish to janrain
      if (//$this->_getParam('post_to_janrain', false) &&
              'publish' == Engine_Api::_()->getApi('settings', 'core')->core_janrain_enable) {
        try {
          $session = new Zend_Session_Namespace('JanrainActivity');
          $session->unsetAll();
          $session->message = $publishMessage;
          $session->url = $publishUrl ? $publishUrl : 'http://' . $_SERVER['HTTP_HOST'] . _ENGINE_R_BASE;
          $session->name = $publishName;
          $session->desc = $publishDesc;
          $session->picture = $publishPicUrl;
        } catch (Exception $e) {
          // Silence
        }
      }
    } catch (Exception $e) {
      $db->rollBack();
      throw $e; // This should be caught by error handler
    }
    // If we're here, we're done
    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Success!');
    $typeItem = ucwords(str_replace(array('sesvideo_'), '', $attachment->getType()));
    // Redirect if in normal context
    if (null === $this->_helper->contextSwitch->getCurrentContext()) {
      $return_url = $form->getValue('return_url', false);
      if (!$return_url) {
        $return_url = $this->view->url(array(), 'default', true);
      }
      return $this->_helper->redirector->gotoUrl($return_url, array('prependBase' => false));
    } else if ('smoothbox' === $this->_helper->contextSwitch->getCurrentContext()) {
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRefresh' => false,
          'messages' => array($typeItem . ' share successfully.')
      ));
    } else if (isset($_POST['is_ajax'])) {
      echo "true";
      die();
    }
  }
  public function subcategoryAction() {
    $category_id = $this->_getParam('category_id', null);
    if ($category_id) {
      $categoryTable = Engine_Api::_()->getDbtable('categories', 'sesvideo');
      $category_select = $categoryTable->select()
              ->from($categoryTable->info('name'))
              ->where('subcat_id = ?', $category_id);
      $subcategory = $categoryTable->fetchAll($category_select);
      $count_subcat = count($subcategory->toarray());
      if (isset($_POST['selected']))
        $selected = $_POST['selected'];
      else
        $selected = '';
      $data = '';
      if ($subcategory && $count_subcat) {
        $data .= '<option value="0">' . Zend_Registry::get('Zend_Translate')->_("Choose a Sub Category") . '</option>';
        foreach ($subcategory as $category) {
          $data .= '<option ' . ($selected == $category['category_id'] ? 'selected = "selected"' : '') . ' value="' . $category["category_id"] . '" >' . Zend_Registry::get('Zend_Translate')->_($category["category_name"]) . '</option>';
        }
      }
    } else
      $data = '';
    echo $data;
    die;
  }
  public function subsubcategoryAction() {
    $category_id = $this->_getParam('subcategory_id', null);
    if ($category_id) {
      $categoryTable = Engine_Api::_()->getDbtable('categories', 'sesvideo');
      $category_select = $categoryTable->select()
              ->from($categoryTable->info('name'))
              ->where('subsubcat_id = ?', $category_id);
      $subcategory = $categoryTable->fetchAll($category_select);
      $count_subcat = count($subcategory->toarray());
      if (isset($_POST['selected']))
        $selected = $_POST['selected'];
      else
        $selected = '';
      $data = '';
      if ($subcategory && $count_subcat) {
        $data .= '<option value="0">' . Zend_Registry::get('Zend_Translate')->_("Choose a Sub Sub Category") . '</option>';
        foreach ($subcategory as $category) {
          $data .= '<option ' . ($selected == $category['category_id'] ? 'selected = "selected"' : '') . ' value="' . $category["category_id"] . '">' . Zend_Registry::get('Zend_Translate')->_($category["category_name"]) . '</option>';
        }
      }
    } else
      $data = '';
    echo $data;
    die;
  }
  public function importVideoFromYoutubePlaylist($playlistId = null, $leftvideos, $values, $form, $limitYoutubePlaylist, $googleApiKey,$approve) {
    if (!$playlistId)
      return;
    require_once 'Google/autoload.php';
    require_once 'Google/Client.php';
    require_once 'Google/Service/YouTube.php';
    $client = new Google_Client();
    $client->setDeveloperKey($googleApiKey);
    $youtube = new Google_Service_YouTube($client);
    $nextPageToken = '';
		$this->_counterVideoUploaded = 0;
		$this->_leftvideo = $leftvideos;
    $playlistItemsResponse = array();
    $videoIds = array();
    $counter = 1;
    $key = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey');
	

    do {
      if (($leftvideos && $counter > 1 && $counter * 50 >= $leftvideos ) || ($counter > 1 && $counter * 50 > $limitYoutubePlaylist && $limitYoutubePlaylist > 0))
        break;
      $playlistItemsResponse = $youtube->playlistItems->listPlaylistItems('snippet', array(
          'playlistId' => $values['code'],
          'maxResults' => 50,
          'pageToken' => $nextPageToken));

      foreach ($playlistItemsResponse['items'] as $playlistItem) {
        $videoIds[] = $playlistItem['snippet']['resourceId']['videoId'];
      }
      $ids = implode(',', $videoIds);
      $data = file_get_contents('https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails&id=' . $ids . '&key=' . $key);
      if (!$data) {
        return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sesvideo_general', true) . '?openTab=videos';
      }
      $data = Zend_Json::decode($data);
      $returnError = $this->uploadYoutubePlaylistVideos($values, $form, $leftvideos, $data, $limitYoutubePlaylist,$approve);
      if (!$returnError)
        return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sesvideo_general', true) . '?openTab=videos';

      $nextPageToken = $playlistItemsResponse['nextPageToken'];
      $counter++;
    } while ($nextPageToken <> '');
    return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sesvideo_general', true) . '?openTab=videos';
  }

  public function uploadYoutubePlaylistVideos($values, $form, $leftVideos, $data, $limitYoutubePlaylist,$approve) {
    $viewer = Engine_Api::_()->user()->getViewer();
    $values['user_id'] = $viewer->getIdentity();
    // Process
    $values['owner_id'] = $viewer->getIdentity();
    $insert_action = false;
    $db = Engine_Api::_()->getDbtable('videos', 'sesvideo')->getAdapter();
    $db->beginTransaction();
    try {
      //Create video
      $table = Engine_Api::_()->getDbtable('videos', 'sesvideo');
      $values['type'] = 1;
      $changeCode = true;
      $counterVideoUploadLeft = 0;
			$values['approve'] = $approve;
			if(isset($values['artists']))
				$artists = $values['artists'];
      foreach ($data['items'] as $videoId) {
        if (($this->_leftvideo && $this->_counterVideoUploaded == $this->_leftvideo) || ($limitYoutubePlaylist == $this->_counterVideoUploaded + 1 && $limitYoutubePlaylist > 0))
          return false;
        $video = $table->createRow();
        $values['title'] = $videoId['snippet']['title'];
        $values['description'] = $videoId['snippet']['description'];
        $values['duration'] = Engine_Date::convertISO8601IntoSeconds($videoId['contentDetails']['duration']);
        $values['code'] = $videoId['id'];
        $values['type'] = 1;
        if (isset($artists))
          $values['artists'] = json_encode($artists);
        else
          $values['artists'] = json_encode(array());
        if (is_null($values['subsubcat_id']))
          $values['subsubcat_id'] = 0;
        if (is_null($values['subcat_id']))
          $values['subcat_id'] = 0;
        //disable lock if password not set.
        if (isset($values['is_locked']) && $values['is_locked'] && $values['password'] == '') {
          $values['is_locked'] = '0';
        }
        $video->setFromArray($values);
        $video->save();
        // Add fields
        $customfieldform = $form->getSubForm('fields');
        if (!is_null($customfieldform)) {
          $customfieldform->setItem($video);
          $customfieldform->saveValues();
        }
        // Now try to create thumbnail
        $thumbnail = $this->handleThumbnail($values['type'], $values['code']);
        $ext = ltrim(strrchr($thumbnail, '.'), '.');
        $thumbnail_parsed = @parse_url($thumbnail);
        $imageUploadSize = @getimagesize($thumbnail);
				$width = isset($imageUploadSize[0]) ? $imageUploadSize[0] : '';
        $height = isset($imageUploadSize[1]) ? $imageUploadSize[1] : '';
        if (@$imageUploadSize && $width > 120 && $height > 90) {
          $valid_thumb = true;
        } else {
					if($values['type'] == 1) {
						$thumbnail = "http://img.youtube.com/vi/".$values['code']."/hqdefault.jpg";
						if (@getimagesize($thumbnail)) {
							 $valid_thumb = true;
							 $thumbnail_parsed = @parse_url($thumbnail);
						} else {
						 $valid_thumb = false;
						}
					} else {
						$valid_thumb = false;
					}
				}
        if ($valid_thumb && $thumbnail && $ext && $thumbnail_parsed && in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
          $tmp_file = APPLICATION_PATH . '/temporary/link_' . md5($thumbnail) . '.' . $ext;
          $thumb_file = APPLICATION_PATH . '/temporary/link_thumb_' . md5($thumbnail) . '.' . $ext;
          $src_fh = fopen($thumbnail, 'r');
          $tmp_fh = fopen($tmp_file, 'w');
          stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);
          //resize video thumbnails
          $image = Engine_Image::factory();
          $image->open($tmp_file)
                  ->resize(500, 500)
                  ->write($thumb_file)
                  ->destroy();
          try {
            $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
                'parent_type' => $video->getType(),
                'parent_id' => $video->getIdentity()
            ));
            // Remove temp file
            @unlink($thumb_file);
            @unlink($tmp_file);
          } catch (Exception $e) {
            //silence 
          }
          $video->photo_id = $thumbFileRow->file_id;
          $video->status = 1;
          $video->save();
        }
				if (isset($values['lat']) && isset($values['lng']) && $values['lat'] != '' && $values['lng'] != '') {
            $dbGetInsert = Engine_Db_Table::getDefaultAdapter();
            $dbGetInsert->query('INSERT INTO engine4_sesbasic_locations (resource_id, lat, lng , resource_type) VALUES ("' . $video->video_id . '", "' . $values['lat'] . '","' . $values['lng'] . '","sesvideo_video")	ON DUPLICATE KEY UPDATE	lat = "' . $values['lat'] . '" , lng = "' . $values['lng'] . '"');
          }
        // CREATE AUTH STUFF HERE
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        if (isset($values['auth_view']))
          $auth_view = $values['auth_view'];
        else
          $auth_view = "everyone";
        $viewMax = array_search($auth_view, $roles);
        foreach ($roles as $i => $role) {
          $auth->setAllowed($video, $role, 'view', ($i <= $viewMax));
        }
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        if (isset($values['auth_comment']))
          $auth_comment = $values['auth_comment'];
        else
          $auth_comment = "everyone";
        $commentMax = array_search($auth_comment, $roles);
        foreach ($roles as $i => $role) {
          $auth->setAllowed($video, $role, 'comment', ($i <= $commentMax));
        }
        // Add tags
        $tags = preg_split('/[,]+/', $values['tags']);
        $video->tags()->addTagMaps($viewer, $tags);
        $owner = $video->getOwner();
        //Create Activity Feed 
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $video, 'sesvideo_video_create');
        if ($action != null) {
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $video);
        }
        // Rebuild privacy
        $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
        foreach ($actionTable->getActionsByObject($video) as $action) {
          $actionTable->resetActivityBindings($action);
        }
        $db->commit();
        $this->_counterVideoUploaded++;
      }
    } catch (Exception $e) {
      return false;
    }
    return true;
  }
  public function createAction() {
    if (!$this->_helper->requireUser->isValid())
      return;
    if (!$this->_helper->requireAuth()->setAuthParams('video', null, 'create')->isValid())
      return;
    $this->view->defaultProfileId = $defaultProfileId = Engine_Api::_()->getDbTable('metas', 'sesvideo')->profileFieldId();
    if (isset($video->category_id) && $video->category_id != 0) {
      $this->view->category_id = $video->category_id;
    } else if (isset($_POST['category_id']) && $_POST['category_id'] != 0)
      $this->view->category_id = $_POST['category_id'];
    else
      $this->view->category_id = 0;
    if (isset($video->subsubcat_id) && $video->subsubcat_id != 0) {
      $this->view->subsubcat_id = $video->subsubcat_id;
    } else if (isset($_POST['subsubcat_id']) && $_POST['subsubcat_id'] != 0)
      $this->view->subsubcat_id = $_POST['subsubcat_id'];
    else
      $this->view->subsubcat_id = 0;
    if (isset($video->subcat_id) && $video->subcat_id != 0) {
      $this->view->subcat_id = $video->subcat_id;
    } else if (isset($_POST['subcat_id']) && $_POST['subcat_id'] != 0)
      $this->view->subcat_id = $_POST['subcat_id'];
    else
      $this->view->subcat_id = 0;
    // Upload video
    if (isset($_GET['ul']))
      return $this->_forward('upload-video', null, null, array('format' => 'json'));
    if (isset($_FILES['Filedata']) && !empty($_FILES['Filedata']['name']))
      $_POST['id'] = $this->uploadVideoAction();
    // Render
    $this->_helper->content->setEnabled();
    // set up data needed to check quota
    $viewer = Engine_Api::_()->user()->getViewer();
    $values['user_id'] = $viewer->getIdentity();
    $paginator = Engine_Api::_()->getApi('core', 'sesvideo')->getVideosPaginator($values);
    $this->view->quota = $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'video', 'max');
    $this->view->current_count = $currentCount = $paginator->getTotalItemCount();
    if ($quota)
      $leftVideos = $quota - $currentCount;
    else
      $leftVideos = 0; //o means unlimited
//Create form
    $this->view->form = $form = new Sesvideo_Form_Video(array('defaultProfileId' => $defaultProfileId));
    if ($this->_getParam('type', false))
      $form->getElement('type')->setValue($this->_getParam('type'));
    if (!$this->getRequest()->isPost()) {
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues('url');
      return;
    }
    // Process
    $values = $form->getValues();
    $values['parent_id'] = $parent_id = $this->_getParam('parent_id', null);
    $values['parent_type'] = $parent_type = $this->_getParam('parent_type', null);
		if( $values['parent_id'] &&  $values['parent_type'])
    $parentItem = Engine_Api::_()->getItem($parent_type, $parent_id);
    $values['owner_id'] = $viewer->getIdentity();
    $insert_action = false;
    $db = Engine_Api::_()->getDbtable('videos', 'sesvideo')->getAdapter();
    $db->beginTransaction();
    try {
			$viewer = Engine_Api::_()->user()->getViewer();
			$isApproveUploadOption = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $viewer, 'video_approve');
			$approveUploadOption = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $viewer, 'video_approve_type');
			$approve = 1;
			if($isApproveUploadOption){
				foreach($approveUploadOption as $valuesIs){
					if ($values['type'] == 1 && $valuesIs == 'youtube') {
						//youtube
						$approve = 0;
						break;
					}else if ($values['type'] == 2 && $valuesIs == 'vimeo') {
						//vimeo
						$approve = 0;
						break;
					}else if ($values['type'] == 3 && $valuesIs == 'myComputer') {
						//my computer
						$approve = 0;
						break;
					}else if ($values['type'] == 4 && $valuesIs == 'dailymotion') {
						//dailymotion
						$approve = 0;
						break;
					}else if ($values['type'] == 5 && $valuesIs == 'youtubePlaylist') {
						//Youtube Playlist
						$approve = 0;
						break;
					}else if ($values['type'] == 16 && $valuesIs == 'url') {
						//From URL
						$approve = 0;
						break;
					}else if ($values['type'] == 17 && $valuesIs == 'embedcode') {
						//From Embed Code
						$approve = 0;
						break;				
					}
				}
			}
      //Create video
      $table = Engine_Api::_()->getDbtable('videos', 'sesvideo');
      if ($values['type'] == 3) {
        $video = Engine_Api::_()->getItem('video', $this->_getParam('id'));
      } else
        $video = $table->createRow();
      if ($values['type'] == 3 && isset($_FILES['photo_id']['name']) && $_FILES['photo_id']['name'] != '') {
        $values['photo_id'] = $this->setPhoto($form->photo_id, $video->video_id, true);
      }
      if ($values['type'] == 5) {
        $limitYoutubePlaylist = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesvideo.youtube.playlist', '25');
        $googleApiKey = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey', 0);
        if (!$googleApiKey)
          return;
        $videoIds = $this->importVideoFromYoutubePlaylist($values['code'], $leftVideos, $values, $form, $limitYoutubePlaylist, $googleApiKey,$approve);
      }
			 
        if (isset($values['artists']))
          $values['artists'] = json_encode($values['artists']);
        else
          $values['artists'] = json_encode(array());

        if (is_null($values['subsubcat_id']))
          $values['subsubcat_id'] = 0;
        if (is_null($values['subcat_id']))
          $values['subcat_id'] = 0;
        //disable lock if password not set.
        if (isset($values['is_locked']) && $values['is_locked'] && $values['password'] == '')
          $values['is_locked'] = '0';
				if(empty($_FILES['photo_id']['name'])){
					unset($values['photo_id']);
				}
				$values['approve'] = $approve;
        $video->setFromArray($values);
        $video->save();
        // Add fields
				
        $customfieldform = $form->getSubForm('fields');
        if (!is_null($customfieldform)) {
          $customfieldform->setItem($video);
          $customfieldform->saveValues();
        }
        // Now try to create thumbnail
        $thumbnail = $this->handleThumbnail($values['type'], $values['code']);
        $ext = ltrim(strrchr($thumbnail, '.'), '.');
        $thumbnail_parsed = @parse_url($thumbnail);
				$imageUploadSize = @getimagesize($thumbnail);
				$width = isset($imageUploadSize[0]) ? $imageUploadSize[0] : '';
        $height = isset($imageUploadSize[1]) ? $imageUploadSize[1] : '';
        if (@$imageUploadSize && $width > 120 && $height > 90) {
          $valid_thumb = true;
        } else{
					if($values['type'] == 1) {
						$thumbnail = "http://img.youtube.com/vi/".$values['code']."/hqdefault.jpg";
						if(@getimagesize($thumbnail)) {
							 $valid_thumb = true;
							 $thumbnail_parsed = @parse_url($thumbnail);
						}else {
						 $valid_thumb = false;
						}
					}else {
						$valid_thumb = false;
					}
				}
			if ($values['type'] == 17) {
				$regex = '/(<iframe.*? src=(\"|\'))(.*?)((\"|\').*)/';
				preg_match($regex, $values['code'], $matches);
				if(count($matches) > 2)
				{
						$video->code = $matches[3];						
						$video->status = 1;
						$video->save();
				}
        
      }
			if(isset($_FILES['photo_id']['name']) && $_FILES['photo_id']['name'] != '' && $values['type'] != 3 ){
				$video->photo_id = $this->setPhoto($form->photo_id, $video->video_id, true);
				$video->save();
			}else if($valid_thumb && $thumbnail && $ext && $thumbnail_parsed && in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
          $tmp_file = APPLICATION_PATH . '/temporary/link_' . md5($thumbnail) . '.' . $ext;
          $thumb_file = APPLICATION_PATH . '/temporary/link_thumb_' . md5($thumbnail) . '.' . $ext;
          $src_fh = fopen($thumbnail, 'r');
          $tmp_fh = fopen($tmp_file, 'w');
          stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);
          //resize video thumbnails
          $image = Engine_Image::factory();
          $image->open($tmp_file)
                  ->resize(500, 500)
                  ->write($thumb_file)
                  ->destroy();
          try {
            $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
                'parent_type' => $video->getType(),
                'parent_id' => $video->getIdentity()
            ));
            // Remove temp file
            @unlink($thumb_file);
            @unlink($tmp_file);
						$video->photo_id = $thumbFileRow->file_id;
						$video->save();
          } catch (Exception $e){
						 @unlink($thumb_file);
             @unlink($tmp_file);
						}
        }
			  if($values['type'] == 16){
				 $videoUrl = Engine_Api::_()->sesvideo()->createVideo(array(), $values['code'], $values,$video);
				 $video->status = 1;
				 $video->save();
			  }
				if($values['type'] != 3){
					$information = $this->handleInformation($values['type'], $values['code']);
					if(is_array($information)){
						$video->duration = isset($information['duration']) ? $information['duration'] : '';
						if (!$video->description) {
							$video->description = isset($information['description']) ? $information['description'] : '';
						}
						if (!$video->title) {
							$video->title = $information['title'];
						}					
						$video->status = 1;
						$video->save();
						// Insert new action item
						$insert_action = true;
					}
				}
				if (isset($_POST['lat']) && isset($_POST['lng']) && $_POST['lat'] != '' && $_POST['lng'] != '') {
            $dbGetInsert = Engine_Db_Table::getDefaultAdapter();
            $dbGetInsert->query('INSERT INTO engine4_sesbasic_locations (resource_id, lat, lng , resource_type) VALUES ("' . $video->video_id . '", "' . $_POST['lat'] . '","' . $_POST['lng'] . '","sesvideo_video")	ON DUPLICATE KEY UPDATE	lat = "' . $_POST['lat'] . '" , lng = "' . $_POST['lng'] . '"');
          }
        if ($values['ignore'] == true) {
          $video->status = 1;
          $video->save();
          $insert_action = true;
        }
        // CREATE AUTH STUFF HERE
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        if (isset($values['auth_view']))
          $auth_view = $values['auth_view'];
        else
          $auth_view = "everyone";
        $viewMax = array_search($auth_view, $roles);
        foreach ($roles as $i => $role) {
          $auth->setAllowed($video, $role, 'view', ($i <= $viewMax));
        }
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        if (isset($values['auth_comment']))
          $auth_comment = $values['auth_comment'];
        else
          $auth_comment = "everyone";
        $commentMax = array_search($auth_comment, $roles);
        foreach ($roles as $i => $role) {
          $auth->setAllowed($video, $role, 'comment', ($i <= $commentMax));
        }
        // Add tags
        $tags = preg_split('/[,]+/', $values['tags']);
        $video->tags()->addTagMaps($viewer, $tags);
        $db->commit();
				
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $db->beginTransaction();
    try {
      if ($approve) {
        $owner = $video->getOwner();
        //Create Activity Feed 
        
        if($parent_id && $parent_type) {
		      $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $parentItem, 'sesevent_event_editeventvideo');
	        if ($action != null) {
	          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $video);
	        }
        } else {
	        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $video, 'sesvideo_video_create');
	        if ($action != null) {
	          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $video);
	        }
        }
				// Rebuild privacy
				$actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
				foreach ($actionTable->getActionsByObject($video) as $action) {
					$actionTable->resetActivityBindings($action);
				}
      }
      
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    if (($video->type == 3 && $video->status != 1) || !$approve) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sesvideo_general', true) . '?openTab=videos';
    }
    if ($parent_id && $parent_type == 'sesevent_event') {
      $eventItem = Engine_Api::_()->getItem('sesevent_event', $parent_id);
      return $this->_helper->redirector->gotoRoute(array('id' => $parent_id), 'sesevent_profile', true);
    } elseif ($parent_id && $parent_type == 'sesblog_blog') {
      $blog = Engine_Api::_()->getItem('sesblog_blog', $parent_id);
      $tab_id = Engine_Api::_()->sesbasic()->getWidgetTabId(array('name' => 'sesblog.profile-videos'));
      return $this->_helper->redirector->gotoRoute(array('blog_id' => $parent_id, 'slug' => $blog->getSlug(), 'tab_id' => $tab_id), 'sesblog_entry_view', true);
    } else {
      return $this->_helper->redirector->gotoRoute(array('user_id' => $viewer->getIdentity(), 'video_id' => $video->getIdentity(), 'slug' => $video->getSlug()), 'sesvideo_view', true);
    }
  }
  protected function setPhoto($photo, $id) {
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
    if (!$fileName) {
      $fileName = $file;
    }
    $name = basename($file);
    $extension = ltrim(strrchr($fileName, '.'), '.');
    $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
        'parent_type' => 'video',
        'parent_id' => $id,
        'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
        'name' => $fileName,
    );
    // Save
    $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
    $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_main.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
            ->resize(500, 500)
            ->write($mainPath)
            ->destroy();
    // Store
    try {
      $iMain = $filesTable->createFile($mainPath, $params);
    } catch (Exception $e) {
      // Remove temp files
      @unlink($mainPath);
      // Throw
      if ($e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE) {
        throw new Sesvideo_Model_Exception($e->getMessage(), $e->getCode());
      } else {
        throw $e;
      }
    }
    // Remove temp files
    @unlink($mainPath);
    // Update row
    // Delete the old file?
    if (!empty($tmpRow)) {
      $tmpRow->delete();
    }
    return $iMain->file_id;
  }
  public function uploadVideoAction() {
    if (!$this->_helper->requireUser()->checkRequire()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }
    $values = $this->getRequest()->getPost();
    if (empty($_FILES['Filedata'])) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
      return;
    }
    if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload') . print_r($_FILES, true);
      return;
    }
    $illegal_extensions = array('php', 'pl', 'cgi', 'html', 'htm', 'txt','zip');
    if (in_array(pathinfo($_FILES['Filedata']['name'], PATHINFO_EXTENSION), $illegal_extensions)) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
      return;
    }
    $db = Engine_Api::_()->getDbtable('videos', 'sesvideo')->getAdapter();
    $db->beginTransaction();
    try {
      $viewer = Engine_Api::_()->user()->getViewer();
      $values['owner_id'] = $viewer->getIdentity();
      $params = array(
          'owner_type' => 'user',
          'owner_id' => $viewer->getIdentity()
      );
      $video = Engine_Api::_()->sesvideo()->createVideo($params, $_FILES['Filedata'], $values);
      $this->view->status = true;
      $this->view->name = $_FILES['Filedata']['name'];
      $this->view->code = $video->code;
      $this->view->video_id = $video->video_id;
      // sets up title and owner_id now just incase members switch page as soon as upload is completed
      $video->title = $_FILES['Filedata']['name'];
      $video->owner_id = $viewer->getIdentity();
      $video->save();
      $db->commit();
      return $video->video_id;
    } catch (Exception $e) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.') . $e;
      // throw $e;
      return;
    }
  }
  public function deleteAction() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $video = Engine_Api::_()->getItem('video', $this->getRequest()->getParam('video_id'));
    if (!$this->_helper->requireAuth()->setAuthParams($video, null, 'delete')->isValid())
      return;
    // In smoothbox
    $this->_helper->layout->setLayout('default-simple');
    $this->view->form = $form = new Sesbasic_Form_Delete();
    $form->setTitle('Delete Video?');
    $form->setDescription('Are you sure that you want to delete this video? It will not be recoverable after being deleted. ');
    $form->submit->setLabel('Delete');
    if (!$video) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Video doesn't exists or not authorized to delete");
      return;
    }
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }
    $db = $video->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      Engine_Api::_()->getApi('core', 'sesvideo')->deleteVideo($video);
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Video has been deleted.');
    return $this->_forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'sesvideo_general', true) . '?openTab=videos',
                'messages' => Array($this->view->message)
    ));
  }
  public function editAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;
    $viewer = Engine_Api::_()->user()->getViewer();
    $video = Engine_Api::_()->getItem('video', $this->_getParam('video_id'));
    if (isset($video->category_id) && $video->category_id != 0) {
      $this->view->category_id = $video->category_id;
    } else if (isset($_POST['category_id']) && $_POST['category_id'] != 0)
      $this->view->category_id = $_POST['category_id'];
    else
      $this->view->category_id = 0;
    if (isset($video->subsubcat_id) && $video->subsubcat_id != 0) {
      $this->view->subsubcat_id = $video->subsubcat_id;
    } else if (isset($_POST['subsubcat_id']) && $_POST['subsubcat_id'] != 0)
      $this->view->subsubcat_id = $_POST['subsubcat_id'];
    else
      $this->view->subsubcat_id = 0;
    if (isset($video->subcat_id) && $video->subcat_id != 0) {
      $this->view->subcat_id = $video->subcat_id;
    } else if (isset($_POST['subcat_id']) && $_POST['subcat_id'] != 0)
      $this->view->subcat_id = $_POST['subcat_id'];
    else
      $this->view->subcat_id = 0;
    // Render
    $this->_helper->content->setEnabled();
    $this->view->defaultProfileId = $defaultProfileId = Engine_Api::_()->getDbTable('metas', 'sesvideo')->profileFieldId();
    if (!$this->_helper->requireSubject()->isValid())
      return;
    if ($viewer->getIdentity() != $video->owner_id && !$this->_helper->requireAuth()->setAuthParams($video, null, 'edit')->isValid()) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    $this->view->video = $video;
    $this->view->form = $form = new Sesvideo_Form_Edit(array('defaultProfileId' => $defaultProfileId));
    if (isset($video->artists)) {
      $artists_array = json_decode($video->artists);
      if (count($artists_array) > 0)
        $form->artists->setValue(json_decode($video->artists));
    }
		$latLng = Engine_Api::_()->getDbTable('locations', 'sesbasic')->getLocationData('sesvideo_video',$video->video_id);
		if($latLng){
			if($form->getElement('lat'))
				$form->getElement('lat')->setValue($latLng->lat);
			if($form->getElement('lng'))
				$form->getElement('lng')->setValue($latLng->lng);
		}
		if($form->getElement('location'))
		$form->getElement('location')->setValue($video->location);
    $form->getElement('search')->setValue($video->search);
    $form->getElement('title')->setValue($video->title);
    $form->getElement('description')->setValue($video->description);
    $form->getElement('category_id')->setValue($video->category_id);
    if ($form->getElement('is_locked'))
      $form->getElement('is_locked')->setValue($video->is_locked);
    if ($form->getElement('password'))
      $form->getElement('password')->setValue($video->password);
    // authorization
    $auth = Engine_Api::_()->authorization()->context;
    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
    foreach ($roles as $role) {
      if (1 === $auth->isAllowed($video, $role, 'view')) {
        $form->auth_view->setValue($role);
      }
      if (1 === $auth->isAllowed($video, $role, 'comment')) {
        $form->auth_comment->setValue($role);
      }
    }
    // prepare tags
    $videoTags = $video->tags()->getTagMaps();
    $tagString = '';
    foreach ($videoTags as $tagmap) {
      if ($tagString !== '')
        $tagString .= ', ';
      $tagString .= $tagmap->getTag()->getTitle();
    }
    $this->view->tagNamePrepared = $tagString;
    $form->tags->setValue($tagString);
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }
    // Process
    $db = Engine_Api::_()->getDbtable('videos', 'sesvideo')->getAdapter();
    $db->beginTransaction();
    try {
      $values = $form->getValues();
      if (isset($values['artists']))
        $values['artists'] = json_encode($values['artists']);
      else
        $values['artists'] = json_encode(array());

      if (isset($_FILES['photo_id']['name']) && $_FILES['photo_id']['name'] != '') {
        $values['photo_id'] = $this->setPhoto($form->photo_id, $video->video_id, true);
      } else {
        if (empty($values['photo_id'])){
          unset($values['photo_id']);
				}
      }
			if (Engine_Api::_()->getApi('core', 'sesbasic')->isModuleEnable(array('seslock'))) {
				//disable lock if password not set.
				if (!$values['is_locked']) {
					$values['is_locked'] = '0';
					$values['password'] = '';
				}else
					unset($values['password']);
			}
      if (isset($_POST['lat']) && isset($_POST['lng']) && $_POST['lat'] != '' && $_POST['lng'] != '') {
        $dbGetInsert = Engine_Db_Table::getDefaultAdapter();
        $dbGetInsert->query('INSERT INTO engine4_sesbasic_locations (resource_id, lat, lng , resource_type) VALUES ("' . $this->_getParam('video_id') . '", "' . $_POST['lat'] . '","' . $_POST['lng'] . '","sesvideo_video")	ON DUPLICATE KEY UPDATE	lat = "' . $_POST['lat'] . '" , lng = "' . $_POST['lng'] . '"');
      }
      $video->setFromArray($values);
      $video->save();
      // Add fields
      $customfieldform = $form->getSubForm('fields');
      if (!is_null($customfieldform)) {
        $customfieldform->setItem($video);
        $customfieldform->saveValues();
      }
      // CREATE AUTH STUFF HERE
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      if ($values['auth_view'])
        $auth_view = $values['auth_view'];
      else
        $auth_view = "everyone";
      $viewMax = array_search($auth_view, $roles);
      foreach ($roles as $i => $role) {
        $auth->setAllowed($video, $role, 'view', ($i <= $viewMax));
      }
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      if ($values['auth_comment'])
        $auth_comment = $values['auth_comment'];
      else
        $auth_comment = "everyone";
      $commentMax = array_search($auth_comment, $roles);
      foreach ($roles as $i => $role) {
        $auth->setAllowed($video, $role, 'comment', ($i <= $commentMax));
      }
      // Add tags
      $tags = preg_split('/[,]+/', $values['tags']);
      $video->tags()->setTagMaps($viewer, $tags);
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $db->beginTransaction();
    try {
      // Rebuild privacy
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach ($actionTable->getActionsByObject($video) as $action) {
        $actionTable->resetActivityBindings($action);
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sesvideo_general', true) . '?openTab=videos';
  }
  public function lastElementDataAction() {
		$this->view->type = $this->_getParam('type','video');
		$this->view->item_id = $this->_getParam('item_id','');
		$this->renderScript('video/last-element-data.tpl');
  }
  public function uploadAction() {
    if (isset($_GET['ul']) || isset($_FILES['Filedata']))
      return $this->_forward('upload-video', null, null, array('format' => 'json'));
    if (!$this->_helper->requireUser()->isValid())
      return;
    $this->view->form = $form = new Sesvideo_Form_Video();
    $this->view->navigation = $this->getNavigation();
    if (!$this->getRequest()->isPost()) {
      if (null !== ($album_id = $this->_getParam('album_id'))) {
        $form->populate(array(
            'album' => $album_id
        ));
      }
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
    $album = $form->saveValues();
    //$this->_helper->redirector->gotoRoute(array('album_id'=>$album->album_id), 'album_editphotos', true);
  }
  public function manageAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;
    // Render
    $this->_helper->content->setEnabled();
    // Populate form
  }
  public function composeUploadAction() {
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer->getIdentity()) {
      $this->_redirect('login');
      return;
    }
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid method');
      return;
    }
    $video_title = $this->_getParam('title');
    $video_url = $this->_getParam('uri');
    $video_type = $this->_getParam('type');
    $composer_type = $this->_getParam('c_type', 'wall');
    // extract code
    $code = $this->extractCode($video_url, $video_type);
    // check if code is valid
    // check which API should be used
    if ($video_type == 1) {
      $valid = $this->checkYouTube($code);
    }
    if ($video_type == 2) {
      $valid = $this->checkVimeo($code);
    }
    if ($video_type == 4) {
      $valid = $this->checkdailymotion($code);
    }
    // check to make sure the user has not met their quota of # of allowed video uploads
    // set up data needed to check quota
    $values['user_id'] = $viewer->getIdentity();
    $paginator = Engine_Api::_()->getApi('core', 'sesvideo')->getVideosPaginator($values);
    $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'video', 'max');
    $current_count = $paginator->getTotalItemCount();
    if (($current_count >= $quota) && !empty($quota)) {
      // return error message
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('You have already uploaded the maximum number of videos allowed. If you would like to upload a new video, please delete an old one first.');
    } else if ($valid) {
      $db = Engine_Api::_()->getDbtable('videos', 'sesvideo')->getAdapter();
      $db->beginTransaction();
      try {
        $information = $this->handleInformation($video_type, $code);
        // create video
        $table = Engine_Api::_()->getDbtable('videos', 'sesvideo');
        $video = $table->createRow();
        $video->title = $information['title'];
        $video->description = $information['description'];
        $video->duration = $information['duration'];
        $video->owner_id = $viewer->getIdentity();
        $video->code = $code;
        $video->type = $video_type;
        $video->save();
        // Now try to create thumbnail
        $thumbnail = $this->handleThumbnail($video->type, $video->code);
        $ext = ltrim(strrchr($thumbnail, '.'), '.');
				$thumbnail_parsed = @parse_url($thumbnail);
				$imageUploadSize = @getimagesize($thumbnail);
				$width = isset($imageUploadSize[0]) ? $imageUploadSize[0] : '';
        $height = isset($imageUploadSize[1]) ? $imageUploadSize[1] : '';
        if (@$imageUploadSize && $width > 120 && $height > 90) {$valid_thumb = true;}else{
					if($video_type == 1) {
							$thumbnail = "http://img.youtube.com/vi/".$video->code."/hqdefault.jpg";
							if (@getimagesize($thumbnail)) {
								 $valid_thumb = true;
								 $thumbnail_parsed = @parse_url($thumbnail);
							} else {	$valid_thumb = false;}
						}else
							$valid_thumb = false;
				}
        $tmp_file = APPLICATION_PATH . '/temporary/link_' . md5($thumbnail) . '.' . $ext;
        $thumb_file = APPLICATION_PATH . '/temporary/link_thumb_' . md5($thumbnail) . '.' . $ext;
        $src_fh = fopen($thumbnail, 'r');
        $tmp_fh = fopen($tmp_file, 'w');
        stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);
        $image = Engine_Image::factory();
        $image->open($tmp_file)
                ->resize(500, 500)
                ->write($thumb_file)
                ->destroy();
        $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
            'parent_type' => $video->getType(),
            'parent_id' => $video->getIdentity()
        ));
				@unlink($tmp_file);
				@unlink($thumb_file);
        // If video is from the composer, keep it hidden until the post is complete
        if ($composer_type)
          $video->search = 0;
        $video->photo_id = $thumbFileRow->file_id;
        $video->status = 1;
        $video->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
				@unlink($tmp_file);
				@unlink($thumb_file);
        throw $e;
      }
      // make the video public
      if ($composer_type === 'wall') {
        // CREATE AUTH STUFF HERE
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        foreach ($roles as $i => $role) {
          $auth->setAllowed($video, $role, 'view', ($i <= $roles));
          $auth->setAllowed($video, $role, 'comment', ($i <= $roles));
        }
      }
      $this->view->status = true;
      $this->view->video_id = $video->video_id;
      $this->view->photo_id = $video->photo_id;
      $this->view->title = $video->title;
      $this->view->description = $video->description;
      $this->view->src = $video->getPhotoUrl();
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Video posted successfully');
    } else {
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('We could not find a video there - please check the URL and try again.');
    }
  }
  public function validationAction() {
    $video_type = $this->_getParam('type');
    $code = $this->_getParam('code');
    $ajax = $this->_getParam('ajax', false);
    $mURL = $this->_getParam('url');
    $valid = false;
    // check which API should be used
    if ($video_type == "youtube") {
      $valid = $this->checkYouTube($code);
    } else if ($video_type == "vimeo") {
      $valid = $this->checkVimeo($code);
    } else if ($video_type == 'dailymotion') {
      $valid = $this->checkdailymotion($code);
    } else if ($video_type == 'youtubePlaylist') {
      $valid = $this->checkYoutubePlaylist($code);
    } else if ($video_type == 'embedCode') {
      $valid = $this->checkembedCode($code);
    }else if ($video_type == 'fromurl') {
      $valid = $this->checkFromUrl($code);
    }
    $this->view->code = $code;
    $this->view->ajax = $ajax;
    $this->view->valid = $valid;
  }
	public function checkembedCode($url){
		if(!$url)
			return false;
		$regex = '/(<iframe.*? src=(\"|\'))(.*?)((\"|\').*)/';
		preg_match($regex, $url, $matches);
		if(count($matches) > 2)
		{
				return true;
		}else
			return false;
	}
	public function checkFromUrl($url){
		if(!$url)
			return false;
		$ch = curl_init(trim($url));
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
		curl_exec($ch);
		# get the content type
  	$output = curl_getinfo($ch,CURLINFO_CONTENT_TYPE);
		if(strpos($output,'video') === FALSE){
			return false;	
		}else
			return true;
	}
  public function getNavigation() {
    $this->view->navigation = $navigation = new Zend_Navigation();
    $navigation->addPage(array(
        'label' => 'Browse Videos',
        'route' => 'sesvideo_general',
        'action' => 'browse',
        'controller' => 'index',
        'module' => 'sesvideo'
    ));
    if (Engine_Api::_()->user()->getViewer()->getIdentity()) {
      $navigation->addPages(array(
          array(
              'label' => 'My Videos',
              'route' => 'sesvideo_general',
              'action' => 'manage',
              'controller' => 'index',
              'module' => 'sesvideo'
          ),
          array(
              'label' => 'Post New Video',
              'route' => 'sesvideo_general',
              'action' => 'create',
              'controller' => 'index',
              'module' => 'sesvideo'
          )
      ));
    }
    return $navigation;
  }
  // HELPER FUNCTIONS
  public function extractCode($url, $type) {
    switch ($type) {
      //youtube
      case "1":
        // change new youtube URL to old one
        $new_code = @pathinfo($url);
        $url = preg_replace("/#!/", "?", $url);

        // get v variable from the url
        $arr = array();
        $arr = @parse_url($url);
        if ($arr['host'] === 'youtu.be') {
          $data = explode("?", $new_code['basename']);
          $code = $data[0];
        } else {
          $parameters = $arr["query"];
          parse_str($parameters, $data);
          $code = $data['v'];
          if ($code == "") {
            $code = $new_code['basename'];
          }
        }
        return $code;
      //vimeo
      case "2":
        // get the first variable after slash
        $code = @pathinfo($url);
        return $code['basename'];
      //dailymotion
      case "4":
        // get the first variable after slash
        $code = @pathinfo($url);
        $code = explode('_', $code['basename']);
        if (isset($code[0]))
          return $code[0];
        else
          return '';
    }
  }
  // YouTube Functions
  public function checkYouTubePlaylist($code) {
    require_once 'Google/autoload.php';
    require_once 'Google/Client.php';
    require_once 'Google/Service/YouTube.php';
    $client = new Google_Client();
    $client->setDeveloperKey(Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey', 0));
    $youtube = new Google_Service_YouTube($client);
    $nextPageToken = '';
    $playlistItemsResponse = $youtube->playlistItems->listPlaylistItems('snippet', array(
        'playlistId' => $code,
        'maxResults' => 50,
        'pageToken' => $nextPageToken));
    if (isset($playlistItemsResponse['items'][0]['snippet']['resourceId']['videoId']))
      return true;
    else
      return false;
  }
  // YouTube Functions
  public function checkYouTube($code) {
    $key = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey');
    if (!$data = @file_get_contents('https://www.googleapis.com/youtube/v3/videos?part=id&id=' . $code . '&key=' . $key))
      return false;
    $data = Zend_Json::decode($data);
    if (empty($data['items']))
      return false;
    return true;
  }
  // Vimeo Functions
  public function checkVimeo($code) {
    //http://www.vimeo.com/api/docs/simple-api
    //http://vimeo.com/api/v2/video
    $data = @simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
    $id = count($data->video->id);
    if ($id == 0)
      return false;
    return true;
  }
  public function checkdailymotion($code) {
    //https://api.dailymotion.com/video/$code?fields=allow_embed,description,duration,thumbnail_url,title
    $data = @file_get_contents("https://api.dailymotion.com/video/$code?fields=allow_embed");
    if ($data != '') {
      $data = json_decode($data, true);
      if (isset($data['allow_embed']) && $data['allow_embed'])
        return true;
    }
    return false;
  }
  // handles thumbnails
  public function handleThumbnail($type, $code = null) {
    switch ($type) {
      //youtube
      case "1":
        return "http://img.youtube.com/vi/$code/maxresdefault.jpg";
      //vimeo
      case "2":
        //thumbnail_medium
        $data = unserialize(file_get_contents("http://vimeo.com/api/v2/video/$code.php"));
        $thumbnail = $data[0]['thumbnail_large'];
        return $thumbnail;
      case "4":
        $data = @file_get_contents("https://api.dailymotion.com/video/$code?fields=thumbnail_url");
        if ($data != '') {
          $data = json_decode($data, true);
          $thumbnail_url = (isset($data['thumbnail_url']) && $data['thumbnail_url']) ? $data['thumbnail_url'] : '';
          return $thumbnail_url;
        }
    }
  }
  // retrieves infromation and returns title + desc
  public function handleInformation($type, $code) {
    switch ($type) {
      //youtube
      case "1":
        $key = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey');
        $data = file_get_contents('https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails&id=' . $code . '&key=' . $key);
        if (empty($data)) {
          return;
        }
        $data = Zend_Json::decode($data);
        $information = array();
        $youtube_video = $data['items'][0];
        $information['title'] = $youtube_video['snippet']['title'];
        $information['description'] = $youtube_video['snippet']['description'];
        $information['duration'] = Engine_Date::convertISO8601IntoSeconds($youtube_video['contentDetails']['duration']);
        return $information;
      //vimeo
      case "2":
        //thumbnail_medium
        $data = simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
        $thumbnail = $data->video->thumbnail_medium;
        $information = array();
        $information['title'] = $data->video->title;
        $information['description'] = $data->video->description;
        $information['duration'] = $data->video->duration;
        return $information;
      case "4":
        $data = @file_get_contents("https://api.dailymotion.com/video/$code?fields=allow_embed,description,duration,thumbnail_url,title");
        $data = json_decode($data, true);
        $information['title'] = $data['title'];
        $information['description'] = $data['description'];
        $information['duration'] = $data['duration'];
        return $information;
    }
  }
  //fetch user favourite item as per given item id .
  public function favouriteItemAction() {
    $item_id = $this->_getParam('item_id', '0');
    $item_type = $this->_getParam('item_type', '0');
    if (!$item_id || !$item_type)
      return;
		$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
		$title = $this->_getParam('title',0);
		$this->view->title = $title == '' ? $view->translate("People Who Favourite This") : $title;
    $page = isset($_POST['page']) ? $_POST['page'] : 1;
    $this->view->viewmore = isset($_POST['viewmore']) ? $_POST['viewmore'] : '';
    $item = Engine_Api::_()->getItem($item_type, $item_id);
    $param['type'] = $this->view->item_type = $item_type;
    $param['id'] = $param['resource_id'] = $this->view->item_id = $item->getIdentity();
    if ($item_type == 'video'){
			$param['type'] = 'sesvideo_video';
      $paginator = Engine_Api::_()->getDbTable('videos', 'sesvideo')->getFavourite($param);
		}
    else
      $paginator = Engine_Api::_()->getDbTable('chanels', 'sesvideo')->getFavourite($param);
    $this->view->item_id = $item_id;
    $this->view->paginator = $paginator;
    // Set item count per page and current page number
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber($page);
  }
  //fetch user like item as per given item id .
  public function likeItemAction() {
    $item_id = $this->_getParam('item_id', '0');
    $item_type = $this->_getParam('item_type', '0');
    if (!$item_id || !$item_type)
      return;
		$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $title = $this->_getParam('title',0);
		$this->view->title = $title == '' ? $view->translate("People Who Like This") : $title;
    $page = isset($_POST['page']) ? $_POST['page'] : 1;
    $this->view->viewmore = isset($_POST['viewmore']) ? $_POST['viewmore'] : '';
    $item = Engine_Api::_()->getItem($item_type, $item_id);
    $param['type'] = $this->view->item_type = $item_type;
    $param['id'] = $this->view->item_id = $item->getIdentity();
    $paginator = Engine_Api::_()->sesvideo()->likeItemCore($param);
    $this->view->item_id = $item_id;
    $this->view->paginator = $paginator;
    // Set item count per page and current page number
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber($page);
  }
}