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
class Siteevent_AnnouncementController extends Core_Controller_Action_Standard {

    public function init() {

        $event_id = $this->_getParam('event_id');

        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "edit")->isValid()) {
            return;
        }

        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, "view")->isValid())
            return;

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.announcement', 1)) {
            return;
        }
    }

    public function manageAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET EVENT ID
        $this->view->event_id = $event_id = $this->_getParam('event_id');

        //GET SITEEVENT ITEM
        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        //END MANAGE-ADMIN CHECK
        //GET REQUEST IS AJAX OR NOT
        $this->view->is_ajax = $this->_getParam('is_ajax', '');

        //WHICH TAB SHOULD COME ACTIVATE
        $this->view->TabActive = "announcements";
        //SET SITEEVENT SUBJECT
        Engine_Api::_()->core()->setSubject($siteevent);
        $fetchColumns = array('announcement_id', 'title', 'body', 'startdate', 'expirydate', 'status');
        $this->view->announcements = Engine_Api::_()->getDbtable('announcements', 'siteevent')->announcements($event_id, 1, 0, $fetchColumns);
    }

    public function createAction() {

        //GETTING THE OBJECT AND GROUP ID AND RESOURCE TYPE.
        $this->view->event_id = $event_id = $this->_getParam('event_id', null);
        $this->view->siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        //WHICH TAB SHOULD COME ACTIVATE
        $this->view->TabActive = "announcements";
        $announcementsTable = Engine_Api::_()->getDbTable('announcements', 'siteevent');

        //MAKE FORM
        $this->view->form = $form = new Siteevent_Form_Announcement_Create();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $values = $form->getValues();

            //BEGIN TRANSACTION
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $values['event_id'] = $event_id;
                $announcement = $announcementsTable->createRow();
                $announcement->setFromArray($values);
                $announcement->save();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            return $this->_helper->redirector->gotoRoute(array('controller' => 'announcement', 'action' => 'manage', 'event_id' => $event_id), 'siteevent_extended', true);
        }
    }

    public function editAction() {

        $announcement_id = $this->_getParam('announcement_id', null);
        $this->view->event_id = $event_id = $this->_getParam('event_id', null);
        $this->view->siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        //WHICH TAB SHOULD COME ACTIVATE
        $this->view->TabActive = "announcements";

        //MAKE FORM
        $this->view->form = $form = new Siteevent_Form_Announcement_Edit();

        //SHOW PRE-FIELD FORM 
        $announcement = Engine_Api::_()->getItem('siteevent_announcement', $announcement_id);
        $resultArray = $announcement->toArray();

        $resultArray['startdate'] = $resultArray['startdate'] . ' 00:00:00';
        $resultArray['expirydate'] = $resultArray['expirydate'] . ' 00:00:00';

        //IF NOT POST OR FORM NOT VALID THAN RETURN AND POPULATE THE FROM.
        if (!$this->getRequest()->isPost()) {
            $form->populate($resultArray);
            return;
        }

        //IF NOT POST OR FORM NOT VALID THAN RETURN
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //GET FORM VALUES
        $values = $form->getValues();

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $announcement->setFromArray($values);
            $announcement->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        return $this->_helper->redirector->gotoRoute(array('controller' => 'announcement', 'action' => 'manage', 'event_id' => $event_id), 'siteevent_extended', true);
    }

    public function deleteAction() {

        //GET THE CONTENT ID AND RESOURCE TYPE.
        $announcement_id = (int) $this->_getParam('announcement_id');
        $event_id = $this->_getParam('event_id');
        Engine_Api::_()->getDbtable('announcements', 'siteevent')->delete(array('announcement_id = ?' => $announcement_id, 'event_id = ?' => $event_id));
        exit();
    }

}