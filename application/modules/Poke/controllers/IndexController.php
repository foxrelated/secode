<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Poke
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: indexController.php 2010-11-27 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
class Poke_IndexController extends Core_Controller_Action_Standard
{

  public function indexAction() {

    if( !$this->_helper->requireUser()->isValid() ) return;

    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled();
    }

  }

  public function pokesettingsAction()
  {	
		$poke_field_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('poke.conn.setting');
		if(!empty($poke_field_check))
		{
			if( !$this->_helper->requireUser()->isValid() ) return;
			//CURRENT USER ID!
			$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
			// Get navigation
			$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
			->getNavigation('poke_main');
			// Make the object for connection setting form
			$this->view->form = $form = new Poke_Form_pokesettingform();
			//check the ID from poke table
			$table  = Engine_Api::_()->getItemTable('poke_setting');
			$select = $table->select()->where("user_id = $user_id");
			$fetch_record = $table->fetchAll($select);
			$userid_check = 0;
			foreach( $fetch_record as $row ){
				if($user_id==$row->user_id){
					$userid_check = 1;
					$currnt_user_poke_id = $row->setting_id;
				}
			}
			//IF ID NOT EXIST THEN INSERT THE DATA
			if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) && $userid_check==0) 
			{
				$connection_setting_table = Engine_Api::_()->getItemTable('poke_setting');
				$viewer = Engine_Api::_()->user()->getViewer();
				$values = $form->getValues();
				$check_value = $values['connection'];
				if($check_value==1){
					// Begin database transaction
					$db = $connection_setting_table->getAdapter();
					$db->beginTransaction();
					try{
						$connection_setting_row = $connection_setting_table->createRow();
						$connection_setting_row->setFromArray($values);
						$connection_setting_row->setting_id = $viewer->getIdentity();				
						$connection_setting_row->user_id = $viewer->getIdentity();
						$connection_setting_row->save();
						$db->commit();
					}
					catch( Exception $e ){
						$db->rollBack();
						throw $e;
					}   
				}        
        $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.')) 
        ));
			}
			elseif($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) && $userid_check==1){
				$values = $form->getValues();
				$check_value = $values['connection'];
				if ($check_value==0) {
					$user_setting = Engine_Api::_()->getItem('poke_setting', $currnt_user_poke_id);
					$user_setting->delete();
				}        
        $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.')) 
        ));
			}
		} else {			
			return $this->_forward('requireauth', 'error', 'core');
		}
  }
}
?>