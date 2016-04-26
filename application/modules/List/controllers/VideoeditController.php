<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: VideoeditController.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_VideoeditController extends Core_Controller_Action_Standard {

	//COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
  public function init() {

		//LOGGED IN USER CAN EDIT OR DELETE VIDEO
    if (!$this->_helper->requireUser()->isValid())
      return;

		//AUTHORIZATION CHECK
    if (!$this->_helper->requireAuth()->setAuthParams('list_listing', null, 'view')->isValid())
      return;

		//SET SUBJECT
    $listing_id = $this->_getParam('listing_id', $this->_getParam('listing_id', null));
    if ($listing_id) {
      $list = Engine_Api::_()->getItem('list_listing', $listing_id);
      if ($list) {
        Engine_Api::_()->core()->setSubject($list);
      }
    }

    //LIST SUBJECT SHOULD BE SET
    if (!$this->_helper->requireSubject()->isValid()) {
      return;
    }
		
  }

	//ACTION FOR EDIT THE VIDEO
  public function editAction() {

		//GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

		//GET LIST SUBJECT
    $this->view->list = $list = Engine_Api::_()->core()->getSubject();

    //AUTHORIZATION CHECK
    if (!$this->_helper->requireAuth()->setAuthParams($list, $viewer, 'edit')->isValid()) {
      return;
    }
    
		//AUTHORIZATION CHECK
    if ($list->owner_id != $viewer_id) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    //AUTHORIZATION CHECK
    $this->view->allowed_upload_video = Engine_Api::_()->list()->allowVideo($list, $viewer);    
    if (empty($this->view->allowed_upload_video)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

		//GET PACKAGE ID
		$this->view->package_id = $this->_getParam('package_id');

    //OVERVIEW IS ALLOWED OR NOT
		$this->view->allow_overview_of_owner = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'list_listing', 'overview');

		//SELECTED TAB
    $this->view->TabActive = "video";

		//GET LEVEL SETTING
    $this->view->allowed_upload_photo = Engine_Api::_()->authorization()->isAllowed($list, $viewer, 'photo');    

		//GET VIDEOS
    $this->view->videos = $videos = array();
    if (Engine_Api::_()->list()->enableVideoPlugin()) {
      $this->view->videos = $videos = Engine_Api::_()->getItemTable('list_clasfvideo', 'list')->getListingVideos($list->listing_id, 0);
    }
    $this->view->count = count($videos);

    //MAKE FORM
    $this->view->form = $form = new List_Form_Video_Editvideo();

    foreach ($videos as $video) {

      $subform = new List_Form_Video_Edit(array('elementsBelongTo' => $video->getGuid()));

      if ($video->status != 1) {
        if ($video->status == 0 || $video->status == 2):
          $msg = $this->view->translate("Your video is currently being processed - you will be notified when it is ready to be viewed.");
        elseif ($video->status == 3):
          $msg = $this->view->translate("Video conversion failed. Please try again.");
        elseif ($video->status == 4):
          $msg = $this->view->translate("Video conversion failed. Video format is not supported by FFMPEG. Please try again.");
        elseif ($video->status == 5):
          $msg = $this->view->translate("Video conversion failed. Audio files are not supported. Please try again.");
        elseif ($video->status == 7):
          $msg = $this->view->translate("Video conversion failed. You may be over the site upload limit.  Try  a smaller file, or delete some files to free up space.");
        endif;

        $subform->addElement('dummy', 'mssg' . $video->video_id, array(
                'description' => $msg,
                'decorators' => array(
                        'ViewHelper',
                        array('HtmlTag', array('tag' => 'div', 'class' => 'tip')),
                        array('Description', array('tag' => 'span', 'placement' => 'APPEND')),
                        array('Description', array('placement' => 'APPEND')),
                ),
        ));
        $t = 'mssg' . $video->video_id;
        $subform->$t->getDecorator("Description")->setOption("placement", "append");
      }
      $subform->populate($video->toArray());
      $form->addSubForm($subform, $video->getGuid());
    }

		//CHECK METHOD
    if (!$this->getRequest()->isPost()) {
      return;
    }

		//FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //GET FORM VALUES
    $values = $form->getValues();

		//VIDEO SUBFORM PROCESS IN EDITING
    foreach ($videos as $video) {
      $subform = $form->getSubForm($video->getGuid());

      $values = $subform->getValues();
      $values = $values[$video->getGuid()];
      if (isset($values['delete']) && $values['delete'] == '1') {
				$videoClassTable= Engine_Api::_()->getItemTable('list_clasfvideo', 'list');
        $videoClassTable->delete(array('video_id = ?' => $video->video_id, 'listing_id = ?' => $list->listing_id));
        Engine_Api::_()->getDbtable('actions', 'activity')->delete(array('type = ?' => 'video_list', 'object_id = ?' => $list->listing_id));
      } else {
        $video->setFromArray($values);
        $video->save();
      }
    }

    return $this->_helper->redirector->gotoRoute(array('action' => 'edit', 'listing_id' => $list->listing_id), 'list_videospecific', true);
  }

  public function deleteAction() {

		//GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

		//GET VIDEO ID
    $video_id = $this->_getParam('video_id');

		//GET LIST SUBJECT
    $this->view->list = $list = Engine_Api::_()->core()->getSubject();

    if ($this->getRequest()->isPost() && $this->getRequest()->getPost('confirm') == true) {

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        Engine_Api::_()->getDbtable('clasfvideos', 'list')->delete(array('listing_id = ?' => $list->listing_id, 'video_id = ?' => $video_id));
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => true,
              'parentRefresh' => '500',
              'parentRefreshTime' => '500',
              'format' => 'smoothbox',
              'messages' => Zend_Registry::get('Zend_Translate')->_('You have successfully delete this video.')
      ));
    }
  }

}