<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitemenu_Installer extends Engine_Package_Installer_Module {

  
  function onPreinstall() {
    $db = $this->getDb();
    
    $getErrorMsg = $this->_getVersion();
    if (!empty($getErrorMsg)) {
      return $this->_error($getErrorMsg);
    }

    $PRODUCT_TYPE = 'sitemenu';
    $PLUGIN_TITLE = 'Sitemenu';
    $PLUGIN_VERSION = '4.8.10p1';
    $PLUGIN_CATEGORY = 'plugin';
    $PRODUCT_DESCRIPTION = 'Advanced Menus Plugin - Interactive and Attractive Navigation';
    $PRODUCT_TITLE = 'Advanced Menus Plugin - Interactive and Attractive Navigation';
    $_PRODUCT_FINAL_FILE = 0;
    $SocialEngineAddOns_version = '4.8.7p14';
    $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
    $is_file = file_exists($file_path);
    if (empty($is_file)) {
      include APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/license3.php";
    } else {
      $db = $this->getDb();
      $select = new Zend_Db_Select($db);
      $select->from('engine4_core_modules')->where('name = ?', $PRODUCT_TYPE);
      $is_Mod = $select->query()->fetchObject();
      if (empty($is_Mod)) {
        include_once $file_path;
      }
    }

    parent::onPreinstall();
  }
  
  public function onInstall() {

    $db = $this->getDb();

    $db->query("ALTER TABLE `engine4_core_menuitems` CHANGE `order` `order` INT( 11 ) NOT NULL DEFAULT '999'");
    
    $db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` ='sitemenu';");
    
    // ADD SHOW COLUMN IN NOTIFICATION TABLE
    $isNotificationTableExist = $db->query("SHOW TABLES LIKE 'engine4_activity_notifications'")->fetch();
    if (!empty($isNotificationTableExist)) {
      $column_exist = $db->query("SHOW COLUMNS FROM engine4_activity_notifications LIKE 'show'")->fetch();
      if (empty($column_exist)) {
        $db->query("ALTER TABLE `engine4_activity_notifications` ADD `show` TINYINT( 1 ) NOT NULL DEFAULT '0'");
      }
    }

    // ADD VIEW COLUMN IN MESSAGE TABLE
    $isMessageTableExist = $db->query("SHOW TABLES LIKE 'engine4_messages_recipients'")->fetch();
    if (!empty($isMessageTableExist)) {
      $column_exist = $db->query("SHOW COLUMNS FROM engine4_messages_recipients LIKE 'inbox_view'")->fetch();
      if (empty($column_exist)) {
        $db->query("ALTER TABLE `engine4_messages_recipients` ADD `inbox_view` TINYINT( 1 ) NOT NULL DEFAULT '0'");
      }
    }
    
    //WORK FOR COMPATIBILITY WITH SITETHEME
        $this->sitethemeCompatibility();
    
    parent::onInstall();
  }

  public function onEnable() {

    $db = $this->getDb();
    // INSERT SITEMENU MINI AND MAIN MENU WIDGET IN HEADER AND REMOVE CORE MINI AND MAIN MENU WIDGET FORM HEADER
    $this->insertMiniAndMainMenuWidget();

    // INSERT SITEMENU FOOTER MENU AND REMOVE CORE FOOTER MENU FROM FOOTER
    $this->insertFooterMenuWidget();
    
    parent::onEnable();
  }

  public function onDisable() {
    $db = $this->getDb();

    // REPLACE SITEMENU MINI AND MAIN MENU BY CORE MINI AND MAIN MENU
    $headerPageId = $db->select()
                    ->from('engine4_core_pages', 'page_id')
                    ->where('name = ?', 'header')
                    ->limit(1)->query()->fetchColumn();

    if (!empty($headerPageId)) {
      $isCoreMiniMenuExist = $db->select()
                      ->from('engine4_core_content', 'content_id')
                      ->where('page_id = ?', $headerPageId)
                      ->where('name = ?', 'core.menu-mini')
                      ->limit(1)->query()->fetchColumn();

      $isSitemenuMiniMenuExist = $db->select()
                      ->from('engine4_core_content', 'content_id')
                      ->where('page_id = ?', $headerPageId)
                      ->where('name = ?', 'sitemenu.menu-mini')
                      ->limit(1)->query()->fetchColumn();

      if (empty($isCoreMiniMenuExist) && !empty($isSitemenuMiniMenuExist))
        $db->query("UPDATE `engine4_core_content` SET `name` = 'core.menu-mini' WHERE `name` = 'sitemenu.menu-mini' LIMIT 1");

      $isCoreMainMenuExist = $db->select()
                      ->from('engine4_core_content', 'content_id')
                      ->where('page_id = ?', $headerPageId)
                      ->where('name = ?', 'core.menu-main')
                      ->limit(1)->query()->fetchColumn();

      $isSitemenuMainMenuExist = $db->select()
                      ->from('engine4_core_content', 'content_id')
                      ->where('page_id = ?', $headerPageId)
                      ->where('name = ?', 'sitemenu.menu-main')
                      ->limit(1)->query()->fetchColumn();

      if (empty($isCoreMainMenuExist) && !empty($isSitemenuMainMenuExist))
        $db->query("UPDATE `engine4_core_content` SET `name` = 'core.menu-main' WHERE `name` = 'sitemenu.menu-main' LIMIT 1");
    }

    // REMOVE SITEMENU FOOTER WIDGET FROM FOOTER
    $footerPageId = $db->select()
                    ->from('engine4_core_pages', 'page_id')
                    ->where('name = ?', 'footer')
                    ->limit(1)->query()->fetchColumn();

    if (!empty($footerPageId)) {
      $isCoreFooterMenuExist = $db->select()
                      ->from('engine4_core_content', 'content_id')
                      ->where('page_id = ?', $footerPageId)
                      ->where('name = ?', 'core.menu-footer')
                      ->limit(1)->query()->fetchColumn();

      $isSitemenuFooterMenuExist = $db->select()
                      ->from('engine4_core_content', 'content_id')
                      ->where('page_id = ?', $footerPageId)
                      ->where('name = ?', 'sitemenu.menu-footer')
                      ->limit(1)->query()->fetchColumn();

      if (empty($isCoreFooterMenuExist) && !empty($isSitemenuFooterMenuExist))
        $db->query("UPDATE `engine4_core_content` SET `name` = 'core.menu-footer' WHERE `name` = 'sitemenu.menu-footer' LIMIT 1");
    }
    parent::onDisable();
  }

  private function insertMiniAndMainMenuWidget() {

    $db = $this->getDb();
    $isMiniMenuWidgetChange = false;
    $headerPageId = $db->select()
                    ->from('engine4_core_pages', 'page_id')
                    ->where('name = ?', 'header')
                    ->limit(1)->query()->fetchColumn();

    if (!empty($headerPageId)) {
      $parentContentId = $db->select()
                      ->from('engine4_core_content', 'content_id')
                      ->where('page_id = ?', $headerPageId)
                      ->where('name = ?', 'main')
                      ->where('type =?', 'container')
                      ->limit(1)->query()->fetchColumn();

      if (!empty($parentContentId)) {
        // CORE MINI MENU WIDGET EXIST
        $isMiniMenuWidgetExist = $db->select()
                        ->from('engine4_core_content', 'content_id')
                        ->where('page_id = ?', $headerPageId)
                        ->where('parent_content_id =?', $parentContentId)
                        ->where('name = ?', 'core.menu-mini')
                        ->where('type =?', 'widget')
                        ->limit(1)->query()->fetchColumn();

        // SITEMENU MINI MENU WIDGET EXIST
        $isSitemenuMiniMenuExist = $db->select()
                        ->from('engine4_core_content', 'content_id')
                        ->where('page_id = ?', $headerPageId)
                        ->where('parent_content_id =?', $parentContentId)
                        ->where('name = ?', 'sitemenu.menu-mini')
                        ->where('type =?', 'widget')
                        ->limit(1)->query()->fetchColumn();
        
        if (!empty($isMiniMenuWidgetExist) && empty($isSitemenuMiniMenuExist)) {
          $isMiniMenuWidgetChange = true;
          $db->query("UPDATE `engine4_core_content` SET `name` = 'sitemenu.menu-mini' WHERE `name` = 'core.menu-mini' LIMIT 1");
        }
        
        // SITETHEME MINI MENU WIDGET EXIST
        if( empty($isMiniMenuWidgetExist) ) {
          $isSitethemeMiniMenuExist = $db->select()
                        ->from('engine4_core_content', 'content_id')
                        ->where('page_id = ?', $headerPageId)
                        ->where('parent_content_id =?', $parentContentId)
                        ->where('name = ?', 'sitetheme.menu-mini')
                        ->where('type =?', 'widget')
                        ->limit(1)->query()->fetchColumn();
          
          if (!empty($isSitethemeMiniMenuExist) && empty($isSitemenuMiniMenuExist)) {
            $isMiniMenuWidgetChange = true;
            $db->query("UPDATE `engine4_core_content` SET `name` = 'sitemenu.menu-mini' WHERE `name` = 'sitetheme.menu-mini' LIMIT 1");
            
            // DELETE SITETHEME SEARCHBOX
            $sitethemeSearchWidgetId = $db->select()
                        ->from('engine4_core_content', 'content_id')
                        ->where('page_id = ?', $headerPageId)
                        ->where('parent_content_id =?', $parentContentId)
                        ->where('name = ?', 'sitetheme.searchbox-sitestoreproduct')
                        ->where('type =?', 'widget')
                        ->limit(1)->query()->fetchColumn();

            if( !empty($sitethemeSearchWidgetId) ) {
              $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`content_id` = $sitethemeSearchWidgetId LIMIT 1");
            }
          }
        }

        if (!empty($isMiniMenuWidgetChange)) {
          // CHANGE ORDER OF MINI MENUS
          $miniMenusLink = $db->select()
                    ->from('engine4_core_menuitems', array('name'))
                    ->where('menu = ?', 'core_mini')
                    ->query()
                    ->fetchAll(Zend_Db::FETCH_COLUMN);

          foreach( $miniMenusLink as $linkName ) {
            if( $linkName == 'sitemenu_mini_cart' ) {
              $db->query("UPDATE `engine4_core_menuitems` SET `order` = '1' WHERE `name` = 'sitemenu_mini_cart' LIMIT 1 ;");
            } else if( $linkName == 'sitemenu_mini_friend_request' ) {
              $db->query("UPDATE `engine4_core_menuitems` SET `order` = '2' WHERE `name` = 'sitemenu_mini_friend_request' LIMIT 1 ;");
            } else if( $linkName == 'core_mini_messages' ) {
              $db->query("UPDATE `engine4_core_menuitems` SET `order` = '3' WHERE `name` = 'core_mini_messages' LIMIT 1 ;");
            } else if( $linkName == 'sitemenu_mini_notification' ) {
              $db->query("UPDATE `engine4_core_menuitems` SET `order` = '4' WHERE `name` = 'sitemenu_mini_notification' LIMIT 1 ;");
            } else if( $linkName == 'core_mini_settings' ) {
              $db->query("UPDATE `engine4_core_menuitems` SET `order` = '5' WHERE `name` = 'core_mini_settings' LIMIT 1 ;");
            } else if( $linkName == 'core_mini_profile' ) {
              $db->query("UPDATE `engine4_core_menuitems` SET `order` = '6' WHERE `name` = 'core_mini_profile' LIMIT 1 ;");
            } else if( $linkName == 'core_mini_admin' ) {
              $db->query("UPDATE `engine4_core_menuitems` SET `order` = '7' WHERE `name` = 'core_mini_admin' LIMIT 1 ;");
            } else if( $linkName == 'core_mini_auth' ) {
              $db->query("UPDATE `engine4_core_menuitems` SET `order` = '98' WHERE `name` = 'core_mini_auth' LIMIT 1 ;");
            } else if( $linkName == 'core_mini_signup' ) {
              $db->query("UPDATE `engine4_core_menuitems` SET `order` = '99' WHERE `name` = 'core_mini_signup' LIMIT 1 ;");
            } 
          }
        }

        // CORE MAIN MENU WIDGET EXIST
        $isMainMenuWidgetExist = $db->select()
                        ->from('engine4_core_content', 'content_id')
                        ->where('page_id = ?', $headerPageId)
                        ->where('parent_content_id =?', $parentContentId)
                        ->where('name = ?', 'core.menu-main')
                        ->where('type =?', 'widget')
                        ->limit(1)->query()->fetchColumn();

        // SITEMENU MAIN MENU WIDGET EXIST
        $isSitemenuMainMenuExist = $db->select()
                        ->from('engine4_core_content', 'content_id')
                        ->where('page_id = ?', $headerPageId)
                        ->where('parent_content_id =?', $parentContentId)
                        ->where('name = ?', 'sitemenu.menu-main')
                        ->where('type =?', 'widget')
                        ->limit(1)->query()->fetchColumn();
        
        if (!empty($isMainMenuWidgetExist) && empty($isSitemenuMainMenuExist)) {
          $db->query("UPDATE `engine4_core_content` SET `name` = 'sitemenu.menu-main' WHERE `name` = 'core.menu-main' LIMIT 1");
        }
        
        // SITETHEME MAIN MENU WIDGET EXIST
        if( empty($isMainMenuWidgetExist) ) {
          $isSitethemeMainMenuExist = $db->select()
                        ->from('engine4_core_content', 'content_id')
                        ->where('page_id = ?', $headerPageId)
                        ->where('parent_content_id =?', $parentContentId)
                        ->where('name = ?', 'sitetheme.menu-main')
                        ->where('type =?', 'widget')
                        ->limit(1)->query()->fetchColumn();
          
          if (!empty($isSitethemeMainMenuExist) && empty($isSitemenuMainMenuExist)) {
            $db->query("UPDATE `engine4_core_content` SET `name` = 'sitemenu.menu-main' WHERE `name` = 'sitetheme.menu-main' LIMIT 1");
            $db->query('UPDATE `engine4_core_content` SET `params` = \'{"sitemenu_is_fixed":"1"}\' WHERE `name` = "sitemenu.menu-main" LIMIT 1');
          }
        }
        
        //WORK FOR COMPATIBILITY WITH SITETHEME
        $this->sitethemeCompatibility();
      }
    }
  }

  private function insertFooterMenuWidget() {

    $db = $this->getDb();
    $footerPageId = $db->select()
                    ->from('engine4_core_pages', 'page_id')
                    ->where('name = ?', 'footer')
                    ->limit(1)->query()->fetchColumn();

    if (!empty($footerPageId)) {
      $parentContentId = $db->select()
                      ->from('engine4_core_content', 'content_id')
                      ->where('page_id = ?', $footerPageId)
                      ->where('name = ?', 'main')
                      ->where('type = ?', 'container')
                      ->limit(1)->query()->fetchColumn();

      if (!empty($parentContentId)) {
        // CORE FOOTER MENU WIDGET EXIST
        $isFooterMenuWidgetExist = $db->select()
                        ->from('engine4_core_content', 'content_id')
                        ->where('page_id = ?', $footerPageId)
                        ->where('parent_content_id =?', $parentContentId)
                        ->where('name = ?', 'core.menu-footer')
                        ->where('type =?', 'widget')
                        ->limit(1)->query()->fetchColumn();

        // SITEMENU FOOTER MENU WIDGET EXIST
        $isSitemenuFooterMenuExist = $db->select()
                        ->from('engine4_core_content', 'content_id')
                        ->where('page_id = ?', $footerPageId)
                        ->where('parent_content_id =?', $parentContentId)
                        ->where('name = ?', 'sitemenu.menu-footer')
                        ->where('type =?', 'widget')
                        ->limit(1)->query()->fetchColumn();

        if (!empty($isFooterMenuWidgetExist) && empty($isSitemenuFooterMenuExist)) {
          $db->query("UPDATE `engine4_core_content` SET `name` = 'sitemenu.menu-footer' WHERE `name` = 'core.menu-footer' LIMIT 1");
        }
      }
    }
  }
  
  private function _getVersion() {
  
    $db = $this->getDb();

    $errorMsg = '';
    $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

    $modArray = array(
      'sitestore' => '4.8.3',
      'sitereview' => '4.8.3',
      'sitereviewlistingtype' => '4.8.3',
      'feedback'  =>  '4.8.3'
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

				$isModSupport = $this->checkVersion($getModVersion->version, $value);
				if (empty($isModSupport)) {
					$finalModules[$key] = $getModVersion->title;
				}
			}
    }

    foreach ($finalModules as $modArray) {
      $errorMsg .= '<div class="tip"><span style="background-color: #da5252;color:#FFFFFF;">Note: You do not have the latest version of the "' . $modArray . '". Please upgrade "' . $modArray . '" on your website to the latest version available in your SocialEngineAddOns Client Area to enable its integration with "'.$modArray.'".<br/> Please <a class="" href="' . $base_url . '/manage">Click here</a> to go Manage Packages.</span></div>';

    }

    return $errorMsg;
  }
      private function checkVersion($databaseVersion, $checkDependancyVersion) {
        $f = $databaseVersion;
        $s = $checkDependancyVersion;
        if (strcasecmp($f, $s) == 0)
            return -1;

        $fArr = explode(".", $f);
        $sArr = explode('.', $s);
        if (count($fArr) <= count($sArr))
            $count = count($fArr);
        else
            $count = count($sArr);

        for ($i = 0; $i < $count; $i++) {
            $fValue = $fArr[$i];
            $sValue = $sArr[$i];
            if (is_numeric($fValue) && is_numeric($sValue)) {
                if ($fValue > $sValue)
                    return 1;
                elseif ($fValue < $sValue)
                    return 0;
                else {
                    if (($i + 1) == $count) {
                        return -1;
                    } else
                        continue;
                }
            }
            elseif (is_string($fValue) && is_numeric($sValue)) {
                $fsArr = explode("p", $fValue);

                if ($fsArr[0] > $sValue)
                    return 1;
                elseif ($fsArr[0] < $sValue)
                    return 0;
                else {
                    return 1;
                }
            } elseif (is_numeric($fValue) && is_string($sValue)) {
                $ssArr = explode("p", $sValue);

                if ($fValue > $ssArr[0])
                    return 1;
                elseif ($fValue < $ssArr[0])
                    return 0;
                else {
                    return 0;
                }
            } elseif (is_string($fValue) && is_string($sValue)) {
                $fsArr = explode("p", $fValue);
                $ssArr = explode("p", $sValue);
                if ($fsArr[0] > $ssArr[0])
                    return 1;
                elseif ($fsArr[0] < $ssArr[0])
                    return 0;
                else {
                    if ($fsArr[1] > $ssArr[1])
                        return 1;
                    elseif ($fsArr[1] < $ssArr[1])
                        return 0;
                    else {
                        return -1;
                    }
                }
            }
        }
    }
  private function sitethemeCompatibility() {

    $db = $this->getDb();

    $headerPageId = $db->select()
                    ->from('engine4_core_pages', 'page_id')
                    ->where('name = ?', 'header')
                    ->limit(1)->query()->fetchColumn();

    if (!empty($headerPageId)) {
      $parentContentId = $db->select()
                      ->from('engine4_core_content', 'content_id')
                      ->where('page_id = ?', $headerPageId)
                      ->where('name = ?', 'main')
                      ->where('type =?', 'container')
                      ->limit(1)->query()->fetchColumn();

      if (!empty($parentContentId)) {
        //WORK FOR COMPATIBILITY WITH SITETHEME STARTS

        $isShoppingHubActive = $db->select()->from('engine4_core_themes', 'active')->where('name = ?', 'shoppinghub')->where('active = ?', 1)->limit(1)->query()->fetchColumn();
        if (!empty($isShoppingHubActive)) {
          $mainMenuRow = $db->select()->from('engine4_core_content')->where('page_id = ?', $headerPageId)
                  ->where('parent_content_id =?', $parentContentId)
                  ->where('name = ?', 'sitemenu.menu-main')
                  ->where('type =?', 'widget')
                  ->limit(1)->query()
                  ->fetch(Zend_Db::FETCH_OBJ);
          if (!empty($mainMenuRow) && !empty($mainMenuRow->params)) {
            $position = strpos($mainMenuRow->params, "sitemenu_fixed_height");
            $pattern = '/"sitemenu_fixed_height":"\d*"/';
            $replacement = '"sitemenu_fixed_height":"50"';
            $string = $mainMenuRow->params;
            if (!empty($position)) {
              $temp_str = preg_replace($pattern, $replacement, $mainMenuRow->params);
              $db->query("UPDATE `engine4_core_content` SET `params` = '$temp_str' WHERE `name` = 'sitemenu.menu-main' LIMIT 1");
            }
          }
        }

        //WORK FOR COMPATIBILITY WITH SITETHEME ENDS
      }
    }
  }

}
