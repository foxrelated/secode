<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: DashboardController.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_DashboardController extends Core_Controller_Action_Standard {

    //ACTION FOR CONTACT INFORMATION
    public function contactAction() {

        //ONLY LOGGED IN USER CAN ADD OVERVIEW
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET EVENT ID AND OBJECT
        $event_id = $this->_getParam('event_id');

        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        Engine_Api::_()->core()->setSubject($siteevent);
        $contactDetails = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.contactdetail', array('phone', 'website', 'email'));
        if (empty($contactDetails)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if (!$siteevent->authorization()->isAllowed($viewer, 'edit')) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if (!Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'siteevent_event', "contact")) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //SELECTED TAB
        $this->view->TabActive = "contactdetails";

        //SET FORM
        $this->view->form = $form = new Siteevent_Form_Contactinfo();
        $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'siteevent');

        //POPULATE FORM
        $row = $tableOtherinfo->getOtherinfo($event_id);
        $value['email'] = $row->email;
        $value['phone'] = $row->phone;
        $value['website'] = $row->website;

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
                        $tableOtherinfo->update(array('email' => $email_id), array('event_id = ?' => $event_id));
                    }
                } else {
                    $tableOtherinfo->update(array('email' => $email_id), array('event_id = ?' => $event_id));
                }
            }

            //CHECK PHONE OPTION IS THERE OR NOT
            if (isset($values['phone'])) {
                $tableOtherinfo->update(array('phone' => $values['phone']), array('event_id = ?' => $event_id));
            }

            //CHECK WEBSITE OPTION IS THERE OR NOT
            if (isset($values['website'])) {
                $tableOtherinfo->update(array('website' => $values['website']), array('event_id = ?' => $event_id));
            }

            //SHOW SUCCESS MESSAGE
            $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
        }
    }

    //ACTION FOR CHANING THE PHOTO
    public function changePhotoAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET EVENT ID
        $this->view->event_id = $event_id = $this->_getParam('event_id');

        $viewer = Engine_Api::_()->user()->getViewer();

        //GET EVENT ITEM
        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        //IF THERE IS NO SITEEVENT.
        if (empty($siteevent)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        Engine_Api::_()->core()->setSubject($siteevent);




        //SELECTED TAB
        $this->view->TabActive = "profilepicture";

        //CAN EDIT OR NOT
        if (!$this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "edit")->isValid()) {
            return;
        }

        //PACKAGE BASED CHECK - CHECKS ARE REMOVED - (profile picture can be uploaded even package setting is no.)
