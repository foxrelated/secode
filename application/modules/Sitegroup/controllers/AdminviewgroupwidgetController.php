<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminviewwidgetController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
//GET CONTENT TABLE
$tableContent = Engine_Api::_()->getDbtable('content', 'core');

//GET CONTENT TABLE NAME
$tableContentName = $tableContent->info('name');

$tableName = Engine_Api::_()->getDbtable('pages', 'core');

//GET WIDGETIZED GROUP INFORMATION
$selectGroup = Engine_Api::_()->sitegroup()->getWidgetizedGroup();

if (empty($selectGroup)) {
  $groupCreate = $tableName->createRow();
  $groupCreate->name = 'sitegroup_index_view';
  $groupCreate->displayname = 'Group Profile';
  $groupCreate->title = 'Group Profile';
  $groupCreate->description = 'This is the group view  group.';
  $groupCreate->custom = 1;
  $group_id = $groupCreate->save();
} else {
  $group_id = $selectGroup->page_id;
}

if (!empty($group_id)) {
  $tableContent->delete(array('page_id =?' => $group_id));
  //INSERT MAIN CONTAINER
  $mainContainer = $tableContent->createRow();
  $mainContainer->page_id = $group_id;
  $mainContainer->type = 'container';
  $mainContainer->name = 'main';
  $mainContainer->order = 2;
  $mainContainer->save();
  $container_id = $mainContainer->content_id;

  //INSERT MAIN-MIDDLE CONTAINER
  $mainMiddleContainer = $tableContent->createRow();
  $mainMiddleContainer->page_id = $group_id;
  $mainMiddleContainer->type = 'container';
  $mainMiddleContainer->name = 'middle';
  $mainMiddleContainer->parent_content_id = $container_id;
  $mainMiddleContainer->order = 6;
  $mainMiddleContainer->save();
  $middle_id = $mainMiddleContainer->content_id;

  //INSERT MAIN-LEFT CONTAINER
  $mainLeftContainer = $tableContent->createRow();
  $mainLeftContainer->page_id = $group_id;
  $mainLeftContainer->type = 'container';
  $mainLeftContainer->name = 'right';
  $mainLeftContainer->parent_content_id = $container_id;
  $mainLeftContainer->order = 4;
  $mainLeftContainer->save();
  $left_id = $mainLeftContainer->content_id;
  $showmaxtab = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.showmore', 8);

	//if(Engine_Api::_()->getApi("settings", "core")->getSetting('sitegroup.layout.setting', 1)){
		//INSERT MAIN-MIDDLE-TAB CONTAINER
		$middleTabContainer = $tableContent->createRow();
		$middleTabContainer->page_id = $group_id;
		$middleTabContainer->type = 'widget';
		$middleTabContainer->name = 'core.container-tabs';
		$middleTabContainer->parent_content_id = $middle_id;
		$middleTabContainer->order = 10;
		$middleTabContainer->params = "{\"max\":\"$showmaxtab\"}";
		$middleTabContainer->save();
		$middle_tab = $middleTabContainer->content_id;
	//}
		//INSERTING THUMB PHOTO WIDGET
		Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.thumbphoto-sitegroup', $middle_id, 3, '','true');
  if(empty($sitegroup_layout_cover_photo)) {
  
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.group-profile-breadcrumb', $middle_id,1, '', 'true');

		//INSERTING GROUP PROFILE GROUP COVER PHOTO WIDGET
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
			Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroupmember.groupcover-photo-sitegroupmembers', $middle_id, 2, '','true');
    }

		//INSERTING TITLE WIDGET
		Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.title-sitegroup', $middle_id, 4, '','true');
		
		//INSERTING LIKE WIDGET
		Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($tableContent, $tableContentName, $group_id, 'widget', 'seaocore.like-button', $middle_id, 5, '','true');
		
		//INSERTING FOLLOW WIDGET
		Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($tableContent, $tableContentName, $group_id, 'widget', 'seaocore.seaocore-follow', $middle_id, 6,'','true');

		//INSERTING FACEBOOK LIKE WIDGET
		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse')) {
			Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($tableContent, $tableContentName, $group_id, 'widget', 'Facebookse.facebookse-sitegroupprofilelike', $middle_id, 7, '','true');
		}

		//INSERTING MAIN PHOTO WIDGET 
		Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.mainphoto-sitegroup', $left_id, 10, '','true');

  } 
  else {
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.group-profile-breadcrumb', $middle_id,1, '', 'true');
  
		//INSERTING GROUP PROFILE GROUP COVER PHOTO WIDGET
		Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.group-cover-information-sitegroup', $middle_id, 2, '','true');
  }

  //INSERTING CONTACT DETAIL WIDGET
  Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.contactdetails-sitegroup', $middle_id, 8, '','true');
  
	//INSERTING WIDGET LINK WIDGET 
  if(!Engine_Api::_()->getApi("settings", "core")->getSetting('sitegroup.layout.setting', 1)){
	   Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.widgetlinks-sitegroup', $left_id, 11, '', 'true');
  }

  //INSERTING OPTIONS WIDGET
  Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.options-sitegroup', $left_id, 12, '','true');

  //INSERTING INFORMATION WIDGET 
  Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.information-sitegroup', $left_id, 10, 'Information', 'true');

  //INSERTING WRITE SOMETHING ABOUT WIDGET 
  Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($tableContent, $tableContentName, $group_id, 'widget', 'seaocore.people-like', $left_id, 15, '','true');

  //INSERTING RATING WIDGET 
  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroupreview.ratings-sitegroupreviews', $left_id, 16, 'Ratings', 'true');
  }

  //INSERTING BADGE WIDGET 
  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupbadge')) {
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroupbadge.badge-sitegroupbadge', $left_id, 17, 'Badge', 'true');
  }

  //INSERTING YOU MAY ALSO LIKE WIDGET 
  Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.suggestedgroup-sitegroup', $left_id, 18, 'You May Also Like', 'true');
  
	$social_share_default_code = '{"title":"Social Share","titleCount":true,"code":"<div class=\"addthis_toolbox addthis_default_style \">\r\n<a class=\"addthis_button_preferred_1\"><\/a>\r\n<a class=\"addthis_button_preferred_2\"><\/a>\r\n<a class=\"addthis_button_preferred_3\"><\/a>\r\n<a class=\"addthis_button_preferred_4\"><\/a>\r\n<a class=\"addthis_button_preferred_5\"><\/a>\r\n<a class=\"addthis_button_compact\"><\/a>\r\n<a class=\"addthis_counter addthis_bubble_style\"><\/a>\r\n<\/div>\r\n<script type=\"text\/javascript\">\r\nvar addthis_config = {\r\n          services_compact: \"facebook, twitter, linkedin, google, digg, more\",\r\n          services_exclude: \"print, email\"\r\n}\r\n<\/script>\r\n<script type=\"text\/javascript\" src=\"http:\/\/s7.addthis.com\/js\/250\/addthis_widget.js\"><\/script>","nomobile":"","name":"sitegroup.socialshare-sitegroup"}';
	
  //INSERTING SOCIAL SHARE WIDGET 
  Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.socialshare-sitegroup', $left_id, 19, 'Social Share','true', $social_share_default_code);

