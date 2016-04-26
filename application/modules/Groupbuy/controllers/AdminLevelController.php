<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Groupbuy
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: AdminLevelController.php
 * @author     Minh Nguyen
 */
class Groupbuy_AdminLevelController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('groupbuy_admin_main', array(), 'groupbuy_admin_main_level');

    // Get level id
    if( null !== ($id = $this->_getParam('id')) ) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }

    if( !$level instanceof Authorization_Model_Level ) {
      throw new Engine_Exception('missing level');
    }

    $id = $level->level_id;

    // Make form
    $form = new Groupbuy_Form_Admin_Settings_Level(array(
      'public' => ( in_array($level->type, array('public')) ),
      'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
    ));
    $form->level_id->setValue($id);
    $this->view->level_id = $id;
    // Populate data
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $form->populate($permissionsTable->getAllowed('groupbuy_deal', $id, array_keys($form->getValues())));
    if($form->commission)
    {
        $mtable  = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $msselect = $mtable->select()
                    ->where("type = 'groupbuy_deal'")
                    ->where("level_id = ?",$id)
                    ->where("name = 'commission'");
        $mallow_s = $mtable->fetchRow($msselect);
        if (!empty($mallow_s))
            $max_s = $mallow_s['value'];
        $max_s_get = $form->commission->getValue();
        if ($max_s_get < 1)
            $form->commission->setValue($max_s);
    }
     $this->view->form = $form;
    // Check post
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
      // Set permissions
      $permissionsTable->setAllowed('groupbuy_deal', $id, $values);

      // Commit
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    $form->addNotice($this->view->translate('Your changes have been saved.'));
  }

}