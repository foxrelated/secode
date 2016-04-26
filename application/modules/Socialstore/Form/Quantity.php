<?php
class Socialstore_Form_Quantity extends Engine_Form{
	
	/**
	 * 
	 * 
	 * @var unknown_type
	 */
	protected $_product;
	
	public function setProduct(Socialstore_Model_Product $product){
		$this->_product =  $product;
	}
	
	public function getProduct(){
		return $this->_product;
	}
	
    public function init()
    {
       //Set Form Informations
    $this -> setAttribs(array('class' => 'global_form_popup','method' => 'post'))
          -> setTitle('Item Quantity')
		  ;
	
	 $product = $this->getProduct();
     $discounts = $product->getDiscount();
     if (($discounts) && (count($discounts) > 0)) {
     	$currency = Socialstore_Api_Core::getCurrencySymbol();
     	$text = $this->getTranslator()->translate("Discount Options: <br />");
     	foreach ($discounts as $discount) {
     		$text.= $this->getTranslator()->translate("Buy")." ".$discount->quantity." ".$this->getTranslator()->translate("at")." ".$currency.$discount->price."<br />";
     	}
     	
     	$description = $text;
     	//$this->addDecorator('HtmlTag', array('tag' => 'br'));
     	$this->setDescription($description);
     	$this->loadDefaultDecorators();
    	$this->getDecorator('Description')->setOption('escape', false);
     }
	 $this->addElement('Hidden', 'option');
	 $this->addElement('Text', 'quantity', array(
      'label' => 'Quantity',
      'allowEmpty' => false,
      'required'=>true,
      'value'=>1,
      'title' => '',             
      //'description' => '0 means not required',  
      'validators' => array(
        array('Int', true),
       	array('GreaterThan',true,array(0))
       
    )));
    
        // Buttons
    $this->addElement('Button', 'checkout', array(
      'label' => 'Check Out',
      'type' => 'submit',
      'value' => 'checkout',
      //'ignore' => true,
      'decorators' => array('ViewHelper')
    ));
    
    $this->addElement('Button', 'submit', array(
      'label' => 'Continue Shopping',
      'type' => 'submit',
      	'value' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));
	
    
    
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
	

    $this->addDisplayGroup(array('checkout', 'submit', 'cancel'), 'buttons');
		
	}

}
