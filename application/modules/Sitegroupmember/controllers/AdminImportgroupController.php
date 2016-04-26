<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupmember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminImportlistingController.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroupmember_AdminImportgroupController extends Core_Controller_Action_Admin {

  protected $_bannedUrls = array();

  //ACTION FOR IMPORTING DATA FROM LISTING TO GROUP
  public function indexAction() {
    $table = Engine_Api::_()->getItemtable('sitegroup_package');
    $packages_select = $table->getPackagesSql()
            ->where("enabled = ?", 1)
            ->order('package_id DESC');
    $this->view->packages = $table->fetchAll($packages_select);

    //INCREASE THE MEMORY ALLOCATION SIZE AND INFINITE SET TIME OUT
    ini_set('memory_limit', '2048M');
    set_time_limit(0);

    //START CODE FOR CREATING THE ListingToGroupImport.log FILE
    if (!file_exists(APPLICATION_PATH . '/temporary/log/GroupToGroupImport.log')) {
      $log = new Zend_Log();
      try {
        $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/GroupToGroupImport.log'));
      } catch (Exception $e) {
        //CHECK DIRECTORY
        if (!@is_dir(APPLICATION_PATH . '/temporary/log') && @mkdir(APPLICATION_PATH . '/temporary/log', 0777, true)) {
          $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/GroupToGroupImport.log'));
        } else {
          //Silence ...
          if (APPLICATION_ENV !== 'production') {
            $log->log($e->__toString(), Zend_Log::CRIT);
          } else {
            //MAKE SURE LOGGING DOESN'T CAUSE EXCEPTIONS
            $log->addWriter(new Zend_Log_Writer_Null());
          }
        }
      }
    }

    //GIVE WRITE PERMISSION IF FILE EXIST
    if (file_exists(APPLICATION_PATH . '/temporary/log/GroupToGroupImport.log')) {
      @chmod(APPLICATION_PATH . '/temporary/log/GroupToGroupImport.log', 0777);
    }
    //END CODE FOR CREATING THE ListingToGroupImport.log FILE
    //GET NAVIGATION
    $this->view->navigationGroup = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_admin_main', array(), 'sitegroup_admin_main_import'); 
    $this->view->navigation = '';//Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitegroupmember_admin_main', array(), 'sitegroupmember_admin_main_import');
    $this->view->isImportData = $getListingImportData = $getImportData = true;
    
    $sitelikeEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitelike');

    $sitetagcheckinEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin');

    $sitegroupDocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument');
    $groupdocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('groupdocument');
    if (!empty($sitegroupDocumentEnabled) && !empty($groupdocumentEnabled)) {

      $groupDocumentsTable = Engine_Api::_()->getDbtable('documents', 'groupdocument');
      $groupDocumentsTableName = $groupDocumentsTable->info('name');

      $sitegroupDocumentsTable = Engine_Api::_()->getDbtable('documents', 'sitegroupdocument');
      $sitegroupDocumentsTableName = $sitegroupDocumentsTable->info('name');

      $groupdocumentRatingTable = Engine_Api::_()->getDbTable('ratings', 'groupdocument');
      $groupdocumentRatingTableName = $groupdocumentRatingTable->info('name');

      $sitegroupdocumentRatingTable = Engine_Api::_()->getDbTable('ratings', 'sitegroupdocument');

      $sitegroupdocumentFieldValueTable = Engine_Api::_()->fields()->getTable('sitegroupdocument_document', 'values');
      $sitegroupdocumentFieldValueTableName = $sitegroupdocumentFieldValueTable->info('name');

      $sitegroupdocumentMetaTable = Engine_Api::_()->fields()->getTable('sitegroupdocument_document', 'meta');
      $sitegroupdocumentMetaTableName = $sitegroupdocumentMetaTable->info('name');

      $groupdocumentFieldValueTable = Engine_Api::_()->fields()->getTable('groupdocument_document', 'values');
      $groupdocumentFieldValueTableName = $groupdocumentFieldValueTable->info('name');
    }

    $sitegroupCategoryTable = Engine_Api::_()->getDbtable('categories', 'sitegroup');
    $sitegroupCategoryTableName = $sitegroupCategoryTable->info('name');

    $table = Engine_Api::_()->getDbtable('groups', 'sitegroup');

    $likeTable = Engine_Api::_()->getDbtable('likes', 'core');
    $likeTableName = $likeTable->info('name');

    $stylesTable = Engine_Api::_()->getDbtable('styles', 'core');
    $stylesTableName = $stylesTable->info('name');

    $commentTable = Engine_Api::_()->getDbtable('comments', 'core');
    $commentTableName = $commentTable->info('name');

    $activityTable = Engine_Api::_()->getDbtable('actions', 'activity');
    $activityTableName = $activityTable->info('name');

    $activityAttachmentsTable = Engine_Api::_()->getDbtable('attachments', 'activity');
    $activityAttachmentsTableName = $activityAttachmentsTable->info('name');

    $locationItemsTable = Engine_Api::_()->getDbtable('locationitems', 'seaocore');

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('group')) {
      $topicTable = Engine_Api::_()->getDbtable('topics', 'group');
      $topicTableName = $topicTable->info('name');
      $sitegroupTopicTable = Engine_Api::_()->getDbtable('topics', 'sitegroup');
      $sitegroupPostTable = Engine_Api::_()->getDbtable('posts', 'sitegroup');

      $postTable = Engine_Api::_()->getDbtable('posts', 'group');
      $postTableName = $postTable->info('name');

      $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'group');
      $sitegroupTopicWatchesTable = Engine_Api::_()->getDbtable('topicwatches', 'sitegroup');
    }
    elseif (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advgroup')) {
      $topicTable = Engine_Api::_()->getDbtable('topics', 'advgroup');
      $topicTableName = $topicTable->info('name');
      
      $sitegroupTopicTable = Engine_Api::_()->getDbtable('topics', 'sitegroup');
      $sitegroupPostTable = Engine_Api::_()->getDbtable('posts', 'sitegroup');

      $postTable = Engine_Api::_()->getDbtable('posts', 'advgroup');
      $postTableName = $postTable->info('name');

      $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'advgroup');
      $sitegroupTopicWatchesTable = Engine_Api::_()->getDbtable('topicwatches', 'sitegroup');
    }

    $sitegroupPollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouppoll');
    $grouppollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('grouppoll');
    if (!empty($sitegroupPollEnabled) && !empty($grouppollEnabled)) {

      $groupPollsTable = Engine_Api::_()->getDbtable('polls', 'grouppoll');
      $groupPollsVotesTable = Engine_Api::_()->getDbtable('votes', 'grouppoll');
      $groupPollOptionsTable = Engine_Api::_()->getDbtable('options', 'grouppoll');

      $sitegroupPollsTable = Engine_Api::_()->getDbtable('polls', 'sitegrouppoll');
      $sitegroupvotesTable = Engine_Api::_()->getDbtable('votes', 'sitegrouppoll');
      $sitegroupoptionsTable = Engine_Api::_()->getDbtable('options', 'sitegrouppoll');
    }

    $coreLinksTable = Engine_Api::_()->getDbtable('links', 'core');

    $manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup');
    if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('group')) {
			$groupMembershipTable = Engine_Api::_()->getDbtable('membership', 'group');
			$listPhotoTable = Engine_Api::_()->getDbtable('photos', 'group');
			$groupCategoryTable = Engine_Api::_()->getDbtable('categories', 'group');
			$groupCategoryTableName = $groupCategoryTable->info('name');
		}
		elseif(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advgroup')) {
			$groupMembershipTable = Engine_Api::_()->getDbtable('membership', 'advgroup');
			$listPhotoTable = Engine_Api::_()->getDbtable('photos', 'advgroup');
			$groupCategoryTable = Engine_Api::_()->getDbtable('categories', 'advgroup');
			$groupCategoryTableName = $groupCategoryTable->info('name');
		}
		
    $sitegroupMembershipTable = Engine_Api::_()->getDbtable('membership', 'sitegroup');

    $sitegroupalbumTable = Engine_Api::_()->getDbtable('albums', 'sitegroup');

    $storageTable = Engine_Api::_()->getDbtable('files', 'storage');
    $storageTableName = $storageTable->info('name');

    $sitegroupFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupform');
    if ($sitegroupFormEnabled) {
      $sitegroupformtable = Engine_Api::_()->getDbtable('sitegroupforms', 'sitegroupform');
      $optionid = Engine_Api::_()->getDbtable('groupquetions', 'sitegroupform');
      $table_option = Engine_Api::_()->fields()->getTable('sitegroupform', 'options');
    }

    $groupAdminTable = Engine_Api::_()->getDbtable('pages', 'core');
    $groupAdminTableName = $groupAdminTable->info('name');
    $sitegroupTable = Engine_Api::_()->getDbtable('contentgroups', 'sitegroup');

    //START IMPORTING WORK IF LIST AND SITEGROUP IS INSTALLED AND ACTIVATE
    if ((Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('group') || Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advgroup')) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroup') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {

      //ADD NEW COLUMN IN LISTING TABLE
      $db = Engine_Db_Table::getDefaultAdapter();
      $type_array = $db->query("SHOW COLUMNS FROM engine4_group_groups LIKE 'is_group_import'")->fetch();
      if (empty($type_array)) {
        $run_query = $db->query("ALTER TABLE `engine4_group_groups` ADD `is_group_import` TINYINT( 2 ) NOT NULL DEFAULT '0'");
      }

      //START IF IMPORTING IS BREAKED BY SOME REASON
      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('group')) {
				$groupTable = Engine_Api::_()->getDbTable('groups', 'group');
      } else {
				$groupTable = Engine_Api::_()->getDbTable('groups', 'advgroup');
      }
      $groupTableName = $groupTable->info('name');
      $selectGroups = $groupTable->select()
              ->from($groupTableName, 'group_id')
              ->where('is_group_import != ?', 1)
              ->order('group_id ASC');
      $groupDatas = $groupTable->fetchAll($selectGroups);

      $this->view->first_listing_id = $first_listing_id = 0;
      $this->view->last_listing_id = $last_listing_id = 0;

      if (!empty($groupDatas)) {

        $flag_first_listing_id = 1;

        foreach ($groupDatas as $groupData) {

          if ($flag_first_listing_id == 1) {
            $this->view->first_listing_id = $first_listing_id = $groupData->group_id;
          }
          $flag_first_listing_id++;

          $this->view->last_listing_id = $last_listing_id = $groupData->group_id;
        }

        if (isset($_GET['assigned_previous_id'])) {
          $this->view->assigned_previous_id = $assigned_previous_id = $_GET['assigned_previous_id'];
        } else {
          $this->view->assigned_previous_id = $assigned_previous_id = $first_listing_id;
        }
      }
      //END IF IMPORTING IS BREAKED BY SOME REASON
      //START IMPORTING IF REQUESTED
      if (isset($_GET['start_import']) && $_GET['start_import'] == 1) {

        $activity_group = $this->_getParam('activity_group');
        $select_package_id = $this->_getParam('select_package_id');

        $selectGroupCategory = $sitegroupCategoryTable->select()
                ->from($sitegroupCategoryTableName, 'category_name')
                ->where('cat_dependency = ?', 0);
        $groupCategoryDatas = $sitegroupCategoryTable->fetchAll($selectGroupCategory);
        if (!empty($groupCategoryDatas)) {
          $groupCategoryDatas = $groupCategoryDatas->toArray();
        }

        $groupCategoryInArrayData = array();
        foreach ($groupCategoryDatas as $groupCategoryData) {
          $groupCategoryInArrayData[] = $groupCategoryData['category_name'];
        }

        $selectGroupCategory = $groupCategoryTable->select()
                ->from($groupCategoryTableName);
        //->where('cat_dependency = ?', 0);
        $groupCategoryDatas = $groupCategoryTable->fetchAll($selectGroupCategory);
        if (!empty($groupCategoryDatas)) {
          $groupCategoryDatas = $groupCategoryDatas->toArray();
          $groupCategoryOther = array(array('category_id' => '0', 'title' => 'Other'));
          $groupCategoryDatas = array_merge($groupCategoryDatas, $groupCategoryOther);
          foreach ($groupCategoryDatas as $groupCategoryData) {
            if (!in_array($groupCategoryData['title'], $groupCategoryInArrayData)) {
              $newCategory = $sitegroupCategoryTable->createRow();
              //$newCategory->user_id = $groupCategoryData['user_id'];
              $newCategory->category_name = $groupCategoryData['title'];
              $newCategory->cat_dependency = 0;
              $newCategory->cat_order = 9999;
              $newCategory->save();
            }
          }
        }
        //END FETCH CATEGORY WOR
        $eventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('event');
        $sitegroupEventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent');
        if ($eventEnabled && $sitegroupEventEnabled) {
          $eventCategoryTable = Engine_Api::_()->getDbtable('categories', 'event');
          $eventCategoryTableName = $eventCategoryTable->info('name');
          $eventCategoryDatas = $eventCategoryTable->getCategoriesAssoc();

          $sitegroupEventCategoryTable = Engine_Api::_()->getDbtable('categories', 'sitegroupevent');
          $sitegroupEventCategoryTableName = $sitegroupEventCategoryTable->info('name');
          $sitegroupEventCategoryDatas = $sitegroupEventCategoryTable->getCategoriesAssoc();
          $eventCategoryInGroup = array();
          foreach ($eventCategoryDatas as $id => $category) {
            if (empty($sitegroupEventCategoryDatas) || !in_array($category, $sitegroupEventCategoryDatas)) {
              $newCategory = $sitegroupEventCategoryTable->createRow();
              $newCategory->title = $category;
              $newCategory->save();
              $eventCategoryInGroup[$id] = $newCategory->category_id;
            } else {
              $eventCategoryInGroup[$id] = array_search($category, $sitegroupEventCategoryDatas);
            }
          }
        }
        $sitegroupUrlEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupurl');
        if (!empty($sitegroupUrlEnabled)) {
          $this->_bannedUrls = Engine_Api::_()->sitegroup()->getBannedUrls();
        }

        //START COMMAN DATA
        $package_id = $select_package_id;
        $package = Engine_Api::_()->getItemTable('sitegroup_package')->fetchRow(array('package_id = ?' => $package_id));

        $selectGroups = $groupTable->select()
                ->where('group_id >= ?', $assigned_previous_id)
                ->from($groupTableName, 'group_id')
                ->where('is_group_import != ?', 1)
                ->order('group_id ASC');
        $groupDatas = $groupTable->fetchAll($selectGroups);

        $next_import_count = 0;
        foreach ($groupDatas as $groupData) {

          $group_id = $groupData->group_id;
          $db = Engine_Db_Table::getDefaultAdapter();
          $db->beginTransaction();
          try {
            if (!empty($group_id)) {
              $group = Engine_Api::_()->getItem('group', $group_id);

              $sitegroup = $table->createRow();
              $sitegroup->title = $group->title;
              $sitegroup->body = $group->description;
              //$sitegroup->overview = $group->overview;
              $sitegroup->owner_id = $group->user_id;

              //START FETCH LIST CATEGORY AND SUB-CATEGORY
              if (!empty($group->category_id)) {
                $groupCategory = $groupCategoryTable->fetchRow(array('category_id = ?' => $group->category_id));
                if (!empty($groupCategory)) {
                  $groupCategoryName = $groupCategory->title;

                  if (!empty($groupCategoryName)) {
                    $groupCategory = $sitegroupCategoryTable->fetchRow(array('category_name = ?' => $groupCategoryName, 'cat_dependency = ?' => 0));
                    if (!empty($groupCategory)) {
                      $groupCategoryId = $sitegroup->category_id = $groupCategory->category_id;
                    }

//                   $listSubCategory = $groupCategoryTable->fetchRow(array('category_id = ?' => $group->subcategory_id, 'cat_dependency = ?' => $group->category_id));
//                   if (!empty($listSubCategory)) {
//                     $listSubCategoryName = $listSubCategory->category_name;
// 
//                     $groupSubCategory = $sitegroupCategoryTable->fetchRow(array('category_name = ?' => $listSubCategoryName, 'cat_dependency = ?' => $groupCategoryId));
//                     if (!empty($groupSubCategory)) {
//                       $sitegroup->subcategory_id = $groupSubCategory->category_id;
//                     }
//                   }
                  }
                }
              } else {
                $groupCategory = $sitegroupCategoryTable->fetchRow(array('category_name = ?' => 'Other', 'cat_dependency = ?' => 0));
                if (!empty($groupCategory)) {
                  $groupCategoryId = $sitegroup->category_id = $groupCategory->category_id;
                }
              }
              //END FETCH LIST CATEGORY AND SUB-CATEGORY
              //START FETCH DEFAULT PACKAGE ID
              if (!empty($package))
                $sitegroup->package_id = $package_id;
              //END FETCH DEFAULT PACKAGE ID

              $sitegroup->profile_type = 0;

              $sitegroup->photo_id = 0;
              //START GET DATA FROM LISTING
              $sitegroup->approved = 1;
              $sitegroup->modified_date = $group->modified_date;
              $sitegroup->view_count = 1;
              if ($group->view_count > 0) {
                $sitegroup->view_count = $group->view_count;
              }
              $sitegroup->search = $group->search;
              $sitegroup->member_count = $group->member_count;

              //START LIKE COUNT 
              $resource = Engine_Api::_()->getItem('group', $group_id);
              $like_count = Engine_Api::_()->getDbtable('likes', 'core')->getLikeCount($resource);
              $sitegroup->like_count = $like_count;
              //END LIKE COUNT WORK

              $sitegroup->offer = 0;
              $sitegroup->member_invite = $group->invite;
              $sitegroup->member_approval = $group->approval;
              $sitegroup->pending = 0;
              $sitegroup->aprrove_date = $group->creation_date;
              $sitegroup->expiration_date = '2250-01-01 00:00:00';
              $sitegroup->save();
              $sitegroup->creation_date = $group->creation_date;
              $sitegroup->save();

              $group->is_group_import = 1;
              $group->save();
              $next_import_count++;
              //END GET DATA FROM LISTING
              //START CREATE NEW GROUP URL


              $sitegroup->group_url = $this->getParseUrl($group->title);

              //END CREATE NEW GROUP URL

              $sitegroup->save();

              if (!empty($sitegroup) && !empty($sitegroup->draft) && empty($sitegroup->pending) && $activity_group) {
                $action = $activityTable->addActivity($sitegroup->getOwner(), $sitegroup, 'sitegroup_new');

                if ($action != null) {
                  $activityTable->attachActivity($action, $sitegroup);
                }
                $action->date = $sitegroup->creation_date;
                $action->save();
              }

              //START LOCATION SAVE IF SITETAGCHECKIN PLUGIN IS ENABLED.
              if (!empty($group->location)) {
                $sitegroup->location = $group->location;
                $sitegroup->save();
                $sitegroup->setLocation();
              }
              //END LOCATION SAVE IF SITETAGCHECKIN PLUGIN IS ENABLED.
              //START PROFILE MAPS WORK
              Engine_Api::_()->getDbtable('profilemaps', 'sitegroup')->profileMapping($sitegroup);

              //EXTRACTING CURRENT ADMIN SETTINGS FOR THIS VIEW GROUP.
              $selectGroupAdmin = $groupAdminTable->select()
                      ->setIntegrityCheck(false)
                      ->from($groupAdminTableName)
                      ->where('name = ?', 'sitegroup_index_view');
              $groupAdminresult = $groupAdminTable->fetchRow($selectGroupAdmin);
              //NOW INSERTING THE ROW IN GROUP TABLE
              //MAKE NEW ENTRY FOR USER LAYOUT
              $groupObject = $sitegroupTable->createRow();
              $groupObject->displayname = ( null !== ($name = $sitegroup->title) ? $name : 'Untitled' );
              $groupObject->title = ( null !== ($name = $sitegroup->title) ? $name : 'Untitled' );
              $groupObject->description = $sitegroup->body;
              $groupObject->name = "sitegroup_index_view";
              $groupObject->url = $groupAdminresult->url;
              $groupObject->custom = $groupAdminresult->custom;
              $groupObject->fragment = $groupAdminresult->fragment;
              $groupObject->keywords = $groupAdminresult->keywords;
              $groupObject->layout = $groupAdminresult->layout;
              $groupObject->view_count = $groupAdminresult->view_count;
              $groupObject->user_id = $sitegroup->owner_id;
              $groupObject->group_id = $sitegroup->group_id;
              $contentGroupId = $groupObject->save();

              //NOW FETCHING GROUP CONTENT DEFAULT SETTING INFORMATION FROM CORE CONTENT TABLE FOR THIS GROUP.
              //NOW INSERTING DEFAULT GROUP CONTENT SETTINGS IN OUR CONTENT TABLE
              $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0);
              if (!$layout) {
                Engine_Api::_()->getDbtable('content', 'sitegroup')->setContentDefault($contentGroupId);
              } else {
                Engine_Api::_()->getApi('layoutcore', 'sitegroup')->setContentDefaultLayout($contentGroupId);
              }

              //START FETCH LIKES
              $this->likeItems('sitegroup_group', $sitegroup->group_id, 'group', $group_id, $sitelikeEnabled, $activity_group);
              //END FETCH LIKES
              //START INSERT SOME DEFAULT DATA IN MANAGE ADMIN TABLE
              $row = $manageadminsTable->createRow();
              $row->user_id = $sitegroup->owner_id;
              $row->group_id = $sitegroup->group_id;
              $row->save();
              //END INSERT SOME DEFAULT DATA IN MANAGE ADMIN TABLE
              //START OFFICER TO MAKE GROUP ADMIN
              if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('group')) {
								$groupListItemsable = Engine_Api::_()->getDbtable('ListItems', 'group');
              } elseif (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advgroup')) {
								$groupListItemsable = Engine_Api::_()->getDbtable('ListItems', 'advgroup');
              }
              $selectGroupListItems = $groupListItemsable->select()
                      ->from($groupListItemsable->info('name'))
                      ->where('list_id = ?', $group_id);
              $groupListItemsDatas = $groupListItemsable->fetchAll($selectGroupListItems);
              if (!empty($groupListItemsDatas)) {
                foreach ($groupListItemsDatas as $groupListItemsData) {
                     $selectGroupAdmin = $manageadminsTable->select()
                      ->from($manageadminsTable->info('name'))
                      ->where('user_id = ?',$groupListItemsData->child_id)
                      ->where('group_id = ?',$sitegroup->group_id);
              $groupAdminresult = $groupAdminTable->fetchRow($selectGroupAdmin);
                  if(empty($groupAdminresult)) {
                    $row = $manageadminsTable->createRow();
                    $row->user_id = $groupListItemsData->child_id;
                    $row->group_id = $sitegroup->group_id;
                    $row->save();
                  }
                }
              }
              //END OFFICER TO MAKE GROUP ADMIN
              //START FETCH MEMBERSHIP TABLE DATA
              $selectGroupMembership = $groupMembershipTable->select()
                      ->from($groupMembershipTable->info('name'))
                      ->where('resource_id = ?', $group_id);
              $groupMembershipDatas = $groupMembershipTable->fetchAll($selectGroupMembership);

              if (!empty($groupMembershipDatas)) {

                foreach ($groupMembershipDatas as $groupMembershipData) {
                  $newMembershipEntry = $sitegroupMembershipTable->createRow();
                  $newMembershipEntry->resource_id = $sitegroup->group_id;
                  $newMembershipEntry->user_id = $groupMembershipData->user_id;
                  $newMembershipEntry->active = $groupMembershipData->active;
                  $newMembershipEntry->resource_approved = $groupMembershipData->resource_approved;
                  $newMembershipEntry->user_approved = $groupMembershipData->user_approved;
                  $newMembershipEntry->title = $groupMembershipData->title;
                  $newMembershipEntry->group_id = $sitegroup->group_id;
                  $newMembershipEntry->save();

                  if ($activity_group && $groupMembershipData->active && $groupMembershipData->resource_approved && $groupMembershipData->user_approved && ($groupMembershipData->user_id != $group->user_id)) {
                    $activityTable->addActivity(Engine_Api::_()->getItem('user', $groupMembershipData->user_id), $sitegroup, 'sitegroup_join');
                  }
                }
              }
              //END FETCH MEMBERSHIP TABLE DATA
              //START FETCH PRIVACY
              $auth = Engine_Api::_()->authorization()->context;

              $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

              $roles_view = array('member', 'registered', 'everyone');
              foreach ($roles_view as $role) {
                if ($auth->isAllowed($group, $role, 'view')) {
                  $values['auth_view'] = $role;
                }
              }

              $roles1 = array('group_list', 'member', 'registered');
              foreach ($roles1 as $role) {
                if ($auth->isAllowed($group, $role, 'comment')) {
                  $values['auth_comment'] = $role;
                }
              }
              if (isset($values['auth_comment']) && $values['auth_comment'] == 'group_list') {
                $values['auth_comment'] = 'owner';
              }

              foreach ($roles1 as $role) {
                if ($auth->isAllowed($group, $role, 'photo')) {
                  $values['auth_spcreate'] = $role;
                }
              }
              if (isset($values['auth_spcreate']) && $values['auth_spcreate'] == 'group_list') {
                $values['auth_spcreate'] = 'owner';
              }

              foreach ($roles1 as $role) {
                if ($auth->isAllowed($group, $role, 'event')) {
                  $values['auth_secreate'] = $role;
                }
              }
              if (isset($values['auth_secreate']) && $values['auth_secreate'] == 'group_list') {
                $values['auth_secreate'] = 'owner';
              }
              
              if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advgroup')) {
								foreach ($roles1 as $role) {
									if ($auth->isAllowed($group, $role, 'sub_group 	a')) {
										$values['auth_sspcreate'] = $role;
									}
								}
								if (isset($values['auth_sspcreate']) && $values['auth_sspcreate'] == 'group_list') {
									$values['auth_sspcreate'] = 'owner';
								}
								$subgroupMax = array_search($values['auth_sspcreate'], $roles);
              }
              
              $viewMax = array_search($values['auth_view'], $roles);
              $commentMax = array_search($values['auth_comment'], $roles);
              $photoMax = array_search($values['auth_spcreate'], $roles);
              $eventMax = array_search($values['auth_secreate'], $roles);

              foreach ($roles as $i => $role) {
                $auth->setAllowed($sitegroup, $role, 'view', ($i <= $viewMax));
                $auth->setAllowed($sitegroup, $role, 'comment', ($i <= $commentMax));
                $auth->setAllowed($sitegroup, $role, 'spcreate', ($i <= $photoMax));
                $auth->setAllowed($sitegroup, $role, 'secreate', ($i <= $eventMax));
                
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advgroup')) {
									$auth->setAllowed($sitegroup, $role, 'sspcreate', ($i <= $viewMax));
                }
// 								if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument')) {
// 									$auth->setAllowed($sitegroup, $role, 'sdcreate', ($i <= $documemtMax));
// 								}
// 
// 								if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouppoll')) {
// 									$auth->setAllowed($sitegroup, $role, 'splcreate', ($i <= $pollMax));
// 								}
              }
              //END FETCH PRIVACY

              if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouppoll') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('grouppoll')) {
                foreach ($roles1 as $role) {
                  if ($auth->isAllowed($group, $role, 'gpcreate')) {
                    $values['splcreate'] = $role;
                  }
                }
                if (isset($values['splcreate']) && $values['splcreate'] == 'group_list') {
                  $values['splcreate'] = 'owner';
                }
                $pollMax = array_search($values['auth_secreate'], $roles);
                foreach ($roles as $i => $role) {
                  $auth->setAllowed($sitegroup, $role, 'splcreate', ($i <= $pollMax));
                }
              }

              if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('groupdocument')) {
                foreach ($roles1 as $role) {
                  if ($auth->isAllowed($group, $role, 'gdcreate')) {
                    $values['auth_sdcreate'] = $role;
                  }
                }
                if (isset($values['auth_sdcreate']) && $values['auth_sdcreate'] == 'group_list') {
                  $values['auth_sdcreate'] = 'owner';
                }
                $documemtMax = array_search($values['auth_secreate'], $roles);
                foreach ($roles as $i => $role) {
                  $auth->setAllowed($sitegroup, $role, 'sdcreate', ($i <= $documemtMax));
                }
              }

              //START FETCH DISCUSSION DATA
              if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion') && !empty($getImportData)) {
                $topicSelect = $topicTable->select()
                        ->from($topicTableName)
                        ->where('group_id = ?', $group_id);
                $topicSelectDatas = $topicTable->fetchAll($topicSelect);
                if (!empty($topicSelectDatas)) {
                  $topicSelectDatas = $topicSelectDatas->toArray();

                  //START LINK WORK
                  $linksSelect = $coreLinksTable->select()
                          ->from($coreLinksTable->info('name'))
                          ->where('parent_type = ?', 'group')
                          ->where('parent_id = ?', $group_id);
                  $linksSelectDatas = $coreLinksTable->fetchAll($linksSelect);
                  if (!empty($linksSelectDatas)) {
                    foreach ($linksSelectDatas as $linksSelectData) {
                      $newlinksEntry = $coreLinksTable->createRow();
                      $newlinksEntry->uri = $linksSelectData->uri;
                      $newlinksEntry->title = $linksSelectData->title;
                      $newlinksEntry->description = $linksSelectData->description;
                      $newlinksEntry->photo_id = $linksSelectData->photo_id;
                      $newlinksEntry->parent_type = 'sitegroup_group';
                      $newlinksEntry->parent_id = $sitegroup->group_id;
                      $newlinksEntry->owner_type = $linksSelectData->owner_type;
                      $newlinksEntry->owner_id = $linksSelectData->owner_id;
                      $newlinksEntry->view_count = $linksSelectData->view_count;
                      $newlinksEntry->creation_date = $linksSelectData->creation_date;
                      $newlinksEntry->search = $linksSelectData->search;
                      $newlinksEntry->save();
                      $newlinksEntry->creation_date = $linksSelectData->creation_date;
                      $newlinksEntry->save();
                    }
                  }
                  //END LINK WORK
                  //START STYLE WORK
                  $stylesSelect = $stylesTable->select()
                          ->from($stylesTable->info('name'))
                          ->where('type = ?', 'group')
                          ->where('id = ?', $group_id);
                  $stylesDatas = $stylesTable->fetchAll($stylesSelect);
                  foreach ($stylesDatas as $stylesSelectData) {
                    $newStylesEntry = $stylesTable->createRow();
                    $newStylesEntry->type = 'sitegroup_group';
                    $newStylesEntry->id = $sitegroup->group_id;
                    $newStylesEntry->style = $stylesSelectData->style;
                    $newStylesEntry->save();
                  }
                  //END STYLE WORK
                  //START FETCH DISCUSSION DATA
                  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion')) {
                    $topicSelect = $topicTable->select()
                            ->from($topicTableName)
                            ->where('group_id = ?', $group_id);
                    $topicSelectDatas = $topicTable->fetchAll($topicSelect);
                    if (!empty($topicSelectDatas)) {
                      $topicSelectDatas = $topicSelectDatas->toArray();

                      foreach ($topicSelectDatas as $topicSelectData) {
                        $groupTopic = $sitegroupTopicTable->createRow();
                        $groupTopic->group_id = $sitegroup->group_id;
                        $groupTopic->user_id = $topicSelectData['user_id'];
                        $groupTopic->title = $topicSelectData['title'];
                        $groupTopic->modified_date = $topicSelectData['modified_date'];
                        $groupTopic->sticky = $topicSelectData['sticky'];
                        $groupTopic->closed = $topicSelectData['closed'];
                        $groupTopic->post_count = $topicSelectData['post_count'];
                        $groupTopic->view_count = $topicSelectData['view_count'];
                        $groupTopic->lastpost_id = $topicSelectData['lastpost_id'];
                        $groupTopic->lastposter_id = $topicSelectData['lastposter_id'];
                        $groupTopic->save();
                        $groupTopic->creation_date = $topicSelectData['creation_date'];
                        $groupTopic->save();

                        //ADD ACTIVITY
                        if ($activity_group) {
                          $activityFeedType = null;
                          if (Engine_Api::_()->sitegroup()->isGroupOwner($sitegroup, $groupTopic->getOwner()) && Engine_Api::_()->sitegroup()->isFeedTypeGroupEnable())
                            $activityFeedType = 'sitegroup_admin_topic_create';
                          elseif ($sitegroup->all_post || Engine_Api::_()->sitegroup()->isGroupOwner($sitegroup, $groupTopic->getOwner()))
                            $activityFeedType = 'sitegroup_topic_create';

                          if ($activityFeedType) {
                            $action = $activityTable->addActivity($groupTopic->getOwner(), $sitegroup, $activityFeedType);
                            Engine_Api::_()->getApi('subCore', 'sitegroup')->deleteFeedStream($action);
                          }
                          if ($action) {
                            $activityTable->attachActivity($action, $groupTopic);
                            $action->date = $groupTopic->creation_date;
                            $action->save();
                          }
                        }

                        //START FETCH TOPIC POST'S
                        $postSelect = $postTable->select()
                                ->from($postTableName)
                                ->where('topic_id = ?', $topicSelectData['topic_id'])
                                ->where('group_id = ?', $group_id);
                        $postSelectDatas = $postTable->fetchAll($postSelect);
                        if (!empty($postSelectDatas)) {
                          $postSelectDatas = $postSelectDatas->toArray();

                          foreach ($postSelectDatas as $postSelectData) {
                            $groupPost = $sitegroupPostTable->createRow();
                            $groupPost->topic_id = $groupTopic->topic_id;
                            $groupPost->group_id = $sitegroup->group_id;
                            $groupPost->user_id = $postSelectData['user_id'];
                            $groupPost->body = $postSelectData['body'];
                            $groupPost->creation_date = $postSelectData['creation_date'];
                            $groupPost->modified_date = $postSelectData['modified_date'];
                            $groupPost->save();
                            $groupPost->creation_date = $postSelectData['creation_date'];
                            $groupPost->save();

                            //ADD ACTIVITY
                            $activityFeedType = null;
                            if (Engine_Api::_()->sitegroup()->isGroupOwner($sitegroup, $groupPost->getOwner()) && Engine_Api::_()->sitegroup()->isFeedTypeGroupEnable())
                              $activityFeedType = 'sitegroup_admin_topic_reply';
                            elseif ($sitegroup->all_post || Engine_Api::_()->sitegroup()->isGroupOwner($sitegroup, $groupPost->getOwner()))
                              $activityFeedType = 'sitegroup_topic_reply';

                            //ACTIVITY      
                            if ($activityFeedType) {
                              $action = $activityTable->addActivity($groupPost->getOwner(), $sitegroup, $activityFeedType);
                              Engine_Api::_()->getApi('subCore', 'sitegroup')->deleteFeedStream($action);
                              if (!empty($action)) {
                                $action->attach($groupPost, Activity_Model_Action::ATTACH_DESCRIPTION);
                                $action->date = $groupPost->creation_date;
                                $action->save();
                              }
                            }
                          }
                        }
                        //END FETCH TOPIC POST'S
                        //START FETCH TOPIC WATCH
                        $topicWatchData = $topicWatchesTable->fetchRow(array('resource_id = ?' => $group_id, 'topic_id = ?' => $topicSelectData['topic_id'], 'user_id = ?' => $topicSelectData['user_id']));
                        $watch = 1;
                        if (!empty($topicWatchData))
                          $watch = $topicWatchData->watch;
                  
                        $sitegroupTopicWatchesTable->insert(array(
                            'resource_id' => $groupTopic->group_id,
                            'topic_id' => $groupTopic->topic_id,
                            'user_id' => $topicSelectData['user_id'],
                            'watch' => $watch,
                            'group_id' => $sitegroup->group_id,
                        ));
                        //END FETCH TOPIC WATCH
                      }
                    }
                  }
                  //END FETCH DISCUSSION DATA

                  $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                  $printMax = array_search('everyone', $roles);
                  foreach ($roles as $i => $role) {
                    $auth->setAllowed($sitegroup, $role, 'print', ($i <= $printMax));
                  }

                  $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                  $tfriendMax = array_search('everyone', $roles);
                  foreach ($roles as $i => $role) {
                    $auth->setAllowed($sitegroup, $role, 'tfriend', ($i <= $tfriendMax));
                  }

                  $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                  $overviewMax = array_search('everyone', $roles);
                  foreach ($roles as $i => $role) {
                    $auth->setAllowed($sitegroup, $role, 'overview', ($i <= $overviewMax));
                  }

                  $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                  $mapMax = array_search('everyone', $roles);
                  foreach ($roles as $i => $role) {
                    $auth->setAllowed($sitegroup, $role, 'map', ($i <= $mapMax));
                  }

                  $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                  $insightMax = array_search('everyone', $roles);
                  foreach ($roles as $i => $role) {
                    $auth->setAllowed($sitegroup, $role, 'insight', ($i <= $insightMax));
                  }

                  $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                  $layoutMax = array_search('everyone', $roles);
                  foreach ($roles as $i => $role) {
                    $auth->setAllowed($sitegroup, $role, 'layout', ($i <= $layoutMax));
                  }

                  $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                  $contactMax = array_search('everyone', $roles);
                  foreach ($roles as $i => $role) {
                    $auth->setAllowed($sitegroup, $role, 'contact', ($i <= $contactMax));
                  }

                  $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                  $formMax = array_search('everyone', $roles);
                  foreach ($roles as $i => $role) {
                    $auth->setAllowed($sitegroup, $role, 'form', ($i <= $formMax));
                  }

                  $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                  $offerMax = array_search('everyone', $roles);
                  foreach ($roles as $i => $role) {
                    $auth->setAllowed($sitegroup, $role, 'offer', ($i <= $offerMax));
                  }

                  $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                  $inviteMax = array_search('everyone', $roles);
                  foreach ($roles as $i => $role) {
                    $auth->setAllowed($sitegroup, $role, 'invite', ($i <= $inviteMax));
                  }
                  //END INSERT SOME DEFAULT DATA
                  //START FETCH PHOTO DATA
                  $selectListPhoto = $listPhotoTable->select()
                          ->from($listPhotoTable->info('name'))
                          ->where('group_id = ?', $group_id);
                  $listPhotoDatas = $listPhotoTable->fetchAll($selectListPhoto);

                  $sitgroup = Engine_Api::_()->getItem('sitegroup_group', $sitegroup->group_id);

                  if (!empty($listPhotoDatas)) {

                    $listPhotoDatas = $listPhotoDatas->toArray();

                    if (empty($group->photo_id)) {
                      foreach ($listPhotoDatas as $listPhotoData) {
                        $group->photo_id = $listPhotoData['photo_id'];
                        break;
                      }
                    }

                    if (!empty($group->photo_id)) {
                      $listPhotoData = $listPhotoTable->fetchRow(array('file_id = ?' => $group->photo_id));
                      if (!empty($listPhotoData)) {
                        $storageData = $storageTable->fetchRow(array('file_id = ?' => $listPhotoData->file_id));

                        if (!empty($storageData)) {

                          $sitgroup->setPhoto($storageData->storage_path);

                          $album_id = $sitegroupalbumTable->update(array('photo_id' => $sitgroup->photo_id, 'owner_id' => $sitgroup->owner_id), array('group_id = ?' => $sitgroup->group_id));

                          $groupProfilePhoto = Engine_Api::_()->getDbTable('photos', 'sitegroup')->fetchRow(array('file_id = ?' => $sitgroup->photo_id));
                          if (!empty($groupProfilePhoto)) {
                            $groupProfilePhotoId = $groupProfilePhoto->photo_id;
                          } else {
                            $groupProfilePhotoId = $sitgroup->photo_id;
                          }

                          //START FETCH LIKES
                          $this->likeItems('sitegroup_photo', $groupProfilePhotoId, 'group_photo', $group->photo_id, $sitelikeEnabled, $activity_group);
                          //END FETCH LIKES
                          //START FETCH COMMENTS
                          $this->commentItems('sitegroup_photo', $groupProfilePhotoId, 'group_photo', $group->photo_id);
                          //END FETCH COMMENTS
                          //START FETCH TAGGER DETAIL
                          $tagmapsTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
                          $tagmapsTableName = $tagmapsTable->info('name');
                          $selectTagmaps = $tagmapsTable->select()
                                  ->from($tagmapsTableName, 'tagmap_id')
                                  ->where('resource_type = ?', 'group_photo')
                                  ->where('resource_id = ?', $group->photo_id);
                          $selectTagmapsDatas = $tagmapsTable->fetchAll($selectTagmaps);
                          foreach ($selectTagmapsDatas as $selectTagmapsData) {
                            $tagMap = Engine_Api::_()->getItem('core_tag_map', $selectTagmapsData->tagmap_id);

                            $newTagmapEntry = $tagmapsTable->createRow();
                            $newTagmapEntry->resource_type = 'sitegroup_photo';
                            $newTagmapEntry->resource_id = $groupProfilePhotoId;
                            $newTagmapEntry->tagger_type = 'user';
                            $newTagmapEntry->tagger_id = $tagMap->tagger_id;
                            $newTagmapEntry->tag_type = 'user';
                            $newTagmapEntry->tag_id = $tagMap->tag_id;
                            $newTagmapEntry->creation_date = $tagMap->creation_date;
                            $newTagmapEntry->extra = $tagMap->extra;
                            $newTagmapEntry->save();
                            $newTagmapEntry->creation_date = $tagMap->creation_date;
                            $newTagmapEntry->save();
                          }
                          //END FETCH TAGGER DETAIL
                        }
                      }

                      $fetchDefaultAlbum = $sitegroupalbumTable->fetchRow(array('group_id = ?' => $sitegroup->group_id, 'default_value = ?' => 1));
                      if (!empty($fetchDefaultAlbum)) {

                        $order = 999;
                        foreach ($listPhotoDatas as $listPhotoData) {

                          if ($listPhotoData['photo_id'] != $group->photo_id) {
                            $params = array(
                                'collection_id' => $fetchDefaultAlbum->album_id,
                                'album_id' => $fetchDefaultAlbum->album_id,
                                'group_id' => $sitgroup->group_id,
                                'user_id' => $listPhotoData['user_id'],
                                'order' => $order,
                            );

                            $storageData = $storageTable->fetchRow(array('file_id = ?' => $listPhotoData['file_id']));
                            if (!empty($storageData)) {
                              $file = array();
                              $file['tmp_name'] = $storageData->storage_path;
                              $path_array = explode('/', $file['tmp_name']);
                              $file['name'] = end($path_array);

                              $groupPhoto = Engine_Api::_()->getDbtable('photos', 'sitegroup')->createPhoto($params, $file);
                              if (!empty($groupPhoto)) {

                                $order++;

                                //START FETCH LIKES
                                $this->likeItems('sitegroup_photo', $groupPhoto->photo_id, 'group_photo', $listPhotoData['photo_id'], $sitelikeEnabled, $activity_group);
                                //END FETCH LIKES
                                //START FETCH COMMENTS
                                $this->commentItems('sitegroup_photo', $groupPhoto->photo_id, 'group_photo', $listPhotoData['photo_id']);

                                //END FETCH COMMENTS
                                //START FETCH TAGGER DETAIL
                                $selectTagmaps = $tagmapsTable->select()
                                        ->from($tagmapsTableName, 'tagmap_id')
                                        ->where('resource_type = ?', 'group_photo')
                                        ->where('resource_id = ?', $listPhotoData['photo_id']);
                                $selectTagmapsDatas = $tagmapsTable->fetchAll($selectTagmaps);
                                foreach ($selectTagmapsDatas as $selectTagmapsData) {
                                  $tagMap = Engine_Api::_()->getItem('core_tag_map', $selectTagmapsData->tagmap_id);

                                  $newTagmapEntry = $tagmapsTable->createRow();
                                  $newTagmapEntry->resource_type = 'sitegroup_photo';
                                  $newTagmapEntry->resource_id = $groupPhoto->photo_id;
                                  $newTagmapEntry->tagger_type = 'user';
                                  $newTagmapEntry->tagger_id = $tagMap->tagger_id;
                                  $newTagmapEntry->tag_type = 'user';
                                  $newTagmapEntry->tag_id = $tagMap->tag_id;
                                  $newTagmapEntry->creation_date = $tagMap->creation_date;
                                  $newTagmapEntry->extra = $tagMap->extra;
                                  $newTagmapEntry->save();
                                  $newTagmapEntry->creation_date = $tagMap->creation_date;
                                  $newTagmapEntry->save();
                                }
                                //END FETCH TAGGER DETAIL
                              }
                            }
                          }
                        }
                      }
                    }

                    if ($activity_group) {

                      $groupPhotoTable = Engine_Api::_()->getDbtable('photos', 'sitegroup');
                      $groupPhotoTableName = $groupPhotoTable->info('name');
                      $select = $groupPhotoTable->select()->from($groupPhotoTableName)->where('user_id = ?', $sitegroup->owner_id)->where('group_id = ?', $sitegroup->group_id);
                      $groupPhotos = $groupPhotoTable->fetchAll($select);
                      $count = count($groupPhotos);
                      if ($count > 1) {

                        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
                        $linked_album_title = $view->htmlLink($fetchDefaultAlbum->getHref(), $fetchDefaultAlbum->getTitle(), array('target' => '_parent'));

                        $activityFeedType = null;
                        if (Engine_Api::_()->sitegroup()->isGroupOwner($sitegroup, $sitegroup->getOwner()) && Engine_Api::_()->sitegroup()->isFeedTypeGroupEnable()) {
                          $activityFeedType = 'sitegroupalbum_admin_photo_new';
                        } elseif ($sitegroup->all_post || Engine_Api::_()->sitegroup()->isGroupOwner($sitegroup, $sitegroup->getOwner())) {
                          $activityFeedType = 'sitegroupalbum_photo_new';
                        }

                        $action = $activityTable->addActivity($sitegroup->getOwner(), $sitegroup, $activityFeedType, null, array('count' => $count, 'linked_album_title' => $linked_album_title));
                        Engine_Api::_()->getApi('subCore', 'sitegroup')->deleteFeedStream($action);
                        $count = 0;

                        foreach ($groupPhotos as $groupPhoto) {

                          if ($action instanceof Activity_Model_Action && $count < 8) {
                            $activityTable->attachActivity($action, $groupPhoto, Activity_Model_Action::ATTACH_MULTI);
                          }
                          $count++;
                        }
                        $action->date = $groupPhoto->creation_date;
                        $action->save();
                      }
                    }
                  }
                }
                //END FETCH PHOTO DATA
                //START FETCH EVENT DATA

                $sitegroupEventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent');
                if (!empty($sitegroupEventEnabled) && !empty($eventEnabled) && !empty($getImportData)) {

                  $groupEventTable = Engine_Api::_()->getDbtable('events', 'event');
                  $groupEventmembershipTable = Engine_Api::_()->getDbtable('membership', 'event');

                  $groupEventAlbumsTable = Engine_Api::_()->getDbtable('albums', 'event');
                  $groupEventPhotosTable = Engine_Api::_()->getDbtable('photos', 'event');

                  $sitegroupEventTable = Engine_Api::_()->getDbtable('events', 'sitegroupevent');
                  $sitegroupEventmembershipTable = Engine_Api::_()->getDbtable('membership', 'sitegroupevent');

                  $sitegroupEventAlbumsTable = Engine_Api::_()->getDbtable('albums', 'sitegroupevent');
                  $sitegroupEventPhotosTable = Engine_Api::_()->getDbtable('photos', 'sitegroupevent');

                  $selectGroupEvent = $groupEventTable->select()
                          ->from($groupEventTable->info('name'))
                          ->where('parent_id = ?', $group_id)
                          ->where('parent_type = ?', 'group');
                  $groupEventsDatas = $groupEventTable->fetchAll($selectGroupEvent);

                  if (!empty($groupEventsDatas)) {
                    foreach ($groupEventsDatas as $groupEventsData) {
                      $newEventEntry = $sitegroupEventTable->createRow();
                      $newEventEntry->title = $groupEventsData->title;
                      $newEventEntry->description = $groupEventsData->description;
                      $newEventEntry->user_id = $groupEventsData->user_id;
                      $newEventEntry->group_id = $sitegroup->group_id;
                      $newEventEntry->parent_type = 'sitegroup_group';
                      $newEventEntry->search = $groupEventsData->search;
                      $newEventEntry->modified_date = $groupEventsData->modified_date;
                      $newEventEntry->starttime = $groupEventsData->starttime;
                      $newEventEntry->endtime = $groupEventsData->endtime;
                      $newEventEntry->location = $groupEventsData->location;
                      $newEventEntry->view_count = $groupEventsData->view_count;
                      $newEventEntry->member_count = $groupEventsData->member_count;
                      $newEventEntry->approval = $groupEventsData->approval;
                      $newEventEntry->invite = $groupEventsData->invite;
                      $newEventEntry->photo_id = $groupEventsData->photo_id;
                      $newEventEntry->category_id = ($eventCategoryInGroup && isset($eventCategoryInGroup[$groupEventsData->category_id])) ? $eventCategoryInGroup[$groupEventsData->category_id] : 0;
                      $newEventEntry->save();
                      $newEventEntry->creation_date = $groupEventsData->creation_date;
                      $newEventEntry->save();

                      if ($activity_group && $newEventEntry->search) {
                        $activityFeedType = null;
                        if (Engine_Api::_()->sitegroup()->isGroupOwner($sitegroup, $newEventEntry->getOwner()) && Engine_Api::_()->sitegroup()->isFeedTypeGroupEnable())
                          $activityFeedType = 'sitegroupevent_admin_new';
                        elseif ($sitegroup->all_post || Engine_Api::_()->sitegroup()->isGroupOwner($sitegroup, $newEventEntry->getOwner()))
                          $activityFeedType = 'sitegroupevent_new';

                        if ($activityFeedType) {
                          $action = $activityTable->addActivity($newEventEntry->getOwner(), $sitegroup, $activityFeedType);
                          Engine_Api::_()->getApi('subCore', 'sitegroup')->deleteFeedStream($action);
                        }
                        if ($action != null) {
                          $activityTable->attachActivity($action, $newEventEntry);
                          $action->date = $newEventEntry->creation_date;
                          $action->save();
                        }
                      }
                      $event = Engine_Api::_()->getItem('event', $groupEventsData->event_id);
                      $sitgroupevent = Engine_Api::_()->getItem('sitegroupevent_event', $newEventEntry->event_id);
                      //START FETCH DISCUSSION DATA
                      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion')) {
                        $topicEventTable = Engine_Api::_()->getDbtable('topics', 'event');
                        $topicEventTableName = $topicEventTable->info('name');
                        $topicEventSelect = $topicEventTable->select()
                                ->from($topicEventTableName)
                                ->where('event_id = ?', $event->event_id);
                        $topicSelectDatas = $topicEventTable->fetchAll($topicEventSelect);
                        if (!empty($topicSelectDatas)) {
                          $topicSelectDatas = $topicSelectDatas->toArray();

                          foreach ($topicSelectDatas as $topicSelectData) {
                            $groupTopic = $sitegroupTopicTable->createRow();
                            $groupTopic->group_id = $sitegroup->group_id;

                            $groupTopic->user_id = $topicSelectData['user_id'];
                            $groupTopic->title = $topicSelectData['title'];
                            $groupTopic->modified_date = $topicSelectData['modified_date'];
                            $groupTopic->sticky = $topicSelectData['sticky'];
                            $groupTopic->closed = $topicSelectData['closed'];
                            $groupTopic->post_count = $topicSelectData['post_count'];
                            $groupTopic->view_count = $topicSelectData['view_count'];
                            $groupTopic->lastpost_id = $topicSelectData['lastpost_id'];
                            $groupTopic->lastposter_id = $topicSelectData['lastposter_id'];
                            $groupTopic->resource_type = 'sitegroupevent_event';
                            $groupTopic->resource_id = $newEventEntry->event_id;
                            //$groupTopic->setFromArray($topicSelectData);
                            $groupTopic->save();
                            $groupTopic->creation_date = $topicSelectData['creation_date'];
                            $groupTopic->save();
                            //ADD ACTIVITY
                            if ($activity_group) {
                              $activityFeedType = null;
                              if (Engine_Api::_()->sitegroup()->isGroupOwner($sitegroup, $groupTopic->getOwner()) && Engine_Api::_()->sitegroup()->isFeedTypeGroupEnable())
                                $activityFeedType = 'sitegroup_admin_topic_create';
                              elseif ($sitegroup->all_post || Engine_Api::_()->sitegroup()->isGroupOwner($sitegroup, $groupTopic->getOwner()))
                                $activityFeedType = 'sitegroup_topic_create';

                              if ($activityFeedType) {
                                $action = $activityTable->addActivity($groupTopic->getOwner(), $sitegroup, $activityFeedType);
                                Engine_Api::_()->getApi('subCore', 'sitegroup')->deleteFeedStream($action);
                              }
                              if ($action) {
                                $activityTable->attachActivity($action, $groupTopic);
                                $action->date = $groupTopic->creation_date;
                                $action->save();
                              }
                            }

                            //START FETCH TOPIC POST'S
                            $postEventTable = Engine_Api::_()->getDbtable('posts', 'event');
                            $postEventTableName = $postEventTable->info('name');
                            $postSelect = $postEventTable->select()
                                    ->from($postEventTableName)
                                    ->where('topic_id = ?', $topicSelectData['topic_id'])
                                    ->where('event_id = ?', $event->event_id);
                            $postSelectDatas = $postTable->fetchAll($postSelect);
                            if (!empty($postSelectDatas)) {
                              $postSelectDatas = $postSelectDatas->toArray();

                              foreach ($postSelectDatas as $postSelectData) {
                                $groupPost = $sitegroupPostTable->createRow();
                                $groupPost->topic_id = $groupTopic->topic_id;
                                $groupPost->group_id = $sitegroup->group_id;
                                $groupPost->user_id = $postSelectData['user_id'];
                                $groupPost->body = $postSelectData['body'];
                                $groupPost->creation_date = $postSelectData['creation_date'];
                                $groupPost->modified_date = $postSelectData['modified_date'];
                                $groupPost->creation_date = $postSelectData['creation_date'];
                                $groupPost->save();

                                //ADD ACTIVITY
                                $activityFeedType = null;
                                if (Engine_Api::_()->sitegroup()->isGroupOwner($sitegroup, $groupPost->getOwner()) && Engine_Api::_()->sitegroup()->isFeedTypeGroupEnable())
                                  $activityFeedType = 'sitegroup_admin_topic_reply';
                                elseif ($sitegroup->all_post || Engine_Api::_()->sitegroup()->isGroupOwner($sitegroup, $groupPost->getOwner()))
                                  $activityFeedType = 'sitegroup_topic_reply';

                                //ACTIVITY      
                                if ($activityFeedType) {
                                  $action = $activityTable->addActivity($groupPost->getOwner(), $sitegroup, $activityFeedType);
                                  Engine_Api::_()->getApi('subCore', 'sitegroup')->deleteFeedStream($action);
                                  if (!empty($action)) {
                                    $action->attach($groupPost, Activity_Model_Action::ATTACH_DESCRIPTION);
                                    $action->date = $groupPost->creation_date;
                                    $action->save();
                                  }
                                }
                              }
                            }
                            //END FETCH TOPIC POST'S
                            //START FETCH TOPIC WATCH
                            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advgroup')) {
															$topicEventWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'advgroup');
                            } else {
															$topicEventWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'group');
                            }
                            $topicWatchData = $topicEventWatchesTable->fetchRow(array('resource_id = ?' => $event->event_id, 'topic_id = ?' => $topicSelectData['topic_id'], 'user_id = ?' => $topicSelectData['user_id']));
                            if (!empty($topicWatchData)) {
                              $watch = $topicWatchData->watch;

                              $sitegroupTopicWatchesTable->insert(array(
                                  'resource_id' => $groupTopic->group_id,
                                  'topic_id' => $groupTopic->topic_id,
                                  'user_id' => $topicSelectData['user_id'],
                                  'watch' => $watch,
                                  'group_id' => $sitegroup->group_id,
                              ));
                            }
                            //END FETCH TOPIC WATCH
                          }
                        }
                      }
                      //END FETCH DISCUSSION DATA
                      //START FETCH PHOTO DATA
                      $selectEventPhoto = $groupEventPhotosTable->select()
                              ->from($groupEventPhotosTable->info('name'))
                              ->where('event_id = ?', $groupEventsData->event_id);
                      $groupEventPhotoDatas = $groupEventPhotosTable->fetchAll($selectEventPhoto);

                      if (!empty($groupEventPhotoDatas)) {

                        $groupEventPhotoDatas = $groupEventPhotoDatas->toArray();

                        if (empty($event->photo_id)) {
                          foreach ($groupEventPhotoDatas as $groupEventPhotoData) {
                            $event->photo_id = $groupEventPhotoData['photo_id'];
                            break;
                          }
                        }

                        if (!empty($event->photo_id)) {
                          $groupEventPhotoData = $groupEventPhotosTable->fetchRow(array('file_id = ?' => $event->photo_id));
                          if (!empty($groupEventPhotoData)) {
                            $storageData = $storageTable->fetchRow(array('file_id = ?' => $groupEventPhotoData->file_id));

                            if (!empty($storageData)) {

                              $sitgroupevent->setPhoto($storageData->storage_path);

                              $album_id = $sitegroupEventAlbumsTable->update(array('photo_id' => $sitgroupevent->photo_id), array('event_id = ?' => $sitgroupevent->event_id));

                              $groupProfilePhoto = Engine_Api::_()->getDbTable('photos', 'sitegroupevent')->fetchRow(array('file_id = ?' => $sitgroupevent->photo_id));
                              if (!empty($groupProfilePhoto)) {
                                $groupProfilePhotoId = $groupProfilePhoto->photo_id;
                              } else {
                                $groupProfilePhotoId = $sitgroupevent->photo_id;
                              }

                              //START FETCH LIKES
                              $this->likeItems('sitegroupevent_photo', $groupProfilePhotoId, 'event_photo', $event->photo_id, $sitelikeEnabled, $activity_group);
                              //END FETCH LIKES
                              //START FETCH COMMENTS
                              $this->commentItems('sitegroupevent_photo', $groupProfilePhotoId, 'event_photo', $event->photo_id);
                              //END FETCH COMMENTS
                              //START FETCH TAGGER DETAIL
                              $tagmapsTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
                              $tagmapsTableName = $tagmapsTable->info('name');
                              $selectTagmaps = $tagmapsTable->select()
                                      ->from($tagmapsTableName, 'tagmap_id')
                                      ->where('resource_type = ?', 'event_photo')
                                      ->where('resource_id = ?', $event->photo_id);
                              $selectTagmapsDatas = $tagmapsTable->fetchAll($selectTagmaps);
                              foreach ($selectTagmapsDatas as $selectTagmapsData) {
                                $tagMap = Engine_Api::_()->getItem('core_tag_map', $selectTagmapsData->tagmap_id);

                                $newTagmapEntry = $tagmapsTable->createRow();
                                $newTagmapEntry->resource_type = 'sitegroupevent_photo';
                                $newTagmapEntry->resource_id = $groupProfilePhotoId;
                                $newTagmapEntry->tagger_type = 'user';
                                $newTagmapEntry->tagger_id = $tagMap->tagger_id;
                                $newTagmapEntry->tag_type = 'user';
                                $newTagmapEntry->tag_id = $tagMap->tag_id;
                                $newTagmapEntry->creation_date = $tagMap->creation_date;
                                $newTagmapEntry->extra = $tagMap->extra;
                                $newTagmapEntry->save();
                                $newTagmapEntry->creation_date = $tagMap->creation_date;
                                $newTagmapEntry->save();
                              }
                              //END FETCH TAGGER DETAIL
                            }
                          }

                          $fetchDefaultAlbum = $sitegroupEventAlbumsTable->fetchRow(array('event_id = ?' => $sitgroupevent->event_id));
                          if (!empty($fetchDefaultAlbum)) {

                            $order = 999;
                            foreach ($groupEventPhotoDatas as $eventPhotoData) {

                              if ($eventPhotoData['file_id'] != $event->photo_id) {
                                $params = array(
                                    'collection_id' => $fetchDefaultAlbum->album_id,
                                    'album_id' => $fetchDefaultAlbum->album_id,
                                    'event_id' => $sitgroupevent->event_id,
                                    'user_id' => $eventPhotoData['user_id'],
                                    'order' => $order,
                                );

                                $storageData = $storageTable->fetchRow(array('file_id = ?' => $eventPhotoData['file_id']));
                                if (!empty($storageData)) {
                                  $file = array();
                                  $file['tmp_name'] = $storageData->storage_path;
                                  $path_array = explode('/', $file['tmp_name']);
                                  $file['name'] = end($path_array);

                                  $groupEventPhoto = Engine_Api::_()->getDbtable('photos', 'sitegroupevent')->createPhoto($params, $file);
                                  if (!empty($groupEventPhoto)) {

                                    $order++;
                                    //START FETCH LIKES
                                    $this->likeItems('sitegroupevent_photo', $groupEventPhoto->photo_id, 'event_photo', $eventPhotoData['photo_id'], $sitelikeEnabled, $activity_group);
                                    //END FETCH LIKES
                                    //START FETCH COMMENTS
                                    $this->commentItems('sitegroupevent_photo', $groupEventPhoto->photo_id, 'event_photo', $eventPhotoData['photo_id']);
                                    //END FETCH COMMENTS
                                    //START FETCH TAGGER DETAIL
                                    $selectTagmaps = $tagmapsTable->select()
                                            ->from($tagmapsTableName, 'tagmap_id')
                                            ->where('resource_type = ?', 'event_photo')
                                            ->where('resource_id = ?', $eventPhotoData['photo_id']);
                                    $selectTagmapsDatas = $tagmapsTable->fetchAll($selectTagmaps);
                                    foreach ($selectTagmapsDatas as $selectTagmapsData) {
                                      $tagMap = Engine_Api::_()->getItem('core_tag_map', $selectTagmapsData->tagmap_id);

                                      $newTagmapEntry = $tagmapsTable->createRow();
                                      $newTagmapEntry->resource_type = 'sitegroupevent_photo';
                                      $newTagmapEntry->resource_id = $groupEventPhoto->photo_id;
                                      $newTagmapEntry->tagger_type = 'user';
                                      $newTagmapEntry->tagger_id = $tagMap->tagger_id;
                                      $newTagmapEntry->tag_type = 'user';
                                      $newTagmapEntry->tag_id = $tagMap->tag_id;
                                      $newTagmapEntry->creation_date = $tagMap->creation_date;
                                      $newTagmapEntry->extra = $tagMap->extra;
                                      $newTagmapEntry->save();
                                    }
                                    //END FETCH TAGGER DETAIL
                                  }
                                }
                              }
                            }
                          }
                        }
                      }
                      //END FETCH PHOTO DATA

                      $sitetagcheckinEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin');
                      if (!empty($groupEventsData->location) && !empty($sitetagcheckinEnabled)) {
                        $selectGroupEventLocation = $locationItemsTable->select()
                                ->from($locationItemsTable->info('name'))
                                ->where('resource_id = ?', $groupEventsData->event_id)
                                ->where('resource_type = ?', 'event');
                        $groupEventsLocationDatas = $locationItemsTable->fetchAll($selectGroupEventLocation);
                        foreach ($groupEventsLocationDatas as $groupEventsLocationData) {
                          $newEventLocationEntry = $locationItemsTable->createRow();
                          $newEventLocationEntry->resource_type = 'sitegroupevent_event';
                          $newEventLocationEntry->resource_id = $newEventEntry->event_id;
                          $newEventLocationEntry->location = $groupEventsLocationData->location;
                          $newEventLocationEntry->latitude = $groupEventsLocationData->latitude;
                          $newEventLocationEntry->longitude = $groupEventsLocationData->longitude;
                          $newEventLocationEntry->formatted_address = $groupEventsLocationData->formatted_address;
                          $newEventLocationEntry->country = $groupEventsLocationData->country;
                          $newEventLocationEntry->state = $groupEventsLocationData->state;
                          $newEventLocationEntry->zipcode = $groupEventsLocationData->zipcode;
                          $newEventLocationEntry->city = $groupEventsLocationData->city;
                          $newEventLocationEntry->address = $groupEventsLocationData->address;
                          $newEventLocationEntry->zoom = $groupEventsLocationData->zoom;
                          $newEventLocationEntry->save();
                        }
                        //event table entry of location id.
                        Engine_Api::_()->getItemTable('sitegroupevent_event')->update(array('seao_locationid' => $newEventLocationEntry->locationitem_id), array('event_id =?' => $newEventEntry->event_id));
                      }

                      $selectGroupMembers = $groupEventmembershipTable->select()
                              ->from($groupEventmembershipTable->info('name'))
                              ->where('resource_id = ?', $groupEventsData->event_id);
                      $groupEventMembershipDatas = $groupEventmembershipTable->fetchAll($selectGroupMembers);

                      foreach ($groupEventMembershipDatas as $groupEventMembershipData) {

                        $newEventmembersEntry = $sitegroupEventmembershipTable->createRow();
                        $newEventmembersEntry->resource_id = $newEventEntry->event_id;
                        $newEventmembersEntry->user_id = $groupEventMembershipData->user_id;
                        $newEventmembersEntry->active = $groupEventMembershipData->active;
                        $newEventmembersEntry->resource_approved = $groupEventMembershipData->resource_approved;
                        $newEventmembersEntry->user_approved = $groupEventMembershipData->user_approved;
                        $newEventmembersEntry->message = $groupEventMembershipData->message;
                        $newEventmembersEntry->rsvp = $groupEventMembershipData->rsvp;
                        $newEventmembersEntry->title = $groupEventMembershipData->title;
                        $newEventmembersEntry->save();
                      }

                      //START FETCH LIKES
                      $this->likeItems('sitegroupevent_event', $newEventEntry->event_id, 'event', $groupEventsData->event_id, $sitelikeEnabled, $activity_group);
                      //END FETCH LIKES
                    }
                  }
                }
              }
              //END FETCH EVENT DATA
              //START GROUP-DOCUMENT IMPORTING WORK            
              if (!empty($sitegroupDocumentEnabled) && !empty($groupdocumentEnabled)) {
                $group_field_id = $db->query("SHOW COLUMNS FROM engine4_sitegroupdocument_document_fields_meta LIKE 'group_field_id'")->fetch();

                if (empty($group_field_id)) {
                  //ADD MAPPING COLUMN IN SITEREVIEW TABLE
                  $group_field_id = $db->query("SHOW COLUMNS FROM engine4_sitegroupdocument_document_fields_meta LIKE 'group_field_id'")->fetch();
                  if (empty($group_field_id)) {
                    $db->query("ALTER TABLE `engine4_sitegroupdocument_document_fields_meta` ADD `group_field_id` INT( 11 ) NOT NULL DEFAULT '0'");
                  }

                  $field_map_array = $db->select()
                          ->from('engine4_groupdocument_document_fields_maps')
                          ->where('option_id = ?', 0)
                          ->where('field_id = ?', 0)
                          ->query()
                          ->fetchAll();
                  $field_map_array_count = count($field_map_array);

                  //if ($field_map_array_count < 1)
                  //die;

                  $child_id_array = array();
                  for ($c = 0; $c < $field_map_array_count; $c++) {
                    $child_id_array[] = $field_map_array[$c]['child_id'];
                  }
                  unset($c);
                  if(!empty($child_id_array)) {
                  $field_meta_array = $db->select()
                          ->from('engine4_groupdocument_document_fields_meta')
                          ->where('field_id IN (' . implode(', ', $child_id_array) . ')')
                          ->where('type != ?', 'profile_type')
                          ->query()
                          ->fetchAll();

                  // Copy each row
                  for ($c = 0; $c < $field_map_array_count; $c++) {

                    $formValues = array(
                        'type' => $field_meta_array[$c]['type'],
                        'label' => $field_meta_array[$c]['label'],
                        'description' => $field_meta_array[$c]['description'],
                        'alias' => $field_meta_array[$c]['alias'],
                        'required' => $field_meta_array[$c]['required'],
                        'display' => $field_meta_array[$c]['display'],
                        'publish' => 0,
                        'search' => 0, //$field_meta_array[$c]['search'],
                        //'show' => $field_meta_array[$c]['show'],
                        'order' => $field_meta_array[$c]['order'],
                        'config' => $field_meta_array[$c]['config'],
                        'validators' => $field_meta_array[$c]['validators'],
                        'filters' => $field_meta_array[$c]['filters'],
                        'style' => $field_meta_array[$c]['style'],
                        'error' => $field_meta_array[$c]['error'],
                    );

                    $field = Engine_Api::_()->fields()->createField('sitegroupdocument_document', $formValues);

                    $db->update('engine4_sitegroupdocument_document_fields_meta', array('config' => $field_meta_array[$c]['config'], 'group_field_id' => $field_meta_array[$c]['field_id']), array('field_id = ?' => $field->field_id));

                    if ($field_meta_array[$c]['type'] == 'select' || $field_meta_array[$c]['type'] == 'radio' || $field_meta_array[$c]['type'] == 'multiselect' || $field_meta_array[$c]['type'] == 'multi_checkbox') {
                      $field_options_array = $db->select()
                              ->from('engine4_groupdocument_document_fields_options')
                              ->where('field_id = ?', $field_meta_array[$c]['field_id'])
                              ->query()
                              ->fetchAll();
                      $field_options_order = 0;
                      foreach ($field_options_array as $field_options) {
                        $field_options_order++;
                        $field = Engine_Api::_()->fields()->getField($field->field_id, 'sitegroupdocument_document');
                        $option = Engine_Api::_()->fields()->createOption('sitegroupdocument_document', $field, array(
                            'label' => $field_options['label'],
                            'order' => $field_options_order,
                        ));

                        $morefield_map_array = $db->select()
                                ->from('engine4_groupdocument_document_fields_maps')
                                ->where('option_id = ?', $field_options['option_id'])
                                ->where('field_id = ?', $field_options['field_id'])
                                ->query()
                                ->fetchAll();
                        $morefield_map_array_count = count($morefield_map_array);

                        if ($morefield_map_array_count < 1)
                          continue;

                        $morechild_id_array = array();
                        for ($morec = 0; $morec < $morefield_map_array_count; $morec++) {
                          $morechild_id_array[] = $morefield_map_array[$morec]['child_id'];
                        }
                        unset($morec);

                        $morefield_meta_array = $db->select()
                                ->from('engine4_groupdocument_document_fields_meta')
                                ->where('field_id IN (' . implode(', ', $morechild_id_array) . ')')
                                ->where('type != ?', 'profile_type')
                                ->query()
                                ->fetchAll();

                        // Copy each row
                        for ($morec = 0; $morec < $morefield_map_array_count; $morec++) {

                          $moreformValues = array(
                              'option_id' => $option->option_id,
                              'type' => $morefield_meta_array[$morec]['type'],
                              'label' => $morefield_meta_array[$morec]['label'],
                              'description' => $morefield_meta_array[$morec]['description'],
                              'alias' => $morefield_meta_array[$morec]['alias'],
                              'required' => $morefield_meta_array[$morec]['required'],
                              'display' => $morefield_meta_array[$morec]['display'],
                              'publish' => 0,
                              'search' => 0, //$morefield_meta_array[$morec]['search'],
                              //'show' => $morefield_meta_array[$morec]['show'],
                              'order' => $morefield_meta_array[$morec]['order'],
                              'config' => $morefield_meta_array[$morec]['config'],
                              'validators' => $morefield_meta_array[$morec]['validators'],
                              'filters' => $morefield_meta_array[$morec]['filters'],
                              'style' => $morefield_meta_array[$morec]['style'],
                              'error' => $morefield_meta_array[$morec]['error'],
                          );

                          $morefield = Engine_Api::_()->fields()->createField('sitegroupdocument_document', $moreformValues);

                          $db->update('engine4_sitegroupdocument_document_fields_meta', array('config' => $morefield_meta_array[$morec]['config'], 'group_field_id' => $morefield_meta_array[$morec]['field_id']), array('field_id = ?' => $morefield->field_id));

                          if ($morefield_meta_array[$morec]['type'] == 'select' || $morefield_meta_array[$morec]['type'] == 'radio' || $morefield_meta_array[$morec]['type'] == 'multiselect' || $morefield_meta_array[$morec]['type'] == 'multi_checkbox') {
                            $morefield_options_array = $db->select()
                                    ->from('engine4_groupdocument_document_fields_options')
                                    ->where('field_id = ?', $morefield_meta_array[$morec]['field_id'])
                                    ->query()
                                    ->fetchAll();
                            $morefield_options_order = 0;
                            foreach ($morefield_options_array as $morefield_options) {
                              $morefield_options_order++;
                              $morefield = Engine_Api::_()->fields()->getField($morefield->field_id, 'sitegroupdocument_document');
                              $moreoption = Engine_Api::_()->fields()->createOption('sitegroupdocument_document', $morefield, array(
                                  'label' => $morefield_options['label'],
                                  'order' => $morefield_options_order,
                              ));
                            }
                          }
                        }
                      }
                    }
                  }
                  }
                }

                $selectGroupDocument = $groupDocumentsTable->select()
                        ->from($groupDocumentsTable->info('name'))
                        ->where('group_id = ?', $group_id);
                $groupDocumentsDatas = $groupDocumentsTable->fetchAll($selectGroupDocument);
                foreach ($groupDocumentsDatas as $groupDocumentsData) {
                  $newDocumentEntry = $sitegroupDocumentsTable->createRow();
                  $newDocumentEntry->owner_id = $groupDocumentsData->owner_id;
                  $newDocumentEntry->group_id = $sitgroup->group_id;
                  $newDocumentEntry->sitegroupdocument_title = $groupDocumentsData->groupdocument_title;
                  $newDocumentEntry->sitegroupdocument_description = $groupDocumentsData->groupdocument_description;

                  $newDocumentEntry->filename_id = 0;
                  $newDocumentEntry->storage_path = $groupDocumentsData->storage_path;

                  $newDocumentEntry->sitegroupdocument_license = $groupDocumentsData->groupdocument_license;
                  $newDocumentEntry->sitegroupdocument_private = $groupDocumentsData->groupdocument_private;
                  $newDocumentEntry->filemime = $groupDocumentsData->filemime;
                  $newDocumentEntry->filesize = $groupDocumentsData->filesize;
                  $newDocumentEntry->doc_id = $groupDocumentsData->doc_id;
                  $newDocumentEntry->secret_password = $groupDocumentsData->secret_password;
                  $newDocumentEntry->access_key = $groupDocumentsData->access_key;
                  $newDocumentEntry->fulltext = $groupDocumentsData->fulltext;
                  $newDocumentEntry->thumbnail = $groupDocumentsData->thumbnail;
                  $newDocumentEntry->creation_date = $groupDocumentsData->creation_date;
                  $newDocumentEntry->modified_date = $groupDocumentsData->modified_date;
                  $newDocumentEntry->comment_count = $groupDocumentsData->comment_count;
                  $newDocumentEntry->like_count = $groupDocumentsData->like_count;
                  $newDocumentEntry->views = $groupDocumentsData->views;
                  $newDocumentEntry->rating = $groupDocumentsData->rating;
                  $newDocumentEntry->email_allow = $groupDocumentsData->email_allow;
                  $newDocumentEntry->download_allow = $groupDocumentsData->download_allow;
                  $newDocumentEntry->secure_allow = $groupDocumentsData->secure_allow;
                  $newDocumentEntry->search = $groupDocumentsData->search;
                  $newDocumentEntry->draft = $groupDocumentsData->draft;
                  $newDocumentEntry->featured = $groupDocumentsData->featured;
                  $newDocumentEntry->approved = $groupDocumentsData->approved;
                  $newDocumentEntry->status = $groupDocumentsData->status;
                  $newDocumentEntry->save();

                  $newDocumentEntry->creation_date = $groupDocumentsData->creation_date;
                  $newDocumentEntry->save();

                  if ($groupDocumentsData->filename_id) {
                    $name = $storageTable->select()
                            ->from($storageTableName, 'name')
                            ->where('file_id = ?', $groupDocumentsData->filename_id)
                            ->where('parent_id = ?', $groupDocumentsData->document_id)
                            ->where('parent_type = ?', 'groupdocument_document')
                            ->query()
                            ->fetchColumn();
                    if ($name) {
                      $newDocumentEntry->storage_path = $name;
                      $newDocumentEntry->save();
                    }
                  }

                  if ($activity_group) {
                    //INSERT NEW ACTIVITY IF DOCUMENT IS JUST GETTING PUBLISHED
                    $action = $activityTable->getActionsByObject($newDocumentEntry);
                    if (count($action->toArray()) <= 0 && $newDocumentEntry->draft == 0 && $newDocumentEntry->approved == 1 && $newDocumentEntry->status == 1 && $newDocumentEntry->activity_feed == 0) {

                      $activityFeedType = null;
                      if (Engine_Api::_()->sitegroup()->isGroupOwner($sitegroup, $newDocumentEntry->getOwner()) && Engine_Api::_()->sitegroup()->isFeedTypeGroupEnable())
                        $activityFeedType = 'sitegroupdocument_admin_new';
                      elseif ($sitegroup->all_post || Engine_Api::_()->sitegroup()->isGroupOwner($sitegroup, $newDocumentEntry->getOwner()))
                        $activityFeedType = 'sitegroupdocument_new';

                      if ($activityFeedType) {
                        $action = $activityTable->addActivity($newDocumentEntry->getOwner(), $sitegroup, $activityFeedType);
                        Engine_Api::_()->getApi('subCore', 'sitegroup')->deleteFeedStream($action);
                      }

                      //MAKE SURE ACTION EXISTS BEFOR ATTACHING THE DOCUMENT TO THE ACTIVITY
                      if ($action != null) {
                        $activityTable->attachActivity($action, $newDocumentEntry);
                        $newDocumentEntry->activity_feed = 1;
                        $newDocumentEntry->save();

                        $action->date = $newDocumentEntry->creation_date;
                        $action->save();
                      }
                    }

                    foreach ($activityTable->getActionsByObject($newDocumentEntry) as $action) {
                      $activityTable->resetActivityBindings($action);
                    }
                  }

                  //START FETCH CUSTOM FIELD VALUES
                  $fieldValueSelect = $sitegroupdocumentMetaTable->select()
                          ->setIntegrityCheck(false)
                          ->from($sitegroupdocumentMetaTableName, array('field_id', 'type'))
                          ->joinInner($groupdocumentFieldValueTableName, "$groupdocumentFieldValueTableName.field_id = $sitegroupdocumentMetaTableName.group_field_id", array('value', 'index', 'field_id as group_field_id'))
                          ->where("$groupdocumentFieldValueTableName.item_id = ?", $groupDocumentsData->document_id);
                  $fieldValues = $sitegroupdocumentMetaTable->fetchAll($fieldValueSelect);
                  foreach ($fieldValues as $fieldValue) {
                    if ($fieldValue->type != 'multi_checkbox' && $fieldValue->type != 'multiselect' && $fieldValue->type != 'radio' && $fieldValue->type != 'select') {
                      $sitegroupdocumentFieldValueTable->insert(array('item_id' => $newDocumentEntry->document_id, 'field_id' => $fieldValue->field_id, 'index' => $fieldValue->index, 'value' => $fieldValue->value));
                    } else {

                      $groupdocumentFieldValues = $db->select()
                              ->from('engine4_groupdocument_document_fields_options')
                              ->where('field_id = ?', $fieldValue->group_field_id)
                              ->query()
                              ->fetchAll(Zend_Db::FETCH_COLUMN);

                      $sitegroupdocumentFieldValues = $db->select()
                              ->from('engine4_sitegroupdocument_document_fields_options')
                              ->where('field_id = ?', $fieldValue->field_id)
                              ->query()
                              ->fetchAll(Zend_Db::FETCH_COLUMN);

                      $mergeFieldValues = array_combine($sitegroupdocumentFieldValues, $groupdocumentFieldValues);
                      $value = array_search($fieldValue->value, $mergeFieldValues);
                      if (!empty($value)) {
                        $sitegroupdocumentFieldValueTable->insert(array('item_id' => $newDocumentEntry->document_id, 'field_id' => $fieldValue->field_id, 'index' => $fieldValue->index, 'value' => $value));
                      }
                    }
                  }
                  //END FETCH CUSTOM FIELD VALUES                  
                  //START FETCH RATTING DATA
                  $selectGroupdocumentRating = $groupdocumentRatingTable->select()
                          ->from($groupdocumentRatingTableName)
                          ->where('document_id = ?', $groupDocumentsData->document_id);

                  $groupdocumentRatingDatas = $groupdocumentRatingTable->fetchAll($selectGroupdocumentRating);
                  if (!empty($groupdocumentRatingDatas)) {
                    $groupdocumentRatingDatas = $groupdocumentRatingDatas->toArray();
                    foreach ($groupdocumentRatingDatas as $groupdocumentRatingData) {

                      $sitegroupdocumentRatingTable->insert(array(
                          'document_id' => $newDocumentEntry->document_id,
                          'user_id' => $groupdocumentRatingData['user_id'],
                          'rating' => $groupdocumentRatingData['rating']
                      ));
                    }
                  }
                  //END FETCH RATTING DATA           
                  //START FETCH COMMENTS
                  $this->commentItems('sitegroupdocument_document', $newDocumentEntry->document_id, 'groupdocument_document', $groupDocumentsData->document_id);
                  //END FETCH COMMENTS		 
                  //START FETCH LIKES
                  $this->likeItems('sitegroupdocument_document', $newDocumentEntry->document_id, 'groupdocument_document', $groupDocumentsData->document_id, $sitelikeEnabled, $activity_group);
                  //END FETCH LIKES	                
                }
              }

              //END GROUP-DOCUMENT IMPORTING WORK 
              //START FETCH POLL DATA
              if (!empty($sitegroupPollEnabled) && !empty($grouppollEnabled) && !empty($getListingImportData)) {

                $selectGroupPoll = $groupPollsTable->select()
                        ->from($groupPollsTable->info('name'))
                        ->where('group_id = ?', $group_id);
                $groupPollsDatas = $groupPollsTable->fetchAll($selectGroupPoll);

                if (!empty($groupPollsDatas)) {

                  foreach ($groupPollsDatas as $groupPollsData) {

                    $newPollEntry = $sitegroupPollsTable->createRow();
                    $newPollEntry->owner_id = $groupPollsData->owner_id;
                    $newPollEntry->group_id = $sitgroup->group_id;
                    $newPollEntry->parent_type = 'sitegroup_group';
                    $newPollEntry->title = $groupPollsData->title;
                    $newPollEntry->description = $groupPollsData->description;
                    $newPollEntry->creation_date = $groupPollsData->creation_date;
                    $newPollEntry->end_settings = $groupPollsData->end_settings;
                    $newPollEntry->end_time = $groupPollsData->end_time;
                    $newPollEntry->views = $groupPollsData->views;
                    $newPollEntry->comment_count = $groupPollsData->comment_count;
                    $newPollEntry->vote_count = $groupPollsData->vote_count;
                    $newPollEntry->search = $groupPollsData->search;
                    $newPollEntry->gp_auth_vote = $groupPollsData->gp_auth_vote;
                    $newPollEntry->approved = $groupPollsData->approved;
                    $newPollEntry->closed = $groupPollsData->closed;
                    $newPollEntry->save();
                    $newPollEntry->creation_date = $groupPollsData->creation_date;
                    $newPollEntry->save();

                    $selectGroupPollOptions = $groupPollOptionsTable->select()
                            ->from($groupPollOptionsTable->info('name'))
                            ->where('poll_id = ?', $groupPollsData->poll_id);
                    $groupPollsOptionsDatas = $groupPollOptionsTable->fetchAll($selectGroupPollOptions);
                    foreach ($groupPollsOptionsDatas as $groupPollsOptionsData) {
                      $newPollOptionEntry = $sitegroupoptionsTable->createRow();
                      $newPollOptionEntry->poll_id = $newPollEntry->poll_id;
                      $newPollOptionEntry->sitegrouppoll_option = $groupPollsOptionsData->grouppoll_option;
                      $newPollOptionEntry->votes = $groupPollsOptionsData->votes;
                      $newPollOptionEntry->save();
                    }

                    if ($activity_group) {

                      $activityFeedType = null;
                      if (Engine_Api::_()->sitegroup()->isGroupOwner($sitegroup, $newPollEntry->getOwner()) && Engine_Api::_()->sitegroup()->isFeedTypeGroupEnable())
                        $activityFeedType = 'sitegrouppoll_admin_new';
                      elseif ($sitegroup->all_post || Engine_Api::_()->sitegroup()->isGroupOwner($sitegroup, $newPollEntry->getOwner()))
                        $activityFeedType = 'sitegrouppoll_new';
                      if ($activityFeedType) {
                        $action = $activityTable->addActivity($newPollEntry->getOwner(), $sitegroup, $activityFeedType);
                        Engine_Api::_()->getApi('subCore', 'sitegroup')->deleteFeedStream($action);
                      }
                      if ($action != null) {
                        $activityTable->attachActivity($action, $newPollEntry);
                      }
                    }

                    $selectGroupPollVotes = $groupPollsVotesTable->select()
                            ->from($groupPollsVotesTable->info('name'))
                            ->where('poll_id = ?', $groupPollsData->poll_id);
                    $groupPollsVotesDatas = $groupPollsVotesTable->fetchAll($selectGroupPollVotes);
                    foreach ($groupPollsVotesDatas as $groupPollsVotesData) {
                      $newPollVotesEntry = $sitegroupvotesTable->createRow();
                      $newPollVotesEntry->poll_id = $newPollEntry->poll_id;
                      $newPollVotesEntry->owner_id = $groupPollsVotesData->owner_id;
                      $newPollVotesEntry->poll_option_id = $groupPollsVotesData->poll_option_id;
                      $newPollVotesEntry->creation_date = $groupPollsVotesData->creation_date;
                      $newPollVotesEntry->modified_date = $groupPollsVotesData->modified_date;
                      $newPollVotesEntry->save();
                      $newPollVotesEntry->creation_date = $groupPollsVotesData->creation_date;
                      $newPollVotesEntry->save();
                    }

                    //START FETCH LIKES
                    $this->likeItems('sitegrouppoll_poll', $newPollEntry->poll_id, 'grouppoll_poll', $groupPollsData->poll_id, $sitelikeEnabled, $activity_group);
                    //END FETCH LIKES
                    //START FETCH COMMENTS
                    $this->commentItems('sitegrouppoll_poll', $newPollEntry->poll_id, 'grouppoll_poll', $groupPollsData->poll_id);
                    //END FETCH COMMENTS                    
                  }
                }
              }
              //END FETCH POLL DATA
              
							//START FETCH ANNOUNCAMENT
							if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advgroup')) {

								$sitegroupAnnouncementsTable = Engine_Api::_()->getDbtable('announcements', 'sitegroup');
								$groupAnnouncementsTable = Engine_Api::_()->getDbtable('announcements', 'advgroup');
								$groupAnnouncementsTableName = $groupAnnouncementsTable->info('name');
								$selectGroupannouncements = $groupAnnouncementsTable->select()
											->from($groupAnnouncementsTableName)
											->where('group_id = ?', $group_id);
								$groupAnnouncementsDatas = $groupAnnouncementsTable->fetchAll($selectGroupannouncements);

								if (!empty($groupAnnouncementsDatas)) {

									foreach ($groupAnnouncementsDatas as $groupAnnouncementsData) {
										$sitegroupAnnouncementsEntry = $sitegroupAnnouncementsTable->createRow();
										$sitegroupAnnouncementsEntry->group_id = $sitgroup->group_id;
										$sitegroupAnnouncementsEntry->title = $groupAnnouncementsData->title;
										$sitegroupAnnouncementsEntry->body = $groupAnnouncementsData->body;
										$sitegroupAnnouncementsEntry->modified_date = $groupAnnouncementsData->modified_date;
										$sitegroupAnnouncementsEntry->startdate = $groupAnnouncementsData->creation_date;
										$sitegroupAnnouncementsEntry->expirydate = $groupAnnouncementsData->creation_date;
										$sitegroupAnnouncementsEntry->status = 1;
										$sitegroupAnnouncementsEntry->save();
										$sitegroupAnnouncementsEntry->creation_date = $groupAnnouncementsData->creation_date;
										$sitegroupAnnouncementsEntry->save();
									}
								}
							}
							//END FETCH ANNOUNCAMENT

							//START FETCH AL LINK
							if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advgroup')) {

								$coreLinkTable = Engine_Api::_()->getDbtable('links', 'core');
								$groupLinksTable = Engine_Api::_()->getDbtable('links', 'advgroup');
								$groupLinksTableName = $groupLinksTable->info('name');
								$selectLinks = $groupLinksTable->select()
																							->from($groupLinksTableName)
																							->where('group_id = ?', $group_id);
								$grouplinksDatas = $groupLinksTable->fetchAll($selectLinks);

								if (!empty($grouplinksDatas)) {
									foreach ($grouplinksDatas as $grouplinksData) {
										$coreLinkEntry = $coreLinkTable->createRow();
										$coreLinkEntry->uri = $grouplinksData->link_content ;
										$coreLinkEntry->title = $grouplinksData->title;
										$coreLinkEntry->description = $grouplinksData->description;
										$coreLinkEntry->parent_type = 'sitegroup_group';
										$coreLinkEntry->parent_id = $sitgroup->group_id;
										$coreLinkEntry->owner_type = 'user';
										$coreLinkEntry->owner_id = $grouplinksData->owner_id;
										$coreLinkEntry->save();
										$coreLinkEntry->creation_date = $grouplinksData->creation_date;
										$coreLinkEntry->save();
									}
								}
							}
							//END FETCH ALL LINK
              //START FETCH SITEGROUP-FORM DATA
              $sitegroupFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupform');
              if ($sitegroupFormEnabled) {
                $sitegroupform = $table_option->createRow();
                $sitegroupform->label = $sitgroup->title;
                $sitegroupform->field_id = 1;
                $option_id = $sitegroupform->save();
                $optionids = $optionid->createRow();
                $optionids->option_id = $option_id;
                $optionids->group_id = $sitgroup->group_id;
                $optionids->save();
                $sitegroupforms = $sitegroupformtable->createRow();
                $sitegroupforms->group_id = $sitgroup->group_id;
                $sitegroupforms->save();
              }
              //END FETCH SITEGROUP-FORM DATA
              //ACTIVITY FEEDS THROUGH POST BOX
              if ($activity_group) {
                $select = $activityTable->select()
                        ->from($activityTableName)
                        ->where('object_id = ?', $group_id)
                        ->where('object_type = ?', 'group')
                        ->where("type = ?", "post")
                        ->order('action_id ASC');

                $activiteyDatas = $activityTable->fetchAll($select);
                foreach ($activiteyDatas as $activiteyData) {
                  $user = Engine_Api::_()->getItem($activiteyData->subject_type, $activiteyData->subject_id);

                  if (Engine_Api::_()->sitegroup()->isGroupOwner($sitegroup, $user) && Engine_Api::_()->sitegroup()->isFeedTypeGroupEnable()) {
                    $feedType = 'sitegroup_post_self';
                  } else {
                    $feedType = 'sitegroup_post';
                  }
                  $activiteyData->params = array();
                  if(!empty($activiteyData->params)) {
                    $activiteyData->params = Zend_Json_Decoder::decode($activiteyData->params);
                  }                  
                  $action = $activityTable->addActivity($user, $sitegroup, $feedType, $activiteyData->body, $activiteyData->params);
                  $action->date = $activiteyData->date;
                  $action->save();

                  $attachement = $activityAttachmentsTable->fetchRow(array('action_id = ?' => $activiteyData->action_id));
                  if ($attachement) {

                    if ($action != null) {
                      $attachementItem = Engine_Api::_()->getItem($attachement->type, $attachement->id);
                      $activityTable->attachActivity($action, $attachementItem);
                    }
                  }
                }

                if ($sitetagcheckinEnabled) {

                  $select = $activityTable->select()
                          ->from($activityTableName)
                          ->where('object_id = ?', $group_id)
                          ->where('object_type = ?', 'group')
                          ->where("type = ?", "sitetagcheckin_add_to_map")
                          ->order('action_id ASC');

                  $activiteyDatas = $activityTable->fetchAll($select);
                  foreach ($activiteyDatas as $activiteyData) {
                    $user = Engine_Api::_()->getItem($activiteyData->subject_type, $activiteyData->subject_id);

                    $activiteyData->params['type'] = 'Group';
                    $activiteyData->params['resource_guid'] = "sitegroup_group_$sitegroup->group_id";

                    $action = $activityTable->addActivity($user, $sitegroup, 'sitetagcheckin_add_to_map', $activiteyData->body, $activiteyData->params);

                    $action->date = $activiteyData->date;
                    $action->save();

                    $attachement = $activityAttachmentsTable->fetchRow(array('action_id = ?' => $activiteyData->action_id));
                    if ($attachement) {

                      if ($action != null) {
                        $attachementItem = Engine_Api::_()->getItem($attachement->type, $attachement->id);
                        $activityTable->attachActivity($action, $attachementItem);
                      }
                    }
                  }
                }
              }
            }
            $db->commit();
            $this->view->assigned_previous_id = $group_id;

            //CREATE LOG ENTRY IN LOG FILE
            if (file_exists(APPLICATION_PATH . '/temporary/log/GroupToGroupImport.log')) {
              $myFile = APPLICATION_PATH . '/temporary/log/GroupToGroupImport.log';
              $error = Zend_Registry::get('Zend_Translate')->_("can't open file");
              $fh = fopen($myFile, 'a') or die($error);
              $current_time = date('D, d M Y H:i:s T');
              $group_id = $sitegroup->group_id;
              $group_title = $sitegroup->title;
              $stringData = $this->view->translate('Group with ID ') . $group_id . $this->view->translate(' is successfully imported into a Group with ID ') . $group_id . $this->view->translate(' at ') . $current_time . $this->view->translate(". Title of that Group is '") . $group_title . "'.\n\n";
              fwrite($fh, $stringData);
              fclose($fh);
            }

            if ($next_import_count >= 100) {
              $this->_redirect("admin/sitegroup/importlisting/index?start_import=1&activity_group=$activity_group&select_package_id=$select_package_id");
            }
          } catch (Exception $e) {
            $db->rollBack();
            throw $e;
          }
        }
      }
    }
  }

  //FETCH LIKE ACCORDING TO ITEM.
  public function likeItems($new_resource_type, $new_resource_id, $old_resource_type, $old_resource_id, $sitelikeEnabled = 0, $activity_group = 0) {

    $activityTable = Engine_Api::_()->getDbtable('actions', 'activity');
    $activityTableName = $activityTable->info('name');

    $activityAttachmentsTable = Engine_Api::_()->getDbtable('attachments', 'activity');
    $activityAttachmentsTableName = $activityAttachmentsTable->info('name');

    $likeTable = Engine_Api::_()->getDbtable('likes', 'core');

    $selectLike = $likeTable->select()
            ->from($likeTable->info('name'), 'like_id')
            ->where('resource_type = ?', $old_resource_type)
            ->where('resource_id = ?', $old_resource_id)
            ->order("creation_date ASC")
    ;
    $selectLikeDatas = $likeTable->fetchAll($selectLike);

    foreach ($selectLikeDatas as $selectLikeData) {
      $like = Engine_Api::_()->getItem('core_like', $selectLikeData->like_id);
      $newLikeEntry = $likeTable->createRow();
      $newLikeEntry->resource_type = $new_resource_type;
      $newLikeEntry->resource_id = $new_resource_id;
      $newLikeEntry->poster_type = 'user';
      $newLikeEntry->poster_id = $like->poster_id;
      $newLikeEntry->creation_date = $like->creation_date;
      $newLikeEntry->save();

      $newLikeEntry->creation_date = $like->creation_date;
      $newLikeEntry->save();

      //ACTIVITY FEED WORK
      if ($sitelikeEnabled && $activity_group) {
        $select = $activityTable->select()
                ->from($activityTableName)
                ->where('object_id = ?', $old_resource_id)
                ->where('object_type = ?', $old_resource_type)
                ->where("type = ?", "like_$old_resource_type")
                ->order('action_id ASC');

        $activiteyDatas = $activityTable->fetchAll($select);
        foreach ($activiteyDatas as $activiteyData) {

          $user = Engine_Api::_()->getItem($activiteyData->subject_type, $activiteyData->subject_id);
          $resource = Engine_Api::_()->getItem($new_resource_type, $new_resource_id);
          Engine_Api::_()->sitelike()->setLikeFeed($user, $resource);
        }
      }
    }
  }

  //FETCH COMMENT ACCORDING TO ITEM.
  public function commentItems($new_resource_type, $new_resource_id, $old_resource_type, $old_resource_id) {

    $activityTable = Engine_Api::_()->getDbtable('actions', 'activity');
    $activityTableName = $activityTable->info('name');

    $activityAttachmentsTable = Engine_Api::_()->getDbtable('attachments', 'activity');
    $activityAttachmentsTableName = $activityAttachmentsTable->info('name');

    $commentTable = Engine_Api::_()->getDbtable('comments', 'core');

    $selectLike = $commentTable->select()
            ->from($commentTable->info('name'), 'comment_id')
            ->where('resource_type = ?', $old_resource_type)
            ->where('resource_id = ?', $old_resource_id)
            ->order("creation_date ASC")
    ;
    $selectLikeDatas = $commentTable->fetchAll($selectLike);
    foreach ($selectLikeDatas as $selectLikeData) {
      $comment = Engine_Api::_()->getItem('core_comment', $selectLikeData->comment_id);
      $newCommentEntry = $commentTable->createRow();
      $newCommentEntry->resource_type = $new_resource_type;
      $newCommentEntry->resource_id = $new_resource_id;
      $newCommentEntry->poster_type = 'user';
      $newCommentEntry->poster_id = $comment->poster_id;
      $newCommentEntry->body = $comment->body;
      $newCommentEntry->creation_date = $comment->creation_date;
      $newCommentEntry->like_count = $comment->like_count;
      $newCommentEntry->save();

      $newCommentEntry->creation_date = $comment->creation_date;
      $newCommentEntry->save();
    }
  }

  public function getParseUrl($slug) {
    //$url = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($url))), '-');
		setlocale(LC_CTYPE, 'pl_PL.utf8');
		$slug = @iconv('UTF-8', 'ASCII//TRANSLIT', $slug);
		$slug = strtolower($slug);
		$slug = strtr($slug, array('&' => '-', '"' => '-', '&' . '#039;' => '-', '<' => '-', '>' => '-', '\'' => '-'));
		$slug = preg_replace('/^[^a-z0-9]{0,}(.*?)[^a-z0-9]{0,}$/si', '\\1', $slug);
		$slug = preg_replace('/[^a-z0-9\-]/', '-', $slug);
		$url = preg_replace('/[\-]{2,}/', '-', $slug);
    if (($this->_bannedUrls && in_array($url, $this->_bannedUrls)) || (Engine_Api::_()->sitegroup()->getGroupId($url)) || (Engine_Api::_()->hasItemType('sitegroup_group') && Engine_Api::_()->sitegroup()->getGroupId($url))) {
      return $this->getParseUrl($url . "-1");
    }
    return $url;
  }

}
