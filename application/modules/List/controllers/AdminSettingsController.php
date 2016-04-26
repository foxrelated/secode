<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: AdminSettingsController.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_AdminSettingsController extends Core_Controller_Action_Admin {

	//ACTION FOR GLOBAL SETTINGS
  public function indexAction() {

		if(isset($_POST) && !empty($_POST['list_lsettings']) ) { 
			$_POST['list_lsettings'] = trim($_POST['list_lsettings']); 
		}
		
	  $db = Zend_Db_Table_Abstract::getDefaultAdapter();
		if (isset($_POST['list_locationfield']) && $_POST['list_locationfield'] == '0') {
			$db->query("UPDATE `engine4_core_menuitems` SET `enabled` = '0' WHERE `engine4_core_menuitems`.`name` = 'list_main_location' LIMIT 1 ;");
		} else {
			$db->query("UPDATE `engine4_core_menuitems` SET `enabled` = '1' WHERE `engine4_core_menuitems`.`name` = 'list_main_location' LIMIT 1 ;");
		}
		
    $list_global_form_content = array('list_location', 'list_report', 'list_share', 'list_socialshare', 'list_rating', 'list_printer', 'list_tellafriend', 'list_captcha_post', 'list_proximitysearch', 'list_checkcomment_widgets', 'list_sponsored_image', 'list_sponsored_color', 'list_feature_image', 'list_featured_color', 'list_page', 'list_proximity_search_kilometer', 'list_title_turncation', 'list_requried_description', 'list_status_show', 'list_title_turncationsponsored', 'submit', "list_browseorder", "list_network", "list_default_show", "list_map_sponsored", "list_map_city", "list_map_zoom","list_manifestUrlS","list_manifestUrlP","list_code_share", "list_locationfield","list_expirydate_enabled", "list_tinymceditor", "list_categorywithslug", "list_description_allow");

    $oldLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.map.city', "World");
		$this->view->isModsSupport = Engine_Api::_()->list()->isModulesSupport();
    include_once APPLICATION_PATH . '/application/modules/List/controllers/license/license1.php';
    $newLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.map.city', "World");

		if($oldLocation != $newLocation) {
			$this->setDefaultMapCenterPoint($oldLocation, $newLocation);
		}
  }

	//ACTION FOR LEVEL SETTINGS
  public function levelAction()
  {
		//GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('list_admin_main', array(), 'list_admin_main_level');

    //GET LEVEL ID
    if( null != ($id = $this->_getParam('id')) ) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }

    if( !$level instanceof Authorization_Model_Level ) {
      throw new Engine_Exception('missing level');
    }

    $id = $level->level_id;

    //MAKE FORM
    $this->view->form = $form = new List_Form_Admin_Settings_Level(array(
      'public' => ( in_array($level->type, array('public')) ),
      'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
    ));
    $form->level_id->setValue($id);

    //POPULATE DATA
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $form->populate($permissionsTable->getAllowed('list_listing', $id, array_keys($form->getValues())));

    //CHECK POST
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    //CHECK VALIDITY
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    //PROCESS
    $values = $form->getValues();
    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();		
    try
    {			
      //SET PERMISSION
      $permissionsTable->setAllowed('list_listing', $id, $values);

      //COMMIT
      $db->commit();
    }catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
  }

	//ACTION FOR WIDGET SETTINGS
  public function widgetSettingsAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('list_admin_main', array(), 'list_admin_main_widget');

		//MAKE FORM
    $this->view->form = $form = new List_Form_Admin_Widget();

		//SAVE DATA
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $lisst_admin_tab = 'widgets_settings';
      $values = $form->getValues();
      Engine_Api::_()->getApi("settings", "core")->setSetting("list_ajax_widgets_list", array("0" => "0", "1" => "0", "2" => "0", "3" => "0", "4" => "0"));
      Engine_Api::_()->getApi("settings", "core")->setSetting("list_ajax_widgets_layout", array("0" => "0", "1" => "0", "2" => "0"));
     
			foreach ($values as $key => $value) {
				Engine_Api::_()->getApi("settings", "core")->setSetting($key, $value);
			}
    }
  }

	//ACTION FOR GETTING THE CATGEORIES, SUBCATEGORIES AND 3RD LEVEL CATEGORIES
  public function categoriesAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('list_admin_main', array(), 'list_admin_main_categories');

    //GET TASK
    if (isset($_POST['task'])) {
      $task = $_POST['task'];
    } elseif (isset($_GET['task'])) {
      $task = $_GET['task'];
    } else {
      $task = "main";
    }

    //GET CATEGORIES TABLE
    $tableCategory = Engine_Api::_()->getDbTable('categories', 'list');
    $tableCategoryName = $tableCategory->info('name');

		//GET STORAGE API
		$this->view->storage = Engine_Api::_()->storage();

    //GET LISTING TABLE
    $tableList = Engine_Api::_()->getDbtable('listings', 'list');

    if ($task == "savecat") {

      //GET CATEGORY ID
      $category_id = $_GET['cat_id'];

      $cat_title_withoutparse = $_GET['cat_title'];

      //GET CATEGORY TITLE
      $cat_title = str_replace("'", "\'", trim($_GET['cat_title']));

      //GET CATEGORY DEPENDANCY
      $cat_dependency = $_GET['cat_dependency'];
      $subcat_dependency = $_GET['subcat_dependency'];
      if ($cat_title == "") {
        if ($category_id != "new") {
          if ($cat_dependency == 0) {
						
						//ON CATEGORY DELETE
            $row_ids = $tableCategory->getSubCategories($category_id);
            foreach ($row_ids as $values) {
              $tableCategory->delete(array('subcat_dependency = ?' => $values->category_id, 'cat_dependency = ?' => $values->category_id));
              $tableCategory->delete(array('category_id = ?' => $values->category_id));
            }

						//SELECT LISTINGS WHICH HAVE THIS CATEGORY
						$rows = $tableList->getCategoryList($category_id);

						if (!empty($rows)) {
							foreach ($rows as $key => $listing_ids) {
								$listing_id = $listing_ids['listing_id'];

								//DELETE ALL MAPPING VALUES FROM FIELD TABLES
								Engine_Api::_()->fields()->getTable('list_listing', 'values')->delete(array('item_id = ?' => $listing_id));
								Engine_Api::_()->fields()->getTable('list_listing', 'search')->delete(array('item_id = ?' => $listing_id));

								//UPDATE THE PROFILE TYPE OF ALREADY CREATED LISTINGS
								$tableList->update(array('profile_type' => 0), array('listing_id = ?' => $listing_id));
							}
						}

						//LISTING TABLE CATEGORY DELETE WORK
            $tableList->update(array('category_id' => 0, 'subcategory_id' => 0, 'subsubcategory_id' => 0), array('category_id = ?' => $category_id));

            $tableCategory->delete(array('category_id = ?' => $category_id));

          } else {
            $tableCategory->update(array('category_name' => $cat_title), array('category_id = ?' => $category_id, 'cat_dependency = ?' => $cat_dependency));

						//SELECT LISTINGS WHICH HAVE THIS CATEGORY
						$rows = $tableList->getCategoryList($category_id);

						if (!empty($rows)) {
							foreach ($rows as $key => $listing_ids) {
								$listing_id = $listing_ids['listing_id'];

								//DELETE ALL MAPPING VALUES FROM FIELD TABLES
								Engine_Api::_()->fields()->getTable('list_listing', 'values')->delete(array('item_id = ?' => $listing_id));
								Engine_Api::_()->fields()->getTable('list_listing', 'search')->delete(array('item_id = ?' => $listing_id));

								//UPDATE THE PROFILE TYPE OF ALREADY CREATED LISTINGS
								$tableList->update(array('profile_type' => 0), array('listing_id = ?' => $listing_id));
							}
						}

						//LIST TABLE SUB-CATEGORY/3RD LEVEL DELETE WORK
						$tableList->update(array('subcategory_id' => 0, 'subsubcategory_id' => 0), array('subcategory_id = ?' => $category_id));
						$tableList->update(array('subsubcategory_id' => 0), array('subsubcategory_id = ?' => $category_id));

            $tableCategory->delete(array('cat_dependency = ?' => $category_id, 'subcat_dependency = ?' => $category_id));
            $tableCategory->delete(array('category_id = ?' => $category_id));
          }
        }
        //SEND AJAX CONFIRMATION
        echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><script type='text/javascript'>";
        echo "window.parent.removecat('$category_id');";
        echo "</script></head><body></body></html>";
        exit();
      } else {
        if ($category_id == 'new') {
          $row_info = $tableCategory->fetchRow($tableCategory->select()->from($tableCategoryName, 'max(cat_order) AS cat_order'));
          $cat_order = $row_info['cat_order'] + 1;
          $row = $tableCategory->createRow();
          $row->category_name = $cat_title_withoutparse;
          $row->cat_order = $cat_order;
          $row->cat_dependency = $cat_dependency;
          $row->subcat_dependency = $subcat_dependency;
          $newcat_id = $row->save();
        } else {
          $tableCategory->update(array('category_name' => $cat_title_withoutparse), array('category_id = ?' => $category_id));
          $newcat_id = $category_id;
        }

        //SEND AJAX CONFIRMATION
        echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><script type='text/javascript'>";
        echo "window.parent.savecat_result('$category_id', '$newcat_id', '$cat_title', '$cat_dependency', '$subcat_dependency');";
        echo "</script></head><body></body></html>";
        exit();
      }
    } elseif ($task == "changeorder") {
      $divId = $_GET['divId'];
      $listOrder = explode(",", $_GET['listorder']);
      //RESORT CATEGORIES
      if ($divId == "categories") {
        for ($i = 0; $i < count($listOrder); $i++) {
          $category_id = substr($listOrder[$i], 4);
          $tableCategory->update(array('cat_order' => $i + 1), array('category_id = ?' => $category_id));
        }
      } elseif (substr($divId, 0, 7) == "subcats") {
        for ($i = 0; $i < count($listOrder); $i++) {
          $category_id = substr($listOrder[$i], 4);
          $tableCategory->update(array('cat_order' => $i + 1), array('category_id = ?' => $category_id));
        }
      } elseif (substr($divId, 0, 11) == "treesubcats") {
        for ($i = 0; $i < count($listOrder); $i++) {
          $category_id = substr($listOrder[$i], 4);
          $tableCategory->update(array('cat_order' => $i + 1), array('category_id = ?' => $category_id));
        }
      }
    }

    $categories = array();
    $category_info = $tableCategory->getCategories(null);
    foreach ($category_info as $value) {
      $sub_cat_array = array();
      $subcategories = $tableCategory->getAllCategories($value->category_id, 'subcategory_id', 0, 'subcategory_id', 0, 0, null, null);
      foreach ($subcategories as $subresults) {
        $subsubcategories = $tableCategory->getAllCategories($subresults->category_id, 'subsubcategory_id', 0, 'subsubcategory_id', 0, 0, null, null);
        $treesubarrays[$subresults->category_id] = array();

        foreach ($subsubcategories as $subsubcategoriesvalues) {

					//GET TOTAL LISTING COUNT
					$subsubcategory_list_count = $tableList->getListingsCount($subsubcategoriesvalues->category_id, 'subsubcategory_id', 0);

          $treesubarrays[$subresults->category_id][] = $treesubarray = array('tree_sub_cat_id' => $subsubcategoriesvalues->category_id,
              'tree_sub_cat_name' => $subsubcategoriesvalues->category_name,
							'count' => $subsubcategory_list_count,
							'file_id' => $subsubcategoriesvalues->file_id,
              'order' => $subsubcategoriesvalues->cat_order);
        }

				//GET TOTAL LISTINGS COUNT
				$subcategory_list_count = $tableList->getListingsCount($subresults->category_id, 'subcategory_id', 0);

         $sub_cat_array[] = $tmp_array = array('sub_cat_id' => $subresults->category_id,
            'sub_cat_name' => $subresults->category_name,
            'tree_sub_cat' => $treesubarrays[$subresults->category_id],
            'count' => $subcategory_list_count,
						'file_id' => $subresults->file_id,
            'order' => $subresults->cat_order);
      }

			//GET TOTAL LISTINGS COUNT
			$category_list_count = $tableList->getListingsCount($value->category_id, 'category_id', 0);

      $categories[] = $category_array = array('category_id' => $value->category_id,
          'category_name' => $value->category_name,
          'order' => $value->cat_order,
          'count' => $category_list_count,
					'file_id' => $value->file_id,
          'sub_categories' => $sub_cat_array);
    }

		$this->view->categories = $categories;
  }

	//ACTION FOR MAPPING OF LISTINGS
	Public function mappingCategoryAction()
	{
		//SET LAYOUT
		$this->_helper->layout->setLayout('admin-simple');

		//GET CATEGORY ID
		$this->view->catid = $catid = $this->_getParam('catid');

		//GET CATEGORY TITLE
		$this->view->oldcat_title = $oldcat_title = $this->_getParam('oldcat_title');

		//GET CATEGORY DEPENDANCY
		$this->view->subcat_dependency = $subcat_dependency = $this->_getParam('subcat_dependency');

    //CREATE FORM
    $this->view->form = $form = new List_Form_Admin_Settings_Mapping();

		$this->view->close_smoothbox = 0;

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

		if( $this->getRequest()->isPost()){ 

			//GET FORM VALUES
			$values = $form->getValues();

			//GET LISTING TABLE
			$tableList = Engine_Api::_()->getDbtable('listings', 'list');

			//GET CATEGORY TABLE
			$tableCategory = Engine_Api::_()->getDbtable('categories', 'list');

			//ON CATEGORY DELETE
			$rows = $tableCategory->getSubCategories($catid);
			foreach ($rows as $row) {
				$tableCategory->delete(array('subcat_dependency = ?' => $row->category_id, 'cat_dependency = ?' => $row->category_id));
				$tableCategory->delete(array('category_id = ?' => $row->category_id));
			}

			$previous_cat_profile_type = Engine_Api::_()->getDbTable('profilemaps', 'list')->getProfileType($catid);
			$new_cat_profile_type = Engine_Api::_()->getDbTable('profilemaps', 'list')->getProfileType($values['new_category_id']);

			//SELECT LISTINGS WHICH HAVE THIS CATEGORY
			if($previous_cat_profile_type != $new_cat_profile_type) {
				$rows = $tableList->getCategoryList($catid);
				if (!empty($rows)) {
					foreach ($rows as $key => $listing_ids) {
						$listing_id = $listing_ids['listing_id'];

						//DELETE ALL MAPPING VALUES FROM FIELD TABLES
						Engine_Api::_()->fields()->getTable('list_listing', 'values')->delete(array('item_id = ?' => $listing_id));
						Engine_Api::_()->fields()->getTable('list_listing', 'search')->delete(array('item_id = ?' => $listing_id));

						//UPDATE THE PROFILE TYPE OF ALREADY CREATED LISTINGS
						$tableList->update(array('profile_type' => $new_cat_profile_type), array('listing_id = ?' => $listing_id));
					}
				}
			}

			//LISTING TABLE CATEGORY DELETE WORK
			if(isset($values['new_category_id']) && !empty($values['new_category_id']) ) {
				$tableList->update(array('category_id' => $values['new_category_id']), array('category_id = ?' => $catid));
			}
			else {
				$tableList->update(array('category_id' => 0), array('category_id = ?' => $catid));
			}

			$tableCategory->delete(array('category_id = ?' => $catid));
   	}

		$this->view->close_smoothbox = 1;
	}

	//ACTION FOR ADD THE CATEGORY ICON
	Public function addIconAction()
	{
		//SET LAYOUT
		$this->_helper->layout->setLayout('admin-simple');

		//GET CATEGORY ID
		$this->view->category_id = $category_id = $this->_getParam('category_id');
		$category = Engine_Api::_()->getItem('list_category', $category_id);

    //CREATE FORM
    $this->view->form = $form = new List_Form_Admin_Settings_Addicon();

		$this->view->close_smoothbox = 0;

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

		//UPLOAD PHOTO
		if( isset($_FILES['photo']) && is_uploaded_file($_FILES['photo']['tmp_name']) )
		{
			//UPLOAD PHOTO
			$photoFile = $category->setPhoto($_FILES['photo']);

			//UPDATE FILE ID IN CATEGORY TABLE
			if(!empty($photoFile->file_id)) {
				$category->file_id = $photoFile->file_id;
				$category->save();
			}
		}

		$this->view->close_smoothbox = 1;
	}

	//ACTION FOR EDIT THE CATEGORY ICON
	Public function editIconAction()
	{
		//SET LAYOUT
		$this->_helper->layout->setLayout('admin-simple');

		//GET CATEGORY ID
		$this->view->category_id = $category_id = $this->_getParam('category_id');

		//GET CATEGORY ITEM
		$category = Engine_Api::_()->getItem('list_category', $category_id);

    //CREATE FORM
    $this->view->form = $form = new List_Form_Admin_Settings_Editicon();

		$this->view->close_smoothbox = 0;

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

		//UPLOAD PHOTO
		if( isset($_FILES['photo']) && is_uploaded_file($_FILES['photo']['tmp_name']) )
		{
			//UPLOAD PHOTO
			$photoFile = $category->setPhoto($_FILES['photo']);

			//UPDATE FILE ID IN CATEGORY TABLE
			if(!empty($photoFile->file_id)) {
				$previous_file_id = $category->file_id;
				$category->file_id = $photoFile->file_id;
				$category->save();
			
				//DELETE PREVIOUS CATEGORY ICON
				$file = Engine_Api::_()->getItem('storage_file', $previous_file_id);
				$file->delete();
			}
		}

		$this->view->close_smoothbox = 1;
	}

  //ACTION FOR DELETE THE CATEGORY ICON
  public function deleteIconAction()
  {
		//SET LAYOUT
		$this->_helper->layout->setLayout('admin-simple');

		//GET CATEGORY ID
		$this->view->category_id = $category_id = $this->_getParam('category_id');

		//GET CATEGORY ITEM
		$category = Engine_Api::_()->getItem('list_category', $category_id);

		$this->view->close_smoothbox = 0;

		if( $this->getRequest()->isPost() && !empty($category->file_id)){

			//DELETE CATEGORY ICON
			$file = Engine_Api::_()->getItem('storage_file', $category->file_id);
			$file->delete();

			//UPDATE FILE ID IN CATEGORY TABLE
			$category->file_id = 0;
			$category->save();

			$this->view->close_smoothbox = 1;
   	}
		$this->renderScript('admin-settings/delete-icon.tpl');
	}

  //ACTINO FOR SEARCH FORM TAB
  public function formSearchAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('list_admin_main', array(), 'list_admin_main_form_search');

		//GET SEARCH TABLE
    $tableSearchForm = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');

    //CHECK POST
    if ($this->getRequest()->isPost()) {
		
			//BEGIN TRANSCATION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      $values = $_POST;
      $rowCategory = $tableSearchForm->getFieldsOptions('list', 'category_id');
			$rowLocation = $tableSearchForm->getFieldsOptions('list', 'location');
      $defaultCategory = 0;
			$defaultAddition = 0;
			$count = 1;
      try {
        foreach ($values['order'] as $key => $value) {
					$multiplyAddition = $count*5;
          $tableSearchForm->update(array('order' =>  $defaultAddition + $defaultCategory + $key + $multiplyAddition + 1), array('searchformsetting_id = ?' => (int) $value));

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
		$select = $tableSearchForm->select()->where('module = ?', 'list')->order('order');

		$this->view->searchForm = $tableSearchForm->fetchAll($select);
  }

  //ACTION FOR DISPLAY/HIDE FIELDS OF SEARCH FORM
  public function diplayFormAction() {
  	
    $field_id = $this->_getParam('id');
    $display = $this->_getParam('display');
    if (!empty($field_id)) {
      Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->update(array('display' => $display), array('module = ?' => 'list', 'searchformsetting_id = ?' => (int) $field_id));
    }
    $this->_redirect('admin/list/settings/form-search');
  }

	//ACTION FOR SHOW STATISTICS OF LISTING PLUGIN
  public function statisticAction() {

		//GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('list_admin_main', array(), 'list_admin_main_statistic');

    //GET LISTING TABLE
    $listingTable = Engine_Api::_()->getDbtable('listings', 'list');
    $listingTableName = $listingTable->info('name');

		//GET THE TOTAL LISTINGS
    $select = $listingTable->select()->from($listingTableName, 'count(*) AS totallisting');
    $this->view->totalList = $select->query()->fetchColumn();

		//GET THE TOTAL PUBLISH LISTINGS
    $select = $listingTable->select()->from($listingTableName, 'count(*) AS totalpublish')->where('draft = ?', 1);
    $this->view->totalPublish = $select->query()->fetchColumn();

		//GET THE TOTAL DRAFT LISTINGS
    $select = $listingTable->select()->from($listingTableName, 'count(*) AS totaldrafted')->where('draft = ?', 0);
    $this->view->totalDrafted = $select->query()->fetchColumn();

		//GET THE TOTAL CLOSED LISTINGS
    $select = $listingTable->select()->from($listingTableName, 'count(*) AS totalclosed')->where('closed = ?', 1);
    $this->view->totalClosed = $select->query()->fetchColumn();

		//GET THE TOTAL OPEN LISTINGS
    $select = $listingTable->select()->from($listingTableName, 'count(*) AS totalopen')->where('closed = ?', 0);
    $this->view->totalOpen = $select->query()->fetchColumn();

		//GET THE TOTAL APPROVE LISTINGS
    $select = $listingTable->select()->from($listingTableName, 'count(*) AS totalapproved')->where('approved = ?', 1);
    $this->view->totalapproved = $select->query()->fetchColumn();

		//GET THE TOTAL DIS-APPROVE LISTINGS
    $select = $listingTable->select()->from($listingTableName, 'count(*) AS totaldisapproved')->where('approved = ?', 0);
    $this->view->totaldisapproved = $select->query()->fetchColumn();

		//GET THE TOTAL FEATURED LISTINGS
    $select = $listingTable->select()->from($listingTableName, 'count(*) AS totalfeatured')->where('featured = ?', 1);
    $this->view->totalfeatured = $select->query()->fetchColumn();

		//GET THE TOTAL SPONSERED LISTINGS
    $select = $listingTable->select()->from($listingTableName, 'count(*) AS totalsponsored')->where('sponsored = ?', 1);
    $this->view->totalsponsored = $select->query()->fetchColumn();

		//GET THE TOTAL REVIEWS
    $reviewtable = Engine_Api::_()->getDbtable('reviews', 'list');
    $review_tablename = $reviewtable->info('name');
    $reviewselect = $reviewtable->select()->from($review_tablename, 'count(*) AS totalreview');
    $this->view->totalreview = $reviewselect->query()->fetchColumn();

		//GET THE TOTAL DISCUSSIONES
    $discussiontable = Engine_Api::_()->getDbtable('topics', 'list');
    $discussion_tablename = $discussiontable->info('name');
    $discussionselect = $discussiontable->select()->from($discussion_tablename, 'count(*) AS totaldiscussion');
    $this->view->totaldiscussion = $discussionselect->query()->fetchColumn();

		//GET THE TOTAL POSTS
    $discussionposttable = Engine_Api::_()->getDbtable('posts', 'list');
    $discussionpost_tablename = $discussionposttable->info('name');
    $discussionpostselect = $discussionposttable->select()->from($discussionpost_tablename, 'count(*) AS totalpost');
    $this->view->totaldiscussionpost = $discussionpostselect->query()->fetchColumn();

    include_once APPLICATION_PATH . '/application/modules/List/controllers/license/license2.php';

		//GET THE TOTAL PHOTOS
    $phototable = Engine_Api::_()->getDbtable('photos', 'list');
    $photo_tablename = $phototable->info('name');
    $photoselect = $phototable->select()->from($photo_tablename, 'count(*) AS totalphoto');
    $totalphotolistings = $phototable->fetchRow($photoselect)->toarray();
    $this->view->totalphotopost = $totalphotolistings['totalphoto'];

		//GET THE TOTAL VIDEOS
    $videotable = Engine_Api::_()->getDbtable('clasfvideos', 'list');
    $video_tablename = $videotable->info('name');
    $videoselect = $videotable->select()->from($video_tablename, 'count(*) AS totalvideo');
    $this->view->totalvideopost = $videoselect->query()->fetchColumn();

		//GET THE TOTAL COMMENTS COUNT
    $commentselect = $listingTable->select()->from($listingTableName, 'sum(comment_count) AS totalcomments');
    $this->view->totalcommentpost = $commentselect->query()->fetchColumn();
  }

	//ACTION FOR SET THE DEFAULT MAP CENTER POINT
  public function setDefaultMapCenterPoint($oldLocation, $newLocation) {

    if ($oldLocation !== $newLocation && $newLocation !== "World" && $newLocation !== "world") {
        $locationResults = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getLatLong(array('location' => $newLocation, 'module' => 'Listing / Catalog Showcase'));
        if(!empty($locationResults['latitude']) && !empty($locationResults['longitude'])) {
            $latitude = $locationResults['latitude'];
            $longitude = $locationResults['longitude'];
        }

      Engine_Api::_()->getApi('settings', 'core')->setSetting('list.map.latitude', $latitude);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('list.map.longitude', $longitude);
    }
  }

	//ACTION FOR SHOWING THE FAQ
  public function faqAction() {

		//GET NAGIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('list_admin_main', array(), 'list_admin_main_faq');
  }

  public function readmeAction() {

  }

}
