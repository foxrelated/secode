<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageurl
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2011-11-15 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageurl_Installer extends Engine_Package_Installer_Module {

  function onInstall() {

    //GET DB
    $db = $this->getDb();
    //CHECK THAT SITEPAGE PLUGIN IS ACTIVATED OR NOT
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_settings')
            ->where('name = ?', 'sitepage.is.active')
            ->limit(1);
    $sitepage_settings = $select->query()->fetchAll();
    if (!empty($sitepage_settings)) {
      $sitepage_is_active = $sitepage_settings[0]['value'];
    } else {
      $sitepage_is_active = 0;
    }
    
    //CHECK THAT SITEPAGE PLUGIN IS INSTALLED OR NOT
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitepage')
            ->where('version >= ?', '4.2.1')
            ->where('enabled = ?', 1);
    $check_sitepage_version = $select->query()->fetchObject();
    if(!empty($check_sitepage_version)) {
      $check_sitepage_version = 1;

    }
    else {
      $check_sitepage_version = 0;
    }

    //CHECK THAT SITEPAGE PLUGIN IS INSTALLED OR NOT
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitepage')
            ->where('enabled = ?', 1);
    $check_sitepage = $select->query()->fetchObject();
    if (!empty($check_sitepage) && !empty($sitepage_is_active) && !empty($check_sitepage_version)) {

      parent::onInstall();
      $table_url_exist = $db->query('SHOW TABLES LIKE \'engine4_sitepage_bannedpageurls\'')->fetch();
      if (!empty($table_url_exist)) {
        $db->query("RENAME TABLE `engine4_sitepage_bannedpageurls` TO `engine4_seaocore_bannedpageurls` ");
      }
      $table_exist = $db->query('SHOW TABLES LIKE \'engine4_seaocore_bannedpageurls\'')->fetch();
			if (empty($table_exist)) {
				$db->query("CREATE TABLE IF NOT EXISTS `engine4_seaocore_bannedpageurls` (
										`bannedpageurl_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
										`word` varchar(255) COLLATE utf8_Unicode_ci NOT NULL,
										PRIMARY KEY (`bannedpageurl_id`),
										UNIQUE KEY `word` (`word`)
									) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");

				$db->query("INSERT IGNORE INTO `engine4_seaocore_bannedpageurls` (`word`) VALUES
										('music'),('polls'),('blogs'),('videos'),	('classifieds'),('albums'),('events'),	('groups'),
										('forums'),('invite'),('recipeitems'),('ads'),	('likes'),('documents'),('sitepage'),
										('sitepagepoll'),('sitepageoffer'),('sitepagevideo'),('sitepagedocument'),('sitepagenote'),
										('sitepageevent'),('sitepagemusic'),('sitepageinvite'),('sitepagereview'),('sitepagebadge'),
									  ('sitepageform'),('sitepagealbum'),('sitepagediscussion'),('recipe'),('sitelike'),('suggestion'),('advanceslideshow'),('feedback'),('grouppoll'),('groupdocumnet'),('sitealbum'),('siteslideshow'),('userconnection'),('communityad'),('list'),('article'),
										('listing'),('store'),('pagevideos'),('pageitem'),('pageitems'),('pageevents'),('pagedocuments'),('pageoffer'),('pagenotes'),('pageinvites'),('pageform'),('pagemusic'),
										('pagereviews'),('businessvideos'),('businessitem'),('businessitems'),('businessevents'),
									  ('businessdocuments'),('businessoffer'),('businessnotes'),('businessinvites'),('businessform'),('businessmusic'),('businessreviews'),('listingitems'),('market'),('document'),('pdf'),('pokes'),('facebook'),('album'),('photo'),('files'),('file'),('page'),
									  ('business'),('backup'),('question'),('answer'),('questions'),('answers'),('newsfeed'),('birthday'),('wall'),('profiletype'),('memberlevel'),('members'),('member'),('memberlevel'),
					          ('level'),('slideshow'),('seo'),('xml'),('cmspages'),('favoritepages'),('help'),('rss'),
										('stories'),('story'),('visits'),('points'),('vote'),('advanced'),('listingitem');");
			}
      
 
      //CHECK THAT SITEPAGE PLUGIN IS INSTALLED OR NOT
			$select = new Zend_Db_Select($db);
			$select
							->from('engine4_core_modules')
							->where('name = ?', 'sitepageurl')
							->where('enabled = ?', 1);
			$check_sitepageurl = $select->query()->fetchObject();
      if(empty($check_sitepageurl)) {
				$includeModules = array("sitepage" => "sitepage","sitepagedocument" => 'Documents', "sitepageoffer" => 'Offers', "sitepageform" => "Form", "sitepagediscussion" => "Discussions", "sitepagenote" => "Notes", "sitepagealbum" => "Photos", "sitepagevideo" => "Videos", "sitepageevent" => "Events", "sitepagepoll" => "Polls", "sitepageinvite" => "Invite & Promote", "sitepagebadge" => "Badges", "sitepagelikebox" => "External Badge", "sitepagemusic" => "Music","sitebusiness" => "sitebusiness","sitebusinessdocument" => 'Documents', "sitebusinessoffer" => 'Offers', "sitebusinessform" => "Form", "sitebusinessdiscussion" => "Discussions", "sitebusinessnote" => "Notes", "sitebusinessalbum" => "Photos", "sitebusinessvideo" => "Videos", "sitebusinessevent" => "Events", "sitebusinesspoll" => "Polls", "sitebusinessinvite" => "Invite & Promote", "sitebusinessbadge" => "Badges", "sitebusinesslikebox" => "External Badge", "sitebusinessmusic" => "Music","list"=>"list");
				$select = new Zend_Db_Select($db);
				$select
								->from('engine4_core_modules','name')
								->where('enabled = ?', 1);
				$enableAllModules = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
				$enableModules = array_intersect(array_keys($includeModules), $enableAllModules);
		
				foreach ($enableAllModules as $moduleName) {
					if(!in_array($moduleName,$enableModules)) {
						$file_path = APPLICATION_PATH . "/application/modules/" . ucfirst($moduleName) . "/settings/manifest.php";
						$contentItem = array();
						if (@file_exists($file_path)) {
							$ret = include $file_path;
							$is_exist = array();
							if (isset($ret['routes'])) {
								foreach ($ret['routes'] as $item) {
									$route = $item['route'];
									$route_array =  explode('/',$route);
									$route_url = strtolower($route_array[0]);
									
									if(!empty($route_url) && !in_array($route_url,$is_exist)) {
										$db->query("INSERT IGNORE INTO `engine4_seaocore_bannedpageurls` (`word`) VALUES ('".$route_url. "')");
									}
									$is_exist[] = $route_url;
								}
							}
						} 
					}
					else {
						if($moduleName == 'sitepage' || $moduleName == 'sitebusiness') {
							$name = $moduleName .'.manifestUrlS';
						}
						else {
							$name = $moduleName .'.manifestUrl';
						}
						$select = new Zend_Db_Select($db);
						$select
								->from('engine4_core_settings','value')
								->where('name = ?', $name)
								->limit(1);
						$route_url = strtolower($select->query()->fetchAll(Zend_Db::FETCH_COLUMN));
						if(!empty($route_url)) {
							$db->query("INSERT IGNORE INTO `engine4_seaocore_bannedpageurls` (`bannedpageurl_id`, `word`) VALUES ('','".$route_url. "')");
						}
					}
				}
      }
    } 
   if(!empty($check_sitepage) && !empty($sitepage_is_active) && empty($check_sitepage_version)) {

      $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
      return $this->_error("<span style='color:red'>Note: The version of the Directory / Pages Plugin on your website is less than the minimum required version: 4.1.8p3. Please download the latest version of this  plugin from your Client Area on SocialEngineAddOns and upgrade it on your website.
</span>");

    }
    elseif (!empty($check_sitepage) && empty($sitepage_is_active)) {
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

      return $this->_error("<span style='color:red'>Note: You have installed the Directory / Pages Plugin but not activated it on your site yet. Please activate it first before installing the Directory / Pages - Short Page URL Extension.</span><br/> <a href='" . 'http://' . $core_final_url . "admin/sitepage/settings/readme'>Click here</a> to activate the Page Plugin.");
    } elseif(empty($check_sitepage)) {
      $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
      return $this->_error("<span style='color:red'>Note: You have not installed the Directory / Pages Plugin on your site yet. Please install it first before installing the Directory / Pages - Short Page URL Extension.</span><br/> <a href='" . $base_url . "/manage'>Click here</a> to go Manage Packages.");
    }
  }

}

?>