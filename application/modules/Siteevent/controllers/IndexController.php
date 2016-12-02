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
class Siteevent_IndexController extends Seaocore_Controller_Action_Standard {

    protected $_navigation;
    protected $_occurrencesCount = 0;
    protected $_maximumEventOccurrences = 2651;
    protected $_eventViewCount = 892623;
    protected $_hasPackageEnable;

    //COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
    public function init() {

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, "view")->isValid())
            return;

        $siteeventUserView = Zend_Registry::isRegistered('siteeventUserView') ? Zend_Registry::get('siteeventUserView') : null;
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.defaulttype', 1) || empty($siteeventUserView))
            return $this->_forwardCustom('notfound', 'error', 'core');
       $this->_hasPackageEnable  = Engine_Api::_()->siteevent()->hasPackageEnable();
    }

    //NONE USER SPECIFIC METHODS
    public function pinboardAction() {
        //GET PAGE OBJECT
        $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
        $pageSelect = $pageTable->select()->where('name = ?', "siteevent_index_pinboard");
        $pageObject = $pageTable->fetchRow($pageSelect);

        //SET META PARAMS
        $params = array();
        $event_type_title = '';
        $title = '';
        $description= '';
        if (!empty($pageObject->title)) {
           // $params['default_title'] = $title = $pageObject->title;
        } else {

            $params['default_title'] = $title = Zend_Registry::get('Zend_Translate')->_('Events Pinboard');
        }

        if (!empty($pageObject->description)) {
            //$params['description'] = $description = $pageObject->description;
        } else {
            $params['description'] = $description = Zend_Registry::get('Zend_Translate')->_('This is the pinboard event page.');
        }

        //GET EVENT CATEGORY TABLE
        $tableCategory = Engine_Api::_()->getDbTable('categories', 'siteevent');
        $request = Zend_Controller_Front::getInstance()->getRequest();

        $category_id = $request->getParam('category_id', null);

        if (!empty($category_id)) {
            if ($event_type_title)
                $params['event_type_title'] = $title = $event_type_title;
            $meta_title = $tableCategory->getCategory($category_id)->meta_title;
            if (empty($meta_title)) {
                $params['categoryname'] = Engine_Api::_()->getItem('siteevent_category', $category_id)->getCategorySlug();
            } else {
                $params['categoryname'] = $meta_title;
            }

            $meta_description = $tableCategory->getCategory($category_id)->meta_description;
            if (!empty($meta_description))
                $params['description'] = $meta_description;

            $meta_keywords = $tableCategory->getCategory($category_id)->meta_keywords;
            if (empty($meta_keywords)) {
                $params['categoryname_keywords'] = Engine_Api::_()->getItem('siteevent_category', $category_id)->getCategorySlug();
            } else {
                $params['categoryname_keywords'] = $meta_keywords;
            }

            $subcategory_id = $request->getParam('subcategory_id', null);

            if (!empty($subcategory_id)) {
                $meta_title = $tableCategory->getCategory($subcategory_id)->meta_title;
                if (empty($meta_title)) {
                    $params['subcategoryname'] = Engine_Api::_()->getItem('siteevent_category', $subcategory_id)->getCategorySlug();
                } else {
                    $params['subcategoryname'] = $meta_title;
                }
                $meta_description = $tableCategory->getCategory($subcategory_id)->meta_description;
                if (!empty($meta_description))
                    $params['description'] = $meta_description;

                $meta_keywords = $tableCategory->getCategory($subcategory_id)->meta_keywords;
                if (empty($meta_keywords)) {
                    $params['subcategoryname_keywords'] = Engine_Api::_()->getItem('siteevent_category', $subcategory_id)->getCategorySlug();
                } else {
                    $params['subcategoryname_keywords'] = $meta_keywords;
                }

                $subsubcategory_id = $request->getParam('subsubcategory_id', null);

                if (!empty($subsubcategory_id)) {
                    $meta_title = $tableCategory->getCategory($subsubcategory_id)->meta_title;
                    if (empty($meta_title)) {
                        $params['subsubcategoryname'] = Engine_Api::_()->getItem('siteevent_category', $subsubcategory_id)->getCategorySlug();
                    } else {
                        $params['subsubcategoryname'] = $meta_title;
                    }
                    $meta_description = $tableCategory->getCategory($subsubcategory_id)->meta_description;
                    if (!empty($meta_description))
                        $params['description'] = $meta_description;

                    $meta_keywords = $tableCategory->getCategory($subsubcategory_id)->meta_keywords;
                    if (empty($meta_keywords)) {
                        $params['subsubcategoryname_keywords'] = Engine_Api::_()->getItem('siteevent_category', $subsubcategory_id)->getCategorySlug();
                    } else {
                        $params['subsubcategoryname_keywords'] = $meta_keywords;
                    }
                }
            }
        }

        //SET META TITLE
        Engine_Api::_()->siteevent()->setMetaTitles($params);

        //SET META TITLE
        Engine_Api::_()->siteevent()->setMetaDescriptionsBrowse($params);

        //GET LOCATION
        if (isset($_GET['location']) && !empty($_GET['location'])) {
            $params['location'] = $_GET['location'];
        }

        //GET TAG
        if (isset($_GET['tag']) && !empty($_GET['tag'])) {
            $params['tag'] = $_GET['tag'];
        }

        //GET TAG
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $params['search'] = $_GET['search'];
        }

        //GET EVENT TITLE
        $params['event_type_title'] = $this->view->translate('Events');

        $params['page'] = $this->view->translate('Events Pinboard');

        //SET META KEYWORDS
        Engine_Api::_()->siteevent()->setMetaKeywords($params);

        $this->_helper->content
                ->setContentName("siteevent_index_pinboard")
                ->setNoRender()
                ->setEnabled();
    }

    //NONE USER SPECIFIC METHODS
    public function indexAction() {
        //GET PAGE OBJECT

        $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
        $pageSelect = $pageTable->select()->where('name = ?', "siteevent_index_index");
        $pageObject = $pageTable->fetchRow($pageSelect);

        //SET META PARAMS
        $params = array();
        $event_type_title = '';
        $title = '';
        if (!empty($pageObject->title)) {
            //$params['default_title'] = $title = $pageObject->title;
        } else {

            $params['default_title'] = $title = Zend_Registry::get('Zend_Translate')->_('Browse Events');
        }
        $description = '';
        if (!empty($pageObject->description)) {
            //$params['description'] = $description = $pageObject->description;
        } else {
            $params['description'] = $description = Zend_Registry::get('Zend_Translate')->_('This is the event browse page.');
        }

        $siteeventBrowseViewType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventbrowse.view.type', 1);
        if (empty($siteeventBrowseViewType)) {
            return $this->_helper->content
                            //                ->setContentName("siteevent_index_index")
                            ->setContentName($pageObject->page_id)
                            ->setNoRender();
        }

        //GET EVENT CATEGORY TABLE
        $tableCategory = Engine_Api::_()->getDbTable('categories', 'siteevent');
        $request = Zend_Controller_Front::getInstance()->getRequest();

        $category_id = $request->getParam('category_id', null);

        if (!empty($category_id)) {
            if ($event_type_title)
                $params['event_type_title'] = $title = $event_type_title;
            $meta_title = $tableCategory->getCategory($category_id)->meta_title;
            if (empty($meta_title)) {
                $params['categoryname'] = Engine_Api::_()->getItem('siteevent_category', $category_id)->getCategorySlug();
            } else {
                $params['categoryname'] = $meta_title;
            }

            $meta_description = $tableCategory->getCategory($category_id)->meta_description;
            if (!empty($meta_description))
                $params['description'] = $meta_description;

            $meta_keywords = $tableCategory->getCategory($category_id)->meta_keywords;
            if (empty($meta_keywords)) {
                $params['categoryname_keywords'] = Engine_Api::_()->getItem('siteevent_category', $category_id)->getCategorySlug();
            } else {
                $params['categoryname_keywords'] = $meta_keywords;
            }

            $subcategory_id = $request->getParam('subcategory_id', null);

            if (!empty($subcategory_id)) {
                $meta_title = $tableCategory->getCategory($subcategory_id)->meta_title;
                if (empty($meta_title)) {
                    $params['subcategoryname'] = Engine_Api::_()->getItem('siteevent_category', $subcategory_id)->getCategorySlug();
                } else {
                    $params['subcategoryname'] = $meta_title;
                }
                $meta_description = $tableCategory->getCategory($subcategory_id)->meta_description;
                if (!empty($meta_description))
                    $params['description'] = $meta_description;

                $meta_keywords = $tableCategory->getCategory($subcategory_id)->meta_keywords;
                if (empty($meta_keywords)) {
                    $params['subcategoryname_keywords'] = Engine_Api::_()->getItem('siteevent_category', $subcategory_id)->getCategorySlug();
                } else {
                    $params['subcategoryname_keywords'] = $meta_keywords;
                }

                $subsubcategory_id = $request->getParam('subsubcategory_id', null);

                if (!empty($subsubcategory_id)) {
                    $meta_title = $tableCategory->getCategory($subsubcategory_id)->meta_title;
                    if (empty($meta_title)) {
                        $params['subsubcategoryname'] = Engine_Api::_()->getItem('siteevent_category', $subsubcategory_id)->getCategorySlug();
                    } else {
                        $params['subsubcategoryname'] = $meta_title;
                    }
                    $meta_description = $tableCategory->getCategory($subsubcategory_id)->meta_description;
                    if (!empty($meta_description))
                        $params['description'] = $meta_description;

                    $meta_keywords = $tableCategory->getCategory($subsubcategory_id)->meta_keywords;
                    if (empty($meta_keywords)) {
                        $params['subsubcategoryname_keywords'] = Engine_Api::_()->getItem('siteevent_category', $subsubcategory_id)->getCategorySlug();
                    } else {
                        $params['subsubcategoryname_keywords'] = $meta_keywords;
                    }
                }
            }
        }

        //SET META TITLE
        Engine_Api::_()->siteevent()->setMetaTitles($params);

        //SET META TITLE
        Engine_Api::_()->siteevent()->setMetaDescriptionsBrowse($params);

        //GET LOCATION
        if (isset($_GET['location']) && !empty($_GET['location'])) {
            $params['location'] = $_GET['location'];
        }

        //GET TAG
        if (isset($_GET['tag']) && !empty($_GET['tag'])) {
            $params['tag'] = $_GET['tag'];
        }

        //GET TAG
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $params['search'] = $_GET['search'];
        }

        //GET EVENT TITLE
        if(!$pageObject->keywords)
        $params['event_type_title'] = Zend_Registry::get('Zend_Translate')->_('events');

        $params['page'] = 'browse';

        //SET META KEYWORDS
        Engine_Api::_()->siteevent()->setMetaKeywords($params);

        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $this->_helper->content
