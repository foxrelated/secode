<?php 
class Socialstore_Form_Attribute_Edit extends Socialstore_Form_Attribute_Create
{
  public function init()
  {
  	parent::init();
	$this->setTitle('Edit Attribute');
	$this->removeElement('type');
  }
}