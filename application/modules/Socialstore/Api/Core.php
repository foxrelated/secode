<?php
class Socialstore_Api_Core extends Core_Api_Abstract {
	const IMAGE_WIDTH = 720;
	const IMAGE_HEIGHT = 720;

	const THUMB_WIDTH = 170;
	const THUMB_HEIGHT = 140;
	
	private static $_instance;
	
	protected $_modelStores;
	
	public function getModelStores(){
		if($this->_modelStores == NULL){
			$this->_modelStores = new Socialstore_Model_DbTable_SocialStores; 
		}
		return $this->_modelStores;
	}
		
	/**
	 * @return Socialstore_Api_Core
	 */
	static public function getInstance(){
		if(self::$_instance == NULL){
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	static public function getDefaultCurrency() {
		return Engine_Api::_() -> getApi('settings', 'core') -> getSetting('store.currency', 'USD');
	}

	static public function getCurrencySymbol() {
		$name = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('store.currency', 'USD');
		$currency = new Socialstore_Model_DbTable_Currencies;
		$select = $currency -> select() -> where('code = ?', $name);
		return $currency -> fetchRow($select) -> symbol;
	}

	static public function	getCategoryName($category_id) {
		$category = new Socialstore_Model_DbTable_Customcategories();
		$select = $category -> select() -> where('customcategory_id = ?', $category_id);
		return $category -> fetchRow($select) -> name;
	}
	/*public function getTaxPercentageByTaxId($tax_id) {
		$Taxs = new Socialstore_Model_DbTable_Vats;
		$tax = $Taxs -> find($tax_id) -> current();
		if(is_object($tax)) {
			return $tax -> value;
		}
		return 0.00;
	}*/
	
	public function getTaxPercentageByTaxId($tax_id) {
		$Taxs = new Socialstore_Model_DbTable_Taxes;
		$tax = $Taxs -> find($tax_id) -> current();
		if(is_object($tax)) {
			return $tax -> value;
		}
		return 0.00;
	}

	public function getStoreByOwnerId($owner_id) {
		$table = $this->getModelStores();
		$rName = $table -> info('name');
		$select = $table -> select();
		$select -> where('owner_id = ?', $owner_id)->where('deleted=0');
		//$select -> where('is_delete = 0');
		return $table -> fetchAll($select) -> current();
	}
	
	static public function isSandboxMode(){
		return  Engine_Api::_()->getApi('settings','core')->getSetting('store.mode',1);
	}
	
	public function getWidgetName($content_id) {
		$table = new Core_Model_DbTable_Content();
		$select = $table->select()->where('content_id = ?', $content_id);
		$result = $table->fetchRow($select);
		if (count($result) > 0) {
			return $result->name;
		} 
	}
    
    public function checkStoreGroupbuyConnection(){
        //Check module storegroupbuyconnection
        $module = 'storegroupbuyconnection';
        $modulesTable = Engine_Api::_()->getDbtable('modules', 'core');
        $mselect = $modulesTable->select()->where('enabled = ?', 1)->where('name  = ?', $module);
        $module_result = $modulesTable->fetchRow($mselect);
        if(count($module_result) > 0) 
        {
         return true;
        }
        else
        {
         return false;
        }        
    }
}
