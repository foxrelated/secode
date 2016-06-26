<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemenu_Api_Core extends Core_Api_Abstract {

    /**
     * Returns an customized sql query.
     *
     * @params array column name with value
     * @return string
     */
    public function getMenusQuery($params) {
        $getEnabledModuleNames = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
        $menuItemsTable = Engine_Api::_()->getDbtable('menuItems', 'core');
        $menuItemsSelect = $menuItemsTable->select()->order('order');

        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $menuItemsSelect->where($key . ' = ?', $value);
            }
        }

        if (!empty($getEnabledModuleNames)) {
            $menuItemsSelect->where('module IN(?)', $getEnabledModuleNames);
        }
        return $menuItemsSelect;
    }

    /**
     * Returns object of fetched menus from core.
     *
     * @params array column name with value
     * @return object
     */
    public function getMenuObject($params = array('menu' => 'core_main')) {
        $selectQuery = $this->getMenusQuery($params);
        return Engine_Api::_()->getDbtable('menuItems', 'core')->fetchAll($selectQuery);
    }

    /**
     * Returns an array of menu items with all sub tabs.
     *
     * @menuName string menu name.
     * @return array
     * Important Note:- This functions returns an array of menus. All the menus are present in array but the info part of that menu which is not displayable is not present. If info part of that menu is accessed anywhere it would give a notice, therefore needs a check for empty of info part anywhere it is used.
     */
    public function getMainMenuArray($menuName) {
        $data = array();
        $menus = Engine_Api::_()->getApi('menus', 'core')->getNavigation($menuName);

        $tempSitemenuLtype = $tempHostType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemenu.global.view', 0);
        foreach ($menus as $menu) {
            $getTabNameArray = explode(" ", $menu->getClass());

            // CHECK TYPE THAT CONTENT AVAILABLE OR NOT FOR THIS ROW.
            $isModuleTypeAvailable = Engine_Api::_()->getDbtable('modules', 'sitemenu')->isModuleTypeAvailable($menu, array('menu' => 'core_main'));
            if (empty($isModuleTypeAvailable))
                continue;

            $selectQuery = $this->getMenusQuery(array('name' => end($getTabNameArray), 'menu' => 'core_main'));
            $getTabNameObj = Engine_Api::_()->getDbtable('menuItems', 'core')->fetchRow($selectQuery);

            if (!empty($getTabNameObj)) {
//                continue;
                $params = $getTabNameObj->params;

                $root_id = !empty($params['root_id']) ? $params['root_id'] : 0;
                $parent_id = !empty($params['parent_id']) ? $params['parent_id'] : 0;

                if (empty($root_id) && empty($parent_id)) {
                    $data[$getTabNameObj->id]['info'] = array('menuObj' => $getTabNameObj, 'zendObj' => $menu);
                } else if (!empty($root_id) && empty($parent_id)) {
                    $data[$root_id][$getTabNameObj->id]['info'] = array('menuObj' => $getTabNameObj, 'zendObj' => $menu);
                } else if (!empty($parent_id) && !empty($root_id)) {
                    $data[$root_id][$parent_id][$getTabNameObj->id]['info'] = array('menuObj' => $getTabNameObj, 'zendObj' => $menu);
                }
            }
        }
        return $data;
    }

    public function markMessageReadUnread($conversation_id, $is_read = false) {
        if (empty($is_read))
            Engine_Api::_()->getDbtable('recipients', 'messages')->update(array('inbox_read' => 0), array('conversation_id =?' => $conversation_id));
        else
            Engine_Api::_()->getDbtable('recipients', 'messages')->update(array('inbox_read' => 1), array('conversation_id =?' => $conversation_id));
    }

    /**
     * Returns an array of menu items with all sub tabs for admin side.
     *
     * @return array
     * Important Note:- This functions returns an array of menus. All the menus are present in array but the info part of that menu which is not displayable is not present. If info part of that menu is accessed anywhere it would give a notice, therefore needs a check for empty of info part anywhere it is used.
     */
    public function getMenuEditorArray() {
        $data = array();

        $getEnabledModuleNames = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
        // Get menu items
        $menuItemsTable = Engine_Api::_()->getDbtable('menuItems', 'core');
        $menuItemsSelect = $menuItemsTable->select()
                ->where("menu = 'core_main'")
                ->order('order');
        if (!empty($getEnabledModuleNames)) {
            $menuItemsSelect->where('module IN(?)', $getEnabledModuleNames);
        }
        $menus = $menuItemsTable->fetchAll($menuItemsSelect);

        foreach ($menus as $getTabNameObj) {
            $params = $getTabNameObj->params;
            $root_id = !empty($params['root_id']) ? $params['root_id'] : 0;
            $parent_id = !empty($params['parent_id']) ? $params['parent_id'] : 0;

            if (empty($root_id) && empty($parent_id)) {
                $data[$getTabNameObj->id]['info'] = array('menuObj' => $getTabNameObj);
            } else if (!empty($root_id) && empty($parent_id)) {
                $data[$root_id][$getTabNameObj->id]['info'] = array('menuObj' => $getTabNameObj);
            } else if (!empty($parent_id) && !empty($root_id)) {
                $data[$root_id][$parent_id][$getTabNameObj->id]['info'] = array('menuObj' => $getTabNameObj);
            }
        }
        return $data;
    }

    /**
     * Toggles the enabled column for the given menu item id.
     *
     * @menu_id int id.
     */
    public function toggleSubMenuStatus($menu_id, $status) {

        $menuItemsTable = Engine_Api::_()->getDbtable('menuItems', 'core');
        $menuItemsSelect = $menuItemsTable->select()
                ->from($menuItemsTable->info('name'), array('params', 'id', 'enabled'))
                ->where("menu = 'core_main'")
                ->order('order');
        $getEnabledModuleNames = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
        if (!empty($getEnabledModuleNames)) {
            $menuItemsSelect->where('module IN(?)', $getEnabledModuleNames);
        }
        $coreMainMenus = $menuItemsTable->fetchAll($menuItemsSelect);

        foreach ($coreMainMenus as $menus) {
            if (isset($menus->params['root_id']) || isset($menus->params['parent_id'])) {
                if ((isset($menus->params['parent_id']) && $menus->params['parent_id'] == $menu_id) || (isset($menus->params['root_id']) && $menus->params['root_id'] == $menu_id )) {
                    $menuItemsTable->update(array('enabled' => $status), array('id = ?' => $menus->id));
                }
            }
        }
    }

    public function isPaidListingPackageEnabled($tempMenuClassArray = array()) {
        $PackageCount = 0;
        if (!empty($tempMenuClassArray) && is_array($tempMenuClassArray) && !empty($tempMenuClassArray[1]) && strstr($tempMenuClassArray[1], 'sitereview_main_create_listtype_')) {
            $explodedRoute = explode('sitereview_main_create_listtype_', $tempMenuClassArray[1]);
            $tabs_listingtype_id = end($explodedRoute);
            if (Engine_Api::_()->sitereview()->hasPackageEnable()) {
                $PackageCount = Engine_Api::_()->getDbTable('packages', 'sitereviewpaidlisting')->getPackageCount($tabs_listingtype_id);
                if ($PackageCount > 0)
                    return $tabs_listingtype_id;
            }
        }
        return false;
    }

    public function isEventPackageEnabled($tempMenuClassArray = array()) {
        if (!empty($tempMenuClassArray) && is_array($tempMenuClassArray) && !empty($tempMenuClassArray[1]) && strstr($tempMenuClassArray[1], 'siteevent_main_create')) {

            if (Engine_Api::_()->siteevent()->hasPackageEnable()) {
                return true;
            }
        }
        return false;
    }

    public function getCachedMenus($menuName) {
        $cache = Zend_Registry::get('Zend_Cache');

        if ($menuName == 'core_footer') {
            $cacheName = 'footer_menu_cache';
            $data = $cache->load($cacheName);
            if (!empty($data)) {
                return $data;
            } else {
                $data = Engine_Api::_()->getApi('menus', 'core')->getNavigation('core_footer');
                $cache->setLifetime(Engine_Api::_()->sitemenu()->cacheLifeInSec());
                $cache->save($data, $cacheName);
                return $data;
            }
        }

        if ($menuName == 'core_main') {
            $viewer = Engine_Api::_()->user()->getViewer();
            $viewer_id = $viewer->getIdentity();
            if (!empty($viewer_id)) {
                $viewer_level_id = Engine_Api::_()->user()->getViewer()->level_id;
            } else {
                $viewer_level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
            }
            $cacheName = 'main_menu_cache_level_' . $viewer_level_id;
            $data = $cache->load($cacheName);
            if (!empty($data)) {
                return $data;
            } else {
                $data = $this->getMainMenuArray('core_main');
                $cache->setLifetime(Engine_Api::_()->sitemenu()->cacheLifeInSec());
                $cache->save($data, $cacheName);
                return $data;
            }
        }
    }

    //FUNCTION TO RETURNS SECONDS CONVERTED FROM DAYS
    public function cacheLifeInSec() {
        return $cacheTimeSec = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemenu.cache.lifetime', 7) * 86400;
    }
    
    public function isCurrentTheme($currentTheme = ''){
        
        if(empty($currentTheme))
            return false;
        
        //Start work for responsive theme/media query
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $theme = '';
        $themeArray = $view->layout()->themes;
        if (isset($themeArray[0])) {
            $theme = $view->layout()->themes[0];
        }

        if ($theme == $currentTheme) {
            return true;
        }
        
        return false;
    }

    /**
     * Get Widgetized PageId
     * @param $params
     */
    public function getWidgetizedPageId($params = array()) {
        //GET CORE CONTENT TABLE
        $tableNamePages = Engine_Api::_()->getDbtable('pages', 'core');
        $page_id = $tableNamePages->select()
                ->from($tableNamePages->info('name'), 'page_id')
                ->where('name =?', $params['name'])
                ->query()
                ->fetchColumn();
        return $page_id;
    }

}
