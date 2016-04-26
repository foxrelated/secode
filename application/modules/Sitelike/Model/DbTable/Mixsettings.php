<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Settings.php 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelike_Model_DbTable_Mixsettings extends Engine_Db_Table {

  protected $_name = 'sitelike_mixsettings' ;
  protected $_rowClass = 'Sitelike_Model_Mixsetting' ;

  	public function getResults($params = array()) {
  	
    $tableName = $this->info('name');
    $select = $this->select()
										->from($this->info('name'), $params['column_name']);
		if(isset($params['mixsetting_id'])) {
			$select =  $select->where('mixsetting_id = ?', $params['mixsetting_id']);
		}
		
		if(isset($params['resource_type'])) {
			$select =  $select->where('resource_type = ?', $params['resource_type']);
		}
		
	  if(isset($params['enabled'])) {
			$select =  $select->where('enabled = ?', $params['enabled']);
		}
		
		return $select->query()->fetchAll();
	}
	
	public function getColumnValue($params = array()) {
  	
  	  $select = $this->select()
            ->from($this->info('name'), $params['columnValue'])
            ->where('resource_type = ?', $params['resource_type']);
            
		if(isset($params['module'])) {
			$select = $select->where('module = ?', $params['module']);
		}
		
    return $select->query()->fetchColumn();
	}
	
	/**
	* Get the settings according module type.
	*
	* @param string $modType
	*/
	public function getSetting($modType) {
    $tableName = $this->info('name');
    $select = $this->select()->from($tableName, 'value')->where('resource_type = ?', $modType);
		$row = $select->query()->fetchAll();
		if( !empty($row) ) {
			return $row[0]['value'];
		}
		return 0;
	}

	/**
	* Set the settings.
	*
	* @param string $key
	* @param string $value
	*/
  public function setSetting($key, $value){
		$this->update(array('value' => $value), array('resource_type =?' => $key));
	}

	/**
	* Get the mix like settings.
	*
	*/
	public function getMixLikeSetting() {
		$mixSettings = array();
    $tableName = $this->info('name');

		$coreTable = Engine_Api::_()->getDbtable('modules', 'core');
    $coreTableName = $coreTable->info('name');

    $select = $this->select()
										->setIntegrityCheck( false )
										->from($tableName, array ( 'resource_type' , 'module'))
										->join( $coreTableName , "$coreTableName . name = $tableName . module" ,array('enabled') )
										//->where( $tableName . '.value = ?' , 1 )
										->where( $tableName . '.enabled = ?' , 1 )
										->where( $coreTableName . '.enabled = ?' , 1 );


    if (Engine_Api::_()->hasModuleBootstrap('sitemobile')) {
			$smModulesName = Engine_Api::_()->getDbtable('modules', 'sitemobile')->info('name');
      $select->join( $smModulesName , "$smModulesName . name = $tableName . module",array('') );
			$enable_type = null;
			if (Engine_Api::_()->sitemobile()->checkMode('tablet-mode')) {
				$enable_type = 'enable_tablet';
			} elseif (Engine_Api::_()->sitemobile()->checkMode('mobile-mode')) {
				$enable_type = 'enable_mobile';
			}

			if ($enable_type && Engine_Api::_()->sitemobile()->isApp()) {
				$enable_type .= '_app';
			}

			if ($enable_type) {
				$select->where("$smModulesName.$enable_type = ?", 1);
			}     
    }

		$row = $select->query()->fetchAll();
		if( !empty($row) ) {
			foreach($row as $modName) {
				$mixSettings[$modName['resource_type']]= $modName['resource_type'];
			}
		}
		return $mixSettings;
	}

	public function getMixLikeItems() {

		$mixSettings = array();
    $tableName = $this->info('name');

		$coreTable = Engine_Api::_()->getDbtable('modules', 'core');
    $coreTableName = $coreTable->info('name');

    $select = $this->select()
										->setIntegrityCheck( false )
										->from($tableName, array ( 'resource_type' , 'item_title'))
										->join( $coreTableName , "$coreTableName . name = $tableName . module" ,array('enabled') )
										//->where( $tableName . '.value = ?' , 1 )
										->where( $tableName . '.enabled = ?' , 1 )
										->where( $coreTableName . '.enabled = ?' , 1 );
		$row = $select->query()->fetchAll();

    if (Engine_Api::_()->hasModuleBootstrap('sitemobile')) {
			$smModulesName = Engine_Api::_()->getDbtable('modules', 'sitemobile')->info('name');
      $select->join( $smModulesName , "$smModulesName . name = $tableName . module",array('') );
			$enable_type = null;
			if (Engine_Api::_()->sitemobile()->checkMode('tablet-mode')) {
				$enable_type = 'enable_tablet';
			} elseif (Engine_Api::_()->sitemobile()->checkMode('mobile-mode')) {
				$enable_type = 'enable_mobile';
			}

			if ($enable_type && Engine_Api::_()->sitemobile()->isApp()) {
				$enable_type .= '_app';
			}

			if ($enable_type) {
				$select->where("$smModulesName.$enable_type = ?", 1);
			}     
    }

		if( !empty($row) ) {
			foreach($row as $modName) {
				$mixSettings[$modName['resource_type']] = $modName['item_title'];
			}
		}
		return $mixSettings; 
	}
}