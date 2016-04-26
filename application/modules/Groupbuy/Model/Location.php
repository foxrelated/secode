<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Category.php
 * @author     Minh Nguyen
 */
class Groupbuy_Model_Location extends Groupbuy_Model_Node {
	public function getUsedCount() {
		$table = Engine_Api::_() -> getDbTable('deals', 'groupbuy');
		$rName = $table -> info('name');
		$ids =  $this->getDescendent(true);
		$select = $table -> select() -> from($rName) -> where($rName . '.location_id IN (?)', $ids) -> where('is_delete = 0');
		$row = $table -> fetchAll($select);
		$total = count($row);
		return $total;
	}

	public function shortTitle() {
		return strlen($this -> title) > 20 ? (substr($this -> title, 0, 17) . '...') : $this -> title;
	}
	public function getDescendent($include_own = true){
		return $this->_table->getDescendent($this, $include_own);
	}
	
}
