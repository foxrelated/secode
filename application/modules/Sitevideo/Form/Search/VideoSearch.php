<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: VideoSearch.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Search_VideoSearch extends Sitevideo_Form_Searchvideofields {

    protected $_fieldType = 'video';
    protected $_searchForm;
    protected $_searchFormSettings;
    protected $_widgetSettings;

    public function getWidgetSettings() {
        return $this->_widgetSettings;
    }

    public function setWidgetSettings($widgetSettings) {
        $this->_widgetSettings = $widgetSettings;
        return $this;
    }

    public function init() {
        $this
                ->setAttribs(array(
                    'id' => 'filter_form',
                    'class' => 'sitevideo_browse_filters field_search_criteria',
                    'method' => 'GET'
        ));

        parent::init();
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        $this->loadDefaultDecorators();

        $this->getVideoTypeElement();

        //GET SEARCH FORM SETTINGS
        $this->_searchFormSettings = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getModuleOptions('sitevideo_video');

        $this->getAdditionalOptionsElement();

        $front = Zend_Controller_Front::getInstance();
        $module = $front->getRequest()->getModuleName();
        $controller = $front->getRequest()->getControllerName();
        $action = $front->getRequest()->getActionName();
        switch (true) {
            case ($controller == 'video' && $action == 'pinboard'):
                $this->setAction($view->url(array('action' => 'pinboard'), "sitevideo_video_general", true))->getDecorator('HtmlTag')->setOption('class', 'browsesitevideos_criteria');
                break;
            default:
                $this->setAction($view->url(array('action' => 'browse'), 'sitevideo_video_general', true))->getDecorator('HtmlTag')->setOption('class', 'browsesitevideos_criteria');
                break;
        }
    }

    public function getVideoTypeElement() {

        $multiOptions = array('' => ' ');
        $profileTypeFields = Engine_Api::_()->fields()->getFieldsObjectsByAlias($this->_fieldType, 'profile_type');
        if (count($profileTypeFields) !== 1 || !isset($profileTypeFields['profile_type']))
            return;
        $profileTypeField = $profileTypeFields['profile_type'];

        $options = $profileTypeField->getOptions();

        foreach ($options as $option) {
            $multiOptions[$option->option_id] = $option->label;
        }

        $this->addElement('hidden', 'profile_type', array(
            'order' => -1000001,
            'class' =>
            'field_toggle' . ' ' .
            'parent_' . 0 . ' ' .
            'option_' . 0 . ' ' .
            'field_' . $profileTypeField->field_id . ' ',
            'onchange' => 'changeFields($(this));',
            'multiOptions' => $multiOptions,
        ));
        return $this->profile_type;
    }

    public function getAdditionalOptionsElement() {

        $orderwhatWhereWithinmile = -1000;
        $i = 99980;
        $rowOrder = 50;

        $this->addElement('Hidden', 'city', array(
            'order' => $i++,
        ));
        $this->addElement('Hidden', 'tag', array(
            'order' => $i++,
        ));

        $this->addElement('Hidden', 'tag_id', array(
            'order' => $i++,
        ));
        $this->addElement('Hidden', 'viewFormat', array(
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
            'order' => $i++,
        ));

        $front = Zend_Controller_Front::getInstance();
        $module = $front->getRequest()->getModuleName();
        $controller = $front->getRequest()->getControllerName();
        $action = $front->getRequest()->getActionName();
        $settings = Engine_Api::_()->getApi('settings', 'core');

        $myLocationDetails = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();

        $this->addElement('Hidden', 'Longitude', array(
            'order' => $i++,
        ));


        if (!empty($this->_searchFormSettings['search']) && !empty($this->_searchFormSettings['search']['display'])) {
            $this->addElement('Text', 'search', array(
                'label' => empty($this->_widgetSettings['whatWhereWithinmile']) ? 'Name / Keyword' : 'What',
                'order' => empty($this->_widgetSettings['whatWhereWithinmile']) ? $this->_searchFormSettings['search']['order'] : $orderwhatWhereWithinmile++,
                'autocomplete' => 'off',
                'decorators' => array(
                    'ViewHelper',
                    array('HtmlTag', array('tag' => 'div')),
                    array('Label', array('tag' => 'div')),
                    array('HtmlTag2', array('tag' => 'li'))
                ),
            ));

            if (isset($_GET['search'])) {
                $this->search->setValue($_GET['search']);
            } elseif (isset($_GET['titleAjax'])) {
                $this->search->setValue($_GET['titleAjax']);
            }
        }
        if (!empty($this->_searchFormSettings['view']) && !empty($this->_searchFormSettings['view']['display'])) {
            $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
            if (!empty($viewer_id)) {
                $show_multiOptions = array();
                $show_multiOptions["0"] = 'Everyone\'s Videos';
                $show_multiOptions["1"] = 'Only My Friends\' Videos';
                $value_deault = 0;
                $enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.network', 0);
                if (empty($enableNetwork)) {
                    $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
                    $viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer_id));
                    if (!empty($viewerNetwork) || Engine_Api::_()->sitevideo()->videoBaseNetworkEnable()) {
                        $show_multiOptions["3"] = 'Only My Networks';
                        $browseDefaulNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.default.show', 0);

                        if (!isset($_GET['view_view']) && !empty($browseDefaulNetwork)) {
                            $value_deault = 3;
                        } elseif (isset($_GET['view_view'])) {
                            $value_deault = $_GET['view_view'];
                        }
                    }
                }
                $this->addElement('Select', 'view_view', array(
                    'label' => 'View',
                    'order' => $this->_searchFormSettings['view']['order'],
                    'multiOptions' => $show_multiOptions,
                    'onchange' => 'searchSitevideos();',
                    'decorators' => array(
                        'ViewHelper',
                        array('HtmlTag', array('tag' => 'div')),
                        array('Label', array('tag' => 'div')),
                        array('HtmlTag2', array('tag' => 'li'))
                    ),
                    'value' => $value_deault,
                ));
            }
        }

        if (!empty($this->_searchFormSettings['orderby']) && !empty($this->_searchFormSettings['orderby']['display'])) {
            $multiOPtionsOrderBy = array(
                '' => '',
                'creation_date' => 'Recently Created',
                'modified_date' => 'Recently Updated',
                'view_count' => 'Most Popular',
                'like_count' => 'Most Liked',
                'comment_count' => 'Most Commented',
                'title' => "Alphabetical (A-Z)",
                'title_reverse' => 'Alphabetical (Z-A)'
            );
            //GET API

            $enableRating = $settings->getSetting('sitevideo.rating', 1);

            if ($enableRating) {
                $multiOPtionsOrderBy = array_merge($multiOPtionsOrderBy, array('rating' => 'Most Rated'));
            }
            $this->addElement('Select', 'orderby', array(
                'label' => 'Browse By',
                'order' => $this->_searchFormSettings['orderby']['order'],
                'multiOptions' => $multiOPtionsOrderBy,
                'onchange' => 'searchSitevideos();',
                'decorators' => array(
                    'ViewHelper',
                    array('HtmlTag', array('tag' => 'div')),
                    array('Label', array('tag' => 'div')),
                    array('HtmlTag2', array('tag' => 'li'))
                ),
            ));
        }

        if (!empty($this->_searchFormSettings['location']) && !empty($this->_searchFormSettings['location']['display']) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.location', 0)) {
            $this->addElement('Text', 'location', array(
                'label' => empty($this->_widgetSettings['whatWhereWithinmile']) ? 'Location' : 'Where',
                'order' => empty($this->_widgetSettings['whatWhereWithinmile']) ? $this->_searchFormSettings['location']['order'] : $orderwhatWhereWithinmile++,
                'placeholder' => 'Enter a location',
                'decorators' => array(
                    'ViewHelper',
                    array('HtmlTag', array('tag' => 'div')),
                    array('Label', array('tag' => 'div')),
                    array('HtmlTag2', array('tag' => 'li'))
                ),
            ));
            $myLocationDetails = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
            if (isset($_GET['location'])) {
                $this->location->setValue($_GET['location']);
            } elseif (isset($_GET['locationSearch'])) {
                $this->location->setValue($_GET['locationSearch']);
            } elseif (isset($myLocationDetails['location'])) {
                $this->location->setValue($myLocationDetails['location']);
            }

            if (isset($_GET['location']) || isset($_GET['locationSearch'])) {

                Engine_Api::_()->seaocore()->setMyLocationDetailsCookie($myLocationDetails);
            }

            if (!isset($_GET['location']) && !isset($_GET['locationSearch']) && isset($this->_widgetSettings['locationDetection']) && empty($this->_widgetSettings['locationDetection'])) {
                $this->location->setValue('');
            }

            if (!empty($this->_searchFormSettings['proximity']) && !empty($this->_searchFormSettings['proximity']['display'])) {
                $flage = $settings->getSetting('sitevideo.video.proximity.search.kilometer', 0);
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
                    'label' => empty($this->_widgetSettings['whatWhereWithinmile']) ? $locationLable : $locationLable,
                    'multiOptions' => $locationOption,
                    'value' => 0,
                    'order' => empty($this->_widgetSettings['whatWhereWithinmile']) ? $this->_searchFormSettings['proximity']['order'] : $orderwhatWhereWithinmile++,
                    'decorators' => array(
                        'ViewHelper',
                        array('HtmlTag', array('tag' => 'div')),
                        array('Label', array('tag' => 'div')),
                        array('HtmlTag2', array('tag' => 'li'))
                    ),
                ));

                if (isset($_GET['locationmiles'])) {
                    $this->locationmiles->setValue($_GET['locationmiles']);
                } elseif (isset($_GET['locationmilesSearch'])) {
                    $this->locationmiles->setValue($_GET['locationmilesSearch']);
                } elseif (isset($myLocationDetails['locationmiles'])) {
                    $this->locationmiles->setValue($myLocationDetails['locationmiles']);
                }
            }
            if (!empty($this->_searchFormSettings['street']) && !empty($this->_searchFormSettings['street']['display'])) {
                $this->addElement('Text', 'video_street', array(
                    'label' => 'Street',
                    'order' => $this->_searchFormSettings['street']['order'],
                    'decorators' => array(
                        'ViewHelper',
                        array('HtmlTag', array('tag' => 'div')),
                        array('Label', array('tag' => 'div')),
                        array('HtmlTag2', array('tag' => 'li'))
                    ),
                ));
            }

            if (!empty($this->_searchFormSettings['city']) && !empty($this->_searchFormSettings['city']['display'])) {
                $this->addElement('Text', 'video_city', array(
                    'label' => 'City',
                    'placeholder' => '',
                    'order' => $this->_searchFormSettings['city']['order'],
                    'decorators' => array(
                        'ViewHelper',
                        array('HtmlTag', array('tag' => 'div')),
                        array('Label', array('tag' => 'div')),
                        array('HtmlTag2', array('tag' => 'li'))
                    ),
                ));
            }

            if (!empty($this->_searchFormSettings['state']) && !empty($this->_searchFormSettings['state']['display'])) {
                $this->addElement('Text', 'video_state', array(
                    'label' => 'State',
                    'order' => $this->_searchFormSettings['state']['order'],
                    'decorators' => array(
                        'ViewHelper',
                        array('HtmlTag', array('tag' => 'div')),
                        array('Label', array('tag' => 'div')),
                        array('HtmlTag2', array('tag' => 'li'))
                    ),
                ));
            }

            if (!empty($this->_searchFormSettings['country']) && !empty($this->_searchFormSettings['country']['display'])) {
                $this->addElement('Text', 'video_country', array(
                    'label' => 'Country',
                    'order' => $this->_searchFormSettings['country']['order'],
                    'decorators' => array(
                        'ViewHelper',
                        array('HtmlTag', array('tag' => 'div')),
                        array('Label', array('tag' => 'div')),
                        array('HtmlTag2', array('tag' => 'li'))
                    ),
                ));
            }
        }
        if ($this->_widgetSettings['viewType'] == 'horizontal' && $this->_widgetSettings['whatWhereWithinmile'] && !$this->_widgetSettings['advancedSearch']) {
            $advancedSearch = $this->_widgetSettings['advancedSearch'];
            $this->addElement('Cancel', 'advances_search', array(
                'label' => 'Advanced search',
                'ignore' => true,
                'link' => true,
                'order' => $orderwhatWhereWithinmile++,
                'onclick' => "advancedSearchLists($advancedSearch, 0);",
                'decorators' => array('ViewHelper'),
            ));

            $this->addElement('hidden', 'advanced_search', array(
                'value' => 0
            ));
        }
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.category.enabled', 1)) {
            if (!empty($this->_searchFormSettings['category_id']) && !empty($this->_searchFormSettings['category_id']['display'])) {
                $translate = Zend_Registry::get('Zend_Translate');
                if (!$this->_widgetSettings['showAllCategories']) {
                    $categories = Engine_Api::_()->getDbTable('videoCategories', 'sitevideo')->getCategories(array('fetchColumns' => array('category_id', 'category_name'), 'sponsored' => 0, 'cat_depandancy' => 1, 'limit' => 0, 'orderBy' => 'category_name', 'havingVideos' => 1));
                } else {
                    $categories = Engine_Api::_()->getDbTable('videoCategories', 'sitevideo')->getCategories(array('fetchColumns' => array('category_id', 'category_name'), 'sponsored' => 0, 'cat_depandancy' => 1, 'orderBy' => 'category_name'));
                }

                if (count($categories) != 0) {
                    $categories_prepared[0] = "";
                    foreach ($categories as $category) {
                        $categories_prepared[$category->category_id] = $translate->translate($category->category_name);
                    }

                    if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
                        $onChangeEvent = "showCustomFields(this.value, 1); addOptions(this.value, 'cat_dependency', 'subcategory_id', 0);";
                        $categoryFiles = 'application/modules/Sitevideo/views/scripts/_videoSubCategory.tpl';
                    } else {
                        $onChangeEvent = "showSMFields(this.value, 1);sm4.core.category.set(this.value, 'subcategory');";
                        $categoryFiles = 'application/modules/Sitevideo/views/sitemobile/scripts/_subCategory.tpl';
                    }
                    $this->addElement('Select', 'category_id', array(
                        'label' => 'Category',
                        'order' => $this->_searchFormSettings['category_id']['order'],
                        'multiOptions' => $categories_prepared,
                        'onchange' => $onChangeEvent,
                        'decorators' => array(
                            'ViewHelper',
                            array('HtmlTag', array('tag' => 'div')),
                            array('Label', array('tag' => 'div')),
                            array('HtmlTag2', array('tag' => 'li'))),
                    ));

                    $this->addElement('Select', 'subcategory_id', array(
                        'RegisterInArrayValidator' => false,
                        'order' => $this->_searchFormSettings['category_id']['order'] + 1,
                        'decorators' => array(array('ViewScript', array(
                                    'showAllCategories' => $this->_widgetSettings['showAllCategories'],
                                    'viewScript' => $categoryFiles,
                                    'class' => 'form element')))
                    ));

                    $this->addElement('Select', 'subsubcategory_id', array(
                        'RegisterInArrayValidator' => false,
                        'order' => $this->_searchFormSettings['category_id']['order'] + 1,
                        'decorators' => array(array('ViewScript', array(
                                    'showAllCategories' => $this->_widgetSettings['showAllCategories'],
                                    'viewScript' => $categoryFiles,
                                    'class' => 'form element')))
                    ));
                }
            }
        }
        $this->_searchForm = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');
        $row = $this->_searchForm->getFieldsOptions('sitevideo_video', 'content_type');
        if (!empty($row) && !empty($row->display)) {
            $contentTypes = Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array("enabled" => 1));
            $contentTypeArray = array();
            if (!empty($contentTypes)) {
                $contentTypeArray[] = 'All';
                $moduleTitle = '';
                foreach ($contentTypes as $contentType) {
                    $contentTypeArray['user'] = Zend_Registry::get('Zend_Translate')->translate('Member Videos');
                    $contentTypeArray[$contentType['item_type']] = $contentType['item_title'];
                }
            }
            if (!empty($contentTypeArray)) {
                $this->addElement('Select', 'videoType', array(
                    'label' => "Video Type",
                    'multiOptions' => $contentTypeArray,
                    'onchange' => 'searchSitevideos();',
                    'order' => $row->order,
                    'decorators' => array(
                        'ViewHelper',
                        array('Label', array('tag' => 'span')),
                        array('HtmlTag', array('tag' => 'li'))
                    ),
                    'value' => '',
                ));
            } else {
                $this->addElement('Hidden', 'videoType', array(
                    'label' => "Video Type",
                    'order' => $row->order,
                    'value' => 'All',
                ));
            }
        }

        $this->addElement('Button', 'done', array(
            'label' => 'Search',
            'onclick' => 'searchSitevideos();',
            //'ignore' => true,
            'order' => 999999999,
            'decorators' => array(
                'ViewHelper',
                //array('Label', array('tag' => 'span')),
                array('HtmlTag', array('tag' => 'li'))
            ),
        ));
    }

}
