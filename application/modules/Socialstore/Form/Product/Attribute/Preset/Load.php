<?php
class Socialstore_Form_Product_Attribute_Preset_Load extends Engine_Form
{
  public function init()
  {
  	$this->setTitle('Load Attribute Preset');
  	$this->setMethod('post');
  	$this->addElement('Hidden', 'product_id');
  	if (Zend_Registry::isRegistered('store_id')) {
  		$store_id = Zend_Registry::get('store_id');
  	}
  	$sql = "Select attributepreset_id,preset_name FROM engine4_socialstore_attributepresets where store_id = $store_id";
	$db = Engine_Db_Table::getDefaultAdapter();
	$results = $db -> fetchPairs($sql);
  	$this->addElement('Select', 'attributepreset_id', array(
      'label' => 'Attribute Preset',
	  'multiOptions' => $results
   ));
  	    //Submit Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Save',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));
     //Cancel link
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onClick'=> 'javascript:parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
     //Display Group of Buttons
    $this->addDisplayGroup(array(
      'submit',
      'cancel',
    ), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper'
      ),
    ));
  }
}