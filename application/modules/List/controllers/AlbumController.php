<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: AlbumController.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_AlbumController extends Core_Controller_Action_Standard {

	//ACTION FOR EDIT PHOTO
  public function editphotosAction() {

		//AUTHORIZATION CHECK
    if (!$this->_helper->requireAuth()->setAuthParams('list_listing', null, 'view')->isValid())
      return;

		//LOGGEND IN USER CAN EDIT PHOTO
    if (!$this->_helper->requireUser()->isValid())
      return;

		//GET LISTING ID AND OBJECT
    $this->view->listing_id = $listing_id = $this->_getParam('listing_id');
    $this->view->list = $list = Engine_Api::_()->getItem('list_listing', $listing_id);

		//IF LIST IS NOT EXIST
    if (empty($list)) {
      return $this->_forward('notfound', 'error', 'core');
    }

		//SET LISTING SUBJECT
		Engine_Api::_()->core()->setSubject($list);

		//GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

		//AUTHORIZATION CHECK
    if (!$this->_helper->requireAuth()->setAuthParams($list, $viewer, 'edit')->isValid()) {
      return;
    }
    
		//AUTHORIZATION CHECK
    $this->view->allowed_upload_photo = Engine_Api::_()->authorization()->isAllowed($list, $viewer, 'photo');
    if (empty($this->view->allowed_upload_photo)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    //OVERVIEW IS ALLOWED OR NOT
		$this->view->allow_overview_of_owner = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'list_listing', 'overview');

		//SELECTED TAB
    $this->view->TabActive = "photo";

		//WHO CAN UPLOAD VIDEO
    $this->view->allowed_upload_video = Engine_Api::_()->list()->allowVideo($list, $viewer);

    //PREPARE DATA
    $this->view->album = $album = $list->getSingletonAlbum();
    $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
    $paginator->setCurrentPageNumber($this->_getParam('page'));
    $paginator->setItemCountPerPage($paginator->getTotalItemCount());
    $this->view->count = count($paginator);

    //MAKE FORM
    $this->view->form = $form = new List_Form_Album_Photos();

    foreach ($paginator as $photo) {
      $subform = new List_Form_Photo_SubEdit(array('elementsBelongTo' => $photo->getGuid()));
      $subform->populate($photo->toArray());
      $form->addSubForm($subform, $photo->getGuid());
      $form->cover->addMultiOption($photo->file_id, $photo->file_id);
    }

		//CHECK METHOD
    if (!$this->getRequest()->isPost()) {
      return;
    }

		//FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $table = Engine_Api::_()->getDbTable('albums', 'list');
    $db = $table->getAdapter();
    $db->beginTransaction();
    try {
      $values = $form->getValues();
      if (!empty($values['cover'])) {

        $album->photo_id = $values['cover'];
        $album->save();

        $list->photo_id = $values['cover'];
        $list->save();
      }

      //PROCESS
      foreach ($paginator as $photo) {

        $subform = $form->getSubForm($photo->getGuid());
        $values = $subform->getValues();
        $values = $values[$photo->getGuid()];

        if (isset($values['delete']) && $values['delete'] == '1') {
          $photo->delete();
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

    return $this->_helper->redirector->gotoRoute(array('action' => 'editphotos', 'listing_id' => $album->listing_id), 'list_albumspecific', true);
  }

}