<?php
class Ynresponsivemetro_AdminManageIntroductionController extends Core_Controller_Action_Admin
{
  public function init()
  {
      $this -> view -> navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynresponsivemetro_admin_main', array(), 'ynresponsivemetro_admin_main_manage_introduction');
  }
  
  public function indexAction()
  {
  	
  }
  
  public function deleteAction()
	{
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$block = $this -> _getParam('block');
		$this -> view -> block = $block;
		// Check post
		if ($this -> getRequest() -> isPost())
		{
			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();

			try
			{
				$metroblock = Engine_Api::_() -> getDbTable('metroblocks', 'ynresponsivemetro') -> getBlocks(array('block' => $block));
				if ($metroblock)
					$metroblock -> delete();
				$db -> commit();
			}
			catch (Exception $e)
			{
				$db -> rollBack();
				throw $e;
			}

			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => 10,
				'parentRefresh' => 10,
				'messages' => array('')
			));
		}
		// Output
		$this -> renderScript('admin-manage-introduction/delete.tpl');
	}
	public function editAction()
	{
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$block = $this -> _getParam('block');
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		// Create form
		$this -> view -> form = $form = new Ynresponsivemetro_Form_Admin_Metro_IntroductionEdit();
		$url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this -> view -> url(array('controller' => 'files'), 'admin_default', true);
		$form -> addNotice($this -> view -> translate('Please go to <a href = "%s" target = "_blank">File & Media Manager</a> to upload icon.', $url));
		$form -> addNotice("If you do not want to set color in color textbox, please kindly copy `transparent` and paste it into color textbox. Please kindly remove `transparent` before select color.");
		$metroblock = Engine_Api::_() -> getDbTable('metroblocks', 'ynresponsivemetro') -> getBlocks(array('block' => $block));
		if ($metroblock)
		{
			$form -> populate($metroblock -> toArray());
		}
		// Not post/invalid
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		$values = $form -> getValues();
		$db = Engine_Api::_() -> getDbtable('metroblocks', 'ynresponsivemetro') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			// Create event
			$table = Engine_Api::_() -> getDbtable('metroblocks', 'ynresponsivemetro');
			if (!$metroblock)
				$metroblock = $table -> createRow();
			$metroblock -> setFromArray($values);
			$metroblock -> block = $block;
			$metroblock -> save();

			// Add photo
			if (!empty($values['photo']))
			{
				$metroblock -> setPhoto($form -> photo);
			}
			// Commit
			$db -> commit();

			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => 10,
				'parentRefresh' => 10,
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('The block was added successfully.'))
			));
		}
		catch( Engine_Image_Exception $e )
		{
			$db -> rollBack();
			$form -> addError(Zend_Registry::get('Zend_Translate') -> _('The image you selected was too large.'));
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
	}
}
