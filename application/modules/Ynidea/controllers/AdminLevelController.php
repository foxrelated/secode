<?php
class Ynidea_AdminLevelController extends Core_Controller_Action_Admin
{
	public function indexAction()
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynidea_admin_main', array(), 'ynidea_admin_main_level');

		// Get level id
		if (null !== ($id = $this -> _getParam('id')))
		{
			$level = Engine_Api::_() -> getItem('authorization_level', $id);
		}
		else
		{
			$level = Engine_Api::_() -> getItemTable('authorization_level') -> getDefaultLevel();
		}

		if (!$level instanceof Authorization_Model_Level)
		{
			throw new Engine_Exception('missing level');
		}

		$id = $level -> level_id;

		// Make form
		$form = new Ynidea_Form_Admin_Settings_Level( array(
			'public' => ( in_array($level -> type, array('public'))),
			'moderator' => ( in_array($level -> type, array('admin', 'moderator'))
			),)
		);
		$form -> level_id -> setValue($id);
		$this -> view -> level_id = $id;
		// Populate data
		$permissionsTable = Engine_Api::_() -> getDbtable('permissions', 'authorization');
		$formSettingValues = $form->getSettingsValues();
		$idea_allowed = $permissionsTable -> getAllowed('ynidea_idea', $id, array_keys($formSettingValues['idea']));
		$ideaValues = array();
		foreach($idea_allowed as $key => $value) {
			$ideaValues['idea_' . $key] = $value;
		}
		$trophy_allowed = $permissionsTable -> getAllowed('ynidea_trophy', $id, array_keys($formSettingValues['trophy']));
		$trophyValues = array();
		foreach($trophy_allowed as $key => $value) {
			$trophyValues['trophy_' . $key] = $value;
		}
		$form -> populate(array_merge($ideaValues,$trophyValues));

		$this -> view -> form = $form;
		// Check post
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		// Check validitiy
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		// Process

		$settingValues = $form->getSettingsValues();

		$db = $permissionsTable -> getAdapter();
		$db -> beginTransaction();

		try
		{
			// Set permissions
			$permissionsTable -> setAllowed('ynidea_idea', $id, $settingValues['idea']);
			$permissionsTable -> setAllowed('ynidea_trophy', $id, $settingValues['trophy']);
			// Commit
			$db -> commit();
		}

		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
		$form -> addNotice($this -> view -> translate('Your changes have been saved.'));
	}

}
