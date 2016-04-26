<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupmember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitegroupmember_AdminSettingsController extends Core_Controller_Action_Admin {

  //ACTION FOR GLOBAL SETTINGS
  public function indexAction() {
  
    $db = Engine_Db_Table::getDefaultAdapter();
    $this->view->hasLanguageDirectoryPermissions = $hasLanguageDirectoryPermissions = Engine_Api::_()->getApi('language', 'sitegroup')->hasDirectoryPermissions();
    $group = Engine_Api::_()->getApi('settings', 'core')->getSetting( "language.phrases.group", "group");
    $groups = Engine_Api::_()->getApi('settings', 'core')->getSetting( "language.phrases.groups", "groups"); 
    if (isset($_POST['language_phrases_groups']) && $_POST['language_phrases_groups'] != $groups && isset($_POST['language_phrases_group']) && $_POST['language_phrases_group'] != $group && !empty($this->view->hasLanguageDirectoryPermissions)) {
      $language_pharse = array('text_groups' => $_POST['language_phrases_groups'] , 'text_group' => $_POST['language_phrases_group']);
      Engine_Api::_()->getApi('language', 'sitegroup')->setTranslateForListType($language_pharse);
    }
    
    //TABS CREATION
    $this->view->navigationGroup = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_admin_main', array(), 'sitegroup_admin_main_member');     

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitegroupmember_admin_main', array(), 'sitegroupmember_admin_settings');

    $this->view->form = $form = new Sitegroupmember_Form_Admin_Global();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();

      //BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        foreach ($values as $key => $value) {
          if ($value != '') {
            Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
          }
        }
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
    
		$pluginName = Engine_Api::_()->sitegroupmember()->isModulesSupport();
		if( !empty($pluginName) ) {
			$this->view->supportingModules = $pluginName;
		}
  }

  public function widgetPlaced($group_id, $widgetname, $parent_content_id, $order, $params) {

  	$table = Engine_Api::_()->getDbtable('content', 'core');
	  $contentTableName = $table->info('name');
		$contentWidget = $table->createRow();
		$contentWidget->page_id = $group_id;
		$contentWidget->type = 'widget';
		$contentWidget->name = $widgetname;
		$contentWidget->parent_content_id = $parent_content_id;
		$contentWidget->order = $order;
		$contentWidget->params = "$params";
		$contentWidget->save();

  }
  
  
  //ACTION FOR FAQ
  public function faqAction() {
    
    //TABS CREATION
    $this->view->navigationGroup = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_admin_main', array(), 'sitegroup_admin_main_member');     

    //TABS CREATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitegroupmember_admin_main', array(), 'sitegroupmember_admin_main_faq');
  }
  
  public function readmeAction() {
    
  }
  
  //ACTION FOR CREATE NEW REVIEW PARAMETER
  public function createAction() {

    //LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //GENERATE FORM
    $form = $this->view->form = new Sitegroupmember_Form_Admin_Create();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

    $this->view->options = array();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {

        //CHECK ROLES
        $options = (array) $this->_getParam('optionsArray');
        $options = array_filter(array_map('trim', $options));
        $options = array_slice($options, 0, 100);
        $this->view->options = $options;
        if (empty($options) || !is_array($options) || count($options) < 1) {
          return $form->addError('You must add at least one roles.');
        }

				$rolesTable = Engine_Api::_()->getDbtable('roles', 'sitegroupmember');
				foreach ($options as $option) {
					$row = $rolesTable->createRow();
					$row->group_category_id = $this->_getParam('category_id');
					$row->role_name = $option;
					$row->is_admincreated = 1;
					$row->save();
				}

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('')
      ));
    }

    $this->renderScript('admin-settings/create.tpl');
  }
  
  //ACTION FOR EDITING THE REVIEW PARAMETER NAME
  public function editAction() {

    //LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    if (!($category_id = $this->_getParam('category_id'))) {
      die('No identifier specified');
    }

    //FETCH ROLES ACCORDING TO THIS CATEGORY
    $categoryIdsArray = array();
    $categoryIdsArray[] = $category_id;
    $roleParams = Engine_Api::_()->getDbtable('roles', 'sitegroupmember')->rolesParams($categoryIdsArray);

    $this->view->options = array();
    $this->view->totalOptions = 1;

    //GENERATE A FORM
    $form = $this->view->form = new Sitegroupmember_Form_Admin_Edit();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    $form->setField($roleParams);

    //CHECK ROLES
    $options = (array) $this->_getParam('optionsArray');
    $options = array_filter(array_map('trim', $options));
    $options = array_slice($options, 0, 100);
    $this->view->options = $options;
    $this->view->totalOptions = Count($options);

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {

        foreach ($values as $key => $value) {
          if ($key != 'options' && $key != 'dummy_text') {
            $role_id = explode('role_name_', $key);

            if (!empty($role_id)) {
              $role = Engine_Api::_()->getItem('sitegroupmember_roles', $role_id[1]);

              if (!empty($role)) {
                $role->role_name = $value;
                $role->save();
              }
            }
          }
        }

        foreach ($options as $index => $option) {
          $row = Engine_Api::_()->getDbtable('roles', 'sitegroupmember')->createRow();
          $row->group_category_id = $this->_getParam('category_id');
          $row->role_name = $option;
          $row->is_admincreated = 1;
          $row->save();
        }

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('Roles has been edited successfully.')
      ));
    }

    $this->renderScript('admin-settings/edit.tpl');
  }

  //ACTION FOR MANAGE MEMBER CATEGORY.
  public function manageCategoryAction() {
    
    //TABS CREATION
    $this->view->navigationGroup = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_admin_main', array(), 'sitegroup_admin_main_member');     

    //TABS CREATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitegroupmember_admin_main', array(), 'sitegroupmember_admin_main_managecategory');
    
    $this->view->manageRoleSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupmember.category.settings', 1);
    
    $this->view->form = $form = new Sitegroupmember_Form_Admin_ManageCategorySettings();
    $this->view->manage_category = true;
    
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
    
      $values = $form->getValues();
      
      //BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        // Okay, save
        foreach ($values as $key => $value) {
          if ($value != '') {
            Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
          }
        }
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_helper->redirector->gotoRoute(array('action' => 'manage-category'));
    }

    //GET ROLES TABLE NAME
    $rolesTable = Engine_Api::_()->getDbtable('roles', 'sitegroupmember');

    $tableCategory = Engine_Api::_()->getDbtable('categories', 'sitegroup');
    $categories = array();
    $category_info = $tableCategory->getCategories();
    foreach ($category_info as $value) {
      $role_params = array();
      $categoryIdsArray = array();
      $categoryIdsArray[] = $value->category_id;
      $getCatRolesParams = $rolesTable->rolesParams($categoryIdsArray);
      foreach ($getCatRolesParams as $roleParam) {
        $role_params[$value->category_id][] = array(
            'cat_role_id' => $roleParam->role_id,
            'role_name' => $roleParam->role_name,
        );
      }

      $categories[] = $category_array = array(
          'category_id' => $value->category_id,
          'category_name' => $value->category_name,
          'order' => $value->cat_order,
          'role_params' => $role_params,
      );
    }
    $this->view->categories = $categories;
  }

  //ACTION FOR DELETING THE REVIEW PARAMETERS
  public function deleteAction() {

    //LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    if (!($category_id = $this->_getParam('category_id'))) {
      die('No identifier specified');
    }

    //GENERATE FORM
    $form = $this->view->form = new Sitegroupmember_Form_Admin_Delete();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();

      foreach ($values as $key => $value) {
        if ($value == 1) {
          $role_id = explode('role_name_', $key);
          $role = Engine_Api::_()->getItem('sitegroupmember_roles', $role_id[1]);

          Engine_Api::_()->getDbtable('roles', 'sitegroupmember')->delete(array('role_id = ?' => $role_id[1], 'is_admincreated =? ' => 1));

          $db = Engine_Db_Table::getDefaultAdapter();
          $db->beginTransaction();

          try {
            $role->delete();
            $db->commit();
          } catch (Exception $e) {
            $db->rollBack();
            throw $e;
          }
        }
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('Roles has been deleted successfully.')
      ));
    }
    $this->renderScript('admin-settings/delete.tpl');
  }
}
