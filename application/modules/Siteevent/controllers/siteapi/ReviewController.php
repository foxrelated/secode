<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ReviewController.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_ReviewController extends Siteapi_Controller_Action_Standard {

    public function init() {

        $event_id = $this->_getParam('event_id');
        $review_id = $this->_getParam('review_id', $this->_getParam('review_id', null));
        if ($review_id) {
            $review = Engine_Api::_()->getItem('siteevent_review', $review_id);
            if ($review) {
                Engine_Api::_()->core()->setSubject($review);
            }
        } else {
            if (!empty($event_id)) {
                $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
                if (!empty($siteevent))
                    Engine_Api::_()->core()->setSubject($siteevent);
            }
        }


        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, "view")->isValid())
            $this->_forward('throw-error', 'index', 'siteevent', array(
                "error_code" => "unauthorized"
            ));
        return;
    }

    /**
     * Throw the init constructor errors.
     *
     * @return array
     */
    public function throwErrorAction() {
        $message = $this->getRequestParam("message", null);
        if (($error_code = $this->getRequestParam("error_code")) && !empty($error_code)) {
            if (!empty($message))
                $this->respondWithValidationError($error_code, $message);
            else
                $this->respondWithError($error_code);
        }

        return;
    }

    public function browseAction() {

        $this->validateRequestMethod();

        //GET VIEWER INFO
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //EVENT SUBJECT SHOULD BE SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event'))
            $this->respondWithError('no_record');

        $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        $event_id = $siteevent->event_id;

        //GET PARAMS
        $params['type'] = '';

        $params = $this->_getAllParams();
        if (!isset($params['order']) || empty($params['order']))
            $params['order'] = 'recent';
        if (isset($params['show'])) {

            switch ($params['show']) {
                case 'friends_reviews':
                    $params['user_ids'] = $viewer->membership()->getMembershipsOfIds();
                    if (empty($params['user_ids']))
                        $params['user_ids'] = -1;
                    break;
                case 'self_reviews':
                    $params['user_id'] = $viewer_id;
                    break;
                case 'featured':
                    $params['featured'] = 1;
                    break;
            }
        }
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        $params['resource_type'] = 'siteevent_event';
        $params['event_id'] = $event_id;
        if (isset($params['user_id']) && !empty($params['user_id']))
            $user_id = $params['user_id'];
        else
            $user_id = $viewer_id;

        //Check event is end or not
        $endDate = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($siteevent->event_id);
        $currentDate = date('Y-m-d H:i:s');
        $endDate = strtotime($endDate);
        $currentDate = strtotime($currentDate);
        $rateuser = Engine_Api::_()->getDbTable("categories", "siteevent")->isGuestReviewAllowed($siteevent->category_id);

        if ($endDate > $currentDate && empty($rateuser)) {
            $this->respondWithError('no_record');
        }

        //GET REVIEW TABLE
        $reviewTable = Engine_Api::_()->getDbTable('reviews', 'siteevent');
        //GET RATING TABLE
        $ratingTable = Engine_Api::_()->getDbTable('ratings', 'siteevent');

        $type = 'user';
        $level_id = $viewer->level_id;

        try {
            //CUSTOM FIELD WORK
            //$customFieldValues = array_intersect_key($searchParams, $searchForm->getFieldElements());
            //GET PAGINATOR
            //GET REVIEW TABLE
            $reviewTable = Engine_Api::_()->getDbTable('reviews', 'siteevent');
            $paginator = $reviewTable->getReviewsPaginator($params, $customFieldValues);
            $paginator->setItemCountPerPage(10);
            $paginator->setCurrentPageNumber($this->_getParam('page', 1));

            if (isset($params['subcategory_id']) && $params['subcategory_id'])
                $searchParams['subcategory_id'] = $params['subcategory_id'];
            if (isset($params['subsubcategory_id']) && $params['subsubcategory_id'])
                $searchParams['subsubcategory_id'] = $params['subsubcategory_id'];

            //GET TOTAL REVIEWS
            $totalReviews = $paginator->getTotalItemCount();


            //START TOP SECTION FOR OVERALL RATING AND IT'S PARAMETER
            $params['resource_id'] = $event_id;
            $params['resource_type'] = $siteevent->getType();
            $params['viewer_id'] = $viewer_id;
            $params['type'] = 'user';
            $noReviewCheck = $reviewTable->getAvgRecommendation($params);
            if (!empty($noReviewCheck)) {
                $noReviewCheck = $noReviewCheck->toArray();
                if ($noReviewCheck)
                    $recommend_percentage = round($noReviewCheck[0]['avg_recommend'] * 100, 3);
            }

            for ($i = 5; $i > 0; $i--) {
                $ratingCount[$i] = $ratingTable->getNumbersOfUserRating($event_id, 'user', 0, $i, 0, 'siteevent_event', array());
            }
            $ratingData = $ratingTable->ratingbyCategory($event_id, $type, $siteevent->getType());
            $hasPosted = $reviewTable->canPostReview($params);
            $reviewRateMyData = $ratingTable->ratingsData($hasPosted);
            $coreApi = Engine_Api::_()->getApi('settings', 'core');

            $siteevent_proscons = $coreApi->getSetting('siteevent.proscons', 1);
            $siteevent_limit_proscons = $coreApi->getSetting('siteevent.limit.proscons', 500);
            $siteevent_recommend = $coreApi->getSetting('siteevent.recommend', 1);
            $siteevent_report = $coreApi->getSetting('siteevent.report', 1);
            $siteevent_email = $coreApi->getSetting('siteevent.email', 1);
            $siteevent_share = $coreApi->getSetting('siteevent.share', 1);

            $create_level_allow = $create_level_allow = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'siteevent_event', "review_create");

            $create_review = ($siteevent->owner_id == $viewer_id) ? Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.allowownerreview', 1) : 1;

            if (!$create_review || empty($create_level_allow)) {
                $can_create = 0;
            } else {
                $can_create = 1;
            }

            $can_delete = Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "review_delete");

            $can_reply = Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "review_reply");

            $can_update = Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "review_update");

            if (isset($params['getRating']) && !empty($params['getRating'])) {
                $ratings['rating_avg'] = $siteevent->rating_avg;
                $ratings['rating_users'] = $siteevent->rating_users;
                $ratings['breakdown_ratings_params'] = $ratingCount;
                if (isset($reviewRateMyData) && is_array($reviewRateMyData) && !empty($reviewRateMyData))
                    $ratings['myRatings'] = $reviewRateMyData;
                if (isset($hasPosted) && !empty($hasPosted))
                    $ratings['review_id'] = $hasPosted;
                $ratings['recomended'] = $noReviewCheck[0]['avg_recommend'];
                $response['ratings'] = $ratings;
            }
            $metaParams = array();
            $response['total_reviews'] = $totalReviews;

            //GET EVENT CATEGORY TABLE
            $tableCategory = Engine_Api::_()->getDbTable('categories', 'siteevent');
            $request = Zend_Controller_Front::getInstance()->getRequest();

            $category_id = $request->getParam('category_id', null);

            if (!empty($category_id)) {

                $metaParams['categoryname'] = Engine_Api::_()->getItem('siteevent_category', $category_id)->getCategorySlug();

                $subcategory_id = $request->getParam('subcategory_id', null);

                if (!empty($subcategory_id)) {

                    $metaParams['subcategoryname'] = Engine_Api::_()->getItem('siteevent_category', $subcategory_id)->getCategorySlug();

                    $subsubcategory_id = $request->getParam('subsubcategory_id', null);

                    if (!empty($subsubcategory_id)) {

                        $metaParams['subsubcategoryname'] = Engine_Api::_()->getItem('siteevent_category', $subsubcategory_id)->getCategorySlug();
                    }
                }
            }

            //SET META TITLES
            //TODO ERROR IN SET META TITLES
            // Engine_Api::_()->siteevent()->setMetaTitles($metaParams);

            $allow_review = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.allowreview', 1);

            if (empty($allow_review)) {
                $this->respondWithError('unauthorized');
            }

            $metaParams['event_type_title'] = $this->translate('Events');

            //GET TAG
            if ($this->_getParam('search', null)) {
                $metaParams['search'] = $this->_getParam('search', null);
            }
            foreach ($paginator as $review) {
                $params = $review->toArray();

                if (isset($params['body']) && !empty($params['body']))
                    $params['body'] = strip_tags($params['body']);

                $params ["owner_title"] = $review->getOwner()->getTitle();
                // owner image Add images 
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($review, true);
                $params = array_merge($params, $getContentImages);
                $event_id = $review->resource_id;
                $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
                $params['event_title'] = $siteevent->title;
                $response['content_title'] = $siteevent->title;
                $user_ratings = Engine_Api::_()->getDbtable('ratings', 'siteevent')->ratingsData($review->review_id, $review->getOwner()->getIdentity(), $review->resource_id, 0);
                $params['overall_rating'] = $user_ratings[0]['rating'];
                $params['category_name'] = Engine_Api::_()->getItem('siteevent_category', $siteevent->category_id)->category_name;
                $helpfulTable = Engine_Api::_()->getDbtable('helpful', 'siteevent');
                $helpful_entry = $helpfulTable->getHelpful($review->review_id, $viewer_id, 1);
                $nothelpful_entry = $helpfulTable->getHelpful($review->review_id, $viewer_id, 2);
                $params['is_helpful'] = ($helpful_entry) ? true : false;
                $params['is_not_helpful'] = ($nothelpful_entry) ? true : false;
                $params['helpful_count'] = $review->getCountHelpful(1);
                $params['nothelpful_count'] = $review->getCountHelpful(2);

                // Add owner images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($siteevent);
                $params = array_merge($params, $getContentImages);
                $tempResponse[] = $params;
            }
            if (!empty($tempResponse))
                $response['reviews'] = $tempResponse;
            $this->respondWithSuccess($response, true);
        } catch (Exception $ex) {
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    //ACTION FOR WRITE A REVIEW
    public function createAction() {
        //EVENT SUBJECT SHOULD BE SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event'))
            $this->respondWithError('no_record');

        //GET VIEWER INFO
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        //GET EVENT SUBJECT
        $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');
        if (!Engine_Api::_()->siteevent()->allowReviewCreate($siteevent)) {
            $this->respondWithError('unauthorized');
        }

        //FETCH REVIEW CATEGORIES
        $categoryIdsArray = array();
        $categoryIdsArray[] = $siteevent->category_id;
        $categoryIdsArray[] = $siteevent->subcategory_id;
        $categoryIdsArray[] = $siteevent->subsubcategory_id;
        $profileTypeReview = Engine_Api::_()->getDbtable('categories', 'siteevent')->getProfileType($categoryIdsArray, 0, 'profile_type_review');

        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $can_create = Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "review_create");

        if (empty($can_create)) {
            $this->respondWithError('unauthorized');
        }
        $coreApi = Engine_Api::_()->getApi('settings', 'core');
        $siteevent_proscons = $coreApi->getSetting('siteevent.proscons', 1);
        $siteevent_limit_proscons = $coreApi->getSetting('siteevent.limit.proscons', 500);
        $siteevent_recommend = $coreApi->getSetting('siteevent.recommend', 1);

        $ratingParams = Engine_Api::_()->getDbtable('ratingparams', 'siteevent')->reviewParams($categoryIdsArray, 'siteevent_event');
        $ratingParam[] = array(
            'type' => 'Rating',
            'name' => 'review_rate_0',
            'label' => $this->translate('Overall Rating')
        );

        foreach ($ratingParams as $ratingparam_id) {
            $ratingParam[] = array(
                'type' => 'Rating',
                'name' => 'review_rate_' . $ratingparam_id->ratingparam_id,
                'label' => $ratingparam_id->ratingparam_name
            );
        }
        if ($this->getRequest()->isGet()) {
            $allowReview = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.allowreview', 1);
            if (isset($allowReview) && !empty($allowReview))
                $response['form'] = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->getReviewCreateForm(array("settingsReview" => array('siteevent_proscons' => $siteevent_proscons, 'siteevent_limit_proscons' => $siteevent_limit_proscons, 'siteevent_recommend' => $siteevent_recommend), 'item' => $siteevent, 'profileTypeReview' => $profileTypeReview));
            $response['ratingParams'] = $ratingParam;
            $this->respondWithSuccess($response, true);
        }

        if ($this->getRequest()->isPost()) {
            // CONVERT POST DATA INTO THE ARRAY.
            $values = array();

            $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->getReviewCreateForm(array("settingsReview" => array('siteevent_proscons' => $siteevent_proscons, 'siteevent_limit_proscons' => $siteevent_limit_proscons, 'siteevent_recommend' => $siteevent_recommend), 'item' => $siteevent, 'profileTypeReview' => $profileTypeReview));
            foreach ($getForm as $element) {
                if (isset($_REQUEST[$element['name']]))
                    $values[$element['name']] = $_REQUEST[$element['name']];
            }

            // START FORM VALIDATION
            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'siteevent')->getReviewCreateFormValidators(array("settingsReview" => array('siteevent_proscons' => $siteevent_proscons, 'siteevent_limit_proscons' => $siteevent_limit_proscons, 'siteevent_recommend' => $siteevent_recommend), 'item' => $siteevent, 'profileTypeReview' => $profileTypeReview));
            $values['validators'] = $validators;
            $validationMessage = $this->isValid($values);
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }
            $postData = $this->_getAllParams();
            if (empty($_REQUEST['review_rate_0'])) {
                $this->respondWithValidationError('validation_fail', "Overall Rating is required");
            }

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {

                $values['owner_id'] = $viewer_id;
                $values['resource_id'] = $siteevent->event_id;
                $values['resource_type'] = $siteevent->getType();
                $values['profile_type_review'] = $profileTypeReview;
                $values['type'] = $viewer_id ? 'user' : 'visitor';

                if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.recommend', 1)) {
                    $values['recommend'] = 0;
                }
                $reviewTable = Engine_Api::_()->getDbtable('reviews', 'siteevent');
                $review = $reviewTable->createRow();
                $review->setFromArray($values);
                $review->view_count = 1;
                $review->save();

