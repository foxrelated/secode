<?php


class Socialstore_Model_DbTable_Categories extends Engine_Db_Table {

	/**
	 * model table name
	 * @var string
	 */
	protected $_name = 'socialstore_categories';

	/**
	 * model class name
	 * @var string
	 */
	protected $_rowClass = 'Socialstore_Model_Category';

	public function getMultiOptions($pid = 0) {
		$select =  $this->select()->where('pid=?', $pid)->order('name');
		$options = array();
		foreach($this->fetchAll($select) as $item){
			if ($item->level == 0) {
				$options[''] =  '';
			}
			else {
				if ($item->getParent()) {
					$options[$item->getParent()->category_id] = '';
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
	public function deleteNode(Socialstore_Model_Category $node, $new_pid = 0){
		// delete any node that make seen from there.
		$value = $node->getIdentity();
		$key  = $node->getIndexKey($node->getLevel());
		
		// update products table and some thing here
		$descendant_ids =  $node->getDescendantIds();
		// TODO HERE
		
		$name =  $this->info('name');
		
		// check to get any new id.
		$this->getAdapter()->delete($name, array("$key=?"=>$value));

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
			$node-> pid =  $pid;
			
			$parent = $this -> find($pid) -> current();

			if($pid && !is_object($parent)){
				throw new Exception("invalid parent identity");
			}
			$node->save();
			
			// duplication where to add the parent code to this applied.
			if(is_object($parent)) {
				for($i = 0; $i <= Socialstore_Model_Category::MAX_LEVEL; ++$i) {
					$key = "p{$i}";
					$node -> {$key} = $parent -> {$key};
				}
				$node -> level = $parent -> level + 1;
				$node -> {"p" . $node -> level} = $node -> getId();
			} else {
				$node -> level = 1;
				$node -> p1 =  $node->getIdentity();
			}

			$node -> save();
			$db->commit();
			return $node;
		} catch(Exception $e) {
			$db -> rollBack();
			throw $e;
		}
	}
	
	public function getMaxLevel(){
		return Socialstore_Model_Category::MAX_LEVEL;
	}

}
