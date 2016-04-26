<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_ProfileController extends Seaocore_Controller_Action_Standard {

  //ACTION FOR SENDING A MESSGE TO GROUP OWNER
  public function messageOwnerAction() {

    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET VIEWER DETAIL
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GET GROUP ID AND GROUP OBJECT
    $group_id = $this->_getParam("group_id");
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

    //GROUP OWNER CAN'T SEND MESSAGE TO HIMSELF
    if ($viewer_id == $sitegroup->owner_id) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //FORM GENERATION
    $this->view->form = $form = new Messages_Form_Compose();
    $form->setTitle('Contact Group Owner');
    $form->setDescription('Create your message with the form given below. Your message will be sent to the admins of this Group.');
    $form->removeElement('to');

    //GET ADMINS ID FOR SENDING MESSAGE
    $manageAdminData = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->getManageAdmin($group_id);
    $manageAdminData = $manageAdminData->toArray();
    $ids = '';
    if (!empty($manageAdminData)) {
      foreach ($manageAdminData as $key => $user_ids) {
        $user_id = $user_ids['user_id'];
        if ($viewer_id != $user_id) {
          $ids = $ids . $user_id . ',';
        }
      }
    }
    $ids = trim($ids, ',');
    $form->toValues->setValue($ids);

    if (!$this->getRequest()->isPost()) {
      return;
    }

    $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
    $db->beginTransaction();

    try {
      $values = $this->getRequest()->getPost();

      $form->populate($values);

      $is_error = 0;
      if (empty($values['title'])) {
        $is_error = 1;
      }

      //SENDING MESSAGE
      if ($is_error == 1) {
        $error = $this->view->translate('Subject is required field !');
        $error = Zend_Registry::get('Zend_Translate')->_($error);

        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error);
        return;
      }

      $recipients = preg_split('/[,. ]+/', $values['toValues']);

      //LIMIT RECIPIENTS IF IT IS NOT A SPECIAL LIST OF MEMBERS
      $recipients = array_slice($recipients, 0, 1000);

      //CLEAN THE RECIPIENTS FOR REPEATING IDS
      //THIS CAN HAPPEN IF RECIPIENTS IS SELECTED AND THEN A FRIEND LIST IS SELECTED
      $recipients = array_unique($recipients);

      $recipientsUsers = Engine_Api::_()->getItemMulti('user', $recipients);

      $sitegroup_title = $sitegroup->title;
      $group_title_with_link = '<a href = http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('group_url' => Engine_Api::_()->sitegroup()->getGroupUrl($group_id)), 'sitegroup_entry_view') . ">$sitegroup_title</a>";

      $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send(
              $viewer, $recipients, $values['title'], $values['body'] . "<br><br>" . $this->view->translate("This message corresponds to the Group:") . $group_title_with_link
      );

      foreach ($recipientsUsers as $user) {
        if ($user->getIdentity() == $viewer->getIdentity()) {
          continue;
        }
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
                $user, $viewer, $conversation, 'message_new'
        );
      }

      //INCREMENT MESSAGES COUNTER
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

      $db->commit();

      return $this->_forwardCustom('success', 'utility', 'core', array(
                  'smoothboxClose' => true,
                  'parentRefresh' => false,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.'))
              ));
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  //ACTION FOR TELL TO THE FRIEND FOR THIS GROUP
  public function tellAFriendAction() {

    //DEFAULT LAYOUT
    $sitemobile = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemobile');
    if ($sitemobile && !Engine_Api::_()->sitemobile()->checkMode('mobile-mode'))
			$this->_helper->layout->setLayout('default-simple');

    //GET VIEWER DETAIL
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewr_id = $viewer->getIdentity();

    //GET GROUP ID AND GROUP OBJECT
    $group_id = $this->_getParam('group_id', $this->_getParam('id', null));
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    if (empty($sitegroup))
      return $this->_forwardCustom('notfound', 'error', 'core');
    //AUTHORIZATION CHECK FOR TELL A FRIEND
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'tfriend');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
 
     if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
      Zend_Registry::set('setFixedCreationForm', true);
      Zend_Registry::set('setFixedCreationHeaderTitle', str_replace(' New ', ' ', $form->getTitle()));
      Zend_Registry::set('setFixedCreationHeaderSubmit', 'Send');
      $this->view->form->setAttrib('id', 'form_sitegroup_tellAFriend');
      Zend_Registry::set('setFixedCreationFormId', '#form_sitegroup_tellAFriend');
			$this->view->form->removeElement('sitegroup_send');
      $this->view->form->removeElement('sitegroup_cancel');
      $this->view->form->removeDisplayGroup('sitegroup_buttons');
      $form->setTitle('');
    }
    
    //FORM GENERATION
    $this->view->form = $form = new Sitegroup_Form_TellAFriend();

    if (!empty($viewr_id)) {
      $value['sender_email'] = $viewer->email;
      $value['sender_name'] = $viewer->displayname;
      $form->populate($value);
    }

    
    //IF THE MODE IS APP MODE THEN
    if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
      Zend_Registry::set('setFixedCreationForm', true);
      Zend_Registry::set('setFixedCreationFormBack', 'Back');
      Zend_Registry::set('setFixedCreationHeaderTitle', Zend_Registry::get('Zend_Translate')->_('Tell a friend'));
      Zend_Registry::set('setFixedCreationHeaderSubmit', Zend_Registry::get('Zend_Translate')->_('Send'));
      $this->view->form->setAttrib('id', 'tellAFriendFrom');
      Zend_Registry::set('setFixedCreationFormId', '#tellAFriendFrom');
      $this->view->form->removeElement('sitegroup_send');
      $this->view->form->removeElement('sitegroup_cancel');
      $form->setTitle('');
    }
    
   
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      $values = $form->getValues();

      //EDPLODES EMAIL IDS
      $reciver_ids = explode(',', $values['sitegroup_reciver_emails']);

      if (!empty($values['sitegroup_send_me'])) {
        $reciver_ids[] = $values['sitegroup_sender_email'];
      }
      $sender_email = $values['sitegroup_sender_email'];

      //CHECK VALID EMAIL ID FORMITE
      $validator = new Zend_Validate_EmailAddress();
      $validator->getHostnameValidator()->setValidateTld(false);

      if (!$validator->isValid($sender_email)) {
        $form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid sender email address value'));
        return;
        }
      $sender = $values['sitegroup_sender_name'];
      $message = $values['sitegroup_message'];
      $heading = ucfirst($sitegroup->getTitle());
      foreach ($reciver_ids as $reciver_id) {
        $reciver_id = trim($reciver_id, ' ');
        if (!$validator->isValid($reciver_id)) {
          $form->addError(Zend_Registry::get('Zend_Translate')->_('Please enter correct email address of the receiver(s).'));
          return;
        }
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($reciver_id, 'SITEGROUP_TELLAFRIEND_EMAIL', array(
          'host' => $_SERVER['HTTP_HOST'],
          'sender_name' => $sender,
          'group_title' => $heading,
          'message' => '<div>' . $message . '</div>',
          'object_link' => 'http://' . $_SERVER['HTTP_HOST'] . Engine_Api::_()->sitegroup()->getHref($sitegroup->group_id, $sitegroup->owner_id, $sitegroup->getSlug()),
          'sender_email' => $sender_email,
          'queue' => false
         ));
        
      }


      if ($sitemobile && Engine_Api::_()->sitemobile()->checkMode('mobile-mode'))
				$this->_forwardCustom('success', 'utility', 'core', array(          
          'parentRedirect' => $sitegroup->getHref(),          
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message to your friend has been sent successfully.'))
      ));
		  else	

      $this->_forwardCustom('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRefresh' => false,
          'format' => 'smoothbox',
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message to your friend has been sent successfully.'))
      ));
    }
  }

  //ACTION FOR PRINTING THE GROUP
  public function printAction() {
    $this->_helper->layout->setLayout('default-simple');
    //GET GROUP ID AND GROUP OBJECT
    $this->view->group_id = $group_id = $this->_getParam('group_id', $this->_getParam('id', null));
    $this->view->sitegroup = $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    if (empty($sitegroup))
      return $this->_forwardCustom('notfound', 'error', 'core');
    //AUTHORIZATION CHECK FOR PRINTING
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'print');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    if ($sitegroup->category_id != 0)
      $this->view->category = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategory($sitegroup->category_id);

    if ($sitegroup->subcategory_id != 0)
      $this->view->subcategory = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategory($sitegroup->subcategory_id);

    if ($sitegroup->subsubcategory_id != 0)
      $this->view->subsubcategory = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategory($sitegroup->subsubcategory_id);
    
    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Sitegroup/View/Helper', 'Sitegroup_View_Helper');
     $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($sitegroup);
  }

  //ACTION FOR WRITE BOX AT VIEW PROFILE GROUP
  public function displayAction() {

    //GET THE TEXT STRING
    $text = $this->_getParam('text_string');

    //GET GROUP ID
    $group_id = $this->_getParam('group_id');

    $writesTable = Engine_Api::_()->getDbtable('writes', 'sitegroup')->setWriteContent($group_id, $text);
    exit();
  }

  public function contactDetailAction() {

    //GET GROUP ID
    $group_id = $this->_getParam("group_id");

    //GET SITEGROUP ITEM    
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

    //GET PHONE
    $phone = $this->_getParam('phone');

    //GET EMAIL
    $email = $this->_getParam('email');

    //GET WEBSITE
    $website = $this->_getParam('website');

    //SAVE DETAILS
    $sitegroup->phone = $phone;
    $sitegroup->email = $email;
    $sitegroup->website = $website;
    $sitegroup->save();
  }

  public function getCoverPhotoAction() {

    //GET GROUP ID
    $group_id = $this->_getParam("group_id");
    $this->view->sitegroup = $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    //START MANAGE-ADMIN CHECK
    $this->view->can_edit = $can_edit = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    $onlyMemberWithPhoto = $this->_getParam("onlyMemberWithPhoto", 1);
    if (empty($sitegroup->group_cover)) {
      $this->view->show_member = $show_member = $this->_getParam("show_member", 0);
      if ($show_member) {
        $this->view->members = $members = Engine_Api::_()->getDbtable('membership', 'sitegroup')->getJoinMembers($sitegroup->group_id, null,null,$onlyMemberWithPhoto);
        $this->view->membersCount = $members->getTotalItemCount();
        $this->view->membersCountView = $this->_getParam("memberCount", 8);
      }
      return;
    }
    $tableAlbum = Engine_Api::_()->getDbtable('albums', 'sitegroup');
    $album = $tableAlbum->getSpecialAlbum($sitegroup, 'cover');
    //$otherinfo = Engine_Api::_()->getDbtable('otherinfo', 'sitegroup')->getOtherinfo($group_id);
    $this->view->photo = $photo = Engine_Api::_()->getItem('sitegroup_photo', $sitegroup->group_cover);

    $this->view->coverTop = 0;
    $this->view->coverLeft = 0;
    if ($album->cover_params && isset($album->cover_params['top'])) {
      $this->view->coverTop = $album->cover_params['top'];
    }
  }
  
  //ACTION FOR Email Me FOR THIS GROUP
  public function emailMeAction() {

    //DEFAULT LAYOUT
    $this->_helper->layout->setLayout('default-simple');

    //GET VIEWER DETAIL
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewr_id = $viewer->getIdentity();

    //GET GROUP ID AND GROUP OBJECT
    $this->view->group_id  = $group_id = $this->_getParam('group_id', $this->_getParam('id', null));
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    if (empty($sitegroup))
      return $this->_forwardCustom('notfound', 'error', 'core');
      
    //AUTHORIZATION CHECK FOR TELL A FRIEND
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'tfriend');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //FORM GENERATION
    $this->view->form = $form = new Sitegroup_Form_EmailMe();

    if (!empty($viewr_id)) {
      $value['sender_email'] = $viewer->email;
      $value['sender_name'] = $viewer->displayname;
      $form->populate($value);
    }

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      $values = $form->getValues();

      //EDPLODES EMAIL IDS
      $reciver_ids = $sitegroup->email; //explode(',', $values['sitegroup_reciver_emails']);
      $values['sitegroup_sender_email'] = $sitegroup->email;
      if (!empty($values['sitegroup_send_me'])) {
        $reciver_ids = $values['sitegroup_sender_email'];
      }
      $sender_email = $values['sitegroup_sender_email'];

      //CHECK VALID EMAIL ID FORMITE
      $validator = new Zend_Validate_EmailAddress();
      $validator->getHostnameValidator()->setValidateTld(false);

      if (!$validator->isValid($sender_email)) {
        $form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid sender email address value'));
        return;
      }
