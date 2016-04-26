<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: LocationController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitetagcheckin_Form_UserLocationsearch extends Fields_Form_Search {

  protected $_searchForm;
  protected $_fieldType = 'user';

  protected $_value;
  protected $_formoptions;

  public function getValue() {
    return $this->_value;
  }

  public function setValue($item) {
    $this->_value = $item;
    return $this;
  }

  public function getFormoptions() {
    return $this->_formoptions;
  }

  public function setFormoptions($item) {
    $this->_formoptions = $item;
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
		
		// Add custom elements
    $this->getMemberTypeElement();
    
		$this->getAdditionalOptionsElement();

    parent::init();

    $this->loadDefaultDecorators();
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    if ($module == 'sitetagcheckin' && $controller == 'index' && $action != 'by-locations') {
			$this->setAction($view->url(array('action' => 'by-locations'), 'sitetagcheckin_bylocation', true))->getDecorator('HtmlTag')->setOption('class', '');
    }
  }

  public function getMemberTypeElement()
  {
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

    $this->addElement('Select', 'profile_type', array(
      'label' => 'What',
      'order' => 2,
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

		$subform = new Zend_Form_SubForm(array(
			'name' => 'extra',
			'order' => 19999999,
			'decorators' => array(
				'FormElements',
			)
		));
		Engine_Form::enableForm($subform);
		
	  $i = -5000;

		$this->addElement('Text', 'displayname', array(
			'label' => 'Who',
			'autocomplete' => 'off',
			'description' => '(Enter keywords or Member name)',
			'order' => 1,
		));
		$this->displayname->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));

    $this->addElement('Hidden', 'user_id', array());


		
	  $this->addElement('Text', 'sitepage_location', array(
			'label' => 'Where',
			'autocomplete' => 'off',
			'description' => '(address, city, state or country)',
			'order' => 3,
			'onclick' => 'locationPage();'
		));
		$this->sitepage_location->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));

		//$flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.proximity.search.kilometer', 0);
		
		$flage = Engine_Api::_()->seaocore()->geoUserSettings('sitetagcheckin');
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
			'order' => 4,
		));


    //Check for Location browse page.
