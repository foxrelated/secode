<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Copy.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Form_Page_Products_Copy extends Engine_Form
{

  protected $_item;
  protected  $_settings;

  public function getItem()
  {
    return $this->_item;
  }

  public function setItem(Core_Model_Item_Abstract $item)
  {
    $this->_item = $item;
    return $this;
  }

  public function init()
  {
    /**
     * @var $settings Core_Model_DbTable_Settings
     */
    $this->_settings = $settings = Engine_Api::_()->getDbTable('settings', 'core');

    // Init form
    $this->setAttrib('class', 'global_form')
      ->setAttrib('id', 'form-upload')
      ->setAttrib('name', 'add_new_product')
      ->setAttrib('style', 'clear: none;')
    ;









    // Element: Categories
    if( !$this->_item ) {
      $customFields = new Store_Form_Page_Custom_Fields();
    } else {
      $customFields = new Store_Form_Page_Custom_Fields(array(
        'item' => $this->getItem()
      ));
    }
    if( get_class($this) == 'Store_Form_Page_Products_Edit' ) {
      $customFields->setIsCreation(true);
    }

		$customFields->removeElement('submit');

    $this->addSubForms(array(
      'fields' => $customFields
    ));

    /**
     * @var $category Fields_Form_Element_ProfileType
     * @var $multiOptions Array
     */
    $category = $this->getSubForm('fields')->getElement('0_0_1');
    $multiOptions = $category->getMultiOptions();
    foreach($multiOptions as $key=>$value){
      if ( $key == '') unset($multiOptions[$key]);
    }

		$array_for_optionns = array();

        foreach ($multiOptions as $n=>$v) {
            array_push($array_for_optionns,'"'.$v.'"');
        }
      $arr = $multiOptions;

      asort($arr);
      $fin =array();
      foreach($arr as $it=>$val){
      $row = str_replace('"', '',$val);
      $fin[$it]=$row;
      }

        $category->setMultiOptions($fin);

        $this->addElement('Hidden', 'page_id', array(
            'order' => -1
        ));

        $this->addElement('Hidden', 'product_id', array(
	    'order' => -2
	));









		// Element: Type
		$this->addElement('Select', 'type', array(
			'label' => 'STORE_Product Type',
			'description' => 'STORE_PRODUCT_TYPE_DESCRIPTION',
      'required' => true,
			'multiOptions' => array(
				'simple'=>'STORE_Tangible',
				'digital'=>'STORE_Digital',
			),
      "onchange" => "switchAmount()"
		));
		$this->type->getDecorator('Description')->setOptions(array('placement' => 'append'));










    // Element: Title
    $this->addElement('Text', 'title', array(
      'label' => 'STORE_Product Title',
      'required' => true,
      'allowEmpty' => false,
    ));








        // Element: Condition
	$this->addElement('Select', 'item_condition', array(
	      'label' => 'Item Condition',
	      'required' => true,
	      'multiOptions' => array(
		  	'' => '',
		  	'new' => 'New',
		  	'used' => 'Used',
		  	'n_a' => 'N/A'
		  ),
		  'value' => '',
	    ));









	// Element: Upload picture
        $fancyUpload = new Engine_Form_Element_FancyUpload('file', array(
	'label' => 'Photos'
    	));
    $fancyUpload->clearDecorators()
                ->addDecorator('FormFancyUpload')
                ->addDecorator('viewScript', array(
                  'viewScript' => '_FancyUpload.tpl',
                  'placement'  => '',
                  ));
    Engine_Form::addDefaultDecorators($fancyUpload);
    $this->addElement($fancyUpload);
    $this->addElement('Hidden', 'fancyuploadfileids', array('order' => -1));











  // Element: Description
  $this->addElement('TinyMce', 'description', array(
      'label' => 'Description',
    ));


    $params = array(
      'mode' => 'exact',
	    'elements' => 'description',
	    'width' => '526px',
	    'height' => '225px',
      'theme_advanced_buttons1' => array(
        'bold', 'italic', 'underline', 'strikethrough', '|',
        'justifyleft', 'justifycenter', 'justifyright', 'justifyfull', '|',
        'bullist', 'numlist', '|',
        'undo', 'redo', '|',
        'sub', 'sup', '|',
        'forecolor', 'forecolorpicker', 'backcolor', 'backcolorpicker', '|'
      ),
      'theme_advanced_buttons2' => array(
        'newdocument', 'code', 'image', 'media', 'preview', 'fullscreen', '|',
        'link', 'unlink', 'anchor', 'charmap', 'cleanup', 'hr', 'removeformat', 'blockquote', 'separator', 'outdent', 'indent', '|',
        'selectall', 'advimage'),
      'theme_advanced_buttons3' => array('formatselect', 'fontselect', 'fontsizeselect', 'styleselectchar', '|', 'table', '|'),
    );

    $this->getView()->getHelper('TinyMce')->setOptions($params);

    







    // Element: additional_params
    $path = Engine_Api::_()->getModuleBootstrap('store')->getModulePath();
    $this->addPrefixPath('Engine_Form_Decorator_', $path . '/Form/Decorator/', 'Decorator');

    $additionalParams = new Store_Form_Element_AdditionalParams('additional_params', array(
      'label' => 'Additional Params',
      'Description' => 'STORE_ADDITIONAL_PARAMS_DESCRIPTION'
    ));
    $additionalParams->clearDecorators()
                ->addDecorator('FormAdditionalParams')
                ->addDecorator('viewScript', array(
                  'viewScript' => '_AdditionalParams.tpl',
                  'placement'  => '',
                  ));
    Engine_Form::addDefaultDecorators($additionalParams);
    $additionalParams->getDecorator('Description')->setOption('escape', false);
    $this->addElement($additionalParams);











    // Element: Quantity
    $this->addElement('Text', 'quantity', array(
      'label' => 'STORE_Quantity',
			'description' => 'STORE_Amount of product for sell',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('Digits'),
			)
    ));
		$this->quantity->getValidator('Digits')->setMessage('STORE_Please enter a valid digits.', 'digitsInvalid');
		$this->quantity->getDecorator("Description")->setOption("placement", "append");

		$this->addElement('Select', 'price_type', array(
			'label' => 'STORE_Price Type',
			'description' => 'STORE_PRICE_TYPE_DESCRIPTION',
			'multiOptions' => array(
				'simple'=>'STORE_Simple',
				'discount'=>'STORE_Discount'
			),
			"onchange" => "switchType()"
		));
		$this->price_type->getDecorator('Description')->setOptions(array('placement' => 'append'));










		//Currency
		//he@todo - currency is in todo list for now
		/*$this->addElement('Select', 'currency', array(
			'label' => 'STORE_Payment Currency',
			'multiOptions' => array(
			)
		));
		$this->currency->getDecorator('Description')->setOptions(array('placement' => 'append'));*/









    // Element: Price
    $this->addElement('Text', 'price', array(
      'label' => 'STORE_Product Price',
       'description' => 'STORE_PRICE_DESCRIPTION',
      'required' => true,
      'allowEmpty' => false,
      'value' => $settings->getSetting('store.minimum.price', 0.15),
    ));
    $this->price->getDecorator('Description')->setOptions(array('placement' => 'append'));














    // Element: List_Price
    $this->addElement('Text', 'list_price', array(
      'label' => 'STORE_Product List Price',
      'description' => 'STORE_LIST_PRICE_DESCRIPTION',
      'required' => true,
      'allowEmpty' => false,
      'value' => (float)($settings->getSetting('store.minimum.price', 0.15) + 0.01),
    ));


    // Discount expiry date
    $discount_expiry_date = new Engine_Form_Element_CalendarDateTime('discount_expiry_date');
    $discount_expiry_date->setLabel("Discount expiry date");
    $discount_expiry_date->setAllowEmpty(true);
    $discount_expiry_date->setDescription('STORE_DISCOUNT_EXPIRY_DATE_DESC');
    $this->addElement($discount_expiry_date);
    $discount_expiry_date->getDecorator('Description')->setOption('escape', false);


		if (Engine_Api::_()->store()->isCreditEnabled()) {
	      $this->addElement('Checkbox', 'via_credits', array(
	        'label' => 'STORE_Select checkbox if you want to sell product with credits, but this doesn\'t mean that you cannot sell with default currency ($ etc), they will work together',
	        'description' => 'Selling with Credits',
	      ));
	    }



    if (Engine_Api::_()->store()->isStoreCreditEnabled()) {
      $this->addElement('Checkbox', 'via_credits', array(
        'label' => 'Selling with Credits',
        'description' => 'STORE_Select checkbox if you want to sell product with credits, but this doesn\'t mean that you cannot sell with default currency ($ etc), they will work together',
      ));
    }

  







        /**
         * @var $table Store_Model_DbTable_Taxes
         */

        // Element: Taxes
        $table = Engine_Api::_()->getDbTable('taxes', 'store');
        $taxes = $table->getTaxesArray();

        $this->addElement('Select', 'tax_id', array(
            'label' => 'Tax',
            'description' => 'STORE_TAX_DESCRIPTION',
            'multiOptions' => $taxes
        ));
        $this->tax_id->getDecorator('Description')->setOptions(array('placement' => 'append'));









        // Element: Tags
    $this->addElement('Text', 'tags', array(
      'label' => 'Tags (Keywords)',
      'autocomplete' => 'off',
      'description' => 'Separate tags with commas.',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));
    $this->tags->getDecorator("Description")->setOption("placement", "append");










    // Element: Execute
    $this->addElement('Button', 'execute', array(
      'label' => 'Save',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    // Element: Cancel
    $this->addElement('Button', 'cancel', array(
      'label' => 'Cancel',
      'onclick' => 'history.go(-1);',
     'decorators' => array('ViewHelper')
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

    public function isValid($data)
    {
        /**
         * @var $atLeast Engine_Validate_AtLeast
         */
        $settings = Engine_Api::_()->getDbTable('settings', 'core');
        $allowFree = $settings->getSetting('store.free.products', 0);

        if ($data['price_type'] == 'discount') {
            $element = $this->getElement('list_price');
            $element->setAllowEmpty(0);
            $element->setRequired(1);
            $element->addValidator(new Engine_Validate_AtLeast($settings->getSetting('store.minimum.price', 0.15)));

            $atLeast = $element->getValidator('AtLeast');
            $minimum_price = (float)($data['price'] + 0.01);
            $atLeast->setMin($minimum_price);
        }

        if ($data['price_type'] == 'simple') {
            $el = $this->getElement('list_price');
            $el->clearValidators();
            $el->addValidator(new Engine_Validate_AtLeast(0));
        }

        if ($data['type'] == 'digital' && $allowFree) {
            $element = $this->getElement('price');
            $element->setAllowEmpty(1);
            $element->clearValidators();
            $element->addValidator('float', true, array('locale' => 'en_US'));
            $element->addValidator(new Engine_Validate_AtLeast(0));
        }

        return parent::isValid($data);
    }
}