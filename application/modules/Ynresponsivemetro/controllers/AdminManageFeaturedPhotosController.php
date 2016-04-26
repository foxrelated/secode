<?php
class Ynresponsivemetro_AdminManageFeaturedPhotosController extends Core_Controller_Action_Admin
{
  public function init()
  {
      $this -> view -> navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynresponsivemetro_admin_main', array(), 'ynresponsivemetro_admin_main_manage_featured_photos');
  }
  
 public function indexAction() 
	{
		if ($this -> getRequest() -> isPost()) 
		{
			$values = $this -> getRequest() -> getPost();
			foreach ($values as $key => $value) 
			{
				if ($key == 'delete_' . $value) 
				{
					$block = Engine_Api::_() -> getItem('ynresponsivemetro_metroblock', $value);
					$block -> delete();
				}
			}
		}
		$page = $this -> _getParam('page', 1);
		$this -> view -> paginator = Engine_Api::_() -> getDbTable('metroblocks', 'ynresponsivemetro') -> getFeaturedPhotosPaginator();
		$this -> view -> paginator -> setItemCountPerPage(10);
		$this -> view -> paginator -> setCurrentPageNumber($page);
	}

	public function deleteAction() {
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$id = $this -> _getParam('id');
		$this -> view -> block_id = $id;
		// Check post
		if ($this -> getRequest() -> isPost()) {
			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();

			try {
				$metroblock = Engine_Api::_() -> getItem('ynresponsivemetro_metroblock', $id);
				$metroblock -> delete();
				$db -> commit();
			} catch (Exception $e) {
				$db -> rollBack();
				throw $e;
			}

			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}
		// Output
		$this -> renderScript('admin-manage-featured-photos/delete.tpl');
	}

	public function createAction() {
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		// Create form
		$this -> view -> form = $form = new Ynresponsivemetro_Form_Admin_Metro_Create();
		$form -> removeElement('icon');
		// Not post/invalid
		if (!$this -> getRequest() -> isPost()) {
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}
		$values = $form -> getValues();
		$table = Engine_Api::_() -> getDbtable('metroblocks', 'ynresponsivemetro');
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try {
			// Create event
			$metroblock = $table -> createRow();
			$metroblock -> setFromArray($values);
			$metroblock -> save();

			// Add photo
			if (!empty($values['photo'])) {
				$metroblock -> setPhoto($form -> photo);
			}
			// Commit
			$db -> commit();

			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('The photo was added successfully.'))));
		} catch( Engine_Image_Exception $e ) {
			$db -> rollBack();
			$form -> addError(Zend_Registry::get('Zend_Translate') -> _('The image you selected was too large.'));
		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}
	}

	public function editAction() 
	{
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$block_id = $this -> getRequest() -> getParam('id');
		$metroblock = Engine_Api::_() -> getItem('ynresponsivemetro_metroblock', $block_id);
		// Create form
		$this -> view -> form = $form = new Ynresponsivemetro_Form_Admin_Metro_Edit();
		$form -> removeElement('icon');
		if (!$this -> getRequest() -> isPost()) 
		{
			$form -> populate($metroblock -> toArray());
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		// Process
		$values = $form -> getValues();

		// Process
		$db = Engine_Api::_() -> getDbTable('metroblocks', 'ynresponsivemetro') -> getAdapter();
		$db -> beginTransaction();

		try {
			if (!empty($values['photo'])) 
			{
				$metroblock -> setPhoto($form -> photo);
			}
			$metroblock -> setFromArray($values);
			$metroblock -> save();
			// Commit
			$db -> commit();
		} catch( Engine_Image_Exception $e ) {
			$db -> rollBack();
			$form -> addError(Zend_Registry::get('Zend_Translate') -> _('The image you selected was too large.'));
		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}
		$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('The photo was edited successfully.'))));
	}

}
