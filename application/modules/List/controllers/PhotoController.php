<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: PhotoController.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_PhotoController extends Core_Controller_Action_Standard {

	//COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
  public function init() {

   if (!$this->_helper->requireAuth()->setAuthParams('list_listing', null, 'view')->isValid())
      return;

		//SET SUBJECT
    if (!Engine_Api::_()->core()->hasSubject()) {

      if (0 != ($photo_id = (int) $this->_getParam('photo_id')) &&
          null != ($photo = Engine_Api::_()->getItem('list_photo', $photo_id))) {
        Engine_Api::_()->core()->setSubject($photo);
      } else if (0 != ($listing_id = (int) $this->_getParam('listing_id')) &&
          null != ($list = Engine_Api::_()->getItem('list_listing', $listing_id))) {
        Engine_Api::_()->core()->setSubject($list);
      }
    }

    $this->_helper->requireUser->addActionRequires(array(
            'upload',
            'upload-photo',
            'edit',
    ));

    $this->_helper->requireSubject->setActionRequireTypes(array(
            'list' => 'list_listing',
            'upload' => 'list_listing',
            'view' => 'list_photo',
            'edit' => 'list_photo',
    ));
  }

	//ACTION FOR UPLOAD PHOTO
  public function uploadAction() {

		//GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

		//GET LISTING
    $this->view->listing_id = $listing_id = $this->_getParam('listing_id');
    $this->view->list = $list = Engine_Api::_()->getItem('list_listing', $listing_id);

		//AUTHORIZATION CHECK
		$this->view->allowed_upload_photo = Engine_Api::_()->authorization()->isAllowed($list, $viewer, 'photo');
    if (empty($this->view->allowed_upload_photo)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    //OVERVIEW IS ALLOWED OR NOT
		$this->view->allow_overview_of_owner = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'list_listing', 'overview');
		//GET SETTINGS
		$this->view->allowed_upload_video = Engine_Api::_()->list()->allowVideo($list, $viewer);

		//SELECTED TAB
		$this->view->TabActive = "photo";

    if (isset($_GET['ul']) || isset($_FILES['Filedata'])) {
      return $this->_forward('upload-photo', null, null, array('format' => 'json', 'listing_id' => (int) $list->getIdentity()));
		}

		//GET ALBUM
    $album = $list->getSingletonAlbum();

		//MAKE FORM
    $this->view->form = $form = new List_Form_Photo_Upload();
    $form->file->setAttrib('data', array('listing_id' => $list->getIdentity()));

		//CHECK METHOD
    if (!$this->getRequest()->isPost()) {
      return;
    }

		//FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //PROCESS
    $table = Engine_Api::_()->getItemTable('list_photo');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {

      $values = $form->getValues();
      $params = array(
              'listing_id' => $list->getIdentity(),
              'user_id' => $viewer->getIdentity(),
      );

			//ADD ACTION AND ATTACHMENTS
      $count = count($values['file']);
      $api = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $list, 'list_photo_upload', null, array('count' => count($values['file']), 'title' => $list->title));

      $count = 0;

      foreach ($values['file'] as $photo_id) {
        $photo = Engine_Api::_()->getItem("list_photo", $photo_id);

        if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity())
          continue;

        $photo->collection_id = $album->album_id;
        $photo->album_id = $album->album_id;
        $photo->save();

        if ($list->photo_id == 0) {
          $list->photo_id = $photo->file_id;
          $list->save();
        }

        if ($action instanceof Activity_Model_Action && $count < 8) {
          $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
        }
        $count++;
      }

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    if ($list->owner_id == $viewer_id) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'editphotos', 'listing_id' => $album->listing_id), 'list_albumspecific', true);
    } else {
      $content_id = $this->_getParam('content_id');
      return $this->_helper->redirector->gotoRoute(array('listing_id' => $album->listing_id, 'user_id' => $list->owner_id, 'slug' => $list->getSlug(), 'tab' => $content_id), 'list_entry_view', true);
    }
  }

	//ACTION FOR UPLOAD PHOTO
  public function uploadPhotoAction() {

		//GET LIST
    $list = Engine_Api::_()->getItem('list_listing', (int) $this->_getParam('listing_id'));

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
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
      return;
    }

    $db = Engine_Api::_()->getDbtable('photos', 'list')->getAdapter();
    $db->beginTransaction();

    try {
      $viewer = Engine_Api::_()->user()->getViewer();
      $album = $list->getSingletonAlbum();

      $params = array(
              'collection_id' => $album->getIdentity(),
              'album_id' => $album->getIdentity(),
              'listing_id' => $list->getIdentity(),
              'user_id' => $viewer->getIdentity(),
      );
      $photo_id = Engine_Api::_()->list()->createPhoto($params, $_FILES['Filedata'])->photo_id;

      if (!$list->photo_id) {
        $list->photo_id = $photo_id;
        $list->save();
      }

      $this->view->status = true;
      $this->view->name = $_FILES['Filedata']['name'];
      $this->view->photo_id = $photo_id;
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
      return;
    }
  }

  //ACTION FOR EDITING OF PHOTOS TITLE AND DISCRIPTION
  public function editAction() {

		//GET PHOTO SUBJECT
    $photo = Engine_Api::_()->core()->getSubject();

		//GET VIEWER
    $this->view->viewer_id = $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

		//AUTHORIZATION CHECK
    $this->view->canEdit = $photo->authorization()->isAllowed(null, 'edit');
    if (empty($this->view->canEdit) && $photo->user_id == $viewer_id) {
      $this->view->canEdit = 1;
		}

    if (empty($this->view->canEdit)) {
      return $this->_forward('requireauth', 'error', 'core');
		}

		//MAKE FORM
    $this->view->form = $form = new List_Form_Photo_Edit();

		//CHECK METHOD
    if (!$this->getRequest()->isPost()) {
      $form->populate($photo->toArray());
      return;
    }

		//FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //PROCESS
    $db = Engine_Api::_()->getDbtable('photos', 'list')->getAdapter();
    $db->beginTransaction();

    try {
      $photo->setFromArray($form->getValues())->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'core', array(
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Changes saved')),
            'layout' => 'default-simple',
            'parentRefresh' => true,
            'closeSmoothbox' => true,
    ));
  }

  //ACTION FOR PHOTO DELETE
  public function removeAction() {

		//GET PHOTO ID AND ITEM
    $photo_id = (int) $this->_getParam('photo_id');
    $photo = Engine_Api::_()->getItem('list_photo', $photo_id);

		//GET LISTING
    $list = $photo->getParent('list_listing');

    $isajax = (int) $this->_getParam('isajax');
    if ($isajax) {
      $db = Engine_Api::_()->getDbTable('photos', 'list')->getAdapter();
      $db->beginTransaction();

      try {
        $photo->delete();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }

		//MAKE FORM
    $this->view->form = $form = new List_Form_Photo_Delete();

		//CHECK METHOD
    if (!$this->getRequest()->isPost()) {
      $form->populate($photo->toArray());
      return;
    }

		//FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $db = Engine_Api::_()->getDbTable('photos', 'list')->getAdapter();
    $db->beginTransaction();

    try {
      $photo->delete();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'core', array(
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Photo deleted')),
            'layout' => 'default-simple',
            'parentRedirect' => $list->getHref(),
            'closeSmoothbox' => true,
    ));
  }

  //ACTION FOR VIEWING THE PHOTO
  public function viewAction() {

		//GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    //GET PHOTOS
    $this->view->image = $photo = Engine_Api::_()->core()->getSubject();

    $album = Engine_Api::_()->getItem('list_album', $photo->album_id);

    //GET LIST DETAILS
    $tablePhoto = Engine_Api::_()->getDbtable('photos', 'list');
    $select = $album->getCollectiblesSelect();
    $this->view->list = $tablePhoto->fetchAll($select);

    //GET LIST DETAILS
    //$this->view->list = $photo->getCollection();

		//GET SETTINGS
    $this->view->canEdit = $photo->authorization()->isAllowed(null, 'edit');
    if (empty($this->view->canEdit) && $photo->user_id == $viewer_id) {
      $this->view->canEdit = 1;
		}

    $this->view->canDelete = $photo->authorization()->isAllowed(null, 'delete');
    if (empty($this->view->canDelete) && $photo->user_id == $viewer_id) {
      $this->view->canDelete = 1;
		}

		$this->view->report = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('list.report', 1);
		$this->view->share = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('list.share', 1);
    $this->view->enablePinit = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photo.pinit', 0);
  }

}