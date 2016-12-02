<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_IndexController extends Siteapi_Controller_Action_Standard {

    public function init() {
        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, "view")->isValid())
            $this->respondWithError('unauthorized');

        //SET EVENT SUBJECT
        if ($this->getRequestParam('event_id') && (0 !== ($event_id = (int) $this->getRequestParam('event_id')) &&
                null !== ($siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id))))
            Engine_Api::_()->core()->setSubject($siteevent);

        $this->_hasPackageEnable = Engine_Api::_()->siteevent()->hasPackageEnable();

        // Set the translations for zend library.
        if (!Zend_Registry::isRegistered('Zend_Translate'))
            Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();
    }

    /**
     * RETURN THE LIST AND DETAILS OF ALL EVENTS WITH SEARCH PARAMETERS.
     * 
     * @return array
     */
    public function indexAction() {

        // VALIDATE REQUEST METHOD
        $this->validateRequestMethod();

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();


        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, "view")->isValid())
            $this->respondWithError('unauthorized');

        // PREPARE RESPONSE
        $values = $response = array();
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $values = $this->_getAllParams();

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        // check to see if request is for specific user's listings
        $user_id = $this->getRequestParam('user_id', null);

        if (!empty($user_id)) {
            $values['user_id'] = $user_id;
        }

        if (isset($values['category_id']) && !empty($values['category_id'])) {

            $profileFields = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->getSearchProfileFields();

            if (isset($profileFields) && !empty($profileFields)) {
                foreach ($profileFields[$values['category_id']] as $element) {
                    if (isset($values[$element['name']]))
                        $customProfileFields[$element['name']] = $values[$element['name']];
                }
            }
        }

        if ($this->getRequestParam('search', null)) {
            $values['search_text'] = $this->getRequestParam('search');
        }
        if ($this->getRequestParam('category_id', null)) {
            $values['category_id'] = $this->getRequestParam('category_id');
        }

        $values['orderby'] = $this->getRequestParam('orderby', 'event_id');

        $values['type'] = 'browse';

        // upcoming(ongoing + upcoming), onlyupcoming(upcoming), onlyOngoing(ongoing), past, all
        $values['action'] = $this->getRequestParam('showEventType', 'upcoming');

        $values['ratingType'] = $this->getRequestParam('ratingType', 'rating_both');

        $getLocation = $this->getRequestParam('getLocation', '1');

        $searchLocation = $this->getRequestParam('location', null);

        //TO GET OR NOT THE EXACT LOCATION OF EVENT
        if (isset($values['restapilocation']) && !empty($values['restapilocation']))
            $values['location'] = $this->getRequestParam('restapilocation', null);

        //TO GET OR NOT THE EXACT LOCATION OF EVENT
        if (!empty($searchLocation) && isset($searchLocation))
            $values['location'] = $this->getRequestParam('location', null);

//        $profileFeilds = $this->_getParam('profileFeilds', null);
//        $detactLocation = $values['detactLocation'] = $params['detactLocation'] = $this->_getParam('detactLocation', 0);
//
//        if ($detactLocation) {
//            $detactLocation = Engine_Api::_()->siteevent()->enableLocation();
//        }
//        if ($detactLocation) {
//            $defaultLocationDistance = $values['defaultLocationDistance'] = $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
//            $values['Latitude'] = $values['latitude'] = $this->_getParam('latitude', 0);
//            $values['Longitude'] = $values['longitude'] = $this->_getParam('longitude', 0);
//        }

        $values['orderbystarttime'] = 1;

