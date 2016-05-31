<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminManageController.php 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteadvsearch_AdminManageController extends Core_Controller_Action_Admin {

  public function indexAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('siteadvsearch_admin_main', array(), 'siteadvsearch_admin_main_manage');

    include APPLICATION_PATH . '/application/modules/Siteadvsearch/controllers/license/license2.php';
  }

  //ACTION FOR CHANGING THE SEQUENCE OF CONTENT TYPES WILL BE SHOWING ON MAIN SEARCH PAGE 
  public function orderAction() {

    if (!$this->getRequest()->isPost())
      return;

    $contentTypes = Engine_Api::_()->getDbtable('contents', 'siteadvsearch')->getContenListPaginator($order = 1);
    $contentTypes->setItemCountPerPage(100);
    foreach ($contentTypes as $contentType) {
      $order = $this->getRequest()->getParam('content_' . $contentType->resource_type);
      if (!$order)
        $order = 999;

      Engine_Db_Table::getDefaultAdapter()->update('engine4_siteadvsearch_contents', array(
          'order' => $order,
              ), array(
          'resource_type = ?' => $contentType->resource_type,
      ));
    }
    return;
  }

  //ACTION FOR ENABLED OR DISABLED TAB OF CONTENT TYPE WILL BE SHOWIING ON MAIN SEARCH PAGE
  public function showTabAction() {

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    $content = Engine_Api::_()->getItem('siteadvsearch_content', $this->_getParam('content_id'));
    try {
      $content->content_tab = $this->_getParam('show');
      $content->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $modules_include = array('sitealbum', 'feedback', 'group', 'event', 'blog', 'classified', 'poll', 'video', 'music', 'album', 'document', 'recipe', 'list', 'sitepage', 'sitebusiness', 'sitegroup', 'sitestore', 'sitestoreproduct', 'siteevent', 'sitefaq', 'sitetutorial', 'sitereview', 'sitereviewlistingtype', 'sitevideo');

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemember'))
      $modules_include = array_merge($modules_include, array('user'));

    if (in_array($content->module_name, $modules_include)) {
      $values = array();
      $values['listingtype_id'] = $content->listingtype_id;
      $values['resource_type'] = $content->resource_type;
      $values['resource_title'] = $content->resource_title;
      $values['default_page'] = 1;
      Engine_Api::_()->getApi('core', 'siteadvsearch')->makeWidgetizePage($values);
    }

    $content->widgetize = 1;
    $content->save();
    $this->_redirect('admin/siteadvsearch/manage');
  }

  //ACTION FOR SHOWING CONTENT TYPE IN THE SEARCH BOX PLACED AT HEADER
  public function showContentSearchAction() {

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    $content = Engine_Api::_()->getItem('siteadvsearch_content', $this->_getParam('content_id'));
    try {
      $content->main_search = $this->_getParam('show');
      $content->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/siteadvsearch/manage');
  }

  //ACTION FOR ENABLED/DISABLED CONTENT TYPE IN SEARCH RESULTS
  public function enableSearchAction() {

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    $content = Engine_Api::_()->getItem('siteadvsearch_content', $this->_getParam('content_id'));
    $option = $this->_getParam('show');
    try {
      $content->enabled = $option;
      if (empty($option)) {
        $content->main_search = 0;
        $content->content_tab = 0;
      }
      $content->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/siteadvsearch/manage');
  }

  //ACTION FOR ADD NEW CONTENT TYPE
  public function addContentAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('siteadvsearch_admin_main', array(), 'siteadvsearch_admin_main_manage');

    $this->view->form = $form = new Siteadvsearch_Form_Admin_Manage_Content();
    if (!$this->getRequest()->isPost())
      return;
    if (!$form->isValid($this->getRequest()->getPost()))
      return;

    // Process
    $values = $form->getValues();
    $contentTable = Engine_Api::_()->getItemTable('siteadvsearch_content');
    $contentCheck = $contentTable->fetchRow(array('module_name = ?' => $values['module_name']));
    if (!empty($contentCheck)) {
      $itemError = "Content Type already exists.";
      $form->getDecorator('errors')->setOption('escape', false);
      $form->addError($itemError);
      return;
    }
    $content = $contentTable->createRow();
    $content->setFromArray($values);
    $content->save();
    $this->_redirect('admin/siteadvsearch/manage');
  }

  //ACTION FOR EDIT TITLE OF A CONTENT TYPE 
  public function editContentAction() {

    $this->_helper->layout->setLayout('default-simple');

    $this->view->content_id = $content_id = $this->_getParam('content_id');
    $resource_title = Engine_Api::_()->getItem('siteadvsearch_content', $content_id)->resource_title;

    $this->view->form = $form = new Siteadvsearch_Form_Admin_Manage_EditContent();

    $form->content->setValue($resource_title);

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost()))
      return;

    // Process
    Engine_Api::_()->getDbtable('contents', 'siteadvsearch')->update(array('resource_title' => $_POST['content']), array('content_id =?' => $content_id));

    $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Module Title has been edited successfully.'))
    ));
  }

  //ACTION FOR DELETE CONTENT TYPE
  public function deleteContentAction() {

    $this->_helper->layout->setLayout('admin-simple');
 
    if ($this->getRequest()->isPost()) {
      $content = Engine_Api::_()->getItem('siteadvsearch_content', $this->_getParam('content_id'));
      $content->delete();
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Selected module has been deleted successfully.'))
      ));
    }
  }

  //ACTION FOR MANAGE ICON FOR CONTENT TYPE WILL BE SHOWING IN SEARCH BOX
  public function manageIconAction() {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('siteadvsearch_admin_main', array(), 'siteadvsearch_admin_main_icon');

    include APPLICATION_PATH . '/application/modules/Siteadvsearch/controllers/license/license2.php';
  }

  //ACTION FOR ADDING ICON TO CONTENT TYPE
  Public function addIconAction() {

    //SET LAYOUT
    $this->_helper->layout->setLayout('admin-simple');
    $content_id = $this->_getParam('content_id', null);

    $searchContent = Engine_Api::_()->getItem('siteadvsearch_content', $content_id);

    //CREATE FORM
    $this->view->form = $form = new Siteadvsearch_Form_Admin_Manage_Addicon();

    $this->view->close_smoothbox = 0;

    if (!$this->getRequest()->isPost())
      return;

    if (!$form->isValid($this->getRequest()->getPost()))
      return;

    //UPLOAD PHOTO
    if (isset($_FILES['photo']) && is_uploaded_file($_FILES['photo']['tmp_name'])) {
      //UPLOAD PHOTO
      $photoFile = $searchContent->setPhoto($_FILES['photo'], $content_id);

      //UPDATE FILE ID IN SEARCH CONTENTS TABLE
      if (!empty($photoFile->file_id)) {
        $searchContent->file_id = $photoFile->file_id;
        $searchContent->save();
      }
    }

    $this->view->close_smoothbox = 1;
  }

  //ACTION FOR EDIT THE CONTENT TYPE ICON
  Public function editIconAction() {

    //SET LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //GET CONTENT ID
    $this->view->content_id = $content_id = $this->_getParam('content_id', null);

    //GET CONTENT ITEM
    $searchContent = Engine_Api::_()->getItem('siteadvsearch_content', $content_id);

    //EDIT FORM
    $this->view->form = $form = new Siteadvsearch_Form_Admin_Manage_Editicon();

    $this->view->close_smoothbox = 0;

    if (!$this->getRequest()->isPost())
      return;

    if (!$form->isValid($this->getRequest()->getPost()))
      return;

    //UPLOAD PHOTO
    if (isset($_FILES['photo']) && is_uploaded_file($_FILES['photo']['tmp_name'])) {
      //UPLOAD PHOTO
      $photoFile = $searchContent->setPhoto($_FILES['photo'], $content_id);

      //UPDATE FILE ID IN SEARCH CONTENTS TABLE
      if (!empty($photoFile->file_id)) {
        $previous_file_id = $searchContent->file_id;
        $searchContent->file_id = $photoFile->file_id;
        $searchContent->save();

        //DELETE PREVIOUS CONTENT TYOE ICON
        $file = Engine_Api::_()->getItem('storage_file', $previous_file_id);
        if (!empty($file)) {
          $file->delete();
        }
      }
    }
    $this->view->close_smoothbox = 1;
  }

}