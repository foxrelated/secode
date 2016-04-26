<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Suggestion_Installer extends Engine_Package_Installer_Module {

  function onPreInstall() {
    $getErrorMsg = $this->getVersion(); 
    if (!empty($getErrorMsg)) {
      return $this->_error($getErrorMsg);
    }

    $PRODUCT_TYPE = 'suggestion';
    $PLUGIN_TITLE = 'Suggestion';
    $PLUGIN_VERSION = '4.8.5p1';
    $PLUGIN_CATEGORY = 'plugin';
    $PRODUCT_DESCRIPTION = 'This plugin provides you the best tools to increase user engagement on your Social Network. This highly customizable plugin enables your site to recommend various content and friends to users, just like Facebook does, and is arguably the most useful social graph feature for your Social Network. The algorithms behind the suggestions are based on user relevance, and highlight content and people that the users might actually be interested in.';
    $_PRODUCT_FINAL_FILE = 'license3.php';
    $_BASE_FILE_NAME = 0;
    $PRODUCT_TITLE = 'Suggestions';
    $SocialEngineAddOns_version = '4.8.5';
    $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
    $is_file = file_exists($file_path);

    if (empty($is_file)) {
      include_once APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/license4.php";
    } else {
      if (!empty($_PRODUCT_FINAL_FILE)) {
        include_once APPLICATION_PATH . '/application/modules/' . $PLUGIN_TITLE . '/controllers/license/' . $_PRODUCT_FINAL_FILE;
      }
      $db = $this->getDb();
      $select = new Zend_Db_Select($db);
      $select->from('engine4_core_modules')->where('name = ?', $PRODUCT_TYPE);
      $is_Mod = $select->query()->fetchObject();
      if (empty($is_Mod)) {
        include_once $file_path;
      }
    }
    parent::onPreInstall();
  }

  function setRecommendationSettings() {
    $db = $this->getDb();
    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_suggestion_module_settings'")->fetch();
    $mixInfoTable_exist = $db->query("SHOW TABLES LIKE 'engine4_suggestion_mixinfos'")->fetch();
    if (!empty($table_exist) && !empty($mixInfoTable_exist)) {
      $getContent = $db->query("SELECT * FROM `engine4_suggestion_mixinfos`")->fetchAll();
      if (!empty($getContent)) {
        foreach ($getContent as $key) {
          $isModExist = $db->query("SELECT * FROM `engine4_suggestion_module_settings` WHERE `module` LIKE '" . $key['name'] . "' LIMIT 1")->fetch();
          if (!empty($isModExist)) {
            $db->query("UPDATE `engine4_suggestion_module_settings` SET `recommendation` = '" . $key['status'] . "' WHERE `engine4_suggestion_module_settings`.`module` LIKE '" . $key['name'] . "' LIMIT 1");
          }
        }
      }
    }
    if(!empty($table_exist))  {
      $db->query('INSERT IGNORE INTO `engine4_suggestion_module_settings` (`module`, `item_type`, `field_name`, `owner_field`, `item_title`, `button_title`, `enabled`, `notification_type`, `quality`, `link`, `popup`, `default`, `settings`) VALUES
        ("sitegroup", "sitegroup_group", "group_id", "owner_id", "Groups", "View this Group", 1, "sitegroup_suggestion", 1, 1, 1, 1, \'a:1:{s:7:"default";i:0;}\');');
    }
  }

  function onInstall() {
    $db = $this->getDb();
        
    // ADD INDEXING IN engine4_suggestion_albums Table
    $table = $db->query('SHOW TABLES LIKE \'engine4_suggestion_albums\'')->fetch();
    if (!empty($table)) {
			$getIndex = $db->query("SHOW INDEX FROM `engine4_suggestion_albums` WHERE Key_name = 'suggestion_id'")->fetch();
			if (empty($getIndex)) {
				$db->query("ALTER TABLE `engine4_suggestion_albums` ADD INDEX ( `suggestion_id` ) ");
			}
    }
    
    // ADD INDEXING IN engine4_suggestion_photos Table
    $table = $db->query('SHOW TABLES LIKE \'engine4_suggestion_photos\'')->fetch();
    if (!empty($table)) {
			$getIndex = $db->query("SHOW INDEX FROM `engine4_suggestion_photos` WHERE Key_name = 'photo_id'")->fetch();
			if (!empty($getIndex)) {
				$db->query("ALTER TABLE engine4_suggestion_photos DROP INDEX photo_id");
			}
    }
    
    // ADD INDEXING IN engine4_suggestion_photos Table
    $table = $db->query('SHOW TABLES LIKE \'engine4_suggestion_photos\'')->fetch();
    if (!empty($table)) {
			$getIndex = $db->query("SHOW INDEX FROM `engine4_suggestion_photos` WHERE Key_name = 'owner_type'")->fetch();
			if (!empty($getIndex)) {
				$db->query("ALTER TABLE engine4_suggestion_photos DROP INDEX owner_type");
			}
    }
    
    // ADD INDEXING IN engine4_suggestion_photos Table
    $table = $db->query('SHOW TABLES LIKE \'engine4_suggestion_photos\'')->fetch();
    if (!empty($table)) {
			$getIndex = $db->query("SHOW INDEX FROM `engine4_suggestion_photos` WHERE Key_name = 'collection_id'")->fetch();
			if (empty($getIndex)) {
				$db->query("ALTER TABLE `engine4_suggestion_photos` ADD INDEX ( `collection_id` )");
			}
    }
    
    // ADD INDEXING IN engine4_suggestion_rejected Table
    $table = $db->query('SHOW TABLES LIKE \'engine4_suggestion_rejected\'')->fetch();
    if (!empty($table)) {
			$getIndex = $db->query("SHOW INDEX FROM `engine4_suggestion_rejected` WHERE Key_name = 'owner_id'")->fetch();
			if (empty($getIndex)) {
				$db->query("ALTER TABLE `engine4_suggestion_rejected` ADD INDEX ( `owner_id` )");
			}
    }
    
    // ADD INDEXING IN engine4_suggestion_suggestions Table
    $table = $db->query('SHOW TABLES LIKE \'engine4_suggestion_suggestions\'')->fetch();
    if (!empty($table)) {
			$getIndex = $db->query("SHOW INDEX FROM `engine4_suggestion_suggestions` WHERE Key_name = 'owner_id'")->fetch();
			if (empty($getIndex)) {
				$db->query("ALTER TABLE `engine4_suggestion_suggestions` ADD INDEX (`owner_id`)");
			}
    }
    
    // ADD INDEXING IN engine4_suggestion_suggestions Table
    $table = $db->query('SHOW TABLES LIKE \'engine4_suggestion_suggestions\'')->fetch();
    if (!empty($table)) {
			$getIndex = $db->query("SHOW INDEX FROM `engine4_suggestion_suggestions` WHERE Key_name = 'entity'")->fetch();
			if (empty($getIndex)) {
				$db->query("ALTER TABLE `engine4_suggestion_suggestions` ADD INDEX ( `entity` , `entity_id` )");
			}
    }
    
    // ADD INDEXING IN engine4_suggestion_module_settings Table
    $table = $db->query('SHOW TABLES LIKE \'engine4_suggestion_module_settings\'')->fetch();
    if (!empty($table)) {
			$getIndex = $db->query("SHOW INDEX FROM `engine4_suggestion_module_settings` WHERE Key_name = 'module'")->fetch();
			if (empty($getIndex)) {
				$db->query("ALTER TABLE `engine4_suggestion_module_settings` ADD INDEX ( `module` )");
			}
    }
    
    // ADD INDEXING IN engine4_suggestion_rejected Table
    $table = $db->query('SHOW TABLES LIKE \'engine4_suggestion_rejected\'')->fetch();
    if (!empty($table)) {
			$getIndex = $db->query("SHOW INDEX FROM `engine4_suggestion_rejected` WHERE Key_name = 'entity'")->fetch();
			if (empty($getIndex)) {
				$db->query("ALTER TABLE `engine4_suggestion_rejected` ADD INDEX ( `entity` , `entity_id` )");
			}
    }
    
    // QUERY FOR SITEGROUP EXTENSIONS
    $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
("sitegroup_suggest_friend", "suggestion", "Suggest to Friends", "Suggestion_Plugin_Menus", \'{"route":"default", "class":"buttonlink icon_page_friend_suggestion smoothbox"}\', "sitegroup_gutter", NULL, 1, 0, 999),
("sitegroup_event_suggest_friend", "suggestion", "Suggest to Friends", "Suggestion_Plugin_Menus", \'{"route":"default", "icon":"application/modules/Suggestion/externals/images/sugg_blub.png"}\', "sitegroupevent_gutter", NULL, 1, 0, 999);');

    $this->setModValue();
    $this->setRecommendationSettings();
    $this->setSettings(
            'user', array('friend.sugg.link' => 'link', 'friend.sugg.mod' => 'quality', 'send.friend.popup' => 'popup', 'friend.sugg.level' => 'settings', 'accept.friend.popup' => 'settings')
    );

    // If "People you may know / Friend Suggestions & Inviter" plugin already installed on the site then "Suggestion / Recommendation Plugin" will not installed on the site.
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'peopleyoumayknow')
            ->where('enabled = ?', '1');
    $page_id_temp = $select->query()->fetchObject();
    if (!empty($page_id_temp)) {
      $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
      return $this->_error('<span style="color:red">Note: You already have the "People you may know / Friend Suggestions & Inviter" module/plugin installed on your site. Please disable the "People you may know / Friend Suggestions & Inviter" module/plugin and then install this plugin.</span><br/> <a href="' . $base_url . '/manage">Click here</a> to go Manage Packages.');
    }


    // Make An Upgrade File.
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_settings', array('value'))
            ->where('name = ?', 'suggestion.show.webmail');
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
      $webmail_values = serialize($webmail_values);

      $db->query("UPDATE `engine4_core_settings` SET `value` = '$webmail_values' WHERE `engine4_core_settings`.`name` = 'suggestion.show.webmail' LIMIT 1;");
    } else {

      $db->query('INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
        ("suggestion.show.webmail", \'a:7:{i:0;s:5:"gmail";i:1;s:5:"yahoo";i:2;s:11:"window_mail";i:3;s:3:"aol";i:4;s:13:"facebook_mail";i:5;s:13:"linkedin_mail";i:6;s:12:"twitter_mail";}\'),
        ("suggestion.friend.select", 1),
        ("suggestion.friend.notify", 1),
        ("suggestion.people.info", 1);'
      );
    }

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


    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_activity_notificationtypes')
            ->where('type = ?', 'friend_request');
    $page_id_temp = $select->query()->fetchObject();
    if (!empty($page_id_temp)) {
      $db->update('engine4_activity_notificationtypes', array(
          'handler' => 'suggestion.widget.request-accept'
              ), array('type =?' => 'friend_request'));
    }

    // Change the "Handler" for in `engine4_activity_notificationtypes` Table for one way friendship
    $isSuggModEnabled = $db->query("SELECT * FROM  `engine4_core_modules` WHERE  `name` LIKE  'suggestion' AND  `enabled` LIKE  '1' LIMIT 1 ;")->fetchAll();
    if (!empty($isSuggModEnabled)) {
      $db->query("UPDATE  `engine4_activity_notificationtypes` SET  `handler` =  'suggestion.widget.request-accept' WHERE  `engine4_activity_notificationtypes`.`type` =  'friend_follow_request' LIMIT 1 ;");
    }

    $isNameAvailable = $db->query("SELECT * FROM  `engine4_activity_notificationtypes` WHERE  `type` LIKE  'friend_suggestion' LIMIT 1")->fetchAll();
    if (!empty($isNameAvailable)) {
      $db->query('UPDATE  `engine4_activity_notificationtypes` SET  `handler` =  "suggestion.widget.get-notify" WHERE  `engine4_activity_notificationtypes`.`type` =  "friend_suggestion" LIMIT 1 ;');
    }

    $db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` ='suggestion';");

    // "Suggest to Friend" link for "Documents / Scribd iPaper Plugin".
    $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("document_gutter_suggest", "document", "Suggest to Friend", "Document_Plugin_Menus", "", "document_gutter", "", 1, 0, 6);');

    // "Suggest to Friend" link for "Blog Plugin".
    $isNameAvailable = $db->query("SELECT * FROM  `engine4_core_menuitems` WHERE  `name` LIKE  'blog_suggest_friend' LIMIT 1")->fetchAll();
    if (!empty($isNameAvailable)) {
      $db->query('UPDATE  `engine4_core_menuitems` SET  `params` =  \'{"route":"suggest_to_friend_link","class":"buttonlink icon_blog_friend_suggestion smoothbox"}\' WHERE  `engine4_core_menuitems`.`name` = "blog_suggest_friend" LIMIT 1 ;');
    }

    // "Suggest to Friend" link for "Classified Plugin".
    $isNameAvailable = $db->query("SELECT * FROM  `engine4_core_menuitems` WHERE  `name` LIKE  'classified_suggest_friend' LIMIT 1")->fetchAll();
    if (!empty($isNameAvailable)) {
      $db->query('UPDATE  `engine4_core_menuitems` SET  `params` =  \'{"route":"suggest_to_friend_link","class":"buttonlink icon_classified_friend_suggestion smoothbox"}\' WHERE  `engine4_core_menuitems`.`name` = "classified_suggest_friend" LIMIT 1 ;');
    }

    // "Suggest to Friend" link for "List Plugin".
    $isNameAvailable = $db->query("SELECT * FROM  `engine4_core_menuitems` WHERE  `name` LIKE  'list_suggest_friend' LIMIT 1")->fetchAll();
    if (!empty($isNameAvailable)) {
      $db->query('UPDATE  `engine4_core_menuitems` SET  `params` =  \'{"route":"suggest_to_friend_link","class":"buttonlink icon_list_friend_suggestion smoothbox"}\' WHERE  `engine4_core_menuitems`.`name` = "list_suggest_friend" LIMIT 1 ;');
    }

    $isNameAvailable = $db->query("SELECT * FROM  `engine4_activity_notificationtypes` WHERE  `type` LIKE  'list_suggestion' LIMIT 1")->fetchAll();
    if (!empty($isNameAvailable)) {
      $db->query('UPDATE  `engine4_activity_notificationtypes` SET  `handler` =  "suggestion.widget.get-notify" WHERE  `engine4_activity_notificationtypes`.`type` =  "list_suggestion" LIMIT 1 ;');
    }

    // "Suggest to Friend" link for "Recipe Plugin".
    $isNameAvailable = $db->query("SELECT * FROM  `engine4_core_menuitems` WHERE  `name` LIKE  'recipe_suggest_friend' LIMIT 1")->fetchAll();
    if (!empty($isNameAvailable)) {
      $db->query('UPDATE  `engine4_core_menuitems` SET  `params` =  \'{"route":"suggest_to_friend_link","class":"buttonlink icon_recipe_friend_suggestion smoothbox"}\' WHERE  `engine4_core_menuitems`.`name` = "recipe_suggest_friend" LIMIT 1 ;');
    }

    // "Suggest to Friend" link for "Photo Suggestion".
    $isNameAvailable = $db->query("SELECT * FROM  `engine4_activity_notificationtypes` WHERE  `handler` LIKE  'suggestion.widget.request-photo' LIMIT 1")->fetchAll();
    if (!empty($isNameAvailable)) {
      $db->query('UPDATE  `engine4_activity_notificationtypes` SET  `handler` =  "suggestion.widget.get-notify" WHERE  `engine4_activity_notificationtypes`.`type` =  "picture_suggestion" LIMIT 1 ;');
    }

    // "Suggest to Friend" link for "Page Suggestion".
    $db->query('UPDATE `engine4_activity_notificationtypes` SET `handler` = "suggestion.widget.get-notify" WHERE `engine4_activity_notificationtypes`.`handler` = "suggestion.widget.request-page";');

    $db->query('INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES  (1, "suggestion_photo", "view", 2, NULL),(2, "suggestion_photo", "view", 2, NULL), (3, "suggestion_photo", "view", 2, NULL), (4, "suggestion_photo", "view", 1, NULL);');


    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'siteestore')
            ->where('enabled = ?', '1');
    $siteestore_temp = $select->query()->fetchObject();
    if (!empty($siteestore_temp)) {

      $select = new Zend_Db_Select($db);
      $select->from('engine4_activity_notificationtypes')->where('type = ?', 'siteestore_suggestion');
      $fetch = $select->query()->fetchObject();
      if (empty($fetch)) {
        $db->query('
INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`,`is_request`, `handler`, `default`)VALUES (
"siteestore_suggestion", "suggestion", \'{item:$subject} has suggested to you a {item:$object:product}.\', "1", "suggestion.widget.get-notify", "1" )     
');
      }

      $select = new Zend_Db_Select($db);
      $select->from('engine4_suggestion_module_settings')->where('module = ?', 'siteestore');
      $fetch = $select->query()->fetchObject();
      if (empty($fetch)) {
        $db->query('
INSERT IGNORE INTO `engine4_suggestion_module_settings` (`module`, `item_type`, `field_name`, `owner_field`, `item_title`, `button_title`, `enabled`, `notification_type`, `quality`, `link`, `popup`, `recommendation`, `default`, `settings`) VALUES
("siteestore", "siteestore_product", "product_id", "owner_id", "Product", "View Product", 1, "siteestore_suggestion", 0, 1, 1, 1, 1, \'a:0:{}\');
');
      }

      $select = new Zend_Db_Select($db);
      $select->from('engine4_core_mailtemplates')->where('type = ?', 'notify_siteestore_suggestion');
      $fetch = $select->query()->fetchObject();
      if (empty($fetch)) {
        $db->query('
INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`)VALUES 
("notify_siteestore_suggestion", "suggestion", "[suggestion_sender], [suggestion_entity], [email], [link]"
);    
');
      }
      
      //IF SITEMOBILEAPP MODULE PLUGIN IS INSTALLED AND SUGGESTION PLUGIN IS GOING TO INSTALLED THEN WE WILL DEFAULT DISABLE SUGGESTION OPTION FROM DASHBOARD OF MOBILE APP.
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
                ->where('name = ?', 'suggestion')
                ->where('enabled = ?', '1');
        $suggestion = $select->query()->fetchObject();
        if (empty($suggestion)) {
          $db->query('INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`, `enable_mobile`, `enable_tablet`, `enable_mobile_app`, `enable_tablet_app`) VALUES ("core_main_suggestion", "suggestion", "Suggestions", NULL, "{\"route\":\"suggestion_explore_suggestions\"}", "core_main", "", 25, 1, 1, 0, 0);');

        }
      }
    }
    
    // INTEGRATE MAGENTO STORE PLUGIN WITH SUGGESTION 
    $this->_integrateSiteestorePlugin();
    
    // INTEGRATE STORE PLUGIN WITH SUGGESTION
    $this->_integrateSitestorePlugin();
    
    // INTEGRATE STORE PRODUCT PLUGIN WITH SUGGESTION
    $this->_integrateSitestoreproductPlugin();
    
    // INTEGRATION ADVANCED EVENT PLUGIN WITH SUGGESTION
    $this->_integrateWithSiteeventPlugin();

    parent::onInstall();
  }
  
  // INTEGRATE MAGENTO STORE PLUGIN WITH SUGGESTION 
  protected function _integrateSiteestorePlugin() {
    $db = $this->getDb();
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'siteestore')
            ->where('enabled = ?', '1');
    $siteestore_temp = $select->query()->fetchObject();
    if (!empty($siteestore_temp)) {

      $select = new Zend_Db_Select($db);
      $select->from('engine4_activity_notificationtypes')->where('type = ?', 'siteestore_suggestion');
      $fetch = $select->query()->fetchObject();
      if (empty($fetch)) {
        $db->query('
INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`,`is_request`, `handler`, `default`)VALUES (
"siteestore_suggestion", "suggestion", \'{item:$subject} has suggested to you a {item:$object:product}.\', "1", "suggestion.widget.get-notify", "1" )     
');
      }

      $select = new Zend_Db_Select($db);
      $select->from('engine4_suggestion_module_settings')->where('module = ?', 'siteestore');
      $fetch = $select->query()->fetchObject();
      if (empty($fetch)) {
        $db->query('
INSERT IGNORE INTO `engine4_suggestion_module_settings` (`module`, `item_type`, `field_name`, `owner_field`, `item_title`, `button_title`, `enabled`, `notification_type`, `quality`, `link`, `popup`, `recommendation`, `default`, `settings`) VALUES
("siteestore", "siteestore_product", "product_id", "owner_id", "Product", "View Product", 1, "siteestore_suggestion", 0, 1, 1, 1, 1, \'a:0:{}\');
');
      }

      $select = new Zend_Db_Select($db);
      $select->from('engine4_core_mailtemplates')->where('type = ?', 'notify_siteestore_suggestion');
      $fetch = $select->query()->fetchObject();
      if (empty($fetch)) {
        $db->query('
INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`)VALUES 
("notify_siteestore_suggestion", "suggestion", "[suggestion_sender], [suggestion_entity], [email], [link]"
);    
');
      }
    }
  }
  
  // INTEGRATE STORE PRODUCT PLUGIN WITH SUGGESTION
  protected function _integrateSitestoreproductPlugin() {
    $db = $this->getDb();
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitestoreproduct')
            ->where('enabled = ?', '1');
    $sitestore_temp = $select->query()->fetchObject();
    if (!empty($sitestore_temp)) {      
      $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name` , `module` , `label` , `plugin` ,`params`, `menu`, `enabled`, `custom`, `order`) VALUES ("sitestoreproduct_gutter_suggesttofriend", "suggestion", "Suggest to Friends", \'Sitestore_Plugin_Menus::showSitestoreproduct\', \'{"route":"suggest_to_friend_link","class":"buttonlink icon_review_friend_suggestion smoothbox", "type":"popup"}\', "sitestoreproduct_gutter", 1, 0, 999 )');
      
      $select = new Zend_Db_Select($db);
      $select->from('engine4_activity_notificationtypes')->where('type = ?', 'sitestoreproduct_suggestion');
      $fetch = $select->query()->fetchObject();
      if (empty($fetch)) {
        $db->query('
INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`,`is_request`, `handler`, `default`)VALUES (
"sitestoreproduct_suggestion", "suggestion", \'{item:$subject} has suggested to you a {item:$object:product}.\', "1", "suggestion.widget.get-notify", "1" )     
');
      }

      $select = new Zend_Db_Select($db);
      $select->from('engine4_suggestion_module_settings')->where('module = ?', 'sitestoreproduct');
      $fetch = $select->query()->fetchObject();
      if (empty($fetch)) {
        $db->query('
INSERT IGNORE INTO `engine4_suggestion_module_settings` (`module`, `item_type`, `field_name`, `owner_field`, `item_title`, `button_title`, `enabled`, `notification_type`, `quality`, `link`, `popup`, `recommendation`, `default`, `settings`) VALUES
("sitestoreproduct", "sitestoreproduct_product", "product_id", "owner_id", "Product", "View Product", 1, "sitestoreproduct_suggestion", 0, 1, 1, 1, 1, \'a:0:{}\');
');
      }

      $select = new Zend_Db_Select($db);
      $select->from('engine4_core_mailtemplates')->where('type = ?', 'notify_sitestoreproduct_suggestion');
      $fetch = $select->query()->fetchObject();
      if (empty($fetch)) {
        $db->query('
INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`)VALUES 
("notify_sitestoreproduct_suggestion", "suggestion", "[suggestion_sender], [suggestion_entity], [email], [link]"
);    
');
      }
    }
  }
  
  
  // INTEGRATE EVENT PLUGIN WITH SUGGESTION
  protected function _integrateWithSiteeventPlugin() {
    $db = $this->getDb();
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'siteevent')
            ->where('enabled = ?', '1');
    $sitestore_temp = $select->query()->fetchObject();
    if (!empty($sitestore_temp)) {
      $db->query('UPDATE `engine4_suggestion_module_settings` SET `enabled` = "0" WHERE `engine4_suggestion_module_settings`.`module` = "event" LIMIT 1');
      
      $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name` , `module` , `label` , `plugin` ,`params`, `menu`, `enabled`, `custom`, `order`) VALUES ("siteevent_gutter_suggesttofriend", "suggestion", "Suggest to Friends", \'Siteevent_Plugin_Menus::showSiteeventSuggestToFriendLink\', \'{"route":"suggest_to_friend_link","class":"buttonlink icon_review_friend_suggestion smoothbox", "type":"popup"}\', "siteevent_gutter", 1, 0, 999 )');
      
      $select = new Zend_Db_Select($db);
      $select->from('engine4_activity_notificationtypes')->where('type = ?', 'siteevent_suggestion');
      $fetch = $select->query()->fetchObject();
      if (empty($fetch)) {
        $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`,`is_request`, `handler`, `default`)VALUES ("siteevent_suggestion", "suggestion", \'{item:$subject} has suggested to you a {item:$object:event}.\', "1", "suggestion.widget.get-notify", "1" )
');
      }

      $select = new Zend_Db_Select($db);
      $select->from('engine4_suggestion_module_settings')->where('module = ?', 'siteevent');
      $fetch = $select->query()->fetchObject();
      if (empty($fetch)) {
        $db->query('
INSERT IGNORE INTO `engine4_suggestion_module_settings` (`module`, `item_type`, `field_name`, `owner_field`, `item_title`, `button_title`, `enabled`, `notification_type`, `quality`, `link`, `popup`, `recommendation`, `default`, `settings`) VALUES
("siteevent", "siteevent_event", "event_id", "owner_id", "Events", "View Event", 1, "siteevent_suggestion", 0, 1, 1, 1, 1, \'a:0:{}\');
');
      }

      $select = new Zend_Db_Select($db);
      $select->from('engine4_core_mailtemplates')->where('type = ?', 'notify_siteevent_suggestion');
      $fetch = $select->query()->fetchObject();
      if (empty($fetch)) {
        $db->query('INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`)VALUES 
("notify_siteevent_suggestion", "suggestion", "[suggestion_sender], [suggestion_entity], [email], [link]"
);');
      }
    }
  }
  
  
  // INTEGRATE STORE PLUGIN WITH SUGGESTION
  protected function _integrateSitestorePlugin() {
    $db = $this->getDb();
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitestore')
            ->where('enabled = ?', '1');
    $sitestore_temp = $select->query()->fetchObject();
    if (!empty($sitestore_temp)) {      
      $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name` , `module` , `label` , `plugin` ,`params`, `menu`, `enabled`, `custom`, `order`) VALUES ("sitestore_gutter_suggesttofriend", "suggestion", "Suggest to Friends", \'Sitestore_Plugin_Menus::showSitestore\', \'{"route":"suggest_to_friend_link","class":"buttonlink icon_review_friend_suggestion smoothbox", "type":"popup"}\', "sitestore_gutter", 1, 0, 999 )');
      
      $select = new Zend_Db_Select($db);
      $select->from('engine4_activity_notificationtypes')->where('type = ?', 'sitestore_suggestion');
      $fetch = $select->query()->fetchObject();
      if (empty($fetch)) {
        $db->query('
INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`,`is_request`, `handler`, `default`)VALUES (
"sitestore_suggestion", "suggestion", \'{item:$subject} has suggested to you a {item:$object:store}.\', "1", "suggestion.widget.get-notify", "1" )     
');
      }

      $select = new Zend_Db_Select($db);
      $select->from('engine4_suggestion_module_settings')->where('module = ?', 'sitestore');
      $fetch = $select->query()->fetchObject();
      if (empty($fetch)) {
        $db->query('
INSERT IGNORE INTO `engine4_suggestion_module_settings` (`module`, `item_type`, `field_name`, `owner_field`, `item_title`, `button_title`, `enabled`, `notification_type`, `quality`, `link`, `popup`, `recommendation`, `default`, `settings`) VALUES
("sitestore", "sitestore_store", "store_id", "owner_id", "Store", "View Store", 1, "sitestore_suggestion", 0, 1, 1, 1, 1, \'a:0:{}\');
');
      }

      $select = new Zend_Db_Select($db);
      $select->from('engine4_core_mailtemplates')->where('type = ?', 'notify_sitestore_suggestion');
      $fetch = $select->query()->fetchObject();
      if (empty($fetch)) {
        $db->query('
INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`)VALUES 
("notify_sitestore_suggestion", "suggestion", "[suggestion_sender], [suggestion_entity], [email], [link]"
);    
');
      }
    }
  }
  

  function onDisable() {
    $db = $this->getDb();
    $db->delete('engine4_suggestion_albums');
    $db->delete('engine4_suggestion_photos');
    $db->delete('engine4_suggestion_suggestions');
    $db->delete('engine4_activity_notifications', array('object_type = ?' => 'suggestion'));
    $db->update('engine4_activity_notificationtypes', array(
        'handler' => 'user.friends.request-friend'
            ), array('type =?' => 'friend_request'));
    $db->delete('engine4_user_signup', array('class = ?' => 'Suggestion_Plugin_Signup_Invite'));

    $db->query("UPDATE  `engine4_activity_notificationtypes` SET  `handler` =  'user.friends.request-follow' WHERE  `engine4_activity_notificationtypes`.`type` =  'friend_follow_request' LIMIT 1 ;");
    parent::onDisable();
  }

  function onEnable() {
    $db = $this->getDb();

    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_settings')
            ->where('name = ?', 'suggestion.signup.invite');
    $page_id_temp = $select->query()->fetchObject()->value;

    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_user_signup')
            ->where('class = ?', 'Suggestion_Plugin_Signup_Invite');
    $isSignup_available = $select->query()->fetchObject()->value;

    if (!empty($isSignup_available)) {
      $db->insert('engine4_user_signup', array(
          'class' => 'Suggestion_Plugin_Signup_Invite',
          'order' => 5,
          'enable' => $page_id_temp,
      ));
    }

    $db->query("UPDATE  `engine4_activity_notificationtypes` SET  `handler` =  'suggestion.widget.request-accept' WHERE  `engine4_activity_notificationtypes`.`type` =  'friend_follow_request' LIMIT 1 ;");

    // Check that "People You May Know" plugin should be disable.
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'peopleyoumayknow')
            ->where('enabled =?', 1);
    $page_id_temp = $select->query()->fetchObject();
    if (!empty($page_id_temp)) {
      $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
      echo '<span style="color:red">Note: You already have the "People you may know / Friend Suggestions & Inviter" module/plugin enabled. Please disable the "People you may know / Friend Suggestions & Inviter" module/plugin before enable this plugin.</span> <br/> <a href="' . $base_url . '/manage">Click here</a> to go Manage Packages.';
      die;
    } else {
      // Update "friend_suggestion" notification type.
      $select = new Zend_Db_Select($db);
      $select
              ->from('engine4_activity_notificationtypes')
              ->where('type = ?', 'friend_suggestion');
      $page_id_temp = $select->query()->fetchObject();
      if (!empty($page_id_temp)) {
        $db->update('engine4_activity_notificationtypes', array(
            'module' => 'suggestion',
            'handler' => 'suggestion.widget.get-notify'
                ), array('type =?' => 'friend_suggestion'));
      }

      // Update "friend_request" notification type.
      $select = new Zend_Db_Select($db);
      $select
              ->from('engine4_activity_notificationtypes')
              ->where('type = ?', 'friend_request');
      $page_id_temp = $select->query()->fetchObject();
      if (!empty($page_id_temp)) {
        $db->update('engine4_activity_notificationtypes', array(
            'handler' => 'suggestion.widget.request-accept'
                ), array('type =?' => 'friend_request'));
      }

      // Insert in "engine4_core_mailtemplates" table for Friend Email.
      $select = new Zend_Db_Select($db);
      $select
              ->from('engine4_core_mailtemplates')
              ->where('type = ?', 'notify_suggest_friend');
      $page_id_temp = $select->query()->fetchObject();
      if (empty($page_id_temp)) {
        $db->insert('engine4_core_mailtemplates', array(
            'type' => 'notify_suggest_friend',
            'module' => 'suggestion',
            'vars' => '[suggestion_sender], [suggestion_entity], [email], [link]',
        ));
      } else {
        $db->update('engine4_core_mailtemplates', array('vars' => '[suggestion_sender], [suggestion_entity], [email], [link]', 'module' => 'suggestion'), array('type =?' => 'notify_suggest_friend'));
      }
    }
    parent::onEnable();
  }

  // Hold the all information of the modules.
  function getModules() {
    return array(
        'event' => array('notification_type' => 'event_suggestion', 'notification_body' => '{item:$subject} has suggested to you an {item:$object:event}.', 'mail_type' => array('old_value' => 'notify_suggest_event', 'new_value' => 'notify_event_suggestion'), 'settings' => array('event.sugg.link' => 'link', 'event.sugg.mod' => 'quality', 'after.event.create' => 'popup', 'after.event.join' => 'settings'), 'widget' => array('old_widget' => 'Suggestion.suggestion-event', 'title' => 'Recommended Event', 'field_ajax' => 'event.ajax.enabled', 'field_limit' => 'sugg.event.wid')),
        
        'album' => array('notification_type' => 'album_suggestion', 'notification_body' => '{item:$subject} has suggested to you an {item:$object:album}.', 'mail_type' => array('old_value' => 'notify_suggest_album', 'new_value' => 'notify_album_suggestion'), 'settings' => array('album.sugg.link' => 'link', 'after.album.create' => 'popup', 'album.sugg.mod' => 'quality'), 'widget' => array('old_widget' => 'Suggestion.suggestion-album', 'title' => 'Recommended Album', 'field_ajax' => 'album.ajax.enabled', 'field_limit' => 'sugg.album.wid')),
        
        'poll' => array('notification_type' => 'poll_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:poll}.', 'mail_type' => array('old_value' => 'notify_suggest_poll', 'new_value' => 'notify_poll_suggestion'), 'settings' => array('poll.sugg.link' => 'link', 'after.poll.create' => 'popup', 'poll.sugg.mod' => 'quality'), 'widget' => array('old_widget' => 'Suggestion.suggestion-poll', 'title' => 'Recommended Poll', 'field_ajax' => 'poll.ajax.enabled', 'field_limit' => 'sugg.poll.wid')),
        
        'blog' => array('notification_type' => 'blog_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:blog}.', 'mail_type' => array('old_value' => 'notify_suggest_blog', 'new_value' => 'notify_blog_suggestion'), 'settings' => array('blog.sugg.link' => 'link', 'after.blog.create' => 'popup', 'blog.sugg.mod' => 'quality'), 'widget' => array('old_widget' => 'Suggestion.suggestion-blog', 'title' => 'Recommended Blog', 'field_ajax' => 'blog.ajax.enabled', 'field_limit' => 'sugg.blog.wid')),
        
        'classified' => array('notification_type' => 'classified_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:classified}.', 'mail_type' => array('old_value' => 'notify_suggest_classified', 'new_value' => 'notify_classified_suggestion'), 'settings' => array('classified.sugg.link' => 'link', 'after.classified.create' => 'popup', 'classified.sugg.mod' => 'quality'), 'widget' => array('old_widget' => 'Suggestion.suggestion-classified', 'title' => 'Recommended Classified', 'field_ajax' => 'classified.ajax.enabled', 'field_limit' => 'sugg.classified.wid')),
        
        'forum' => array('notification_type' => 'forum_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:forum}.', 'mail_type' => array('old_value' => 'notify_suggest_forum', 'new_value' => 'notify_forum_suggestion'), 'settings' => array('forum.sugg.link' => 'link', 'forum.sugg.mod' => 'quality', 'after.forum.create' => 'popup', 'after.forum.join' => 'settings'), 'widget' => array('old_widget' => 'Suggestion.suggestion-forum', 'title' => 'Recommended Forum', 'field_ajax' => 'forum.ajax.enabled', 'field_limit' => 'sugg.forum.wid')),
        
        'group' => array('notification_type' => 'group_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:group}.', 'mail_type' => array('old_value' => 'notify_suggest_group', 'new_value' => 'notify_group_suggestion'), 'settings' => array('group.sugg.link' => 'link', 'group.sugg.mod' => 'quality', 'after.group.create' => 'popup', 'after.group.join' => 'settings'), 'widget' => array('old_widget' => 'Suggestion.suggestion-group', 'title' => 'Recommended Group', 'field_ajax' => 'group.ajax.enabled', 'field_limit' => 'sugg.group.wid')),
        
        'music' => array('notification_type' => 'music_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:music}.', 'mail_type' => array('old_value' => 'notify_suggest_music', 'new_value' => 'notify_music_suggestion'), 'settings' => array('music.sugg.link' => 'link', 'after.music.create' => 'popup', 'music.sugg.mod' => 'quality'), 'widget' => array('old_widget' => 'Suggestion.suggestion-music', 'title' => 'Recommended Music', 'field_ajax' => 'music.ajax.enabled', 'field_limit' => 'sugg.music.wid')),
        
        'video' => array('notification_type' => 'video_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:video}.', 'mail_type' => array('old_value' => 'notify_suggest_video', 'new_value' => 'notify_video_suggestion'), 'settings' => array('video.sugg.link' => 'link', 'after.video.create' => 'popup', 'video.sugg.mod' => 'quality'), 'widget' => array('old_widget' => 'Suggestion.suggestion-video', 'title' => 'Recommended Video', 'field_ajax' => 'video.ajax.enabled', 'field_limit' => 'sugg.video.wid')),
        
        'document' => array('notification_type' => 'document_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:document}.', 'mail_type' => array('old_value' => 'notify_suggest_document', 'new_value' => 'notify_document_suggestion'), 'settings' => array('document.sugg.link' => 'link', 'document.sugg.mod' => 'quality'), 'widget' => array('old_widget' => 'Suggestion.suggestion-document', 'title' => 'Recommended Document', 'field_ajax' => 'document.ajax.enabled', 'field_limit' => 'sugg.document.wid')),
        
        'list' => array('notification_type' => 'list_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:list}.', 'mail_type' => array('old_value' => 'notify_suggest_list', 'new_value' => 'notify_listing_suggestion'), 'settings' => array('list.sugg.link' => 'link', 'after.list.create' => 'popup', 'list.sugg.mod' => 'quality'), 'widget' => array('old_widget' => 'Suggestion.suggestion-list', 'title' => 'Recommended List', 'field_ajax' => 'list.ajax.enabled', 'field_limit' => 'sugg.list.wid')),
        
        'recipe' => array('notification_type' => 'recipe_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:recipe}.', 'mail_type' => array('old_value' => 0, 'new_value' => 'notify_recipe_suggestion'), 'settings' => array('recipe.sugg.link' => 'link', 'after.recipe.create' => 'popup', 'recipe.sugg.mod' => 'quality'), 'widget' => array('old_widget' => 'Suggestion.suggestion-recipe', 'title' => 'Recommended Recipe', 'field_ajax' => 'recipe.ajax.enabled', 'field_limit' => 'sugg.recipe.wid')),
        
        'sitepage' => array('notification_type' => 'page_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:page}.', 'mail_type' => array('old_value' => 0, 'new_value' => 'notify_page_suggestion'), 'settings' => array('sitepage.sugg.link' => 'link', 'sitepage.sugg.mod' => 'quality'), 'widget' => array('old_widget' => 'Suggestion.suggestion-sitepage', 'title' => 'Recommended Page', 'field_ajax' => 'sitepage.ajax.enabled', 'field_limit' => 'sugg.sitepage.wid')),
        'sitebusiness' => array('notification_type' => 'business_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:business}.', 'mail_type' => array('old_value' => 0, 'new_value' => 'notify_business_suggestion'), 'settings' => array('sitebusiness.sugg.link' => 'link', 'sitebusiness.sugg.mod' => 'quality'), 'widget' => array('old_widget' => 'Suggestion.suggestion-sitebusiness', 'title' => 'Recommended Business', 'field_ajax' => 'sitebusiness.ajax.enabled', 'field_limit' => 'sugg.sitebusiness.wid')),
        
        'sitegroup' => array('notification_type' => 'group_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:group}.', 'mail_type' => array('old_value' => 0, 'new_value' => 'notify_group_suggestion'), 'settings' => array('sitegroup.sugg.link' => 'link', 'sitegroup.sugg.mod' => 'quality'), 'widget' => array('old_widget' => 'Suggestion.suggestion-sitegroup', 'title' => 'Recommended Group', 'field_ajax' => 'sitegroup.ajax.enabled', 'field_limit' => 'sugg.sitegroup.wid')),
        
        'sitepagedocument' => array('notification_type' => 'page_document_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:page document}.', 'mail_type' => array('old_value' => 0, 'new_value' => 'notify_page_document_suggestion'), 'settings' => array('sitepage.document.sugg.link' => 'settings'), 'widget' => array()),
        
        'sitepagepoll' => array('notification_type' => 'page_poll_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:page poll}.', 'mail_type' => array('old_value' => 0, 'new_value' => 'notify_page_poll_suggestion'), 'settings' => array('sitepage.poll.sugg.link' => 'settings'), 'widget' => array()),
        
        'sitepagemusic' => array('notification_type' => 'page_music_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:page music}.', 'mail_type' => array('old_value' => 0, 'new_value' => 'notify_page_music_suggestion'), 'settings' => array('sitepage.music.sugg.link' => 'settings'), 'widget' => array()),
        
        'sitepageoffer' => array('notification_type' => 'page_offer_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:page offer}.', 'mail_type' => array('old_value' => 0, 'new_value' => 'notify_page_offer_suggestion'), 'settings' => array('sitepage.offer.sugg.link' => 'settings'), 'widget' => array()),
        
        'sitepagevideo' => array('notification_type' => 'page_video_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:page video}.', 'mail_type' => array('old_value' => 0, 'new_value' => 'notify_page_video_suggestion'), 'settings' => array('sitepage.video.sugg.link' => 'settings'), 'widget' => array()),
        
        'sitepageevent' => array('notification_type' => 'page_event_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:page event}.', 'mail_type' => array('old_value' => 0, 'new_value' => 'notify_page_event_suggestion'), 'settings' => array('sitepage.event.sugg.link' => 'settings'), 'widget' => array()),
        
        'sitepagereview' => array('notification_type' => 'page_review_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:page review}.', 'mail_type' => array('old_value' => 0, 'new_value' => 'notify_page_review_suggestion'), 'settings' => array('sitepage.review.sugg.link' => 'settings'), 'widget' => array()),
        
        'sitepagealbum' => array('notification_type' => 'page_album_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:page album}.', 'mail_type' => array('old_value' => 0, 'new_value' => 'notify_page_album_suggestion'), 'settings' => array('sitepage.albumsugg.link' => 'settings'), 'widget' => array()),
        
        'sitepagenote' => array('notification_type' => 'page_note_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:page note}.', 'mail_type' => array('old_value' => 0, 'new_value' => 'notify_page_note_suggestion'), 'settings' => array('sitepage.note.sugg.link' => 'settings'), 'widget' => array()),
        
        'sitebusinessdocument' => array('notification_type' => 'business_document_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:business document}.', 'mail_type' => array('old_value' => 0, 'new_value' => 'notify_business_document_suggestion'), 'settings' => array('sitebusiness.document.sugg.link' => 'settings'), 'widget' => array()),
        
        'sitebusinesspoll' => array('notification_type' => 'business_poll_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:business poll}.', 'mail_type' => array('old_value' => 0, 'new_value' => 'notify_business_poll_suggestion'), 'settings' => array('sitebusiness.poll.sugg.link' => 'settings'), 'widget' => array()),
        
        'sitebusinessmusic' => array('notification_type' => 'business_music_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:business music}.', 'mail_type' => array('old_value' => 0, 'new_value' => 'notify_business_music_suggestion'), 'settings' => array('sitebusiness.music.sugg.link' => 'settings'), 'widget' => array()),
        
        'sitebusinessoffer' => array('notification_type' => 'business_offer_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:business offer}.', 'mail_type' => array('old_value' => 0, 'new_value' => 'notify_business_offer_suggestion'), 'settings' => array('sitebusiness.offer.sugg.link' => 'settings'), 'widget' => array()),
        
        'sitebusinessvideo' => array('notification_type' => 'business_video_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:business video}.', 'mail_type' => array('old_value' => 0, 'new_value' => 'notify_business_video_suggestion'), 'settings' => array('sitebusiness.video.sugg.link' => 'settings'), 'widget' => array()),
        
        'sitebusinessevent' => array('notification_type' => 'business_event_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:business event}.', 'mail_type' => array('old_value' => 0, 'new_value' => 'notify_business_event_suggestion'), 'settings' => array('sitebusiness.event.sugg.link' => 'settings'), 'widget' => array()),
        
        'sitebusinessreview' => array('notification_type' => 'business_review_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:business review}.', 'mail_type' => array('old_value' => 0, 'new_value' => 'notify_business_review_suggestion'), 'settings' => array('sitebusiness.review.sugg.link' => 'settings'), 'widget' => array()),
        
        'sitebusinessalbum' => array('notification_type' => 'business_album_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:business album}.', 'mail_type' => array('old_value' => 0, 'new_value' => 'notify_business_album_suggestion'), 'settings' => array('sitebusiness.albumsugg.link' => 'settings'), 'widget' => array()),
        
        'sitebusinessnote' => array('notification_type' => 'business_note_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:business note}.', 'mail_type' => array('old_value' => 0, 'new_value' => 'notify_business_note_suggestion'), 'settings' => array('sitebusiness.note.sugg.link' => 'settings'), 'widget' => array()),
        
        'sitegroupdocument' => array('notification_type' => 'group_document_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:group document}.', 'mail_type' => array('old_value' => 0, 'new_value' => 'notify_group_document_suggestion'), 'settings' => array('sitegroup.document.sugg.link' => 'settings'), 'widget' => array()),
        
        'sitegrouppoll' => array('notification_type' => 'group_poll_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:group poll}.', 'mail_type' => array('old_value' => 0, 'new_value' => 'notify_group_poll_suggestion'), 'settings' => array('sitegroup.poll.sugg.link' => 'settings'), 'widget' => array()),
        
        'sitegroupmusic' => array('notification_type' => 'group_music_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:group music}.', 'mail_type' => array('old_value' => 0, 'new_value' => 'notify_group_music_suggestion'), 'settings' => array('sitegroup.music.sugg.link' => 'settings'), 'widget' => array()),
        
        'sitegroupoffer' => array('notification_type' => 'group_offer_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:group offer}.', 'mail_type' => array('old_value' => 0, 'new_value' => 'notify_group_offer_suggestion'), 'settings' => array('sitegroup.offer.sugg.link' => 'settings'), 'widget' => array()),
        
        'sitegroupvideo' => array('notification_type' => 'group_video_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:group video}.', 'mail_type' => array('old_value' => 0, 'new_value' => 'notify_group_video_suggestion'), 'settings' => array('sitegroup.video.sugg.link' => 'settings'), 'widget' => array()),
        
        'sitegroupevent' => array('notification_type' => 'group_event_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:group event}.', 'mail_type' => array('old_value' => 0, 'new_value' => 'notify_group_event_suggestion'), 'settings' => array('sitegroup.event.sugg.link' => 'settings'), 'widget' => array()),
        
        'sitegroupreview' => array('notification_type' => 'group_review_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:group review}.', 'mail_type' => array('old_value' => 0, 'new_value' => 'notify_group_review_suggestion'), 'settings' => array('sitegroup.review.sugg.link' => 'settings'), 'widget' => array()),
        
        'sitegroupalbum' => array('notification_type' => 'group_album_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:group album}.', 'mail_type' => array('old_value' => 0, 'new_value' => 'notify_group_album_suggestion'), 'settings' => array('sitegroup.albumsugg.link' => 'settings'), 'widget' => array()),
        
        'sitegroupnote' => array('notification_type' => 'group_note_suggestion', 'notification_body' => '{item:$subject} has suggested to you a {item:$object:group note}.', 'mail_type' => array('old_value' => 0, 'new_value' => 'notify_group_note_suggestion'), 'settings' => array('sitegroup.note.sugg.link' => 'settings'), 'widget' => array())
    );
  }

  // It is the main function, which perform all action in installation process.
  function setModValue() {
    $db = $this->getDb();
    // Get the Module Name.
    $default_module = $this->getModules();
    foreach ($default_module as $mod_name => $mod_value) {
      // Replace the old widgets to new widgets.
      $this->setWidgets($mod_name, $mod_value['widget']);

      $select = new Zend_Db_Select($db);
      $select
              ->from('engine4_core_modules')
              ->where('name = ?', $mod_name);
      $is_enabled = $select->query()->fetch();
      if (!empty($is_enabled)) {

        //Step 1:  UPDATE THE "ACTIVITY_NOTIFICATIONTYPE" TABLE FOR RESPECTIVE MODULES.
        $this->setNotificationType(array(
            'type' => $mod_value['notification_type'],
            'module' => 'suggestion',
            'body' => $mod_value['notification_body'],
            'is_request' => 1,
            'handler' => 'suggestion.widget.get-notify',
            'default' => 1
                )
        );

        // Step 2: Delete & Install in `engine4_core_mailtemplates`
        $this->setMailTemplate($mod_value['mail_type']);

        // Set the settings in `engine4_suggestion_module_settings` table accordingly.
        // Step 3: Delete and set settings in new table. `engine4_core_settings`
        $this->setSettings($mod_name, $mod_value['settings']);
      } else {
        $this->deleteModValue($mod_name, $mod_value);
      }
    }
  }

  // Step 3: Delete and set settings in new table. `engine4_core_settings`
  function setSettings($mod_name, $settings) {
    $db = $this->getDb();

    $Query = "SELECT `engine4_suggestion_module_settings`.`settings` FROM  `engine4_suggestion_module_settings` WHERE  `module` LIKE  '$mod_name' LIMIT 1";

    $value = $db->query($Query)->fetch();
    $checkValue = unserialize($value['settings']);

    $notificationStatus = 1;
    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_suggestion_notifications'")->fetch();
    if (!empty($table_exist)) {
      $isEnabled = "SELECT `engine4_suggestion_notifications`.`status` FROM  `engine4_suggestion_notifications` WHERE  `type` LIKE  '$mod_name' LIMIT 1";

      $value = $db->query($isEnabled)->fetch();
      if (!empty($value)) {
        $notificationStatus = $value['status'];
      }
    }

    if (!empty($checkValue['default'])) {
      return;
    }

    foreach ($settings as $old_value => $field_name) {

      switch ($field_name) {
        case 'link':
          $default_value = 1;
          break;

        case 'popup':
          $default_value = 1;
          break;

        case 'settings':
          $default_value = '';
          break;
      }

      // Search old value exist or not.
      $Query = "SELECT * FROM  `engine4_core_settings` WHERE  `name` LIKE  '$old_value' LIMIT 1";
      $old_value_exist = $db->query($Query)->fetch();
      if (!empty($old_value_exist)) {
        $default_value = $old_value_exist['value'];
      }

      if (!strstr($field_name, 'settings')) {
        // Update value in new table.
        $set_settings = serialize(array('default' => 1));
        $db->query("UPDATE `engine4_suggestion_module_settings` SET `$field_name` = $default_value, `settings` = '" . $set_settings . "', `enabled` = " . $notificationStatus . "  WHERE `engine4_suggestion_module_settings`.`module` LIKE '$mod_name' LIMIT 1 ;");
      } else {
        //if( !empty($default_value) ) {
        $value_array = $checkValue;
        $value_array['default'] = 1;
        $changeValue = $this->getChangeValue($old_value);
        $value_array[$changeValue] = $default_value;
        $serialized_value_array = serialize($value_array);
        $db->query("UPDATE `engine4_suggestion_module_settings` SET `settings` = '" . $serialized_value_array . "', `enabled` = " . $notificationStatus . " WHERE `engine4_suggestion_module_settings`.`module` LIKE '$mod_name' LIMIT 1 ;");
        //}
      }
      // Delete From old Table.
      $db->query("DELETE FROM `engine4_core_settings` WHERE `engine4_core_settings`.`name` = '$old_value' LIMIT 1");
    }
  }

  function getChangeValue($value) {
    switch ($value) {
      case 'sitepage.poll.sugg.link': $value = 'poll_sugg_link';
        break;
      case 'sitepage.video.sugg.link': $value = 'video_sugg_link';
        break;
      case 'sitepage.event.sugg.link': $value = 'event_sugg_link';
        break;
      case 'sitepage.review.sugg.link': $value = 'review_sugg_link';
        break;
      case 'sitepage.albumsugg.link': $value = 'album_sugg_link';
        break;
      case 'sitepage.note.sugg.link': $value = 'note_sugg_link';
        break;
      case 'sitepage.offer.sugg.link': $value = 'offer_sugg_link';
        break;
      case 'sitepage.music.sugg.link': $value = 'music_sugg_link';
        break;
      case 'sitepage.document.sugg.link': $value = 'document_sugg_link';
        break;

      case 'sitebusiness.document.sugg.link': $value = 'document_sugg_link';
        break;
      case 'sitebusiness.music.sugg.link': $value = 'music_sugg_link';
        break;
      case 'sitebusiness.offer.sugg.link': $value = 'offer_sugg_link';
        break;
      case 'sitebusiness.video.sugg.link': $value = 'video_sugg_link';
        break;
      case 'sitebusiness.event.sugg.link': $value = 'event_sugg_link';
        break;
      case 'sitebusiness.review.sugg.link': $value = 'review_sugg_link';
        break;
      case 'sitebusiness.albumsugg.link': $value = 'album_sugg_link';
        break;
      case 'sitebusiness.note.sugg.link': $value = 'note_sugg_link';
        break;
      case 'after.event.join': $value = 'after_event_join';
        break;
      case 'after.forum.join': $value = 'after_forum_join';
        break;
      case 'after.group.join': $value = 'after_group_join';
        break;
      
      case 'sitegroup.poll.sugg.link': $value = 'poll_sugg_link';
        break;
      case 'sitegroup.document.sugg.link': $value = 'document_sugg_link';
        break;
      case 'sitegroup.music.sugg.link': $value = 'music_sugg_link';
        break;
      case 'sitegroup.offer.sugg.link': $value = 'offer_sugg_link';
        break;
      case 'sitegroup.video.sugg.link': $value = 'video_sugg_link';
        break;
      case 'sitegroup.event.sugg.link': $value = 'event_sugg_link';
        break;
      case 'sitegroup.review.sugg.link': $value = 'review_sugg_link';
        break;
      case 'sitegroup.albumsugg.link': $value = 'album_sugg_link';
        break;
      case 'sitegroup.note.sugg.link': $value = 'note_sugg_link';
        break;
    }
    return $value;
  }

  // Step 2: Delete & Install in `engine4_core_mailtemplates`
  function setMailTemplate($mailTemp) {
    $db = $this->getDb();
    // Insert in the `engine4_core_mailtemplates` table.
    if (!empty($mailTemp)) {

      // Delete old value if exist in database.
      if (!empty($mailTemp['old_value'])) {
        $old_SQL = 'DELETE FROM `engine4_core_mailtemplates` WHERE `engine4_core_mailtemplates`.`type` LIKE "' . $mailTemp['old_value'] . '" LIMIT 1';
        $db->query($old_SQL);
      }

      $mail_SQL = 'INSERT IGNORE INTO  `engine4_core_mailtemplates` ( `type` , `module` , `vars` ) VALUES ("' . $mailTemp['new_value'] . '",  "suggestion",  "[suggestion_sender], [suggestion_entity], [email], [link]")';
      $db->query($mail_SQL);
    }
  }

  // Step 1: Upgrade the `engine4_activity_notificationtypes`
  function setNotificationType($notificationType) {
    $db = $this->getDb();

    $type = $notificationType['type'];
    $module = $notificationType['module'];
    $body = $notificationType['body'];
    $is_request = $notificationType['is_request'];
    $handler = $notificationType['handler'];
    $default = $notificationType['default'];

    $isAvailable = $db->query("SELECT * FROM  `engine4_activity_notificationtypes` WHERE  `type` LIKE  '$type' LIMIT 1")->fetchAll();
    if (empty($isAvailable)) {
      $db->query("INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
  ('$type', '$module', '$body', $is_request, '$handler', $default)");
    } else {
      $db->query("UPDATE `engine4_activity_notificationtypes` SET `handler` = '" . $handler . "' WHERE `engine4_activity_notificationtypes`.`type` = '$type' LIMIT 1;");
    }
    return;
  }

  // (1) If plugin not installed on the site (2) Admin has Disabled the suggestion
  function deleteModValue($mod_name, $mod_value) {
    $db = $this->getDb();
    $SQL = "SELECT `engine4_suggestion_module_settings`.`settings` FROM  `engine4_suggestion_module_settings` WHERE  `module` LIKE  '$mod_name' LIMIT 1";
    $is_Exist = $db->query($SQL)->fetch();
    if (!empty($is_Exist)) {
      // 1. Delete from `engine4_activity_notificationtype` table.
      $Query = 'DELETE FROM `engine4_activity_notifications` WHERE `engine4_activity_notifications`.`type` = "' . $mod_value['notification_type'] . '" LIMIT 1';
      $db->query($Query);

      $Query = 'DELETE FROM `engine4_activity_notificationtypes` WHERE `engine4_activity_notificationtypes`.`type` = "' . $mod_value['notification_type'] . '" LIMIT 1';
      $db->query($Query);

      // 2. Delete from `engine4_core_mailtemplates` table
      $Query = 'DELETE FROM `engine4_core_mailtemplates` WHERE `engine4_core_mailtemplates`.`type` LIKE "' . $mod_value['mail_type']['old_value'] . '" LIMIT 1';
      $db->query($Query);

      $Query = 'DELETE FROM `engine4_core_mailtemplates` WHERE `engine4_core_mailtemplates`.`type` LIKE "' . $mod_value['mail_type']['new_value'] . '" LIMIT 1';
      $db->query($Query);

      // 3. Update - delete from `engine4_suggestion_module_settings` table
      $SQL = 'UPDATE  `engine4_suggestion_module_settings` SET  `enabled` =  "0", `link` =  "0", `popup` =  "0", `settings` = "a:0:{}" WHERE  `engine4_suggestion_module_settings`.`module` LIKE ' . $mod_name . ' LIMIT 1 ;';
    }
  }

  function setWidgets($mod_name, $widget_settings) {
    $db = $this->getDb();
    $select = new Zend_Db_Select($db);

    if (!empty($mod_name) && !empty($widget_settings)) {
      $getWidgets = $db->query("SELECT * FROM  `engine4_core_content` WHERE  `name` LIKE  '" . $widget_settings['old_widget'] . "'")->fetchAll();

      if (!empty($getWidgets)) {

        $ajaxQuery = "SELECT * FROM  `engine4_core_settings` WHERE  `name` LIKE  '" . $widget_settings["field_ajax"] . "' LIMIT 1";
        $ajaxValue = $db->query($ajaxQuery)->fetch();
        $ajaxValue = empty($ajaxValue) ? 0 : $ajaxValue['value'];

        $limitQuery = "SELECT * FROM  `engine4_core_settings` WHERE  `name` LIKE  '" . $widget_settings["field_limit"] . "' LIMIT 1";
        $limitValue = $db->query($limitQuery)->fetch();
        $limitValue = empty($limitValue) ? 0 : $limitValue['value'];

        $titleQuery = "SELECT * FROM  `engine4_core_content` WHERE  `name` LIKE  '" . $widget_settings["old_widget"] . "' LIMIT 1";
        $titleValues = $db->query($titleQuery)->fetch();
        $titleValues = empty($titleValues) ? 0 : Zend_Json::decode($titleValues['params']);

        $paramTitle = !empty($titleValues['title']) ? $titleValues['title'] : $widget_settings['title'];

        $Parems = '{"title":"' . $paramTitle . '","resource_type":"' . $mod_name . '","getWidAjaxEnabled":"' . $ajaxValue . '","getWidLimit":"' . $limitValue . '","nomobile":"0","name":"suggestion.common-suggestion"}';
        // May be widgets placed more then 1 time on the pages then we will call this function.
        foreach ($getWidgets as $widgets) {
          $db->query('UPDATE  `engine4_core_content` SET  `name` =  "suggestion.common-suggestion",
	`params` =  \'' . $Parems . '\' WHERE  `engine4_core_content`.`name` =\'' . $widget_settings['old_widget'] . '\';');
        }

        // Delete the content from the table.
        $db->query("DELETE FROM `engine4_core_settings` WHERE `engine4_core_settings`.`name` = '" . $widget_settings["field_ajax"] . "' LIMIT 1");
        $db->query("DELETE FROM `engine4_core_settings` WHERE `engine4_core_settings`.`name` = '" . $widget_settings["field_limit"] . "' LIMIT 1");
      }
    }
  }
  
  //Sitemobile code, to call my.sql
  public function onPostInstall() {
    $moduleName='suggestion';
    $db = $this->getDb();
		$select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitemobile')
            ->where('enabled = ?', 1);
    $is_sitemobile_object = $select->query()->fetchObject();
    if(!empty($is_sitemobile_object)) {
			$db->query("INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`) VALUES
('$moduleName','1')");
			$select = new Zend_Db_Select($db);
			$select
							->from('engine4_sitemobile_modules')
							->where('name = ?', $moduleName)
							->where('integrated = ?', 0);
			$is_sitemobile_object = $select->query()->fetchObject();
      if($is_sitemobile_object)  {
				$actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
				$controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
				if($controllerName == 'manage' && $actionName == 'install') {
          $view = new Zend_View();
					$baseUrl = ( !empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://':'http://') .  $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
					$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
					$redirector->gotoUrl($baseUrl . 'admin/sitemobile/module/enable-mobile/enable_mobile/1/name/'.$moduleName.'/integrated/0/redirect/install');
				} 
      }
    }
  }

  private function getVersion() {
  
    $db = $this->getDb();

    $errorMsg = '';
    $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

    $modArray = array(
      'sitemobile' => '4.6.0p4',
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

}

?>