//        if (!$detactLocation && empty($_GET['seaolocation']) && isset($values['location'])) {
//            unset($values['location']);
//
//            if (empty($_GET['latitude']) && isset($values['latitude'])) {
//                unset($values['latitude']);
//            }
//
//            if (empty($_GET['longitude']) && isset($values['longitude'])) {
//                unset($values['longitude']);
//            }
//
//            if (empty($_GET['Latitude']) && isset($values['Latitude'])) {
//                unset($values['Latitude']);
//            }
//
//            if (empty($_GET['Longitude']) && isset($values['Longitude'])) {
//                unset($values['Longitude']);
//            }
//        }

        try {
            //GET EVENTS PAGINATOR
            // seperate paginator for featured & sponsored events 
            if (isset($values['event_time']) && !empty($values['event_time']) && ($values['event_time'] == 'featured' || $values['event_time'] == 'sponsored')) {
                if ($values['event_time'] == 'featured') {
                    $values['featured'] = 1;
                } elseif ($values['event_time'] == 'sponsored') {
                    $values['sponsored'] = 1;
                }
                $paginator = Engine_Api::_()->getDbTable('events', 'siteevent')->getEvent('', $values);
            } else {
                $paginator = Engine_Api::_()->getDbTable('events', 'siteevent')->getSiteeventsPaginator($values, $customProfileFields);
            }
            $paginator->setItemCountPerPage($this->getRequestParam("limit", 20));
            $paginator->setCurrentPageNumber($this->getRequestParam("page", 1));

            //SET VIEW
            Engine_Api::_()->getApi('Core', 'siteapi')->setView();
            $response['canCreate'] = Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, 'create');
            $response['packagesEnabled'] = $this->_packagesEnabled();

            $response["getTotalItemCount"] = $getTotalItemCount = $paginator->getTotalItemCount();

            if (!empty($getTotalItemCount)) {
                foreach ($paginator as $eventObj) {
                    // continue if Deleted member
                    if (empty($eventObj->host_id))
                        continue;
                    $event = $eventObj->toArray();

                    if (!$event['location'])
                        $event['location'] = '';

                    //CATEGORY NAME
                    $event['category_name'] = Engine_Api::_()->getItem('siteevent_category', $event['category_id'])->category_name;

                    $occurrence_id = Engine_Api::_()->getDbTable('events', 'siteevent')->getNextOccurID($eventObj->getIdentity());

                    //GET DATES OF EVENT
                    $tz = Engine_Api::_()->getApi('settings', 'core')->core_locale_timezone;
                    if (!empty($viewer_id)) {
                        $tz = $viewer->timezone;
                    }
                    $occurrenceTable = Engine_Api::_()->getDbTable('occurrences', 'siteevent');
                    $dates = $occurrenceTable->getEventDate($eventObj->getIdentity(), $occurrence_id);

                    if (isset($dates['starttime']) && !empty($dates['starttime']) && isset($tz)) {
                        $startDateObject = new Zend_Date(strtotime($dates['starttime']));
                        $startDateObject->setTimezone($tz);
                        $dates['starttime'] = $startDateObject->get('YYYY-MM-dd HH:mm:ss');
                    }
                    if (isset($dates['endtime']) && !empty($dates['endtime']) && isset($tz)) {
                        $endDateObject = new Zend_Date(strtotime($dates['endtime']));
                        $endDateObject->setTimezone($tz);
                        $dates['endtime'] = $endDateObject->get('YYYY-MM-dd HH:mm:ss');
                    }

                    $event['isRepeatEvent'] = ($eventObj->isRepeatEvent()) ? 1 : 0;

                    if (!isset($dates) || empty($dates))
                        continue;

                    $event = array_merge($event, $dates);

                    $totalEventOccurrences = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getOccurrenceCount($eventObj->event_id);
                    if (!empty($eventObj->repeat_params) && $totalEventOccurrences > 1) {
                        $event['hasMultipleDates'] = 1;
                    } else {
                        $event['hasMultipleDates'] = 0;
                    }

                    // ADD OWNER IMAGES
                    $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($eventObj, true);
                    $event = array_merge($event, $getContentImages);
                    $event["owner_title"] = $eventObj->getOwner()->getTitle();

                    // ADD EVENT IMAGES
                    $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($eventObj);
                    $event = array_merge($event, $getContentImages);

                    //GET EXACT LOCATION
                    if ($getLocation == 1 && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1) && !$siteevent->is_online) {
                        //GET LOCATION
                        $value['id'] = $event['event_id'];
                        $location = Engine_Api::_()->getDbtable('locations', 'siteevent')->getLocation($value);
                        if (isset($location) && is_array($location))
                            $event['location'] = $location->location;
                    }

                    $event['hosted_by'] = '';
                    if (isset($eventObj->host_type) && !empty($eventObj->host_id)) {
                        $organizerObj = Engine_Api::_()->getItem($eventObj->host_type, $eventObj->host_id);
                        $organizer['host_type'] = $eventObj->host_type;
                        $organizer['host_id'] = $organizerObj->getIdentity();
                        $organizer['host_title'] = $organizerObj->getTitle();
                        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($organizerObj, false);
                        $organizer = array_merge($organizer, $getContentImages);
                        $event['hosted_by'] = $organizer;
                    }

                    $leaders = Engine_Api::_()->getItem('siteevent_event', $event['event_id'])->getLedBys(false);
                    // Set default ledby.
                    $defaultLedby['title'] = $eventObj->getOwner()->getTitle();
                    $defaultLedby['type'] = $eventObj->getOwner()->getType();
                    $defaultLedby['id'] = $eventObj->getOwner()->getIdentity();
                    $event['ledby'][] = $defaultLedby;

                    // Set array of ledby.
                    foreach ($leaders as $leader) {
                        $tempLeader['title'] = $leader->getOwner()->getTitle();
                        $tempLeader['type'] = $leader->getOwner()->getType();
                        $tempLeader['id'] = $leader->getOwner()->getIdentity();
                        $event['ledby'][] = $tempLeader;
                    }

                    //occurence id
                    $isAllowedView = $eventObj->authorization()->isAllowed($viewer, 'view');
                    $event["allow_to_view"] = empty($isAllowedView) ? 0 : 1;

                    $isAllowedEdit = $eventObj->authorization()->isAllowed($viewer, 'edit');
                    $event["edit"] = empty($isAllowedEdit) ? 0 : 1;

                    $isAllowedDelete = $eventObj->authorization()->isAllowed($viewer, 'delete');
                    $event["delete"] = empty($isAllowedDelete) ? 0 : 1;

                    $tempResponse[] = $event;
                }
                if (!empty($tempResponse))
                    $response['response'] = $tempResponse;
            }
        } catch (Exception $ex) {
            $this->respondWithError('internal_server_error', $ex->getMessage());
        }

        //RESPONSE
        $this->respondWithSuccess($response, true);
    }

    /**
     * GET SEARCH FORM 
     * 
     * @return array
     */
    public function searchFormAction() {

        // VALIDATE REQUEST METHOD
        $this->validateRequestMethod();
        $response = array();
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, 'view'))
            $this->respondWithError('unauthorized');

        try {
            $restapilocation = $this->getRequestParam('restapilocation', null);
            $response = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->getBrowseSearchForm($restapilocation);
            $this->respondWithSuccess($response, true);
        } catch (Expection $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    /**
     * GET LIST AND DETAILS OF ALL VIEWER EVENTS 
     * 
     * @return array
     */
    public function manageAction() {

        //VALIDATE REQUEST
        $this->validateRequestMethod();

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, "create")->isValid())
            $this->respondWithError('unauthorized');
        $request = Zend_Controller_Front::getInstance()->getRequest();

        $viewer_id = $viewer->getIdentity();
        $values = $response = array();

        $isEnabledPackage = Engine_Api::_()->siteevent()->hasPackageEnable();
        $getHost = Engine_Api::_()->getApi('core', 'siteapi')->getHost();
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $baseUrl = @trim($baseUrl, "/");

        //GET ALL PARAMETERS
        $values = $this->_getAllParams();

        if ($this->getRequestParam('user_id', null))
            $subject = Engine_Api::_()->getItem('user', $this->getRequestParam('user_id'));
        $subject = !empty($subject) ? $subject : $viewer;

        //GET RSVP
        $rsvp = $this->_getParam('rsvp', -1);

        $values['rsvp'] = $rsvp;
        $values['user_id'] = $subject->getIdentity();
        $values['type'] = 'manage';
        $values['orderby'] = 'event_id';
        $values['action'] = 'manage';
        $rsvp_form = $this->getRequestParam('rsvp_form', true);

        //TO GET OR NOT THE EXACT LOCATION OF EVENT
        $getLocation = $this->_getParam('getLocation', null);

        if (isset($values['host_type']) && !empty($values['host_type']) && !empty($values['host_id']) && !empty($values['host_type'])) {
            $this->setRequestMethod();
            $this->_forward('index', 'index', 'siteevent', array(
                'host_type' => $values['host_type'],
                'host_id' => $values['host_id'],
                'showEventType' => 'all',
                'viewtype' => '',
            ));
            return;
        }

        // Only mine
        if (@$values['view'] == 2) {
            $select = $table->select()
                    ->where('user_id = ?', $subject->getIdentity());
        }
        // All membership
        else {
            $membership = Engine_Api::_()->getDbtable('membership', 'Siteevent');
            $select = $membership->getMembershipsOfSelect($subject);
            $select->where('event_id IS NOT NULL');
        }

        $viewType = $values['viewtype'] = $this->getRequestParam('viewType', 'upcoming');


        try {
            //GET PAGINATOR
            $paginator = Engine_Api::_()->getDbTable('events', 'siteevent')->getSiteeventsPaginator($values);
            $paginator->setItemCountPerPage($this->getRequestParam("limit", 20));
            $paginator->setCurrentPageNumber($this->getRequestParam("page", 1));
            $showEventUpcomingPastCount = $this->_getParam('showEventUpcomingPastCount', false);
            $limit = $this->getRequestParam("limit", 20);

            if ($showEventUpcomingPastCount) {
                if ($values['viewtype'] == 'upcoming') {
                    $totalUpcomingEventCount = $paginator->getTotalItemCount();
                    $values['viewtype'] = 'past';
                    $paginator = Engine_Api::_()->getDbTable('events', 'siteevent')->getSiteeventsPaginator($values);
                    $totalPastEventCount = $paginator->getTotalItemCount();
                    $totalPages = ceil(($totalUpcomingEventCount) / $limit);
                } else {
                    $totalPastEventCount = $paginator->getTotalItemCount();
                    $values['viewtype'] = 'upcoming';
                    $paginator = Engine_Api::_()->getDbTable('events', 'siteevent')->getSiteeventsPaginator($values);
                    $totalUpcomingEventCount = $paginator->getTotalItemCount();
                    $totalPages = ceil(($event['totalPastEventCount']) / $limit);
                }
                $response['totalUpcomingEventCount'] = $totalUpcomingEventCount;
                $response['totalPastEventCount'] = $totalPastEventCount;
                $response['totalpages'] = $totalPages;
            } else {
                if ($values['viewtype'] == 'upcoming') {
                    $values['viewtype'] = 'past';
                    $paginator = Engine_Api::_()->getDbTable('events', 'siteevent')->getSiteeventsPaginator($values);
                } else {
                    $values['viewtype'] = 'upcoming';
                    $paginator = Engine_Api::_()->getDbTable('events', 'siteevent')->getSiteeventsPaginator($values);
                }
            }

            //GET THE NO. OF INVITATION COUNT
            $invite_count = Engine_Api::_()->getDbTable('membership', 'siteevent')->getInviteCount($viewer_id);

            Engine_Api::_()->getApi('Core', 'siteapi')->setView();

            $response['invite_count'] = $invite_count;

            $values['viewtype'] = '';
            $values['showEventType'] = 'all';

            //GET PAGINATOR
            $paginator = Engine_Api::_()->getDbTable('events', 'siteevent')->getSiteeventsPaginator($values);
            $paginator->setItemCountPerPage($this->getRequestParam("limit", 20));
            $paginator->setCurrentPageNumber($this->getRequestParam("page", 1));

            $response['getTotalItemCount'] = $getTotalItemCount = $paginator->getTotalItemCount();

            if (!empty($getTotalItemCount)) {
                foreach ($paginator as $eventObj) {
                    // continue if Deleted member
                    if (empty($eventObj->host_id))
                        continue;
                    $event = $eventObj->toArray();

                    if (!$event['location'])
                        $event['location'] = '';

                    $event['category_name'] = Engine_Api::_()->getItem('siteevent_category', $event['category_id'])->category_name;
                    $occurrence_id = Engine_Api::_()->getDbTable('events', 'siteevent')->getNextOccurID($eventObj->getIdentity());

                    //GET DATES OF EVENT
                    $tz = Engine_Api::_()->getApi('settings', 'core')->core_locale_timezone;
                    if (!empty($viewer_id)) {
                        $tz = $viewer->timezone;
                    }
                    $occurrenceTable = Engine_Api::_()->getDbTable('occurrences', 'siteevent');
                    $dates = $occurrenceTable->getEventDate($eventObj->getIdentity(), $occurrence_id);

                    if (isset($dates['starttime']) && !empty($dates['starttime']) && isset($tz)) {
                        $startDateObject = new Zend_Date(strtotime($dates['starttime']));
                        $startDateObject->setTimezone($tz);
                        $dates['starttime'] = $startDateObject->get('YYYY-MM-dd HH:mm:ss');
                    }
                    if (isset($dates['endtime']) && !empty($dates['endtime']) && isset($tz)) {
                        $endDateObject = new Zend_Date(strtotime($dates['endtime']));
                        $endDateObject->setTimezone($tz);
                        $dates['endtime'] = $endDateObject->get('YYYY-MM-dd HH:mm:ss');
                    }

                    if (!isset($dates) || empty($dates))
                        continue;

                    // Text for multiple date
                    $event = array_merge($event, $dates);
                    $event['isRepeatEvent'] = ($eventObj->isRepeatEvent()) ? 1 : 0;
                    $totalEventOccurrences = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getOccurrenceCount($eventObj->event_id);
                    if (!empty($eventObj->repeat_params) && $totalEventOccurrences > 1) {
                        $event['hasMultipleDates'] = 1;
                    } else {
                        $event['hasMultipleDates'] = 0;
                    }

                    // Text for Payment Status
                    if (Engine_Api::_()->siteevent()->hasPackageEnable()) {
                        if (!$eventObj->getPackage()->isFree()) {
                            $event['paymentStatus'] = $event['paymentStatus'] . $this->translate('Payment: ');
                            if ($eventObj->status == "initial")
                                $event['paymentStatus'] = $this->translate("Not made");
                            elseif ($eventObj->status == "active")
                                $event['paymentStatus'] = $this->translate("Yes");
                            else
                                $event['paymentStatus'] = $this->translate(ucfirst($eventObj->status));
                        }

                        // Text for Expiration Date
                        if (!empty($eventObj->approved_date)) {
                            $event['approveStatus'] = $this->translate('First Approved on ') . $eventObj->approved_date;
                        }

                        $expiry = $eventObj->getExpiryDate();
                        if ($expiry !== "Expired" && $expiry !== $this->translate('Never Expires'))
                            $event['expiryStatus'] = $this->translate("Expiration Date: ") . $expiry;
                        else {
                            $event['expiryStatus'] = $expiry;
                        }
                    }

                    $leaders = Engine_Api::_()->getItem('siteevent_event', $event['event_id'])->getLedBys(false);
                    // Set default ledby.
                    $defaultLedby['title'] = $eventObj->getOwner()->getTitle();
                    $defaultLedby['type'] = $eventObj->getOwner()->getType();
                    $defaultLedby['id'] = $eventObj->getOwner()->getIdentity();
                    $event['ledby'][] = $defaultLedby;

                    // Set array of ledby.
                    foreach ($leaders as $leader) {
                        $tempLeader['title'] = $leader->getOwner()->getTitle();
                        $tempLeader['type'] = $leader->getOwner()->getType();
                        $tempLeader['id'] = $leader->getOwner()->getIdentity();
                        $event['ledby'][] = $tempLeader;
                    }

                    $event['hosted_by'] = '';
                    if (isset($eventObj->host_type) && !empty($eventObj->host_id)) {
                        $organizerObj = Engine_Api::_()->getItem($eventObj->host_type, $eventObj->host_id);
                        $organizer['host_type'] = $eventObj->host_type;
                        $organizer['host_id'] = $organizerObj->getIdentity();
                        $organizer['host_title'] = $organizerObj->getTitle();
                        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($organizerObj, false);
                        $organizer = array_merge($organizer, $getContentImages);
                        $event['hosted_by'] = $organizer;
                    }


                    // Add owner images
                    $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($eventObj, true);
                    $event = array_merge($event, $getContentImages);
                    $event["owner_title"] = $eventObj->getOwner()->getTitle();

                    // Add images 
                    $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($eventObj);
                    $event = array_merge($event, $getContentImages);

                    //GET EXACT LOCATION
                    if ($getLocation == 1 && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1) && !$siteevent->is_online) {

                        //GET LOCATION
                        $value['id'] = $event['event_id'];
                        // $siteeventLocationEvents = Zend_Registry::isRegistered('siteeventLocationEvents') ? Zend_Registry::get('siteeventLocationEvents') : null;
                        $location = Engine_Api::_()->getDbtable('locations', 'siteevent')->getLocation($value);
                        if (isset($location) && is_array($location))
                            $event['location'] = $location->location;
                    }

                    $row = $eventObj->membership()->getRow($viewer);
                    $event['rsvp'] = $row->rsvp;
                    $list = $eventObj->getLeaderList();
                    $leaderRow = $list->get($viewer);

                    $hostText = '';
                    if ($viewer_id == $eventObj->owner_id)
                        $hostText = $viewType == 'upcoming' ? "You are owner." : "You were owner.";
                    if ($leaderRow != null && empty($hostText))
                        $hostText = $viewType == 'upcoming' ? "You are leader." : "You were leader.";
                    if (($viewer_id == (int) $eventObj->host_id) && $event->host_type == 'user' && empty($hostText))
                        $hostText = $viewType == 'upcoming' ? "You are host." : "You were host.";
                    if (!Engine_Api::_()->siteevent()->isTicketBasedEvent() && empty($hostText) && isset($event['rsvp']) && $event['membership_userid'] == $viewer_id)
                        $hostText = $viewType == 'upcoming' ? ($event['rsvp'] == 3 ? "You are invited." : "You are guest.") : ($event->rsvp == 3 ? "You were invited." : "You were guest.");
                    if (empty($hostText))
                        $hostText = 'You like this.';

                    $event['hostText'] = $hostText;

                    // if ($item->owner_id == $viewer_id || $leaderRow != null)
                    $isAllowedView = $eventObj->authorization()->isAllowed($viewer, 'view');
                    $event["allow_to_view"] = empty($isAllowedView) ? 0 : 1;

                    $isAllowedEdit = $eventObj->authorization()->isAllowed($viewer, 'edit');
                    $event["edit"] = empty($isAllowedEdit) ? 0 : 1;

                    $isAllowedDelete = $eventObj->authorization()->isAllowed($viewer, 'delete');
                    $event["delete"] = empty($isAllowedDelete) ? 0 : 1;

                    //profile rsvp form
                    if ($rsvp_form == 1)
                        $event['profile_rsvp_form'] = $this->_getProfileRSVP($eventObj);

                    $tempMenu = array();
                    if ($eventObj->owner_id == $viewer_id || $leaderRow != null) {
                        if ($isAllowedEdit) {
                            $tempMenu[] = array(
                                'label' => $this->translate('Edit Event Details'),
                                'name' => 'edit',
                                'url' => 'advancedevents/edit/' . $eventObj->getIdentity(),
                            );
                        }
                        if ($eventObj->draft == 1 && $isAllowedEdit) {
                            $tempMenu[] = array(
                                'label' => $this->translate('Publish'),
                                'name' => 'publish',
                                'url' => 'advancedevents/publish/' . $eventObj->getIdentity()
                            );
                        }

                        if (_CLIENT_TYPE && ((_CLIENT_TYPE == 'android' && _ANDROID_VERSION >= '1.6.3') || _CLIENT_TYPE == 'ios' && _IOS_VERSION >= '1.5.1')) {
                            if ($isEnabledPackage && Engine_Api::_()->siteeventpaid()->canShowPaymentLink($eventObj->event_id)) {
                                $getOauthToken = Engine_Api::_()->getApi('oauth', 'siteapi')->getAccessOauthToken($viewer);
                                $tempMenu[] = array(
                                    'label' => $this->translate('Make Payment'),
                                    'name' => 'package_payment',
                                    'url' => $getHost . '/' . $baseUrl . "/advancedevents/payment?token=" . $getOauthToken['token'] . "&event_id=" . $eventObj->event_id . "&disableHeaderAndFooter=1"
                                );
                            }
                        }

                        if (empty($eventObj->draft)) {
                            if (!$eventObj->closed && $isAllowedEdit) {
                                $tempMenu[] = array(
                                    'label' => $this->translate('Cancel Event'),
                                    'name' => 'close',
                                    'url' => 'advancedevents/close/' . $eventObj->getIdentity(),
                                    'isClosed' => $eventObj->closed
                                );
                            } else if ($isAllowedEdit) {
                                $tempMenu[] = array(
                                    'label' => $this->translate('Re-Publish '),
                                    'name' => 'close',
                                    'url' => 'advancedevents/close/' . $eventObj->getIdentity(),
                                    'isClosed' => $eventObj->closed
                                );
                            }
                        }

                        if ($isAllowedDelete) {
                            $tempMenu[] = array(
                                'label' => $this->translate('Delete'),
                                'name' => 'delete',
                                'url' => 'advancedevents/delete/' . $eventObj->getIdentity()
                            );
                        }

                        $auth = Engine_Api::_()->authorization()->context;

                        if ($viewType != 'past' && $auth->isAllowed($eventObj, $viewer, "invite") && (!isset($eventObj->rsvp) || $eventObj->rsvp == null || (isset($eventObj->rsvp) && $eventObj->membership_userid == $viewer->getIdentity()))) {

                            $tempMenu[] = array(
                                'label' => $this->translate('Invite Guests'),
                                'name' => 'invite',
                                'url' => 'advancedevents/member/invite/' . $eventObj->getIdentity()
                            );
                        }
                    } elseif (!$eventObj->membership()->isMember($viewer, null)) {
                        $tempMenu[] = array(
                            'label' => $this->translate('Join Event'),
                            'name' => 'join',
                            'url' => 'advancedevents/member/join/' . $eventObj->getIdentity(),
                        );
                    } else if ($eventObj->membership()->isMember($viewer, true)) {
                        $tempMenu[] = array(
                            'label' => $this->translate('Leave Event'),
                            'name' => 'leave',
                            'url' => 'advancedevents/member/leave/' . $eventObj->getIdentity(),
                        );
                    }
                    $event['menu'] = $tempMenu;

                    $tempResponse[] = $event;
                }
                if (!empty($tempResponse))
                    $response['response'] = $tempResponse;
            }
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
        //RESPONSE
        $response['canCreate'] = Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, 'create');
        $response['packagesEnabled'] = $this->_packagesEnabled();
        $this->respondWithSuccess($response, true);
    }

    /**
     * PROFILE OF A EVENT
     * 
     * @return array
     */
    public function viewAction() {
        if (Engine_Api::_()->core()->hasSubject())
            $siteevent = $subject = Engine_Api::_()->core()->getSubject('siteevent_event');

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($subject))
            $this->respondWithError('no_record');

        $event_id = $subject['event_id'];

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $isAllowedView = $subject->authorization()->isAllowed($viewer, 'view');

        $occurrence_id = $this->_getParam('occurrence_id', null);

        $rsvp_form = $this->getRequestParam('rsvp_form', true);

        //GET SETTING
        $showContent = $this->_getParam('showContent', array("memberCount", "viewCount", "likeCount", "commentCount", "tags", "category", "ownerName", "rsvp", "price", "startDate", "endDate", "location"));

        if (Engine_Api::_()->siteevent()->isTicketBasedEvent() && in_array('rsvp', $showContent)) {
            unset($showContent['rsvp']);
        }

        // RETURN IF LOGGED-IN USER NOT AUTHORIZED TO VIEW EVENT.
        if (empty($isAllowedView))
            $this->respondWithError('unauthorized');


        //PREPARE RESPONSE
        $bodyParams = array();

        //Privacy formValues
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('leader', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        $explodeParentType = explode('_', $parent_type);
        if (!empty($explodeParentType) && isset($explodeParentType[0]) && isset($explodeParentType[1])) {
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($explodeParentType[0] . 'member') && ($parent_type == 'sitepage_page' || $parent_type == 'sitebusiness_business' || $parent_type == 'sitegroup_group') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parent_type, 'item_module' => $explodeParentType[0])))) {
                $roles = array('leader', 'member', 'parent_member', 'registered', 'everyone');
            } elseif (($parent_type == 'sitepage_page' || $parent_type == 'sitebusiness_business' || $parent_type == 'sitegroup_group' || $parent_type == 'sitestore_store') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parent_type, 'item_module' => $explodeParentType[0])))) {
                $roles = array('leader', 'member', 'registered', 'everyone');
            } elseif (($parent_type == 'sitereview_listing') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitereview_listing_' . $parentTypeItem->listingtype_id, 'item_module' => 'sitereview')))) {
                $roles = array('leader', 'member', 'registered', 'everyone');
            }
        }

        foreach ($roles as $roleString) {

            $role = $roleString;
            if ($role === 'leader') {
                $role = $leaderList;
            }


            if (1 == $auth->isAllowed($siteevent, $role, "view")) {
                $authValues['auth_view'] = $roleString;
            }

            if (1 == $auth->isAllowed($siteevent, $role, "comment")) {
                $authValues['auth_comment'] = $roleString;
            }
        }
        $ownerList = '';
        $roles_photo = array('leader', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
        $explodeParentType = explode('_', $parent_type);
        if (!empty($explodeParentType) && isset($explodeParentType[0]) && isset($explodeParentType[1])) {
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($explodeParentType[0] . 'member') && ($parent_type == 'sitepage_page' || $parent_type == 'sitebusiness_business' || $parent_type == 'sitegroup_group') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parent_type, 'item_module' => $explodeParentType[0])))) {
                $roles_photo = array('leader', 'member', 'like_member', 'parent_member', 'registered');
                $getContentOwnerList = 'get' . ucfirst($parentTypeItem->getShortType()) . 'OwnerList';
                $ownerList = $parentTypeItem->$getContentOwnerList();
            } elseif (($parent_type == 'sitepage_page' || $parent_type == 'sitebusiness_business' || $parent_type == 'sitegroup_group' || $parent_type == 'sitestore_store') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parent_type, 'item_module' => $explodeParentType[0])))) {
                $roles_photo = array('leader', 'member', 'like_member', 'registered');
                $getContentOwnerList = 'get' . ucfirst($parentTypeItem->getShortType()) . 'OwnerList';
                $ownerList = $parentTypeItem->$getContentOwnerList();
            } elseif (($parent_type == 'sitereview_listing') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitereview_listing_' . $parentTypeItem->listingtype_id, 'item_module' => 'sitereview')))) {
                $roles_photo = array('leader', 'member', 'registered', 'everyone');
            }
        }
        foreach ($roles_photo as $roleString) {

            $role = $roleString;
            if ($role === 'leader') {
                $role = $leaderList;
            }

            if ($role === 'like_member' && $ownerList) {
                $role = $ownerList;
            }

            //Here we change isAllowed function for like privacy work only for populate.
            $siteeventAllow = Engine_Api::_()->getApi('allow', 'siteevent');
            if (1 == $siteeventAllow->isAllowed($siteevent, $role, 'topic')) {
                $authValues['auth_topic'] = $roleString;
            }

            if (1 == $siteeventAllow->isAllowed($siteevent, $role, 'post')) {
                $authValues['auth_post'] = $roleString;
            }
        }

        foreach ($roles_photo as $roleString) {

            $role = $roleString;
            if ($role === 'leader') {
                $role = $leaderList;
            }

            if ($role === 'like_member' && $ownerList) {
                $role = $ownerList;
            }

            //Here we change isAllowed function for like privacy work only for populate.
            $siteeventAllow = Engine_Api::_()->getApi('allow', 'siteevent');

            if (1 == $siteeventAllow->isAllowed($siteevent, $role, 'photo')) {
                $authValues['auth_photo'] = $roleString;
            }
        }

        $videoEnable = Engine_Api::_()->siteevent()->enableVideoPlugin();
        if ($videoEnable) {
            $roles_video = array('leader', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
            $explodeParentType = explode('_', $parent_type);
            if (!empty($explodeParentType) && isset($explodeParentType[0]) && isset($explodeParentType[1])) {
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($explodeParentType[0] . 'member') && ($parent_type == 'sitepage_page' || $parent_type == 'sitebusiness_business' || $parent_type == 'sitegroup_group') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parent_type, 'item_module' => $explodeParentType[0])))) {
                    $roles_video = array('leader', 'member', 'like_member', 'parent_member', 'registered');
                    $getContentOwnerList = 'get' . ucfirst($parentTypeItem->getShortType()) . 'OwnerList';
                    $ownerList = $parentTypeItem->$getContentOwnerList();
                } elseif (($parent_type == 'sitepage_page' || $parent_type == 'sitebusiness_business' || $parent_type == 'sitegroup_group' || $parent_type == 'sitestore_store') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parent_type, 'item_module' => $explodeParentType[0])))) {
                    $roles_video = array('leader', 'member', 'like_member', 'registered');
                    $getContentOwnerList = 'get' . ucfirst($parentTypeItem->getShortType()) . 'OwnerList';
                    $ownerList = $parentTypeItem->$getContentOwnerList();
                } elseif (($parent_type == 'sitereview_listing') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitereview_listing_' . $parentTypeItem->listingtype_id, 'item_module' => 'sitereview')))) {
                    $roles_video = array('leader', 'member', 'registered', 'everyone');
                }
            }
            foreach ($roles_video as $roleString) {

                $role = $roleString;
                if ($role === 'leader') {
                    $role = $leaderList;
                }

                if ($role === 'like_member' && $ownerList) {
                    $role = $ownerList;
                }

                //Here we change isAllowed function for like privacy work only for populate.
                $siteeventAllow = Engine_Api::_()->getApi('allow', 'siteevent');
                if (1 == $siteeventAllow->isAllowed($siteevent, $role, 'video')) {
                    $authValues['auth_video'] = $roleString;
                }
            }
        }


        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventdocument')) {
            foreach ($roles_photo as $roleString) {
                $role = $roleString;
                if ($role === 'leader') {
                    $role = $leaderList;
                }

                if ($role === 'like_member' && $ownerList) {
                    $role = $ownerList;
                }

                //Here we change isAllowed function for like privacy work only for populate.
                $siteeventAllow = Engine_Api::_()->getApi('allow', 'siteevent');
                if (1 == $siteeventAllow->isAllowed($siteevent, $role, 'document')) {
                    $authValues['auth_document'] = $roleString;
                }
            }
        }

        if (Engine_Api::_()->siteevent()->listBaseNetworkEnable()) {
            if (empty($siteevent->networks_privacy)) {
                $authValues['networks_privacy'] = array(0);
            }
        }

        $authValues['auth_invite'] = $auth->isAllowed($siteevent, 'member', 'invite');

        //prepare tags
        $siteeventTags = $siteevent->tags()->getTagMaps();
        $tagString = '';

        foreach ($siteeventTags as $tagmap) {

            if ($tagString != '')
                $tagString .= ', ';
            $tagString .= $tagmap->getTag()->getTitle();
        }

        $tagNamePrepared = $tagString;

        //TO GET OR NOT THE EXACT LOCATION OF EVENT
        $getLocation = $this->_getParam('getLocation', null);

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        // PREPARE RESPONSE ARRAY
        $bodyParams['response'] = $subject->toArray();

        if (isset($bodyParams['response']['body']) && !empty($bodyParams['response']['body']))
            $bodyParams['response']['body'] = strip_tags($bodyParams['response']['body']);

        if (!$bodyParams['response']['location'])
            $bodyParams['response']['location'] = '';

        $bodyParams['response']['guid'] = $subject->getGuid();
        $bodyParams['response']['isowner'] = $subject->isOwner($viewer);

        if (isset($authValues) && !empty($authValues))
            $bodyParams = array_merge($bodyParams, $authValues);

        if (isset($tagString) && !empty($tagString))
            $bodyParams['tags'] = $tagString;

        //SAVE THE OCCURRENCE ID IN THE.
        if (empty($occurrence_id) || !is_numeric($occurrence_id)) {
            //GET THE NEXT UPCOMING OCCURRENCE ID
            $occurrence_id = Engine_Api::_()->getDbTable('events', 'siteevent')->getNextOccurID($this->_getParam('event_id'));
        }

        $bodyParams['response']['occurrence_id'] = $occurrence_id;
        try {
            //GET DATES OF EVENT
            $occurrenceTable = Engine_Api::_()->getDbTable('occurrences', 'siteevent');
            $dates = $occurrenceTable->getEventDate($event_id, $occurrence_id);
            $bodyParams['response'] = array_merge($bodyParams['response'], $dates);
            $bodyParams['response']['isRepeatEvent'] = ($siteevent->isRepeatEvent()) ? 1 : 0;
            $totalEventOccurrences = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getOccurrenceCount($siteevent->event_id);
            if (!empty($siteevent->repeat_params) && $totalEventOccurrences > 1) {
                $bodyParams['response']['hasMultipleDates'] = 1;
            } else {
                $bodyParams['response']['hasMultipleDates'] = 0;
            }

            //get Event status 
            $endDate = strtotime($dates['endtime']);
            $startDate = strtotime($dates['starttime']);
            $currentDate = time();

            $status = $color = '';
            //@todo work for event status.
            $lastOccurrenceEndDate = $occurrenceTable->getOccurenceEndDate($siteevent->event_id, 'DESC');
            $lastOccurrenceEndDate = strtotime($lastOccurrenceEndDate, array('format' => 'M/d/yy h:mm a'));
            $isLastOccurrenceEnd = 1;
            if ($lastOccurrenceEndDate > $currentDate) {
                $isLastOccurrenceEnd = 0;
            }

            $firstOccurrenceStartDate = $occurrenceTable->getOccurenceStartDate($siteevent->event_id, 'ASC');
            $firstOccurrenceStartDate = strtotime($firstOccurrenceStartDate, array('format' => 'M/d/yy h:mm a'));
            $isFirstOccurrenceStart = 1;
            if ($firstOccurrenceStartDate > $currentDate) {
                $isFirstOccurrenceStart = 0;
            }

            $isEventFinished = 0;
            $leftOccurrences = 0;
            if ($endDate < $currentDate) {
                $isEventFinished = 1;

                $next_occurrence_id = Engine_Api::_()->getDbTable('events', 'siteevent')->getNextOccurID($siteevent->event_id);
                $nextOccurrenceDate = $occurrenceTable->getEventDate($siteevent->event_id, $next_occurrence_id);

                $leftOccurrences = $occurrenceTable->getOccurrenceCount($siteevent->event_id, array('upcomingOccurrences' => 1));
            }

            $futureEvent = 0;
            if ($startDate > $currentDate) {
                $futureEvent = 1;
            }

            if ($siteevent->closed) {
                $status = $this->translate("Event has cancelled");
                $color = 'R';
            }
            if ($isEventFinished && empty($siteevent->closed)) {
                if ($isLastOccurrenceEnd || empty($siteevent->repeat_params)) {
                    $status = $this->translate("Event has ended");
                    $color = 'R';
                } elseif ($siteevent->repeat_params) {
                    $this->translate("This occurrence has ended");
                    $color = 'R';
                }
            }
            if ($siteevent->repeat_params && $nextOccurrenceDate['starttime'] && !$isLastOccurrenceEnd && empty($siteevent->closed)) {
                $datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium');
                $date = $this->locale()->toEventDateTime($this->nextOccurrenceDate['starttime'], array('size' => $datetimeFormat));
                $status = $this->translate("Next Occurrence:" . $date);
                $color = 'B';
            }

            if ($futureEvent && empty($siteevent->closed)) {
                if (!$isFirstOccurrenceStart || empty($siteevent->repeat_params)) {
                    $status = $this->translate("Event has not started");
                    $color = 'B';
                } else {
                    $status = $this->translate("This occurrence has not started");
                    $color = 'B';
                }
            } else if (empty($siteevent->closed)) {
                if ($siteevent->repeat_params && empty($isEventFinished) && empty($siteevent->closed)) {
                    $color = 'G';
                    $status = $this->translate("This occurrence is ongoing");
                } else if (empty($isEventFinished)) {
                    $color = 'G';
                    $status = $this->translate("Event is ongoing");
                }
            }

            if ($isEventFull && empty($siteevent->closed)) {
                $color = 'R';
                $status = $this->translate("Event is Full");
            }

            if ($isEventFull && empty($siteevent->closed)) {
                $occurrence = Engine_Api::_()->getItem('siteevent_occurrence', $occurrence_id);
                if (Engine_Api::_()->siteevent()->isTicketBasedEvent()) {
                    
                } elseif (!Engine_Api::_()->siteevent()->isTicketBasedEvent() && $viewer_id && empty($occurrence->waitlist_flag))
                    $endDate = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($siteevent->event_id, 'DESC', $occurrence_id);
                if (null === $siteevent->membership()->getRow($viewer) && strtotime($endDate) > time()) {
                    if ($siteevent->membership()->isResourceApprovalRequired()) {
                        $color = 'G';
                        $status = $this->translate("Request Invite");
                    } else if (strtotime($endDate) > time()) {
                        $color = 'G';
                        $status = $this->translate("Join Event");
                    }
                }
            }

            $isEventFull = $siteevent->isEventFull();
            if (!empty($isEventFull) && _CLIENT_TYPE && ((_CLIENT_TYPE == 'android' && _ANDROID_VERSION >= '1.7.4') || _CLIENT_TYPE == 'ios' && _IOS_VERSION >= '1.5.9')) {
                $status = $this->translate("Event is Full");
                if (!empty($viewer_id)) {
                    $params = array();
                    $params['occurrence_id'] = $occurrence_id;
                    $params['user_id'] = $viewer_id;
                    $params['columnName'] = 'waitlist_id';
                    $inWaitlist = Engine_Api::_()->getDbTable('waitlists', 'siteevent')->getColumnValue($params);
                    if (!empty($inWaitlist)) {
                        $status = $this->translate("You are added to the waitlist.");
                    }
                }
            }

            //end event status work
            if (isset($status) && !empty($status))
                $bodyParams['response']['status'] = $status;

            if (isset($color) && !empty($color))
                $bodyParams['response']['status_color'] = $color;

            if ($viewer->getIdentity()) {
                $is_member = $subject->membership()->isMember($viewer, null);
                $can_upload = $subject->authorization()->isAllowed(null, 'photo');
                $bodyParams['response']['isMember'] = (!empty($is_member)) ? 1 : 0;
                $bodyParams['response']['canUpload'] = (!empty($can_upload)) ? 1 : 0;
            }

            //GETTING CATEGORY and SUBCATEGORY,SUBSUBCATEGORY-if any
            $category_id = $siteevent->category_id;
            if (!empty($category_id)) {

                $bodyparams['categoryname'] = Engine_Api::_()->getItem('siteevent_category', $category_id)->getTitle();

                $subcategory_id = $siteevent->subcategory_id;

                if (!empty($subcategory_id)) {

                    $bodyparams['subcategoryname'] = ucfirst(Engine_Api::_()->getItem('siteevent_category', $subcategory_id)->getTitle());

                    $subsubcategory_id = $siteevent->subsubcategory_id;

                    if (!empty($subsubcategory_id)) {

                        $bodyparams['subsubcategoryname'] = Engine_Api::_()->getItem('siteevent_category', $subsubcategory_id)->getTitle();
                    }
                }
            }

            //GET DATES OF EVENT
            $tz = Engine_Api::_()->getApi('settings', 'core')->core_locale_timezone;
            if (!empty($viewer_id)) {
                $tz = $viewer->timezone;
            } $occurrenceTable = Engine_Api::_()->getDbTable('occurrences', 'siteevent');
            $dates = $occurrenceTable->getEventDate($event_id, $occurrence_id);

            if (isset($dates['starttime']) && !empty($dates['starttime']) && isset($tz)) {
                $startDateObject = new Zend_Date(strtotime($dates['starttime']));
                $startDateObject->setTimezone($tz);
                $bodyParams['response']['starttime'] = $startDateObject->get('YYYY-MM-dd HH:mm:ss');
            }
            if (isset($dates['endtime']) && !empty($dates['endtime']) && isset($tz)) {
                $endDateObject = new Zend_Date(strtotime($dates['endtime']));
                $endDateObject->setTimezone($tz);
                $bodyParams['response']['endtime'] = $endDateObject->get('YYYY-MM-dd HH:mm:ss');
            }

            $host = $siteevent->getHost();

            if (isset($host)) {
                $host_icons = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($host, false);
                $organizer['host_type'] = $host->getType();
                $organizer['host_id'] = $host->getIdentity();
                $organizer['host_title'] = $host->getTitle();
                $organizer['image_icon'] = $host_icons;

                $userEvents = Engine_Api::_()->getDbTable('events', 'siteevent')->userEvent($organizer);
                $organizer['event_hosted'] = count($userEvents);
                $bodyParams['response']['host'] = $organizer;
            }

            $item = Engine_Api::_()->getItem('siteevent_event', $subject->event_id);
            $bodyParams['response']['ledby'] = Engine_Api::_()->getItem('siteevent_event', $subject['event_id'])->getLedBys();

            // Get the rsvp value according to the occurence_id
            $occurrence_id = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurrence($subject->event_id);
            Zend_Registry::set('occurrence_id', $occurrence_id);
            $row = $subject->membership()->getRow($viewer);
            $currentRsvp = $bodyParams['response']['rsvp'] = $row->rsvp;

            // Add Image
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($subject);
            $bodyParams['response'] = array_merge($bodyParams['response'], $getContentImages);

            //ADD OWNER IMAGE
            $bodyParams['response']["owner_title"] = $subject->getOwner()->getTitle();
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($subject, true);
            $bodyParams['response'] = array_merge($bodyParams['response'], $getContentImages);
            $bodyParams['response']['get_attending_count'] = $subject->getAttendingCount();
            $bodyParams['response']['get_maybe_count'] = $subject->getMaybeCount();
            $bodyParams['response']['get_not_attending_count'] = $subject->getNotAttendingCount();
            $bodyParams['response']['get_awaiting_reply_count'] = $subject->getAwaitingReplyCount();

//            //profile rsvp form
            $row = $subject->membership()->getRow($viewer);
            // GETTING THE RSVP.
            if ($this->getRequestParam('rsvp_form', null)) {
                if ($row->active) {
                    $getRSVP = $this->getRequestParam('rsvp', 2);
                    if ($this->getRequest()->isPost() && isset($getRSVP)) {
                        $subject->membership()
                                ->getMemberInfo($viewer)
                                ->setFromArray(array('rsvp' => $getRSVP))
                                ->save();

                        $this->successResponseNoContent('no_content');
                    } else {
                        $bodyParams['profile_rsvp_form'] = $this->_getProfileRSVP($subject);
                    }
                }
            }
            //GET THE GUTTER-MENUS.
            if ($this->getRequestParam('gutter_menu', true))
                $bodyParams['gutterMenu'] = $this->_gutterMenus($subject);

            // SET THE ANNOUNCEMENT IN RESPONSE
            if ($this->getRequestParam('announcement', false) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.announcement', 1)) {
                $announcementArray = array();
                $announcementLimit = 3;
                $fetchColumns = array('announcement_id', 'title', 'body');
                $announcements = Engine_Api::_()->getDbtable('announcements', 'siteevent')->announcements($siteevent->event_id, 0, $announcementLimit, $fetchColumns);
                if (COUNT($announcements)) {
                    $announcementArray['announcementCount'] = COUNT($announcements);
                    $announcementArray['announcementCreate'] = $this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "edit")->isValid();
                    $announcementArray['canDelete'] = $this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "delete")->isValid();
                    foreach ($announcements as $item) {
                        $announcement = $item->toArray();
                        if (isset($announcement['body']) && !empty($announcement['body']))
                            $announcement['body'] = strip_tags($announcement['body']);
                        $announcementArray['announcements'][] = $announcement;
                    }
                }

                $bodyParams['announcement'] = $announcementArray;
            }

            //GET THE EVENT PROFILE TABS.
            if ($this->getRequestParam('profile_tabs', true))
                $bodyParams['profile_tabs'] = $this->_profileTAbsContainer($subject);

            //GET EXACT LOCATION
            if ($getLocation == 1 && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1) && !$siteevent->is_online) {
                //GET LOCATION
                $location = Engine_Api::_()->getDbtable('locations', 'siteevent')->getLocation($event_id);
                if ($location) {
                    $bodyParams['response']['location'] = $location->toArray();
                }
            }

            // Increment view count
            if (!$subject->getOwner()->isSelf($viewer)) {
                $subject->view_count++;
                $subject->save();
            }
        } catch (Exception $ex) {
            $this->respondWithError('internal_server_error');
        }

        $this->respondWithSuccess($bodyParams);
    }

    /**
     * Getting the "Gutter Menus" array.
     * 
     * @return array
     */
    private function _gutterMenus($subject) {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $owner = $subject->getOwner();
        $menus = array();

        //GET USER LEVEL ID
        if ($viewer->getIdentity()) {
            $level_id = $viewer->level_id;
            $viewer_id = $viewer->getIdentity();
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $siteevent = $subject;

        if ($subject->authorization()->isAllowed($viewer, 'invite')) {

            $occure_id = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurrence($subject->event_id);

            //CHECK IF THE EVENT IS PAST EVENT THEN ALSO DO NOT SHOW THE INVITE AND PROMOTE LINK
            $endDate = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($subject->event_id, 'DESC', $occure_id);

            // $currentDate = $this->locale()->toEventDateTime(time());
            if (strtotime($endDate) > time()) {
                $menus[] = array(
                    'name' => 'invite',
                    'label' => $this->translate('Invite Guests'),
                    'url' => 'advancedevents/member/invite/' . $subject->getIdentity() . '/' . $occure_id,
                );
            }
        }

        // EDIT EVENT DETAILS DASHBOARD
        if ($viewer->getIdentity() && $subject->authorization()->isAllowed($viewer, 'edit')) {
            $menus[] = array(
                'name' => 'edit',
                'label' => $this->translate('Edit Event Details'),
                'url' => 'advancedevents/edit/' . $subject->getIdentity(),
            );

            if (_CLIENT_TYPE && ((_CLIENT_TYPE == 'android' && _ANDROID_VERSION >= '1.7.4') || (_CLIENT_TYPE == 'ios' && _IOS_VERSION >= '1.5.9')) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.waitlist', 1)) {
                $menus[] = array(
                    'name' => 'capacity_waitlist',
                    'label' => $this->translate('Capacity & Waitlist'),
                    'url' => 'advancedevents/capacity-and-waitlist/' . $subject->getIdentity(),
                );
            }
        }

        if (!empty($viewer_id)) {
            $menus[] = array(
                'name' => 'share',
                'label' => $this->translate('Share This Event'),
                'url' => 'activity/share',
                'urlParams' => array(
                    "type" => $subject->getType(),
                    "id" => $subject->getIdentity()
                )
            );
        }

        $videoTable = Engine_Api::_()->getDbtable('videos', 'siteevent');
        //TOTAL VIDEO COUNT FOR THIS EVENT
        $counter = $videoTable->getEventVideoCount($subject->getIdentity());
        $allowed_upload_video = Engine_Api::_()->siteevent()->allowVideo($subject, $viewer, $counter);
        if ($allowed_upload_video) {
            $menus[] = array(
                'name' => 'videoCreate',
                'label' => $this->translate('Add Video'),
                'url' => 'advancedevents/video/create/' . $subject->getIdentity(),
            );
        }

        //SHOW MESSAGE OWNER LINK TO USER IF MESSAGING IS ENABLED FOR THIS LEVEL
        if (isset($viewer->level_id) && !empty($viewer->level_id)) {
            $showMessageOwner = 0;
            $showMessageOwner = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'auth');

            if ($showMessageOwner != 'none') {
                $showMessageOwner = 1;
            }

            //SHOW IF AUTHORIZED
            if ($siteevent->owner_id !== $viewer_id && !empty($viewer_id) && !empty($showMessageOwner)) {

                if (!empty($viewer_id)) {

                    $menus[] = array(
                        'name' => 'messageowner',
                        'label' => $this->translate('Message Owner'),
                        'url' => 'advancedevents/messageowner/' . $subject->getIdentity()
                    );
                }
            }
        }

        //TELL A FRIEND
        $menus[] = array(
            'name' => 'tellafriend',
            'label' => $this->translate('Tell a friend'),
            'url' => 'advancedevents/tellafriend/' . $subject->getIdentity()
        );

        //PUBLISH EVENT
        if ($siteevent->draft == 1 && ($viewer_id == $siteevent->owner_id)) {
            $menus[] = array(
                'name' => 'publish',
                'label' => $this->translate('Publish'),
                'url' => 'advancedevents/publish/' . $subject->getIdentity(),
            );
        }

        //SHOW IF AUTHORIZED
        if ($viewer_id == $siteevent->owner_id && empty($siteevent->draft)) {

            if (!empty($siteevent->closed)) {
                $label = Zend_Registry::get('Zend_Translate')->_('Re-publish Event');
            } else {
                $label = Zend_Registry::get('Zend_Translate')->_('Cancel Event');
            }
            $menus[] = array(
                'name' => 'close',
                'label' => $label,
                'url' => 'advancedevents/close/' . $subject->getIdentity(),
                'isclosed' => $siteevent->closed
            );

            $menus[] = array(
                'name' => 'notification_settings',
                'label' => $this->translate('Notification Settings'),
                'url' => 'advancedevents/notifications/' . $subject->getIdentity(),
            );
        }

        $can_delete = $siteevent->authorization()->isAllowed(null, "delete");

        //AUTHORIZATION CHECK
        if (!empty($can_delete) && !empty($viewer_id)) {

            $menus[] = array(
                'name' => 'delete',
                'label' => $this->translate('Delete'),
                'url' => 'advancedevents/delete/' . $subject->getIdentity(),
            );
        }

        if (!empty($viewer_id)) {
            $menus[] = array(
                'name' => 'report',
                'label' => $this->translate('Report This Event'),
                'url' => 'report/create/subject/' . $subject->getGuid(),
                'urlParams' => array(
                    "type" => $subject->getType(),
                    "id" => $subject->getIdentity()
                )
            );
        }

        $isEnabledPackage = Engine_Api::_()->siteevent()->hasPackageEnable();
        $getHost = Engine_Api::_()->getApi('core', 'siteapi')->getHost();
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $baseUrl = @trim($baseUrl, "/");

        if (_CLIENT_TYPE && ((_CLIENT_TYPE == 'android' && _ANDROID_VERSION >= '1.6.3') || _CLIENT_TYPE == 'ios' && _IOS_VERSION >= '1.5.1')) {
            if (isset($viewer_id) && !empty($viewer_id) && isset($subject->owner_id) && !empty($subject->owner_id) && $viewer_id == $subject->owner_id) {
                if ($isEnabledPackage && Engine_Api::_()->siteeventpaid()->canShowPaymentLink($subject->event_id)) {
                    $getOauthToken = Engine_Api::_()->getApi('oauth', 'siteapi')->getAccessOauthToken($viewer);
                    $menus[] = array(
                        'label' => $this->translate('Make Payment'),
                        'name' => 'package_payment',
                        'url' => $getHost . '/' . $baseUrl . "/advancedevents/payment?token=" . $getOauthToken['token'] . "&event_id=" . $subject->event_id . "&disableHeaderAndFooter=1"
                    );
                }
            }
        }

        $enabledDiary = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.diary', 1);
        //AUTHORIZATION CHECK
        if ($viewer->getIdentity() && empty($siteevent->draft) && !empty($siteevent->search) && !empty($siteevent->approved) && $enabledDiary) {

            //GET USER LEVEL ID
            if (!empty($viewer_id)) {
                $level_id = $viewer->level_id;
            } else {
                $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
            }

            //GET LEVEL SETTING
            $can_create = Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_diary', "create");
            if (!empty($can_create)) {

                //AUTHORIZATION CHECK
                if (Engine_Api::_()->authorization()->isAllowed('siteevent_diary', $viewer, 'view')) {

                    $menus[] = array(
                        'name' => 'diary',
                        'label' => $this->translate('Add To Diary'),
                        'url' => 'advancedevents/diaries/add',
                        'urlParams' => array(
                            "event_id" => $subject->getIdentity()
                        )
                    );
                }
            }

            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) != 1) {

                //GET VIEWER   
                $viewer_id = $viewer->getIdentity();
                $create_review = ($siteevent->owner_id == $viewer_id) ? Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.allowownerreview', 1) : 1;
                if (!empty($create_review)) {
                    if (Engine_Api::_()->siteevent()->allowReviewCreate($siteevent)) {
                        //Check event is end or not
                        $endDate = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($siteevent->event_id);
                        $currentDate = date('Y-m-d H:i:s');
                        $endDate = strtotime($endDate);
                        $currentDate = strtotime($currentDate);
                        $ratingTable = Engine_Api::_()->getDbtable('ratings', 'siteevent');
                        $reviewTable = Engine_Api::_()->getDbTable('reviews', 'siteevent');
                        $authorizationApi = Engine_Api::_()->authorization();
                        $create_level_allow = $authorizationApi->getPermission($level_id, 'siteevent_event', "review_create");
                        //SET HAS POSTED
                        if (empty($viewer_id)) {
                            $hasPosted = $hasPosted = 0;
                        } else {
                            $params = array();
                            $params['resource_id'] = $siteevent->event_id;
                            $params['resource_type'] = $siteevent->getType();
                            $params['viewer_id'] = $viewer_id;
                            $params['type'] = 'user';
                            $hasPosted = $reviewTable->canPostReview($params);
                        }
                        $can_reply = Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "review_reply");
                        $can_update = Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "review_update");
                        $rateuser = Engine_Api::_()->getDbTable("categories", "siteevent")->isGuestReviewAllowed($siteevent->category_id);

                        if (!empty($hasPosted) && !empty($can_update)) {
                            $menus[] = array(
                                'name' => 'updateReview',
                                'label' => $this->translate('Update Review'),
                                'url' => 'advancedevents/review/update/' . $siteevent->getIdentity(),
                                'urlParams' => array(
                                    "review_id" => $hasPosted
                                )
                            );
                        } else if (empty($hasPosted) && !empty($create_level_allow)) {
                            if ($endDate < $currentDate && empty($rateuser)) {
                                $menus[] = array(
                                    'name' => 'createReview',
                                    'label' => $this->translate('Write Review'),
                                    'url' => 'advancedevents/review/create/' . $subject->getIdentity(),
                                );
                            }
                        }
                    }
                }
            }
        }
        if ($viewer->getIdentity()) {
            $occurrence_id = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurrence($subject->event_id);
            Zend_Registry::set('occurrence_id', $occurrence_id);
            $row = $subject->membership()->getRow($viewer);

            //CHECK IF THE EVENT IS PAST EVENT THEN WE WILL NOT SHOW JOIN OR REQUEST INVITE LINK EVENT LINK.
            $endDate = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($subject->event_id, 'DESC', $occurrence_id);
            $isEventFull = $subject->isEventFull(array('occurrence_id' => $occure_id));
//        TODO TICKET BASED EVENT
//        if(Engine_Api::_()->siteevent()->isTicketBasedEvent() && Engine_Api::_()->siteeventticket()->bookNowButton($subject) && ($subject->isRepeatEvent() || (!$subject->isRepeatEvent() && !$isEventFull))){
//            return array(
//                'label' => 'Book Now',
//                'class' => 'buttonlink icon_siteevents_tickets',
//                'route' => 'siteeventticket_ticket',
//                'params' => array(
//                    'action' => 'buy',
//                    'event_id' => $subject->getIdentity(),
//                    'occurrence_id' => $occure_id,
//                ),
//            );
//        }        

            $occurrence = Engine_Api::_()->getItem('siteevent_occurrence', $occure_id);
            //@todo paid extension advanced event
            //if (!Engine_Api::_()->siteevent()->isTicketBasedEvent()) {
            // Not yet associated at all
            $isEventFull = $siteevent->isEventFull();
            if (!empty($isEventFull) && _CLIENT_TYPE && ((_CLIENT_TYPE == 'android' && _ANDROID_VERSION >= '1.7.4') || _CLIENT_TYPE == 'ios' && _IOS_VERSION >= '1.5.9')) {
                $status = $this->translate("Event is Full");
                if (!empty($viewer_id)) {
                    $params = array();
                    $params['occurrence_id'] = $occurrence_id;
                    $params['user_id'] = $viewer_id;
                    $params['columnName'] = 'waitlist_id';
                    $inWaitlist = Engine_Api::_()->getDbTable('waitlists', 'siteevent')->getColumnValue($params);
                    if (empty($inWaitlist) && ($viewer_id != $siteevent->owner_id)) {
                        $menus[] = array(
                            'name' => 'join-waitlist',
                            'label' => $this->translate('Add me to waitlist'),
                            'url' => 'advancedevents/waitlist/join/' . $subject->getIdentity(),
                        );
                    }
                }
            } else if (null === $row && !$isEventFull && empty($occurrence->waitlist_flag)) {
                if (strtotime($endDate) > time()) {
                    if ($subject->membership()->isResourceApprovalRequired()) {
                        $menus[] = array(
                            'name' => 'request_invite',
                            'label' => $this->translate('Request Invite'),
                            'url' => 'advancedevents/member/request/' . $subject->getIdentity(),
                        );
                    } else {
                        $menus[] = array(
                            'name' => 'join',
                            'label' => $this->translate('Join Event'),
                            'url' => 'advancedevents/member/join/' . $subject->getIdentity(),
                        );
                    }
                }
            }
            // Full member
            // @todo consider owner
            else if ($row->active) {
                //if (!$subject->isOwner($viewer)) {
                $menus[] = array(
                    'name' => 'leave',
                    'label' => $this->translate('Leave Event'),
                    'url' => 'advancedevents/member/leave/' . $subject->getIdentity(),
                );
                // }
            } else if (!$row->resource_approved && $row->user_approved) {
                $menus[] = array(
                    'name' => 'cancel_invite',
                    'label' => $this->translate('Cancel Invite Request'),
                    'url' => 'advancedevents/member/cancel/' . $subject->getIdentity(),
                );
            } else if (!$row->user_approved && $row->resource_approved) {

                $acceptinvite_array = array(
                    'name' => 'accept_invite',
                    'label' => $this->translate('Accept Event Invite'),
                    'url' => 'advancedevents/member/accept/' . $subject->getIdentity(),
                );

                $ignoreinvite_array = array(
                    'name' => 'ignore_invite',
                    'label' => $this->translate('Ignore Event Invite'),
                    'url' => 'advancedevents/member/reject/' . $subject->getIdentity(),
                );

                if (strtotime($endDate) > time()) {
                    $menus[] = $acceptinvite_array;
                    $menus[] = $ignoreinvite_array;
                } else {
                    $menus[] = $ignoreinvite_array;
                }
            }
            //  }
        }
        return $menus;
    }

    /**
     * Get the list of container tabs.
     * 
     * @return array
     */
    private function _profileTAbsContainer($subject) {
        $response[] = array(
            'name' => 'update',
            'label' => $this->translate('Updates'),
        );

        $hasOverview = true;
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.overview', 1)) {
            $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'siteevent');
            $overview = $tableOtherinfo->getColumnValue($subject->getIdentity(), 'overview');
            $hasOverview = !empty($overview);
        }
        if ($hasOverview) {

            $response[] = array(
                'label' => $this->translate('Overview'),
                'name' => 'overview',
                'url' => 'advancedevents/description/' . $subject->getIdentity()
            );
        } else if (isset($subject->body) && !empty($subject->body)) {
            $response[] = array(
                'label' => $this->translate('Description'),
                'name' => 'description',
                'url' => 'advancedevents/description/' . $subject->getIdentity()
            );
        }
        if ($subject->member_count > 0) {
            $response[] = array(
                'name' => 'members',
                'label' => $this->translate('Guests'),
                'totalItemCount' => $subject->member_count,
                'url' => 'advancedevents/member/list/' . $subject->getIdentity(),
                'urlParams' => array(
                )
            );
        }

        if (isset($subject->profile_type) && !empty($subject->profile_type)) {
            $getProfileInfo = Engine_Api::_()->getApi('Siteapi_Core', 'siteevent')->getInformation($subject);
            if (count($getProfileInfo) > 0) {
                $response[] = array(
                    'name' => 'information',
                    'label' => $this->translate('Information'),
                    'url' => 'advancedevents/information/' . $subject->getIdentity()
                );
            }
        }
        if ($subject->getSingletonAlbum()->getCollectiblesPaginator()->getTotalItemCount() > 0) {
            $response[] = array(
                'name' => 'photos',
                'label' => $this->translate('Photos'),
                'totalItemCount' => $subject->getSingletonAlbum()->getCollectiblesPaginator()->getTotalItemCount(),
                'url' => 'advancedevents/photo/list/' . $subject->getIdentity(),
            );
        }
        // Get paginator
