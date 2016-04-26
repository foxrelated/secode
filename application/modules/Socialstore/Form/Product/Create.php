<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Create.php
 * @author     Long Le
 */
class Url_Validator extends Zend_Validate_Abstract
{
    const INVALID_URL = 'invalidUrl';
    protected $_messageTemplates = array(
        self::INVALID_URL   => "'%value%' is not a valid URL.",
    );
 
    public function isValid($value)
    {
        $valueString = (string) $value;
        $this->_setValue($valueString);
 
        if (!Zend_Uri::check($value)) {
            $this->_error(self::INVALID_URL);
            return false;
        }
        return true;
    }
}

class Socialstore_Form_Product_Create extends Engine_Form
{
  public $_error = array();

  public function init()
  {
  	$this
      ->addPrefixPath('Socialstore_Form_Decorator', APPLICATION_PATH . '/application/modules/Socialstore/Form/Decorator', 'decorator')
      ->addPrefixPath('Socialstore_Form_Element', APPLICATION_PATH . '/application/modules/Socialstore/Form/Element', 'element')
      ->addElementPrefixPath('Socialstore_Form_Decorator', APPLICATION_PATH . '/application/modules/Socialstore/Form/Decorator', 'decorator')
      ->setAttrib('enctype','multipart/form-data');
	if (Zend_Registry::isRegistered('store_id')) {     
		$store_id = Zend_Registry::get('store_id');
	}
    $this->setTitle('Add New Product')
      ->setDescription('Add your new product below, then click "Add Product" to publish Product.');
    $user = Engine_Api::_()->user()->getViewer();
	if($user->getIdentity()){
		$user_level = Engine_Api::_()->user()->getViewer()->level_id;
	}else{
		$user_level = 0;
	}
    
    $this->addElement('Text', 'title', array(
      'label' => 'Product Name*',
      'allowEmpty' => false,
      'required'=>true,
      'title' => 'Name of Product',             
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '63'))
    )));
    $this->title->getDecorator("Description")->setOption("placement", "append");
     
    $this->addElement('Text', 'sku', array(
      'label' => 'SKU*',
      'allowEmpty' => false,
      'required'=>true,
      'title' => '',             
      'description' => '',  
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '128'))
    )));
     $this->sku->getDecorator("Description")->setOption("placement", "append");
	
   $this->addElement('Select', 'product_type', array(
      'label' => 'Product Type',
      'title' => '',             
      'description' => '',
   	  'onchange' => 'showDownloadUrl()',  
	  'multiOptions' => array(
        'default' => 'Default',
        'downloadable' => 'Downloadable',
      ),
   ));
     $this->product_type->getDecorator("Description")->setOption("placement", "append");  
     $this->addElement('File', 'downloadable_file', array(
        'label' => 'Product*',
        'description' => 'Downloadable Product',
      ));
      $this->downloadable_file->getDecorator("Description")->setOption("placement", "append");
     
      $this->addElement('File', 'preview_file', array(
      	'label' => 'Preview File',
      	'description' => 'Preview File only available for mp3 product!',
      ));
      $this->preview_file->getDecorator("Description")->setOption("placement", "append");
      $this->preview_file->addValidator('Extension', false, 'mp3');
      
      $this->addElement('Text', 'weight',array(
      'label'=>'Weight',
      'title' => '',  
      'allowEmpty' => true,
      'required'=>false,
      'description' => '',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
     'value'=>    '0.00',
      'validators' => array(
       	array('Float', true)
       
    )));
    $this->weight->getDecorator("Description")->setOption("placement", "append");
	$this->weight -> addFilter('Callback', array(array($this, 'filterRound')));
    
      $this->addElement('Select', 'weight_unit', array(
      'label' => 'Weight Unit',
      'title' => '',             
      'description' => '',
	  'multiOptions' => array(
        'kg' => 'Kilogram',
        'lb' => 'Pound',
      ),
   	));
    $this->weight_unit->getDecorator("Description")->setOption("placement", "append");
    
    $allowed_html = Engine_Api::_()->authorization()->getPermission($user_level, 'social_product', 'auth_html'); 
  	$this->addElement('TinyMce', 'description', array(
      'label' => 'Short Description',
      'required'=>false,
  	  'allowEmpty'=>true,
        'editorOptions' => array(
          'bbcode' => 1,
          'html' => 1
        ),
      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_Html(array('AllowedTags'=>$allowed_html))),
    ));
    $this->addElement('TinyMce', 'body', array(
      'label' => 'Detail',
      'required'=>false,
  	  'allowEmpty'=>true,
        'editorOptions' => array(
          'bbcode' => 1,
          'html' => 1
        ),
      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_Html(array('AllowedTags'=>$allowed_html))),
    ));
     $route = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.pathname', "socialstore");
    
     $this->addElement('MultiLevel', 'category_id', array(
        'label' => 'Category*',
        'required'=>true,
        'allowEmpty'=>false,
        'model'=>'Socialstore_Model_DbTable_Customcategories',
        'store_id' => Zend_Registry::get('store_id'),
        'onchange'=>"en4.store.changeCategory($(this),'category_id','Socialstore_Model_DbTable_Customcategories','$route')",
        'title' => '',
		'value' => ''
     ));
	 
	 $this->addElement('radio','view_status',array(
	 	'label'=>'Enable to show',
	 	'description'=>'',
	 	'multiOptions'=>array(
			'hide'=>'No',
			'show'=>'Yes',
		),
		'value'=>'show'
	 ));
	 
	$this->addElement('Text', 'available_quantity', array(
      'label' => 'Total Quantity',
      'allowEmpty' => false,
      'required'=>true,
      'value'=> 0,
      'title' => '',             
      'description' => '0 means unlimited',  
      'validators' => array(
        array('Int', true),
        array('GreaterThan',true,array(-1))
	  )));
	 $this->available_quantity->getDecorator("Description")->setOption("placement", "append");
	 
	 $this->addElement('Text', 'min_qty_purchase', array(
      'label' => 'Minimum Quantity For Purchase',
      'allowEmpty' => false,
      'required'=>true,
      'value'=>1,
      'title' => '',             
      'validators' => array(
        array('Int', true),
       	array('GreaterThan',true,array(0))
       
    )));
	$this->min_qty_purchase->getDecorator("Description")->setOption("placement", "append");
     $this->addElement('Text', 'max_qty_purchase', array(
      'label' => 'Maximum Quantity For Purchase',
      'allowEmpty' => false,
      'required'=>true,
      'title' => '',       
      'value'=>0,      
      'description' => '0 means unlimited',  
      'validators' => array(
        array('Int', true)
	)));
	$this->max_qty_purchase->getDecorator("Description")->setOption("placement", "append");
	
	$this->addElement('File', 'thumbnail', array(
        'label' => 'Photo*',
        'title' => 'Main photo',
    	'required'=>true,
        'description' => 'Main photo of store (jpg, png, gif, jpeg)',
      ));
      $this->thumbnail->getDecorator("Description")->setOption("placement", "append");
      $this->thumbnail->addValidator('Extension', false, 'jpg,png,gif,jpeg');
	
     $this->addElement('Text', 'pretax_price',array(
      'label'=>'Pre-Tax Price*',
      'title' => '',  
      'allowEmpty' => false,
      'required'=>true,
      'description' => '',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
     'value'=>    '0.00',
      'validators' => array(
        array('NotEmpty', true),
       	array('Float', true),
       	array('GreaterThan',true,array(0))
       
    )));
	$this->pretax_price -> addFilter('Callback', array(array($this, 'filterRound')));
    $this->pretax_price->getDecorator("Description")->setOption("placement", "append");
    
    $this->addElement('select','tax_id', array(
		'label'=>'VAT (%)',		
		'multiOptions'=>Socialstore_Model_DbTable_Taxes::getMultiOptions($store_id),
	));
	
    $this->addElement('Text', 'discount_price',array(
      'label'=>'Discount Price',
      'title' => '',  
      'allowEmpty' => true,
      'required' => false,
      'description' => '',
      'onchange' => 'discountPriceChange()',  
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
      'validators' => array(
        array('NotEmpty', true),
       	array('Float', true),
    )));
	$this->discount_price -> addFilter('Callback', array(array($this, 'filterRound')));
    $this->discount_price->getDecorator("Description")->setOption("placement", "append");
    $available = new Engine_Form_Element_CalendarDateTime('available_date');
    $available->setLabel("Discount Available Date");
    $available->setAllowEmpty(true);
    $available->setRequired(false);
    $this->addElement($available);
    
    $expire = new Engine_Form_Element_CalendarDateTime('expire_date');
    $expire->setLabel("Discount Expire Date");
    $expire->setAllowEmpty(true);
    $expire->setRequired(false);
    $this->addElement($expire);
    
    
	$this->addElement('Button', 'execute', array(
      'label' => 'Add Product',
      'type' => 'button',
      'onclick' => 'this.form.submit(); removeSubmit()',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));
    $checkVideo = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('video');
    $checkYnVideo = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('ynvideo');
	if ($checkVideo || $checkYnVideo) {
		$description = Zend_Registry::get('Zend_Translate')
          ->_('You could upload the preview video <a target = "_blank" href="%1$s">here</a> and paste the link to the field above!');
           $description = sprintf($description,$this->getView()->url(array('action' => 'create'), "video_general"));
	     $this->addElement('Text', 'video_url',array(
	      'label'=>'Video Preview Link',
	      'description' => $description,
	      'validators' => array(
	        new Url_Validator,
	      ),
	      'filters' => array(
	        new Engine_Filter_Censor(),
	      ),
	     'value'=>'',
	    ));
	     $this->video_url->getDecorator("Description")->setOption("placement", "append")->setOption('escape', false);

	}
    
      
    $availableLabels = array(
       'everyone'            => 'Everyone',
      'registered'          => 'All Registered Members',
      'owner_network'       => 'Friends and Networks',
      'owner_member_member' => 'Friends of Friends',
      'owner_member'        => 'Friends Only',
      'owner'               => 'Just Me',
    );

    $options =(array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('social_product', $user, 'product_authcom');
    $options = array_intersect_key($availableLabels, array_flip($options));
    
    $this->addElement('Select', 'product_authcom', array(
      'label' => 'Comment Privacy',
      'title' => 'Who may post comments on this product?',
      'description' => 'Who may post comments on this product?',
      'multiOptions' => $options,
      'value' => 'everyone',
    ));
    $this->product_authcom->getDecorator('Description')->setOption('placement', 'append');
    
    
    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'my-products'), 'socialstore_mystore_general', true),
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
  public function filterRound($value)
  {
    if( empty($value) ) {
      return '0';
    }
    return round($value, 2);
  }
};