//       foreach ($reciver_ids as $reciver_id) {
//         $reciver_id = trim($reciver_id, ' ');
//         if (!$validator->isValid($reciver_id)) {
//           $form->addError(Zend_Registry::get('Zend_Translate')->_('Please enter correct email address of the receiver(s).'));
//           return;
//         }
//       }
      $sender = $values['sitegroup_sender_name'];
      $message = $values['sitegroup_message'];
      $heading = ucfirst($sitegroup->getTitle());
      Engine_Api::_()->getApi('mail', 'core')->sendSystem($reciver_ids, 'SITEGROUP_EMAILME_EMAIL', array(
          'host' => $_SERVER['HTTP_HOST'],
          'sender_name' => $sender,
          'group_title' => $heading,
          'message' => '<div>' . $message . '</div>',
          'object_link' => 'http://' . $_SERVER['HTTP_HOST'] . Engine_Api::_()->sitegroup()->getHref($sitegroup->group_id, $sitegroup->owner_id, $sitegroup->getSlug()),
          'sender_email' => $sender_email,
          'queue' => true
      ));

      $this->_forwardCustom('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRefresh' => false,
          'format' => 'smoothbox',
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message to group owner has been sent successfully.'))
      ));
    }
  }

  public function uploadCoverPhotoAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //LAYOUT
    $this->_helper->layout->setLayout('default-simple');
    if (!$this->_helper->requireUser()->checkRequire()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }

    //GROUP ID
    $group_id = $this->_getParam('group_id');

    $special = $this->_getParam('special', 'cover');
    $this->view->sitegroup = $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    //CHECK FORM VALIDATION
    $file='';
    $notNeedToCreate=false;
    $photo_id = $this->_getParam('photo_id');
    if ($photo_id) {
      $photo = Engine_Api::_()->getItem('sitegroup_photo', $photo_id);
      $album = Engine_Api::_()->getItem('sitegroup_album', $photo->album_id);
      if ($album && $album->type == 'cover') {
        $notNeedToCreate = true;
      }
      if ($photo->file_id && !$notNeedToCreate)
        $file = Engine_Api::_()->getItemTable('storage_file')->getFile($photo->file_id);
    }


		//PROCESS
		$db = Engine_Db_Table::getDefaultAdapter();
		$db->beginTransaction();
		try {
			//CREATE PHOTO
			$tablePhoto = Engine_Api::_()->getDbtable('photos', 'sitegroup');
			if (!$notNeedToCreate) {
				$photo = $tablePhoto->createRow();
				$photo->setFromArray(array(
						'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
						'group_id' => $group_id
				));
				$photo->save();
				if ($file) {
					$photo->setPhoto($file);
				} else {
					$photo->setPhoto($_FILES['Filedata'],true);
				}


				$tableAlbum = Engine_Api::_()->getDbtable('albums', 'sitegroup');
				$album = $tableAlbum->getSpecialAlbum($sitegroup, $special);

				$tablePhotoName = $tablePhoto->info('name');
				$photoSelect = $tablePhoto->select()->from($tablePhotoName, 'order')->where('album_id = ?', $album->album_id)->order('order DESC')->limit(1);
				$photo_rowinfo = $tablePhoto->fetchRow($photoSelect);
				$photo->collection_id = $album->album_id;
				$photo->album_id = $album->album_id;
				$order = 0;
				if (!empty($photo_rowinfo)) {
					$order = $photo_rowinfo->order + 1;
				}
				$photo->order = $order;
				$photo->save();
			}

			$album->cover_params = $this->_getParam('position', array('top' => '0', 'left' => 0));
			$album->save();
			if (!$album->photo_id) {
				$album->photo_id = $photo->file_id;
				$album->save();
			}
			$sitegroup->group_cover = $photo->photo_id;
			$sitegroup->save();
			//ADD ACTIVITY
			$viewer = Engine_Api::_()->user()->getViewer();
			$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
			$activityFeedType = null;
			if (Engine_Api::_()->sitegroup()->isGroupOwner($sitegroup) && Engine_Api::_()->sitegroup()->isFeedTypeGroupEnable())
				$activityFeedType = 'sitegroup_admin_cover_update';
			elseif ($sitegroup->all_post || Engine_Api::_()->sitegroup()->isGroupOwner($sitegroup))
				$activityFeedType = 'sitegroup_cover_update';


			if ($activityFeedType) {
				$action = $activityApi->addActivity($viewer, $sitegroup, $activityFeedType);
			}
			if ($action) {
				Engine_Api::_()->getApi('subCore', 'sitegroup')->deleteFeedStream($action);
				if ($photo)
					Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $photo);
			}

			$this->view->status = true;
			$db->commit();
      return $this->_redirectCustom($sitegroup->getHref());
		} catch (Exception $e) {
			$db->rollBack();
			$this->view->status = false;
			$this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
			return;
		}
  }

  public function removeCoverPhotoAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    $group_id = $this->_getParam('group_id');
    if ($this->getRequest()->isPost()) {
      $special = $this->_getParam('special', 'cover');
      $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
      $sitegroup->group_cover = 0;
      $sitegroup->save();
      $tableAlbum = Engine_Api::_()->getDbtable('albums', 'sitegroup');
      $album = $tableAlbum->getSpecialAlbum($sitegroup, $special);
      $album->cover_params = array('top' => '0', 'left' => 0);
      $album->save();
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
      ));
    }
  }

  public function getAlbumsPhotosAction() {
    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;
    $sitegroupalbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum');
    if (!$sitegroupalbumEnabled) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    //GET GROUP ID
    $group_id = $this->_getParam("group_id");
    $this->view->sitegroup = $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    ////START MANAGE-ADMIN CHECK
    $this->view->can_edit = $can_edit = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($can_edit))
      return;
    //FETCH ALBUMS
    $this->view->recentAdded = $recentAdded = $this->_getParam("recent", false);
    $this->view->album_id = $album_id = $this->_getParam("album_id");
    if ($album_id) {
      $this->view->album = $album = Engine_Api::_()->getItem('sitegroup_album', $album_id);
      $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
      $paginator->setItemCountPerPage(10000);
    } elseif ($recentAdded) {
      $paginator = Engine_Api::_()->getDbtable('photos', 'sitegroup')->getPhotos(array('group_id' => $group_id, 'orderby' => 'photo_id DESC', 'start' => 0, 'end' => 100));
    } else {
      $paramsAlbum['group_id'] = $group_id;
      $paginator = Engine_Api::_()->getDbtable('albums', 'sitegroup')->getAlbums($paramsAlbum);
    }
    $this->view->paginator = $paginator;
  }

}