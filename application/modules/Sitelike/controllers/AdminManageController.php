<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminManageController.php 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelike_AdminManageController extends Core_Controller_Action_Admin {

  public function indexAction() {

    if (!$this->_helper->requireUser()->isValid())
      return;
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitelike_admin_main', array(), 'sitelike_admin_manage_modules');

    $this->view->enabled_modules_array = Engine_Api::_()->getDbtable('modules',
'core')->getEnabledModuleNames();
    $page = $this->_getParam('page', 1);  // Page id: Controll pagination.
    $pagesettingsTable = Engine_Api::_()->getItemTable('sitelike_mixsettings');
    $pagesettingsTableName = $pagesettingsTable->info('name');
    $pagesettingsSelect = $pagesettingsTable->select();
    $this->view->paginator = Zend_Paginator::factory($pagesettingsSelect);
    $this->view->paginator->setItemCountPerPage(100);
    $this->view->paginator->setCurrentPageNumber($page);
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $obj = Engine_Api::_()->getItem('sitelike_mixsettings', $value);
          if (empty($obj->is_delete)) {
            $obj->delete();
          }
        }
      }
    }
  }

  // Function: Manage Module - Creation Tab.
  public function moduleCreateAction() {

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus',
'core')->getNavigation('sitelike_admin_main', array(), 'sitelike_admin_manage_modules');
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    $module_table = Engine_Api::_()->getDbTable('mixsettings', 'sitelike');
    $module_name = $module_table->info('name');
    $this->view->form = $form = new Sitelike_Form_Admin_Module();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();
      $resource_type = $values['resource_type'];
      $title = $values['title_items'];
      $module = $values['module'];


      $customCheck = $module_table->fetchRow(array('resource_type = ?' => $resource_type, 'module = ?' => $module));
      if (!empty($customCheck)) {
        $itemError = Zend_Registry::get('Zend_Translate')->_("This ‘Content Module’ already exist.");
        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($itemError);
        return;
      }

      $settingsArray = array('content_type' => $resource_type, 'tab1_show' => '1', 'tab1_duration' => '7',
'tab1_name' => 'This Week', 'tab1_entries' => '3', 'tab2_show' => '1', 'tab2_duration' => '30', 'tab2_name' =>
'This Month', 'tab2_entries' => '3', 'tab3_show' => '1', 'tab3_duration' => 'overall', 'tab3_name' =>
'Overall', 'tab3_entries' => '3', 'view_layout' => '1');

      $resourceTypeTable = Engine_Api::_()->getItemTable($resource_type);
      $primaryId = current($resourceTypeTable->info("primary"));
      if (!empty($primaryId))
        $values['resource_id'] = $primaryId;

      //BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $db->query("DELETE FROM `engine4_activity_actiontypes` WHERE `engine4_activity_actiontypes`.`type` = 'like_$resource_type' LIMIT 1");

        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("like_' . $resource_type . '", "' . $module . '", "{item:$subject} likes the ' . $title . ' {item:$object}:", 1, 5, 1, 1, 1, 0);');

        $table = Engine_Api::_()->getDbtable('mixsettings', 'sitelike');
        $settingstable = Engine_Api::_()->getDbtable('settings', 'sitelike');
        $settingsrow = $settingstable->createRow();
        $row = $table->createRow();
        $row->setFromArray($values);
        $row->save();
        $settingsrow->setFromArray($settingsArray);
        $settingsrow->save();

        //start
        //this function use to write add phrase in the custom.csv file.
        $this->addPhraseAction(array('{item:$subject} likes the ' . $title . ' {item:$object}:' =>'{item:$subject} likes the ' . $title . ' {item:$object}:',
            '' => '',
        ));
        $activityResourceType = strtoupper($resource_type);
        $this->addPhraseAction(array("ADMIN_ACTIVITY_TYPE_LIKE_$activityResourceType" => 'When someone like
the ' . $title . '.',
            '' => '',
        ));

        $this->addPhraseAction(array("_ACTIVITY_ACTIONTYPE_LIKE_$activityResourceType" => 'When I liked the '
. $title . '',
            '' => '',
        ));
        //end
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
  }

  //FOR ADDED PHRASE IN THE LANGUAGE FILE.
  public function addPhraseAction($phrase) {

    if ($phrase) {
      //file path name
      $targetFile = APPLICATION_PATH . '/application/languages/en/custom.csv';
      if (!file_exists($targetFile)) {
        //Sets access of file
        touch($targetFile);
        //changes permissions of the specified file.
        chmod($targetFile, 0777);
      }
      if (file_exists($targetFile)) {
        $writer = new Engine_Translate_Writer_Csv($targetFile);
        $writer->setTranslations($phrase);
        $writer->write();
        //clean the entire cached data manually
        @Zend_Registry::get('Zend_Cache')->clean();
      }
    }
  }

  // Function: Manage Module - Creation Tab.
  public function moduleEditAction() {

    $manageModules = Engine_Api::_()->getItem('sitelike_mixsettings', $this->_getParam('mixsetting_id'));
    $modules = Engine_Api::_()->getItem('sitelike_mixsettings', $manageModules->mixsetting_id);

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus',
'core')->getNavigation('sitelike_admin_main', array(), 'sitelike_admin_manage_modules');

    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

    $this->view->form = $form = new Sitelike_Form_Admin_Moduleedit();
    //SHOW PRE-FIELD FORM
    $form->populate($manageModules->toArray());

    //IF NOT POST OR FORM NOT VALID THAN RETURN
    if (!$this->getRequest()->isPost()) {
      $form->populate($manageModules->toArray());
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

      $manageModules->setFromArray($values);
      $manageModules->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    return $this->_helper->redirector->gotoRoute(array('controller' => 'manage', 'action' => 'index'));
  }

  public function enabledContentTabAction() {
    $value = $this->_getParam('mixsetting_id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    $content = Engine_Api::_()->getItemTable('sitelike_mixsettings')->fetchRow(array('mixsetting_id = ?' =>
$value));
    try {
      $content->enabled = !$content->enabled;
      $content->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitelike/manage');
  }

  public function deleteModuleAction() {
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->resource_type = $resource_type = $this->_getParam('resource_type');

    $mixsettingstable = Engine_Api::_()->getDbtable('mixsettings', 'sitelike');
    $sub_status_select = $mixsettingstable->fetchRow(array('resource_type = ?' => $resource_type));
    $this->view->module = $sub_status_select->module;

    if ($this->getRequest()->isPost()) {
      $settingTable = Engine_Api::_()->getItemTable('sitelike_setting')->fetchRow(array('content_type = ?' =>
$resource_type));
      $custom = Engine_Api::_()->getItemTable('sitelike_mixsettings')->fetchRow(array('resource_type = ?' =>
$resource_type));

      if (!empty($settingTable))
        $settingTable->delete();
      $custom->delete();

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
      ));
    }
  }

}

?>