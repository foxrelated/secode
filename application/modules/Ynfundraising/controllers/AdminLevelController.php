<?php
class Ynfundraising_AdminLevelController extends Core_Controller_Action_Admin
{
	public function init()
  	{
	}
	public function indexAction()
	{

		// Get level id
		if (null !== ($id = $this->_getParam ( 'id' ))) {
			$level = Engine_Api::_ ()->getItem ( 'authorization_level', $id );
		} else {
			$level = Engine_Api::_ ()->getItemTable ( 'authorization_level' )->getDefaultLevel ();
		}

		if (! $level instanceof Authorization_Model_Level) {
			throw new Engine_Exception ( 'missing level' );
		}

		$id = $level->level_id;

		// Make form
		$this->view->form = $form = new Ynfundraising_Form_Admin_Settings_Level ( array (
				'public' => (in_array ( $level->type, array (
						'public'
				) )),
				'moderator' => (in_array ( $level->type, array (
						'admin',
						'moderator'
				) ))
		) );
		$form->level_id->setValue ( $id );

		// Populate values
		$permissionsTable = Engine_Api::_ ()->getDbtable ( 'permissions', 'authorization' );
		$form->populate ( $permissionsTable->getAllowed ( 'ynfundraising_campaign', $id, array_keys ( $form->getValues () ) ) );

		// Check post
		if (! $this->getRequest ()->isPost ()) {
			return;
		}

		// Check validitiy
		if (! $form->isValid ( $this->getRequest ()->getPost () )) {
			return;
		}

		// Process

		$values = $form->getValues ();

		$db = $permissionsTable->getAdapter ();
		$db->beginTransaction ();

		try {
			// Set permissions
			$permissionsTable->setAllowed ( 'ynfundraising_campaign', $id, $values );

			// Commit
			$db->commit ();
		}

		catch ( Exception $e ) {
			$db->rollBack ();
			throw $e;
		}
		$form->addNotice ( 'Your changes have been saved.' );
	}
}