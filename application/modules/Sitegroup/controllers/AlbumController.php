<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AlbumController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_AlbumController extends Seaocore_Controller_Action_Standard {

  public function init() {

    //HERE WE CHECKING THE SITEGROUP ALBUM IS ENABLED OR NOT
    $sitegroupalbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum');
    if (!$sitegroupalbumEnabled) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext
            ->addActionContext('rate', 'json')
            ->addActionContext('validation', 'html')
            ->initContext();
    $group_id = $this->_getParam('group_id', $this->_getParam('id', null));

    //PACKAGE BASE PRIYACY START
    if (!empty($group_id)) {
      $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
      if ($sitegroup) {
        Engine_Api::_()->core()->setSubject($sitegroup);      
        if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
          if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupalbum")) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
          }
        } else {
          $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'spcreate');
          if (empty($isGroupOwnerAllow)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
          }
        }
      }
    }
    //PACKAGE BASE PRIYACY END
    else {
      if (Engine_Api::_()->core()->hasSubject() != null) {
        $photo = Engine_Api::_()->core()->getSubject();
        $album = $photo->getCollection();
        $group_id = $album->group_id;
      }
    }
  }

  //ACTION FOR EDIT THE ALBUM
  public function editAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //CHECK REQUERIED SUBJECT IS THERE OR NOT
    if (!$this->_helper->requireSubject('sitegroup_group')->isValid())
      return;

    //GET ALBUM ID
    $album_id = $this->_getParam('album_id');

    //GET GROUP ID
    $group_id = $this->_getParam('group_id');

    //GET SITEGROUP ITEM
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    $ownerList = $sitegroup->getGroupOwnerList();

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //GET ALBUM ITEM
    $album = Engine_Api::_()->getItem('sitegroup_album', $album_id);
    if (!Engine_Api::_()->core()->hasSubject('sitegroup_group')) {
      Engine_Api::_()->core()->setSubject($album);
    }

    //MAKE FORM
    $this->view->form = $form = new Sitegroup_Form_Album_Edit();

    //START PHOTO PRIVACY WORK
    $sitegroupalbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum');
    if ($sitegroupalbumEnabled) {
      $auth = Engine_Api::_()->authorization()->context;
     // $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
      	$sitegroupmemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
			if (!empty($sitegroupmemberEnabled)) {
				$roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
			} else {
				$roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 	'registered', 'everyone');
			}