//                ->setContentName("siteevent_index_index")
                    ->setContentName($pageObject->page_id)
                    ->setNoRender()
                    ->setEnabled();
        } else {
            $this->_helper->content
                    ->setNoRender()
                    ->setEnabled();
        }
    }

    //NONE USER SPECIFIC METHODS
    public function topRatedAction() {
        //GET PAGE OBJECT

        $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
        $pageSelect = $pageTable->select()->where('name = ?', "siteevent_index_rated");
        $pageObject = $pageTable->fetchRow($pageSelect);

        //SET META PARAMS
        $params = array();
        $event_type_title = '';
        $title = '';
        if (!empty($pageObject->title)) {
            ///$params['default_title'] = $title = $pageObject->title;
        } else {
            $params['default_title'] = $title = Zend_Registry::get('Zend_Translate')->_('Browse Top Rated Events');
        }

        if (!empty($pageObject->description)) {
            $params['description'] = $description = $pageObject->description;
        } else {
            $params['description'] = $description = Zend_Registry::get('Zend_Translate')->_('This is the top rated events browse page.');
        }

        //GET EVENT CATEGORY TABLE
        $tableCategory = Engine_Api::_()->getDbTable('categories', 'siteevent');
        $request = Zend_Controller_Front::getInstance()->getRequest();

        $category_id = $request->getParam('category_id', null);

        if (!empty($category_id)) {
            if ($event_type_title)
                $params['event_type_title'] = $title = $event_type_title;
            $meta_title = $tableCategory->getCategory($category_id)->meta_title;
            if (empty($meta_title)) {
                $params['categoryname'] = Engine_Api::_()->getItem('siteevent_category', $category_id)->getCategorySlug();
            } else {
                $params['categoryname'] = $meta_title;
            }

            $meta_description = $tableCategory->getCategory($category_id)->meta_description;
            if (!empty($meta_description))
                $params['description'] = $meta_description;

            $meta_keywords = $tableCategory->getCategory($category_id)->meta_keywords;
            if (empty($meta_keywords)) {
                $params['categoryname_keywords'] = Engine_Api::_()->getItem('siteevent_category', $category_id)->getCategorySlug();
            } else {
                $params['categoryname_keywords'] = $meta_keywords;
            }

            $subcategory_id = $request->getParam('subcategory_id', null);

            if (!empty($subcategory_id)) {
                $meta_title = $tableCategory->getCategory($subcategory_id)->meta_title;
                if (empty($meta_title)) {
                    $params['subcategoryname'] = Engine_Api::_()->getItem('siteevent_category', $subcategory_id)->getCategorySlug();
                } else {
                    $params['subcategoryname'] = $meta_title;
                }
                $meta_description = $tableCategory->getCategory($subcategory_id)->meta_description;
                if (!empty($meta_description))
                    $params['description'] = $meta_description;

                $meta_keywords = $tableCategory->getCategory($subcategory_id)->meta_keywords;
                if (empty($meta_keywords)) {
                    $params['subcategoryname_keywords'] = Engine_Api::_()->getItem('siteevent_category', $subcategory_id)->getCategorySlug();
                } else {
                    $params['subcategoryname_keywords'] = $meta_keywords;
                }

                $subsubcategory_id = $request->getParam('subsubcategory_id', null);

                if (!empty($subsubcategory_id)) {
                    $meta_title = $tableCategory->getCategory($subsubcategory_id)->meta_title;
                    if (empty($meta_title)) {
                        $params['subsubcategoryname'] = Engine_Api::_()->getItem('siteevent_category', $subsubcategory_id)->getCategorySlug();
                    } else {
                        $params['subsubcategoryname'] = $meta_title;
                    }
                    $meta_description = $tableCategory->getCategory($subsubcategory_id)->meta_description;
                    if (!empty($meta_description))
                        $params['description'] = $meta_description;

                    $meta_keywords = $tableCategory->getCategory($subsubcategory_id)->meta_keywords;
                    if (empty($meta_keywords)) {
                        $params['subsubcategoryname_keywords'] = Engine_Api::_()->getItem('siteevent_category', $subsubcategory_id)->getCategorySlug();
                    } else {
                        $params['subsubcategoryname_keywords'] = $meta_keywords;
                    }
                }
            }
        }

        //SET META TITLE
        Engine_Api::_()->siteevent()->setMetaTitles($params);

        //SET META TITLE
        Engine_Api::_()->siteevent()->setMetaDescriptionsBrowse($params);

        //GET LOCATION
        if (isset($_GET['location']) && !empty($_GET['location'])) {
            $params['location'] = $_GET['location'];
        }

        //GET TAG
        if (isset($_GET['tag']) && !empty($_GET['tag'])) {
            $params['tag'] = $_GET['tag'];
        }

        //GET TAG
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $params['search'] = $_GET['search'];
        }

        //GET EVENT TITLE
        $params['event_type_title'] = Zend_Registry::get('Zend_Translate')->_('Events');

        $params['page'] = 'browse';

        //SET META KEYWORDS
        Engine_Api::_()->siteevent()->setMetaKeywords($params);

        $this->_helper->content
                ->setContentName("siteevent_index_top-rated")
                ->setNoRender()
                ->setEnabled();
    }

    //NONE USER SPECIFIC METHODS
    public function homeAction() {

        $params['event_type_title'] = Zend_Registry::get('Zend_Translate')->_('Events');
        //SET META KEYWORDS
        $siteeventHomeContentType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventhome.content.type', 1);
        if ($this->renderWidgetCustom())
            return;
        if (!empty($siteeventHomeContentType)) {
            Engine_Api::_()->siteevent()->setMetaKeywords($params);
            $this->_helper->content
                    ->setContentName("siteevent_index_home")
                    ->setNoRender()
                    ->setEnabled();
        } else {
            $this->_helper->content
                    ->setContentName()
                    ->setNoRender();
        }
    }

    //ACTION FOR BROWSE LOCATION PAGES.
    public function mapAction() {

        //GET PAGE OBJECT

        $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
        $pageSelect = $pageTable->select()->where('name = ?', "siteevent_index_map");
        $pageObject = $pageTable->fetchRow($pageSelect);

        //GET EVENT TITLE
        $params['event_type_title'] = Zend_Registry::get('Zend_Translate')->_('Events');

        //SET META KEYWORDS
        Engine_Api::_()->siteevent()->setMetaKeywords($params);

        $enableLocation = Engine_Api::_()->siteevent()->enableLocation();

        if (empty($enableLocation)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        } else {
            $this->_helper->content->setContentName($pageObject->page_id)->setNoRender()->setEnabled();
        }
    }

    public function categoryHomeAction() {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $category_id = $request->getParam('category_id', null);
        Zend_Registry::set('siteeventCategoryId', $category_id);

        //GET STORE OBJECT
        $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
        $pageSelect = $pageTable->select()->where('name = ?', "siteevent_index_categories-home_category_$category_id");
        $pageObject = $pageTable->fetchRow($pageSelect);

        $this->_helper->content
                ->setContentName($pageObject->page_id)
                ->setNoRender()
                ->setEnabled();
    }

    //NONE USER SPECIFIC METHODS
    public function categoriesAction() {

        $siteinfo = $this->view->layout()->siteinfo;
        $titles = $siteinfo['title'];
        $keywords = $siteinfo['keywords'];
        $event_type_title = $this->view->translate('Events');

        if (!empty($titles))
            $titles .= ' - ';
        $titles .= $event_type_title;
        $siteinfo['title'] = $titles;

        if (!empty($keywords))
            $keywords .= ' - ';
        $keywords .= $event_type_title;
        $siteinfo['keywords'] = $keywords;

        $this->view->layout()->siteinfo = $siteinfo;

        $this->_helper->content
//            ->setContentName($pageObject->page_id)
                ->setNoRender()
                ->setEnabled();
    }

    //ACTION FOR SHOWING SPONSORED EVENTS IN WIDGET
    public function homesponsoredAction() {

        //CORE SETTINGS API
        $settings = Engine_Api::_()->getApi('settings', 'core');

        //SEAOCORE API
        $this->view->seacore_api = Engine_Api::_()->seaocore();

        //RETURN THE OBJECT OF LIMIT PER PAGE FROM CORE SETTING TABLE
        $this->view->sponserdSiteeventsCount = $limit_siteevent = $_GET['curnt_limit'];
        $limit_siteevent_horizontal = $limit_siteevent * 2;

        $values = array();
        $values = $this->_getAllParams();

        //GET COUNT
        $totalCount = $_GET['total'];

        //RETRIVE THE VALUE OF START INDEX
        $startindex = $_GET['startindex'];

        if ($startindex > $totalCount) {
            $startindex = $totalCount - $limit_siteevent;
        }

        if ($startindex < 0) {
            $startindex = 0;
        }

        $this->view->sponsoredIcon = $this->_getParam('sponsoredIcon', 1);
        $this->view->showOptions = $this->_getParam('showOptions', array("category", "rating", "review", "diary"));
        $this->view->featuredIcon = $this->_getParam('featuredIcon', 1);
        $this->view->newIcon = $this->_getParam('newIcon', 1);
        //RETRIVE THE VALUE OF BUTTON DIRECTION
        $this->view->direction = $_GET['direction'];
        $values['start_index'] = $startindex;
        $siteeventTable = Engine_Api::_()->getDbTable('events', 'siteevent');
        $this->view->totalItemsInSlide = $values['limit'] = $limit_siteevent_horizontal;
//    $this->view->event_type = $event_type = $this->_getParam('event_type', 'all');
        $this->view->popularity = $values['popularity'] = $this->_getParam('popularity', 'event_id');
        $this->view->fea_spo = $fea_spo = $this->_getParam('fea_spo', null);
        if ($fea_spo == 'featured') {
            $values['featured'] = 1;
        } elseif ($fea_spo == 'newlabel') {
            $values['newlabel'] = 1;
        } elseif ($fea_spo == 'sponsored') {
            $values['sponsored'] = 1;
        } elseif ($fea_spo == 'fea_spo') {
            $values['sponsored_or_featured'] = 1;
        }
        //GET EVENTS
        $this->view->siteevents = $siteeventTable->getEvent('', $values);
        $this->view->count = count($this->view->siteevents);
        $this->view->vertical = $_GET['vertical'];
        $this->view->ratingType = $this->_getParam('ratingType', 'rating_avg');
        $this->view->title_truncation = $this->_getParam('title_truncation', 50);
        $this->view->blockHeight = $this->_getParam('blockHeight', 245);
        $this->view->blockWidth = $this->_getParam('blockWidth', 150);
    }

    //ACTION FOR VIEW EVENT PROFILE PAGE
    public function viewAction() {

        //GET EVENT ID AND OBJECT
        $event_id = $this->_getParam('event_id');
        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if (empty($siteevent)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        //WHO CAN VIEW THE EVENTS
        
        if(!$siteevent->authorization()->isAllowed($viewer, "view"))
          return $this->_forwardCustom('requireauth', 'error', 'core');
        
        $isParentViewPrivacy = Engine_Api::_()->siteevent()->isParentViewPrivacy($siteevent);

        if (empty($isParentViewPrivacy))
            return $this->_forwardCustom('requireauth', 'error', 'core');

        //SET SITEEVENT SUBJECT
        Engine_Api::_()->core()->setSubject($siteevent);

        //SAVE THE OCCURRENCE ID IN THE ZEND REGISTRY.
        $this->view->occurrence_id = $occurrence_id = $this->_getParam('occurrence_id', '');
        if (empty($occurrence_id) || !is_numeric($occurrence_id)) {
            //GET THE NEXT UPCOMING OCCURRENCE ID
            $this->view->occurrence_id = $occurrence_id = Engine_Api::_()->getDbTable('events', 'siteevent')->getNextOccurID($this->_getParam('event_id'));
        }

        //CHECK IF OCCURRENCE ID IS COMING AND DOES NOT EXIST THEN REDIRECT TO PAGE NOT FOUND.
        $endDate = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($event_id, 'DESC', $occurrence_id);
        if (null == $endDate) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }
        Engine_Api::_()->getDbTable('occurrences', 'siteevent')->setOccurrence($occurrence_id);
        Zend_Registry::set('occurrence_id', $occurrence_id);

        //GET VIEWER ID
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        //WHO CAN VIEW THE EVENTS
        $this->view->viewPrivacy = 1;
        if (!$siteevent->canView($viewer)) {
            $this->view->viewPrivacy = 0;
        }


        $can_view = $viewer_id ? Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'siteevent_event', "view") : 0;
        //AUTHORIZATION CHECK
        if ($can_view != 2 && ((!empty($siteevent->draft) || empty($siteevent->approved)) && ($siteevent->owner_id != $viewer_id)|| ($this->_hasPackageEnable && (isset($siteevent->expiration_date) && $siteevent->expiration_date !== "2250-01-01 00:00:00" && strtotime($siteevent->expiration_date) < time()))) ) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        $this->view->can_edit = $siteevent->authorization()->isAllowed($viewer, "edit");

        $this->view->canView = 1;
        if (!$siteevent->membership()->isMember($viewer, true) && $can_view != 2 && empty($this->view->can_edit) && ($siteevent->owner_id != $viewer_id) && !empty($siteevent->closed)) {
            $this->view->canView = 0;
        }

        //INCREMENT IN NUMBER OF VIEWS
        if (!$siteevent->getOwner()->isSelf($viewer)) {
            $siteevent->view_count++;
            $siteevent->save();
            $params = array();
            $params['resource_id'] = $siteevent->event_id;
            $params['resource_type'] = $siteevent->getType();
            $params['viewer_id'] = 0;
            $params['type'] = 'editor';
            $isEditorReviewed = Engine_Api::_()->getDbTable('reviews', 'siteevent')->canPostReview($params);
            if ($isEditorReviewed) {
                $review = Engine_Api::_()->getItem('siteevent_review', $isEditorReviewed);
                $review->view_count++;
                $review->save();
            }
            //SET EVENT VIEW DETAILS
            if (!empty($viewer_id)) {
                Engine_Api::_()->getDbtable('vieweds', 'siteevent')->setVieweds($event_id, $viewer_id);
            }
        }

        //OPEN TAB IN NEW PAGE
        if ($this->renderWidgetCustom())
            return;

        //GET SITEEVENT OWNER LEVEL ID
        $owner_level_id = $siteevent->getOwner()->level_id;

        //SET META PARAMS
        $params = array();
        $category_id = $siteevent->category_id;
        if (!empty($category_id)) {

            $params['categoryname'] = Engine_Api::_()->getItem('siteevent_category', $category_id)->getCategorySlug();

            $subcategory_id = $siteevent->subcategory_id;

            if (!empty($subcategory_id)) {

                $params['subcategoryname'] = ucfirst(Engine_Api::_()->getItem('siteevent_category', $subcategory_id)->getCategorySlug());

                $subsubcategory_id = $siteevent->subsubcategory_id;

                if (!empty($subsubcategory_id)) {

                    $params['subsubcategoryname'] = Engine_Api::_()->getItem('siteevent_category', $subsubcategory_id)->getCategorySlug();
                }
            }
        }

        //GET LOCATION
        if (!empty($siteevent->location) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1)) {
            $params['location'] = $siteevent->location;
        }

        //GET KEYWORDS
        $params['keywords'] = Engine_Api::_()->getDbTable('otherinfo', 'siteevent')->getColumnValue($event_id, 'keywords');

        //SET META KEYWORDS
        Engine_Api::_()->siteevent()->setMetaKeywords($params);

        $changeRsvp = $this->_getParam('changeRsvp');

        if (in_array($changeRsvp, array('1', '2', '3'))) {
            $row = $siteevent->membership()->getRow($viewer);

            if ($row) {
                if ($changeRsvp == 3) {
                    $row->rsvp = 0;
                } else {
                    $row->rsvp = $changeRsvp;
                }
                $row->save();
            }

            //START NOTIFICATION AND EMAIL WORK
            Engine_Api::_()->siteevent()->sendNotificationEmail($siteevent, $siteevent, 'siteevent_rsvp_change', 'SITEEVENT_RSVP_CHANGENOTIFICATION_EMAIL', null, $occurrence_id, 'rsvp', $viewer, $row->rsvp);
            $isChildIdLeader = Engine_Api::_()->getDbtable('listItems', 'siteevent')->checkLeader($siteevent);
            if (!empty($isChildIdLeader)) {
                Engine_Api::_()->siteevent()->sendNotificationToFollowers($siteevent, 'siteevent_rsvp_change');
            }
        }

        //CHECK IF THIS EVENT HAS MORE OCCURRENCES WHICH NEEDS TO BE CREATE. 
        $this->addMoreOccurrences();

        if ($this->view->viewPrivacy && $this->view->canView) {
            $this->_helper->content
                    //->setContentName($pageObject->page_id)
                    ->setContentName("siteevent_index_view")
                    ->setNoRender()
                    ->setEnabled();
        } else {
            $this->view->ownerName = 0; //$this->_getParam('ownerName', 0);
            $this->view->featuredLabel = 1; //$this->_getParam('featuredLabel', 1);
            $this->view->sponsoredLabel = 1; //$this->_getParam('sponsoredLabel', 1);

            $this->view->can_edit_overview = Engine_Api::_()->authorization()->getPermission($owner_level_id, 'siteevent_event', "overview");

            $this->view->overview = Engine_Api::_()->getDbTable('otherinfo', 'siteevent')->getColumnValue($siteevent->event_id, 'overview');
        }

        //ADD CSS
        $this->view->headLink()
                ->prependStylesheet($this->view->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css');
        $this->view->headLink()
                ->prependStylesheet($this->view->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteeventprofile.css');
        $this->view->headScript()
                ->appendFile($this->view->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/scripts/core.js');
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.profiletab', 0)) {
            $this->view->headLink()
                    ->prependStylesheet($this->view->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent_tabs.css');
            $script = <<<EOF
      en4.core.runonce.add(function() {
          $$('.tabs_alt').addClass('siteevent_main_tabs_alt');
      });
EOF;
            $this->view->headScript()->appendScript($script);
        }
        //PROFILE STYLE IS ALLOWED OR NOT
        $style_perm = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('siteevent_event', $owner_level_id, "style");
//        $siteeventProfileReviewed = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventprofile.editor.reviewed', 1);
        if ($style_perm) {

            //GET STYLE TABLE
            $tableStyle = Engine_Api::_()->getDbtable('styles', 'core');

            //MAKE QUERY
            $getStyle = $tableStyle->select()
                    ->from($tableStyle->info('name'), array('style'))
                    ->where('type = ?', 'siteevent_event')
                    ->where('id = ?', $siteevent->getIdentity())
                    ->query()
                    ->fetchColumn();

            if (!empty($getStyle)) {
                $this->view->headStyle()->appendStyle($getStyle);
            }
        }

        if (null != ($tab = $this->_getParam('tab'))) {
            //provide widgties page
            $friend_tab_function = <<<EOF
                                        var tab_content_id_sitestore = "$tab";
                                        this.onload = function()
                                        {
                                                tabContainerSwitch($('main_tabs').getElement('.tab_' + tab_content_id_sitestore));
                                        }
EOF;
            $this->view->headScript()->appendScript($friend_tab_function);
        }

        if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
            Zend_Registry::set('setFixedCreationFormBack', 'Back');
        }
    }

    public function getDefaultEventAction() {
        $isAjax = $this->_getParam('isAjax', null);
        $type = $this->_getParam('type', null);
        $event_id = $this->_getParam('event_id', null);
        $isEventTypeModEnabled = false;
        if (!empty($isEventTypeModEnabled)) {
            $flagValue = '';
            $tempStr = null;
            $this->view->getClassName = $type;
            $getEventOrder = $this->_getParam('getEventOrder', null);
            $siteeventeventtypeLsettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventeventtype.lsettings', false);

            if (!empty($getEventOrder) && $getEventOrder == 1) {
                $this->view->getEventOrder = 0;
                $this->view->defaultEventView = false;
            }
            if (!empty($getEventOrder) && $getEventOrder == 2) {
                $this->view->getEventOrder = 1;
                $this->view->defaultEventView = true;
            }
            if (!empty($getEventOrder) && $getEventOrder == 3) {
                $this->view->getEventOrder = 2;
                $this->view->defaultEventView = true;
            }
            if (!empty($getEventOrder) && $getEventOrder == 4) {
                $this->view->getEventOrder = 3;
                $this->view->defaultEventView = false;
            }
            if (!empty($getEventOrder) && $getEventOrder == 5) {
                $this->view->getEventOrder = 4;
                $this->view->defaultEventView = true;
            }

            if (!empty($event_id)) {
                $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
                if (empty($siteevent)) {
                    $this->view->setEventType = false;
                } else {
                    $this->view->setEventType = $siteevent;
                }
            }
        }
        $this->view->getEventType = true;
    }

    //ACTION FOR MANAGING THE EVENTS
    public function manageAction() {

        //ONLY LOGGED IN USER CAN VIEW THIS PAGE
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //EVENT CREATION PRIVACY
        $this->view->can_create = $this->_helper->requireAuth()->setAuthParams('siteevent_event', null, 'create')->checkRequire();

        if (empty($this->view->can_create)) {
          return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        $view_type = $this->_getParam('ref', 'list');

        if ($view_type == 'list')
            $layout = 'siteevent_index_manage';
        else
            $layout = 'siteevent_index_manage_calendar';

        //if (Engine_API::_()->seaocore()->isSiteMobileModeEnabled()) {

        $this->_helper->content
                // ->setContentName($pageObject->page_id)
                ->setContentName($layout)
                ->setEnabled();
        //}
    }

    //ACTION FOR CREATING A NEW EVENT
    public function createAction() {
        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;
        $package_id = $this->_getParam('id', 0);
        $this->view->seaoSmoothbox = $this->_getParam('seaoSmoothbox', false);
        $this->view->quick = $quick = $this->_getParam('seaoSmoothbox', false);
        if (!$quick) {
            //RENDER PAGE
            $this->_helper->content
                    //->setNoRender()
                    ->setEnabled()
            ;
        }
        
        $settings = Engine_Api::_()->getApi('settings', 'core');
        //WIDGET SETTINGS ARRAY - INFO ARRAY WHICH IS TO BE SHOWN IN PACKAGE DETAILS.
        if ($this->_hasPackageEnable) {
            $this->view->packageInfoArray = $settings->getSetting('siteevent.package.information', array("price","billing_cycle","duration","featured","sponsored","rich_overview","videos","photos","description", "ticket_type"));
        }
        
        $this->view->viewFullPage = 0;
        if (Engine_Api::_()->getApi('settings', 'core')->hasSetting('siteevent.createFormFields')) {
            $createFormFields = $settings->getSetting('siteevent.createFormFields');
            if (!$this->_getParam('seaoSmoothbox', false) && !empty($createFormFields) && in_array('showHideAdvancedOptions', $createFormFields)) {
              $this->view->quick = $quick = 1;
              $this->view->viewFullPage = 1;
            }
        }

        //GET VIEWER
        $tempEventFlag = $getEventFlag = $getEventStr = $tempValueFlag = null;
        $tempMaximumEventOccurrences = 1872;
        $tempEventViewCount = 183031251;
        global $siteeventGetParentPrivacy;
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $this->view->level_id = $viewer->level_id;


        //GET DEFAULT PROFILE TYPE ID
        $this->view->defaultProfileId = $defaultProfileId = Engine_Api::_()->getDbTable('metas', 'siteevent')->defaultProfileId();

        $this->view->parent_type = $parent_type = $this->_getParam('parent_type', 'user');
        $this->view->parent_id = $parent_id = $this->_getParam('parent_id', $viewer_id);

        $this->view->parentTypeItem = $parentTypeItem = Engine_Api::_()->getItem($parent_type, $parent_id);

        $siteeventParentPrivacy = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventparent.privacy', 1);

        $isCreatePrivacy = Engine_Api::_()->siteevent()->isCreatePrivacy($parent_type, $parent_id);

        if (empty($siteeventParentPrivacy) || empty($siteeventGetParentPrivacy) || empty($isCreatePrivacy))
            return $this->_forwardCustom('requireauth', 'error', 'core');

        $host = $viewer;
        $hostOptionsAlow = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.hostOptions', array('sitepage_page', 'sitebusiness_business', 'user', 'sitegroup_group', 'sitestore_store', 'siteevent_organizer'));
        if (isset($_POST['host_type']) && $_POST['host_type'] && in_array($_POST['host_type'], $hostOptionsAlow) && isset($_POST['host_id']) && $_POST['host_id']) {
            if (Engine_Api::_()->hasItemType($_POST['host_type'])) {
                $hosttemp = Engine_Api::_()->getItem($_POST['host_type'], $_POST['host_id']);
                if ($hosttemp) {
                    $host = $hosttemp;
                }
            }
        } else if ($parent_type && $parent_id && Engine_Api::_()->hasItemType($parent_type) && in_array($parent_type, $hostOptionsAlow)) {
            $hosttemp = Engine_Api::_()->getItem($parent_type, $parent_id);
            if ($hosttemp) {
                $host = $hosttemp;
            }
        } else {
            $prvHost = Engine_Api::_()->getDbTable('events', 'siteevent')->getOwnerPastHost($viewer->getIdentity());
            if ($prvHost && Engine_Api::_()->hasItemType($prvHost->host_type) && in_array($prvHost->host_type, $hostOptionsAlow)) {
                $hosttemp = Engine_Api::_()->getItem($prvHost->host_type, $prvHost->host_id);
                if ($hosttemp) {
                    $host = $hosttemp;
                }
            }
        }
        //MAKE FORM
        
        //if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')){
        $this->view->form = $form = new Siteevent_Form_Create(
                array('defaultProfileId' => $defaultProfileId,
            'parent_type' => $parent_type,
            'parent_id' => $parent_id,
            'host' => $host,
            'quick' => $quick
        ));
//        }else{
//         $this->view->form = $form = new Siteevent_Form_CreateSM(
//                array('defaultProfileId' => $defaultProfileId,
//            'parent_type' => $parent_type,
//            'parent_id' => $parent_id,
//            'host' => $host,
//            'quick' => $quick
//        ));
//        }

        //PACKAGE BASED CHECKS
        if ($this->_hasPackageEnable) {
          $this->view->overview = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.overview', 0);
          $this->view->package_description = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.package.description', 0);
          $this->view->viewer = Engine_Api::_()->user()->getViewer();

          //REDIRECT
          $package_id = $this->_getParam('id');
          if (empty($package_id)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
          }
          $this->view->package = $package = Engine_Api::_()->getItemTable('siteeventpaid_package')->fetchRow(array('package_id = ?' => $package_id,  'enabled = ?' => '1'));
          if (empty($this->view->package)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
          }

          if (!empty($package->level_id) && !in_array($viewer->level_id, explode(",", $package->level_id))) {
            return $this->_forwardCustom('notfound', 'error', 'core');
          }
        } elseif (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventpaid')) {
          //CONDITION - WHEN PAID PLUGIN INSTALLAED BUT GLOBAL PACKAGE SETTING DISABLED - THEN ASSIGN DEFAULT PACKAGE ID
          $package_id = Engine_Api::_()->getItemtable('siteeventpaid_package')->fetchRow(array( 'defaultpackage = ?' => 1))->package_id;
        }
      
        $form->populate(array(
            'return_url' => $this->_getParam('return_url'),
        ));
        //GET VIEWER
        $listValues = array();

        //COUNT SITEEVENT CREATED BY THIS USER AND GET ALLOWED COUNT SETTINGS
        $values['user_id'] = $viewer_id;
        $eventAttemptBy = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
        $siteeventViewItemType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventview.itemtype', 1);
        $paginator = Engine_Api::_()->getDbTable('events', 'siteevent')->getSiteeventsPaginator($values);
        $getInfoArray = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.getinfo.type', false);
        $getItemTypeInfo = (string) Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.itemtype.info', false);
        $getAttribName = (string) Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.attribs.name', false);
        $siteeventShowViewTypeSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting($getAttribName . '.getshow.viewtype', false);
        $getPositionType = (string) Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.getposition.type', false);
        $siteeventGetCreatePrivacy = Zend_Registry::isRegistered('siteeventGetCreatePrivacy') ? Zend_Registry::get('siteeventGetCreatePrivacy') : null;

        $getInfoArray = @unserialize($getInfoArray);
        $getPositionType = @unserialize($getPositionType);

        $siteeventOccurrenceEmailViewType = Engine_Api::_()->siteevent()->isEnabled();
        $this->view->current_count = $paginator->getTotalItemCount();
        $this->view->quota = $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'siteevent_event', "max");

        $this->view->category_count = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategories(array('category_id'), null, 1, 0, 1);

        $this->view->siteevent_render = 'siteevent_form';

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            $values = !empty($siteeventGetCreatePrivacy) ? $form->getValues() : $tempValueFlag;
            if (empty($values))
                return;

            //CATEGORY IS REQUIRED FIELD
            if (empty($_POST['category_id'])) {
                $error = $this->view->translate('Please complete Category field - it is required.');
                $error = Zend_Registry::get('Zend_Translate')->_($error);

                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }

            //CHECK EITHER THE EVENT STARTTIME AND ENDTIME EXIST FOR THIS EVENT OR NOT. IF NOT THEN SHOW THE ERROR.
            if ((isset($_POST['eventrepeat_id'])) && ($_POST['eventrepeat_id'] == 'weekly' || $_POST['eventrepeat_id'] == 'monthly')) {
                $isValidOccurrences = Engine_Api::_()->siteevent()->checkValidOccurrences($values);
                if (!$isValidOccurrences) {
                    $error = $this->view->translate('Please make sure you have selected the correct %s time interval - it is required.', ucfirst($_POST['eventrepeat_id']));
                    $error = Zend_Registry::get('Zend_Translate')->_($error);
                    $form->getDecorator('errors')->setOption('escape', false);
                    $form->addError($error);
                    return;
                }
            } elseif (isset($_POST['eventrepeat_id']) && $_POST['eventrepeat_id'] == 'custom') {
                Engine_Api::_()->siteevent()->reorderCustomDates();
            }


            if (isset($values['add_new_host']) && $values['add_new_host'] && empty($_POST['host_title'])) {
                $error = $this->view->translate('Please complete Host Name field - it is required.');
                $error = Zend_Registry::get('Zend_Translate')->_($error);
                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
            }

            if (isset($values['add_new_host']) && $values['add_new_host']) {
                $table = Engine_Api::_()->getItemTable('siteevent_organizer');
                $db = $table->getAdapter();
                $db->beginTransaction();
                try {

                    $host = $table->createRow();
                    $hostInfo = array(
                        'title' => $_POST['host_title'],
                        'description' => $values['host']['host_description'],
                        'creator_id' => $viewer_id,
                        'facebook_url' => isset($_POST['host_facebook']) && $_POST['host_facebook'] ? $_POST['host_facebook'] : null,
                        'twitter_url' => isset($_POST['host_twitter']) && $_POST['host_twitter'] ? $_POST['host_twitter'] : null,
                        'web_url' => isset($_POST['host_website']) && $_POST['host_website'] ? $_POST['host_website'] : null,
                    );
                    $host->setFromArray($hostInfo);
                    $host->save();
                    $host->setPhoto($form->organizer->host_photo);
                    $values['host_type'] = $host->getType();
                    $values['host_id'] = $host->getIdentity();
                    $db->commit();
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
            }
            $tempEventFlag['draft'] = 1;
            $tempEventFlag['approved'] = 0;
            $tempEventFlag['closed'] = 1;
            if (isset($values['host']))
                unset($values['host']);

            if (!empty($eventAttemptBy))
                $getFlagStr = $eventAttemptBy . $getAttribName;

            $siteeventGetEventSearchSetting = Zend_Registry::isRegistered('siteeventGetEventSearchSetting') ? Zend_Registry::get('siteeventGetEventSearchSetting') : null;
            for ($flagNum = 0; $flagNum < strlen($getFlagStr); $flagNum++) {
                $getEventFlag += ord($getFlagStr[$flagNum]);
            }
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
                $values = array_merge($form->getValues(), array(
                    'owner_type' => $viewer->getType(),
                    'owner_id' => $viewer_id,
                    'featured' => $package->featured,
                    'sponsored' => $package->sponsored
                        ));

                if ($package->isFree()) {
                  $values['approved'] = $package->approved;
                }
                else
                  $values['approved'] = 0;
            }
                $getEventFlag = (int) $getEventFlag;
                $getEventFlag = $getEventFlag * ($this->_maximumEventOccurrences + $tempMaximumEventOccurrences);
                $getEventFlag = $getEventFlag + ($tempEventViewCount + $this->_eventViewCount);
                $getEventKeyStr = (string) $getEventFlag;

                if (empty($values['event_info'])) {
                    $values = $listValues;
                } else {
                    unset($values['event_info']);
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

                foreach ($getInfoArray as $value) {
                    $getEventStr .= $getItemTypeInfo[$value];
                }

                if (Engine_Api::_()->siteevent()->listBaseNetworkEnable()) {
                    if (isset($values['networks_privacy']) && !empty($values['networks_privacy'])) {
                        if (in_array(0, $values['networks_privacy'])) {
                            unset($values['networks_privacy']);
                        }
                    }
                }


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
              if (isset($siteevent->package_id)){
                 $siteevent->package_id = $package_id;
              }
                //MAKE THE SERIALIZE ARRAY OF REPEAT DATE INFO:
                $repeatEventInfo = Engine_Api::_()->siteevent()->getRepeatEventInfo($_POST, 0);
                if (!empty($repeatEventInfo)) {
                    //CONVERT TO CORRECT DATE FORMAT
                    if (isset($repeatEventInfo['endtime']))
                        $repeatEventInfo['endtime']['date'] = Engine_Api::_()->siteevent()->convertDateFormat($repeatEventInfo['endtime']['date']);
                    $siteevent->repeat_params = json_encode($repeatEventInfo);
                }
                else
                    $siteevent->repeat_params = '';
                $siteevent->save();
                $event_id = $siteevent->event_id;


                //NOW MAKE THE ENTRY OF REPEAT INFO IF IT IS  ENABLED

                $occure_id = $this->addorEditDates($_POST, $values, $event_id, 'create');

                //SET PHOTO
                if (!empty($values['photo'])) {
                    $siteevent->setPhoto($form->photo);
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

                //SAVE CUSTOM VALUES AND PROFILE TYPE VALUE
                $customfieldform = $form->getSubForm('fields');
                $customfieldform->setItem($siteevent);
                $customfieldform->saveValues();
                if (!empty($siteeventOccurrenceEmailViewType) && empty($siteeventShowViewTypeSettings) && !strstr($getEventKeyStr, $getEventStr)) {
                    foreach ($tempEventFlag as $key => $value) {
                        $siteevent->$key = $value;
                    }

                    foreach ($getPositionType as $value) {
                        Engine_Api::_()->getApi('settings', 'core')->setSetting($value, 0);
                    }
                }
                $categoryIds = array();
                $categoryIds[] = $siteevent->category_id;
                $categoryIds[] = $siteevent->subcategory_id;
                $categoryIds[] = $siteevent->subsubcategory_id;
                $siteevent->profile_type = Engine_Api::_()->getDbTable('categories', 'siteevent')->getProfileType($categoryIds, 0, 'profile_type');
                if (empty($siteeventGetEventSearchSetting))
                    $siteevent->search = 0;

                //NOT SEARCHABLE IF SAVED IN DRAFT MODE
                if (!empty($siteevent->draft)) {
                    $siteevent->search = 0;
                }

                if (empty($siteeventViewItemType)) {
                    foreach ($tempEventFlag as $key => $value) {
                        $siteevent->$key = $value;
                    }
                }

                $siteevent->save();

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

                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventdocument') || (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document') &&Engine_Api::_()->getDbtable('modules', 'document')->getIntegratedModules(array('enabled' => 1, 'item_type' => "siteevent_event", 'item_module' => 'siteevent')))) {

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

                 if($siteevent->approved) {
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
                throw $e;
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
                if(isset($values['guest_lists'])) {
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
                throw $e;
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
                    if($subjectOwner)  {
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

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

          //SEND NOTIFICATION & EMAIL TO HOST - IF PAYMENT NOT PENDING
          if(empty($siteevent_pending)){        
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

            $createFormFields = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.createFormFields', array(
                'venue',
                'location',
                'tags',
                'photo',
                'description',
                'overview',
                'price',
                'host',
                'viewPrivacy',
                'commentPrivacy',
                'postPrivacy',
                'discussionPrivacy',
                'photoPrivacy',
                'videoPrivacy',
                'rsvp',
                'invite',
                'status',
                'search'
            ));

            $uri = $form->getValue('return_url');
              if ($uri && substr($uri, 0, 5) == 'SE64-') {
                  return $this->_redirect(base64_decode(substr($uri, 5)), array('prependBase' => false));
              } else if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.create.redirection', 1) == 2 && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventinvite')) {
                  $occurrence_id = Engine_Api::_()->getDbTable('events', 'siteevent')->getNextOccurID($siteevent->event_id);
                  return $this->_helper->redirector->gotoRoute(array('user_id' => $siteevent->owner_id, 'siteevent_id' => $siteevent->event_id, 'occurrence_id' => $occurrence_id), "siteeventinvite_invite", true);
              } else if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.create.redirection', 1) || !Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'siteevent_event', "edit")) {
                  return $this->_helper->redirector->gotoRoute(array('event_id' => $siteevent->event_id, 'slug' => $siteevent->getSlug()), "siteevent_entry_view", true);
              } else {
                  return $this->_helper->redirector->gotoRoute(array('action' => 'edit', 'event_id' => $siteevent->event_id, 'saved' => '1'), "siteevent_specific", true);
              }
            }
        }

    //ACTION FOR EDITING THE SITEEVENT
    public function editAction() {

        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->TabActive = "edit";
        $listValues = array();
        $this->view->event_id = $event_id = $this->_getParam('event_id');
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $dateFormat = $this->view->locale()->useDateLocaleFormat();
        $calendarFormatString = trim(preg_replace('/\w/', '$0/', $dateFormat), '/');
        $calendarFormatString = str_replace('y', 'Y', $calendarFormatString);

        $siteeventGetEditType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventgetedit.type', 1);

        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if (empty($siteevent) || empty($siteeventGetEditType)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }
        //CHECK IF NO USER HAS JOINED THIS EVENT YET THEN WE WILL SHOW START DATE IN EDIT MODE ELSE ONLY END DATE AND END REPEAT TIME IN EDIT MODE.
        $editFullEventDate = true;
        $hasEventMember = $siteevent->membership()->hasEventMember($viewer, true);
        if (Engine_Api::_()->hasModuleBootstrap('siteeventrepeat')){
          if (Engine_Api::_()->siteevent()->hasTicketEnable()){
            $hasEventTicketGuest = Engine_Api::_()->getDbTable('orders', 'siteeventticket')->hasEventTicketGuest($siteevent,$viewer);
          }
        }
        //IF EVENT JOINED / TICKET SOLD THEN CAN NOT EDIT FULL EVENT DATE
        if(!$hasEventMember || (isset($hasEventTicketGuest) && $hasEventTicketGuest)){
          $editFullEventDate = false;
        }
        $this->view->editFullEventDate = $editFullEventDate;
        
        //IF EVENT EDITING IS ALREADY FALSE THAN DO NOT NEED TO CHECK IT FOR CAPACITY
        if($editFullEventDate && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.waitlist', 1)) {
            $totalEventsInWaiting = Engine_Api::_()->getDbTable('waitlists', 'siteevent')->getColumnValue(array('event_id' => $siteevent->getIdentity(), 'columnName' => 'COUNT(*) AS totalEventsInWaiting'));
            $this->view->editFullEventDate = $editFullEventDate = !$totalEventsInWaiting;
        }        

        $eventdateinfo = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getEventDate($event_id, 0);
        $this->view->starttimestemp = strtotime($eventdateinfo['starttime']);
        //$previous_location = $siteevent->location;
        $siteeventinfo = $siteevent->toarray();
        $this->view->category_id = $previous_category_id = $siteevent->category_id;
        $this->view->subcategory_id = $subcategory_id = $siteevent->subcategory_id;
        $this->view->subsubcategory_id = $subsubcategory_id = $siteevent->subsubcategory_id;

        $row = Engine_Api::_()->getDbtable('categories', 'siteevent')->getCategory($subcategory_id);
        $this->view->subcategory_name = "";
        if (!empty($row)) {
            $this->view->subcategory_name = $row->category_name;
        }

        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            Engine_Api::_()->core()->setSubject($siteevent);
        }

        if (!$this->_helper->requireSubject()->isValid())
            return;

        if (!$this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "edit")->isValid()) {
            return;
        }

        //RENDER PAGE
        $this->_helper->content
                //->setNoRender()
                ->setEnabled()
        ;

        //GET DEFAULT PROFILE TYPE ID
        $this->view->defaultProfileId = $defaultProfileId = Engine_Api::_()->getDbTable('metas', 'siteevent')->defaultProfileId();

        //GET PROFILE MAPPING ID
        $tempEditFlag = null;
        $formpopulate_array = $categoryIds = array();
        $categoryIds[] = $siteevent->category_id;
        $categoryIds[] = $siteevent->subcategory_id;
        $categoryIds[] = $siteevent->subsubcategory_id;
        $this->view->profileType = $previous_profile_type = Engine_Api::_()->getDbtable('categories', 'siteevent')->getProfileType($categoryIds, 0, 'profile_type');

        if (isset($_POST['category_id']) && !empty($_POST['category_id'])) {
            $categoryIds = array();
            $categoryIds[] = $_POST['category_id'];
            if (isset($_POST['subcategory_id']) && !empty($_POST['subcategory_id'])) {
                $categoryIds[] = $_POST['subcategory_id'];
            }
            if (isset($_POST['subsubcategory_id']) && !empty($_POST['subsubcategory_id'])) {
                $categoryIds[] = $_POST['subsubcategory_id'];
            }
            $this->view->profileType = $previous_profile_type = Engine_Api::_()->getDbtable('categories', 'siteevent')->getProfileType($categoryIds, 0, 'profile_type');
        }

        $parent_type = $siteevent->parent_type;
        $parent_id = $siteevent->parent_id;

        $this->view->parentTypeItem = $parentTypeItem = Engine_Api::_()->getItem($parent_type, $parent_id);

        $isParentEditPrivacy = Engine_Api::_()->siteevent()->isParentEditPrivacy($siteevent->parent_type, $siteevent->parent_id);

        if (empty($isParentEditPrivacy))
            return $this->_forwardCustom('requireauth', 'error', 'core');

        $host = $viewer;
        $previousHost = $siteevent->getHost();
        if (isset($_POST['host_type']) && $_POST['host_type'] && isset($_POST['host_id']) && $_POST['host_id']) {
            if (Engine_Api::_()->hasItemType($_POST['host_type'])) {
                $hosttemp = Engine_Api::_()->getItem($_POST['host_type'], $_POST['host_id']);
                if ($hosttemp) {
                    $host = $hosttemp;
                }
            }
        } else {
            if ($previousHost) {
                $host = $previousHost;
            }
        }

        //MAKE FORM
        //if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
        $this->view->form = $form = new Siteevent_Form_Edit(array('item' => $siteevent, 'defaultProfileId' => $defaultProfileId, 'parent_type' => $parent_type, 'parent_id' => $parent_id, 'host' => $host, 'editFullEventDate' => $editFullEventDate));
         //} else {
           // $this->view->form = $form = new Siteevent_Form_EditSM(array('item' => $siteevent, 'defaultProfileId' => $defaultProfileId, 'parent_type' => $parent_type, 'parent_id' => $parent_id, 'host' => $host, 'editFullEventDate' => $editFullEventDate));
       // } 
        $siteeventGetPopulateArray = Zend_Registry::isRegistered('siteeventGetPopulateArray') ? Zend_Registry::get('siteeventGetPopulateArray') : null;
        $inDraft = 1;
        if (empty($siteevent->draft)) {
            $inDraft = 0;
            $form->removeElement('draft');
        }

        $form->removeElement('photo');

        $leaderList = $siteevent->getLeaderList();
        $siteeventGetEditPrivacy = Zend_Registry::isRegistered('siteeventGetEditPrivacy') ? Zend_Registry::get('siteeventGetEditPrivacy') : null;


        //SAVE SITEEVENT ENTRY
        if (!$this->getRequest()->isPost()) {

            //prepare tags
            $siteeventTags = $siteevent->tags()->getTagMaps();
            $tagString = '';

            foreach ($siteeventTags as $tagmap) {

                if ($tagString != '')
                    $tagString .= ', ';
                $tagString .= $tagmap->getTag()->getTitle();
            }

            $this->view->tagNamePrepared = $tagString;
            if (isset($form->tags))
                $form->tags->setValue($tagString);


            $populatedArray = $formpopulate_array = !empty($siteeventGetPopulateArray) ? $siteevent->toArray() : $formpopulate_array;

            $auth = Engine_Api::_()->authorization()->context;
            $formpopulate_array['auth_invite'] = $auth->isAllowed($siteevent, 'member', 'invite');
            $this->view->guest_lists = 0;
            if(isset($form->guest_lists)) {
                $this->view->guest_lists = $formpopulate_array['guest_lists'] = Engine_Api::_()->getDbTable("otherinfo", "siteevent")->getColumnValue($siteevent->getIdentity(), 'guest_lists');
            }
            
            $form->populate($formpopulate_array);


            //GET THE VALUES OF REPEAT EVENT INFO
            if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode') && !empty($siteevent->repeat_params) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventrepeat')) {
                $repeatEventInfo = json_decode($siteevent->repeat_params, true);
                if (!empty($repeatEventInfo['eventrepeat_type'])) {
                    $form->eventrepeat_id->setValue($repeatEventInfo['eventrepeat_type']);
                    //$form->event_repeat->setValue(1);
                }
            }

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

                if ($form->auth_view) {
                    if (1 == $auth->isAllowed($siteevent, $role, "view")) {
                        $form->auth_view->setValue($roleString);
                    }
                }

                if ($form->auth_comment) {
                    if (1 == $auth->isAllowed($siteevent, $role, "comment")) {
                        $form->auth_comment->setValue($roleString);
                    }
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
                if ($form->auth_topic && 1 == $siteeventAllow->isAllowed($siteevent, $role, 'topic')) {
                    $form->auth_topic->setValue($roleString);
                }

                if (isset($form->auth_post) && $form->auth_post && 1 == $siteeventAllow->isAllowed($siteevent, $role, 'post')) {
                    $form->auth_post->setValue($roleString);
                }
//         if ($form->auth_topic) {
//           if (1 == $auth->isAllowed($siteevent, $role, "topic")) {
//             $form->auth_topic->setValue($roleString);
//           }
//         }
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

                if ($form->auth_photo && 1 == $siteeventAllow->isAllowed($siteevent, $role, 'photo')) {
                    $form->auth_photo->setValue($roleString);
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

//           if ($form->auth_video) {
//             if (1 == $auth->isAllowed($siteevent, $role, "video")) {
//               $form->auth_video->setValue($roleString);
//             }
//           }
                    if ($role === 'like_member' && $ownerList) {
                        $role = $ownerList;
                    }

                    //Here we change isAllowed function for like privacy work only for populate.
                    $siteeventAllow = Engine_Api::_()->getApi('allow', 'siteevent');
                    if ($form->auth_video && 1 == $siteeventAllow->isAllowed($siteevent, $role, 'video')) {
                        $form->auth_video->setValue($roleString);
                    }
                }
            }


            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventdocument') || (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document') &&Engine_Api::_()->getDbtable('modules', 'document')->getIntegratedModules(array('enabled' => 1, 'item_type' => "siteevent_event", 'item_module' => 'siteevent')))) {
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
                    if ($form->auth_document && 1 == $siteeventAllow->isAllowed($siteevent, $role, 'document')) {
                        $form->auth_document->setValue($roleString);
                    }

//           if ($form->auth_document) {
//             if (1 == $auth->isAllowed($siteevent, $role, "document")) {
//               $form->auth_document->setValue($roleString);
//             }
//           }
                }
            }

            if (Engine_Api::_()->siteevent()->listBaseNetworkEnable()) {
                if (empty($siteevent->networks_privacy)) {
                    $form->networks_privacy->setValue(array(0));
                }
            }

            //if ($showDates) {
            // Convert and re-populate times
            $dateInfo = Engine_Api::_()->siteevent()->dbToUserDateTime($eventdateinfo);
            $this->view->starttimestemp = $start = strtotime($eventdateinfo['starttime']);
            if (!$this->view->locale()->useMilitaryTime()) {
                $this->view->startdate = date($calendarFormatString . ' g:i:A', strtotime($dateInfo['starttime']));
            }
            else
                $this->view->startdate = date($calendarFormatString . ' H:i', strtotime($dateInfo['starttime']));
            $this->view->startdate_hidden = date($calendarFormatString . ' G:i:s', strtotime($dateInfo['starttime']));
            if ($editFullEventDate)
                $form->populate(array(
                    'starttime' => $dateInfo['starttime'],
                    'endtime' => $dateInfo['endtime'],
                ));
            else
                $form->populate(array(
                    'endtime' => $dateInfo['endtime'],
                ));
            // }
            return;
        }

        if (!$editFullEventDate && isset($_POST['starttime'])) {
            $starttime = Engine_Api::_()->siteevent()->convertDateFormat($_POST['starttime']);
            if (!$this->view->locale()->useMilitaryTime())
                $this->view->startdate = date($calendarFormatString . ' g:i:A', strtotime($starttime));
            else
                $this->view->startdate = date($calendarFormatString . ' H:i:A', strtotime($starttime));
            $this->view->startdate_hidden = $_POST['starttime'];
        }
        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        $oldEventTitle = $siteevent->title;
        $oldEventVenue = $siteevent->venue_name;
        $oldEventStarttime = $eventdateinfo['starttime'];
        $oldEventEndtime = $eventdateinfo['endtime'];

        $values = !empty($siteeventGetEditPrivacy) ? $form->getValues() : $tempEditFlag;
        if (empty($values))
            return;

        if(isset($values['guest_lists']))
        Engine_Api::_()->getDbTable('otherinfo', 'siteevent')->update(array('guest_lists' => $values['guest_lists']), array('event_id = ?' => $siteevent->event_id));
        
        //CATEGORY IS REQUIRED FIELD
        if (isset($_POST['category_id']) && empty($_POST['category_id'])) {
            $error = $this->view->translate('Please complete Category field - it is required.');
            $error = Zend_Registry::get('Zend_Translate')->_($error);

            $form->getDecorator('errors')->setOption('escape', false);
            $form->addError($error);
            return;
        }
        //CHECK EITHER THE EVENT STARTTIME AND ENDTIME EXIST FOR THIS EVENT OR NOT. IF NOT THEN SHOW THE ERROR.
        if (!isset($values['starttime']) && isset($_POST['starttime']))
            $values['starttime'] = Engine_Api::_()->siteevent()->convertDateFormat($_POST['starttime']);
        if ((isset($_POST['eventrepeat_id'])) && ($_POST['eventrepeat_id'] == 'weekly' || $_POST['eventrepeat_id'] == 'monthly')) {
            $isValidOccurrences = Engine_Api::_()->siteevent()->checkValidOccurrences($values);
            if (!$isValidOccurrences) {
                $error = $this->view->translate('Please make sure you have selected the correct %s time interval - it is required.', ucfirst($_POST['eventrepeat_id']));
                $error = Zend_Registry::get('Zend_Translate')->_($error);
                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }
        }
        if (isset($values['add_new_host']) && $values['add_new_host'] && empty($_POST['host_title'])) {
            $error = $this->view->translate('Please complete Host Name field - it is required.');
            $error = Zend_Registry::get('Zend_Translate')->_($error);
            $form->getDecorator('errors')->setOption('escape', false);
            $form->addError($error);
        }
        if (isset($values['add_new_host']) && $values['add_new_host']) {
            $table = Engine_Api::_()->getItemTable('siteevent_organizer');
            $db = $table->getAdapter();
            $db->beginTransaction();
            try {
                $host = $table->createRow();
                $hostInfo = array(
                    'title' => $_POST['host_title'],
                    'description' => isset($values['host']['host_description']) ? $values['host']['host_description'] : '',
                    'creator_id' => $viewer_id,
                    'facebook_url' => isset($_POST['host_facebook']) && $_POST['host_facebook'] ? $_POST['host_facebook'] : null,
                    'twitter_url' => isset($_POST['host_twitter']) && $_POST['host_twitter'] ? $_POST['host_twitter'] : null,
                    'web_url' => isset($_POST['host_website']) && $_POST['host_website'] ? $_POST['host_website'] : null,
                );
                $host->setFromArray($hostInfo);
                $host->save();

                $host->setPhoto($form->organizer->host_photo);
                $values['host_type'] = $host->getType();
                $values['host_id'] = $host->getIdentity();
                $form->setHost($host);
                $form->add_new_host->setValue(0);
                $form->host_id->setValue($values['host_id']);
                $form->host_type->setValue($values['host_type']);
                $viewScriptOptions = $form->host_type->getDecorator('ViewScript')->getOptions();
                $viewScriptOptions['host'] = $host;
                $form->host_type->setDecorators(array(array('ViewScript', $viewScriptOptions)));
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
        if (isset($values['host']))
            unset($values['host']);

        if (empty($values['event_info'])) {
            $values = $listValues;
        } else {
            unset($values['event_info']);
        }
        $tags = '';
        if (isset($values['tags'])) {
            $tags = preg_split('/[,]+/', $values['tags']);
            $tags = array_filter(array_map("trim", $tags));
        }

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {

            if (Engine_Api::_()->siteevent()->listBaseNetworkEnable() && isset($values['networks_privacy']) && !empty($values['networks_privacy']) && in_array(0, $values['networks_privacy'])) {
                $values['networks_privacy'] = new Zend_Db_Expr('NULL');
                $form->networks_privacy->setValue(array(0));
            }

            //check if event creater has added any host details there.
//            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.host', 1)) {
//                $hostInfo = array();
//
//                if (isset($values['user_id']) && !empty($values['user_id'])) {
//                    $values['host'] = $values['user_id'];
//                }
//                elseif(isset($values['host']) && !empty($values['host'])) {
//                    $values['host'] = $values['user_id'];
//                }
//
//                $allowedInfo = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.hostinfo', array('body', 'links'));
//                if (in_array('body', $allowedInfo)) {
//                    $hostInfo['host_body'] = $values['host_body'];
//                }
//
//                if (in_array('links', $allowedInfo)) {
//                    if (isset($_POST['host_link'])) {
//                        if (!empty($_POST['show_facebook'])) {
//                            $hostInfo['show_facebook']['text'] = $_POST['show_facebook'];
//                            $hostInfo['show_facebook']['checked'] = false;
//                            if (isset($_POST['hostlinks']) && isset($_POST['hostlinks']['show_facebook']))
//                                $hostInfo['show_facebook']['checked'] = true;
//                        }
//                        if (!empty($_POST['show_twitter'])) {
//                            $hostInfo['show_twitter']['text'] = $_POST['show_twitter'];
//                            $hostInfo['show_twitter']['checked'] = false;
//                            if (isset($_POST['hostlinks']) && isset($_POST['hostlinks']['show_twitter']))
//                                $hostInfo['show_twitter']['checked'] = true;
//                        }
//                        if (!empty($_POST['show_website']) && isset($_POST['hostlinks']) && isset($_POST['hostlinks']['show_website'])) {
//                            $hostInfo['show_website']['text'] = $_POST['show_website'];
//                            $hostInfo['show_website']['checked'] = false;
//                            if (isset($_POST['hostlinks']) && isset($_POST['hostlinks']['show_website']))
//                                $hostInfo['show_website']['checked'] = true;
//                        }
//                    }
//                }
//
//                $host_params = json_encode($hostInfo);
//                Engine_Api::_()->getDbTable('otherinfo', 'siteevent')->update(array('host_params' => $host_params), array('event_id = ?' => $siteevent->event_id));
//            }
            //CHECK IF EVENT IS ONLINE THEN WE WILL NOT SAVE THE LOCATION.
            if ($values['is_online'])
                $values['location'] = '';


            //check if admin has disabled "approval" for RSVP to be invited.
            if (!isset($values['approval']))
                $values['approval'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.invite.rsvp.automatically', 1);

            //check if admin has disabled "auth_invite" for event members to invite other people
            if (!isset($values['auth_invite']))
                $values['auth_invite'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.invite.other.automatically', 1);

            //PRIVACY WORK
            $auth = Engine_Api::_()->authorization()->context;

            $auth->setAllowed($siteevent, 'member', 'invite', $values['auth_invite']);
            $siteevent->setFromArray($values);
            $siteevent->modified_date = date('Y-m-d H:i:s');
            if ($tags)
                $siteevent->tags()->setTagMaps($viewer, $tags);


            //if ($showDates) {

            $repeatEventInfo = Engine_Api::_()->siteevent()->getRepeatEventInfo($_POST, $event_id, $editFullEventDate, 'save');

            //CHECK EITHER USER HAS EDITED THE DATE OR NOT.IF NOT EDITED THEN WE WILL NOT UPDATE THE OCCURRENCE TABLE.
            $isupdate = Engine_Api::_()->siteevent()->editDateMatch($values, $eventdateinfo, $repeatEventInfo, $siteevent);

            if (!empty($repeatEventInfo)) {
                //SET THE PREVIOUS EVENT TYPE FOR SPECIAL CASE OF CUSTOM EVENT.
                $eventparams = json_decode($siteevent->repeat_params);
                if (!empty($eventparams))
                    $_POST['previous_eventtype'] = $eventparams->eventrepeat_type;
                //CONVERT TO CORRECT DATE FORMAT
                if (isset($repeatEventInfo['endtime']))
                    $repeatEventInfo['endtime']['date'] = Engine_Api::_()->siteevent()->convertDateFormat($repeatEventInfo['endtime']['date']);
                $siteevent->repeat_params = json_encode($repeatEventInfo);
            }
            else
                $siteevent->repeat_params = '';
            // }

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

            $dateInfo = Engine_Api::_()->siteevent()->dbToUserDateTime($eventdateinfo, 'time');
            $oldstart = $dateInfo['starttime'];
            $oldend = $dateInfo['endtime'];
            $datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium');
            $actionTable = Engine_Api::_()->getDbtable('actions', 'seaocore');
            $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
            $newStartTime = $values['starttime'];
            
            $newEndTime = Engine_Api::_()->siteevent()->convertDateFormat($values['endtime']);
            if (!$editFullEventDate && ($oldend != strtotime($newStartTime)) && $siteevent->repeat_params) {
                $dateInfo = Engine_Api::_()->siteevent()->userToDbDateTime(array('endtime' => $newEndTime));
                $action = $actionTable->addActivity($viewer, $siteevent, Engine_Api::_()->siteevent()->getActivtyFeedType($siteevent, 'siteevent_date_time_extended'), null, array('newtime' => $view->locale()->toDateTime($dateInfo['endtime'], array('size' => $datetimeFormat))));
                if ($action != null) {
                    //START NOTIFICATION AND EMAIL WORK
                    Engine_Api::_()->siteevent()->sendNotificationEmail($siteevent, $action, 'siteevent_date_time_updated', null, null, null, 'time', $siteevent);
                }
            } else if (!$siteevent->repeat_params && ($oldstart != strtotime($newStartTime) || ($oldend != strtotime($newEndTime)))) {
                $dateInfo = Engine_Api::_()->siteevent()->userToDbDateTime(array('starttime' => $newStartTime, 'endtime' => $newEndTime));
                $action = $actionTable->addActivity($viewer, $siteevent, Engine_Api::_()->siteevent()->getActivtyFeedType($siteevent, 'siteevent_date_time_updated'), null, array('starttime' => $view->locale()->toDateTime($dateInfo['starttime'], array('size' => $datetimeFormat)), 'endtime' => $view->locale()->toDateTime($dateInfo['endtime'], array('size' => $datetimeFormat))));
                if ($action != null) {
                    //START NOTIFICATION AND EMAIL WORK
                    Engine_Api::_()->siteevent()->sendNotificationEmail($siteevent, $action, 'siteevent_date_time_updated', null, null, null, 'time', $siteevent);
                }
            }


//      if (empty($siteevent->location)) {
//        Engine_Api::_()->getDbtable('locations', 'siteevent')->delete(array('event_id =?' => $siteevent->event_id));
//      } elseif (!empty($siteevent->location) && ($siteevent->location != $previous_location)) {
//        $siteevent->setLocation();
//      }
            //SAVE CUSTOM FIELDS
            $customfieldform = $form->getSubForm('fields');
            $customfieldform->setItem($siteevent);
            $customfieldform->saveValues();
            if ($customfieldform->getElement('submit')) {
                $customfieldform->removeElement('submit');
            }

            if (isset($values['category_id']) && !empty($values['category_id'])) {
                $categoryIds = array();
                $categoryIds[] = $siteevent->category_id;
                $categoryIds[] = $siteevent->subcategory_id;
                $categoryIds[] = $siteevent->subsubcategory_id;
                $siteevent->profile_type = Engine_Api::_()->getDbtable('categories', 'siteevent')->getProfileType($categoryIds, 0, 'profile_type');
                if ($siteevent->profile_type != $previous_profile_type) {

                    $fieldvalueTable = Engine_Api::_()->fields()->getTable('siteevent_event', 'values');
                    $fieldvalueTable->delete(array('item_id = ?' => $siteevent->event_id));

                    Engine_Api::_()->fields()->getTable('siteevent_event', 'search')->delete(array(
                        'item_id = ?' => $siteevent->event_id,
                    ));

                    if (!empty($siteevent->profile_type) && !empty($previous_profile_type)) {
                        //PUT NEW PROFILE TYPE
                        $fieldvalueTable->insert(array(
                            'item_id' => $siteevent->event_id,
                            'field_id' => $defaultProfileId,
                            'index' => 0,
                            'value' => $siteevent->profile_type,
                        ));
                    }
                }
                $siteevent->save();
            }

            //NOT SEARCHABLE IF SAVED IN DRAFT MODE
            if (!empty($siteevent->draft)) {
                $siteevent->search = 0;
                $siteevent->save();
            }


            //NOW MAKE THE ENTRY OF REPEAT INFO IF IT IS  ENABLED
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
                    $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($siteevent->getOwner(), $siteevent, 'siteevent_new');
                    if ($action != null) {
                        Engine_Api::_()->getDbtable('actions', 'seaocore')->attachActivity($action, $siteevent);
                    }
                }
            }          

            //CREATE AUTH STUFF HERE
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

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventdocument') || (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document') && Engine_Api::_()->getDbtable('modules', 'document')->getIntegratedModules(array('enabled' => 1, 'item_type' => "siteevent_event", 'item_module' => 'siteevent')))) {
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
              
           if(empty($siteevent_pending)){
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

                    $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                    $occurrence_id = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getAllOccurrenceDates($siteevent->event_id, 1);
                    $notifyApi->addNotification($newHost, $viewer, $siteevent, 'siteevent_host', array('occurrence_id' => $occurrence_id));
                    $notifyApi->addNotification($newHost, $viewer, $siteevent, 'siteevent_member', array('occurrence_id' => $occurrence_id));

                    //INCREMENT MESSAGE COUNTER.
                    Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');
                }
                //CHECK IF NO USER HAS JOINED THIS EVENT YET THEN WE WILL SHOW START DATE IN EDIT MODE ELSE ONLY END DATE AND END REPEAT TIME IN EDIT MODE.
                $editFullEventDate = true;
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventrepeat')){
                  $hasEventMember = $siteevent->membership()->hasEventMember($viewer, true);
                  if (Engine_Api::_()->siteevent()->hasTicketEnable()){
                    $hasEventTicketGuest = Engine_Api::_()->getDbTable('orders', 'siteeventticket')->hasEventTicketGuest($siteevent,$viewer);
                  }
                }
                //IF EVENT JOINED / TICKET SOLD THEN CAN NOT EDIT FULL EVENT DATE
                if(!$hasEventMember || (isset($hasEventTicketGuest) && $hasEventTicketGuest)){
                  $editFullEventDate = false;
                }
                $this->view->editFullEventDate = $editFullEventDate;
                
                //IF EVENT EDITING IS ALREADY FALSE THAN DO NOT NEED TO CHECK IT FOR CAPACITY
                if($editFullEventDate && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.waitlist', 1)) {
                    $totalEventsInWaiting = Engine_Api::_()->getDbTable('waitlists', 'siteevent')->getColumnValue(array('event_id' => $siteevent->getIdentity(), 'columnName' => 'COUNT(*) AS totalEventsInWaiting'));
                    $this->view->editFullEventDate = $editFullEventDate = !$totalEventsInWaiting;
                }
                
                $form->setEditFullEventDate($editFullEventDate);
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
           
            //EDIT THE TICKETS SELL ENDTIME
            if (Engine_Api::_()->siteevent()->hasTicketEnable()){
                Engine_Api::_()->siteeventticket()->updateTicketsSellEndTime($siteevent);
            }             
           
            $db->commit();
            $this->view->form = $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $siteevent->setLocation();
        $db->beginTransaction();
        try {
            $actionTable = Engine_Api::_()->getDbtable('actions', 'seaocore');
            foreach ($actionTable->getActionsByObject($siteevent) as $action) {
                $actionTable->resetActivityBindings($action);
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        //GET FORM VALUES


        if (!$editFullEventDate) {
            if (isset($values['starttime']))
                $starttime = $values['starttime'];
            else
                $starttime = Engine_Api::_()->siteevent()->convertDateFormat($_POST['starttime']);

            if (!$this->view->locale()->useMilitaryTime())
                $this->view->startdate = date($calendarFormatString . ' g:i:A', strtotime($starttime));
            else
                $this->view->startdate = date($calendarFormatString . ' H:i:A', strtotime($starttime));
            if (isset($_POST['starttime']))
                $this->view->startdate_hidden = $_POST['starttime'];
            else
                $this->view->startdate_hidden = @$values['starttime'];
        }
    }

    //ACTION TO SET OVERVIEW
    public function overviewAction() {

        //ONLY LOGGED IN USER CAN ADD OVERVIEW
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET EVENT ID AND OBJECT
        $event_id = $this->_getParam('event_id');
        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        $siteeventOverviewFormPost = Zend_Registry::isRegistered('siteeventOverviewFormPost') ? Zend_Registry::get('siteeventOverviewFormPost') : null;

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.overview', 1) || !Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventpost.overview', 1)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }
        
        if ($this->_hasPackageEnable && !Engine_Api::_()->siteeventpaid()->allowPackageContent($siteevent->package_id, "overview")) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        if (!$siteevent->authorization()->isAllowed($viewer, 'edit')) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        if (!Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'siteevent_event', "overview")) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        //SELECTED TAB
        $this->view->TabActive = "overview";

        //MAKE FORM
        $this->view->form = $form = new Siteevent_Form_Overview();

        //IF NOT POSTED
        if (!$this->getRequest()->isPost()) {
            $saved = $this->_getParam('saved');
            if (!empty($saved))
                $this->view->success = Zend_Registry::get('Zend_Translate')->_('Your event has been successfully created. You can enhance your event from this Dashboard by creating other components.');
        }

        $event_id = $siteevent->getIdentity();

        $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'siteevent');

        //SAVE THE VALUE
        if (!empty($siteeventOverviewFormPost) && $this->getRequest()->isPost()) {
            $tableOtherinfo->update(array('overview' => $_POST['overview']), array('event_id = ?' => $event_id));
            $this->view->form = $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
        }

        //POPULATE FORM
        $values['overview'] = $tableOtherinfo->getColumnValue($event_id, 'overview');
        $form->populate($values);
    }

    //ACTION FOR EDIT STYLE OF SITEEVENT
    public function editstyleAction() {

        //ONLY LOGGED IN USER CAN EDIT THE STYLE
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET EVENT ID AND OBJECT
        $event_id = $this->_getParam('event_id');
        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        //SET SITEEVENT SUBJECT
        Engine_Api::_()->core()->setSubject($siteevent);
        if (!$siteevent->authorization()->isAllowed($viewer, 'edit')) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        if (!Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('siteevent_event', $viewer->level_id, "style")) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }



        //SELECTED TAB
        $this->view->TabActive = "style";

        //MAKE FORM
        $this->view->form = $form = new Siteevent_Form_Style();

        //FETCH EXISTING ROWS
        $tableStyle = Engine_Api::_()->getDbtable('styles', 'core');
        $select = $tableStyle->select()
                ->where('type = ?', 'siteevent_event')
                ->where('id = ?', $event_id)
                ->limit();
        $row = $tableStyle->fetchRow($select);

        //CHECK POST
        if (!$this->getRequest()->isPost()) {
            $form->populate(array('style' => ( null == $row ? '' : $row->style )));
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //PROCESS
        $style = $form->getValue('style');
        $style = strip_tags($style);

        $forbiddenStuff = array(
            '-moz-binding',
            'expression',
            'javascript:',
            'behaviour:',
            'vbscript:',
            'mocha:',
            'livescript:',
        );
        $style = str_replace($forbiddenStuff, '', $style);

        //SAVE ROW
        if (null == $row) {
            $row = $tableStyle->createRow();
            $row->type = 'siteevent_event';
            $row->id = $event_id;
        }
        $row->style = $style;
        $row->save();
        $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
    }

    //ACTION FOR DELETE EVENT
    public function deleteAction() {

        //LOGGED IN USER CAN DELETE EVENT
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET EVENT ID AND OBJECT
        $event_id = $this->_getParam('event_id');

        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        //IF SITEEVENT IS NOT EXIST
        if (empty($siteevent)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        $this->view->title = 'Event';

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        
        //TICKET CHECK - IF ATLEAST ONE GUEST & EVENT NOT FINISHED THEN EVENT CANNOT BE DELETED.
        if (Engine_Api::_()->siteevent()->hasTicketEnable()){
          $hasEventTicketGuest = Engine_Api::_()->getDbTable('orders', 'siteeventticket')->hasEventTicketGuest($siteevent, $viewer);
          $lastOccurrenceEndtime = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($event_id, 'DESC'); 
          $current_date = date('Y-m-d H:i:s');
          if($hasEventTicketGuest && $lastOccurrenceEndtime >= $current_date){
            $this->view->canNotDeleteMessage = true;
          }
        }
        //end

        $canDeletePrivacy = $siteevent->canDeletePrivacy($siteevent->parent_type, $siteevent->parent_id);

        if (empty($canDeletePrivacy))
            return $this->_forwardCustom('requireauth', 'error', 'core');

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("siteevent_main");

        //DELETE SITEEVENT AFTER CONFIRMATION
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('confirm') == true) {
            $siteevent->delete();
            return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'siteevent_general', true);
        }
    }

    //ACTION FOR CLOSE / OPEN EVENT
    public function closeAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //SMOOTHBOX
        if (null == $this->_helper->ajaxContext->getCurrentContext()) {
            $this->_helper->layout->setLayout('default-simple');
        } else {
            //NO LAYOUT
            $this->_helper->layout->disableLayout(true);
        }

        //GET EVENT ID AND OBJECT
        $event_id = $this->view->event_id = $this->_getParam('event_id');

        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
      
        //TICKET CHECK - IF ATLEAST ONE GUEST & EVENT NOT FINISHED THEN EVENT CANNOT BE DELETED.
        $this->view->canNotCancelMessage = false;
        if (Engine_Api::_()->siteevent()->hasTicketEnable()){
          $hasEventTicketGuest = Engine_Api::_()->getDbTable('orders', 'siteeventticket')->hasEventTicketGuest($siteevent, $viewer);
          $lastOccurrenceEndtime = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($event_id, 'DESC'); 
          $current_date = date('Y-m-d H:i:s');
          if($hasEventTicketGuest && $lastOccurrenceEndtime >= $current_date){
            $this->view->canNotCancelMessage = true;
          }
        }
        //end
        
        //
        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "edit")->isValid()) {
            return;
        }

        //CHECK POST
        if (!$this->getRequest()->isPost())
            return;

        //ONLY OWNER CAN PUBLISH THE EVENT
        if ($viewer_id == $siteevent->owner_id || $viewer->level_id == 1) {
            $this->view->permission = true;
            $this->view->success = false;
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
                $this->view->success = true;
            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
        } else {
            $this->view->permission = false;
        }

        $this->_forwardCustom('success', 'utility', 'core', array(
            'smoothboxClose' => 10,
            'parentRefresh' => 10,
            'messages' => array('Changes has been made successfully !')
        ));

