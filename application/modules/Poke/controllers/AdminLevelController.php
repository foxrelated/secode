<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Poke
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: AdminLevelController.php 2010-11-27 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
class Poke_AdminLevelController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
  	$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('poke_admin_main', array(), 'poke_admin_main_level');
    
    //Get level id
    if( null !== ($id = $this->_getParam('id')) ) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } 
    else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }

    if( !$level instanceof Authorization_Model_Level ) {
      throw new Engine_Exception('missing level');
    }
		
    //Takine id
    $id = $level->level_id;
    if(!empty($id)) {
			$this->view->send = Engine_Api::_()->authorization()->getPermission($id, 'poke', 'send');
		} 
    
    //Make form
    $this->view->form = $form = new Poke_Form_Admin_Level(array(
      'public' => ( in_array($level->type, array('public')) ),
      'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
    ));
    $form->level_id->setValue($id);

    //Populate values
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $form->populate($permissionsTable->getAllowed('poke', $id, array_keys($form->getValues())));

    //Check post
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    // Check validitiy
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    $values = $form->getValues();
		$db = $permissionsTable->getAdapter();
    $db->beginTransaction();
		try
    {
	$permissionsTable->setAllowed('poke', $id, $values);

      // Commit
      $db->commit();
    }
		catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
	  return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }
}
?>