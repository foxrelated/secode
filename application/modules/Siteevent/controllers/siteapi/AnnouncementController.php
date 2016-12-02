<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_AnnouncementController extends Siteapi_Controller_Action_Standard {

    public function init() {

        $event_id = $this->_getParam('event_id');

        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        if (!empty($siteevent))
            Engine_Api::_()->core()->setSubject($siteevent);

        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, "view")->isValid())
            $this->respondWithError('unauthorized');

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.announcement', 1)) {
            $this->respondWithError('unauthorized');
        }
    }

    public function indexAction() {

        //DONT RENDER THIS IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event') || !Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.announcement', 1)) {
            $this->respondWithError('unauthorized');
        }

        //GET VIEWER INFORMATION
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET SITEEVENT SUBJECT
        $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        $limit = $this->_getParam('itemCount', 3);

        $fetchColumns = array('announcement_id', 'title', 'body');
        $announcements = Engine_Api::_()->getDbtable('announcements', 'siteevent')->announcements($siteevent->event_id, 0, $limit, $fetchColumns);
        $childCount = count($announcements);

        if ($childCount <= 0) {
            $this->respondWithError('no_record');
        }
        $response['itemCount'] = $childCount;

        $response['canCreate'] = $this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "edit")->isValid();

        $response['canDelete'] = $this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "delete")->isValid();
        foreach ($announcements as $item) {
            $announcement = $item->toArray();
            if (isset($announcement['body']) && !empty($announcement['body']))
                $announcement['body'] = strip_tags($announcement['body']);
            $tempresponse[] = $announcement;
        }
        $response['announcements'] = $tempresponse;

        $this->respondWithSuccess($response);
    }

    public function createAction() {

        //GETTING THE OBJECT AND GROUP ID AND RESOURCE TYPE.
        $event_id = $event_id = $this->_getParam('event_id', null);
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        if (empty($siteevent))
            $this->respondWithError('no_record');

        //WHICH TAB SHOULD COME ACTIVATE
        $announcementsTable = Engine_Api::_()->getDbTable('announcements', 'siteevent');

        if (!$this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "edit")->isValid()) {
            $this->respondWithError('unauthorized');
        }

        //MAKE FORM
        $form = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->getAnnouncementCreateForm();

        if ($this->getRequest()->isGet())
            $this->respondWithSuccess($form);


        if ($this->getRequest()->isPost()) {
            foreach ($form as $element) {

                if (isset($_REQUEST[$element['name']]))
                    $values[$element['name']] = $_REQUEST[$element['name']];
            }

            // START FORM VALIDATION
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'siteevent')->getAnnouncementFormValidators();
            $values['validators'] = $validators;
            $validationMessage = $this->isValid($values);
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            if (!isset($values['status']))
                $values['status'] = 1;

            //BEGIN TRANSACTION
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $values['event_id'] = $event_id;
                $announcement = $announcementsTable->createRow();
                $announcement->setFromArray($values);
                $announcement->save();
                $db->commit();
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollBack();
            }
        }
    }

    public function deleteAction() {

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET THE CONTENT ID AND RESOURCE TYPE.
        $announcement_id = (int) $this->_getParam('announcement_id');
        $event_id = $this->_getParam('event_id');
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        $announcement = Engine_Api::_()->getItem('siteevent_announcement', $announcement_id);

        if (empty($announcement_id) || empty($siteevent))
            $this->respondWithError('no_record');


        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        //WHICH TAB SHOULD COME ACTIVATE
        $announcementsTable = Engine_Api::_()->getDbTable('announcements', 'siteevent');

        if (!$this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "delete")->isValid()) {
            $this->respondWithError('unauthorized');
        }

        Engine_Api::_()->getDbtable('announcements', 'siteevent')->delete(array('announcement_id = ?' => $announcement_id, 'event_id = ?' => $event_id));

        $this->successResponseNoContent('no_content', true);
    }

}
