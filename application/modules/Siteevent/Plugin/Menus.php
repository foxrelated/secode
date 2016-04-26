<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Plugin_Menus {

    public function showSiteeventSuggestToFriendLink($row) {
        $params = $row->params;
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $subject = Engine_Api::_()->core()->getSubject();
        if (!empty($viewer_id) && !empty($subject)) {
            return array(
                'class' => $params['class'],
                'route' => $params['route'],
                'action' => 'edit',
                'params' => array(
                    'product_id' => $subject->getIdentity(),
                    'modName' => 'siteevent',
                    'modContentId' => $subject->getIdentity(),
                    'modError' => 1
                ),
            );
        }
    }

    public function canCreateSiteevents($row) {

        //MUST BE LOGGED IN USER
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$viewer || !$viewer->getIdentity()) {
            $creation_link = Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, "creation_link");

            if(!$creation_link) return false; 
            
        } else {
            //MUST BE ABLE TO CRETE EVENTS
            if (!Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, "create")) {
                return false;
            }
        }

        //MUST BE ABLE TO VIEW EVENTS
        if (!Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, "view")) {
            return false;
        }

        return true;
    }

    public function canViewSiteevents($row) {

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //MUST BE ABLE TO VIEW EVENTS
        if (!Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, "view")) {
            return false;
        }

        return true;
    }

    public function canViewBrosweReview($row) {

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //MUST BE ABLE TO VIEW DIARIES
        if (!Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, "view")) {
            return false;
        }
        $request = Zend_Controller_Front::getInstance()->getRequest();

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) || !Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.allowreview', 1))
            return false;

        $route['route'] = 'siteevent_review_browse';
        if ('siteevent' == $request->getModuleName() &&
                'review' == $request->getControllerName() &&
                'browse' == $request->getActionName()) {
            $route['active'] = true;
        }

        return $route;

        return true;
    }

    public function canViewCategories($row) {     
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //MUST BE ABLE TO VIEW CATEGORIES
        if (!Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, "view")) {
            return false;
        }
        
        if(Engine_Api::_()->seaocore()->isSitemobileApp()){
          return false;
        }
        $request = Zend_Controller_Front::getInstance()->getRequest();

        $route['route'] = 'siteevent_review_categories';
        $route['action'] = 'categories';
        if ('siteevent' == $request->getModuleName() &&
                'index' == $request->getControllerName() &&
                'categories' == $request->getActionName()) {
            $route['active'] = true;
        } else {
            $route['active'] = false;
        }
        return $route;
    }

    public function canViewDiary($row) {

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //MUST BE ABLE TO VIEW DIARIES
        if (!Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, "view")) {
            return false;
        }

        //MUST BE ABLE TO VIEW DIARIES
        if (!Engine_Api::_()->authorization()->isAllowed('siteevent_diary', $viewer, "view")) {
            return false;
        }

        return true;
    }

    public function canViewEditors($row) {

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //MUST BE ABLE TO VIEW EVENTS
        if (!Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, "view")) {
            return false;
        }

        $editorsCount = Engine_Api::_()->getDbTable('editors', 'siteevent')->getEditorsCount(0);

        if ($editorsCount <= 0) {
            return false;
        }

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 2 || !Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2)) {
            return false;
        }

        return true;
    }

    public function userProfileDiary() {

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //MUST BE ABLE TO VIEW DIARIES
        if (!Engine_Api::_()->authorization()->isAllowed('siteevent_diary', $viewer, "view")) {
            return false;
        }

        //RETURN FALSE IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('user')) {
            return false;
        }

        $user = Engine_Api::_()->core()->getSubject('user');

        $diary_id = Engine_Api::_()->getDbtable('diaries', 'siteevent')->getRecentDiaryId($user->user_id);

        if (!empty($diary_id)) {
            return array(
                'class' => 'buttonlink',
                'route' => 'siteevent_diary_general',
                'icon' => Zend_Registry::get('Zend_View')->layout()->staticBaseUrl.'application/modules/Siteevent/externals/images/icons/diary.png',
                'params' => array(
                    'member' => $user->getTitle(),
                ),
            );
        } else {
            return false;
        }
    }

    public function siteeventGutterEdit($row) {

        //RETURN FALSE IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return false;
        }

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET EVENT SUBJECT
        $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        //AUTHORIZATION CHECK
        if (!$siteevent->authorization()->isAllowed($viewer, "edit")) {
            return false;
        }

        return array(
            'class' => 'buttonlink icon_siteevent_dashboard',
            'route' => "siteevent_specific",
            'action' => 'edit',
            'params' => array(
                'event_id' => $siteevent->getIdentity(),
            ),
        );
    }

    public function siteeventGutterEditoverview($row) {

        //RETURN FALSE IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return false;
        }

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET EVENT SUBJECT
        $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.overview', 1)) {
            return false;
        }

        if (!$siteevent->authorization()->isAllowed($viewer, 'edit')) {
            return false;
        }       

        //PACKAGE BASED CHECKS
        if (Engine_Api::_()->siteevent()->hasPackageEnable() && !Engine_Api::_()->siteeventpaid()->allowPackageContent($siteevent->package_id, "overview")) {
                return false;
        } elseif (!$siteevent->authorization()->isAllowed($viewer, 'overview')){
              return false;
        }
        return array(
            'class' => 'buttonlink siteevent_gutter_editoverview',
            'route' => "siteevent_specific",
            'action' => 'overview',
            'params' => array(
                'event_id' => $siteevent->getIdentity(),
            ),
        );
    }

    public function siteeventGutterEditstyle($row) {

        //RETURN FALSE IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return false;
        }

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET EVENT SUBJECT
        $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');




        if (!$siteevent->authorization()->isAllowed($viewer, 'edit')) {
            return false;
        }

        if (!$siteevent->authorization()->isAllowed($viewer, 'style')) {
            return false;
        }

        return array(
            'class' => 'buttonlink siteevent_gutter_editstyle',
            'route' => "siteevent_specific",
            'action' => 'editstyle',
            'params' => array(
                'event_id' => $siteevent->getIdentity(),
            ),
        );
    }

    public function siteeventGutterShare() {

        //RETURN FALSE IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return false;
        }

        //GET SUBJECT
        $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        //GET VIEWER ID
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        //RETURN IF VIEWER IS EMPTY
        if (empty($viewer_id)) {
            return false;
        }

        return array(
            'class' => 'smoothbox seao_icon_sharelink',
            'route' => 'default',
            'params' => array(
                'module' => 'activity',
                'controller' => 'index',
                'action' => 'share',
                'type' => $siteevent->getType(),
                'id' => $siteevent->getIdentity(),
                'format' => 'smoothbox',
            ),
        );
    }

    public function siteeventGutterMessageowner($row) {

        //RETURN FALSE IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return false;
        }

        //GET VIEWER INFO
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //RETURN IF NOT AUTHORIZED
        if (empty($viewer_id)) {
            return false;
        }

        //GET SUBJECT
        $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        //SHOW MESSAGE OWNER LINK TO USER IF MESSAGING IS ENABLED FOR THIS LEVEL
        $showMessageOwner = 0;
        $showMessageOwner = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'auth');
        if ($showMessageOwner != 'none') {
            $showMessageOwner = 1;
        }

        //RETURN IF NOT AUTHORIZED
        if ($siteevent->owner_id == $viewer_id || empty($viewer_id) || empty($showMessageOwner)) {
            return false;
        }

        return array(
            'class' => 'smoothbox icon_siteevents_messageowner buttonlink',
            'route' => "siteevent_specific",
            'action' => 'messageowner',
            'params' => array(
                'event_id' => $siteevent->getIdentity(),
            ),
        );
    }

    public function siteeventGutterNotifyGuest($row) {

        //RETURN FALSE IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return false;
        }

        //GET VIEWER INFO
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //RETURN IF NOT AUTHORIZED
        if (empty($viewer_id)) {
            return false;
        }

        //GET SUBJECT
        $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');


        //RETURN IF NOT AUTHORIZED
        if ($siteevent->owner_id != $viewer_id || $viewer->level_id != 1) {
            return false;
        }

        return array(
            'class' => 'smoothbox icon_siteevents_messageowner buttonlink',
            'route' => "siteevent_specific",
            'controller' => 'member',
            'action' => 'notify-guest',
            'params' => array(
                'event_id' => $siteevent->getIdentity(),
            ),
        );
    }

    public function siteeventGutterTfriend($row) {

        //RETURN FALSE IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return false;
        }

        //GET SUBJECT
        $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        return array(
            'class' => 'smoothbox buttonlink icon_siteevents_tellafriend',
            'route' => "siteevent_specific",
            'action' => 'tellafriend',
            'params' => array(
                'event_id' => $siteevent->getIdentity(),
            ),
        );
    }

    public function siteeventGutterPrint($row) {

        //RETURN FALSE IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return false;
        }

        //GET SUBJECT
        $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        $occurrence_id = Zend_Registry::isRegistered('occurrence_id') ? Zend_Registry::get('occurrence_id') : null;

        return array(
            'class' => 'buttonlink icon_siteevents_printer',
            'route' => "siteevent_specific",
            'action' => 'print',
            'target' => '_blank',
            'params' => array(
                'event_id' => $siteevent->getIdentity(),
                'occurrence_id' => $occurrence_id,
            ),
        );
    }

    public function siteeventGutterPublish($row) {

        //RETURN FALSE IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return false;
        }

        //GET SUBJECT
        $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        //GET VIEWER ID
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        //RETURN IF NOT AUTHORIZED
        if ($siteevent->draft != 1 || ($viewer_id != $siteevent->owner_id)) {
            return false;
        }

        return array(
            'class' => 'buttonlink smoothbox icon_siteevent_publish',
            'route' => "siteevent_specific",
            'action' => 'publish',
            'params' => array(
                'event_id' => $siteevent->getIdentity()
            ),
        );
    }

    public function siteeventGutterReview() {

        //RETURN FALSE IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return false;
        }

        //NON LOGGED IN USER CAN'T BE THE EDITOR
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        if (empty($viewer_id)) {
            return false;
        }

        //GET SUBJECT
        $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) || Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 2) {
            return false;
        }

        //SHOW THIS LINK ONLY EDITOR
        $isEditor = Engine_Api::_()->getDbTable('editors', 'siteevent')->isEditor($viewer_id);
        if (empty($isEditor)) {
            return false;
        }

        //EDITOR REVIEW HAS BEEN POSTED OR NOT
        $params = array();
        $params['resource_id'] = $siteevent->event_id;
        $params['resource_type'] = $siteevent->getType();
        $params['type'] = 'editor';
        $params['notIncludeStatusCheck'] = 1;
        $isEditorReviewed = Engine_Api::_()->getDbTable('reviews', 'siteevent')->canPostReview($params);

        $params = array();
        $params['event_id'] = $siteevent->getIdentity();
        if (!empty($isEditorReviewed)) {

            $editorreview = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.editorreview', 0);
            $review = Engine_Api::_()->getItem('siteevent_review', $isEditorReviewed);
            if (empty($editorreview) && $viewer_id != $review->owner_id) {
                return false;
            }

            $label = Zend_Registry::get('Zend_Translate')->_('Edit an Editor Review');
            $action = 'edit';
            $params['review_id'] = $isEditorReviewed;
        } else {
            $label = Zend_Registry::get('Zend_Translate')->_('Write an Editor Review');
            $action = 'create';
        }

        return array(
            'label' => $label,
            'class' => 'buttonlink icon_siteevents_review',
            'route' => "siteevent_extended",
            'controller' => 'editor',
            'action' => $action,
            'params' => $params,
        );
    }

    public function siteeventGutterClose() {

        //RETURN FALSE IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return false;
        }

        //GET SUBJECT
        $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        //GET VIEWER ID
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        //RETURN IF NOT AUTHORIZED
        if ($viewer_id != $siteevent->owner_id || !empty($siteevent->draft)) {
            return false;
        }

        if (!empty($siteevent->closed)) {
            $label = Zend_Registry::get('Zend_Translate')->_('Re-publish Event');
            $class = 'buttonlink smoothbox icon_siteevent_publish';
        } else {
            $label = Zend_Registry::get('Zend_Translate')->_('Cancel Event');
            $class = 'buttonlink smoothbox icon_siteevent_cancel';
        }

        return array(
            'label' => $label,
            'class' => $class,
            'route' => 'siteevent_specific',
            'params' => array(
                'action' => 'close',
                'event_id' => $siteevent->getIdentity()
            ),
        );
    }

    public function siteeventGutterDelete($row) {

        //RETURN FALSE IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return false;
        }

        //GET VIEWER ID
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        //GET SUBJECT
        $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        //EVENT DELETE PRIVACY
        $can_delete = $siteevent->authorization()->isAllowed(null, "delete");

        //AUTHORIZATION CHECK
        if (empty($can_delete) || empty($viewer_id)) {
            return false;
        }

        return array(
            'class' => 'buttonlink seaocore_icon_delete',
            'route' => 'siteevent_specific',
            'params' => array(
                'action' => 'delete',
                'event_id' => $siteevent->getIdentity()
            ),
        );
    }

    public function siteeventGutterReport() {

        //RETURN FALSE IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return false;
        }

        //GET VIEWER ID
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        if (empty($viewer_id)) {
            return false;
        }

        //GET SUBJECT
        $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        return array(
            'class' => 'smoothbox buttonlink icon_siteevents_report',
            'route' => 'default',
            'params' => array(
                'module' => 'core',
                'controller' => 'report',
                'action' => 'create',
                'route' => 'default',
                'subject' => $siteevent->getGuid()
            ),
        );
    }

    public function siteeventGutterDiary($row) {
        //CHECK EDITOR REVIEW IS ALLOWED OR NOT
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.diary', 1)) {
            return false;
        }
        //RETURN FALSE IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return false;
        }

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET EVENT SUBJECT
        $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        //AUTHORIZATION CHECK
        if (!empty($siteevent->draft) || empty($siteevent->search) || empty($siteevent->approved)) {
            return false;
        }

        //GET VIEWER ID
        $viewer_id = $viewer->getIdentity();

        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        //GET LEVEL SETTING
        $can_create = Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_diary', "create");

        if (empty($can_create)) {
            return false;
        }

        //AUTHORIZATION CHECK
        if (!Engine_Api::_()->authorization()->isAllowed('siteevent_diary', $viewer, 'view')) {
            return false;
        }

        return array(
            'class' => 'buttonlink smoothbox siteevent_icon_diary_add',
            'route' => "siteevent_diary_general",
            'action' => 'add',
            'params' => array(
                'event_id' => $siteevent->getIdentity(),
            ),
        );
    }

    public function siteeventGutterChangephoto($row) {

        //RETURN FALSE IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return false;
        }

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET EVENT SUBJECT
        $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');




        //AUTHORIZATION CHECK
        if (!$siteevent->authorization()->isAllowed($viewer, "edit")) {
            return false;
        }

        return array(
            'class' => 'buttonlink icon_siteevent_edit',
            'route' => "siteevent_specific",
            'action' => 'change-photo',
            'params' => array(
                'event_id' => $siteevent->getIdentity(),
            ),
        );
    }

    // Diary Profile page Gutter 
    public function onMenuInitialize_siteeventDiaryGutterEdit($row) {
        //RETURN FALSE IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_diary')) {
            return false;
        }
        //GET VIEWER ID
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        //RETURN IF VIEWER IS EMPTY
        if (empty($viewer_id)) {
            return false;
        }

        //GET EVENT SUBJECT
        $subject = Engine_Api::_()->core()->getSubject('siteevent_diary');

        if ($viewer_id != $subject->owner_id)
            return false;

        return array(
            'class' => 'buttonlink smoothbox seaocore_icon_edit',
            'route' => "siteevent_diary_general",
            'action' => 'edit',
            'params' => array(
                'diary_id' => $subject->getIdentity(),
            ),
        );
    }

    // Diary Profile page Gutter 
    public function onMenuInitialize_siteeventDiaryGutterDelete($row) {

        //RETURN FALSE IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_diary')) {
            return false;
        }
        //GET VIEWER ID
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        //RETURN IF VIEWER IS EMPTY
        if (empty($viewer_id)) {
            return false;
        }

        //GET EVENT SUBJECT
        $subject = Engine_Api::_()->core()->getSubject('siteevent_diary');

        if ($viewer_id != $subject->owner_id)
            return false;


        return array(
            'class' => 'buttonlink smoothbox seaocore_icon_delete',
            'route' => "siteevent_diary_general",
            'action' => 'delete',
            'params' => array(
                'diary_id' => $subject->getIdentity(),
            ),
        );
    }

    public function onMenuInitialize_siteeventDiaryGutterShare() {

        //RETURN FALSE IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_diary')) {
            return false;
        }
        //GET VIEWER ID
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        //RETURN IF VIEWER IS EMPTY
        if (empty($viewer_id)) {
            return false;
        }
        //GET SUBJECT
        $subject = Engine_Api::_()->core()->getSubject('siteevent_diary');
        return array(
            'class' => 'smoothbox seaocore_icon_share buttonlink',
            'route' => 'default',
            'params' => array(
                'module' => 'activity',
                'controller' => 'index',
                'action' => 'share',
                'type' => $subject->getType(),
                'id' => $subject->getIdentity(),
                'format' => 'smoothbox',
            ),
        );
    }

    public function onMenuInitialize_siteeventDiaryGutterReport() {

        //RETURN FALSE IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_diary')) {
            return false;
        }
        //GET VIEWER ID
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        //RETURN IF VIEWER IS EMPTY
        if (empty($viewer_id)) {
            return false;
        }

        //GET SUBJECT
        $subject = Engine_Api::_()->core()->getSubject('siteevent_diary');

        return array(
            'class' => 'smoothbox buttonlink icon_siteevents_report',
            'route' => 'default',
            'params' => array(
                'module' => 'core',
                'controller' => 'report',
                'action' => 'create',
                'route' => 'default',
                'subject' => $subject->getGuid()
            ),
        );
    }

    public function onMenuInitialize_siteeventDiaryGutterTfriend($row) {
        //GET VIEWER ID
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        if (empty($viewer_id)) {
            return false;
        }

        //RETURN FALSE IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_diary')) {
            return false;
        }

        //GET SUBJECT
        $subject = Engine_Api::_()->core()->getSubject('siteevent_diary');

        return array(
            'class' => 'smoothbox buttonlink icon_siteevents_tellafriend',
            'route' => "siteevent_diary_general",
            'params' => array(
                'action' => 'tell-a-friend',
                'type' => $subject->getType(),
                'diary_id' => $subject->getIdentity(),
            ),
        );
    }

    public function onMenuInitialize_siteeventDiaryGutterCreate($row) {
        //GET VIEWER ID
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        //RETURN IF VIEWER IS EMPTY
        if (empty($viewer_id)) {
            return false;
        }

        return array(
            'class' => 'buttonlink smoothbox siteevent_icon_diary_add',
            'route' => "siteevent_diary_general",
            'action' => 'create',
        );
    }

    public function onMenuInitialize_SiteeventTopicWatch() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $siteevent = $subject->getParent();


        $isWatching = null;
        $canPost = $siteevent->authorization()->isAllowed($viewer, "topic");
        if (!$canPost && !$viewer->getIdentity())
            return false;

        $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'siteevent');
        $isWatching = $topicWatchesTable
                ->select()
                ->from($topicWatchesTable->info('name'), 'watch')
                ->where('resource_id = ?', $siteevent->getIdentity())
                ->where('topic_id = ?', $subject->getIdentity())
                ->where('user_id = ?', $viewer->getIdentity())
                ->limit(1)
                ->query()
                ->fetchColumn(0)
        ;

        if (false === $isWatching) {
            $isWatching = null;
        } else {
            $isWatching = (bool) $isWatching;
        }

        if (!$isWatching) {
            return array(
                'label' => 'Watch Topic',
                'route' => 'default',
                'class' => 'smoothbox ui-btn-default ui-btn-action',
                'params' => array(
                    'module' => 'siteevent',
                    'controller' => 'topic',
                    'action' => 'watch',
                    'watch' => 1,
                    'topic_id' => $subject->getIdentity(),
                )
            );
        } else {
            return array(
                'label' => 'Stop Watching Topic',
                'route' => 'default',
                'class' => 'smoothbox ui-btn-default ui-btn-action',
                'params' => array(
                    'module' => 'siteevent',
                    'controller' => 'topic',
                    'action' => 'watch',
                    'watch' => 0,
                    'topic_id' => $subject->getIdentity(),
                )
            );
        }
    }

    public function onMenuInitialize_SiteeventTopicRename() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $siteevent = $subject->getParent();

        $canEdit = $siteevent->authorization()->isAllowed($viewer, "edit");
        if (!$canEdit && !$viewer->getIdentity())
            return false;

        return array(
            'label' => 'Rename',
            'route' => 'default',
            'class' => 'smoothbox ui-btn-default ui-btn-action',
            'params' => array(
                'module' => 'siteevent',
                'controller' => 'topic',
                'action' => 'rename',
                'topic_id' => $subject->getIdentity(),
            )
        );
    }

    public function onMenuInitialize_SiteeventTopicDelete() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $siteevent = $subject->getParent();

        $canEdit = $siteevent->authorization()->isAllowed($viewer, "edit");

        if (!$canEdit && !$viewer->getIdentity())
            return false;

        return array(
            'label' => 'Delete Topic',
            'route' => 'default',
            'class' => 'smoothbox ui-btn-default ui-btn-danger',
            'params' => array(
                'module' => 'siteevent',
                'controller' => 'topic',
                'action' => 'delete',
                'topic_id' => $subject->getIdentity(),
                'content_id' => Zend_Controller_Front::getInstance()->getRequest()->getParam('content_id')
            )
        );
    }

    public function onMenuInitialize_SiteeventTopicOpen() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $siteevent = $subject->getParent();

        $canEdit = $siteevent->authorization()->isAllowed($viewer, "edit");

        if (!$canEdit && !$viewer->getIdentity())
            return false;

        if (!$subject->closed) {
            return array(
                'label' => 'Close',
                'route' => 'default',
                'class' => 'smoothbox ui-btn-default ui-btn-action',
                'params' => array(
                    'module' => 'siteevent',
                    'controller' => 'topic',
                    'action' => 'close',
                    'topic_id' => $subject->getIdentity(),
                    'closed' => 1,
                    'content_id' => Zend_Controller_Front::getInstance()->getRequest()->getParam('content_id')
                )
            );
        } else {
            return array(
                'label' => 'Open',
                'route' => 'default',
                'class' => 'smoothbox ui-btn-default ui-btn-action',
                'params' => array(
                    'module' => 'siteevent',
                    'controller' => 'topic',
                    'action' => 'close',
                    'topic_id' => $subject->getIdentity(),
                    'closed' => 0,
                    'content_id' => Zend_Controller_Front::getInstance()->getRequest()->getParam('content_id')
                )
            );
        }
    }

    public function onMenuInitialize_SiteeventTopicSticky() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $siteevent = $subject->getParent();

        $canEdit = $siteevent->authorization()->isAllowed($viewer, "edit");

        if (!$canEdit && !$viewer->getIdentity())
            return false;

        if (!$subject->sticky) {
            return array(
                'label' => 'Make Sticky',
                'route' => 'default',
                'class' => 'smoothbox ui-btn-default ui-btn-action',
                'params' => array(
                    'module' => 'siteevent',
                    'controller' => 'topic',
                    'action' => 'sticky',
                    'topic_id' => $subject->getIdentity(),
                    'sticky' => 1,
                    'content_id' => Zend_Controller_Front::getInstance()->getRequest()->getParam('content_id')
                )
            );
        } else {
            return array(
                'label' => 'Remove Sticky',
                'route' => 'default',
                'class' => 'smoothbox ui-btn-default ui-btn-action',
                'params' => array(
                    'module' => 'siteevent',
                    'controller' => 'topic',
                    'action' => 'sticky',
                    'topic_id' => $subject->getIdentity(),
                    'sticky' => 0,
                    'content_id' => Zend_Controller_Front::getInstance()->getRequest()->getParam('content_id')
                )
            );
        }
    }

    public function onMenuInitialize_SiteeventPhotoEdit($row) {

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $siteevent = $subject->getParent()->getParent();

        $canEdit = $subject->getCollection()->authorization()->isAllowed(null, "edit");
        if (!$canEdit && !$viewer->getIdentity() && $subject->user_id != $viewer->getIdentity())
            return false;

        return array(
            'label' => 'Edit',
            'route' => "siteevent_photo_extended",
            'class' => 'ui-btn-action smoothbox',
            'params' => array(
                'action' => 'edit',
                'photo_id' => $subject->getIdentity(),
            //'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
            )
        );
    }

    public function onMenuInitialize_SiteeventPhotoDelete($row) {

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $siteevent = $subject->getParent()->getParent();

        $canDelete = $subject->getCollection()->authorization()->isAllowed(null, "delete");
        if (!$canDelete && !$viewer->getIdentity() && $subject->user_id != $viewer->getIdentity())
            return false;

        return array(
            'label' => 'Delete',
            'route' => "siteevent_photo_extended",
            'class' => 'ui-btn-danger smoothbox',
            'params' => array(
                'action' => 'remove',
                'photo_id' => $subject->getIdentity()
            )
        );
    }

    public function onMenuInitialize_SiteeventPhotoShare($row) {

        $subject = Engine_Api::_()->core()->getSubject();
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!SEA_PHOTOLIGHTBOX_SHARE && !$viewer->getIdentity())
            return false;

        return array(
            'label' => 'Share',
            'class' => 'ui-btn-action smoothbox',
            'route' => 'default',
            'params' => array(
                'module' => 'activity',
                'action' => 'share',
                'type' => $subject->getType(),
                'id' => $subject->getIdentity(),
            )
        );
    }

    public function onMenuInitialize_SiteeventPhotoReport($row) {

        $subject = Engine_Api::_()->core()->getSubject();
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!SEA_PHOTOLIGHTBOX_REPORT && !$viewer->getIdentity())
            return false;

        return array(
            'label' => 'Report',
            'class' => 'ui-btn-action smoothbox',
            'route' => 'default',
            'params' => array(
                'module' => 'core',
                'controller' => 'report',
                'action' => 'create',
                'subject' => $subject->getGuid(),
            )
        );
    }

    public function onMenuInitialize_SiteeventPhotoProfile($row) {

        $subject = Engine_Api::_()->core()->getSubject();
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!SEA_PHOTOLIGHTBOX_MAKEPROFILEPHOTO && !$viewer->getIdentity())
            return false;

        return array(
            'label' => 'Make Profile Photo',
            'route' => 'user_extended',
            'class' => 'ui-btn-action smoothbox',
            'params' => array(
                'module' => 'user',
                'controller' => 'edit',
                'action' => 'external-photo',
                'photo' => $subject->getGuid()
            )
        );
    }

    //SITEMOBILE PAGE VIDEO MENUS
    public function onMenuInitialize_SiteeventVideoAdd($row) {
        $viewer = Engine_Api::_()->user()->getViewer();
        
        if(!$viewer->getIdentity()) {
            return false;
        }
        
        $subject = Engine_Api::_()->core()->getSubject();
        $siteevent = $subject->getParent();

        $type_video = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.show.video');
        $paginator = Engine_Api::_()->getDbTable('clasfvideos', 'siteevent')->getEventVideos($siteevent->event_id, 1, $type_video);       
        $totalVideo = $paginator->getTotalItemCount();
        
        $canCreate = Engine_Api::_()->siteevent()->allowVideo($siteevent, $viewer, $totalVideo, $uploadVideo = 1);
        if (!$canCreate)
            return false;

        return array(
            'label' => 'Add Video',
            'route' => "siteevent_video_create",
            'class' => 'ui-btn-action',
            'params' => array(
                'event_id' => $siteevent->event_id,
                'content_id' => Zend_Controller_Front::getInstance()->getRequest()->getParam('content_id')
            )
        );
    }

    public function onMenuInitialize_SiteeventVideoEdit($row) {

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $siteevent = $subject->getParent();


        $can_edit = $siteevent->authorization()->isAllowed($viewer, "edit");
        if (!$can_edit && $viewer->getIdentity() != $subject->owner_id)
            return false;

        return array(
            'label' => 'Edit Video',
            'route' => "siteevent_video_edit",
            'class' => 'ui-btn-action',
            'params' => array(
                'video_id' => $subject->video_id,
                'event_id' => $siteevent->event_id,
                'content_id' => Zend_Controller_Front::getInstance()->getRequest()->getParam('content_id')
            )
        );
    }

    public function onMenuInitialize_SiteeventVideoDelete($row) {

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $siteevent = $subject->getParent();


        $can_edit = $siteevent->authorization()->isAllowed($viewer, "edit");
        if (!$can_edit && $viewer->getIdentity() != $subject->owner_id)
            return false;

        return array(
            'label' => 'Delete Video',
            'route' => "siteevent_video_delete",
            'class' => 'ui-btn-danger',
            'params' => array(
                'video_id' => $subject->video_id,
                'event_id' => $siteevent->event_id,
                'content_id' => Zend_Controller_Front::getInstance()->getRequest()->getParam('content_id')
            )
        );
    }

    //SITEMOBILE PAGE REVIEW MENUS
    public function onMenuInitialize_SiteeventReviewUpdate($row) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $siteevent = $subject->getParent();



        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) || Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 1) {
            return;
        }
        //GET VIEWER   
        $viewer_id = $viewer->getIdentity();
        $create_review = ($siteevent->owner_id == $viewer_id) ? Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.allowownerreview', 1) : 1;
        if (empty($create_review)) {
            return;
        }

        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        //GET REVIEW TABLE
        $reviewTable = Engine_Api::_()->getDbTable('reviews', 'siteevent');
        if ($viewer_id) {
            $params = array();
            $params['resource_id'] = $siteevent->listing_id;
            $params['resource_type'] = $siteevent->getType();
            $params['viewer_id'] = $viewer_id;
            $params['type'] = 'user';
            $hasPosted = $reviewTable->canPostReview($params);
        } else {
            $hasPosted = 0;
        }

        $autorizationApi = Engine_Api::_()->authorization();
        if ($autorizationApi->getPermission($level_id, 'siteevent_event', "review_create") && empty($hasPosted)) {
            $createAllow = 1;
        } elseif ($autorizationApi->getPermission($level_id, 'siteevent_event', "review_update") && !empty($hasPosted)) {
            $createAllow = 2;
        } else {
            $createAllow = 0;
        }

        if ($createAllow != 2)
            return;
        return array(
            'label' => 'Update your Review',
            'action' => 'update',
            'route' => "siteevent_user_general",
            'class' => 'ui-btn-action',
            'params' => array(
                'event_id' => $siteevent->getIdentity(),
                'review_id' => $subject->getIdentity(),
                'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
            )
        );
    }

    //SITEMOBILE PAGE REVIEW MENUS
    public function onMenuInitialize_SiteeventReviewCreate($row) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $siteevent = $subject->getParent();

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) || Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 1) {
            return;
        }

        //GET VIEWER   
        $viewer_id = $viewer->getIdentity();
        $create_review = ($siteevent->owner_id == $viewer_id) ? Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.allowownerreview', 1) : 1;
        if (empty($create_review)) {
            return;
        }
        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }
        //GET REVIEW TABLE
        $reviewTable = Engine_Api::_()->getDbTable('reviews', 'siteevent');
        if ($viewer_id) {
            $level_id = $level_id;
            $params = array();
            $params['resource_id'] = $siteevent->listing_id;
            $params['resource_type'] = $siteevent->getType();
            $params['viewer_id'] = $viewer_id;
            $params['type'] = 'user';
            $hasPosted = $reviewTable->canPostReview($params);
        } else {
            $hasPosted = 0;
            $level_id = $level_id;
        }

        $autorizationApi = Engine_Api::_()->authorization();
        if ($autorizationApi->getPermission($level_id, 'siteevent_event', "review_create") && empty($hasPosted)) {
            $createAllow = 1;
        } elseif ($autorizationApi->getPermission($level_id, 'siteevent_event', "review_update") && !empty($hasPosted)) {
            $createAllow = 2;
        } else {
            $createAllow = 0;
        }
        return array(
            'label' => 'Write a Review',
            'action' => 'create',
            'route' => "siteevent_user_general",
            'class' => 'ui-btn-action',
            'params' => array(
                'event_id' => $siteevent->getIdentity(),
                'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
            )
        );
    }

    public function onMenuInitialize_SiteeventReviewShare($row) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $siteevent = $subject->getParent();

        //GET VIEWER   
        $viewer_id = $viewer->getIdentity();
        $coreApi = Engine_Api::_()->getApi('settings', 'core');
        $siteevent_share = $coreApi->getSetting('siteevent.share', 1);

        if ($siteevent_share && $siteevent->owner_id != 0):
          if(Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')){
            return array(
                'label' => 'Share Review',
                'route' => "siteevent_user_general",
                'action' => 'share',
                'class' => 'ui-btn-action smoothbox',
                'params' => array(
                    'event_id' => $siteevent->getIdentity(),
                    'review_id' => $subject->getIdentity(),
                    'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
                )
            );
          }else{
             return array(
              'label' => 'Share Review',
              'route' => "default",
              'class' => 'ui-btn-action smoothbox',
              'params' => array(
                  'module' => 'activity',
                  'action' => 'share',
                  'type' => $subject->getType(),
                  'id' => $subject->getIdentity(),
              )
          );
          }
      endif;
        return;
    }

    public function onMenuInitialize_SiteeventReviewEmail($row) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $siteevent = $subject->getParent();

        //GET VIEWER   
        $viewer_id = $viewer->getIdentity();
        $coreApi = Engine_Api::_()->getApi('settings', 'core');
        $siteevent_email = $coreApi->getSetting('siteevent.email', 1);

        if ($siteevent_email):
            return array(
                'label' => 'Email Review',
                'route' => "siteevent_user_general",
                'action' => 'email',
                'class' => 'ui-btn-action smoothbox',
                'params' => array(
                    'event_id' => $siteevent->getIdentity(),
                    'review_id' => $subject->getIdentity(),
                    'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
                )
            );
        endif;
        return;
    }

    public function onMenuInitialize_SiteeventReviewDelete($row) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $siteevent = $subject->getParent();

        //GET VIEWER   
        $viewer_id = $viewer->getIdentity();
        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $this->view->level_id = $level_id = $viewer->level_id;
        } else {
            $this->view->level_id = $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }
        $can_delete = Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "review_delete");
        if (!empty($can_delete) && ($can_delete != 1 || $viewer_id == $siteevent->owner_id)) :
            return array(
                'label' => 'Delete Review',
                'route' => "siteevent_user_general",
                'action' => 'delete',
                'class' => 'ui-btn-danger smoothbox',
                'params' => array(
                    'event_id' => $siteevent->getIdentity(),
                    'review_id' => $subject->getIdentity(),
                    'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
                )
            );
        endif;
        return;
    }

