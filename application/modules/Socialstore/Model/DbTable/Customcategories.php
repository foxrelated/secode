<?php


class Socialstore_Model_DbTable_Customcategories extends Engine_Db_Table {

	/**
	 * model table name
	 * @var string
	 */
	protected $_name = 'socialstore_customcategories';

	/**
	 * model class name
	 * @var string
	 */
	protected $_rowClass = 'Socialstore_Model_Customcategory';

	public function getMultiOptions($pid = 0, $store_id = null) {
		$select =  $this->select()->where('parent_category_id=?', $pid);
		if ($store_id != null) {
			$select->where('store_id = ?', $store_id);
		}
		$options = array();
		foreach($this->fetchAll($select) as $item){
			if ($item->level == 0) {
				$options[''] =  '';
			}
			else {
				if ($item->getParent()) {
					$options[$item->getParent()->customcategory_id] = '';
				}
				else {
					$options[''] = '';
				} 
			}
			$options[$item->getIdentity()] = $item->getName();
		}
		return $options;
		
	}
	
	/**
	 * @param  Socialstore_Model_Category  $node
	 * @param  int                   $new_pid
	 */
	public function deleteNode(Socialstore_Model_Customcategory $node, $new_pid = 0){
		
		$ids_object = $node->getDescendantIds();
    	$descendant_ids = array();
    	foreach ($ids_object as $id_ob) {
    		$descendant_ids[] = $id_ob->customcategory_id;
    	}
		$descendant_ids[] = $node->customcategory_id;
		// TODO HERE
		$name =  $this->info('name');
		$select = $this->select()->where('customcategory_id in (?)', $descendant_ids);
		$results = $this->fetchAll($select);
		foreach ($results as $result) {
			$result->delete();
		}
		$node->delete();
	}
	
	public function addNode($data, $pid = 0) {
		/**
		 * check parent id is exists or not.
		 */

		$db = $this -> getAdapter();
		$db -> beginTransaction();
		try {

			$pid = (int)$pid;
			// check if exists;
			$node = $this -> fetchNew();

			$node->name = $data['name'];
			$node->store_category_id = $data['category_id'];
			$node->store_id = $data['store_id'];
			$node-> parent_category_id =  $pid;
			
			$parent = $this -> find($pid) -> current();

			if($pid && !is_object($parent)){
				throw new Exception("invalid parent identity");
			}
			$node->save();
			
			// duplication where to add the parent code to this applied.
			if(is_object($parent)) {
				$node -> level = $parent -> level + 1;
			} else {
				$node -> level = 1;
			}

			$node -> save();
			$db->commit();
			return $node;
		} catch(Exception $e) {
			$db -> rollBack();
			throw $e;
		}
	}
	
	public function getMaxLevel() {
		$select = $this->select();
		$table_name = $this->info('name');
		$select->from($table_name, 'MAX(level) as maxLev');
		$result = $this->fetchRow($select);
		return $result['maxLev'];
	}
	
	public function getName($category_id) {
		$select = $this->select()->where('customcategory_id = ?', $category_id);
		$result = $this->fetchRow($select);
		if ($result) {
			return $result->name;
		}
	}
}
