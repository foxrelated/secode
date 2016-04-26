<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitegroupevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: WidgetSettings.php 6590 2010-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

//START LANGUAGE WORK
Engine_Api::_()->getApi('language', 'sitegroup')->languageChanges();
//END LANGUAGE WORK

//GET DB
$db = Zend_Db_Table_Abstract::getDefaultAdapter();

// ********* Start OnInstall() Code *******************


		$column_exist_featured = $db->query('SHOW COLUMNS FROM engine4_sitegroup_photos LIKE \'featured\'')->fetch();
		if (empty($column_exist_featured)) {
			$db->query("ALTER TABLE `engine4_sitegroup_photos` ADD `featured` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `photo_hide`");
		}

		$column_exist_album_featured = $db->query('SHOW COLUMNS FROM engine4_sitegroup_albums LIKE \'featured\'')->fetch();
		if (empty($column_exist_album_featured)) {
			$db->query("ALTER TABLE `engine4_sitegroup_albums` ADD `featured` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `type` ");
		}

    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_settings')
            ->where('name = ?', 'sitegroupalbum.is.active')
            ->limit(1);
    $sitegroupalbum_settings = $select->query()->fetchAll();

    // WIDGET SETTING WORK
		$select = new Zend_Db_Select($db);
		$select
		->from('engine4_core_menuitems')
		->where('name = ?', 'sitegroupalbum_admin_widget_settings')
		->limit(1);
		$info = $select->query()->fetch();
		if (empty($info) && !empty($sitegroupalbum_settings)) {
		$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enabled`) VALUES
		("sitegroupalbum_admin_widget_settings", "sitegroupalbum", "Widget Settings", "", \'{"route":"admin_default","module":"sitegroupalbum","controller":"album"}\', "sitegroupalbum_admin_main", "", 0, 3, 1)');
		}

    $select = new Zend_Db_Select($db);
    $select->from('engine4_core_modules')
            ->where('name = ?', 'sitegroupalbum')
            ->where('version < ?', '4.2.1');
    $is_enabled = $select->query()->fetchObject();
		if (!empty($is_enabled)) {
			$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitegroupalbum_admin_main_photo_featured", "sitegroupalbum", "Featured Photos", "", \'{"route":"admin_default","module":"sitegroupalbum","controller":"settings", "action": "featured"}\', "sitegroupalbum_admin_main", "", 1, 0, 4)');

			$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitegroupalbum_admin_submain_album_tab", "sitegroupalbum", "Tabbed Albums Widget", "", \'{"route":"admin_default","module":"sitegroupalbum","controller":"album", "action": "index"}\', "sitegroupalbum_admin_submain", "", 1, 0, 2)');


			$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitegroupalbum_admin_submain_photo_tab", "sitegroupalbum", "Tabbed Photos Widget", "", \'{"route":"admin_default","module":"sitegroupalbum","controller":"photo", "action": "index"}\', "sitegroupalbum_admin_submain", "", 1, 0, 3)');

			$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitegroupalbum_admin_submain_dayitems", "sitegroupalbum", "Album of the Day", "", \'{"route":"admin_default","module":"sitegroupalbum","controller":"album", "action": "manage-day-items"}\', "sitegroupalbum_admin_submain", "", 1, 0, 4)');

			$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitegroupalbum_admin_submain_photo_items", "sitegroupalbum", "Photo of the Day", "", \'{"route":"admin_default","module":"sitegroupalbum","controller":"photo", "action": "photo-of-day"}\', "sitegroupalbum_admin_submain", "", 1, 0, 5)');

      $select = new Zend_Db_Select($db);
			$select
							->from('engine4_core_pages')
							->where('name = ?', 'sitegroup_album_home')
							->limit(1);
			$info = $select->query()->fetch();
			if (empty($info)) {
				$db->insert('engine4_core_pages', array(
      'name' => 'sitegroup_album_home',
      'displayname' => 'Group Albums Home',
      'title' => 'Group Albums Home',
      'description' => 'This is group album home group.',
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
					'name' => 'sitegroupalbum.photo-of-the-day',
					'parent_content_id' => $left_id,
					'order' => 8,
					'params' => '{"title":"Photo of the Day"}',
			));

			$db->insert('engine4_core_content', array(
					'page_id' => $group_id,
					'type' => 'widget',
					'name' => 'sitegroupalbum.featured-photos',
					'parent_content_id' => $left_id,
					'order' => 9,
					'params' => '{"title":"Featured Photos","titleCount":"true"}',
			));

			$db->insert('engine4_core_content', array(
					'page_id' => $group_id,
					'type' => 'widget',
					'name' => 'sitegroup.popularphotos-sitegroup',
					'parent_content_id' => $left_id,
					'order' => 10,
					'params' => '{"title":"Most Popular Photos","titleCount":"true"}',
			));

			$db->insert('engine4_core_content', array(
					'page_id' => $group_id,
					'type' => 'widget',
					'name' => 'sitegroupalbum.homephotolike-sitegroup',
					'parent_content_id' => $left_id,
					'order' => 11,
					'params' => '{"title":"Most Liked Photos","titleCount":"true"}',
			));

			$db->insert('engine4_core_content', array(
					'page_id' => $group_id,
					'type' => 'widget',
					'name' => 'sitegroupalbum.homephotocomment-sitegroup',
					'parent_content_id' => $left_id,
					'order' => 12,
					'params' => '{"title":"Most Commented Photos","titleCount":"true"}',
			));

			// Middle
			$db->insert('engine4_core_content', array(
					'page_id' => $group_id,
					'type' => 'widget',
					'name' => 'sitegroupalbum.featured-albums-slideshow',
					'parent_content_id' => $middle_id,
					'order' => 13,
					'params' => '{"title":"Featured Albums","titleCount":"true"}',
			));

		// Middele
			$db->insert('engine4_core_content', array(
					'page_id' => $group_id,
					'type' => 'widget',
					'name' => 'sitegroupalbum.featured-photos-carousel',
					'parent_content_id' => $middle_id,
					'order' => 14,
					'params' => '{"title":"Featured Photos","vertical":"0", "noOfRow":"2","inOneRow":"3","interval":"250","name":"sitegroupalbum.featured-photos-carousel"}',
			));

			$db->insert('engine4_core_content', array(
					'page_id' => $group_id,
					'type' => 'widget',
					'name' => 'sitegroupalbum.list-photos-tabs-view',
					'parent_content_id' => $middle_id,
					'order' => 15,
					'params' => '{"title":"Photos","margin_photo":"12"}',
			));

			$db->insert('engine4_core_content', array(
					'page_id' => $group_id,
					'type' => 'widget',
					'name' => 'sitegroupalbum.list-albums-tabs-view',
					'parent_content_id' => $middle_id,
					'order' => 16,
					'params' => '{"title":"Albums","margin_photo":"12"}',
			));
			// Right Side
			$db->insert('engine4_core_content', array(
					'page_id' => $group_id,
					'type' => 'widget',
					'name' => 'sitegroupalbum.sitegroupalbumlist-link',
					'parent_content_id' => $right_id,
					'order' => 18,
					'params' => '',
			));

			// Right Side
			$db->insert('engine4_core_content', array(
					'page_id' => $group_id,
					'type' => 'widget',
					'name' => 'sitegroupalbum.search-sitegroupalbum',
					'parent_content_id' => $right_id,
					'order' => 17,
					'params' => '',
			));

			$db->insert('engine4_core_content', array(
					'page_id' => $group_id,
					'type' => 'widget',
					'name' => 'sitegroupalbum.album-of-the-day',
					'parent_content_id' => $right_id,
					'order' => 19,
					'params' => '{"title":"Album of the Day"}',
			));

			$db->insert('engine4_core_content', array(
					'page_id' => $group_id,
					'type' => 'widget',
					'name' => 'sitegroupalbum.featured-albums',
					'parent_content_id' => $right_id,
					'order' => 20,
					'params' => '{"title":"Featured Albums","itemCountPerGroup":4}',
			));

			$db->insert('engine4_core_content', array(
					'page_id' => $group_id,
					'type' => 'widget',
					'name' => 'sitegroupalbum.list-popular-albums',
					'parent_content_id' => $right_id,
					'order' => 21,
					'params' => '{"title":"Most Liked Albums","itemCountPerGroup":"4","popularType":"like","name":"sitegroupalbum.list-popular-albums"}',
			));
			$db->insert('engine4_core_content', array(
					'page_id' => $group_id,
					'type' => 'widget',
					'name' => 'sitegroupalbum.list-popular-albums',
					'parent_content_id' => $right_id,
					'order' => 22,
					'params' => '{"title":"Popular Albums","itemCountPerGroup":"4","popularType":"view","name":"sitegroupalbum.list-popular-albums"}',
			));
      }
      $db->update('engine4_core_pages', array('displayname' => 'Browse Group Albums'), array('displayname = ?' => 'Group Albums')); 
    }
    
    //    //START CODE FOR LIGHTBOX
//    //HERE WE CHECKING THAT SITEGROUPALBUM ENTRY EXIST IN THE CORE MODULE TABLE OR NOT
//    $select = new Zend_Db_Select($db);
//    $select
//            ->from('engine4_core_modules', array('version'))
//            ->where("name =?", "sitegroupalbum");
//    $sitegroupalbumVersion = $select->query()->fetchAll();
//
//    //IF NOT EXIST THEN WE INSERTING THE LIGHTBOX SHOULD BE DISPLAY OR NOT
//    if (empty($sitegroupalbumVersion)) {
//      $value = '';
//      $select = new Zend_Db_Select($db);
//      $value = $select
//              ->from('engine4_core_settings', array('value'))
//              ->where("name =?", "socialengineaddon.display.lightbox")
//              ->query()
//              ->fetchColumn();
//
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
//                ->where("value =?", "activity");
//        $name = $select->query()->fetchColumn();
//        if (empty($name)) {
//          $name = 'socialengineaddon.lightbox.option.display.' . ++$count;
//          $db->insert('engine4_core_settings', array(
//              'name' => $name,
//              'value' => 'activity'
//          ));
//        }
//
//        $select = new Zend_Db_Select($db);
//        $select
//                ->from('engine4_core_settings', array('name'))
//                ->where("value =?", "sitegroupalbum");
//        $name = $select->query()->fetchColumn();
//        if (empty($name)) {
//          $name = 'socialengineaddon.lightbox.option.display.' . ++$count;
//          $db->insert('engine4_core_settings', array(
//              'name' => $name,
//              'value' => 'sitegroupalbum'
//          ));
//        }       
//      }          
//    }
//    //END CODE FOR LIGHTBOX

    //START THE WORK FOR MAKE WIDGETIZE GROUP OF ALBUMS LISTING AND ALBUM VIEW GROUP
    $select = new Zend_Db_Select($db);
		$select
						->from('engine4_core_modules')
						->where('name = ?', 'sitegroupalbum')
						->where('version < ?', '4.2.0');
		$is_enabled = $select->query()->fetchObject();
    if(!empty($is_enabled)) {
			$select = new Zend_Db_Select($db);
			$select
							->from('engine4_core_modules')
							->where('name = ?', 'communityad')
							->where('enabled 	 = ?', 1)
							->limit(1);
				;
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
							->where('name = ?', 'sitegroup_album_browse')
							->limit(1);
			;
			$info = $select->query()->fetch();

			if ( empty($info) ) {
				$db->insert('engine4_core_pages', array(
						'name' => 'sitegroup_album_browse',
						'displayname' => 'Browse Group Albums',
						'title' => 'Group Albums',
						'description' => 'This is the group albums.',
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
						'name' => 'sitegroupalbum.search-sitegroupalbum',
						'parent_content_id' => $right_id,
						'order' => 3,
						'params' => '{"title":"","titleCount":"true"}',
				));

				$db->insert('engine4_core_content', array(
						'page_id' => $group_id,
						'type' => 'widget',
						'name' => 'sitegroupalbum.sitegroup-album',
						'parent_content_id' => $middle_id,
						'order' => 2,
						'params' => '{"title":"","titleCount":""}',
				));

				$db->insert('engine4_core_content', array(
						'page_id' => $group_id,
						'type' => 'widget',
						'name' => 'sitegroup.mostrecentphotos-sitegroup',
						'parent_content_id' => $right_id,
						'order' => 4,
						'params' => '{"title":"Recent Photos","titleCount":"true"}',
				));

			$db->insert('engine4_core_content', array(
						'page_id' => $group_id,
						'type' => 'widget',
						'name' => 'sitegroup.popularphotos-sitegroup',
						'parent_content_id' => $right_id,
						'order' => 5,
						'params' => '{"title":"Most Popular Photos","titleCount":"true"}',
				));

				$db->insert('engine4_core_content', array(
						'page_id' => $group_id,
						'type' => 'widget',
						'name' => 'sitegroupalbum.sitegroup-sponsoredalbum',
						'parent_content_id' => $right_id,
						'order' => 6,
						'params' => '{"title":"Sponsored Albums","titleCount":"true"}',
				));

				if ( $infomation && $rowinfo ) {
					$db->insert('engine4_core_content', array(
							'page_id' => $group_id,
							'type' => 'widget',
							'name' => 'sitegroup.group-ads',
							'parent_content_id' => $right_id,
							'order' => 7,
							'params' => '{"title":"","titleCount":""}',
					));
				}
			}

			$db = $this->getDb();
			$select = new Zend_Db_Select($db);

			// Check if it's already been placed
			$select = new Zend_Db_Select($db);
			$select
							->from('engine4_core_pages')
							->where('name = ?', 'sitegroup_album_view')
							->limit(1);
			;
			$info = $select->query()->fetch();

			if ( empty($info) ) {
				$db->insert('engine4_core_pages', array(
						'name' => 'sitegroup_album_view',
						'displayname' => 'Group Album View Page',
						'title' => 'View Group Album',
						'description' => 'This is the view group for a group album.',
						'custom' => 1,
						'provides' => 'subject=sitegroupalbum',
				));
				$group_id = $db->lastInsertId('engine4_core_pages');

				// containers
				$db->insert('engine4_core_content', array(
						'page_id' => $group_id,
						'type' => 'container',
						'name' => 'main',
						'parent_content_id' => null,
						'order' => 1,
						'params' => '',
				));
				$container_id = $db->lastInsertId('engine4_core_content');

				$db->insert('engine4_core_content', array(
						'page_id' => $group_id,
						'type' => 'container',
						'name' => 'right',
						'parent_content_id' => $container_id,
						'order' => 1,
						'params' => '',
				));
				$right_id = $db->lastInsertId('engine4_core_content');

				$db->insert('engine4_core_content', array(
						'page_id' => $group_id,
						'type' => 'container',
						'name' => 'middle',
						'parent_content_id' => $container_id,
						'order' => 3,
						'params' => '',
				));
				$middle_id = $db->lastInsertId('engine4_core_content');

				// middle column content
				$db->insert('engine4_core_content', array(
						'page_id' => $group_id,
						'type' => 'widget',
						'name' => 'sitegroupalbum.album-content',
						'parent_content_id' => $middle_id,
						'order' => 1,
						'params' => '',
				));

				if ( $infomation && $rowinfo ) {
					$db->insert('engine4_core_content', array(
							'page_id' => $group_id,
							'type' => 'widget',
							'name' => 'sitegroup.group-ads',
							'parent_content_id' => $right_id,
							'order' => 1,
							'params' => '{"title":"","titleCount":""}',
					));
				}

			}

			$select = new Zend_Db_Select($db);
			$select
			->from('engine4_core_modules')
			->where('name = ?', 'mobi')
			->where('enabled 	 = ?', 1)
			->limit(1);
			;  

			$infomation = $select->query()->fetch();
			if(!empty($infomation)) {
				$select = new Zend_Db_Select($db);
				$select
				->from('engine4_core_pages')
				->where('name = ?', 'sitegroupalbum_mobi_view')
				->limit(1);
				;
				$info = $select->query()->fetch();
				if (empty($info)) {
					$db->insert('engine4_core_pages', array(
								'name' => 'sitegroupalbum_mobi_view',
								'displayname' => 'Mobile Group Album Profile',
								'title' => 'Mobile Group Album Profile',
								'description' => 'This is the mobile verison of a Group album profile group.',
								'custom' => 0,
					));
					$group_id = $db->lastInsertId('engine4_core_pages');

					// containers
					$db->insert('engine4_core_content', array(
								'page_id' => $group_id,
								'type' => 'container',
								'name' => 'main',
								'parent_content_id' => null,
								'order' => 1,
								'params' => '',
					));
					$container_id = $db->lastInsertId('engine4_core_content');

					$db->insert('engine4_core_content', array(
								'page_id' => $group_id,
								'type' => 'container',
								'name' => 'right',
								'parent_content_id' => $container_id,
								'order' => 1,
								'params' => '',
					));
					$right_id = $db->lastInsertId('engine4_core_content');

					$db->insert('engine4_core_content', array(
								'page_id' => $group_id,
								'type' => 'container',
								'name' => 'middle',
								'parent_content_id' => $container_id,
								'order' => 3,
								'params' => '',
					));
					$middle_id = $db->lastInsertId('engine4_core_content');

					// middle column content
					$db->insert('engine4_core_content', array(
							'page_id' => $group_id,
							'type' => 'widget',
							'name' => 'sitegroupalbum.album-content',
							'parent_content_id' => $middle_id,
							'order' => 1,
							'params' => '',
					));
				}
			}
    }
    //END THE WORK FOR MAKE WIDGETIZE GROUP OF ALBUMS LISTING AND ALBUM VIEW GROUP
    $select = new Zend_Db_Select($db);
    $select
          ->from('engine4_core_settings')
          ->where('name = ?', 'sitegroup.feed.type');
  $info = $select->query()->fetch();
    $enable = 1;
    if (!empty($info))
      $enable = $info['value'];
    $db->query('INSERT IGNORE INTO  `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_object_thumb`) VALUES("sitegroupalbum_admin_photo_new", "sitegroupalbum", "{item:$object} added {var:$count} photo(s) to the album {var:$linked_album_title}:", '.$enable.', 6, 2, 1, 1, 1, 1)');

// ********* End OnInstall() Code *********************

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enabled`) VALUES
("sitegroupalbum_admin_widget_settings", "sitegroupalbum", "Widget Settings", "", \'{"route":"admin_default","module":"sitegroupalbum","controller":"widgets"}\', "sitegroupalbum_admin_main", "", 0, 3, 1)');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitegroupalbum_admin_main_photo_featured", "sitegroupalbum", "Featured Photos", "", \'{"route":"admin_default","module":"sitegroupalbum","controller":"settings", "action": "featured"}\', "sitegroupalbum_admin_main", "", 1, 0, 4)');