//        if (Engine_Api::_()->siteevent()->hasPackageEnable()) {         
//          if (Engine_Api::_()->siteeventpaid()->allowPackageContent($siteevent->package_id, "photo")) {
//            $allowed_upload_photo = 1;
//          }else{
//            $allowed_upload_photo = 0;
//          }  
//        }else{
//          //AUTHORIZATION CHECK
//          $allowed_upload_photo = Engine_Api::_()->authorization()->isAllowed($siteevent, $viewer, "photo");
//        }
//        
//        if (empty($allowed_upload_photo)) {
//            return $this->_forward('requireauth', 'error', 'core');
//        }

        //GET FORM
        $this->view->form = $form = new Siteevent_Form_ChangePhoto();

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
            $db = Engine_Api::_()->getDbTable('events', 'siteevent')->getAdapter();
            $db->beginTransaction();
            //PROCESS
            try {
                //SET PHOTO
                $siteevent->setPhoto($form->Filedata);
                $db->commit();
            } catch (Engine_Image_Adapter_Exception $e) {
                $db->rollBack();
                $form->addError(Zend_Registry::get('Zend_Translate')->_('The uploaded file is not supported or is corrupt.'));
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $action = Engine_Api::_()->getDbtable('actions', 'seaocore')->addActivity($viewer, $siteevent, Engine_Api::_()->siteevent()->getActivtyFeedType($siteevent, 'siteevent_change_photo'));

            $file_id = Engine_Api::_()->getDbtable('photos', 'siteevent')->getPhotoId($event_id, $siteevent->photo_id);

            $photo = Engine_Api::_()->getItem('siteevent_photo', $file_id);

            if ($action != null) {
                Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $photo);
            }
        } else if ($form->getValue('coordinates') !== '') {
            $storage = Engine_Api::_()->storage();
            $iProfile = $storage->get($siteevent->photo_id, 'thumb.profile');
            $iSquare = $storage->get($siteevent->photo_id, 'thumb.icon');
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



        if (!empty($siteevent->photo_id)) {
            $photoTable = Engine_Api::_()->getItemTable('siteevent_photo');
            $order = $photoTable->select()
                    ->from($photoTable->info('name'), array('order'))
                    ->where('event_id = ?', $siteevent->event_id)
                    ->group('photo_id')
                    ->order('order ASC')
                    ->limit(1)
                    ->query()
                    ->fetchColumn();

            $photoTable->update(array('order' => $order - 1), array('file_id = ?' => $siteevent->photo_id));
        }

        return $this->_helper->redirector->gotoRoute(array('action' => 'change-photo', 'event_id' => $event_id), "siteevent_dashboard", true);
    }

    //ACTION FOR REMOVE THE PHOTO
    public function removePhotoAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET EVENT ID
        $event_id = $this->_getParam('event_id');

        //GET EVENT ITEM
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        $viewer = Engine_Api::_()->user()->getViewer();

        //CAN EDIT OR NOT
        if (!$this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "edit")->isValid()) {
            return;
        }

        //GET FILE ID
        $file_id = Engine_Api::_()->getDbtable('photos', 'siteevent')->getPhotoId($event_id, $siteevent->photo_id);

        //DELETE PHOTO
        if (!empty($file_id)) {
            $photo = Engine_Api::_()->getItem('siteevent_photo', $file_id);
            $photo->delete();
        }

        //SET PHOTO ID TO ZERO
        $siteevent->photo_id = 0;
        $siteevent->save();

        return $this->_helper->redirector->gotoRoute(array('action' => 'change-photo', 'event_id' => $event_id), "siteevent_dashboard", true);
    }

    //ACTION FOR CONTACT INFORMATION
    public function metaDetailAction() {

        //ONLY LOGGED IN USER CAN ADD OVERVIEW
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET EVENT ID AND OBJECT
        $event_id = $this->_getParam('event_id');

        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        
        Engine_Api::_()->core()->setSubject($siteevent);

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.metakeyword', 1)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if (!$siteevent->authorization()->isAllowed($viewer, 'edit')) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if (!Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'siteevent_event', "metakeyword")) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //SELECTED TAB
        $this->view->TabActive = "metadetails";

        //SET FORM
        $this->view->form = $form = new Siteevent_Form_Metainfo();

        $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'siteevent');

        //POPULATE FORM
        $value['keywords'] = $tableOtherinfo->getColumnValue($event_id, 'keywords');

        $form->populate($value);

        //CHECK FORM VALIDATION
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            //GET FORM VALUES
            $values = $form->getValues();
            $tableOtherinfo->update(array('keywords' => $values['keywords']), array('event_id = ?' => $event_id));

            //SHOW SUCCESS MESSAGE
            $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
        }
    }

    //ACTION FOR NOTIFICATION SETTINGS
    public function notificationSettingsAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET THE LOGGEDIN USER INFORMATION
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        //SAVE THE OCCURRENCE ID IN THE ZEND REGISTRY.
        $occurrence_id = $this->_getParam('occurrence_id', '');
        if (empty($occurrence_id) || !is_numeric($occurrence_id)) {
            //GET THE NEXT UPCOMING OCCURRENCE ID
            $occurrence_id = Engine_Api::_()->getDbTable('events', 'siteevent')->getNextOccurID($this->_getParam('event_id'));
        }
        Zend_Registry::set('occurrence_id', $occurrence_id);
        //GET EVENT ID
        $this->view->event_id = $event_id = $this->_getParam('event_id');

        //GET SITEEVENT ITEM
        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
