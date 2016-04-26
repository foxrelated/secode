<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Facebookse_Installer extends Engine_Package_Installer_Module {

  function onPreInstall() {
    $PRODUCT_TYPE = 'facebookse';
    $PLUGIN_TITLE = 'Facebookse';
    $PLUGIN_VERSION = '4.8.10p1';
    $PLUGIN_CATEGORY = 'plugin';
    $PRODUCT_DESCRIPTION = 'Facebookse Plugin';
    $_PRODUCT_FINAL_FILE = 0;
    $_BASE_FILE_NAME = 0;
    $PRODUCT_TITLE = 'Advanced Facebook Integration / Likes, Social Plugins and Open Graph';
    $SocialEngineAddOns_version = '4.8.5';
    $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
    $is_file = file_exists($file_path);
    if (empty($is_file)) {
      include_once APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/license4.php";
    } else {
      if (!empty($_PRODUCT_FINAL_FILE)) {
        include_once APPLICATION_PATH . '/application/modules/' . $PLUGIN_TITLE . '/controllers/license/' . $_PRODUCT_FINAL_FILE;
      }
      $db = $this->getDb();
      $select = new Zend_Db_Select($db);
      $select->from('engine4_core_modules')->where('name = ?', $PRODUCT_TYPE);
      $is_Mod = $select->query()->fetchObject();
      if (empty($is_Mod)) {
        include_once $file_path;
      }
    }
    parent::onPreInstall();
  }

  function onInstall() {

    $db = $this->getDb();  
    
    $select = new Zend_Db_Select($db);
    $contents = $select
            ->from('engine4_core_content', 'content_id')
            ->where('name = ?','Facebookse.facebookse-groupprofilelike')
            ->orwhere('name = ?','Facebookse.facebookse-eventprofilelike')
            ->orwhere('name = ?','Facebookse.facebookse-sitepageprofilelike')
            ->orwhere('name = ?','Facebookse.facebookse-listprofilelike')
            ->query()
            ->fetchAll();
    
    
    if(!empty($contents)){
      foreach ($contents as $content){
        $db->query("UPDATE  `engine4_core_content` SET  `name` =  'Facebookse.facebookse-commonlike' WHERE  `engine4_core_content`.`content_id` = ".$content['content_id'].";");
      }
    }

    
    //CHECK IF TABLE EXIST OR NOT:
    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_facebookse_mixsettings'")->fetch();
    if (empty($table_exist)) {
      $insertentry = true;
    }
    else {
      $insertentry = false;
    }
    $db->query("CREATE TABLE IF NOT EXISTS `engine4_facebookse_mixsettings` (
  `mixsetting_id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `module_name` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `resource_type` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `resource_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `owner_field` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `module_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `module_description` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `enable` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `send_button` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `like_type` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'like',
  `like_faces` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `like_width` int(10) unsigned NOT NULL DEFAULT '450',
  `like_font` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `like_color` varchar(11) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `layout_style` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'standard',
  `opengraph_enable` tinyint(2) NOT NULL DEFAULT '0',
  `title` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `photo_id` int(10) NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `types` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `fbadmin_appid` tinyint(2) NOT NULL DEFAULT '1',
  `commentbox_enable` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `commentbox_privacy` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `commentbox_width` int(10) unsigned NOT NULL DEFAULT '450',
  `commentbox_color` varchar(11) NOT NULL DEFAULT 'light',
  `module_enable` tinyint(1) NOT NULL DEFAULT '1',
  `default` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `activityfeed_type` varchar(32) NOT NULL,
  `streampublish_message` text NOT NULL,
  `streampublish_story_title` varchar(128) NOT NULL,
  `streampublish_link` varchar(255) NOT NULL,
  `streampublish_caption` varchar(128) NOT NULL,
  `streampublish_description` text NOT NULL,
  `streampublish_action_link_text` varchar(128) NOT NULL,
  `streampublish_action_link_url` varchar(255) NOT NULL,
  `streampublishenable` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `activityfeedtype_text` varchar(255) NOT NULL,
  `action_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'og.likes',
  `object_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'object',
  `like_commentbox` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `fbbutton_liketext` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Like',
  `fbbutton_unliketext` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Unlike',
  `show_customicon` tinyint(1) NOT NULL DEFAULT '1',
  `fbbutton_likeicon` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `fbbutton_unlikeicon` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`mixsetting_id`),
  KEY `module` (`module`, `resource_type`)
) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;");

    $select = new Zend_Db_Select($db);
    $module_fb = $select
            ->from('engine4_core_modules', 'name')
            ->where('name = ?', 'facebookse')
            ->query()
            ->fetchColumn();

    if ($insertentry) {
      $this->insertEntry();
    }



    $db->query("CREATE TABLE IF NOT EXISTS `engine4_facebookse_statistics` ( 
  `statistic_id` int(11) unsigned NOT NULL auto_increment, 
  `url` text NOT NULL, 
  `updated` timestamp NOT NULL default '0000-00-00 00:00:00' on update CURRENT_TIMESTAMP, 
  `url_scrape` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0',
  `url_type` varchar(50) NOT NULL,
  `content_id` int(11) unsigned NOT NULL,
  `resource_type` varchar(50) NOT NULL,  
PRIMARY KEY (`statistic_id`),
KEY `content_id` (`content_id`,`resource_type`) ) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_unicode_ci;");

    $column_exist = $db->query('SHOW COLUMNS FROM engine4_facebookse_statistics LIKE \'url_scrape\'')->fetch();
    if (empty($column_exist)) {
      $db->query('ALTER TABLE `engine4_facebookse_statistics` ADD `url_scrape` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT \'0\';');
    }  
    

    $column_exist = $db->query('SHOW COLUMNS FROM engine4_facebookse_statistics LIKE \'url_type\'')->fetch();
    if (empty($column_exist)) {
      $db->query('ALTER TABLE `engine4_facebookse_statistics` ADD `url_type` varchar(50) NOT NULL;');
    }

    $select = new Zend_Db_Select($db);
    $version = $select
            ->from('engine4_core_modules', 'version')
            ->where('name = ?', 'facebookse')
            ->where('version <= ?', '4.2.3')
            ->query()
            ->fetchObject();

    if (!empty($version)) {

      $select = new Zend_Db_Select($db);
      //for facebook like table entry in the mix settings table.
      $facebookse_likes_exist = $db->query("SHOW TABLES LIKE 'engine4_facebookse_likes'")->fetch();
      if (!empty($facebookse_likes_exist)) {
        $column_exist = $db->query('SHOW COLUMNS FROM engine4_facebookse_likes LIKE \'like_width\'')->fetch();
        if (empty($column_exist)) {
          $db->query('ALTER TABLE `engine4_facebookse_likes` ADD `like_width` INT( 10 ) UNSIGNED NOT NULL DEFAULT \'450\' AFTER `like_faces`;');
        }        
        //check if send_button column exist in facebook like table:
        $column_exist = $db->query('SHOW COLUMNS FROM engine4_facebookse_likes LIKE \'send_button\'')->fetch();
        if (empty($column_exist)) {
          $db->query('ALTER TABLE `engine4_facebookse_likes` ADD `send_button` tinyint(1) unsigned NOT NULL DEFAULT 1 AFTER `like_faces`;');
        }
        $facebookse_likes = $db->query("SELECT * FROM `engine4_facebookse_likes`")->fetchAll();
        foreach ($facebookse_likes as $likesetting) {
          if ($likesetting['content_type'] == 'user_profile') {
            $likesetting['content_type'] = 'user';
          }
          if ($likesetting['content_type'] == 'site_homepage') {
            $likesetting['content_type'] = 'home';
          }

          $db->query('UPDATE `engine4_facebookse_mixsettings` SET `enable` = "' . $likesetting['enable'] . '",
					`send_button` = "' . $likesetting['send_button'] . '", `like_type` = "' . $likesetting['like_type'] . '", `like_faces` = "' . $likesetting['like_faces'] . '", `like_width` = "' . $likesetting['like_width'] . '" , `like_font` = "' . $likesetting['like_font'] . '" , `like_color` = "' . $likesetting['like_color'] . '" , `layout_style` = "' . $likesetting['layout_style'] . '" WHERE `engine4_facebookse_mixsettings`.`module` = "' . $likesetting['content_type'] . '" ;');
        }

        //$db->query('DROP TABLE `engine4_facebookse_likes`;');
      }
      $select = new Zend_Db_Select($db);
      //for metainfos table entry in the mixsettings table.
      $fbmetainfo_table_existt = $db->query("SHOW TABLES LIKE 'engine4_facebookse_metainfos'")->fetch();
      if (!empty($fbmetainfo_table_existt)) {
        $fbmetainfo_table = $db->query("SELECT * FROM `engine4_facebookse_metainfos`")->fetchAll();
        foreach ($fbmetainfo_table as $metainfo) {
          if ($metainfo['entity'] == 'profile') {
            $metainfo['entity'] = 'user';
          }
          if ($metainfo['entity'] == 'Home') {
            $metainfo['entity'] = 'home';
          }
          $db->query('UPDATE `engine4_facebookse_mixsettings` SET `opengraph_enable` = "' . $metainfo['enable'] . '", `title` = "' . $metainfo['title'] . '", `photo_id` = "' . $metainfo['photo_id'] . '", `description` = "' . $metainfo['description'] . '" , `fbadmin_appid` = "' . $metainfo['fbadmin_appid'] . '" WHERE `engine4_facebookse_mixsettings`.`module` = "' . $metainfo['entity'] . '" ;');
          $db->query('UPDATE `engine4_facebookse_mixsettings` SET `types` = \'' . $metainfo['types'] . '\' WHERE `engine4_facebookse_mixsettings`.`module` = "' . $metainfo['entity'] . '";');
        }

        //$db->query('DROP TABLE IF EXISTS `engine4_facebookse_metainfos`;');
      }

      $select = new Zend_Db_Select($db);
      //for metainfos table entry in the mixsettings table.
      $fbcomments_table_existt = $db->query("SHOW TABLES LIKE 'engine4_facebookse_comments'")->fetch();
      if (!empty($fbcomments_table_existt)) {
        $fbcomments_table = $db->query("SELECT * FROM `engine4_facebookse_comments`")->fetchAll();
        foreach ($fbcomments_table as $fbcomment) {

          $db->query('UPDATE `engine4_facebookse_mixsettings` SET `commentbox_enable` = "' . $fbcomment['enable'] . '", `commentbox_privacy` = "' . $fbcomment['commentbox_privacy'] . '", `commentbox_width` = "' . $fbcomment['commentbox_width'] . '", `commentbox_color` = "' . $fbcomment['commentbox_color'] . '" WHERE `engine4_facebookse_mixsettings`.`module` = "' . $fbcomment['content_type'] . '" ;');
        }

        //$db->query('DROP TABLE IF EXISTS `engine4_facebookse_comments`;');
      }

      //ADDING THE COLUMN FOR SITEBUSINESS PLUGIN IF IT DOES NOTE THERE.
      $table_exist = $db->query('SHOW TABLES LIKE \'engine4_facebookse_feedsettings\'')->fetch();
      if (!empty($table_exist)) {
        $column_exist = $db->query('SHOW COLUMNS FROM engine4_facebookse_feedsettings LIKE \'feedpublish_types\'')->fetch();
        if (empty($column_exist)) {
          $db->query('ALTER TABLE `engine4_facebookse_feedsettings` ADD `feedpublish_types` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;');
        }
        //FETCHING ALL USERS FEED SETTING INFO AND PUT ALL COLUMNS INFO IN TO ONE COLUMN FEEDPUBLISH_TYPE COLOMN.
        $select = new Zend_Db_Select($db);
        //for metainfos table entry in the mixsettings table.
        $fbcomments_table = $db->query("SELECT * FROM `engine4_facebookse_feedsettings`")->fetchAll();
        foreach ($fbcomments_table as $fbfeedsetting) {
          $feedsetting_id = $fbfeedsetting['feedsetting_id'];
          unset($fbfeedsetting['feedsetting_id']);
          unset($fbfeedsetting['user_id']);
          unset($fbfeedsetting['feedpublish_types']);
          $db->query("UPDATE `engine4_facebookse_feedsettings` SET `feedpublish_types` = '" . serialize($fbfeedsetting) . "' WHERE `engine4_facebookse_feedsettings`.`feedsetting_id` = '" . $feedsetting_id . "' ;");
        }
      }
    }



    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'facebookse')
            ->where('version < ?', '4.1.6p2');
    $is_enabled = $select->query()->fetchObject();

    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_pages')
            ->where('name = ?', 'recipe_index_view')
            ->limit(1);
    $page = $select->query()->fetchObject();
    if (!empty($page) && !empty($is_enabled)) {
      $page_id = $page->page_id;

      // Check if it's already been placed
      $select = new Zend_Db_Select($db);
      $select
              ->from('engine4_core_content')
              ->where('page_id = ?', $page_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', 'Facebookse.facebookse-recipeprofilelike')
      ;
      $info = $select->query()->fetch();

      if (empty($info)) {

        // container_id (will always be there)
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_content')
                ->where('page_id = ?', $page_id)
                ->where('type = ?', 'container')
                ->where('name = ?', 'main')
                ->limit(1);
        $container = $select->query()->fetchObject();
        if (!empty($container)) {
          $container_id = $container->content_id;

          // middle_id (will always be there)
          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_content')
                  ->where('parent_content_id = ?', $container_id)
                  ->where('type = ?', 'container')
                  ->where('name = ?', 'middle')
                  ->limit(1);
          $middle = $select->query()->fetchObject();
          if (!empty($middle)) {
            $middle_id = $middle->content_id;

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'Facebookse.facebookse-recipeprofilelike',
                'parent_content_id' => $middle_id,
                'order' => 1,
                'params' => '{"title":"","titleCount":true}',
            ));
          }
        }
      }
    }


    //MAKING A COLOMN IN THE INVITE TABLE

    $type_array = $db->query("SHOW COLUMNS FROM `engine4_invites` LIKE 'social_profileid'")->fetch();
    if (empty($type_array)) {
      $run_query = $db->query("ALTER TABLE `engine4_invites` ADD `social_profileid` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL AFTER `new_user_id` ");
    }



    //UPDATING facebookse_widgetsettings table for the "Like Box" of "fb_height" field.
    $table_exist = $db->query('SHOW TABLES LIKE \'engine4_facebookse_widgetsettings\'')->fetch();
    if (!empty($table_exist)) {
      //UPDATING facebookse_widgetsettings table for the "Like Box" of "fb_height" field.
      $db->query("UPDATE  `engine4_facebookse_widgetsettings` SET  `fb_height` =  '588' WHERE  `engine4_facebookse_widgetsettings`.`widget_type` = 'likebox';");
    }
    $db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` = 'facebookse';");
    $facebookse_time = time();
    $db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
			('facebookse.base.time', $facebookse_time ),
			('facebookse.checkset.var', 0 ),  
			('facebookse.get.pathinfo', 'Facebookse/controllers/license/license2.php');");

    //REMOVING THE OLD COLOMNS FROM FACEBOOKSE_FEEDSETTINGS TABLE AND ADDING ONE NEW COLOMN.
    $db->query('UPDATE `engine4_facebookse_mixsettings` SET `owner_field` = "user_id" WHERE `engine4_facebookse_mixsettings`.`module` = "sitepagealbum" AND `engine4_facebookse_mixsettings`.`resource_type` = "sitepage_photo";');

    $db->query('UPDATE `engine4_facebookse_mixsettings` SET `owner_field` = "user_id" WHERE `engine4_facebookse_mixsettings`.`module` = "sitebusinessalbum" AND `engine4_facebookse_mixsettings`.`resource_type` = "sitebusiness_photo";');

    $db->query("ALTER TABLE `engine4_facebookse_mixsettings` CHANGE `module` `module` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
    
    //CHECK IF THE ACTION TYPE COLUMN EXIST IN MIX SETTINGS TABLE
     $column_exist = $db->query('SHOW COLUMNS FROM engine4_facebookse_mixsettings LIKE \'action_type\'')->fetch();
    if (empty($column_exist)) {
      $db->query('ALTER TABLE `engine4_facebookse_mixsettings` ADD `action_type` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT \'og.likes\'');
    }
    
    //CHECK IF THE OBJECT TYPE COLUMN EXIST IN MIX SETTINGS TABLE
     $column_exist = $db->query('SHOW COLUMNS FROM engine4_facebookse_mixsettings LIKE \'object_type\'')->fetch();
    if (empty($column_exist)) {
      $db->query('ALTER TABLE `engine4_facebookse_mixsettings` ADD `object_type` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT \'object\'');
    }
    
     //CHECK IF THE LIKE_COMMENTBOX COLUMN EXIST IN MIX SETTINGS TABLE
     $column_exist = $db->query('SHOW COLUMNS FROM engine4_facebookse_mixsettings LIKE \'like_commentbox\'')->fetch();
    if (empty($column_exist)) {
      $db->query('ALTER TABLE `engine4_facebookse_mixsettings` ADD `like_commentbox` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT \'1\'');
    }
    
    //CHECK IF THE ACTION TEXT COLUMN EXIST IN MIX SETTINGS TABLE
     $column_exist = $db->query('SHOW COLUMNS FROM engine4_facebookse_mixsettings LIKE \'fbbutton_liketext\'')->fetch();
    if (empty($column_exist)) {
      $db->query('ALTER TABLE `engine4_facebookse_mixsettings` ADD `fbbutton_liketext` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT \'Like\'');
    }
    
     //CHECK IF THE ACTION TEXT COLUMN EXIST IN MIX SETTINGS TABLE
     $column_exist = $db->query('SHOW COLUMNS FROM engine4_facebookse_mixsettings LIKE \'fbbutton_unliketext\'')->fetch();
    if (empty($column_exist)) {
      $db->query('ALTER TABLE `engine4_facebookse_mixsettings` ADD `fbbutton_unliketext` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT \'Unlike\'');
    }

    //INSERT THE ENTRY FOR OUR Siteestore PLUGIN IF THEY ARE UPGRADING THE FACEBOOK PLUGIN WHO HAS VERSION BELOW 4.2.7P1

    $version = $this->getCurrentVersion('4.2.7', $db);
    if (!empty($version)) {
      $db->query("INSERT IGNORE INTO `engine4_facebookse_mixsettings` (`module`, `resource_type`, `resource_id`, `owner_field`, `module_title`, `module_description`, `enable`, `send_button`, `like_type`, `like_faces`, `like_width`, `like_font`, `like_color`, `layout_style`, `opengraph_enable`, `title`, `photo_id`, `description`, `types`, `fbadmin_appid`, `commentbox_enable`, `commentbox_privacy`, `commentbox_width`, `commentbox_color`, `module_enable`, `default`, `activityfeed_type`, `streampublish_message`, `streampublish_story_title`, `streampublish_link`, `streampublish_caption`, `streampublish_description`, `streampublish_action_link_text`, `streampublish_action_link_url`, `streampublishenable`, `activityfeedtype_text`) VALUES ('siteestore', 'siteestore_product', 'product_id', '', 'title', 'description', 1, 1, 'like', 0, 450, '', '', 'standard', 0, '', 0, '', '', 1, 0, 0, 450, 'light', 1, 1, '', '', '', '', '', '', '', '', 0, '');");
    }
    
	$fb_comment_id = $db->query("SHOW COLUMNS FROM engine4_core_comments LIKE 'fb_comment_id'")->fetch();
	if (empty($fb_comment_id)) {
		$db->query("ALTER TABLE `engine4_core_comments` ADD `fb_comment_id` BIGINT UNSIGNED NOT NULL");
	}

	$content_id = $db->query("SHOW COLUMNS FROM engine4_facebookse_statistics LIKE 'content_id'")->fetch();
	if (empty($content_id)) {		
		$db->query("ALTER TABLE `engine4_facebookse_statistics` ADD `content_id` INT( 11 ) NOT NULL, ADD `resource_type` VARCHAR( 50 ) NOT NULL");
		$db->query("ALTER TABLE `engine4_facebookse_statistics` ADD INDEX (`content_id` ,`resource_type`)");
	}

    //UPDATING THE FACEBOOK MIXSETTING TABLE WITH THE DEFAULT MODULE TITLES.
    $version = $this->getCurrentVersion('4.2.7p1', $db);
    if (!empty($version)) {

      //ADD A MODULE TITLE COLOMN IN MIXSETTNG TABLE
      $column_exist = $db->query('SHOW COLUMNS FROM engine4_facebookse_mixsettings LIKE \'module_name\'')->fetch();
    if (empty($column_exist)) {
      $run_query = $db->query("ALTER TABLE `engine4_facebookse_mixsettings` ADD `module_name` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL AFTER `module`");     
      $run_query = $db->query("ALTER TABLE `engine4_facebookse_mixsettings` DROP INDEX `module`");
      $run_query = $db->query("ALTER TABLE `engine4_facebookse_mixsettings` ADD INDEX ( `resource_type` )");
      
      
    }
      $select = new Zend_Db_Select($db);
      $modules_Core = $db->query("SELECT title, name FROM `engine4_core_modules`")->fetchAll();
      foreach ($modules_Core as $mod) {
        $module_core[$mod['name']] = $mod['title'];
      }
      $modules_Mixsettings = $db->query("SELECT module, module_name, resource_type FROM `engine4_facebookse_mixsettings`")->fetchAll();

      foreach ($modules_Mixsettings as $module) {

        switch ($module['resource_type']) {
          case 'album_photo':
            $module_title = 'Albums Photo';
            break;
          case 'forum_topic':
            $module_title = 'Forum Topic Creation';
            break;
          case 'list_photo':
            $module_title = 'Listings Photo';
            break;
          case 'recipe_photo':
            $module_title = 'Recipes Photo';
            break;
          case 'sitepagenote_photo':
            $module_title = 'Directory / Pages - Notes Photo';
            break;
          case 'sitebusinessnote_photo':
            $module_title = 'Directory / Business - Notes Photo';
            break;
          case 'sitegroupnote_photo':
            $module_title = 'Groups / Communities - Notes Photo';
            break;
          case 'user':
            $module_title = 'Member Profile';
            break;

          case 'home':
            $module_title = 'Site Homepage';
            break;
          default:
            $module_title = $module_core[$module['module']];
        }

        if (!empty($module['resource_type']))
          $db->query('UPDATE `engine4_facebookse_mixsettings` SET `module_name` = "' . $module_title . '" WHERE `engine4_facebookse_mixsettings`.`resource_type` = "' . $module['resource_type'] . '";');
        else
          $db->query('UPDATE `engine4_facebookse_mixsettings` SET `module_name` = "' . $module_title . '" WHERE `engine4_facebookse_mixsettings`.`module` = "' . $module['module'] . '" AND `engine4_facebookse_mixsettings`.`resource_type` = "" ;');
      }
      
      $db->query('UPDATE `engine4_facebookse_mixsettings` SET `streampublish_story_title` = "{*sitepagereview_title*}" WHERE `engine4_facebookse_mixsettings`.`module` = "sitepagereview" AND `engine4_facebookse_mixsettings`.`resource_type` = "sitepagereview_review" ;');
    }

    if (empty($module_fb) || !empty($version)) :
      //CHECK IF THE SITE REVIEW PLUGIN IS ALREADY INSTALLED:
      $select = new Zend_Db_Select($db);
      $select
              ->from('engine4_core_modules')
              ->where('name = ?', 'sitereview');
      $is_enabled = $select->query()->fetchObject();

      if (!empty($is_enabled)) {
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_sitereview_listingtypes', array('listingtype_id', 'title_singular'));
        $listingTypes = $select->query()->fetchAll();
        foreach ($listingTypes as $listingType) {
          $this->insertListingTypes($listingType, $db);
        }
      }
    endif;

    //INSERTING THE THIRDPARTY MODULE ENTRIES:
    $ThirdPartyModules = array('ultimatenews', 'advgroup', 'ynvideo', 'advancedarticles', 'mp3music', 'page', 'ynblog');
    foreach ($ThirdPartyModules as $module) {
      $this->addThirdpartyModule($module, $db);
    }
    
   
    //INSERT THE DEFALUT MODULES ENTRY WHICH WERE BUILD AFTER THIS PLUGIN RELEASE
    $version = $this->getCurrentVersion('4.6.0p2', $db);
    if (!empty($version)) {
      $defaultModules = array('sitestore_store', 'sitestoreproduct_product', 'siteevent_event', 'siteeventdocument_document');
      foreach ($defaultModules as $module) {
        $this->addDefaultModule($module, $db);
      }
    }

    $mixsettingTable = $db->query('SHOW TABLES LIKE \'engine4_facebookse_mixsettings\'')->fetch();
    if (!empty($mixsettingTable)) {
      $resourcetypeIndex = $db->query("SHOW INDEX FROM `engine4_facebookse_mixsettings` WHERE Key_name = 'resource_type'")->fetch();
      if (!empty($resourcetypeIndex)) {
        $db->query("ALTER TABLE `engine4_facebookse_mixsettings` DROP INDEX `resource_type`");
      }

      $moduleIndex = $db->query("SHOW INDEX FROM `engine4_facebookse_mixsettings` WHERE Key_name = 'module'")->fetch();
      if (empty($moduleIndex)) {
        $db->query("ALTER TABLE `engine4_facebookse_mixsettings` ADD INDEX(`module`, `resource_type`)");
      }

      //ADD SOME COLOUMN IN MIXSETTING TABLE
      //CHECK IF THE ACTION TEXT COLUMN EXIST IN MIX SETTINGS TABLE
      $column_exist = $db->query('SHOW COLUMNS FROM engine4_facebookse_mixsettings LIKE \'show_customicon\'')->fetch();
      if (empty($column_exist)) {
        $db->query('ALTER TABLE `engine4_facebookse_mixsettings` ADD `show_customicon` tinyint(1) NOT NULL DEFAULT \'1\'');
      }
      //CHECK IF THE ACTION TEXT COLUMN EXIST IN MIX SETTINGS TABLE
      $column_exist = $db->query('SHOW COLUMNS FROM engine4_facebookse_mixsettings LIKE \'fbbutton_likeicon\'')->fetch();
      if (empty($column_exist)) {
        $db->query('ALTER TABLE `engine4_facebookse_mixsettings` ADD `fbbutton_likeicon` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL');
      }
      //CHECK IF THE ACTION TEXT COLUMN EXIST IN MIX SETTINGS TABLE
      $column_exist = $db->query('SHOW COLUMNS FROM engine4_facebookse_mixsettings LIKE \'fbbutton_unlikeicon\'')->fetch();
      if (empty($column_exist)) {
        $db->query('ALTER TABLE `engine4_facebookse_mixsettings` ADD `fbbutton_unlikeicon` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL');
      }
    }

    $core_facebook_details = $db->query("SELECT `value` FROM `engine4_core_settings` WHERE `name` = 'core.facebook.appid' LIMIT 1")->fetchColumn();
 
    if(empty($core_facebook_details)){ 
      $db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ('facebookse.app.created.after', true);");
    }

    include_once APPLICATION_PATH . '/application/modules/Facebookse/controllers/license/license3.php';
    parent::onInstall();
  }

  function onEnable() {

    //.....................................................

    $db = $this->getDb();
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_menuitems')
            ->where('name = ?', 'facebookse_friend_home');
    $check_intro_temp = $select->query()->fetchObject();
    if (!empty($check_intro_temp)) {
      $db->update('engine4_core_menuitems', array(
          'name' => 'facebooksepage_friend_home'), array('name =?' => 'facebookse_friend_home'));
    }

    //.......................................................
    $db = $this->getDb();
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_menuitems')
            ->where('module =?', 'facebooksepage')
            ->where('name = ?', 'facebooksepage_friend_home');
    $check_intro = $select->query()->fetchObject();
    if (empty($check_intro)) {
      $db->update('engine4_core_menuitems', array(
          'module' => 'facebookse', 'plugin' => 'Facebookse_Plugin_Menus', 'params' => '{"route":"facebookse_index_settings", "icon":"' . $this->view->layout()->staticBaseUrl . 'application/modules/Facebookse/externals/images/facebookse.png"}'
              ), array('name =?' => 'facebooksepage_friend_home'));
    }
    parent::onEnable();
  }

  function onDisable() {

    $db = $this->getDb();
    $select = new Zend_Db_Select($db);

    //CHECKING IF ANY OF IT'S DEPENDENT FACEBOOK PLUGIN IS ENABLED THEN FIRST THOSE PLUGIN MUST HAVE TO BE DISABLED.
    $select
            ->from('engine4_core_modules', array('name'))
            ->where('enabled = ?', 1);
    $moduleData = $select->query()->fetchAll();

    $subModuleArray = array("facebooksefeed", "facebooksepage");

    foreach ($moduleData as $key => $moduleName) {
      if (in_array($moduleName['name'], $subModuleArray)) {
        $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
        $error_msg1 = Zend_Registry::get('Zend_Translate')->_('Note: Please disable all the integrated sub-modules of Advanced Facebook Plugin before disabling the Advanced Facebook Plugin itself.');
        echo "<div style='background-color: #E9F4FA;border-radius:7px 7px 7px 7px;float:left;overflow: hidden;padding:10px;'><div style='background:#FFFFFF;border:1px solid #D7E8F1;overflow:hidden;padding:20px;'><span style='color:red'>$error_msg1</span><br/> <a href='" . $base_url . "/manage'>Click here</a> to go Manage Packages.</div></div>";
        die;
      }
    }


    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_menuitems')
            ->where('name = ?', 'facebooksepage_friend_home');
    $check_intro = $select->query()->fetchObject();

    if (!empty($check_intro)) {
      $db->update('engine4_core_menuitems', array(
          'module' => 'facebooksepage', 'plugin' => 'Facebooksepage_Plugin_Menus', 'params' => '{"route":"friends_facebooksepage_viewall", "icon":"application/modules/Facebooksepage/externals/images/facebookse.png"}'
              ), array('name =?' => 'facebooksepage_friend_home'));
    }
    parent::onDisable();
  }

  //GETTING THE CURRENT VERSION.

  public function getCurrentVersion($maxVersion, $db) {

    $select = new Zend_Db_Select($db);
    $version = $select
            ->from('engine4_core_modules', 'version')
            ->where('name = ?', 'facebookse')
            ->where('version <= ?', $maxVersion)
            ->query()
            ->fetchObject();
    return $version;
  }

  //THIS IS THE SPECIAL CASE FOR SITE REVIEW PLUGIN. IF SITE REIVEW PLUGIN IS INSTALLED ALREADY THEN INSERT THE LISTING TYPE ENTRY IN OUR PLUGIN ALSO.

  function insertListingTypes($listType, $db) {

    //BEGIN TRANSACTION  
    $listingtype_id = $listType['listingtype_id'];
    $listingtype_title = 'Multiple Listing Types - ' . str_replace('"', '\"', $listType['title_singular']);
    
    //CHECK IF THIS LISTING TYPE ENTRY IS ALREADY THERE OR NOT:
    $select = new Zend_Db_Select($db);
    $row = $select
            ->from('engine4_facebookse_mixsettings', 'mixsetting_id')            
            ->where('resource_type = ?', 'sitereview_listing_' . $listingtype_id)
            ->query()
            ->fetchColumn();
    
    if (empty($row))
      $db->query("INSERT IGNORE INTO `engine4_facebookse_mixsettings` (`module`, `module_name`, `resource_type`, `resource_id`, `owner_field`, `module_title`, `module_description`, `enable`, `send_button`, `like_type`, `like_faces`, `like_width`, `like_font`, `like_color`, `layout_style`, `opengraph_enable`, `title`, `photo_id`, `description`, `types`, `fbadmin_appid`, `commentbox_enable`, `commentbox_privacy`, `commentbox_width`, `commentbox_color`, `module_enable`, `default`, `activityfeed_type`, `streampublish_message`, `streampublish_story_title`, `streampublish_link`, `streampublish_caption`, `streampublish_description`, `streampublish_action_link_text`, `streampublish_action_link_url`, `streampublishenable`, `activityfeedtype_text`) VALUES ('sitereview', '$listingtype_title', 'sitereview_listing_$listingtype_id', 'listing_id', 'owner_id', 'title', 'body', 1, 1, 'like', 1, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 0, 1, 450, 'light', 1, 1, 'sitereview_new_listtype_$listingtype_id', 'View my 
$listingtype_title!', '{*sitereview_title*}', '{*sitereview_url*}',  '{*actor*} posted a new $listingtype_title on {*site_title*}: {*site_url*}.', '{*sitereview_desc*}', 'View $listingtype_title', '{*sitereview_url*}', 1, 'Posting a new $listingtype_title')");
  }

  function insertEntry() {

    $db = $this->getDb();
    $db->query("INSERT IGNORE INTO `engine4_facebookse_mixsettings` (`module`, `module_name`, `resource_type`, `resource_id`, `owner_field`, `module_title`, `module_description`, `enable`, `send_button`, `like_type`, `like_faces`, `like_width`, `like_font`, `like_color`, `layout_style`, `opengraph_enable`, `title`, `photo_id`, `description`, `types`, `fbadmin_appid`, `commentbox_enable`, `commentbox_privacy`, `commentbox_width`, `commentbox_color`, `module_enable`, `default`, `activityfeed_type`, `streampublish_message`, `streampublish_story_title`, `streampublish_link`, `streampublish_caption`, `streampublish_description`, `streampublish_action_link_text`, `streampublish_action_link_url`, `streampublishenable`, `activityfeedtype_text`) VALUES
('album', 'Albums', 'album', 'album_id', 'owner_id', 'title', 'description', 1, 1, 'like', 1, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'album_photo_new', 'View my Album!', '{*album_title*}', '{*album_url*}', '{*actor*} created an Album on {*site_title*}: {*site_url*}.', '{*album_desc*}', 'View Album', '{*album_url*}', 1, 'Creating an Album / Add Photo'),
('blog', 'Blogs' ,'blog', 'blog_id', 'owner_id', 'title', 'body', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'blog_new', 'Read my Blog!', '{*blog_title*}', '{*blog_url*}', '{*actor*} posted a Blog entry on {*site_title*}: {*site_url*}.', '{*blog_desc*}', 'View Blog', '{*blog_url*}', 1, 'Creating a Blog-post'),
('classified', 'Classifieds' ,'classified', 'classified_id', 'owner_id', 'title', 'body', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'classified_new', 'View my Classified!', '{*classified_title*}', '{*classified_url*}', '{*actor*} created a Classified on {*site_title*}: {*site_url*}.', '{*classified_desc*}', 'View Classified', '{*classified_url*}', 1, 'Creating a Classified'),
('event', 'Events', 'event', 'event_id', 'user_id', 'title', 'description', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'event_create', 'Attend my Event!', '{*event_title*}', '{*event_url*}', '{*actor*} created an Event on {*site_title*}: {*site_url*}.', '{*event_desc*}', 'RSVP', '{*event_url*}', 1, 'Creating an Event'),
('group', 'Groups', 'group', 'group_id', 'user_id', 'title', 'description', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'group_create', 'Join my Group!', '{*group_title*}', '{*group_url*}', '{*actor*} created a Group on {*site_title*}: {*site_url*}.', '{*group_desc*}', 'Join Group', '{*browse_group_url*}', 1, 'Creating a Group'),
('forum', 'Forum Topic Creation', 'forum_topic', 'topic_id', 'user_id', 'title', 'description', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'forum_topic_create', 'Join me for discussion!', '{*forumtopic_title*}', '{*forumtopic_url*}', '{*actor*} started a discussion on {*site_title*}: {*site_url*}.', '{*forumtopic_desc*}', 'View Discussion', '{*forumtopic_url*}', 1, 'Creating a Forum Topic'),
('music', 'Music', 'music_playlist', 'playlist_id', 'owner_id', 'title', 'description', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'music_playlist_new', 'Listen to my Music!', '{*music_title*}', '{*music_url*}', '{*actor*} created a Music Playlist on {*site_title*}: {*site_url*}.', '{*music_desc*}', 'Listen to Music', '{*music_url*}', 1, 'Creating a Music Playlist'),
('video', 'Videos', 'video', 'video_id', 'owner_id', 'title', 'description', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'video_new', 'Watch my Video!', '{*video_title*}', '{*video_url*}', '{*actor*} added a Video on {*site_title*}: {*site_url*}.', '{*video_desc*}', 'Watch Video', '{*video_url*}', 1, 'Creating a Video'),
('poll', 'Polls', 'poll', 'poll_id', 'user_id', 'title', 'description', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'poll_new', 'Vote on my Poll!', '{*poll_title*}', '{*poll_url*}', '{*actor*} created a Poll on {*site_title*}: {*site_url*}.', '{*poll_desc*}', 'Vote on Poll', '{*poll_url*}', 1, 'Creating a Poll'),
('list', 'Listing', 'list_listing', 'listing_id', 'owner_id', 'title', 'body', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'list_new', 'View my Listing!', '{*listing_title*}', '{*listing_url*}', '{*actor*} created a Listing on {*site_title*}: {*site_url*}.', '{*listing_desc*}', 'View Listing', '{*listing_url*}', 1, 'Creating a Listing / Catalog item'),
('recipe','Recipes', 'recipe', 'recipe_id', 'owner_id', 'title', 'body', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'recipe_new', 'View my Recipe!', '{*recipe_title*}', '{*recipe_url*}', '{*actor*} created a new Recipe on {*site_title*}: {*site_url*}.', '{*recipe_desc*}', 'View Recipe', '{*browse_recipe_url*}', 1, 'Creating a Recipe'),
('sitepage', 'Directory / Pages', 'sitepage_page', 'page_id', 'owner_id', 'title', 'body', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'sitepage_new', 'Join my Page!', '{*sitepage_title*}', '{*sitepage_url*}', '{*actor*} created a new Page  on {*site_title*}: {*site_url*}.', '{*sitepage_desc*}', 'View Page', '{*sitepage_url*}', 1, 'Creating a Directory Item / Page'),
('sitepagenote', 'Directory / Pages - Notes Extension', 'sitepagenote_note', 'note_id', 'owner_id', 'title', 'body', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'sitepagenote_new', 'Read my Page Note!', '{*sitepagenote_title*}', '{*sitepagenote_url*}', '{*actor*} created a new Page Note on {*site_title*}: {*site_url*}.', '{*sitepagenote_desc*}', 'View Note', '{*sitepagenote_url*}', 1, 'Creating a Page Note'),
('sitepagevideo','Directory / Pages - Videos Extension', 'sitepagevideo_video', 'video_id', 'owner_id', 'title', 'description', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'sitepagevideo_new', 'Watch my Page Video!', '{*sitepagevideo_title*}', '{*sitepagevideo_url*}', '{*actor*} added a new Page Video on {*site_title*}: {*site_url*}.', '{*sitepagevideo_desc*}', 'Watch Video', '{*sitepagevideo_url*}', 1, 'Creating a Page Video'),
('sitepagepoll', 'Directory / Pages - Polls Extension', 'sitepagepoll_poll', 'poll_id', 'owner_id', 'title', 'description', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'sitepagepoll_new', 'Vote on my Page Poll!', '{*sitepagepoll_title*}', '{*sitepagepoll_url*}', '{*actor*} created a new Page Poll on {*site_title*}: {*site_url*}.', '{*sitepagepoll_desc*}', 'Vote on Poll', '{*sitepagepoll_url*}', 1, 'Creating a Page Poll'),
('sitepagereview', 'Directory / Pages - Reviews and Ratings Extension', 'sitepagereview_review', 'review_id', 'owner_id', 'title', 'body', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'sitepagereview_new', 'Read my Page Review', '{*sitepagereview_title*}', '{*sitepagereview_url*}', '{*actor*} posted a Review on a Page on {*site_title*}: {*site_url*}.', '{*sitepagereview_desc*}', 'View Review', '{*sitepagereview_url*}', 1, 'Creating a Page Review'),
('sitepagedocument', 'Directory / Pages - Documents Extension', 'sitepagedocument_document', 'document_id', 'owner_id', 'sitepagedocument_title', 'sitepagedocument_description', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, '', '', '', '', '', '', '', '', 0, ''),
('sitepageevent', 'Directory / Pages - Events Extension', 'sitepageevent_event', 'event_id', 'user_id', 'title', 'description', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'sitepageevent_new', 'Attend my Page Event!', '{*sitepageevent_title*}', '{*sitepageevent_url*}', '{*actor*} created a new Page Event on {*site_title*}: {*site_url*}.', '{*sitepageevent_desc*}', 'RSVP', '{*sitepageevent_url*}', 1, 'Creating a Page Event'),
('sitepagemusic', 'Directory / Pages - Music Extension', 'sitepagemusic_playlist', 'playlist_id', 'owner_id', 'title', 'description', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'sitepagemusic_playlist_new', 'Listen to my Page Music!', '{*sitepagemusic_title*}', '{*sitepagemusic_url*}', '{*actor*} uploaded new Page Music on {*site_title*}: {*site_url*}.', '{*sitepagemusic_desc*}', 'Listen to Music', '{*sitepagemusic_url*}', 1, 'Creating a Page Music Playlist'),
('sitepagealbum', 'Directory / Pages - Photo Albums Extension', 'sitepage_photo', 'photo_id', 'user_id', 'title', 'description', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'sitepagealbum_photo_new', 'View my Page Album!', '{*sitepagealbum_title*}', '{*sitepagealbum_url*}', '{*actor*} created a new Page Album on {*site_title*}: {*site_url*}.', '{*sitepagealbum_desc*}', 'View Page Album', '{*sitepagealbum_url*}', 1, 'Creating a Page Album'),
('document', 'Documents', 'document', 'document_id', 'owner_id', 'document_title', 'document_description', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'document_new', 'View my Document!', '{*document_title*}', '{*document_url*}', '{*actor*} added a Document on {*site_title*}: {*site_url*}.', '{*document_desc*}', 'View Document', '{*document_url*}', 1, 'Creating a Document'),
('sitebusiness', 'Directory / Businesses', 'sitebusiness_business', 'business_id', 'owner_id', 'title', 'body', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'sitebusiness_new', 'Join my Business!', '{*sitebusiness_title*}', '{*sitebusiness_url*}', '{*actor*} created a new Business  on {*site_title*}: {*site_url*}.', '{*sitebusiness_desc*}', 'View Business', '{*sitebusiness_url*}', 1, 'Creating a Directory Item / Business'),
('sitebusinessnote', 'Directory / Businesses - Notes Extension', 'sitebusinessnote_note', 'note_id', 'owner_id', 'title', 'body', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'sitebusinessnote_new', 'Read my Business Note!', '{*sitebusinessnote_title*}', '{*sitebusinessnote_url*}', '{*actor*} created a new Business Note on {*site_title*}: {*site_url*}.', '{*sitebusinessnote_desc*}', 'View Note', '{*sitebusinessnote_url*}', 1, 'Creating a Business Note'),
('sitebusinessvideo', 'Directory / Businesses - Videos Extension', 'sitebusinessvideo_video', 'video_id', 'owner_id', 'title', 'description', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'sitebusinessvideo_new', 'Watch my Business Video!', '{*sitebusinessvideo_title*}', '{*sitebusinessvideo_url*}', '{*actor*} added a new Business Video on {*site_title*}: {*site_url*}.', '{*sitebusinessvideo_desc*}', 'Watch Video', '{*sitebusinessvideo_url*}', 1, 'Creating a Business Video'),
('sitebusinesspoll', 'Directory / Businesses - Polls Extension', 'sitebusinesspoll_poll', 'poll_id', 'owner_id', 'title', 'description', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'sitebusinesspoll_new', 'Vote on my Business Poll!', '{*sitebusinesspoll_title*}', '{*sitebusinesspoll_url*}', '{*actor*} created a new Business Poll on {*site_title*}: {*site_url*}.', '{*sitebusinesspoll_desc*}', 'Vote on Poll', '{*sitebusinesspoll_url*}', 1, 'Creating a Business Poll'),
('sitebusinessreview','Directory / Businesses - Reviews Extension', 'sitebusinessreview_review', 'review_id', 'owner_id', 'title', 'body', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'sitebusinessreview_new', 'Read my Business Review', '{*sitebusinessreview_title*}', '{*sitebusinessreview_url*}', '{*actor*} posted a Review on a Business on {*site_title*}: {*site_url*}.', '{*sitebusinessreview_desc*}', 'View Review', '{*sitebusinessreview_url*}', 1, 'Creating a Business Review'),
('sitebusinessdocument', 'Directory / Businesses - Documents Extension', 'sitebusinessdocument_document', 'document_id', 'owner_id', 'sitebusinessdocument_title', 'sitebusinessdocument_description', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, '', '', '', '', '', '', '', '', 1, ''),
('sitebusinessevent', 'Directory / Businesses - Events Extension', 'sitebusinessevent_event', 'event_id', 'user_id', 'title', 'description', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'sitebusinessevent_new', 'Attend my Business Event!', '{*sitebusinessevent_title*}', '{*sitebusinessevent_url*}', '{*actor*} created a new Business Event on {*site_title*}: {*site_url*}.', '{*sitebusinessevent_desc*}', 'RSVP', '{*sitebusinessevent_url*}', 1, 'Creating a Business Event'),
('sitebusinessmusic', 'Directory / Businesses - Music Extension', 'sitebusinessmusic_playlist', 'playlist_id', 'owner_id', 'title', 'description', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'sitebusinessmusic_playlist_new', 'Listen to my Business Music!', '{*sitebusinessmusic_title*}', '{*sitebusinessmusic_url*}', '{*actor*} uploaded new Business Music on {*site_title*}: {*site_url*}.', '{*sitebusinessmusic_desc*}', 'Listen to Music', '{*sitebusinessmusic_url*}', 1, 'Creating a Business Music Playlist'),
('sitebusinessalbum', 'Directory / Businesses - Photo Albums Extension', 'sitebusiness_photo', 'photo_id', 'user_id', 'title', 'description', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'sitebusinessalbum_photo_new', 'View my Business Album!', '{*sitebusinessalbum_title*}', '{*sitebusinessalbum_url*}', '{*actor*} created a new Business Album on {*site_title*}: {*site_url*}.', '{*sitebusinessalbum_desc*}', 'View Album', '{*sitebusinessalbum_url*}', 1, 'Creating a Business Album / Add Photo'),
('home', 'Site Home Page', 'home', '', '', '', '', 1, 1, 'like', 0, 450, '', 'light', 'standard', 1, '', 0, '', '', 0, 1, 1, 450, 'light', 1, 1, 'signup', 'Join me on {*site_title*}: {*site_url*}', '{*actor*} has joined {*site_title*}: {*site_url*}', '{*site_url*}', 'Join {*actor*} on {*site_title*}: {*site_url*}', '{*site_description*}', 'Join {*site_title*}', '{*signup_page*}', 1, 'Linking to Facebook'),
('user', '', 'user', 'user_id', 'user_id', '', '', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 0, 1, 450, 'light', 1, 1, 'profile_photo_update', 'My new Photo!', '{*actor*} has a new profile photo on {*site_title*}', '{*profile_url*}', '', '', 'View Profile', '{*profile_url*}', 1, 'Uploading new Profile Photo'),
('sitealbum', 'Advanced Photo Albums', 'album', 'album_id', 'owner_id', 'title', 'description', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, '', '', '', '', '', '', '', '', 1, ''),
('list', 'Listing Photos', 'list_photo', 'photo_id', 'user_id', 'title', 'description', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, '', '', '', '', '', '', '', '', 1, ''),
('recipe', 'Recipe Photos', 'recipe_photo', 'photo_id', 'user_id', 'title', 'description', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, '', '', '', '', '', '', '', '', 1, ''),
('sitepagenote', 'Directory / Pages - Notes Photo', 'sitepagenote_photo', 'photo_id', 'user_id', 'title', 'description', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, '', '', '', '', '', '', '', '', 1, ''),
('sitebusinessnote', 'Directory / Business - Notes Extension', 'sitebusinessnote_photo', 'photo_id', 'user_id', 'title', 'description', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, '', '', '', '', '', '', '', '', 1, ''),
('sitepagediscussion', 'Directory / Pages - Discussions Extension ', 'sitepage_topic', 'topic_id', 'user_id', 'title', 'body', 1, 1, 'like', 0, 450, '', '', 'standard', 1, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'sitepage_topic_create', 'Join me for discussion!', '{*sitepagediscussion_title*}', '{*sitepagediscussion_url*}', '{*actor*} posted a new Page Discussion Topic  on {*site_title*}: {*site_url*}.', '{*sitepagediscussion_desc*}', 'View Discussion', '{*sitepagediscussion_url*}', 1, 'Creating a Page Topic'),
('sitebusinessdiscussion', 'Directory / Businesses - Topic Creation ', 'sitebusiness_topic', 'topic_id', 'user_id', 'title', 'body', 1, 1, 'like', 0, 450, '', '', 'standard', 1, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'sitebusiness_topic_create', 'Join me for discussion!', '{*sitebusinessdiscussion_title*}', '{*sitebusinessdiscussion_url*}', '{*actor*} posted a new Business Discussion Topic  on {*site_title*}: {*site_url*}.', '{*sitebusinessdiscussion_desc*}', 'View Discussion', '{*sitebusinessdiscussion_url*}', 1, 'Creating a Business Topic'),
('album', 'Album Photos', 'album_photo', 'photo_id', 'owner_id', 'title', 'description', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, '', '', '', '', '', '', '', '', 0, ''),
('sitebusinessdiscussion', 'Directory / Businesses - Topic Reply', '', 'topic_id', 'user_id', 'title', 'body', 1, 1, 'like', 0, 450, '', '', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'sitebusiness_topic_reply', 'Join me for discussion!', '{*sitebusinessdiscussion_title*}', '{*sitebusinessdiscussion_url*}', '{*actor*} is taking part in a Business Discussion on {*site_title*}: {*site_url*}.', '{*sitebusinessdiscussion_desc*}', 'View Discussion', '{*sitebusinessdiscussion_url*}', 1, 'Replying to a Business Topic'),
('sitepagediscussion', 'Directory / Pages - Topic Creation', '', 'topic_id', 'user_id', 'title', 'body', 1, 1, 'like', 0, 450, '', '', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'sitepage_topic_reply', 'Join me for discussion!', '{*sitepagediscussion_title*}', '{*sitepagediscussion_url*}', '{*actor*} is taking part in a Page Discussion on {*site_title*}: {*site_url*}.', '{*sitepagediscussion_desc*}', 'View Discussion', '{*sitepagediscussion_url*}', 1, 'Replying to a Page Topic'),
('forum', 'Forums Topic Reply', '', 'topic_id', 'user_id', 'title', 'description', 1, 1, 'like', 0, 450, '', '', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'forum_topic_reply', 'Join me for discussion!', '{*forumtopic_title*}', '{*forumtopic_url*}', '{*actor*} is taking part in a discussion on {*site_title*}: {*site_url*}.', '{*forumtopic_desc*}', 'View Discussion', '{*forumtopic_url*}', 1, 'Replying to a Forum Topic'),


('sitegroup', 'Groups / Communities', 'sitegroup_group', 'group_id', 'owner_id', 'title', 'body', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'sitegroup_new', 'Join my Group!', '{*sitegroup_title*}', '{*sitegroup_url*}', '{*actor*} created a new Group  on {*site_title*}: {*site_url*}.', '{*sitegroup_desc*}', 'View Group', '{*sitegroup_url*}', 1, 'Creating a Group / Community'),
('sitegroupnote', 'Groups / Communities - Notes Extension', 'sitegroupnote_note', 'note_id', 'owner_id', 'title', 'body', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'sitegroupnote_new', 'Read my Group Note!', '{*sitegroupnote_title*}', '{*sitegroupnote_url*}', '{*actor*} created a new Group Note on {*site_title*}: {*site_url*}.', '{*sitegroupnote_desc*}', 'View Note', '{*sitegroupnote_url*}', 1, 'Creating a Group Note'),
('sitegroupvideo', 'Groups / Communities - Videos Extension', 'sitegroupvideo_video', 'video_id', 'owner_id', 'title', 'description', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'sitegroupvideo_new', 'Watch my Group Video!', '{*sitegroupvideo_title*}', '{*sitegroupvideo_url*}', '{*actor*} added a new Group Video on {*site_title*}: {*site_url*}.', '{*sitegroupvideo_desc*}', 'Watch Video', '{*sitegroupvideo_url*}', 1, 'Creating a Group Video'),
('sitegrouppoll', 'Groups / Communities - Polls Extension', 'sitegrouppoll_poll', 'poll_id', 'owner_id', 'title', 'description', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'sitegrouppoll_new', 'Vote on my Group Poll!', '{*sitegrouppoll_title*}', '{*sitegrouppoll_url*}', '{*actor*} created a new Group Poll on {*site_title*}: {*site_url*}.', '{*sitegrouppoll_desc*}', 'Vote on Poll', '{*sitegrouppoll_url*}', 1, 'Creating a Group Poll'),
('sitegroupreview','Groups / Communities - Reviews Extension', 'sitegroupreview_review', 'review_id', 'owner_id', 'title', 'body', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'sitegroupreview_new', 'Read my Group Review', '{*sitegroupreview_title*}', '{*sitegroupreview_url*}', '{*actor*} posted a Review on a Group on {*site_title*}: {*site_url*}.', '{*sitegroupreview_desc*}', 'View Review', '{*sitegroupreview_url*}', 1, 'Creating a Group Review'),
('sitegroupdocument', 'Groups / Communities - Documents Extension', 'sitegroupdocument_document', 'document_id', 'owner_id', 'sitegroupdocument_title', 'sitegroupdocument_description', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, '', '', '', '', '', '', '', '', 1, ''),
('sitegroupevent', 'Groups / Communities - Events Extension', 'sitegroupevent_event', 'event_id', 'user_id', 'title', 'description', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'sitegroupevent_new', 'Attend my Group Event!', '{*sitegroupevent_title*}', '{*sitegroupevent_url*}', '{*actor*} created a new Group Event on {*site_title*}: {*site_url*}.', '{*sitegroupevent_desc*}', 'RSVP', '{*sitegroupevent_url*}', 1, 'Creating a Group Event'),
('sitegroupmusic', 'Groups / Communities - Music Extension', 'sitegroupmusic_playlist', 'playlist_id', 'owner_id', 'title', 'description', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'sitegroupmusic_playlist_new', 'Listen to my Group Music!', '{*sitegroupmusic_title*}', '{*sitegroupmusic_url*}', '{*actor*} uploaded new Group Music on {*site_title*}: {*site_url*}.', '{*sitegroupmusic_desc*}', 'Listen to Music', '{*sitegroupmusic_url*}', 1, 'Creating a Group Music Playlist'),
('sitegroupalbum', 'Groups / Communities - Photo Albums Extension', 'sitegroup_photo', 'photo_id', 'user_id', 'title', 'description', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'sitegroupalbum_photo_new', 'View my Group Album!', '{*sitegroupalbum_title*}', '{*sitegroupalbum_url*}', '{*actor*} created a new Group Album on {*site_title*}: {*site_url*}.', '{*sitegroupalbum_desc*}', 'View Album', '{*sitegroupalbum_url*}', 1, 'Creating a Group Album / Add Photo'),
('sitegroupnote', 'Group / Community - Notes Extension', 'sitegroupnote_photo', 'photo_id', 'user_id', 'title', 'description', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, '', '', '', '', '', '', '', '', 1, ''),
('sitegroupdiscussion', 'Groups / Communities - Topic Creation ', 'sitegroup_topic', 'topic_id', 'user_id', 'title', 'body', 1, 1, 'like', 0, 450, '', '', 'standard', 1, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'sitegroup_topic_create', 'Join me for discussion!', '{*sitegroupdiscussion_title*}', '{*sitegroupdiscussion_url*}', '{*actor*} posted a new Group Discussion Topic  on {*site_title*}: {*site_url*}.', '{*sitegroupdiscussion_desc*}', 'View Discussion', '{*sitegroupdiscussion_url*}', 1, 'Creating a Group Topic'),
('sitegroupdiscussion', 'Groups / Communities - Topic Reply', '', 'topic_id', 'user_id', 'title', 'body', 1, 1, 'like', 0, 450, '', '', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'sitegroup_topic_reply', 'Join me for discussion!', '{*sitegroupdiscussion_title*}', '{*sitegroupdiscussion_url*}', '{*actor*} is taking part in a Group Discussion on {*site_title*}: {*site_url*}.', '{*sitegroupdiscussion_desc*}', 'View Discussion', '{*sitegroupdiscussion_url*}', 1, 'Replying to a Group Topic'),
('sitestore', 'Stores / Marketplace - Stores', 'sitestore_store', 'store_id', 'owner_id', 'title', 'body', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'sitestore_new', 'View my Store!', '{*sitestore_title*}', '{*sitestore_url*}', '{*actor*} created a new Store  on {*site_title*}: {*site_url*}.', '{*sitestore_desc*}', 'View Store', '{*sitestore_url*}', 1, 'Creating a Stores / Marketplace'),
('siteevent', 'Advanced Events', 'siteevent_event', 'event_id', 'owner_id', 'title', 'body', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'siteevent_new', 'Join my Event!', '{*siteevent_title*}', '{*siteevent_url*}', '{*actor*} created a new Event on {*site_title*}: {*site_url*}.', '{*siteevent_desc*}', 'View Event', '{*siteevent_url*}', 1, 'Creating an Event'),
('siteeventdocument', 'Advanced Events - Documents Extension', 'siteeventdocument_document', 'document_id', 'owner_id', 'title', 'description', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, '', '', '', '', '', '', '', '', 1, ''),
('sitestoreproduct', 'Stores / Marketplace - Products', 'sitestoreproduct_product', 'product_id', 'owner_id', 'title', 'body', 1, 1, 'like', 1, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 0, 1, 450, 'light', 1, 1, 'sitestoreproduct_new', 'View my Product!', '{*sitestoreproduct_title*}', '{*sitestoreproduct_url*}',  '{*actor*} created a new Product: {*sitestoreproduct_title*} on Store: {*sitestore_title*} on {*site_title*}: {*site_url*}.', '{*sitestoreproduct_desc*}', 'View Product', '{*sitestoreproduct_url*}', 1, 'Creating a Stores / Marketplace - Product'),
('siteestore',  '', 'siteestore_product', 'product_id', '', 'title', 'description', 1, 1, 'like', 0, 450, '', '', 'standard', 0, '', 0, '', '', 1, 0, 0, 450, 'light', 1, 1, '', '', '', '', '', '', '', '', 0, ''),

('siteforum', 'Forum Topic', 'forum_topic', 'topic_id', 'user_id', 'title', 'description', 1, 1, 'like', 0, 450, '', '', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 0, 'siteforum_topic_create', 'View my Forum Topic!', '{*siteforum_title*}', '{*siteforum_url*}', '{*actor*} created a Siteforum on {*site_title*}: {*site_url*}.', '{*siteforum_desc*}', 'View Siteforum', '{*siteforum_url*}', 1, 'Creating a Siteforum'),

( 'sitevideo', 'Video', 'video', 'video_id', 'owner_id', 'title', 'description', 1, 1, 'like', 0, 450, '', '', 'standard', 1, '', 0, '', '', 0, 1, 1, 450, 'light', 1, 1, 'sitevideo_video_new', 'View my Video!', '{*sitevideo_title*}', '{*sitevideo_url*}', '{*actor*} created a Video on {*site_title*}: {*site_url*}.', '{*sitevideo_desc*}', 'View Video', '{*sitevideo_url*}', 1, 'Creating a Video'),

( 'sitevideo', 'Video Channels', 'sitevideo_channel', 'channel_id', 'owner_id', 'title', 'description', 1, 1, 'like', 0, 450, '', '', 'standard', 1, '', 0, '', '', 0, 1, 1, 450, 'light', 1, 1, 'sitevideo_channel_new', 'View my Video Channel!', '{*sitevideo_title*}', '{*sitevideo_url*}', '{*actor*} created a Video Channel on {*site_title*}: {*site_url*}.', '{*sitevideo_desc*}', 'View Video Channel', '{*sitevideo_url*}', 1, 'Creating a Video Channel');");
  }

  public function addThirdpartyModule($module, $db) {

    //FIRST CHECK IF THE MODULE ENTRY IS ALREAYD THERE OR NOT:

    $select = new Zend_Db_Select($db);
    $module = $select
            ->from('engine4_facebookse_mixsettings', 'module')
            ->where('module = ?', $module)
            ->query()
            ->fetchColumn();
    if (!empty($module))
      return;
    $moduleinfo = '';
    switch ($module) {

      case 'ultimatenews':
        $moduleinfo = "('ultimatenews', 'Ultimate News', 'ultimatenews_content', 'content_id', 'owner_id', 'title', 'content', 1, 1, 'like', 1, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 0, 0, '', '', '', '', '', '', '', '', 0, '')";
        break;
      case 'ynvideo':
        $moduleinfo = "('ynvideo', 'Video', 'video', 'video_id', 'owner_id', 'title', 'description', 1, 1, 'like', 1, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 0, 0, '', '', '', '', '', '', '', '', 0, '')";
        break;
      case 'mp3music':
        $moduleinfo = "('mp3music', 'Music', 'mp3music_album', 'album_id', 'user_id', 'title', 'description', 1, 1, 'like', 1, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 0, 0, '', '', '', '', '', '', '', '', 0, '')";
        break;
      case 'page':
        $moduleinfo = "('page', 'Page', 'page', 'page_id', 'user_id', 'title', 'description', 1, 1, 'like', 1, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 0, 0, '', '', '', '', '', '', '', '', 0, '')";
        break;
      case 'advgroup':
        $moduleinfo = "('advgroup', 'Group', 'group', 'group_id', 'user_id', 'title', 'description', 1, 1, 'like', 1, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 0, 0, '', '', '', '', '', '', '', '', 0, '')";
        break;
      case 'advancedarticles':
        $moduleinfo = "('advancedarticles', 'Article', 'artarticle', 'article_id', 'user_id', 'title', 'description', 1, 1, 'like', 1, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 0, 0, '', '', '', '', '', '', '', '', 0, '')";
        break;
      case 'ynblog':
        $moduleinfo = "('ynblog', 'Blog', 'blog', 'blog_id', 'owner_id', 'title', 'body', 1, 1, 'like', 1, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 0, 0, '', '', '', '', '', '', '', '', 0, '')";
        break;
    }
if (!empty($moduleinfo)) { 
    $db->query("INSERT IGNORE INTO `engine4_facebookse_mixsettings` (`module`, `module_name`, `resource_type`, `resource_id`, `owner_field`, `module_title`, `module_description`, `enable`, `send_button`, `like_type`, `like_faces`, `like_width`, `like_font`, `like_color`, `layout_style`, `opengraph_enable`, `title`, `photo_id`, `description`, `types`, `fbadmin_appid`, `commentbox_enable`, `commentbox_privacy`, `commentbox_width`, `commentbox_color`, `module_enable`, `default`, `activityfeed_type`, `streampublish_message`, `streampublish_story_title`, `streampublish_link`, `streampublish_caption`, `streampublish_description`, `streampublish_action_link_text`, `streampublish_action_link_url`, `streampublishenable`, `activityfeedtype_text`) VALUES
    " . $moduleinfo);
}
  }
  
  
  
  //ADD DEFAULT MODULES WHICH WERE BUILD AFTER FACEBOOK PLUGIN RELEASE.
  public function addDefaultModule($resource_type, $db) {
    
     //CHECK IF THIS ENTRY ALREADY EXIST THERE
      $select = new Zend_Db_Select($db);
      $select
            ->from('engine4_facebookse_mixsettings')
            ->where('resource_type = ?', $resource_type);
            
    $insertmodule = $select->query()->fetchObject();    
    if (!empty($insertmodule)) return;
    $moduleinfo = '';
    switch ($resource_type) {
      
      case 'sitestore_store':
       
        $moduleinfo = "('sitestore', 'Stores / Marketplace - Stores', 'sitestore_store', 'store_id', 'owner_id', 'title', 'body', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'sitestore_new', 'View my Store!', '{*sitestore_title*}', '{*sitestore_url*}', '{*actor*} created a new Store  on {*site_title*}: {*site_url*}.', '{*sitestore_desc*}', 'View Store', '{*sitestore_url*}', 1, 'Creating a Stores / Marketplace')";
        break;
      
      case 'sitestoreproduct_product':
        
        $moduleinfo = "('sitestoreproduct', 'Stores / Marketplace - Products', 'sitestoreproduct_product', 'product_id', 'owner_id', 'title', 'body', 1, 1, 'like', 1, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 0, 1, 450, 'light', 1, 1, 'sitestoreproduct_new', 'View my Product!', '{*sitestoreproduct_title*}', '{*sitestoreproduct_url*}',  '{*actor*} created a new Product: {*sitestoreproduct_title*} on Store: {*sitestore_title*} on {*site_title*}: {*site_url*}.', '{*sitestoreproduct_desc*}', 'View Product', '{*sitestoreproduct_url*}', 1, 'Creating a Stores / Marketplace - Product')";
        break;
      
      case 'siteevent_event':
        
        $moduleinfo = "('siteevent', 'Advanced Events', 'siteevent_event', 'event_id', 'owner_id', 'title', 'body', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'siteevent_new', 'Join my Event!', '{*siteevent_title*}', '{*siteevent_url*}', '{*actor*} created a new Event on {*site_title*}: {*site_url*}.', '{*siteevent_desc*}', 'View Event', '{*siteevent_url*}', 1, 'Creating an Event')";
        break;
      
      case 'siteeventdocument_document':
        
        $moduleinfo = "('siteeventdocument', 'Advanced Events - Documents Extension', 'siteeventdocument_document', 'document_id', 'owner_id', 'siteeventdocument_title', 'siteeventdocument_description', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, '', '', '', '', '', '', '', '', 1, '')";
        break;
        
      default :
        $moduleinfo = '';
      
    }

    if (!empty($moduleinfo)) { 
      
      $db->query("INSERT IGNORE INTO `engine4_facebookse_mixsettings` (`module`, `module_name`, `resource_type`, `resource_id`, `owner_field`, `module_title`, `module_description`, `enable`, `send_button`, `like_type`, `like_faces`, `like_width`, `like_font`, `like_color`, `layout_style`, `opengraph_enable`, `title`, `photo_id`, `description`, `types`, `fbadmin_appid`, `commentbox_enable`, `commentbox_privacy`, `commentbox_width`, `commentbox_color`, `module_enable`, `default`, `activityfeed_type`, `streampublish_message`, `streampublish_story_title`, `streampublish_link`, `streampublish_caption`, `streampublish_description`, `streampublish_action_link_text`, `streampublish_action_link_url`, `streampublishenable`, `activityfeedtype_text`)VALUES
    " . $moduleinfo);
    }
    
  }

}

?>
