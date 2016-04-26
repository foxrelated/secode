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

	$contentWidgetName[] = array (
    'title' => $view->translate( 'Likes Navigation Tabs' ) ,
    'description' => $view->translate( 'Contains the navigation tabs like : Liked Items, My Likes, My Friends’ 	Likes, etc. This navigation widget should be placed in the Browse Liked Items page.' ) ,
    'category' => $view->translate( 'Likes' ) ,
    'type' => 'widget' ,
    'name' => 'sitelike.navigation-like' ,
  ) ;

	$contentWidgetName[] = array (
    'title' => $view->translate( 'Liked Items' ) ,
    'description' => $view->translate( 'Displays the Recent, Most Popular and Random liked content and profiles. Settings for this can be done in the Global Settings and Mixed Content Widgets sections.' ),
    'category' => $view->translate( 'Likes' ) ,
    'type' => 'widget' ,
    'name' => 'sitelike.sitemobile-list-browse-mixlikes' ,
		'isPaginated' => true,
		'defaultParams' => array(
				'title' => $view->translate( 'Liked Items' ) ,
				'itemCountPerPage' => 10,
		),
			'adminForm' => array(
				'elements' => array(
					array(
							'Radio',
							'tab_show',
							array(
									'label' => $view->translate('Default ordering in Content Likes.'),
									'multiOptions' => array(
											'1' => $view->translate('Recent'),
											'2' => $view->translate('Most Popular.'),
											'0' => $view->translate('Random'),
									),
									'value' => '1',
							)
					),
			),
		)
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
    'description' => $view->translate( 'Displays all the people who have liked a content item. This is a generalized widget for content / member profiles. This widget should be placed on a Module
Profile page in the left column.' ) ,
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
            'itemCountPerPage' => 1,
        ),
  ) ;

	return $contentWidgetName;
?>