//        $table = Engine_Api::_()->getItemTable('group_topic');
//        $select = $table->select()
//                ->where('group_id = ?', $subject->getIdentity())
//                ->order('sticky DESC')
//                ->order('modified_date DESC');
//
//        $paginator = Zend_Paginator::factory($select);
//
//        $response[] = array(
//            'name' => 'discussion',
//            'label' => 'Discussions',
//            'totalItemCount' => $paginator->getTotalItemCount()
//        );
        //VIDEO TABLE
        $videoTable = Engine_Api::_()->getDbtable('videos', 'siteevent');

        //TOTAL VIDEO COUNT FOR THIS EVENT
        $counter = $videoTable->getEventVideoCount($subject->event_id);
        if ($counter > 0) {
            $response[] = array(
                'name' => 'video',
                'label' => $this->translate('Videos'),
                'totalItemCount' => $counter,
                'url' => 'advancedevents/videos/' . $subject->getIdentity()
            );
        }


        $params['resource_type'] = 'siteevent_event';
        $params['event_id'] = $subject->event_id;
        $reviewTable = Engine_Api::_()->getDbTable('reviews', 'siteevent');
        $paginator = $reviewTable->getReviewsPaginator($params, $customFieldValues);
        //GET TOTAL REVIEWS
        $totalReviews = $paginator->getTotalItemCount();
        if ($totalReviews > 0) {
            $response[] = array(
                'name' => 'reviews',
                'label' => $this->translate('User Reviews'),
                'url' => 'advancedevents/reviews/browse',
                'urlParams' => array(
                    'event_id' => $subject->event_id
                ),
                'totalItemCount' => $totalReviews
            );
        }


//        $response[] = array(
//            'name' => 'map',
//            'label' => 'Map',
//            'urlParams' => array(
//                'event_id' => $subject->event_id
//            )
//        );

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.announcement', 1)) {
            $fetchColumns = array('announcement_id', 'title', 'body');
            $announcements = Engine_Api::_()->getDbtable('announcements', 'siteevent')->announcements($subject->event_id, 0, $limit, $fetchColumns);
            $childCount = count($announcements);

            if ($childCount > 0) {
                $response[] = array(
                    'name' => 'announcement',
                    'label' => $this->translate('Announcement'),
                    'url' => 'advancedevents/announcement/' . $subject->getIdentity(),
                    'totalItemCount' => $childCount
                );
            }
        }

        if ($subject->isRepeatEvent()) {
            $response[] = array(
                'name' => 'occurence_index',
                'label' => $this->translate('Occurrences'),
                'url' => 'siteeventrepeat/index/' . $subject->getIdentity()
            );
            $response[] = array(
                'name' => 'occurence_info',
                'label' => $this->translate('Info'),
                'url' => 'siteeventrepeat/info/' . $subject->getIdentity()
            );
        }

        return $response;
    }

    /**
     * Getting the event profile page rsvp form array.
     * 
     * @return array
     */
    private function _getProfileRSVP() {
        $rsvpForm = array();
        $rsvpForm[] = array(
            'type' => 'Select',
            'name' => 'rsvp',
            'multiOptions' => array(
                2 => $this->translate('Attending'),
                1 => $this->translate('Maybe Attending'),
                0 => $this->translate('Not Attending'),
            //3 => 'Awaiting Reply',
            )
        );

        $rsvpForm[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => $this->translate('Submit')
        );

        return $rsvpForm;
    }

    /**
     * Delete the Event.
     * 
     * @return array
     */
    public function deleteAction() {
        // Validate request methods
        $this->validateRequestMethod('DELETE');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        //GET EVENT ID AND OBJECT
        $event_id = $this->_getParam('event_id');


        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        // RETURN IF NO SUBJECT AVAILABLE.
        if (empty($siteevent))
            $this->respondWithError('no_record');

        // GET LOGGED-IN USER LEVEL ID.
        if (!empty($viewer_id))
            $level_id = $viewer->level_id;

        if (!empty($level_id)) {
            $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
            $allowToDelete = $permissionsTable->getAllowed('event', $level_id, 'delete');
        }

        // RETURN IF LOGGED-IN USER NOT AUTHORIZED TO DELETE EVENT.
        if (empty($allowToDelete))
            $this->respondWithError('unauthorized');

        $db = $siteevent->getTable()->getAdapter();
        $db->beginTransaction();
        try {
            $siteevent->delete();
            $db->commit();

            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    //ACTION FOR PUBLISH EVENT
    public function publishAction() {

        //CHECK METHOD
        $this->validateRequestMethod('POST');

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        //GET EVENT ID AND OBJECT
        $event_id = $this->_getParam('event_id');

        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if (empty($siteevent))
            $this->respondWithError('no_record');

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "edit")->isValid()) {
            $this->respondWithError('unauthorized');
        }

        //ONLY OWNER CAN PUBLISH THE EVENT
        if ($viewer_id == $siteevent->owner_id || $viewer->level_id == 1) {

            $db = Engine_Api::_()->getDbtable('events', 'siteevent')->getAdapter();
            $db->beginTransaction();
            try {

                if (!empty($_POST['search'])) {
                    $siteevent->search = 1;
                } else {
                    $siteevent->search = 0;
                }

                $siteevent->modified_date = new Zend_Db_Expr('NOW()');
                $siteevent->draft = 0;
                $siteevent->save();
                $db->commit();
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollback();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        } else {
            $this->respondWithError('unauthorized');
        }
    }

    //ACTION FOR CLOSE / OPEN EVENT
    public function closeAction() {

        //CHECK METHOD
        $this->validateRequestMethod('POST');

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');


        //GET EVENT ID AND OBJECT
        $event_id = $this->_getParam('event_id');
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if (empty($siteevent))
            $this->respondWithError('no_record');


        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //TICKET CHECK - IF ATLEAST ONE GUEST & EVENT NOT FINISHED THEN EVENT CANNOT BE DELETED.
//        if (Engine_Api::_()->siteevent()->hasTicketEnable()){
//          $hasEventTicketGuest = Engine_Api::_()->getDbTable('orders', 'siteeventticket')->hasEventTicketGuest($siteevent, $viewer);
//          $lastOccurrenceEndtime = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($event_id, 'DESC'); 
//          $current_date = date('Y-m-d H:i:s');
//          if($hasEventTicketGuest && $lastOccurrenceEndtime >= $current_date){
//            $canNotCancelMessage = true;
//          }
//        }
        //end
        //
      
        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "edit")->isValid()) {
            $this->respondWithError('unauthorized');
        }



        //ONLY OWNER CAN PUBLISH THE EVENT
        if ($viewer_id == $siteevent->owner_id || $viewer->level_id == 1) {
            $db = Engine_Api::_()->getDbtable('events', 'siteevent')->getAdapter();
            $db->beginTransaction();
            try {

                if (!$siteevent->closed) {
                    $emailType = 'SITEEVENT_EVENT_CANCELED';
                    $defaultMessage = Zend_Registry::get('Zend_Translate')->_('Event owner did not mention any reason while canceling the event.');
                } elseif ($siteevent->closed) {
                    $emailType = 'SITEEVENT_EVENT_PUBLISHED';
                    $defaultMessage = Zend_Registry::get('Zend_Translate')->_('Event owner did not mention any reason while publishing the event.');
                }
//TODO Email work
                if (isset($_POST['email']) && $_POST['email'] == 1) {
                    $message = $_POST['reason'];
                    $select = $siteevent->membership()->getMembersObjectSelect();
                    $members = Engine_Api::_()->getDbTable('users', 'user')->fetchAll($select);
                    foreach ($members as $member) {
                        Engine_Api::_()->getApi('mail', 'core')->sendSystem($member->email, $emailType, array(
                            'event_title' => $siteevent->title,
                            'event_message' => !empty($message) ? $message : $defaultMessage,
                            'event_link' => '<a href="' . 'http://' . $_SERVER['HTTP_HOST'] .
                            Zend_Controller_Front::getInstance()->getRouter()->assemble(array('event_id' => $siteevent->getIdentity(), 'slug' => $siteevent->getSlug()), "siteevent_entry_view", true) . '"  >' . 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('event_id' => $siteevent->getIdentity(), 'slug' => $siteevent->getSlug()), "siteevent_entry_view", true) . ' </a>',
                            'email' => $siteevent->getOwner()->email,
                            'queue' => true
                        ));
                    }
                }

                $siteevent->modified_date = new Zend_Db_Expr('NOW()');
                $siteevent->closed = !$siteevent->closed;
                $siteevent->save();
                $db->commit();
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollback();
                $this->respondWithError('internal_server_error', $e->getMessage());
            }
        } else {
            $this->respondWithError('unauthorized');
        }
    }

    //RETURNS EXACT LOCATION
    public function mapAction() {
        $this->validateRequestMethod('GET');

        $event_id = $this->_getParam('event_id');
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if (empty($siteevent))
            $this->respondWithError('no_record');

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1) || $siteevent->is_online) {
            $this->respondWithError('unauthorized');
        }
        //GET LOCATION
        $value['id'] = $siteevent->getIdentity();
        // $siteeventLocationEvents = Zend_Registry::isRegistered('siteeventLocationEvents') ? Zend_Registry::get('siteeventLocationEvents') : null;
        $location = Engine_Api::_()->getDbtable('locations', 'siteevent')->getLocation($value);


        //DONT RENDER IF LOCAITON IS EMPTY
        if (!empty($location)) {
            $location = $location->toArray();
            $response['response'] = $location;
            $this->respondWithSuccess($response, true);
        }
    }

    //ACTION FOR TELL A FRIEND ABOUT EVENT
    public function tellafriendAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET FORM
        if ($this->getRequest()->isGet()) {
            $response = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->getTellAFriendForm();
            $this->respondWithSuccess($response, true);
        } else if ($this->getRequest()->isPost()) {
            //FORM VALIDATION
            //GET EVENT ID AND OBJECT
            $event_id = $this->_getParam('event_id', $this->_getParam('event_id', null));
            $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
            if (empty($siteevent))
                $this->respondWithError('no_record');
            //GET FORM VALUES
            $values = $this->_getAllParams();
            $errorMessage = array();

            if (empty($values['sender_email']) && !isset($values['sender_email']))
                $errorMessage[] = $this->translate("Your Email field is required");

            if (empty($values['sender_name']) && !isset($values['sender_name']))
                $errorMessage[] = $this->translate("Your Name field is required");

            if (empty($values['message']) && !isset($values['message']))
                $errorMessage[] = $this->translate("Message field is required");

            if (empty($values['receiver_emails']) && !isset($values['receiver_emails']))
                $errorMessage[] = $this->translate("To field is required");

            if (isset($errorMessage) && count($errorMessage) > 0)
                $this->respondWithValidationError('validation_fail', $errorMessage);

            //EXPLODE EMAIL IDS
            $reciver_ids = explode(',', $values['receiver_emails']);
            if (!empty($values['send_me'])) {
                $reciver_ids[] = $values['sender_email'];
            }
            $sender_email = $values['sender_email'];
            $heading = $siteevent->title;

            //CHECK VALID EMAIL ID FORMAT
            $validator = new Zend_Validate_EmailAddress();
            $validator->getHostnameValidator()->setValidateTld(false);
            $errorMessage = array();

            if (!$validator->isValid($sender_email)) {
                $errorMessage[] = $this->translate('Invalid sender email address value');
                $this->respondWithValidationError('validation_fail', $errorMessage);
            }
            $errorMessage = array();
            foreach ($reciver_ids as $receiver_id) {
                $receiver_id = trim($receiver_id, ' ');
                ($reciver_ids);
                if (!$validator->isValid($receiver_id)) {
                    $errorMessage[] = $this->translate('Please enter correct email address of the receiver(s).');
                    $this->respondWithValidationError('validation_fail', $errorMessage);
                }
            }
            $slug_singular = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.slugsingular', 'event-item');
            $objectLink = "/" . $slug_singular . '/view/' . $event_id . '/' . $siteevent->getSlug();
            $sender = $values['sender_name'];
            $message = $values['message'];
            try {
                Engine_Api::_()->getApi('mail', 'core')->sendSystem($reciver_ids, 'SITEEVENT_TELLAFRIEND_EMAIL', array(
                    'host' => $_SERVER['HTTP_HOST'],
                    'sender' => $sender,
                    'heading' => $heading,
                    'message' => '<div>' . $message . '</div>',
                    'object_link' => $objectLink,
                    'email' => $sender_email,
                    'queue' => true
                ));
            } catch (Exception $ex) {
                $this->respondWithError('internal_server_error', $ex->getMessage());
            }
            $this->successResponseNoContent('no_content', true);
        }
    }

