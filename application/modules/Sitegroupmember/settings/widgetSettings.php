<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupmember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: WidgetSettings.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$db = Engine_Db_Table::getDefaultAdapter();

// ************* Start OnInstall() Code ***************
//START DELETE FOR PAGE JOIN FEED FROM STREAM TABLE.
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_modules')
        ->where('name = ?', 'sitegroupmember')
        ->where('version <= ?', '4.5.0p3');
$version_check = $select->query()->fetchObject();
if (!empty($version_check)) {

  $select = new Zend_Db_Select($db);
  $select->from('engine4_activity_stream', "action_id")->where('type = ?', 'sitegroup_join')->group('action_id');
  $str_action_ids = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
  if (!empty($str_action_ids)) {
    $select = new Zend_Db_Select($db);
    $select->from('engine4_activity_actions', "action_id")->where('type = ?', 'sitegroup_join')->where('action_id IN(?)', $str_action_ids);
    $action_ids = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
  }

  if (!empty($str_action_ids) && !empty($action_ids)) {
    $diff_action_ids = array_diff($str_action_ids, $action_ids);
  }

  if ($diff_action_ids) {
    $db->delete('engine4_activity_stream', array('action_id IN(?)' => $diff_action_ids));
  }
}

$select = new Zend_Db_Select($db);
$select->from('engine4_core_modules')
        ->where('name = ?', 'sitegroupmember')
        ->where('enabled = ?', 1);
$check_sitegroupmember = $select->query()->fetchObject();
if (empty($check_sitegroupmember)) {

  //All entry in manage admin table move in the membership table install group member plugin.
  $select = new Zend_Db_Select($db);
  $select->from('engine4_sitegroup_manageadmins', array('group_id', 'user_id'));
  $check_sitegroup = $select->query()->fetchAll();
  if (!empty($check_sitegroup)) {
    foreach ($check_sitegroup as $result) {
      $db->insert('engine4_sitegroup_membership', array(
          'resource_id' => $result['group_id'],
          'user_id' => $result['user_id'],
          'group_id' => $result['group_id'],
      ));
    }
  }

  //For member count for group table.
  $select = new Zend_Db_Select($db);
  $select->from('engine4_sitegroup_manageadmins', array('group_id', 'user_id', 'COUNT(*) as count'))->group('group_id');
  $check_count = $select->query()->fetchAll();

  if (!empty($check_count)) {
    foreach ($check_count as $check_counts) {
      $db->query("UPDATE `engine4_sitegroup_groups` SET `member_count` = '" . $check_counts['count'] . "' WHERE `engine4_sitegroup_groups`.`group_id` = '" . $check_counts['group_id'] . "';");
    }
  }


  //update all member level settings with new setting member. group plugin version condition.
  $select = new Zend_Db_Select($db);
  $select
          ->from('engine4_authorization_levels', array('level_id'))
          ->where('title != ?', 'public');
  $check_sitegroup = $select->query()->fetchAll();
  foreach ($check_sitegroup as $modArray) {

    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_authorization_permissions', array('params', 'name', 'level_id'))
            ->where('type LIKE "%sitegroup_group%"')
            ->where('level_id = ?', $modArray['level_id'])
            ->where('name != ?', 'auth_html')
            ->where('name LIKE "%auth_%"');
    $result = $select->query()->fetchAll();

    foreach ($result as $results) {
      $params = Zend_Json::decode($results['params']);
      $params[] = 'member';
      $paramss = Zend_Json::encode($params);
      $db->query("UPDATE `engine4_authorization_permissions` SET `params` = '$paramss' WHERE `engine4_authorization_permissions`.`type` = 'sitegroup_group' AND `engine4_authorization_permissions`.`name` = '" . $results['name'] . "' AND `engine4_authorization_permissions`.`level_id` = '" . $results['level_id'] . "';");
    }
  }
  //END
}

//Start Memeber Profile group Widget
$select = new Zend_Db_Select($db);
$select
			->from('engine4_core_pages')
			->where('name = ?', 'user_profile_index')
			->limit(1);
$group_id = $select->query()->fetchAll();

if (!empty($group_id)) {
	$page_id = $group_id[0]['page_id'];  
	$selectWidgetId = new Zend_Db_Select($db);
		$selectWidgetId->from('engine4_core_content', array('content_id'))
		->where('page_id =?', $page_id)
		->where('type = ?', 'widget')
		->where('name = ?', 'core.container-tabs')
		->limit(1);
	$fetchWidgetContentId = $selectWidgetId->query()->fetchAll(); 
	if (!empty($fetchWidgetContentId)) {
		$tab_id = $fetchWidgetContentId[0]['content_id'];
		
		// Check if it's already been placed
		$select = new Zend_Db_Select( $db ) ;
		$select
				->from( 'engine4_core_content' )
				->where( 'page_id = ?' , $page_id )
				->where( 'type = ?' , 'widget' )
				->where( 'name = ?' , 'sitegroup.profile-joined-sitegroup');
		$info = $select->query()->fetch();
		
		if(empty($info)) {
			$db->insert('engine4_core_content', array(
			'page_id' => $page_id,
			'type' => 'widget',
			'name' => 'sitegroup.profile-joined-sitegroup',
			'parent_content_id' => $tab_id,
			'order' => 999,
			'params' => '{"title":"Joined Groups","titleCount":""}',
			));
		}
	}
}
//End Memeber Profile group Widget

$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_pages')
        ->where('name = ?', 'sitegroupmember_index_home')
        ->limit(1);
$info = $select->query()->fetch();

