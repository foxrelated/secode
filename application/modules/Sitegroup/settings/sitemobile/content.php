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

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
$ads_Array = $categories_prepared = array();
$categories = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategories();
if (count($categories) != 0) {
  $categories_prepared[0] = "";
  foreach ($categories as $category) {
    $categories_prepared[$category->category_id] = $category->category_name;
  }
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

$detactLocationElement =                 array(
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
                );

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

$category_groups_multioptions = array(
    'view_count' => $view->translate('Views'),
    'like_count' => $view->translate('Likes'),
    'comment_count' => $view->translate('Comments'),
);

if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
  $category_groups_multioptions['review_count'] = $view->translate('Reviews');
}


$showContentOptions = array("mainPhoto" => "Group Profile Photo", "title" => "Group Title", "sponsored" => "Sponsored Label", "featured" => "Featured Label", "category" => "Category", "subcategory" => "Sub-Category", "subsubcategory" => "3rd Level Category", "likeButton" => "Like Button", "followButton" => "Follow", "description" => "About / Description", "phone" => "Phone", "email" => "Email", "website" => "Website", "location" => "Group Location", "tags" => "Tags", "price" => "Price");
$showContentDefault = array("mainPhoto", "title", "sponsored", "featured", "category", "subcategory", "subsubcategory", "likeButton", "followButton", "description", "phone", "email", "website", "location", "tags", "price");

if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
  $showContentOptions['addButton'] = 'Add People Button';
  $showContentOptions['joinButton'] = 'Join Group Button / Cancel Membership Request Button';
	$showContentDefault['leaveButton'] = 'Leave Group Button';
  $showContentDefault[] = 'addButton';
  $showContentDefault[] = 'joinButton';
  $showContentDefault[] = 'leaveButton';
}

if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupbadge')) {
  $showContentOptions['badge'] = 'Badge';
  $showContentDefault[] = 'badge';
}

$popularity_options = array(
    'Recently Posted' => $view->translate('Recently Posted'),
    'Most Viewed' => $view->translate('Most Viewed'),
    'Featured' => $view->translate('Featured'),
    'Sponosred' => $view->translate('Sponosred'),
    'Most Joined' => $view->translate('Most Joined'),
//    'Most Commented' => $view->translate('Most Commented'),
//    'Top Rated' => $view->translate('Top Rated'),
//    'Most Likes' => $view->translate('Most Liked'),
    
);

