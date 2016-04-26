<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitetagcheckin_AdminSettingsController extends Core_Controller_Action_Admin {

  //ACTION FOR SAVE THE GOLBAL SETTINGS
  public function indexAction() {
    
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    if ($this->getRequest()->isPost()) {
     
			if (isset($_POST['sitetagcheckin_groupsettings']) && $_POST['sitetagcheckin_groupsettings'] == '1') {
				$this->grouplocations();
				$this->groupProfilePage();
			}
			
			if (isset($_POST['sitetagcheckin_groupsettings']) && $_POST['sitetagcheckin_groupsettings'] == '0') {
				$db->query("UPDATE `engine4_core_menuitems` SET `enabled` = '0' WHERE `engine4_core_menuitems`.`name` = 'sitetagcheckin_main_grouplocation' LIMIT 1 ;");
				$db->query("UPDATE `engine4_core_menuitems` SET `enabled` = '0' WHERE `engine4_core_menuitems`.`name` = 'sitetagcheckin_main_groupbylocation' LIMIT 1 ;");
			} else {
				$db->query("UPDATE `engine4_core_menuitems` SET `enabled` = '1' WHERE `engine4_core_menuitems`.`name` = 'sitetagcheckin_main_grouplocation' LIMIT 1 ;");
				$db->query("UPDATE `engine4_core_menuitems` SET `enabled` = '1' WHERE `engine4_core_menuitems`.`name` = 'sitetagcheckin_main_groupbylocation' LIMIT 1 ;");
			}
		}
    $onactive_disabled = array('sitetagcheckin_tooltip_bgcolor', 'submit', 'sitetagcheckin_proximity_search_kilometer', 'sitetagcheckin_groupsettings', 'sitetagcheckin_usersettings', 'sitetagcheckin_levelsettings', 'sitetagcheckin_networksettings', 'sitetagcheckinprofile_mapping', 'sitetagcheckin_userstatus', 'sitetagcheckin_mapshow', 'sitetagcheckin_layouts_oder', 'sitetagcheckin_default_textarea_text', 'sitetagcheckin_map_zoom', 'sitetagcheckin_map_city');
    $afteractive_disabled = array('environment_mode', 'submit_lsetting');
    $oldLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.map.city', "World");
    
    $pluginName = 'sitetagcheckin';
    if (!empty($_POST[$pluginName . '_lsettings']))
      $_POST[$pluginName . '_lsettings'] = @trim($_POST[$pluginName . '_lsettings']);
    
    include APPLICATION_PATH . '/application/modules/Sitetagcheckin/controllers/license/license1.php';
        $newLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.map.city', "World");
    $this->setDefaultMapCenterPoint($oldLocation, $newLocation);
  }

  //MAKE FAQ ACTION 
  public function faqAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitetagcheckin_admin_main', array(), 'sitetagcheckin_admin_main_faq');
  }
  
  public function readmeAction() {
	  
  }

  public function guidelinesAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitetagcheckin_admin_main', array(), 'sitetagcheckin_admin_main_settings');
  }
  
  
  //Sink the event location.
  public function usersinkLocationAction() {
  
    //PROCESS
    set_time_limit(0);
    ini_set("max_execution_time", "300");
    ini_set("memory_limit", "256M");

    $seLocationsTable = Engine_Api::_()->getDbtable('locationitems', 'seaocore');
    
		$usersTable = Engine_Api::_()->getDbtable('users', 'user');
		$usersTableName = $usersTable->info('name');

		$profilemapsTable = Engine_Api::_()->getDbtable('profilemaps', 'sitetagcheckin');
		$profilemapsTablename = $profilemapsTable->info('name');
		
		$select = $profilemapsTable->select()
		                           ->from($profilemapsTablename); 
							
		$option_id =  $profilemapsTable->fetchAll($select);
		$option_id_location = array();
		foreach($option_id as $optionId) {
			$option_id_location[] = $optionId['profile_type'];
		}
		
		$valuesTable = Engine_Api::_()->fields()->getTable('user', 'values');
		$valuesTableName = $valuesTable->info('name');

		$select = $valuesTable->select()->setIntegrityCheck(false)
							->from($valuesTableName)
							->join($usersTableName, "$valuesTableName.item_id = $usersTableName.user_id", null)
							->where($valuesTableName . '.field_id IN (?)', (array) $option_id_location)
							->where($usersTableName . '.seao_locationid = ?', 0);
							//->where($usersTableName . '.location <> ?', '');
		$this->view->row = $row = $valuesTable->fetchAll($select);
// 	  foreach($result as $results) { print_r($results);die;
// 			$results['profile_type'];
// 		}




// 		$metaTable = Engine_Api::_()->fields()->getTable('user', 'meta');
// 		$metaTableName = $metaTable->info('name');
// 		
// 		$select = $metaTable->select()
// 						->from($metaTableName, array('type'))
// 						->where($metaTableName . '.type = ?', 'location')
// 						->where($metaTableName . '.display = ?', '1')
// 						->where($metaTableName . '.search = ?', '1');
// 		$metaResults = $metaTable->fetchAll($select);
// 		if (count($metaResults) != 0) {
// 		
// 			$usersTable = Engine_Api::_()->getDbtable('users', 'user');
// 			$usersTableName = $usersTable->info('name');
// 		
// 			$searchTable = Engine_Api::_()->fields()->getTable('user', 'search');
// 			$searchTableName = $searchTable->info('name');
// 			
// 			$select = $searchTable->select()
// 							->setIntegrityCheck(false)
// 							->from($searchTableName, array('location', 'item_id'))
// 							->joinLeft($usersTableName, "$searchTableName.item_id = $usersTableName.user_id", null)
// 							->where($usersTableName . '.seao_locationid = ?', 0)
// 							->where($searchTableName . '.location <> ?', '');
// 			$this->view->row  = $row = $searchTable->fetchAll($select);
		
			$db = Zend_Db_Table_Abstract::getDefaultAdapter();
			$table_exist = $db->query('SHOW TABLES LIKE \'engine4_user_fields_search\'')->fetch();
			if (!empty($table_exist)) {
				$column_exist = $db->query('SHOW COLUMNS FROM engine4_user_fields_search LIKE \'location\'')->fetch();
			}
			
			$this->view->error = 0;

			if ($this->getRequest()->isPost()) {

				foreach ($row as $result) {

					if (!empty($result['value'])) {
					  Engine_Api::_()->getDbtable('locationitems', 'seaocore')->delete(array('resource_id =?' => $result['item_id'], 'resource_type = ?' => 'user'));
					  
						$seao_locationid = $seLocationsTable->getLocationItemId($result['value'], '', 'user', $result['item_id']);

						if (!empty($column_exist)) {
							Engine_Api::_()->fields()->getTable('user', 'search')->update(array('location' => $result['value']), array('item_id =?' => $result['item_id']));
						}

						//member table entry of location id.
						Engine_Api::_()->getDbtable('users', 'user')->update(array('seao_locationid'=>  $seao_locationid, 'location' => $result['value']), array('user_id =?' => $result['item_id']));
					}
				}
				$this->view->error = 1;
			}
		//}
  }
  
  //Sink the albums location.
  public function sinkalbumsLocationAction() {
  
    //PROCESS
    set_time_limit(0);
    ini_set("max_execution_time", "300");
    ini_set("memory_limit", "256M");
    
    $seLocationsTable = Engine_Api::_()->getDbtable('locationitems', 'seaocore');

    	$albumstable = Engine_Api::_()->getItemTable('album');
	  $albumids = $albumstable->select()
															->from($albumstable->info('name'), array('album_id'))
															->where('seao_locationid = ?', 0)
															->query()
															->fetchAll(Zend_Db::FETCH_COLUMN);
															
		$addlocationsTable = Engine_Api::_()->getItemTable('sitetagcheckin_addlocation');
		$select = $addlocationsTable->select()
															->from($addlocationsTable->info('name'), array('params', 'resource_id'))
															->where('resource_type = ?', 'album');
		$row = $addlocationsTable->fetchAll($select);
		$count = 0;
		foreach ($row as $result) {
			$postData = $result->params;
			$location = $postData['checkin']['label'];
			if($location && in_array($result->resource_id, $albumids)) 
			$count ++;
			
		}
		
		$this->view->row = $count;

    $this->view->error = 0;
    
		if ($this->getRequest()->isPost()) {
		
		  $addlocationsTable = Engine_Api::_()->getItemTable('sitetagcheckin_addlocation');
			$select = $addlocationsTable->select()
																->from($addlocationsTable->info('name'), array('params', 'resource_id'))
																->where('resource_type = ?', 'album')
																->where($addlocationsTable->info('name') . '.resource_id IN (?)', (array) $albumids)
																->limit('1500');
			$row = $addlocationsTable->fetchAll($select);
			
			foreach ($row as $result) {
				$postData = $result->params;
				$location = $postData['checkin']['label'];
				$album_id = $result->resource_id;
				if (!empty($location)) {
					$seao_locationid = $seLocationsTable->getLocationItemId($location, '', 'album', $album_id);
					Engine_Api::_()->getItemTable('album')->update(array('location' => $location, 'seao_locationid'=>  $seao_locationid), array('album_id =?' => $album_id));
				}
			}
			$this->view->error = 1;
		}
  }
  
  //Sink the event location.
  public function sinkLocationAction() {
  
    //PROCESS
    set_time_limit(0);
    ini_set("max_execution_time", "300");
    ini_set("memory_limit", "256M");
    
    $seLocationsTable = Engine_Api::_()->getDbtable('locationitems', 'seaocore');

		$eventstable = Engine_Api::_()->getItemTable('event');
		$select = $eventstable->select()->where('location <> ?', '')->where('seao_locationid = ?', 0)->limit('1500');
	  $this->view->row  = $row = $eventstable->fetchAll($select);
	  
    $this->view->error = 0;

		if ($this->getRequest()->isPost()) {

			foreach ($row as $result) {

				//Accrodeing to event  location entry in the seaocore location table.
				if (!empty($result['location'])) {
					$seao_locationid = $seLocationsTable->getLocationItemId($result['location'], '', 'event', $result['event_id']);

					//event table entry of location id.
					Engine_Api::_()->getItemTable('event')->update(array('seao_locationid'=>  $seao_locationid), array('event_id =?' => $result['event_id']));
				}
			}
			$this->view->error = 1;
		}
  }

  public function grouplocations() {
  
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'group')
            ->where('enabled = ?', 1);
    $check_group = $select->query()->fetchObject();
    if (!empty($check_group)) {
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
  }
  //FOR EVENT WORK
  public function userlocationsAction() {
  
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitetagcheckin_admin_main', array(), 'sitetagcheckin_admin_main_userlocations');

    $this->view->userSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.usersettings');
		$usersTable = Engine_Api::_()->getDbtable('users', 'user');
		$usersTableName = $usersTable->info('name');

		$profilemapsTable = Engine_Api::_()->getDbtable('profilemaps', 'sitetagcheckin');
		$profilemapsTablename = $profilemapsTable->info('name');
		
		$select = $profilemapsTable->select()->from($profilemapsTablename); 
		$this->view->option_id = $option_id =  $profilemapsTable->fetchAll($select);
		if (count($option_id) != 0) {
			$option_id_location = array();
			
			foreach($option_id as $optionId) {
				$option_id_location[] = $optionId['profile_type'];
			}
			
			$valuesTable = Engine_Api::_()->fields()->getTable('user', 'values');
			$valuesTableName = $valuesTable->info('name');

			$select = $valuesTable->select()->setIntegrityCheck(false)
								->from($valuesTableName)
								->join($usersTableName, "$valuesTableName.item_id = $usersTableName.user_id", null)
								->where($valuesTableName . '.field_id IN (?)', (array) $option_id_location)
								->where($valuesTableName . '.value <> ?', '')
								->where($usersTableName . '.seao_locationid = ?', 0);
								//->where($usersTableName . '.location <> ?', '');
			$this->view->row = $row = $valuesTable->fetchAll($select);
    }

// 		$metaTable = Engine_Api::_()->fields()->getTable('user', 'meta');
// 		$metaTableName = $metaTable->info('name');
// 		
// 		$select = $metaTable->select()
// 						->from($metaTableName, array('type'))
// 						->where($metaTableName . '.type = ?', 'location')
// 						->where($metaTableName . '.display = ?', '1')
// 						->where($metaTableName . '.search = ?', '1');
// 		$metaResults = $metaTable->fetchAll($select);
// 		if (count($metaResults) != 0) {
// 		
// 			$usersTable = Engine_Api::_()->getDbtable('users', 'user');
// 			$usersTableName = $usersTable->info('name');
// 		
// 			$searchTable = Engine_Api::_()->fields()->getTable('user', 'search');
// 			$searchTableName = $searchTable->info('name');
// 			
// 			$select = $searchTable->select()
// 			->setIntegrityCheck(false)
// 														->from($searchTableName, array('location', 'item_id'))
// 														->joinLeft($usersTableName, "$searchTableName.item_id = $usersTableName.user_id", null)
// 														->where($usersTableName . '.seao_locationid = ?', 0)
// 														->where($searchTableName . '.location <> ?', '');
// 			$this->view->row  = $searchTable->fetchAll($select);
// 		}
  }
  //FOR EVENT WORK
  public function albumlocationsAction() {
  
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitetagcheckin_admin_main', array(), 'sitetagcheckin_admin_main_albumlocations');
		
	  $albumstable = Engine_Api::_()->getItemTable('album');
	  $albumids = $albumstable->select()
															->from($albumstable->info('name'), array('album_id'))
															->where('seao_locationid = ?', 0)
															->query()
															->fetchAll(Zend_Db::FETCH_COLUMN);
															
		$addlocationsTable = Engine_Api::_()->getItemTable('sitetagcheckin_addlocation');
		$select = $addlocationsTable->select()
															->from($addlocationsTable->info('name'), array('params', 'resource_id'))
															->where('resource_type = ?', 'album');
		$row = $addlocationsTable->fetchAll($select);
		$count = 0;
		foreach ($row as $result) {
			$postData = $result->params;
			$location = $postData['checkin']['label'];
			if($location && in_array($result->resource_id, $albumids)) 
			$count ++;
			
		}
		$this->view->row = $count;
  }
  
  
  //FOR EVENT WORK
  public function locationsAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitetagcheckin_admin_main', array(), 'sitetagcheckin_admin_main_locations');
    if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('event') || Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('ynevent')) {

      $db = Zend_Db_Table_Abstract::getDefaultAdapter();
			$seao_locationid = $db->query("SHOW COLUMNS FROM engine4_event_events LIKE 'seao_locationid'")->fetch(); 
			if (empty($seao_locationid)) {
				$db->query("ALTER TABLE `engine4_event_events` ADD `seao_locationid` INT( 11 ) NOT NULL AFTER `category_id`");
			}

			$mainManage = $db->query("SELECT * FROM  `engine4_core_menuitems` WHERE  `name` LIKE  '%event_main_manage%'")->fetchAll();
			if (!empty($mainManage)) {
				$db->query("UPDATE `engine4_core_menuitems` SET `order` = '4' WHERE `engine4_core_menuitems`.`name` = 'event_main_manage' LIMIT 1 ;");
			}

			$mainCreate = $db->query("SELECT * FROM  `engine4_core_menuitems` WHERE  `name` LIKE  '%event_main_create%'")->fetchAll();
			if (!empty($mainCreate)) {
				$db->query("UPDATE `engine4_core_menuitems` SET `order` = '5' WHERE `engine4_core_menuitems`.`name` = 'event_main_create' LIMIT 1 ;");
			}
      
		//START THE WORK FOR MAKE WIDGETIZE PAGE OF Locatio or map.
		$select = new Zend_Db_Select($db);
		$select
						->from('engine4_core_pages')
						->where('name = ?', 'sitetagcheckin_index_by-locations')
						->limit(1);
		$info = $select->query()->fetch();
		if ( empty($info) ) {
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
		if ( empty($info) ) {
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
		$select = new Zend_Db_Select( $db ) ;
		$select
					->from( 'engine4_core_modules' )
					->where( 'name = ?' , 'event')
					->where( 'enabled = ?' , 1) ;
		$check_event = $select->query()->fetchObject() ;
		if ( !empty( $check_event ) ) {
			$select = new Zend_Db_Select( $db ) ;
			$select
					->from( 'engine4_core_pages' )
					->where( 'name = ?' , 'event_profile_index' )
					->limit( 1 ) ;
			$page_id = $select->query()->fetchObject()->page_id ;
			if ( !empty( $page_id ) ) {
			// container_id (will always be there)
			$select = new Zend_Db_Select( $db ) ;
			$select
					->from( 'engine4_core_content' )
					->where( 'page_id = ?' , $page_id )
					->where( 'type = ?' , 'container' )
					->where( 'name = ?' , 'main' )
					->limit( 1 ) ;
			$container_id = $select->query()->fetchObject()->content_id ;
			if ( !empty( $container_id ) ) {
				// left_id (will always be there)
				$select = new Zend_Db_Select( $db ) ;
				$select
							->from( 'engine4_core_content' )
							->where( 'parent_content_id = ?' , $container_id )
							->where( 'type = ?' , 'container' )
							->where( 'name = ?' , 'left' )
							->limit( 1 ) ;
				$left_id = $select->query()->fetchObject()->content_id ;
				if ( !empty( $left_id ) ) {
					// Check if it's already been placed
					$select = new Zend_Db_Select( $db ) ;
					$select
								->from( 'engine4_core_content' )
								->where( 'parent_content_id = ?' , $left_id )
								->where( 'type = ?' , 'widget' )
								->where( 'name = ?' , 'sitetagcheckin.syncevents-location' ) ;
					$info = $select->query()->fetch() ;
					if ( empty( $info ) ) {
						// tab on profile
						$db->insert( 'engine4_core_content' , array (
						'page_id' => $page_id ,
						'type' => 'widget' ,
						'name' => 'sitetagcheckin.syncevents-location' ,
						'parent_content_id' => $left_id ,
						'order' => 50,
						'params' => '' ,
						)) ;
					}
				}
			}
		}
	}
	
		//EVENT PROFILE PAGE
		$select = new Zend_Db_Select( $db ) ;
		$select
					->from( 'engine4_core_modules' )
					->where( 'name = ?' , 'ynevent')
					->where( 'enabled = ?' , 1) ;
		$check_event = $select->query()->fetchObject() ;
		if ( !empty( $check_event ) ) {
			$select = new Zend_Db_Select( $db ) ;
			$select
					->from( 'engine4_core_pages' )
					->where( 'name = ?' , 'ynevent_profile_index' )
					->limit( 1 ) ;
			$page_id = $select->query()->fetchObject()->page_id ;
			if ( !empty( $page_id ) ) {
			// container_id (will always be there)
			$select = new Zend_Db_Select( $db ) ;
			$select
					->from( 'engine4_core_content' )
					->where( 'page_id = ?' , $page_id )
					->where( 'type = ?' , 'container' )
					->where( 'name = ?' , 'main' )
					->limit( 1 ) ;
			$container_id = $select->query()->fetchObject()->content_id ;
			if ( !empty( $container_id ) ) {
				// left_id (will always be there)
				$select = new Zend_Db_Select( $db ) ;
				$select
							->from( 'engine4_core_content' )
							->where( 'parent_content_id = ?' , $container_id )
							->where( 'type = ?' , 'container' )
							->where( 'name = ?' , 'left' )
							->limit( 1 ) ;
				$left_id = $select->query()->fetchObject()->content_id ;
				if ( !empty( $left_id ) ) {
					// Check if it's already been placed
					$select = new Zend_Db_Select( $db ) ;
					$select
								->from( 'engine4_core_content' )
								->where( 'parent_content_id = ?' , $left_id )
								->where( 'type = ?' , 'widget' )
								->where( 'name = ?' , 'sitetagcheckin.syncevents-location' ) ;
					$info = $select->query()->fetch() ;
					if ( empty( $info ) ) {
						// tab on profile
						$db->insert( 'engine4_core_content' , array (
						'page_id' => $page_id ,
						'type' => 'widget' ,
						'name' => 'sitetagcheckin.syncevents-location' ,
						'parent_content_id' => $left_id ,
						'order' => 50,
						'params' => '' ,
						)) ;
					}
				}
			}
		}
	}

			$eventstable = Engine_Api::_()->getItemTable('event');
			$select = $eventstable->select()->where('location <> ?', '')->where('seao_locationid = ?', 0);
			$this->view->row  = $row = $eventstable->fetchAll($select);
	  }
  }
  
  public function manageAction() {

		//GET NAVIGATION
     $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitetagcheckin_admin_main', array(), 'sitetagcheckin_admin_main_settings');

		$this->view->usersettings = $userSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.usersettings');
		if (empty($userSettings)) {
		  return;
		}
		
		//FETCH MAPPING DATA
    $tableCategory = Engine_Api::_()->getDbtable('profilemaps', 'sitetagcheckin');
    $tableCategoryName = $tableCategory->info('name');
    
	  $optionsTable = Engine_Api::_()->fields()->getTable('user', 'options');
		$optionsTableName = $optionsTable->info('name');

	  $metaTable = Engine_Api::_()->fields()->getTable('user', 'meta');
		$metaTableName = $metaTable->info('name');
		
    $select = $optionsTable->select()
            ->setIntegrityCheck(false)
            ->from($optionsTableName, array('option_id', 'field_id', 'label'))
            ->joinLeft($tableCategoryName, "$optionsTableName.option_id = $tableCategoryName.option_id", array('profile_type', 'profilemap_id'))
            ->joinLeft($metaTableName, "$tableCategoryName.profile_type = $metaTableName.field_id", array('label as labelLocation'))
            ->where($optionsTableName . ".field_id = ?", 1);
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
		$this->view->paginator->setItemCountPerPage(500);
  }
  
  
  //ACTION FOR MAP THE PROFILE WITH CATEGORY
  public function mapAction() {

    $this->_helper->layout->setLayout('admin-simple');

    //GENERATE THE FORM
    $form = $this->view->form = new Sitetagcheckin_Form_Admin_Map();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        //SAVE THE NEW MAPPING
        $row = Engine_Api::_()->getDbtable('profilemaps', 'sitetagcheckin')->createRow();
        $row->profile_type = $values['profile_type'];
        $row->option_id = $this->_getParam('option_id');
        $row->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
      ));
    }
    $this->renderScript('admin-settings/map.tpl');
  }
  
  //ACTION FOR DELETE MAPPING 
  public function deleteAction() {
    $this->_helper->layout->setLayout('admin-simple');

		//GET MAPPING ID
    $this->view->profilemap_id = $profilemap_id = $this->_getParam('profilemap_id');

    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

				//GET MAPPING ITEM
        $sitetagcheckin_profilemap = Engine_Api::_()->getItem('sitetagcheckin_profilemap', $profilemap_id);

				//DELETE MAPPING
        $sitetagcheckin_profilemap->delete();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Mapping deleted successfully !'))
      ));
    }
    $this->renderScript('admin-settings/delete.tpl');
  }
  
  public function groupProfilePage() {

     $db = Zend_Db_Table_Abstract::getDefaultAdapter();
		//EVENT PROFILE PAGE
			$select = new Zend_Db_Select( $db ) ;
			$select
						->from( 'engine4_core_modules' )
						->where( 'name = ?' , 'group')
						->where( 'enabled = ?' , 1) ;
			$check_event = $select->query()->fetchObject() ;

			if ( !empty( $check_event ) ) {
				$select = new Zend_Db_Select( $db ) ;
				$select
						->from( 'engine4_core_pages' )
						->where( 'name = ?' , 'group_profile_index' )
						->limit( 1 ) ;
				$page_id = $select->query()->fetchObject()->page_id ;
				if ( !empty( $page_id ) ) {
				// container_id (will always be there)
				$select = new Zend_Db_Select( $db ) ;
				$select
						->from( 'engine4_core_content' )
						->where( 'page_id = ?' , $page_id )
						->where( 'type = ?' , 'container' )
						->where( 'name = ?' , 'main' )
						->limit( 1 ) ;
				$container_id = $select->query()->fetchObject()->content_id ;
				if ( !empty( $container_id ) ) {

					// left_id (will always be there)
					$select = new Zend_Db_Select( $db ) ;
					$select
								->from( 'engine4_core_content' )
								->where( 'parent_content_id = ?' , $container_id )
								->where( 'type = ?' , 'container' )
								->where( 'name = ?' , 'middle' )
								->where( 'page_id = ?' , $page_id )
								->limit( 1 ) ;
					$middle_id = $select->query()->fetchObject()->content_id; 

					if ( !empty( $middle_id ) ) {
						$select = new Zend_Db_Select( $db ) ;
						$select
									->from( 'engine4_core_content' )
									->where( 'parent_content_id = ?' , $middle_id )
									->where( 'type = ?' , 'widget' )
									->where( 'name = ?' , 'core.container-tabs' )
									->where( 'page_id = ?' , $page_id )
									->limit( 1 ) ;
						$tab_id = $select->query()->fetchObject()->content_id;
						if (!empty($tab_id)) {
							// Check if it's already been placed
							$select = new Zend_Db_Select( $db ) ;
							$select
										->from( 'engine4_core_content' )
										->where( 'parent_content_id = ?' , $tab_id )
										->where( 'type = ?' , 'widget' )
										->where( 'name = ?' , 'sitetagcheckin.profile-map-sitetagcheckin' ) ;
							$info = $select->query()->fetch();
							if ( empty( $info ) ) {
								// tab on profile
								$db->insert( 'engine4_core_content' , array (
								'page_id' => $page_id ,
								'type' => 'widget' ,
								'name' => 'sitetagcheckin.profile-map-sitetagcheckin' ,
								'parent_content_id' => $tab_id,
								'order' => 30,
								'params' => '{"title":"Map","titleCount":false,"checkin_show_options":"1","checkin_map_height":"500","nomobile":"0","name":"sitetagcheckin.profile-map-sitetagcheckin"}' ,
								)) ;
							}
						}
					}
				}
			}
		}
  }
  
	//ACTION FOR SET THE DEFAULT MAP CENTER POINT
  public function setDefaultMapCenterPoint($oldLocation, $newLocation) {

    if ($oldLocation !== $newLocation && $newLocation !== "World" && $newLocation !== "world") {
        $locationResults = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getLatLong(array('location' => $newLocation, 'module' => 'Geo-Location, Geo-Tagging, Check-Ins & Proximity Search'));
        if(!empty($locationResults['latitude']) && !empty($locationResults['longitude'])) {
            $latitude = $locationResults['latitude'];
            $longitude = $locationResults['longitude'];
        }

      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitetagcheckin.map.latitude', $latitude);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitetagcheckin.map.longitude', $longitude);
    }
  }  
}