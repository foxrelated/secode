<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Birthday
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Core.php 7244 2010-09-01 01:49:53Z john $
 * @author     Jung
 */
class Birthdayemail_Api_Core extends Core_Api_Abstract
{
  // For SE4.1.1
  public function canRunTask($module,$taskPlugin, $old_started_last){
    $taskTable = Engine_Api::_()->getDbtable('tasks', 'core');
    $task= $taskTable->fetchRow(array('module = ?' => $module,'plugin = ?' => $taskPlugin));
                   if($task){                    
                           if(time()>=($task->timeout + $old_started_last)){
                           return 1;
                           }
                    return 0;           
                   }
    return 0;     
  }

	// check for CDN concept
	public function getCdnPath(){
		$storagemodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('storage');
		$storageversion = $storagemodule->version; 

		$db = Engine_Db_Table::getDefaultAdapter();
		$type_array = $db->query("SHOW COLUMNS FROM engine4_storage_servicetypes LIKE 'enabled'")->fetch();
		$cdn_path = "";

		if($storageversion >= '4.1.6' && !empty($type_array)) {
			$storageServiceTypeTable = Engine_Api::_()->getDbtable('serviceTypes', 'storage');
			$storageServiceTypeTableName = $storageServiceTypeTable->info('name');

			$storageServiceTable = Engine_Api::_()->getDbtable('services', 'storage');
			$storageServiceTableName = $storageServiceTable->info('name');

			$select = $storageServiceTypeTable->select()
									->setIntegrityCheck(false)
									->from($storageServiceTypeTableName, array(''))
									->join($storageServiceTableName, "$storageServiceTypeTableName.servicetype_id = $storageServiceTableName.servicetype_id", array('enabled', 'config', 'default'))
									->where("$storageServiceTypeTableName.plugin != ?", "Storage_Service_Local")
									->where("$storageServiceTypeTableName.enabled = ?", 1)
									->limit(1);
												
			$storageCheck = $storageServiceTypeTable->fetchRow($select); 
			if(!empty($storageCheck)) {
				if($storageCheck->enabled == 1 && $storageCheck->default == 1) {
					$config = Zend_Json::decode($storageCheck->config);
					$config_baseUrl = $config['baseUrl'];
					if(!empty($config_baseUrl)) {
						if (!preg_match('/http:\/\//', $config_baseUrl) && !preg_match('/https:\/\//', $config_baseUrl)) {
							$cdn_path.= "http://".$config_baseUrl;
						}
						else {
							$cdn_path.= $config_baseUrl;
						}
					}
				}
			}
		}
		return $cdn_path;
	}

}