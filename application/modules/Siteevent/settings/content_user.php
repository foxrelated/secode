<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content_user.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

$widgetDescription = Engine_Api::_()->siteevent()->isTicketBasedEvent() ? ' [Note: "Join Button" will be replaced by "Book Now", RSVP & Guest details will not display, if "Tickets" setting is enabled in "Ticket Settings" section.]' : '';
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
$type_video = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.show.video');

if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.proximity.search.kilometer', 0)) {
    $locationDescription = "Choose the kilometers within which events will be displayed. (This setting will only work, if you have chosen 'Yes' in the above setting.)";
    $locationLableS = "Kilometer";
    $locationLable = "Kilometers";
} else {
    $locationDescription = "Choose the miles within which events will be displayed. (This setting will only work, if you have chosen 'Yes' in the above setting.)";
    $locationLableS = "Mile";
    $locationLable = "Miles";
}

$detactLocationElement = array(
    'Select',
    'detactLocation',
    array(
        'label' => 'Do you want to display events based on user’s current location?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => '0'
    )
);

$contentTypes = Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1));
$contentTypeArray = array();
if (!empty($contentTypes)) {
    $contentTypeArray[] = 'All';
    $moduleTitle = '';
    foreach ($contentTypes as $contentType) {
        if ($contentType['item_title']) {
            $contentTypeArray['user'] = Zend_Registry::get('Zend_Translate')->translate('Member Events');
            $contentTypeArray[$contentType['item_type']] = $contentType['item_title'];
        } else {
            if (Engine_Api::_()->hasModuleBootstrap('sitereview') && Engine_Api::_()->hasModuleBootstrap('sitereviewlistingtype')) {
                $moduleTitle = 'Reviews & Ratings - Multiple Listing Types';
            } elseif (Engine_Api::_()->hasModuleBootstrap('sitereview')) {
                $moduleTitle = 'Multiple Listing Types Plugin Core (Reviews & Ratings Plugin)';
            }
            $explodedResourceType = explode('_', $contentType['item_type']);
            if (isset($explodedResourceType[2]) && $moduleTitle) {
                $listingtypesTitle = Engine_Api::_()->getDbtable('listingtypes', 'sitereview')->getListingRow($explodedResourceType[2])->title_plural;
                $listingtypesTitle = $listingtypesTitle . ' ( ' . $moduleTitle . ' ) ';
                $contentTypeArray[$contentType['item_type']] = $listingtypesTitle;
            } else {
                $contentTypeArray[$contentType['item_type']] = Engine_Api::_()->getDbtable('modules', 'siteevent')->getModuleTitle($contentType['item_module']);
            }
        }
    }
}
if (!empty($contentTypeArray)) {
    $contentTypeElement = array(
        'Select',
        'eventType',
        array(
            'label' => $view->translate('Event Type'),
            'multiOptions' => $contentTypeArray,
        ),
        'value' => '',
    );
} else {
    $contentTypeElement = array(
        'Hidden',
        'eventType',
        array(
            'label' => $view->translate('Event Type'),
            'value' => 'All',
        )
    );
}

$defaultLocationDistanceElement = array(
    'Select',
    'defaultLocationDistance',
    array(
        'label' => $locationDescription,
        'multiOptions' => array(
            '0' => '',
            '1' => '1 ' . $locationLableS,
            '2' => '2 ' . $locationLable,
            '5' => '5 ' . $locationLable,
            '10' => '10 ' . $locationLable,
            '20' => '20 ' . $locationLable,
            '50' => '50 ' . $locationLable,
            '100' => '100 ' . $locationLable,
            '250' => '250 ' . $locationLable,
            '500' => '500 ' . $locationLable,
            '750' => '750 ' . $locationLable,
            '1000' => '1000 ' . $locationLable,
        ),
        'value' => '1000'
    )
);

//CHECK IF FACEBOOK PLUGIN IS ENABLE
$fbmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebookse');

if (!empty($fbmodule) && !empty($fbmodule->enabled) && $fbmodule->version > '4.2.7p1') {
    $show_like_button = array(
        '1' => $view->translate('Yes, show SocialEngine Core Like button'),
        '2' => $view->translate('Yes, show Facebook Like button'),
        '0' => $view->translate('No'),
    );
    $default_value = 2;
} else {
    $show_like_button = array(
        '1' => $view->translate('Yes, show SocialEngine Core Like button'),
        '0' => $view->translate('No'),
    );
    $default_value = 1;
}