$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitegroupalbum_admin_submain_album_tab", "sitegroupalbum", "Tabbed Albums Widget", "", \'{"route":"admin_default","module":"sitegroupalbum","controller":"album", "action": "index"}\', "sitegroupalbum_admin_submain", "", 1, 0, 2)');


$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitegroupalbum_admin_submain_photo_tab", "sitegroupalbum", "Tabbed Photos Widget", "", \'{"route":"admin_default","module":"sitegroupalbum","controller":"photo", "action": "index"}\', "sitegroupalbum_admin_submain", "", 1, 0, 3)');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitegroupalbum_admin_submain_dayitems", "sitegroupalbum", "Album of the Day", "", \'{"route":"admin_default","module":"sitegroupalbum","controller":"album", "action": "manage-day-items"}\', "sitegroupalbum_admin_submain", "", 1, 0, 4)');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitegroupalbum_admin_submain_photo_items", "sitegroupalbum", "Photo of the Day", "", \'{"route":"admin_default","module":"sitegroupalbum","controller":"photo", "action": "photo-of-day"}\', "sitegroupalbum_admin_submain", "", 1, 0, 5)');

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
  $select_group = $select
          ->from('engine4_core_pages', 'page_id')
          ->where('name = ?', 'sitegroup_index_view')
          ->limit(1);
  $group = $select_group->query()->fetchAll();
  if (!empty($group)) {
    $group_id = $group[0]['page_id'];

    //INSERTING THE PHOTO WIDGET IN SITEGROUP_ADMIN_CONTENT TABLE ALSO.
    Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->setAdminDefaultInfo('sitegroup.photos-sitegroup', $group_id, 'Photos', 'true', '110');

    //INSERTING THE PHOTO WIDGET IN CORE_CONTENT TABLE ALSO.
    Engine_Api::_()->getApi('layoutcore', 'sitegroup')->setContentDefaultInfo('sitegroup.photos-sitegroup', $group_id, 'Photos', 'true', '110');

    //INSERTING THE PHOTO WIDGET IN SITEGROUP_CONTENT TABLE ALSO.
    $select = new Zend_Db_Select($db);
    $select = $select
            ->from('engine4_sitegroup_contentgroups', 'contentgroup_id');
    $contentgroup_ids = $select->query()->fetchAll();
    foreach ($contentgroup_ids as $contentgroup_id) {
      if (!empty($contentgroup_id)) {
        $contentgroup_id = $contentgroup_id['contentgroup_id'];
        Engine_Api::_()->getDbtable('content', 'sitegroup')->setDefaultInfo('sitegroup.photos-sitegroup', $contentgroup_id, 'Photos', 'true', '110');

        //INSERT THE RANDOM ALBUM WIDGET
        $select = new Zend_Db_Select($db);
        $select_content = $select
                ->from('engine4_sitegroup_content')
                ->where('contentgroup_id = ?', $contentgroup_id)
                ->where('type = ?', 'widget')
                ->where('name = ?', 'sitegroup.albums-sitegroup')
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
                    ->where('type = ?', 'container')
										->where('contentgroup_id = ?', $contentgroup_id)
										->where('name in (?)', array('left', 'right'))
                    ->limit(1);
            $left = $select_left->query()->fetchAll();
            if (!empty($left)) {
              $left_id = $left[0]['content_id'];
              $db->insert('engine4_sitegroup_content', array(
                  'contentgroup_id' => $contentgroup_id,
                  'type' => 'widget',
                  'name' => 'sitegroup.albums-sitegroup',
                  'parent_content_id' => $left_id,
                  'order' => 25,
                  'params' => '{"title":"Albums","titleCount":""}',
              ));
            }
          }
        }

        //INSERT THE PHOTO STRIP WIDGET IN SITEGROUP CONTENT TABLE FOR USER
//         $select = new Zend_Db_Select($db);
//         $select_content = $select
//                 ->from('engine4_sitegroup_content')
//                 ->where('contentgroup_id = ?', $contentgroup_id)
//                 ->where('type = ?', 'widget')
//                 ->where('name = ?', 'sitegroup.photorecent-sitegroup')
//                 ->limit(1);
//         $content = $select_content->query()->fetchAll();
//         if (empty($content)) {
//           $select = new Zend_Db_Select($db);
//           $select_container = $select
//                   ->from('engine4_sitegroup_content', 'content_id')
//                   ->where('contentgroup_id = ?', $contentgroup_id)
//                   ->where('type = ?', 'container')
//                   ->limit(1);
//           $container = $select_container->query()->fetchAll();
//           if (!empty($container)) {
//             $container_id = $container[0]['content_id'];
//             $select = new Zend_Db_Select($db);
//             $select_middle = $select
//                     ->from('engine4_sitegroup_content')
//                     ->where('parent_content_id = ?', $container_id)
//                     ->where('type = ?', 'container')
//                     ->where('name = ?', 'middle')
//                     ->limit(1);
//             $middle = $select_middle->query()->fetchAll();
//             if (!empty($middle)) {
//               $middle_id = $middle[0]['content_id'];
//               $db->insert('engine4_sitegroup_content', array(
//                   'contentgroup_id' => $contentgroup_id,
//                   'type' => 'widget',
//                   'name' => 'sitegroup.photorecent-sitegroup',
//                   'parent_content_id' => $middle_id,
//                   'order' => 5,
//                   'params' => '{"title":"","titleCount":""}',
//               ));
//             }
//           }
//         }
      }
    }

    //INSERT THE RANDOM ALBUM WIDGET IN ADMIN CONTENT TABLE
    $select = new Zend_Db_Select($db);
    $select_content = $select
            ->from('engine4_sitegroup_admincontent')
            ->where('group_id = ?', $group_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitegroup.albums-sitegroup')
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
								->where('group_id = ?', $group_id)
								->where('name in (?)', array('left', 'right'))
                ->limit(1);
        $left = $select_left->query()->fetchAll();
        if (!empty($left)) {
          $left_id = $left[0]['admincontent_id'];
          $db->insert('engine4_sitegroup_admincontent', array(
              'group_id' => $group_id,
              'type' => 'widget',
              'name' => 'sitegroup.albums-sitegroup',
              'parent_content_id' => $left_id,
              'order' => 25,
              'params' => '{"title":"Albums","titleCount":""}',
          ));
        }
      }
    }

