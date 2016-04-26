<?php

class Ynidea_Model_DbTable_Ideas extends Engine_Db_Table
{

    protected $_rowClass = 'Ynidea_Model_Idea';
	
	public function getIdeasByCategory($category_id) {
		$select = $this -> select() -> where('category_id = ?', $category_id);
		return $this -> fetchAll($select);
	}
	
	public function getAllChildrenIdeasByCategory($node) {
		$return_arr = array();
		$cur_arr = array();
		$list_categories = array();
		Engine_Api::_() -> getItemTable('ynidea_category') -> appendChildToTree($node, $list_categories);
		foreach ($list_categories as $category) {
			$tableIdea = Engine_Api::_() -> getItemTable('ynidea_idea');
			$select = $tableIdea -> select() -> where('category_id = ?', $category -> category_id);
			$cur_arr = $tableIdea -> fetchAll($select);
			if (count($cur_arr) > 0) {
				$return_arr[] = $cur_arr;
			}
		}
		return $return_arr;
	}
}
