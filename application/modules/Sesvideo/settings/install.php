<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: install.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_Installer extends Engine_Package_Installer_Module {

  public function onPreinstall() {

    $db = $this->getDb();
    
    $sesbasic_currentversion = '4.8.9p10';
    $sesbasiccheckcurrentversion = '48910';
    
    $select = new Zend_Db_Select($db);
    $select->from('engine4_core_modules')
            ->where('name = ?', 'sesbasic');
    $sesbasic_enabled = $select->query()->fetchObject();
    
    $sesbasic_siteversion = @explode('.', $sesbasic_enabled->version);
    if(strstr($sesbasic_siteversion[2], "p")) {
	    $sesbasic_site_last = str_replace('p','', $sesbasic_siteversion[2]);
    } else {
	    $sesbasic_site_last = $sesbasic_siteversion[2];
    }
    $sesbasic_finalsiteversion = $sesbasic_siteversion[0] . $sesbasic_siteversion[1] . $sesbasic_site_last ;

    if (empty($sesbasic_enabled)) {
      return $this->_error('<div class="global_form"><div><div><p style="color:red;">The required SocialEngineSolutions Basic Required Plugin is not installed on your website. Please download the latest version of this FREE plugin from <a href="http://www.socialenginesolutions.com" target="_blank">SocialEngineSolutions.com</a> website.</p></div></div></div>');
    } else {
      if (isset($sesbasic_enabled->enabled) && !empty($sesbasic_enabled->enabled)) {
        if ($sesbasic_finalsiteversion >= $sesbasiccheckcurrentversion) {
        } else {
          return $this->_error('<div class="global_form"><div><div><p style="color:red;">The latest version of the SocialEngineSolutions Basic Required Plugin installed on your website is less than the minimum required version: ' . $sesbasic_currentversion . '. Please upgrade this Free plugin to its latest version after downloading the latest version of this plugin from <a href="http://www.socialenginesolutions.com" target="_blank">SocialEngineSolutions.com</a> website.</p></div></div></div>');
        }
      } else {
        return $this->_error('<div class="global_form"><div><div><p style="color:red;">The SocialEngineSolutions Basic Required Plugin is installed but not enabled on your website. So, please first enable it from the "Manage" >> "Packages & Plugins" section.</p></div></div></div>');
      }
    }

    parent::onPreinstall();
  }

  public function onInstall() {
    $db = $this->getDb();
    
    $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("mobi_browse_sesvideo", "sesvideo", "Videos","", \'{"route":"sesvideo_general"}\', "mobi_browse", NULL, "1", "0", "8");');

		$table_exist_ratings = $db->query('SHOW TABLES LIKE \'engine4_sesvideo_ratings\'')->fetch();
		if (empty($table_exist_ratings)) {
			$db->query('DROP TABLE IF EXISTS `engine4_sesvideo_ratings`;');
			$db->query('CREATE TABLE IF NOT EXISTS `engine4_sesvideo_ratings` (
				`rating_id`  int(11) unsigned NOT NULL auto_increment,
				`resource_id` int(11) NOT NULL,
				`resource_type` varchar(128) NOT NULL,
				`user_id` int(9) unsigned NOT NULL,
				`rating` tinyint(1) unsigned DEFAULT NULL, 
				`creation_date` DATETIME NOT NULL ,
				`video_id` int(11) NOT NULL,
				PRIMARY KEY  (`rating_id`),
				UNIQUE KEY `uniqueKey` (`user_id`,`resource_type`,`resource_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');
			
			//SE video plugin check
			$select = new Zend_Db_Select($db);
			$select->from('engine4_core_modules')
						->where('name = ?', 'video');
			$sevideo_enabled = $select->query()->fetchObject();
			if (!empty($sevideo_enabled)) {
				$db->query('INSERT IGNORE INTO engine4_sesvideo_ratings (`resource_id`, `resource_type`, `user_id`, `rating`, `video_id`)  select video_id,"video", user_id,rating, video_id from engine4_video_ratings;');
			}
		}
		
		$table_exist_video = $db->query('SHOW TABLES LIKE \'engine4_video_videos\'')->fetch();
		if (!empty($table_exist_video)) {
			$importthumbnail = $db->query('SHOW COLUMNS FROM engine4_video_videos LIKE \'importthumbnail\'')->fetch();
			if (empty($importthumbnail)) {
				$db->query('ALTER TABLE  `engine4_video_videos` ADD  `importthumbnail` TINYINT( 1 ) NOT NULL DEFAULT "0";');
			}
		}
    
    parent::onInstall();
  }
	public function onDisable(){
		 $db = $this->getDb();
		$db->query("UPDATE engine4_core_jobtypes SET plugin = 'video_Plugin_Job_Encode',title = 'Video Encode',module='video' WHERE plugin = 'Sesvideo_Plugin_Job_Encode'");

		$db->query("UPDATE engine4_core_jobtypes SET plugin = 'video_Plugin_Job_Maintenance_RebuildPrivacy' ,title = 'Rebuild Video Privacy',module='video' WHERE plugin = 'Sesvideo_Plugin_Job_Maintenance_RebuildPrivacy'");	
		parent::onDisable();
 }
 public function onEnable(){
	  $db = $this->getDb();
	 $db->query("UPDATE engine4_core_jobtypes SET plugin = 'Sesvideo_Plugin_Job_Encode',title = 'Advanced Videos & Channels Plugin Video Encode',module='sesvideo' WHERE plugin = 'video_Plugin_Job_Encode'");

		$db->query("UPDATE engine4_core_jobtypes SET plugin = 'Sesvideo_Plugin_Job_Maintenance_RebuildPrivacy',module='sesvideo' ,title = 'Advanced Videos & Channels Plugin Rebuild Video Privacy' WHERE plugin = 'video_Plugin_Job_Maintenance_RebuildPrivacy'");
		parent::onEnable();
 }
}
