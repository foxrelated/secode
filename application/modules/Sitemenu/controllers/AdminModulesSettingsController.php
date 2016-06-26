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

class Sitemenu_AdminModulesSettingsController extends Core_Controller_Action_Admin {

    public function indexAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitemenu_admin_main', array(), 'sitemenu_admin_main_manage');

        $this->view->enabled_modules_array = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
        $page = $this->_getParam('page', 1); // Page id: Controll pagination.
        include APPLICATION_PATH . '/application/modules/Sitemenu/controllers/license/license2.php';
        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            foreach ($values as $key => $value) {
                if ($key == 'delete_' . $value) {
                    $obj = Engine_Api::_()->getItem('sitemenu_module', $value);
                    if (empty($obj->is_delete)) {
                        $obj->delete();
                    }
                }
            }
        }
    }

    // Function: Manage Module - Creation Tab.
    public function moduleCreateAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitemenu_admin_main', array(), 'sitemenu_admin_main_manage');

        $module_table = Engine_Api::_()->getDbTable('modules', 'sitemenu');
        $this->view->modules_id = $module_id = $this->_getParam('module_id', 0);
        $this->view->form = $form = new Sitemenu_Form_Admin_Module(array('moduleId' => $module_id));
        $this->view->module_form_count = @count($form->module_name->options);
        $temp_error_array = array();


        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            $db = Zend_Db_Table_Abstract::getDefaultAdapter();
            if (!empty($values) && !empty($values['module_name'])) {
                $moduleName = $values['module_name'];
                $moduleTitle = isset($values['module_title']) ? $values['module_title'] : null;
                $image_option = isset($values['image_option']) ? $values['image_option'] : 1;
                $itemType = isset($values['item_type']) ? $values['item_type'] : null;
                $itemCategory = !empty ($values['category_option']) ? $values['category_name'] : null ;
                $category_title_field = (!empty ($values['category_option']) && isset($values['category_title_field'])) ? $values['category_title_field'] : null ;

                if (strstr($itemType, "sitereview")) {
                    $itemType = "sitereview_listing";
                }

                $field_title = $values['table_title'];
                $field_body = $values['table_body'];
                $field_owner = $values['table_owner'];

                if (!empty($itemType)) {
                    $hasItemType = Engine_Api::_()->hasItemType($itemType);
                }
                if (!empty($hasItemType)) {
                    $table_name = Engine_Api::_()->getItemTable($itemType)->info('name');

                    // Condition: Check owner field is available or not in given table.
                    if (!empty($field_owner)) {
                        $is_owner = $db->query("SHOW COLUMNS FROM " . $table_name . " LIKE '" . $field_owner . "'")->fetch();
                        if (empty($is_owner)) {
                            $temp_error_array[] = 'Please check the Content Owner Field. A field matching the one specified by you could not be found in the database table.';
                        }
                    } 

                    // Condition: Check title field is available or not in given table.
                    if (!empty($field_title)) {
                        $is_title = $db->query("SHOW COLUMNS FROM " . $table_name . " LIKE '" . $field_title . "'")->fetch();
                        if (empty($is_title)) {
                            $temp_error_array[] = 'Please check the Content Title Field. A field matching the one specified by you could not be found in the database table.';
                        }
                    }

                    // Condition: Check body/description field is available or not in given table.
                    if (!empty($field_body)) {
                        $is_body = $db->query("SHOW COLUMNS FROM " . $table_name . " LIKE '" . $field_body . "'")->fetch();
                        if (empty($is_body)) {
                            $temp_error_array[] = 'Please check the Content Body/Description Field. A field matching the one specified by you could not be found in the database table.';
                        }
                    }

                    // Condition: Check like field is available or not in given table.
                    if (!empty($values['like_field'])) {
                        $is_like = $db->query("SHOW COLUMNS FROM " . $table_name . " LIKE '" . $values['like_field'] . "'")->fetch();
                        if (empty($is_like)) {
                            $temp_error_array[] = 'Please check the Content like Field. A field matching the one specified by you could not be found in the database table.';
                        }
                    }

                    //Condition: Check comment field is available or not in given table.
                    if (!empty($values['comment_field'])) {
                        $is_comment = $db->query("SHOW COLUMNS FROM " . $table_name . " LIKE '" . $values['comment_field'] . "'")->fetch();
                        if (empty($is_comment)) {
                            $temp_error_array[] = 'Please check the Content comment Field. A field matching the one specified by you could not be found in the database table.';
                        }
                    }

                    //Condition: Check creation date field is available or not in given table.
                    if (!empty($values['date_field'])) {
                        $is_date = $db->query("SHOW COLUMNS FROM " . $table_name . " LIKE '" . $values['date_field'] . "'")->fetch();
                        if (empty($is_date)) {
                            $temp_error_array[] = 'Please check the Content creation date Field. A field matching the one specified by you could not be found in the database table.';
                        }
                    }

                    //Condition: Check featured field is available or not in given table.
                    if (!empty($values['featured_field'])) {
                        $is_featured = $db->query("SHOW COLUMNS FROM " . $table_name . " LIKE '" . $values['featured_field'] . "'")->fetch();
                        if (empty($is_featured)) {
                            $temp_error_array[] = 'Please check the Content featured coloum Field. A field matching the one specified by you could not be found in the database table.';
                        }
                    }

                    //Condition: Check sponsored field is available or not in given table.
                    if (!empty($values['sponsored_field'])) {
                        $is_sponsored = $db->query("SHOW COLUMNS FROM " . $table_name . " LIKE '" . $values['sponsored_field'] . "'")->fetch();
                        if (empty($is_sponsored)) {
                            $temp_error_array[] = 'Please check the Content sponsored Field. A field matching the one specified by you could not be found in the database table.';
                        }
                    }

                    if (!empty($itemCategory) && !empty($values['category_option'])) {
                      $hasItemCategory = Engine_Api::_()->hasItemType($itemCategory);

                      if (empty($hasItemCategory)) {
                          $temp_error_array[] = 'Please enter a correct database category table item.';
                      }
                      if(!empty($hasItemCategory)){
                        $category_table_name = Engine_Api::_()->getItemTable($itemCategory)->info('name');
                        if (!empty($values['category_title_field'])) {
                          $is_category_title_field = $db->query("SHOW COLUMNS FROM " . $category_table_name . " LIKE '" . $values['category_title_field'] . "'")->fetch();
                          if (empty($is_category_title_field)) {
                              $temp_error_array[] = 'Please check the Category Title Field. A field matching the one specified by you could not be found in the database category table.';
                          }
                        }
                      }
                    }
                    
                    if (!empty($temp_error_array)) {
                      foreach ($temp_error_array as $error_message) {
                        $form->getDecorator('errors')->setOption('escape', false);
                        $form->addError($error_message);
                      }
                      return;
                    }

                      if (empty($module_id)) {
                          $moduleTable = $module_table->createRow();
                          $moduleTable->module_name = $moduleName;
                          $moduleTable->module_title = $moduleTitle;
                          $moduleTable->item_type = $itemType;
                          $moduleTable->title_field = $field_title;
                          $moduleTable->body_field = $field_body;
                          $moduleTable->owner_field = $field_owner;
                          $moduleTable->like_field = $values['like_field'];
                          $moduleTable->comment_field = $values['comment_field'];
                          $moduleTable->date_field = $values['date_field'];
                          $moduleTable->featured_field = $values['featured_field'];
                          $moduleTable->sponsored_field = $values['sponsored_field'];
                          $moduleTable->image_option = $values['image_option'];
                          $moduleTable->category_name = $values['category_name'];
                          $moduleTable->category_title_field = $category_title_field;
                          $moduleTable->save();
                      } else {
                          $module_table->update(array('module_name' => $moduleName, 'module_title' => $moduleTitle, 'item_type' => $itemType, 'title_field' => $field_title, 'body_field' => $field_body, 'like_field' => $values['like_field'], 'comment_field' => $values['comment_field'], 'date_field' => $values['date_field'], 'featured_field' => $values['featured_field'], 'sponsored_field' => $values['sponsored_field'], 'image_option' => $image_option, 'category_name' => $itemCategory, 'category_title_field' => $category_title_field), array('module_id =?' => $module_id));
                      }
                      $this->_helper->redirector->gotoRoute(array('module' => 'sitemenu', 'controller' => 'modules-settings', 'action' => 'index'), 'admin_default', true);
                    
                } else {
                        $form->getDecorator('errors')->setOption('escape', false);
                        $form->addError('Please enter a correct database table item.');
                        return;
                }
            } else{
              $form->getDecorator('errors')->setOption('escape', false);
              $form->addError("Please choose Content Module. It is required.");
              return;
            }
        }

    }

    public function moduleDeleteAction() {
      
      //CHECK PERMISSION FOR VIEW.
    if (!$this->_helper->requireUser()->isValid())
      return;
      
    $module_id = $this->_getParam('module_id');
    if (!empty($module_id)) {
      $moduleItem = Engine_Api::_()->getItem('sitemenu_module', $module_id);
      if(!empty($moduleItem->is_delete)){
        return $this->_forward('notfound', 'error', 'core');
      }
    }

    // Check post
    if ($this->getRequest()->isPost()) {
      if (empty($moduleItem->is_delete))
        $moduleItem->delete();
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => 'Deleted Successsfully.'
      ));
    }
  }

    public function moduleDetailAction() {
        $module_id = $this->_getParam('module_id');
        $this->view->moduleItem = Engine_Api::_()->getItem('sitemenu_module', $module_id);
    }

    public function moduleStatusAction() {
        $module_id = $this->_getParam('module_id');
        $module = Engine_Api::_()->getItem('sitemenu_module', $module_id);
        
        $module->status = 1-$module->status;
        $module->save();
        $this->_redirectCustom(array('route' => 'admin_default', 'module' => 'sitemenu', 'controller' => 'modules-settings', 'action' => 'index'));
    }
    
  public function createCategoriesMenuAction() {
    $module_id = $this->_getParam('module_id');
    $module = Engine_Api::_()->getItem('sitemenu_module', $module_id);
    $this->view->moduleTitle = $module->module_title;

    //WORK FOR GETTING CATEGORY LIST FOR CHECKBOXES

    if (!empty($module->category_name)) {

      if (!empty($module->category_title_field)) {
        $category_title_field = $module->category_title_field;

        //WORK FOR SHOWING CATEGORY DROPDOWN      
        $category_table = Engine_Api::_()->getItemTable($module->category_name);
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

          if (strstr($module->item_type, "sitereview")) {
            $sitereviewTableName = explode("sitereview_listing_", $module->item_type);
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
      if (!empty($module->item_type))
        switch ($module->item_type) {
          case 'video':
            $categoryTable = Engine_Api::_()->getDbtable('categories', 'video');
            $category_select = $categoryTable->select()
                    ->from($categoryTable->info('name'), array('category_id', 'category_name'))
                    ->where('category_id != ?', 0);
            $categoryArray = $category_select->query()->fetchAll();

            break;
          case 'classified':
            $categoryTable = Engine_Api::_()->getDbtable('categories', 'classified');
            $category_select = $categoryTable->select()
                    ->from($categoryTable->info('name'), array('category_id', 'category_name'))
                    ->where('category_id != ?', 0);
            $categoryArray = $category_select->query()->fetchAll();

            break;
          case 'sitepagedocument_document':
            $categoryTable = Engine_Api::_()->getDbtable('categories', 'sitepagedocument');
            $category_select = $categoryTable->select()
                    ->from($categoryTable->info('name'), array('category_id', 'title As category_name'))
                    ->where('category_id != ?', 0);
            $categoryArray = $category_select->query()->fetchAll();

            break;
          case 'sitepageevent_event':
            $categoryTable = Engine_Api::_()->getDbtable('categories', 'sitepageevent');
            $category_select = $categoryTable->select()
                    ->from($categoryTable->info('name'), array('category_id', 'title As category_name'))
                    ->where('category_id != ?', 0);
            $categoryArray = $category_select->query()->fetchAll();

            break;
          case 'sitepagenote_note':
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
    
     if ($this->getRequest()->isPost()) {
    
      $postArray = $_POST;      
      $values = array();
      
      if(!empty($module) && !empty($module->module_name)){
        $menuItemsTable = Engine_Api::_()->getDbtable('menuItems', 'core');
        $menuItemsSelect = $menuItemsTable->select()
                ->from($menuItemsTable->info('name'), array('id', 'params', 'order'))
                ->where("module = ?", $module->module_name)
                ->where("menu = 'core_main'")
                ->limit(1);

        $getEnabledModuleNames = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
        if (!empty($getEnabledModuleNames)) {
          $menuItemsSelect->where('module IN(?)', $getEnabledModuleNames);
        }
        $coreMainMenuItem = $menuItemsTable->fetchRow($menuItemsSelect);
        
      }  else {
        return;
      }
      
      if(!empty($coreMainMenuItem)){
        $tempParams = $coreMainMenuItem->params;
        if(!empty($tempParams) && isset($tempParams['parent_id']) && !empty($tempParams['parent_id'])){
          return;
        }elseif(!empty($tempParams) && isset($tempParams['root_id']) && !empty($tempParams['root_id'])){
          $values['parent_id'] = $coreMainMenuItem->id;
          $values['root_id'] = $tempParams['root_id'];
        }  else{
          $values['root_id'] = $coreMainMenuItem->id;
        }
        if (!empty($coreMainMenuItem->order)) {
            $menuItemOrder = ($coreMainMenuItem->order * 10) + 1;
          }
      }
      
      // WORK FOR CATEGORY URL
      if (!empty($module->item_type)) {
        if(strstr($module->item_type, "sitereview")){
          $sitereviewTableName = explode("sitereview_listing_", $module->item_type);
          $listingtypeId = $sitereviewTableName[1];
          $has_item_type = Engine_Api::_()->hasItemType("sitereview_listing");
        }else{
          $has_item_type = Engine_Api::_()->hasItemType($module->item_type);
        }
//        if(!empty($has_item_type)){
//          $category_table = Engine_Api::_()->getItemTable($module->category_name);
//        }
      }
      
      $values['uri']= "";
      $values['icon']= "";
    $menuItemsTable = Engine_Api::_()->getDbtable('menuItems', 'core');
      $db = $menuItemsTable->getAdapter();
      $db->beginTransaction();

      try {
        if(isset($postArray['category_submenu']) && !empty($postArray['category_submenu'])){
        foreach ($postArray['category_submenu'] as $category_id => $category){
          $temp_url = "";
          // WORK FOR CATEGORY URL
            if (!empty($has_item_type)) {
              if (!empty($category_table)) {
                $category_table = Engine_Api::_()->getItemTable($module->category_name);
                $category_select = $category_table->select();
                if (!empty($listingtypeId)) {
                  $category_select = $category_select->where('listingtype_id =?', $listingtypeId);
                }
                $category_select->where('category_id = ?', $category_id);
                $category_obj = $category_table->fetchRow($category_select);
                $tempHref = $category_obj->getHref();

                if (!empty($tempHref)) {
                  $values['uri'] = $tempHref;

                }elseif (!empty($module->category_name)) {
                  $temp_url = $this->view->url(array('action' => 'browse'), $module->module_name . '_general') . "?category_id=" . $category_id;
                  if (!empty($temp_url)):
                    $values['uri'] = $temp_url;
                  endif;
                }
              
            } elseif (empty($module->category_name) && !empty($module->module_name)) {

              switch ($module->module_name) {
                case 'video':
                  $temp_url = $this->view->url(array('action' => 'browse'), $module->module_name . '_general') . "?category=" . $category_id;
                  if (!empty($temp_url)):
                    $values['uri'] = $temp_url;
                  endif;
                  break;

                case 'classified':
                  $temp_url = $this->view->url(array('action' => 'browse'), $module->module_name . '_general') . "?category_id=" . $category_id;
                  if (!empty($temp_url)):
                    $values['uri'] = $temp_url;
                  endif;
                  break;

                case 'sitepagedocument':
                  $temp_url = $this->view->url(array('action' => 'browse'), $module->module_name . '_browse') . "?document_category_id=" . $category_id;
                  if (!empty($temp_url)):
                    $values['uri'] = $temp_url;
                  endif;
                  break;
                  
                case 'sitepageevent':
                  $temp_url = $this->view->url(array('action' => 'browse'), $module->module_name . '_browse') . "?event_category_id=" . $category_id;
                  if (!empty($temp_url)):
                    $values['uri'] = $temp_url;
                  endif;
                  break;

                case 'sitepagenote':
                  $temp_url = $this->view->url(array('action' => 'browse'), $module->module_name . '_browse') . "?note_category_id=" . $category_id;
                  if (!empty($temp_url)):
                    $values['uri'] = $temp_url;
                  endif;
                  break;

                default:
                  break;
              }
            }
          }


        $menuItem = $menuItemsTable->createRow();
          $menuItem->label = $category;
          $menuItem->params = $values;
          $menuItem->menu = 'core_main';
          $menuItem->module = 'core'; // Need to do this to prevent it from being hidden
          $menuItem->plugin = '';
          $menuItem->submenu = '';
          $menuItem->custom = 1;
          $menuItem->order = !empty($menuItemOrder)? $menuItemOrder : 999;
          $menuItem->save();

          $menuItem->name = 'custom_' . sprintf('%d', $menuItem->id);
          $menuItem->save();
        }
      }else {
        $this->view->isPost = true;
        return;
        }
      
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 1000,
          'messages' => "Your category menu has been successfully created.",
      ));
  }
    }
}