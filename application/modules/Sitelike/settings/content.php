<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
  $aafModuleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'advancedactivity'
  );
	$mixSettingsResults = Engine_Api::_()->getDbtable( 'mixsettings' , 'sitelike' )->getMixLikeItems();
	$mixSettingsItems = array_merge(array(""),$mixSettingsResults);
	$view = Zend_Registry::isRegistered( 'Zend_View' ) ? Zend_Registry::get( 'Zend_View' ) : null ;

	if ( Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'user' ) ) {
	$contentWidgetName[] = array (
		'title' => $view->translate( 'User Likes' ) ,
		'description' => $view->translate( 'Displays the content and profiles liked by a member on his profile. This widget should be placed on Member Profile page. Settings for this can be done in the Other Widgets section.' ) ,
		'category' => $view->translate( 'Likes' ) ,
		'type' => 'widget' ,
		'name' => 'sitelike.profile-user-likes' ,
		'isPaginated' => true,
		'defaultParams' => array(
				'itemCountPerPage' => 3,
		),
	) ;
	}

	$contentWidgetName[] = array (
    'title' => $view->translate( 'Likes Navigation Tabs' ) ,
    'description' => $view->translate( 'Contains the navigation tabs like : Liked Items, My Likes, My Friends’ 	Likes, etc. This navigation widget should be placed in the Browse Liked Items page.' ) ,
    'category' => $view->translate( 'Likes' ) ,
    'type' => 'widget' ,
    'name' => 'sitelike.navigation-like' ,
  ) ;

	$contentWidgetName[] = array (
    'title' => $view->translate( 'Most Liked Items' ) ,
    'description' => $view->translate( 'Displays the Most Liked content and profiles. Tabs can be configured in this widget. Settings for this can be done in the Tabbed Widgets and Mixed Content Widgets sections.' ) ,
    'category' => $view->translate( 'Likes' ) ,
    'type' => 'widget' ,
    'name' => 'sitelike.mix-like' ,
  ) ;

  if (!empty($aafModuleEnabled)) {
  	$contentWidgetName[] = array (
			'title' => $view->translate( 'Welcome: Most Liked Items' ) ,
			'description' => $view->translate( 'This widget is for the Welcome Tab in Advanced Activity Feeds. It
displays the overall Most Liked content and profiles.' ) ,
			'category' => $view->translate( 'Likes' ) ,
			'type' => 'widget' ,
			'name' => 'sitelike.welcomemix-like' ,
    ) ;
  }
  
	$contentWidgetName[] = array (
    'title' => $view->translate( 'Liked Items' ) ,
    'description' => $view->translate( 'Displays the Recent, Most Popular and Random liked content and profiles. Settings for this can be done in the Global Settings and Mixed Content Widgets sections.' ) ,
    'category' => $view->translate( 'Likes' ) ,
    'type' => 'widget' ,
    'name' => 'sitelike.list-browse-mixlikes' ,
		'isPaginated' => true,
		'defaultParams' => array(
				'title' => $view->translate( 'Liked Items' ) ,
				'itemCountPerPage' => 10,
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
					),
				),
			),
		),
  ) ;

  $contentWidgetName[] =  array(
    'title' => $view->translate( 'Like Button'),
    'description' => $view->translate( 'Displays the Like Button to Like the content / member. This is a generalized widget for content / member profiles. This widget should be placed on a Module
Profile page, preferably above Tabbed Block if present.'),
    'category' => $view->translate( 'Likes'),
    'type' => 'widget',
    'name' => 'sitelike.common-like-button',
    'defaultParams' => array(
    'title' => '',
    ),
  );

	$contentWidgetName[] = array (
    'title' => $view->translate( 'Likes (Everyone)' ) ,
    'description' => $view->translate( 'Displays all the people who have liked a content item. This is a generalized widget for content / member profiles. This widget should be placed on a Module Profile page in the left column.' ) ,
    'category' => $view->translate( 'Likes' ) ,
    'type' => 'widget' ,
    'name' => 'sitelike.common-like' ,
		'isPaginated' => true,
		'defaultParams' => array(
			'title' => '',
			'itemCountPerPage' => 3,
		),
  ) ;

	$contentWidgetName[] = array (
    'title' => $view->translate( 'Likes (Friends)' ) ,
    'description' => $view->translate( 'Displays the friends who have liked a content item. This is a generalized widget for content / member profiles. This widget should be placed on a Module Profile
page in the left column.' ) ,
    'category' => $view->translate( 'Likes' ) ,
    'type' => 'widget' ,
    'name' => 'sitelike.common-friend-like' ,
		'isPaginated' => true,
		'defaultParams' => array(
            'title' => '',
            'itemCountPerPage' => 3,
        ),
  ) ;

  $contentWidgetName[] = array (
    'title' => $view->translate( 'Most Liked Items (selected content)' ) ,
    'description' => $view->translate('Displays the Most Liked Content for the content type that you select for this widget. Settings for this can be done in the Tabbed Widgets section of Likes
    Plugin & Widgets. The content types available are the ones enabled from the "Manage Modules" section of Likes Plugin & Widgets. You can place this widget multiple times on a page with different
    content type chosen for each placement.') ,
    'category' => $view->translate( 'Likes' ) ,
    'type' => 'widget' ,
    'autoEdit' => true,
    'name' => 'sitelike.list-like-items' ,
    'defaultParams' => array (
      'title' => $view->translate( 'Most Liked Items type' ) ,
    ) ,
		'adminForm' => array(
			'elements' => array(
				array(
					'select',
					'resource_type',
					array(
						'label' => $view->translate('Select the content'),
						'multiOptions' =>	$mixSettingsItems,
					)
				),
			),
		)
	);
	return $contentWidgetName;
?>