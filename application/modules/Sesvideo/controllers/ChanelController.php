<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: ChanelController.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
class Sesvideo_ChanelController extends Core_Controller_Action_Standard {
  public function init() {
    $setting = Engine_Api::_()->getApi('settings', 'core');
    if (!$setting->getSetting('video_enable_chanel', 1)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
  }
  public function indexAction() {
    $chanel_id = $this->_getParam('chanel_id', false);
    if ($chanel_id) {
      $chanel_id = Engine_Api::_()->getDbtable('chanels', 'sesvideo')->getChanelId($chanel_id);
    } else {
      return $this->_forward('requireauth', 'error', 'core');
    }
    $chanel = Engine_Api::_()->getItem('sesvideo_chanel', $chanel_id);
    if ($chanel) {
      Engine_Api::_()->core()->setSubject($chanel);
    }
    if (!$this->_helper->requireSubject()->isValid())
      return;
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
	  if (!$subject->isOwner($viewer)) {
			$subject->view_count++;
			$subject->save();
		}
    if (!$this->_helper->requireAuth()->setAuthParams($chanel, $viewer, 'view')->isValid())
      return;
    // if this is sending a message id, the user is being directed from a coversation
    // check if member is part of the conversation
    $message_id = $this->getRequest()->getParam('message');
    $message_view = false;
    if ($message_id) {
      $conversation = Engine_Api::_()->getItem('messages_conversation', $message_id);
      if ($conversation->hasRecipient(Engine_Api::_()->user()->getViewer())) {
        $message_view = true;
      }
    }
    $this->view->message_view = $message_view;
		$getmodule = Engine_Api::_()->getDbTable('modules', 'core')->getModule('core');
		if (!empty($getmodule->version) && version_compare($getmodule->version, '4.8.8') >= 0){
			$this->view->doctype('XHTML1_RDFA');
			$this->view->docActive = true;
		}
    /* Insert data for recently viewed widget */
    if ($viewer->getIdentity() != 0 && isset($chanel->chanel_id)) {
      $dbObject = Engine_Db_Table::getDefaultAdapter();
      $dbObject->query('INSERT INTO engine4_sesvideo_recentlyviewitems (resource_id, resource_type,owner_id,creation_date ) VALUES ("' . $chanel->chanel_id . '", "sesvideo_chanel","' . $viewer->getIdentity() . '",NOW())	ON DUPLICATE KEY UPDATE	creation_date = NOW()');
    }
    // Render
    $this->_helper->content->setEnabled();
  }
  public function overviewAction() {
    $chanel_id = $this->_getParam('chanel_id', false);
    if (!$this->_helper->requireAuth()->setAuthParams('sesvideo_chanel', null, 'edit')->isValid()) {
      return;
    }
    if ($chanel_id) {
      $chanel = Engine_Api::_()->getItem('sesvideo_chanel', $chanel_id);
      // In smoothbox
      $this->_helper->layout->setLayout('default-simple');
      $this->view->form = $form = new Sesvideo_Form_Chanel_Overview();
      $form->populate($chanel->toArray());
      if (!$chanel) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_("Channel doesn't exists or not authorized");
        return;
      }
      if (!$this->getRequest()->isPost()) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
        return;
      }
      $db = $chanel->getTable()->getAdapter();
      $db->beginTransaction();
      try {
        $chanel->overview = $_POST['overview'];
        $chanel->save();
        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Channel Overview has been updated successfully.');
        $db->commit();
        return $this->_forward('success', 'utility', 'core', array(
                    'messages' => Array($this->view->message),
                    'layout' => 'default-simple',
                    'parentRefresh' => true,
                    'smoothboxClose' => false,
        ));
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
  }
	//update cover photo function
	public function editCoverphotoAction(){
		$chanel_id = $this->_getParam('chanel_id', '0');
		if ($chanel_id == 0)
			return;
		$chanel = Engine_Api::_()->getItem('sesvideo_chanel', $chanel_id);
		if(!$chanel)
			return;
		$art_cover = $chanel->cover_id;
		if(isset($_FILES['Filedata']))
			$data = $_FILES['Filedata'];
		else if(isset($_FILES['webcam']))
			$data = $_FILES['webcam'];
		$chanel->setCoverPhoto($data);
		if($art_cover != 0){
			$im = Engine_Api::_()->getItem('storage_file', $art_cover);
			$im->delete();
		}
		echo json_encode(array('status'=>"true",'src'=>Engine_Api::_()->storage()->get($chanel->cover_id)->getPhotoUrl()));die;
	}
	//remove cover photo action
	public function removeCoverAction(){
		$chanel_id = $this->_getParam('chanel_id', '0');
		if ($chanel_id == 0)
			return;
		$chanel = Engine_Api::_()->getItem('sesvideo_chanel', $chanel_id);		
		if(!$chanel)
			return;
		if(isset($chanel->cover_id) && $chanel->cover_id>0){
			$im = Engine_Api::_()->getItem('storage_file', $chanel->cover_id);
			$chanel->cover_id = 0;
			$chanel->save();
			$im->delete();
		}
		echo "true";die;
	}
  public function categoryAction() {
    if (empty($_GET['category_id']) && empty($_GET['subcat_id']) && empty($_GET['subsubcat_id']))
      return $this->_helper->redirector->gotoRoute(array('action' => 'browse'), 'sesvideo_chanel', true);
    // Render
    $this->_helper->content->setEnabled();
  }

  public function browseAction() {
    // Render
    $this->_helper->content->setEnabled();
  }
  //get search chanel
  public function getChanelAction() {
    $sesdata = array();
    $value['search'] = $this->_getParam('text', '');
    $chanels = Engine_Api::_()->getDbtable('chanels', 'sesvideo')->getChanels($value);
    foreach ($chanels as $chanel) {
      $chanel_icon = $this->view->itemPhoto($chanel, 'thumb.icon');
      $sesdata[] = array(
          'id' => $chanel->chanel_id,
          'chanel_id' => $chanel->chanel_id,
          'label' => $chanel->title,
          'photo' => $chanel_icon,
      );
    }
    return $this->_helper->json($sesdata);
  }
  public function createAction() {
    if (!$this->_helper->requireUser->isValid())
      return;
    if (!$this->_helper->requireAuth()->setAuthParams('sesvideo_chanel', null, 'create')->isValid())
      return;
		$optionsEnableChanel = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.enable.chaneloption', 0);
    if (isset($_POST['category_id'])) {
      $this->view->category_id = $_POST['category_id'];
    }
    if (isset($_POST['subcat_id'])) {
      $this->view->subcat_id = $_POST['subcat_id'];
    }
    if (isset($_POST['subsubcat_id'])) {
      $this->view->subsubcat_id = $_POST['subsubcat_id'];
    }
    // Render
    $this->_helper->content->setEnabled();
    // set up data needed to check quota
    $viewer = Engine_Api::_()->user()->getViewer();
    $values['user_id'] = $user_id = $viewer->getIdentity();
    $paginator = Engine_Api::_()->getApi('core', 'sesvideo')->getChanelPaginator($values);
    $this->view->quota = $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'video', 'maxchanel');
    $this->view->current_count = $paginator->getTotalItemCount();
    // Create form
    $this->view->form = $form = new Sesvideo_Form_Chanel();
    if ($this->_getParam('type', false))
      $form->getElement('type')->setValue($this->_getParam('type'));
    if (!$this->getRequest()->isPost()) {
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }
    // Process
    $values = $form->getValues();
    $values['owner_id'] = $viewer->getIdentity();
    $insert_action = false;
    $db = Engine_Api::_()->getDbtable('chanels', 'sesvideo')->getAdapter();
    $db->beginTransaction();
    $dbChanel = Engine_Api::_()->getDbtable('chanelvideos', 'sesvideo')->getAdapter();
    $dbChanel->beginTransaction();
    try {
      // Create video
      $table = Engine_Api::_()->getDbtable('chanels', 'sesvideo');
      $chanel = $table->createRow();
      if (is_null($values['subsubcat_id']))
        $values['subsubcat_id'] = 0;
      if (is_null($values['subcat_id']))
        $values['subcat_id'] = 0;
      $chanel->setFromArray($values);
      $chanel->save();
      // Now try to create thumbnail
      if (isset($_FILES['chanel_cover']['name']) && $_FILES['chanel_cover']['name'] != '') {
        $chanel->cover_id = $this->setPhoto($form->chanel_cover, $chanel->chanel_id, true);
      }
      if (isset($_FILES['chanel_thumbnail']['name']) && $_FILES['chanel_thumbnail']['name'] != '') {
        $chanel->thumbnail_id = $this->setPhoto($form->chanel_thumbnail, $chanel->chanel_id);
      }
      $chanel->save();
      if (empty($_POST['custom_url']) && $_POST['custom_url'] == '') {
        $chanel->custom_url = $chanel->chanel_id;
      } else {
        $chanel->custom_url = $_POST['custom_url'];
      }
      $chanel->save();
      // CREATE AUTH STUFF HERE
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      if (empty($values['auth_view'])) {
        $values['auth_view'] = key($form->auth_view->options);
        if (empty($values['auth_view'])) {
          $values['auth_view'] = 'everyone';
        }
      }
      if (empty($values['auth_comment'])) {
        $values['auth_comment'] = key($form->auth_comment->options);
        if (empty($values['auth_comment'])) {
          $values['auth_comment'] = 'owner_member';
        }
      }

      $viewMax = array_search($values['auth_view'], $roles);
      $commentMax = array_search($values['auth_comment'], $roles);

      //set roles
      foreach ($roles as $i => $role) {
        $auth->setAllowed($chanel, $role, 'view', ($i <= $viewMax));
        $auth->setAllowed($chanel, $role, 'comment', ($i <= $commentMax));
      }
      // Add tags
      $tags = preg_split('/[,]+/', $values['tags']);
      $chanel->tags()->addTagMaps($viewer, $tags);

      //Create Activity Feed 
      $owner = $chanel->getOwner();
      $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $chanel, 'sesvideo_chanel_create');
      if ($action != null) {
        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $chanel);
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $db->beginTransaction();
    try {
      if ($insert_action) {
        $owner = $video->getOwner();
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $chanel, 'video_chanel_new');
        if ($action != null) {
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $chanel);
        }
      }
      // Rebuild privacy
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach ($actionTable->getActionsByObject($chanel) as $action) {
        $actionTable->resetActivityBindings($action);
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    // Now try to create videos
    if (isset($values['video_ids']) && $values['video_ids'] != '' && isset($chanel->chanel_id)) {
      $explodeIds = explode(',', rtrim($values['video_ids'], ','));
      $queryString = '';
      $runQuery = false;
      foreach ($explodeIds as $valuesChanel) {
        if (intval($valuesChanel) == 0 || $valuesChanel == '')
          continue;
        $valueChanels['chanel_id'] = $chanel->chanel_id;
        $valueChanels['video_id'] = $valuesChanel;
        $valueChanels['owner_id'] = $user_id;
        $valueChanels['creation_date'] = 'NOW()';
        $valueChanels['modified_date'] = 'NOW()';
        $queryString .= '(' . implode(',', $valueChanels) . '),';
        $runQuery = true;
      }

      //Activity Feed work
      $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity(Engine_Api::_()->user()->getViewer(), $chanel, 'sesvideo_chanel_new', null, array('count' => count(explode(',', rtrim($values['video_ids'], ',')))));
//      if ($action instanceof Activity_Model_Action && $count < 8) {
//        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
//      }
      if ($runQuery) {
        $dbObject = Engine_Db_Table::getDefaultAdapter();
        $query = 'INSERT IGNORE INTO engine4_video_chanelvideos (`chanel_id`, `video_id` ,`owner_id`,`creation_date`,`modified_date`) VALUES ';
        $stmt = $dbObject->query($query . rtrim($queryString, ','));
      }
    }
    if (isset($chanel->custom_url))
      return $this->_helper->redirector->gotoRoute(array('chanel_id' => $chanel->custom_url), 'sesvideo_chanel_view', true);
    else
      return $this->_helper->redirector->gotoRoute(array('action' => 'browse'), 'sesvideo_chanel', true);
  }
  //function to download chanel photo from lightbox
  public function downloadAction() {
    $file_id = $this->getRequest()->getParam('file_id');
    $chanelphoto = Engine_Api::_()->getItem('sesvideo_chanelphoto', $this->getRequest()->getParam('photo_id'));
    if (!$chanelphoto)
      return;
    $chanelphoto->download_count = $chanelphoto->download_count + 1;
    $chanelphoto->save();
    $file_id = $chanelphoto->file_id;
    if ($file_id == '' || intval($file_id) == 0)
      return;
    $storageTable = Engine_Api::_()->getDbTable('files', 'storage');
    $select = $storageTable->select()->from($storageTable->info('name'), array('storage_path', 'name'))->where('file_id = ?', $file_id);
    $storageData = $storageTable->fetchRow($select);
    $storageData = (object) $storageData->toArray();
    if (empty($storageData->name) || $storageData->name == '' || empty($storageData->storage_path) || $storageData->storage_path == '')
      return;
    //Get base path
    $basePath = APPLICATION_PATH . '/' . $storageData->storage_path;
    @chmod($basePath, 0777);
    header("Content-Disposition: attachment; filename=" . urlencode(basename($storageData->name)), true);
    header("Content-Transfer-Encoding: Binary", true);
    header("Content-Type: application/force-download", true);
    header("Content-Type: application/octet-stream", true);
    header("Content-Type: application/download", true);
    header("Content-Description: File Transfer", true);
    header("Content-Length: " . filesize($basePath), true);
    readfile("$basePath");
    exit();
    // for safety resason double check
    return;
  }

