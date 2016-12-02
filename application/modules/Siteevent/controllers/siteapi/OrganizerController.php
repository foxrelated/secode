<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    TopicController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_OrganizerController extends Siteapi_Controller_Action_Standard {

    //COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
    public function init() {

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, "view")->isValid())
            $this->respondWithError('unauthorized');

        //RETURN IF SUBJECT IS ALREADY SET
        if (Engine_Api::_()->core()->hasSubject())
            $this->respondWithError('unauthorized');

        //SET TOPIC OR EVENT SUBJECT
        if (0 != ($organizer_id = (int) $this->_getParam('organizer_id')) &&
                null != ($organizer = Engine_Api::_()->getItem('siteevent_organizer', $organizer_id))) {
            Engine_Api::_()->core()->setSubject($organizer);
        }
    }

    public function viewAction() {
        //RETURN IF EVENT SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_organizer'))
            $this->respondWithError('unauthorized');

        $showEvents = $this->_getParam('showEvents', 1);
        $profileTabs = $this->_getParam('profileTabs', 1);
        $getInfo = $this->_getParam('getInfo', null);


        $viewtype = $this->_getParam('viewType', 'upcoming');

        //GET EVENT SUBJECT
        $organizer = Engine_Api::_()->core()->getSubject();
        if (empty($organizer)) {
            return $this->respondWithError('no_record');
        }
        $response = $organizer->toArray();

        $suffix = '';
        if (strpos($response['web_url'], "http") === false)
            $suffix = "http://";
        if (isset($response['facebook_url']) && !empty($response['facebook_url']))
            $response['facebook_url'] = 'https://facebook.com/' . $response['facebook_url'];
        if (isset($response['twitter_url']) && !empty($response['twitter_url']))
            $response['twitter_url'] = 'https://twitter.com/' . $response['twitter_url'];
        if (isset($response['web_url']) && !empty($response['web_url']))
            $response['web_url'] = $suffix . $response['web_url'];

        $contentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($organizer);
        $response = array_merge($response, $contentImages);
        $response['countOrganizedEvent'] = $organizer->countOrganizedEvent();
        $response['addedBy'] = $organizer->getOwner()->displayname;

        if (isset($getInfo) && !empty($getInfo)) {
            $getInfoArray['Added By'] = $response['addedBy'];
            $getInfoArray['Events Hosted '] = $organizer->countOrganizedEvent();

            $allowedInfo = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.hostinfo', array('body', 'sociallinks'));

            if (in_array('body', $allowedInfo)) {
                if (isset($response['description']) && !empty($response['description']))
                    $getInfoArray['Description'] = strip_tags($response['description']);
            }

            if (in_array('sociallinks', $allowedInfo)) {
                if (isset($response['facebook_url']) && !empty($response['facebook_url']))
                    $getInfoArray['Facebook URL'] = $response['facebook_url'];
                if (isset($response['twitter_url']) && !empty($response['twitter_url']))
                    $getInfoArray['Twitter URL'] = $response['twitter_url'];
                if (isset($response['web_url']) && !empty($response['web_url']))
                    $getInfoArray['Web URL'] = $response['web_url'];
            }

            $ratingEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2);
            if ($ratingEnable) {
                $tempRating = Engine_Api::_()->getDbtable('events', 'siteevent')->avgTotalRating(
                        array('host_type' => $organizer->getType(), 'host_id' => $organizer->getIdentity(), 'more_than' => 0));

                // Added variable for rating to show rating bar
                if (_CLIENT_TYPE && ((_CLIENT_TYPE == 'ios') && _IOS_VERSION && _IOS_VERSION >= '1.5.3') || (_CLIENT_TYPE == 'android') && _ANDROID_VERSION && _ANDROID_VERSION >= '1.7') {
                    if (isset($tempRating) && !empty($tempRating))
                        $getInfoArray['total_rating'] = $tempRating;
                } else {
                    if (isset($tempRating) && !empty($tempRating))
                        $getInfoArray['Total Rating'] = $tempRating;
                }
            }

            // Added variable for description to show full description
            if (_CLIENT_TYPE && ((_CLIENT_TYPE == 'ios') && _IOS_VERSION && _IOS_VERSION >= '1.5.3') || (_CLIENT_TYPE == 'android') && _ANDROID_VERSION && _ANDROID_VERSION >= '1.7') {
                if (in_array('body', $allowedInfo)) {
                    if (isset($response['description']) && !empty($response['description'])) {
                        $getInfoArray['description'] = strip_tags($response['description']);
                        if (isset($getInfoArray['Description']) && !empty($getInfoArray['Description']))
                            unset($getInfoArray['Description']);
                    }
                }
            }

            if (isset($getInfoArray) && !empty($getInfoArray))
                $this->respondWithSuccess($getInfoArray, true);
        }

//        //GET EVENTS PAGINATOR
//        $paginator = Engine_Api::_()->getDbTable('events', 'siteevent')->getSiteeventsPaginator($values, $customProfileFields);
//        $paginator->setItemCountPerPage($this->getRequestParam("limit", 20));
//        $paginator->setCurrentPageNumber($this->getRequestParam("page", 1));
//
//        //SET VIEW
//        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
//        $response['canCreate'] = Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, 'create');
//        $response["getTotalItemCount"] = $getTotalItemCount = $paginator->getTotalItemCount();
//
//
//        if (isset($showEvents) && !empty($showEvents) && empty($getInfo)) {
//            try {
//                $values['viewType'] = $viewtype;
//                $values['host_type'] = 'siteevent_organizer';
//                $values['host_id'] = $organizer->getIdentity();
//
//
//                if (!empty($getTotalItemCount)) {
//                    foreach ($paginator as $eventObj) {
//                        $event = $eventObj->toArray();
//
//                        // ADD OWNER IMAGES
//                        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($eventObj, true);
//                        $event = array_merge($event, $getContentImages);
//                        $event["owner_title"] = $eventObj->getOwner()->getTitle();
//
//                        // ADD EVENT IMAGES
//                        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($eventObj);
//                        $event = array_merge($event, $getContentImages);
//                        $tempResponse[] = $event;
//                    }
//                    $response['events'] = $tempResponse;
//                }
//            } catch (Exception $e) {
//                
//            }
//        }


        if (isset($profileTabs) && !empty($profileTabs) && empty($getInfo)) {
            $profileTabsArray[] = array(
                'name' => 'organizer_info',
                'label' => $this->translate('Info'),
                'url' => 'advancedevents/organizer/' . $organizer->getIdentity(),
                'urlParams' => array(
                    'getInfo' => 1
                )
            );

            if ($organizer->countOrganizedEvent() > 0) {
                $profileTabsArray[] = array(
                    'name' => 'organizer_events',
                    'label' => $this->translate('Events'),
                    'url' => 'advancedevents/',
                    'totalItemCount' => $organizer->countOrganizedEvent(),
                    'urlParams' => array(
                        'host_type' => 'siteevent_organizer',
                        'host_id' => $organizer->getIdentity()
                    )
                );
            }

            $response['profileTabs'] = $profileTabsArray;
        }
        $this->respondWithSuccess($response, true);
    }

}
