<?php
class Ynidea_AdminCategoryController extends Core_Controller_Action_Admin {
	protected $_paginate_params = array();
	public function init() {
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynidea_admin_main', array(), 'ynidea_admin_main_categories');
	}

	public function getDbTable() {
		return Engine_Api::_() -> getDbTable('categories', 'ynidea');
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
		$parentId = $this -> _getParam('parent_id', 0);
		$form = $this -> view -> form = new Ynidea_Form_Admin_Category();
		$form -> setAction($this -> getFrontController() -> getRouter() -> assemble(array()));
		$table = $this -> getDbTable();
		$node = $table -> getNode($parentId);
		//maximum 3 level category
		if ($node -> level > 2) {
			throw new Zend_Exception('Maximum 3 levels of category.');
		}
		// Check post
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
			// we will add the category
			$values = $form -> getValues();
			$user = Engine_Api::_() -> user() -> getViewer();
			$data = array('user_id' => $user -> getIdentity(), 'title' => $values["label"]);
			$table -> addChild($node, $data);
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'messages' => array('Add category successfully!')));
		}

		// Output
	}

	public function deleteCategoryAction() {

		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$id = $this -> _getParam('id');
		$this -> view -> category_id = $id;
		$table = $this -> getDbTable();
		$node = $table -> getNode($id);
		$categories = array();
		$table -> appendChildToTree($node, $categories);
		$level = $node -> level;
		unset($categories[0]);
		
		$hasIdea = $node -> checkHasIdea();
		$tableIdea = Engine_Api::_() -> getItemTable('ynidea_idea');
		if ($hasIdea || (count($categories) > 0)) 
		{
			$this -> view -> moveCates = $moveCates = $node -> getMoveCategoriesByLevel($node -> level);
		}
		// Check post
		if ($this -> getRequest() -> isPost()) {
			$move_category_id = $this -> _getParam('move_category');
			$node_id = $this -> getRequest() -> getPost('node_id', 0);
			// go through logs and see which classified used this category and set it to ZERO
			if (is_object($node)) {
				if ($hasIdea || (count($categories) > 0)) {
					if ($move_category_id != 'none') {
						//move businiesses to another category
						if ($hasIdea) 
						{
							//get ideas of deleted category
							$ideas = $tableIdea -> getIdeasByCategory($node -> category_id);
							foreach ($ideas as $idea) 
							{
								$idea -> category_id = $move_category_id;
								$idea -> save();
							}
						}
						//delete its type + node
						$table -> deleteNode($node);
						//move sub category
						$move_node = $table -> getNode($move_category_id);
						foreach ($categories as $item) 
						{
							
							$arr_item = $item -> toArray();
							unset($arr_item['category_id']);
							unset($arr_item['parent_id']);
							unset($arr_item['pleft']);
							unset($arr_item['pright']);
							
							$update_category_id = $item -> category_id;
							
							if($item -> level - $move_node -> level == 1)
							{
								$newNode = $table -> addChild($move_node, $arr_item);
								//udpate ideas with new category_id
								$list_ideas = $tableIdea -> getIdeasByCategory($update_category_id);
								foreach($list_ideas as $item_ideas)
								{
									$item_ideas -> category_id = $newNode -> category_id;
									$item_ideas -> save();
								}
								$newNode -> save();
								$move_node = $newNode;
								
							}
							else
							{
								while($item -> level - $move_node -> level < 1)
								{
									$move_node = $table -> getNode($move_node -> parent_id);
								}
								$newNode = $table -> addChild($move_node, $arr_item);
								//udpate ideas with new category_id
								$list_ideas = $tableIdea -> getIdeasByCategory($update_category_id);
								foreach($list_ideas as $item_ideas)
								{
									$item_ideas -> category_id = $newNode -> category_id;
									$item_ideas -> save();
								}
								$newNode -> save();
								$move_node = $newNode;
							}
						}
					} 
					else //delete all
					{
						//delete its ideas
						$ideas = $tableIdea -> getAllChildrenIdeasByCategory($node);
						if (count($ideas) > 0) {
							foreach ($ideas as $item) {
								foreach ($item->toArray() as $idea) {
									$delete_item = Engine_Api::_() -> getItem('ynidea_idea', $idea['idea_id']);
									if($delete_item)
										$delete_item -> delete();
								}
							}
						}
						$table -> deleteNode($node);
					}
				}
				//delete all if category has no sub or no ideas
				else
				{
					//delete its ideas
					$ideas = $tableIdea -> getAllChildrenIdeasByCategory($node);
					if (count($ideas) > 0) {
						foreach ($ideas as $item) {
							foreach ($item->toArray() as $idea) {
								$delete_item = Engine_Api::_() -> getItem('ynidea_idea', $idea['idea_id']);
								if($delete_item)
									$delete_item -> delete();
							}
						}
					}
					//delete its type + node
					$table -> deleteNode($node);
				}
			}
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'messages' => array('Delete category successfully!')));
		}
	}

	public function editCategoryAction() {

		// Must have an id
		if (!($id = $this -> _getParam('id'))) {
			throw new Zend_Exception('No identifier specified');
		}
		// Generate and assign form
		$category = Engine_Api::_() -> getItem('ynidea_category', $id);

		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$form = $this -> view -> form = new Ynidea_Form_Admin_Category( array('category' => $category));
		$form -> setAction($this -> getFrontController() -> getRouter() -> assemble(array()));

		// Check post
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
			// Ok, we're good to add field
			$values = $form -> getValues();

			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();

			try {
				// edit category in the database
				// Transaction
				$row = Engine_Api::_() -> getItem('ynidea_category', $values["id"]);
				$row -> title = $values["label"];
				$row -> save();
				$db -> commit();
			} catch( Exception $e ) {
				$db -> rollBack();
				throw $e;
			}
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'messages' => array('Edit category successfully!')));
		}

		$form -> setField($category);

		// Output
	}

	public function sortAction() {
		$table = $this -> getDbTable();
		$node = $table -> getNode($this -> _getParam('parent_id', 0));
		$categories = $node -> getChilren();
		$order = explode(',', $this -> getRequest() -> getParam('order'));
		foreach ($order as $i => $item) {
			$category_id = substr($item, strrpos($item, '_') + 1);
			foreach ($categories as $category) {
				if ($category -> category_id == $category_id) {
					$category -> order = $i;
					$category -> save();
				}
			}
		}
	}

}
