<?php

class Socialstore_Form_Product_Attribute_Edit extends Socialstore_Form_Product_Attribute_Add{
	public function init(){
		parent::init();
		
		$this->setTitle('Edit Attribute Set');
	}
}
