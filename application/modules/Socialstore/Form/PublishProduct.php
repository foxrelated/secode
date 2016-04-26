<?php
class Socialstore_Form_PublishProduct extends Engine_Form
{
  public $_error = array();

  public function init()
  {   
    $this->setTitle('Publish Product')
      ->setDescription('STORE_FORM_PUBLISH_PRODUCT_DESCRIPTION');
  	$user = Engine_Api::_()->user()->getViewer();
    $user_level = Engine_Api::_()->user()->getViewer()->level_id;
    
    $publish_fee = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('social_product', $user, 'product_pubfee');
    $feature_fee = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('social_product', $user, 'product_ftedfee');
    
	$view =  Zend_Registry::get('Zend_View');
        
    $this->addElement('dummy', 'product_publish_fee',array(
      'label'=>'Fee for publishing',
      'description' =>($publish_fee?  $view->currency($publish_fee): $view->translate('Free')),

    ));
	$this->product_publish_fee->getDecorator("Description")->setOption("placement", "append")->setEscape(false);
    
	$this->addElement('dummy', 'product_feature_fee',array(
      'label'=>'Fee for featuring',
      'description' =>( $feature_fee? $view->currency($feature_fee): $view->translate('Free')),
    ));
	$this->product_feature_fee->getDecorator("Description")->setOption("placement", "append")->setEscape(false);
	if(Engine_Api::_()->socialstore()->checkStoreGroupbuyConnection())
	{
		$gda_fee = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('social_product', $user, 'product_gdafee');
		$this->addElement('dummy', 'product_gda_fee',array(
	      'label'=>'Fee for request deal',
	      'description' =>( $gda_fee? $view->currency($gda_fee): $view->translate('Free')),
	    ));
	    $this->product_gda_fee->getDecorator("Description")->setOption("placement", "append")->setEscape(false);
	}
	
    $translate = Zend_Registry::get('Zend_Translate');
    $this->addElement('Radio', 'publish_option', array(
        'label' => 'How do you wish to publish your product?',
        'multiOptions' => array(
          '0' => $translate->translate('Publish with no featured option').': '.'<strong>'. $view->currency($publish_fee) .'</strong>',
          '1' => $translate->translate('Publish with featured option').': '.'<strong>'. $view->currency($publish_fee + $feature_fee).'</strong>'
        ),
		'value' => '0',
		'escape'=>false
      ));
    
     // Init Deal Request checkbox
     if(Engine_Api::_()->socialstore()->checkStoreGroupbuyConnection())
	 {
	    $this->addElement('Checkbox', 'gda', array(
	      'label' => "Enable Deal Request",
	      'value' => 0,  
	      'checked' => false,
	    ));
	    $default_seller_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller'=>'help'), 'socialstore_extended', true);    
	    $URL = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.sellerpolicy', $default_seller_url);
	    $this->addElement('Cancel', 'link', array(
	      'label' => 'Term of Use and Privacy Statement',
	      'link' => true,
	      'onclick' =>'goto("'.$URL.'")',
	      'decorators' => array(
	        'ViewHelper'
	      )
	    ));                                                    
	    $this->addDisplayGroup(array('gda', 'link'), 'buttons1', array(
	      'decorators' => array(
	        'FormElements',
	        'DivDivDivWrapper'
	      )));   
      //end MinhNC 
      }
    $this->addElement('Button', 'execute', array(
      'label' => 'Publish',
      'type' => 'button',
      'onclick' => 'this.form.submit(); removeSubmit()',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));
    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'my-products'), 'socialstore_mystore_general', true),
      'onclick' => '',
      'decorators' => array(
        'ViewHelper',
      ),
    ));
     // DisplayGroup: buttons
    $this->addDisplayGroup(array(
      'execute',
    	'cancel',
      ), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper'
      ),
    ));
  }
}