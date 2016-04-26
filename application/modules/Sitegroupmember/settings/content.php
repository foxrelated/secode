<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupmember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
$manageCategorySettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupmember.category.settings', 1);

$categories_member = Engine_Api::_()->getDbtable('roles', 'sitegroupmember')->getSiteAdminRoles(array(), 'adminParams');
$categoryOptions = array();
$categoryOptions['0'] = 'Un-categorized (Display members who have not selected their membership roles.)';
if (!empty($categories_member)) {
	asort($categories_member, SORT_LOCALE_STRING);

	foreach( $categories_member as $v ) {
		if ($manageCategorySettings != 1) {
			$categoryOptions['groupadminRole'] = 'Roles created by Group Admins';
		}
	  $row = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategory($v['group_category_id']);
		$categoryOptions[$v['role_id']] = $v['role_name'] . '  [' .  $row->category_name . ']';
	}
}

$categories = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategories();
if (count($categories) != 0) {
  $categories_prepared[0] = "";
  foreach ($categories as $category) {
    $categories_prepared[$category->category_id] = $category->category_name;
  }
}

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
$final_array =  array(
	array(
		'title' => 'Group Profile Members',
		'description' => 'This widget form the Member tab on the Group Profile and displays the members of the Group. You can choose to display all members or members based on their Roles by using the edit settings of this widget. It should be placed in the Tabbed Blocks area of the Group Profile.',
		'category' => 'Group Profile',
		'type' => 'widget',
		'name' => 'sitegroupmember.profile-sitegroupmembers',
		'defaultParams' => array(
			'title' => 'Members',
			'titleCount' => true,
		),
	  'adminForm' => array(
			'elements' => array(
				array(
					'Radio',
					'show_option',
					array(
						'label' => 'Do you want to display members on the basis of their roles?',
						'multiOptions' => array(
						    '0' => 'Yes, display members based on their roles.',
								'1' => 'No, display all members.',	
						),'value' => 1,
					)
				),
				array(
					'MultiCheckbox',
					'roles_id',
					array(
						'label' => 'Choose the member roles which you want to display in this block.',
						'multiOptions' => $categoryOptions,
					),
				),
			),
		),
	),
	
	 array(
    'title' => 'Group Profile Announcements',
    'description' => 'Displays list of announcements posted by group admins for their Groups. This widget should be placed on the Group Profile.',
    'category' => 'Group Profile',
    'type' => 'widget',
    'name' => 'sitegroupmember.profile-sitegroupmembers-announcements',
		'defaultParams' => array(
			'title' => 'Announcements',
			'titleCount' => true,
		),
		'adminForm' => array(
			'elements' => array(
				array(
					'Text',
					'itemCount',
					array(
							'label' => 'Count',
							'description' => '(number of announcements to show)',
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
		'title' => 'Group’s Member of the Day',
		'description' => 'Displays the Member of the Day as selected by the Admin from the ‘Member of the Day’ section of this plugin.',
		'category' => 'Groups',
		'type' => 'widget',
		'name' => 'sitegroupmember.member-of-the-day',
		//'adminForm' => 'Sitegroupmember_Form_Admin_Item',
		'defaultParams' => array(
				'title' => 'Member of the Day'
		),
	),

	array(
		'title' => 'Group’s Featured Members Slideshow',
		'description' => 'Displays featured members in an attractive slideshow. You can set the count of the number of members to show in this widget. If the total number of members featured are more than that count, then the members to be displayed will be sequentially picked up.',
		'category' => 'Groups',
		'type' => 'widget',
		'name' => 'sitegroupmember.featured-members-slideshow',
		'isPaginated' => true,
		'defaultParams' => array(
				'title' => 'Featured Members',
				'itemCountPerGroup' => 10,
		),
	),
  
  array(
		'title' => 'Recent / Top Group Joiners',
		'description' => 'Displays the recent / top Group joiners on the site. You can place this widget multiple times on a group.',
		'category' => 'Groups',
		'type' => 'widget',
		'name' => 'sitegroupmember.home-recent-mostvaluable-sitegroupmember',
		'defaultParams' => array(
				'title' => $view->translate('Recent Group Joiners')
		),
		'adminForm' => array(
			'elements' => array(
				array(
				'Select',
				'select_option',
					array(
						'label' => 'Choose recent / top Group joiners to be shown in this block.',
						'multiOptions' => array(
							1 => 'Recent Group Joiners',
							2 => 'Top Group Joiners',
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
			'title' => 'Browse Members',
			'description' => 'Displays the link to view Group’s Members Browse group.',
			'category' => 'Groups',
			'type' => 'widget',
			'name' => 'sitegroupmember.sitegroupmemberlist-link',
			'defaultParams' => array(
					'title' => '',
					'titleCount' => true,
			),
	),
    
	array(
	'title' => 'Search Group Members form',
	'description' => 'Displays the form for searching Group Members on the basis of various filters. You can edit the fields to be available in this form.',
	'category' => 'Groups',
	'type' => 'widget',
	'name' => 'sitegroupmember.search-sitegroupmember',
	'defaultParams' => array(
			'title' => '',
			'search_column' => array("0" => "1", "1" => "2", "2" => "3", "3" => "4", "4" => '5'),
			'titleCount' => true,

	),
	
	'adminForm' => array(
					'elements' => array(
					array(
							'MultiCheckbox',
							'search_column',
							array(
									'label' => 'Choose the fields that you want to be available in the Search Group Members form widget.',
									'multiOptions' => array("2" => "Browse By", "3" => "Group Title", "4" => "Member Keywords", "5" => "Group Category"),
							),
					),
			),
	)
),
     array(
        'title' => 'Group Members',
        'description' => 'Displays the list of Members from Groups created on your community. This widget should be placed in the widgetized Group Members group. Results from the Search Group Members form are also shown here.',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroupmember.sitegroup-member',
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
                        'description' => '(number of members to show)',
                        'value' => 20,
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
        'title' => 'Group’s Ajax based Tabbed widget for Members',
        'description' => 'Displays the Recent Group Joiners and Featured Members in separate AJAX based tabs.',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroupmember.list-members-tabs-view',
        'defaultParams' => array(
            'title' => 'Members',
            'margin_photo'=>12,
            'showViewMore'=>1
        ),
         'adminForm' => array(
            'elements' => array(
                 array(
                  'Radio',
                  'showViewMore',
                  array(
                      'label' => 'Show "View More" link',
                      'multiOptions' => array(
                          '1' => 'Yes',
                          '0' => 'No',
                      ),'value' => 1,
                  )
              ),
							array(
								'Text',
								'itemCount',
									array(
										'label' => 'Count',
										'description' => '(number of members to show)',
										'value' => 10,
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
        'title' => 'Most Joined Groups',
        'description' => 'Displays a list of groups having maximum number of members. You can choose number of members to be shown.',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroupmember.mostjoined-sitegroup',
        'defaultParams' => array(
            'title' => 'Most Joined Groups',
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
            ),
        ),
    ),
    array(
        'title' => 'Most Popular Groups',
        'description' => 'Displays the list of Groups having maximum number of comments / likes / views / members. You can place this widget multiple times on a group with different popularity criterion chosen for each placement.',
        'category' => 'Groups',
        'type' => 'widget',
        'name' => 'sitegroupmember.mostactive-sitegroup',
        'defaultParams' => array(
            'title' => 'Most Popular Groups',
            'titleCount' => true,
							'statistics' => array("members")
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'active_groups',
                    array(
                        'label' => 'Select popularity criteria.',
                        'multiOptions' => array('comment_count' => 'Comments', 'like_count' => 'Likes', 'view_count' => 'Views', 'member_count' => 'Members'),
                        'value' => 'member_count',
                    )
                ),
                	array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => 'Select the information options that you want to be available in this block.',
                        'multiOptions' => array("comments" => "Comments", "likes" => "Likes", "views" => "Views", "members" => "Members"),
                    ),
                ), 
            ),
        ),
    ),
		array(
			'title' => 'Group Profile Cover Photo and Members',
			'description' => 'Displays the cover photo of a Group. From the Edit Settings section of this widget, you can also choose to display group member’s profile photos, if Group Admin has not selected a cover photo. It is recommended to place this widget on the Group Profile at the top.',
			'category' => 'Group Profile',
			'type' => 'widget',
			'name' => 'sitegroupmember.groupcover-photo-sitegroupmembers',
			'defaultParams' => array(
				'title' => '',
				'titleCount' => true,
				'showContent' => array("title", "followButton", "likeButton", "joinButton", "addButton"),
				'statistics' => array("followCount", "likeCount", "memberCount")
			),
			'adminForm' => array(
				'elements' => array(
               array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Enter the cover photo height (in px). (Minimum 150 px required.)',
                        'value' => '300',
                    )
                ),
                 array(
                    'Select',
                    'memberCount',
                    array(
                        'label' => 'Select members to be displayed in a row.',
                        'multiOptions' => array('1' => '1', '2' => '2','3' => '3', '4' => '4', '5' => '5','6'=>'6','7' => '7', '7' => '7','8' => '8', '9' => '9', '10' => '10','11'=>'11','12'=>'12'),
                        'value' => '8',
                    )
                ),
								array(
									'Radio',
									'onlyMemberWithPhoto',
									array(
										'label' => 'Do you want to show only those members who have uploaded their profile pictures?',
										'multiOptions' => array(
												'1' => 'Yes',
												'0' => 'No',
										),
										'value' => 1,
									),
					      ),
					      array(
                    'MultiCheckbox',
                    'showContent',
                    array(
                        'label' => 'Select the information options that you want to be available in this block.',
                        'multiOptions' => array('title' => 'Group Title' ,"followButton" => "Follow", "likeButton" => "Like", "joinButton" => "Join Group", "addButton" => "Add People"),
                    ),
                ), 
                	array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => 'Select the information options that you want to be available in this block.',
                        'multiOptions' => array("followCount" => "Follow", "likeCount" => "Like", "memberCount" => "Member"),
                    ),
                ), 
				),
			),
		)
);

return $final_array;