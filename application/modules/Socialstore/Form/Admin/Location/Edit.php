<?php

class Socialstore_Form_Admin_Location_Edit extends Socialstore_Form_Admin_Location_Create{
	public function init(){
		parent::init();
		
		$this->setTitle('Edit Location');
	}
}