$final_array = array(
    array(
        'title' => $view->translate('Browse Groups'),
        'description' => $view->translate('Displays a list of all the groups on site. This widget should be placed on the Browse Groups group.'),
        'category' => $view->translate('Groups'),
        'type' => 'widget',
        'name' => 'sitegroup.sitemobile-groups-sitegroup',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'layouts_views' => array("0" => "1", "1" => "2", "2" => "3"),
            'view_selected' => 'grid',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'layouts_views',
                    array(
                        'label' => $view->translate('Choose the view types that you want to be available for groups on the groups home and browse groups.'),
                        'multiOptions' => array("1" => "List View", "2" => "Grid View")
                    ),
                ),
                array(
                    'Radio',
                    'view_selected',
                    array(
                        'label' => $view->translate('Select a default view type for Groups / Communities.'),
                        'multiOptions' => array("list" => "List View", "grid" => "Grid View")
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For Grid View.',
                        'value' => '325',
                    )
                ),
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => $view->translate('Category'),
                        'multiOptions' => $categories_prepared,
                    )
                ),
                array(
                    'MultiCheckbox',
                    'content_display',
                    array(
                        'label' => $view->translate('Choose the options that you want to be displayed for the Groups in this block.'),
                        'multiOptions' => array(
                            "featured" => "Featured Label",
                            "sponsored" => "Sponsored Label",
                            "closed" => "Close Group Icon",
                            "ratings" => "Ratings",
                            "date" => "Creation Date",
                            "owner" => "Created By",
                            "likeCount" => "Likes",
                            "followCount" => "Followers",
                            "memberCount" => "Members",
                            "reviewCount" => "Reviews",
                            "commentCount" => "Comments",
                            "viewCount" => "Views",
                            "location" => "Location",
                            "price" => "Price",
                        )
                    ),
                ),
                $detactLocationElement,
                $defaultLocationDistanceElement,
            ),
        )
    ),  
    array(
        'title' => $view->translate('Popular / Recent Groups'),
        'description' => $view->translate('Displays Groups based on the Popularity Criteria and other settings that you choose for this widget. You can place this widget multiple times on a group with different popularity criterion chosen for each placement.'),
        'category' => $view->translate('Groups'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitegroup.sitemobile-popular-groups',
        'defaultParams' => array(
            'title' => $view->translate('Groups'),
            'titleCount' => true,
            'viewType' => 'gridview',
            'columnWidth' => '180'
        ),
        'adminForm' => array(
            'elements' => array(         
                 array(
                    'MultiCheckbox',
                    'layouts_views',
                    array(
                        'label' => $view->translate('Choose the view types that you want to be available for groups on the groups home and browse groups.'),
                        'multiOptions' => array("1" => "List View", "2" => "Grid View"),
                    ),
                ),
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => $view->translate('Choose the View Type for Groups.'),
                        'multiOptions' => array(
                            'listview' => $view->translate('List View'),
                            'gridview' => $view->translate('Grid View'),
                        ),
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For Grid View.',
                        'value' => '325',
                    )
                ),
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => $view->translate('Category'),
                        'multiOptions' => $categories_prepared,
                    )
                ),
                array(
                    'MultiCheckbox',
                    'content_display',
                    array(
                        'label' => $view->translate('Choose the options that you want to be displayed for the Groups in this block.'),
                        'multiOptions' => array(
                            "featured" => "Featured Label",
                            "sponsored" => "Sponsored Label",
                            "ratings" => "Ratings",
                            "date" => "Creation Date",
                            "owner" => "Created By",
                            "likeCount" => "Likes",
                            "followCount" => "Followers",
                            "memberCount" => "Members",
                            "reviewCount" => "Reviews",
                            "commentCount" => "Comments",
                            "viewCount" => "Views",
                            "location" => "Location",
                            "price" => "Price",
                        )
                    ),
                ),        
                array(
                    'Select',
                    'popularity',
                    array(
                        'label' => $view->translate('Popularity Criteria'),
                        'multiOptions' => $popularity_options,
                        'value' => 'Recently Posted',
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of Groups to show)'),
                        'value' => 5,
                    )
                ),
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => $view->translate('Title Truncation Limit'),
                        'value' => 16,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                $detactLocationElement,
                $defaultLocationDistanceElement,
            ),
        ),
    ),   
    array(
        'title' => 'Categories, Sub-categories and 3<sup>rd</sup> Level-categories',
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
        'title' => $view->translate('Group Profile Info'),
        'description' => $view->translate('This widget forms the Info tab on the Group Profile and displays the information of the Group. It should be placed in the Tabbed Blocks area of the Group Profile.'),
        'category' => $view->translate('Group Profile'),
        'type' => 'widget',
        'name' => 'sitegroup.sitemobile-info-sitegroup',
        'defaultParams' => array(
            'title' => $view->translate('Info'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Group Profile Overview',
        'description' => 'Displays rich overview on Group\'s profile, created by its admin using the editor from Group Dashboard. This should be placed in the Tabbed Blocks area of Group Profile.',
        'category' => 'Group Profile',
        'type' => 'widget',
        'name' => 'sitegroup.sitemobile-overview-sitegroup',
        'defaultParams' => array(
            'title' => 'Overview',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Group Profile Map'),
        'description' => $view->translate('This widget forms the Map tab on the Group Profile. It displays the map showing the Group position as well as the location details of the group. It should be placed in the Tabbed Blocks area of the Group Profile. This feature will be available to Groups based on their Package and Member Level of their owners.'),
        'category' => $view->translate('Group Profile'),
        'type' => 'widget',
        'name' => 'sitegroup.sitemobile-location-sitegroup',
        'defaultParams' => array(
            'title' => $view->translate('Map'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Alphabetic Filtering of Groups'),
        'description' => $view->translate("This widget enables users to alphabetically filter the directory items / groups on your site by clicking on the desired alphabet. The widget shows all the alphabets for filtering."),
        'category' => $view->translate('Groups'),
        'type' => 'widget',
        'name' => 'sitegroup.alphabeticsearch-sitegroup',
        'defaultParams' => array(
            'title' => $view->translate(""),
            'titleCount' => "",
        ),
    ),
    array(
        'title' => 'Group Profile Cover Photo and Information',
        'description' => 'Displays the cover photo of a Group. From the Edit Settings section of this widget, you can also choose to display group member’s profile photos, if Group Admin has not selected a cover photo. It is recommended to place this widget on the Group Profile at the top.',
        'category' => 'Group Profile',
        'type' => 'widget',
        'name' => 'sitegroup.sitemobile-groupcover-photo-information',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'showContent' => $showContentDefault
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'showContent',
                    array(
                        'label' => 'Select the information options that you want to be available in this block.',
                        'multiOptions' => $showContentOptions,
                    ),
                ),
                array(
                    'Radio',
                    'strachPhoto',
                    array(
                        'label' => 'Do you want group profile pictures to be displayed in consistent blocks of fixed dimension below the cover photo on your site?',
                        'multiOptions' => array("1" => "Yes (Though the dimensions of the group profile picture block will be consistent, and the photos with unequal dimension will be shown in the center of the block.)", "0" => "No (The dimension of the group profile picture block will not be fixed. In this case block’s dimensions will depend on the dimensions of group profile picture.)"),
                        'value' => 0
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => $view->translate('Group Profile Likes'),
        'description' => $view->translate('Displays list of user who have liked the group. This widget should be placed on Group Profile.'),
        'category' => 'Group Profile',
        'type' => 'widget',
        'name' => 'seaocore.sitemobile-people-like',
        'defaultParams' => array(
            'title' => "Member Likes",
            'titleCount' => "true",
        ),
    ),
    array(
        'title' => 'Content Profile: Content Followers',
        'description' => 'Displays a list of all the users who are Following the content on which this widget is placed. This widget should be placed on any content’s profile / view group.',
        'category' => 'Group Profile',
        'type' => 'widget',
        'name' => 'seaocore.sitemobile-followers',
        'defaultParams' => array(
            'title' => "Followers",
            'titleCount' => "true",
        ),
    ),
    array(
        'title' => 'Group Profile Featured Group Admins',
        'description' => "Displays the Featured Admins of a group. This widget should be placed on the Group Profile.",
        'category' => 'Group Profile',
        'type' => 'widget',
        'name' => 'sitegroup.featuredowner-sitegroup',
        'defaultParams' => array(
            'title' => "Group Admins",
            'titleCount' => "true",
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
                        'description' => '(number of groups to show)',
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
        'title' => $view->translate('Profile Groups'),
        'description' => $view->translate('Displays members\' groups on their profile.'),
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
                        'label' => $view->translate('Which all Groups related to the user do you want to display in this tab widget on their profile?'),
                        'multiOptions' => array(
                            1 => $view->translate('Groups Owned by the user. (Group Owner)'),
                            2 => $view->translate('Groups Administered by the user. (Group Admin)')
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
    ),
    array(
        'title' => $view->translate('Close Group Message'),
        'description' => $view->translate('If a Group is closed, then show this message.'),
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroup.closegroup-sitegroup',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Group Profile Breadcrumb'),
        'description' => $view->translate('Displays breadcrumb of the group based on the categories. This widget should be placed on the Group Profile group.'),
        'category' => $view->translate('Group Profile'),
        'type' => 'widget',
        'name' => 'sitegroup.group-profile-breadcrumb',
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
);

if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
  $joined_array = array(
      array(
          'title' => $view->translate('Joined / Owned Groups'),
          'description' => $view->translate('Displays groups administered and joined by members on their profiles. This widget should be placed on the Member Profile group.'),
          'category' => 'Groups',
          'type' => 'widget',
          'name' => 'sitegroup.profile-joined-sitegroup',
          'defaultParams' => array(
              'title' => $view->translate('Joined / Owned Groups'),
              'titleCount' => true,
          ),
          'adminForm' => array(
              'elements' => array(
                  array(
                      'Radio',
                      'groupAdminJoined',
                      array(
                          'label' => $view->translate('Which all Groups related to the user do you want to display in this tab widget on their profile?'),
                          'multiOptions' => array(
                              1 => $view->translate('Both Groups Administered and Joined by user'),
                              2 => $view->translate('Only Groups Joined by user')
                          ),
                          'value' => 2,
                      )
                  ),
                  array(
                      'Text',
                      'textShow',
                      array(
                          'label' => $view->translate('Enter the verb to be displayed for the group admin approved members. (If you do not want to display any verb, then simply leave this field blank.)'),
                          'value' => 'Verified',
                      ),
                  ),
                  array(
                      'Radio',
                      'showMemberText',
                      array(
                          'label' => $view->translate('Show Member Text?'),
                          'multiOptions' => array(
                              1 => $view->translate('Yes'),
                              0 => $view->translate('No')
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
if (!empty($joined_array)) {
  $final_array = array_merge($final_array, $joined_array);
}
return $final_array;
?>