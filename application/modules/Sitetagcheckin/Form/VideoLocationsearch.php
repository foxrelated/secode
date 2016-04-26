<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: LocationController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitetagcheckin_Form_VideoLocationsearch extends Engine_Form {

  protected $_searchForm;

//   protected $_value;
// 
//   public function getValue() {
//     return $this->_value;
//   }
// 
//   public function setValue($item) {
//     $this->_value = $item;
//     return $this;
//   }


  public function init() {
  
    //$this->_value = unserialize($this->_value);
  
    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName(); 
    $controller = $front->getRequest()->getControllerName(); 
    $action = $front->getRequest()->getActionName();

    // Add custom elements
    $this->setAttribs(array(
			'id' => 'video_filter_form',
			'class' => '',
		))
		->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
		->setMethod('POST');
		
		$this->getAdditionalOptionsElement();

    parent::init();

    $this->loadDefaultDecorators();
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    if ($module == 'sitetagcheckin' && $controller == 'index' && $action != 'by-locations') {
			$this->setAction($view->url(array('action' => 'videoby-locations'), 'sitetagcheckin_bylocation', true))->getDecorator('HtmlTag')->setOption('class', '');
    }
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
	  


    // prepare categories
//     $finalArray[0]  = "All Categories";
//     if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'video' )) {
// 			$categories = Engine_Api::_()->video()->getCategories();
// 		} elseif(Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'advvideo' )) {
// 			$categories = Engine_Api::_()->getDbTable('categories', 'advvideo')->getCategoriesAssoc();
// 		}
// 		$finalArray = array_merge($finalArray, $categories); 
		
		// prepare categories
    $categories = Engine_Api::_()->video()->getCategories();
    $categories_prepared[0] = "All Categories";
    foreach ($categories as $category){
      $categories_prepared[$category->category_id] = $category->category_name;
    }
    
		//if (count($finalArray)) != 0) {
		
			// category field
			$this->addElement('Select', 'category_id', array(
				'label' => 'Category',
				'order' => 1,
				'multiOptions' => $categories_prepared,
			));
		//}

		$this->addElement('Text', 'search', array(
			'label' => 'What',
			'autocomplete' => 'off',
			'description' => '(Enter keywords or Video name)',
			'order' => 2,
		));
		$this->search->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));
		
		
	  $this->addElement('Text', 'video_location', array(
			'label' => 'Where',
			'autocomplete' => 'off',
			'description' => '(address, city, state or country)',
			'order' => 3,
			'onclick' => 'locationVideo();'
		));
		$this->video_location->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));

		$flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.proximity.search.kilometer', 0);
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
				'order' => 5,
				'ignore' => true,
				'onclick' => 'return locationSearch();'
			));
			$this->addSubForm($subform, $subform->getName());
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
		
    $this->addDisplayGroup(array('advances_search', 'done', 'locationmiles', 'category_id', 'done', 'video_location', 'search'), 'grp3');
    $button_video = $this->getDisplayGroup('grp3');
    $button_video->setDecorators(array(
			'FormElements',
			'Fieldset',
			array('HtmlTag', array('tag' => 'div', 'id' => 'video3', 'class' => 'grp_field'))
    ));
    
    $video2 = array();

    //if (!empty($this->_value['street'])) {
			$this->addElement('Text', 'video_street', array(
				'label' => 'Street',
				'autocomplete' => 'off',
				'order' => 7,
			));
			$video2[] = 'video_street';
		//}

		//if (!empty($this->_value['city'])) {
			$this->addElement('Text', 'video_city', array(
				'label' => 'City',
				'autocomplete' => 'off',
				'order' => 8,
			));
			$video2[] = 'video_city';
		//}

		//if (!empty($this->_value['state'])) {
			$this->addElement('Text', 'video_state', array(
				'label' => 'State',
				'autocomplete' => 'off',
				'order' => 9,
			));
			$video2[] = 'video_state';
		//}

		//if (!empty($this->_value['country'])) {
			$this->addElement('Text', 'video_country', array(
				'label' => 'Country',
				'autocomplete' => 'off',
				'order' => 10,
			));
			$video2[] = 'video_country';
		//}

		if(!empty($video2)) {
			$this->addDisplayGroup($video2, 'grp2');
			$button_video = $this->getDisplayGroup('grp2');
			$button_video->setDecorators(array(
				'FormElements',
				'Fieldset',
				array('HtmlTag', array('tag' => 'div', 'id' => 'video2', 'class' => 'grp_field'))
			));
		}


	  $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		if (!empty($viewer_id)) {
			$this->addElement('Select', 'view_view', array(
				'label' => 'View:',
				'multiOptions' => array(
					'0' => 'Everyone\'s Videos',
					'1' => 'Only My Friends\' Videos',
				),
			));
    }

    $this->addElement('Select', 'orderby', array(
      'label' => 'Browse By:',
      'multiOptions' => array(
        'creation_date' => 'Most Recent',
        'view_count' => 'Most Viewed',
        'comment_count' => 'Most Commented',
        'rating' => 'Highest Rated',
      ),
    ));

    $this->addElement('Hidden', 'Latitude', array(
      'order' => $i--,
    ));

    $this->addElement('Hidden', 'Longitude', array(
      'order' => $i--,
    ));

    $this->addDisplayGroup(array('orderby', 'view_view'), 'grp1');
    $button_video = $this->getDisplayGroup('grp1');
    $button_video->setDecorators(array(
			'FormElements',
			'Fieldset',
			array('HtmlTag', array('tag' => 'div', 'id' => 'video1', 'class' => 'grp_field'))
    ));

    return $this;
  }
}