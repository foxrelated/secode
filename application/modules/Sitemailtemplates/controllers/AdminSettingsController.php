<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemailtemplates
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 6590 2012-06-20 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemailtemplates_AdminSettingsController extends Core_Controller_Action_Admin {

    //ACTION FOR GLOBAL SETTINGS
    public function indexAction() {
        $onactive_disabled = array('sitemailtemplates_title_enable', 'sitemailtemplates_site_title', 'sitemailtemplates_icon_show', 'sitemailtemplates_icon1', 'logo_photo_preview', 'sitemailtemplates_header_color', 'sitemailtemplates_title_color', 'sitemailtemplates_bg_color', 'sitemailtemplates_footer1', 'sitemailtemplates_check_setting', 'testemail_demo', 'testemail_admin', 'submit');
        $this->view->textFlag = $textFlag = $this->_getParam('textFlag', 0);
        $afteractive_disabled = array('environment_mode', 'submit_lsetting');

        $this->view->isModsSupport = Engine_Api::_()->getApi('mail', 'sitemailtemplates')->isModulesSupport();

        $pluginName = 'sitemailtemplates';
        if (!empty($_POST[$pluginName . '_lsettings']))
            $_POST[$pluginName . '_lsettings'] = @trim($_POST[$pluginName . '_lsettings']);

        include APPLICATION_PATH . '/application/modules/Sitemailtemplates/controllers/license/license1.php';
    }

    //ACTION FOR FAQ
    public function faqAction() {
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitemailtemplates_admin_main', array(), 'sitemailtemplates_admin_main_faq');
    }

    //ACTION FOR MANAGE TEMPLATES
    public function manageAction() {
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitemailtemplates_admin_main', array(), 'sitemailtemplates_admin_main_manage');

        //FORM GENERATION
        $this->view->formFilter = $formFilter = new Sitemailtemplates_Form_Admin_Filter();

        //MAKE QUERY
        $sitemailtemplateTable = Engine_Api::_()->getDbTable('templates', 'sitemailtemplates');
        $tablesitemailtemplatesName = $sitemailtemplateTable->info('name');
        $select = $sitemailtemplateTable->select()
                ->from($tablesitemailtemplatesName, array('template_id', 'template_title', 'active_template', 'active_delete'));

        //GET PAGE COUNT
        $page = $this->_getParam('page', 1);

        $values = array();
        if ($formFilter->isValid($this->_getAllParams())) {
            $values = $formFilter->getValues();
        }

        foreach ($values as $key => $value) {
            if (null === $value) {
                unset($values[$key]);
            }
        }

        $values = array_merge(array(
            'order' => 'template_id',
            'order_direction' => 'DESC',
                ), $values);

        $this->view->assign($values);

        $select->order((!empty($values['order']) ? $values['order'] : 'template_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

        //GET PAGINATOR
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
        $this->view->paginator->setItemCountPerPage(20);
        $this->view->paginator = $paginator->setCurrentPageNumber($page);
    }

    //ACTION FOR CREATE TEMPLATE
    public function createAction() {
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitemailtemplates_admin_main', array(), 'sitemailtemplates_admin_main_manage');

        //FORM GENERATION
        $this->view->form = $form = new Sitemailtemplates_Form_Admin_Create();

        //CHECK POST FORM
        if ((!$this->getRequest()->isPost())) {
            return;
        }

        if ((!$form->isValid($this->getRequest()->getPost()))) {
            return;
        }

        $sitemailtemplateTable = Engine_Api::_()->getDbTable('templates', 'sitemailtemplates');
        $values = $this->getRequest()->getPost();
        if ($values['active_template'] == 1) {
            $sitemailtemplateTable->update(array('active_template' => 0));
        }

        //POPULATE FORM
        $form->populate($values);

        unset($values['logo_photo_preview']);

        //SAVE VALUES IN DATABASE
        $sitemailtemplateTable = $sitemailtemplateTable->createRow();
        $sitemailtemplateTable->setFromArray($values);
        $sitemailtemplateTable->save();
        $template_id = $sitemailtemplateTable->template_id;

        $validator = new Zend_Validate_EmailAddress();
        $validator->getHostnameValidator()->setValidateTld(false);

        //GET SET EMAIL ADDRESS OF THE ADMIN
        $emailAdmin = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;

        //GET SITE TITLE
        if (!empty($values['site_title'])) {
            $site_title = $values['site_title'];
        } else {
            $site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 1);
        }

        if (!empty($values['testemail_demo'])) {
            $contactAdminEmail = $values['testemail_admin'];
            if ($validator->isValid($contactAdminEmail)) {
                Engine_Api::_()->getApi('mail', 'core')->sendSystem($values['testemail_admin'], 'SITEMAILTEMPLATES_CONTACTS_EMAIL_NOTIFICATION', array(
                    'subject' => 'Test email to check your settings for Email Template',
                    'message' => 'This is a test email send to you to check the settings configured by you for Email Template.',
                    'site_title' => $site_title,
                    'template_id' => $template_id,
                    'email' => $emailAdmin,
                    'queue' => false));
            }
        }

        $form->testemail_demo->setValue(0);
        $form->addNotice('Your changes have been saved.');

        $this->_redirect("admin/sitemailtemplates/settings/manage");
    }

    //ACTION FOR EDIT TEMPLATE
    public function editAction() {
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitemailtemplates_admin_main', array(), 'sitemailtemplates_admin_main_manage');

        $template_id = $this->_getParam('template_id');

        //GET TEMPLATE INFO
        $this->view->sitemailtemplate = $sitemailtemplate = Engine_Api::_()->getItem('sitemailtemplates_templates', $template_id);

        //GENERATE EDIT FORM
        $this->view->form = $form = new Sitemailtemplates_Form_Admin_Edit();

        if (empty($sitemailtemplate->testemail_admin)) {
            $email = Engine_Api::_()->user()->getViewer()->email;
            $sitemailtemplate->testemail_admin = $email;
        }

        $form->populate($sitemailtemplate->toArray());

        //CHECK POST FORM
        if ((!$this->getRequest()->isPost())) {
            return;
        }

        if ((!$form->isValid($this->getRequest()->getPost()))) {
            return;
        }

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $this->getRequest()->getPost();

            //POPULATE FORM
            $form->populate($values);

            if ($sitemailtemplate->active_template) {
                unset($values['active_template']);
            }

            if (isset($values['active_template']) && $values['active_template'] == 1) {
                Engine_Api::_()->getDbTable('templates', 'sitemailtemplates')->update(array('active_template' => 0));
            }

            unset($values['logo_photo_preview']);
            $sitemailtemplate->setFromArray($values);
            $sitemailtemplate->save();
            $template_id = $sitemailtemplate->template_id;

            $validator = new Zend_Validate_EmailAddress();
            $validator->getHostnameValidator()->setValidateTld(false);

            //GET SET EMAIL ADDRESS OF THE ADMIN
            $emailAdmin = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;

            //GET SITE TITLE
            if (!empty($values['site_title'])) {
                $site_title = $values['site_title'];
            } else {
                $site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 1);
            }

            if (!empty($values['testemail_demo'])) {
                $contactAdminEmail = $values['testemail_admin'];
                if (!$validator->isValid($contactAdminEmail)) {
                    continue;
                }
                Engine_Api::_()->getApi('mail', 'core')->sendSystem($values['testemail_admin'], 'SITEMAILTEMPLATES_CONTACTS_EMAIL_NOTIFICATION', array(
                    'subject' => '[Header(member)]-[Footer(member)] Test email to check your settings for Email Template',
                    'message' => 'This is a test email send to you to check the settings configured by you for Email Template.',
                    'site_title' => $site_title,
                    'template_id' => $template_id,
                    'email' => $emailAdmin,
                    'member_type' => 'member',
                    'queue' => false));

                Engine_Api::_()->getApi('mail', 'core')->sendSystem($values['testemail_admin'], 'SITEMAILTEMPLATES_CONTACTS_EMAIL_NOTIFICATION', array(
                    'subject' => '[Header(non-member)]-[Footer(non-member)] Test email to check your settings for Email Template',
                    'message' => 'This is a test email send to you to check the settings configured by you for Email Template.',
                    'site_title' => $site_title,
                    'template_id' => $template_id,
                    'email' => $emailAdmin,
                    'member_type' => 'non-member',
                    'queue' => false));
            }

            $form->testemail_demo->setValue(0);
            $form->addNotice('Your changes have been saved.');

            $this->_redirect("admin/sitemailtemplates/settings/manage");
        }
    }

    //ACTION FOR DELETE TEMPLATE
    public function deleteAction() {
        //LAYOUT
        $this->_helper->layout->setLayout('admin-simple');

        //GET TEMPLATE ID
        $template_id = $this->_getParam('template_id');

        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                Engine_Api::_()->getDbtable('templates', 'sitemailtemplates')->delete(array('template_id =?' => $template_id));

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            Engine_Api::_()->getDbtable('MailTemplates', 'core')->update(array('template_id' => 0), array('template_id = ?' => $template_id));

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('')
            ));
        }
    }

    //ACTION FOR DELETE TEMPLATE
    public function multiDeleteAction() {
        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();

            //IF ADMIN CLICK ON DELETE SELECTED BUTTON
            if (!empty($values['delete'])) {
                foreach ($values as $key => $value) {
                    if ($key == 'delete_' . $value) {
                        $template_id = (int) $value;
                        Engine_Api::_()->getDbtable('templates', 'sitemailtemplates')->delete(array('template_id =?' => $template_id));
                        Engine_Api::_()->getDbtable('MailTemplates', 'core')->update(array('template_id' => 0), array('template_id = ?' => $template_id));
                    }
                }
            }
        }
        $this->_redirect("admin/sitemailtemplates/settings/manage");
    }

    //ACTION FOR ACTIVATE TEMPLATE
    public function activateTemplateAction() {

        //GET TEMPLATE ID
        $this->view->template_id = $template_id = $this->_getParam('template_id');

        if ($this->getRequest()->isPost()) {
            $sitemailtemplateTable = Engine_Api::_()->getItemTable('sitemailtemplates_templates');
            $sitemailtemplateTable->update(array('active_template' => 0));
            $sitemailtemplateTable->update(array('active_template' => 1), array('template_id = ?' => $template_id));

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('')
            ));
        }
    }

    //ACTION FOR SHOW DEFAULT TEMPLATE
    public function showTemplateAction() {

        //GET TEMPLATE ID
        $this->view->template_id = $this->_getParam('template_id');
    }

    public function readmeAction() {
        
    }

}
