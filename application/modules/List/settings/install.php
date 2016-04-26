<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: install.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Installer extends Engine_Package_Installer_Module {

  function onPreInstall() {
    $PRODUCT_TYPE = 'list';
    $PLUGIN_TITLE = 'List';
    $PLUGIN_VERSION = '4.8.8';
    $PLUGIN_CATEGORY = 'plugin';
    $PRODUCT_DESCRIPTION = 'Listing / Catalog Showcase';
    $_PRODUCT_FINAL_FILE = 0;
    $_BASE_FILE_NAME = 0;
    $PRODUCT_TITLE = 'Listing / Catalog Showcase';
    $SocialEngineAddOns_version = '4.8.6';
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
      if( empty($is_Mod) ) {
				include_once $file_path;
      }
    }
    $this->setActivityFeeds();
    parent::onPreInstall();
  }

  function onInstall() {
    include_once APPLICATION_PATH . '/application/modules/List/controllers/license/license3.php';

    $db = $this->getDb();

		$db->query('UPDATE  `engine4_core_content` SET  `name` =  "seaocore.like-button" WHERE  `engine4_core_content`.`name` ="list.listing-like-button";');
		
    $db->query('UPDATE  `engine4_core_content` SET  `name` =  "seaocore.people-like" WHERE  `engine4_core_content`.`name` ="list.listing-like";');

		//START THE WORK FOR MAKE WIDGETIZE PAGE OF Locatio or map.
		$select = new Zend_Db_Select($db);
		$select
						->from('engine4_core_pages')
						->where('name = ?', 'list_index_map')
						->limit(1);
		$info = $select->query()->fetch();

		if ( empty($info) ) {
			$db->insert('engine4_core_pages', array(
					'name' => 'list_index_map',
					'displayname' => 'Browse Listings’ Locations',
					'title' => 'Browse Listings’ Locations',
					'description' => 'Browse Listings’ Locations',
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
					'name' => 'list.browsenevigation-list',
					'parent_content_id' => $top_middle_id,
					'order' => 1,
					'params' => '',
			));
			
			//INSERT WIDGET OF LOCATION SEARCH AND CORE CONTENT
			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'list.location-search',
				'parent_content_id' => $middle_id,
				'order' => 2,
				'params' => '{"title":"","titleCount":"true","street":"1","city":"1","state":"1","country":"1"}',
				));

			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'list.browselocation-list',
				'parent_content_id' => $middle_id,
				'order' => 3,
				'params' => '{"title":"","titleCount":"true"}',
			));
		}
    //END THE WORK FOR MAKE WIDGETIZE PAGE OF LOCATIO OR MAP.

		//START THE WORK FOR MAKE WIDGETIZE PAGE OF Locatio or map.MOBILE PAGE.
		$select = new Zend_Db_Select($db);
		$select
						->from('engine4_core_pages')
						->where('name = ?', 'list_index_mobilemap')
						->limit(1);
		$info = $select->query()->fetch();

		if ( empty($info) ) {
			$db->insert('engine4_core_pages', array(
					'name' => 'list_index_mobilemap',
					'displayname' => 'Mobile Browse Listings’ Locations',
					'title' => 'Mobile Browse Listings’ Locations',
					'description' => 'Mobile Browse Listings’ Locations',
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
					'name' => 'list.browsenevigation-list',
					'parent_content_id' => $top_middle_id,
					'order' => 1,
					'params' => '',
			));
			
			//INSERT WIDGET OF LOCATION SEARCH AND CORE CONTENT
			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'list.location-search',
				'parent_content_id' => $middle_id,
				'order' => 2,
				'params' => '{"title":"","titleCount":"true","street":"1","city":"1","state":"1","country":"1"}',
				));

			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'list.browselocation-list',
				'parent_content_id' => $middle_id,
				'order' => 3,
				'params' => '{"title":"","titleCount":"true"}',
			));

		}
		//END LOCARTION WORK.

    $table_list_exist = $db->query('SHOW TABLES LIKE \'engine4_list_listings\'')->fetch();
    if (!empty($table_list_exist)) {
      $column_exist = $db->query('SHOW COLUMNS FROM engine4_list_listings LIKE \'aprrove_date\'')->fetch();
      if (!empty($column_exist)) {
        $db->query('ALTER TABLE `engine4_list_listings` CHANGE `aprrove_date` `approved_date` DATETIME NULL DEFAULT NULL;');
      }
      $column_exist = $db->query('SHOW COLUMNS FROM engine4_list_listings LIKE \'end_date\'')->fetch();
      if (empty($column_exist)) {
        $db->query('ALTER TABLE `engine4_list_listings` ADD `end_date` DATETIME NULL DEFAULT NULL AFTER `draft` ;');
      }
    }
    //WORK FOR CORE CONTENT PAGES
		$select = new Zend_Db_Select($db);

    $select->from('engine4_core_content',array('params'))
            ->where('name = ?', 'list.socialshare-list');
		$result = $select->query()->fetchObject();
    if(!empty($result->params)) {
			$params = Zend_Json::decode($result->params);
			if(isset($params['code'])) {
				$code = $params['code'];
				$db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
				('list.code.share','".$code. "');");
			}
    }

    $table_exist = $db->query('SHOW TABLES LIKE \'engine4_list_itemofthedays\'')->fetch();
    if (!empty($table_exist)) {
      $column_exist = $db->query('SHOW COLUMNS FROM engine4_list_itemofthedays LIKE \'endtime\'')->fetch();
      if (empty($column_exist)) {
        $db->query('ALTER TABLE `engine4_list_itemofthedays` ADD `endtime` DATE NOT NULL');
        $db->query('ALTER TABLE `engine4_list_itemofthedays` add index(`endtime`);');
      }

			$column_exist = $db->query('SHOW COLUMNS FROM engine4_list_itemofthedays LIKE \'title\'')->fetch();
			if(!empty($column_exist)) {
				$db->query("ALTER TABLE `engine4_list_itemofthedays` DROP `title`");
			}
    }

    $table_exist = $db->query('SHOW TABLES LIKE \'engine4_list_photos\'')->fetch();
    if (!empty($table_exist)) {
      $column_exist = $db->query('SHOW COLUMNS FROM engine4_list_photos LIKE \'view_count\'')->fetch();
      if (empty($column_exist)) {
        $db->query('ALTER TABLE `engine4_list_photos` ADD `view_count` INT( 11 ) NOT NULL ');
      }

     $column_exist = $db->query('SHOW COLUMNS FROM engine4_list_photos LIKE \'type\'')->fetch();
      if (empty($column_exist)) {
        $db->query('ALTER TABLE `engine4_list_photos` ADD `type` VARCHAR( 16 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL AFTER `file_id`;');
      }

      $column_exist = $db->query('SHOW COLUMNS FROM engine4_list_photos LIKE \'description\'')->fetch();
      if (empty($column_exist)) {
        $db->query('ALTER TABLE `engine4_list_photos` CHANGE `description` `description` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL');
      }
    }

    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'list');
    $check_module = $select->query()->fetchObject();
    if (!empty($check_module)) {

      $curr_module_version = strcasecmp($check_module->version, '4.1.3');
      if ($curr_module_version < 0) {

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_pages')
                ->where('name = ?', 'list_index_home')
                ->limit(1);
        $page = $select->query()->fetch();
        if (!empty($page)) {
          $page_id = $page['page_id'];

          $db = Zend_Db_Table_Abstract::getDefaultAdapter();
          $db = $this->getDb();

          $db->delete('engine4_core_content', array(
              'page_id = ?' => $page_id,
              'name = ?' => "core.container-tabs"
          ));

          $db->delete('engine4_core_content', array(
              'page_id = ?' => $page_id,
              'name = ?' => "list.random-list"
          ));
          $db->delete('engine4_core_content', array(
              'page_id = ?' => $page_id,
              'name = ?' => "list.mostviewed-list"
          ));
          $db->delete('engine4_core_content', array(
              'page_id = ?' => $page_id,
              'name = ?' => "list.recentlyposted-list"
          ));

          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_content')
                  ->where('name = ?', 'middle')
                  ->where('page_id = ?', $page_id)
                  ->limit(1);
          $middle = $select->query()->fetch();
          if (!empty($middle)) {
            $middle_id = $middle['content_id'];
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'list.recently-popular-random-list',
                'parent_content_id' => $middle_id,
                'order' => 16,
                'params' => '{"title":"","titleCount":""}',
            ));
          }
        }
      }
    }

    // Mobile Listing Home
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_pages')
            ->where('name = ?', 'list_mobi_home')
            ->limit(1);
    ;
    $info = $select->query()->fetch();

    if (empty($info)) {
      $db->insert('engine4_core_pages', array(
          'name' => 'list_mobi_home',
          'displayname' => 'Mobile Listing Home',
          'title' => 'Mobile Listing Home',
          'description' => 'This is the mobile verison of a listing home page.',
          'custom' => 0
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

      // containers
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => null,
          'order' => 1,
          'params' => '',
      ));
      $container_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'middle',
          'parent_content_id' => $container_id,
          'order' => 2,
          'params' => '',
      ));
      $middle_id = $db->lastInsertId('engine4_core_content');

      // widgets entry
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'list.browsenevigation-list',
          'parent_content_id' => $middle_id,
          'order' => 1,
          'params' => '{"title":"","titleCount":"true"}',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'list.zerolisiting-list',
          'parent_content_id' => $middle_id,
          'order' => 2,
          'params' => '{"title":"","titleCount":"true"}',
      ));
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'list.search-list',
          'parent_content_id' => $middle_id,
          'order' => 3,
          'params' => '{"title":"","titleCount":"true"}',
      ));
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'list.recently-popular-random-list',
          'parent_content_id' => $middle_id,
          'order' => 4,
          'params' => '{"title":"","titleCount":"true"}',
      ));
    }

    // Mobile Browse Listings
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_pages')
            ->where('name = ?', 'list_mobi_index')
            ->limit(1);
    ;
    $info = $select->query()->fetch();

    if (empty($info)) {
      $db->insert('engine4_core_pages', array(
          'name' => 'list_mobi_index',
          'displayname' => 'Mobile Browse Listings',
          'title' => 'Mobile Browse Listings',
          'description' => 'This is the mobile verison of a listing browse page.',
          'custom' => 0
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

      // containers
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => null,
          'order' => 1,
          'params' => '',
      ));
      $container_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'middle',
          'parent_content_id' => $container_id,
          'order' => 2,
          'params' => '',
      ));
      $middle_id = $db->lastInsertId('engine4_core_content');


      // widgets entry
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'list.browsenevigation-list',
          'parent_content_id' => $middle_id,
          'order' => 1,
          'params' => '{"title":"","titleCount":"true"}',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'list.search-list',
          'parent_content_id' => $middle_id,
          'order' => 2,
          'params' => '{"title":"","titleCount":"true"}',
      ));
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'list.listings-list',
          'parent_content_id' => $middle_id,
          'order' => 3,
          'params' => '{"title":"","titleCount":"true"}',
      ));
    }

    // Mobile Listing Profile
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_pages')
            ->where('name = ?', 'list_mobi_view')
            ->limit(1);
    ;
    $info = $select->query()->fetch();

    if (empty($info)) {
      $db->insert('engine4_core_pages', array(
          'name' => 'list_mobi_view',
          'displayname' => 'Mobile Listing Profile',
          'title' => 'Mobile Listing Profile',
          'description' => 'This is the mobile verison of a listing profile.',
          'custom' => 0
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

      // containers
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => null,
          'order' => 1,
          'params' => '',
      ));
      $container_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'middle',
          'parent_content_id' => $container_id,
          'order' => 2,
          'params' => '',
      ));
      $middle_id = $db->lastInsertId('engine4_core_content');

      // widgets entry
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'list.mainphoto-list',
          'parent_content_id' => $middle_id,
          'order' => 1,
          'params' => '{"title":"","titleCount":"true"}',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'list.title-list',
          'parent_content_id' => $middle_id,
          'order' => 2,
          'params' => '{"title":"","titleCount":"true"}',
      ));

      // middle tabs
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'core.container-tabs',
          'parent_content_id' => $middle_id,
          'order' => 4,
          'params' => '{"max":"6"}',
      ));
      $tab_middle_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'activity.feed',
          'parent_content_id' => $tab_middle_id,
          'order' => 1,
          'params' => '{"title":"What\'s New","titleCount":"true"}',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'list.info-list',
          'parent_content_id' => $tab_middle_id,
          'order' => 2,
          'params' => '{"title":"Info","titleCount":"true"}',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'list.overview-list',
          'parent_content_id' => $tab_middle_id,
          'order' => 3,
          'params' => '{"title":"Overview","titleCount":"true"}',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'list.location-list',
          'parent_content_id' => $tab_middle_id,
          'order' => 4,
          'params' => '{"title":"Map","titleCount":"true"}',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'list.photos-list',
          'parent_content_id' => $tab_middle_id,
          'order' => 5,
          'params' => '{"title":"Photos","titleCount":"true"}',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'list.video-list',
          'parent_content_id' => $tab_middle_id,
          'order' => 6,
          'params' => '{"title":"Videos","titleCount":"true"}',
      ));
    }

    $db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` ='list';");

//    //START CODE FOR LIGHTBOX
//    //HERE WE CHECKING THAT SITEPAGEALBUM ENTRY EXIST IN THE CORE MODULE TABLE OR NOT
//    $select = new Zend_Db_Select($db);
//    $select
//            ->from('engine4_core_modules', array('version'))
//            ->where("name = ?", "list");
//    $listVersion = $select->query()->fetchAll();
//    $value = '';
//    $select = new Zend_Db_Select($db);
//    $value = $select
//            ->from('engine4_core_settings', array('value'))
//            ->where("name = ?", "socialengineaddon.display.lightbox")
//            ->query()
//            ->fetchColumn();
//    //IF NOT EXIST THEN WE INSERTING THE LIGHTBOX SHOULD BE DISPLAY OR NOT
//    if (empty($listVersion)) {
//      //IF LIGHTBOX IS NOT DISPLAY THEN WE WILL INSERTING THE ACTIVITY FEED VALUE
//      if (empty($value)) {
//        $select = new Zend_Db_Select($db);
//        $select
//                ->from('engine4_core_settings', array('name'))
//                ->where("name Like ?", "%socialengineaddon.lightbox.option.display%");
//        $name = $select->query()->fetchAll();
//
//        $count = count($name);
//
//        $select = new Zend_Db_Select($db);
//        $select
//                ->from('engine4_core_settings', array('name'))
//                ->where("value = ?", "list");
//        $name = $select->query()->fetchColumn();
//        if (empty($name)) {
//          $name = 'socialengineaddon.lightbox.option.display.' . ++$count;
//          $db->insert('engine4_core_settings', array(
//              'name' => $name,
//              'value' => 'list'
//          ));
//        }
//      }
//    } else {
//      if($listVersion <= '4.1.9') {
//        if (empty($value)) {
//          $select = new Zend_Db_Select($db);
//          $select
//                  ->from('engine4_core_settings', array('name'))
//                  ->where("name Like ?", "%socialengineaddon.lightbox.option.display%");
//          $name = $select->query()->fetchAll();
//
//          $count = count($name);
//
//          $select = new Zend_Db_Select($db);
//          $select
//                  ->from('engine4_core_settings', array('name'))
//                  ->where("value = ?", "list");
//          $name = $select->query()->fetchColumn();
//          if (empty($name)) {
//            $name = 'socialengineaddon.lightbox.option.display.' . ++$count;
//            $db->insert('engine4_core_settings', array(
//                'name' => $name,
//                'value' => 'list'
//            ));
//          }
//        }
//      }
//    }
//    //END CODE FOR LIGHTBOX    

//START ------------------------WORK FOR 4.2.7 RELEASE

    $table_exist = $db->query('SHOW TABLES LIKE \'engine4_list_categories\'')->fetch();
    if (!empty($table_exist)) {

			$catOrderColumn = $db->query("SHOW COLUMNS FROM engine4_list_categories LIKE 'cat_order'")->fetch();
			if(!empty($catOrderColumn)) {
				$db->query("ALTER TABLE `engine4_list_categories` CHANGE `cat_order` `cat_order` SMALLINT( 3 ) NOT NULL DEFAULT '0';");
			}

      $column_exist = $db->query('SHOW COLUMNS FROM engine4_list_categories LIKE \'subcat_dependency\'')->fetch();
      if (empty($column_exist)) {
				$db->query("ALTER TABLE `engine4_list_categories` ADD `subcat_dependency` INT( 11 ) NOT NULL DEFAULT '0'");
      }

      $column_exist = $db->query('SHOW COLUMNS FROM engine4_list_categories LIKE \'file_id\'')->fetch();
      if (empty($column_exist)) {
				$db->query("ALTER TABLE `engine4_list_categories` ADD `file_id` INT( 11 ) NOT NULL DEFAULT '0'");
      }

      $column_exist = $db->query('SHOW COLUMNS FROM engine4_list_categories LIKE \'user_id\'')->fetch();
      if (!empty($column_exist)) {
				$db->query("ALTER TABLE `engine4_list_categories` DROP `user_id`");
      }
    }

    $table_exist = $db->query('SHOW TABLES LIKE \'engine4_list_listings\'')->fetch();
    if (!empty($table_exist)) {

			$photoIdColumn = $db->query("SHOW COLUMNS FROM engine4_list_listings LIKE 'photo_id'")->fetch();
			if(!empty($photoIdColumn)) {
				$db->query("ALTER TABLE `engine4_list_listings` CHANGE `photo_id` `photo_id` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0';");
			}

			$closedColumn = $db->query("SHOW COLUMNS FROM engine4_list_listings LIKE 'closed'")->fetch();
			if(!empty($closedColumn)) {
				$db->query("ALTER TABLE `engine4_list_listings` CHANGE `closed` `closed` TINYINT( 1 ) NOT NULL DEFAULT '0';");
			}

			$loction_column_exist = $db->query("SHOW COLUMNS FROM engine4_list_listings LIKE 'location'")->fetch();
			if(empty($loction_column_exist)) {
				$db->query("ALTER TABLE `engine4_list_listings` ADD `location` VARCHAR( 255 ) NOT NULL AFTER `photo_id`;");
			}

      $column_exist = $db->query('SHOW COLUMNS FROM engine4_list_listings LIKE \'subsubcategory_id\'')->fetch();
      if (empty($column_exist)) {
				$db->query("ALTER TABLE `engine4_list_listings` ADD `subsubcategory_id` INT( 11 ) NOT NULL DEFAULT '0'");
			}

      $column_exist = $db->query('SHOW COLUMNS FROM engine4_list_listings LIKE \'rate_count\'')->fetch();
      if (!empty($column_exist)) {
        $db->query('ALTER TABLE `engine4_list_listings` CHANGE `rate_count` `review_count` INT( 11 ) NOT NULL');

				//FETCH LISTINGS
				$listings = $db->select()->from('engine4_list_listings', 'listing_id')->query()->fetchAll();

				if (!empty($listings)) {
					foreach($listings as $listing)
					{
						$listing_id = $listing['listing_id'];

						if(!empty($listing_id)) {

							//GET TOTAL REVIEWS CORROSPONDING TO LISTING ID
							$total_reviews = $db->select()
															->from('engine4_list_reviews', array('COUNT(*) AS count'))
															->where('listing_id = ?', $listing_id)
															->limit(1)
															->query()
															->fetchColumn();

              $db->update('engine4_list_listings', array('review_count' => $total_reviews), array('listing_id = ?' => $listing_id));
						}
					}
				}
      }
    }

		//START PROFILE MAPPING WORK
		$select = new Zend_Db_Select($db);
		$select
			->from('engine4_core_modules')
			->where('name = ?', 'list')
			->where('version < ?', '4.2.7');
		$is_enabled = $select->query()->fetchObject();

		$table_exist = $db->query("SHOW TABLES LIKE 'engine4_list_listing_fields_meta'")->fetch();

		if (!empty($table_exist) && !empty($is_enabled)) {

			//START IMPORT ALL LOCATION DATA
			//GET LOCATION FIELD ID
			$loction_field_id = $db->select()
											->from('engine4_list_listing_fields_meta', array('field_id'))
											->where('type = ?', 'location')
											->where('alias = ?', 'location')
											->order('field_id ASC')
											->limit(1)
											->query()
											->fetchColumn();

			if(!empty($loction_field_id)) {

				//GET ALL LISTS
				$lists = $db->select()
												->from('engine4_list_listings', 'listing_id')
												->query()
												->fetchAll();
		
				if (!empty($lists)) {
					foreach($lists as $list) {
						$listing_id = $list['listing_id'];
						$loction = $db->select()
														->from('engine4_list_listing_fields_values', array('value'))
														->where('field_id = ?', $loction_field_id)
														->where('item_id = ?', $listing_id)
														->limit(1)
														->query()
														->fetchColumn();
					
						if(!empty($loction)) {
							//$db->query("UPDATE `engine4_list_listings` SET `location` = '$loction' WHERE `engine4_list_listings`.`listing_id` = $listing_id LIMIT 1;");
							$db->update('engine4_list_listings', array('location' => $loction), array('listing_id = ?' => $listing_id));
						}
					}
				}

				$db->delete('engine4_list_listing_fields_maps', array(
						'child_id = ?' => $loction_field_id,
				));
				
				$db->delete('engine4_list_listing_fields_meta', array(
						'field_id = ?' => $loction_field_id,
						'type = ?' => 'location',
				));

				$db->delete('engine4_list_listing_fields_values', array(
						'field_id = ?' => $loction_field_id,
				));
			}
			else {

				//SET GLOBAL SETTING NO
				$location_setting = $db->select()
												->from('engine4_core_settings', array('value'))
												->where('name = ?', 'list.locationfield')
												->limit(1)
												->query()
												->fetchColumn();

				if(empty($location_setting)) {
					$db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
					('list.locationfield', '0');");
				}
			}
			//END IMPORT ALL LOCATION DATA

			$field_id = $db->select()
											->from('engine4_list_listing_fields_meta', array('field_id'))
											->where('type = ?', 'profile_type')
											->where('alias = ?', 'profile_type')
											->limit(1)
											->query()
											->fetchColumn();

			if(empty($field_id)) {
				$db->query("INSERT IGNORE INTO `engine4_list_listing_fields_meta` (`type`, `label`, `description`, `alias`, `required`, `config`, `validators`, `filters`, `display`, `search`) VALUES ('profile_type', 'Default Type', '', 'profile_type', 1, '', NULL, NULL, 0, 2)");

				$field_id = $db->select()
								->from('engine4_list_listing_fields_meta', array('field_id'))
								->where('type = ?', 'profile_type')
								->where('alias = ?', 'profile_type')
								->limit(1)
								->query()
								->fetchColumn();

				if(!empty($field_id)) {
					$db->query("INSERT IGNORE INTO `engine4_list_listing_fields_options` (`field_id`, `label`, `order`) VALUES ($field_id, 'Default Type', 0)");

					$option_id = $db->select()
									->from('engine4_list_listing_fields_options', array('option_id'))
									->where('field_id = ?', $field_id)
									->where('label = ?', 'Default Type')
									->limit(1)
									->query()
									->fetchColumn();
					
					if(!empty($option_id))
					{
						$db->query("UPDATE `engine4_list_listing_fields_maps` SET `field_id` = $field_id, `option_id` = $option_id WHERE `field_id` = 0 AND `option_id` = 0");
						$db->query("INSERT IGNORE INTO `engine4_list_listing_fields_maps` (`field_id`, `option_id`, `child_id`, `order`) VALUES (0, 0, $field_id, 1)");
						$db->query("ALTER TABLE `engine4_list_listing_fields_search` ADD `profile_type` SMALLINT( 11 ) UNSIGNED DEFAULT NULL , ADD INDEX ( `profile_type` )");
						$db->query("UPDATE `engine4_list_listing_fields_search` SET `profile_type` = $option_id");

						if (!empty($lists)) {
							foreach($lists as $list)
							{
								$listing_id = $list['listing_id'];
								$db->query("INSERT IGNORE INTO `engine4_list_listing_fields_values` (`item_id`, `field_id`, `index`, `value`) VALUES ($listing_id, $field_id, 0, $option_id)");
							}
						}

						$table_exist = $db->query("SHOW TABLES LIKE 'engine4_list_listings'")->fetch();
						$column_exist = $db->query("SHOW COLUMNS FROM engine4_list_listings LIKE 'profile_type'")->fetch();
						if (!empty($table_exist) && empty($column_exist)) {
							$db->query("ALTER TABLE `engine4_list_listings` ADD `profile_type` INT( 11 ) NOT NULL DEFAULT '0'");
							$db->query("UPDATE `engine4_list_listings` SET `profile_type` = $option_id");
						}

						$db->query("CREATE TABLE IF NOT EXISTS `engine4_list_profilemaps` (`profilemap_id` int(11) unsigned NOT NULL AUTO_INCREMENT, `category_id` int(11) NOT NULL, `profile_type` int(11) NOT NULL, PRIMARY KEY (`profilemap_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

						$categories = $db->select()
														->from('engine4_list_categories', 'category_id')
														->where('cat_dependency = ?', 0)
														->where('subcat_dependency = ?', 0)
														->query()
														->fetchAll();

						if (!empty($categories)) {
							foreach($categories as $category)
							{
								$category_id = $category['category_id'];
								$db->query("INSERT IGNORE INTO `engine4_list_profilemaps` (`category_id`, `profile_type`) VALUES ($category_id, $option_id)");
							}
						}
					}
				}
			}
		}

    $table_import_exist = $db->query('SHOW TABLES LIKE \'engine4_list_imports\'')->fetch();
    if(!empty($table_import_exist)) {
			$locationColumn = $db->query("SHOW COLUMNS FROM engine4_list_imports LIKE 'location'")->fetch();
			if(!empty($locationColumn)) {
				$db->query("ALTER TABLE `engine4_list_imports` CHANGE `location` `location` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;");
			}
    }

    $table_exist = $db->query('SHOW TABLES LIKE \'engine4_list_locations\'')->fetch();
    if(!empty($table_exist)) {		
			$locationtableColumn = $db->query("SHOW COLUMNS FROM engine4_list_locations LIKE 'location'")->fetch();
			if(!empty($locationtableColumn)) {
				$db->query("ALTER TABLE `engine4_list_locations` CHANGE `location` `location` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;");
			}

			$locationtableCountryColumn = $db->query("SHOW COLUMNS FROM engine4_list_locations LIKE 'country'")->fetch();
			if(!empty($locationtableCountryColumn)) {
				$db->query("ALTER TABLE `engine4_list_locations` CHANGE `country` `country` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;");
			}

			$locationtableStateColumn = $db->query("SHOW COLUMNS FROM engine4_list_locations LIKE 'state'")->fetch();
			if(!empty($locationtableStateColumn)) {
				$db->query("ALTER TABLE `engine4_list_locations` CHANGE `state` `state` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;");
			}

			$locationtablezipcodeColumn = $db->query("SHOW COLUMNS FROM engine4_list_locations LIKE 'zipcode'")->fetch();
			if(!empty($locationtablezipcodeColumn)) {
				$db->query("ALTER TABLE `engine4_list_locations` CHANGE `zipcode` `zipcode` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;");
			}

			$locationtablecityColumn = $db->query("SHOW COLUMNS FROM engine4_list_locations LIKE 'city'")->fetch();
			if(!empty($locationtablecityColumn)) {
				$db->query("ALTER TABLE `engine4_list_locations` CHANGE `city` `city` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;");
			}

			$locationtablezoomColumn = $db->query("SHOW COLUMNS FROM engine4_list_locations LIKE 'zoom'")->fetch();
			if(!empty($locationtablezoomColumn)) {
				$db->query("ALTER TABLE `engine4_list_locations` CHANGE `zoom` `zoom` TINYINT( 2 ) NOT NULL;");
			}
		}

    $table_exist = $db->query('SHOW TABLES LIKE \'engine4_list_topicwatches\'')->fetch();
    if(!empty($table_exist)) {
			$topicwatchestableresourceidColumn = $db->query("SHOW COLUMNS FROM engine4_list_topicwatches LIKE 'resource_id'")->fetch();
			if(!empty($topicwatchestableresourceidColumn)) {
				$db->query("ALTER TABLE `engine4_list_topicwatches` CHANGE `resource_id` `resource_id` INT( 11 ) UNSIGNED NOT NULL;");
			}

			$topicwatchestabletopicidColumn = $db->query("SHOW COLUMNS FROM engine4_list_topicwatches LIKE 'topic_id'")->fetch();
			if(!empty($topicwatchestabletopicidColumn)) {
				$db->query("ALTER TABLE `engine4_list_topicwatches` CHANGE `topic_id` `topic_id` INT( 11 ) UNSIGNED NOT NULL;");
			}

			$topicwatchestableuseridColumn = $db->query("SHOW COLUMNS FROM engine4_list_topicwatches LIKE 'user_id'")->fetch();
			if(!empty($topicwatchestableuseridColumn)) {
				$db->query("ALTER TABLE `engine4_list_topicwatches` CHANGE `user_id` `user_id` INT( 11 ) UNSIGNED NOT NULL;");
			}
		}


   //END PROFILE MAPPING WORK

