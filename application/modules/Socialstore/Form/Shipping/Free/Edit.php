<?php

class Socialstore_Form_Shipping_Free_Edit extends Socialstore_Form_Shipping_Free_Create{
	public function init(){
		parent::init();
		
		$this->setTitle('Edit Shipping Rule');
	}
}
