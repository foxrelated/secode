<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content_user.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

$categories_prepared = array();
$categories = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategories();
if (count($categories) != 0) {
  $categories_prepared[0] = "";
  foreach ($categories as $category) {
    $categories_prepared[$category->category_id] = $category->category_name;
  }
}

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
$ads_Array = array();
$social_share_default_code = '<div class="addthis_toolbox addthis_default_style ">
<a class="addthis_button_preferred_1"></a>
<a class="addthis_button_preferred_2"></a>
<a class="addthis_button_preferred_3"></a>
<a class="addthis_button_preferred_4"></a>
<a class="addthis_button_preferred_5"></a>
<a class="addthis_button_compact"></a>
<a class="addthis_counter addthis_bubble_style"></a>
</div>
<script type="text/javascript">
var addthis_config = {
          services_compact: "facebook, twitter, linkedin, google, digg, more",
          services_exclude: "print, email"
}
</script>
<script type="text/javascript" src="https://s7.addthis.com/js/250/addthis_widget.js"></script>';
$final_array = array(
    array(
        'title' => $view->translate('Group Profile Overview'),
        'description' => $view->translate('Displays rich overview on Group\'s profile, created by you using the editor from Group Dashboard. This should be placed in the Tabbed Blocks area of Group Profile.'),
        'category' => $view->translate('Group Profile'),
        'type' => 'widget',
        'name' => 'sitegroup.overview-sitegroup',
        'defaultParams' => array(
            'title' => $view->translate('Overview'),
            'titleCount' => true,
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
        'title' => 'Parent Group of a Sub Group',
        'description' => 'Displays the parent group in which the currently viewed sub groups is created. This widget should be placed on the Group Profile group.',
        'category' => 'Group Profile',
        'type' => 'widget',
        'name' => 'sitegroup.parentgroup-sitegroup',
        'defaultParams' => array(
            'title' => 'Parent Group of a Sub Group',
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
//    array(
//        'title' => $view->translate("Group Profile 'Save to foursquare' Button"),
//        'description' => $view->translate("This Button will enable Group visitors to add the Group's place or tip to their foursquare To-Do List. Note that this feature will be available to you based on your Group's Package and your Member Level."),
//        'category' => $view->translate('Group Profile'),
//        'type' => 'widget',
//        'name' => 'sitegroup.foursquare-sitegroup',
//        'defaultParams' => array(
//            'title' => '',
//            'titleCount' => true,
//        ),
//    ),
		array(
			'title' => $view->translate('Group Profile Social Share Buttons'),
			'description' => $view->translate("Contains Social Sharing buttons and enables users to easily share Groups on their favorite Social Networks. You can personalize the code for social sharing buttons by adding your own code generated from: <a href='http://www.addthis.com' target='_blank'>http://www.addthis.com</a>"),
			'category' => $view->translate('Group Profile'),
			'type' => 'widget',
			'name' => 'sitegroup.socialshare-sitegroup',
			'defaultParams' => array(
					'title' => $view->translate('Social Share'),
					'titleCount' => true,
			),
			'requirements' => array(
				'subject' => 'sitegroup_group',
			),
      'autoEdit' => true,
			'adminForm' => array(
				'elements' => array(
					array(
							'Textarea',
							'code',
							array(
									'description' => $view->translate("Social Sharing Buttons Code: You can personalize the code for social sharing buttons by adding your own code generated from: <a href='http://www.addthis.com' target='_blank'>http://www.addthis.com</a>"),
									'value' => $social_share_default_code,
									'decorators' => array('ViewHelper', array('Description', array('placement' => 'PREPEND','escape' => false)))
							),
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
        'title' => $view->translate('Group Profile Title'),
        'description' => $view->translate('Displays the Title of the Group. This widget should be placed on the Group Profile, in the middle column at the top.'),
        'category' => $view->translate('Group Profile'),
        'type' => 'widget',
        'name' => 'sitegroup.title-sitegroup',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Group Profile Info'),
        'description' => $view->translate('This widget forms the Info tab on the Group Profile and displays the information of the Group. It should be placed in the Tabbed Blocks area of the Group Profile. You may enter content for this section from the Edit Info and Profile Info sections of the Group Dashboard.'),
        'category' => $view->translate('Group Profile'),
        'type' => 'widget',
        'name' => 'sitegroup.info-sitegroup',
        'defaultParams' => array(
            'title' => $view->translate('Info'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Group Profile Information Group'),
        'description' => $view->translate('Displays the owner, category, tags, views and other information about a Group. This widget should be placed on the Group Profile in the left column.'),
        'category' => $view->translate('Group Profile'),
        'type' => 'widget',
        'name' => 'sitegroup.information-sitegroup',
        'defaultParams' => array(
            'title' => $view->translate('Information'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Group Profile Photo'),
        'description' => $view->translate('Displays the main cover photo of a Group. This widget must be placed on the Group Profile at the top of left column.'),
        'category' => $view->translate('Group Profile'),
        'type' => 'widget',
        'name' => 'sitegroup.mainphoto-sitegroup',
        'defaultParams' => array(
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Group Profile Map'),
        'description' => $view->translate('This widget forms the Map tab on the Group Profile. It displays the map showing the Group position as well as the location details of the group. It should be placed in the Tabbed Blocks area of the Group Profile. Location details can be entered from the Location section of the Dashboard. Note that this feature will be available to you based on your Group\'s Package and your Member Level.'),
        'category' => $view->translate('Group Profile'),
        'type' => 'widget',
        'name' => 'sitegroup.location-sitegroup',
        'defaultParams' => array(
            'title' => $view->translate('Map'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Group Profile Options'),
        'description' => $view->translate('Displays the various action link options to users viewing your Group. This widget should be placed on the Group Profile in the left column, below the Group profile photo.'),
        'category' => $view->translate('Group Profile'),
        'type' => 'widget',
        'name' => 'sitegroup.options-sitegroup',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Group Profile Owner Group Tags'),
        'description' => $view->translate('Displays all the tags chosen by the owner of your Group for his Groups. This widget should be placed on the Group Profile.'),
        'category' => $view->translate('Group Profile'),
        'type' => 'widget',
        'name' => 'sitegroup.tags-sitegroup',
        'defaultParams' => array(
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Group Profile Owner Groups'),
        'description' => $view->translate('Displays other Groups owned by the owner of your Group. This widget should be placed on the Group Profile.'),
        'category' => $view->translate('Group Profile'),
        'type' => 'widget',
        'name' => 'sitegroup.usergroup-sitegroup',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Group Profile About Group'),
        'description' => $view->translate('Displays the About Us information for your Group. You can enter information for this widget simply by clicking on the widget.'),
        'category' => $view->translate('Group Profile'),
        'type' => 'widget',
        'name' => 'sitegroup.write-group',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
			'title' => $view->translate('Content Profile: Follow Button'),
			'description' => $view->translate('This is the Follow Button to be placed on the Content Profile page. It enables users to Follow the content being currently viewed.'),
			'category' => $view->translate('Group Profile'),
			'type' => 'widget',
			'name' => 'seaocore.seaocore-follow',
			'defaultParams' => array(
					'title' => '',
			),
    ),
    array(
        'title' => $view->translate('Content Profile: Like Button for Content'),
        'description' => $view->translate('This is the Like Button to be placed on the Content Profile page. It enables users to Like the content being currently viewed. The best place to put this widget is right above the Tabbed Blocks on the Content Profile page.'),
        'category' => $view->translate('Group Profile'),
        'type' => 'widget',
        'name' => 'seaocore.like-button',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    array(
        'title' => $view->translate('Group Profile You May Also Like'),
        'description' => $view->translate('Displays the other Groups that a user may like, based on your Group.'),
        'category' => $view->translate('Group Profile'),
        'type' => 'widget',
        'name' => 'sitegroup.suggestedgroup-sitegroup',
        'defaultParams' => array(
            'title' => $view->translate('You May Also Like'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Most Liked Groups',
        'description' => 'Displays list of groups having maximum number of likes.',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroups.mostlikes-sitegroups',
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
        'title' => $view->translate('Content Profile: Content Likes'),
        'description' => $view->translate('Displays the users who have liked the content being currently viewed. This widget should be placed on the  Content Profile page.'),
        'category' => $view->translate('Group Profile'),
        'type' => 'widget',
        'name' => 'seaocore.people-like',
    ),
    array(
        'title' => $view->translate('Group Profile Group Insights'),
        'description' => $view->translate('Displays the insights of your Group to your Group Admins only. These insights include metrics like views, likes, comments and active users of the Group.'),
        'category' => $view->translate('Group Profile'),
        'type' => 'widget',
        'name' => 'sitegroup.insights-sitegroup',
        'defaultParams' => array(
            'title' => $view->translate('Insights'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Group Profile Apps Links'),
        'description' => $view->translate("Displays the Apps related links for your Group. This widget should be placed in the left column."),
        'category' => $view->translate('Group Profile'),
        'type' => 'widget',
        'name' => 'sitegroup.widgetlinks-sitegroup',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
        ),
    ),
		array(
				'title' => $this->view->translate('Group Profile Linked Groups'),
				'description' => $this->view->translate('Displays Linked Groups of your Group.'),
				'category' => $this->view->translate('Group Profile'),
				'type' => 'widget',
				'name' => 'sitegroup.favourite-group',
				'defaultParams' => array(
								'title' => $this->view->translate('Linked Groups'),
								'titleCount' => true,
				),
    ),
    array(
        'title' => $view->translate('Group Profile Featured Group Admins'),
        'description' => $view->translate("Displays the Featured Admins of your Group."),
        'category' => $view->translate('Group Profile'),
        'type' => 'widget',
        'name' => 'sitegroup.featuredowner-sitegroup',
        'defaultParams' => array(
            'title' => $view->translate("Group admins"),
            'titleCount' => "",
        ),
    ), 
    array(
        'title' => $view->translate('Group Profile Alternate Thumb Photo'),
        'description' => $view->translate('Displays the thumb photo of a Group. This works as an alternate profile photo when you have set the layout of Group Profile to be tabbed, from the Group Layout Settings, and have integrated with the "Advertisements / Community Ads Plugin" by SocialEngineAddOns. In that case, the left column of the Group Profile having the main profile photo gets hidden to accomodate Ads. This widget must be placed on the Group Profile at the top of middle column.'),
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
                        'label' => $view->translate('Show Group Profile Title.'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No'),
                        ),
                        'value' => 1,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => $view->translate('Group Profile Contact Details'),
        'description' => $view->translate("Displays the Contact Details of your Group."),
        'category' => $view->translate('Group Profile'),
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
                        'label' => $view->translate('Select the contact details you want to display'),
                        'multiOptions' => array("1" => "Phone", "2" => "Email", "3" => "Website"),
                    ),
                ),
                array(
									'Radio',
									'emailme',
									array(
											'label' => $view->translate('Do you want users to send emails to Groups via a customized pop up when they click on "Email Me" link?'),
											'multiOptions' => array(
													1 => $view->translate('Yes, open customized pop up'),
													0 => $view->translate('No, open browser`s default pop up')
											),
											'value' => '0'
									)
                ),
            ),
        ),
    ), 
);
if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
  $ads_Array = array(
      array(
          'title' => $view->translate('Group Profile Alternate Cover Thumb Photo'),
          'description' => $view->translate('Displays the thumb photo of your Group. This widget must be placed on the Group Profile at the top of middle column. This photo shows up only in special cases.'),
          'category' => $view->translate('Group Profile'),
          'type' => 'widget',
          'name' => 'sitegroup.thumbphoto-sitegroup',
          'defaultParams' => '',
      ),
  );
}
if (!empty($ads_Array)) {
  $final_array = array_merge($final_array, $ads_Array);
}

$fbgroup_sitegroup_Array = array(
      array(
          'title' => $view->translate('Facebook Like Box'),
          'description' => $view->translate('This widget contains the Facebook Like Box which enables Group Admins to gain Likes for their Facebook Page from this website. The edit popup contains the settings to customize the Facebook Like Box. This widget should be placed on the Group Profile.'),
          'category' => $view->translate('Group Profile'),
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
                        'label' => $view->translate('Title'),
                        'value' => '',
                    )
                ),
                
                 array(
                    "Text",
                    "fb_width",
                    array(
                        'label' => $view->translate('Width'),
                        'description' => $view->translate('Width of the Facebook Like Box in pixels.'),
                        'value' => '220',
                    )
                ),
              array(
                    "Text",
                    "fb_height",
                    array(
                        'label' => $view->translate('Height'),
                        'description' => $view->translate('Height of the Facebook Like Box in pixels (optional).'),
                        'value' => '588',
                    )
                ),
                array(
                    "Select",
                    "widget_color_scheme",
                    array(
                        'label' => $view->translate('Color Scheme'),
                        'description' => $view->translate('Color scheme of the Facebook Like Box in pixels.'),
                       'multiOptions' => array('light' => 'light', 'dark' => 'dark')
                    )
                ),
                array(
                    "MultiCheckbox",
                    "widget_show_faces",
                    array(
                        //'label' => 'Show Profile Photos in this plugin.',
                        'description' => $view->translate('Show Faces'),
                        'multiOptions' => array('1' => $view->translate('Show profile photos of users who like the linked Facebook Page in the Facebook Like Box.'))
                         
                    )
                ),
                
                array(
                    "Text",
                    "widget_border_color",
                    array(
                        'label' => $view->translate('Border Color'),
                        'description' => $view->translate('The border color of the plugin')
                       
                         
                    )
                ),
                array(
                    "MultiCheckbox",
                    "show_stream",
                    array(
                        
                        'description' => $view->translate('Stream'),
                        'multiOptions' => array('1' => $view->translate('Show the Facebook Page profile stream for the public feeds in the Facebook Like Box.')),
                       
                        
                    )
                ),
                array(
                    "MultiCheckbox",
                    "show_header",
                    array(
                        
                        'description' => $view->translate('Header'),
                        'multiOptions' => array('1' => $view->translate("Show the 'Find us on Facebook' bar at top. Only shown when either stream or profile photos are present.")),
                       
                       
                    )
                ),
            )
        )
          
   ));
   
   if (!empty($fbgroup_sitegroup_Array)) {
  $final_array = array_merge($final_array, $fbgroup_sitegroup_Array);
}
return $final_array;
?>
