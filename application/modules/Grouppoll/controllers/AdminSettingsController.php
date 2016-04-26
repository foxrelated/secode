<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Grouppoll
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 6590 2010-12-08 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Grouppoll_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
      $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
	      ->getNavigation('grouppoll_admin_main', array(), 'grouppoll_admin_main_settings');

      // generate the form
      $this->view->form  = $form = new Grouppoll_Form_Admin_Global();

      if( $this->getRequest()->isPost() ) {
	      $values = $this->getRequest()->getPost();
	      $authorization = Engine_Api::_()->getApi('settings', 'core')->grouppoll_auth;
	      if(empty($authorization)) {
		      $table = Engine_Api::_()->getItemTable('group');
		      $rName = $table->info('name');
		      $select = $table->select()->from($rName, array('group_id'));
		      $group_id_array = $select->query()->fetchAll();
		      foreach($group_id_array as $group_id) {
			      $group = Engine_Api::_()->getItem('group', $group_id['group_id']);
			      $auth = Engine_Api::_()->authorization()->context;
			      $roles = array('officer', 'member', 'registered', 'everyone');
			      $gpcreate_value = 'officer';
			      $gpcreate = array_search($gpcreate_value, $roles);
			      $officerList = $group->getOfficerList();
			      foreach( $roles as $i => $role ) {
				      if( $role === 'officer' ) {
					      $role = $officerList;
				      }
				      $auth->setAllowed($group, $role, 'gpcreate', ($i <= $gpcreate));
			      }
		      }
		      $values['grouppoll_auth'] = 1;
	      }
	      Engine_Api::_()->getApi('settings', 'core')->getSetting('grouppoll.isActivate', 0);
	      $is_error = 0;
	      if ($values['grouppoll_maxoptions'] == 0 || $values['grouppoll_title_turncation'] == 0) {
		      $is_error = 1;
	      }
	      if (($is_error == 1) && !empty($navi_auth_value)) {
		      $error = $this->view->translate('Filled value can not be zero !');
		      $this->view->status = false;
		      $error = Zend_Registry::get('Zend_Translate')->_($error);
		      $form->getDecorator('errors')->setOption('escape', false);
		      $form->addError($error);
		      return;
	      }
	      else {
			      foreach ($values as $key => $values) {
				      Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $values);
		      }
	      }
      }
   }

	//ACTION FOR FAQ
  public function faqAction()
  {
  	$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      	 ->getNavigation('grouppoll_admin_main', array(), 'grouppoll_admin_main_faq');
  }
}
?>