<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Grouppoll
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminLevelController.php 6590 2010-12-08 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Grouppoll_AdminLevelController extends Core_Controller_Action_Admin
{
  public function indexAction()
  { 

  	//TAB CREATION 
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('grouppoll_admin_main', array(), 'grouppoll_admin_main_level');

    //FETCH LEVEL ID 
    if ( null !== ($id = $this->_getParam('id')) ) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    }
		else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }

    if ( !$level instanceof Authorization_Model_Level ) {
      throw new Engine_Exception($this->view->translate('missing level'));
    }

		//FETCH LEVEL ID
    $id = $level->level_id;

    //GENERATE FORM
    $this->view->form = $form = new Grouppoll_Form_Admin_Settings_Level(array(
      'public' => ( in_array($level->type, array('public')) ),
      'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
    ));

		if (!empty($id)) {
			$form->level_id->setValue($id);
		}
		
    //GET AUTHORIZATION
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');

    if ( !$this->getRequest()->isPost() ) {
      $form->populate($permissionsTable->getAllowed('group', $id, array_keys($form->getValues())));
			$form->populate($permissionsTable->getAllowed('grouppoll_poll', $id, array_keys($form->getValues())));
      return;
    }
    
    if ( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    //GET POSTED VALUE
    $values = $form->getValues();
		$auth_gpcreate = array('auth_gpcreate' => $values['auth_gpcreate']);
		$gpcreate = array('gpcreate' => $values['gpcreate']);
		$gp_auth_vote = array('gp_auth_vote' => $values['gp_auth_vote']);
    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();
    $permissionsTable->setAllowed('group', $id, $auth_gpcreate);
    $permissionsTable->setAllowed('group', $id, $gpcreate);
    $permissionsTable->setAllowed('grouppoll_poll', $id, $gp_auth_vote);
    $db->commit();
  }
}
?>