<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Mixsettings.php 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Facebookse_Model_DbTable_Mixsettings extends Engine_Db_Table {

  protected $_name = 'facebookse_mixsettings' ;
  protected $_rowClass = 'Facebookse_Model_Mixsetting' ;
  protected $_feedTypes;
  protected $_moduleSetting = array();

	public function getMixLikeItems($default = '') {

		$mixSettings = array();
    $tableName = $this->info('name');

		$coreTable = Engine_Api::_()->getDbtable('modules', 'core');
    $coreTableName = $coreTable->info('name');

    $select = $this->select()
										->setIntegrityCheck( false )
										->from($tableName, array ('resource_type', 'module_name', 'module', 'default', 'module_title'))
										->join( $coreTableName , "$coreTableName . name = $tableName . module", array('title') )                 							->where( $coreTableName . '.enabled = ?' , 1 );
    if (!empty($default)) {
      $select->where( $tableName . '.default = ?' , 1 );
      
    }
    $select->where( $tableName . '.module_enable = ?' , 1 );
		$row = $select->query()->fetchAll();
   
		if( !empty($row) ) {
			foreach($row as $modName) { 
        if ($modName['resource_type']) {  
          if (empty($modName['default']))
            $mixSettings[$modName['module']] = !empty($modName['module_name']) ? $modName['module_name'] : $modName['title'];
          else             
          $mixSettings[$modName['resource_type']] = !empty($modName['module_name']) ? $modName['module_name'] : $modName['title'];
        }
			}
		}
    
		return $mixSettings; 
	}
	  
  //FETCHING THE LOCATION INFO OF THE PAGE IF IT'S HAVE.
  public function getLocationinfo ($module, $module_temp, $table_id, $params = array()) {
   $plugin_pagelocation_table = Engine_Api::_()->getDbtable('locations', $module);
	 $plugin_pagelocation_tableName = $plugin_pagelocation_table->info('name');
	 $select = $plugin_pagelocation_table->select()
						->setIntegrityCheck(false)
						->from($plugin_pagelocation_tableName,$params)
		        ->where($module_temp.' = ?', $table_id);
	 $fbmetainfolocationTable = $plugin_pagelocation_table->fetchRow($select);
	 return $fbmetainfolocationTable;
  }

  //GETTTING THE METAINFO OF A REQUESTED MODULE:  
   Public function getMetainfo ($module = '', $resourcetype = '') { 
    if (empty($module) && empty($resourcetype)) return;
    if (!$this->_moduleSetting || ($this->_moduleSetting && $this->_moduleSetting->module != $module) || TRUE) {
        $this->_moduleSetting = $this->getModInfo($module, $resourcetype);
     }
     return $this->_moduleSetting;
  }
  
   //GETTTING THE METAINFO OF A REQUESTED MODULE:  
  Public function getActivityFeedInfo ($module, $activitytype = '') { 
		$fbmetainfo_tableName = $this->info('name');
		$select = $this->select()
						->setIntegrityCheck(false)
							->from($fbmetainfo_tableName)
							->where('module = ?', $module);
    if (!empty($activitytype))
      	$select->where('activityfeed_type = ?', $activitytype);						
		$select->limit(1);
 		$metainfos = $this->fetchRow($select);
		return $metainfos;
  }
  
  //CHECK IF THE MODULE IS ENABLED OR NOT.
   //GETTTING THE METAINFO OF A REQUESTED MODULE:  
  Public function isModuleEnbled ($module, $resourcetype = '') { 
		
    if (!$this->_moduleSetting || ($this->_moduleSetting && $this->_moduleSetting->module != $module)) {
        $this->_moduleSetting = $this->getModInfo($module, $resourcetype);
     }
     if($this->_moduleSetting)
        return $this->_moduleSetting->module_enable;
     return 0;
     
  }
  
  public function getModuleTypes($params = array('activityfeed_type', 'streampublishenable', 'module', 'module_name'))
  { 
    if( null === $this->_feedTypes ) {
      $this->_feedTypes = $this->select()
          ->from($this, $params)
          ->query()
          ->fetchAll();
    }
    return $this->_feedTypes;
  }
  
  //CHECK IF THE FACEBOOK LIKE BUTTON IS ENABLED ON THE CONTENT PAGE.
  public function checkLikeButton($module, $resourcetype = '')
  { 
    if (!$this->_moduleSetting || ($this->_moduleSetting && $this->_moduleSetting->module != $module)) { 
        $this->_moduleSetting = $this->getModInfo($module, $resourcetype);
     }
     if($this->_moduleSetting)
        return $this->_moduleSetting->enable;
     return 0;
  }
  
  //GET CUSTOM LIKE BUTTON ICON.
  public function getFBIconCustom($module = '', $resourcetype = '') {
    
     if (!$this->_moduleSetting || ($this->_moduleSetting && ($this->_moduleSetting->module != $module && $this->_moduleSetting->resource_type != $resourcetype))) {
        $this->_moduleSetting = $this->getModInfo($module, $resourcetype);
     }
     
     return $this->_moduleSetting;   
  }
  
  public function getModInfo($module = '', $resourcetype = '') {
    $coreTable = Engine_Api::_()->getDbtable('modules', 'core');
    $coreTableName = $coreTable->info('name');
    $fbmetainfo_tableName = $this->info('name');
		$select = $this->select()	
              ->setIntegrityCheck(false)
							->from($fbmetainfo_tableName);
    if ((!empty($resourcetype) && $resourcetype != 'home') || (!empty($module) && $module != 'home'))
            $select->join( $coreTableName , "$coreTableName . name = $fbmetainfo_tableName . module", NULL )                 							->where( $coreTableName . '.enabled = ?' , 1 );
     if (!empty($module))
      $select->where('module = ?', $module);
    if (!empty($resourcetype))
      	$select->where('resource_type = ?', $resourcetype);
    						
		$select->limit(1);
		$settings = $this->fetchRow($select);
    
    return $settings;
  }
  
  public function checkDefaultModule($module = '', $resourcetype = '') {
    if (!$this->_moduleSetting || ($this->_moduleSetting && $this->_moduleSetting->module != $module)) { 
        $this->_moduleSetting = $this->getModInfo($module, $resourcetype);
     }
     if($this->_moduleSetting)
        return $this->_moduleSetting->default;
     return 0;
    
  }
}
?>