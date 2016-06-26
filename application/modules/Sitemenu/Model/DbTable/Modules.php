<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Modules.php 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitemenu_Model_DbTable_Modules extends Engine_Db_Table {

  protected $_name = 'sitemenu_modules';
  protected $_rowClass = 'Sitemenu_Model_Module';

  // Function: Return the 'Module Name'array, which are available in the table.
  public function getModuleName() {

    // Queary which return the modules name which are already set by admin.
    $moduleArray = $this->select()
                    ->from($this->info('name'), array('module_name'))
                    ->query()->fetchAll(Zend_Db::FETCH_COLUMN);

      // Array: Which modules are not allowed.
      $not_allow_modules = array('facebookse', 'facebooksefeed', 'facebooksepage', 'grouppoll', 'birthday', 'poke', 'sitelike', 'dbbackup', 'suggestion', 'mcard', 'groupdocument', 'siteslideshow', 'mapprofiletypelevel', 'peopleyoumayknow', 'userconnection', 'communityad', 'seaocore', 'feedback',  'sitepagealbum', 'sitepageinvite', 'sitepagepoll', 'sitepagediscussion', 'sitepagedocument', 'sitepagenote', 'sitepageevent', 'sitepageoffer', 'sitepagevideo', 'sitepageform', 'sitepagebadge', 'sitepagereview', 'sitepagegeolocation', 'sitepageadmincontact', 'sitepageurl', 'sitepagetwitter', 'sitepagewishlist', 'sitemenu', 'sitegroupadmincontact', 'sitegroupalbum', 'sitegroupbadge', 'sitegroupdiscussion', 'sitegroupdocument', 'sitegroupevent', 'sitegroupform', 'sitegroupintegration', 'sitegroupinvite', 'sitegrouplikebox', 'sitegroupmember', 'sitegroupmusic', 'sitegroupnote', 'sitegroupoffer', 'sitegrouppoll', 'sitegroupreview', 'sitegroupurl', 'sitegroupvideo', 'siteeventadmincontact', 'siteeventdocument', 'siteeventemail', 'siteeventinvite', 'siteeventrepeat', 'sitestoreadmincontact', 'sitestorealbum', 'sitestoredocument', 'sitestoreform', 'sitestoreintegration', 'sitestoreinvite', 'sitestorelikebox', 'sitestoreoffer', 'sitestorereservation', 'sitestorereview', 'sitestoreurl', 'sitestorevideo', 'sitebusinessadmincontact', 'sitebusinessalbum', 'sitebusinessbadge', 'sitebusinessdiscussion', 'sitebusinessdocument', 'sitebusinessevent', 'sitebusinessform', 'sitebusinessintegration', 'sitebusinessinvite', 'sitebusinesslikebox', 'sitebusinessmember', 'sitebusinessmusic', 'sitebusinessnote', 'sitebusinessoffer', 'sitebusinesspoll', 'sitebusinessreview', 'sitebusinessurl', 'sitebusinessvideo', 'sitegroupadmincontact', 'sitegroupalbum', 'sitegroupbadge', 'sitegroupdiscussion', 'sitegroupdocument', 'sitegroupevent', 'sitegroupform', 'sitegroupintegration', 'sitegroupinvite', 'sitegrouplikebox', 'sitegroupmember', 'sitegroupmusic', 'sitegroupnote', 'sitegroupoffer', 'sitegrouppoll', 'sitegroupreview', 'sitegroupurl', 'sitegroupvideo', 'Advancedactivity', 'Siteusercoverphoto', 'Sitecontentcoverphoto', 'Siteluminous', 'Sitereviewlistingtype', 'Sitereviewpaidlisting');
      
      return array_merge($moduleArray, $not_allow_modules);
    }
    
    /**
     * Returns an array of modules which are enabled in the manage module tab.
     *
     * @menuName string menu name.
     * @return array
     */

  public function getContentList() {
    $getEnabledModuleNames = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();

    $moduleSelect = $this->select()
            ->from($this->info('name'), array("module_id", "module_title"))
            ->where('status = ? ', 1);

    if (!empty($getEnabledModuleNames)) {
      $moduleSelect->where('module_name IN(?)', $getEnabledModuleNames);
    }

    $menus = $moduleSelect->query()->fetchAll();

    $data = array();
    foreach ($menus as $menu) {
      $data[$menu['module_id']] = $menu['module_title'];
    }
    return $data;
  }

  /**
   * Returns module attribute
   *
   * @param array $fetch_column_array
   * @param int $moduleId
   * @return array
   */
  public function getModuleAttribute($fetch_column_array, $moduleId) {

    $select = $this->select()
            ->from($this->info('name'), $fetch_column_array)
            ->where("module_id = ?", $moduleId);

    return $this->fetchRow($select);
  }

  /**
   * Returns menu item attribute
   *
   * @param array $fetch_column_array
   * @param int $menuItemId
   * @return array
   */
  public function getMenuItemColum($fetch_column_array, $menuItemId) {
    if (!empty($menuItemId)) {
      $menuItemsTable = Engine_Api::_()->getDbtable('menuItems', 'core');
      return $menuItemsTable->select()
                      ->from($menuItemsTable->info('name'), $fetch_column_array)
                      ->where("id = ?", $menuItemId)->query()->fetchColumn();
    }
  }

  public function isModuleTypeAvailable($menu, $params) {
    $sitemenuManageType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemenu.manage.type', 1);
    $sitemenuInfoType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemenu.info.type', 1);
    if (isset($params['menu']) && !empty($params['menu']) && $params['menu'] == 'core_mini') {
      foreach ($menu as $row) {
        // Check enabled
        if (isset($row->enabled) && !$row->enabled) {
          continue;
        }

        // Plugin
        $page = null;
        $multi = false;
        if (!empty($row->plugin)) {

          // Support overriding the method
          if (strpos($row->plugin, '::') !== false) {
            list($pluginName, $method) = explode('::', $row->plugin);
          } else {
            $pluginName = $row->plugin;
            $method = 'onMenuInitialize_' . $this->_formatMenuName($row->name);
          }

          // Load the plugin
          try {
            $plugin = Engine_Api::_()->loadClass($pluginName);
          } catch (Exception $e) {
            // Silence exceptions
            continue;
          }

          // Run plugin
          try {
            $result = $plugin->$method($row);
          } catch (Exception $e) {
            // Silence exceptions
            continue;
          }

          if ($result === true) {
            // Just generate normally
          } else if ($result === false) {
            // Don't generate
            continue;
          } else if (is_array($result)) {
            // We got either page params or multiple page params back
            // Single
            if (array_values($result) !== $result) {
              $page = $result;
            }
            // Multi
            else {
              // We have to do this manually
              foreach ($result as $key => $value) {
                if (is_numeric($key)) {
                  if (!empty($options)) {
                    $value = array_merge_recursive($value, $options);
                  }
                  if (!isset($result['label']))
                    $result['label'] = $row->label;
                  $pages[] = $value;
                }
              }
              continue;
            }
          } else if ($result instanceof Zend_Db_Table_Row_Abstract && $result->getTable() instanceof Core_Model_DbTable_MenuItems) {
            // We got the row (or a different row?) back ...
            $row = $result;
          } else {
            // We got a weird data type back
            continue;
          }
        }

        // No page was made, use row
        if (null === $page) {
          $page = (array) $row->params;
        }

        // Add label
        if (!isset($page['label'])) {
          $page['label'] = $row->label;
        }

        // Add custom options
        if (!empty($options)) {
          $page = array_merge_recursive($page, $options);
        }

        // Standardize arguments
        if (!isset($page['reset_params'])) {
          $page['reset_params'] = true;
        }

        // Set page as active, if necessary
        if (!isset($page['active']) && null !== $activeItem && $activeItem == $row->name) {
          $page['active'] = true;
        }

        $page['class'] = (!empty($page['class']) ? $page['class'] . ' ' : '' ) . 'menu_' . $name;
        $page['class'] .= " " . $row->name;

        // Get submenu
        if ($row->submenu) {
          $page['pages'] = $this->getMenuParams($row->submenu);
        }

        // Maintain menu item order 
        $page['order'] = $row->order;

        $pages[] = $page;
      }
    } else if (isset($params['menu']) && !empty($params['menu']) && $params['menu'] == 'core_main') {
      if (isset($params['menuType']) && !empty($row->plugin)) {
        // Support overriding the method
        if (strpos($row->plugin, '::') !== false) {
          list($pluginName, $method) = explode('::', $row->plugin);
        } else {
          $pluginName = $row->plugin;
          $method = 'onMenuInitialize_' . $this->_formatMenuName($row->name);
        }

        // Load the plugin
        try {
          $plugin = Engine_Api::_()->loadClass($pluginName);
        } catch (Exception $e) {
          // Silence exceptions
          continue;
        }

        // Run plugin
        try {
          $result = $plugin->$method($row);
        } catch (Exception $e) {
          // Silence exceptions
          continue;
        }

        if ($result === true) {
          // Just generate normally
        } else if ($result === false) {
          // Don't generate
          continue;
        } else if (is_array($result)) {
          // We got either page params or multiple page params back
          // Single
          if (array_values($result) !== $result) {
            $page = $result;
          }
          // Multi
          else {
            // We have to do this manually
            foreach ($result as $key => $value) {
              if (is_numeric($key)) {
                if (!empty($options)) {
                  $value = array_merge_recursive($value, $options);
                }
                if (!isset($result['label']))
                  $result['label'] = $row->label;
                $pages[] = $value;
              }
            }
            continue;
          }
        } else if ($result instanceof Zend_Db_Table_Row_Abstract && $result->getTable() instanceof Core_Model_DbTable_MenuItems) {
          // We got the row (or a different row?) back ...
          $row = $result;
        } else {
          // We got a weird data type back
          continue;
        }
      } else {
        $hostType = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
        $tempHostType = $tempSitemenuLtype = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemenu.global.view', 0);
        $sitemenuLtype = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemenu.lsettings', null);
        for ($check = 0; $check < strlen($hostType); $check++) {
          $tempHostType += @ord($hostType[$check]);
        }

        for ($check = 0; $check < strlen($sitemenuLtype); $check++) {
          $tempSitemenuLtype += @ord($sitemenuLtype[$check]);
        }
        $sitemenuGlobalType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemenu.global.type', null);
        
        if(!empty($sitemenuGlobalType) || (($sitemenuManageType == $tempHostType) && ($sitemenuInfoType == $tempSitemenuLtype)))
          return true;
      }
    } else
    if (isset($params['menu']) && !empty($params['menu']) && $params['menu'] == 'core_footer') {
      foreach ($menu as $row) {
        // Check enabled
        if (isset($row->enabled) && !$row->enabled) {
          continue;
        }

        // Plugin
        $page = null;
        $multi = false;
        if (!empty($row->plugin)) {

          // Support overriding the method
          if (strpos($row->plugin, '::') !== false) {
            list($pluginName, $method) = explode('::', $row->plugin);
          } else {
            $pluginName = $row->plugin;
            $method = 'onMenuInitialize_' . $this->_formatMenuName($row->name);
          }

          // Load the plugin
          try {
            $plugin = Engine_Api::_()->loadClass($pluginName);
          } catch (Exception $e) {
            // Silence exceptions
            continue;
          }

          // Run plugin
          try {
            $result = $plugin->$method($row);
          } catch (Exception $e) {
            // Silence exceptions
            continue;
          }

          if ($result === true) {
            // Just generate normally
          } else if ($result === false) {
            // Don't generate
            continue;
          } else if (is_array($result)) {
            // We got either page params or multiple page params back
            // Single
            if (array_values($result) !== $result) {
              $page = $result;
            }
            // Multi
            else {
              // We have to do this manually
              foreach ($result as $key => $value) {
                if (is_numeric($key)) {
                  if (!empty($options)) {
                    $value = array_merge_recursive($value, $options);
                  }
                  if (!isset($result['label']))
                    $result['label'] = $row->label;
                  $pages[] = $value;
                }
              }
              continue;
            }
          } else if ($result instanceof Zend_Db_Table_Row_Abstract && $result->getTable() instanceof Core_Model_DbTable_MenuItems) {
            // We got the row (or a different row?) back ...
            $row = $result;
          } else {
            // We got a weird data type back
            continue;
          }
        }

        // No page was made, use row
        if (null === $page) {
          $page = (array) $row->params;
        }

        // Add label
        if (!isset($page['label'])) {
          $page['label'] = $row->label;
        }

        // Add custom options
        if (!empty($options)) {
          $page = array_merge_recursive($page, $options);
        }

        // Standardize arguments
        if (!isset($page['reset_params'])) {
          $page['reset_params'] = true;
        }

        // Set page as active, if necessary
        if (!isset($page['active']) && null !== $activeItem && $activeItem == $row->name) {
          $page['active'] = true;
        }

        $page['class'] = (!empty($page['class']) ? $page['class'] . ' ' : '' ) . 'menu_' . $name;
        $page['class'] .= " " . $row->name;

        // Get submenu
        if ($row->submenu) {
          $page['pages'] = $this->getMenuParams($row->submenu);
        }

        // Maintain menu item order 
        $page['order'] = $row->order;

        $pages[] = $page;
      }
    }
    return;
  }

  public function getModulesPaginator($params = array()) {

    $paginator = Zend_Paginator::factory($this->getModulesSelect($params));

    if (!empty($params['page'])) {
      $paginator->setCurrentPageNumber($params['page']);
    }

    if (!empty($params['limit'])) {
      $paginator->setItemCountPerPage($params['limit']);
    }

    return $paginator;
  }

  public function getModulesSelect($params) {

    $moduleTableName = $this->info('name');
    $enabledModuleNames = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
    $select = $this->select();

    if (!empty($enabledModuleNames) && count($enabledModuleNames) > 0)
      $select->where("module_name IN (?)", array($enabledModuleNames));

    return $select;
  }

}