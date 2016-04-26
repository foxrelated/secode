<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Search.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_Search extends Siteevent_Form_Searchfields {

    protected $_fieldType = 'siteevent_event';
    protected $_searchForm;
    protected $_hasMobileMode = false;
    protected $_widgetSettings;

    public function getWidgetSettings() {
        return $this->_widgetSettings;
    }

    public function setWidgetSettings($widgetSettings) {
        $this->_widgetSettings = $widgetSettings;
        return $this;
    }

    public function getHasMobileMode() {
        return $this->_hasMobileMode;
    }

    public function setHasMobileMode($flage) {
        $this->_hasMobileMode = $flage;
        return $this;
    }

    public function init() {
        $this
                ->setAttribs(array(
                    'id' => 'filter_form',
                    'class' => 'siteevents_browse_filters field_search_criteria',
                    'method' => 'GET'
        ));
        parent::init();

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $this->loadDefaultDecorators();

        $this->getMemberTypeElement();

        $this->getAdditionalOptionsElement();

        $front = Zend_Controller_Front::getInstance();
        $module = $front->getRequest()->getModuleName();
        $controller = $front->getRequest()->getControllerName();
        $action = $front->getRequest()->getActionName();
        $resultsAction = '';
        
        if(isset($this->_widgetSettings['resultsAction']))
        $resultsAction = $this->_widgetSettings['resultsAction'];

        switch ($resultsAction) {
            case 'map':
                $this->setAction($view->url(array('action' => 'map'), 'siteevent_general', true))->getDecorator('HtmlTag')->setOption('class', 'browsesiteevents_criteria');
                break;

            case 'top-rated':
                $this->setAction($view->url(array('action' => 'top-rated'), "siteevent_general", true))->getDecorator('HtmlTag')->setOption('class', 'browsesiteevents_criteria');
                break;

            case 'pinboard':
                $this->setAction($view->url(array('action' => 'pinboard'), "siteevent_general", true))->getDecorator('HtmlTag')->setOption('class', 'browsesiteevents_criteria');
                break;

            default:
                $this->setAction($view->url(array('action' => 'index'), "siteevent_general", true))->getDecorator('HtmlTag')->setOption('class', 'browsesiteevents_criteria');
                break;
        }
    }

    public function getMemberTypeElement() {

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
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $orderwhatWhereWithinmile = -1000;
        $i = 99980;

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

        $myLocationDetails = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();

        $this->addElement('Hidden', 'Longitude', array(
            'order' => $i++,
        ));              

        $this->_searchForm = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');

        $row = $this->_searchForm->getFieldsOptions('siteevent', 'search');
        if (!empty($row) && !empty($row->display)) {
            
            $this->addElement('Text', 'search', array(
                'label' => (isset($this->_widgetSettings['whatWhereWithinmile']) && empty($this->_widgetSettings['whatWhereWithinmile'])) ? 'Name / Keyword' : 'What',
                'order' => (isset($this->_widgetSettings['whatWhereWithinmile']) && empty($this->_widgetSettings['whatWhereWithinmile']))  ? $row->order : $orderwhatWhereWithinmile,
                'autocomplete' => 'off',
                'decorators' => array(
                    'ViewHelper',
                    array('Label', array('tag' => 'span')),
                    array('HtmlTag', array('tag' => 'li'))
                ),
            ));

            if (isset($_GET['search'])) {
                $this->search->setValue($_GET['search']);
            } elseif (isset($_GET['titleAjax'])) {
                $this->search->setValue($_GET['titleAjax']);
            }
        }

        $row = $this->_searchForm->getFieldsOptions('siteevent', 'venue');
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.veneuname', 1) && !empty($row) && !empty($row->display)) {
            $this->addElement('Text', 'venue_name', array(
                'label' => 'Venue',
                'order' => $row->order,
                'decorators' => array(
                    'ViewHelper',
                    array('Label', array('tag' => 'span')),
                    array('HtmlTag', array('tag' => 'li'))
                ),
            ));
        }

        $row = $this->_searchForm->getFieldsOptions('siteevent', 'by_time');
        if (!empty($row) && !empty($row->display)) {
            $this->addElement('Select', 'event_time', array(
                'label' => 'Occurring',
                'order' => $row->order,
                'multiOptions' => array(
                    '' => "",
                    'today' => "Today",
                    'tomorrow' => "Tomorrow",
                    'this_weekend' => "This Weekend",
                    'this_week' => "This Week",
                    'this_month' => "This Month",
                ),
                'decorators' => array(
                    'ViewHelper',
                    array('Label', array('tag' => 'span')),
                    array('HtmlTag', array('tag' => 'li'))
                ),
            ));
        }

        //GET API
        $settings = Engine_Api::_()->getApi('settings', 'core');

        $row = $this->_searchForm->getFieldsOptions('siteevent', 'location');
        if (!empty($row) && !empty($row->display) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1)) {

            $advancedSearchOrder = $row->order;
            $this->addElement('Text', 'location', array(
                'label' => (isset($this->_widgetSettings['whatWhereWithinmile']) && empty($this->_widgetSettings['whatWhereWithinmile'])) ? 'Location' : 'Where',
                'order' => (isset($this->_widgetSettings['whatWhereWithinmile']) && empty($this->_widgetSettings['whatWhereWithinmile'])) ? $row->order : ++$orderwhatWhereWithinmile,
                'decorators' => array(
                    'ViewHelper',
                    array('Label', array('tag' => 'span')),
                    array('HtmlTag', array('tag' => 'li'))
                ),
                    //'value' => $location,
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
            
            if(!isset($_GET['location']) && !isset($_GET['locationSearch']) && isset($this->_widgetSettings['locationDetection']) && empty($this->_widgetSettings['locationDetection'])) {
                $this->location->setValue('');
            }

            $row = $this->_searchForm->getFieldsOptions('siteevent', 'proximity');
            if (!empty($row) && !empty($row->display)) {

                $flage = $settings->getSetting('siteevent.proximity.search.kilometer', 0);
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
                $advancedSearchOrder = $row->order + 1;
                $this->addElement('Select', 'locationmiles', array(
                    'label' => (isset($this->_widgetSettings['whatWhereWithinmile']) && empty($this->_widgetSettings['whatWhereWithinmile'])) ? $locationLable : $locationLable,
                    'multiOptions' => $locationOption,
                    'value' => 0,
                    'order' => (isset($this->_widgetSettings['whatWhereWithinmile']) && empty($this->_widgetSettings['whatWhereWithinmile'])) ? $row->order + 1 : ++$orderwhatWhereWithinmile,
                    'decorators' => $this->gethasMobileMode() ? array(
                        'ViewHelper',
                        array('Label', array('tag' => 'span')),
                        array('HtmlTag', array('tag' => 'li'))
                    ) : array(
                        'ViewHelper',
                        array('Label', array('tag' => 'span')),
                        array(array("img" => "HtmlTag"), array(
                                "tag" => "img",
                                "openOnly" => true,
                                "src" => "./application/modules/Seaocore/externals/images/help.gif",
                                "align" => "middle",
                                "class" => "siteevent_locationmiles_tip",
                                "placement" => "APPEND",
                                'title' => $view->translate('Radius targeting (also known as proximity targeting or "Target a radius") allows you to search content within a certain distance from the selected location, rather than choosing individual city, region, or country. If you want to search content in specific city, region, or country then simply do not select this option.'),
            'onclick' => 'showRadiusTip();return false;'
                            )),                        
                        array('HtmlTag', array('tag' => 'li')),
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

            $rowStreet = $this->_searchForm->getFieldsOptions('siteevent', 'street');
            if (!empty($rowStreet) && !empty($rowStreet->display)) {
                $this->addElement('Text', 'siteevent_street', array(
                    'label' => 'Street',
                    'order' => $rowStreet->order,
                    'decorators' => array(
                        'ViewHelper',
                        array('Label', array('tag' => 'span')),
                        array('HtmlTag', array('tag' => 'li'))
                    ),
                ));
            }

            $rowCity = $this->_searchForm->getFieldsOptions('siteevent', 'city');
            if (!empty($rowCity) && !empty($rowCity->display)) {
                $this->addElement('Text', 'siteevent_city', array(
                    'label' => 'City',
                    'order' => $rowCity->order,
                    'placeholder' => '',
                    'decorators' => array(
                        'ViewHelper',
                        array('Label', array('tag' => 'span')),
                        array('HtmlTag', array('tag' => 'li'))
                    ),
                ));
            }
            $rowState = $this->_searchForm->getFieldsOptions('siteevent', 'state');
            if (!empty($rowState) && !empty($rowState->display)) {
                $this->addElement('Text', 'siteevent_state', array(
                    'label' => 'State',
                    'order' => $rowState->order,
                    'decorators' => array(
                        'ViewHelper',
                        array('Label', array('tag' => 'span')),
                        array('HtmlTag', array('tag' => 'li'))
                    ),
                ));
            }
            $rowCountry = $this->_searchForm->getFieldsOptions('siteevent', 'country');
            if (!empty($rowCountry) && !empty($rowCountry->display)) {
                $this->addElement('Text', 'siteevent_country', array(
                    'label' => 'Country',
                    'order' => $rowCountry->order,
                    'decorators' => array(
                        'ViewHelper',
                        array('Label', array('tag' => 'span')),
                        array('HtmlTag', array('tag' => 'li'))
                    ),
                ));
            }
        }

        if (isset($this->_widgetSettings['viewType']) && $this->_widgetSettings['viewType'] == 'horizontal' && $this->_widgetSettings['whatWhereWithinmile'] && !$this->_widgetSettings['advancedSearch']) {

            $advancedSearch = $this->_widgetSettings['advancedSearch'];
            $this->addElement('Cancel', 'advances_search', array(
                'label' => 'Advanced search',
                'ignore' => true,
                'link' => true,
                'order' => ++$orderwhatWhereWithinmile,
                'onclick' => "advancedSearchLists($advancedSearch, 0);",
                'decorators' => array('ViewHelper'),
            ));

            $this->addElement('hidden', 'advanced_search', array(
                'value' => 0
            ));
        }

        $row = $this->_searchForm->getFieldsOptions('siteevent', 'orderby');
        if (!empty($row) && !empty($row->display)) {

            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 3 || Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 2) {
                $multiOptionsOrderBy = array(
                    '' => "",
                    'title' => "Alphabetical",
                    'event_id' => 'Recently Created',
                    'starttime' => 'Start Time',
                    'view_count' => 'Most Viewed',
                    'like_count' => "Most Liked",
                    'comment_count' => "Most Commented",
                    'member_count' => "Most Joined",
                    'review_count' => "Most Reviewed",
                    'rating_avg' => "Most Rated",
                );
            } elseif (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 1) {
                $multiOptionsOrderBy = array(
                    '' => "",
                    'title' => "Alphabetical",
                    'event_id' => 'Recently Created',
                    'starttime' => 'Start Time',
                    'view_count' => 'Most Viewed',
                    'like_count' => "Most Liked",
                    'comment_count' => "Most Commented",
                    'member_count' => "Most Joined",
                    'rating_avg' => "Most Rated",
                );
            } else {
                $multiOptionsOrderBy = array(
                    '' => "",
                    'title' => "Alphabetical",
                    'event_id' => 'Recently Created',
                    'starttime' => 'Start Time',
                    'view_count' => 'Most Viewed',
                    'like_count' => "Most Liked",
                    'comment_count' => "Most Commented",
                    'member_count' => "Most Joined",
                );
            }
            
            if (Engine_Api::_()->siteevent()->isTicketBasedEvent()) {
                unset($multiOptionsOrderBy['member_count']);
            }               

            $settings = Engine_Api::_()->getApi('settings', 'core');
            $rowLocation = $this->_searchForm->getFieldsOptions('siteevent', 'location');

            $rowProximity = $this->_searchForm->getFieldsOptions('siteevent', 'proximity');

            if (!empty($rowLocation) && !empty($rowLocation->display) && !empty($rowProximity) && !empty($rowProximity->display) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1)) {
                $multiOptionsOrderBy = array_merge($multiOptionsOrderBy, array('distance' => 'Location: Near to Far'));
            }

            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.price', 0)) {
                $multiOptionsOrderBy = array_merge($multiOptionsOrderBy, array('priceLTH' => 'Price: Low to High', 'priceHTL' => 'Price: High to Low'));
            }

            $this->addElement('Select', 'orderby', array(
                'label' => 'Browse By',
                'multiOptions' => $multiOptionsOrderBy,
                'onchange' => $this->gethasMobileMode() ? '' : 'searchSiteevents();',
                'order' => $row->order,
                'decorators' => array(
                    'ViewHelper',
                    array('Label', array('tag' => 'span')),
                    array('HtmlTag', array('tag' => 'li'))
                ),
            ));
        } else {
            $this->addElement('hidden', 'orderby', array(
            ));
        }

        $row = $this->_searchForm->getFieldsOptions('siteevent', 'show');
        if (!empty($row) && !empty($row->display)) {
            $show_multiOptions = array();
            $show_multiOptions["1"] = "Everyone's Events";
            $show_multiOptions["2"] = "Only My Friends' Events";
            $show_multiOptions["4"] = "Events I Like";
//            $show_multiOptions["5"] = "This Month Events";
//            $show_multiOptions["6"] = "This Week Events";
//            $show_multiOptions["7"] = "This Weekend Events";
//            $show_multiOptions["8"] = "Today Events";
            $value_deault = 1;
            $enableNetwork = $settings->getSetting('siteevent.network', 0);
            if (empty($enableNetwork)) {
                $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
                $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
                $viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer_id));

                if (!empty($viewerNetwork)) {
                    $show_multiOptions["3"] = 'Only My Networks';
                    $browseDefaulNetwork = $settings->getSetting('siteevent.default.show', 0);

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
                'onchange' => $this->gethasMobileMode() ? '' : 'searchSiteevents();',
                'order' => $row->order,
                'decorators' => array(
                    'ViewHelper',
                    array('Label', array('tag' => 'span')),
                    array('HtmlTag', array('tag' => 'li'))
                ),
                'value' => $value_deault,
            ));
        } else {
            $this->addElement('hidden', 'show', array(
                'value' => 1
            ));
        }

        $row = $this->_searchForm->getFieldsOptions('siteevent', 'view');
        if (!empty($row) && !empty($row->display)) {

            $this->addElement('Select', 'showEventType', array(
                'label' => 'View',
                'multiOptions' => array('upcoming' => 'Upcoming & Ongoing', 'onlyUpcoming' => 'Upcoming', 'onlyOngoing' => 'Ongoing', 'past' => 'Past'),
                'order' => $row->order,
                'decorators' => array(
                    'ViewHelper',
                    array('Label', array('tag' => 'span')),
                    array('HtmlTag', array('tag' => 'li'))
                ),
                'value' => 'upcoming',
            ));
        } else {
            $this->addElement('hidden', 'showEventType', array(
                'value' => 'upcoming'
            ));
        }

        $row = $this->_searchForm->getFieldsOptions('siteevent', 'price');
        if (!empty($row) && !empty($row->display) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.price', 0)) {

            if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode') || (isset($this->_widgetSettings['priceFieldType']) && $this->_widgetSettings['priceFieldType'] != 'slider')){
                $subform = new Engine_Form(array(
                    'description' => 'Price',
                    'elementsBelongTo' => 'price',
                    'order' => $row->order,
                    'decorators' => array(
                        'FormElements',
                        array('Description', array('placement' => 'PREPEND', 'tag' => 'label', 'class' => 'form-label')),
                        //array('Label', array('tag' => 'span')),
                        array('HtmlTag', array('tag' => 'li', 'class' => '', 'id' => 'integer-wrapper'))
                    )
                ));
                //Engine_Form::enableForm($subform);
                //unset($params['options']['label']);
                $params['options']['decorators'] = array('ViewHelper', array('HtmlTag', array('tag' => 'div', 'class' => 'form-element')));
                $params['options']['decorators'] = array('ViewHelper');
                if ($this->gethasMobileMode())
                    $params['options']['placeholder'] = 'min';
                $subform->addElement('text', 'min', $params['options']);
                if ($this->gethasMobileMode())
                    $params['options']['placeholder'] = 'max';
                $subform->addElement('text', 'max', $params['options']);
                $this->addSubForm($subform, 'price');
            }
            else {
                $this->addElement('Text', 'priceSlider', array(
                    'decorators' => array(array('ViewScript', array(
                                'viewScript' => 'application/modules/Siteevent/views/scripts/_slider.tpl',
                                'minPrice' => (isset($this->_widgetSettings['minPrice'])) ? $this->_widgetSettings['minPrice'] : 0,
                                'maxPrice' => (isset($this->_widgetSettings['minPrice'])) ? $this->_widgetSettings['minPrice'] : 999,
                                'class' => 'form element'
                            ))), 'order' => $row->order,
                ));
            }
            $this->addElement('Hidden', 'minPrice', array('order' => 8743));
            $this->addElement('Hidden', 'maxPrice', array('order' => 9777));
        }

        $row = $this->_searchForm->getFieldsOptions('siteevent', 'category_id');
        if (!empty($row) && !empty($row->display)) {
            $translate = Zend_Registry::get('Zend_Translate');
            if (isset($this->_widgetSettings['showAllCategories']) && $this->_widgetSettings['showAllCategories']) {
                $categories = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategories(array('category_id', 'category_name'), null, 0, 0, 1);
            } else {
                $categories = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategories(array('category_id', 'category_name'), null, 0, 0, 1, 0, 'cat_order', 1, 1);
            }

            if (count($categories) != 0) {
                $categories_prepared[0] = "";
                foreach ($categories as $category) {
                    $categories_prepared[$category->category_id] = $translate->translate($category->category_name);
                }

            if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
              $onChangeEvent = "showFields(this.value, 1); addOptions(this.value, 'cat_dependency', 'subcategory_id', 0);";
              $categoryFiles = 'application/modules/Siteevent/views/scripts/_subCategory.tpl';
            } else {
              $onChangeEvent = "showSMFields(this.value, 1);sm4.core.category.set(this.value, 'subcategory');";
              $categoryFiles = 'application/modules/Siteevent/views/sitemobile/scripts/_subCategory.tpl';
            }
            
                $this->addElement('Select', 'category_id', array(
                    'label' => 'Category',
                    'order' => $row->order,
                    'multiOptions' => $categories_prepared,
                    'onchange' => $onChangeEvent,
                    'decorators' => array(
                        'ViewHelper',
                        array('Label', array('tag' => 'span')),
                        array('HtmlTag', array('tag' => 'li'))),
                ));

                if(isset($this->_widgetSettings['showAllCategories'])) {
                    $this->addElement('Select', 'subcategory_id', array(
                        'RegisterInArrayValidator' => false,
                        'order' => $row->order + 1,
                        'decorators' => array(array('ViewScript', array(
                                    'showAllCategories' => isset($this->_widgetSettings['showAllCategories']) ? $this->_widgetSettings['showAllCategories'] : 1,
                                    'viewScript' => $categoryFiles,
                                    'class' => 'form element')))
                    ));
                } else {
                     $this->addElement('Select', 'subcategory_id', array(
                        'RegisterInArrayValidator' => false,
                        'order' => $row->order + 1,
                        'decorators' => array(array('ViewScript', array(
                                    'viewScript' => $categoryFiles,
                                    'class' => 'form element')))
                    ));
                }
                
                
            }
        }

        $row = $this->_searchForm->getFieldsOptions('siteevent', 'dates');
        if (!empty($row) && !empty($row->display)) {
            $start = new Engine_Form_Element_CalendarDateTime('starttimesearchsiteevent');
            $start->setLabel("From");
    
            if ($module == 'siteadvsearch' && $controller == 'index' && $action == 'browse-page') {
							$start->setAttrib('loadedbyAjax', 'TRUE');
            }
            //$start->setAllowEmpty(false);
            $start->setOrder($row->order);

            $start->addDecorators(array(
                array('ViewHelper'),
                array('HtmlTag', array('tag' => 'li')),
                array('Label', array('tag' => 'span')),
            ));

            $this->addElement($start);

            // End time
            $end = new Engine_Form_Element_CalendarDateTime('endtimesearchsiteevent');
            $translate = Zend_Registry::get('Zend_Translate');
            $to = $translate->translate("To");
            
            $end->setLabel($to);
            if ($module == 'siteadvsearch' && $controller == 'index' && $action == 'browse-page') {
							$end->setAttrib('loadedbyAjax', 'TRUE');
            }
//            $end->setAllowEmpty(false);
            $end->setOrder($row->order + 1);

            $end->addDecorators(array(
                array('ViewHelper'),
                array('HtmlTag', array('tag' => 'li')),
                array('Label', array('tag' => 'span')),
            ));
            $this->addElement($end);
        }

        $row = $this->_searchForm->getFieldsOptions('siteevent', 'has_photo');
        if (!empty($row) && !empty($row->display)) {
            $this->addElement('Checkbox', 'has_photo', array(
                'label' => "Only Events With Photos",
                'order' => $row->order,
                'decorators' => array(
                    'ViewHelper',
                    array('Label', array('placement' => 'APPEND', 'tag' => 'label')),
                    array('HtmlTag', array('tag' => 'li'))
                ),
            ));
        }

        $row = $this->_searchForm->getFieldsOptions('siteevent', 'has_free_price');
        if (!empty($row) && !empty($row->display) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.price', 0)) {
            $this->addElement('Checkbox', 'has_free_price', array(
                'label' => "Only Free Events",
                'order' => $row->order,
                'decorators' => array(
                    'ViewHelper',
                    array('Label', array('placement' => 'APPEND', 'tag' => 'label')),
                    array('HtmlTag', array('tag' => 'li'))
                ),
            ));
        }

        $row = $this->_searchForm->getFieldsOptions('siteevent', 'has_review');
        if (!empty($row) && !empty($row->display) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2)) {

            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 3) {
                $multiOptions = array(
                    '' => '',
                    'rating_avg' => 'Any Review',
                    'rating_editor' => 'Editor Reviews',
                    'rating_users' => 'User Reviews',
                );
            } elseif (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 2) {
                $multiOptions = array(
                    '' => '',
                    'rating_users' => 'User Reviews',
                );
            } elseif (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 1) {
                $multiOptions = array(
                    '' => '',
                    'rating_editor' => 'Editor Reviews',
                );
            }

            $this->addElement('Select', 'has_review', array(
                'label' => "Events Having",
                'multiOptions' => $multiOptions,
                'onchange' => $this->gethasMobileMode() ? '' : 'searchSiteevents();',
                'order' => $row->order,
                'decorators' => array(
                    'ViewHelper',
                    array('Label', array('tag' => 'span')),
                    array('HtmlTag', array('tag' => 'li'))
                ),
                'value' => '',
            ));
        }

        $row = $this->_searchForm->getFieldsOptions('siteevent', 'content_type');
        if (!empty($row) && !empty($row->display)) {
            $contentTypes = Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array("enabled" => 1));
            $contentTypeArray = array();
            if (!empty($contentTypes)) {
                $contentTypeArray[] = 'All';
                $moduleTitle = '';
                foreach ($contentTypes as $contentType) {
                    $contentTypeArray['user'] = Zend_Registry::get('Zend_Translate')->translate('Member Events');
                    $contentTypeArray[$contentType['item_type']] = $contentType['item_title'];
// 							if(Engine_Api::_()->hasModuleBootstrap('sitereview') && Engine_Api::_()->hasModuleBootstrap('sitereviewlistingtype')) {
// 								$moduleTitle = 'Reviews & Ratings - Multiple Listing Types';
// 							} elseif(Engine_Api::_()->hasModuleBootstrap('sitereview')) {
// 								$moduleTitle = 'Reviews & Ratings';
// 							}
// 							$explodedResourceType = explode('_', $contentType['item_type']);
// 							if(isset($explodedResourceType[2]) && $moduleTitle){
// 								$listingtypesTitle = Engine_Api::_()->getDbtable('listingtypes', 'sitereview')->getListingRow($explodedResourceType[2])->title_plural;
// 								$listingtypesTitle = $listingtypesTitle . ' ( ' .$moduleTitle . ' ) ';
// 								$contentTypeArray[$contentType['item_type']] = $listingtypesTitle;
// 							} else {
// 								$contentTypeArray[$contentType['item_type']] = Engine_Api::_()->getDbtable('modules', 'siteevent')->getModuleTitle($contentType['item_module']);
// 							}	
                }
            }
            if (!empty($contentTypeArray)) {
                $this->addElement('Select', 'eventType', array(
                    'label' => "Event Type",
                    'multiOptions' => $contentTypeArray,
                    'onchange' => $this->gethasMobileMode() ? '' : 'searchSiteevents();',
                    'order' => $row->order,
                    'decorators' => array(
                        'ViewHelper',
                        array('Label', array('tag' => 'span')),
                        array('HtmlTag', array('tag' => 'li'))
                    ),
                    'value' => '',
                ));
            } else {
                $this->addElement('Hidden', 'eventType', array(
                    'label' => "Event Type",
                    'order' => $row->order,
                    'value' => 'All',
                ));
            }
        }

        if ($this->gethasMobileMode()) {
            $this->addElement('Button', 'done', array(
                'label' => 'Search',
                'type' => 'submit',
                'ignore' => true,
                'order' => 999999999,
                'decorators' => array(
                    'ViewHelper',
                    //array('Label', array('tag' => 'span')),
                    array('HtmlTag', array('tag' => 'li'))
                ),
            ));
        } else {
            $this->addElement('Button', 'done', array(
                'label' => 'Search',
                'onclick' => 'searchSiteevents();',
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

}