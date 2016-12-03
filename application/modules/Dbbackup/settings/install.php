<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Dbbackup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: install.php 2010-10-25 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
class Dbbackup_Installer extends Engine_Package_Installer_Module {

  function onPreInstall() {
		$PRODUCT_TYPE = 'backup';
		$PLUGIN_TITLE = 'Dbbackup';
		$PLUGIN_VERSION = '4.2.3';
		$PLUGIN_CATEGORY = 'plugin';
		$PRODUCT_DESCRIPTION = 'Backup and Restore';
		$_PRODUCT_FINAL_FILE = 'license4.php';
		$_BASE_FILE_NAME = 'backupnew';
    $PRODUCT_TITLE = 'Backup and Restore';
    $SocialEngineAddOns_version = '4.8.5';
    $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
    $is_file = file_exists($file_path);
    if (empty($is_file)) {
      include_once APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/license5.php";
    } else {
			if( !empty($_PRODUCT_FINAL_FILE) ) {
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
    parent::onPreInstall();
  }

  function onInstall() {
		$db     = $this->getDb();


		$dbbackup_set_time = time();

//		$select = new Zend_Db_Select($db);
//		$select
//			->from('engine4_dbbackup_settings')
//			->where('name = ?', 'backup_optionsettings')
//			->where('settings_id = ?', 9);
//		$check_module = $select->query()->fetchObject();
//		if( !empty($check_module) ) {
//			$db->update('engine4_dbbackup_settings', array(
//				'settings_id'    => 10,
//				'name' => 'backup_optionsettings'
//				), array('settings_id =?' => 9, 'name =?' => 'backup_optionsettings'));
//		}


//		$select = new Zend_Db_Select($db);
//		$select
//			->from('engine4_dbbackup_settings')
//			->where('name = ?', 'backup_files')
//			->where('settings_id = ?', 8);
//		$check_module = $select->query()->fetchObject();
//		if( !empty($check_module) ) {
//			$db->update('engine4_dbbackup_settings', array(
//				'settings_id'    => 9,
//				'name' => 'backup_files'
//				), array('settings_id =?' => 8, 'name =?' => 'backup_files'));
//		}

		$db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` ='backup';");

		$select = new Zend_Db_Select($db);
		$select
			->from('engine4_dbbackup_settings')
			->where('name = ?', 'backup_rootfiles');
			//->where('settings_id = ?', 8);
		$check_module = $select->query()->fetchObject();
		if( empty($check_module) ) {
			$db->insert('engine4_dbbackup_settings', array(
				//'settings_id' => 8,
				'name' => 'backup_rootfiles',
				'value' => 1
			));
		}
		
		$select = new Zend_Db_Select($db);
		$select
			->from('engine4_dbbackup_settings')
			->where('name = ?', 'backup_modulesfiles')
			;
		$check_module = $select->query()->fetchObject();
		if( empty($check_module) ) {
			$db->insert('engine4_dbbackup_settings', array(
				'name' => 'backup_modulesfiles',
				'value' => 1
			));
		}			
		
		$db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
		('dbbackup.set.time', $dbbackup_set_time ),
		('dbbackup.check.variable', 0 ),  
		('dbbackup.time.var', 4752000 ),
		('dbbackup.get.path', 'Dbbackup/controllers/license/license3.php');");

		parent::onInstall();
  }
}
?>