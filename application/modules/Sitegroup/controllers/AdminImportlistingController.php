<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminImportlistingController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_AdminImportlistingController extends Core_Controller_Action_Admin {

  //ACTION FOR IMPORTING DATA FROM LISTING TO GROUP
  public function indexAction() {

    //INCREASE THE MEMORY ALLOCATION SIZE AND INFINITE SET TIME OUT
    ini_set('memory_limit', '2048M');
    set_time_limit(0);

    //START CODE FOR CREATING THE ListingToGroupImport.log FILE
    if (!file_exists(APPLICATION_PATH . '/temporary/log/ListingToGroupImport.log')) {
      $log = new Zend_Log();
      try {
        $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/ListingToGroupImport.log'));
      } catch (Exception $e) {
        //CHECK DIRECTORY
        if (!@is_dir(APPLICATION_PATH . '/temporary/log') && @mkdir(APPLICATION_PATH . '/temporary/log', 0777, true)) {
          $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/ListingToGroupImport.log'));
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
    if (file_exists(APPLICATION_PATH . '/temporary/log/ListingToGroupImport.log')) {
      @chmod(APPLICATION_PATH . '/temporary/log/ListingToGroupImport.log', 0777);
    }
    //END CODE FOR CREATING THE ListingToGroupImport.log FILE
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_admin_main', array(), 'sitegroup_admin_main_import');

    //START IMPORTING WORK IF LIST AND SITEGROUP IS INSTALLED AND ACTIVATE
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('list') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroup')) {

      //ADD NEW COLUMN IN LISTING TABLE
      $db = Engine_Db_Table::getDefaultAdapter();
      $type_array = $db->query("SHOW COLUMNS FROM engine4_list_listings LIKE 'is_import'")->fetch();
      if (empty($type_array)) {
        $run_query = $db->query("ALTER TABLE `engine4_list_listings` ADD `is_import` TINYINT( 2 ) NOT NULL DEFAULT '0' AFTER `subcategory_id` ");
      }

      //START IF IMPORTING IS BREAKED BY SOME REASON
      $listingTable = Engine_Api::_()->getDbTable('listings', 'list');
      $listingTableName = $listingTable->info('name');
      $selectListings = $listingTable->select()
              ->from($listingTableName, 'listing_id')
              ->where('is_import != ?', 1)
              ->order('listing_id ASC');
      $listingDatas = $listingTable->fetchAll($selectListings);

      $this->view->first_listing_id = $first_listing_id = 0;
      $this->view->last_listing_id = $last_listing_id = 0;

      if (!empty($listingDatas)) {

        $flag_first_listing_id = 1;

        foreach ($listingDatas as $listingData) {

          if ($flag_first_listing_id == 1) {
            $this->view->first_listing_id = $first_listing_id = $listingData->listing_id;
          }
          $flag_first_listing_id++;

          $this->view->last_listing_id = $last_listing_id = $listingData->listing_id;
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

        //START FETCH CATEGORY WORK
        $groupCategoryTable = Engine_Api::_()->getDbtable('categories', 'sitegroup');
        $groupCategoryTableName = $groupCategoryTable->info('name');
        $selectGroupCategory = $groupCategoryTable->select()
                ->from($groupCategoryTableName, 'category_name')
                ->where('cat_dependency = ?', 0);
        $groupCategoryDatas = $groupCategoryTable->fetchAll($selectGroupCategory);
        if (!empty($groupCategoryDatas)) {
          $groupCategoryDatas = $groupCategoryDatas->toArray();
        }

        $groupCategoryInArrayData = array();
        foreach ($groupCategoryDatas as $groupCategoryData) {
          $groupCategoryInArrayData[] = $groupCategoryData['category_name'];
        }

        $listCategoryTable = Engine_Api::_()->getDbtable('categories', 'list');
        $listCategoryTableName = $listCategoryTable->info('name');
        $selectListCategory = $listCategoryTable->select()
                ->from($listCategoryTableName)
                ->where('cat_dependency = ?', 0);
        $listCategoryDatas = $listCategoryTable->fetchAll($selectListCategory);
        if (!empty($listCategoryDatas)) {
          $listCategoryDatas = $listCategoryDatas->toArray();
          foreach ($listCategoryDatas as $listCategoryData) {
            if (!in_array($listCategoryData['category_name'], $groupCategoryInArrayData)) {
              $newCategory = $groupCategoryTable->createRow();
              //$newCategory->user_id = $listCategoryData['user_id'];
              $newCategory->category_name = $listCategoryData['category_name'];
              $newCategory->cat_dependency = 0;
              $newCategory->cat_order = 9999;
              $newCategory->save();

              $selectListSubCategory = $listCategoryTable->select()
                      ->from($listCategoryTableName)
                      ->where('cat_dependency = ?', $listCategoryData['category_id']);
              $listSubCategoryDatas = $listCategoryTable->fetchAll($selectListSubCategory);
              foreach ($listSubCategoryDatas as $listSubCategoryData) {
                $newSubCategory = $groupCategoryTable->createRow();
                //$newSubCategory->user_id = $listCategoryData['user_id'];
                $newSubCategory->category_name = $listSubCategoryData->category_name;
                $newSubCategory->cat_dependency = $newCategory->category_id;
                $newSubCategory->cat_order = 9999;
                $newSubCategory->save();
              }
            } elseif (in_array($listCategoryData['category_name'], $groupCategoryInArrayData)) {

              $groupCategory = $groupCategoryTable->fetchRow(array('category_name = ?' => $listCategoryData['category_name'], 'cat_dependency = ?' => 0));
              if (!empty($groupCategory))
                $groupCategoryId = $groupCategory->category_id;

              $selectGroupSubCategory = $groupCategoryTable->select()
                      ->from($groupCategoryTableName, array('category_name'))
                      ->where('cat_dependency = ?', $groupCategoryId);
              $groupSubCategoryDatas = $groupCategoryTable->fetchAll($selectGroupSubCategory);
              if (!empty($groupSubCategoryDatas)) {
                $groupSubCategoryDatas = $groupSubCategoryDatas->toArray();
              }

              $groupSubCategoryInArrayData = array();
              foreach ($groupSubCategoryDatas as $groupSubCategoryData) {
                $groupSubCategoryInArrayData[] = $groupSubCategoryData['category_name'];
              }

              $listCategory = $listCategoryTable->fetchRow(array('category_name = ?' => $listCategoryData['category_name'], 'cat_dependency = ?' => 0));
              if (!empty($listCategory))
                $listCategoryId = $listCategory->category_id;

              $selectListSubCategory = $listCategoryTable->select()
                      ->from($listCategoryTableName)
                      ->where('cat_dependency = ?', $listCategoryId);
              $listSubCategoryDatas = $listCategoryTable->fetchAll($selectListSubCategory);
              if (!empty($listSubCategoryDatas)) {
                $listSubCategoryDatas = $listSubCategoryDatas->toArray();
              }

              foreach ($listSubCategoryDatas as $listSubCategoryData) {
                if (!in_array($listSubCategoryData['category_name'], $groupSubCategoryInArrayData)) {
                  $newSubCategory = $groupCategoryTable->createRow();
                  //$newSubCategory->user_id = $listSubCategoryData['user_id'];
                  $newSubCategory->category_name = $listSubCategoryData['category_name'];
                  $newSubCategory->cat_dependency = $groupCategoryId;
                  $newSubCategory->cat_order = 9999;
                  $newSubCategory->save();
                }
              }
            }
          }
        }
        //END FETCH CATEGORY WOR
        //START COMMAN DATA
        $package_id = Engine_Api::_()->getItemtable('sitegroup_package')->fetchRow(array('defaultpackage = ?' => 1))->package_id;
        $package = Engine_Api::_()->getItemTable('sitegroup_package')->fetchRow(array('package_id = ?' => $package_id));

        $metaTable = Engine_Api::_()->fields()->getTable('list_listing', 'meta');
        $selectMetaData = $metaTable->select()->where('type = ?', 'currency');
        $metaData = $metaTable->fetchRow($selectMetaData);

        $table = Engine_Api::_()->getDbtable('groups', 'sitegroup');

        $likeTable = Engine_Api::_()->getDbtable('likes', 'core');
        $likeTableName = $likeTable->info('name');

        $commentTable = Engine_Api::_()->getDbtable('comments', 'core');
        $commentTableName = $commentTable->info('name');

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion')) {
          $topicTable = Engine_Api::_()->getDbtable('topics', 'list');
          $topicTableName = $topicTable->info('name');
          $groupTopicTable = Engine_Api::_()->getDbtable('topics', 'sitegroup');
          $groupPostTable = Engine_Api::_()->getDbtable('posts', 'sitegroup');

          $postTable = Engine_Api::_()->getDbtable('posts', 'list');
          $postTableName = $postTable->info('name');

          $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'list');
          $groupTopicWatchesTable = Engine_Api::_()->getDbtable('topicwatches', 'sitegroup');
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
          $reviewTable = Engine_Api::_()->getDbtable('reviews', 'list');
          $reviewTableName = $reviewTable->info('name');
          $groupReviewTable = Engine_Api::_()->getDbtable('reviews', 'sitegroupreview');
          $reviewRatingTable = Engine_Api::_()->getDbtable('ratings', 'sitegroupreview');
        }

        $manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup');

        $listLocationTable = Engine_Api::_()->getDbtable('locations', 'list');

        $groupLocationTable = Engine_Api::_()->getDbtable('locations', 'sitegroup');

        $albumTable = Engine_Api::_()->getDbtable('albums', 'sitegroup');
        $listPhotoTable = Engine_Api::_()->getDbtable('photos', 'list');
        $storageTable = Engine_Api::_()->getDbtable('files', 'storage');

        $sitegroupFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupform');
        if ($sitegroupFormEnabled) {
          $sitegroupformtable = Engine_Api::_()->getDbtable('sitegroupforms', 'sitegroupform');
          $optionid = Engine_Api::_()->getDbtable('groupquetions', 'sitegroupform');
          $table_option = Engine_Api::_()->fields()->getTable('sitegroupform', 'options');
        }

        $writeTable = Engine_Api::_()->getDbtable('writes', 'list');
        $groupWriteTable = Engine_Api::_()->getDbtable('writes', 'sitegroup');

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('video') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo')) {

          $groupVideoTable = Engine_Api::_()->getDbtable('videos', 'sitegroupvideo');
          $groupVideoTableName = $groupVideoTable->info('name');

          $listVideoRating = Engine_Api::_()->getDbTable('ratings', 'video');
          $listVideoRatingName = $listVideoRating->info('name');

          $groupVideoRatingTable = Engine_Api::_()->getDbTable('ratings', 'sitegroupvideo');

          $listVideoTable = Engine_Api::_()->getDbtable('clasfvideos', 'list');
          $listVideoTableName = $listVideoTable->info('name');
        }

        $groupAdminTable = Engine_Api::_()->getDbtable('pages', 'core');
        $groupAdminTableName = $groupAdminTable->info('name');
        $groupTable = Engine_Api::_()->getDbtable('contentgroups', 'sitegroup');
        //END COMMON DATA

        $selectListings = $listingTable->select()
                ->where('listing_id >= ?', $assigned_previous_id)
                ->from($listingTableName, 'listing_id')
                ->where('is_import != ?', 1)
                ->order('listing_id ASC');
        $listingDatas = $listingTable->fetchAll($selectListings);

        $next_import_count = 0;
        foreach ($listingDatas as $listingData) {

          $listing_id = $listingData->listing_id;

          if (!empty($listing_id)) {
            $listing = Engine_Api::_()->getItem('list_listing', $listing_id);

            $sitegroup = $table->createRow();
            $sitegroup->title = $listing->title;
            $sitegroup->body = $listing->body;
            $sitegroup->overview = $listing->overview;
            $sitegroup->owner_id = $listing->owner_id;

            //START FETCH LIST CATEGORY AND SUB-CATEGORY
            if (!empty($listing->category_id)) {
              $listCategory = $listCategoryTable->fetchRow(array('category_id = ?' => $listing->category_id, 'cat_dependency = ?' => 0));
              if (!empty($listCategory)) {
                $listCategoryName = $listCategory->category_name;

                if (!empty($listCategoryName)) {
                  $groupCategory = $groupCategoryTable->fetchRow(array('category_name = ?' => $listCategoryName, 'cat_dependency = ?' => 0));
                  if (!empty($groupCategory)) {
                    $groupCategoryId = $sitegroup->category_id = $groupCategory->category_id;
                  }

                  $listSubCategory = $listCategoryTable->fetchRow(array('category_id = ?' => $listing->subcategory_id, 'cat_dependency = ?' => $listing->category_id));
                  if (!empty($listSubCategory)) {
                    $listSubCategoryName = $listSubCategory->category_name;

                    $groupSubCategory = $groupCategoryTable->fetchRow(array('category_name = ?' => $listSubCategoryName, 'cat_dependency = ?' => $groupCategoryId));
                    if (!empty($groupSubCategory)) {
                      $sitegroup->subcategory_id = $groupSubCategory->category_id;
                    }
                  }
                }
              }
            }
            //END FETCH LIST CATEGORY AND SUB-CATEGORY
            //START FETCH DEFAULT PACKAGE ID
            if (!empty($package))
              $sitegroup->package_id = $package_id;
            //END FETCH DEFAULT PACKAGE ID

            $sitegroup->profile_type = 0;

            $sitegroup->photo_id = 0;

            //START FETCH PRICE
            if (!empty($metaData)) {
              $field_id = $metaData->field_id;

              $valueTable = Engine_Api::_()->fields()->getTable('list_listing', 'values');
              $selectValueData = $valueTable->select()->where('item_id = ?', $listing_id)->where('field_id = ?', $field_id);
              $valueData = $valueTable->fetchRow($selectValueData);
              if (!empty($valueData)) {
                $sitegroup->price = $valueData->value;
              }
            }
            //END FETCH PRICE
            //START GET DATA FROM LISTING
            $sitegroup->creation_date = $listing->creation_date;
            $sitegroup->modified_date = $listing->modified_date;
            $sitegroup->approved = $listing->approved;
            $sitegroup->featured = $listing->featured;
            $sitegroup->sponsored = $listing->sponsored;

            $sitegroup->view_count = 1;
            if ($listing->view_count > 0) {
              $sitegroup->view_count = $listing->view_count;
            }

            $sitegroup->comment_count = $listing->comment_count;
            $sitegroup->like_count = $listing->like_count;
            $sitegroup->search = $listing->search;
            $sitegroup->closed = $listing->closed;
            $sitegroup->draft = $listing->draft;
            $sitegroup->offer = 0;

            if (!empty($listing->aprrove_date)) {
              $sitegroup->pending = 0;
              $sitegroup->aprrove_date = $listing->aprrove_date;
              $sitegroup->expiration_date = '2250-01-01 00:00:00';
            }

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
              $sitegroup->rating = round($listing->rating, 0);
            }

            $sitegroup->save();
            $listing->is_import = 1;
            $listing->save();
            $next_import_count++;
            //END GET DATA FROM LISTING
            //START CREATE NEW GROUP URL
            $group_url = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($listing->title))), '-');
            $sitegroup_table = Engine_Api::_()->getItemTable('sitegroup_group');
            $group = $sitegroup_table->fetchRow(array('group_url = ?' => $group_url));
            if (!empty($group)) {
              $sitegroup->group_url = $group_url . $sitegroup->group_id;
            } else {
              $sitegroup->group_url = $group_url;
            }
            //END CREATE NEW GROUP URL

            $sitegroup->save();

            //START PROFILE MAPS WORK
            Engine_Api::_()->getDbtable('profilemaps', 'sitegroup')->profileMapping($sitegroup);

//             //EXTRACTING CURRENT ADMIN SETTINGS FOR THIS VIEW GROUP.
//             $selectGroupAdmin = $groupAdminTable->select()
//                             ->setIntegrityCheck(false)
//                             ->from($groupAdminTableName)
//                             ->where('name = ?', 'sitegroup_index_view');
//             $groupAdminresult = $groupAdminTable->fetchRow($selectGroupAdmin);
//             //NOW INSERTING THE ROW IN GROUP TABLE
//             //MAKE NEW ENTRY FOR USER LAYOUT
//             $groupObject = $groupTable->createRow();
//             $groupObject->displayname = ( null !== ($name = $sitegroup->title) ? $name : 'Untitled' );
//             $groupObject->title = ( null !== ($name = $sitegroup->title) ? $name : 'Untitled' );
//             $groupObject->description = $sitegroup->body;
//             $groupObject->name = "sitegroup_index_view";
//             $groupObject->url = $groupAdminresult->url;
//             $groupObject->custom = $groupAdminresult->custom;
//             $groupObject->fragment = $groupAdminresult->fragment;
//             $groupObject->keywords = $groupAdminresult->keywords;
//             $groupObject->layout = $groupAdminresult->layout;
//             $groupObject->view_count = $groupAdminresult->view_count;
//             $groupObject->user_id = $sitegroup->owner_id;
//             $groupObject->group_id = $sitegroup->group_id;
//             $contentGroupId = $groupObject->save();
// 
//             //NOW FETCHING GROUP CONTENT DEFAULT SETTING INFORMATION FROM CORE CONTENT TABLE FOR THIS GROUP.
//             //NOW INSERTING DEFAULT GROUP CONTENT SETTINGS IN OUR CONTENT TABLE
// 						$layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0);
// 						if (!$layout) {
// 							Engine_Api::_()->getDbtable('content', 'sitegroup')->setContentDefault($contentGroupId);
// 						} else {
// 							Engine_Api::_()->getApi('layoutcore', 'sitegroup')->setContentDefaultLayout($contentGroupId);
// 						}
            //START FETCH TAG
            $listTags = $listing->tags()->getTagMaps();
            $tagString = '';

            foreach ($listTags as $tagmap) {

              if ($tagString != '')
                $tagString .= ', ';
              $tagString .= $tagmap->getTag()->getTitle();

              $owner = Engine_Api::_()->getItem('user', $listing->owner_id);
              $tags = preg_split('/[,]+/', $tagString);
              $tags = array_filter(array_map("trim", $tags));
              $sitegroup->tags()->setTagMaps($owner, $tags);
            }
            //END FETCH TAG
            //START FETCH LIKES
            $selectLike = $likeTable->select()
                    ->from($likeTableName, 'like_id')
                    ->where('resource_type = ?', 'list_listing')
                    ->where('resource_id = ?', $listing_id);
            $selectLikeDatas = $likeTable->fetchAll($selectLike);
            foreach ($selectLikeDatas as $selectLikeData) {
              $like = Engine_Api::_()->getItem('core_like', $selectLikeData->like_id);

              $newLikeEntry = $likeTable->createRow();
              $newLikeEntry->resource_type = 'sitegroup_group';
              $newLikeEntry->resource_id = $sitegroup->group_id;
              $newLikeEntry->poster_type = 'user';
              $newLikeEntry->poster_id = $like->poster_id;
              $newLikeEntry->creation_date = $like->creation_date;
              $newLikeEntry->save();
            }
            //END FETCH LIKES
            //START FETCH COMMENTS
            $selectLike = $commentTable->select()
                    ->from($commentTableName, 'comment_id')
                    ->where('resource_type = ?', 'list_listing')
                    ->where('resource_id = ?', $listing_id);
            $selectLikeDatas = $commentTable->fetchAll($selectLike);
            foreach ($selectLikeDatas as $selectLikeData) {
              $comment = Engine_Api::_()->getItem('core_comment', $selectLikeData->comment_id);

              $newLikeEntry = $commentTable->createRow();
              $newLikeEntry->resource_type = 'sitegroup_group';
              $newLikeEntry->resource_id = $sitegroup->group_id;
              $newLikeEntry->poster_type = 'user';
              $newLikeEntry->poster_id = $comment->poster_id;
              $newLikeEntry->body = $comment->body;
              $newLikeEntry->creation_date = $comment->creation_date;
              $newLikeEntry->like_count = $comment->like_count;
              $newLikeEntry->save();
            }
            //END FETCH COMMENTS
            //START FETCH PRIVACY
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

            foreach ($roles as $role) {
              if ($auth->isAllowed($listing, $role, 'view')) {
                $values['auth_view'] = $role;
              }
            }

            foreach ($roles as $role) {
              if ($auth->isAllowed($listing, $role, 'comment')) {
                $values['auth_comment'] = $role;
              }
            }

            foreach ($roles as $role) {
              if ($auth->isAllowed($listing, $role, 'photo')) {
                $values['auth_spcreate'] = $role;
              }
            }

            $viewMax = array_search($values['auth_view'], $roles);
            $commentMax = array_search($values['auth_comment'], $roles);
            $photoMax = array_search($values['auth_spcreate'], $roles);

            foreach ($roles as $i => $role) {
              $auth->setAllowed($sitegroup, $role, 'view', ($i <= $viewMax));
              $auth->setAllowed($sitegroup, $role, 'comment', ($i <= $commentMax));
              $auth->setAllowed($sitegroup, $role, 'spcreate', ($i <= $photoMax));
            }
            //END FETCH PRIVACY
            //START FETCH DISCUSSION DATA
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion')) {

              foreach ($roles as $i => $role) {
                $auth->setAllowed($sitegroup, $role, 'sdicreate', ($i <= $photoMax));
              }

              $topicSelect = $topicTable->select()
                      ->from($topicTableName)
                      ->where('listing_id = ?', $listing_id);
              $topicSelectDatas = $topicTable->fetchAll($topicSelect);
              if (!empty($topicSelectDatas)) {
                $topicSelectDatas = $topicSelectDatas->toArray();

                foreach ($topicSelectDatas as $topicSelectData) {
                  $groupTopic = $groupTopicTable->createRow();
                  $groupTopic->group_id = $sitegroup->group_id;
                  $groupTopic->user_id = $topicSelectData['user_id'];
                  $groupTopic->title = $topicSelectData['title'];
                  $groupTopic->creation_date = $topicSelectData['creation_date'];
                  $groupTopic->modified_date = $topicSelectData['modified_date'];
                  $groupTopic->sticky = $topicSelectData['sticky'];
                  $groupTopic->closed = $topicSelectData['closed'];
                  $groupTopic->post_count = $topicSelectData['post_count'];
                  $groupTopic->view_count = $topicSelectData['view_count'];
                  $groupTopic->lastpost_id = $topicSelectData['lastpost_id'];
                  $groupTopic->lastposter_id = $topicSelectData['lastposter_id'];
                  $groupTopic->save();

                  //START FETCH TOPIC POST'S
                  $postSelect = $postTable->select()
                          ->from($postTableName)
                          ->where('topic_id = ?', $topicSelectData['topic_id'])
                          ->where('listing_id = ?', $listing_id);
                  $postSelectDatas = $postTable->fetchAll($postSelect);
                  if (!empty($postSelectDatas)) {
                    $postSelectDatas = $postSelectDatas->toArray();

                    foreach ($postSelectDatas as $postSelectData) {
                      $groupPost = $groupPostTable->createRow();
                      $groupPost->topic_id = $groupTopic->topic_id;
                      $groupPost->group_id = $sitegroup->group_id;
                      $groupPost->user_id = $postSelectData['user_id'];
                      $groupPost->body = $postSelectData['body'];
                      $groupPost->creation_date = $postSelectData['creation_date'];
                      $groupPost->modified_date = $postSelectData['modified_date'];
                      $groupPost->save();
                    }
                  }
                  //END FETCH TOPIC POST'S
                  //START FETCH TOPIC WATCH
                  $topicWatchData = $topicWatchesTable->fetchRow(array('resource_id = ?' => $listing_id, 'topic_id = ?' => $topicSelectData['topic_id'], 'user_id = ?' => $topicSelectData['user_id']));
                  if (!empty($topicWatchData))
                    $watch = $topicWatchData->watch;

                  $groupTopicWatchesTable->insert(array(
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
            //START FETCH REVIEW DATA
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
              $reviewTableSelect = $reviewTable->select()
                      ->from($reviewTableName, array('MAX(review_id) as review_id'))
                      ->where('listing_id = ?', $listing_id)
                      ->where('owner_id != ?', $listing->owner_id)
                      ->group('owner_id')
                      ->order('review_id ASC');
              $reviewSelectDatas = $reviewTable->fetchAll($reviewTableSelect);
              if (!empty($reviewSelectDatas)) {
                $reviewSelectDatas = $reviewSelectDatas->toArray();
                foreach ($reviewSelectDatas as $reviewSelectData) {
                  $review = Engine_Api::_()->getItem('list_review', $reviewSelectData['review_id']);

                  $groupReview = $groupReviewTable->createRow();
                  $groupReview->group_id = $sitegroup->group_id;
                  $groupReview->owner_id = $review->owner_id;
                  $groupReview->title = $review->title;
                  $groupReview->body = $review->body;
                  $groupReview->view_count = 1;
                  $groupReview->recommend = 1;
                  $groupReview->creation_date = $review->creation_date;
                  $groupReview->modified_date = $review->modified_date;
                  $groupReview->save();

                  $reviewRating = $reviewRatingTable->createRow();
                  $reviewRating->review_id = $groupReview->review_id;
                  $reviewRating->category_id = $sitegroup->category_id;
                  $reviewRating->group_id = $groupReview->group_id;
                  $reviewRating->reviewcat_id = 0;
                  $reviewRating->rating = round($listing->rating, 0);
                  $reviewRating->save();
                }
              }
            }
            //END FETCH REVIEW DATA
            //START INSERT SOME DEFAULT DATA
            $row = $manageadminsTable->createRow();
            $row->user_id = $sitegroup->owner_id;
            $row->group_id = $sitegroup->group_id;
            $row->save();

            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            $privacyMax = array_search('everyone', $roles);
            foreach ($roles as $i => $role) {
              $auth->setAllowed($sitegroup, $role, 'print', ($i <= $privacyMax));
              $auth->setAllowed($sitegroup, $role, 'tfriend', ($i <= $privacyMax));
              $auth->setAllowed($sitegroup, $role, 'overview', ($i <= $privacyMax));
              $auth->setAllowed($sitegroup, $role, 'map', ($i <= $privacyMax));
              $auth->setAllowed($sitegroup, $role, 'insight', ($i <= $privacyMax));
              $auth->setAllowed($sitegroup, $role, 'layout', ($i <= $privacyMax));
              $auth->setAllowed($sitegroup, $role, 'contact', ($i <= $privacyMax));
              $auth->setAllowed($sitegroup, $role, 'form', ($i <= $privacyMax));
              $auth->setAllowed($sitegroup, $role, 'offer', ($i <= $privacyMax));
              $auth->setAllowed($sitegroup, $role, 'invite', ($i <= $privacyMax));
            }

            $locationData = $listLocationTable->fetchRow(array('listing_id = ?' => $listing_id));
            if (!empty($locationData)) {
              $sitegroup->location = $locationData->location;
              $sitegroup->save();

              $groupLocation = $groupLocationTable->createRow();
              $groupLocation->group_id = $sitegroup->group_id;
              $groupLocation->location = $sitegroup->location;
              $groupLocation->latitude = $locationData->latitude;
              $groupLocation->longitude = $locationData->longitude;
              $groupLocation->formatted_address = $locationData->formatted_address;
              $groupLocation->country = $locationData->country;
              $groupLocation->state = $locationData->state;
              $groupLocation->zipcode = $locationData->zipcode;
              $groupLocation->city = $locationData->city;
              $groupLocation->address = $locationData->address;
              $groupLocation->zoom = $locationData->zoom;
              $groupLocation->save();
            }
            //END INSERT SOME DEFAULT DATA
            //START FETCH PHOTO DATA
            $selectListPhoto = $listPhotoTable->select()
                    ->from($listPhotoTable->info('name'))
                    ->where('listing_id = ?', $listing_id);
            $listPhotoDatas = $listPhotoTable->fetchAll($selectListPhoto);

            $sitgroup = Engine_Api::_()->getItem('sitegroup_group', $sitegroup->group_id);

            if (!empty($listPhotoDatas)) {

              $listPhotoDatas = $listPhotoDatas->toArray();

              if (empty($listing->photo_id)) {
                foreach ($listPhotoDatas as $listPhotoData) {
                  $listing->photo_id = $listPhotoData['photo_id'];
                  break;
                }
              }

              if (!empty($listing->photo_id)) {
                $listPhotoData = $listPhotoTable->fetchRow(array('file_id = ?' => $listing->photo_id));
                if (!empty($listPhotoData)) {
                  $storageData = $storageTable->fetchRow(array('file_id = ?' => $listPhotoData->file_id));

                  if (!empty($storageData)) {

                    $sitgroup->setPhoto($storageData->storage_path);

                    $album_id = $albumTable->update(array('photo_id' => $sitgroup->photo_id, 'owner_id' => $sitgroup->owner_id), array('group_id = ?' => $sitgroup->group_id));

                    $groupProfilePhoto = Engine_Api::_()->getDbTable('photos', 'sitegroup')->fetchRow(array('file_id = ?' => $sitgroup->photo_id));
                    if (!empty($groupProfilePhoto)) {
                      $groupProfilePhotoId = $groupProfilePhoto->photo_id;
                    } else {
                      $groupProfilePhotoId = $sitgroup->photo_id;
                    }

                    //START FETCH LIKES
                    $selectLike = $likeTable->select()
                            ->from($likeTableName, 'like_id')
                            ->where('resource_type = ?', 'list_photo')
                            ->where('resource_id = ?', $listing->photo_id);
                    $selectLikeDatas = $likeTable->fetchAll($selectLike);
                    foreach ($selectLikeDatas as $selectLikeData) {
                      $like = Engine_Api::_()->getItem('core_like', $selectLikeData->like_id);

                      $newLikeEntry = $likeTable->createRow();
                      $newLikeEntry->resource_type = 'sitegroup_photo';
                      $newLikeEntry->resource_id = $groupProfilePhotoId;
                      $newLikeEntry->poster_type = 'user';
                      $newLikeEntry->poster_id = $like->poster_id;
                      $newLikeEntry->creation_date = $like->creation_date;
                      $newLikeEntry->save();
                    }
                    //END FETCH LIKES
                    //START FETCH COMMENTS
                    $selectLike = $commentTable->select()
                            ->from($commentTableName, 'comment_id')
                            ->where('resource_type = ?', 'list_photo')
                            ->where('resource_id = ?', $listing->photo_id);
                    $selectLikeDatas = $commentTable->fetchAll($selectLike);
                    foreach ($selectLikeDatas as $selectLikeData) {
                      $comment = Engine_Api::_()->getItem('core_comment', $selectLikeData->comment_id);

                      $newLikeEntry = $commentTable->createRow();
                      $newLikeEntry->resource_type = 'sitegroup_photo';
                      $newLikeEntry->resource_id = $groupProfilePhotoId;
                      $newLikeEntry->poster_type = 'user';
                      $newLikeEntry->poster_id = $comment->poster_id;
                      $newLikeEntry->body = $comment->body;
                      $newLikeEntry->creation_date = $comment->creation_date;
                      $newLikeEntry->like_count = $comment->like_count;
                      $newLikeEntry->save();
                    }
                    //END FETCH COMMENTS
                    //START FETCH TAGGER DETAIL
                    $tagmapsTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
                    $tagmapsTableName = $tagmapsTable->info('name');
                    $selectTagmaps = $tagmapsTable->select()
                            ->from($tagmapsTableName, 'tagmap_id')
                            ->where('resource_type = ?', 'list_photo')
                            ->where('resource_id = ?', $listing->photo_id);
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
                    }
                    //END FETCH TAGGER DETAIL
                  }
                }

                $fetchDefaultAlbum = $albumTable->fetchRow(array('group_id = ?' => $sitegroup->group_id, 'default_value = ?' => 1));
                if (!empty($fetchDefaultAlbum)) {

                  $order = 999;
                  foreach ($listPhotoDatas as $listPhotoData) {

                    if ($listPhotoData['photo_id'] != $listing->photo_id) {
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
                          $selectLike = $likeTable->select()
                                  ->from($likeTableName, 'like_id')
                                  ->where('resource_type = ?', 'list_photo')
                                  ->where('resource_id = ?', $listPhotoData['photo_id']);
                          $selectLikeDatas = $likeTable->fetchAll($selectLike);
                          foreach ($selectLikeDatas as $selectLikeData) {
                            $like = Engine_Api::_()->getItem('core_like', $selectLikeData->like_id);

                            $newLikeEntry = $likeTable->createRow();
                            $newLikeEntry->resource_type = 'sitegroup_photo';
                            $newLikeEntry->resource_id = $groupPhoto->photo_id;
                            $newLikeEntry->poster_type = 'user';
                            $newLikeEntry->poster_id = $like->poster_id;
                            $newLikeEntry->creation_date = $like->creation_date;
                            $newLikeEntry->save();
                          }
                          //END FETCH LIKES
                          //START FETCH COMMENTS
                          $selectLike = $commentTable->select()
                                  ->from($commentTableName, 'comment_id')
                                  ->where('resource_type = ?', 'list_photo')
                                  ->where('resource_id = ?', $listPhotoData['photo_id']);
                          $selectLikeDatas = $commentTable->fetchAll($selectLike);
                          foreach ($selectLikeDatas as $selectLikeData) {
                            $comment = Engine_Api::_()->getItem('core_comment', $selectLikeData->comment_id);

                            $newLikeEntry = $commentTable->createRow();
                            $newLikeEntry->resource_type = 'sitegroup_photo';
                            $newLikeEntry->resource_id = $groupPhoto->photo_id;
                            $newLikeEntry->poster_type = 'user';
                            $newLikeEntry->poster_id = $comment->poster_id;
                            $newLikeEntry->body = $comment->body;
                            $newLikeEntry->creation_date = $comment->creation_date;
                            $newLikeEntry->like_count = $comment->like_count;
                            $newLikeEntry->save();
                          }
                          //END FETCH COMMENTS
                          //START FETCH TAGGER DETAIL
                          $selectTagmaps = $tagmapsTable->select()
                                  ->from($tagmapsTableName, 'tagmap_id')
                                  ->where('resource_type = ?', 'list_photo')
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
            //START FETCH engine4_list_writes DATA
            $writeData = $writeTable->fetchRow(array('listing_id = ?' => $listing_id));
            if (!empty($writeData)) {
              $write = $groupWriteTable->createRow();
              $write->group_id = $sitegroup->group_id;
              $write->text = $writeData->text;
              $write->save();
            }
            //END FETCH engine4_list_writes DATA
            //START FETCH VIDEO DATA
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('video') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo')) {

              $selectListVideos = $listVideoTable->select()
                      ->from($listVideoTableName, 'video_id')
                      ->where('listing_id = ?', $listing_id)
                      ->group('video_id');
              $listVideoDatas = $listVideoTable->fetchAll($selectListVideos);
              foreach ($listVideoDatas as $listVideoData) {
                $listVideo = Engine_Api::_()->getItem('video', $listVideoData->video_id);
                if (!empty($listVideo)) {
                  $db = $groupVideoTable->getAdapter();
                  $db->beginTransaction();

                  try {
                    $groupVideo = $groupVideoTable->createRow();
                    $groupVideo->group_id = $sitegroup->group_id;
                    $groupVideo->title = $listVideo->title;
                    $groupVideo->description = $listVideo->description;
                    $groupVideo->search = $listVideo->search;
                    $groupVideo->owner_id = $listVideo->owner_id;
                    $groupVideo->creation_date = $listVideo->creation_date;
                    $groupVideo->modified_date = $listVideo->modified_date;

                    $groupVideo->view_count = 1;
                    if ($listVideo->view_count > 0) {
                      $groupVideo->view_count = $listVideo->view_count;
                    }

                    $groupVideo->comment_count = $listVideo->comment_count;
                    $groupVideo->type = $listVideo->type;
                    $groupVideo->code = $listVideo->code;
                    $groupVideo->rating = $listVideo->rating;
                    $groupVideo->status = $listVideo->status;
                    $groupVideo->featured = 0;
                    $groupVideo->file_id = 0;
                    $groupVideo->duration = $listVideo->duration;
                    $groupVideo->save();
                    $db->commit();
                  } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                  }

                  //START VIDEO THUMB WORK
                  if (!empty($groupVideo->code) && !empty($groupVideo->type) && !empty($listVideo->photo_id)) {
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
                              'parent_type' => 'sitegroupvideo_video',
                              'parent_id' => $groupVideo->video_id
                                  ));

                          //REMOVE TEMP FILE
                          @unlink($thumb_file);
                          @unlink($tmp_file);
                        } catch (Exception $e) {
                          
                        }

                        $groupVideo->photo_id = $thumbFileRow->file_id;
                        $groupVideo->save();
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
                    $groupVideo->tags()->setTagMaps($owner, $tags);
                  }
                  //END FETCH TAG
                  //START FETCH LIKES
                  $selectLike = $likeTable->select()
                          ->from($likeTableName, 'like_id')
                          ->where('resource_type = ?', 'video')
                          ->where('resource_id = ?', $listVideoData->video_id);
                  $selectLikeDatas = $likeTable->fetchAll($selectLike);
                  foreach ($selectLikeDatas as $selectLikeData) {
                    $like = Engine_Api::_()->getItem('core_like', $selectLikeData->like_id);

                    $newLikeEntry = $likeTable->createRow();
                    $newLikeEntry->resource_type = 'sitegroupvideo_video';
                    $newLikeEntry->resource_id = $groupVideo->video_id;
                    $newLikeEntry->poster_type = 'user';
                    $newLikeEntry->poster_id = $like->poster_id;
                    $newLikeEntry->creation_date = $like->creation_date;
                    $newLikeEntry->save();
                  }
                  //END FETCH LIKES
                  //START FETCH COMMENTS
                  $selectLike = $commentTable->select()
                          ->from($commentTableName, 'comment_id')
                          ->where('resource_type = ?', 'video')
                          ->where('resource_id = ?', $listVideoData->video_id);
                  $selectLikeDatas = $commentTable->fetchAll($selectLike);
                  foreach ($selectLikeDatas as $selectLikeData) {
                    $comment = Engine_Api::_()->getItem('core_comment', $selectLikeData->comment_id);

                    $newLikeEntry = $commentTable->createRow();
                    $newLikeEntry->resource_type = 'sitegroupvideo_video';
                    $newLikeEntry->resource_id = $groupVideo->video_id;
                    $newLikeEntry->poster_type = 'user';
                    $newLikeEntry->poster_id = $comment->poster_id;
                    $newLikeEntry->body = $comment->body;
                    $newLikeEntry->creation_date = $comment->creation_date;
                    $newLikeEntry->like_count = $comment->like_count;
                    $newLikeEntry->save();
                  }
                  //END FETCH COMMENTS
                  //START UPDATE TOTAL LIKES IN GROUP-VIDEO TABLE
                  $selectLikeCount = $likeTable->select()
                          ->from($likeTableName, array('COUNT(*) AS like_count'))
                          ->where('resource_type = ?', 'sitegroupvideo_video')
                          ->where('resource_id = ?', $groupVideo->video_id);
                  $selectLikeCounts = $likeTable->fetchAll($selectLikeCount);
                  if (!empty($selectLikeCounts)) {
                    $selectLikeCounts = $selectLikeCounts->toArray();
                    $groupVideo->like_count = $selectLikeCounts[0]['like_count'];
                    $groupVideo->save();
                  }
                  //END UPDATE TOTAL LIKES IN GROUP-VIDEO TABLE
                  //START FETCH RATTING DATA
                  $selectVideoRating = $listVideoRating->select()
                          ->from($listVideoRatingName)
                          ->where('video_id = ?', $listVideoData->video_id);

                  $listVideoRatingDatas = $listVideoRating->fetchAll($selectVideoRating);
                  if (!empty($listVideoRatingDatas)) {
                    $listVideoRatingDatas = $listVideoRatingDatas->toArray();
                  }

                  foreach ($listVideoRatingDatas as $listVideoRatingData) {

                    $groupVideoRatingTable->insert(array(
                        'video_id' => $groupVideo->video_id,
                        'user_id' => $listVideoRatingData['user_id'],
                        'group_id' => $groupVideo->group_id,
                        'rating' => $listVideoRatingData['rating']
                    ));
                  }
                  //END FETCH RATTING DATA
                }
              }
              //END FETCH VIDEO DATA
            }
          }

          $this->view->assigned_previous_id = $listing_id;

          //CREATE LOG ENTRY IN LOG FILE
          if (file_exists(APPLICATION_PATH . '/temporary/log/ListingToGroupImport.log')) {
            $myFile = APPLICATION_PATH . '/temporary/log/ListingToGroupImport.log';
            $error = Zend_Registry::get('Zend_Translate')->_("can't open file");
            $fh = fopen($myFile, 'a') or die($error);
            $current_time = date('D, d M Y H:i:s T');
            $group_id = $sitegroup->group_id;
            $group_title = $sitegroup->title;
            $stringData = $this->view->translate('Listing with ID ') . $listing_id . $this->view->translate(' is successfully imported into a Group with ID ') . $group_id . $this->view->translate(' at ') . $current_time . $this->view->translate(". Title of that Group is '") . $group_title . "'.\n\n";
            fwrite($fh, $stringData);
            fclose($fh);
          }

          if ($next_import_count >= 100) {
            $this->_redirect("admin/sitegroup/importlisting/index?start_import=1");
          }
        }
      }
    }
  }

  //ACTION FOR IMPORTING DATA FROM CSV FILE
  public function importAction() {

    //INCREASE THE MEMORY ALLOCATION SIZE AND INFINITE SET TIME OUT
    ini_set('memory_limit', '2048M');
    set_time_limit(0);

    $this->_helper->layout->setLayout('admin-simple');

    //MAKE FORM
    $this->view->form = $form = new Sitegroup_Form_Admin_Import_Import();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //MAKE SURE THAT FILE EXTENSION SHOULD NOT DIFFER FROM ALLOWED TYPE
      $ext = str_replace(".", "", strrchr($_FILES['filename']['name'], "."));
      if (!in_array($ext, array('csv', 'CSV'))) {
        $error = $this->view->translate("Invalid file extension. Only 'csv' extension is allowed.");
        $error = Zend_Registry::get('Zend_Translate')->_($error);

        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error);
        return;
      }

      //START READING DATA FROM CSV FILE
      $fname = $_FILES['filename']['tmp_name'];
      $fp = fopen($fname, "r");

      if (!$fp) {
        echo "$fname File opening error";
        exit;
      }

      $formData = array();
      $formData = $form->getValues();

      if ($formData['import_seperate'] == 1) {
        while ($buffer = fgets($fp, 9999)) {
          $explode_array[] = explode('|', $buffer);
        }
      } else {
        while ($buffer = fgets($fp, 9999)) {
          $explode_array[] = explode(',', $buffer);
        }
      }
      //END READING DATA FROM CSV FILE

      $import_count = 0;
      foreach ($explode_array as $explode_data) {

        //GET GROUP DETAILS FROM DATA ARRAY
        $db = Engine_Db_Table::getDefaultAdapter();
        $columns = $db->query("SHOW COLUMNS FROM engine4_sitegroup_imports")->fetchAll();
        $values = array();
        $values['title'] = trim($explode_data[0]);
        $values['group_url'] = trim($explode_data[1]);
        $values['category'] = trim($explode_data[2]);
        $values['sub_category'] = trim($explode_data[3]);
        $values['subsub_category'] = trim($explode_data[4]);
        $values['body'] = trim($explode_data[5]);
        $values['overview'] = trim($explode_data[6]);
        $values['tags'] = trim($explode_data[7]);
        $values['location'] = trim($explode_data[8]);
        $values['price'] = trim($explode_data[9]);
        $values['email'] = trim($explode_data[10]);
        $values['website'] = trim($explode_data[11]);
        $values['phone'] = trim($explode_data[12]);
        $values['userclaim'] = trim($explode_data[13]);
        $values['img_name'] = trim($explode_data[14]);

        $i = 1;
        $custom_field = '';
        foreach ($columns as $column) {
          $custom_field .= $column['Field'] . ',';
        }
        $custom_field = explode('userclaim', $custom_field);
        $custom_field = ltrim($custom_field[1], ',');
        $custom_field = rtrim($custom_field, ',');
        $custom_field = explode(',', $custom_field);
        $required_field = 1;

        $count_value_checkbox = 0;
        $count_value_selectbox = 0;
        $count_value_etnicity = 0;
        $count_value_lookingfor = 0;
        $count_value_interestedIn = 0;
        if (isset($explode_data[15])) {
          foreach ($custom_field as $field) {
            if (empty($field))
              continue;

            $column_name = $field;
            $explode_column = explode('_', $column_name);
            $columnIsRequired='';
            $fieldType ='';
            if(isset($explode_column[2])) {
            $columnIsRequired = $db->select()->from('engine4_sitegroup_group_fields_meta', 'required')->where('field_id = ?', $explode_column[2])->query()->fetchColumn();
            }
            if(isset($explode_column[1])) {
            $fieldType = $db->select()->from('engine4_sitegroup_group_fields_meta', 'type')->where('field_id = ?', $explode_column[1])->query()->fetchColumn();
            }
            if ($fieldType == 'multi_checkbox' && !empty($columnIsRequired)) {
              if (($explode_data[14 + $i] != ' ' && !empty($explode_data[14 + $i])) || ($explode_data[14 + $i] == 0)) {
                $count_value_checkbox++;
              }
              if ($count_value_checkbox == 0) {
                $required_field = 0;
                break;
              }
            } elseif ($fieldType == 'multiselect' && !empty($columnIsRequired)) {
              if (($explode_data[14 + $i] != ' ' && !empty($explode_data[14 + $i])) || ($explode_data[14 + $i] == 0)) {
                $count_value_selectbox++;
              }
              if ($count_value_selectbox == 0) {
                $required_field = 0;
                break;
              }
            } elseif ($fieldType == 'looking_for' && !empty($columnIsRequired)) {
              if (($explode_data[14 + $i] != ' ' && !empty($explode_data[14 + $i])) || ($explode_data[14 + $i] == 0)) {
                $count_value_lookingfor++;
              }
              if ($count_value_lookingfor == 0) {
                $required_field = 0;
                break;
              }
            } elseif ($fieldType == 'ethnicity' && !empty($columnIsRequired)) {
              if (($explode_data[14 + $i] != ' ' && !empty($explode_data[14 + $i])) || ($explode_data[14 + $i] == 0)) {
                $count_value_etnicity++;
              }
              if ($count_value_etnicity == 0) {
                $required_field = 0;
                break;
              }
            } elseif ($fieldType == 'partner_gender' && !empty($columnIsRequired)) {
              if (($explode_data[14 + $i] != ' ' && !empty($explode_data[14 + $i])) || ($explode_data[14 + $i] == 0)) {
                $count_value_interestedIn++;
              }
              if ($count_value_interestedIn == 0) {
                $required_field = 0;
                break;
              }
            } elseif (!empty($columnIsRequired) && ($explode_data[14 + $i] == ' ' || empty($explode_data[14 + $i]))) {
              $required_field = 0;
              break;
            }
            $values[$column_name] = trim($explode_data[14 + $i]);
            $i++;
          }
        }

        //IF GROUP TITLE AND CATEGORY IS EMPTY THEN CONTINUE;
        if (empty($values['title']) || empty($values['group_url']) || empty($values['category']) || empty($values['body'])) {
          continue;
        }

        $db = Engine_Api::_()->getDbtable('imports', 'sitegroup')->getAdapter();
        $db->beginTransaction();

        try {
          $import = Engine_Api::_()->getDbtable('imports', 'sitegroup')->createRow();
          $import->setFromArray($values);
          $import->save();

          //COMMIT
          $db->commit();

          if (empty($import_count)) {
            $first_import_id = $last_import_id = $import->import_id;

            //SAVE DATA IN `engine4_sitegroup_importfiles` TABLE
            $db = Engine_Api::_()->getDbtable('importfiles', 'sitegroup')->getAdapter();
            $db->beginTransaction();

            try {

              //FETCH PRIVACY
              if (empty($formData['auth_view'])) {
                $formData['auth_view'] = "everyone";
              }

              if (empty($formData['auth_comment'])) {
                $formData['auth_comment'] = "everyone";
              }

              //SAVE OTHER DATA IN engine4_sitegroup_importfiles TABLE
              $importFile = Engine_Api::_()->getDbtable('importfiles', 'sitegroup')->createRow();
              $importFile->filename = $_FILES['filename']['name'];
              $importFile->status = 'Pending';
              $importFile->first_import_id = $first_import_id;
              $importFile->last_import_id = $last_import_id;
              $importFile->current_import_id = $first_import_id;
              $importFile->first_group_id = 0;
              $importFile->last_group_id = 0;
              $importFile->view_privacy = $formData['auth_view'];
              $importFile->comment_privacy = $formData['auth_comment'];
              $importFile->save();

              //COMMIT
              $db->commit();
            } catch (Exception $e) {
              $db->rollBack();
              throw $e;
            }
          } else {

            //UPDATE LAST IMPORT ID
            $last_import_id = $import->import_id;
            $importFile->last_import_id = $last_import_id;
            $importFile->save();
          }

          $import_count++;
        } catch (Exception $e) {
          $db->rollBack();
          throw $e;
        }
      }

      //CLOSE THE SMOOTHBOX
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRedirect' => $this->_helper->url->url(array('module' => 'sitegroup', 'controller' => 'admin-importlisting', 'action' => 'manage')),
          'parentRedirectTime' => '15',
          'format' => 'smoothbox',
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('CSV file has been imported succesfully !'))
      ));
    }
  }

  //ACTION FOR IMPORTING DATA FROM CSV FILE
  public function dataImportAction() {

    //INCREASE THE MEMORY ALLOCATION SIZE AND INFINITE SET TIME OUT
    ini_set('memory_limit', '2048M');
    set_time_limit(0);

    $this->_helper->layout->setLayout('admin-simple');
    $this->view->importfile_id = $importfile_id = $this->_getParam('importfile_id');
    $current_import = $this->_getParam('current_import');

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //RETURN IF importfile_id IS EMPTY
    if (empty($importfile_id)) {
      return;
    }

    //GET IMPORT FILE OBJECT
    $importFile = Engine_Api::_()->getItem('sitegroup_importfile', $importfile_id);
    if (empty($importFile)) {
      return;
    }

    //CHECK IF IMPORT WORK IS ALREADY IN RUNNING STATUS FOR SOME FILE
    $tableImportFile = Engine_Api::_()->getDbTable('importfiles', 'sitegroup');
    $importFileStatusData = $tableImportFile->fetchRow(array('status = ?' => 'Running'));
    if (!empty($importFileStatusData) && empty($current_import)) {
      return;
    }

    //UPDATE THE STATUS
    $importFile->status = 'Running';
    $importFile->save();

    $first_import_id = $importFile->first_import_id;
    $last_import_id = $importFile->last_import_id;

    $current_import_id = $importFile->current_import_id;
    $return_current_import_id = $this->_getParam('current_import_id');
    if (!empty($return_current_import_id)) {
      $current_import_id = $this->_getParam('current_import_id');
    }

    //MAKE QUERY
    $tableImport = Engine_Api::_()->getDbtable('imports', 'sitegroup');

    $sqlStr = "import_id BETWEEN " . "'" . $current_import_id . "'" . " AND " . "'" . $last_import_id . "'" . "";

    $select = $tableImport->select()
            ->from($tableImport->info('name'), array('import_id'))
            ->where($sqlStr);
    $importDatas = $select->query()->fetchAll();

    if (empty($importDatas)) {
      return;
    }

    //START CODE FOR CREATING THE ListingToGroupImport.log FILE
    if (!file_exists(APPLICATION_PATH . '/temporary/log/CSVToGroupImport.log')) {
      $log = new Zend_Log();
      try {
        $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/CSVToGroupImport.log'));
      } catch (Exception $e) {
        //CHECK DIRECTORY
        if (!@is_dir(APPLICATION_PATH . '/temporary/log') &&
                @mkdir(APPLICATION_PATH . '/temporary/log', 0777, true)) {
          $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/CSVToGroupImport.log'));
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

    //GIVE WRITE PERMISSION TO LOG FILE IF EXIST
    if (file_exists(APPLICATION_PATH . '/temporary/log/CSVToGroupImport.log')) {
      @chmod(APPLICATION_PATH . '/temporary/log/CSVToGroupImport.log', 0777);
    }
    //END CODE FOR CREATING THE CSVToGroupImport.log FILE
    //START COLLECTING COMMON DATAS
    $package_id = Engine_Api::_()->getItemtable('sitegroup_package')->fetchRow(array('defaultpackage = ?' => 1))->package_id;
    $package = Engine_Api::_()->getItemTable('sitegroup_package')->fetchRow(array('package_id = ?' => $package_id));
    $table = Engine_Api::_()->getItemTable('sitegroup_group');
    $groupCategoryTable = Engine_Api::_()->getDbtable('categories', 'sitegroup');
    $groupAdminTable = Engine_Api::_()->getDbtable('pages', 'core');
    $groupAdminTableName = $groupAdminTable->info('name');
    $manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup');
    $groupTable = Engine_Api::_()->getDbtable('contentgroups', 'sitegroup');
    $albumTable = Engine_Api::_()->getDbtable('albums', 'sitegroup');
    //END COLLECTING COMMON DATAS

    $import_count = 0;

    //START THE IMPORT WORK
    foreach ($importDatas as $importData) {

      //GET IMPORT FILE OBJECT
      $importFile = Engine_Api::_()->getItem('sitegroup_importfile', $importfile_id);

      //BREAK IF STATUS IS STOP
      if ($importFile->status == 'Stopped') {
        break;
      }

      $import_id = $importData['import_id'];
      if (empty($import_id)) {
        continue;
      }

      $import = Engine_Api::_()->getItem('sitegroup_import', $import_id);
      if (empty($import)) {
        continue;
      }

      //GET GROUP DETAILS FROM DATA ARRAY
      $values = array();
      $values['title'] = $import->title;
      $group_url = $import->group_url;
      $group_category = $import->category;
      $group_subcategory = $import->sub_category;
      $sitegroup_subsubcategory = $import->subsub_category;
      $values['body'] = $import->body;
      $values['price'] = $import->price;
      $values['location'] = $import->location;
      $values['overview'] = $import->overview;
      $group_tags = $import->tags;
      $values['email'] = $import->email;
      $values['website'] = $import->website;
      $values['phone'] = $import->phone;
      $values['userclaim'] = $import->userclaim;
      $values['owner_type'] = $viewer->getType();
      $values['owner_id'] = $viewer->getIdentity();
      $values['package_id'] = $package_id;

      //IF GROUP TITLE AND CATEGORY IS EMPTY THEN CONTINUE;
      if (empty($values['title']) || empty($group_category)) {
        continue;
      }

      $db = $table->getAdapter();
      $db->beginTransaction();

      try {

        $sitegroup = $table->createRow();
        $sitegroup->setFromArray($values);

        $sitegroup->pending = 0;
        $sitegroup->approved = 1;
        $sitegroup->aprrove_date = date('Y-m-d H:i:s');

        if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
          $expirationDate = $package->getExpirationDate();
          if (!empty($expirationDate))
            $sitegroup->expiration_date = date('Y-m-d H:i:s', $expirationDate);
          else
            $sitegroup->expiration_date = '2250-01-01 00:00:00';
        }
        else {
          $sitegroup->expiration_date = '2250-01-01 00:00:00';
        }

        $sitegroup->view_count = 1;
        $sitegroup->save();
        $group_id = $sitegroup->group_id;

        $importFile->current_import_id = $import->import_id;
        $importFile->last_group_id = $group_id;
        $importFile->save();

        if (empty($importFile->first_group_id)) {
          $importFile->first_group_id = $group_id;
          $importFile->save();
        }
        $import_count++;

        //START CREATE NEW GROUP URL
        if (empty($group_url)) {
          $group_url = $values['title'];
        }

        $group_url = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($group_url))), '-');

        $group = $table->fetchRow(array('group_url = ?' => $group_url));
        if (!empty($group)) {
          $sitegroup->group_url = $group_url . $sitegroup->group_id;
        } else {
          $sitegroup->group_url = $group_url;
        }

        $sitegroup->group_url = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($sitegroup->group_url))), '-');
        //END CREATE NEW GROUP URL
        //START CATEGORY WORK
        $groupCategory = $groupCategoryTable->fetchRow(array('category_name = ?' => $group_category, 'cat_dependency = ?' => 0));
        if (!empty($groupCategory)) {
          $sitegroup->category_id = $groupCategory->category_id;

          $groupSubcategory = $groupCategoryTable->fetchRow(array('category_name = ?' => $group_subcategory, 'cat_dependency = ?' => $sitegroup->category_id));

          if (!empty($groupSubcategory)) {
            $sitegroup->subcategory_id = $groupSubcategory->category_id;
            $sitegroupSubsubcategory = $groupCategoryTable->fetchRow(array('category_name = ?' => $sitegroup_subsubcategory, 'cat_dependency = ?' => $groupSubcategory->category_id));
            if (!empty($sitegroupSubsubcategory)) {
              $sitegroup->subsubcategory_id = $sitegroupSubsubcategory->category_id;
            }
          }

          //START PROFILE MAPS WORK
          Engine_Api::_()->getDbtable('profilemaps', 'sitegroup')->profileMapping($sitegroup);
        }
        //END CATEGORY WORK

        $sitegroup->save();

        //SAVE TAGS
        $tags = preg_split('/[#]+/', $group_tags);
        $tags = array_filter(array_map("trim", $tags));
        $sitegroup->tags()->addTagMaps($viewer, $tags);

        //START PROFILE IMAGE IMPORTING WORK
        $import_image = 'sitegroup_importfiles_' . $importfile_id;
        $archiveFilename = APPLICATION_PATH . "/public/$import_image" . DIRECTORY_SEPARATOR . $importFile->photo_filename;

        if (!empty($import->img_name) && (strstr($import->img_name, 'http') || strstr($import->img_name, 'https'))) {
          $sitegroup->setImportPhoto($import->img_name, 1);
          $albumTable = Engine_Api::_()->getDbtable('albums', 'sitegroup');
          $album_id = $albumTable->update(array('photo_id' => $sitegroup->photo_id), array('group_id = ?' => $sitegroup->group_id));
        }

        if (file_exists(APPLICATION_PATH . "/public/$import_image") && !empty($import->img_name)) {
          // Make temporary folder
          $archiveOutputPath = substr($archiveFilename, 0, strrpos($archiveFilename, '.'));

          if (file_exists(APPLICATION_PATH . "/public/$import_image" . DIRECTORY_SEPARATOR . $importFile->photo_filename)) {
            // Extract
            $zip = new ZipArchive;
            $res = $zip->open($archiveFilename);
            $zip->extractTo(APPLICATION_PATH . "/public/$import_image");
            $zip->close();
            @chmod($archiveOutputPath, 0777);
            @unlink($archiveFilename);
          }

          $archiveFilename1 = APPLICATION_PATH . "/public/$import_image";
          if (file_exists($archiveFilename1 . '/' . $import->img_name)) {
            $sitegroup->setPhoto($archiveFilename1 . '/' . $import->img_name);
          }
        }
        //END PROFILE IMAGE IMPORTING WORK
        //START CUSTOM FIELDS IMPORT WORK 
        //GET PROFILE MAPPING ID
        $categoryIds = array();
        $categoryIds[] = $sitegroup->category_id;
        $categoryIds[] = $sitegroup->subcategory_id;
        $categoryIds[] = $sitegroup->subsubcategory_id;
        $profile_type = Engine_Api::_()->getDbtable('categories', 'sitegroup')->getProfileType($categoryIds, 0, 'profile_type');

        $groupFieldValueTable = Engine_Api::_()->fields()->getTable('sitegroup_group', 'values');
        $groupFieldOptionTable = Engine_Api::_()->fields()->getTable('sitegroup_group', 'options');
        $groupFieldMapsTable = Engine_Api::_()->fields()->getTable('sitegroup_group', 'maps');
        $groupFieldSearchTable = Engine_Api::_()->fields()->getTable('sitegroup_group', 'search');

        $db = Engine_Db_Table::getDefaultAdapter();
        $columns = $db->query("SHOW COLUMNS FROM engine4_sitegroup_imports")->fetchAll();
        $custom_field = '';
        $countCustomFields = 0;
        foreach ($columns as $column) {
          $countCustomFields++;
          $custom_field .= $column['Field'] . ',';
        }
        $custom_field = explode('userclaim', $custom_field);
        $custom_field = ltrim($custom_field[1], ',');
        $custom_field = rtrim($custom_field, ',');
        $custom_fields = explode(',', $custom_field);
        if ($countCustomFields > 16) {
          foreach ($custom_fields as $cloumn_name) {
            $field = explode('_', $cloumn_name);
            $field_id = $field[2];
            $selectFieldsMapTable = $groupFieldMapsTable->select()
                    ->from($groupFieldMapsTable->info('name'), array('option_id'))
                    ->where('child_id = ?', $field_id);
            $fieldsMappingResult = $selectFieldsMapTable->query()->fetchAll();
            $option_ids = array();
            foreach ($fieldsMappingResult as $map) {
              $option_ids[] = $map['option_id'];
            }
            if (!in_array($profile_type, $option_ids)) {
              continue;
            }

            $profileTypeExist = $db->select()->from('engine4_sitegroup_group_fields_values', 'value')->where('item_id = ?', $group_id)->where('value = ?', $profile_type)->where('field_id = ?', 1)->query()->fetchColumn();
            if (empty($profileTypeExist)) {
              $groupFieldValueTable->insert(array('item_id' => $group_id, 'field_id' => 1, 'index' => 0, 'value' => $profile_type));
            }

            $groupFieldMetaTable = Engine_Api::_()->fields()->getTable('sitegroup_group', 'meta');
            $selectMetaData = $groupFieldMetaTable->select()->from($groupFieldMetaTable->info('name'), array('type', 'alias', 'search', 'required'))->where('field_id = ?', $field_id);
            $metaData = $groupFieldMetaTable->fetchRow($selectMetaData);
            $fieldType = $metaData->type;
            $fieldId = $field_id;
            $fieldAlias = $metaData->alias;
            $fieldSearch = $metaData->search;
            $fieldRequired = $metaData->required;

            if ($fieldType == 'multi_checkbox' || $fieldType == 'multiselect') {
              if ($import->$cloumn_name == 'Yes' || $import->$cloumn_name == 'yes') {
                $option_value = $field[4];
                $option_exist = 1;
              } else {
                $option_exist = 0;
              }
            } elseif (($fieldType == 'gender' || $fieldType == 'radio' || $fieldType == 'select') && !empty($import->$cloumn_name)) {
              $option_exist = 1;
              $selectOptionTable = $groupFieldOptionTable->select()
                      ->from($groupFieldOptionTable->info('name'), 'option_id')
                      ->where('label = ?', $import->$cloumn_name)
                      ->where('field_id = ?', $field_id);
              $optionTableResult = $groupFieldOptionTable->fetchRow($selectOptionTable);
              $option_value = $optionTableResult->option_id;
            } else {
              if (!empty($import->$cloumn_name)) {
                $option_value = $import->$cloumn_name;
                $option_exist = 1;
              } else {
                $option_exist = 0;
              }
            }

            $selectValueTable = $groupFieldValueTable->select()
                    ->from($groupFieldValueTable->info('name'), 'index')
                    ->where('field_id = ?', $fieldId)
                    ->where('item_id = ?', $group_id)
                    ->order('index DESC');
            $index_value = $groupFieldValueTable->fetchRow($selectValueTable);

            if (!empty($option_exist) && !empty($option_value)) {
              if (count($index_value)) {
                $index_value = $index_value->index;
                $groupFieldValueTable->insert(array('item_id' => $group_id, 'field_id' => $fieldId, 'index' => $index_value + 1, 'value' => $option_value));
              } else {
                $groupFieldValueTable->insert(array('item_id' => $group_id, 'field_id' => $fieldId, 'index' => 0, 'value' => $option_value));
              }
            }
            if ($fieldSearch == 1 && !empty($fieldAlias)) {
              $field_label = $fieldAlias;
            } elseif ($fieldSearch == 1 && empty($fieldAlias)) {
              $field_label = 'field_' . $fieldId;
            }

            if (!empty($fieldSearch)) {
              $selectSearchTable = $groupFieldSearchTable->select()
                      ->from($groupFieldSearchTable->info('name'), $field_label)
                      ->where('item_id = ?', $group_id)
                      ->where('profile_type = ?', (string) ($profile_type));
              $fieldSearchValue = $groupFieldSearchTable->fetchRow($selectSearchTable);

              if (!empty($option_exist)) {
                if (count($fieldSearchValue)) {
                  if (!empty($fieldSearchValue->$field_label)) {
                    $field_value = $fieldSearchValue->$field_label . ',' . $option_value;
                  } else {
                    $field_value = $option_value;
                  }
                } else {
                  $field_value = $option_value;
                }

                if (empty($fieldSearchValue)) {
                  $db->insert('engine4_sitegroup_group_fields_search', array('item_id' => $group_id, 'profile_type' => $profile_type, $field_label => $field_value));
                } else {
                  $db->update('engine4_sitegroup_group_fields_search', array($field_label => $field_value), array('profile_type = ?' => $profile_type, 'item_id = ?' => $group_id));
                }
              }
            }
          }
        }
        $sitegroup->profile_type = $profile_type;


        //PUT GROUP OWNER IN MANAGE ADMIN TABLE
        $row = $manageadminsTable->createRow();
        $row->user_id = $sitegroup->owner_id;
        $row->group_id = $sitegroup->group_id;
        $row->save();

        //DEFAULT ENTRIES FOR SITEAPGE-FORM
        $group_id = $sitegroup->group_id;
        $sitegroupFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupform');
        if ($sitegroupFormEnabled) {

          $sitegroupformtable = Engine_Api::_()->getDbtable('sitegroupforms', 'sitegroupform');
          $optionid = Engine_Api::_()->getDbtable('groupquetions', 'sitegroupform');
          $table_option = Engine_Api::_()->fields()->getTable('sitegroupform', 'options');

          $sitegroupform = $table_option->createRow();
          $sitegroupform->label = $values['title'];
          $sitegroupform->field_id = 1;
          $option_id = $sitegroupform->save();
          $optionids = $optionid->createRow();
          $optionids->option_id = $option_id;
          $optionids->group_id = $group_id;
          $optionids->save();
          $sitegroupforms = $sitegroupformtable->createRow();
          $sitegroupforms->group_id = $group_id;
          $sitegroupforms->save();
        }

        //SET PHOTO
        $album_id = $albumTable->insert(array(
            'photo_id' => 0,
            'owner_id' => $sitegroup->owner_id,
            'group_id' => $sitegroup->group_id,
            'title' => $sitegroup->title,
            'creation_date' => $sitegroup->creation_date,
            'modified_date' => $sitegroup->modified_date));

        $sitegroup->setLocation();

        //SET PRIVACY
        $auth = Engine_Api::_()->authorization()->context;
        $sitegroupmemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
        if (!empty($sitegroupmemberEnabled)) {
          $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        } else {
          $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        }

        $privacyMax = array_search('everyone', $roles);

        if (empty($importFile->view_privacy)) {
          $importFile->view_privacy = "everyone";
        }

        if (empty($importFile->comment_privacy)) {
          $importFile->comment_privacy = "everyone";
        }

        $viewMax = array_search($importFile->view_privacy, $roles);
        $commentMax = array_search($importFile->comment_privacy, $roles);

        foreach ($roles as $i => $role) {
          $auth->setAllowed($sitegroup, $role, 'view', ($i <= $viewMax));
          $auth->setAllowed($sitegroup, $role, 'comment', ($i <= $commentMax));
          $auth->setAllowed($sitegroup, $role, 'print', ($i <= $privacyMax));
          $auth->setAllowed($sitegroup, $role, 'tfriend', ($i <= $privacyMax));
          $auth->setAllowed($sitegroup, $role, 'overview', ($i <= $privacyMax));
          $auth->setAllowed($sitegroup, $role, 'map', ($i <= $privacyMax));
          $auth->setAllowed($sitegroup, $role, 'insight', ($i <= $privacyMax));
          $auth->setAllowed($sitegroup, $role, 'layout', ($i <= $privacyMax));
          $auth->setAllowed($sitegroup, $role, 'contact', ($i <= $privacyMax));
          $auth->setAllowed($sitegroup, $role, 'form', ($i <= $privacyMax));
          $auth->setAllowed($sitegroup, $role, 'offer', ($i <= $privacyMax));
          $auth->setAllowed($sitegroup, $role, 'invite', ($i <= $privacyMax));
        }

        $sitegroupmemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
        if (!empty($sitegroupmemberEnabled)) {
          $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
        } else {
          $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
        }

        $createMax = array_search("owner", $roles);

        //START SITEGROUPDICUSSION PLUGIN WORK
        $sitegroupdiscussionEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion');
        if ($sitegroupdiscussionEnabled) {
          foreach ($roles as $i => $role) {
            $auth->setAllowed($sitegroup, $role, 'sdicreate', ($i <= $createMax));
          }
        }
        //END SITEGROUPDICUSSION PLUGIN WORK        
        //START SITEGROUPALBUM PLUGIN WORK
        $sitegroupalbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum');
        if ($sitegroupalbumEnabled) {
          foreach ($roles as $i => $role) {
            $auth->setAllowed($sitegroup, $role, 'spcreate', ($i <= $createMax));
          }
        }
        //END SITEGROUPALBUM PLUGIN WORK
        //START SITEGROUPDOCUMENT PLUGIN WORK
        $sitegroupDocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument');
        if ($sitegroupDocumentEnabled) {
          foreach ($roles as $i => $role) {
            $auth->setAllowed($sitegroup, $role, 'sdcreate', ($i <= $createMax));
          }
        }
        //END SITEGROUPDOCUMENT PLUGIN WORK
        //START SITEGROUPVIDEO PLUGIN WORK
        $sitegroupVideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo');
        if ($sitegroupVideoEnabled) {
          foreach ($roles as $i => $role) {
            $auth->setAllowed($sitegroup, $role, 'svcreate', ($i <= $createMax));
          }
        }
        //END SITEGROUPVIDEO PLUGIN WORK
        //START SITEGROUPPOLL PLUGIN WORK
        $sitegroupPollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouppoll');
        if ($sitegroupPollEnabled) {
          foreach ($roles as $i => $role) {
            $auth->setAllowed($sitegroup, $role, 'splcreate', ($i <= $createMax));
          }
        }
        //END SITEGROUPPOLL PLUGIN WORK
        //START SITEGROUPNOTE PLUGIN WORK
        $sitegroupNoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupnote');
        if ($sitegroupNoteEnabled) {
          foreach ($roles as $i => $role) {
            $auth->setAllowed($sitegroup, $role, 'sncreate', ($i <= $createMax));
          }
        }
        //END SITEGROUPNOTE PLUGIN WORK
        //START SITEGROUPEVENT PLUGIN WORK
        $sitegroupEventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent');
        if ($sitegroupEventEnabled) {
          foreach ($roles as $i => $role) {
            $auth->setAllowed($sitegroup, $role, 'secreate', ($i <= $createMax));
          }
        }
        //END SITEGROUPEVENT PLUGIN WORK
        //Commit
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