//ACTION FOR MESSAGING THE EVENT OWNER
    public function messageownerAction() {

        //LOGGED IN USER CAN SEND THE MESSAGE
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET EVENT ID AND OBJECT
        $event_id = $this->_getParam("event_id");
        if (!empty($event_id)) {
            $event = Engine_Api::_()->getItem('siteevent_event', $event_id);
            if (empty($event))
                $this->respondWithError('no_record');
        }
        else {
            $this->respondWithError('parameter_missing');
        }
        //OWNER CANT SEND A MESSAGE TO HIMSELF
        //GET THE ORGANIZER ID TO WHOM THE MESSAGE HAS TO BE SEND
        $organizer_id = $this->_getParam("host_id");

        $leader_id = 0;
        if (empty($organizer_id)) {
            $leader_id = $organizer_id = $this->_getParam("leader_id");
        }

        if ($viewer_id == $organizer_id) {
            $this->respondWithError('unauthorized');
        }

        if (empty($organizer_id)) {
            $organizer_id = $event->owner_id;
        }
        if ($this->getRequest()->isGet()) {
            $response = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->getMessageOwnerForm();
            $this->respondWithSuccess($response, true);
        } else if ($this->getRequest()->isPost()) {
            $values = $this->_getAllParams();

            //CHECK METHOD/DATA
            Engine_Api::_()->getApi('Core', 'siteapi')->setView();
            $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
            $db->beginTransaction();

            try {

                $is_error = 0;
                if (empty($values['title'])) {
                    $is_error = 1;
                }

                //SENDING MESSAGE
                if ($is_error == 1) {
                    $this->respondWithError('Subject is required field !');
                }

                $recipients = preg_split('/[,. ]+/', $organizer_id);

                //LIMIT RECIPIENTS IF IT IS NOT A SPECIAL SITEEVENT OF MEMBERS
                $recipients = array_slice($recipients, 0, 1000);

                //CLEAN THE RECIPIENTS FOR REPEATING IDS
                $recipients = array_unique($recipients);

                $user = Engine_Api::_()->getItem('user', $organizer_id);

                $event_title = $event->title;
                $http = _ENGINE_SSL ? 'https://' : 'http://';
                $event_title_with_link = '<a href =' . $http . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('event_id' => $event_id, 'slug' => $event->getSlug()), "siteevent_entry_view") . ">$event_title</a>";
                $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send($viewer, $recipients, $values['title'], $values['body'] . "<br><br>" . $this->translate('This message corresponds to the Event: ' . $event_title_with_link));

                try {
                    Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $conversation, 'message_new');
                    //INCREMENT MESSAGE COUNTER
                    Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');
                } catch (Exception $e) {
                    //todo notification error
                    //Blank Exception  
                }
                //INCREMENT MESSAGE COUNTER
                Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

                $db->commit();
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithError('internal_server_error', $e->getMessage());
            }
        }
    }

    public function informationAction() {
        // VALIDATE REQUEST METHOD
        $this->validateRequestMethod();
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET EVENT ID AND OBJECT
        $event_id = $this->_getParam('event_id');
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        if (empty($siteevent) && !isset($siteevent))
            $this->respondWithError('no_record');

        $getProfileInfo = Engine_Api::_()->getApi('Siteapi_Core', 'siteevent')->getInformation($siteevent);

        $this->respondWithSuccess($getProfileInfo, true);
    }

    public function descriptionAction() {

        // VALIDATE REQUEST METHOD
        $this->validateRequestMethod();

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();


        //GET EVENT ID AND OBJECT
        $event_id = $this->_getParam('event_id');
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        if (empty($siteevent) && !isset($siteevent))
            $this->respondWithError('no_record');

        $hasOverview = true;
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.overview', 1)) {
            $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'siteevent');
            $overview = strip_tags($tableOtherinfo->getColumnValue($siteevent->getIdentity(), 'overview'));
            $hasOverview = !empty($overview);
        }
        if ($hasOverview)
            $this->respondWithSuccess($overview, true);

        else if (isset($siteevent->body) && !empty($siteevent->body))
            $this->respondWithSuccess(strip_tags($siteevent->body), true);
    }

    //ACTION TO SET OVERVIEW
    public function overviewAction() {

        //ONLY LOGGED IN USER CAN ADD OVERVIEW
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();


        //GET EVENT ID AND OBJECT
        $event_id = $this->_getParam('event_id');
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.overview', 1) || !Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventpost.overview', 1)) {
            $this->respondWithError('unauthorized');
        }

        if ($this->_hasPackageEnable && !Engine_Api::_()->siteeventpaid()->allowPackageContent($siteevent->package_id, "overview")) {
            $this->respondWithError('unauthorized');
        }
        if (!$siteevent->authorization()->isAllowed($viewer, 'edit')) {
            $this->respondWithError('unauthorized');
        }

        if (!Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'siteevent_event', "overview")) {
            $this->respondWithError('unauthorized');
        }

        //FORM GENERATION
        if ($this->getRequest()->isGet()) {
            $response = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->getOverviewForm();
            $this->respondWithSuccess($response, true);
        } else if ($this->getRequest()->isPost()) {

            $values = $this->_getAllParams();
            if (empty($values['overview']))
                $this->respondWithError('parameter_missing', 'title');

            $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'siteevent');

            //SAVE THE VALUE
            $tableOtherinfo->update(array('overview' => $values['overview']), array('event_id = ?' => $event_id));
            $this->successResponseNoContent('no_content', true);
        }
    }

    public function notificationsAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');


        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        $event_id = $this->_getParam('event_id');

        //GET EVENT SUBJECT
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        //SAVE THE OCCURRENCE ID IN THE ZEND REGISTRY.
        $occurrence_id = $this->_getParam('occurrence_id', '');
        if (empty($occurrence_id) || !is_numeric($occurrence_id)) {
            //GET THE NEXT UPCOMING OCCURRENCE ID
            $occurrence_id = Engine_Api::_()->getDbTable('events', 'siteevent')->getNextOccurID($this->_getParam('event_id'));
        }

        //GET THE LEADERS LIST AND CHECK IF THE VIEWER IS LEADER OR NORMAL USER.
        if ($siteevent->owner_id == $viewer->getIdentity()) {
            $isLeader = 1;
        } else {
            $list = $siteevent->getLeaderList();
            $listItem = $list->get($viewer);
            $isLeader = ( null !== $listItem );
        }

        $row = Engine_Api::_()->getDbTable('membership', 'siteevent')->getRow($siteevent, $viewer);

        if (!$row) {
            $row->notification = Zend_Json_Decoder::decode('{"email":"0","notification":"1","action_notification":["posted","created","joined","comment","like","follow","rsvp"],"action_email":["posted","created","joined","rsvp"]}');
        }

        //SET FORM
        if ($this->getRequest()->isGet()) {
            $response['form'] = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->getNotificationForm($isLeader);
            $response['formValues'] = $row->notification;
            $this->respondWithSuccess($response, true);
        }

        //CHECK FORM VALIDATION
        if ($this->getRequest()->isPost()) {
            //GET FORM VALUES
            $values = $this->_getAllParams();

            if ($values['email'] == 1) {
                $notfication['email'] = 1;
                $notfication['action_email'] = explode(',', $values['action_email']);
            } else
                $notfication['email'] = 0;

            if ($values['notification'] == 1) {
                $notfication['notification'] = 1;
                $notfication['action_notification'] = explode(',', $values['action_notification']);
            } else
                $notfication['notification'] = 0;

            Engine_Api::_()->getDbtable('membership', 'siteevent')->update(array('notification' => $notfication), array('resource_id =?' => $event_id, 'user_id =?' => $row->user_id));
            $this->successResponseNoContent('no_content', true);
        }
    }

    //ACTION FOR EDIT THE LOCATION
    public function editLocationAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        //GET EVENT ID AND OBJECT
        $event_id = $this->_getParam('event_id');
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        //IF LOCATION SETTING IS ENABLED
        if (!Engine_Api::_()->siteevent()->enableLocation()) {
            $this->respondWithError('unauthorized');
        }

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        try {
            //AUTHORIZATION CHECK
//            if (!$this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "edit")->isValid) {
//                $this->respondWithError('unauthorized');
//            }
            //GET LOCATION TABLE
            $locationTable = Engine_Api::_()->getDbtable('locations', 'siteevent');

            //MAKE VALUE ARRAY
            $values = array();
            $value['id'] = $siteevent->event_id;

            //GET LOCATION
            $location = $locationTable->getLocation($value);


            if (!empty($location)) {

                //MAKE FORM
                if ($this->getRequest()->isGet()) {
                    $this->respondWithSuccess(array(
                        'form' => Engine_Api::_()->getApi('Siteapi_Core', 'siteevent')->getEditLocationForm(array('item' => $siteevent, 'location' => $location->location)),
                        'formValues' => $location->toArray()
                    ));
                }
            } else {
                $this->respondWithError('no_record');
            }

            //CHECK POST
            if ($this->getRequest()->isPost()) {
                $values = $location->toArray();
                $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'siteevent')->getEditLocationForm();
                foreach ($getForm as $element) {

                    if (isset($_REQUEST[$element['name']]))
                        $values[$element['name']] = $_REQUEST[$element['name']];
                }
                // START FORM VALIDATION
                $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'siteevent')->getEditLocationValidators();
                $values['validators'] = $validators;
                $validationMessage = $this->isValid($values);
                if (!empty($validationMessage) && @is_array($validationMessage)) {
                    $this->respondWithValidationError('validation_fail', $validationMessage);
                }

                //GET FORM VALUES
                unset($values['submit']);
                unset($values['location']);
                unset($values['validators']);

                //UPDATE LOCATION
                $locationTable->update($values, array('event_id = ?' => $event_id));
                $this->successResponseNoContent('no_content', true);
            }
        } catch (Exception $e) {
            $this->respondWithError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Get Categories , Sub-Categories, SubSub-Categories and Events array
     * 
     * 
     */
    public function categoriesAction() {

        // VALIDATE REQUEST METHOD
        $this->validateRequestMethod();

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();


        // PREPARE RESPONSE
        $values = $response = array();
        $category_id = $this->getRequestParam('category_id', null);
        $subCategory_id = $this->getRequestParam('subCategory_id', null);
        $subsubcategory_id = $this->getRequestParam('subsubcategory_id', null);
        $showAllCategories = $this->getRequestParam('showAllCategories', 1);
        $showCategories = $this->getRequestParam('showCategories', 1);
        $showEvents = $this->getRequestParam('showEvents', 1);

        if ($this->getRequestParam('showCount')) {
            $showCount = 1;
        } else {
            $showCount = $this->getRequestParam('showCount', 0);
        }
        $orderBy = $this->getRequestParam('orderBy', 'category_name');

        $tableCategory = Engine_Api::_()->getDbtable('categories', 'siteevent');
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        $categories = array();

        //GET EVENT TABLE
        $tableSiteevent = Engine_Api::_()->getDbtable('events', 'siteevent');
        $siteeventShowAllCategories = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventshow.allcategories', 1);
        $showAllCategories = !empty($siteeventShowAllCategories) ? $showAllCategories : 0;

        if ($showCategories) {

            if ($showAllCategories) {

                $category_info = $tableCategory->getCategories(array('category_id', 'category_name', 'cat_order', 'photo_id'), null, 0, 0, 1, 0, $orderBy, 1);
                $categoriesCount = count($category_info);
                foreach ($category_info as $value) {

                    $sub_cat_array = array();

                    if ($showCount) {
                        $categories[] = $category_array = array('category_id' => $value->category_id,
                            'category_name' => $this->translate($value->category_name),
                            'order' => $value->cat_order,
                            'count' => $tableSiteevent->getEventsCount($value->category_id, 'category_id', 1),
                            'images' => Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($value),
                        );
                    } else {
                        $categories[] = $category_array = array('category_id' => $value->category_id,
                            'category_name' => $this->translate($value->category_name),
                            'order' => $value->cat_order,
                            'images' => Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($value),
                        );
                    }
                }
            } else {
                $category_info = $tableCategory->getCategorieshasevents(0, 'category_id', null, array(), array('category_id', 'category_name', 'cat_order', 'photo_id'));
                $categoriesCount = count($category_info);
                foreach ($category_info as $value) {
                    if ($showCount) {
                        $categories[] = $category_array = array('category_id' => $value->category_id,
                            'category_name' => $value->category_name,
                            'order' => $value->cat_order,
                            'count' => $tableSiteevent->getEventsCount($value->category_id, 'category_id', 1),
                            'images' => Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($value),
                        );
                    } else {
                        $categories[] = $category_array = array('category_id' => $value->category_id,
                            'category_name' => $this->translate($value->category_name),
                            'order' => $value->cat_order,
                            'images' => Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($value),
                        );
                    }
                }
            }

            $response['categories'] = $categories;

            if (!empty($category_id)) {

                if ($showAllCategories) {
                    $category_info2 = $tableCategory->getSubcategories($category_id, array('category_id', 'category_name', 'cat_order', 'photo_id'));

                    foreach ($category_info2 as $subresults) {
                        if ($showCount) {
                            $sub_cat_array[] = $tmp_array = array('sub_cat_id' => $subresults->category_id,
                                'sub_cat_name' => $this->translate($subresults->category_name),
                                'count' => $tableSiteevent->getEventsCount($subresults->category_id, 'subcategory_id', 1),
                                'order' => $subresults->cat_order);
                        } else {
                            $sub_cat_array[] = $tmp_array = array('sub_cat_id' => $subresults->category_id,
                                'sub_cat_name' => $this->translate($subresults->category_name),
                                'order' => $subresults->cat_order);
                        }
                    }
                } else {
                    $category_info2 = $tableCategory->getCategorieshasevents($category_id, 'subcategory_id', null, array(), array('category_id', 'category_name', 'cat_order', 'photo_id'));
                    foreach ($category_info2 as $subresults) {
                        if ($showCount) {
                            $sub_cat_array[] = $tmp_array = array('sub_cat_id' => $subresults->category_id,
                                'sub_cat_name' => $this->translate($subresults->category_name),
                                'count' => $tableSiteevent->getEventsCount($subresults->category_id, 'subcategory_id', 1),
                                'order' => $subresults->cat_order);
                        } else {
                            $sub_cat_array[] = $tmp_array = array('sub_cat_id' => $subresults->category_id,
                                'sub_cat_name' => $this->translate($subresults->category_name),
                                'order' => $subresults->cat_order);
                        }
                    }
                }

                $response['subCategories'] = $sub_cat_array;
            }

            if (!empty($subCategory_id)) {
                if ($showAllCategories) {
                    $subcategory_info2 = $tableCategory->getSubcategories($subCategory_id, array('category_id', 'category_name', 'cat_order', 'photo_id'));
                    $treesubarrays = array();
                    foreach ($subcategory_info2 as $subvalues) {
                        if ($showCount) {
                            $treesubarrays[] = $treesubarray = array('tree_sub_cat_id' => $subvalues->category_id,
                                'tree_sub_cat_name' => $this->translate($subvalues->category_name),
                                'count' => $tableSiteevent->getEventsCount($subvalues->category_id, 'subsubcategory_id', 1),
                                'order' => $subvalues->cat_order,
                            );
                        } else {
                            $treesubarrays[] = $treesubarray = array('tree_sub_cat_id' => $subvalues->category_id,
                                'tree_sub_cat_name' => $this->translate($subvalues->category_name),
                                'order' => $subvalues->cat_order,
                            );
                        }
                    }
                } else {
                    $subcategory_info2 = $tableCategory->getCategorieshasevents($subCategory_id, 'subsubcategory_id', null, array(), array('category_id', 'category_name', 'cat_order', 'photo_id'));
                    $treesubarrays = array();
                    foreach ($subcategory_info2 as $subvalues) {
                        if ($showCount) {
                            $treesubarrays[] = $treesubarray = array('tree_sub_cat_id' => $subvalues->category_id,
                                'tree_sub_cat_name' => $this->translate($subvalues->category_name),
                                'order' => $subvalues->cat_order,
                                'count' => $tableSiteevent->getEventsCount($subvalues->category_id, 'subsubcategory_id', 1),
                            );
                        } else {
                            $treesubarrays[] = $treesubarray = array('tree_sub_cat_id' => $subvalues->category_id,
                                'tree_sub_cat_name' => $this->translate($subvalues->category_name),
                                'order' => $subvalues->cat_order
                            );
                        }
                    }
                }
                $response['subsubCategories'] = $treesubarrays;
            }
        }

        if ($showEvents && isset($category_id) && !empty($category_id)) {
            $params = array();
            $itemCount = $params['itemCount'] = $this->_getParam('itemCount', 0);
            $params['showEventType'] = $this->getRequestParam('showEventType', 'upcoming');
            $params['popularity'] = $popularity = $this->getRequestParam('popularity', 'view_count');
            $params['interval'] = $interval = $this->getRequestParam('interval', 'overall');
            $params['limit'] = $totalPages = $this->getRequestParam('eventCount', 5);
            $params['truncation'] = $this->getRequestParam('truncation', 25);

//        $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);
//        if ($this->view->detactLocation) {
//            $this->view->detactLocation = Engine_Api::_()->siteevent()->enableLocation();
//        }
//        if ($this->view->detactLocation) {
//            $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
//            $params['latitude'] = $this->_getParam('latitude', 0);
//            $params['longitude'] = $this->_getParam('longitude', 0);
//        }
            //GET CATEGORIES
            $categories = array();
            $category_info = Engine_Api::_()->getDbtable('categories', 'siteevent')->getCategorieshasevents($category_id, 'category_id', $itemCount, $params, array('category_id', 'category_name', 'cat_order'));
            $category_events_array = array();

            $params['category_id'] = $category_id;
            $params['subcategory_id'] = $subCategory_id;
            $params['subsubcategory_id'] = $subsubcategory_id;
            //GET PAGE RESULTS
            $category_events_info = $category_events_info = Engine_Api::_()->getDbtable('events', 'siteevent')->eventsBySettings($params);
            foreach ($category_events_info as $result_info) {
                // continue if Deleted member
                if (empty($result_info->host_id))
                    continue;
                $occurrence_id = Engine_Api::_()->getDbTable('events', 'siteevent')->getNextOccurID($result_info->getIdentity());

                //GET DATES OF EVENT
                $tz = Engine_Api::_()->getApi('settings', 'core')->core_locale_timezone;
                if (!empty($viewer_id)) {
                    $tz = $viewer->timezone;
                }
                $occurrenceTable = Engine_Api::_()->getDbTable('occurrences', 'siteevent');
                $dates = $occurrenceTable->getEventDate($result_info->getIdentity(), $occurrence_id);

                if (isset($dates['starttime']) && !empty($dates['starttime']) && isset($tz)) {
                    $startDateObject = new Zend_Date(strtotime($dates['starttime']));
                    $startDateObject->setTimezone($tz);
                    $eventdateinfo['starttime'] = $startDateObject->get('YYYY-MM-dd HH:mm:ss');
                }
                if (isset($dates['endtime']) && !empty($dates['endtime']) && isset($tz)) {
                    $endDateObject = new Zend_Date(strtotime($dates['endtime']));
                    $endDateObject->setTimezone($tz);
                    $eventdateinfo['endtime'] = $endDateObject->get('YYYY-MM-dd HH:mm:ss');
                }

                $totalEventOccurrences = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getOccurrenceCount($result_info->event_id);
                if (!empty($result_info->repeat_params) && $totalEventOccurrences > 1) {
                    $hasMultipleDates = 1;
                } else {
                    $hasMultipleDates = 0;
                }


                $tmp_array = array('event_id' => $result_info->event_id,
                    'imageSrc' => Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($result_info),
                    'event_title' => $result_info->title,
                    'owner_id' => $result_info->owner_id,
                    'popularityCount' => $result_info->$popularity,
                    'slug' => $result_info->getSlug(),
                    'starttime' => $eventdateinfo['starttime'],
                    'endtime' => $eventdateinfo['endtime'],
                    'hasMultipleDates' => $hasMultipleDates
                );

                if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1) && !$result_info->is_online) {

                    //GET LOCATION
                    $locationParams['id'] = $result_info->event_id;

                    $location = Engine_Api::_()->getDbtable('locations', 'siteevent')->getLocation($locationParams);
                    if (isset($location) && isset($location->location))
                        $tmp_array['location'] = $location->location;
                }

                $host = $result_info->getHost();
                if (isset($host) && !empty($host)) {
                    $host_icons = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($host);
                    $organizer['host_type'] = $host->getType();
                    $organizer['host_id'] = $host->getIdentity();
                    $organizer['host_title'] = $host->getTitle();
                    $organizer['image_icon'] = $host_icons;

                    $userEvents = Engine_Api::_()->getDbTable('events', 'siteevent')->userEvent($host);
                    $organizer['event_hosted'] = count($userEvents);
                    $tmp_array['host'] = $organizer;
                }
                $category_events_array[] = $tmp_array;
            }

            $response['events'] = $category_events_array;
        }
        if (isset($categoriesCount) && !empty($categoriesCount))
            $response['totalItemCount'] = $categoriesCount;
        $response['canCreate'] = Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, 'create');
        $response['packagesEnabled'] = $this->_packagesEnabled();

        $this->respondWithSuccess($response, true);
    }

    /**
     * Edit the Event.
     * 
     * 
     */
    public function editAction() {

        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        $listValues = array();
        $event_id = $this->_getParam('event_id');
        $occurence_id = $this->getRequestParam('occurence_id');
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);


        if (empty($siteevent)) {
            $this->respondWithError('no_record');
        }

        //CHECK IF NO USER HAS JOINED THIS EVENT YET THEN WE WILL SHOW START DATE IN EDIT MODE ELSE ONLY END DATE AND END REPEAT TIME IN EDIT MODE.
        $editFullEventDate = true;
        $hasEventMember = $siteevent->membership()->hasEventMember($viewer, true);
        if (Engine_Api::_()->hasModuleBootstrap('siteeventrepeat')) {
            if (Engine_Api::_()->siteevent()->hasTicketEnable()) {
                $hasEventTicketGuest = Engine_Api::_()->getDbTable('orders', 'siteeventticket')->hasEventTicketGuest($siteevent, $viewer);
            }
        }

        //IF EVENT JOINED / TICKET SOLD THEN CAN NOT EDIT FULL EVENT DATE
        if (!$hasEventMember || (isset($hasEventTicketGuest) && $hasEventTicketGuest)) {
            $editFullEventDate = false;
        }

        //IF EVENT EDITING IS ALREADY FALSE THAN DO NOT NEED TO CHECK IT FOR CAPACITY
        if ($editFullEventDate && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.waitlist', 1)) {
            $totalEventsInWaiting = Engine_Api::_()->getDbTable('waitlists', 'siteevent')->getColumnValue(array('event_id' => $siteevent->getIdentity(), 'columnName' => 'COUNT(*) AS totalEventsInWaiting'));
            $editFullEventDate = !$totalEventsInWaiting;
        }

        $eventdateinfo = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getEventDate($event_id, 0);

        $starttimestemp = strtotime($eventdateinfo['starttime']);
        //$previous_location = $siteevent->location;
        $siteeventinfo = $siteevent->toarray();
        $previous_category_id = $siteevent->category_id;
        $subcategory_id = $siteevent->subcategory_id;
        $subsubcategory_id = $siteevent->subsubcategory_id;

        $row = Engine_Api::_()->getDbtable('categories', 'siteevent')->getCategory($subcategory_id);
        $subcategory_name = "";
        if (!empty($row)) {
            $subcategory_name = $row->category_name;
        }

        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            Engine_Api::_()->core()->setSubject($siteevent);
        }

        if (!$this->_helper->requireSubject()->isValid())
            $this->respondWithError('unauthorized');


        if (!$this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "edit")->isValid()) {
            $this->respondWithError('unauthorized');
        }

        //GET DEFAULT PROFILE TYPE ID
        $defaultProfileId = Engine_Api::_()->getDbTable('metas', 'siteevent')->defaultProfileId();

        //GET PROFILE MAPPING ID
        $tempEditFlag = null;
        $formpopulate_array = $categoryIds = array();
        $categoryIds[] = $siteevent->category_id;
        $categoryIds[] = $siteevent->subcategory_id;
        $categoryIds[] = $siteevent->subsubcategory_id;
        $previous_profile_type = Engine_Api::_()->getDbtable('categories', 'siteevent')->getProfileType($categoryIds, 0, 'profile_type');

        if (isset($_POST['category_id']) && !empty($_POST['category_id'])) {
            $categoryIds = array();
            $categoryIds[] = $_POST['category_id'];
            if (isset($_POST['subcategory_id']) && !empty($_POST['subcategory_id'])) {
                $categoryIds[] = $_POST['subcategory_id'];
            }
            if (isset($_POST['subsubcategory_id']) && !empty($_POST['subsubcategory_id'])) {
                $categoryIds[] = $_POST['subsubcategory_id'];
            }
            $previous_profile_type = Engine_Api::_()->getDbtable('categories', 'siteevent')->getProfileType($categoryIds, 0, 'profile_type');
        }

        $parent_type = $siteevent->parent_type;
        $parent_id = $siteevent->parent_id;

        $parentTypeItem = Engine_Api::_()->getItem($parent_type, $parent_id);

        $isParentEditPrivacy = Engine_Api::_()->siteevent()->isParentEditPrivacy($siteevent->parent_type, $siteevent->parent_id);

        if (empty($isParentEditPrivacy))
            $this->respondWithError('unauthorized');


        // $host = $viewer;

        $previousHost = $siteevent->getHost();

        if (isset($previousHost))
            $host_icons = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($previousHost);

        $form = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->getForm($siteevent, $parent_type, $parent_id, $previousHost, $host_icons, $previous_profile_type);

        //Privacy formValues
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('leader', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        $explodeParentType = explode('_', $parent_type);
        if (!empty($explodeParentType) && isset($explodeParentType[0]) && isset($explodeParentType[1])) {
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($explodeParentType[0] . 'member') && ($parent_type == 'sitepage_page' || $parent_type == 'sitebusiness_business' || $parent_type == 'sitegroup_group') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parent_type, 'item_module' => $explodeParentType[0])))) {
                $roles = array('leader', 'member', 'parent_member', 'registered', 'everyone');
            } elseif (($parent_type == 'sitepage_page' || $parent_type == 'sitebusiness_business' || $parent_type == 'sitegroup_group' || $parent_type == 'sitestore_store') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parent_type, 'item_module' => $explodeParentType[0])))) {
                $roles = array('leader', 'member', 'registered', 'everyone');
            } elseif (($parent_type == 'sitereview_listing') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitereview_listing_' . $parentTypeItem->listingtype_id, 'item_module' => 'sitereview')))) {
                $roles = array('leader', 'member', 'registered', 'everyone');
            }
        }

        foreach ($roles as $roleString) {

            $role = $roleString;
            if ($role === 'leader') {
                $role = $leaderList;
            }


            if (1 == $auth->isAllowed($siteevent, $role, "view")) {
                $authValues['auth_view'] = $roleString;
            }

            if (1 == $auth->isAllowed($siteevent, $role, "comment")) {
                $authValues['auth_comment'] = $roleString;
            }
        }
        $ownerList = '';
        $roles_photo = array('leader', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
        $explodeParentType = explode('_', $parent_type);
        if (!empty($explodeParentType) && isset($explodeParentType[0]) && isset($explodeParentType[1])) {
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($explodeParentType[0] . 'member') && ($parent_type == 'sitepage_page' || $parent_type == 'sitebusiness_business' || $parent_type == 'sitegroup_group') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parent_type, 'item_module' => $explodeParentType[0])))) {
                $roles_photo = array('leader', 'member', 'like_member', 'parent_member', 'registered');
                $getContentOwnerList = 'get' . ucfirst($parentTypeItem->getShortType()) . 'OwnerList';
                $ownerList = $parentTypeItem->$getContentOwnerList();
            } elseif (($parent_type == 'sitepage_page' || $parent_type == 'sitebusiness_business' || $parent_type == 'sitegroup_group' || $parent_type == 'sitestore_store') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parent_type, 'item_module' => $explodeParentType[0])))) {
                $roles_photo = array('leader', 'member', 'like_member', 'registered');
                $getContentOwnerList = 'get' . ucfirst($parentTypeItem->getShortType()) . 'OwnerList';
                $ownerList = $parentTypeItem->$getContentOwnerList();
            } elseif (($parent_type == 'sitereview_listing') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitereview_listing_' . $parentTypeItem->listingtype_id, 'item_module' => 'sitereview')))) {
                $roles_photo = array('leader', 'member', 'registered', 'everyone');
            }
        }
        foreach ($roles_photo as $roleString) {

            $role = $roleString;
            if ($role === 'leader') {
                $role = $leaderList;
            }

            if ($role === 'like_member' && $ownerList) {
                $role = $ownerList;
            }

            //Here we change isAllowed function for like privacy work only for populate.
            $siteeventAllow = Engine_Api::_()->getApi('allow', 'siteevent');
            if (1 == $siteeventAllow->isAllowed($siteevent, $role, 'topic')) {
                $authValues['auth_topic'] = $roleString;
            }

            if (1 == $siteeventAllow->isAllowed($siteevent, $role, 'post')) {
                $authValues['auth_post'] = $roleString;
            }
        }

        foreach ($roles_photo as $roleString) {

            $role = $roleString;
            if ($role === 'leader') {
                $role = $leaderList;
            }

            if ($role === 'like_member' && $ownerList) {
                $role = $ownerList;
            }

            //Here we change isAllowed function for like privacy work only for populate.
            $siteeventAllow = Engine_Api::_()->getApi('allow', 'siteevent');

            if (1 == $siteeventAllow->isAllowed($siteevent, $role, 'photo')) {
                $authValues['auth_photo'] = $roleString;
            }
        }

        $videoEnable = Engine_Api::_()->siteevent()->enableVideoPlugin();
        if ($videoEnable) {
            $roles_video = array('leader', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
            $explodeParentType = explode('_', $parent_type);
            if (!empty($explodeParentType) && isset($explodeParentType[0]) && isset($explodeParentType[1])) {
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($explodeParentType[0] . 'member') && ($parent_type == 'sitepage_page' || $parent_type == 'sitebusiness_business' || $parent_type == 'sitegroup_group') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parent_type, 'item_module' => $explodeParentType[0])))) {
                    $roles_video = array('leader', 'member', 'like_member', 'parent_member', 'registered');
                    $getContentOwnerList = 'get' . ucfirst($parentTypeItem->getShortType()) . 'OwnerList';
                    $ownerList = $parentTypeItem->$getContentOwnerList();
                } elseif (($parent_type == 'sitepage_page' || $parent_type == 'sitebusiness_business' || $parent_type == 'sitegroup_group' || $parent_type == 'sitestore_store') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parent_type, 'item_module' => $explodeParentType[0])))) {
                    $roles_video = array('leader', 'member', 'like_member', 'registered');
                    $getContentOwnerList = 'get' . ucfirst($parentTypeItem->getShortType()) . 'OwnerList';
                    $ownerList = $parentTypeItem->$getContentOwnerList();
                } elseif (($parent_type == 'sitereview_listing') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitereview_listing_' . $parentTypeItem->listingtype_id, 'item_module' => 'sitereview')))) {
                    $roles_video = array('leader', 'member', 'registered', 'everyone');
                }
            }
            foreach ($roles_video as $roleString) {

                $role = $roleString;
                if ($role === 'leader') {
                    $role = $leaderList;
                }

                if ($role === 'like_member' && $ownerList) {
                    $role = $ownerList;
                }

                //Here we change isAllowed function for like privacy work only for populate.
                $siteeventAllow = Engine_Api::_()->getApi('allow', 'siteevent');
                if (1 == $siteeventAllow->isAllowed($siteevent, $role, 'video')) {
                    $authValues['auth_video'] = $roleString;
                }
            }
        }


        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventdocument')) {
            foreach ($roles_photo as $roleString) {
                $role = $roleString;
                if ($role === 'leader') {
                    $role = $leaderList;
                }

                if ($role === 'like_member' && $ownerList) {
                    $role = $ownerList;
                }

                //Here we change isAllowed function for like privacy work only for populate.
                $siteeventAllow = Engine_Api::_()->getApi('allow', 'siteevent');
                if (1 == $siteeventAllow->isAllowed($siteevent, $role, 'document')) {
                    $authValues['auth_document'] = $roleString;
                }
            }
        }

        if (Engine_Api::_()->siteevent()->listBaseNetworkEnable()) {
            if (empty($siteevent->networks_privacy)) {
                $authValues['networks_privacy'] = array(0);
            }
        }

        $authValues['auth_invite'] = $auth->isAllowed($siteevent, 'member', 'invite');

        //prepare tags
        $siteeventTags = $siteevent->tags()->getTagMaps();
        $tagString = '';

        foreach ($siteeventTags as $tagmap) {

            if ($tagString != '')
                $tagString .= ', ';
            $tagString .= $tagmap->getTag()->getTitle();
        }

        $tagNamePrepared = $tagString;


        //GET EVENT CREATE FORM
        if ($this->getRequest()->isGet()) {
            if (!empty($siteevent) && !empty($siteevent->profile_type))
                $getProfileInfo = Engine_Api::_()->getApi('Siteapi_Core', 'siteevent')->getProfileInfo($siteevent, true);
            $form['formValues'] = $siteevent->toArray();

            $repeat_params = json_decode($siteevent->repeat_params, true);
            if (isset($repeat_params['eventrepeat_type']) && !empty($repeat_params['eventrepeat_type']))
                $repeat_params['eventrepeat_id'] = $repeat_params['eventrepeat_type'];

            if (isset($repeat_params['endtime']['date']) && !empty($repeat_params['endtime']['date']))
                $repeat_params['date'] = $repeat_params['endtime']['date'];

            if (isset($repeat_params) && !empty($repeat_params)) {
                unset($form['formValues']['repeat_params']);
                if ($repeat_params['eventrepeat_type'] == 'daily' && isset($repeat_params['repeat_interval'])) {
                    $repeat_params['repeat_interval'] = $repeat_params['repeat_interval'] / (24 * 60 * 60);
                }

                if (isset($repeat_params['eventrepeat_type']) && $repeat_params['eventrepeat_type'] == 'monthly' && isset($repeat_params['repeat_day']) && !empty($repeat_params['repeat_day'])) {
                    $repeat_params['monthlyType'] = 1;
                } else {
                    $repeat_params['monthlyType'] = 0;
                }

                $form['formValues'] = array_merge($form['formValues'], $repeat_params);
            }

            if (isset($authValues) && !empty($authValues))
                $form['formValues'] = array_merge($form['formValues'], $authValues);


            if (isset($tagString) && !empty($tagString))
                $form['formValues']['tags'] = $tagString;


            //GET DATES OF EVENT
            $occurrenceTable = Engine_Api::_()->getDbTable('occurrences', 'siteevent');
            $dates = $occurrenceTable->getEventDate($event_id);
            $form['formValues'] = array_merge($form['formValues'], $dates);

            if (isset($getProfileInfo) && !empty($getProfileInfo))
                $form['formValues'] = array_merge($form['formValues'], $getProfileInfo);

            if ($siteevent->host_type == 'siteevent_organizer') {
                $organizerObj = Engine_Api::_()->getItem($siteevent->host_type, $siteevent->host_id);
                $content = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($organizerObj, false);
                $content_photo = $content['image_icon'];
                $link = $content['content_url'];
                $form['hostCreateForm'][0]['value'] = $form['formValues']['host_title'] = $organizerObj->title;
                $form['hostCreateForm'][1]['value'] = $form['formValues']['host_description'] = $organizerObj->description;
                $form['hostCreateForm'][3]['value'] = $form['formValues']['host_link'] = ($organizerObj->facebook_url || $organizerObj->twitter_url || $organizerObj->web_url) ? 1 : 0;
                $form['hostCreateFormSocial'][0]['value'] = $form['formValues']['host_facebook'] = ($organizerObj->facebook_url) ? $organizerObj->facebook_url : "";
                $form['hostCreateFormSocial'][1]['value'] = $form['formValues']['host_twitter'] = ($organizerObj->twitter_url) ? $organizerObj->twitter_url : "";
                $form['hostCreateFormSocial'][2]['value'] = $form['formValues']['host_website'] = ($organizerObj->web_url) ? $organizerObj->web_url : "";
                $form['hostCreateForm'][2]['value'] = $form['formValues']['host_icon'] = $content_photo;
            }

            $this->respondWithSuccess($form);
        }

        $oldEventTitle = $siteevent->title;
        $oldEventVenue = $siteevent->venue_name;
        $oldEventStarttime = $eventdateinfo['starttime'];
        $oldEventEndtime = $eventdateinfo['endtime'];

        $inDraft = 1;

        $leaderList = $siteevent->getLeaderList();

        if ($this->getRequest()->isPost() || $this->getRequest()->isPut()) {
            $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->getForm(null, $parent_type, $parent_id, $host_icons, $previous_profile_type);
            $_POST = $values = $data = $_REQUEST;
            $values = $siteevent->toArray();
            $params = $this->_getAllParams();
            foreach ($getForm['form'] as $element) {
                if (isset($_REQUEST[$element['name']])) {
                    $values[$element['name']] = $_REQUEST[$element['name']];
                }
            }

            $values['subcategory_id'] = (!empty($_REQUEST['subcategory_id'])) ? $_REQUEST['subcategory_id'] : 0;
            $values['subsubcategory_id'] = (!empty($_REQUEST['subsubcategory_id'])) ? $_REQUEST['subsubcategory_id'] : 0;


            // START FORM VALIDATION
            $eventValidators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'siteevent')->getFormValidators($values);
            $values['validators'] = $eventValidators;

            $eventValidationMessage = $this->isValid($values);

            if (!@is_array($validationMessage) && isset($values['category_id'])) {

                $categoryIds = array();
                $categoryIds[] = $values['category_id'];
                $categoryIds[] = $values['subcategory_id'];
                $categoryIds[] = $values['subsubcategory_id'];
                $values['profile_type'] = Engine_Api::_()->getDbTable('categories', 'siteevent')->getProfileType($categoryIds, 0, 'profile_type');

                if (isset($values['profile_type']) && !empty($values['profile_type'])) {
                    $getProfileInfo = Engine_Api::_()->getApi('Siteapi_Core', 'siteevent')->getProfileInfo($siteevent, true);
                    if (isset($getProfileInfo))
                        $values = array_merge($values, $getProfileInfo);
                    // START FORM VALIDATION
                    $profileFieldsValidators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'siteevent')->getFieldsFormValidations($values);
                    $values['validators'] = $profileFieldsValidators;
                    $profileFieldsValidationMessage = $this->isValid($values);
                }
            }
            if (is_array($eventValidationMessage) && is_array($profileFieldsValidationMessage))
                $validationMessage = array_merge($eventValidationMessage, $profileFieldsValidationMessage);

            else if (is_array($eventValidationMessage))
                $validationMessage = $eventValidationMessage;
            else if (is_array($profileFieldsValidationMessage))
                $validationMessage = $profileFieldsValidationMessage;
            else
                $validationMessage = 1;

            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            $values['is_online'] = empty($values['is_online']) ? 0 : $values['is_online'];
            // $values['host_type'] = $parent_type;
            // $values['host_id'] = $parent_id;

            $table = Engine_Api::_()->getItemTable('siteevent_event');
            $db = $table->getAdapter();
            $db->beginTransaction();
            $user_level = $viewer->level_id;
            try {
                //Create siteevent
                if (!$this->_hasPackageEnable) {
                    //Create siteevent
                    $values = array_merge($values, array(
                        'owner_type' => $viewer->getType(),
                        'owner_id' => $viewer_id,
                        'featured' => Engine_Api::_()->authorization()->getPermission($user_level, 'siteevent_event', "featured"),
                        'sponsored' => Engine_Api::_()->authorization()->getPermission($user_level, 'siteevent_event', "sponsored"),
                        'approved' => Engine_Api::_()->authorization()->getPermission($user_level, 'siteevent_event', "approved")
                    ));
                } else {
                    $values = array_merge($values, array(
                        'owner_type' => $viewer->getType(),
                        'owner_id' => $viewer_id,
                        'featured' => $siteevent->featured,
                        'sponsored' => $siteevent->sponsored
                    ));

                    $values['approved'] = $siteevent->approved;
                }

                if (empty($values['subcategory_id'])) {
                    $values['subcategory_id'] = 0;
                }

                if (empty($values['subsubcategory_id'])) {
                    $values['subsubcategory_id'] = 0;
                }

                //check if admin has disabled "approval" for RSVP to be invited.
                if (!isset($values['approval']))
                    $values['approval'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.invite.rsvp.automatically', 1);

                //check if admin has disabled "auth_invite" for event members to invite other people
                if (!isset($values['auth_invite']))
                    $values['auth_invite'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.invite.other.automatically', 1);


                if (Engine_Api::_()->siteevent()->listBaseNetworkEnable()) {
                    if (isset($values['networks_privacy']) && !empty($values['networks_privacy'])) {
                        if (in_array(0, $values['networks_privacy'])) {
                            unset($values['networks_privacy']);
                        }
                    }
                }

                $values['draft'] = empty($values['draft']) ? 0 : $values['draft'];
                $values['approved'] = empty($values['approved']) ? 0 : $values['approved'];
                $values['closed'] = empty($values['closed']) ? 0 : $values['closed'];

                //check if event creater has added any host details there.
                $values['parent_type'] = $parent_type;
                $values['parent_id'] = $parent_id;

                //IF EVENT IS ONLY THEN LOCATION FIELD SHOULD BE EMPTY
                if (!empty($values['is_online'])) {
                    $values['location'] = '';
                } else {
                    $values['is_online'] = 0;
                }
                $siteevent->setFromArray($values);

                $siteevent->setFromArray($values);
                $siteevent->modified_date = date('Y-m-d H:i:s');
                if ($tags)
                    $siteevent->tags()->setTagMaps($viewer, $tags);

                try {
                    //@todo Local error on upgrade
                    Engine_Api::_()->getApi('Core', 'siteapi')->setView();
                    Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();
                    if ((isset($_POST['eventrepeat_id'])) && ($_POST['eventrepeat_id'] == 'weekly' || $_POST['eventrepeat_id'] == 'monthly') || ($_POST['eventrepeat_id'] == 'daily')) {

                        if ($_POST['eventrepeat_id'] == 'weekly') {
                            $_POST['id_weekly-repeat_interval'] = $data['repeat_week'];
                            if (isset($_POST['repeat_weekday'])) {
                                $weekdays = array(1 => 'monday', 2 => 'tuesday', 3 => 'wednesday', 4 => 'thursday', 5 => 'friday', 6 => 'saturday', 7 => 'sunday');

                                $_POST['repeat_weekday'] = array_map('intval', explode(',', $_POST['repeat_weekday']));
                                foreach ($_POST['repeat_weekday'] as $weekday) {
                                    $_POST['weekly-repeat_on_' . $weekdays[$weekday]] = $weekdays[$weekday];
                                }
                            }
                            if (isset($_POST['date']) && !empty($_POST['date'])) {
                                $_POST['weekly_repeat_time'] = array(
                                    'date' => $_POST['date']
                                );
                            }
                            $isValidOccurrences = Engine_Api::_()->siteevent()->checkValidOccurrences($_POST);
                            if (!$isValidOccurrences) {
                                $errorMessage = array();
                                $errorMessage[] = $this->translate('Please make sure you have selected the correct time interval - it is required');
                                $this->respondWithValidationError('validation_fail', $errorMessage);
                            } else {
                                $repeatEventInfo['repeat_interval'] = 0;
                                $repeatEventInfo['repeat_week'] = $_POST['repeat_week'];
                                $repeatEventInfo['repeat_weekday'] = $_POST['repeat_weekday'];
                                $repeatEventInfo['eventrepeat_type'] = $_POST['eventrepeat_id'];
                                $repeatEventInfo['endtime']['date'] = $_POST['date'];
                            }
                        }
                        if ($_POST['eventrepeat_id'] == 'monthly') {

                            $repeatEventInfo['repeat_interval'] = 0;

                            if (isset($_REQUEST['monthlyType']) && !empty($_REQUEST['monthlyType'])) {
                                if (isset($_POST['repeat_day']) && !empty($_POST['repeat_day']))
                                    $repeatEventInfo['repeat_day'] = $_POST['repeat_day'];
                            }else {

                                if (isset($_POST['repeat_week']) && !empty($_POST['repeat_week']))
                                    $repeatEventInfo['repeat_week'] = $_POST['repeat_week'];

                                if (isset($_POST['repeat_weekday']) && !empty($_POST['repeat_weekday']))
                                    $repeatEventInfo['repeat_weekday'] = $_POST['repeat_weekday'];
                            }
                            $repeatEventInfo['eventrepeat_type'] = $_POST['eventrepeat_id'];
                            $repeatEventInfo['endtime']['date'] = $_POST['date'];
                            $repeatEventInfo['repeat_month'] = $_POST['repeat_month'];
                            $_POST['monthly_repeat_time']['date'] = $_POST['date'];
                            $completeEventInfo = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->getRepeatEventCompleteInfo($_POST, $repeatEventInfo, 0);
                            $_POST = $completeEventInfo;
                            $isValidOccurrences = Engine_Api::_()->siteevent()->checkValidOccurrences($_POST);
                        }
                        if ($_POST['eventrepeat_id'] == 'daily') {
                            $repeatEventInfo['repeat_interval'] = $_POST['repeat_interval'] * (24 * 3600);
                            $repeatEventInfo['eventrepeat_type'] = $_POST['eventrepeat_id'];
                            $repeatEventInfo['endtime']['date'] = $_POST['date'];
                            $isValidOccurrences = true;
                        }
                        if (!$isValidOccurrences) {
                            $errorMessage = array();
                            $errorMessage[] = $this->translate('Please make sure you have selected the correct time interval - it is required');
                            $this->respondWithValidationError('validation_fail', $errorMessage);
                        }
                    } elseif (isset($_POST['eventrepeat_id']) && $_POST['eventrepeat_id'] == 'custom') {
                        $repeatEventInfo['eventrepeat_type'] = $_POST['eventrepeat_id'];
//                        $_POST['countcustom_dates'] = 2;
                        Engine_Api::_()->siteevent()->reorderCustomDates();
                        $siteevent->repeat_params = json_encode($repeatEventInfo);
                        $siteevent->save();
                    }
                    //CHECK EITHER USER HAS EDITED THE DATE OR NOT.IF NOT EDITED THEN WE WILL NOT UPDATE THE OCCURRENCE TABLE.
                    $isupdate = Engine_Api::_()->siteevent()->editDateMatch($_POST, $eventdateinfo, $repeatEventInfo, $siteevent);
                } catch (Exception $ex) {
                    // Blank Exception
                }
                if (!empty($repeatEventInfo)) {
                    //SET THE PREVIOUS EVENT TYPE FOR SPECIAL CASE OF CUSTOM EVENT.
                    $eventparams = json_decode($siteevent->repeat_params);
                    if (!empty($eventparams))
                        $values['previous_eventtype'] = $eventparams->eventrepeat_type;
                    //CONVERT TO CORRECT DATE FORMAT
                    if (isset($repeatEventInfo['endtime']))
                        $repeatEventInfo['endtime']['date'] = $repeatEventInfo['endtime']['date'];
                    $siteevent->repeat_params = json_encode($repeatEventInfo);
                } else
                    $siteevent->repeat_params = '';
                // }


                if ($editFullEventDate && $isupdate) {
                    //CHECK IF SITEREPEAT EVENT IS NOT ENABLED THEN WE WILL DO NOT DELETE OCCURRENCE.We will just update that
                    if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventrepeat')) {
                        //SELECT THE ALL OCCURRENCES OF THIS EVENT.
                        $tableOccurence = Engine_Api::_()->getDbtable('occurrences', 'siteevent');
                        try {

                            $dateInfo = Engine_Api::_()->siteevent()->userToDbDateTime(array('starttime' => $values['starttime'], 'endtime' => $values['endtime']));
                            $values['starttime'] = $dateInfo['starttime'];
                            $values['endtime'] = $dateInfo['endtime'];
                            $tableOccurence->update(array('starttime' => $values['starttime'], 'endtime' => $values['endtime']), array('event_id =?' => $event_id));
                        } catch (Exception $E) {
                            //silence
                        }
                    } else {
                        $occure_id = $this->addorEditDates($_POST, $values, $event_id, 'edit');
                    }
                } else if (!$editFullEventDate && $isupdate) {
                    $this->editDates($_POST, $values, $event_id, 'edit');
                }

                $siteevent->save();

                $actionTable = Engine_Api::_()->getDbtable('actions', 'seaocore');
                //NOTIFICATION AND ACTIVITY FEED WORK WHEN EDIT THE EVENT TITLE
                if ($siteevent->title !== $oldEventTitle) {
                    $link = $siteevent->getHref();
                    $newTitle = "<b><a href='$link'>$siteevent->title</a></b>";
                    //$oldTitle ="<a href='$link'>$oldEventTitle</a>";
                    $action = $actionTable->addActivity($viewer, $siteevent, Engine_Api::_()->siteevent()->getActivtyFeedType($siteevent, 'siteevent_title_updated'), null, array('oldtitle' => $oldEventTitle, 'newtitle' => $newTitle));
                    if ($action != null) {
                        //START NOTIFICATION AND EMAIL WORK
                        Engine_Api::_()->siteevent()->sendNotificationEmail($siteevent, $action, 'siteevent_title_updated', null, null, null, 'title', $siteevent);
                    }
                }

                if ($siteevent->venue_name !== $oldEventVenue) {
                    $action = $actionTable->addActivity($viewer, $siteevent, Engine_Api::_()->siteevent()->getActivtyFeedType($siteevent, 'siteevent_venue_updated'), null, array('oldvenue' => $oldEventVenue, 'newvenue' => $siteevent->venue_name));
                    if ($action != null) {
                        //START NOTIFICATION AND EMAIL WORK
                        Engine_Api::_()->siteevent()->sendNotificationEmail($siteevent, $action, 'siteevent_venue_updated', null, null, null, 'venue', $siteevent);
                    }
                }

                // Profile Fields: start work to save profile fields.
                $profileTypeField = null;
                $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('siteevent_event');
                if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
                    $profileTypeField = $topStructure[0]->getChild();
                }

                $profileTypeValue = $siteevent->profile_type;
                Engine_Api::_()->getApi('Siteapi_Core', 'siteevent')->setProfileFields($siteevent, $data);


                //NOT SEARCHABLE IF SAVED IN DRAFT MODE
                if (!empty($siteevent->draft)) {
                    $siteevent->search = 0;
                    $siteevent->save();
                }

                //NOW MAKE THE ENTRY OF REPEAT INFO IF IT IS  ENABLED
                $event_id = $siteevent->event_id;

                //NOW MAKE THE ENTRY OF REPEAT INFO IF IT IS  ENABLED

                if ($siteevent->draft == 0 && $siteevent->search && $inDraft) {
                    //INSERT ACTIVITY IF EVENT IS SEARCHABLE
                    if ($parent_type != 'user' && $parent_type != 'sitereview_listing') {
                        $getModuleName = strtolower($parentTypeItem->getModuleName());
                        $isOwner = 'is' . ucfirst($parentTypeItem->getShortType()) . 'Owner';
                        $isFeedTypeEnable = 'isFeedType' . ucfirst($parentTypeItem->getShortType()) . 'Enable';
                        $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
                        $activityFeedType = null;
                        if (Engine_Api::_()->$getModuleName()->$isOwner($parentTypeItem) && Engine_Api::_()->$getModuleName()->$isFeedTypeEnable())
                            $activityFeedType = $getModuleName . 'event_admin_new';
                        elseif ($parentTypeItem->all_post || Engine_Api::_()->$getModuleName()->$isOwner($parentTypeItem))
                            $activityFeedType = $getModuleName . 'event_new';

                        if ($activityFeedType) {
                            $action = $actionTable->addActivity($viewer, $parentTypeItem, $activityFeedType);
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
                    } else {
//                        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($siteevent->getOwner(), $siteevent, 'siteevent_new');
                        if ($action != null) {
                            Engine_Api::_()->getDbtable('actions', 'seaocore')->attachActivity($action, $siteevent);
                        }
                    }
                }

                if (isset($values['tags'])) {
                    $tags = preg_split('/[,]+/', $values['tags']);
                    $tags = array_filter(array_map("trim", $tags));
                }
                if ($tags)
                    $siteevent->tags()->setTagMaps($viewer, $tags);


                //CREATE AUTH STUFF HERE
                $auth = Engine_Api::_()->authorization()->context;
                $auth->setAllowed($siteevent, 'member', 'invite', $values['auth_invite']);
                $roles = array('leader', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                $explodeParentType = explode('_', $parent_type);
                if (!empty($explodeParentType) && isset($explodeParentType[0]) && isset($explodeParentType[1])) {
                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($explodeParentType[0] . 'member') && ($parent_type == 'sitepage_page' || $parent_type == 'sitebusiness_business' || $parent_type == 'sitegroup_group') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parent_type, 'item_module' => $explodeParentType[0])))) {
                        $roles = array('leader', 'member', 'parent_member', 'registered', 'everyone');
                    } elseif (($parent_type == 'sitepage_page' || $parent_type == 'sitebusiness_business' || $parent_type == 'sitegroup_group' || $parent_type == 'sitestore_store') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parent_type, 'item_module' => $explodeParentType[0])))) {
                        $roles = array('leader', 'member', 'registered', 'everyone');
                    } elseif (($parent_type == 'sitereview_listing') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitereview_listing_' . $parentTypeItem->listingtype_id, 'item_module' => 'sitereview')))) {
                        $roles = array('leader', 'member', 'registered', 'everyone');
                    }
                }

                if (empty($values['auth_view'])) {
                    $values['auth_view'] = "everyone";
                }

                if (empty($values['auth_comment'])) {
                    $values['auth_comment'] = "everyone";
                }

                $viewMax = array_search($values['auth_view'], $roles);
                $commentMax = array_search($values['auth_comment'], $roles);

                foreach ($roles as $i => $role) {

                    if ($role === 'leader') {
                        $role = $leaderList;
                    }

                    $auth->setAllowed($siteevent, $role, "view", ($i <= $viewMax));
                    $auth->setAllowed($siteevent, $role, "comment", ($i <= $commentMax));
                }
                $ownerList = '';
                $roles = array('leader', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
                $explodeParentType = explode('_', $parent_type);
                if (!empty($explodeParentType) && isset($explodeParentType[0]) && isset($explodeParentType[1])) {
                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($explodeParentType[0] . 'member') && ($parent_type == 'sitepage_page' || $parent_type == 'sitebusiness_business' || $parent_type == 'sitegroup_group') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parent_type, 'item_module' => $explodeParentType[0])))) {
                        $roles = array('leader', 'member', 'like_member', 'parent_member', 'registered');
                        $getContentOwnerList = 'get' . ucfirst($parentTypeItem->getShortType()) . 'OwnerList';
                        $ownerList = $parentTypeItem->$getContentOwnerList();
                    } elseif (($parent_type == 'sitepage_page' || $parent_type == 'sitebusiness_business' || $parent_type == 'sitegroup_group' || $parent_type == 'sitestore_store') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parent_type, 'item_module' => $explodeParentType[0])))) {
                        $roles = array('leader', 'member', 'like_member', 'registered');
                        $getContentOwnerList = 'get' . ucfirst($parentTypeItem->getShortType()) . 'OwnerList';
                        $ownerList = $parentTypeItem->$getContentOwnerList();
                    } elseif (($parent_type == 'sitereview_listing') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitereview_listing_' . $parentTypeItem->listingtype_id, 'item_module' => 'sitereview')))) {
                        $roles = array('leader', 'member', 'registered', 'everyone');
                    }
                }

                if ($values['auth_topic'])
                    $auth_topic = $values['auth_topic'];
                else
                    $auth_topic = "member";
                $topicMax = array_search($auth_topic, $roles);
                $postMax = '';
                if (isset($values['auth_post']) && $values['auth_post'])
                    $auth_post = $values['auth_post'];
                else
                    $auth_post = "member";
                $postMax = array_search($auth_post, $roles);

                if ($values['auth_photo'])
                    $auth_photo = $values['auth_photo'];
                else
                    $auth_photo = "member";
                $photoMax = array_search($auth_photo, $roles);

                if (!isset($values['auth_video']) && empty($values['auth_video'])) {
                    $values['auth_video'] = "member";
                }

                $videoMax = array_search($values['auth_video'], $roles);

                foreach ($roles as $i => $role) {

                    if ($role === 'leader') {
                        $role = $leaderList;
                    }
                    if ($role === 'like_member' && $ownerList) {
                        $role = $ownerList;
                    }

                    $auth->setAllowed($siteevent, $role, "topic", ($i <= $topicMax));
                    if ($postMax)
                        $auth->setAllowed($siteevent, $role, "post", ($i <= $postMax));
                    $auth->setAllowed($siteevent, $role, "photo", ($i <= $photoMax));
                    $auth->setAllowed($siteevent, $role, "video", ($i <= $videoMax));
                }

                // Create some auth stuff for all leaders
                $auth->setAllowed($siteevent, $leaderList, 'photo.edit', 1);
                $auth->setAllowed($siteevent, $leaderList, 'topic.edit', 1);
                $auth->setAllowed($siteevent, $leaderList, 'video.edit', 1);
                $auth->setAllowed($siteevent, $leaderList, 'edit', 1);
                $auth->setAllowed($siteevent, $leaderList, 'delete', 1);

                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventdocument')) {
                    if (empty($values['auth_document'])) {
                        $values['auth_document'] = "member";
                    }
                    $documentMax = array_search($values['auth_document'], $roles);
                    foreach ($roles as $i => $role) {

                        if ($role === 'leader') {
                            $role = $leaderList;
                        }

                        if ($role === 'like_member' && $ownerList) {
                            $role = $ownerList;
                        }
                        $auth->setAllowed($siteevent, $role, "document", ($i <= $documentMax));
                    }

                    $auth->setAllowed($siteevent, $leaderList, 'document.edit', 1);
                }

                if ($previous_category_id != $siteevent->category_id) {
                    Engine_Api::_()->getDbtable('ratings', 'siteevent')->editEventCategory($siteevent->event_id, $previous_category_id, $siteevent->category_id, $siteevent->getType());
                }

                $httpHost = _ENGINE_SSL ? 'https://' : 'http://';
                $viewerGetTitle = $viewer->getTitle();
                $event_title_with_link = '<a href = ' . $httpHost . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('event_id' => $siteevent->event_id, 'slug' => $siteevent->getSlug()), 'siteevent_entry_view') . ">$siteevent->title</a>";

                $sender_link = '<a href = ' . $httpHost . $_SERVER['HTTP_HOST'] . $viewer->getHref() . ">$viewerGetTitle</a>";

                $event_url = $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('event_id' => $siteevent->event_id, 'slug' => $siteevent->getSlug()), 'siteevent_entry_view');
                $newHost = $siteevent->getHost();
                //PACKAGE BASED CHECKS
                $siteevent_pending = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventpaid') ? $siteevent->pending : 0;

                if (empty($siteevent_pending)) {
                    //SEND NOTIFICATION & EMAIL TO HOST - IF PAYMENT NOT PENDING
                    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.host', 1) && $siteevent->host_type == 'user' && $viewer_id != $siteevent->host_id) {
                        if ($newHost && ($editFullEventDate || empty($previousHost) || $previousHost->getType() != $newHost->getType() || $previousHost->getIdentity() != $newHost->getIdentity())) {

                            $row = $siteevent->membership()->getRow($newHost);
                            if (null == $row) {
                                $siteevent->membership()->addMember($newHost)->setUserApproved($newHost);
                                $row = $siteevent->membership()->getRow($newHost);
                                $row->rsvp = 2;
                                $row->save();
                            }

                            //UPDATE THE MEMBER COUNT IN EVENT TABLE
                            $siteevent->member_count = $siteevent->membership()->getMemberCount();
                            $siteevent->save();

                            Engine_Api::_()->getApi('mail', 'core')->sendSystem($newHost->email, 'SITEEVENT_HOST_EMAIL', array(
                                'event_title_with_link' => $event_title_with_link,
                                'sender' => $sender_link,
                                'event_url' => $event_url,
                                'queue' => true
                            ));

                            try {
                                $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                                $occurrence_id = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getAllOccurrenceDates($siteevent->event_id, 1);
                                $notifyApi->addNotification($newHost, $viewer, $siteevent, 'siteevent_host', array('occurrence_id' => $occurrence_id));
                                $notifyApi->addNotification($newHost, $viewer, $siteevent, 'siteevent_member', array('occurrence_id' => $occurrence_id));
                            } catch (Exception $ex) {
                                
                            }

                            //INCREMENT MESSAGE COUNTER.
                            Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');
                        }
                        //CHECK IF NO USER HAS JOINED THIS EVENT YET THEN WE WILL SHOW START DATE IN EDIT MODE ELSE ONLY END DATE AND END REPEAT TIME IN EDIT MODE.
                        $editFullEventDate = true;
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventrepeat')) {
                            $hasEventMember = $siteevent->membership()->hasEventMember($viewer, true);
                            if (Engine_Api::_()->siteevent()->hasTicketEnable()) {
                                $hasEventTicketGuest = Engine_Api::_()->getDbTable('orders', 'siteeventticket')->hasEventTicketGuest($siteevent, $viewer);
                            }
                        }
                        //IF EVENT JOINED / TICKET SOLD THEN CAN NOT EDIT FULL EVENT DATE
                        if (!$hasEventMember || (isset($hasEventTicketGuest) && $hasEventTicketGuest)) {
                            $editFullEventDate = false;
                        }
                        $editFullEventDate = $editFullEventDate;

                        //IF EVENT EDITING IS ALREADY FALSE THAN DO NOT NEED TO CHECK IT FOR CAPACITY
                        if ($editFullEventDate && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.waitlist', 1)) {
                            $totalEventsInWaiting = Engine_Api::_()->getDbTable('waitlists', 'siteevent')->getColumnValue(array('event_id' => $siteevent->getIdentity(), 'columnName' => 'COUNT(*) AS totalEventsInWaiting'));
                            $editFullEventDate = $editFullEventDate = !$totalEventsInWaiting;
                        }

                        //IF EVENT IS NOT FULLY EDITABLE THEN REMOVE THE FORM STARTTIME ELEMENT.
                    } elseif ($siteevent->host_type == 'sitepage_page' && $newHost && (empty($previousHost) || $previousHost->getType() != $newHost->getType() || $previousHost->getIdentity() != $newHost->getIdentity())) {
                        $occurrence_id = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getAllOccurrenceDates($siteevent->event_id, 1);
                        $manageAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->getManageAdmin($siteevent->host_id, $viewer_id);
                        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');

                        foreach ($manageAdmins as $admins) {
                            $newHost = Engine_Api::_()->getItem('user', $admins['user_id']);
                            $sitepage = Engine_Api::_()->getItem('sitepage_page', $admins['page_id']);
                            $item_title_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . $sitepage->getHref();
                            $item_title_link = "<a href='$item_title_baseurl'>" . $sitepage->getTitle() . "</a>";
                            $notifyApi->addNotification($newHost, $viewer, $siteevent, 'siteevent_page_host', array('occurrence_id' => $occurrence_id, 'page' => $item_title_link));
                            Engine_Api::_()->getApi('mail', 'core')->sendSystem($newHost->email, 'SITEEVENT_PAGE_HOST', array(
                                'page_title_with_link' => $item_title_link,
                                'event_title_with_link' => $event_title_with_link,
                                'sender' => $sender_link,
                                'event_url' => $event_url,
                                'queue' => true
                            ));
                        }
                    } elseif ($siteevent->host_type == 'sitebusiness_business' && $newHost && (empty($previousHost) || $previousHost->getType() != $newHost->getType() || $previousHost->getIdentity() != $newHost->getIdentity())) {
                        $occurrence_id = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getAllOccurrenceDates($siteevent->event_id, 1);
                        $manageAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitebusiness')->getManageAdmin($siteevent->host_id, $viewer_id);
                        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');

                        foreach ($manageAdmins as $admins) {
                            $newHost = Engine_Api::_()->getItem('user', $admins['user_id']);
                            $sitebusiness = Engine_Api::_()->getItem('sitebusiness_business', $admins['business_id']);
                            $item_title_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . $sitebusiness->getHref();
                            $item_title_link = "<a href='$item_title_baseurl'>" . $sitebusiness->getTitle() . "</a>";
                            $notifyApi->addNotification($newHost, $viewer, $siteevent, 'siteevent_business_host', array('occurrence_id' => $occurrence_id, 'business' => $item_title_link));

                            Engine_Api::_()->getApi('mail', 'core')->sendSystem($newHost->email, 'SITEEVENT_BUSINESS_HOST', array(
                                'business_title_with_link' => $item_title_link,
                                'event_title_with_link' => $event_title_with_link,
                                'sender' => $sender_link,
                                'event_url' => $event_url,
                                'queue' => true
                            ));
                        }
                    } elseif ($siteevent->host_type == 'sitegroup_group' && $newHost && (empty($previousHost) || $previousHost->getType() != $newHost->getType() || $previousHost->getIdentity() != $newHost->getIdentity())) {
                        $occurrence_id = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getAllOccurrenceDates($siteevent->event_id, 1);
                        $manageAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->getManageAdmin($siteevent->host_id, $viewer_id);
                        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');

                        foreach ($manageAdmins as $admins) {
                            $newHost = Engine_Api::_()->getItem('user', $admins['user_id']);
                            $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $admins['group_id']);
                            $item_title_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . $sitegroup->getHref();
                            $item_title_link = "<a href='$item_title_baseurl'>" . $sitegroup->getTitle() . "</a>";
                            $notifyApi->addNotification($newHost, $viewer, $siteevent, 'siteevent_group_host', array('occurrence_id' => $occurrence_id, 'group' => $item_title_link));

                            Engine_Api::_()->getApi('mail', 'core')->sendSystem($newHost->email, 'SITEEVENT_GROUP_HOST', array(
                                'group_title_with_link' => $item_title_link,
                                'event_title_with_link' => $event_title_with_link,
                                'sender' => $sender_link,
                                'event_url' => $event_url,
                                'queue' => true
                            ));
                        }
                    } elseif ($siteevent->host_type == 'sitestore_store' && $newHost && (empty($previousHost) || $previousHost->getType() != $newHost->getType() || $previousHost->getIdentity() != $newHost->getIdentity())) {
                        $occurrence_id = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getAllOccurrenceDates($siteevent->event_id, 1);
                        $manageAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->getManageAdmin($siteevent->host_id, $viewer_id);
                        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');

                        foreach ($manageAdmins as $admins) {
                            $newHost = Engine_Api::_()->getItem('user', $admins['user_id']);
                            $sitestore = Engine_Api::_()->getItem('sitestore_store', $admins['store_id']);
                            $item_title_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . $sitestore->getHref();
                            $item_title_link = "<a href='$item_title_baseurl'>" . $sitestore->getTitle() . "</a>";
                            $notifyApi->addNotification($newHost, $viewer, $siteevent, 'siteevent_store_host', array('occurrence_id' => $occurrence_id, 'store' => $item_title_link));

                            Engine_Api::_()->getApi('mail', 'core')->sendSystem($newHost->email, 'SITEEVENT_STORE_HOST', array(
                                'store_title_with_link' => $item_title_link,
                                'event_title_with_link' => $event_title_with_link,
                                'sender' => $sender_link,
                                'event_url' => $event_url,
                                'queue' => true
                            ));
                        }
                    }
                }//end - pending check
                // Work for changing host start 
                if (_CLIENT_TYPE && ((_CLIENT_TYPE == 'android' && _ANDROID_VERSION >= '1.8') || (_CLIENT_TYPE == 'ios' && _IOS_VERSION >= '1.5.8'))) {

                    if ($params['host_type'] == 'siteevent_organizer' && (!isset($params['host_id']) || empty($params['host_id'])) && (!isset($params['host_title']) || empty($params['host_title'])))
                        $this->respondWithValidationError('validation_fail', 'Host title missing');

                    if (isset($params['host_id']) && isset($params['host_type'])) {
                        $siteevent->host_id = $params['host_id'];
                        $siteevent->host_type = $params['host_type'];
                        if ($params['host_type'] == 'siteevent_organizer') {
                            $host = Engine_Api::_()->getItem('siteevent_organizer', $params['host_id']);
                            if (isset($params['host_title']) && !empty($params['host_title']))
                                $host->title = $params['host_title'];

                            if (isset($params['host_description']) && !empty($params['host_description']))
                                $host->description = $params['host_description'];

                            if (isset($params['host_facebook']) && !empty($params['host_facebook']))
                                $host->facebook_url = $params['host_facebook'];

                            if (isset($params['host_twitter']) && !empty($params['host_twitter']))
                                $host->twitter_url = $params['host_twitter'];

                            if (isset($params['host_website']) && !empty($params['host_website']))
                                $host->web_url = $params['host_website'];

                            $host->save();

                            if (isset($_FILES['host_photo']) && !empty($_FILES['host_photo']))
                                $host = Engine_Api::_()->getApi('Siteapi_Core', 'siteevent')->setPhotoForOrganizer($_FILES['host_photo'], $host);

                            $host->save();
                        }
                        $siteevent->save();
                    }
                    else if (isset($params['host_title']) && $params['host_title']) {
                        $table = Engine_Api::_()->getItemTable('siteevent_organizer');
                        $db = $table->getAdapter();
                        $db->beginTransaction();
                        $host = $table->createRow();
                        $hostInfo = array(
                            'title' => $params['host_title'],
                            'description' => isset($params['host_description']) && $params['host_description'] ? $params['host_description'] : null,
                            'creator_id' => $viewer_id,
                            'facebook_url' => isset($params['host_facebook']) && $params['host_facebook'] ? $params['host_facebook'] : "",
                            'twitter_url' => isset($params['host_twitter']) && $params['host_twitter'] ? $params['host_twitter'] : "",
                            'web_url' => isset($params['host_website']) && $params['host_website'] ? $params['host_website'] : "",
                        );
                        $host->setFromArray($hostInfo);
                        $host->save();

                        if (isset($_FILES['host_photo']) && !empty($_FILES['host_photo']))
                            $host = Engine_Api::_()->getApi('Siteapi_Core', 'siteevent')->setPhotoForOrganizer($_FILES['host_photo'], $host);

                        $siteevent->host_type = $host->getType();
                        $siteevent->host_id = $host->getIdentity();
                        $siteevent->save();
                    }
                }
                // work for changing host ends
                //EDIT THE TICKETS SELL ENDTIME
                if (Engine_Api::_()->siteevent()->hasTicketEnable()) {
                    Engine_Api::_()->siteeventticket()->updateTicketsSellEndTime($siteevent);
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
            }

            $siteevent->setLocation();
            $db->beginTransaction();
            try {
                $actionTable = Engine_Api::_()->getDbtable('actions', 'seaocore');
                foreach ($actionTable->getActionsByObject($siteevent) as $action) {
                    $actionTable->resetActivityBindings($action);
                }
                $db->commit();
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollBack();
            }
        }
    }

    public function packagesAction() {
        //ONLY LOGGED IN USER CAN VIEW THIS PAGE
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        // CHECK FOR PERMISSION OF CREATE EVENT
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, "create")->isValid())
            $this->respondWithError('unauthorized');
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        $viewer_id = $viewer->getIdentity();

        $overview = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.overview', 0);


        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $bodyParams = array();
        $packageInfoArray = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.package.information', array("price", "billing_cycle", "duration", "featured", "sponsored", "rich_overview", "videos", "photos", "description", "ticket_type"));
        $package_id = $this->getRequestParam('package_id', 0);