$popularity_options = array(
    'view_count' => $view->translate('Most Viewed'),
    'like_count' => $view->translate('Most Liked'),
    'comment_count' => $view->translate('Most Commented'),
    'review_count' => $view->translate('Most Reviewed'),
    'member_count' => $view->translate('Most Joined'),
    'rating_avg' => $view->translate('Most Rated (Average Rating)'),
    'rating_editor' => $view->translate('Most Rated (Editor Rating)'),
    'rating_users' => $view->translate('Most Rated (User Ratings)'),
    'event_id' => $view->translate('Recently Created'),
    'modified_date' => $view->translate('Recently Updated'),
    'starttime' => $view->translate('Start Time'),
);

$featuredSponsoredElement = array(
    'Select',
    'fea_spo',
    array(
        'label' => $view->translate('Show Events'),
        'multiOptions' => array(
            '' => '',
            'newlabel' => $view->translate('New Only'),
            'featured' => $view->translate('Featured Only'),
            'sponsored' => $view->translate('Sponsored Only'),
            'fea_spo' => $view->translate('Either Featured or Sponsored'),
        ),
        'value' => '',
    )
);

$statisticsElement = array(
    'MultiCheckbox',
    'statistics',
    array(
        'label' => $view->translate('Choose the statistics that you want to be displayed for the Events in this block.'),
        'multiOptions' => array("viewCount" => "Views", "likeCount" => "Likes", "commentCount" => "Comments", "memberCount" => "Guests", 'reviewCount' => 'Reviews'),
    ),
);

$statisticsDiaryElement = array(
    'MultiCheckbox',
    'statisticsDiary',
    array(
        'label' => $view->translate('Choose the statistics that you want to be displayed for the Diaries in this block (This setting will work only for the list view).'),
        'multiOptions' => array("viewCount" => "Views", "entryCount" => "Events"),
    ),
);

$tempOtherInfoElement = array(
    "hostName" => "Hosted By",
    "categoryLink" => "Category",
    "featuredLabel" => "Featured Label",
    "sponsoredLabel" => "Sponsored Label",
    "newLabel" => "New Label",
    "startDate" => "Start Date and Time",
    "endDate" => "End Date and Time",
    "ledBy" => "Led By",
    "price" => "Price",
    "venueName" => "Venue Name",
    "location" => "Location",
    "directionLink" => "Open Get Direction popup on clicking location. (Dependent on Location)"
);

$otherInfoElement = array(
    'MultiCheckbox',
    'eventInfo',
    array(
        'label' => $view->translate('Choose the options that you want to be displayed for the Events in this block.'),
        'multiOptions' => array_merge($tempOtherInfoElement, array("viewCount" => "Views", "likeCount" => "Likes", "commentCount" => "Comments", "memberCount" => "Guests", 'reviewCount' => 'Reviews', 'ratingStar' => 'Ratings')),
    ),
);

$otherInfoElementGrid = array(
    'MultiCheckbox',
    'eventInfo',
    array(
        'label' => $view->translate('Choose the options that you want to be displayed for the Events in this block.'),
        'multiOptions' => array_merge(array(
            "hostName" => "Hosted By",
            "categoryLink" => "Category",
            "featuredLabel" => "Featured Label (for Grid View only)",
            "sponsoredLabel" => "Sponsored Label (for Grid View only)",
            "newLabel" => "New Label (for Grid View only)",
            "startDate" => "Start Date and Time",
            "endDate" => "End Date and Time",
            "ledBy" => "Led By",
            "price" => "Price",
            "venueName" => "Venue Name",
            "location" => "Location",
            "directionLink" => "Open Get Direction popup on clicking location. (Dependent on Location)"
                ), array("viewCount" => "Views", "likeCount" => "Likes", "commentCount" => "Comments", "memberCount" => "Guests", 'reviewCount' => 'Reviews', 'ratingStar' => 'Ratings')),
    ),
);

$truncationLocationElement = array(
    'Text',
    'truncationLocation',
    array(
        'label' => 'Truncation Limit of Location (Depend on Location)',
        'value' => 50,
    )
);

$ratingTypeElement = array(
    'Select',
    'ratingType',
    array(
        'label' => $view->translate('Rating Type'),
        'multiOptions' => array('rating_avg' => $view->translate('Average Ratings'), 'rating_editor' => $view->translate('Only Editor Ratings'), 'rating_users' => $view->translate('Only User Ratings'), 'rating_both' => $view->translate('Both User and Editor Ratings')),
    )
);

