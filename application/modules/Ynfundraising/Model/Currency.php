<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfundraising
 * @author     YouNet Company
 */

class Ynfundraising_Model_Currency extends Core_Model_Item_Abstract 
{
	protected $_searchTriggers = false;
	public function currencyDisplay() {
		return Zend_Currency::USE_SYMBOL;
	}

	public function currencyPosition() {
		switch($this->position) {
			case 'Standard' :
				return Zend_Currency::STANDARD;
			case 'Left' :
				return Zend_Currency::LEFT;
			case 'Right' :
				return Zend_Currency::RIGHT;
		}
		return Zend_Currency::STANDARD;
	}
}