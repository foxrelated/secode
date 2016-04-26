<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupmember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content_user.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

$manageCategorySettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupmember.category.settings', 1);
$categories_member = Engine_Api::_()->getDbtable('roles', 'sitegroupmember')->getRolesAssoc(Engine_Api::_()->core()->getSubject('sitegroup_group')->group_id);
$categoryOptions = array();
$categoryOptions['0'] = 'Un-categorized (Display members who have not selected their membership roles.)';
if (!empty($categories_member)) {
	asort($categories_member, SORT_LOCALE_STRING);
	foreach( $categories_member as $key => $v ) {
	  $row = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategory(Engine_Api::_()->core()->getSubject('sitegroup_group')->category_id);
		$categoryOptions[$key] = $v . '  [' .  $row->category_name . ']';
	}
}

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
	array(
			'title' => $view->translate('Group Profile Members'),
			'description' => $view->translate('Displays your Group\'s members.'),
			'category' => $view->translate('Group Profile'),
			'type' => 'widget',
			'name' => 'sitegroupmember.profile-sitegroupmembers',
			'defaultParams' => array(
					'title' => $view->translate('Members'),
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
)
?>