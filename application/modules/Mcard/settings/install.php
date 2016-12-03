<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Mcard
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2010-10-13 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Mcard_Installer extends Engine_Package_Installer_Module {

	function onPreInstall()
	{
		$PRODUCT_TYPE = 'mcard';
		$PLUGIN_TITLE = 'Mcard';
		$PLUGIN_VERSION = '4.6.0p1';
		$PLUGIN_CATEGORY = 'plugin';
		$PRODUCT_DESCRIPTION = 'Create a plugin for Social Engine that gives members nice looking Membership Cards';
		$_PRODUCT_FINAL_FILE = 'license3.php';
		$_BASE_FILE_NAME = 'mcardnew';
    $PRODUCT_TITLE = 'Membership Card';
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
    $db     = $this->getDb();

    // Insert the "Mcard Widget" for "Member Profile Page"
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'user_profile_index')
      ->limit(1);
    $page_id = $select->query()->fetchObject()->page_id;
    if(!empty($page_id))
    {
      // Find out the "Main Container ID".
      $select = new Zend_Db_Select($db);
      $select
        ->from('engine4_core_content')
        ->where('page_id = ?', $page_id)
        ->where('type = ?', 'container')
        ->limit(1);
      $container_id = $select->query()->fetchObject()->content_id;

      // Find out the "Right Content ID".
      $select = new Zend_Db_Select($db);
      $select
        ->from('engine4_core_content')
        ->where('parent_content_id = ?', $container_id)
        ->where('type = ?', 'container')
        ->where('name = ?', 'middle')
        ->limit(1);
      $right_id = $select->query()->fetchObject()->content_id;      
      
      // Find out the "Profile Tab ID".
      $select = new Zend_Db_Select($db);
      $select
        ->from('engine4_core_content')
        ->where('parent_content_id = ?', $right_id)
        ->where('type = ?', 'widget')
        ->where('name = ?', 'core.container-tabs')
        ->limit(1);
      $tab_id = $select->query()->fetchObject()->content_id;    	
    	
	    // Check the "Mcard Widget", if it's already been placed
	    $select = new Zend_Db_Select($db);
	    $select
	      ->from('engine4_core_content')
	      ->where('page_id = ?', $page_id)
	      ->where('type = ?', 'widget')
	      ->where('name = ?', 'mcard.print-card')
	      ;
	    $info = $select->query()->fetch();
	    // If "People You May Know" widget not available in table.
	    if( empty($info) && !empty($tab_id))
	    {
	      // Set "People You May Know" widget
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type'    => 'widget',
	        'name'    => 'mcard.print-card',
	        'parent_content_id' => $tab_id,
	        'order'   => 999,
	        'params'  => '{"title":"Membership Card"}',
	      ));
	    }
	    else if(empty($right_id) && !empty($info)){
	    	$db->delete('engine4_core_content', array('page_id = ?' => $page_id, 'name = ?' => 'mcard.print-card'));
	    }
    }

		$db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` ='mcard';");

    parent::onInstall();
  }
  
  //SITEMOBILE CODE TO CALL MY.SQL ON POST INSTALL
    public function onPostInstall() {
        $moduleName = 'mcard';
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitemobile')
                ->where('enabled = ?', 1);
        $is_sitemobile_object = $select->query()->fetchObject();
        if (!empty($is_sitemobile_object)) {
            $db->query("INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`) VALUES
('$moduleName','1')");
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_sitemobile_modules')
                    ->where('name = ?', $moduleName)
                    ->where('integrated = ?', 0);
            $is_sitemobile_object = $select->query()->fetchObject();
            if ($is_sitemobile_object) {
                $actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
                $controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
                if ($controllerName == 'manage' && $actionName == 'install') {
                    $view = new Zend_View();
                    $baseUrl = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
                    $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
                    $redirector->gotoUrl($baseUrl . 'admin/sitemobile/module/enable-mobile/enable_mobile/1/name/' . $moduleName . '/integrated/0/redirect/install');
                }
            }
        }
    }
}
