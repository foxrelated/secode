<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$isActive = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.isActivate', 0);
if (empty($isActive)) {
  return;
}

$routeStartP = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.manifestUrlP', "groupitems");
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
$ads_Array = $categories_prepared = array();
$categories = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategories();
if (count($categories) != 0) {
  $categories_prepared[0] = "";
  foreach ($categories as $category) {
    $categories_prepared[$category->category_id] = $category->category_name;
  }
}
$linkDescription = 'Displays various links like  "Groups I Admin", "Groups I\'ve Claimed" and "Groups I Like" on your site. This widget should be placed on Groups / Communities - Manage Groups page.';       
$linksOptions = array("groupAdmin" => "Groups I Admin", "groupClaimed" => "Groups I\'ve Claimed", 'groupLiked' => 'Groups I Like');
$linksValues = array("groupAdmin","groupClaimed","groupLiked");

if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
  $linksOptions = array_merge($linksOptions, array('groupsJoined' => 'Groups I\'ve Joined'));
  $linksValues[] =  "groupsJoined";  
  $linkDescription = 'Displays various links like  "Groups I Admin", "Groups I\'ve Claimed", "Groups I Like" and "Groups I\'ve Joined" on your site. This widget should be placed on Groups / Communities - Manage Groups page.'; 
}
$detactLocationElement = array(
    'Select',
    'detactLocation',
    array(
        'label' => 'Do you want to display members based on user’s current location?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => '0'
    )
);

$showProfileField = array(
	'Radio',
	'showProfileField',
	array(
		'label' => 'Do you want to show custom field?',
		'multiOptions' => array(
				1 => 'Yes',
				0 => 'No'
		),
		'value' => 0,
	),
);
$customFieldHeading = array(
    'Radio',
    'custom_field_heading',
    array(
        'label' => 'Do you want to show "Heading" of custom field?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => '0'
    )
);

$customFieldTitle = array(
    'Radio',
    'custom_field_title',
    array(
        'label' => 'Do you want to show “Title" of custom field?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => '0'
    )
);

$customParamsCount = array(
		'Text',
		'customFieldCount',
		array(
				'label' => $view->translate('Custom Profile Fields'),
				'description' => $view->translate('(number of profile fields to show.)'),
				'value' => 2,
		)
);

$featuredSponsoredElement = array(
    'Select',
    'fea_spo',
    array(
        'label' => 'Show Groups',
        'multiOptions' => array(
            '' => '',
            'featured' => 'Featured Only',
            'sponsored' => 'Sponsored Only',
            'fea_spo' => 'Both Featured and Sponsored',
        ),
        'value' => 'sponsored',
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
            '3' => 'Auto Load Groups on Scrolling Down'),
        'value' => 2,
    )
);

if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {

  $popularity_options = array(
      'view_count' => 'Most Viewed',
      'like_count' => 'Most Liked',
      'comment_count' => 'Most Commented',
      'review_count' => 'Most Reviewed',
      'rating' => 'Most Rated',
      'group_id' => 'Most Recent',
      'modified_date' => 'Recently Updated',
  );
} else {
  $popularity_options = array(
      'view_count' => 'Most Viewed',
      'like_count' => 'Most Liked',
      'comment_count' => 'Most Commented',
      'group_id' => 'Most Recent',
      'modified_date' => 'Recently Updated',
  );
}

$category_groups_multioptions = array(
    'view_count' => 'Views',
    'like_count' => 'Likes',
    'comment_count' => 'Comments',
);
$pinboardShowsOptions = array(
    "viewCount" => "Views",
    "likeCount" => "Likes",
    "commentCount" => "Comments",
    'followCount' => 'Followers',
    "price" => 'Price',
    "location" => "Location"
);

if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
  $pinboardShowsOptions['reviewsRatings'] = "Reviews & Ratings";
}

$pinboardPopularityOptions = array(
    'view_count' => 'Most Viewed',
    'like_count' => 'Most Liked',
    'comment_count' => 'Most Commented',
    'follow_count' => 'Most Following',
    'group_id' => 'Most Recent',
    'modified_date' => 'Recently Updated',
);

$showContent_timeline = array("mainPhoto" => "Group Profile Photo", "title" => "Group Title", "followButton" => "Follow Button", "likeButton" => "Like Button", "likeCount" => "Total Likes", "followCount" => "Total Followers");
$showContent_option = array("mainPhoto", "title", "followButton", "likeButton", "followCount", "likeCount");

$layouts_tabs = array("0" => "1", "1" => "2", "2" => "3", "3" => "4", "4" => '5');
$layouts_tabs_options = array("1" => "Recent", "2" => "Most Popular", "3" => "Random", "4" => "Featured", "5" => "Sponsored");
$joined_order = array();

if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
  $showContent_timeline['memberCount'] = 'Total Members';
  $showContent_timeline['addButton'] = 'Add People Button';
  $showContent_timeline['joinButton'] = 'Join Group Button';

  $showContent_option[] = 'addButton';
  $showContent_option[] = 'joinButton';
  $showContent_option[] = 'memberCount';

  $pinboardShowsOptions['memberCount'] = 'Members';
  $pinboardPopularityOptions['member_count'] = "Most Joined Groups";
  $layouts_tabs['5'] = "6";
  $layouts_tabs_options["6"] = "Most Joined Groups";
  $joined_order = array(
      'Text',
      'joined_order',
      array(
          'label' => 'Most Joined Groups Tab (order)',
      ),
  );

}



$statisticsElement = array("likeCount" => "Likes", "followCount" => "Followers", "viewCount" => "Views", "commentCount" => "Comments");
$statisticsElementValue = array("viewCount", "likeCount", "followCount", "commentCount");


$statisticsBrowseElement = $statisticsElement;
$statisticsBrowseElementValue = $statisticsElementValue;
  
if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
  $statisticsElement['reviewCount'] = "Reviews";
  $statisticsElementValue[] = "reviewCount";
}
if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
  $statisticsElement['memberCount'] = "Members";
  $statisticsElementValue[] = "memberCount";
  
  $statisticsMemmberElement['memberApproval'] = "Join immediately / Must be approved";
  $statisticsMemmberElementstatisticsElementValue[] = "memberApproval";
  
  $statisticsBrowseElement = array_merge($statisticsElement, $statisticsMemmberElement);
  $statisticsBrowseElementValue = array_merge($statisticsElementValue, $statisticsMemmberElementstatisticsElementValue);
}


