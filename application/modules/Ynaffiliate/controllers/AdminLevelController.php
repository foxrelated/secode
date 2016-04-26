<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: AdminLevelController.php 7244 2010-09-01 01:49:53Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Ynaffiliate_AdminLevelController extends Core_Controller_Action_Admin {

   public function indexAction() {
      $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
              ->getNavigation('ynaffiliate_admin_main', array(), 'ynaffiliate_admin_main_level');

      // Get level id
      if (null !== ($id = $this->_getParam('id'))) {
         $level = Engine_Api::_()->getItem('authorization_level', $id);
      } else {
         $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
      }

      if (!$level instanceof Authorization_Model_Level) {
         throw new Engine_Exception('missing level');
      }

      $id = $level->level_id;

      // Make form
      $this->view->form = $form = new Ynaffiliate_Form_Admin_Settings_Level();
      $form->level_id->setValue($id);
      
//      $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
//      
//      $form->populate($permissionsTable->getAllowed('ynaffiliate', $id, array_keys($form->getValues())));
//
//      if (!$this->getRequest()->isPost()) {
//         $form->populate($permissionsTable->getAllowed('ynaffiliate', $id, array_keys($form->getValues())));
//         return;
//      }
//
//      // Check validitiy
//      if (!$form->isValid($this->getRequest()->getPost())) {
//         return;
//      }
//
//
//      $values = $form->getValues();
//
//      $db = $permissionsTable->getAdapter();
//      $db->beginTransaction();
//
//      try {
//         // Set permissions
//         $permissionsTable->setAllowed('ynaffiliate', $id, $values);
//
//         // Commit
//         $db->commit();
//      } catch (Exception $e) {
//         $db->rollBack();
//         throw $e;
//      }
      $form->addNotice('Your changes have been saved.');
   }

}