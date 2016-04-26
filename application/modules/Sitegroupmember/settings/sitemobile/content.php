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

$manageCategorySettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupmember.category.settings', 1);

if(Engine_Api::_()->core()->hasSubject('sitegroup_group')) {
	$categories_member = Engine_Api::_()->getDbtable('roles', 'sitegroupmember')->getRolesAssoc(Engine_Api::_()->core()->getSubject('sitegroup_group')->group_id);
} else {
  $categories_member = Engine_Api::_()->getDbtable('roles', 'sitegroupmember')->getSiteAdminRoles(array(), 'adminParams');
}

$categoryOptions = array();
$categoryOptions['0'] = 'Un-categorized (Display members who have not selected their membership roles.)';
if (!empty($categories_member)) {
	asort($categories_member, SORT_LOCALE_STRING);
	foreach( $categories_member as $key => $v ) {

    if(Engine_Api::_()->core()->hasSubject('sitegroup_group')) {
			$row = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategory(Engine_Api::_()->core()->getSubject('sitegroup_group')->category_id);
      $categoryOptions[$key] = $v . '  [' .  $row->category_name . ']';
    } else  {
			if ($manageCategorySettings != 1) {
				$categoryOptions['groupadminRole'] = 'Roles created by Group Admins';
			}
			$row = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategory($v['group_category_id']);
			$categoryOptions[$v['role_id']] = $v['role_name'] . '  [' .  $row->category_name . ']';
    }

		
	}
}

$final_array =  array(
	array(
		'title' => 'Group Profile Members',
		'description' => 'This widget form the Member tab on the Group Profile and displays the members of the Group. You can choose to display all members or members based on their Roles by using the edit settings of this widget. It should be placed in the Tabbed Blocks area of the Group Profile.',
		'category' => 'Group Profile',
		'type' => 'widget',
		'name' => 'sitegroupmember.sitemobile-profile-sitegroupmembers',
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
						'description' => 'Do you want to display members on the basis of their roles?',
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
						'description' => 'Choose the member roles which you want to display in this block.',
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
);

return $final_array;