//        if (isset($package_id) && !empty($package_id)) {
//            $package = Engine_Api::_()->getItemTable('siteeventpaid_package')->fetchRow(array('package_id = ?' => $package_id));
//
//            if (!isset($package) && empty($package))
//                $this->respondWithError('no_record');
//
//            if (in_array('price', $packageInfoArray)) {
//                if ($package->price > 0.00) {
//                    $packageShowArray['price']['label'] = $this->translate('Price');
//                    $packageShowArray['price']['value'] = $package->price;
//                    $packageShowArray['price']['currency'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
//                } else {
//                    $packageShowArray['price']['label'] = $this->translate('Price');
//                    $packageShowArray['price']['value'] = $this->translate('FREE');
//                }
//            }
//
//            if (Engine_Api::_()->siteevent()->hasTicketEnable() && in_array('ticket_type', $packageInfoArray)) {
//                if ($package->ticket_type) {
//                    $packageShowArray['ticket_type']['label'] = $this->translate('Ticket Types');
//                    $packageShowArray['ticket_type']['value'] = $this->translate("PAID & FREE");
//                } else {
//                    $packageShowArray['ticket_type']['label'] = $this->translate('Ticket Types');
//                    $packageShowArray['ticket_type']['value'] = $this->translate('FREE');
//                }
//            }
//
//            if (in_array('billing_cycle', $packageInfoArray)) {
//                $packageShowArray['billing_cycle']['label'] = $this->translate('Billing Cycle');
//                $packageShowArray['billing_cycle']['value'] = $package->getBillingCycle();
//            }
//            if (in_array('duration', $packageInfoArray)) {
//                $packageShowArray['duration']['label'] = $this->translate("Duration");
//                $packageShowArray['duration']['value'] = $package->getPackageQuantity();
//            }
//
//            if (in_array('featured', $packageInfoArray)) {
//                if ($package->featured == 1) {
//                    $packageShowArray['featured']['label'] = $this->translate('Featured');
//                    $packageShowArray['featured']['value'] = $this->translate('Yes');
//                } else {
//                    $packageShowArray['featured']['label'] = $this->translate('Featured');
//                    $packageShowArray['featured']['value'] = $this->translate('No');
//                }
//            }
//
//            if (in_array('sponsored', $packageInfoArray)) {
//                if ($package->sponsored == 1) {
//                    $packageShowArray['sponsored']['label'] = $this->translate('Sponsored');
//                    $packageShowArray['sponsored']['value'] = $this->translate('Yes');
//                } else {
//                    $packageShowArray['sponsored']['label'] = $this->translate('Sponsored');
//                    $packageShowArray['sponsored']['value'] = $this->translate('No');
//                }
//            }
//
//            if (in_array('rich_overview', $packageInfoArray) && ($overview && (empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "overview")))) {
//                if ($package->overview == 1) {
//                    $packageShowArray['rich_overview']['label'] = $this->translate('Rich Overview');
//                    $packageShowArray['rich_overview']['value'] = $this->translate('Yes');
//                } else {
//                    $packageShowArray['rich_overview']['label'] = $this->translate('Rich Overview');
//                    $packageShowArray['rich_overview']['value'] = $this->translate('No');
//                }
//            }
//
//            if (in_array('videos', $packageInfoArray) && (empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "video"))) {
//                if ($package->video == 1) {
//                    if ($package->video_count) {
//                        $packageShowArray['videos']['label'] = $this->translate('Videos');
//                        $packageShowArray['videos']['value'] = $package->video_count;
//                    } else {
//                        $packageShowArray['videos']['label'] = $this->translate('Videos');
//                        $packageShowArray['videos']['value'] = $this->translate("Unlimited");
//                    }
//                } else {
//                    $packageShowArray['videos']['label'] = $this->translate('Videos');
//                    $packageShowArray['videos']['value'] = $this->translate('No');
//                }
//            }
//
//            if (in_array('photos', $packageInfoArray) && (empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "photo"))) {
//                if ($package->photo == 1) {
//                    if ($packagem->photo_count) {
//                        $packageShowArray['photos']['label'] = $this->translate('Photos');
//                        $packageShowArray['photos']['value'] = $package->photo_count;
//                    } else {
//                        $packageShowArray['photos']['label'] = $this->translate('Photos');
//                        $packageShowArray['photos']['value'] = $this->translate("Unlimited");
//                    }
//                } else {
//                    $packageShowArray['photos']['label'] = $this->translate('Photos');
//                    $packageShowArray['photos']['value'] = $this->translate('No');
//                }
//            }
//
//            if (Engine_Api::_()->siteevent()->hasTicketEnable() && in_array('commission', $packageInfoArray)) {
//                if (!empty($package->ticket_settings)) {
//                    $siteeventticketInfo = @unserialize($package->ticket_settings);
//                    $commissionType = $siteeventticketInfo['commission_handling'];
//                    $commissionFee = $siteeventticketInfo['commission_fee'];
//                    $commissionRate = $siteeventticketInfo['commission_rate'];
//                }
//                if (!empty($package->ticket_settings) && isset($commissionType)) {
//                    if (empty($commissionType)) {
//                        $packageShowArray['commission']['label'] = $this->translate('Commission');
//                        $packageShowArray['commission']['value'] = $commissionFee;
//                        $packageShowArray['commission']['value'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
//                    } else {
//                        $packageShowArray['commission']['label'] = $this->translate('Commission');
//                        $packageShowArray['commission']['value'] = $commissionRate . '%';
//                    }
//                } else {
//                    $packageShowArray['commission']['label'] = $this->translate('Commission');
//                    $packageShowArray['commission']['value'] = $this->translate("N/A");
//                }
//            }
//            if (in_array('description', $packageInfoArray)) {
//                $packageShowArray['description']['label'] = $this->translate("Description");
//                $packageShowArray['description']['value'] = $this->translate($package->description);
//            }
//
//            if (isset($packageShowArray) && !empty($packageShowArray))
//                $response['package'] = array_merge($response, $packageShowArray);
//
//            if (isset($response) && !empty($response))
//                $this->respondWithSuccess($response);
//        }


        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventpaid') && $this->_hasPackageEnable) {
            $packageCount = Engine_Api::_()->getDbTable('packages', 'siteeventpaid')->getPackageCount();
            if ($packageCount == 1) {
                $package = Engine_Api::_()->getDbTable('packages', 'siteeventpaid')->getEnabledPackage();
                if (($package->price == '0.00')) {
                    $bodyParams['getTotalItemCount'] = 0;
                    $this->respondWithSuccess($bodyParams);
                }
                $bodyParams["getTotalItemCount"] = 1;

                if (isset($package->package_id) && !empty($package->package_id))
                    $packageShowArray['package_id'] = $package->package_id;

                if (isset($package->title) && !empty($package->title)) {
                    $packageShowArray['title']['label'] = $this->translate('Title');
                    $packageShowArray['title']['value'] = $this->translate($package->title);
                }


                if (in_array('price', $packageInfoArray)) {
                    if ($package->price > 0.00) {
                        $packageShowArray['price']['label'] = $this->translate('Price');
                        $packageShowArray['price']['value'] = $package->price;
                        $packageShowArray['price']['currency'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
                    } else {
                        $packageShowArray['price']['label'] = $this->translate('Price');
                        $packageShowArray['price']['value'] = $this->translate('FREE');
                    }
                }

                if (Engine_Api::_()->siteevent()->hasTicketEnable() && in_array('ticket_type', $packageInfoArray)) {
                    if ($package->ticket_type) {
                        $packageShowArray['ticket_type']['label'] = $this->translate('Ticket Types');
                        $packageShowArray['ticket_type']['value'] = $this->translate("PAID & FREE");
                    } else {
                        $packageShowArray['ticket_type']['label'] = $this->translate('Ticket Types');
                        $packageShowArray['ticket_type']['value'] = $this->translate('FREE');
                    }
                }

                if (in_array('billing_cycle', $packageInfoArray)) {
                    $packageShowArray['billing_cycle']['label'] = $this->translate('Billing Cycle');
                    $packageShowArray['billing_cycle']['value'] = $package->getBillingCycle();
                }
                if (in_array('duration', $packageInfoArray)) {
                    $packageShowArray['duration']['label'] = $this->translate("Duration");
                    $packageShowArray['duration']['value'] = $package->getPackageQuantity();
                }

                if (in_array('featured', $packageInfoArray)) {
                    if ($package->featured == 1) {
                        $packageShowArray['featured']['label'] = $this->translate('Featured');
                        $packageShowArray['featured']['value'] = $this->translate('Yes');
                    } else {
                        $packageShowArray['featured']['label'] = $this->translate('Featured');
                        $packageShowArray['featured']['value'] = $this->translate('No');
                    }
                }

                if (in_array('sponsored', $packageInfoArray)) {
                    if ($package->sponsored == 1) {
                        $packageShowArray['Sponsored']['label'] = $this->translate('Sponsored');
                        $packageShowArray['Sponsored']['value'] = $this->translate('Yes');
                    } else {
                        $packageShowArray['Sponsored']['label'] = $this->translate('Sponsored');
                        $packageShowArray['Sponsored']['value'] = $this->translate('No');
                    }
                }

                if (in_array('rich_overview', $packageInfoArray) && ($overview && (empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "overview")))) {
                    if ($package->overview == 1) {
                        $packageShowArray['rich_overview']['label'] = $this->translate('Rich Overview');
                        $packageShowArray['rich_overview']['value'] = $this->translate('Yes');
                    } else {
                        $packageShowArray['rich_overview']['label'] = $this->translate('Rich Overview');
                        $packageShowArray['rich_overview']['value'] = $this->translate('No');
                    }
                }

                if (in_array('videos', $packageInfoArray) && (empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "video"))) {
                    if ($package->video == 1) {
                        if ($package->video_count) {
                            $packageShowArray['videos']['label'] = $this->translate('Videos');
                            $packageShowArray['videos']['value'] = $package->video_count;
                        } else {
                            $packageShowArray['videos']['label'] = $this->translate('Videos');
                            $packageShowArray['videos']['value'] = $this->translate("Unlimited");
                        }
                    } else {
                        $packageShowArray['videos']['label'] = $this->translate('Videos');
                        $packageShowArray['videos']['value'] = $this->translate('No');
                    }
                }

                if (in_array('photos', $packageInfoArray) && (empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "photo"))) {
                    if ($package->photo == 1) {
                        if ($packagem->photo_count) {
                            $packageShowArray['photos']['label'] = $this->translate('Photos');
                            $packageShowArray['photos']['value'] = $package->photo_count;
                        } else {
                            $packageShowArray['photos']['label'] = $this->translate('Photos');
                            $packageShowArray['photos']['value'] = $this->translate("Unlimited");
                        }
                    } else {
                        $packageShowArray['photos']['label'] = $this->translate('Photos');
                        $packageShowArray['photos']['value'] = $this->translate('No');
                    }
                }

                if (Engine_Api::_()->siteevent()->hasTicketEnable() && in_array('commission', $packageInfoArray)) {
                    if (!empty($package->ticket_settings)) {
                        $siteeventticketInfo = @unserialize($package->ticket_settings);
                        $commissionType = $siteeventticketInfo['commission_handling'];
                        $commissionFee = $siteeventticketInfo['commission_fee'];
                        $commissionRate = $siteeventticketInfo['commission_rate'];
                    }
                    if (!empty($package->ticket_settings) && isset($commissionType)) {
                        if (empty($commissionType)) {
                            $packageShowArray['commission']['label'] = $this->translate('Commission');
                            $packageShowArray['commission']['value'] = $commissionFee;
                            $packageShowArray['commission']['value'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
                        } else {
                            $packageShowArray['commission']['label'] = $this->translate('Commission');
                            $packageShowArray['commission']['value'] = $commissionRate . '%';
                        }
                    } else {
                        $packageShowArray['commission']['label'] = $this->translate('Commission');
                        $packageShowArray['commission']['value'] = $this->translate("N/A");
                    }
                }
                if (in_array('description', $packageInfoArray)) {
                    $packageShowArray['description']['label'] = $this->translate("Description");
                    $packageShowArray['description']['value'] = $this->translate($package->description);
                }

                $packageArray['response']['package'] = $packageShowArray;
                $tempMenu = array();
                $tempMenu[] = array(
                    'label' => $this->translate('Create Event'),
                    'name' => 'create',
                    'url' => 'advancedevents/create',
                    'urlParams' => array(
                        'package_id' => $package->package_id
                    )
                );
                $tempMenu[] = array(
                    'label' => $this->translate('Package Info'),
                    'name' => 'package_info',
                    'url' => 'advancedevents/packages',
                    'urlParams' => array(
                        'package_id' => $package->package_id
                    )
                );

                $packageArray['response']['menu'] = $tempMenu;

                if (isset($packageArray) && !empty($packageArray))
                    $this->respondWithSuccess($packageArray);
            } else {
                $overview = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.overview', 1);
                $package_description = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.package.description', 1);
                $paginator = Engine_Api::_()->getDbtable('packages', 'siteeventpaid')->getPackagesSql($viewer_id);
                $bodyParams["getTotalItemCount"] = $paginator->getTotalItemCount();
                foreach ($paginator as $package) {
                    $packageShowArray = array();

                    if (isset($package->package_id) && !empty($package->package_id))
                        $packageShowArray['package_id'] = $package->package_id;

                    if (isset($package->title) && !empty($package->title)) {
                        $packageShowArray['title']['label'] = $this->translate('Title');
                        $packageShowArray['title']['value'] = $this->translate($package->title);
                    }

                    if (in_array('price', $packageInfoArray)) {
                        if ($package->price > 0.00) {
                            $packageShowArray['price']['label'] = $this->translate('Price');
                            $packageShowArray['price']['value'] = $package->price;
                            $packageShowArray['price']['currency'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
                        } else {
                            $packageShowArray['price']['label'] = $this->translate('Price');
                            $packageShowArray['price']['value'] = $this->translate('FREE');
                        }
                    }

                    if (Engine_Api::_()->siteevent()->hasTicketEnable() && in_array('ticket_type', $packageInfoArray)) {
                        if ($package->ticket_type) {
                            $packageShowArray['ticket_type']['label'] = $this->translate('Ticket Types');
                            $packageShowArray['ticket_type']['value'] = $this->translate("PAID & FREE");
                        } else {
                            $packageShowArray['ticket_type']['label'] = $this->translate('Ticket Types');
                            $packageShowArray['ticket_type']['value'] = $this->translate('FREE');
                        }
                    }

                    if (in_array('billing_cycle', $packageInfoArray)) {
                        $packageShowArray['billing_cycle']['label'] = $this->translate('Billing Cycle');
                        $packageShowArray['billing_cycle']['value'] = $package->getBillingCycle();
                    }
                    if (in_array('duration', $packageInfoArray)) {
                        $packageShowArray['duration']['label'] = $this->translate("Duration");
                        $packageShowArray['duration']['value'] = $package->getPackageQuantity();
                    }

                    if (in_array('featured', $packageInfoArray)) {
                        if ($package->featured == 1) {
                            $packageShowArray['featured']['label'] = $this->translate('Featured');
                            $packageShowArray['featured']['value'] = $this->translate('Yes');
                        } else {
                            $packageShowArray['featured']['label'] = $this->translate('Featured');
                            $packageShowArray['featured']['value'] = $this->translate('No');
                        }
                    }

                    if (in_array('sponsored', $packageInfoArray)) {
                        if ($package->sponsored == 1) {
                            $packageShowArray['Sponsored']['label'] = $this->translate('Sponsored');
                            $packageShowArray['Sponsored']['value'] = $this->translate('Yes');
                        } else {
                            $packageShowArray['Sponsored']['label'] = $this->translate('Sponsored');
                            $packageShowArray['Sponsored']['value'] = $this->translate('No');
                        }
                    }

                    if (in_array('rich_overview', $packageInfoArray) && ($overview && (empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "overview")))) {
                        if ($package->overview == 1) {
                            $packageShowArray['rich_overview']['label'] = $this->translate('Rich Overview');
                            $packageShowArray['rich_overview']['value'] = $this->translate('Yes');
                        } else {
                            $packageShowArray['rich_overview']['label'] = $this->translate('Rich Overview');
                            $packageShowArray['rich_overview']['value'] = $this->translate('No');
                        }
                    }

                    if (in_array('videos', $packageInfoArray) && (empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "video"))) {
                        if ($package->video == 1) {
                            if ($package->video_count) {
                                $packageShowArray['videos']['label'] = $this->translate('Videos');
                                $packageShowArray['videos']['value'] = $package->video_count;
                            } else {
                                $packageShowArray['videos']['label'] = $this->translate('Videos');
                                $packageShowArray['videos']['value'] = $this->translate("Unlimited");
                            }
                        } else {
                            $packageShowArray['videos']['label'] = $this->translate('Videos');
                            $packageShowArray['videos']['value'] = $this->translate('No');
                        }
                    }

                    if (in_array('photos', $packageInfoArray) && (empty($level_id) || Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "photo"))) {
                        if ($package->photo == 1) {
                            if ($packagem->photo_count) {
                                $packageShowArray['photos']['label'] = $this->translate('Photos');
                                $packageShowArray['photos']['value'] = $package->photo_count;
                            } else {
                                $packageShowArray['photos']['label'] = $this->translate('Photos');
                                $packageShowArray['photos']['value'] = $this->translate("Unlimited");
                            }
                        } else {
                            $packageShowArray['photos']['label'] = $this->translate('Photos');
                            $packageShowArray['photos']['value'] = $this->translate('No');
                        }
                    }

                    if (Engine_Api::_()->siteevent()->hasTicketEnable() && in_array('commission', $packageInfoArray)) {
                        if (!empty($package->ticket_settings)) {
                            $siteeventticketInfo = @unserialize($package->ticket_settings);
                            $commissionType = $siteeventticketInfo['commission_handling'];
                            $commissionFee = $siteeventticketInfo['commission_fee'];
                            $commissionRate = $siteeventticketInfo['commission_rate'];
                        }
                        if (!empty($package->ticket_settings) && isset($commissionType)) {
                            if (empty($commissionType)) {
                                $packageShowArray['commission']['label'] = $this->translate('Commission');
                                $packageShowArray['commission']['value'] = $commissionFee;
                                $packageShowArray['commission']['value'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
                            } else {
                                $packageShowArray['commission']['label'] = $this->translate('Commission');
                                $packageShowArray['commission']['value'] = $commissionRate . '%';
                            }
                        } else {
                            $packageShowArray['commission']['label'] = $this->translate('Commission');
                            $packageShowArray['commission']['value'] = $this->translate("N/A");
                        }
                    }
                    if (in_array('description', $packageInfoArray)) {
                        $packageShowArray['description']['label'] = $this->translate("Description");
                        $packageShowArray['description']['value'] = $this->translate($package->description);
                    }

                    $packageArray["package"] = $packageShowArray;
                    $tempMenu = array();
                    $tempMenu[] = array(
                        'label' => $this->translate('Create Event'),
                        'name' => 'create',
                        'url' => 'advancedevents/create',
                        'urlParams' => array(
                            'package_id' => $package->package_id
                        )
                    );
                    $tempMenu[] = array(
                        'label' => $this->translate('Package Info'),
                        'name' => 'package_info',
                        'url' => 'advancedevents/packages',
                        'urlParams' => array(
                            'package_id' => $package->package_id
                        )
                    );

                    $packageArray['menu'] = $tempMenu;
                    $bodyParams['response'][] = $packageArray;
                }

                if (isset($bodyParams) && !empty($bodyParams))
                    $this->respondWithSuccess($bodyParams);
            }
        }

        $bodyParams['getTotalItemCount'] = 0;
        $this->respondWithSuccess($bodyParams);
    }

    public function createAction() {

        //ONLY LOGGED IN USER CAN VIEW THIS PAGE
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        // CHECK FOR PERMISSION OF CREATE EVENT
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, "create")->isValid())
            $this->respondWithError('unauthorized');

        $request = Zend_Controller_Front::getInstance()->getRequest();

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        $viewer_id = $viewer->getIdentity();

        try {

            $settings = Engine_Api::_()->getApi('settings', 'core');

            $subject = $viewer = Engine_Api::_()->user()->getViewer();
            $viewer_id = $viewer->getIdentity();

            //GET DEFAULT PROFILE TYPE ID
            $defaultProfileId = Engine_Api::_()->getDbTable('metas', 'siteevent')->defaultProfileId();
            $parent_type = $this->_getParam('parent_type', 'user');
            $parent_id = $this->_getParam('parent_id', $viewer_id);

            $parentTypeItem = Engine_Api::_()->getItem($parent_type, $parent_id);

            $siteeventParentPrivacy = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventparent.privacy', 1);

            $isCreatePrivacy = Engine_Api::_()->siteevent()->isCreatePrivacy($parent_type, $parent_id);

            $host = $viewer;

            if (empty($siteeventParentPrivacy) || empty($isCreatePrivacy))
                $this->respondWithError('unauthorized');

            $host_icons = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($host);

            //PACKAGE BASED CHECKS
            if (_CLIENT_TYPE && ((_CLIENT_TYPE == 'android' && _ANDROID_VERSION >= '1.6.3') || _CLIENT_TYPE == 'ios' && _IOS_VERSION >= '1.5.1')) {
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventpaid') && $this->_hasPackageEnable) {
                    $packageCount = Engine_Api::_()->getDbTable('packages', 'siteeventpaid')->getPackageCount();
                    if ($packageCount == 1) {
                        $package = Engine_Api::_()->getDbTable('packages', 'siteeventpaid')->getEnabledPackage();
                        if (($package->price == '0.00')) {
                            $package_id = $package->package_id;
                        } else {
                            $package_id = $this->getRequestParam('package_id', 0);
                            $package = Engine_Api::_()->getItemTable('siteeventpaid_package')->fetchRow(array('package_id = ?' => $package_id));

                            if (!isset($package) && empty($package))
                                $this->respondWithError('siteevent_package_error');
                        }
                    } else {
                        $package_id = $this->getRequestParam('package_id', 0);
                        $package = Engine_Api::_()->getItemTable('siteeventpaid_package')->fetchRow(array('package_id = ?' => $package_id));

                        if (!isset($package) && empty($package))
                            $this->respondWithError('siteevent_package_error');
                    }
                } else if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventpaid')) {
                    $package_id = 1;
                    $package = Engine_Api::_()->getItemTable('siteeventpaid_package')->fetchRow(array('package_id = ?' => $package_id));
                } else {
                    $package_id = 0;
                }
            } else {
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventpaid') && $this->_hasPackageEnable) {
                    $this->respondWithError('siteevent_package_error');
                } else if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventpaid')) {
                    $package_id = 1;
                    $package = Engine_Api::_()->getItemTable('siteeventpaid_package')->fetchRow(array('package_id = ?' => $package_id));
                } else {
                    $package_id = 0;
                }
            }

