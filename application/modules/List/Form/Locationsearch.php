<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    List
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: LocationController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class List_Form_Locationsearch extends Fields_Form_Search {

  protected $_searchForm;

  protected $_fieldType = 'list_listing';

  protected $_value;

  public function getValue() {
    return $this->_value;
  }

  public function setValue($item) {
    $this->_value = $item;
    return $this;
  }


  public function init() {
  
    $this->_value = unserialize($this->_value);
  
    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName(); 
    $controller = $front->getRequest()->getControllerName(); 
    $action = $front->getRequest()->getActionName();

    // Add custom elements
    $this->setAttribs(array(
			'id' => 'filter_form',
			'class' => '',
		))
		->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
		->setMethod('POST');
			
		$this->_searchForm = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');
		
		$this->getMemberTypeElement();
		
		$this->getAdditionalOptionsElement();

    parent::init();

    $this->loadDefaultDecorators();
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    if ($module == 'list' && $controller == 'index' && $action != 'map') {
			$this->setAction($view->url(array('action' => 'map'), 'list_general', true))->getDecorator('HtmlTag')->setOption('class', '');
    }
  }
  
  public function getMemberTypeElement() {

    $multiOptions = array('' => ' ');
    $profileTypeFields = Engine_Api::_()->fields()->getFieldsObjectsByAlias($this->_fieldType, 'profile_type');
    if( count($profileTypeFields) !== 1 || !isset($profileTypeFields['profile_type']) ) return;
    $profileTypeField = $profileTypeFields['profile_type'];

    $options = $profileTypeField->getOptions();

//     if( count($options) <= 1 ) {
//       if( count($options) == 1 ) {
//         $this->_topLevelId = $profileTypeField->field_id;
//         $this->_topLevelValue = $options[0]->option_id;
//       }
//       return;
//     }

    foreach( $options as $option ) {
      $multiOptions[$option->option_id] = $option->label;
    }

    $this->addElement('hidden', 'profile_type', array(
      'order' => -1000001,
      'class' =>
        'field_toggle' . ' ' .
        'parent_' . 0 . ' ' .
        'option_' . 0 . ' ' .
        'field_'  . $profileTypeField->field_id  . ' ',
      'onchange' => 'changeFields($(this));',
      'multiOptions' => $multiOptions,
    ));
    return $this->profile_type;
  }
  
  public function getAdditionalOptionsElement() {

    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName(); 
    $controller = $front->getRequest()->getControllerName(); 
    $action = $front->getRequest()->getActionName();
    
		//GET API
		$settings = Engine_Api::_()->getApi('settings', 'core');
		
		$subform = new Zend_Form_SubForm(array(
			'name' => 'extra',
			'order' => 19999999,
			'decorators' => array(
				'FormElements',
			)
		));
		Engine_Form::enableForm($subform);
		
	  $i = -5000;

		$this->addElement('Text', 'search', array(
			'label' => 'What',
			'autocomplete' => 'off',
			'description' => '(Enter keywords or Listing name)',
			'order' => 1,
		));
		$this->search->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));

	  $this->addElement('Text', 'list_location', array(
			'label' => 'Where',
			'autocomplete' => 'off',
			'description' => '(address, city, state or country)',
			'order' => 2,
			'onclick' => 'locationPage();'
		));
		$this->list_location->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));

    $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.locationfield', 1);
		if (!empty($enableLocation)) {
			$enableProximitysearch = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.proximitysearch', 1);

			if (!empty($enableProximitysearch)) {
				$flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.proximity.search.kilometer', 0);
				if ($flage) {
					$locationLable = "Within Kilometers";
					$locationOption = array(
						'0' => '',
						'1' => '1 Kilometer',
						'2' => '2 Kilometers',
						'5' => '5 Kilometers',
						'10' => '10 Kilometers',
						'20' => '20 Kilometers',
						'50' => '50 Kilometers',
						'100' => '100 Kilometers',
						'250' => '250 Kilometers',
						'500' => '500 Kilometers',
						'750' => '750 Kilometers',
						'1000' => '1000 Kilometers',
					);
				} else {
					$locationLable = "Within Miles";
					$locationOption = array(
						'0' => '',
						'1' => '1 Mile',
						'2' => '2 Miles',
						'5' => '5 Miles',
						'10' => '10 Miles',
						'20' => '20 Miles',
						'50' => '50 Miles',
						'100' => '100 Miles',
						'250' => '250 Miles',
						'500' => '500 Miles',
						'750' => '750 Miles',
						'1000' => '1000 Miles',
					);
				}
				$this->addElement('Select', 'locationmiles', array(
					'label' => $locationLable,
					'multiOptions' => $locationOption,
					'value' => '0',
			    'order' => 3,
				));
			}
	  }

    //Check for Location browse page.
		if ($module == 'list' && $controller == 'index' && $action != 'map') {
			$subform->addElement('Button', 'done', array(
				'label' => 'Search',
				'type' => 'submit',
				'ignore' => true,
			));
			$this->addSubForm($subform, $subform->getName());
		}
		else {
			$subform->addElement('Button', 'done', array(
				'label' => 'Search',
				'type' => 'submit',
				'ignore' => true,
				'onclick' => 'return locationSearch();'
			));
			$this->addSubForm($subform, $subform->getName());
		}
		
		// Element: cancel
    $this->addElement('Cancel', 'advances_search', array(
			'label' => 'Advanced search',
			'ignore' => true,
			'link' => true,
			'order' => 4,
			'onclick' => 'advancedSearchLists();',
			'decorators' => array('ViewHelper'),
    ));

		$this->addElement('hidden', 'advanced_search', array(
			'value' => 0
		));

    $this->addDisplayGroup(array('advances_search', 'locationmiles', 'search', 'done', 'list_location'), 'grp3');
    $button_group = $this->getDisplayGroup('grp3');
    $button_group->setDecorators(array(
			'FormElements',
			'Fieldset',
			array('HtmlTag', array('tag' => 'li', 'id' => 'group3', 'style' => 'width:100%;'))
    ));

    $group2 = array();

    if (!empty($this->_value['street'])) {
			$this->addElement('Text', 'list_street', array(
				'label' => 'Street',
				'autocomplete' => 'off',
				'order' => 5,
			));
			$group2[] = 'list_street';
		}

		if (!empty($this->_value['city'])) {
			$this->addElement('Text', 'list_city', array(
				'label' => 'City',
				'autocomplete' => 'off',
				'order' => 6,
			));
			$group2[] = 'list_city';
		}

		if (!empty($this->_value['state'])) {
			$this->addElement('Text', 'list_state', array(
				'label' => 'State',
				'autocomplete' => 'off',
				'order' => 7,
			));
			$group2[] = 'list_state';
		}

		if (!empty($this->_value['country'])) {
			$this->addElement('Text', 'list_country', array(
				'label' => 'Country',
				'autocomplete' => 'off',
				'order' => 8,
			));
			$group2[] = 'list_country';
		}

		if(!empty($group2)) {
			$this->addDisplayGroup($group2, 'grp2');
			$button_group = $this->getDisplayGroup('grp2');
			$button_group->setDecorators(array(
				'FormElements',
				'Fieldset',
				array('HtmlTag', array('tag' => 'li', 'id' => 'group2', 'style' => 'width:100%;'))
			));
		}

		$this->addElement('Select', 'orderby', array(
			'label' => 'Browse By',
			'multiOptions' => array(
				'' => "",
				'creation_date' => 'Most Recent',
				'view_count' => 'Most Viewed',
				'title' => "Alphabetic",
			),
			'order' => 9,
		));

		$this->addElement('Select', 'closed', array(
			'label' => 'Status',
			'multiOptions' => array(
				'' => 'All Listings',
				'0' => 'Only Open Listings',
				'1' => 'Only Closed Listings',
			),
			'order' => 10,
		));

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		$show_multiOptions = array();
		$show_multiOptions["1"] = 'Everyone\'s Posts';
		$show_multiOptions["2"] = 'Only My Friends\' Posts';
		$show_multiOptions["4"] = 'Listings I Like';
		$value_deault = 1;
		$enableNetwork = $settings->getSetting('list.network', 0);
		if (empty($enableNetwork)) {
			$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
			$networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
			$viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer_id));

			if (!empty($viewerNetwork)) {
				$show_multiOptions["3"] = 'Only My Networks';
				$browseDefaulNetwork = $settings->getSetting('list.default.show', 0);

				if (!isset($_GET['show']) && !empty($browseDefaulNetwork)) {
					$value_deault = 3;
				} elseif (isset($_GET['show'])) {
					$value_deault = $_GET['show'];
				}
			}
		}
		
    if (!empty($viewer_id)) {
			$this->addElement('Select', 'show', array(
				'label' => 'Show',
				'multiOptions' => $show_multiOptions,
				'order' => 11,
				'value' => $value_deault,
			));
    }
		
		$this->addElement('Checkbox', 'has_photo', array(
			'label' => 'Only Listings With Photos',
			'order' => 12,
		));
		
		$this->addElement('Checkbox', 'has_review', array(
			'label' => 'Only Listings With Reviews',
			'order' => 13,
		));
		

		$categories = Engine_Api::_()->getDbTable('categories', 'list')->getCategories(0, 0);
		if (count($categories) != 0) {
			$categories_prepared[0] = "";
			foreach ($categories as $category) {
				$categories_prepared[$category->category_id] = $category->category_name;
			}

			$this->addElement('Select', 'category_id', array(
							'label' => 'Category',
							'order' => 20,
							'multiOptions' => $categories_prepared,
							'onchange' => " var profile_type = getProfileType($(this).value);
														$('profile_type').value = profile_type;
														changeFields($('profile_type'));
														subcategories(this.value, '', '');",
			));
		
			$this->addElement('Select', 'subcategory_id', array(
					'RegisterInArrayValidator' => false,
					'order' => 21,
					'decorators' => array(array('ViewScript', array(
																	'viewScript' => 'application/modules/List/views/scripts/_Subcategory.tpl',
																	'class' => 'form element')))
				));

			$this->addElement('Select', 'subsubcategory_id', array(
					'RegisterInArrayValidator' => false,
					'order' => 22,
					'decorators' => array(array('ViewScript', array(
																	'viewScript' => 'application/modules/List/views/scripts/_Subcategory.tpl',
																	'class' => 'form element')))
			));
		}
		
    $this->addElement('Hidden', 'page', array(
            'order' => $i++,
    ));

    $this->addElement('Hidden', 'tag', array(
            'order' => $i++,
    ));

    $this->addElement('Hidden', 'tag_id', array(
            'order' => $i++,
    ));