if (empty($info)) {
  $db->insert('engine4_core_pages', array(
      'name' => 'sitegroupmember_index_home',
      'displayname' => 'Group Members Home',
      'title' => 'Group Members Home',
      'description' => 'This is group member home group.',
      'custom' => 1
  ));
  $group_id = $db->lastInsertId('engine4_core_pages');

  // containers
  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'container',
      'name' => 'main',
      'parent_content_id' => null,
      'order' => 2,
      'params' => '',
  ));
  $container_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'container',
      'name' => 'right',
      'parent_content_id' => $container_id,
      'order' => 5,
      'params' => '',
  ));
  $right_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'container',
      'name' => 'left',
      'parent_content_id' => $container_id,
      'order' => 4,
      'params' => '',
  ));
  $left_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'container',
      'name' => 'top',
      'parent_content_id' => null,
      'order' => 1,
      'params' => '',
  ));
  $top_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'container',
      'name' => 'middle',
      'parent_content_id' => $top_id,
      'order' => 6,
      'params' => '',
  ));
  $top_middle_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'container',
      'name' => 'middle',
      'parent_content_id' => $container_id,
      'order' => 6,
      'params' => '',
  ));
  $middle_id = $db->lastInsertId('engine4_core_content');

  // Top Middle
  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'widget',
      'name' => 'sitegroup.browsenevigation-sitegroup',
      'parent_content_id' => $top_middle_id,
      'order' => 3,
      'params' => '',
  ));

  // Left
  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'widget',
      'name' => 'sitegroupmember.home-recent-mostvaluable-sitegroupmember',
      'parent_content_id' => $left_id,
      'order' => 16,
      'params' => '{"title":"Recent Members","select_option":"1","titleCount":"true"}',
  ));

  // Middele
  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'widget',
      'name' => 'sitegroupmember.featured-members-slideshow',
      'parent_content_id' => $left_id,
      'order' => 14,
      'params' => '{"title":"Featured Members","titleCount":"true"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'widget',
      'name' => 'sitegroupmember.list-members-tabs-view',
      'parent_content_id' => $middle_id,
      'order' => 17,
      'params' => '{"title":"Members","margin_photo":"12","showViewMore":"1"}',
  ));

  // Right Side
  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'widget',
      'name' => 'sitegroupmember.home-recent-mostvaluable-sitegroupmember',
      'parent_content_id' => $right_id,
      'order' => 20,
      'params' => '{"title":"Top Group Joiners","select_option":"2","titleCount":"true"}',
  ));

  // Right Side
  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'widget',
      'name' => 'sitegroupmember.search-sitegroupmember',
      'parent_content_id' => $right_id,
      'order' => 18,
      'params' => '',
  ));

  // Right Side
  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'widget',
      'name' => 'sitegroupmember.sitegroupmemberlist-link',
      'parent_content_id' => $right_id,
      'order' => 19,
      'params' => '',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'widget',
      'name' => 'sitegroupmember.member-of-the-day',
      'parent_content_id' => $left_id,
      'order' => 15,
      'params' => '{"title":"Member of the Day"}',
  ));
}

$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_modules')
        ->where('name = ?', 'communityad')
        ->where('enabled 	 = ?', 1)
        ->limit(1);
$infomation = $select->query()->fetch();

$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_settings')
        ->where('name = ?', 'sitegroup.communityads')
        ->where('value 	 = ?', 1)
        ->limit(1);
$rowinfo = $select->query()->fetch();

$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_pages')
        ->where('name = ?', 'sitegroupmember_index_browse')
        ->limit(1);
$info_browse = $select->query()->fetch();

if (empty($info_browse)) {
  $db->insert('engine4_core_pages', array(
      'name' => 'sitegroupmember_index_browse',
      'displayname' => 'Browse Group Members',
      'title' => 'Group Members',
      'description' => 'This is the group members.',
      'custom' => 1,
  ));
  $group_id = $db->lastInsertId('engine4_core_pages');

  //CONTAINERS
  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'container',
      'name' => 'main',
      'parent_content_id' => Null,
      'order' => 2,
      'params' => '',
  ));
  $container_id = $db->lastInsertId('engine4_core_content');

  //INSERT MAIN - MIDDLE CONTAINER
  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'container',
      'name' => 'middle',
      'parent_content_id' => $container_id,
      'order' => 6,
      'params' => '',
  ));
  $middle_id = $db->lastInsertId('engine4_core_content');

  //INSERT MAIN - RIGHT CONTAINER
  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'container',
      'name' => 'right',
      'parent_content_id' => $container_id,
      'order' => 5,
      'params' => '',
  ));
  $right_id = $db->lastInsertId('engine4_core_content');

  //INSERT TOP CONTAINER
  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'container',
      'name' => 'top',
      'parent_content_id' => Null,
      'order' => 1,
      'params' => '',
  ));
  $top_id = $db->lastInsertId('engine4_core_content');

  //INSERT TOP- MIDDLE CONTAINER
  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'container',
      'name' => 'middle',
      'parent_content_id' => $top_id,
      'order' => 6,
      'params' => '',
  ));
  $top_middle_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'widget',
      'name' => 'sitegroup.browsenevigation-sitegroup',
      'parent_content_id' => $top_middle_id,
      'order' => 1,
      'params' => '{"title":"","titleCount":""}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'widget',
      'name' => 'sitegroupmember.search-sitegroupmember',
      'parent_content_id' => $right_id,
      'order' => 3,
      'params' => '{"title":"","titleCount":"true"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'widget',
      'name' => 'sitegroupmember.sitegroup-member',
      'parent_content_id' => $middle_id,
      'order' => 2,
      'params' => '{"title":"","titleCount":""}',
  ));

  if (!empty($infomation) && !empty($rowinfo)) {
    $db->insert('engine4_core_content', array(
        'page_id' => $group_id,
        'type' => 'widget',
        'name' => 'sitegroup.group-ads',
        'parent_content_id' => $right_id,
        'order' => 11,
        'params' => '{"title":"","titleCount":""}',
    ));
  }
}

//Member home group
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_pages')
        ->where('name = ?', 'user_index_home')
        ->limit(1);
