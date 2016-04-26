<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    facebookse
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminManageController.php 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Facebookse_AdminManageController extends Core_Controller_Action_Admin {

    protected $_navigation;

    public function indexAction() {

        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->navigation = $this->getNavigation();

        $this->view->enabled_modules_array = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
        $page = $this->_getParam('page', 1);  // Page id: Controll pagination.
        $pagesettingsTable = Engine_Api::_()->getItemTable('facebookse_mixsettings');
        $pagesettingsTableName = $pagesettingsTable->info('name');
        $pagesettingsSelect = $pagesettingsTable->select();
        $this->view->paginator = Zend_Paginator::factory($pagesettingsSelect);
        $this->view->paginator->setItemCountPerPage(50);
        $this->view->paginator->setCurrentPageNumber($page);
        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            foreach ($values as $key => $value) {
                if ($key == 'delete_' . $value) {
                    $obj = Engine_Api::_()->getItem('facebookse_mixsettings', $value);
                    if (empty($obj->is_delete)) {
                        $obj->delete();
                    }
                }
            }
        }
    }

    // Function: Manage Module - Creation Tab.
    public function moduleCreateAction() {

        $this->view->navigation = $this->getNavigation();
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $module_table = Engine_Api::_()->getDbTable('mixsettings', 'facebookse');
        $module_name = $module_table->info('name');
        $this->view->form = $form = new Facebookse_Form_Admin_Module();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            $resource_type = $values['resource_type'];
            $title = $values['title_items'];
            $module = $values['module'];

            $customCheck = $module_table->fetchRow(array('resource_type = ?' => $resource_type, 'module' => $module));
            if (!empty($customCheck)) {
                $itemError = Zend_Registry::get('Zend_Translate')->_("This ‘Content Module’ already exist.");
                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($itemError);
                return;
            }

            $values[] = array('module' => $module, 'enable' => '1', 'send_button' => '1', 'like_type' => 'like', 'like_faces' => '1', 'like_width' => '450', 'like_font' => '', 'like_color' => 'light', 'layout_style' => 'standard', 'default' => 0);

            $resourceTypeTable = Engine_Api::_()->getItemTable($resource_type);
            $primaryId = current($resourceTypeTable->info("primary"));
            if (!empty($primaryId)) {
                $values['resource_id'] = $primaryId;
            }

            if (!empty($resource_type)) {
                $values['resource_type'] = $resource_type;
            }
            //BEGIN TRANSACTION
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $table = Engine_Api::_()->getDbtable('mixsettings', 'facebookse');
                $row = $table->createRow();
                $values['default'] = '0';
                $row->setFromArray($values);
                $row->save();
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

        $manageModules = Engine_Api::_()->getItem('facebookse_mixsettings', $this->_getParam('mixsetting_id'));
        $this->view->navigation = $this->getNavigation();
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $this->view->form = $form = new Facebookse_Form_Admin_Moduleedit();

        $module_array = $manageModules->toArray();

        //SHOW PRE-FIELD FORM
        //IF NOT POST OR FORM NOT VALID THAN RETURN
        if (!$this->getRequest()->isPost()) {
            if (!$module_array['activityfeed_type']) {
                unset($module_array['activityfeedtype_text']);
                unset($module_array['streampublish_message']);
                unset($module_array['streampublish_story_title']);
                unset($module_array['streampublish_link']);
                unset($module_array['streampublish_caption']);
                unset($module_array['streampublish_description']);
                unset($module_array['streampublish_action_link_text']);
                unset($module_array['streampublish_action_link_url']);
            }
            $form->populate($module_array);
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
        if (empty($values['activityfeed_type']))
            unset($values['activityfeed_type']);

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {

            $manageModules->setFromArray($values);
            $manageModules->save();

            //UPDATING THE MODULES ENABLE OR DISABLE INFO IN FACEBOOKSEFEED TABLE ALSO IF IT EXISTS

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        return $this->_helper->redirector->gotoRoute(array('controller' => 'manage', 'action' => 'index'));
    }

    public function enabledContentTabAction() {
        $value = $this->_getParam('mixsetting_id');
        $value_type = explode('_', $value);
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        $content = Engine_Api::_()->getItemTable('facebookse_mixsettings')->fetchRow(array('mixsetting_id = ?' => $value_type[0]));
        try {
            if ($value_type[1] == 'advfb')
                $content->module_enable = !$content->module_enable;
            else
                $content->streampublishenable = !$content->streampublishenable;
            $content->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $this->_redirect('admin/facebookse/manage');
    }

    public function deleteModuleAction() {
        $this->_helper->layout->setLayout('admin-simple');
        $this->view->resource_type = $resource_type = $this->_getParam('resource_type');

        $mixsettingstable = Engine_Api::_()->getDbtable('mixsettings', 'facebookse');
        $sub_status_select = $mixsettingstable->fetchRow(array('resource_type = ?' => $resource_type));
        $this->view->module = $sub_status_select->module;

        if ($this->getRequest()->isPost()) {
            $custom_module = Engine_Api::_()->getItemTable('facebookse_mixsettings')->fetchRow(array('resource_type = ?' => $resource_type));
            if (!empty($custom_module)) {

                $custom_module->delete();
            }


            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
            ));
        }
    }

    public function getNavigation($active = false) {
        if (is_null($this->_navigation)) {
            $navigation = $this->_navigation = new Zend_Navigation();
            $navigation_auth = Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.navi.auth');

            if (Engine_Api::_()->user()->getViewer()->getIdentity()) {
                $navigation->addPage(array(
                    'label' => 'Global Settings',
                    'route' => 'facebookse_admin',
                    'module' => 'facebookse',
                    'controller' => 'admin-settings',
                    'action' => 'index',
                    'active' => $active
                ));

                if (!empty($navigation_auth)) {

                    $navigation->addPage(array(
                        'label' => 'FB Like Button Settings',
                        'route' => 'facebookse_admin_like_settings',
                        'module' => 'facebookse',
                        'controller' => 'admin-settings',
                        'action' => 'likesettings',
                    ));

//          $navigation->addPage(array(
//						'label' => 'FB Like Button View',
//						'route' => 'facebookse_admin_like_view',
//						'module' => 'facebookse',
//						'controller' => 'admin-settings',
//						'action' => 'likeview',          
//					));

                    $navigation->addPage(array(
                        'label' => 'Likes Integration',
                        'route' => 'facebookse_admin_like_init_settings',
                        'module' => 'facebookse',
                        'controller' => 'admin-settings',
                        'action' => 'likeintsettings',
                    ));

                    $navigation->addPage(array(
                        'label' => 'Facebook Comments Box Settings',
                        'route' => 'facebookse_admin_comment_settings',
                        'module' => 'facebookse',
                        'controller' => 'admin-settings',
                        'action' => 'commentsettings',
                    ));


//					$navigation->addPage(array(
//							'label' => 'FB Social Plugins Settings',
//							'route' => 'facebookse_admin_widget_settings',
//							'module' => 'facebookse',
//							'controller' => 'admin-settings',
//							'action' => 'widgetsettings'
//						));

                    $navigation->addPage(array(
                        'label' => 'Open Graph Settings',
                        'route' => 'facebookse_admin_manage_opengraph',
                        'module' => 'facebookse',
                        'controller' => 'admin-settings',
                        'action' => 'opengraph'
                    ));

                    $navigation->addPage(array(
                        'label' => 'Statistics',
                        'route' => 'facebookse_admin_manage_statistics',
                        'module' => 'facebookse',
                        'controller' => 'admin-settings',
                        'action' => 'statistics'
                    ));

                    $navigation->addPage(array(
                        'label' => 'Manage Modules',
                        'route' => 'facebookse_admin_manage_modules',
                        'module' => 'facebookse',
                        'controller' => 'admin-manage',
                        'action' => 'index'
                    ));
                }

                $navigation->addPage(array(
                    'label' => 'FAQ',
                    'route' => 'facebookse_admin_faq',
                    'module' => 'facebookse',
                    'controller' => 'admin-settings',
                    'action' => 'faq'
                ));
            }
        }
        return $this->_navigation;
    }

}

?>