//
    public function onMenuInitialize_SiteeventReviewReport($row) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $siteevent = $subject->getParent();

        //GET VIEWER   
        $viewer_id = $viewer->getIdentity();
        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $this->view->level_id = $level_id = $viewer->level_id;
        } else {
            $this->view->level_id = $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }
        $coreApi = Engine_Api::_()->getApi('settings', 'core');
        $siteevent_report = $coreApi->getSetting('siteevent.report', 1);
        if ($siteevent_report && $viewer_id):
            return array(
                'label' => 'Report',
                'route' => 'default',
                'class' => 'ui-btn-action smoothbox',
                'params' => array(
                    'module' => 'core',
                    'controller' => 'report',
                    'action' => 'create',
                    'subject' => $subject->getGuid(),
                // 'format' => 'smoothbox'
                )
            );
        endif;
        return;
    }

    public function SiteeventGutterInvite() {
       //CHECK IF SITEEVENT INVITE EXTENSION IS ENABLED THEN WE WILL NOT SHOW THIS LINK.
       //THIS CONDITION WORKS ONLY FOR MOBILE SITE
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.invite.other.guests', 1) && !Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.invite.other.automatically', 1) && Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode'))
            return;

        $siteeventinvite = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventinvite');
        if ($siteeventinvite && Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode'))
            return;

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        if ($subject->getType() !== 'siteevent_event') {
            throw new Event_Model_Exception('This event does not exist.');
        }
        if (!$subject->authorization()->isAllowed($viewer, 'invite')) {
            return false;
        }
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $occure_id = Zend_Registry::isRegistered('occurrence_id') ? Zend_Registry::get('occurrence_id') : null;
        //CHECK IF THE EVENT IS PAST EVENT THEN ALSO DO NOT SHOW THE INVITE AND PROMOTE LINK
        $endDate = $view->locale()->toEventDateTime(Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($subject->getIdentity(), 'DESC', $occure_id));
        $currentDate = $view->locale()->toEventDateTime(time());
        if (strtotime($endDate) < strtotime($currentDate))
            return false;
        return array(
            'label' => 'Invite Guests',
            'class' => 'buttonlink smoothbox icon_siteevents_inviteguests',
            'route' => 'siteevent_extended',
            'params' => array(
                //'module' => 'event',
                'controller' => 'member',
                'action' => 'invite',
                'event_id' => $subject->getIdentity(),
                'occurrence_id' => $occure_id,
                'format' => 'smoothbox',
            ),
        );
    }

    public function SiteeventGutterMember() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        if ($subject->getType() !== 'siteevent_event') {
            throw new Event_Model_Exception('Whoops, not an event!');
        }

        if (!$viewer->getIdentity()) {
            return false;
        }

        $row = $subject->membership()->getRow($viewer);
        $occure_id = Zend_Registry::isRegistered('occurrence_id') ? Zend_Registry::get('occurrence_id') : null;
        //CHECK IF THE EVENT IS PAST EVENT THEN WE WILL NOT SHOW JOIN OR REQUEST INVITE LINK EVENT LINK.
        $endDate = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($subject->event_id, 'DESC', $occure_id);
        
        $isEventFull = $subject->isEventFull(array('occurrence_id' => $occure_id));
        
        if(Engine_Api::_()->siteevent()->isTicketBasedEvent() && Engine_Api::_()->siteeventticket()->bookNowButton($subject) && ($subject->isRepeatEvent() || (!$subject->isRepeatEvent() && !$isEventFull))){
            return array(
                'label' => 'Book Now',
                'class' => 'buttonlink icon_siteevents_tickets',
                'route' => 'siteeventticket_ticket',
                'params' => array(
                    'action' => 'buy',
                    'event_id' => $subject->getIdentity(),
                    'occurrence_id' => $occure_id,
                ),
            );
        }        
        
        $occurrence = Engine_Api::_()->getItem('siteevent_occurrence', $occure_id);
        if(!Engine_Api::_()->siteevent()->isTicketBasedEvent()){     
            // Not yet associated at all
            if (null === $row && !$isEventFull && empty($occurrence->waitlist_flag)) {
                if (strtotime($endDate) < time())
                    return;
                if ($subject->membership()->isResourceApprovalRequired()) {
                    return array(
                        'label' => 'Request Invite',
                        'class' => 'buttonlink smoothbox icon_siteevents_invitejoin',
                        'route' => 'siteevent_extended',
                        'params' => array(
                            'controller' => 'member',
                            'action' => 'request',
                            'event_id' => $subject->getIdentity(),
                            'occurrence_id' => $occure_id,
                        ),
                    );
                } else {

                    return array(
                        'label' => 'Join Event',
                        'class' => 'buttonlink smoothbox icon_siteevents_invitejoin',
                        'route' => 'siteevent_extended',
                        'params' => array(
                            'controller' => 'member',
                            'action' => 'join',
                            'event_id' => $subject->getIdentity(),
                            'occurrence_id' => $occure_id
                        ),
                    );
                }
            }

            // Full member
            // @todo consider owner
            else if ($row->active) {
                //if (!$subject->isOwner($viewer)) {
                return array(
                    'label' => 'Leave Event',
                    'class' => 'buttonlink smoothbox icon_siteevents_inviteleave',
                    'route' => 'siteevent_extended',
                    'params' => array(
                        'controller' => 'member',
                        'action' => 'leave',
                        'event_id' => $subject->getIdentity(),
                        'occurrence_id' => $occure_id
                    ),
                );
                // }
            } else if (!$row->resource_approved && $row->user_approved) {
                return array(
                    'label' => 'Cancel Invite Request',
                    'class' => 'buttonlink smoothbox icon_siteevents_invitecancel',
                    'route' => 'siteevent_extended',
                    'params' => array(
                        'controller' => 'member',
                        'action' => 'cancel',
                        'event_id' => $subject->getIdentity(),
                        'occurrence_id' => $occure_id
                    ),
                );
            } else if (!$row->user_approved && $row->resource_approved) {

                $acceptinvite_array = array(
                    'label' => 'Accept Event Invite',
                    'class' => 'buttonlink smoothbox icon_siteevents_inviteaccept',
                    'route' => 'siteevent_extended',
                    'params' => array(
                        'controller' => 'member',
                        'action' => 'accept',
                        'event_id' => $subject->getIdentity(),
                        'occurrence_id' => $occure_id
                    ),
                );

                $ignoreinvite_array = array(
                    'label' => 'Ignore Event Invite',
                    'class' => 'buttonlink smoothbox icon_siteevents_invitereject',
                    'route' => 'siteevent_extended',
                    'params' => array(
                        'controller' => 'member',
                        'action' => 'reject',
                        'event_id' => $subject->getIdentity(),
                        'occurrence_id' => $occure_id
                    ),
                );

                if (strtotime($endDate) > time())
                    return array(
                        $acceptinvite_array, $ignoreinvite_array
                    );
                else
                    return array(
                        $ignoreinvite_array
                    );
            } 
//            else {
//                throw new Event_Model_Exception('An error has occurred.');
//            }
        }

        return false;
    }

    public function siteeventGutterNotificationSettings($row) {

        //RETURN FALSE IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return false;
        }

        //GET EVENT SUBJECT
        $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');
        $viewer = Engine_Api::_()->user()->getViewer();
