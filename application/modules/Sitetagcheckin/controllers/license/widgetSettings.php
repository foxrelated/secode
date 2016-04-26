<?php

$db = Zend_Db_Table_Abstract::getDefaultAdapter();

$db->query(
        '
	INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`,
	`submenu`, `enabled`, `custom`, `order`) VALUES 

	( "sitetagcheckin_admin_main_geotag", "sitetagcheckin", "Location Entities", "", \'{"route":"admin_default","module":"sitetagcheckin","controller":"geotag","action":"index"}\', "sitetagcheckin_admin_main", "", "1", "0", "2"),
	("sitetagcheckin_admin_main_checkin", "sitetagcheckin", "Check-Ins", "", \'{"route":"admin_default","module":"sitetagcheckin","controller":"checkin","action":"index"}\', "sitetagcheckin_admin_main", "", "1", "0", "3"),
	("sitetagcheckin_admin_manage_modules", "sitetagcheckin", "Manage Modules", "", \'{"route":"admin_default","module":"sitetagcheckin","controller":"manage","action":"index"}\', "sitetagcheckin_admin_main", "", "1", "0", "4"),
	("sitetagcheckin_admin_main_locations","sitetagcheckin", "Event Locations", "",\'{"route":"admin_default","module":"sitetagcheckin","controller":"settings","action":"locations"}\', "sitetagcheckin_admin_main", "", "1", "0", "5"),
	
	("sitetagcheckin_admin_main_userlocations","sitetagcheckin", "Member Locations", "",\'{"route":"admin_default","module":"sitetagcheckin","controller":"settings","action":"userlocations"}\', "sitetagcheckin_admin_main", "", "1", "0", "6");
	'
);

$db->query(
        '
	INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
	("sitetagcheckin_add_to_map", "sitetagcheckin", \'{item:$subject} {var:$checked_into_verb} {item:$object} {var:$event_date}.<br/>{body:$body}\', 1, 7, 1, 1, 1, 1),

	("sitetagcheckin_album_photo_new", "sitetagcheckin", \'{item:$subject} added {var:$count} photo(s) to the album {item:$object} - {var:$prefixadd} {var:$location}.\', 1, 5, 1, 3, 1, 1),

	("sitetagcheckin_checkin", "sitetagcheckin", \'{item:$subject} is \', 1, 5, 0, 1, 4, 1),

	("sitetagcheckin_content", "sitetagcheckin", \'{item:$subject} {var:$checked_into_verb} {item:$object}.<br/>{body:$body}\', 1, 7, 1, 1, 1, 1),

	("sitetagcheckin_location", "sitetagcheckin", \'{item:$subject} updated the location of a photo to {var:$location}.\', 1, 7, 1, 3, 1, 1),

	("sitetagcheckin_post", "sitetagcheckin", \'{actors:$subject:$object}:\r\n{body:$body}\', 1, 7, 1, 1, 1, 1),

	("sitetagcheckin_post_self", "sitetagcheckin", \'{item:$subject}\r\n{body:$body}\', 1, 5, 1, 1, 1, 1),

	("sitetagcheckin_status", "sitetagcheckin", \'{item:$subject}\r\n{body:$body}\', 1, 5, 0, 1, 4, 1),

	("sitetagcheckin_tagged_new", "sitetagcheckin", \'{item:$subject} tagged {item:$object} in a {var:$label} - {var:$prefixadd} {var:$location}.\', 1, 7, 1, 1, 1, 1),

	("sitetagcheckin_profile_photo", "sitetagcheckin", \'{item:$subject} added a new profile photo - {var:$prefixadd} {var:$location}.\', 1, 5, 1, 1, 1, 1);
	'
);

$db->query(
        '
	INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
	("sitetagcheckin_tagged", "sitetagcheckin", \'{item:$subject} mentioned you in a {item:$object:$label}.\', 0, "", 1),
	("sitetagcheckin_tagged_location", "sitetagcheckin", \'{item:$subject} added location to a {item:$object:$label} in which you are tagged.\', 0, "", 1)
	'
);


//CHECK THAT SITEEVENT PLUGIN IS INSTALLED OR NOT
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_modules')
        ->where('name = ?', 'siteevent')
        ->where('enabled = ?', 1);
$check_siteevent = $select->query()->fetchObject();
if (!empty($check_siteevent)) {
  $select = new Zend_Db_Select($db);
  $check_sitetagcheckin = $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'sitetagcheckin')->query()->fetchObject();

  if (!empty($check_sitetagcheckin)) {

    $select = new Zend_Db_Select($db);
    $select_page = $select
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'siteevent_index_view')
            ->limit(1);
    $page = $select_page->query()->fetchAll();
    if (!empty($page)) {
      $page_id = $page[0]['page_id'];

      $select = new Zend_Db_Select($db);
      $select_content = $select
              ->from('engine4_core_content')
              ->where('page_id = ?', $page_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitetagcheckin.checkinbutton-sitetagcheckin')
              ->limit(1);
      $content = $select_content->query()->fetchAll();

      if (empty($content)) {
        $select = new Zend_Db_Select($db);
        $select_container = $select
                ->from('engine4_core_content', 'content_id')
                ->where('page_id = ?', $page_id)
                ->where('type = ?', 'container')
                ->where('name = ?', 'main')
                ->limit(1);
        $container = $select_container->query()->fetchAll();

        if (!empty($container)) {
          $container_id = $container[0]['content_id'];
          $select = new Zend_Db_Select($db);
          $select_middle = $select
                  ->from('engine4_core_content')
                  ->where('parent_content_id = ?', $container_id)
                  ->where('page_id = ?', $page_id)
                  ->where('type = ?', 'container')
                  ->where('name in (?)', array('left', 'right'))
                  ->limit(1);
          $middle = $select_middle->query()->fetchAll();
          if (!empty($middle)) {
            $middle_id = $middle[0]['content_id'];
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitetagcheckin.checkinbutton-sitetagcheckin',
                'parent_content_id' => $middle_id,
                'order' => 14,
                'params' => '{"title":"","titleCount":false,"checkin_use":"1","checkin_button_sidebar":"1","checkin_button":"1","checkin_button_link":"Check-in here","checkin_icon":"1","checkin_verb":"Check-in","checkedinto_verb":"checked-into","checkin_your":"You\'ve checked-in here","checkin_total":"Total check-ins here","nomobile":"0","name":"sitetagcheckin.checkinbutton-sitetagcheckin"}',
            ));
          }
        }
      }
      //PUT X PEOPLE HERE WIDGET IN ADMIN CONTENT TABLE FOR PAGE
      $select = new Zend_Db_Select($db);
      $select_content = $select
              ->from('engine4_core_content')
              ->where('page_id = ?', $page_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitetagcheckin.checkinuser-sitetagcheckin')
              ->limit(1);
      $content = $select_content->query()->fetchAll();
      if (empty($content)) {
        $select = new Zend_Db_Select($db);
        $select_container = $select
                ->from('engine4_core_content', 'content_id')
                ->where('page_id = ?', $page_id)
                ->where('type = ?', 'container')->where('name = ?', 'main')
                ->limit(1);
        $container = $select_container->query()->fetchAll();
        if (!empty($container)) {
          $container_id = $container[0]['content_id'];
          $select = new Zend_Db_Select($db);
          $select_left = $select
                  ->from('engine4_core_content')
                  ->where('parent_content_id = ?', $container_id)
                  ->where('page_id = ?', $page_id)
                  ->where('type = ?', 'container')
                  ->where('name in (?)', array('left', 'right'))
                  ->limit(1);
          $left = $select_left->query()->fetchAll();
          if (!empty($left)) {
            $left_id = $left[0]['content_id'];
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
                'parent_content_id' => $left_id,
                'order' => 14,
                'params' => '{"title":"","titleCount":true,"checkedin_heading":"People Here","checkedin_see_all_heading":"People Who\'ve Been Here","checkedin_users":"1","checkedin_user_photo":"1","checkedin_user_name":"1","checkedin_user_checkedtime":"1","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}'
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
                'parent_content_id' => $left_id,
                'order' => 14,
                'params' => '{"title":"","titleCount":true,"checkedin_heading":"People Been Here","checkedin_see_all_heading":"People Who\'ve Been Here","checkedin_users":"0","checkedin_user_photo":"1","checkedin_user_name":"1","checkedin_user_checkedtime":"1","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}'
            ));
          }
        }
      }
    }
  }
}

//CHECK THAT SITEPAGE PLUGIN IS ACTIVATED OR NOT
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_settings')
        ->where('name = ?', 'sitepage.is.active')
        ->limit(1);
$sitepage_settings = $select->query()->fetchAll();
if (!empty($sitepage_settings)) {
  $sitepage_is_active = $sitepage_settings[0]['value'];
} else {
  $sitepage_is_active = 0;
}

//CHECK THAT SITEPAGE PLUGIN IS INSTALLED OR NOT
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_modules')
        ->where('name = ?', 'sitepage')
        ->where('enabled = ?', 1);
$check_sitepage = $select->query()->fetchObject();
if (!empty($check_sitepage) && !empty($sitepage_is_active)) {
  $select = new Zend_Db_Select($db);
  $check_sitetagcheckin = $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'sitetagcheckin')->query()->fetchObject();

  if (!empty($check_sitetagcheckin)) {
    //PUT CHECKINBUTTON WIDGET IN ADMIN CONTENT TABLE FOR PAGE

    $select = new Zend_Db_Select($db);
    $select_page = $select
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'sitepage_index_view')
            ->limit(1);
    $page = $select_page->query()->fetchAll();
    if (!empty($page)) {
      $page_id = $page[0]['page_id'];
      $select = new Zend_Db_Select($db);
      $select_content = $select
              ->from('engine4_sitepage_admincontent')
              ->where('page_id = ?', $page_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitetagcheckin.checkinbutton-sitetagcheckin')
              ->limit(1);
      $content = $select_content->query()->fetchAll();
      if (empty($content)) {
        $select = new Zend_Db_Select($db);
        $select_container = $select
                ->from('engine4_sitepage_admincontent', 'admincontent_id')
                ->where('page_id = ?', $page_id)
                ->where('type = ?', 'container')
                ->limit(1);
        $container = $select_container->query()->fetchAll();
        if (!empty($container)) {
          $container_id = $container[0]['admincontent_id'];
          $select = new Zend_Db_Select($db);
          $select_middle = $select
                  ->from('engine4_sitepage_admincontent')
                  ->where('parent_content_id = ?', $container_id)
                  ->where('page_id = ?', $page_id)
                  ->where('type = ?', 'container')
                  ->where('name in (?)', array('left', 'right'))
                  ->limit(1);
          $middle = $select_middle->query()->fetchAll();
          if (!empty($middle)) {
            $middle_id = $middle[0]['admincontent_id'];
            $db->insert('engine4_sitepage_admincontent', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitetagcheckin.checkinbutton-sitetagcheckin',
                'parent_content_id' => $middle_id,
                'order' => 14,
                'params' => '{"title":"","titleCount":false,"checkin_use":"1","checkin_button_sidebar":"1","checkin_button":"1","checkin_button_link":"Check-in here","checkin_icon":"1","checkin_verb":"Check-in","checkedinto_verb":"checked-into","checkin_your":"You\'ve checked-in here","checkin_total":"Total check-ins here","nomobile":"0","name":"sitetagcheckin.checkinbutton-sitetagcheckin"}',
            ));
          }
        }
      }

      //PUT X PEOPLE HERE WIDGET IN ADMIN CONTENT TABLE FOR PAGE
      $select = new Zend_Db_Select($db);
      $select_content = $select
              ->from('engine4_sitepage_admincontent')
              ->where('page_id = ?', $page_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitetagcheckin.checkinuser-sitetagcheckin')
              ->limit(1);
      $content = $select_content->query()->fetchAll();
      if (empty($content)) {
        $select = new Zend_Db_Select($db);
        $select_container = $select
                ->from('engine4_sitepage_admincontent', 'admincontent_id')
                ->where('page_id = ?', $page_id)
                ->where('type = ?', 'container')
                ->limit(1);
        $container = $select_container->query()->fetchAll();
        if (!empty($container)) {
          $container_id = $container[0]['admincontent_id'];
          $select = new Zend_Db_Select($db);
          $select_left = $select
                  ->from('engine4_sitepage_admincontent')
                  ->where('parent_content_id = ?', $container_id)
                  ->where('page_id = ?', $page_id)
                  ->where('type = ?', 'container')
                  ->where('name in (?)', array('left', 'right'))
                  ->limit(1);
          $left = $select_left->query()->fetchAll();
          if (!empty($left)) {
            $left_id = $left[0]['admincontent_id'];
            $db->insert('engine4_sitepage_admincontent', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
                'parent_content_id' => $left_id,
                'order' => 14,
                'params' => '{"title":"","titleCount":true,"checkedin_heading":"People Here","checkedin_see_all_heading":"People Who\'ve Been Here","checkedin_users":"1","checkedin_user_photo":"1","checkedin_user_name":"1","checkedin_user_checkedtime":"1","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}'
            ));

            $db->insert('engine4_sitepage_admincontent', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
                'parent_content_id' => $left_id,
                'order' => 14,
                'params' => '{"title":"","titleCount":true,"checkedin_heading":"People Been Here","checkedin_see_all_heading":"People Who\'ve Been Here","checkedin_users":"0","checkedin_user_photo":"1","checkedin_user_name":"1","checkedin_user_checkedtime":"1","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}'
            ));
          }
        }
      }

      //PUT CHECKINBUTTON WIDGET IN CORE CONTENT TABLE FOR PAGE
      $select = new Zend_Db_Select($db);
      $select_content = $select
              ->from('engine4_core_content')
              ->where('page_id = ?', $page_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitetagcheckin.checkinbutton-sitetagcheckin')
              ->limit(1);
      $content = $select_content->query()->fetchAll();
      if (empty($content)) {
        $select = new Zend_Db_Select($db);
        $select_container = $select
                ->from('engine4_core_content', 'content_id')
                ->where('page_id = ?', $page_id)
                ->where('type = ?', 'container')
                ->limit(1);
        $container = $select_container->query()->fetchAll();
        if (!empty($container)) {
          $container_id = $container[0]['content_id'];
          $select = new Zend_Db_Select($db);
          $select_middle = $select
                  ->from('engine4_core_content')
                  ->where('parent_content_id = ?', $container_id)
                  ->where('page_id = ?', $page_id)
                  ->where('type = ?', 'container')
                  ->where('name in (?)', array('left', 'right'))
                  ->limit(1);
          $middle = $select_middle->query()->fetchAll();
          if (!empty($middle)) {
            $middle_id = $middle[0]['content_id'];
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitetagcheckin.checkinbutton-sitetagcheckin',
                'parent_content_id' => $middle_id,
                'order' => 14,
                'params' => '{"title":"","titleCount":false,"checkin_use":"1","checkin_button_sidebar":"1","checkin_button":"1","checkin_button_link":"Check-in here","checkin_icon":"1","checkin_verb":"Check-in","checkedinto_verb":"checked-into","checkin_your":"You\'ve checked-in here","checkin_total":"Total check-ins here","nomobile":"0","name":"sitetagcheckin.checkinbutton-sitetagcheckin"}',
            ));
          }
        }
      }

      //PUT CHECKINBUTTON WIDGET IN USER CONTENT TABLE FOR PAGE
      $select = new Zend_Db_Select($db);
      $select = $select
              ->from('engine4_sitepage_contentpages', 'contentpage_id');

      $contentpage_ids = $select->query()->fetchAll();
      foreach ($contentpage_ids as $contentpage_id) {
        if (!empty($contentpage_id)) {
          $contentpage_id = $contentpage_id['contentpage_id'];
          $select = new Zend_Db_Select($db);
          $select_content = $select
                  ->from('engine4_sitepage_content')
                  ->where('contentpage_id = ?', $contentpage_id)
                  ->where('type = ?', 'widget')
                  ->where('name = ?', 'sitetagcheckin.checkinbutton-sitetagcheckin')
                  ->limit(1);
          $content = $select_content->query()->fetchAll();
          if (empty($content)) {
            $select = new Zend_Db_Select($db);
            $select_container = $select
                    ->from('engine4_sitepage_content', 'content_id')
                    ->where('contentpage_id = ?', $contentpage_id)
                    ->where('type = ?', 'container')
                    ->limit(1);
            $container = $select_container->query()->fetchAll();
            if (!empty($container)) {
              $container_id = $container[0]['content_id'];
              $select = new Zend_Db_Select($db);
              $select_middle = $select
                      ->from('engine4_sitepage_content')
                      ->where('parent_content_id = ?', $container_id)
                      ->where('contentpage_id = ?', $contentpage_id)
                      ->where('type = ?', 'container')
                      ->where('name in (?)', array('left', 'right'))
                      ->limit(1);
              $middle = $select_middle->query()->fetchAll();
              if (!empty($middle)) {
                $middle_id = $middle[0]['content_id'];
                $db->insert('engine4_sitepage_content', array(
                    'contentpage_id' => $contentpage_id,
                    'type' => 'widget',
                    'name' => 'sitetagcheckin.checkinbutton-sitetagcheckin',
                    'parent_content_id' => $middle_id,
                    'order' => 14,
                    'params' => '{"title":"","titleCount":false,"checkin_use":"1","checkin_button_sidebar":"1","checkin_button":"1","checkin_button_link":"Check-in here","checkin_icon":"1","checkin_verb":"Check-in","checkedinto_verb":"checked-into","checkin_your":"You\'ve checked-in here","checkin_total":"Total check-ins here","nomobile":"0","name":"sitetagcheckin.checkinbutton-sitetagcheckin"}',
                ));
              }
            }
          }
        }
      }

      //PUT CHECKINUSER WIDGET IN CORE CONTENT TABLE FOR PAGE
      $select = new Zend_Db_Select($db);
      $select_content = $select
              ->from('engine4_core_content')
              ->where('page_id = ?', $page_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitetagcheckin.checkinuser-sitetagcheckin')
              ->limit(1);
      $content = $select_content->query()->fetchAll();
      if (empty($content)) {
        $select = new Zend_Db_Select($db);
        $select_container = $select
                ->from('engine4_core_content', 'content_id')
                ->where('page_id = ?', $page_id)
                ->where('type = ?', 'container')
                ->limit(1);
        $container = $select_container->query()->fetchAll();
        if (!empty($container)) {
          $container_id = $container[0]['content_id'];
          $select = new Zend_Db_Select($db);
          $select_left = $select
                  ->from('engine4_core_content')
                  ->where('parent_content_id = ?', $container_id)
                  ->where('page_id = ?', $page_id)
                  ->where('type = ?', 'container')
                  ->where('name in (?)', array('left', 'right'))
                  ->limit(1);
          $left = $select_left->query()->fetchAll();
          if (!empty($left)) {
            $left_id = $left[0]['content_id'];
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
                'parent_content_id' => $left_id,
                'order' => 14,
                'params' => '{"title":"","titleCount":true,"checkedin_heading":"People Here","checkedin_see_all_heading":"People Who\'ve Been Here","checkedin_users":"1","checkedin_user_photo":"1","checkedin_user_name":"1","checkedin_user_checkedtime":"1","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}'
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
                'parent_content_id' => $left_id,
                'order' => 14,
                'params' => '{"title":"","titleCount":true,"checkedin_heading":"People Have Been Here","checkedin_see_all_heading":"People Who\'ve been here","checkedin_users":"0","checkedin_user_photo":"1","checkedin_user_name":"1","checkedin_user_checkedtime":"1","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}'
            ));
          }
        }
      }

      //PUT CHECKINUSER WIDGET IN USER CONTENT TABLE FOR PAGE
      $select = new Zend_Db_Select($db);
      $select = $select
              ->from('engine4_sitepage_contentpages', 'contentpage_id');

      $contentpage_ids = $select->query()->fetchAll();
      foreach ($contentpage_ids as $contentpage_id) {
        if (!empty($contentpage_id)) {
          $contentpage_id = $contentpage_id['contentpage_id'];
          $select = new Zend_Db_Select($db);
          $select_content = $select
                  ->from('engine4_sitepage_content')
                  ->where('contentpage_id = ?', $contentpage_id)
                  ->where('type = ?', 'widget')
                  ->where('name = ?', 'sitetagcheckin.checkinuser-sitetagcheckin')
                  ->limit(1);
          $content = $select_content->query()->fetchAll();
          if (empty($content)) {
            $select = new Zend_Db_Select($db);
            $select_container = $select
                    ->from('engine4_sitepage_content', 'content_id')
                    ->where('contentpage_id = ?', $contentpage_id)
                    ->where('type = ?', 'container')
                    ->limit(1);
            $container = $select_container->query()->fetchAll();
            if (!empty($container)) {
              $container_id = $container[0]['content_id'];
              $select = new Zend_Db_Select($db);
              $select_left = $select
                      ->from('engine4_sitepage_content')
                      ->where('parent_content_id = ?', $container_id)
                      ->where('contentpage_id = ?', $contentpage_id)
                      ->where('type = ?', 'container')
                      ->where('name in (?)', array('left', 'right'))
                      ->limit(1);
              $left = $select_left->query()->fetchAll();
              if (!empty($left)) {
                $left_id = $left[0]['content_id'];
                $db->insert('engine4_sitepage_content', array(
                    'contentpage_id' => $contentpage_id,
                    'type' => 'widget',
                    'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
                    'parent_content_id' => $left_id,
                    'order' => 14,
                    'params' => '{"title":"","titleCount":true,"checkedin_heading":"People Here","checkedin_see_all_heading":"People who have been here","checkedin_users":"1","checkedin_user_photo":"1","checkedin_user_name":"1","checkedin_user_checkedtime":"1","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}'
                ));
              }

              $db->insert('engine4_sitepage_content', array(
                  'contentpage_id' => $contentpage_id,
                  'type' => 'widget',
                  'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
                  'parent_content_id' => $left_id,
                  'order' => 14,
                  'params' => '{"title":"","titleCount":true,"checkedin_heading":"People Have Been Here","checkedin_see_all_heading":"People Who\'ve Been Here","checkedin_users":"0","checkedin_user_photo":"1","checkedin_user_name":"1","checkedin_user_checkedtime":"1","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}'
              ));
            }
          }
        }
      }

      //PUT CHECKINUSER WIDGET IN USER CONTENT TABLE FOR PAGE
      $select = new Zend_Db_Select($db);
      $select = $select
              ->from('engine4_sitepage_contentpages', 'contentpage_id');

      $contentpage_ids = $select->query()->fetchAll();
      foreach ($contentpage_ids as $contentpage_id) {
        if (!empty($contentpage_id)) {
          $contentpage_id = $contentpage_id['contentpage_id'];
          $select = new Zend_Db_Select($db);
          $select_content = $select
                  ->from('engine4_sitepage_content')
                  ->where('contentpage_id = ?', $contentpage_id)
                  ->where('type = ?', 'widget')
                  ->where('name = ?', 'sitetagcheckin.checkinuser-sitetagcheckin')
                  ->limit(1);
          $content = $select_content->query()->fetchAll();
          if (empty($content)) {
            $select = new Zend_Db_Select($db);
            $select_container = $select
                    ->from('engine4_sitepage_content', 'content_id')
                    ->where('contentpage_id = ?', $contentpage_id)
                    ->where('type = ?', 'container')
                    ->limit(1);
            $container = $select_container->query()->fetchAll();
            if (!empty($container)) {
              $container_id = $container[0]['content_id'];
              $select = new Zend_Db_Select($db);
              $select_left = $select
                      ->from('engine4_sitepage_content')
                      ->where('parent_content_id = ?', $container_id)
                      ->where('contentpage_id = ?', $contentpage_id)
                      ->where('type = ?', 'container')
                      ->where('name in (?)', array('left', 'right'))
                      ->limit(1);
              $left = $select_left->query()->fetchAll();
              if (!empty($left)) {
                $left_id = $left[0]['content_id'];
                $db->insert('engine4_sitepage_content', array(
                    'contentpage_id' => $contentpage_id,
                    'type' => 'widget',
                    'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
                    'parent_content_id' => $left_id,
                    'order' => 14,
                    'params' => '{"title":"","titleCount":true,"checkedin_heading":"People Here","checkedin_see_all_heading":"People Who\'ve Been Here","checkedin_users":"1","checkedin_user_photo":"1","checkedin_user_name":"1","checkedin_user_checkedtime":"1","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}'
                ));

                $db->insert('engine4_sitepage_content', array(
                    'contentpage_id' => $contentpage_id,
                    'type' => 'widget',
                    'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
                    'parent_content_id' => $left_id,
                    'order' => 14,
                    'params' => '{"title":"","titleCount":true,"checkedin_heading":"People Have Been Here","checkedin_see_all_heading":"People Who\'ve Been Here","checkedin_users":"0","checkedin_user_photo":"1","checkedin_user_name":"1","checkedin_user_checkedtime":"1","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}'
                ));
              }
            }
          }
        }
      }
    }
  }
}

//CHECK THAT SITEBUSINESS PLUGIN IS ACTIVATED OR NOT
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_settings')
        ->where('name = ?', 'sitebusiness.is.active')
        ->limit(1);
$sitebusiness_settings = $select->query()->fetchAll();
if (!empty($sitebusiness_settings)) {
  $sitebusiness_is_active = $sitebusiness_settings[0]['value'];
} else {
  $sitebusiness_is_active = 0;
}

//CHECK THAT SITEBUSINESS PLUGIN IS INSTALLED OR NOT
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_modules')
        ->where('name = ?', 'sitebusiness')
        ->where('enabled = ?', 1);
$check_sitebusiness = $select->query()->fetchObject();
if (!empty($check_sitebusiness) && !empty($sitebusiness_is_active)) {
  $select = new Zend_Db_Select($db);
  $check_sitetagcheckin = $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'sitetagcheckin')->query()->fetchObject();

  if (!empty($check_sitetagcheckin)) {
    //PUT CHECKINBUTTON WIDGET IN ADMIN CONTENT TABLE FOR BUSINESS
    $select = new Zend_Db_Select($db);
    $select_business = $select
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'sitebusiness_index_view')
            ->limit(1);
    $business = $select_business->query()->fetchAll();
    if (!empty($business)) {
      $page_id = $business_id = $business[0]['page_id'];
      $select = new Zend_Db_Select($db);
      $select_content = $select
              ->from('engine4_sitebusiness_admincontent')
              ->where('business_id = ?', $business_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitetagcheckin.checkinbutton-sitetagcheckin')
              ->limit(1);
      $content = $select_content->query()->fetchAll();
      if (empty($content)) {
        $select = new Zend_Db_Select($db);
        $select_container = $select
                ->from('engine4_sitebusiness_admincontent', 'admincontent_id')
                ->where('business_id = ?', $business_id)
                ->where('type = ?', 'container')
                ->limit(1);
        $container = $select_container->query()->fetchAll();
        if (!empty($container)) {
          $container_id = $container[0]['admincontent_id'];
          $select = new Zend_Db_Select($db);
          $select_middle = $select
                  ->from('engine4_sitebusiness_admincontent')
                  ->where('business_id = ?', $business_id)
                  ->where('parent_content_id = ?', $container_id)
                  ->where('type = ?', 'container')
                  ->where('name in (?)', array('left', 'right'))
                  ->limit(1);
          $middle = $select_middle->query()->fetchAll();
          if (!empty($middle)) {
            $middle_id = $middle[0]['admincontent_id'];
            $db->insert('engine4_sitebusiness_admincontent', array(
                'business_id' => $business_id,
                'type' => 'widget',
                'name' => 'sitetagcheckin.checkinbutton-sitetagcheckin',
                'parent_content_id' => $middle_id,
                'order' => 14,
                'params' => '{"title":"","titleCount":false,"checkin_use":"1","checkin_button_sidebar":"1","checkin_button":"1","checkin_button_link":"Check-in here","checkin_icon":"1","checkin_verb":"Check-in","checkedinto_verb":"checked-into","checkin_your":"You\'ve checked-in here","checkin_total":"Total check-ins here","nomobile":"0","name":"sitetagcheckin.checkinbutton-sitetagcheckin"}',
            ));
          }
        }
      }

      //PUT X PEOPLE HERE WIDGET IN ADMIN CONTENT TABLE FOR BUSINESS
      $select = new Zend_Db_Select($db);
      $select_content = $select
              ->from('engine4_sitebusiness_admincontent')
              ->where('business_id = ?', $business_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitetagcheckin.checkinuser-sitetagcheckin')
              ->limit(1);
      $content = $select_content->query()->fetchAll();
      if (empty($content)) {
        $select = new Zend_Db_Select($db);
        $select_container = $select
                ->from('engine4_sitebusiness_admincontent', 'admincontent_id')
                ->where('business_id = ?', $business_id)
                ->where('type = ?', 'container')
                ->limit(1);
        $container = $select_container->query()->fetchAll();
        if (!empty($container)) {
          $container_id = $container[0]['admincontent_id'];
          $select = new Zend_Db_Select($db);
          $select_left = $select
                  ->from('engine4_sitebusiness_admincontent')
                  ->where('business_id = ?', $business_id)
                  ->where('parent_content_id = ?', $container_id)
                  ->where('type = ?', 'container')
                  ->where('name in (?)', array('left', 'right'))
                  ->limit(1);
          $left = $select_left->query()->fetchAll();
          if (!empty($left)) {
            $left_id = $left[0]['admincontent_id'];
            $db->insert('engine4_sitebusiness_admincontent', array(
                'business_id' => $business_id,
                'type' => 'widget',
                'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
                'parent_content_id' => $left_id,
                'order' => 14,
                'params' => '{"title":"","titleCount":true,"checkedin_heading":"People Here","checkedin_see_all_heading":"People who have been here","checkedin_users":"1","checkedin_user_photo":"1","checkedin_user_name":"1","checkedin_user_checkedtime":"1","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}'
            ));

            $db->insert('engine4_sitebusiness_admincontent', array(
                'business_id' => $business_id,
                'type' => 'widget',
                'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
                'parent_content_id' => $left_id,
                'order' => 14,
                'params' => '{"title":"","titleCount":true,"checkedin_heading":"People Have Been Here","checkedin_see_all_heading":"People Who\'ve Been Here","checkedin_users":"0","checkedin_user_photo":"1","checkedin_user_name":"1","checkedin_user_checkedtime":"1","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}'
            ));
          }
        }
      }

      //PUT CHECKINBUTTON WIDGET IN CORE CONTENT TABLE FOR BUSINESS
      $select = new Zend_Db_Select($db);
      $select_content = $select
              ->from('engine4_core_content')
              ->where('page_id = ?', $business_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitetagcheckin.checkinbutton-sitetagcheckin')
              ->limit(1);
      $content = $select_content->query()->fetchAll();
      if (empty($content)) {
        $select = new Zend_Db_Select($db);
        $select_container = $select
                ->from('engine4_core_content', 'content_id')
                ->where('page_id = ?', $business_id)
                ->where('type = ?', 'container')
                ->limit(1);
        $container = $select_container->query()->fetchAll();
        if (!empty($container)) {
          $container_id = $container[0]['content_id'];
          $select = new Zend_Db_Select($db);
          $select_middle = $select
                  ->from('engine4_core_content')
                  ->where('parent_content_id = ?', $container_id)
                  ->where('page_id = ?', $business_id)
                  ->where('type = ?', 'container')
                  ->where('name in (?)', array('left', 'right'))
                  ->limit(1);
          $middle = $select_middle->query()->fetchAll();
          if (!empty($middle)) {
            $middle_id = $middle[0]['content_id'];
            $db->insert('engine4_core_content', array(
                'page_id' => $business_id,
                'type' => 'widget',
                'name' => 'sitetagcheckin.checkinbutton-sitetagcheckin',
                'parent_content_id' => $middle_id,
                'order' => 14,
                'params' => '{"title":"","titleCount":false,"checkin_use":"1","checkin_button_sidebar":"1","checkin_button":"1","checkin_button_link":"Check-in here","checkin_icon":"1","checkin_verb":"Check-in","checkedinto_verb":"checked-into","checkin_your":"You\'ve checked-in here","checkin_total":"Total check-ins here","nomobile":"0","name":"sitetagcheckin.checkinbutton-sitetagcheckin"}',
            ));
          }
        }
      }

      //PUT CHECKINBUTTON WIDGET IN USER CONTENT TABLE FOR BUSINESS
      $select = new Zend_Db_Select($db);
      $select = $select
              ->from('engine4_sitebusiness_contentbusinesses', 'contentbusiness_id');

      $contentbusiness_ids = $select->query()->fetchAll();
      foreach ($contentbusiness_ids as $contentbusiness_id) {
        if (!empty($contentbusiness_id)) {
          $contentbusiness_id = $contentbusiness_id['contentbusiness_id'];
          $select = new Zend_Db_Select($db);
          $select_content = $select
                  ->from('engine4_sitebusiness_content')
                  ->where('contentbusiness_id = ?', $contentbusiness_id)
                  ->where('type = ?', 'widget')
                  ->where('name = ?', 'sitetagcheckin.checkinbutton-sitetagcheckin')
                  ->limit(1);
          $content = $select_content->query()->fetchAll();
          if (empty($content)) {
            $select = new Zend_Db_Select($db);
            $select_container = $select
                    ->from('engine4_sitebusiness_content', 'content_id')
                    ->where('contentbusiness_id = ?', $contentbusiness_id)
                    ->where('type = ?', 'container')
                    ->limit(1);
            $container = $select_container->query()->fetchAll();
            if (!empty($container)) {
              $container_id = $container[0]['content_id'];
              $select = new Zend_Db_Select($db);
              $select_middle = $select
                      ->from('engine4_sitebusiness_content')
                      ->where('parent_content_id = ?', $container_id)
                      ->where('contentbusiness_id = ?', $contentbusiness_id)
                      ->where('type = ?', 'container')
                      ->where('name in (?)', array('left', 'right'))
                      ->limit(1);
              $middle = $select_middle->query()->fetchAll();
              if (!empty($middle)) {
                $middle_id = $middle[0]['content_id'];
                $db->insert('engine4_sitebusiness_content', array(
                    'contentbusiness_id' => $contentbusiness_id,
                    'type' => 'widget',
                    'name' => 'sitetagcheckin.checkinbutton-sitetagcheckin',
                    'parent_content_id' => $middle_id,
                    'order' => 14,
                    'params' => '{"title":"","titleCount":false,"checkin_use":"1","checkin_button_sidebar":"1","checkin_button":"1","checkin_button_link":"Check-in here","checkin_icon":"1","checkin_verb":"Check-in","checkedinto_verb":"checked-into","checkin_your":"You\'ve checked-in here","checkin_total":"Total check-ins here","nomobile":"0","name":"sitetagcheckin.checkinbutton-sitetagcheckin"}',
                ));
              }
            }
          }
        }
      }

      //PUT CHECKINUSER WIDGET IN CORE CONTENT TABLE FOR BUSINESS
      $select = new Zend_Db_Select($db);
      $select_content = $select
              ->from('engine4_core_content')
              ->where('page_id = ?', $page_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitetagcheckin.checkinuser-sitetagcheckin')
              ->limit(1);
      $content = $select_content->query()->fetchAll();
      if (empty($content)) {
        $select = new Zend_Db_Select($db);
        $select_container = $select
                ->from('engine4_core_content', 'content_id')
                ->where('page_id = ?', $page_id)
                ->where('type = ?', 'container')
                ->limit(1);
        $container = $select_container->query()->fetchAll();
        if (!empty($container)) {
          $container_id = $container[0]['content_id'];
          $select = new Zend_Db_Select($db);
          $select_left = $select
                  ->from('engine4_core_content')
                  ->where('parent_content_id = ?', $container_id)
                  ->where('page_id = ?', $page_id)
                  ->where('type = ?', 'container')
                  ->where('name in (?)', array('left', 'right'))
                  ->limit(1);
          $left = $select_left->query()->fetchAll();
          if (!empty($left)) {
            $left_id = $left[0]['content_id'];
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
                'parent_content_id' => $left_id,
                'order' => 14,
                'params' => '{"title":"","titleCount":true,"checkedin_heading":"People Here","checkedin_see_all_heading":"People Who\'ve Been Here","checkedin_users":"1","checkedin_user_photo":"1","checkedin_user_name":"1","checkedin_user_checkedtime":"1","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}'
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
                'parent_content_id' => $left_id,
                'order' => 5,
                'params' => '{"title":"","titleCount":true,"checkedin_heading":"People Have Been Here","checkedin_see_all_heading":"People Who\'ve Been Here","checkedin_users":"0","checkedin_user_photo":"1","checkedin_user_name":"1","checkedin_user_checkedtime":"1","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}'
            ));
          }
        }
      }

      //PUT CHECKINUSER WIDGET IN USER CONTENT TABLE FOR BUSINESS
      $select = new Zend_Db_Select($db);
      $select = $select
              ->from('engine4_sitebusiness_contentbusinesses', 'contentbusiness_id');

      $contentbusiness_ids = $select->query()->fetchAll();
      foreach ($contentbusiness_ids as $contentbusiness_id) {
        if (!empty($contentbusiness_id)) {
          $contentbusiness_id = $contentbusiness_id['contentbusiness_id'];
          $select = new Zend_Db_Select($db);
          $select_content = $select
                  ->from('engine4_sitebusiness_content')
                  ->where('contentbusiness_id = ?', $contentbusiness_id)
                  ->where('type = ?', 'widget')
                  ->where('name = ?', 'sitetagcheckin.checkinuser-sitetagcheckin')
                  ->limit(1);
          $content = $select_content->query()->fetchAll();
          if (empty($content)) {
            $select = new Zend_Db_Select($db);
            $select_container = $select
                    ->from('engine4_sitebusiness_content', 'content_id')
                    ->where('contentbusiness_id = ?', $contentbusiness_id)
                    ->where('type = ?', 'container')
                    ->limit(1);
            $container = $select_container->query()->fetchAll();
            if (!empty($container)) {
              $container_id = $container[0]['content_id'];
              $select = new Zend_Db_Select($db);
              $select_left = $select
                      ->from('engine4_sitebusiness_content')
                      ->where('parent_content_id = ?', $container_id)
                      ->where('contentbusiness_id = ?', $contentbusiness_id)
                      ->where('type = ?', 'container')
                      ->where('name in (?)', array('left', 'right'))
                      ->limit(1);
              $left = $select_left->query()->fetchAll();
              if (!empty($left)) {
                $left_id = $left[0]['content_id'];
                $db->insert('engine4_sitebusiness_content', array(
                    'contentbusiness_id' => $contentbusiness_id,
                    'type' => 'widget',
                    'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
                    'parent_content_id' => $left_id,
                    'order' => 14,
                    'params' => '{"title":"","titleCount":true,"checkedin_heading":"People Here","checkedin_see_all_heading":"People Who\' ve Been Here","checkedin_users":"1","checkedin_user_photo":"1","checkedin_user_name":"1","checkedin_user_checkedtime":"1","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}'
                ));

                $db->insert('engine4_sitebusiness_content', array(
                    'contentbusiness_id' => $contentbusiness_id,
                    'type' => 'widget',
                    'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
                    'parent_content_id' => $left_id,
                    'order' => 14,
                    'params' => '{"title":"","titleCount":true,"checkedin_heading":"People Have Been Here","checkedin_see_all_heading":"People Who\'ve Been Here","checkedin_users":"0","checkedin_user_photo":"1","checkedin_user_name":"1","checkedin_user_checkedtime":"1","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}'
                ));
              }
            }
          }
        }
      }
    }
  }
}

//CHECK THAT SITEGROUP PLUGIN IS ACTIVATED OR NOT
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_settings')
        ->where('name = ?', 'sitegroup.is.active')
        ->limit(1);
$sitegroup_settings = $select->query()->fetchAll();
if (!empty($sitegroup_settings)) {
  $sitegroup_is_active = $sitegroup_settings[0]['value'];
} else {
  $sitegroup_is_active = 0;
}

//CHECK THAT SITEGROUP PLUGIN IS INSTALLED OR NOT
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_modules')
        ->where('name = ?', 'sitegroup')
        ->where('enabled = ?', 1);
$check_sitegroup = $select->query()->fetchObject();


if (!empty($check_sitegroup) && !empty($sitegroup_is_active)) {
  $select = new Zend_Db_Select($db);
  $check_sitetagcheckin = $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'sitetagcheckin')->query()->fetchObject();

  if (!empty($check_sitetagcheckin)) {
    //PUT CHECKINBUTTON WIDGET IN ADMIN CONTENT TABLE FOR GROUP
    $select = new Zend_Db_Select($db);
    $select_group = $select
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'sitegroup_index_view')
            ->limit(1);
    $group = $select_group->query()->fetchAll();
    if (!empty($group)) {
      $page_id = $group_id = $group[0]['page_id'];

      $select = new Zend_Db_Select($db);
      $select_content = $select
              ->from('engine4_sitegroup_admincontent')
              ->where('group_id = ?', $group_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitetagcheckin.checkinbutton-sitetagcheckin')
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
          $select_middle = $select
                  ->from('engine4_sitegroup_admincontent')
                  ->where('parent_content_id = ?', $container_id)
                  ->where('group_id = ?', $group_id)
                  ->where('type = ?', 'container')
                  ->where('name in (?)', array('left', 'right'))
                  ->limit(1);
          $middle = $select_middle->query()->fetchAll();
          if (!empty($middle)) {
            $middle_id = $middle[0]['admincontent_id'];
            $db->insert('engine4_sitegroup_admincontent', array(
                'group_id' => $group_id,
                'type' => 'widget',
                'name' => 'sitetagcheckin.checkinbutton-sitetagcheckin',
                'parent_content_id' => $middle_id,
                'order' => 14,
                'params' => '{"title":"","titleCount":false,"checkin_use":"1","checkin_button_sidebar":"1","checkin_button":"1","checkin_button_link":"Check-in here","checkin_icon":"1","checkin_verb":"Check-in","checkedinto_verb":"checked-into","checkin_your":"You\'ve checked-in here","checkin_total":"Total check-ins here","nomobile":"0","name":"sitetagcheckin.checkinbutton-sitetagcheckin"}',
            ));
          }
        }
      }

      //PUT X PEOPLE HERE WIDGET IN ADMIN CONTENT TABLE FOR GROUP
      $select = new Zend_Db_Select($db);
      $select_content = $select
              ->from('engine4_sitegroup_admincontent')
              ->where('group_id = ?', $group_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitetagcheckin.checkinuser-sitetagcheckin')
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
                  ->where('group_id = ?', $group_id)
                  ->where('type = ?', 'container')
                  ->where('name in (?)', array('left', 'right'))
                  ->limit(1);
          $left = $select_left->query()->fetchAll();
          if (!empty($left)) {
            $left_id = $left[0]['admincontent_id'];
            $db->insert('engine4_sitegroup_admincontent', array(
                'group_id' => $group_id,
                'type' => 'widget',
                'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
                'parent_content_id' => $left_id,
                'order' => 14,
                'params' => '{"title":"","titleCount":true,"checkedin_heading":"People Here","checkedin_see_all_heading":"People who have been here","checkedin_users":"1","checkedin_user_photo":"1","checkedin_user_name":"1","checkedin_user_checkedtime":"1","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}'
            ));

            $db->insert('engine4_sitegroup_admincontent', array(
                'group_id' => $group_id,
                'type' => 'widget',
                'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
                'parent_content_id' => $left_id,
                'order' => 14,
                'params' => '{"title":"","titleCount":true,"checkedin_heading":"People Have Been Here","checkedin_see_all_heading":"People Who\'ve Been Here","checkedin_users":"0","checkedin_user_photo":"1","checkedin_user_name":"1","checkedin_user_checkedtime":"1","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}'
            ));
          }
        }
      }

      //PUT CHECKINBUTTON WIDGET IN CORE CONTENT TABLE FOR GROUP
      $select = new Zend_Db_Select($db);
      $select_content = $select
              ->from('engine4_core_content')
              ->where('page_id = ?', $group_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitetagcheckin.checkinbutton-sitetagcheckin')
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
          echo $select_middle = $select
          ->from('engine4_core_content')
          ->where('parent_content_id = ?', $container_id)
          ->where('page_id = ?', $group_id)
          ->where('type = ?', 'container')
          ->where('name in (?)', array('left', 'right'))
          ->limit(1);
          $middle = $select_middle->query()->fetchAll();

          if (!empty($middle)) {
            $middle_id = $middle[0]['content_id'];
            $db->insert('engine4_core_content', array(
                'page_id' => $group_id,
                'type' => 'widget',
                'name' => 'sitetagcheckin.checkinbutton-sitetagcheckin',
                'parent_content_id' => $middle_id,
                'order' => 14,
                'params' => '{"title":"","titleCount":false,"checkin_use":"1","checkin_button_sidebar":"1","checkin_button":"1","checkin_button_link":"Check-in here","checkin_icon":"1","checkin_verb":"Check-in","checkedinto_verb":"checked-into","checkin_your":"You\'ve checked-in here","checkin_total":"Total check-ins here","nomobile":"0","name":"sitetagcheckin.checkinbutton-sitetagcheckin"}',
            ));
          }
        }
      }

      //PUT CHECKINBUTTON WIDGET IN USER CONTENT TABLE FOR GROUP
      $select = new Zend_Db_Select($db);
      $select = $select
              ->from('engine4_sitegroup_contentgroups', 'contentgroup_id');

      $contentgroup_ids = $select->query()->fetchAll();
      foreach ($contentgroup_ids as $contentgroup_id) {
        if (!empty($contentgroup_id)) {
          $contentgroup_id = $contentgroup_id['contentgroup_id'];
          $select = new Zend_Db_Select($db);
          $select_content = $select
                  ->from('engine4_sitegroup_content')
                  ->where('contentgroup_id = ?', $contentgroup_id)
                  ->where('type = ?', 'widget')
                  ->where('name = ?', 'sitetagcheckin.checkinbutton-sitetagcheckin')
                  ->limit(1);
          $content = $select_content->query()->fetchAll();
          if (empty($content)) {
            $select = new Zend_Db_Select($db);
            $select_container = $select
                    ->from('engine4_sitegroup_content', 'content_id')
                    ->where('contentgroup_id = ?', $contentgroup_id)
                    ->where('type = ?', 'container')
                    ->limit(1);
            $container = $select_container->query()->fetchAll();
            if (!empty($container)) {
              $container_id = $container[0]['content_id'];
              $select = new Zend_Db_Select($db);
              $select_middle = $select
                      ->from('engine4_sitegroup_content')
                      ->where('parent_content_id = ?', $container_id)
                      ->where('contentgroup_id = ?', $contentgroup_id)
                      ->where('type = ?', 'container')
                      ->where('name in (?)', array('left', 'right'))
                      ->limit(1);
              $middle = $select_middle->query()->fetchAll();
              if (!empty($middle)) {
                $middle_id = $middle[0]['content_id'];
                $db->insert('engine4_sitegroup_content', array(
                    'contentgroup_id' => $contentgroup_id,
                    'type' => 'widget',
                    'name' => 'sitetagcheckin.checkinbutton-sitetagcheckin',
                    'parent_content_id' => $middle_id,
                    'order' => 14,
                    'params' => '{"title":"","titleCount":false,"checkin_use":"1","checkin_button_sidebar":"1","checkin_button":"1","checkin_button_link":"Check-in here","checkin_icon":"1","checkin_verb":"Check-in","checkedinto_verb":"checked-into","checkin_your":"You\'ve checked-in here","checkin_total":"Total check-ins here","nomobile":"0","name":"sitetagcheckin.checkinbutton-sitetagcheckin"}',
                ));
              }
            }
          }
        }
      }

      //PUT CHECKINUSER WIDGET IN CORE CONTENT TABLE FOR GROUP
      $select = new Zend_Db_Select($db);
      $select_content = $select
              ->from('engine4_core_content')
              ->where('page_id = ?', $page_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitetagcheckin.checkinuser-sitetagcheckin')
              ->limit(1);
      $content = $select_content->query()->fetchAll();
      if (empty($content)) {
        $select = new Zend_Db_Select($db);
        $select_container = $select
                ->from('engine4_core_content', 'content_id')
                ->where('page_id = ?', $page_id)
                ->where('type = ?', 'container')
                ->limit(1);
        $container = $select_container->query()->fetchAll();
        if (!empty($container)) {
          $container_id = $container[0]['content_id'];
          $select = new Zend_Db_Select($db);
          $select_left = $select
                  ->from('engine4_core_content')
                  ->where('parent_content_id = ?', $container_id)
                  ->where('page_id = ?', $page_id)
                  ->where('type = ?', 'container')
                  ->where('name in (?)', array('left', 'right'))
                  ->limit(1);
          $left = $select_left->query()->fetchAll();
          if (!empty($left)) {
            $left_id = $left[0]['content_id'];
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
                'parent_content_id' => $left_id,
                'order' => 14,
                'params' => '{"title":"","titleCount":true,"checkedin_heading":"People Here","checkedin_see_all_heading":"People Who\'ve Been Here","checkedin_users":"1","checkedin_user_photo":"1","checkedin_user_name":"1","checkedin_user_checkedtime":"1","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}'
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
                'parent_content_id' => $left_id,
                'order' => 5,
                'params' => '{"title":"","titleCount":true,"checkedin_heading":"People Have Been Here","checkedin_see_all_heading":"People Who\'ve Been Here","checkedin_users":"0","checkedin_user_photo":"1","checkedin_user_name":"1","checkedin_user_checkedtime":"1","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}'
            ));
          }
        }
      }

      //PUT CHECKINUSER WIDGET IN USER CONTENT TABLE FOR GROUP
      $select = new Zend_Db_Select($db);
      $select = $select
              ->from('engine4_sitegroup_contentgroups', 'contentgroup_id');

      $contentgroup_ids = $select->query()->fetchAll();
      foreach ($contentgroup_ids as $contentgroup_id) {
        if (!empty($contentgroup_id)) {
          $contentgroup_id = $contentgroup_id['contentgroup_id'];
          $select = new Zend_Db_Select($db);
          $select_content = $select
                  ->from('engine4_sitegroup_content')
                  ->where('contentgroup_id = ?', $contentgroup_id)
                  ->where('type = ?', 'widget')
                  ->where('name = ?', 'sitetagcheckin.checkinuser-sitetagcheckin')
                  ->limit(1);
          $content = $select_content->query()->fetchAll();
          if (empty($content)) {
            $select = new Zend_Db_Select($db);
            $select_container = $select
                    ->from('engine4_sitegroup_content', 'content_id')
                    ->where('contentgroup_id = ?', $contentgroup_id)
                    ->where('type = ?', 'container')
                    ->limit(1);
            $container = $select_container->query()->fetchAll();
            if (!empty($container)) {
              $container_id = $container[0]['content_id'];
              $select = new Zend_Db_Select($db);
              $select_left = $select
                      ->from('engine4_sitegroup_content')
                      ->where('parent_content_id = ?', $container_id)
                      ->where('contentgroup_id = ?', $contentgroup_id)
                      ->where('type = ?', 'container')
                      ->where('name in (?)', array('left', 'right'))
                      ->limit(1);
              $left = $select_left->query()->fetchAll();
              if (!empty($left)) {
                $left_id = $left[0]['content_id'];
                $db->insert('engine4_sitegroup_content', array(
                    'contentgroup_id' => $contentgroup_id,
                    'type' => 'widget',
                    'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
                    'parent_content_id' => $left_id,
                    'order' => 14,
                    'params' => '{"title":"","titleCount":true,"checkedin_heading":"People Here","checkedin_see_all_heading":"People Who\' ve Been Here","checkedin_users":"1","checkedin_user_photo":"1","checkedin_user_name":"1","checkedin_user_checkedtime":"1","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}'
                ));

                $db->insert('engine4_sitegroup_content', array(
                    'contentgroup_id' => $contentgroup_id,
                    'type' => 'widget',
                    'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
                    'parent_content_id' => $left_id,
                    'order' => 14,
                    'params' => '{"title":"","titleCount":true,"checkedin_heading":"People Have Been Here","checkedin_see_all_heading":"People Who\'ve Been Here","checkedin_users":"0","checkedin_user_photo":"1","checkedin_user_name":"1","checkedin_user_checkedtime":"1","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}'
                ));
              }
            }
          }
        }
      }
    }
  }
}

//CHECK THAT SITEGROUP PLUGIN IS ACTIVATED OR NOT
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_settings')
        ->where('name = ?', 'sitestore.is.active')
        ->limit(1);
$sitestore_settings = $select->query()->fetchAll();
if (!empty($sitestore_settings)) {
  $sitestore_is_active = $sitestore_settings[0]['value'];
} else {
  $sitestore_is_active = 0;
}
//CHECK THAT SITESTORE PLUGIN IS INSTALLED OR NOT
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_modules')
        ->where('name = ?', 'sitestore')
        ->where('enabled = ?', 1);
$check_sitestore = $select->query()->fetchObject();


if (!empty($check_sitestore) && !empty($sitestore_is_active)) {
  $select = new Zend_Db_Select($db);
  $check_sitetagcheckin = $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'sitetagcheckin')->query()->fetchObject();

  if (!empty($check_sitetagcheckin)) {
    //PUT CHECKINBUTTON WIDGET IN ADMIN CONTENT TABLE FOR STORE
    $select = new Zend_Db_Select($db);
    $select_store = $select
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'sitestore_index_view')
            ->limit(1);
    $store = $select_store->query()->fetchAll();
    if (!empty($store)) {
      $page_id = $store_id = $store[0]['page_id'];

      $select = new Zend_Db_Select($db);
      $select_content = $select
              ->from('engine4_sitestore_admincontent')
              ->where('store_id = ?', $store_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitetagcheckin.checkinbutton-sitetagcheckin')
              ->limit(1);
      $content = $select_content->query()->fetchAll();
      if (empty($content)) {
        $select = new Zend_Db_Select($db);
        $select_container = $select
                ->from('engine4_sitestore_admincontent', 'admincontent_id')
                ->where('store_id = ?', $store_id)
                ->where('type = ?', 'container')
                ->limit(1);
        $container = $select_container->query()->fetchAll();
        if (!empty($container)) {
          $container_id = $container[0]['admincontent_id'];
          $select = new Zend_Db_Select($db);
          $select_middle = $select
                  ->from('engine4_sitestore_admincontent')
                  ->where('parent_content_id = ?', $container_id)
                  ->where('store_id = ?', $store_id)
                  ->where('type = ?', 'container')
                  ->where('name in (?)', array('left', 'right'))
                  ->limit(1);
          $middle = $select_middle->query()->fetchAll();
          if (!empty($middle)) {
            $middle_id = $middle[0]['admincontent_id'];
            $db->insert('engine4_sitestore_admincontent', array(
                'store_id' => $store_id,
                'type' => 'widget',
                'name' => 'sitetagcheckin.checkinbutton-sitetagcheckin',
                'parent_content_id' => $middle_id,
                'order' => 14,
                'params' => '{"title":"","titleCount":false,"checkin_use":"1","checkin_button_sidebar":"1","checkin_button":"1","checkin_button_link":"Explore this store","checkin_icon":"1","checkin_verb":"Explore","checkedinto_verb":"explored","checkin_your":"You\'ve explored","checkin_total":"Total explored","nomobile":"0","name":"sitetagcheckin.checkinbutton-sitetagcheckin"}',
            ));
          }
        }
      }

      //PUT X PEOPLE HERE WIDGET IN ADMIN CONTENT TABLE FOR STORE
      $select = new Zend_Db_Select($db);
      $select_content = $select
              ->from('engine4_sitestore_admincontent')
              ->where('store_id = ?', $store_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitetagcheckin.checkinuser-sitetagcheckin')
              ->limit(1);
      $content = $select_content->query()->fetchAll();
      if (empty($content)) {
        $select = new Zend_Db_Select($db);
        $select_container = $select
                ->from('engine4_sitestore_admincontent', 'admincontent_id')
                ->where('store_id = ?', $store_id)
                ->where('type = ?', 'container')
                ->limit(1);
        $container = $select_container->query()->fetchAll();
        if (!empty($container)) {
          $container_id = $container[0]['admincontent_id'];
          $select = new Zend_Db_Select($db);
          $select_left = $select
                  ->from('engine4_sitestore_admincontent')
                  ->where('parent_content_id = ?', $container_id)
                  ->where('store_id = ?', $store_id)
                  ->where('type = ?', 'container')
                  ->where('name in (?)', array('left', 'right'))
                  ->limit(1);
          $left = $select_left->query()->fetchAll();
          if (!empty($left)) {
            $left_id = $left[0]['admincontent_id'];
            $db->insert('engine4_sitestore_admincontent', array(
                'store_id' => $store_id,
                'type' => 'widget',
                'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
                'parent_content_id' => $left_id,
                'order' => 14,
                'params' => '{"title":"","titleCount":true,"checkedin_heading":"People Here","checkedin_see_all_heading":"People who have been here","checkedin_users":"1","checkedin_user_photo":"1","checkedin_user_name":"1","checkedin_user_checkedtime":"1","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}'
            ));

            $db->insert('engine4_sitestore_admincontent', array(
                'store_id' => $store_id,
                'type' => 'widget',
                'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
                'parent_content_id' => $left_id,
                'order' => 14,
                'params' => '{"title":"","titleCount":true,"checkedin_heading":"People Have Been Here","checkedin_see_all_heading":"People Who\'ve Been Here","checkedin_users":"0","checkedin_user_photo":"1","checkedin_user_name":"1","checkedin_user_checkedtime":"1","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}'
            ));
          }
        }
      }

      //PUT CHECKINBUTTON WIDGET IN CORE CONTENT TABLE FOR STORE
      $select = new Zend_Db_Select($db);
      $select_content = $select
              ->from('engine4_core_content')
              ->where('page_id = ?', $store_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitetagcheckin.checkinbutton-sitetagcheckin')
              ->limit(1);
      $content = $select_content->query()->fetchAll();
      if (empty($content)) {
        $select = new Zend_Db_Select($db);
        $select_container = $select
                ->from('engine4_core_content', 'content_id')
                ->where('page_id = ?', $store_id)
                ->where('type = ?', 'container')
                ->limit(1);
        $container = $select_container->query()->fetchAll();

        if (!empty($container)) {
          $container_id = $container[0]['content_id'];
          $select = new Zend_Db_Select($db);
          echo $select_middle = $select
          ->from('engine4_core_content')
          ->where('parent_content_id = ?', $container_id)
          ->where('page_id = ?', $store_id)
          ->where('type = ?', 'container')
          ->where('name in (?)', array('left', 'right'))
          ->limit(1);
          $middle = $select_middle->query()->fetchAll();

          if (!empty($middle)) {
            $middle_id = $middle[0]['content_id'];
            $db->insert('engine4_core_content', array(
                'page_id' => $store_id,
                'type' => 'widget',
                'name' => 'sitetagcheckin.checkinbutton-sitetagcheckin',
                'parent_content_id' => $middle_id,
                'order' => 14,
                'params' => '{"title":"","titleCount":false,"checkin_use":"1","checkin_button_sidebar":"1","checkin_button":"1","checkin_button_link":"Explore this store","checkin_icon":"1","checkin_verb":"Explore","checkedinto_verb":"explored","checkin_your":"You\'ve explored","checkin_total":"Total explored","nomobile":"0","name":"sitetagcheckin.checkinbutton-sitetagcheckin"}',
            ));
          }
        }
      }

      //PUT CHECKINBUTTON WIDGET IN USER CONTENT TABLE FOR STORE
      $select = new Zend_Db_Select($db);
      $select = $select
              ->from('engine4_sitestore_contentstores', 'contentstore_id');

      $contentstore_ids = $select->query()->fetchAll();
      foreach ($contentstore_ids as $contentstore_id) {
        if (!empty($contentstore_id)) {
          $contentstore_id = $contentstore_id['contentstore_id'];
          $select = new Zend_Db_Select($db);
          $select_content = $select
                  ->from('engine4_sitestore_content')
                  ->where('contentstore_id = ?', $contentstore_id)
                  ->where('type = ?', 'widget')
                  ->where('name = ?', 'sitetagcheckin.checkinbutton-sitetagcheckin')
                  ->limit(1);
          $content = $select_content->query()->fetchAll();
          if (empty($content)) {
            $select = new Zend_Db_Select($db);
            $select_container = $select
                    ->from('engine4_sitestore_content', 'content_id')
                    ->where('contentstore_id = ?', $contentstore_id)
                    ->where('type = ?', 'container')
                    ->limit(1);
            $container = $select_container->query()->fetchAll();
            if (!empty($container)) {
              $container_id = $container[0]['content_id'];
              $select = new Zend_Db_Select($db);
              $select_middle = $select
                      ->from('engine4_sitestore_content')
                      ->where('parent_content_id = ?', $container_id)
                      ->where('contentstore_id = ?', $contentstore_id)
                      ->where('type = ?', 'container')
                      ->where('name in (?)', array('left', 'right'))
                      ->limit(1);
              $middle = $select_middle->query()->fetchAll();
              if (!empty($middle)) {
                $middle_id = $middle[0]['content_id'];
                $db->insert('engine4_sitestore_content', array(
                    'contentstore_id' => $contentstore_id,
                    'type' => 'widget',
                    'name' => 'sitetagcheckin.checkinbutton-sitetagcheckin',
                    'parent_content_id' => $middle_id,
                    'order' => 14,
                    'params' => '{"title":"","titleCount":false,"checkin_use":"1","checkin_button_sidebar":"1","checkin_button":"1","checkin_button_link":"Explore this store","checkin_icon":"1","checkin_verb":"Explore","checkedinto_verb":"explored","checkin_your":"You\'ve explored","checkin_total":"Total explored","nomobile":"0","name":"sitetagcheckin.checkinbutton-sitetagcheckin"}',
                ));
              }
            }
          }
        }
      }

      //PUT CHECKINUSER WIDGET IN CORE CONTENT TABLE FOR STORE
      $select = new Zend_Db_Select($db);
      $select_content = $select
              ->from('engine4_core_content')
              ->where('page_id = ?', $page_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitetagcheckin.checkinuser-sitetagcheckin')
              ->limit(1);
      $content = $select_content->query()->fetchAll();
      if (empty($content)) {
        $select = new Zend_Db_Select($db);
        $select_container = $select
                ->from('engine4_core_content', 'content_id')
                ->where('page_id = ?', $page_id)
                ->where('type = ?', 'container')
                ->limit(1);
        $container = $select_container->query()->fetchAll();
        if (!empty($container)) {
          $container_id = $container[0]['content_id'];
          $select = new Zend_Db_Select($db);
          $select_left = $select
                  ->from('engine4_core_content')
                  ->where('parent_content_id = ?', $container_id)
                  ->where('page_id = ?', $page_id)
                  ->where('type = ?', 'container')
                  ->where('name in (?)', array('left', 'right'))
                  ->limit(1);
          $left = $select_left->query()->fetchAll();
          if (!empty($left)) {
            $left_id = $left[0]['content_id'];
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
                'parent_content_id' => $left_id,
                'order' => 14,
                'params' => '{"title":"","titleCount":true,"checkedin_heading":"People Here","checkedin_see_all_heading":"People Who\'ve Been Here","checkedin_users":"1","checkedin_user_photo":"1","checkedin_user_name":"1","checkedin_user_checkedtime":"1","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}'
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
                'parent_content_id' => $left_id,
                'order' => 5,
                'params' => '{"title":"","titleCount":true,"checkedin_heading":"People Have Been Here","checkedin_see_all_heading":"People Who\'ve Been Here","checkedin_users":"0","checkedin_user_photo":"1","checkedin_user_name":"1","checkedin_user_checkedtime":"1","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}'
            ));
          }
        }
      }

      //PUT CHECKINUSER WIDGET IN USER CONTENT TABLE FOR STORE
      $select = new Zend_Db_Select($db);
      $select = $select
              ->from('engine4_sitestore_contentstores', 'contentstore_id');

      $contentstore_ids = $select->query()->fetchAll();
      foreach ($contentstore_ids as $contentstore_id) {
        if (!empty($contentstore_id)) {
          $contentstore_id = $contentstore_id['contentstore_id'];
          $select = new Zend_Db_Select($db);
          $select_content = $select
                  ->from('engine4_sitestore_content')
                  ->where('contentstore_id = ?', $contentstore_id)
                  ->where('type = ?', 'widget')
                  ->where('name = ?', 'sitetagcheckin.checkinuser-sitetagcheckin')
                  ->limit(1);
          $content = $select_content->query()->fetchAll();
          if (empty($content)) {
            $select = new Zend_Db_Select($db);
            $select_container = $select
                    ->from('engine4_sitestore_content', 'content_id')
                    ->where('contentstore_id = ?', $contentstore_id)
                    ->where('type = ?', 'container')
                    ->limit(1);
            $container = $select_container->query()->fetchAll();
            if (!empty($container)) {
              $container_id = $container[0]['content_id'];
              $select = new Zend_Db_Select($db);
              $select_left = $select
                      ->from('engine4_sitestore_content')
                      ->where('parent_content_id = ?', $container_id)
                      ->where('contentstore_id = ?', $contentstore_id)
                      ->where('type = ?', 'container')
                      ->where('name in (?)', array('left', 'right'))
                      ->limit(1);
              $left = $select_left->query()->fetchAll();
              if (!empty($left)) {
                $left_id = $left[0]['content_id'];
                $db->insert('engine4_sitestore_content', array(
                    'contentstore_id' => $contentstore_id,
                    'type' => 'widget',
                    'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
                    'parent_content_id' => $left_id,
                    'order' => 14,
                    'params' => '{"title":"","titleCount":true,"checkedin_heading":"People Here","checkedin_see_all_heading":"People Who\' ve Been Here","checkedin_users":"1","checkedin_user_photo":"1","checkedin_user_name":"1","checkedin_user_checkedtime":"1","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}'
                ));

                $db->insert('engine4_sitestore_content', array(
                    'contentstore_id' => $contentstore_id,
                    'type' => 'widget',
                    'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
                    'parent_content_id' => $left_id,
                    'order' => 14,
                    'params' => '{"title":"","titleCount":true,"checkedin_heading":"People Have Been Here","checkedin_see_all_heading":"People Who\'ve Been Here","checkedin_users":"0","checkedin_user_photo":"1","checkedin_user_name":"1","checkedin_user_checkedtime":"1","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}'
                ));
              }
            }
          }
        }
      }
    }
  }
}
//PUT MAP WIDGET IN USER PROFILE PAGE
$select = new Zend_Db_Select($db);
$selectPage = $select
        ->from('engine4_core_pages', array('page_id'))
        ->where('name =?', 'user_profile_index')
        ->limit(1);
$page_id = $selectPage->query()->fetchAll();
if (!empty($page_id)) {
  $page_id = $page_id[0]['page_id'];
  $select = new Zend_Db_Select($db);
  $selectWidgetId = $select
          ->from('engine4_core_content', array('content_id'))
          ->where('page_id =?', $page_id)
          ->where('type = ?', 'widget')
          ->where('name = ?', 'core.container-tabs')
          ->limit(1);
  $fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
  if (!empty($fetchWidgetContentId)) {
    $tab_id = $fetchWidgetContentId[0]['content_id'];
    $select = new Zend_Db_Select($db);
    $selectWidgetId = $select
            ->from('engine4_core_content', array('content_id'))
            ->where('page_id =?', $page_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitetagcheckin.map-sitetagcheckin')
            ->limit(1);
    $fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
    if (empty($fetchWidgetContentId)) {
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitetagcheckin.map-sitetagcheckin',
          'parent_content_id' => $tab_id,
          'order' => 30,
          'params' => '{"title":"Map","titleCount":"false"}',
      ));
    }
  }
}

//PUT CHECK-INS WIDGET IN USER PROFILE PAGE
$select = new Zend_Db_Select($db);
$selectPage = $select
        ->from('engine4_core_pages', array('page_id'))
        ->where('name =?', 'user_profile_index')
        ->limit(1);
$page_id = $selectPage->query()->fetchAll();
if (!empty($page_id)) {
  $page_id = $page_id[0]['page_id'];
  $select = new Zend_Db_Select($db);
  $selectWidgetId = $select
          ->from('engine4_core_content', array('content_id'))
          ->where('page_id =?', $page_id)
          ->where('type = ?', 'widget')
          ->where('name = ?', 'core.container-tabs')
          ->limit(1);
  $fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
  if (!empty($fetchWidgetContentId)) {
    $tab_id = $fetchWidgetContentId[0]['content_id'];
    $select = new Zend_Db_Select($db);
    $selectWidgetId = $select
            ->from('engine4_core_content', array('content_id'))
            ->where('page_id =?', $page_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitetagcheckin.profile-checkins-sitetagcheckin')
            ->limit(1);
    $fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
    if (empty($fetchWidgetContentId)) {
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitetagcheckin.profile-checkins-sitetagcheckin',
          'parent_content_id' => $tab_id,
          'order' => 30,
          'params' => '{"title":"Check-ins","titleCount":"false"}',
      ));
    }
  }
}

//PUT LOCATION WIDGET ON ALBUM VIEW PAGE
$select = new Zend_Db_Select($db);
$select_album = $select
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'album_album_view')
        ->limit(1);
$album = $select_album->query()->fetchAll();
if (!empty($album)) {
  $album_id = $album[0]['page_id'];
  $select = new Zend_Db_Select($db);
  $select_content = $select
          ->from('engine4_core_content')
          ->where('page_id = ?', $album_id)
          ->where('type = ?', 'widget')
          ->where('name = ?', 'sitetagcheckin.location-sitetagcheckin')
          ->limit(1);
  $content = $select_content->query()->fetchAll();
  if (empty($content)) {
    $select = new Zend_Db_Select($db);
    $select_container = $select
            ->from('engine4_core_content', 'content_id')
            ->where('page_id = ?', $album_id)
            ->where('type = ?', 'container')
            ->limit(1);
    $container = $select_container->query()->fetchAll();
    if (!empty($container)) {
      $container_id = $container[0]['content_id'];
      $select = new Zend_Db_Select($db);
      $select_middle = $select
              ->from('engine4_core_content')
              ->where('parent_content_id = ?', $container_id)
              ->where('type = ?', 'container')
              ->where('name = ?', 'middle')
              ->limit(1);
      $middle = $select_middle->query()->fetchAll();
      if (!empty($middle)) {
        $middle_id = $middle[0]['content_id'];
        $db->insert('engine4_core_content', array(
            'page_id' => $album_id,
            'type' => 'widget',
            'name' => 'sitetagcheckin.location-sitetagcheckin',
            'parent_content_id' => $middle_id,
            'order' => 3,
            'params' => '{"title":"","titleCount":""}',
        ));
      }
    }
  }

  $select = new Zend_Db_Select($db);
  $select_content = $select
          ->from('engine4_core_content')
          ->where('page_id = ?', $album_id)
          ->where('type = ?', 'widget')
          ->where('name = ?', 'sitetagcheckin.location-suggestions-sitetagcheckin')
          ->limit(1);
  $content = $select_content->query()->fetchAll();
  if (empty($content)) {
    $select = new Zend_Db_Select($db);
    $select_container = $select
            ->from('engine4_core_content', 'content_id')
            ->where('page_id = ?', $album_id)
            ->where('type = ?', 'container')
            ->limit(1);
    $container = $select_container->query()->fetchAll();
    if (!empty($container)) {
      $container_id = $container[0]['content_id'];
      $select = new Zend_Db_Select($db);
      $select_middle = $select
              ->from('engine4_core_content')
              ->where('parent_content_id = ?', $container_id)
              ->where('type = ?', 'container')
              ->where('name in (?)', array('left', 'right'))
              ->limit(1);
      $middle = $select_middle->query()->fetchAll();
      if (!empty($middle)) {
        $middle_id = $middle[0]['content_id'];
        $db->insert('engine4_core_content', array(
            'page_id' => $album_id,
            'type' => 'widget',
            'name' => 'sitetagcheckin.location-suggestions-sitetagcheckin',
            'parent_content_id' => $middle_id,
            'order' => 1,
            'params' => '{"title":"Add a Location to Your Photos","titleCount":false}',
        ));
      }
    }
  }
}

//PUT LOCATION WIDGET ON ALBUM VIEW PAGE
$select = new Zend_Db_Select($db);
$select_album = $select
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'album_photo_view')
        ->limit(1);
$album = $select_album->query()->fetchAll();
if (!empty($album)) {
  $album_id = $album[0]['page_id'];
  $select = new Zend_Db_Select($db);
  $select_content = $select
          ->from('engine4_core_content')
          ->where('page_id = ?', $album_id)
          ->where('type = ?', 'widget')
          ->where('name = ?', 'sitetagcheckin.location-sitetagcheckin')
          ->limit(1);
  $content = $select_content->query()->fetchAll();
  if (empty($content)) {
    $select = new Zend_Db_Select($db);
    $select_container = $select
            ->from('engine4_core_content', 'content_id')
            ->where('page_id = ?', $album_id)
            ->where('type = ?', 'container')
            ->limit(1);
    $container = $select_container->query()->fetchAll();
    if (!empty($container)) {
      $container_id = $container[0]['content_id'];
      $select = new Zend_Db_Select($db);
      $select_middle = $select
              ->from('engine4_core_content')
              ->where('parent_content_id = ?', $container_id)
              ->where('type = ?', 'container')
              ->where('name = ?', 'middle')
              ->limit(1);
      $middle = $select_middle->query()->fetchAll();
      if (!empty($middle)) {
        $middle_id = $middle[0]['content_id'];
        $db->insert('engine4_core_content', array(
            'page_id' => $album_id,
            'type' => 'widget',
            'name' => 'sitetagcheckin.location-sitetagcheckin',
            'parent_content_id' => $middle_id,
            'order' => 5,
            'params' => '{"title":"","titleCount":""}',
        ));
      }
    }
  }

  $select = new Zend_Db_Select($db);
  $select_content = $select
          ->from('engine4_core_content')
          ->where('page_id = ?', $album_id)
          ->where('type = ?', 'widget')
          ->where('name = ?', 'sitetagcheckin.location-suggestions-sitetagcheckin')
          ->limit(1);
  $content = $select_content->query()->fetchAll();
  if (empty($content)) {
    $select = new Zend_Db_Select($db);
    $select_container = $select
            ->from('engine4_core_content', 'content_id')
            ->where('page_id = ?', $album_id)
            ->where('type = ?', 'container')
            ->where('name = ?', 'main')
            ->limit(1);
    $container = $select_container->query()->fetchAll();
    if (!empty($container)) {
      $container_id = $container[0]['content_id'];
      $select = new Zend_Db_Select($db);
      $select_middle = $select
              ->from('engine4_core_content')
              ->where('parent_content_id = ?', $container_id)
              ->where('page_id = ?', $album_id)
              ->where('type = ?', 'container')
              ->where('name = ?', 'right')
              ->limit(1);
      $middle = $select_middle->query()->fetchAll();
      if (empty($middle)) {
        $db->insert('engine4_core_content', array(
            'page_id' => $album_id,
            'type' => 'container',
            'name' => 'right',
            'parent_content_id' => $container_id,
            'order' => 1,
        ));
        $middle_id = $db->lastInsertId('engine4_core_content');
      } else {
        $middle_id = $middle[0]['content_id'];
      }

      $db->insert('engine4_core_content', array(
          'page_id' => $album_id,
          'type' => 'widget',
          'name' => 'sitetagcheckin.location-suggestions-sitetagcheckin',
          'parent_content_id' => $middle_id,
          'order' => 1,
      ));
    }
  }
}

//CHECK THAT SITEALBUM PLUGIN IS ACTIVATED OR NOT
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_settings')
        ->where('name = ?', 'sitealbum.isActivate')
        ->limit(1);
$sitealbum_settings = $select->query()->fetchAll();
if (!empty($sitealbum_settings)) {
  $sitealbum_is_active = $sitealbum_settings[0]['value'];
} else {
  $sitealbum_is_active = 0;
}

//CHECK THAT SITEALBUM PLUGIN IS INSTALLED OR NOT
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_modules')
        ->where('name = ?', 'sitealbum')
        ->where('enabled = ?', 1);
$check_sitealbum = $select->query()->fetchObject();
if (!empty($check_sitealbum) && !empty($sitealbum_is_active)) {

  //PUT LOCATION WIDGET ON ALBUM VIEW PAGE
  $select = new Zend_Db_Select($db);
  $select_album = $select
          ->from('engine4_core_pages', 'page_id')
          ->where('name = ?', 'sitealbum_album_view')
          ->limit(1);
  $album = $select_album->query()->fetchAll();
  if (!empty($album)) {
    $album_id = $album[0]['page_id'];
    $select = new Zend_Db_Select($db);
    $select_content = $select
            ->from('engine4_core_content')
            ->where('page_id = ?', $album_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitetagcheckin.location-suggestions-sitetagcheckin')
            ->limit(1);
    $content = $select_content->query()->fetchAll();
    if (empty($content)) {
      $select = new Zend_Db_Select($db);
      $select_container = $select
              ->from('engine4_core_content', 'content_id')
              ->where('page_id = ?', $album_id)
              ->where('type = ?', 'container')
              ->limit(1);
      $container = $select_container->query()->fetchAll();
      if (!empty($container)) {
        $container_id = $container[0]['content_id'];
        $select = new Zend_Db_Select($db);
        $select_middle = $select
                ->from('engine4_core_content')
                ->where('parent_content_id = ?', $container_id)
                ->where('type = ?', 'container')
                ->where('name = ?', 'right')
                ->limit(1);
        $middle = $select_middle->query()->fetchAll();
        if (!empty($middle)) {
          $middle_id = $middle[0]['content_id'];
          $db->insert('engine4_core_content', array(
              'page_id' => $album_id,
              'type' => 'widget',
              'name' => 'sitetagcheckin.location-suggestions-sitetagcheckin',
              'parent_content_id' => $middle_id,
              'order' => 1,
              'params' => '{"title":"Add a Location to Your Photos","titleCount":false}',
          ));
        }
      }
    }
  }

  //PUT LOCATION WIDGET ON PHOTO VIEW PAGE
  $select = new Zend_Db_Select($db);
  $select_album = $select
          ->from('engine4_core_pages', 'page_id')
          ->where('name = ?', 'sitealbum_photo_view')
          ->limit(1);
  $album = $select_album->query()->fetchAll();
  if (!empty($album)) {
    $album_id = $album[0]['page_id'];
    $select = new Zend_Db_Select($db);
    $select_content = $select
            ->from('engine4_core_content')
            ->where('page_id = ?', $album_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitetagcheckin.location-suggestions-sitetagcheckin')
            ->limit(1);
    $content = $select_content->query()->fetchAll();
    if (empty($content)) {
      $select = new Zend_Db_Select($db);
      $select_container = $select
              ->from('engine4_core_content', 'content_id')
              ->where('page_id = ?', $album_id)
              ->where('type = ?', 'container')
              ->limit(1);
      $container = $select_container->query()->fetchAll();
      if (!empty($container)) {
        $container_id = $container[0]['content_id'];
        $select = new Zend_Db_Select($db);
        $select_middle = $select
                ->from('engine4_core_content')
                ->where('parent_content_id = ?', $container_id)
                ->where('type = ?', 'container')
                ->where('name = ?', 'right')
                ->limit(1);
        $middle = $select_middle->query()->fetchAll();
        if (!empty($middle)) {
          $middle_id = $middle[0]['content_id'];
          $db->insert('engine4_core_content', array(
              'page_id' => $album_id,
              'type' => 'widget',
              'name' => 'sitetagcheckin.location-suggestions-sitetagcheckin',
              'parent_content_id' => $middle_id,
              'order' => 1,
              'params' => '{"title":"Add a Location to Your Photos","titleCount":false}',
          ));
        }
      }
    }
  }

  //PUT LOCATION WIDGET ON PHOTO VIEW PAGE
  $select = new Zend_Db_Select($db);
  $select_album = $select
          ->from('engine4_core_pages', 'page_id')
          ->where('name = ?', 'sitealbum_index_index')
          ->limit(1);
  $album = $select_album->query()->fetchAll();
  if (!empty($album)) {
    $album_id = $album[0]['page_id'];
    $select = new Zend_Db_Select($db);
    $select_content = $select
            ->from('engine4_core_content')
            ->where('page_id = ?', $album_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitetagcheckin.location-suggestions-sitetagcheckin')
            ->limit(1);
    $content = $select_content->query()->fetchAll();
    if (empty($content)) {
      $select = new Zend_Db_Select($db);
      $select_container = $select
              ->from('engine4_core_content', 'content_id')
              ->where('page_id = ?', $album_id)
              ->where('type = ?', 'container')
              ->where('name = ?', 'main')
              ->limit(1);
      $container = $select_container->query()->fetchAll();
      if (!empty($container)) {
        $container_id = $container[0]['content_id'];
        $select = new Zend_Db_Select($db);
        $select_middle = $select
                ->from('engine4_core_content')
                ->where('parent_content_id = ?', $container_id)
                ->where('type = ?', 'container')
                ->where('name = ?', 'right')
                ->limit(1);
        $middle = $select_middle->query()->fetchAll();
        if (!empty($middle)) {
          $middle_id = $middle[0]['content_id'];
          $db->insert('engine4_core_content', array(
              'page_id' => $album_id,
              'type' => 'widget',
              'name' => 'sitetagcheckin.location-suggestions-sitetagcheckin',
              'parent_content_id' => $middle_id,
              'order' => 1,
              'params' => '{"title":"Add a Location to Your Photos","titleCount":false}',
          ));
        }
      }
    }
  }
}

//CHECK THAT ADVANCED ACTIVITY PLUGIN IS ACTIVATED OR NOT
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_settings')
        ->where('name = ?', 'advancedactivity.isActivate')
        ->limit(1);
$advancedactivity_settings = $select->query()->fetchAll();
if (!empty($advancedactivity_settings)) {
  $advancedactivity_is_active = $advancedactivity_settings[0]['value'];
} else {
  $advancedactivity_is_active = 0;
}

//CHECK THAT ADVANCED ACTIVITY PLUGIN IS INSTALLED OR NOT
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_modules')
        ->where('name = ?', 'advancedactivity')
        ->where('enabled = ?', 1);
$check_advancedactivity = $select->query()->fetchObject();
if (!empty($check_advancedactivity) && !empty($advancedactivity_is_active)) {
  $advancedactivityContentTable = $db->query('SHOW TABLES LIKE \'engine4_advancedactivity_contents\'')->fetch();
  if (!empty($advancedactivityContentTable)) {
    $db->query("INSERT IGNORE INTO `engine4_advancedactivity_contents` (`module_name`, `filter_type`, `resource_title`, `content_tab`, `order`, `default`) VALUES ('sitetagcheckin', 'sitetagcheckin', 'Location', 1, 999, 0)");
  }
}

//START THE WORK FOR MAKE WIDGETIZE PAGE OF Locatio or map.
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_pages')
        ->where('name = ?', 'sitetagcheckin_index_by-locations')
        ->limit(1);
$info = $select->query()->fetch();
if (empty($info)) {
  $db->insert('engine4_core_pages', array(
      'name' => 'sitetagcheckin_index_by-locations',
      'displayname' => 'Browse Events’ Locations',
      'title' => 'Browse Events’ Locations',
      'description' => 'Browse Events’ Locations',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId('engine4_core_pages');

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'top',
      'parent_content_id' => null,
      'order' => 1,
      'params' => '',
  ));
  $top_id = $db->lastInsertId('engine4_core_content');

  //CONTAINERS
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'main',
      'parent_content_id' => Null,
      'order' => 2,
      'params' => '',
  ));
  $container_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'middle',
      'parent_content_id' => $top_id,
      'params' => '',
  ));
  $top_middle_id = $db->lastInsertId('engine4_core_content');

  //INSERT MAIN - MIDDLE CONTAINER
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'middle',
      'parent_content_id' => $container_id,
      'order' => 2,
      'params' => '',
  ));
  $middle_id = $db->lastInsertId('engine4_core_content');

  // Top Middle
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'core.content',
      'parent_content_id' => $top_middle_id,
      'order' => 1,
      'params' => '',
  ));

  //INSERT WIDGET OF LOCATION SEARCH AND CORE CONTENT
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitetagcheckin.location-search',
      'parent_content_id' => $middle_id,
      'order' => 2,
      'params' => '',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitetagcheckin.bylocation-event',
      'parent_content_id' => $middle_id,
      'order' => 3,
      'params' => '{"title":"","titleCount":true,"order_by":"2","nomobile":"0"}',
  ));
}
//END THE WORK FOR MAKE WIDGETIZE PAGE OF LOCATIO OR MAP.
//START THE WORK FOR MAKE WIDGETIZE PAGE OF Locatio or map.
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_pages')
        ->where('name = ?', 'sitetagcheckin_index_mobileby-locations')
        ->limit(1);
$info = $select->query()->fetch();
if (empty($info)) {
  $db->insert('engine4_core_pages', array(
      'name' => 'sitetagcheckin_index_mobileby-locations',
      'displayname' => 'Mobile Browse Events’ Locations',
      'title' => 'Mobile Browse Events’ Locations',
      'description' => 'Mobile Browse Events’ Locations',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId('engine4_core_pages');

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'top',
      'parent_content_id' => null,
      'order' => 1,
      'params' => '',
  ));
  $top_id = $db->lastInsertId('engine4_core_content');

  //CONTAINERS
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'main',
      'parent_content_id' => Null,
      'order' => 2,
      'params' => '',
  ));
  $container_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'middle',
      'parent_content_id' => $top_id,
      'params' => '',
  ));
  $top_middle_id = $db->lastInsertId('engine4_core_content');

  //INSERT MAIN - MIDDLE CONTAINER
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'middle',
      'parent_content_id' => $container_id,
      'order' => 2,
      'params' => '',
  ));
  $middle_id = $db->lastInsertId('engine4_core_content');

  // Top Middle
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'core.content',
      'parent_content_id' => $top_middle_id,
      'order' => 1,
      'params' => '',
  ));

  //INSERT WIDGET OF LOCATION SEARCH AND CORE CONTENT
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitetagcheckin.location-search',
      'parent_content_id' => $middle_id,
      'order' => 2,
      'params' => '',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitetagcheckin.bylocation-event',
      'parent_content_id' => $middle_id,
      'order' => 3,
      'params' => '{"title":"","titleCount":true,"order_by":"2","nomobile":"0"}',
  ));
}
//END THE WORK FOR MAKE WIDGETIZE PAGE OF LOCATIO OR MAP.
//EVENT PROFILE PAGE
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_modules')
        ->where('name = ?', 'event')
        ->where('enabled = ?', 1);
$check_event = $select->query()->fetchObject();
if (!empty($check_event)) {
  $select = new Zend_Db_Select($db);
  $select
          ->from('engine4_core_pages')
          ->where('name = ?', 'event_profile_index')
          ->limit(1);
  $page_id = $select->query()->fetchObject()->page_id;
  if (!empty($page_id)) {
    // container_id (will always be there)
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_content')
            ->where('page_id = ?', $page_id)
            ->where('type = ?', 'container')
            ->where('name = ?', 'main')
            ->limit(1);
    $container_id = $select->query()->fetchObject()->content_id;
    if (!empty($container_id)) {
      // left_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
              ->from('engine4_core_content')
              ->where('parent_content_id = ?', $container_id)
              ->where('type = ?', 'container')
              ->where('name = ?', 'left')
              ->limit(1);
      $left_id = $select->query()->fetchObject()->content_id;
      if (!empty($left_id)) {
        // Check if it's already been placed
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_content')
                ->where('parent_content_id = ?', $left_id)
                ->where('type = ?', 'widget')
                ->where('name = ?', 'sitetagcheckin.syncevents-location');
        $info = $select->query()->fetch();
        if (empty($info)) {
          // tab on profile
          $db->insert('engine4_core_content', array(
              'page_id' => $page_id,
              'type' => 'widget',
              'name' => 'sitetagcheckin.syncevents-location',
              'parent_content_id' => $left_id,
              'order' => 50,
              'params' => '',
          ));
        }
      }
    }
  }
}

//EVENT PROFILE PAGE
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_modules')
        ->where('name = ?', 'ynevent')
        ->where('enabled = ?', 1);
$check_event = $select->query()->fetchObject();
if (!empty($check_event)) {
  $select = new Zend_Db_Select($db);
  $select
          ->from('engine4_core_pages')
          ->where('name = ?', 'ynevent_profile_index')
          ->limit(1);
  $page_id = $select->query()->fetchObject()->page_id;
  if (!empty($page_id)) {
    // container_id (will always be there)
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_content')
            ->where('page_id = ?', $page_id)
            ->where('type = ?', 'container')
            ->where('name = ?', 'main')
            ->limit(1);
    $container_id = $select->query()->fetchObject()->content_id;
    if (!empty($container_id)) {
      // left_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
              ->from('engine4_core_content')
              ->where('parent_content_id = ?', $container_id)
              ->where('type = ?', 'container')
              ->where('name = ?', 'left')
              ->limit(1);
      $left_id = $select->query()->fetchObject()->content_id;
      if (!empty($left_id)) {
        // Check if it's already been placed
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_content')
                ->where('parent_content_id = ?', $left_id)
                ->where('type = ?', 'widget')
                ->where('name = ?', 'sitetagcheckin.syncevents-location');
        $info = $select->query()->fetch();
        if (empty($info)) {
          // tab on profile
          $db->insert('engine4_core_content', array(
              'page_id' => $page_id,
              'type' => 'widget',
              'name' => 'sitetagcheckin.syncevents-location',
              'parent_content_id' => $left_id,
              'order' => 50,
              'params' => '',
          ));
        }
      }
    }
  }
}