//  //INSERTING FOUR SQUARE WIDGET 
//  Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.foursquare-sitegroup', $left_id, 20,'','true');

  //INSERTING INSIGHTS WIDGET 
  Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.insights-sitegroup', $left_id, 21, 'Insights','true');

  //INSERTING FEATURED OWNER WIDGET 
  Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.featuredowner-sitegroup', $left_id, 22, 'Owners', 'true');

  //INSERTING ALBUM WIDGET 
  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum')) {
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.albums-sitegroup', $left_id, 23, 'Albums' ,'true');
  }
 
  //INSERTING GROUP PROFILE PLAYER WIDGET 
  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic')) {
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroupmusic.profile-player', $left_id, 24,'','true');
  }
 
  //INSERTING ALBUM WIDGET   
  Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.favourite-group', $left_id, 25, 'Linked Groups', 'true');
  //if(Engine_Api::_()->getApi("settings", "core")->getSetting('sitegroup.layout.setting', 1)){
		//INSERTING ACTIVITY FEED WIDGET
		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
			$advanced_activity_params = '{"title":"Updates","advancedactivity_tabs":["aaffeed"],"nomobile":"0","name":"advancedactivity.home-feeds"}';
			Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($tableContent, $tableContentName, $group_id, 'widget', 'advancedactivity.home-feeds', $middle_tab, 2, 'Updates', 'true',$advanced_activity_params);
		} else {
			Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($tableContent, $tableContentName, $group_id, 'widget', 'activity.feed', $middle_tab, 2, 'Updates', 'true');
		}

		//INSERTING INFORAMTION WIDGET
		Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.info-sitegroup', $middle_tab, 3, 'Info', 'true');

		//INSERTING OVERVIEW WIDGET
		Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.overview-sitegroup', $middle_tab, 4, 'Overview', 'true');

		//INSERTING LOCATION WIDGET
		Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($tableContent, $tableContentName, $group_id, 'widget', 'sitegroup.location-sitegroup', $middle_tab, 5, 'Map', 'true');

		//INSERTING LINKS WIDGET
		Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($tableContent, $tableContentName, $group_id, 'widget', 'core.profile-links', $middle_tab, 125, 'Links', 'true');
  //}
}
?>