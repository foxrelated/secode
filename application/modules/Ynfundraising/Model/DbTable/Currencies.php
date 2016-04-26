<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Yndonation
 * @author     YouNet Company
 */

class Ynfundraising_Model_DbTable_Currencies extends Engine_Db_Table {
	protected $_rowClass = 'Ynfundraising_Model_Currency';

	public static function getMultiOptions() {
		$t = new self;
		$select = $t -> select() -> where('status=?', 'Enable') -> order('name');
		$result = array();
		$result[''] = '';
		foreach($t->fetchAll($select) as $item) {
			$result[$item -> code] = $item -> name;
		}
		return $result;
	}

	/**
	 * retriev currecy object
	 * @var  string  $name
	 * return Socialstore_Model_Currency
	 */
	public function getCurrency($name = NULL) {
		if($name == NULL) {
			$name = Engine_Api::_() -> getApi('core','ynfundraising') -> getDefaultCurrency();
		}
		$item = $this -> find($name) -> current();
		if(!is_object($item)) {
			$name = Engine_Api::_() -> getApi('core','ynfundraising') -> getDefaultCurrency();
			$item = $this -> find($name) -> current();
		}
		return $item;

	}
	public function getCurrencyName($code = NULL) {
		/*if($name == NULL) {
			$name = Engine_Api::_() -> getApi('core','Socialstore') -> getDefaultCurrency();
		}*/
		//$item = $this -> find($code) -> current();
		$table = new self;
	    $rName = $table->info('name');
	    $select = $table->select()->from($rName)  ;
	    $select->where('code = ?', $code);
	    $item = $table->fetchRow($select);
		/*if(!is_object($item)) {
			$name = Engine_Api::_() -> getApi('core','Socialstore') -> getDefaultCurrency();
			$item = $this -> find($name) -> current();
		}*/
		return $item->name;

	}


}