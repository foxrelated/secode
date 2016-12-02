<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitetagcheckin_Installer extends Engine_Package_Installer_Module {

  function onPreinstall() {
    $db = $this->getDb();
    $PRODUCT_TYPE = 'sitetagcheckin';
    $PLUGIN_TITLE = 'Sitetagcheckin';
    $PLUGIN_VERSION = '4.8.10p1';
    $PLUGIN_CATEGORY = 'plugin';
    $PRODUCT_DESCRIPTION = 'Geo-Location, Geo-Tagging, Check-Ins & Proximity Search Plugin';
    $_PRODUCT_FINAL_FILE = 0;
    $SocialEngineAddOns_version = '4.8.11';
    $PRODUCT_TITLE = 'Geo-Location, Geo-Tagging, Check-Ins & Proximity Search Plugin';
    $getErrorMsg = $this->getVersion();
    if (!empty($getErrorMsg)) {
      return $this->_error($getErrorMsg);
    }
    $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
    $is_file = file_exists($file_path);

    if (empty($is_file)) {
      include_once APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/license3.php";
    } else {
      $select = new Zend_Db_Select($db);
      $select->from('engine4_core_modules')->where('name = ?', $PRODUCT_TYPE);
      $is_Mod = $select->query()->fetchObject();
      if (empty($is_Mod)) {
        include_once $file_path;
      }
    }

		$paramsObject = $db->select()
						->from('engine4_activity_actiontypes', array('type', 'body'))
						->where('module like ?', '%sitetagcheckin%')
						->where('type in (?)', array('sitetagcheckin_add_to_map', 'sitetagcheckin_lct_add_to_map'))
						->query()
						->fetchAll();

		foreach($paramsObject as $params) {
			$type = $params['type'];
			$haystack = $params['body'];
			$needle = '{var:$event_date}';
			if (strpos($haystack,$needle) !== false) {
				$params = str_replace($needle, '{VarCheckin:$event_date}', $haystack);
				$db->update('engine4_activity_actiontypes', array('body' => "$params"), array('type =?' =>$type));
			}
		}

    parent::onPreinstall();
  }
  
  function onInstall() {
    $db = $this->getDb();
		
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitemember')
            ->where('enabled = ?', 1);
    $is_sitemember_object = $select->query()->fetchObject();
    if(!empty($is_sitemember_object)) {
        $db->query("UPDATE  `engine4_core_menuitems` SET  `enabled` =  '0' WHERE  `engine4_core_menuitems`.`name` = 'sitetagcheckin_admin_main_userlocations' LIMIT 1 ;");
    }
    
    //START ALBUM LOCATION WORK
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'album')
            ->where('enabled = ?', 1);
    $check_album = $select->query()->fetchObject();

    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'advalbum')
            ->where('enabled = ?', 1);
    $check_advalbum = $select->query()->fetchObject();
    
    if (!empty($check_album) || !empty($check_advalbum)) {
      
      $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitetagcheckin_admin_main_albumlocations","sitetagcheckin", "Album Locations", "",\'{"route":"admin_default","module":"sitetagcheckin","controller":"settings","action":"albumlocations"}\', "sitetagcheckin_admin_main", "", "0", "0", "7");');
      
			$mainManage = $db->query("SELECT * FROM  `engine4_core_menuitems` WHERE  `name` LIKE  '%album_main_manage%'")->fetchAll();
			if (!empty($mainManage)) {
				$db->query("UPDATE `engine4_core_menuitems` SET `order` = '3' WHERE `engine4_core_menuitems`.`name` = 'album_main_manage' LIMIT 1 ;");
			}

			$mainCreate = $db->query("SELECT * FROM  `engine4_core_menuitems` WHERE  `name` LIKE  '%album_main_upload%'")->fetchAll();
			if (!empty($mainCreate)) {
				$db->query("UPDATE `engine4_core_menuitems` SET `order` = '4' WHERE `engine4_core_menuitems`.`name` = 'album_main_upload' LIMIT 1 ;");
			}

      $table_exist = $db->query('SHOW TABLES LIKE \'engine4_album_albums\'')->fetch();
			if (!empty($table_exist)) {
				$column_exist = $db->query('SHOW COLUMNS FROM engine4_album_albums LIKE \'seao_locationid\'')->fetch();
				if (empty($column_exist)) {
					$db->query("ALTER TABLE engine4_album_albums ADD `seao_locationid` INT(11) NOT NULL");
				}
			}
			
			$table_exist = $db->query('SHOW TABLES LIKE \'engine4_album_albums\'')->fetch();
			if (!empty($table_exist)) {
				$column_exist = $db->query('SHOW COLUMNS FROM engine4_album_albums LIKE \'location\'')->fetch();
				if (empty($column_exist)) {
					$db->query("ALTER TABLE engine4_album_albums ADD `location` VARCHAR(264) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
				}
			}


			//START THE WORK FOR MAKE WIDGETIZE PAGE FOR ALBUM LOCATION.
			$select = new Zend_Db_Select($db);
			$select
							->from('engine4_core_pages')
							->where('name = ?', 'sitetagcheckin_location_albumby-locations')
							->limit(1);
			$info = $select->query()->fetch();
			if ( empty($info) ) {
				$db->insert('engine4_core_pages', array(
						'name' => 'sitetagcheckin_location_albumby-locations',
						'displayname' => 'Browse Albums’ Locations',
						'title' => 'Browse Albums’ Locations',
						'description' => 'Browse Albums’ Locations',
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
					'name' => 'sitetagcheckin.albumlocation-search',
					'parent_content_id' => $middle_id,
					'order' => 2,
					'params' => '',
					));

				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitetagcheckin.bylocation-album',
					'parent_content_id' => $middle_id,
					'order' => 3,
					'params' => '{"title":"","titleCount":true,"order_by":"2","nomobile":"0"}',
				));
			}
			//END THE WORK FOR MAKE WIDGETIZE PAGE OF ALBUMLOCATIO OR MAP.

			//START THE WORK FOR MAKE WIDGETIZE PAGE OF ALBUMLOCATIO OR MAP.
			$select = new Zend_Db_Select($db);
			$select
							->from('engine4_core_pages') 
							->where('name = ?', 'sitetagcheckin_location_mobilealbumby-locations')
							->limit(1);
			$info = $select->query()->fetch();
			if ( empty($info) ) {
				$db->insert('engine4_core_pages', array(
						'name' => 'sitetagcheckin_location_mobilealbumby-locations',
						'displayname' => 'Mobile Browse Albums’ Locations',
						'title' => 'Mobile Browse Albums’ Locations',
						'description' => 'Mobile Browse Albums’ Locations',
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
					'name' => 'sitetagcheckin.albumlocation-search',
					'parent_content_id' => $middle_id,
					'order' => 2,
					'params' => '',
					));

				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitetagcheckin.bylocation-album',
					'parent_content_id' => $middle_id,
					'order' => 3,
					'params' => '{"title":"","titleCount":true,"order_by":"2","nomobile":"0"}',
				));
			}
			//END THE WORK FOR MAKE WIDGETIZE PAGE OF ALBUMLOCATIO OR MAP.
    }
    //END ALBUM LOCATION WORK
    
    //START VIDEO LOCATION WORK
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'video')
            ->where('enabled = ?', 1);
    $check_video = $select->query()->fetchObject();

    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'advvideo')
            ->where('enabled = ?', 1);
    $check_advvideo = $select->query()->fetchObject();
    
    if (!empty($check_video) || !empty($check_advvideo)) {
    
			$mainManage = $db->query("SELECT * FROM  `engine4_core_menuitems` WHERE  `name` LIKE  '%video_main_manage%'")->fetchAll();
			if (!empty($mainManage)) {
				$db->query("UPDATE `engine4_core_menuitems` SET `order` = '3' WHERE `engine4_core_menuitems`.`name` = 'video_main_manage' LIMIT 1 ;");
			}

			$mainCreate = $db->query("SELECT * FROM  `engine4_core_menuitems` WHERE  `name` LIKE  '%video_main_create%'")->fetchAll();
			if (!empty($mainCreate)) {
				$db->query("UPDATE `engine4_core_menuitems` SET `order` = '4' WHERE `engine4_core_menuitems`.`name` = 'video_main_create' LIMIT 1 ;");
			}

      $table_exist = $db->query('SHOW TABLES LIKE \'engine4_video_videos\'')->fetch();
			if (!empty($table_exist)) {
				$column_exist = $db->query('SHOW COLUMNS FROM engine4_video_videos LIKE \'seao_locationid\'')->fetch();
				if (empty($column_exist)) {
					$db->query("ALTER TABLE engine4_video_videos ADD `seao_locationid` INT(11) NOT NULL");
				}
			}
			
			$table_exist = $db->query('SHOW TABLES LIKE \'engine4_video_videos\'')->fetch();
			if (!empty($table_exist)) {
				$column_exist = $db->query('SHOW COLUMNS FROM engine4_video_videos LIKE \'location\'')->fetch();
				if (empty($column_exist)) {
					$db->query("ALTER TABLE engine4_video_videos ADD `location` VARCHAR(264) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
				}
			}


			//START THE WORK FOR MAKE WIDGETIZE PAGE FOR VIDEO LOCATION.
			$select = new Zend_Db_Select($db);
			$select
							->from('engine4_core_pages')
							->where('name = ?', 'sitetagcheckin_location_videoby-locations')
							->limit(1);
			$info = $select->query()->fetch();
			if ( empty($info) ) {
				$db->insert('engine4_core_pages', array(
						'name' => 'sitetagcheckin_location_videoby-locations',
						'displayname' => 'Browse Videos’ Locations',
						'title' => 'Browse Videos’ Locations',
						'description' => 'Browse Videos’ Locations',
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
					'name' => 'sitetagcheckin.videolocation-search',
					'parent_content_id' => $middle_id,
					'order' => 2,
					'params' => '',
					));

				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitetagcheckin.bylocation-video',
					'parent_content_id' => $middle_id,
					'order' => 3,
					'params' => '{"title":"","titleCount":true,"order_by":"2","nomobile":"0"}',
				));
			}
			//END THE WORK FOR MAKE WIDGETIZE PAGE OF VIDEOLOCATIO OR MAP.

			//START THE WORK FOR MAKE WIDGETIZE PAGE OF VIDEOLOCATIO OR MAP.
			$select = new Zend_Db_Select($db);
			$select
							->from('engine4_core_pages') 
							->where('name = ?', 'sitetagcheckin_location_mobilevideoby-locations')
							->limit(1);
			$info = $select->query()->fetch();
			if ( empty($info) ) {
				$db->insert('engine4_core_pages', array(
						'name' => 'sitetagcheckin_location_mobilevideoby-locations',
						'displayname' => 'Mobile Browse Videos’ Locations',
						'title' => 'Mobile Browse Videos’ Locations',
						'description' => 'Mobile Browse Videos’ Locations',
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
					'name' => 'sitetagcheckin.videolocation-search',
					'parent_content_id' => $middle_id,
					'order' => 2,
					'params' => '',
					));

				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitetagcheckin.bylocation-video',
					'parent_content_id' => $middle_id,
					'order' => 3,
					'params' => '{"title":"","titleCount":true,"order_by":"2","nomobile":"0"}',
				));
			}
			//END THE WORK FOR MAKE WIDGETIZE PAGE OF VIDEOLOCATIO OR MAP.
    }
    //END VIDEO LOCATION WORK

    $db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` ='sitetagcheckin';");
    
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'event')
            ->where('enabled = ?', 1);
    $check_event = $select->query()->fetchObject();
    
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'ynevent')
            ->where('enabled = ?', 1);
    $check_ynevent = $select->query()->fetchObject();
    
    if (!empty($check_event) || !empty($check_ynevent)) {
			$table_exist = $db->query('SHOW TABLES LIKE \'engine4_event_events\'')->fetch();
			if (!empty($table_exist)) {
				$column_exist = $db->query('SHOW COLUMNS FROM engine4_event_events LIKE \'seao_locationid\'')->fetch();
				if (empty($column_exist)) {
					$db->query("ALTER TABLE `engine4_event_events` ADD `seao_locationid` INT( 11 ) NOT NULL");
				}
			}

			$mainManage = $db->query("SELECT * FROM  `engine4_core_menuitems` WHERE  `name` LIKE  '%event_main_manage%'")->fetchAll();
			if (!empty($mainManage)) {
				$db->query("UPDATE `engine4_core_menuitems` SET `order` = '4' WHERE `engine4_core_menuitems`.`name` = 'event_main_manage' LIMIT 1 ;");
			}

			$mainCreate = $db->query("SELECT * FROM  `engine4_core_menuitems` WHERE  `name` LIKE  '%event_main_create%'")->fetchAll();
			if (!empty($mainCreate)) {
				$db->query("UPDATE `engine4_core_menuitems` SET `order` = '5' WHERE `engine4_core_menuitems`.`name` = 'event_main_create' LIMIT 1 ;");
			}
			
			//START WORK FOR SINK LOCATION WITH NEW LOCATION TABLE.
			$table_exist = $db->query('SHOW TABLES LIKE \'engine4_event_events\'')->fetch();
			if (!empty($table_exist)) {
				$select = new Zend_Db_Select($db);
				$select
						->from('engine4_event_events', array('event_id', 'seao_locationid', 'location'))
						->where('location <> ?', '')
						->where('seao_locationid <> ?', 0);
				$result = $select->query()->fetchAll();
				
				if (!empty($result)) {
					foreach ($result as $results) {

						$select = new Zend_Db_Select($db);
						$select
								->from('engine4_seaocore_locations')
								->where('location_id = ?', $results['seao_locationid']);
						$resultlocations = $select->query()->fetchAll();
						if (!empty($resultlocations)) {
							foreach($resultlocations as $resultlocation) {
								$resultlocation['location'] = str_replace("'", "\'", $resultlocation['location']);
							  $resultlocation['formatted_address'] = str_replace("'", "\'", $resultlocation['formatted_address']);
							  	$resultlocation['country'] = str_replace("'", "\'", $resultlocation['country']);
							  $resultlocation['state'] = str_replace("'", "\'", $resultlocation['state']);
							  $resultlocation['city'] = str_replace("'", "\'", $resultlocation['city']);
							  $resultlocation['address'] = str_replace("'", "\'", $resultlocation['address']);
								$db->query("INSERT IGNORE INTO `engine4_seaocore_locationitems` (`resource_type`, `resource_id`, `location`, `latitude`, `longitude`, `formatted_address`, `country`, `state`, `zipcode`, `city`, `address`, `zoom`) VALUES ('event', '" . $results['event_id']. "', '" . $resultlocation['location']. "', '" . $resultlocation['latitude']. "', '" . $resultlocation['longitude']. "', '" . $resultlocation['formatted_address']. "', '" . $resultlocation['country']. "', '" . $resultlocation['state']. "', '" . $resultlocation['zipcode']. "', '" . $resultlocation['city']. "', '" . $resultlocation['address']. "', '" . $resultlocation['zoom']. "')");

								$select = new Zend_Db_Select($db);
								$select
									->from('engine4_seaocore_locationitems', array('locationitem_id'))
									->where('resource_type = ?', 'event')
									->where('resource_id = ?', $results['event_id']); 
								$info = $select->query()->fetch();
								
								if (!empty($info)) {
									$db->query("UPDATE `engine4_event_events` SET `seao_locationid` = '" .$info['locationitem_id']. "' WHERE `engine4_event_events`.`event_id` = '" .$results['event_id']. "';");
								}
							}
						}
					}
				}
			}
			// END WORK FOR SINK LOCATION WITH NEW LOCATION TABLE.
    }

    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'group')
            ->where('enabled = ?', 1);
    $check_group = $select->query()->fetchObject();
    
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'advgroup')
            ->where('enabled = ?', 1);
    $check_advgroup = $select->query()->fetchObject();
    if (!empty($check_group) || !empty($check_advgroup)) {
    	$mainManage = $db->query("SELECT * FROM  `engine4_core_menuitems` WHERE  `name` LIKE  '%group_main_manage%'")->fetchAll();
			if (!empty($mainManage)) {
				$db->query("UPDATE `engine4_core_menuitems` SET `order` = '3' WHERE `engine4_core_menuitems`.`name` = 'group_main_manage' LIMIT 1 ;");
			}

			$mainCreate = $db->query("SELECT * FROM  `engine4_core_menuitems` WHERE  `name` LIKE  '%group_main_create%'")->fetchAll();
			if (!empty($mainCreate)) {
				$db->query("UPDATE `engine4_core_menuitems` SET `order` = '4' WHERE `engine4_core_menuitems`.`name` = 'group_main_create' LIMIT 1 ;");
			}

      $table_exist = $db->query('SHOW TABLES LIKE \'engine4_group_groups\'')->fetch();
			if (!empty($table_exist)) {
				$column_exist = $db->query('SHOW COLUMNS FROM engine4_group_groups LIKE \'seao_locationid\'')->fetch();
				if (empty($column_exist)) {
					$db->query("ALTER TABLE engine4_group_groups ADD `seao_locationid` INT( 11 ) NOT NULL");
				}
			}
			
			$table_exist = $db->query('SHOW TABLES LIKE \'engine4_group_groups\'')->fetch();
			if (!empty($table_exist)) {
				$column_exist = $db->query('SHOW COLUMNS FROM engine4_group_groups LIKE \'location\'')->fetch();
				if (empty($column_exist)) {
					$db->query("ALTER TABLE engine4_group_groups ADD `location` VARCHAR( 264 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
				}
			}
    }
    
    $table_exist = $db->query('SHOW TABLES LIKE \'engine4_album_photos\'')->fetch();
    if (!empty($table_exist)) {
      $column_exist = $db->query('SHOW COLUMNS FROM engine4_album_photos LIKE \'skip_photo\'')->fetch();
      if (empty($column_exist)) {
        $db->query("ALTER TABLE `engine4_album_photos` ADD  `skip_photo` TINYINT( 1 ) NOT NULL DEFAULT  '0'");
      }
    }

    $table_exist = $db->query('SHOW TABLES LIKE \'engine4_sitebusinessevent_events\'')->fetch();
    if (!empty($table_exist)) {
      $column_exist = $db->query('SHOW COLUMNS FROM engine4_sitebusinessevent_events LIKE \'seao_locationid\'')->fetch();
      if (empty($column_exist)) {
        $db->query("ALTER TABLE `engine4_sitebusinessevent_events` ADD `seao_locationid` INT( 11 ) NOT NULL");
      }
    }
    
    $table_exist = $db->query('SHOW TABLES LIKE \'engine4_users\'')->fetch();
    if (!empty($table_exist)) {
      $column_exist = $db->query('SHOW COLUMNS FROM engine4_users LIKE \'seao_locationid\'')->fetch();
      if (empty($column_exist)) {
        $db->query("ALTER TABLE engine4_users ADD `seao_locationid` INT( 11 ) NOT NULL");
      }
    }
    
    $table_exist = $db->query('SHOW TABLES LIKE \'engine4_users\'')->fetch();
    if (!empty($table_exist)) {
      $column_exist = $db->query('SHOW COLUMNS FROM engine4_users LIKE \'location\'')->fetch();
      if (empty($column_exist)) {
        $db->query("ALTER TABLE engine4_users ADD `location` VARCHAR( 264 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
      }
    }



    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'group')
            ->where('enabled = ?', 1);
    $check_group = $select->query()->fetchObject();
    if (!empty($check_group)) {

			//START THE WORK FOR MAKE WIDGETIZE PAGE OF GROUPLOCATIO OR MAP.
			$select = new Zend_Db_Select($db);
			$select
							->from('engine4_core_pages')
							->where('name = ?', 'sitetagcheckin_location_by-locations')
							->limit(1);
			$info = $select->query()->fetch();
			if ( empty($info) ) {
				$db->insert('engine4_core_pages', array(
						'name' => 'sitetagcheckin_location_by-locations',
						'displayname' => 'Browse Groups’ Locations',
						'title' => 'Browse Groups’ Locations',
						'description' => 'Browse Groups’ Locations',
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
					'name' => 'sitetagcheckin.grouplocation-search',
					'parent_content_id' => $middle_id,
					'order' => 2,
					'params' => '',
					));

				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitetagcheckin.bylocation-group',
					'parent_content_id' => $middle_id,
					'order' => 3,
					'params' => '{"title":"","titleCount":true,"order_by":"2","nomobile":"0"}',
				));
			}
			//END THE WORK FOR MAKE WIDGETIZE PAGE OF GROUPLOCATIO OR MAP.

			//START THE WORK FOR MAKE WIDGETIZE PAGE OF GROUPLOCATIO OR MAP.
			$select = new Zend_Db_Select($db);
			$select
							->from('engine4_core_pages') 
							->where('name = ?', 'sitetagcheckin_location_mobileby-locations')
							->limit(1);
			$info = $select->query()->fetch();
			if ( empty($info) ) {
				$db->insert('engine4_core_pages', array(
						'name' => 'sitetagcheckin_location_mobileby-locations',
						'displayname' => 'Mobile Browse Groups’ Locations',
						'title' => 'Mobile Browse Groups’ Locations',
						'description' => 'Mobile Browse Groups’ Locations',
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
					'name' => 'sitetagcheckin.grouplocation-search',
					'parent_content_id' => $middle_id,
					'order' => 2,
					'params' => '',
					));

				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitetagcheckin.bylocation-group',
					'parent_content_id' => $middle_id,
					'order' => 3,
					'params' => '{"title":"","titleCount":true,"order_by":"2","nomobile":"0"}',
				));
			}
			//END THE WORK FOR MAKE WIDGETIZE PAGE OF GROUPLOCATIO OR MAP.
    }


    //START THE WORK FOR MAKE WIDGETIZE PAGE OF USERLOCATIO OR MAP.
		$select = new Zend_Db_Select($db);
		$select
						->from('engine4_core_pages')
						->where('name = ?', 'sitetagcheckin_location_userby-locations')
						->limit(1);
		$info = $select->query()->fetch();
		if ( empty($info) ) {
			$db->insert('engine4_core_pages', array(
					'name' => 'sitetagcheckin_location_userby-locations',
					'displayname' => 'Browse Members’ Locations',
					'title' => 'Browse Members’ Locations',
					'description' => 'Browse Members’ Locations',
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
			
// 			$db->insert('engine4_core_content', array(
// 					'page_id' => $page_id,
// 					'type' => 'container',
// 					'name' => 'middle',
// 					'parent_content_id' => $top_id,
// 					'params' => '',
// 			));
// 			$top_middle_id = $db->lastInsertId('engine4_core_content');
			
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
// 			$db->insert('engine4_core_content', array(
// 					'page_id' => $page_id,
// 					'type' => 'widget',
// 					'name' => 'core.content',
// 					'parent_content_id' => $top_middle_id,
// 					'order' => 1,
// 					'params' => '',
// 			));
			
			//INSERT WIDGET OF LOCATION SEARCH AND CORE CONTENT
			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitetagcheckin.userlocation-search',
				'parent_content_id' => $middle_id,
				'order' => 2,
				'params' => '{"title":"","titleCount":true,"form_options":["street","city","state","country","hasphoto","isonline"],"nomobile":"0","name":"sitetagcheckin.userlocation-search"}',
				));

			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitetagcheckin.bylocation-user',
				'parent_content_id' => $middle_id,
				'order' => 3,
				'params' => '{"title":"","titleCount":true,"order_by":"2","nomobile":"0"}',
			));
		}
    //END THE WORK FOR MAKE WIDGETIZE PAGE OF USERLOCATIO OR MAP.


    //START THE WORK FOR MAKE WIDGETIZE PAGE OF USERLOCATIO OR MAP.
		$select = new Zend_Db_Select($db);
		$select
						->from('engine4_core_pages') 
						->where('name = ?', 'sitetagcheckin_location_usermobileby-locations')
						->limit(1);
		$info = $select->query()->fetch();
		if ( empty($info) ) {
			$db->insert('engine4_core_pages', array(
					'name' => 'sitetagcheckin_location_usermobileby-locations',
					'displayname' => 'Mobile Browse Members’ Locations',
					'title' => 'Mobile Browse Members’ Locations',
					'description' => 'Mobile Browse Members’ Locations',
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
			
// 			$db->insert('engine4_core_content', array(
// 					'page_id' => $page_id,
// 					'type' => 'container',
// 					'name' => 'middle',
// 					'parent_content_id' => $top_id,
// 					'params' => '',
// 			));
// 			$top_middle_id = $db->lastInsertId('engine4_core_content');
			
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
// 			$db->insert('engine4_core_content', array(
// 					'page_id' => $page_id,
// 					'type' => 'widget',
// 					'name' => 'core.content',
// 					'parent_content_id' => $top_middle_id,
// 					'order' => 1,
// 					'params' => '',
// 			));
			
			//INSERT WIDGET OF LOCATION SEARCH AND CORE CONTENT
			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitetagcheckin.userlocation-search',
				'parent_content_id' => $middle_id,
				'order' => 2,
				'params' => '{"title":"","titleCount":true,"form_options":["street","city","state","country","hasphoto","isonline"],"nomobile":"0","name":"sitetagcheckin.userlocation-search"}',
				));

			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitetagcheckin.bylocation-user',
				'parent_content_id' => $middle_id,
				'order' => 3,
				'params' => '{"title":"","titleCount":true,"order_by":"2","nomobile":"0"}',
			));
    }
    //END THE WORK FOR MAKE WIDGETIZE PAGE OF USERLOCATIO OR MAP.
    
    
		$select = new Zend_Db_Select($db);
		$select
           ->from('engine4_core_modules')
           ->where('name = ?', 'sitepage')
           ->where('enabled = ?', 1);
		$is_sitepage_object = $select->query()->fetchObject();
		if(!empty($is_sitepage_object)) {
			$db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
("sitetagcheckin_spal_photo_new", "sitetagcheckin", "{item:$object} added {var:$count} photo(s) to the album {var:$linked_album_title} - {var:$prefixadd} {var:$location}.", 1, 5, 1, 3, 1, 1)');
			$db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
("sitetagcheckin_page_tagged", "sitetagcheckin", "{item:$subject} mentioned your page with a {item:$object:$label}.", "0", "", "1")');
    }

		$select = new Zend_Db_Select($db);
		$select
           ->from('engine4_core_modules')
           ->where('name = ?', 'sitebusiness')
           ->where('enabled = ?', 1);
		$is_sitebusiness_object = $select->query()->fetchObject();
		if(!empty($is_sitebusiness_object)) {
			$db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
("sitetagcheckin_sbal_photo_new", "sitetagcheckin", "{item:$object} added {var:$count} photo(s) to the album {var:$linked_album_title} - {var:$prefixadd} {var:$location}.", 1, 5, 1, 3, 1, 1)');
			$db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
("sitetagcheckin_business_tagged", "sitetagcheckin", "{item:$subject} mentioned your business with a {item:$object:$label}.", "0", "", "1")');
    }

		$select = new Zend_Db_Select($db);
		$select
           ->from('engine4_core_modules')
           ->where('name = ?', 'sitegroup')
           ->where('enabled = ?', 1);
		$is_sitegroup_object = $select->query()->fetchObject();
		if(!empty($is_sitegroup_object)) {
			$db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
("sitetagcheckin_sgal_photo_new", "sitetagcheckin", "{item:$object} added {var:$count} photo(s) to the album {var:$linked_album_title} - {var:$prefixadd} {var:$location}.", 1, 5, 1, 3, 1, 1)');
			$db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
("sitetagcheckin_group_tagged", "sitetagcheckin", "{item:$subject} mentioned your group with a {item:$object:$label}.", "0", "", "1")');

			$db->query('INSERT IGNORE INTO `engine4_sitetagcheckin_contents` (`module`, `resource_type`, `resource_id`, `value`, `default`, `enabled`) VALUES("sitegroup", "sitegroup_group", "group_id", "1", "1", "1"),("sitegroupalbum", "sitegroup_album", "album_id", 1, 1, 1),("sitegroupnote", "sitegroupnote_note", "note_id", 1, 1, 1),("sitegroupevent", "sitegroupevent_event", "event_id", 1, 1, 1),("sitegroupmusic", "sitegroupmusic_playlist", "playlist_id", 1, 1, 1),("sitegroupdiscussion", "sitegroup_topic", "topic_id", 1, 1, 1),("sitegroupvideo", "sitegroupvideo_video", "video_id", 1, 1, 1),("sitegrouppoll", "sitegrouppoll_poll", "poll_id", 1, 1, 1),("sitegroupdocument", "sitegroupdocument_document", "document_id", 1, 1, 1),("sitegroupreview", "sitegroupreview_review", "review_id", 1, 1, 1)');
    }

		$select = new Zend_Db_Select($db);
		$select
           ->from('engine4_core_modules')
           ->where('name = ?', 'sitestore')
           ->where('enabled = ?', 1);
		$is_sitestore_object = $select->query()->fetchObject();
		if(!empty($is_sitestore_object)) {
			$db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
("sitetagcheckin_ssal_photo_new", "sitetagcheckin", "{item:$object} added {var:$count} photo(s) to the album {var:$linked_album_title} - {var:$prefixadd} {var:$location}.", 1, 5, 1, 3, 1, 1)');
			$db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
("sitetagcheckin_store_tagged", "sitetagcheckin", "{item:$subject} mentioned your store with a {item:$object:$label}.", "0", "", "1")');
			$db->query('INSERT IGNORE INTO `engine4_sitetagcheckin_contents` (`module`, `resource_type`, `resource_id`, `value`, `default`, `enabled`) VALUES("sitestore", "sitestore_store", "store_id", "1", "1", "1")');
    }

		$select = new Zend_Db_Select($db);
		$select
           ->from('engine4_core_modules')
           ->where('name = ?', 'siteevent')
           ->where('enabled = ?', 1);
		$is_siteevent_object = $select->query()->fetchObject();
		if(!empty($is_siteevent_object)) {
			$db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
("sitetagcheckin_seal_photo_new", "sitetagcheckin", "{item:$object} added {var:$count} photo(s) to the album {var:$linked_album_title} - {var:$prefixadd} {var:$location}.", 1, 5, 1, 3, 1, 1)');
			$db->query('INSERT IGNORE INTO `engine4_sitetagcheckin_contents` (`module`, `resource_type`, `resource_id`, `value`, `default`, `enabled`) VALUES("siteevent", "siteevent_event", "event_id", "1", "1", "1")');
// 			$db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
// ("sitetagcheckin_event_tagged", "sitetagcheckin", "{item:$subject} mentioned your event with a {item:$object:$label}.", "0", "", "1")');
    }
    
    
    $select = new Zend_Db_Select($db);
		$select
           ->from('engine4_core_modules')
           ->where('name = ?', 'sitereview')
           ->where('enabled = ?', 1);
		$is_sitereview_object = $select->query()->fetchObject();
		if(!empty($is_sitereview_object)) {
			$db->query('INSERT IGNORE INTO `engine4_sitetagcheckin_contents` (`module`, `resource_type`, `resource_id`, `value`, `default`, `enabled`) VALUES("sitereview", "sitereview_listing", "listing_id", "1", "1", "1")');
    }
    
		$table_exist = $db->query('SHOW TABLES LIKE \'engine4_sitetagcheckin_addlocations\'')->fetch();
		if (!empty($table_exist)) {
			$sync_album = $db->query('SHOW COLUMNS FROM engine4_sitetagcheckin_addlocations LIKE \'sync_album\'')->fetch();
			if (empty($sync_album)) {
				$db->query('ALTER TABLE `engine4_sitetagcheckin_addlocations` ADD `sync_album` tinyint(4) NOT NULL DEFAULT "0";');
			}
		}
		
    parent::onInstall();
  }
  
  private function getVersion() {
    $db = $this->getDb();

    $errorMsg = '';
    $finalModules = $getResultArray = array();
    $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

    $modArray = array(
        'sitepage' => '4.2.6p1',
        'advancedactivity' => '4.8.0p4'
    );
    foreach ($modArray as $key => $value) {
      $isMod = $db->query("SELECT * FROM  `engine4_core_modules` WHERE  `name` LIKE  '" . $key . "'")->fetch();
      if (!empty($isMod) && !empty($isMod['version'])) {
        $isModSupport = $this->checkVersion($isMod['version'], $value);
        if (empty($isModSupport)) {
          $finalModules['modName'] = $key;
          $finalModules['title'] = $isMod['title'];
          $finalModules['versionRequired'] = $value;
          $finalModules['versionUse'] = $isMod['version'];
          $getResultArray[] = $finalModules;
        }
      }
    }

    foreach ($getResultArray as $modArray) {
      $errorMsg .= '<div class="tip"><span>Note: Your website does not have the latest version of "' . $modArray['title'] . '". Please upgrade "' . $modArray['title'] . '" on your website to the latest version available in your SocialEngineAddOns Client Area to enable its integration with "Geo-Location, Geo-Tagging, Check-Ins & Proximity Search Plugin".<br/> Please <a href="' . $base_url . '/manage">Click here</a> to go Manage Packages.</span></div>';
    }

    return $errorMsg;
  }
      private function checkVersion($databaseVersion, $checkDependancyVersion) {
        $f = $databaseVersion;
        $s = $checkDependancyVersion;
        if (strcasecmp($f, $s) == 0)
            return -1;

        $fArr = explode(".", $f);
        $sArr = explode('.', $s);
        if (count($fArr) <= count($sArr))
            $count = count($fArr);
        else
            $count = count($sArr);

        for ($i = 0; $i < $count; $i++) {
            $fValue = $fArr[$i];
            $sValue = $sArr[$i];
            if (is_numeric($fValue) && is_numeric($sValue)) {
                if ($fValue > $sValue)
                    return 1;
                elseif ($fValue < $sValue)
                    return 0;
                else {
                    if (($i + 1) == $count) {
                        return -1;
                    } else
                        continue;
                }
            }
            elseif (is_string($fValue) && is_numeric($sValue)) {
                $fsArr = explode("p", $fValue);

                if ($fsArr[0] > $sValue)
                    return 1;
                elseif ($fsArr[0] < $sValue)
                    return 0;
                else {
                    return 1;
                }
            } elseif (is_numeric($fValue) && is_string($sValue)) {
                $ssArr = explode("p", $sValue);

                if ($fValue > $ssArr[0])
                    return 1;
                elseif ($fValue < $ssArr[0])
                    return 0;
                else {
                    return 0;
                }
            } elseif (is_string($fValue) && is_string($sValue)) {
                $fsArr = explode("p", $fValue);
                $ssArr = explode("p", $sValue);
                if ($fsArr[0] > $ssArr[0])
                    return 1;
                elseif ($fsArr[0] < $ssArr[0])
                    return 0;
                else {
                    if ($fsArr[1] > $ssArr[1])
                        return 1;
                    elseif ($fsArr[1] < $ssArr[1])
                        return 0;
                    else {
                        return -1;
                    }
                }
            }
        }
    }
  public function onPostInstall() {

    $db = $this->getDb();
		$select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitemobile')
            ->where('enabled = ?', 1);
    $is_sitemobile_object = $select->query()->fetchObject();
    if(!empty($is_sitemobile_object)) {
			$db->query("INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`) VALUES
('sitetagcheckin','1')");
			$select = new Zend_Db_Select($db);
			$select
							->from('engine4_sitemobile_modules')
							->where('name = ?', 'sitetagcheckin')
							->where('integrated = ?', 0);
			$is_sitemobile_object = $select->query()->fetchObject();
      if($is_sitemobile_object)  {
				$actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
				$controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
				if($controllerName == 'manage' && $actionName == 'install') {
          $view = new Zend_View();
					$baseUrl = ( !empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://':'http://') .  $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
					$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
					$redirector->gotoUrl($baseUrl . 'admin/sitemobile/module/enable-mobile/enable_mobile/1/name/sitetagcheckin/integrated/0/redirect/install');
				} 
      }
    }
  }
}