//                    if (!empty($profileTypeReview)) {
//                        //SAVE CUSTOM VALUES AND PROFILE TYPE VALUE
//                        $form = new Siteevent_Form_Review_Create(array('item' => $siteevent, 'profileTypeReview' => $profileTypeReview));
//                        $form->populate($postData);
//                        $customfieldform = $form->getSubForm('fields');
//                        $customfieldform->setItem($review);
//                        $customfieldform->saveValues();
//                    }
                //INCREASE REVIEW COUNT IN EVENT TABLE
                if (!empty($viewer_id))
                    $siteevent->review_count++;
                $siteevent->save();

//                $params['event_id'] = $siteevent->event_id;
//                foreach ($ratingParam as $rating) {
//                    if (isset($_REQUEST[$rating['name']])) {
//                        $params['rating_id'] = $rating['name'];
//                        $params['rating'] = $_REQUEST[$rating['name']];
//                        $this->_rate($params, $review);
//                    }
//                }
                $reviewRatingTable = Engine_Api::_()->getDbtable('ratings', 'siteevent');
                if (!empty($review_id)) {
                    $reviewRatingTable->delete(array('review_id = ?' => $review->review_id));
                }

                $postData['user_id'] = $viewer_id;
                $postData['review_id'] = $review->review_id;
                $postData['category_id'] = $siteevent->category_id;
                $postData['resource_id'] = $review->resource_id;
                $postData['resource_type'] = $review->resource_type;

                $review_count = Engine_Api::_()->getDbtable('ratings', 'siteevent')->getReviewId($viewer_id, $siteevent->getType(), $review->resource_id);
                if (count($review_count) == 0 || empty($viewer_id)) {
                    //CREATE RATING DATA
                    $reviewRatingTable->createRatingData($postData, $values['type']);
                } else {
                    $reviewRatingTable->update(array('review_id' => $review->review_id), array('resource_type = ?' => $review->resource_type, 'user_id = ?' => $viewer_id, 'resource_id = ?' => $review->resource_id));
                }

                //UPDATE RATING IN RATING TABLE
                if (!empty($viewer_id)) {
                    $reviewRatingTable->listRatingUpdate($review->resource_id, $review->resource_type);
                }

                if (empty($review_id) && !empty($viewer_id)) {
                    $activityApi = Engine_Api::_()->getDbtable('actions', 'seaocore');

                    //ACTIVITY FEED
                    $action = $activityApi->addActivity($viewer, $siteevent, 'siteevent_review_add');

                    if ($action != null) {
                        $activityApi->attachActivity($action, $review);

                        //START NOTIFICATION AND EMAIL WORK
                        //Engine_Api::_()->siteevent()->sendNotificationEmail($siteevent, $action, 'siteevent_write_review', 'SITEEVENT_REVIEW_WRITENOTIFICATION_EMAIL', null, null, 'created', $review);
                        $isChildIdLeader = Engine_Api::_()->getDbtable('listItems', 'siteevent')->checkLeader($siteevent);

                        if (!empty($isChildIdLeader)) {
                            Engine_Api::_()->siteevent()->sendNotificationToFollowers($siteevent, 'siteevent_write_review');
                        }
                        //END NOTIFICATION AND EMAIL WORK
                    }
                }

                $db->commit();
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $ex->getMessage());
            }
        }
    }

    //ACTION FOR UPDATE THE REVIEW
    public function updateAction() {

        //REVIEW SUBJECT SHOULD BE SET
        if (!$this->_helper->requireSubject('siteevent_review')->isValid())
            $this->respondWithError('unauthorized');

        //GET VIEWER INFO
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (Engine_Api::_()->core()->hasSubject())
            $review = Engine_Api::_()->core()->getSubject('siteevent_review');

        $siteevent = Engine_Api::_()->core()->getSubject()->getParent();

        $review_id = $review->getIdentity();
        if (empty($siteevent))
            $this->respondWithError('no_record');

        //FETCH REVIEW CATEGORIES
        $categoryIdsArray = array();
        $categoryIdsArray[] = $siteevent->category_id;
        $categoryIdsArray[] = $siteevent->subcategory_id;
        $categoryIdsArray[] = $siteevent->subsubcategory_id;
        $profileTypeReview = Engine_Api::_()->getDbtable('categories', 'siteevent')->getProfileType($categoryIdsArray, 0, 'profile_type_review');

        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        $can_update = Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "review_update");

        if (empty($can_update)) {
            $this->respondWithError('unauthorized');
        }

        //GET RATING TABLE
        $ratingTable = Engine_Api::_()->getDbTable('ratings', 'siteevent');

        $coreApi = Engine_Api::_()->getApi('settings', 'core');
        $siteevent_proscons = $coreApi->getSetting('siteevent.proscons', 1);
        $siteevent_limit_proscons = $coreApi->getSetting('siteevent.limit.proscons', 500);
        $siteevent_recommend = $coreApi->getSetting('siteevent.recommend', 1);
        $review_id = (int) $this->_getParam('review_id');
        $review = Engine_Api::_()->core()->getSubject();


        $ratingParams = Engine_Api::_()->getDbtable('ratingparams', 'siteevent')->reviewParams($categoryIdsArray, 'siteevent_event');
        $ratingParam[] = array(
            'type' => 'Rating',
            'name' => 'review_rate_0',
            'label' => $this->translate($this->translate('Overall Rating'))
        );

        foreach ($ratingParams as $ratingparam_id) {
            $ratingParam[] = array(
                'type' => 'Rating',
                'name' => 'review_rate_' . $ratingparam_id->ratingparam_id,
                'label' => $this->translate($ratingparam_id->ratingparam_name)
            );
        }
        $ratingValues = array();
        $reviewRateMyDatas = $ratingTable->ratingsData($review_id);

        foreach ($reviewRateMyDatas as $reviewRateMyData) {
            $ratingValues['review_rate_' . $reviewRateMyData['ratingparam_id']] = $reviewRateMyData['rating'];
        }

        if ($this->getRequest()->isGet()) {
            $response['form'] = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->getReviewUpdateForm();
            $response['ratingParams'] = $ratingParam;
            if (isset($ratingValues) && !empty($ratingValues))
                $response['formValues'] = $ratingValues;
            $this->respondWithSuccess($response, true);
        }


        if ($this->getRequest()->isGet()) {
            $response = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->getReviewUpdateForm();
            $this->respondWithSuccess($response, true);
        }

        if ($this->getRequest()->isPost()) {
            $values = array();
            $getForm = Engine_Api::_()->getApi('Siteapi_Core', 'Siteevent')->getReviewUpdateForm();
            foreach ($getForm as $element) {
                if (isset($_REQUEST[$element['name']]))
                    $values[$element['name']] = $_REQUEST[$element['name']];
            }
            if (!empty($values['body']))
            // START FORM VALIDATION
                $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'siteevent')->getReviewUpdateFormValidators();
            $values['validators'] = $validators;
            $validationMessage = $this->isValid($values);
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }
            $postData = $this->_getAllParams();
            if (empty($_REQUEST['review_rate_0'])) {
                $this->respondWithValidationError('validation_fail', "Overall Rating is required");
            }
            try {
                $postData['user_id'] = $viewer_id;
                $postData['category_id'] = $siteevent->category_id;
                $postData['resource_id'] = $review->resource_id;
                $postData['resource_type'] = $siteevent->getType();
                $postData['review_id'] = $review_id;
                $postData['profile_type_review'] = $profileTypeReview;
                $reviewDescription = Engine_Api::_()->getDbtable('reviewDescriptions', 'siteevent');
                $reviewDescription->insert(array('review_id' => $review_id, 'body' => $postData['body'], 'modified_date' => date('Y-m-d H:i:s'), 'user_id' => $viewer_id));
                $reviewRatingTable = Engine_Api::_()->getDbtable('ratings', 'siteevent');
                $reviewRatingTable->delete(array('review_id = ?' => $review_id));

                //CREATE RATING DATA
                $reviewRatingTable->createRatingData($postData, 'user');

                Engine_Api::_()->getDbtable('ratings', 'siteevent')->listRatingUpdate($review->resource_id, $review->resource_type);
                $this->successResponseNoContent('no_content', true);

//                if (!empty($profileTypeReview)) {
//                    //SAVE CUSTOM VALUES AND PROFILE TYPE VALUE
//                    $form = new Siteevent_Form_Review_Create(array('item' => $siteevent, 'profileTypeReview' => $profileTypeReview));
//                    $form->populate($postData);
//                    $customfieldform = $form->getSubForm('fields');
//                    $customfieldform->setItem($review);
//                    $customfieldform->saveValues();
//                }
            } catch (Exception $ex) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $ex->getMessage());
            }
        }
