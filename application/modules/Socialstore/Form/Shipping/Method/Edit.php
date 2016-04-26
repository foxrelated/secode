<?php

class Socialstore_Form_Shipping_Method_Edit extends Socialstore_Form_Shipping_Method_Create{
	public function init(){
		parent::init();
		
		$this->setTitle('Edit Shipping Method');
	}
}
