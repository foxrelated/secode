<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminImporteventController.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_AdminImporteventController extends Core_Controller_Action_Admin {

    //ACTION FOR SHOWING IMPORT INSTRUCTIONS
    public function indexAction() {

        //INCREASE THE MEMORY ALLOCATION SIZE AND INFINITE SET TIME OUT
        ini_set('memory_limit', '2048M');
        set_time_limit(0);
        ini_set('upload_max_filesize', '100M');
        ini_set('post_max_size', '100M');
        ini_set('max_input_time', 600);
        ini_set('max_execution_time', 600);
        $coreModuleTable = Engine_Api::_()->getDbtable('modules', 'core');

        include APPLICATION_PATH . '/application/modules/Siteevent/controllers/license/license2.php';

        $this->view->sitepageeventEnabled = $sitepageeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent');
        $this->view->sitebusinesseventEnabled = $sitebusinesseventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessevent');
        $this->view->sitegroupeventEnabled = $sitegroupeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent');
        $this->view->siteeventRepeatEnabled = $siteeventRepeatEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventrepeat');

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteevent_admin_main', array(), 'siteevent_admin_main_import');

        //START CODE FOR CREATING THE EventToReviewImport.log FILE
        if (!file_exists(APPLICATION_PATH . '/temporary/log/EventToSiteeventImport.log')) {
            $log = new Zend_Log();
            try {
                $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/EventToSiteeventImport.log'));
            } catch (Exception $e) {
                //CHECK DIRECTORY
                if (!@is_dir(APPLICATION_PATH . '/temporary/log') && @mkdir(APPLICATION_PATH . '/temporary/log', 0777, true)) {
                    $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/EventToSiteeventImport.log'));
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
        if (file_exists(APPLICATION_PATH . '/temporary/log/EventToSiteeventImport.log')) {
            @chmod(APPLICATION_PATH . '/temporary/log/EventToSiteeventImport.log', 0777);
        }
        //END CODE FOR CREATING THE EventToSiteeventImport.log FILE 
        //IMPORT ALL THE CORE EVENTS IN SITEEVENT PLUGIN
        //GET SITEEVENT TABLE
        $siteeventTable = Engine_Api::_()->getDbTable('events', 'siteevent');
        $siteeventTableName = $siteeventTable->info('name');

        $streamTable = Engine_Api::_()->getDbTable('stream', 'activity');

        //GET EVENT ATTACHMENT  TABLE
        $attachmentsTable = Engine_Api::_()->getDbtable('attachments', 'activity');
        $attachmentsTableName = $attachmentsTable->info('name');

        //GET CONVERSATION TABLE
        $conversationTable = Engine_Api::_()->getDbtable('conversations', 'messages');

        //GET SITEEVENT CATEGORIES TABLE
        $siteeventCategoryTable = Engine_Api::_()->getDbtable('categories', 'siteevent');
        $siteeventCategoryTableName = $siteeventCategoryTable->info('name');

        //GET SITEEVENT LOCATION TABLE
        $siteeventLocationTable = Engine_Api::_()->getDbtable('locations', 'siteevent');
        $siteeventLocationTableName = $siteeventLocationTable->info('name');

        //GET SITEEVENTOCCURRENCE TABLE
        $siteeventOccurrencesTable = Engine_Api::_()->getDbtable('occurrences', 'siteevent');

        //GET SITEEVENTMEMBERSHIP TABLE
        $siteeventMembershipTable = Engine_Api::_()->getDbtable('membership', 'siteevent');
        $siteeventMembershipTableName = $siteeventMembershipTable->info('name');

        //GET SITEEVENT OTHER INFO TABLE
        $siteeventOtherinfoTable = Engine_Api::_()->getDbTable('otherinfo', 'siteevent');

        //GET SITEEVENT TOPIC TABLE
        $siteeventTopicTable = Engine_Api::_()->getDbtable('topics', 'siteevent');
        $siteeventTopicTableName = $siteeventTopicTable->info('name');

        //GET SITEEVENT POST TABLE
        $siteeventPostTable = Engine_Api::_()->getDbtable('posts', 'siteevent');
        $siteeventPostTableName = $siteeventPostTable->info('name');

        //GET SITEEVENT WATCH TABLE
        $siteeventTopicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'siteevent');
        $siteeventTopicWatchesTableName = $siteeventTopicWatchesTable->info('name');

        //GET SITEEVENT PHOTO TABLE
        $siteeventPhotoTable = Engine_Api::_()->getDbtable('photos', 'siteevent');
        $siteeventPhotoTableName = $siteeventPhotoTable->info('name');

        //GET STORAGE TABLE
        $storageTable = Engine_Api::_()->getDbtable('files', 'storage');

        //GET ALBUMS TABLE
        $siteeventAlbumTable = Engine_Api::_()->getDbtable('albums', 'siteevent');

        //GET CORE LIKE TABLES
        $likeTable = Engine_Api::_()->getDbtable('likes', 'core');
        $likeTableName = $likeTable->info('name');

        //GET CORE COMMENT TABLES
        $commentTable = Engine_Api::_()->getDbtable('comments', 'core');
        $commentTableName = $commentTable->info('name');

        //GET TAGMAPS TABLE
        $tagmapsTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
        $tagmapsTableName = $tagmapsTable->info('name');

        //GET STYLE TABLES
        $stylesTable = Engine_Api::_()->getDbtable('styles', 'core');
        $stylesTableName = $stylesTable->info('name');

        //GET ACTIONS TABLE
        $actionsTable = Engine_Api::_()->getDbtable('actions', 'activity');
        $actionsTableName = $actionsTable->info('name');
        $organizersTable = Engine_Api::_()->getDbtable('organizers', 'siteevent');

        $this->view->first_event_id = $first_event_id = 0;
        $this->view->last_event_id = $last_event_id = 0;
        $this->view->first_sitepageevent_id = $first_sitepageevent_id = 0;
        $this->view->last_sitepageevent_id = $last_sitepageevent_id = 0;
        $this->view->first_sitebusinessevent_id = $first_sitebusinessevent_id = 0;
        $this->view->last_sitebusinessevent_id = $last_sitebusinessevent_id = 0;
        $this->view->first_sitegroupevent_id = $first_sitegroupevent_id = 0;
        $this->view->last_sitegroupevent_id = $last_sitegroupevent_id = 0;

        if ($eventEnabled) {

            //GET EVENT TABLES 
            $eventTable = Engine_Api::_()->getDbTable('events', 'event');
            $eventTableName = $eventTable->info('name');

            //GET EVENT CATEGORIES TABLE
            $eventCategoryTable = Engine_Api::_()->getDbtable('categories', 'event');
            $eventCategoryTableName = $eventCategoryTable->info('name');

            //GET EVENT MEMBERSHIP TABLE
            $eventMembershipTable = Engine_Api::_()->getDbtable('membership', 'event');

            //GET EVENT TOPIC TABLE
            $eventTopicTable = Engine_Api::_()->getDbtable('topics', 'event');
            $eventTopicTableName = $eventTopicTable->info('name');

            //GET EVENT POST TABLE
            $eventPostTable = Engine_Api::_()->getDbtable('posts', 'event');
            $eventPostTableName = $eventPostTable->info('name');

            //GET EVENT PHOTO TABLE
            $eventPhotoTable = Engine_Api::_()->getDbtable('photos', 'event');
            $eventPhotoTableName = $eventPhotoTable->info('name');

            //GET EVENT TOPICWATCHES  TABLE
            $eventTopicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'event');
            $eventTopicWatchesTableName = $eventTopicWatchesTable->info('name');

            //ADD NEW COLUMN IN EVENT TABLE
            $db = Engine_Db_Table::getDefaultAdapter();
            $is_event_import = $db->query("SHOW COLUMNS FROM engine4_event_events LIKE 'is_event_import'")->fetch();
            if (empty($is_event_import)) {
                $run_query = $db->query("ALTER TABLE `engine4_event_events` ADD is_event_import TINYINT( 2 ) NOT NULL DEFAULT '0'");
            }

            $siteeventDocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventdocument');
            $eventdocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('eventdocument');

            //START IF IMPORTING IS BREAKED BY SOME REASON
            $selectEvents = $eventTable->select()
                    ->from($eventTableName, 'event_id')
                    ->where('is_event_import != ?', 1)
                    //->where('category_id != ?', 0)
                    ->order('event_id ASC');
            $eventDatas = $eventTable->fetchAll($selectEvents);

            $this->view->repeatTypeEvents = 0;
            if (!empty($eventDatas)) {

                $flag_first_event_id = 1;

                foreach ($eventDatas as $eventData) {

                    if ($flag_first_event_id == 1) {
                        $this->view->first_event_id = $first_event_id = $eventData->event_id;
                    }
                    $flag_first_event_id++;

                    $this->view->last_event_id = $last_event_id = $eventData->event_id;
                }

                if (isset($_GET['event_assigned_previous_id'])) {
                    $this->view->event_assigned_previous_id = $event_assigned_previous_id = $_GET['event_assigned_previous_id'];
                } else {
                    $this->view->event_assigned_previous_id = $event_assigned_previous_id = $first_event_id;
                }
            }

            //START IMPORTING IF REQUESTED
            if (isset($_GET['start_import']) && $_GET['start_import'] == 1 && $_GET['module'] == 'event') {

                //ACTIVITY FEED IMPORT
                $activity_event = $this->_getParam('activity_event');

                //START FETCH CATEGORY WORK
                $selectSiteeventCategory = $siteeventCategoryTable->select()
                        ->from($siteeventCategoryTableName, 'category_name')
                        ->where('category_name != ?', '')
                        ->where('cat_dependency = ?', 0);
                $siteeventCategoryDatas = $siteeventCategoryTable->fetchAll($selectSiteeventCategory);
                if (!empty($siteeventCategoryDatas)) {
                    $siteeventCategoryDatas = $siteeventCategoryDatas->toArray();
                }

                $siteeventCategoryInArrayData = array();
                foreach ($siteeventCategoryDatas as $siteeventCategoryData) {
                    $siteeventCategoryInArrayData[] = $siteeventCategoryData['category_name'];
                }

                $selectEventCategory = $eventCategoryTable->select()
                        ->where('title != ?', '')
                        ->from($eventCategoryTableName);
                $eventCategoryDatas = $eventCategoryTable->fetchAll($selectEventCategory);

                if (!empty($eventCategoryDatas)) {
                    $eventCategoryDatas = $eventCategoryDatas->toArray();
                    foreach ($eventCategoryDatas as $eventCategoryData) {
                        if (!in_array($eventCategoryData['title'], $siteeventCategoryInArrayData)) {
                            $newCategory = $siteeventCategoryTable->createRow();
                            $newCategory->category_name = $eventCategoryData['title'];
                            $newCategory->cat_dependency = 0;
                            $newCategory->cat_order = 9999;
                            $newCategory->save();
                        }
                    }
                }

                $other_category_id = $siteeventCategoryTable->select()
                        ->from($siteeventCategoryTableName, 'category_id')
                        ->where('category_name = ?', 'Others')
                        ->where('cat_dependency = ?', 0)
                        ->query()
                        ->fetchColumn();

                if (empty($other_category_id)) {
                    $newCategory = $siteeventCategoryTable->createRow();
                    $newCategory->category_name = 'Others';
                    $newCategory->cat_dependency = 0;
                    $newCategory->cat_order = 9999;
                    $other_category_id = $newCategory->save();
                }

                // CREATE CATEGORIES DEFAULT PAGES
                $categoryIds = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategoriesArray(array('cat_dependency' => 0, 'subcat_dependency' => 0));
                Engine_Api::_()->siteevent()->categoriesPageCreate($categoryIds);


                //START EVENT IMPORTING
                $selectEvents = $eventTable->select()
                        ->where('event_id >= ?', $event_assigned_previous_id)
                        ->from($eventTableName, 'event_id')
                        ->where('is_event_import != ?', 1)
                        // ->where('category_id != ?', 0)
                        ->order('event_id ASC');
                $eventDatas = $eventTable->fetchAll($selectEvents);
                $eventDatasToArray = $eventDatas->toArray();
                $next_import_count = 0;
                if (!empty($eventDatasToArray)) {
                    foreach ($eventDatas as $eventData) {
                        $db = Engine_Db_Table::getDefaultAdapter();
                        $db->beginTransaction();
                        try {
                            $event_id = $eventData->event_id;
                            if (!empty($event_id)) {
                                $event = Engine_Api::_()->getItem('event', $event_id);
                                $siteevent = $siteeventTable->createRow();
                                $siteevent->title = $event->title;
                                $siteevent->body = $event->description;
                                $siteevent->owner_id = $event->user_id;
                                $siteevent->parent_type = $event->parent_type;
                                $siteevent->parent_id = $event->parent_id;
                                //START FETCH LIST CATEGORY AND SUB-CATEGORY
                                if (!empty($event->category_id)) {
                                    $eventCategory = $eventCategoryTable->fetchRow(array('category_id = ?' => $event->category_id));
                                    if (!empty($eventCategory)) {
                                        $eventCategoryName = $eventCategory->title;
                                        if (!empty($eventCategoryName)) {
                                            $siteeventCategory = $siteeventCategoryTable->fetchRow(array('category_name = ?' => $eventCategoryName, 'cat_dependency = ?' => 0));
                                            if (!empty($siteeventCategory)) {
                                                $siteeventCategoryId = $siteevent->category_id = $siteeventCategory->category_id;
                                            }
                                        }
                                    }
                                } else {
                                    $siteevent->category_id = $other_category_id;
                                }
                                //END FETCH LIST CATEGORY AND SUB-CATEGORY
                                $siteevent->creation_date = $event->creation_date;
                                $siteevent->modified_date = $event->modified_date;
                                $siteevent->save();
                                $siteevent->creation_date = $event->creation_date;
                                $siteevent->modified_date = $event->modified_date;
                                $siteevent->view_count = 1;
                                if ($event->view_count > 0) {
                                    $siteevent->view_count = $event->view_count;
                                }
                                $siteevent->search = $event->search;
                                $siteevent->approval = $event->approval;
                                $siteevent->member_count = $event->member_count;
                                $siteevent->location = $event->location;

                                if ($event->host) {
                                    $organizer = $organizersTable->getOrganizer(array('creator_id' => $event->user_id, 'equal_title' => $event->host));
                                    if (empty($organizer)) {
                                        $organizer = $organizersTable->createRow();
                                        $organizer->title = $event->host;
                                        $organizer->creator_id = $event->user_id;
                                        $organizer->save();
                                    }
                                    $siteevent->host_type = $organizer->getType();
                                    $siteevent->host_id = $organizer->getIdentity();
                                } else {
                                    $siteevent->host_type = $event->getOwner()->getType();
                                    $siteevent->host_id = $event->user_id;
                                }
                                if(isset($siteevent->capacity) && isset($event->capacity)) {
                                    $siteevent->capacity = $event->capacity;
                                }
                                $siteevent->save();

                                $siteevent->approved = 1;
                                $siteevent->featured = 0;
                                $siteevent->sponsored = 0;
                                $siteevent->newlabel = 0;
                                $siteevent->approved_date = date('Y-m-d H:i:s');

                                //FATCH SITEEVENT CATEGORIES
                                $categoryIdsArray = array();
                                $categoryIdsArray[] = $siteevent->category_id;
                                $categoryIdsArray[] = $siteevent->subcategory_id;
                                $categoryIdsArray[] = $siteevent->subsubcategory_id;
                                $siteevent->profile_type = $siteeventCategoryTable->getProfileType($categoryIdsArray, 0, 'profile_type');
                                $siteevent->save();
                                $siteevent->setLocation();
                                $siteevent->repeat_params = '';
                                $event->is_event_import = 1;
                                $event->save();
                                $next_import_count++;
                                //END GET DATA FROM EVENT

                                $leaderList = $siteevent->getLeaderList();
                                $values = array();

                                //START FETCH PRIVACY
                                $auth = Engine_Api::_()->authorization()->context;
                                //START VIEW PRIVACY
                                $rolesEvents = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                                foreach ($rolesEvents as $rolesEvent) {
                                    if ($auth->isAllowed($event, $rolesEvent, 'view')) {
                                        $values['auth_view'] = $rolesEvent;
                                    }
                                }
                                $viewMax = array_search($values['auth_view'], $rolesEvents);
                                $rolesSiteevents = array('leader', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                                foreach ($rolesSiteevents as $i => $rolesSiteevent) {
                                    if ($rolesSiteevent === 'leader') {
                                        $rolesSiteevent = $leaderList;
                                    }
                                    $auth->setAllowed($siteevent, $rolesSiteevent, 'view', ($i <= $viewMax));
                                }
                                //END VIEW PRIVACY
                                //START COMMENT PRIVACY
                                $rolesEvents = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
                                foreach ($rolesEvents as $rolesEvent) {
                                    if ($auth->isAllowed($event, $rolesEvent, 'comment')) {
                                        $values['auth_comment'] = $rolesEvent;
                                    }
                                }
                                $commentMax = array_search($values['auth_comment'], $rolesEvents);
                                $rolesSiteevents = array('leader', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
                                foreach ($rolesSiteevents as $i => $rolesSiteevent) {
                                    if ($rolesSiteevent === 'leader') {
                                        $rolesSiteevent = $leaderList;
                                    }
                                    $auth->setAllowed($siteevent, $rolesSiteevent, 'comment', ($i <= $commentMax));
                                }
                                //END COMMENT PRIVACY
                                //START PHOTO PRIVACY
                                $rolesEvents = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
                                foreach ($rolesEvents as $rolesEvent) {
                                    if ($auth->isAllowed($event, $rolesEvent, 'photo')) {
                                        $values['auth_photo'] = $rolesEvent;
                                    }
                                }
                                $photoMax = array_search($values['auth_photo'], $rolesEvents);
                                $rolesSiteevents = array('leader', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');

                                foreach ($rolesSiteevents as $i => $rolesSiteevent) {
                                    if ($rolesSiteevent === 'leader') {
                                        $rolesSiteevent = $leaderList;
                                    }
                                    $auth->setAllowed($siteevent, $rolesSiteevent, 'photo', ($i <= $photoMax));
                                }
                                //END PHOTO PRIVACY
                                //START TOPIC PRIVACY
                                $rolesEvents = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
                                foreach ($rolesEvents as $rolesEvent) {
                                    if ($auth->isAllowed($event, $rolesEvent, 'comment')) {
                                        $values['auth_comment'] = $rolesEvent;
                                    }
                                }
                                $commentMax = array_search($values['auth_comment'], $rolesEvents);
                                $rolesSiteevents = array('leader', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
                                foreach ($rolesSiteevents as $i => $rolesSiteevent) {
                                    if ($rolesSiteevent === 'leader') {
                                        $rolesSiteevent = $leaderList;
                                    }
                                    $auth->setAllowed($siteevent, $rolesSiteevent, 'topic', ($i <= $commentMax));
                                }
                                //END TOPIC PRIVACY

                                //START VIDEO PRIVACY
                                $rolesSiteevents = array('leader', 'member',"like_member", 'owner_member', 'owner_member_member', 'owner_network', 'registered');
                                $videoMax = array_search("member", $rolesSiteevents);
                                foreach ($rolesSiteevents as $i => $rolesSiteevent) {
                                    if ($rolesSiteevent === 'leader') {
                                        $rolesSiteevent = $leaderList;
                                    }
                                    $auth->setAllowed($siteevent, $rolesSiteevent, 'video', ($i <= $videoMax));
                                }
                                //END VIDEO PRIVACY

                               //START POST PRIVACY
																if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
                                $rolesSiteevents = array('leader', 'member', 'like_member','owner_member', 'owner_member_member', 'owner_network', 'registered');
                                $postMax = array_search("member", $rolesSiteevents);
                                foreach ($rolesSiteevents as $i => $rolesSiteevent) {
                                    if ($rolesSiteevent === 'leader') {
                                        $rolesSiteevent = $leaderList;
                                    }
                                    $auth->setAllowed($siteevent, $rolesSiteevent, 'post', ($i <= $postMax));
                                }
																}
																//END POST PRIVACY

                                //START DOCUMENT PRIVACY
                                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventdocument')) {
                                    $rolesSiteevents = array('leader', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
                                    $documentMax = array_search("member", $rolesSiteevents);
                                    foreach ($rolesSiteevents as $i => $rolesSiteevent) {

                                        if ($rolesSiteevent === 'leader') {
                                            $rolesSiteevent = $leaderList;
                                        }

                                        $auth->setAllowed($siteevent, $rolesSiteevent, "document", ($i <= $documentMax));
                                    }

                                    $auth->setAllowed($siteevent, $leaderList, 'document.edit', 1);
                                }
                                //END DOCUMENT PRIVACY
                                //SET INVITE PRIVACY
                                $auth->setAllowed($siteevent, 'member', 'invite', $auth->isAllowed($event, 'member', 'invite'));

                                //CREATE SOME AUTH STUFF FOR ALL LEADERS
                                $auth->setAllowed($siteevent, $leaderList, 'photo.edit', 1);
                                $auth->setAllowed($siteevent, $leaderList, 'topic.edit', 1);
                                $auth->setAllowed($siteevent, $leaderList, 'video.edit', 1);
                                $auth->setAllowed($siteevent, $leaderList, 'edit', 1);
                                $auth->setAllowed($siteevent, $leaderList, 'delete', 1);

                                //GENERATE ACITIVITY FEED
                                if ($activity_event) {
                                    //START FETCH ACTIONS
                                    $selectActionId = $actionsTable->select()
                                            ->from($actionsTableName, 'action_id')
                                            ->where('object_type = ?', 'event')
                                            ->where('object_id = ?', $event_id)
                                            ->where('type =?', 'event_create')
                                            ->query()
                                            ->fetchColumn();
                                    if ($selectActionId) {
                                        $action = Engine_Api::_()->getItem('activity_action', $selectActionId);
                                        $action->type = 'siteevent_new';
                                        $action->object_id = $siteevent->getIdentity();
                                        $action->object_type = $siteevent->getType();
                                        $action->save();
                                        $actionsTable->resetActivityBindings($action);
                                    }
                                    //END FETCH ACTIONS
                                    //START FETCH ACTIONS
                                    $selectActionId = $actionsTable->select()
                                            ->from($actionsTableName, 'action_id')
                                            ->where('object_type = ?', 'event')
                                            ->where('object_id = ?', $event_id)
                                            ->where('type =?', 'post');
                                    $selectActionIds = $actionsTable->fetchAll($selectActionId);
                                    if ($selectActionIds) {
                                        foreach ($selectActionIds as $selectActionId) {
                                            $action = Engine_Api::_()->getItem('activity_action', $selectActionId['action_id']);
                                            $action->type = 'siteevent_post';
                                            $action->object_id = $siteevent->getIdentity();
                                            $action->object_type = $siteevent->getType();
                                            $action->save();
                                            $actionsTable->resetActivityBindings($action);
                                        }
                                    }
                                    //END FETCH ACTIONS
                                    //START FETCH ACTIONS
                                    $selectActionId = $actionsTable->select()
                                            ->from($actionsTableName, 'action_id')
                                            ->where('object_type = ?', 'event')
                                            ->where('object_id = ?', $event_id)
                                            ->where('type =?', 'sitetagcheckin_post');
                                    $selectActionIds = $actionsTable->fetchAll($selectActionId);
                                    if ($selectActionIds) {
                                        foreach ($selectActionIds as $selectActionId) {
                                            $action = Engine_Api::_()->getItem('activity_action', $selectActionId['action_id']);
                                            $action->type = 'sitetagcheckin_post';
                                            $action->object_id = $siteevent->getIdentity();
                                            $action->object_type = $siteevent->getType();
                                            $action->save();
                                            $actionsTable->resetActivityBindings($action);
                                        }
                                    }
                                    //END FETCH ACTIONS
                                    //START FETCH ACTIONS
                                    $selectActionId = $actionsTable->select()
                                            ->from($actionsTableName, 'action_id')
                                            ->where('object_type = ?', 'event')
                                            ->where('object_id = ?', $event_id)
                                            ->where('type =?', 'like_event');
                                    $selectActionIds = $actionsTable->fetchAll($selectActionId);
                                    if ($selectActionIds) {
                                        foreach ($selectActionIds as $selectActionId) {
                                            $action = Engine_Api::_()->getItem('activity_action', $selectActionId['action_id']);
                                            $action->type = 'like_siteevent_event';
                                            $action->object_id = $siteevent->getIdentity();
                                            $action->object_type = $siteevent->getType();
                                            $action->save();
                                            $actionsTable->resetActivityBindings($action);
                                        }
                                    }
                                    //END FETCH ACTIONS
                                    //UPDATE EVENT TYPE 
                                    $attachmentsTable->update(array('type' => $siteevent->getType(), 'id' => $siteevent->getIdentity()), array('type = ?' => 'event', 'id =?' => $event_id));

                                    $conversationTable->update(array('resource_type' => $siteevent->getType(), 'resource_id' => $siteevent->getIdentity()), array('resource_type = ?' => 'event', 'resource_id =?' => $event_id));
                                }

                                $row = $siteeventOtherinfoTable->getOtherinfo($siteevent->getIdentity());
                                $overview = '';
                                if (empty($row)) {
                                    $siteeventOtherinfoTable->insert(array(
                                        'event_id' => $siteevent->getIdentity(),
                                            // 'host_params' => json_encode($hostInfo),
                                    )); //COMMIT
                                }

                                //INSERT IN OCCURENCE TABLE
                                $viewer = Engine_Api::_()->user()->getViewer();
                                $row_occurrence = $siteeventOccurrencesTable->createRow();
                                $oldTz = date_default_timezone_get();
                                date_default_timezone_set($viewer->timezone);
                                date_default_timezone_set($oldTz);
                                $row_occurrence->event_id = $siteevent->getIdentity();
                                $row_occurrence->starttime = date("Y-m-d H:i:s", strtotime($event->starttime));
                                $row_occurrence->endtime = date("Y-m-d H:i:s", strtotime($event->endtime));
                                $row_occurrence->save();
                                $occurrence_id = $row_occurrence->occurrence_id;
                                $row_occurrence->starttime = date("Y-m-d H:i:s", strtotime($event->starttime));
                                $row_occurrence->endtime = date("Y-m-d H:i:s", strtotime($event->endtime));
                                $row_occurrence->save();
                                //EVENT DOCUMENT IMPORT WORK
                                if (!empty($siteeventDocumentEnabled) && !empty($eventdocumentEnabled)) {
                                    $this->importDocuments($event, $siteevent, $activity_event);
                                }

                                //GET EVENT MEMBERS
                                $eventMembers = $eventMembershipTable->fetchAll(array('resource_id = ?' => $event_id));
                                foreach ($eventMembers as $members) {
                                    $row_membership = $siteeventMembershipTable->createRow();
                                    $row_membership->resource_id = $siteevent->getIdentity();
                                    $row_membership->user_id = $members['user_id'];
                                    $row_membership->active = $members['active'];
                                    $row_membership->resource_approved = $members['resource_approved'];
                                    $row_membership->user_approved = $members['user_approved'];
                                    $row_membership->message = $members['message'];
                                    $row_membership->rsvp = $members['rsvp'];
                                    $row_membership->title = $members['title'];
                                    $row_membership->occurrence_id = $occurrence_id;
                                    $row_membership->save();
                                }

                                //START FETCH ACTIONS
                                if ($activity_event) {
                                    $selectActionId = $actionsTable->select()
                                            ->from($actionsTableName, 'action_id')
                                            ->where('object_type = ?', 'event')
                                            ->where('object_id = ?', $event_id)
                                            ->where('type =?', 'event_join');
                                    $selectActionIds = $eventMembershipTable->fetchAll($selectActionId);
                                    if ($selectActionIds) {
                                        foreach ($selectActionIds as $selectActionId) {
                                            $action = Engine_Api::_()->getItem('activity_action', $selectActionId['action_id']);
                                            $action->type = 'siteevent_join';
                                            $action->object_id = $siteevent->getIdentity();
                                            $action->object_type = $siteevent->getType();
                                            $action->params = '{"occurrence_id":"' . $occurrence_id . '"}';
                                            $action->save();
                                            $actionsTable->resetActivityBindings($action);
                                        }
                                    }
                                }
                                //END FETCH ACTIONS

                                $eventTopicSelect = $eventTopicTable->select()
                                        ->from($eventTopicTableName)
                                        ->where('event_id = ?', $event_id);
                                $eventTopicSelectDatas = $eventTopicTable->fetchAll($eventTopicSelect);
                                if (!empty($eventTopicSelectDatas)) {
                                    $eventTopicSelectDatas = $eventTopicSelectDatas->toArray();

                                    foreach ($eventTopicSelectDatas as $eventTopicSelectData) {
                                        $siteeventTopic = $siteeventTopicTable->createRow();
                                        $siteeventTopic->event_id = $siteevent->getIdentity();
                                        $siteeventTopic->user_id = $eventTopicSelectData['user_id'];
                                        $siteeventTopic->title = $eventTopicSelectData['title'];
                                        $siteeventTopic->sticky = $eventTopicSelectData['sticky'];
                                        $siteeventTopic->closed = $eventTopicSelectData['closed'];
                                        $siteeventTopic->view_count = $eventTopicSelectData['view_count'];
                                        $siteeventTopic->lastpost_id = $eventTopicSelectData['lastpost_id'];
                                        $siteeventTopic->lastposter_id = $eventTopicSelectData['lastposter_id'];
                                        $siteeventTopic->creation_date = $eventTopicSelectData['creation_date'];
                                        $siteeventTopic->modified_date = $eventTopicSelectData['modified_date'];
                                        $siteeventTopic->save();

                                        $siteeventTopic->creation_date = $eventTopicSelectData['creation_date'];
                                        $siteeventTopic->modified_date = $eventTopicSelectData['modified_date'];
                                        $siteeventTopic->save();
                                        //UPDATE TOPIC TYPE 
                                        $attachmentsTable->update(array('type' => 'siteevent_topic', 'id' => $siteeventTopic->getIdentity()), array('type = ?' => 'event_topic', 'id =?' => $eventTopicSelectData['topic_id']));
                                        if ($activity_event) {
                                            $selectActionId = $actionsTable->select()
                                                    ->from($actionsTableName, 'action_id')
                                                    ->where('object_type = ?', 'event_topic')
                                                    ->where('object_id =?', $eventTopicSelectData['topic_id'])
                                                    ->where('type =?', 'event_topic_create');
                                            $selectActions = $actionsTable->fetchAll($selectActionId);
                                            foreach ($selectActions as $selectAction) {
                                                $action = Engine_Api::_()->getItem('activity_action', $selectAction['action_id']);
                                                if ($action) {
                                                    $action->type = 'siteevent_topic_create';
                                                    $action->object_type = 'siteevent_event';
                                                    $action->object_id = $siteevent->getIdentity();
                                                    $action->save();
                                                    $actionsTable->resetActivityBindings($action);
                                                }
                                            }
                                        }

                                        //START FETCH TOPIC POST'S
                                        $eventPostSelect = $eventPostTable->select()
                                                ->from($eventPostTableName)
                                                ->where('topic_id = ?', $eventTopicSelectData['topic_id'])
                                                ->where('event_id = ?', $event_id);
                                        $eventPostSelectDatas = $eventPostTable->fetchAll($eventPostSelect);
                                        if (!empty($eventPostSelectDatas)) {
                                            $eventPostSelectDatas = $eventPostSelectDatas->toArray();

                                            foreach ($eventPostSelectDatas as $eventPostSelectData) {
                                                $siteeventPost = $siteeventPostTable->createRow();
                                                $siteeventPost->topic_id = $siteeventTopic->topic_id;
                                                $siteeventPost->event_id = $siteevent->event_id;
                                                $siteeventPost->user_id = $eventPostSelectData['user_id'];
                                                $siteeventPost->body = $eventPostSelectData['body'];
                                                $siteeventPost->creation_date = $eventPostSelectData['creation_date'];
                                                $siteeventPost->modified_date = $eventPostSelectData['modified_date'];
                                                $siteeventPost->save();
                                                $siteeventPost->creation_date = $eventPostSelectData['creation_date'];
                                                $siteeventPost->modified_date = $eventPostSelectData['modified_date'];
                                                $siteeventPost->save();
                                                $attachmentsTable->update(array('type' => 'siteevent_post', 'id' => $siteeventTopic->getIdentity()), array('type = ?' => 'event_post', 'id =?' => $eventPostSelectData['post_id']));
                                                if ($activity_event) {
                                                    $selectActionId = $actionsTable->select()
                                                            ->from($actionsTableName, 'action_id')
                                                            ->where('object_type = ?', 'event_topic')
                                                            ->where('object_id =?', $eventPostSelectData['topic_id'])
                                                            ->where('type =?', 'event_topic_reply');
                                                    $selectActions = $actionsTable->fetchAll($selectActionId);
                                                    foreach ($selectActions as $selectAction) {
                                                        $action = Engine_Api::_()->getItem('activity_action', $selectAction['action_id']);
                                                        if ($action) {
                                                            $action->type = 'siteevent_topic_reply';
                                                            $action->object_type = 'siteevent_event';
                                                            $action->object_id = $siteevent->getIdentity();
                                                            $action->save();
                                                            $actionsTable->resetActivityBindings($action);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        //END FETCH TOPIC POST'S

                                        $siteeventTopic->post_count = $eventTopicSelectData['post_count'];
                                        $siteeventTopic->save();

                                        //START FETCH TOPIC WATCH
                                        $eventTopicWatchDatas = $eventTopicWatchesTable->fetchAll(array('resource_id = ?' => $event_id));
                                        foreach ($eventTopicWatchDatas as $eventTopicWatchData) {
                                            if (!empty($eventTopicWatchData)) {
                                                $siteeventTopicWatchSelect = $siteeventTopicWatchesTable->select()
                                                        ->from($siteeventTopicWatchesTableName)
                                                        ->where('resource_id = ?', $siteeventTopic->event_id)
                                                        ->where('topic_id = ?', $siteeventTopic->topic_id)
                                                        ->where('user_id = ?', $eventTopicWatchData->user_id);
                                                $siteeventTopicWatchSelectDatas = $siteeventTopicWatchesTable->fetchRow($siteeventTopicWatchSelect);

                                                if (empty($siteeventTopicWatchSelectDatas)) {
                                                    $siteeventTopicWatchesTable->insert(array(
                                                        'resource_id' => $siteeventTopic->event_id,
                                                        'topic_id' => $siteeventTopic->topic_id,
                                                        'user_id' => $eventTopicWatchData->user_id,
                                                        'watch' => $eventTopicWatchData->watch
                                                    ));
                                                }
                                            }
                                        }
                                        //END FETCH TOPIC WATCH
                                    }
                                }

                                //START FETCH LIKES
                                $selectLike = $likeTable->select()
                                        ->from($likeTableName, 'like_id')
                                        ->where('resource_type = ?', 'event')
                                        ->where('resource_id = ?', $event_id);
                                $selectLikeDatas = $likeTable->fetchAll($selectLike);
                                foreach ($selectLikeDatas as $selectLikeData) {
                                    $like = Engine_Api::_()->getItem('core_like', $selectLikeData->like_id);
                                    $newLikeEntry = $likeTable->createRow();
                                    $newLikeEntry->resource_type = 'siteevent_event';
                                    $newLikeEntry->resource_id = $siteevent->getIdentity();
                                    $newLikeEntry->poster_type = 'user';
                                    $newLikeEntry->poster_id = $like->poster_id;
                                    $newLikeEntry->creation_date = $like->creation_date;
                                    $newLikeEntry->save();

                                    $newLikeEntry->creation_date = $like->creation_date;
                                    $newLikeEntry->save();
                                }
                                //END FETCH LIKES
                                //START FETCH PHOTO DATA
                                $selectEventPhoto = $eventPhotoTable->select()
                                        ->from($eventPhotoTableName)
                                        ->where('event_id = ?', $event_id);
                                $eventPhotoDatas = $eventPhotoTable->fetchAll($selectEventPhoto);

                                if (!empty($eventPhotoDatas)) {

                                    $eventPhotoDatas = $eventPhotoDatas->toArray();

                                    if (empty($event->photo_id)) {
                                        foreach ($eventPhotoDatas as $eventPhotoData) {
                                            $event->photo_id = $eventPhotoData['photo_id'];
                                            break;
                                        }
                                    }

                                    if (!empty($event->photo_id)) {
                                        $eventPhotoData = $eventPhotoTable->fetchRow(array('file_id = ?' => $event->photo_id));
                                        if (!empty($eventPhotoData)) {
                                            $storageData = $storageTable->fetchRow(array('file_id = ?' => $eventPhotoData->file_id));

                                            if (!empty($storageData) && !empty($storageData->storage_path)) {
                                                if (is_string($storageData->storage_path) && file_exists($storageData->storage_path))
                                                    $siteevent->setPhoto($storageData->storage_path);

                                                $album_id = $siteeventAlbumTable->update(array('photo_id' => $siteevent->photo_id), array('event_id = ?' => $siteevent->event_id));

                                                $siteeventProfilePhoto = Engine_Api::_()->getDbTable('photos', 'siteevent')->fetchRow(array('file_id = ?' => $siteevent->photo_id));
                                                if (!empty($siteeventProfilePhoto)) {
                                                    $siteeventProfilePhotoId = $siteeventProfilePhoto->photo_id;
                                                } else {
                                                    $siteeventProfilePhotoId = $siteevent->photo_id;
                                                }

                                                //START FETCH LIKES
                                                $selectLike = $likeTable->select()
                                                        ->from($likeTableName, 'like_id')
                                                        ->where('resource_type = ?', 'event_photo')
                                                        ->where('resource_id = ?', $event->photo_id);
                                                $selectLikeDatas = $likeTable->fetchAll($selectLike);
                                                foreach ($selectLikeDatas as $selectLikeData) {
                                                    $like = Engine_Api::_()->getItem('core_like', $selectLikeData->like_id);
                                                    $newLikeEntry = $likeTable->createRow();
                                                    $newLikeEntry->resource_type = 'siteevent_photo';
                                                    $newLikeEntry->resource_id = $siteeventProfilePhotoId;
                                                    $newLikeEntry->poster_type = 'user';
                                                    $newLikeEntry->poster_id = $like->poster_id;
                                                    $newLikeEntry->creation_date = $like->creation_date;
                                                    $newLikeEntry->save();

                                                    $newLikeEntry->creation_date = $like->creation_date;
                                                    $newLikeEntry->save();
                                                }
                                                //END FETCH LIKES
                                                //START FETCH COMMENTS
                                                $selectLike = $commentTable->select()
                                                        ->from($commentTableName, 'comment_id')
                                                        ->where('resource_type = ?', 'event_photo')
                                                        ->where('resource_id = ?', $event->photo_id);
                                                $selectLikeDatas = $commentTable->fetchAll($selectLike);
                                                foreach ($selectLikeDatas as $selectLikeData) {
                                                    $comment = Engine_Api::_()->getItem('core_comment', $selectLikeData->comment_id);

                                                    $newLikeEntry = $commentTable->createRow();
                                                    $newLikeEntry->resource_type = 'siteevent_photo';
                                                    $newLikeEntry->resource_id = $siteeventProfilePhotoId;
                                                    $newLikeEntry->poster_type = 'user';
                                                    $newLikeEntry->poster_id = $comment->poster_id;
                                                    $newLikeEntry->body = $comment->body;
                                                    $newLikeEntry->creation_date = $comment->creation_date;
                                                    $newLikeEntry->like_count = $comment->like_count;
                                                    $newLikeEntry->save();

                                                    $newLikeEntry->creation_date = $comment->creation_date;
                                                    $newLikeEntry->save();
                                                }
                                                //END FETCH COMMENTS
                                                //START FETCH TAGGER DETAIL
                                                $selectTagmaps = $tagmapsTable->select()
                                                        ->from($tagmapsTableName, 'tagmap_id')
                                                        ->where('resource_type = ?', 'event_photo')
                                                        ->where('resource_id = ?', $event->photo_id);
                                                $selectTagmapsDatas = $tagmapsTable->fetchAll($selectTagmaps);
                                                foreach ($selectTagmapsDatas as $selectTagmapsData) {
                                                    $tagMap = Engine_Api::_()->getItem('core_tag_map', $selectTagmapsData->tagmap_id);

                                                    $newTagmapEntry = $tagmapsTable->createRow();
                                                    $newTagmapEntry->resource_type = 'siteevent_photo';
                                                    $newTagmapEntry->resource_id = $siteeventProfilePhotoId;
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

                                        $fetchDefaultAlbum = $siteeventAlbumTable->fetchRow(array('event_id = ?' => $siteevent->event_id));
                                        if (!empty($fetchDefaultAlbum)) {

                                            $selectEventPhoto = $eventPhotoTable->select()
                                                    ->from($eventPhotoTable->info('name'))
                                                    ->where('event_id = ?', $event_id);
                                            $eventPhotoDatas = $eventPhotoTable->fetchAll($selectEventPhoto);

                                            $order = 999;
                                            foreach ($eventPhotoDatas as $eventPhotoData) {

                                                if ($eventPhotoData['file_id'] != $event->photo_id) {
                                                    $params = array(
                                                        'collection_id' => $fetchDefaultAlbum->album_id,
                                                        'album_id' => $fetchDefaultAlbum->album_id,
                                                        'event_id' => $siteevent->event_id,
                                                        'user_id' => $eventPhotoData['user_id'],
                                                        'order' => $order
                                                    );

                                                    $storageData = $storageTable->fetchRow(array('file_id = ?' => $eventPhotoData['file_id']));
                                                    if (!empty($storageData) && !empty($storageData->storage_path)) {
                                                        $file = array();
                                                        $file['tmp_name'] = $storageData->storage_path;
                                                        $path_array = explode('/', $file['tmp_name']);
                                                        $file['name'] = end($path_array);

                                                        $siteeventPhoto = Engine_Api::_()->siteevent()->createPhoto($params, $file);
                                                        if (!empty($siteeventPhoto)) {

                                                            $order++;

                                                            //START FETCH LIKES
                                                            $selectLike = $likeTable->select()
                                                                    ->from($likeTableName, 'like_id')
                                                                    ->where('resource_type = ?', 'event_photo')
                                                                    ->where('resource_id = ?', $eventPhotoData['photo_id']);
                                                            $selectLikeDatas = $likeTable->fetchAll($selectLike);
                                                            foreach ($selectLikeDatas as $selectLikeData) {
                                                                $like = Engine_Api::_()->getItem('core_like', $selectLikeData->like_id);

                                                                $newLikeEntry = $likeTable->createRow();
                                                                $newLikeEntry->resource_type = 'siteevent_photo';
                                                                $newLikeEntry->resource_id = $siteeventPhoto->photo_id;
                                                                $newLikeEntry->poster_type = 'user';
                                                                $newLikeEntry->poster_id = $like->poster_id;
                                                                $newLikeEntry->creation_date = $like->creation_date;
                                                                $newLikeEntry->save();

                                                                $newLikeEntry->creation_date = $like->creation_date;
                                                                $newLikeEntry->save();
                                                            }
                                                            //END FETCH LIKES
                                                            //START FETCH COMMENTS
                                                            $selectLike = $commentTable->select()
                                                                    ->from($commentTableName, 'comment_id')
                                                                    ->where('resource_type = ?', 'event_photo')
                                                                    ->where('resource_id = ?', $eventPhotoData['photo_id']);
                                                            $selectLikeDatas = $commentTable->fetchAll($selectLike);
                                                            foreach ($selectLikeDatas as $selectLikeData) {
                                                                $comment = Engine_Api::_()->getItem('core_comment', $selectLikeData->comment_id);

                                                                $newLikeEntry = $commentTable->createRow();
                                                                $newLikeEntry->resource_type = 'siteevent_photo';
                                                                $newLikeEntry->resource_id = $siteeventPhoto->photo_id;
                                                                $newLikeEntry->poster_type = 'user';
                                                                $newLikeEntry->poster_id = $comment->poster_id;
                                                                $newLikeEntry->body = $comment->body;
                                                                $newLikeEntry->creation_date = $comment->creation_date;
                                                                $newLikeEntry->like_count = $comment->like_count;
                                                                $newLikeEntry->save();

                                                                $newLikeEntry->creation_date = $comment->creation_date;
                                                                $newLikeEntry->save();
                                                            }
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
                                                                $newTagmapEntry->resource_type = 'siteevent_photo';
                                                                $newTagmapEntry->resource_id = $siteeventPhoto->photo_id;
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
                                }

                                //START STYLES
                                $selectStyles = $stylesTable->select()
                                        ->from($stylesTableName, 'style')
                                        ->where('type = ?', 'event')
                                        ->where('id = ?', $event_id);
                                $selectStyleDatas = $stylesTable->fetchRow($selectStyles);
                                if (!empty($selectStyleDatas)) {
                                    $selectSiteeventStyles = $stylesTable->select()
                                            ->from($stylesTableName, 'style')
                                            ->where('type = ?', 'siteevent_event')
                                            ->where('id = ?', $siteevent->getIdentity());
                                    $selectSiteeventStyleDatas = $stylesTable->fetchRow($selectSiteeventStyles);
                                    if (empty($selectSiteeventStyleDatas)) {
                                        //CREATE
                                        $stylesTable->insert(array(
                                            'type' => 'siteevent_event',
                                            'id' => $siteevent->getIdentity(),
                                            'style' => $selectStyleDatas->style
                                        ));
                                    }
                                }
                                //END STYLES
                                //START UPDATE TOTAL LIKES IN SITEEVENT TABLE
                                $selectLikeCount = $likeTable->select()
                                        ->from($likeTableName, array('COUNT(*) AS like_count'))
                                        ->where('resource_type = ?', 'event')
                                        ->where('resource_id = ?', $event_id);
                                $selectLikeCounts = $likeTable->fetchAll($selectLikeCount);
                                if (!empty($selectLikeCounts)) {
                                    $selectLikeCounts = $selectLikeCounts->toArray();
                                    $siteevent->like_count = $selectLikeCounts[0]['like_count'];
                                    $siteevent->save();
                                }
                                //END UPDATE TOTAL LIKES IN SITEEVENT TABLES

                                if ($activity_event) {
                                    $selectActionId = $actionsTable->select()
                                            ->from($actionsTableName, 'action_id')
                                            ->where('object_type = ?', 'event')
                                            ->where('object_id = ?', $event_id)
                                            ->where('type =?', 'event_photo_upload')
                                            ->query()
                                            ->fetchColumn();
                                    if ($selectActionId) {
                                        $action = Engine_Api::_()->getItem('activity_action', $selectActionId);
                                        $action->type = 'siteevent_photo_upload';
                                        $action->object_id = $siteevent->getIdentity();
                                        $action->object_type = $siteevent->getType();
                                        $action->params = array_merge($action->params, array('title' => $siteevent->getTitle()));
                                        $action->save();
                                        $actionsTable->resetActivityBindings($action);
                                    }
                                }
                            }

                            //CREATE LOG ENTRY IN LOG FILE
                            if (file_exists(APPLICATION_PATH . '/temporary/log/EventToSiteeventImport.log')) {
                                $myFile = APPLICATION_PATH . '/temporary/log/EventToSiteeventImport.log';
                                $error = Zend_Registry::get('Zend_Translate')->_("can't open file");
                                $fh = fopen($myFile, 'a') or die($error);
                                $current_time = date('D, d M Y H:i:s T');
                                $siteevent_title = $siteevent->title;
                                $stringData = $this->view->translate('Event with ID ') . $event_id . $this->view->translate(' is successfully imported into a Advanced Event with ID ') . $siteevent->event_id . $this->view->translate(' at ') . $current_time . $this->view->translate(". Title of that Event is '") . $siteevent_title . "'.\n\n";
                                fwrite($fh, $stringData);
                                fclose($fh);
                            }

                            $db->commit();
                            $this->view->event_assigned_previous_id = $event_id;
                        } catch (Exception $e) {
                            $db->rollback();
                            throw($e);
                        }
                        if ($next_import_count >= 100) {
                            $this->_redirect("admin/siteevent/importevent/index?start_import=1&module=event&recall=1&activity_event=$activity_event");
                        }
                    }
                } else {
                    if ($_GET['recall']) {
                        echo json_encode(array('success' => 1));
                        exit();
                    }
                }
            }
        } else if ($yneventEnabled) {
            //GET EVENT TABLES 
            $yneventTable = Engine_Api::_()->getDbTable('events', 'ynevent');
            $yneventTableName = $yneventTable->info('name');

            //GET EVENT CATEGORIES TABLE
            $yneventCategoryTable = Engine_Api::_()->getDbtable('categories', 'ynevent');
            $yneventCategoryTableName = $yneventCategoryTable->info('name');

            //GET EVENT MEMBERSHIP TABLE
            $yneventMembershipTable = Engine_Api::_()->getDbtable('membership', 'ynevent');

            //GET EVENT TOPIC TABLE
            $yneventTopicTable = Engine_Api::_()->getDbtable('topics', 'ynevent');
            $yneventTopicTableName = $yneventTopicTable->info('name');

            //GET EVENT POST TABLE
            $yneventPostTable = Engine_Api::_()->getDbtable('posts', 'ynevent');
            $yneventPostTableName = $yneventPostTable->info('name');

            //GET EVENT PHOTO TABLE
            $yneventPhotoTable = Engine_Api::_()->getDbtable('photos', 'ynevent');
            $yneventPhotoTableName = $yneventPhotoTable->info('name');

            //GET EVENT TOPICWATCHES  TABLE
            $yneventTopicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'ynevent');
            $yneventTopicWatchesTableName = $yneventTopicWatchesTable->info('name');

            //GET FOLLOW TABLE
            $followEventTable = Engine_Api::_()->getDbtable('follow', 'ynevent');
            $followEventTableName = $followEventTable->info('name');

            //GET SEAOCORE FOLLOW TABLE
            $followSeaocoreTable = Engine_Api::_()->getDbtable('follows', 'seaocore');
            $followSeaocoreTableName = $followSeaocoreTable->info('name');

            $eventRatingTable = Engine_Api::_()->getDbtable('ratings', 'ynevent');
            $eventRatingTableName = $eventRatingTable->info('name');

            $siteeventRatingTable = Engine_Api::_()->getDbtable('ratings', 'siteevent');
            $siteeventRatingTableName = $siteeventRatingTable->info('name');

            //ADD NEW COLUMN IN EVENT TABLE
            $db = Engine_Db_Table::getDefaultAdapter();
            $is_event_import = $db->query("SHOW COLUMNS FROM engine4_event_events LIKE 'is_event_import'")->fetch();
            if (empty($is_event_import)) {
                $run_query = $db->query("ALTER TABLE `engine4_event_events` ADD is_event_import TINYINT( 2 ) NOT NULL DEFAULT '0'");
            }

            //START IF IMPORTING IS BREAKED BY SOME REASON
            $selectEvents = $yneventTable->select()
                    ->from($yneventTableName, 'event_id')
                    ->where('is_event_import != ?', 1)
                    //->group('repeat_group')
                    ->order('event_id ASC'); //die;
            $yneventDatas = $yneventTable->fetchAll($selectEvents);

            $this->view->first_event_id = $first_event_id = 0;
            $this->view->last_event_id = $last_event_id = 0;
            $this->view->repeatTypeEvents = 0;
            if (!$this->view->siteeventRepeatEnabled) {
                $this->view->repeatTypeEvents = $repeatTypeEvents = $yneventTable->select()
                        ->from($yneventTableName, 'event_id')
                        ->where('is_event_import != ?', 1)
                        ->where('repeat_type !=?', 0)
                        ->query()
                        ->fetchColumn();
            }
            if (!empty($yneventDatas)) {

                $flag_first_event_id = 1;

                foreach ($yneventDatas as $yneventData) {

                    if ($flag_first_event_id == 1) {
                        $this->view->first_event_id = $first_event_id = $yneventData->event_id;
                    }
                    $flag_first_event_id++;

                    $this->view->last_event_id = $last_event_id = $yneventData->event_id;
                }

                if (isset($_GET['event_assigned_previous_id'])) {
                    $this->view->event_assigned_previous_id = $ynevent_assigned_previous_id = $_GET['event_assigned_previous_id'];
                } else {
                    $this->view->event_assigned_previous_id = $ynevent_assigned_previous_id = $first_event_id;
                }
            }

            //START IMPORTING IF REQUESTED
            if (isset($_GET['start_import']) && $_GET['start_import'] == 1 && $_GET['module'] == 'event') {

                //ACTIVITY FEED IMPORT
                $activity_event = $this->_getParam('activity_event');

                //START FETCH CATEGORY WORK
                $selectSiteeventCategory = $siteeventCategoryTable->select()
                        ->from($siteeventCategoryTableName, 'category_name')
                        ->where('category_name != ?', '')
                        ->where('cat_dependency = ?', 0);
                $siteeventCategoryDatas = $siteeventCategoryTable->fetchAll($selectSiteeventCategory);
                if (!empty($siteeventCategoryDatas)) {
                    $siteeventCategoryDatas = $siteeventCategoryDatas->toArray();
                }

                $siteeventCategoryInArrayData = array();
                foreach ($siteeventCategoryDatas as $siteeventCategoryData) {
                    $siteeventCategoryInArrayData[] = $siteeventCategoryData['category_name'];
                }

                $selectEventCategory = $yneventCategoryTable->select()
                        ->where('title != ?', '')
                        ->from($yneventCategoryTableName);
                $yneventCategoryDatas = $yneventCategoryTable->fetchAll($selectEventCategory);

                if (!empty($yneventCategoryDatas)) {
                    $yneventCategoryDatas = $yneventCategoryDatas->toArray();

                    foreach ($yneventCategoryDatas as $yneventCategoryData) {
                        if (!in_array($yneventCategoryData['title'], $siteeventCategoryInArrayData)) {
                            $newCategory = $siteeventCategoryTable->createRow();
                            $newCategory->category_name = $yneventCategoryData['title'];
                            $newCategory->cat_dependency = 0;
                            $newCategory->cat_order = 9999;
                            $newCategory->save();
                        }
                    }
                }

                $other_category_id = $siteeventCategoryTable->select()
                        ->from($siteeventCategoryTableName, 'category_id')
                        ->where('category_name = ?', 'Others')
                        ->where('cat_dependency = ?', 0)
                        ->query()
                        ->fetchColumn();

                if (empty($other_category_id)) {
                    $newCategory = $siteeventCategoryTable->createRow();
                    $newCategory->category_name = 'Others';
                    $newCategory->cat_dependency = 0;
                    $newCategory->cat_order = 9999;
                    $other_category_id = $newCategory->save();
                }

                // CREATE CATEGORIES DEFAULT PAGES
                $categoryIds = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategoriesArray(array('cat_dependency' => 0, 'subcat_dependency' => 0));
                Engine_Api::_()->siteevent()->categoriesPageCreate($categoryIds);

                //START EVENT IMPORTING
                $selectEvents = $yneventTable->select()
                        ->where('event_id >= ?', $ynevent_assigned_previous_id)
                        ->from($yneventTableName, 'event_id')
                        ->where('is_event_import != ?', 1)
                        //->group('repeat_group')
                        ->order('event_id ASC');

                $yneventDatas = $yneventTable->fetchAll($selectEvents);

                $next_import_count = 0;
                $yneventDatasArray = $yneventDatas->toArray();

                if ($yneventDatasArray) {
                    foreach ($yneventDatas as $yneventData) {
                        $ynevent_id = $yneventData->event_id;

                        $db = Engine_Db_Table::getDefaultAdapter();
                        $db->beginTransaction();
                        try {
                            if (!empty($ynevent_id)) {
                                $ynevent = Engine_Api::_()->getItem('ynevent_event', $ynevent_id);
                                $siteevent = $siteeventTable->createRow();
                                $siteevent->title = $ynevent->title;
                                $siteevent->body = $ynevent->description;
                                $siteevent->owner_id = $ynevent->user_id;
                                $siteevent->parent_type = $ynevent->parent_type;
                                $siteevent->parent_id = $ynevent->parent_id;
                                //START FETCH LIST CATEGORY AND SUB-CATEGORY
                                if (!empty($ynevent->category_id)) {
                                    $yneventCategory = $yneventCategoryTable->fetchRow(array('category_id = ?' => $ynevent->category_id));
                                    if (!empty($yneventCategory)) {
                                        $yneventCategoryName = $yneventCategory->title;
                                        if (!empty($yneventCategoryName)) {
                                            $siteeventCategory = $siteeventCategoryTable->fetchRow(array('category_name = ?' => $yneventCategoryName, 'cat_dependency = ?' => 0));
                                            if (!empty($siteeventCategory)) {
                                                $siteeventCategoryId = $siteevent->category_id = $siteeventCategory->category_id;
                                            }
                                        }
                                    }
                                } else {
                                    $siteevent->category_id = $other_category_id;
                                }
                                //END FETCH LIST CATEGORY AND SUB-CATEGORY
                                $siteevent->creation_date = $ynevent->creation_date;
                                $siteevent->modified_date = $ynevent->modified_date;
                                $siteevent->save();
                                $siteevent->creation_date = $ynevent->creation_date;
                                $siteevent->modified_date = $ynevent->modified_date;

                                $siteevent->view_count = 1;
                                if ($ynevent->view_count > 0) {
                                    $siteevent->view_count = $ynevent->view_count;
                                }
                                $siteevent->search = $ynevent->search;
                                $siteevent->approval = $ynevent->approval;
                                $siteevent->member_count = $ynevent->member_count;
                                $siteevent->location = $ynevent->location;
                                $siteevent->networks_privacy = $ynevent->networks_privacy;
                                //$siteevent->invite = $ynevent->invite;
                                $siteevent->save();

                                $siteevent->approved = 1;
                                $siteevent->featured = $ynevent->featured;
                                $siteevent->sponsored = 0;
                                $siteevent->newlabel = 0;
                                $siteevent->approved_date = date('Y-m-d H:i:s');
                                $siteevent->price = $ynevent->price;

                                //FATCH SITEEVENT CATEGORIES
                                $categoryIdsArray = array();
                                $categoryIdsArray[] = $siteevent->category_id;
                                $categoryIdsArray[] = $siteevent->subcategory_id;
                                $categoryIdsArray[] = $siteevent->subsubcategory_id;
                                $siteevent->profile_type = $siteeventCategoryTable->getProfileType($categoryIdsArray, 0, 'profile_type');
                                if ($ynevent->host) {
                                    $organizer = $organizersTable->getOrganizer(array('creator_id' => $ynevent->user_id, 'equal_title' => $ynevent->host));
                                    if (empty($organizer)) {
                                        $organizer = $organizersTable->createRow();
                                        $organizer->title = $ynevent->host;
                                        $organizer->creator_id = $ynevent->user_id;
                                        $organizer->save();
                                    }
                                    $siteevent->host_type = $organizer->getType();
                                    $siteevent->host_id = $organizer->getIdentity();
                                } else {
                                    $siteevent->host_type = $ynevent->getOwner()->getType();
                                    $siteevent->host_id = $ynevent->user_id;
                                }

                                if(isset($siteevent->capacity) && isset($ynevent->capacity)) {
                                    $siteevent->capacity = $ynevent->capacity;
                                }
                                $siteevent->save();
                                $siteevent->setLocation();
                                $siteevent->repeat_params = '';
                                $ynevent->is_event_import = 1;
                                $ynevent->save();
                                $next_import_count++;
                                //END GET DATA FROM EVENT

                                $leaderList = $siteevent->getLeaderList();

                                //START FETCH PRIVACY
                                $auth = Engine_Api::_()->authorization()->context;
                                $values = array();
                                //START VIEW PRIVACY
                                $rolesEvents = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                                foreach ($rolesEvents as $rolesEvent) {
                                    if ($auth->isAllowed($ynevent, $rolesEvent, 'view')) {
                                        $values['auth_view'] = $rolesEvent;
                                    }
                                }
                                $viewMax = array_search($values['auth_view'], $rolesEvents);
                                $rolesSiteevents = array('leader', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                                foreach ($rolesSiteevents as $i => $rolesSiteevent) {
                                    if ($rolesSiteevent === 'leader') {
                                        $rolesSiteevent = $leaderList;
                                    }
                                    $auth->setAllowed($siteevent, $rolesSiteevent, 'view', ($i <= $viewMax));
                                }
                                //END VIEW PRIVACY
                                //START COMMENT PRIVACY
                                $rolesEvents = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
                                foreach ($rolesEvents as $rolesEvent) {
                                    if ($auth->isAllowed($ynevent, $rolesEvent, 'comment')) {
                                        $values['auth_comment'] = $rolesEvent;
                                    }
                                }
                                $commentMax = array_search($values['auth_comment'], $rolesEvents);
                                $rolesSiteevents = array('leader', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
                                foreach ($rolesSiteevents as $i => $rolesSiteevent) {
                                    if ($rolesSiteevent === 'leader') {
                                        $rolesSiteevent = $leaderList;
                                    }
                                    $auth->setAllowed($siteevent, $rolesSiteevent, 'comment', ($i <= $commentMax));
                                }
                                //END COMMENT PRIVACY
                                //START PHOTO PRIVACY
                                $rolesEvents = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
                                foreach ($rolesEvents as $rolesEvent) {
                                    if ($auth->isAllowed($ynevent, $rolesEvent, 'photo')) {
                                        $values['auth_photo'] = $rolesEvent;
                                    }
                                }
                                $photoMax = array_search($values['auth_photo'], $rolesEvents);
                                $rolesSiteevents = array('leader', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');

                                foreach ($rolesSiteevents as $i => $rolesSiteevent) {
                                    if ($rolesSiteevent === 'leader') {
                                        $rolesSiteevent = $leaderList;
                                    }
                                    $auth->setAllowed($siteevent, $rolesSiteevent, 'photo', ($i <= $photoMax));
                                }
                                //END PHOTO PRIVACY
                                //START TOPIC PRIVACY
                                $rolesEvents = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
                                foreach ($rolesEvents as $rolesEvent) {
                                    if ($auth->isAllowed($ynevent, $rolesEvent, 'comment')) {
                                        $values['auth_comment'] = $rolesEvent;
                                    }
                                }
                                $commentMax = array_search($values['auth_comment'], $rolesEvents);
                                $rolesSiteevents = array('leader', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
                                foreach ($rolesSiteevents as $i => $rolesSiteevent) {
                                    if ($rolesSiteevent === 'leader') {
                                        $rolesSiteevent = $leaderList;
                                    }
                                    $auth->setAllowed($siteevent, $rolesSiteevent, 'topic', ($i <= $commentMax));
                                }
                                //END TOPIC PRIVACY
                                //START VIDEO PRIVACY
                                $rolesEvents = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
                                foreach ($rolesEvents as $rolesEvent) {
                                    if ($auth->isAllowed($ynevent, $rolesEvent, 'video')) {
                                        $values['auth_video'] = $rolesEvent;
                                    }
                                }
                                $videoMax = array_search($values['auth_video'], $rolesEvents);
                                $rolesSiteevents = array('leader', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
                                foreach ($rolesSiteevents as $i => $rolesSiteevent) {
                                    if ($rolesSiteevent === 'leader') {
                                        $rolesSiteevent = $leaderList;
                                    }
                                    $auth->setAllowed($siteevent, $rolesSiteevent, 'video', ($i <= $videoMax));
                                }
                                //END VIDEO PRIVACY

                               //START POST PRIVACY
																if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
                                $rolesSiteevents = array('leader', 'member', 'like_member','owner_member', 'owner_member_member', 'owner_network', 'registered');
                                $postMax = array_search("member", $rolesSiteevents);
                                foreach ($rolesSiteevents as $i => $rolesSiteevent) {
                                    if ($rolesSiteevent === 'leader') {
                                        $rolesSiteevent = $leaderList;
                                    }
                                    $auth->setAllowed($siteevent, $rolesSiteevent, 'post', ($i <= $postMax));
                                }
																}
																//END POST PRIVACY

                                //START DOCUMENT PRIVACY
                                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventdocument')) {
                                    $rolesSiteevents = array('leader', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
                                    $documentMax = array_search("member", $rolesSiteevents);
                                    foreach ($rolesSiteevents as $i => $rolesSiteevent) {
                                        if ($rolesSiteevent === 'leader') {
                                            $rolesSiteevent = $leaderList;
                                        }
                                        $auth->setAllowed($siteevent, $rolesSiteevent, "document", ($i <= $documentMax));
                                    }

                                    $auth->setAllowed($siteevent, $leaderList, 'document.edit', 1);
                                }
                                //END DOCUMENT PRIVACY
                                //CREATE SOME AUTH STUFF FOR ALL LEADERS
                                $auth->setAllowed($siteevent, $leaderList, 'photo.edit', 1);
                                $auth->setAllowed($siteevent, $leaderList, 'topic.edit', 1);
                                $auth->setAllowed($siteevent, $leaderList, 'video.edit', 1);
                                $auth->setAllowed($siteevent, $leaderList, 'edit', 1);
                                $auth->setAllowed($siteevent, $leaderList, 'delete', 1);

                                //SET INVITE PRIVACY
                                $auth->setAllowed($siteevent, 'member', 'invite', $auth->isAllowed($ynevent, 'member', 'invite'));

                                //GENERATE ACITIVITY FEED
                                if ($activity_event) {
                                    //START FETCH ACTIONS
                                    $selectActionId = $actionsTable->select()
                                            ->from($actionsTableName, 'action_id')
                                            ->where('object_type = ?', 'event')
                                            ->where('object_id = ?', $ynevent_id)
                                            ->where('type =?', 'ynevent_create')
                                            ->query()
                                            ->fetchColumn();
                                    if ($selectActionId) {
                                        $action = Engine_Api::_()->getItem('activity_action', $selectActionId);
                                        $action->type = 'siteevent_new';
                                        $action->object_id = $siteevent->getIdentity();
                                        $action->object_type = $siteevent->getType();
                                        $action->save();
                                        $actionsTable->resetActivityBindings($action);
                                    }
                                    //END FETCH ACTIONS
                                    //START FETCH ACTIONS
                                    $selectActionId = $actionsTable->select()
                                            ->from($actionsTableName, 'action_id')
                                            ->where('object_type = ?', 'event')
                                            ->where('object_id = ?', $ynevent_id)
                                            ->where('type =?', 'post');
                                    $selectActionIds = $actionsTable->fetchAll($selectActionId);
                                    if ($selectActionIds) {
                                        foreach ($selectActionIds as $selectActionId) {
                                            $action = Engine_Api::_()->getItem('activity_action', $selectActionId['action_id']);
                                            $action->type = 'siteevent_post';
                                            $action->object_id = $siteevent->getIdentity();
                                            $action->object_type = $siteevent->getType();
                                            $action->save();
                                            $actionsTable->resetActivityBindings($action);
                                        }
                                    }
                                    //END FETCH ACTIONS
                                    //START FETCH ACTIONS
                                    $selectActionId = $actionsTable->select()
                                            ->from($actionsTableName, 'action_id')
                                            ->where('object_type = ?', 'event')
                                            ->where('object_id = ?', $ynevent_id)
                                            ->where('type =?', 'sitetagcheckin_post');
                                    $selectActionIds = $actionsTable->fetchAll($selectActionId);
                                    if ($selectActionIds) {
                                        foreach ($selectActionIds as $selectActionId) {
                                            $action = Engine_Api::_()->getItem('activity_action', $selectActionId['action_id']);
                                            $action->type = 'sitetagcheckin_post';
                                            $action->object_id = $siteevent->getIdentity();
                                            $action->object_type = $siteevent->getType();
                                            $action->save();
                                            $actionsTable->resetActivityBindings($action);
                                        }
                                    }
                                    //END FETCH ACTIONS
                                    //START FETCH ACTIONS
                                    $selectActionId = $actionsTable->select()
                                            ->from($actionsTableName, 'action_id')
                                            ->where('object_type = ?', 'event')
                                            ->where('object_id = ?', $ynevent_id)
                                            ->where('type =?', 'like_event');
                                    $selectActionIds = $actionsTable->fetchAll($selectActionId);
                                    if ($selectActionIds) {
                                        foreach ($selectActionIds as $selectActionId) {
                                            $action = Engine_Api::_()->getItem('activity_action', $selectActionId['action_id']);
                                            $action->type = 'like_siteevent_event';
                                            $action->object_id = $siteevent->getIdentity();
                                            $action->object_type = $siteevent->getType();
                                            $action->save();
                                            $actionsTable->resetActivityBindings($action);
                                        }
                                    }
                                    //END FETCH ACTIONS
                                    //UPDATE EVENT TYPE 
                                    $attachmentsTable->update(array('type' => $siteevent->getType(), 'id' => $siteevent->getIdentity()), array('type = ?' => 'event', 'id =?' => $ynevent_id));
                                    /*
                                      //UPDATE PHOTO TYPE
                                      $attachmentsTable->update(array('type' => 'siteevent_photo', 'id' => $siteevent->getIdentity()), array('type = ?' => 'event_photo', 'id =?' => $ynevent_id)); */
                                }

                                $row = $siteeventOtherinfoTable->getOtherinfo($siteevent->getIdentity());
                                if (empty($row)) {
                                    $siteeventOtherinfoTable->insert(array(
                                        'event_id' => $siteevent->getIdentity(),
                                        //  'host_params' => json_encode($hostInfo),
                                        'phone' => $ynevent->phone,
                                        'email' => $ynevent->email,
                                        'website' => $ynevent->url,
                                        'about' => $ynevent->brief_description
                                    )); //COMMIT
                                }
                                if ($ynevent->repeat_type) {
                                    //START EVENT IMPORTING
                                    $repeatevents = $yneventTable->select()
                                            ->from($yneventTableName, '*')
                                            //->where('event_id != ?', $ynevent_id)
                                            ->where('repeat_group = ?', $ynevent->repeat_group)
                                            ->order('event_id ASC');
                                    $ynRepeatevents = $yneventTable->fetchAll($repeatevents);
                                    foreach ($ynRepeatevents as $repeatevent) {
                                        //INSERT IN OCCURENCE TABLE
                                        $repeatevents = Engine_Api::_()->getItem('ynevent_event', $repeatevent['event_id']);
                                        $viewer = Engine_Api::_()->user()->getViewer();
                                        $row_occurrence = $siteeventOccurrencesTable->createRow();
                                        $oldTz = date_default_timezone_get();
                                        date_default_timezone_set($viewer->timezone);
                                        date_default_timezone_set($oldTz);
                                        $row_occurrence->event_id = $siteevent->getIdentity();
                                        $row_occurrence->starttime = date("Y-m-d H:i:s", strtotime($repeatevents->starttime));
                                        $row_occurrence->endtime = date("Y-m-d H:i:s", strtotime($repeatevents->endtime));
                                        $row_occurrence->save();
                                        $occurrence_id = $row_occurrence->occurrence_id;
                                        $row_occurrence->starttime = date("Y-m-d H:i:s", strtotime($repeatevents->starttime));
                                        $row_occurrence->endtime = date("Y-m-d H:i:s", strtotime($repeatevents->endtime));
                                        $row_occurrence->save();

                                        //GET EVENT MEMBERS
                                        $yneventMembers = $yneventMembershipTable->fetchAll(array('resource_id = ?' => $repeatevent['event_id']));
                                        foreach ($yneventMembers as $members) {
                                            $row_membership = $siteeventMembershipTable->createRow();
                                            $row_membership->resource_id = $siteevent->getIdentity();
                                            $row_membership->user_id = $members['user_id'];
                                            $row_membership->active = $members['active'];
                                            $row_membership->resource_approved = $members['resource_approved'];
                                            $row_membership->user_approved = $members['user_approved'];
                                            $row_membership->message = $members['message'];
                                            $row_membership->rsvp = $members['rsvp'];
                                            $row_membership->title = $members['title'];
                                            $row_membership->occurrence_id = $occurrence_id;
                                            $row_membership->save();
                                        }
                                        $repeatevents->is_event_import = 1;
                                        $repeatevents->save();
                                        if ($ynevent->repeat_type == 1) {
                                            $repeatParams = array();
                                            $repeatParams['repeat_interval'] = '86400';
                                            $repeatParams['eventrepeat_type'] = 'daily';
                                            $repeatParams['endtime'] = array("date" => date("m/d/Y", strtotime($ynevent->end_repeat)));
                                            $siteevent->repeat_params = json_encode($repeatParams);
                                        } elseif ($ynevent->repeat_type == 7) {
                                            $repeatParams = array();
                                            $repeatParams['repeat_interval'] = '0';
                                            $repeatParams['repeat_week'] = 1;
                                            $repeatParams['repeat_weekday'][] = date("N", strtotime($ynevent->starttime));
                                            $repeatParams['eventrepeat_type'] = 'weekly';
                                            $repeatParams['endtime'] = array("date" => date("m/d/Y", strtotime($ynevent->end_repeat)));
                                            $siteevent->repeat_params = json_encode($repeatParams);
                                        } elseif ($ynevent->repeat_type == 30) {
                                            $repeatParams = array();
                                            $repeatParams['repeat_interval'] = '0';
                                            $repeatParams['repeat_month'] = 1;
                                            $repeatParams['eventrepeat_type'] = 'monthly';
                                            $repeatParams['endtime'] = array("date" => date("m/d/Y", strtotime($ynevent->end_repeat)));
                                            $repeatParams['repeat_day'] = date("j", strtotime($ynevent->starttime));
                                            $siteevent->repeat_params = json_encode($repeatParams);
                                        }
                                        $siteevent->save();

                                        $conversationTable->update(array('resource_type' => $siteevent->getType(), 'resource_id' => $siteevent->getIdentity()), array('resource_type = ?' => 'event', 'resource_id =?' => $repeatevent['event_id']));
                                    }

                                    if ($activity_event) {
                                        $actionsTable->update(array('type' => 'siteevent_join', 'object_type' => 'siteevent_join', 'object_id' => $siteevent->getIdentity()), array('type = ?' => 'ynevent_join', 'object_type = ?' => 'event', 'object_id =?' => $repeatevent['event_id']));


                                        $streamTable->update(array('type' => 'siteevent_join', 'object_type' => 'siteevent_join', 'object_id' => $siteevent->getIdentity()), array('type = ?' => 'ynevent_join', 'object_type = ?' => 'event', 'object_id =?' => $repeatevent['event_id']));
                                    }
//                                 if ($activity_event) {
//                                     //START FETCH ACTIONS
//                                     $selectActionId = $actionsTable->select()
//                                             ->from($actionsTableName, 'action_id')
//                                             ->where('object_type = ?', 'event')
//                                             ->where('object_id = ?', $ynevent_id)
//                                             ->where('type =?', 'ynevent_join');
//                                     $selectActionIds = $yneventMembershipTable->fetchAll($selectActionId);
//                                     if ($selectActionIds) {
//                                         foreach ($selectActionIds as $selectActionId) {
//                                             $action = Engine_Api::_()->getItem('activity_action', $selectActionId['action_id']);
//                                             $action->type = 'siteevent_join';
//                                             $action->object_id = $siteevent->getIdentity();
//                                             $action->object_type = $siteevent->getType();
//                                             $action->params = '{"occurrence_id":"' . $occurrence_id . '"}';
//                                             $action->save();
// // 																						if($action)
// //                                             $actionsTable->resetActivityBindings($action);
//                                         }
//                                     }
//                                 }
                                } else {
                                    //INSERT IN OCCURENCE TABLE
                                    $viewer = Engine_Api::_()->user()->getViewer();
                                    $row_occurrence = $siteeventOccurrencesTable->createRow();
                                    $oldTz = date_default_timezone_get();
                                    date_default_timezone_set($viewer->timezone);
                                    date_default_timezone_set($oldTz);
                                    $row_occurrence->event_id = $siteevent->getIdentity();
                                    $row_occurrence->starttime = date("Y-m-d H:i:s", strtotime($ynevent->starttime));
                                    $row_occurrence->endtime = date("Y-m-d H:i:s", strtotime($ynevent->endtime));
                                    $row_occurrence->save();
                                    $occurrence_id = $row_occurrence->occurrence_id;

                                    $row_occurrence->starttime = date("Y-m-d H:i:s", strtotime($ynevent->starttime));
                                    $row_occurrence->endtime = date("Y-m-d H:i:s", strtotime($ynevent->endtime));
                                    $row_occurrence->save();
                                    //GET EVENT MEMBERS
                                    $yneventMembers = $yneventMembershipTable->fetchAll(array('resource_id = ?' => $ynevent_id));
                                    foreach ($yneventMembers as $members) {
                                        $row_membership = $siteeventMembershipTable->createRow();
                                        $row_membership->resource_id = $siteevent->getIdentity();
                                        $row_membership->user_id = $members['user_id'];
                                        $row_membership->active = $members['active'];
                                        $row_membership->resource_approved = $members['resource_approved'];
                                        $row_membership->user_approved = $members['user_approved'];
                                        $row_membership->message = $members['message'];
                                        $row_membership->rsvp = $members['rsvp'];
                                        $row_membership->title = $members['title'];
                                        $row_membership->occurrence_id = $occurrence_id;
                                        $row_membership->save();
                                    }
                                    $conversationTable->update(array('resource_type' => $siteevent->getType(), 'resource_id' => $siteevent->getIdentity()), array('resource_type = ?' => 'event', 'resource_id =?' => $ynevent_id));
                                    if ($activity_event) {
                                        $actionsTable->update(array('type' => 'siteevent_join', 'object_type' => 'siteevent_event', 'object_id' => $siteevent->getIdentity()), array('type = ?' => 'ynevent_join', 'object_type = ?' => 'event', 'object_id =?' => $ynevent_id));


                                        $streamTable->update(array('type' => 'siteevent_join', 'object_type' => 'siteevent_event', 'object_id' => $siteevent->getIdentity()), array('type = ?' => 'ynevent_join', 'object_type = ?' => 'event', 'object_id =?' => $ynevent_id));
                                    }
//                                 if ($activity_event) {
//                                     //START FETCH ACTIONS
//                                     $selectActionId = $actionsTable->select()
//                                             ->from($actionsTableName, 'action_id')
//                                             ->where('object_type = ?', 'event')
//                                             ->where('object_id = ?', $ynevent_id)
//                                             ->where('type =?', 'ynevent_join');
//                                     $selectActionIds = $yneventMembershipTable->fetchAll($selectActionId);
//                                     if ($selectActionIds) {
//                                         foreach ($selectActionIds as $selectActionId) {
//                                             $action = Engine_Api::_()->getItem('activity_action', $selectActionId['action_id']);
//                                             $action->type = 'siteevent_join';
//                                             $action->object_id = $siteevent->getIdentity();
//                                             $action->object_type = $siteevent->getType();
//                                             $action->params = '{"occurrence_id":"' . $occurrence_id . '"}';
//                                             $action->save();
//                                             //$actionsTable->resetActivityBindings($action);
//                                         }
//                                     }
//                                 }
                                }

                                //EVENT DOCUMENT IMPORT WORK
                                if (!empty($siteeventDocumentEnabled) && !empty($eventdocumentEnabled)) {
                                    $this->importDocuments($ynevent, $siteevent, $activity_event);
                                }

                                $yneventTopicSelect = $yneventTopicTable->select()
                                        ->from($yneventTopicTableName)
                                        ->where('event_id = ?', $ynevent_id);
                                $yneventTopicSelectDatas = $yneventTopicTable->fetchAll($yneventTopicSelect);
                                if (!empty($yneventTopicSelectDatas)) {
                                    $yneventTopicSelectDatas = $yneventTopicSelectDatas->toArray();

                                    foreach ($yneventTopicSelectDatas as $yneventTopicSelectData) {
                                        $siteeventTopic = $siteeventTopicTable->createRow();
                                        $siteeventTopic->event_id = $siteevent->getIdentity();
                                        $siteeventTopic->user_id = $yneventTopicSelectData['user_id'];
                                        $siteeventTopic->title = $yneventTopicSelectData['title'];
                                        $siteeventTopic->creation_date = $yneventTopicSelectData['creation_date'];
                                        $siteeventTopic->modified_date = $yneventTopicSelectData['modified_date'];
                                        $siteeventTopic->sticky = $yneventTopicSelectData['sticky'];
                                        $siteeventTopic->closed = $yneventTopicSelectData['closed'];
                                        $siteeventTopic->view_count = $yneventTopicSelectData['view_count'];
                                        $siteeventTopic->lastpost_id = $yneventTopicSelectData['lastpost_id'];
                                        $siteeventTopic->lastposter_id = $yneventTopicSelectData['lastposter_id'];
                                        $siteeventTopic->save();

                                        $siteeventTopic->creation_date = $yneventTopicSelectData['creation_date'];
                                        $siteeventTopic->modified_date = $yneventTopicSelectData['modified_date'];
                                        $siteeventTopic->save();
                                        $attachmentsTable->update(array('type' => 'siteevent_topic', 'id' => $siteeventTopic->getIdentity()), array('type = ?' => 'event_topic', 'id =?' => $yneventTopicSelectData['topic_id']));
                                        if ($activity_event) {
                                            $selectActionId = $actionsTable->select()
                                                    ->from($actionsTableName, 'action_id')
                                                    ->where('object_type = ?', 'event_topic')
                                                    ->where('object_id =?', $yneventTopicSelectData['topic_id'])
                                                    ->where('type =?', 'ynevent_topic_create');
                                            $selectActions = $actionsTable->fetchAll($selectActionId);

                                            foreach ($selectActions as $selectAction) {
                                                $action = Engine_Api::_()->getItem('activity_action', $selectAction['action_id']);

                                                if ($action) {
                                                    $action->type = 'siteevent_topic_create';
                                                    $action->object_type = 'siteevent_event';
                                                    $action->object_id = $siteevent->getIdentity();
                                                    $action->save();
                                                    $actionsTable->resetActivityBindings($action);
                                                }
                                            }
                                        }

                                        //START FETCH TOPIC POST'S
                                        $yneventPostSelect = $yneventPostTable->select()
                                                ->from($yneventPostTableName)
                                                ->where('topic_id = ?', $yneventTopicSelectData['topic_id'])
                                                ->where('event_id = ?', $ynevent_id);
                                        $yneventPostSelectDatas = $yneventPostTable->fetchAll($yneventPostSelect);
                                        if (!empty($yneventPostSelectDatas)) {
                                            $yneventPostSelectDatas = $yneventPostSelectDatas->toArray();

                                            foreach ($yneventPostSelectDatas as $yneventPostSelectData) {
                                                $siteeventPost = $siteeventPostTable->createRow();
                                                $siteeventPost->topic_id = $siteeventTopic->topic_id;
                                                $siteeventPost->event_id = $siteevent->event_id;
                                                $siteeventPost->user_id = $yneventPostSelectData['user_id'];
                                                $siteeventPost->body = $yneventPostSelectData['body'];
                                                $siteeventPost->creation_date = $yneventPostSelectData['creation_date'];
                                                $siteeventPost->modified_date = $yneventPostSelectData['modified_date'];
                                                $siteeventPost->save();

                                                $siteeventPost->creation_date = $yneventPostSelectData['creation_date'];
                                                $siteeventPost->modified_date = $yneventPostSelectData['modified_date'];
                                                $siteeventPost->save();
                                                $attachmentsTable->update(array('type' => 'siteevent_post', 'id' => $siteeventTopic->getIdentity()), array('type = ?' => 'event_post', 'id =?' => $yneventPostSelectData['post_id']));
                                                if ($activity_event) {
                                                    $selectActionId = $actionsTable->select()
                                                            ->from($actionsTableName, 'action_id')
                                                            ->where('object_type = ?', 'event_topic')
                                                            ->where('object_id =?', $yneventPostSelectData['topic_id'])
                                                            ->where('type =?', 'ynevent_topic_reply');
                                                    $selectActions = $actionsTable->fetchAll($selectActionId);

                                                    foreach ($selectActions as $selectAction) {
                                                        $action = Engine_Api::_()->getItem('activity_action', $selectAction['action_id']);
                                                        if ($action) {
                                                            $action->type = 'siteevent_topic_reply';
                                                            $action->object_type = 'siteevent_event';
                                                            $action->object_id = $siteevent->getIdentity();
                                                            $action->save();
                                                            $actionsTable->resetActivityBindings($action);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        //END FETCH TOPIC POST'S

                                        $siteeventTopic->post_count = $yneventTopicSelectData['post_count'];
                                        $siteeventTopic->save();

                                        //START FETCH TOPIC WATCH
                                        $yneventTopicWatchDatas = $yneventTopicWatchesTable->fetchAll(array('resource_id = ?' => $ynevent_id));
                                        foreach ($yneventTopicWatchDatas as $yneventTopicWatchData) {
                                            if (!empty($yneventTopicWatchData)) {
                                                $siteeventTopicWatchSelect = $siteeventTopicWatchesTable->select()
                                                        ->from($siteeventTopicWatchesTableName)
                                                        ->where('resource_id = ?', $siteeventTopic->event_id)
                                                        ->where('topic_id = ?', $siteeventTopic->topic_id)
                                                        ->where('user_id = ?', $yneventTopicWatchData->user_id);
                                                $siteeventTopicWatchSelectDatas = $siteeventTopicWatchesTable->fetchRow($siteeventTopicWatchSelect);

                                                if (empty($siteeventTopicWatchSelectDatas)) {
                                                    $siteeventTopicWatchesTable->insert(array(
                                                        'resource_id' => $siteeventTopic->event_id,
                                                        'topic_id' => $siteeventTopic->topic_id,
                                                        'user_id' => $yneventTopicWatchData->user_id,
                                                        'watch' => $yneventTopicWatchData->watch
                                                    ));
                                                }
                                            }
                                        }
                                        //END FETCH TOPIC WATCH
                                    }
                                }

                                //START FETCH LIKES
                                $selectLike = $likeTable->select()
                                        ->from($likeTableName, 'like_id')
                                        ->where('resource_type = ?', 'event')
                                        ->where('resource_id = ?', $ynevent_id);
                                $selectLikeDatas = $likeTable->fetchAll($selectLike);
                                foreach ($selectLikeDatas as $selectLikeData) {
                                    $like = Engine_Api::_()->getItem('core_like', $selectLikeData->like_id);
                                    $newLikeEntry = $likeTable->createRow();
                                    $newLikeEntry->resource_type = 'siteevent_event';
                                    $newLikeEntry->resource_id = $siteevent->getIdentity();
                                    $newLikeEntry->poster_type = 'user';
                                    $newLikeEntry->poster_id = $like->poster_id;
                                    $newLikeEntry->creation_date = $like->creation_date;
                                    $newLikeEntry->save();

                                    $newLikeEntry->creation_date = $like->creation_date;
                                    $newLikeEntry->save();
                                }
                                //END FETCH LIKES
                                //START FETCH PHOTO DATA
                                $selectEventPhoto = $yneventPhotoTable->select()
                                        ->from($yneventPhotoTableName)
                                        ->where('event_id = ?', $ynevent_id);
                                $yneventPhotoDatas = $yneventPhotoTable->fetchAll($selectEventPhoto);

                                if (!empty($yneventPhotoDatas)) {

                                    $yneventPhotoDatas = $yneventPhotoDatas->toArray();

                                    if (empty($ynevent->photo_id)) {
                                        foreach ($yneventPhotoDatas as $yneventPhotoData) {
                                            $ynevent->photo_id = $yneventPhotoData['photo_id'];
                                            break;
                                        }
                                    }

                                    if (!empty($ynevent->photo_id)) {
                                        $yneventPhotoData = $yneventPhotoTable->fetchRow(array('file_id = ?' => $ynevent->photo_id));
                                        if (!empty($yneventPhotoData)) {
                                            $storageData = $storageTable->fetchRow(array('file_id = ?' => $yneventPhotoData->file_id));

                                            if (!empty($storageData) && !empty($storageData->storage_path)) {
                                                if (is_string($storageData->storage_path) && file_exists($storageData->storage_path))
                                                    $siteevent->setPhoto($storageData->storage_path);

                                                $album_id = $siteeventAlbumTable->update(array('photo_id' => $siteevent->photo_id), array('event_id = ?' => $siteevent->event_id));

                                                $siteeventProfilePhoto = Engine_Api::_()->getDbTable('photos', 'siteevent')->fetchRow(array('file_id = ?' => $siteevent->photo_id));
                                                if (!empty($siteeventProfilePhoto)) {
                                                    $siteeventProfilePhotoId = $siteeventProfilePhoto->photo_id;
                                                } else {
                                                    $siteeventProfilePhotoId = $siteevent->photo_id;
                                                }

                                                //START FETCH LIKES
                                                $selectLike = $likeTable->select()
                                                        ->from($likeTableName, 'like_id')
                                                        ->where('resource_type = ?', 'ynevent_photo')
                                                        ->where('resource_id = ?', $ynevent->photo_id);
                                                $selectLikeDatas = $likeTable->fetchAll($selectLike);
                                                foreach ($selectLikeDatas as $selectLikeData) {
                                                    $like = Engine_Api::_()->getItem('core_like', $selectLikeData->like_id);
                                                    $newLikeEntry = $likeTable->createRow();
                                                    $newLikeEntry->resource_type = 'siteevent_photo';
                                                    $newLikeEntry->resource_id = $siteeventProfilePhotoId;
                                                    $newLikeEntry->poster_type = 'user';
                                                    $newLikeEntry->poster_id = $like->poster_id;
                                                    $newLikeEntry->creation_date = $like->creation_date;
                                                    $newLikeEntry->save();

                                                    $newLikeEntry->creation_date = $like->creation_date;
                                                    $newLikeEntry->save();
                                                }
                                                //END FETCH LIKES
                                                //START FETCH COMMENTS
                                                $selectLike = $commentTable->select()
                                                        ->from($commentTableName, 'comment_id')
                                                        ->where('resource_type = ?', 'ynevent_photo')
                                                        ->where('resource_id = ?', $ynevent->photo_id);
                                                $selectLikeDatas = $commentTable->fetchAll($selectLike);
                                                foreach ($selectLikeDatas as $selectLikeData) {
                                                    $comment = Engine_Api::_()->getItem('core_comment', $selectLikeData->comment_id);

                                                    $newLikeEntry = $commentTable->createRow();
                                                    $newLikeEntry->resource_type = 'siteevent_photo';
                                                    $newLikeEntry->resource_id = $siteeventProfilePhotoId;
                                                    $newLikeEntry->poster_type = 'user';
                                                    $newLikeEntry->poster_id = $comment->poster_id;
                                                    $newLikeEntry->body = $comment->body;
                                                    $newLikeEntry->creation_date = $comment->creation_date;
                                                    $newLikeEntry->like_count = $comment->like_count;
                                                    $newLikeEntry->save();

                                                    $newLikeEntry->creation_date = $comment->creation_date;
                                                    $newLikeEntry->save();
                                                }
                                                //END FETCH COMMENTS
                                                //START FETCH TAGGER DETAIL
                                                $selectTagmaps = $tagmapsTable->select()
                                                        ->from($tagmapsTableName, 'tagmap_id')
                                                        ->where('resource_type = ?', 'ynevent_photo')
                                                        ->where('resource_id = ?', $ynevent->photo_id);
                                                $selectTagmapsDatas = $tagmapsTable->fetchAll($selectTagmaps);
                                                foreach ($selectTagmapsDatas as $selectTagmapsData) {
                                                    $tagMap = Engine_Api::_()->getItem('core_tag_map', $selectTagmapsData->tagmap_id);

                                                    $newTagmapEntry = $tagmapsTable->createRow();
                                                    $newTagmapEntry->resource_type = 'siteevent_photo';
                                                    $newTagmapEntry->resource_id = $siteeventProfilePhotoId;
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

                                        $fetchDefaultAlbum = $siteeventAlbumTable->fetchRow(array('event_id = ?' => $siteevent->event_id));
                                        if (!empty($fetchDefaultAlbum)) {

                                            $selectEventPhoto = $yneventPhotoTable->select()
                                                    ->from($yneventPhotoTable->info('name'))
                                                    ->where('event_id = ?', $ynevent_id);
                                            $yneventPhotoDatas = $yneventPhotoTable->fetchAll($selectEventPhoto);

                                            $order = 999;
                                            foreach ($yneventPhotoDatas as $yneventPhotoData) {

                                                if ($yneventPhotoData['file_id'] != $ynevent->photo_id) {
                                                    $params = array(
                                                        'collection_id' => $fetchDefaultAlbum->album_id,
                                                        'album_id' => $fetchDefaultAlbum->album_id,
                                                        'event_id' => $siteevent->event_id,
                                                        'user_id' => $yneventPhotoData['user_id'],
                                                        'order' => $order
                                                    );

                                                    $storageData = $storageTable->fetchRow(array('file_id = ?' => $yneventPhotoData['file_id']));
                                                    if (!empty($storageData) && !empty($storageData->storage_path)) {
                                                        $file = array();
                                                        $file['tmp_name'] = $storageData->storage_path;
                                                        $path_array = explode('/', $file['tmp_name']);
                                                        $file['name'] = end($path_array);

                                                        $siteeventPhoto = Engine_Api::_()->siteevent()->createPhoto($params, $file);
                                                        if (!empty($siteeventPhoto)) {

                                                            $order++;

                                                            //START FETCH LIKES
                                                            $selectLike = $likeTable->select()
                                                                    ->from($likeTableName, 'like_id')
                                                                    ->where('resource_type = ?', 'ynevent_photo')
                                                                    ->where('resource_id = ?', $yneventPhotoData['photo_id']);
                                                            $selectLikeDatas = $likeTable->fetchAll($selectLike);
                                                            foreach ($selectLikeDatas as $selectLikeData) {
                                                                $like = Engine_Api::_()->getItem('core_like', $selectLikeData->like_id);

                                                                $newLikeEntry = $likeTable->createRow();
                                                                $newLikeEntry->resource_type = 'siteevent_photo';
                                                                $newLikeEntry->resource_id = $siteeventPhoto->photo_id;
                                                                $newLikeEntry->poster_type = 'user';
                                                                $newLikeEntry->poster_id = $like->poster_id;
                                                                $newLikeEntry->creation_date = $like->creation_date;
                                                                $newLikeEntry->save();

                                                                $newLikeEntry->creation_date = $like->creation_date;
                                                                $newLikeEntry->save();
                                                            }
                                                            //END FETCH LIKES
                                                            //START FETCH COMMENTS
                                                            $selectLike = $commentTable->select()
                                                                    ->from($commentTableName, 'comment_id')
                                                                    ->where('resource_type = ?', 'ynevent_photo')
                                                                    ->where('resource_id = ?', $yneventPhotoData['photo_id']);
                                                            $selectLikeDatas = $commentTable->fetchAll($selectLike);
                                                            foreach ($selectLikeDatas as $selectLikeData) {
                                                                $comment = Engine_Api::_()->getItem('core_comment', $selectLikeData->comment_id);

                                                                $newLikeEntry = $commentTable->createRow();
                                                                $newLikeEntry->resource_type = 'siteevent_photo';
                                                                $newLikeEntry->resource_id = $siteeventPhoto->photo_id;
                                                                $newLikeEntry->poster_type = 'user';
                                                                $newLikeEntry->poster_id = $comment->poster_id;
                                                                $newLikeEntry->body = $comment->body;
                                                                $newLikeEntry->creation_date = $comment->creation_date;
                                                                $newLikeEntry->like_count = $comment->like_count;
                                                                $newLikeEntry->save();

                                                                $newLikeEntry->creation_date = $comment->creation_date;
                                                                $newLikeEntry->save();
                                                            }
                                                            //END FETCH COMMENTS
                                                            //START FETCH TAGGER DETAIL
                                                            $selectTagmaps = $tagmapsTable->select()
                                                                    ->from($tagmapsTableName, 'tagmap_id')
                                                                    ->where('resource_type = ?', 'ynevent_photo')
                                                                    ->where('resource_id = ?', $yneventPhotoData['photo_id']);
                                                            $selectTagmapsDatas = $tagmapsTable->fetchAll($selectTagmaps);
                                                            foreach ($selectTagmapsDatas as $selectTagmapsData) {
                                                                $tagMap = Engine_Api::_()->getItem('core_tag_map', $selectTagmapsData->tagmap_id);

                                                                $newTagmapEntry = $tagmapsTable->createRow();
                                                                $newTagmapEntry->resource_type = 'siteevent_photo';
                                                                $newTagmapEntry->resource_id = $siteeventPhoto->photo_id;
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
                                }

                                //START STYLES
                                $selectStyles = $stylesTable->select()
                                        ->from($stylesTableName, 'style')
                                        ->where('type = ?', 'ynevent_event')
                                        ->where('id = ?', $ynevent_id);
                                $selectStyleDatas = $stylesTable->fetchRow($selectStyles);
                                if (!empty($selectStyleDatas)) {
                                    $selectSiteeventStyles = $stylesTable->select()
                                            ->from($stylesTableName, 'style')
                                            ->where('type = ?', 'siteevent_event')
                                            ->where('id = ?', $siteevent->getIdentity());
                                    $selectSiteeventStyleDatas = $stylesTable->fetchRow($selectSiteeventStyles);
                                    if (empty($selectSiteeventStyleDatas)) {
                                        //CREATE
                                        $stylesTable->insert(array(
                                            'type' => 'siteevent_event',
                                            'id' => $siteevent->getIdentity(),
                                            'style' => $selectStyleDatas->style
                                        ));
                                    }
                                }
                                //END STYLES
                                //START UPDATE TOTAL LIKES IN SITEEVENT TABLE
                                $selectLikeCount = $likeTable->select()
                                        ->from($likeTableName, array('COUNT(*) AS like_count'))
                                        ->where('resource_type = ?', 'event')
                                        ->where('resource_id = ?', $ynevent_id);
                                $selectLikeCounts = $likeTable->fetchAll($selectLikeCount);
                                if (!empty($selectLikeCounts)) {
                                    $selectLikeCounts = $selectLikeCounts->toArray();
                                    $siteevent->like_count = $selectLikeCounts[0]['like_count'];
                                    $siteevent->save();
                                }
                                //END UPDATE TOTAL LIKES IN SITEEVENT TABLE

                                if ($activity_event) {
                                    $selectActionId = $actionsTable->select()
                                            ->from($actionsTableName, 'action_id')
                                            ->where('object_type = ?', 'event')
                                            ->where('object_id = ?', $ynevent_id)
                                            ->where('type =?', 'ynevent_photo_upload')
                                            ->query()
                                            ->fetchColumn();
                                    if ($selectActionId) {
                                        $action = Engine_Api::_()->getItem('activity_action', $selectActionId);
                                        $action->type = 'siteevent_photo_upload';
                                        $action->object_id = $siteevent->getIdentity();
                                        $action->object_type = $siteevent->getType();
                                        $action->params = array_merge($action->params, array('title' => $siteevent->getTitle()));
                                        $action->save();
                                        $actionsTable->resetActivityBindings($action);
                                    }
                                }

                                //END PRIVACY WORK
                                //START FOLLOW WORK
                                $selectfollow = $followEventTable->select()
                                        ->from($followEventTableName, array('*'))
                                        ->where('resource_id = ?', $ynevent_id)
                                        ->where('follow = ?', 1);
                                $followEvents = $followEventTable->fetchAll($selectfollow);
                                if (!empty($followEvents)) {
                                    $followEvents = $followEvents->toArray();
                                    foreach ($followEvents as $follow) {
                                        //CREATE
                                        $followSeaocoreTable->insert(array(
                                            'resource_type' => 'siteevent_event',
                                            'resource_id' => $siteevent->getIdentity(),
                                            'poster_type' => 'user',
                                            'poster_id' => $follow['user_id'],
                                            'creation_date' => $ynevent->creation_date
                                        ));
                                    }
                                }
                                //END FOLLOW WORK 	
                                //START RATING WORK
                                $selectEventRatings = $eventRatingTable->select()
                                        ->from($eventRatingTableName, array('*'))
                                        ->where('event_id = ?', $ynevent_id);
                                $eventRatingDatas = $eventRatingTable->fetchAll($selectEventRatings);
                                if (!empty($eventRatingDatas)) {
                                    $eventRatings = $eventRatingDatas->toArray();
                                    foreach ($eventRatings as $rating) {
                                        //CREATE
                                        $siteeventRatingTable->insert(array(
                                            'review_id' => 0,
                                            'resource_id' => $siteevent->getIdentity(),
                                            'resource_type' => 'siteevent_event',
                                            'user_id' => $rating['user_id'],
                                            'ratingparam_id' => 0,
                                            'rating' => $rating['rating'],
                                            'category_id' => 0,
                                            'type' => 'user'
                                        ));
                                    }

                                    $rating_users = $siteeventRatingTable
                                            ->select()
                                            ->from($siteeventRatingTableName, array('AVG(rating) AS avg_rating'))
                                            //->join($tableReviewName, "$tableReviewName.review_id = $tableRatingName.review_id", null)
                                            ->where($siteeventRatingTableName . ".ratingparam_id = ?", 0)
                                            ->where($siteeventRatingTableName . ".resource_id = ?", $siteevent->getIdentity())
                                            ->where($siteeventRatingTableName . ".resource_type = ?", 'siteevent_event')
                                            ->where($siteeventRatingTableName . ".type in (?) ", array('user'))
                                            ->where($siteeventRatingTableName . ".rating != ?", 0)
                                            ->group($siteeventRatingTableName . '.resource_id')
                                            ->query()
                                            ->fetchColumn();

                                    $siteevent->rating_users = $rating_users;
                                    $siteevent->save();
                                }
                                //END RATING WORK
                                //START FETCH VIDEO DATA
                                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('ynvideo')) {

                                    $eventVideoTable = Engine_Api::_()->getDbtable('videos', 'ynvideo');
                                    $eventVideoTableName = $eventVideoTable->info('name');
                                    $siteeventVideoTable = Engine_Api::_()->getDbtable('videos', 'siteevent');
                                    $eventVideoRating = Engine_Api::_()->getDbtable('ratings', 'ynvideo');
                                    $eventVideoRatingName = $eventVideoRating->info('name');
                                    $siteeventVideoRating = Engine_Api::_()->getDbtable('videoratings', 'siteevent');
                                    $selectEventVideos = $eventVideoTable->select()
                                            ->from($eventVideoTableName, 'video_id')
                                            ->where('parent_id = ?', $ynevent_id)
                                            ->where('parent_type = ?', 'event');
                                    $eventVideoDatas = $eventVideoTable->fetchAll($selectEventVideos);
                                    foreach ($eventVideoDatas as $eventVideoData) {
                                        $eventVideo = Engine_Api::_()->getItem('video', $eventVideoData->video_id);
                                        if (!empty($eventVideo)) {
                                            $db = $siteeventVideoTable->getAdapter();
                                            $db->beginTransaction();

                                            try {
                                                $siteeventVideo = $siteeventVideoTable->createRow();
                                                $siteeventVideo->event_id = $siteevent->event_id;
                                                $siteeventVideo->title = $eventVideo->title;
                                                $siteeventVideo->description = $eventVideo->description;
                                                $siteeventVideo->search = $eventVideo->search;
                                                $siteeventVideo->owner_id = $eventVideo->owner_id;
                                                $siteeventVideo->creation_date = $eventVideo->creation_date;
                                                $siteeventVideo->modified_date = $eventVideo->modified_date;

                                                $siteeventVideo->view_count = 1;
                                                if ($eventVideo->view_count > 0) {
                                                    $siteeventVideo->view_count = $eventVideo->view_count;
                                                }

                                                $siteeventVideo->comment_count = $eventVideo->comment_count;
                                                $siteeventVideo->type = $eventVideo->type;
                                                $siteeventVideo->code = $eventVideo->code;
                                                $siteeventVideo->rating = $eventVideo->rating;
                                                $siteeventVideo->status = $eventVideo->status;
                                                $siteeventVideo->file_id = 0;
                                                $siteeventVideo->duration = $eventVideo->duration;
                                                $siteeventVideo->save();

                                                $siteeventVideo->creation_date = $eventVideo->creation_date;
                                                $siteeventVideo->modified_date = $eventVideo->modified_date;
                                                $siteeventVideo->save();

                                                $db->commit();
                                            } catch (Exception $e) {
                                                $db->rollBack();
                                                throw $e;
                                            }

                                            if ($activity_event) {
                                                $selectActionId = $actionsTable->select()
                                                        ->from($actionsTableName, 'action_id')
                                                        ->where('object_type = ?', 'video')
                                                        ->where('object_id =?', $eventVideoData->video_id)
                                                        ->where('type =?', 'video_new');
                                                $selectActions = $actionsTable->fetchAll($selectActionId);

                                                foreach ($selectActions as $selectAction) {
                                                    $action = Engine_Api::_()->getItem('activity_action', $selectAction['action_id']);

                                                    if ($action) {
                                                        $video = Engine_Api::_()->getItem('video', $action->object_id);

                                                        if ($video) {
                                                            if ($video->parent_id == $ynevent_id) {
                                                                $action->type = 'siteevent_video_new';
                                                                $action->object_type = 'siteevent_event';
                                                                $action->object_id = $siteevent->getIdentity();
                                                                $action->save();
                                                                $actionsTable->resetActivityBindings($action);
                                                            }
                                                        }
                                                    }
                                                }

                                                $selectActionId = $actionsTable->select()
                                                        ->from($actionsTableName, 'action_id')
                                                        ->where('object_type = ?', 'video')
                                                        ->where('object_id =?', $eventVideoData->video_id)
                                                        ->where('type =?', 'ynvideo_new');
                                                $selectActions = $actionsTable->fetchAll($selectActionId);

                                                foreach ($selectActions as $selectAction) {
                                                    $action = Engine_Api::_()->getItem('activity_action', $selectAction['action_id']);

                                                    if ($action) {
                                                        $video = Engine_Api::_()->getItem('video', $action->object_id);

                                                        if ($video) {
                                                            if ($video->parent_id == $ynevent_id) {
                                                                $action->type = 'siteevent_video_new';
                                                                $action->object_type = 'siteevent_event';
                                                                $action->object_id = $siteevent->getIdentity();
                                                                $action->save();
                                                                $actionsTable->resetActivityBindings($action);
                                                            }
                                                        }
                                                    }
                                                }

                                                $selectActionId = $actionsTable->select()
                                                        ->from($actionsTableName, 'action_id')
                                                        ->where('object_type = ?', 'video')
                                                        ->where('object_id =?', $eventVideoData->video_id)
                                                        ->where('type =?', 'ynevent_video_create');
                                                $selectActions = $actionsTable->fetchAll($selectActionId);

                                                foreach ($selectActions as $selectAction) {
                                                    $action = Engine_Api::_()->getItem('activity_action', $selectAction['action_id']);

                                                    if ($action) {
                                                        $video = Engine_Api::_()->getItem('video', $action->object_id);

                                                        if ($video) {
                                                            if ($video->parent_id == $ynevent_id) {
                                                                $action->type = 'siteevent_video_new';
                                                                $action->object_type = 'siteevent_event';
                                                                $action->object_id = $siteevent->getIdentity();
                                                                $action->save();
                                                                $actionsTable->resetActivityBindings($action);
                                                            }
                                                        }
                                                    }
                                                }
                                            }

                                            //START VIDEO THUMB WORK
                                            if (!empty($siteeventVideo->code) && !empty($siteeventVideo->type) && !empty($eventVideo->photo_id)) {
                                                $storageData = $storageTable->fetchRow(array('file_id = ?' => $eventVideo->photo_id));
                                                if (!empty($storageData) && !empty($storageData->storage_path)) {
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
                                                                'parent_id' => $siteeventVideo->video_id
                                                            ));

                                                            //REMOVE TEMP FILE
                                                            @unlink($thumb_file);
                                                            @unlink($tmp_file);
                                                        } catch (Exception $e) {
                                                            
                                                        }

                                                        $siteeventVideo->photo_id = $thumbFileRow->file_id;
                                                        $siteeventVideo->save();
                                                    }
                                                }
                                            }
                                            //END VIDEO THUMB WORK
                                            //START FETCH TAG
                                            $videoTags = $eventVideo->tags()->getTagMaps();
                                            $tagString = '';

                                            foreach ($videoTags as $tagmap) {

                                                if ($tagString != '')
                                                    $tagString .= ', ';
                                                $tagString .= $tagmap->getTag()->getTitle();

                                                $owner = Engine_Api::_()->getItem('user', $eventVideo->owner_id);
                                                $tags = preg_split('/[,]+/', $tagString);
                                                $tags = array_filter(array_map("trim", $tags));
                                                $siteeventVideo->tags()->setTagMaps($owner, $tags);
                                            }
                                            //END FETCH TAG
                                            //START FETCH LIKES
                                            $selectLike = $likeTable->select()
                                                    ->from($likeTableName, 'like_id')
                                                    ->where('resource_type = ?', 'video')
                                                    ->where('resource_id = ?', $eventVideoData->video_id);
                                            $selectLikeDatas = $likeTable->fetchAll($selectLike);
                                            foreach ($selectLikeDatas as $selectLikeData) {
                                                $like = Engine_Api::_()->getItem('core_like', $selectLikeData->like_id);

                                                $newLikeEntry = $likeTable->createRow();
                                                $newLikeEntry->resource_type = 'siteevent_video';
                                                $newLikeEntry->resource_id = $siteeventVideo->video_id;
                                                $newLikeEntry->poster_type = 'user';
                                                $newLikeEntry->poster_id = $like->poster_id;
                                                $newLikeEntry->creation_date = $like->creation_date;
                                                $newLikeEntry->save();

                                                $newLikeEntry->creation_date = $like->creation_date;
                                                $newLikeEntry->save();
                                            }
                                            //END FETCH LIKES
                                            //START FETCH COMMENTS
                                            $selectLike = $commentTable->select()
                                                    ->from($commentTableName, 'comment_id')
                                                    ->where('resource_type = ?', 'video')
                                                    ->where('resource_id = ?', $eventVideoData->video_id);
                                            $selectLikeDatas = $commentTable->fetchAll($selectLike);
                                            foreach ($selectLikeDatas as $selectLikeData) {
                                                $comment = Engine_Api::_()->getItem('core_comment', $selectLikeData->comment_id);

                                                $newLikeEntry = $commentTable->createRow();
                                                $newLikeEntry->resource_type = 'siteevent_video';
                                                $newLikeEntry->resource_id = $siteeventVideo->video_id;
                                                $newLikeEntry->poster_type = 'user';
                                                $newLikeEntry->poster_id = $comment->poster_id;
                                                $newLikeEntry->body = $comment->body;
                                                $newLikeEntry->creation_date = $comment->creation_date;
                                                $newLikeEntry->like_count = $comment->like_count;
                                                $newLikeEntry->save();

                                                $newLikeEntry->creation_date = $comment->creation_date;
                                                $newLikeEntry->save();
                                            }
                                            //END FETCH COMMENTS
                                            //START UPDATE TOTAL LIKES IN REVIEW-VIDEO TABLE
                                            $selectLikeCount = $likeTable->select()
                                                    ->from($likeTableName, array('COUNT(*) AS like_count'))
                                                    ->where('resource_type = ?', 'siteevent_video')
                                                    ->where('resource_id = ?', $siteeventVideo->video_id);
                                            $selectLikeCounts = $likeTable->fetchAll($selectLikeCount);
                                            if (!empty($selectLikeCounts)) {
                                                $selectLikeCounts = $selectLikeCounts->toArray();
                                                $siteeventVideo->like_count = $selectLikeCounts[0]['like_count'];
                                                $siteeventVideo->save();
                                            }
                                            //END UPDATE TOTAL LIKES IN REVIEW-VIDEO TABLE
                                            //START FETCH RATTING DATA
                                            $selectVideoRating = $eventVideoRating->select()
                                                    ->from($eventVideoRatingName)
                                                    ->where('video_id = ?', $eventVideoData->video_id);

                                            $eventVideoRatingDatas = $eventVideoRating->fetchAll($selectVideoRating);
                                            if (!empty($eventVideoRatingDatas)) {
                                                $eventVideoRatingDatas = $eventVideoRatingDatas->toArray();
                                            }

                                            foreach ($eventVideoRatingDatas as $eventVideoRatingData) {

                                                $siteeventVideoRating->insert(array(
                                                    'videorating_id' => $siteeventVideo->video_id,
                                                    'user_id' => $eventVideoRatingData['user_id'],
                                                    'rating' => $eventVideoRatingData['rating']
                                                ));
                                            }
                                            //END FETCH RATTING DATA
                                        }
                                    }
                                }
                                //END FETCH VIDEO DATA
                            }

                            //CREATE LOG ENTRY IN LOG FILE
                            if (file_exists(APPLICATION_PATH . '/temporary/log/EventToSiteeventImport.log')) {
                                $myFile = APPLICATION_PATH . '/temporary/log/EventToSiteeventImport.log';
                                $error = Zend_Registry::get('Zend_Translate')->_("can't open file");
                                $fh = fopen($myFile, 'a') or die($error);
                                $current_time = date('D, d M Y H:i:s T');
                                $siteevent_title = $siteevent->title;
                                $stringData = $this->view->translate('Event with ID ') . $ynevent_id . $this->view->translate(' is successfully imported into a Advanced Event with ID ') . $siteevent->event_id . $this->view->translate(' at ') . $current_time . $this->view->translate(". Title of that Event is '") . $siteevent_title . "'.\n\n";
                                fwrite($fh, $stringData);
                                fclose($fh);
                            }

                            $db->commit();

                            $this->view->event_assigned_previous_id = $ynevent_id;
                        } catch (Exception $e) {
                            $db->rollback();
                            throw($e);
                        }

                        if ($next_import_count >= 1) {
                            $this->_redirect("admin/siteevent/importevent/index?start_import=1&module=event&recall=1&activity_event=$activity_event");
                        }
                    }
                } else {
                    if ($_GET['recall']) {
                        echo json_encode(array('success' => 1));
                        exit();
                    }
                }
            }
        }

        if ($sitepageeventEnabled && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')))) {

            //GET EVENT TABLES 
            $sitepageeventTable = Engine_Api::_()->getDbTable('events', 'sitepageevent');
            $sitepageeventTableName = $sitepageeventTable->info('name');

            //GET EVENT CATEGORIES TABLE
            $sitepageeventCategoryTable = Engine_Api::_()->getDbtable('categories', 'sitepageevent');
            $sitepageeventCategoryTableName = $sitepageeventCategoryTable->info('name');

            //GET EVENT MEMBERSHIP TABLE
            $sitepageeventMembershipTable = Engine_Api::_()->getDbtable('membership', 'sitepageevent');

            //GET EVENT TOPIC TABLE
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion')) {
                $sitepageeventTopicTable = Engine_Api::_()->getDbtable('topics', 'sitepage');
                $sitepageeventTopicTableName = $sitepageeventTopicTable->info('name');

                //GET EVENT POST TABLE
                $sitepageeventPostTable = Engine_Api::_()->getDbtable('posts', 'sitepage');
                $sitepageeventPostTableName = $sitepageeventPostTable->info('name');

                //GET EVENT TOPICWATCHES  TABLE
                $sitepageeventTopicWatchesTable = Engine_Api::_()->getDbtable('topicwatches', 'sitepage');
                $sitepageeventTopicWatchesTableName = $sitepageeventTopicWatchesTable->info('name');
            }

            //GET EVENT PHOTO TABLE
            $sitepageeventPhotoTable = Engine_Api::_()->getDbtable('photos', 'sitepageevent');
            $sitepageeventPhotoTableName = $sitepageeventPhotoTable->info('name');

            //ADD NEW COLUMN IN EVENT TABLE
            $db = Engine_Db_Table::getDefaultAdapter();
            $is_event_import = $db->query("SHOW COLUMNS FROM engine4_sitepageevent_events LIKE 'is_event_import'")->fetch();
            if (empty($is_event_import)) {
                $run_query = $db->query("ALTER TABLE `engine4_sitepageevent_events` ADD is_event_import TINYINT( 2 ) NOT NULL DEFAULT '0'");
            }

            //START IF IMPORTING IS BREAKED BY SOME REASON
            $selectEvents = $sitepageeventTable->select()
                    ->from($sitepageeventTableName, 'event_id')
                    ->where('is_event_import != ?', 1)
                    ->order('event_id ASC');
            $sitepageeventDatas = $sitepageeventTable->fetchAll($selectEvents);


            if (!empty($sitepageeventDatas)) {

                $flag_first_sitepageevent_id = 1;

                foreach ($sitepageeventDatas as $sitepageeventData) {

                    if ($flag_first_sitepageevent_id == 1) {
                        $this->view->first_sitepageevent_id = $first_sitepageevent_id = $sitepageeventData->event_id;
                    }
                    $flag_first_sitepageevent_id++;

                    $this->view->last_sitepageevent_id = $last_sitepageevent_id = $sitepageeventData->event_id;
                }

                if (isset($_GET['sitepageevent_assigned_previous_id'])) {
                    $this->view->sitepageevent_assigned_previous_id = $sitepageevent_assigned_previous_id = $_GET['sitepageevent_assigned_previous_id'];
                } else {
                    $this->view->sitepageevent_assigned_previous_id = $sitepageevent_assigned_previous_id = $first_sitepageevent_id;
                }
            }
            //START IMPORTING IF REQUESTED
            if (isset($_GET['start_import']) && $_GET['start_import'] == 1 && $_GET['module'] == 'sitepageevent') {

                //ACTIVITY FEED IMPORT
                $activity_sitepageevent = $this->_getParam('activity_sitepageevent');

                //START FETCH CATEGORY WORK
                $selectSiteeventCategory = $siteeventCategoryTable->select()
                        ->from($siteeventCategoryTableName, 'category_name')
                        ->where('category_name != ?', '')
                        ->where('cat_dependency = ?', 0);
                $siteeventCategoryDatas = $siteeventCategoryTable->fetchAll($selectSiteeventCategory);
                if (!empty($siteeventCategoryDatas)) {
                    $siteeventCategoryDatas = $siteeventCategoryDatas->toArray();
                }

                $siteeventCategoryInArrayData = array();
                foreach ($siteeventCategoryDatas as $siteeventCategoryData) {
                    $siteeventCategoryInArrayData[] = $siteeventCategoryData['category_name'];
                }

                if (!empty($sitepageeventCategoryDatas)) {
                    $sitepageeventCategoryDatas = $sitepageeventCategoryDatas->toArray();
                    foreach ($sitepageeventCategoryDatas as $sitepageeventCategoryData) {
                        if (!in_array($sitepageeventCategoryData['title'], $siteeventCategoryInArrayData)) {
                            $newCategory = $siteeventCategoryTable->createRow();
                            $newCategory->category_name = $sitepageeventCategoryData['title'];
                            $newCategory->cat_dependency = 0;
                            $newCategory->cat_order = 9999;
                            $newCategory->save();
                        }
                    }
                }

                $other_category_id = $siteeventCategoryTable->select()
                        ->from($siteeventCategoryTableName, 'category_id')
                        ->where('category_name = ?', 'Others')
                        ->where('cat_dependency = ?', 0)
                        ->query()
                        ->fetchColumn();

                if (empty($other_category_id)) {
                    $newCategory = $siteeventCategoryTable->createRow();
                    $newCategory->category_name = 'Others';
                    $newCategory->cat_dependency = 0;
                    $newCategory->cat_order = 9999;
                    $other_category_id = $newCategory->save();
                }

                // CREATE CATEGORIES DEFAULT PAGES
                $categoryIds = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategoriesArray(array('cat_dependency' => 0, 'subcat_dependency' => 0));
                Engine_Api::_()->siteevent()->categoriesPageCreate($categoryIds);

                //START EVENT IMPORTING
                $selectEvents = $sitepageeventTable->select()
                        ->where('event_id >= ?', $sitepageevent_assigned_previous_id)
                        ->from($sitepageeventTableName, 'event_id')
                        ->where('is_event_import != ?', 1)
                        ->order('event_id ASC');
                $sitepageeventDatas = $sitepageeventTable->fetchAll($selectEvents);

                $next_import_count = 0;
                $sitepageeventDatasArray = $sitepageeventDatas->toArray();
                if ($sitepageeventDatasArray) {
                    foreach ($sitepageeventDatas as $sitepageeventData) {
                        $db = Engine_Db_Table::getDefaultAdapter();
                        $db->beginTransaction();
                        try {
                            $sitepageevent_id = $sitepageeventData->event_id;
                            if (!empty($sitepageevent_id)) {
                                $sitepageevent = Engine_Api::_()->getItem('sitepageevent_event', $sitepageevent_id);
                                $siteevent = $siteeventTable->createRow();
                                $siteevent->title = $sitepageevent->title;
                                $siteevent->body = $sitepageevent->description;
                                $siteevent->owner_id = $sitepageevent->user_id;
                                $siteevent->parent_type = 'sitepage_page';
                                $siteevent->parent_id = $sitepageevent->page_id;
                                $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepageevent->page_id);
                                //START FETCH LIST CATEGORY AND SUB-CATEGORY
                                if (!empty($sitepageevent->category_id)) {
                                    $sitepageeventCategory = $sitepageeventCategoryTable->fetchRow(array('category_id = ?' => $sitepageevent->category_id));
                                    if (!empty($sitepageeventCategory)) {
                                        $sitepageeventCategoryName = $sitepageeventCategory->title;
                                        if (!empty($sitepageeventCategoryName)) {
                                            $siteeventCategory = $siteeventCategoryTable->fetchRow(array('category_name = ?' => $sitepageeventCategoryName, 'cat_dependency = ?' => 0));
                                            if (!empty($siteeventCategory)) {
                                                $siteeventCategoryId = $siteevent->category_id = $siteeventCategory->category_id;
                                            }
                                        }
                                    }
                                } else {
                                    $siteevent->category_id = $other_category_id;
                                }
                                //END FETCH LIST CATEGORY AND SUB-CATEGORY
                                $siteevent->creation_date = $sitepageevent->creation_date;
                                $siteevent->modified_date = $sitepageevent->modified_date;
                                $siteevent->save();
                                $siteevent->creation_date = $sitepageevent->creation_date;
                                $siteevent->modified_date = $sitepageevent->modified_date;
                                $siteevent->view_count = 1;
                                if ($sitepageevent->view_count > 0) {
                                    $siteevent->view_count = $sitepageevent->view_count;
                                }
                                $siteevent->search = $sitepageevent->search;
                                $siteevent->approval = $sitepageevent->approval;
                                $siteevent->member_count = $sitepageevent->member_count;
                                $siteevent->location = $sitepageevent->location;

                                if ($sitepageevent->host) {
                                    $organizer = $organizersTable->getOrganizer(array('creator_id' => $sitepageevent->user_id, 'equal_title' => $sitepageevent->host));
                                    if (empty($organizer)) {
                                        $organizer = $organizersTable->createRow();
                                        $organizer->title = $sitepageevent->host;
                                        $organizer->creator_id = $sitepageevent->user_id;
                                        $organizer->save();
                                    }
                                    $siteevent->host_type = $organizer->getType();
                                    $siteevent->host_id = $organizer->getIdentity();
                                } else {
                                    $siteevent->host_type = $sitepageevent->getOwner()->getType();
                                    $siteevent->host_id = $sitepageevent->user_id;
                                }
                                if(isset($siteevent->capacity) && isset($sitepageevent->capacity)) {
                                    $siteevent->capacity = $sitepageevent->capacity;
                                }
                                $siteevent->save();

                                $siteevent->approved = 1;
                                $siteevent->featured = $sitepageevent->featured;
                                $siteevent->sponsored = 0;
                                $siteevent->newlabel = 0;
                                $siteevent->approved_date = date('Y-m-d H:i:s');

                                //FATCH SITEEVENT CATEGORIES
                                $categoryIdsArray = array();
                                $categoryIdsArray[] = $siteevent->category_id;
                                $categoryIdsArray[] = $siteevent->subcategory_id;
                                $categoryIdsArray[] = $siteevent->subsubcategory_id;
                                $siteevent->profile_type = $siteeventCategoryTable->getProfileType($categoryIdsArray, 0, 'profile_type');
                                $siteevent->save();
                                $siteevent->setLocation();
                                $siteevent->repeat_params = '';
                                $sitepageevent->is_event_import = 1;
                                $sitepageevent->save();
                                $next_import_count++;
                                //END GET DATA FROM EVENT

                                $leaderList = $siteevent->getLeaderList();
                                $values = array();

                                //START FETCH PRIVACY
                                $auth = Engine_Api::_()->authorization()->context;

                                //START VIEW PRIVACY
                                $rolesEvents = array('leader' => 'owner', 'parent_member' => 'member', 'registered' => 'registered', 'everyone' => 'everyone');
                                foreach ($rolesEvents as $key => $rolesEvent) {
                                    if ($auth->isAllowed($sitepage, $rolesEvent, 'view')) {
                                        $values['auth_view'] = $key;
                                    }
                                }

                                if (empty($values['auth_view']))
                                    $values['auth_view'] = 'everyone';
                                $rolesSiteevents = array('leader', 'member', 'parent_member', 'registered', 'everyone');
                                $viewMax = array_search($values['auth_view'], $rolesSiteevents);

                                foreach ($rolesSiteevents as $i => $rolesSiteevent) {
                                    if ($rolesSiteevent === 'leader') {
                                        $rolesSiteevent = $leaderList;
                                    }

                                    $auth->setAllowed($siteevent, $rolesSiteevent, 'view', ($i <= $viewMax));
                                }
                                //END VIEW PRIVACY
                                //START COMMENT PRIVACY
                                $rolesEvents = array('leader' => 'owner', 'parent_member' => 'member', 'registered' => 'registered', 'everyone' => 'everyone');
                                foreach ($rolesEvents as $key => $rolesEvent) {
                                    if ($auth->isAllowed($sitepage, $rolesEvent, 'comment')) {
                                        $values['auth_comment'] = $key;
                                    }
                                }
                                if (empty($values['auth_comment']))
                                    $values['auth_comment'] = 'everyone';
                                $rolesSiteevents = array('leader', 'member', 'parent_member', 'registered', 'everyone');
                                $commentMax = array_search($values['auth_comment'], $rolesSiteevents);
                                foreach ($rolesSiteevents as $i => $rolesSiteevent) {
                                    if ($rolesSiteevent === 'leader') {
                                        $rolesSiteevent = $leaderList;
                                    }
                                    $auth->setAllowed($siteevent, $rolesSiteevent, 'comment', ($i <= $commentMax));
                                }
                                //END COMMENT PRIVACY
                                //GET THE PAGE ADMIN LIST.
                                $ownerList = $sitepage->getPageOwnerList();

                                //START PHOTO PRIVACY
                                $rolesEvents = array('leader' => 'owner', 'like_member' => 'like_member', 'parent_member' => 'member', 'registered' => 'registered');
                                foreach ($rolesEvents as $key => $rolesEvent) {
                                    $roleString = $key;
                                    if ($rolesEvent === 'like_member' && $ownerList) {
                                        $rolesEvent = $ownerList;
                                    }

                                    $sitepageAllow = Engine_Api::_()->getApi('allow', 'sitepage');
                                    if ($sitepageAllow->isAllowed($sitepage, $rolesEvent, 'spcreate')) {
                                        $values['auth_photo'] = $roleString;
                                    }
                                }

                                if (empty($values['auth_photo']))
                                    $values['auth_photo'] = 'owner';
                                $rolesSiteevents = array('leader', 'member', 'like_member', 'parent_member', 'registered');
                                $photoMax = array_search($values['auth_photo'], $rolesSiteevents);
                                $getContentOwnerList = 'get' . ucfirst($sitepage->getShortType()) . 'OwnerList';
                                $ownerList = $sitepage->$getContentOwnerList();
                                foreach ($rolesSiteevents as $i => $rolesSiteevent) {
                                    if ($rolesSiteevent === 'leader') {
                                        $rolesSiteevent = $leaderList;
                                    }
                                    if ($rolesSiteevent === 'like_member' && $ownerList) {
                                        $rolesSiteevent = $ownerList;
                                    }
                                    $auth->setAllowed($siteevent, $rolesSiteevent, 'photo', ($i <= $photoMax));
                                }
                                //END PHOTO PRIVACY
                                //START TOPIC PRIVACY
                                $rolesEvents = array("leader" => 'owner', 'like_member' => 'like_member', 'parent_member' => 'member', 'registered' => 'registered');
                                foreach ($rolesEvents as $key => $rolesEvent) {
                                    $roleString = $key;
                                    if ($rolesEvent === 'like_member' && $ownerList) {
                                        $rolesEvent = $ownerList;
                                    }

                                    $sitepageAllow = Engine_Api::_()->getApi('allow', 'sitepage');
                                    if ($sitepageAllow->isAllowed($sitepage, $rolesEvent, 'sdicreate')) {
                                        $values['auth_sdicreate'] = $roleString;
                                    }
                                }

                                if (empty($values['auth_sdicreate']))
                                    $values['auth_sdicreate'] = 'owner';
                                $rolesSiteevents = array('leader', 'member', 'like_member', 'parent_member', 'registered');
                                $topicMax = array_search($values['auth_sdicreate'], $rolesSiteevents);
                                $getContentOwnerList = 'get' . ucfirst($sitepage->getShortType()) . 'OwnerList';
                                $ownerList = $sitepage->$getContentOwnerList();
                                foreach ($rolesSiteevents as $i => $rolesSiteevent) {
                                    if ($rolesSiteevent === 'leader') {
                                        $rolesSiteevent = $leaderList;
                                    }
                                    if ($rolesSiteevent === 'like_member' && $ownerList) {
                                        $rolesSiteevent = $ownerList;
                                    }
                                    $auth->setAllowed($siteevent, $rolesSiteevent, 'topic', ($i <= $topicMax));
                                }
                                //END TOPIC PRIVACY
                                //START VIDEO PRIVACY
                                $rolesEvents = array('leader' => 'owner', 'like_member' => 'like_member', 'parent_member' => 'member', 'registered' => 'registered');
                                foreach ($rolesEvents as $key => $rolesEvent) {
                                    $roleString = $key;
                                    if ($rolesEvent === 'like_member' && $ownerList) {
                                        $rolesEvent = $ownerList;
                                    }
                                    $sitepageAllow = Engine_Api::_()->getApi('allow', 'sitepage');
                                    if ($sitepageAllow->isAllowed($sitepage, $rolesEvent, 'svcreate')) {
                                        $values['auth_video'] = $roleString;
                                    }
                                }

                                if (empty($values['auth_video']))
                                    $values['auth_video'] = 'owner';
                                $rolesSiteevents = array('leader', 'member', 'like_member', 'parent_member', 'registered');
                                $videoMax = array_search($values['auth_video'], $rolesSiteevents);
                                $getContentOwnerList = 'get' . ucfirst($sitepage->getShortType()) . 'OwnerList';
                                $ownerList = $sitepage->$getContentOwnerList();
                                foreach ($rolesSiteevents as $i => $rolesSiteevent) {
                                    if ($rolesSiteevent === 'leader') {
                                        $rolesSiteevent = $leaderList;
                                    }
                                    if ($rolesSiteevent === 'like_member' && $ownerList) {
                                        $rolesSiteevent = $ownerList;
                                    }
                                    $auth->setAllowed($siteevent, $rolesSiteevent, 'video', ($i <= $videoMax));
                                }
                                //END VIDEO PRIVACY

                               //START POST PRIVACY
																if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
                                $rolesSiteevents = array('leader', 'member', 'like_member','owner_member', 'owner_member_member', 'owner_network', 'registered');
                                $postMax = array_search("member", $rolesSiteevents);
                                foreach ($rolesSiteevents as $i => $rolesSiteevent) {
                                    if ($rolesSiteevent === 'leader') {
                                        $rolesSiteevent = $leaderList;
                                    }
                                    $auth->setAllowed($siteevent, $rolesSiteevent, 'post', ($i <= $postMax));
                                }
																}
																//END POST PRIVACY

                                //START DOCUMENT PRIVACY
                                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventdocument')) {
                                    $rolesEvents = array('leader' => 'owner', 'like_member' => 'like_member', 'parent_member' => 'member', 'registered' => 'registered');
                                    foreach ($rolesEvents as $key => $rolesEvent) {
                                        $roleString = $key;
                                        if ($rolesEvent === 'like_member' && $ownerList) {
                                            $rolesEvent = $ownerList;
                                        }
                                        $sitepageAllow = Engine_Api::_()->getApi('allow', 'sitepage');
                                        if ($sitepageAllow->isAllowed($sitepage, $rolesEvent, 'sdcreate')) {
                                            $values['auth_document'] = $roleString;
                                        }
                                    }

                                    if (empty($values['auth_document']))
                                        $values['auth_document'] = 'owner';
                                    $rolesSiteevents = array('leader', 'member', 'like_member', 'parent_member', 'registered');
                                    $documentMax = array_search($values['auth_document'], $rolesSiteevents);
                                    $getContentOwnerList = 'get' . ucfirst($sitepage->getShortType()) . 'OwnerList';
                                    $ownerList = $sitepage->$getContentOwnerList();
                                    foreach ($rolesSiteevents as $i => $rolesSiteevent) {
                                        if ($rolesSiteevent === 'leader') {
                                            $rolesSiteevent = $leaderList;
                                        }
                                        if ($rolesSiteevent === 'like_member' && $ownerList) {
                                            $rolesSiteevent = $ownerList;
                                        }
                                        $auth->setAllowed($siteevent, $rolesSiteevent, 'document', ($i <= $documentMax));
                                    }
                                    $auth->setAllowed($siteevent, $leaderList, 'document.edit', 1);
                                }
                                //END DOCUMENT PRIVACY
                                //SET INVITE PRIVACY
                                $auth->setAllowed($siteevent, 'member', 'invite', $auth->isAllowed($sitepageevent, 'member', 'invite'));

                                //CREATE SOME AUTH STUFF FOR ALL LEADERS
                                $auth->setAllowed($siteevent, $leaderList, 'photo.edit', 1);
                                $auth->setAllowed($siteevent, $leaderList, 'topic.edit', 1);
                                $auth->setAllowed($siteevent, $leaderList, 'video.edit', 1);
                                $auth->setAllowed($siteevent, $leaderList, 'edit', 1);
                                $auth->setAllowed($siteevent, $leaderList, 'delete', 1);

                                //GENERATE ACITIVITY FEED
                                if ($activity_sitepageevent) {
                                    //START FETCH ACTIONS
                                    $selectActionId = $actionsTable->select()
                                            ->from($actionsTableName, 'action_id')
                                            ->where('object_type = ?', 'sitepage_page')
                                            ->where('object_id = ?', $sitepage->getIdentity())
                                            ->where('type =?', 'sitepageevent_new')
                                            ->query()
                                            ->fetchColumn();
                                    if ($selectActionId) {
                                        $action = Engine_Api::_()->getItem('activity_action', $selectActionId);
                                        $action->type = 'siteevent_new';
                                        $action->object_id = $siteevent->getIdentity();
                                        $action->object_type = $siteevent->getType();
                                        $action->save();
                                        $actionsTable->resetActivityBindings($action);
                                    }
                                    //END FETCH ACTIONS
                                    //START FETCH ACTIONS
                                    $selectActionId = $actionsTable->select()
                                            ->from($actionsTableName, 'action_id')
                                            ->where('object_type = ?', 'sitepageevent_event')
                                            ->where('object_id = ?', $sitepageevent_id)
                                            ->where('type =?', 'post');
                                    $selectActionIds = $actionsTable->fetchAll($selectActionId);

                                    if ($selectActionIds) {
                                        foreach ($selectActionIds as $selectActionId) {
                                            $action = Engine_Api::_()->getItem('activity_action', $selectActionId['action_id']);
                                            $settingsCoreApi = Engine_Api::_()->getApi('settings', 'core');
                                            $name = 'siteevent_event_leader_owner_sitepage_page';
                                            if (!$settingsCoreApi->$name) {
                                                $action->type = 'siteevent_post';
                                            } else {
                                                $action->type = 'siteevent_post_parent';
                                            }
                                            $action->object_id = $siteevent->getIdentity();
                                            $action->object_type = $siteevent->getType();
                                            $action->save();
                                            $actionsTable->resetActivityBindings($action);
                                        }
                                    }
                                    //END FETCH ACTIONS
                                    //START FETCH ACTIONS
                                    $selectActionId = $actionsTable->select()
                                            ->from($actionsTableName, 'action_id')
                                            ->where('object_type = ?', 'sitepageevent_event')
                                            ->where('object_id = ?', $sitepageevent_id)
                                            ->where('type =?', 'sitetagcheckin_post');
                                    $selectActionIds = $actionsTable->fetchAll($selectActionId);
                                    if ($selectActionIds) {
                                        foreach ($selectActionIds as $selectActionId) {
                                            $action = Engine_Api::_()->getItem('activity_action', $selectActionId['action_id']);
                                            $action->type = 'sitetagcheckin_post';
                                            $action->object_id = $siteevent->getIdentity();
                                            $action->object_type = $siteevent->getType();
                                            $action->save();
                                            $actionsTable->resetActivityBindings($action);
                                        }
                                    }
                                    //END FETCH ACTIONS
                                    //START FETCH ACTIONS
                                    $selectActionId = $actionsTable->select()
                                            ->from($actionsTableName, 'action_id')
                                            ->where('object_type = ?', 'sitepageevent_event')
                                            ->where('object_id = ?', $sitepageevent_id)
                                            ->where('type =?', 'like_sitepageevent_event');
                                    $selectActionIds = $actionsTable->fetchAll($selectActionId);
                                    if ($selectActionIds) {
                                        foreach ($selectActionIds as $selectActionId) {
                                            $action = Engine_Api::_()->getItem('activity_action', $selectActionId['action_id']);
                                            $action->type = 'like_siteevent_event';
                                            $action->object_id = $siteevent->getIdentity();
                                            $action->object_type = $siteevent->getType();
                                            $action->save();
                                            $actionsTable->resetActivityBindings($action);
                                        }
                                    }
                                    //END FETCH ACTIONS
                                    //UPDATE EVENT TYPE 
                                    $attachmentsTable->update(array('type' => $siteevent->getType(), 'id' => $siteevent->getIdentity()), array('type = ?' => 'sitepageevent_event', 'id =?' => $sitepageevent_id));

                                    //UPDATE PHOTO TYPE 
                                    $attachmentsTable->update(array('type' => 'siteevent_photo', 'id' => $siteevent->getIdentity()), array('type = ?' => 'sitepageevent_photo', 'id =?' => $sitepageevent_id));

                                    $conversationTable->update(array('resource_type' => $siteevent->getType(), 'resource_id' => $siteevent->getIdentity()), array('resource_type = ?' => 'sitepageevent_event', 'resource_id =?' => $sitepageevent_id));
                                }

                                $row = $siteeventOtherinfoTable->getOtherinfo($siteevent->getIdentity());
                                $overview = '';
                                if (empty($row)) {
                                    $siteeventOtherinfoTable->insert(array(
                                        'event_id' => $siteevent->getIdentity()
                                    ));
                                }

                                //INSERT IN OCCURENCE TABLE
                                $viewer = Engine_Api::_()->user()->getViewer();
                                $row_occurrence = $siteeventOccurrencesTable->createRow();
                                $oldTz = date_default_timezone_get();
                                date_default_timezone_set($viewer->timezone);
                                date_default_timezone_set($oldTz);
                                $row_occurrence->event_id = $siteevent->getIdentity();
                                $row_occurrence->starttime = date("Y-m-d H:i:s", strtotime($sitepageevent->starttime));
                                $row_occurrence->endtime = date("Y-m-d H:i:s", strtotime($sitepageevent->endtime));
                                $row_occurrence->save();
                                $occurrence_id = $row_occurrence->occurrence_id;
                                $row_occurrence->starttime = date("Y-m-d H:i:s", strtotime($sitepageevent->starttime));
                                $row_occurrence->endtime = date("Y-m-d H:i:s", strtotime($sitepageevent->endtime));
                                $row_occurrence->save();
                                //GET EVENT MEMBERS
                                $sitepageeventMembers = $sitepageeventMembershipTable->fetchAll(array('resource_id = ?' => $sitepageevent_id));
                                foreach ($sitepageeventMembers as $members) {
                                    $row_membership = $siteeventMembershipTable->createRow();
                                    $row_membership->resource_id = $siteevent->getIdentity();
                                    $row_membership->user_id = $members['user_id'];
                                    $row_membership->active = $members['active'];
                                    $row_membership->resource_approved = $members['resource_approved'];
                                    $row_membership->user_approved = $members['user_approved'];
                                    $row_membership->message = $members['message'];
                                    $row_membership->rsvp = $members['rsvp'];
                                    $row_membership->title = $members['title'];
                                    $row_membership->occurrence_id = $occurrence_id;
                                    $row_membership->save();
                                }

                                //START FETCH ACTIONS
                                if ($activity_sitepageevent) {
                                    $selectActionId = $actionsTable->select()
                                            ->from($actionsTableName, 'action_id')
                                            ->where('object_type = ?', 'sitepageevent_event')
                                            ->where('object_id = ?', $sitepageevent_id)
                                            ->where('type =?', 'sitepageevent_join');
                                    $selectActionIds = $sitepageeventMembershipTable->fetchAll($selectActionId);
                                    if ($selectActionIds) {
                                        foreach ($selectActionIds as $selectActionId) {
                                            $action = Engine_Api::_()->getItem('activity_action', $selectActionId['action_id']);
                                            $action->type = 'siteevent_join';
                                            $action->object_id = $siteevent->getIdentity();
                                            $action->object_type = $siteevent->getType();
                                            $action->params = '{"occurrence_id":"' . $occurrence_id . '"}';
                                            $action->save();
                                            $actionsTable->resetActivityBindings($action);
                                        }
                                    }
                                }
                                //END FETCH ACTIONS

                                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion')) {
                                    $sitepageeventTopicSelect = $sitepageeventTopicTable->select()
                                            ->from($sitepageeventTopicTableName)
                                            ->where('page_id = ?', $sitepage->getIdentity())
                                            ->where('resource_id = ?', $sitepageevent_id)
                                            ->where('resource_type = ?', 'sitepageevent_event');
                                    $sitepageeventTopicSelectDatas = $sitepageeventTopicTable->fetchAll($sitepageeventTopicSelect);
                                    if (!empty($sitepageeventTopicSelectDatas)) {
                                        $sitepageeventTopicSelectDatas = $sitepageeventTopicSelectDatas->toArray();

                                        foreach ($sitepageeventTopicSelectDatas as $sitepageeventTopicSelectData) {
                                            $siteeventTopic = $siteeventTopicTable->createRow();
                                            $siteeventTopic->event_id = $siteevent->getIdentity();
                                            $siteeventTopic->user_id = $sitepageeventTopicSelectData['user_id'];
                                            $siteeventTopic->title = $sitepageeventTopicSelectData['title'];
                                            $siteeventTopic->sticky = $sitepageeventTopicSelectData['sticky'];
                                            $siteeventTopic->closed = $sitepageeventTopicSelectData['closed'];
                                            $siteeventTopic->view_count = $sitepageeventTopicSelectData['view_count'];
                                            $siteeventTopic->lastpost_id = $sitepageeventTopicSelectData['lastpost_id'];
                                            $siteeventTopic->lastposter_id = $sitepageeventTopicSelectData['lastposter_id'];
                                            $siteeventTopic->save();

                                            $siteeventTopic->creation_date = $sitepageeventTopicSelectData['creation_date'];
                                            $siteeventTopic->modified_date = $sitepageeventTopicSelectData['modified_date'];
                                            $siteeventTopic->save();
                                            $topic_id = $sitepageeventTopicSelectData['topic_id'];
                                            //UPDATE TOPIC TYPE 
                                            $attachmentsTable->update(array('type' => 'siteevent_topic', 'id' => $siteeventTopic->getIdentity()), array('type = ?' => 'sitepage_topic', 'id =?' => $topic_id));
                                            if ($activity_sitepageevent) {
                                                $selectActionId = $actionsTable->select()
                                                        ->from($actionsTableName, 'action_id')
                                                        ->where('object_type = ?', 'sitepage_page')
                                                        ->where('object_id =?', $sitepage->getIdentity())
                                                        ->where('params like(?)', '{"child_id":' . $topic_id . '}')
                                                        ->where('type =?', 'sitepage_topic_create');
                                                $selectActions = $actionsTable->fetchAll($selectActionId);

                                                foreach ($selectActions as $selectAction) {
                                                    $action = Engine_Api::_()->getItem('activity_action', $selectAction['action_id']);

                                                    if ($action) {
                                                        $action->type = 'siteevent_topic_create';
                                                        $action->object_type = 'siteevent_event';
                                                        $action->object_id = $siteevent->getIdentity();
                                                        $action->params = '';
                                                        $action->save();
                                                        $actionsTable->resetActivityBindings($action);
                                                    }
                                                }
                                            }

                                            if ($activity_sitepageevent) {
                                                $selectActionId = $actionsTable->select()
                                                        ->from($actionsTableName, 'action_id')
                                                        ->where('object_type = ?', 'sitepage_page')
                                                        ->where('object_id =?', $sitepage->getIdentity())
                                                        ->where('params like(?)', '{"child_id":' . $topic_id . '}')
                                                        ->where('type =?', 'sitepage_admin_topic_create');
                                                $selectActions = $actionsTable->fetchAll($selectActionId);

                                                foreach ($selectActions as $selectAction) {
                                                    $action = Engine_Api::_()->getItem('activity_action', $selectAction['action_id']);
                                                    if ($action) {
                                                        $action->type = 'siteevent_topic_create_parent';
                                                        $action->object_type = 'siteevent_event';
                                                        $action->object_id = $siteevent->getIdentity();
                                                        $action->params = '';
                                                        $action->save();
                                                        $actionsTable->resetActivityBindings($action);
                                                    }
                                                }
                                            }

                                            //START FETCH TOPIC POST'S
                                            $sitepageeventPostSelect = $sitepageeventPostTable->select()
                                                    ->from($sitepageeventPostTableName)
                                                    ->where('topic_id = ?', $sitepageeventTopicSelectData['topic_id'])
                                                    ->where('page_id = ?', $sitepage->getIdentity());

                                            $sitepageeventPostSelectDatas = $sitepageeventPostTable->fetchAll($sitepageeventPostSelect);
                                            if (!empty($sitepageeventPostSelectDatas)) {
                                                $sitepageeventPostSelectDatas = $sitepageeventPostSelectDatas->toArray();

                                                foreach ($sitepageeventPostSelectDatas as $sitepageeventPostSelectData) {

                                                    $siteeventPost = $siteeventPostTable->createRow();
                                                    $siteeventPost->topic_id = $siteeventTopic->topic_id;
                                                    $siteeventPost->event_id = $siteevent->event_id;
                                                    $siteeventPost->user_id = $sitepageeventPostSelectData['user_id'];
                                                    $siteeventPost->body = $sitepageeventPostSelectData['body'];
                                                    $siteeventPost->creation_date = $sitepageeventPostSelectData['creation_date'];
                                                    $siteeventPost->modified_date = $sitepageeventPostSelectData['modified_date'];
                                                    $siteeventPost->save();
                                                    $siteeventPost->creation_date = $sitepageeventPostSelectData['creation_date'];
                                                    $siteeventPost->modified_date = $sitepageeventPostSelectData['modified_date'];

                                                    $siteeventPost->save();
                                                    $topic_id = $sitepageeventPostSelectData['topic_id'];
                                                    $attachmentsTable->update(array('type' => 'siteevent_post', 'id' => $siteeventPost->getIdentity()), array('type = ?' => 'sitepage_post', 'id =?' => $sitepageeventPostSelectData['post_id']));
                                                    if ($activity_sitepageevent) {
                                                        $selectActionId = $actionsTable->select()
                                                                ->from($actionsTableName, 'action_id')
                                                                ->where('object_type = ?', 'sitepage_page')
                                                                ->where('object_id =?', $sitepage->getIdentity())
                                                                ->where('params like(?)', '{"child_id":' . $topic_id . '}')
                                                                ->where('type =?', 'sitepage_topic_reply');
                                                        $selectActions = $actionsTable->fetchAll($selectActionId);

                                                        foreach ($selectActions as $selectAction) {
                                                            $action = Engine_Api::_()->getItem('activity_action', $selectAction['action_id']);

                                                            if ($action) {
                                                                $action->type = 'siteevent_topic_reply';
                                                                $action->object_type = 'siteevent_event';
                                                                $action->object_id = $siteevent->getIdentity();
                                                                $action->params = '';
                                                                $action->save();
                                                                $actionsTable->resetActivityBindings($action);
                                                                //UPDATE POST TYPE 
                                                            }
                                                        }
                                                    }

                                                    if ($activity_sitepageevent) {
                                                        $selectActionId = $actionsTable->select()
                                                                ->from($actionsTableName, 'action_id')
                                                                ->where('object_type = ?', 'sitepage_page')
                                                                ->where('object_id =?', $sitepage->getIdentity())
                                                                ->where('params like(?)', '{"child_id":' . $topic_id . '}')
                                                                ->where('type =?', 'sitepage_admin_topic_reply');
                                                        $selectActions = $actionsTable->fetchAll($selectActionId);

                                                        foreach ($selectActions as $selectAction) {
                                                            $action = Engine_Api::_()->getItem('activity_action', $selectAction['action_id']);

                                                            if ($action) {
                                                                $action->type = 'siteevent_topic_reply_parent';
                                                                $action->object_type = 'siteevent_event';
                                                                $action->object_id = $siteevent->getIdentity();
                                                                $action->params = '';
                                                                $action->save();
                                                                $actionsTable->resetActivityBindings($action);
                                                                //UPDATE POST TYPE 
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            //END FETCH TOPIC POST'S

                                            $siteeventTopic->post_count = $sitepageeventTopicSelectData['post_count'];
                                            $siteeventTopic->save();

                                            //START FETCH TOPIC WATCH
                                            $sitepageeventTopicWatchDatas = $sitepageeventTopicWatchesTable->fetchAll(array('resource_id = ?' => $sitepageevent_id));
                                            foreach ($sitepageeventTopicWatchDatas as $sitepageeventTopicWatchData) {
                                                if (!empty($sitepageeventTopicWatchData)) {
                                                    $siteeventTopicWatchSelect = $siteeventTopicWatchesTable->select()
                                                            ->from($siteeventTopicWatchesTableName)
                                                            ->where('resource_id = ?', $siteeventTopic->event_id)
                                                            ->where('topic_id = ?', $siteeventTopic->topic_id)
                                                            ->where('user_id = ?', $sitepageeventTopicWatchData->user_id);
                                                    $siteeventTopicWatchSelectDatas = $siteeventTopicWatchesTable->fetchRow($siteeventTopicWatchSelect);

                                                    if (empty($siteeventTopicWatchSelectDatas)) {
                                                        $siteeventTopicWatchesTable->insert(array(
                                                            'resource_id' => $siteeventTopic->event_id,
                                                            'topic_id' => $siteeventTopic->topic_id,
                                                            'user_id' => $sitepageeventTopicWatchData->user_id,
                                                            'watch' => $sitepageeventTopicWatchData->watch
                                                        ));
                                                    }
                                                }
                                            }
                                            //END FETCH TOPIC WATCH
                                        }
                                    }
                                }

                                //START FETCH LIKES
                                $selectLike = $likeTable->select()
                                        ->from($likeTableName, 'like_id')
                                        ->where('resource_type = ?', 'sitepageevent_event')
                                        ->where('resource_id = ?', $sitepageevent_id);
                                $selectLikeDatas = $likeTable->fetchAll($selectLike);
                                foreach ($selectLikeDatas as $selectLikeData) {
                                    $like = Engine_Api::_()->getItem('core_like', $selectLikeData->like_id);
                                    $newLikeEntry = $likeTable->createRow();
                                    $newLikeEntry->resource_type = 'siteevent_event';
                                    $newLikeEntry->resource_id = $siteevent->getIdentity();
                                    $newLikeEntry->poster_type = 'user';
                                    $newLikeEntry->poster_id = $like->poster_id;
                                    $newLikeEntry->creation_date = $like->creation_date;
                                    $newLikeEntry->save();

                                    $newLikeEntry->creation_date = $like->creation_date;
                                    $newLikeEntry->save();
                                }
                                //END FETCH LIKES
                                //START FETCH PHOTO DATA
                                $selectEventPhoto = $sitepageeventPhotoTable->select()
                                        ->from($sitepageeventPhotoTableName)
                                        ->where('event_id = ?', $sitepageevent_id);
                                $sitepageeventPhotoDatas = $sitepageeventPhotoTable->fetchAll($selectEventPhoto);

                                if (!empty($sitepageeventPhotoDatas)) {

                                    $sitepageeventPhotoDatas = $sitepageeventPhotoDatas->toArray();

                                    if (empty($sitepageevent->photo_id)) {
                                        foreach ($sitepageeventPhotoDatas as $sitepageeventPhotoData) {
                                            $sitepageevent->photo_id = $sitepageeventPhotoData['photo_id'];
                                            break;
                                        }
                                    }

                                    if (!empty($sitepageevent->photo_id)) {
                                        $sitepageeventPhotoData = $sitepageeventPhotoTable->fetchRow(array('file_id = ?' => $sitepageevent->photo_id));
                                        if (!empty($sitepageeventPhotoData)) {
                                            $storageData = $storageTable->fetchRow(array('file_id = ?' => $sitepageeventPhotoData->file_id));

                                            if (!empty($storageData) && !empty($storageData->storage_path)) {
                                                if (is_string($storageData->storage_path) && file_exists($storageData->storage_path))
                                                    $siteevent->setPhoto($storageData->storage_path);

                                                $album_id = $siteeventAlbumTable->update(array('photo_id' => $siteevent->photo_id), array('event_id = ?' => $siteevent->event_id));

                                                $siteeventProfilePhoto = Engine_Api::_()->getDbTable('photos', 'siteevent')->fetchRow(array('file_id = ?' => $siteevent->photo_id));
                                                if (!empty($siteeventProfilePhoto)) {
                                                    $siteeventProfilePhotoId = $siteeventProfilePhoto->photo_id;
                                                } else {
                                                    $siteeventProfilePhotoId = $siteevent->photo_id;
                                                }

                                                //START FETCH LIKES
                                                $selectLike = $likeTable->select()
                                                        ->from($likeTableName, 'like_id')
                                                        ->where('resource_type = ?', 'sitepageevent_photo')
                                                        ->where('resource_id = ?', $sitepageevent->photo_id);
                                                $selectLikeDatas = $likeTable->fetchAll($selectLike);
                                                foreach ($selectLikeDatas as $selectLikeData) {
                                                    $like = Engine_Api::_()->getItem('core_like', $selectLikeData->like_id);
                                                    $newLikeEntry = $likeTable->createRow();
                                                    $newLikeEntry->resource_type = 'siteevent_photo';
                                                    $newLikeEntry->resource_id = $siteeventProfilePhotoId;
                                                    $newLikeEntry->poster_type = 'user';
                                                    $newLikeEntry->poster_id = $like->poster_id;
                                                    $newLikeEntry->creation_date = $like->creation_date;
                                                    $newLikeEntry->save();

                                                    $newLikeEntry->creation_date = $like->creation_date;
                                                    $newLikeEntry->save();
                                                }
                                                //END FETCH LIKES
                                                //START FETCH COMMENTS
                                                $selectLike = $commentTable->select()
                                                        ->from($commentTableName, 'comment_id')
                                                        ->where('resource_type = ?', 'sitepageevent_photo')
                                                        ->where('resource_id = ?', $sitepageevent->photo_id);
                                                $selectLikeDatas = $commentTable->fetchAll($selectLike);
                                                foreach ($selectLikeDatas as $selectLikeData) {
                                                    $comment = Engine_Api::_()->getItem('core_comment', $selectLikeData->comment_id);

                                                    $newLikeEntry = $commentTable->createRow();
                                                    $newLikeEntry->resource_type = 'siteevent_photo';
                                                    $newLikeEntry->resource_id = $siteeventProfilePhotoId;
                                                    $newLikeEntry->poster_type = 'user';
                                                    $newLikeEntry->poster_id = $comment->poster_id;
                                                    $newLikeEntry->body = $comment->body;
                                                    $newLikeEntry->creation_date = $comment->creation_date;
                                                    $newLikeEntry->like_count = $comment->like_count;
                                                    $newLikeEntry->save();

                                                    $newLikeEntry->creation_date = $comment->creation_date;
                                                    $newLikeEntry->save();
                                                }
                                                //END FETCH COMMENTS
                                                //START FETCH TAGGER DETAIL
                                                $selectTagmaps = $tagmapsTable->select()
                                                        ->from($tagmapsTableName, 'tagmap_id')
                                                        ->where('resource_type = ?', 'sitepageevent_photo')
                                                        ->where('resource_id = ?', $sitepageevent->photo_id);
                                                $selectTagmapsDatas = $tagmapsTable->fetchAll($selectTagmaps);
                                                foreach ($selectTagmapsDatas as $selectTagmapsData) {
                                                    $tagMap = Engine_Api::_()->getItem('core_tag_map', $selectTagmapsData->tagmap_id);

                                                    $newTagmapEntry = $tagmapsTable->createRow();
                                                    $newTagmapEntry->resource_type = 'siteevent_photo';
                                                    $newTagmapEntry->resource_id = $siteeventProfilePhotoId;
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

                                        $fetchDefaultAlbum = $siteeventAlbumTable->fetchRow(array('event_id = ?' => $siteevent->event_id));
                                        if (!empty($fetchDefaultAlbum)) {

                                            $selectEventPhoto = $sitepageeventPhotoTable->select()
                                                    ->from($sitepageeventPhotoTable->info('name'))
                                                    ->where('event_id = ?', $sitepageevent_id);
                                            $sitepageeventPhotoDatas = $sitepageeventPhotoTable->fetchAll($selectEventPhoto);

                                            $order = 999;
                                            foreach ($sitepageeventPhotoDatas as $sitepageeventPhotoData) {

                                                if ($sitepageeventPhotoData['file_id'] != $sitepageevent->photo_id) {
                                                    $params = array(
                                                        'collection_id' => $fetchDefaultAlbum->album_id,
                                                        'album_id' => $fetchDefaultAlbum->album_id,
                                                        'event_id' => $siteevent->event_id,
                                                        'user_id' => $sitepageeventPhotoData['user_id'],
                                                        'order' => $order
                                                    );

                                                    $storageData = $storageTable->fetchRow(array('file_id = ?' => $sitepageeventPhotoData['file_id']));
                                                    if (!empty($storageData) && !empty($storageData->storage_path)) {
                                                        $file = array();
                                                        $file['tmp_name'] = $storageData->storage_path;
                                                        $path_array = explode('/', $file['tmp_name']);
                                                        $file['name'] = end($path_array);

                                                        $siteeventPhoto = Engine_Api::_()->siteevent()->createPhoto($params, $file);
                                                        if (!empty($siteeventPhoto)) {

                                                            $order++;

                                                            //START FETCH LIKES
                                                            $selectLike = $likeTable->select()
                                                                    ->from($likeTableName, 'like_id')
                                                                    ->where('resource_type = ?', 'sitepageevent_photo')
                                                                    ->where('resource_id = ?', $sitepageeventPhotoData['photo_id']);
                                                            $selectLikeDatas = $likeTable->fetchAll($selectLike);
                                                            foreach ($selectLikeDatas as $selectLikeData) {
                                                                $like = Engine_Api::_()->getItem('core_like', $selectLikeData->like_id);

                                                                $newLikeEntry = $likeTable->createRow();
                                                                $newLikeEntry->resource_type = 'siteevent_photo';
                                                                $newLikeEntry->resource_id = $siteeventPhoto->photo_id;
                                                                $newLikeEntry->poster_type = 'user';
                                                                $newLikeEntry->poster_id = $like->poster_id;
                                                                $newLikeEntry->creation_date = $like->creation_date;
                                                                $newLikeEntry->save();

                                                                $newLikeEntry->creation_date = $like->creation_date;
                                                                $newLikeEntry->save();
                                                            }
                                                            //END FETCH LIKES
                                                            //START FETCH COMMENTS
                                                            $selectLike = $commentTable->select()
                                                                    ->from($commentTableName, 'comment_id')
                                                                    ->where('resource_type = ?', 'sitepageevent_photo')
                                                                    ->where('resource_id = ?', $sitepageeventPhotoData['photo_id']);
                                                            $selectLikeDatas = $commentTable->fetchAll($selectLike);
                                                            foreach ($selectLikeDatas as $selectLikeData) {
                                                                $comment = Engine_Api::_()->getItem('core_comment', $selectLikeData->comment_id);

                                                                $newLikeEntry = $commentTable->createRow();
                                                                $newLikeEntry->resource_type = 'siteevent_photo';
                                                                $newLikeEntry->resource_id = $siteeventPhoto->photo_id;
                                                                $newLikeEntry->poster_type = 'user';
                                                                $newLikeEntry->poster_id = $comment->poster_id;
                                                                $newLikeEntry->body = $comment->body;
                                                                $newLikeEntry->creation_date = $comment->creation_date;
                                                                $newLikeEntry->like_count = $comment->like_count;
                                                                $newLikeEntry->save();

                                                                $newLikeEntry->creation_date = $comment->creation_date;
                                                                $newLikeEntry->save();
                                                            }
                                                            //END FETCH COMMENTS
                                                            //START FETCH TAGGER DETAIL
                                                            $selectTagmaps = $tagmapsTable->select()
                                                                    ->from($tagmapsTableName, 'tagmap_id')
                                                                    ->where('resource_type = ?', 'sitepageevent_photo')
                                                                    ->where('resource_id = ?', $sitepageeventPhotoData['photo_id']);
                                                            $selectTagmapsDatas = $tagmapsTable->fetchAll($selectTagmaps);
                                                            foreach ($selectTagmapsDatas as $selectTagmapsData) {
                                                                $tagMap = Engine_Api::_()->getItem('core_tag_map', $selectTagmapsData->tagmap_id);

                                                                $newTagmapEntry = $tagmapsTable->createRow();
                                                                $newTagmapEntry->resource_type = 'siteevent_photo';
                                                                $newTagmapEntry->resource_id = $siteeventPhoto->photo_id;
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
                                }

                                //START STYLES
                                $selectStyles = $stylesTable->select()
                                        ->from($stylesTableName, 'style')
                                        ->where('type = ?', 'sitepageevent_event')
                                        ->where('id = ?', $sitepageevent_id);
                                $selectStyleDatas = $stylesTable->fetchRow($selectStyles);
                                if (!empty($selectStyleDatas)) {
                                    $selectSiteeventStyles = $stylesTable->select()
                                            ->from($stylesTableName, 'style')
                                            ->where('type = ?', 'siteevent_event')
                                            ->where('id = ?', $siteevent->getIdentity());
                                    $selectSiteeventStyleDatas = $stylesTable->fetchRow($selectSiteeventStyles);
                                    if (empty($selectSiteeventStyleDatas)) {
                                        //CREATE
                                        $stylesTable->insert(array(
                                            'type' => 'siteevent_event',
                                            'id' => $siteevent->getIdentity(),
                                            'style' => $selectStyleDatas->style
                                        ));
                                    }
                                }
                                //END STYLES
                                //START UPDATE TOTAL LIKES IN SITEEVENT TABLE
                                $selectLikeCount = $likeTable->select()
                                        ->from($likeTableName, array('COUNT(*) AS like_count'))
                                        ->where('resource_type = ?', 'sitepageevent_event')
                                        ->where('resource_id = ?', $sitepageevent_id);
                                $selectLikeCounts = $likeTable->fetchAll($selectLikeCount);
                                if (!empty($selectLikeCounts)) {
                                    $selectLikeCounts = $selectLikeCounts->toArray();
                                    $siteevent->like_count = $selectLikeCounts[0]['like_count'];
                                    $siteevent->save();
                                }
                                //END UPDATE TOTAL LIKES IN SITEEVENT TABLES

                                if ($activity_sitepageevent) {
                                    $selectActionId = $actionsTable->select()
                                            ->from($actionsTableName, 'action_id')
                                            ->where('object_type = ?', 'sitepageevent_event')
                                            ->where('object_id = ?', $sitepageevent_id)
                                            ->where('type =?', 'sitepageevent_photo_upload')
                                            ->query()
                                            ->fetchColumn();
                                    if ($selectActionId) {
                                        $action = Engine_Api::_()->getItem('activity_action', $selectActionId);
                                        $action->type = 'siteevent_photo_upload';
                                        $action->object_id = $siteevent->getIdentity();
                                        $action->object_type = $siteevent->getType();
                                        $action->params = array_merge($action->params, array('title' => $siteevent->getTitle()));
                                        $action->save();
                                        $actionsTable->resetActivityBindings($action);
                                    }
                                }
                            }

                            //CREATE LOG ENTRY IN LOG FILE
                            if (file_exists(APPLICATION_PATH . '/temporary/log/EventToSiteeventImport.log')) {
                                $myFile = APPLICATION_PATH . '/temporary/log/EventToSiteeventImport.log';
                                $error = Zend_Registry::get('Zend_Translate')->_("can't open file");
                                $fh = fopen($myFile, 'a') or die($error);
                                $current_time = date('D, d M Y H:i:s T');
                                $siteevent_title = $siteevent->title;
                                $stringData = $this->view->translate('Event with ID ') . $sitepageevent_id . $this->view->translate(' is successfully imported into a Advanced Event with ID ') . $siteevent->event_id . $this->view->translate(' at ') . $current_time . $this->view->translate(". Title of that Event is '") . $siteevent_title . "'.\n\n";
                                fwrite($fh, $stringData);
                                fclose($fh);
                            }

                            $coreModuleTable->update(array('enabled' => 0), array('name = ?' => 'sitepageevent'));
                            Engine_Api::_()->getDbtable('content', 'core')->delete(array('name =?' => 'sitepageevent.profile-sitepageevents'));
                            Engine_Api::_()->getDbtable('content', 'core')->delete(array('name =?' => 'sitepageevent.profile-events'));
                            Engine_Api::_()->getDbtable('admincontent', 'sitepage')->delete(array('name =?' => 'sitepageevent.profile-sitepageevents'));
                            Engine_Api::_()->getDbtable('admincontent', 'sitepage')->delete(array('name =?' => 'sitepageevent.profile-events'));
                            Engine_Api::_()->getDbtable('content', 'sitepage')->delete(array('name =?' => 'sitepageevent.profile-sitepageevents'));
                            Engine_Api::_()->getDbtable('content', 'sitepage')->delete(array('name =?' => 'sitepageevent.profile-events'));
                            $db->commit();
                            $this->view->sitepageevent_assigned_previous_id = $sitepageevent_id;
                        } catch (Exception $e) {
                            $db->rollback();
                            throw($e);
                        }
                        if ($next_import_count >= 100) {
                            $this->_redirect("admin/siteevent/importevent/index?start_import=1&module=sitepageevent&recall=1&activity_sitepageevent=$activity_sitepageevent");
                        }
                    }
                } else {
                    if ($_GET['recall']) {
                        echo json_encode(array('success' => 1));
                        exit();
                    }
                }
            }
        }

        if ($sitebusinesseventEnabled && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitebusiness_business', 'item_module' => 'sitebusiness')))) {

            //GET EVENT TABLES 
            $sitebusinesseventTable = Engine_Api::_()->getDbTable('events', 'sitebusinessevent');
            $sitebusinesseventTableName = $sitebusinesseventTable->info('name');

            //GET EVENT CATEGORIES TABLE
            $sitebusinesseventCategoryTable = Engine_Api::_()->getDbtable('categories', 'sitebusinessevent');
            $sitebusinesseventCategoryTableName = $sitebusinesseventCategoryTable->info('name');

            //GET EVENT MEMBERSHIP TABLE
            $sitebusinesseventMembershipTable = Engine_Api::_()->getDbtable('membership', 'sitebusinessevent');

            //GET EVENT TOPIC TABLE
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessdiscussion')) {
                $sitebusinesseventTopicTable = Engine_Api::_()->getDbtable('topics', 'sitebusiness');
                $sitebusinesseventTopicTableName = $sitebusinesseventTopicTable->info('name');

                //GET EVENT POST TABLE
                $sitebusinesseventPostTable = Engine_Api::_()->getDbtable('posts', 'sitebusiness');
                $sitebusinesseventPostTableName = $sitebusinesseventPostTable->info('name');

                //GET EVENT TOPICWATCHES  TABLE
                $sitebusinesseventTopicWatchesTable = Engine_Api::_()->getDbtable('topicwatches', 'sitebusiness');
                $sitebusinesseventTopicWatchesTableName = $sitebusinesseventTopicWatchesTable->info('name');
            }

            //GET EVENT PHOTO TABLE
            $sitebusinesseventPhotoTable = Engine_Api::_()->getDbtable('photos', 'sitebusinessevent');
            $sitebusinesseventPhotoTableName = $sitebusinesseventPhotoTable->info('name');

            //ADD NEW COLUMN IN EVENT TABLE
            $db = Engine_Db_Table::getDefaultAdapter();
            $is_event_import = $db->query("SHOW COLUMNS FROM engine4_sitebusinessevent_events LIKE 'is_event_import'")->fetch();
            if (empty($is_event_import)) {
                $run_query = $db->query("ALTER TABLE `engine4_sitebusinessevent_events` ADD is_event_import TINYINT( 2 ) NOT NULL DEFAULT '0'");
            }

            //START IF IMPORTING IS BREAKED BY SOME REASON
            $selectEvents = $sitebusinesseventTable->select()
                    ->from($sitebusinesseventTableName, 'event_id')
                    ->where('is_event_import != ?', 1)
                    ->order('event_id ASC');
            $sitebusinesseventDatas = $sitebusinesseventTable->fetchAll($selectEvents);

            if (!empty($sitebusinesseventDatas)) {

                $flag_first_sitebusinessevent_id = 1;

                foreach ($sitebusinesseventDatas as $sitebusinesseventData) {

                    if ($flag_first_sitebusinessevent_id == 1) {
                        $this->view->first_sitebusinessevent_id = $first_sitebusinessevent_id = $sitebusinesseventData->event_id;
                    }
                    $flag_first_sitebusinessevent_id++;

                    $this->view->last_sitebusinessevent_id = $last_sitebusinessevent_id = $sitebusinesseventData->event_id;
                }

                if (isset($_GET['sitebusinessevent_assigned_previous_id'])) {
                    $this->view->sitebusinessevent_assigned_previous_id = $sitebusinessevent_assigned_previous_id = $_GET['sitebusinessevent_assigned_previous_id'];
                } else {
                    $this->view->sitebusinessevent_assigned_previous_id = $sitebusinessevent_assigned_previous_id = $first_sitebusinessevent_id;
                }
            }
            //START IMPORTING IF REQUESTED
            if (isset($_GET['start_import']) && $_GET['start_import'] == 1 && $_GET['module'] == 'sitebusinessevent') {

                //ACTIVITY FEED IMPORT
                $activity_sitebusinessevent = $this->_getParam('activity_sitebusinessevent');

                //START FETCH CATEGORY WORK
                $selectSiteeventCategory = $siteeventCategoryTable->select()
                        ->from($siteeventCategoryTableName, 'category_name')
                        ->where('category_name != ?', '')
                        ->where('cat_dependency = ?', 0);
                $siteeventCategoryDatas = $siteeventCategoryTable->fetchAll($selectSiteeventCategory);
                if (!empty($siteeventCategoryDatas)) {
                    $siteeventCategoryDatas = $siteeventCategoryDatas->toArray();
                }

                $siteeventCategoryInArrayData = array();
                foreach ($siteeventCategoryDatas as $siteeventCategoryData) {
                    $siteeventCategoryInArrayData[] = $siteeventCategoryData['category_name'];
                }

                if (!empty($sitebusinesseventCategoryDatas)) {
                    $sitebusinesseventCategoryDatas = $sitebusinesseventCategoryDatas->toArray();
                    foreach ($sitebusinesseventCategoryDatas as $sitebusinesseventCategoryData) {
                        if (!in_array($sitebusinesseventCategoryData['title'], $siteeventCategoryInArrayData)) {
                            $newCategory = $siteeventCategoryTable->createRow();
                            $newCategory->category_name = $sitebusinesseventCategoryData['title'];
                            $newCategory->cat_dependency = 0;
                            $newCategory->cat_order = 9999;
                            $newCategory->save();
                        }
                    }
                }

                $other_category_id = $siteeventCategoryTable->select()
                        ->from($siteeventCategoryTableName, 'category_id')
                        ->where('category_name = ?', 'Others')
                        ->where('cat_dependency = ?', 0)
                        ->query()
                        ->fetchColumn();

                if (empty($other_category_id)) {
                    $newCategory = $siteeventCategoryTable->createRow();
                    $newCategory->category_name = 'Others';
                    $newCategory->cat_dependency = 0;
                    $newCategory->cat_order = 9999;
                    $other_category_id = $newCategory->save();
                }

                // CREATE CATEGORIES DEFAULT PAGES
                $categoryIds = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategoriesArray(array('cat_dependency' => 0, 'subcat_dependency' => 0));
                Engine_Api::_()->siteevent()->categoriesPageCreate($categoryIds);

                //START EVENT IMPORTING
                $selectEvents = $sitebusinesseventTable->select()
                        ->where('event_id >= ?', $sitebusinessevent_assigned_previous_id)
                        ->from($sitebusinesseventTableName, 'event_id')
                        ->where('is_event_import != ?', 1)
                        ->order('event_id ASC');
                $sitebusinesseventDatas = $sitebusinesseventTable->fetchAll($selectEvents);
                $sitebusinesseventDatasArray = $sitebusinesseventDatas->toArray();
                $next_import_count = 0;
                if (!empty($sitebusinesseventDatasArray)) {
                    foreach ($sitebusinesseventDatas as $sitebusinesseventData) {
                        $db = Engine_Db_Table::getDefaultAdapter();
                        $db->beginTransaction();
                        try {
                            $sitebusinessevent_id = $sitebusinesseventData->event_id;
                            if (!empty($sitebusinessevent_id)) {
                                $sitebusinessevent = Engine_Api::_()->getItem('sitebusinessevent_event', $sitebusinessevent_id);
                                $siteevent = $siteeventTable->createRow();
                                $siteevent->title = $sitebusinessevent->title;
                                $siteevent->body = $sitebusinessevent->description;
                                $siteevent->owner_id = $sitebusinessevent->user_id;
                                $siteevent->parent_type = 'sitebusiness_business';
                                $siteevent->parent_id = $sitebusinessevent->business_id;
                                $sitebusiness = Engine_Api::_()->getItem('sitebusiness_business', $sitebusinessevent->business_id);
                                //START FETCH LIST CATEGORY AND SUB-CATEGORY
                                if (!empty($sitebusinessevent->category_id)) {
                                    $sitebusinesseventCategory = $sitebusinesseventCategoryTable->fetchRow(array('category_id = ?' => $sitebusinessevent->category_id));
                                    if (!empty($sitebusinesseventCategory)) {
                                        $sitebusinesseventCategoryName = $sitebusinesseventCategory->title;
                                        if (!empty($sitebusinesseventCategoryName)) {
                                            $siteeventCategory = $siteeventCategoryTable->fetchRow(array('category_name = ?' => $sitebusinesseventCategoryName, 'cat_dependency = ?' => 0));
                                            if (!empty($siteeventCategory)) {
                                                $siteeventCategoryId = $siteevent->category_id = $siteeventCategory->category_id;
                                            }
                                        }
                                    }
                                } else {
                                    $siteevent->category_id = $other_category_id;
                                }
                                //END FETCH LIST CATEGORY AND SUB-CATEGORY
                                $siteevent->creation_date = $sitebusinessevent->creation_date;
                                $siteevent->modified_date = $sitebusinessevent->modified_date;
                                $siteevent->save();
                                $siteevent->creation_date = $sitebusinessevent->creation_date;
                                $siteevent->modified_date = $sitebusinessevent->modified_date;
                                $siteevent->view_count = 1;
                                if ($sitebusinessevent->view_count > 0) {
                                    $siteevent->view_count = $sitebusinessevent->view_count;
                                }
                                $siteevent->search = $sitebusinessevent->search;
                                $siteevent->approval = $sitebusinessevent->approval;
                                $siteevent->member_count = $sitebusinessevent->member_count;
                                $siteevent->location = $sitebusinessevent->location;

                                if ($sitebusinessevent->host) {
                                    $organizer = $organizersTable->getOrganizer(array('creator_id' => $sitebusinessevent->user_id, 'equal_title' => $sitebusinessevent->host));
                                    if (empty($organizer)) {
                                        $organizer = $organizersTable->createRow();
                                        $organizer->title = $sitebusinessevent->host;
                                        $organizer->creator_id = $sitebusinessevent->user_id;
                                        $organizer->save();
                                    }
                                    $siteevent->host_type = $organizer->getType();
                                    $siteevent->host_id = $organizer->getIdentity();
                                } else {
                                    $siteevent->host_type = $sitebusinessevent->getOwner()->getType();
                                    $siteevent->host_id = $sitebusinessevent->user_id;
                                }
                                if(isset($siteevent->capacity) && isset($sitebusinessevent->capacity)) {
                                    $siteevent->capacity = $sitebusinessevent->capacity;
                                }
                                $siteevent->save();

                                $siteevent->approved = 1;
                                $siteevent->featured = $sitebusinessevent->featured;
                                $siteevent->sponsored = 0;
                                $siteevent->newlabel = 0;
                                $siteevent->approved_date = date('Y-m-d H:i:s');

                                //FATCH SITEEVENT CATEGORIES
                                $categoryIdsArray = array();
                                $categoryIdsArray[] = $siteevent->category_id;
                                $categoryIdsArray[] = $siteevent->subcategory_id;
                                $categoryIdsArray[] = $siteevent->subsubcategory_id;
                                $siteevent->profile_type = $siteeventCategoryTable->getProfileType($categoryIdsArray, 0, 'profile_type');
                                $siteevent->save();
                                $siteevent->setLocation();
                                $siteevent->repeat_params = '';
                                $sitebusinessevent->is_event_import = 1;
                                $sitebusinessevent->save();
                                $next_import_count++;
                                //END GET DATA FROM EVENT

                                $leaderList = $siteevent->getLeaderList();
                                $values = array();

                                //START FETCH PRIVACY
                                $auth = Engine_Api::_()->authorization()->context;

                                //START VIEW PRIVACY
                                $rolesEvents = array('leader' => 'owner', 'parent_member' => 'member', 'registered' => 'registered', 'everyone' => 'everyone');
                                foreach ($rolesEvents as $key => $rolesEvent) {
                                    if ($auth->isAllowed($sitebusiness, $rolesEvent, 'view')) {
                                        $values['auth_view'] = $key;
                                    }
                                }

                                if (empty($values['auth_view']))
                                    $values['auth_view'] = 'everyone';
                                $rolesSiteevents = array('leader', 'member', 'parent_member', 'registered', 'everyone');
                                $viewMax = array_search($values['auth_view'], $rolesSiteevents);

                                foreach ($rolesSiteevents as $i => $rolesSiteevent) {
                                    if ($rolesSiteevent === 'leader') {
                                        $rolesSiteevent = $leaderList;
                                    }

                                    $auth->setAllowed($siteevent, $rolesSiteevent, 'view', ($i <= $viewMax));
                                }
                                //END VIEW PRIVACY
                                //START COMMENT PRIVACY
                                $rolesEvents = array('leader' => 'owner', 'parent_member' => 'member', 'registered' => 'registered', 'everyone' => 'everyone');
                                foreach ($rolesEvents as $key => $rolesEvent) {
                                    if ($auth->isAllowed($sitebusiness, $rolesEvent, 'comment')) {
                                        $values['auth_comment'] = $key;
                                    }
                                }
                                if (empty($values['auth_comment']))
                                    $values['auth_comment'] = 'everyone';
                                $rolesSiteevents = array('leader', 'member', 'parent_member', 'registered', 'everyone');
                                $commentMax = array_search($values['auth_comment'], $rolesSiteevents);
                                foreach ($rolesSiteevents as $i => $rolesSiteevent) {
                                    if ($rolesSiteevent === 'leader') {
                                        $rolesSiteevent = $leaderList;
                                    }
                                    $auth->setAllowed($siteevent, $rolesSiteevent, 'comment', ($i <= $commentMax));
                                }
                                //END COMMENT PRIVACY
                                //GET THE BUSINESS ADMIN LIST.
                                $ownerList = $sitebusiness->getBusinessOwnerList();

                                //START PHOTO PRIVACY
                                $rolesEvents = array('leader' => 'owner', 'like_member' => 'like_member', 'parent_member' => 'member', 'registered' => 'registered');
                                foreach ($rolesEvents as $key => $rolesEvent) {
                                    $roleString = $key;
                                    if ($rolesEvent === 'like_member' && $ownerList) {
                                        $rolesEvent = $ownerList;
                                    }

                                    $sitebusinessAllow = Engine_Api::_()->getApi('allow', 'sitebusiness');
                                    if ($sitebusinessAllow->isAllowed($sitebusiness, $rolesEvent, 'spcreate')) {
                                        $values['auth_photo'] = $roleString;
                                    }
                                }

                                if (empty($values['auth_photo']))
                                    $values['auth_photo'] = 'owner';
                                $rolesSiteevents = array('leader', 'member', 'like_member', 'parent_member', 'registered');
                                $photoMax = array_search($values['auth_photo'], $rolesSiteevents);
                                $getContentOwnerList = 'get' . ucfirst($sitebusiness->getShortType()) . 'OwnerList';
                                $ownerList = $sitebusiness->$getContentOwnerList();
                                foreach ($rolesSiteevents as $i => $rolesSiteevent) {
                                    if ($rolesSiteevent === 'leader') {
                                        $rolesSiteevent = $leaderList;
                                    }
                                    if ($rolesSiteevent === 'like_member' && $ownerList) {
                                        $rolesSiteevent = $ownerList;
                                    }
                                    $auth->setAllowed($siteevent, $rolesSiteevent, 'photo', ($i <= $photoMax));
                                }
                                //END PHOTO PRIVACY
                                //START TOPIC PRIVACY
                                $rolesEvents = array("leader" => 'owner', 'like_member' => 'like_member', 'parent_member' => 'member', 'registered' => 'registered');
                                foreach ($rolesEvents as $key => $rolesEvent) {
                                    $roleString = $key;
                                    if ($rolesEvent === 'like_member' && $ownerList) {
                                        $rolesEvent = $ownerList;
                                    }

                                    $sitebusinessAllow = Engine_Api::_()->getApi('allow', 'sitebusiness');
                                    if ($sitebusinessAllow->isAllowed($sitebusiness, $rolesEvent, 'sdicreate')) {
                                        $values['auth_sdicreate'] = $roleString;
                                    }
                                }

                                if (empty($values['auth_sdicreate']))
                                    $values['auth_sdicreate'] = 'owner';
                                $rolesSiteevents = array('leader', 'member', 'like_member', 'parent_member', 'registered');
                                $topicMax = array_search($values['auth_sdicreate'], $rolesSiteevents);
                                $getContentOwnerList = 'get' . ucfirst($sitebusiness->getShortType()) . 'OwnerList';
                                $ownerList = $sitebusiness->$getContentOwnerList();
                                foreach ($rolesSiteevents as $i => $rolesSiteevent) {
                                    if ($rolesSiteevent === 'leader') {
                                        $rolesSiteevent = $leaderList;
                                    }
                                    if ($rolesSiteevent === 'like_member' && $ownerList) {
                                        $rolesSiteevent = $ownerList;
                                    }
                                    $auth->setAllowed($siteevent, $rolesSiteevent, 'topic', ($i <= $topicMax));
                                }
                                //END TOPIC PRIVACY
                                //START VIDEO PRIVACY
                                $rolesEvents = array('leader' => 'owner', 'like_member' => 'like_member', 'parent_member' => 'member', 'registered' => 'registered');
                                foreach ($rolesEvents as $key => $rolesEvent) {
                                    $roleString = $key;
                                    if ($rolesEvent === 'like_member' && $ownerList) {
                                        $rolesEvent = $ownerList;
                                    }
                                    $sitebusinessAllow = Engine_Api::_()->getApi('allow', 'sitebusiness');
                                    if ($sitebusinessAllow->isAllowed($sitebusiness, $rolesEvent, 'svcreate')) {
                                        $values['auth_video'] = $roleString;
                                    }
                                }

                                if (empty($values['auth_video']))
                                    $values['auth_video'] = 'owner';
                                $rolesSiteevents = array('leader', 'member', 'like_member', 'parent_member', 'registered');
                                $videoMax = array_search($values['auth_video'], $rolesSiteevents);
                                $getContentOwnerList = 'get' . ucfirst($sitebusiness->getShortType()) . 'OwnerList';
                                $ownerList = $sitebusiness->$getContentOwnerList();
                                foreach ($rolesSiteevents as $i => $rolesSiteevent) {
                                    if ($rolesSiteevent === 'leader') {
                                        $rolesSiteevent = $leaderList;
                                    }
                                    if ($rolesSiteevent === 'like_member' && $ownerList) {
                                        $rolesSiteevent = $ownerList;
                                    }
                                    $auth->setAllowed($siteevent, $rolesSiteevent, 'video', ($i <= $videoMax));
                                }
                                //END VIDEO PRIVACY

                               //START POST PRIVACY
																if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
                                $rolesSiteevents = array('leader', 'member', 'like_member','owner_member', 'owner_member_member', 'owner_network', 'registered');
                                $postMax = array_search("member", $rolesSiteevents);
                                foreach ($rolesSiteevents as $i => $rolesSiteevent) {
                                    if ($rolesSiteevent === 'leader') {
                                        $rolesSiteevent = $leaderList;
                                    }
                                    $auth->setAllowed($siteevent, $rolesSiteevent, 'post', ($i <= $postMax));
                                }
																}
																//END POST PRIVACY

                                //START DOCUMENT PRIVACY
                                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventdocument')) {
                                    $rolesEvents = array('leader' => 'owner', 'like_member' => 'like_member', 'parent_member' => 'member', 'registered' => 'registered');
                                    foreach ($rolesEvents as $key => $rolesEvent) {
                                        $roleString = $key;
                                        if ($rolesEvent === 'like_member' && $ownerList) {
                                            $rolesEvent = $ownerList;
                                        }
                                        $sitebusinessAllow = Engine_Api::_()->getApi('allow', 'sitebusiness');
                                        if ($sitebusinessAllow->isAllowed($sitebusiness, $rolesEvent, 'sdcreate')) {
                                            $values['auth_document'] = $roleString;
                                        }
                                    }

                                    if (empty($values['auth_document']))
                                        $values['auth_document'] = 'owner';
                                    $rolesSiteevents = array('leader', 'member', 'like_member', 'parent_member', 'registered');
                                    $documentMax = array_search($values['auth_document'], $rolesSiteevents);
                                    $getContentOwnerList = 'get' . ucfirst($sitebusiness->getShortType()) . 'OwnerList';
                                    $ownerList = $sitebusiness->$getContentOwnerList();
                                    foreach ($rolesSiteevents as $i => $rolesSiteevent) {
                                        if ($rolesSiteevent === 'leader') {
                                            $rolesSiteevent = $leaderList;
                                        }
                                        if ($rolesSiteevent === 'like_member' && $ownerList) {
                                            $rolesSiteevent = $ownerList;
                                        }
                                        $auth->setAllowed($siteevent, $rolesSiteevent, 'document', ($i <= $documentMax));
                                    }
                                    $auth->setAllowed($siteevent, $leaderList, 'document.edit', 1);
                                }
                                //END DOCUMENT PRIVACY
                                //SET INVITE PRIVACY
                                $auth->setAllowed($siteevent, 'member', 'invite', $auth->isAllowed($sitebusinessevent, 'member', 'invite'));

                                //CREATE SOME AUTH STUFF FOR ALL LEADERS
                                $auth->setAllowed($siteevent, $leaderList, 'photo.edit', 1);
                                $auth->setAllowed($siteevent, $leaderList, 'topic.edit', 1);
                                $auth->setAllowed($siteevent, $leaderList, 'video.edit', 1);
                                $auth->setAllowed($siteevent, $leaderList, 'edit', 1);
                                $auth->setAllowed($siteevent, $leaderList, 'delete', 1);

                                //GENERATE ACITIVITY FEED
                                if ($activity_sitebusinessevent) {
                                    //START FETCH ACTIONS
                                    $selectActionId = $actionsTable->select()
                                            ->from($actionsTableName, 'action_id')
                                            ->where('object_type = ?', 'sitebusiness_business')
                                            ->where('object_id = ?', $sitebusiness->getIdentity())
                                            ->where('type =?', 'sitebusinessevent_new')
                                            ->query()
                                            ->fetchColumn();
                                    if ($selectActionId) {
                                        $action = Engine_Api::_()->getItem('activity_action', $selectActionId);
                                        $action->type = 'siteevent_new';
                                        $action->object_id = $siteevent->getIdentity();
                                        $action->object_type = $siteevent->getType();
                                        $action->save();
                                        $actionsTable->resetActivityBindings($action);
                                    }
                                    //END FETCH ACTIONS
                                    //START FETCH ACTIONS
                                    $selectActionId = $actionsTable->select()
                                            ->from($actionsTableName, 'action_id')
                                            ->where('object_type = ?', 'sitebusinessevent_event')
                                            ->where('object_id = ?', $sitebusinessevent_id)
                                            ->where('type =?', 'post');
                                    $selectActionIds = $actionsTable->fetchAll($selectActionId);
                                    if ($selectActionIds) {
                                        foreach ($selectActionIds as $selectActionId) {
                                            $settingsCoreApi = Engine_Api::_()->getApi('settings', 'core');
                                            $name = 'siteevent_event_leader_owner_sitebusiness_business';
                                            if (!$settingsCoreApi->$name) {
                                                $action->type = 'siteevent_post';
                                            } else {
                                                $action->type = 'siteevent_post_parent';
                                            }
                                            $action = Engine_Api::_()->getItem('activity_action', $selectActionId['action_id']);
                                            $action->type = 'siteevent_post';
                                            $action->object_id = $siteevent->getIdentity();
                                            $action->object_type = $siteevent->getType();
                                            $action->save();
                                            $actionsTable->resetActivityBindings($action);
                                        }
                                    }
                                    //END FETCH ACTIONS
                                    //START FETCH ACTIONS
                                    $selectActionId = $actionsTable->select()
                                            ->from($actionsTableName, 'action_id')
                                            ->where('object_type = ?', 'sitebusinessevent_event')
                                            ->where('object_id = ?', $sitebusinessevent_id)
                                            ->where('type =?', 'sitetagcheckin_post');
                                    $selectActionIds = $actionsTable->fetchAll($selectActionId);
                                    if ($selectActionIds) {
                                        foreach ($selectActionIds as $selectActionId) {
                                            $action = Engine_Api::_()->getItem('activity_action', $selectActionId['action_id']);
                                            $action->type = 'sitetagcheckin_post';
                                            $action->object_id = $siteevent->getIdentity();
                                            $action->object_type = $siteevent->getType();
                                            $action->save();
                                            $actionsTable->resetActivityBindings($action);
                                        }
                                    }
                                    //END FETCH ACTIONS
                                    //START FETCH ACTIONS
                                    $selectActionId = $actionsTable->select()
                                            ->from($actionsTableName, 'action_id')
                                            ->where('object_type = ?', 'sitebusinessevent_event')
                                            ->where('object_id = ?', $sitebusinessevent_id)
                                            ->where('type =?', 'like_sitebusinessevent_event');
                                    $selectActionIds = $actionsTable->fetchAll($selectActionId);
                                    if ($selectActionIds) {
                                        foreach ($selectActionIds as $selectActionId) {
                                            $action = Engine_Api::_()->getItem('activity_action', $selectActionId['action_id']);
                                            $action->type = 'like_siteevent_event';
                                            $action->object_id = $siteevent->getIdentity();
                                            $action->object_type = $siteevent->getType();
                                            $action->save();
                                            $actionsTable->resetActivityBindings($action);
                                        }
                                    }
                                    //END FETCH ACTIONS
                                    //UPDATE EVENT TYPE 
                                    $attachmentsTable->update(array('type' => $siteevent->getType(), 'id' => $siteevent->getIdentity()), array('type = ?' => 'sitebusinessevent_event', 'id =?' => $sitebusinessevent_id));

                                    $conversationTable->update(array('resource_type' => $siteevent->getType(), 'resource_id' => $siteevent->getIdentity()), array('resource_type = ?' => 'sitebusinessevent_event', 'resource_id =?' => $sitebusinessevent_id));
                                }

                                $row = $siteeventOtherinfoTable->getOtherinfo($siteevent->getIdentity());
                                $overview = '';
                                if (empty($row)) {
                                    $siteeventOtherinfoTable->insert(array(
                                        'event_id' => $siteevent->getIdentity()
                                    ));
                                }

                                //INSERT IN OCCURENCE TABLE
                                $viewer = Engine_Api::_()->user()->getViewer();
                                $row_occurrence = $siteeventOccurrencesTable->createRow();
                                $oldTz = date_default_timezone_get();
                                date_default_timezone_set($viewer->timezone);
                                date_default_timezone_set($oldTz);
                                $row_occurrence->event_id = $siteevent->getIdentity();
                                $row_occurrence->starttime = date("Y-m-d H:i:s", strtotime($sitebusinessevent->starttime));
                                $row_occurrence->endtime = date("Y-m-d H:i:s", strtotime($sitebusinessevent->endtime));
                                $row_occurrence->save();
                                $occurrence_id = $row_occurrence->occurrence_id;
                                $row_occurrence->starttime = date("Y-m-d H:i:s", strtotime($sitebusinessevent->starttime));
                                $row_occurrence->endtime = date("Y-m-d H:i:s", strtotime($sitebusinessevent->endtime));
                                $row_occurrence->save();
                                //GET EVENT MEMBERS
                                $sitebusinesseventMembers = $sitebusinesseventMembershipTable->fetchAll(array('resource_id = ?' => $sitebusinessevent_id));
                                foreach ($sitebusinesseventMembers as $members) {
                                    $row_membership = $siteeventMembershipTable->createRow();
                                    $row_membership->resource_id = $siteevent->getIdentity();
                                    $row_membership->user_id = $members['user_id'];
                                    $row_membership->active = $members['active'];
                                    $row_membership->resource_approved = $members['resource_approved'];
                                    $row_membership->user_approved = $members['user_approved'];
                                    $row_membership->message = $members['message'];
                                    $row_membership->rsvp = $members['rsvp'];
                                    $row_membership->title = $members['title'];
                                    $row_membership->occurrence_id = $occurrence_id;
                                    $row_membership->save();
                                }

                                //START FETCH ACTIONS
                                if ($activity_sitebusinessevent) {
                                    $selectActionId = $actionsTable->select()
                                            ->from($actionsTableName, 'action_id')
                                            ->where('object_type = ?', 'sitebusinessevent_event')
                                            ->where('object_id = ?', $sitebusinessevent_id)
                                            ->where('type =?', 'sitebusinessevent_join');
                                    $selectActionIds = $sitebusinesseventMembershipTable->fetchAll($selectActionId);
                                    if ($selectActionIds) {
                                        foreach ($selectActionIds as $selectActionId) {
                                            $action = Engine_Api::_()->getItem('activity_action', $selectActionId['action_id']);
                                            $action->type = 'siteevent_join';
                                            $action->object_id = $siteevent->getIdentity();
                                            $action->object_type = $siteevent->getType();
                                            $action->params = '{"occurrence_id":"' . $occurrence_id . '"}';
                                            $action->save();
                                            $actionsTable->resetActivityBindings($action);
                                        }
                                    }
                                }
                                //END FETCH ACTIONS

                                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessdiscussion')) {
                                    $sitebusinesseventTopicSelect = $sitebusinesseventTopicTable->select()
                                            ->from($sitebusinesseventTopicTableName)
                                            ->where('business_id = ?', $sitebusiness->getIdentity())
                                            ->where('resource_id = ?', $sitebusinessevent_id)
                                            ->where('resource_type = ?', 'sitebusinessevent_event');
                                    $sitebusinesseventTopicSelectDatas = $sitebusinesseventTopicTable->fetchAll($sitebusinesseventTopicSelect);
                                    if (!empty($sitebusinesseventTopicSelectDatas)) {
                                        $sitebusinesseventTopicSelectDatas = $sitebusinesseventTopicSelectDatas->toArray();

                                        foreach ($sitebusinesseventTopicSelectDatas as $sitebusinesseventTopicSelectData) {
                                            $siteeventTopic = $siteeventTopicTable->createRow();
                                            $siteeventTopic->event_id = $siteevent->getIdentity();
                                            $siteeventTopic->user_id = $sitebusinesseventTopicSelectData['user_id'];
                                            $siteeventTopic->title = $sitebusinesseventTopicSelectData['title'];
                                            $siteeventTopic->sticky = $sitebusinesseventTopicSelectData['sticky'];
                                            $siteeventTopic->closed = $sitebusinesseventTopicSelectData['closed'];
                                            $siteeventTopic->view_count = $sitebusinesseventTopicSelectData['view_count'];
                                            $siteeventTopic->lastpost_id = $sitebusinesseventTopicSelectData['lastpost_id'];
                                            $siteeventTopic->lastposter_id = $sitebusinesseventTopicSelectData['lastposter_id'];
                                            $siteeventTopic->save();

                                            $siteeventTopic->creation_date = $sitebusinesseventTopicSelectData['creation_date'];
                                            $siteeventTopic->modified_date = $sitebusinesseventTopicSelectData['modified_date'];
                                            $siteeventTopic->save();
                                            $topic_id = $sitebusinesseventTopicSelectData['topic_id'];
                                            //UPDATE TOPIC TYPE 
                                            $attachmentsTable->update(array('type' => 'siteevent_topic', 'id' => $siteeventTopic->getIdentity()), array('type = ?' => 'sitebusiness_topic', 'id =?' => $topic_id));
                                            if ($activity_sitebusinessevent) {
                                                $selectActionId = $actionsTable->select()
                                                        ->from($actionsTableName, 'action_id')
                                                        ->where('object_type = ?', 'sitebusiness_business')
                                                        ->where('object_id =?', $sitebusiness->getIdentity())
                                                        ->where('params like(?)', '{"child_id":' . $topic_id . '}')
                                                        ->where('type =?', 'sitebusiness_topic_create');
                                                $selectActions = $actionsTable->fetchAll($selectActionId);

                                                foreach ($selectActions as $selectAction) {
                                                    $action = Engine_Api::_()->getItem('activity_action', $selectAction['action_id']);

                                                    if ($action) {
                                                        $action->type = 'siteevent_topic_create';
                                                        $action->object_type = 'siteevent_event';
                                                        $action->object_id = $siteevent->getIdentity();
                                                        $action->params = '';
                                                        $action->save();
                                                        $actionsTable->resetActivityBindings($action);
                                                    }
                                                }
                                            }

                                            if ($activity_sitebusinessevent) {
                                                $selectActionId = $actionsTable->select()
                                                        ->from($actionsTableName, 'action_id')
                                                        ->where('object_type = ?', 'sitebusiness_business')
                                                        ->where('object_id =?', $sitebusiness->getIdentity())
                                                        ->where('params like(?)', '{"child_id":' . $topic_id . '}')
                                                        ->where('type =?', 'sitebusiness_admin_topic_create');
                                                $selectActions = $actionsTable->fetchAll($selectActionId);

                                                foreach ($selectActions as $selectAction) {
                                                    $action = Engine_Api::_()->getItem('activity_action', $selectAction['action_id']);

                                                    if ($action) {
                                                        $action->type = 'siteevent_topic_create_parent';
                                                        $action->object_type = 'siteevent_event';
                                                        $action->object_id = $siteevent->getIdentity();
                                                        $action->params = '';
                                                        $action->save();
                                                        $actionsTable->resetActivityBindings($action);
                                                    }
                                                }
                                            }


                                            //START FETCH TOPIC POST'S
                                            $sitebusinesseventPostSelect = $sitebusinesseventPostTable->select()
                                                    ->from($sitebusinesseventPostTableName)
                                                    ->where('topic_id = ?', $sitebusinesseventTopicSelectData['topic_id'])
                                                    ->where('business_id = ?', $sitebusiness->getIdentity());

                                            $sitebusinesseventPostSelectDatas = $sitebusinesseventPostTable->fetchAll($sitebusinesseventPostSelect);
                                            if (!empty($sitebusinesseventPostSelectDatas)) {
                                                $sitebusinesseventPostSelectDatas = $sitebusinesseventPostSelectDatas->toArray();

                                                foreach ($sitebusinesseventPostSelectDatas as $sitebusinesseventPostSelectData) {

                                                    $siteeventPost = $siteeventPostTable->createRow();
                                                    $siteeventPost->topic_id = $siteeventTopic->topic_id;
                                                    $siteeventPost->event_id = $siteevent->event_id;
                                                    $siteeventPost->user_id = $sitebusinesseventPostSelectData['user_id'];
                                                    $siteeventPost->body = $sitebusinesseventPostSelectData['body'];
                                                    $siteeventPost->creation_date = $sitebusinesseventPostSelectData['creation_date'];
                                                    $siteeventPost->modified_date = $sitebusinesseventPostSelectData['modified_date'];
                                                    $siteeventPost->save();
                                                    $siteeventPost->creation_date = $sitebusinesseventPostSelectData['creation_date'];
                                                    $siteeventPost->modified_date = $sitebusinesseventPostSelectData['modified_date'];

                                                    $siteeventPost->save();
                                                    $topic_id = $sitebusinesseventPostSelectData['topic_id'];
                                                    $attachmentsTable->update(array('type' => 'siteevent_post', 'id' => $siteeventPost->getIdentity()), array('type = ?' => 'sitebusiness_post', 'id =?' => $sitebusinesseventPostSelectData['post_id']));
                                                    if ($activity_sitebusinessevent) {
                                                        $selectActionId = $actionsTable->select()
                                                                ->from($actionsTableName, 'action_id')
                                                                ->where('object_type = ?', 'sitebusiness_business')
                                                                ->where('object_id =?', $sitebusiness->getIdentity())
                                                                ->where('params like(?)', '{"child_id":' . $topic_id . '}')
                                                                ->where('type =?', 'sitebusiness_admin_topic_reply');
                                                        $selectActions = $actionsTable->fetchAll($selectActionId);

                                                        foreach ($selectActions as $selectAction) {
                                                            $action = Engine_Api::_()->getItem('activity_action', $selectAction['action_id']);

                                                            if ($action) {
                                                                $action->type = 'siteevent_topic_reply_parent';
                                                                $action->object_type = 'siteevent_event';
                                                                $action->object_id = $siteevent->getIdentity();
                                                                $action->params = '';
                                                                $action->save();
                                                                $actionsTable->resetActivityBindings($action);
                                                                //UPDATE POST TYPE 
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            //END FETCH TOPIC POST'S

                                            $siteeventTopic->post_count = $sitebusinesseventTopicSelectData['post_count'];
                                            $siteeventTopic->save();

                                            //START FETCH TOPIC WATCH
                                            $sitebusinesseventTopicWatchDatas = $sitebusinesseventTopicWatchesTable->fetchAll(array('resource_id = ?' => $sitebusinessevent_id));
                                            foreach ($sitebusinesseventTopicWatchDatas as $sitebusinesseventTopicWatchData) {
                                                if (!empty($sitebusinesseventTopicWatchData)) {
                                                    $siteeventTopicWatchSelect = $siteeventTopicWatchesTable->select()
                                                            ->from($siteeventTopicWatchesTableName)
                                                            ->where('resource_id = ?', $siteeventTopic->event_id)
                                                            ->where('topic_id = ?', $siteeventTopic->topic_id)
                                                            ->where('user_id = ?', $sitebusinesseventTopicWatchData->user_id);
                                                    $siteeventTopicWatchSelectDatas = $siteeventTopicWatchesTable->fetchRow($siteeventTopicWatchSelect);

                                                    if (empty($siteeventTopicWatchSelectDatas)) {
                                                        $siteeventTopicWatchesTable->insert(array(
                                                            'resource_id' => $siteeventTopic->event_id,
                                                            'topic_id' => $siteeventTopic->topic_id,
                                                            'user_id' => $sitebusinesseventTopicWatchData->user_id,
                                                            'watch' => $sitebusinesseventTopicWatchData->watch
                                                        ));
                                                    }
                                                }
                                            }
                                            //END FETCH TOPIC WATCH
                                        }
                                    }
                                }

                                //START FETCH LIKES
                                $selectLike = $likeTable->select()
                                        ->from($likeTableName, 'like_id')
                                        ->where('resource_type = ?', 'sitebusinessevent_event')
                                        ->where('resource_id = ?', $sitebusinessevent_id);
                                $selectLikeDatas = $likeTable->fetchAll($selectLike);
                                foreach ($selectLikeDatas as $selectLikeData) {
                                    $like = Engine_Api::_()->getItem('core_like', $selectLikeData->like_id);
                                    $newLikeEntry = $likeTable->createRow();
                                    $newLikeEntry->resource_type = 'siteevent_event';
                                    $newLikeEntry->resource_id = $siteevent->getIdentity();
                                    $newLikeEntry->poster_type = 'user';
                                    $newLikeEntry->poster_id = $like->poster_id;
                                    $newLikeEntry->creation_date = $like->creation_date;
                                    $newLikeEntry->save();

                                    $newLikeEntry->creation_date = $like->creation_date;
                                    $newLikeEntry->save();
                                }
                                //END FETCH LIKES
                                //START FETCH PHOTO DATA
                                $selectEventPhoto = $sitebusinesseventPhotoTable->select()
                                        ->from($sitebusinesseventPhotoTableName)
                                        ->where('event_id = ?', $sitebusinessevent_id);
                                $sitebusinesseventPhotoDatas = $sitebusinesseventPhotoTable->fetchAll($selectEventPhoto);

                                if (!empty($sitebusinesseventPhotoDatas)) {

                                    $sitebusinesseventPhotoDatas = $sitebusinesseventPhotoDatas->toArray();

                                    if (empty($sitebusinessevent->photo_id)) {
                                        foreach ($sitebusinesseventPhotoDatas as $sitebusinesseventPhotoData) {
                                            $sitebusinessevent->photo_id = $sitebusinesseventPhotoData['photo_id'];
                                            break;
                                        }
                                    }

                                    if (!empty($sitebusinessevent->photo_id)) {
                                        $sitebusinesseventPhotoData = $sitebusinesseventPhotoTable->fetchRow(array('file_id = ?' => $sitebusinessevent->photo_id));
                                        if (!empty($sitebusinesseventPhotoData)) {
                                            $storageData = $storageTable->fetchRow(array('file_id = ?' => $sitebusinesseventPhotoData->file_id));

                                            if (!empty($storageData) && !empty($storageData->storage_path)) {

                                                if (is_string($storageData->storage_path) && file_exists($storageData->storage_path))
                                                    $siteevent->setPhoto($storageData->storage_path);

                                                $album_id = $siteeventAlbumTable->update(array('photo_id' => $siteevent->photo_id), array('event_id = ?' => $siteevent->event_id));

                                                $siteeventProfilePhoto = Engine_Api::_()->getDbTable('photos', 'siteevent')->fetchRow(array('file_id = ?' => $siteevent->photo_id));
                                                if (!empty($siteeventProfilePhoto)) {
                                                    $siteeventProfilePhotoId = $siteeventProfilePhoto->photo_id;
                                                } else {
                                                    $siteeventProfilePhotoId = $siteevent->photo_id;
                                                }

                                                //START FETCH LIKES
                                                $selectLike = $likeTable->select()
                                                        ->from($likeTableName, 'like_id')
                                                        ->where('resource_type = ?', 'sitebusinessevent_photo')
                                                        ->where('resource_id = ?', $sitebusinessevent->photo_id);
                                                $selectLikeDatas = $likeTable->fetchAll($selectLike);
                                                foreach ($selectLikeDatas as $selectLikeData) {
                                                    $like = Engine_Api::_()->getItem('core_like', $selectLikeData->like_id);
                                                    $newLikeEntry = $likeTable->createRow();
                                                    $newLikeEntry->resource_type = 'siteevent_photo';
                                                    $newLikeEntry->resource_id = $siteeventProfilePhotoId;
                                                    $newLikeEntry->poster_type = 'user';
                                                    $newLikeEntry->poster_id = $like->poster_id;
                                                    $newLikeEntry->creation_date = $like->creation_date;
                                                    $newLikeEntry->save();

                                                    $newLikeEntry->creation_date = $like->creation_date;
                                                    $newLikeEntry->save();
                                                }
                                                //END FETCH LIKES
                                                //START FETCH COMMENTS
                                                $selectLike = $commentTable->select()
                                                        ->from($commentTableName, 'comment_id')
                                                        ->where('resource_type = ?', 'sitebusinessevent_photo')
                                                        ->where('resource_id = ?', $sitebusinessevent->photo_id);
                                                $selectLikeDatas = $commentTable->fetchAll($selectLike);
                                                foreach ($selectLikeDatas as $selectLikeData) {
                                                    $comment = Engine_Api::_()->getItem('core_comment', $selectLikeData->comment_id);

                                                    $newLikeEntry = $commentTable->createRow();
                                                    $newLikeEntry->resource_type = 'siteevent_photo';
                                                    $newLikeEntry->resource_id = $siteeventProfilePhotoId;
                                                    $newLikeEntry->poster_type = 'user';
                                                    $newLikeEntry->poster_id = $comment->poster_id;
                                                    $newLikeEntry->body = $comment->body;
                                                    $newLikeEntry->creation_date = $comment->creation_date;
                                                    $newLikeEntry->like_count = $comment->like_count;
                                                    $newLikeEntry->save();

                                                    $newLikeEntry->creation_date = $comment->creation_date;
                                                    $newLikeEntry->save();
                                                }
                                                //END FETCH COMMENTS
                                                //START FETCH TAGGER DETAIL
                                                $selectTagmaps = $tagmapsTable->select()
                                                        ->from($tagmapsTableName, 'tagmap_id')
                                                        ->where('resource_type = ?', 'sitebusinessevent_photo')
                                                        ->where('resource_id = ?', $sitebusinessevent->photo_id);
                                                $selectTagmapsDatas = $tagmapsTable->fetchAll($selectTagmaps);
                                                foreach ($selectTagmapsDatas as $selectTagmapsData) {
                                                    $tagMap = Engine_Api::_()->getItem('core_tag_map', $selectTagmapsData->tagmap_id);

                                                    $newTagmapEntry = $tagmapsTable->createRow();
                                                    $newTagmapEntry->resource_type = 'siteevent_photo';
                                                    $newTagmapEntry->resource_id = $siteeventProfilePhotoId;
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

                                        $fetchDefaultAlbum = $siteeventAlbumTable->fetchRow(array('event_id = ?' => $siteevent->event_id));
                                        if (!empty($fetchDefaultAlbum)) {

                                            $selectEventPhoto = $sitebusinesseventPhotoTable->select()
                                                    ->from($sitebusinesseventPhotoTable->info('name'))
                                                    ->where('event_id = ?', $sitebusinessevent_id);
                                            $sitebusinesseventPhotoDatas = $sitebusinesseventPhotoTable->fetchAll($selectEventPhoto);

                                            $order = 999;
                                            foreach ($sitebusinesseventPhotoDatas as $sitebusinesseventPhotoData) {

                                                if ($sitebusinesseventPhotoData['file_id'] != $sitebusinessevent->photo_id) {
                                                    $params = array(
                                                        'collection_id' => $fetchDefaultAlbum->album_id,
                                                        'album_id' => $fetchDefaultAlbum->album_id,
                                                        'event_id' => $siteevent->event_id,
                                                        'user_id' => $sitebusinesseventPhotoData['user_id'],
                                                        'order' => $order
                                                    );

                                                    $storageData = $storageTable->fetchRow(array('file_id = ?' => $sitebusinesseventPhotoData['file_id']));
                                                    if (!empty($storageData) && !empty($storageData->storage_path)) {
                                                        $file = array();
                                                        $file['tmp_name'] = $storageData->storage_path;
                                                        $path_array = explode('/', $file['tmp_name']);
                                                        $file['name'] = end($path_array);

                                                        $siteeventPhoto = Engine_Api::_()->siteevent()->createPhoto($params, $file);
                                                        if (!empty($siteeventPhoto)) {

                                                            $order++;

                                                            //START FETCH LIKES
                                                            $selectLike = $likeTable->select()
                                                                    ->from($likeTableName, 'like_id')
                                                                    ->where('resource_type = ?', 'sitebusinessevent_photo')
                                                                    ->where('resource_id = ?', $sitebusinesseventPhotoData['photo_id']);
                                                            $selectLikeDatas = $likeTable->fetchAll($selectLike);
                                                            foreach ($selectLikeDatas as $selectLikeData) {
                                                                $like = Engine_Api::_()->getItem('core_like', $selectLikeData->like_id);

                                                                $newLikeEntry = $likeTable->createRow();
                                                                $newLikeEntry->resource_type = 'siteevent_photo';
                                                                $newLikeEntry->resource_id = $siteeventPhoto->photo_id;
                                                                $newLikeEntry->poster_type = 'user';
                                                                $newLikeEntry->poster_id = $like->poster_id;
                                                                $newLikeEntry->creation_date = $like->creation_date;
                                                                $newLikeEntry->save();

                                                                $newLikeEntry->creation_date = $like->creation_date;
                                                                $newLikeEntry->save();
                                                            }
                                                            //END FETCH LIKES
                                                            //START FETCH COMMENTS
                                                            $selectLike = $commentTable->select()
                                                                    ->from($commentTableName, 'comment_id')
                                                                    ->where('resource_type = ?', 'sitebusinessevent_photo')
                                                                    ->where('resource_id = ?', $sitebusinesseventPhotoData['photo_id']);
                                                            $selectLikeDatas = $commentTable->fetchAll($selectLike);
                                                            foreach ($selectLikeDatas as $selectLikeData) {
                                                                $comment = Engine_Api::_()->getItem('core_comment', $selectLikeData->comment_id);

                                                                $newLikeEntry = $commentTable->createRow();
                                                                $newLikeEntry->resource_type = 'siteevent_photo';
                                                                $newLikeEntry->resource_id = $siteeventPhoto->photo_id;
                                                                $newLikeEntry->poster_type = 'user';
                                                                $newLikeEntry->poster_id = $comment->poster_id;
                                                                $newLikeEntry->body = $comment->body;
                                                                $newLikeEntry->creation_date = $comment->creation_date;
                                                                $newLikeEntry->like_count = $comment->like_count;
                                                                $newLikeEntry->save();

                                                                $newLikeEntry->creation_date = $comment->creation_date;
                                                                $newLikeEntry->save();
                                                            }
                                                            //END FETCH COMMENTS
                                                            //START FETCH TAGGER DETAIL
                                                            $selectTagmaps = $tagmapsTable->select()
                                                                    ->from($tagmapsTableName, 'tagmap_id')
                                                                    ->where('resource_type = ?', 'sitebusinessevent_photo')
                                                                    ->where('resource_id = ?', $sitebusinesseventPhotoData['photo_id']);
                                                            $selectTagmapsDatas = $tagmapsTable->fetchAll($selectTagmaps);
                                                            foreach ($selectTagmapsDatas as $selectTagmapsData) {
                                                                $tagMap = Engine_Api::_()->getItem('core_tag_map', $selectTagmapsData->tagmap_id);

                                                                $newTagmapEntry = $tagmapsTable->createRow();
                                                                $newTagmapEntry->resource_type = 'siteevent_photo';
                                                                $newTagmapEntry->resource_id = $siteeventPhoto->photo_id;
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
                                }

                                //START STYLES
                                $selectStyles = $stylesTable->select()
                                        ->from($stylesTableName, 'style')
                                        ->where('type = ?', 'sitebusinessevent_event')
                                        ->where('id = ?', $sitebusinessevent_id);
                                $selectStyleDatas = $stylesTable->fetchRow($selectStyles);
                                if (!empty($selectStyleDatas)) {
                                    $selectSiteeventStyles = $stylesTable->select()
                                            ->from($stylesTableName, 'style')
                                            ->where('type = ?', 'siteevent_event')
                                            ->where('id = ?', $siteevent->getIdentity());
                                    $selectSiteeventStyleDatas = $stylesTable->fetchRow($selectSiteeventStyles);
                                    if (empty($selectSiteeventStyleDatas)) {
                                        //CREATE
                                        $stylesTable->insert(array(
                                            'type' => 'siteevent_event',
                                            'id' => $siteevent->getIdentity(),
                                            'style' => $selectStyleDatas->style
                                        ));
                                    }
                                }
                                //END STYLES
                                //START UPDATE TOTAL LIKES IN SITEEVENT TABLE
                                $selectLikeCount = $likeTable->select()
                                        ->from($likeTableName, array('COUNT(*) AS like_count'))
                                        ->where('resource_type = ?', 'sitebusinessevent_event')
                                        ->where('resource_id = ?', $sitebusinessevent_id);
                                $selectLikeCounts = $likeTable->fetchAll($selectLikeCount);
                                if (!empty($selectLikeCounts)) {
                                    $selectLikeCounts = $selectLikeCounts->toArray();
                                    $siteevent->like_count = $selectLikeCounts[0]['like_count'];
                                    $siteevent->save();
                                }
                                //END UPDATE TOTAL LIKES IN SITEEVENT TABLES

                                if ($activity_sitebusinessevent) {
                                    $selectActionId = $actionsTable->select()
                                            ->from($actionsTableName, 'action_id')
                                            ->where('object_type = ?', 'sitebusinessevent_event')
                                            ->where('object_id = ?', $sitebusinessevent_id)
                                            ->where('type =?', 'sitebusinessevent_photo_upload')
                                            ->query()
                                            ->fetchColumn();
                                    if ($selectActionId) {
                                        $action = Engine_Api::_()->getItem('activity_action', $selectActionId);
                                        $action->type = 'siteevent_photo_upload';
                                        $action->object_id = $siteevent->getIdentity();
                                        $action->object_type = $siteevent->getType();
                                        $action->params = array_merge($action->params, array('title' => $siteevent->getTitle()));
                                        $action->save();
                                        $actionsTable->resetActivityBindings($action);
                                    }
                                }
                            }

                            //CREATE LOG ENTRY IN LOG FILE
                            if (file_exists(APPLICATION_PATH . '/temporary/log/EventToSiteeventImport.log')) {
                                $myFile = APPLICATION_PATH . '/temporary/log/EventToSiteeventImport.log';
                                $error = Zend_Registry::get('Zend_Translate')->_("can't open file");
                                $fh = fopen($myFile, 'a') or die($error);
                                $current_time = date('D, d M Y H:i:s T');
                                $siteevent_title = $siteevent->title;
                                $stringData = $this->view->translate('Event with ID ') . $sitebusinessevent_id . $this->view->translate(' is successfully imported into a Advanced Event with ID ') . $siteevent->event_id . $this->view->translate(' at ') . $current_time . $this->view->translate(". Title of that Event is '") . $siteevent_title . "'.\n\n";
                                fwrite($fh, $stringData);
                                fclose($fh);
                            }

                            $coreModuleTable->update(array('enabled' => 0), array('name = ?' => 'sitebusinessevent'));
                            Engine_Api::_()->getDbtable('content', 'core')->delete(array('name =?' => 'sitebusinessevent.profile-sitebusinessevents'));
                            Engine_Api::_()->getDbtable('content', 'core')->delete(array('name =?' => 'sitebusinessevent.profile-events'));
                            Engine_Api::_()->getDbtable('admincontent', 'sitebusiness')->delete(array('name =?' => 'sitebusinessevent.profile-sitebusinessevents'));
                            Engine_Api::_()->getDbtable('admincontent', 'sitebusiness')->delete(array('name =?' => 'sitebusinessevent.profile-events'));
                            Engine_Api::_()->getDbtable('content', 'sitebusiness')->delete(array('name =?' => 'sitebusinessevent.profile-sitebusinessevents'));
                            Engine_Api::_()->getDbtable('content', 'sitebusiness')->delete(array('name =?' => 'sitebusinessevent.profile-events'));
                            $db->commit();
                            $this->view->sitebusinessevent_assigned_previous_id = $sitebusinessevent_id;
                        } catch (Exception $e) {
                            $db->rollback();
                            throw($e);
                        }
                        if ($next_import_count >= 100) {
                            $this->_redirect("admin/siteevent/importevent/index?start_import=1&module=sitebusinessevent&recall=1&activity_sitebusinessevent=$activity_sitebusinessevent");
                        }
                    }
                } else {
                    if ($_GET['recall']) {
                        echo json_encode(array('success' => 1));
                        exit();
                    }
                }
            }
        }

        if ($sitegroupeventEnabled && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitegroup_group', 'item_module' => 'sitegroup')))) {

            //GET EVENT TABLES 
            $sitegroupeventTable = Engine_Api::_()->getDbTable('events', 'sitegroupevent');
            $sitegroupeventTableName = $sitegroupeventTable->info('name');

            //GET EVENT CATEGORIES TABLE
            $sitegroupeventCategoryTable = Engine_Api::_()->getDbtable('categories', 'sitegroupevent');
            $sitegroupeventCategoryTableName = $sitegroupeventCategoryTable->info('name');

            //GET EVENT MEMBERSHIP TABLE
            $sitegroupeventMembershipTable = Engine_Api::_()->getDbtable('membership', 'sitegroupevent');

            //GET EVENT TOPIC TABLE
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion')) {
                $sitegroupeventTopicTable = Engine_Api::_()->getDbtable('topics', 'sitegroup');
                $sitegroupeventTopicTableName = $sitegroupeventTopicTable->info('name');

                //GET EVENT POST TABLE
                $sitegroupeventPostTable = Engine_Api::_()->getDbtable('posts', 'sitegroup');
                $sitegroupeventPostTableName = $sitegroupeventPostTable->info('name');

                //GET EVENT TOPICWATCHES  TABLE
                $sitegroupeventTopicWatchesTable = Engine_Api::_()->getDbtable('topicwatches', 'sitegroup');
                $sitegroupeventTopicWatchesTableName = $sitegroupeventTopicWatchesTable->info('name');
            }

            //GET EVENT PHOTO TABLE
            $sitegroupeventPhotoTable = Engine_Api::_()->getDbtable('photos', 'sitegroupevent');
            $sitegroupeventPhotoTableName = $sitegroupeventPhotoTable->info('name');

            //ADD NEW COLUMN IN EVENT TABLE
            $db = Engine_Db_Table::getDefaultAdapter();
            $is_event_import = $db->query("SHOW COLUMNS FROM engine4_sitegroupevent_events LIKE 'is_event_import'")->fetch();
            if (empty($is_event_import)) {
                $run_query = $db->query("ALTER TABLE `engine4_sitegroupevent_events` ADD is_event_import TINYINT( 2 ) NOT NULL DEFAULT '0'");
            }

            //START IF IMPORTING IS BREAKED BY SOME REASON
            $selectEvents = $sitegroupeventTable->select()
                    ->from($sitegroupeventTableName, 'event_id')
                    ->where('is_event_import != ?', 1)
                    ->order('event_id ASC');
            $sitegroupeventDatas = $sitegroupeventTable->fetchAll($selectEvents);

            if (!empty($sitegroupeventDatas)) {

                $flag_first_sitegroupevent_id = 1;

                foreach ($sitegroupeventDatas as $sitegroupeventData) {

                    if ($flag_first_sitegroupevent_id == 1) {
                        $this->view->first_sitegroupevent_id = $first_sitegroupevent_id = $sitegroupeventData->event_id;
                    }
                    $flag_first_sitegroupevent_id++;

                    $this->view->last_sitegroupevent_id = $last_sitegroupevent_id = $sitegroupeventData->event_id;
                }

                if (isset($_GET['sitegroupevent_assigned_previous_id'])) {
                    $this->view->sitegroupevent_assigned_previous_id = $sitegroupevent_assigned_previous_id = $_GET['sitegroupevent_assigned_previous_id'];
                } else {
                    $this->view->sitegroupevent_assigned_previous_id = $sitegroupevent_assigned_previous_id = $first_sitegroupevent_id;
                }
            }
            //START IMPORTING IF REQUESTED
            if (isset($_GET['start_import']) && $_GET['start_import'] == 1 && $_GET['module'] == 'sitegroupevent') {

                //ACTIVITY FEED IMPORT
                $activity_sitegroupevent = $this->_getParam('activity_sitegroupevent');

                //START FETCH CATEGORY WORK
                $selectSiteeventCategory = $siteeventCategoryTable->select()
                        ->from($siteeventCategoryTableName, 'category_name')
                        ->where('category_name != ?', '')
                        ->where('cat_dependency = ?', 0);
                $siteeventCategoryDatas = $siteeventCategoryTable->fetchAll($selectSiteeventCategory);
                if (!empty($siteeventCategoryDatas)) {
                    $siteeventCategoryDatas = $siteeventCategoryDatas->toArray();
                }

                $siteeventCategoryInArrayData = array();
                foreach ($siteeventCategoryDatas as $siteeventCategoryData) {
                    $siteeventCategoryInArrayData[] = $siteeventCategoryData['category_name'];
                }

                if (!empty($sitegroupeventCategoryDatas)) {
                    $sitegroupeventCategoryDatas = $sitegroupeventCategoryDatas->toArray();
                    foreach ($sitegroupeventCategoryDatas as $sitegroupeventCategoryData) {
                        if (!in_array($sitegroupeventCategoryData['title'], $siteeventCategoryInArrayData)) {
                            $newCategory = $siteeventCategoryTable->createRow();
                            $newCategory->category_name = $sitegroupeventCategoryData['title'];
                            $newCategory->cat_dependency = 0;
                            $newCategory->cat_order = 9999;
                            $newCategory->save();
                        }
                    }
                }

                $other_category_id = $siteeventCategoryTable->select()
                        ->from($siteeventCategoryTableName, 'category_id')
                        ->where('category_name = ?', 'Others')
                        ->where('cat_dependency = ?', 0)
                        ->query()
                        ->fetchColumn();

                if (empty($other_category_id)) {
                    $newCategory = $siteeventCategoryTable->createRow();
                    $newCategory->category_name = 'Others';
                    $newCategory->cat_dependency = 0;
                    $newCategory->cat_order = 9999;
                    $other_category_id = $newCategory->save();
                }

                // CREATE CATEGORIES DEFAULT PAGES
                $categoryIds = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategoriesArray(array('cat_dependency' => 0, 'subcat_dependency' => 0));
                Engine_Api::_()->siteevent()->categoriesPageCreate($categoryIds);

                //START EVENT IMPORTING
                $selectEvents = $sitegroupeventTable->select()
                        ->where('event_id >= ?', $sitegroupevent_assigned_previous_id)
                        ->from($sitegroupeventTableName, 'event_id')
                        ->where('is_event_import != ?', 1)
                        ->order('event_id ASC');
                $sitegroupeventDatas = $sitegroupeventTable->fetchAll($selectEvents);
                $sitegroupeventDatasArray = $sitegroupeventDatas->toArray();
                $next_import_count = 0;
                if (!empty($sitegroupeventDatasArray)) {
                    foreach ($sitegroupeventDatas as $sitegroupeventData) {
                        $db = Engine_Db_Table::getDefaultAdapter();
                        $db->beginTransaction();
                        try {
                            $sitegroupevent_id = $sitegroupeventData->event_id;
                            if (!empty($sitegroupevent_id)) {
                                $sitegroupevent = Engine_Api::_()->getItem('sitegroupevent_event', $sitegroupevent_id);
                                $siteevent = $siteeventTable->createRow();
                                $siteevent->title = $sitegroupevent->title;
                                $siteevent->body = $sitegroupevent->description;
                                $siteevent->owner_id = $sitegroupevent->user_id;
                                $siteevent->parent_type = 'sitegroup_group';
                                $siteevent->parent_id = $sitegroupevent->group_id;
                                $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $sitegroupevent->group_id);
                                //START FETCH LIST CATEGORY AND SUB-CATEGORY
                                if (!empty($sitegroupevent->category_id)) {
                                    $sitegroupeventCategory = $sitegroupeventCategoryTable->fetchRow(array('category_id = ?' => $sitegroupevent->category_id));
                                    if (!empty($sitegroupeventCategory)) {
                                        $sitegroupeventCategoryName = $sitegroupeventCategory->title;
                                        if (!empty($sitegroupeventCategoryName)) {
                                            $siteeventCategory = $siteeventCategoryTable->fetchRow(array('category_name = ?' => $sitegroupeventCategoryName, 'cat_dependency = ?' => 0));
                                            if (!empty($siteeventCategory)) {
                                                $siteeventCategoryId = $siteevent->category_id = $siteeventCategory->category_id;
                                            }
                                        }
                                    }
                                } else {
                                    $siteevent->category_id = $other_category_id;
                                }
                                //END FETCH LIST CATEGORY AND SUB-CATEGORY
                                $siteevent->creation_date = $sitegroupevent->creation_date;
                                $siteevent->modified_date = $sitegroupevent->modified_date;
                                $siteevent->save();
                                $siteevent->creation_date = $sitegroupevent->creation_date;
                                $siteevent->modified_date = $sitegroupevent->modified_date;
                                $siteevent->view_count = 1;
                                if ($sitegroupevent->view_count > 0) {
                                    $siteevent->view_count = $sitegroupevent->view_count;
                                }
                                $siteevent->search = $sitegroupevent->search;
                                $siteevent->approval = $sitegroupevent->approval;
                                $siteevent->member_count = $sitegroupevent->member_count;
                                $siteevent->location = $sitegroupevent->location;

                                if ($sitegroupevent->host) {
                                    $organizer = $organizersTable->getOrganizer(array('creator_id' => $sitegroupevent->user_id, 'equal_title' => $sitegroupevent->host));
                                    if (empty($organizer)) {
                                        $organizer = $organizersTable->createRow();
                                        $organizer->title = $sitegroupevent->host;
                                        $organizer->creator_id = $sitegroupevent->user_id;
                                        $organizer->save();
                                    }
                                    $siteevent->host_type = $organizer->getType();
                                    $siteevent->host_id = $organizer->getIdentity();
                                } else {
                                    $siteevent->host_type = $sitegroupevent->getOwner()->getType();
                                    $siteevent->host_id = $sitegroupevent->user_id;
                                }
                                if(isset($siteevent->capacity) && isset($sitegroupevent->capacity)) {
                                    $siteevent->capacity = $sitegroupevent->capacity;
                                }
                                $siteevent->save();

                                $siteevent->approved = 1;
                                $siteevent->featured = $sitegroupevent->featured;
                                $siteevent->sponsored = 0;
                                $siteevent->newlabel = 0;
                                $siteevent->approved_date = date('Y-m-d H:i:s');

                                //FATCH SITEEVENT CATEGORIES
                                $categoryIdsArray = array();
                                $categoryIdsArray[] = $siteevent->category_id;
                                $categoryIdsArray[] = $siteevent->subcategory_id;
                                $categoryIdsArray[] = $siteevent->subsubcategory_id;
                                $siteevent->profile_type = $siteeventCategoryTable->getProfileType($categoryIdsArray, 0, 'profile_type');
                                $siteevent->save();
                                $siteevent->setLocation();
                                $siteevent->repeat_params = '';
                                $sitegroupevent->is_event_import = 1;
                                $sitegroupevent->save();
                                $next_import_count++;
                                //END GET DATA FROM EVENT

                                $leaderList = $siteevent->getLeaderList();
                                $values = array();

                                //START FETCH PRIVACY
                                $auth = Engine_Api::_()->authorization()->context;

                                //START VIEW PRIVACY
                                $rolesEvents = array('leader' => 'owner', 'parent_member' => 'member', 'registered' => 'registered', 'everyone' => 'everyone');
                                foreach ($rolesEvents as $key => $rolesEvent) {
                                    if ($auth->isAllowed($sitegroup, $rolesEvent, 'view')) {
                                        $values['auth_view'] = $key;
                                    }
                                }

                                if (empty($values['auth_view']))
                                    $values['auth_view'] = 'everyone';
                                $rolesSiteevents = array('leader', 'member', 'parent_member', 'registered', 'everyone');
                                $viewMax = array_search($values['auth_view'], $rolesSiteevents);

                                foreach ($rolesSiteevents as $i => $rolesSiteevent) {
                                    if ($rolesSiteevent === 'leader') {
                                        $rolesSiteevent = $leaderList;
                                    }

                                    $auth->setAllowed($siteevent, $rolesSiteevent, 'view', ($i <= $viewMax));
                                }
                                //END VIEW PRIVACY
                                //START COMMENT PRIVACY
                                $rolesEvents = array('leader' => 'owner', 'parent_member' => 'member', 'registered' => 'registered', 'everyone' => 'everyone');
                                foreach ($rolesEvents as $key => $rolesEvent) {
                                    if ($auth->isAllowed($sitegroup, $rolesEvent, 'comment')) {
                                        $values['auth_comment'] = $key;
                                    }
                                }
                                if (empty($values['auth_comment']))
                                    $values['auth_comment'] = 'everyone';
                                $rolesSiteevents = array('leader', 'member', 'parent_member', 'registered', 'everyone');
                                $commentMax = array_search($values['auth_comment'], $rolesSiteevents);
                                foreach ($rolesSiteevents as $i => $rolesSiteevent) {
                                    if ($rolesSiteevent === 'leader') {
                                        $rolesSiteevent = $leaderList;
                                    }
                                    $auth->setAllowed($siteevent, $rolesSiteevent, 'comment', ($i <= $commentMax));
                                }
                                //END COMMENT PRIVACY
                                //GET THE GROUP ADMIN LIST.
                                $ownerList = $sitegroup->getGroupOwnerList();

                                //START PHOTO PRIVACY
                                $rolesEvents = array('leader' => 'owner', 'like_member' => 'like_member', 'parent_member' => 'member', 'registered' => 'registered');
                                foreach ($rolesEvents as $key => $rolesEvent) {
                                    $roleString = $key;
                                    if ($rolesEvent === 'like_member' && $ownerList) {
                                        $rolesEvent = $ownerList;
                                    }

                                    $sitegroupAllow = Engine_Api::_()->getApi('allow', 'sitegroup');
                                    if ($sitegroupAllow->isAllowed($sitegroup, $rolesEvent, 'spcreate')) {
                                        $values['auth_photo'] = $roleString;
                                    }
                                }

                                if (empty($values['auth_photo']))
                                    $values['auth_photo'] = 'owner';
                                $rolesSiteevents = array('leader', 'member', 'like_member', 'parent_member', 'registered');
                                $photoMax = array_search($values['auth_photo'], $rolesSiteevents);
                                $getContentOwnerList = 'get' . ucfirst($sitegroup->getShortType()) . 'OwnerList';
                                $ownerList = $sitegroup->$getContentOwnerList();
                                foreach ($rolesSiteevents as $i => $rolesSiteevent) {
                                    if ($rolesSiteevent === 'leader') {
                                        $rolesSiteevent = $leaderList;
                                    }
                                    if ($rolesSiteevent === 'like_member' && $ownerList) {
                                        $rolesSiteevent = $ownerList;
                                    }
                                    $auth->setAllowed($siteevent, $rolesSiteevent, 'photo', ($i <= $photoMax));
                                }
                                //END PHOTO PRIVACY
                                //START TOPIC PRIVACY
                                $rolesEvents = array("leader" => 'owner', 'like_member' => 'like_member', 'parent_member' => 'member', 'registered' => 'registered');
                                foreach ($rolesEvents as $key => $rolesEvent) {
                                    $roleString = $key;
                                    if ($rolesEvent === 'like_member' && $ownerList) {
                                        $rolesEvent = $ownerList;
                                    }

                                    $sitegroupAllow = Engine_Api::_()->getApi('allow', 'sitegroup');
                                    if ($sitegroupAllow->isAllowed($sitegroup, $rolesEvent, 'sdicreate')) {
                                        $values['auth_sdicreate'] = $roleString;
                                    }
                                }

                                if (empty($values['auth_sdicreate']))
                                    $values['auth_sdicreate'] = 'owner';
                                $rolesSiteevents = array('leader', 'member', 'like_member', 'parent_member', 'registered');
                                $topicMax = array_search($values['auth_sdicreate'], $rolesSiteevents);
                                $getContentOwnerList = 'get' . ucfirst($sitegroup->getShortType()) . 'OwnerList';
                                $ownerList = $sitegroup->$getContentOwnerList();
                                foreach ($rolesSiteevents as $i => $rolesSiteevent) {
                                    if ($rolesSiteevent === 'leader') {
                                        $rolesSiteevent = $leaderList;
                                    }
                                    if ($rolesSiteevent === 'like_member' && $ownerList) {
                                        $rolesSiteevent = $ownerList;
                                    }
                                    $auth->setAllowed($siteevent, $rolesSiteevent, 'topic', ($i <= $topicMax));
                                }
                                //END TOPIC PRIVACY
                                //START VIDEO PRIVACY
                                $rolesEvents = array('leader' => 'owner', 'like_member' => 'like_member', 'parent_member' => 'member', 'registered' => 'registered');
                                foreach ($rolesEvents as $key => $rolesEvent) {
                                    $roleString = $key;
                                    if ($rolesEvent === 'like_member' && $ownerList) {
                                        $rolesEvent = $ownerList;
                                    }
                                    $sitegroupAllow = Engine_Api::_()->getApi('allow', 'sitegroup');
                                    if ($sitegroupAllow->isAllowed($sitegroup, $rolesEvent, 'svcreate')) {
                                        $values['auth_video'] = $roleString;
                                    }
                                }

                                if (empty($values['auth_video']))
                                    $values['auth_video'] = 'owner';
                                $rolesSiteevents = array('leader', 'member', 'like_member', 'parent_member', 'registered');
                                $videoMax = array_search($values['auth_video'], $rolesSiteevents);
                                $getContentOwnerList = 'get' . ucfirst($sitegroup->getShortType()) . 'OwnerList';
                                $ownerList = $sitegroup->$getContentOwnerList();
                                foreach ($rolesSiteevents as $i => $rolesSiteevent) {
                                    if ($rolesSiteevent === 'leader') {
                                        $rolesSiteevent = $leaderList;
                                    }
                                    if ($rolesSiteevent === 'like_member' && $ownerList) {
                                        $rolesSiteevent = $ownerList;
                                    }
                                    $auth->setAllowed($siteevent, $rolesSiteevent, 'video', ($i <= $videoMax));
                                }
                                //END VIDEO PRIVACY

                               //START POST PRIVACY
																if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
                                $rolesSiteevents = array('leader', 'member', 'like_member','owner_member', 'owner_member_member', 'owner_network', 'registered');
                                $postMax = array_search("member", $rolesSiteevents);
                                foreach ($rolesSiteevents as $i => $rolesSiteevent) {
                                    if ($rolesSiteevent === 'leader') {
                                        $rolesSiteevent = $leaderList;
                                    }
                                    $auth->setAllowed($siteevent, $rolesSiteevent, 'post', ($i <= $postMax));
                                }
																}
																//END POST PRIVACY

                                //START DOCUMENT PRIVACY
                                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventdocument')) {
                                    $rolesEvents = array('leader' => 'owner', 'like_member' => 'like_member', 'parent_member' => 'member', 'registered' => 'registered');
                                    foreach ($rolesEvents as $key => $rolesEvent) {
                                        $roleString = $key;
                                        if ($rolesEvent === 'like_member' && $ownerList) {
                                            $rolesEvent = $ownerList;
                                        }
                                        $sitegroupAllow = Engine_Api::_()->getApi('allow', 'sitegroup');
                                        if ($sitegroupAllow->isAllowed($sitegroup, $rolesEvent, 'sdcreate')) {
                                            $values['auth_document'] = $roleString;
                                        }
                                    }

                                    if (empty($values['auth_document']))
                                        $values['auth_document'] = 'owner';
                                    $rolesSiteevents = array('leader', 'member', 'like_member', 'parent_member', 'registered');
                                    $documentMax = array_search($values['auth_document'], $rolesSiteevents);
                                    $getContentOwnerList = 'get' . ucfirst($sitegroup->getShortType()) . 'OwnerList';
                                    $ownerList = $sitegroup->$getContentOwnerList();
                                    foreach ($rolesSiteevents as $i => $rolesSiteevent) {
                                        if ($rolesSiteevent === 'leader') {
                                            $rolesSiteevent = $leaderList;
                                        }
                                        if ($rolesSiteevent === 'like_member' && $ownerList) {
                                            $rolesSiteevent = $ownerList;
                                        }
                                        $auth->setAllowed($siteevent, $rolesSiteevent, 'document', ($i <= $documentMax));
                                    }
                                    $auth->setAllowed($siteevent, $leaderList, 'document.edit', 1);
                                }
                                //END DOCUMENT PRIVACY
                                //SET INVITE PRIVACY
                                $auth->setAllowed($siteevent, 'member', 'invite', $auth->isAllowed($sitegroupevent, 'member', 'invite'));

                                //CREATE SOME AUTH STUFF FOR ALL LEADERS
                                $auth->setAllowed($siteevent, $leaderList, 'photo.edit', 1);
                                $auth->setAllowed($siteevent, $leaderList, 'topic.edit', 1);
                                $auth->setAllowed($siteevent, $leaderList, 'video.edit', 1);
                                $auth->setAllowed($siteevent, $leaderList, 'edit', 1);
                                $auth->setAllowed($siteevent, $leaderList, 'delete', 1);

                                //GENERATE ACITIVITY FEED
                                if ($activity_sitegroupevent) {
                                    //START FETCH ACTIONS
                                    $selectActionId = $actionsTable->select()
                                            ->from($actionsTableName, 'action_id')
                                            ->where('object_type = ?', 'sitegroup_group')
                                            ->where('object_id = ?', $sitegroup->getIdentity())
                                            ->where('type =?', 'sitegroupevent_new')
                                            ->query()
                                            ->fetchColumn();
                                    if ($selectActionId) {
                                        $action = Engine_Api::_()->getItem('activity_action', $selectActionId);
                                        $action->type = 'siteevent_new';
                                        $action->object_id = $siteevent->getIdentity();
                                        $action->object_type = $siteevent->getType();
                                        $action->save();
                                        $actionsTable->resetActivityBindings($action);
                                    }
                                    //END FETCH ACTIONS
                                    //START FETCH ACTIONS
                                    $selectActionId = $actionsTable->select()
                                            ->from($actionsTableName, 'action_id')
                                            ->where('object_type = ?', 'sitegroupevent_event')
                                            ->where('object_id = ?', $sitegroupevent_id)
                                            ->where('type =?', 'post');
                                    $selectActionIds = $actionsTable->fetchAll($selectActionId);
                                    if ($selectActionIds) {
                                        foreach ($selectActionIds as $selectActionId) {
                                            $action = Engine_Api::_()->getItem('activity_action', $selectActionId['action_id']);
                                            $settingsCoreApi = Engine_Api::_()->getApi('settings', 'core');
                                            $name = 'siteevent_event_leader_owner_sitegroup_group';
                                            if (!$settingsCoreApi->$name) {
                                                $action->type = 'siteevent_post';
                                            } else {
                                                $action->type = 'siteevent_post_parent';
                                            }
                                            $action->object_id = $siteevent->getIdentity();
                                            $action->object_type = $siteevent->getType();
                                            $action->save();
                                            $actionsTable->resetActivityBindings($action);
                                        }
                                    }
                                    //END FETCH ACTIONS
                                    //START FETCH ACTIONS
                                    $selectActionId = $actionsTable->select()
                                            ->from($actionsTableName, 'action_id')
                                            ->where('object_type = ?', 'sitegroupevent_event')
                                            ->where('object_id = ?', $sitegroupevent_id)
                                            ->where('type =?', 'sitetagcheckin_post');
                                    $selectActionIds = $actionsTable->fetchAll($selectActionId);
                                    if ($selectActionIds) {
                                        foreach ($selectActionIds as $selectActionId) {
                                            $action = Engine_Api::_()->getItem('activity_action', $selectActionId['action_id']);
                                            $action->type = 'sitetagcheckin_post';
                                            $action->object_id = $siteevent->getIdentity();
                                            $action->object_type = $siteevent->getType();
                                            $action->save();
                                            $actionsTable->resetActivityBindings($action);
                                        }
                                    }
                                    //END FETCH ACTIONS
                                    //START FETCH ACTIONS
                                    $selectActionId = $actionsTable->select()
                                            ->from($actionsTableName, 'action_id')
                                            ->where('object_type = ?', 'sitegroupevent_event')
                                            ->where('object_id = ?', $sitegroupevent_id)
                                            ->where('type =?', 'like_sitegroupevent_event');
                                    $selectActionIds = $actionsTable->fetchAll($selectActionId);
                                    if ($selectActionIds) {
                                        foreach ($selectActionIds as $selectActionId) {
                                            $action = Engine_Api::_()->getItem('activity_action', $selectActionId['action_id']);
                                            $action->type = 'like_siteevent_event';
                                            $action->object_id = $siteevent->getIdentity();
                                            $action->object_type = $siteevent->getType();
                                            $action->save();
                                            $actionsTable->resetActivityBindings($action);
                                        }
                                    }
                                    //END FETCH ACTIONS
                                    //UPDATE EVENT TYPE 
                                    $attachmentsTable->update(array('type' => $siteevent->getType(), 'id' => $siteevent->getIdentity()), array('type = ?' => 'sitegroupevent_event', 'id =?' => $sitegroupevent_id));

                                    $conversationTable->update(array('resource_type' => $siteevent->getType(), 'resource_id' => $siteevent->getIdentity()), array('resource_type = ?' => 'sitegroupevent_event', 'resource_id =?' => $sitegroupevent_id));
                                }

                                $row = $siteeventOtherinfoTable->getOtherinfo($siteevent->getIdentity());
                                $overview = '';
                                if (empty($row)) {
                                    $siteeventOtherinfoTable->insert(array(
                                        'event_id' => $siteevent->getIdentity()
                                    ));
                                }

                                //INSERT IN OCCURENCE TABLE
                                $viewer = Engine_Api::_()->user()->getViewer();
                                $row_occurrence = $siteeventOccurrencesTable->createRow();
                                $oldTz = date_default_timezone_get();
                                date_default_timezone_set($viewer->timezone);
                                date_default_timezone_set($oldTz);
                                $row_occurrence->event_id = $siteevent->getIdentity();
                                $row_occurrence->starttime = date("Y-m-d H:i:s", strtotime($sitegroupevent->starttime));
                                $row_occurrence->endtime = date("Y-m-d H:i:s", strtotime($sitegroupevent->endtime));
                                $row_occurrence->save();
                                $occurrence_id = $row_occurrence->occurrence_id;
                                $row_occurrence->starttime = date("Y-m-d H:i:s", strtotime($sitegroupevent->starttime));
                                $row_occurrence->endtime = date("Y-m-d H:i:s", strtotime($sitegroupevent->endtime));
                                $row_occurrence->save();
                                //GET EVENT MEMBERS
                                $sitegroupeventMembers = $sitegroupeventMembershipTable->fetchAll(array('resource_id = ?' => $sitegroupevent_id));
                                foreach ($sitegroupeventMembers as $members) {
                                    $row_membership = $siteeventMembershipTable->createRow();
                                    $row_membership->resource_id = $siteevent->getIdentity();
                                    $row_membership->user_id = $members['user_id'];
                                    $row_membership->active = $members['active'];
                                    $row_membership->resource_approved = $members['resource_approved'];
                                    $row_membership->user_approved = $members['user_approved'];
                                    $row_membership->message = $members['message'];
                                    $row_membership->rsvp = $members['rsvp'];
                                    $row_membership->title = $members['title'];
                                    $row_membership->occurrence_id = $occurrence_id;
                                    $row_membership->save();
                                }

                                //START FETCH ACTIONS
                                if ($activity_sitegroupevent) {
                                    $selectActionId = $actionsTable->select()
                                            ->from($actionsTableName, 'action_id')
                                            ->where('object_type = ?', 'sitegroupevent_event')
                                            ->where('object_id = ?', $sitegroupevent_id)
                                            ->where('type =?', 'sitegroupevent_join');
                                    $selectActionIds = $sitegroupeventMembershipTable->fetchAll($selectActionId);
                                    if ($selectActionIds) {
                                        foreach ($selectActionIds as $selectActionId) {
                                            $action = Engine_Api::_()->getItem('activity_action', $selectActionId['action_id']);
                                            $action->type = 'siteevent_join';
                                            $action->object_id = $siteevent->getIdentity();
                                            $action->object_type = $siteevent->getType();
                                            $action->params = '{"occurrence_id":"' . $occurrence_id . '"}';
                                            $action->save();
                                            $actionsTable->resetActivityBindings($action);
                                        }
                                    }
                                }
                                //END FETCH ACTIONS

                                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion')) {
                                    $sitegroupeventTopicSelect = $sitegroupeventTopicTable->select()
                                            ->from($sitegroupeventTopicTableName)
                                            ->where('group_id = ?', $sitegroup->getIdentity())
                                            ->where('resource_id = ?', $sitegroupevent_id)
                                            ->where('resource_type = ?', 'sitegroupevent_event');
                                    $sitegroupeventTopicSelectDatas = $sitegroupeventTopicTable->fetchAll($sitegroupeventTopicSelect);
                                    if (!empty($sitegroupeventTopicSelectDatas)) {
                                        $sitegroupeventTopicSelectDatas = $sitegroupeventTopicSelectDatas->toArray();

                                        foreach ($sitegroupeventTopicSelectDatas as $sitegroupeventTopicSelectData) {
                                            $siteeventTopic = $siteeventTopicTable->createRow();
                                            $siteeventTopic->event_id = $siteevent->getIdentity();
                                            $siteeventTopic->user_id = $sitegroupeventTopicSelectData['user_id'];
                                            $siteeventTopic->title = $sitegroupeventTopicSelectData['title'];
                                            $siteeventTopic->sticky = $sitegroupeventTopicSelectData['sticky'];
                                            $siteeventTopic->closed = $sitegroupeventTopicSelectData['closed'];
                                            $siteeventTopic->view_count = $sitegroupeventTopicSelectData['view_count'];
                                            $siteeventTopic->lastpost_id = $sitegroupeventTopicSelectData['lastpost_id'];
                                            $siteeventTopic->lastposter_id = $sitegroupeventTopicSelectData['lastposter_id'];
                                            $siteeventTopic->creation_date = $sitegroupeventTopicSelectData['creation_date'];
                                            $siteeventTopic->modified_date = $sitegroupeventTopicSelectData['modified_date'];
                                            $siteeventTopic->save();

                                            $siteeventTopic->creation_date = $sitegroupeventTopicSelectData['creation_date'];
                                            $siteeventTopic->modified_date = $sitegroupeventTopicSelectData['modified_date'];
                                            $siteeventTopic->save();
                                            $topic_id = $sitegroupeventTopicSelectData['topic_id'];
                                            //UPDATE TOPIC TYPE 
                                            $attachmentsTable->update(array('type' => 'siteevent_topic', 'id' => $siteeventTopic->getIdentity()), array('type = ?' => 'sitegroup_topic', 'id =?' => $topic_id));
                                            if ($activity_sitegroupevent) {
                                                $selectActionId = $actionsTable->select()
                                                        ->from($actionsTableName, 'action_id')
                                                        ->where('object_type = ?', 'sitegroup_group')
                                                        ->where('object_id =?', $sitegroup->getIdentity())
                                                        ->where('params like(?)', '{"child_id":' . $topic_id . '}')
                                                        ->where('type =?', 'sitegroup_topic_create');
                                                $selectActions = $actionsTable->fetchAll($selectActionId);

                                                foreach ($selectActions as $selectAction) {
                                                    $action = Engine_Api::_()->getItem('activity_action', $selectAction['action_id']);

                                                    if ($action) {
                                                        $action->type = 'siteevent_topic_create';
                                                        $action->object_type = 'siteevent_event';
                                                        $action->object_id = $siteevent->getIdentity();
                                                        $action->params = '';
                                                        $action->save();
                                                        $actionsTable->resetActivityBindings($action);
                                                    }
                                                }
                                            }

                                            if ($activity_sitegroupevent) {
                                                $selectActionId = $actionsTable->select()
                                                        ->from($actionsTableName, 'action_id')
                                                        ->where('object_type = ?', 'sitegroup_group')
                                                        ->where('object_id =?', $sitegroup->getIdentity())
                                                        ->where('params like(?)', '{"child_id":' . $topic_id . '}')
                                                        ->where('type =?', 'sitegroup_admin_topic_create');
                                                $selectActions = $actionsTable->fetchAll($selectActionId);

                                                foreach ($selectActions as $selectAction) {
                                                    $action = Engine_Api::_()->getItem('activity_action', $selectAction['action_id']);

                                                    if ($action) {
                                                        $action->type = 'siteevent_topic_create_parent';
                                                        $action->object_type = 'siteevent_event';
                                                        $action->object_id = $siteevent->getIdentity();
                                                        $action->params = '';
                                                        $action->save();
                                                        $actionsTable->resetActivityBindings($action);
                                                    }
                                                }
                                            }

                                            //START FETCH TOPIC POST'S
                                            $sitegroupeventPostSelect = $sitegroupeventPostTable->select()
                                                    ->from($sitegroupeventPostTableName)
                                                    ->where('topic_id = ?', $sitegroupeventTopicSelectData['topic_id'])
                                                    ->where('group_id = ?', $sitegroup->getIdentity());

                                            $sitegroupeventPostSelectDatas = $sitegroupeventPostTable->fetchAll($sitegroupeventPostSelect);
                                            if (!empty($sitegroupeventPostSelectDatas)) {
                                                $sitegroupeventPostSelectDatas = $sitegroupeventPostSelectDatas->toArray();

                                                foreach ($sitegroupeventPostSelectDatas as $sitegroupeventPostSelectData) {

                                                    $siteeventPost = $siteeventPostTable->createRow();
                                                    $siteeventPost->topic_id = $siteeventTopic->topic_id;
                                                    $siteeventPost->event_id = $siteevent->event_id;
                                                    $siteeventPost->user_id = $sitegroupeventPostSelectData['user_id'];
                                                    $siteeventPost->body = $sitegroupeventPostSelectData['body'];
                                                    $siteeventPost->creation_date = $sitegroupeventPostSelectData['creation_date'];
                                                    $siteeventPost->modified_date = $sitegroupeventPostSelectData['modified_date'];
                                                    $siteeventPost->save();
                                                    $siteeventPost->creation_date = $sitegroupeventPostSelectData['creation_date'];
                                                    $siteeventPost->modified_date = $sitegroupeventPostSelectData['modified_date'];

                                                    $siteeventPost->save();
                                                    $topic_id = $sitegroupeventPostSelectData['topic_id'];
                                                    $attachmentsTable->update(array('type' => 'siteevent_post', 'id' => $siteeventPost->getIdentity()), array('type = ?' => 'sitegroup_post', 'id =?' => $sitegroupeventPostSelectData['post_id']));
                                                    if ($activity_sitegroupevent) {
                                                        $selectActionId = $actionsTable->select()
                                                                ->from($actionsTableName, 'action_id')
                                                                ->where('object_type = ?', 'sitegroup_group')
                                                                ->where('object_id =?', $sitegroup->getIdentity())
                                                                ->where('params like(?)', '{"child_id":' . $topic_id . '}')
                                                                ->where('type =?', 'sitegroup_topic_reply');
                                                        $selectActions = $actionsTable->fetchAll($selectActionId);

                                                        foreach ($selectActions as $selectAction) {
                                                            $action = Engine_Api::_()->getItem('activity_action', $selectAction['action_id']);

                                                            if ($action) {
                                                                $action->type = 'siteevent_topic_reply';
                                                                $action->object_type = 'siteevent_event';
                                                                $action->object_id = $siteevent->getIdentity();
                                                                $action->params = '';
                                                                $action->save();
                                                                $actionsTable->resetActivityBindings($action);
                                                                //UPDATE POST TYPE 
                                                            }
                                                        }
                                                    }

                                                    if ($activity_sitegroupevent) {
                                                        $selectActionId = $actionsTable->select()
                                                                ->from($actionsTableName, 'action_id')
                                                                ->where('object_type = ?', 'sitegroup_group')
                                                                ->where('object_id =?', $sitegroup->getIdentity())
                                                                ->where('params like(?)', '{"child_id":' . $topic_id . '}')
                                                                ->where('type =?', 'sitegroup_admin_topic_reply');
                                                        $selectActions = $actionsTable->fetchAll($selectActionId);

                                                        foreach ($selectActions as $selectAction) {
                                                            $action = Engine_Api::_()->getItem('activity_action', $selectAction['action_id']);

                                                            if ($action) {
                                                                $action->type = 'siteevent_topic_reply_parent';
                                                                $action->object_type = 'siteevent_event';
                                                                $action->object_id = $siteevent->getIdentity();
                                                                $action->params = '';
                                                                $action->save();
                                                                $actionsTable->resetActivityBindings($action);
                                                                //UPDATE POST TYPE 
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            //END FETCH TOPIC POST'S

                                            $siteeventTopic->post_count = $sitegroupeventTopicSelectData['post_count'];
                                            $siteeventTopic->save();

                                            //START FETCH TOPIC WATCH
                                            $sitegroupeventTopicWatchDatas = $sitegroupeventTopicWatchesTable->fetchAll(array('resource_id = ?' => $sitegroupevent_id));
                                            foreach ($sitegroupeventTopicWatchDatas as $sitegroupeventTopicWatchData) {
                                                if (!empty($sitegroupeventTopicWatchData)) {
                                                    $siteeventTopicWatchSelect = $siteeventTopicWatchesTable->select()
                                                            ->from($siteeventTopicWatchesTableName)
                                                            ->where('resource_id = ?', $siteeventTopic->event_id)
                                                            ->where('topic_id = ?', $siteeventTopic->topic_id)
                                                            ->where('user_id = ?', $sitegroupeventTopicWatchData->user_id);
                                                    $siteeventTopicWatchSelectDatas = $siteeventTopicWatchesTable->fetchRow($siteeventTopicWatchSelect);

                                                    if (empty($siteeventTopicWatchSelectDatas)) {
                                                        $siteeventTopicWatchesTable->insert(array(
                                                            'resource_id' => $siteeventTopic->event_id,
                                                            'topic_id' => $siteeventTopic->topic_id,
                                                            'user_id' => $sitegroupeventTopicWatchData->user_id,
                                                            'watch' => $sitegroupeventTopicWatchData->watch
                                                        ));
                                                    }
                                                }
                                            }
                                            //END FETCH TOPIC WATCH
                                        }
                                    }
                                }

                                //START FETCH LIKES
                                $selectLike = $likeTable->select()
                                        ->from($likeTableName, 'like_id')
                                        ->where('resource_type = ?', 'sitegroupevent_event')
                                        ->where('resource_id = ?', $sitegroupevent_id);
                                $selectLikeDatas = $likeTable->fetchAll($selectLike);
                                foreach ($selectLikeDatas as $selectLikeData) {
                                    $like = Engine_Api::_()->getItem('core_like', $selectLikeData->like_id);
                                    $newLikeEntry = $likeTable->createRow();
                                    $newLikeEntry->resource_type = 'siteevent_event';
                                    $newLikeEntry->resource_id = $siteevent->getIdentity();
                                    $newLikeEntry->poster_type = 'user';
                                    $newLikeEntry->poster_id = $like->poster_id;
                                    $newLikeEntry->creation_date = $like->creation_date;
                                    $newLikeEntry->save();

                                    $newLikeEntry->creation_date = $like->creation_date;
                                    $newLikeEntry->save();
                                }
                                //END FETCH LIKES
                                //START FETCH PHOTO DATA
                                $selectEventPhoto = $sitegroupeventPhotoTable->select()
                                        ->from($sitegroupeventPhotoTableName)
                                        ->where('event_id = ?', $sitegroupevent_id);
                                $sitegroupeventPhotoDatas = $sitegroupeventPhotoTable->fetchAll($selectEventPhoto);

                                if (!empty($sitegroupeventPhotoDatas)) {

                                    $sitegroupeventPhotoDatas = $sitegroupeventPhotoDatas->toArray();

                                    if (empty($sitegroupevent->photo_id)) {
                                        foreach ($sitegroupeventPhotoDatas as $sitegroupeventPhotoData) {
                                            $sitegroupevent->photo_id = $sitegroupeventPhotoData['photo_id'];
                                            break;
                                        }
                                    }

                                    if (!empty($sitegroupevent->photo_id)) {
                                        $sitegroupeventPhotoData = $sitegroupeventPhotoTable->fetchRow(array('file_id = ?' => $sitegroupevent->photo_id));
                                        if (!empty($sitegroupeventPhotoData)) {
                                            $storageData = $storageTable->fetchRow(array('file_id = ?' => $sitegroupeventPhotoData->file_id));

                                            if (!empty($storageData) && !empty($storageData->storage_path)) {
                                                if (is_string($storageData->storage_path) && file_exists($storageData->storage_path))
                                                    $siteevent->setPhoto($storageData->storage_path);

                                                $album_id = $siteeventAlbumTable->update(array('photo_id' => $siteevent->photo_id), array('event_id = ?' => $siteevent->event_id));

                                                $siteeventProfilePhoto = Engine_Api::_()->getDbTable('photos', 'siteevent')->fetchRow(array('file_id = ?' => $siteevent->photo_id));
                                                if (!empty($siteeventProfilePhoto)) {
                                                    $siteeventProfilePhotoId = $siteeventProfilePhoto->photo_id;
                                                } else {
                                                    $siteeventProfilePhotoId = $siteevent->photo_id;
                                                }

                                                //START FETCH LIKES
                                                $selectLike = $likeTable->select()
                                                        ->from($likeTableName, 'like_id')
                                                        ->where('resource_type = ?', 'sitegroupevent_photo')
                                                        ->where('resource_id = ?', $sitegroupevent->photo_id);
                                                $selectLikeDatas = $likeTable->fetchAll($selectLike);
                                                foreach ($selectLikeDatas as $selectLikeData) {
                                                    $like = Engine_Api::_()->getItem('core_like', $selectLikeData->like_id);
                                                    $newLikeEntry = $likeTable->createRow();
                                                    $newLikeEntry->resource_type = 'siteevent_photo';
                                                    $newLikeEntry->resource_id = $siteeventProfilePhotoId;
                                                    $newLikeEntry->poster_type = 'user';
                                                    $newLikeEntry->poster_id = $like->poster_id;
                                                    $newLikeEntry->creation_date = $like->creation_date;
                                                    $newLikeEntry->save();

                                                    $newLikeEntry->creation_date = $like->creation_date;
                                                    $newLikeEntry->save();
                                                }
                                                //END FETCH LIKES
                                                //START FETCH COMMENTS
                                                $selectLike = $commentTable->select()
                                                        ->from($commentTableName, 'comment_id')
                                                        ->where('resource_type = ?', 'sitegroupevent_photo')
                                                        ->where('resource_id = ?', $sitegroupevent->photo_id);
                                                $selectLikeDatas = $commentTable->fetchAll($selectLike);
                                                foreach ($selectLikeDatas as $selectLikeData) {
                                                    $comment = Engine_Api::_()->getItem('core_comment', $selectLikeData->comment_id);

                                                    $newLikeEntry = $commentTable->createRow();
                                                    $newLikeEntry->resource_type = 'siteevent_photo';
                                                    $newLikeEntry->resource_id = $siteeventProfilePhotoId;
                                                    $newLikeEntry->poster_type = 'user';
                                                    $newLikeEntry->poster_id = $comment->poster_id;
                                                    $newLikeEntry->body = $comment->body;
                                                    $newLikeEntry->creation_date = $comment->creation_date;
                                                    $newLikeEntry->like_count = $comment->like_count;
                                                    $newLikeEntry->save();

                                                    $newLikeEntry->creation_date = $comment->creation_date;
                                                    $newLikeEntry->save();
                                                }
                                                //END FETCH COMMENTS
                                                //START FETCH TAGGER DETAIL
                                                $selectTagmaps = $tagmapsTable->select()
                                                        ->from($tagmapsTableName, 'tagmap_id')
                                                        ->where('resource_type = ?', 'sitegroupevent_photo')
                                                        ->where('resource_id = ?', $sitegroupevent->photo_id);
                                                $selectTagmapsDatas = $tagmapsTable->fetchAll($selectTagmaps);
                                                foreach ($selectTagmapsDatas as $selectTagmapsData) {
                                                    $tagMap = Engine_Api::_()->getItem('core_tag_map', $selectTagmapsData->tagmap_id);

                                                    $newTagmapEntry = $tagmapsTable->createRow();
                                                    $newTagmapEntry->resource_type = 'siteevent_photo';
                                                    $newTagmapEntry->resource_id = $siteeventProfilePhotoId;
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

                                        $fetchDefaultAlbum = $siteeventAlbumTable->fetchRow(array('event_id = ?' => $siteevent->event_id));
                                        if (!empty($fetchDefaultAlbum)) {

                                            $selectEventPhoto = $sitegroupeventPhotoTable->select()
                                                    ->from($sitegroupeventPhotoTable->info('name'))
                                                    ->where('event_id = ?', $sitegroupevent_id);
                                            $sitegroupeventPhotoDatas = $sitegroupeventPhotoTable->fetchAll($selectEventPhoto);

                                            $order = 999;
                                            foreach ($sitegroupeventPhotoDatas as $sitegroupeventPhotoData) {

                                                if ($sitegroupeventPhotoData['file_id'] != $sitegroupevent->photo_id) {
                                                    $params = array(
                                                        'collection_id' => $fetchDefaultAlbum->album_id,
                                                        'album_id' => $fetchDefaultAlbum->album_id,
                                                        'event_id' => $siteevent->event_id,
                                                        'user_id' => $sitegroupeventPhotoData['user_id'],
                                                        'order' => $order
                                                    );

                                                    $storageData = $storageTable->fetchRow(array('file_id = ?' => $sitegroupeventPhotoData['file_id']));
                                                    if (!empty($storageData) && !empty($storageData->storage_path)) {
                                                        $file = array();
                                                        $file['tmp_name'] = $storageData->storage_path;
                                                        $path_array = explode('/', $file['tmp_name']);
                                                        $file['name'] = end($path_array);

                                                        $siteeventPhoto = Engine_Api::_()->siteevent()->createPhoto($params, $file);
                                                        if (!empty($siteeventPhoto)) {

                                                            $order++;

                                                            //START FETCH LIKES
                                                            $selectLike = $likeTable->select()
                                                                    ->from($likeTableName, 'like_id')
                                                                    ->where('resource_type = ?', 'sitegroupevent_photo')
                                                                    ->where('resource_id = ?', $sitegroupeventPhotoData['photo_id']);
                                                            $selectLikeDatas = $likeTable->fetchAll($selectLike);
                                                            foreach ($selectLikeDatas as $selectLikeData) {
                                                                $like = Engine_Api::_()->getItem('core_like', $selectLikeData->like_id);

                                                                $newLikeEntry = $likeTable->createRow();
                                                                $newLikeEntry->resource_type = 'siteevent_photo';
                                                                $newLikeEntry->resource_id = $siteeventPhoto->photo_id;
                                                                $newLikeEntry->poster_type = 'user';
                                                                $newLikeEntry->poster_id = $like->poster_id;
                                                                $newLikeEntry->creation_date = $like->creation_date;
                                                                $newLikeEntry->save();

                                                                $newLikeEntry->creation_date = $like->creation_date;
                                                                $newLikeEntry->save();
                                                            }
                                                            //END FETCH LIKES
                                                            //START FETCH COMMENTS
                                                            $selectLike = $commentTable->select()
                                                                    ->from($commentTableName, 'comment_id')
                                                                    ->where('resource_type = ?', 'sitegroupevent_photo')
                                                                    ->where('resource_id = ?', $sitegroupeventPhotoData['photo_id']);
                                                            $selectLikeDatas = $commentTable->fetchAll($selectLike);
                                                            foreach ($selectLikeDatas as $selectLikeData) {
                                                                $comment = Engine_Api::_()->getItem('core_comment', $selectLikeData->comment_id);

                                                                $newLikeEntry = $commentTable->createRow();
                                                                $newLikeEntry->resource_type = 'siteevent_photo';
                                                                $newLikeEntry->resource_id = $siteeventPhoto->photo_id;
                                                                $newLikeEntry->poster_type = 'user';
                                                                $newLikeEntry->poster_id = $comment->poster_id;
                                                                $newLikeEntry->body = $comment->body;
                                                                $newLikeEntry->creation_date = $comment->creation_date;
                                                                $newLikeEntry->like_count = $comment->like_count;
                                                                $newLikeEntry->save();

                                                                $newLikeEntry->creation_date = $comment->creation_date;
                                                                $newLikeEntry->save();
                                                            }
                                                            //END FETCH COMMENTS
                                                            //START FETCH TAGGER DETAIL
                                                            $selectTagmaps = $tagmapsTable->select()
                                                                    ->from($tagmapsTableName, 'tagmap_id')
                                                                    ->where('resource_type = ?', 'sitegroupevent_photo')
                                                                    ->where('resource_id = ?', $sitegroupeventPhotoData['photo_id']);
                                                            $selectTagmapsDatas = $tagmapsTable->fetchAll($selectTagmaps);
                                                            foreach ($selectTagmapsDatas as $selectTagmapsData) {
                                                                $tagMap = Engine_Api::_()->getItem('core_tag_map', $selectTagmapsData->tagmap_id);

                                                                $newTagmapEntry = $tagmapsTable->createRow();
                                                                $newTagmapEntry->resource_type = 'siteevent_photo';
                                                                $newTagmapEntry->resource_id = $siteeventPhoto->photo_id;
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
                                }

                                //START STYLES
                                $selectStyles = $stylesTable->select()
                                        ->from($stylesTableName, 'style')
                                        ->where('type = ?', 'sitegroupevent_event')
                                        ->where('id = ?', $sitegroupevent_id);
                                $selectStyleDatas = $stylesTable->fetchRow($selectStyles);
                                if (!empty($selectStyleDatas)) {
                                    $selectSiteeventStyles = $stylesTable->select()
                                            ->from($stylesTableName, 'style')
                                            ->where('type = ?', 'siteevent_event')
                                            ->where('id = ?', $siteevent->getIdentity());
                                    $selectSiteeventStyleDatas = $stylesTable->fetchRow($selectSiteeventStyles);
                                    if (empty($selectSiteeventStyleDatas)) {
                                        //CREATE
                                        $stylesTable->insert(array(
                                            'type' => 'siteevent_event',
                                            'id' => $siteevent->getIdentity(),
                                            'style' => $selectStyleDatas->style
                                        ));
                                    }
                                }
                                //END STYLES
                                //START UPDATE TOTAL LIKES IN SITEEVENT TABLE
                                $selectLikeCount = $likeTable->select()
                                        ->from($likeTableName, array('COUNT(*) AS like_count'))
                                        ->where('resource_type = ?', 'sitegroupevent_event')
                                        ->where('resource_id = ?', $sitegroupevent_id);
                                $selectLikeCounts = $likeTable->fetchAll($selectLikeCount);
                                if (!empty($selectLikeCounts)) {
                                    $selectLikeCounts = $selectLikeCounts->toArray();
                                    $siteevent->like_count = $selectLikeCounts[0]['like_count'];
                                    $siteevent->save();
                                }
                                //END UPDATE TOTAL LIKES IN SITEEVENT TABLES

                                if ($activity_sitegroupevent) {
                                    $selectActionId = $actionsTable->select()
                                            ->from($actionsTableName, 'action_id')
                                            ->where('object_type = ?', 'sitegroupevent_event')
                                            ->where('object_id = ?', $sitegroupevent_id)
                                            ->where('type =?', 'sitegroupevent_photo_upload')
                                            ->query()
                                            ->fetchColumn();
                                    if ($selectActionId) {
                                        $action = Engine_Api::_()->getItem('activity_action', $selectActionId);
                                        $action->type = 'siteevent_photo_upload';
                                        $action->object_id = $siteevent->getIdentity();
                                        $action->object_type = $siteevent->getType();
                                        $action->params = array_merge($action->params, array('title' => $siteevent->getTitle()));
                                        $action->save();
                                        $actionsTable->resetActivityBindings($action);
                                    }
                                }
                            }

                            //CREATE LOG ENTRY IN LOG FILE
                            if (file_exists(APPLICATION_PATH . '/temporary/log/EventToSiteeventImport.log')) {
                                $myFile = APPLICATION_PATH . '/temporary/log/EventToSiteeventImport.log';
                                $error = Zend_Registry::get('Zend_Translate')->_("can't open file");
                                $fh = fopen($myFile, 'a') or die($error);
                                $current_time = date('D, d M Y H:i:s T');
                                $siteevent_title = $siteevent->title;
                                $stringData = $this->view->translate('Event with ID ') . $sitegroupevent_id . $this->view->translate(' is successfully imported into a Advanced Event with ID ') . $siteevent->event_id . $this->view->translate(' at ') . $current_time . $this->view->translate(". Title of that Event is '") . $siteevent_title . "'.\n\n";
                                fwrite($fh, $stringData);
                                fclose($fh);
                            }
                            $coreModuleTable->update(array('enabled' => 0), array('name = ?' => 'sitegroupevent'));
                            Engine_Api::_()->getDbtable('content', 'core')->delete(array('name =?' => 'sitegroupevent.profile-sitegroupevents'));
                            Engine_Api::_()->getDbtable('content', 'core')->delete(array('name =?' => 'sitegroupevent.profile-events'));
                            Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->delete(array('name =?' => 'sitegroupevent.profile-sitegroupevents'));
                            Engine_Api::_()->getDbtable('admincontent', 'sitegroup')->delete(array('name =?' => 'sitegroupevent.profile-events'));
                            Engine_Api::_()->getDbtable('content', 'sitegroup')->delete(array('name =?' => 'sitegroupevent.profile-sitegroupevents'));
                            Engine_Api::_()->getDbtable('content', 'sitegroup')->delete(array('name =?' => 'sitegroupevent.profile-events'));
                            $db->commit();
                            $this->view->sitegroupevent_assigned_previous_id = $sitegroupevent_id;
                        } catch (Exception $e) {
                            $db->rollback();
                            throw($e);
                        }
                        if ($next_import_count >= 100) {
                            $this->_redirect("admin/siteevent/importevent/index?start_import=1&module=sitegroupevent&recall=1&activity_sitegroupevent=$activity_sitegroupevent");
                        }
                    }
                } else {
                    if ($_GET['recall']) {
                        echo json_encode(array('success' => 1));
                        exit();
                    }
                }
            }
        }
    }

    public function importDocuments($event, $siteevent, $activity_event = 0) {

        $db = Engine_Db_Table::getDefaultAdapter();
        $eventDocumentsTable = Engine_Api::_()->getDbtable('documents', 'eventdocument');
        $eventDocumentsTableName = $eventDocumentsTable->info('name');
        //GET ACTIONS TABLE
        $actionsTable = Engine_Api::_()->getDbtable('actions', 'activity');
        $actionsTableName = $actionsTable->info('name');
        //GET EVENT ATTACHMENT  TABLE
        $attachmentsTable = Engine_Api::_()->getDbtable('attachments', 'activity');
        $attachmentsTableName = $attachmentsTable->info('name');
        $siteeventDocumentsTable = Engine_Api::_()->getDbtable('documents', 'siteeventdocument');
        $siteeventDocumentsTableName = $siteeventDocumentsTable->info('name');

        $eventdocumentRatingTable = Engine_Api::_()->getDbTable('ratings', 'eventdocument');
        $eventdocumentRatingTableName = $eventdocumentRatingTable->info('name');

        $siteeventdocumentRatingTable = Engine_Api::_()->getDbTable('ratings', 'siteeventdocument');

        $siteeventdocumentFieldValueTable = Engine_Api::_()->fields()->getTable('siteeventdocument_document', 'values');
        $siteeventdocumentFieldValueTableName = $siteeventdocumentFieldValueTable->info('name');

        $siteeventdocumentMetaTable = Engine_Api::_()->fields()->getTable('siteeventdocument_document', 'meta');
        $siteeventdocumentMetaTableName = $siteeventdocumentMetaTable->info('name');

        $eventdocumentFieldValueTable = Engine_Api::_()->fields()->getTable('eventdocument_document', 'values');
        $eventdocumentFieldValueTableName = $eventdocumentFieldValueTable->info('name');

        //GET STORAGE TABLE
        $storageTable = Engine_Api::_()->getDbtable('files', 'storage');
        $storageTableName = $storageTable->info('name');

        $event_field_id = $db->query("SHOW COLUMNS FROM engine4_siteeventdocument_document_fields_meta LIKE 'event_field_id'")->fetch();

        if (empty($event_field_id)) {
            //ADD MAPPING COLUMN IN SITEREVIEW TABLE
            $event_field_id = $db->query("SHOW COLUMNS FROM engine4_siteeventdocument_document_fields_meta LIKE 'event_field_id'")->fetch();
            if (empty($event_field_id)) {
                $db->query("ALTER TABLE `engine4_siteeventdocument_document_fields_meta` ADD `event_field_id` INT( 11 ) NOT NULL DEFAULT '0'");
            }

            $field_map_array = $db->select()
                    ->from('engine4_eventdocument_document_fields_maps')
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

            $field_meta_array = $db->select()
                    ->from('engine4_eventdocument_document_fields_meta')
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

                $field = Engine_Api::_()->fields()->createField('siteeventdocument_document', $formValues);

                $db->update('engine4_siteeventdocument_document_fields_meta', array('config' => $field_meta_array[$c]['config'], 'event_field_id' => $field_meta_array[$c]['field_id']), array('field_id = ?' => $field->field_id));

                if ($field_meta_array[$c]['type'] == 'select' || $field_meta_array[$c]['type'] == 'radio' || $field_meta_array[$c]['type'] == 'multiselect' || $field_meta_array[$c]['type'] == 'multi_checkbox') {
                    $field_options_array = $db->select()
                            ->from('engine4_eventdocument_document_fields_options')
                            ->where('field_id = ?', $field_meta_array[$c]['field_id'])
                            ->query()
                            ->fetchAll();
                    $field_options_order = 0;
                    foreach ($field_options_array as $field_options) {
                        $field_options_order++;
                        $field = Engine_Api::_()->fields()->getField($field->field_id, 'siteeventdocument_document');
                        $option = Engine_Api::_()->fields()->createOption('siteeventdocument_document', $field, array(
                            'label' => $field_options['label'],
                            'order' => $field_options_order,
                        ));

                        $morefield_map_array = $db->select()
                                ->from('engine4_eventdocument_document_fields_maps')
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
                                ->from('engine4_eventdocument_document_fields_meta')
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

                            $morefield = Engine_Api::_()->fields()->createField('siteeventdocument_document', $moreformValues);

                            $db->update('engine4_siteeventdocument_document_fields_meta', array('config' => $morefield_meta_array[$morec]['config'], 'event_field_id' => $morefield_meta_array[$morec]['field_id']), array('field_id = ?' => $morefield->field_id));

                            if ($morefield_meta_array[$morec]['type'] == 'select' || $morefield_meta_array[$morec]['type'] == 'radio' || $morefield_meta_array[$morec]['type'] == 'multiselect' || $morefield_meta_array[$morec]['type'] == 'multi_checkbox') {
                                $morefield_options_array = $db->select()
                                        ->from('engine4_eventdocument_document_fields_options')
                                        ->where('field_id = ?', $morefield_meta_array[$morec]['field_id'])
                                        ->query()
                                        ->fetchAll();
                                $morefield_options_order = 0;
                                foreach ($morefield_options_array as $morefield_options) {
                                    $morefield_options_order++;
                                    $morefield = Engine_Api::_()->fields()->getField($morefield->field_id, 'siteeventdocument_document');
                                    $moreoption = Engine_Api::_()->fields()->createOption('siteeventdocument_document', $morefield, array(
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

        $selectEventDocument = $eventDocumentsTable->select()
                ->from($eventDocumentsTable->info('name'))
                ->where('event_id = ?', $event->event_id);
        $eventDocumentsDatas = $eventDocumentsTable->fetchAll($selectEventDocument);
        foreach ($eventDocumentsDatas as $eventDocumentsData) {
            $newDocumentEntry = $siteeventDocumentsTable->createRow();
            $newDocumentEntry->owner_id = $eventDocumentsData->owner_id;
            $newDocumentEntry->event_id = $siteevent->event_id;
            $newDocumentEntry->title = $eventDocumentsData->title;
            $newDocumentEntry->description = $eventDocumentsData->description;

            $newDocumentEntry->filename_id = 0;
            $newDocumentEntry->storage_path = $eventDocumentsData->storage_path;

            $newDocumentEntry->license = $eventDocumentsData->license;
            $newDocumentEntry->private = $eventDocumentsData->private;
            $newDocumentEntry->filemime = $eventDocumentsData->filemime;
            $newDocumentEntry->filesize = $eventDocumentsData->filesize;
            $newDocumentEntry->doc_id = $eventDocumentsData->doc_id;
            $newDocumentEntry->secret_password = $eventDocumentsData->secret_password;
            $newDocumentEntry->access_key = $eventDocumentsData->access_key;
            $newDocumentEntry->fulltext = $eventDocumentsData->fulltext;
            $newDocumentEntry->thumbnail = $eventDocumentsData->thumbnail;
            $newDocumentEntry->creation_date = $eventDocumentsData->creation_date;
            $newDocumentEntry->modified_date = $eventDocumentsData->modified_date;
            $newDocumentEntry->comment_count = $eventDocumentsData->comment_count;
            $newDocumentEntry->like_count = $eventDocumentsData->like_count;
            $newDocumentEntry->view_count = $eventDocumentsData->view_count;
            $newDocumentEntry->rating = $eventDocumentsData->rating;
            $newDocumentEntry->email_allow = $eventDocumentsData->email_allow;
            $newDocumentEntry->download_allow = $eventDocumentsData->download_allow;
            $newDocumentEntry->secure_allow = $eventDocumentsData->secure_allow;
            $newDocumentEntry->search = $eventDocumentsData->search;
            $newDocumentEntry->draft = $eventDocumentsData->draft;
            $newDocumentEntry->featured = $eventDocumentsData->featured;
            $newDocumentEntry->approved = $eventDocumentsData->approved;
            $newDocumentEntry->status = $eventDocumentsData->status;
            $newDocumentEntry->save();

            $newDocumentEntry->creation_date = $eventDocumentsData->creation_date;
            $newDocumentEntry->save();

            if ($eventDocumentsData->filename_id) {
                $name = $storageTable->select()
                        ->from($storageTableName, 'name')
                        ->where('file_id = ?', $eventDocumentsData->filename_id)
                        ->where('parent_id = ?', $eventDocumentsData->document_id)
                        ->where('parent_type = ?', 'eventdocument_document')
                        ->query()
                        ->fetchColumn();
                if ($name) {
                    $newDocumentEntry->storage_path = $name;
                    $newDocumentEntry->save();
                }
            }

            if ($activity_event) {

                //START FETCH ACTIONS
                $selectActionId = $actionsTable->select()
                        ->from($actionsTableName, 'action_id')
                        ->where('object_type = ?', 'event')
                        ->where('object_id = ?', $event->event_id)
                        ->where('type =?', 'eventdocument_new');

                $selectActionIds = $actionsTable->fetchAll($selectActionId);
                foreach ($selectActionIds as $selectActionId) {
                    if ($selectActionId) {
                        $action = Engine_Api::_()->getItem('activity_action', $selectActionId['action_id']);
                        $action->type = 'siteeventdocument_new';
                        $action->object_id = $siteevent->getIdentity();
                        $action->object_type = $siteevent->getType();
                        $action->save();
                        $actionsTable->resetActivityBindings($action);
                    }
                }
                //END FETCH ACTIONS

                $selectAttachmentId = $attachmentsTable->select()
                        ->from($attachmentsTableName, 'id')
                        ->where('type = ?', 'eventdocument_document')
                        ->where('id =?', $eventDocumentsData->document_id);

                $selectAttachmentIds = $attachmentsTable->fetchAll($selectAttachmentId);
                foreach ($selectAttachmentIds as $selectAttachmentId) {
                    if ($selectAttachmentId) {
                        $attachmentsTable->update(array('type' => 'siteeventdocument_document', 'id' => $newDocumentEntry->getIdentity()), array('type = ?' => 'eventdocument_document', 'id =?' => $selectAttachmentId['id']));
                    }
                }
            }

//      if ($activity_event) {
//        //INSERT NEW ACTIVITY IF DOCUMENT IS JUST GETTING PUBLISHED
//        $action = $activityTable->getActionsByObject($newDocumentEntry);
//        if (count($action->toArray()) <= 0 && $newDocumentEntry->draft == 0 && $newDocumentEntry->approved == 1 && $newDocumentEntry->status == 1 && $newDocumentEntry->activity_feed == 0) {
//
//          $activityFeedType = null;
//          if (Engine_Api::_()->siteevent()->isGroupOwner($siteevent, $newDocumentEntry->getOwner()) && Engine_Api::_()->siteevent()->isFeedTypeGroupEnable())
//            $activityFeedType = 'siteeventdocument_admin_new';
//          elseif ($siteevent->all_post || Engine_Api::_()->siteevent()->isGroupOwner($siteevent, $newDocumentEntry->getOwner()))
//            $activityFeedType = 'siteeventdocument_new';
//
//          if ($activityFeedType) {
//            $action = $activityTable->addActivity($newDocumentEntry->getOwner(), $siteevent, $activityFeedType);
//            Engine_Api::_()->getApi('subCore', 'siteevent')->deleteFeedStream($action);
//          }
//
//          //MAKE SURE ACTION EXISTS BEFOR ATTACHING THE DOCUMENT TO THE ACTIVITY
//          if ($action != null) {
//            $activityTable->attachActivity($action, $newDocumentEntry);
//            $newDocumentEntry->activity_feed = 1;
//            $newDocumentEntry->save();
//
//            $action->date = $newDocumentEntry->creation_date;
//            $action->save();
//          }
//        }
//
//        foreach ($activityTable->getActionsByObject($newDocumentEntry) as $action) {
//          $activityTable->resetActivityBindings($action);
//        }
//      }
            //START FETCH CUSTOM FIELD VALUES
            $fieldValueSelect = $siteeventdocumentMetaTable->select()
                    ->setIntegrityCheck(false)
                    ->from($siteeventdocumentMetaTableName, array('field_id', 'type'))
                    ->joinInner($eventdocumentFieldValueTableName, "$eventdocumentFieldValueTableName.field_id = $siteeventdocumentMetaTableName.event_field_id", array('value', 'index', 'field_id as event_field_id'))
                    ->where("$eventdocumentFieldValueTableName.item_id = ?", $eventDocumentsData->document_id);
            $fieldValues = $siteeventdocumentMetaTable->fetchAll($fieldValueSelect);
            foreach ($fieldValues as $fieldValue) {
                if ($fieldValue->type != 'multi_checkbox' && $fieldValue->type != 'multiselect' && $fieldValue->type != 'radio' && $fieldValue->type != 'select') {
                    $siteeventdocumentFieldValueTable->insert(array('item_id' => $newDocumentEntry->document_id, 'field_id' => $fieldValue->field_id, 'index' => $fieldValue->index, 'value' => $fieldValue->value));
                } else {

                    $eventdocumentFieldValues = $db->select()
                            ->from('engine4_eventdocument_document_fields_options')
                            ->where('field_id = ?', $fieldValue->event_field_id)
                            ->query()
                            ->fetchAll(Zend_Db::FETCH_COLUMN);

                    $siteeventdocumentFieldValues = $db->select()
                            ->from('engine4_siteeventdocument_document_fields_options')
                            ->where('field_id = ?', $fieldValue->field_id)
                            ->query()
                            ->fetchAll(Zend_Db::FETCH_COLUMN);

                    $mergeFieldValues = array_combine($siteeventdocumentFieldValues, $eventdocumentFieldValues);
                    $value = array_search($fieldValue->value, $mergeFieldValues);
                    if (!empty($value)) {
                        $siteeventdocumentFieldValueTable->insert(array('item_id' => $newDocumentEntry->document_id, 'field_id' => $fieldValue->field_id, 'index' => $fieldValue->index, 'value' => $value));
                    }
                }
            }
            //END FETCH CUSTOM FIELD VALUES                  
            //START FETCH RATTING DATA
            $selectEventdocumentRating = $eventdocumentRatingTable->select()
                    ->from($eventdocumentRatingTableName)
                    ->where('document_id = ?', $eventDocumentsData->document_id);

            $eventdocumentRatingDatas = $eventdocumentRatingTable->fetchAll($selectEventdocumentRating);
            if (!empty($eventdocumentRatingDatas)) {
                $eventdocumentRatingDatas = $eventdocumentRatingDatas->toArray();
                foreach ($eventdocumentRatingDatas as $eventdocumentRatingData) {

                    $siteeventdocumentRatingTable->insert(array(
                        'document_id' => $newDocumentEntry->document_id,
                        'user_id' => $eventdocumentRatingData['user_id'],
                        'rating' => $eventdocumentRatingData['rating']
                    ));
                }
            }
            //END FETCH RATTING DATA           
            //START FETCH COMMENTS
            $this->commentItems('siteeventdocument_document', $newDocumentEntry->document_id, 'eventdocument_document', $eventDocumentsData->document_id);
            //END FETCH COMMENTS		 
            //START FETCH LIKES
            $sitelikeEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitelike');
            $this->likeItems('siteeventdocument_document', $newDocumentEntry->document_id, 'eventdocument_document', $eventDocumentsData->document_id, $sitelikeEnabled, $activity_event);
            //END FETCH LIKES	                
        }
    }

    //FETCH LIKE ACCORDING TO ITEM.
    public function likeItems($new_resource_type, $new_resource_id, $old_resource_type, $old_resource_id, $sitelikeEnabled = 0, $activity_event = 0) {

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
            if ($sitelikeEnabled && $activity_event) {
                $select = $activityTable->select()
                        ->from($activityTableName)
                        ->where('object_id = ?', $old_resource_id)
                        ->where('object_type = ?', $old_resource_type)
                        ->where("type = ?", "like_$old_resource_type")
                        ->order('action_id ASC');

                $activityDatas = $activityTable->fetchAll($select);
                foreach ($activityDatas as $activityData) {

                    $user = Engine_Api::_()->getItem($activityData->subject_type, $activityData->subject_id);
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

}