$group_id = $select->query()->fetchObject()->page_id;
if (!empty($group_id)) {
  $select = new Zend_Db_Select($db);
  $select
          ->from('engine4_core_content')
          ->where('page_id = ?', $group_id)
          ->where('type = ?', 'container')
          ->where('name = ?', 'main')
          ->limit(1);
  $container_id = $select->query()->fetchObject()->content_id;
  if (!empty($container_id)) {
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_content')
            ->where('parent_content_id = ?', $container_id)
            ->where('type = ?', 'container')
            ->where('name = ?', 'right')
            ->limit(1);
    $right_id = $select->query()->fetchObject()->content_id;
    if (!empty($right_id)) {
      $select = new Zend_Db_Select($db);
      $select
              ->from('engine4_core_content')
              ->where('parent_content_id = ?', $right_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitegroupmember.mostjoined-sitegroup');
      $info = $select->query()->fetch();
      if (empty($info)) {
        $db->insert('engine4_core_content', array(
            'page_id' => $group_id,
            'type' => 'widget',
            'name' => 'sitegroupmember.mostjoined-sitegroup',
            'parent_content_id' => $right_id,
            'order' => 1,
            'params' => '{"title":"Most Joined Groups"}',
        ));
      }
    }
  }
}

// *************** End OnInstall() Code

$sitegroupmember_enabled_group_layout = 1;
$sitegroup_package = Engine_Api::_()->sitegroup()->hasPackageEnable();

if (!empty($sitegroup_package)) {

  $menuitemsTable = Engine_Api::_()->getDbtable('menuItems', 'core');

  //if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
  $menuitemsTable->update(array("params" => '{"route":"sitegroup_packages"}'), array('name = ?' => 'sitegroup_main_create', 'module = ?' => "sitegroup", "menu = ?" => "sitegroup_main"));
  $menuitemsTable->update(array("params" => '{"route":"sitegroup_packages","class":"buttonlink icon_sitegroup_new"}'), array('name = ?' => 'sitegroup_quick_create', 'module = ?' => "sitegroup", "menu = ?" => "sitegroup_quick"));


  //package is enable, set enable level settings

  $level_values["contact_detail"] = array('phone', 'website', 'email');

  $sitegroupMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');

  //START SITEGROUPDOCUMENT PLUGIN WORK
  $sitegroupDocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument');
  if ($sitegroupDocumentEnabled) {
    $level_values["sdcreate"] = 1;
    if (!empty($sitegroupMemberEnabled)) {
      $level_values["auth_sdcreate"] = array("registered", "owner_network", "owner_member_member", "owner_member", "member", 'like_member', "owner");
    } else {
      $level_values["auth_sdcreate"] = array("registered", "owner_network", "owner_member_member", "owner_member", 'like_member', "owner");
    }
  }
  //END SITEGROUPDOCUMENT PLUGIN WORK
  //START SITEGROUPEVENT PLUGIN WORK
  $sitegroupNoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent');
  if ($sitegroupNoteEnabled) {
    $level_values["secreate"] = 1;
    if (!empty($sitegroupMemberEnabled)) {
      $level_values["auth_secreate"] = array("registered", "owner_network", "owner_member_member", "owner_member", "member", 'like_member', "owner");
    } else {
      $level_values["auth_secreate"] = array("registered", "owner_network", "owner_member_member", "owner_member", 'like_member', "owner");
    }
  }

  //END SITEGROUPEVENT PLUGIN WORK
  //START SITEGROUPOFFER PLUGIN WORK
  $sitegroupFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupform');
  if ($sitegroupFormEnabled) {
    $level_values["form"] = 1;
  }
  //END SITEGROUPOFFER PLUGIN WORK
  //START SITEGROUPINVITE PLUGIN WORK
  $sitegroupInviteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupinvite');
  if ($sitegroupInviteEnabled) {
    $level_values["invite"] = 1;
  }

  //END SITEGROUPINVITE PLUGIN WORK
  //START SITEGROUPNOTE PLUGIN WORK
  $sitegroupNoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupnote');
  if ($sitegroupNoteEnabled) {
    $level_values["sncreate"] = 1;
    if (!empty($sitegroupMemberEnabled)) {
      $level_values["auth_sncreate"] = array("registered", "owner_network", "owner_member_member", "owner_member", "member", 'like_member', "owner");
    } else {
      $level_values["auth_sncreate"] = array("registered", "owner_network", "owner_member_member", "owner_member", 'like_member', "owner");
    }
  }
  //END SITEGROUPNOTE PLUGIN WORK
  //START SITEGROUPOFFER PLUGIN WORK
  $sitegroupOfferEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupoffer');
  if ($sitegroupOfferEnabled) {
    $level_values["offer"] = 1;
  }
  //END SITEGROUPOFFER PLUGIN WORK
  //START PHOTO PRIVACY WORK
  $sitegroupAlbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum');
  if ($sitegroupAlbumEnabled) {
    $level_values["spcreate"] = 1;
    if (!empty($sitegroupMemberEnabled)) {
      $level_values["auth_spcreate"] = array("registered", "owner_network", "owner_member_member", "owner_member", "member", 'like_member', "owner");
    } else {
      $level_values["auth_spcreate"] = array("registered", "owner_network", "owner_member_member", "owner_member", 'like_member', "owner");
    }
  }
  //END PHOTO PRIVACY WORK
  //START SITEGROUPPOLL PLUGIN WORK
  $sitegroupPollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouppoll');
  if ($sitegroupPollEnabled) {
    $level_values["splcreate"] = 1;
    if (!empty($sitegroupMemberEnabled)) {
      $level_values["auth_splcreate"] = array("registered", "owner_network", "owner_member_member", "owner_member", "member", 'like_member', "owner");
    } else {
      $level_values["auth_splcreate"] = array("registered", "owner_network", "owner_member_member", "owner_member", 'like_member', "owner");
    }
  }
  //END SITEGROUPPOLL PLUGIN WORK
  //START SITEGROUPVIDEO PLUGIN WORK
  $sitegroupVideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo');
  if ($sitegroupVideoEnabled) {
    $level_values["svcreate"] = 1;
    if (!empty($sitegroupMemberEnabled)) {
      $level_values["auth_svcreate"] = array("registered", "owner_network", "owner_member_member", "owner_member", "member", 'like_member', "owner");
    } else {
      $level_values["auth_svcreate"] = array("registered", "owner_network", "owner_member_member", "owner_member", 'like_member', "owner");
    }
  }
  //END SITEGROUPVIDEO PLUGIN WORK
  //START SITEGROUPINTEGRATION PLUGIN WORK
  $sitegroupintegrationEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupintegration');
  if ($sitegroupintegrationEnabled) {
    $mixSettingsResults = Engine_Api::_()->getDbtable('mixsettings', 'sitegroupintegration'
            )->getIntegrationItems();
    foreach ($mixSettingsResults as $modNameValue) {
      $level_values[$modNameValue["resource_type"]] = 1;
    }
  }
  //END SITEGROUPINTEGRATION PLUGIN WORK

  $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
  foreach (Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll() as $level) {
    if ($level->type != "public") {
      $permissionsTable->setAllowed("sitegroup_group", $level->level_id, $level_values);
    }
  }
  //}
  $db->query("UPDATE `engine4_core_settings` SET `value` = '0' WHERE `engine4_core_settings`.`name` = 'sitegroup.package.enable' LIMIT 1 ;");
}


$permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');

foreach (Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll() as $level) {
  if ($level->type != "public") {
    $db->query("UPDATE `engine4_authorization_permissions` SET `value` = '0' WHERE `engine4_authorization_permissions`.`level_id` ='" . $level->level_id . "' AND `engine4_authorization_permissions`.`type` = 'sitegroup_group' AND `engine4_authorization_permissions`.`name` = 'insight' LIMIT 1 ;");
  }
}


$contentTable = Engine_Api::_()->getDbtable('content', 'core');
$contentTableName = $contentTable->info('name');
$groupTable = Engine_Api::_()->getDbtable('pages', 'core');
$groupTableName = $groupTable->info('name');

//FOR LAYOUT GROUP HOME, BROWSE, PINBOARD.
$selectGroup = $groupTable->select()
        ->from($groupTableName, array('page_id'))
        ->where('name =?', 'sitegroup_index_home')
        ->limit(1);
$fetchGroupId = $selectGroup->query()->fetchAll();
if (!empty($fetchGroupId)) {
  $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = '" . $fetchGroupId[0]['page_id'] . "' AND `engine4_core_content`.`type` = 'widget';");

  $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = '" . $fetchGroupId[0]['page_id'] . "' AND `engine4_core_content`.`type` = 'container' AND `engine4_core_content`.`name` = 'right';");

  $selectGroup = $contentTable->select()
          ->from($contentTableName, array('content_id'))
          ->where('page_id =?', $fetchGroupId[0]['page_id'])
          ->where('type =?', 'container')
          ->where('name =?', 'left')
          ->limit(1);
  $fetchLeftId = $selectGroup->query()->fetchAll();

  $selectGroup = $contentTable->select()
          ->from($contentTableName, array('content_id'))
          ->where('page_id =?', $fetchGroupId[0]['page_id'])
          ->where('type =?', 'container')
          ->where('name =?', 'main')
          ->limit(1);
  $fetchMainMiddleId = $selectGroup->query()->fetchAll();

  $selectGroup = $contentTable->select()
          ->from($contentTableName, array('content_id'))
          ->where('page_id =?', $fetchGroupId[0]['page_id'])
          ->where('type =?', 'container')
          ->where('name =?', 'top')
          ->limit(1);
  $fetchTopId = $selectGroup->query()->fetchAll();

  $selectGroup = $contentTable->select()
          ->from($contentTableName, array('content_id'))
          ->where('parent_content_id =?', $fetchMainMiddleId[0]['content_id'])
          ->limit(1);
  $fetchMiddleId = $selectGroup->query()->fetchAll();

  $selectGroup = $contentTable->select()
          ->from($contentTableName, array('content_id'))
          ->where('parent_content_id =?', $fetchTopId[0]['content_id'])
          ->limit(1);
  $fetchTopMiddleId = $selectGroup->query()->fetchAll();
  if (!empty($fetchTopMiddleId)) {
    //for top middle placed.
    $this->widgetPlaced($fetchGroupId[0]['page_id'], 'sitegroup.browsenevigation-sitegroup', $fetchTopMiddleId[0]['content_id'], 25, '{"title":"","titleCount":"true"}');

    $this->widgetPlaced($fetchGroupId[0]['page_id'], 'sitegroup.search-sitegroup', $fetchTopMiddleId[0]['content_id'], 26, '{"title":"","titleCount":true,"viewType":"horizontal","nomobile":"0","name":"sitegroup.search-sitegroup"}');
  }

  if (!empty($fetchLeftId)) {
    //for left side.
    $this->widgetPlaced($fetchGroupId[0]['page_id'], 'sitegroup.item-sitegroup', $fetchLeftId[0]['content_id'], 10, '{"title":"Group of the day","titleCount":"true"}');

    $this->widgetPlaced($fetchGroupId[0]['page_id'], 'sitegroup.newgroup-sitegroup', $fetchLeftId[0]['content_id'], 11, '{"title":"","titleCount":"true"}');

    $this->widgetPlaced($fetchGroupId[0]['page_id'], 'sitegroup.mostdiscussion-sitegroup', $fetchLeftId[0]['content_id'], 12, '{"title":"Most Discussed Groups","titleCount":"true"}');

    $this->widgetPlaced($fetchGroupId[0]['page_id'], 'sitegroup.mostfollowers-sitegroup', $fetchLeftId[0]['content_id'], 13, '{"title":"Most Followed Groups","titleCount":"true"}');

    $this->widgetPlaced($fetchGroupId[0]['page_id'], 'sitegroupmember.mostjoined-sitegroup', $fetchLeftId[0]['content_id'], 14, '{"title":"Most Joined Groups","titleCount":true}');

    $this->widgetPlaced($fetchGroupId[0]['page_id'], 'sitegroup.mostlikes-sitegroup', $fetchLeftId[0]['content_id'], 15, '{"title":"Most Liked Groups","titleCount":"true"}');

    $this->widgetPlaced($fetchGroupId[0]['page_id'], 'sitegroup.mostcommented-sitegroup', $fetchLeftId[0]['content_id'], 16, '{"title":"Most Commented Groups","titleCount":"true"}');

    $this->widgetPlaced($fetchGroupId[0]['page_id'], 'sitegroup.recentview-sitegroup', $fetchLeftId[0]['content_id'], 17, '{"title":"Recently Viewed","titleCount":"true"}');

    $this->widgetPlaced($fetchGroupId[0]['page_id'], 'sitegroup.recentfriend-sitegroup', $fetchLeftId[0]['content_id'], 18, '{"title":"Recently Viewed By Friends","titleCount":"true"}');

    $this->widgetPlaced($fetchGroupId[0]['page_id'], 'sitegroup.tagcloud-sitegroup', $fetchLeftId[0]['content_id'], 19, '{"title":"","titleCount":"true"}');

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion')) {
      $this->widgetPlaced($fetchGroupId[0]['page_id'], 'suggestion.common-suggestion', $fetchLeftId[0]['content_id'], 20, '{"title":"Recommended Groups","resource_type":null,"getWidAjaxEnabled":"1","getWidLimit":"5","nomobile":"0","name":"suggestion.common-suggestion"}');
    }
  }

  if (!empty($fetchMiddleId)) {
    //for main middle container.
    $this->widgetPlaced($fetchGroupId[0]['page_id'], 'sitegroup.zerogroup-sitegroup', $fetchMiddleId[0]['content_id'], 21, '{"title":"","titleCount":"true"}');

    $this->widgetPlaced($fetchGroupId[0]['page_id'], 'sitegroup.ajax-carousel-sitegroup', $fetchMiddleId[0]['content_id'], 22, '{"title":"Sponsored Groups","titleCount":true,"statistics":["followCount","memberCount"],"fea_spo":"sponsored","viewType":"0","blockHeight":"224","blockWidth":"170","popularity":"view_count","featuredIcon":"1","sponsoredIcon":"1","itemCount":"4","interval":"300","category_id":"0","truncation":"50","nomobile":"0","name":"sitegroup.ajax-carousel-sitegroup"}');

    $this->widgetPlaced($fetchGroupId[0]['page_id'], 'sitegroup.categories', $fetchMiddleId[0]['content_id'], 23, '{"title":"Categories","titleCount":true,"showAllCategories":"0","show2ndlevelCategory":"1","show3rdlevelCategory":"0","nomobile":"0","name":"sitegroup.categories"}');

    $this->widgetPlaced($fetchGroupId[0]['page_id'], 'sitegroup.slideshow-sitegroup', $fetchMiddleId[0]['content_id'], 24, '{"title":"Featured Groups","titleCount":true,"itemCount":"4","category_id":"0","nomobile":"0","name":"sitegroup.slideshow-sitegroup"}');

    $this->widgetPlaced($fetchGroupId[0]['page_id'], 'sitegroup.category-groups-sitegroup', $fetchMiddleId[0]['content_id'], 25, '{"title":"Popular Groups","titleCount":true,"itemCount":"9","groupCount":"3","popularity":"view_count","interval":"overall","columnCount":"3","nomobile":"0","name":"sitegroup.category-groups-sitegroup"}');

    $this->widgetPlaced($fetchGroupId[0]['page_id'], 'sitegroup.recently-popular-random-sitegroup', $fetchMiddleId[0]['content_id'], 26, '{"title":"","titleCount":"","layouts_views":["1","2","3"],"layouts_oder":"2","layouts_tabs":["1","2","3","4","5","6"],"recent_order":"4","popular_order":"5","random_order":"6","featured_order":"1","sponosred_order":"2","list_limit":"10","grid_limit":"16","columnWidth":"198","columnHeight":"200","statistics":["likeCount","followCount","viewCount","memberCount"],"turncation":"40","showlikebutton":"0","showfeaturedLable":"0","showsponsoredLable":"0","showlocation":"0","showprice":"0","showpostedBy":"0","showdate":"0","category_id":"0","joined_order":"3","nomobile":"0","name":"sitegroup.recently-popular-random-sitegroup"}');
  }
}