//END ------------------------WORK FOR 4.2.7 RELEASE

		$listingTable = $db->query("SHOW TABLES LIKE 'engine4_list_listings'")->fetch();
    if(!empty($listingTable)) {
			$networks_privacy = $db->query("SHOW COLUMNS FROM engine4_list_listings LIKE 'networks_privacy'")->fetch();
			if(empty($networks_privacy)) {
				$db->query("ALTER TABLE `engine4_list_listings` ADD `networks_privacy` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;");
			}
    }
    
    parent::onInstall();
  }
  
  
  function onDisable() {
  
    $db = $this->getDb();
    
    $select = new Zend_Db_Select($db);
    $select
					->from('engine4_core_modules')
					->where('name = ?', 'sitepage');
    $check_module_sitepage = $select->query()->fetchObject();
    if (!empty($check_module_sitepage)) {
			$db->query("UPDATE `engine4_core_menuitems` SET  `enabled` =  '0' WHERE  `engine4_core_menuitems`.`name` ='sitepage_list_gutter_create';");
    }
    
    $select = new Zend_Db_Select($db);
    $select
					->from('engine4_core_modules')
					->where('name = ?', 'sitebusiness');
    $check_module_sitebusiness = $select->query()->fetchObject();
    if (!empty($check_module_sitebusiness)) {
			$db->query("UPDATE `engine4_core_menuitems` SET  `enabled` =  '0' WHERE  `engine4_core_menuitems`.`name` ='sitebusiness_list_gutter_create';");
    }
    
    $select = new Zend_Db_Select($db);
    $select
					->from('engine4_core_modules')
					->where('name = ?', 'sitegroup');
    $check_module_sitegroup = $select->query()->fetchObject();
    if (!empty($check_module_sitegroup)) {
			$db->query("UPDATE `engine4_core_menuitems` SET  `enabled` =  '0' WHERE  `engine4_core_menuitems`.`name` ='sitegroup_list_gutter_create';");
    }
    
    parent::onDisable();
  }
  
  public function onEnable() {
  
    $db = $this->getDb();
    
    $select = new Zend_Db_Select($db);
    $select
					->from('engine4_core_modules')
					->where('name = ?', 'sitepage');
    $check_module_sitepage = $select->query()->fetchObject();
    if (!empty($check_module_sitepage)) {
			$db->query("UPDATE `engine4_core_menuitems` SET  `enabled` =  '1' WHERE  `engine4_core_menuitems`.`name` ='sitepage_list_gutter_create';");
    }
    
    $select = new Zend_Db_Select($db);
    $select
					->from('engine4_core_modules')
					->where('name = ?', 'sitebusiness');
    $check_module_sitebusiness = $select->query()->fetchObject();
    if (!empty($check_module_sitebusiness)) {
			$db->query("UPDATE `engine4_core_menuitems` SET  `enabled` =  '1' WHERE  `engine4_core_menuitems`.`name` ='sitebusiness_list_gutter_create';");
    }
    
    $select = new Zend_Db_Select($db);
    $select
					->from('engine4_core_modules')
					->where('name = ?', 'sitegroup');
    $check_module_sitegroup = $select->query()->fetchObject();
    if (!empty($check_module_sitegroup)) {
			$db->query("UPDATE `engine4_core_menuitems` SET  `enabled` =  '1' WHERE  `engine4_core_menuitems`.`name` ='sitegroup_list_gutter_create';");
    }

    parent::onEnable();
  }

  public function setActivityFeeds() {
    $db = $this->getDb();
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'nestedcomment')
            ->where('enabled = ?', 1);
    $is_nestedcomment_object = $select->query()->fetchObject();
    if ($is_nestedcomment_object) {
        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("nestedcomment_list_listing", "list", \'{item:$subject} replied to a comment on {item:$owner}\'\'s listing {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
    }
  }   
}
