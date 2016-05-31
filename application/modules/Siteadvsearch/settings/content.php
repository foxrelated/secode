<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$popularity_options = array(
    'contenttype' => 'Content Type',
    'postedby' => 'Posted By',
    'viewcount' => 'Views',
    'likecount' => 'Likes',
    'commentcount' => 'Comments',
    'photocount' => 'Photos',
    'rating' => 'Rating',
    'reviewcount' => 'Reviews',
    'followercount' => 'Followers',
    'description' => 'Description',
    'category' => 'Category',
    'location' => 'Location',
    'sponsored' => 'Sponsered',
    'featured' => 'Featured',
);

$locationBasedSearching = array();
$locationbasedContent = array();
 if(Engine_Api::_()->hasModuleBootstrap('sitecitycontent')) {
    $locationbasedContent = array(
                    'Radio',
                    'showLocationBasedContent',
                    array(
                        'label' => 'Show results based on the location, saved in user’s browser cookie.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0
                    )
                );

    $locationBasedSearching = array(
                    'Radio',
                    'showLocationSearch',
                    array(
                        'label' => 'Do you want to enable location based searching?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0
                    )
                );
}

return array(
    array(
        'title' => 'Advanced Search - Mini Menu',
        'description' => 'Shows the site-wide mini menu. You can edit its contents in your menu editor.',
        'category' => 'Advanced Search',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteadvsearch.menu-mini',
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'advsearch_search_width',
                    array(
                        'label' => 'Enter width for searchbox.',
                        'value' => 275,
                    )
                ),
            ),
        ),
        'requirements' => array(
            'header-footer',
        ),
    ),
    array(
        'title' => 'Advanced Search Box',
        'description' => 'Displays Advanced Search Box. This widget can be placed anywhere on the site.',
        'category' => 'Advanced Search',
        'type' => 'widget',
        'name' => 'siteadvsearch.search-box',
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'advsearch_search_box_width',
                    array(
                        'label' => 'Enter width for searchbox.',
                        'value' => 275,
                    )
                ),
                array(
                    'Text',
                    'advsearch_search_box_width_for_nonloggedin',
                    array(
                        'label' => 'Enter width for searchbox non logged-in user.',
                        'value' => 275,
                    )
                ),
                $locationBasedSearching,
                $locationbasedContent,
            ),
        ),
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Advanced Search Contents',
        'description' => 'Shows all the search type contents. This widget must be placed at "Advanced Search - All Results Page" page.',
        'category' => 'Advanced Search',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteadvsearch.search-contents',
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'advsearch_showmore',
                    array(
                        'label' => 'How many tabs do you want to show on Search Page navigation bar by default? (Note: If number of tabs exceeds the limit entered by you then a "More" tab will appear, clicking on which will show the remaining hidden tabs. To choose the tab to be shown in this navigation menu, and their sequence, please visit: "Manage Modules" section.)',
                        'value' => 8,
                    )
                ),
                array(
                    'Radio',
                    'show_resourcetype_option',
                    array(
                        'label' => 'Do you want to show content / resource type filter on search page?',
                        'description' => '',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0
                    )
                ),
                array(
                    'MultiCheckbox',
                    'show_statstics',
                    array(
                        'label' => 'Select the details you want to display',
                        'multiOptions' => $popularity_options,
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
                            '3' => 'Auto Load Contents on Scrolling Down'),
                        'value' => 2,
                    )
                ),
                $locationBasedSearching,
                $locationbasedContent,
            )
        )
    ),
    array(
        'title' => 'Advanced Search - Musics form',
        'description' => 'Displays the form for searching Musics on the basis of various fields and filters. This widget must be placed on “Advanced Search - Musics” page.',
        'category' => 'Advanced Search',
        'type' => 'widget',
        'name' => 'siteadvsearch.music-browse-search',
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Advanced Search - Album Search form',
        'description' => 'Displays the form for searching Albums on the basis of various fields and filters. This widget must be placed on “Advanced Search - Albums” page.',
        'category' => 'Advanced Search',
        'type' => 'widget',
        'name' => 'siteadvsearch.album-browse-search',
    ),
);