//        $editPrivacy = $siteevent->authorization()->isAllowed($viewer, "edit");
//        if (empty($editPrivacy)) {
//            return false;
//        }
        
        $row = $siteevent->membership()
                    ->getRow($viewer);
        if(!$row)
         return false;
        
        return array(
            'class' => 'buttonlink icon_siteevent_notification smoothbox',
            'route' => "siteevent_specific",
            'action' => 'notifications',
            'params' => array(
                'event_id' => $siteevent->getIdentity()
            ),
        );
    }
    
   public function showAdminTransactionsTab() { 
    //IF SETTING DISABLED THEN DONT DISPLAY THIS TAB
    $ticketEnabled = Engine_Api::_()->siteevent()->hasTicketEnable();
    $packageEnabled = Engine_Api::_()->siteevent()->hasPackageEnable();
    
    if (empty($ticketEnabled) && empty($packageEnabled)) {
      return false;
    }
    
    if($packageEnabled){
      return array(
              'route' => "admin_default",
              'module' => 'siteeventpaid',
              'controller' => 'payment',
          );
    }elseif($ticketEnabled){
      //PAYMENT FLOW CHECK
      $paymentToSiteadmin = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.payment.to.siteadmin', 0);
      if($paymentToSiteadmin){
        return array(
              'route' => "admin_default",
              'module' => 'siteeventticket',
              'controller' => 'transaction',
          );         
        }else{
        return array(
              'route' => "admin_default",
              'module' => 'siteeventticket',
              'controller' => 'transaction',
              'action' => 'order-commission-transaction'
          );    
      }
    }
    
   }

}