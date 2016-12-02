<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$type_video = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.show.video');
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
$siteeventrepeat = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventrepeat');
$siteeventrepeat_settings = array();
$eventinfoSetting = array();
if ($siteeventrepeat) {
    $siteeventrepeat_settings = array("showrepeatinfo" => "Event Types (Daily, Weekly, Monthly) and Time");
    $eventinfoSetting = array(
        'MultiCheckbox',
        'options',
        array(
            'label' => 'Select the options that you want to display in this block.',
            'multiOptions' => $siteeventrepeat_settings,
        ),
    );
}
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

    if (!empty($contentTypes))
        $contentTypeArray[] = 'All';
    $moduleTitle = '';
    foreach ($contentTypes as $contentType) {
        if ($contentType['item_title']) {
            $contentTypeArray['user'] = 'Member Events';
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
        'contentType',
        array(
            'label' => 'Event Type',
            'multiOptions' => $contentTypeArray,
        ),
        'value' => '',
    );
} else {
    $contentTypeElement = array(
        'Hidden',
        'contentType',
        array(
            'label' => 'Event Type',
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

$popularity_options = array(
    'view_count' => 'Most Viewed',
    'like_count' => 'Most Liked',
    'comment_count' => 'Most Commented',
    'review_count' => 'Most Reviewed',
    'member_count' => 'Most Joined',
    'rating_avg' => 'Most Rated (Average Rating)',
    'rating_editor' => 'Most Rated (Editor Rating)',
    'rating_users' => 'Most Rated (User Ratings)',
    'event_id' => 'Recently Created',
    'modified_date' => 'Recently Updated',
    'starttime' => 'Start Time',
);

$featuredSponsoredElement = array(
    'Select',
    'fea_spo',
    array(
        'label' => 'Show Events',
        'multiOptions' => array(
            '' => '',
            'newlabel' => 'New Only',
            'featured' => 'Featured Only',
            'sponsored' => 'Sponsored Only',
            'fea_spo' => 'Either Featured or Sponsored',
        ),
        'value' => '',
    )
);

$statisticsElement = array(
    'MultiCheckbox',
    'statistics',
    array(
        'label' => 'Choose the statistics that you want to be displayed for the Events in this block.',
        'multiOptions' => array("viewCount" => "Views", "likeCount" => "Likes", "commentCount" => "Comments", "memberCount" => "Guests", 'reviewCount' => 'Reviews'),
    ),
);

$statisticsDiaryElement = array(
    'MultiCheckbox',
    'statisticsDiary',
    array(
        'label' => 'Choose the statistics that you want to be displayed for the Diaries in this block (This setting will work only for the list view).',
        'multiOptions' => array("viewCount" => "Views", "entryCount" => "Events"),
    ),
);

$tempOtherInfoElement = array(
    "hostName" => "Hosted By",
    "categoryLink" => "Category",
//    "featuredLabel" => "Featured Label (for Grid View only)",
//    "sponsoredLabel" => "Sponsored Label (for Grid View only)",
//    "newLabel" => "New Label (for Grid View only)",
    "startDate" => "Start Date and Time",
    "endDate" => "End Date and Time",
    "ledBy" => "Led By",
    "price" => "Price",
    "venueName" => "Venue Name",
    "location" => "Location",
);

$otherInfoElement = array(
    'MultiCheckbox',
    'eventInfo',
    array(
        'label' => 'Choose the options that you want to be displayed for the Events in this block.',
        'multiOptions' => array_merge($tempOtherInfoElement, array("viewCount" => "Views", "likeCount" => "Likes", "commentCount" => "Comments", "memberCount" => "Guests", 'reviewCount' => 'Reviews', 'ratingStar' => 'Ratings')),
    ),
);

$otherInfoElementGrid = array(
    'MultiCheckbox',
    'eventInfo',
    array(
        'label' => 'Choose the options that you want to be displayed for the Events in this block.',
        'multiOptions' => array_merge(array(
            "hostName" => "Hosted By",
            "categoryLink" => "Category",
//            "featuredLabel" => "Featured Label (for Grid View only)",
//            "sponsoredLabel" => "Sponsored Label (for Grid View only)",
//            "newLabel" => "New Label (for Grid View only)",
            "startDate" => "Start Date and Time",
            "endDate" => "End Date and Time",
            "ledBy" => "Led By",
            "price" => "Price",
            "venueName" => "Venue Name",
            "location" => "Location",
                ), array("viewCount" => "Views", "likeCount" => "Likes", "commentCount" => "Comments", "memberCount" => "Guests", 'reviewCount' => 'Reviews', 'ratingStar' => 'Ratings')),
    ),
);

$truncationLocationElement = array(
    'Text',
    'truncationLocation',
    array(
        'label' => 'Truncation Limit of Location (Depend on Location)',
        'value' => 40,
    )
);

$ratingTypeElement = array(
    'Select',
    'ratingType',
    array(
        'label' => 'Rating Type',
        'multiOptions' => array('rating_avg' => 'Average Ratings', 'rating_editor' => 'Only Editor Ratings', 'rating_users' => 'Only User Ratings', 'rating_both' => 'Both User and Editor Ratings'),
    )
);

$eventTypeElement = array(
    'Select',
    'showEventType',
    array(
        'label' => 'Select Events that you want to be shown in this block.',
        'multiOptions' => array('all' => 'All Events', 'upcoming' => 'Ongoing & Upcoming Events', 'onlyUpcoming' => 'Only Upcoming Events', 'past' => 'Only Past Events'),
        'value' => 'upcoming',
    )
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
        'title' => 'Event Profile: Overview',
        'description' => 'This widget forms the Overview tab on the Event Profile page and displays the overview of the event, which the owner has created using the editor in event dashboard. This widget should be placed in the Tabbed Blocks area of the Advanced Events - Event Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.overview-siteevent',
        'defaultParams' => array(
            'title' => 'Overview',
            'titleCount' => true,
        ),
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'showAfterEditorReview',
                    array(
                        'label' => 'Do you want to display this block even when the Overview is shown in "Event Profile: Editor Review / Overview / Description" widget?',
                        'multiOptions' => array(
                            2 => 'Yes, always display this block.',
                            1 => 'No, display this block when Overview is not displayed in that widget.',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'showComments',
                    array(
                        'label' => 'Enable Comments',
                        'description' => 'Do you want to enable comments in this widget? (If enabled, then users will be able to comment on the event being viewed. Note: If you enable this, then you should not place the ‘Event / Review Profile: Comments & Replies’ widget on Advanced Events - Event Profile page.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
            )
        )
    ),
    array(
        'title' => 'Event Profile: Description',
        'description' => 'This widget forms the Description tab on the Event Profile page and displays the description of the event. This widget should be placed in the Tabbed Blocks area of the Advanced Events - Event Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.description-siteevent',
        'defaultParams' => array(
            'title' => 'Description',
            'titleCount' => true,
        ),
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'showAlways',
                    array(
                        'label' => 'Do you want to display this block even when the Description is shown in "Advanced Events - Event Profile: Editor Review / Overview / Description" widget?',
                        'multiOptions' => array(
                            2 => 'Yes, always display this block.',
                            1 => 'No, display this block when Description is not displayed in that widget.',
                        ),
                        'value' => 1,
                    )
                ),
            )
        )
    ),
    array(
        'title' => 'Event Profile: Event Status',
        'description' => "Displays Status of the event being currently viewed. This widget should be placed on Advanced Events - Event Profile page.",
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.event-status',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'showButton',
                    array(
                        'label' => 'Show "Join Event / Request Invite" Button.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Categories Home: Categories Hierarchy for Events',
        'description' => 'Displays the Categories, Sub-categories and 3rd Level-categories of Events in an expandable form. Clicking on them will redirect the viewer to the list of events created in that category. Multiple settings are available to customize this widget. This widget should be placed on the Categories Home Page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.categories-home',
        'defaultParams' => array(
            'title' => 'Categories',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'showAllCategories',
                    array(
                        'label' => 'Do you want all the categories, sub-categories and 3rd level categories to be shown to the users even if they have 0 events in them?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'show2ndlevelCategory',
                    array(
                        'label' => 'Do you want to show sub-categories in this widget?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'show3rdlevelCategory',
                    array(
                        'label' => 'Do you want to show 3rd level category to the viewer? This settings will only work if you choose to show sub-categories from the setting above.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Select',
                    'orderBy',
                    array(
                        'label' => 'Categories Ordering',
                        'multiOptions' => array('category_name' => 'Alphabetical', 'cat_order' => 'Ordering as in categories tab'),
                        'value' => 'category_name',
                    ),
                ),
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => 'Show 3rd level categories of sub-categories in',
                        'multiOptions' => array('expanded' => 'Expanded View', 'collapsed' => 'Collapsed View'),
                        'value' => 'expanded',
                    )
                ),
                array(
                    'Radio',
                    'showCount',
                    array(
                        'label' => 'Show Events count along with Categories,Sub-categories and 3rd level categories.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
            )
        ),
    ),
 array(
        'title' => 'Event Calendar',
        'description' => 'Displays a calendar which highlights the dates having some Events. You can choose to show event count on a particular date from the Edit Settings of this widget.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.calendarview-siteevent',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
               array(
                    'Radio',
                    'siteevent_calendar_event_count',
                    array(
                        'label' => 'Show events count. (Selecting "Yes" here will display the events count with date in calendar.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '0',
                    )
                ),
                array(
                    'Radio',
                    'siteevent_calendar_event_count_type',
                    array(
                        'label' => 'Select the Events which you want to be shown in the event calendar.',
                        'multiOptions' => array(
                            1 => 'Events joined by current logged-in member.',
                            0 => 'All Events (If you place this widget on "Advanced Events - Event Manage Page", then all events belonging to current logged-in member will only be shown.)'
                        ),
                        'value' => '0',
                    )
                ),
                $detactLocationElement,
                $defaultLocationDistanceElement,
            ),
        ),
    ),
    array(
        'title' => 'Member Profile: Profile Events',
        'description' => 'Displays a member\'s events on their profile. This widget should be placed in the Tabbed Blocks area of Member Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.profile-siteevent',
        'defaultParams' => array(
            'title' => 'Events',
            'titleCount' => true,
            'eventInfo' => array("hostName","location","startDate"),
        ),
        'adminForm' => array(
            'elements' => array(
                $contentTypeElement,
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                $hiddenSubSubCatElement,
                $otherInfoElement,
                $eventTypeElement,
                $truncationLocationElement,
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation Limit',
                        'value' => 30,
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of Events to show)',
                        'value' => 5,
                    )
                ),
                $ratingTypeElement,
            ),
        ),
    ),
    array(
        'title' => 'Event Profile: Information (Profile Fields)',
        'description' => 'Displays the Questions added from the "Profile Fields" section in the Admin Panel. This widget should be placed in the Tabbed Blocks area of Advanced Events - Events Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.specification-siteevent',
        'defaultParams' => array(
            'title' => 'Information',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Event Profile: Event Information',
        'description' => 'Displays the category, tags, views, and other information about an event. This widget should be placed on Advanced Events - Event Profile page in the left column.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.information-siteevent',
        'defaultParams' => array(
            'title' => 'Information',
            'titleCount' => true,
            'showContent' => array("memberCount", "viewCount", "likeCount", "commentCount", "tags", "category", "rsvp", "ownerName", "venue", "price", "startDate", "endDate", "location", "addtodiary")
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'showContent',
                    array(
                        'label' => 'Select the information options that you want to be available in this block.',
                        'multiOptions' => array("hostName" => "Hosted By", "categoryLink" => "Category", "startDate" => "Start Date and Time", "endDate" => "End Date and Time", "ledBy" => "Led By", "price" => "Price", "venueName" => "Venue Name", "location" => "Location", "viewCount" => "Views", "likeCount" => "Likes", "commentCount" => "Comments", "memberCount" => "Guests", "reviewCount" => "Reviews", "tags" => "Tags", "rsvp" => "Members RSVPs", "joinLink" => "Join Event Button", "likeButton" => "Like Button", "socialShare" => "Social Share", "addtodiary" => "Add to Diary"),
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Event Profile: Map',
        'description' => 'This widget forms the Map tab on the Event Profile page. It displays the map showing the event position as well as the location details of the event.It should be placed in the Tabbed Blocks area of the Advanced Events - Event Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.location-siteevent',
        'defaultParams' => array(
            'title' => 'Map',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                $ratingTypeElement,
            )
        )
    ),
    array(
        'title' => 'Event Profile: Event Photos',
        'description' => 'This widget forms the Photos tab on the Event Profile page and displays the photos of the event. This widget should be placed in the Tabbed Blocks area of the Advanced Events - Event Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.photos-siteevent',
        'defaultParams' => array(
            'title' => 'Photos',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(Number of photos to show)',
                        'value' => 10,
                    )
                ),
            )
        )
    ),
    array(
        'title' => 'Event Profile: Event Videos',
        'description' => 'This widget forms the Videos tab on the Event Profile page and displays the videos of the event. This widget should be placed in the Tabbed Blocks area of the Advanced Events - Event Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.video-siteevent',
        'defaultParams' => array(
            'title' => 'Videos',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'count',
                    array(
                        'label' => 'Count',
                        'description' => '(number of videos to show)',
                        'value' => 5,
                    )
                ),
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation Limit',
                        'value' => 30,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
            )
        )
    ),
    array(
        'title' => 'My Events: User’s Events',
        'description' => 'Displays a list of all the events joined, owned, hosted, etc. of a user on your site. This widget should be placed on Advanced Events - My Events page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.manage-events-siteevent',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,          
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation Limit',
                        'value' => 25,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Browse Events',
        'description' => 'Displays a list of all the events on your site. This widget should be placed on Advanced Events - Browse Events page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.browse-events-siteevent',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'layouts_views' => array("2"),
            'layouts_order' => 2,
            'eventInfo' => array("hostName","location","startDate"),
            'truncationGrid' => 30
        ),
        'adminForm' => array(
            'elements' => array(
                $contentTypeElement,
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                $hiddenSubSubCatElement,
                array(
                    'MultiCheckbox',
                    'layouts_views',
                    array(
                        'label' => 'Choose the view types that you want to be available for events.',
                        'multiOptions' => array("1" => "List View", "2" => "Grid View"),
                        //'value' => 2,
                    ),
                ),
                array(
                    'Radio',
                    'layouts_order',
                    array(
                        'label' => 'Select a default view type for Events.',
                        'multiOptions' => array("1" => "List View", "2" => "Grid View"),
                        'value' => 2,
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For Grid View.',
                        'value' => '320',
                    )
                ),
                $otherInfoElement,
                

                array(
                    'Radio',
                    'orderby',
                    array(
                        'label' => 'Default ordering in Browse Events. (Note: Selecting multiple ordering will make your page load slower.)',
                        'multiOptions' => array(
                            'starttime' => 'All events in ascending order of start time.',
                            'view_count' => 'All events in descending order of views.',
                            'title' => 'All events in alphabetical order.',
                            'sponsored' => 'Sponsored events followed by others in ascending order of start time.',
                            'featured' => 'Featured events followed by others in ascending order of start time.',
                            'fespfe' => 'Sponsored & Featured events followed by Sponsored events followed by Featured events followed by others in ascending order of start time.',
                            'spfesp' => 'Featured & Sponsored events followed by Featured events followed by Sponsored events followed by others in ascending order of start time.',
                            'newlabel' => 'Events marked as New followed by others in ascending order of start time.',
                        ),
                        'value' => 'starttime',
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of Events to show)',
                        'value' => 10,
                    )
                ),
                $truncationLocationElement,
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation Limit',
                        'value' => 30,
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
                        'label' => 'Title Truncation Limit in Grid View',
                        'value' => 30,
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
//    array(
//        'title' => 'Popular / Recent / Random Events',
//        'description' => 'Displays Events based on the Popularity / Sorting Criteria and other settings that you choose for this widget. You can place this widget multiple times on a page with different popularity criterion chosen for each placement.',
//        'category' => 'Advanced Events',
//        'type' => 'widget',
//        'autoEdit' => true,
//        'name' => 'siteevent.events-siteevent',
//        'defaultParams' => array(
//            'title' => 'Events',
//            'titleCount' => true,
//            'statistics' => array("hostName","location","startDate"),
//            'viewType' => 'listview',
//            'columnWidth' => '180'
//        ),
//        'adminForm' => array(
//            'elements' => array(
//                $contentTypeElement,
//                $featuredSponsoredElement,
//                $eventTypeElement,
//                
//                array(
//                    'Radio',
//                    'viewType',
//                    array(
//                        'label' => 'Choose the View Type for events.',
//                        'multiOptions' => array(
//                            'listview' => 'List View',
//                            'gridview' => 'Grid View',
//                        ),
//                        'value' => 'listview',
//                    )
//                ),
//                array(
//                    'Text',
//                    'columnWidth',
//                    array(
//                        'label' => 'Column Width For Grid View.',
//                        'value' => '180',
//                    )
//                ),
//                array(
//                    'Text',
//                    'columnHeight',
//                    array(
//                        'label' => 'Column Height For Grid View.',
//                        'value' => '320',
//                    )
//                ),
//                array(
//                    'Select',
//                    'popularity',
//                    array(
//                        'label' => 'Popularity / Sorting Criteria',
//                        'multiOptions' => array_merge($popularity_options, array('random' => 'Random')),
//                        'value' => 'view_count',
//                    )
//                ),
//                array(
//                    'Select',
//                    'interval',
//                    array(
//                        'label' => 'Popularity Duration (This duration will be applicable to these Popularity / Sorting Criteria:  Most Liked, Most Commented, Most Rated and Recently Created.)',
//                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
//                        'value' => 'overall',
//                    )
//                ),
//                $categoryElement,
//                $subCategoryElement,
//                $hiddenCatElement,
//                $hiddenSubCatElement,
//                $hiddenSubSubCatElement,
//                $otherInfoElementGrid,
//                array(
//                    'Text',
//                    'itemCount',
//                    array(
//                        'label' => 'Count',
//                        'description' => '(number of Events to show)',
//                        'value' => 3,
//                    )
//                ),
//                $truncationLocationElement,
//                array(
//                    'Text',
//                    'truncation',
//                    array(
//                        'label' => 'Title Truncation Limit',
//                        'value' => 30,
//                        'validators' => array(
//                            array('Int', true),
//                            array('GreaterThan', true, array(0)),
//                        ),
//                    )
//                ),
//                $ratingTypeElement,
//                $detactLocationElement,
//                $defaultLocationDistanceElement,
//            ),
//        ),
//    ),
    array(
        'title' => 'Recently Viewed by Users',
        'description' => 'Displays events that have been recently viewed by Users of your site. Multiple settings are available for this widget in its Edit section.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.recently-viewed-siteevent',
        'defaultParams' => array(
            'title' => 'Recently Viewed By Friends',
            'titleCount' => true,
            'eventInfo' => array("hostName","location","startDate"),
        ),
        'adminForm' => array(
            'elements' => array(
                $contentTypeElement,
                $featuredSponsoredElement,
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                $hiddenSubSubCatElement,
                array(
                    'Radio',
                    'show',
                    array(
                        'label' => 'Show recently viewed events of:',
                        'multiOptions' => array(
                            '1' => 'Currently logged-in member’s friends.',
                            '0' => 'Currently logged-in member.',
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => 'Choose the View Type for events.',
                        'multiOptions' => array(
                            'listview' => 'List View',
                            'gridview' => 'Grid View',
                        ),
                        'value' => 'listview',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For Grid View.',
                        'value' => '320',
                    )
                ),
                $otherInfoElementGrid,
                $truncationLocationElement,
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation Limit',
                        'value' => 30,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    ),
                ),
                array(
                    'Text',
                    'count',
                    array(
                        'label' => 'Count',
                        'description' => '(number of Events to show)',
                        'value' => 10,
                    )
                ),
                $ratingTypeElement,
            ),
        ),
    ),
    array(
        'title' => 'Message for Zero Events',
        'description' => 'Displays a message to users when there are no Events. This widget should be placed in the top of the middle column of Advanced Events - Events Home page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.zeroevent-siteevent',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => 'Content Profile: Like Button for Content',
        'description' => 'This is the Like Button to be placed on the Content Profile page. It enables users to Like the content being currently viewed. The best place to put this widget is right above the Tabbed Blocks on the Content Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'seaocore.like-button',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => 'Event Profile: "Write a Review" Button',
        'description' => 'Displays a "Write a Review" button on Advanced Events - Event Profile page. When clicked, users will be redirected to write a review form for the event being viewed.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.review-button',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    array(
        'title' => 'Event Profile: Event Discussions',
        'description' => 'This widget forms the Discussions tab on the Advanced Events - Event Profile page and displays the discussions of the event. This widget should be placed in the Tabbed Blocks area of the Advanced Events - Event Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.discussion-siteevent',
        'defaultParams' => array(
            'title' => 'Discussions',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Event Profile: Related Events',
        'description' => 'Displays a list of all events related to the event being viewed. The related events are shown based on the tags and top-level category of the event being viewed. You can choose the related event criteria from the Edit Settings. This widget should be placed on the Advanced Events - Event Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.related-events-view-siteevent',
        'defaultParams' => array(
            'title' => 'More Events in %s',
            'titleCount' => true,
            'eventInfo' => array("hostName","location","startDate"),
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'title',
                    array(
                        'label' => 'Title',
                        'description' => "Enter below the format in which you want to display the title of the widget. (Note: To display category's name on event profile page, enter title as: More Events in %s. 'More Events in %s' will only work if you choose \"Events associated with same \'Categories'\" option in below setting. )",
                        'value' => "More Events in %s",
                    )
                ),
                $contentTypeElement,
                array(
                    'Radio',
                    'related',
                    array(
                        'label' => 'Choose which all Events should be displayed here as Events related to the current Event.',
                        'multiOptions' => array(
                            'tags' => "Events having same tag. (Note: 'Tags Field' should be enabled from Global Settings.)",
                            'categories' => 'Events associated with same \'Categories\'.'
                        ),
                        'value' => 'categories',
                    )
                ),
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => 'Choose the View Type for events.'
                        ,
                        'multiOptions' => array(
                            'listview' => 'List View',
                            'gridview' => 'Grid View',
                        ),
                        'value' => 'listview',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For Grid View.',
                        'value' => '320',
                    )
                ),
                $eventTypeElement,
                
                $otherInfoElementGrid,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of Events to show)',
                        'value' => 5,
                    )
                ),
                $truncationLocationElement,
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation Limit',
                        'value' => 30,
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
    array(
        'title' => 'Event Profile: Editor Review / Overview / Description',
        'description' => "This widget forms a tab on the Advanced Events - Event Profile page which displays Editor Review / Overview / Description of the event. If Editor Review is written, then the Editor Review will be shown in this block, otherwise Overview of the event will display. If Overview is also not written, then the description of the event will be shown. Multiple settings are available to customize this widget. This widget should be placed in Tabbed Blocks area of the Advanced Events - Event Profile page.",
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.editor-reviews-siteevent',
        'autoEdit' => true,
        'defaultParams' => array(
            'titleEditor' => "Review",
            'titleOverview' => "Overview",
            'titleDescription' => "Description",
            'titleCount' => "",
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'titleEditor',
                    array(
                        'label' => 'Title for Editor Review',
                        'value' => "Review",
                    )
                ),
                array(
                    'Text',
                    'titleOverview',
                    array(
                        'label' => 'Title for Overview',
                        'value' => "Overview",
                    )
                ),
                array(
                    'Text',
                    'titleDescription',
                    array(
                        'label' => 'Title for Description',
                        'value' => "Description",
                    )
                ),
                array(
                    'Hidden',
                    'title',
                    array()
                ),
                array(
                    'Radio',
                    'show_slideshow',
                    array(
                        'label' => 'Show Slideshow',
                        'description' => 'Do you want to display event photos slideshow in this block? (If you select \'Yes\', then users will be able to select photos and a video to be displayed in this slideshow from Photos and Videos section respectively of their Event Dashboard. Note: If you enable this, then you should not place the \'Event Profile: Event Photos Slideshow\' widget on Advanced Events - Event Profile page.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Text',
                    'slideshow_height',
                    array(
                        'label' => 'Enter the height of the slideshow (in pixels).',
                        'value' => 400,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'slideshow_width',
                    array(
                        'label' => 'Enter the width of the slideshow (in pixels).',
                        'value' => 600,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Radio',
                    'showCaption',
                    array(
                        'label' => 'Do you want to show image description in this Slideshow?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'showButtonSlide',
                    array(
                        'label' => "Select the navigation type for this Slideshow.",
                        'multiOptions' => array(
                            2 => 'Show thumbnails of photos and videos.',
                            1 => 'Show bullet (circle) navigation.',
                            0 => 'Hide navigation.'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'mouseEnterEvent',
                    array(
                        'label' => "By which action do you want slides navigation to occur from thumbnails / small circles?",
                        'multiOptions' => array(
                            1 => 'Mouse-over',
                            0 => 'On-click'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Radio',
                    'thumbPosition',
                    array(
                        'label' => "Where do you want to show image thumbnails?",
                        'multiOptions' => array(
                            'bottom' => 'In the bottom of Slideshow',
                            'left' => 'In the left of Slideshow',
                            'right' => 'In the right of Slideshow',
                        ),
                        'value' => 'bottom',
                    )
                ),
                array(
                    'Radio',
                    'autoPlay',
                    array(
                        'label' => "Do you want the Slideshow to automatically start playing when Event Profile page is opened?",
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Text',
                    'slidesLimit',
                    array(
                        'label' => 'How many slides you want to show in slideshow?',
                        'value' => 20,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'captionTruncation',
                    array(
                        'label' => 'Truncation limit for slideshow description',
                        'value' => 200,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Radio',
                    'showComments',
                    array(
                        'label' => 'Enable Comments',
                        'description' => 'Do you want to enable comments in this widget? (If enabled, then users will be able to comment on the event being viewed. Note: If you enable this, then you should not place the ‘Event / Review Profile: Comments & Replies’ widget on Advanced Events - Event Profile page.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
            )
        )
    ),
    array(
        'title' => 'Editor / Member Profile: Profile Reviews',
        'description' => 'Displays a list of all the reviews written by the editors / members of your site whose profile is being viewed. From Edit settings of this widget, you can choose to show Editor reviews or User Reviews in this widget. This widget should be placed in the Tabbed Blocks area of Advanced Events - Editor Profile page or Member Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.editor-profile-reviews-siteevent',
        'autoEdit' => true,
        'defaultParams' => array(
            'title' => "Reviews",
            'titleCount' => "",
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'type',
                    array(
                        'label' => 'Review Type',
                        'description' => 'Choose the type of reviews that you want to display in this widget.',
                        'multiOptions' => array(
                            'user' => 'User Reviews',
                            'editor' => 'Editor Reviews'
                        ),
                        'value' => 'user',
                    ),
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of reviews to show)',
                        'value' => 10,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Editor / Member Profile: Comments & Replies',
        'description' => "Displays a list of all the comments and replies by the members on Events  and Reviews on your site. This widget should be placed in the Tabbed Blocks area of Advanced Events - Editor Profile page or Member Profile page.",
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.editor-replies-siteevent',
        'defaultParams' => array(
            'title' => "Replies",
            'titleCount' => "",
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of comments & replies to show)',
                        'value' => 5,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Editor Profile: Editor’s Member Profile Photo',
        'description' => 'Displays Editors’ member profile photo on their editor profile. This widget should be placed on Advanced Events - Editor Profile page in the right / left column.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.editor-photo-siteevent',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => 'Event Profile: User Reviews',
        'description' => 'This widget forms the User Reviews tab on the Advanced Events - Event Profile page and displays all the reviews written by the users of your site for the Event being viewed. This widget should be placed in the Tabbed Blocks area of the Advanced Events - Event Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.user-siteevent',
        'defaultParams' => array(
            'title' => "User Reviews",
            'titleCount' => "true",
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemReviewsCount',
                    array(
                        'label' => 'Number of user reviews to show',
                        'value' => 5,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Review Profile: Review View',
        'description' => 'Displays the main Review. You can configure various setting from Edit Settings of this widget. This widget should be placed on Advanced Events - Review Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.profile-review-siteevent',
        'defaultParams' => array(
            'title' => 'Reviews',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Event Profile: Breadcrumb',
        'description' => 'Displays breadcrumb of the event based on the categories. This widget should be placed on the Advanced Events - Event Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.list-profile-breadcrumb',
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => 'Event Profile: Event Information & Options',
        'description' => 'Displays event profile photo with event information and various action links that can be performed on the Events from their Profile page (edit, delete, tell a friend, share, etc.). You can manage the Action Links available in this widget from the Menu Editor section by choosing Advanced Events - Event Profile Page Options Menu. You can choose various information options from the Edit settings of this widget. This widget should be placed on the Advanced Events - Event Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.list-information-profile',
        'defaultParams' => array(
            'title' => '',
            'showContent' => array("memberCount", "postedDate", "postedBy", "viewCount", "likeCount", "commentCount", "photo", "tags", "location", "description", "title", "reviewCreate", "price", "startDate", "endDate")
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'showContent',
                    array(
                        'label' => 'Select the information options that you want to be available in this block.',
                        'multiOptions' => array_merge(array("title" => "Title", "postedDate" => "Posted Date", "hostName" => "Hosted By", "categoryLink" => "Category", "startDate" => "Start Date and Time", "endDate" => "End Date and Time", "ledBy" => "Led By", "price" => "Price", "photo" => "Photo", "featuredLabel" => "Featured Label", "sponsoredLabel" => "Sponsored Label", "newLabel" => "New Label", "tags" => "Tags", "description" => "Description", "reviewCreate" => "Write a review", "venueName" => "Venue Name", "location" => "Location", "viewCount" => "Views", "likeCount" => "Likes", "commentCount" => "Comments", "memberCount" => "Guests", "reviewCount" => "Reviews", "tags" => "Tags", "likeButton" => "Like Button"), $siteeventrepeat_settings),
                    ),
                ),
                array(
                    'Text',
                    'truncationDescription',
                    array(
                        'label' => "Enter the trucation limit for the Event Description. (If you want to show the full description, then enter '0'.)",
                        'value' => 300,
                    )
                ),
            )
        )
    ),
    array(
        'title' => 'Event Profile: Event Rating',
        'description' => 'This widget displays the overall rating given to the event by editors, member of your site and other users along with the rating parameters as configured by you from the Advanced Events section in the Admin Panel. You can choose who should be able to give review from the Admin Panel. Multiple settings are available to customize this widget. This widget should be placed in the left column on the Advanced Events - Event Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.overall-ratings',
        'defaultParams' => array(
            'title' => 'Reviews',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'show_rating',
                    array(
                        'label' => 'Select from below type of ratings to be displayed in this widget',
                        'multiOptions' => array(
                            'avg' => 'Combined Editor and User Rating',
                            'both' => 'Editor and User Ratings separately',
                            'editor' => 'Only Editor Ratings',
                        ),
                        'value' => 'avg',
                    ),
                ),
                array(
                    'Radio',
                    'ratingParameter',
                    array(
                        'label' => 'Do you want to show Rating Parameters in this widget?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
            ),
        )
    ),
    array(
        'title' => 'Event Profile: Guests',
        'description' => 'Displays guests of an event being currently viewed. This widget should be placed on the "Advanced Event: Event Profile" page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.profile-members',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Guests',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'event',
        ),
    ),
    array(
        'title' => 'Event Profile: Event RSVP',
        'description' => 'Displays options to users for RSVP`ing to an event on event`s profile.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.profile-rsvp',
        'requirements' => array(
            'subject' => 'event',
        ),
    ),
    array(
        'title' => "Event Profile: Event Host’s Information",
        'description' => 'Displays host of the event being currently viewed. From the Edit Settings of this widget you can choose various information options to be shown about the host. This widget can be placed in the right / left / right extended / left extended column of the Advanced Events - Event Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.profile-host-info',
        'requirements' => array(
            'subject' => 'event',
        ),
        'adminForm' => array(
            'elements' => array(               
                array(
                    'MultiCheckbox',
                    'showInfo',
                    array(
                        'label' => 'Choose the statistics that you want to be displayed for the Host in this block.',
                        'multiOptions' => array(
                            'totalevent' => 'Total Events hosted by the Host.',
                            'totalguest' => 'Number of guests who have joined Events hosted by the Host.',
                            'totalrating' => 'Ratings on the Events hosted by the Host.',
                            'hostDescription' => 'Host Description',
                        ),
                    )
                ),
            )
        )
    ),
    array(
        'title' => 'Event Profile: Announcements',
        'description' => 'Displays list of announcements posted by event owner for their Events. This widget should be placed on the Advanced Events - Event Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.profile-announcements-siteevent',
        'defaultParams' => array(
            'title' => 'Announcements',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'showTitle',
                    array(
                        'label' => 'Show announcement title.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Number of announcements to show',
                        'value' => 3,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Editor Profile: Similar Editors',
        'description' => 'Displays Editors similar to the Editors whose profile is being viewed. This widget should be placed on Advanced Events - Editor Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.editors-siteevent',
        'defaultParams' => array(
            'title' => 'Site Editors',
        ),
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of editors to show)',
                        'value' => 5,
                    )
                ),
                array(
                    'Radio',
                    'superEditor',
                    array(
                        'label' => 'Show Super Editor.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Review Profile: Breadcrumb',
        'description' => 'Displays breadcrumb of the review based on the categories and the event to which it belongs. This widget should be placed on the Advanced Events - Review Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.profile-review-breadcrumb-siteevent',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => 'Browse Diaries',
        'description' => 'Displays a list of diaries created by adding events on your site. This widget should be placed on "Advanced Events - Browse Diaries" page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.diary-browse',
        'defaultParams' => array(
            'title' => '',
            'statisticsDiary' => array("viewCount", "entryCount"),
            'listThumbsValue' => 2,
        ),
        'adminForm' => array(
            'elements' => array(
                $statisticsDiaryElement,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Number of diaries to show per page',
                        'value' => 10,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Diary Profile: Added Events',
        'description' => 'Displays a list of all the events added in the diary being viewed. This widget should be placed on the Advanced Events - Diary Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.diary-profile-items',
        'autoEdit' => true,
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'eventInfo' => array("likeCount","memberCount"),
            'statisticsDiary' => array("viewCount", "entryCount"),
            'show_buttons' => array("diary", "comment", "like", "share")
        ),
        'adminForm' => array(
            'elements' => array(
                $contentTypeElement,
                array(
                    'Radio',
                    'postedby',
                    array(
                        'label' => 'Show posted by option. (Selecting "Yes" here will display the member\'s name who has created the diary.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1',
                    )
                ),
                $otherInfoElement,
                $statisticsDiaryElement,
                $truncationLocationElement,
                array(
                    'Text',
                    'truncationDescription',
                    array(
                        'label' => "Enter the trucation limit for the Event Description. (If you want to hide the description, then enter '0'.)",
                        'value' => 100,
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of Events to show)',
                        'value' => 10,
                    )
                ),
                $ratingTypeElement,
            ),
        ),
    )
);

$video_widgets = array(
    array(
        'title' => 'Video View: Event Video',
        'description' => "Displays event video being currently viewed. This widget should be placed on the Advanced Events - Video View page.",
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.video-content',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
     array(
        'title' => 'Ajax based main Events Home widget',
        'description' => "Contains multiple Ajax based tabs showing Recently Posted, Popular, Most Reviewed, Featured and Sponsored events in a block in separate ajax based tabs respectively. You can configure various settings for this widget from the Edit settings.",
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.recently-popular-random-siteevent',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
            'eventInfo' => array("hostName","location","startDate"),
            'layouts_views' => array("listview", "gridview"),
//            'ajaxTabs' => array("upcoming", "mostZZZreviewed", "mostZZZpopular", "featured", "sponsored", "mostZZZjoined", "thisZZZmonth", "thisZZZweek", "thisZZZweekend", "today"),
            'showContent' => array("price", "location"),
        ),
        'adminForm' => array(
            'elements' => array(
                $contentTypeElement,
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                $hiddenSubSubCatElement,
                $otherInfoElement,
                $eventTypeElement,
                array(
                    'MultiCheckbox',
                    'layouts_views',
                    array(
                        'label' => $view->translate('Choose the view types that you want to be available for events.'),
                        'multiOptions' => array("1" => $view->translate("List View"), "2" => $view->translate("Grid View")),
                        'value'=>array("2")
                    ),
                ),
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => $view->translate('Choose the View Type for events.'),
                        'multiOptions' => array(
                            'listview' => $view->translate('List View'),
                            'gridview' => $view->translate('Grid View'),
                        ),
                        'value' => 'gridview',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For Grid View.',
                        'value' => '320',
                    )
                ),
                array(
                    'Select',
                    'ajaxTabs',
                    array(
                        'label' => 'Select Events that you want to be shown in this block.',
                        'multiOptions' => array("upcoming" => "Upcoming (Only upcoming events will be shown in this tab, irrespective of the option chosen in above drop-down)", "mostZZZreviewed" => "Most Reviewed", "mostZZZpopular" => "Most Popular", "featured" => "Featured", "sponsored" => "Sponsored", "mostZZZjoined" => "Most Joined", "thisZZZmonth" => "This Month", "thisZZZweek" => "This Week", "thisZZZweekend" => "This Weekend", "today" => "Today")
                    )
                ),
                
                array(
                    'Text',
                    'limit',
                    array(
                        'label' => 'Count',
                        'description' => '(number of Events to show)',
                        'value' => 10,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    ),
                ),
                $truncationLocationElement,
                array(
                    'Text',
                    'truncationList',
                    array(
                        'label' => 'Title Truncation Limit in List View',
                        'value' => 30,
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
                        'label' => 'Title Truncation Limit in Grid View',
                        'value' => 30,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                $ratingTypeElement,
                $detactLocationElement,
                $defaultLocationDistanceElement,
            )
        ),
    ),
    array(
        'title' => 'Host / Member / Content Profile: Hosted Events',
        'description' => 'This widget displays all the events hosted by the host (You can choose who can host events from the Global Settings section of Advanced Events Plugin.). Multiple settings are available in the Edit Settings section of this widget. This widget can be placed on the Member Profile page, Advanced Events - Host Profile Page or the profile page of host as configured by you from the Global Settings section of Advanced Events Plugin.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.host-events',
        'autoEdit' => true,
        'defaultParams' => array(
            'title' => 'Events',
            'titleCount' => true,
            'eventInfo' => array("hostName","location","startDate"),
        ),
        'adminForm' => array(
            'elements' => array(
                $contentTypeElement,
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                $hiddenSubSubCatElement,
                $otherInfoElement,
                
             $truncationLocationElement,
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation Limit',
                        'value' => 35,
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of Events to show)',
                        'value' => 5,
                    )
                ),
                $ratingTypeElement,
            ),
        )
    ),
    array(
        'title' => 'Content Type: Profile Events',
        'description' => "Displays a list of events created in the content being currently viewed. This widget should be placed on content's Profile page.",
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.contenttype-events',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'eventInfo' => array("hostName","location","startDate"),
        ),
        'adminForm' => array(
            'elements' => array(
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                $hiddenSubSubCatElement,
                $otherInfoElement,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of Events to show)',
                        'value' => 5,
                    )
                ),
                $truncationLocationElement,
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation Limit',
                        'value' => 30,
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
    array(
        'title' => 'Host Profile: Information',
        'description' => 'Displays the title, total events hosted, description, etc about the host (where host is ‘other individual or organization’). This widget should be placed on the ‘Advanced Events - Host Profile Page’.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.organizer-info',
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'showInfo',
                    array(
                        'label' => 'Choose the statistics that you want to be displayed for the Host in this block.',
                        'multiOptions' => array("title" => "Title", "description" => "Host Description", "photo" => "Photo", 'creator' => 'Creator', 'options' => "Edit / Remove Options", 'totalevent' => 'Total Events hosted by the Host.', 'totalguest' => 'Number of guests who have joined Events hosted by the Host.', 'totalrating' => 'Ratings on the Events hosted by the Host.'),
                    ),
                )
            ))
    ),
    array(
        'title' => 'Discussion Topic View: Discussion Topic',
        'description' => "Displays event discussion topic being currently viewed. This widget should be placed on the ‘Advanced Events - Discussion Topic View’ page.",
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.discussion-content',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'postorder',
                    array(
                        'label' => 'Select the order of posts to be displayed in this block.',
                        'multiOptions' => array(
                            1 => 'Newer to older',
                            0 => 'Older to newer'
                        ),
                        'value' => 0,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Content Profile: Follow Button',
        'description' => 'This is the Follow Button to be placed on the Content Profile page. It enables users to Follow the content being currently viewed.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'seaocore.seaocore-follow',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
);

if (empty($type_video)) {
    $final_array = array_merge($final_array, $video_widgets);
}

if (!empty($ads_Array)) {
    $final_array = array_merge($final_array, $ads_Array);
}

return $final_array;