//     //INSERT THE PHOTO STRIP WIDGET IN ADMIN CONTENT TABLE
//     $select = new Zend_Db_Select($db);
//     $select_content = $select
//             ->from('engine4_sitegroup_admincontent')
//             ->where('group_id = ?', $group_id)
//             ->where('type = ?', 'widget')
//             ->where('name = ?', 'sitegroup.photorecent-sitegroup')
//             ->limit(1);
//     $content = $select_content->query()->fetchAll();
//     if (empty($content)) {
//       $select = new Zend_Db_Select($db);
//       $select_container = $select
//               ->from('engine4_sitegroup_admincontent', 'admincontent_id')
//               ->where('group_id = ?', $group_id)
//               ->where('type = ?', 'container')
//               ->limit(1);
//       $container = $select_container->query()->fetchAll();
//       if (!empty($container)) {
//         $container_id = $container[0]['admincontent_id'];
//         $select = new Zend_Db_Select($db);
//         $select_middle = $select
//                 ->from('engine4_sitegroup_admincontent')
//                 ->where('parent_content_id = ?', $container_id)
//                 ->where('type = ?', 'container')
//                 ->where('name = ?', 'middle')
//                 ->limit(1);
//         $middle = $select_middle->query()->fetchAll();
//         if (!empty($middle)) {
//           $middle_id = $middle[0]['admincontent_id'];
//           $db->insert('engine4_sitegroup_admincontent', array(
//               'group_id' => $group_id,
//               'type' => 'widget',
//               'name' => 'sitegroup.photorecent-sitegroup',
//               'parent_content_id' => $middle_id,
//               'order' => 5,
//               'params' => '{"title":"","titleCount":""}',
//           ));
//         }
//       }
//     }

    //INSERT THE RANDOM ALBUM WIDGET IN CORE CONTENT TABLE
    $select = new Zend_Db_Select($db);
    $select_content = $select
            ->from('engine4_core_content')
            ->where('page_id = ?', $group_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitegroup.albums-sitegroup')
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
								->where('page_id = ?', $group_id)
								->where('name in (?)', array('left', 'right'))
                ->limit(1);
        $left = $select_left->query()->fetchAll();
        if (!empty($left)) {
          $left_id = $left[0]['content_id'];
          $db->insert('engine4_core_content', array(
              'page_id' => $group_id,
              'type' => 'widget',
              'name' => 'sitegroup.albums-sitegroup',
              'parent_content_id' => $left_id,
              'order' => 25,
              'params' => '{"title":"Albums","titleCount":""}',
          ));
        }
      }
    }

