<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Poke
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: install.php 2010-11-27 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
class Poke_Installer extends Engine_Package_Installer_Module {

  function onPreInstall() {

    $getErrorMsg = $this->getVersion(); 
    if (!empty($getErrorMsg)) {
      return $this->_error($getErrorMsg);
    }

    $db = $this->getDb();
    //CHECK SOCIALENGINEADDONS PLUGIN IS INSTALL OR NOT.
    $pluginName = 'Poke Plugin';

    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'seaocore');
    $check_socialengineaddons = $select->query()->fetchAll();

    $baseUrl = $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl();
    $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
    if ( strstr($url_string, "manage/install") ) {
      $calling_from = 'install';
    }
    else if ( strstr($url_string, "manage/query") ) {
      $calling_from = 'queary';
    }
    $explode_base_url = explode("/", $baseUrl);
    foreach ( $explode_base_url as $url_key ) {
      if ( $url_key != 'install' ) {
        $core_final_url .= $url_key . '/';
      }
    }

    if( empty($check_socialengineaddons) ) {
      // Page plugin is not install at your site.
return $this->_error('<div class="global_form"><div><div>The SocialEngineAddOns Core Plugin is not installed on your site. Please download the latest version of this FREE plugin from your Client Area
on <a href="http://www.socialengineaddons.com" target="_blank">SocialEngineAddOns</a> and install on your site before installing this plugin.</div></div></div>');
    } else if( !empty($check_socialengineaddons) && empty($check_socialengineaddons[0]['enabled']) ) {
      // Plugin not Enable at your site
      return $this->_error("<span style='color:red'>Note: You have installed the SocialEngineAddOns Core Plugin but not enabled it on your site yet. Please enabled it first before installing the
      $pluginName .</span><br/> <a href='" . 'http://' . $core_final_url . "install/manage/'>Click here</a> to enabled the SocialEngineAddOns Core Plugin.");

    } else if( $check_socialengineaddons[0]['version'] < '4.2.0' ) {
      // Please activate page plugin
      return $this->_error('<div class="global_form"><div><div> You do not have the latest version of the SocialEngineAddOns Core Plugin. Please download the latest
version of this FREE plugin from your Client Area on <a href="http://www.socialengineaddons.com" target="_blank">SocialEngineAddOns</a> and upgrade this on your site.</div></div></div>');
    }

    $select = new Zend_Db_Select($db);
    $select
        ->from('engine4_core_settings')
        ->where('name = ?', 'poke.mailoption');
		$page_id = $select->query()->fetchObject();
		if( empty($page_id) ) {
				$db->insert('engine4_core_settings', array(
					'name' => 'poke.mailoption',
					'value'    => 1
				));
		}

    $select = new Zend_Db_Select($db);
    $select
        ->from('engine4_core_settings')
        ->where('name = ?', 'poke.title.turncation');
		$page_id = $select->query()->fetchObject();
		if( empty($page_id) ) {
				$db->insert('engine4_core_settings', array(
					'name' => 'poke.title.turncation',
					'value'    => 19
				));
		}

    $select = new Zend_Db_Select($db);
    $select
        ->from('engine4_core_settings')
        ->where('name = ?', 'poke.updateoption');
		$page_id = $select->query()->fetchObject();
		if( empty($page_id) ) {
				$db->insert('engine4_core_settings', array(
					'name' => 'poke.updateoption',
					'value'    => 1
				));
		}

		
		$select = new Zend_Db_Select($db);
    $select
        ->from('engine4_core_settings')
        ->where('name = ?', 'poke.conn.setting');
		$page_id = $select->query()->fetchObject();
		if( empty($page_id) ) {
				$db->insert('engine4_core_settings', array(
					'name' => 'poke.conn.setting',
					'value'    => 0
				));
		}
     
    $db = $this->getDb();
		// ------------------------------------------ SHOW TAB IN [MEMBER Home PAGE] -----------------------------------
		$select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'poke');
    $is_poke_object = $select->query()->fetchObject();

    if(empty($is_poke_object)) {

			// Insert the "Mix Widget" for "Member Home Page"
			$select = new Zend_Db_Select($db);
			$select
				->from('engine4_core_pages')
				->where('name = ?', 'user_index_home')
				->limit(1);
			$page_id = $select->query()->fetchObject();
			if(!empty($page_id))
			{
				$page_id = $page_id->page_id;
				// Find out the "Main Container ID".
				$select = new Zend_Db_Select($db);
				$select
					->from('engine4_core_content')
					->where('page_id = ?', $page_id)
					->where('type = ?', 'container')
					->limit(1);
				$container_id = $select->query()->fetchObject();
				if( !empty($container_id) )
				{
					$container_id = $container_id->content_id;
					// Find out the "Right Content ID".
					$select = new Zend_Db_Select($db);
					$select
						->from('engine4_core_content')
						->where('parent_content_id = ?', $container_id)
						->where('type = ?', 'container')
						->where('name = ?', 'right')
						->limit(1);
					$right_id = $select->query()->fetchObject();
					
					if( !empty($right_id) )
					{
						$right_id = $right_id->content_id;
						// Check the "Mix Widget", if it's already been placed
						$select = new Zend_Db_Select($db);
						$select
							->from('engine4_core_content')
							->where('page_id = ?', $page_id)
							->where('type = ?', 'widget')
							->where('name = ?', 'poke.list-pokeusers')
							;
						$info = $select->query()->fetch();
						if( empty($info) && !empty($right_id) )
						{
							$db->insert('engine4_core_content', array(
								'page_id' => $page_id,
								'type'    => 'widget',
								'name'    => 'poke.list-pokeusers',
								'parent_content_id' => $right_id,
								'order'   => 1,
								'params'  => '{"title":"Pokes"}',
							));
						}
						else if( empty($right_id) && !empty($info) ){
							$db->delete('engine4_core_content', array('page_id = ?' => $page_id, 'name = ?' => 'poke.list-pokeusers'));
						}
					}
				}
			}
       //IF SITEMOBILEAPP MODULE PLUGIN IS INSTALLED AND SUGGESTION PLUGIN IS GOING TO INSTALLED THEN WE WILL DEFAULT DISABLE POKES OPTION FROM DASHBOARD OF MOBILE APP.
      $select = new Zend_Db_Select($db);
      $select
              ->from('engine4_core_modules')
              ->where('name = ?', 'sitemobileapp')
              ->where('enabled = ?', '1');
      $sitemobileapp = $select->query()->fetchObject();
      if(!empty($sitemobileapp)) {
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'poke')
                ->where('enabled = ?', '1');
        $poke = $select->query()->fetchObject();
        if (empty($poke)) {
          $db->query('INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`, `enable_mobile`, `enable_tablet`, `enable_mobile_app`, `enable_tablet_app`) VALUES ("core_main_pokes", "poke", "Pokes", "Poke_Plugin_Menus::canCreatePoke", "{\"route\":\"poke_general\"}", "core_main", "", 24,1,1, 0, 0);');

        }
      }
    }

    parent::onPreInstall();
  }

