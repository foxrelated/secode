<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: DashboardController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_DashboardController extends Core_Controller_Action_Standard {

    //SET THE VALUE FOR ALL ACTION DEFAULT
    public function init() {

        if (!$this->_helper->requireAuth()->setAuthParams('sitegroup_group', null, 'view')->isValid())
            return;

        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext
                ->addActionContext('rate', 'json')
                ->addActionContext('validation', 'html')
                ->initContext();

        $group_url = $this->_getParam('group_url', $this->_getParam('group_url', null));
        $group_id = $this->_getParam('group_id', $this->_getParam('group_id', null));

        if ($group_url) {
            $group_id = Engine_Api::_()->sitegroup()->getGroupId($group_url);
        }

        if ($group_id) {
            $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
            if ($sitegroup) {
                Engine_Api::_()->core()->setSubject($sitegroup);
            }
        }

        //FOR UPDATE EXPIRATION
        if ((Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.task.updateexpiredgroups') + 900) <= time()) {
            Engine_Api::_()->sitegroup()->updateExpiredGroups();
        }
    }

    //ACTION FOR SHOWING THE APPS AT DASHBOARD
    public function appAction() {

        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitegroup_main');

        //VERSION CHECK APPLIED FOR - PACKAGE WORK
        $this->view->siteeventVersion = false;
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')) {
            if (Engine_Api::_()->sitegroup()->checkVersion(Engine_Api::_()->getDbtable('modules', 'core')->getModule('siteevent')->version, '4.8.8')) {
                $this->view->siteeventVersion = true;
            }
        }

        $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0);

        //GET THE LOGGEDIN USER INFORMATION
        $this->view->viewer_id = $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        //GET THE SITEGROUP ID FROM THE URL
        $this->view->group_id = $group_id = $this->_getParam('group_id');

        //SET THE SUBJECT OF SITEGROUP
        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('sitegroup_group');

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($subject, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //END MANAGE-ADMIN CHECK
        //VERSION CHECK APPLIED FOR - PACKAGE WORK
        $this->view->siteeventVersion = false;
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')) {
            $siteeventModule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('siteevent');
            $siteeventVersion = $siteeventModule->version;
            if ($siteeventVersion >= '4.8.8p1') {
                $this->view->siteeventVersion = true;
            }
        }

        $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0);

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum')) {

            //PACKAGE BASE PRIYACY START
            if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                if (Engine_Api::_()->sitegroup()->allowPackageContent($subject->package_id, "modules", "sitegroupalbum")) {
                    $this->view->allowed_upload_photo = 1;
                }
            } else {
                $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($subject, 'spcreate');
                if (!empty($isGroupOwnerAllow)) {
                    $this->view->allowed_upload_photo = 1;
                }
            }
            //START THE GROUP ALBUM WORK
            $this->view->default_album_id = Engine_Api::_()->getItemTable('sitegroup_album')->getDefaultAlbum($group_id)->album_id;
            //END THE GROUP ALBUM WORK

            $this->view->albumtab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroup.photos-sitegroup', $group_id, $layout);
        }

        //PASS THE GROUP ID IN THE CORRESPONDING TPL FILE
        $this->view->sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
        $this->view->sitegroups_view_menu = 16;

        //START THE GROUP POLL WORK
        $sitegroupPollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouppoll');
        if ($sitegroupPollEnabled) {

            //PACKAGE BASE PRIYACY START
            if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                if (Engine_Api::_()->sitegroup()->allowPackageContent($subject->package_id, "modules", "sitegrouppoll")) {
                    $this->view->can_create_poll = 1;
                }
            } else {
                $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($subject, 'splcreate');
                if (!empty($isGroupOwnerAllow)) {
                    $this->view->can_create_poll = 1;
                }
            }
            //PACKAGE BASE PRIYACY END

            $this->view->polltab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegrouppoll.profile-sitegrouppolls', $group_id, $layout);
        }
        //END THE GROUP POLL WORK
        //START THE GROUP DOCUMENT WORK
        $sitegroupDocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument');
        if ($sitegroupDocumentEnabled || (Engine_Api::_()->hasModuleBootstrap('document') && Engine_Api::_()->getDbtable('modules', 'document')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitegroup_group', 'item_module' => 'sitegroup')))) {

            //PACKAGE BASE PRIYACY START
            if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                if (Engine_Api::_()->sitegroup()->allowPackageContent($subject->package_id, "modules", "sitegroupdocument")) {
                    $this->view->can_create_doc = 1;
                }
            } else {
                $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($subject, 'sdcreate');
                if (!empty($isGroupOwnerAllow)) {
                    $this->view->can_create_doc = 1;
                }
            }
            //PACKAGE BASE PRIYACY END

            if ($sitegroupDocumentEnabled) {
                $this->view->documenttab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroupdocument.profile-sitegroupdocuments', $group_id, $layout);
            } else {
                $this->view->documenttab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('document.contenttype-documents', $group_id, $layout);
            }
        }
        //END THE GROUP DOCUMENT WORK
        //START THE GROUP INVITE WORK
        $this->view->can_invite = 0;
        $sitegroupInviteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupinvite');
        if ($sitegroupInviteEnabled) {

            //START MANAGE-ADMIN CHECK
            $this->view->can_invite = Engine_Api::_()->sitegroup()->isManageAdmin($subject, 'invite');
            //END MANAGE-ADMIN CHECK
        }
        //END THE GROUP INVITE WORK
        //START THE GROUP VIDEO WORK
        $sitegroupVideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo');
        if ($sitegroupVideoEnabled || (Engine_Api::_()->hasModuleBootstrap('sitevideo') && Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitegroup_group', 'item_module' => 'sitegroup')))) {

            //PACKAGE BASE PRIYACY START
            if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                if (Engine_Api::_()->sitegroup()->allowPackageContent($subject->package_id, "modules", "sitegroupvideo")) {
                    $this->view->can_create_video = 1;
                }
            } else {
                $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($subject, 'svcreate');
                if (!empty($isGroupOwnerAllow)) {
                    $this->view->can_create_video = 1;
                }
            }
            //PACKAGE BASE PRIYACY END

            if ($sitegroupVideoEnabled) {
                $this->view->videotab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroupvideo.profile-sitegroupvideos', $group_id, $layout);
            } else {
                $this->view->videotab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitevideo.contenttype-videos', $group_id, $layout);
            }
        }
        //END THE GROUP VIDEO WORK
        //START THE GROUP EVENT WORK
        $sitegroupeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent');
        if ($sitegroupeventEnabled || (Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitegroup_group', 'item_module' => 'sitegroup')))) {

            //PACKAGE BASE PRIYACY START
            if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                if (Engine_Api::_()->sitegroup()->allowPackageContent($subject->package_id, "modules", "sitegroupevent")) {
                    $this->view->can_create_event = 1;
                }
            } else {
                $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($subject, 'secreate');
                if (!empty($isGroupOwnerAllow)) {
                    $this->view->can_create_event = 1;
                }
            }
            //PACKAGE BASE PRIYACY END

            if ($sitegroupeventEnabled) {
                $this->view->eventtab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroupevent.profile-sitegroupevents', $group_id, $layout);
            } else {
                $this->view->eventtab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('siteevent.contenttype-events', $group_id, $layout);
            }
        }
        //END THE GROUP EVENT WORK
        //START THE GROUP NOTE WORK
        $sitegroupNoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupnote');
        if ($sitegroupNoteEnabled) {

            //PACKAGE BASE PRIYACY START
            if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                if (Engine_Api::_()->sitegroup()->allowPackageContent($subject->package_id, "modules", "sitegroupnote")) {
                    $this->view->can_create_notes = 1;
                }
            } else {
                $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($subject, 'sncreate');
                if (!empty($isGroupOwnerAllow)) {
                    $this->view->can_create_notes = 1;
                }
            }
            //PACKAGE BASE PRIYACY END

            $this->view->notetab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroupnote.profile-sitegroupnotes', $group_id, $layout);
        }
        //END THE GROUP NOTE WORK
        //START THE GROUP REVEIW WORK
        $sitegroupReviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview');
        if ($sitegroupReviewEnabled) {
            $hasPosted = Engine_Api::_()->getDbTable('reviews', 'sitegroupreview')->canPostReview($subject->group_id, $viewer_id);
            if (empty($hasPosted) && !empty($viewer_id)) {
                $this->view->can_create_review = 1;
            } else {
                $this->view->can_create_review = 0;
            }

            $this->view->reviewtab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroupreview.profile-sitegroupreviews', $group_id, $layout);
        }
        //END THE GROUP REVEIW WORK
        //START THE GROUP DISCUSSION WORK
        $sitegroupDiscussionEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion');
        if ($sitegroupDiscussionEnabled) {

            //START MANAGE-ADMIN CHECK
            $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($subject, 'sdicreate');
            if (!empty($isManageAdmin)) {
                $this->view->can_create_discussion = 1;
            }
            //END MANAGE-ADMIN CHECK
            //PACKAGE BASE PRIYACY START
            if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                if (!Engine_Api::_()->sitegroup()->allowPackageContent($subject->package_id, "modules", "sitegroupdiscussion")) {
                    $this->view->can_create_discussion = 0;
                }
            }
            //PACKAGE BASE PRIYACY END

            $this->view->discussiontab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroup.discussion-sitegroup', $group_id, $layout);
        }
        //END THE GROUP DISCUSSION WORK
        //START THE GROUP FORM WORK
        $sitegroupFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupform');
        if ($sitegroupFormEnabled) {

            //START MANAGE-ADMIN CHECK
            $this->view->can_form = Engine_Api::_()->sitegroup()->isManageAdmin($subject, 'form');
            //END MANAGE-ADMIN CHECK

            $group_id = $this->_getParam('group_id');
            $quetion = Engine_Api::_()->getDbtable('groupquetions', 'sitegroupform');
            $select_quetion = $quetion->select()->where('group_id = ?', $group_id);
            $result_quetion = $quetion->fetchRow($select_quetion);
            $this->view->option_id = $result_quetion->option_id;

            $this->view->formtab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroupform.sitegroup-viewform', $group_id, $layout);
        }
        //END THE GROUP FORM WORK
        //START THE GROUP OFFER WORK
        $this->view->moduleEnable = $sitegroupOfferEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupoffer');
        if ($sitegroupOfferEnabled) {

            //START MANAGE-ADMIN CHECK
            $this->view->can_offer = Engine_Api::_()->sitegroup()->isManageAdmin($subject, 'offer');
            //END MANAGE-ADMIN CHECK

            $this->view->offertab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroupoffer.profile-sitegroupoffers', $group_id, $layout);
        }
        //END THE GROUP OFFER WORK
        //START THE GROUP MUSIC WORK
        $sitegroupMusicEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic');
        if ($sitegroupMusicEnabled) {

            //PACKAGE BASE PRIYACY START
            if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                if (Engine_Api::_()->sitegroup()->allowPackageContent($subject->package_id, "modules", "sitegroupmusic")) {
                    $this->view->can_create_musics = 1;
                }
            } else {
                $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($subject, 'smcreate');
                if (!empty($isGroupOwnerAllow)) {
                    $this->view->can_create_musics = 1;
                }
            }
            //PACKAGE BASE PRIYACY END

            $this->view->musictab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroupmusic.profile-sitegroupmusic', $group_id, $layout);
        }
        //END THE GROUP MUSIC WORK

        $this->view->is_ajax = $this->_getParam('is_ajax', '');
    }

    //ACTION FOR CONTACT INFORMATION
    public function announcementsAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitegroup_main');

        //GET GROUP ID
        $this->view->group_id = $group_id = $this->_getParam('group_id');

        //GET SITEGROUP ITEM
        $this->view->sitegroup = $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //END MANAGE-ADMIN CHECK
        //GET REQUEST IS AJAX OR NOT
        $this->view->is_ajax = $this->_getParam('is_ajax', '');

        //SHOW SELECTED TAB
        $this->view->sitegroups_view_menu = 30;

        $this->view->announcements = Engine_Api::_()->getDbtable('announcements', 'sitegroup')->announcements(array('group_id' => $group_id, 'hideExpired' => 0), array('announcement_id', 'title', 'body', 'startdate', 'expirydate', 'status'));
    }

    //ACTION FOR CONTACT INFORMATION
    public function notificationSettingsAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitegroup_main');

        //GET THE LOGGEDIN USER INFORMATION
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET GROUP ID
        $this->view->group_id = $group_id = $this->_getParam('group_id');

        //GET SITEGROUP ITEM
        $this->view->sitegroup = $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }


        //END MANAGE-ADMIN CHECK
        //GET REQUEST IS AJAX OR NOT
        $this->view->is_ajax = $this->_getParam('is_ajax', '');

        //SHOW SELECTED TAB
        $this->view->sitegroups_view_menu = 31;

        //SET FORM
        $this->view->form = $form = new Sitegroup_Form_NotificationSettings();

        $ManageAdminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup');
        $ManageAdminsTableName = $ManageAdminsTable->info('name');

        $select = $ManageAdminsTable->select()
                ->from($ManageAdminsTableName)
                ->where($ManageAdminsTableName . '.group_id = ?', $group_id)
                ->where($ManageAdminsTableName . '.user_id = ?', $viewer_id);
        $results = $ManageAdminsTable->fetchRow($select);


        //POPULATE FORM
        $this->view->email = $value['email'] = $results["email"];
        $value['action_email'] = json_decode($results['action_email']);

        $this->view->notification = $value['notification'] = $results["notification"];
        $value['action_notification'] = unserialize($results['action_notification']);

        $form->populate($value);
        //CHECK FORM VALIDATION
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            //GET FORM VALUES
            $values = $form->getValues();
            if (isset($values['email'])) {
                $ManageAdminsTable->update(array('email' => $values['email'], 'action_email' => json_encode($values['action_email'])), array('group_id =?' => $group_id, 'user_id =?' => $viewer_id));

                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {

                    if (in_array('posted', $values['action_email']))
                        $email['emailposted'] = 1;
                    else
                        $email['emailposted'] = 0;

                    if (in_array('created', $values['action_email']))
                        $email['emailcreated'] = 1;
                    else
                        $email['emailcreated'] = 0;

                    Engine_Api::_()->getDbtable('membership', 'sitegroup')->update(array('email' => $values['email'], 'action_email' => json_encode($email)), array('group_id =?' => $group_id, 'resource_id =?' => $group_id, 'user_id =?' => $viewer_id));
                }
            }

            if (isset($values['notification'])) {
                $ManageAdminsTable->update(array('notification' => $values['notification'], 'action_notification' => serialize($values['action_notification'])), array('group_id =?' => $group_id, 'user_id =?' => $viewer_id));

                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {

                    if (in_array('posted', $values['action_notification']))
                        $notification['notificationposted'] = 1;
                    else
                        $notification['notificationposted'] = 0;

                    if (in_array('created', $values['action_notification']))
                        $notification['notificationcreated'] = 1;
                    else
                        $notification['notificationcreated'] = 0;

                    if (in_array('follow', $values['action_notification']))
                        $notification['notificationfollow'] = 1;
                    else
                        $notification['notificationfollow'] = 0;

                    if (in_array('like', $values['action_notification']))
                        $notification['notificationlike'] = 1;
                    else
                        $notification['notificationlike'] = 0;

                    if (in_array('comment', $values['action_notification']))
                        $notification['notificationcomment'] = 1;
                    else
                        $notification['notificationcomment'] = 0;

                    if (in_array('join', $values['action_notification']))
                        $notification['notificationjoin'] = 1;
                    else
                        $notification['notificationjoin'] = 0;

                    Engine_Api::_()->getDbtable('membership', 'sitegroup')->update(array('notification' => $values['notification'], 'action_notification' => json_encode($notification)), array('group_id =?' => $group_id, 'resource_id =?' => $group_id, 'user_id =?' => $viewer_id));
                }
            }

            if (empty($results) && $viewer == 1) {
                $form->addError(Zend_Registry::get('Zend_Translate')->_('Your changes will not be reflected as you are not an admin of this group. Please go to the Admin Panel >> Manage >> Members to login to the Group Admin’s account to save your changes.'));
                return;
            } else {
                //SHOW SUCCESS MESSAGE
                $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
            }
        } else {
            $this->view->is_ajax = $this->_getParam('is_ajax', '');
        }
    }

    //ACTION FOR CONTACT INFORMATION
    public function contactAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitegroup_main');

        //GET GROUP ID
        $this->view->group_id = $group_id = $this->_getParam('group_id');

        //GET SITEGROUP ITEM
        $this->view->sitegroup = $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'contact');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //END MANAGE-ADMIN CHECK
        //GET REQUEST IS AJAX OR NOT
        $this->view->is_ajax = $this->_getParam('is_ajax', '');

        //SHOW SELECTED TAB
        $this->view->sitegroups_view_menu = 20;

        //SET FORM
        $this->view->form = $form = new Sitegroup_Form_Contactinfo(array('groupowner' => Engine_Api::_()->user()->getUser($sitegroup->owner_id)));

        //POPULATE FORM
        $value['email'] = $sitegroup->email;
        $value['phone'] = $sitegroup->phone;
        $value['website'] = $sitegroup->website;
        $form->populate($value);

        //CHECK FORM VALIDATION
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            //GET FORM VALUES
            $values = $form->getValues();
            if (isset($values['email'])) {
                $email_id = $values['email'];

                //CHECK EMAIL VALIDATION
                $validator = new Zend_Validate_EmailAddress();
                $validator->getHostnameValidator()->setValidateTld(false);
                if (!empty($email_id)) {
                    if (!$validator->isValid($email_id)) {
                        $form->addError(Zend_Registry::get('Zend_Translate')->_('Please enter a valid email address.'));
                        return;
                    } else {
                        $sitegroup->email = $email_id;
                    }
                } else {
                    $sitegroup->email = $email_id;
                }
            }

            //CHECK PHONE OPTION IS THERE OR NOT
            if (isset($values['phone'])) {
                $sitegroup->phone = $values['phone'];
            }

            //CHECK WEBSITE OPTION IS THERE OR NOT
            if (isset($values['website'])) {
                $sitegroup->website = $values['website'];
            }

            //SAVE VALUES
            $sitegroup->save();

            //SHOW SUCCESS MESSAGE
            $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
        } else {
            $this->view->is_ajax = $this->_getParam('is_ajax', '');
        }
    }

    //ACTION FOR EDIT STYLE
    public function editStyleAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitegroup_main');

        //GET GROUP ID AND GROUP OBJECT
        $this->view->group_id = $group_id = $this->_getParam('group_id');
        $this->view->sitegroup = $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

        if (empty($sitegroup)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $this->view->sitegroups_view_menu = 3;
        $this->view->is_ajax = $this->_getParam('is_ajax', '');

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //END MANAGE-ADMIN CHECK
        //GET FORM
        $this->view->form = $form = new Sitegroup_Form_Style();

        //GET CURRENT ROW
        $tableStyle = Engine_Api::_()->getDbtable('styles', 'core');

        $row = $tableStyle->fetchRow(array('type = ?' => 'sitegroup_group', 'id = ? ' => $group_id));
        $style = $sitegroup->getGroupStyle();

        //POPULATE
        if (!$this->getRequest()->isPost()) {
            $form->populate(array(
                'style' => ( null == $row ? '' : $row->style )
            ));
            return;
        }

        //Whoops, form was not valid
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //GET STYLE
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

        //SAVE IN DATABASE
        if (null == $row) {
            $row = $tableStyle->createRow();
            $row->type = 'sitegroup_group';
            $row->id = $group_id;
        }
        $row->style = $style;
        $row->save();
        $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
    }

    //ACTION FOR ALL LOCATION
    public function allLocationAction() {

        //USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //LOCAITON ENABLE OR NOT
        if (!Engine_Api::_()->sitegroup()->enableLocation()) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitegroup_main');

        $this->view->sitegroups_view_menu = 4;

        //GET GROUP ID, GROUP OBJECT AND THEN CHECK GROUP VALIDATION
        $this->view->group_id = $group_id = $this->_getParam('group_id');

        //$location_id = $this->_getParam('location_id');
        $this->view->sitegroup = $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
        if (empty($sitegroup)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'map');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //END MANAGE-ADMIN CHECK
        if (!empty($sitegroup->location)) {
            $mainLocationId = Engine_Api::_()->getDbtable('locations', 'sitegroup')->getLocationId($sitegroup->group_id, $sitegroup->location);
            $this->view->mainLocationObject = Engine_Api::_()->getItem('sitegroup_location', $mainLocationId);
            $value['mainlocationId'] = $mainLocationId;
        }
        $value['id'] = $sitegroup->getIdentity();
        $value['mapshow'] = 'Map Tab';
        $group = $this->_getParam('group');

        $this->view->location = $paginator = Engine_Api::_()->getDbtable('locations', 'sitegroup')->getLocation($value);

        $paginator->setItemCountPerPage(10);
        $this->view->paginator = $paginator->setCurrentPageNumber($group);
    }

    //ACTION FOR EDIT LOCATION
    public function editLocationAction() {

        //USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //LOCAITON ENABLE OR NOT
        if (!Engine_Api::_()->sitegroup()->enableLocation()) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitegroup_main');

        $this->view->sitegroups_view_menu = 4;

        //GET GROUP ID, GROUP OBJECT AND THEN CHECK GROUP VALIDATION
        $this->view->group_id = $group_id = $this->_getParam('group_id');
        $this->view->sitegroup = $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
        if (empty($sitegroup)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'map');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //END MANAGE-ADMIN CHECK

        $location_id = $this->_getParam('location_id');

        $locationTable = Engine_Api::_()->getDbtable('locations', 'sitegroup');

        $locationFieldEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.locationfield', 1);

        if (empty($location_id)) {
            $location_id = Engine_Api::_()->getDbtable('locations', 'sitegroup')->getLocationId($group_id, $sitegroup->location);
        }
        if ($locationFieldEnable && $location_id) {
            $params['location_id'] = $location_id;
            $params['id'] = $group_id;
            $this->view->location = $location = Engine_Api::_()->getDbtable('locations', 'sitegroup')->getLocation($params);
        }

        //Get form
        if (!empty($location)) {
            $this->view->form = $form = new Sitegroup_Form_Location(array(
                'item' => $sitegroup,
                'location' => $location->location
            ));

            if (!$this->getRequest()->isPost()) {
                $form->populate($location->toarray());
                return;
            }

            //FORM VALIDAITON
            if (!$form->isValid($this->getRequest()->getPost())) {
                return;
            }

            //FORM VALIDAITON
            if ($form->isValid($this->getRequest()->getPost())) {
                $values = $form->getValues();
                unset($values['submit']);
                unset($values['location']);
                unset($values['locationParams']);
                $locationTable->update($values, array('group_id =?' => $group_id, 'location_id =?' => $location_id));
            }
            $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
        }

        $this->view->location = $location = Engine_Api::_()->getDbtable('locations', 'sitegroup')->getLocation($params);
    }

    //ACTION FOR EDIT ADDRESS
    public function addLocationAction() {

        //USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET GROUP ID, GROUP OBJECT AND THEN CHECK GROUP VALIDATION
        $tab_selected_id = $this->_getParam('tab');
        $this->view->group_id = $group_id = $this->_getParam('group_id');
        $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
        if (empty($sitegroup)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'map');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //END MANAGE-ADMIN CHECK

        $this->view->form = $form = new Sitegroup_Form_Address(array('item' => $sitegroup));
        $form->setTitle('Add Location');
        $form->setDescription('Add your location below, then click "Save Location" to save your location.');

        //POPULATE FORM
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();
        if (empty($values['location'])) {
            $itemError = Zend_Registry::get('Zend_Translate')->_("Please enter the location.");
            $form->addError($itemError);
            return;
        }

        if (empty($values['locationParams'])) {
            $itemError = Zend_Registry::get('Zend_Translate')->_("Please select location from the auto-suggest.");
            $form->addError($itemError);
            return;
        }

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {

            if (!empty($values['main_location'])) {
                $sitegroup->location = $values['location'];
                $sitegroup->save();
            }

            $location = array();
            unset($values['submit']);
            $location = $values['location'];
            $locationName = $values['locationname'];
            if (!empty($location)) {
                $sitegroup->setLocation($location, $locationName);
            }
            $db->commit();
            if (!empty($tab_selected_id)) {
                $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => 500,
                    'parentRedirect' => $this->_helper->url->url(array('group_url' => Engine_Api::_()->sitegroup()->getGroupUrl($group_id), 'tab' => $tab_selected_id), 'sitegroup_entry_view', true),
                    'parentRedirectTime' => '2',
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your group location has been added successfully.'))
                ));
            } else {
                $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => 500,
                    'parentRefresh' => 100,
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your group location has been added successfully.'))
                ));
            }
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    //ACTION FOR LEAVE THE JOIN MEMBER.
    public function deleteLocationAction() {

        $group_id = $this->_getParam('group_id');
        $tab_selected_id = $this->_getParam('tab');
        $location_id = $this->_getParam('location_id');
        $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
        $location = Engine_Api::_()->getItem('sitegroup_location', $location_id);
        if ($this->getRequest()->isPost()) {
            if ($location->location == $sitegroup->location) {
                $sitegroup->location = '';
                $sitegroup->save();
            }

            if (!empty($group_id)) {
                Engine_Api::_()->getDbtable('locations', 'sitegroup')->delete(array('location_id =?' => $location_id, 'group_id =?' => $group_id));
            }

            if (!empty($tab_selected_id)) {
                $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => 500,
                    'parentRedirect' => $this->_helper->url->url(array('group_url' => Engine_Api::_()->sitegroup()->getGroupUrl($group_id), 'tab' => $tab_selected_id), 'sitegroup_entry_view', true),
                    'parentRedirectTime' => '2',
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('You have successfully delete location for this group.'))
                ));
            } else {
                $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => 500,
                    'parentRefresh' => 100,
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('You have successfully delete location for this group.'))
                ));
            }
        }
    }

    //ACTION FOR EDIT ADDRESS
    public function editAddressAction() {

        //USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET GROUP ID, GROUP OBJECT AND THEN CHECK GROUP VALIDATION
        $tab_selected_id = $this->_getParam('tab');
        $this->view->group_id = $group_id = $this->_getParam('group_id');
        $location_id = $this->_getParam('location_id');
        $this->view->sitegroup = $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

        $multipleLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.multiple.location', 0);

        $location = Engine_Api::_()->getItem('sitegroup_location', $location_id);

        if (empty($sitegroup)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'map');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //END MANAGE-ADMIN CHECK

        $this->view->form = $form = new Sitegroup_Form_Address(array('item' => $sitegroup));
        $form->setTitle('Edit Location');
        $form->setDescription('Edit your location below, then click "Save Location" to save your location.');

        if (!empty($multipleLocation) && $location->location == $sitegroup->location) {
            $form->main_location->setValue(1);
        }

        //POPULATE FORM
        if (!$this->getRequest()->isPost()) {
            if (!empty($multipleLocation)) {
                $form->populate($location->toArray());
            } else {
                $form->populate($sitegroup->toArray());
            }
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        $values = $form->getValues();

        if (empty($values['location']) && !empty($multipleLocation)) {
            $itemError = Zend_Registry::get('Zend_Translate')->_("Please enter the location.");
            $form->addError($itemError);
            return;
        }

        if (empty($values['locationParams'])) {
            $itemError = Zend_Registry::get('Zend_Translate')->_("Please select location from the auto-suggest.");
            $form->addError($itemError);
            return;
        }

        $locationTable = Engine_Api::_()->getDbtable('locations', 'sitegroup');
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {

            if (!empty($multipleLocation)) {
                if (!empty($values['main_location'])) {
                    $sitegroup->location = $values['location'];
                    $sitegroup->save();
                } elseif ($sitegroup->location == $location->location) {
                    $sitegroup->location = '';
                    $sitegroup->save();
                }
                if ($location->location != $values['location']) {
                    $locationTable->delete(array('location_id =?' => $location_id));
                    $sitegroup->setLocation($values['location']);
                }
            } else {
                if (!empty($values['location']) && $values['location'] != $sitegroup->location) {
                    $locationTable->delete(array('location_id =?' => $location_id));
                    $sitegroup->location = $values['location'];
                    $sitegroup->setLocation($values['location']);
                    $sitegroup->save();
                }
            }

            $location = '';
            $locationName = '';
            unset($values['submit']);
            $location = $values['location'];

            if (isset($values['locationname']))
                $locationName = $values['locationname'];


            if (!empty($location)) {
                // $sitegroup->setLocation();

                if (!empty($multipleLocation)) {
                    $locationTable->update(array('location' => $location, 'locationname' => $locationName), array('group_id =?' => $group_id, 'location_id =?' => $location_id));
                } else {
                    $locationTable->update(array('location' => $location), array('group_id =?' => $group_id, 'location_id =?' => $location_id));
                }
            } else {
                $locationTable->delete(array('group_id =?' => $group_id, 'location_id =?' => $location_id));
            }

            $db->commit();
            if (!empty($tab_selected_id)) {
                $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => 500,
                    'parentRedirect' => $this->_helper->url->url(array('group_url' => Engine_Api::_()->sitegroup()->getGroupUrl($group_id), 'tab' => $tab_selected_id), 'sitegroup_entry_view', true),
                    'parentRedirectTime' => '2',
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your group location has been modified successfully.'))
                ));
            } elseif (!empty($multipleLocation)) {
                $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => 500,
                    'parentRedirect' => $this->_helper->url->url(array('action' => 'all-location', 'group_id' => $group_id), 'sitegroup_dashboard', true),
                    'parentRedirectTime' => '2',
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your group location has been modified successfully.'))
                ));
            } else {
                $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => 500,
                    'parentRefresh' => 100,
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your group location has been modified successfully.'))
                ));
            }
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    //ACTION FOR FEATURED OWNER
    public function featuredOwnersAction() {

        //USER VALIDAITON
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitegroup_main');

        $this->view->sitegroups_view_menu = 17;

        //GET GROUP ID AND GROUP OBJECT
        $this->view->group_id = $group_id = $this->_getParam('group_id', null);
        $this->view->sitegroup = $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

        //EDIT PRIVACY
        $editPrivacy = 0;
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
        if (!empty($isManageAdmin)) {
            $editPrivacy = 1;
        }

        $manageAdminAllowed = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.manageadmin', 1);
        if (empty($editPrivacy) || empty($manageAdminAllowed)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //GET FEATURED ADMINS
        $this->view->featuredhistories = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->featuredAdmins($group_id);

        $this->view->is_ajax = $this->_getParam('is_ajax', '');
    }

    //ACTION FOR MAKING FAVOURITE
    public function favouriteAction() {

        //USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GETTING VIEWER ID
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        //GET THE SUBJECT
        $getGroupId = $this->_getParam('group_id', $this->_getParam('id', null));
        $this->view->sitegroup = $sitegroup = Engine_Api::_()->core()->getSubject();
        $group_id = $sitegroup->group_id;

        //CALLING THE FUNCTION AND PASS THE VALUES OF GROUP ID AND USER ID.
        $this->view->userListings = Engine_Api::_()->getDbtable('groups', 'sitegroup')->getGroups($group_id, $viewer_id);

        //CHECK POST.
        if ($this->getRequest()->isPost()) {

            //GET VALUE FROM THE FORM.
            $values = $this->getRequest()->getPost();
            $selected_group_id = $values['group_id'];
            if (!empty($selected_group_id)) {

                $favouritesTable = Engine_Api::_()->getDbtable('favourites', 'sitegroup');
                $row = $favouritesTable->createRow();
                $row->group_id = $selected_group_id;
                $row->group_id_for = $getGroupId;
                $row->owner_id = $viewer_id;
                $row->save();

                $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => 100,
                    'parentRefresh' => 100,
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your Group has been successfully linked.'))
                ));
            }
        }
        //RENDER THE SCRIPT.
        $this->renderScript('dashboard/favourite.tpl');
    }

    //ACTION FOR DELETING THE FAVOURITE
    public function favouriteDeleteAction() {

        //USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GETTING THE VIEWER ID.
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        //GET THE SUBJECT AND CHECK AUTH.
        $this->view->sitegroup = $sitegroup = Engine_Api::_()->core()->getSubject();
        $group_id = $sitegroup->group_id;

        //CALLING THE FUNCTION.
        $this->view->userListings = Engine_Api::_()->getDbtable('favourites', 'sitegroup')->deleteLink($group_id, $viewer_id);

        //CHECK POST.
        if ($this->getRequest()->isPost()) {

            $values = $this->getRequest()->getPost();
            $group_id_for = $group_id;
            $group_id = $values['group_id'];
            if (!empty($group_id)) {

                //DELETE THE RESULT FORM THE TABLE.
                $sitegroupTable = Engine_Api::_()->getDbtable('favourites', 'sitegroup');
                $sitegroupTable->delete(array('group_id =?' => $group_id, 'group_id_for =?' => $group_id_for));

                $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => 100,
                    'parentRefresh' => 100,
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your Group has been successfully unlinked.'))
                ));
            }
        }
        //RENDER THE SCRIPT.
        $this->renderScript('dashboard/favourite-delete.tpl');
        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($subject, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //END MANAGE-ADMIN CHECK
        //VERSION CHECK APPLIED FOR - PACKAGE WORK
        $this->view->siteeventVersion = false;
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')) {
            if (Engine_Api::_()->sitegroup()->checkVersion(Engine_Api::_()->getDbtable('modules', 'core')->getModule('siteevent')->version, '4.8.8')) {
                $this->view->siteeventVersion = true;
            }
        }
    }

    //ACTION FOR FOURSQUARE CODE
    public function foursquareAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;

        //SMOOTHBOX
        if (null === $this->_helper->ajaxContext->getCurrentContext()) {
            $this->_helper->layout->setLayout('default-simple');
        } else {
            //NO LAYOUT
            $this->_helper->layout->disableLayout(true);
        }

        //GET GROUP ID AND SITEGROUP OBJECT
        $siteagroup = Engine_Api::_()->getItem('sitegroup_group', $this->_getParam('group_id'));

        //GENERATE FORM
        $this->view->form = $form = new Sitegroup_Form_Foursquare();

        //POPULATE THE FORM
        $form->populate($siteagroup->toArray());

        if (!$this->getRequest()->isPost())
            return;

        //SAVE THE FOURSQUARE CODE IN DATABASE
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $db = Engine_Api::_()->getDbtable('groups', 'sitegroup')->getAdapter();
            $db->beginTransaction();
            try {
                $siteagroup->foursquare_text = $_POST['foursquare_text'];
                $siteagroup->save();
                $db->commit();
            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => false,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Text saved successfully.'))
            ));
        }
    }

    //ACTION: GET-STARTED
    public function getStartedAction() {

        //USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitegroup_main');

        //VIEWER INFORMATION
        $this->view->viewer_id = $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        //GET GROUP ID AND GROUP SUBJECT
        $this->view->group_id = $group_id = $this->_getParam('group_id');
        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('sitegroup_group');

        //GET PHOTO ID
        $this->view->photo_id = $subject->photo_id;

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($subject, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //END MANAGE-ADMIN CHECK
        //VERSION CHECK APPLIED FOR - PACKAGE WORK
        $this->view->siteeventVersion = false;
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')) {
            $siteeventModule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('siteevent');
            $siteeventVersion = $siteeventModule->version;
            if ($siteeventVersion >= '4.8.8p1') {
                $this->view->siteeventVersion = true;
            }
        }

        $this->view->is_ajax = $this->_getParam('is_ajax', '');

        //OVERVIEW PRIVACY
        $this->view->overviewPrivacy = 0;
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($subject, 'overview');
        if (!empty($isManageAdmin)) {
            $this->view->overviewPrivacy = 1;
        }

        $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0);

        //START GROUP ALBUM WORK
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum')) {

            //PACKAGE BASE PRIYACY START
            if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                if (Engine_Api::_()->sitegroup()->allowPackageContent($subject->package_id, "modules", "sitegroupalbum")) {
                    $this->view->allowed_upload_photo = 1;
                }
            } else {
                $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($subject, 'spcreate');
                if (!empty($isGroupOwnerAllow)) {
                    $this->view->allowed_upload_photo = 1;
                }
            }
            //START THE GROUP ALBUM WORK
            $this->view->default_album_id = Engine_Api::_()->getItemTable('sitegroup_album')->getDefaultAlbum($group_id)->album_id;
            //END THE GROUP ALBUM WORK

            $this->view->albumtab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroup.photos-sitegroup', $group_id, $layout);
        }
        //END GROUP ALBUM WORK
        //GET GROUP OBJECT
        $this->view->sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
        $this->view->sitegroups_view_menu = 12;

        //START THE GROUP POLL WORK
        $sitegroupPollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouppoll');
        if ($sitegroupPollEnabled) {

            //PACKAGE BASE PRIYACY START
            if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                if (Engine_Api::_()->sitegroup()->allowPackageContent($subject->package_id, "modules", "sitegrouppoll")) {
                    $this->view->can_create_poll = 1;
                }
            } else {
                $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($subject, 'splcreate');
                if (!empty($isGroupOwnerAllow)) {
                    $this->view->can_create_poll = 1;
                }
            }
            //PACKAGE BASE PRIYACY END

            $this->view->polltab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegrouppoll.profile-sitegrouppolls', $group_id, $layout);
        }
        //END THE GROUP POLL WORK
        //START THE GROUP DOCUMENT WORK
        $sitegroupDocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument');
        if ($sitegroupDocumentEnabled || (Engine_Api::_()->hasModuleBootstrap('document') && Engine_Api::_()->getDbtable('modules', 'document')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitegroup_group', 'item_module' => 'sitegroup')))) {

            //PACKAGE BASE PRIYACY START
            if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                if (Engine_Api::_()->sitegroup()->allowPackageContent($subject->package_id, "modules", "sitegroupdocument")) {
                    $this->view->can_create_doc = 1;
                }
            } else {
                $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($subject, 'sdcreate');
                if (!empty($isGroupOwnerAllow)) {
                    $this->view->can_create_doc = 1;
                }
            }
            //PACKAGE BASE PRIYACY END

            if ($sitegroupDocumentEnabled) {
                $this->view->documenttab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroupdocument.profile-sitegroupdocuments', $group_id, $layout);
            } else {
                $this->view->documenttab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('document.contenttype-documents', $group_id, $layout);
            }
        }
        //END THE GROUP DOCUMENT WORK
        //START THE GROUP INVITE WORK
        $this->view->can_invite = 0;
        $sitegroupInviteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupinvite');
        if ($sitegroupInviteEnabled) {

            //START MANAGE-ADMIN CHECK
            $this->view->can_invite = Engine_Api::_()->sitegroup()->isManageAdmin($subject, 'invite');
            //END MANAGE-ADMIN CHECK
        }
        //END THE GROUP INVITE WORK
        //START THE GROUP VIDEO WORK
        $sitegroupVideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo');
        if ($sitegroupVideoEnabled || (Engine_Api::_()->hasModuleBootstrap('sitevideo') && Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitegroup_group', 'item_module' => 'sitegroup')))) {

            //PACKAGE BASE PRIYACY START
            if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                if (Engine_Api::_()->sitegroup()->allowPackageContent($subject->package_id, "modules", "sitegroupvideo")) {
                    $this->view->can_create_video = 1;
                }
            } else {
                $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($subject, 'svcreate');
                if (!empty($isGroupOwnerAllow)) {
                    $this->view->can_create_video = 1;
                }
            }
            //PACKAGE BASE PRIYACY END

            if ($sitegroupVideoEnabled) {
                $this->view->videotab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroupvideo.profile-sitegroupvideos', $group_id, $layout);
            } else {
                $this->view->videotab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitevideo.contenttype-videos', $group_id, $layout);
            }
        }
        //END THE GROUP VIDEO WORK
        $sitegroupeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent');
        //START THE GROUP EVENT WORK
        if ($sitegroupeventEnabled || (Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitegroup_group', 'item_module' => 'sitegroup')))) {

            //PACKAGE BASE PRIYACY START
            if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                if (Engine_Api::_()->sitegroup()->allowPackageContent($subject->package_id, "modules", "sitegroupevent")) {
                    $this->view->can_create_event = 1;
                }
            } else {
                $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($subject, 'secreate');
                if (!empty($isGroupOwnerAllow)) {
                    $this->view->can_create_event = 1;
                }
            }
            //PACKAGE BASE PRIYACY END

            if ($sitegroupeventEnabled) {
                $this->view->eventtab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroupevent.profile-sitegroupevents', $group_id, $layout);
            } else {
                $this->view->eventtab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('siteevent.contenttype-events', $group_id, $layout);
            }
        }
        //END THE GROUP EVENT WORK
        //START THE GROUP NOTE WORK
        $sitegroupNoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupnote');
        if ($sitegroupNoteEnabled) {

            //PACKAGE BASE PRIYACY START
            if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                if (Engine_Api::_()->sitegroup()->allowPackageContent($subject->package_id, "modules", "sitegroupnote")) {
                    $this->view->can_create_notes = 1;
                }
            } else {
                $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($subject, 'sncreate');
                if (!empty($isGroupOwnerAllow)) {
                    $this->view->can_create_notes = 1;
                }
            }
            //PACKAGE BASE PRIYACY END

            $this->view->notetab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroupnote.profile-sitegroupnotes', $group_id, $layout);
        }
        //END THE GROUP NOTE WORK
        //START THE GROUP REVEIW WORK
        $sitegroupReviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview');
        if ($sitegroupReviewEnabled) {
            $hasPosted = Engine_Api::_()->getDbTable('reviews', 'sitegroupreview')->canPostReview($subject->group_id, $viewer_id);
            if (empty($hasPosted) && !empty($viewer_id)) {
                $this->view->can_create_review = 1;
            } else {
                $this->view->can_create_review = 0;
            }

            $this->view->reviewtab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroupreview.profile-sitegroupreviews', $group_id, $layout);
        }
        //END THE GROUP REVEIW WORK
        //START THE GROUP DISCUSSION WORK
        $sitegroupDiscussionEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion');
        if ($sitegroupDiscussionEnabled) {

            //START MANAGE-ADMIN CHECK
            $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($subject, 'sdicreate');
            if (!empty($isManageAdmin)) {
                $this->view->can_create_discussion = 1;
            }
            //END MANAGE-ADMIN CHECK
            //PACKAGE BASE PRIYACY START
            if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                if (!Engine_Api::_()->sitegroup()->allowPackageContent($subject->package_id, "modules", "sitegroupdiscussion")) {
                    $this->view->can_create_discussion = 0;
                }
            }
            //PACKAGE BASE PRIYACY END

            $this->view->discussiontab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroup.discussion-sitegroup', $group_id, $layout);
        }
        //END THE GROUP DISCUSSION WORK
        //START THE GROUP OFFER WORK
        $this->view->moduleEnable = $sitegroupOfferEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupoffer');
        if ($sitegroupOfferEnabled) {

            //START MANAGE-ADMIN CHECK
            $this->view->can_offer = Engine_Api::_()->sitegroup()->isManageAdmin($subject, 'offer');
            //END MANAGE-ADMIN CHECK

            $this->view->offertab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroupoffer.profile-sitegroupoffers', $group_id, $layout);
        }
        //END THE GROUP OFFER WORK
        //START THE GROUP FORM WORK
        $sitegroupFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupform');
        if ($sitegroupFormEnabled) {

            //START MANAGE-ADMIN CHECK
            $this->view->can_form = Engine_Api::_()->sitegroup()->isManageAdmin($subject, 'form');
            //END MANAGE-ADMIN CHECK

            $group_id = $this->_getParam('group_id');
            $quetion = Engine_Api::_()->getDbtable('groupquetions', 'sitegroupform');
            $select_quetion = $quetion->select()->where('group_id = ?', $group_id);
            $result_quetion = $quetion->fetchRow($select_quetion);
            $this->view->option_id = $result_quetion->option_id;

            $this->view->formtab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroupform.sitegroup-viewform', $group_id, $layout);
        }
        //END THE GROUP FORM WORK
        //START THE GROUP MUSIC WORK
        $sitegroupMusicEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic');
        if ($sitegroupMusicEnabled) {

            //PACKAGE BASE PRIYACY START
            if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                if (Engine_Api::_()->sitegroup()->allowPackageContent($subject->package_id, "modules", "sitegroupmusic")) {
                    $this->view->can_create_musics = 1;
                }
            } else {
                $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($subject, 'smcreate');
                if (!empty($isGroupOwnerAllow)) {
                    $this->view->can_create_musics = 1;
                }
            }
            //PACKAGE BASE PRIYACY END

            $this->view->musictab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroupmusic.profile-sitegroupmusic', $group_id, $layout);
        }
        //END THE GROUP MUSIC WORK
        //$this->view->updatestab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('activity.feed', $group_id, $layout);
    }

    //ACTION FOR HIDE THE PHOTO
    public function hidePhotoAction() {

        //SET LAYOUT
        $this->_helper->layout->setLayout('default-simple');

        //GET AJAX VALUE
        $is_ajax = $this->_getParam('isajax', '');

        //IF REQUEST IS NOT AJAX THEN ONLY SHOW FORM
        if (empty($is_ajax)) {
            $this->view->form = $form = new Sitegroup_Form_Hidephoto();
        } else {
            Engine_Api::_()->getDbtable('photos', 'sitegroup')->update(array('photo_hide' => 1), array('photo_id = ?' => $this->_getParam('hide_photo_id', null)));
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
            ));
        }
    }

    //ACTION FOR SHOW MARKETING GROUP
    public function marketingAction() {

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitegroup_main');

        //GET VIEWER IDENTITY
        $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        //GET GROUP ID AND SITEGROUP OBJECT
        $this->view->group_id = $group_id = $this->_getParam('group_id', null);
        $this->view->sitegroup = $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $this->view->enableFoursquare = 1;
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'foursquare');
        if (empty($isManageAdmin)) {
            $this->view->enableFoursquare = 0;
        }

        $this->view->enabletwitter = $sitegrouptwitterEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouptwitter');
        if ($sitegrouptwitterEnabled) {
            $this->view->enabletwitter = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'twitter');
        }


        $this->view->enableInvite = 1;
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'invite');
        if (empty($isManageAdmin)) {
            $this->view->enableInvite = 0;
        }

        $this->view->enableSendUpdate = 1;
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'sendupdate');
        if (empty($isManageAdmin)) {
            $this->view->enableSendUpdate = 0;
        }

        $sitegroupLikeboxEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouplikebox');
        if (!empty($sitegroupLikeboxEnabled))
            $this->view->enableLikeBox = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'likebox');
        //END MANAGE-ADMIN CHECK

        $this->view->sitegroups_view_menu = 20;
        $this->view->is_ajax = $this->_getParam('is_ajax', '');

        $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0);
        $this->view->fblikebox_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroup.fblikebox-sitegroup', $group_id, $layout);
    }

    //ACTION FOR CREATING OVERVIEW
    public function overviewAction() {

        //USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitegroup_main');

        //GET GROUP ID, GROUP OBJECT AND GROUP VALIDAITON
        $this->view->group_id = $group_id = $this->_getParam('group_id');
        $this->view->sitegroup = $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
        if (empty($sitegroup)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'overview');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //END MANAGE-ADMIN CHECK

        $overview = '';
        if (!empty($sitegroup->overview)) {
            $overview = $sitegroup->overview;
        }

        //FORM GENERATION
        $this->view->form = $form = new Sitegroup_Form_Overview();

        if (!$this->getRequest()->isPost()) {

            $saved = $this->_getParam('saved');
            if (!empty($saved))
                $this->view->success = Zend_Registry::get('Zend_Translate')->_('Your group has been successfully created. You can enhance your group from this dashboard by creating other components.');
        }

        if ($this->getRequest()->isPost()) {

            $overview = $_POST['body'];
            $sitegroup->overview = $overview;
            $sitegroup->save();
            $this->view->form = $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
        }
        $values['body'] = $overview;
        $form->populate($values);
        $this->view->sitegroups_view_menu = 2;
    }

    //ACTION FOR CHANGING THE GROUP PROFILE PICTURE
    public function profilePictureAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitegroup_main');

        //GET GROUP ID
        $this->view->group_id = $group_id = $this->_getParam('group_id');

        //GET SITEGROUP ITEM
        $this->view->sitegroup = $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

        //GET SELECTED TAB
        $this->view->sitegroups_view_menu = 22;

        //GET REQUEST IS ISAJAX OR NOT
        $this->view->is_ajax = $this->_getParam('is_ajax', '');

        //GET FORM
        $this->view->form = $form = new Sitegroup_Form_Photo();

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
            $db = $sitegroup->getTable()->getAdapter();
            $db->beginTransaction();
            //PROCESS
            try {

                //SET PHOTO
                $sitegroup->setPhoto($form->Filedata);

                //SET ALBUMS PARAMS
                $paramsAlbum = array();
                $paramsAlbum['group_id'] = $group_id;
                $paramsAlbum['default_value'] = 1;
                $paramsAlbum['limit'] = 1;

                //FETCH PHOTO ID
                $photo_id = Engine_Api::_()->getItemTable('sitegroup_album')->getDefaultAlbum($sitegroup->group_id)->photo_id;
                if ($photo_id == 0) {
                    Engine_Api::_()->getItemTable('sitegroup_album')->update(array('photo_id' => $sitegroup->photo_id, 'owner_id' => $sitegroup->owner_id), array('group_id = ?' => $sitegroup->group_id, 'default_value = ?' => 1));
                }
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
            $iProfile = $storage->get($sitegroup->photo_id, 'thumb.profile');
            $iSquare = $storage->get($sitegroup->photo_id, 'thumb.icon');
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

        return $this->_helper->redirector->gotoRoute(array('action' => 'profile-picture', 'group_id' => $group_id), 'sitegroup_dashboard', true);
    }

    //ACTION FOR FILL THE DATA OF PROFILE TYPE
    public function profileTypeAction() {

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitegroup_main');

        //GET GROUP ID AND SITEGROUP OBJECT
        $this->view->group_id = $group_id = $this->_getParam('group_id', null);
        $this->view->sitegroup = $sitegroup = Engine_Api::_()->core()->getSubject();
        ;

        //GET PROFILE TYPE
        $profile_type_exist = $sitegroup->profile_type;

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //END MANAGE-ADMIN CHECK
        $this->view->sitegroups_view_menu = 10;

        //PROFILE FIELDS FORM DATA
        $aliasedFields = $sitegroup->fields()->getFieldsObjectsByAlias();
        $this->view->topLevelId = $topLevelId = 0;
        $this->view->topLevelValue = $topLevelValue = null;
        if (isset($aliasedFields['profile_type'])) {
            $aliasedFieldValue = $aliasedFields['profile_type']->getValue($sitegroup);
            $topLevelId = $aliasedFields['profile_type']->field_id;
            $topLevelValue = ( is_object($aliasedFieldValue) ? $aliasedFieldValue->value : null );
            if (!$topLevelId || !$topLevelValue) {
                $topLevelId = null;
                $topLevelValue = null;
            }
            $this->view->topLevelId = $topLevelId;
            $this->view->topLevelValue = $topLevelValue;
        }

        //GET FORM
        $form = $this->view->form = new Fields_Form_Standard(array(
            'item' => Engine_Api::_()->core()->getSubject(),
            'topLevelId' => $topLevelId,
            'topLevelValue' => $topLevelValue,
        ));
        $form->submit->setLabel('Save Info');
        $form->setTitle('Edit Group Profile Info');
        if (empty($profile_type_exist)) {
            $form->setDescription('Profile information enables you to add additional information about your group depending on its category. This non-generic additional information will help others know more specific details about your group. First select a relevant Profile Type for your group, and then fill the corresponding profile information fields.');
        } else {
            $form->setDescription('Profile information enables you to add additional information about your group depending on its category. This non-generic additional information will help others know more specific details about your group.');
        }

        //SAVE DATA IF POSTED
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $form->saveValues();
            $values = $this->getRequest()->getPost();

            $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));

            $group_id = $this->_getParam('group_id', null);

            if (isset($values['0_0_1']) && !empty($values['0_0_1'])) {
                $profile_type = $values['0_0_1'];
                $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
                $sitegroup->profile_type = $profile_type;
                $sitegroup->save();
            }
        }

        //IF PACKAGE INABLE
        if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {

            $profileField_level = Engine_Api::_()->sitegroup()->getPackageProfileLevel($group_id);
            if (empty($profileField_level)) {
                return $this->_forward('requireauth', 'error', 'core');
            }
            if ($profileField_level == 2) {
                $fieldsProfile = array("0_0_1", "submit");

                //PROFILE SELECT WORK
                if (empty($sitegroup->profile_type)) {
                    $profileType = $form->getElement('0_0_1')
                            ->getMultiOptions();

                    $profileTypePackage = Engine_Api::_()->sitegroup()->getSelectedProfilePackage($group_id);

                    $profileTypeFinal = array_intersect_key($profileType, $profileTypePackage);

                    //ONLY SET SELECTED PROFILE TYPE
                    $profileType = $form->getElement('0_0_1')
                            ->setMultiOptions($profileTypeFinal);
                    if (count($profileTypeFinal) <= 1) {
                        $form->removeElement("0_0_1");
                        $form->removeElement("submit");
                        $error = Zend_Registry::get('Zend_Translate')->_("There are no profile fields available.");
                        $form->addError($error);
                    }
                }

                $field_id = array();
                $fieldsProfile_2 = Engine_Api::_()->sitegroup()->getProfileFields($group_id);
                $fieldsProfile = array_merge($fieldsProfile, $fieldsProfile_2);

                //PROFILE FIELD IS SELECTED BUT THERE ARE NOT ANY PROFILE FIELDS
                if (!empty($sitegroup->profile_type)) {
                    $profile_field_flage = true;
                    foreach ($fieldsProfile_2 as $k => $v) {
                        $explodeField = explode("_", $v);
                        if ($explodeField['1'] == $sitegroup->profile_type) {
                            $profile_field_flage = false;
                            break;
                        }
                    }

                    if ($profile_field_flage) {
                        $form->removeElement("submit");
                        $error = Zend_Registry::get('Zend_Translate')->_("There are no profile fields available.");
                        $form->addError($error);
                    }
                }

                foreach ($fieldsProfile_2 as $k => $v) {
                    $explodeField = explode("_", $v);
                    $field_id[] = $explodeField['2'];
                }

                $elements = $form->getElements();
                foreach ($elements as $key => $value) {
                    $explode = explode("_", $key);
                    if ($explode['0'] != "1" && $explode['0'] != "submit") {
                        if (in_array($explode['0'], $field_id)) {
                            $field_id[] = $explode['2'];
                            $fieldsProfile[] = $key;
                            continue;
                        }
                    }

                    if (!in_array($key, $fieldsProfile)) {
                        $form->removeElement($key);
                        $form->addElement('Hidden', $key, array(
                            "value" => "",
                        ));
                    }
                }
            }
        }//END PACKAGE WORK
        else {
            //START LEVEL CHECKS
            $group_owner = Engine_Api::_()->getItem('user', $sitegroup->owner_id);
            $can_profile = Engine_Api::_()->authorization()->getPermission($group_owner->level_id, "sitegroup_group", "profile");
            if (empty($can_profile)) {
                return $this->_forward('requireauth', 'error', 'core');
            }

            if ($can_profile == 2) {
                $fieldsProfile = array("0_0_1", "submit");

                //PROFILE SELECT WORK
                if (empty($sitegroup->profile_type)) {
                    $profileType = $form->getElement('0_0_1')
                            ->getMultiOptions();

                    $profileTypePackage = Engine_Api::_()->sitegroup()->getSelectedProfileLevel($group_owner->level_id);

                    $profileTypeFinal = array_intersect_key($profileType, $profileTypePackage);

                    //ONLY SET SELECTED PROFILE TYPE
                    $profileType = $form->getElement('0_0_1')
                            ->setMultiOptions($profileTypeFinal);
                    if (count($profileTypeFinal) <= 1) {
                        $form->removeElement("0_0_1");
                        $form->removeElement("submit");
                        $error = Zend_Registry::get('Zend_Translate')->_("There are no profile fields available.");
                        $form->addError($error);
                    }
                }
                $fieldsProfile_2 = Engine_Api::_()->sitegroup()->getLevelProfileFields($group_owner->level_id);
                $fieldsProfile = array_merge($fieldsProfile, $fieldsProfile_2);


                //PROFILE FIELD IS SELECTED BUT THERE ARE NOT ANY PROFILE FIELDS
                if (!empty($sitegroup->profile_type)) {
                    $profile_field_flage = true;
                    foreach ($fieldsProfile_2 as $k => $v) {
                        $explodeField = explode("_", $v);
                        if ($explodeField['1'] == $sitegroup->profile_type) {
                            $profile_field_flage = false;
                            break;
                        }
                    }

                    if ($profile_field_flage) {
                        $form->removeElement("submit");
                        $error = Zend_Registry::get('Zend_Translate')->_("There are no profile fields available.");
                        $form->addError($error);
                    }
                }

                foreach ($fieldsProfile_2 as $k => $v) {
                    $explodeField = explode("_", $v);
                    $field_id[] = $explodeField['2'];
                }
                $elements = $form->getElements();
                foreach ($elements as $key => $value) {

                    $explode = explode("_", $key);
                    if ($explode['0'] != "1" && $explode['0'] != "submit") {
                        if (in_array($explode['0'], $field_id)) {
                            $field_id[] = $explode['2'];
                            $fieldsProfile[] = $key;
                            continue;
                        }
                    }
                    if (!in_array($key, $fieldsProfile)) {
                        $form->removeElement($key);
                        $form->addElement('Hidden', $key, array(
                            "value" => "",
                        ));
                    }
                }
            }//END LEVEL WORK
        }
    }

    //ACTION FOR REMOVE THE PHOTO
    public function removePhotoAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitegroup_main', array(), 'sitegroup_main_manage');

        //GET SITEGROUP ITEM
        $this->view->sitegroup = $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $this->_getParam('group_id'));

        //CHECK FORM SUBMIT
        if (isset($_POST['submit']) && $_POST['submit'] == 'submit') {
            $sitegroup->photo_id = 0;
            $sitegroup->save();
            return $this->_helper->redirector->gotoRoute(array('action' => 'profile-picture', 'group_id' => $sitegroup->group_id), 'sitegroup_dashboard', true);
        }
    }

    //ACTION FOR UNHIDE THE PHOTO
    public function unhidePhotoAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //SET LAYOUT
        $this->_helper->layout->setLayout('default-simple');

        //UNHIDE PHOTO FORM
        $this->view->form = $form = new Sitegroup_Form_Unhidephoto();

        //CHECK FORM VALIDAITON
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            Engine_Api::_()->getDbtable('photos', 'sitegroup')->update(array('photo_hide' => 0), array('group_id = ?' => $this->_getParam('group_id', null), 'photo_hide = ?' => 1));
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'format' => 'smoothbox',
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your photos have been restored.'))
            ));
        }
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

        //GROUP ID
        $group_id = $this->_getParam('group_id');

        $special = $this->_getParam('special', 'overview');
        $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

        //START MANAGE-ADMIN CHECK
        $can_edit = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');

        if ($special == 'overview') {
            $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'overview');
            if (empty($can_edit)) {
                return $this->_forward('requireauth', 'error', 'core');
            }

            if (empty($isManageAdmin)) {
                return $this->_forward('requireauth', 'error', 'core');
            }
        } else {
            $photoCreate = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'spcreate');
            if (empty($can_edit) && empty($photoCreate)) {
                return $this->_forward('requireauth', 'error', 'core');
            }
        }
        //END MANAGE-ADMIN CHECK
        //END MANAGE-ADMIN CHECK
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
        $db = Engine_Api::_()->getDbtable('photos', 'sitegroup')->getAdapter();
        $db->beginTransaction();
        try {
            //CREATE PHOTO
            $tablePhoto = Engine_Api::_()->getDbtable('photos', 'sitegroup');
            $photo = $tablePhoto->createRow();
            $photo->setFromArray(array(
                'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
                'group_id' => $group_id
            ));
            $photo->save();
            $photo->setPhoto($_FILES[$fileName]);

            $this->view->status = true;
            $this->view->name = $_FILES[$fileName]['name'];
            $this->view->photo_id = $photo->photo_id;
            $this->view->photo_url = $photo->getPhotoUrl();

            $tableAlbum = Engine_Api::_()->getDbtable('albums', 'sitegroup');
            $album = $tableAlbum->getSpecialAlbum($sitegroup, $special);
            $tablePhotoName = $tablePhoto->info('name');
            $photoSelect = $tablePhoto->select()->from($tablePhotoName, 'order')->where('album_id = ?', $album->album_id)->order('order DESC')->limit(1);
            $photo_rowinfo = $tablePhoto->fetchRow($photoSelect);
            $photo->collection_id = $album->album_id;
            $photo->album_id = $album->album_id;
            $order = 0;
            if (!empty($photo_rowinfo)) {
                $order = $photo_rowinfo->order + 1;
            }
            $photo->order = $order;
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

    //ACTION FOR Twitter CODE
    public function twitterAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;

        //SMOOTHBOX
        if (null === $this->_helper->ajaxContext->getCurrentContext()) {
            $this->_helper->layout->setLayout('default-simple');
        } else {
            //NO LAYOUT
            $this->_helper->layout->disableLayout(true);
        }

        //GET GROUP ID AND SITEGROUP OBJECT
        $siteagroup = Engine_Api::_()->getItem('sitegroup_group', $this->_getParam('group_id'));

        //GENERATE FORM
        $this->view->form = $form = new Sitegroup_Form_Twitter();

        //POPULATE THE FORM
        $form->populate($siteagroup->toArray());

        if (!$this->getRequest()->isPost())
            return;

        //SAVE THE Twitter CODE IN DATABASE
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $db = Engine_Api::_()->getDbtable('groups', 'sitegroup')->getAdapter();
            $db->beginTransaction();
            try {
                $siteagroup->twitter_user_name = $_POST['twitter_user_name'];
                $siteagroup->save();
                $db->commit();
            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
        }

        $this->_forward('success', 'utility', 'core', array(
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your account has been added successfully! You can view your recent tweets on profile of your group.')),
            'parentRefresh' => false,
            'format' => 'smoothbox',
            'smoothboxClose' => 1500,
        ));
    }

    //ACTION FOR Twitter CODE
    public function facebookAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;

        //SMOOTHBOX
        if (null === $this->_helper->ajaxContext->getCurrentContext()) {
            $this->_helper->layout->setLayout('default-simple');
        } else {
            //NO LAYOUT
            $this->_helper->layout->disableLayout(true);
        }

        //GET GROUP ID AND SITEGROUP OBJECT
        $siteagroup = Engine_Api::_()->getItem('sitegroup_group', $this->_getParam('group_id'));

        //GENERATE FORM
        $this->view->form = $form = new Sitegroup_Form_Facebook();

        //POPULATE THE FORM
        $form->populate($siteagroup->toArray());

        if (!$this->getRequest()->isPost())
            return;

        //SAVE THE Twitter CODE IN DATABASE
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $db = Engine_Api::_()->getDbtable('groups', 'sitegroup')->getAdapter();
            $db->beginTransaction();
            try {
                $siteagroup->fbpage_url = $_POST['fbpage_url'];
                $siteagroup->save();
                $db->commit();
            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
        }

        $this->_forward('success', 'utility', 'core', array(
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your Facebook Page has been successfully linked.')),
            'parentRefresh' => false,
            'format' => 'smoothbox',
            'smoothboxClose' => 1500,
        ));
    }

    public function manageMemberCategoryAction() {

        //CHECK PERMISSION FOR VIEW.
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET NAVIGATION.
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitegroup_main');

        $this->view->sitegroups_view_menu = 40;

        //GETTING THE OBJECT AND GROUP ID.
        $this->view->group_id = $group_id = $this->_getParam('group_id', null);
        $this->view->sitegroup = $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
        $this->view->is_ajax = $this->_getParam('is_ajax', '');

        //EDIT PRIVACY
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');

        $manageAdminAllowed = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.manageadmin', 1);
        $manageMemberSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupmember.category.settings', 1);

        if (empty($isManageAdmin) || empty($manageAdminAllowed)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if ($manageMemberSettings == 3) {
            $is_admincreated = array("0" => 0, "1" => 1);
            $group_id = array("0" => 0, "1" => $group_id);
        } elseif ($manageMemberSettings == 2) {
            $is_admincreated = array("0" => 0);
            $group_id = array("1" => $group_id);
        }

        $rolesTable = Engine_Api::_()->getDbtable('roles', 'sitegroupmember');
        $rolesTableName = $rolesTable->info('name');

        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            $row = $rolesTable->createRow();
            $row->is_admincreated = 0;
            $row->role_name = $values['category_name'];
            $row->group_category_id = $sitegroup->category_id;
            $row->group_id = $sitegroup->group_id;
            $row->save();
        }

        $select = $rolesTable->select()
                ->from($rolesTableName)
                ->where($rolesTableName . '.is_admincreated IN (?)', (array) $is_admincreated)
                ->where($rolesTableName . '.group_id IN (?)', (array) $group_id)
                ->where($rolesTableName . '.group_category_id = ? ', $sitegroup->category_id)
                ->order('role_id DESC');
        $this->view->manageRolesHistories = $rolesTable->fetchALL($select);
    }

    public function editRoleAction() {

        $role_id = (int) $this->_getParam('role_id');
        $group_id = (int) $this->_getParam('group_id');

        $role = Engine_Api::_()->getItem('sitegroupmember_roles', $role_id);

        $this->view->form = $form = new Sitegroup_Form_EditRole();
        $form->populate($role->toArray());

        $rolesTable = Engine_Api::_()->getDbtable('roles', 'sitegroupmember');

        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        // Process
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $values = $form->getValues();
            $role->setFromArray($values);
            $role->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 500,
            'parentRedirect' => $this->_helper->url->url(array('group_url' => Engine_Api::_()->sitegroup()->getGroupUrl($group_id), 'action' => 'manage-member-category', 'group_id' => $group_id), 'sitegroup_dashboard', true),
            'parentRedirectTime' => '2',
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Roles has been edited successfully.'))
        ));
    }

    //THIS ACTION FOR DELETE MANAGE ADMIN AND CALLING FROM THE CORE.JS FILE.
    public function deleteMemberCategoryAction() {

        $role_id = (int) $this->_getParam('category_id');
        $group_id = (int) $this->_getParam('group_id');
        $rolesTable = Engine_Api::_()->getDbtable('roles', 'sitegroupmember');
        $rolesTable->delete(array('role_id = ?' => $role_id, 'group_id = ?' => $group_id));
    }

    public function resetPositionCoverPhotoAction() {
        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;
        //GET GROUP ID
        $group_id = $this->_getParam("group_id");
        $this->view->sitegroup = $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
        ////START MANAGE-ADMIN CHECK
        $this->view->can_edit = $can_edit = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
        if (empty($can_edit))
            return;
        $tableAlbum = Engine_Api::_()->getDbtable('albums', 'sitegroup');
        $album = $tableAlbum->getSpecialAlbum($sitegroup, 'cover');
        $album->cover_params = $this->_getParam('position', array('top' => '0', 'left' => 0));
        $album->save();
    }

    public function getAlbumsPhotosAction() {
        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;
        $sitegroupalbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum');
        if (!$sitegroupalbumEnabled) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //GET GROUP ID
        $group_id = $this->_getParam("group_id");
        $this->view->sitegroup = $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
        ////START MANAGE-ADMIN CHECK
        $this->view->can_edit = $can_edit = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
        if (empty($can_edit))
            return;
        //FETCH ALBUMS
        $this->view->recentAdded = $recentAdded = $this->_getParam("recent", false);
        $this->view->album_id = $album_id = $this->_getParam("album_id");
        if ($album_id) {
            $this->view->album = $album = Engine_Api::_()->getItem('sitegroup_album', $album_id);
            $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
            $paginator->setItemCountPerPage(10000);
        } elseif ($recentAdded) {
            $paginator = Engine_Api::_()->getDbtable('photos', 'sitegroup')->getPhotos(array('group_id' => $group_id, 'orderby' => 'photo_id DESC', 'start' => 0, 'end' => 100));
        } else {
            $paramsAlbum['group_id'] = $group_id;
            $paginator = Engine_Api::_()->getDbtable('albums', 'sitegroup')->getAlbums($paramsAlbum);
        }
        $this->view->paginator = $paginator;
    }

    public function uploadCoverPhotoAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //LAYOUT
        $this->_helper->layout->setLayout('default-simple');
        if (!$this->_helper->requireUser()->checkRequire()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
            return;
        }

        //GROUP ID
        $group_id = $this->_getParam('group_id');

        $special = $this->_getParam('special', 'cover');
        $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //GET FORM
        $this->view->form = $form = new Sitegroup_Form_Photo_Cover();

        //CHECK FORM VALIDATION
        $file = '';
        $notNeedToCreate = false;
        $photo_id = $this->_getParam('photo_id');
        if ($photo_id) {
            $photo = Engine_Api::_()->getItem('sitegroup_photo', $photo_id);
            $album = Engine_Api::_()->getItem('sitegroup_album', $photo->album_id);
            if ($album && $album->type == 'cover') {
                $notNeedToCreate = true;
            }
            if ($photo->file_id && !$notNeedToCreate)
                $file = Engine_Api::_()->getItemTable('storage_file')->getFile($photo->file_id);
        }

        if (empty($photo_id) || empty($photo)) {
            if (!$this->getRequest()->isPost()) {
                return;
            }

            //CHECK FORM VALIDATION
            if (!$form->isValid($this->getRequest()->getPost())) {
                return;
            }
        }
        //UPLOAD PHOTO
        if ($form->Filedata->getValue() !== null || $photo || ($notNeedToCreate && $file)) {
            //PROCESS
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                //CREATE PHOTO
                $tablePhoto = Engine_Api::_()->getDbtable('photos', 'sitegroup');
                if (!$notNeedToCreate) {
                    $photo = $tablePhoto->createRow();
                    $photo->setFromArray(array(
                        'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
                        'group_id' => $group_id
                    ));
                    $photo->save();
                    if ($file) {
                        $photo->setPhoto($file);
                    } else {
                        $photo->setPhoto($form->Filedata, true);
                    }


                    $tableAlbum = Engine_Api::_()->getDbtable('albums', 'sitegroup');
                    $album = $tableAlbum->getSpecialAlbum($sitegroup, $special);

                    $tablePhotoName = $tablePhoto->info('name');
                    $photoSelect = $tablePhoto->select()->from($tablePhotoName, 'order')->where('album_id = ?', $album->album_id)->order('order DESC')->limit(1);
                    $photo_rowinfo = $tablePhoto->fetchRow($photoSelect);
                    $photo->collection_id = $album->album_id;
                    $photo->album_id = $album->album_id;
                    $order = 0;
                    if (!empty($photo_rowinfo)) {
                        $order = $photo_rowinfo->order + 1;
                    }
                    $photo->order = $order;
                    $photo->save();
                }

                $album->cover_params = $this->_getParam('position', array('top' => '0', 'left' => 0));
                $album->save();
                if (!$album->photo_id) {
                    $album->photo_id = $photo->file_id;
                    $album->save();
                }
                $sitegroup->group_cover = $photo->photo_id;
                $sitegroup->save();
                //ADD ACTIVITY
                $viewer = Engine_Api::_()->user()->getViewer();
                $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
                $activityFeedType = null;
                if (Engine_Api::_()->sitegroup()->isGroupOwner($sitegroup) && Engine_Api::_()->sitegroup()->isFeedTypeGroupEnable())
                    $activityFeedType = 'sitegroup_admin_cover_update';
                elseif ($sitegroup->all_post || Engine_Api::_()->sitegroup()->isGroupOwner($sitegroup))
                    $activityFeedType = 'sitegroup_cover_update';


                if ($activityFeedType) {
                    $action = $activityApi->addActivity($viewer, $sitegroup, $activityFeedType);
                }
                if ($action) {
                    Engine_Api::_()->getApi('subCore', 'sitegroup')->deleteFeedStream($action);
                    if ($photo)
                        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $photo);
                }

                $this->view->status = true;
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                $this->view->status = false;
                $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
                return;
            }
        }
    }

    public function removeCoverPhotoAction() {
        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        $group_id = $this->_getParam('group_id');
        if ($this->getRequest()->isPost()) {
            $special = $this->_getParam('special', 'cover');
            $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
            $sitegroup->group_cover = 0;
            $sitegroup->save();
            $tableAlbum = Engine_Api::_()->getDbtable('albums', 'sitegroup');
            $album = $tableAlbum->getSpecialAlbum($sitegroup, $special);
            $album->cover_params = array('top' => '0', 'left' => 0);
            $album->save();

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
            ));
        }
    }

}
