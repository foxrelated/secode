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

class Socialstore_Form_Create extends Engine_Form
{
  public $_error = array();

  public function init()
  {   
    $this
      ->addPrefixPath('Socialstore_Form_Decorator', APPLICATION_PATH . '/application/modules/Socialstore/Form/Decorator', 'decorator')
      ->addPrefixPath('Socialstore_Form_Element', APPLICATION_PATH . '/application/modules/Socialstore/Form/Element', 'element')
      ->addElementPrefixPath('Socialstore_Form_Decorator', APPLICATION_PATH . '/application/modules/Socialstore/Form/Decorator', 'decorator');
  	
  	$this->setTitle('Create Your Store')
      ->setDescription('Create your store below, then click "Create Store" to publish Store.');
    $user = Engine_Api::_()->user()->getViewer();
    $user_level = Engine_Api::_()->user()->getViewer()->level_id;
    
    $this->addElement('Text', 'title', array(
      'label' => 'Store Title*',
      'allowEmpty' => false,
      'required'=>true,
      'title' => 'Title of Store',             
      'description' => 'Title of Store',  
      'style' => 'width: 300px',
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '128'))
    )));
     $this->title->getDecorator("Description")->setOption("placement", "append");
     $route = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.pathname', "socialstore");
     
          $this->addElement('MultiLevel', 'category_id', array(
	        'label' => 'Category*',
	        'required'=>true,
	        'allowEmpty'=>false,
	        'model'=>'Socialstore_Model_DbTable_Storecategories',
	        'onchange'=>"en4.store.changeCategory($(this),'category_id','Socialstore_Model_DbTable_Storecategories','$route')",
			'title' => '',
			'value' => ''
     	));
     
	$this->addElement('MultiLevel', 'location_id', array(
        'label' => 'Location*',
        'required'=>true,
		'allowEmpty' => false,
        'model'=>'Socialstore_Model_DbTable_Locations',
        'onchange'=>"en4.store.changeCategory($(this),'location_id','Socialstore_Model_DbTable_Locations','$route')",
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
	
	$allowed_html = Engine_Api::_()->authorization()->getPermission($user_level, 'social_store', 'auth_html'); 
  	$this->addElement('TinyMce', 'description', array(
      'label' => 'Description',
      'required'=>false,
      'style'=>'width:400px',
  	  'allowEmpty'=>true,
        'editorOptions' => array(
          'bbcode' => 1,
          'html' => 1
        ),
      'config'=>array(
      	'width'=>'400px',
      	'height'=>'200px'
      ),
      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_Html(array('AllowedTags'=>$allowed_html))),
    ));
	
	
	
    $this->addElement('File', 'thumbnail', array(
        'label' => 'Photo*',
        'title' => 'Main photo',
    	'required'=>true,
        'description' => 'Main photo of store (jpg, png, gif, jpeg)',
      ));
      $this->thumbnail->getDecorator("Description")->setOption("placement", "append");
      $this->thumbnail->addValidator('Extension', false, 'jpg,png,gif,jpeg');

    
    $this->addElement('Text', 'contact_name',array(
      'label'=>'Contact Name*',
      'description' => '',
      'allowEmpty' => false,
      'required'=>true,
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
     'value'=>'',
    ));
    $this->addElement('Text', 'contact_email', array(
      'label' => 'Email Address*',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
        array('EmailAddress', true),
      ),
    ));	
    
    $this->addElement('Text', 'contact_address',array(
      'label'=>'Address*',
      'description' => '',
      'allowEmpty' => false,
      'required'=>true,
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
     'value'=>'',
    ));
	
	$this->addElement('Text', 'contact_phone',array(
      'label'=>'Phone',
      'description' => '',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
     'value'=>'',
    ));
    $this->addElement('Text', 'contact_fax',array(
      'label'=>'Fax',
      'description' => '',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
     'value'=>'',
    ));
     $this->addElement('Text', 'contact_website',array(
      'label'=>'Website',
      'description' => '',
      'validators' => array(
        new Url_Validator,
      ),
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
     'value'=>'',
    ));
        
      // Add subforms
    if( !$this->_item ) {
      $customFields = new Socialstore_Form_Custom_Fields();
    } else {
      $customFields = new Socialstore_Form_Custom_Fields(array(
        'item' => $this->getItem()
      ));
    }
    if( get_class($this) == 'Socialstore_Form_Create' ) {
      $customFields->setIsCreation(true);
    }

    $this->addSubForms(array(
      'fields' => $customFields
    ));
    // View
    $availableLabels = array(
       'everyone'            => 'Everyone',
      'registered'          => 'All Registered Members',
      'owner_network'       => 'Friends and Networks',
      'owner_member_member' => 'Friends of Friends',
      'owner_member'        => 'Friends Only',
      'owner'               => 'Just Me',
    );

    $options =(array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('social_store', $user, 'store_authcom');
    $options = array_intersect_key($availableLabels, array_flip($options));

    // Comment
    $this->addElement('Select', 'store_authcom', array(
      'label' => 'Comment Privacy',
      'title' => 'Who may post comments on this store?',
      'description' => 'Who may post comments on this store?',
      'multiOptions' => $options,
      'value' => 'everyone',
    ));
    $this->store_authcom->getDecorator('Description')->setOption('placement', 'append');
    

    $this->addElement('Button', 'execute', array(
      'label' => 'Submit',
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
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'socialstore_mystore_general', true),
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
};
