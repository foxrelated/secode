<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: AdminManageSlideController.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_AdminManageSlideController extends Core_Controller_Action_Admin {

  public function indexAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sesvideo_admin_main', array(), 'sesvideo_admin_main_manageSlides');
    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('galleries', 'sesvideo')->getGallery();
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $gallery = Engine_Api::_()->getItem('sesvideo_gallery', $value)->delete();
          $db->query("DELETE FROM engine4_sesvideo_slides WHERE gallery_id = " . $value);
        }
      }
    }
    $page = $this->_getParam('page', 1);
    $paginator->setItemCountPerPage(25);
    $paginator->setCurrentPageNumber($page);
  }

  public function createSlideAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sesvideo_admin_main', array(), 'sesvideo_admin_main_manageSlides');
    $this->view->gallery_id = $id = $this->_getParam('id');
    $this->view->slide_id = $slide_id = $this->_getParam('slide_id', false);
    if (!$id)
      return;
    $this->view->form = $form = new Sesvideo_Form_Admin_Createslide();
    if ($slide_id) {
      //$form->setTitle("Edit HTML5 Video Background");
      $form->submit->setLabel('Save Changes');
      $form->setTitle("Edit Video or Photo");
      $form->setDescription("Below, edit the details for the video or photo.");
      $slide = Engine_Api::_()->getItem('sesvideo_slide', $slide_id);
      $form->populate($slide->toArray());
    }
    if ($this->getRequest()->isPost()) {
      if (!$form->isValid($this->getRequest()->getPost()))
        return;
      $db = Engine_Api::_()->getDbtable('slides', 'sesvideo')->getAdapter();
      $db->beginTransaction();
      try {
        $table = Engine_Api::_()->getDbtable('slides', 'sesvideo');
        $values = $form->getValues();
        if (!isset($slide))
          $slide = $table->createRow();
        $slide->setFromArray($values);
        if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
          // Store video in temporary storage object for ffmpeg to handle
          $storage = Engine_Api::_()->getItemTable('storage_file');
          $filename = $storage->createFile($form->file, array(
              'parent_id' => $slide->slide_id,
              'parent_type' => 'sesvideo_slide',
              'user_id' => 1,
          ));
          // Remove temporary file
          @unlink($file['tmp_name']);
          $slide->file_id = $filename->file_id;
          $slide->file_type = $filename->extension;
        }
        if (isset($_FILES['thumb']['name']) && $_FILES['thumb']['name'] != '') {
          // Store video in temporary storage object for ffmpeg to handle
          $storage = Engine_Api::_()->getItemTable('storage_file');
          $thumbname = $storage->createFile($form->thumb, array(
              'parent_id' => $slide->slide_id,
              'parent_type' => 'sesvideo_slide',
              'user_id' => 1,
          ));
          // Remove temporary file
          @unlink($file['tmp_name']);
          $slide->thumb_icon = $thumbname->file_id;
        }
        $slide->gallery_id = $id;
        $slide->save();
        $db->commit();
        $url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'sesvideo', 'controller' => 'manage-slide', 'action' => 'manage', 'id' => $id), 'admin_default', true);
        header("Location:" . $url);
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
  }

  public function deleteSlideAction() {
    $this->view->type = $this->_getParam('type', null);
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->item_id = $id;
    // Check post
    if ($this->getRequest()->isPost()) {
      $slide = Engine_Api::_()->getItem('sesvideo_slide', $id);
      if ($slide->thumb_icon) {
        $item = Engine_Api::_()->getItem('storage_file', $slide->thumb_icon);
        if ($item->storage_path) {
          @unlink($item->storage_path);
          $item->remove();
        }
      }
      if ($slide->file_id) {
        $item = Engine_Api::_()->getItem('storage_file', $slide->file_id);
        if ($item->storage_path) {
          @unlink($item->storage_path);
          $item->remove();
        }
      }
      $slide->delete();

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('Slide Delete Successfully.')
      ));
    }
    // Output
    $this->renderScript('admin-manage-slide/delete-slide.tpl');
  }

  public function manageAction() {
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $slide = Engine_Api::_()->getItem('sesvideo_slide', $value);
          if ($slide->thumb_icon) {
            $item = Engine_Api::_()->getItem('storage_file', $slide->thumb_icon);
            if ($item->storage_path) {
              @unlink($item->storage_path);
              $item->remove();
            }
          }
          if ($slide->file_id) {
            $item = Engine_Api::_()->getItem('storage_file', $slide->file_id);
            if ($item->storage_path) {
              @unlink($item->storage_path);
              $item->remove();
            }
          }
          $slide->delete();
        }
      }
    }
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sesvideo_admin_main', array(), 'sesvideo_admin_main_manageSlides');
    $this->view->gallery_id = $id = $this->_getParam('id');
    if (!$id)
      return;
    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('slides', 'sesvideo')->getSlides($id);
    $page = $this->_getParam('page', 1);
    $paginator->setItemCountPerPage(1000);
    $paginator->setCurrentPageNumber($page);
  }
  public function orderAction() {

    if (!$this->getRequest()->isPost())
      return;

    $slidesTable = Engine_Api::_()->getDbtable('slides', 'sesvideo');
    $slides = $slidesTable->fetchAll($slidesTable->select());
    foreach ($slides as $slide) {
      $order = $this->getRequest()->getParam('slide_' . $slide->slide_id);
      if (!$order)
        $order = 999;
      $slide->order = $order;
      $slide->save();
    }
    return;
  }
  public function deleteGalleryAction() {

    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');

    $this->view->form = $form = new Sesbasic_Form_Admin_Delete();
    $form->setTitle('Delete This HTML5 Video?');
    $form->setDescription('Are you sure that you want to delete this HTML5 video background? It will not be recoverable after being deleted.');
    $form->submit->setLabel('Delete');

    $id = $this->_getParam('id');
    $this->view->item_id = $id;
    // Check post
    if ($this->getRequest()->isPost()) {
      $chanel = Engine_Api::_()->getItem('sesvideo_gallery', $id)->delete();
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->query("DELETE FROM engine4_sesvideo_slides WHERE gallery_id = " . $id);
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('Gallery Delete Successfully.')
      ));
    }
    // Output
    $this->renderScript('admin-manage-slide/delete-gallery.tpl');
  }

  public function createGalleryAction() {

    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id', 0);

    $this->view->form = $form = new Sesvideo_Form_Admin_Gallery();
    if ($id) {
      $form->setTitle("Edit Gallery Name");
      $form->submit->setLabel('Save Changes');
      $gallery = Engine_Api::_()->getItem('sesvideo_gallery', $id);
      $form->populate($gallery->toArray());
    }
    if ($this->getRequest()->isPost()) {
      if (!$form->isValid($this->getRequest()->getPost()))
        return;
      $db = Engine_Api::_()->getDbtable('galleries', 'sesvideo')->getAdapter();
      $db->beginTransaction();
      try {
        $table = Engine_Api::_()->getDbtable('galleries', 'sesvideo');
        $values = $form->getValues();
        if (!$id)
          $gallery = $table->createRow();
        $gallery->setFromArray($values);
        $gallery->creation_date = date('Y-m-d h:i:s');
        $gallery->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('Gallery created successfully.')
      ));
    }
  }

}