$eventTypeElement = array(
    'Radio',
    'showEventType',
    array(
        'label' => $view->translate('Select Events that you want to be shown in this block.'),
        'multiOptions' => array('all' => $view->translate('All Events'), 'upcoming' => $view->translate('Only Upcoming Events')),
        'value' => 'upcoming',
    )
);

$titlePositionElement = array(
    'Radio',
    'titlePosition',
    array(
        'label' => 'Do you want "Event Title" to be displayed inside the Grid View?',
        'multiOptions' => array(1 => 'Yes', 0 => 'No'),
        'value' => 1,
    ),
);

$categories = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategories(array('category_id', 'category_name'), null, 0, 0, 1);
$categories_prepared = array();
if (count($categories) != 0) {
    $categories_prepared[0] = "";
    foreach ($categories as $category) {
        $categories_prepared[$category->category_id] = $category->category_name;
    }

    $categoryElement = array(
        'Select',
        'category_id',
        array(
            'label' => 'Category',
            'multiOptions' => $categories_prepared,
            'RegisterInArrayValidator' => false,
            'onchange' => 'addOptions(this.value, "cat_dependency", "subcategory_id", 0); setHiddenValues("category_id")'
    ));

    $subCategoryElement = array(
        'Select',
        'subcategory_id',
        array(
            'RegisterInArrayValidator' => false,
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => 'application/modules/Siteevent/views/scripts/_category.tpl',
                        'class' => 'form element')))
    ));
}

$calendarElement = array(
    'Select',
    'date',
    array(
        'RegisterInArrayValidator' => false,
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => 'application/modules/Siteevent/views/scripts/_calendar.tpl',
                    'class' => 'form element')))
        ));

$hiddenCatElement = array(
    'Text',
    'hidden_category_id',
    array(
        ));

$hiddenSubCatElement = array(
    'Text',
    'hidden_subcategory_id',
    array(
        ));

$hiddenSubSubCatElement = array(
    'Text',
    'hidden_subsubcategory_id',
    array(
        ));

$final_array = array(
    array(
        'title' => $view->translate('Content Type Event'),
        'description' => $view->translate('Displays a list of content type events on your site. This widget should be placed on Advanced Events - Event Profile page.').$widgetDescription,
        'category' => $view->translate('Advanced Events'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.contenttype-events',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'layouts_views' => array("1", "2", "3"),
            'layouts_order' => 1,
            'statistics' => array("viewCount", "likeCount", "commentCount", "memberCount", "reviewCount"),
            'columnWidth' => '180',
            'truncationGrid' => 90
        ),
        'adminForm' => array(
            'elements' => array(
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                $hiddenSubSubCatElement,
                array(
                    'MultiCheckbox',
                    'eventFilterTypes',
                    array(
                        'label' => $view->translate('Choose the Event Types of which you want to show Events.'),
                        'multiOptions' => array("upcoming" => "Upcoming Events", "past" => "Past Events"),
                    ),
                ),
                array(
                    'MultiCheckbox',
                    'layouts_views',
                    array(
                        'label' => $view->translate('Choose the view types that you want to be available for events.'),
                        'multiOptions' => array("1" => $view->translate("List View"), "2" => $view->translate("Grid View"), "3" => $view->translate("Map View")),
                    ),
                ),
                array(
                    'Radio',
                    'layouts_order',
                    array(
                        'label' => $view->translate('Select a default view type for Events.'),
                        'multiOptions' => array("1" => $view->translate("List View"), "2" => $view->translate("Grid View"), "3" => $view->translate("Map View")),
                        'value' => 1,
                    )
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => $view->translate('Column Width For Grid View.'),
                        'value' => '180',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => $view->translate('Column Height For Grid View.'),
                        'value' => '328',
                    )
                ),
                $otherInfoElement,
                $titlePositionElement,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of Events to show)'),
                        'value' => 10,
                    )
                ),
                $truncationLocationElement,
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => $view->translate('Title Truncation Limit'),
                        'value' => 25,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Text',
                    'truncationGrid',
                    array(
                        'label' => $view->translate('Title Truncation Limit in Grid View'),
                        'value' => 90,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                $ratingTypeElement,
                $detactLocationElement,
                $defaultLocationDistanceElement,
            ),
        ),
    ),
);


return $final_array;