//GET EVENT CREATE FORM
            if ($this->getRequest()->isGet()) {
                $response = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->getForm(null, $parent_type, $parent_id, $host, $host_icons);
                $this->respondWithSuccess($response, true);
            }


            $listValues = array();

//COUNT SITEEVENT CREATED BY THIS USER AND GET ALLOWED COUNT SETTINGS
            $values['user_id'] = $viewer_id;
            $paginator = Engine_Api::_()->getDbTable('events', 'siteevent')->getSiteeventsPaginator($values);

            $siteeventOccurrenceEmailViewType = Engine_Api::_()->siteevent()->isEnabled();
            $current_count = $paginator->getTotalItemCount();
            $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'siteevent_event', "max");

            $category_count = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategories(array('category_id'), null, 1, 0, 1);

            if ($this->getRequest()->isPost()) {

                if ($current_count > $quota)
                    $this->respondWithError('unauthorized');

                $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->getForm(null, $parent_type, $parent_id, $host_icons);
                $values = $data = $_REQUEST;
                foreach ($getForm['form'] as $element) {
                    if (isset($_REQUEST[$element['name']]))
                        $values[$element['name']] = $_REQUEST[$element['name']];
                }

// START FORM VALIDATION
                $eventValidators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'siteevent')->getFormValidators($values);
                $values['validators'] = $eventValidators;

                $eventValidationMessage = $this->isValid($values);
                if (!@is_array($validationMessage) && isset($values['category_id'])) {

                    $categoryIds = array();
                    $categoryIds[] = $values['category_id'];
                    $categoryIds[] = $values['subcategory_id'];
                    $categoryIds[] = $values['subsubcategory_id'];

                    try {
                        $values['profile_type'] = Engine_Api::_()->getDbTable('categories', 'siteevent')->getProfileType($categoryIds, 0, 'profile_type');
                    } catch (Exception $ex) {
                        $values['profile_type'] = 0;
                    }
                    if (isset($values['profile_type']) && !empty($values['profile_type'])) {

                        // START FORM VALIDATION
                        $profileFieldsValidators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'siteevent')->getFieldsFormValidations($values);
                        $values['validators'] = $profileFieldsValidators;
                        $profileFieldsValidationMessage = $this->isValid($values);
                    }
                }
                if (is_array($eventValidationMessage) && is_array($profileFieldsValidationMessage))
                    $validationMessage = array_merge($eventValidationMessage, $profileFieldsValidationMessage);
                else if (is_array($eventValidationMessage))
                    $validationMessage = $eventValidationMessage;
                else if (is_array($profileFieldsValidationMessage))
                    $validationMessage = $profileFieldsValidationMessage;
                else
                    $validationMessage = 1;

                if (!empty($validationMessage) && @is_array($validationMessage)) {
                    $this->respondWithValidationError('validation_fail', $validationMessage);
                }

                $values['is_online'] = empty($values['is_online']) ? 0 : $values['is_online'];
                $values['host_type'] = $parent_type;
                $values['host_id'] = $parent_id;

                if (empty($values['starttime'])) {
                    $values['starttime'] = date('Y-m-d H:i:s', time());
                }
                if (empty($values['endtime'])) {
                    $values['endtime'] = date('Y-m-d H:i:s', time() + 4 * 3600);
                }

