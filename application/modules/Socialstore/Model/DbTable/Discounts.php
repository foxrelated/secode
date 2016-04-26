<?php
class Socialstore_Model_DbTable_Discounts extends Engine_Db_Table {

	/**
	 *
	 * Define currency table name
	 * @var   string
	 */
	protected $_name = 'socialstore_discounts';

	/**
	 * Define currency model class
	 * @var    string
	 */
	protected $_rowClass = 'Socialstore_Model_Discount';
}