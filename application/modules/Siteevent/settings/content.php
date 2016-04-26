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

$widgetDescription = Engine_Api::_()->siteevent()->isTicketBasedEvent() ? ' [Note: "Join Button" will be replaced by "Book Now", RSVP & Guest details will not display, if "Tickets" setting is enabled in "Ticket Settings" section.]' : '';
$slug_plural = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.slugplural', 'event-items');
$type_video = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.show.video');
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
$url = $view->url(array(), "siteevent_general", true);
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

$showShareElement = array(
    'Select',
    'shareOptions',
    array(
        'label' => 'Do you want to show share links in this widget?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => '0'
    )
);

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

$showViewMoreContent = array(
    'Select',
    'show_content',
    array(
        'label' => 'What do you want for view more content?',
        'description' => '',
        'multiOptions' => array(
            '1' => 'Pagination',
            '2' => 'Show View More Link at Bottom',
            '3' => 'Auto Load Events on Scrolling Down'),
        'value' => 2,
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
        'eventType',
        array(
            'label' => 'Event Type',
            'multiOptions' => $contentTypeArray,
        ),
        'value' => '',
    );
} else {
    $contentTypeElement = array(
        'Hidden',
        'eventType',
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

//CHECK IF FACEBOOK PLUGIN IS ENABLE
$fbmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebookse');

if (!empty($fbmodule) && !empty($fbmodule->enabled) && $fbmodule->version > '4.2.7p1') {
    $show_like_button = array(
        '1' => 'Yes, show SocialEngine Core Like button',
        '2' => 'Yes, show Facebook Like button',
        '0' => 'No',
    );
    $default_value = 2;
} else {
    $show_like_button = array(
        '1' => 'Yes, show SocialEngine Core Like button',
        '0' => 'No',
    );
    $default_value = 1;
}

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

if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('birthday')) {
   $actionLinksElement = array(
    'MultiCheckbox',
    'actionLinks',
    array(
        'label' => 'Choose the action links that you want to be displayed for the Events in this block.',
        'multiOptions' => array("events" => "Events", "diaries" => "Diaries", "createNewEvent" => "Create New Event", "invites" => "Invites", "birthday" => "Birthday"),
    ),
);
$actionLinksElementValue = array('events', 'diaries', 'createNewEvent', 'invites', 'birthday');
} else {
    $actionLinksElement = array(
    'MultiCheckbox',
    'actionLinks',
    array(
        'label' => 'Choose the action links that you want to be displayed for the Events in this block.',
        'multiOptions' => array("events" => "Events", "diaries" => "Diaries", "createNewEvent" => "Create New Event", "invites" => "Invites"),
    ),
);
$actionLinksElementValue = array('events', 'diaries', 'createNewEvent', 'invites');
}

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

$titlePositionElement = array(
    'Radio',
    'titlePosition',
    array(
        'label' => 'Do you want "Event Title" to be displayed inside the Grid View?',
        'multiOptions' => array(1 => 'Yes', 0 => 'No'),
        'value' => 1,
    ),
);

$descriptionPositionElement = array(
    'Radio',
    'descriptionPosition',
    array(
        'label' => 'Do you want "Event Description" to be displayed in this block?',
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

$eventProfileButtonsArray = array("signIn" => "Sign In", "signUp" => "Sign Up", "uploadPhotos" => "Upload Photos", "uploadVideos" => "Upload Videos");

if (Engine_Api::_()->siteevent()->hasTicketEnable()){
  $eventProfileButtonsArray = array_merge($eventProfileButtonsArray, array('mytickets' => 'My Tickets'));
}
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
            'loaded_by_ajax' => 1
        ),
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'loaded_by_ajax',
                    array(
                        'label' => 'Widget Content Loading',
                        'description' => 'Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
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
            'loaded_by_ajax' => 1
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
                array(
                    'Radio',
                    'loaded_by_ajax',
                    array(
                        'label' => 'Widget Content Loading',
                        'description' => 'Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)',
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
        'title' => 'Event Profile: Event Status',
        'description' => "Displays Status of the event being currently viewed. This widget should be placed on Advanced Events - Event Profile page.".$widgetDescription,
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
                        'value' => 0,
                    )
                ),
                array(
                    'Radio',
                    'showEventFullStatus',
                    array(
                        'label' => 'Do you want to show "Event is Full" message.',
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
        'title' => 'Event Profile: Event Archives',
        'description' => 'Displays the month-wise archives for the events posted on your site by the event owner which is being currently viewed. This widget should be placed on the Advanced Events- Event Profile Page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.archives-siteevent',
        'defaultParams' => array(
            'title' => 'Archives',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => 'Navigation Tabs',
        'description' => "This widget displays the navigation tabs for 'Advanced Events Plugin' having links of Events Home, Browse Events etc.",
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.navigation-siteevent',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'navigationTabTitle',
                    array(
                        'label' => 'Navigation Bar Heading',
                        'value' => 'Events'
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Categories Hierarchy for Events (sidebar)',
        'description' => 'Displays the Categories, Sub-categories and 3rd Level-categories of Events in an expandable form. Clicking on them will redirect the viewer to Advanced Events - Browse Events page displaying the list of events created in that category. Multiple settings are available to customize this widget. It is recommended to place this widget in \'Full Width\'.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.categories-sidebar-siteevent',
        'defaultParams' => array(
            'title' => 'Categories',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
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
        'title' => 'Categories Hierarchy for Events',
        'description' => 'Displays the Categories, Sub-categories and 3<sup>rd</sup> Level-categories of events in an expandable form. Clicking on them will redirect the viewer to the list of events created in that category. Multiple settings are available to customize this widget. It is recommended to place this widget in the middle column of the Advanced Events - Events Home page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.categories-middle-siteevent',
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
                        'value' => 0,
                    )
                ),
                array(
                    'Radio',
                    'showEventType',
                    array(
                        'label' => "Select Events belonging to which you want to show categories in this block. (This setting will only work if you have selected 'No' in the above setting.)",
                        'multiOptions' => array('all' => 'All Events', 'upcoming' => 'Only Upcoming Events'),
                        'value' => 'upcoming',
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
        'title' => 'Sponsored Categories',
        'description' => 'Displays the Sponsored categories, sub-categories and 3<sup>rd</sup> level-categories. You can make categories as Sponsored from "Categories" section of Admin Panel.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.categories-sponsored',
        'defaultParams' => array(
            'title' => 'Sponsored Categories',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of categories to show. Enter 0 for displaying all categories.)',
                        'value' => 0,
                    )
                ),
                array(
                    'Radio',
                    'showIcon',
                    array(
                        'label' => 'Do you want to display the icons along with the categories in this block?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Member Profile: Profile Events',
        'description' => 'Displays a member\'s events on their profile. This widget should be placed in the Tabbed Blocks area of Member Profile page.'.$widgetDescription,
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.profile-siteevent',
        'defaultParams' => array(
            'title' => 'Events',
            'titleCount' => true
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
                    'Radio',
                    'showEventFilter',
                    array(
                        'label' => 'Show Event Filters',
                        'description' => 'Do you want to show Events Filter Option',
                        'multiOptions' => array('1' => 'Yes', '0' => 'No'),
                    )
                ),
                array(
                    'MultiCheckbox',
                    'eventFilterTypes',
                    array(
                        'label' => 'Choose the filtering sections that you want to be available in this block.',
                        'multiOptions' => array("joined" => "Attending, Maybe Attending and Not Attending", "ledOwner" => "Leading", "host" => "Hosting", "liked" => "Liked", "userreviews" => "Rated"),
                    ),
                ),
                array(
                    'Radio',
                    'eventtypesall',
                    array(
                        'label' => 'Choose the Events to be available in the "All" section of events filtering available in this widget. [Events belonging to the member whose profile is currently being viewed will only be shown.]',
                        'multiOptions' => array(
                            'ownerledby' => 'Events "Owned" and "Led by"',
                            'ownerledbyjoined' => 'Events "Owned", "Led by" and "Joined by"',
                            'ownerledbyjoinedhost' => 'Events "Owned", "Led by", "Joined by", and "Hosted by"',
                        ),
                        'value' => 'ownerledbyjoinedhost',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'typesOfViews',
                    array(
                        'label' => 'Choose the view types that you want to be available for events on this widget.',
                        'multiOptions' => array(
                            'listview' => 'List View',
                            'gridview' => 'Grid View',
                            'mapview' => 'Map View',
                        ),
                    )
                ),
                array(
                    'Radio',
                    'layoutViewType',
                    array(
                        'label' => 'Choose the View Type for events.',
                        'multiOptions' => array(
                            'listview' => 'List View',
                            'gridview' => 'Grid View',
                            'mapview' => 'Map View',
                        ),
                        'value' => 'listview',
                    )
                ),
                $titlePositionElement,
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => 'Column Width For Grid View.',
                        'value' => '180',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For Grid View.',
                        'value' => '216',
                    )
                ),
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
                    'truncationGrid',
                    array(
                        'label' => 'Title Truncation Limit in Grid View',
                        'value' => 90,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
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
                array(
                    'Radio',
                    'showEventCount',
                    array(
                        'label' => 'Show Events Count in Event Tab',
                        'description' => 'Do you want to show Events Count in Event Tab.',
                        'multiOptions' => array('1' => 'Yes', '0' => 'No'),
                    )
                ),
                array(
                    'Radio',
                    'showEventUpcomingPastCount',
                    array(
                        'label' => 'Show Upcoming & Past Count in Event Tab',
                        'description' => 'Do you want to show Upcoming & Past Count in Event Tab.',
                        'multiOptions' => array('1' => 'Yes', '0' => 'No'),
                        'value' => 0,
                    )
                ),
                $ratingTypeElement,
                $showShareElement
            ),
        ),
    ),
    array(
        'title' => 'Popular Events Slideshow',
        'description' => 'Displays events based on the Popularity / Sorting Criteria and other settings configured by you in an attractive slideshow with interactive controls. You can place this widget multiple times on a page with different popularity criterion chosen for each placement.'.$widgetDescription,
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.slideshow-siteevent',
        'autoEdit' => 'true',
        'defaultParams' => array(
            'title' => 'Featured Events',
            'titleCount' => true
        ),
        'adminForm' => array(
            'elements' => array(
                $contentTypeElement,
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                $hiddenSubSubCatElement,
                $featuredSponsoredElement,
                $otherInfoElement,
                $eventTypeElement,
                array(
                    'Select',
                    'popularity',
                    array(
                        'label' => 'Popularity / Sorting Criteria',
                        'multiOptions' => array_merge($popularity_options, array('random' => 'Random')),
                        'value' => 'event_id',
                    )
                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => 'Popularity Duration (This duration will be applicable to these Popularity / Sorting Criteria:  Most Liked, Most Commented, Most Rated and Recently Created.)',
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
                array(
                    'Text',
                    'blockHeight',
                    array(
                        'label' => 'Enter the height of this block (in pixels).',
                        'value' => 195,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                $truncationLocationElement,
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation Limit',
                        'value' => 45,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Text',
                    'truncationDescription',
                    array(
                        'label' => 'Description Truncation Limit.[Note: Enter 0 to hide the description.]',
                        'value' => 150,
                        'validators' => array(
                            array('Int', true),
                        ),
                    )
                ),
                array(
                    'Text',
                    'count',
                    array(
                        'label' => 'Count',
                        'description' => '(number of events to show)',
                        'value' => 10,
                    )
                ),
                $ratingTypeElement,
                $detactLocationElement,
                $defaultLocationDistanceElement,
            ),
        ),
    ),
    array(
        'title' => 'Event Profile: Event Photos Slideshow',
        'description' => 'Displays a Video and Photos selected by the event owners from their Event dashboard in an attractive slideshow. (If you place this widget, then users will be able to select photos and a video to be displayed in this slideshow from Photos and Videos section respectively of their Event Dashboard. Note: If you place this widget, then you should disable the event photos slideshow setting available in the \'Event Profile: Editor Review / Overview / Description\' widget.) It should be placed on Advanced Events - Event Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.slideshow-list-photo',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
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
            )
        )
    ),
    array(
        'title' => 'Review / Editor Profile: Social Share Buttons',
        'description' => "Contains Social Sharing buttons and enables users to easily share Reviews / Editors' profiles on their favorite Social Networks. It is recommended to place this widget on the Advanced Events - Review Profile page or Advanced Events - Editor Profile page. You can customize the code for social sharing buttons from Global Settings of this plugin by adding your own code generated from: <a href='http://www.addthis.com' target='_blank'>http://www.addthis.com</a>",
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.socialshare-siteevent',
        'defaultParams' => array(
            'title' => 'Social Share',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => 'Event / Diary Profile: Share and Report Options',
        'description' => "Displays the various action link options to users viewing an event / diary (Report, Print, Share, etc). It also contains Social Sharing buttons to enable users to easily share events / diaries on their favourite Social Network. You can customize the code for social sharing buttons from Global Settings of this plugin by adding your own code generated from: <a href='http://www.addthis.com' target='_blank'>http://www.addthis.com</a>. You can manage the Action Links available in this widget from the Edit settings of this widget. This widget should be placed on the Advanced Events - Event Profile page or the Advanced Events - Diary Profile page.",
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.share',
        'autoEdit' => true,
        'defaultParams' => array(
            'title' => 'Share and Report',
            'titleCount' => true,
            'options' => array("siteShare", "friend", "report", "print", "socialShare"),
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'options',
                    array(
                        'label' => 'Select the options that you want to display in this block.',
                        'multiOptions' => array("siteShare" => "Site Share", "friend" => "Tell a Friend", "report" => "Report", 'print' => 'Print', 'socialShare' => 'Social Share'),
                    ),
                ),
                array(
                    'Radio',
                    'allowSocialSharing',
                    array(
                        'label' => 'Allow social sharing even after the event is ended. [Note: This setting will only work when this widget is placed on Advanced Events - Event Profile page.]',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 0,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Event Profile: Event Title',
        'description' => 'Displays the Title of the event. This widget should be placed on the Advanced Events - Event Profile page, in the middle column at the top.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.title-siteevent',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                $eventinfoSetting
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
            'loaded_by_ajax' => 1
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'loaded_by_ajax',
                    array(
                        'label' => 'Widget Content Loading',
                        'description' => 'Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                )
            )
        )
    ),
    array(
        'title' => 'Event / Review Profile: Quick Information (Profile Fields)',
        'description' => 'Displays the Questions enabled to be shown in this widget from the \'Profile Fields\' section in the Admin Panel. This widget should be placed in the right / left column on the Advanced Events - Review Profile page or Advanced Events - Events Profile.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.quick-specification-siteevent',
        'defaultParams' => array(
            'title' => 'Quick Information',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'show_specificationlink',
                    array(
                        'label' => 'Show \'More Information\' link. (Note: This link will only be displayed, if you have placed \'Event Profile: Information\' widget in the Tabbed Blocks area of the Advanced Events - Event Profile page as users will be redirected to this tab on clicking the link.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Text',
                    'show_specificationtext',
                    array(
                        'label' => 'Please enter the text below which you want to display in place of "More Information" link in this widget.',
                        'value' => 'More Information',
                    ),
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Number of information to show',
                        'value' => 5,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Event Profile: Event Information',
        'description' => 'Displays the category, tags, views, and other information about an event. This widget should be placed on Advanced Events - Event Profile page in the left column.'.$widgetDescription,
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
                        'multiOptions' => array("hostName" => "Hosted By", "categoryLink" => "Category", "startDate" => "Start Date and Time", "endDate" => "End Date and Time", "ledBy" => "Led By", "price" => "Price", "venueName" => "Venue Name", "location" => "Location", "directionLink" => "Get Directions Link (Dependent on Location)", "viewCount" => "Views", "likeCount" => "Likes", "commentCount" => "Comments", "memberCount" => "Guests", "reviewCount" => "Reviews", "tags" => "Tags", "rsvp" => "Members RSVPs", "joinLink" => "Join Event Button", "likeButton" => "Like Button", "socialShare" => "Social Share", "addtodiary" => "Add to Diary"),
                    ),
                ),
                array(
                    'Radio',
                    'allowSocialSharing',
                    array(
                        'label' => 'Allow social sharing even after the event is ended. [Note: This setting will only work when this widget is placed on Advanced Events - Event Profile page.]',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 0,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Event Profile: Event Profile Photo',
        'description' => 'Displays the profile photo of an event. This widget must be placed on the Advanced Events - Event Profile page in the left / right column.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.mainphoto-siteevent',
        'defaultParams' => array(
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'ownerName',
                    array(
                        'label' => 'Do you want to display event owner’s name in this widget?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Radio',
                    'featuredLabel',
                    array(
                        'label' => 'Do you want to show Featured Label or not?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'sponsoredLabel',
                    array(
                        'label' => 'Do you want to show Sponsored Label or not?',
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
        'title' => "Event Profile: Event Owner's Photo",
        'description' => "Displays the Event owner's photo with owner's name. This widget should be placed in the right column of Advanced Events - Event Profile Page.",
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.event-owner-photo',
        'requirements' => array(
            'subject' => 'siteevent_event',
        ),
        'adminForm' => array(
            'elements' => array(
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
        'title' => 'Event Profile: Left / Right Column Map',
        'description' => 'This widget displays the map showing location of the Event being currently viewed. It should be placed in the left / right column of the Advanced Events - Event Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.location-sidebar-siteevent',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'showContent' => array("startEndDates", "addToCalendar")
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'showContent',
                    array(
                        'label' => 'Select the information options that you want to be available in this block.',
                        'multiOptions' => array("startDate" => "Start Date and Time", "endDate" => "End Date and Time"),
                    ),
                ),
                array(
                    'Text',
                    'height',
                    array(
                        'label' => 'Enter the height of the map (in pixels).',
                        'value' => 200,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Event Profile: Event Options',
        'description' => 'Displays the various action link options to users viewing an Event. This widget should be placed on the Advanced Events - Event Profile page in the left column, below the event profile photo.'.$widgetDescription,
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.options-siteevent',
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
        'title' => 'Event Profile: Event Photos',
        'description' => 'This widget forms the Photos tab on the Event Profile page and displays the photos of the event. This widget should be placed in the Tabbed Blocks area of the Advanced Events - Event Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.photos-siteevent',
        'defaultParams' => array(
            'title' => 'Photos',
            'titleCount' => true,
            'loaded_by_ajax' => 1
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'loaded_by_ajax',
                    array(
                        'label' => 'Widget Content Loading',
                        'description' => 'Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)',
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
                        'label' => 'Count',
                        'description' => '(Number of photos to show)',
                        'value' => 20,
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
            'loaded_by_ajax' => 1
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'loaded_by_ajax',
                    array(
                        'label' => 'Widget Content Loading',
                        'description' => 'Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Text',
                    'count',
                    array(
                        'label' => 'Count',
                        'description' => '(number of videos to show)',
                        'value' => 10,
                    )
                ),
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation Limit',
                        'value' => 35,
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
        'title' => 'Create New Event Link',
        'description' => 'Displays the link to Create New Event.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.newevent-siteevent',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'quick',
                    array(
                        'label' => 'Do you want to enable users to quickly create their events? (If selected "Yes", then event creation form will be open in the lightbox when users click on this link.)',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No'
                        ),
                        'value' => '1',
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Popular Locations',
        'description' => 'Displays the popular locations of events with frequency.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.popularlocation-siteevent',
        'defaultParams' => array(
            'title' => 'Popular Locations',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'eventType',
                    array(
                        'label' => 'Select Events belonging to which you want to show popular locations in this block.',
                        'multiOptions' => array(
                            'all' => 'All Events',
                            'upcoming' => 'Only Upcoming Events'
                        ),
                        'value' => 'upcoming',
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of locations to show)',
                        'value' => 10,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Popular Event Tags',
        'description' => "Displays popular tags. You can choose to display tags based on their frequency / alphabets from the Edit Settings of this widget. This widget should be placed on the 'Advanced Events - Event Profile' / 'Advanced Events - Browse Events' / 'Advanced Events - Events Home' pages.",
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.tagcloud-siteevent',
        'defaultParams' => array(
            'title' => 'Popular Tags (%s)',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'title',
                    array(
                        'label' => 'Title',
                        'description' => "Enter below the format in which you want to display the title of the widget. (Note: To display count of tags on events browse and home pages, enter title as: Title (%s). To display event owner’s name on event profile page, enter title as: %s's Tags.)",
                        'value' => 'Popular Tags (%s)',
                    )
                ),
                array(
                    'Radio',
                    'eventType',
                    array(
                        'label' => 'Select Events belonging to which you want to show popular tags in this block.',
                        'multiOptions' => array(
                            'all' => 'All Events',
                            'upcoming' => 'Only Upcoming Events'
                        ),
                        'value' => 'upcoming',
                    )
                ),
                array(
                    'Radio',
                    'orderingType',
                    array(
                        'label' => 'Do you want to show popular event tags in alphabetical order?',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No'
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of tags to show. Enter 0 for displaying all tags.)',
                        'value' => 25,
                    )
                ),
                
              array(
                    'Radio',
                    'loaded_by_ajax',
                    array(
                        'label' => 'Widget Content Loading',
                        'description' => 'Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)',
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
        'title' => 'Event Profile: Owner’s / Host’s Events',
        'description' => 'Displays a list of other events owned by the event owner. This widget should be placed on Advanced Events - Event Profile page.'.$widgetDescription,
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.userevent-siteevent',
        'defaultParams' => array(
            'title' => "%s's Events",
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'title',
                    array(
                        'label' => 'Title',
                        'description' => "Enter below the format in which you want to display the title of the widget. (Note: To display event owner’s name on event profile page, enter title as: %s's Events.)",
                        'value' => "%s's Events",
                    )
                ),
                array(
                    'Radio',
                    'show',
                    array(
                        'label' => 'Show other events of:',
                        'multiOptions' => array(
                            'owner' => 'Owner of Event being currently viewed.',
                            'host' => 'Host of Event being currently viewed.',
                        ),
                        'value' => 'owner',
                    )
                ),
                $contentTypeElement,
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                $hiddenSubSubCatElement,
                $otherInfoElementGrid,
                $eventTypeElement,
                $titlePositionElement,
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
                    'columnWidth',
                    array(
                        'label' => 'Column Width For Grid View.',
                        'value' => '180',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For Grid View.',
                        'value' => '328',
                    )
                ),
                array(
                    'Text',
                    'count',
                    array(
                        'label' => 'Count',
                        'description' => '(number of Events to show)',
                        'value' => 3,
                    )
                ),
                $truncationLocationElement,
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation Limit',
                        'value' => 24,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                $ratingTypeElement,
                array(
                    'Radio',
                    'networkBased',
                    array(
                        'label' => 'Do you want to display events belonging to same network as joined by the user currently viewing this widget?',
                        'multiOptions' => array(
                            '1' => "Yes",
                            '0' => 'No'
                        ),
                        'value' => '0',
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Event Profile: About Event',
        'description' => 'Displays the About Event information for events as entered by event owners. This widget should be placed on the Advanced Events - Event Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.write-siteevent',
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
        'title' => 'Browse Events: Breadcrumb',
        'description' => 'Displays breadcrumb based on the categories searched from the search form widget. This widget should be placed on Advanced Events - Browse Events page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.browse-breadcrumb-siteevent',
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
        'title' => 'My Events: User’s Events',
        'description' => 'Displays a list of all the events joined, owned, hosted, etc. of a user on your site. This widget should be placed on Advanced Events - My Events page.'.$widgetDescription,
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.manage-events-siteevent',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'layouts_order' => 1,
            'statistics' => array("viewCount", "likeCount", "commentCount", "memberCount", "reviewCount"),
            'columnWidth' => '180',
            'actionLinks' => $actionLinksElementValue
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'quick',
                    array(
                        'label' => 'Do you want to enable users to quickly create their events? (If selected "Yes", then event creation form will be open in the lightbox when users click on this link.)',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No'
                        ),
                        'value' => '1',
                    )
                ),
                $statisticsElement,
                $actionLinksElement,
                array(
                    'Radio',
                    'postedby',
                    array(
                        'label' => "Show led by option. (Selecting 'Yes' here will display the member's name who has created the event.)",
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'dateTimeDisplayed',
                    array(
                        'label' => "Date & Time should be shown in different lines or should be shown as combined. (Selecting 'Yes' here will display the Date & Time in different lines.)",
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'eventInfo',
                    array(
                        'label' => 'Choose the options that you want to be displayed for the Events in this block.',
                        'multiOptions' => array("featuredLabel" => "Featured Label", "sponsoredLabel" => "Sponsored Label", "newLabel" => "New Label"),
                    ),
                ),
                array(
                    'Radio',
                    'showEventUpcomingPastCount',
                    array(
                        'label' => 'Show Upcoming & Past Counts',
                        'description' => 'Do you want to show Upcoming & Past counts in this block.',
                        'multiOptions' => array('1' => 'Yes', '0' => 'No'),
                        'value' => 0,
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
        'description' => 'Displays a list of all the events on your site. This widget should be placed on Advanced Events - Browse Events page.'.$widgetDescription,
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.browse-events-siteevent',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'layouts_views' => array("1", "2", "3"),
            'layouts_order' => 1,
            'columnWidth' => '180',
            'truncationGrid' => 90
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
                        'multiOptions' => array("1" => "List View", "2" => "Grid View", "3" => "Map View"),
                    ),
                ),
                array(
                    'Radio',
                    'layouts_order',
                    array(
                        'label' => 'Select a default view type for Events.',
                        'multiOptions' => array("1" => "List View", "2" => "Grid View", "3" => "Map View"),
                        'value' => 1,
                    )
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => 'Column Width For Grid View.',
                        'value' => '180',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For Grid View.',
                        'value' => '328',
                    )
                ),
                $otherInfoElement,
                $titlePositionElement,

                array(
                    'Radio',
                    'orderby',
                    array(
                        'label' => 'Default ordering in Browse Events. (Note: Selecting multiple ordering will make your page load slower.)',
                        'multiOptions' => array(
                            'starttime' => 'All events in ascending order of start time.',
                            'viewcount' => 'All events in descending order of views.',
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
                        'label' => 'Title Truncation Limit in Grid View',
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
                $showViewMoreContent,
                $showShareElement
            ),
        ),
    ),
    array(
        'title' => 'Popular / Recent / Random Events',
        'description' => 'Displays Events based on the Popularity / Sorting Criteria and other settings that you choose for this widget. You can place this widget multiple times on a page with different popularity criterion chosen for each placement.'.$widgetDescription,
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.events-siteevent',
        'defaultParams' => array(
            'title' => 'Events',
            'titleCount' => true,
            'viewType' => 'listview',
            'columnWidth' => '180'
        ),
        'adminForm' => array(
            'elements' => array(
                $contentTypeElement,
                $featuredSponsoredElement,
                $eventTypeElement,
                array(
                    'Text',
                    'titleLink',
                    array(
                        'label' => 'Enter Title Link',
                        'description' => 'If you want to show the "Explore All" link and redirect it to "Browse Event" then please use this code <a href="/'.$slug_plural.'/index">Explore All</a> Otherwise simply leave this field empty, if you do not want to show any link.',
                        'value' => '',
                    )
                ),
                array(
                    'Radio',
                    'titleLinkPosition',
                    array(
                        'label' => 'Enter Title Link Position',
                        'description' => 'Please select the position of the title link. Setting will work only if above setting "Enter Title Link" is not empty.',
                        'multiOptions' => array(
                            'top' => 'Top',
                            'bottom' => 'Bottom',
                        ),
                        'value' => 'bottom',
                    )
                ),
                array(
                    'Text',
                    'photoHeight',
                    array(
                        'label' => 'Enter the height of image.',
                        'value' => 370,
                    )
                ),
                array(
                    'Text',
                    'photoWidth',
                    array(
                        'label' => 'Enter the width of image.',
                        'value' => 350,
                    )
                ),
                $titlePositionElement,
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
                    'columnWidth',
                    array(
                        'label' => 'Column Width For Grid View.',
                        'value' => '180',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For Grid View.',
                        'value' => '328',
                    )
                ),
                array(
                    'Select',
                    'popularity',
                    array(
                        'label' => 'Popularity / Sorting Criteria',
                        'multiOptions' => array_merge($popularity_options, array('random' => 'Random')),
                        'value' => 'view_count',
                    )
                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => 'Popularity Duration (This duration will be applicable to these Popularity / Sorting Criteria:  Most Liked, Most Commented, Most Rated and Recently Created.)',
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                $hiddenSubSubCatElement,
                $otherInfoElementGrid,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of Events to show)',
                        'value' => 3,
                    )
                ),
                $truncationLocationElement,
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation Limit',
                        'value' => 16,
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
        'title' => 'Most Discussed Events',
        'description' => 'Displays the events having the most number of discussions. Multiple settings available in the Edit Settings of this widget.'.$widgetDescription,
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.most-discussed-events',
        'defaultParams' => array(
            'title' => 'Reviews',
            'titleCount' => true,
            'viewType' => 'listview',
            'columnWidth' => '180'
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
                $otherInfoElementGrid,
                $eventTypeElement,
                $titlePositionElement,
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
                    'columnWidth',
                    array(
                        'label' => 'Column Width For Grid View.',
                        'value' => '180',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For Grid View.',
                        'value' => '328',
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of Events to show)',
                        'value' => 3,
                    )
                ),
                $truncationLocationElement,
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation Limit',
                        'value' => 16,
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
        'title' => 'Search Events Form',
        'description' => 'Displays the form for searching Events on the basis of various fields and filters. Settings for this form can be configured from the Search Form Settings section.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.search-siteevent',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => 'Show Search Form',
                        'multiOptions' => array(
                            'horizontal' => 'Horizontal',
                            'vertical' => 'Vertical',
                        ),
                        'value' => 'vertical'
                    )
                ),
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
                    'resultsAction',
                    array(
                        'label' => 'Select the page where you want to display the results of search.',
                        'multiOptions' => array(
                            'index' => 'Browse Events',
                            'pinboard' => 'Browse Events - Pinboard View',
                            'top-rated' => 'Top Rated Events',
                        ),
                        'value' => 'index',
                    )
                ),
                array(
                    'Radio',
                    'locationDetection',
                    array(
                        'label' => "Allow browser to detect user's current location.",
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'whatWhereWithinmile',
                    array(
                        'label' => 'Do you want to show "What, Where and Within Miles" in single row and bold text label? [Note: This setting will not work when form is placed in right/left column.]',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Radio',
                    'advancedSearch',
                    array(
                        'label' => 'Do you want to show all advanced search fields expanded  [Note: This setting will not work if above setting set "No" and when form is placed in right/left column.]',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Radio',
                    'priceFieldType',
                    array(
                        'label' => 'Enable price slider',
                        'multiOptions' => array(
                            'slider' => 'Yes, show the slider.',
                            'text' => 'No, show the min and max price text box instead of slider.',
                        ),
                        'value' => 'slider'
                    )
                ),
                array(
                    'text',
                    'minPrice',
                    array(
                        'label' => 'Slider range starting value if enabled.',
                        'value' => 0
                    )
                ),
                array(
                    'text',
                    'maxPrice',
                    array(
                        'label' => 'Slider range ending value if enabled.',
                        'value' => 999
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'AJAX based Events Carousel',
        'description' => 'This widget contains an attractive AJAX based carousel, showcasing the events on the site. You can choose to show sponsored / featured / new events in this widget from the settings of this widget. You can place this widget multiple times on a page with different criterion chosen for each placement.'.$widgetDescription,
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.sponsored-siteevent',
        'defaultParams' => array(
            'title' => 'Events Carousel',
            'titleCount' => true,
            'showOptions' => array("category", "rating", "review"),
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
                    'showPagination',
                    array(
                        'label' => 'Do you want to show next / previous pagination?',
                        'multiOptions' => array(1 => 'Yes', 0 => 'No'),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => 'Carousel Type',
                        'multiOptions' => array(
                            '0' => 'Horizontal',
                            '1' => 'Vertical',
                        ),
                        'value' => '0',
                    )
                ),
                array(
                    'Text',
                    'blockHeight',
                    array(
                        'label' => 'Enter the height of each slideshow item.',
                        'value' => 240,
                    )
                ),
                array(
                    'Text',
                    'blockWidth',
                    array(
                        'label' => 'Enter the width of each slideshow item.',
                        'value' => 150,
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Enter number of events in a Row / Column for Horizontal / Vertical Carousel Type respectively as selected by you from the above setting.',
                        'value' => 3,
                    )
                ),
                array(
                    'Select',
                    'popularity',
                    array(
                        'label' => 'Popularity / Sorting Criteria',
                        'multiOptions' => $popularity_options,
                        'value' => 'event_id',
                    )
                ),
                $otherInfoElement,
                $eventTypeElement,
                array(
                    'Text',
                    'interval',
                    array(
                        'label' => 'Speed',
                        'description' => '(transition interval between two slides in millisecs)',
                        'value' => 300,
                    )
                ),
                $truncationLocationElement,
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation Limit',
                        'value' => 50,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    ),
                ),
                $ratingTypeElement,
                $detactLocationElement,
                $defaultLocationDistanceElement,
            ),
        ),
    ),
    array(
        'title' => 'Special Events',
        'description' => 'Displays events as special events. You can choose events to be shown in this widget from the settings of this widget. Other settings are also available.'.$widgetDescription,
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.special-events',
        'adminForm' => 'Siteevent_Form_Admin_Settings_Specialevents',
        'defaultParams' => array(
            'title' => 'Special Events',
        ),
    ),
    array(
        'title' => 'Review of the Day',
        'description' => 'Displays a review as review of the day. You can choose the review to be shown in this widget from the settings of this widget. Other settings are also available.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.review-of-the-day',
        'adminForm' => 'Siteevent_Form_Admin_Settings_Reviewdayitem',
        'defaultParams' => array(
            'title' => 'Review of the Day',
        ),
    ),
    array(
        'title' => 'Recently Viewed by Users',
        'description' => 'Displays events that have been recently viewed by Users of your site. Multiple settings are available for this widget in its Edit section.'.$widgetDescription,
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.recently-viewed-siteevent',
        'defaultParams' => array(
            'title' => 'Recently Viewed By Friends',
            'titleCount' => true,
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
                    'columnWidth',
                    array(
                        'label' => 'Column Width For Grid View.',
                        'value' => '180',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For Grid View.',
                        'value' => '328',
                    )
                ),
                $otherInfoElementGrid,
                $titlePositionElement,
                $truncationLocationElement,
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation Limit',
                        'value' => 16,
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
                        'value' => 3,
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
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'seeAllReviews',
                    array(
                        'label' => 'Show "See all Reviews" button',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                )
            ),
        ),
    ),
    array(
        'title' => 'Event Profile: Action Buttons',
        'description' => 'Displays "Sign In", "Sign Up", "Upload Photos" and "Upload Videos" buttons on Advanced Events - Event Profile page. The best place to put this widget is right/left column of Advanced Events - Event Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.profile-event-buttons',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'showButtons',
                    array(
                        'label' => 'Choose the buttons that you want to display.',
                        'multiOptions' => $eventProfileButtonsArray
                    ),
                ),
                array(
                    'Radio',
                    'show_after_event_finish',
                    array(
                        'label' => 'Show "Upload Photo" and "Upload Videos"',
                        'description' => 'When do you want to user can "Upload Photo" and "Upload Videos" in event?',
                        'multiOptions' => array(
                            1 => 'Always Allow',
                            0 => 'After finished the event'
                        ),
                        'value' => 1,
                    )
                )
            ),
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
            'loaded_by_ajax' => 1
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'loaded_by_ajax',
                    array(
                        'label' => 'Widget Content Loading',
                        'description' => 'Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                )
            )
        )
    ),
    array(
        'title' => 'Content Profile: Content Likes',
        'description' => 'Displays the users who have liked the content being currently viewed. This widget should be placed on the  Content Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'seaocore.people-like',
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of users to show)',
                        'value' => 3,
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
        'title' => 'Ajax based main Events Home widget',
        'description' => "Contains multiple Ajax based tabs showing Recently Posted, Popular, Most Reviewed, Featured and Sponsored events in a block in separate ajax based tabs respectively. You can configure various settings for this widget from the Edit settings.".$widgetDescription,
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.recently-popular-random-siteevent',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
            'layouts_views' => array("listZZZview", "gridZZZview", "mapZZZview"),
            'ajaxTabs' => array("upcoming", "mostZZZreviewed", "mostZZZpopular", "featured", "sponsored", "mostZZZjoined", "thisZZZmonth", "thisZZZweek", "thisZZZweekend", "today"),
            'showContent' => array("price", "location"),
            'upcoming_order' => 1,
            'reviews_order' => 2,
            'popular_order' => 3,
            'featured_order' => 4,
            'sponosred_order' => 5,
            'joined_order' => 6,
            'columnWidth' => '180'
        ),
        'adminForm' => array(
            'elements' => array(
                array('Text', 'titleLink', array(
                        'label' => 'Enter Title Link',
                        'description' => 'If you do not want to show title link, then simply leave this field empty. Eg. <a href="/event-items">Explore Events »</a>'
                    )
                ),
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
                        'label' => 'Choose the view types that you want to be available for events on the home and browse pages of events.',
                        'multiOptions' => array("listZZZview" => "List View", "gridZZZview" => "Grid View", "mapZZZview" => "Map View")
                    ),
                ),
                array(
                    'Radio',
                    'defaultOrder',
                    array(
                        'label' => 'Select a default view type for Events',
                        'multiOptions' => array("listZZZview" => "List View", "gridZZZview" => "Grid View", "mapZZZview" => "Map View"),
                        'value' => "listZZZview",
                    )
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => 'Column Width For Grid View.',
                        'value' => '180',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For Grid View.',
                        'value' => '328',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'ajaxTabs',
                    array(
                        'label' => 'Select the tabs that you want to be available in this block.',
                        'multiOptions' => array("upcoming" => "Upcoming (Only upcoming events will be shown in this tab, irrespective of the option chosen in above drop-down)", "mostZZZreviewed" => "Most Reviewed", "mostZZZpopular" => "Most Popular", "featured" => "Featured", "sponsored" => "Sponsored", "mostZZZjoined" => "Most Joined", "thisZZZmonth" => "This Month", "thisZZZweek" => "This Week", "thisZZZweekend" => "This Weekend", "today" => "Today")
                    )
                ),
                array(
                    'Text',
                    'upcoming_order',
                    array(
                        'label' => 'Upcoming Tab (order)',
                        'value' => 1
                    ),
                ),
                array(
                    'Text',
                    'reviews_order',
                    array(
                        'label' => 'Most Reviewed Tab (order)',
                        'value' => 2
                    ),
                ),
                array(
                    'Text',
                    'popular_order',
                    array(
                        'label' => 'Most Popular Tab (order)',
                        'value' => 3
                    ),
                ),
                array(
                    'Text',
                    'featured_order',
                    array(
                        'label' => 'Featured Tab (order)',
                        'value' => 4
                    ),
                ),
                array(
                    'Text',
                    'sponosred_order',
                    array(
                        'label' => 'Sponosred Tab (order)',
                        'value' => 5
                    ),
                ),
                array(
                    'Text',
                    'joined_order',
                    array(
                        'label' => 'Most Joined Tab (order)',
                        'value' => 6
                    ),
                ),
                array(
                    'Text',
                    'month_order',
                    array(
                        'label' => 'This Month Tab (order)',
                        'value' => 7
                    ),
                ),
                array(
                    'Text',
                    'week_order',
                    array(
                        'label' => 'This Week Tab (order)',
                        'value' => 8
                    ),
                ),
                array(
                    'Text',
                    'weekend_order',
                    array(
                        'label' => 'This Weekend Tab (order)',
                        'value' => 9
                    ),
                ),
                array(
                    'Text',
                    'today_order',
                    array(
                        'label' => 'Today Tab (order)',
                        'value' => 10
                    ),
                ),
                $titlePositionElement,
                array(
                    'Radio',
                    'showViewMore',
                    array(
                        'label' => 'Show "View More".',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Text',
                    'limit',
                    array(
                        'label' => 'Count',
                        'description' => '(number of Events to show)',
                        'value' => 12,
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
                        'value' => 600,
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
                $showShareElement,
                array(
                      'Radio',
                      'loaded_by_ajax',
                      array(
                          'label' => 'Widget Content Loading',
                          'description' => 'Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)',
                          'multiOptions' => array(
                              1 => 'Yes',
                              0 => 'No'
                          ),
                          'value' => 1,
                      )
                  ),                 
            )
        ),
    ),
    array(
        'title' => 'Categorically Popular Events',
        'description' => 'This attractive widget categorically displays the most popular events on your site. It displays 5 Events for each category. From the edit popup of this widget, you can choose the number of categories to show, criteria for popularity and the duration for consideration of popularity.'.$widgetDescription,
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.category-events-siteevent',
        'defaultParams' => array(
            'title' => 'Popular Events',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                $detactLocationElement,
                $defaultLocationDistanceElement,
                $eventTypeElement,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'No. of categories to show. Enter 0 to show all categories.',
                        'value' => 0,
                    )
                ),
                array(
                    'Text',
                    'eventCount',
                    array(
                        'label' => 'No. of events to be shown in each category.',
                        'value' => 5,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Select',
                    'popularity',
                    array(
                        'label' => 'Popularity / Sorting Criteria',
                        'multiOptions' => array(
                            'view_count' => 'Views',
                            'like_count' => 'Likes',
                            'comment_count' => 'Comments',
                            'member_count' => 'Guests',
                            'review_count' => 'Reviews',
                        ),
                        'value' => 'view_count',
                    )
                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => 'Popularity Duration (This duration will be applicable to all Popularity / Sorting Criteria except Views.)',
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
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
        'title' => 'Horizontal Search Events Form',
        'description' => "This widget searches over Event Titles, Locations and Categories. This widget should be placed in full-width / extended column. Multiple settings are available in the edit settings section of this widget.",
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.searchbox-siteevent',
        'defaultParams' => array(
            'title' => "Search",
            'titleCount' => "",
            'loaded_by_ajax' => 0
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'locationDetection',
                    array(
                        'label' => "Allow browser to detect user's current location.",
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'MultiCheckbox',
                    'formElements',
                    array(
                        'label' => 'Choose the options that you want to be displayed in this block.(Note:Proximity Search will not display if location field will be disabled.)',
                        'multiOptions' => array("textElement" => "Auto-suggest for Keywords", "categoryElement" => "Category Filtering", "locationElement" => "Location field", "locationmilesSearch" => "Proximity Search"),
                    ),
                ),
                array(
                    'MultiCheckbox',
                    'categoriesLevel',
                    array(
                        'label' => 'Select the category level belonging to which categories will be displayed in the category drop-down of this widget.',
                        'multiOptions' => array("category" => "Category", "subcategory" => "Sub-category", "subsubcategory" => "3rd level category"),
                    ),
                ),
                array(
                    'Radio',
                    'showAllCategories',
                    array(
                        'label' => 'Do you want all the categories, sub-categories and 3rd level categories to be shown to the users even if they have 0 events in them?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Text',
                    'textWidth',
                    array(
                        'label' => 'Width for AutoSuggest',
                        'value' => 275,
                    )
                ),
                array(
                    'Text',
                    'locationWidth',
                    array(
                        'label' => 'Width for Location field',
                        'value' => 250,
                    )
                ),
                array(
                    'Text',
                    'locationmilesWidth',
                    array(
                        'label' => 'Width for Proximity Search field',
                        'value' => 125,
                    )
                ),
                array(
                    'Text',
                    'categoryWidth',
                    array(
                        'label' => 'Width for Category Filtering',
                        'value' => 150,
                    )
                ),
               array(
                    'Radio',
                    'loaded_by_ajax',
                    array(
                        'label' => 'Widget Content Loading',
                        'description' => 'Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Event Profile: Related Events',
        'description' => 'Displays a list of all events related to the event being viewed. The related events are shown based on the tags and top-level category of the event being viewed. You can choose the related event criteria from the Edit Settings. This widget should be placed on the Advanced Events - Event Profile page.'.$widgetDescription,
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.related-events-view-siteevent',
        'defaultParams' => array(
            'title' => 'More Events in %s',
            'titleCount' => true,
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
                    'columnWidth',
                    array(
                        'label' => 'Column Width For Grid View.',
                        'value' => '180',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For Grid View.',
                        'value' => '328',
                    )
                ),
                $eventTypeElement,
                $titlePositionElement,
                $otherInfoElementGrid,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of Events to show)',
                        'value' => 3,
                    )
                ),
                $truncationLocationElement,
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation Limit',
                        'value' => 24,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                $ratingTypeElement,
                $detactLocationElement,
                $defaultLocationDistanceElement,
                array(
                    'Radio',
                    'networkBased',
                    array(
                        'label' => 'Do you want to display events belonging to same network as joined by the user currently viewing this widget?',
                        'multiOptions' => array(
                            '1' => "Yes",
                            '0' => 'No'
                        ),
                        'value' => '0',
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Event Profile: Contact Details',
        'description' => "Displays the Contact Details of an event. This widget should be placed on the Advanced Events - Event Profile page.",
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.contactdetails-siteevent',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'contacts' => array("0" => "1", "1" => "2", "2" => "3"),
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'contacts',
                    array(
                        'label' => 'Select the contact details you want to display',
                        'multiOptions' => array("1" => "Phone", "2" => "Email", "3" => "Website"),
                    ),
                ),
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
            'loaded_by_ajax' => 1
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
                    'loaded_by_ajax',
                    array(
                        'label' => 'Widget Content Loading',
                        'description' => 'Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
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
        'title' => 'Featured Editor',
        'description' => 'Displays the Featured Editor on your site. Edit settings of this widget contains option to select Featured Editor.',
        'category' => 'Advanced Events',
        'autoEdit' => true,
        'type' => 'widget',
        'name' => 'siteevent.editor-featured-siteevent',
        'adminForm' => 'Siteevent_Form_Admin_Editors_Featured',
        'defaultParams' => array(
            'title' => 'Featured Editor',
        ),
    ),
    array(
        'title' => 'Editors Home: Editors',
        'description' => "Displays a list of all the editors on site. This widget should be placed on 'Advanced Events - Editors Home' page.",
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.editors-home',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
        ),
        'adminForm' => array(
            'elements' => array(
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
        )
    ),
    array(
        'title' => 'Editors Statistics',
        'description' => 'Displays statistics of all the Editors on your site added by you from the \'Manage Editors\' section in the Admin Panel.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.editors-home-statistics-siteevent',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        )
    ),
    array(
        'title' => 'Event Profile: About Editor',
        'description' => 'Displays the description (written by you from the \'Manage Editor\' section in the Admin Panel and Editors) about the Editor who has written \'Editor Review\' for the event. This widget should be placed on the Advanced Events - Event Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.about-editor-siteevent',
        'defaultParams' => array(
            'title' => 'About Me',
            'titleCount' => "",
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => 'Editor / Member Profile: About Editor',
        'description' => 'Displays the description written by you (from the \'Manage Editors\' section in the Admin Panel) and Editors (using this widget) about the Editor whose Editor Profile is being viewed. This widget should be placed on the Advanced Events - Editor Profile page or Member Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.editor-profile-info',
        'defaultParams' => array(
            'title' => "About Me",
            'titleCount' => "",
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'show_badge',
                    array(
                        'label' => 'Displays the  badge assigned by you from \'Manage Editors\' section.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'show_designation',
                    array(
                        'label' => 'Displays the designation assigned by you from \'Manage Editors\' section.',
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
        'title' => 'Editor / Member Profile: Editor’s Name and Designation',
        'description' => 'Displays the name and designation of the Editor whose profile is being viewed. This widget should be placed on the Advanced Events - Editor Profile page or Member Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.editor-profile-title',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'show_designation',
                    array(
                        'label' => 'Do you want to display Editor’s designation in this block? (You can assign the designation from the ‘Manage Editors’ section of this plugin.)',
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
        'title' => 'Event Profile: User Reviews',
        'description' => 'This widget forms the User Reviews tab on the Advanced Events - Event Profile page and displays all the reviews written by the users of your site for the Event being viewed. This widget should be placed in the Tabbed Blocks area of the Advanced Events - Event Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.user-siteevent',
        'defaultParams' => array(
            'title' => "User Reviews",
            'titleCount' => "true",
            'loaded_by_ajax' => 1
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemProsConsCount',
                    array(
                        'label' => 'Number of reviews’ Pros and Cons to be displayed in the search results using \'Only Pros\' and \'Only Cons\' in the \'Show\' review search bar.',
                        'value' => 3,
                    )
                ),
                array(
                    'Text',
                    'itemReviewsCount',
                    array(
                        'label' => 'Number of user reviews to show',
                        'value' => 3,
                    )
                ),
                array(
                    'Radio',
                    'loaded_by_ajax',
                    array(
                        'label' => 'Widget Content Loading',
                        'description' => 'Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)',
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
        'description' => 'Displays event profile photo with event information and various action links that can be performed on the Events from their Profile page (edit, delete, tell a friend, share, etc.). You can manage the Action Links available in this widget from the Menu Editor section by choosing Advanced Events - Event Profile Page Options Menu. You can choose various information options from the Edit settings of this widget. This widget should be placed on the Advanced Events - Event Profile page.'.$widgetDescription,
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.list-information-profile',
        'defaultParams' => array(
            'title' => '',
            'showContent' => array("memberCount", "postedDate", "postedBy", "viewCount", "likeCount", "commentCount", "photo", "photosCarousel", "tags", "location", "description", "title", "reviewCreate", "price", "startDate", "endDate")
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'showContent',
                    array(
                        'label' => 'Select the information options that you want to be available in this block.',
                        'multiOptions' => array_merge(array("title" => "Title", "postedDate" => "Posted Date", "hostName" => "Hosted By", "categoryLink" => "Category", "startDate" => "Start Date and Time", "endDate" => "End Date and Time", "ledBy" => "Led By", "price" => "Price", "photo" => "Photo", "photosCarousel" => 'Photos Carousel (Note: Carousel will only be displayed, if the event has atleast 2 photos and Photo option of this setting is enabled.)', "featuredLabel" => "Featured Label", "sponsoredLabel" => "Sponsored Label", "newLabel" => "New Label", "tags" => "Tags", "description" => "Description", "reviewCreate" => "Write a review", "venueName" => "Venue Name", "location" => "Location", "directionLink" => "Get Directions Link (Dependent on Location)", "viewCount" => "Views", "likeCount" => "Likes", "commentCount" => "Comments", "memberCount" => "Guests", "reviewCount" => "Reviews", "tags" => "Tags", "likeButton" => "Like Button"), $siteeventrepeat_settings),
                    ),
                ),
                array(
                    'Radio',
                    'like_button',
                    array(
                        'label' => 'Do you want to enable Like button in this block?',
                        'multiOptions' => $show_like_button,
                        'value' => $default_value,
                    ),
                ),
                array(
                    'Radio',
                    'actionLinks',
                    array(
                        'label' => 'Do you want action links like print, tell a friend, edit details, etc. to the available for the events in this block?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
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
        'description' => 'Displays guests of an event being currently viewed. This widget should be placed on the "Advanced Event: Event Profile" page.'.$widgetDescription,
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.profile-members',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Guests',
            'titleCount' => true,
            'loaded_by_ajax' => 1,
        ),
        'requirements' => array(
            'subject' => 'event',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'loaded_by_ajax',
                    array(
                        'label' => 'Widget Content Loading',
                        'description' => 'Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)',
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
        'title' => 'Event Profile: Guests (sidebar)',
        'description' => 'This widget displays all the guests of the event being currently viewed. From the edit settings you can choose to show guests who are attending, maybe attending and not attending the event. This widget should be placed in the right / left column of the Advanced Events - Event Profile page.'.$widgetDescription,
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.profile-members-sidebar',
        'defaultParams' => array(
            'title' => 'Guests',
            'titleCount' => true,
            'loaded_by_ajax' => 0,
        ),
        'requirements' => array(
            'subject' => 'event',
        ),
        'adminForm' => array(
            'elements' => array(
                
                array(
                    'Radio',
                    'loaded_by_ajax',
                    array(
                        'label' => 'Widget Content Loading',
                        'description' => 'Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
                
                array(
                    'MultiCheckbox',
                    'join_filters',
                    array(
                        'label' => 'Select the RSVP status belonging to which guests will be shown in this widget.',
                        'multiOptions' => array("2" => "Attending", "1" => "Maybe Attending", "0" => "Not Attending"),
                    ),
                ),
                array(
                    'Radio',
                    'show_seeall',
                    array(
                        'label' => 'Show "See All" link.',
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
                        'label' => 'Count',
                        'description' => '(number of members to show)',
                        'value' => 10,
                    )
                ),
            ),
        )
    ),
    array(
        'title' => 'Event Profile: Event RSVP',
        'description' => 'Displays options to users for RSVP`ing to an event on event`s profile.'.$widgetDescription,
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
                    'Select',
                    'placeWidget',
                    array(
                        'label' => 'Select column in which this widget is to be placed.',
                        'multiOptions' => array(
                            'smallColumn' => 'Right / Left Column',
                            'largeColumn' => 'Right Extended / Left Extended Column'
                        ),
                        'value' => 1,
                    )
                ),
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
                            'socialLinks' => 'Social Links',
                            'messageHost' => 'Message Host',
                            'viewHostProfile' => 'View Host Profile'
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
        'title' => 'Event Profile: Event User Ratings',
        'description' => 'This widget displays the overall rating given to the event by member of your site. This widget should be placed in the right / left column on the Advanced Event - Event Profile page. (This widget will only display when you have chosen \'Yes, allow only Ratings\' value for the field \'Allow Only Ratings\' for the associated listing type.)',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.user-ratings',
        'defaultParams' => array(
            'title' => 'User Ratings',
            'titleCount' => true,
        ),
        'adminForm' => array(
        )
    ),
    array(
        'title' => 'Top Reviewers',
        'description' => 'This widget shows the top reviewers for the events on your site based on the number of reviews posted by them. Multiple settings are available for this widget.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.top-reviewers-siteevent',
        'defaultParams' => array(
            'title' => 'Top Reviewers',
        ),
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'type',
                    array(
                        'label' => 'Review Type',
                        'description' => 'Choose the review type for which maximum reviewers should be shown in this widget.',
                        'multiOptions' => array(
                            'overall' => 'Overall',
                            'user' => 'User Reviews',
                            'editor' => 'Editor Reviews'
                        ),
                        'value' => 'user'
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of reviewers to show)',
                        'value' => 3,
                    )
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
                    'Radio',
                    'viewType',
                    array(
                        'label' => 'Display Type',
                        'multiOptions' => array(
                            '1' => 'Horizontal',
                            '0' => 'Vertical',
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of editors to show)',
                        'value' => 4,
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
        'title' => 'Popular / Recent / Random Reviews',
        'description' => 'Displays Reviews based on the Popularity / Sorting Criteria and other settings that you choose for this widget. You can place this widget multiple times on a page with different popularity criterion chosen for each placement.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.popular-reviews-siteevent',
        'defaultParams' => array(
            'title' => 'Popular Reviews',
            'statistics' => array("viewCount"),
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'type',
                    array(
                        'label' => 'Review Type',
                        'multiOptions' => array(
                            'overall' => 'All Reviews',
                            'user' => 'User Reviews',
                            'editor' => 'Editor Reviews',
                        ),
                        'value' => 'user'
                    )
                ),
                array(
                    'Radio',
                    'status',
                    array(
                        'label' => 'Do you want to show only featured reviews.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '0',
                    )
                ),
                array(
                    'Select',
                    'popularity',
                    array(
                        'label' => 'Popularity / Sorting Criteria.(The popularity criterion: Most Helpful, Most Liked, Most Commented and Most Replied will not be applicable, if you have chosen Editor Reviews from the \'Review Type\' setting above.)',
                        'multiOptions' => array(
                            'view_count' => 'Most Viewed',
                            'like_count' => 'Most Liked',
                            'comment_count' => 'Most Commented',
                            'helpful_count' => 'Most Helpful',
                            'reply_count' => 'Most Replied',
                            'review_id' => 'Most Recent',
                            'modified_date' => 'Recently Updated',
                            'RAND()' => 'Random',
                        ),
                        'value' => 'view_count',
                    )
                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => 'Popularity Duration (This duration will be applicable to these Popularity / Sorting Criteria: Most Liked, Most Commented, Most Recent and Recently Updated)',
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
                array(
                    'Radio',
                    'groupby',
                    array(
                        'label' => 'Show multiple reviews from the same editor / user.',
                        'description' => '(If selected "No", only one review will be displayed from a reviewer.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => 'Choose the statistics to be displayed for the reviews in this widget. (Note: This settings will not work if you choose to show Editor Reviews from the "Review Type" setting above.)',
                        'multiOptions' => array("viewCount" => "Views", "likeCount" => "Likes", "commentCount" => "Comments", 'replyCount' => 'Replies', 'helpfulCount' => 'Helpful'),
                    ),
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of reviews to show)',
                        'value' => 3,
                    )
                ),
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation limit',
                        'value' => 16,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Browse Reviews: Search Reviews Form',
        'description' => 'Displays the form for searching reviews. It is recommended to place this widget on Advanced Events - Browse Reviews page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.review-browse-search',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => 'Browse Reviews: User Reviews Statistics',
        'description' => 'Displays statistics for all the reviews written by the users of your site. This widget should be placed in the left column of the Advanced Events - Browse Review page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.reviews-statistics',
        'defaultParams' => array(
            'title' => 'Reviews Statistics',
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => 'Editor / Member Profile: Editor’s Reviews Statistics',
        'description' => 'Displays statistics for all the editor reviews written by the Editor whose Editor Profile is being viewed. This widget should be placed on the Advanced Events - Editor Profile page or Member Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.editor-profile-statistics',
        'defaultParams' => array(
            'title' => 'Editor Statistics',
        ),
        'adminForm' => array(
            'elements' => array(
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
            'viewTypes' => array("list", "grid"),
            'statisticsDiary' => array("viewCount", "entryCount"),
            'viewTypeDefault' => 'list',
            'listThumbsValue' => 2,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'viewTypes',
                    array(
                        'label' => 'Choose the view types.',
                        'multiOptions' => array("list" => "List View", "grid" => "Pinboard View"),
                    ),
                ),
                array(
                    'Radio',
                    'viewTypeDefault',
                    array(
                        'label' => 'Choose the default view type',
                        'multiOptions' => array("list" => "List View", "grid" => "Pinboard View"),
                        'value' => 'list',
                    )
                ),
                $statisticsDiaryElement,
                array(
                    'Text',
                    'listThumbsValue',
                    array(
                        'label' => 'Enter the number of event thumbnails to be shown along with the cover photo of a diary. (This setting will only work, if you have chosen Pinboard View from the above setting.)',
                        'value' => 2,
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Number of diaries to show per page',
                        'value' => 20,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Browse Diaries: Search Diaries Form',
        'description' => 'Displays the form for searching diaries. It is recommended to place this widget on Advanced Events - Browse Diaries page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.diary-browse-search',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => 'Form Type',
                        'multiOptions' => array(
                            'horizontal' => 'Horizontal',
                            'vertical' => 'Vertical',
                        ),
                        'value' => 'horizontal'
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Popular / Recent / Random Diaries',
        'description' => 'Displays Event Diaries based on the Popularity / Sorting Criteria and other settings that you choose for this widget. You can place this widget multiple times on a page with different popularity criterion chosen for each placement.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.diary-events',
        'autoEdit' => true,
        'defaultParams' => array(
            'title' => 'Popular Diaries',
            'statisticsDiary' => array("viewCount", "entryCount"),
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'type',
                    array(
                        'label' => 'Show Diaries of:',
                        'multiOptions' => array(
                            'friends' => 'Currently logged-in member’s friends.',
                            'viewer' => 'Currently logged-in member.',
                            'none' => 'Everyone'
                        ),
                        'value' => 'none'
                    )
                ),
                array(
                    'Select',
                    'orderby',
                    array(
                        'label' => 'Popularity / Sorting Criteria',
                        'multiOptions' => array(
                            'total_item' => 'Having maximum number of Events',
                            'creation_date' => 'Most Recent',
                            'view_count' => 'Most Viewed',
                            'RAND()' => 'Random'
                        ),
                        'value' => 'creation_date',
                    )
                ),
                $statisticsDiaryElement,
                array(
                    'Text',
                    'limit',
                    array(
                        'label' => 'Number of diaries to show',
                        'value' => 3,
                    )
                ),
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation limit',
                        'value' => 16,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Create a New Diary',
        'description' => 'Displays the link to Create a New Diary.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.diary-creation-link',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => 'Event Profile: "Add to Diary" Button',
        'description' => 'Displays a "Add to Diary" button on Advanced Events - Event Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.diary-add-link',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'diaryAddCount',
                    array(
                        'label' => 'Do you want to show count of currently viewed event added to diary?',
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
        'title' => 'Diary Profile: Added Events',
        'description' => 'Displays a list of all the events added in the diary being viewed. This widget should be placed on the Advanced Events - Diary Profile page.'.$widgetDescription,
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.diary-profile-items',
        'autoEdit' => true,
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'shareOptions' => array("siteShare", "friend", "report", "print", "socialShare"),
            'statisticsDiary' => array("viewCount", "entryCount"),
            'show_buttons' => array("diary", "comment", "like", "share", "facebook", "pinit")
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
                array(
                    'MultiCheckbox',
                    'shareOptions',
                    array(
                        'label' => 'Select the options that you want to display in this block.',
                        'multiOptions' => array("siteShare" => "Site Share", "friend" => "Tell a Friend", "report" => "Report", 'print' => 'Print', 'socialShare' => 'Social Share'),
                    ),
                ),
                array(
                    'Text',
                    'itemWidth',
                    array(
                        'label' => 'One event Width',
                        'description' => 'Enter the width for each pinboard item.',
                        'value' => 237,
                    )
                ),
                array(
                    'Radio',
                    'withoutStretch',
                    array(
                        'label' => 'Do you want to display the images without stretching them to the width of each diary block? (This setting will only work, if you have chosen Pinboard View from the above setting.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '0',
                    )
                ),
                $otherInfoElement,
                $statisticsDiaryElement,
                array(
                    'MultiCheckbox',
                    'show_buttons',
                    array(
                        'label' => 'Choose the action links that you want to be available for each Event pinboard item.',
                        'multiOptions' => array("diary" => "Diary", "comment" => "Comment", "like" => "Like / Unlike", 'share' => 'Share', 'facebook' => 'Facebook', 'twitter' => 'Twitter', 'pinit' => 'Pin it', 'tellAFriend' => 'Tell a Friend', 'print' => 'Print')
                    ),
                ),
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
    ),
    array(
        'title' => 'Browse Events\' Locations',
        'description' => "Displays the form for searching Events corresponding to location on the basis of various filters and a list of all the events having location entered corresponding to them on the site. This widget must be placed on Advanced Events - Browse Events' Location page.".$widgetDescription,
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.browselocation-siteevent',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                $otherInfoElement,
                $truncationLocationElement,
                $ratingTypeElement,
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
                    'priceFieldType',
                    array(
                        'label' => 'Enable price slider in search form',
                        'multiOptions' => array(
                            'slider' => 'Yes, show the slider.',
                            'text' => 'No, show the min and max price text box instead of slider.',
                        ),
                        'value' => 'slider'
                    )
                ),
                array(
                    'text',
                    'minPrice',
                    array(
                        'label' => 'Slider range starting value if enabled.',
                        'value' => 0
                    )
                ),
                array(
                    'text',
                    'maxPrice',
                    array(
                        'label' => 'Slider range ending value if enabled.',
                        'value' => 999
                    )
                ),
                array(
                    'Radio',
                    'locationDetection',
                    array(
                        'label' => "Allow browser to detect user's current location.",
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
        'title' => 'Event Profile: Event Photos Carousel',
        'description' => 'Displays photo thumbnails in an attractive carousel, clicking on which opens the photo in lightbox. This widget should be placed on the Advanced Events - Event Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.photos-carousel',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of photos to show)',
                        'value' => 2,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    ),
                ),
            ),
        ),
        'requirements' => array(
            'subject' => 'siteevent',
        ),
    ),
    array(
        'title' => 'Event / Review Profile: Comments & Replies',
        'description' => 'Enable users to comment and reply on the event / review being viewed. Displays all the comments and replies on the events / reviews. This widget should be placed on Advanced Events - Event Profile page or Advanced Events - Review Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'seaocore.seaocores-nestedcomments',
        'defaultParams' => array(
            'title' => 'Comments'
        ),
        'requirements' => array(
            'subject',
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => 'Category Navigation Bar',
        'description' => 'Displays categories in this block. You can configure various settings for this widget from the Edit settings.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.listtypes-categories',
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'viewDisplayHR',
                    array(
                        'label' => 'Select the placement position of the navigation bar',
                        'multiOptions' => array(
                            1 => 'Horizontal',
                            0 => 'Vertical'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Hidden',
                    'nomobile',
                    array(
                        'label' => '',
                    )
                ),    
            ))
    ),
    array(
        'title' => 'Categories Banner',
        'description' => 'Displays banners for categories, sub-categories and 3rd level categories on Advanced Events - Browse Events page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => false,
        'name' => 'siteevent.categories-banner-siteevent',
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
        'title' => 'Review Profile: Owner Reviews',
        'description' => 'Displays the other reviews posted by the owner of the review which is being viewed. This widget should be placed on Advanced Events - Review Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.ownerreviews-siteevent',
        'autoEdit' => true,
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'statistics' => array("likeCount", "replyCount", "commentCount")
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => 'Choose the statistics to be displayed for the reviews in this widget.',
                        'multiOptions' => array("viewCount" => "Views", "likeCount" => "Likes", "commentCount" => "Comments", 'replyCount' => 'Replies', 'helpfulCount' => 'Helpful'),
                    ),
                ),
                array(
                    'Text',
                    'count',
                    array(
                        'label' => 'Number of reviews to show',
                        'value' => 3,
                    )
                ),
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation Limit',
                        'value' => 24,
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
        'title' => 'Events Home: Pinboard View',
        'description' => 'Displays events in Pinboard View on the Events Home page. Multiple settings are available to customize this widget.'.$widgetDescription,
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.pinboard-events-siteevent',
        'defaultParams' => array(
            'title' => 'Recent',
            'show_buttons' => array("membership", "comment", "like", 'share', 'facebook', 'twitter', 'pinit')
        ),
        'adminForm' => array(
            'elements' => array(
                $contentTypeElement,
                $featuredSponsoredElement,
                array(
                    'Select',
                    'popularity',
                    array(
                        'label' => 'Popularity / Sorting Criteria',
                        'multiOptions' => $popularity_options,
                        'value' => 'event_id',
                    )
                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => 'Popularity Duration (This duration will be applicable to these Popularity / Sorting Criteria:  Most Liked, Most Commented, Most Rated and Recently Created.)',
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                $hiddenSubSubCatElement,
                $otherInfoElement,
                $eventTypeElement,
                array(
                    'Radio',
                    'userComment',
                    array(
                        'label' => 'Do you want to show user comments and enable user to post comment or not?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1'
                    ),
                ),
                array(
                    'Select',
                    'autoload',
                    array(
                        'label' => 'Do you want to enable auto-loading of old pinboard items when users scroll down to the bottom of this page?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1'
                    )
                ),
                array(
                    'Select',
                    'defaultLoadingImage',
                    array(
                        'label' => 'Do you want to show a Loading image when this widget renders on a page?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1
                    )
                ),
                array(
                    'Text',
                    'itemWidth',
                    array(
                        'label' => 'One Item Width',
                        'description' => 'Enter the width for each pinboard item.',
                        'value' => 237,
                    )
                ),
                array(
                    'Radio',
                    'withoutStretch',
                    array(
                        'label' => 'Do you want to display the images without stretching them to the width of each pinboard item?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '0',
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of Events to show)',
                        'value' => 12,
                    )
                ),
                array(
                    'Text',
                    'noOfTimes',
                    array(
                        'label' => 'Auto-Loading Count',
                        'description' => 'Enter the number of times that auto-loading of old pinboard items should occur on scrolling down. (Select 0 if you do not want such a restriction and want auto-loading to occur always. Because of auto-loading on-scroll, users are not able to click on links in footer; this setting has been created to avoid this.)',
                        'value' => 0,
                    )
                ),
                array(
                    'MultiCheckbox',
                    'show_buttons',
                    array(
                        'label' => 'Choose the action links that you want to be available for the Events displayed in this block. (This setting will only work, if you have chosen Pinboard View from the above setting.)',
                        'multiOptions' => array("membership" => "Join Button", "comment" => "Comment", "like" => "Like / Unlike", 'share' => 'Share', 'facebook' => 'Facebook', 'twitter' => 'Twitter', 'pinit' => 'Pin it', 'tellAFriend' => 'Tell a Friend', 'print' => 'Print')
                    ),
                ),
                $truncationLocationElement,
                array(
                    'Text',
                    'truncationDescription',
                    array(
                        'label' => "Enter the trucation limit for the Event Description. (If you want to hide the description, then enter '0'.)",
                        'value' => 100,
                    )
                ),
                $ratingTypeElement,
                $detactLocationElement,
                $defaultLocationDistanceElement,
            ),
        ),
    ),
    array(
        'title' => 'Browse Events: Pinboard View',
        'description' => 'Displays a list of all the events on site in attractive Pinboard View. You can also choose to display events based on user’s current location by using the Edit Settings of this widget. It is recommended to place this widget on “Browse Events ‘s Pinboard View” page'.$widgetDescription,
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.pinboard-browse',
        'defaultParams' => array(
            'title' => 'Recent',
            'show_buttons' => array("membership", "comment", "like", 'share', 'facebook', 'twitter', 'pinit')
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
                    'Radio',
                    'userComment',
                    array(
                        'label' => 'Do you want to show user comments and enable user to post comment or not?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1'
                    ),
                ),
                array(
                    'Select',
                    'autoload',
                    array(
                        'label' => 'Do you want to enable auto-loading of old pinboard items when users scroll down to the bottom of this page?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1'
                    )
                ),
                array(
                    'Select',
                    'defaultLoadingImage',
                    array(
                        'label' => 'Do you want to show a Loading image when this widget renders on a page?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1
                    )
                ),
                array(
                    'Text',
                    'itemWidth',
                    array(
                        'label' => 'One Item Width',
                        'description' => 'Enter the width for each pinboard item.',
                        'value' => 237,
                    )
                ),
                array(
                    'Radio',
                    'withoutStretch',
                    array(
                        'label' => 'Do you want to display the images without stretching them to the width of each pinboard item?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '0',
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of Events to show)',
                        'value' => 12,
                    )
                ),
                array(
                    'MultiCheckbox',
                    'show_buttons',
                    array(
                        'label' => 'Choose the action links that you want to be available for the Events displayed in this block. (This setting will only work, if you have chosen Pinboard View from the above setting.)',
                        'multiOptions' => array("membership" => "Join Button", "comment" => "Comment", "like" => "Like / Unlike", 'share' => 'Share', 'facebook' => 'Facebook', 'twitter' => 'Twitter', 'pinit' => 'Pin it', 'tellAFriend' => 'Tell a Friend', 'print' => 'Print')
                    ),
                ),
                $truncationLocationElement,
                array(
                    'Text',
                    'truncationDescription',
                    array(
                        'label' => "Enter the trucation limit for the Event Description. (If you want to hide the description, then enter '0'.)",
                        'value' => 100,
                    )
                ),
                $ratingTypeElement,
                $detactLocationElement,
                $defaultLocationDistanceElement,
            ),
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
            'loaded_by_ajax'=>1
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'loaded_by_ajax',
                    array(
                        'label' => 'Widget Content Loading',
                        'description' => 'Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                $contentTypeElement,
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
        'title' => 'My Events Calendar: Calendar View',
        'description' => 'Displays list of all Events (joined, led by, hosted, etc by the member who is currently viewing \'My Events Calendar page\') in calendar view. This widget should be placed in \'Advanced Events - My Events Calendar\'.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.mycalendar-siteevent',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'actionLinks' => $actionLinksElementValue
        ),
        'adminForm' => array(
            'elements' => array(
               $actionLinksElement 
             ),
        ),
    ),
    array(
        'title' => 'Categories / Sub-categories in Grid View',
        'description' => 'Displays Categories and Sub-categories in Grid view on Categories Home page and associated Category’s Home page respectively.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteevent.categories-grid-view',
        'defaultParams' => array(
            'title' => 'Categories',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'showSubCategoriesCount',
                    array(
                        'label' => 'Show number of sub-categories / 3rd level categories to be shown on mouseover on a category / sub-category respectively.',
                        'value' => '5',
                        'maxlength' => 1
                    )
                ),
                array(
                    'Radio',
                    'showCount',
                    array(
                        'label' => 'Show event count along with the sub-category / 3rd level category name.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => 'Column Width For Grid View.',
                        'value' => '234',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For Grid View.',
                        'value' => '216',
                    )
                ),
                array(
                    'Text',
                    'categoryCount',
                    array(
                        'label' => 'Please enter no. of categories do you want to show.',
                        'value' => '8',
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Categories Title',
        'description' => 'Display the categories name on category home page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => false,
        'name' => 'siteevent.category-name-siteevent',
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
        'title' => 'Category Profile: Breadcrumb',
        'description' => 'Displays breadcrumb of the category. This widget should be placed on the Category Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.categories-home-breadcrumb',
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
            ))
    ),
    array(
        'title' => 'Host / Member / Content Profile: Hosted Events',
        'description' => 'This widget displays all the events hosted by the host (You can choose who can host events from the Global Settings section of Advanced Events Plugin.). Multiple settings are available in the Edit Settings section of this widget. This widget can be placed on the Member Profile page, Advanced Events - Host Profile Page or the profile page of host as configured by you from the Global Settings section of Advanced Events Plugin.'.$widgetDescription,
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.host-events',
        'autoEdit' => true,
        'defaultParams' => array(
            'title' => 'Events',
            'titleCount' => true,
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
                array(
                    'MultiCheckbox',
                    'typesOfViews',
                    array(
                        'label' => 'Choose the view types that you want to be available for events on this widget.',
                        'multiOptions' => array(
                            'listview' => 'List View',
                            'gridview' => 'Grid View',
                            'mapview' => 'Map View',
                        ),
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
                            'mapview' => 'Map View',
                        ),
                        'value' => 'listview',
                    )
                ),
                $titlePositionElement,
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => 'Column Width For Grid View.',
                        'value' => '180',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For Grid View.',
                        'value' => '216',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'eventFilterTypes',
                    array(
                        'label' => 'Select Events that you want to be shown in this block.',
                        'multiOptions' => array("upcoming" => "Upcoming Events", "past" => "Past Events"),
                    ),
                ),
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
                    'truncationGrid',
                    array(
                        'label' => 'Title Truncation Limit in Grid View',
                        'value' => 90,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
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
                array(
                    'Radio',
                    'networkBased',
                    array(
                        'label' => 'Do you want to display events belonging to same network as joined by the user currently viewing this widget?',
                        'multiOptions' => array(
                            '1' => "Yes",
                            '0' => 'No'
                        ),
                        'value' => '0',
                    )
                ),
                $showShareElement
            ),
        )
    ),
    array( 
        'title' => 'Content Type: Profile Events',
        'description' => "Displays a list of events, in the content being currently viewed. You can manage the content types for which event owners will be able to manage events from the Manage Modules section of Advanced Events plugin. This widget should be placed on content's Profile page.".$widgetDescription ,
        'category' => 'Advanced Events',
        'type' => 'widget',
       // 'autoEdit' => true,
        'name' => 'siteevent.contenttype-events',
        'defaultParams' => array(
            'title' => 'Events',
            'titleCount' => true,
            'layouts_views' => array("1", "2", "3"),
            'layouts_order' => 1,
            'columnWidth' => '180',
            'truncationGrid' => 90,
            'eventFilterTypes' => array("onlyOngoing", "upcoming", "past"),
            'eventOwnerType' => array("lead", "host"),
            'eventInfo' => array("viewCount", "likeCount", "commentCount", "memberCount", 'reviewCount', 'ratingStar')
            
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
                        'label' => 'Select Events that you want to be shown in this block.',
                        'multiOptions' => array("onlyOngoing" => "Ongoing Events","upcoming" => "Upcoming Events", "past" => "Past Events"),
                    ),
                ),
                array(
                    'MultiCheckbox',
                    'eventOwnerType',
                    array(
                        'label' => 'Which all events do you want to show in this widget?',
                        'multiOptions' => array("lead" => "Events Leaded by the selected Content Type", "host" => "Events Hosted by the selected Content Type"),
                    ),
                ),
                array(
                    'MultiCheckbox',
                    'layouts_views',
                    array(
                        'label' => 'Choose the view types that you want to be available for events.',
                        'multiOptions' => array("1" => "List View", "2" => "Grid View", "3" => "Map View"),
                    ),
                ),
                array(
                    'Radio',
                    'layouts_order',
                    array(
                        'label' => 'Select a default view type for Events.',
                        'multiOptions' => array("1" => "List View", "2" => "Grid View", "3" => "Map View"),
                        'value' => 1,
                    )
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => 'Column Width For Grid View.',
                        'value' => '180',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For Grid View.',
                        'value' => '328',
                    )
                ),
                $otherInfoElement,
                $titlePositionElement,
                $descriptionPositionElement,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count Per Page',
                        'description' => '(number of Events to be shown on one page)',
                        'value' => 10,
                    )
                ),
                $truncationLocationElement,
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
                array(
                    'Text',
                    'truncationGrid',
                    array(
                        'label' => 'Title Truncation Limit in Grid View',
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
    array(
        'title' => 'Host Profile: Information',
        'description' => 'Displays the title, social links, total events hosted, description, etc about the host (where host is ‘other individual or organization’). This widget should be placed on the ‘Advanced Events - Host Profile Page’.',
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
                        'multiOptions' => array("title" => "Title", "description" => "Host Description", "links" => "Social Links", "photo" => "Photo", 'creator' => 'Creator', 'options' => "Edit / Remove Options", 'totalevent' => 'Total Events hosted by the Host.', 'totalguest' => 'Number of guests who have joined Events hosted by the Host.', 'totalrating' => 'Ratings on the Events hosted by the Host.'),
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
        'title' => 'Event Profile: Owner and Leaders',
        'description' => "Displays owner and leaders of the event being currently viewed. This widget should be placed in the right / left column on the Advanced Events - Events Profile page.",
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.led-by-siteevent',
        'defaultParams' => array(
            'title' => 'Led By',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
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
    array(
        'title' => 'Event Profile: Add to My Calendar',
        'description' => 'This widget will displays the links to add the events on the Google, iCal, Outlook and Yahoo! calender. This widget should be placed in the right / left column on the Advanced Events - Events Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.add-to-my-calendar-siteevent',
        'defaultParams' => array(
            'title' => '',
            'calendarOptions' => array("google", "iCal","Outlook","yahoo")
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'calendarOptions',
                    array(
                        'label' => 'Choose the options to show links to add the events on the calender.',
                        'multiOptions' => array("google" => "Google Calendar", "iCal" => "iCal", "outlook" => "Outlook Calendar", "yahoo" => "Yahoo! Calendar"),
                    ),
                ),
            ),
        ),
    ),  
);

if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
    $ads_Array = array(
        array(
            'title' => 'Ads Widget',
            'description' => 'Displays Community Ads on your site. From the Edit Settings of this widget, you can choose number of ads to be displayed.',
            'category' => 'Advanced Events',
            'type' => 'widget',
            'name' => 'siteevent.event-ads',
            'defaultParams' => array(
                'title' => '',
                'titleCount' => true,
            ),
            'adminForm' => array(
                'elements' => array(
                    array(
                        'Text',
                        'limit',
                        array(
                            'label' => 'Count',
                            'description' => '(number of Ads to show).',
                            'value' => 3,
                        )
                    ),
                ),
            ),
    ));
}

$video_widgets = array(
    array(
        'title' => 'Video View Page: People Also Liked',
        'description' => 'Displays a list of other Event Videos that the people who liked this Event Video also liked. You can choose the number of entries to be shown. This widget should be placed on Advanced Events - Video View Page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.show-also-liked',
        'defaultParams' => array(
            'title' => 'People Also Liked',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of videos to show)',
                        'value' => 3,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    ),
                ),
            ),
        ),
        'requirements' => array(
            'subject' => 'siteevent',
        ),
    ),
    array(
        'title' => 'Video View Page: Other Videos From Event',
        'description' => 'Displays a list of other Event Videos corresponding to the Event of which the video is being viewed. You can choose the number of entries to be shown. This widget should be placed on Advanced Events - Video View Page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.show-same-poster',
        'defaultParams' => array(
            'title' => 'Other Videos From Event',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of videos to show)',
                        'value' => 3,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    ),
                ),
            ),
        ),
        'requirements' => array(
            'subject' => 'siteevent',
        ),
    ),
    array(
        'title' => 'Video View Page: Similar Videos',
        'description' => 'Displays Event Videos similar to the Event Video being viewed based on tags. You can choose the number of entries to be shown. This widget should be placed on Advanced Events - Video View Page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'siteevent.show-same-tags',
        'defaultParams' => array(
            'title' => 'Similar Videos',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of videos to show)',
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
);

if (empty($type_video)) {
    $final_array = array_merge($final_array, $video_widgets);
}

if (!empty($ads_Array)) {
    $final_array = array_merge($final_array, $ads_Array);
}

return $final_array;