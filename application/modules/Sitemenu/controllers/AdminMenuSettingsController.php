<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminMenuSettingsController.php 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemenu_AdminMenuSettingsController extends Core_Controller_Action_Admin {

    public function __call($method, $params) {
        /*
         * YOU MAY DISPLAY ANY ERROR MESSAGE USING FORM OBJECT.
         * YOU MAY EXECUTE ANY SCRIPT, WHICH YOU WANT TO EXECUTE ON FORM SUBMIT.
         * REMEMBER:
         *    RETURN TRUE: IF YOU DO NOT WANT TO STOP EXECUTION.
         *    RETURN FALSE: IF YOU WANT TO STOP EXECUTION.
         */
        if (!empty($method) && $method == 'Sitemenu_Form_Admin_Global') {
            
        }
        return true;
    }

    // Admin Tab:  Menu Editor
    public function editorAction() {

        //REMOVE CACHEING OF MAIN MENU WHEN MENU EDITOR IS VISITED ON ADMIN SIDE CONTROL PANEL
        $cache = Zend_Registry::get('Zend_Cache');
        $cache->remove('footer_menu_cache');

        $levels = Engine_Api::_()->getDbtable('levels', 'authorization')->getLevelsAssoc();
        foreach ($levels as $level_id => $level_name) {
            $cache->remove('main_menu_html_for_' . $level_id);
            $cache->remove('main_menu_cache_level_' . $level_id);
            $cache->remove('browse_main_menu_cache_level_' . $level_id);
        }

        if (isset($_POST['sitemenu_lsettings']) && !empty($_POST['sitemenu_lsettings'])) {
            $_POST['sitemenu_lsettings'] = @trim($_POST['sitemenu_lsettings']);
        }

        include APPLICATION_PATH . '/application/modules/Sitemenu/controllers/license/license1.php';
    }

    // ACTION FOR CREATING A MENU ITEM

    public function createAction() {

        //GET THE MENU NAME WHICH IS BEING EDITED
        $name = $this->_getParam('name');

        //GET THE MODULE LIST THAT CAN BE CHOOSEN TO SHOW CONTENT
        $moduleArray = Engine_Api::_()->getDbtable('modules', 'sitemenu')->getContentList();

        if ($name == 'core_main') {
            // Get form
            $this->view->form = $form = new Sitemenu_Form_Admin_Menu_ItemCreate(array('moduleArray' => $moduleArray, 'isCustom' => '1'));

            // Check stuff
            if (!$this->getRequest()->isPost()) {
                return;
            }
            if (!$form->isValid($this->getRequest()->getPost())) {
                return;
            }
            // Save
            $values = $form->getValues();

            if ($values['show_in_tab'] == 1 && empty($values['icon'])) {
                $form->addError("Please enter icon as you have selected only icon.");
                return;
            }
            $label = $values['label'];
            unset($values['label']);

            // IF THE OPTION SELECTED MENU ITEM IS NOT A SUBMENU
            if ($values['is_submenu'] == 0) {
                $values['root_id'] = 0;
                $values['parent_id'] = 0;
            }

            //IF THE MENU ITEM IS NOT A SUBMENU THEN SET THE VIEW TYPE AS OF PARENT MENU
            $menuItemsTable = Engine_Api::_()->getDbtable('menuItems', 'core');
            if (!empty($values['root_id'])) {
                $params = Engine_Api::_()->getDbtable('modules', 'sitemenu')->getMenuItemColum(array('params'), $values['root_id']);

                if (!empty($params) && isset($params['menu_item_view_type'])) {
                    $params = Zend_Json::decode($params);
                    if ($params['menu_item_view_type'] == 1 || $params['menu_item_view_type'] == 2) {
                        $values['content'] = 0;
                        $values['viewby'] = 0;
                        $values['is_category'] = 0;
                        $values['category_id'] = 0;
                        $values['content_height'] = 0;
                    }
                }
            }

            if (empty($values['root_id'])) {
                if (isset($values['menu_item_view_type']) && $values['menu_item_view_type'] != 1 && $values['menu_item_view_type'] != 2) {
                    if (!is_numeric($values['content_height']) || $values['content_height'] <= 0) {
                        $form->addError("Please enter valid height. Height should be a number. ");
                        return;
                    }
                }
            }

            if (empty($values['is_sub_navigation'])) {
                $values['sub_navigation'] = 0;
            }
            unset($values['select_sub_navigation']);
            unset($values['message']);
            unset($values['noSubMenuMessage']);
            unset($values['is_submenu']);
            if (isset($values['lumious_enabled_message']))
                unset($values['lumious_enabled_message']);

            if (isset($values['data_rel'])) {
                $values['data-rel'] = $values['data_rel'];
                unset($values['data_rel']);
            }

            if (!empty($values['parent_id'])) {
                $menuItemEnabled = Engine_Api::_()->getDbtable('modules', 'sitemenu')->getMenuItemColum(array('enabled'), $values['parent_id']);
                $order = Engine_Api::_()->getDbtable('modules', 'sitemenu')->getMenuItemColum(array('order'), $values['parent_id']);
                if ($menuItemEnabled != $values['enabled']) {
                    if (empty($menuItemEnabled)) {
                        $form->addError("Please uncheck the 'Enabled?' checkbox. Parent sub menu is disabled therefore this menu cannot be enabled.");
                        return;
                    }
                }
            } elseif (!empty($values['root_id'])) {
                $menuItemEnabled = Engine_Api::_()->getDbtable('modules', 'sitemenu')->getMenuItemColum(array('enabled'), $values['root_id']);
                $order = Engine_Api::_()->getDbtable('modules', 'sitemenu')->getMenuItemColum(array('order'), $values['root_id']);
                if ($menuItemEnabled != $values['enabled']) {
                    if (empty($menuItemEnabled)) {
                        $form->addError("Please uncheck the 'Enabled?' checkbox. Parent menu is disabled therefore this menu cannot be enabled.");
                        return;
                    }
                }
            }

            $db = $menuItemsTable->getAdapter();
            $db->beginTransaction();

            try {
                include APPLICATION_PATH . '/application/modules/Sitemenu/controllers/license/license2.php';
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                $this->view->status = false;
                $this->view->error = true;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRedirect' => $this->view->url(array('module' => 'sitemenu', 'action' => 'editor', 'controller' => 'menu-settings'), 'admin_default', true),
                'format' => 'smoothbox',
                'messages' => "Your Menu Item has been created successfully."
            ));
        }
    }

    // ACTION FOR EDITING A MENU ITEM

    public function editAction() {
        //GET NAME OF THE MENU ITEM WHICH IS BEING EDITED
        $name = $this->_getParam('name');

        //GET THE NAME OF THE MENU WHOSE MENU ITEM IS BEING EDITED
        $mainMenu = $this->_getParam('mainMenu');

        if ($mainMenu == 'core_main') {
            //GET THE NUMBER OF CHILD MENUS OF THE MENU ITEM BEING EDITED
            $childCount = $this->_getParam('childCount');

            //GET THE DEPTH OF THE MENU ITEM BEING EDITED
            $this->view->depth = $info_array = $this->_getParam('info_string');

            $tempParamsFlag = null;

            //GET MENU ITEM
            $menuItemsTable = Engine_Api::_()->getDbtable('menuItems', 'core');
            $db = $menuItemsTable->getAdapter();
            $db->beginTransaction();
            try {
                $menuItemsSelect = $menuItemsTable->select()
                        ->where('name = ?', $name);

                $enabledModuleNames = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
                if (!empty($enabledModuleNames)) {
                    $menuItemsSelect->where('module IN(?)', $enabledModuleNames);
                }

                $this->view->menuItem = $menuItem = $menuItemsTable->fetchRow($menuItemsSelect);
                if (!$menuItem) {
                    throw new Core_Model_Exception('missing menu item');
                }
                $this->view->submenu_id = !empty($menuItem['params']['parent_id']) ? $menuItem['params']['parent_id'] : 0;
                if (isset($menuItem['params']['category_id'])) {
                    $this->view->category_id = !empty($menuItem['params']['category_id']) ? $menuItem['params']['category_id'] : 0;
                } else {
                    $this->view->category_id = 0;
                }

                //ARRAY OF POPULARITY CRITERIA SELECTED
                $viewbyArray = 0;
                if (!empty($menuItem['params']['viewby'])) {
                    $this->view->viewby_value = $viewbyArray = $menuItem['params']['viewby'];
                }

                //ARRAY OF NAVIGATION MENU TO BE SHOWN IF ANY
                $navigationArray = !empty($menuItem['params']['sub_navigation']) ? unserialize($menuItem['params']['sub_navigation']) : 0;

                //ARRAY OF MODULES WHOSE CONTENT CAN BE SHOWN
                $moduleArray = Engine_Api::_()->getDbtable('modules', 'sitemenu')->getContentList();

                // GETTING STANDARD NAVIGATION MENUS OBJECT FROM THE MENUITEMS TABLE
                $navMenu = null;
                if (!empty($menuItem->module) && $menuItem->module != 'core') {

                    //FOR FINIDING THE NAVIGATION MENUS
                    if (strstr($menuItem->module, "sitereview")) {
                        $sitereviewMenuNameArray = explode("core_main_sitereview_listtype_", $menuItem->name);
                        $listingtypeId = $sitereviewMenuNameArray[1];
                        $navMenu = 'sitereview_main_listtype_' . $listingtypeId;
                    } else {
                        $navMenu = $menuItem->module . "_main";
                    }

                    $menuItemsSelect = $menuItemsTable->select()
                            ->from('engine4_core_menuitems', array('id', 'label'))
                            ->where('menu = ?', $navMenu);

                    if (!empty($enabledModuleNames)) {
                        $menuItemsSelect->where('module IN(?)', $enabledModuleNames);
                    }
                    $navigationObj = $menuItemsTable->fetchAll($menuItemsSelect);
                    $this->view->countStandardNavigation = count($navigationObj);
                } else {
                    $navigationObj = array();
                }

                // GET EDIT FORM OF MAIN MENU
                $this->view->form = $form = new Sitemenu_Form_Admin_Menu_ItemEdit(array('menuItem' => $menuItem->id, 'childCount' => $childCount, 'info_array' => $info_array, 'moduleArray' => $moduleArray, 'navigationObj' => $navigationObj, 'navigationArray' => $navigationArray, 'isCustom' => $menuItem->custom));

                //GET THE PARENT MENU ITEMS TO CHECK THE PARENT VIEW TYPE
                $parentMenuItemParams = Engine_Api::_()->getDbtable('modules', 'sitemenu')->getMenuItemColum(array('params'), $menuItem->id);
                if (!empty($parentMenuItemParams)) {
                    $parentMenuItemParams = Zend_Json::decode($parentMenuItemParams);
                }
                if (isset($parentMenuItemParams['menu_item_view_type']) && !empty($parentMenuItemParams['menu_item_view_type'])) {
                    $form->menu_item_view_type->setValue($parentMenuItemParams['menu_item_view_type']);
                } else {
                    $form->menu_item_view_type->setValue(1);
                }
                $this->view->parent_view_type = isset($parentMenuItemParams['menu_item_view_type']) ? $parentMenuItemParams['menu_item_view_type'] : 1;

                // Make safe
                $menuItemData = $menuItem->toArray();
                if (isset($menuItemData['params']) && is_array($menuItemData['params'])) {
                    $menuItemData = array_merge($menuItemData, $menuItemData['params']);
                }
                if (!$menuItem->custom && (!isset($menuItemData['uri']) || $menuItemData['uri'] == 'Separator')) {
                    $form->removeElement('uri');
                }

                $tempParamsFlag = $menuItemData['params'];
                unset($menuItemData['params']);

                if (isset($menuItemData['data-rel'])) {
                    $menuItemData['data_rel'] = $menuItemData['data-rel'];
                    unset($menuItemData['data-rel']);
                }
                if (empty($menuItemData['root_id'])) {
                    $menuItemData['is_submenu'] = 0;
                } else {
                    $menuItemData['is_submenu'] = 1;
                }

                $previous_root_id = isset($menuItemData['root_id']) ? $menuItemData['root_id'] : 0;
                $previous_parent_id = isset($menuItemData['parent_id']) ? $menuItemData['parent_id'] : 0;

                // Check stuff
                if (!$this->getRequest()->isPost()) {
                    $menuItemData['viewby'] = $viewbyArray;
                    $form->populate($menuItemData);
                    return;
                }
                if (!$form->isValid($this->getRequest()->getPost())) {
                    return;
                }

                // Save
                $values = $form->getValues();
                $menuItem->label = $values['label'];

                if ($values['show_in_tab'] == 1 && empty($values['icon'])) {
                    $form->addError("Please enter icon as you have selected only icon.");
                    return;
                }

                $values['nav_menu'] = $navMenu;
                if (empty($values['is_sub_navigation'])) {
                    $values['sub_navigation'] = 0;
                } else {
                    $values['sub_navigation'] = serialize($values['select_sub_navigation']);
                }

                if (empty($values['is_submenu'])) {
                    $values['root_id'] = 0;
                    $values['parent_id'] = 0;
                }

                if (!empty($values['root_id'])) {
                    $params = Engine_Api::_()->getDbtable('modules', 'sitemenu')->getMenuItemColum(array('params'), $values['root_id']);
                    if (!empty($params) && isset($params['menu_item_view_type'])) {
                        $params = Zend_Json::decode($params);
                        if ($params['menu_item_view_type'] == 1 || $params['menu_item_view_type'] == 2) {
                            $values['content'] = 0;
                            $values['viewby'] = 0;
                            $values['is_category'] = 0;
                            $values['category_id'] = 0;
                            $values['content_height'] = 0;
                        }
                    }
                }

                if (empty($values['root_id'])) {
                    if (isset($values['menu_item_view_type']) && $values['menu_item_view_type'] != 1 && $values['menu_item_view_type'] != 2) {
                        if (!is_numeric($values['content_height']) || $values['content_height'] <= 0) {
                            $form->addError("Please enter valid height. Height should be a number. ");
                            return;
                        }
                    }
                }

                if ((isset($values['parent_id']) && $values['parent_id'] != $previous_parent_id) || (isset($values['root_id']) && $values['root_id'] != $previous_root_id)) {

                    //SET THE MENU ITEM ORDER ACCORDING TO THE HIERARCHY
                    if (!empty($values['parent_id'])) {
                        $menuItemOrder = Engine_Api::_()->getDbtable('modules', 'sitemenu')->getMenuItemColum(array('order'), $values['parent_id']);
                    } elseif (!empty($values['root_id'])) {
                        $menuItemOrder = Engine_Api::_()->getDbtable('modules', 'sitemenu')->getMenuItemColum(array('order'), $values['root_id']);
                    }
                    if (!empty($menuItemOrder)) {
                        $menuItemOrder = ($menuItemOrder * 10) + 1;
                    }
                }

                //UNSET ALL THE INDEX THAT ARE NOT TO BE SAVED IN DATABASE
                if (isset($values['label']))
                    unset($values['label']);
                if (isset($values['select_sub_navigation']))
                    unset($values['select_sub_navigation']);
                if (isset($values['is_sub_navigation']))
                    unset($values['is_sub_navigation']);
                if (isset($values['message']))
                    unset($values['message']);
                if (isset($values['noSubMenuMessage']))
                    unset($values['noSubMenuMessage']);
                if (isset($values['is_submenu']))
                    unset($values['is_submenu']);

                // REARRANGE THE MENU ARRAY IF A PARENT MENU ITEM IS MADE SUB MENU OF ANOTHER MENU ITEM 
                $menuArray = Engine_Api::_()->sitemenu()->getMainMenuArray($mainMenu);
                if (!empty($menuItemData['root_id']) && empty($menuItemData['parent_id'])) {
                    if (isset($menuArray[$menuItemData['root_id']][$menuItem->id]['info']))
                        unset($menuArray[$menuItemData['root_id']][$menuItem->id]['info']);

                    if (isset($menuArray[$menuItemData['root_id']][$menuItem->id])) {
                        foreach ($menuArray[$menuItemData['root_id']][$menuItem->id] as $child) {
                            $childItemSelect = $menuItemsTable->select()
                                    ->where('id = ?', $child['info']['menuObj']->id);
                            $childItem = $menuItemsTable->fetchRow($childItemSelect);
                            $childItemData = $childItem->toArray();
                            if (!empty($values['root_id'])) {
                                $childItemData['params']['root_id'] = $values['root_id'];
                            } else {
                                $childItemData['params']['root_id'] = $menuItem['id'];
                                $childItemData['params']['parent_id'] = 0;
                            }
                            $childItem->params = $childItemData['params'];
                            $childItem->save();
                        }
                    }
                } else {
                    // REARRANGE THE MENU ARRAY IF A PARENT MENU ITEM IS MADE ROOT MENU
                    if (empty($menuItemData['root_id']) && empty($menuItemData['parent_id']) && !empty($values['root_id'])) {
                        if (isset($menuArray[$menuItem->id]['info']))
                            unset($menuArray[$menuItem->id]['info']);
                        if (isset($menuArray[$menuItem->id])) {
                            foreach ($menuArray[$menuItem->id] as $child) {
                                $childItemSelect = $menuItemsTable->select()
                                        ->where('id = ?', $child['info']['menuObj']->id);
                                $childItem = $menuItemsTable->fetchRow($childItemSelect);
                                $childItemData = $childItem->toArray();
                                $childItemData['params']['root_id'] = $values['root_id'];
                                $childItemData['params']['parent_id'] = $menuItem['id'];
                                $childItem->params = $childItemData['params'];
                                $childItem->save();
                            }
                        }
                    }
                }

                if (empty($values['data_rel']) && isset($tempParamsFlag['data-rel'])) {
                    // Remove the target
                    $tempParams = array();
                    foreach ($tempParamsFlag as $key => $item) {
                        if ($key != 'data-rel') {
                            $tempParams[$key] = $item;
                        }
                    }
                }
                $tempParamsFlag = !empty($tempParams) ? $tempParams : $tempParamsFlag;
                if (is_array($tempParamsFlag))
                    $values = array_merge($tempParamsFlag, $values);
                $menuItem->params = $values;

                //IF USER TRIES TO MAKE MENU ITEM A SUB MENU OF DISABLED MENU ITEM
                if (!empty($values['parent_id'])) {
                    $menuItemEnabled = Engine_Api::_()->getDbtable('modules', 'sitemenu')->getMenuItemColum(array('enabled'), $values['parent_id']);
                    if ($menuItemEnabled != $values['enabled']) {
                        if (empty($menuItemEnabled)) {
                            $form->addError("Please uncheck the 'Enabled?' checkbox. Parent sub menu is disabled therefore this menu cannot be enabled.");
                            return;
                        }
                    }
                } elseif (!empty($values['root_id'])) {
                    $menuItemEnabled = Engine_Api::_()->getDbtable('modules', 'sitemenu')->getMenuItemColum(array('enabled'), $values['root_id']);
                    if ($menuItemEnabled != $values['enabled']) {
                        if (empty($menuItemEnabled)) {
                            $form->addError("Please uncheck the 'Enabled?' checkbox. Parent menu is disabled therefore this menu cannot be enabled.");
                            return;
                        }
                    }
                }
                $menuItem->enabled = $values['enabled'];
                Engine_Api::_()->sitemenu()->toggleSubMenuStatus($menuItem->id, $values['enabled']);

                include APPLICATION_PATH . '/application/modules/Sitemenu/controllers/license/license2.php';
                $db->commit();

                //REDIRECT PAGE TO MENU EDITOR
                $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => true,
                    'parentRedirect' => $this->view->url(array('module' => 'sitemenu', 'action' => 'editor', 'controller' => 'menu-settings'), 'admin_default', true),
                    'format' => 'smoothbox',
                    'messages' => "Settings saved successfully."
                ));
            } catch (Exception $e) {
                $db->rollBack();
            }
        }
    }

    // ACTION FOR DELETING A MENU ITEM
    public function deleteAction() {
        //GET MENU ITEM NAME TO BE DELETED
        $name = $this->_getParam('name');
        //GET THE DEPTH OF THE MENU ITEM TO BE DELETE
        $menuItemDepth = $this->_getParam('info_string');

        // Get menu item
        $menuItemsTable = Engine_Api::_()->getDbtable('menuItems', 'core');
        $db = $menuItemsTable->getAdapter();
        $db->beginTransaction();
        try {
            $menuItemsSelect = $menuItemsTable->select()
                    ->where('name = ?', $name)
                    ->order('order ASC');

            $enabledModuleNames = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
            if (!empty($enabledModuleNames)) {
                $menuItemsSelect->where('module IN(?)', $enabledModuleNames);
            }

            $menuItem = $menuItemsTable->fetchRow($menuItemsSelect);
            if (!$menuItem || !$menuItem->custom) {
                throw new Core_Model_Exception('missing menu item');
            }

            if ($menuItemDepth < 2) {
                // Get DELETE FORM
                $this->view->canDeleteMenu = true;

                // Check stuff
                if (!$this->getRequest()->isPost()) {
                    return;
                }

                $menuItem->delete();
                $db->commit();

                $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => true,
                    'parentRedirect' => $this->view->url(array('module' => 'sitemenu', 'action' => 'editor', 'controller' => 'menu-settings'), 'admin_default', true),
                    'format' => 'smoothbox',
                    'messages' => "Your Menu Item has been deleted successfully."
                ));
            }
        } catch (Exception $e) {
            $db->rollBack();
        }
    }

    // ACTION FOR ORDERING THE MENUS, SUB MENUS AND SUB SUB MENUS ON MENU SETTINGS TAB
    public function orderAction() {

        if (!$this->getRequest()->isPost()) {
            return;
        }

        $table = Engine_Api::_()->getDbtable('menuItems', 'core');
        $menuitemsObj = $table->fetchAll($table->select()->where('menu = ?', $this->getRequest()->getParam('menu')));
        $tempMenuArray = array();
        foreach ($menuitemsObj as $menuitemObj) {
            $tempMenuArray[$menuitemObj->id] = $menuitemObj;
        }

        include APPLICATION_PATH . '/application/modules/Sitemenu/controllers/license/license2.php';

        if (!empty($isMenuEnabled)) {
            foreach ($menuitemsObj as $menuTab) {
                $order = $this->getRequest()->getParam('admin_menus_item_' . $menuTab->name, 0);
                if (!empty($order)) {
                    $params = $menuTab->params;
                    if (!empty($params) && !empty($params['root_id']) && !empty($params['parent_id'])) {
                        $root_item_obj = $tempMenuArray[$params['root_id']];
                        $parent_item_obj = $tempMenuArray[$params['parent_id']];
                        $order = $root_item_obj->order . $parent_item_obj->order . $order;
                    } elseif (!empty($params) && !empty($params['root_id']) && empty($params['parent_id'])) {
                        $root_item_obj = $tempMenuArray[$params['root_id']];
                        $order = $root_item_obj->order . $order;
                    }
                }
                if (!empty($order)) {
                    $menuTab->order = $order;
                }
                $menuTab->save();
            }
        }
        return;
    }

    // Ajax: ACTION FOR GETTING SUB MENUS OF A MENU IN CREATE AND EDIT FORM
    public function getSubMenusAction() {
        $parent_id = $this->_getParam('parent_id');
        $main_tab_id = $this->_getParam('main_tab_id', null);

        $getEnabledModuleNames = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
        $menuItemsTable = Engine_Api::_()->getDbtable('menuItems', 'core');
        $menuItemsSelect = $menuItemsTable->select()
                ->from($menuItemsTable->info('name'), array('id', 'label', 'params'))
                ->where("menu = 'core_main'");

        if (!empty($getEnabledModuleNames)) {
            $menuItemsSelect->where('module IN(?)', $getEnabledModuleNames);
        }

        $menuItems = $menuItemsTable->fetchAll($menuItemsSelect);
        $data = $arr_data = array();

        foreach ($menuItems as $menuItem) {
            $menuItemParams = $menuItem->params;

            if ($parent_id == $menuItem->id) {
                if (isset($menuItemParams['menu_item_view_type'])) {
                    $this->view->parentViewType = $menuItemParams['menu_item_view_type'];
                } else {
                    $this->view->parentViewType = 1;
                }
            }
            if (!empty($menuItemParams['root_id'])) {
                if ($menuItemParams['root_id'] == $parent_id && empty($menuItemParams['parent_id']) && ($menuItem->id != $main_tab_id)) {
                    $data['id'] = $menuItem->id;
                    $data['label'] = $menuItem->label;
                    $arr_data[] = $data;
                }
            }
        }

        $this->view->submenus = $arr_data;
    }

    // Ajax: ACTION FOR GETTING VIEW BY OPTIONS IN CREATE AND EDIT FORM
    public function getOptionListAction() {
        $module_id = $this->_getParam('moduleId');

        $fetch_column_array = array("item_type", "like_field", "comment_field", "date_field", "featured_field", "sponsored_field", "category_name", "category_title_field");
        $menus = Engine_Api::_()->getDbtable('modules', 'sitemenu')->getModuleAttribute($fetch_column_array, $module_id);

        $data = array();

        if (!empty($menus['like_field'])) {
            $data['1'] = "Most Liked";
        }
        if (!empty($menus['comment_field'])) {
            $data['2'] = "Most commented";
        }
        if (!empty($menus['date_field'])) {
            $data['3'] = "Most recent";
        }
        if (!empty($menus['featured_field'])) {
            $data['4'] = "Featured";
        }
        if (!empty($menus['sponsored_field'])) {
            $data['5'] = "Sponsored";
        }

        if (!empty($data)) {
            $this->view->modules = $data;
        } else {
            $this->view->modules = 'null';
        }

        if (!empty($menus['category_name'])) {
            $this->view->is_category = 1;

            if (!empty($menus['category_name']) && !empty($menus['category_title_field'])) {
                $category_title_field = $menus['category_title_field'];

                //WORK FOR SHOWING CATEGORY DROPDOWN      
                $category_table = Engine_Api::_()->getItemTable($menus['category_name']);
                if (!empty($category_table)) {
                    $category_table_name = $category_table->info('name');
                    $categorySelect = $category_table->select()
                            ->from($category_table_name, array('category_id', $category_title_field . ' As category_name'))
                            ->where('category_id != ?', 0);

                    //WORK FOR SEPARATING SUB CATEGORY IF EXIST FROM THE CATEGORY ARRAY
                    $db = Engine_Db_Table::getDefaultAdapter();
                    $column_cat_exist = $db->query('SHOW COLUMNS FROM ' . $category_table_name . ' LIKE \'cat_dependency\'')->fetch();
                    if (!empty($column_cat_exist)) {
                        $categorySelect->where('cat_dependency = ?', 0);
                    }

                    $column_sub_cat_exist = $db->query('SHOW COLUMNS FROM ' . $category_table_name . ' LIKE \'subcat_dependency\'')->fetch();
                    if (!empty($column_sub_cat_exist)) {
                        $categorySelect->where('subcat_dependency = ?', 0);
                    }

                    if (strstr($menus['item_type'], "sitereview")) {
                        $sitereviewTableName = explode("sitereview_listing_", $menus['item_type']);
                        $listingtypeId = $sitereviewTableName[1];
                        if (!empty($listingtypeId))
                            $categorySelect->where('listingtype_id = ?', $listingtypeId);
                    }


                    $categoryArray = $categorySelect->query()->fetchAll();
                }
                if (!empty($categoryArray)) {
                    $this->view->categoryArray = $categoryArray;
                } else {
                    $this->view->categoryArray = 0;
                }
            }
        } else {
            if (!empty($menus['item_type']))
                switch ($menus['item_type']) {
                    case 'video':
                        $this->view->is_category = 1;
                        $categoryTable = Engine_Api::_()->getDbtable('categories', 'video');
                        $category_select = $categoryTable->select()
                                ->from($categoryTable->info('name'), array('category_id', 'category_name'))
                                ->where('category_id != ?', 0);
                        $categoryArray = $category_select->query()->fetchAll();

                        break;
                    case 'classified':
                        $this->view->is_category = 1;
                        $categoryTable = Engine_Api::_()->getDbtable('categories', 'classified');
                        $category_select = $categoryTable->select()
                                ->from($categoryTable->info('name'), array('category_id', 'category_name'))
                                ->where('category_id != ?', 0);
                        $categoryArray = $category_select->query()->fetchAll();

                        break;
                    case 'sitepagedocument_document':
                        $this->view->is_category = 1;
                        $categoryTable = Engine_Api::_()->getDbtable('categories', 'sitepagedocument');
                        $category_select = $categoryTable->select()
                                ->from($categoryTable->info('name'), array('category_id', 'title As category_name'))
                                ->where('category_id != ?', 0);
                        $categoryArray = $category_select->query()->fetchAll();

                        break;
                    case 'sitepageevent_event':
                        $this->view->is_category = 1;
                        $categoryTable = Engine_Api::_()->getDbtable('categories', 'sitepageevent');
                        $category_select = $categoryTable->select()
                                ->from($categoryTable->info('name'), array('category_id', 'title As category_name'))
                                ->where('category_id != ?', 0);
                        $categoryArray = $category_select->query()->fetchAll();

                        break;
                    case 'sitepagenote_note':
                        $this->view->is_category = 1;
                        $categoryTable = Engine_Api::_()->getDbtable('categories', 'sitepagenote');
                        $category_select = $categoryTable->select()
                                ->from($categoryTable->info('name'), array('category_id', 'title As category_name'))
                                ->where('category_id != ?', 0);
                        $categoryArray = $category_select->query()->fetchAll();

                        break;

                    default:
                        $categoryArray = 0;
                        break;
                }
            if (!empty($categoryArray)) {
                $this->view->categoryArray = $categoryArray;
            } else {
                $this->view->categoryArray = 0;
            }
        }
    }

}
