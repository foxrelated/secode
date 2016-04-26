<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Groupbuy
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: AdminCategoryController.php
 * @author     Minh Nguyen
 */
class Groupbuy_AdminCategoryController extends Core_Controller_Action_Admin {
	protected $_paginate_params = array();
	public function init() {
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('groupbuy_admin_main', array(), 'groupbuy_admin_main_categories');
	}

	/**
	 *
	 *
	 * @return Groupbuy_Model_DbTable_Categories
	 */
	public function getDbTable() {
		return Engine_Api::_() -> getDbTable('categories', 'groupbuy');
	}

	public function indexAction() {
		$table = $this -> getDbTable();
		$node = $table -> getNode($this -> _getParam('parent_id', 0));
		$this -> view -> categories = $node -> getChilren();
		$this -> view -> category = $node;
	}

	public function addCategoryAction() {
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');

		// Generate and assign form
		$form = $this -> view -> form = new Groupbuy_Form_Admin_Category();
		$form -> setAction($this -> getFrontController() -> getRouter() -> assemble( array()));

		// Check post
		if($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
			// we will add the category
			$values = $form -> getValues();
			$table = $this -> getDbTable();
			$parentId = $this -> _getParam('parent_id', 0);
			$node = $table -> getNode($parentId);
			$user = Engine_Api::_() -> user() -> getViewer();
			$data = array('user_id' => $user -> getIdentity(), 'title' => $values["label"]);
			$table -> addChild($node, $data);
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}

		// Output
		$this -> renderScript('admin-category/form.tpl');
	}

	public function deleteCategoryAction() {
		
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$id = $this -> _getParam('id');     
		$this -> view -> category_id = $id;
		$options = Engine_Api::_()->getDbTable('categories','groupbuy')->getDeleteOptions($id);
		$table = $this -> getDbTable();
		$node = $table -> find($id) -> current();
		$this->view->usedCount =  $usedCount = $node->getUsedCount();
		if(!$options)
            $this->view->canNotDelete = true;
		$moveNode  = $this->view->moveNode = new Zend_Form_Element_Select('node_id', array(
			'label'=>'Category',
			'multiOptions'=> $options,
			'description'=>$this->view->translate('Select a category to relocale ').$usedCount. $this->view->translate(' item(s)') 			
		));      
		
		
			
		// Check post
		if($this -> getRequest() -> isPost()) {
			$node_id=  $this->getRequest()->getPost('node_id',0);
			// go through logs and see which classified used this category and set it to ZERO			
			if(is_object($node)) {
				$table -> deleteNode($node, $node_id);
			}
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}

		// Output
		$this -> renderScript('admin-category/delete.tpl');
	}

	public function editCategoryAction() {
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$form = $this -> view -> form = new Groupbuy_Form_Admin_Category();
		$form -> setAction($this -> getFrontController() -> getRouter() -> assemble( array()));

		// Check post
		if($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
			// Ok, we're good to add field
			$values = $form -> getValues();

			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();

			try {
				// edit category in the database
				// Transaction
				$row = Engine_Api::_() -> getItem('groupbuy_category', $values["id"]);

				$row -> title = $values["label"];
				$row -> save();
				$db -> commit();
			} catch( Exception $e ) {
				$db -> rollBack();
				throw $e;
			}
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}

		// Must have an id
		if(!($id = $this -> _getParam('id'))) {
			throw new Zend_Exception('No identifier specified');
		}

		// Generate and assign form
		$category = Engine_Api::_() -> getItem('groupbuy_category', $id);
		$form -> setField($category);

		// Output
		$this -> renderScript('admin-category/form.tpl');
	}

}