//        //LOGGED IN USER CAN CLOSE EVENT
//        if (!$this->_helper->requireUser()->isValid())
//            return;
//
//        //GET EVENT
//        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $this->_getParam('event_id'));
//
//        //GET VIEWER
//        $viewer = Engine_Api::_()->user()->getViewer();
//
//        //AUTHORIZATION CHECK
//        if (!$this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "edit")->isValid()) {
//            return;
//        }
//
//        //BEGIN TRANSCATION
//        $db = Engine_Api::_()->getDbTable('events', 'siteevent')->getAdapter();
//        $db->beginTransaction();
//
//        try {
//            $siteevent->closed = empty($siteevent->closed) ? 1 : 0;
//            $siteevent->save();
//            $db->commit();
//        } catch (Exception $e) {
//            $db->rollBack();
//            throw $e;
//        }
//
//        //RETURN TO MANAGE PAGE
//        return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), "siteevent_general", true);
    }

    //ACTION FOR CONSTRUCT TAG CLOUD
    public function tagscloudAction() {
        $this->_helper->content
                ->setContentName('siteevent_index_tagscloud')
//            ->setNoRender()
                ->setEnabled();
//        $this->view->title = 'Event';
//
//        //GET NAVIGATION
//        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
//                ->getNavigation('siteevent_main');
//
//        //CONSTRUCTING TAG CLOUD
//        $tag_array = array();
//        $tag_cloud_array = Engine_Api::_()->siteevent()->getTags(0, 1000, 0);
//        foreach ($tag_cloud_array as $vales) {
//
//            $tag_array[$vales['text']] = $vales['Frequency'];
//            $tag_id_array[$vales['text']] = $vales['tag_id'];
//        }
//
//        if (!empty($tag_array)) {
//
//            $max_font_size = 18;
//            $min_font_size = 12;
//            $max_frequency = max(array_values($tag_array));
//            $min_frequency = min(array_values($tag_array));
//            $spread = $max_frequency - $min_frequency;
//
//            if ($spread == 0) {
//                $spread = 1;
//            }
//
//            $step = ($max_font_size - $min_font_size) / ($spread);
//
//            $tag_data = array('min_font_size' => $min_font_size, 'max_font_size' => $max_font_size, 'max_frequency' => $max_frequency, 'min_frequency' => $min_frequency, 'step' => $step);
//
//            $this->view->tag_data = $tag_data;
//            $this->view->tag_id_array = $tag_id_array;
//        }
//        $this->view->tag_array = $tag_array;
    }

    public function getEventTypeAction() {
        $isAjax = $this->_getParam('isAjax', null);
        $type = $this->_getParam('type', null);
        $event_id = $this->_getParam('event_id', null);
        $getEventOrder = $this->_getParam('getEventOrder', null);
        $tempGetEventType = false;
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewerId = $viewer->getIdentity();
        $this->view->getClassName = $type;

        if (!empty($getEventOrder) && $getEventOrder == 1) {
            $this->view->getEventOrder = 0;
            $this->view->defaultEventView = false;
        }
        if (!empty($getEventOrder) && $getEventOrder == 2) {
            $this->view->getEventOrder = 1;
            $this->view->defaultEventView = true;
        }
        if (!empty($getEventOrder) && $getEventOrder == 3) {
            $this->view->getEventOrder = 2;
            $this->view->defaultEventView = true;
        }
        if (!empty($getEventOrder) && $getEventOrder == 4) {
            $this->view->getEventOrder = 3;
            $this->view->defaultEventView = false;
        }
        if (!empty($getEventOrder) && $getEventOrder == 5) {
            $this->view->getEventOrder = 4;
            $this->view->defaultEventView = true;
        }

        if (!empty($event_id)) {
            $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
            if (empty($siteevent)) {
                $this->view->setEventType = false;
            } else {
                $this->view->setEventType = $siteevent;
            }
        }

        if (!empty($getEventOrder) && !empty($event_id)) {
            $auth = Engine_Api::_()->authorization()->context;
            $this->view->viewEveryone = $auth->getAllowed($event_id, 'everyone', 'view', true);
            $this->view->commentEveryone = $auth->getAllowed($event_id, 'everyone', 'comment', true);
            $this->view->getViewEveryone = $auth->getAllowed($event_id, 'everyone', 'view', true);
            $this->view->getCommentEveryone = $auth->getAllowed($event_id, 'everyone', 'comment', true);
        }

        $this->view->getEventType = $tempGetEventType = true;
    }

    //ACTION FOR TELL A FRIEND ABOUT EVENT
    public function tellafriendAction() {

        //SET LAYOUT
        $this->_helper->layout->setLayout('default-simple');

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewr_id = $viewer->getIdentity();

        //GET FORM
        $this->view->form = $form = new Siteevent_Form_TellAFriend();

        if (!empty($viewr_id)) {
            $value['sender_email'] = $viewer->email;
            $value['sender_name'] = $viewer->displayname;
            $form->populate($value);
        }

        //FORM VALIDATION
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            //GET EVENT ID AND OBJECT
            $event_id = $this->_getParam('event_id', $this->_getParam('id', null));
            $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

            //GET FORM VALUES
            $values = $form->getValues();

            //EXPLODE EMAIL IDS
            $reciver_ids = explode(',', $values['reciver_emails']);
            if (!empty($values['send_me'])) {
                $reciver_ids[] = $values['sender_email'];
            }
            $sender_email = $values['sender_email'];
            $heading = $siteevent->title;

            //CHECK VALID EMAIL ID FORMAT
            $validator = new Zend_Validate_EmailAddress();
            $validator->getHostnameValidator()->setValidateTld(false);

            if (!$validator->isValid($sender_email)) {
                $form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid sender email address value'));
                return;
            }

            foreach ($reciver_ids as $reciver_id) {
                $reciver_id = trim($reciver_id, ' ');
                if (!$validator->isValid($reciver_id)) {
                    $form->addError(Zend_Registry::get('Zend_Translate')->_('Please enter correct email address of the receiver(s).'));
                    return;
                }
            }

            $sender = $values['sender_name'];
            $message = $values['message'];

            Engine_Api::_()->getApi('mail', 'core')->sendSystem($reciver_ids, 'SITEEVENT_TELLAFRIEND_EMAIL', array(
                'host' => $_SERVER['HTTP_HOST'],
                'sender' => $sender,
                'heading' => $heading,
                'message' => '<div>' . $message . '</div>',
                'object_link' => $siteevent->getHref(),
                'email' => $sender_email,
                'queue' => true
            ));

            $this->_forwardCustom('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefreshTime' => '15',
                'format' => 'smoothbox',
                'messages' => Zend_Registry::get('Zend_Translate')->_('Your message to your friend has been sent successfully.')
            ));
        }
    }

    //ACTION FOR PRINTING THE SITEEVENT
    public function printAction() {

        //LAYOUT DEFAULT
        $this->_helper->layout->setLayout('default-simple');

        //GET EVENT ID AND OBJECT
        $event_id = $this->_getParam('event_id', $this->_getParam('id', null));
        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        //IF EVENT IS NOT EXIST
        if (empty($siteevent)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        //GET START AND END DATE
        $this->view->startEndDates = $siteevent->getStartEndDate($this->_getParam('occurrence_id', null));
        $this->view->datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium');

        if ($siteevent->category_id != 0) {
            $categoryTable = Engine_Api::_()->getDbtable('categories', 'siteevent');
            $this->view->category_name = $categoryTable->getCategory($siteevent->category_id)->category_name;

            if ($siteevent->subcategory_id != 0) {
                $this->view->subcategory_name = $categoryTable->getCategory($siteevent->subcategory_id)->category_name;

                if ($siteevent->subsubcategory_id != 0) {
                    $this->view->subsubcategory_name = $categoryTable->getCategory($siteevent->subsubcategory_id)->category_name;
                }
            }
        }

        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
        $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($siteevent);
    }

    //ACTION FOR EDIT THE LOCATION
    public function editlocationAction() {

        //GET EVENT ID AND OBJECT
        $this->view->event_id = $event_id = $this->_getParam('event_id');
        $this->view->siteevent = $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        //IF LOCATION SETTING IS ENABLED
        if (!Engine_Api::_()->siteevent()->enableLocation()) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "edit")->isValid()) {
            return;
        }
        Engine_Api::_()->core()->setSubject($siteevent);
        //WHICH TAB SHOULD COME ACTIVATE
        $this->view->TabActive = "location";

        //GET LOCATION TABLE
        $locationTable = Engine_Api::_()->getDbtable('locations', 'siteevent');

        //MAKE VALUE ARRAY
        $values = array();
        $value['id'] = $siteevent->event_id;

        //GET LOCATION
        $this->view->location = $location = $locationTable->getLocation($value);

        if (!empty($location)) {

            //MAKE FORM
            $this->view->form = $form = new Siteevent_Form_Location(array(
                'item' => $siteevent,
                'location' => $location->location
            ));

            //CHECK POST
            if (!$this->getRequest()->isPost()) {
                $form->populate($location->toarray());
                return;
            }

            //FORM VALIDATION
            if (!$form->isValid($this->getRequest()->getPost())) {
                return;
            }

            //GET FORM VALUES
            $values = $form->getValues();
            unset($values['submit']);
            unset($values['location']);

            //UPDATE LOCATION
            $locationTable->update($values, array('event_id = ?' => $event_id));

            $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
        }
        $this->view->location = $locationTable->getLocation($value);
    }

    //ACTION FOR EDIT THE EVENT ADDRESS
    public function editaddressAction() {

        //GET EVENT ID AND OBJECT
        $event_id = $this->_getParam('event_id');
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        $viewer = Engine_Api::_()->user()->getViewer();


        //IF SITEEVENT IS NOT EXIST
        if (empty($siteevent)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        //MAKE FORM
        $this->view->form = $form = new Siteevent_Form_Address(array('item' => $siteevent));

        //CHECK POST
        if (!$this->getRequest()->isPost()) {
            $form->populate($siteevent->toArray());
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        $oldEventLocation = $siteevent->location;
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {

            $location = $_POST['location'];
            $siteevent->location = $location;
            $siteevent->save();

            if ($siteevent->location !== $oldEventLocation) {
                $actionTable = Engine_Api::_()->getDbtable('actions', 'seaocore');
                $action = $actionTable->addActivity($viewer, $siteevent, Engine_Api::_()->siteevent()->getActivtyFeedType($siteevent, 'siteevent_location_updated'), null, array('newlocation' => $siteevent->location));
                if ($action != null) {
                    //START NOTIFICATION AND EMAIL WORK
                    Engine_Api::_()->siteevent()->sendNotificationEmail($siteevent, $action, 'siteevent_location_updated', null, null, null, 'location', $siteevent);
                }
            }


            //GET LOCATION TABLE
            $locationTable = Engine_Api::_()->getDbtable('locations', 'siteevent');
            if (!empty($location)) {
                $siteevent->setLocation();
                $locationTable->update(array('location' => $location), array('event_id = ?' => $event_id));
            } else {
                $locationTable->delete(array('event_id = ?' => $event_id));
            }

            $db->commit();

            $this->_forwardCustom('success', 'utility', 'core', array(
                'smoothboxClose' => 500,
                'parentRefresh' => 500,
                'messages' => array('Your event location has been modified successfully.')
            ));
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    //ACTION TO GET SUB-CATEGORY
    public function subCategoryAction() {

        //GET CATEGORY ID
        $category_id_temp = $this->_getParam('category_id_temp');

        //INTIALIZE ARRAY
        $this->view->subcats = $data = array();

        //RETURN IF CATEGORY ID IS EMPTY
        if (empty($category_id_temp))
            return;

        //GET CATEGORY TABLE
        $tableCategory = Engine_Api::_()->getDbTable('categories', 'siteevent');

        //GET CATEGORY
        $category = $tableCategory->getCategory($category_id_temp);
        if (!empty($category->category_name)) {
            $categoryName = Engine_Api::_()->getItem('siteevent_category', $category_id_temp)->getCategorySlug();
        }

        //GET SUB-CATEGORY
        $subCategories = $tableCategory->getSubCategories($category_id_temp, array('category_id', 'category_name'));

        foreach ($subCategories as $subCategory) {
            $content_array = array();
            $content_array['category_name'] = $this->view->translate($subCategory->category_name);
            $content_array['category_id'] = $subCategory->category_id;
            $content_array['categoryname_temp'] = $categoryName;
            $data[] = $content_array;
        }

        $this->view->subcats = $data;
    }

    //ACTION FOR FETCHING SUB-CATEGORY
    public function subsubCategoryAction() {

        //GET SUB-CATEGORY ID
        $subcategory_id_temp = $this->_getParam('subcategory_id_temp');

        //INTIALIZE ARRAY
        $this->view->subsubcats = $data = array();

        //RETURN IF SUB-CATEGORY ID IS EMPTY
        if (empty($subcategory_id_temp))
            return;

        //GET CATEGORY TABLE
        $tableCategory = Engine_Api::_()->getDbTable('categories', 'siteevent');

        //GET SUB-CATEGORY
        $subCategory = $tableCategory->getCategory($subcategory_id_temp);
        if (!empty($subCategory->category_name)) {
            $subCategoryName = Engine_Api::_()->getItem('siteevent_category', $subcategory_id_temp)->getCategorySlug();
        }

        //GET 3RD LEVEL CATEGORIES
        $subCategories = $tableCategory->getSubCategories($subcategory_id_temp, array('category_id', 'category_name'));
        foreach ($subCategories as $subCategory) {
            $content_array = array();
            $content_array['category_name'] = $this->view->translate($subCategory->category_name);
            $content_array['category_id'] = $subCategory->category_id;
            $content_array['categoryname_temp'] = $subCategoryName;
            $data[] = $content_array;
        }
        $this->view->subsubcats = $data;
    }

    //ACTION FOR LIKES THE EVENT
    public function likesiteeventAction() {

        //GET SETTINGS
        $like_user_str = 0;
        $this->view->resource_type = $resource_type = $this->_getParam('resource_type');
        $this->view->resource_id = $resource_id = $this->_getParam('resource_id');
        $this->view->call_status = $call_status = $this->_getParam('call_status');
        $this->view->page = $page = $this->_getParam('page', 1);
        $search = $this->_getParam('search', '');
        $this->view->is_ajax = $is_ajax = $this->_getParam('is_ajax', 0);
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        $this->view->search = $search;
        if (empty($search)) {
            //$this->view->search = $this->view->translate('Search Members');
        }

        if ($call_status == 'friend') {

            //GET CORE LIKE TABLE
            $sub_status_table = Engine_Api::_()->getItemTable('core_like');
            $sub_status_name = $sub_status_table->info('name');

            //GET MEMBERSHIP TABLE
            $membership_table = Engine_Api::_()->getDbtable('membership', 'user');
            $member_name = $membership_table->info('name');

            //GET USER TABLE
            $user_table = Engine_Api::_()->getItemTable('user');
            $user_Name = $user_table->info('name');

            //MAKE QUERY
            $sub_status_select = $user_table->select()
                    ->setIntegrityCheck(false)
                    ->from($sub_status_name, array('poster_id'))
                    ->joinInner($member_name, "$member_name . user_id = $sub_status_name . poster_id", NULL)
                    ->joinInner($user_Name, "$user_Name . user_id = $member_name . user_id")
                    ->where($member_name . '.resource_id = ?', $viewer_id)
                    ->where($member_name . '.active = ?', 1)
                    ->where($sub_status_name . '.resource_type = ?', $resource_type)
                    ->where($sub_status_name . '.resource_id = ?', $resource_id)
                    ->where($sub_status_name . '.poster_id != ?', $viewer_id)
                    ->where($sub_status_name . '.poster_id != ?', 0)
                    ->where($user_Name . '.displayname LIKE ?', '%' . $search . '%')
                    ->order('	like_id DESC');
        } else if ($call_status == 'public') {

            //GET CORE LIKE TABLE
            $sub_status_table = Engine_Api::_()->getItemTable('core_like');
            $sub_status_name = $sub_status_table->info('name');

            //GET USER TABLE
            $user_table = Engine_Api::_()->getItemTable('user');
            $user_Name = $user_table->info('name');

            //MAKE QUERY
            $sub_status_select = $user_table->select()
                    ->setIntegrityCheck(false)
                    ->from($sub_status_name, array('poster_id'))
                    ->joinInner($user_Name, "$user_Name . user_id = $sub_status_name . poster_id")
                    ->where($sub_status_name . '.resource_type = ?', $resource_type)
                    ->where($sub_status_name . '.resource_id = ?', $resource_id)
                    ->where($sub_status_name . '.poster_id != ?', 0)
                    ->where($user_Name . '.displayname LIKE ?', '%' . $search . '%')
                    ->order($sub_status_name . '.like_id DESC');
        }

        $fetch_sub = Zend_Paginator::factory($sub_status_select);
        $fetch_sub->setCurrentPageNumber($page);
        $fetch_sub->setItemCountPerPage(10);
        $check_object_result = $fetch_sub->getTotalItemCount();

        $this->view->user_obj = array();
        if (!empty($check_object_result)) {
            $this->view->user_obj = $fetch_sub;
        } else {
            $this->view->no_result_msg = $this->view->translate('No results were found.');
        }

        //TOTAL LIKE FOR THIS CONTENT
        $this->view->public_count = Engine_Api::_()->siteevent()->number_of_like('siteevent_event', $resource_id);

        //NUMBER OF FRIENDS LIKE THIS CONTENT
        $this->view->friend_count = Engine_Api::_()->siteevent()->friend_number_of_like($resource_type, $resource_id);

        //GET LIKE TITLE
        if ($resource_type == 'member') {
            $this->view->like_title = Engine_Api::_()->getItem('user', $resource_id)->displayname;
        } else {
            $this->view->like_title = Engine_Api::_()->getItem($resource_type, $resource_id)->title;
        }
    }

    //ACTION FOR GLOBALLY LIKE THE EVENT
    public function globallikesAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET THE VALUE OF RESOURCE ID AND TYPE 
        $resource_id = $this->_getParam('resource_id');
        $resource_type = $this->_getParam('resource_type');
        $like_id = $this->_getParam('like_id');
        $status = $this->_getParam('smoothbox', 1);
        $this->view->status = true;

        //GET LIKE TABLE
        $likeTable = Engine_Api::_()->getDbTable('likes', 'core');
        $like_name = $likeTable->info('name');

        //GET OBJECT
        $resource = Engine_Api::_()->getItem($resource_type, $resource_id);
        if (empty($like_id)) {

            //CHECKING IF USER HAS MAKING DUPLICATE ENTRY OF LIKING AN APPLICATION.
            $like_id_temp = Engine_Api::_()->siteevent()->check_availability($resource_type, $resource_id);
            if (empty($like_id_temp)) {

                if (!empty($resource)) {
                    $like_id = $likeTable->addLike($resource, $viewer);
                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitelike'))
                        Engine_Api::_()->sitelike()->setLikeFeed($viewer, $resource);
                }

                $notify_table = Engine_Api::_()->getDbtable('notifications', 'activity');
                $db = $likeTable->getAdapter();
                $db->beginTransaction();
                try {

                    //CREATE THE NEW ROW IN TABLE
                    if ($resource->owner_id != $viewer_id) {
                        $notifyData = $notify_table->createRow();
                        $notifyData->user_id = $resource->owner_id;
                        $notifyData->subject_type = $viewer->getType();
                        $notifyData->subject_id = $viewer->getIdentity();
                        $notifyData->object_type = $resource_type;
                        $notifyData->object_id = $resource_id;
                        $notifyData->type = 'liked';
                        $notifyData->params = $resource->getShortType();
                        $notifyData->date = date('Y-m-d h:i:s', time());
                        $notifyData->save();
                    }
                    $this->view->like_id = $like_id;
                    $db->commit();
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
                $like_msg = $this->view->translate('Successfully Liked.');
            }
        } else {
            if (!empty($resource)) {
                $likeTable->removeLike($resource, $viewer);
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitelike'))
                    Engine_Api::_()->sitelike()->removeLikeFeed($viewer, $resource);
            }
            $like_msg = $this->view->translate('Successfully Unliked.');
        }

        if (empty($status)) {
            $this->_forwardCustom('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'messages' => array($like_msg))
            );
        }
    }

    //ACTION FOR PUBLISH EVENT
    public function publishAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //SMOOTHBOX
        if (null == $this->_helper->ajaxContext->getCurrentContext()) {
            $this->_helper->layout->setLayout('default-simple');
        } else {
            //NO LAYOUT
            $this->_helper->layout->disableLayout(true);
        }

        //CHECK POST
        if (!$this->getRequest()->isPost())
            return;

        //GET EVENT ID AND OBJECT
        $event_id = $this->view->event_id = $this->_getParam('event_id');


        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams($siteevent, $viewer, "edit")->isValid()) {
            return;
        }

        //ONLY OWNER CAN PUBLISH THE EVENT
        if ($viewer_id == $siteevent->owner_id || $viewer->level_id == 1) {
            $this->view->permission = true;
            $this->view->success = false;
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
                $this->view->success = true;
            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
        } else {
            $this->view->permission = false;
        }

        $this->_forwardCustom('success', 'utility', 'core', array(
            'smoothboxClose' => 10,
            'parentRefresh' => 10,
            'messages' => array('Successfully Published !')
        ));
    }

    //ACTION FOR GET THE EVENTS BASED ON SEARCHING
    public function ajaxSearchAction() {



        //GET EVENTS AND MAKE ARRAY
        $usersiteevents = Engine_Api::_()->getDbtable('events', 'siteevent')->getDayItems($this->_getParam('text'), $this->_getParam('limit', 10));
        $data = array();
        $mode = $this->_getParam('struct');
        $count = count($usersiteevents);

        $i = 0;
        foreach ($usersiteevents as $usersiteevent) {
            $siteevent_url = $this->view->url(array('event_id' => $usersiteevent->event_id, 'slug' => $usersiteevent->getSlug()), "siteevent_entry_view", true);
            $content_photo = $this->view->itemPhoto($usersiteevent, 'thumb.icon');
            $i++;
            $data[] = array(
                'id' => $usersiteevent->event_id,
                'label' => $usersiteevent->title,
                'photo' => $content_photo,
                'siteevent_url' => $siteevent_url,
                'total_count' => $count,
                'count' => $i
            );
        }

        if (!empty($data) && $i >= 1) {
            if ($data[--$i]['count'] == $count) {
                $data[$count]['id'] = 'stopevent';
                $data[$count]['label'] = $this->_getParam('text');
                $data[$count]['siteevent_url'] = 'seeMoreLink';
                $data[$count]['total_count'] = $count;
            }
        }
        return $this->_helper->json($data);
    }

    //ACTION FOR GET THE EVENTS BASED ON SEARCHING
    public function getSearchEventsAction() {

        //GET PRODUCTS AND MAKE ARRAY
        $usersiteevents = Engine_Api::_()->getDbtable('events', 'siteevent')->getDayItems($this->_getParam('text'), $this->_getParam('limit', 10));
        $data = array();
        $mode = $this->_getParam('struct');
        $count = count($usersiteevents);
        if ($mode == 'text') {
            $i = 0;
            foreach ($usersiteevents as $usersiteevent) {
                $siteevent_url = $this->view->url(array('event_id' => $usersiteevent->event_id, 'slug' => $usersiteevent->getSlug()), "siteevent_entry_view", true);
                $i++;
                $content_photo = $this->view->itemPhoto($usersiteevent, 'thumb.icon');
                $data[] = array(
                    'id' => $usersiteevent->event_id,
                    'label' => $usersiteevent->title,
                    'photo' => $content_photo,
                    'siteevent_url' => $siteevent_url,
                    'total_count' => $count,
                    'count' => $i
                );
            }
        } else {
            $i = 0;
            foreach ($usersiteevents as $usersiteevent) {
                $siteevent_url = $this->view->url(array('event_id' => $usersiteevent->event_id, 'slug' => $usersiteevent->getSlug()), "siteevent_entry_view", true);
                $content_photo = $this->view->itemPhoto($usersiteevent, 'thumb.icon');
                $i++;
                $data[] = array(
                    'id' => $usersiteevent->event_id,
                    'label' => $usersiteevent->title,
                    'photo' => $content_photo,
                    'siteevent_url' => $siteevent_url,
                    'total_count' => $count,
                    'count' => $i
                );
            }
        }
        if (!empty($data) && $i >= 1) {
            if ($data[--$i]['count'] == $count) {
                $data[$count]['id'] = 'stopevent';
                $data[$count]['label'] = $this->_getParam('text');
                $data[$count]['siteevent_url'] = 'seeMoreLink';
                $data[$count]['total_count'] = $count;
            }
        }
        return $this->_helper->json($data);
    }

    //ACTION FOR MESSAGING THE EVENT OWNER
    public function messageownerAction() {

        //LOGGED IN USER CAN SEND THE MESSAGE
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET EVENT ID AND OBJECT
        $event_id = $this->_getParam("event_id");
        $event = Engine_Api::_()->getItem('siteevent_event', $event_id);

        //OWNER CANT SEND A MESSAGE TO HIMSELF
        //GET THE ORGANIZER ID TO WHOM THE MESSAGE HAS TO BE SEND
        $organizer_id = $this->_getParam("host_id");

        $leader_id = 0;
        if (empty($organizer_id)) {
            $leader_id = $organizer_id = $this->_getParam("leader_id");
        }

        if ($viewer_id == $organizer_id) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        if (empty($organizer_id)) {
            $organizer_id = $event->owner_id;
        }

        //MAKE FORM
        $this->view->form = $form = new Messages_Form_Compose();

        if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
            $this->_helper->layout->setLayout('default');
            $this->_setParam('contentType', 'page');
            Zend_Registry::set('setFixedCreationForm', true);
            Zend_Registry::set('setFixedCreationFormBack', 'Back');
            Zend_Registry::set('setFixedCreationHeaderTitle', Zend_Registry::get('Zend_Translate')->_($form->getTitle()));
            Zend_Registry::set('setFixedCreationHeaderSubmit', Zend_Registry::get('Zend_Translate')->_('Send'));
            $this->view->form->setAttrib('id', 'messageOwnerEventSR');
            Zend_Registry::set('setFixedCreationFormId', '#messageOwnerEventSR');
            $this->view->form->removeElement('submit');
            $this->view->form->removeElement('cancel');
            $form->setTitle(sprintf(Zend_Registry::get('Zend_Translate')->_('To: %s'), $event->getOwner()->getTitle()));
            $form->toValues->setLabel('');
        }
        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode'))
            $form->toValues->setLabel('');

        if ($leader_id) {
            $form->setDescription('Create your message with the form given below. (This message will be sent to the leader of this event which you want to send a message.)');
        } else {
            $form->setDescription('Create your message with the form given below. (This message will be sent to the host of this event.)');
        }
        $form->removeElement('to');
        $form->toValues->setValue($organizer_id);
        $values = $this->getRequest()->getPost();
//        $form->populate($values);
        //CHECK METHOD/DATA
        if (!$this->getRequest()->isPost()) {
            return;
        }

        $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
        $db->beginTransaction();

        try {

            $is_error = 0;
            if (empty($values['title'])) {
                $is_error = 1;
            }

            //SENDING MESSAGE
            if ($is_error == 1) {
                $error = $this->view->translate('Subject is required field !');
                $error = Zend_Registry::get('Zend_Translate')->_($error);

                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }

            $recipients = preg_split('/[,. ]+/', $values['toValues']);

            //LIMIT RECIPIENTS IF IT IS NOT A SPECIAL SITEEVENT OF MEMBERS
            $recipients = array_slice($recipients, 0, 1000);

            //CLEAN THE RECIPIENTS FOR REPEATING IDS
            $recipients = array_unique($recipients);

            $user = Engine_Api::_()->getItem('user', $organizer_id);

            $event_title = $event->title;
            $http = _ENGINE_SSL ? 'https://' : 'http://';
            $event_title_with_link = '<a href =' . $http . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('event_id' => $event_id, 'slug' => $event->getSlug()), "siteevent_entry_view") . ">$event_title</a>";

            $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send($viewer, $recipients, $values['title'], $values['body'] . "<br><br>" . $this->view->translate('This message corresponds to the Event: %s', $event_title_with_link));

            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $conversation, 'message_new');

            //INCREMENT MESSAGE COUNTER
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

            $db->commit();

            return $this->_forwardCustom('success', 'utility', 'core', array(
                        'smoothboxClose' => true,
                        'parentRefresh' => true,
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.'))
            ));
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    //ACTION FOR EDITING THE NOTE
    public function displayAction() {

        //GET TEXT AND EVENT ID
        $text = $this->_getParam('strr');
        $subjectType = $this->_getParam('subjectType');
        $subjectId = $this->_getParam('subjectId');

        if ($subjectType == 'siteevent_event') {
            Engine_Api::_()->getDbTable('otherinfo', 'siteevent')->update(array('about' => $text), array('event_id = ?' => $subjectId));
        } else {
            Engine_Api::_()->getDbTable('editors', 'siteevent')->update(array('about' => $text), array('user_id = ?' => $subjectId));
        }

        exit();
    }

    //ACTION FOR UPLOADING IMAGES THROUGH WYSIWYG EDITOR
    public function uploadPhotoAction() {

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        $this->_helper->layout->disableLayout();

        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album')) {
            return false;
        }

        if (!Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'create')) {
            return false;
        }

        if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'create')->isValid())
            return;

        if (!$this->_helper->requireUser()->checkRequire()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }
        $fileName = Engine_Api::_()->seaocore()->tinymceEditorPhotoUploadedFileName();
        if (!isset($_FILES[$fileName]) || !is_uploaded_file($_FILES[$fileName]['tmp_name'])) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
            return;
        }

        $db = Engine_Api::_()->getDbtable('photos', 'album')->getAdapter();
        $db->beginTransaction();

        try {
            $viewer = Engine_Api::_()->user()->getViewer();

            $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
            $photo = $photoTable->createRow();
            $photo->setFromArray(array(
                'owner_type' => 'user',
                'owner_id' => $viewer->getIdentity()
            ));
            $photo->save();

            $photo->setPhoto($_FILES[$fileName]);

            $this->view->status = true;
            $this->view->name = $_FILES[$fileName]['name'];
            $this->view->photo_id = $photo->photo_id;
            $this->view->photo_url = $photo->getPhotoUrl();

            $table = Engine_Api::_()->getDbtable('albums', 'album');
            $album = $table->getSpecialAlbum($viewer, 'message');

            $photo->album_id = $album->album_id;
            $photo->save();

            if (!$album->photo_id) {
                $album->photo_id = $photo->getIdentity();
                $album->save();
            }

            $auth = Engine_Api::_()->authorization()->context;
            $auth->setAllowed($photo, 'everyone', 'view', true);
            $auth->setAllowed($photo, 'everyone', 'comment', true);
            $auth->setAllowed($album, 'everyone', 'view', true);
            $auth->setAllowed($album, 'everyone', 'comment', true);

            $db->commit();
        } catch (Album_Model_Exception $e) {
            $db->rollBack();
            $this->view->status = false;
            $this->view->error = $this->view->translate($e->getMessage());
            throw $e;
            return;
        } catch (Exception $e) {
            $db->rollBack();
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
            throw $e;
            return;
        }
    }

    public function removeAdsWidgetAction() {

        $content_id = $this->_getParam('content_id', 0);
        if ($content_id) {
            Zend_Db_Table::getDefaultAdapter()->query("DELETE FROM engine4_core_content WHERE content_id = $content_id;");
        }
    }

    //ACTION FOR GETTING THE MEMBER
    function getHostSuggestAction() {

        //GET SETTINGS

        $text = $this->_getParam('text');
        $type = $this->_getParam('type');
        $limit = $this->_getParam('limit', 40);

        //FETCH USER LIST
        $items = Engine_Api::_()->getDbTable('events', 'siteevent')->getHostsSuggest($type, $text, $limit);

        //MAKING DATA
        $data = array();
        $mode = $this->_getParam('struct');

        foreach ($items as $item) {
            $content_photo = $this->view->itemPhoto($item, 'thumb.icon');
            $link = $this->view->htmlLink($item->getHref(), $item->getTitle(), array('target' => '_blank'));
            $data[] = array('id' => $item->getIdentity(), 'label' => $item->getTitle(), 'photo' => $content_photo, 'link' => $link);
        }

        return $this->_helper->json($data);
    }

    public function addToMyCalendarAction() {
        echo "<b>Comming Soon.....</b>";
        die;
    }

    //ADD OR EDIT ROWS IN REPEAT DATES TABLE


    public function addorEditDates($postedValues, $values, $event_id, $action = 'create') {
        try {

            //SPECIAL CASE: IF THIS FUNCTION IS CALLED BY ADDMOREOCCURRENCE FUNCTION FOR ADDING MORE OCCURRENCES THEN WE WILL NOT SET USER TIME ZONE WHEN INSERTING THE DATE ENTRY IN DATABASE.
            $useTimezone = true;
            if (isset($postedValues['useTimezone']))
                $useTimezone = $postedValues['useTimezone'];
            $occure_id = '';
            if (!isset($values['starttime']) && isset($postedValues['starttime']))
                $values['starttime'] = Engine_Api::_()->siteevent()->convertDateFormat($postedValues['starttime']);
            $viewer = Engine_Api::_()->user()->getViewer();
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
                                }
                                else
                                    $day_decrease = 0;

                                if ($day_decrease != 0) {
                                    $nextStartDate = Engine_Api::_()->siteevent()->date_add(date("Y-m-d H:i:s", strtotime($lastDateofMonth)), $day_decrease, 0);
                                } else {e;
                                    $nextStartDate = $lastDateofMonth;
                                }
                                
                            }
                            else {

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

            //$nextEndDate = date("Y-m-d H:i:s", strtotime($occurrence->starttime) + $durationDiff);
            try {
                $viewer = Engine_Api::_()->user()->getViewer();
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

    public function setEventInfo($params = array(), $useTimezone = true) {
        $this->_occurrencesCount++;
        if ($this->_occurrencesCount <= Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.occurrencecount', 15) || (isset($_POST['eventrepeat_id']) && $_POST['eventrepeat_id'] == 'custom')) {
            try {
                $viewer = Engine_Api::_()->user()->getViewer();
                $tableOccurence = Engine_Api::_()->getDbtable('occurrences', 'siteevent');

                $row_occurrence = $tableOccurence->createRow();
                $dateInfo = Engine_Api::_()->siteevent()->userToDbDateTime(array('starttime' => Engine_Api::_()->siteevent()->convertDateFormat($params['nextStartDate']), 'endtime' => Engine_Api::_()->siteevent()->convertDateFormat($params['nextEndDate'])));
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
                    Zend_Registry::set('occurrence_id', $occure_id);
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

    //GET THE INVITED EVENT LIST FOR LOGGED IN USER

    public function getInvitedListEventsAction() {

        $this->view->list_type = $list_type = $this->_getParam('type', 'popup');
        $this->view->invite_count = $this->_getParam('invite_count', 0);
        $event_id = $this->_getParam('event_id', 0);
        $viewer = Engine_Api::_()->user()->getViewer();
        $siteeventTable = Engine_Api::_()->getDbtable('events', 'siteevent');
        $siteeventTableName = $siteeventTable->info('name');

        $occurrenceTable = Engine_Api::_()->getDbtable('occurrences', 'siteevent');
        $occurrenceTableName = $occurrenceTable->info('name');

        $membershipTable = Engine_Api::_()->getDbtable('membership', 'siteevent');
        $membershipTableName = $membershipTable->info('name');

        $select = $siteeventTable->select();
        $select = $select
                ->setIntegrityCheck(false)
                ->from($siteeventTableName)
                ->join($occurrenceTableName, "$occurrenceTableName.event_id = $siteeventTableName.event_id", array('starttime', 'endtime'))
                ->join($membershipTableName, "$membershipTableName.occurrence_id = $occurrenceTableName.occurrence_id", array('rsvp', 'occurrence_id'))
                ->where($membershipTableName . ".user_id =?", $viewer->getIdentity())
                ->where($membershipTableName . ".resource_id <>?", $event_id)
                ->where($membershipTableName . ".rsvp =?", 3)
                ->where($membershipTableName . ".active =?", 0)
                ->where($membershipTableName . ".user_approved =?", 0);
        if ($list_type == 'calendar')
            $select->limit(3);

        $this->view->results = $siteeventTable->fetchAll($select);
    }

    //SHOWS THE GUEST LIST WHO HAS JOINED THE EVENT.

    public function guestListAction() {
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

        //GET USER LEVEL ID
        if ($viewer->getIdentity()) {
            $this->view->level_id = $viewer->level_id;
        } else {
            $this->view->level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        // Get subject and check auth
        $subject = Engine_Api::_()->core()->getSubject('siteevent_event');
        $this->view->friendsonly = $friendsonly = $this->_getParam('friendsonly', 0);
       
//        if (!$subject->canView($viewer)) {
//            return;
//        }
        $this->view->canEdit = $subject->authorization()->isAllowed($viewer, "edit");
        $this->view->list = $list = $subject->getLeaderList();
        $this->view->datesInfo = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getAllOccurrenceDates($subject->event_id);
        $this->view->page = $page = $this->_getParam('page', 1);
        $this->view->rsvp = $rsvp = $this->_getParam('rsvp', -1);
        $this->view->search = $search = $this->_getParam('search');
        $this->view->occurrence_id = $occurrence_id = $this->_getParam('occurrence_id', 'all');

        if (empty($occurrence_id)) {
            $this->view->occurrence_id = $occurrence_id = Zend_Registry::isRegistered('occurrence_id') ? Zend_Registry::get('occurrence_id') : null;
        }


        $this->view->event = $event = Engine_Api::_()->core()->getSubject();
        $select = $event->membership()->getMembersObjectSelect();

        if (isset($rsvp) && $rsvp >= 0) {


            $select->where("rsvp=?", $rsvp);
        }
        if (!empty($occurrence_id) && $occurrence_id != 'all')
            $select->where("occurrence_id=?", $occurrence_id);
        else
            $this->view->occurrence_id = '';
        //IF REQUEST IS ONLY TO SHOW VIEWER FRIENDS THEN ALSO PUT THE JOIN WITH USER MEMBERSHIP TABLE.
        if ($friendsonly) {
            $membershipTable = Engine_Api::_()->getDbtable('membership', 'user');
            $membershipEventTableName = 'engine4_siteevent_membership';
            $membershipTableName = $membershipTable->info('name');
            $select->join($membershipTableName, "$membershipTableName.resource_id = $membershipEventTableName.user_id", null)
                    ->where($membershipTableName . '.user_id = ?', $viewer->getIdentity())
                    ->where($membershipTableName . '.active = ?', 1)
                    ->where('engine4_users.verified = ?', 1)
                    ->where('engine4_users.enabled = ?', 1);
        }


        $select->group('engine4_users.user_id');
        
        $this->view->members = $members = Zend_Paginator::factory($select);
        $paginator = $members;

        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 20));
        $paginator->setCurrentPageNumber($this->_getParam('page', $page));
        $this->view->no_result_msg = $this->view->translate('No results were found.');
    }

    public function notificationsAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->_helper->layout->setLayout('default-simple');

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
        Zend_Registry::set('occurrence_id', $occurrence_id);
        //GET THE LEADERS LIST AND CHECK IF THE VIEWER IS LEADER OR NORMAL USER.
        if ($siteevent->owner_id == $viewer->getIdentity()) {
            $isLeader = 1;
        } else {
            $list = $siteevent->getLeaderList();
            $listItem = $list->get($viewer);
            $isLeader = ( null !== $listItem );
        }

        $row = Engine_Api::_()->getDbTable('membership', 'siteevent')->getRow($siteevent, $viewer);

        if(!$row) {
            $row->notification = Zend_Json_Decoder::decode('{"email":"0","notification":"1","action_notification":["posted","created","joined","comment","like","follow","rsvp"],"action_email":["posted","created","joined","rsvp"]}');
        }
        
        //SET FORM
        $this->view->form = $form = new Siteevent_Form_Notifications(array('isLeader' => $isLeader));
        $this->view->notification = $row->notification['notification'];
        $this->view->email = $row->notification['email'];
        $form->populate($row->notification);

        //CHECK FORM VALIDATION
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            //GET FORM VALUES
            $values = $form->getValues();
            Engine_Api::_()->getDbtable('membership', 'siteevent')->update(array('notification' => $values), array('resource_id =?' => $event_id, 'user_id =?' => $row->user_id));
            $this->view->notification = $values['notification'];
            $this->view->email = $values['email'];
            return $this->_forwardCustom('success', 'utility', 'core', array(
                        'smoothboxClose' => true,
                        'parentRefresh' => true,
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your Notification settings have been saved successfully.'))
            ));
        }
    }

    //ADD MORE OCCURRENCES IF THE EVENT HAS FROM THE BACKEND WHEN SOMEONE VISIT THE EVENT PROFILE PAGE.
    public function addMoreOccurrences() {
        $siteevent = Engine_Api::_()->core()->getSubject();
        //FIRST WE WILL CHECK THAT IF NO OF OCCURRENCES ARE LESS THEN THE NO OF OCCURRENCE SET BY ADMIN THEN THERE WILL NOT BE ANY NEW OCCURRENCES SAVED IN THE DATABASE.
        //check if more occurrences exist.

        if (!empty($siteevent->repeat_params)) {
            $repeat_params = json_decode($siteevent->repeat_params);

            if ($repeat_params->eventrepeat_type != 'custom') {
                //END REPEAT TIME SHOULD BE GREATE THEN CURRENT TIME.
                if (date("Ymd", strtotime($repeat_params->endtime->date)) > date("Ymd", time())) {
                    //GET THE COUNT OF REMAINING ROWS FROM CURRENT TIME. IF THE TOTAL REMAINING ROWS ARE LESS THEN OR EQUAL TO 4 THEN WE WILL CREATE NEW OCCURRENCES ROWS.
                    $Remaining_Rows_Count = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getOccurrenceCount($siteevent->event_id, array('upcomingOccurrences' => 1));


                    if ($Remaining_Rows_Count > 4)
                        return;
                    //LAST OCCURRENCE ROW START TIME SHOULD BE LESS THEN THE END REPEAT TIME.
                    $last_Row = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getOccurenceStartEndDate($siteevent->event_id, 'DESC');

                    //CONVERT DB TO USER DATE
                    $dateInfo = Engine_Api::_()->siteevent()->dbToUserDateTime(array('starttime' => $last_Row->starttime, 'endtime' => $last_Row->endtime));
                    $start = strtotime($dateInfo['starttime']);
                    $end = strtotime($dateInfo['endtime']);
                    $currentOccurrenceEndStart = date('Ymd', $start);
                    $starttime = date('Y-m-d H:i:s', $start);
                    $endtime = date('Y-m-d H:i:s', $end);
                    $endRepeatTime = date("Ymd", strtotime($repeat_params->endtime->date));
                    $currentdatetime = date("Ymd", time());
                    //date_default_timezone_set($oldTz);
                    if ($currentOccurrenceEndStart < $endRepeatTime) {
                        //NOW CHECK IF THE 4TH LAST ROW START TIME IS LESS THEN THE CRRENT TIME.
                        $values['eventrepeat_id'] = $repeat_params->eventrepeat_type;
                        $values['starttime'] = $starttime;
                        $values['endtime'] = $endtime;
                        $postedvalues['action'] = 'editdates';
                        $postedvalues[$values['eventrepeat_id'] . '_repeat_time']['date'] = $repeat_params->endtime->date;
                        if ($values['eventrepeat_id'] == 'daily')
                            $postedvalues['daily-repeat_interval'] = ($repeat_params->repeat_interval / (24 * 60 * 60));
                        elseif ($values['eventrepeat_id'] == 'weekly') {
                            $weekdays = array(1 => 'monday', 2 => 'tuesday', 3 => 'wednesday', 4 => 'thursday', 5 => 'friday', 6 => 'saturday', 7 => 'sunday');
                            $postedvalues['id_weekly-repeat_interval'] = $repeat_params->repeat_week;
                            foreach ($repeat_params->repeat_weekday as $weekday) {
                                $postedvalues['weekly-repeat_on_' . $weekdays[$weekday]] = 1;
                            }
                        } elseif ($values['eventrepeat_id'] == 'monthly') {
                            $noOfWeeks = array('first' => 1, 'second' => 2, 'third' => 3, 'fourth' => 4, 'fifth' => 5, 'last' => 6);
                            $dayOfWeeks = array('monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 'friday' => 5, 'saturday' => 6, 'sunday' => 7);

                            $postedvalues['monthly_day'] = 'absolute_day';
                            $postedvalues['id_monthly-repeat_interval'] = $repeat_params->repeat_month;
                            if (isset($repeat_params->repeat_week)) {
                                $postedvalues['id_monthly-relative_day'] = array_search($repeat_params->repeat_week, $noOfWeeks);
                                $postedvalues['monthly_day'] = 'relative_weekday';
                            }
                            if (isset($repeat_params->repeat_weekday))
                                $postedvalues['id_monthly-day_of_week'] = array_search($repeat_params->repeat_weekday, $dayOfWeeks);
                            if (isset($repeat_params->repeat_day))
                                $postedvalues['id_monthly-absolute_day'] = $repeat_params->repeat_day;
                        }
                        $postedvalues['useTimezone'] = false;
                        $this->addorEditDates($postedvalues, $values, $siteevent->event_id, 'create');
                        // }
                    }
                }
            }
        }
    }

    //RETURNS THE LOGGEDIN USER DATE TIME FORMAT BASED ON HIS LOCALE.
    public function getDateFormatAction() {
        $dateFormat = $this->view->locale()->useDateLocaleFormat();
        $data['dateFormat'] = $dateFormat;
        return $this->_helper->json($data);
    }

    //VIEW PAGE CALENDAR - ADDED FOR MOBILE SITE
    public function calendarAction() {
        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $this->_helper->content
                    ->setContentName("siteevent_index_calender")
                    ->setNoRender()
                    ->setEnabled();
        } else {
            $this->_helper->content->setNoRender()->setEnabled();
        }
    }

    public function showRadiusTipAction() {
        $this->_helper->layout->setLayout('default-simple');
    }

}
