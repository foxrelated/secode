<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: VideoController.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_VideoController extends Core_Controller_Action_Standard {

	//COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
  public function init() {

    $video_id = $this->_getParam('video_id', $this->_getParam('id', null));
    if ($video_id) {
      $video = Engine_Api::_()->getItem('video', $video_id);
      if ($video) {
        Engine_Api::_()->core()->setSubject($video);
      }
    }
  }

	//ACTION FOR SHOWING THE VIDEO LISTING
  public function indexAction() {

		//ONLY LOGGED IN USER CAN VIEW THIS PAGE
    if (!$this->_helper->requireUser->isValid())
      return;

		//VIDEO CREATION SHOULD BE ALLOWED
    if (!$this->_helper->requireAuth()->setAuthParams('video', null, 'create')->isValid())
      return;

		//GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

		//GET LISTING
    $this->view->listing_id = $listing_id = $this->_getParam('listing_id');
    $this->view->list = $list = Engine_Api::_()->getItem('list_listing', $listing_id);

		//GET CONTENT ID
    $this->view->content_id = $content_id = $this->_getParam('content_id');

		//WHO CAN EDIT THE LISTING
    $this->view->canEdit = Engine_Api::_()->authorization()->isAllowed($list, $viewer, 'edit');
    
		//ACTIVE TAB
    $this->view->TabActive = "video";

		//VIDEO UPLOAD IS ALLOWED OR NOT
		$this->view->allowed_upload_video = Engine_Api::_()->list()->allowVideo($list, $viewer);
		if (empty($this->view->allowed_upload_video)) {
			return $this->_forward('requireauth', 'error', 'core');
		}

		//PHOTO UPLOAD IS ALLOWED OR NOT
    $this->view->allowed_upload_photo = Engine_Api::_()->authorization()->isAllowed($list, $viewer, 'photo');

    $video = null;
    $values['user_id'] = $viewer->getIdentity();

		//COUNT TOTAL VIDEO
    $videoTable = Engine_Api::_()->getDbtable('videos', 'video');
    $this->view->videoCount = $videoTable->select()
																				->from($videoTable->info('name'), array('COUNT(*) AS total_video'))
																				->where('status = ?', 1)
																				->where('owner_id = ?', $viewer_id)
																				->query()
																				->fetchColumn();

    $this->view->video = $video = Engine_Api::_()->getItemTable('list_clasfvideo', 'list')->getListingVideos($listing_id, 0);

    $this->view->message = $message = null;
    $session = new Zend_Session_Namespace();
    if (isset($session->video_message)) {
      $message = $session->video_message;
      unset($session->video_message);
    }

    //UPLOAD VIDEO
    if (isset($_GET['ul']) || isset($_FILES['Filedata'])) {
      return $this->_forward('upload-video', null, null, array('format' => 'json'));
    }

		//GET VIDEO PAGINATOR
    $values['user_id'] = $viewer_id;
    $paginator = Engine_Api::_()->getApi('core', 'video')->getVideosPaginator($values);
		$this->view->current_count = $paginator->getTotalItemCount();

		//GET TOTAL ALLOWED VIDEO
    $this->view->quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'video', 'max');
    
    //MAKE FORM
    $this->view->form = $form = new List_Form_Video_Video();

    if ($this->_getParam('type', false)) {
      $form->getElement('type')->setValue($this->_getParam('type'));
		}

    $this->view->display = 0;

		//CHECK POST
    if (!$this->getRequest()->isPost()) {
      return;
    }

    $this->view->display = 1;

    if (!$form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues('url');
      return;
    }

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //PROCESS
      $values = $form->getValues();
      $values['owner_id'] = $viewer->getIdentity();
      $insert_action = false;
      $db = Engine_Api::_()->getDbtable('videos', 'video')->getAdapter();
      $db->beginTransaction();
      try {

        //CREATE VIDEO
        $table = Engine_Api::_()->getDbtable('videos', 'video');
        if ($values['type'] == 3) {
          $video = Engine_Api::_()->getItem('video', $this->_getParam('id'));
        }
        else {
          $video = $table->createRow();
				}

        $video->setFromArray($values);
        $video->save();

				//CREATE THUMBNAIL
        $thumbnail = $this->handleThumbnail($video->type, $video->code);
        $ext = ltrim(strrchr($thumbnail, '.'), '.');
        $thumbnail_parsed = @parse_url($thumbnail);

        if (@GetImageSize($thumbnail)) {
          $valid_thumb = true;
        } else {
          $valid_thumb = false;
        }

        if ($valid_thumb && $thumbnail && $ext && $thumbnail_parsed && in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {

          $tmp_file = APPLICATION_PATH . '/temporary/link_' . md5($thumbnail) . '.' . $ext;
          $thumb_file = APPLICATION_PATH . '/temporary/link_thumb_' . md5($thumbnail) . '.' . $ext;
          $src_fh = fopen($thumbnail, 'r');
          $tmp_fh = fopen($tmp_file, 'w');
          stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);
          $image = Engine_Image::factory();
          $image->open($tmp_file)
              ->resize(120, 240)
              ->write($thumb_file)
              ->destroy();
          try {
            $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
                        'parent_type' => $video->getType(),
                        'parent_id' => $video->getIdentity()
                ));

            //REMOVE TEMP FILES
            @unlink($thumb_file);
            @unlink($tmp_file);
          } catch (Exception $e) {

          }
          $information = $this->handleInformation($video->type, $video->code);

          $video->duration = $information['duration'];
          if (!$video->description)
            $video->description = $information['description'];
          $video->photo_id = $thumbFileRow->file_id;
          $video->status = 1;
          $video->save();

          //INSERT NEW ACTION ITEM
          $insert_action = true;
        }

        if ($values['ignore'] == true) {

          $video->status = 1;
          $video->save();

          //INSERT NEW ACTION ITEM
          $insert_action = true;
          $owner = $video->getOwner();

          //INSERT NEW ACTION ITEM
          $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $video, 'video_new');
          if ($action != null) {
            Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $video);
          }
        }

        //CREATE AUTH STUFF HERE
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');
        if (isset($values['auth_view']))
          $auth_view = $values['auth_view'];
        else
          $auth_view = "everyone";
        $viewMax = array_search($auth_view, $roles);

        foreach ($roles as $i => $role) {
          $auth->setAllowed($video, $role, 'view', ($i <= $viewMax));
        }

        if (isset($values['auth_comment']))
          $auth_comment = $values['auth_comment'];
        else
          $auth_comment = "everyone";
        $commentMax = array_search($auth_comment, $roles);
        foreach ($roles as $i => $role) {
          $auth->setAllowed($video, $role, 'comment', ($i <= $commentMax));
        }

        //ADD TAGS
        $tags = preg_split('/[,]+/', $values['tags']);
        $video->tags()->addTagMaps($viewer, $tags);
        $db->commit();
        $db->beginTransaction();
        try {
          if ($insert_action) {
            $owner = $video->getOwner();
            $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $video, 'video_new');
            if ($action != null) {
              Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $video);
            }
          }

          //REBUILD PRIVACY
          $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
          foreach ($actionTable->getActionsByObject($video) as $action) {
            $actionTable->resetActivityBindings($action);
          }
          $db->commit();
        } catch (Exception $e) {
          $db->rollBack();
          throw $e;
        }

        $id = $video->getIdentity();
        $table = Engine_Api::_()->getItemTable('list_clasfvideo', 'list');
        $select = $table->select();
        $rName = $table->info('name');
        $select->where($rName . '.listing_id = ?', $listing_id);
        $row = $table->fetchAll($select);
        if ($id != NULL) {
          try {

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            $row = $table->createRow();
            $row->listing_id = $listing_id;
            $row->created = date('Y-m-d H:i:s');
            $row->video_id = $id;
            $row->save();

            $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
            $list = Engine_Api::_()->getItem('list_listing', $listing_id);
            if (!Engine_Api::_()->core()->hasSubject('list_listing')) {
              Engine_Api::_()->core()->clearSubject();
              Engine_Api::_()->core()->setSubject($list);
            }
            $subject = Engine_Api::_()->core()->getSubject();
            $subjectOwner = $subject->getOwner('user');

            $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $subject, 'video_list', '', array(
                        'owner' => $subjectOwner->getGuid(),
                        'title' => $subject->getTitle()
                ));

            if ($action != null) {
              Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $video);
            }
            $db->commit();
            unset($_POST);
            if ($list->owner_id == $viewer_id) {
              return $this->_helper->redirector->gotoRoute(array('action' => 'edit', 'listing_id' => $listing_id), 'list_videospecific', true);
            } else {
              $content_id = $this->_getParam('content_id');
              return $this->_helper->redirector->gotoRoute(array('listing_id' => $listing_id, 'user_id' => $list->owner_id, 'slug' => $list->getSlug(), 'tab' => $content_id), 'list_entry_view', true);
            }
          } catch (Exception $e) {
            $db->rollBack();
            throw $e;
          }
        }
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
  }

	//ACTION FOR AUTO SUGGEST SEARCH BASED ON VIDEO TITLE
  public function suggestAction() {

		//GET DETAILS
		$params = array();
    $params['viewer_id'] = Engine_Api::_()->user()->getViewer()->getIdentity();
		$params['text'] = $this->_getParam('text');
		$params['limit'] = $this->_getParam('limit', 40);

		//FETCH RESULTS
    $videoLists = Engine_Api::_()->list()->getAutoSuggestedVideo($params);

    $data = array();
    $mode = $this->_getParam('struct');

    if ($mode == 'text') {
      foreach ($videoLists as $videolist) {
        $content_photo = $this->view->itemPhoto($videolist, 'thumb.icon');
        $data[] = array(
                'id' => $videolist->video_id,
                'label' => $videolist->title,
                'photo' => $content_photo
        );
      }
    } else {
      foreach ($videoLists as $videolist) {
        $content_photo = $this->view->itemPhoto($videolist, 'thumb.icon');
        $data[] = array(
                'id' => $videolist->video_id,
                'label' => $videolist->title,
                'photo' => $content_photo
        );
      }
    }

    if ($this->_getParam('sendNow', true)) {
      return $this->_helper->json($data);
    } else {
      $this->_helper->viewRenderer->setNoRender(true);
      $data = Zend_Json::encode($data);
      $this->getResponse()->setBody($data);
    }
  }

  //stored the selected search  video title into clasfvideo
  public function loadAction() {

    $url = '';
    $this->view->listing_id = $listing_id = $this->_getParam('listing_id');

    $session = new Zend_Session_Namespace();
    $list = Engine_Api::_()->getItem('list_listing', $listing_id);

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //PROCESS
    $table = Engine_Api::_()->getDbtable('videos', 'video');
    $select = $table->select()
            ->order('creation_date DESC');
    $rName = $table->info('name');
    $select->where($rName . '.owner_id = ?', $viewer_id);

    if ($this->getRequest()->isPost()) {
      $values = $_POST;

      if (!empty($values['video_id'])) {
        $select->where($rName . '.video_id = ?', $values['video_id']);
      } else {
        $select->where($rName . '.video_id = ?', 0);
      }
    }
    $this->view->uploadvideolist = $uploadvideolist = $table->fetchAll($select);

    if (empty($uploadvideolist) || empty($values['video_id'])) {
      $session->video_message = 'No matching videos were found.';
      $this->_helper->redirector->gotoRoute(array('action' => 'index', 'listing_id' => $listing_id), 'list_video_upload');
    }

    foreach ($uploadvideolist as $list) {
      $id = $list->video_id;
		}

    $table = Engine_Api::_()->getItemTable('list_clasfvideo', 'list');
    $select = $table->select();
    $rName = $table->info('name');
    $select->where($rName . '.listing_id = ?', $listing_id);
    $row = $table->fetchAll($select);

    if ($id != NULL) {
      try {
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        $row = $table->createRow();
        $row->listing_id = $listing_id;
        $row->created = date('Y-m-d H:i:s');
        $row->video_id = $id;
        $row->save();

        $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');

        $list = Engine_Api::_()->getItem('list_listing', $listing_id);
        if (!Engine_Api::_()->core()->hasSubject('list_listing')) {
          Engine_Api::_()->core()->clearSubject();
          Engine_Api::_()->core()->setSubject($list);
        }
        $subject = Engine_Api::_()->core()->getSubject();
        $subjectOwner = $subject->getOwner('user');

        //ACTIVITY
        $video = Engine_Api::_()->getItem('video', $id);
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $subject, 'video_list', '', array(
                    'owner' => $subjectOwner->getGuid(),
                    'title' => $subject->getTitle()
            ));
        if ($action != null) {
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $video);
        }
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }

    if ($list->owner_id == $viewer_id) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'edit', 'listing_id' => $listing_id), 'list_videospecific', true);
    } else {
      $content_id = $this->_getParam('content_id');
      return $this->_helper->redirector->gotoRoute(array('listing_id' => $listing_id, 'user_id' => $list->owner_id, 'slug' => $list->getSlug(), 'tab' => $content_id), 'list_entry_view', true);
    }

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

    if (empty($values['Filename'])) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
      return;
    }

    if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload') . print_r($_FILES, true);
      return;
    }

    $db = Engine_Api::_()->getDbtable('videos', 'video')->getAdapter();
    $db->beginTransaction();

    try {
      $viewer = Engine_Api::_()->user()->getViewer();
      $values['owner_id'] = $viewer->getIdentity();

      $params = array(
              'owner_type' => 'user',
              'owner_id' => $viewer->getIdentity()
      );
      $video = Engine_Api::_()->video()->createVideo($params, $_FILES['Filedata'], $values);
      $video->owner_id = $viewer->getIdentity();
      $video->save();
      $this->view->status = true;
      $this->view->name = $_FILES['Filedata']['name'];
      $this->view->code = $video->code;
      $this->view->video_id = $video->getIdentity();

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.') . $e;
      //throw $e;
      return;
    }
  }

	//ACTION FOR VALIDATING FOR VIDEO UPLOAD
  public function validationAction() {

    $video_type = $this->_getParam('type');
    $code = $this->_getParam('code');
    $ajax = $this->_getParam('ajax', false);
    $valid = false;

		//CHECK WHICH API SHOULD BE USED
    if ($video_type == "youtube") {
      $valid = $this->checkYouTube($code);
    }

    if ($video_type == "vimeo") {
      $valid = $this->checkVimeo($code);
    }

    $this->view->code = $code;
    $this->view->ajax = $ajax;
    $this->view->valid = $valid;
  }

  //HELPER FUNCTIONS
  public function extractCode($url, $type) {

    switch ($type) {

      //YOUTUBE
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

      //VIMEO
      case "2":
				//GET THE FIRST VARIABLE AFTER SLASH
        $code = @pathinfo($url);
        return $code['basename'];
    }
  }

  //FUNCTION YOUTUBE
  public function checkYouTube($code) {

    if (!$data = @file_get_contents("http://gdata.youtube.com/feeds/api/videos/" . $code))
      return false;

    if ($data == "Video not found")
      return false;
    return true;
  }

  //FUNCTION VIMEO
  public function checkVimeo($code) {

    //http://www.vimeo.com/api/docs/simple-api
    //http://vimeo.com/api/v2/video
    $data = @simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
    $id = count($data->video->id);
    if ($id == 0)
      return false;
    return true;
  }

  //FUNCTION FOR HANDLING THUMBNAILS
  public function handleThumbnail($type, $code = null) {
    switch ($type) {

      //YOUTUBE
      case "1":
        //http://img.youtube.com/vi/Y75eFjjgAEc/default.jpg
        return "http://img.youtube.com/vi/$code/default.jpg";

      //VIMEO
      case "2":
				//MEDIUM THUMBNAIL
        $data = simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
        $thumbnail = $data->video->thumbnail_medium;
        return $thumbnail;
    }
  }

	//FUNCTION FOR RETREVES INFORMATION AND RETURES TITLE AND DESCRIPTION
  public function handleInformation($type, $code) {
    switch ($type) {

      //YOUTUBE
      case "1":
        $yt = new Zend_Gdata_YouTube();
        $youtube_video = $yt->getVideoEntry($code);
        $information = array();
        $information['title'] = $youtube_video->getTitle();
        $information['description'] = $youtube_video->getVideoDescription();
        $information['duration'] = $youtube_video->getVideoDuration();
        //http://img.youtube.com/vi/Y75eFjjgAEc/default.jpg
        return $information;

      //VIMEO
      case "2":
				//MEDIUM THUMBNAIL
        $data = simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
        $thumbnail = $data->video->thumbnail_medium;
        $information = array();
        $information['title'] = $data->video->title;
        $information['description'] = $data->video->description;
        $information['duration'] = $data->video->duration;
        //http://img.youtube.com/vi/Y75eFjjgAEc/default.jpg
        return $information;
    }
  }

}
