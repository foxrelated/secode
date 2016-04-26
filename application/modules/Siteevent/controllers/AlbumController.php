<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AlbumController.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_AlbumController extends Core_Controller_Action_Standard {

    //ACTION FOR EDIT PHOTO
    public function editphotosAction() {

        //LOGGEND IN USER CAN EDIT PHOTO
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET EVENT ID AND OBJECT
        $this->view->event_id = $event_id = $this->_getParam('event_id');
        $change_url = $this->_getParam('change_url', 0);
        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, "view")->isValid())
            return;

        //IF SITEEVENT IS NOT EXIST
        if (empty($siteevent)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //SET EVENT SUBJECT
        Engine_Api::_()->core()->setSubject($siteevent);

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //AUTHORIZATION CHECK
        $this->view->allowed_upload_video = Engine_Api::_()->siteevent()->allowVideo($siteevent, $viewer);
        if (!$this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "edit")->isValid()) {
            return;
        }
       
        //SELECTED TAB
        $this->view->TabActive = "photo";

        //PREPARE DATA
        $this->view->album = $album = $siteevent->getSingletonAlbum();
        $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage($paginator->getTotalItemCount());
        $this->view->count = count($paginator);

        //START - PACKAGE BASED CHECKS
        $this->view->upload_photo = 0;
        if (Engine_Api::_()->siteevent()->hasPackageEnable()) {
          $package = Engine_Api::_()->getItem('siteeventpaid_package', $siteevent->package_id);
          if(Engine_Api::_()->siteeventpaid()->allowPackageContent($siteevent->package_id, "photo")) {
            $allowed_upload_photo = 1;
            if(empty($package->photo_count))
            $this->view->upload_photo = 1;
            elseif($package->photo_count > $paginator->getTotalItemCount()) 
            $this->view->upload_photo = 1;
          } else {
          $allowed_upload_photo = 0;
          }
        } else {//AUTHORIZATION CHECK
          $this->view->upload_photo = $allowed_upload_photo = Engine_Api::_()->authorization()->isAllowed($siteevent, $viewer, "photo");
        }
        //START - PACKAGE BASED CHECKS
        if (empty($allowed_upload_photo)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $this->view->slideShowEnanle = $slideShowEnanle = $slideShowEnable = $this->slideShowEnable();

        //MAKE FORM
        $this->view->form = $form = new Siteevent_Form_Album_Photos();
        $this->view->enableVideoPlugin = $slideShowEnanle ? Engine_Api::_()->siteevent()->allowVideo($siteevent, $viewer) : 0;
        if ($this->view->enableVideoPlugin) {
            $form->addElement('Radio', 'video_snapshot_id', array(
                'label' => 'Video Snapshot',
            ));
        }
        foreach ($paginator as $photo) {
            $subform = new Siteevent_Form_Photo_SubEdit(array('elementsBelongTo' => $photo->getGuid()));
            if (empty($slideShowEnable)) {
                $subform->removeElement('show_slidishow');
            }
            $subform->populate($photo->toArray());
            $form->addSubForm($subform, $photo->getGuid());
            $form->cover->addMultiOption($photo->file_id, $photo->file_id);
            if ($this->view->enableVideoPlugin) {
                $form->video_snapshot_id->addMultiOption($photo->photo_id, $photo->photo_id);
            }
        }

        //CHECK METHOD
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $table = Engine_Api::_()->getDbTable('albums', 'siteevent');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $values = $form->getValues();
            if (!empty($values['cover']) && $siteevent->photo_id != $values['cover']) {

                $album->photo_id = $values['cover'];
                $album->save();

                $siteevent->photo_id = $values['cover'];
                $siteevent->save();
                $siteevent->updateAllCoverPhotos();
            }

            if (!empty($values['video_snapshot_id'])) {

                $siteeventOtherInfo = Engine_Api::_()->getDbtable('otherinfo', 'siteevent')->getOtherinfo($siteevent->event_id);
                $siteeventOtherInfo->video_snapshot_id = $values['video_snapshot_id'];
                $siteeventOtherInfo->save();
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

            if (!empty($siteevent->photo_id)) {
                $photoTable = Engine_Api::_()->getItemTable('siteevent_photo');
                $order = $photoTable->select()
                        ->from($photoTable->info('name'), array('order'))
                        ->where('event_id = ?', $siteevent->event_id)
                        ->group('photo_id')
                        ->order('order ASC')
                        ->limit(1)
                        ->query()
                        ->fetchColumn();

                $photoTable->update(array('order' => $order - 1), array('file_id = ?' => $siteevent->photo_id));
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        if (empty($change_url)) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'editphotos', 'event_id' => $album->event_id), "siteevent_albumspecific", true);
        } else {
            return $this->_helper->redirector->gotoRoute(array('action' => 'change-photo', 'event_id' => $album->event_id), "siteevent_dashboard", true);
        }
    }

    public function orderAction() {

        if (!$this->_helper->requireUser()->isValid())
            return;

        if (!$this->_helper->requireSubject('siteevent_event')->isValid())
            return;

        $subject = Engine_Api::_()->core()->getSubject();

        $order = $this->_getParam('order');
        if (!$order) {
            $this->view->status = false;
            return;
        }

        $album = $subject->getSingletonAlbum();

        // Get a list of all photos in this album, by order
        $photoTable = Engine_Api::_()->getItemTable('siteevent_photo');
        $currentOrder = $photoTable->select()
                ->from($photoTable, 'photo_id')
                ->where('album_id = ?', $album->getIdentity())
                ->where('event_id = ?', $subject->getIdentity())
                ->order('order ASC')
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN)
        ;

        // Find the starting point?
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
            $photoTable->update(array(
                'order' => $i,
                    ), array(
                'photo_id = ?' => $photo_id,
            ));
        }

        $this->view->status = true;
    }

    public function slideShowEnable() {

        //GET CONTENT TABLE
        $tableContent = Engine_Api::_()->getDbtable('content', 'core');
        $tableContentName = $tableContent->info('name');

        //GET PAGE TABLE
        $tablePage = Engine_Api::_()->getDbtable('pages', 'core');
        $tablePageName = $tablePage->info('name');
        //GET PAGE ID
        $page_id = $tablePage->select()
                ->from($tablePageName, array('page_id'))
                ->where('name = ?', "siteevent_index_view")
                ->query()
                ->fetchColumn();

        if (empty($page_id)) {
            return false;
        }

        $content_id = $tableContent->select()
                ->from($tableContent->info('name'), array('content_id'))
                ->where('page_id = ?', $page_id)
                ->where('name = ?', 'siteevent.slideshow-list-photo')
                ->query()
                ->fetchColumn();

        if ($content_id)
            return true;

        $params = $tableContent->select()
                ->from($tableContent->info('name'), array('params'))
                ->where('page_id = ?', $page_id)
                ->where('name = ?', 'siteevent.editor-reviews-siteevent')
                ->query()
                ->fetchColumn();
        if ($params) {
            $params = Zend_Json::decode($params);
            if (!isset($params['show_slideshow']) || $params['show_slideshow']) {
                return true;
            }
            return false;
        }
    }

}