//        else {
//            //REVIEW SUBJECT SHOULD BE SET
//            if (!$this->_helper->requireSubject('siteevent_review')->isValid())
//                return;
//
//            //GET VIEWER INFO
//            $viewer = Engine_Api::_()->user()->getViewer();
//            $viewer_id = $viewer->getIdentity();
//
//            $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject()->getParent();
//
//            $this->view->tab = $this->_getParam('tab');
//            //FATCH REVIEW CATEGORIES
//            $categoryIdsArray = array();
//            $categoryIdsArray[] = $siteevent->category_id;
//            $categoryIdsArray[] = $siteevent->subcategory_id;
//            $categoryIdsArray[] = $siteevent->subsubcategory_id;
//            $profileTypeReview = Engine_Api::_()->getDbtable('categories', 'siteevent')->getProfileType($categoryIdsArray, 0, 'profile_type_review');
//
//            $this->view->reviewCategory = Engine_Api::_()->getDbtable('ratingparams', 'siteevent')->reviewParams($categoryIdsArray, $siteevent->getType());
//
//            //COUNT REVIEW CATEGORY
//            $this->view->total_reviewcats = Count($this->view->reviewCategory);
//
//            //GET USER LEVEL ID
//            if (!empty($viewer_id)) {
//                $level_id = $viewer->level_id;
//            } else {
//                $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
//            }
//
//            $this->view->can_update = $can_update = Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "review_update");
//
//            if (empty($can_update)) {
//                return $this->_forwardCustom('requireauth', 'error', 'core');
//            }
//            $review_id = (int) $this->_getParam('review_id');
//
//            $review = Engine_Api::_()->core()->getSubject();
//            $this->view->reviewRateMyData = Engine_Api::_()->getDbtable('ratings', 'siteevent')->ratingsData($review_id);
//
//
//            $this->view->form = $form = new Siteevent_Form_Review_Update(array('item' => $siteevent));
//
//            if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
//              Zend_Registry::set('setFixedCreationForm', true);
//              Zend_Registry::set('setFixedCreationFormBack', 'Back');
//              Zend_Registry::set('setFixedCreationHeaderTitle', Zend_Registry::get('Zend_Translate')->_('Update your Review'));
//              Zend_Registry::set('setFixedCreationHeaderSubmit', Zend_Registry::get('Zend_Translate')->_('Submit'));
//              $this->view->form->setAttrib('id', 'siteevent_update');
//              Zend_Registry::set('setFixedCreationFormId', '#siteevent_update');
//              $this->view->form->removeElement('submit');
//              $form->setTitle(sprintf(Zend_Registry::get('Zend_Translate')->_('For %s'), $siteevent->getTitle()));
//              $form->setDescription('');
//           }
//            if ($this->getRequest()->isPost() && $this->getRequest()->getPost()) {
//                $postData = $this->getRequest()->getPost();
//                $otherValues = $form->getValues();
//                $postData = array_merge($postData, $otherValues);
//                $postData['user_id'] = $viewer_id;
//                $postData['category_id'] = $siteevent->category_id;
//                $postData['resource_id'] = $review->resource_id;
//                $postData['resource_type'] = $siteevent->getType();
//                $postData['review_id'] = $review_id;
//                $postData['profile_type_review'] = $profileTypeReview;
//                $reviewDescription = Engine_Api::_()->getDbtable('reviewDescriptions', 'siteevent');
//                $reviewDescription->insert(array('review_id' => $review_id, 'body' => $postData['body'], 'modified_date' => date('Y-m-d H:i:s'), 'user_id' => $viewer_id));
//                $reviewRatingTable = Engine_Api::_()->getDbtable('ratings', 'siteevent');
//                $reviewRatingTable->delete(array('review_id = ?' => $review_id));
//                //CREATE RATING DATA
//                $reviewRatingTable->createRatingData($postData, 'user');
//
//                Engine_Api::_()->getDbtable('ratings', 'siteevent')->listRatingUpdate($review->resource_id, $review->resource_type);
//
//                if (!empty($profileTypeReview)) {
//                    //SAVE CUSTOM VALUES AND PROFILE TYPE VALUE
//                    $form = new Siteevent_Form_Review_Create(array('item' => $siteevent, 'profileTypeReview' => $profileTypeReview));
//                    $form->populate($postData);
//                    $customfieldform = $form->getSubForm('fields');
//                    $customfieldform->setItem($review);
//                    $customfieldform->saveValues();
//                }
//
//                return $this->_redirectCustom($siteevent->getHref(array('tab' => $this->view->tab)), array('prependBase' => false));
//            }
//        }
    }