//     //INSERT THE PHOTO STRIP WIDGET IN CORE CONTENT TABLE
//     $select = new Zend_Db_Select($db);
//     $select_content = $select
//             ->from('engine4_core_content')
//             ->where('page_id = ?', $group_id)
//             ->where('type = ?', 'widget')
//             ->where('name = ?', 'sitegroup.photorecent-sitegroup')
//             ->limit(1);
//     $content = $select_content->query()->fetchAll();
//     if (empty($content)) {
//       $select = new Zend_Db_Select($db);
//       $select_container = $select
//               ->from('engine4_core_content', 'content_id')
//               ->where('page_id = ?', $group_id)
//               ->where('type = ?', 'container')
//               ->limit(1);
//       $container = $select_container->query()->fetchAll();
//       if (!empty($container)) {
//         $container_id = $container[0]['content_id'];
//         $select = new Zend_Db_Select($db);
//         $select_middle = $select
//                 ->from('engine4_core_content')
//                 ->where('parent_content_id = ?', $container_id)
//                 ->where('type = ?', 'container')
//                 ->where('name = ?', 'middle')
//                 ->limit(1);
//         $middle = $select_middle->query()->fetchAll();
//         if (!empty($middle)) {
//           $middle_id = $middle[0]['content_id'];
//           $db->insert('engine4_core_content', array(
//               'page_id' => $group_id,
//               'type' => 'widget',
//               'name' => 'sitegroup.photorecent-sitegroup',
//               'parent_content_id' => $middle_id,
//               'order' => 5,
//               'params' => '{"title":"","titleCount":""}',
//           ));
//         }
//       }
//     }
  }
  
  
  
    
  $contentTable = Engine_Api::_()->getDbtable('content', 'core');
  $contentTableName = $contentTable->info('name');
 
  $select = new Zend_Db_Select($db);
	$select
					->from('engine4_core_modules')
					->where('name = ?', 'communityad')
					->where('enabled 	 = ?', 1)
					->limit(1);
	;
	$infomation = $select->query()->fetch();
	$select = new Zend_Db_Select($db);
	$select
					->from('engine4_core_settings')
					->where('name = ?', 'sitegroup.communityads')
					->where('value 	 = ?', 1)
					->limit(1);
    ;
    $rowinfo = $select->query()->fetch();

  $select = new Zend_Db_Select($db);
  $select
          ->from('engine4_core_pages')
          ->where('name = ?', 'sitegroup_album_browse')
          ->limit(1);
  ;
  $info = $select->query()->fetch();
  if ( empty($info) ) {
    $db->insert('engine4_core_pages', array(
        'name' => 'sitegroup_album_browse',
        'displayname' => 'Browse Group albums',
        'title' => 'Group Albums List',
        'description' => 'This is the group albums.',
        'custom' => 1,
    ));
    $group_id = $db->lastInsertId('engine4_core_pages');
//INSERT MAIN CONTAINER
    $mainContainer = $contentTable->createRow();
    $mainContainer->page_id = $group_id;
    $mainContainer->type = 'container';
    $mainContainer->name = 'main';
    $mainContainer->order = 2;
    $mainContainer->save();
    $container_id = $mainContainer->content_id;

//INSERT MAIN - MIDDLE CONTAINER
    $mainMiddleContainer = $contentTable->createRow();
    $mainMiddleContainer->page_id = $group_id;
    $mainMiddleContainer->type = 'container';
    $mainMiddleContainer->name = 'middle';
    $mainMiddleContainer->parent_content_id = $container_id;
    $mainMiddleContainer->order = 6;
    $mainMiddleContainer->save();
    $middle_id = $mainMiddleContainer->content_id;

//INSERT MAIN - RIGHT CONTAINER
    $mainRightContainer = $contentTable->createRow();
    $mainRightContainer->page_id = $group_id;
    $mainRightContainer->type = 'container';
    $mainRightContainer->name = 'right';
    $mainRightContainer->parent_content_id = $container_id;
    $mainRightContainer->order = 5;
    $mainRightContainer->save();
    $right_id = $mainRightContainer->content_id;

//INSERT TOP CONTAINER
    $topContainer = $contentTable->createRow();
    $topContainer->page_id = $group_id;
    $topContainer->type = 'container';
    $topContainer->name = 'top';
    $topContainer->order = 1;
    $topContainer->save();
    $top_id = $topContainer->content_id;

//INSERT TOP- MIDDLE CONTAINER
    $topMiddleContainer = $contentTable->createRow();
    $topMiddleContainer->page_id = $group_id;
    $topMiddleContainer->type = 'container';
    $topMiddleContainer->name = 'middle';
    $topMiddleContainer->parent_content_id = $top_id;
    $topMiddleContainer->order = 6;
    $topMiddleContainer->save();
    $top_middle_id = $topMiddleContainer->content_id;

    //INSERT NAVIGATION WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.browsenevigation-sitegroup', $top_middle_id, 1);

//INSERT ALBUM WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroupalbum.sitegroup-album', $middle_id, 2);

    //INSERT SEARCH GROUP ALBUM WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroupalbum.search-sitegroupalbum', $right_id, 3, "", "true");

    //INSERT RECENT GROUP ALBUM WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.mostrecentphotos-sitegroup', $right_id, 4, "Recent Photos", "true");

    //INSERT SPONSORED GROUP ALBUM WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroupalbum.sitegroup-sponsoredalbum', $right_id, 5, "Sponsored Albums", "true");

    //INSERT MOST POUPLAR GROUP ALBUM WIDGET
    Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.popularphotos-sitegroup', $right_id, 6, "Most Popular Photos", "true");

    if ( $infomation && $rowinfo ) {
      Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.group-ads', $right_id, 7, "", "true");
    }
  }

  $select = new Zend_Db_Select($db);

  // Check if it's already been placed
  $select = new Zend_Db_Select($db);
  $select
          ->from('engine4_core_pages')
          ->where('name = ?', 'sitegroup_album_view')
          ->limit(1);
  ;
  $info = $select->query()->fetch();

  if ( empty($info) ) {
    $db->insert('engine4_core_pages', array(
        'name' => 'sitegroup_album_view',
        'displayname' => 'Group Album View Page',
        'title' => 'View Group Album',
        'description' => 'This is the view group for a group album.',
        'custom' => 1,
        'provides' => 'subject=sitegroupalbum',
    ));
    $group_id = $db->lastInsertId('engine4_core_pages');

    // containers
    $db->insert('engine4_core_content', array(
        'page_id' => $group_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => 1,
        'params' => '',
    ));
    $container_id = $db->lastInsertId('engine4_core_content');

    $db->insert('engine4_core_content', array(
        'page_id' => $group_id,
        'type' => 'container',
        'name' => 'right',
        'parent_content_id' => $container_id,
        'order' => 1,
        'params' => '',
    ));
    $right_id = $db->lastInsertId('engine4_core_content');

    $db->insert('engine4_core_content', array(
        'page_id' => $group_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $container_id,
        'order' => 3,
        'params' => '',
    ));
    $middle_id = $db->lastInsertId('engine4_core_content');

    // middle column content
    $db->insert('engine4_core_content', array(
        'page_id' => $group_id,
        'type' => 'widget',
        'name' => 'sitegroupalbum.album-content',
        'parent_content_id' => $middle_id,
        'order' => 1,
        'params' => '',
    ));

    if ( $infomation && $rowinfo ) {
      Engine_Api::_()->sitegroup()->setDefaultDataContentWidget($contentTable, $contentTableName, $group_id, 'widget', 'sitegroup.group-ads', $right_id, 1, "", "true");
    }
  }  

  $select = new Zend_Db_Select($db);
	$select
	->from('engine4_core_modules')
	->where('name = ?', 'mobi')
	->where('enabled 	 = ?', 1)
	->limit(1);
	;  

	$infomation = $select->query()->fetch();
	if(!empty($infomation)) {
		$select = new Zend_Db_Select($db);
		$select
		->from('engine4_core_pages')
		->where('name = ?', 'sitegroupalbum_mobi_view')
		->limit(1);
		;
		$info = $select->query()->fetch();
		if (empty($info)) {
			$db->insert('engine4_core_pages', array(
						'name' => 'sitegroupalbum_mobi_view',
						'displayname' => 'Mobile Group Album Profile',
						'title' => 'Mobile Group Album Profile',
						'description' => 'This is the mobile verison of a Group album profile group.',
						'custom' => 0,
			));
			$group_id = $db->lastInsertId('engine4_core_pages');

			// containers
			$db->insert('engine4_core_content', array(
						'page_id' => $group_id,
						'type' => 'container',
						'name' => 'main',
						'parent_content_id' => null,
						'order' => 1,
						'params' => '',
			));
			$container_id = $db->lastInsertId('engine4_core_content');

			$db->insert('engine4_core_content', array(
						'page_id' => $group_id,
						'type' => 'container',
						'name' => 'right',
						'parent_content_id' => $container_id,
						'order' => 1,
						'params' => '',
			));
			$right_id = $db->lastInsertId('engine4_core_content');

			$db->insert('engine4_core_content', array(
						'page_id' => $group_id,
						'type' => 'container',
						'name' => 'middle',
						'parent_content_id' => $container_id,
						'order' => 3,
						'params' => '',
			));
			$middle_id = $db->lastInsertId('engine4_core_content');

			// middle column content
			$db->insert('engine4_core_content', array(
					'page_id' => $group_id,
					'type' => 'widget',
					'name' => 'sitegroupalbum.album-content',
					'parent_content_id' => $middle_id,
					'order' => 1,
					'params' => '',
			));
		}
	}

  $select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_pages')
        ->where('name = ?', 'sitegroup_album_home')
        ->limit(1);