  public function deleteAction() {
		
    $viewer = Engine_Api::_()->user()->getViewer();
    $chanel = Engine_Api::_()->getItem('sesvideo_chanel', $this->getRequest()->getParam('chanel_id'));
    if (!$chanel->authorization()->isAllowed($viewer, 'delete'))
     return $this->_forward('notfound', 'error', 'core');
    $chanel_id = $this->getRequest()->getParam('chanel_id', false);
    if ($chanel_id) {
      // In smoothbox
      $this->_helper->layout->setLayout('default-simple');

      $this->view->form = $form = new Sesbasic_Form_Delete();
      $form->setTitle('Delete Channel');
      $form->setDescription('Are you sure that you want to delete this channel? It will not be recoverable after being deleted. ');
      $form->submit->setLabel('Delete');

      if (!$chanel) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_("Channel doesn't exists or not authorized to delete");
        return;
      }
      if (!$this->getRequest()->isPost()) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
        return;
      }
      $dbObject = Engine_Db_Table::getDefaultAdapter();
      $query = $dbObject->query('DELETE FROM engine4_video_chanels WHERE  `chanel_id` = ' . $chanel_id);
      $queryVideos = $dbObject->query('DELETE FROM engine4_video_chanelvideos WHERE  `chanel_id` = ' . $chanel_id);
      $queryFollow = $dbObject->query('DELETE FROM engine4_video_chanelfollows WHERE  `chanel_id` = ' . $chanel_id);
      $this->view->status = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Channel has been deleted.');
      return $this->_forward('success', 'utility', 'core', array(
                  'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'sesvideo_general', true) . '?openTab=my_channels',
                  'messages' => Array($this->view->message)
      ));
    }
  }

  public function deletePhotoAction() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $chanelPhoto = Engine_Api::_()->getItem('sesvideo_chanelphoto', $this->getRequest()->getParam('photo_id'));
    if (!$this->_helper->requireAuth()->setAuthParams('sesvideo_chanelphoto', $viewer, 'delete')->isValid())
      return;
    $photo_id = $this->getRequest()->getParam('photo_id');
    if ($photo_id) {
      // In smoothbox
      $this->_helper->layout->setLayout('default-simple');
      
      $this->view->form = $form = new Sesbasic_Form_Delete();
      $form->setTitle('Delete Photo');
      $form->setDescription('Are you sure that you want to delete this photo? It will not be recoverable after being deleted. ');
      $form->submit->setLabel('Delete');
      
      if (!$chanelPhoto) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_("Channel Photo doesn't exists or not authorized to delete");
        return;
      }
      if (!$this->getRequest()->isPost()) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
        return;
      }
      $dbObject = Engine_Db_Table::getDefaultAdapter();
      $query = $dbObject->query('DELETE FROM engine4_video_chanelphotos WHERE  `chanelphoto_id` = ' . $photo_id);
      $this->view->status = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('PChanel hoto has been deleted successfully.');
      return $this->_forward('success', 'utility', 'core', array(
                  'parentRedirect' => Engine_Api::_()->getItem('sesvideo_chanel', $this->getRequest()->getParam('chanel_id'))->getHref(),
                  'messages' => Array($this->view->message)
      ));
    }
  }

  public function photosAction() {

    if (isset($_GET['ul']) || isset($_FILES['Filedata']))
      return $this->_forward('upload-photo', null, null, array('format' => 'json'));
    if (!$this->_getParam('chanel_id', false))
      return $this->_forward('requireauth', 'error', 'core');
    if (!$this->_helper->requireUser()->isValid())
      return;
    $viewer = Engine_Api::_()->user()->getViewer();
    $chanel = Engine_Api::_()->getItem('sesvideo_chanel', $this->_getParam('chanel_id'));
    if ($chanel)
      Engine_Api::_()->core()->setSubject($chanel);
    if ($viewer->getIdentity() != $chanel->owner_id && !$this->_helper->requireAuth()->setAuthParams('sesvideo_chanel', null, 'edit')->isValid()) {
      return;
    }
    /* check sesalbum plugin enable or not ,if no then return */
    if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesalbum'))
      return $this->_forward('requireauth', 'error', 'core');
    $this->view->form = $form = new Sesvideo_Form_Photos();
    if (!$this->getRequest()->isPost()) {
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $db = Engine_Api::_()->getItemTable('sesvideo_chanelphoto')->getAdapter();
    $db->beginTransaction();
    try {
      $chanel = $form->saveValues();
      if (isset($_POST['lat']) && isset($_POST['lng']) && $_POST['lat'] != '' && $_POST['lng'] != '') {
        $dbGetInsert = Engine_Db_Table::getDefaultAdapter();
        $dbGetInsert->query('INSERT INTO engine4_sesbasic_locations (resource_id, lat, lng , resource_type) VALUES ("' . $chanel->album_id . '", "' . $_POST['lat'] . '","' . $_POST['lng'] . '","sesvideo_chanel")	ON DUPLICATE KEY UPDATE	lat = "' . $_POST['lat'] . '" , lng = "' . $_POST['lng'] . '"');
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $url = $this->view->url(array('controller' => 'chanel', 'module' => 'sesvideo', 'action' => 'index', 'chanel_id' => $chanel->custom_url), 'sesvideo_chanel_view');
    header('location:' . $url);
  }

  public function uploadPhotoAction() {
    if (!$this->_helper->requireAuth()->setAuthParams('sesvideo_chanel', null, 'create')->isValid())
      return;

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

    if (empty($_GET['isURL']) || $_GET['isURL'] == 'false') {
      $isURL = false;
      $values = $this->getRequest()->getPost();
      if (empty($values['Filename']) && !isset($_FILES['Filedata'])) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
        return;
      }
      if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
        return;
      }
      $uploadSource = $_FILES['Filedata'];
    } else {
      $uploadSource = $_POST['Filedata'];
      $isURL = true;
    }

    $db = Engine_Api::_()->getDbtable('chanelphotos', 'sesvideo')->getAdapter();
    $db->beginTransaction();
    try {
      $viewer = Engine_Api::_()->user()->getViewer();
      $photoTable = Engine_Api::_()->getDbtable('chanelphotos', 'sesvideo');
      $photo = $photoTable->createRow();
      $photo->setFromArray(array(
          'owner_id' => $viewer->getIdentity()
      ));
      $photo->save();
      $photo->order = $photo->chanelphoto_id;
      $setPhoto = $photo->setPhoto($uploadSource, $isURL);
      if (!$setPhoto) {
        $db->rollBack();
        $this->view->status = false;
        $this->view->error = 'An error occurred.';
        return;
      }
      $photo->save();
      $this->view->status = true;
      $this->view->photo_id = $photo->chanelphoto_id;
      $this->view->url = $photo->getPhotoUrl('thumb.normal');
      $db->commit();
    } catch (Sesvideo_Model_Exception $e) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
      throw $e;
      return;
    }
  }

  //ACTION FOR PHOT) DELETE
  public function removeAction() {
    if (empty($_POST['photo_id']))
      die('error');
    //GET PHOTO ID AND ITEM
    $photo_id = (int) $this->_getParam('photo_id');
    $photo = Engine_Api::_()->getItem('sesvideo_chanelphoto', $photo_id);
    $db = Engine_Api::_()->getDbTable('chanelphotos', 'sesvideo')->getAdapter();
    $db->beginTransaction();
    try {
      $photo->delete();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  //location chanel photo 
  public function locationAction() {
    $this->view->type = $this->_getParam('type', 'sesvideo_chanelphoto');
    $this->view->photo_id = $photo_id = $this->_getParam('photo_id');
    $viewer = Engine_Api::_()->user()->getViewer();
    $photo = Engine_Api::_()->getItem('sesvideo_chanelphoto', $photo_id);
    $this->view->photo = $photo;
    $this->view->form = $form = new Sesvideo_Form_Chanel_Location();
    $form->populate($photo->toArray());
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
      $dbGetInsert->query('INSERT INTO engine4_sesbasic_locations (resource_id, lat, lng , resource_type) VALUES ("' . $photo_id . '", "' . $_POST['lat'] . '","' . $_POST['lng'] . '","sesvideo_chanelphoto")	ON DUPLICATE KEY UPDATE	lat = "' . $_POST['lat'] . '" , lng = "' . $_POST['lng'] . '"');
    }
    $db = $photo->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      $photo->setFromArray($values);
      $photo->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    return $this->_forward('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your location have been saved.')),
                'layout' => 'default-simple',
                'parentRefresh' => false,
                'smoothboxClose' => true,
    ));
  }

  public function editPhotoAction() {
    $this->view->photo_id = $photo_id = $this->_getParam('photo_id');
    $this->view->photo = Engine_Api::_()->getItem('sesvideo_chanelphoto', $photo_id);
  }

  //edit photo details from lightbox
  public function editDetailAction() {
    $status = true;
    $error = false;
    if (!$this->_helper->requireAuth()->setAuthParams('sesvideo_chanelphoto', null, 'edit')->isValid()) {
      $status = false;
      $error = true;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $photo = Engine_Api::_()->getItem('sesvideo_chanelphoto', $_POST['photo_id']);
    if ($status && !$error) {
      $values['title'] = $_POST['title'];
      $values['description'] = $_POST['description'];
      $values['location'] = $_POST['location'];
      //update location data in sesbasic location table
      if ($_POST['lat'] != '' && $_POST['lng'] != '') {
        $dbGetInsert = Engine_Db_Table::getDefaultAdapter();
        $dbGetInsert->query('INSERT INTO engine4_sesbasic_locations (resource_id, lat, lng , resource_type) VALUES ("' . $_POST['photo_id'] . '", "' . $_POST['lat'] . '","' . $_POST['lng'] . '","sesvideo_chanelphoto")	ON DUPLICATE KEY UPDATE	lat = "' . $_POST['lat'] . '" , lng = "' . $_POST['lng'] . '"');
      }
      $db = $photo->getTable()->getAdapter();
      $db->beginTransaction();
      try {
        $photo->setFromArray($values);
        $photo->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        $status = false;
        $error = true;
      }
    }
    echo json_encode(array('status' => $status, 'error' => $error));
    die;
  }

  //edit photo details from light function.
  public function saveInformationAction() {
    $photo_id = $this->_getParam('photo_id');
    $title = $this->_getParam('title', null);
    $description = $this->_getParam('description', null);
    $location = $this->_getParam('location', null);
    if (($this->_getParam('lat')) && ($this->_getParam('lng')) && $this->_getParam('lat', '') != '' && $this->_getParam('lng', '') != '') {
      $dbGetInsert = Engine_Db_Table::getDefaultAdapter();
      $dbGetInsert->query('INSERT INTO engine4_sesbasic_locations (resource_id, lat, lng , resource_type) VALUES ("' . $photo_id . '", "' . $this->_getParam('lat') . '","' . $this->_getParam('lng') . '","sesvideo_chanelphoto")	ON DUPLICATE KEY UPDATE	lat = "' . $this->_getParam('lat') . '" , lng = "' . $this->_getParam('lng') . '"');
    }
    Engine_Api::_()->getDbTable('chanelphotos', 'sesvideo')->update(array('title' => $title, 'description' => $description, 'location' => $location), array('chanelphoto_id = ?' => $photo_id));
  }

  public function editAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;
    $viewer = Engine_Api::_()->user()->getViewer();
    $chanel = Engine_Api::_()->getItem('sesvideo_chanel', $this->_getParam('chanel_id'));
    if ($chanel)
      Engine_Api::_()->core()->setSubject($chanel);
    if (!$this->_helper->requireSubject()->isValid())
      return;
			
    if ($viewer->getIdentity() != $chanel->owner_id && !$chanel->authorization()->isAllowed($viewer, 'edit')) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    if (isset($chanel->category_id)) {
      $this->view->category_id = $chanel->category_id;
    }
    if (isset($chanel->subsubcat_id)) {
      $this->view->subsubcat_id = $chanel->subsubcat_id;
    }
		
    if (isset($chanel->subcat_id)) {
      $this->view->subcat_id = $chanel->subcat_id;
    }
    $this->view->chanel = $chanel;
    $this->view->form = $form = new Sesvideo_Form_Chanel_Edit();
    if ($chanel) {
      $form->populate($chanel->toArray());
    }
			
	
    $chanelTags = $chanel->tags()->getTagMaps();
    $tagString = '';
    foreach ($chanelTags as $tagmap) {
      if ($tagString !== '')
        $tagString .= ', ';
      $tagString .= $tagmap->getTag()->getTitle();
    }
    $this->view->tagNamePrepared = $tagString;
    $form->tags->setValue($tagString);
    if (!$this->getRequest()->isPost()) {
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      foreach ($roles as $role) {
        if (1 === $auth->isAllowed($chanel, $role, 'view') && isset($form->auth_view)) {
          $form->auth_view->setValue($role);
        }
        if (1 === $auth->isAllowed($chanel, $role, 'comment') && isset($form->auth_comment)) {
          $form->auth_comment->setValue($role);
        }
      }
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
    $values = $form->getValues();
    $values['owner_id'] = $viewer->getIdentity();
    $insert_action = false;
    $db = Engine_Api::_()->getDbtable('chanels', 'sesvideo')->getAdapter();
    $db->beginTransaction();
    $dbChanel = Engine_Api::_()->getDbtable('chanelvideos', 'sesvideo')->getAdapter();
    $dbChanel->beginTransaction();
    try {
      // Create video
      if (is_null($values['subsubcat_id']))
        $values['subsubcat_id'] = 0;
      if (is_null($values['subcat_id']))
        $values['subcat_id'] = 0;
      $chanel->setFromArray($values);
      $chanel->save();

      $deleteCo = $deleteTh = true;
      $previousCover = $chanel->cover_id;
      $previousThumbnail = $chanel->thumbnail_id;
      if (isset($values['remove_chanel_cover']) && !empty($values['remove_chanel_cover'])) {
        //Delete categories icon
        $coverIm = Engine_Api::_()->getItem('storage_file', $previousCover);
        $chanel->cover_id = 0;
        $chanel->save();
        $coverIm->delete();
        $deleteCo = false;
      }
      if (isset($values['remove_chanel_thumbnail']) && !empty($values['remove_chanel_thumbnail'])) {
        //Delete categories icon
        $thumbnailIcon = Engine_Api::_()->getItem('storage_file', $previousThumbnail);
        $chanel->thumbnail_id = 0;
        $chanel->save();
        $thumbnailIcon->delete();
        $deleteTh = false;
      }

      // Now try to create thumbnail
      if (isset($_FILES['chanel_cover']['name']) && $_FILES['chanel_cover']['name'] != '') {
        $CoverIconId = $this->setPhoto($form->chanel_cover, $chanel->chanel_id, true);
        if (!empty($CoverIconId)) {
          if ($previousCover && $deleteCo) {
            $chanelIcon = Engine_Api::_()->getItem('storage_file', $previousCover);
            $chanelIcon->delete();
          }
          $chanel->cover_id = $CoverIconId;
          $chanel->save();
        }
      }
      if (isset($_FILES['chanel_thumbnail']['name']) && $_FILES['chanel_thumbnail']['name'] != '') {
        $ThumbnailIconId = $this->setPhoto($form->chanel_thumbnail, $chanel->chanel_id);
        if (!empty($ThumbnailIconId)) {
          if ($previousThumbnail && $deleteTh) {
            $chanelThub = Engine_Api::_()->getItem('storage_file', $previousThumbnail);
            $chanelThub->delete();
          }
          $chanel->thumbnail_id = $ThumbnailIconId;
          $chanel->save();
        }
      }
      $chanel->save();
      if (empty($_POST['custom_url']) && $_POST['custom_url'] == '') {
        $chanel->custom_url = $chanel->chanel_id;
      } else {
        $chanel->custom_url = $_POST['custom_url'];
      }
      $chanel->save();
      // CREATE AUTH STUFF HERE
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      if (empty($values['auth_view'])) {
        $values['auth_view'] = key($form->auth_view->options);
        if (empty($values['auth_view'])) {
          $values['auth_view'] = 'everyone';
        }
      }
      if (empty($values['auth_comment'])) {
        $values['auth_comment'] = key($form->auth_comment->options);
        if (empty($values['auth_comment'])) {
          $values['auth_comment'] = 'owner_member';
        }
      }

      $viewMax = array_search($values['auth_view'], $roles);
      $commentMax = array_search($values['auth_comment'], $roles);
      //set roles
      foreach ($roles as $i => $role) {
        $auth->setAllowed($chanel, $role, 'view', ($i <= $viewMax));
        $auth->setAllowed($chanel, $role, 'comment', ($i <= $commentMax));
      }
      // Add tags
      $tags = preg_split('/[,]+/', $values['tags']);
      $chanel->tags()->addTagMaps($viewer, $tags);
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $db->beginTransaction();
    try {
      if ($insert_action) {
        $owner = $video->getOwner();
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $chanel, 'video_chanel_new');
        if ($action != null) {
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $chanel);
        }
      }
      // Rebuild privacy
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach ($actionTable->getActionsByObject($chanel) as $action) {
        $actionTable->resetActivityBindings($action);
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
	
		$dbObject = Engine_Db_Table::getDefaultAdapter();
		$existingVideoChanel = $dbObject->query('SELECT GROUP_CONCAT(video_id) as existing_video_ids FROM engine4_video_chanelvideos WHERE chanel_id = '.$chanel->chanel_id)->fetchColumn();
		if($existingVideoChanel && $existingVideoChanel != ''){
				$existingVideoChanel = explode(',',$existingVideoChanel);
		}
		
    // delete videos 
    if (isset($values['delete_video_ids']) && $values['delete_video_ids'] != '' && isset($chanel->chanel_id)) {
      $ids = str_replace(' ', ',', $values['delete_video_ids']);
      $query = 'DELETE FROM engine4_video_chanelvideos WHERE (`video_id`) IN (' . rtrim($ids, ',') . ') AND `chanel_id` = ' . $chanel->chanel_id;
      $stmt = $dbObject->query($query . rtrim($queryString, ','));
    }

    // Now try to create videos
    if (isset($values['video_ids']) && $values['video_ids'] != '' && isset($chanel->chanel_id)) {
      $explodeIds = explode(',', rtrim($values['video_ids'], ','));
      $queryString = '';
      $runQuery = false;
      foreach ($explodeIds as $valuesChanel) {
        if (intval($valuesChanel) == 0 || $valuesChanel == '')
          continue;
        $valueChanels['chanel_id'] = $chanel->chanel_id;
        $valueChanels['video_id'] = $valuesChanel;
        $valueChanels['owner_id'] = $viewer->getIdentity();
        $valueChanels['modified_date'] = 'NOW()';
        $queryString .= '(' . implode(',', $valueChanels) . '),';
        $runQuery = true;
      }
      if ($runQuery) {
        $query = 'INSERT IGNORE INTO engine4_video_chanelvideos (`chanel_id`, `video_id` ,`owner_id`,`creation_date`) VALUES ';
        $stmt = $dbObject->query($query . rtrim($queryString, ','));
      }
			$newVideos = array_diff(explode(',',rtrim($values['video_ids'],',')),$existingVideoChanel);
			$totalNewVideos = count($newVideos);
		if($totalNewVideos >0){
			$followerChannel = Engine_Api::_()->getDbtable('chanelfollows', 'sesvideo')->getChanelFollowers($chanel->chanel_id,false,$chanel->owner_id);
			$siteTitle = (((!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST']);
			$title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core_general_site_title', $this->view->translate('_SITE_TITLE'));
			$logo = $dbObject->query("SELECT params FROM engine4_core_content WHERE page_id = 1 AND name = 'core.menu-logo'")->fetchColumn();
			if($logo && $logo != ''){
				$logoData = json_decode($logo,true);
				if(isset($logoData['logo']))
					$logoRe = '<img src="'.$siteUrl.$logoData['logo'].'" alt="" style="max-height:40px;" />';	
			}
			if(empty($logoRe))
				$logoRe = $siteTitle;
			$contentEmailNotification = 
			'<table width="100%" bgcolor="#f0f0f0" align="center" valign="top" cellspacing="0" cellpadding="0" border="0"><tbody><tr><td><table width="680" align="center">
<tr><td style="padding:0 20px;"><table><tr><td bgcolor="#0186bf" style="padding:10px;"><a href="'.$siteUrl.'">'.$logoRe.'</a></td></tr><tr><td style="font-family: arial,Arial,sans-serif; font-size: 20px; line-height: 24px; font-weight: bold; color: rgb(34, 34, 34); text-decoration: none; padding: 20px 0px 10px;">Check out the latest video from your channel subscriptions for '.date('M d, Y').'.</td></tr>';
									$counter = 1;
								foreach($newVideos as $video){
									if($counter > 5)
										break;
									$counter++;
									$video = Engine_Api::_()->getItem('sesvideo_video', $video);
									$user =  Engine_Api::_()->getItem('user', $video->owner_id);
									if(!$video)
										return;
									$contentEmailNotification .=
									'<tr><td><div style="width:100%;background:#fff;"><div><a href="'.$siteUrl.$video->getHref().'"><img src="'.$siteUrl.$video->getPhotoUrl().'" alt="" align="left" width="100%" /></a></div><div style="padding:15px;clear:both;"><div><a href="'.$video->getHref().'" style="font-family:arial,Arial,sans-serif;font-size:17px;color:#222222;line-height:15px;font-weight:bold;text-decoration:none;">'.$video->getTitle().'</a><span style="display:block; font-family:arial,Arial,sans-serif;color:#999999;font-size:12px;line-height:15px;text-decoration:none;margin-top:5px;">•&nbsp;'.$this->view->translate(array('%s view', '%s views', $video->view_count), $this->view->locale()->toNumber($video->view_count)).'</span></div><div style="clear:both;margin-top:15px;"><table><tr>
<td><img src="'.$chanel->getPhotoUrl().'" width="30" height="30" style="display:block" border="0" /></td><td style="padding-left:10px;"><a href="'.$siteUrl.$chanel->getHref().'" style="font-family:arial,Arial,sans-serif;font-size:12px;color:#222222;line-height:15px;text-decoration:none" target="_blank">'.$chanel->getTitle().'</a></td></tr></table>
</div></div></div></td></tr>';
								}
								$contentEmailNotification .= '<tr><td style="height:30px;"></td></tr><tr><td style="font-family:arial,Arial,sans-serif;font-size:20px;line-height:25px;letter-spacing:0px;font-weight:bold;color:#222222">Recommended <a style="float:right;font-size:15px;font-weight:bold;text-decoration:none;color:#0186bf;" href="">view more</a></td></tr><tr><td style="height:15px;"></td></tr>';
								//recommended Videos
								$recommendedVideos = Engine_Api::_()->getDbtable('videos', 'sesvideo')->getVideo(array('not_video_id',implode(',',$newVideos),'criteria'=>5,'info'=>'view_count','limit_data'=>6),false);
								if(count($recommendedVideos)){
									$contentEmailNotification .= '<tr><td>';
									$i=1;
									foreach($recommendedVideos as $value){
										$user =  Engine_Api::_()->getItem('user', $value->owner_id);
										if($i == 1  || $i == 4){
											$margin = '';	
										}else
											$margin = 'margin-left:5%;';
								$contentEmailNotification .= '<div style="height:200px;width:30%;background:#fff;float:left;margin-bottom:20px;'.$margin.'"><div><img src="'.$siteUrl.$value->getPhotoUrl().'" alt="" align="left" width="100%" height="120" /></div><div style="padding:10px;clear:both;"><div><a href="'.$value->getHref().'" style="font-family:arial,Arial,sans-serif;font-size:13px;color:#222222;line-height:15px;font-weight:bold;text-decoration:none;">'.$value->getTitle().'</a>
<span style="display:block;"><a href="'.$siteUrl.$user->getHref().'" style="font-family:arial,Arial,sans-serif;font-size:12px;color:#999999;line-height:15px;letter-spacing:0px;text-decoration:none" target="_blank">by '.$user->getTitle().'</a>
</span><span style="display:block; font-family:arial,Arial,sans-serif;font-size:12px;color:#999999;line-height:15px;letter-spacing:0px">'.$this->view->translate(array('%s view', '%s views', $value->view_count), $this->view->locale()->toNumber($value->view_count)).'</span></div></div></div>';
								$i++;
						}
						$contentEmailNotification .= '</td></tr>';
				}
			$contentEmailNotification .= '</td></tr> </table></td></tr></tbody></table>';
			if(count($followerChannel)){
				foreach($followerChannel as $follower){
					$userObj = Engine_Api::_()->user()->getUser($follower['owner_id']);
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($userObj->email, 'SESVIDEO_CHANNEL_SUBSCRIPTION_EMAIL', array(
							'content' => $contentEmailNotification,
							'title' => $chanel->title,
            ));
				}
			}
		}
   }
    if (isset($chanel->custom_url))
      return $this->_helper->redirector->gotoRoute(array('chanel_id' => $chanel->custom_url), 'sesvideo_chanel_view', true);
    else
      return $this->_helper->redirector->gotoRoute(array('action' => 'browse'), 'sesvideo_chanel', true);
  }
  protected function setPhoto($photo, $id, $resize = false) {
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
        'parent_type' => 'sesvideo_chanel',
        'parent_id' => $id,
        'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
        'name' => $fileName,
    );
    // Save
    $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
    if ($resize) {
      // Resize image (main)
      $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_cover.' . $extension;
      $image = Engine_Image::factory();
      $image->open($file)
              ->write($mainPath)
              ->destroy();
    } else {
      $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_main.' . $extension;
      $image = Engine_Image::factory();
      $image->open($file)
              ->resize(500, 500)
              ->write($mainPath)
              ->destroy();
    }
    // normal main  image resize
    $normalMainPath = $path . DIRECTORY_SEPARATOR . $base . '_icon.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
            ->resize(100, 100)
            ->write($normalMainPath)
            ->destroy();
    // Store
    try {
      $iMain = $filesTable->createFile($mainPath, $params);
      $iNormalMain = $filesTable->createFile($normalMainPath, $params);
      $iMain->bridge($iNormalMain, 'thumb.icon');
    } catch (Exception $e) {
      // Remove temp files
      @unlink($mainPath);
      @unlink($normalMainPath);
      // Throw
      if ($e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE) {
        throw new Sesvideo_Model_Exception($e->getMessage(), $e->getCode());
      } else {
        throw $e;
      }
    }
    // Remove temp files
    @unlink($mainPath);
    @unlink($normalMainPath);
    // Update row
    // Delete the old file?
    if (!empty($tmpRow)) {
      $tmpRow->delete();
    }
    return $iMain->file_id;
  }

  //chanel photo view page
  function viewAction() {

    $photo_id = $this->_getParam('photo_id', false);
    if (!$photo_id)
      return $this->_forward('requireauth', 'error', 'core');
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->photo = $photo = Engine_Api::_()->getItem('sesvideo_chanelphoto', $photo_id);
    $this->view->chanel = $chanel = $photo->getChanel();
    Engine_Api::_()->core()->setSubject($photo);
    if (!$viewer || !$viewer->getIdentity() || !$chanel->isOwner($viewer)) {
      $photo->view_count = new Zend_Db_Expr('view_count + 1');
      $photo->save();
    }
    if (!$this->_helper->requireAuth()->setAuthParams('sesvideo_chanelphoto', null, 'view')->isValid())
      return;
    $checkchanel = Engine_Api::_()->getItem('sesvideo_chanel', $this->_getParam('chanel_id'));
    if (!($checkchanel instanceof Core_Model_Item_Abstract) || !$checkchanel->getIdentity() || $checkchanel->chanel_id != $photo->chanel_id) {
      $this->_forward('requiresubject', 'error', 'core');
      return;
    }
    // Render
    $this->_helper->content->setEnabled();
  }

  //photo like action
  function likeAction() {
    if (Engine_Api::_()->user()->getViewer()->getIdentity() == 0) {
      echo json_encode(array('status' => 'false', 'error' => 'Login'));
      die;
    }
    $photo_id = $this->_getParam('photo_id');
    if (intval($photo_id) == 0) {
      echo json_encode(array('status' => 'false', 'error' => 'Invalid argument supplied.'));
      die;
    }
    $tableLike = Engine_Api::_()->getDbtable('likes', 'core');
    $tableMainLike = $tableLike->info('name');
    $photo = Engine_Api::_()->getDbtable('chanelphotos', 'sesvideo');
    $select = $tableLike->select()->from($tableMainLike)->where('resource_type =?', 'sesvideo_chanelphoto')->where('poster_id =?', Engine_Api::_()->user()->getViewer()->getIdentity())->where('poster_type =?', 'user')->where('resource_id =?', $photo_id);
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
      $photo->update(array(
          'like_count' => new Zend_Db_Expr('like_count - 1'),
              ), array(
          'chanelphoto_id = ?' => $photo_id,
      ));
      $like_count = Engine_Api::_()->getItem('sesvideo_chanelphoto', $photo_id);
      echo json_encode(array('status' => 'true', 'error' => '', 'condition' => 'reduced', 'like_count' => $like_count->like_count));
      die;
    } else {
      //update
      $db = $tableLike->getAdapter();
      $db->beginTransaction();
      try {
        $like = $tableLike->createRow();
        $like->poster_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $like->resource_type = 'sesvideo_chanelphoto';
        $like->resource_id = $photo_id;
        $like->poster_type = 'user';
        $like->save();

        $photo->update(array(
            'like_count' => new Zend_Db_Expr('like_count + 1'),
                ), array(
            'chanelphoto_id = ?' => $photo_id,
        ));
        // Commit
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      //notification work

      $like_count = Engine_Api::_()->getItem('sesvideo_chanelphoto', $photo_id);
      $viewer = Engine_Api::_()->user()->getViewer();
      $owner = $like_count->getOwner();
      if ($owner->getType() == 'user' && $owner->getIdentity() != $viewer->getIdentity()) {
        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
        $notifyApi->addNotification($owner, $viewer, $like_count, 'liked', array(
            'label' => 'chanel photo'
        ));
      }
      echo json_encode(array('status' => 'true', 'error' => '', 'condition' => 'increment', 'like_count' => $like_count->like_count));
      die;
    }
  }

  //get data when user click on last photo in lightbox (advance lightbox)
  public function lastElementDataAction() {
    //send data is in .tpl
  }

  //get all photo as per view type in light box(advance)
  public function allPhotosAction() {
    $this->view->photo_id = $photo_id = $this->getRequest()->getParam('photo_id', '0');
    $this->view->chanel_id = $album_id = $this->getRequest()->getParam('chanel_id', '0');
    $viewer = Engine_Api::_()->user()->getViewer();
    $params = array();
    // send extra params to view for extra URL parameters
    $this->view->params = $params;
    $page = isset($_POST['page']) ? $_POST['page'] : 1;
    $is_ajax = isset($_POST['is_ajax']) ? $_POST['is_ajax'] : 0;
    $params['paginator'] = true;
    $photo = Engine_Api::_()->getItem('sesvideo_chanelphoto', $photo_id);
    //FETCH photos
    $paginator = $this->view->allPhotos = Engine_Api::_()->getDbTable('chanelphotos', 'sesvideo')->getPhotoCustom($photo, $params, '', true);
    $paginator->setItemCountPerPage(30);
    $this->view->limit = ($page - 1) * 30;
    $this->view->page = $page;
    $this->view->is_ajax = $is_ajax;
    $paginator->setCurrentPageNumber($page);
  }

  //function to open photos in lightbox
  public function imageViewerDetailAction() {
    $this->view->photo_id = $photo_id = $this->getRequest()->getParam('photo_id', '0');
    $this->view->chanel_id = $chanel_id = $this->getRequest()->getParam('chanel_id', '0');
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
		$this->view->params = array();
    // initialize extra next previous params
    $extraParamsNext = $extraParamsPrevious = array();
    if ($this->getRequest()->getParam('limit') != '' && !is_null($this->getRequest()->getParam('limit'))) {
      $extraParamsNext['limit'] = $this->getRequest()->getParam('limit') + 1;
      $extraParamsPrevious['limit'] = $this->getRequest()->getParam('limit') - 1;
    }
    $this->view->extraParamsNext = $extraParamsNext;
    $this->view->extraParamsPrevious = $extraParamsPrevious;
    $this->view->photo = $photo = Engine_Api::_()->getItem('sesvideo_chanelphoto', $photo_id);
    $this->view->chanel = $chanel = Engine_Api::_()->getItem('sesvideo_chanel', $chanel_id);
    if (!$photo->authorization()->isAllowed($viewer, 'view')) {
      $imagePrivateURL = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.private.photo', 1);
      if (!is_file($imagePrivateURL))
        $imagePrivateURL = 'application/modules/Sesalbum/externals/images/private-photo.jpg';
      $this->view->imagePrivateURL = $imagePrivateURL;
    }
    $this->view->canComment = $chanel->authorization()->isAllowed($viewer, 'comment');
    // send extra params to view for extra URL parameters
    // get next photo URL
    $this->view->nextPhoto = Engine_Api::_()->sesvideo()->nextPhoto($photo, $extraParamsNext);
    // get previous photo URL
    $this->view->previousPhoto = Engine_Api::_()->sesvideo()->previousPhoto($photo, $extraParamsPrevious);
    if (!$viewer || !$viewer->getIdentity() || $chanel->owner_id != $viewer->getIdentity()) {
      $photo->view_count = new Zend_Db_Expr('view_count + 1');
      $photo->save();
    }
    $this->view->canEdit = $canEdit = $photo->authorization()->isAllowed($viewer, 'edit');
    $this->view->canDelete = $canDelete = $photo->authorization()->isAllowed($viewer, 'delete');

    $getmodule = Engine_Api::_()->getDbTable('modules', 'core')->getModule('core');
    if (!empty($getmodule->version) && version_compare($getmodule->version, '4.8.6') < 0)
      $this->view->toArray = true;
    else
      $this->view->toArray = false;
    if ($viewer->getIdentity() == 0)
      $level = Engine_Api::_()->getDbtable('levels', 'authorization')->getPublicLevel()->level_id;
    else
      $level = $viewer;
    $type = Engine_Api::_()->authorization()->getPermission($level, 'album', 'imageviewer');
    if ($type == 0)
      $this->renderScript('chanel/image-viewer-detail-basic.tpl');
    else
      $this->renderScript('chanel/image-viewer-detail-advance.tpl');
  }

  public function manageVideosAction() {
    $data = $this->_getParam('data', false);
    $is_chanel = $this->_getParam('is_chanel', false);
    $chanel_id = $this->_getParam('chanel_id', false);
    $viewer = Engine_Api::_()->user()->getViewer();
    if ($data || $is_chanel) {
      $value['criteria'] = $data;
      if ($is_chanel) {
        $this->view->identityWidget = 'editChanel';
        $this->view->editChanel = true;
        $chanel = Engine_Api::_()->getItem('sesvideo_chanel', $chanel_id);
        $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('chanelvideos', 'sesvideo')->getChanelAssociateVideos($chanel, array('paginator' => 'true'));
        $this->view->countItem = count($paginator);
      } else {
				$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $value['info'] = 'recently_created';
        if ($data == 'my_created') {
          $value['user_id'] = $viewer->getIdentity();
          $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('videos', 'sesvideo')->getVideo($value);
          $this->view->typeSearch = $view->translate('My Created videos');
        } else if ($data == 'watch_later') {
					 $this->view->getVideoWatch = 'getVideoWatch';
          $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('watchlaters', 'sesvideo')->getWatchlaterItems($value);
          $this->view->typeSearch = $view->translate('Watch Later videos');
        } else if ($data == 'liked_videos') {
          $this->view->getVideoItem = 'getVideoItem';
          $this->view->paginator = $paginator = Engine_Api::_()->sesvideo()->getLikesContents(array('resource_type' => 'video'));
          $this->view->typeSearch = $view->translate('Liked videos');
        } else if ($data == 'rated_videos') {
          $this->view->getVideoItem = 'getVideoItem';
          $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('ratings', 'sesvideo')->getRatedItems(array('resource_type' => 'video'));
          $this->view->typeSearch = $view->translate('Rated videos');
        } else {
          echo 'error';
          die;
        }
        $this->view->identityWidget = 'addChanel';
        $this->view->is_ajax = $is_ajax = isset($_POST['is_ajax']) ? true : false;
        $this->view->data = isset($_POST['data']) ? $_POST['data'] : 'my_created';
        $this->view->page = $page = isset($_POST['page']) ? $_POST['page'] : 1;
        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 21));
        $paginator->setCurrentPageNumber($page);
        $this->view->countItem = $paginator->getTotalItemCount();
      }
    }
  }

  public function chanelDataAction() {
    $chanel_id = $this->_getParam('chanel_id', false);
    if ($chanel_id) {
      if (isset($_POST['params']))
        $params = json_decode($_POST['params'], true);
      $this->view->category_limit = $category_limit = isset($params['category_limit']) ? $params['category_limit'] : $this->_getParam('category_limit', '10');
      $this->view->video_limit = $video_limit = isset($params['video_limit']) ? $params['video_limit'] : $this->_getParam('video_limit', '8');
      $this->view->chanel_limit = $chanel_limit = isset($params['chanel_limit']) ? $params['chanel_limit'] : $this->_getParam('chanel_limit', '8');
      $this->view->count_chanel = $count_chanel = isset($params['count_chanel']) ? $params['count_chanel'] : $this->_getParam('count_chanel', '1');
      $this->view->width = $width = isset($params['width']) ? $params['width'] : $this->_getParam('width', '120');
      $this->view->height = $height = isset($params['height']) ? $params['height'] : $this->_getParam('height', '80');
      $this->view->seemore_text = $seemore_text = isset($params['seemore_text']) ? $params['seemore_text'] : $this->_getParam('seemore_text', '+ See all [category_name]');
      $this->view->allignment_seeall = $allignment_seeall = isset($params['allignment_seeall']) ? $params['allignment_seeall'] : $this->_getParam('allignment_seeall', 'left');
      $this->view->title_truncation = $title_truncation = isset($params['title_truncation']) ? $params['title_truncation'] : $this->_getParam('title_truncation', '100');
      $this->view->description_truncation = $description_truncation = isset($params['description_truncation']) ? $params['description_truncation'] : $this->_getParam('description_truncation', '150');
      $show_criterias = isset($params['show_criterias']) ? $params['show_criterias'] : $this->_getParam('show_criteria', array('by', 'view', 'title', 'follow', 'followButton', 'featuredLabel', 'sponsoredLabel', 'description', 'chanelPhoto', 'chanelVideo', 'chanelThumbnail','rating'));
      foreach ($show_criterias as $show_criteria)
        $this->view->{$show_criteria . 'Active'} = $show_criteria;
      $resultArray = array();
      $chanelDatas = $resultArray['chanel_data'] = Engine_Api::_()->getDbTable('chanels', 'sesvideo')->getChanels(array('chanel_id' => $chanel_id), false);
      if (in_array('chanelVideo', $show_criterias)) {
        foreach ($chanelDatas as $chanelData) {
          $resultArray['videos'] = Engine_Api::_()->getDbTable('chanelvideos', 'sesvideo')->getChanelAssociateVideos($chanelData, array('limit_data' => $video_limit, 'paginator' => false));
        }
      }
      $this->view->resultArray = $resultArray;
    } else {
      $this->_forward('requireauth', 'error', 'core');
    }
  }

  public function checkurlAction() {
    $data = $this->_getParam('data', false);
    $chanel_id = $this->_getParam('chanel_id', false);
    $return = 0;
    $httpConfig = (!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://";
    $setting = Engine_Api::_()->getApi('settings', 'core');
    $url = $httpConfig . $_SERVER['HTTP_HOST'] . '/' . $setting->getSetting('video.videos.manifest', 'videos') . '/' . $setting->getSetting('video.chanel.manifest', 'chanels') . '/' . $data;
    if ($data) {
      //if (!preg_match('/[^A-Za-z0-9]/', $data)) {
        $paginator = Engine_Api::_()->getDbtable('chanels', 'sesvideo')->checkUrl($data, $chanel_id);
        $slugExists = $paginator->getTotalItemCount();
        if ($slugExists <= 0)
          $return = 1;
        else
          $return = 0;
      //} else {
        //$return = 0;
      //}
    } else {
      $return = 1;
    }
    echo $return;
    die;
  }

  public function followAction() {

    if (!$this->_helper->requireUser->isValid())
      return;

    $chanelId = $this->_getParam('chanel_id');
    $userId = Engine_Api::_()->user()->getViewer()->getIdentity();

    if ($userId == 0)
      return '';
		$chanel = Engine_Api::_()->getItem('sesvideo_chanel', $chanelId);
    $checkFollow = Engine_Api::_()->getDbtable('chanelfollows', 'sesvideo')->checkFollow($userId, $chanelId);
    if ($checkFollow == 0) {

      $chanelFollow = Engine_Api::_()->getDbtable('chanelfollows', 'sesvideo')->createRow();
      $chanelFollow->chanel_id = $chanelId;
      $chanelFollow->owner_id = $userId;
      $chanelFollow->creation_date = 'NOW()';
      $chanelFollow->save();

      $viewer = Engine_Api::_()->user()->getViewer();
      
      Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('type =?' => 'sesvideo_chanel_follow', "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $chanel->getType(), "object_id = ?" => $chanel->getIdentity()));

      $owner = $chanel->getOwner();
      if ($chanel->owner_id != $viewer->getIdentity()) {
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner, $viewer, $chanel, 'sesvideo_chanel_follow');
      }

      $result = Engine_Api::_()->getDbtable('actions', 'activity')->fetchRow(array('type =?' => 'sesvideo_chanel_follow', "subject_id =?" => $viewer->getIdentity(), "object_type =? " => $chanel->getType(), "object_id = ?" => $chanel->getIdentity()));
      if (!$result) {
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $chanel, 'sesvideo_chanel_follow');
        if ($action)
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $chanel);
      }
			$chanel->follow_count = $chanel->follow_count + 1;
			$chanel->save();
			echo json_encode(array('status' => 'true', 'error' => '', 'condition' => 'increment', 'count' => $chanel->follow_count));die;
      die;
    } else {
      $chanelFollow = Engine_Api::_()->getDbtable('chanelfollows', 'sesvideo')->delete(array('chanel_id =?' => $chanelId, 'owner_id =?' => $userId));
			$chanel->follow_count = $chanel->follow_count - 1;
			$chanel->save();
			echo json_encode(array('status' => 'true', 'error' => '', 'condition' => 'reduced', 'count' => $chanel->follow_count));die;
      die;
    }
  }

  public function followChanelAction() {

    if (!$this->_helper->requireUser->isValid())
      return;
    $chanelId = $this->_getParam('chanel_id');
    $userId = $this->_getParam('user_id');
    $checkFollow = Engine_Api::_()->getDbtable('chanelfollows', 'sesvideo')->checkFollow($userId, $chanelId);
    if (!empty($checkFollow))
      return false;
    $chanelFollow = Engine_Api::_()->getDbtable('chanelfollows', 'sesvideo')->createRow();
    $chanelFollow->chanel_id = $chanelId;
    $chanelFollow->owner_id = $userId;
    $chanelFollow->creation_date = 'NOW()';
    $chanelFollow->save();
  }

  public function deleteFollowChanelAction() {
    if (!$this->_helper->requireUser->isValid())
      return;
    $chanelID = $this->_getParam('chanel_id');
    $userID = $this->_getParam('user_id');
    $chanelFollow = Engine_Api::_()->getDbtable('chanelfollows', 'sesvideo')->delete(array('chanel_id =?' => $chanelID, 'owner_id =?' => $userID));
  }

}