	function onInstall() {
		parent::onInstall();
  }

  private function getVersion() {
  
    $db = $this->getDb();

    $errorMsg = '';
    $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

    $modArray = array(
      'seaocore' => '4.8.5',
      'sitemobile' => '4.6.0p3'
    );
    
    $finalModules = array();
    foreach ($modArray as $key => $value) {
    		$select = new Zend_Db_Select($db);
		$select->from('engine4_core_modules')
					->where('name = ?', "$key")
					->where('enabled = ?', 1);
		$isModEnabled = $select->query()->fetchObject();
			if (!empty($isModEnabled)) {
				$select = new Zend_Db_Select($db);
				$select->from('engine4_core_modules',array('title', 'version'))
					->where('name = ?', "$key")
					->where('enabled = ?', 1);
				$getModVersion = $select->query()->fetchObject();

				$isModSupport = strcasecmp($getModVersion->version, $value);
				if ($isModSupport < 0) {
					$finalModules[] = $getModVersion->title;
				}
			}
    }

    foreach ($finalModules as $modArray) {
      $errorMsg .= '<div class="tip"><span style="background-color: #da5252;color:#FFFFFF;">Note: You do not have the latest version of the "' . $modArray . '". Please upgrade "' . $modArray . '" on your website to the latest version available in your SocialEngineAddOns Client Area to enable its integration with "Mobile / Tablet Plugin".<br/> Please <a class="" href="' . $base_url . '/manage">Click here</a> to go Manage Packages.</span></div>';
    }

    return $errorMsg;
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
('poke','1')");
			$select = new Zend_Db_Select($db);
			$select
							->from('engine4_sitemobile_modules')
							->where('name = ?', 'poke')
							->where('integrated = ?', 0);
			$is_sitemobile_object = $select->query()->fetchObject();
      if($is_sitemobile_object)  {
				$actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
				$controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
				if($controllerName == 'manage' && $actionName == 'install') {
          $view = new Zend_View();
					$baseUrl = ( !empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://':'http://') .  $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
					$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
					$redirector->gotoUrl($baseUrl . 'admin/sitemobile/module/enable-mobile/enable_mobile/1/name/poke/integrated/0/redirect/install');
				} 
      }
    }
  }
}
?>