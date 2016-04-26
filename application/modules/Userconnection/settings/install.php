<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    User Connection
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Install.php 2010-07-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Userconnection_Installer extends Engine_Package_Installer_Module
{
	function onPreInstall()
	{
		$PRODUCT_TYPE = 'userconnection';
		$PLUGIN_TITLE = 'Userconnection';
		$PLUGIN_VERSION = '4.3.0';
		$PLUGIN_CATEGORY = 'plugin';
		$PRODUCT_DESCRIPTION = 'Userconnection';
		$_PRODUCT_FINAL_FILE = 'license3.php';
		$_BASE_FILE_NAME = 'userconnectionnew';
    $PRODUCT_TITLE = 'Userconnection';
    $SocialEngineAddOns_version = '4.8.5';
    $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
    $is_file = file_exists($file_path);
    if (empty($is_file)) {
      include_once APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/license4.php";
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

  function onInstall()
  {
		$db = $this->getDb();
		$db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` ='userconnection';");

    parent::onInstall();
  }
}
?>