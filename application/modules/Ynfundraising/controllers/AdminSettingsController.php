<?php
class Ynfundraising_AdminSettingsController extends Core_Controller_Action_Admin
{
	/**
	 * init check exist Ynidea plugin enable
	 *
	 */
	public function init()
  	{

	}
	public function indexAction()
	{
		// Make form
		$this->view->form = $form = new Ynfundraising_Form_Admin_Global;

		if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()))
		{
			$values = $form->getValues();

			foreach ($values as $key => $value)
			{
				Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
			}
			$form->addNotice('Your changes have been saved.');
		}

	}
}