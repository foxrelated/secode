<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: WaitlistController.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_WaitlistController extends Core_Controller_Action_Standard {

    //COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
    public function init() {

        //ONLY LOGGED IN USER CAN ACCESS
        if (!$this->_helper->requireUser()->isValid())
            return;

        //AUTHENTICATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, "view")->isValid()) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.waitlist', 1)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
    }

    public function manageAction() {

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET EVENT ID AND OBJECT
        $this->view->event_id = $event_id = $this->_getParam('event_id');
        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        if (!$siteevent->authorization()->isAllowed($viewer, 'edit')) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //SELECTED TAB
        $this->view->TabActive = "waitlist";
        $this->view->call_same_action = $this->_getParam('call_same_action', 0);

        //MAKE FORM
        $this->view->form = $form = new Siteevent_Form_Waitlist_Capacity();

        $params = array();
        $params['event_id'] = $event_id;

        if (isset($_POST['search'])) {
            $this->_helper->layout->disableLayout();
            $this->view->only_list_content = true;
            $params['username'] = isset($_POST['username']) ? $_POST['username'] : '';
            $params['creation_date_start'] = isset($_POST['creation_date_start']) ? $_POST['creation_date_start'] : '';
            $params['creation_date_end'] = isset($_POST['creation_date_end']) ? $_POST['creation_date_end'] : '';
            $params['occurrence_id'] = isset($_POST['occurrence_id']) ? $_POST['occurrence_id'] : '';
        }

        //MAKE PAGINATOR
        $this->view->paginator = Engine_Api::_()->getDbtable('waitlists', 'siteevent')->getSiteeventWaitlistsPaginator($params);
        $this->view->total_item = $this->view->paginator->getTotalItemCount();
        $this->view->paginator->setItemCountPerPage(50);

        $occurrenceTable = Engine_Api::_()->getDbtable('occurrences', 'siteevent');

        $this->view->datesInfo = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getAllOccurrenceDates($event_id);

        //SAVE THE VALUE
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) && isset($_POST['capacity'])) {

            if (Engine_Api::_()->siteevent()->isTicketBasedEvent()) {
                $currentCapacity = $occurrenceTable->maxSoldTickets(array('event_id' => $event_id));
            } else {
                $currentCapacity = $occurrenceTable->maxMembers(array('event_id' => $event_id, 'rsvp' => 2));
            }

            if (!empty($_POST['capacity']) && $currentCapacity > $_POST['capacity']) {
                $error = $this->view->translate('Capacity value can not be less than %1s as %2s members are already joined this event.', $currentCapacity, $currentCapacity);
                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }

            $previousCapacity = $siteevent->capacity;

            $siteevent->capacity = empty($_POST['capacity']) ? NULL : $_POST['capacity'];

            if (empty($siteevent->capacity)) {
                Engine_Api::_()->getDbTable('occurrences', 'siteevent')->update(array('waitlist_flag' => 0), array('event_id = ?' => $siteevent->event_id));
            }

            $siteevent->save();
            $this->view->form = $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
        }

        //POPULATE FORM
        $form->populate($siteevent->toArray());
    }

    public function joinAction() {

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET OCCURRENCE ID AND OBJECT
        $occurrence_id = $this->_getParam('occurrence_id');
        $occurrence = Engine_Api::_()->getItem('siteevent_occurrence', $occurrence_id);

        $waitlistTable = Engine_Api::_()->getDbTable('waitlists', 'siteevent');

        $siteevent = $occurrence->getParent();

        $host = $siteevent->getHost()->getType();
        $host_id = 0;
        if ($host == 'user') {
            $host_id = $siteevent->getHost()->getIdentity();
        }

        $this->view->isHost = false;
        if ($host_id == $viewer_id) {
            $this->view->isHost = true;
        }

        if ($siteevent->owner_id == $viewer_id) {
            $this->view->isLeader = $isLeader = 1;
        } else {
            $list = $siteevent->getLeaderList();
            $listItem = $list->get($viewer);
            $this->view->isLeader = $isLeader = ( null !== $listItem );
        }

        $params = array();
        $params['occurrence_id'] = $occurrence_id;
        $params['user_id'] = $viewer_id;
        $params['columnName'] = 'waitlist_id';
        $this->view->waitlist_id = $waitlist_id = $waitlistTable->getColumnValue($params);

        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('confirm') == true) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $waitlist = $waitlistTable->createRow();
                $waitlist->setFromArray($params);
                $waitlist->save();
                
                Engine_Api::_()->getDbTable('occurrences', 'siteevent')->update(array('waitlist_flag' => 1), array('occurrence_id = ?' => $occurrence_id));

                $getLeaders = $this->getLeaders($siteevent);

                $eventTitle = $siteevent->getTitle();
                $userTitle = $viewer->getTitle();
                $https = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . $_SERVER['HTTP_HOST'];
                $siteeventlink = $https . $siteevent->getHref();
                $userLink = $https . $viewer->getHref();
                $event_title_with_link = "<a href='$siteeventlink'>$eventTitle</a>";
                $user_title_with_link = "<a href='$userLink'>$userTitle</a>";
                $notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');

                foreach ($getLeaders as $user_subject) {

                    $notificationTable->addNotification($user_subject, $viewer, $siteevent, 'SITEEVENT_JOIN_WAITLIST', array(
                        'event_title' => $eventTitle,
                        'event_title_with_link' => $event_title_with_link,
                        'user_title_with_link' => $user_title_with_link,
                        'queue' => false
                    ));
                }
                
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => 10,
                'format' => 'smoothbox',
                'messages' => Zend_Registry::get('Zend_Translate')->_('You have successfully joined this event in waitlist and you can also view such events through "Waitlist" link available on "My Events Page".')));
        }
    }

    public function messageWaitlisterAction() {

        //GET WAITLIST ID AND OBJECT
        $waitlist_id = $this->_getParam("waitlist_id");
        $waitlist = Engine_Api::_()->getItem('siteevent_waitlist', $waitlist_id);

        $occurrence = Engine_Api::_()->getItem('siteevent_occurrence', $waitlist->occurrence_id);

        if (empty($occurrence)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //MAKE FORM
        $this->view->form = $form = new Messages_Form_Compose();

        $form->setDescription(Zend_Registry::get('Zend_Translate')->_('Create your message with the form given below. (This message will be sent to the waitlister.)'));

        $form->removeElement('to');
        $form->toValues->setValue($waitlist->user_id);
        $values = $this->getRequest()->getPost();

        //CHECK METHOD/DATA
        if (!$this->getRequest()->isPost()) {
            return;
        }

        $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
        $db->beginTransaction();

        try {

            //SENDING MESSAGE
            if (empty($values['title'])) {
                $error = $this->view->translate('Subject is required field !');
                $error = Zend_Registry::get('Zend_Translate')->_($error);
                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }

            $user = Engine_Api::_()->getItem('user', $waitlist->user_id);

            $event = $occurrence->getParent();

            $event_title = $event->getTitle();
            $http = _ENGINE_SSL ? 'https://' : 'http://';
            $event_title_with_link = '<a href =' . $http . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('event_id' => $event->getIdentity(), 'slug' => $event->getSlug(), 'occurrence_id' => $occurrence->getIdentity()), "siteevent_entry_view") . ">$event_title</a>";

            $viewer = Engine_Api::_()->user()->getViewer();

            $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send($viewer, $values['toValues'], $values['title'], $values['body'] . "<br><br>" . $this->view->translate('This message corresponds to the Event: %s', $event_title_with_link));

            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $conversation, 'message_new');

            //INCREMENT MESSAGE COUNTER
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

            $db->commit();

            return $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => true,
                    //'parentRefresh' => true,
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.'))
            ));
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    //ACTION FOR DELETE WAITLIST
    public function deleteAction() {

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET WAITLIST ID AND WAITLIST OBJECT
        $this->view->waitlist_id = $waitlist_id = $this->_getParam('waitlist_id');
        $waitlist = Engine_Api::_()->getItem('siteevent_waitlist', $waitlist_id);

        //GET EVENT OBJECT
        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_occurrence', $waitlist->occurrence_id)->getParent();

        if (!$siteevent->authorization()->isAllowed($viewer, 'edit')) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $occurrence = Engine_Api::_()->getItem('siteevent_occurrence', $waitlist->occurrence_id);

        $waitlistCount = Engine_Api::_()->getDbTable('waitlists', 'siteevent')->waitlistCount(array('occurrence_id' => $waitlist->occurrence_id));

        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {

                $waitlist->delete();

                if ($waitlistCount == 1) {
                    $occurrence->waitlist_flag = 0;
                    $occurrence->save();
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $url = $this->_helper->url->url(array('controller' => 'waitlist', 'action' => 'manage', 'event_id' => $siteevent->event_id), 'siteevent_extended', true);

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRedirect' => $url,
                'parentRedirectTime' => '15',
                'format' => 'smoothbox',
                'messages' => Zend_Registry::get('Zend_Translate')->_('Waitlist has been deleted successfully.')
            ));
        }
    }

    //ACTION FOR MULTI DELETE WAITLISTS
    public function multiDeleteAction() {

        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();

            $waitlistTable = Engine_Api::_()->getDbTable('waitlists', 'siteevent');
            foreach ($values as $key => $value) {
                if ($key == 'delete_' . $value) {
                    $waitlist_id = (int) $value;

                    $waitlist = Engine_Api::_()->getItem('siteevent_waitlist', $waitlist_id);

                    $occurrence = Engine_Api::_()->getItem('siteevent_occurrence', $waitlist->occurrence_id);
                    $waitlistCount = $waitlistTable->waitlistCount(array('occurrence_id' => $waitlist->occurrence_id));

                    $waitlist->delete();

                    if ($waitlistCount == 1) {
                        $occurrence->waitlist_flag = 0;
                        $occurrence->save();
                    }
                }
            }
        }

        $event_id = $this->_getParam('event_id', null);
        return $this->_helper->redirector->gotoRoute(array('controller' => 'waitlist', 'action' => 'manage', 'event_id' => $event_id), "siteevent_extended", true);
    }

    public function checkTicketAvailabilityAction() {

        $occurrence_id = $this->_getParam('occurrence_id', null);
        $totalTicketsInCart = $this->_getParam('totalTicketsInCart', null);
        
        $occurrence = Engine_Api::_()->getItem('siteevent_occurrence', $occurrence_id);

        $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');
        if(!empty($occurrence->waitlist_flag)) {
            $this->view->eventIsFull = 1;
        }
        elseif (empty($siteevent->capacity)) {
            $this->view->eventIsFull = 0;
        } else {
            $totalSoldTickets = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->totalSoldTickets(array('occurrence_id' => $occurrence_id));

            $this->view->eventIsFull = 1;
            if ($siteevent->capacity >= ($totalSoldTickets + $totalTicketsInCart)) {
                $this->view->eventIsFull = 0;
            }
        }
    }

    public function eventsInWaitingAction() {

        $params['limit'] = 50;
        $params['showEventType'] = $this->view->showEventType = 'all';
        $this->view->titlePosition = $this->_getParam('titlePosition', 1);
        $this->view->statistics = $params['statistics'] = $this->_getParam('eventInfo', array("likeCount", "memberCount", "hostName", "location", "directionLink", "startDate", "endDate"));
        $this->view->postedby = $params['postedby'] = $this->_getParam('postedby', 1);
        $params['ratingType'] = $this->view->ratingType = $this->_getParam('ratingType', 'rating_avg');
        $this->view->showContent = $params['showContent'] = $this->_getParam('showContent', array("price", "location"));
        $this->view->truncation = $params['truncation'] = $this->_getParam('truncation', 16);
        $this->view->bottomLine = $params['bottomLine'] = $this->_getParam('bottomLine', 2);
        $this->view->bottomLineGrid = $params['bottomLineGrid'] = $this->_getParam('bottomLineGrid', 2);
        $this->view->columnWidth = $params['columnWidth'] = $this->_getParam('columnWidth', '180');
        $this->view->truncationLocation = $params['truncationLocation'] = $this->_getParam('truncationLocation', 35);
        $this->view->title_truncationGrid = $params['truncationGrid'] = $this->_getParam('truncationGrid', 100);
        $this->view->columnHeight = $params['columnHeight'] = $this->_getParam('columnHeight', '328');
        $params['interval'] = $interval = $this->_getParam('interval', 'overall');
        $this->view->enableLocation = $checkLocation = Engine_Api::_()->siteevent()->enableLocation();

        $params['eventsInWaitlist'] = 1;
        $params['user_id'] = Engine_Api::_()->user()->getViewer()->getIdentity();
        $params['popularity'] = 'creation_date';

        $this->view->params = $params;

        //GET EVENTS
        $this->view->events = $paginator = Engine_Api::_()->getDbTable('events', 'siteevent')->eventsBySettings($params);
    }

    public function getLeaders($siteevent) {

        $list = $siteevent->getLeaderList();
        $list_id = $list['list_id'];

        $listItemTable = Engine_Api::_()->getDbTable('listItems', 'siteevent');
        $listItemTableName = $listItemTable->info('name');

        $userTable = Engine_Api::_()->getDbtable('users', 'user');
        $userTableName = $userTable->info('name');
        $selectLeaders = $listItemTable->select()
            ->from($listItemTableName, array('child_id'))
            ->where("list_id = ?", $list_id)
            ->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN);
        $selectLeaders[] = $siteevent->owner_id;

        $select = $userTable->select()
            ->from($userTableName)
            ->where("$userTableName.user_id IN (?)", (array) $selectLeaders);
        return $userTable->fetchAll($select);
    }

    public function unsetWaitlistFlagAction() {

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET WAITLIST ID AND WAITLIST OBJECT
        $this->view->occurrence_id = $occurrence_id = $this->_getParam('occurrence_id');
        $occurrence = Engine_Api::_()->getItem('siteevent_occurrence', $occurrence_id);

        //GET EVENT OBJECT
        $siteevent = $occurrence->getParent();

        if (!$siteevent->authorization()->isAllowed($viewer, 'edit')) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {

                $occurrence->waitlist_flag = 0;
                $occurrence->save();

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $url = $this->_helper->url->url(array('controller' => 'waitlist', 'action' => 'manage', 'event_id' => $siteevent->event_id), 'siteevent_extended', true);

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRedirect' => $url,
                'parentRedirectTime' => '15',
                'format' => 'smoothbox',
                'messages' => Zend_Registry::get('Zend_Translate')->_('Changes has been saved successfully.')
            ));
        }
    }

}