//FOR BROWSE GROUP WORK.
$db->query("UPDATE `engine4_core_content` SET `order` = '880' WHERE `engine4_core_content`.`name` ='sitegroup.popularlocations-sitegroup' LIMIT 1 ;");

$db->query("UPDATE `engine4_core_content` SET `order` = '999' WHERE `engine4_core_content`.`name` ='sitegroup.tagcloud-sitegroup' LIMIT 1 ;");

$selectGroup = $groupTable->select()
        ->from($groupTableName, array('page_id'))
        ->where('name =?', 'sitegroup_index_index')
        ->limit(1);
$group_id = $selectGroup->query()->fetchAll();

$db->query('UPDATE `engine4_core_content` SET `params` = \'{"title":"","titleCount":true,"layouts_views":["1","2","3"],"layouts_oder":"2","columnWidth":"195","statistics":["likeCount","followCount","viewCount","memberCount"],"columnHeight":"200","turncation":"40","showlikebutton":"0","showfeaturedLable":"0","showsponsoredLable":"0","showlocation":"0","showprice":"0","showpostedBy":"0","showdate":"0","category_id":"0","nomobile":"0","name":"sitegroup.groups-sitegroup"}\' WHERE `engine4_core_content`.`name` ="sitegroup.groups-sitegroup" AND  `engine4_core_content`.`page_id` ="' . $group_id[0]['page_id'] . '" LIMIT 1 ;');

