<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PhotoController.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_PhotoController extends Seaocore_Controller_Action_Standard {

    //COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
    public function init() {

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, "view")->isValid())
            return;

        //SET SUBJECT
        if (!Engine_Api::_()->core()->hasSubject()) {

            if (0 != ($photo_id = (int) $this->_getParam('photo_id')) &&
                    null != ($photo = Engine_Api::_()->getItem('siteevent_photo', $photo_id))) {
                Engine_Api::_()->core()->setSubject($photo);
            } else if (0 != ($event_id = (int) $this->_getParam('event_id')) &&
                    null != ($siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id))) {
                Engine_Api::_()->core()->setSubject($siteevent);
            }
        }

        $this->_helper->requireUser->addActionRequires(array(
            'upload',
            'upload-photo',
            'edit',
        ));

        $this->_helper->requireSubject->setActionRequireTypes(array(
            'siteevent' => 'siteevent_event',
            'upload' => 'siteevent_event',
            'view' => 'siteevent_photo',
            'edit' => 'siteevent_photo',
        ));
    }

    //ACTION FOR UPLOAD PHOTO
    public function uploadAction() {

     if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        //GET EVENT
        $this->view->event_id = $event_id = $this->_getParam('event_id');

        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        $this->view->can_edit = $siteevent->authorization()->isAllowed($viewer, "edit");

        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("siteevent_main");       

        if (Engine_Api::_()->siteevent()->hasPackageEnable()) {
          $photoCount = Engine_Api::_()->getItem('siteeventpaid_package', $siteevent->package_id)->photo_count;
          $paginator = $siteevent->getSingletonAlbum()->getCollectiblesPaginator();
          if (Engine_Api::_()->siteeventpaid()->allowPackageContent($siteevent->package_id, "photo")) {
            $this->view->allowed_upload_photo = 1;
            if (empty($photoCount))
              $this->view->allowed_upload_photo = 1;
            elseif ($photoCount <= $paginator->getTotalItemCount())
              $this->view->allowed_upload_photo = 0;
          } else {
            $this->view->allowed_upload_photo = 0;
          }
        } else {//AUTHORIZATION CHECK
          $this->view->allowed_upload_photo = $allowed_upload_photo = Engine_Api::_()->authorization()->isAllowed($siteevent, $viewer, "photo");
        }
        
        if (!$this->getRequest()->isPost()) {
          if (empty($this->view->allowed_upload_photo)) {
              return $this->_forwardCustom('requireauth', 'error', 'core');
          }
        }

        //GET SETTINGS
        $this->view->allowed_upload_video = Engine_Api::_()->siteevent()->allowVideo($siteevent, $viewer);

        //SELECTED TAB
        $this->view->TabActive = "photo";

        if (isset($_GET['ul']) || isset($_FILES['Filedata'])) {
            return $this->_forwardCustom('upload-photo', null, null, array('format' => 'json', 'event_id' => (int) $siteevent->getIdentity()));
        }

        //GET ALBUM
        $album = $siteevent->getSingletonAlbum();

        //MAKE FORM
        $this->view->form = $form = new Siteevent_Form_Photo_Upload();
        
          if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
      Zend_Registry::set('setFixedCreationForm', true);
      Zend_Registry::set('setFixedCreationHeaderTitle', str_replace(' New ', ' ', 'Add Photo'));
      Zend_Registry::set('setFixedCreationHeaderSubmit', 'Save');
      $this->view->form->setAttrib('id', 'photo-upload');
      Zend_Registry::set('setFixedCreationFormId', '#photo-upload');
      $this->view->form->removeElement('submit');
      $form->setTitle('');
    }

        $form->file->setAttrib('data', array('event_id' => $siteevent->getIdentity()));
        $this->view->tab_id = $content_id = $this->_getParam('content_id');
        //CHECK METHOD
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //PROCESS
        $table = Engine_Api::_()->getItemTable('siteevent_photo');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {

            $values = $form->getValues();
            $params = array(
                'event_id' => $siteevent->getIdentity(),
                'user_id' => $viewer->getIdentity(),
            );

            //ADD ACTION AND ATTACHMENTS
            $count = count($values['file']);
            $api = Engine_Api::_()->getDbtable('actions', 'seaocore');
            $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $siteevent, Engine_Api::_()->siteevent()->getActivtyFeedType($siteevent, 'siteevent_photo_upload'), null, array('count' => count($values['file']), 'title' => $siteevent->title));

            $count = 0;

            foreach ($values['file'] as $photo_id) {
                $photo = Engine_Api::_()->getItem("siteevent_photo", $photo_id);

                if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity())
                    continue;

                $photo->collection_id = $album->album_id;
                $photo->album_id = $album->album_id;
                $photo->save();

                if ($siteevent->photo_id == 0) {
                    $siteevent->photo_id = $photo->file_id;
                    $siteevent->save();
                }

                if ($action instanceof Activity_Model_Action && $count < 8) {
                    $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
                }
                $count++;
            }

            if ($action) {
                //START NOTIFICATION AND EMAIL WORK
                Engine_Api::_()->siteevent()->sendNotificationEmail($siteevent, $action, 'siteevent_photo_upload', 'SITEEVENT_PHOTO_UPLOADNOTIFICATION_EMAIL', null, null, 'created', $photo);
                $isChildIdLeader = Engine_Api::_()->getDbtable('listItems', 'siteevent')->checkLeader($siteevent);
                if (!empty($isChildIdLeader)) {
                    Engine_Api::_()->siteevent()->sendNotificationToFollowers($siteevent, 'siteevent_photo_upload');
                }
                //END NOTIFICATION AND EMAIL WORK
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        if ($this->view->can_edit) {
            return $this->_gotoRouteCustom(array('action' => 'editphotos', 'event_id' => $album->event_id), "siteevent_albumspecific", true);
        } else {
            return $this->_gotoRouteCustom(array('event_id' => $album->event_id, 'slug' => $siteevent->getSlug(), 'tab' => $content_id), "siteevent_entry_view", true);
        }
     }else{
        //CLEAR CACHE ON FORM DISPLAY, ALL FIELDS SHOULD BE EMPTY.(FOR SITEMOBILE)
        $this->view->clear_cache = true;
        $this->view->noDomCache = true; 
                
        //GET VIEWER
			  $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        //GET EVENT
        $this->view->event_id = $event_id = $this->_getParam('event_id');

        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        $this->view->can_edit = $siteevent->authorization()->isAllowed($viewer, "edit");
        $this->view->tab_selected_id = $content_id = $this->_getParam('content_id');
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("siteevent_main");

        //AUTHORIZATION CHECK
        $this->view->allowed_upload_photo = Engine_Api::_()->authorization()->isAllowed($siteevent, $viewer, "photo");

			if (empty($this->view->allowed_upload_photo)) {
				return $this->_forwardCustom('requireauth', 'error', 'core');
			}
			//GET ALBUM
			$album = $siteevent->getSingletonAlbum();
      $set_cover = true;
			//MAKE FORM
			$this->view->form = $form = new Siteevent_Form_Photo_Upload();
			$form->file->setAttrib('data', array('event_id' => $siteevent->getIdentity()));
                        
			//IF NOT POST OR FORM NOT VALID, RETURN
			if (!$this->getRequest()->isPost()) {
				return;
			}

			//IF NOT POST OR FORM NOT VALID, RETURN
			if (!$form->isValid($this->getRequest()->getPost())) {
				return;
			}

			//CHECK MAX FILE SIZE
			if (!$this->_helper->requireUser()->checkRequire()) {
				$this->view->status = false;
				$this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
				return;
			}

			//IF NOT POST OR FORM NOT VALID, RETURN
			if (!$this->getRequest()->isPost()) {
				$this->view->status = false;
				$this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
				return;
			}

			//FORM VALUES
			$values = $this->getRequest()->getPost();
			if (empty($values)) {
				return;
			}
		  //PROCESS
			$tablePhoto = Engine_Api::_()->getDbtable('photos', 'siteevent');
			$db = $tablePhoto->getAdapter();
			$db->beginTransaction();

			//COUNT NO. OF PHOTOS (CHECK ATLEAST SINGLE PHOTO UPLOAD).
			$count = 0;
			foreach ($_FILES['Filedata']['name'] as $data) {
				if (!empty($data)) {
					$count = 1;
					break;
				}
			}

			try {

				if (!isset($_FILES['Filedata']) || !isset($_FILES['Filedata']['name']) || $count == 0) {
					$this->view->status = false;
					$form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid Upload'));
					return;
				}

				$values['file'] = array();
				foreach ($_FILES['Filedata']['name'] as $key => $uploadFile) {
					$viewer = Engine_Api::_()->user()->getViewer();
					$album = $siteevent->getSingletonAlbum();
					$rows = $tablePhoto->fetchRow($tablePhoto->select()->from($tablePhoto->info('name'), 'order')->order('order DESC')->limit(1));
					$order = 0;
					if (!empty($rows)) {
						$order = $rows->order + 1;
					}
					$params = array(
							'collection_id' => $album->getIdentity(),
							'album_id' => $album->getIdentity(),
							'event_id' => $siteevent->getIdentity(),
							'user_id' => $viewer->getIdentity(),
							'order' => $order
					);

					$file = array('name' => $_FILES['Filedata']['name'][$key], 'tmp_name' => $_FILES['Filedata']['tmp_name'][$key], 'type' => $_FILES['Filedata']['type'][$key], 'size' => $_FILES['Filedata']['size'][$key], 'error' => $_FILES['Filedata']['error'][$key]);

					if (!is_uploaded_file($file['tmp_name'])) {
						continue;
					}

					$photo = Engine_Api::_()->siteevent()->createPhoto($params, $file);
					$photo_id = $photo->photo_id;
					if (!$siteevent->photo_id) {
						$siteevent->photo_id = $photo_id;
						$siteevent->save();
					}
					$this->view->status = true;
					$this->view->name = $_FILES['Filedata']['name'];
					$this->view->photo_id = $photo_id;
					$values['file'][] = $photo->photo_id;
					$db->commit();
					$order++;
				}
				$api = Engine_Api::_()->getDbtable('actions', 'activity');
				$action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $siteevent, 'siteevent_photo_upload', null, array('count' => count($values['file']), 'title' => $siteevent->title));
				$count = 0;
				foreach ($values['file'] as $photo_id) {
					$photo = Engine_Api::_()->getItem("siteevent_photo", $photo_id);

					if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity())
						continue;

					$photo->collection_id = $album->album_id;
					$photo->album_id = $album->album_id;
					$photo->save();

					if ($siteevent->photo_id == 0) {
						$siteevent->photo_id = $photo->file_id;
						$siteevent->save();
					}

					if ($action instanceof Activity_Model_Action && $count < 8) {
						$api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
					}
					$count++;
				}
			} catch (Exception $e) {
				$db->rollBack();
				$this->view->status = false;
				$this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
				return;
			}
      return $this->_gotoRouteCustom(array('event_id' => $album->event_id, 'slug' => $siteevent->getSlug(), 'tab' => $content_id), "siteevent_entry_view", true);
     }
    }

    //ACTION FOR UPLOAD PHOTO
    public function uploadPhotoAction() {

        //GET SITEEVENT
        $siteevent = Engine_Api::_()->getItem('siteevent_event', (int) $this->_getParam('event_id'));

        if (!$this->_helper->requireUser()->checkRequire()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
            return;
        }
      
    //AUTHORIZATION CHECK
    $allowed_upload_photo = 1;
    if (Engine_Api::_()->siteevent()->hasPackageEnable()) {
      $photoCount = Engine_Api::_()->getItem('siteeventpaid_package', $siteevent->package_id)->photo_count;
      $paginator = $siteevent->getSingletonAlbum()->getCollectiblesPaginator();

      if (Engine_Api::_()->siteeventpaid()->allowPackageContent($siteevent->package_id, "photo")) {
          
        if(empty($photoCount)) {
            $allowed_upload_photo = 1;
        }  
        elseif ($photoCount <= $paginator->getTotalItemCount())
          $allowed_upload_photo = 0;
      } else
        $allowed_upload_photo = 0;
    }

    if (empty($allowed_upload_photo)) {
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Maximum photo upload limit has been exceeded.');
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
        $tablePhoto = Engine_Api::_()->getDbtable('photos', 'siteevent');
        $db = $tablePhoto->getAdapter();
        $db->beginTransaction();

        try {
            $viewer = Engine_Api::_()->user()->getViewer();
            $album = $siteevent->getSingletonAlbum();
            $rows = $tablePhoto->fetchRow($tablePhoto->select()->from($tablePhoto->info('name'), 'order')->order('order DESC')->limit(1));
            $order = 0;
            if (!empty($rows)) {
                $order = $rows->order + 1;
            }
            $params = array(
                'collection_id' => $album->getIdentity(),
                'album_id' => $album->getIdentity(),
                'event_id' => $siteevent->getIdentity(),
                'user_id' => $viewer->getIdentity(),
                'order' => $order
            );
            $photo_id = Engine_Api::_()->siteevent()->createPhoto($params, $_FILES['Filedata'])->photo_id;

            if (!$siteevent->photo_id) {
                $siteevent->photo_id = $photo_id;
                $siteevent->save();
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
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer->getIdentity();

        //AUTHORIZATION CHECK
        $canEdit = $photo->canEdit();
        if (!$canEdit) {
            return;
        }

        //MAKE FORM
        $this->view->form = $form = new Siteevent_Form_Photo_Edit();

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
        $db = Engine_Api::_()->getDbtable('photos', 'siteevent')->getAdapter();
        $db->beginTransaction();

        try {
            $photo->setFromArray($form->getValues())->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_forwardCustom('success', 'utility', 'core', array(
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
        $photo = Engine_Api::_()->getItem('siteevent_photo', $photo_id);

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer->getIdentity();

        //AUTHORIZATION CHECK
        $canEdit = $photo->canEdit();
        if (!$canEdit) {
            return;
        }

        //GET EVENT
        $siteevent = $photo->getParent('siteevent_event');

        $isajax = (int) $this->_getParam('isajax');
        if ($isajax) {
            $db = Engine_Api::_()->getDbTable('photos', 'siteevent')->getAdapter();
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
        $this->view->form = $form = new Siteevent_Form_Photo_Delete();

        //CHECK METHOD
        if (!$this->getRequest()->isPost()) {
            $form->populate($photo->toArray());
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $db = Engine_Api::_()->getDbTable('photos', 'siteevent')->getAdapter();
        $db->beginTransaction();

        try {
            $photo->delete();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_forwardCustom('success', 'utility', 'core', array(
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Photo deleted')),
                    'layout' => 'default-simple',
                    'parentRedirect' => $siteevent->getHref(),
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

        //GET SITEEVENT DETAILS
        $this->view->siteevent = $photo->getCollection();

        //GET SETTINGS
        $this->view->canEdit = $photo->canEdit();

        if (!$viewer || !$viewer_id || $photo->user_id != $viewer->getIdentity()) {
            $photo->view_count = new Zend_Db_Expr('view_count + 1');
            $photo->save();
        }

        $this->view->enablePinit = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photo.pinit', 0);
    }

}