// 		if ($module == 'sitepage' && $controller == 'index' && $action != 'map') {
// 			$subform->addElement('Button', 'done', array(
// 				'label' => 'Search',
// 				'type' => 'submit',
// 				'ignore' => true,
// 			));
// 			$this->addSubForm($subform, $subform->getName());
// 		}
// 		else {
			$this->addElement('Button', 'done', array(
				'label' => 'Search',
				'type' => 'submit',
				'ignore' => true,
				'order' => 5,
				'onclick' => 'return locationSearch();'
			));
			//$this->addSubForm($subform, $subform->getName());
		//}
		
		// Element: cancel
    $this->addElement('Cancel', 'advances_search', array(
			'label' => 'Advanced search',
			'ignore' => true,
			'link' => true,
			'order' => 6,
			'onclick' => 'advancedSearchEvents();',
			'decorators' => array('ViewHelper'),
    ));

		$this->addElement('hidden', 'advanced_search', array(
			'value' => 0
		));
		
		
    $this->addDisplayGroup(array('advances_search', 'done', 'user_id', 'profile_type', 'locationmiles', 'done', 'sitepage_location', 'displayname'), 'grp3');
    $button_group = $this->getDisplayGroup('grp3');
    $button_group->setDecorators(array(
			'FormElements',
			'Fieldset',
			array('HtmlTag', array('tag' => 'li', 'id' => 'group3', 'class' => 'grp_field'))
    ));
    
    $group2 = array();


    if (!empty($this->_formoptions) && in_array("street", $this->_formoptions)) {
			$this->addElement('Text', 'sitepage_street', array(
				'label' => 'Street',
				'autocomplete' => 'off',
				'order' => 7,
			));
			$group2[] = 'sitepage_street';
		}

		if (!empty($this->_formoptions) && in_array("city", $this->_formoptions)) {
			$this->addElement('Text', 'sitepage_city', array(
				'label' => 'City',
				'autocomplete' => 'off',
				'order' => 8,
			));
			$group2[] = 'sitepage_city';
		}

		if (!empty($this->_formoptions) && in_array("state", $this->_formoptions)) {
			$this->addElement('Text', 'sitepage_state', array(
				'label' => 'State',
				'autocomplete' => 'off',
				'order' => 9,
			));
			$group2[] = 'sitepage_state';
		}

		if (!empty($this->_formoptions) && in_array("country", $this->_formoptions)) {
			$this->addElement('Text', 'sitepage_country', array(
				'label' => 'Country',
				'autocomplete' => 'off',
				'order' => 10,
			));
			$group2[] = 'sitepage_country';
		}

		if(!empty($group2)) {
			$this->addDisplayGroup($group2, 'grp2');
			$button_group = $this->getDisplayGroup('grp2');
			$button_group->setDecorators(array(
				'FormElements',
				'Fieldset',
				array('HtmlTag', array('tag' => 'li', 'id' => 'group2', 'class' => 'grp_field'))
			));
		}


    $this->addElement('Hidden', 'Latitude', array(
      'order' => $i--,
    ));

    $this->addElement('Hidden', 'Longitude', array(
      'order' => $i--,
    ));

		if (!empty($this->_formoptions) && in_array("hasphoto", $this->_formoptions)) {
			$this->addElement('Checkbox', 'has_photo', array(
				'label' => 'Only Members With Photos',
			));
			$group1[] = 'has_photo';
		}
		
		if (!empty($this->_formoptions) && in_array("isonline", $this->_formoptions)) {
			$this->addElement('Checkbox', 'is_online', array(
				'label' => 'Only Online Members',
			));
			$group1[] = 'is_online';
    }
		
		//if (!empty($this->_formoptions) && in_array("orderby", $this->_formoptions)) {
			$multiOPtionsOrderBy = array(
				'' => '',
        'title' => "Alphabetical",
        "distance" => "Distance",
				'creation_date' => 'Most Recent',
				'view_count' => 'Most Viewed',
				'member_count' => 'Most Popular',
			);

			$this->addElement('Select', 'orderby', array(
					'label' => 'Browse By',
					'multiOptions' => $multiOPtionsOrderBy,
			));
			$group1[] = 'orderby';
    //}
		
		//if (!empty($this->_formoptions) && in_array("view_view", $this->_formoptions)) {
			$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
			if (!empty($viewer_id)) {
				$show_multiOptions = array();
				$show_multiOptions["1"] = 'All Members';
				$show_multiOptions["2"] = 'Only My Friends';
				$value_deault = 1;
				
				$this->addElement('Select', 'view_view', array(
					'label' => 'Show',
					'multiOptions' => $show_multiOptions,
					'value' => $value_deault,
				));
				$group1[] = 'view_view';
			}
		//}
		
		$levelSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.levelsettings');
		if (!empty($levelSettings)) {
			$authorization = Engine_Api::_()->getDbTable('levels', 'authorization');
			$authorizatioName = $authorization->info('name');
			$select = $authorization->select()->from($authorizatioName);
			$resultau = $authorization->fetchAll($select);

			if (count($resultau) != 0) {
				$au_title[0] = "";
				foreach ($resultau as $resultaus) {
					$au_title[$resultaus->level_id] = $resultaus->title;
				}

				$this->addElement('Select', 'level_id', array(
					'label' => 'Member Levels',
					'multiOptions' => $au_title,
				));
				$group1[] = 'level_id';
			}
		}

		$networkSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.networksettings');
		if (!empty($networkSettings)) {
			$networks = Engine_Api::_()->getDbTable('networks', 'network');
			$networksname = $networks->info('name');
			$select = $networks->select()->from($networksname);
			$result = $networks->fetchAll($select);

			if (count($result) != 0) {
				$network_title[0] = "";
				foreach ($result as $results) {
					$network_title[$results->network_id] = $results->title;
				}

				$this->addElement('Select', 'network_id', array(
					'label' => 'Networks',
					'multiOptions' => $network_title,
				));
				$group1[] = 'network_id';
			}
		}
    //$group1 = array_merge(array('profile_type'), $group1);

   // if (!empty($this->_formoptions) && (in_array("has_photo", $this->_formoptions) || in_array("orderby", $this->_formoptions) || in_array("is_online", $this->_formoptions))) {
			$this->addDisplayGroup($group1, 'grp1');
			$button_group = $this->getDisplayGroup('grp1');
			$button_group->setDecorators(array(
				'FormElements',
				'Fieldset',
				array('HtmlTag', array('tag' => 'li', 'id' => 'group1', 'class' => 'grp_field'))
			));
    //}
    return $this;
  }
}