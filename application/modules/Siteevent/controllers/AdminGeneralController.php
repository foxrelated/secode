<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminGeneralController.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_AdminGeneralController extends Core_Controller_Action_Admin {

    //ACTION FOR MAKING THE SITEEVENT FEATURED/UNFEATURED
    public function featuredAction() {

        $event_id = $this->_getParam('event_id');
        if (!empty($event_id)) {
            $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
            $siteevent->featured = !$siteevent->featured;
            $siteevent->save();
        }
        $this->_redirect('admin/siteevent/manage');
    }

    //ACTION FOR MAKING THE SPONSORED /UNSPONSORED
    public function sponsoredAction() {

        $event_id = $this->_getParam('event_id');
        if (!empty($event_id)) {
            $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
            $siteevent->sponsored = !$siteevent->sponsored;
            $siteevent->save();
        }
        $this->_redirect('admin/siteevent/manage');
    }

    //ACTION FOR MAKING THE SITEEVENT FEATURED/UNFEATURED
    public function newlabelAction() {

        $event_id = $this->_getParam('event_id');
        if (!empty($event_id)) {
            $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
            $siteevent->newlabel = !$siteevent->newlabel;
            $siteevent->save();
        }
        $this->_redirect('admin/siteevent/manage');
    }

    //ACTION FOR MAKING THE SPONSORED /UNSPONSORED
    public function sponsoredCategoryAction() {

        $category_id = $this->_getParam('category_id');
        if (!empty($category_id)) {
            $category = Engine_Api::_()->getItem('siteevent_category', $category_id);
            $category->sponsored = !$category->sponsored;
            $category->save();
        }
        $this->_redirect('admin/siteevent/settings/categories');
    }

    //ACTION FOR MAKING THE SITEEVENT APPROVE/DIS-APPROVE
    public function approvedAction() {

        $event_id = $this->_getParam('event_id');
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        $email = array();
        try {

            $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

            $email['title'] = $siteevent->title;
            $owner = Engine_Api::_()->user()->getUser($siteevent->owner_id);
            $email['mail_id'] = $owner->email;
            $siteevent->approved = !$siteevent->approved;

            if (!empty($siteevent->approved)) {
                  if (isset($siteevent->pending) && !empty($siteevent->pending)) {
                    $sendActiveMail = 1;
                    $siteevent->pending = 0;
                  }
              //START - PACKAGE BASED CHECKS
              if (Engine_Api::_()->siteevent()->hasPackageEnable()) {
                  $diff_days = 0;
                  $package = $siteevent->getPackage();
                  if (($siteevent->expiration_date !== '2250-01-01 00:00:00' && !empty($siteevent->expiration_date) && $siteevent->expiration_date !== '0000-00-00 00:00:00') && date('Y-m-d', strtotime($siteevent->expiration_date)) > date('Y-m-d')) {
                    $diff_days = round((strtotime($siteevent->expiration_date) - strtotime(date('Y-m-d H:i:s'))) / 86400);
                  }
                  if (($diff_days <= 0) || empty($siteevent->expiration_date) || $siteevent->expiration_date == '0000-00-00 00:00:00') {
                    if (!$package->isFree()) {
                      if ($siteevent->status != "active") {
                        $relDate = new Zend_Date(time());
                        $relDate->add((int) 1, Zend_Date::DAY);
                        $siteevent->expiration_date = date('Y-m-d H:i:s', $relDate->toValue());
                      } else {
                        $expirationDate = $package->getExpirationDate();
                        if (!empty($expirationDate))
                          $siteevent->expiration_date = date('Y-m-d H:i:s', $expirationDate);
                        else
                          $siteevent->expiration_date = '2250-01-01 00:00:00';
                      }
                    }
                    else {
                      $expirationDate = $package->getExpirationDate();
                      if (!empty($expirationDate))
                        $siteevent->expiration_date = date('Y-m-d H:i:s', $expirationDate);
                      else
                        $siteevent->expiration_date = '2250-01-01 00:00:00';
                    }
                  }
               }
                //END - PACKAGE BASED CHECKS               
                if (empty($siteevent->approved_date))
                    $siteevent->approved_date = date('Y-m-d H:i:s');
                
                //ADDED TO ATTACH ACTIVITY FEED ON APPROVAL
                if (isset($sendActiveMail) && $sendActiveMail) {
                $viewer = Engine_Api::_()->user()->getViewer();
                Engine_Api::_()->siteevent()->sendMail("ACTIVE", $event_id);
                  if (!empty($siteevent) && empty($siteevent->draft) && isset($siteevent->pending) && empty($siteevent->pending)) {
                    //ON APPROVAL - ATTACH ACTIVITY FEED
                    $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $siteevent, 'siteevent_new');

                    if ($action != null) {
                      Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $siteevent);
                    }
                    
                    //ON APPROVAL - SEND NOTIFICATION & EMAIL TO HOST.
                    Engine_Api::_()->siteevent()->sendNotificationToHost($siteevent->event_id);
                  } 
                }
                //END
                else{
                  $email['subject'] = 'Your Event has been approved.';
                  $email['message'] = "Your event  \"" . $email['title'] . " \" has been approved.";
                  Engine_Api::_()->siteevent()->aprovedEmailNotification($siteevent, $email);  
                }
            } else {
                $email['subject'] = 'Your Event has been disapproved.';
                $email['message'] = "Your event  \"" . $email['title'] . " \" has been disapproved.";
                Engine_Api::_()->siteevent()->aprovedEmailNotification($siteevent, $email);
            }
            $siteevent->save();
            
            //PACKAGE BASED CHECKS
            $siteevent_pending = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventpaid') ? $siteevent->pending : 0;
            $parentTypeItem = Engine_Api::_()->getItem($siteevent->parent_type, $siteevent->parent_id);

            $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
           if ($siteevent->draft == 0 && $siteevent->search && empty($siteevent_pending)) { 
                //INSERT ACTIVITY IF EVENT IS SEARCHABLE
                if ($siteevent->parent_type != 'user' && $siteevent->parent_type != 'sitereview_listing') {
                    $getModuleName = strtolower($parentTypeItem->getModuleName());
                    $isOwner = 'is' . ucfirst($parentTypeItem->getShortType()) . 'Owner';
                    $isFeedTypeEnable = 'isFeedType' . ucfirst($parentTypeItem->getShortType()) . 'Enable';
                    $activityFeedType = null;
                    if (Engine_Api::_()->$getModuleName()->$isOwner($parentTypeItem) && Engine_Api::_()->$getModuleName()->$isFeedTypeEnable())
                        $activityFeedType = $getModuleName . 'event_admin_new';
                    elseif ($parentTypeItem->all_post || Engine_Api::_()->$getModuleName()->$isOwner($parentTypeItem))
                        $activityFeedType = $getModuleName . 'event_new';

                    if ($activityFeedType) {
                        $action = $actionTable->addActivity($siteevent->getOwner(), $parentTypeItem, $activityFeedType);
                        Engine_Api::_()->getApi('subCore', $getModuleName)->deleteFeedStream($action);
                    }
                    if ($action != null) {
                        $actionTable->attachActivity($action, $siteevent);
                    }

                    //SENDING ACTIVITY FEED TO FACEBOOK.
                    $enable_Facebooksefeed = $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksefeed');
                    if (!empty($enable_Facebooksefeed)) {
                        $event_array = array();
                        $event_array['type'] = $getModuleName . 'event_new';
                        $event_array['object'] = $siteevent;
                        Engine_Api::_()->facebooksefeed()->sendFacebookFeed($event_array);
                    }
                } elseif ($siteevent->parent_type == 'sitereview_listing') {
                    $action = $actionTable->addActivity($siteevent->getOwner(), $parentTypeItem, 'sitereview_event_new_listtype_' . $parentTypeItem->listingtype_id);
                    if ($action != null) {
                        $actionTable->attachActivity($action, $siteevent);
                    }
                } else {
                    $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($siteevent->getOwner(), $siteevent, 'siteevent_new');
                    if ($action != null) {
                        Engine_Api::_()->getDbtable('actions', 'seaocore')->attachActivity($action, $siteevent);
                    }
                }
            }            
            
            
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $this->_redirect('admin/siteevent/manage');
    }

    //ACTION FOR MAKING THE SITEEVENT RENEW
    public function renewAction() {

    $this->_helper->layout->setLayout('admin-simple');
    $this->view->event_id = $event_id = $this->_getParam('event_id');
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if (!empty($siteevent->approved)) {
          $package = $siteevent->getPackage();
          if ($siteevent->expiration_date !== '2250-01-01 00:00:00') {

            $expiration = $package->getExpirationDate();

            $diff_days = 0;
            if (!empty($siteevent->expiration_date) && $siteevent->expiration_date !== '0000-00-00 00:00:00') {
              $diff_days = round((strtotime($siteevent->expiration_date) - strtotime(date('Y-m-d H:i:s'))) / 86400);
            }
            if ($expiration) {
              $date = date('Y-m-d H:i:s', $expiration);

              if ($diff_days >= 1) {

                $diff_days_expiry = round((strtotime($date) - strtotime(date('Y-m-d H:i:s'))) / 86400);
                $incrmnt_date = date('d', time()) + $diff_days_expiry + $diff_days;
                $incrmnt_date = date('Y-m-d H:i:s', mktime(date("H"), date("i"), date("s"), date("m"), $incrmnt_date));
              } else {
                $incrmnt_date = $date;
              }

              $siteevent->expiration_date = $incrmnt_date;
            } else {
              $siteevent->expiration_date = '2250-01-01 00:00:00';
            }
          }
          if ($package->isFree())
            $siteevent->status = "initial";
          else
            $siteevent->status = "active";
        }
        $siteevent->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Renew Succesfully.'))
      ));
    }
    $this->renderScript('admin-general/renew.tpl');
  }

    public function categoriesAction() {

        $element_value = $this->_getParam('element_value', 1);
        $element_type = $this->_getParam('element_type', 'category_id');

        $categoriesTable = Engine_Api::_()->getDbTable('categories', 'siteevent');
        $select = $categoriesTable->select()
                ->from($categoriesTable->info('name'), array('category_id', 'category_name'))
                ->where("$element_type = ?", $element_value);

        if ($element_type == 'category_id') {
            $select->where('cat_dependency = ?', 0)->where('subcat_dependency = ?', 0);
        } elseif ($element_type == 'cat_dependency') {
            $select->where('subcat_dependency = ?', 0);
        } elseif ($element_type == 'subcat_dependency') {
            $select->where('cat_dependency = ?', $element_value);
        }

        $categoriesData = $categoriesTable->fetchAll($select);

        $categories = array();
        if (Count($categoriesData) > 0) {
            foreach ($categoriesData as $category) {
                $data = array();
                $data['category_name'] = $category->category_name;
                $data['category_id'] = $category->category_id;
                $categories[] = $data;
            }
        }

        $this->view->categories = $categories;
    }

    //ACTION FOR DELETE THE EVENT
    public function deleteAction() {

        $this->_helper->layout->setLayout('admin-simple');
        $event_id = $this->_getParam('event_id');
        $this->view->event_id = $event_id;

        if ($this->getRequest()->isPost()) {
            Engine_Api::_()->getItem('siteevent_event', $event_id)->delete();
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('Deleted Succesfully.')
            ));
        }
        $this->renderScript('admin-general/delete.tpl');
    }

    //ACTION FOR CHANGE THE OWNER OF THE EVENT
    public function changeOwnerAction() {

        //LAYOUT
        $this->_helper->layout->setLayout('admin-simple');

        //GET EVENT ID
        $this->view->event_id = $event_id = $this->_getParam('event_id');

        //FORM
        $form = $this->view->form = new Siteevent_Form_Admin_Changeowner();

        //SET ACTION
        $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

        //GET SITEEVENT ITEM
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        //OLD OWNER ID
        $oldownerid = $siteevent->owner_id;

        //CHECK FORM VALIDATION
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            //GET FORM VALUES
            $values = $form->getValues();

            //GET USER ID WHICH IS NOW NEW USER
            $changeuserid = $values['user_id'];

            //CHANGE USER TABLE
            $changed_user = Engine_Api::_()->getItem('user', $changeuserid);

            //OWNER USER TABLE
            $user = Engine_Api::_()->getItem('user', $siteevent->owner_id);

            //GET DB
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                $activityTable = Engine_Api::_()->getDbtable('actions', 'seaocore');
                $activityTableName = $activityTable->info('name');

                $select = $activityTable->select()
                        ->from($activityTableName)
                        ->where('subject_id = ?', $oldownerid)
                        ->where('subject_type = ?', 'user')
                        ->where('object_id = ?', $event_id)
                        ->where('object_type = ?', 'siteevent_event')
                        ->where('type = ?', 'siteevent_new')
                ;
                $activityData = $activityTable->fetchRow($select);
                if (!empty($activityData)) {
                    $activityData->subject_id = $changeuserid;
                    $activityData->save();
                    $activityTable->resetActivityBindings($activityData);
                }

                //UPDATE EVENT TABLE
                Engine_Api::_()->getDbtable('events', 'siteevent')->update(array('owner_id' => $changeuserid), array('event_id = ?' => $event_id));

                Engine_Api::_()->getDbtable('events', 'siteevent')->update(array('parent_id' => $changeuserid), array('event_id = ?' => $event_id, 'parent_type =? ' => 'user'));

                $membershipTable = Engine_Api::_()->getDbtable('membership', 'siteevent');
                $getPreviousOwnerSelect = $membershipTable->select()->where('resource_id = ?', $siteevent->event_id)->where('user_id = ?', $user->user_id);
                $getPreviousOwnerDetails = $membershipTable->fetchAll($getPreviousOwnerSelect);

                $getNewOwnerSelect = $membershipTable->select()->where('resource_id = ?', $siteevent->event_id)->where('user_id = ?', $changed_user->user_id);
                $getNewOwnerDetails = $membershipTable->fetchAll($getNewOwnerSelect);

                $membershipTable->delete(array('resource_id = ?' => $siteevent->event_id, 'user_id = ?' => $user->user_id));

                $membershipTable->delete(array('resource_id = ?' => $siteevent->event_id, 'user_id = ?' => $changed_user->user_id));

                foreach ($getPreviousOwnerDetails as $getPreviousOwnerDetail) {
                    $membershipTable->insert(array(
                        'resource_id' => $getPreviousOwnerDetail->resource_id,
                        'user_id' => $changed_user->user_id,
                        'active' => $getPreviousOwnerDetail->active,
                        'resource_approved' => $getPreviousOwnerDetail->resource_approved,
                        'user_approved' => $getPreviousOwnerDetail->user_approved,
                        'message' => $getPreviousOwnerDetail->message,
                        'rsvp' => $getPreviousOwnerDetail->rsvp,
                        'occurrence_id' => $getPreviousOwnerDetail->occurrence_id
                    ));
                }

                foreach ($getNewOwnerDetails as $getNewOwnerDetail) {
                    $membershipTable->insert(array(
                        'resource_id' => $getNewOwnerDetail->resource_id,
                        'user_id' => $user->user_id,
                        'active' => $getNewOwnerDetail->active,
                        'resource_approved' => $getNewOwnerDetail->resource_approved,
                        'user_approved' => $getNewOwnerDetail->user_approved,
                        'message' => $getNewOwnerDetail->message,
                        'rsvp' => $getNewOwnerDetail->rsvp,
                        'occurrence_id' => $getNewOwnerDetail->occurrence_id
                    ));
                }

                $list = $siteevent->getLeaderList();
                $list_id = $list['list_id'];
                Engine_Api::_()->getDbtable('listItems', 'siteevent')->update(array('child_id' => $user->user_id), array('list_id = ?' => $list_id, 'child_id = ?' => $changed_user->user_id));

                //UPDATE PHOTO TABLE
                $photoTable = Engine_Api::_()->getDbtable('photos', 'siteevent');
                $photoTableName = $photoTable->info('name');
                $selectPhotos = $photoTable->select()
                        ->from($photoTableName)
                        ->where('user_id = ?', $oldownerid)
                        ->where('event_id = ?', $event_id);
                $photoDatas = $photoTable->fetchAll($selectPhotos);
                foreach ($photoDatas as $photoData) {
                    $photoData->user_id = $changeuserid;
                    $photoData->save();

                    $select = $activityTable->select()
                            ->from($activityTableName)
                            ->where('subject_id = ?', $oldownerid)
                            ->where('subject_type = ?', 'user')
                            //->where('object_id = ?', $photoData->photo_id)
                            ->where('object_id = ?', $event_id)
                            ->where('object_type = ?', 'siteevent_event')
                            ->where('type = ?', 'siteevent_photo_upload')
                    ;
                    $activityDatas = $activityTable->fetchAll($select);
                    foreach ($activityDatas as $activityData) {
                        $activityData->subject_id = $changeuserid;
                        $activityData->save();
                        $activityTable->resetActivityBindings($activityData);
                    }
                }

                $select = $activityTable->select()
                        ->from($activityTableName)
                        ->where('subject_id = ?', $oldownerid)
                        ->where('subject_type = ?', 'user')
                        ->where('object_id = ?', $event_id)
                        ->where('object_type = ?', 'siteevent_event')
                        ->where('type = ?', 'siteevent_change_photo');

                $activityDatas = $activityTable->fetchAll($select);
                foreach ($activityDatas as $activityData) {
                    $activityData->subject_id = $changeuserid;
                    $activityData->save();
                    $activityTable->resetActivityBindings($activityData);
                }

                $select = $activityTable->select()
                        ->from($activityTableName)
                        ->where('subject_id = ?', $oldownerid)
                        ->where('subject_type = ?', 'user')
                        ->where('object_id = ?', $event_id)
                        ->where('object_type = ?', 'siteevent_event')
                        ->where('type = ?', 'siteevent_diary_add_event');

                $activityDatas = $activityTable->fetchAll($select);
                foreach ($activityDatas as $activityData) {
                    $activityData->subject_id = $changeuserid;
                    $activityData->save();
                    $activityTable->resetActivityBindings($activityData);
                }

                if ($siteevent->parent_type == 'sitepage_page') {
                    $select = $activityTable->select()
                            ->from($activityTableName)
                            ->where('subject_id = ?', $oldownerid)
                            ->where('subject_type = ?', 'user')
                            ->where('object_id = ?', $siteevent->parent_id)
                            ->where('object_type = ?', 'sitepage_page')
                            ->where('type = ?', 'sitepageevent_new');

                    $activityDatas = $activityTable->fetchAll($select);
                    foreach ($activityDatas as $activityData) {
                        $activityData->subject_id = $changeuserid;
                        $activityData->save();
                        $activityTable->resetActivityBindings($activityData);
                    }
                }

                if ($siteevent->parent_type == 'sitebusiness_business') {
                    $select = $activityTable->select()
                            ->from($activityTableName)
                            ->where('subject_id = ?', $oldownerid)
                            ->where('subject_type = ?', 'user')
                            ->where('object_id = ?', $siteevent->parent_id)
                            ->where('object_type = ?', 'sitebusiness_business')
                            ->where('type = ?', 'sitebusinessevent_new');

                    $activityDatas = $activityTable->fetchAll($select);
                    foreach ($activityDatas as $activityData) {
                        $activityData->subject_id = $changeuserid;
                        $activityData->save();
                        $activityTable->resetActivityBindings($activityData);
                    }
                }

                if ($siteevent->parent_type == 'sitegroup_group') {
                    $select = $activityTable->select()
                            ->from($activityTableName)
                            ->where('subject_id = ?', $oldownerid)
                            ->where('subject_type = ?', 'user')
                            ->where('object_id = ?', $siteevent->parent_id)
                            ->where('object_type = ?', 'sitegroup_group')
                            ->where('type = ?', 'sitegroupevent_new');

                    $activityDatas = $activityTable->fetchAll($select);
                    foreach ($activityDatas as $activityData) {
                        $activityData->subject_id = $changeuserid;
                        $activityData->save();
                        $activityTable->resetActivityBindings($activityData);
                    }
                }

                if ($siteevent->parent_type == 'sitestore_store') {
                    $select = $activityTable->select()
                            ->from($activityTableName)
                            ->where('subject_id = ?', $oldownerid)
                            ->where('subject_type = ?', 'user')
                            ->where('object_id = ?', $siteevent->parent_id)
                            ->where('object_type = ?', 'sitestore_store')
                            ->where('type = ?', 'sitestoreevent_new');

                    $activityDatas = $activityTable->fetchAll($select);
                    foreach ($activityDatas as $activityData) {
                        $activityData->subject_id = $changeuserid;
                        $activityData->save();
                        $activityTable->resetActivityBindings($activityData);
                    }
                }

                if ($siteevent->parent_type == 'sitereview_listing') {
                    $paretItem = $parentTypeItem = Engine_Api::_()->getItem('sitereview_listing', $siteevent->parent_id);
                    $select = $activityTable->select()
                            ->from($activityTableName)
                            ->where('subject_id = ?', $oldownerid)
                            ->where('subject_type = ?', 'user')
                            ->where('object_id = ?', $siteevent->parent_id)
                            ->where('object_type = ?', 'sitereview_listing')
                            ->where('type = ?', 'sitereview_event_new_listtype_' . $paretItem->listingtype_id);

                    $activityDatas = $activityTable->fetchAll($select);
                    foreach ($activityDatas as $activityData) {
                        $activityData->subject_id = $changeuserid;
                        $activityData->save();
                        $activityTable->resetActivityBindings($activityData);
                    }
                }

                $select = $activityTable->select()
                        ->from($activityTableName)
                        ->where('subject_id = ?', $oldownerid)
                        ->where('subject_type = ?', 'user')
                        ->where('object_id = ?', $event_id)
                        ->where('object_type = ?', 'siteevent_event')
                        ->where('type = ?', 'siteevent_cover_update');

                $activityDatas = $activityTable->fetchAll($select);
                foreach ($activityDatas as $activityData) {
                    $activityData->subject_id = $changeuserid;
                    $activityData->save();
                    $activityTable->resetActivityBindings($activityData);
                }

                Engine_Api::_()->getDbtable('photos', 'siteevent')->update(array('user_id' => $changeuserid), array('user_id = ?' => $oldownerid, 'event_id = ?' => $event_id));

                //UPDATE VIDEO TABLE
                $videoTable = Engine_Api::_()->getDbtable('videos', 'siteevent');
                $videoTableName = $videoTable->info('name');
                $selectVideos = $videoTable->select()
                        ->from($videoTableName)
                        ->where('owner_id = ?', $oldownerid)
                        ->where('event_id = ?', $event_id);
                $videoDatas = $videoTable->fetchAll($selectVideos);
                foreach ($videoDatas as $videoData) {
                    $videoData->owner_id = $changeuserid;
                    $videoData->save();

                    $select = $activityTable->select()
                            ->from($activityTableName)
                            ->where('subject_id = ?', $oldownerid)
                            ->where('subject_type = ?', 'user')
                            //->where('object_id = ?', $videoData->video_id)
                            ->where('object_id = ?', $event_id)
                            ->where('object_type = ?', 'siteevent_event')
                            ->where('type = ?', 'siteevent_video_new')
                    ;
                    $activityDatas = $activityTable->fetchAll($select);
                    foreach ($activityDatas as $activityData) {
                        $activityData->subject_id = $changeuserid;
                        $activityData->save();
                        $activityTable->resetActivityBindings($activityData);
                    }
                }

                //UPDATE DOCUMENT TABLE
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventdocument')) {
                    $documentTable = Engine_Api::_()->getDbtable('documents', 'siteeventdocument');
                    $documentTableName = $documentTable->info('name');
                    $selectDocuments = $documentTable->select()
                            ->from($documentTableName)
                            ->where('owner_id = ?', $oldownerid)
                            ->where('event_id = ?', $event_id);
                    $documentDatas = $documentTable->fetchAll($selectDocuments);
                    foreach ($documentDatas as $documentData) {
                        $documentData->owner_id = $changeuserid;
                        $documentData->save();

                        $select = $activityTable->select()
                                ->from($activityTableName)
                                ->where('subject_id = ?', $oldownerid)
                                ->where('subject_type = ?', 'user')
                                //->where('object_id = ?', $documentData->document_id)
                                ->where('object_id = ?', $event_id)
                                ->where('object_type = ?', 'siteevent_event')
                                ->where('type = ?', 'siteeventdocument_new')
                        ;
                        $activityDatas = $activityTable->fetchAll($select);
                        foreach ($activityDatas as $activityData) {
                            $activityData->subject_id = $changeuserid;
                            $activityData->save();
                            $activityTable->resetActivityBindings($activityData);
                        }
                    }
                }

                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('video')) {

                    $videoTable = Engine_Api::_()->getDbtable('videos', 'video');
                    $videoTableName = $videoTable->info('name');

                    $clasfVideoTable = Engine_Api::_()->getDbtable('clasfvideos', 'siteevent');
                    $clasfVideoTableName = $clasfVideoTable->info('name');

                    $videoDatas = $clasfVideoTable->select()
                            ->setIntegrityCheck()
                            ->from($clasfVideoTableName, array('video_id'))
                            ->joinLeft($videoTableName, "$clasfVideoTableName.video_id = $clasfVideoTableName.video_id", array(''))
                            ->where("$clasfVideoTableName.event_id = ?", $event_id)
                            ->where("$videoTableName.owner_id = ?", $oldownerid)
                            ->query()
                            ->fetchAll(Zend_Db::FETCH_COLUMN);

                    if (!empty($videoDatas)) {

                        $db->update('engine4_video_videos', array('owner_id' => $changeuserid), array('video_id IN (?)' => (array) $videoDatas));

                        $select = $activityTable->select()
                                ->from($activityTableName)
                                ->where('subject_id = ?', $oldownerid)
                                ->where('subject_type = ?', 'user')
                                ->where('object_id IN (?)', $videoDatas)
                                ->where('object_type =?', 'video')
                                ->where("type = 'video_new' OR type = 'video_siteevent'")
                        ;
                        $activityDatas = $activityTable->fetchAll($select);
                        foreach ($activityDatas as $activityData) {
                            $activityData->subject_id = $changeuserid;
                            $activityData->save();
                            $activityTable->resetActivityBindings($activityData);
                        }

                        $select = $activityTable->select()
                                ->from($activityTableName)
                                ->where('subject_id = ?', $oldownerid)
                                ->where('subject_type = ?', 'user')
                                ->where('object_id =?', $event_id)
                                ->where('object_type =?', 'siteevent_event')
                                ->where("type = 'video_siteevent'")
                        ;
                        $activityDatas = $activityTable->fetchAll($select);
                        foreach ($activityDatas as $activityData) {
                            $activityData->subject_id = $changeuserid;
                            $activityData->save();
                            $activityTable->resetActivityBindings($activityData);
                        }
                    }
                }

                //UPDATE REVIEW TABLE
                $reviewTable = Engine_Api::_()->getDbtable('reviews', 'siteevent');
                $previousOwnerReviewed = $reviewTable->canPostReview(array('resource_id' => $event_id, 'resource_type' => 'siteevent_event', 'viewer_id' => $oldownerid));
                $newOwnerReviewed = $reviewTable->canPostReview(array('resource_id' => $event_id, 'resource_type' => 'siteevent_event', 'viewer_id' => $changeuserid));
                if (!empty($previousOwnerReviewed) && empty($newOwnerReviewed)) {
                    $reviewTable->update(array('owner_id' => $changeuserid), array('review_id = ?' => $previousOwnerReviewed));
                    $db->update('engine4_siteevent_reviewdescriptions', array('user_id' => $changeuserid), array('review_id = ?' => $previousOwnerReviewed));

                    $select = $activityTable->select()
                            ->from($activityTableName)
                            ->where('subject_id = ?', $oldownerid)
                            ->where('subject_type = ?', 'user')
                            ->where('object_type = ?', 'siteevent_event')
                            ->where('object_id = ?', $event_id)
                            // ->where('object_id = ?', $previousOwnerReviewed)
                            ->where('type = ?', 'siteevent_review_add')
                    ;
                    $activityDatas = $activityTable->fetchAll($select);
                    foreach ($activityDatas as $activityData) {
                        $activityData->subject_id = $changeuserid;
                        $activityData->save();
                        $activityTable->resetActivityBindings($activityData);
                    }
                }

                //UPDATE DISCUSSION/TOPIC WORK
                $topicTable = Engine_Api::_()->getDbtable('topics', 'siteevent');
                $topicTableName = $topicTable->info('name');
                $selectTopic = $topicTable->select()
                        ->from($topicTableName)
                        ->where('user_id = ?', $oldownerid)
                        ->where('event_id = ?', $event_id);
                $topicDatas = $topicTable->fetchAll($selectTopic);
                foreach ($topicDatas as $topicData) {
                    $topicData->user_id = $changeuserid;
                    $topicData->lastposter_id = $changeuserid;
                    $topicData->save();

                    $select = $activityTable->select()
                            ->from($activityTableName)
                            ->where('subject_id = ?', $oldownerid)
                            ->where('subject_type = ?', 'user')
                            ->where('object_type = ?', 'siteevent_event')
                            ->where('object_id = ?', $event_id)
                            //->where('object_id = ?', $topicData->topic_id)
                            ->where('type = ?', 'siteevent_topic_create')
                    ;
                    $activityDatas = $activityTable->fetchAll($select);
                    foreach ($activityDatas as $activityData) {
                        $activityData->subject_id = $changeuserid;
                        $activityData->save();
                        $activityTable->resetActivityBindings($activityData);
                    }
                }

                $postTable = Engine_Api::_()->getDbtable('posts', 'siteevent');
                $postTableName = $postTable->info('name');
                $selectPost = $postTable->select()
                        ->from($postTableName)
                        ->where('user_id = ?', $oldownerid)
                        ->where('event_id = ?', $event_id);
                $postDatas = $postTable->fetchAll($selectPost);
                foreach ($postDatas as $postData) {
                    $postData->user_id = $changeuserid;
                    $postData->save();

                    $select = $activityTable->select()
                            ->from($activityTableName)
                            ->where('subject_id = ?', $oldownerid)
                            ->where('subject_type = ?', 'user')
                            ->where('object_type = ?', 'siteevent_event')
                            ->where('object_id = ?', $event_id)
                            // ->where('object_id = ?', $postData->post_id)
                            ->where('type = ?', 'siteevent_topic_reply')
                    ;
                    $activityDatas = $activityTable->fetchAll($select);
                    foreach ($activityDatas as $activityData) {
                        $activityData->subject_id = $changeuserid;
                        $activityData->save();
                        $activityTable->resetActivityBindings($activityData);
                    }
                }

                //UPDATE THE POST
                $attachementTable = Engine_Api::_()->getDbtable('attachments', 'activity');
                $attachementTableName = $attachementTable->info('name');

                $select = $activityTable->select()
                        ->from($activityTableName)
                        ->where('subject_id = ?', $oldownerid)
                        ->where('subject_type = ?', 'user')
                        ->where('object_id = ?', $event_id)
                        ->where('object_type = ?', 'siteevent_event')
                        ->where('type = ?', 'post')
                ;
                $activityDatas = $activityTable->fetchAll($select);
                foreach ($activityDatas as $activityData) {

                    $select = $attachementTable->select()
                            ->from($attachementTableName, array('type', 'id'))
                            ->where('action_id = ?', $activityData->action_id);
                    $attachmentData = $attachementTable->fetchRow($select);

                    if ($attachmentData->type == 'video') {
                        $db->update('engine4_video_videos', array('owner_id' => $changeuserid), array('video_id = ?' => $attachmentData->id));
                    } elseif ($attachmentData->type == 'album_photo') {
                        //UNABLE TO DO THIS CHANGE BECAUSE FOR WALL POST THERE IS ONLY ONE ALBUM PER USER SO WE CAN NOT SAY THAT THIS IS ONLY THE WALL POST POSTED BY SITEEVENT PROFILE PAGE.
                    } elseif ($attachmentData->type == 'music_playlist_song') {
                        $db->update('engine4_music_playlists', array('owner_id' => $changeuserid), array('playlist_id = ?' => $attachmentData->id));
                    } elseif ($attachmentData->type == 'core_link') {
                        $db->update('engine4_core_links', array('owner_id' => $changeuserid), array('link_id = ?' => $attachmentData->id));
                    }

                    if ($attachmentData->type != 'album_photo') {
                        $activityData->subject_id = $changeuserid;
                        $activityData->save();
                        $activityTable->resetActivityBindings($activityData);
                    }
                }

                //EMAIL TO NEW AND PREVIOUS OWNER        
                //GET EVENT URL
                $httpVar = _ENGINE_SSL ? 'https://' : 'http://';
                $list_baseurl = $httpVar . $_SERVER['HTTP_HOST'] .
                        Zend_Controller_Front::getInstance()->getRouter()->assemble(array('event_id' => $event_id, 'slug' => $siteevent->getSlug()), "siteevent_entry_view", true);

                //MAKING EVENT TITLE LINK
                $list_title_link = '<a href="' . $list_baseurl . '"  >' . $siteevent->title . ' </a>';

                //GET ADMIN EMAIL
                $email = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;

                //EMAIL THAT GOES TO OLD OWNER
                Engine_Api::_()->getApi('mail', 'core')->sendSystem($user->email, 'SITEEVENT_CHANGEOWNER_EMAIL', array(
                    'list_title' => $siteevent->title,
                    'list_title_with_link' => $list_title_link,
                    'object_link' => $list_baseurl,
                    'site_contact_us_link' => $httpVar . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/help/contact',
                    'email' => $email,
                    'queue' => true
                ));

                //EMAIL THAT GOES TO NEW OWNER
                Engine_Api::_()->getApi('mail', 'core')->sendSystem($changed_user->email, 'SITEEVENT_BECOMEOWNER_EMAIL', array(
                    'list_title' => $siteevent->title,
                    'list_title_with_link' => $list_title_link,
                    'object_link' => $list_baseurl,
                    'site_contact_us_link' => $httpVar . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/help/contact',
                    'email' => $email,
                    'queue' => true
                ));

                //COMMIT
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            //SUCCESS
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 300,
                'parentRefresh' => 300,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('The event owner has been changed succesfully.'))
            ));
        }
    }

    //ACTION FOR GETTING THE LIST OF USERS
    public function getOwnerAction() {

        //GET SITEEVENT ITEM
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $this->_getParam('event_id'));

        //USER TABLE
        $tableUser = Engine_Api::_()->getDbtable('users', 'user');
        $userTableName = $tableUser->info('name');
        $noncreate_owner_level = array();
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        foreach (Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll() as $level) {
            $can_create = 0;
            if ($level->type != "public") {
                $can_create = Engine_Api::_()->authorization()->getPermission($level->level_id, 'siteevent_event', "edit");
                if (empty($can_create)) {
                    $noncreate_owner_level[] = $level->level_id;
                }
            }
        }

        //SELECT
        $select = $tableUser->select()
                ->where('displayname  LIKE ? ', '%' . $this->_getParam('text') . '%')
                ->where('user_id !=?', $siteevent->owner_id)
                ->order('displayname ASC')
                ->limit($this->_getParam('limit', 40));

        if (!empty($noncreate_owner_level)) {
            $str = (string) ( is_array($noncreate_owner_level) ? "'" . join("', '", $noncreate_owner_level) . "'" : $noncreate_owner_level );
            $select->where($userTableName . '.level_id not in (?)', new Zend_Db_Expr($str));
        }

        //FETCH
        $userlists = $tableUser->fetchAll($select);

        //MAKING DATA
        $data = array();
        $mode = $this->_getParam('struct');

        if ($mode == 'text') {
            foreach ($userlists as $userlist) {
                $content_photo = $this->view->itemPhoto($userlist, 'thumb.icon');
                $data[] = array(
                    'id' => $userlist->user_id,
                    'label' => $userlist->displayname,
                    'photo' => $content_photo
                );
            }
        } else {
            foreach ($userlists as $userlist) {
                $content_photo = $this->view->itemPhoto($userlist, 'thumb.icon');
                $data[] = array(
                    'id' => $userlist->user_id,
                    'label' => $userlist->displayname,
                    'photo' => $content_photo
                );
            }
        }

        return $this->_helper->json($data);
    }

    public function setTemplateAction() {

        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteevent_admin_main', array(), 'siteevent_admin_main_template');

        $this->view->form = $form = new Siteevent_Form_Admin_Template();

        $previousHomeTemplate = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.hometemplate', 'template1');
        $previousProfileTemplate = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.profiletemplate', 'template1');
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();

            if (isset($values['siteevent_hometemplate']) && !empty($values['siteevent_hometemplate']) && !empty($previousHomeTemplate) && $values['siteevent_hometemplate'] != $previousHomeTemplate) {
                $templateHome = $values['siteevent_hometemplate'] . "Home";
                Engine_Api::_()->getApi('template', 'siteevent')->$templateHome();
            }

            if (isset($values['siteevent_profiletemplate']) && !empty($values['siteevent_profiletemplate']) && !empty($previousProfileTemplate) && $values['siteevent_profiletemplate'] != $previousProfileTemplate) {
                $templateProfile = $values['siteevent_profiletemplate'] . "Profile";
                Engine_Api::_()->getApi('template', 'siteevent')->$templateProfile();
            }

            foreach ($values as $key => $value) {
                Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
            }

            $form->addNotice('Your changes have been saved.');
        }
    }

}
