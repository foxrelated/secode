<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Search.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Form_Search extends Fields_Form_Search {

  protected $_fieldType = 'list_listing';
	protected $_searchForm;

  public function init() {

    parent::init();

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $this->loadDefaultDecorators();

    $this
        ->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'lists_browse_filters field_search_criteria',
                'method' => 'get'
        ));

		$this->getMemberTypeElement();

    $this->getAdditionalOptionsElement();

    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $controller = $front->getRequest()->getControllerName();
    $action = $front->getRequest()->getActionName();

		if($module == 'list' && $controller == 'index' && $action == 'manage') {
			$this->setAction($view->url(array('action' => 'manage'), 'list_general', true))->getDecorator('HtmlTag')->setOption('class', 'browselists_criteria');
		}
		else {
			$this->setAction($view->url(array('action' => 'index'), 'list_general', true))->getDecorator('HtmlTag')->setOption('class', 'browselists_criteria');
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

    $i = 99990;

    $this->addElement('Hidden', 'page', array(
            'order' => $i++,
    ));

    $this->addElement('Hidden', 'tag', array(
            'order' => $i++,
    ));

    $this->addElement('Hidden', 'tag_id', array(
            'order' => $i++,
    ));

    $this->addElement('Hidden', 'city', array(
            'order' => $i++,
    ));

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

		$this->_searchForm = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');

    $row = $this->_searchForm->getFieldsOptions('list', 'search');
    if (!empty($row) && !empty($row->display)) {
			$this->addElement('Text', 'search', array(
							'label' => 'Name / Keyword',
							'order' => $row->order,
							'decorators' => array(
											'ViewHelper',
											array('Label', array('tag' => 'span')),
											array('HtmlTag', array('tag' => 'li'))
							),
			));
		}

    $list_fields = Zend_Registry::get('list_fields');
    if (empty($list_fields)) {
      exit();
    }

		//GET API
		$settings = Engine_Api::_()->getApi('settings', 'core');

		$locationField = $settings->getSetting('list.locationfield', 1);
    $row = $this->_searchForm->getFieldsOptions('list', 'list_location');
    if (!empty($row) && !empty($row->display) && !empty($locationField)) {
			$this->addElement('Text', 'list_location', array(
							'label' => 'Location',
							'order' => $row->order,
							'decorators' => array(
											'ViewHelper',
											array('Label', array('tag' => 'span')),
											array('HtmlTag', array('tag' => 'li'))
							),
			));

			$enable = $settings->getSetting('list.proximitysearch', 1);
			if (!empty($enable)) {

				$flage = $settings->getSetting('list.proximity.search.kilometer', 0);
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
								'value' => 'normal',
								'order' => $row->order + 1,
								'decorators' => array(
												'ViewHelper',
												array('Label', array('tag' => 'span')),
												array('HtmlTag', array('tag' => 'li'))
								),
				));
			}
		}

    $row = $this->_searchForm->getFieldsOptions('list', 'orderby');
    if (!empty($row) && !empty($row->display)) {
			$this->addElement('Select', 'orderby', array(
							'label' => 'Browse By',
							'multiOptions' => array(
											'' => "",
											'creation_date' => 'Most Recent',
											'view_count' => 'Most Viewed',
											'title' => "Alphabetic",
							),
							'onchange' => 'searchLists();',
							'order' => $row->order,
							'decorators' => array(
											'ViewHelper',
											array('Label', array('tag' => 'span')),
											array('HtmlTag', array('tag' => 'li'))
							),
			));
		}
    else {
      $this->addElement('hidden', 'orderby', array(      
      ));
    }

    $row = $this->_searchForm->getFieldsOptions('list', 'closed');
    if (!empty($row) && !empty($row->display)) {
			$this->addElement('Select', 'closed', array(
							'label' => 'Status',
							'multiOptions' => array(
											'' => 'All Listings',
											'0' => 'Only Open Listings',
											'1' => 'Only Closed Listings',
							),
							'onchange' => 'searchLists();',
							'order' => $row->order,
							'decorators' => array(
											'ViewHelper',
											array('Label', array('tag' => 'span')),
											array('HtmlTag', array('tag' => 'li'))
							),
			));
		}

    $row = $this->_searchForm->getFieldsOptions('list', 'show');
    if (!empty($row) && !empty($row->display)) {
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

			$this->addElement('Select', 'show', array(
							'label' => 'Show',
							'multiOptions' => $show_multiOptions,
							'onchange' => 'searchLists();',
							'order' => $row->order,
							'decorators' => array(
											'ViewHelper',
											array('Label', array('tag' => 'span')),
											array('HtmlTag', array('tag' => 'li'))
							),
							'value' => $value_deault,
			));
		}
    else {
      $this->addElement('hidden', 'show', array(
              'value' => 1
      ));
    }

    $row = $this->_searchForm->getFieldsOptions('list', 'category_id');
    if (!empty($row) && !empty($row->display)) {

			$categories = Engine_Api::_()->getDbTable('categories', 'list')->getCategories(0, 0);
			if (count($categories) != 0) {
				$categories_prepared[0] = "";
				foreach ($categories as $category) {
					$categories_prepared[$category->category_id] = $category->category_name;
				}

				$this->addElement('Select', 'category_id', array(
								'label' => 'Category',
								'order' => $row->order,
								'multiOptions' => $categories_prepared,
								'onchange' => " var profile_type = getProfileType($(this).value);
															$('profile_type').value = profile_type;
															changeFields($('profile_type'));
															subcategories(this.value, '', '');",
									'decorators' => array(
												'ViewHelper',
												array('Label', array('tag' => 'span')),
												array('HtmlTag', array('tag' => 'li'))),
				));
			
				$this->addElement('Select', 'subcategory_id', array(
						'RegisterInArrayValidator' => false,
						'order' => $row->order+1,
						'decorators' => array(array('ViewScript', array(
																		'viewScript' => 'application/modules/List/views/scripts/_Subcategory.tpl',
																		'class' => 'form element')))
					));

				$this->addElement('Select', 'subsubcategory_id', array(
						'RegisterInArrayValidator' => false,
						'order' => $row->order+2,
						'decorators' => array(array('ViewScript', array(
																		'viewScript' => 'application/modules/List/views/scripts/_Subcategory.tpl',
																		'class' => 'form element')))
				));
			}
		}

    $row = $this->_searchForm->getFieldsOptions('list', 'has_photo');
    if (!empty($row) && !empty($row->display)) {
			$this->addElement('Checkbox', 'has_photo', array(
							'label' => 'Only Listings With Photos',
							'order' => $row->order,
							'decorators' => array(
											'ViewHelper',
											array('Label', array('placement' => 'APPEND', 'tag' => 'label')),
											array('HtmlTag', array('tag' => 'li'))
							),
			));
		}

    $row = $this->_searchForm->getFieldsOptions('list', 'has_review');
    if (!empty($row) && !empty($row->display)) {
			$this->addElement('Checkbox', 'has_review', array(
							'label' => 'Only Listings With Reviews',
							'order' => $row->order,
							'decorators' => array(
											'ViewHelper',
											array('Label', array('placement' => 'APPEND', 'tag' => 'label')),
											array('HtmlTag', array('tag' => 'li'))
							),
			));
		}

    $this->addElement('Button', 'done', array(
            'label' => 'Search',
            'onclick' => 'searchLists();',
            'ignore' => true,
            'order' => 999999999,
            'decorators' => array(
                    'ViewHelper',
                    //array('Label', array('tag' => 'span')),
                    array('HtmlTag', array('tag' => 'li'))
            ),
    ));
  }

}