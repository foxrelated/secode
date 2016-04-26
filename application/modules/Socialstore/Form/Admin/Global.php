<?php


class Socialstore_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    
	$currency = Socialstore_Api_Core::getDefaultCurrency();
	
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');
      $this->addElement('Radio', 'store_mode', array(
        'label' => '*Enable Test Mode?',
        'description' => 'Allow admin to test Store by using development mode?',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('store.mode', 1),
      )); 

	 $this->addElement('select', 'store_currency', array(
        'label' => 'Default Currency',
        'required'=>true,
        'multiOptions' => Socialstore_Model_DbTable_Currencies::getMultiOptions(),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('store.currency', 'USD'),
      ));
	  
	  $this->addElement('Radio', 'store_guestpurchase', array(
        'label' => 'Guests Purchase Products?',
        'description' => 'Allow guests to purchase products?',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('store.guestpurchase', 0),
      ));
      
      $this->addElement('Radio', 'store_rate', array(
        'label' => 'Owners Rate Store?',
        'description' => 'Allow sellers to rate their own stores?',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('store.rate', 0),
      ));
	  
	 $this->addElement('Radio', 'store_product_rate', array(
        'label' => 'Owners Rate Product?',
        'description' => 'Allow sellers to rate their own products?',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('store.product.rate', 0),
      ));
	
      
    $this->addElement('Text', 'store_minrequest',array(
	      'label'=>'Minimum Request Amount For Sellers',
	      'title' => 'Minimum Request Amount For Sellers',  
	      'description' => $currency,
	      'filters' => array(
	        new Engine_Filter_Censor(),
	      ),
	     'value'=> Engine_Api::_()->getApi('settings', 'core')->getSetting('store.minrequest', 100.00),
    ));
    $this->addElement('Text', 'store_maxrequest',array(
	      'label'=>'Maximum Request Amount For Sellers',
	      'title' => 'Maximum Request Amount For Sellers',  
	      'description' => $currency,
	      'filters' => array(
	        new Engine_Filter_Censor(),
	      ),
	     'value'=> Engine_Api::_()->getApi('settings', 'core')->getSetting('store.maxrequest', 1000.00),
    ));  
      
    $this->addElement('Text', 'store_page', array(
      'label' => 'Number Of Stores Per Page',
      'description' => 'How many store will be shown per page? (Enter a number between 1 and 999)',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('store.page', 10),
    ));

    $this->addElement('Text', 'store_product_page', array(
      'label' => 'Number Of Products Per Page',
      'description' => 'How many products will be shown per page? (Enter a number between 1 and 999)',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('store.product.page', 10),
    ));
	
	$this->addElement('Text', 'store_orderidlength',array(
	      'label'=>'OrderID Character Length',
	      'description' => 'How many characters do you want for length of an Order ID? (Enter a number between 4 and 10)',
	      'filters' => array(
	        'StringTrim'
	      ),
	     'value'=> Engine_Api::_()->getApi('settings', 'core')->getSetting('store.orderidlength', 8),
	     'validators'=>array(
	     	'Int',
		 	array('Between',true,array('min'=>4,'max'=>10))
		 ),
    )); 
	
    $this->addElement('Text', 'store_pathname',array(
	      'label'=>'Replace URL text',
	      'description' => 'Please fill in the text that you want to appear in URL instead of "socialstore"',
	      'filters' => array(
	        'StringTrim'
	      ),
	     'value'=> Engine_Api::_()->getApi('settings', 'core')->getSetting('store.pathname', 'socialstore'),
    )); 
	//MinhNC add field support Deal Request
	if(Engine_Api::_()->socialstore()->checkStoreGroupbuyConnection())
	{
	    $default_seller_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller'=>'help'), 'socialstore_extended', true);    
	    $this->addElement('Text', 'store_sellerpolicy',array(
	          'label'=>'URL Seller Policy',
	          'description' => 'Please fill in the text that you want to show link when enable Deal Request',
	          'filters' => array(
	            'StringTrim'
	          ),
	         'value'=> Engine_Api::_()->getApi('settings', 'core')->getSetting('store.sellerpolicy', $default_seller_url),
	    ));
	    
	    $default_buyer_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller'=>'help'), 'socialstore_extended', true);    
	    $this->addElement('Text', 'store_buyerpolicy',array(
	          'label'=>'URL Buyer Policy',
	          'description' => 'Please fill in the text that you want to show link when submit Deal Request',
	          'filters' => array(
	            'StringTrim'
	          ),
	         'value'=> Engine_Api::_()->getApi('settings', 'core')->getSetting('store.buyerpolicy', $default_buyer_url),
	    ));  
    }
    //end MinhNC
    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}