//       //EXTRACTING CURRENT ADMIN SETTINGS FOR THIS VIEW GROUP.
//       $selectGroupAdmin = $groupAdminTable->select()
//                       ->setIntegrityCheck(false)
//                       ->from($groupAdminTableName)
//                       ->where('name = ?', 'sitegroup_index_view');
//       $groupAdminresult = $groupAdminTable->fetchRow($selectGroupAdmin);
// 
//       //NOW INSERTING THE ROW IN GROUP TABLE
//       $groupObject = $groupTable->createRow();
//       $groupObject->displayname = ( null !== ($name = $values['title']) ? $name : 'Untitled' );
//       $groupObject->title = ( null !== ($name = $values['title']) ? $name : 'Untitled' );
//       $groupObject->description = $values['body'];
//       $groupObject->name = "sitegroup_index_view";
//       $groupObject->url = $groupAdminresult->url;
//       $groupObject->custom = $groupAdminresult->custom;
//       $groupObject->fragment = $groupAdminresult->fragment;
//       $groupObject->keywords = $groupAdminresult->keywords;
//       $groupObject->layout = $groupAdminresult->layout;
//       $groupObject->view_count = $groupAdminresult->view_count;
//       $groupObject->user_id = $values['owner_id'];
//       $groupObject->group_id = $group_id;
//       $contentGroupId = $groupObject->save();
// 
//       //NOW FETCHING GROUP CONTENT DEFAULT SETTING INFORMATION FROM CORE CONTENT TABLE FOR THIS GROUP.
//       $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0);
//       if (!$layout) {
//         Engine_Api::_()->getDbtable('content', 'sitegroup')->setContentDefault($contentGroupId);
//       } else {
//         Engine_Api::_()->getApi('layoutcore', 'sitegroup')->setContentDefaultLayout($contentGroupId);
//       }
      //IF ALL GROUPS HAS BEEN IMPORTED THAN CHANGE THE STATUS
      if ($importFile->current_import_id == $importFile->last_import_id) {
        $importFile->status = 'Completed';
      }
      $importFile->save();

      //CREATE LOG ENTRY IN LOG FILE
      if (file_exists(APPLICATION_PATH . '/temporary/log/CSVToGroupImport.log')) {

        $stringData = '';
        if ($import_count == 1) {
          $stringData .= "\n\n----------------------------------------------------------------------------------------------------------------\n";
          $stringData .= $this->view->translate("Import History of '") . $importFile->filename . $this->view->translate("' with file id: ") . $importFile->importfile_id . $this->view->translate(", created on ") . $importFile->creation_date . $this->view->translate(" is given below.");
          $stringData .= "\n----------------------------------------------------------------------------------------------------------------\n\n";
        }

        $myFile = APPLICATION_PATH . '/temporary/log/CSVToGroupImport.log';
        $fh = fopen($myFile, 'a') or die("can't open file");
        $current_time = date('D, d M Y H:i:s T');
        $group_id = $sitegroup->group_id;
        $group_title = $sitegroup->title;
        $stringData .= $this->view->translate("Successfully created a new group at ") . $current_time . $this->view->translate(". ID and title of that Group are ") . $group_id . $this->view->translate(" and '") . $group_title . $this->view->translate("' respectively.") . "\n\n";
        fwrite($fh, $stringData);
        fclose($fh);
      }

      if ($import_count >= 100) {
        $current_import_id = $importFile->current_import_id + 1;
        $this->_redirect("admin/sitegroup/importlisting/data-import?importfile_id=$importfile_id&current_import_id=$current_import_id&current_import=1");
      }
    }

    $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Importing is done successfully !'))
    ));
  }

  //ACTION FOR MANAGING THE CSV FILES DATAS
  public function manageAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_admin_main', array(), 'sitegroup_admin_main_import');

    //FORM CREATION FOR SORTING
    $this->view->formFilter = $formFilter = new Sitegroup_Form_Admin_Import_Filter();
    $group = $this->_getParam('page', 1);

    $tableImportFile = Engine_Api::_()->getDbTable('importfiles', 'sitegroup');
    $select = $tableImportFile->select();

    //IF IMPORT IS IN RUNNING STATUS FOR SOME FILE THAN DONT SHOW THE START BUTTON FOR ALL
    $importFileStatusData = $tableImportFile->fetchRow(array('status = ?' => 'Running'));
    $this->view->runningSomeImport = 0;
    if (!empty($importFileStatusData)) {
      $this->view->runningSomeImport = 1;
    }

    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array(
        'order' => 'importfile_id',
        'order_direction' => 'DESC',
            ), $values);

    $this->view->assign($values);

    $select->order((!empty($values['order']) ? $values['order'] : 'importfile_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->total_slideshows = $paginator->getTotalItemCount();
    $this->view->paginator->setItemCountPerPage(100);
    $this->view->paginator = $paginator->setCurrentPageNumber($group);
  }

  //ACTION FOR STOP IMPORTING DATA
  public function stopAction() {

    //UPDATE THE STATUS TO STOP
    $importfile_id = $this->_getParam('importfile_id');
    $importFile = Engine_Api::_()->getItem('sitegroup_importfile', $importfile_id);
    $importFile->status = 'Stopped';
    $importFile->save();

    //REDIRECTING TO MANAGE GROUP IF FORCE STOP
    $forceStop = $this->_getParam('forceStop');
    if (!empty($forceStop)) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'manage'));
    }
  }

  //ACTION FOR ROLLBACK IMPORTING DATA
  public function rollbackAction() {

    //INCREASE THE MEMORY ALLOCATION SIZE AND INFINITE SET TIME OUT
    ini_set('memory_limit', '2048M');
    set_time_limit(0);

    $this->_helper->layout->setLayout('admin-simple');
    $this->view->importfile_id = $importfile_id = $this->_getParam('importfile_id');

    //FETCH IMPORT FILE OBJECT
    $importFile = Engine_Api::_()->getItem('sitegroup_importfile', $importfile_id);

    //IF STATUS IS PENDING THAN RETURN
    if ($importFile->status == 'Pending') {
      return;
    }

    $returend_current_group_id = $this->_getParam('current_group_id');

    $redirect = 0;
    if (isset($_GET['redirect'])) {
      $redirect = $_GET['redirect'];
    }

    if (empty($redirect) && isset($_POST['redirect'])) {
      $redirect = $_POST['redirect'];
    }

    //START ROLLBACK IF CONFIRM BY USER OR RETURNED CURRENT GROUP ID IS NOT EMPTY
    if (!empty($redirect)) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        $first_group_id = $importFile->first_group_id;
        $last_group_id = $importFile->last_group_id;

        if (!empty($first_group_id) && !empty($last_group_id)) {
          $tableGroup = Engine_Api::_()->getDbtable('groups', 'sitegroup');

          $current_group_id = $first_group_id;

          if (!empty($returend_current_group_id)) {
            $current_group_id = $returend_current_group_id;
          }

          //MAKE QUERY
          $sqlStr = "group_id BETWEEN " . "'" . $current_group_id . "'" . " AND " . "'" . $last_group_id . "'" . "";

          $select = $tableGroup->select()
                  ->from($tableGroup->info('name'), array('group_id'))
                  ->where($sqlStr);
          $groupDatas = $select->query()->fetchAll();

          if (!empty($groupDatas)) {
            $rollback_count = 0;
            foreach ($groupDatas as $groupData) {
              $group_id = $groupData['group_id'];

              //DELETE GROUP
              Engine_Api::_()->sitegroup()->onGroupDelete($group_id);

              $db->commit();

              $rollback_count++;

              //REDIRECTING TO SAME ACTION AFTER EVERY 100 ROLLBACKS
              if ($rollback_count >= 100) {
                $current_group_id = $group_id + 1;
                $this->_redirect("admin/sitegroup/importlisting/rollback?importfile_id=$importfile_id&current_group_id=$current_group_id&redirect=1");
              }
            }
          }
        }
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      //UPDATE THE DATA IN engine4_sitegroup_importfiles TABLE
      $importFile->status = 'Pending';
      $importFile->first_group_id = 0;
      $importFile->last_group_id = 0;
      $importFile->current_import_id = $importFile->first_import_id;
      $importFile->save();

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Rollbacked successfully !'))
      ));
    }
    $this->renderScript('admin-importlisting/rollback.tpl');
  }

  //ACTION FOR DELETE IMPORT FILES AND IMPORT DATA
  public function deleteAction() {
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->importfile_id = $importfile_id = $this->_getParam('importfile_id');

    //IF CONFIRM FOR DATA DELETION
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        //IMPORT FILE OBJECT
        $importFile = Engine_Api::_()->getItem('sitegroup_importfile', $importfile_id);

        if (!empty($importFile)) {

          $first_import_id = $importFile->first_import_id;
          $last_import_id = $importFile->last_import_id;

          //MAKE QUERY FOR FETCH THE DATA
          $tableImport = Engine_Api::_()->getDbtable('imports', 'sitegroup');

          $sqlStr = "import_id BETWEEN " . "'" . $first_import_id . "'" . " AND " . "'" . $last_import_id . "'" . "";

          $select = $tableImport->select()
                  ->from($tableImport->info('name'), array('import_id'))
                  ->where($sqlStr);
          $importDatas = $select->query()->fetchAll();

          if (!empty($importDatas)) {
            foreach ($importDatas as $importData) {
              $import_id = $importData['import_id'];

              //DELETE IMPORT DATA BELONG TO IMPORT FILE
              $tableImport->delete(array('import_id = ?' => $import_id));
            }
          }

          //FINALLY DELETE IMPORT FILE DATA
          Engine_Api::_()->getDbtable('importfiles', 'sitegroup')->delete(array('importfile_id = ?' => $importfile_id));
        }

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Import data has been deleted successfully !'))
      ));
    }
    $this->renderScript('admin-importlisting/delete.tpl');
  }

  //ACTION FOR DELETE SLIDESHOW AND THEIR BELONGINGS
  public function multiDeleteAction() {
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();

      //IF ADMIN CLICK ON DELETE SELECTED BUTTON
      if (!empty($values['delete'])) {
        foreach ($values as $key => $value) {
          if ($key == 'delete_' . $value) {
            $importfile_id = (int) $value;
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
              //IMPORT FILE OBJECT
              $importFile = Engine_Api::_()->getItem('sitegroup_importfile', $importfile_id);

              if (!empty($importFile)) {

                $first_import_id = $importFile->first_import_id;
                $last_import_id = $importFile->last_import_id;

                //MAKE QUERY FOR FETCH THE DATA
                $tableImport = Engine_Api::_()->getDbtable('imports', 'sitegroup');

                $sqlStr = "import_id BETWEEN " . "'" . $first_import_id . "'" . " AND " . "'" . $last_import_id . "'" . "";

                $select = $tableImport->select()
                        ->from($tableImport->info('name'), array('import_id'))
                        ->where($sqlStr);
                $importDatas = $select->query()->fetchAll();

                if (!empty($importDatas)) {
                  foreach ($importDatas as $importData) {
                    $import_id = $importData['import_id'];

                    //DELETE IMPORT DATA BELONG TO IMPORT FILE
                    $tableImport->delete(array('import_id = ?' => $import_id));
                  }
                }

                //FINALLY DELETE IMPORT FILE DATA
                Engine_Api::_()->getDbtable('importfiles', 'sitegroup')->delete(array('importfile_id = ?' => $importfile_id));
              }

              $db->commit();
            } catch (Exception $e) {
              $db->rollBack();
              throw $e;
            }
          }
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'manage'));
  }

  //ACTION FOR DOWNLOADING THE CSV TEMPLATE FILE
  public function downloadSampleAction() {

    $path = $this->_getPath();
    $file_path = "$path/example_group_import.csv";

    @chmod($path, 0777);
    @chmod($file_path, 0777);

    $file_string = "";

    $file_string = "Title|Group_Url|Category|Sub_Category|3rd Level Category|Description|Overview|Tag_String|Location|Price|Email_Id|Web_Address|Phone_Number|Claim_a_Group|Photo Name.ext";

    @chmod($path, 0777);
    @chmod($file_path, 0777);
    $fp = fopen(APPLICATION_PATH . '/temporary/example_group_import.csv', 'w+');
    fwrite($fp, $file_string);
    fclose($fp);

    //KILL ZEND'S OB
    $isGZIPEnabled = false;
    if (ob_get_level()) {
      $isGZIPEnabled = true;
      @ob_end_clean();
    }

    $path = APPLICATION_PATH . "/temporary/example_group_import.csv";
    header("Content-Disposition: attachment; filename=" . urlencode(basename($path)), true);
    header("Content-Transfer-Encoding: Binary", true);
    //header("Content-Type: application/x-tar", true);
    // header("Content-Type: application/force-download", true);
    header("Content-Type: application/octet-stream", true);
    header("Content-Type: application/download", true);
    header("Content-Description: File Transfer", true);
    if (empty($isGZIPEnabled)) {
      header("Content-Length: " . filesize($path), true);
    }

    readfile("$path");

    exit();
  }

  //ACTION FOR DOWNLOADING THE CSV TEMPLATE FILE
  public function downloadAction() {

    $path_import = $this->_getPathImport();

    $db = Engine_Db_Table::getDefaultAdapter();
    $import_id = $db->select('importfile_id')
            ->from('engine4_sitegroup_importfiles')
            ->query()
            ->fetchColumn();

    if (file_exists($path_import) && is_file($path_import) && !empty($import_id)) {

      //KILL ZEND'S OB
      $isGZIPEnabled = false;
      if (ob_get_level()) {
        $isGZIPEnabled = true;
        @ob_end_clean();
      }

      header("Content-Disposition: attachment; filename=" . urlencode(basename($path_import)), true);
      header("Content-Transfer-Encoding: Binary", true);
      header("Content-Type: application/x-tar", true);
      //header("Content-Type: application/force-download", true);
      header("Content-Type: application/octet-stream", true);
      header("Content-Type: application/download", true);
      header("Content-Description: File Transfer", true);
      if (empty($isGZIPEnabled)) {
        header("Content-Length: " . filesize($path_import), true);
      }
      readfile("$path_import");
    } else {
      $path = $this->_getPath();
      $file_path = "$path/previous_group_import.csv";

      @chmod($path, 0777);
      @chmod($file_path, 0777);

      $file_string = "";
      $file_string = "Title|Group_Url|Category|Sub_Category|3rd Level Category|Description|Overview|Tag_String|Location|Price|Email_Id|Web_Address|Phone_Number|Claim_a_Group|Photo Name.ext";

      @chmod($path, 0777);
      @chmod($file_path, 0777);
      $fp = fopen(APPLICATION_PATH . '/temporary/previous_group_import.csv', 'w+');
      fwrite($fp, $file_string);
      fclose($fp);

      //KILL ZEND'S OB
      $isGZIPEnabled = false;
      if (ob_get_level()) {
        $isGZIPEnabled = true;
        @ob_end_clean();
      }

      $path = APPLICATION_PATH . "/temporary/previous_group_import.csv";
      header("Content-Disposition: attachment; filename=" . urlencode(basename($path)), true);
      header("Content-Transfer-Encoding: Binary", true);
      //header("Content-Type: application/x-tar", true);
      //header("Content-Type: application/force-download", true);
      header("Content-Type: application/octet-stream", true);
      header("Content-Type: application/download", true);
      header("Content-Description: File Transfer", true);
      if (empty($isGZIPEnabled)) {
        header("Content-Length: " . filesize($path), true);
      }
      readfile("$path");
    }

    exit();
  }

  protected function _getPathImport($key = 'path') {

    $basePath = realpath(APPLICATION_PATH . "/public/sitegroup_group");
    return $this->_checkPath($this->_getParam($key, ''), $basePath);
  }

  protected function _getPath($key = 'path') {
    $basePath = realpath(APPLICATION_PATH . "/temporary");
    return $this->_checkPath($this->_getParam($key, ''), $basePath);
  }

  protected function _checkPath($path, $basePath) {
    //SANATIZE
    $path = preg_replace('/\.{2,}/', '.', $path);
    $path = preg_replace('/[\/\\\\]+/', '/', $path);
    $path = trim($path, './\\');
    $path = $basePath . '/' . $path;

    //Resolve
    $basePath = realpath($basePath);
    $path = realpath($path);

    //CHECK IF THIS IS A PARENT OF THE BASE PATH
    if ($basePath != $path && strpos($basePath, $path) !== false) {
      return $this->_helper->redirector->gotoRoute(array());
    }
    return $path;
  }

  public function showFieldsAction() {

    $locale = Zend_Registry::get('Zend_Translate')->getLocale();
    $territories = Zend_Locale::getTranslationList('territory', $locale, 2);
    $fieldType = $this->_getParam('field');

    if ($fieldType == 'ethnicity') {
      $this->view->values = array(
          'asian' => 'Asian',
          'black' => 'Black / African descent',
          'hispanic' => 'Latino / Hispanic',
          'pacific' => 'Pacific Islander',
          'white' => 'White / Caucasian',
          'other' => 'Other'
      );
    } elseif ($fieldType == 'occupation') {
      $this->view->values = array('admn' => 'Administrative / Secretarial',
          'arch' => 'Architecture / Interior design',
          'crea' => 'Artistic / Creative / Performance',
          'educ' => 'Education / Teacher / Professor',
          'mngt' => 'Executive / Management',
          'fash' => 'Fashion / Model / Beauty',
          'fina' => 'Financial / Accounting / Real Estate',
          'labr' => 'Labor / Construction',
          'lawe' => 'Law enforcement / Security / Military',
          'legl' => 'Legal',
          'medi' => 'Medical / Dental / Veterinary / Fitness',
          'nonp' => 'Nonprofit / Volunteer / Activist',
          'poli' => 'Political / Govt / Civil Service / Military',
          'retl' => 'Retail / Food services',
          'retr' => 'Retired',
          'sale' => 'Sales / Marketing',
          'self' => 'Self-Employed / Entrepreneur',
          'stud' => 'Student',
          'tech' => 'Technical / Science / Computers / Engineering',
          'trav' => 'Travel / Hospitality / Transportation',
          'othr' => 'Other profession'
      );
    } elseif ($fieldType == 'education_level') {
      $this->view->values = array(
          'high_school' => 'High School',
          'some_college' => 'Some College',
          'associates' => 'Associates Degree',
          'bachelors' => 'Bachelors Degree',
          'graduate' => 'Graduate Degree',
          'phd' => 'PhD / Post Doctoral'
      );
    } elseif ($fieldType == 'relationship_status') {
      $this->view->values = array(
          'single' => 'Single',
          'relationship' => 'In a Relationship',
          'engaged' => 'Engaged',
          'married' => 'Married',
          'complicated' => 'Its Complicated',
          'open' => 'In an Open Relationship',
          'widow' => 'Widowed'
      );
    } elseif ($fieldType == 'looking_for') {
      $this->view->values = array(
          'friendship' => 'Friendship',
          'dating' => 'Dating',
          'relationship' => 'A Relationship',
          'networking' => 'Networking'
      );
    } elseif ($fieldType == 'weight') {
      $this->view->values = array(
          'slender' => 'Slender',
          'average' => 'Average',
          'athletic' => 'Athletic',
          'heavy' => 'Heavy',
          'stocky' => 'Stocky',
          'little_fat' => 'A few extra pounds'
      );
    } elseif ($fieldType == 'religion') {
      $this->view->values = array(
          'agnostic' => 'Agnostic',
          'atheist' => 'Atheist',
          'buddhist' => 'Buddhist',
          'taoist' => 'Taoist',
          'catholic' => 'Christian (Catholic)',
          'mormon' => 'Christian (LDS)',
          'protestant' => 'Christian (Protestant)',
          'hindu' => 'Hindu',
          'jewish' => 'Jewish',
          'muslim' => 'Muslim ',
          'spiritual' => 'Spiritual',
          'other' => 'Other'
      );
    } elseif ($fieldType == 'political_views') {
      $this->view->values = array(
          'mid' => 'Middle of the Road',
          'far_right' => 'Very Conservative',
          'right' => 'Conservative',
          'left' => 'Liberal',
          'far_left' => 'Very Liberal',
          'anarchy' => 'Non-conformist',
          'libertarian' => 'Libertarian',
          'green' => 'Green',
          'other' => 'Other'
      );
    } elseif ($fieldType == 'income') {
      $this->view->values = array(
          '0' => 'Less than $25,000',
          '25_35' => '$25,001 to $35,000',
          '35_50' => '$35,001 to $50,000',
          '50_75' => '$50,001 to $75,000',
          '75_100' => '$75,001 to $100,000',
          '100_150' => '$100,001 to $150,000',
          '1' => '$150,001'
      );
    } elseif ($fieldType == 'partner_gender') {
      $this->view->values = array(
          'men' => 'Men',
          'women' => 'Women'
      );
    } elseif ($fieldType == 'country') {
      $this->view->values = $territories;
    } elseif ($fieldType == 'zodiac') {
      $this->view->values = array(
          'apricorn' => 'Apricorn',
          'aquarius' => 'Aquarius',
          'pisces' => 'Pisces',
          'aries' => 'Aries',
          'taurus' => 'Taurus',
          'gemini' => 'Gemini',
          'cancer' => 'Cancer',
          'leo' => 'Leo',
          'virgo' => 'Virgo',
          'libra' => 'Libra',
          'scorpio' => 'Scorpio',
          'sagittarius' => 'Sagittarius'
      );
    } elseif ($fieldType == 'date') {
      $this->view->values = array(
          'YYYY-MM-DD' => '2013-8-15'
      );
    }
  }

  public function uploadPhotoAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_admin_main', array(), 'sitegroup_admin_main_import');

    $this->view->importfile_id = $importfile_id = $this->_getParam('importfile_id');

    $this->view->importFile = Engine_Api::_()->getItem('sitegroup_importfile', $importfile_id);

    $tableImportFile = Engine_Api::_()->getDbTable('importfiles', 'sitegroup');
    $select = $tableImportFile->select();

    //IF IMPORT IS IN RUNNING STATUS FOR SOME FILE THAN DONT SHOW THE START BUTTON FOR ALL
    $importFileStatusData = $tableImportFile->fetchRow(array('status = ?' => 'Running'));
    $this->view->runningSomeImport = 0;
    if (!empty($importFileStatusData)) {
      $this->view->runningSomeImport = 1;
    }
  }

  public function uploadAction() {

    $importfile_id = $this->_getParam('importfile_id');
    $basePath = 'sitegroup_importfiles_' . $importfile_id;

    if (is_dir(APPLICATION_PATH . "/public/$basePath") != 1) {
      @mkdir(APPLICATION_PATH . "/public/$basePath", 0777, true);
    }

    @chmod(APPLICATION_PATH . "/public/$basePath", 0777);
    $this->view->path = $path = APPLICATION_PATH . "/public/$basePath";

    // Check method
    if (!$this->getRequest()->isPost()) {
      return;
    }

    // Check ul bit
    if (null === $this->_getParam('ul')) {
      return;
    }

    // Prepare
    if (empty($_FILES['Filedata'])) {
      $this->view->error = 'File failed to upload. Check your server settings (such as php.ini max_upload_filesize).';
      return;
    }

    // Prevent evil files from being uploaded
    $disallowedExtensions = array('zip');
    if (!in_array(end(explode(".", $_FILES['Filedata']['name'])), $disallowedExtensions)) {
      $this->view->error = 'File type or extension forbidden.';
      return;
    }


    $info = $_FILES['Filedata'];
    $targetFile = $path . '/' . $info['name'];
    $vals = array();

    if (file_exists($targetFile)) {
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("File already exists.");
      return;
    }

    // Try to move uploaded file
    if (!move_uploaded_file($info['tmp_name'], $targetFile)) {
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Unable to move file to upload directory.");
      return;
    }

    $groupImportfileTable = Engine_Api::_()->getDbtable('importfiles', 'sitegroup');

    $selectImportTable = $groupImportfileTable->select()
            ->from($groupImportfileTable->info('name'), 'photo_filename')
            ->where('importfile_id = ?', $importfile_id);
    $fileName = $selectImportTable->query()->fetchColumn();

    if (!empty($fileName)) {
      $import_image_path = 'sitereview_importfiles_' . $importfile_id;
      @unlink(APPLICATION_PATH . "/public/" . $import_image_path . '/' . $fileName);
    }

    $groupImportfileTable->update(array('photo_filename' => $_FILES['Filedata']['name']), array('importfile_id = ?' => $importfile_id));

    $this->view->target_path = $info['tmp_name'];
    $this->view->status = 1;

    // Redirect
    if (null === $this->_helper->contextSwitch->getCurrentContext()) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    } else if ('smoothbox' === $this->_helper->contextSwitch->getCurrentContext()) {
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => true,
                  'parentRefresh' => true,
              ));
    }
  }

}