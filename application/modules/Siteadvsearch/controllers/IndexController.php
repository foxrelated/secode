<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteadvsearch_IndexController extends Core_Controller_Action_Standard {

    public function indexAction() {

        $siteadvsearch_setRender = Zend_Registry::isRegistered('siteadvsearch_setRender') ? Zend_Registry::get('siteadvsearch_setRender') : null;
        if (!empty($siteadvsearch_setRender)) {
            $this->_helper->content
                    ->setNoRender()
                    ->setEnabled();
        }
    }

    //ACTION FOR SHOWING WIDGETIZE PAGE CORRESPONDING CONTENT TYPE
    public function browsePageAction() {

        $listingtype_id = $this->_getParam('listingtype_id', null);
        $resourceType = $this->_getParam('resource_type', null);
        $siteadvsearch_browsePage = Zend_Registry::isRegistered('siteadvsearch_browsePage') ? Zend_Registry::get('siteadvsearch_browsePage') : null;

        if (!empty($listingtype_id))
            $widgetizePageName = "siteadvsearch_index_browse-page_listtype_$listingtype_id";
        elseif (!empty($resourceType))
            $widgetizePageName = "siteadvsearch_index_browse-page_$resourceType";

        if (!empty($siteadvsearch_browsePage)) {
            $this->_helper->content
                    ->setContentName($widgetizePageName)
                    ->setNoRender()
                    ->setEnabled();
        }
    }

    //ACTION FOR SHOWING THE MEMBERS LIST
    public function browseMemberAction() {

        $info = Engine_Api::_()->siteadvsearch()->getWidgetInfo();
        $this->view->widgetizePage = $widgetizePage = 0;
        if (!empty($info) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemember')) {
            $this->view->widgetizePage = $widgetizePage = 1;
        }

        if ($widgetizePage) {
            $this->_helper->content->setEnabled();
        } else {
            $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.browse', 1);
            if (!$require_check) {
                if (!$this->_helper->requireUser()->isValid())
                    return;
            }
            if (!$this->_executeSearch()) {
                // throw new Exception('error');
            }

            if ($this->_getParam('ajax')) {
                $this->renderScript('_browseUsers.tpl');
            }
        }
    }

    protected function _executeSearch() {

        $this->view->is_ajax = $this->_getParam('isajax', 0);
        $this->view->showContent = Engine_Api::_()->siteadvsearch()->getpaginationTypeValue();
        $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();

        // Check form
        $form = new User_Form_Search(array(
            'type' => 'user'
        ));

        if (!$form->isValid($this->_getAllParams())) {
            $this->view->error = true;
            $this->view->totalUsers = 0;
            $this->view->userCount = 0;
            $this->view->page = 1;
            return false;
        }

        $this->view->form = $form;

        // Get search params
        if (isset($params['page']) && !empty($params['page']))
            $page = $params['page'];
        else
            $page = 1;

        $ajax = (bool) $this->_getParam('ajax', false);
        $options = $form->getValues();

        // Process options
        $tmp = array();
        $originalOptions = $options;
        foreach ($options as $k => $v) {
            if (null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0)) {
                continue;
            } else if (false !== strpos($k, '_field_')) {
                list($null, $field) = explode('_field_', $k);
                $tmp['field_' . $field] = $v;
            } else if (false !== strpos($k, '_alias_')) {
                list($null, $alias) = explode('_alias_', $k);
                $tmp[$alias] = $v;
            } else {
                $tmp[$k] = $v;
            }
        }
        $options = $tmp;

        // Get table info
        $table = Engine_Api::_()->getItemTable('user');
        $userTableName = $table->info('name');

        $searchTable = Engine_Api::_()->fields()->getTable('user', 'search');
        $searchTableName = $searchTable->info('name');

        //extract($options); // displayname
        $profile_type = @$options['profile_type'];
        $displayname = @$options['displayname'];
        if (!empty($options['extra'])) {
            extract($options['extra']); // is_online, has_photo, submit
        }

        // Contruct query
        $select = $table->select()
                //->setIntegrityCheck(false)
                ->from($userTableName)
                ->joinLeft($searchTableName, "`{$searchTableName}`.`item_id` = `{$userTableName}`.`user_id`", null)
                //->group("{$userTableName}.user_id")
                ->where("{$userTableName}.search = ?", 1)
                ->where("{$userTableName}.enabled = ?", 1);

        $searchDefault = true;

        // Build the photo and is online part of query
        if (isset($has_photo) && !empty($has_photo)) {
            $select->where($userTableName . '.photo_id != ?', "0");
            $searchDefault = false;
        }

        if (isset($is_online) && !empty($is_online)) {
            $select
                    ->joinRight("engine4_user_online", "engine4_user_online.user_id = `{$userTableName}`.user_id", null)
                    ->group("engine4_user_online.user_id")
                    ->where($userTableName . '.user_id != ?', "0");
            $searchDefault = false;
        }

        // Add displayname
        if (!empty($displayname)) {
            $select->where("(`{$userTableName}`.`username` LIKE ? || `{$userTableName}`.`displayname` LIKE ?)", "%{$displayname}%");
            $searchDefault = false;
        }

        // Build search part of query
        $searchParts = Engine_Api::_()->fields()->getSearchQuery('user', $options);
        foreach ($searchParts as $k => $v) {
            $select->where("`{$searchTableName}`.{$k}", $v);

            if (isset($v) && $v != "") {
                $searchDefault = false;
            }
        }

        if ($searchDefault) {
            $select->order("{$userTableName}.lastlogin_date DESC");
        } else {
            $select->order("{$userTableName}.displayname ASC");
        }

        // Build paginator
        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber($page);

        $this->view->page = $page;
        $this->view->ajax = $ajax;
        $this->view->paginator = $paginator;
        $this->view->totalUsers = $paginator->getTotalItemCount();
        $this->view->totalCount = $paginator->getTotalItemCount();
        $this->view->userCount = $paginator->getCurrentItemCount();
        $this->view->topLevelId = $form->getTopLevelId();
        $this->view->topLevelValue = $form->getTopLevelValue();
        $this->view->formValues = array_filter($originalOptions);
        $this->view->params = $params;

        return true;
    }

    //ACTION FOR GET THE SEARCH RESULT BASED ON CORE SEARCH TABLE
    public function getSearchResultAction() {

        $values = array();
        $values['text'] = $this->_getParam('text', null);
        $values['searchLocation'] = $this->_getParam('searchLocation', null);
        $values['pagination'] = '';
        $values['resource_type'] = '';
        $values['limit'] = 6;
        $setContentItems = Engine_Api::_()->siteadvsearch()->setContentItems();
        $items = Engine_Api::_()->siteadvsearch()->getCoreSearchData($values);
        $count = count($items);
        $siteadvsearch_searchResult = Zend_Registry::isRegistered('siteadvsearch_searchResult') ? Zend_Registry::get('siteadvsearch_searchResult') : null;
        $dataSearchable = array();
        $i = 0;

        if (empty($setContentItems) && !empty($siteadvsearch_searchResult)) {
            foreach ($items as $item) {
                $type = $item->type;
                if (!Engine_Api::_()->hasItemType($type))
                    continue;

                $item = $this->view->item($type, $item->id);
                if (count($item)) {
                    if ($item->getPhotoUrl() != '')
                        $content_photo = $this->view->itemPhoto($item, 'thumb.icon');
                    else
                        $content_photo = "<img src='" . $this->view->layout()->staticBaseUrl . "application/modules/Siteadvsearch/externals/images/nophoto_icon.png' alt='' />";

//            if(isset($item->listingtype_id))
//            $listingTypeId = $item->listingtype_id;
//            else
//            $listingTypeId = 0;
//            
//            $resourceTitle = Engine_Api::_()->getDbTable('contents', 'siteadvsearch')->getResourceTitle($item->getType(), $listingTypeId);
//            if(empty($resourceTitle))
//                $resourceTitle = $item->getShortType();
                    $resourceTitle = $item->getShortType();
                    if ($item->getShortType() == 'user') {
                        $resourceTitle = 'member';
                    }

                    if (is_null($item->getTitle()))
                        continue;

                    $i++;

                    if ($i > 5)
                        continue;
                    $dataSearchable[] = array(
                        'label' => $item->getTitle(),
                        'type' => $this->view->translate(ucfirst($resourceTitle)),
                        'photo' => $content_photo,
                        'item_url' => $item->getHref(),
                        'total_count' => $count,
                        'count' => $i
                    );
                }
            }

            if ($i > 5) {
                $dataSearchable[] = array(
                    'id' => 'stopevent',
                    'label' => $this->_getParam('text'),
                    'item_url' => 'seeMoreLink',
                    'total_count' => $count,
                );
            }
        }
        return $this->_helper->json($dataSearchable);
    }

    //ACTION FOR GET THE SEARCH RESULT BASED ON CORE SEARCH TABLE
    public function getSearchContentAction() {

        //GET SEARCHABLE TEXT FROM GLOBAL SEARCH BOX
        $text = $this->_getParam('text', null);
        $pos = strpos($text, '#');
        if (!empty($text)) {
            $values = array();
            $values['text'] = $text;
            $values['searchLocation'] = $this->_getParam('searchLocation', null);
            if (empty($values['searchLocation']) && $this->_getParam('showLocationBasedContent')) {
                //$myLocationDetails = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
                //if(!empty($myLocationDetails['location'])) {
                $values['searchLocation'] = $this->_getParam('searchLocationValue', null);
                //}
            }

            $values['pagination'] = '';
            $values['resource_type'] = '';
            $values['limit'] = $this->_getParam('limit');

            $items = Engine_Api::_()->siteadvsearch()->getCoreSearchData($values);
            $countSearchableResult = count($items);

            $countDefaultContent = Engine_Api::_()->getDbtable('contents', 'siteadvsearch')->getContentTypes(1);

            //START PRIVACY WORK FOR SHOWING CONTENT TYPE TO NON-LOGGED IN USERS
            $i = 0;

            foreach ($countDefaultContent as $contentType) {
                if (Engine_Api::_()->hasModuleBootstrap('sitehashtag') && $contentType->resource_type == 'sitehashtag_hashtag' && $pos !== 0)
                    continue;
                $privacyCheck = Engine_Api::_()->siteadvsearch()->canViewItemType($contentType->resource_type, $contentType->listingtype_id);
                if (empty($privacyCheck))
                    continue;
                $i++;
            }
            //END PRIVACY WORK

            $countDefaultContent = $i;
            $count = $countSearchableResult + $countDefaultContent;
        }

        $siteadvsearch_searchContent = Zend_Registry::isRegistered('siteadvsearch_searchContent') ? Zend_Registry::get('siteadvsearch_searchContent') : null;
        $setContentItems = Engine_Api::_()->siteadvsearch()->setContentItems();
        $data = array();
        $dataSearchable = array();
        $i = 0;

        if (empty($setContentItems) && !empty($siteadvsearch_searchContent)) {
            if (!empty($text)) {
                foreach ($items as $item) {

                    $type = $item->type;
                    if (!Engine_Api::_()->hasItemType($type)) {
                        continue;
                    }
                    $item = $this->view->item($type, $item->id);
                    if (count($item)) {
                        if ($item->getPhotoUrl() != '')
                            $content_photo = $this->view->itemPhoto($item, 'thumb.icon');
                        else
                            $content_photo = "<img src='" . $this->view->layout()->staticBaseUrl . "application/modules/Siteadvsearch/externals/images/nophoto_icon.png' alt='' />";

                        $i++;

//            if(isset($item->listingtype_id))
//            $listingTypeId = $item->listingtype_id;
//            else
//            $listingTypeId = 0;
//            
//            $resourceTitle = Engine_Api::_()->getDbTable('contents', 'siteadvsearch')->getResourceTitle($item->getType(), $listingTypeId);
//            if(empty($resourceTitle))
//                $resourceTitle = $item->getShortType();

                        $resourceTitle = $item->getShortType();
                        if ($item->getShortType() == 'user') {
                            $resourceTitle = 'member';
                        }
                        $iType = $this->view->translate(ucfirst($resourceTitle));
                        if (is_array($iType) && isset($iType[0])) {
                            $iType = $iType[0];
                        }
                        $dataSearchable[] = array(
                            'label' => $item->getTitle(),
                            'type' => $iType,
                            'photo' => $content_photo,
                            'item_url' => $item->getHref(),
                            'total_count' => $count,
                            'count' => $i
                        );
                    }
                }

                $realCount = $i;
                $data = $this->contentModules($contentType = 1, $i, $count, $text);
                $data = array_merge($dataSearchable, $data);
                $i = $data['total_result'];
                unset($data['total_result']);

                $count = $realCount + $countDefaultContent;
                $data[$count]['id'] = 'stopevent';
                $data[$count]['label'] = $this->_getParam('text');
                $data[$count]['item_url'] = 'seeMoreLink';
                $data[$count]['total_count'] = $count;
            } else {
                $data = $this->contentModules($contentType = 2, $i, $count);
                unset($data['total_result']);
            }
        }

        return $this->_helper->json($data);
    }

    //ACTION FOR SHOWING ALL CONTENT BASED ON CORE SEARCH TABLE
    public function showContentAction() {

        $this->view->viewer = Engine_Api::_()->user()->getViewer();
        $this->view->is_ajax = $this->_getParam('isajax', 0);
        $this->view->statstics = Engine_Api::_()->siteadvsearch()->getContentTypeOptions();
        $this->view->showContent = Engine_Api::_()->siteadvsearch()->getpaginationTypeValue();
        $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();

        $resourceType = $params['resource_type'] = $this->_getParam('resource_type', null);
        $this->view->text = $text = $params['search'] = $_GET['search'];

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('feedback') && $resourceType == 'feedback')
            $this->view->browse_url = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('check_browse_id' => 1), 'feedback_browse');

        if (isset($params['page']) && !empty($params['page']))
            $page = $params['page'];
        else
            $page = 1;

        $values = array();
        $values['text'] = $text;
        $values['searchLocation'] = isset($_GET['searchLocation']) ? $_GET['searchLocation'] : '';
        if (empty($values['searchLocation']) && $this->_getParam('showLocationBasedContent')) {
            $myLocationDetails = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
            if (!empty($myLocationDetails['location'])) {
                $values['searchLocation'] = $myLocationDetails['location'];
            }
        }

        $values['pagination'] = 1;
        $values['resource_type'] = $resourceType;
        $values['limit'] = '';
        $this->view->paginator = $paginator = Engine_Api::_()->siteadvsearch()->getCoreSearchData($values);

        if (!empty($text)) {
            $this->view->totalCount = $paginator->getTotalItemCount();
            $paginator->setItemCountPerPage(10);
            $paginator->setCurrentPageNumber($page);
        }
        $this->view->params = $params;
    }

    //FUNCTION FOR SHOWING CONTENT TYPE ENBLED FROM MANAGE MODULES AT ADMIN SIDE
    public function contentModules($contentType, $i, $count, $text = NULL) {

        if ($contentType == 1)
            $j = $i;
        else
            $j = 0;
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $items = Engine_Api::_()->getDbtable('contents', 'siteadvsearch')->getContentTypes(2);
        foreach ($items as $item) {
            $pos = strpos($text, '#');

            if (Engine_Api::_()->hasModuleBootstrap('sitehashtag') && $item->resource_type == 'sitehashtag_hashtag' && $pos !== 0) {
                continue;
            }

            //START PRIVACY WORK FOR SHOWING CONTENT TYPE TO NON-LOGGED IN USERS
            $privacyCheck = Engine_Api::_()->siteadvsearch()->canViewItemType($item->resource_type, $item->listingtype_id);
            if (empty($privacyCheck))
                continue;
            //END PRIVACY WORK

            if (!empty($item->file_id)) {
                $photo = Engine_Api::_()->storage()->get($item->file_id, '')->getPhotoUrl();
                $content_photo = "<img src='$photo' alt='' />";
            } else if (Engine_Api::_()->hasModuleBootstrap('sitehashtag') && $item->resource_type == 'sitehashtag_hashtag') {
                $content_photo = "<img src='" . $this->view->layout()->staticBaseUrl . "application/modules/Sitehashtag/externals/images/Hashtag.png' alt='' />";
            } else
                $content_photo = "<img src='" . $this->view->layout()->staticBaseUrl . "application/modules/Siteadvsearch/externals/images/search-icon.png' alt='' />";

            $item->resource_title = $item->resource_title ? $this->view->translate($item->resource_title) : $item->resource_title;

            if ($contentType == 1)
                $label = $this->view->translate('Find all %s with', $item->resource_title) . ' "' . $text . '"';
            else
                $label = $this->view->translate('Find in %s', $item->resource_title);

            $item_url = $this->view->url(array('action' => 'index'), 'siteadvsearch_general', true);
            $item_url .= '?query=' . urlencode($text) . '&type=' . $item->resource_type;
            if (Engine_Api::_()->hasModuleBootstrap('sitehashtag') && $item->resource_type == 'sitehashtag_hashtag')
                $item_url = $view->url(array(), 'sitehashtag_general') . '?search=' . urlencode($text);
            $j++;
            $data[] = array(
                'label' => $label,
                'type' => '',
                'photo' => $content_photo,
                'item_url' => $item_url,
                'total_count' => $count,
                'count' => $j
            );
        }

        $data['total_result'] = $j;
        return $data;
    }

    //ACTION FOR SHOWING THE GROUPS LIST BASED ON SEARCH STRING
    public function browseGroupAction() {

        $viewer = Engine_Api::_()->user()->getViewer();

        $this->view->is_ajax = $this->_getParam('isajax', 0);
        $this->view->showContent = Engine_Api::_()->siteadvsearch()->getpaginationTypeValue();
        $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();

        // Check create
        $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('group', null, 'create');

        // Form
        $this->view->formFilter = $formFilter = new Group_Form_Filter_Browse();
        $defaultValues = $formFilter->getValues();

        if (!$viewer || !$viewer->getIdentity()) {
            $formFilter->removeElement('view');
        }

        // Populate options
        $siteadvsearch_browseGroup = Zend_Registry::isRegistered('siteadvsearch_browseGroup') ? Zend_Registry::get('siteadvsearch_browseGroup') : null;
        $categories = Engine_Api::_()->getDbtable('categories', 'group')->getCategoriesAssoc();
        $formFilter->category_id->addMultiOptions($categories);

        // Populate form data
        if ($formFilter->isValid($this->_getAllParams())) {
            $this->view->formValues = $values = $formFilter->getValues();
        } else {
            $formFilter->populate($defaultValues);
            $this->view->formValues = $values = array();
        }

        // Prepare data
        $this->view->formValues = $values = $formFilter->getValues();

        if ($viewer->getIdentity() && @$values['view'] == 1) {
            $values['users'] = array();
            foreach ($viewer->membership()->getMembersInfo(true) as $memberinfo) {
                $values['users'][] = $memberinfo->user_id;
            }
        }

        $values['search'] = 1;

        // check to see if request is for specific user's listings
        $user_id = $params['user'] = $this->_getParam('user');
        if ($user_id) {
            $values['user_id'] = $user_id;
        }

        if (isset($params['page']) && !empty($params['page']))
            $page = $params['page'];
        else
            $page = 1;

        // Make paginator
        $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('group')
                ->getGroupPaginator($values);
        $this->view->totalCount = $paginator->getTotalItemCount();
        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber($page);
        $this->view->params = $params;

        // Render
        if (!empty($siteadvsearch_browseGroup))
            $this->_helper->content
                    //->setNoRender()
                    ->setEnabled()
            ;
    }

    //ACTION FOR SHOWING THE POLLS LIST BASED ON SEARCH STRING
    public function browsePollAction() {

        // Prepare
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('poll', null, 'create');

        // Get form
        $this->view->form = $form = new Poll_Form_Search();

        $this->view->is_ajax = $this->_getParam('isajax', 0);
        $this->view->showContent = Engine_Api::_()->siteadvsearch()->getpaginationTypeValue();
        $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $siteadvsearch_browsePoll = Zend_Registry::isRegistered('siteadvsearch_browsePoll') ? Zend_Registry::get('siteadvsearch_browsePoll') : null;

        // Process form
        $values = array();
        if ($form->isValid($this->_getAllParams())) {
            $values = $form->getValues();
        }
        $values['browse'] = 1;

        $this->view->formValues = array_filter($values);

        if (@$values['show'] == 2 && $viewer->getIdentity()) {
            // Get an array of friend ids
            $values['users'] = $viewer->membership()->getMembershipsOfIds();
        }
        unset($values['show']);

        if (isset($params['page']) && !empty($params['page']))
            $currentPageNumber = $params['page'];
        else
            $currentPageNumber = 1;

        // Make paginator
        $itemCountPerPage = Engine_Api::_()->getApi('settings', 'core')->getSetting('poll.perPage', 10);

        $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('poll')->getPollsPaginator($values);
        $this->view->totalCount = $paginator->getTotalItemCount();
        $paginator
                ->setItemCountPerPage($itemCountPerPage)
                ->setCurrentPageNumber($currentPageNumber)
        ;
        $this->view->params = $params;

        // Render
        if (!empty($siteadvsearch_browsePoll))
            $this->_helper->content
                    //->setNoRender()
                    ->setEnabled()
            ;
    }

    //ACTION FOR SHOWING THE VIDEOS LIST BASED ON SEARCH STRING
    public function browseVideoAction() {

        // Permissions
        $this->view->can_create = $this->_helper->requireAuth()->setAuthParams('video', null, 'create')->checkRequire();

        // Prepare
        $viewer = Engine_Api::_()->user()->getViewer();

        $this->view->is_ajax = $this->_getParam('isajax', 0);
        $this->view->showContent = Engine_Api::_()->siteadvsearch()->getpaginationTypeValue();
        $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $siteadvsearch_browseVideo = Zend_Registry::isRegistered('siteadvsearch_browseVideo') ? Zend_Registry::get('siteadvsearch_browseVideo') : null;

        // Make form
        // Note: this code is duplicated in the video.browse-search widget
        $this->view->form = $form = new Video_Form_Search();

        // Process form
        if ($form->isValid($this->_getAllParams())) {
            $values = $form->getValues();
        } else {
            $values = array();
        }
        $this->view->formValues = $values;

        $values['status'] = 1;
        $values['search'] = 1;

        $this->view->category = @$values['category'];
        $this->view->text = @$values['text'];


        if (!empty($values['tag'])) {
            $this->view->tag = Engine_Api::_()->getItem('core_tag', $values['tag'])->text;
        }

        // check to see if request is for specific user's listings
        $this->view->user_id = $user_id = $this->_getParam('user');
        if ($user_id) {
            $values['user_id'] = $user_id;
        }

        $page = $params['page'] = $this->_getParam('page', 1);


        // Get videos
        $this->view->paginator = $paginator = Engine_Api::_()->getApi('core', 'video')->getVideosPaginator($values);
        $this->view->totalCount = $paginator->getTotalItemCount();
        $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('video.page', 12);
        $paginator->setItemCountPerPage($items_count);
        $paginator->setCurrentPageNumber($page);
        $this->view->params = $params;

        // Render
        if (!empty($siteadvsearch_browseVideo))
            $this->_helper->content
                    //->setNoRender()
                    ->setEnabled()
            ;
    }

    //ACTION FOR SHOWING THE CLASSIFIEDS LIST BASED ON SEARCH STRING
    public function browseClassifiedAction() {

        // Check auth
        if (!$this->_helper->requireAuth()->setAuthParams('classified', null, 'view')->isValid())
            return;

        $viewer = Engine_Api::_()->user()->getViewer();

        $this->view->can_create = $this->_helper->requireAuth()->setAuthParams('classified', null, 'create')->checkRequire();

        $this->view->is_ajax = $this->_getParam('isajax', 0);
        $this->view->showContent = Engine_Api::_()->siteadvsearch()->getpaginationTypeValue();
        $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $siteadvsearch_browseClassified = Zend_Registry::isRegistered('siteadvsearch_browseClassified') ? Zend_Registry::get('siteadvsearch_browseClassified') : null;

        // Make form
        // Note: this code is duplicated in the video.browse-search widget
        $this->view->form = $form = new Classified_Form_Search();


        if (!$viewer->getIdentity()) {
            $form->removeElement('show');
        }

        // Populate form
        $categories = Engine_Api::_()->getDbtable('categories', 'classified')->getCategoriesAssoc();
        if (!empty($categories) && is_array($categories) && $form->getElement('category')) {
            $form->getElement('category')->addMultiOptions($categories);
        }

        // Process form
        if ($form->isValid($this->_getAllParams())) {
            $values = $form->getValues();
        } else {
            $values = array();
        }
        $this->view->formValues = array_filter($values);

        $customFieldValues = array_intersect_key($values, $form->getFieldElements());

        // Process options
        $tmp = array();
        foreach ($customFieldValues as $k => $v) {
            if (null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0)) {
                continue;
            } else if (false !== strpos($k, '_field_')) {
                list($null, $field) = explode('_field_', $k);
                $tmp['field_' . $field] = $v;
            } else if (false !== strpos($k, '_alias_')) {
                list($null, $alias) = explode('_alias_', $k);
                $tmp[$alias] = $v;
            } else {
                $tmp[$k] = $v;
            }
        }
        $customFieldValues = $tmp;

        // Do the show thingy
        if (@$values['show'] == 2) {
            // Get an array of friend ids to pass to getClassifiedsPaginator
            $table = Engine_Api::_()->getItemTable('user');
            $select = $viewer->membership()->getMembersSelect('user_id');
            $friends = $table->fetchAll($select);
            // Get stuff
            $ids = array();
            foreach ($friends as $friend) {
                $ids[] = $friend->user_id;
            }
            //unset($values['show']);
            $values['users'] = $ids;
        }

        $this->view->user_id = $userId = $this->_getParam('user_id');

        // check to see if request is for specific user's listings
        if (($user_id = $userId)) {
            $values['user_id'] = $user_id;
        }

        $page = $params['page'] = $this->_getParam('page', 1);

        $this->view->assign($values);
        $this->view->params = $params;

        // items needed to show what is being filtered in browse page
        if (!empty($values['tag'])) {
            $this->view->tag_text = Engine_Api::_()->getItem('core_tag', $values['tag'])->text;
        }

        $view = $this->view;
        $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

        $paginator = Engine_Api::_()->getItemTable('classified')->getClassifiedsPaginator($values, $customFieldValues);
        $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('classified.page', 10);
        $paginator->setItemCountPerPage($items_count);
        $this->view->paginator = $paginator->setCurrentPageNumber($page);
        $this->view->totalCount = $paginator->getTotalItemCount();

        if (!empty($values['category'])) {
            $this->view->categoryObject = Engine_Api::_()->getDbtable('categories', 'classified')
                            ->find($values['category'])->current();
        }

        // Render
        if (!empty($siteadvsearch_browseClassified))
            $this->_helper->content
                    //->setNoRender()
                    ->setEnabled()
            ;
    }

    //ACTION FOR SHOWING THE ALBUMS LIST BASED ON SEARCH STRING
    public function browseAlbumAction() {

        if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid())
            return;

        $settings = Engine_Api::_()->getApi('settings', 'core');

        $this->view->is_ajax = $this->_getParam('isajax', 0);
        $this->view->showContent = Engine_Api::_()->siteadvsearch()->getpaginationTypeValue();
        $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $siteadvsearch_browseAlbum = Zend_Registry::isRegistered('siteadvsearch_browseAlbum') ? Zend_Registry::get('siteadvsearch_browseAlbum') : null;

        // Get params
        switch ($this->_getParam('sort', 'recent')) {
            case 'popular':
                $order = 'view_count';
                break;
            case 'recent':
            default:
                $order = 'modified_date';
                break;
        }


        // Prepare data
        $table = Engine_Api::_()->getItemTable('album');
        if (!in_array($order, $table->info('cols'))) {
            $order = 'modified_date';
        }

        $select = $table->select()
                ->where("search = 1")
                ->order($order . ' DESC');

        $user_id = $params['user'] = $this->_getParam('user');
        $sort = $params['sort'] = $this->_getParam('sort');
        $search = $params['search'] = $this->_getParam('search', false);
        $category_id = $params['category_id'] = $this->_getParam('category_id');
        if ($user_id)
            $select->where("owner_id = ?", $user_id);
        if ($category_id)
            $select->where("category_id = ?", $category_id);

        if ($search) {
            $select->where('title LIKE ? OR description LIKE ?', '%' . $search . '%');
        }

        if (isset($params['page']) && !empty($params['page']))
            $page = $params['page'];
        else
            $page = 1;

        $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('album', null, 'create');

        $paginator = $this->view->paginator = Zend_Paginator::factory($select);
        $this->view->totalCount = $paginator->getTotalItemCount();
        $paginator->setItemCountPerPage($settings->getSetting('album_page', 28));
        $paginator->setCurrentPageNumber($page);

        $searchForm = new Album_Form_Search();
        $searchForm->getElement('sort')->setValue($sort);
        $searchForm->getElement('search')->setValue($search);
        $is_category_id = $searchForm->getElement('category_id');
        if ($is_category_id) {
            $is_category_id->setValue($category_id);
        }
        $this->view->searchParams = $searchForm->getValues();
        $this->view->params = $params;

        // Render
        if (!empty($siteadvsearch_browseAlbum))
            $this->_helper->content
                    //->setNoRender()
                    ->setEnabled()
            ;
    }

    //ACTION FOR SHOWING THE MUSIC LIST BASED ON SEARCH STRING
    public function browseMusicAction() {

        // Can create?
        $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('music_playlist', null, 'create');

        $this->view->is_ajax = $this->_getParam('isajax', 0);
        $this->view->showContent = Engine_Api::_()->siteadvsearch()->getpaginationTypeValue();
        $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $siteadvsearch_browseMusic = Zend_Registry::isRegistered('siteadvsearch_browseMusic') ? Zend_Registry::get('siteadvsearch_browseMusic') : null;

        // Get browse params
        $this->view->formFilter = $formFilter = new Siteadvsearch_Form_MusicSearch();
        if ($formFilter->isValid($this->_getAllParams())) {
            $values = $formFilter->getValues();
        } else {
            $values = array();
        }
        $this->view->formValues = array_filter($values);

        // Show
        $viewer = Engine_Api::_()->user()->getViewer();
        if (@$values['show'] == 2 && $viewer->getIdentity()) {
            // Get an array of friend ids
            $values['users'] = $viewer->membership()->getMembershipsOfIds();
        }
        unset($values['show']);

        if (isset($params['page']) && !empty($params['page']))
            $page = $params['page'];
        else
            $page = 1;


        // Get paginator
        $this->view->paginator = $paginator = Engine_Api::_()->music()->getPlaylistPaginator($values);
        $this->view->totalCount = $paginator->getTotalItemCount();
        $paginator->setItemCountPerPage(Engine_Api::_()->getApi('settings', 'core')->getSetting('music.playlistsperpage', 10));
        $paginator->setCurrentPageNumber($page);

        // Render
        if (!empty($siteadvsearch_browseMusic))
            $this->_helper->content
                    //->setNoRender()
                    ->setEnabled()
            ;
    }

    //ACTION FOR SHOWING THE BLOGS LIST BASED ON SEARCH STRING
    public function browseBlogAction() {

        // Prepare data
        $viewer = Engine_Api::_()->user()->getViewer();

        // Permissions
        $this->view->canCreate = $this->_helper->requireAuth()->setAuthParams('blog', null, 'create')->checkRequire();

        $this->view->is_ajax = $this->_getParam('isajax', 0);
        $this->view->showContent = Engine_Api::_()->siteadvsearch()->getpaginationTypeValue();

        // Make form
        // Note: this code is duplicated in the blog.browse-search widget
        $this->view->form = $form = new Blog_Form_Search();

        $form->removeElement('draft');
        if (!$viewer->getIdentity()) {
            $form->removeElement('show');
        }

        // Populate form
        $siteadvsearch_browseBlog = Zend_Registry::isRegistered('siteadvsearch_browseBlog') ? Zend_Registry::get('siteadvsearch_browseBlog') : null;
        $categories = Engine_Api::_()->getDbtable('categories', 'blog')->getCategoriesAssoc();
        if (!empty($categories) && is_array($categories) && $form->getElement('category')) {
            $form->getElement('category')->addMultiOptions($categories);
        }

        // Process form
        $form->isValid($this->_getAllParams());
        $values = $form->getValues();
        $this->view->formValues = array_filter($values);
        $values['draft'] = "0";
        $values['visible'] = "1";

        // Do the show thingy
        if (@$values['show'] == 2) {
            // Get an array of friend ids
            $table = Engine_Api::_()->getItemTable('user');
            $select = $viewer->membership()->getMembersSelect('user_id');
            $friends = $table->fetchAll($select);
            // Get stuff
            $ids = array();
            foreach ($friends as $friend) {
                $ids[] = $friend->user_id;
            }
            //unset($values['show']);
            $values['users'] = $ids;
        }

        $this->view->assign($values);

        // Get blogs
        $paginator = Engine_Api::_()->getItemTable('blog')->getBlogsPaginator($values);
        $this->view->totalCount = $paginator->getTotalItemCount();
        $items_per_page = Engine_Api::_()->getApi('settings', 'core')->blog_page;
        $paginator->setItemCountPerPage($items_per_page);

        $this->view->paginator = $paginator->setCurrentPageNumber($values['page']);

        if (!empty($values['category'])) {
            $this->view->categoryObject = Engine_Api::_()->getDbtable('categories', 'blog')
                            ->find($values['category'])->current();
        }

        // Render
        if (!empty($siteadvsearch_browseBlog))
            $this->_helper->content
                    //->setNoRender()
                    ->setEnabled()
            ;
    }

    //ACTION FOR SHOWING THE EVENTS LIST BASED ON SEARCH STRING
    public function browseEventAction() {

        // Prepare
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('event', null, 'create');

        $this->view->is_ajax = $this->_getParam('isajax', 0);
        $this->view->showContent = Engine_Api::_()->siteadvsearch()->getpaginationTypeValue();
        $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $siteadvsearch_browseEvent = Zend_Registry::isRegistered('siteadvsearch_browseEvent') ? Zend_Registry::get('siteadvsearch_browseEvent') : null;

        $filter = $params['filter'] = $this->_getParam('filter', 'future');
        if ($filter != 'past' && $filter != 'future')
            $filter = 'future';
        $this->view->filter = $filter;

        // Create form
        $this->view->formFilter = $formFilter = new Event_Form_Filter_Browse();
        $defaultValues = $formFilter->getValues();

        if (!$viewer || !$viewer->getIdentity()) {
            $formFilter->removeElement('view');
        }

        // Populate options
        foreach (Engine_Api::_()->getDbtable('categories', 'event')->select()->order('title ASC')->query()->fetchAll() as $row) {
            $formFilter->category_id->addMultiOption($row['category_id'], $row['title']);
        }
        if (count($formFilter->category_id->getMultiOptions()) <= 1) {
            $formFilter->removeElement('category_id');
        }

        // Populate form data
        if ($formFilter->isValid($this->_getAllParams())) {
            $this->view->formValues = $values = $formFilter->getValues();
        } else {
            $formFilter->populate($defaultValues);
            $this->view->formValues = $values = array();
        }

        // Prepare data
        $this->view->formValues = $values = $formFilter->getValues();

        if ($viewer->getIdentity() && @$values['view'] == 1) {
            $values['users'] = array();
            foreach ($viewer->membership()->getMembersInfo(true) as $memberinfo) {
                $values['users'][] = $memberinfo->user_id;
            }
        }

        $values['search'] = 1;

        if ($filter == "past") {
            $values['past'] = 1;
        } else {
            $values['future'] = 1;
        }

        // check to see if request is for specific user's listings
        $params['user'] = $userId = $this->_getParam('user');
        if (($user_id = $userId)) {
            $values['user_id'] = $user_id;
        }

        if (isset($params['page']) && !empty($params['page']))
            $page = $params['page'];
        else
            $page = 1;

        // Get paginator
        $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('event')
                ->getEventPaginator($values);
        $this->view->totalCount = $paginator->getTotalItemCount();
        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber($page);
        $this->view->params = $params;


        // Render
        if (!empty($siteadvsearch_browseEvent))
            $this->_helper->content
                    //->setNoRender()
                    ->setEnabled()
            ;
    }

    //ACTION FOR SHOWING THE FORUM LIST BASED ON SEARCH STRING
    public function browseForumAction() {

        $this->view->is_ajax = $this->_getParam('isajax', 0);
        $this->view->showContent = Engine_Api::_()->siteadvsearch()->getpaginationTypeValue();
        $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();

        if (isset($params['page']) && !empty($params['page']))
            $page = $params['page'];
        else
            $page = 1;

        $this->view->text = $searchText = $_GET['search'];
        $topicTable = Engine_Api::_()->getDbtable('topics', 'forum');
        $topicTableName = $topicTable->info('name');
        $postTable = Engine_Api::_()->getDbtable('posts', 'forum');
        $postTableName = $postTable->info('name');
        $siteadvsearch_browseForum = Zend_Registry::isRegistered('siteadvsearch_browseForum') ? Zend_Registry::get('siteadvsearch_browseForum') : null;

        if (!empty($siteadvsearch_browseForum)) {
            $select = $topicTable->select()
                    ->setIntegrityCheck(false)
                    ->from($topicTableName)
                    ->joinLeft($postTableName, $postTableName . '.topic_id = ' . $topicTableName . '.topic_id')
                    ->where($topicTableName . ".title LIKE ? OR " . $postTableName . ".body LIKE ? ", '%' . $searchText . '%')
                    ->group($topicTableName . '.topic_id');
            $this->view->paginator = $paginator = Zend_Paginator::factory($select);
            $this->view->totalCount = $paginator->getTotalItemCount();
            $paginator->setItemCountPerPage(10);
            $paginator->setCurrentPageNumber($page);
        }
    }

}
