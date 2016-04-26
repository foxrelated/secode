<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Groupbuy
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Categories.php
 * @author     Minh Nguyen
 */
class Groupbuy_Model_DbTable_Categories extends Groupbuy_Model_DbTable_Nodes {

	protected $_rowClass = 'Groupbuy_Model_Category';

	/**
	 * new node with supply data will be added append to $node
	 * @param   Groupbuy_Model_Node  $node
	 * @throw Exception
	 */
	public function deleteNode(Groupbuy_Model_Node $node, $node_id = NULL) {
		$result = $node -> getDescendent(true);

		$db = $this -> getAdapter();
		$sql = 'update engine4_groupbuy_deals set category_id =  '.$node_id.' where category_id in (' . implode(',', $result) . ',0)';
		$db -> query($sql);
		parent::deleteNode($node);
	}

	
}
