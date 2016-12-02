<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminContentController.php 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitetagcheckin_AdminContentController extends Core_Controller_Action_Admin {

  //ACTION FOR CREATING THE CONTENT WHICH CAN BE TAGGABLE
  public function addAction() {
    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitetagcheckin_admin_main', array(), 'sitetagcheckin_admin_manage_modules');

    //GET FORM
    $this->view->form = $form = new Sitetagcheckin_Form_Admin_Content_Add();

    //CHECK FORM 
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //GET FORM VALUES
      $values = $form->getValues();

      //GET RESOURCE TYPE
      $resource_type = $values['resource_type'];

      //GET CONTENT TABLE
      $contentTable = Engine_Api::_()->getDbTable('contents', 'sitetagcheckin');

      //CHECK WHETHER THIS MODULE ALREADY ADDED OR NOT
      $customCheck = $contentTable->getContentInformation(array('resource_type' => $resource_type));
      if (!empty($customCheck)) {
        $itemError = Zend_Registry::get('Zend_Translate')->_("This ‘Content Module’ already exist.");
        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($itemError);
        return;
      }

      //GET ITEM OF RESOURCE TYPE TABLE
      $resourceTypeTable = Engine_Api::_()->getItemTable($resource_type);

      //GET PRIMARY KEY ID
      $primaryId = current($resourceTypeTable->info("primary"));
      if (!empty($primaryId))
        $values['resource_id'] = $primaryId;

      //BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
		include APPLICATION_PATH . '/application/modules/Sitetagcheckin/controllers/license/license2.php';

        //END
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      //REDIRECTING TO THE MANAGE CONTENT PAGE
      return $this->_helper->redirector->gotoRoute(array('controller' => 'manage', 'action' => 'index'));
    }
  }

  //ACTION FOR EDITING THE MODULE WHICH CAN BE TAGGABLE
  public function editAction() {

    //GET CONTENT
    $getContentItem = Engine_Api::_()->getItem('sitetagcheckin_contents', $this->_getParam('content_id'));

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitetagcheckin_admin_main', array(), 'sitetagcheckin_admin_manage_modules');

    //GET FORM
    $this->view->form = $form = new Sitetagcheckin_Form_Admin_Content_Edit();

    $values = $getContentItem->toArray();

    $form->populate($values);

    //IF NOT POST OR FORM NOT VALID THAN RETURN
    if (!$this->getRequest()->isPost()) {
      $form->populate($values);
      return;
    }

    //IF NOT POST OR FORM NOT VALID THAN RETURN
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //GET FORM VALUES
    $values = $form->getValues();
    unset($values['module']);
    unset($values['resource_type']);

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {

      //SET VALUES
      $getContentItem->setFromArray($values);

      //SAVE VALUES
      $getContentItem->save();

      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //REDIRECTING TO THE MANAGE CONTENT PAGE
    return $this->_helper->redirector->gotoRoute(array('controller' => 'manage', 'action' => 'index'));
  }

  //ACTION FOR DELETE THE CONTENT
  public function deleteAction() {

    //SET DEFAULT SIMPLE LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //GET RESOURCE TYPE
    $this->view->resource_type = $resource_type = $this->_getParam('resource_type');

    //GET MODULE NAME
    $this->view->module = $this->_getParam('module_name');

    //GET CONTENT TABLE
    $contentTable = Engine_Api::_()->getDbtable('contents', 'sitetagcheckin');

    //GET CONTENT
    $content = $contentTable->getContentInformation(array('resource_type' => $resource_type));

    if ($this->getRequest()->isPost()) {

      //DELETE CONTENT
      $content->delete();

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
      ));
    }
  }

  //ACTION FOR ENABLE THE CONTENT FOR CHECKIN
  public function enabledAction() {

    //GET CONETNT ID
    $content_id = $this->_getParam('content_id');

    //GET DB
    $db = Engine_Db_Table::getDefaultAdapter();

    $db->beginTransaction();

    //GET CONTENT TABLE
    $contentTable = Engine_Api::_()->getDbtable('contents', 'sitetagcheckin');

    //GET CONTENT
    $content = $contentTable->getContentInformation(array('content_id' => $content_id));

    //SAVE CONTENT
    try {
      $content->enabled = !$content->enabled;
      $content->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitetagcheckin/manage');
  }

}
