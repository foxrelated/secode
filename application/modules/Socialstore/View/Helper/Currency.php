<?php

class Socialstore_View_Helper_Currency extends Zend_View_Helper_Abstract {
		
	/**
	 * @var Zend_Currency
	 */
	protected static $_currency;
	
	
	/**
	 * currency option array
	 * @var   array
	 */
	protected static $_currencyOptions;
	
	protected static $_defaultCurrency;
	
	public static function getDefaultCurrency(){
		if(self::$_defaultCurrency == NULL){
			self::$_defaultCurrency =  Engine_Api::_()->getApi('core','Socialstore')->getDefaultCurrency();
		}
		return self::$_defaultCurrency;
	}
	
	/**
	 * @return  Zend_Currency 
	 */
	public static function getCurrency(){
		if(null === self::$_currency){
			self::$_currency = new Zend_Currency('en_US');
		}
		return self::$_currency;
	}
	
	protected static  function _buildCurrencyOption(){
		$table = new Socialstore_Model_DbTable_Currencies;
		$select =  $table->select();
		foreach($table->fetchAll($select) as $item){
			self::$_currencyOptions[$item->code] = array(
				'symbol'=>$item->symbol,
				'currency'=>$item->code,
				'precision'=>$item->precision,
				'display'=>$item->currencyDisplay(),
				'position'=>$item->currencyPosition(),
			);
		}
	}
	
	public static function getCurrencyOption($currency= NULL){
		if($currency == NULL){
			$currency  = self::getDefaultCurrency();
		}
		if(self::$_currencyOptions == NULL){
			self::_buildCurrencyOption();
		}
		if(isset(self::$_currencyOptions[$currency])){
			return self::$_currencyOptions[$currency];
		}
		return array();
	}
	/**
	 * magic function call
	 * @param   decimal   $value
	 * @param   int       $precision
	 * @return  string    
	 * @throws  Zend_Currency_Exception   IF INVALID VALUE 
	 */
	public function currency($value, $currency = NULL) {
        if(!$value)
          $value = 0 ;
		$string = self::getCurrency()->toCurrency($value, $this->getCurrencyOption($currency));
		
		// allow trim
		//$string =  rtrim(rtrim(rtrim($string,'0'),','),'.');
		return $string;	
	}
}