//       foreach ($roles as $role) {
//         if ($form->auth_tag) {
//           if (1 == $auth->isAllowed($album, $role, 'tag')) {
//             $form->auth_tag->setValue($role);
//           }
//         }
//       }

			foreach ($roles as $roleString) {
				$role = $roleString;
				if ($role === 'like_member') {
					$role = $ownerList;
				}
				if ($form->auth_tag) {
					if (1 == $auth->isAllowed($album, $role, 'tag')) {
						$form->auth_tag->setValue($roleString);
					}
				}
			}
    }
    //END PHOTO PRIVACY WORK
    //COMMENT PRIVACY
    $auth = Engine_Api::_()->authorization()->context;
    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
    $commentMax = array_search("everyone", $roles);
    foreach ($roles as $i => $role) {
      $auth->setAllowed($album, $role, 'comment', ($i <= $commentMax));
    }
    //END PHOTO PRIVACY	WORK
    //CHECK FORM VALIDATION
    if (!$this->getRequest()->isPost()) {
      $form->populate($album->toArray());
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    //CHECK FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    //PROCESS
    $db = $album->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      //GET FORM VALUES
      $values = $form->getValues();
      $album->setFromArray($values);
      $album->save();

      //CREATE AUTH STUFF HERE
      $auth = Engine_Api::_()->authorization()->context;
      //$roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
      $sitegroupmemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
			if (!empty($sitegroupmemberEnabled)) {
				$roles = array('owner', 'member', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
			} else {
				$roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 	'registered', 'everyone');
			}

      //REBUILD PRIVACY
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach ($actionTable->getActionsByObject($album) as $action) {
        $actionTable->resetActivityBindings($action);
      }

      //START TAG PRIVACY
      if (empty($values['auth_tag'])) {
        $values['auth_tag'] = key($form->auth_tag->options);
        if (empty($values['auth_tag'])) {
          $values['auth_tag'] = 'registered';
        }
      }
      $tagMax = array_search($values['auth_tag'], $roles);
			foreach ($roles as $i => $role) {
				if ($role === 'like_member') {
					$role = $ownerList;
				}
				$auth->setAllowed($album, $role, 'tag', ($i <= $tagMax));
			}

//       foreach ($roles as $i => $role) {
//         $auth->setAllowed($album, $role, 'tag', ($i <= $tagMax));
//       }
      //END TAG PRIVACY
      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    return $this->_forwardCustom('success', 'utility', 'core', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Changes saved.')),
        'smoothboxClose' => 300,
        'parentRefresh' => 300,
    ));
  }

  //ACTION FOR VIEW THE ALBUM
  public function viewAction() {

   //GET GROUP ID
    $group_id = $this->_getParam('group_id');

    $album_id = $this->_getParam('album_id');
    $album = Engine_Api::_()->getItem('sitegroup_album', $album_id);
    if (!$album) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    
    //GET SITEGROUP ITEM
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'view');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    
    //NAVIGATION WORK FOR FOOTER.(DO NOT DISPLAY NAVIGATION IN FOOTER ON VIEW PAGE.)
    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
         if(!Zend_Registry::isRegistered('sitemobileNavigationName')){
         Zend_Registry::set('sitemobileNavigationName','setNoRender');
         }
    }
    
    if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
      Zend_Registry::set('setFixedCreationFormBack', 'Back');
     }
    //CHECK THE VERSION OF THE CORE MODULE
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled()
      ;
    }

  }

  //ACTION FOR DELETE THE ALBUM
  public function deleteAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //CHECK REQUERIED SUBJECT IS THERE OR NOT
    if (!$this->_helper->requireSubject('sitegroup_group')->isValid())
      return;

    //GET GROUP ID
    $group_id = $this->_getParam('group_id', null);

    //GET SITEGROUP ITEM
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //GET ALBUM ID
    $album_id = $this->_getParam('album_id', $this->_getParam('album_id', null));

    //GET ALBUM ITEM
    $album = Engine_Api::_()->getItem('sitegroup_album', $album_id);

    //GET DELETE FORM
    $this->view->form = $form = new Sitegroup_Form_Album_Delete();

    //CHECK ALBUM EXIST OR NOT TO DELETE
    if (!$album) {
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Album doesn't exist or not authorized to delete.");
      return;
    }

    //CHECK FORM VALIDATION
    if (!$this->getRequest()->isPost()) {
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    //GET DB
    $db = $album->getTable()->getAdapter();
    $db->beginTransaction();
    try {

      //DELETE ALBUM
      $album->delete();

      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 2,
        'parentRedirect' => $this->_helper->url->url(array('group_url' => Engine_Api::_()->sitegroup()->getGroupUrl($group_id), 'tab' => $this->_getParam('tab')), 'sitegroup_entry_view'),
        'parentRedirectTime' => '2',
        'format' => 'smoothbox',
        'messages' => Zend_Registry::get('Zend_Translate')->_("Album has been deleted.")
    ));
  }

  //ACTION FOR EDIT PHOTOS TO THE ALBUM
  public function editPhotosAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //CHECK REQUERIED SUBJECT IS THERE OR NOT
    if (!$this->_helper->requireSubject('sitegroup_group')->isValid())
      return;

    //GET SITEGROUP ITEM
    $this->view->sitegroup = $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');

    //SEND TAB ID TO THE TPL
    $this->view->tab_selected_id = $this->_getParam('tab');

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //GET ALBUM ID
    $album_id = $this->view->album_id = $this->_getParam('album_id');

    //GET GROUP ID
    $group_id = $this->view->group_id = $this->_getParam('group_id');

    //GET REQUEST ISAJAX OR NOT
    $isajax = $this->_getParam('is_ajax');

    //GET ITEM ALBUM
    $this->view->album = $album = $sitegroup->getSingletonAlbum($album_id);

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitegroup_main');

    //SEND CURRENT GROUP NUMBER TO THE TPL
    $this->view->currentGroupNumbers = $currentGroupNumbers = $this->_getParam('groups', 1);

    //SEND PHOTOS PER GROUP TO THE TPL
    $this->view->photos_per_group = $photos_per_group = 20;

    //SET GROUP PHOTO PARAMS
    $paramsPhoto = array();
    $paramsPhoto['group_id'] = $group_id;
    $paramsPhoto['album_id'] = $album_id;
    $paramsPhoto['order'] = 'order ASC';
    $paramsPhoto['viewGroup'] = 1;

    //GET TOTAL PHOTOS
    $total_photo = Engine_Api::_()->getDbtable('photos', 'sitegroup')->getPhotosCount($paramsPhoto);
    
    $group_vars = Engine_Api::_()->sitegroup()->makeGroup($total_photo, $photos_per_group, $currentGroupNumbers);
    $group_array = Array();
    for ($x = 0; $x <= $group_vars[2] - 1; $x++) {
      if ($x + 1 == $group_vars[1]) {
        $link = "1";
      } else {
        $link = "0";
      }
      $group_array[$x] = Array('group' => $x + 1,
          'link' => $link);
    }
    $this->view->grouparray = $group_array;
    $this->view->maxgroup = $group_vars[2];
    $this->view->pstart = 1;
    $this->view->total_images = $total_photo;

    //SET LIMIT PARAMS
    $paramsPhoto['start'] = $photos_per_group;
    $paramsPhoto['end'] = $group_vars[0];
    $paramsPhoto['viewGroup'] = 1;

    //GETTING THE PHOTOS ACCORDING TO LIMIT
    $this->view->photos = Engine_Api::_()->getDbtable('photos', 'sitegroup')->getPhotos($paramsPhoto);

    //MAKE EDIT PHOTOS FORM
    $this->view->form = $form = new Sitegroup_Form_Album_Photos();
    foreach ($this->view->photos as $photo) {
      $subform = new Sitegroup_Form_Photo_Edit(array('elementsBelongTo' => $photo->getGuid()));
      $subform->populate($photo->toArray());
      $form->addSubForm($subform, $photo->getGuid());
      $form->cover->addMultiOption($photo->file_id, $photo->file_id);

//      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
//				$form->group_cover->addMultiOption($photo->file_id, $photo->file_id);
//      }
    }

    //CHECK FORM VALIDATION
    if (!$this->getRequest()->isPost()) {
      return;
    }

    //CHECK FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      //return;
    }

    //GET DB
    $db = $album->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      //GET FORM VALUES
      $values = $form->getValues();
      if (!empty($values['cover'])) {
        $album->photo_id = $values['cover'];
        $album->save();
      }