//CHECK EITHER THE EVENT STARTTIME AND ENDTIME EXIST FOR THIS EVENT OR NOT. IF NOT THEN SHOW THE ERROR.
                $table = Engine_Api::_()->getItemTable('siteevent_event');
                $db = $table->getAdapter();
                $db->beginTransaction();
                $user_level = $viewer->level_id;
                try {
//Create siteevent
                    if (!$this->_hasPackageEnable) {
                        //Create siteevent
                        $values = array_merge($values, array(
                            'owner_type' => $viewer->getType(),
                            'owner_id' => $viewer_id,
                            'featured' => Engine_Api::_()->authorization()->getPermission($user_level, 'siteevent_event', "featured"),
                            'sponsored' => Engine_Api::_()->authorization()->getPermission($user_level, 'siteevent_event', "sponsored"),
                            'approved' => Engine_Api::_()->authorization()->getPermission($user_level, 'siteevent_event', "approved")
                        ));
                    } else {
                        $values = array_merge($values, array(
                            'owner_type' => $viewer->getType(),
                            'owner_id' => $viewer_id,
                            'featured' => $package->featured,
                            'sponsored' => $package->sponsored
                        ));

                        if ($package->isFree()) {
                            $values['approved'] = $package->approved;
                        } else
                            $values['approved'] = 0;
                    }

                    if (empty($values['subcategory_id'])) {
                        $values['subcategory_id'] = 0;
                    }

                    if (empty($values['subsubcategory_id'])) {
                        $values['subsubcategory_id'] = 0;
                    }

//check if admin has disabled "approval" for RSVP to be invited.
                    if (!isset($values['approval']))
                        $values['approval'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.invite.rsvp.automatically', 1);

//check if admin has disabled "auth_invite" for event members to invite other people
                    if (!isset($values['auth_invite']))
                        $values['auth_invite'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.invite.other.automatically', 1);
                    if (Engine_Api::_()->siteevent()->listBaseNetworkEnable()) {
                        if (isset($values['networks_privacy']) && !empty($values['networks_privacy'])) {
                            if (in_array(0, $values['networks_privacy'])) {
                                unset($values['networks_privacy']);
                            }
                        }
                    }

// Convert times
                    $oldTz = date_default_timezone_get();
                    date_default_timezone_set($viewer->timezone);
                    date_default_timezone_set($oldTz);
                    if (!empty($values['starttime'])) {
                        $start = strtotime($values['starttime']);
                        $values['starttime'] = date('Y-m-d H:i:s', $start);
                    }
                    if (!empty($values['endtime'])) {
                        $end = strtotime($values['endtime']);
                        $values['endtime'] = date('Y-m-d H:i:s', $end);
                    }
                    $values['draft'] = empty($values['draft']) ? 0 : $values['draft'];
                    $values['approved'] = empty($values['approved']) ? 0 : $values['approved'];
                    $values['closed'] = empty($values['closed']) ? 0 : $values['closed'];
                    $values['search'] = empty($values['search']) ? 1 : $values['search'];

//check if event creater has added any host details there.
                    $siteevent = $table->createRow();
                    $values['parent_type'] = $parent_type;
                    $values['parent_id'] = $parent_id;
//IF EVENT IS ONLY THEN LOCATION FIELD SHOULD BE EMPTY
                    if (!empty($values['is_online'])) {
                        $values['location'] = '';
                    } else {
                        $values['is_online'] = 0;
                    }
                    $siteevent->setFromArray($values);

                    if ($siteevent->approved) {
                        $siteevent->approved_date = date('Y-m-d H:i:s');
                        //START PACKAGE WORK
                        if (isset($siteevent->pending)) {
                            $siteevent->pending = 0;
                        }
                        if ($this->_hasPackageEnable) {
                            $expirationDate = $package->getExpirationDate();
                            if (!empty($expirationDate))
                                $siteevent->expiration_date = date('Y-m-d H:i:s', $expirationDate);
                            else
                                $siteevent->expiration_date = '2250-01-01 00:00:00';
                        }
                        //END PACKAGE WORK
                    }

                    $siteevent->save();

                    if (isset($siteevent->package_id)) {
                        $siteevent->package_id = $package_id;
                    }
//MAKE THE SERIALIZE ARRAY OF REPEAT DATE INFO:
//                    $repeatEventInfo = Engine_Api::_()->siteevent()->getRepeatEventInfo($_POST, 0);
//                    if (!empty($repeatEventInfo)) {
//                        //CONVERT TO CORRECT DATE FORMAT
//                        if (isset($repeatEventInfo['endtime']))
//                            $repeatEventInfo['endtime']['date'] = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->convertDateFormat($repeatEventInfo['endtime']['date']);
//                        $siteevent->repeat_params = json_encode($repeatEventInfo);
//                    } else
//                        $siteevent->repeat_params = '';
//                    $siteevent->save();
//                    $event_id = $siteevent->event_id;
//SET PHOTO
                    if (!empty($_FILES)) {
                        Engine_Api::_()->getApi('Siteapi_Core', 'siteevent')->setPhoto($_FILES['photo'], $siteevent);
                        $albumTable = Engine_Api::_()->getDbtable('albums', 'siteevent');
                        $album_id = $albumTable->update(array('photo_id' => $siteevent->photo_id), array('event_id = ?' => $siteevent->event_id));
                    }

//ADDING TAGS
                    $keywords = '';
                    if (isset($values['tags']) && !empty($values['tags'])) {
                        $tags = preg_split('/[,]+/', $values['tags']);
                        $tags = array_filter(array_map("trim", $tags));
                        $siteevent->tags()->addTagMaps($viewer, $tags);

                        foreach ($tags as $tag) {
                            $keywords .= " $tag";
                        }
                    }

//NOT SEARCHABLE IF SAVED IN DRAFT MODE
                    if (!empty($siteevent->draft)) {
                        $siteevent->search = 0;
                    }

                    $siteevent->save();

                    Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();
                    Engine_Api::_()->getApi('Core', 'siteapi')->setView();
                    if ((isset($_POST['eventrepeat_id'])) && ($_POST['eventrepeat_id'] == 'weekly' || $_POST['eventrepeat_id'] == 'monthly') || ($_POST['eventrepeat_id'] == 'daily')) {

                        if ($_POST['eventrepeat_id'] == 'weekly') {
                            $_POST['id_weekly-repeat_interval'] = $values['repeat_week'];
                            if (isset($values['repeat_weekday'])) {
                                $weekdays = array(1 => 'monday', 2 => 'tuesday', 3 => 'wednesday', 4 => 'thursday', 5 => 'friday', 6 => 'saturday', 7 => 'sunday');

                                $values['repeat_weekday'] = array_map('intval', explode(',', $values['repeat_weekday']));
                                foreach ($values['repeat_weekday'] as $weekday) {
                                    $_POST['weekly-repeat_on_' . $weekdays[$weekday]] = $weekdays[$weekday];
                                }
                            }

                            if (isset($_POST['date']) && !empty($_POST['date'])) {
                                $_POST['weekly_repeat_time'] = array(
                                    'date' => $_POST['date']
                                );
                            }
                            $isValidOccurrences = Engine_Api::_()->siteevent()->checkValidOccurrences($values);

                            if (!$isValidOccurrences) {
                                $errorMessage = array();
                                $errorMessage[] = $this->translate('Please make sure you have selected the correct time interval - it is required');
                                $this->respondWithValidationError('validation_fail', $errorMessage);
                            } else {
                                $repeatEventInfo['repeat_interval'] = 0;
                                $repeatEventInfo['repeat_week'] = $values['repeat_week'];
                                $repeatEventInfo['repeat_weekday'] = $values['repeat_weekday'];
                                $repeatEventInfo['eventrepeat_type'] = $values['eventrepeat_id'];
                                $repeatEventInfo['endtime']['date'] = $values['date'];
                            }
                        }
                        if ($_POST['eventrepeat_id'] == 'monthly') {
                            $repeatEventInfo['repeat_interval'] = 0;
                            $repeatEventInfo['repeat_week'] = $values['repeat_week'];
                            $repeatEventInfo['repeat_weekday'] = $values['repeat_weekday'];
                            $repeatEventInfo['eventrepeat_type'] = $values['eventrepeat_id'];
                            $repeatEventInfo['endtime']['date'] = $values['date'];
                            $repeatEventInfo['repeat_month'] = $values['repeat_month'];
                            $_POST['monthly_repeat_time']['date'] = $values['date'];
                            $completeEventInfo = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->getRepeatEventCompleteInfo($_POST, $repeatEventInfo, 0);
                            $_POST = $completeEventInfo;
                            $isValidOccurrences = Engine_Api::_()->siteevent()->checkValidOccurrences($values);
                        }
                        if ($_POST['eventrepeat_id'] == 'daily') {
                            $repeatEventInfo['repeat_interval'] = $values['repeat_interval'] * (24 * 3600);
                            $repeatEventInfo['eventrepeat_type'] = $values['eventrepeat_id'];
                            $repeatEventInfo['endtime']['date'] = $values['date'];
                            $isValidOccurrences = true;
                        }
                        if (!$isValidOccurrences) {
                            $errorMessage = array();
                            $errorMessage[] = $this->translate('Please make sure you have selected the correct time interval - it is required');
                            $this->respondWithValidationError('validation_fail', $errorMessage);
                        } else {
                            $siteevent->repeat_params = json_encode($repeatEventInfo);
                            $siteevent->save();
                        }
                    } elseif (isset($_POST['eventrepeat_id']) && $_POST['eventrepeat_id'] == 'custom') {
                        $repeatEventInfo['eventrepeat_type'] = $values['eventrepeat_id'];
//                        $_POST['countcustom_dates'] = 2;
                        Engine_Api::_()->siteevent()->reorderCustomDates();
                        $siteevent->repeat_params = json_encode($repeatEventInfo);
                        $siteevent->save();
                    }
                    if (_CLIENT_TYPE && ((_CLIENT_TYPE == 'android' && _ANDROID_VERSION >= '1.8') || (_CLIENT_TYPE == 'ios' && _IOS_VERSION >= '1.5.8'))) {
                        if ($values['host_type'] == 'siteevent_organizer' && (!isset($values['host_id']) || empty($values['host_id'])) && (!isset($values['host_title']) || empty($values['host_title'])))
                            $this->respondWithValidationError('validation_fail', 'Host title missing');

                        if (isset($values['host_title']) && $values['host_title']) {
                            $table = Engine_Api::_()->getItemTable('siteevent_organizer');
                            $db = $table->getAdapter();
                            $db->beginTransaction();
                            $host = $table->createRow();
                            $hostInfo = array(
                                'title' => $_REQUEST['host_title'],
                                'description' => isset($_REQUEST['host_description']) && $_REQUEST['host_description'] ? $_REQUEST['host_description'] : null,
                                'creator_id' => $viewer_id,
                                'facebook_url' => isset($_REQUEST['host_facebook']) && $_REQUEST['host_facebook'] ? $_REQUEST['host_facebook'] : "",
                                'twitter_url' => isset($_REQUEST['host_twitter']) && $_REQUEST['host_twitter'] ? $_REQUEST['host_twitter'] : "",
                                'web_url' => isset($_REQUEST['host_website']) && $_REQUEST['host_website'] ? $_REQUEST['host_website'] : "",
                            );
                            $host->setFromArray($hostInfo);
                            $host->save();

                            if (isset($_FILES['host_photo']) && !empty($_FILES['host_photo']))
                                $host = Engine_Api::_()->getApi('Siteapi_Core', 'siteevent')->setPhotoForOrganizer($_FILES['host_photo'], $host);

                            $siteevent->host_type = $host->getType();
                            $siteevent->host_id = $host->getIdentity();
                            $siteevent->save();
                        }
                        else if (isset($_REQUEST['host_id']) && isset($_REQUEST['host_type'])) {
                            $siteevent->host_id = $_REQUEST['host_id'];
                            $siteevent->host_type = $_REQUEST['host_type'];
                            $siteevent->save();
                        }
                    }

//NOW MAKE THE ENTRY OF REPEAT INFO IF IT IS  ENABLED
                    $event_id = $siteevent->event_id;
                    $occure_id = $this->addorEditDates($_POST, $values, $event_id, 'create');
// Profile Fields: start work to save profile fields.
                    $profileTypeField = null;
                    $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('siteevent_event');
                    if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
                        $profileTypeField = $topStructure[0]->getChild();
                    }
                    if ($profileTypeField) {

                        $profileTypeValue = $siteevent->profile_type;

                        if ($profileTypeValue) {
                            $profileValues = Engine_Api::_()->fields()->getFieldsValues($siteevent);

                            $valueRow = $profileValues->createRow();
                            $valueRow->field_id = $profileTypeField->field_id;
                            $valueRow->item_id = $siteevent->getIdentity();
                            $valueRow->value = $profileTypeValue;
                            $valueRow->save();
                        } else {
                            $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('siteevent_event');
                            if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
                                $profileTypeField = $topStructure[0]->getChild();
                                $options = $profileTypeField->getOptions();
                                if (count($options) == 1) {
                                    $profileValues = Engine_Api::_()->fields()->getFieldsValues($siteevent);
                                    $valueRow = $profileValues->createRow();
                                    $valueRow->field_id = $profileTypeField->field_id;
                                    $valueRow->item_id = $siteevent->getIdentity();
                                    $valueRow->value = $options[0]->option_id;
                                    $valueRow->save();
                                }
                            }
                        }

                        // Save the profile fields information.
                        Engine_Api::_()->getApi('Siteapi_Core', 'siteevent')->setProfileFields($siteevent, $data);
                    }

//PRIVACY WORK
                    $auth = Engine_Api::_()->authorization()->context;
                    $auth->setAllowed($siteevent, 'member', 'invite', $values['auth_invite']);
                    $roles = array('leader', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                    $explodeParentType = explode('_', $parent_type);
                    if (!empty($explodeParentType) && isset($explodeParentType[0]) && isset($explodeParentType[1])) {
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($explodeParentType[0] . 'member') && ($parent_type == 'sitepage_page' || $parent_type == 'sitebusiness_business' || $parent_type == 'sitegroup_group') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parent_type, 'item_module' => $explodeParentType[0])))) {
                            $roles = array('leader', 'member', 'parent_member', 'registered', 'everyone');
                        } elseif (($parent_type == 'sitepage_page' || $parent_type == 'sitebusiness_business' || $parent_type == 'sitegroup_group' || $parent_type == 'sitestore_store') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parent_type, 'item_module' => $explodeParentType[0])))) {
                            $roles = array('leader', 'member', 'registered', 'everyone');
                        } elseif (($parent_type == 'sitereview_listing') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitereview_listing_' . $parentTypeItem->listingtype_id, 'item_module' => 'sitereview')))) {
                            $roles = array('leader', 'member', 'registered', 'everyone');
                        }
                    }

                    $leaderList = $siteevent->getLeaderList();

                    if (empty($values['auth_view'])) {
                        $values['auth_view'] = "everyone";
                    }

                    if (empty($values['auth_comment'])) {
                        $values['auth_comment'] = "registered";
                    }

                    $viewMax = array_search($values['auth_view'], $roles);
                    $commentMax = array_search($values['auth_comment'], $roles);

                    foreach ($roles as $i => $role) {

                        if ($role === 'leader') {
                            $role = $leaderList;
                        }

                        $auth->setAllowed($siteevent, $role, "view", ($i <= $viewMax));
                        $auth->setAllowed($siteevent, $role, "comment", ($i <= $commentMax));
                    }
                    $ownerList = '';
                    $roles = array('leader', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
                    $explodeParentType = explode('_', $parent_type);
                    if (!empty($explodeParentType) && isset($explodeParentType[0]) && isset($explodeParentType[1])) {
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($explodeParentType[0] . 'member') && ($parent_type == 'sitepage_page' || $parent_type == 'sitebusiness_business' || $parent_type == 'sitegroup_group')) {
                            $roles = array('leader', 'member', 'like_member', 'parent_member', 'registered');
                            $getContentOwnerList = 'get' . ucfirst($parentTypeItem->getShortType()) . 'OwnerList';
                            $ownerList = $parentTypeItem->$getContentOwnerList();
                        } elseif ($parent_type == 'sitepage_page' || $parent_type == 'sitebusiness_business' || $parent_type == 'sitegroup_group' || $parent_type == 'sitestore_store') {
                            $roles = array('leader', 'member', 'like_member', 'registered');
                            $getContentOwnerList = 'get' . ucfirst($parentTypeItem->getShortType()) . 'OwnerList';
                            $ownerList = $parentTypeItem->$getContentOwnerList();
                        } elseif (($parent_type == 'sitereview_listing') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitereview_listing_' . $parentTypeItem->listingtype_id, 'item_module' => 'sitereview')))) {
                            $roles = array('leader', 'member', 'registered', 'everyone');
                        }
                    }

                    if (empty($values['auth_topic'])) {
                        $values['auth_topic'] = "member";
                    }

                    if (empty($values['auth_photo'])) {
                        $values['auth_photo'] = "member";
                    }

                    if (!isset($values['auth_video']) && empty($values['auth_video'])) {
                        $values['auth_video'] = "member";
                    }

                    if (isset($values['auth_post']) && empty($values['auth_post'])) {
                        $values['auth_post'] = "member";
                    }

                    $topicMax = array_search($values['auth_topic'], $roles);
                    $photoMax = array_search($values['auth_photo'], $roles);
                    $videoMax = array_search($values['auth_video'], $roles);
                    $postMax = '';
                    if (isset($values['auth_post']) && !empty($values['auth_post']))
                        $postMax = array_search($values['auth_post'], $roles);

                    foreach ($roles as $i => $role) {

                        if ($role === 'leader') {
                            $role = $leaderList;
                        }

                        if ($role === 'like_member' && $ownerList) {
                            $role = $ownerList;
                        }

                        $auth->setAllowed($siteevent, $role, "topic", ($i <= $topicMax));
                        $auth->setAllowed($siteevent, $role, "photo", ($i <= $photoMax));
                        $auth->setAllowed($siteevent, $role, "video", ($i <= $videoMax));
                        if ($postMax)
                            $auth->setAllowed($siteevent, $role, "post", ($i <= $postMax));
                    }

// Create some auth stuff for all leaders
                    $auth->setAllowed($siteevent, $leaderList, 'photo.edit', 1);
                    $auth->setAllowed($siteevent, $leaderList, 'topic.edit', 1);
                    $auth->setAllowed($siteevent, $leaderList, 'video.edit', 1);
                    $auth->setAllowed($siteevent, $leaderList, 'edit', 1);
                    $auth->setAllowed($siteevent, $leaderList, 'delete', 1);

                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventdocument')) {

                        if (empty($values['auth_document'])) {
                            $values['auth_document'] = "member";
                        }

                        $documentMax = array_search($values['auth_document'], $roles);
                        foreach ($roles as $i => $role) {

                            if ($role === 'leader') {
                                $role = $leaderList;
                            }

                            if ($role === 'like_member' && $ownerList) {
                                $role = $ownerList;
                            }

                            $auth->setAllowed($siteevent, $role, "document", ($i <= $documentMax));
                        }

                        $auth->setAllowed($siteevent, $leaderList, 'document.edit', 1);
                    }

                    if ($siteevent->approved) {
                        //notification work for page and business and group pluin.
                        if (Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitepage') && Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitepagemember') && $parent_type == 'sitepage_page') {
                            Engine_Api::_()->sitepage()->sendInviteEmail($siteevent, null, array('tempValue' => 'Pageevent Invite', 'parent_id' => $parent_id, 'parent_type' => $parent_type, 'notificationType' => 'siteevent_page_invite', 'emailType' => 'SITEEVENT_PAGE_INVITE_EMAIL'));
                        } elseif (Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitebusinessmember') && Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitebusiness') && $parent_type == 'sitebusiness_business') {
                            Engine_Api::_()->sitebusiness()->sendInviteEmail($siteevent, null, array('tempValue' => 'Businessevent Invite', 'parent_id' => $parent_id, 'parent_type' => $parent_type, 'notificationType' => 'siteevent_business_invite', 'emailType' => 'SITEEVENT_BUSINESS_INVITE_EMAIL'));
                        } elseif (Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitegroupmember') && Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitegroup') && $parent_type == 'sitegroup_group') {
                            Engine_Api::_()->sitegroup()->sendInviteEmail($siteevent, null, array('tempValue' => 'Groupevent Invite', 'parent_id' => $parent_id, 'parent_type' => $parent_type, 'notificationType' => 'siteevent_group_invite', 'emailType' => 'SITEEVENT_GROUP_INVITE_EMAIL'));
                        }
                    }

