<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventinvite
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventinvite_Installer extends Engine_Package_Installer_Module {

    function onPreInstall() {
        $db = $this->getDb();

        //CHECK THAT SITEEVENT PLUGIN IS ACTIVATED OR NOT
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_settings')
                ->where('name = ?', 'siteevent.isActivate')
                ->limit(1);
        $siteevent_settings = $select->query()->fetchAll();
        if (!empty($siteevent_settings)) {
            $siteevent_is_active = 1;
        } else {
            $siteevent_is_active = 0;
        }

        //CHECK THAT SITEEVENT PLUGIN IS INSTALLED OR NOT
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'siteevent')
                ->where('enabled = ?', 1);
        $check_siteevent = $select->query()->fetchObject();
        if (!empty($check_siteevent) && !empty($siteevent_is_active)) {
            $PRODUCT_TYPE = 'siteeventinvite';
            $PLUGIN_TITLE = 'Siteeventinvite';
            $PLUGIN_VERSION = '4.7.1';
            $PLUGIN_CATEGORY = 'plugin';
            $PRODUCT_DESCRIPTION = 'Advanced Events - Inviter and Promotion Extension';
            $PRODUCT_TITLE = 'Advanced Events - Inviter and Promotion Extension';
            $_PRODUCT_FINAL_FILE = 0;
            $SocialEngineAddOns_version = '4.8.10p4';
            $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
            $is_file = file_exists($file_path);
            if (empty($is_file)) {
                include APPLICATION_PATH . "/application/modules/Siteevent/controllers/license/license3.php";
            } else {
                include $file_path;
            }

            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_settings', array('value'))
                    ->where('name = ?', 'eventinvite.show.webmail');
            $inviteModules = $select->query()->fetchColumn();
            if (!empty($inviteModules)) {
                $webmail_values = unserialize($inviteModules);
                //IF FACEBOOK_MAIL DOES NOT EXIST IN TABLE THEN INSERT THIS 
                if (!in_array('facebook_mail', $webmail_values)) {
                    $webmail_values[] = 'facebook_mail';
                }
                //IF LINKEDIN MAIL DOES NOT EXIST IN TABLE THEN INSERT THIS  
                if (!in_array('linkedin_mail', $webmail_values)) {
                    $webmail_values[] = 'linkedin_mail';
                }
                $select = new Zend_Db_Select($db);
                $select
                        ->from('engine4_core_settings', array('value'))
                        ->where('name = ?', 'eventinvite.show.webmail');
                $inviteModules = $select->query()->fetchColumn();
                if (!empty($inviteModules)) {
                    $webmail_values = unserialize($inviteModules);
                    //IF FACEBOOK_MAIL DOES NOT EXIST IN TABLE THEN INSERT THIS 
                    if (!in_array('facebook_mail', $webmail_values)) {
                        $webmail_values[] = 'facebook_mail';
                    }
                    //IF LINKEDIN MAIL DOES NOT EXIST IN TABLE THEN INSERT THIS  
                    if (!in_array('linkedin_mail', $webmail_values)) {
                        $webmail_values[] = 'linkedin_mail';
                    }
                    //IF TWITTER MAIL DOES NOT EXIST IN TABLE THEN INSERT THIS  
                    if (!in_array('twitter_mail', $webmail_values)) {
                        $webmail_values[] = 'twitter_mail';
                    }
                    
                    if (!in_array('csvFileImport', $webmail_values)) {
                        $webmail_values[] = 'csvFileImport';
                    }
                    
                    $webmail_values = serialize($webmail_values);

                    $db->query("UPDATE `engine4_core_settings` SET `value` = '$webmail_values' WHERE `engine4_core_settings`.`name` = 'eventinvite.show.webmail' LIMIT 1;");
                } else {
                    $db->query('INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
				("eventinvite.show.webmail", \'a:7:{i:0;s:5:"gmail";i:1;s:5:"yahoo";i:2;s:11:"window_mail";i:3;s:3:"aol";i:4;s:13:"facebook_mail";i:5;s:13:"linkedin_mail";i:6;s:12:"twitter_mail";i:7;s:13:"csvFileImport"}\');'
                    );
                }

                $eventTime = time();
                $db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
			('siteeventinvite.basetime', $eventTime ),
			('siteeventinvite.isvar', 0 ),
			('siteeventinvite.filepath', 'Siteeventinvite/controllers/license/license2.php');");
            }
            parent::onPreInstall();
        } elseif (!empty($check_siteevent) && empty($siteevent_is_active)) {
            $baseUrl = $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl();
            $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
            if (strstr($url_string, "manage/install")) {
                $calling_from = 'install';
            } else if (strstr($url_string, "manage/query")) {
                $calling_from = 'queary';
            }
            $explode_base_url = explode("/", $baseUrl);
            foreach ($explode_base_url as $url_key) {
                if ($url_key != 'install') {
                    $core_final_url .= $url_key . '/';
                }
            }

            return $this->_error("<span style='color:red'>Note: You have installed the 'Advanced Events Plugin' but not activated it on your site yet. Please activate it first before installing the 'Advanced Events - Inviter and Promotion Extension'.</span><br/> <a href='" . 'http://' . $core_final_url . "admin/siteevent/settings/readme'>Click here</a> to activate the Advanced Events Plugin.");
        } else {
            $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
            return $this->_error("<span style='color:red'>Note: You have not installed the <a href='http://www.socialengineaddons.com/socialengine-advanced-events-plugin' target='_blank'>Advanced Events Plugin</a> on your site yet. Please install it first before installing the 'Advanced Events - Inviter and Promotion Extension'.</span><br/> <a href='" . $base_url . "/manage'>Click here</a> to go Manage Packages.");
        }
    }

  function onInstall() {
    $db = $this->getDb();

    $db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` ='siteeventinvite';");
    //MAKING A COLOMN IN THE INVITE TABLE

    $type_array = $db->query("SHOW COLUMNS FROM `engine4_invites` LIKE 'social_profileid'")->fetch();
    if (empty($type_array)) {
      $run_query = $db->query("ALTER TABLE `engine4_invites` ADD `social_profileid` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL AFTER `new_user_id` ");
    }

    //ADDING A COLOMN SERVICE PROVIDEER AND INVITE TYPES:
    $type_array = $db->query("SHOW COLUMNS FROM `engine4_invites` LIKE 'service'")->fetch();
    if (empty($type_array)) {
      $run_query = $db->query("ALTER TABLE `engine4_invites` ADD `service` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL AFTER `new_user_id` ");
    }

    $type_array = $db->query("SHOW COLUMNS FROM `engine4_invites` LIKE 'invite_type'")->fetch();
    if (empty($type_array)) {
      $run_query = $db->query("ALTER TABLE `engine4_invites` ADD `invite_type` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL AFTER `new_user_id` ");
    }
    //ADDDING A COLUMN OF INVITED USERS DISPLAY NAME.
    $type_array = $db->query("SHOW COLUMNS FROM `engine4_invites` LIKE 'displayname'")->fetch();
    if (empty($type_array)) {
      $run_query = $db->query("ALTER TABLE `engine4_invites` ADD `displayname` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL AFTER `new_user_id` ");
    }

    parent::onInstall();
  }

  function onEnable() {

        $db = $this->getDb();

        $db->update('engine4_core_menuitems', array('enabled' => 1), array('name = ?' => 'siteevent_admin_main_invite'));

        parent::onEnable();
    }

    function onDisable() {

        $db = $this->getDb();

        $db->update('engine4_core_menuitems', array('enabled' => 0), array('name = ?' => 'siteevent_admin_main_invite'));

        parent::onDisable();
    }

    public function onPostInstall() {

			//WORK FOR THE WORD CHANGES IN THE ADVANCED EVENT PLUGIN .CSV FILE.
			$actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
			$controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
			if ($controllerName == 'manage' && ($actionName == 'install' || $actionName == 'query')) {
					$view = new Zend_View();
					$baseUrl = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
					$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
					if ($actionName == 'install') {
							$redirector->gotoUrl($baseUrl . 'admin/siteevent/settings/language/redirect/install');
					} else {
							$redirector->gotoUrl($baseUrl . 'admin/siteevent/settings/language/redirect/query');
					}
			}
    }

}