//			if (isset($values['group_cover'])) {
//				Engine_Api::_()->getDbtable('groups', 'sitegroup')->update(array('group_cover' => $values['group_cover']), array('group_id =?' => $group_id));
//			}

      //PROCESS
      foreach ($this->view->photos as $photo) { 
        $subform = $form->getSubForm($photo->getGuid());
        $values = $subform->getValues();
        $values = $values[$photo->getGuid()];

        //UNSET TEH PHOTO ID
        unset($values['photo_id']);

        if (isset($values['delete']) && $values['delete'] == '1') {
          $photo->delete();

          //FETCHING ALL PHOTOS
          $count = Engine_Api::_()->getDbtable('photos', 'sitegroup')->getPhotosCount($paramsPhoto);
          if (empty($count)) {
            Engine_Api::_()->getItemTable('sitegroup_album')->update(array('photo_id' => 0,), array('group_id =?' => $group_id, 'album_id =?' => $album_id));
          }
        } else { 
          $photo->setFromArray($values);
          $photo->save();
        }
      }
      

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    if (!$isajax) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'view', 'group_id' => $group_id, 'album_id' => $album_id, 'slug' => $album->getSlug(), 'tab' => $this->view->tab_selected_id), 'sitegroup_albumphoto_general', true);
    }
  }

  public function viewAlbumAction() {

    //GET GROUP ID
    $group_id = $this->_getParam('group_id', null);
    
    //SET SITEGROUP ITEM
    $this->view->sitegroup = $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');

    //SEND TAB ID TO THE TPL
    $this->view->tab_selected_id = $this->_getParam('tab');

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'view');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //SET ALBUMS PARAMS
    $paramsAlbum = array();
    $paramsAlbum['group_id'] = $group_id;
    $albums_order = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupalbum.albumsorder', 1);
    if($albums_order) {
			$paramsAlbum['orderby'] = 'album_id DESC';
    } else {
      $paramsAlbum['orderby'] = 'album_id ASC';
    }

    //FETCH ALBUMS
    $this->view->album = Engine_Api::_()->getDbtable('albums', 'sitegroup')->getAlbums($paramsAlbum);
  }

  //ACTION FOR CHANGE THE ORDER OF THE PHOTOS
  public function orderAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET ALBUM ID
    $album_id = $this->_getParam('album_id');

    //GET GROUP ID
    $group_id = $this->_getParam('group_id');

    //GET ORDER
    $order = $this->_getParam('order');
    if (!$order) {
      $this->view->status = false;
      return;
    }

    //GET CURRENT ORDER
    $currentOrder = Engine_Api::_()->getDbtable('photos', 'sitegroup')->getGroupPhotosOrder($album_id, $group_id);

    //FIND THE STARTING POINT?
    $start = null;
    $end = null;
    for ($i = 0, $l = count($currentOrder); $i < $l; $i++) {
      if (in_array($currentOrder[$i], $order)) {
        $start = $i;
        $end = $i + count($order);
        break;
      }
    }

    if (null === $start || null === $end) {
      $this->view->status = false;
      return;
    }

    for ($i = 0, $l = count($currentOrder); $i < $l; $i++) {
      if ($i >= $start && $i <= $end) {
        $photo_id = $order[$i - $start];
      } else {
        $photo_id = $currentOrder[$i];
      }
      Engine_Api::_()->getItemTable('sitegroup_photo')->update(array('order' => $i), array('photo_id = ?' => $photo_id));
    }
    $this->view->status = true;
  }

  //ACTION FOR CHANGE THE ORDER OF THE ALBUMS
  public function albumOrderAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET GROUP ID
    $group_id = $this->_getParam('group_id');

    //GET ORDER
    $order = $this->_getParam('order');
    if (!$order) {
      $this->view->status = false;
      return;
    }

    //GET CURRENT ORDER OF ALBUM
    $currentOrder = Engine_Api::_()->getDbtable('albums', 'sitegroup')->getGroupAlbumsOrder($group_id);

    //FIND THE STARTING POINT?
    $start = null;
    $end = null;
    for ($i = 0, $l = count($currentOrder); $i < $l; $i++) {
      if (in_array($currentOrder[$i], $order)) {
        $start = $i;
        $end = $i + count($order);
        break;
      }
    }

    if (null === $start || null === $end) {
      $this->view->status = false;
      return;
    }

    for ($i = 0, $l = count($currentOrder); $i < $l; $i++) {
      if ($i >= $start && $i <= $end) {
        $photo_id = $order[$i - $start];
      } else {
        $photo_id = $currentOrder[$i];
      }
      Engine_Api::_()->getItemTable('sitegroup_album')->update(array('order' => $i), array('photo_id = ?' => $photo_id));
    }
    $this->view->status = true;
  }

  public function composeUploadAction() {
    if (!Engine_Api::_()->user()->getViewer()->getIdentity()) {
      $this->_redirect('login');
      return;
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid method');
      return;
    }

    if (empty($_FILES['Filedata'])) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Get album
    $viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbtable('albums', 'sitegroup');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $type = $this->_getParam('type', 'wall');

      if (empty($type))
        $type = 'wall';
      $group_id = $this->_getParam('group_id', $this->_getParam('id', null));

      //PACKAGE BASE PRIYACY START
      $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

      $album = $table->getSpecialAlbum($sitegroup, $type);

      $photoTable = Engine_Api::_()->getDbtable('photos', 'sitegroup');
      $photo = $photoTable->createRow();
      $photo->setFromArray(array(
          'group_id' => $group_id,
          'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity()
      ));
      $photo->save();
      $photo->setPhoto($_FILES['Filedata']);

      if ($type == 'message') {
        $photo->title = Zend_Registry::get('Zend_Translate')->_('Attached Image');
      }

      $photo->album_id = $album->album_id;
      $photo->collection_id = $album->album_id;
      $photo->save();

      if (!$album->photo_id) {
        $album->photo_id = $photo->file_id;
        $album->save();
      }

      if ($type != 'message') {
        // Authorizations
        $auth = Engine_Api::_()->authorization()->context;
        $auth->setAllowed($photo, 'everyone', 'view', true);
        $auth->setAllowed($photo, 'everyone', 'comment', true);
      }

      $db->commit();

      $this->view->status = true;
      $this->view->photo_id = $photo->photo_id;
      $this->view->album_id = $album->album_id;
      $this->view->src = $photo->getPhotoUrl();
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Photo saved successfully');
      

      $requesttype = $this->_getParam('feedphoto', false);
      if ($requesttype) {
      	echo '<img src="'. $photo->getPhotoUrl() . '" id="compose-photo-preview-image" class="compose-preview-image"><div id="advfeed-photo"><input type="hidden" name="attachment[photo_id]" value="'.$photo->photo_id.'"><input type="hidden" name="attachment[type]" value="sitegroupphoto"></div>';
      	exit();
      }
    } catch (Exception $e) {
      $db->rollBack();
      //throw $e;
      $this->view->status = false;
    }
  }

  public function browseAction() {
 
    //CHECK VIEW PRIVACY
    if (!$this->_helper->requireAuth()->setAuthParams('sitegroup_group', null, 'view')->isValid())
      return;

     //CHECK THE VERSION OF THE CORE MODULE
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else { 
      $this->_helper->content
              ->setNoRender()
              ->setEnabled()
      ;
    }

  }

  public function homeAction() {
 
    //CHECK VIEW PRIVACY
    if (!$this->_helper->requireAuth()->setAuthParams('sitegroup_group', null, 'view')->isValid())
      return;

     //CHECK THE VERSION OF THE CORE MODULE
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else { 
      $this->_helper->content
              ->setNoRender()
              ->setEnabled()
      ;
    }

  }

  public function featuredAction() {
    
    $this->view->album = $album = Engine_Api::_()->getItem('sitegroup_album', $this->_getParam('album_id', $this->_getParam('album_id', null)));
    $album->featured = !$album->featured;
    $album->save();
    exit(0);
  }

  //ACTION FOR ADDING ALBUM OF THE DAY
  public function addAlbumOfDayAction() {
    //FORM GENERATION
    $form = $this->view->form = new Sitegroupalbum_Form_ItemOfDayday();
    $album_id = $this->_getParam('album_id');
   // $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    //CHECK POST
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //GET FORM VALUES
      $values = $form->getValues();

      //BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        //GET ITEM OF THE DAY TABLE
        $dayItemTime = Engine_Api::_()->getDbtable('itemofthedays', 'sitegroup');

				//FETCH RESULT FOR resource_id
        $select = $dayItemTime->select()->where('resource_id = ?', $album_id)->where('resource_type = ?', 'sitegroup_album');
        $row = $dayItemTime->fetchRow($select);

        if (empty($row)) {
          $row = $dayItemTime->createRow();
          $row->resource_id = $album_id;
        }
        $row->start_date = $values["starttime"];
        $row->end_date = $values["endtime"];
				$row->resource_type = 'sitegroup_album';
        $row->save();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_forwardCustom('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  //'parentRefresh' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('The Album of the Day has been added successfully.'))
              ));
    }
  }

}
?>