$selectGroup = $contentTable->select()
        ->from($contentTableName, array('content_id'))
        ->where('page_id =?', $group_id[0]['page_id'])
        ->where('type =?', 'container')
        ->where('name =?', 'right')
        ->limit(1);
$fetchRightId = $selectGroup->query()->fetchAll();
if (!empty($fetchRightId)) {
  $this->widgetPlaced($group_id[0]['page_id'], 'sitegroupmember.mostjoined-sitegroup', $fetchRightId[0]['content_id'], 700, '{"title":"Most Joined Groups","titleCount":true,"itemCount":"3","category_id":"0","featured":"0","sponsored":"0","nomobile":"0","name":"sitegroupmember.mostjoined-sitegroup"}');

  $this->widgetPlaced($group_id[0]['page_id'], 'sitegroup.mostfollowers-sitegroup', $fetchRightId[0]['content_id'], 750, '{"title":"Most Followed Groups","titleCount":true,"itemCount":"3","category_id":"0","featured":"0","sponsored":"0","interval":"overall","nomobile":"0","name":"sitegroup.mostfollowers-sitegroup"}');
}


//FOR BROWSE PINBOARD GROUP.

$selectGroup = $groupTable->select()
        ->from($groupTableName, array('page_id'))
        ->where('name =?', 'sitegroup_index_pinboard_browse')
        ->limit(1);
$group_id = $selectGroup->query()->fetchAll();
if (!empty($group_id)) {
  $db->query('UPDATE `engine4_core_content` SET `params` = \'{"title":"","titleCount":true,"postedby":"1","showoptions":["viewCount","likeCount","commentCount","price","location"],"detactLocation":"0","defaultlocationmiles":"1000","itemWidth":"274","withoutStretch":"1","itemCount":"12","show_buttons":["comment","like","share","facebook","twitter"],"truncationDescription":"100","nomobile":"0","name":"sitegroup.pinboard-browse"}\' WHERE `engine4_core_content`.`name` ="sitegroup.pinboard-browse" AND  `engine4_core_content`.`page_id` ="' . $group_id[0]['page_id'] . '" LIMIT 1 ;');
}


