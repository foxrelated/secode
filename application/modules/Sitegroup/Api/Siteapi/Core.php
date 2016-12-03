<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Core.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Api_Siteapi_Core extends Core_Api_Abstract {

    /**
     * Get the "Directory Pages Search" form.
     * 
     * @return array
     */
    public function getBrowseSearchForm() {

        $searchForm = array();
        $viewer = Engine_Api::_()->user()->getViewer();
        $searchFormSettings = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getModuleOptions('sitegroup');
        $sitegroupofferEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupoffer');
        $sitegroupreviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview');

        if (isset($searchFormSettings['show']) && !empty($searchFormSettings['show']) && isset($searchFormSettings['show']['display']) && !empty($searchFormSettings['show']['display'])) {
            $searchForm[] = array(
                'type' => 'Select',
                'name' => 'show',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Show'),
                'multiOptions' => array(
                    '1' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Everyone\'s Groups'),
                    '2' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Only My Friends\' Groups'),
                    '4' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Groups I Like'),
                    '5' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Featured Groups')
                )
            );
        }

        if (isset($searchFormSettings['orderby']) && !empty($searchFormSettings['orderby']) && !empty($searchFormSettings['orderby']['display']) && isset($sitegroupreviewEnabled) && !empty($sitegroupreviewEnabled)) {
            $searchForm[] = array(
                'type' => 'Select',
                'name' => 'orderby',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Browse By'),
                'multiOptions' => array(
                    '' => '',
                    'creation_date' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Recent'),
                    'view_count' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Viewed'),
                    'comment_count' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Commented'),
                    'like_count' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Liked'),
                    'title' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Alphabetical'),
                    'review_count' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Reviewed'),
                    'rating' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Rated')
                ),
            );
        } elseif (isset($searchFormSettings['orderby']) && !empty($searchFormSettings['orderby']) && isset($searchFormSettings['orderby']['display']) && !empty($searchFormSettings['orderby']['display'])) {
            $searchForm[] = array(
                'type' => 'Select',
                'name' => 'orderby',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Browse By'),
                'multiOptions' => array(
                    '' => '',
                    'creation_date' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Recent'),
                    'view_count' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Viewed'),
                    'comment_count' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Commented'),
                    'like_count' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Liked'),
                    'title' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Alphabetical'),
                ),
            );
        }

        if (!empty($searchFormSettings['search']) && !empty($searchFormSettings['search']['display'])) {
            $searchForm[] = array(
                'type' => 'Text',
                'name' => 'search',
                'label' => $this->translate('Search Groups')
            );
        }

        $searchForm[] = array(
            'type' => 'Text',
            'name' => 'sitegroup_location',
            'label' => $this->translate(' Location ')
        );

        if (!empty($searchFormSettings['location']) && !empty($searchFormSettings['location']['display'])) {
            $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.locationfield', 1);
            if (!empty($enableLocation)) {
                if (!empty($searchFormSettings['locationmiles']) && !empty($searchFormSettings['locationmiles']['display'])) {
                    $enableProximitysearch = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.proximitysearch', 1);
                    if (!empty($enableProximitysearch)) {
                        $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.proximity.search.kilometer', 0);
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

                        $searchForm[] = array(
                            'type' => 'Text',
                            'name' => 'locationmiles',
                            'label' => $locationLable,
                            'multiOptions' => $locationOption,
                        );
                    }
                }


                if (!empty($searchFormSettings['street']) && !empty($searchFormSettings['street']['display'])) {
                    $searchForm[] = array(
                        'type' => 'Text',
                        'name' => 'sitegroup_street',
                        'label' => $this->translate('Street')
                    );
                }

                if (!empty($this->_searchFormSettings['city']) && !empty($this->_searchFormSettings['city']['display'])) {
                    $searchForm[] = array(
                        'type' => 'Text',
                        'name' => 'sitegroup_city',
                        'label' => $this->translate('City')
                    );
                }

                if (!empty($this->_searchFormSettings['state']) && !empty($this->_searchFormSettings['state']['display'])) {
                    $searchForm[] = array(
                        'type' => 'Text',
                        'name' => 'sitegroup_state',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('State')
                    );
                }

                if (!empty($this->_searchFormSettings['country']) && !empty($this->_searchFormSettings['country']['display'])) {
                    $searchForm[] = array(
                        'type' => 'Text',
                        'name' => 'sitegroup_country',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Country')
                    );
                }
            }
        }


        if (!empty($searchFormSettings['category_id']) && !empty($searchFormSettings['category_id']['display'])) {
            $categories = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategories(array('category_id', 'category_name'), null, 0, 0, 1);
            $getCategoryArray = array();
            $getCategoryArray[0] = '';
            if (isset($categories) && !empty($categories)) {
                foreach ($categories as $category)
                    $getCategoryArray[$category->category_id] = $category->category_name;
            }

            if (count($getCategoryArray) > 1) {
                $searchForm[] = array(
                    'type' => 'Select',
                    'name' => 'category_id',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Category'),
                    'multiOptions' => $getCategoryArray
                );
            }
        }


        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupbadge') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupbadge.seaching.bybadge', 1) && !empty($searchFormSettings['badge_id']) && !empty($searchFormSettings['badge_id']['display'])) {

            $params = array();
            $params['search_code'] = 1;
            $badgeData = Engine_Api::_()->getDbTable('badges', 'sitegroupbadge')->getBadgesData($params);
            if (!empty($badgeData)) {
                $badgeData = $badgeData->toArray();
                $badgeCount = Count($badgeData);

                if (!empty($badgeCount)) {
                    $badge_options = array();
                    $badge_options[0] = '';
                    foreach ($badgeData as $name) {
                        $badge_options[$name['badge_id']] = $name['title'];
                    }
                    $searchForm[] = array(
                        'type' => 'Select',
                        'name' => 'badge_id',
                        'label' => $this->translate('Badge'),
                        'multiOptions' => $badge_options,
                    );
                }
            }
        }

        if (isset($sitegroupofferEnabled) && !empty($sitegroupofferEnabled) && isset($searchFormSettings['offer_type']) && !empty($searchFormSettings['offer_type']) && isset($searchFormSettings['offer_type']['display']) && !empty($searchFormSettings['offer_type']['display'])) {
            $searchForm[] = array(
                'type' => 'Select',
                'name' => 'offer_type',
                'label' => $this->translate('Groups With Offers'),
                'multiOptions' => array(
                    '' => '',
                    'all' => 'All Offers',
                    'hot' => 'Hot Offers',
                    'featured' => 'Featured Offers',
                )
            );
        }



        if (isset($searchFormSettings['closed']) && !empty($searchFormSettings['closed']) && isset($searchFormSettings['closed']['display']) && !empty($searchFormSettings['closed']['display'])) {
            $searchForm[] = array(
                'type' => 'Select',
                'name' => 'closed',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Status'),
                'multiOptions' => array(
                    '' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Groups'),
                    '0' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Only Open Groups'),
                    '1' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Only Closed Groups')
                )
            );
        }

        if (!empty($searchFormSettings['price']) && !empty($searchFormSettings['price']['display'])) {
            $enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.price.field', 1);
            if (!empty($enablePrice)) {
                $searchForm[] = array(
                    'type' => 'Text',
                    'name' => 'min',
                    'label' => $this->translate('Min Price')
                );
                $searchForm[] = array(
                    'type' => 'Text',
                    'name' => 'max',
                    'label' => $this->translate('Max Price')
                );
            }
        }

        if (!empty($searchFormSettings['has_photo']) && !empty($searchFormSettings['has_photo']['display'])) {
            $searchForm[] = array(
                'type' => 'Checkbox',
                'name' => 'has_photo',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Only Groups With Photos'),
            );
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview') && !empty($searchFormSettings['has_review']) && !empty($searchFormSettings['has_review']['display'])) {
            $searchForm[] = array(
                'type' => 'Checkbox',
                'name' => 'has_review',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Only Groups With Reviews'),
            );
        }

        $searchForm[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Search')
        );

        $searchFormData = array();
        $searchFormData['form'] = $searchForm;
        return $searchFormData;
    }

    /**
     * Get the "Page Create" form.
     * 
     * @param object $subject get subject only in case of edit.
     * @return array
     */
    public function getForm($sitegroup = null, $package_id = 0) {

        $user = $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $user->getIdentity();
        $createForm = array();
        $user_level = Engine_Api::_()->user()->getViewer()->level_id;
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $createFormFields = array(
            'location',
            'tags',
            'photo',
            'description',
            'overview',
            'price',
            'viewPrivacy',
            'commentPrivacy',
            'showHideAdvancedOptions' => 'Advanced Show / Hide Options',
            'allPostPrivacy'
        );

        $sitegroupMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');

        $show_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.showurl.column', 1);
        $change_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.change.url', 1);
        $edit_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.edit.url', 0);

        $groupadminsetting = $settings->getSetting('sitegroup.manageadmin', 1);
        if (!empty($groupadminsetting)) {
            $ownerTitle = "Group Admins";
        } else {
            $ownerTitle = "Just Me";
        }

        $profileFields = $this->getProfileTypes();
        if (!empty($profileFields)) {
            $this->_profileFieldsArray = $profileFields;
        }

        $this->_create = 1;
        $profileFieldsArray = $this->_getProfileFields();

        try {

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion')) {
                $createFormFields = array_merge($createFormFields, array('discussionPrivacy'));
            }

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum')) {
                $createFormFields = array_merge($createFormFields, array('photoPrivacy'));
            }

            if ((Engine_Api::_()->hasModuleBootstrap('sitevideo') && Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitegroup_group', 'item_module' => 'sitegroup')))) {
                $createFormFields = array_merge($createFormFields, array('videoPrivacy'));
            } elseif (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo')) {
                $createFormFields = array_merge($createFormFields, array('videoPrivacy'));
            }

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent')) {
                $createFormFields = array_merge($createFormFields, array('EventPrivacy'));
            }

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument')) {
                $createFormFields = array_merge($createFormFields, array('documentPrivacy'));
            }

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouppoll')) {
                $createFormFields = array_merge($createFormFields, array('pollPrivacy'));
            }

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupnote')) {
                $createFormFields = array_merge($createFormFields, array('notePrivacy'));
            }

            // if ((Engine_Api::_()->hasModuleBootstrap('sitegroup') && Engine_Api::_()->getDbtable('modules', 'sitegroup')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitegroup_group', 'item_module' => 'sitegroup')))) {
            //     $createFormFields = array_merge($createFormFields, array('groupPrivacy'));
            // } elseif (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroup')) {
            //     $createFormFields = array_merge($createFormFields, array('groupPrivacy'));
            // }

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic')) {
                $createFormFields = array_merge($createFormFields, array('musicPrivacy'));
            }

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
                $createFormFields = array_merge($createFormFields, array('memberTitle'));
                $createFormFields = array_merge($createFormFields, array('memberInvite'));
                $createFormFields = array_merge($createFormFields, array('memberApproval'));
            }

            $createFormFields = array_merge($createFormFields, array(
                'subPagePrivacy',
                'claimThisPage',
                'status',
                'search'
            ));

            if (Engine_Api::_()->getApi('settings', 'core')->hasSetting('sitegroup.createFormFields')) {
                $createFormFields = $settings->getSetting('sitegroup.createFormFields', $createFormFields);
            }

            // Page title
            $createForm[] = array(
                'type' => 'Text',
                'name' => 'title',
                'label' => $this->translate('Title'),
                'hasValidator' => true
            );

            // Page Url
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupurl') && !$sitegroup && !empty($show_url)) {
                $createForm[] = array(
                    'type' => 'Text',
                    'name' => 'group_url',
                    'label' => $this->translate('URL'),
                    'hasValidator' => true
                );
            }

            // Tags
            if (!empty($createFormFields) && in_array('tags', $createFormFields) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.tags', 1)) {
                $createForm[] = array(
                    'type' => 'Text',
                    'name' => 'tags',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Tags (Keywords)'),
                    'description' => Zend_Registry::get('Zend_Translate')->_('Separate tags with commas.'),
                );
            }

            $categories = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategories(array('category_id', 'category_name'), null, 0, 0, 1);

            if (count($categories) != 0) {
                $getCategories = array();
                $getCategories[0] = "";
                $subCategories = array();
                $subcategoryData = array();
                $subsubCategories = array();
                $profileTypeArray = array();
                foreach ($categories as $category) {
                    $subCategoriesObj = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getSubCategories($category->category_id);
                    if ($subCategoriesObj->count() > 0) {

                        unset($subCategories);
                        $subCategories[$category->category_id]['form'] = array(
                            "type" => "Select",
                            "name" => "subcategory_id",
                            "label" => "Sub-Category",
                            'multiOptions' => array(),
                        );

                        $subCategories[$category->category_id]['form']['multiOptions'][0] = '';
                        foreach ($subCategoriesObj as $subcategory) {
                            $subCategories[$category->category_id]['form']['multiOptions'][$subcategory->category_id] = $subcategory->category_name;
                            $subsubCategoriesObj = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getSubCategories($subcategory->category_id);
                            if ($subsubCategoriesObj->count() > 0) {
                                unset($subsubCategories);
                                $subsubCategories[$subcategory->category_id]['form'] = array(
                                    "type" => "Select",
                                    "name" => "subsubcategory_id",
                                    "label" => "3rd Level Category",
                                    "multiOptions" => array(),
                                );
                                $subsubCategories[$subcategory->category_id]['form']['multiOptions'][0] = '';
                                foreach ($subsubCategoriesObj as $subsubcategory) {
                                    $subsubCategories[$subcategory->category_id]['form']['multiOptions'][$subsubcategory->category_id] = $subsubcategory->category_name;
                                }
                                $subCategories[$category->category_id]['subsubCategories'][] = $subsubCategories;
                            }
                        }
                        $subcategoryData[$category->category_id] = $subCategories[$category->category_id];
                        $profileCategoryMapArray[$category->category_id] = Engine_Api::_()->getDbTable('profilemaps', 'sitegroup')->getProfileType($category->category_id);
                    }
                    $getCategories[$category->category_id] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($category->category_name);
                }
            }

            $createForm[] = array(
                'type' => 'Select',
                'name' => 'category_id',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Category'),
                'multiOptions' => $getCategories,
                'hasValidator' => 'true'
            );

            // Add categories & subcategories in case of edit group form
            if (isset($sitegroup->category_id) && !empty($sitegroup->category_id)) {
                $createForm[] = $subcategoryData[$sitegroup->category_id]['form'];
            }

            if (isset($sitegroup->subcategory_id) && !empty($sitegroup->subcategory_id)) {
                $subsubCategoriesObj = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getSubCategories($sitegroup->subcategory_id);
                foreach ($subsubCategoriesObj as $subsubcategory) {
                    $multiOptions = $subsubcategory->category_name;
                }
                if ($subsubCategoriesObj->count() > 0) {
                    unset($subsubCategories);
                    $createForm[] = array(
                        "type" => "Select",
                        "name" => "subsubcategory_id",
                        "label" => "3rd Level Category",
                        "multiOptions" => $multiOptions,
                    );
                }
            }

            if (isset($profileFieldsArray) && !empty($profileFieldsArray)) {
                foreach ($profileCategoryMapArray as $key => $value) {
                    if (isset($profileFieldsArray[$value]) && !empty($profileFieldsArray[$value]))
                        $profileFieldsForm[$key] = $profileFieldsArray[$value];
                }
            }

            // Add profile fields according to categories selected in case of edit group form
            if (isset($profileFieldsArray) && !empty($profileFieldsArray) && isset($sitegroup->profile_type) && !empty($sitegroup->profile_type) && isset($profileFieldsForm[$sitegroup->category_id]) && !empty($profileFieldsForm[$sitegroup->category_id])) {
                $createForm = array_merge($createForm, $profileFieldsForm[$sitegroup->category_id]);
            }

            // Package based checks
            if ($hasPackageEnable) {
                if (Engine_Api::_()->sitegrouppaid()->allowPackageContent($package_id, "overview")) {
                    $allowOverview = 1;
                } else {
                    $allowOverview = 0;
                }
            } else {
                // AUthorization checks
                $allowOverview = Engine_Api::_()->authorization()->getPermission($user->level_id, 'sitegroup_group', "overview");
            }

            // Package based checks
            $allowEdit = Engine_Api::_()->authorization()->getPermission($user->level_id, 'sitegroup_group', "edit");

            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.overview', 1) && isset($createFormFields) && (!empty($createFormFields) && in_array('overview', $createFormFields)) && $allowOverview && $allowEdit) {
                $description = 'Short Description';
            } else {
                $description = 'Description';
            }

            if ($settings->getSetting('sitegroup.description.allow', 1)) {
                if ($settings->getSetting('sitegroup.requried.description', 1)) {
                    $createForm[] = array(
                        'type' => 'Textarea',
                        'name' => 'body',
                        'label' => $description,
                        'hasValidators' => 'true'
                    );
                } else {
                    $createForm[] = array(
                        'type' => 'Textarea',
                        'name' => 'body',
                        'label' => $description,
                    );
                }
            }

            // Photo
            if (in_array('photo', $createFormFields)) {
                $createForm[] = array(
                    'type' => 'File',
                    'name' => 'photo',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Add Photo')
                );
            }

            if (!empty($createFormFields) && in_array('price', $createFormFields) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.price', 0)) {
                //   $localeObject = Zend_Registry::get('Locale');
                $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
                $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);
                $createForm[] = array(
                    'type' => 'Text',
                    'name' => 'price',
                    'label' => sprintf(Zend_Registry::get('Zend_Translate')->_('Price (%s)'), $currencyName),
                );
            }


            // Location
            if (isset($createFormFields) && !empty($createFormFields) && in_array('location', $createFormFields) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.location', 1)) {

                $locationDefault = $settings->getSetting('seaocore.locationdefault', '');
                $seaocore_locationspecific = $settings->getSetting('seaocore.locationspecific', '');
                $seaocore_locationspecificcontent = $settings->getSetting('seaocore.locationspecificcontent', '');

                if ($seaocore_locationspecific && $seaocore_locationspecificcontent) {
                    $locations = Engine_Api::_()->getDbTable('locationcontents', 'seaocore')->getLocations(array('status' => 1));
                    $locationsArray = array();
                    $locationsArray[] = '';
                    foreach ($locations as $location) {
                        $locationsArray[$location->location] = $location->title;
                    }
                    if ($locations) {
                        $createForm[] = array(
                            'type' => 'Select',
                            'name' => 'location',
                            'description' => $this->translate('Eg: Fairview Park, Berkeley, CA'),
                            'label' => $this->translate('Enter a location'),
                            'multiOptions' => $locationsArray
                        );
                    }
                } else {
                    $createForm[] = array(
                        'type' => 'Text',
                        'name' => 'location',
                        'description' => $this->translate('Eg: Fairview Park, Berkeley, CA'),
                        'label' => $this->translate('Enter a location'),
                    );
                }
            }

            //START SITEGROUPMEMBER PLUGIN WORK
            $allowMemberInLevel = Engine_Api::_()->authorization()->getPermission($user_level, 'sitegroup_group', 'smecreate');
            $allowMemberInthisPackage = false;
            $allowMemberInthisPackage = Engine_Api::_()->sitegroup()->allowPackageContent($package_id, "modules", "sitegroupmember");
            if (!empty($createFormFields) && (in_array('memberTitle', $createFormFields) || in_array('memberInvite', $createFormFields) || in_array('memberApproval', $createFormFields))) {
                $member_approval = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.member.approval.option', 1);
                if ($sitegroupMemberEnabled && in_array('memberApproval', $createFormFields) && !empty($member_approval)) {
                    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                        if ($allowMemberInthisPackage) {
                            $createForm[] = array(
                                'type' => 'Radio',
                                'name' => 'member_approval',
                                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Approve members?'),
                                'description' => 'When people try to join this group, should they be allowed ' .
                                'to join immediately, or should they be forced to wait for approval?',
                                'multiOptions' => array(
                                    '1' => 'New members can join immediately.',
                                    '0' => 'New members must be approved.',
                                ),
                                'value' => '1',
                            );
                        }
                    } else if (!empty($allowMemberInLevel)) {
                        $createForm[] = array(
                            'type' => 'Radio',
                            'name' => 'member_approval',
                            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Approve members?'),
                            'description' => 'When people try to join this group, should they be allowed ' .
                            'to join immediately, or should they be forced to wait for approval?',
                            'multiOptions' => array(
                                '1' => 'New members can join immediately.',
                                '0' => 'New members must be approved.',
                            ),
                            'value' => '1',
                        );
                    }
                }
            }

            // View Privacy work start
            $availableLabels = array(
                'everyone' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Everyone'),
                'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Registered Members'),
                'owner_network' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends and Networks'),
                'owner_member_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends of Friends'),
                'owner_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends Only'),
                'member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('SitePage Guests Only'),
                'leader' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Owner and Leaders Only')
            );

            $view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitegroup_group', $user, "auth_view");
            $view_options = array_intersect_key($availableLabels, array_flip($view_options));
            if (!empty($createFormFields) && in_array('viewPrivacy', $createFormFields) && count($view_options) > 1) {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'auth_view',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('View Privacy'),
                    'description' => Zend_Registry::get('Zend_Translate')->_("Who may see this Page?"),
                    'multiOptions' => $view_options,
                );
            }
            // View Privacy work end
            //START SITEGROUPEVENT PLUGIN WORK
            if (!empty($createFormFields) && in_array('EventPrivacy', $createFormFields)) {
                if ((Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent')) || ((Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitegroup_group', 'item_module' => 'sitegroup'))))) {
                    $availableLabels = array(
                        'registered' => 'All Registered Members',
                        'owner_network' => 'Friends and Networks',
                        'owner_member_member' => 'Friends of Friends',
                        'owner_member' => 'Friends Only',
                        'like_member' => 'Who Liked This Group',
                    );
                    if (!empty($sitegroupMemberEnabled) && $allowMemberInthisPackage) {
                        $availableLabels['member'] = 'Group Members Only';
                    } elseif (!empty($sitegroupMemberEnabled) && $allowMemberInLevel) {
                        $availableLabels['member'] = 'Group Members Only';
                    }
                    $availableLabels['owner'] = $ownerTitle;

                    $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitegroup_group', $user, 'auth_secreate');
                    $options_create = array_intersect_key($availableLabels, array_flip($options));

                    if (!empty($options_create)) {
                        $can_show_list = true;
                        if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                            if (!Engine_Api::_()->sitegroup()->allowPackageContent($package_id, "modules", "sitegroupevent")) {
                                $can_show_list = false;
                            }
                        }
                        if ($can_show_list) {
                            if (count($options_create) > 1) {
                                $createForm[] = array(
                                    'type' => 'Select',
                                    'name' => 'secreate',
                                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Event Creation Privacy'),
                                    'description' => Zend_Registry::get('Zend_Translate')->_("Who may create Event for this group?"),
                                    'multiOptions' => $options_create,
                                    'value' => @array_search(@end($options_create), $options_create)
                                );
                            }
                        }
                    }
                }
            }
            //END SITEGROUPEVENT PLUGIN WORK 


            if (!empty($createFormFields) && in_array('allPostPrivacy', $createFormFields)) {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'all_post',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Event Creation Privacy'),
                    'description' => Zend_Registry::get('Zend_Translate')->_("Post in Updates Tab"),
                    'multiOptions' => array("1" => "All Registered Members", "0" => "Group Admins"),
                    'value' => '1'
                );
            }

            //START SITEGROUPMEMBER PLUGIN WORK
            $allowMemberInLevel = Engine_Api::_()->authorization()->getPermission($user_level, 'sitegroup_group', 'smecreate');

            $allowMemberInthisPackage = false;

            $allowMemberInthisPackage = Engine_Api::_()->sitegroup()->allowPackageContent($package_id, "modules", "sitegroupmember");
            if (!empty($createFormFields) && (in_array('memberTitle', $createFormFields) || in_array('memberInvite', $createFormFields) || in_array('memberApproval', $createFormFields))) {
                if ($sitegroupMemberEnabled) {
                    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                        if ($allowMemberInthisPackage) {

                            $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.member.title', 1);
                            if (in_array('memberTitle', $createFormFields) && !empty($memberTitle)) {
                                $createForm[] = array(
                                    'type' => 'Text',
                                    'name' => 'member_title',
                                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('What will members be called?'),
                                    'description' => 'Ex: Dance Lovers, Hikers, Innovators, Music Lovers, etc.',
                                );
                            }

                            $memberInvite = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.invite.option', 1);
                            if (in_array('memberInvite', $createFormFields) && !empty($memberInvite)) {
                                $createForm[] = array(
                                    'type' => 'Radio',
                                    'name' => 'member_invite',
                                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Invite member'),
                                    'multiOptions' => array(
                                        '0' => 'Yes, members can invite other people.',
                                        '1' => 'No, only group admins can invite other people.',
                                    ),
                                    'value' => '1',
                                );
                            }
                        }
                    } else if (!empty($allowMemberInLevel)) {

                        $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.member.title', 1);
                        if (in_array('memberTitle', $createFormFields) && !empty($memberTitle)) {
                            $createForm[] = array(
                                'type' => 'Text',
                                'name' => 'member_title',
                                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('What will members be called?'),
                                'description' => 'Ex: Dance Lovers, Hikers, Innovators, Music Lovers, etc.',
                            );
                        }

                        if (in_array('memberInvite', $createFormFields)) {
                            $createForm[] = array(
                                'type' => 'Radio',
                                'name' => 'member_invite',
                                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Invite member'),
                                'multiOptions' => array(
                                    '0' => 'Yes, members can invite other people.',
                                    '1' => 'No, only group admins can invite other people.',
                                ),
                                'value' => '1',
                            );
                        }
                    }
                }
            }

            //NETWORK BASE GROUP VIEW PRIVACY
            if (Engine_Api::_()->getApi('subCore', 'sitegroup')->groupBaseNetworkEnable()) {
                // Make Network List
                $table = Engine_Api::_()->getDbtable('networks', 'network');
                $select = $table->select()
                        ->from($table->info('name'), array('network_id', 'title'))
                        ->order('title');
                $result = $table->fetchAll($select);

                $networksOptions = array('0' => 'Everyone');
                foreach ($result as $value) {
                    $networksOptions[$value->network_id] = $value->title;
                }
                if (count($networksOptions) > 0) {
                    $viewPricavyEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.networkprofile.privacy', 0);
                    if ($viewPricavyEnable) {
                        $desc = 'Select the networks, members of which should be able to see your group. (Press Ctrl and click to select multiple networks. Applied privacy will be a combination of the privacy chosen above in "View Privacy" and the privacy chosen here.)';
                    } else {
                        $desc = 'Select the networks, members of which should be able to see your Group in browse and search groups. (Press Ctrl and click to select multiple networks. Applied privacy will be a combination of the privacy chosen above in "View Privacy" and the privacy chosen here.)';
                    }
                    $createForm[] = array(
                        'type' => 'Multiselect',
                        'name' => 'networks_privacy',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Networks Selection'),
                        'description' => $desc,
                        'multiOptions' => $networksOptions,
                        'value' => array(0)
                    );
                }
            }

            $comment_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitegroup_group', $user, "auth_comment");
            $comment_options = array_intersect_key($availableLabels, array_flip($comment_options));
            if (!empty($createFormFields) && in_array('commentPrivacy', $createFormFields) && count($comment_options) > 1) {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'auth_comment',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Comment Privacy'),
                    'description' => Zend_Registry::get('Zend_Translate')->_("Who may comment on this Page?"),
                    'multiOptions' => $comment_options,
                    'value' => key($comment_options),
                );
            }

            $availableLabels = array(
                'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Registered Members'),
                'owner_network' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends and Networks'),
                'owner_member_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends of Friends'),
                'owner_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends Only'),
                'member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Page Guests Only'),
                'leader' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Owner and Leaders Only')
            );

            if (!empty($explodeParentType) && isset($explodeParentType[0]) && isset($explodeParentType[1])) {
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($explodeParentType[0] . 'member') && ($parent_type == 'sitegroup_group' || $this->_parent_type == 'sitebusiness_business' || $parent_type == 'sitegroup_group') && (Engine_Api::_()->getDbtable('modules', 'sitegroup')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parent_type, 'item_module' => $explodeParentType[0])))) {
                    $shortTypeName = ucfirst($explodeParentType[1]);
                    $availableLabels = array(
                        'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Registered Members'),
                        'parent_member' => $shortTypeName . ' Members Only',
                        'like_member' => 'Who liked this ' . $shortTypeName,
                        'member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Page Guests Only'),
                        'leader' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Just Me')
                    );
                } elseif (($parent_type == 'sitegroup_group' || $parent_type == 'sitebusiness_business' || $parent_type == 'sitegroup_group' || $parent_type == 'sitestore_store') && (Engine_Api::_()->getDbtable('modules', 'sitegroup')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parent_type, 'item_module' => $explodeParentType[0])))) {
                    $shortTypeName = ucfirst($explodeParentType[1]);
                    $availableLabels = array(
                        'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Registered Members'),
                        'like_member' => 'Who liked this ' . $shortTypeName,
                        'member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Page Guests Only'),
                        'leader' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Just Me')
                    );
                } elseif (($parent_type == 'sitereview_listing') && (Engine_Api::_()->getDbtable('modules', 'sitegroup')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitereview_listing_' . $parentItem->listingtype_id, 'item_module' => 'sitereview')))) {
                    $availableLabels = array(
                        'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Registered Members'),
                        'member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Page Guests Only'),
                        'leader' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Just Me')
                    );
                }
            }

            //START DISCUSSION PRIVACY WORK
            if (!empty($createFormFields) && in_array('discussionPrivacy', $createFormFields)) {
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion')) {
                    $availableLabels = array(
                        'registered' => 'All Registered Members',
                        'owner_network' => 'Friends and Networks',
                        'owner_member_member' => 'Friends of Friends',
                        'owner_member' => 'Friends Only',
                        'like_member' => 'Who Liked This Group',
                    );
                    if (!empty($sitegroupMemberEnabled) && $allowMemberInthisPackage) {
                        $availableLabels['member'] = 'Group Members Only';
                    } elseif (!empty($sitegroupMemberEnabled) && $allowMemberInLevel) {
                        $availableLabels['member'] = 'Group Members Only';
                    }
                    $availableLabels['owner'] = $ownerTitle;

                    $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitegroup_group', $user, 'auth_sdicreate');
                    $options_create = array_intersect_key($availableLabels, array_flip($options));

                    if (!empty($options_create)) {
                        $can_show_list = true;
                        if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                            if (!Engine_Api::_()->sitegroup()->allowPackageContent($package_id, "modules", "sitegroupdiscussion")) {
                                $can_show_list = false;
                            }
                        } else {
                            $can_create = Engine_Api::_()->authorization()->getPermission($user_level, 'sitegroup_group', 'sdicreate');
                            if (!$can_create) {
                                $can_show_list = false;
                            }
                        }
                        if ($can_show_list) {
                            if (count($options_create) > 1) {
                                $createForm[] = array(
                                    'type' => 'Select',
                                    'name' => 'sdicreate',
                                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Discussion Topic Privacy'),
                                    'description' => 'Who may post discussion topics for this group?',
                                    'multiOptions' => $options_create,
                                    'value' => key($options_create),
                                );
                            }
                        }
                    }
                }
            }
            //END DISCUSSION PRIVACY WORK 
            //START PHOTO PRIVACY WORK
            if (!empty($createFormFields) && in_array('photoPrivacy', $createFormFields)) {
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum')) {
                    $availableLabels = array(
                        'registered' => 'All Registered Members',
                        'owner_network' => 'Friends and Networks',
                        'owner_member_member' => 'Friends of Friends',
                        'owner_member' => 'Friends Only',
                        'like_member' => 'Who Liked This Group',
                    );
                    if (!empty($sitegroupMemberEnabled) && $allowMemberInthisPackage) {
                        $availableLabels['member'] = 'Group Members Only';
                    } elseif (!empty($sitegroupMemberEnabled) && $allowMemberInLevel) {
                        $availableLabels['member'] = 'Group Members Only';
                    }
                    $availableLabels['owner'] = $ownerTitle;

                    $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitegroup_group', $user, 'auth_spcreate');
                    $options_create = array_intersect_key($availableLabels, array_flip($options));

                    if (!empty($options_create)) {
                        $can_show_list = true;
                        if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                            if (!Engine_Api::_()->sitegroup()->allowPackageContent($package_id, "modules", "sitegroupalbum")) {
                                $can_show_list = false;
                            }
                        } else {
                            $can_create = Engine_Api::_()->authorization()->getPermission($user_level, 'sitegroup_group', 'spcreate');
                            if (!$can_create) {
                                $can_show_list = false;
                            }
                        }

                        if ($can_show_list) {
                            if (count($options_create) > 1) {
                                $createForm[] = array(
                                    'type' => 'Select',
                                    'name' => 'spcreate',
                                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Photo Creation Privacy'),
                                    'description' => 'Who may upload photos for this group?',
                                    'multiOptions' => $options_create,
                                    'value' => @array_search(@end($options_create), $options_create),
                                );
                            }
                        }
                    }
                }
            }
            //END PHOTO PRIVACY WORK
            //START SITEGROUPDOCUMENT PLUGIN WORK
            if (!empty($createFormFields) && in_array('documentPrivacy', $createFormFields)) {
                $sitegroupDocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument');
                if ($sitegroupDocumentEnabled) {
                    $availableLabels = array(
                        'registered' => 'All Registered Members',
                        'owner_network' => 'Friends and Networks',
                        'owner_member_member' => 'Friends of Friends',
                        'owner_member' => 'Friends Only',
                        'like_member' => 'Who Liked This Group',
                    );
                    if (!empty($sitegroupMemberEnabled) && $allowMemberInthisPackage) {
                        $availableLabels['member'] = 'Group Members Only';
                    } elseif (!empty($sitegroupMemberEnabled) && $allowMemberInLevel) {
                        $availableLabels['member'] = 'Group Members Only';
                    }
                    $availableLabels['owner'] = $ownerTitle;

                    $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitegroup_group', $user, 'auth_sdcreate');
                    $options_create = array_intersect_key($availableLabels, array_flip($options));

                    if (!empty($options_create)) {
                        $can_show_list = true;
                        if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                            if (!Engine_Api::_()->sitegroup()->allowPackageContent($package_id, "modules", "sitegroupdocument")) {
                                $can_show_list = false;
                            }
                        } else {
                            $can_create = Engine_Api::_()->authorization()->getPermission($user_level, 'sitegroup_group', 'sdcreate');
                            if (!$can_create) {
                                $can_show_list = false;
                            }
                        }
                        if ($can_show_list) {
                            if (count($options_create) > 1) {
                                $createForm[] = array(
                                    'type' => 'Select',
                                    'name' => 'sdcreate',
                                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Documents Creation Privacy'),
                                    'description' => 'Who may create documents for this group?',
                                    'multiOptions' => $options_create,
                                    'value' => @array_search(@end($options_create), $options_create),
                                );
                            }
                        }
                    }
                }
            }
            //END SITEGROUPDOCUMENT PLUGIN WORK

            if (!empty($createFormFields) && in_array('videoPrivacy', $createFormFields)) {
                $sitegroupVideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo');
                if (($sitegroupVideoEnabled) || ((Engine_Api::_()->hasModuleBootstrap('sitevideo') && Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitegroup_group', 'item_module' => 'sitegroup'))))) {
                    $availableLabels = array(
                        'registered' => 'All Registered Members',
                        'owner_network' => 'Friends and Networks',
                        'owner_member_member' => 'Friends of Friends',
                        'owner_member' => 'Friends Only',
                        'like_member' => 'Who Liked This Group',
                    );
                    if (!empty($sitegroupMemberEnabled) && $allowMemberInthisPackage) {
                        $availableLabels['member'] = 'Group Members Only';
                    } elseif (!empty($sitegroupMemberEnabled) && $allowMemberInLevel) {
                        $availableLabels['member'] = 'Group Members Only';
                    }
                    $availableLabels['owner'] = $ownerTitle;

                    $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitegroup_group', $user, 'auth_svcreate');
                    $options_create = array_intersect_key($availableLabels, array_flip($options));

                    if (!empty($options_create)) {
                        $can_show_list = true;
                        if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                            if (!Engine_Api::_()->sitegroup()->allowPackageContent($package_id, "modules", "sitegroupvideo")) {
                                $can_show_list = false;
                            }
                        } else {
                            $can_create = Engine_Api::_()->authorization()->getPermission($user_level, 'sitegroup_group', 'svcreate');
                            if (!$can_create) {
                                $can_show_list = false;
                            }
                        }
                        if ($can_show_list) {
                            if (count($options_create) > 1) {
                                $createForm[] = array(
                                    'type' => 'Select',
                                    'name' => 'svcreate',
                                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Videos Creation Privacy'),
                                    'description' => 'Who may create videos for this group?',
                                    'multiOptions' => $options_create,
                                    'value' => @array_search(@end($options_create), $options_create),
                                );
                            }
                        }
                    }
                }
            }
            //END SITEGROUPVIDEO PLUGIN WORK
            //START SITEGROUPPOLL PLUGIN WORK
            if (!empty($createFormFields) && in_array('pollPrivacy', $createFormFields)) {
                $sitegroupPollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouppoll');
                if ($sitegroupPollEnabled) {

                    $availableLabels = array(
                        'registered' => 'All Registered Members',
                        'owner_network' => 'Friends and Networks',
                        'owner_member_member' => 'Friends of Friends',
                        'owner_member' => 'Friends Only',
                        'like_member' => 'Who Liked This Group',
                    );
                    if (!empty($sitegroupMemberEnabled) && $allowMemberInthisPackage) {
                        $availableLabels['member'] = 'Group Members Only';
                    } elseif (!empty($sitegroupMemberEnabled) && $allowMemberInLevel) {
                        $availableLabels['member'] = 'Group Members Only';
                    }
                    $availableLabels['owner'] = $ownerTitle;

                    $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitegroup_group', $user, 'auth_splcreate');
                    $options_create = array_intersect_key($availableLabels, array_flip($options));

                    if (!empty($options_create)) {
                        $can_show_list = true;
                        if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                            if (!Engine_Api::_()->sitegroup()->allowPackageContent($package_id, "modules", "sitegrouppoll")) {
                                $can_show_list = false;
                            }
                        } else {
                            $can_create = Engine_Api::_()->authorization()->getPermission($user_level, 'sitegroup_group', 'splcreate');
                            if (!$can_create) {
                                $can_show_list = false;
                            }
                        }
                        if ($can_show_list) {
                            if (count($options_create) > 1) {
                                $createForm[] = array(
                                    'type' => 'Select',
                                    'name' => 'splcreate',
                                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Polls Creation Privacy'),
                                    'description' => 'Who may create polls for this group?',
                                    'multiOptions' => $options_create,
                                    'value' => @array_search(@end($options_create), $options_create),
                                );
                            }
                        }
                    }
                }
            }
            //END SITEGROUPPOLL PLUGIN WORK
            //START SITEGROUPNOTE PLUGIN WORK
            if (!empty($createFormFields) && in_array('notePrivacy', $createFormFields)) {
                $sitegroupNoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupnote');
                if ($sitegroupNoteEnabled) {
                    $availableLabels = array(
                        'registered' => 'All Registered Members',
                        'owner_network' => 'Friends and Networks',
                        'owner_member_member' => 'Friends of Friends',
                        'owner_member' => 'Friends Only',
                        'like_member' => 'Who Liked This Group',
                    );
                    if (!empty($sitegroupMemberEnabled) && $allowMemberInthisPackage) {
                        $availableLabels['member'] = 'Group Members Only';
                    } elseif (!empty($sitegroupMemberEnabled) && $allowMemberInLevel) {
                        $availableLabels['member'] = 'Group Members Only';
                    }
                    $availableLabels['owner'] = $ownerTitle;

                    $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitegroup_group', $user, 'auth_sncreate');
                    $options_create = array_intersect_key($availableLabels, array_flip($options));

                    if (!empty($options_create)) {
                        $can_show_list = true;
                        if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                            if (!Engine_Api::_()->sitegroup()->allowPackageContent($package_id, "modules", "sitegroupnote")) {
                                $can_show_list = false;
                            }
                        } else {
                            $can_create = Engine_Api::_()->authorization()->getPermission($user_level, 'sitegroup_group', 'sncreate');
                            if (!$can_create) {
                                $can_show_list = false;
                            }
                        }
                        if ($can_show_list) {
                            if (count($options_create) > 1) {
                                $createForm[] = array(
                                    'type' => 'Select',
                                    'name' => 'sncreate',
                                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Notes Creation Privacy'),
                                    'description' => 'Who may create notes for this group?',
                                    'multiOptions' => $options_create,
                                    'value' => @array_search(@end($options_create), $options_create),
                                );
                            }
                        }
                    }
                }
            }
            //END SITEGROUPNOTE PLUGIN WORK
            //START SITEGROUPMUSIC PLUGIN WORK
            if (!empty($createFormFields) && in_array('musicPrivacy', $createFormFields)) {
                $sitegroupMusicEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic');
                if ($sitegroupMusicEnabled) {
                    $availableLabels = array(
                        'registered' => 'All Registered Members',
                        'owner_network' => 'Friends and Networks',
                        'owner_member_member' => 'Friends of Friends',
                        'owner_member' => 'Friends Only',
                        'like_member' => 'Who Liked This Group',
                    );
                    if (!empty($sitegroupMemberEnabled) && $allowMemberInthisPackage) {
                        $availableLabels['member'] = 'Group Members Only';
                    } elseif (!empty($sitegroupMemberEnabled) && $allowMemberInLevel) {
                        $availableLabels['member'] = 'Group Members Only';
                    }
                    $availableLabels['owner'] = $ownerTitle;

                    $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitegroup_group', $user, 'auth_smcreate');
                    $options_create = array_intersect_key($availableLabels, array_flip($options));

                    if (!empty($options_create)) {
                        $can_show_list = true;
                        if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                            if (!Engine_Api::_()->sitegroup()->allowPackageContent($package_id, "modules", "sitegroupmusic")) {
                                $can_show_list = false;
                            }
                        } else {
                            $can_create = Engine_Api::_()->authorization()->getPermission($user_level, 'sitegroup_group', 'smcreate');
                            if (!$can_create) {
                                $can_show_list = false;
                            }
                        }
                        if ($can_show_list) {
                            if (count($options_create) > 1) {
                                $createForm[] = array(
                                    'type' => 'Select',
                                    'name' => 'smcreate',
                                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Music Creation Privacy'),
                                    'description' => 'Who may upload music for this group?',
                                    'multiOptions' => $options_create,
                                    'value' => @array_search(@end($options_create), $options_create),
                                );
                            }
                        }
                    }
                }
            }
            //END SITEGROUPMUSIC PLUGIN WORK
            //START SUB GROUP WORK
            if (!empty($createFormFields) && in_array('subGroupPrivacy', $createFormFields)) {
                if (empty($parent_id)) {
                    $available_Labels = array(
                        'registered' => 'All Registered Members',
                        'owner_network' => 'Friends and Networks',
                        'owner_member_member' => 'Friends of Friends',
                        'owner_member' => 'Friends Only',
                        'like_member' => 'Who Liked This Group',
                    );
                    if (!empty($sitegroupMemberEnabled) && $allowMemberInthisPackage) {
                        $available_Labels['member'] = 'Group Members Only';
                    } elseif (!empty($sitegroupMemberEnabled) && $allowMemberInLevel) {
                        $available_Labels['member'] = 'Group Members Only';
                    }
                    $available_Labels['owner'] = $ownerTitle;

                    $subgroupcreate_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitegroup_group', $user, 'auth_sspcreate');
                    $subgroupcreate_options = array_intersect_key($available_Labels, array_flip($subgroupcreate_options));

                    $can_create = Engine_Api::_()->authorization()->getPermission($user_level, 'sitegroup_group', 'sspcreate');
                    $can_show_list = true;
                    if (!$can_create) {
                        $can_show_list = false;
                    }

                    if (count($subgroupcreate_options) > 1 && !empty($can_show_list)) {
                        $createForm[] = array(
                            'type' => 'Select',
                            'name' => 'sspcreate',
                            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Sub Groups Creation Privacy'),
                            'description' => 'Who may create sub groups in this group?',
                            'multiOptions' => $subgroupcreate_options,
                            'value' => @array_search(@end($subgroupcreate_options), $subgroupcreate_options),
                        );
                    }
                }
            }
            //END WORK FOR SUBGROUP WORK.

            if (!empty($createFormFields) && in_array('claimThisGroup', $createFormFields)) {
                $table = Engine_Api::_()->getDbtable('listmemberclaims', 'sitegroup');
                $select = $table->select()
                        ->where('user_id = ?', $viewer_id)
                        ->limit(1);

                $row = $table->fetchRow($select);
                if ($row !== null) {
                    $createForm[] = array(
                        'type' => 'Checkbox',
                        'name' => 'userclaim',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Show "Claim this Group" link on this group.'),
                        'value' => 1,
                    );
                }
            }

            // Search
            if (!empty($createFormFields) && in_array('search', $createFormFields) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.show.browse', 1)) {
                $createForm[] = array(
                    'type' => 'Checkbox',
                    'name' => 'search',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Show this Group on browse group and in various blocks."),
                    'value' => 1,
                );
            }

            $createForm[] = array(
                'label' => $this->translate('Status'),
                'name' => 'draft',
                'type' => 'select',
                'multiOptions' => array(
                    '1' => $this->translate("Published"),
                    '0' => $this->translate("Saved As Draft")
                ),
                'value' => 1,
            );

            if (isset($subject) && !empty($subject))
                $label = 'Save';
            else
                $label = 'Create';

            $createForm[] = array(
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($label),
                'type' => 'Submit',
                'name' => 'submit'
            );

            $responseForm['form'] = $createForm;
            $responseForm['fields'] = $profileFieldsForm;
            $responseForm['subCategories'] = $subcategoryData;
            return $responseForm;
        } catch (Exception $ex) {
            
        }
    }

    /**
     *
     * Message owner form
     *
     * @return array
     */
    public function getMessageOwnerForm() {
        $message = array();

        // Init title
        $message[] = array(
            'type' => 'Text',
            'name' => 'title',
            'label' => $this->translate('Subject'),
            'hasValidators' => 'true'
        );

        // Init body - plain text
        $message[] = array(
            'type' => 'Textarea',
            'name' => 'body',
            'label' => $this->translate('Message'),
        );

        $message[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => $this->translate('Send Message'),
        );
        return $message;
    }

    /**
     * Enables video plugin
     *
     * 
     * @return boolean
     */
    public function enableVideoPlugin() {

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.show.video', 1)) {
            return Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('video');
        } else {
            return 1;
        }
    }

    /**
     * Enables Notes plugin
     *
     * 
     * @return boolean
     */
    public function enableNotesPlugin() {
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.show.note', 1)) {
            return Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('note');
        } else {
            return 1;
        }
    }

    /**
     * Gets the Profile Types of a Page Based on category
     *
     * @param array object of profilefieldmaps
     *
     * @return array
     */
    public function getProfileTypes($profileFields = array()) {

        $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('sitegroup_group');

        if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
            $profileTypeField = $topStructure[0]->getChild();
            $options = $profileTypeField->getOptions();

            $options = $profileTypeField->getElementParams('sitegroup_group');
            if (isset($options['options']['multiOptions']) && !empty($options['options']['multiOptions']) && is_array($options['options']['multiOptions'])) {
                // Make exist profile fields array.         
                foreach ($options['options']['multiOptions'] as $key => $value) {
                    if (!empty($key)) {
                        $profileFields[$key] = $value;
                    }
                }
            }
        }
        return $profileFields;
    }

    /*
     * Get the profile fields
     * 
     * @param fieldsForm array
     * @return array
     */

    private function _getProfileFields($fieldsForm = array()) {
        foreach ($this->_profileFieldsArray as $option_id => $prfileFieldTitle) {
            if (!empty($option_id)) {
                $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('sitegroup_group');
                $getRowsMatching = $mapData->getRowsMatching('option_id', $option_id);
                $fieldArray = array();
                $getFieldInfo = Engine_Api::_()->fields()->getFieldInfo();
                $getHeadingName = '';
                foreach ($getRowsMatching as $map) {
                    $meta = $map->getChild();
                    $type = $meta->type;

                    if (!empty($type) && ($type == 'heading')) {
                        $getHeadingName = $meta->label;
                        continue;
                    }

                    if (!empty($this->_validateSearchProfileFields) && (!isset($meta->search) || empty($meta->search)))
                        continue;

                    $fieldForm = $getMultiOptions = array();
                    $key = $map->getKey();

                    // Findout respective form element field array.
                    if (isset($getFieldInfo['fields'][$type]) && !empty($getFieldInfo['fields'][$type])) {
                        $getFormFieldTypeArray = $getFieldInfo['fields'][$type];

                        // In case of Generic profile fields.
                        if (isset($getFormFieldTypeArray['category']) && ($getFormFieldTypeArray['category'] == 'generic')) {
                            // If multiOption enabled then perpare the multiOption array.

                            if (($type == 'select') || ($type == 'radio') || (isset($getFormFieldTypeArray['multi']) && !empty($getFormFieldTypeArray['multi']))) {
                                $getOptions = $meta->getOptions();
                                if (!empty($getOptions)) {
                                    foreach ($getOptions as $option) {
                                        $getMultiOptions[$option->option_id] = $option->label;
                                    }
                                }
                            }

                            // Prepare Generic form.
                            $fieldForm['type'] = ucfirst($type);
                            $fieldForm['name'] = $key . '_field_' . $meta->field_id;
                            $fieldForm['label'] = (isset($meta->label) && !empty($meta->label)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($meta->label) : '';
                            $fieldForm['description'] = (isset($meta->description) && !empty($meta->description)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($meta->description) : '';

                            // Add multiOption, If available.
                            if (!empty($getMultiOptions)) {
                                $fieldForm['multiOptions'] = $getMultiOptions;
                            }
                            // Add validator, If available.
                            if (isset($meta->required) && !empty($meta->required))
                                $fieldForm['hasValidator'] = true;

                            if (COUNT($this->_profileFieldsArray) > 1) {

                                if (isset($this->_create) && !empty($this->_create) && $this->_create == 1) {
//                                    $optionCategoryName = Engine_Api::_()->getDbtable('options', 'sitegroup')->getProfileTypeLabel($option_id);
                                    $fieldsForm[$option_id][] = $fieldForm;
                                } else {
                                    $fieldsForm[$option_id][] = $fieldForm;
                                }
                            } else
                                $fieldsForm[] = $fieldForm;
                        }else if (isset($getFormFieldTypeArray['category']) && ($getFormFieldTypeArray['category'] == 'specific') && !empty($getFormFieldTypeArray['base'])) { // In case of Specific profile fields.
                            // Prepare Specific form.
                            $fieldForm['type'] = ucfirst($getFormFieldTypeArray['base']);
                            $fieldForm['name'] = $key . '_field_' . $meta->field_id;
                            $fieldForm['label'] = (isset($meta->label) && !empty($meta->label)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($meta->label) : '';
                            $fieldForm['description'] = (isset($meta->description) && !empty($meta->description)) ? $meta->description : '';

                            // Add multiOption, If available.
                            if ($getFormFieldTypeArray['base'] == 'select') {
                                $getOptions = $meta->getOptions();
                                foreach ($getOptions as $option) {
                                    $getMultiOptions[$option->option_id] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($option->label);
                                }
                                $fieldForm['multiOptions'] = $getMultiOptions;
                            }

                            // Add validator, If available.
                            if (isset($meta->required) && !empty($meta->required))
                                $fieldForm['hasValidator'] = true;

                            if (COUNT($this->_profileFieldsArray) > 1) {
                                if (isset($this->_create) && !empty($this->_create) && $this->_create == 1) {
                                    $optionCategoryName = Engine_Api::_()->getDbtable('options', 'sitereview')->getProfileTypeLabel($option_id);
                                    $fieldsForm[$option_id][] = $fieldForm;
                                } else {
                                    $fieldsForm[$option_id][] = $fieldForm;
                                }
                            } else
                                $fieldsForm[] = $fieldForm;
                        }
                    }
                }
            }
        }
        return $fieldsForm;
    }

    /**
     * Tell a friend Form
     *
     * @return array
     */
    public function getTellAFriendForm() {
        $tell[] = array(
            'type' => 'Text',
            'name' => 'sender_name',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Your Name'),
            'hasValidator' => 'true'
        );

        $tell[] = array(
            'type' => 'Text',
            'name' => 'sender_email',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Your Email'),
            'has Validator' => 'true'
        );

        $tell[] = array(
            'type' => 'Text',
            'name' => 'receiver_emails',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('To'),
            'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Separate multiple addresses with commas'),
            'hasValidators' => 'true'
        );

        $tell[] = array(
            'type' => 'Textarea',
            'name' => 'message',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Message'),
            'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('You can send a personal note in the mail.'),
            'hasValidator' => 'true',
        );

        $tell[] = array(
            'type' => 'Checkbox',
            'name' => 'send_me',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Send a copy to my email address."),
        );


        $tell[] = array(
            'type' => 'Submit',
            'name' => 'send',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Tell a Friend'),
        );

        $response = array();
        $response['form'] = $tell;

        return $response;
    }

    /**
     * Gets claim a group form
     *
     * @return array
     *
     */
    public function getClaimForm() {
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $url = $view->url(array('action' => 'terms'), 'sitegroup_claimgroups', true);
        $response = array();
        $response['info'] = array();
        $response['info']['title'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate('Claim a Page');
        $response['info']['description'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate('Below, you can file a claim for a group on this community that you believe should be owned by you. Your request will be sent to the administration.');
        $response['form'] = array();

        // claimer name
        $response['form'][] = array(
            'type' => 'Text',
            'name' => 'nickname',
            'label' => $this->translate('Your Name'),
            'has Validator' => 'true'
        );

        // claimer email
        $response['form'][] = array(
            'type' => 'Text',
            'name' => 'email',
            'label' => $this->translate('Your Email'),
            'has Validator' => 'true'
        );

        // claimer about
        $response['form'][] = array(
            'type' => 'Text',
            'name' => 'about',
            'label' => $this->translate('About you and the group'),
            'has Validator' => 'true'
        );

        // claimer contactno
        $response['form'][] = array(
            'type' => 'Text',
            'name' => 'contactno',
            'label' => $this->translate('Your Contact number.'),
            'has Validator' => 'true'
        );

        // claimer comments
        $response['form'][] = array(
            'type' => 'Text',
            'name' => 'usercomments',
            'label' => $this->translate('Comments'),
        );


        $description = $description = sprintf(Zend_Registry::get('Zend_Translate')->_("I have read and agree to the <a href='javascript:void(0);' onclick=window.open('%s','mywindow','width=500,height=500')>terms of service</a>."), $url);


        $response['form'][] = array(
            'type' => 'checkbox',
            'name' => 'terms',
            'label' => $this->translate('Terms of Service'),
            'description' => $description,
            'hasValidator' => true,
        );

        $response['form'][] = array(
            'type' => 'Submit',
            'name' => 'send',
            'label' => $this->translate('Send'),
        );

        return $response;
    }

    /**
     * Get information of sitegroup
     *
     * @param sitegroup object
     * @return array 
     */
    public function getInformation($sitegroup) {
        $profileFields = $this->getProfileTypes();
        if (!empty($profileFields)) {
            $this->_profileFieldsArray = $profileFields;
        }
        $information = $this->getProfileInfo($sitegroup);

//        $basicinfoarray = array();
//        $basicinfoarray[$this->translate('Posted By:')] = array('link' => $sitegroup->getParent()->getHref(), 'name' => $sitegroup->getParent()->getTitle());
//        $basicinfoarray[$this->translate('Posted:')] = $this->translate(gmdate('M d, Y', strtotime($sitegroup->creation_date)));
//        $basicinfoarray[$this->translate('Last Updated:')] = $this->translate(gmdate('M d, Y', strtotime($sitegroup->modified_date)));
//
//        $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.member.title', 1);
//        if (!empty($sitegroup->member_count))
//            $basicinfoarray[$this->translate($memberTitle)] = $sitegroup->member_count;
//
//        $basicinfoarray[$this->translate('Views:')] = $sitegroup->view_count;
//        $basicinfoarray[$this->translate('Likes:')] = $sitegroup->like_count;
//
//        if (!empty($sitegroup->follow_count))
//            $basicinfoarray[$this->translate('Followers:')] = $sitegroup->follow_count;
//
//        // Category 
//        $tableCategories = Engine_Api::_()->getDbTable('categories', 'sitegroup');
//        if ($sitegroup->category_id) {
//            $categoriesNmae = $tableCategories->getCategory($sitegroup->category_id);
//            if (!empty($categoriesNmae->category_name)) {
//                $category_name = $categoriesNmae->category_name;
//            }
//
//            if ($sitegroup->subcategory_id) {
//                $subcategory_name = $tableCategories->getCategory($sitegroup->subcategory_id);
//                if (!empty($subcategory_name->category_name)) {
//                    $subcategory_name = $subcategory_name->category_name;
//                }
//
//                // Get sub-sub category
//                if ($sitegroup->subsubcategory_id) {
//                    $subsubcategory_name = $tableCategories->getCategory($sitegroup->subsubcategory_id);
//                    if (!empty($subsubcategory_name->category_name)) {
//                        $subsubcategory_name = $subsubcategory_name->category_name;
//                    }
//                }
//            }
//        }
//
//        $categoryData = "";
//        if ($category_name != '') {
//            // $category = $this->htmlLink($this->url(array('category_id' => $sitegroup->category_id, 'categoryname' => Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategorySlug($category_name)), 'sitegroup_general_category'), $this->translate($category_name));
//            $categoryData = $category_name;
//            if ($subcategory_name != '') {
//                // $subcategory = $this->htmlLink($this->url(array('category_id' => $sitegroup->subcategory_id, 'subcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategorySlug($category_name)), 'sitegroup_general_subcategory'), $this->translate($subcategory_name));
//                $categoryData .= " >> " . $subcategory_name;
//            }
//            if ($subsubcategory_name != '') {
//                // $subsubcategory = $this->htmlLink($this->url(array('category_id' => $sitegroup->subsubcategory_id, 'subsubcategoryname' => Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategorySlug($category_name)), 'sitegroup_general_subsubcategory'), $this->translate($subsubcategory_name));
//                $categoryData .= " >> " . $subsubcategory_name;
//            }
//        }
//
//        if (strlen($categoryData) > 1)
//            $basicinfoarray[$this->translate('Category:')] = $categoryData;
//
//        $sitegrouptags = $sitegroup->tags()->getTagMaps();
//        $tagsData = "";
//        if (count($sitegrouptags) > 0) {
//            $tagcount = 0;
//            foreach ($sitegroupTags as $tag) {
//                $tagsData .= " #" . $tag->getTag()->text;
//            }
//        }
//
//        if (strlen($tagsData) > 1)
//            $basicinfoarray[$this->translate('Tags:')] = $tagsData;
//
//        $enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.price.field', 1);
//
////        if ($enablePrice && $sitegroup->price) {
////            $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
////            $currencyObj = new Zend_Currency($currency);
////            $basicinfoarray[$this->translate('Price:')] = $currencyObj->toCurrency($sitegroup->price);
////        }
//
//        $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.locationfield', 1);
//        if ($enableLocation && $sitegroup->location)
//            $basicinfoarray[$this->translate('Location:')] = $sitegroup->location;
//
//        $basicinfoarray[$this->translate('Description:')] = $sitegroup->body;
//        $information['basic_information'] = $basicinfoarray;
        return $information;
    }

    /**
     * Gets default profile ids
     *
     * @param sitegroup object
     * return array
     */
    public function getDefaultProfileTypeId($subject) {
        $getFieldId = null;
        $fieldsByAlias = Engine_Api::_()->fields()->getFieldsObjectsByAlias($subject);
        if (!empty($fieldsByAlias['profile_type'])) {
            $optionId = $fieldsByAlias['profile_type']->getValue($subject);
            $getFieldId = $optionId->value;
        }
        if (empty($getFieldId)) {
            return;
        }

        return $getFieldId;
    }

    /**
     * Get the Profile Fields Information, which will show on profile group.
     *
     * @param sitegroup object , setkeyasresponse boolean
     * @return array
     */
    public function getProfileInfo($subject, $setKeyAsResponse = false) {
        // Getting the default Profile Type id.
        $getFieldId = $this->getDefaultProfileTypeId($subject);
        // Start work to get form values.
        $values = Engine_Api::_()->fields()->getFieldsValues($subject);
        $fieldValues = array();
        // In case if Profile Type available. like User module.
        if (!empty($getFieldId)) {
            // Set the default profile type.
            $this->_profileFieldsArray[$getFieldId] = $getFieldId;
            $_getProfileFields = $this->_getProfileFields();
            $specificProfileFields[$getFieldId] = $_getProfileFields[$getFieldId];
            foreach ($specificProfileFields as $heading => $tempValue) {
                foreach ($tempValue as $value) {
                    $key = $value['name'];
                    $label = $value['label'];
                    $type = $value['type'];
                    $parts = @explode('_', $key);

                    if (count($parts) < 3)
                        continue;

                    list($parent_id, $option_id, $field_id) = $parts;

                    $valueRows = $values->getRowsMatching(array(
                        'field_id' => $field_id,
                        'item_id' => $subject->getIdentity()
                    ));

                    if (!empty($valueRows)) {
                        foreach ($valueRows as $fieldRow) {

                            $tempValue = $fieldRow->value;

                            // In case of Select or Multi send the respective label.
                            if (isset($value['multiOptions']) && !empty($value['multiOptions']) && isset($value['multiOptions'][$fieldRow->value]))
                                $tempValue = $value['multiOptions'][$fieldRow->value];
                            $tempKey = !empty($setKeyAsResponse) ? $key : $label;
                            $fieldValues[$tempKey] = $tempValue;
                        }
                    }
                }
            }
        } else { // In case, If there are no Profile Type available and only Profile Fields are available. like Classified.
            $getType = $subject->getType();
            $_getProfileFields = $this->_getProfileFields($getType);

            foreach ($_getProfileFields as $value) {
                $key = $value['name'];
                $label = $value['label'];
                $parts = @explode('_', $key);

                if (count($parts) < 3)
                    continue;

                list($parent_id, $option_id, $field_id) = $parts;

                $valueRows = $values->getRowsMatching(array(
                    'field_id' => $field_id,
                    'item_id' => $subject->getIdentity()
                ));

                if (!empty($valueRows)) {
                    foreach ($valueRows as $fieldRow) {
                        if (!empty($fieldRow->value)) {
                            $tempKey = !empty($setKeyAsResponse) ? $key : $label;
                            $fieldValues[$tempKey] = $fieldRow->value;
                        }
                    }
                }
            }
        }

        return $fieldValues;
    }

    public function getcommentForm($type, $id) {
        $commentform = array();
        $commentform[] = array(
            'type' => "text",
            'name' => 'body',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Comment'),
        );
    }

    public function setPhoto($photo, $subject, $needToUplode = false, $params = array()) {
        try {

            if ($photo instanceof Zend_Form_Element_File) {
                $file = $photo->getFileName();
            } else if (is_array($photo) && !empty($photo['tmp_name'])) {
                $file = $photo['tmp_name'];
            } else if (is_string($photo) && file_exists($photo)) {
                $file = $photo;
            } else {
                throw new Group_Model_Exception('invalid argument passed to setPhoto');
            }
        } catch (Exception $e) {
            
        }

        $imageName = $photo['name'];
        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

        $params = array(
            'parent_type' => 'siteevent_event',
            'parent_id' => $subject->getIdentity()
        );

        // Save
        $storage = Engine_Api::_()->storage();

        // Resize image (main)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(720, 720)
                ->write($path . '/m_' . $imageName)
                ->destroy();

        // Resize image (profile)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(200, 400)
                ->write($path . '/p_' . $imageName)
                ->destroy();

        // Resize image (normal)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(140, 160)
                ->write($path . '/in_' . $imageName)
                ->destroy();

        // Resize image (icon)
        $image = Engine_Image::factory();
        $image->open($file);

        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)
                ->write($path . '/is_' . $imageName)
                ->destroy();

        // Store
        $iMain = $storage->create($path . '/m_' . $imageName, $params);
        $iProfile = $storage->create($path . '/p_' . $imageName, $params);
        $iIconNormal = $storage->create($path . '/in_' . $imageName, $params);
        $iSquare = $storage->create($path . '/is_' . $imageName, $params);

        $iMain->bridge($iProfile, 'thumb.profile');
        $iMain->bridge($iIconNormal, 'thumb.normal');
        $iMain->bridge($iSquare, 'thumb.icon');

        // Remove temp files
        @unlink($path . '/p_' . $imageName);
        @unlink($path . '/m_' . $imageName);
        @unlink($path . '/in_' . $imageName);
        @unlink($path . '/is_' . $imageName);

        // Update row
        if (empty($needToUplode)) {
            $subject->modified_date = date('Y-m-d H:i:s');
            $subject->photo_id = $iMain->file_id;
            $subject->save();
        }

        // Add to album
        $viewer = Engine_Api::_()->user()->getViewer();
        $photoTable = Engine_Api::_()->getItemTable('sitegroup_photo');
        if (isset($params['album_id']) && !empty($params['album_id'])) {
            $album = Engine_Api::_()->getItem('sitegroup_album', $params['album_id']);
            if (!$album->toArray()) {
                $album = $subject->getSingletonAlbum();
                $album->owner_id = $viewer->getIdentity();
                $album->save();
            }
        } else {
            $album = $subject->getSingletonAlbum();
            $album->owner_id = $viewer->getIdentity();
            $album->save();
        }

        $photoItem = $photoTable->createRow();
        $photoItem->setFromArray(array(
            'group_id' => $subject->getIdentity(),
            'album_id' => $album->getIdentity(),
            'user_id' => $viewer->getIdentity(),
            'file_id' => $iMain->getIdentity(),
            'collection_id' => $album->getIdentity()
        ));
        $photoItem->save();

        return $subject;
    }

    /**
     * Gutter menu show on the sitegroup manage/profile page.
     * 
     * @return array
     */
    public function gutterMenus($subject, $action = 'view') {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $menus = array();
        $allowGroup = Engine_Api::_()->sitegroup()->allowInThisGroup($subject, "sitegroupmember", 'smecreate');
        $can_edit = Engine_Api::_()->sitegroup()->isManageAdmin($subject, 'edit');


        // EDIT PAGE DETAILS DASHBOARD
        if ($viewer->getIdentity() && $subject->authorization()->isAllowed($viewer, 'edit')) {
            $menus[] = array(
                'name' => 'edit',
                'label' => $this->translate('Edit Group Details'),
                'url' => 'advancedgroup/edit/' . $subject->getIdentity(),
            );
        }

        // Share Page
        if (!empty($viewer_id) && ($action == 'view')) {
            $menus[] = array(
                'name' => 'share',
                'label' => $this->translate('Share This Group'),
                'url' => 'activity/share',
                'urlParams' => array(
                    "type" => $subject->getType(),
                    "id" => $subject->getIdentity()
                )
            );
        }

        //SHOW MESSAGE OWNER LINK TO USER IF MESSAGING IS ENABLED FOR THIS LEVEL
        if (isset($viewer->level_id) && !empty($viewer->level_id) && ($action == 'view')) {
            $showMessageOwner = 0;
            $showMessageOwner = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'auth');

            if ($showMessageOwner != 'none') {
                $showMessageOwner = 1;
            }

            //SHOW IF AUTHORIZED
            if ($subject->owner_id != $viewer_id && !empty($viewer_id) && !empty($showMessageOwner)) {
                if (!empty($viewer_id)) {
                    $menus[] = array(
                        'name' => 'messageowner',
                        'label' => $this->translate('Message Owner'),
                        'url' => 'advancedgroup/messageowner/' . $subject->getIdentity()
                    );
                }
            }
        }

        //TELL A FRIEND
        if (($action == 'view')) {
            $menus[] = array(
                'name' => 'tellafriend',
                'label' => $this->translate('Tell a friend'),
                'url' => 'advancedgroup/tellafriend/' . $subject->getIdentity()
            );
        }

        // write a review
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview') && $viewer_id) {
            $hasPostedReview = Engine_Api::_()->getDbtable('reviews', 'sitegroupreview')->canPostReview($subject->getIdentity(), $viewer_id);
            if ($hasPostedReview) {
                $menus[] = array(
                    'label' => $this->translate("Update Review"),
                    'name' => 'update_review',
                    'url' => "advancedgroups/review/edit/" . $subject->getIdentity() . "/" . $hasPostedReview,
                );
            } else if ($viewer_id != $subject->owner_id && $viewer_id) {
                $menus[] = array(
                    'label' => $this->translate("Write a Review "),
                    'name' => 'create_review',
                    'url' => "advancedgroups/reviews/create/" . $subject->getIdentity(),
                );
            }
        }
        // get follow and unfollow
        if ($viewer_id != $subject->getOwner()->getIdentity() && $viewer_id) {
            $followsData = Engine_Api::_()->getDbTable('follows', 'seaocore')->getFollow($subject, $viewer);
            if ($followsData) {
                $menus[] = array(
                    'label' => $this->translate("UnFollow"),
                    'name' => 'unfollow',
                    'url' => 'advancedgroup/follow/' . $subject->getIdentity(),
                );
            } else {
                $menus[] = array(
                    'label' => $this->translate("Follow"),
                    'name' => 'follow',
                    'url' => 'advancedgroup/follow/' . $subject->getIdentity(),
                );
            }
        }
        if (Engine_Api::_()->hasModuleBootstrap('sitegroupmember') && !empty($viewer_id)) {
            $joinMembers = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($viewer_id, $subject->group_id);
            if (empty($joinMembers) && $viewer_id != $subject->owner_id && !empty($allowGroup)) {
                if (!empty($viewer_id)) {
                    if (!empty($subject->member_approval))
                        $menus[] = array(
                            'label' => $this->translate('Join Group'),
                            'name' => 'join',
                            'url' => 'advancedgroups/member/join/' . $subject->getIdentity(),
                        );
                    else {
                        $menus[] = array(
                            'label' => $this->translate('Join Group'),
                            'name' => 'request_invite',
                            'url' => 'advancedgroups/member/request/' . $subject->getIdentity(),
                        );
                    }
                }
            }
            $joinMembers = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($viewer_id, $subject->group_id, 'Cancel');
            if (!empty($joinMembers) && $viewer_id != $subject->owner_id && !empty($allowGroup)) {
                $menus[] = array(
                    'label' => $this->translate('Cancel Membership Request'),
                    'name' => 'cancel',
                    'url' => 'advancedgroups/member/cancel/' . $subject->getIdentity(),
                );
            }
            $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($viewer_id, $subject->group_id, $params = "Leave");
            if (!empty($hasMembers) && $viewer_id != $subject->owner_id && Engine_Api::_()->sitegroup()->allowInThisGroup($subject, "sitegroupmember", 'smecreate')) {
                $menus[] = array(
                    'label' => $this->translate('Leave Group'),
                    'name' => 'leave',
                    'url' => 'advancedgroups/member/leave/' . $subject->getIdentity(),
                );
            }

            $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($viewer_id, $subject->group_id, $params = 'Invite');
            if (!empty($hasMembers) && !empty($can_edit)) {
                $menus[] = array(
                    'label' => $this->translate('Add People'),
                    'name' => 'invite',
                    'url' => 'advancedgroups/member/invite-members/' . $subject->getIdentity(),
                );
            } elseif (!empty($hasMembers) && empty($subject->member_invite)) {
                $menus[] = array(
                    'label' => $this->translate('Add People'),
                    'name' => 'invite',
                    'url' => 'advancedgroups/member/invite-members/' . $subject->getIdentity(),
                );
            }
        }

// package work
//        if (Engine_Api::_()->sitegroup()->canShowPaymentLink($subject->page_id)) {
//            $menus[] = array(
//                'name' => 'payment',
//                'label' => $this->translate('Payment page'),
//                'url' => 'sitepage/gateway/' . $subject->getIdentity(),
//            );
//        }
// notification settings
// $menus[] = array(
//     'name' => 'notification',
//     'label' => $this->translate('Notification Setting'),
//     'url' => 'sitepage/notification-settings'
// );
        // PUBLISH PAGE
        if ($subject->draft != 1 && ($viewer_id == $subject->owner_id)) {
            $menus[] = array(
                'name' => 'publish',
                'label' => $this->translate('Publish Group'),
                'url' => 'advancedgroup/publish/' . $subject->getIdentity(),
            );
        }
// claim this page 
        if ($viewer->getIdentity() && $subject->authorization()->isAllowed($viewer, 'claim') && ($action == 'view')) {
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteg.oup', 1)) {
                $listmemberclaimsTable = Engine_Api::_()->getDbtable('listmemberclaims', 'sitegroup');
                $listmemberclaimsTablename = $listmemberclaimsTable->info('name');
                $select = $listmemberclaimsTable->select()->from($listmemberclaimsTablename, array('count(*) as total_count'))
                        ->where('user_id = ?', $subject->owner_id);
                $row = $listmemberclaimsTable->fetchAll($select);

                if (isset($row[0]['total_count']) && !empty($row[0]['total_count'])) {
                    $total_count = 1;
                }

                if ($total_count && $subject->owner_id != $viewer_id && !empty($subject->userclaim)) {
                    $menus[] = array(
                        'name' => 'claim',
                        'label' => $this->translate('Claim Group'),
                        'url' => 'advancedgroup/claim/' . $subject->getIdentity(),
                    );
                }
            }
        }

        if ($action != "manage") {
// CLOSE / OPEN AN Page
            if ($viewer->getIdentity() && $subject->closed != 1 && $subject->authorization()->isAllowed($viewer, 'edit')) {
                $menus[] = array(
                    'name' => 'close',
                    'label' => $this->translate('Close Group'),
                    'url' => 'advancedgroup/close/' . $subject->getIdentity(),
                );
            }

// CLOSE / OPEN AN PAGE
            if ($viewer->getIdentity() && $subject->closed == 1 && $subject->authorization()->isAllowed($viewer, 'edit')) {
                $menus[] = array(
                    'name' => 'open',
                    'label' => $this->translate('Open Group'),
                    'url' => 'advancedgroup/close/' . $subject->getIdentity(),
                );
            }
        }

// CLOSE / OPEN AN PAGE
        if ($viewer->getIdentity() && $subject->authorization()->isAllowed($viewer, 'delete')) {
            $menus[] = array(
                'name' => 'delete',
                'label' => $this->translate('Delete Group'),
                'url' => 'advancedgroup/delete/' . $subject->getIdentity(),
            );
        }

        if (!empty($viewer_id) && ($action == 'view')) {
            $menus[] = array(
                'name' => 'report',
                'label' => $this->translate('Report This Group'),
                'url' => 'report/create/subject/' . $subject->getGuid(),
                'urlParams' => array(
                    "type" => $subject->getType(),
                    "id" => $subject->getIdentity()
                )
            );
        }

        return $menus;
    }

    /**
     * Set the profile fields value to newly created listing.
     * 
     * @return array
     */
    public function setProfileFields($sitegroup, $data) {
// Iterate over values
        $values = Engine_Api::_()->fields()->getFieldsValues($sitegroup);

        $fVals = $data;
        $privacyOptions = Fields_Api_Core::getFieldPrivacyOptions();
        foreach ($fVals as $key => $value) {
            if (strstr($key, 'oauth'))
                continue;
            $parts = explode('_', $key);
            if (count($parts) < 3)
                continue;
            list($parent_id, $option_id, $field_id) = $parts;

            $valueParts = explode(',', $value);

// Array mode
            if (is_array($valueParts) && count($valueParts) > 1) {
// Lookup
                $valueRows = $values->getRowsMatching(array(
                    'field_id' => $field_id,
                    'item_id' => $sitegroup->getIdentity()
                ));
// Delete all
                foreach ($valueRows as $valueRow) {
                    $valueRow->delete();
                }
                if ($field_id == 0)
                    continue;
// Insert all
                $indexIndex = 0;
                if (is_array($valueParts) || !empty($valueParts)) {
                    foreach ((array) $valueParts as $singleValue) {

                        $valueRow = $values->createRow();
                        $valueRow->field_id = $field_id;
                        $valueRow->item_id = $sitegroup->getIdentity();
                        $valueRow->index = $indexIndex++;
                        $valueRow->value = $singleValue;
                        $valueRow->save();
                    }
                } else {
                    $valueRow = $values->createRow();
                    $valueRow->field_id = $field_id;
                    $valueRow->item_id = $sitegroup->getIdentity();
                    $valueRow->index = 0;
                    $valueRow->value = '';
                    $valueRow->save();
                }
            }

// Scalar mode
            else {
                try {
// Lookup
                    $valueRows = $values->getRowsMatching(array(
                        'field_id' => $field_id,
                        'item_id' => $sitegroup->getIdentity()
                    ));
// Delete all
                    $prevPrivacy = null;
                    foreach ($valueRows as $valueRow) {
                        $valueRow->delete();
                    }

// Remove value row if empty
                    if (empty($value)) {
                        if ($valueRow) {
                            $valueRow->delete();
                        }
                        continue;
                    }

                    if ($field_id == 0)
                        continue;
// Lookup
                    $valueRow = $values->getRowMatching(array(
                        'field_id' => $field_id,
                        'item_id' => $sitegroup->getIdentity(),
                        'index' => 0
                    ));
// Create if missing
                    $isNew = false;
                    if (!$valueRow) {
                        $isNew = true;
                        $valueRow = $values->createRow();
                        $valueRow->field_id = $field_id;
                        $valueRow->item_id = $sitegroup->getIdentity();
                    }
                    $valueRow->value = htmlspecialchars($value);
                    $valueRow->save();
                } catch (Exception $ex) {
                    
                }
            }
        }

        return;
    }

    /**
     * Translate the text from english to specified language by user
     *
     * 	@param message string
     *   @return string
     */
    private function translate($message) {
        return Engine_Api::_()->getApi('Core', 'siteapi')->translate($message);
    }

}
