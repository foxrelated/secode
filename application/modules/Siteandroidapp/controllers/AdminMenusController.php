<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteandroidapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    AdminMenusController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteandroidapp_AdminMenusController extends Core_Controller_Action_Admin {

    public function manageAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteandroidapp_admin_main', array(), 'siteandroidapp_admin_api_menus');

        // Synchroniz
        if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("sitereview") && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("sitereviewlistingtype"))
                Engine_Api::_()->getApi('core', 'siteandroidapp')->synchroniseDashboardMenus();
                
        
        $table = Engine_Api::_()->getDbtable('menus', 'siteandroidapp');
        $select = $table->getSelect();
        $this->view->paginator = $table->fetchAll($select);
    }

    public function addMenuAction() {
        $this->view->form = $form = new Siteandroidapp_Form_Admin_Menu_Add();

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $table = Engine_Api::_()->getDbtable('menus', 'siteandroidapp');
        $values = $this->getRequest()->getPost();

        $row = $table->createRow();
        $row->setFromArray($values);
        $row->save();

        // Close the smoothbox
        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 10,
            'parentRefresh' => 10,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Successfully created!'))
        ));
    }

    public function editMenuAction() {
        $id = $this->_getParam('id');
        $table = Engine_Api::_()->getItem('siteandroidapp_menus', $id);

        $this->view->form = $form = new Siteandroidapp_Form_Admin_Menu_Edit();

        if (isset($table->default) && !empty($table->default)) {
            if(($table->name !== 'terms_of_service') && ($table->name !== 'privacy_policy'))
                $form->removeElement('url');
            
            $form->removeElement('icon');
        }

        $form->populate($table->toArray());

        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();

            $db = Engine_Api::_()->getDbtable('menus', 'siteandroidapp')->getAdapter();
            $db->beginTransaction();
            try {
                $table->setFromArray($values);
                $table->save();

                $db->commit();

                // Close the smoothbox
                $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => 10,
                    'parentRefresh' => 10,
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Successfully created!'))
                ));
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }

    /*
     * Change the order of menu/category from manage menus page.
     */

    public function orderAction() {
        if (!empty($_POST)) {
            foreach ($_POST as $key => $value) {
                if (strstr($key, "content_")) {
                    $keyArray = explode("content_", $key);

                    if (!empty($keyArray))
                        $image_id = end($keyArray);

                    if (!empty($image_id)) {
                        $obj = Engine_Api::_()->getItem('siteandroidapp_menus', $image_id);
                        $obj->order = $value;
                        $obj->save();
                    }
                }
            }
        }
    }

    /*
     * Give the information of menu/category.
     */

    public function infoAction() {
        $this->view->menu = Engine_Api::_()->getItem('siteandroidapp_menus', $this->_getParam('id', null));
    }

    /*
     * Change the status(Enable/Disable) of dashboard menu.
     */

    public function statusAction() {
        $this->_helper->layout->setLayout('admin-simple');
        $this->view->id = $id = $this->_getParam('id');
        $this->view->table = $table = Engine_Api::_()->getItem('siteandroidapp_menus', $id);

        if ($this->getRequest()->isPost()) {

            $table->status = !$table->status;
            $table->save();

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Successfully Changed!'))
            ));
        }
    }

    /*
     * Delete the menu / category from app dashboard.
     */

    public function deleteAction() {
        $this->_helper->layout->setLayout('admin-simple');
        $this->view->id = $id = $this->_getParam('id');

        if ($this->getRequest()->isPost()) {
            if (!empty($id))
                Engine_Api::_()->getItem('siteandroidapp_menus', $id)->delete();

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Deleted Successfully!'))
            ));
        }
    }

}
