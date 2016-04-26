<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Nestedcomment
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminModuleController.php 2014-11-07 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Nestedcomment_AdminModuleController extends Core_Controller_Action_Admin {

    public function indexAction() {

        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('nestedcomment_admin_main', array(), 'nestedcomment_admin_manage_modules');

        $this->view->enabled_modules_array = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();

        $page = $this->_getParam('page', 1);
        $pagesettingsTable = Engine_Api::_()->getItemTable('nestedcomment_modules');
        $pagesettingsTableName = $pagesettingsTable->info('name');
        $pagesettingsSelect = $pagesettingsTable->select();
        $moduleCoreName = Engine_Api::_()->getDbtable('modules', 'core')->info('name');
        $pagesettingsSelect = $pagesettingsTable->select()
                ->setIntegrityCheck(false)
                ->from($pagesettingsTableName)
                ->join($moduleCoreName, "$pagesettingsTableName.module = $moduleCoreName.name", 'title')
                ->where($moduleCoreName . ".enabled=?", 1)
                ->order($moduleCoreName . ".name ASC");

        include APPLICATION_PATH . '/application/modules/Nestedcomment/controllers/license/license2.php';

        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            foreach ($values as $key => $value) {
                if ($key == 'delete_' . $value) {
                    $obj = Engine_Api::_()->getItem('nestedcomment_modules', $value);
                    if (empty($obj->is_delete)) {
                        $obj->delete();
                    }
                }
            }
        }
    }

    // Function: Manage Module - Creation Tab.
    public function addAction() {

        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('nestedcomment_admin_main', array(), 'nestedcomment_admin_manage_modules');
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $module_table = Engine_Api::_()->getDbTable('modules', 'nestedcomment');
        $this->view->form = $form = new Nestedcomment_Form_Admin_Module_Add();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            $resource_type = $values['resource_type'];
            $module = $values['module'];
            $customCheck = $module_table->fetchRow(array('resource_type = ?' => $resource_type, 'module = ?' => $module));
            if (!empty($customCheck)) {
                $itemError = Zend_Registry::get('Zend_Translate')->_("This ‘Content Module’ already exist.");
                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($itemError);
                return;
            }

            //BEGIN TRANSACTION
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $values = $form->getValues();

                include APPLICATION_PATH . '/application/modules/Nestedcomment/controllers/license/license2.php';
                //end
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
        }
    }

    // Function: Manage Module - Creation Tab.
    public function editAction() {

        $manageModules = Engine_Api::_()->getItem('nestedcomment_modules', $this->_getParam('module_id'));
        $modules = Engine_Api::_()->getItem('nestedcomment_modules', $manageModules->module_id);

        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('nestedcomment_admin_main', array(), 'nestedcomment_admin_manage_modules');

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        if ($modules->resource_type == 'advancedactivity') {
            $this->view->form = $form = new Nestedcomment_Form_Admin_Module_AdvancedActivityEdit();
        } else {
            $this->view->form = $form = new Nestedcomment_Form_Admin_Module_Edit();
        }

        //IF NOT POST OR FORM NOT VALID THAN RETURN
        if (!$this->getRequest()->isPost()) {
            $val = $manageModules->toArray();
            if ($val['params']) {
                $decodedParams = Zend_Json_Decoder::decode($val['params']);
                $form->populate($decodedParams);

                if (isset($decodedParams['showAsLike'])) {
                    $this->view->showAsLike = $decodedParams['showAsLike'];
                }

                if (isset($decodedParams['aaf_comment_like_box'])) {
                    $this->view->aaf_comment_like_box = $decodedParams['aaf_comment_like_box'];
                }

                if (isset($decodedParams['showAsNested'])) {
                    $this->view->showAsNested = $decodedParams['showAsNested'];
                }
            } else {
                $form->populate($manageModules->toArray());
            }
            return;
        }

        //IF NOT POST OR FORM NOT VALID THAN RETURN
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //GET FORM VALUES
        $values = $form->getValues();
        $values['module'] = $modules->module;
        $values['resource_type'] = $modules->resource_type;

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {

            $manageModules->setFromArray($values);
            $manageModules->save();
            $manageModules->params = Zend_Json_Encoder::encode($values);
            $manageModules->save();
            $db->commit();

            $val = $manageModules->toArray();
            if ($val['params']) {
                $decodedParams = Zend_Json_Decoder::decode($val['params']);

                if (isset($decodedParams['showAsLike'])) {
                    $this->view->showAsLike = $decodedParams['showAsLike'];
                }

                if (isset($decodedParams['aaf_comment_like_box'])) {
                    $this->view->aaf_comment_like_box = $decodedParams['aaf_comment_like_box'];
                }

                if (isset($decodedParams['showAsNested'])) {
                    $this->view->showAsNested = $decodedParams['showAsNested'];
                }
            }
            $form->addNotice('Your changes have been saved.');
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        // return $this->_helper->redirector->gotoRoute(array('controller' => 'module', 'action' => 'index'));
    }

    public function disabledAction() {

        $this->view->resource_type = $resource_type = $this->_getParam('resource_type');

        $value = $this->_getParam('module_id');
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        $content = Engine_Api::_()->getItemTable('nestedcomment_modules')->fetchRow(array('module_id = ?' =>
            $value));
        try {
            $content->enabled = !$content->enabled;
            $content->save();


            switch ($resource_type) {
                case 'blog':
                    $this->replaceWidget('blog_index_view', 'nestedcomment.comments', 'core.comments');
                    break;
                case 'album':
                    $this->replaceWidget('album_album_view', 'nestedcomment.comments', 'core.comments');
                    break;
                case 'album_photo':
                    $this->replaceWidget('album_photo_view', 'nestedcomment.comments', 'core.comments');
                    break;
                case 'classified':
                    $this->replaceWidget('classified_index_view', 'nestedcomment.comments', 'core.comments');
                    break;
                case 'group':
                    $this->replaceWidget('group_profile_index', 'nestedcomment.comments', 'core.comments');
                    break;
                case 'event':
                    $this->replaceWidget('event_profile_index', 'nestedcomment.comments', 'core.comments');
                    break;
                case 'music':
                    $this->replaceWidget('music_playlist_view', 'nestedcomment.comments', 'core.comments');
                    break;
                case 'poll':
                    $this->replaceWidget('poll_poll_view', 'nestedcomment.comments', 'core.comments');
                    break;
                case 'video':
                    $this->replaceWidget('video_index_view', 'nestedcomment.comments', 'core.comments');
                    break;
                case 'sitestaticpage_page':
                    $db->query("UPDATE  `engine4_core_menuitems` SET  `enabled` =  '0' WHERE  `engine4_core_menuitems`.`name` = 'sitestaticpage_admin_main_level' LIMIT 1 ;");
                    break;
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $this->_redirect('admin/nestedcomment/module');
    }

    public function enabledAction() {

        $this->view->resource_type = $resource_type = $this->_getParam('resource_type');

        $value = $this->_getParam('module_id');
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        $content = Engine_Api::_()->getItemTable('nestedcomment_modules')->fetchRow(array('module_id = ?' =>
            $value));
        try {
            $content->enabled = !$content->enabled;
            $content->save();

            $params = '{"taggingContent":["friends"],"showComposerOptions":["addLink","addPhoto"],"showAsNested":"1","showAsLike":"1","showDislikeUsers":"0","showLikeWithoutIcon":"0","showLikeWithoutIconInReplies":"0","loaded_by_ajax":"1"}';
            switch ($resource_type) {
                case 'blog':
                    $this->replaceWidget('blog_index_view', 'core.comments', 'nestedcomment.comments', $params);
                    break;
                case 'album':
                    $this->replaceWidget('album_album_view', 'core.comments', 'nestedcomment.comments', $params);
                    break;
                case 'album_photo':
                    $this->replaceWidget('album_photo_view', 'core.comments', 'nestedcomment.comments', $params);
                    break;
                case 'classified':
                    $this->replaceWidget('classified_index_view', 'core.comments', 'nestedcomment.comments', $params);
                    break;
                case 'group':
                    $this->replaceWidget('group_profile_index', 'core.comments', 'nestedcomment.comments', $params);
                    break;
                case 'event':
                    $this->replaceWidget('event_profile_index', 'core.comments', 'nestedcomment.comments', $params);
                    break;
                case 'music':
                    $this->replaceWidget('music_playlist_view', 'core.comments', 'nestedcomment.comments', $params);
                    break;
                case 'poll':
                    $this->replaceWidget('poll_poll_view', 'core.comments', 'nestedcomment.comments', $params);
                    break;
                case 'video':
                    $this->replaceWidget('video_index_view', 'core.comments', 'nestedcomment.comments', $params);
                    $this->replaceWidget('sitevideo_video_view', 'core.comments', 'nestedcomment.comments', $params);
                    break;
                case 'sitestaticpage_page':
                    $this->setStaticPage();
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $this->_redirect('admin/nestedcomment/module');
    }

    public function setStaticPage() {

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitestaticpage_admin_main_level", "sitestaticpage", "Member Level Settings", "", \'{"route":"admin_default","module":"sitestaticpage","controller":"level"}\', "sitestaticpage_admin_main", "", "1", "0", "3");');

        $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitestaticpage_page' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');");

        $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitestaticpage_page' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin')");

        $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitestaticpage_page' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');");

        $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'sitestaticpage_page' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');");

        $db->query("UPDATE  `engine4_core_menuitems` SET  `enabled` =  '1' WHERE  `engine4_core_menuitems`.`name` = 'sitestaticpage_admin_main_level' LIMIT 1 ;");
    }

    public function deleteAction() {
        $this->_helper->layout->setLayout('admin-simple');
        $this->view->resource_type = $resource_type = $this->_getParam('resource_type');
        $modulestable = Engine_Api::_()->getDbtable('modules', 'nestedcomment');
        $sub_status_select = $modulestable->fetchRow(array('resource_type = ?' => $resource_type));
        $this->view->module = $sub_status_select->module;

        if ($this->getRequest()->isPost()) {
            $custom = Engine_Api::_()->getItemTable('nestedcomment_modules')->fetchRow(array('resource_type = ?' =>
                $resource_type));
            $custom->delete();

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
            ));
        }
    }

    public function replaceWidget($pageName = null, $oldWidgetName = null, $newwidgetName = null, $params = null) {

        //GET CORE PAGE TABLE
        $tableNamePage = Engine_Api::_()->getDbtable('pages', 'core');
        $tableNameContent = Engine_Api::_()->getDbtable('content', 'core');
        $page_id = $tableNamePage->select()
                ->from($tableNamePage->info('name'), 'page_id')
                ->where('name =?', $pageName)
                ->query()
                ->fetchColumn();
        $db = Engine_Db_Table::getDefaultAdapter();

        // if($page_id) {
        $db->query("UPDATE `engine4_core_content` SET `name` = '$newwidgetName', `params` = '$params' WHERE `engine4_core_content`.`page_id`= $page_id and `engine4_core_content`.`name`= '$oldWidgetName';");
        //} else {
        $content_id = $tableNameContent->select()
                ->from($tableNameContent->info('name'), 'content_id')
                ->where('type =?', 'widget')
                ->where('name =?', "$newwidgetName")
                ->where('page_id =?', "$page_id")
                ->query()
                ->fetchColumn();

        if (!$content_id) {
            $main_id = $tableNameContent->select()
                    ->from($tableNameContent->info('name'), 'content_id')
                    ->where('type =?', 'container')
                    ->where('name =?', "main")
                    ->where('page_id =?', "$page_id")
                    ->query()
                    ->fetchColumn();

            $parent_content_id = $tableNameContent->select()
                    ->from($tableNameContent->info('name'), 'content_id')
                    ->where('name =?', "middle")
                    ->where('type =?', 'container')
                    ->where('page_id =?', "$page_id")
                    ->where('parent_content_id =?', "$main_id")
                    ->query()
                    ->fetchColumn();
            // Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => "$newwidgetName",
                'page_id' => "$page_id",
                'params' => "$params",
                'parent_content_id' => $parent_content_id,
                'order' => 999
            ));
        }



        // }
    }

}
