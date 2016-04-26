<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    List
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Content.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

$category_listings_multioptions = array(
    'view_count' => $view->translate('Views'),
    'like_count' => $view->translate('Likes'),
    'comment_count' => $view->translate('Comments'),
    'review_count' => $view->translate('Reviews'),
);

$statisticsElement = array(
    'MultiCheckbox',
    'statistics',
    array(
        'label' => $view->translate('Choose the statistics that you want to be displayed for the Listings in this block.'),
        'multiOptions' => array("viewCount" => "Views", "likeCount" => "Likes", "commentCount" => "Comments", 'reviewCount' => 'Reviews'),
    //'value' =>array("viewCount","likeCount","commentCount","reviewCount"),
    ),
);

return array(
    array(
        'title' => $view->translate('Listing Overview'),
        'description' => $view->translate('This widget forms the Overview tab on the Listing Profile page and displays the overview of the listing, which the owner has created using the editor in listing dashboard. It should be placed in the Tabbed Blocks area of the Listing Profile page.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.overview-list',
        'defaultParams' => array(
            'title' => 'Overview',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Listing Archives'),
        'description' => $view->translate('Displays the month-wise archives for the listings posted on your site.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.archives-list',
        'defaultParams' => array(
            'title' => 'Archives',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Navigation Tabs'),
        'description' => $view->translate('Displays the Navigation tabs listings having links of Listings Home, Browse Listings, etc. This widget should be placed at the top of Listings Home and Browse Listings pages.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.browsenevigation-list',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Categories Hierarchy for Listings (sidebar)'),
        'description' => $view->translate('Displays the Categories, Sub-categories and 3rd Level-categories of Listings in an expandable form. Clicking on them will redirect the viewer to the list of Listings created in that category. This widget should be placed on the Browse Listings Page.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.categories-list',
        'defaultParams' => array(
            'title' => 'Categories',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Categories Hierarchy for Listings'),
        'description' => $view->translate('Displays the Categories, Sub-categories and 3<sup>rd</sup> Level-categories of listings in an expandable form. Clicking on them will redirect the viewer to the list of listings created in that category.This widget should be placed on the Listings Home Pag'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.categories',
        'defaultParams' => array(
            'title' => $view->translate('Categories'),
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'showAllCategories',
                    array(
                        'label' => $view->translate('Do you want all the categories, sub-categories and 3rd level categories to be shown to the users even if they have 0 listings in them? (Note: Selecting "Yes" will display all the categories WITHOUT  the count of the listings created in them if "Browse by Networks" are enabled  from the Global Settings of Listings / Catalog Showcase Plugin and display all the categories with count if selected "No" for Browse by Networks. While selecting "No" here will display categories with count of the listings.)'),
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
                        'label' => $view->translate('Do you want to show sub-categories in this widget?'),
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
                        'label' => $view->translate('Do you want to show 3rd level category to the viewer? (This setting will depend on above setting.)'),
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
        'title' => $view->translate('Profile Listings'),
        'description' => $view->translate('Displays a member\'s listings on their profile.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.profile-list',
        'defaultParams' => array(
            'title' => 'Listings',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Featured Listings Slideshow'),
        'description' => $view->translate('Displays the Featured Listings in the form of an attractive Slideshow with interactive controls. The setting of this widget can be done in the widget settings section of this plugin.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.slideshow-list',
        'defaultParams' => array(
            'title' => 'Featured Listings',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Listing Social Share Buttons'),
        'description' => $view->translate("Contains Social Sharing buttons and enables users to easily share Listings on their favorite Social Networks. This widget should be placed on the Listing View Page. You can customize the code for social sharing buttons from Global Settings of this plugin by adding your own code generated from: <a href='http://www.addthis.com' target='_blank'>http://www.addthis.com</a>"),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.socialshare-list',
        'defaultParams' => array(
            'title' => $view->translate('Social Share'),
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'list_listing',
        ),
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
        'title' => $view->translate('Listing Reviews'),
        'description' => $view->translate('This widget forms the Reviews tab on the Listing Profile page and displays the reviews given to a listing by the users. Users can also write a review through this widget. It should be placed in the Tabbed Blocks area of the Listing Profile page.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.review-list',
        'defaultParams' => array(
            'title' => 'Reviews',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Listing Title'),
        'description' => $view->translate('Displays the Title of the listing. This widget should be placed on the Listing Profile Page, in the middle column at the top.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.title-list',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Listing Info'),
        'description' => $view->translate('This widget forms the Info tab on the Listing Profile page and displays the information of the listing. It should be placed in the Tabbed Blocks area of the Listing Profile page.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.info-list',
        'defaultParams' => array(
            'title' => 'Info',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Information Listing'),
        'description' => $view->translate('Displays the owner, category, tags, views and other information about a listing. This widget should be placed on the Listing Profile page in the left column.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.information-list',
        'defaultParams' => array(
            'title' => 'Information',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'owner_photo',
                    array(
                        'label' => 'Do you want to show listing owner in this widget?',
                        'multiOptions' => array(
                            '1' => 'yes',
                            '0' => 'no',
                        ),
                        'value' => 1,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => $view->translate('Listing Cover Photo '),
        'description' => $view->translate('Displays the main cover photo of a listing. This widget must be placed on the Listing Profile page at the top of left column.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.mainphoto-list',
        'defaultParams' => array(
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Listing Map'),
        'description' => $view->translate('This widget forms the Map tab on the Listing Profile page. It displays the map showing the listing position as well as the location details of the listing. It should be placed in the Tabbed Blocks area of the Listing Profile page.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.location-list',
        'defaultParams' => array(
            'title' => 'Map',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Listing Options'),
        'description' => $view->translate('Displays the various action link options to users viewing a listing. This widget should be placed on the Listing Profile page in the left column, below the listing profile photo.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.options-list',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Owner Listing Tags'),
        'description' => $view->translate('Displays all the tags chosen by the listing owner for his listings. This widget should be placed on the Listing Profile page.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.tags-list',
        'defaultParams' => array(
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Listing Photos'),
        'description' => $view->translate('This widget forms the Photos tab on the Listing Profile page and displays the photos of the listing. It should be placed in the Tabbed Blocks area of the Listing Profile page.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.photos-list',
        'defaultParams' => array(
            'title' => 'Photos',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Listing Videos'),
        'description' => $view->translate('This widget forms the Videos tab on the Listing Profile page and displays the videos of the listing. It should be placed in the Tabbed Blocks area of the Listing Profile page.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.video-list',
        'defaultParams' => array(
            'title' => 'Videos',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Listing Rating'),
        'description' => $view->translate('Displays the overall rating given to a listing by other users. Users can also rate the listing using this widget.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.ratings-list',
        'defaultParams' => array(
            'title' => 'Ratings',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Post a New Listing'),
        'description' => $view->translate('Displays the link to Post a New Listing.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.newlisting-list',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Most Commented Listings'),
        'description' => $view->translate('Displays the listings having the most number of comments. The setting of this widget can be done in the widget settings section of this plugin.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.mostcommented-list',
        'defaultParams' => array(
            'title' => 'Most Commented Listings',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Most Liked Listings'),
        'description' => $view->translate('Displays the listings that have been Liked by most number of users. The setting of this widget can be done in the widget settings section of this plugin.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.mostlikes-list',
        'defaultParams' => array(
            'title' => 'Most Liked Listings',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Popular Locations'),
        'description' => $view->translate('Displays the popular locations of listings. The setting of this widget can be done in the widget settings section of this plugin.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.popularlocation-list',
        'defaultParams' => array(
            'title' => 'Popular Locations',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of locations to show)'),
                        'value' => 5,
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
        'title' => $view->translate('Popular Listing Tags'),
        'description' => $view->translate('Shows popular tags with frequency.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.tagcloud-list',
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
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of tags to show)'),
                        'value' => 20,
                    )
                ),
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
        'title' => $view->translate('Owner Listings'),
        'description' => $view->translate('Displays the other listings of a listing owner. This widget should be placed on the Listing Profile Page.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.userlisting-list',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('About Listing'),
        'description' => $view->translate('Displays the About Us information for listings. This widget should be placed on the Listing Profile page.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.write-page',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Recent Listings'),
        'description' => $view->translate('Displays a listing of recently posted listings.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.recentlyposted-list',
        'defaultParams' => array(
            'title' => 'Recent',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Popular Listings'),
        'description' => $view->translate('Displays the popular listings on the site. The setting of this widget can be done in the widget settings section of this plugin.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.mostviewed-list',
        'defaultParams' => array(
            'title' => 'Most Popular',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Browse Listings'),
        'description' => $view->translate('Displays a listing of all the listings on site. This widget should be placed on the Browse Listings page.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.listings-list',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'layouts_views' => array("0" => "1", "1" => "2", "2" => "3"),
            'layouts_oder' => 1,
            'statistics' => array("viewCount", "likeCount", "commentCount", "reviewCount")
        ),
        'adminForm' => array(
            'elements' => array(
                $statisticsElement,
                array(
                    'MultiCheckbox',
                    'layouts_views',
                    array(
                        'label' => 'Choose the view types that you want to be available for listings on the listing home and browse listing.',
                        'multiOptions' => array("1" => "List View", "2" => "Grid View", "3" => "Map View")
                    ),
                ),
                array(
                    'Radio',
                    'layouts_oder',
                    array(
                        'label' => 'Select a default view type for listing.',
                        'multiOptions' => array("1" => "List View", "2" => "Grid View", "3" => "Map View")
                    )
                ),
                array(
                    'Select',
                    'show_content',
                    array(
                        'label' => 'What do you want for view more content?',
                        'description' => '',
                        'multiOptions' => array(
                            '1' => 'Pagination',
                            '2' => 'Show View More Link at Bottom',
                            '3' => 'Auto Load Listings on Scrolling Down'),
                        'value' => 2,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => $view->translate('Top Rated Listings'),
        'description' => $view->translate('Displays the top rated listings on the site.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.mostrated-list',
        'defaultParams' => array(
            'title' => 'Top Rated Listings',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Search Listings form'),
        'description' => $view->translate('Displays the form for searching listings.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.search-list',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Random Listings'),
        'description' => $view->translate('Displays a listing of listings randomly.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.random-list',
        'defaultParams' => array(
            'title' => 'Random',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Sponsored Listings Carousel'),
        'description' => $view->translate('This widget contains an attractive AJAX based carousel, showcasing the sponsored listings on the site. The setting of this widget can be done in the widget settings section of this plugin.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.sponsored-list',
        'defaultParams' => array(
            'title' => 'Sponsored Listings',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Listing of the Day'),
        'description' => $view->translate('Displays the Listing of the Day as selected by the Admin from the widget settings section of this plugin.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.item-list',
        'defaultParams' => array(
            'title' => 'Listing of the Day',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Recently Viewed Listings'),
        'description' => $view->translate('Displays the recently viewed listings on the site. The setting of this widget can be done in the widget settings section of this plugin.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.recentview-list',
        'defaultParams' => array(
            'title' => 'Recently Viewed',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Recently Viewed By Friends'),
        'description' => $view->translate('Displays the listings that have been recently viewed by friends on the site. The setting of this widget can be done in the widget settings section of this plugin.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.recentfriend-list',
        'defaultParams' => array(
            'title' => 'Recently Viewed By Friends',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Message for Zero Listings'),
        'description' => $view->translate('This widget should be placed in the top of the middle column of Listings Home page.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.zerolisiting-list',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Close Listing Message'),
        'description' => $view->translate('If any close lisings,then show msg.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.closelisting-list',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Like Button for Listings'),
        'description' => $view->translate('This is the Like Button to be placed on the Listing Profile Page. The best place to put this widget is right above the Tabbed Blocks on the Listing Profile page. If you have the Likes Plugin and Widgets from SocialEngineAddOns installed on your site, then you may replace this button widget with the Like Button for Listings widget of that plugin.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'seaocore.like-button',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    array(
        'title' => $view->translate('You May Also Like'),
        'description' => $view->translate('Displays the other listings that a user may like, based on the listing being currently viewed. This widget should be placed on the Listing Profile Page.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.suggestedlist-list',
        'defaultParams' => array(
            'title' => $view->translate('You May Also Like'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Listing Discussions'),
        'description' => $view->translate('This widget forms the Discussions tab on the Listing Profile page and displays the discussions of the listing. It should be placed in the Tabbed Blocks area of the Listing Profile page.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.discussion-list',
        'defaultParams' => array(
            'title' => 'Discussions',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Listing Likes'),
        'description' => $view->translate('Displays that which all users have liked a listing. This widget should be placed on the Listing Profile Page.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'seaocore.people-like',
    ),
    array(
        'title' => $view->translate('Most Discussed Listings'),
        'description' => $view->translate('Displays the listings having the most number of discussions. The setting of this widget can be done in the widget settings section of this plugin.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.mostdiscussion-list',
        'defaultParams' => array(
            'title' => 'Most Discussed Listing',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Ajax based main Listings Home widget'),
        'description' => $view->translate("Contains multiple Ajax based tabs showing Recently Posted, Popular, Random, Featured and Sponsored Listings. Settings of this widget are available in the Widget Settings section."),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.recently-popular-random-list',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
        ),
    ),
    array(
        'title' => $view->translate('Categorically Popular Listings'),
        'description' => $view->translate('This attractive widget categorically displays the most popular listings on your site. It displays 5 Listings for each category. From the edit popup of this widget, you can choose the number of categories to show, criteria for popularity and the duration for consideration of popularity.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.category-listings-list',
        'defaultParams' => array(
            'title' => $view->translate('Popular Listings'),
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Category Count'),
                        'description' => $view->translate('No. of Categories to show. Enter 0 for showing all categories.'),
                        'value' => 0,
                    )
                ),
                array(
                    'Text',
                    'listingCount',
                    array(
                        'label' => $view->translate('Listings Count per Category'),
                        'description' => $view->translate('No. of Listings to be shown in each Category.'),
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
                        'label' => $view->translate('Popularity Criteria'),
                        'multiOptions' => $category_listings_multioptions,
                        'value' => 'view_count',
                    )
                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => $view->translate('Popularity Duration (This duration will be applicable to all Popularity Criteria except Views.)'),
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => $view->translate('Listings: Auto-suggest Search'),
        'description' => $view->translate("Displays auto-suggest search box for Listings. As user types, Listings will be displayed in an auto-suggest box."),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.searchbox-list',
        'defaultParams' => array(
            'title' => $view->translate("Search"),
            'titleCount' => "",
        ),
    ),
    array(
        'title' => $view->translate('Browse Listings’ Locations'),
        'description' => $view->translate('Displays a list of all the lisings having location entered corresponding to them on the site. This widget should be placed on Browse Listings’ Locations page.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.browselocation-list',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Search Listings Location Form',
        'description' => 'Displays the form for searching Listings corresponding to location on the basis of various filters.',
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.location-search',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'street',
                    array(
                        'label' => $view->translate('Show street option.'),
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
                        'label' => $view->translate('Show city option.'),
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
                        'label' => $view->translate('Show state option.'),
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
                        'label' => $view->translate('Show country option.'),
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
        'title' => $view->translate('Related Listings'),
        'description' => $view->translate('Displays a list of Listings related to the Listing currently being viewed. This widget should be placed on Listing View Page. The related Listings are shown based on the tags and top-level category of the Listing being viewed.'),
        'category' => $view->translate('Listings'),
        'type' => 'widget',
        'name' => 'list.related-listings-view-list',
        'defaultParams' => array(
            'title' => $view->translate('Related Listings'),
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'related',
                    array(
                        'label' => $view->translate('Choose which all Listings should be displayed here as Listings related to the current Listing.'),
                        'multiOptions' => array(
                            'tags' => "Listings having same tag.",
                            'categories' => 'Listings associated with same `Categories`.'
                        ),
                        'value' => 'categories',
                    )
                ),
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => $view->translate('Title Truncation Limit'),
                        'value' => 26,
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
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of Listings to show)'),
                        'value' => 3,
                    )
                ),
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
);