//FOR PROFILE GROUP LAYOUT.
$LayoutSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0);
if (empty($LayoutSettings)) {

  $select = new Zend_Db_Select($db);
  $select_group = $select
          ->from('engine4_core_pages', 'page_id')
          ->where('name = ?', 'sitegroup_index_view')
          ->limit(1);
  $group = $select_group->query()->fetchAll();
  if (!empty($group)) {

    $group_id = $group[0]['page_id'];

    $selectGroup = $contentTable->select()
            ->from($contentTableName, array('content_id'))
            ->where('page_id =?', $group_id)
            ->where('type =?', 'container')
            ->where('name =?', 'left')
            ->limit(1);
    $left_Id = $selectGroup->query()->fetchAll();

    $selectPage = $contentTable->select()
            ->from($contentTableName, array('content_id'))
            ->where('page_id =?', $group_id)
            ->where('type =?', 'container')
            ->where('name =?', 'right')
            ->limit(1);
    $right_Id = $selectGroup->query()->fetchAll();
    if (!empty($right_Id[0]['content_id'])) {
      $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = '" . $group_id . "' AND `engine4_core_content`.`type` = 'widget' AND `engine4_core_content`.`parent_content_id` = '" . $right_Id[0]['content_id'] . "';");
    }

    if (!empty($left_Id[0]['content_id'])) {
      $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = '" . $group_id . "' AND `engine4_core_content`.`type` = 'widget' AND `engine4_core_content`.`parent_content_id` = '" . $left_Id[0]['content_id'] . "';");
    }

    $db->query("UPDATE `engine4_core_content` SET `name` = 'right' WHERE `engine4_core_content`.`name` ='left' AND  `engine4_core_content`.`page_id` ='" . $group_id . "' LIMIT 1 ;");

    $selectGroup = $contentTable->select()
            ->from($contentTableName, array('content_id'))
            ->where('page_id =?', $group_id)
            ->where('type =?', 'container')
            ->where('name =?', 'right')
            ->limit(1);
    $right_Id = $selectGroup->query()->fetchAll();

    $right_Id = $right_Id[0]['content_id'];

    if (!empty($right_Id)) {

      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent')) {
        $this->widgetPlaced($group_id, 'sitegroupevent.profile-events', $right_Id, 150, '{"title":"Upcoming Events"}');
      }

      $this->widgetPlaced($group_id, 'sitegroup.photolike-sitegroup', $right_Id, 151, '{"title":"Most Liked Photos","titleCount":""}');

      $this->widgetPlaced($group_id, 'sitegroup.write-group', $right_Id, 152, '{"title":"","titleCount":true}');

      $this->widgetPlaced($group_id, 'sitegroup.information-sitegroup', $right_Id, 153, '{"title":"Information","titleCount":"true","showContent":["ownerPhoto","ownerName","modifiedDate","viewCount","likeCount","commentCount","tags","location","price","memberCount","followerCount","categoryName"]}');

      $edit_layout_setting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layout.setting', 0);
      if (empty($edit_layout_setting)) {
        $this->widgetPlaced($group_id, 'sitegroup.widgetlinks-sitegroup', $right_Id, 154, '{"title":"","titleCount":true}');
      }

      $this->widgetPlaced($group_id, 'sitegroup.options-sitegroup', $right_Id, 205, '{"title":"","titleCount":"true"}');

      $this->widgetPlaced($group_id, 'sitegroup.featuredowner-sitegroup', $right_Id, 206, '{"title":"Owners","titleCount":"true"}');

      $this->widgetPlaced($group_id, 'sitegroup.socialshare-sitegroup', $right_Id, 207, '{"title":"Social Share","titleCount":"true"}');

      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
        $this->widgetPlaced($group_id, 'sitegroupreview.ratings-sitegroupreviews', $right_Id, 8, '{"title":"Ratings","titleCount":"true"}');
      }

      $this->widgetPlaced($group_id, 'sitegroup.favourite-group', $right_Id, 209, '{"title":"Linked Groups","titleCount":true,"itemCount":"3","category_id":"0","featured":"0","sponsored":"0","nomobile":"0","name":"sitegroup.favourite-group"}');

      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument')) {
        $this->widgetPlaced($group_id, 'sitegroupdocument.recent-sitegroupdocuments', $right_Id, 210, '{"title":"Most Recent Documents"}');
      }
      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupnote')) {
        $this->widgetPlaced($group_id, 'sitegroupnote.recent-sitegroupnotes', $right_Id, 211, '{"title":"Most Recent Notes","titleCount":true}');
      }

      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouppoll')) {
        $this->widgetPlaced($group_id, 'sitegrouppoll.vote-sitegrouppolls', $right_Id, 212, '{"title":"Most Voted Polls","titleCount":true}');

        $this->widgetPlaced($group_id, 'sitegrouppoll.view-sitegrouppolls', $right_Id, 213, '{"title":"Most Viewed Polls","titleCount":true,"itemCount":"3","nomobile":"0","name":"sitegrouppoll.view-sitegrouppolls"}');
      }

      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo')) {
        $this->widgetPlaced($group_id, 'sitegroupvideo.view-sitegroupvideos', $right_Id, 214, '{"title":"Most Viewed Videos","titleCount":true}');
      }

      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic')) {
        $this->widgetPlaced($group_id, 'sitegroupmusic.like-sitegroupmusic', $right_Id, 215, '{"title":"Most Liked Playlists","titleCount":true}');
      }

      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument')) {
        $this->widgetPlaced($group_id, 'sitegroupdocument.like-sitegroupdocuments', $right_Id, 216, '{"title":"Most Liked Documents","titleCount":true}');
      }

      $this->widgetPlaced($group_id, 'sitegroup.photocomment-sitegroup', $right_Id, 217, '{"title":"Most Commented Photos","titleCount":"","itemCount":"4","nomobile":"0","name":"sitegroup.photocomment-sitegroup"}');

      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouppoll')) {
        $this->widgetPlaced($group_id, 'sitegrouppoll.comment-sitegrouppolls', $right_Id, 218, '{"title":"Most Commented Polls","titleCount":true,"itemCount":"3","nomobile":"0","name":"sitegrouppoll.comment-sitegrouppolls"}');
      }

      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
        $this->widgetPlaced($group_id, 'sitegroupreview.like-sitegroupreviews', $right_Id, 219, '{"title":"Most Liked Reviews","titleCount":true,"itemCount":"3","nomobile":"0","name":"sitegroupreview.like-sitegroupreviews"}');
      }

      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin')) {
        $this->widgetPlaced($group_id, 'sitetagcheckin.checkinbutton-sitetagcheckin', $right_Id, 220, '{"title":"Check-in here","titleCount":true,"checkin_use":"1","checkin_button_sidebar":"1","checkin_button":"1","checkin_button_link":"Check-in here","checkin_icon":"1","checkin_verb":"Check-in","checkedinto_verb":"checked-into","checkin_your":"You\'ve checked-in here","checkin_total":"Total check-ins here","nomobile":"0","name":"sitetagcheckin.checkinbutton-sitetagcheckin"}');

        $this->widgetPlaced($group_id, 'sitetagcheckin.checkinuser-sitetagcheckin', $right_Id, 221, '{"title":"","titleCount":true,"checkedin_heading":"People Here","checkedin_see_all_heading":"People who have been here","checkedin_users":"0","checkedin_user_photo":"1","checkedin_user_name":"1","checkedin_user_checkedtime":"1","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}');
      }

      $this->widgetPlaced($group_id, 'seaocore.layout-width', $right_Id, 222, '{"title":"","layoutWidth":"225","layoutWidthType":"px","nomobile":"0","name":"seaocore.layout-width"}');
    }
  }
}


if (array_key_exists('language_phrases_groups', $_POST)) {
  Engine_Api::_()->getApi('settings', 'core')->setSetting('language_phrases_groups', $_POST['language_phrases_groups']);
}

if (array_key_exists('language_phrases_group', $_POST)) {
  Engine_Api::_()->getApi('settings', 'core')->setSetting('language_phrases_group', $_POST['language_phrases_group']);
}

$db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
("sitegroupmember_notificationpost", "sitegroupmember", \'{item:$subject} posted in {item:$object}.\', 0, "");');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
("sitegroup_main_member", "sitegroupmember", "Members", \'Sitegroupmember_Plugin_Menus::canViewMembers\', \'{"route":"sitegroupmember_home","action":"home"}\', "sitegroup_main", "", 1, 0, 999);');