$info = $select->query()->fetch();
if (empty($info)) {
  $db->insert('engine4_core_pages', array(
      'name' => 'sitegroup_album_home',
      'displayname' => 'Group Albums Home',
      'title' => 'Group Albums Home',
      'description' => 'This is group album home group.',
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
      'name' => 'sitegroupalbum.photo-of-the-day',
      'parent_content_id' => $left_id,
      'order' => 8,
      'params' => '{"title":"Photo of the Day"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'widget',
      'name' => 'sitegroupalbum.featured-photos',
      'parent_content_id' => $left_id,
      'order' => 9,
      'params' => '{"title":"Featured Photos","titleCount":"true"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'widget',
      'name' => 'sitegroup.popularphotos-sitegroup',
      'parent_content_id' => $left_id,
      'order' => 10,
      'params' => '{"title":"Most Popular Photos","titleCount":"true"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'widget',
      'name' => 'sitegroupalbum.homephotolike-sitegroup',
      'parent_content_id' => $left_id,
      'order' => 11,
      'params' => '{"title":"Most Liked Photos","titleCount":"true"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'widget',
      'name' => 'sitegroupalbum.homephotocomment-sitegroup',
      'parent_content_id' => $left_id,
      'order' => 12,
      'params' => '{"title":"Most Commented Photos","titleCount":"true"}',
  ));

  // Middle
  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'widget',
      'name' => 'sitegroupalbum.featured-albums-slideshow',
      'parent_content_id' => $middle_id,
      'order' => 13,
      'params' => '{"title":"Featured Albums","titleCount":"true"}',
  ));

