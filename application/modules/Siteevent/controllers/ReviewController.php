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
class Siteevent_ReviewController extends Seaocore_Controller_Action_Standard {

    //COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
    public function init() {

        //CHECK SUBJECT
        if (Engine_Api::_()->core()->hasSubject())
            return;

        if (!in_array($this->_getParam('action', null), array('browse', 'categories'))) {

            //SET REVIEW SUBJECT
            if (0 != ($review_id = (int) $this->_getParam('review_id')) &&
                    null != ($review = Engine_Api::_()->getItem('siteevent_review', $review_id))) {
                Engine_Api::_()->core()->setSubject($review);
            } else if (0 != ($event_id = (int) $this->_getParam('event_id')) &&
                    null != ($siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id))) {
                Engine_Api::_()->core()->setSubject($siteevent);
            }

            if (!empty($review)) {
                $siteevent = $review->getParent();
            }


            if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) || Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 1) {
                return;
            }

            //AUTHORIZATION CHECK
            if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, "view")->isValid())
                return;
        }
    }

    public function browseAction() {

        //GET VIEWER INFO
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

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

        $params['resource_type'] = 'siteevent_event';

        $searchForm = $this->view->searchForm = new Siteevent_Form_Review_Search(array('type' => 'siteevent_review'));
        $searchForm->populate($this->_getAllParams());
        $this->view->formValues = $searchParams = $searchForm->getValues();

        //GET REVIEW TABLE
        $reviewTable = Engine_Api::_()->getDbTable('reviews', 'siteevent');

        //CUSTOM FIELD WORK
        $customFieldValues = array_intersect_key($searchParams, $searchForm->getFieldElements());

        //GET PAGINATOR
        $paginator = $reviewTable->getReviewsPaginator($params, $customFieldValues);
        $this->view->paginator = $paginator->setItemCountPerPage(10);
        $this->view->paginator = $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        if (isset($params['subcategory_id']) && $params['subcategory_id'])
            $searchParams['subcategory_id'] = $params['subcategory_id'];
        if (isset($params['subsubcategory_id']) && $params['subsubcategory_id'])
            $searchParams['subsubcategory_id'] = $params['subsubcategory_id'];
        $this->view->searchParams = $searchParams;

        //GET TOTAL REVIEWS
        $this->view->totalReviews = $paginator->getTotalItemCount();

        $metaParams = array();

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
        Engine_Api::_()->siteevent()->setMetaTitles($metaParams);

        $allow_review = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.allowreview', 1);

        if (empty($allow_review)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        $metaParams['event_type_title'] = $this->view->translate('Events');


        //GET TAG
        if ($this->_getParam('search', null)) {
            $metaParams['search'] = $this->_getParam('search', null);
        }

        //SET META KEYWORDS
        Engine_Api::_()->siteevent()->setMetaKeywords($metaParams);
        
        // For App
      
    $items_count = 10;
    $paginator->setItemCountPerPage($items_count);
    $this->view->page = $this->_getParam('page', 1);
    $this->view->autoContentLoad = $isappajax = $this->_getParam('isappajax', false);
    $totalItemCount = $paginator->getTotalItemCount();
    $this->view->totalPages = ceil(($totalItemCount) / $items_count);
        
        
        //RENDER
    if(!$isappajax){
        $this->_helper->content
                //->setNoRender()
                ->setEnabled()
        ;
    }
    }

    //GET CATEGORIES ACTION
    public function categoriesAction() {

        $element_value = $this->_getParam('element_value', 1);
        $element_type = $this->_getParam('element_type', 'category_id');
        $showAllCategories = $this->_getParam('showAllCategories', 1);

        $categoriesTable = Engine_Api::_()->getDbTable('categories', 'siteevent');
        $categoryTableName = $categoriesTable->info('name');
        $select = $categoriesTable->select()
                ->from($categoryTableName, array('category_id', 'category_name'))
                ->where($categoryTableName.".$element_type = ?", $element_value);

        if ($element_type == 'category_id') {
            $select->where('cat_dependency = ?', 0)->where('subcat_dependency = ?', 0);
        } elseif ($element_type == 'cat_dependency') {
            $select->where('subcat_dependency = ?', 0);
        } elseif ($element_type == 'subcat_dependency') {
            $select->where('cat_dependency = ?', $element_value);
        }

        if (!$showAllCategories) {
            $tableEvent = Engine_Api::_()->getDbTable('events', 'siteevent');
            $tableEventName = $tableEvent->info('name');
            $select->setIntegrityCheck();
            if($element_type == 'subcat_dependency') {
                $select->join($tableEventName, "$tableEventName.subcategory_id=$categoryTableName.$element_type", null);
            }
            else {
                $select->join($tableEventName, "$tableEventName.category_id=$categoryTableName.$element_type", null);
            }
            $select->where($tableEventName . '.approved = ?', 1)->where($tableEventName . '.draft = ?', 0)->where($tableEventName . '.search = ?', 1)->where($tableEventName . '.closed = ?', 0);
            $select = $tableEvent->getNetworkBaseSql($select, array('not_groupBy' => 1));
        }
        
        $select->group($categoryTableName.'.category_id');

        $categoriesData = $categoriesTable->fetchAll($select);

        $categories = array();
        if (Count($categoriesData) > 0) {
            foreach ($categoriesData as $category) {
                $data = array();
                $data['category_name'] = $this->view->translate($category->category_name);
                $data['category_id'] = $category->category_id;
                $data['category_slug'] = $category->getCategorySlug();
                $categories[] = $data;
            }
        }

        $this->view->categories = $categories;
    }

    //ACTION FOR WRITE A REVIEW
    public function createAction() {

        //EVENT SUBJECT SHOULD BE SET
        if (!$this->_helper->requireSubject('siteevent_event')->isValid())
            return;
        
        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
        //GET VIEWER INFO
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET EVENT SUBJECT
        $siteevent = Engine_Api::_()->core()->getSubject();

        if (!Engine_Api::_()->siteevent()->allowReviewCreate($siteevent)) {
            return;
        }

        //FATCH REVIEW CATEGORIES
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
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        $postData = $this->getRequest()->getPost();

        if ($this->getRequest()->isPost() && $postData) {
            $isvalid = 1;
            if (empty($viewer_id) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.captcha', 1)) {
                $captchas = $postData['captcha'];
                $isvalid = $this->validateCaptcha($captchas);
            }
            if (!$isvalid) {
                echo Zend_Json::encode(array('captchaError' => 1));
                exit();
            } else {
                $db = Engine_Db_Table::getDefaultAdapter();
                $db->beginTransaction();

                try {

                    $coreApi = Engine_Api::_()->getApi('settings', 'core');
                    $this->view->siteevent_proscons = $siteevent_proscons = $coreApi->getSetting('siteevent.proscons', 1);
                    $this->view->siteevent_limit_proscons = $siteevent_limit_proscons = $coreApi->getSetting('siteevent.limit.proscons', 500);
                    $this->view->siteevent_recommend = $siteevent_recommend = $coreApi->getSetting('siteevent.recommend', 1);

                    $form = new Siteevent_Form_Review_Create(array("settingsReview" => array('siteevent_proscons' => $this->view->siteevent_proscons, 'siteevent_limit_proscons' => $this->view->siteevent_limit_proscons, 'siteevent_recommend' => $this->view->siteevent_recommend), 'item' => $siteevent, 'profileTypeReview' => $profileTypeReview));
                    $form->populate($postData);
                    $otherValues = $form->getValues();

                    $values = array_merge($postData, $otherValues);
                    $values['owner_id'] = $viewer_id;
                    $values['resource_id'] = $siteevent->event_id;
                    $values['resource_type'] = $siteevent->getType();
                    $values['profile_type_review'] = $profileTypeReview;
                    $values['type'] = $viewer_id ? 'user' : 'visitor';
                    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.recommend', 1)) {
                        $values['recommend'] = 1;
                    } else {
                        $values['recommend'] = 0;
                    }
                    $reviewTable = Engine_Api::_()->getDbtable('reviews', 'siteevent');
                    $review = $reviewTable->createRow();
                    $review->setFromArray($values);
                    $review->view_count = 1;
                    $review->save();

                    if (!empty($profileTypeReview)) {
                        //SAVE CUSTOM VALUES AND PROFILE TYPE VALUE
                        $form = new Siteevent_Form_Review_Create(array('item' => $siteevent, 'profileTypeReview' => $profileTypeReview));
                        $form->populate($postData);
                        $customfieldform = $form->getSubForm('fields');
                        $customfieldform->setItem($review);
                        $customfieldform->saveValues();
                    }

                    //INCREASE REVIEW COUNT IN EVENT TABLE
                    if (!empty($viewer_id))
                        $siteevent->review_count++;

                    $siteevent->save();

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
                            Engine_Api::_()->siteevent()->sendNotificationEmail($siteevent, $action, 'siteevent_write_review', 'SITEEVENT_REVIEW_WRITENOTIFICATION_EMAIL', null, null, 'created', $review);
                            $isChildIdLeader = Engine_Api::_()->getDbtable('listItems', 'siteevent')->checkLeader($siteevent);
                            if (!empty($isChildIdLeader)) {
                                Engine_Api::_()->siteevent()->sendNotificationToFollowers($siteevent, 'siteevent_write_review');
                            }
                            //END NOTIFICATION AND EMAIL WORK
                        }
                    }

                    $db->commit();
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
                echo Zend_Json::encode(array('captchaError' => 0, 'review_href' => $review->getHref()));
                exit();
            }
        }
        }else{
          
			//GET Event SUBJECT
			$this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject();

			$event_id = $siteevent->getIdentity();

			//GET REVIEW TABLE
			$reviewTable = Engine_Api::_()->getDbTable('reviews', 'siteevent');


			//GET VIEWER ID
			$viewer = Engine_Api::_()->user()->getViewer();
			$this->view->viewer_id = $viewer_id = $viewer->getIdentity();

			//GET USER LEVEL ID
			if (!empty($viewer_id)) {
				$this->view->level_id = $level_id = $viewer->level_id;
			} else {
				$this->view->level_id = $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
			}
      
			$autorizationApi = Engine_Api::_()->authorization();
			$this->view->create_level_allow = $create_level_allow = $autorizationApi->getPermission($level_id, 'siteevent_event', "review_create");


			if (empty($create_level_allow)) {
				$this->view->can_create = $can_create = 0;
			} else {
				$this->view->can_create = $can_create = 1;
			}

			//GET RATING TABLE
			$ratingTable = Engine_Api::_()->getDbTable('ratings', 'siteevent');

			//GET REVIEW ID
			if (!empty($viewer_id)) {
				$params = array();
				$params['resource_id'] = $siteevent->event_id;
				$params['resource_type'] = $siteevent->getType();
				$params['viewer_id'] = $viewer_id;
				$params['type'] = 'user';
				$review_id = $this->view->hasPosted = $reviewTable->canPostReview($params);
			} else {
				$review_id = $this->view->hasPosted = 0;
			}

			if (empty($can_create)) {
				return $this->_forwardCustom('requireauth', 'error', 'core');
			}

			//CREATE FORM
			if ($this->view->can_create && !$review_id) {

				//FATCH REVIEW CATEGORIES
				$categoryIdsArray = array();
				$categoryIdsArray[] = $siteevent->category_id;
				$categoryIdsArray[] = $siteevent->subcategory_id;
				$categoryIdsArray[] = $siteevent->subsubcategory_id;
				$profileTypeReview = Engine_Api::_()->getDbtable('categories', 'siteevent')->getProfileType($categoryIdsArray, 0, 'profile_type_review');

				$this->view->form = $form = new Siteevent_Form_Review_SitemobileCreate(array('item' => $siteevent, 'profileTypeReview' => $profileTypeReview));
          if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
          Zend_Registry::set('setFixedCreationForm', true);
          Zend_Registry::set('setFixedCreationFormBack', 'Back');
          Zend_Registry::set('setFixedCreationHeaderTitle', Zend_Registry::get('Zend_Translate')->_('Write a Review'));
          Zend_Registry::set('setFixedCreationHeaderSubmit', Zend_Registry::get('Zend_Translate')->_('Submit'));
          $this->view->form->setAttrib('id', 'siteevent_create');
          Zend_Registry::set('setFixedCreationFormId', '#siteevent_create');
          $this->view->form->removeElement('submit');
          $form->setTitle(sprintf(Zend_Registry::get('Zend_Translate')->_('For %s'), $siteevent->getTitle()));
          $form->setDescription('');
        }
			}

			//START TOP SECTION FOR OVERALL RATING AND IT'S PARAMETER
			$params = array();
			$params['resource_id'] = $event_id;
			$params['resource_type'] = $siteevent->getType();
			$params['type'] = 'user';
			$noReviewCheck = $reviewTable->getAvgRecommendation($params);
			if (!empty($noReviewCheck)) {
          				$this->view->noReviewCheck = $noReviewCheck->toArray();
				if($this->view->noReviewCheck)
				$this->view->recommend_percentage = round($noReviewCheck[0]['avg_recommend'] * 100, 3);
			}
			$this->view->ratingDataTopbox = $ratingTable->ratingbyCategory($event_id, 'user', $siteevent->getType());

			$this->view->isajax = $this->_getParam('isajax', 0);

			//FATCH REVIEW CATEGORIES
			$categoryIdsArray = array();
			$categoryIdsArray[] = $siteevent->category_id;
			$categoryIdsArray[] = $siteevent->subcategory_id;
			$categoryIdsArray[] = $siteevent->subsubcategory_id;
			$this->view->reviewCategory = Engine_Api::_()->getDbtable('ratingparams', 'siteevent')->reviewParams($categoryIdsArray, $siteevent->getType());

			//COUNT REVIEW CATEGORY
			$this->view->total_reviewcats = Count($this->view->reviewCategory);

			//GET REVIEW RATE DATA
			$this->view->reviewRateMyData = $this->view->reviewRateData = $ratingTable->ratingsData($review_id);

			//CUSTOM FIELDS
			$this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Siteevent/View/Helper', 'Siteevent_View_Helper');

			$postData = $this->getRequest()->getPost();

			if ($this->getRequest()->isPost() && $postData) {

					$db = Engine_Db_Table::getDefaultAdapter();
					$db->beginTransaction();

					try {
						
						$otherValues = $_POST;          
						
						$values = array_merge($postData, $otherValues);

						$values['owner_id'] = $viewer_id;
						$values['resource_id'] = $siteevent->event_id;
						$values['resource_type'] = $siteevent->getType();
						$values['profile_type_review'] = $profileTypeReview;
						$values['type'] = $viewer_id ? 'user' : 'visitor';
						if(Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.recommend', 1)) {
							$values['recommend'] = 1;
						} else {
							$values['recommend'] = 0;
						}
						$reviewTable = Engine_Api::_()->getDbtable('reviews', 'siteevent');
						$review = $reviewTable->createRow();
						$review->setFromArray($values);
						$review->view_count = 1;
						$review->save();

						if (!empty($profileTypeReview)) {
							//SAVE CUSTOM VALUES AND PROFILE TYPE VALUE
							$form = new Siteevent_Form_Review_SitemobileCreate(array('item' => $siteevent, 'profileTypeReview' => $profileTypeReview));
							$customfieldform = $form->getSubForm('fields');
							$customfieldform->setItem($review);
							$customfieldform->saveValues();
						}

						//INCREASE REVIEW COUNT IN Event TABLE
						if (!empty($viewer_id))
							$siteevent->review_count++;

						$siteevent->save();

						$reviewRatingTable = Engine_Api::_()->getDbtable('ratings', 'siteevent');
						if (!empty($review_id)) {
							$reviewRatingTable->delete(array('review_id = ?' => $review->review_id));
						}

						$postData['user_id'] = $viewer_id;
						$postData['review_id'] = $review->review_id;
						$postData['category_id'] = $siteevent->category_id;
						$postData['resource_id'] = $review->resource_id;
						$postData['resource_type'] = $review->resource_type;
					

							//CREATE RATING DATA
							$reviewRatingTable->createRatingData($postData, $values['type']);
              
						//UPDATE RATING IN RATING TABLE
						if (!empty($viewer_id)) {
							$reviewRatingTable->listRatingUpdate($review->resource_id, $review->resource_type);
						}

						if (empty($review_id) && !empty($viewer_id)) {
							$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');

							//ACTIVITY FEED
							$action = $activityApi->addActivity($viewer, $siteevent, 'siteevent_review_add');

							if ($action != null) {
								$activityApi->attachActivity($action, $review);
							}
						}

						if (empty($viewer_id)) {
							$review->status = 0;
							$review->save();
							$email = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;
							$admin_emails = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.contact', Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.from', 'email@domain.com'));
							$explodeEmails = explode(",", $admin_emails);
							foreach ($explodeEmails as $value) {
								Engine_Api::_()->getApi('mail', 'core')->sendSystem($value, 'SITEEVENT_REVIEW_WRITE', array(
										'event_Name' => $siteevent->title,
										'event_Name_With_link' => '<a href="' . 'http://' . $_SERVER['HTTP_HOST'] .
										Zend_Controller_Front::getInstance()->getRouter()->assemble(array('event_id' => $siteevent->event_id), "siteevent_entry_view", true) . '"  >' . $siteevent->title . '</a>',
										'user_name' => $review->anonymous_name,
										'review_title' => $review->title,
										'review_description' => $review->body,
										'review_link' => '<a href="' . 'http://' . $_SERVER['HTTP_HOST'] .
										Zend_Controller_Front::getInstance()->getRouter()->assemble(array('review_id' => $review->review_id, 'event_id' => $review->resource_id), "siteevent_view_review", true) . '"  >' . 'http://' . $_SERVER['HTTP_HOST'] .
										Zend_Controller_Front::getInstance()->getRouter()->assemble(array('review_id' => $review->review_id, 'event_id' => $review->resource_id), "siteevent_view_review", true) . '</a>',
										'email' => $email,
										'queue' => false
								));
							}
						}

						if ($siteevent->owner_id != $viewer_id && !empty($review->owner_id)) {
							$object_parent_with_link = '<a href="' . 'http://' . $_SERVER['HTTP_HOST'] . '/' . $siteevent->getHref() . '">' . $siteevent->getTitle() . '</a>';
							$subjectOwner = $siteevent->getOwner('user');
							$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
							$notifyApi->addNotification($subjectOwner, $viewer, $review, 'siteevent_write_review', array("object_parent_with_link" => $object_parent_with_link));
						}

						$db->commit();
					} catch (Exception $e) {
						$db->rollBack();
						throw $e;
					}

         return $this->_redirectCustom($siteevent->getHref(array('tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab'))), array('prependBase' => false));
			}

        }
    }

    //ACTION FOR UPDATE THE REVIEW
    public function updateAction() {


        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            //REVIEW SUBJECT SHOULD BE SET
            if (!$this->_helper->requireSubject('siteevent_review')->isValid())
                return;

            //GET VIEWER INFO
            $viewer = Engine_Api::_()->user()->getViewer();
            $viewer_id = $viewer->getIdentity();

            $siteevent = Engine_Api::_()->core()->getSubject()->getParent();


            //FATCH REVIEW CATEGORIES
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
                return $this->_forwardCustom('requireauth', 'error', 'core');
            }

            $postData = $this->getRequest()->getPost();
            if ($this->getRequest()->isPost() && $postData) {

                $review_id = (int) $this->_getParam('review_id');
                $review = Engine_Api::_()->core()->getSubject();

                $form = new Siteevent_Form_Review_Update(array('item' => $siteevent));
                $form->populate($postData);
                $otherValues = $form->getValues();
                $postData = array_merge($postData, $otherValues);

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
                echo Zend_Json::encode(array('captchaError' => 0, 'review_href' => $review->getHref()));

                if (!empty($profileTypeReview)) {
                    //SAVE CUSTOM VALUES AND PROFILE TYPE VALUE
                    $form = new Siteevent_Form_Review_Create(array('item' => $siteevent, 'profileTypeReview' => $profileTypeReview));
                    $form->populate($postData);
                    $customfieldform = $form->getSubForm('fields');
                    $customfieldform->setItem($review);
                    $customfieldform->saveValues();
                }

                exit();
            }
        } else {
            //REVIEW SUBJECT SHOULD BE SET
            if (!$this->_helper->requireSubject('siteevent_review')->isValid())
                return;

            //GET VIEWER INFO
            $viewer = Engine_Api::_()->user()->getViewer();
            $viewer_id = $viewer->getIdentity();

            $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject()->getParent();

            $this->view->tab = $this->_getParam('tab');
            //FATCH REVIEW CATEGORIES
            $categoryIdsArray = array();
            $categoryIdsArray[] = $siteevent->category_id;
            $categoryIdsArray[] = $siteevent->subcategory_id;
            $categoryIdsArray[] = $siteevent->subsubcategory_id;
            $profileTypeReview = Engine_Api::_()->getDbtable('categories', 'siteevent')->getProfileType($categoryIdsArray, 0, 'profile_type_review');

            $this->view->reviewCategory = Engine_Api::_()->getDbtable('ratingparams', 'siteevent')->reviewParams($categoryIdsArray, $siteevent->getType());

            //COUNT REVIEW CATEGORY
            $this->view->total_reviewcats = Count($this->view->reviewCategory);

            //GET USER LEVEL ID
            if (!empty($viewer_id)) {
                $level_id = $viewer->level_id;
            } else {
                $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
            }

            $this->view->can_update = $can_update = Engine_Api::_()->authorization()->getPermission($level_id, 'siteevent_event', "review_update");

            if (empty($can_update)) {
                return $this->_forwardCustom('requireauth', 'error', 'core');
            }
            $review_id = (int) $this->_getParam('review_id');

            $review = Engine_Api::_()->core()->getSubject();
            $this->view->reviewRateMyData = Engine_Api::_()->getDbtable('ratings', 'siteevent')->ratingsData($review_id);


            $this->view->form = $form = new Siteevent_Form_Review_Update(array('item' => $siteevent));

            if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
              Zend_Registry::set('setFixedCreationForm', true);
              Zend_Registry::set('setFixedCreationFormBack', 'Back');
              Zend_Registry::set('setFixedCreationHeaderTitle', Zend_Registry::get('Zend_Translate')->_('Update your Review'));
              Zend_Registry::set('setFixedCreationHeaderSubmit', Zend_Registry::get('Zend_Translate')->_('Submit'));
              $this->view->form->setAttrib('id', 'siteevent_update');
              Zend_Registry::set('setFixedCreationFormId', '#siteevent_update');
              $this->view->form->removeElement('submit');
              $form->setTitle(sprintf(Zend_Registry::get('Zend_Translate')->_('For %s'), $siteevent->getTitle()));
              $form->setDescription('');
           }
            if ($this->getRequest()->isPost() && $this->getRequest()->getPost()) {
                $postData = $this->getRequest()->getPost();
                $otherValues = $form->getValues();
                $postData = array_merge($postData, $otherValues);
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

                if (!empty($profileTypeReview)) {
                    //SAVE CUSTOM VALUES AND PROFILE TYPE VALUE
                    $form = new Siteevent_Form_Review_Create(array('item' => $siteevent, 'profileTypeReview' => $profileTypeReview));
                    $form->populate($postData);
                    $customfieldform = $form->getSubForm('fields');
                    $customfieldform->setItem($review);
                    $customfieldform->saveValues();
                }

                return $this->_redirectCustom($siteevent->getHref(array('tab' => $this->view->tab)), array('prependBase' => false));
            }
        }
    }

    public function rateAction() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $rating = $this->_getParam('rating');
        $event_id = $this->_getParam('event_id');

        $postData = array();

        //GET EVENT SUBJECT
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);


        $reviewRatingTable = Engine_Api::_()->getDbtable('ratings', 'siteevent');
        $review = $reviewRatingTable->getReviewId($viewer_id, $siteevent->getType(), $siteevent->event_id);

        if (count($review) == 0) {
            //CREATE RATING DATA
            $postData['user_id'] = $viewer_id;
            $postData['review_id'] = 0;
            $postData['category_id'] = $siteevent->category_id;
            $postData['resource_id'] = $siteevent->event_id;
            $postData['resource_type'] = $siteevent->getType();
            $postData['review_rate_0'] = $rating;
            $values['type'] = $viewer_id ? 'user' : 'visitor';
            $reviewRatingTable->createRatingData($postData, $values['type']);
        } else {
            $reviewRatingTable->update(array('rating' => $rating), array('resource_type = ?' => $review->resource_type, 'user_id = ?' => $viewer_id, 'resource_id = ?' => $siteevent->event_id));
        }

        //UPDATE RATING IN RATING TABLE
        if (!empty($viewer_id) && (count($review) == 0)) {
            $rating_only = 1;
            $user_rating = $reviewRatingTable->listRatingUpdate($siteevent->event_id, $siteevent->getType(), $rating_only);
        } else {
            $rating_only = 1;
            $user_rating = $reviewRatingTable->listRatingUpdate($review->resource_id, $review->resource_type, $rating_only);
        }

        $totalUsers = $reviewRatingTable->select()
                        ->from($reviewRatingTable->info('name'), 'COUNT(*) AS count')
                        ->where('user_id != ?', 0)
                        ->where('type = ?', 'user')
                        ->query()->fetchColumn();

        $data = array();
        $data[] = array(
            'rating' => $rating,
            'rating_users' => $user_rating,
            'users' => $totalUsers,
        );
        return $this->_helper->json($data);
        $data = Zend_Json::encode($data);
        $this->getResponse()->setBody($data);
    }

    //ACTION FOR MARKING HELPFUL REVIEWS
    public function helpfulAction() {

        //NOT VALID USER THEN RETURN
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER DETAIL
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        //GET RATING
        $helpful = $this->_getParam('helpful');

        //GET REVIEW ID
        $review_id = $this->_getParam('review_id');
        $review = Engine_Api::_()->core()->getSubject();
        $siteevent = Engine_Api::_()->core()->getSubject()->getParent();

        $anonymous = $this->_getParam('anonymous', 0);
        if (!empty($anonymous)) {
            return $this->_helper->redirector->gotoRoute(array('review_id' => $review_id, 'event_id' => $review->resource_id), "siteevent_view_review", true);
        }

        //GET HELPFUL TABLE
        $helpfulTable = Engine_Api::_()->getDbtable('helpful', 'siteevent');

        $this->view->already_entry = $helpfulTable->getHelpful($review_id, $viewer_id, $helpful);

        if (empty($this->view->already_entry)) {
            $this->view->already_entry = 0;
        }

        //MAKE ENTRY FOR HELPFUL
        $helpfulTable->setHelful($review_id, $viewer_id, $helpful);

        echo Zend_Json::encode(array('already_entry' => $this->view->already_entry));
        exit();
    }

    //ACTION FOR VIEW REVIEWS
    public function viewAction() {

        //IF ANONYMOUS USER THEN SEND HIM TO SIGN IN PAGE
        $check_anonymous_help = $this->_getParam('anonymous');
        if ($check_anonymous_help) {
            if (!$this->_helper->requireUser()->isValid())
                return;
        }

        //GET LOGGED IN USER INFORMATION
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        //GET EVENT ID AND OBJECT
        $siteevent = Engine_Api::_()->core()->getSubject()->getParent();



        //WHO CAN VIEW THE EVENTS
        if (!$this->_helper->requireAuth()->setAuthParams($siteevent, null, "view")->isValid() || !Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.allowreview', 1)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        $review = Engine_Api::_()->core()->getSubject();
        if (empty($review)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
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
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        if ($can_view != 2 && ($review->status != 1 && empty($review->owner_id))) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        $params = array();
        $params['event_type_title'] = 'Events';

        //GET LOCATION
        if (!empty($siteevent->location) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1)) {
            $params['location'] = $siteevent->location;
        }

        $params['tag'] = $siteevent->getKeywords(', ');

        //GET EVENT CATEGORY TABLE
        $tableCategory = Engine_Api::_()->getDbTable('categories', 'siteevent');

        $category_id = $siteevent->category_id;
        if (!empty($category_id)) {

            $params['categoryname'] = Engine_Api::_()->getItem('siteevent_category', $category_id)->getCategorySlug();

            $subcategory_id = $siteevent->subcategory_id;

            if (!empty($subcategory_id)) {

                $params['subcategoryname'] = Engine_Api::_()->getItem('siteevent_category', $subcategory_id)->getCategorySlug();

                $subsubcategory_id = $siteevent->subsubcategory_id;

                if (!empty($subsubcategory_id)) {

                    $params['subsubcategoryname'] = Engine_Api::_()->getItem('siteevent_category', $subsubcategory_id)->getCategorySlug();
                }
            }
        }

        if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
          Zend_Registry::set('setFixedCreationFormBack', 'Back');
        }
        //SET META KEYWORDS
        Engine_Api::_()->siteevent()->setMetaKeywords($params);

        //GET PAGE OBJECT
        $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
        $pageSelect = $pageTable->select()->where('name = ?', "siteevent_review_view");
        $pageObject = $pageTable->fetchRow($pageSelect);

        $this->_helper->content
                ->setContentName('siteevent_review_view')
                ->setNoRender()
                ->setEnabled();
    }

    //ACTION FOR DELETING REVIEW
    public function deleteAction() {

        //ONLY LOGGED IN USER CAN DELETE REVIEW
        if (!$this->_helper->requireUser()->isValid())
            return;

        //SUBJECT SHOULD BE SET
        if (!$this->_helper->requireSubject('siteevent_review')->isValid())
            return;

        //GET VIEWER ID
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->review = $review = Engine_Api::_()->core()->getSubject();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        $this->view->siteevent = $siteevent = $review->getParent();

        //GET REVIEW ID AND REVIEW OBJECT
        $review_id = $this->_getParam('review_id');


        //AUTHORIZATION CHECK
        $can_delete = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'siteevent_event', "review_delete");

        //WHO CAN DELETE THE REVIEW
        if (empty($can_delete) || ($can_delete == 1 && $viewer_id != $review->owner_id)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                //DELETE REVIEW FROM DATABASE
                Engine_Api::_()->getItem('siteevent_review', (int) $review_id)->delete();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            //REDIRECT
            $url = $this->_helper->url->url(array('event_id' => $siteevent->getIdentity(), 'slug' => $siteevent->getSlug(), 'tab' => $this->_getParam('tab')), "siteevent_entry_view", true);
            
            $this->_forwardCustom('success', 'utility', 'core', array(
                'parentRedirect' => $url,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your review has been deleted successfully.'))
            ));
        } else {
            $this->renderScript('review/delete.tpl');
        }
    }

    //VALIDATES CAPTCHA RESPONSE
    function validateCaptcha($captcha) {

        $captchaId = $captcha['id'];
        $captchaInput = $captcha['input'];
        $session = new Zend_Session_Namespace();
        if (!isset($session->setword)) {
            $captchaSession = new Zend_Session_Namespace('Zend_Form_Captcha_' . $captchaId);
            $captchaIterator = $captchaSession->getIterator();
            if (isset($captchaIterator['word']))
                $captchaWord = $captchaIterator['word'];
            $session->setword = $captchaWord;
        }
        else {
            $captchaWord = $session->setword;
        }

        if ($captchaWord) {
            if ($captchaInput != $captchaWord) {
                return 0;
            } else {
                return 1;
            }
        } else {
            return 0;
        }
    }

    //ACTION FOR EMAIL THE REVIEW
    public function emailAction() {

        if (!$this->_helper->requireUser()->isValid())
            return;

        //SUBJECT SHOULD BE SET
        if (!$this->_helper->requireSubject('siteevent_review')->isValid())
            return;

        //SET LAYOUT
        $this->_helper->layout->setLayout('default-simple');

        $review = Engine_Api::_()->core()->getSubject();
        $siteevent = $review->getParent();


        //GET FORM
        $this->view->form = $form = new Siteevent_Form_Review_Email();

        //NOT VALID FORM POST THEN RETURN
        if (!$this->getRequest()->isPost())
            return;

        //FORM VALIDATION
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            //GET VIEWER ID
            $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
            $postData = $this->getRequest()->getPost();
            $emailTo = $postData['emailTo'];
            $userComment = $postData['userComment'];

            //EDPLODES EMAIL IDS
            $reciver_ids = explode(',', $postData['emailTo']);

            //CHECK VALID EMAIL ID FORMITE
            $validator = new Zend_Validate_EmailAddress();
            $validator->getHostnameValidator()->setValidateTld(false);

            foreach ($reciver_ids as $reciver_id) {
                $reciver_id = trim($reciver_id, ' ');
                if (!$validator->isValid($reciver_id)) {
                    $form->addError(Zend_Registry::get('Zend_Translate')->_('Please enter correct email address of the receiver(s).'));
                    return;
                }
            }

            //SEND EMAIL
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($reciver_ids, 'SITEEVENT_EMAIL_FRIEND', array(
                'user_email' => Engine_Api::_()->getItem('user', $viewer_id)->email,
                'userComment' => $userComment,
                'site_title' => Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 1),
                'review_title' => $review->title,
                'review_title_with_link' => '<a href="' . 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('review_id' => $review->review_id, 'event_id' => $siteevent->event_id), "siteevent_view_review", true) . '">' . $review->title . '</a>',
                'email' => Engine_Api::_()->getApi('settings', 'core')->core_mail_from,
                'queue' => false
            ));

            $this->_forwardCustom('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                //'parentRefreshTime' => '15',
                //'format' => 'smoothbox',
                'messages' => Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.')
            ));
        }
    }

}