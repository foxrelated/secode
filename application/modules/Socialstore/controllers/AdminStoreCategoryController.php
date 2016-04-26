<?php

class Socialstore_AdminStoreCategoryController extends Core_Controller_Action_Admin {

	protected $_paginate_params = array();

	public function init() {
		parent::init();
		Zend_Registry::set('admin_active_menu', 'socialstore_admin_main_storecategories');		

	}

	/**
	 *
	 *
	 * @return Socialstore_Model_DbTable_Categories
	 */
	public function getDbTable() {
		return Engine_Api::_() -> getDbTable('storecategories', 'Socialstore');
	}

	public function indexAction() {
		$table = $this -> getDbTable();

		$pid = $this -> _getParam('pid', 0);
		$this->view->pid  = $pid = (int)$pid;

		$select = $table -> select() -> where('parent_category_id=?', $pid);
		$node = $table -> find($pid) -> current();
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$page = $request -> getParam('page', 1);
		$this -> view -> categories = $paginator = Zend_Paginator::factory($select);
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($page);
		$this -> view -> category = $node;
	}

	public function createAction() {
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');

		// Generate and assign form
		$form = $this -> view -> form = new Socialstore_Form_Admin_Category_Create();
		$pid= $this -> _getParam('pid', 0);
		

		// Check post
		$req = $this -> getRequest();
		
		
		if($req-> isPost() && $form -> isValid($req-> getPost())) {
			// we will add the category
			$data = $form -> getValues();
			$table = $this -> getDbTable();			
			$node = $table -> addNode($data, $pid);
			
			if(is_object($node)){
				$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
			}else{
				$form->addError('An error occurs');
			}
				
		}
		// Output
		$this -> renderScript('admin-store-category/form.tpl');
	}
	
	public function editAction() {
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$form = $this -> view -> form = new Socialstore_Form_Admin_Category_Edit();

		$req =  $this->getRequest();
		
		$id =  $this->_getParam('id',0);
		
		$table = $this->getDbTable();
		
		$item = $table->find($id)->current();
		
		if(!is_object($item)){
			$form->setError("Category not found.");
			return ;
		}
		
		if($req->isGet()){
			$form->populate($item->toArray());
		}				
		
		
		// Check post
		if($req-> isPost() && $form -> isValid($req-> getPost())) {
			// Ok, we're good to add field
			$item->name =  $form->getValue('name');
			$item->save();
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}

		// Output
		$this -> renderScript('admin-store-category/form.tpl');
	}
	public function deleteAction() {

		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$id = $this -> _getParam('id');
		$this -> view -> category_id = $id;
		$table = $this -> getDbTable();
		$node = $table -> find($id) -> current();
		
		if(!is_object($node)){
			return ;
		}
		$row = $node->getUsedCount();
		$this->view->usedCount = $total = count($row);
		if ($total > 0) {
			$this->view->form = $form = new Socialstore_Form_Admin_Category_StoreCatChange();
		}
		$req  = $this->getRequest();
		
		
		$post = $this -> getRequest() -> getPost();

		
		// Check post
		if($req-> isPost()) {
			if ($total > 0) {
				if(!$form -> isValid($post)) {
					return;
				}
				$new_category = $form->getValue('category_id');
				$ids_object = $node->getDescendantIds();
		    	$ids_array = array();
		    	foreach ($ids_object as $id_ob) {
		    		$ids_array[] = $id_ob->storecategory_id;
		    	}
		    	$ids_array[] = $node->storecategory_id;
				if (in_array($new_category, $ids_array)) {
					return $form->addError('You cannot select deleted category or sub-categories of deleted Category!');
				}
				if (!empty($row)) {
					$CustomCategories = new Socialstore_Model_DbTable_Customcategories;
					foreach ($row as $store) {
						$old_cat = $store->category_id;
						$store->category_id = $new_category;
						$store->save();
						$select = $CustomCategories->select()->where('store_category_id = ?', $old_cat);
						$custom_cats = $CustomCategories->fetchAll($select);
						if (count($custom_cats) > 0) {
							foreach ($custom_cats as $custom_cat) {
								$custom_cat->store_category_id = $new_category;
								$custom_cat->save();
							}
						}
						$products = $store->getProductsOfStore();
						if (count($products) > 0) {
							foreach ($products as $product) {
								$product->storecategory_id = $new_category;
								$product->save();
							}
						}
					}
				}
			}
			$table -> deleteNode($node, 0);
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}

		// Output
		$this -> renderScript('admin-store-category/delete.tpl');
	}

}
