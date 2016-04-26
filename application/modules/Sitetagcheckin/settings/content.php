<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
$final_array =  array(
    array(
        'title' => $view->translate('User: Map & Location Feeds'),
        'description' => $view->translate('Displays on user profile a Map with location markers for the various check-ins, geo-tagging and other location related actions done by users and their friends. It also shows the related activity feeds. Map markers are placed in an aggregated manner. Clicking on any map-marker shows its respective feed and content in attractive tooltip. Users can switch between viewing map and feeds. This widget should be placed on the Member Profile page in the Tab Container.'),
        'category' => $view->translate('Geo-Location, Geo-Tagging, Check-Ins & Proximity Search'),
        'type' => 'widget',
        'name' => 'sitetagcheckin.map-sitetagcheckin',
        'defaultParams' => array(
            'title' => $view->translate('Map'),
            'titleCount' => false,
        ),
        'adminForm' => array(
            'elements' => array(
               array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of photos to show in the photo strip which is displayed on clicking the "Add Photos To Map" link.)'),
                        'value' => 7,
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
        'title' => $view->translate('Checked-in Users'),
        'description' => $view->translate('Displays the checked-in users for a content. The value for maximum time for checked-in status in a content can be configured from the Check-Ins tab. Multiple settings are available for this widget. This widget should be placed on the content profile / view pages.'),
        'category' => $view->translate('Geo-Location, Geo-Tagging, Check-Ins & Proximity Search'),
        'type' => 'widget',
        'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
        'defaultParams' => array(
            'title' => $view->translate(''),
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'checkedin_heading',
                     array(
                        // 'label' => $view->translate('Check-in Button / Link'),
                        'description' => $view->translate('Enter the text for showing the number of people who have checked-in into this content. (Ex: People Here)'),
                        'value' => 'People Here',
                    )
                    
                ),
                array(
                    'Text',
                    'checkedin_see_all_heading',
                    array(
                        // 'label' => $view->translate('Check-in Button / Link'),
                        'description' => $view->translate('A lightbox will come on clicking the \'See All\' link which will enable users to see people who have been in this content. Enter the text of the heading of this lightbox.'),
                        'value' => 'People who have been here',
                    )
                ),
                array(
                    'Radio',
                    'checkedin_users',
                    array(
                        'label' => 'Who all users should be displayed in this widget?',
                        'multiOptions' => array(
                            '1' => 'Currently checked-in users',
                            '0' => 'Current and past checked-in users',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'checkedin_user_photo',
                    array(
                        'label' => 'Do you want to show profile photos of the checked-in users?',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'checkedin_user_name',
                    array(
                        'label' => "Do you want to show the display name of the checked-in users?",
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'checkedin_user_checkedtime',
                    array(
                        'label' => 'Do you want to show the time when users have checked-in into this content?',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Text',
                    'checkedin_item_count',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of users to show)'),
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
        'title' => $view->translate('Content Check-in button & stats'),
        'description' => $view->translate('Displays Check-in button on various content profile / view pages to allow users to check-in into them. Also shows check-in statistics for the content. Highly configurable widget with multiple settings.'),
        'category' => $view->translate('Geo-Location, Geo-Tagging, Check-Ins & Proximity Search'),
        'type' => 'widget',
        'name' => 'sitetagcheckin.checkinbutton-sitetagcheckin',
        'defaultParams' => array(
            'title' => $view->translate(''),
            'titleCount' => true,
        ),
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'checkin_use',
                    array(
                        //       'label' => $view->translate('Purpose'),
                        'description' => $view->translate('Do you want users to be able to select a date while checking into this content? (Enabling this could be useful for content types that users could have visited in the past. If you disable this, then the current date is taken for the check-in action.)'),
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'checkin_button_sidebar',
                    array(
                        //'label' => $view->translate('Set Widget'),
                        'description' => $view->translate('How do you want to display the different phrases with statistics in this widget? (It is recommended to select the option \'In different lines\', if this widget is placed in the right/left columns of the page)'),
                        'multiOptions' => array(
                            '1' => 'In different lines',
                            '0' => 'In single line',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'checkin_button',
                    array(
                        //'label' => $view->translate('Check-in'),
                        'description' => $view->translate('How do you want the "Check in here" button/link widget to be displayed? (You can change the button/link text below.)'),
                        'multiOptions' => array(
                            '1' => 'As button',
                            '0' => 'As link',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Text',
                    'checkin_button_link',
                    array(
                        // 'label' => $view->translate('Check-in Button / Link'),
                        'description' => $view->translate('Enter the text of this button/link.'),
                        'value' => 'Check-in here',
                    )
                ),
                array(
                    'Radio',
                    'checkin_icon',
                    array(
                        //'label' => $view->translate('Check-in'),
                        'description' => $view->translate('Choose the icon that you want to show in this check-in button/link.'),
                        'multiOptions' => array(
                            '0' => 'Tick-mark check-in icon',
                            '1' => 'Pin check-in icon',
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Text',
                    'checkin_verb',
                    array(
                        // 'label' => $view->translate('Check-in Verb'),
                        'description' => $view->translate('A lightbox will come on clicking the button/link which will enable users to post update. Enter the text of the submit button in this.'),
                        'value' => 'Check-in',
                    )
                ),
                array(
                    'Text',
                    'checkedinto_verb',
                    array(
                        //     'label' => $view->translate('Checked-into Verb'),
                        'description' => $view->translate('Enter the action verb to be displayed in the feeds for check-ins using this button.'),
                        'value' => 'checked-into',
                    )
                ),
                array(
                    'Text',
                    'checkin_your',
                    array(
                        //    'label' => $view->translate('Your Check-in'),
                        'description' => $view->translate('Enter the text for showing the number of check-ins made by the viewer of this content. (Ex: You\'ve checked in here)'),
                        'value' => 'You\'ve checked-in here',
                    )
                ),
                array(
                    'Text',
                    'checkin_total',
                    array(
                        //  'label' => $view->translate('Total Check-in'),
                        'description' => $view->translate('Enter the text for showing total number of check-ins made on this content. (Ex: Total check-ins here)'),
                        'value' => 'Total check-ins here',
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => $view->translate('Profile Checkin / Location Feeds'),
        'description' => $view->translate('Displays all the location related feeds for a user like for check-ins done from the status update box, check-ins done via the check-in button, adding location to photos, etc. This widget should be placed on Member Profile page in the Tab Container.'),
        'category' => $view->translate('Geo-Location, Geo-Tagging, Check-Ins & Proximity Search'),
        'type' => 'widget',
        'name' => 'sitetagcheckin.profile-checkins-sitetagcheckin',
        'defaultParams' => array(
            'title' => $view->translate('Check-Ins'),
            'titleCount' => false,
        ),
    ),
        array(
			'title' => $view->translate('Browse Members’ Locations'),
			'description' => $view->translate('Displays a list of all the Members having location associated with their profiles. This widget should be placed on Browse Members’ Locations page.'),
			'category' => $view->translate('Geo-Location, Geo-Tagging, Check-Ins & Proximity Search'),
			'type' => 'widget',
			'name' => 'sitetagcheckin.bylocation-user',
			'defaultParams' => array(
					'title' => '',
					'titleCount' => true,
			),
    ),
    array(
			'title' => 'Search Members Location Form',
			'description' => 'Displays the form for searching Members corresponding to location on the basis of various filters.',
			'category' => $view->translate('Geo-Location, Geo-Tagging, Check-Ins & Proximity Search'),
			'type' => 'widget',
			'name' => 'sitetagcheckin.userlocation-search',
			'defaultParams' => array(
				'title' => '',
				'titleCount' => true,
				'form_options' => array("advancedsearchLink" => "advancedsearchLink", "street" => "street", "city" => "city", "state" => "state", "country" => "country", "hasphoto" => "hasphoto", "isonline" => "isonline"),
			),
		  'adminForm' => array(
				'elements' => array(
					array(
						'MultiCheckbox',
						'form_options',
						array(
								'label' => $view->translate('Choose the options that you want to be available for search members location form.'),
								'multiOptions' => array("advancedsearchLink" => "Advanced Search Link", "street" => "Street", "city" => "City", "state" => "State", "country" => "Country", "hasphoto" => "Only Members With Photos", "isonline" => "Only Online Members"),
						),
          ),
					
				),
			),
    ),
    array(
        'title' => $view->translate('Content: Map & Check-in Feeds'),
        'description' => $view->translate('Displays the location associated with the content on map and the updates made by the users while checking into the content. This widget should be placed on content view / profile pages. You can configure various settings for this widget.'),
        'category' => $view->translate('Geo-Location, Geo-Tagging, Check-Ins & Proximity Search'),
        'type' => 'widget',
        'name' => 'sitetagcheckin.profile-map-sitetagcheckin',
        'defaultParams' => array(
            'title' => $view->translate('Check-Ins'),
            'titleCount' => false,
        ),
				'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'checkin_show_options',
                    array(
                        //'label' => $view->translate('Check-in'),
                        'description' => $view->translate('What do you want to display in this widget?'),
                        'multiOptions' => array(
                            '2' => 'Both Map and Feeds',
                            '1' => 'Only Map',
                            '0' => 'Only Feeds',
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Text',
                    'checkin_map_height',
                    array(
                        'label' => $view->translate('Enter the height of the map in pixels.'),
                        //'description' => $view->translate('(number of users to show)'),
                        'value' => 500,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
            ),
        ),
    ),

      );
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album')) {
  $album_array = array(
  array(
        'title' => $view->translate('Albums: Add Location Suggestion'),
        'description' => $view->translate("This widget can help you in quickly getting location information from users for their albums and photos. It suggests to users their albums to which they can easily add location from within the widget. In some cases, users are also suggested the location that they may add to the album."),
        'category' => $view->translate('Geo-Location, Geo-Tagging, Check-Ins & Proximity Search'),
        'type' => 'widget',
        'name' => 'sitetagcheckin.location-sitetagcheckin',
        'defaultParams' => array(
            'title' => $view->translate(""),
            'titleCount' => "",
        ),
    ),
    array(
        'title' => $view->translate('Albums Location Suggestions'),
        'description' => $view->translate('This widget will enable you to nicely prompt users to add location to their photo albums in an interactive manner. If a photo in the album has location associated, then the user will be able to choose if that is the location for the whole album, else will be able to select a new location.'),
        'category' => $view->translate('Geo-Location, Geo-Tagging, Check-Ins & Proximity Search'),
        'type' => 'widget',
        'name' => 'sitetagcheckin.location-suggestions-sitetagcheckin',
        'defaultParams' => array(
            'title' => $view->translate('Add Location to Your Photos'),
            'titleCount' => false,
        ),
      ),
      		array(
			'title' => $view->translate('Browse Albums’ Locations'),
			'description' => $view->translate('Displays a list of all the Albums having location entered corresponding to them on the site. This widget should be placed on Browse Albums’ Locations page.'),
			'category' => $view->translate('Geo-Location, Geo-Tagging, Check-Ins & Proximity Search'),
			'type' => 'widget',
			'name' => 'sitetagcheckin.bylocation-album',
			'defaultParams' => array(
			'title' => '',
			'titleCount' => true,
			),
		),
		
		array(
			'title' => 'Search Albums Location Form',
			'description' => 'Displays the form for searching Albums corresponding to location on the basis of various filters.',
			'category' => $view->translate('Geo-Location, Geo-Tagging, Check-Ins & Proximity Search'),
			'type' => 'widget',
			'name' => 'sitetagcheckin.albumlocation-search',
			'defaultParams' => array(
			'title' => '',
			'titleCount' => true,
			),
		),
      );
      }
      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('event')) {
  $event_array = array(
  array(
        'title' => $view->translate('Browse Events’ Locations'),
        'description' => $view->translate('Displays a list of all the events having location entered corresponding to them on the site. This widget should be placed on Browse Events’ Locations page.'),
        'category' => $view->translate('Geo-Location, Geo-Tagging, Check-Ins & Proximity Search'),
        'type' => 'widget',
        'name' => 'sitetagcheckin.bylocation-event',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
			'title' => 'Search Events Location Form',
			'description' => 'Displays the form for searching Events corresponding to location on the basis of various filters.',
			'category' => $view->translate('Geo-Location, Geo-Tagging, Check-Ins & Proximity Search'),
			'type' => 'widget',
			'name' => 'sitetagcheckin.location-search',
			'defaultParams' => array(
				'title' => '',
				'titleCount' => true,
			),
    ),

    array(
			'title' => 'Event Profile: Sync Event’s Location',
			'description' => 'This widget automatically syncs the location of the event on which it is placed with Google Places. This widget should be placed on Event Profile Page.',
			'category' => $view->translate('Geo-Location, Geo-Tagging, Check-Ins & Proximity Search'),
			'type' => 'widget',
			'name' => 'sitetagcheckin.syncevents-location',
// 			'defaultParams' => array(
// 				'title' => '',
// 				'titleCount' => true,
// 			),
    ),
    );
    }
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('group')){
   $group_array = array(
   array(
        'title' => $view->translate('Browse Groups’ Locations'),
        'description' => $view->translate('Displays a list of all the Groups having location entered corresponding to them on the site. This widget should be placed on Browse Groups’ Locations page.'),
        'category' => $view->translate('Geo-Location, Geo-Tagging, Check-Ins & Proximity Search'),
        'type' => 'widget',
        'name' => 'sitetagcheckin.bylocation-group',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
			'title' => 'Search Groups Location Form',
			'description' => 'Displays the form for searching Groups corresponding to location on the basis of various filters.',
			'category' => $view->translate('Geo-Location, Geo-Tagging, Check-Ins & Proximity Search'),
			'type' => 'widget',
			'name' => 'sitetagcheckin.grouplocation-search',
			'defaultParams' => array(
				'title' => '',
				'titleCount' => true,
			),
    ),
    );
}
if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('video')){
	$video_array = array(
		array(
			'title' => $view->translate('Browse Videos’ Locations'),
			'description' => $view->translate('Displays a list of all the Videos having location entered corresponding to them on the site. This widget should be placed on Browse Videos’ Locations page.'),
			'category' => $view->translate('Geo-Location, Geo-Tagging, Check-Ins & Proximity Search'),
			'type' => 'widget',
			'name' => 'sitetagcheckin.bylocation-video',
			'defaultParams' => array(
			'title' => '',
			'titleCount' => true,
			),
		),
		
		array(
			'title' => 'Search Videos Location Form',
			'description' => 'Displays the form for searching Videos corresponding to location on the basis of various filters.',
			'category' => $view->translate('Geo-Location, Geo-Tagging, Check-Ins & Proximity Search'),
			'type' => 'widget',
			'name' => 'sitetagcheckin.videolocation-search',
			'defaultParams' => array(
			'title' => '',
			'titleCount' => true,
			),
		),
	);
}
if (!empty($video_array)) {
  $final_array = array_merge($final_array, $video_array);
}
if (!empty($group_array)) {
  $final_array = array_merge($final_array, $group_array);
}
if (!empty($event_array)) {
  $final_array = array_merge($final_array, $event_array);
}
if (!empty($album_array)) {
  $final_array = array_merge($final_array, $album_array);
}
return $final_array;
  
?>