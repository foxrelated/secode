<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_AdminSettingsController extends Core_Controller_Action_Admin {

    public function __call($method, $params) {
        /*
         * YOU MAY DISPLAY ANY ERROR MESSAGE USING FORM OBJECT.
         * YOU MAY EXECUTE ANY SCRIPT, WHICH YOU WANT TO EXECUTE ON FORM SUBMIT.
         * REMEMBER:
         *    RETURN TRUE: IF YOU DO NOT WANT TO STOP EXECUTION.
         *    RETURN FALSE: IF YOU WANT TO STOP EXECUTION.
         */
        if (!empty($method) && $method == 'Siteevent_Form_Admin_Settings_Global') {
            
        }
        return true;
    }

    //ACTION FOR GLOBAL SETTINGS
    public function indexAction() {
      $pluginName = 'siteevent';
      
      $redirectionPrevious = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.redirection', 'home');

      // TRIM LICENSE KEYS
      if(!empty($_POST)) {
        foreach($_POST as $key => $value) {
          if(@strstr($key, "_lsettings")) {
            $_POST[$key] = @trim($_POST[$key]);
          }
        } 
      }
      $_POST['siteevent_calender_format'] = 1;
      include APPLICATION_PATH . '/application/modules/Siteevent/controllers/license/license1.php';
      
      $redirectionNew = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.redirection', 'home');
      if($redirectionPrevious != $redirectionNew) {
          $db = Zend_Db_Table_Abstract::getDefaultAdapter();
          $db->update('engine4_core_menuitems', array('params' => '{"route":"siteevent_general","action":"'.$redirectionNew.'"}'), array('name = ?' => 'core_main_siteevent'));
      }
      
    }

    public function createEditAction() {

        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteevent_admin_main', array(), 'siteevent_admin_main_settings');

        //GET NAVIGATION
        $this->view->navigationGeneral = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteevent_admin_main_settings', array(), 'siteevent_admin_main_createedit');

        //GET TINYMCE SETTINGS
        $this->view->upload_url = "";
        $viewer = Engine_Api::_()->user()->getViewer();
        $albumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album');
        if (Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'create') && $albumEnabled) {
            $this->view->upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'upload-photo'), 'siteevent_general', true);
        }

        $orientation = $this->view->layout()->orientation;
        if ($orientation == 'right-to-left') {
            $this->view->directionality = 'rtl';
        } else {
            $this->view->directionality = 'ltr';
        }

        $local_language = explode('_', $this->view->locale()->getLocale()->__toString());
        $this->view->language = $local_language[0];

        $this->view->form = $form = new Siteevent_Form_Admin_Settings_CreateEdit();

        if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
            $values = $form->getValues();
            include APPLICATION_PATH . '/application/modules/Siteevent/controllers/license/license2.php';
        }
    }

    // Added phrase in language file.
    public function addPhraseAction($phrase) {

        if ($phrase) {
            //file path name
            $targetFile = APPLICATION_PATH . '/application/languages/en/custom.csv';
            if (!file_exists($targetFile)) {
                //Sets access of file
                touch($targetFile);
                //changes permissions of the specified file.
                chmod($targetFile, 0777);
            }
            if (file_exists($targetFile)) {
                $writer = new Engine_Translate_Writer_Csv($targetFile);
                $writer->setTranslations($phrase);
                $writer->write();
                //clean the entire cached data manually
                @Zend_Registry::get('Zend_Cache')->clean();
            }
        }
    }

    //ACTION FOR LEVEL SETTINGS
    public function levelAction() {

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteevent_admin_main', array(), 'siteevent_admin_main_level');

        //$this->view->tab_type = 'levelType';
        //GET LEVEL ID
        if (null != ($id = $this->_getParam('id'))) {
            $level = Engine_Api::_()->getItem('authorization_level', $id);
        } else {
            $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
        }

        if (!$level instanceof Authorization_Model_Level) {
            throw new Engine_Exception('missing level');
        }

        $id = $level->level_id;

        //MAKE FORM
        $this->view->form = $form = new Siteevent_Form_Admin_Settings_Level(array(
            'public' => ( in_array($level->type, array('public')) ),
            'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
        ));
        $form->level_id->setValue($id);

        //POPULATE DATA
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $form->populate($permissionsTable->getAllowed('siteevent_event', $id, array_keys($form->getValues())));

        $diaryArray = array();
        $diaryArray['create_diary'] = $permissionsTable->getAllowed('siteevent_diary', $id, 'create');
        $diaryArray['diary'] = $permissionsTable->getAllowed('siteevent_diary', $id, 'view');
        $diaryArray['auth_diary'] = $permissionsTable->getAllowed('siteevent_diary', $id, 'auth_view');
        $form->populate($diaryArray);

        //CHECK POST
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //CHECK VALIDITY
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //PROCESS
        $values = $form->getValues();

        $diarySettings = array();
        $otherSettings = array();
        foreach ($values as $key => $value) {
            if ($key == 'create_diary') {
                $diarySettings['create'] = $value;
            } elseif ($key == 'diary') {
                $diarySettings['view'] = $value;
            } elseif ($key == 'auth_diary') {
                $diarySettings['auth_view'] = $value;
            } else {
                $otherSettings[$key] = $value;
            }
        }

        $db = $permissionsTable->getAdapter();
        $db->beginTransaction();
        try {
            include APPLICATION_PATH . '/application/modules/Siteevent/controllers/license/license2.php';
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    //ACTION FOR GETTING THE CATGEORIES, SUBCATEGORIES AND 3RD LEVEL CATEGORIES
    public function categoriesAction() {

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteevent_admin_main', array(), 'siteevent_admin_main_categories');

        $this->view->success_msg = $this->_getParam('success');

        //LIGHTBOX FOR OTHER PLUGINS    
        $this->view->template_type = $this->_getParam('template_type');

        $integration_plugin_name = array('advancedactivity', 'communityad', 'facebookse', 'facebooksefeed', 'suggestion', 'sitepage', 'sitefaq', 'sitetagcheckin', 'sitevideoview', 'sitelike', 'advancedslideshow');

        $this->view->pluginCounts = 0;
        foreach ($integration_plugin_name as $plugin) {
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($plugin)) {
                $this->view->pluginCounts++;
            }
        }

        //GET TASK
        if (isset($_POST['task'])) {
            $task = $_POST['task'];
        } elseif (isset($_GET['task'])) {
            $task = $_GET['task'];
        } else {
            $task = "main";
        }

        $orientation = $this->view->layout()->orientation;
        if ($orientation == 'right-to-left') {
            $this->view->directionality = 'rtl';
        } else {
            $this->view->directionality = 'ltr';
        }

        $local_language = $this->view->locale()->getLocale()->__toString();
        $local_language = explode('_', $local_language);
        $this->view->language = $local_language[0];

        //GET CATEGORIES TABLE
        $tableCategory = Engine_Api::_()->getDbTable('categories', 'siteevent');
        $tableCategoryName = $tableCategory->info('name');

        //GET STORAGE API
        $this->view->storage = Engine_Api::_()->storage();

        //GET EVENT TABLE
        $tableSiteevent = Engine_Api::_()->getDbtable('events', 'siteevent');

        if ($task == "changeorder") {
            $divId = $_GET['divId'];
            $siteeventOrder = explode(",", $_GET['siteeventorder']);
            //RESORT CATEGORIES
            if ($divId == "categories") {
                for ($i = 0; $i < count($siteeventOrder); $i++) {
                    $category_id = substr($siteeventOrder[$i], 4);
                    $tableCategory->update(array('cat_order' => $i + 1), array('category_id = ?' => $category_id));
                }
            } elseif (substr($divId, 0, 7) == "subcats") {
                for ($i = 0; $i < count($siteeventOrder); $i++) {
                    $category_id = substr($siteeventOrder[$i], 4);
                    $tableCategory->update(array('cat_order' => $i + 1), array('category_id = ?' => $category_id));
                }
            } elseif (substr($divId, 0, 11) == "treesubcats") {
                for ($i = 0; $i < count($siteeventOrder); $i++) {
                    $category_id = substr($siteeventOrder[$i], 4);
                    $tableCategory->update(array('cat_order' => $i + 1), array('category_id = ?' => $category_id));
                }
            }
        }

        $categories = array();
        $category_info = $tableCategory->getCategories(array('category_id', 'category_name', 'cat_order', 'file_id', 'banner_id', 'sponsored'), null, 0, 0, 1);
        foreach ($category_info as $value) {
            $sub_cat_array = array();
            $subcategories = $tableCategory->getSubCategories($value->category_id);
            foreach ($subcategories as $subresults) {
                $subsubcategories = $tableCategory->getSubCategories($subresults->category_id);
                $treesubarrays[$subresults->category_id] = array();

                foreach ($subsubcategories as $subsubcategoriesvalues) {

                    //GET TOTAL EVENT COUNT
                    $subsubcategory_siteevent_count = $tableSiteevent->getEventsCount($subsubcategoriesvalues->category_id, 'subsubcategory_id');

                    $treesubarrays[$subresults->category_id][] = $treesubarray = array(
                        'tree_sub_cat_id' => $subsubcategoriesvalues->category_id,
                        'tree_sub_cat_name' => $subsubcategoriesvalues->category_name,
                        'count' => $subsubcategory_siteevent_count,
                        'file_id' => $subsubcategoriesvalues->file_id,
                        'banner_id' => $subsubcategoriesvalues->banner_id,
                        'order' => $subsubcategoriesvalues->cat_order,
                        'sponsored' => $subsubcategoriesvalues->sponsored);
                }

                //GET TOTAL EVENTS COUNT
                $subcategory_siteevent_count = $tableSiteevent->getEventsCount($subresults->category_id, 'subcategory_id');

                $sub_cat_array[] = $tmp_array = array(
                    'sub_cat_id' => $subresults->category_id,
                    'sub_cat_name' => $subresults->category_name,
                    'tree_sub_cat' => $treesubarrays[$subresults->category_id],
                    'count' => $subcategory_siteevent_count,
                    'file_id' => $subresults->file_id,
                    'banner_id' => $subresults->banner_id,
                    'order' => $subresults->cat_order,
                    'sponsored' => $subresults->sponsored);
            }

            //GET TOTAL EVENTS COUNT
            $category_siteevent_count = $tableSiteevent->getEventsCount($value->category_id, 'category_id');

            $categories[] = $category_array = array('category_id' => $value->category_id,
                'category_name' => $value->category_name,
                'order' => $value->cat_order,
                'count' => $category_siteevent_count,
                'file_id' => $value->file_id,
                'banner_id' => $value->banner_id,
                'sponsored' => $value->sponsored,
                'sub_categories' => $sub_cat_array);
        }

        $this->view->categories = $categories;

        //GET CATEGORIES TABLE
        $tableCategory = Engine_Api::_()->getDbTable('categories', 'siteevent');
        $tableCategoryName = $tableCategory->info('name');
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->view->category_id = $category_id = $request->getParam('category_id', 0);
        $perform = $request->getParam('perform', 'add');
        $cat_dependency = 0;
        $subcat_dependency = 0;
        if ($category_id) {
            $category = Engine_Api::_()->getItem('siteevent_category', $category_id);
            if ($category && empty($category->cat_dependency)) {
                $cat_dependency = $category->category_id;
            } elseif ($category && !empty($category->cat_dependency)) {
                $cat_dependency = $category->category_id;
                $subcat_dependency = $category->category_id;
            }
        }

        if ($perform == 'add') {
            $this->view->form = $form = new Siteevent_Form_Admin_Categories_Add();

            //CHECK POST
            if (!$this->getRequest()->isPost()) {
                return;
            }

            //CHECK VALIDITY
            if (!$form->isValid($this->getRequest()->getPost())) {

                if (empty($_POST['category_name'])) {
                    $form->addError($this->view->translate("Category Name * Please complete this field - it is required."));
                }
                return;
            }

            //PROCESS
            $values = $form->getValues();

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {

                $row_info = $tableCategory->fetchRow($tableCategory->select()->from($tableCategoryName, 'max(cat_order) AS cat_order'));
                $cat_order = $row_info['cat_order'] + 1;

                //GET CATEGORY TITLE
                $category_name = str_replace("'", "\'", trim($values['category_name']));
                $values['cat_order'] = $cat_order;
                $values['category_name'] = $category_name;

                $values['cat_dependency'] = $cat_dependency;
                $values['subcat_dependency'] = $subcat_dependency;

                $row = $tableCategory->createRow();
                $row->setFromArray($values);

                //UPLOAD ICON
                if (isset($_FILES['icon'])) {
                    $photoFileIcon = $row->setPhoto($form->icon);
                    //UPDATE FILE ID IN CATEGORY TABLE
                    if (!empty($photoFileIcon->file_id)) {
                        $row->file_id = $photoFileIcon->file_id;
                    }
                }

                //UPLOAD CATEGORY PHOTO
                if (isset($_FILES['photo'])) {
                    $photoFile = $row->setPhoto($form->photo, true);
                    //UPDATE FILE ID IN CATEGORY TABLE
                    if (!empty($photoFile->file_id)) {
                        $row->photo_id = $photoFile->file_id;
                    }
                }

                //UPLOAD BANNER
                if (isset($_FILES['banner'])) {
                    $photoFileBanner = $row->setPhoto($form->banner);
                    //UPDATE FILE ID IN CATEGORY TABLE
                    if (!empty($photoFileBanner->file_id)) {
                        $row->banner_id = $photoFileBanner->file_id;
                    }
                }

                $banner_url = preg_match('/\s*[a-zA-Z0-9]{2,5}:\/\//', $values['banner_url']);

                if (empty($banner_url)) {
                    if ($values['banner_url']) {
                        $row->banner_url = "http://" . $values['banner_url'];
                    } else {
                        $row->banner_url = $values['banner_url'];
                    }
                } else {
                    $row->banner_url = $values['banner_url'];
                }

                $category_id = $row->save();

                if (empty($cat_dependency) && empty($subcat_dependency)) {
                    Engine_Api::_()->siteevent()->categoriesPageCreate(array(0 => $category_id));
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            return $this->_helper->redirector->gotoRoute(array('module' => 'siteevent', 'action' => 'categories', 'controller' => 'settings', 'category_id' => $category_id, 'perform' => 'edit'), 'admin_default', true);
        } else {
            $this->view->form = $form = new Siteevent_Form_Admin_Categories_Edit();
            $category = Engine_Api::_()->getItem('siteevent_category', $category_id);
            $form->populate($category->toArray());

            //CHECK POST
            if (!$this->getRequest()->isPost()) {
                return;
            }

            //CHECK VALIDITY
            if (!$form->isValid($this->getRequest()->getPost())) {

                if (empty($_POST['category_name'])) {
                    $form->addError($this->view->translate("Category Name * Please complete this field - it is required."));
                }
                return;
            }
            $values = $form->getValues();

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {

                //GET CATEGORY TITLE
                $category_name = str_replace("'", "\'", trim($values['category_name']));

                $category->category_name = $category_name;

                $category->meta_title = $values['meta_title'];
                $category->meta_description = $values['meta_description'];
                $category->meta_keywords = $values['meta_keywords'];
                $category->sponsored = $values['sponsored'];
                if(isset($values['allow_guestreview'])) {
                    $category->allow_guestreview = $values['allow_guestreview'];
                } else {
                    $category->allow_guestreview = 1;
                }
                $category->banner_title = $values['banner_title'];
                $category->banner_url_window = $values['banner_url_window'];
                $category->category_slug = $values['category_slug'];
                $category->top_content = $values['top_content'];
                $category->bottom_content = $values['bottom_content'];
                $cat_dependency = $category->cat_dependency;
                $subcat_dependency = $category->subcat_dependency;
                if ($category_id && empty($subcat_dependency) && !empty($cat_dependency)) {
                    $cat_dependency = $cat_dependency;
                    $subcat_dependency = 0;
                } elseif ($category_id && !empty($subcat_dependency) && !empty($cat_dependency)) {
                    $cat_dependency = $cat_dependency;
                    $subcat_dependency = $subcat_dependency;
                }

                $category->cat_dependency = $cat_dependency;
                $category->subcat_dependency = $subcat_dependency;

                include APPLICATION_PATH . '/application/modules/Siteevent/controllers/license/license2.php';

                if (empty($tempCategoriesFlag))
                    return;

                //UPLOAD ICON
                if (isset($_FILES['icon'])) {
                    $previous_file_id = $category->file_id;
                    $photoFileIcon = $category->setPhoto($form->icon);
                    //UPDATE FILE ID IN CATEGORY TABLE
                    if (!empty($photoFileIcon->file_id)) {

                        //DELETE PREVIOUS CATEGORY ICON
                        if ($previous_file_id) {
                            $file = Engine_Api::_()->getItem('storage_file', $previous_file_id);
                            $file->delete();
                        }

                        $category->file_id = $photoFileIcon->file_id;
                        $category->save();
                    }
                }

                //UPLOAD CATEGORY PHOTO
                if (isset($_FILES['photo'])) {
                    $previous_photo_id = $category->photo_id;
                    $photoFile = $category->setPhoto($form->photo, true);
                    //UPDATE FILE ID IN CATEGORY TABLE
                    if (!empty($photoFile->file_id)) {
                        $category->photo_id = $photoFile->file_id;

                        //DELETE PREVIOUS CATEGORY ICON
                        if ($previous_photo_id) {
                            $file = Engine_Api::_()->getItem('storage_file', $previous_photo_id);
                            $file->delete();
                        }
                    }
                }

                //UPLOAD BANNER
                if (isset($_FILES['banner'])) {
                    $previous_banner_id = $category->banner_id;
                    $photoFileBanner = $category->setPhoto($form->banner);
                    //UPDATE FILE ID IN CATEGORY TABLE
                    if (!empty($photoFileBanner->file_id)) {

                        //DELETE PREVIOUS CATEGORY BANNER
                        if ($previous_banner_id) {
                            $file = Engine_Api::_()->getItem('storage_file', $previous_banner_id);
                            $file->delete();
                        }

                        $category->banner_id = $photoFileBanner->file_id;
                        $category->save();
                    }
                }

                $banner_url = preg_match('/\s*[a-zA-Z0-9]{2,5}:\/\//', $values['banner_url']);

                if (empty($banner_url)) {
                    if ($values['banner_url']) {
                        $category->banner_url = "http://" . $values['banner_url'];
                    } else {
                        $category->banner_url = $values['banner_url'];
                    }
                } else {
                    $category->banner_url = $values['banner_url'];
                }
                $category->save();

                if (isset($values['removephoto']) && !empty($values['removephoto'])) {
                    //DELETE CATEGORY ICON
                    $file = Engine_Api::_()->getItem('storage_file', $category->photo_id);

                    //UPDATE FILE ID IN CATEGORY TABLE
                    $category->photo_id = 0;
                    $category->save();
                    $file->delete();
                }

                if (isset($values['removeicon']) && !empty($values['removeicon'])) {

                    $previous_icon_id = $category->file_id;

                    if ($previous_icon_id) {
                        //UPDATE FILE ID IN CATEGORY TABLE
                        $category->file_id = 0;
                        $category->save();

                        //DELETE CATEGORY ICON
                        $file = Engine_Api::_()->getItem('storage_file', $previous_icon_id);
                        $file->delete();
                    }
                }

                if (isset($values['removebanner']) && !empty($values['removebanner'])) {

                    $previous_banner_id = $category->banner_id;

                    if ($previous_banner_id) {
                        //UPDATE FILE ID IN CATEGORY TABLE
                        $category->banner_id = 0;
                        $category->save();

                        //DELETE CATEGORY ICON
                        $file = Engine_Api::_()->getItem('storage_file', $previous_banner_id);
                        $file->delete();
                    }
                }

                if (empty($cat_dependency) && empty($subcat_dependency)) {
                    Engine_Api::_()->siteevent()->categoriesPageCreate(array(0 => $category_id));
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            return $this->_helper->redirector->gotoRoute(array('module' => 'siteevent', 'action' => 'categories', 'controller' => 'settings', 'category_id' => $category_id, 'perform' => 'edit'), 'admin_default', true);
        }
    }

    //ACTION FOR MAPPING OF EVENTS
    Public function mappingCategoryAction() {

        //SET LAYOUT
        $this->_helper->layout->setLayout('admin-simple');

        //GET CATEGORY ID AND OBJECT
        $this->view->catid = $catid = $this->_getParam('category_id');
        $category = Engine_Api::_()->getItem('siteevent_category', $catid);

        //GET CATEGORY DEPENDANCY
        $this->view->subcat_dependency = $subcat_dependency = $this->_getParam('subcat_dependency');

        //CREATE FORM
        $this->view->form = $form = new Siteevent_Form_Admin_Settings_Mapping();

        $this->view->close_smoothbox = 0;

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        if ($this->getRequest()->isPost()) {

            //GET FORM VALUES
            $values = $form->getValues();

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {

                //GET EVENT TABLE
                $tableSiteevent = Engine_Api::_()->getDbtable('events', 'siteevent');
                $tableSiteeventName = $tableSiteevent->info('name');

                //GET REVIEW TABLE
                $reviewTable = Engine_Api::_()->getDbtable('reviews', 'siteevent');
                $reviewTableName = $reviewTable->info('name');

                //GET CATEGORY TABLE
                $tableCategory = Engine_Api::_()->getDbtable('categories', 'siteevent');

                //ON CATEGORY DELETE
                $rows = $tableCategory->getSubCategories($catid);
                foreach ($rows as $row) {
                    $subrows = $tableCategory->getSubCategories($row->category_id);
                    foreach ($subrows as $subrow) {
                        $subrow->delete();
                    }
                    $row->delete();
                }

                $previous_cat_profile_type = $tableCategory->getProfileType(null, $catid);
                $new_cat_profile_type = $tableCategory->getProfileType(null, $values['new_category_id']);

                /// EVENTS WHICH HAVE THIS CATEGORY
                if ($previous_cat_profile_type != $new_cat_profile_type && !empty($values['new_category_id'])) {
                    $events = $tableSiteevent->getCategoryList($catid, 'category_id');

                    foreach ($events as $event) {

                        //DELETE ALL MAPPING VALUES FROM FIELD TABLES
                        Engine_Api::_()->fields()->getTable('siteevent_event', 'values')->delete(array('item_id = ?' => $event->event_id));
                        Engine_Api::_()->fields()->getTable('siteevent_event', 'search')->delete(array('item_id = ?' => $event->event_id));
                        //UPDATE THE PROFILE TYPE OF ALREADY CREATED EVENTS
                        $tableSiteevent->update(array('profile_type' => $new_cat_profile_type), array('event_id = ?' => $event->event_id));

                        //REVIEW PROFILE TYPE UPDATION WORK
                        $reviewIds = $reviewTable->select()
                                ->from($reviewTableName, 'review_id')
                                ->where('resource_id = ?', $event->event_id)
                                ->where('resource_type = ?', 'siteevent_event')
                                ->query()
                                ->fetchAll(Zend_Db::FETCH_COLUMN);
                        if (!empty($reviewIds)) {
                            foreach ($reviewIds as $reviewId) {
                                //DELETE ALL MAPPING VALUES FROM FIELD TABLES
                                Engine_Api::_()->fields()->getTable('siteevent_review', 'values')->delete(array('item_id = ?' => $reviewId));
                                Engine_Api::_()->fields()->getTable('siteevent_review', 'search')->delete(array('item_id = ?' => $reviewId));

                                //UPDATE THE PROFILE TYPE OF ALREADY CREATED REVIEWS
                                $reviewTable->update(array('profile_type_review' => $new_cat_profile_type), array('resource_id = ?' => $reviewId));
                            }
                        }
                    }
                }

                //EVENT TABLE CATEGORY DELETE WORK
                if (isset($values['new_category_id']) && !empty($values['new_category_id'])) {
                    $tableSiteevent->update(array('category_id' => $values['new_category_id']), array('category_id = ?' => $catid));
                } else {

                    $selectEvents = $tableSiteevent->select()
                            ->from($tableSiteevent->info('name'))
                            ->where('category_id = ?', $catid);

                    foreach ($tableSiteevent->fetchAll($selectEvents) as $event) {
                        $event->delete();
                    }
                }

                $category->delete();

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }

        $this->view->close_smoothbox = 1;
    }

    //ACTION FOR GETTING THE MEMBER WHICH CAN BE CLAIMED THE PAGE
    function getEventsAction() {

        //GET EVENT TABLE
        $siteeventTable = Engine_Api::_()->getDbtable('events', 'siteevent');
        $siteeventTableName = $siteeventTable->info('name');

        //MAKE QUERY
        $select = $siteeventTable->select()
                ->where('title  LIKE ? ', '%' . $this->_getParam('text') . '%')
                ->where($siteeventTableName . '.closed = ?', '0')
                ->where($siteeventTableName . '.approved = ?', '1')
                ->where($siteeventTableName . '.draft = ?', '0')
                ->where($siteeventTableName . '.search = ?', '1')
                ->order('title ASC')
                ->limit($this->_getParam('limit', 40));

        //FETCH RESULTS
        $usersiteevents = $siteeventTable->fetchAll($select);
        $data = array();
        $mode = $this->_getParam('struct');

        if ($mode == 'text') {
            foreach ($usersiteevents as $usersiteevent) {
                $content_photo = $this->view->itemPhoto($usersiteevent, 'thumb.icon');
                $data[] = array(
                    'id' => $usersiteevent->event_id,
                    'label' => $usersiteevent->title,
                    'photo' => $content_photo
                );
            }
        } else {
            foreach ($usersiteevents as $usersiteevent) {
                $content_photo = $this->view->itemPhoto($usersiteevent, 'thumb.icon');
                $data[] = array(
                    'id' => $usersiteevent->event_id,
                    'label' => $usersiteevent->title,
                    'photo' => $content_photo
                );
            }
        }
        return $this->_helper->json($data);
    }

    //ACTION FOR GETTING THE MEMBER WHICH CAN BE CLAIMED THE PAGE
    function getReviewsAction() {

        //GET EVENT TABLE
        $reviewTable = Engine_Api::_()->getDbtable('reviews', 'siteevent');
        $reviewTableName = $reviewTable->info('name');

        //MAKE QUERY
        $select = $reviewTable->select()
                ->where('title  LIKE ? ', '%' . $this->_getParam('text') . '%')
                ->where($reviewTableName . '.type != ?', 'visitor')
                ->where($reviewTableName . '.status = ?', '1')
                ->order('title ASC')
                ->limit($this->_getParam('limit', 40));

        //FETCH RESULTS
        $reviews = $reviewTable->fetchAll($select);
        $data = array();
        $mode = $this->_getParam('struct');

        if ($mode == 'text') {
            foreach ($reviews as $review) {
                $content_photo = $this->view->itemPhoto($review->getOwner(), 'thumb.icon');
                $data[] = array(
                    'id' => $review->review_id,
                    'label' => $review->title,
                    'photo' => $content_photo
                );
            }
        } else {
            foreach ($reviews as $review) {
                $content_photo = $this->view->itemPhoto($review->getOwner(), 'thumb.icon');
                $data[] = array(
                    'id' => $review->review_id,
                    'label' => $review->title,
                    'photo' => $content_photo
                );
            }
        }
        return $this->_helper->json($data);
    }

    //ACTINO FOR SEARCH FORM TAB
    public function formSearchAction() {

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteevent_admin_main', array(), 'siteevent_admin_main_formsearch');

        //GET SEARCH TABLE
        $tableSearchForm = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');

        //CHECK POST
        if ($this->getRequest()->isPost()) {

            //BEGIN TRANSCATION
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            $values = $_POST;
            $rowCategory = $tableSearchForm->getFieldsOptions('siteevent', 'category_id');
            $rowLocation = $tableSearchForm->getFieldsOptions('siteevent', 'location');
            $defaultCategory = 0;
            $defaultAddition = 0;
            $count = 1;
            try {
                foreach ($values['order'] as $key => $value) {
                    $multiplyAddition = $count * 5;
                    $tableSearchForm->update(array('order' => $defaultAddition + $defaultCategory + $key + $multiplyAddition + 1), array('searchformsetting_id = ?' => (int) $value));

                    if (!empty($rowCategory) && $value == $rowCategory->searchformsetting_id) {
                        $defaultCategory = 1;
                        $defaultAddition = 10000000;
                    }
                    $count++;
                }
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }

        //MAKE QUERY
        $select = $tableSearchForm->select()->where('module = ?', 'siteevent')->order('order');
        include APPLICATION_PATH . '/application/modules/Siteevent/controllers/license/license2.php';
    }

    //ACTION FOR DISPLAY/HIDE FIELDS OF SEARCH FORM
    public function diplayFormAction() {

        $field_id = $this->_getParam('id');
        $name = $this->_getParam('name');
        $display = $this->_getParam('display');

        if (!empty($field_id)) {

            if ($name == 'location' && $display == 0) {
                Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->update(array('display' => $display), array('module = ?' => 'siteevent', 'name = ?' => 'proximity'));
            }

            Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->update(array('display' => $display), array('module = ?' => 'siteevent', 'searchformsetting_id = ?' => (int) $field_id));
        }
        $this->_redirect('admin/siteevent/settings/form-search');
    }

    //ACTION FOR SHOW STATISTICS OF EVENT PLUGIN
    public function statisticAction() {

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteevent_admin_main', array(), 'siteevent_admin_main_statistic');

        //GET EVENT TABLE
        $eventTable = Engine_Api::_()->getDbtable('events', 'siteevent');
        $eventTableName = $eventTable->info('name');

        include APPLICATION_PATH . '/application/modules/Siteevent/controllers/license/license2.php';

        //GET EVENT DETAILS
        $select = $eventTable->select()->from($eventTableName, 'count(*) AS totalevent');

        $this->view->totalSiteevent = $select->query()->fetchColumn();
        
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventrepeat')) {
            
            $select->where('repeat_params != ?', '');
            $this->view->totalRepeatSiteevent = $select->query()->fetchColumn();
            
            $this->view->totalNonRepeatSiteevent = $this->view->totalSiteevent - $this->view->totalRepeatSiteevent;
        }        
        

        $this->view->totalEditors = Engine_Api::_()->getDbTable('editors', 'siteevent')->getEditorsCount();

        $select = $eventTable->select()->from($eventTableName, 'count(*) AS totalpublish')->where('draft = ?', 0);

        $this->view->totalPublish = $select->query()->fetchColumn();

        $select = $eventTable->select()->from($eventTableName, 'count(*) AS totaldrafted')->where('draft = ?', 1);

        $this->view->totalDrafted = $select->query()->fetchColumn();

        $select = $eventTable->select()->from($eventTableName, 'count(*) AS totalclosed')->where('closed = ?', 1);

        $this->view->totalClosed = $select->query()->fetchColumn();

        $select = $eventTable->select()->from($eventTableName, 'count(*) AS totalopen')->where('closed = ?', 0);

        $this->view->totalOpen = $select->query()->fetchColumn();

        $select = $eventTable->select()->from($eventTableName, 'count(*) AS totalapproved')->where('approved = ?', 1);

        $this->view->totalapproved = $select->query()->fetchColumn();

        $select = $eventTable->select()->from($eventTableName, 'count(*) AS totaldisapproved')->where('approved = ?', 0);

        $this->view->totaldisapproved = $select->query()->fetchColumn();

        $select = $eventTable->select()->from($eventTableName, 'count(*) AS totalfeatured')->where('featured = ?', 1);

        $this->view->totalfeatured = $select->query()->fetchColumn();

        $select = $eventTable->select()->from($eventTableName, 'count(*) AS totalsponsored')->where('sponsored = ?', 1);

        $this->view->totalsponsored = $select->query()->fetchColumn();

        $select = $eventTable->select()->from($eventTableName, 'sum(comment_count) AS totalcomments');

        $this->view->totalEventComments = $select->query()->fetchColumn();
        if (empty($this->view->totalEventComments))
            $this->view->totalEventComments = 0;

        $select = $eventTable->select()->from($eventTableName, 'sum(like_count) AS totalLikes');

        $this->view->totalEventLikes = $select->query()->fetchColumn();
        if (empty($this->view->totalEventLikes))
            $this->view->totalEventLikes = 0;

        //GET REVIEW TABLE
        $reviewTable = Engine_Api::_()->getDbtable('reviews', 'siteevent');
        $reviewTableName = $reviewTable->info('name');

        //GET REVIEW DETAILS
        $select = $reviewTable->select()->setIntegrityCheck(false)
                ->from($reviewTableName, 'count(*) AS totalreview')
                ->where($reviewTableName . '.resource_type = ?', 'siteevent_event')
                ->where($reviewTableName . '.type = ?', 'editor');

        $select->joinLeft("$eventTableName", "$eventTableName.event_id = $reviewTableName.resource_id", null);

        $this->view->totalEditorReviews = $select->query()->fetchColumn();

        $select = $reviewTable->select()->setIntegrityCheck(false)
                ->from($reviewTableName, 'count(*) AS totalreview')
                ->where($reviewTableName . '.status = ?', 0)
                ->where($reviewTableName . '.resource_type = ?', 'siteevent_event')
                ->where($reviewTableName . '.type = ?', 'editor');

        $select->joinLeft("$eventTableName", "$eventTableName.event_id = $reviewTableName.resource_id", null);

        $this->view->totalDraftEditorReviews = $select->query()->fetchColumn();

        $select = $reviewTable->select()->setIntegrityCheck(false)
                ->from($reviewTableName, 'count(*) AS totalreview')
                ->where($reviewTableName . '.resource_type = ?', 'siteevent_event')
                ->where($reviewTableName . '.type = ?', 'user')
                ->where($reviewTableName . '.owner_id != ?', 0);

        $select->joinLeft("$eventTableName", "$eventTableName.event_id = $reviewTableName.resource_id", null);

        $this->view->totalUserReviews = $select->query()->fetchColumn();

        $select = $reviewTable->select()->setIntegrityCheck(false)
                ->from($reviewTableName, 'count(*) AS totalreview')
                ->where($reviewTableName . '.resource_type = ?', 'siteevent_event')
                ->where($reviewTableName . '.type = ?', 'user')
                ->where($reviewTableName . '.owner_id = ?', 0)
                ->where($reviewTableName . '.status = ?', 1);

        $select->joinLeft("$eventTableName", "$eventTableName.event_id = $reviewTableName.resource_id", null);

        $this->view->totalApprovedVisitorsReviews = $select->query()->fetchColumn();

        $select = $reviewTable->select()->setIntegrityCheck(false)
                ->from($reviewTableName, 'count(*) AS totalreview')
                ->where($reviewTableName . '.resource_type = ?', 'siteevent_event')
                ->where($reviewTableName . '.type = ?', 'user')
                ->where($reviewTableName . '.owner_id = ?', 0)
                ->where($reviewTableName . '.status = ?', 0);

        $select->joinLeft("$eventTableName", "$eventTableName.event_id = $reviewTableName.resource_id", null);

        $this->view->totalDisApprovedVisitorsReviews = $select->query()->fetchColumn();

        $select = $reviewTable->select()->setIntegrityCheck(false)
                ->from($reviewTableName, 'count(*) AS totalreview');

        $select->joinLeft("$eventTableName", "$eventTableName.event_id = $reviewTableName.resource_id", null);

        $this->view->totalReviews = $select->query()->fetchColumn();

        //GET THE TOTAL DISCUSSIONES
        $discussionTable = Engine_Api::_()->getDbtable('topics', 'siteevent');
        $discussionTableName = $discussionTable->info('name');
        $select = $discussionTable->select()->setIntegrityCheck(false)
                ->from($discussionTableName, 'count(*) AS totaldiscussion');

        $select->joinLeft("$eventTableName", "$eventTableName.event_id = $discussionTableName.event_id", null);

        $this->view->totalDiscussionTopics = $select->query()->fetchColumn();

        //GET THE TOTAL POSTS
        $discussionPostTable = Engine_Api::_()->getDbtable('posts', 'siteevent');
        $discussionPostTableName = $discussionPostTable->info('name');
        $select = $discussionPostTable->select()->setIntegrityCheck(false)
                ->from($discussionPostTableName, 'count(*) AS totalpost');

        $select->joinLeft("$eventTableName", "$eventTableName.event_id = $discussionPostTableName.event_id", null);

        $this->view->totalDiscussionPosts = $select->query()->fetchColumn();

        //GET THE TOTAL PHOTOS
        $photoTable = Engine_Api::_()->getDbtable('photos', 'siteevent');
        $photoTableName = $photoTable->info('name');
        $select = $photoTable->select()->setIntegrityCheck(false)
                ->from($photoTableName, 'count(*) AS totalphoto');

        $select->joinLeft("$eventTableName", "$eventTableName.event_id = $photoTableName.event_id", null);

        $this->view->totalPhotos = $select->query()->fetchColumn();

        //GET THE TOTAL VIDEOS
        $type_video = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.show.video');
        if (empty($type_video)) {
            $videoTable = Engine_Api::_()->getDbtable('videos', 'siteevent');
        } else {
            $videoTable = Engine_Api::_()->getDbtable('clasfvideos', 'siteevent');
        }
        $videoTableName = $videoTable->info('name');
        $select = $videoTable->select()->setIntegrityCheck(false)
                ->from($videoTableName, 'count(*) AS totalvideo');

        $select->joinLeft("$eventTableName", "$eventTableName.event_id = $videoTableName.event_id", null);

        $this->view->totalVideos = $select->query()->fetchColumn();

        //GET WISHLITS
        $this->view->totalDiaries = Engine_Api::_()->getDbTable('diaries', 'siteevent')->getDiaryCount();

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventdocument')) {
            $documentTable = Engine_Api::_()->getDbtable('documents', 'siteeventdocument');
            $documentTableName = $documentTable->info('name');
            $select = $documentTable->select()->setIntegrityCheck(false)
                    ->from($documentTableName, 'count(*) AS totaldocument');
            $this->view->totalDocuments = $select->query()->fetchColumn();
        }
        
        //TICKETS STATISTICS
        if (Engine_Api::_()->hasModuleBootstrap('siteeventticket')) {
          //GET THE TOTAL TICKETS
          $ticketTable = Engine_Api::_()->getDbtable('tickets', 'siteeventticket');
          $ticketTableName = $ticketTable->info('name');
          $select = $ticketTable->select()->setIntegrityCheck(false)
              ->from($ticketTableName, 'count(*) AS totalticket');
          $this->view->totalEventTickets = $select->query()->fetchColumn();

          //GET THE TOTAL ORDERS
          $orderTable = Engine_Api::_()->getDbtable('orders', 'siteeventticket');
          $orderTableName = $orderTable->info('name');
          $select = $orderTable->select()->setIntegrityCheck(false)
              ->from($orderTableName, 'count(*) AS totalorder');
          $this->view->totalTicketOrders = $select->query()->fetchColumn();
        }
  }
    
    //ACTION FOR SET THE DEFAULT MAP CENTER POINT
    public function setDefaultMapCenterPoint($oldLocation, $newLocation, $returnLatLng = 0) {

      if ($oldLocation !== $newLocation && $newLocation !== "World" && $newLocation !== "world") {
          $locationResults = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getLatLong(array('location' => $newLocation, 'module' => 'Advanced Events'));
          if(!empty($locationResults['latitude']) && !empty($locationResults['longitude'])) {
              $latitude = $locationResults['latitude'];
              $longitude = $locationResults['longitude'];
          }

            if ($returnLatLng) {
                return array('latitude' => $latitude, 'longitude' => $longitude);
            }        

        Engine_Api::_()->getApi('settings', 'core')->setSetting('siteevent.map.latitude', $latitude);
        Engine_Api::_()->getApi('settings', 'core')->setSetting('siteevent.map.longitude', $longitude);
      }
    }    

    //ACTION FOR SHOWING THE FAQ
    public function faqAction() {

        //GET NAGIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteevent_admin_main', array(), 'siteevent_admin_main_faq');

        $this->view->faq = 1;
        $this->view->faq_type = $this->_getParam('faq_type', 'general');
    }

    //ACTION FOR SHOWING THE Video
    public function showVideoAction() {

        //GET NAGIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteevent_admin_main', array(), 'siteevent_admin_main_video');

        $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteevent_admin_submain', array(), 'siteevent_admin_submain_general_tab');

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        //MAKE FORM
        $this->view->form = $form = new Siteevent_Form_Admin_Video_General();
        $type_video_value = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.show.video', 1);

        if ($this->getRequest()->isPost()) {
          
          $currentYouTubeApiKey = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey');
          if ( !empty($_POST['video_youtube_apikey']) && $_POST['video_youtube_apikey'] != $currentYouTubeApiKey ) {
            $response = Engine_Api::_()->seaocore()->verifyYotubeApiKey($_POST['video_youtube_apikey']);
            if ( !empty($response['errors']) ) {
              $error_message = array('Invalid API Key');
              foreach ( $response['errors'] as $error ) {
                $error_message[] = "Error Reason (" . $error['reason'] . '): ' . $error['message'];
              }          
              $youTubeError = true;                  
              $form->video_youtube_apikey->addErrors($error_message);
              return;
            }
          }

            $values = $_POST;

            include APPLICATION_PATH . '/application/modules/Siteevent/controllers/license/license2.php';

            $reviewVideoTable = Engine_Api::_()->getDbtable('videos', 'siteevent');
            $reviewVideoTableName = $reviewVideoTable->info('name');

            $reviewVideoRatingTable = Engine_Api::_()->getDbTable('videoratings', 'siteevent');
            $reviewVideoRatingName = $reviewVideoRatingTable->info('name');

            $siteeventVideoTable = Engine_Api::_()->getDbtable('clasfvideos', 'siteevent');
            $siteeventVideoTableName = $siteeventVideoTable->info('name');

            if (empty($tempShowVideo))
                return;

            if (isset($values['siteevent_show_video']) && ($type_video_value != $values['siteevent_show_video'])) {

                $coreVideoTable = Engine_Api::_()->getDbtable('videos', 'video');
                $coreVideoTableName = $coreVideoTable->info('name');

                $videoRating = Engine_Api::_()->getDbTable('ratings', 'video');
                $videoRatingName = $videoRating->info('name');

                if (!empty($values['siteevent_show_video'])) {

                    $selectEventVideos = $reviewVideoTable->select()
                            ->from($reviewVideoTableName, array('video_id', 'event_id'))
                            ->where('is_import != ?', 1)
                            ->group('video_id');
                    $eventVideoDatas = $reviewVideoTable->fetchAll($selectEventVideos);
                    foreach ($eventVideoDatas as $eventVideoData) {
                        $listVideo = Engine_Api::_()->getItem('siteevent_video', $eventVideoData->video_id);
                        if (!empty($listVideo)) {

                            $db = $siteeventVideoTable->getAdapter();
                            $db->beginTransaction();

                            try {
                                $clasfVideo = $siteeventVideoTable->createRow();
                                $clasfVideo->event_id = $eventVideoData->event_id;
                                $clasfVideo->video_id = $eventVideoData->video_id;
                                $clasfVideo->is_import = 1;
                                $clasfVideo->created = $listVideo->creation_date;

                                $clasfVideo->save();
                                $db->commit();
                            } catch (Exception $e) {
                                $db->rollBack();
                                throw $e;
                            }

                            $db = $coreVideoTable->getAdapter();
                            $db->beginTransaction();

                            try {
                                $coreVideo = $coreVideoTable->createRow();
                                $coreVideo->title = $listVideo->title;
                                $coreVideo->description = $listVideo->description;
                                $coreVideo->search = $listVideo->search;
                                $coreVideo->owner_id = $listVideo->owner_id;
                                $coreVideo->creation_date = $listVideo->creation_date;
                                $coreVideo->modified_date = $listVideo->modified_date;

                                $coreVideo->view_count = 1;
                                if ($listVideo->view_count > 0) {
                                    $coreVideo->view_count = $listVideo->view_count;
                                }

                                $coreVideo->comment_count = $listVideo->comment_count;
                                $coreVideo->type = $listVideo->type;
                                $coreVideo->code = $listVideo->code;
                                $coreVideo->rating = $listVideo->rating;
                                $coreVideo->status = $listVideo->status;
                                $coreVideo->file_id = 0;
                                $coreVideo->duration = $listVideo->duration;
                                $coreVideo->save();
                                $db->commit();
                            } catch (Exception $e) {
                                $db->rollBack();
                                throw $e;
                            }

                            //START VIDEO THUMB WORK
                            if (!empty($coreVideo->code) && !empty($coreVideo->type) && !empty($listVideo->photo_id)) {
                                $storageTable = Engine_Api::_()->getDbtable('files', 'storage');
                                $storageData = $storageTable->fetchRow(array('file_id = ?' => $listVideo->photo_id));
                                if (!empty($storageData)) {
                                    $thumbnail = $storageData->storage_path;

                                    $ext = ltrim(strrchr($thumbnail, '.'), '.');
                                    $thumbnail_parsed = @parse_url($thumbnail);

                                    if (@GetImageSize($thumbnail)) {
                                        $valid_thumb = true;
                                    } else {
                                        $valid_thumb = false;
                                    }

                                    if ($valid_thumb && $thumbnail && $ext && $thumbnail_parsed && in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
                                        $tmp_file = APPLICATION_PATH . '/temporary/link_' . md5($thumbnail) . '.' . $ext;
                                        $thumb_file = APPLICATION_PATH . '/temporary/link_thumb_' . md5($thumbnail) . '.' . $ext;
                                        $src_fh = fopen($thumbnail, 'r');
                                        $tmp_fh = fopen($tmp_file, 'w');
                                        stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);
                                        $image = Engine_Image::factory();
                                        $image->open($tmp_file)
                                                ->resize(120, 240)
                                                ->write($thumb_file)
                                                ->destroy();

                                        try {
                                            $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
                                                'parent_type' => 'video',
                                                'parent_id' => $coreVideo->video_id
                                            ));

                                            //REMOVE TEMP FILE
                                            @unlink($thumb_file);
                                            @unlink($tmp_file);
                                        } catch (Exception $e) {
                                            
                                        }

                                        $coreVideo->photo_id = $thumbFileRow->file_id;
                                        $coreVideo->save();
                                    }
                                }
                            }
                            //END VIDEO THUMB WORK
                            //START FETCH TAG
                            $videoTags = $listVideo->tags()->getTagMaps();
                            $tagString = '';

                            foreach ($videoTags as $tagmap) {

                                if ($tagString != '')
                                    $tagString .= ', ';
                                $tagString .= $tagmap->getTag()->getTitle();

                                $owner = Engine_Api::_()->getItem('user', $listVideo->owner_id);
                                $tags = preg_split('/[,]+/', $tagString);
                                $tags = array_filter(array_map("trim", $tags));
                                $coreVideo->tags()->setTagMaps($owner, $tags);
                            }
                            //END FETCH TAG

                            $likeTable = Engine_Api::_()->getDbtable('likes', 'core');
                            $likeTableName = $likeTable->info('name');

                            //START FETCH LIKES
                            $selectLike = $likeTable->select()
                                    ->from($likeTableName, 'like_id')
                                    ->where('resource_type = ?', 'siteevent_video')
                                    ->where('resource_id = ?', $eventVideoData->video_id);
                            $selectLikeDatas = $likeTable->fetchAll($selectLike);
                            foreach ($selectLikeDatas as $selectLikeData) {
                                $like = Engine_Api::_()->getItem('core_like', $selectLikeData->like_id);

                                $newLikeEntry = $likeTable->createRow();
                                $newLikeEntry->resource_type = 'video';
                                $newLikeEntry->resource_id = $like->resource_id;
                                $newLikeEntry->poster_type = 'user';
                                $newLikeEntry->poster_id = $like->poster_id;
                                $newLikeEntry->creation_date = $like->creation_date;
                                $newLikeEntry->save();
                            }
                            //END FETCH LIKES

                            $commentTable = Engine_Api::_()->getDbtable('comments', 'core');
                            $commentTableName = $commentTable->info('name');

                            //START FETCH COMMENTS
                            $selectLike = $commentTable->select()
                                    ->from($commentTableName, 'comment_id')
                                    ->where('resource_type = ?', 'siteevent_video')
                                    ->where('resource_id = ?', $eventVideoData->video_id);
                            $selectLikeDatas = $commentTable->fetchAll($selectLike);
                            foreach ($selectLikeDatas as $selectLikeData) {
                                $comment = Engine_Api::_()->getItem('core_comment', $selectLikeData->comment_id);

                                $newLikeEntry = $commentTable->createRow();
                                $newLikeEntry->resource_type = 'video';
                                $newLikeEntry->resource_id = $comment->resource_id;
                                $newLikeEntry->poster_type = 'user';
                                $newLikeEntry->poster_id = $comment->poster_id;
                                $newLikeEntry->body = $comment->body;
                                $newLikeEntry->creation_date = $comment->creation_date;
                                $newLikeEntry->like_count = $comment->like_count;
                                $newLikeEntry->save();
                            }
                            //END FETCH COMMENTS
                            //START UPDATE TOTAL LIKES IN EVENT-VIDEO TABLE
                            $selectLikeCount = $likeTable->select()
                                    ->from($likeTableName, array('COUNT(*) AS like_count'))
                                    ->where('resource_type = ?', 'siteevent_video')
                                    ->where('resource_id = ?', $coreVideo->video_id);
                            $selectLikeCounts = $likeTable->fetchAll($selectLikeCount);
// 						if (!empty($selectLikeCounts)) {
//   
//    
// 							$selectLikeCounts = $selectLikeCounts->toArray();
// 							$coreVideo->like_count = $selectLikeCounts[0]['like_count'];
// 							$coreVideo->save();
// 						}
                            //END UPDATE TOTAL LIKES IN EVENT-VIDEO TABLE
                            //START FETCH RATTING DATA
                            $selectVideoRating = $videoRating->select()
                                    ->from($videoRatingName)
                                    ->where('video_id = ?', $eventVideoData->video_id);

                            $videoRatingDatas = $videoRating->fetchAll($selectVideoRating);
                            if (!empty($videoRatingDatas)) {
                                $videoRatingDatas = $videoRatingDatas->toArray();
                            }

                            foreach ($videoRatingDatas as $videoRatingData) {

                                $reviewVideoRatingTable->insert(array(
                                    'videorating_id' => $coreVideo->video_id,
                                    'user_id' => $videoRatingData['user_id'],
                                    'rating' => $videoRatingData['rating']
                                ));
                            }
                            //END FETCH RATTING DATA
                            $reviewVideoTable->update(array('is_import' => 1), array('video_id = ?' => $eventVideoData->video_id));
                        }
                    }
                    //END FETCH VIDEO DATA
                } else {
                    //START FETCH VIDEO DATA


                    $selectSiteeventVideos = $siteeventVideoTable->select()
                            ->from($siteeventVideoTableName, array('video_id', 'event_id'))
                            ->where('is_import != ?', 1)
                            ->group('video_id');
                    $siteeventVideoDatas = $siteeventVideoTable->fetchAll($selectSiteeventVideos);
                    foreach ($siteeventVideoDatas as $siteeventVideoData) {
                        $siteeventVideo = Engine_Api::_()->getItem('video', $siteeventVideoData->video_id);
                        if (!empty($siteeventVideo)) {
                            $db = $reviewVideoTable->getAdapter();
                            $db->beginTransaction();

                            try {
                                $eventVideo = $reviewVideoTable->createRow();
                                $eventVideo->event_id = $siteeventVideoData->event_id;
                                $eventVideo->title = $siteeventVideo->title;
                                $eventVideo->description = $siteeventVideo->description;
                                $eventVideo->search = $siteeventVideo->search;
                                $eventVideo->owner_id = $siteeventVideo->owner_id;
                                $eventVideo->creation_date = $siteeventVideo->creation_date;
                                $eventVideo->modified_date = $siteeventVideo->modified_date;

                                $eventVideo->view_count = 1;
                                if ($siteeventVideo->view_count > 0) {
                                    $eventVideo->view_count = $siteeventVideo->view_count;
                                }

                                $eventVideo->comment_count = $siteeventVideo->comment_count;
                                $eventVideo->type = $siteeventVideo->type;
                                $eventVideo->code = $siteeventVideo->code;
                                $eventVideo->rating = $siteeventVideo->rating;
                                $eventVideo->status = $siteeventVideo->status;
                                $eventVideo->file_id = 0;
                                $eventVideo->duration = $siteeventVideo->duration;
                                $eventVideo->is_import = 1;
                                $eventVideo->save();
                                $db->commit();
                            } catch (Exception $e) {
                                $db->rollBack();
                                throw $e;
                            }

                            //START VIDEO THUMB WORK
                            if (!empty($eventVideo->code) && !empty($eventVideo->type) && !empty($siteeventVideo->photo_id)) {
                                $storageTable = Engine_Api::_()->getDbtable('files', 'storage');
                                $storageData = $storageTable->fetchRow(array('file_id = ?' => $siteeventVideo->photo_id));
                                if (!empty($storageData)) {
                                    $thumbnail = $storageData->storage_path;

                                    $ext = ltrim(strrchr($thumbnail, '.'), '.');
                                    $thumbnail_parsed = @parse_url($thumbnail);

                                    if (@GetImageSize($thumbnail)) {
                                        $valid_thumb = true;
                                    } else {
                                        $valid_thumb = false;
                                    }

                                    if ($valid_thumb && $thumbnail && $ext && $thumbnail_parsed && in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
                                        $tmp_file = APPLICATION_PATH . '/temporary/link_' . md5($thumbnail) . '.' . $ext;
                                        $thumb_file = APPLICATION_PATH . '/temporary/link_thumb_' . md5($thumbnail) . '.' . $ext;
                                        $src_fh = fopen($thumbnail, 'r');
                                        $tmp_fh = fopen($tmp_file, 'w');
                                        stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);
                                        $image = Engine_Image::factory();
                                        $image->open($tmp_file)
                                                ->resize(120, 240)
                                                ->write($thumb_file)
                                                ->destroy();

                                        try {
                                            $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
                                                'parent_type' => 'siteevent_video',
                                                'parent_id' => $eventVideo->video_id
                                            ));

                                            //REMOVE TEMP FILE
                                            @unlink($thumb_file);
                                            @unlink($tmp_file);
                                        } catch (Exception $e) {
                                            
                                        }

                                        $eventVideo->photo_id = $thumbFileRow->file_id;
                                        $eventVideo->save();
                                    }
                                }
                            }
                            //END VIDEO THUMB WORK
                            //START FETCH TAG
                            $videoTags = $siteeventVideo->tags()->getTagMaps();
                            $tagString = '';

                            foreach ($videoTags as $tagmap) {

                                if ($tagString != '')
                                    $tagString .= ', ';
                                $tagString .= $tagmap->getTag()->getTitle();

                                $owner = Engine_Api::_()->getItem('user', $siteeventVideo->owner_id);
                                $tags = preg_split('/[,]+/', $tagString);
                                $tags = array_filter(array_map("trim", $tags));
                                $eventVideo->tags()->setTagMaps($owner, $tags);
                            }
                            //END FETCH TAG

                            $likeTable = Engine_Api::_()->getDbtable('likes', 'core');
                            $likeTableName = $likeTable->info('name');

                            //START FETCH LIKES
                            $selectLike = $likeTable->select()
                                    ->from($likeTableName, 'like_id')
                                    ->where('resource_type = ?', 'video')
                                    ->where('resource_id = ?', $siteeventVideoData->video_id);
                            $selectLikeDatas = $likeTable->fetchAll($selectLike);
                            foreach ($selectLikeDatas as $selectLikeData) {
                                $like = Engine_Api::_()->getItem('core_like', $selectLikeData->like_id);

                                $newLikeEntry = $likeTable->createRow();
                                $newLikeEntry->resource_type = 'siteevent_video';
                                $newLikeEntry->resource_id = $eventVideo->video_id;
                                $newLikeEntry->poster_type = 'user';
                                $newLikeEntry->poster_id = $like->poster_id;
                                $newLikeEntry->creation_date = $like->creation_date;
                                $newLikeEntry->save();
                            }
                            //END FETCH LIKES

                            $commentTable = Engine_Api::_()->getDbtable('comments', 'core');
                            $commentTableName = $commentTable->info('name');

                            //START FETCH COMMENTS
                            $selectLike = $commentTable->select()
                                    ->from($commentTableName, 'comment_id')
                                    ->where('resource_type = ?', 'video')
                                    ->where('resource_id = ?', $siteeventVideoData->video_id);
                            $selectLikeDatas = $commentTable->fetchAll($selectLike);
                            foreach ($selectLikeDatas as $selectLikeData) {
                                $comment = Engine_Api::_()->getItem('core_comment', $selectLikeData->comment_id);

                                $newLikeEntry = $commentTable->createRow();
                                $newLikeEntry->resource_type = 'siteevent_video';
                                $newLikeEntry->resource_id = $eventVideo->video_id;
                                $newLikeEntry->poster_type = 'user';
                                $newLikeEntry->poster_id = $comment->poster_id;
                                $newLikeEntry->body = $comment->body;
                                $newLikeEntry->creation_date = $comment->creation_date;
                                $newLikeEntry->like_count = $comment->like_count;
                                $newLikeEntry->save();
                            }
                            //END FETCH COMMENTS
                            //START UPDATE TOTAL LIKES IN EVENT-VIDEO TABLE
                            $selectLikeCount = $likeTable->select()
                                    ->from($likeTableName, array('COUNT(*) AS like_count'))
                                    ->where('resource_type = ?', 'siteevent_video')
                                    ->where('resource_id = ?', $eventVideo->video_id);
                            $selectLikeCounts = $likeTable->fetchAll($selectLikeCount);
                            if (!empty($selectLikeCounts)) {
                                $selectLikeCounts = $selectLikeCounts->toArray();
                                $eventVideo->like_count = $selectLikeCounts[0]['like_count'];
                                $eventVideo->save();
                            }
                            //END UPDATE TOTAL LIKES IN EVENT-VIDEO TABLE
                            //START FETCH RATTING DATA
                            $selectVideoRating = $videoRating->select()
                                    ->from($videoRatingName)
                                    ->where('video_id = ?', $siteeventVideoData->video_id);

                            $videoRatingDatas = $videoRating->fetchAll($selectVideoRating);
                            if (!empty($videoRatingDatas)) {
                                $videoRatingDatas = $videoRatingDatas->toArray();
                            }

                            foreach ($videoRatingDatas as $videoRatingData) {

                                $reviewVideoRatingTable->insert(array(
                                    'videorating_id' => $eventVideo->video_id,
                                    'user_id' => $videoRatingData['user_id'],
                                    'rating' => $videoRatingData['rating']
                                ));
                            }
                            //END FETCH RATTING DATA
                            $siteeventVideoTable->update(array('is_import' => 0), array('video_id = ?' => $siteeventVideoData->video_id));
                        }
                    }
                }
            }

            if (isset($values['siteevent_show_video']) && ($type_video_value != $values['siteevent_show_video'])) {
                if (!empty($values['siteevent_show_video'])) {

                    $db->query("UPDATE `engine4_activity_actiontypes` SET `enabled` = '1' WHERE `engine4_activity_actiontypes`.`type` = 'video_siteevent' ");
                    $db->query("UPDATE `engine4_activity_actiontypes` SET `enabled` = '0' WHERE `engine4_activity_actiontypes`.`type` = 'siteevent_video_new' ");
                } elseif (empty($values['siteevent_show_video'])) {

                    $db->query("UPDATE `engine4_activity_actiontypes` SET `enabled` = '1' WHERE `engine4_activity_actiontypes`.`type` = 'siteevent_video_new' ");
                    $db->query("UPDATE `engine4_activity_actiontypes` SET `enabled` = '0' WHERE `engine4_activity_actiontypes`.`type` = 'video_siteevent' ");
                }
            }

            // Okay, save
            foreach ($values as $key => $value) {
                Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
            }

            if (isset($values['siteevent_show_video'])) {
                if (!empty($values['siteevent_show_video'])) {
                    Engine_Api::_()->getDbtable('menuItems', 'core')->update(array('enabled' => 0), array('name = ?' => 'siteevent_admin_submain_setting_tab', 'module = ?' => 'siteevent'));
                    Engine_Api::_()->getDbtable('menuItems', 'core')->update(array('enabled' => 0), array('name = ?' => 'siteevent_admin_submain_utilities_tab', 'module = ?' => 'siteevent'));
                } else {
                    Engine_Api::_()->getDbtable('menuItems', 'core')->update(array('enabled' => 1), array('name = ?' => 'siteevent_admin_submain_setting_tab', 'module = ?' => 'siteevent'));
                    Engine_Api::_()->getDbtable('menuItems', 'core')->update(array('enabled' => 1), array('name = ?' => 'siteevent_admin_submain_utilities_tab', 'module = ?' => 'siteevent'));
                }
            }
            return $this->_helper->redirector->gotoRoute(array('action' => 'show-video'));
        }
    }

    public function readmeAction() {

        $this->view->faq = 0;
        $this->view->faq_type = $this->_getParam('faq_type', 'general');
    }

    //ACTION FOR DELETE THE EVENT
    public function deleteCategoryAction() {

        $this->_helper->layout->setLayout('admin-simple');
        $category_id = $this->_getParam('category_id');

        $cat_dependency = $this->_getParam('cat_dependency');

        $this->view->category_id = $category_id;

        //GET CATEGORIES TABLE
        $tableCategory = Engine_Api::_()->getDbTable('categories', 'siteevent');
        $tableCategoryName = $tableCategory->info('name');

        //GET EVENT TABLE
        $tableSiteevent = Engine_Api::_()->getDbtable('events', 'siteevent');

        if ($this->getRequest()->isPost()) {
            //if($cat_dependency != 0) {
            //IF SUB-CATEGORY AND 3RD LEVEL CATEGORY IS MAPPED
            $previous_cat_profile_type = $tableCategory->getProfileType(null, $category_id);

            if ($previous_cat_profile_type) {

                //SELECT EVENTS WHICH HAVE THIS CATEGORY
                $events = $tableSiteevent->getCategoryList($category_id, 'category_id');

                foreach ($events as $event) {

                    //DELETE ALL MAPPING VALUES FROM FIELD TABLES
                    Engine_Api::_()->fields()->getTable('siteevent_event', 'values')->delete(array('item_id = ?' => $event->event_id));
                    Engine_Api::_()->fields()->getTable('siteevent_event', 'search')->delete(array('item_id = ?' => $event->event_id));

                    //UPDATE THE PROFILE TYPE OF ALREADY CREATED EVENTS
                    $tableSiteevent->update(array('profile_type' => 0), array('event_id = ?' => $event->event_id));

                    //GET REVIEW TABLE
                    $reviewTable = Engine_Api::_()->getDbTable('reviews', 'siteevent');
                    $reviewTableName = $reviewTable->info('name');

                    //REVIEW PROFILE TYPE UPDATION WORK
                    $reviewIds = $reviewTable->select()
                            ->from($reviewTableName, 'review_id')
                            ->where('resource_id = ?', $event->event_id)
                            ->where('resource_type = ?', 'siteevent_event')
                            ->query()
                            ->fetchAll(Zend_Db::FETCH_COLUMN)
                    ;
                    if (!empty($reviewIds)) {
                        foreach ($reviewIds as $reviewId) {
                            //DELETE ALL MAPPING VALUES FROM FIELD TABLES
                            Engine_Api::_()->fields()->getTable('siteevent_review', 'values')->delete(array('item_id = ?' => $reviewId));
                            Engine_Api::_()->fields()->getTable('siteevent_review', 'search')->delete(array('item_id = ?' => $reviewId));

                            //UPDATE THE PROFILE TYPE OF ALREADY CREATED REVIEWS
                            $reviewTable->update(array('profile_type_review' => 0), array('resource_id = ?' => $reviewId));
                        }
                    }
                }
            }

            //SITEEVENT TABLE SUB-CATEGORY/3RD LEVEL DELETE WORK
            $tableSiteevent->update(array('subcategory_id' => 0, 'subsubcategory_id' => 0), array('subcategory_id = ?' => $category_id));
            $tableSiteevent->update(array('subsubcategory_id' => 0), array('subsubcategory_id = ?' => $category_id));

            $tableCategory->delete(array('cat_dependency = ?' => $category_id, 'subcat_dependency = ?' => $category_id));
            $tableCategory->delete(array('category_id = ?' => $category_id));

            //}
            //GET URL
            $url = $this->_helper->url->url(array('action' => 'categories', 'controller' => 'settings', 'perform' => 'add', 'category_id' => 0));
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRedirect' => $url,
                'parentRedirectTime' => 1,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
            ));
        }

        $this->renderScript('admin-settings/delete-category.tpl');
    }

    public function getWidgetParamsAction() {

        $content_id = isset($_GET['content_id']) ? $_GET['content_id'] : 0;
        $toValues = $toValuesArray = array();
        $params = array('starttime' => '', 'endtime' => '');
        $toValuesString = '';
        if ($content_id) {

            //GET CONTENT TABLE
            $tableContent = Engine_Api::_()->getDbtable('content', 'core');
            $tableContentName = $tableContent->info('name');

            //GET CONTENT
            $params = $tableContent->select()
                    ->from($tableContentName, array('params'))
                    ->where('content_id = ?', $content_id)
                    ->where('name = ?', 'siteevent.special-events')
                    ->query()
                    ->fetchColumn();

            if (!empty($params)) {
                $params = Zend_Json_Decoder::decode($params);
                if (isset($params['toValues']) && !empty($params['toValues'])) {
                    $toValues = $params['toValues'];
                    if (!empty($toValues)) {
                        $toValues = explode(',', $toValues);
                        $toValues = array_unique($toValues);
                        $toValuesString = implode(',', $toValues);
                        $toValuesArray = array();
                        foreach ($toValues as $key => $id) {
                            $event = Engine_Api::_()->getItem('siteevent_event', $id);
                            if ($event instanceof Core_Model_Item_Abstract) {
                                $toValuesArray[$key]['id'] = $id;
                                $toValuesArray[$key]['title'] = $event->getTitle();
                            }
                        }
                    }
                }
            }
        }
        $this->view->toValuesArray = $toValuesArray;
        $this->view->toValuesString = $toValuesString;
    }

    public function integrationsAction() {

        $pluginName = 'siteevent';
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteevent_admin_main', array(), 'siteevent_admin_main_integrations');
    }

    //ACTION FOR AD SHOULD BE DISPLAY OR NOT ON PAGES
    public function adsettingsAction() {

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteevent_admin_main', array(), 'siteevent_admin_main_ads');

        //FORM
        $this->view->form = $form = new Siteevent_Form_Admin_Adsettings();

        //CHECK THAT COMMUNITY AD PLUGIN IS ENABLED OR NOT
        $communityadEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad');
        if ($communityadEnabled) {
            $this->view->ismoduleenabled = $ismoduleenabled = 1;
        } else {
            $this->view->ismoduleenabled = $ismoduleenabled = 0;
        }

        //CHECK FORM VALIDATION
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            foreach ($values as $key => $value) {
                if ($key != 'submit' && $key != 'note') {
                    Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
                }
            }
        }
    }

    //ACTION FOR THE LANGUAGE FILE CHANGE DURING THE UPGRADE.
    public function languageAction() {

        //START LANGUAGE WORK
        Engine_Api::_()->getApi('language', 'siteevent')->languageChanges();
        //END LANGUAGE WORK
        $redirect = $this->_getParam('redirect', false);
        if ($redirect == 'install') {
            $this->_redirect('install/manage');
        } elseif ($redirect == 'query') {
            $this->_redirect('install/manage/complete');
        }
    }
    
    //SITEEVENTPAID - PACKAGES SETTINGS TAB
    public function packageSettingsAction() {

        //TAB CREATION
        $this->view->navigation = $this->_navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('siteevent_admin_main', array(), 'siteeventticket_admin_main_ticket');
    
        $this->view->navigationGeneral = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('siteeventticket_admin_main_ticket', array(), 'siteevent_admin_main_packagesettings');
        
        $previousPackageSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.package.setting',1);

        $this->view->form = $form = new Siteevent_Form_Admin_Settings_Package();
        if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
             $values = $form->getValues();
            //UNSET ALL CHECKBOX VALUES BEFORE WE SET NEW VALUES.
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.package.setting', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.package.information')) {
              Engine_Api::_()->getApi('settings', 'core')->removeSetting('siteevent.package.information');
            }

           foreach ($values as $key => $value) {
                 if ($key != 'save' && $key != 'is_remove_note') {
                     Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
                 }
           }

           if ($previousPackageSetting != $values['siteevent_package_setting']) {
                  $menuitemsTable = Engine_Api::_()->getDbTable('menuitems', 'core');
                  if(!empty($values['siteevent_package_setting'])) {
                    $menuitemsTable->update(array('params' => '{"route":"siteevent_package"}'), array('name = ?' => 'siteevent_main_create', 'module = ?' => "siteevent", "menu = ?" => "siteevent_main"));
                    $menuitemsTable->update(array('params' => '{"route":"siteevent_package"}'), array('name = ?' => 'siteevent_quick_create', 'module = ?' => "siteevent", "menu = ?" => "siteevent_quick"));
                  }
                  else {
                    $menuitemsTable->update(array('params' => '{"route":"siteevent_general","action":"create"}'), array('name = ?' => 'siteevent_main_create', 'module = ?' => "siteevent", "menu = ?" => "siteevent_main"));
                    $menuitemsTable->update(array('params' => '{"route":"siteevent_general","action":"create"}'), array('name = ?' => 'siteevent_quick_create', 'module = ?' => "siteevent", "menu = ?" => "siteevent_quick"));                      
                  }
           }           
         }
      }

}