//COMMIT
                    $db->commit();
                } catch (Exception $e) {
                    $db->rollBack();
                }

                $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'siteevent');
                $db->beginTransaction();
                try {
                    $row = $tableOtherinfo->getOtherinfo($event_id);
                    $overview = '';
                    if (isset($values['overview'])) {
                        $overview = $values['overview'];
                    }
                    $guest_lists = 0;
                    if (isset($values['guest_lists'])) {
                        $guest_lists = $values['guest_lists'];
                    }
                    if (empty($row))
                        Engine_Api::_()->getDbTable('otherinfo', 'siteevent')->insert(array(
                            'event_id' => $event_id,
                            'overview' => $overview,
                            'guest_lists' => $guest_lists
                        )); //COMMIT
                    $db->commit();
                } catch (Exception $e) {
                    $db->rollBack();
                }

                if (!empty($event_id)) {
                    $siteevent->setLocation();
                }

                $db->beginTransaction();
                try {
//PACKAGE BASED CHECKS

                    $siteevent_pending = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventpaid') ? $siteevent->pending : 0;

                    $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
                    if ($siteevent->draft == 0 && $siteevent->search && empty($siteevent_pending)) {

                        //INSERT ACTIVITY IF EVENT IS SEARCHABLE
                        if ($parent_type != 'user' && $parent_type != 'sitereview_listing') {
                            $getModuleName = strtolower($parentTypeItem->getModuleName());
                            $isOwner = 'is' . ucfirst($parentTypeItem->getShortType()) . 'Owner';
                            $isFeedTypeEnable = 'isFeedType' . ucfirst($parentTypeItem->getShortType()) . 'Enable';
                            $activityFeedType = null;
                            if (Engine_Api::_()->$getModuleName()->$isOwner($parentTypeItem) && Engine_Api::_()->$getModuleName()->$isFeedTypeEnable())
                                $activityFeedType = $getModuleName . 'event_admin_new';
                            elseif ($parentTypeItem->all_post || Engine_Api::_()->$getModuleName()->$isOwner($parentTypeItem))
                                $activityFeedType = $getModuleName . 'event_new';

                            if ($activityFeedType) {
                                $action = $actionTable->addActivity($viewer, $parentTypeItem, $activityFeedType);
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
                        } elseif ($parent_type == 'sitereview_listing') {
                            $action = $actionTable->addActivity($viewer, $parentTypeItem, 'sitereview_event_new_listtype_' . $parentTypeItem->listingtype_id);
                            if ($action != null) {
                                $actionTable->attachActivity($action, $siteevent);
                            }
                        } else {
                            $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $siteevent, 'siteevent_new');
                            if ($action != null) {
                                Engine_Api::_()->getDbtable('actions', 'seaocore')->attachActivity($action, $siteevent);
                            }
                        }
                    }

                    $users = Engine_Api::_()->getDbtable('editors', 'siteevent')->getAllEditors(0, 1);

                    foreach ($users as $user_ids) {

                        $subjectOwner = Engine_Api::_()->getItem('user', $user_ids->user_id);
                        if ($subjectOwner) {
                            $host = $_SERVER['HTTP_HOST'];
                            $newVar = _ENGINE_SSL ? 'https://' : 'http://';
                            $object_link = $newVar . $host . $siteevent->getHref();
                            $viewerGetTitle = $viewer->getTitle();
                            $sender_link = '<a href=' . $newVar . $host . $viewer->getHref() . ">$viewerGetTitle</a>";
                            Engine_Api::_()->getApi('mail', 'core')->sendSystem($subjectOwner->email, 'SITEEVENT_EVENT_CREATION_EDITOR', array(
                                'sender' => $sender_link,
                                'object_link' => $object_link,
                                'object_title' => $siteevent->getTitle(),
                                'object_description' => $siteevent->getDescription(),
                                'queue' => true
                            ));
                        }
                    }

//SEND NOTIFICATION & EMAIL TO HOST - IF PAYMENT NOT PENDING
                    if (empty($siteevent_pending)) {
                        Engine_Api::_()->siteevent()->sendNotificationToHost($siteevent->event_id);
                    }

//UPDATE KEYWORDS IN SEARCH TABLE
                    if (!empty($keywords)) {
                        Engine_Api::_()->getDbTable('search', 'core')->update(array('keywords' => $keywords), array('type = ?' => 'siteevent_event', 'id = ?' => $siteevent->event_id));
                    }

//SENDING ACTIVITY FEED TO FACEBOOK.
                    $enable_Facebooksefeed = $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksefeed');
                    if (!empty($enable_Facebooksefeed)) {

                        $sitepage_array = array();
                        $sitepage_array['type'] = 'siteevent_new';
                        $sitepage_array['object'] = $siteevent;

                        Engine_Api::_()->facebooksefeed()->sendFacebookFeed($sitepage_array);
                    }
                    $db->commit();
// Change request method POST to GET
                    $this->setRequestMethod();
                    $this->_forward('view', 'index', 'siteevent', array(
                        'event_id' => $siteevent->getIdentity()
                    ));
                    return;
                } catch (Exception $e) {
                    $db->rollBack();
                }
            }
        } catch (Expection $ex) {
            
        }
    }

    public function calenderAction() {
// Validate request methods
        $this->validateRequestMethod();
        $viewer = Engine_Api::_()->user()->getViewer();
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

//GET USER LEVEL ID
        if ($viewer->getIdentity()) {
            $level_id = $viewer->level_id;
            $viewer_id = $viewer->getIdentity();
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }
        $params = $this->_getAllParams();

        $categoryIds = Engine_Api::_()->getDbTable('categories', 'siteevent')->getParentCategories();
        $siteeventCalenderViewType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventcalender.viewtype', 1);

// GET CATEGORY
        $category_id = $this->_getParam('category_id', null);
        $params = $this->_getAllParams();

        if (!isset($params['viewtype']))
            $params['viewtype'] = $this->_getParam('viewtype', 'calendar');


        $params['page'] = $this->_getParam('page', 1);
        $viewtype = $params['viewtype'];

        if (!isset($params['limit']))
            $params['limit'] = $this->_getParam('itemCount', 10);

        $params['siteevent_calendar_event_count'] = $this->_getParam('siteevent_calendar_event_count', 1);
//        $params['postedby'] = $this->_getParam('postedby', 1);
//        $params['user_id'] = $this->_getParam('user_id', $viewer_id);
        $params['ismanage'] = $this->_getParam('ismanage', 1);
        $params['statistics'] = $this->_getParam('statistics', array("viewCount", "likeCount", "commentCount", "memberCount", "reviewCount"));
        $params['showContent'] = $this->_getParam('showContent', array("price", "location"));

        $eventsCountType = $this->_getParam('siteevent_calendar_event_count_type', 'all');

        if ($eventsCountType == 1 || $eventsCountType == 'onlyjoined')
            $params['siteevent_calendar_event_count_type'] = 'onlyjoined';
        else
            $params['siteevent_calendar_event_count_type'] = 'all';

//        if (empty($siteeventCalenderViewType))
//            $this->respondWithError('unauthorized');
//CASE:1 SHOW THE CALENDAR VIEW
        if ($params['viewtype'] == 'calendar') {
            $params = $params;
            $param['display_today_birthday'] = "M";
// GET THE MONTH FROM URL IF PRESENT OTHERWISE SET IT TO THE CURRENT MONTH
            $date = strtotime($this->_getParam('date_current', null));
            if (empty($date)) {
                $date = time();
            }
// GET THIS, LAST AND NEXT MONTHS
            $date = mktime(23, 59, 59, date("m", $date), 1, date("Y", $date));
            $date_next = mktime(0, 0, 0, date("m", $date) + 1, 1, date("Y", $date));
            $date_last = mktime(0, 0, 0, date("m", $date) - 1, 1, date("Y", $date));

            $days_in_month = date('t', $date);
//GET THE LAST DATE OF MONTH      
            $startdate = date("Y", $date) . '-' . date("m", $date) . '-' . 01;
            $lastDateofMonth = date("Y", $date) . '-' . date("m", $date) . '-' . $days_in_month . ' 23:59:59';
            $params['calendarlist'] = 1;
            try {
                $dateInfo = Engine_Api::_()->siteevent()->userToDbDateTime(array('starttime' => $startdate, 'endtime' => $lastDateofMonth));
                $date = $dateInfo['starttime'];
                $lastDateofMonth = $dateInfo['endtime'];
                $params['starttime'] = $date;
                $params['endtime'] = $lastDateofMonth;
                $params['sql'] = 'count';
                $paramsContentType = '';
                $params['location'] = $this->_getParam('restapilocation', '');


                $monthEventResults = Engine_Api::_()->getDbTable('events', 'siteevent')->getEvent($paramsContentType, $params);

                foreach ($monthEventResults as $results) {
                    //GET DATES OF EVENT
                    $tz = Engine_Api::_()->getApi('settings', 'core')->core_locale_timezone;
                    if (!empty($viewer_id)) {
                        $tz = $viewer->timezone;
                    }

                    if (isset($results['starttime']) && !empty($results['starttime']) && isset($tz)) {
                        $startDateObject = new Zend_Date(strtotime($results['starttime']));
                        $startDateObject->setTimezone($tz);
                        $results['starttime'] = $startDateObject->get('YYYY-MM-dd HH:mm:ss');
                    }
                    $finalResults[] = $results;
                }

//GET THE NUMBER OF DAYS IN THE MONTH
//GET THE FIRST DAY OF THE MONTH
                $date = $date_current;
                $first_day_of_month = date("w", $date);
                if ($first_day_of_month == 0) {
                    $first_day_of_month = 7;
                }
                $first_day_of_month = $first_day_of_month;

//GET THE LAST DAY OF THE MONTH
                $last_day_of_month = $last_day_of_month = ($first_day_of_month - 1) + $days_in_month;

//GET THE TOTAL NUMBER OF CELLS TO BE DISPLAYED IN THE CALENDER TABLE
                $total_cells = $total_cells = (floor($last_day_of_month / 7) + 1) * 7;

//GET CURRENT MONTH THAT HAS TO BE DISPLAYED
                $current_month = date("m", $date);

//GET THE TEXT OF THE CURRENT MONTH
                $current_month_text = date($date, array('format' => 'MMMM'));

//GET THE YEAR OF THE CURRENT MONTHS		
                $current_year = date("Y", $date);
            } catch (Exception $e) {
                $this->respondWithValidationError('internal_server_error', $ex->getMessage());
            }
            $this->respondWithSuccess($finalResults, true);
        }
        if ($params['viewtype'] == 'list') {
//CASE:2 SHOW THE CALENDAR LISTING
            $date = strtotime($this->_getParam('date_current', null));
//            $date = ($this->_getParam('date_current', null));

            if (empty($date)) {
                $date = time();
            }

            $starttime = date("Y-m-d H:i:s", $date);
            $endtime = date("Y-m-d H:i:s", $date + (24 * 3600 - 1));

            $dateInfo = Engine_Api::_()->siteevent()->userToDbDateTime(array('starttime' => $starttime, 'endtime' => $endtime));
            $timezone = Engine_Api::_()->getApi('settings', 'core')->core_locale_timezone;
            if ($viewer->getIdentity()) {
                $timezone = $viewer->timezone;
            }
//$starttime = date("Y-m-d", $date);
            $todaysDate = date("Y-m-d", time());
            $oldTz = date_default_timezone_get();
            date_default_timezone_set($timezone);
            $todaysDate = strtotime($todaysDate);
            date_default_timezone_set($oldTz);

            $todaysDate = $todaysDate;
            $currentDate = Engine_Api::_()->siteevent()->userToDbDateTime(array('starttime' => $starttime));
            $current_date = $currentDate['starttime'];


//NOW FIND THE START TIME AND END TIME OF THE DATE
//$starttime = $datetime;
            $paramsContentType = '';
//$params['date_current'] = $datetime;
            $params['calendarlist'] = 1;
            $params['starttime'] = $dateInfo['starttime'];
            $params['endtime'] = $dateInfo['endtime'];

            if ($this->_getParam('sql', sidecalendar) == 'sidecalendar')
                $params['siteevent_calendar_event_count_type'] = $this->_getParam('siteevent_calendar_event_count_type', null);
            $params['sql'] = false;

            if (!empty($category_id))
                $params['category_id'] = $category_id;

            $params['eventType'] = $this->_getParam('eventType', 'All');

            $params['siteevent_calendar_event_count_type'] = $this->_getParam('siteevent_calendar_event_count_type', 'all');
            try {

                $paginator = $siteeventCalendar = Engine_Api::_()->getDbTable('events', 'siteevent')->getEvent($paramsContentType, $params);

                $totalCount = $paginator->getTotalItemCount();
                $postedby = $this->_getParam('postedby', 1);
                $showContent = $this->_getParam('showContent', array("price", "location"));


                if ($totalCount > 0) {
                    foreach ($paginator as $eventObj) {
                        // continue if Deleted member
                        if (empty($eventObj->host_id))
                            continue;
                        $event = $eventObj->toArray();

                        //CATEGORY NAME
                        $event['category_name'] = Engine_Api::_()->getItem('siteevent_category', $event['category_id'])->category_name;

                        //GET DATES OF EVENT
                        $tz = Engine_Api::_()->getApi('settings', 'core')->core_locale_timezone;
                        if (!empty($viewer_id)) {
                            $tz = $viewer->timezone;
                        }

                        $occurrence_id = Engine_Api::_()->getDbTable('events', 'siteevent')->getNextOccurID($eventObj->getIdentity());
                        $occurrenceTable = Engine_Api::_()->getDbTable('occurrences', 'siteevent');
                        $dates = $occurrenceTable->getEventDate($eventObj->getIdentity(), $occurrence_id);

                        if (isset($dates['starttime']) && !empty($dates['starttime']) && isset($tz)) {
                            $startDateObject = new Zend_Date(strtotime($dates['starttime']));
                            $startDateObject->setTimezone($tz);
                            $dates['starttime'] = $startDateObject->get('YYYY-MM-dd HH:mm:ss');
                        }
                        if (isset($dates['endtime']) && !empty($dates['endtime']) && isset($tz)) {
                            $endDateObject = new Zend_Date(strtotime($dates['endtime']));
                            $endDateObject->setTimezone($tz);
                            $dates['endtime'] = $endDateObject->get('YYYY-MM-dd HH:mm:ss');
                        }

                        $event['isRepeatEvent'] = ($eventObj->isRepeatEvent()) ? 1 : 0;
                        $event = array_merge($event, $dates);

                        // ADD OWNER IMAGES
                        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($eventObj, true);
                        $event = array_merge($event, $getContentImages);
                        $event["owner_title"] = $eventObj->getOwner()->getTitle();

                        try {
                            // ADD EVENT IMAGES
                            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($eventObj);
                        } catch (Exception $ex) {
                            
                        }

                        $event = array_merge($event, $getContentImages);

                        //GET EXACT LOCATION
                        if ($getLocation == 1 && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1) && !$siteevent->is_online) {
                            //GET LOCATION
                            $value['id'] = $event['event_id'];
                            $location = Engine_Api::_()->getDbtable('locations', 'siteevent')->getLocation($value);
                            if ($location)
                                $event['location'] = $location->toArray();
                        }
                        $event['hosted_by'] = '';
                        $organizerObj = Engine_Api::_()->getItem($eventObj->host_type, $eventObj->host_id);
                        $organizer['host_type'] = $organizerObj->getType();
                        $organizer['host_id'] = $organizerObj->getIdentity();
                        $organizer['host_title'] = $organizerObj->getTitle();
                        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($organizerObj);
                        $organizer = array_merge($organizer, $getContentImages);
                        $event['hosted_by'] = $organizer;

                        $leaders = Engine_Api::_()->getItem('siteevent_event', $event['event_id'])->getLedBys(false);
                        // Set default ledby.
                        $defaultLedby['title'] = $eventObj->getOwner()->getTitle();
                        $defaultLedby['type'] = $eventObj->getOwner()->getType();
                        $defaultLedby['id'] = $eventObj->getOwner()->getIdentity();
                        $event['ledby'][] = $defaultLedby;

                        // Set array of ledby.
                        foreach ($leaders as $leader) {
                            $tempLeader['title'] = $leader->getOwner()->getTitle();
                            $tempLeader['type'] = $leader->getOwner()->getType();
                            $tempLeader['id'] = $leader->getOwner()->getIdentity();
                            $event['ledby'][] = $tempLeader;
                        }

                        //occurence id
                        $isAllowedView = $eventObj->authorization()->isAllowed($viewer, 'view');
                        $event["allow_to_view"] = empty($isAllowedView) ? 0 : 1;

                        $isAllowedEdit = $eventObj->authorization()->isAllowed($viewer, 'edit');
                        $event["edit"] = empty($isAllowedEdit) ? 0 : 1;

                        $isAllowedDelete = $eventObj->authorization()->isAllowed($viewer, 'delete');
                        $event["delete"] = empty($isAllowedDelete) ? 0 : 1;

                        $tempResponse[] = $event;
                    }
                    if (!empty($tempResponse))
                        $response['response'] = $tempResponse;
                }
            } catch (Exception $ex) {
                $this->respondWithError('internal_server_error', $ex->getMessage());
            }
//RESPONSE
            $response['getTotalItemCount'] = $response['totalItemCount'] = $totalCount;
            $response['canCreate'] = Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, 'create');
            $response['packagesEnabled'] = $this->_packagesEnabled();

            $this->respondWithSuccess($response, true);
        }
    }

    /*
     * search Hosts
     */

    public function getHostsAction() {
        $values = $this->_getAllParams();

        if (!isset($values['host_type_select']) || empty($values['host_type_select']))
            $this->respondWithValidationError('parameter_missing', 'host_type_select missing');

        if (!isset($values['host_auto']) || empty($values['host_auto']))
            $this->respondWithValidationError('parameter_missing', 'host_auto missing');

        $subject_type = $values['host_type_select'];
        $searchText = $values['host_auto'];
        $limit = $this->_getParam('limit', 40);

        //FETCH USER LIST
        $items = Engine_Api::_()->getDbTable('events', 'siteevent')->getHostsSuggest($subject_type, $searchText, $limit);

        //MAKING DATA
        $data = array();
        $mode = $this->_getParam('struct');

        foreach ($items as $item) {
            $content = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($item, false);
            $content_photo = $content['image_icon'];
            $link = $content['content_url'];
            $data[] = array('type' => 'host', 'host_id' => $item->getIdentity(), 'host_title' => $item->getTitle(), 'image_icon' => $content_photo, 'host_link' => $link);
        }

        $this->respondWithSuccess($data, true);
    }

    public function addorEditDates($postedValues, $values, $event_id, $action = 'create') {
        try {

//SPECIAL CASE: IF THIS FUNCTION IS CALLED BY ADDMOREOCCURRENCE FUNCTION FOR ADDING MORE OCCURRENCES THEN WE WILL NOT SET USER TIME ZONE WHEN INSERTING THE DATE ENTRY IN DATABASE.
            $useTimezone = true;
            if (isset($postedValues['useTimezone']))
                $useTimezone = $postedValues['useTimezone'];
            $occure_id = '';

            if (!isset($values['starttime']) && isset($postedValues['starttime']))
                $values['starttime'] = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->convertDateFormat($postedValues['starttime']);

            $viewer = Engine_Api::_()->user()->getViewer();
            $viewer_id = $viewer->getIdentity();

            if (!empty($event_id))
                $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
            $isEventMember = false;
            if (isset($postedValues['action']) && $postedValues['action'] == 'editdates')
                $isEventMember = true;
            if ($action == 'edit') {
//FIRST WE WILL CHECK THAT EITHER EVENT OWNER WAS JOINED ANY EVENT OCCURRENCE THEN WE WILL JOIN OWNER AGAIN FOR NEW FIRST EVENT OCCURRENCE ELSE WE WILL NOT JOIN AGAIN.
                $user = Engine_Api::_()->getItem('user', $siteevent->owner_id);
                if (!$siteevent->membership()->isEventMember($user, true)) {
                    $isEventMember = true;
                }
//                else {
//                    $siteevent->member_count--;
//                    $siteevent->save();
//                }


                if (!isset($postedValues['previous_eventtype']) || ($postedValues['previous_eventtype'] != 'custom' || $postedValues['previous_eventtype'] != $values['eventrepeat_id'])) {
                    Engine_Api::_()->getDbtable('occurrences', 'siteevent')->deleteRepeatEvent($event_id);
                    Engine_Api::_()->getDbtable('membership', 'siteevent')->deleteEventMember($viewer, $event_id);
                    $siteevent->member_count = 0;
                    $siteevent->save();
                }
            }

            if ((!isset($postedValues['action']) || $postedValues['action'] != 'editdates') && (!isset($values['eventrepeat_id']) || ($values['eventrepeat_id'] == 'daily' || $values['eventrepeat_id'] == 'never'))) {
                $params = array();
                $params['nextStartDate'] = $values['starttime'];
                $params['nextEndDate'] = $values['endtime'];
                $params['event_id'] = $event_id;

                if (!$isEventMember)
                    $params['is_member'] = 1;
                $occure_id = $this->setEventInfo($params, $useTimezone);
            }

            if (isset($values['eventrepeat_id']) && $values['eventrepeat_id'] !== 'never') {
                $params = array();

                if ($values['eventrepeat_id'] != 'custom')
                    $start = strtotime($values['starttime']);
                if (isset($postedValues[$values['eventrepeat_id'] . '_repeat_time']['date']) && !empty($postedValues[$values['eventrepeat_id'] . '_repeat_time']['date'])) {


                    $postedValues[$values['eventrepeat_id'] . '_repeat_time']['date'] = Engine_Api::_()->siteevent()->convertDateFormat($postedValues[$values['eventrepeat_id'] . '_repeat_time']['date']);
                    $repeat_endtime = strtotime($postedValues[$values['eventrepeat_id'] . '_repeat_time']['date']) + (24 * 3600 - 1);
                    //WE WILL CHECK HERE THAT IF THE END DATE IS GREATER THEN NEXT YEAR END DATE THEN WE WILL ONLY CREATE MAX OF NEXT YEAR DATES ENTRY.
                    //GET THE NEXT YEAR LAST DATE
//          $nextyear = date("Y", strtotime('+1 year'));
//          $nextyearendtime = strtotime($nextyear . "-12-31 23:59:59");
//          if ($repeat_endtime > $nextyearendtime) {
//            $repeat_endtime = $nextyearendtime;
//          }
                }
//date_default_timezone_set($oldTz);
                if (isset($postedValues[$values['eventrepeat_id'] . '_repeat_time']['date']) && !empty($postedValues[$values['eventrepeat_id'] . '_repeat_time']['date']))
                    $postedValues[$values['eventrepeat_id'] . '_repeat_time'] = date('Y-m-d H:i:s', $repeat_endtime);
                if ($values['eventrepeat_id'] != 'custom') {
                    $starttime = strtotime($values['starttime']);
                    $endtime = strtotime($values['endtime']);
                    $durationDiff = $endtime - $starttime;
                }
                if ($values['eventrepeat_id'] === 'daily') {

                    //get the all events occuerrence dates                    
                    $total_no_occurrence = floor((strtotime($postedValues[$values['eventrepeat_id'] . '_repeat_time']) - strtotime($values['starttime']) ) / ($postedValues['daily-repeat_interval'] * 24 * 60 * 60));

                    $params = array();
                    for ($i = 1; $i <= $total_no_occurrence; $i++) {
                        $nexttimestamp = $starttime + ($postedValues['daily-repeat_interval'] * 24 * 60 * 60) * $i;
                        $nextStartDate = date("Y-m-d H:i:s", $nexttimestamp);
                        $nextEndDate = date("Y-m-d H:i:s", ($nexttimestamp + $durationDiff));
                        $params['nextStartDate'] = $nextStartDate;
                        $params['nextEndDate'] = $nextEndDate;
                        $params['event_id'] = $event_id;
                        $this->setEventInfo($params, $useTimezone);
                    }
                }
//CASE:2 WEEKLY
                elseif ($values['eventrepeat_id'] === 'weekly') {
                    $weekdays = array(1 => 'monday', 2 => 'tuesday', 3 => 'wednesday', 4 => 'thursday', 5 => 'friday', 6 => 'saturday', 7 => 'sunday');
                    $weekdays_Temp = $weekdays;
                    $firstStartweekday = date("N", $start);
                    $skip_firstweekdays = false;
                    //get the all events occuerrence dates  
                    $nextStartTime = $start;
                    $j = 0;
                    for ($i = $start; $i <= $repeat_endtime; $i = $nextStartTime) {
                        $j++;
                        $week_loop = 0;

                        foreach ($weekdays_Temp as $key => $weekday) {
                            $params = array();

                            if (isset($postedValues['weekly-repeat_on_' . $weekday])) {
                                $week_loop++;
                                //IF THE START WEEKS WEEKDAY IS GREATER THEN THE SELECTED WEEKDAY THEN WE WILL SKIP THAT ONLY FOR FIRST START WEEK. 
                                if (!$skip_firstweekdays && $firstStartweekday > $key) {

                                    continue;
                                }


                                $eventstartweekday = date("N", $nextStartTime);

                                if ($skip_firstweekdays == false && $eventstartweekday == $key) {

                                    $nextStartTime = $start;
                                } elseif ($skip_firstweekdays == false) {
                                    $nextStartTime = $nextStartTime + ($key - $eventstartweekday) * 24 * 3600;
                                } else {

                                    if ($week_loop > 1)
                                        $nextStartTime = $nextStartTime + (($key - $eventstartweekday)) * 24 * 3600;
                                    else
                                        $nextStartTime = $nextStartTime + ((7 - $eventstartweekday) + ($postedValues['id_weekly-repeat_interval'] - 1) * 7 + $key) * 24 * 3600;
                                    $nextStartDate = date("Y-m-d H:i:s", $nextStartTime);
                                }
                                //EXCEPTIONAL CASE: 
                                //IF ACTION IS EDITDATES AND NEXT START DATE IS EQUAL TO START DATE THEN WILL CONTINUE HERE.
                                if (isset($postedValues['action']) && $postedValues['action'] == 'editdates' && $nextStartTime == $start)
                                    continue;



                                if ($nextStartTime <= $repeat_endtime) {

                                    $nextStartDate = date("Y-m-d H:i:s", $nextStartTime);
                                    $nextEndDate = date("Y-m-d H:i:s", ($nextStartTime + $durationDiff));

                                    $params['nextStartDate'] = $nextStartDate;
                                    $params['nextEndDate'] = $nextEndDate;
                                    $params['event_id'] = $event_id;
                                    if (!$isEventMember) {
                                        $params['is_member'] = 1;
                                        $isEventMember = true;
                                    }
                                    $this->setEventInfo($params, $useTimezone);
                                }
                                //}
                                // }
                            }
                        }

                        $week_loop = 0;
                        $skip_firstweekdays = true;
                    }
                }
//CASE:3 MONTHLY
                elseif ($values['eventrepeat_id'] === 'monthly') {
                    $params = array();
                    //CHECK FOR EITHER ABSOLUTE MONTH DAY OR RELATIVE DAY
                    $noOfWeeks = array('first' => 1, 'second' => 2, 'third' => 3, 'fourth' => 4, 'fifth' => 5, 'last' => 6);
                    $dayOfWeeks = array(1 => 'monday', 2 => 'tuesday', 3 => 'wednesday', 4 => 'thursday', 5 => 'friday', 6 => 'saturday', 7 => 'sunday');


                    $monthly_array = array();
                    //HERE WE WILL FIRST CHECK THAT THE EVENT START TIME IS VALID OR NOT.

                    $currentmonthEvent = false;

                    //get the all events occuerrence dates
                    if ($postedValues['monthly_day'] != 'relative_weekday') {
                        $starttime_DayMonth = date("j", $start);
                        $current_month = date("Ym", time());
                        $starttime_month = date("Ym", $start);
                        if ($postedValues['id_monthly-absolute_day'] >= $starttime_DayMonth && $current_month == $starttime_month)
                            $currentmonthEvent = true;
                        for ($i = $start; $i <= $repeat_endtime; $i = $nextStartTime) {
                            $dayofMonth = date("j", $i);
                            if ($currentmonthEvent) {
                                $nextStartTime = strtotime(Engine_Api::_()->siteevent()->date_add(Engine_Api::_()->siteevent()->date_add(date("Y-m-d H:i:s", $i), 0, 0), ($postedValues['id_monthly-absolute_day'] - $dayofMonth)));
                            } elseif (isset($postedValues['action']) && $postedValues['action'] == 'editdates') {
                                $nextStartTime = strtotime(Engine_Api::_()->siteevent()->date_add(date("Y-m-d H:i:s", $i), 0, $postedValues['id_monthly-repeat_interval']));
                            } else {
                                $nextStartTime = strtotime(Engine_Api::_()->siteevent()->date_add(Engine_Api::_()->siteevent()->date_add(date("Y-m-d H:i:s", $i), 0, $postedValues['id_monthly-repeat_interval']), ($postedValues['id_monthly-absolute_day'] - $dayofMonth)));
                            }


                            //EXCEPTIONAL CASE: 
                            //IF ACTION IS EDITDATES AND NEXT START DATE IS EQUAL TO START DATE THEN WILL CONTINUE HERE.
                            if (isset($postedValues['action']) && $postedValues['action'] == 'editdates' && $nextStartTime == $start)
                                continue;

                            if ($nextStartTime <= $repeat_endtime) {
                                $nextStartDate = date("Y-m-d H:i:s", $nextStartTime);
                                $nextEndDate = date("Y-m-d H:i:s", ($nextStartTime + $durationDiff));
                                $params = array();
                                $params['nextStartDate'] = $nextStartDate;
                                $params['nextEndDate'] = $nextEndDate;
                                $params['event_id'] = $event_id;
                                if (!$isEventMember) {
                                    $params['is_member'] = 1;
                                    $isEventMember = true;
                                }
                                $this->setEventInfo($params, $useTimezone);
                            }

                            $currentmonthEvent = false;
                        }
                    } else {

                        $starttime_Week = Engine_Api::_()->siteevent()->getWeeks($values['starttime'], 'monday');
                        $starttime_Weekday = date("N", $start);
                        if ($starttime_Week < $noOfWeeks[$postedValues['id_monthly-relative_day']] || ($starttime_Week == $noOfWeeks[$postedValues['id_monthly-relative_day']] && $starttime_Weekday <= array_search($postedValues['id_monthly-day_of_week'], $dayOfWeeks)))
                            $currentmonthEvent = true;


                        for ($i = $start; $i <= $repeat_endtime; $i = $nextStartTime) {
                            $params = array();
                            $dayofMonth = date("j", $i);
                            if ($currentmonthEvent) {
                                $repeatMonthStartDate = Engine_Api::_()->siteevent()->date_add(Engine_Api::_()->siteevent()->date_add(date("Y-m-d H:i:s", $i), 0, 0), ('01' - $dayofMonth));
                            } else {

                                $repeatMonthStartDate = Engine_Api::_()->siteevent()->date_add(Engine_Api::_()->siteevent()->date_add(date("Y-m-d H:i:s", $i), 0, $postedValues['id_monthly-repeat_interval']), ('01' - $dayofMonth));
                            }
                            if ($postedValues['id_monthly-relative_day'] == 'last') {
                                $days_in_month = date('t', strtotime($repeatMonthStartDate));

                                $getRepeatTime = explode(" ", $repeatMonthStartDate);
                                $getTimeString = explode(":", $getRepeatTime[1]);

                                //GET THE LAST DATE OF MONTH
                                $lastDateofMonth = date("Y-m-d H:i:s", mktime($getTimeString[0], $getTimeString[1], $getTimeString[2], date("m", strtotime($repeatMonthStartDate)), $days_in_month, date("Y", strtotime($repeatMonthStartDate))));


                                $totalnoofWeeks = ceil(date('j', strtotime($lastDateofMonth)) / 7);
                                $lastday_Weekday = date("N", strtotime($lastDateofMonth));
                                if ($totalnoofWeeks == 5 && $lastday_Weekday < array_search($postedValues['id_monthly-day_of_week'], $dayOfWeeks))
                                    $totalnoofWeeks--;
                                $noOfWeeks['last'] = $totalnoofWeeks;


                                if ($lastday_Weekday < array_search($postedValues['id_monthly-day_of_week'], $dayOfWeeks)) {
                                    $day_decrease = -((7 - array_search($postedValues['id_monthly-day_of_week'], $dayOfWeeks)) + $lastday_Weekday);
                                } else if ($lastday_Weekday > array_search($postedValues['id_monthly-day_of_week'], $dayOfWeeks)) {
                                    $day_decrease = -( $lastday_Weekday - array_search($postedValues['id_monthly-day_of_week'], $dayOfWeeks));
                                } else
                                    $day_decrease = 0;

                                if ($day_decrease != 0) {
                                    $nextStartDate = Engine_Api::_()->siteevent()->date_add(date("Y-m-d H:i:s", strtotime($lastDateofMonth)), $day_decrease, 0);
                                } else {
                                    e;
                                    $nextStartDate = $lastDateofMonth;
                                }
                            } else {

                                $repeatMonthStartTime = strtotime($repeatMonthStartDate);

                                $repeatMonthStartWeekday = date("N", $repeatMonthStartTime);

                                if ($repeatMonthStartWeekday <= array_search($postedValues['id_monthly-day_of_week'], $dayOfWeeks))
                                    $month_day = array_search($postedValues['id_monthly-day_of_week'], $dayOfWeeks) - $repeatMonthStartWeekday;
                                else
                                    $month_day = (7 - $repeatMonthStartWeekday) + array_search($postedValues['id_monthly-day_of_week'], $dayOfWeeks);


                                $nextStartDate = Engine_Api::_()->siteevent()->date_add($repeatMonthStartDate, (($month_day) + ($noOfWeeks[$postedValues['id_monthly-relative_day']] - 1) * 7));
                            }

                            $nextStartTime = strtotime($nextStartDate);
                            //IF START TIME WEEK IS NOT EQUAL TO THE REQUIRED WEEK THEN CONTINUE.CASE: IF WEEK IS FIFTH WEEK.

                            $starttime_Week = Engine_Api::_()->siteevent()->getWeeks($nextStartDate, 'monday');
                            if ($postedValues['id_monthly-relative_day'] != 'last') {
                                if ($starttime_Week < $noOfWeeks[$postedValues['id_monthly-relative_day']]) {
                                    continue;
                                }
                            }

                            //EXCEPTIONAL CASE: 
                            //IF ACTION IS EDITDATES AND NEXT START DATE IS EQUAL TO START DATE THEN WILL CONTINUE HERE.
                            if (isset($postedValues['action']) && $postedValues['action'] == 'editdates' && $nextStartTime == $start)
                                continue;

                            if ($repeat_endtime >= $nextStartTime) {
                                $nextStartDate = date("Y-m-d H:i:s", $nextStartTime);
                                $nextEndDate = date("Y-m-d H:i:s", ($nextStartTime + $durationDiff));

                                $params['nextStartDate'] = $nextStartDate;
                                $params['nextEndDate'] = $nextEndDate;
                                $params['event_id'] = $event_id;
                                if (!$isEventMember) {
                                    $params['is_member'] = 1;
                                    $isEventMember = true;
                                }

                                $this->setEventInfo($params, $useTimezone);
                            }

                            $currentmonthEvent = false;
                        }
                    }
                }
//CASE:4 CUSTOM
                elseif ($values['eventrepeat_id'] === 'custom') {


                    if ($action == 'create' || (isset($postedValues['isEventMember']) && !$postedValues['isEventMember']))
                        $isEventMember = false;
                    elseif ((isset($postedValues['isEventMember']) && $postedValues['isEventMember']))
                        $isEventMember = true;

                    //CREATE THE ROWS FOR EACH CUSTOM DATES IN THE OCCURRENCES TABLE
                    //CREATE THE ROWS FOR EACH CUSTOM ROW IN THE REPEAT DATES TABLE

                    for ($i = 0; $i <= $postedValues['countcustom_dates']; $i++) {
                        $params = array();
                        if (isset($postedValues['customdate_' . $i])) {
                            $startenddate = explode("-", $postedValues['customdate_' . $i]);
                            $params['nextStartDate'] = $startenddate[0];
                            $params['nextEndDate'] = $startenddate[1];
                            $params['event_id'] = $event_id;
                            if (!$isEventMember) {
                                $params['is_member'] = 1;
                                $postedValues['isEventMember'] = true;
                                $isEventMember = true;
                            }
                            $this->setEventInfo($params, $useTimezone);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            
        }

        return $occure_id;
    }

    public function setEventInfo($params = array(), $useTimezone = true) {
        $this->_occurrencesCount++;
        if ($this->_occurrencesCount <= Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.occurrencecount', 15) || (isset($_POST['eventrepeat_id']) && $_POST['eventrepeat_id'] == 'custom')) {
            try {
                $viewer = Engine_Api::_()->user()->getViewer();
                $viewer_id = $viewer->getIdentity();

                $tableOccurence = Engine_Api::_()->getDbtable('occurrences', 'siteevent');

                $row_occurrence = $tableOccurence->createRow();
                $dateInfo = Engine_Api::_()->siteevent()->userToDbDateTime(array('starttime' => Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->convertDateFormat($params['nextStartDate']), 'endtime' => Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->convertDateFormat($params['nextEndDate'])));
                $row_occurrence->event_id = $params['event_id'];
                $row_occurrence->starttime = $dateInfo['starttime'];
                $row_occurrence->endtime = $dateInfo['endtime'];
//IF TICKET PLUGIN ENABLED
                if (Engine_Api::_()->siteevent()->hasTicketEnable()) {
                    //RESET TICKET_ID_SOLD ARRAY OF NEW OCCURRENCES.
                    $row_occurrence->ticket_id_sold = Engine_Api::_()->getDbtable('tickets', 'siteeventticket')->resetTicketIdSoldArray($params['event_id']);
                }

                $row_occurrence->save();
                $occure_id = $row_occurrence->occurrence_id;

// Add owner as member
//we will join the event owner only for his first event occurrence.
                $siteevent = Engine_Api::_()->getItem('siteevent_event', $params['event_id']);
                if (isset($params['is_member']) && $siteevent->parent_type == 'user') {
                    $owner = Engine_Api::_()->getItem('user', $siteevent->owner_id);
                    $siteevent->membership()->addMember($owner)
                            ->setUserApproved($owner)
                            ->setResourceApproved($owner);

                    // Add owner rsvp
                    $siteevent->membership()
                            ->getMemberInfo($owner)
                            ->setFromArray(array('rsvp' => 2, 'occurrence_id' => $occure_id))
                            ->save();
                }
                return $occure_id;
            } catch (Exception $E) {
                
            }
        }
    }

    public function memberSuggestAction() {
// Validate request methods
        $this->validateRequestMethod();

        $data = array();
        $subject_guid = $this->getRequestParam('subject', null);
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if ($subject_guid) {
            $subject = Engine_Api::_()->getItemByGuid($subject_guid);
        } else {
            $subject = $viewer;
        }

        if ($viewer->getIdentity()) {
            $data = array();
            $table = Engine_Api::_()->getItemTable('user');
            $select = $subject->membership()->getMembersObjectSelect();

            if (0 < ($limit = (int) $this->getRequestParam('limit', 10))) {
                $select->limit($limit);
            }

            if (null !== ($text = $this->getRequestParam('search', $this->getRequestParam('value')))) {
                $select->where('`' . $table->info('name') . '`.`displayname` LIKE ?', '%' . $text . '%');
            }
            $select->where('`' . $table->info('name') . '`.`user_id` <> ?', $viewer->getIdentity());
            $select->order("{$table->info('name')}.displayname ASC");
            $ids = array();
            foreach ($select->getTable()->fetchAll($select) as $friend) {
                $tempData['type'] = 'user';
                $tempData['id'] = $friend->getIdentity();
                $tempData['guid'] = $friend->getGuid();
                $tempData['label'] = $friend->getTitle();

// Add images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($friend);
                $tempData = array_merge($tempData, $getContentImages);

                $data[] = $tempData;
            }
        }
        $this->respondWithSuccess($data);
    }

//Execute when an event is being edited. here we will only edit the event occurrences and add new if there are any.
    public function editDates($postedValues, $values, $event_id, $action = 'edit') {

//IF EVENT TYPE IF CUSTOM THEN WE WILL CALL ADDOREDIT FUNCTION TO CREATE NEW ROWS ONLY.
        if ($values['eventrepeat_id'] == 'custom') {
            $postedValues['action'] = 'editdates';
            $this->addorEditDates($postedValues, $values, $event_id, 'append');
            return;
        }
        $values['starttime'] = Engine_Api::_()->siteevent()->convertDateFormat($postedValues['starttime']);
//CASE 1: WHEN END DATE DURATION IS CHANGED.
        $starttime = strtotime($values['starttime']);
        $endtime = strtotime($values['endtime']);

        $durationDiff = $endtime - $starttime;
//SELECT THE ALL OCCURRENCES OF THIS EVENT.
        $tableOccurence = Engine_Api::_()->getDbtable('occurrences', 'siteevent');
        $getALLOccurrences = $tableOccurence->getAllOccurrenceDates($event_id, 0);

        $params = array();
//NOW UPDATE THE ENDDATE OF EACH OCCURRENCE ACCORDING TO THE CURRENT DURATION.
        foreach ($getALLOccurrences as $occurrence) {

            $nextEndDate = date("Y-m-d H:i:s", strtotime($occurrence->starttime) + $durationDiff);
            try {
                $viewer = Engine_Api::_()->user()->getViewer();
                $viewer_id = $viewer->getIdentity();

                $tableOccurence = Engine_Api::_()->getDbtable('occurrences', 'siteevent');

                $endtime = strtotime($occurrence->starttime) + $durationDiff;
                $oldTz = date_default_timezone_get();
                date_default_timezone_set($viewer->timezone);
                date_default_timezone_set($oldTz);
                $nextEndDate = date("Y-m-d H:i:s", $endtime);
                $tableOccurence->update(array('endtime' => $nextEndDate), array('occurrence_id =?' => $occurrence->occurrence_id));
                $occurrenceEndStartDate = $occurrence->starttime;
                $occurrenceEndDate = $nextEndDate;
            } catch (Exception $E) {
                
            }
        }

//NOW CHECK IF THE END REPEAT TIME IS ALSO INCREASED. IF YES THEN WE WILL ALSO ADD NEW ROWS TO THE TABLE.

        $dateInfo = Engine_Api::_()->siteevent()->dbToUserDateTime(array('starttime' => $occurrenceEndStartDate, 'endtime' => $occurrenceEndDate));


        $values['starttime'] = $dateInfo['starttime'];
        $values['endtime'] = $dateInfo['endtime'];
        $postedValues['action'] = 'editdates';

        $this->addorEditDates($postedValues, $values, $event_id, 'create');
    }

    private function _packagesEnabled() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (_CLIENT_TYPE && ((_CLIENT_TYPE == 'android' && _ANDROID_VERSION >= '1.6.3') || _CLIENT_TYPE == 'ios' && _IOS_VERSION >= '1.5.1')) {
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventpaid') && $this->_hasPackageEnable) {
                $packageCount = Engine_Api::_()->getDbTable('packages', 'siteeventpaid')->getPackageCount();
                if ($packageCount == 1) {
                    $package = Engine_Api::_()->getDbTable('packages', 'siteeventpaid')->getEnabledPackage();
                    if (($package->price == '0.00')) {
                        return 0;
                    } else
                        return 1;
                } else {
                    $overview = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.overview', 1);
                    $package_description = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.package.description', 1);
                    $paginator = Engine_Api::_()->getDbtable('packages', 'siteeventpaid')->getPackagesSql($viewer_id);
                    $getTotalItemCount = $paginator->getTotalItemCount();

                    if ($getTotalItemCount > 0) {
                        return 1;
                    } else {
                        return 0;
                    }
                }
            }
        }
        return 0;
    }

    /*
     * Add capacity and waitlist
     */

    public function capacityAndWaitlistAction() {
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
                'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Enter the value of maximum members who can join this event. After this capacity is reached, members will be able to apply for the waitlist of this event.'),
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

}