//     $this->addElement('Hidden', 'city', array(
//             'order' => $i++,
//     ));

    $this->addElement('Hidden', 'start_date', array(
            'order' => $i++,
    ));

    $this->addElement('Hidden', 'end_date', array(
            'order' => $i++,
    ));

    $this->addElement('Hidden', 'category', array(
            'order' => $i++,
    ));

    $this->addElement('Hidden', 'subcategory', array(
            'order' => $i++,
    ));

    $this->addElement('Hidden', 'subsubcategory', array(
            'order' => $i++,
    ));

    $this->addElement('Hidden', 'categoryname', array(
            'order' => $i++,
    ));

    $this->addElement('Hidden', 'subcategoryname', array(
            'order' => $i++,
    ));

    $this->addElement('Hidden', 'subsubcategoryname', array(
            'order' => $i++,
    ));

    $this->addElement('Hidden', 'Latitude', array(
      'order' => $i--,
    ));
    
    $this->addElement('Hidden', 'Longitude', array(
      'order' => $i--,
    ));
//, 'category_id', 'subcategory_id','subsubcategory_id'
    $this->addDisplayGroup(array('profile_type','orderby', 'show', 'has_review', 'has_photo','closed' , 'category_id', 'subcategory_id','subsubcategory_id'), 'grp1');
    $button_group = $this->getDisplayGroup('grp1');
    $button_group->setDecorators(array(
			'FormElements',
			'Fieldset',
			array('HtmlTag', array('tag' => 'li', 'id' => 'group1', 'style' => 'width:100%;'))
    ));

    return $this;
  }
}