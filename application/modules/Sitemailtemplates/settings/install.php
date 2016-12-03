<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemailtemplates
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 6590 2012-06-20 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemailtemplates_Installer extends Engine_Package_Installer_Module {

  function onPreinstall() {
    $db = $this->getDb();
    $PRODUCT_TYPE = 'sitemailtemplates';
    $PLUGIN_TITLE = 'Sitemailtemplates';
    $PLUGIN_VERSION = '4.8.10p1';
    $PLUGIN_CATEGORY = 'plugin';
    $PRODUCT_DESCRIPTION = 'Email Templates Plugin';
    $_PRODUCT_FINAL_FILE = 0;
    $SocialEngineAddOns_version = '4.8.7p18';
    $PRODUCT_TITLE = 'Email Templates Plugin';
    $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
    $is_file = file_exists($file_path);
    if (empty($is_file)) {
      include_once APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/license3.php";
    } else {
      $select = new Zend_Db_Select($db);
      $select->from('engine4_core_modules')->where('name = ?', $PRODUCT_TYPE);
      $is_Mod = $select->query()->fetchObject();
      if (empty($is_Mod)) {
        include_once $file_path;
      }
    }
    parent::onPreinstall();
  }

  function onInstall() {

    //GET DB
    $db = $this->getDb();

		//CHECK THAT SITEPAGE PLUGIN IS ACTIVATED OR NOT
		$select = new Zend_Db_Select($db);
		$select
							->from('engine4_core_modules')
							->where('name = ?', 'sitepage')
							->where('enabled = ?', 1);
		$check_sitepage = $select->query()->fetchAll();

		//CHECK THAT SITEBUSINESS PLUGIN IS ACTIVATED OR NOT
		$select = new Zend_Db_Select($db);
		$select
							->from('engine4_core_modules')
							->where('name = ?', 'sitebusiness')
							->where('enabled = ?', 1);
		$check_sitebusiness = $select->query()->fetchAll();

		//CHECK THAT BIRTHDAYEMAIL PLUGIN IS ACTIVATED OR NOT
		$select = new Zend_Db_Select($db);
		$select
							->from('engine4_core_modules')
							->where('name = ?', 'birthdayemail')
							->where('enabled = ?', 1);
		$check_birthdaymail = $select->query()->fetchAll();
		
		if($check_sitepage) {
			$select = new Zend_Db_Select($db);
			$select
								->from('engine4_core_settings','value')
								->where('name = ?', 'sitepage.bg.color')
								->limit(1);
			$bgColor = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
			if(!empty($bgColor)) {
				$db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
				('sitemailtemplates.bg.color','".$bgColor['0']. "');");
			}
			$select = new Zend_Db_Select($db);
			$select
								->from('engine4_core_settings','value')
								->where('name = ?', 'sitepage.header.color')
								->limit(1);
			$headerColor = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
			if(!empty($headerColor)) {
				$db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
				('sitemailtemplates.header.color','".$headerColor['0']. "');");
			}
			$select = new Zend_Db_Select($db);
			$select
								->from('engine4_core_settings','value')
								->where('name = ?', 'sitepage.title.color')
								->limit(1);
			$headerTextColor = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
			if(!empty($headerTextColor)) {
				$db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
				('sitemailtemplates.title.color','".$headerTextColor['0']. "');");
			}
		}
		if($check_sitebusiness) {
			$select = new Zend_Db_Select($db);
			$select
								->from('engine4_core_settings','value')
								->where('name = ?', 'sitebusiness.bg.color')
								->limit(1);
			$bgColor = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
			if(!empty($bgColor)) {
				$db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
				('sitemailtemplates.bg.color','".$bgColor['0']. "');");
			}
			$select = new Zend_Db_Select($db);
			$select
								->from('engine4_core_settings','value')
								->where('name = ?', 'sitebusiness.header.color')
								->limit(1);
			$headerColor = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
			if(!empty($headerColor)) {
				$db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
				('sitemailtemplates.header.color','".$headerColor['0']. "');");
			}
			$select = new Zend_Db_Select($db);
			$select
								->from('engine4_core_settings','value')
								->where('name = ?', 'sitebusiness.title.color')
								->limit(1);
			$headerTextColor = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
			if(!empty($headerTextColor)) {
				$db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
				('sitemailtemplates.title.color','".$headerTextColor['0']. "');");
			}
		}
		if($check_birthdaymail) {    
			$select = new Zend_Db_Select($db);
			$select
								->from('engine4_core_settings','value')
								->where('name = ?', 'birthdayemail.color')
								->limit(1);
			$bgColor = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
			if(!empty($bgColor)) {
				$db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
				('sitemailtemplates.bg.color','".$bgColor['0']. "');");
			}
			$select = new Zend_Db_Select($db);
			$select
								->from('engine4_core_settings','value')
								->where('name = ?', 'birthdayemail.title.color')
								->limit(1);
			$headerTitleColor = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
			if(!empty($headerTitleColor)) {
				$db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
				('sitemailtemplates.title.color','".$headerTitleColor['0']. "');");
			}
		}

    $db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` ='sitemailtemplates';");

    $select = new Zend_Db_Select($db);
    $select
					->from('engine4_core_modules')
					->where('name = ?', 'sitemailtemplates')
					->where('version = ?', '4.2.4');
    $sitemailtemplates_newversion = $select->query()->fetchObject();

    if($sitemailtemplates_newversion) {

			$db->query("DROP TABLE IF EXISTS `engine4_sitemailtemplates_templates`;");
			$db->query("CREATE TABLE `engine4_sitemailtemplates_templates` (
								`template_id` int(11) NOT NULL auto_increment,
								`testemail_admin` varchar(255) NOT NULL,
								`template_title` varchar(128) NOT NULL,
								`show_title` tinyint(1) NOT NULL default '1',
								`site_title` varchar(128) NOT NULL,
								`sitetitle_fontsize` tinyint(2) NOT NULL ,
								`sitetitle_fontfamily` varchar(32) NOT NULL,
								`sitetitle_location` varchar(16) NOT NULL,
								`sitetitle_position` varchar(16) NOT NULL,
								`show_icon` tinyint(1) NOT NULL,
								`img_path` varchar(255) NOT NULL,
								`sitelogo_location` varchar(16) NOT NULL,
								`sitelogo_position` varchar(16) NOT NULL,
								`show_tagline` tinyint(1) NOT NULL ,
								`tagline_title` varchar(128) NOT NULL,
								`tagline_fontsize` tinyint(2) NOT NULL ,
								`tagline_fontfamily` varchar(32) NOT NULL,
								`tagline_location` varchar(16) NOT NULL,
								`tagline_position` varchar(16) NOT NULL,
								`header_bgcol` varchar(16) NOT NULL,
								`header_outpadding` tinyint(2) NOT NULL ,
								`header_titlecolor` varchar(16) NOT NULL,
								`header_tagcolor` varchar(16) NOT NULL,
								`header_bottomcolor` varchar(16) NOT NULL,
								`header_bottomwidth` tinyint(2) NOT NULL ,
								`footer_bottomcol` varchar(16) NOT NULL,
								`footer_bottomwidth` tinyint(2) NOT NULL ,
								`lr_bordercolor` varchar(16) NOT NULL,
								`body_outerbgcol` varchar(16) NOT NULL,
								`body_innerbgcol` varchar(16) NOT NULL,
								`signature_bgcol` varchar(16) NOT NULL,
								`active_delete` tinyint(1) NOT NULL,
								`lr_bottomwidth` tinyint(2) NOT NULL ,
								`active_template` tinyint(2) NOT NULL ,
								PRIMARY KEY (`template_id`)
							) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;");

      $bg_color = $db->select()
				->from('engine4_core_settings', array('value'))
				->where('name = ?', 'sitemailtemplates.bg.color')
				->limit(1)
				->query()
				->fetchColumn();
      if(!empty($bg_color)) {
        $db->query("UPDATE  `engine4_sitemailtemplates_templates` SET  `lr_bordercolor` =  '".$bg_color."' WHERE  `engine4_sitemailtemplates_templates`.`template_id` ='1';");
        $db->query("DELETE FROM `engine4_core_settings` WHERE `engine4_core_settings`.`name` = 'sitemailtemplates.bg.color' LIMIT 1");
      }
    
      $header_bgcolor = $db->select()
				->from('engine4_core_settings', array('value'))
				->where('name = ?', 'sitemailtemplates.header.color')
				->limit(1)
				->query()
				->fetchColumn();
      if(!empty($header_bgcolor)) {
        $db->query("UPDATE  `engine4_sitemailtemplates_templates` SET  `header_bgcol` =  '".$header_bgcolor."' WHERE  `engine4_sitemailtemplates_templates`.`template_id` ='1';");
        $db->query("DELETE FROM `engine4_core_settings` WHERE `engine4_core_settings`.`name` = 'sitemailtemplates.header.color' LIMIT 1");
      }
 
      $title_color = $db->select()
				->from('engine4_core_settings', array('value'))
				->where('name = ?', 'sitemailtemplates.title.color')
				->limit(1)
				->query()
				->fetchColumn();
      if(!empty($title_color)) {
        $db->query("UPDATE  `engine4_sitemailtemplates_templates` SET  `header_titlecolor` =  '".$title_color."' WHERE  `engine4_sitemailtemplates_templates`.`template_id` ='1';");
        $db->query("DELETE FROM `engine4_core_settings` WHERE `engine4_core_settings`.`name` = 'sitemailtemplates.title.color' LIMIT 1");
      }
    }

		$emailSignatureColumn = $db->query("SHOW COLUMNS FROM engine4_core_mailtemplates LIKE 'email_signature'")->fetch();
		if(!empty($emailSignatureColumn)) {
			$db->query("ALTER TABLE `engine4_core_mailtemplates` CHANGE `email_signature` `email_signature_en` LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
		}
    
    $fancytemplateColumn = $db->query("SHOW COLUMNS FROM engine4_core_mailtemplates LIKE 'fancytemplate'")->fetch();
		if(!empty($fancytemplateColumn)) {
			$db->query("ALTER TABLE `engine4_core_mailtemplates` CHANGE `fancytemplate` `enable_template` INT( 2 ) NOT NULL DEFAULT '1';");
		}

    $templateidColumn = $db->query("SHOW COLUMNS FROM engine4_core_mailtemplates LIKE 'template_id'")->fetch();
		if(empty($templateidColumn)) {
			$db->query("ALTER TABLE `engine4_core_mailtemplates` ADD `template_id` INT( 5 ) NOT NULL DEFAULT '0' AFTER `vars`");
		}

    $enable_templateColumn = $db->query("SHOW COLUMNS FROM engine4_core_mailtemplates LIKE 'enable_template'")->fetch();
		if(empty($enable_templateColumn)) {
			$db->query("ALTER TABLE `engine4_core_mailtemplates` ADD `enable_template` INT( 2 ) NOT NULL DEFAULT '1' AFTER `template_id`");
		}

    $emailSignatureColumn = $db->query("SHOW COLUMNS FROM engine4_core_mailtemplates LIKE 'email_signature_en'")->fetch();
			if(empty($emailSignatureColumn)) {
				$db->query("ALTER TABLE `engine4_core_mailtemplates` ADD `email_signature_en` LONGTEXT NOT NULL AFTER `enable_template`;");
			}

		$showSignatureColumn = $db->query("SHOW COLUMNS FROM engine4_core_mailtemplates LIKE 'show_signature'")->fetch();
		if(empty($showSignatureColumn)) {
			$db->query("ALTER TABLE `engine4_core_mailtemplates` ADD `show_signature` TINYINT( 2 ) NOT NULL DEFAULT '0' AFTER `email_signature_en`;");
		}

		$select = new Zend_Db_Select($db);
		$select
					->from('engine4_core_modules')
					->where('name = ?', 'sitemailtemplates')
					->where('version < ?', '4.2.6');
		$sitemailtemplates_newversion = $select->query()->fetchObject();

		if(!empty($sitemailtemplates_newversion)) {
			$select = new Zend_Db_Select($db);
			$select
							->from('engine4_core_settings')
							->where('name = ?', 'sitemailtemplates.footer1');
			$info = $select->query()->fetch();
			if (!empty($info)) {
				$value = $info['value'];
				$db->update('engine4_core_mailtemplates', array('email_signature_en' => $value));
			}
		}
    parent::onInstall();
   
  }

}