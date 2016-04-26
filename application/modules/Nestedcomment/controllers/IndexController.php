<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Nestedcomment
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 6590 2014-11-07 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Nestedcomment_IndexController extends Core_Controller_Action_Standard {

    /**
     * Handles HTTP POST request to comment on an activity feed item
     *
     * Uses the default route and can be accessed from
     *  - /nestedcomment/index/reply
     *
     * @throws Engine_Exception If a user lacks authorization
     * @return void
     */
    public function replyAction() {
        // Make sure user exists
        if (!$this->_helper->requireUser()->isValid())
            return;

        // Make form
        $this->view->form = $form = new Nestedcomment_Form_Reply();
        $isShare = $this->_getParam('isShare');
        // Not post
        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Not a post');
            return;
        }
        $settings = Engine_Api::_()->getApi('settings', 'core');
        // Not valid
        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
            return;
        }
        if (!empty($settings->aaf_composer_value) && ($settings->aaf_composer_value != ($settings->aaf_list_view_value + $settings->aaf_publish_str_value))) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
            return;
        }
        // Start transaction
        $db = Engine_Api::_()->getDbtable('actions', 'advancedactivity')->getAdapter();
        $db->beginTransaction();

        try {
            $viewer = Engine_Api::_()->user()->getViewer();
            $action_id = $this->view->action_id = $this->_getParam('action_id', $this->_getParam('action', null));
            $action = Engine_Api::_()->getDbtable('actions', 'advancedactivity')->getActionById($action_id);
            $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type . "_" . $action->subject_id);
            $body = $form->getValue('body');

            // Check authorization
            if (!Engine_Api::_()->authorization()->isAllowed($action->getCommentObject(), null, 'comment'))
                throw new Engine_Exception('This user is not allowed to reply on this item.');

            // Add the comment
            $subject = $viewer;
            if (Engine_Api::_()->advancedactivity()->isBaseOnContentOwner($viewer, $action->getObject()))
                $subject = $action->getObject();
            if ($subject->getType() == 'siteevent_event') {
                $subject = $subject->getParent();
            }
            $row = $action->comments()->addComment($subject, $body);

            $row->parent_comment_id = $this->_getParam('comment_id', null);

            $row->save();

            // Notifications
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');

            // Add notification for owner of activity (if user and not viewer)
            if ($action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity()) {
                $notifyApi->addNotification($actionOwner, $subject, $action, 'replied', array(
                    'label' => 'post'
                ));
            }

            // Add a notification for all users that commented or like except the viewer and poster
            // @todo we should probably limit this
            $commentedUserNotifications = array();
            foreach ($action->comments()->getAllCommentsUsers() as $notifyUser) {
                if ($notifyUser->getType() == 'user' && $notifyUser->getIdentity() != $viewer->getIdentity() && $notifyUser->getIdentity() != $actionOwner->getIdentity()) {

                    $commentedUserNotifications[] = $notifyUser->getIdentity();
                    $notifyApi->addNotification($notifyUser, $subject, $action, 'replied_replied', array(
                        'label' => 'post'
                    ));
                }
            }

            // Add a notification for all users that commented or like except the viewer and poster
            // @todo we should probably limit this
            foreach ($action->likes()->getAllLikesUsers() as $notifyUser) {
                if (in_array($notifyUser->getIdentity(), $commentedUserNotifications))
                    continue;

                if ($notifyUser->getType() == 'user' && $notifyUser->getIdentity() != $viewer->getIdentity() && $notifyUser->getIdentity() != $actionOwner->getIdentity()) {
                    $notifyApi->addNotification($notifyUser, $subject, $action, 'liked_replied', array(
                        'label' => 'post'
                    ));
                }
            }

            //PAGE COMMENT CREATE NOTIFICATION WORK
            $object_type = $action->object_type;
            $object_id = $action->object_id;

            if ($object_type == 'sitepage_page' && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage')) {
                if (Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitepage')->version) {
                    $sitepage = Engine_Api::_()->getItem('sitepage_page', $object_id);
                    Engine_Api::_()->sitepage()->sendNotificationEmail($sitepage, $action, 'sitepage_activityreply', '', 'Activity Reply');
                }
            } else if ($object_type == 'sitegroup_group') {
                $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $object_id);
                Engine_Api::_()->sitegroup()->sendNotificationEmail($sitegroup, $action, 'sitepage_activityreply', '', 'Activity Reply');
            } else if ($object_type == 'sitestore_store') {
                $sitestore = Engine_Api::_()->getItem('sitestore_store', $object_id);
                Engine_Api::_()->sitestore()->sendNotificationEmail($sitestore, $action, 'sitepage_activityreply', '', 'Activity Reply');
            } else if ($object_type == 'sitebusiness_business') {
                $sitebusiness = Engine_Api::_()->getItem('sitebusiness_business', $object_id);
                Engine_Api::_()->sitebusiness()->sendNotificationEmail($sitebusiness, $action, 'sitebusiness_activityreply', '', 'Activity Reply');
            } else if ($object_type == 'siteevent_event') {
                $siteevent = Engine_Api::_()->getItem('siteevent_event', $object_id);
                Engine_Api::_()->siteevent()->sendNotificationEmail($siteevent, $action, 'siteevent_activityreply', '', 'Activity Reply', null, 'comment', $viewer);
            }

            // Stats
            //Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.comments');

            $attachment = null;
            $attachmentPhotoValue = $this->_getParam('photo_id');
            $attachmentType = $this->_getParam('type');

            if ($attachmentPhotoValue && $attachmentType) {
                $attachment = Engine_Api::_()->getItem('album_photo', $attachmentPhotoValue);
                if (isset($row->attachment_type))
                    $row->attachment_type = ( $attachment ? $attachment->getType() : '' );
                if (isset($row->attachment_id))
                    $row->attachment_id = ( $attachment ? $attachment->getIdentity() : 0 );
                $row->save();
            }

            $composerDatas = $this->getRequest()->getParam('composer', null);

            $tagsArray = array();
            parse_str($composerDatas['tag'], $tagsArray);
            if (!empty($tagsArray)) {

//                if ($action) {
//                    $actionParams = (array) $action->params;
//                    $action->params = array_merge((array) $action->params, array('tags' => $tagsArray));
//                    $action->save();
//                }
                $viewer = Engine_Api::_()->_()->user()->getViewer();
                $type_name = Zend_Registry::get('Zend_Translate')->translate('post');
                if (is_array($type_name)) {
                    $type_name = $type_name[0];
                } else {
                    $type_name = 'post';
                }
                $notificationAPi = Engine_Api::_()->getDbtable('notifications', 'activity');

                foreach ($tagsArray as $key => $tagStrValue) {

                    $tag = Engine_Api::_()->getItemByGuid($key);
                    if (in_array($tag->getIdentity(), $commentedUserNotifications))
                        continue;
                    if ($action && $tag && ($tag instanceof User_Model_User) && !$tag->isSelf($viewer)) {
                        $notificationAPi->addNotification($tag, $viewer, $action, 'tagged', array(
                            'object_type_name' => $type_name,
                            'label' => $type_name,
                        ));
                    } else if ($tag && ($tag instanceof Sitepage_Model_Page)) {
                        $subject_title = $viewer->getTitle();
                        $page_title = $tag->getTitle();
                        foreach ($tag->getPageAdmins() as $owner) {
                            if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                                $notificationAPi->addNotification($owner, $viewer, $action, 'sitepage_tagged', array(
                                    'subject_title' => $subject_title,
                                    'label' => $type_name,
                                    'object_type_name' => $type_name,
                                    'page_title' => $page_title
                                ));
                            }
                        }
                    } else if ($tag && ($tag instanceof Sitebusiness_Model_Business)) {
                        $subject_title = $viewer->getTitle();
                        $business_title = $tag->getTitle();
                        foreach ($tag->getBusinessAdmins() as $owner) {
                            if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                                $notificationAPi->addNotification($owner, $viewer, $action, 'sitebusiness_tagged', array(
                                    'subject_title' => $subject_title,
                                    'label' => $type_name,
                                    'object_type_name' => $type_name,
                                    'business_title' => $business_title
                                ));
                            }
                        }
                    } else if ($tag && ($tag instanceof Sitegroup_Model_Group)) {
                        $subject_title = $viewer->getTitle();
                        $store_title = $tag->getTitle();
                        foreach ($tag->getGroupAdmins() as $owner) {
                            if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                                $notificationAPi->addNotification($owner, $viewer, $action, 'sitegroup_tagged', array(
                                    'subject_title' => $subject_title,
                                    'label' => $type_name,
                                    'object_type_name' => $type_name,
                                    'group_title' => $store_title
                                ));
                            }
                        }
                    } else if ($tag && ($tag instanceof Sitestore_Model_Store)) {
                        $subject_title = $viewer->getTitle();
                        $store_title = $tag->getTitle();
                        foreach ($tag->getStoreAdmins() as $owner) {
                            if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                                $notificationAPi->addNotification($owner, $viewer, $action, 'sitestore_tagged', array(
                                    'subject_title' => $subject_title,
                                    'label' => $type_name,
                                    'object_type_name' => $type_name,
                                    'store_title' => $store_title
                                ));
                            }
                        }
                    } else if ($tag && ($tag instanceof Core_Model_Item_Abstract)) {
                        $subject_title = $viewer->getTitle();
                        $item_type = Zend_Registry::get('Zend_Translate')->translate($tag->getShortType());
                        $item_title = $tag->getTitle();
                        $owner = $tag->getOwner();
                        if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                            $notificationAPi->addNotification($owner, $viewer, $action, 'aaf_tagged', array(
                                'subject_title' => $subject_title,
                                'label' => $type_name,
                                'object_type_name' => $type_name,
                                'item_title' => $item_title,
                                'item_type' => $item_type
                            ));
                        }
                        if (($tag instanceof Group_Model_Group)) {
                            foreach ($tag->getOfficerList()->getAll() as $offices) {
                                $owner = Engine_Api::_()->getItem('user', $offices->child_id);
                                if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                                    $notificationAPi->addNotification($owner, $viewer, $action, 'aaf_tagged', array(
                                        'subject_title' => $subject_title,
                                        'label' => $type_name,
                                        'object_type_name' => $type_name,
                                        'item_title' => $item_title,
                                        'item_type' => $item_type
                                    ));
                                }
                            }
                        }
                    }
                }

                if ($action) {
                    $data = array_merge((array) $action->params, array('tags' => $tagsArray));
                    $row->params = Zend_Json::encode($data);
                }
                $row->save();
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        // Assign message for json
        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Reply posted');

        // Redirect if not json
        if (null === $this->_getParam('format', null)) {
            $this->_redirect($form->return_url->getValue(), array('prependBase' => false));
        } else if ('json' === $this->_getParam('format', null)) {
            $helper = 'advancedActivity';
            if (!empty($isShare)) {
                $helper = 'advancedActivityShare';
            }
            $method = 'update';
            $show_all_comments = $this->_getParam('show_all_comments');

            $onViewPage = $this->_getParam('onViewPage');
            if ($onViewPage) {
                $show_all_comments = true;
            }
            $this->view->body = $this->view->$helper($action, array('noList' => false, 'submitReply' => false, 'onViewPage' => $onViewPage, 'viewAllLikes' => $onViewPage, 'viewAllComments' => $onViewPage, 'hideReply' => true), $method, $show_all_comments);
        }
    }

    public function replyEditAction() {
        // Make sure user exists
        if (!$this->_helper->requireUser()->isValid())
            return;

        // Make form
        $this->view->form = $form = new Nestedcomment_Form_Reply();
        $isShare = $this->_getParam('isShare');
        // Not post
        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Not a post');
            return;
        }
        $settings = Engine_Api::_()->getApi('settings', 'core');
        // Not valid
        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
            return;
        }
        if (!empty($settings->aaf_composer_value) && ($settings->aaf_composer_value != ($settings->aaf_list_view_value + $settings->aaf_publish_str_value))) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
            return;
        }
        // Start transaction
        $db = Engine_Api::_()->getDbtable('actions', 'advancedactivity')->getAdapter();
        $db->beginTransaction();

        try {
            $viewer = Engine_Api::_()->user()->getViewer();
            $action_id = $this->view->action_id = $this->_getParam('action_id', $this->_getParam('action', null));
            $action = Engine_Api::_()->getDbtable('actions', 'advancedactivity')->getActionById($action_id);
            $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type . "_" . $action->subject_id);
            $body = $form->getValue('body');

            // Check authorization
            if (!Engine_Api::_()->authorization()->isAllowed($action->getCommentObject(), null, 'comment'))
                throw new Engine_Exception('This user is not allowed to comment on this item.');

            // Add the comment
            $subject = $viewer;
            if (Engine_Api::_()->advancedactivity()->isBaseOnContentOwner($viewer, $action->getObject()))
                $subject = $action->getObject();
            if ($subject->getType() == 'siteevent_event') {
                $subject = $subject->getParent();
            }
            $row = $action->comments()->getComment($this->_getParam('comment_id', null));
            $row->body = $body;
            $row->save();
            $attachment = null;
            $attachmentPhotoValue = $this->_getParam('photo_id');
            $attachmentType = $this->_getParam('type');
            if ($attachmentPhotoValue && $attachmentType) {
                $attachment = Engine_Api::_()->getItem('album_photo', $attachmentPhotoValue);
                if (isset($row->attachment_type))
                    $row->attachment_type = ( $attachment ? $attachment->getType() : '' );
                if (isset($row->attachment_id))
                    $row->attachment_id = ( $attachment ? $attachment->getIdentity() : 0 );
                $row->save();
            } elseif (!$attachmentPhotoValue && !$attachmentType) {
                if (isset($row->attachment_type))
                    $row->attachment_type = '';
                if (isset($row->attachment_id))
                    $row->attachment_id = '';
                $row->save();
            }
            $composerDatas = $this->getRequest()->getParam('composer', null);

            $tagsArray = array();
            parse_str($composerDatas['tag'], $tagsArray);
            if (!empty($tagsArray)) {

//                if ($action) {
//                    $actionParams = (array) $action->params;
//                    $action->params = array_merge((array) $action->params, array('tags' => $tagsArray));
//                    $action->save();
//                }
                $viewer = Engine_Api::_()->_()->user()->getViewer();
                $type_name = Zend_Registry::get('Zend_Translate')->translate('post');
                if (is_array($type_name)) {
                    $type_name = $type_name[0];
                } else {
                    $type_name = 'post';
                }
                $notificationAPi = Engine_Api::_()->getDbtable('notifications', 'activity');

                foreach ($tagsArray as $key => $tagStrValue) {

                    $tag = Engine_Api::_()->getItemByGuid($key);
                    if (in_array($tag->getIdentity(), $commentedUserNotifications))
                        continue;
                    if ($action && $tag && ($tag instanceof User_Model_User) && !$tag->isSelf($viewer)) {
                        $notificationAPi->addNotification($tag, $viewer, $action, 'tagged', array(
                            'object_type_name' => $type_name,
                            'label' => $type_name,
                        ));
                    } else if ($tag && ($tag instanceof Sitepage_Model_Page)) {
                        $subject_title = $viewer->getTitle();
                        $page_title = $tag->getTitle();
                        foreach ($tag->getPageAdmins() as $owner) {
                            if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                                $notificationAPi->addNotification($owner, $viewer, $action, 'sitepage_tagged', array(
                                    'subject_title' => $subject_title,
                                    'label' => $type_name,
                                    'object_type_name' => $type_name,
                                    'page_title' => $page_title
                                ));
                            }
                        }
                    } else if ($tag && ($tag instanceof Sitebusiness_Model_Business)) {
                        $subject_title = $viewer->getTitle();
                        $business_title = $tag->getTitle();
                        foreach ($tag->getBusinessAdmins() as $owner) {
                            if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                                $notificationAPi->addNotification($owner, $viewer, $action, 'sitebusiness_tagged', array(
                                    'subject_title' => $subject_title,
                                    'label' => $type_name,
                                    'object_type_name' => $type_name,
                                    'business_title' => $business_title
                                ));
                            }
                        }
                    } else if ($tag && ($tag instanceof Sitegroup_Model_Group)) {
                        $subject_title = $viewer->getTitle();
                        $store_title = $tag->getTitle();
                        foreach ($tag->getGroupAdmins() as $owner) {
                            if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                                $notificationAPi->addNotification($owner, $viewer, $action, 'sitegroup_tagged', array(
                                    'subject_title' => $subject_title,
                                    'label' => $type_name,
                                    'object_type_name' => $type_name,
                                    'group_title' => $store_title
                                ));
                            }
                        }
                    } else if ($tag && ($tag instanceof Sitestore_Model_Store)) {
                        $subject_title = $viewer->getTitle();
                        $store_title = $tag->getTitle();
                        foreach ($tag->getStoreAdmins() as $owner) {
                            if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                                $notificationAPi->addNotification($owner, $viewer, $action, 'sitestore_tagged', array(
                                    'subject_title' => $subject_title,
                                    'label' => $type_name,
                                    'object_type_name' => $type_name,
                                    'store_title' => $store_title
                                ));
                            }
                        }
                    } else if ($tag && ($tag instanceof Core_Model_Item_Abstract)) {
                        $subject_title = $viewer->getTitle();
                        $item_type = Zend_Registry::get('Zend_Translate')->translate($tag->getShortType());
                        $item_title = $tag->getTitle();
                        $owner = $tag->getOwner();
                        if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                            $notificationAPi->addNotification($owner, $viewer, $action, 'aaf_tagged', array(
                                'subject_title' => $subject_title,
                                'label' => $type_name,
                                'object_type_name' => $type_name,
                                'item_title' => $item_title,
                                'item_type' => $item_type
                            ));
                        }
                        if (($tag instanceof Group_Model_Group)) {
                            foreach ($tag->getOfficerList()->getAll() as $offices) {
                                $owner = Engine_Api::_()->getItem('user', $offices->child_id);
                                if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                                    $notificationAPi->addNotification($owner, $viewer, $action, 'aaf_tagged', array(
                                        'subject_title' => $subject_title,
                                        'label' => $type_name,
                                        'object_type_name' => $type_name,
                                        'item_title' => $item_title,
                                        'item_type' => $item_type
                                    ));
                                }
                            }
                        }
                    }
                }

                if ($action) {
                    $data = array_merge((array) $action->params, array('tags' => $tagsArray));
                    $row->params = Zend_Json::encode($data);
                }
                $row->save();
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        // Assign message for json
        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Comment posted');

        // Redirect if not json
        if (null === $this->_getParam('format', null)) {
            $this->_redirect($form->return_url->getValue(), array('prependBase' => false));
        } else if ('json' === $this->_getParam('format', null)) {
            $helper = 'advancedActivity';
            if (!empty($isShare)) {
                $helper = 'advancedActivityShare';
            }
            $method = 'update';
            $show_all_comments = $this->_getParam('show_all_comments');

            $onViewPage = $this->_getParam('onViewPage');
            if ($onViewPage) {
                $show_all_comments = true;
            }
            $this->view->body = $this->view->$helper($action, array('noList' => false, 'submitReply' => false, 'onViewPage' => $onViewPage, 'viewAllLikes' => $onViewPage, 'viewAllComments' => $onViewPage, 'hideReply' => true), $method, $show_all_comments);
        }
    }

    /**
     * Handles HTTP POST request to comment on an activity feed item
     *
     * Uses the default route and can be accessed from
     *  - /activity/index/comment
     *
     * @throws Engine_Exception If a user lacks authorization
     * @return void
     */
    public function commentEditAction() {
        // Make sure user exists
        if (!$this->_helper->requireUser()->isValid())
            return;

        // Make form
        $this->view->form = $form = new Activity_Form_Comment();
        $isShare = $this->_getParam('isShare');
        // Not post
        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Not a post');
            return;
        }
        $settings = Engine_Api::_()->getApi('settings', 'core');
        // Not valid
        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
            return;
        }
        if (!empty($settings->aaf_composer_value) && ($settings->aaf_composer_value != ($settings->aaf_list_view_value + $settings->aaf_publish_str_value))) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
            return;
        }
        // Start transaction
        $db = Engine_Api::_()->getDbtable('actions', 'advancedactivity')->getAdapter();
        $db->beginTransaction();

        try {
            $viewer = Engine_Api::_()->user()->getViewer();
            $action_id = $this->view->action_id = $this->_getParam('action_id', $this->_getParam('action', null));
            $action = Engine_Api::_()->getDbtable('actions', 'advancedactivity')->getActionById($action_id);
            $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type . "_" . $action->subject_id);
            $body = $form->getValue('body');

            // Check authorization
            if (!Engine_Api::_()->authorization()->isAllowed($action->getCommentObject(), null, 'comment'))
                throw new Engine_Exception('This user is not allowed to comment on this item.');

            // Add the comment
            $subject = $viewer;
            if (Engine_Api::_()->advancedactivity()->isBaseOnContentOwner($viewer, $action->getObject()))
                $subject = $action->getObject();
            if ($subject->getType() == 'siteevent_event') {
                $subject = $subject->getParent();
            }

            $row = $action->comments()->getComment($this->_getParam('comment_id', null));
            $row->body = $body;
            $row->save();

            $attachment = null;
            $attachmentPhotoValue = $this->_getParam('photo_id');
            $attachmentType = $this->_getParam('type');
            if ($attachmentPhotoValue && $attachmentType) {
                $attachment = Engine_Api::_()->getItem('album_photo', $attachmentPhotoValue);
                if (isset($row->attachment_type))
                    $row->attachment_type = ( $attachment ? $attachment->getType() : '' );
                if (isset($row->attachment_id))
                    $row->attachment_id = ( $attachment ? $attachment->getIdentity() : 0 );
                $row->save();
            } elseif (!$attachmentPhotoValue && !$attachmentType) {
                if (isset($row->attachment_type))
                    $row->attachment_type = '';
                if (isset($row->attachment_id))
                    $row->attachment_id = '';
                $row->save();
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $composerDatas = $this->getRequest()->getParam('composer', null);

        $tagsArray = array();
        parse_str($composerDatas['tag'], $tagsArray);
        if (!empty($tagsArray)) {

            $viewer = Engine_Api::_()->_()->user()->getViewer();
            $type_name = Zend_Registry::get('Zend_Translate')->translate('post');
            if (is_array($type_name)) {
                $type_name = $type_name[0];
            } else {
                $type_name = 'post';
            }
            $notificationAPi = Engine_Api::_()->getDbtable('notifications', 'activity');

            foreach ($tagsArray as $key => $tagStrValue) {

                $tag = Engine_Api::_()->getItemByGuid($key);
                if (in_array($tag->getIdentity(), $commentedUserNotifications))
                    continue;
                if ($action && $tag && ($tag instanceof User_Model_User) && !$tag->isSelf($viewer)) {
                    $notificationAPi->addNotification($tag, $viewer, $action, 'tagged', array(
                        'object_type_name' => $type_name,
                        'label' => $type_name,
                    ));
                } else if ($tag && ($tag instanceof Sitepage_Model_Page)) {
                    $subject_title = $viewer->getTitle();
                    $page_title = $tag->getTitle();
                    foreach ($tag->getPageAdmins() as $owner) {
                        if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                            $notificationAPi->addNotification($owner, $viewer, $action, 'sitepage_tagged', array(
                                'subject_title' => $subject_title,
                                'label' => $type_name,
                                'object_type_name' => $type_name,
                                'page_title' => $page_title
                            ));
                        }
                    }
                } else if ($tag && ($tag instanceof Sitebusiness_Model_Business)) {
                    $subject_title = $viewer->getTitle();
                    $business_title = $tag->getTitle();
                    foreach ($tag->getBusinessAdmins() as $owner) {
                        if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                            $notificationAPi->addNotification($owner, $viewer, $action, 'sitebusiness_tagged', array(
                                'subject_title' => $subject_title,
                                'label' => $type_name,
                                'object_type_name' => $type_name,
                                'business_title' => $business_title
                            ));
                        }
                    }
                } else if ($tag && ($tag instanceof Sitegroup_Model_Group)) {
                    $subject_title = $viewer->getTitle();
                    $store_title = $tag->getTitle();
                    foreach ($tag->getGroupAdmins() as $owner) {
                        if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                            $notificationAPi->addNotification($owner, $viewer, $action, 'sitegroup_tagged', array(
                                'subject_title' => $subject_title,
                                'label' => $type_name,
                                'object_type_name' => $type_name,
                                'group_title' => $store_title
                            ));
                        }
                    }
                } else if ($tag && ($tag instanceof Sitestore_Model_Store)) {
                    $subject_title = $viewer->getTitle();
                    $store_title = $tag->getTitle();
                    foreach ($tag->getStoreAdmins() as $owner) {
                        if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                            $notificationAPi->addNotification($owner, $viewer, $action, 'sitestore_tagged', array(
                                'subject_title' => $subject_title,
                                'label' => $type_name,
                                'object_type_name' => $type_name,
                                'store_title' => $store_title
                            ));
                        }
                    }
                } else if ($tag && ($tag instanceof Core_Model_Item_Abstract)) {
                    $subject_title = $viewer->getTitle();
                    $item_type = Zend_Registry::get('Zend_Translate')->translate($tag->getShortType());
                    $item_title = $tag->getTitle();
                    $owner = $tag->getOwner();
                    if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                        $notificationAPi->addNotification($owner, $viewer, $action, 'aaf_tagged', array(
                            'subject_title' => $subject_title,
                            'label' => $type_name,
                            'object_type_name' => $type_name,
                            'item_title' => $item_title,
                            'item_type' => $item_type
                        ));
                    }
                    if (($tag instanceof Group_Model_Group)) {
                        foreach ($tag->getOfficerList()->getAll() as $offices) {
                            $owner = Engine_Api::_()->getItem('user', $offices->child_id);
                            if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                                $notificationAPi->addNotification($owner, $viewer, $action, 'aaf_tagged', array(
                                    'subject_title' => $subject_title,
                                    'label' => $type_name,
                                    'object_type_name' => $type_name,
                                    'item_title' => $item_title,
                                    'item_type' => $item_type
                                ));
                            }
                        }
                    }
                }
            }

            if ($action) {
                $data = array_merge((array) $action->params, array('tags' => $tagsArray));
                $row->params = Zend_Json::encode($data);
            }
            $row->save();
        }
        // Assign message for json
        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Comment edited');

        // Redirect if not json
        if (null === $this->_getParam('format', null)) {
            $this->_redirect($form->return_url->getValue(), array('prependBase' => false));
        } else if ('json' === $this->_getParam('format', null)) {
            $helper = 'advancedActivity';
            if (!empty($isShare)) {
                $helper = 'advancedActivityShare';
            }
            $method = 'update';
            $show_all_comments = $this->_getParam('show_all_comments');

            $onViewPage = $this->_getParam('onViewPage');
            if ($onViewPage) {
                $show_all_comments = true;
            }
            $this->view->body = $this->view->$helper($action, array('noList' => false, 'submitComment' => false, 'onViewPage' => $onViewPage, 'viewAllLikes' => $onViewPage, 'viewAllComments' => $onViewPage, 'hideReply' => false), $method, $show_all_comments);
        }
    }

}
