<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Locationsearch.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_Locationsearch extends Fields_Form_Search {

    protected $_searchForm;
    protected $_fieldType = 'siteevent_event';
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

        if ($module == 'siteevent' && $controller == 'index' && $action != 'map') {
            $this->setAction($view->url(array('action' => 'map'), 'siteevent_general', true))->getDecorator('HtmlTag')->setOption('class', '');
        }
    }

    public function getMemberTypeElement() {

        $multiOptions = array('' => ' ');
        $profileTypeFields = Engine_Api::_()->fields()->getFieldsObjectsByAlias('siteevent_event', 'profile_type');
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
        $order = 1;
        $row = $this->_searchForm->getFieldsOptions('siteevent', 'search');
        if (!empty($row) && !empty($row->display)) {
            $this->addElement('Text', 'search', array(
                'label' => 'What',
                'autocomplete' => 'off',
                'description' => '(Enter keywords or Event name)',
                'order' => $order,
            ));
            $this->search->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));
        }

        $row = $this->_searchForm->getFieldsOptions('siteevent', 'location');
        if (!empty($row) && !empty($row->display) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1)) {
            $this->addElement('Text', 'location', array(
                'label' => 'Where',
                'autocomplete' => 'off',
                'description' => Zend_Registry::get('Zend_Translate')->_('(address, city, state or country)'),
                'order' => ++$order,
                'onclick' => 'locationPage();'
            ));
            $this->location->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));

            $myLocationDetails = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
            
            if (isset($myLocationDetails['location'])) {
                $this->location->setValue($myLocationDetails['location']);
            }            

            if (isset($_POST['location'])) {
                if (($_POST['location'])) {
                    $myLocationDetails['location'] = $_POST['location'];
                    $myLocationDetails['latitude'] = $_POST['Latitude'];
                    $myLocationDetails['longitude'] = $_POST['Longitude'];
                    $myLocationDetails['locationmiles'] = $_POST['locationmiles'];
                }

                Engine_Api::_()->seaocore()->setMyLocationDetailsCookie($myLocationDetails);
            }
            
            if(!isset($_POST['location']) && empty($this->_widgetSettings['locationDetection'])) {
                $this->location->setValue('');
            }              

            $row = $this->_searchForm->getFieldsOptions('siteevent', 'proximity');
            if (!empty($row) && !empty($row->display)) {
                $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.proximity.search.kilometer', 0);
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
                    'order' => ++$order,
//                    'decorators' => array(
//                        'ViewHelper',
//                        array('Label', array('tag' => 'span')),
//                        array(array("img" => "HtmlTag"), array(
//                                "tag" => "img",
//                                "openOnly" => true,
//                                "src" => "./application/modules/Seaocore/externals/images/help.gif",
//                                "align" => "middle",
//                                "class" => "siteevent_locationmiles_tip",
//                                "placement" => "APPEND",
//                                'title' => 'Radius targeting (also known as proximity targeting or "Target a radius") allows you to search content within a certain distance from the selected location, rather than choosing individual city, region, or country. If you want to search content in specific city, region, or country then simply do not select this option.',
//                            )),                        
//                        array('HtmlTag', array('tag' => 'li')),
//                    ),                    
                ));               

                if (isset($myLocationDetails['locationmiles'])) {
                    $this->locationmiles->setValue($myLocationDetails['locationmiles']);
                }
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
        } else {
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
            'order' => ++$order,
            'onclick' => 'advancedSearchLists();',
            'decorators' => array('ViewHelper'),
        ));

        $this->addElement('hidden', 'advanced_search', array(
            'value' => 0
        ));

        $this->addDisplayGroup(array('advances_search', 'locationmiles', 'search', 'done', 'location'), 'grp3');
        $button_group = $this->getDisplayGroup('grp3');
        $button_group->setDecorators(array(
            'FormElements',
            'Fieldset',
            array('HtmlTag', array('tag' => 'li', 'id' => 'group3', 'style' => 'width:100%;'))
        ));

        $group2 = array();

        $row = $this->_searchForm->getFieldsOptions('siteevent', 'location');
        if (!empty($row) && !empty($row->display) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1)) {
            $rowStreet = $this->_searchForm->getFieldsOptions('siteevent', 'street');
            if (!empty($rowStreet) && !empty($rowStreet->display)) {
                $this->addElement('Text', 'siteevent_street', array(
                    'label' => 'Street',
                    'autocomplete' => 'off',
                    'order' => ++$order,
                ));
                $group2[] = 'siteevent_street';
            }

            $rowCity = $this->_searchForm->getFieldsOptions('siteevent', 'city');
            if (!empty($rowCity) && !empty($rowCity->display)) {
                $this->addElement('Text', 'siteevent_city', array(
                    'label' => 'City',
                    'placeholder' => '',
                    'autocomplete' => 'off',
                    'order' => ++$order,
                ));
                $group2[] = 'siteevent_city';
            }

            $rowState = $this->_searchForm->getFieldsOptions('siteevent', 'state');
            if (!empty($rowState) && !empty($rowState->display)) {
                $this->addElement('Text', 'siteevent_state', array(
                    'label' => 'State',
                    'autocomplete' => 'off',
                    'order' => ++$order,
                ));
                $group2[] = 'siteevent_state';
            }

            $rowCountry = $this->_searchForm->getFieldsOptions('siteevent', 'country');
            if (!empty($rowCountry) && !empty($rowCountry->display)) {
                $this->addElement('Text', 'siteevent_country', array(
                    'label' => 'Country',
                    'autocomplete' => 'off',
                    'order' => ++$order,
                ));
                $group2[] = 'siteevent_country';
            }
        }

        $row = $this->_searchForm->getFieldsOptions('siteevent', 'venue');
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.veneuname', 1) && !empty($row) && !empty($row->display)) {
            $this->addElement('Text', 'venue_name', array(
                'label' => 'Venue',
                'order' => ++$order,
            ));
            $group2[] = 'venue_name';
        }

        $row = $this->_searchForm->getFieldsOptions('siteevent', 'dates');
        if (!empty($row) && !empty($row->display)) {
            $start = new Engine_Form_Element_CalendarDateTime('starttime');
            $start->setLabel("From");
            $start->setOrder(++$order);
//            $currentTime = date('Y-m-d H:i:s');
//            $start->setValue($currentTime);
            $this->addElement($start);
            $group2[] = 'starttime';

            // End time
            $end = new Engine_Form_Element_CalendarDateTime('endtime');
            $end->setLabel("To");
            $end->setOrder(++$order);
            $this->addElement($end);
            $group2[] = 'endtime';
        }

        $row = $this->_searchForm->getFieldsOptions('siteevent', 'price');
        if (!empty($row) && !empty($row->display) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.price', 0)) {

            if ($this->_widgetSettings['priceFieldType'] != 'slider') {

                $this->addElement('Text', 'minPrice', array(
                    'label' => 'Price',
                    'placeholder' => 'min',
                    'order' => ++$order,
                ));
                $group2[] = 'minPrice';

                $this->addElement('Text', 'maxPrice', array(
                    'placeholder' => 'max',
                    'order' => ++$order,
                ));
                $group2[] = 'maxPrice';
            } else {
                $this->addElement('Text', 'priceSlider', array(
                    'decorators' => array(array('ViewScript', array(
                                'viewScript' => 'application/modules/Siteevent/views/scripts/_slider.tpl',
                                'minPrice' => $this->_widgetSettings['minPrice'],
                                'maxPrice' => $this->_widgetSettings['maxPrice'],
                                'class' => 'form element',
                                'locationPage' => 1,
                            ))),
                    'order' => ++$order,
                ));

                $group2[] = 'priceSlider';
                $this->addElement('Hidden', 'minPrice', array('order' => 8743));
                $this->addElement('Hidden', 'maxPrice', array('order' => 9777));
            }
        }

        if (!empty($group2)) {
            $this->addDisplayGroup($group2, 'grp2');
            $button_group = $this->getDisplayGroup('grp2');
            $button_group->setDecorators(array(
                'FormElements',
                'Fieldset',
                array('HtmlTag', array('tag' => 'li', 'id' => 'group2', 'style' => 'width:100%;'))
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

            $multiOptionsOrderBy = array_merge($multiOptionsOrderBy, array('distance' => 'Location: Near to Far'));
            
            if (Engine_Api::_()->siteevent()->isTicketBasedEvent()) {
                unset($multiOptionsOrderBy['member_count']);
            }            

            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.price', 0)) {
                $multiOptionsOrderBy = array_merge($multiOptionsOrderBy, array('priceLTH' => 'Price: Low to High', 'priceHTL' => 'Price: High to Low'));
            }

            $this->addElement('Select', 'orderby', array(
                'label' => 'Browse By',
                'multiOptions' => $multiOptionsOrderBy,
                'order' => ++$order,
                'value' => 'starttime'
            ));
        }

        $row = $this->_searchForm->getFieldsOptions('siteevent', 'show');
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        if (!empty($row) && !empty($row->display) && !empty($viewer_id)) {

            $show_multiOptions = array();
            $show_multiOptions["1"] = "Everyone's Events";
            $show_multiOptions["2"] = "Only My Friends' Events";
            $show_multiOptions["4"] = "Events I Like";
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
                'order' => ++$order,
                'value' => $value_deault,
            ));
        }

        $row = $this->_searchForm->getFieldsOptions('siteevent', 'view');
        if (!empty($row) && !empty($row->display)) {

            $this->addElement('Select', 'showEventType', array(
                'label' => 'View',
                'multiOptions' => array('upcoming' => 'Upcoming & Ongoing', 'onlyUpcoming' => 'Upcoming', 'onlyOngoing' => 'Ongoing', 'past' => 'Past'),
                'order' => $row->order,
                'value' => 'upcoming',
            ));
        } else {
            $this->addElement('hidden', 'showEventType', array(
                'value' => 'upcoming'
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
                'order' => ++$order,
                'value' => '',
            ));
        }

        $row = $this->_searchForm->getFieldsOptions('siteevent', 'has_free_price');
        if (!empty($row) && !empty($row->display) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.price', 0)) {
            $this->addElement('Checkbox', 'has_free_price', array(
                'label' => "Only Free Events",
                'order' =>++$order,
            ));
        }

        $row = $this->_searchForm->getFieldsOptions('siteevent', 'has_photo');
        if (!empty($row) && !empty($row->display)) {
            $this->addElement('Checkbox', 'has_photo', array(
                'label' => "Only Events With Photos",
                'order' => ++$order,
            ));
        }

        $row = $this->_searchForm->getFieldsOptions('siteevent', 'category_id');
        if (!empty($row) && !empty($row->display)) {
            if ($this->_widgetSettings['showAllCategories']) {
                $categories = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategories(array('category_id', 'category_name'), null, 0, 0, 1);
            } else {
                $categories = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategories(array('category_id', 'category_name'), null, 0, 0, 1, 0, 'cat_order', 1, 1);
            }

            if (count($categories) != 0) {
                $categories_prepared[0] = "";
                foreach ($categories as $category) {
                    $categories_prepared[$category->category_id] = $category->category_name;
                }

                $this->addElement('Select', 'category_id', array(
                    'label' => 'Category',
                    'multiOptions' => $categories_prepared,
                    'order' => ++$order,
                    'onchange' => "showFields(this.value, 1); addOptions(this.value, 'cat_dependency', 'subcategory_id', 0);",
                ));

                $this->addElement('Select', 'subcategory_id', array(
                    'RegisterInArrayValidator' => false,
                    'order' => ++$order,
                    'decorators' => array(array('ViewScript', array(
                                'viewScript' => 'application/modules/Siteevent/views/scripts/_subCategory.tpl',
                                'class' => 'form element')))
                ));
            }
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

        $this->addElement('Hidden', 'Longitude', array(
            'order' => $i++,
        ));   

        $this->addDisplayGroup(array('profile_type', 'orderby', 'show', 'showEventType', 'has_photo', 'closed', 'has_review', 'category_id', 'subcategory_id', 'subsubcategory_id', 'has_free_price'), 'grp1');
        $button_group = $this->getDisplayGroup('grp1');
        $button_group->setDecorators(array(
            'FormElements',
            'Fieldset',
            array('HtmlTag', array('tag' => 'li', 'id' => 'group1', 'style' => 'width:100%;'))
        ));

        return $this;
    }

}