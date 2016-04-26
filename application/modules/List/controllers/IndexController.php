<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: IndexController.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_IndexController extends Core_Controller_Action_Standard {

  protected $_navigation;

	//COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
  public function init() {

		//AUTHORIZATION CHECK
    if (!$this->_helper->requireAuth()->setAuthParams('list_listing', null, 'view')->isValid())
      return;
  }

  //NONE USER SPECIFIC METHODS
  public function indexAction() {

		//GET CORE VERSION
    $coreVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core')->version;

    if ($coreVersion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled();
    }
  }

  //NONE USER SPECIFIC METHODS
  public function homeAction() {

		//GET CORE VERSION
    $coreVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core')->version;

    if ($coreVersion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled();
    }
  }

  //ACTION FOR BROWSE LOCATION PAGES.
	public function mapAction() {
	
		$enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.locationfield', 1);
		
		if (empty($enableLocation)) {
			return $this->_forward('notfound', 'error', 'core');
		} else {
			$this->_helper->content->setEnabled();
		}
  }

  //ACTION FOR BROWSE LOCATION PAGES.
	public function mobilemapAction() {

		$enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.locationfield', 1);
		
		if (empty($enableLocation)) {
			return $this->_forward('notfound', 'error', 'core');
		} else {
			$this->_helper->content->setEnabled();
		}
  }
  
	//ACTION FOR SHOWING SPONSORED LISTINGS IN WIDGET
  public function homesponsoredAction() {

		//CORE SETTINGS API
		$settings = Engine_Api::_()->getApi('settings', 'core');

		//SEAOCORE API
		$this->view->seacore_api = Engine_Api::_()->seaocore();
	
    //RETURN THE OBJECT OF LIMIT PER PAGE FROM CORE SETTING TABLE
    $limit_list = (int) $settings->getSetting('list.sponserdlist.widgets', 4);
    $limit_list_horizontal = $limit_list * 2;

		//GET LISTING TABLE
		$listTable = Engine_Api::_()->getDbTable('listings', 'list');
    $totalList = $listTable->getListing('Total Sponsored List');

    //GET COUNT
    $totalCount = $totalList->count();

    //RETRIVE THE VALUE OF START INDEX
    $startindex = $_GET['startindex'];

    if ($startindex > $totalCount) {
      $startindex = $totalCount - $limit_list;
    }

    if ($startindex < 0) {
      $startindex = 0;
		}

    //RETRIVE THE VALUE OF BUTTON DIRECTION
    $this->view->direction = $_GET['direction'];
    $values['start_index'] = $startindex;

    //GET LISTINGS
    $this->view->lists = $listTable->getListing('Sponsored List AJAX', $values);

		$this->view->sponserdListsCount = $settings->getSetting('list.sponserdlist.widgets', 4);
  }

	//ACTION FOR VIEW LISTING PROFILE PAGE
  public function viewAction() {

    //GET VIEWER ID
		$viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

		//GET LISTING ID AND OBJECT
    $listing_id = $this->_getParam('listing_id');
    $list = Engine_Api::_()->getItem('list_listing', $this->_getParam('listing_id'));

    if (empty($list)) {
      return $this->_forward('notfound', 'error', 'core');
    }

		//WHO CAN VIEW THE LISTINGS
    if( !$this->_helper->requireAuth()->setAuthParams($list, null, 'view')->isValid() ) {
			return $this->_forward('requireauth', 'error', 'core');
    }

		//ADD CSS
    $this->view->headLink()
           ->prependStylesheet($this->view->layout()->staticBaseUrl . 'application/modules/List/externals/styles/style_list.css');

		//SET LIST SUBJECT
		Engine_Api::_()->core()->setSubject($list);

    //GET USER LEVEL ID
    if (!empty($viewer_id)) {
      $level_id = $viewer->level_id;
    } else {
      $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
    }

		//GET LEVEL SETTING
		$can_view = Engine_Api::_()->authorization()->getPermission($level_id, 'list_listing', 'view');

		//AUTHORIZATION CHECK
    if($can_view != 2 && ((empty($list->draft) || empty($list->search) || empty($list->approved)) && ($list->owner_id != $viewer_id))) {
      return $this->_forward('requireauth', 'error', 'core');
    }

		//INCREMENT IN NUMBER OF VIEWS
		$list->view_count++;
		$list->save();
		
		//SET LISTING VIEW DETAILS
		Engine_Api::_()->getDbtable('vieweds', 'list')->setVieweds($listing_id, $viewer_id);

		//GET LIST OWNER LEVEL ID
		$owner_level_id = Engine_Api::_()->getItem('user', $list->owner_id)->level_id;

		//PROFILE STYLE IS ALLOWED OR NOT
		$style_perm = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('list_listing', $owner_level_id, 'style');
		if ($style_perm) {

			//GET STYLE TABLE
			$tableStyle = Engine_Api::_()->getDbtable('styles', 'core');

			//MAKE QUERY
			$getStyle = $tableStyle->select()
							->from($tableStyle->info('name'), array('style'))
							->where('type = ?', 'list_listing')
							->where('id = ?', $list->getIdentity())
							->query()
							->fetchColumn();

			if (!empty($getStyle)) {
				$this->view->headStyle()->appendStyle($getStyle);
			}
		}

    if (null != ($tab = $this->_getParam('tab'))) {
      //provide widgties page
      $friend_tab_function = <<<EOF
                                        var content_id = "$tab";
                                        this.onload = function()
                                        {
                                                tabContainerSwitch($('main_tabs').getElement('.tab_' + content_id));
                                        }
EOF;
      $this->view->headScript()->appendScript($friend_tab_function);
    }

		//GET CORE VERSION
    $coreVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core')->version;
    if ($coreVersion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled();
    }
  }

  //ACTION FOR MANAGING THE LISTINGS
  public function manageAction() {

		//ONLY LOGGED IN USER CAN VIEW THIS PAGE
    if (!$this->_helper->requireUser()->isValid())
      return;

    $list_manage = Zend_Registry::get('list_manage');

		//GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

		//CREATION PRIVACY CHECK
    if( !$this->_helper->requireAuth()->setAuthParams('list_listing', null, 'create')->isValid() )
        return;

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('list_main');

    //GET QUICK NAVIGATION
    $this->view->quickNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('list_quick');

		//GET EDIT AND DELETE SETTINGS
    $this->view->can_edit = $this->_helper->requireAuth()->setAuthParams('list_listing', null, 'edit')->checkRequire();
    $this->view->can_delete = $this->_helper->requireAuth()->setAuthParams('list_listing', null, 'delete')->checkRequire();

    $this->view->allowed_upload_photo = $allowed_upload_photo = 0;
    $this->view->allowed_upload_video = 0;

    //ABLE TO UPLOAD VIDEO OR NOT
    $allowed_upload_videoEnable = Engine_Api::_()->list()->enableVideoPlugin();
    $allowed_upload_video_video = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'video', 'create');
    $allowed_upload_video = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'list_listing', 'video');
    if ((!empty($allowed_upload_video) && !empty($allowed_upload_videoEnable) && !empty($allowed_upload_video_video))) {
      $this->view->allowed_upload_video = 1;
    }

		//ABLE TO ADD PHOTO OR NOT
    $allowed_upload_photo = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'list_listing', 'photo');
    if ($allowed_upload_photo) {
      $this->view->allowed_upload_photo = $allowed_upload_photo;
    }

		//MAKE FORM
    $this->view->form = $form = new List_Form_Search();
    $form->removeElement('show');

    //RATTING IS ENABLED OR NOT
    $this->view->ratngShow = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('list.rating', 1);

    //PROCESS FORM
    unset($form->getElement('orderby')->options['']);
    if ($form->isValid($this->_getAllParams())) {
      $values = $form->getValues();
    } else {
      $values = array();
    }

    if (empty($list_manage)) {
      exit();
    }

    //MAKE DATA ARRAY
    $values['user_id'] = $viewer_id;
    $values['type'] = 'manage';
   
		//GET CUSTOM FIELD VALUES
		$customFieldValues = array_intersect_key($values, $form->getFieldElements());

    //GET PAGINATOR
    $this->view->paginator = Engine_Api::_()->getDbTable('listings', 'list')->getListsPaginator($values, $customFieldValues);
    $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('list.page', 10);
    $this->view->paginator->setItemCountPerPage($items_count);
    $this->view->paginator->setCurrentPageNumber($values['page']);
		$this->view->current_count = $this->view->paginator->getTotalItemCount();

    //MAXIMUM ALLOWED LISTINGS
    $this->view->quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'list_listing', 'max');

		$this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
  }

  //ACTION FOR CREATING A NEW LISTING
  public function createAction() {

    //ONLY LOGGED IN USER CAN CREATE
    if (!$this->_helper->requireUser()->isValid())
      return;

		//CHECK FOR CREATION PRIVACY
    if (!$this->_helper->requireAuth()->setAuthParams('list_listing', null, 'create')->isValid())
      return;

		//GET DEFAULT PROFILE TYPE ID
		$this->view->defaultProfileId = $defaultProfileId = Engine_Api::_()->getDbTable('metas', 'list')->defaultProfileId();
    
    $this->view->getCategoriesCount = Engine_Api::_()->getDbTable('categories', 'list')->getCategoriesCount();
    if($this->view->getCategoriesCount == 0) {
      $this->view->getDefaultProfileType = Engine_Api::_()->getDbTable('metas', 'list')->getDefaultProfileType();
    }    

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('list_main');

    $currentbase_time = time();
    $check_result_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('listing.check.var');
    $base_result_time = Engine_Api::_()->getApi('settings', 'core')->getSetting('listing.base.time');
    $get_result_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('listing.get.path');
    $listing_time_var = Engine_Api::_()->getApi('settings', 'core')->getSetting('listing.time.var');
    $mod_type_name = Engine_Api::_()->getApi('settings', 'core')->getSetting('listing.var.name');
    $word_name = strrev($mod_type_name);
    $file_path = APPLICATION_PATH . '/application/modules/' . $get_result_show;
    $list_featured = Zend_Registry::get('list_featured');
    
    if (empty($list_featured)) {
      return;
    }

    if (($currentbase_time - $base_result_time > $listing_time_var) && empty($check_result_show)) {
      $is_file_exist = file_exists($file_path);
      if (!empty($is_file_exist)) {
        $fp = fopen($file_path, "r");
        while (!feof($fp)) {
          $get_file_content .= fgetc($fp);
        }
        fclose($fp);
        $listing_set_type = strstr($get_file_content, $word_name);
      }
      if (empty($listing_set_type)) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('list.view.attempt', 1);
        Engine_Api::_()->getApi('settings', 'core')->setSetting('list.flag.info', 1);
        return;
      } else {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('listing.check.var', 1);
      }
    }

		//MAKE FORM
		$this->view->form = $form = new List_Form_Create(array('defaultProfileId' => $defaultProfileId));

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
    $list_category = Zend_Registry::get('list_category');

		//COUNT LIST CREATED BY THIS USER AND GET ALLOWED COUNT SETTINGS
    $values['user_id'] = $viewer->getIdentity();
    $paginator = Engine_Api::_()->getDbTable('listings', 'list')->getListsPaginator($values);
    $this->view->current_count = $paginator->getTotalItemCount();
    $this->view->quota = $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'list_listing', 'max');

    $list_host = str_replace("www.", "", strtolower($_SERVER['HTTP_HOST']));

    $list_render = Zend_Registry::get('list_render');
    if (!empty($list_render)) {
      $this->view->list_render = Zend_Registry::get('list_render');
    }

    $this->view->expiry_setting = $expiry_setting = Engine_Api::_()->list()->expirySettings();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $table = Engine_Api::_()->getItemTable('list_listing');
      $db = $table->getAdapter();
      $db->beginTransaction();
      $user_level = $viewer->level_id;
      try {
        //Create list
        $values = array_merge($form->getValues(), array(
            'owner_type' => $viewer->getType(),
            'owner_id' => $viewer->getIdentity(),
            'featured' => Engine_Api::_()->authorization()->getPermission($user_level, 'list_listing', 'featured'),
            'sponsored' => Engine_Api::_()->authorization()->getPermission($user_level, 'list_listing', 'sponsored'),
            'approved' => Engine_Api::_()->authorization()->getPermission($user_level, 'list_listing', 'approved')
                ));

        if (empty($values['subcategory_id'])) {
          $values['subcategory_id'] = 0;
        }

        if (empty($values['subsubcategory_id'])) {
          $values['subsubcategory_id'] = 0;
        }

        if ($expiry_setting == 1 && $values['end_date_enable'] == 1) {
          // Convert times
          $oldTz = date_default_timezone_get();
          date_default_timezone_set($viewer->timezone);
          $end = strtotime($values['end_date']);
          date_default_timezone_set($oldTz);
          $values['end_date'] = date('Y-m-d H:i:s', $end);
        } elseif (isset($values['end_date'])) {
          unset($values['end_date']);
        }

        $list = $table->createRow();
        $list->setFromArray($values);

        if ($list->approved) {
          $list->approved_date = date('Y-m-d H:i:s');
        }

        if (!empty($list_category)) {
          $list->save();
          $listing_id = $list->listing_id;
        }

        
        //START PAGE INTEGRATION WORK
        $page_id = $this->_getParam('page_id');
        if (!empty($page_id)) {
					$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
					$moduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitepageintegration' ) ;
					if (!empty($moduleEnabled)) {
						$contentsTable = Engine_Api::_()->getDbtable('contents', 'sitepageintegration');
						$row = $contentsTable->createRow();
						$row->owner_id = $viewer_id;
						$row->resource_owner_id = $list->owner_id;
						$row->page_id = $page_id;
						$row->resource_type = 'list_listing';
						$row->resource_id = $list->listing_id;;
						$row->save();
					}
        }
        
        $business_id = $this->_getParam('business_id');
        if (!empty($business_id)) {
					$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
					$moduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitebusinessintegration' ) ;
					if (!empty($moduleEnabled)) {
						$contentsTable = Engine_Api::_()->getDbtable('contents', 'sitebusinessintegration');
						$row = $contentsTable->createRow();
						$row->owner_id = $viewer_id;
						$row->resource_owner_id = $list->owner_id;
						$row->business_id = $business_id;
						$row->resource_type = 'list_listing';
						$row->resource_id = $list->listing_id;;
						$row->save();
					}
        }
        //END PAGE INTEGRATION WORK

        
        //SET PHOTO
        if (!empty($values['photo'])) {
          $list->setPhoto($form->photo);
        }

				//ADDING TAGS
				$keywords = '';
				if (isset($values['tags']) && !empty($values['tags'])) {
					$tags = preg_split('/[,]+/', $values['tags']);
					$tags = array_filter(array_map("trim", $tags));
					$list->tags()->addTagMaps($viewer, $tags);

					foreach($tags as $tag) {
						$keywords .= " $tag";
					}
				}

        //SAVE CUSTOM VALUES
        $customfieldform = $form->getSubForm('fields');
        $customfieldform->setItem($list);
        $customfieldform->saveValues();

				//NOT SEARCHABLE IF SAVED IN DRAFT MODE
				if(empty($list->draft)) {
					$list->search = 0;
					$list->save();
				}
        
        if($this->view->getCategoriesCount == 0) {
          $list->profile_type = Engine_Api::_()->getDbTable('metas', 'list')->getDefaultProfileType();
          $list->save();
        }         

        //PRIVACY WORK
        $list_flag_info = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.flag.info', 0);
        if (empty($list_flag_info)) {
          $list_host = convert_uuencode($list_host);
          Engine_Api::_()->getApi('settings', 'core')->setSetting('list.view.attempt', $list_host);
        }

        $auth = Engine_Api::_()->authorization()->context;

        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

        if (empty($values['auth_view'])) {
          $values['auth_view'] = array("everyone");
        }

        if (empty($values['auth_comment'])) {
          $values['auth_comment'] = array("everyone");
        }

        $viewMax = array_search($values['auth_view'], $roles);
        $commentMax = array_search($values['auth_comment'], $roles);

        foreach ($roles as $i => $role) {
          $auth->setAllowed($list, $role, 'view', ($i <= $viewMax));
          $auth->setAllowed($list, $role, 'comment', ($i <= $commentMax));
        }

        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
        if (empty($values['auth_photo'])) {
          $values['auth_photo'] = array("registered");
        }

        if (!isset($values['auth_video']) && empty($values['auth_video'])) {
          $values['auth_video'] = array("registered");
        }

        $photoMax = array_search($values['auth_photo'], $roles);
				$videoMax = array_search($values['auth_video'], $roles);
        foreach ($roles as $i => $roles) {
          $auth->setAllowed($list, $roles, 'photo', ($i <= $photoMax));
					$auth->setAllowed($list, $roles, 'video', ($i <= $videoMax));
        }

        //COMMIT
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      if (!empty($listing_id)) {
        $list->setLocation();
      }
      $db->beginTransaction();
      try {
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $list, 'list_new');

        if ($action != null) {
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $list);
        }

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

			//UPDATE KEYWORDS IN SEARCH TABLE
			if(!empty($keywords)) {
				Engine_Api::_()->getDbTable('search', 'core')->update(array('keywords' => $keywords), array('type = ?' => 'list_listing', 'id = ?' => $list->listing_id));
			}

			//OVERVIEW IS ENABLED OR NOT
			$allowOverview = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'list_listing', 'overview');

	    //CHECK FOR LEVEL SETTING
			if ($allowOverview) {
				return $this->_helper->redirector->gotoRoute(array('action' => 'overview', 'listing_id' => $list->listing_id, 'saved' => '1'), 'list_specific', true);
			} 
			else if(Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'list_listing', 'photo')) {
				return $this->_helper->redirector->gotoRoute(array('listing_id' => $list->listing_id, 'saved' => '1'), 'list_albumspecific', true);
			} 
			else if (Engine_Api::_()->list()->allowVideo($list, $viewer)) { 
				return $this->_helper->redirector->gotoRoute(array('listing_id' => $list->listing_id, 'saved' => '1'), 'list_videospecific', true);
			}
			else{ 
				return $this->_helper->redirector->gotoRoute(array('listing_id' => $list->listing_id, 'user_id' => $list->owner_id, 'slug' => $list->getSlug()), 'list_entry_view', true);
			}
    }
		else {
      $results = $this->getRequest()->getPost();
      if (!empty($results) && $results['subcategory_id']) {

        $this->view->category_id = $results['category_id'];
        $subcategory_id = $results['subcategory_id'];

        $table = Engine_Api::_()->getDbtable('categories', 'list');
        $categoriesName = $table->info('name');

        $select = $table->select()->from($categoriesName, 'category_name')
                ->where("(category_id = $subcategory_id)");

        $row = $table->fetchRow($select);
        $this->view->subcategory_name = $row->category_name;
        return;
      }
    }
  }

  //ACTION FOR EDITING THE LIST
  public function editAction() {

    if (!$this->_helper->requireUser()->isValid())
      return;
    $this->view->TabActive = "edit";
    $this->view->listing_id = $listing_id = $this->_getParam('listing_id');
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $this->view->list = $list = Engine_Api::_()->getItem('list_listing', $listing_id);
    $previous_location = $list->location; 

    if (empty($list)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $listinfo = $list->toarray();
    $this->view->category_id = $listinfo['category_id'];
    $this->view->subcategory_id = $subcategory_id = $listinfo['subcategory_id'];

    $row = Engine_Api::_()->getDbtable('categories', 'list')->getCategory($subcategory_id);
    $this->view->subcategory_name = "";
    if (!empty($row)) {
      $this->view->subcategory_name = $row->category_name;
    }

    if (!Engine_Api::_()->core()->hasSubject('list_listing')) {
      Engine_Api::_()->core()->setSubject($list);
    }

    $this->view->allow_overview_of_owner = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'list_listing', 'overview');

    $this->view->allowed_upload_photo = $allowed_upload_photo = 0;
    $this->view->allowed_upload_video = 0;
    $subject_list = $list;

    if (Engine_Api::_()->list()->allowVideo($list, $viewer)) {
      $this->view->allowed_upload_video = 1;
    }
    $allowed_upload_photo = Engine_Api::_()->authorization()->isAllowed($subject_list, $viewer, 'photo');

    if ($allowed_upload_photo) {
      $this->view->allowed_upload_photo = $allowed_upload_photo;
    }

    if (!$this->_helper->requireSubject()->isValid())
      return;

    if (!$this->_helper->requireAuth()->setAuthParams($list, $viewer, 'edit')->isValid()) {
      return;
    }

		//GET DEFAULT PROFILE TYPE ID
		$this->view->defaultProfileId = $defaultProfileId = Engine_Api::_()->getDbTable('metas', 'list')->defaultProfileId();
    
    $this->view->getCategoriesCount = Engine_Api::_()->getDbTable('categories', 'list')->getCategoriesCount();
    if($this->view->getCategoriesCount == 0) {
      $this->view->getDefaultProfileType = Engine_Api::_()->getDbTable('metas', 'list')->getDefaultProfileType();
    }        

		//GET PROFILE MAPPING TABLE
		$tableProfilemaps = Engine_Api::_()->getDbTable('profilemaps', 'list');

		//GET PROFILE MAPPING ID
		$this->view->profileType = $previous_profile_type = $tableProfilemaps->getProfileType($list->category_id);

		if(isset($_POST['category_id']) && !empty($_POST['category_id'])) {
			$this->view->profileType = $tableProfilemaps->getProfileType($_POST['category_id']);
		}	

    //MAKE FORM
    $this->view->form = $form = new List_Form_Edit(array('item' => $list, 'defaultProfileId' => $defaultProfileId));

    if (!empty($list->draft)) {
      $form->removeElement('draft');
    }

    $form->removeElement('photo');
 
    $this->view->expiry_setting = $expiry_setting = Engine_Api::_()->list()->expirySettings();

    //SAVE LIST ENTRY
    if (!$this->getRequest()->isPost()) {

      //prepare tags
      $listTags = $list->tags()->getTagMaps();
      $tagString = '';

      foreach ($listTags as $tagmap) {

        if ($tagString != '')
          $tagString .= ', ';
        $tagString .= $tagmap->getTag()->getTitle();
      }

      $this->view->tagNamePrepared = $tagString;
      $form->tags->setValue($tagString);

      //etc
      $form->populate($list->toArray());

      if ($list->end_date && $list->end_date !='0000-00-00 00:00:00') {
        $form->end_date_enable->setValue(1);
        // Convert and re-populate times
        $end = strtotime($list->end_date);
        $oldTz = date_default_timezone_get();
        date_default_timezone_set($viewer->timezone);
        $end = date('Y-m-d H:i:s', $end);
        date_default_timezone_set($oldTz);

        $form->populate(array(
            'end_date' => $end,
        ));
      } else if (empty($list->end_date) ||  $list->end_date =='0000-00-00 00:00:00') {
        $date = (string) date('Y-m-d');
        $form->end_date->setValue($date . ' 00:00:00');
      }

      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

      foreach ($roles as $role) {
        if ($form->auth_view) {
          if (1 == $auth->isAllowed($list, $role, 'view')) {
            $form->auth_view->setValue($role);
          }
        }

        if ($form->auth_comment) {
          if (1 == $auth->isAllowed($list, $role, 'comment')) {
            $form->auth_comment->setValue($role);
          }
        }
      }

      $roles_photo = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
      foreach ($roles_photo as $role_photo) {
        if ($form->auth_photo) {
          if (1 == $auth->isAllowed($list, $role_photo, 'photo')) {
            $form->auth_photo->setValue($role_photo);
          }
        }
      }

      $videoEnable = Engine_Api::_()->list()->enableVideoPlugin();
      if ($videoEnable) {
        $roles_video = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
        foreach ($roles_video as $role_video) {
          if ($form->auth_video) {
            if (1 == $auth->isAllowed($list, $role_video, 'video')) {
              $form->auth_video->setValue($role_video);
            }
          }
        }
      }
      return;
    }

		//FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //GET FORM VALUES
    $values = $form->getValues();

		if ($expiry_setting == 1 && $values['end_date_enable'] == 1) {
			// Convert times
			$oldTz = date_default_timezone_get();
			date_default_timezone_set($viewer->timezone);
			$end = strtotime($values['end_date']);
			date_default_timezone_set($oldTz);
			$values['end_date'] = date('Y-m-d H:i:s', $end);
		} elseif ($expiry_setting == 1 && isset($values['end_date'])) {
			$values['end_date'] = NULL;
		} elseif (isset($values['end_date'])) {
			unset($values['end_date']);
		}

		if (isset($values['category_id']) && !empty($values['category_id'])) {
			$list->profile_type = $tableProfilemaps->getProfileType($values['category_id']);
			if($list->profile_type != $previous_profile_type) {

				$fieldvalueTable = Engine_Api::_()->fields()->getTable('list_listing', 'values');
				$fieldvalueTable->delete(array('item_id = ?' => $list->listing_id));

				Engine_Api::_()->fields()->getTable('list_listing', 'search')->delete(array(
								'item_id = ?' => $list->listing_id,
				));

				if(!empty($list->profile_type) && !empty($previous_profile_type)) {
						//PUT NEW PROFILE TYPE
						$fieldvalueTable->insert(array(
								'item_id' => $list->listing_id,
								'field_id' => $defaultProfileId,
								'index' => 0,
								'value' => $list->profile_type,
						));
				}
			}
			$list->save();
		}

    $tags = preg_split('/[,]+/', $values['tags']);
    $tags = array_filter(array_map("trim", $tags));

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {

      $list->setFromArray($values);
      $list->modified_date = date('Y-m-d H:i:s');
      $list->tags()->setTagMaps($viewer, $tags);
      $list->save();

      if(empty($list->location)) {
        Engine_Api::_()->getDbtable('locations', 'list')->delete(array('listing_id =?' => $list->listing_id));
      }
      elseif(!empty($list->location) && ($list->location != $previous_location)) {
        $list->setLocation();
      }          

      //SAVE CUSTOM FIELDS
      $customfieldform = $form->getSubForm('fields');
      $customfieldform->setItem($list);
      $customfieldform->saveValues();

			//NOT SEARCHABLE IF SAVED IN DRAFT MODE
			if(empty($list->draft)) {
				$list->search = 0;
				$list->save();
			}
      
      if($this->view->getCategoriesCount == 0) {
        $list->profile_type = Engine_Api::_()->getDbTable('metas', 'list')->getDefaultProfileType();
        $list->save();
      }          

      //CREATE AUTH STUFF HERE
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      $values = $form->getValues();
      if ($values['auth_view'])
        $auth_view = $values['auth_view'];
      else
        $auth_view = "everyone";
      $viewMax = array_search($auth_view, $roles);

      foreach ($roles as $i => $role) {
        $auth->setAllowed($list, $role, 'view', ($i <= $viewMax));
      }

      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      if ($values['auth_comment'])
        $auth_comment = $values['auth_comment'];
      else
        $auth_comment = "everyone";
      $commentMax = array_search($auth_comment, $roles);

      foreach ($roles as $i => $role) {
        $auth->setAllowed($list, $role, 'comment', ($i <= $commentMax));
      }
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
      if ($values['auth_photo'])
        $auth_photo = $values['auth_photo'];
      else
        $auth_photo = "registered";
      $photoMax = array_search($auth_photo, $roles);

      foreach ($roles as $i => $role) {
        $auth->setAllowed($list, $role, 'photo', ($i <= $photoMax));
      }

      $roles_video = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
      if (!isset($values['auth_video']) && empty($values['auth_video'])) {
        $values['auth_video'] = "registered";
      }

      $videoMax = array_search($values['auth_video'], $roles_video);
      foreach ($roles_video as $i => $role_video) {
        $auth->setAllowed($list, $role_video, 'video', ($i <= $videoMax));
      }

      $db->commit();
      $this->view->form = $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $list->setLocation();
    $db->beginTransaction();
    try {
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach ($actionTable->getActionsByObject($list) as $action) {
        $actionTable->resetActivityBindings($action);
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

		//RETURN TO MANAGE PAGE
    return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'list_general', true);
  }

  //ACTION TO SET OVERVIEW
  public function overviewAction() {

		//ONLY LOGGED IN USER CAN ADD OVERVIEW
    if (!$this->_helper->requireUser()->isValid())
      return;

		//GET VIEWER
		$viewer = Engine_Api::_()->user()->getViewer();

		//GET LISTING ID AND OBJECT
    $listing_id = $this->_getParam('listing_id');
    $this->view->list = $list = Engine_Api::_()->getItem('list_listing', $listing_id);

		//IF LIST IS NOT EXIST
    if (empty($list)) {
      return $this->_forward('notfound', 'error', 'core');
    }

    //AUTHORIZATION CHECK
    if (!$this->_helper->requireAuth()->setAuthParams($list, $viewer, 'edit')->isValid()) {
      return;
    }

    //OVERVIEW IS ALLOWED OR NOT
		$allowOverview = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'list_listing', 'overview');
    if ($allowOverview) { 
      $this->view->allow_overview_of_owner = 1;
    } 
		else { 
			return $this->_forward('requireauth', 'error', 'core'); 
		}

		//SELECTED TAB
		$this->view->TabActive = "overview";

		//GET SETTINGS
    $this->view->allowed_upload_video = Engine_Api::_()->list()->allowVideo($list, $viewer);
    $this->view->allowed_upload_photo = Engine_Api::_()->authorization()->isAllowed($list, $viewer, 'photo');

		$overview = '';
    if (!empty($list->overview)) {
      $overview = $list->overview;
		}

		//MAKE FORM
    $this->view->form = $form = new List_Form_Overview();

		//IF NOT POSTED
    if (!$this->getRequest()->isPost()) {
      $saved = $this->_getParam('saved');
      if (!empty($saved))
        $this->view->success = 'Your listing has been successfully created. You can enhance your listing from this dashboard by creating other components.';
    }

		//SAVE THE VALUE
    if ($this->getRequest()->isPost()) {
      $overview = $_POST['overview'];
      $list->overview = $overview;
      $list->save();
      $this->view->form = $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
    }

		//POPULATE FORM
    $values['overview'] = $overview;
    $form->populate($values);
  }

  //ACTION FOR EDIT STYLE OF LIST
  public function editstyleAction() {

		//ONLY LOGGED IN USER CAN EDIT THE STYLE
    if (!$this->_helper->requireUser()->isValid())
      return;

		//AUTHORIZATION CHECK
    if (!$this->_helper->requireAuth()->setAuthParams('list_listing', null, 'style')->isValid())
      return;

		//GET VIEWER
		$viewer = Engine_Api::_()->user()->getViewer();

		//GET LISTING ID AND OBJECT
    $listing_id = $this->_getParam('listing_id');
    $this->view->list = $list = Engine_Api::_()->getItem('list_listing', $listing_id);

    if (empty($list)) {
      return $this->_forward('notfound', 'error', 'core');
    }

    //AUTHORIZATION CHECK
    if (!$this->_helper->requireAuth()->setAuthParams($list, $viewer, 'edit')->isValid()) {
      return;
    }

    //OVERVIEW IS ALLOWED OR NOT
		$this->view->allow_overview_of_owner = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'list_listing', 'overview');

		//SELECTED TAB
    $this->view->TabActive = "style";

		//GET SETTINGS
    $this->view->allowed_upload_video = Engine_Api::_()->list()->allowVideo($list, $viewer);
		$this->view->allowed_upload_photo = Engine_Api::_()->authorization()->isAllowed($list, $viewer, 'photo');

    //MAKE FORM
    $this->view->form = $form = new List_Form_Style();

    //FETCH EXISTING ROWS
    $tableStyle = Engine_Api::_()->getDbtable('styles', 'core');
    $select = $tableStyle->select()
            ->where('type = ?', 'list_listing')
            ->where('id = ?', $listing_id)
            ->limit();
    $row = $tableStyle->fetchRow($select);

    //CHECK POST
    if (!$this->getRequest()->isPost()) {
      $form->populate(array('style' => ( null == $row ? '' : $row->style )));
      return;
    }

    //FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //PROCESS
    $style = $form->getValue('style');
    $style = strip_tags($style);

    $forbiddenStuff = array(
        '-moz-binding',
        'expression',
        'javascript:',
        'behaviour:',
        'vbscript:',
        'mocha:',
        'livescript:',
    );
    $style = str_replace($forbiddenStuff, '', $style);

    //SAVE ROW
    if (null == $row) {
      $row = $tableStyle->createRow();
      $row->type = 'list_listing';
      $row->id = $listing_id;
    }
    $row->style = $style;
    $row->save();
    $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
  }

  //ACTION FOR DELETE LISTING
  public function deleteAction() {

		//LOGGED IN USER CAN DELETE LISTING
    if (!$this->_helper->requireUser()->isValid())
      return;

		//GET LISTING ID AND OBJECT
    $listing_id = $this->_getParam('listing_id');
    $this->view->list = $list = Engine_Api::_()->getItem('list_listing', $listing_id);

		//GET VIEWER
		$viewer = Engine_Api::_()->user()->getViewer();

    //AUTHORIZATION CHECK
    if (!$this->_helper->requireAuth()->setAuthParams($list, $viewer, 'delete')->isValid()) {
      return;
    }

		//GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('list_main', array(), 'list_main_manage');

		//DELETE LIST AFTER CONFIRMATION
    if ($this->getRequest()->isPost() && $this->getRequest()->getPost('confirm') == true) {
      $list->delete();
      return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'list_general', true);
    }
  }

  //ACTION FOR CLOSE / OPEN LISTING
  public function closeAction() {

		//LOGGED IN USER CAN CLOSE LISTING
    if (!$this->_helper->requireUser()->isValid())
      return;

		//GET LISTING
    $list = Engine_Api::_()->getItem('list_listing', $this->_getParam('listing_id'));

		//GET VIEWER
		$viewer = Engine_Api::_()->user()->getViewer();

		//AUTHORIZATION CHECK
    if (!$this->_helper->requireAuth()->setAuthParams($list, $viewer, 'edit')->isValid()) {
      return;
    }

		//BEGIN TRANSCATION
    $db = Engine_Api::_()->getDbTable('listings', 'list')->getAdapter();
    $db->beginTransaction();

    try {
      $list->closed = empty($list->closed) ? 1 : 0;
      $list->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

		//RETURN TO MANAGE PAGE
    return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'list_general', true);
  }

  //ACTION FOR RATING FAQs
  public function ratingAction() {

		//GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

		//GET RATING
    $rating = $this->_getParam('rating');

		//GET FAQs ID
    $listing_id = $this->_getParam('listing_id');

		//GET RATING TABLE
    $tableRating = Engine_Api::_()->getDbtable('ratings', 'list');

		//BEGIN TRANSCATION
    $db = $tableRating->getAdapter();
    $db->beginTransaction();

    try {
      $tableRating->setFaqRating($listing_id, $viewer_id, $rating);

      $total = $tableRating->countRating($listing_id);

      $list = Engine_Api::_()->getItem('list_listing', $listing_id);

			//UPDATE CURRENT AVERAGE RATING IN FAQs TABLE
			$list->rating = $rating = $tableRating->getAvgRating($listing_id);

			//SAVE AND COMMIT
      $list->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $data = array();
    $data[] = array(
        'total' => $total,
        'rating' => $rating,
    );
    return $this->_helper->json($data);
    $data = Zend_Json::encode($data);
    $this->getResponse()->setBody($data);
  }

  //ACTION FOR CONSTRUCT TAG CLOUD
  public function tagscloudAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('list_main');

    //CONSTRUCTING TAG CLOUD
    $tag_array = array();
    $tag_cloud_array = Engine_Api::_()->getDbtable('listings', 'list')->getTagCloud('', 0);
    foreach ($tag_cloud_array as $vales) {

      $tag_array[$vales['text']] = $vales['Frequency'];
      $tag_id_array[$vales['text']] = $vales['tag_id'];
    }

    if (!empty($tag_array)) {

      $max_font_size = 18;
      $min_font_size = 12;
      $max_frequency = max(array_values($tag_array));
      $min_frequency = min(array_values($tag_array));
      $spread = $max_frequency - $min_frequency;

      if ($spread == 0) {
        $spread = 1;
      }

      $step = ($max_font_size - $min_font_size) / ($spread);

      $tag_data = array('min_font_size' => $min_font_size, 'max_font_size' => $max_font_size, 'max_frequency' => $max_frequency, 'min_frequency' => $min_frequency, 'step' => $step);

      $this->view->tag_data = $tag_data;
      $this->view->tag_id_array = $tag_id_array;
    }
    $this->view->tag_array = $tag_array;
  }

  //ACTION FOR TELL A FRIEND ABOUT LISTING
  public function tellafriendAction() {

		//SET LAYOUT
    $this->_helper->layout->setLayout('default-simple');

		//GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewr_id = $viewer->getIdentity();

    //GET FORM
    $this->view->form = $form = new List_Form_TellAFriend();

    if (!empty($viewr_id)) {
      $value['sender_email'] = $viewer->email;
      $value['sender_name'] = $viewer->displayname;
      $form->populate($value);
    }

    //FORM VALIDATION
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

			//GET LISTING ID AND OBJECT
      $listing_id = $this->_getParam('listing_id', $this->_getParam('id', null));
      $list = Engine_Api::_()->getItem('list_listing', $listing_id);

			//GET FORM VALUES
      $values = $form->getValues();

      //EXPLODE EMAIL IDS
      $reciver_ids = explode(',', $values['reciver_emails']);
      if (!empty($values['send_me'])) {
        $reciver_ids[] = $values['sender_email'];
      }
      $sender_email = $values['sender_email'];
      $heading = $list->title;

      //CHECK VALID EMAIL ID FORMAT
      $validator = new Zend_Validate_EmailAddress();
      $validator->getHostnameValidator()->setValidateTld(false);

      if (!$validator->isValid($sender_email)) {
        $form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid sender email address value'));
        return;
      }

      foreach ($reciver_ids as $reciver_id) {
        $reciver_id = trim($reciver_id, ' ');
        if (!$validator->isValid($reciver_id)) {
          $form->addError(Zend_Registry::get('Zend_Translate')->_('Please enter correct email address of the receiver(s).'));
          return;
        }
      }

      $sender = $values['sender_name'];
      $message = $values['message'];

      Engine_Api::_()->getApi('mail', 'core')->sendSystem($reciver_ids, 'LIST_TELLAFRIEND_EMAIL', array(
          'host' => $_SERVER['HTTP_HOST'],
          'sender' => $sender,
          'heading' => $heading,
          'message' => '<div>' . $message . '</div>',
          'object_link' => $list->getHref(),
          'email' => $sender_email,
          'queue' => false
      ));

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          //'parentRefreshTime' => '15',
          'format' => 'smoothbox',
          'messages' => Zend_Registry::get('Zend_Translate')->_('Your message to your friend has been sent successfully.')
      ));
    }
  }

	//ACTION FOR PRINTING THE LIST
  public function printAction() {

		//LAYOUT DEFAULT
    $this->_helper->layout->setLayout('default-simple');

		//GET LISTING ID AND OBJECT
    $listing_id = $this->_getParam('listing_id', $this->_getParam('id', null));
    $this->view->list = $list = Engine_Api::_()->getItem('list_listing', $listing_id);

		//IF LISTING IS NOT EXIST
    if (empty($list)) {
      return $this->_forward('notfound', 'error', 'core');
    }

    if ($list->category_id != 0) {
			$categoryTable = Engine_Api::_()->getDbtable('categories', 'list');
      $this->view->category_name = $categoryTable->getCategory($list->category_id)->category_name;
		
			if ($list->subcategory_id != 0) {
				$this->view->subcategory_name = $categoryTable->getCategory($list->subcategory_id)->category_name;
			}
		}

    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($list);
  }

	//ACTION FOR EDIT THE LOCATION
  public function editlocationAction() {

		//IF LOCATION SETTING IS ENABLED
    if (!Engine_Api::_()->list()->enableLocation()) {
      return $this->_forward('requireauth', 'error', 'core');
    }

		//GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

		//GET LISTING ID AND OBJECT
    $this->view->listing_id = $listing_id = $this->_getParam('listing_id');
    $this->view->list = $list = Engine_Api::_()->getItem('list_listing', $listing_id);

    //AUTHORIZATION CHECK
    if (!$this->_helper->requireAuth()->setAuthParams($list, $viewer, 'edit')->isValid()) {
      return;
    }

		//IF LISTING IS NOT EXIST
    if (empty($list)) {
      return $this->_forward('notfound', 'error', 'core');
    }

    //OVERVIEW IS ALLOWED OR NOT
		$this->view->allow_overview_of_owner = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'list_listing', 'overview');

		//WHICH TAB SHOULD COME ACTIVATE
    $this->view->TabActive = "location";

		//GET SETTINGS
    $this->view->allowed_upload_video = Engine_Api::_()->list()->allowVideo($list, $viewer);
		$this->view->allowed_upload_photo = Engine_Api::_()->authorization()->isAllowed($list, $viewer, 'photo');

		//GET LOCATION TABLE
		$locationTable = Engine_Api::_()->getDbtable('locations', 'list');

		//MAKE VALUE ARRAY
		$values = array();
    $value['id'] = $list->listing_id;

		//GET LOCATION
    $this->view->location = $location = $locationTable->getLocation($value);

    if (!empty($location)) {

			//MAKE FORM
      $this->view->form = $form = new List_Form_Location(array(
                  'item' => $list,
                  'location' => $location->location
              ));

			//CHECK POST
      if (!$this->getRequest()->isPost()) {
        $form->populate($location->toarray());
        return;
      }

      //FORM VALIDATION
      if (!$form->isValid($this->getRequest()->getPost())) {
        return;
      }

			//GET FORM VALUES
			$values = $form->getValues();
			unset($values['submit']);
			unset($values['location']);
			
			//UPDATE LOCATION
			$locationTable->update($values, array('listing_id = ?' => $listing_id));
      
      $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
    }
    $this->view->location = $locationTable->getLocation($value);
  }

	//ACTION FOR EDIT THE LISTING ADDRESS
  public function editaddressAction() {

		//GET LISTING ID AND OBJECT
    $listing_id = $this->_getParam('listing_id');
    $list = Engine_Api::_()->getItem('list_listing', $listing_id);

		//IF LIST IS NOT EXIST
    if (empty($list)) {
      return $this->_forward('notfound', 'error', 'core');
    }

		//MAKE FORM
    $this->view->form = $form = new List_Form_Address(array('item' => $list));

		//CHECK POST
    if (!$this->getRequest()->isPost()) {
      $form->populate($list->toArray());
      return;
    }

    //FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {

      $location = $_POST['location'];
      $list->location = $location;
      $list->save();
      
			//GET LOCATION TABLE
      $locationTable = Engine_Api::_()->getDbtable('locations', 'list');
      if (!empty($location)) {
				$list->setLocation();
        $locationTable->update(array('location' => $location), array('listing_id = ?' => $listing_id));
      } else {
        $locationTable->delete(array('listing_id = ?' => $listing_id));
      }

			$db->commit();
      
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 500,
          'parentRefresh' => 500,
          'messages' => array('Your listing location has been modified successfully.')
      ));
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

	//ACTION FOR SELECT SUB-CATEGORY
  public function selectsubcategoryAction() {

		//GET CATEGORY ID
    $category_id_temp = $_GET['category_id_temp'];
    if (empty($category_id_temp))
      return;
    
		//GET DATA
    $this->view->subcats = $data = array();
    $results = Engine_Api::_()->getDbTable('categories', 'list')->getSubCategories($category_id_temp);
    foreach ($results as $value) {
      $content_array['category_name'] = Zend_Registry::get('Zend_Translate')->_($value->category_name);
      $content_array['category_id'] = $value->category_id;
      $data[] = $content_array;
    }

    $this->view->subcats = $data;
  }

	//ACTION TO GET SUB-CATEGORY
  public function subCategoryAction() {

		//GET CATEGORY ID
    $category_id_temp = $this->_getParam('category_id_temp');

		//INTIALIZE ARRAY
		$this->view->subcats = $data = array();

		//RETURN IF CATEGORY ID IS EMPTY
    if (empty($category_id_temp))
      return;

		//GET CATEGORY TABLE
		$tableCategory = Engine_Api::_()->getDbTable('categories', 'list');

		//GET CATEGORY
    $category = $tableCategory->getCategory($category_id_temp);
    if (!empty($category->category_name)) {
      $categoryName = $tableCategory->getCategorySlug($category->category_name);
    }

		//GET SUB-CATEGORY
    $subCategories = $tableCategory->getSubCategories($category_id_temp);
  
    foreach ($subCategories as $subCategory) {
      $content_array = array();
      $content_array['category_name'] = Zend_Registry::get('Zend_Translate')->_($subCategory->category_name);
      $content_array['category_id'] = $subCategory->category_id;
      $content_array['categoryname_temp'] = $categoryName;
      $data[] = $content_array;
    }
 
    $this->view->subcats = $data;
  }

  //ACTION FOR FETCHING SUB-CATEGORY
  public function subsubCategoryAction() {

		//GET SUB-CATEGORY ID
    $subcategory_id_temp = $this->_getParam('subcategory_id_temp');

		//INTIALIZE ARRAY
		$this->view->subsubcats = $data = array();

		//RETURN IF SUB-CATEGORY ID IS EMPTY
    if(empty($subcategory_id_temp))
      return;
    
		//GET CATEGORY TABLE
		$tableCategory = Engine_Api::_()->getDbTable('categories', 'list');

		//GET SUB-CATEGORY
    $subCategory = $tableCategory->getCategory($subcategory_id_temp);
    if (!empty($subCategory->category_name)) {
      $subCategoryName = $tableCategory->getCategorySlug($subCategory->category_name);
    }

		//GET 3RD LEVEL CATEGORIES
    $subCategories = $tableCategory->getSubCategories($subcategory_id_temp);
    foreach ($subCategories as $subCategory) {
      $content_array = array();
      $content_array['category_name'] = Zend_Registry::get('Zend_Translate')->_($subCategory->category_name);
      $content_array['category_id'] = $subCategory->category_id;
      $content_array['categoryname_temp'] = $subCategoryName;
      $data[] = $content_array;
    }
    $this->view->subsubcats = $data;
  }

  //ACTION FOR PUBLISH LISTING
  public function publishAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //SMOOTHBOX
    if (null == $this->_helper->ajaxContext->getCurrentContext()) {
      $this->_helper->layout->setLayout('default-simple');
    } else {
      //NO LAYOUT
      $this->_helper->layout->disableLayout(true);
    }

		//CHECK POST
    if (!$this->getRequest()->isPost())
      return;

		//GET LISTING ID AND OBJECT
		$listing_id = $this->view->listing_id = $this->_getParam('listing_id');
    $list = Engine_Api::_()->getItem('list_listing', $listing_id);

    //GET VIEWER
		$viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

		//AUTHORIZATION CHECK
    if (!$this->_helper->requireAuth()->setAuthParams($list, $viewer, 'edit')->isValid()) {
      return;
    }

		//ONLY OWNER CAN PUBLISH THE LISTING
    if ($viewer_id == $list->owner_id) {
      $this->view->permission = true;
      $this->view->success = false;
      $db = Engine_Api::_()->getDbtable('listings', 'list')->getAdapter();
      $db->beginTransaction();
      try {

				if (!empty($_POST['search'])) {
					$list->search = 1;
				}
				else {
					$list->search = 0;
				}

        $list->modified_date = new Zend_Db_Expr('NOW()');
        $list->draft = 1;
        $list->save();
        $db->commit();
        $this->view->success = true;
      } catch (Exception $e) {
        $db->rollback();
        throw $e;
      }
    } else {
      $this->view->permission = false;
    }

    $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
        'messages' => array('Successfully Published !')
    ));
  }

	//ACTION FOR AJAX TABBED WIDGET
  public function ajaxhomelistAction() {

		//GET SETTINGS
    $tab_show_values = $this->_getParam('tab_show', null);
    $this->view->active_tab1 = 0;
    $this->view->active_tab2 = 0;
    $this->view->active_tab3 = 0;
    $this->view->active_tab4 = 0;
    $this->view->active_tab5 = 0;
    $this->view->active_tab_list = 0;
    $this->view->active_tab_image = 0;

    $ShowViewArray = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.ajax.widgets.layout', array("0" => "1", "1" => "2", "2" => "3"));
    switch ($tab_show_values) {

      case "Recently Posted":
        $this->view->listings = $list = Engine_Api::_()->getDbTable('listings', 'list')->getListing('Recently Posted');
        $this->view->active_tab1 = 1;
        $this->view->active_tab_list = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('list.recent.widgets', 10);
        $this->view->active_tab_image = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('list.recent.thumbs', 15);
        break;

      case "Most Viewed":
        $this->view->listings = $list = Engine_Api::_()->getDbTable('listings', 'list')->getListing('Most Viewed');
        $this->view->active_tab2 = 1;
        $this->view->active_tab_list = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('list.popular.widgets', 10);
        $this->view->active_tab_image = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('list.popular.thumbs', 15);
        break;

      case "Random":
        $this->view->listings = $list = Engine_Api::_()->getDbTable('listings', 'list')->getListing('Random');
        $this->view->active_tab3 = 1;
        $this->view->active_tab_list = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('list.random.widgets', 10);
        $this->view->active_tab_image = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('list.random.thumbs', 15);
        break;

      case "Featured":
        $this->view->listings = $list = Engine_Api::_()->getDbTable('listings', 'list')->getListing('Featured');
        $this->view->active_tab4 = 1;
        $this->view->active_tab_list = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('list.featured.list', 10);
        $this->view->active_tab_image = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('list.featured.thumbs', 15);
        break;

      case "Sponosred":
        $this->view->listings = $list = Engine_Api::_()->getDbTable('listings', 'list')->getListing('Sponosred');
        $this->view->active_tab5 = 1;
        $this->view->active_tab_list = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('list.sponsored.list', 10);
        $this->view->active_tab_image = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('list.sponosred.thumbs', 15);
        break;
    }

		//GET LAYOUT SETTING
    $this->view->list_view = 0;
    $this->view->grid_view = 0;
    $this->view->map_view = 0;
    $defaultOrder = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.ajax.layouts.oder', 1);
    $this->view->defaultView = -1;
    if (in_array("1", $ShowViewArray)) {
      $this->view->list_view = 1;
      if ($this->view->defaultView == -1 || $defaultOrder == 1)
        $this->view->defaultView = 0;
    }

    if (in_array("2", $ShowViewArray)) {
      $this->view->grid_view = 1;
      if ($this->view->defaultView == -1 || $defaultOrder == 2)
        $this->view->defaultView = 1;
    }

    if (in_array("3", $ShowViewArray)) {
      $this->view->map_view = 1;
      if ($this->view->defaultView == -1 || $defaultOrder == 3)
        $this->view->defaultView = 2;
    }
	
		//GET LOCATION SETTING
    $this->view->enableLocation = $checkLocation = Engine_Api::_()->list()->enableLocation();
    if (!empty($this->view->map_view)) {

      $this->view->flageSponsored = 0;

      if (!empty($checkLocation)) {
        $listing_ids = array();
        $sponsored = array();
        foreach ($list as $list_listing) {
          $listing_id = $list_listing->getIdentity();
          $listing_ids[] = $listing_id;
          $list_temp[$listing_id] = $list_listing;
        }
        $values['listing_ids'] = $listing_ids;

        $this->view->locations = $locations = Engine_Api::_()->getDbtable('locations', 'list')->getLocation($values);
        foreach ($locations as $location) {
          if ($list_temp[$location->listing_id]->sponsored) {
            $this->view->flageSponsored = 1;
            break;
          }
        }
        $this->view->list = $list_temp;
      }
    }

		//RATING IS ALLOWED OR NOT
    $this->view->ratngShow = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('list.rating', 1);

    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
  }

  //ACTION FOR GET THE LISTINGS BASED ON SEARCHING
  public function getSearchListingsAction() {

		//GET LISTINGS AND MAKE ARRAY
    $userlists = Engine_Api::_()->getDbtable('listings', 'list')->getDayItems($this->_getParam('text'), $this->_getParam('limit', 10));
    $data = array();
    $mode = $this->_getParam('struct');
    $count = count($userlists);
    if ($mode == 'text') {
      $i = 0;
      foreach ($userlists as $userlist) {
        $list_url = $this->view->url(array('user_id' => $userlist->owner_id, 'listing_id' => $userlist->listing_id, 'slug' => $userlist->getSlug()), 'list_entry_view', true);
        $i++;
        $content_photo = $this->view->itemPhoto($userlist, 'thumb.icon');
        $data[] = array(
            'id' => $userlist->listing_id,
            'label' => $userlist->title,
            'photo' => $content_photo,
            'list_url' => $list_url,
            'total_count' => $count,
            'count' => $i
        );
      }
    } else {
      $i = 0;
      foreach ($userlists as $userlist) {
        $list_url = $this->view->url(array('user_id' => $userlist->owner_id, 'listing_id' => $userlist->listing_id, 'slug' => $userlist->getSlug()), 'list_entry_view', true);
        $content_photo = $this->view->itemPhoto($userlist, 'thumb.icon');
        $i++;
        $data[] = array(
            'id' => $userlist->listing_id,
            'label' => $userlist->title,
            'photo' => $content_photo,
            'list_url' => $list_url,
            'total_count' => $count,
            'count' => $i
        );
      }
    }
    if (!empty($data) && $i >= 1) {
      if ($data[--$i]['count'] == $count) {
        $data[$count]['id'] = 'stopevent';
        $data[$count]['label'] = $this->_getParam('text');
        $data[$count]['list_url'] = 'seeMoreLink';
        $data[$count]['total_count'] = $count;
      }
    }
    return $this->_helper->json($data);
  }

	//ACTION FOR MESSAGING THE LISTING OWNER
  public function messageownerAction() {

		//LOGGED IN USER CAN SEND THE MESSAGE
    if (!$this->_helper->requireUser()->isValid())
      return;

		//GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

		//GET LISTING ID AND OBJECT
    $listing_id = $this->_getParam("listing_id");
    $listing = Engine_Api::_()->getItem('list_listing', $listing_id);

		//OWNER CANT SEND A MESSAGE TO HIMSELF
    if ($viewer_id == $listing->owner_id) {
      return $this->_forward('requireauth', 'error', 'core');
    }

		//MAKE FORM
    $this->view->form = $form = new Messages_Form_Compose();
    $form->setDescription('Create your message with the form given below. (This message will be sent to the owner of this Listing.)');
    $form->removeElement('to');
    $form->toValues->setValue("$listing->owner_id");

    //CHECK METHOD/DATA
    if (!$this->getRequest()->isPost()) {
      return;
    }

    $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
    $db->beginTransaction();

    try {
      $values = $this->getRequest()->getPost();

      $is_error = 0;
      if (empty($values['title'])) {
        $is_error = 1;
      }

			//SENDING MESSAGE
      if ($is_error == 1) {
        $error = $this->view->translate('Subject is required field !');
        $error = Zend_Registry::get('Zend_Translate')->_($error);

        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error);
        return;
      }    
      
      $recipients = preg_split('/[,. ]+/', $values['toValues']);

			//LIMIT RECIPIENTS IF IT IS NOT A SPECIAL LIST OF MEMBERS
      $recipients = array_slice($recipients, 0, 1000);

			//CLEAN THE RECIPIENTS FOR REPEATING IDS
      $recipients = array_unique($recipients);

      $user = Engine_Api::_()->getItem('user', $listing->owner_id);

      $listing_title = $listing->title;
      $listing_title_with_link = '<a href = http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('listing_id' => $listing_id, 'user_id' => $listing->owner_id, 'slug' => $listing->getSlug()), 'list_entry_view') . ">$listing_title</a>";

      $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send($viewer, $recipients, $values['title'], $values['body'] . "<br><br>" . $this->view->translate('This message corresponds to the Listing: ') . $listing_title_with_link);

      Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $conversation, 'message_new');

      //INCREMENT MESSAGE COUNTER
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

      $db->commit();

      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => true,
                  //'parentRefresh' => true,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.'))
              ));
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

	//ACTION FOR CHANING THE PHOTO
  public function changePhotoAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET LISTING ID
    $this->view->listing_id = $listing_id = $this->_getParam('listing_id');

    $viewer = Engine_Api::_()->user()->getViewer();
    
    //GET LISTING ITEM
    $this->view->list = $list = Engine_Api::_()->getItem('list_listing', $listing_id);

    //IF THERE IS NO LIST.
    if (empty($list)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    //CAN EDIT OR NOT
    if (!$this->_helper->requireAuth()->setAuthParams($list, $viewer, 'edit')->isValid()) {
      return;
    }    

    //OVERVIEW IS ALLOWED OR NOT
		$this->view->allow_overview_of_owner = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'list_listing', 'overview');
		
    $this->view->allowed_upload_photo = $allowed_upload_photo = 0;
    $this->view->allowed_upload_video = 0;

    //ABLE TO UPLOAD VIDEO OR NOT
    $allowed_upload_videoEnable = Engine_Api::_()->list()->enableVideoPlugin();
    $allowed_upload_video_video = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'video', 'create');
    $allowed_upload_video = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'list_listing', 'video');
    if ((!empty($allowed_upload_video) && !empty($allowed_upload_videoEnable) && !empty($allowed_upload_video_video))) {
      $this->view->allowed_upload_video = 1;
    }

		//ABLE TO ADD PHOTO OR NOT
    $allowed_upload_photo = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'list_listing', 'photo');
    if ($allowed_upload_photo) {
      $this->view->allowed_upload_photo = $allowed_upload_photo;
    }
    
    //GET FORM
    $this->view->form = $form = new List_Form_ChangePhoto();

    //CHECK FORM VALIDATION
    if (!$this->getRequest()->isPost()) {
      return;
    }

    //CHECK FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //UPLOAD PHOTO
    if ($form->Filedata->getValue() !== null) {
      //GET DB
      $db = Engine_Api::_()->getDbTable('listings', 'list')->getAdapter();
      $db->beginTransaction();
      //PROCESS
      try {
        //SET PHOTO
        $list->setPhoto($form->Filedata);
        $db->commit();
      } catch (Engine_Image_Adapter_Exception $e) {
        $db->rollBack();
        $form->addError(Zend_Registry::get('Zend_Translate')->_('The uploaded file is not supported or is corrupt.'));
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    } else if ($form->getValue('coordinates') !== '') {
      $storage = Engine_Api::_()->storage();
      $iProfile = $storage->get($list->photo_id, 'thumb.profile');
      $iSquare = $storage->get($list->photo_id, 'thumb.icon');
      $pName = $iProfile->getStorageService()->temporary($iProfile);
      $iName = dirname($pName) . '/nis_' . basename($pName);
      list($x, $y, $w, $h) = explode(':', $form->getValue('coordinates'));
      $image = Engine_Image::factory();
      $image->open($pName)
              ->resample($x + .1, $y + .1, $w - .1, $h - .1, 48, 48)
              ->write($iName)
              ->destroy();
      $iSquare->store($iName);
      @unlink($iName);
    }
    
    $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $list, 'list_change_photo');

    $file_id = Engine_Api::_()->getDbtable('photos', 'list')->getPhotoId($listing_id, $list->photo_id);

    $photo = Engine_Api::_()->getItem('list_photo', $file_id);
 
    if ($action != null) {
      Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $photo);
    }
                    
    return $this->_helper->redirector->gotoRoute(array('action' => 'change-photo', 'listing_id' => $listing_id), 'list_specific', true);
  }

  //ACTION FOR REMOVE THE PHOTO
  public function removePhotoAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET LISTING ID
    $listing_id = $this->_getParam('listing_id');

    //GET LISTING ITEM
    $list = Engine_Api::_()->getItem('list_listing', $listing_id);
    $viewer = Engine_Api::_()->user()->getViewer();
    
    //CAN EDIT OR NOT
    if (!$this->_helper->requireAuth()->setAuthParams($list, $viewer, 'edit')->isValid()) {
      return;
    }   
    
    //GET PHOTO ID
    $photo_id = $list->photo_id;

    //GET FILE ID
    $file_id = Engine_Api::_()->getDbtable('photos', 'list')->getPhotoId($listing_id, $photo_id);

    //DELETE PHOTO
    if (!empty($file_id)) {
      $photo = Engine_Api::_()->getItem('list_photo', $file_id);
      $photo->delete();
    }

    //SET PHOTO ID TO ZERO
    $list->photo_id = 0;
    $list->save();
    
    return $this->_helper->redirector->gotoRoute(array('action' => 'change-photo', 'listing_id' => $listing_id), 'list_specific', true);
  }

	//ACTION FOR EDITING THE NOTE
  public function displayAction() {

    //GET TEXT AND LISTING ID
    $text = $this->_getParam('strr');
    $listing_id = $this->_getParam('listing_id');

    //SAVE THE NEW TEXT
    Engine_Api::_()->getDbtable('writes', 'list')->setWriteContent($listing_id, $text);
    exit();
  }

  //ACTION FOR UPLOADING THE OVERVIEWS PHOTOS FROM THE EDITOR
  public function uploadPhotoAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //LAYOUT
    $this->_helper->layout->disableLayout();
    if (!$this->_helper->requireUser()->checkRequire()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }

    //LIST ID
    $listing_id = $this->_getParam('listing_id');
    $list = Engine_Api::_()->getItem('list_listing', $listing_id);

    //IF NOT POST OR FORM NOT VALID, RETURN
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }
 $fileName = Engine_Api::_()->seaocore()->tinymceEditorPhotoUploadedFileName();
    //IF NOT POST OR FORM NOT VALID, RETURN
    if (!isset($_FILES[$fileName]) || !is_uploaded_file($_FILES[$fileName]['tmp_name'])) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
      return;
    }

    //PROCESS
    $db = Engine_Api::_()->getDbtable('photos', 'list')->getAdapter();
    $db->beginTransaction();
    try {
      //CREATE PHOTO
      $tablePhoto = Engine_Api::_()->getDbtable('photos', 'list');
      $photo = $tablePhoto->createRow();
      $photo->setFromArray(array(
          'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
          'listing_id' => $listing_id
      ));
      $photo->save();
      $photo->setPhoto($_FILES[$fileName]);

      $this->view->status = true;
      $this->view->name = $_FILES[$fileName]['name'];
      $this->view->photo_id = $photo->photo_id;
      $this->view->photo_url = $photo->getPhotoUrl();

      $tableAlbum = Engine_Api::_()->getDbtable('albums', 'list'); 
      $album = $tableAlbum->getSpecialAlbum($list, 'overview');
      $tablePhotoName = $tablePhoto->info('name');
      $photoSelect = $tablePhoto->select()->from($tablePhotoName)->where('album_id = ?', $album->album_id)->order('photo_id DESC')->limit(1);
      $photo_rowinfo = $tablePhoto->fetchRow($photoSelect);
      $photo->collection_id = $album->album_id;
      $photo->album_id = $album->album_id;
      $photo->type = 'overview';
      $photo->title = 'Overview Photo';
      $photo->save();

      if (!$album->photo_id) {
        $album->photo_id = $photo->file_id;
        $album->save();
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
      return;
    }
  }

}
