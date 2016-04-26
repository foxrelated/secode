<?php

class Socialstore_Form_Shipping_Rule_Edit extends Socialstore_Form_Shipping_Rule_Create{
	public function init(){
		parent::init();
		
		$this->setTitle('Edit Shipping Rule');
	}
}