if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
  $category_groups_multioptions['member_count'] = 'Members';
}
if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
  $category_groups_multioptions['review_count'] = 'Reviews';
}
if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.proximity.search.kilometer', 0)) {
  $locationDescription = "Choose the kilometers within which groups will be displayed. (This setting will only work, if you have chosen 'Yes' in the above setting.)";
  $locationLableS = "Kilometer";
  $locationLable = "Kilometers";
} else {
  $locationDescription = "Choose the miles within which groups will be displayed. (This setting will only work, if you have chosen 'Yes' in the above setting.)";
  $locationLableS = "Mile";
  $locationLable = "Miles";
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


$final_array = array(
    array(
        'title' => 'Group Archives',
        'description' => 'Displays the month-wise archives for the groups posted on your site.',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroup.archives-sitegroup',
        'defaultParams' => array(
            'title' => 'Archives',
            'titleCount' => true,
        )
    ),
    array(
        'title' => 'Navigation Tabs',
        'description' => 'Displays the Navigation tabs groups having links of Groups Home, Browse Groups, etc. This widget should be placed at the top of Groups Home and Browse Groups groups.',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroup.browsenevigation-sitegroup',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Categories, Sub-categories and 3<sup>rd</sup> Level-categories (sidebar)',
        'description' => 'Displays the Categories, Sub-categories and 3<sup>rd</sup> Level-categories of groups in an expandable form. Clicking on them will redirect the viewer to the list of groups created in that category.',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroup.categories-sitegroup',
        'defaultParams' => array(
            'title' => 'Categories',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Categories, Sub-categories and 3<sup>rd</sup> Level-categories',
        'description' => 'Displays the Categories, Sub-categories and 3<sup>rd</sup> Level-categories of groups in an expandable form. Clicking on them will redirect the viewer to the list of groups created in that category.',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroup.categories',
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
                        'label' => 'Do you want all the categories, sub-categories and 3rd level categories to be shown to the users even if they have 0 groups in them? (Note: Selecting "Yes" will display all the categories WITHOUT  the count of the groups created in them if "Browse by Networks" are enabled  from the Global Settings of Groups / Communities Plugin and display all the categories with count if selected "No" for Browse by Networks. While selecting "No" here will display categories with count of the groups.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
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
                        'label' => 'Do you want to show 3rd level category to the viewer? (This setting will depend on above setting.)',
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
        'title' => 'Profile Groups',
        'description' => 'Displays members\' groups on their profile.',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroup.profile-sitegroup',
        'defaultParams' => array(
            'title' => 'Groups',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'groupAdmin',
                    array(
                        'label' => 'Which all Groups related to the user do you want to display in this tab widget on their profile?',
                        'multiOptions' => array(
                            1 => 'Groups Owned by the user. (Group Owner)',
                            2 => 'Groups Administered by the user. (Group Admin)'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categories_prepared,
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
        ),
    ),
    array(
        'title' => 'Featured Groups Slideshow',
        'description' => 'Displays the Featured Groups in the form of an attractive Slideshow with interactive controls.',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroup.slideshow-sitegroup',
        'defaultParams' => array(
            'title' => 'Featured Groups',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of groups to show)',
                        'value' => 10,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categories_prepared,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Sponsored Groups Slideshow',
        'description' => 'Displays the Sponsored Groups in the form of an attractive Slideshow with interactive controls.',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroup.sponsored-slideshow-sitegroup',
        'defaultParams' => array(
            'title' => 'Sponsored Groups',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of groups to show)',
                        'value' => 10,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categories_prepared,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Post a New Group',
        'description' => 'Displays the link to Post a New Group.',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroup.newgroup-sitegroup',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        )
    ),
    array(
        'title' => 'Most Commented Groups',
        'description' => 'Displays the list of Groups having maximum number of comments.',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroup.mostcommented-sitegroup',
        'defaultParams' => array(
            'title' => 'Most Commented Groups',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of groups to show)',
                        'value' => 3,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categories_prepared,
                    )
                ),
                array(
                    'Select',
                    'featured',
                    array(
                        'label' => 'Featured',
                        'multiOptions' => array(
                            0 => '',
                            2 => 'Yes',
                            1 => 'No',
                        ),
                    )
                ),
                array(
                    'Select',
                    'sponsored',
                    array(
                        'label' => 'Sponsored',
                        'multiOptions' => array(
                            0 => '',
                            2 => 'Yes',
                            1 => 'No',
                        ),
                    )
                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => 'Time Period',
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Most Followed Groups',
        'description' => 'Displays a list of groups having maximum number of followers. You can choose the number of entries to be shown.',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroup.mostfollowers-sitegroup',
        'defaultParams' => array(
            'title' => 'Most Followed Groups',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of groups to show)',
                        'value' => 3,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    ),
                ),
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categories_prepared,
                    )
                ),
                array(
                    'Select',
                    'featured',
                    array(
                        'label' => 'Featured',
                        'multiOptions' => array(
                            0 => '',
                            2 => 'Yes',
                            1 => 'No',
                        ),
                    )
                ),
                array(
                    'Select',
                    'sponsored',
                    array(
                        'label' => 'Sponsored',
                        'multiOptions' => array(
                            0 => '',
                            2 => 'Yes',
                            1 => 'No',
                        ),
                    )
                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => 'Time Period',
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Most Liked Groups',
        'description' => 'Displays list of groups having maximum number of likes.',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroup.mostlikes-sitegroup',
        'defaultParams' => array(
            'title' => 'Most Liked Groups',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of groups to show)',
                        'value' => 3,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    ),
                ),
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categories_prepared,
                    )
                ),
                array(
                    'Select',
                    'featured',
                    array(
                        'label' => 'Featured',
                        'multiOptions' => array(
                            0 => '',
                            2 => 'Yes',
                            1 => 'No',
                        ),
                    )
                ),
                array(
                    'Select',
                    'sponsored',
                    array(
                        'label' => 'Sponsored',
                        'multiOptions' => array(
                            0 => '',
                            2 => 'Yes',
                            1 => 'No',
                        ),
                    )
                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => 'Time Period',
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Popular Group Tags',
        'description' => 'Shows popular tags with frequency.',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroup.tagcloud-sitegroup',
        'adminForm' => array(
            'elements' => array(
                array(
                    'hidden',
                    'title',
                    array(
                        'label' => ''
                    )
                ),
                array(
                    'hidden',
                    'nomobile',
                    array(
                        'label' => ''
                    )
                ),
//          array(
//            'hidden',
//            'execute',
//            array(
//              'label' => ''
//            )
//          ),
//          array(
//            'hidden',
//            'cancel',
//            array(
//              'label' => ''
//            )
//          ),
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categories_prepared,
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Recent Groups',
        'description' => 'Displays list of recently created Groups.',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroup.recentlyposted-sitegroup',
        'defaultParams' => array(
            'title' => 'Recent',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of groups to show)',
                        'value' => 10,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categories_prepared,
                    )
                ),
                array(
                    'Select',
                    'featured',
                    array(
                        'label' => 'Featured',
                        'multiOptions' => array(
                            0 => '',
                            2 => 'Yes',
                            1 => 'No',
                        ),
                    )
                ),
                array(
                    'Select',
                    'sponsored',
                    array(
                        'label' => 'Sponsored',
                        'multiOptions' => array(
                            0 => '',
                            2 => 'Yes',
                            1 => 'No',
                        ),
                    )
                ),
                array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => 'Choose the statistics that you want to be displayed for the Groups in this block.',
                        'multiOptions' => $statisticsElement,
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Popular Groups',
        'description' => 'Displays list of popular groups on the site.',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroup.mostviewed-sitegroup',
        'defaultParams' => array(
            'title' => 'Most Popular',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of groups to show)',
                        'value' => 10,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categories_prepared,
                    )
                ),
                array(
                    'Select',
                    'featured',
                    array(
                        'label' => 'Featured',
                        'multiOptions' => array(
                            0 => '',
                            2 => 'Yes',
                            1 => 'No',
                        ),
                    )
                ),
                array(
                    'Select',
                    'sponsored',
                    array(
                        'label' => 'Sponsored',
                        'multiOptions' => array(
                            0 => '',
                            2 => 'Yes',
                            1 => 'No',
                        ),
                    )
                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => 'Time Period',
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Browse Groups: Pinboard View',
        'description' => 'Displays a list of all the groups on site in attractive Pinboard View. You can also choose to display groups based on user’s current location by using the Edit Settings of this widget. It is recommended to place this widget on "Browse Groups\'s Pinboard View" group. ',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroup.pinboard-browse',
        'autoEdit' => true,
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'postedby',
                    array(
                        'label' => 'Show posted by option. (Selecting "Yes" here will display the member\'s name who has created the group.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'showfeaturedLable',
                    array(
                        'label' => 'Do you want “Featured Label” for the Groups to be displayed in block?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showsponsoredLable',
                    array(
                        'label' => 'Do you want “Sponsored Label”  for the Groups to be displayed in block?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'MultiCheckbox',
                    'showoptions',
                    array(
                        'label' => 'Choose the options that you want to be displayed for the Groups in this block.',
                        'multiOptions' => $pinboardShowsOptions,
                    //'value' =>array("viewCount","likeCount","commentCount","reviewCount"),
                    ),
                ),
                array(
                    'Select',
                    'detactLocation',
                    array(
                        'label' => 'Do you want to display groups based on user’s current location?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '0'
                    )
                ),
                array(
                    'Select',
                    'defaultlocationmiles',
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
                ),
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categories_prepared,
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
                        'description' => '(number of Groups to show)',
                        'value' => 12,
                    )
                ),
                array(
                    'MultiCheckbox',
                    'show_buttons',
                    array(
                        'label' => 'Choose the action links that you want to be available for the Groups displayed in this block.',
                        'multiOptions' => array("comment" => "Comment", "like" => "Like / Unlike", 'share' => 'Share', 'facebook' => 'Facebook', 'twitter' => 'Twitter', 'pinit' => 'Pin it', 'tellAFriend' => 'Tell a Friend', 'print' => 'Print')
                    //'value' =>array("viewCount","likeCount","commentCount","reviewCount"),
                    ),
                ),
                array(
                    'Text',
                    'truncationDescription',
                    array(
                        'label' => "Enter the truncation limit for the Group Description. (If you want to hide the description, then enter '0'.)",
                        'value' => 100,
                    )
                ),
                array(
                    'Select',
                    'commentSection',
                    array(
                        'label' => 'Do you want to display comments?',
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
                        'label' => 'Do you want to show a Loading image when this widget renders on a group?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '0'
                    )
                ),
// 						array(
// 							'Radio',
// 							'getdirection',
// 							array(
// 									'label' => 'Show get direction link.',
// 									'multiOptions' => array(
// 											1 => 'Yes',
// 											0 => 'No'
// 									),
// 									'value' => 1,
// 							),
// 						),
            ),
        )
    ),
