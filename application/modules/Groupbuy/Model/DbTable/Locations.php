<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Categories.php
 * @author     Minh Nguyen
 */
class Groupbuy_Model_DbTable_Locations extends Groupbuy_Model_DbTable_Nodes {

	protected $_rowClass = 'Groupbuy_Model_Location';
	protected $_rootLabel = 'All Locations';
	protected $_relationTableName = 'engine4_groupbuy_location_relations';
	protected $_primary = 'location_id';

	/**
	 * new node with supply data will be added append to $node
	 * @param   Groupbuy_Model_Node  $node
	 * @throw Exception
	 */
	public function deleteNode(Groupbuy_Model_Node $node, $node_id = NULL) {

		$result = $node -> getDescendent(true);
		$db = $this -> getAdapter();
		$result = $this -> getDescendent($node -> getIdentity());
		$sql = 'update engine4_groupbuy_deals set location_id =  '.$node_id.'  where location_id in (' . implode(',', $result) . ',0)';
		$db -> query($sql);
		parent::deleteNode($node);
	}

}
