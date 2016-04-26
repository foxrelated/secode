<?php


class Socialstore_Model_DbTable_Storecategories extends Engine_Db_Table {

	/**
	 * model table name
	 * @var string
	 */
	protected $_name = 'socialstore_storecategories';

	/**
	 * model class name
	 * @var string
	 */
	protected $_rowClass = 'Socialstore_Model_Storecategory';

	public function getMultiOptions($pid = 0) {
		$select =  $this->select()->where('parent_category_id=?', $pid);
		$options = array();
		foreach($this->fetchAll($select) as $item){
			if ($item->level == 0) {
				$options[''] =  '';
			}
			else {
				if ($item->getParent()) {
					$options[$item->getParent()->storecategory_id] = '';
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
	public function deleteNode(Socialstore_Model_Storecategory $node, $new_pid = 0){
		
		// TODO HERE
		$ids_object = $node->getDescendantIds();
    	$descendant_ids = array();
    	foreach ($ids_object as $id_ob) {
    		$descendant_ids[] = $id_ob->storecategory_id;
    	}
    	$descendant_ids[] = $item->storecategory_id;
		$name =  $this->info('name');
		$select = $this->select()->where('storecategory_id in (?)', $descendant_ids);
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

			$node -> setFromArray($data);
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
}
