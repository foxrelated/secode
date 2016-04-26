<?php
class Ynresponsivemetro_AdminManageBlocksController extends Core_Controller_Action_Admin
{
	public function init()
	{
	    $this -> view -> navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynresponsivemetro_admin_main', array(), 'ynresponsivemetro_admin_main_manage_blocks');
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
		$this -> renderScript('admin-manage-blocks/delete.tpl');
	}

	public function deletePhotoAction()
	{
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$block_id = $this -> _getParam('block_id');
		$this -> view -> block_id = $block_id;
		// Check post
		if ($this -> getRequest() -> isPost())
		{
			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();
			try
			{
				$metroblock = Engine_Api::_() -> getDbTable('metroblocks', 'ynresponsivemetro') -> findRow($block_id);
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
		$this -> renderScript('admin-manage-blocks/delete-photo.tpl');
	}

	public function deletePhotosAction()
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
			$tableName = Engine_Api::_() -> getDbtable('metroblocks', 'ynresponsivemetro') -> info('name');
			try
			{
				$db -> query("DELETE FROM $tableName WHERE block = $block");
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
		$this -> renderScript('admin-manage-blocks/delete.tpl');
	}

	public function editAction()
	{
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$block = $this -> _getParam('block');
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		// Create form
		$this -> view -> form = $form = new Ynresponsivemetro_Form_Admin_Metro_Edit();
		$url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this -> view -> url(array('controller' => 'files'), 'admin_default', true);
		$form -> addNotice($this -> view -> translate('Please go to <a href = "%s" target = "_blank">File & Media Manager</a> to upload icon.', $url));
		$form -> setTitle("Edit Block " . $block);
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
			// Create block
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

	public function addPhotoAction()
	{
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$block = $this -> _getParam('block');
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		// Create form
		$this -> view -> form = $form = new Ynresponsivemetro_Form_Admin_Metro_Create();
		$form -> removeElement('title');
		$form -> removeElement('description');
		$form -> removeElement('icon');
		$form -> removeElement('link');
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
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('The photo was added successfully.'))
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