//    public function _rate($params = array(), $review) {
//
//        $viewer = Engine_Api::_()->user()->getViewer();
//        $viewer_id = $viewer->getIdentity();
//        $rating_id = $params['rating_id'];
//        $rating = $params ['rating'];
//        $event_id = $params['event_id'];
//        //GET EVENT SUBJECT
//        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
//        try {
//            $reviewRatingTable = Engine_Api::_()->getDbtable('ratings', 'siteevent');
//            if (count($review) == 0) {
//                //CREATE RATING DATA
//                $postData['user_id'] = $viewer_id;
//                $postData['review_id'] = 0;
//                $postData['category_id'] = $siteevent->category_id;
//                $postData['resource_id'] = $siteevent->event_id;
//                $postData['resource_type'] = $siteevent->getType();
//                $postData['review_rate_0'] = $rating;
//                $values['type'] = $viewer_id ? 'user' : 'visitor';
//                $reviewRatingTable->createRatingData($postData, $values['type']);
//            } else {
//                $reviewRatingTable->update(array('rating' => $rating), array('resource_type = ?' => $review->resource_type, 'user_id = ?' => $viewer_id, 'resource_id = ?' => $siteevent->event_id, 'ratingparam_id' => $rating_id));
//            }
//            //UPDATE RATING IN RATING TABLE
//            if (!empty($viewer_id) && (count($review) == 0)) {
//                $rating_only = 1;
//                $user_rating = $reviewRatingTable->listRatingUpdate($siteevent->event_id, $siteevent->getType(), $rating_only);
//            } else {
//                $rating_only = 1;
//                $user_rating = $reviewRatingTable->listRatingUpdate($review->resource_id, $review->resource_type, $rating_only);
//            }
//
//            $totalUsers = $reviewRatingTable->select()
//                            ->from($reviewRatingTable->info('name'), 'COUNT(*) AS count')
//                            ->where('user_id != ?', 0)
//                            ->where('type = ?', 'user')
//                            ->query()->fetchColumn();
//            return;
//        } catch (Exception $ex) {
//            
//        }
//    }
    //ACTION FOR MARKING HELPFUL REVIEWS
    public function helpfulAction() {

        //NOT VALID USER THEN RETURN
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        //GET VIEWER DETAIL
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        //GET RATING
        $helpful = $this->_getParam('helpful');
        if (!isset($helpful))
            $this->respondWithValidationError('validation_fail', 'field is required');

        //GET REVIEW ID
        $review_id = $this->_getParam('review_id');

        if (Engine_Api::_()->core()->hasSubject())
            $review = Engine_Api::_()->core()->getSubject();

        if (empty($review))
            $this->respondWithError('no_record');

        $siteevent = Engine_Api::_()->core()->getSubject()->getParent();
        if (empty($siteevent))
            $this->respondWithError('no_record');

        try {
            //GET HELPFUL TABLE
            $helpfulTable = Engine_Api::_()->getDbtable('helpful', 'siteevent');

            $already_entry = $helpfulTable->getHelpful($review_id, $viewer_id, $helpful);

            if (!empty($already_entry)) {
                $this->respondWithValidationError('validation_fail', 'Already given feedback');
            }

            //MAKE ENTRY FOR HELPFUL
            $helpfulTable->setHelful($review_id, $viewer_id, $helpful);

            $params = $review->toArray();
            $params ["owner_title"] = $review->getOwner()->getTitle();
            // owner image Add images 
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($review, true);
            $params = array_merge($params, $getContentImages);
            $event_id = $review->resource_id;
            $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
            $params['event_title'] = $siteevent->title;
            $user_ratings = Engine_Api::_()->getDbtable('ratings', 'siteevent')->ratingsData($review->review_id, $review->getOwner()->getIdentity(), $review->resource_id, 0);
            $params['overall_rating'] = $user_ratings[0]['rating'];
            $params['category_name'] = Engine_Api::_()->getItem('siteevent_category', $siteevent->category_id)->category_name;
            $helpfulTable = Engine_Api::_()->getDbtable('helpful', 'siteevent');
            $helpful_entry = $helpfulTable->getHelpful($review->review_id, $viewer_id, 1);
            $nothelpful_entry = $helpfulTable->getHelpful($review->review_id, $viewer_id, 2);
            $params['is_helpful'] = $helpful_entry;
            $params['is_not_helpful'] = $nothelpful_entry;
            $params['helpful_count'] = $review->getCountHelpful(1);
            $params['nothelpful_count'] = $review->getCountHelpful(2);

            // Add owner images
            $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($siteevent);
            $params = array_merge($params, $getContentImages);

            $this->respondWithSuccess($params, true);
        } catch (Exception $ex) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

    //ACTION FOR VIEW REVIEW
    public function viewAction() {
        // Validate request methods
        $this->validateRequestMethod();

        //IF ANONYMOUS USER THEN SEND HIM TO SIGN IN PAGE
        $check_anonymous_help = $this->_getParam('anonymous');
        if ($check_anonymous_help) {
            if (!$this->_helper->requireUser()->isValid())
                $this->respondWithError('unauthorized');
        }

        //GET LOGGED IN USER INFORMATION
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!Engine_Api::_()->core()->hasSubject('siteevent_review')) {
            $this->respondWithError('no_record');
        }

        //GET EVENT ID AND OBJECT
        $siteevent = Engine_Api::_()->core()->getSubject()->getParent();

        //WHO CAN VIEW THE EVENTS
        if (!$this->_helper->requireAuth()->setAuthParams($siteevent, null, "view")->isValid() || !Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.allowreview', 1)) {
            $this->respondWithError('unauthorized');
        }

        $review = Engine_Api::_()->core()->getSubject();
        if (empty($review)) {
            $this->respondWithError('no_record');
        }

        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }
        //GET LEVEL SETTING
        $can_view = Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "view");

        if ($can_view != 2 && $viewer_id != $siteevent->owner_id && ($siteevent->draft == 1 || $siteevent->search == 0 || $siteevent->approved != 1)) {
            $this->respondWithError('unauthorized');
        }

        if ($can_view != 2 && ($review->status != 1 && empty($review->owner_id))) {
            $this->respondWithError('unauthorized');
        }

        $params = array();
        $params = $review->toArray();
        $params['owner_title'] = $review->getOwner()->getTitle();
        $params['event_type_title'] = 'Events';
        $params['helpful_count'] = Engine_Api::_()->getDbTable('helpful', 'siteevent')->getCountHelpful($review->review_id, 1);
        //GET LOCATION
        if (!empty($siteevent->location) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1)) {
            $params['location'] = $siteevent->location;
        }
        $params['tag'] = $siteevent->getKeywords(', ');

        //GET EVENT CATEGORY TABLE
        $tableCategory = Engine_Api::_()->getDbTable('categories', 'siteevent');

        $category_id = $siteevent->category_id;
        if (!empty($category_id)) {

            $params['categoryname'] = Engine_Api::_()->getItem('siteevent_category', $category_id)->category_name;

            $subcategory_id = $siteevent->subcategory_id;

            if (!empty($subcategory_id)) {

                $params['subcategoryname'] = Engine_Api::_()->getItem('siteevent_category', $subcategory_id)->category_name;

                $subsubcategory_id = $siteevent->subsubcategory_id;

                if (!empty($subsubcategory_id)) {

                    $params['subsubcategoryname'] = Engine_Api::_()->getItem('siteevent_category', $subsubcategory_id)->category_name;
                }
            }
        }
        $response['response'] = $params;
        $this->respondWithSuccess($response, true);
    }

    //ACTION FOR DELETING REVIEW
    public function deleteAction() {

        $this->validateRequestMethod('DELETE');

        //ONLY LOGGED IN USER CAN DELETE REVIEW
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        //SUBJECT SHOULD BE SET
        if (!$this->_helper->requireSubject('siteevent_review')->isValid())
            $this->respondWithError('no_record');

        //GET VIEWER ID
        $viewer = Engine_Api::_()->user()->getViewer();
        $review = Engine_Api::_()->core()->getSubject();
        $viewer_id = $viewer->getIdentity();
        $siteevent = $review->getParent();

        //GET REVIEW ID AND REVIEW OBJECT
        $review_id = $this->_getParam('review_id');

        //AUTHORIZATION CHECK
        $can_delete = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'siteevent_event', "review_delete");

        //WHO CAN DELETE THE REVIEW
        if (empty($can_delete) || ($can_delete == 1 && $viewer_id != $review->owner_id)) {
            $this->respondWithError('unauthorized');
        }

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {

            //DELETE REVIEW FROM DATABASE
            Engine_Api::_()->getItem('siteevent_review', (int) $review_id)->delete();
            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $ex) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $ex->getMessage());
        }
    }

}
