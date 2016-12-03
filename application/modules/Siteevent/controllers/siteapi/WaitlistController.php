<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: WaitlistControllers.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_WaitlistController extends Siteapi_Controller_Action_Standard {

    public function init() {
        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, "view")->isValid())
            $this->respondWithError('unauthorized');

        //SET EVENT SUBJECT
        if ($this->getRequestParam('event_id') && (0 !== ($event_id = (int) $this->getRequestParam('event_id')) &&
                null !== ($siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id))))
            Engine_Api::_()->core()->setSubject($siteevent);

        $this->_hasPackageEnable = Engine_Api::_()->siteevent()->hasPackageEnable();
    }

    /*
     * Add capacity and waitlist
     */

    public function indexAction() {
        $event_id = $this->_getParam('event_id');
        $occurence_id = $this->_getParam('occurence_id');
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        if (empty($siteevent)) {
            $this->respondWithError('no_record');
        }

        if (!$siteevent->authorization()->isAllowed($viewer, 'edit')) {
            $this->respondWithError('no_record');
        }

        if ($this->getRequest()->isGet()) {
            $waitListForm = array();
            $waitListForm[] = array(
                'type' => 'Text',
                'name' => 'capacity',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Capacity'),
                'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Enter the value of maximum members who can join this event. After this capacity is reached, members will be able to apply for the waitlist of this event, which you will be able to manage from the below section.'),
            );

            $waitListForm[] = array(
                'type' => 'Submit',
                'name' => 'submit',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Save')
            );

            $this->respondWithSuccess($waitListForm);
        } else if ($this->getRequest()->isPost()) {
            $params = array();
            $occurrenceTable = Engine_Api::_()->getDbtable('occurrences', 'siteevent');
            if (Engine_Api::_()->siteevent()->isTicketBasedEvent()) {
                $currentCapacity = $occurrenceTable->maxSoldTickets(array('event_id' => $event_id));
            } else {
                $currentCapacity = $occurrenceTable->maxMembers(array('event_id' => $event_id, 'rsvp' => 2));
            }

            if (!empty($_POST['capacity']) && $currentCapacity > $_POST['capacity']) {
                $capacityMessage = 'Capacity value can not be less than ' . $currentCapacity . ' as ' . $currentCapacity . ' members are already joined this event.';
                $this->respondWithValidationError('validation_fail', $capacityMessage);
            }

            $siteevent->capacity = empty($_POST['capacity']) ? NULL : $_POST['capacity'];

            if (empty($siteevent->capacity)) {
                Engine_Api::_()->getDbTable('occurrences', 'siteevent')->update(array('waitlist_flag' => 0), array('event_id = ?' => $siteevent->event_id));
            }
            $siteevent->save();

            $this->successResponseNoContent('no_content', true);
        }
    }

    /*
     * Join event in waitlist
     */
    
    public function joinAction() {
        $event_id = $this->_getParam('event_id');
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        if (empty($siteevent)) {
            $this->respondWithError('no_record');
        }

        //GET OCCURRENCE ID AND OBJECT
        $occurrence_id = $this->_getParam('occurrence_id');
        $occurrence = Engine_Api::_()->getItem('siteevent_occurrence', $occurrence_id);

        $waitlistTable = Engine_Api::_()->getDbTable('waitlists', 'siteevent');

//        $siteevent = $occurrence->getParent();

        $host = $siteevent->getHost()->getType();
        $host_id = 0;
        if ($host == 'user') {
            $host_id = $siteevent->getHost()->getIdentity();
        }

        $params = array();
        $params['occurrence_id'] = $occurrence_id;
        $params['user_id'] = $viewer_id;
        $params['columnName'] = 'waitlist_id';

        if ($this->getRequest()->isPost()) {
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

            $this->successResponseNoContent('no_content', true);
        }
    }
    
    protected function getLeaders($siteevent) {
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

}
