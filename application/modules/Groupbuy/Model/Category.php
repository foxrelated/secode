<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Groupbuy
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Category.php
 * @author     Minh Nguyen
 */
class Groupbuy_Model_Category extends Groupbuy_Model_Node {
	
	
	// Properties
	public function getTable() {
		if(is_null($this -> _table)) {
			$this -> _table = Engine_Api::_() -> getDbtable('categories', 'groupbuy');
		}

		return $this -> _table;
	}

	public function setTitle($newTitle) {
		$this -> title = $newTitle;
		$this -> save();
		return $this;
	}

	public function getUsedCount() {
		$table = Engine_Api::_() -> getDbTable('deals', 'groupbuy');
		$rName = $table -> info('name');
		$ids =  $this->getDescendent(true);
		$select = $table -> select() -> from($rName) -> where($rName . '.category_id in (?)', $ids) -> where('is_delete = 0');
		$row = $table -> fetchAll($select);
		$total = count($row);
		return $total;
	}

	public function shortTitle() {
		return strlen($this -> title) > 20 ? (substr($this -> title, 0, 17) . '...') : $this -> title;
	}
	

}