//     array(
//         'title' => 'Group Profile  Cover Photo and Information',
//         'description' => 'Displays the group cover photo with group profile photo, title and various action links that can be performed on the group from their Profile group (Like, Follow, etc.). You can choose various options from the Edit Settings of this widget. This widget should be placed on the Group Profile group.',
//         'category' => 'Group Profile',
//         'type' => 'widget',
//         'name' => 'sitegroup.group-cover-information-sitegroup',
//         'defaultParams' => array(
//             'title' => 'Information',
//             'titleCount' => true,
//             'showContent' => $showContent_option
//         ),
//         'adminForm' => array(
//             'elements' => array(
//                 array(
//                     'MultiCheckbox',
//                     'showContent',
//                     array(
//                         'label' => 'Select the information options that you want to be available in this block.',
//                         'multiOptions' => $showContent_timeline,
//                     ),
//                 ), 
//                 array(
//                     'Text',
//                     'columnHeight',
//                     array(
//                         'label' => 'Enter the cover photo height (in px). (Minimum 150 px required.)',
//                         'value' => '300',
//                     )
//                 ),             
//             ),
//         ),
//     ),
    array(
        'title' => 'Browse Groups’ Locations',
        'description' => 'Displays a list of all the groups having location entered corresponding to them on the site. This widget should be placed on Browse Groups’ Locations group.',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroup.browselocation-sitegroup',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
// 						array(
// 							'Radio',
// 							'getdirection',
// 							array(
// 									'label' => 'Show get direction link.',
// 									'multiOptions' => array(
// 											1 => 'Yes',
// 											0 => 'No'
// 									),
// 									'value' => 1,
// 							),
// 						),
            ),
        )
    ),
    array(
        'title' => 'Browse Groups',
        'description' => 'Displays a list of all the groups on site. This widget should be placed on the Browse Groups group.',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroup.groups-sitegroup',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'layouts_views' => array("0" => "1", "1" => "2", "2" => "3"),
            'layouts_oder' => 1,
            'columnWidth' => 100,
            'statistics' => $statisticsBrowseElementValue,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'layouts_views',
                    array(
                        'label' => 'Choose the view types that you want to be available for groups on the groups home and browse groups.',
                        'multiOptions' => array("1" => "List View", "2" => "Grid View", "3" => "Map View")
                    ),
                ),
                array(
                    'Radio',
                    'layouts_oder',
                    array(
                        'label' => 'Select a default view type for Groups / Communities.',
                        'multiOptions' => array("1" => "List View", "2" => "Grid View", "3" => "Map View")
                    )
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => 'Column Width For Grid View.',
                        'value' => '100',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For Grid View.',
                        'value' => '350',
                    )
                ),
                array(
                    'Text',
                    'turncation',
                    array(
                        'label' => 'Title Truncation Limit For Grid View.',
                        'value' => '40',
                    )
                ),
                array(
                    'Radio',
                    'showlikebutton',
                    array(
                        'label' => 'Do you want to show “Like Button” when users mouse over on Groups in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showfeaturedLable',
                    array(
                        'label' => 'Do you want “Featured Label” for the Groups to be displayed in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showsponsoredLable',
                    array(
                        'label' => 'Do you want “Sponsored Label”  for the Groups to be displayed in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showlocation',
                    array(
                        'label' => 'Do you want “Location” of the Groups to be displayed in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showgetdirection',
                    array(
                        'label' => 'Do you want “Get Direction” link for the groups having location to be displayed in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showprice',
                    array(
                        'label' => 'Do you want “Price” of the Groups to be displayed?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showpostedBy',
                    array(
                        'label' => 'Do you want “Posted By” of the Groups to be displayed in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showdate',
                    array(
                        'label' => 'Do you want “Creation Date” of the Groups to be displayed in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showContactDetails',
                    array(
                        'label' => 'Do you want “Contact Details” of the Groups to be displayed in list and map view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => 'Choose the statistics that you want to be displayed for the Listings in this block.',
                        'multiOptions' => $statisticsBrowseElement,
                    ),
                ),
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categories_prepared,
                    )
                ),
                $showProfileField,
								$customFieldHeading,
								$customFieldTitle,
        $showViewMoreContent,
								$customParamsCount,
                $detactLocationElement,
                $defaultLocationDistanceElement,
            ),
        )
    ),
    array(
        'title' => 'Search Groups form',
        'description' => 'Displays the form for searching Groups on the basis of various filters.',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroup.search-sitegroup',
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
                    'locationDetection',
                    array(
                        'label' => "Allow browser to detect user's current location.",
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
        'title' => 'Random Groups',
        'description' => 'Displays list of Groups randomly.',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroup.random-sitegroup',
        'defaultParams' => array(
            'title' => 'Random',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of groups to show)',
                        'value' => 3,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categories_prepared,
                    )
                ),
                array(
                    'Select',
                    'featured',
                    array(
                        'label' => 'Featured',
                        'multiOptions' => array(
                            0 => '',
                            2 => 'Yes',
                            1 => 'No',
                        ),
                    )
                ),
                array(
                    'Select',
                    'sponsored',
                    array(
                        'label' => 'Sponsored',
                        'multiOptions' => array(
                            0 => '',
                            2 => 'Yes',
                            1 => 'No',
                        ),
                    )
                ),
                array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => 'Choose the statistics that you want to be displayed for the Groups in this block.',
                        'multiOptions' => $statisticsElement,
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Sponsored Groups Carousel',
        'description' => 'This widget contains an attractive AJAX based carousel, showcasing the sponsored Groups on the site.',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroup.sponsored-sitegroup',
        'defaultParams' => array(
            'title' => 'Sponsored Groups',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of groups to show)',
                        'value' => 4,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Text',
                    'interval',
                    array(
                        'label' => 'Sponsored Carousel Speed',
                        'description' => '(What maximum Carousel Speed should be applied to the sponsored widget?)',
                        'value' => 300,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categories_prepared,
                    )
                ),
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation Limit',
                        'description' => '(What maximum limit should be applied to the number of characters in the titles of items in the Sponsored widgets? (Enter a number between 1 and 999. Titles having more characters than this limit will be truncated. Complete titles will be shown on mouseover.))',
                        'value' => 18,
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
        'title' => 'AJAX based Groups Carousel',
        'description' => 'This widget contains an attractive AJAX based carousel, showcasing the groups on the site. You can choose to show sponsored / featured in this widget from the settings of this widget. You can place this widget multiple times on a group with different criterion chosen for each placement.',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroup.ajax-carousel-sitegroup',
        'defaultParams' => array(
            'title' => 'Groups Carousel',
            'titleCount' => true,
            'statistics' => $statisticsElementValue,
        ),
        'adminForm' => array(
            'elements' => array(
                $featuredSponsoredElement,
                array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => 'Choose the statistics that you want to be displayed for the Listings in this block.',
                        'multiOptions' => $statisticsElement,
                    ),
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
                        'value' => '1',
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
                    'Select',
                    'popularity',
                    array(
                        'label' => 'Popularity Criteria',
                        'multiOptions' => $popularity_options,
                        'value' => 'listing_id',
                    )
                ),
                array(
                    'Radio',
                    'featuredIcon',
                    array(
                        'label' => 'Do you want to show the featured icon / label. (You can choose the marker from the \'Global Settings\' section in the Admin Panel.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'sponsoredIcon',
                    array(
                        'label' => 'Do you want to show the sponsored icon / label. (You can choose the marker from the \'Global Settings\' section in the Admin Panel.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of groups to show)',
                        'value' => 4,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Text',
                    'interval',
                    array(
                        'label' => 'Sponsored Carousel Speed',
                        'description' => '(What maximum Carousel Speed should be applied to the sponsored widget?)',
                        'value' => 300,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categories_prepared,
                    )
                ),
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation Limit',
                        'description' => '(What maximum limit should be applied to the number of characters in the titles of items in the Sponsored widgets? (Enter a number between 1 and 999. Titles having more characters than this limit will be truncated. Complete titles will be shown on mouseover.))',
                        'value' => 50,
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
        'title' => 'Group of the Day',
        'description' => 'Displays the Group of the Day as selected by the Admin from the widget settings section of this plugin.',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroup.item-sitegroup',
        'defaultParams' => array(
            'title' => 'Group of the Day',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Recently Viewed Groups',
        'description' => 'Displays list of recently viewed Groups on the site.',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroup.recentview-sitegroup',
        'defaultParams' => array(
            'title' => 'Recently Viewed',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of groups to show)',
                        'value' => 3,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categories_prepared,
                    )
                ),
                array(
                    'Select',
                    'featured',
                    array(
                        'label' => 'Featured',
                        'multiOptions' => array(
                            0 => '',
                            2 => 'Yes',
                            1 => 'No',
                        ),
                    )
                ),
                array(
                    'Select',
                    'sponsored',
                    array(
                        'label' => 'Sponsored',
                        'multiOptions' => array(
                            0 => '',
                            2 => 'Yes',
                            1 => 'No',
                        ),
                    )
                ),
                array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => 'Choose the statistics that you want to be displayed for the Groups in this block.',
                        'multiOptions' => $statisticsElement,
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Recently Viewed By Friends',
        'description' => 'Displays list of Groups recently viewed by friends.',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroup.recentfriend-sitegroup',
        'defaultParams' => array(
            'title' => 'Recently Viewed By Friends',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of groups to show)',
                        'value' => 3,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categories_prepared,
                    )
                ),
                array(
                    'Select',
                    'featured',
                    array(
                        'label' => 'Featured',
                        'multiOptions' => array(
                            0 => '',
                            2 => 'Yes',
                            1 => 'No',
                        ),
                    )
                ),
                array(
                    'Select',
                    'sponsored',
                    array(
                        'label' => 'Sponsored',
                        'multiOptions' => array(
                            0 => '',
                            2 => 'Yes',
                            1 => 'No',
                        ),
                    )
                ),
                array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => 'Choose the statistics that you want to be displayed for the Groups in this block.',
                        'multiOptions' => $statisticsElement,
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Message for Zero Groups',
        'description' => 'This widget should be placed in the top of the middle column of the Groups Home group.',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroup.zerogroup-sitegroup',
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
        'title' => 'Close Group Message',
        'description' => 'If a Group is closed, then show this message.',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroup.closegroup-sitegroup',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Popular Locations',
        'description' => 'Displays list of popular locations of Groups.',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroup.popularlocations-sitegroup',
        'defaultParams' => array(
            'title' => 'Popular Locations',
            'titleCount' => "",
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of locations to show)',
                        'value' => 3,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categories_prepared,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Group Profile Overview',
        'description' => 'Displays rich overview on Group\'s profile, created by its admin using the editor from Group Dashboard. This should be placed in the Tabbed Blocks area of Group Profile.',
        'category' => 'Group Profile',
        'type' => 'widget',
        'name' => 'sitegroup.overview-sitegroup',
        'defaultParams' => array(
            'title' => 'Overview',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Group Profile Breadcrumb',
        'description' => 'Displays breadcrumb of the group based on the categories. This widget should be placed on the Group Profile group.',
        'category' => 'Group Profile',
        'type' => 'widget',
        'name' => 'sitegroup.group-profile-breadcrumb',
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => 'Sub Groups of a Group',
        'description' => 'Displays the sub groups created in the Group which is being viewed currently. This widget should be placed on the Group Profile group.',
        'category' => 'Group Profile',
        'type' => 'widget',
        'name' => 'sitegroup.subgroup-sitegroup',
        'defaultParams' => array(
            'title' => 'Sub Groups of a Group',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of sub-groups to show)',
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
        'title' => 'Parent Group of a Sub Group',
        'description' => 'Displays the parent group in which the currently viewed sub groups is created. This widget should be placed on the Group Profile group.',
        'category' => 'Group Profile',
        'type' => 'widget',
        'name' => 'sitegroup.parentgroup-sitegroup',
        'defaultParams' => array(
            'title' => 'Parent Group of a Sub Group',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of parent-groups to show)',
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
//    array(
//        'title' => "Group Profile 'Save to foursquare' Button",
//        'description' => "This Button will enable group visitors to add the Group\'s place or tip to their foursquare To-Do List. There is also Member Level and Package setting for this button.",
//        'category' => 'Group Profile',
//        'type' => 'widget',
//        'name' => 'sitegroup.foursquare-sitegroup',
//        'defaultParams' => array(
//            'title' => '',
//            'titleCount' => true,
//        ),
//    ),
    array(
        'title' => 'Group Profile Social Share Buttons',
        'description' => "Contains Social Sharing buttons and enables users to easily share Groups on their favorite Social Networks. This widget should be placed on the Group View Group. You can customize the code for social sharing buttons from Global Settings of this plugin by adding your own code generated from: <a href='http://www.addthis.com' target='_blank'>http://www.addthis.com</a>",
        'category' => 'Group Profile',
        'type' => 'widget',
        'name' => 'sitegroup.socialshare-sitegroup',
        'defaultParams' => array(
            'title' => 'Social Share',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'sitegroup_group',
        ),
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Hidden',
                    'nomobile',
                    array(
                        'label' => '',
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Group Profile Title',
        'description' => 'Displays the Title of the Group. This widget should be placed on the Group Profile, in the middle column at the top.',
        'category' => 'Group Profile',
        'type' => 'widget',
        'name' => 'sitegroup.title-sitegroup',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Group Profile Info',
        'description' => 'This widget forms the Info tab on the Group Profile and displays the information of the Group. It should be placed in the Tabbed Blocks area of the Group Profile.',
        'category' => 'Group Profile',
        'type' => 'widget',
        'name' => 'sitegroup.info-sitegroup',
        'defaultParams' => array(
            'title' => 'Info',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Group Profile Information Group',
        'description' => 'Displays the owner, category, tags, views and other information about a Group. This widget should be placed on the Group Profile in the left column.',
        'category' => 'Group Profile',
        'type' => 'widget',
        'name' => 'sitegroup.information-sitegroup',
        'defaultParams' => array(
            'title' => 'Information',
            'titleCount' => true,
            'showContent' => array("ownerPhoto", "ownerName", "modifiedDate", "viewCount", "likeCount", "commentCount", "tags", "location", "price", "memberCount", "followerCount", "categoryName")
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'showContent',
                    array(
                        'label' => 'Select the information options that you want to be available in this block.',
                        'multiOptions' => array("ownerPhoto" => "Group Owner's Photo", "ownerName" => "Owner's Name", "modifiedDate" => "Modified Date", "viewCount" => "Views", "likeCount" => "Likes", "commentCount" => "Comments", "tags" => "Tags", "location" => "Location", "price" => "Price", "memberCount" => 'Member', "followerCount" => 'Follower', "categoryName" => "Category"),
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Group Profile Photo',
        'description' => 'Displays the main cover photo of a Group. This widget must be placed on the Group Profile at the top of left column.',
        'category' => 'Group Profile',
        'type' => 'widget',
        'name' => 'sitegroup.mainphoto-sitegroup',
        'defaultParams' => array(
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Group Profile Map',
        'description' => 'This widget forms the Map tab on the Group Profile. It displays the map showing the Group position as well as the location details of the group. It should be placed in the Tabbed Blocks area of the Group Profile. This feature will be available to Groups based on their Package and Member Level of their owners.',
        'category' => 'Group Profile',
        'type' => 'widget',
        'name' => 'sitegroup.location-sitegroup',
        'defaultParams' => array(
            'title' => 'Map',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Group Profile Options',
        'description' => 'Displays the various action link options to users viewing a Group. This widget should be placed on the Group Profile in the left column, below the Group profile photo.',
        'category' => 'Group Profile',
        'type' => 'widget',
        'name' => 'sitegroup.options-sitegroup',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Group Profile Owner Group Tags',
        'description' => 'Displays all the tags chosen by the Group owner for his Groups. This widget should be placed on the Group Profile.',
        'category' => 'Group Profile',
        'type' => 'widget',
        'name' => 'sitegroup.tags-sitegroup',
        'defaultParams' => array(
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Group Profile Owner Groups',
        'description' => 'Displays list of other groups owned by the group owner.This widget should be placed on Group Profile.',
        'category' => 'Group Profile',
        'type' => 'widget',
        'name' => 'sitegroup.usergroup-sitegroup',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of groups to show)',
                        'value' => 3,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categories_prepared,
                    )
                ),
                array(
                    'Select',
                    'popularity',
                    array(
                        'label' => 'Popularity Criteria',
                        'multiOptions' => array(
                            'view_count' => 'Views',
                            'like_count' => 'Likes',
                        ),
                        'value' => 'view_count',
                    )
                ),
                array(
                    'Select',
                    'featured',
                    array(
                        'label' => 'Featured',
                        'multiOptions' => array(
                            0 => '',
                            2 => 'Yes',
                            1 => 'No',
                        ),
                    )
                ),
                array(
                    'Select',
                    'sponsored',
                    array(
                        'label' => 'Sponsored',
                        'multiOptions' => array(
                            0 => '',
                            2 => 'Yes',
                            1 => 'No',
                        ),
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Group Profile About Group',
        'description' => 'Displays the About Us information for groups. This widget should be placed on the Group Profile.',
        'category' => 'Group Profile',
        'type' => 'widget',
        'name' => 'sitegroup.write-group',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Content Profile: Follow Button',
        'description' => 'This is the Follow Button to be placed on the Content Profile page. It enables users to Follow the content being currently viewed.',
        'category' => 'Group Profile',
        'type' => 'widget',
        'name' => 'seaocore.seaocore-follow',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    array(
        'title' => 'Content Profile: Like Button for Content',
        'description' => 'This is the Like Button to be placed on the Content Profile page. It enables users to Like the content being currently viewed. The best place to put this widget is right above the Tabbed Blocks on the Content Profile page.',
        'category' => 'Group Profile',
        'type' => 'widget',
        'name' => 'seaocore.like-button',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    array(
        'title' => 'Group Profile You May Also Like',
        'description' => 'Displays list of groups that might be liked by user based on the group being currently viewed.This widget should be placed on the Group Profile.',
        'category' => 'Group Profile',
        'type' => 'widget',
        'name' => 'sitegroup.suggestedgroup-sitegroup',
        'defaultParams' => array(
            'title' => 'You May Also Like',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of groups to show)',
                        'value' => 3,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Select',
                    'featured',
                    array(
                        'label' => 'Featured',
                        'multiOptions' => array(
                            0 => '',
                            2 => 'Yes',
                            1 => 'No',
                        ),
                    )
                ),
                array(
                    'Select',
                    'sponsored',
                    array(
                        'label' => 'Sponsored',
                        'multiOptions' => array(
                            0 => '',
                            2 => 'Yes',
                            1 => 'No',
                        ),
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Content Profile: Content Likes',
        'description' => 'Displays the users who have liked the content being currently viewed. This widget should be placed on the  Content Profile page.',
        'category' => 'Group Profile',
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
        'title' => 'Group Profile Group Insights',
        'description' => 'Displays the insights of a Group to its Group Admins. These insights include metrics like views, likes, comments and active users of the Group. This widget should be placed on the Group Profile.',
        'category' => 'Group Profile',
        'type' => 'widget',
        'name' => 'sitegroup.insights-sitegroup',
        'defaultParams' => array(
            'title' => 'Insights',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Group Profile Alternate Thumb Photo',
        'description' => 'Displays the thumb photo of a Group. This works as an alternate profile photo when you have set the layout of Group Profile to be tabbed, from the Group Layout Settings, and have integrated with the "Advertisements / Community Ads Plugin" by SocialEngineAddOns. In that case, the left column of the Group Profile having the main profile photo gets hidden to accomodate Ads. This widget must be placed on the Group Profile at the top of middle column.',
        'category' => 'Group Profile',
        'type' => 'widget',
        'name' => 'sitegroup.thumbphoto-sitegroup',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'showTitle',
                    array(
                        'label' => 'Show Group Profile Title.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
                        ),
                        'value' => 1,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Group Profile Apps Links',
        'description' => "Displays the Apps related links like Documents, Form, Photos, Poll, etc on Group Profile, depending on the Groups related apps installed on your site. This widget should be placed on the Group Profile in the left column.",
        'category' => 'Group Profile',
        'type' => 'widget',
        'name' => 'sitegroup.widgetlinks-sitegroup',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
        ),
    ),
    array(
        'title' => 'Group Profile Linked Groups',
        'description' => 'Displays list of groups linked to a group. This widget should be placed on the Group Profile.',
        'category' => 'Group Profile',
        'type' => 'widget',
        'name' => 'sitegroup.favourite-group',
        'defaultParams' => array(
            'title' => 'Linked Groups',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of groups to show)',
                        'value' => 3,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categories_prepared,
                    )
                ),
                array(
                    'Select',
                    'featured',
                    array(
                        'label' => 'Featured',
                        'multiOptions' => array(
                            0 => '',
                            2 => 'Yes',
                            1 => 'No',
                        ),
                    )
                ),
                array(
                    'Select',
                    'sponsored',
                    array(
                        'label' => 'Sponsored',
                        'multiOptions' => array(
                            0 => '',
                            2 => 'Yes',
                            1 => 'No',
                        ),
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Group Profile Featured Group Admins',
        'description' => "Displays the Featured Admins of a group. This widget should be placed on the Group Profile.",
        'category' => 'Group Profile',
        'type' => 'widget',
        'name' => 'sitegroup.featuredowner-sitegroup',
        'defaultParams' => array(
            'title' => "Group admins",
            'titleCount' => "",
        ),
    ),
    array(
        'title' => 'Horizontal Search Groups Form',
        'description' => "This widget searches over Group Titles, Locations and Categories. This widget should be placed in full-width / extended column. Multiple settings are available in the edit settings section of this widget.",
        'category' => 'Pages',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitegroup.horizontal-searchbox-sitegroup',
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
                        'value' => 0,
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
                        'label' => 'Do you want all the categories, sub-categories and 3rd level categories to be shown to the users even if they have 0 groups in them?',
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
        'title' => 'AJAX Search for Groups',
        'description' => "This widget searches over Group Titles via AJAX. The search interface is similar to Facebook search.",
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroup.searchbox-sitegroup',
        'defaultParams' => array(
            'title' => "Search",
            'titleCount' => "",
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categories_prepared,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Alphabetic Filtering of Groups',
        'description' => "This widget enables users to alphabetically filter the directory items / groups on your site by clicking on the desired alphabet. The widget shows all the alphabets for filtering.",
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroup.alphabeticsearch-sitegroup',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
        ),
    ),
    array(
        'title' => 'Categorically Popular Groups',
        'description' => 'This attractive widget categorically displays the most popular groups on your site. It displays 5 Groups for each category. From the edit popup of this widget, you can choose the number of categories to show, criteria for popularity and the duration for consideration of popularity.',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroup.category-groups-sitegroup',
        'defaultParams' => array(
            'title' => 'Popular Groups',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Category Count',
                        'description' => 'No. of Categories to show. Enter 0 for showing all categories.',
                        'value' => 0,
                    )
                ),
                array(
                    'Text',
                    'groupCount',
                    array(
                        'label' => 'Groups Count per Category',
                        'description' => 'No. of Groups to be shown in each Category.',
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
                        'label' => 'Popularity Criteria',
                        'multiOptions' => $category_groups_multioptions,
                        'value' => 'view_count',
// 											'onchange'=>'javascript:if($("popularity").value=="view_count"){ $("interval-wrapper").style.display = "none";}else{$("interval-wrapper").style.display = "block"; }',
                    )
                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => 'Popularity Duration (This duration will be applicable to all Popularity Criteria except Views.)',
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
                array(
                    'Select',
                    'columnCount',
                    array(
                        'label' => 'Select categories to be displayed in a row.',
                        'multiOptions' => array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5'),
                        'value' => '2',
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Group Profile Contact Details',
        'description' => "Displays the Contact Details of a group. This widget should be placed on the Group Profile.",
        'category' => 'Group Profile',
        'type' => 'widget',
        'name' => 'sitegroup.contactdetails-sitegroup',
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
                array(
                    'Radio',
                    'emailme',
                    array(
                        'label' => 'Do you want users to send emails to Groups via a customized pop up when they click on "Email Me" link?',
                        'multiOptions' => array(
                            1 => 'Yes, open customized pop up',
                            0 => 'No, open browser`s default pop up'
                        ),
                        'value' => '0'
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Group Profile Render Layout',
        'description' => "Displays the layout of the group when site header and footer are not rendered.",
        'category' => 'Group Profile',
        'type' => 'widget',
        'name' => 'sitegroup.onrender-sitegroup',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
        ),
    ),
    array(
        'title' => 'Popular / Recent / Random / Location Based Groups: Pinboard View',
        'description' => 'Displays groups based on popularity criteria, location and other settings that you want to choose for this widget in attractive Pinboard View. You can place this widget multiple times with different popularity criterion chosen for each placement. You can also choose to display groups based on user’s current location by using the Edit Settings of this widget.',
        'category' => 'Groups',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitegroup.pinboard-groups',
        'defaultParams' => array(
            'title' => 'Recent',
            'statistics' => array("likeCount", "commentCount"),
            'show_buttons' => array("comment", "like", 'share', 'facebook', 'twitter', 'pinit')
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categories_prepared,
                    )
                ),
                array(
                    'Select',
                    'fea_spo',
                    array(
                        'label' => 'Show Groups',
                        'multiOptions' => array(
                            '' => '',
                            'featured' => 'Featured Only',
                            'sponsored' => 'Sponsored Only',
                            'fea_spo' => 'Both Featured and Sponsored',
                        ),
                        'value' => '',
                    )
                ),
                array(
                    'Select',
                    'detactLocation',
                    array(
                        'label' => 'Do you want to display groups based on user’s current location? (Note:- For this you must be enabled the auto-loading.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '0'
                    )
                ),
                array(
                    'Select',
                    'locationmiles',
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
                ),
                array(
                    'Select',
                    'popularity',
                    array(
                        'label' => 'Popularity Criteria',
                        'multiOptions' => $pinboardPopularityOptions,
                        'value' => 'group_id',
                    )
                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => 'Popularity Duration (This duration will be applicable to these Popularity Criteria:  Most Liked, Most Commented, Most Rated and Most Recent.)',
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
                array(
                    'Radio',
                    'postedby',
                    array(
                        'label' => 'Show posted by option. (Selecting "Yes" here will display the member\'s name who has created the group.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'showoptions',
                    array(
                        'label' => 'Choose the options that you want to be displayed for the Groups in this block.',
                        'multiOptions' => $pinboardShowsOptions,
                    //'value' =>array("viewCount","likeCount","commentCount","reviewCount"),
                    ),
                ),
                array(
                    'Select',
                    'autoload',
                    array(
                        'label' => 'Do you want to enable auto-loading of old pinboard items when users scroll down to the bottom of this group?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1'
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
                        'description' => '(number of Groups to show)',
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
                        'label' => 'Choose the action links that you want to be available for the Groups displayed in this block.',
                        'multiOptions' => array("comment" => "Comment", "like" => "Like / Unlike", 'share' => 'Share', 'facebook' => 'Facebook', 'twitter' => 'Twitter', 'pinit' => 'Pin it', 'tellAFriend' => 'Tell a Friend', 'print' => 'Print')
                    //'value' =>array("viewCount","likeCount","commentCount","reviewCount"),
                    ),
                ),
                array(
                    'Text',
                    'truncationDescription',
                    array(
                        'label' => "Enter the truncation limit for the Group Description. (If you want to hide the description, then enter '0'.)",
                        'value' => 100,
                    )
                ),
            ),
        ),
    ),
//    
//        array(
//        'title' => 'Group Home: Pinboard View',
//        'description' => 'Displays groups in Pinboard View on the Groups Home group. Multiple settings are available to customize this widget.',
//        'category' => 'Groups',
//        'type' => 'widget',
//        'autoEdit' => true,
//        'name' => 'sitegroup.pinboard-groups-sitegroup',
//        'defaultParams' => array(
//            'title' => 'Recent',
//            'statistics' => array("likeCount", "reviewCount"),
//            'show_buttons' => array("comment", "like", 'share', 'facebook', 'twitter', 'pinit')
//        ),
//        'adminForm' => array(
//            'elements' => array(
//                //$listingTypeCategoryElement,
//               // $ratingTypeElement,
//                $featuredSponsoredElement,
//                array(
//                    'Select',
//                    'popularity',
//                    array(
//                        'label' => 'Popularity Criteria',
//                        'multiOptions' => $popularity_options,
//                        'value' => 'group_id',
//                    )
//                ),
//                array(
//                    'Select',
//                    'interval',
//                    array(
//                        'label' => 'Popularity Duration (This duration will be applicable to these Popularity Criteria:  Most Liked, Most Commented, Most Rated and Most Recent.)',
//                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall',
//                        'value' => 'overall',
//                    )
//                ),
//                //$categoryElement,
//                //$hiddenCatElement,
//                //$hiddenSubCatElement,
//                //$hiddenSubSubCatElement,
//                //$statisticsElement,
//                array(
//                    'Radio',
//                    'postedby',
//                    array(
//                        'label' => 'Show posted by option. (Selecting "Yes" here will display the member\'s name who has created the listing.)',
//                        'multiOptions' => array(
//                            1 => 'Yes',
//                            0 => 'No')
//                        ),
//                        'value' => '1',
//                    )
//                ),
//                array(
//                    'Select',
//                    'autoload',
//                    array(
//                        'label' => 'Do you want to enable auto-loading of old pinboard items when users scroll down to the bottom of this group?',
//                        'multiOptions' => array(
//                            1 => 'Yes',
//                            0 => 'No')
//                        ),
//                        'value' => '1'
//                    )
//                ),
//                array(
//                    'Text',
//                    'itemWidth',
//                    array(
//                        'label' => 'One Item Width',
//                        'description' => 'Enter the width for each pinboard item.',
//                        'value' => 237,
//                    )
//                ),
//                array(
//                    'Radio',
//                    'withoutStretch',
//                    array(
//                        'label' => 'Do you want to display the images without stretching them to the width of each pinboard item?',
//                        'multiOptions' => array(
//                            1 => 'Yes',
//                            0 => 'No')
//                        ),
//                        'value' => '0',
//                    )
//                ),
//                array(
//                    'Text',
//                    'itemCount',
//                    array(
//                        'label' => 'Count',
//                        'description' => '(number of Listings to show)',
//                        'value' => 3,
//                    )
//                ),
//                array(
//                    'Text',
//                    'noOfTimes',
//                    array(
//                        'label' => 'Auto-Loading Count',
//                        'description' => 'Enter the number of times that auto-loading of old pinboard items should occur on scrolling down. (Select 0 if you do not want such a restriction and want auto-loading to occur always. Because of auto-loading on-scroll, users are not able to click on links in footer; this setting has been created to avoid this.)',
//                        'value' => 0,
//                    )
//                ),
//                array(
//                    'MultiCheckbox',
//                    'show_buttons',
//                    array(
//                        'label' => 'Choose the action links that you want to be available for the Listings displayed in this block. (This setting will only work, if you have chosen Pinboard View from the above setting.)',
//                        'multiOptions' => array("comment" => "Comment", "like" => "Like / Unlike", 'share' => 'Share', 'facebook' => 'Facebook', 'twitter' => 'Twitter', 'pinit' => 'Pin it', 'tellAFriend' => 'Tell a Friend', 'print' => 'Print')
//                    //'value' =>array("viewCount","likeCount","commentCount","reviewCount"),
//                    ),
//                ),
//                array(
//                    'Text',
//                    'truncationDescription',
//                    array(
//                        'label' => "Enter the trucation limit for the Listing Description. (If you want to hide the description, then enter '0'.)"),
//                        'value' => 100,
//                    )
//                ),
//            ),
//        ),
//    ),
    array(
        'title' => 'Search Groups Location Form',
        'description' => 'Displays the form for searching Groups corresponding to location on the basis of various filters.',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroup.location-search',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'advancedsearchLink',
                    array(
                        'label' => 'Show Advanced Search option.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'street',
                    array(
                        'label' => 'Show street option.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'city',
                    array(
                        'label' => 'Show city option.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'state',
                    array(
                        'label' => 'Show state option.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'country',
                    array(
                        'label' => 'Show country option.',
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
        'title' => 'Ajax Based Recently Posted, Popular, Random, Featured and Sponsored Groups',
        'description' => "Displays the recently posted, popular, random, featured and sponsored groups in a block in separate ajax based tabs respectively.",
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroup.recently-popular-random-sitegroup',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
            'layouts_views' => array("0" => "1", "1" => "2", "2" => "3"),
            'layouts_oder' => 1,
            'layouts_tabs' => $layouts_tabs,
            'recent_order' => 1,
            'popular_order' => 2,
            'random_order' => 3,
            'featured_order' => 4,
            'sponosred_order' => 5,
            'list_limit' => 10,
            'grid_limit' => 15,
            'columnWidth' => '188',
            'columnHeight' => '350',
            'statistics' => $statisticsElementValue,
        ),
        'adminForm' => array(
            'elements' => array(
array(
                    'Text',
                    'titleLink',
                    array(
                        'label' => 'Enter Title Link',
                        'description' => 'If you want to show the "Explore All" link and redirect it to "Browse Group" then please use this code <a href="/'.$routeStartP.'/index">Explore All</a> Otherwise simply leave this field empty, if you do not want to show any link.',
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
//                array(
//                    'Text',
//                    'photoHeight',
//                    array(
//                        'label' => 'Enter the height of image.',
//                    )
//                ),
                array(
                    'Text',
                    'photoWidth',
                    array(
                        'label' => 'Enter the width of image.',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'layouts_views',
                    array(
                        'label' => 'Choose the view types that you want to be available for groups on the groups home and browse groups.',
                        'multiOptions' => array("1" => "List View", "2" => "Grid View", "3" => "Map View"),
                    ),
                ),
                array(
                    'Radio',
                    'layouts_oder',
                    array(
                        'label' => 'Select a default view type for Groups / Communities.',
                        'multiOptions' => array("1" => "List View", "2" => "Grid View", "3" => "Map View"),
                    )
                ),
                array(
                    'Select',
                    'detactLocation',
                    array(
                        'label' => 'Do you want to display groups based on user’s current location? (Note:- For this you must be enabled the auto-loading.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '0'
                    )
                ),
                array(
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
                ),
                array(
                    'Text',
                    'list_limit',
                    array(
                        'label' => 'List View (Limit)',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    ),
                ),
                array(
                    'Text',
                    'grid_limit',
                    array(
                        'label' => 'Grid View (Limit)',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    ),
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => 'Column Width For Grid View.',
                        'value' => '188',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For Grid View.',
                        'value' => '350',
                    )
                ),
                array(
                    'Text',
                    'listview_turncation',
                    array(
                        'label' => 'Title Truncation Limit For List View.',
                        'value' => '40',
                    )
                ),
                array(
                    'Text',
                    'turncation',
                    array(
                        'label' => 'Title Truncation Limit For Grid View.',
                        'value' => '40',
                    )
                ),
                array(
                    'Radio',
                    'showlikebutton',
                    array(
                        'label' => 'Do you want to show “Like Button” when users mouse over on Groups in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showfeaturedLable',
                    array(
                        'label' => 'Do you want “Featured Label” for the Groups to be displayed in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showsponsoredLable',
                    array(
                        'label' => 'Do you want “Sponsored Label”  for the Groups to be displayed in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showlocation',
                    array(
                        'label' => 'Do you want “Location” of the Groups to be displayed in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showgetdirection',
                    array(
                        'label' => 'Do you want “Get Direction” link for the groups having location to be displayed in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showprice',
                    array(
                        'label' => 'Do you want “Price” of the Groups to be displayed in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showpostedBy',
                    array(
                        'label' => 'Do you want “Posted By” of the Groups to be displayed in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showdate',
                    array(
                        'label' => 'Do you want “Creation Date” of the Groups to be displayed in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'MultiCheckbox',
                    'layouts_tabs',
                    array(
                        'label' => 'Choose the ajax tabs that you want to be there in the Main Groups Home Widget.',
                        'multiOptions' => $layouts_tabs_options,
                    ),
                ),
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categories_prepared,
                    )
                ),
                array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => 'Choose the statistics that you want to be displayed for the Groups in this block.',
                        'multiOptions' => $statisticsElement,
                    ),
                ),
                array(
                    'Text',
                    'recent_order',
                    array(
                        'label' => 'Recent Tab (order)',
                    ),
                ),
                array(
                    'Text',
                    'popular_order',
                    array(
                        'label' => 'Most Popular Tab (order)',
                    ),
                ),
                array(
                    'Text',
                    'random_order',
                    array(
                        'label' => 'Random Tab (order)',
                    ),
                ),
                array(
                    'Text',
                    'featured_order',
                    array(
                        'label' => 'Featured Tab (order)',
                    ),
                ),
                array(
                    'Text',
                    'sponosred_order',
                    array(
                        'label' => 'Sponosred Tab (order)',
                    ),
                ),
                $joined_order,
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
          'title' => "My Groups: Groups' links",
          'description' => $linkDescription,
          'category' => 'Groups',
          'type' => 'widget',
          'name' => 'sitegroup.links-sitegroup',
          'defaultParams' => array(
              'title' => '',
              'titleCount' => false,
              'showLinks' => $linksValues
          ),
      'adminForm' => array(
          'elements' => array(
            array(
                'MultiCheckbox',
                'showLinks',
                array(
                    'label' => 'Choose the action links that you want to be available for the Groups displayed in this block.',
                    'multiOptions' => $linksOptions,
                  //  'value' => $linksValues
                ),
            ),
          ),
        ),
    ),
    
    array(
         'title' => 'My Groups: User’s Groups',
        'description' => 'Displays a list of all the groups joined, owned, admin, etc. of a user on your site. This widget should be placed on Groups / Communities - Manage Groups page.',
          'category' => 'Groups',
          'type' => 'widget',
          'name' => 'sitegroup.manage-sitegroup',
          'defaultParams' => array(
              'title' => '',
              'titleCount' => false,
          ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'groupAdminJoined',
                    array(
                        'label' => 'Which all Groups related to the user do you want to display in this widget?',
                        'multiOptions' => array(
                              1 => 'Both Groups Administered and Joined by user',
                              2 => 'Only Groups Administered by user'
                        ),
                        'value' => '1'
                    )
                ),
                array(
                    'Radio',
                    'showOwnerInfo',
                    array(
                        'label' => 'Do you want to show the member information (You are owner / admin / member) for the group. If enabled this information will be shown to you on Groups / Communities - Manage Groups page.',
                        'multiOptions' => array(
                              1 => 'Yes',
                              0 => 'No'
                        ),
                        'value' => '0'
                    )
                ),
            ),
        ),
    ),    
    array(
          'title' => 'My Groups: Search Groups Form',
          'description' => 'Displays the form for searching Groups on the basis of various fields and filters. Settings for this form can be configured from the Search Form Settings section. This widget should be placed on Groups / Communities - Manage Groups page.',
          'category' => 'Groups',
          'type' => 'widget',
          'name' => 'sitegroup.manage-search-sitegroup',
          'defaultParams' => array(
              'title' => '',
              'titleCount' => false,
          ),
    ),    
    array(
        'title' => 'Search Groups Form (Pinboard Results)',
        'description' => 'Displays the form for searching Groups based on location and various filters. (Note: This widget supports both Pinboard and normal layouts for search results.)',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroup.horizontal-search',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'browseredirect',
                    array(
                        'label' => 'Choose the layout of browse group where you want to display the search results. (If you are placing this widget on a group having Pinboard layout, then choose Pinboard layout below.)',
                        'multiOptions' => array(
                            'pinboard' => 'Pinboard Layout',
                            'default' => 'Normal Layout'
                        ),
                        'value' => '1'
                    )
                ),
                array(
                    'Radio',
                    'advancedsearchLink',
                    array(
                        'label' => 'Show Advanced Search option.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'street',
                    array(
                        'label' => 'Show street option.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'city',
                    array(
                        'label' => 'Show city option.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'state',
                    array(
                        'label' => 'Show state option.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'country',
                    array(
                        'label' => 'Show country option.',
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
);

if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
  $ads_Array = array(
      array(
          'title' => 'Group Ads Widget',
          'description' => 'Displays community ads in various widgets and view groups of this plugin.',
          'category' => 'Groups',
          'type' => 'widget',
          'name' => 'sitegroup.group-ads',
          'defaultParams' => array(
              'title' => '',
              'titleCount' => true,
          ),
  ));
}

if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {  
  $joined_array = array(
      array(
          'title' => 'Joined / Owned Groups',
          'description' => 'Displays groups administered and joined by members on their profiles. This widget should be placed on the Member Profile group.',
          'category' => 'Groups',
          'type' => 'widget',
          'name' => 'sitegroup.profile-joined-sitegroup',
          'defaultParams' => array(
              'title' => 'Groups',
              'titleCount' => true,
          ),
          'adminForm' => array(
              'elements' => array(
                  array(
                      'Radio',
                      'groupAdminJoined',
                      array(
                          'label' => 'Which all Groups related to the user do you want to display in this tab widget on their profile?',
                          'multiOptions' => array(
                              1 => 'Both Groups Administered and Joined by user',
                              2 => 'Only Groups Joined by user'
                          ),
                          'value' => 1,
                      )
                  ),
									array(
                      'Radio',
                      'joinMoreGroups',
                      array(
                          'label' => 'Do you want to display the "Join More Groups" to the Group Ower?',
                          'multiOptions' => array(
                              1 => 'Yes',
                              0 => 'No'
                          ),
                          'value' => 1,
                      ),
                  ),
                  array(
                      'Text',
                      'textShow',
                      array(
                          'label' => 'Enter the verb to be displayed for the group admin approved members. (If you do not want to display any verb, then simply leave this field blank.)',
                          'value' => 'Verified',
                      ),
                  ),
                  array(
                      'Radio',
                      'showMemberText',
                      array(
                          'label' => 'Show Member Text?',
                          'multiOptions' => array(
                              1 => 'Yes',
                              0 => 'No'
                          ),
                          'value' => 1,
                      )
                  ),
                  array(
                      'Select',
                      'category_id',
                      array(
                          'label' => 'Category',
                          'multiOptions' => $categories_prepared,
                      )
                  ),
              )
          ),
      )
  );
}

$fbgroup_sitegroup_Array = array(
    array(
        'title' => 'Group Profile: Facebook Like Box',
        'description' => 'This widget contains the Facebook Like Box which enables Group Admins to gain Likes for their Facebook Page from this website. The edit popup contains the settings to customize the Facebook Like Box. This widget should be placed on the Group Profile.',
        'category' => 'Group Profile',
        'type' => 'widget',
        'name' => 'sitegroup.fblikebox-sitegroup',
        'defaultParams' => array(
            'title' => ''
        ),
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    "Text",
                    "title",
                    array(
                        'label' => 'Title',
                        'value' => '',
                    )
                ),
                array(
                    "Text",
                    "fb_width",
                    array(
                        'label' => 'Width',
                        'description' => 'Width of the Facebook Like Box in pixels.',
                        'value' => '220',
                    )
                ),
                array(
                    "Text",
                    "fb_height",
                    array(
                        'label' => 'Height',
                        'description' => 'Height of the Facebook Like Box in pixels (optional).',
                        'value' => '588',
                    )
                ),
                array(
                    "Select",
                    "widget_color_scheme",
                    array(
                        'label' => 'Color Scheme',
                        'description' => 'Color scheme of the Facebook Like Box in pixels.',
                        'multiOptions' => array('light' => 'light', 'dark' => 'dark')
                    )
                ),
                array(
                    "MultiCheckbox",
                    "widget_show_faces",
                    array(
                        //'label' => 'Show Profile Photos in this plugin.',
                        'description' => 'Show Faces',
                        'multiOptions' => array('1' => 'Show profile photos of users who like the linked Facebook Page in the Facebook Like Box.')
                    )
                ),
                array(
                    "Text",
                    "widget_border_color",
                    array(
                        'label' => 'Border Color',
                        'description' => 'Border color of the Facebook Like Box'
                    )
                ),
                array(
                    "MultiCheckbox",
                    "show_stream",
                    array(
                        'description' => 'Stream',
                        'multiOptions' => array('1' => 'Show the Facebook Page profile stream for the public feeds in the Facebook Like Box.'),
                    )
                ),
                array(
                    "MultiCheckbox",
                    "show_header",
                    array(
                        'description' => 'Header',
                        'multiOptions' => array('1' => "Show the 'Find us on Facebook' bar at top. Only shown when either stream or profile photos are present."),
                    )
                ),
            )
        )
        ));

// if (!empty($recentPopularWidgt)) {
//   $final_array = array_merge($final_array, $recentPopularWidgt);
// }
if (!empty($joined_array)) {
  $final_array = array_merge($final_array, $joined_array);
}
if (!empty($ads_Array)) {
  $final_array = array_merge($final_array, $ads_Array);
}
if (!empty($fbgroup_sitegroup_Array)) {
  $final_array = array_merge($final_array, $fbgroup_sitegroup_Array);
}
return $final_array;
?>