$db->query('INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
("SITEGROUPMEMBER_REQUEST_EMAIL", "sitegroupmember", "[host],[email],[recipient_title],[subject],[message],[template_header],[site_title],[template_footer]");');

$db->query('INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
("SITEGROUPMEMBER_APPROVE_EMAIL", "sitegroupmember", "[host],[email],[recipient_title],[subject],[message],[template_header],[site_title],[template_footer]");');

$select = new Zend_Db_Select($db);
$advancedactivity = $select->from('engine4_core_modules', 'name')
        ->where('name = ?', 'advancedactivity')
        ->query()
        ->fetchcolumn();

$is_enabled = $select->query()->fetchObject();
if (!empty($advancedactivity)) {
  $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_grouped`) VALUES ("sitegroup_join", "sitegroupmember", \'{item:$subject} joined the group {item:$object}:\', 1, 3, 1, 1, 1, 1, 1);');
} else {
  $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("sitegroup_join", "sitegroupmember", \'{item:$subject} joined the group {item:$object}:\', 1, 3, 1, 1, 1, 1);');
}

$db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
("sitegroup_addmember", "sitegroupmember", \'{item:$subject} has joined you in group {item:$object}.\', 0, "");');

$db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
("sitegroupmember_approve", "sitegroupmember", \'{item:$subject} has requested to join the group {item:$object}.\', 0, "");');

$db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
("sitegroupmember_accepted", "sitegroupmember", \'Your request to join the group {item:$object} has been approved.\', 0, "");');

$db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
("sitegroupmember_invite", "sitegroupmember", \'{item:$subject} has invited you to the group {item:$object}.\', 1, "sitegroupmember.widget.request-member");');

$db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
("sitegroup_join", "sitegroupmember", \'{item:$subject} joined the group {item:$object}.\', 0, "");');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`,`order`) VALUES
("sitegroupmember_admin_manage_member", "sitegroupmember", "Manage Members", "", \'{"route":"admin_default","module":"sitegroupmember","controller":"manage","action":"index"}\', "sitegroupmember_admin_main", "", 3),
("sitegroupmember_admin_main_managecategory", "sitegroupmember", "Manage Member Roles", "", \'{"route":"admin_default","module":"sitegroupmember","controller":"settings", "action": "manage-category"}\', "sitegroupmember_admin_main", "", 2),
("sitegroupmember_admin_widget_settings", "sitegroupmember", "Member Of the Day", "", \'{"route":"admin_default","module":"sitegroupmember","controller":"widgets","action":"index"}\', "sitegroupmember_admin_main", "", 4),
("sitegroupmember_admin_main_import", "sitegroupmember", "Import Groups", "", \'{"route":"admin_default","module":"sitegroupmember","controller":"importgroup"}\', "sitegroupmember_admin_main", "", 5);');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`) VALUES
("sitegroup_gutter_join", "sitegroupmember", "Join Group", "Sitegroupmember_Plugin_Menus", \'{"route":"sitegroup_profilegroupmember", "class":"buttonlink smoothbox icon_sitegroup_join","action":"join"}\', "sitegroup_gutter", "", 0, 1),
("sitegroup_gutter_leave", "sitegroupmember", "Leave Group", "Sitegroupmember_Plugin_Menus", \'{"route":"sitegroup_profilegroupmember", "class":"buttonlink smoothbox icon_sitegroup_leave","action":"leave"}\', "sitegroup_gutter", "", 0, 2),
("sitegroup_gutter_request", "sitegroupmember", "Join Group", "Sitegroupmember_Plugin_Menus", \'{"route":"sitegroup_profilegroupmember", "class":"buttonlink smoothbox icon_sitegroup_join","action":"request"}\', "sitegroup_gutter", "", 0, 3),
("sitegroup_gutter_cancel", "sitegroupmember", "Cancel Membership Request", "Sitegroupmember_Plugin_Menus", \'{"route":"sitegroup_profilegroupmember", "class":"buttonlink smoothbox  icon_sitegroup_cancel","action":"cancel"}\', "sitegroup_gutter", "", 0, 4),
("sitegroup_gutter_invite", "sitegroupmember", "Add People", "Sitegroupmember_Plugin_Menus", \'{"route":"sitegroup_profilegroupmember", "class":"buttonlink smoothbox icon_sitegroup_ad_member","action":"invite"}\', "sitegroup_gutter", "", 0, 5),
("sitegroup_gutter_invite_groupadmin", "sitegroupmember", "Add People", "Sitegroupmember_Plugin_Menus", \'{"route":"sitegroup_profilegroupmember", "class":"buttonlink smoothbox icon_sitegroup_ad_member","action":"invite-members"}\', "sitegroup_gutter", "", 0, 8),
("sitegroup_gutter_respondinvite", "sitegroupmember", "Respond to Membership Request", "Sitegroupmember_Plugin_Menus", \'{"route":"sitegroup_profilegroupmember", "class":"buttonlink smoothbox icon_sitegroup_accept","action":"respond"}\', "sitegroup_gutter", "", 0, 998),

("sitegroup_gutter_respondmemberinvite", "sitegroupmember", "Respond to Membership Invitation", "Sitegroupmember_Plugin_Menus", \'{"route":"sitegroup_profilegroupmember", "class":"buttonlink smoothbox icon_sitegroup_accept","action":"respondinvite"}\', "sitegroup_gutter", "", 0, 990);');



$select = new Zend_Db_Select($db);
$select->from('engine4_core_modules')
        ->where('name = ?', 'sitegroup')
        ->where('enabled = ?', 1);
$check_sitegroup = $select->query()->fetchObject();
if (!empty($check_sitegroup)) {
  $select = new Zend_Db_Select($db);
  $select_group = $select
          ->from('engine4_core_pages', 'page_id')
          ->where('name = ?', 'sitegroup_index_view')
          ->limit(1);
  $group = $select_group->query()->fetchAll();

  if (!empty($group)) {
    $group_id = $group[0]['page_id'];

    //INSERTING THE MEMBER WIDGET IN SITEGROUP_ADMIN_CONTENT TABLE ALSO.
    Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminDefaultInfo('sitegroupmember.profile-sitegroupmembers', $group_id, 'Members', 'true', '122');
    //INSERTING THE MEMBER WIDGET IN SITEGROUP_ADMIN_CONTENT TABLE ALSO.
    Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminDefaultInfo('sitegroupmember.profile-sitegroupmembers-announcements', $group_id, 'Announcements', 'true', '123');

    //INSERTING THE MEMBER WIDGET IN CORE_CONTENT TABLE ALSO.
    Engine_Api::_()->getApi('layoutcore', 'sitegroup')->setContentDefaultInfo('sitegroupmember.profile-sitegroupmembers', $group_id, 'Members', 'true', '122');
    Engine_Api::_()->getApi('layoutcore', 'sitegroup')->setContentDefaultInfo('sitegroupmember.profile-sitegroupmembers-announcements', $group_id, 'Announcements', 'true', '123');

    //INSERTING THE MEMBER WIDGET IN SITEGROUP_CONTENT TABLE ALSO.
    $select = new Zend_Db_Select($db);
    $contentgroup_ids = $select->from('engine4_sitegroup_contentgroups', 'contentgroup_id')->query()->fetchAll();
    foreach ($contentgroup_ids as $contentgroup_id) {
      if (!empty($contentgroup_id)) {
        Engine_Api::_()->getDbtable('content', 'sitegroup')->setDefaultInfo('sitegroupmember.profile-sitegroupmembers', $contentgroup_id['contentgroup_id'], 'Members', 'true', '122');
        Engine_Api::_()->getDbtable('content', 'sitegroup')->setDefaultInfo('sitegroupmember.profile-sitegroupmembers-announcements', $contentgroup_id['contentgroup_id'], 'Announcements', 'true', '123');
        if (!empty($sitegroupmember_enabled_group_layout)) {
          $select = new Zend_Db_Select($db);
          $select_content = $select
                  ->from('engine4_sitegroup_content')
                  ->where('contentgroup_id = ?', $contentgroup_id['contentgroup_id'])
                  ->where('type = ?', 'widget')
                  ->where('name = ?', 'sitegroupmember.groupcover-photo-sitegroupmembers')
                  ->limit(1);
          $content = $select_content->query()->fetchAll();
          if (empty($content)) {
            $select = new Zend_Db_Select($db);
            $select_container = $select
                    ->from('engine4_sitegroup_content', 'content_id')
                    ->where('contentgroup_id = ?', $contentgroup_id['contentgroup_id'])
                    ->where('type = ?', 'container')
                    ->limit(1);
            $container = $select_container->query()->fetchAll();
            if (!empty($container)) {
              $container_id = $container[0]['content_id'];
              $select = new Zend_Db_Select($db);
              $select_left = $select
                      ->from('engine4_sitegroup_content')
                      ->where('parent_content_id = ?', $container_id)
                      ->where('type = ?', 'container')
                      ->where('name = ?', 'middle')
                      ->limit(1);
              $left = $select_left->query()->fetchAll();
              if (!empty($left)) {
                $left_id = $left[0]['content_id'];
                $db->insert('engine4_sitegroup_content', array(
                    'contentgroup_id' => $contentgroup_id['contentgroup_id'],
                    'type' => 'widget',
                    'name' => 'sitegroupmember.groupcover-photo-sitegroupmembers',
                    'parent_content_id' => $left_id,
                    'order' => 1,
                    'params' => '{"title":""}',
                ));
              }
            }
          }
        }
      }
    }

    if (!empty($sitegroupmember_enabled_group_layout)) {
      Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->delete(array('name =?' => 'sitegroup.photorecent-sitegroup'));
      Engine_Api::_()->getDbtable('content', 'sitegroup')->delete(array('name =?' => 'sitegroup.photorecent-sitegroup'));
      Engine_Api::_()->getDbtable('content', 'core')->delete(array('name =?' => 'sitegroup.photorecent-sitegroup'));
      Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->delete(array('name =?' => 'sitegroup.group-cover-information-sitegroup'));
      Engine_Api::_()->getDbtable('content', 'sitegroup')->delete(array('name =?' => 'sitegroup.group-cover-information-sitegroup'));
      Engine_Api::_()->getDbtable('content', 'core')->delete(array('name =?' => 'sitegroup.group-cover-information-sitegroup'));
      $select = new Zend_Db_Select($db);
      $select_content = $select
              ->from('engine4_sitegroup_admincontent')
              ->where('group_id = ?', $group_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitegroupmember.groupcover-photo-sitegroupmembers')
              ->limit(1);
      $content = $select_content->query()->fetchAll();
      if (empty($content)) {
        $select = new Zend_Db_Select($db);
        $select_container = $select
                ->from('engine4_sitegroup_admincontent', 'admincontent_id')
                ->where('group_id = ?', $group_id)
                ->where('type = ?', 'container')
                ->limit(1);
        $container = $select_container->query()->fetchAll();
        if (!empty($container)) {
          $container_id = $container[0]['admincontent_id'];
          $select = new Zend_Db_Select($db);
          $select_left = $select
                  ->from('engine4_sitegroup_admincontent')
                  ->where('parent_content_id = ?', $container_id)
                  ->where('type = ?', 'container')
                  ->where('name = ?', 'middle')
                  ->limit(1);
          $left = $select_left->query()->fetchAll();
          if (!empty($left)) {
            $left_id = $left[0]['admincontent_id'];
            $db->insert('engine4_sitegroup_admincontent', array(
                'group_id' => $group_id,
                'type' => 'widget',
                'name' => 'sitegroupmember.groupcover-photo-sitegroupmembers',
                'parent_content_id' => $left_id,
                'order' => 1,
                'params' => '{"title":""}',
            ));
          }
        }
      }

      $select = new Zend_Db_Select($db);
      $select_content = $select
              ->from('engine4_core_content')
              ->where('page_id = ?', $group_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitegroupmember.groupcover-photo-sitegroupmembers')
              ->limit(1);
      $content = $select_content->query()->fetchAll();
      if (empty($content)) {
        $select = new Zend_Db_Select($db);
        $select_container = $select
                ->from('engine4_core_content', 'content_id')
                ->where('page_id = ?', $group_id)
                ->where('type = ?', 'container')
                ->limit(1);
        $container = $select_container->query()->fetchAll();
        if (!empty($container)) {
          $container_id = $container[0]['content_id'];
          $select = new Zend_Db_Select($db);
          $select_left = $select
                  ->from('engine4_core_content')
                  ->where('parent_content_id = ?', $container_id)
                  ->where('type = ?', 'container')
                  ->where('name = ?', 'middle')
                  ->limit(1);
          $left = $select_left->query()->fetchAll();
          if (!empty($left)) {
            $left_id = $left[0]['content_id'];
            $db->insert('engine4_core_content', array(
                'page_id' => $group_id,
                'type' => 'widget',
                'name' => 'sitegroupmember.groupcover-photo-sitegroupmembers',
                'parent_content_id' => $left_id,
                'order' => 1,
                'params' => '{"title":""}',
            ));
          }
        }
      }
    }
  }
}

$select = new Zend_Db_Select($db);
$select
				->from('engine4_core_modules')
				->where('name = ?', 'sitemobile')
				->where('enabled = ?', 1);
$is_sitemobile_object = $select->query()->fetchObject();
if(!empty($is_sitemobile_object)) {
  Engine_Api::_()->getApi('modules', 'sitemobile')->addModuleStart('sitegroupmember');
}


	  $select = new Zend_Db_Select($db);
		$select
					->from('engine4_core_modules')
					->where('name = ?', 'sitemobile')
					->where('enabled = ?', 1);
		$is_sitemobile_object = $select->query()->fetchObject();
		if($is_sitemobile_object)  {
				include APPLICATION_PATH . "/application/modules/Sitegroupmember/controllers/mobileLayoutCreation.php";
		}