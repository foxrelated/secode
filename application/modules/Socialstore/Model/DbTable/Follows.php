<?php

class Socialstore_Model_DbTable_Follows extends Engine_Db_Table{
	
	/**
	 * model table name
	 * @var string
	 */
	protected $_name =  'socialstore_follows';
	
	
	/**
	 * model class name
	 * @var string
	 */
	protected $_rowClass = 'Socialstore_Model_Follow';
	
	
}