//SET SITEEVENT SUBJECT
        Engine_Api::_()->core()->setSubject($siteevent);
        //CAN EDIT OR NOT
        if (!$this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "edit")->isValid()) {
            return;
        }

        //SELECTED TAB
        $this->view->TabActive = "notifications";
        $membershipTable = Engine_Api::_()->getDbTable('membership', 'siteevent');

				//GET THE LEADERS LIST AND CHECK IF THE VIEWER IS LEADER OR NORMAL USER.
				if ($siteevent->owner_id == $viewer->getIdentity()) {
					$isLeader = 1;
				} else { 
						$list = $siteevent->getLeaderList();
						$listItem = $list->get($viewer);
						$isLeader = ( null !== $listItem );
				}

        //SET FORM
        $this->view->form = $form = new Siteevent_Form_NotificationSettings(array('isLeader' => $isLeader));
        $row = $membershipTable->getRow($siteevent, $viewer);
        
        if(!$row) {
            $row->notification = Zend_Json_Decoder::decode('{"email":"0","notification":"1","action_notification":["posted","created","joined","comment","like","follow","rsvp"],"action_email":["posted","created","joined","rsvp"]}');
        }
        $this->view->notification = $row->notification['notification'];
        $this->view->email = $row->notification['email'];
        $form->populate($row->notification);

        //CHECK FORM VALIDATION
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            //GET FORM VALUES
            $values = $form->getValues();
            $membershipTable->update(array('notification' => $values), array('user_id =?' => $viewer_id, 'resource_id =?' => $event_id));
            //SHOW SUCCESS MESSAGE
            $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));

            $this->view->notification = $values['notification'];
            $this->view->email = $values['email'];
        }
    }
    
    public function icalOutlookAction() {
        $event_id = $this->_getParam('event_id');
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        $eventdateinfo = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getEventDate($siteevent->getIdentity(), 0);
        $start_time = strtotime($eventdateinfo['starttime']);
        $end_time = strtotime($eventdateinfo['endtime']);
        $params = array('title' => $siteevent->getTitle(),
            'datetime' => array('start' => date('Ymd\THis\Z', $start_time), 'end' => date('Ymd\THis\Z', $end_time)),
            'location' => $siteevent->location,
            'description' => $siteevent->body
        );
        
        $icsPath = APPLICATION_PATH . '/application/modules/Siteevent/settings';
        $icsFile = 'calendar.ics';

        //KILL ZEND'S OB
        $isGZIPEnabled = false;
        if (ob_get_level()) {
          $isGZIPEnabled = true;
          @ob_end_clean();
        }

        // Send headers
        header("Content-Disposition: attachment; filename=" . urlencode(basename($icsFile)), true);
        header("Content-Transfer-Encoding: Binary", true);
        header("Content-Type: application/force-download", true);
        header("Content-Type: application/octet-stream", true);
        header("Content-Type: application/download", true);
        header("Content-Description: File Transfer", true);
        
        if (empty($isGZIPEnabled)) {
            header('content-length: ' . filesize($icsPath . DIRECTORY_SEPARATOR . $icsFile));
        }
    
        flush();
        $http = _ENGINE_SSL ? 'https://' : 'http://';
        $handle = fopen($icsPath . DIRECTORY_SEPARATOR . $icsFile, 'r');
        echo "BEGIN:VCALENDAR\n";
        echo "VERSION:2.0\n";
        echo "X-WR-CALNAME:".$siteevent->getTitle(). "\n";
        //echo "PRODID:http://www.socialengineaddons.com/socialengine-advanced-events-plugin\n";
        echo "CALSCALE:GREGORIAN\n";
        echo "METHOD:REQUEST\n"; // requied by Outlook
        echo "BEGIN:VEVENT\n";
        echo "URL:". $http . $_SERVER['HTTP_HOST'] . $siteevent->getHref()."\n";
        echo "DTSTART:".$params['datetime']['start']."\n";
        echo "DTEND:".$params['datetime']['end']."\n";
        echo "SUMMARY:".$siteevent->getTitle()."\n";
        echo "LOCATION:".$siteevent->location."\n";
        echo "DESCRIPTION:".$siteevent->getDescription()."\n";
        echo "UID:".uniqid()."\n";
        echo "SEQUENCE:0\n";
        //echo "DTSTAMP:20101125T112600\n";
        echo "END:VEVENT\n";
        echo "END:VCALENDAR\n";
        flush();
        exit();
        
    }
}