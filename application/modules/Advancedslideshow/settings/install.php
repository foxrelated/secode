<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedslideshow_Installer extends Engine_Package_Installer_Module {

  function onPreInstall() {
    $PRODUCT_TYPE = 'advancedslideshow';
    $PLUGIN_TITLE = 'Advancedslideshow';
    $PLUGIN_VERSION = '4.8.6';
    $PLUGIN_CATEGORY = 'plugin';
    $PRODUCT_DESCRIPTION = 'Advancedslideshow Plugin';
    $_PRODUCT_FINAL_FILE = 0;
    $_BASE_FILE_NAME = 0;
    $PRODUCT_TITLE = 'Advancedslideshow Plugin';
    $SocialEngineAddOns_version = '4.8.5';
    $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
    $is_file = file_exists($file_path);
    if (empty($is_file)) {
      include_once APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/license4.php";
    } else {
      if (!empty($_PRODUCT_FINAL_FILE)) {
        include_once APPLICATION_PATH . '/application/modules/' . $PLUGIN_TITLE . '/controllers/license/' . $_PRODUCT_FINAL_FILE;
      }
      include_once $file_path;
    }
    parent::onPreInstall();
  }

  function onInstall() {
    $db = $this->getDb();
    
    $tableExist = $db->query("SHOW TABLES LIKE 'engine4_advancedslideshows'")->fetch(); 
    if($tableExist) {
      $column_exist = $db->query("SHOW COLUMNS FROM `engine4_advancedslideshows` LIKE 'resource_type'")->fetch();
      if(empty($column_exist)) {
        $db->query("ALTER TABLE `engine4_advancedslideshows` ADD `resource_type` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");        
      }
      
			$column_exist = $db->query("SHOW COLUMNS FROM engine4_advancedslideshows LIKE 'resource_id'")->fetch();
			if (empty($column_exist)) {
				$db->query("ALTER TABLE `engine4_advancedslideshows` ADD `resource_id` INT( 11 ) NOT NULL DEFAULT '0'");
			}      
    }
    
    $tableExist = $db->query("SHOW TABLES LIKE 'engine4_advancedslideshow_images'")->fetch(); 
    if($tableExist) {
      $column_exist = $db->query("SHOW COLUMNS FROM `engine4_advancedslideshow_images` LIKE 'params'")->fetch();
      if(empty($column_exist)) {
        $db->query("ALTER TABLE `engine4_advancedslideshow_images` ADD `params` text COLLATE utf8_unicode_ci");        
      } 
    }    

    $pageTime = time();
    $mosName = 'advancedslideshow';
    $filePath = ucfirst($mosName) . '/controllers/license/license3.php';

    $db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
		('$mosName.basetime', $pageTime ),
		('$mosName.isvar', 0 ),
		('$mosName.filepath', '$filePath');");
    
    
    
    
    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_advancedslideshows'")->fetch();
    if (!empty($table_exist)) {
      $column_exist = $db->query("SHOW COLUMNS FROM engine4_advancedslideshows LIKE 'noob_elements'")->fetch();
      if (empty($column_exist)) {
        $db->query("ALTER TABLE `engine4_advancedslideshows` ADD `noob_elements` text");
      }
    }   
    
    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_advancedslideshow_images'")->fetch();
    if (!empty($table_exist)) {
      $column_exist = $db->query("SHOW COLUMNS FROM engine4_advancedslideshow_images LIKE 'slide_html'")->fetch();
      if (empty($column_exist)) {
        $db->query("ALTER TABLE `engine4_advancedslideshow_images` ADD `slide_html` text");
      }
    }    
    
    $select = new Zend_Db_Select($db);
    $select->from('engine4_core_modules')
           ->where('name = ?', 'advancedslideshow')
           ->where('version <= ?', '4.7.0p1');
    $is_enabled = $select->query()->fetchObject();
    if (!empty($is_enabled)) {    
        $table_exist = $db->query("SHOW TABLES LIKE 'engine4_advancedslideshows'")->fetch();
        if (!empty($table_exist)) {
          $db->query("ALTER TABLE `engine4_advancedslideshows` CHANGE `widget_page` `widget_page` INT NULL DEFAULT NULL");
          $widgetPageIndex = $db->query("SHOW INDEX FROM `engine4_advancedslideshows` WHERE Key_name = 'widget_page'")->fetch();
          if (empty($widgetPageIndex)) {
            $db->query("ALTER TABLE `engine4_advancedslideshows` ADD INDEX ( `widget_page` )");
          }

          $db->query("ALTER TABLE `engine4_advancedslideshows` CHANGE `widget_position` `widget_position` VARCHAR( 225 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");
          $widgetPositionIndex = $db->query("SHOW INDEX FROM `engine4_advancedslideshows` WHERE Key_name = 'widget_position'")->fetch();
          if (empty($widgetPositionIndex)) {
            $db->query("ALTER TABLE `engine4_advancedslideshows` ADD INDEX ( `widget_position` )");
          }      
        }          
    }
    parent::onInstall();
  }
  
  function onPostinstall() {
    $db = $this->getDb();
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'advancedslideshow')
            ->where('version <= ?', '4.8.0');
    $check_advancedslideshow = $select->query()->fetchObject();
    
    if(!empty($check_advancedslideshow)) {
        $table_exist = $db->query("SHOW TABLES LIKE 'engine4_advancedslideshow_images'")->fetch();
        if (!empty($table_exist)) {
          $column_exist = $db->query("SHOW COLUMNS FROM engine4_advancedslideshow_images LIKE 'extension'")->fetch();
          if (!empty($column_exist)) {
            $db->query("ALTER TABLE `engine4_advancedslideshow_images` DROP `extension`");
          }
          
          $column_exist = $db->query("SHOW COLUMNS FROM engine4_advancedslideshow_images LIKE 'creation_date'")->fetch();
          if (!empty($column_exist)) {
            $db->query("ALTER TABLE `engine4_advancedslideshow_images` DROP `creation_date`");
          }      
          
          $column_exist = $db->query("SHOW COLUMNS FROM engine4_advancedslideshow_images LIKE 'user_id'")->fetch();
          if (!empty($column_exist)) {
            $db->query("ALTER TABLE `engine4_advancedslideshow_images` DROP `user_id`");
          }           
        }   
    }    

    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_advancedslideshow_images')
            ->where('file_id != ?', 0);
    $isSlideExists = $select->query()->fetchObject();
    if (!empty($check_advancedslideshow) && !empty($isSlideExists)) {
      $view = new Zend_View();
      $baseUrl = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
      $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
      
      $actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
      
      if($actionName == 'install') {
				$redirector->gotoUrl($baseUrl . 'admin/advancedslideshow/settings/transfer-slides/allowTransfer/1/redirect/install');
			}
			else {
				$redirector->gotoUrl($baseUrl . 'admin/advancedslideshow/settings/transfer-slides/allowTransfer/1/redirect/query');
			}	      

    }
        
  }  

}
?>
