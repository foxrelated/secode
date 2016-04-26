<?php

class Socialstore_Model_DbTable_Favourites extends Engine_Db_Table{
	
	/**
	 * model table name
	 * @var string
	 */
	protected $_name =  'socialstore_favourites';
	
	
	/**
	 * model class name
	 * @var string
	 */
	protected $_rowClass = 'Socialstore_Model_Favourite';
	
	
}