// Middele
  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'widget',
      'name' => 'sitegroupalbum.featured-photos-carousel',
      'parent_content_id' => $middle_id,
      'order' => 14,
      'params' => '{"title":"Featured Photos","vertical":"0", "noOfRow":"2","inOneRow":"3","interval":"250","name":"sitegroupalbum.featured-photos-carousel"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'widget',
      'name' => 'sitegroupalbum.list-photos-tabs-view',
      'parent_content_id' => $middle_id,
      'order' => 15,
      'params' => '{"title":"Photos","margin_photo":"12"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'widget',
      'name' => 'sitegroupalbum.list-albums-tabs-view',
      'parent_content_id' => $middle_id,
      'order' => 16,
      'params' => '{"title":"Albums","margin_photo":"12"}',
  ));
  // Right Side
  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'widget',
      'name' => 'sitegroupalbum.sitegroupalbumlist-link',
      'parent_content_id' => $right_id,
      'order' => 18,
      'params' => '',
  ));

   // Right Side
  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'widget',
      'name' => 'sitegroupalbum.search-sitegroupalbum',
      'parent_content_id' => $right_id,
      'order' => 17,
      'params' => '',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'widget',
      'name' => 'sitegroupalbum.album-of-the-day',
      'parent_content_id' => $right_id,
      'order' => 19,
      'params' => '{"title":"Album of the Day"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'widget',
      'name' => 'sitegroupalbum.featured-albums',
      'parent_content_id' => $right_id,
      'order' => 20,
      'params' => '{"title":"Featured Albums","itemCountPerGroup":4}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'widget',
      'name' => 'sitegroupalbum.list-popular-albums',
      'parent_content_id' => $right_id,
      'order' => 21,
      'params' => '{"title":"Most Liked Albums","itemCountPerGroup":"4","popularType":"like","name":"sitegroupalbum.list-popular-albums"}',
  ));
  $db->insert('engine4_core_content', array(
      'page_id' => $group_id,
      'type' => 'widget',
      'name' => 'sitegroupalbum.list-popular-albums',
      'parent_content_id' => $right_id,
      'order' => 22,
      'params' => '{"title":"Popular Albums","itemCountPerGroup":"4","popularType":"view","name":"sitegroupalbum.list-popular-albums"}',
  ));
}
}

$select = new Zend_Db_Select($db);
$select
				->from('engine4_core_modules')
				->where('name = ?', 'sitemobile')
				->where('enabled = ?', 1);
$is_sitemobile_object = $select->query()->fetchObject();
if(!empty($is_sitemobile_object)) {
  Engine_Api::_()->getApi('modules', 'sitemobile')->addModuleStart('sitegroupalbum');
}



		$select = new Zend_Db_Select($db);
		$select
					->from('engine4_core_modules')
					->where('name = ?', 'sitemobile')
					->where('enabled = ?', 1);
		$is_sitemobile_object = $select->query()->fetchObject();
		if($is_sitemobile_object)  {
				include APPLICATION_PATH . "/application/modules/Sitegroupalbum/controllers/mobileLayoutCreation.php";
		}




?>