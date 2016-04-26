<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_IndexController extends Seaocore_Controller_Action_Standard {

    protected $_navigation;

    //SET THE VALUE FOR ALL ACTION DEFAULT
    public function init() {

        //CHECK VIEW PRIVACY
        if (!$this->_helper->requireAuth()->setAuthParams('sitegroup_group', null, 'view')->isValid())
            return;

        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext
                ->addActionContext('rate', 'json')
                ->addActionContext('validation', 'html')
                ->initContext();

        //GET GROUP ID AND GROUP URL
        $group_url = $this->_getParam('group_url', null);
        $group_id = $this->_getParam('group_id', null);

        if ($group_url) {
            $group_id = Engine_Api::_()->sitegroup()->getGroupId($group_url);
        }
        if ($group_id) {
            $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
            if ($sitegroup) {
                Engine_Api::_()->core()->setSubject($sitegroup);
            }
        }

        //FOR UPDATE EXPIRATION
        if ((Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.task.updateexpiredgroups') + 900) <= time()) {
            Engine_Api::_()->sitegroup()->updateExpiredGroups();
        }
    }

    //ACTION FOR SHOWING THE GROUP LIST
    public function indexAction() {

        $searchForm = new Sitegroup_Form_Search(array('type' => 'sitegroup_group'));
        Zend_Registry::set('Sitegroup_Form_Search', $searchForm);

        $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
        $coreversion = $coremodule->version;
        if ($coreversion < '4.1.0') {
            $this->_helper->content->render();
        } else {
            $this->_helper->content
                    ->setNoRender()
                    ->setEnabled();
        }
    }

    //ACTION FOR SHOWING THE GROUP LIST
    public function pinboardBrowseAction() {

        $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
        $coreversion = $coremodule->version;
        if ($coreversion < '4.1.0') {
            $this->_helper->content->render();
        } else {
            $this->_helper->content
                    ->setNoRender()
                    ->setContentName("sitegroup_index_pinboard_browse")
                    ->setEnabled();
        }
    }

    //ACTION FOR SHOWING THE HOME GROUP
    public function homeAction() {

        $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
        $coreversion = $coremodule->version;
        if ($coreversion < '4.1.0') {
            $this->_helper->content->render();
        } else {
            $this->_helper->content
                    ->setNoRender()
                    ->setEnabled();
        }
    }

    //ACTION FOR BROWSE LOCATION GROUPS.
    public function mapAction() {

        $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.locationfield', 1);

        if (empty($enableLocation)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        } else {
            $this->_helper->content->setEnabled();
        }
    }

    //ACTION FOR BROWSE LOCATION GROUPS.
    public function mobilemapAction() {

        $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.locationfield', 1);

        if (empty($enableLocation)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        } else {
            $this->_helper->content->setEnabled();
        }
    }

    //ACTION FOR SHOWING SPONSERED GROUP AT HOME GROUP
    public function homeSponsoredAction() {

        //RETURN THE OBJECT OF LIMIT PER GROUP FROM CORE SETTING TABLE
        $limit_sitegroup = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.sponserdsitegroup.widgets', 4);
        $limit_sitegroup_horizontal = $limit_sitegroup * 2;

        $totalSitegroup = Engine_Api::_()->sitegroup()->getLising('Total Sponsored Sitegroup');

        // Total Count Sponsored Group
        $totalCount = $totalSitegroup->count();

        //RETRIVE THE VALUE OF START INDEX
        $startindex = $_GET['startindex'];

        if ($startindex > $totalCount) {
            $startindex = $totalCount - $limit_sitegroup;
        }

        if ($startindex < 0) {
            $startindex = 0;
        }

        //RETRIVE THE VALUE OF BUTTON DIRECTION
        $this->view->direction = $_GET['direction'];
        $values['start_index'] = $startindex;
        $values['totalgroups'] = $_GET['limit'];
        $values['category_id'] = $_GET['category_id'];
        $this->view->titletruncation = $_GET['titletruncation'];
        $this->view->totalgroups = $_GET['limit'];

        $this->view->sitegroups = $result = Engine_Api::_()->getDbTable('groups', 'sitegroup')->getListings('Sponsored Sitegroup AJAX', $values, null, null, array('group_id', 'photo_id', 'owner_id', 'title', 'group_url'));

        //Pass the total number of result in tpl file
        $this->view->count = count($result);
    }

    //ACTION FOR SHOWING SPONSORED LISTINGS IN WIDGET
    public function ajaxCarouselAction() {

        //SEAOCORE API
        $this->view->seacore_api = Engine_Api::_()->seaocore();

        //RETURN THE OBJECT OF LIMIT PER GROUP FROM CORE SETTING TABLE
        $this->view->sponserdSitegroupsCount = $limit_sitegroup = $_GET['curnt_limit'];
        $limit_sitegroup_horizontal = $limit_sitegroup * 2;

        $values = array();
        $values = $this->_getAllParams();

        //GET COUNT
        $totalCount = $_GET['total'];

        //RETRIVE THE VALUE OF START INDEX
        $startindex = $_GET['startindex'];

        if ($startindex > $totalCount) {
            $startindex = $totalCount - $limit_sitegroup;
        }

        if ($startindex < 0) {
            $startindex = 0;
        }

        $this->view->sponsoredIcon = $this->_getParam('sponsoredIcon', 1);
        //$this->view->showOptions = $this->_getParam('showOptions', array("category", "rating", "review"));
        $this->view->featuredIcon = $this->_getParam('featuredIcon', 1);
        //RETRIVE THE VALUE OF BUTTON DIRECTION
        $this->view->direction = $_GET['direction'];
        $values['start_index'] = $startindex;
        $sitegroupTable = Engine_Api::_()->getDbTable('groups', 'sitegroup');
        $this->view->totalItemsInSlide = $values['limit'] = $limit_sitegroup_horizontal;
        $this->view->popularity = $values['popularity'] = $this->_getParam('popularity', 'group_id');
        $this->view->fea_spo = $fea_spo = $this->_getParam('fea_spo', null);
        if ($fea_spo == 'featured') {
            $values['featured'] = 1;
        } elseif ($fea_spo == 'sponsored') {
            $values['sponsored'] = 1;
        } elseif ($fea_spo == 'fea_spo') {
            $values['sponsored'] = 1;
            $values['featured'] = 1;
        }

        //GET LISTINGS
        $this->view->sitegroups = $sitegroupTable->getListing('', $values);
        $this->view->count = count($this->view->sitegroups);
        $this->view->vertical = $_GET['vertical'];
        $this->view->ratingType = $this->_getParam('ratingType', 'rating');
        $this->view->title_truncation = $this->_getParam('title_truncation', 50);
        $this->view->blockHeight = $this->_getParam('blockHeight', 245);
        $this->view->blockWidth = $this->_getParam('blockWidth', 150);
        $this->view->statistics = Zend_Json_Decoder::decode($this->_getParam('statistics'));
    }

    //ACTION FOR GROUP PROFILE GROUP
    public function viewAction() {

        if (!Engine_Api::_()->core()->hasSubject('sitegroup_group'))
            return $this->_forwardCustom('notfound', 'error', 'core');

        //VIEWER INFORMATION
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        //GET SUBJECT AND GROUP ID
        $this->view->sitegroup = $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');

        $levelHost = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.level.createhost', 0);
        $package = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.lsettings', 0);

        //Start group member work for privacy.
        $this->view->sitegroupMemberEnabled = $sitegroupMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
        if (!empty($sitegroupMemberEnabled)) {
            $this->view->member_approval = $sitegroup->member_approval;
            $this->view->hasMembers = Engine_Api::_()->getDbTable('membership', 'sitegroup')->hasMembers($viewer_id, $sitegroup->group_id);

            //START MANAGE-ADMIN CHECK
            $this->view->viewPrivacy = $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'view');
            if (empty($isManageAdmin)) {
                if (!$sitegroup->isViewableByNetwork()) {
                    return $this->_forwardCustom('requireauth', 'error', 'core');
                } else {
                    return;
                }
            }

            //GROUP VIEW AUTHORIZATION
            if (!Engine_Api::_()->sitegroup()->canViewGroup($sitegroup)) {
                return;
            }
        } else {
            //START MANAGE-ADMIN CHECK
            $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'view');
            if (empty($isManageAdmin)) {
                return $this->_forwardCustom('requireauth', 'error', 'core');
            }

            //GROUP VIEW AUTHORIZATION
            if (!Engine_Api::_()->sitegroup()->canViewGroup($sitegroup)) {
                return $this->_forwardCustom('requireauth', 'error', 'core');
            }
        }
        //End group member work for privacy.

        $LevelHost = $this->checkLevelHost($levelHost, 'sitegroup');

        //INCREMENT IN NUMBER OF VIEWS
        $PackagesHost = $this->checkPackageHost($package);

        if (($PackagesHost != $LevelHost) && ($sitegroup->view_count % 20 == $maxView)) {
            Engine_Api::_()->sitegroup()->setDisabledType();
            Engine_Api::_()->getItemtable('sitegroup_package')->setEnabledPackages();
        }

        $edit_layout_setting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0);

        //OPEN TAB IN NEW PAGE
        if (!$edit_layout_setting && $this->renderWidgetCustom())
            return;

        //OPEN TAB IN NEW PAGE
        if ($edit_layout_setting && $this->renderUserWidgetCustom())
            return;

        $this->view->can_edit_overview = $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'overview');

        $this->view->headLink()
                ->appendStylesheet($this->view->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/styles/style_sitegroup_profile.css');

        $commonCss = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.common.css');
        $pageEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage');
        $businessEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusiness');

        if ($commonCss && $pageEnabled && $businessEnabled) {
            $this->view->headLink()->appendStylesheet($this->view->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/common_style_page_business_group.css');
        } elseif ($commonCss && $pageEnabled) {
            $this->view->headLink()->appendStylesheet($this->view->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/common_style_page_business.css');
        } elseif ($commonCss && $businessEnabled) {
            $this->view->headLink()->appendStylesheet($this->view->layout()->staticBaseUrl . 'application/modules/Sitebusiness/externals/styles/common_style_business_group.css');
        } else {
            $this->view->headLink()->appendStylesheet($this->view->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/styles/style_sitegroup.css');
        }

        if (!$sitegroup->all_post && !Engine_Api::_()->sitegroup()->isGroupOwner($sitegroup)) {
            $this->view->headStyle()->appendStyle(".activity-post-container{
    display:none;
    }");
            $this->view->headStyle()->appendStyle(".adv_post_container_box{
    display:none;
    }");
        }
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum')) {
            $this->view->headLink()
                    ->appendStylesheet($this->view->layout()->staticBaseUrl . 'application/modules/Sitegroupalbum/externals/styles/style_sitegroupalbum.css'
            );
        }

        //CALL FUNCTION FOR INCRESING THE MEMORY LIMIT
        $this->setPhpIniMemorySize();
        $maxView = 19;

        $groupStatistics = <<<EOF
       en4.core.runonce.add(function(){
        en4.sitegroup.groupStatistics("$sitegroup->group_id");   
       });
EOF;
        $this->view->headScript()->appendScript($groupStatistics);

        $style = $sitegroup->getGroupStyle();
        if (!empty($style)) {
            $this->view->headStyle()->appendStyle($style);
        }

        if (null !== ($tab = $this->_getParam('tab'))) {
            //PROVIDE WIDGETISE GROUPS
            $groupprofile_tab_function = <<<EOF
                                        var content_id = "$tab";
                                        this.onload = function()
                                        {
      																		if(window.tabContainerSwitch) 
      																		{
                                              tabContainerSwitch($('main_tabs').getElement('.tab_' + content_id));
																					}
                                        }
EOF;
            $this->view->headScript()->appendScript($groupprofile_tab_function);
        }

        if (!empty($edit_layout_setting)) {
            $showHideHeaderFooter = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.show.hide.header.footer', 'default');
            if ($showHideHeaderFooter == 'default-simple') {
                $this->_helper->layout->setLayout('default-simple');
            }
            $cont = Engine_Content::getInstance();

            if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
                $storage = Engine_Api::_()->getDbtable('contentgroups', 'sitegroup');
            } else {
                $storage = Engine_Api::_()->getDbtable('mobileContentgroups', 'sitegroup');
            }

            $cont->setStorage($storage);
            $this->view->sitemain = $this->view->content('sitegroup_index_view');
            $cont = Engine_Content::getInstance();
            $storage = Engine_Api::_()->getDbtable('pages', 'core');
            $cont->setStorage($storage);
        } else {
            $viewPrivacy = empty($isManageAdmin) && !empty($sitegroupMemberEnabled) && empty($this->view->hasMembers);
            if (!$viewPrivacy) {
                $this->_helper->content->setNoRender()->setEnabled();
            } else {
                //$cont->setStorage($storage);
                $this->view->sitemain = $this->view->content('sitegroup_index_view');
                $cont = Engine_Content::getInstance();
                $storage = Engine_Api::_()->getDbtable('pages', 'core');
                $cont->setStorage($storage);
            }
        }


        // Start: Suggestion work.
        $is_moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');
        // Here we are delete this poll suggestion if viewer have.
        if (!empty($is_moduleEnabled)) {
            Engine_Api::_()->getApi('suggestion', 'sitegroup')->deleteSuggestion($viewer->getIdentity(), 'sitegroup', $sitegroup->group_id, 'group_suggestion');
        }
        // End: Suggestion work.
        //NAVIGATION WORK FOR FOOTER.(DO NOT DISPLAY NAVIGATION IN FOOTER ON VIEW PAGE.)
        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            if (!Zend_Registry::isRegistered('sitemobileNavigationName')) {
                Zend_Registry::set('sitemobileNavigationName', 'setNoRender');
            }
        }
        if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
            Zend_Registry::set('setFixedCreationFormBack', 'Back');
        }
    }

    //ACTINO FOR MANAGING MY GROUPS
    public function manageAction() {

        //USER VALDIATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->autoContentLoad = $isappajax = $this->_getParam('isappajax', 0);

        $sitegroup_manage = Zend_Registry::isRegistered('sitegroup_manage') ? Zend_Registry::get('sitegroup_manage') : null;

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GROUP CREATION PRIVACY
        $this->view->can_create = $this->_helper->requireAuth()->setAuthParams('sitegroup_group', null, 'create')->checkRequire();

        if (empty($this->view->can_create)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $this->view->can_edit = $this->_helper->requireAuth()->setAuthParams('sitegroup_group', null, 'edit')->checkRequire();
            $this->view->enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.price.field', 1);
            $this->view->enableLocation = $checkLocation = Engine_Api::_()->sitegroup()->enableLocation();
            $this->view->can_delete = $this->_helper->requireAuth()->setAuthParams('sitegroup_group', null, 'delete')->checkRequire();

            Engine_Api::_()->getDbtable('groupstatistics', 'sitegroup')->setViews();

            //GET VIEWER CLAIMS
            $claim_id = Engine_Api::_()->getDbtable('claims', 'sitegroup')->getViewerClaims($viewer_id);

            //CLAIM IS ENABLED OR NOT
            $canClaim = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitegroup_group', 'claim');

            $this->view->showClaimLink = 0;
            if (!empty($claim_id) && !empty($canClaim)) {
                $this->view->showClaimLink = 1;
            }

            //NAVIGATION
            $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('sitegroup_main');

            //QUICK NAVIGATION
            $this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('sitegroup_quick');

            //FORM GENERATION
            $this->view->form = $form = new Sitegroup_Form_Managesearch(array(
                'type' => 'sitegroup_group'
            ));

            $form->removeElement('show');

            //SITEGROUP-REVIEW IS ENABLED OR NOT
            $this->view->ratngShow = $ratingShow = (int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview');

            //CHECK THAT WE HAVE TO SHOW MANAGE ADMIN LINK OR NOTE
            $admingroups = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->getManageAdminGroups($viewer_id);

            //GET STUFF
            $manageadmin_ids = array();
            foreach ($admingroups as $admingroup) {
                $manageadmin_ids[] = $admingroup->group_id;
            }
            $manageadmin_values = array();
            $manageadmin_values['admingroups'] = $manageadmin_ids;
            $manageadmin_values['orderby'] = 'creation_date';
            $manageadmin_data = Engine_Api::_()->sitegroup()->getSitegroupsPaginator($manageadmin_values, null);
            $this->view->manageadmin_count = $manageadmin_data->getTotalItemCount();
            //END CHECK THAT WE HAVE TO SHOW MANAGE ADMIN LINK OR NOTE
            //PROCESS FORM
            $request = $this->getRequest()->getPost();

            //PROCESS FORM
            if ($form->isValid($this->_getAllParams())) {
                $values = $form->getValues();
                if ($values['subcategory_id'] == 0) {
                    $values['subsubcategory_id'] = 0;
                    $values['subsubcategory'] = 0;
                }
            } else {
                $values = array();
            }

            if (empty($sitegroup_manage)) {
                return;
            }

            //CHECK TO SEE IF REQUEST IS FOR SPECIFIC USER'S GROUPS
            // $values['user_id'] = $viewer->getIdentity();
            $values['type'] = 'manage';
            $values['type_location'] = 'manage';

//        if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
//            $onlymember = Engine_Api::_()->getDbtable('membership', 'sitegroup')->getJoinGroups($viewer->getIdentity(), 'onlymember');
//
//            $onlymemberids = array();
//            foreach ($onlymember as $onlymembers) {
//              $onlymemberids[] = $onlymembers->group_id;
//            } 
//            if (!empty($onlymemberids)) {
//                $values['adminjoinedgroups'] = array_merge($onlymemberids, $manageadmin_values['admingroups']);
//            }
//        } else {
            $values['adminjoinedgroups'] = $manageadmin_values['admingroups'];
//        }

            $this->view->formValues = $values;

            //GET PAGINATOR
            $this->view->paginator = $paginator = Engine_Api::_()->sitegroup()->getSitegroupsPaginator($values, null);
            $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.group', 10);


            $paginator->setItemCountPerPage($items_count);
            $this->view->paginator = $paginator->setCurrentPageNumber($values['page']);

            $this->view->page = $values['page'] ? $values['page'] : 1;
            $this->view->totalPages = ceil(($paginator->getTotalItemCount()) / $items_count);
            //MAXIMUM ALLOWED GROUPS
            //WE HAVE IMPORT GROUPS FUNCTIONALITY, WE DONT WANT TO SHOW GROUP LIMIT ALERT MESSAGE TO SUPERADMIN SO WE ARE SETTING $this->view->quota = 0;
            $this->view->quota = 0;
            if ($viewer->level_id != 1) {
                $this->view->quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitegroup_group', 'max');
            }
            $this->view->current_count = $paginator->getTotalItemCount();
            $this->view->category_id = $values['category_id'];
            $this->view->subcategory_id = $values['subcategory_id'];
            $this->view->subsubcategory_id = $values['subsubcategory_id'];

            //if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
            $coreversion = $coremodule->version;
            if (!$isappajax) {
                if ($coreversion < '4.1.0') {
                    $this->_helper->content->render();
                } else {
                    $this->_helper->content
                            //->setNoRender()
                            ->setEnabled();
                }
            }
            //}
        } else {
            $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
            $coreversion = $coremodule->version;
            if (!$isappajax) {
                if ($coreversion < '4.1.0') {
                    $this->_helper->content->render();
                } else {
                    $this->_helper->content
                            ->setNoRender()
                            ->setEnabled();
                }
            }
        }
    }

    // create  sitegroup sitegroup
    public function createAction() {

        //USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->quick = $quick = $this->_getParam('seaoSmoothbox', false);
        if (!$quick) {
            //RENDER PAGE
            $this->_helper->content
                    //->setNoRender()
                    ->setEnabled()
            ;
        }

        if (Engine_Api::_()->getApi('settings', 'core')->hasSetting('sitegroup.createFormFields')) {
            $settings = Engine_Api::_()->getApi('settings', 'core');
            $createFormFields = $settings->getSetting('sitegroup.createFormFields');
            if (!$this->_getParam('seaoSmoothbox', false) && !empty($createFormFields) && in_array('showHideAdvancedOptions', $createFormFields)) {
                $this->view->quick = $quick = 1;
            }
        }

        //SITEMOBILE_MODULE_NOT_SUPPORT_DESC_FOR_SOMEGROUPES
        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            Engine_API::_()->sitemobile()->setupRequestError();
        } else {
            $this->_helper->content->setEnabled();
        }

        //GROUP CREATE PRIVACY
        if (!$this->_helper->requireAuth()->setAuthParams('sitegroup_group', null, 'create')->isValid())
            return;
        $package_id = 0;
        $viewer = Engine_Api::_()->user()->getViewer();
        global $sitegroup_is_approved;
        $sitegroupHostName = str_replace('www.', '', @strtolower($_SERVER['HTTP_HOST']));
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitegroup_main', array(), 'sitegroup_main_create');
        $getPackageAuth = Engine_Api::_()->sitegroup()->getPackageAuthInfo('sitegroup');

        $levelHost = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.level.createhost', 0);
        $package = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.lsettings', 0);
        $LevelHost = $this->checkLevelHost($levelHost, 'sitegroup');
        $PackagesHost = $this->checkPackageHost($package);
        $sub_status_table = Engine_Api::_()->getDbTable('groupstatistics', 'sitegroup');

        if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
            //REDIRECT
            $package_id = $this->_getParam('id');
            if (empty($package_id)) {
                return $this->_forwardCustom('notfound', 'error', 'core');
            }
            $this->view->package = $package = Engine_Api::_()->getItemTable('sitegroup_package')->fetchRow(array('package_id = ?' => $package_id, 'enabled = ?' => '1'));
            if (empty($this->view->package)) {
                return $this->_forwardCustom('notfound', 'error', 'core');
            }

            if (!empty($package->level_id) && !in_array($viewer->level_id, explode(",", $package->level_id))) {
                return $this->_forwardCustom('notfound', 'error', 'core');
            }
        } else {
            $package_id = Engine_Api::_()->getItemtable('sitegroup_package')->fetchRow(array('defaultpackage = ?' => 1))->package_id;
        }

        $maxCount = 10;
        $table = Engine_Api::_()->getDbtable('groups', 'sitegroup');
        $tablename = $table->info('name');
        $select = $table->select()->from($tablename, array('count(*) as count'));
        $results = $table->fetchRow($select);
        if (($PackagesHost != $LevelHost) && ($results->count > $maxCount)) {
            Engine_Api::_()->sitegroup()->setDisabledType();
            Engine_Api::_()->getItemtable('sitegroup_package')->setEnabledPackages();
        }
        $sitegroup_featured = Zend_Registry::isRegistered('sitegroup_featured') ? Zend_Registry::get('sitegroup_featured') : null;
        $manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup');
        $row = $manageadminsTable->createRow();

        //FORM VALIDATION
        $this->view->form = $form = new Sitegroup_Form_Create(array("packageId" => $package_id, "owner" => $viewer, 'quick' => $quick));
        $this->view->sitegroupUrlEnabled = $sitegroupUrlEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupurl');

        $this->view->show_url = $show_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.showurl.column', 1);
        if (!empty($sitegroupUrlEnabled) && empty($show_url)) {
            $form->removeElement('group_url');
            $form->removeElement('group_url_msg');
        }
        if (empty($sitegroup_featured)) {
            return;
        }

        $isHost = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.isHost', 0);
        if (empty($isHost)) {
            Engine_Api::_()->getApi('settings', 'core')->setSetting('sitegroup.viewgroup.sett', convert_uuencode($sitegroupHostName));
        }

        //SET UP DATA NEEDED TO CHECK QUOTA

        $sitegroup_category = Zend_Registry::isRegistered('sitegroup_category') ? Zend_Registry::get('sitegroup_category') : null;
        $values['user_id'] = $viewer->getIdentity();
        // $paginator = Engine_Api::_()->getApi('core', 'sitegroup')->getSitegroupsPaginator($values);
        $count = Engine_Api::_()->getDbtable('groups', 'sitegroup')->countUserGroups($values);
        $this->view->quota = $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitegroup_group', 'max');

        $sitegroup_render = Zend_Registry::isRegistered('sitegroup_render') ? Zend_Registry::get('sitegroup_render') : null;
        $this->view->current_count = $count;

        if (!empty($sitegroup_render)) {
            $this->view->sitegroup_render = $sitegroup_render;
        }

        //IF NOT POST OR FORM NOT VALID, RETURN
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $table = Engine_Api::_()->getItemTable('sitegroup_group');
            $db = $table->getAdapter();
            $db->beginTransaction();

            try {
                // Create sitegroup
                $values = array_merge($form->getValues(), array(
                    'owner_id' => $viewer->getIdentity(),
                    'package_id' => $package_id
                ));

                $is_error = 0;
                if (isset($values['category_id']) && empty($values['category_id'])) {
                    $is_error = 1;
                }
                if (empty($values['subcategory_id'])) {
                    $values['subcategory_id'] = 0;
                }
                if (empty($values['subsubcategory_id'])) {
                    $values['subsubcategory_id'] = 0;
                }

                //SET ERROR MESSAGE
                if ($is_error == 1) {
                    $this->view->status = false;
                    $error = Zend_Registry::get('Zend_Translate')->_('Group Category * Please complete this field - it is required.');
                    $form->getDecorator('errors')->setOption('escape', false);
                    $form->addError($error);
                    return;
                }
                $sitegroup = $table->createRow();

                if (Engine_Api::_()->getApi('subCore', 'sitegroup')->groupBaseNetworkEnable()) {
                    if (isset($values['networks_privacy']) && !empty($values['networks_privacy'])) {
                        if (in_array(0, $values['networks_privacy'])) {
                            $values['networks_privacy'] = new Zend_Db_Expr('NULL');
                        } else {
                            $values['networks_privacy'] = (string) ( is_array($values['networks_privacy']) ? join(",", $values['networks_privacy']) : $netowrkIds );
                        }
                    }
                }
                if (!empty($sitegroupUrlEnabled)) {
                    if (empty($show_url)) {
                        $resultGroupTable = $table->select()->where('title =?', $values['title'])->from($table, 'title')
                                        ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
                        $count_index = count($resultGroupTable);
                        $resultGroupUrl = $table->select()->where('group_url =?', $values['title'])->from($table, 'group_url')
                                        ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
                        $count_index_url = count($resultGroupUrl);
                    }
                    $urlArray = Engine_Api::_()->sitegroup()->getBannedUrls();
                    if (!empty($show_url)) {
                        if (in_array(strtolower($values['group_url']), $urlArray)) {
                            $form->addError(Zend_Registry::get('Zend_Translate')->_('Sorry, this URL has been restricted by our automated system. Please choose a different URL.'));
                            return;
                        }
                    } elseif (!empty($sitegroupUrlEnabled)) {
                        $lastgroup_id = $table->select()
                                ->from($table->info('name'), array('group_id'))->order('group_id DESC')
                                ->query()
                                ->fetchColumn();
                        $values['group_url'] = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($values['title']))), '-');
                        if (!empty($count_index) || !empty($count_index_url)) {
                            $lastgroup_id = $lastgroup_id + 1;
                            $values['group_url'] = $values['group_url'] . '-' . $lastgroup_id;
                        } else {
                            $values['group_url'] = $values['group_url'];
                        }
                        if (in_array(strtolower($values['group_url']), $urlArray)) {

                            $form->addError(Zend_Registry::get('Zend_Translate')->_('Sorry, this Group Title has been restricted by our automated system. Please choose a different Title.', array('escape' => false)));
                            return;
                        }
                    }
                }
                $sitegroup->setFromArray($values);


                $user_level = $viewer->level_id;
                if (!Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                    $sitegroup->featured = Engine_Api::_()->authorization()->getPermission($user_level, 'sitegroup_group', 'featured');
                    $sitegroup->sponsored = Engine_Api::_()->authorization()->getPermission($user_level, 'sitegroup_group', 'sponsored');
                    $sitegroup->approved = Engine_Api::_()->authorization()->getPermission($user_level, 'sitegroup_group', 'approved');
                } else {
                    $sitegroup->featured = $package->featured;
                    $sitegroup->sponsored = $package->sponsored;
                    if ($package->isFree() && !empty($sitegroup_is_approved) && !empty($getPackageAuth)) {
                        $sitegroup->approved = $package->approved;
                    } else {
                        $sitegroup->approved = 0;
                    }
                }

                if (!empty($sitegroup->approved)) {
                    $sitegroup->pending = 0;
                    $sitegroup->aprrove_date = date('Y-m-d H:i:s');

                    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                        $expirationDate = $package->getExpirationDate();
                        if (!empty($expirationDate))
                            $sitegroup->expiration_date = date('Y-m-d H:i:s', $expirationDate);
                        else
                            $sitegroup->expiration_date = '2250-01-01 00:00:00';
                    }
                    else {
                        $sitegroup->expiration_date = '2250-01-01 00:00:00';
                    }
                }
                if (!empty($sitegroup_category)) {
                    $sitegroup->save();
                    $group_id = $sitegroup->group_id;
                }

                if (!empty($sitegroup->approved)) {
                    Engine_Api::_()->sitegroup()->sendMail("ACTIVE", $sitegroup->group_id);
                } else {
                    Engine_Api::_()->sitegroup()->sendMail("APPROVAL_PENDING", $sitegroup->group_id);
                }

                $manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup');
                $row = $manageadminsTable->createRow();
                $row->user_id = $sitegroup->owner_id;
                $row->group_id = $sitegroup->group_id;
                $row->save();

                //START PROFILE MAPS WORK
                Engine_Api::_()->getDbtable('profilemaps', 'sitegroup')->profileMapping($sitegroup);


                $group_id = $sitegroup->group_id;
                if (!empty($sitegroupUrlEnabled) && empty($show_url)) {
                    $values['group_url'] = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($values['title']))), '-');
                    if (!empty($count_index) || !empty($count_index_url)) {
                        $values['group_url'] = $values['group_url'] . '-' . $group_id;
                        $table->update(array('group_url' => $values['group_url']), array('group_id = ?' => $group_id));
                    } else {
                        $values['group_url'] = $values['group_url'];
                        $table->update(array('group_url' => $values['group_url']), array('group_id = ?' => $group_id));
                    }
                }

                $sitegroupFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupform');
                if ($sitegroupFormEnabled) {
                    $tablecontent = Engine_Api::_()->getDbtable('content', 'core');
                    $params = $tablecontent->select()
                                    ->from($tablecontent->info('name'), 'params')
                                    ->where('name = ?', 'sitegroupform.sitegroup-viewform')
                                    ->query()->fetchColumn();
                    $decodedParam = Zend_Json::decode($params);
                    $tabName = $decodedParam['title'];
                    if (empty($tabName))
                        $tabName = 'Form';
                    $sitegroupformtable = Engine_Api::_()->getDbtable('sitegroupforms', 'sitegroupform');
                    $optionid = Engine_Api::_()->getDbtable('groupquetions', 'sitegroupform');
                    $table_option = Engine_Api::_()->fields()->getTable('sitegroupform', 'options');
                    $sitegroupform = $table_option->createRow();
                    $sitegroupform->setFromArray($values);
                    $sitegroupform->label = $values['title'];
                    $sitegroupform->field_id = 1;
                    $option_id = $sitegroupform->save();
                    $optionids = $optionid->createRow();
                    $optionids->option_id = $option_id;
                    $optionids->group_id = $group_id;
                    $optionids->save();
                    $sitegroupforms = $sitegroupformtable->createRow();
                    if (isset($sitegroupforms->offer_tab_name))
                        $sitegroupforms->offer_tab_name = $tabName;
                    $sitegroupforms->description = 'Please leave your feedback below and enter your contact details.';
                    $sitegroupforms->group_id = $group_id;
                    $sitegroupforms->save();
                }
                //SET PHOTO
                if (!empty($values['photo'])) {
                    $sitegroup->setPhoto($form->photo);
                    $sitegroupinfo = $sitegroup->toarray();
                    $albumTable = Engine_Api::_()->getDbtable('albums', 'sitegroup');
                    $album_id = $albumTable->update(array('photo_id' => $sitegroupinfo['photo_id'], 'owner_id' => $sitegroupinfo['owner_id']), array('group_id = ?' => $sitegroupinfo['group_id']));
                } else {
                    $sitegroupinfo = $sitegroup->toarray();
                    $albumTable = Engine_Api::_()->getDbtable('albums', 'sitegroup');
                    $album_id = $albumTable->insert(array(
                        'photo_id' => 0,
                        'owner_id' => $sitegroupinfo['owner_id'],
                        'group_id' => $sitegroupinfo['group_id'],
                        'title' => $sitegroupinfo['title'],
                        'creation_date' => $sitegroupinfo['creation_date'],
                        'modified_date' => $sitegroupinfo['modified_date']));
                }

                //ADD TAGS
                $tags = preg_split('/[,]+/', $values['tags']);
                $tags = array_filter(array_map("trim", $tags));
                $sitegroup->tags()->addTagMaps($viewer, $tags);

                if (!empty($group_id)) {
                    $sitegroup->setLocation();
                }

                // Set privacy
                $auth = Engine_Api::_()->authorization()->context;

                //get the group admin list.
                $ownerList = $sitegroup->getGroupOwnerList();

                $sitegroupmemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
                if (!empty($sitegroupmemberEnabled)) {
                    $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                } else {
                    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                }


                if (!isset($values['auth_view']) || empty($values['auth_view'])) {
                    $values['auth_view'] = "everyone";
                }

                if (!isset($values['auth_comment']) || empty($values['auth_comment'])) {
                    $values['auth_comment'] = "everyone";
                }

                $viewMax = array_search($values['auth_view'], $roles);
                $commentMax = array_search($values['auth_comment'], $roles);

                foreach ($roles as $i => $role) {
                    $auth->setAllowed($sitegroup, $role, 'view', ($i <= $viewMax));
                    $auth->setAllowed($sitegroup, $role, 'comment', ($i <= $commentMax));
                    $auth->setAllowed($sitegroup, $role, 'print', 1);
                    $auth->setAllowed($sitegroup, $role, 'tfriend', 1);
                    $auth->setAllowed($sitegroup, $role, 'overview', 1);
                    $auth->setAllowed($sitegroup, $role, 'map', 1);
                    $auth->setAllowed($sitegroup, $role, 'insight', 1);
                    $auth->setAllowed($sitegroup, $role, 'layout', 1);
                    $auth->setAllowed($sitegroup, $role, 'contact', 1);
                    $auth->setAllowed($sitegroup, $role, 'form', 1);
                    $auth->setAllowed($sitegroup, $role, 'offer', 1);
                    $auth->setAllowed($sitegroup, $role, 'invite', 1);
                }

                $sitegroupmemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
                if (!empty($sitegroupmemberEnabled)) {
                    $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                } else {
                    $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                }

                //START WORK FOR SUB GROUP.
                if (empty($values['auth_sspcreate'])) {
                    $values['auth_sspcreate'] = "owner";
                }

                $createMax = array_search($values['auth_sspcreate'], $roles);
                foreach ($roles as $i => $role) {
                    if ($role === 'like_member') {
                        $role = $ownerList;
                    }
                    $auth->setAllowed($sitegroup, $role, 'sspcreate', ($i <= $createMax));
                }
                //END WORK FOR SUBGROUP
                //START SITEGROUPDISCUSSION PLUGIN WORK      
                $sitegroupdiscussionEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion');
                if ($sitegroupdiscussionEnabled) {
                    //START DISCUSSION PRIVACY WORK
                    if (empty($values['sdicreate'])) {
                        $values['sdicreate'] = "registered";
                    }

                    $createMax = array_search($values['sdicreate'], $roles);
                    foreach ($roles as $i => $role) {
                        if ($role === 'like_member') {
                            $role = $ownerList;
                        }
                        $auth->setAllowed($sitegroup, $role, 'sdicreate', ($i <= $createMax));
                    }
                    //END DISCUSSION PRIVACY WORK
                }
                //END SITEGROUPDISCUSSION PLUGIN WORK        
                //START SITEGROUPALBUM PLUGIN WORK      
                $sitegroupalbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum');
                if ($sitegroupalbumEnabled) {
                    //START PHOTO PRIVACY WORK
                    if (empty($values['spcreate'])) {
                        $values['spcreate'] = "registered";
                    }

                    $createMax = array_search($values['spcreate'], $roles);
                    foreach ($roles as $i => $role) {
                        if ($role === 'like_member') {
                            $role = $ownerList;
                        }
                        $auth->setAllowed($sitegroup, $role, 'spcreate', ($i <= $createMax));
                    }
                    //END PHOTO PRIVACY WORK
                }
                //END SITEGROUPALBUM PLUGIN WORK
                //START SITEGROUPDOCUMENT PLUGIN WORK
                $sitegroupDocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument');
                if ($sitegroupDocumentEnabled) {
                    $sitegroupmemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
                    if (!empty($sitegroupmemberEnabled)) {
                        $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                    } else {
                        $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                    }

                    if (empty($values['sdcreate'])) {
                        $values['sdcreate'] = "registered";
                    }

                    $createMax = array_search($values['sdcreate'], $roles);
                    foreach ($roles as $i => $role) {
                        if ($role === 'like_member') {
                            $role = $ownerList;
                        }
                        $auth->setAllowed($sitegroup, $role, 'sdcreate', ($i <= $createMax));
                    }
                }
                //END SITEGROUPDOCUMENT PLUGIN WORK
                //START SITEGROUPVIDEO PLUGIN WORK
                $sitegroupVideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo');
                if ($sitegroupVideoEnabled || (Engine_Api::_()->hasModuleBootstrap('sitevideo') && Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitegroup_group', 'item_module' => 'sitegroup')))) {
                    if (empty($values['svcreate'])) {
                        $values['svcreate'] = "registered";
                    }

                    $createMax = array_search($values['svcreate'], $roles);
                    foreach ($roles as $i => $role) {
                        if ($role === 'like_member') {
                            $role = $ownerList;
                        }
                        $auth->setAllowed($sitegroup, $role, 'svcreate', ($i <= $createMax));
                    }
                }
                //END SITEGROUPVIDEO PLUGIN WORK
                //START SITEGROUPPOLL PLUGIN WORK
                $sitegroupPollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouppoll');
                if ($sitegroupPollEnabled) {
                    if (empty($values['splcreate'])) {
                        $values['splcreate'] = "registered";
                    }

                    $createMax = array_search($values['splcreate'], $roles);
                    foreach ($roles as $i => $role) {
                        if ($role === 'like_member') {
                            $role = $ownerList;
                        }
                        $auth->setAllowed($sitegroup, $role, 'splcreate', ($i <= $createMax));
                    }
                }
                //END SITEGROUPPOLL PLUGIN WORK
                //START SITEGROUPNOTE PLUGIN WORK
                $sitegroupNoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupnote');
                if ($sitegroupNoteEnabled) {
                    if (empty($values['sncreate'])) {
                        $values['sncreate'] = "registered";
                    }

                    $createMax = array_search($values['sncreate'], $roles);
                    foreach ($roles as $i => $role) {
                        if ($role === 'like_member') {
                            $role = $ownerList;
                        }
                        $auth->setAllowed($sitegroup, $role, 'sncreate', ($i <= $createMax));
                    }
                }
                //END SITEGROUPNOTE PLUGIN WORK
                //START SITEGROUPMUSIC PLUGIN WORK
                $sitegroupMusicEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic');
                if ($sitegroupMusicEnabled) {
                    if (empty($values['smcreate'])) {
                        $values['smcreate'] = "registered";
                    }

                    $createMax = array_search($values['smcreate'], $roles);
                    foreach ($roles as $i => $role) {
                        if ($role === 'like_member') {
                            $role = $ownerList;
                        }
                        $auth->setAllowed($sitegroup, $role, 'smcreate', ($i <= $createMax));
                    }
                }
                //END SITEGROUPMUSIC PLUGIN WORK
                //START SITEGROUPEVENT PLUGIN WORK
                $sitegroupeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent');
                if ($sitegroupeventEnabled || (Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitegroup_group', 'item_module' => 'sitegroup')))) {
                    if (empty($values['secreate'])) {
                        $values['secreate'] = "registered";
                    }

                    $createMax = array_search($values['secreate'], $roles);
                    foreach ($roles as $i => $role) {
                        if ($role === 'like_member') {
                            $role = $ownerList;
                        }
                        $auth->setAllowed($sitegroup, $role, 'secreate', ($i <= $createMax));
                    }
                }
                //END SITEGROUPEVENT PLUGIN WORK
                //START SITEGROUPMEMBER PLUGIN WORK
                $sitegroupMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
                if ($sitegroupMemberEnabled) {
                    $membersTable = Engine_Api::_()->getDbtable('membership', 'sitegroup');
                    $row = $membersTable->createRow();
                    $row->resource_id = $sitegroup->group_id;
                    $row->group_id = $sitegroup->group_id;
                    $row->user_id = $sitegroup->owner_id;
                    $row->notification = 1;
                    $row->save();
                    Engine_Api::_()->sitegroup()->updateMemberCount($sitegroup);
                    $sitegroup->save();
                }
                $memberInvite = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.invite.option', 1);
                $member_approval = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.member.approval.option', 1);
                if (empty($memberInvite)) {
                    $memberInviteOption = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.invite.automatically', 1);
                    $sitegroup->member_invite = $memberInviteOption;
                    $sitegroup->save();
                }
                if (empty($member_approval)) {
                    $member_approvalOption = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.member.approval.automatically', 1);
                    $sitegroup->member_approval = $member_approvalOption;
                    $sitegroup->save();
                }
                //END SITEGROUPMEMBER PLUGIN WORK
                //START INTERGRATION EXTENSION WORK
                //START GROUP INTEGRATION WORK
                $business_id = $this->_getParam('business_id');
                if (!empty($business_id)) {
                    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
                    $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessintegration');
                    if (!empty($moduleEnabled)) {
                        $contentsTable = Engine_Api::_()->getDbtable('contents', 'sitebusinessintegration');
                        $row = $contentsTable->createRow();
                        $row->owner_id = $viewer_id;
                        $row->resource_owner_id = $sitegroup->owner_id;
                        $row->business_id = $business_id;
                        $row->resource_type = 'sitegroup_group';
                        $row->resource_id = $sitegroup->group_id;
                        $row->save();
                    }
                }
                //END GROUP INTEGRATION WORK
                //START PAGE INTEGRATION WORK
                $page_id = $this->_getParam('page_id');
                if (!empty($page_id)) {
                    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
                    $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageintegration');
                    if (!empty($moduleEnabled)) {
                        $contentsTable = Engine_Api::_()->getDbtable('contents', 'sitepageintegration');
                        $row = $contentsTable->createRow();
                        $row->owner_id = $viewer_id;
                        $row->resource_owner_id = $sitegroup->owner_id;
                        $row->page_id = $page_id;
                        $row->resource_type = 'sitegroup_group';
                        $row->resource_id = $sitegroup->group_id;
                        $row->save();
                    }
                }
                //END PAGE INTEGRATION WORK
                //START PAGE INTEGRATION WORK
                $store_id = $this->_getParam('store_id');
                if (!empty($store_id)) {
                    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
                    $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreintegration');
                    if (!empty($moduleEnabled)) {
                        $contentsTable = Engine_Api::_()->getDbtable('contents', 'sitestoreintegration');
                        $row = $contentsTable->createRow();
                        $row->owner_id = $viewer_id;
                        $row->resource_owner_id = $sitegroup->owner_id;
                        $row->store_id = $store_id;
                        $row->resource_type = 'sitegroup_group';
                        $row->resource_id = $sitegroup->group_id;
                        $row->save();
                    }
                }
                //END PAGE INTEGRATION WORK
                //END INTERGRATION EXTENSION WORK
                //START SUB GROUP WORK
                $parent_id = $this->_getParam('parent_id');
                if (!empty($parent_id)) {
                    $sitegroup->subgroup = 1;
                    $sitegroup->parent_id = $parent_id;
                    $sitegroup->save();
                }
                //END  SUB GROUP WORK
                //CUSTOM FIELD WORK
                if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.profile.fields', 1)) {
                    $customfieldform = $form->getSubForm('fields');
                    $customfieldform->setItem($sitegroup);
                    $customfieldform->saveValues();
                }

                //START DEFAULT EMAIL TO SUPERADMIN WHEN ANYONE CREATE GROUPS.
                $emails = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.defaultgroupcreate.email', Engine_API::_()->seaocore()->getSuperAdminEmailAddress());
                if (!empty($emails)) {
                    $emails = explode(",", $emails);
                    $host = $_SERVER['HTTP_HOST'];
                    $newVar = _ENGINE_SSL ? 'https://' : 'http://';
                    $object_link = $newVar . $host . $sitegroup->getHref();
                    $viewerGetTitle = $viewer->getTitle();
                    $sender_link = '<a href=' . $newVar . $host . $viewer->getHref() . ">$viewerGetTitle</a>";
                    foreach ($emails as $email) {
                        $email = trim($email);
                        Engine_Api::_()->getApi('mail', 'core')->sendSystem($email, 'SITEGROUP_GROUP_CREATION', array(
                            'sender' => $sender_link,
                            'object_link' => $object_link,
                            'object_title' => $sitegroup->getTitle(),
                            'object_description' => $sitegroup->getDescription(),
                            'queue' => true
                        ));
                    }
                }
                //END DEFAULT EMAIL TO SUPERADMIN WHEN ANYONE CREATE GROUPS.
                // Commit
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            if (!empty($sitegroup) && !empty($sitegroup->draft) && empty($sitegroup->pending)) {
                Engine_Api::_()->sitegroup()->attachGroupActivity($sitegroup);


                //START AUTOMATICALLY LIKE THE GROUP WHEN MEMBER CREATE A GROUP.
                $autoLike = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.automatically.like', 1);
                if (!empty($autoLike)) {
                    Engine_Api::_()->sitegroup()->autoLike($sitegroup->group_id, 'sitegroup_group');
                }
                //END AUTOMATICALLY LIKE THE GROUP WHEN MEMBER CREATE A GROUP.
                //SENDING ACTIVITY FEED TO FACEBOOK.
                $enable_Facebooksefeed = $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksefeed');
                if (!empty($enable_Facebooksefeed)) {

                    $sitegroup_array = array();
                    $sitegroup_array['type'] = 'sitegroup_new';
                    $sitegroup_array['object'] = $sitegroup;

                    Engine_Api::_()->facebooksefeed()->sendFacebookFeed($sitegroup_array);
                }
            }
            //EXTRACTING CURRENT ADMIN SETTINGS FOR THIS VIEW GROUP.
            $groupAdminTable = Engine_Api::_()->getDbtable('pages', 'core');
            $groupAdminTableName = $groupAdminTable->info('name');
            $selectGroupAdmin = $groupAdminTable->select()
                    ->setIntegrityCheck(false)
                    ->from($groupAdminTableName)
                    ->where('name = ?', 'sitegroup_index_view');
            $groupAdminresult = $groupAdminTable->fetchRow($selectGroupAdmin);

            //NOW INSERTING THE ROW IN GROUP TABLE
            $groupTable = Engine_Api::_()->getDbtable('contentgroups', 'sitegroup');

            //CREATE NEW GROUP
//       $groupObject = $groupTable->createRow();
//       $groupObject->displayname = ( null !== ($name = $values['title']) ? $name : 'Untitled' );
//       $groupObject->title = ( null !== ($name = $values['title']) ? $name : 'Untitled' );
//       $groupObject->description = $values['body'];
//       $groupObject->name = "sitegroup_index_view";
//       $groupObject->url = $groupAdminresult->url;
//       $groupObject->custom = $groupAdminresult->custom;
//       $groupObject->fragment = $groupAdminresult->fragment;
//       $groupObject->keywords = $groupAdminresult->keywords;
//       $groupObject->layout = $groupAdminresult->layout;
//       $groupObject->view_count = $groupAdminresult->view_count;
//       $groupObject->user_id = $values['owner_id'];
//       $groupObject->group_id = $sitegroup->group_id;
//       $contentGroupId = $groupObject->save();
// 
//       //NOW FETCHING GROUP CONTENT DEFAULT SETTING INFORMATION FROM CORE CONTENT TABLE FOR THIS GROUP.
//       //NOW INSERTING DEFAULT GROUP CONTENT SETTINGS IN OUR CONTENT TABLE
//       $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0);
//       $sitegroup_layout_cover_photo = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layout.cover.photo', 1);
//       if (!$layout) {
//         Engine_Api::_()->getDbtable('content', 'sitegroup')->setContentDefault($contentGroupId, $sitegroup_layout_cover_photo);
//       } else {
//         Engine_Api::_()->getApi('layoutcore', 'sitegroup')->setContentDefaultLayout($contentGroupId, $sitegroup_layout_cover_photo);
//       }
            //REDIRECT
            return $this->_helper->redirector->gotoRoute(array('action' => 'get-started', 'group_id' => $sitegroup->group_id, 'saved' => '1'), 'sitegroup_dashboard', true);
        } /* else {
          $results = $this->getRequest()->getPost();
          if (!empty($results) && isset($results['subcategory_id'])) {
          $this->view->category_id = $results['category_id'];
          if (!empty($results['subcategory_id'])) {
          $this->view->subcategory_name = Engine_Api::_()->getDbtable('categories', 'sitegroup')->getCategory($results['subcategory_id'])->category_name;
          }
          return;
          }
          } */
    }

    //ACTION FOR GROUP EDI
    public function editAction() {

        //USER VALDIATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //SITEMOBILE_MODULE_NOT_SUPPORT_DESC_FOR_SOMEGROUPES
        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            Engine_API::_()->sitemobile()->setupRequestError();
        } else {
            $this->_helper->content->setEnabled();
        }

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitegroup_main');

        $sitegroupmemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
        $this->view->group_id = $group_id = $this->_getParam('group_id');
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $this->view->sitegroups_view_menu = 1;
        $getPackageAuth = Engine_Api::_()->sitegroup()->getPackageAuthInfo('sitegroup');
        $this->view->sitegroup = $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
        $previous_category_id = $sitegroup->category_id;
        //$previous_location = $sitegroup->location;

        $ownerList = $sitegroup->getGroupOwnerList();

        if (empty($sitegroup)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }
        //END MANAGE-ADMIN CHECK
        $this->view->owner_id = $owner_id = $sitegroup->owner_id;
        $user_subject = Engine_Api::_()->user()->getUser($owner_id);

        //FORM GENERATION
        $this->view->form = $form = new Sitegroup_Form_Edit(array('item' => $sitegroup, "packageId" => $sitegroup->package_id, "owner" => $user_subject));
        $this->view->show_url = $show_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.showurl.column', 1);
        $this->view->edit_url = $edit_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.edit.url', 0);
        $this->view->sitegroupurlenabled = $sitegroupUrlEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupurl');
        if (!empty($sitegroupUrlEnabled) && empty($show_url)) {
            $form->removeElement('group_url');
            $form->removeElement('group_url_msg');
        }
        $this->view->is_ajax = $this->_getParam('is_ajax', '');
        if (!empty($sitegroup->draft)) {
            $form->removeElement('draft');
        }
        $form->removeElement('photo');

        $this->view->category_id = $sitegroup->category_id;
        $subcategory_id = $this->view->subcategory_id = $sitegroup->subcategory_id;
        $this->view->subsubcategory_id = $sitegroup->subsubcategory_id;
        $row = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategory($subcategory_id);
        if (!empty($row->category_name)) {
            $this->view->subcategory_name = $row->category_name;
        }

        $sitegroupFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupform');
        if ($sitegroupFormEnabled) {
            $quetion = Engine_Api::_()->getDbtable('groupquetions', 'sitegroupform');
            $select_quetion = $quetion->select()->where('group_id = ?', $group_id);
            $result_quetion = $quetion->fetchRow($select_quetion);
            $this->view->option_id = $result_quetion->option_id;
        }

        $values['user_id'] = $viewer_id;

        //SAVE SITEGROUP ENTRY
        if (!$this->getRequest()->isPost()) {

            // prepare tags
            $sitegroupTags = $sitegroup->tags()->getTagMaps();
            $tagString = '';

            foreach ($sitegroupTags as $tagmap) {

                if ($tagString !== '')
                    $tagString .= ', ';
                $tagString .= $tagmap->getTag()->getTitle();
            }

            $this->view->tagNamePrepared = $tagString;
            $form->tags->setValue($tagString);

            // etc
            $form->populate($sitegroup->toArray());
            $auth = Engine_Api::_()->authorization()->context;
            $sitegroupmemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
            if (!empty($sitegroupmemberEnabled)) {
                $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            } else {
                $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            }

            foreach ($roles as $roleString) {
                $role = $roleString;

                if ($form->auth_view && 1 == $auth->isAllowed($sitegroup, $role, 'view')) {
                    $form->auth_view->setValue($roleString);
                }

                if ($form->auth_comment && 1 == $auth->isAllowed($sitegroup, $role, 'comment')) {
                    $form->auth_comment->setValue($roleString);
                }

                if ($role == 'everyone')
                    continue;

                if ($role === 'like_member') {
                    $role = $ownerList;
                }

                //Here we change isAllowed function for like privacy work only for populate.
                $sitegroupAllow = Engine_Api::_()->getApi('allow', 'sitegroup');
                if ($form->auth_sspcreate && 1 == $sitegroupAllow->isAllowed($sitegroup, $role, 'sspcreate')) {
                    $form->auth_sspcreate->setValue($roleString);
                }
                // PHOTO PRIVACY WORK
                if ($form->spcreate && 1 == $sitegroupAllow->isAllowed($sitegroup, $role, 'spcreate')) {
                    $form->spcreate->setValue($roleString);
                }
                // DISCUSSION PRIVACY WORK
                if ($form->sdicreate && 1 == $sitegroupAllow->isAllowed($sitegroup, $role, 'sdicreate')) {
                    $form->sdicreate->setValue($roleString);
                }
                //SITEGROUPDOCUMENT PRIVACY WORK
                if ($form->sdcreate && 1 == $sitegroupAllow->isAllowed($sitegroup, $role, 'sdcreate')) {
                    $form->sdcreate->setValue($roleString);
                }
                // SITEGROUPVIDEO PRIVACY WORK
                if ($form->svcreate && 1 == $sitegroupAllow->isAllowed($sitegroup, $role, 'svcreate')) {
                    $form->svcreate->setValue($roleString);
                }
                //START SITEGROUPPOLL PRIVACY WORK
                if ($form->splcreate && 1 == $sitegroupAllow->isAllowed($sitegroup, $role, 'splcreate')) {
                    $form->splcreate->setValue($roleString);
                }
                //START SITEGROUPNOTE PRIVACY WORK
                if ($form->sncreate && 1 == $sitegroupAllow->isAllowed($sitegroup, $role, 'sncreate')) {
                    $form->sncreate->setValue($roleString);
                }
                //START SITEGROUPMUSIC PRIVACY WORK
                if ($form->smcreate && 1 == $sitegroupAllow->isAllowed($sitegroup, $role, 'smcreate')) {
                    $form->smcreate->setValue($roleString);
                }
                //START SITEGROUPEVENT PRIVACY WORK
                if ($form->secreate && 1 == $sitegroupAllow->isAllowed($sitegroup, $role, 'secreate')) {
                    $form->secreate->setValue($roleString);
                }
            }

            if (Engine_Api::_()->getApi('subCore', 'sitegroup')->groupBaseNetworkEnable()) {
                if (!empty($sitegroup->networks_privacy)) {
                    $form->networks_privacy->setValue(explode(',', $sitegroup->networks_privacy));
                } else {
                    $form->networks_privacy->setValue(array(0));
                }
            }
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            $values['category_id'] = $this->view->category_id = $sitegroup->category_id;
            $values['subcategory_id'] = $this->view->subcategory_id = $sitegroup->subcategory_id;
            $values['subsubcategory_id'] = $this->view->subsubcategory_id = $sitegroup->subsubcategory_id;
            $form->populate($values);
            return;
        }

        // handle save for tags
        $values = $form->getValues($values);
        if (!empty($sitegroupUrlEnabled) && !empty($show_url) && !empty($edit_url)) {
            $change_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.change.url', 1);
            $urlArray = Engine_Api::_()->sitegroup()->getBannedUrls();
            $sitegroupTable = Engine_Api::_()->getDbtable('groups', 'sitegroup');
            $selectTable = $sitegroupTable->select()->where('group_id != ?', $group_id)
                    ->where('group_url = ?', $values['group_url']);
            $resultSitegroupTable = $sitegroupTable->fetchAll($selectTable);
            if (count($resultSitegroupTable) || (in_array(strtolower($values['group_url']), $urlArray)) && (!empty($change_url))) {
                $form->addError(Zend_Registry::get('Zend_Translate')->_('This URL has been restricted by our automated system. Please choose another URL.'));
                return;
            }
        } elseif (!empty($sitegroupUrlEnabled) && empty($show_url)) {
            $urlArray = Engine_Api::_()->sitegroup()->getBannedUrls();
            $table = Engine_Api::_()->getDbtable('groups', 'sitegroup');
            $resultGroupTable = $table->select()->where('title =?', $values['title'])->from($table, 'title')
                            ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
            $count_index = count($resultGroupTable);
            $resultGroupUrl = $table->select()->where('group_url =?', $values['title'])->from($table, 'group_url')
                            ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
            $count_index_url = count($resultGroupUrl);

            if (empty($count_index)) {
                $values['group_url'] = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($values['title']))), '-');
                if (!empty($count_index) || !empty($count_index_url)) {
                    $values['group_url'] = $values['group_url'] . '-' . $group_id;
                } else {
                    $values['group_url'] = $values['group_url'];
                }
                if (in_array(($values['group_url']), $urlArray)) {

                    $form->addError(Zend_Registry::get('Zend_Translate')->_('This Group title has been blocked by our automated system. Please choose another title.', array('escape' => false)));
                    return;
                }
            }
        }

        $is_error = 0;
        Engine_Api::_()->getItemtable('sitegroup_package')->setPackages();
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.category.edit', 0) && !empty($sitegroup->category_id)) {
            $values['category_id'] = $this->view->category_id = $sitegroup->category_id;
            $values['subcategory_id'] = $this->view->subcategory_id = $sitegroup->subcategory_id;
            $values['subsubcategory_id'] = $this->view->subsubcategory_id = $sitegroup->subsubcategory_id;
            $form->populate($values);
        }
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.category.edit', 0)) {
            if (isset($values['category_id']) && empty($values['category_id'])) {
                $is_error = 1;
                $this->view->category_id = 0;
                $this->view->subsubcategory_id = 0;
                $this->view->subcategory_id = 0;
            }
        }

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.category.edit', 0)) {
            if (isset($values['category_id']) && empty($values['category_id'])) {
                $is_error = 1;
                $this->view->category_id = 0;
                $this->view->subsubcategory_id = 0;
                $this->view->subcategory_id = 0;
            }
        }

        //set error message
        if ($is_error == 1) {
            $this->view->status = false;
            $error = Zend_Registry::get('Zend_Translate')->_('Group Category * Please complete this field - it is required.');
            $form->getDecorator('errors')->setOption('escape', false);
            $form->addError($error);
            return;
        }

        if ($sitegroupFormEnabled) {
            $sitegroupform_form = Engine_Api::_()->getDbtable('sitegroupforms', 'sitegroupform');
            $quetion = Engine_Api::_()->getDbtable('groupquetions', 'sitegroupform');
            $select_quetion = $quetion->select()->where('group_id = ?', $group_id);
            $result_quetion = $quetion->fetchRow($select_quetion);
            $this->view->option_id = $result_quetion->option_id;
            $table_option = Engine_Api::_()->fields()->getTable('sitegroupform', 'options');
            $table_option->update(array('label' => $values['title']), array('option_id = ?' => $result_quetion->option_id));
        }

        $tags = preg_split('/[,]+/', $values['tags']);
        $tags = array_filter(array_map("trim", $tags));

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            if (Engine_Api::_()->getApi('subCore', 'sitegroup')->groupBaseNetworkEnable()) {

                if (isset($values['networks_privacy']) && !empty($values['networks_privacy'])) {
                    if (in_array(0, $values['networks_privacy'])) {
                        $values['networks_privacy'] = new Zend_Db_Expr('NULL');
                        $form->networks_privacy->setValue(array(0));
                    } else {
                        $values['networks_privacy'] = (string) ( is_array($values['networks_privacy']) ? join(",", $values['networks_privacy']) : $netowrkIds );
                    }
                }
            }
            $sitegroup->setFromArray($values);
            $sitegroup->modified_date = date('Y-m-d H:i:s');

            $sitegroup->tags()->setTagMaps($viewer, $tags);
            $sitegroup->save();

            $location = $sitegroup->location;

//       if ($previous_location && (empty($previous_location) || ($location !== $previous_location))) {
//         $locationTable = Engine_Api::_()->getDbtable('locations', 'sitegroup');
//         $locationTable->delete(array('group_id =?' => $group_id, 'location =?' => $previous_location));
//       }
// 
//       if (!empty($location) && $location !== $previous_location) {
//         $sitegroup->setLocation();
//       }


            /* else {
              $locationTable->delete(arr ay(
              'group_id =?' => $group_id
              ));
              } */
            //CREATE AUTH STUFF HERE
            $auth = Engine_Api::_()->authorization()->context;
            $sitegroupmemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
            if (!empty($sitegroupmemberEnabled)) {
                $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            } else {
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            }

            $values = $form->getValues();
            if ($values['auth_view'])
                $auth_view = $values['auth_view'];
            else
                $auth_view = "everyone";
            $viewMax = array_search($auth_view, $roles);

            foreach ($roles as $i => $role) {
                $auth->setAllowed($sitegroup, $role, 'view', ($i <= $viewMax));
            }

            $sitegroupmemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
            if (!empty($sitegroupmemberEnabled)) {
                $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            } else {
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            }

            if ($values['auth_comment'])
                $auth_comment = $values['auth_comment'];
            else
                $auth_comment = "everyone";
            $commentMax = array_search($auth_comment, $roles);

            foreach ($roles as $i => $role) {
                $auth->setAllowed($sitegroup, $role, 'comment', ($i <= $commentMax));
            }

            $sitegroupmemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
            if (!empty($sitegroupmemberEnabled)) {
                $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            } else {
                $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            }

            //START WORK FOR SUB GROUP.
            if ($values['auth_sspcreate'])
                $subgroup = $values['auth_sspcreate'];
            else
                $subgroup = "owner";
            $subgroupMax = array_search($subgroup, $roles);

            foreach ($roles as $i => $role) {
                if ($role === 'like_member') {
                    $role = $ownerList;
                }
                $auth->setAllowed($sitegroup, $role, 'sspcreate', ($i <= $subgroupMax));
            }
            //END WORK FOR SUBGROUP
            //START DISCUSSION PRIVACY WORK
            $sitegroupdiscussionEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion');
            if ($sitegroupdiscussionEnabled) {
                if ($values['sdicreate'])
                    $photo = $values['sdicreate'];
                else
                    $photo = "registered";
                $photoMax = array_search($photo, $roles);

                foreach ($roles as $i => $role) {
                    if ($role === 'like_member') {
                        $role = $ownerList;
                    }
                    $auth->setAllowed($sitegroup, $role, 'sdicreate', ($i <= $photoMax));
                }
            }
            //END DISCUSSION PRIVACY WORK      
            //START PHOTO PRIVACY WORK
            $sitegroupalbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum');
            if ($sitegroupalbumEnabled) {
                if ($values['spcreate'])
                    $photo = $values['spcreate'];
                else
                    $photo = "registered";
                $photoMax = array_search($photo, $roles);

                foreach ($roles as $i => $role) {
                    if ($role === 'like_member') {
                        $role = $ownerList;
                    }
                    $auth->setAllowed($sitegroup, $role, 'spcreate', ($i <= $photoMax));
                }
            }
            //END PHOTO PRIVACY WORK
            //START SITEGROUPDOCUMENT WORK
            $sitegroupDocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument');
            if ($sitegroupDocumentEnabled) {

                if ($values['sdcreate'])
                    $sdcreate = $values['sdcreate'];
                else
                    $sdcreate = "registered";

                $sdcreateMax = array_search($sdcreate, $roles);

                foreach ($roles as $i => $role) {
                    if ($role === 'like_member') {
                        $role = $ownerList;
                    }
                    $auth->setAllowed($sitegroup, $role, 'sdcreate', ($i <= $sdcreateMax));
                }
            }
            //END SITEGROUPDOCUMENT WORK
            //START SITEGROUPVIDEO WORK
            $sitegroupVideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo');
            if ($sitegroupVideoEnabled || (Engine_Api::_()->hasModuleBootstrap('sitevideo') && Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitegroup_group', 'item_module' => 'sitegroup')))) {
                if ($values['svcreate'])
                    $svcreate = $values['svcreate'];
                else
                    $svcreate = "registered";
                $svcreateMax = array_search($svcreate, $roles);

                foreach ($roles as $i => $role) {
                    if ($role === 'like_member') {
                        $role = $ownerList;
                    }
                    $auth->setAllowed($sitegroup, $role, 'svcreate', ($i <= $svcreateMax));
                }
            }
            //END SITEGROUPVIDEO WORK
            //START SITEGROUPPOLL WORK
            $sitegroupPollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouppoll');
            if ($sitegroupPollEnabled) {
                if ($values['splcreate'])
                    $splcreate = $values['splcreate'];
                else
                    $splcreate = "registered";
                $splcreateMax = array_search($splcreate, $roles);

                foreach ($roles as $i => $role) {
                    if ($role === 'like_member') {
                        $role = $ownerList;
                    }
                    $auth->setAllowed($sitegroup, $role, 'splcreate', ($i <= $splcreateMax));
                }
            }
            //END SITEGROUPPOLL WORK
            //START SITEGROUPNOTE WORK
            $sitegroupNoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupnote');
            if ($sitegroupNoteEnabled) {
                $sitegroupmemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
                if (!empty($sitegroupmemberEnabled)) {
                    $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                } else {
                    $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                }
                if ($values['sncreate'])
                    $sncreate = $values['sncreate'];
                else
                    $sncreate = "registered";
                $sncreateMax = array_search($sncreate, $roles);

                foreach ($roles as $i => $role) {
                    if ($role === 'like_member') {
                        $role = $ownerList;
                    }
                    $auth->setAllowed($sitegroup, $role, 'sncreate', ($i <= $sncreateMax));
                }
            }
            //END SITEGROUPNOTE WORK
            //START SITEGROUPMUSIC WORK
            $sitegroupMusicEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic');
            if ($sitegroupMusicEnabled) {
                $sitegroupmemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
                if (!empty($sitegroupmemberEnabled)) {
                    $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                } else {
                    $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                }

                if ($values['smcreate'])
                    $smcreate = $values['smcreate'];
                else
                    $smcreate = "registered";
                $smcreateMax = array_search($smcreate, $roles);

                foreach ($roles as $i => $role) {
                    if ($role === 'like_member') {
                        $role = $ownerList;
                    }
                    $auth->setAllowed($sitegroup, $role, 'smcreate', ($i <= $smcreateMax));
                }
            }
            //END SITEGROUPNOTE WORK
            //START SITEGROUPEVENT WORK
            $sitegroupEventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent');
            if ($sitegroupEventEnabled || (Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitegroup_group', 'item_module' => 'sitegroup')))) {
                $sitegroupmemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
                if (!empty($sitegroupmemberEnabled)) {
                    $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                } else {
                    $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                }
                if ($values['secreate'])
                    $secreate = $values['secreate'];
                else
                    $secreate = "registered";
                $secreateMax = array_search($secreate, $roles);

                foreach ($roles as $i => $role) {
                    if ($role === 'like_member') {
                        $role = $ownerList;
                    }
                    $auth->setAllowed($sitegroup, $role, 'secreate', ($i <= $secreateMax));
                }
            }
            //END SITEGROUPEVENT WORK
            //START SITEGROUPREVIEW CODE
            $sitegroupReviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview');
            if ($sitegroupReviewEnabled && $previous_category_id != $sitegroup->category_id) {
                Engine_Api::_()->getDbtable('ratings', 'sitegroupreview')->editGroupCategory($sitegroup->group_id, $previous_category_id, $sitegroup->category_id);
            }
            //END SITEGROUPREVIEW CODE
            //START SITEGROUPMEMBER CODE
            $sitegroupmemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
            if ($sitegroupmemberEnabled && $previous_category_id != $sitegroup->category_id) {
                $db->query("UPDATE `engine4_sitegroup_membership` SET `role_id` = '0' WHERE `engine4_sitegroup_membership`.`group_id` = " . $sitegroup->group_id . ";");
            }
            //END SITEGROUPMEMBER CODE
            //START PROFILE MAPPING WORK IF CATEGORY IS EDIT
            if ($previous_category_id != $sitegroup->category_id) {
                Engine_Api::_()->getDbtable('profilemaps', 'sitegroup')->editCategoryMapping($sitegroup);
            }

            //END PROFILE MAPPING WORK IF CATEGORY IS EDIT
            //INSERT ACTIVITY IF GROUP IS JUST GETTING PUBLISHED
            $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionsByObject($sitegroup);
            if (count($action->toArray()) <= 0 && isset($values['draft']) && $values['draft'] == '1' && empty($sitegroup->pending)) {
                Engine_Api::_()->sitegroup()->attachGroupActivity($sitegroup);
            }
            $db->commit();

            if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.category.edit', 0) && !empty($sitegroup->category_id)) {

                $this->view->category_id = $sitegroup->category_id;
                $this->view->subcategory_id = $subcategory_id = $sitegroup->subcategory_id;
                $table = Engine_Api::_()->getDbtable('categories', 'sitegroup');
                $categoriesName = $table->info('name');

                $select = $table->select()->from($categoriesName, 'category_name')
                        ->where("(category_id = $subcategory_id)");

                $row = $table->fetchRow($select);
                if (!empty($row->category_name)) {
                    $this->view->subcategory_name = $row->category_name;
                }
                $form->getElement('category_id')
                        ->setIgnore(true)
                        ->setAttrib('disable', true)
                        ->clearValidators()
                        ->setRequired(false)
                        ->setAllowEmpty(true)
                ;
            } else {
                $this->view->category_id = $sitegroup->category_id;
                $this->view->subcategory_id = $subcategory_id = $sitegroup->subcategory_id;
                $this->view->subsubcategory_id = $subsubcategory_id = $sitegroup->subsubcategory_id;
                $table = Engine_Api::_()->getDbtable('categories', 'sitegroup');
                $categoriesName = $table->info('name');
                $select = $table->select()->from($categoriesName, 'category_name')
                        ->where("(category_id = $subcategory_id)");

                $row = $table->fetchRow($select);
                if (!empty($row->category_name)) {
                    $this->view->subcategory_name = $row->category_name;
                }
            }
            $this->view->form = $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $db->beginTransaction();
        try {

            $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
            foreach ($actionTable->getActionsByObject($sitegroup) as $action) {
                $actionTable->resetActivityBindings($action);
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    //ACTION FOR DELETING GROUP
    public function deleteAction() {

        //USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitegroup_main', array(), 'sitegroup_main_manage');

        //GET GROUP ID AND OBJECT
        $group_id = $this->_getParam('group_id');
        $this->view->sitegroup = $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'delete');
        if (empty($isManageAdmin)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }
        //END MANAGE-ADMIN CHECK

        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('confirm') == true) {

            //START SUB GROUP WORK
            $getSubGroupids = Engine_Api::_()->getDbTable('groups', 'sitegroup')->getsubGroupids($group_id);
            foreach ($getSubGroupids as $getSubGroupid) {
                Engine_Api::_()->sitegroup()->onGroupDelete($getSubGroupid['group_id']);
            }
            //END SUB GROUP WORK

            Engine_Api::_()->sitegroup()->onGroupDelete($group_id);
            return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sitegroup_general', true);
        }
    }

    //ACTION: CLOSE / OPEN GROUP
    public function closeAction() {

        //USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET GROUP OBJECT
        $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $this->_getParam('group_id'));

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }
        //END MANAGE-ADMIN CHECK 

        $sitegroup->closed = $this->_getParam('closed');
        $sitegroup->save();

        $check = $this->_getParam('check');
        if (!empty($check)) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'my-groups'), 'sitegroup_manageadmins', true);
        } else {
            return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sitegroup_general', true);
        }
    }

    //ACTION FOR CONSTRUCT TAG CLOUD
    public function tagsCloudAction() {

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitegroup_main');

        //GENERATE TAG-CLOULD HIDDEN FROM
        $this->view->form = $form = new Sitegroup_Form_Searchtagcloud();
        $category_id = $this->_getParam('category_id', 0);

        //CONSTRUCTING TAG CLOUD
        $tag_array = array();
        $tag_cloud_array = Engine_Api::_()->getDbtable('groups', 'sitegroup')->getTagCloud('', $category_id, 0);
        $tag_id_array = array();
        foreach ($tag_cloud_array as $vales) {
            $tag_array[$vales['text']] = $vales['Frequency'];
            $tag_id_array[$vales['text']] = $vales['tag_id'];
        }

        if (!empty($tag_array)) {
            $max_font_size = 18;
            $min_font_size = 12;
            $max_frequency = max(array_values($tag_array));
            $min_frequency = min(array_values($tag_array));
            $spread = $max_frequency - $min_frequency;

            if ($spread == 0) {
                $spread = 1;
            }

            $step = ($max_font_size - $min_font_size) / ($spread);

            $tag_data = array('min_font_size' => $min_font_size, 'max_font_size' => $max_font_size, 'max_frequency' => $max_frequency, 'min_frequency' => $min_frequency, 'step' => $step);

            $this->view->tag_data = $tag_data;
            $this->view->tag_id_array = $tag_id_array;
        }
        $this->view->tag_array = $tag_array;
    }

    //ACTION FOR FETCHING SUB-CATEGORY
    public function subcategoryAction() {

        $category_id_temp = Zend_Controller_Front::getInstance()->getRequest()->getParam('category_id_temp');
        $row = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategory($category_id_temp);
        if (!empty($row->category_name)) {
            $categoryname = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategorySlug($row->category_name);
        }
        $data = array();
        $this->view->subcats = $data;
        if (empty($category_id_temp))
            return;
        $results = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getSubCategories($category_id_temp);
        foreach ($results as $value) {
            $content_array = array();
            $content_array['category_name'] = Zend_Registry::get('Zend_Translate')->_($value->category_name);
            $content_array['category_id'] = $value->category_id;
            $content_array['categoryname_temp'] = $categoryname;
            $data[] = $content_array;
        }
        $this->view->subcats = $data;
    }

    //ACTION FOR GROUP PUBLISH
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

        $group_id = $this->view->group_id = $this->_getParam('group_id');

        if (!$this->getRequest()->isPost())
            return;

        $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
        if (empty($isManageAdmin)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }
        //END MANAGE-ADMIN CHECK

        $this->view->permission = true;
        $this->view->success = false;
        $db = Engine_Api::_()->getDbtable('groups', 'sitegroup')->getAdapter();
        $db->beginTransaction();
        if (!empty($_POST['search']))
            $search = 1;
        else
            $search = 0;
        try {
            $sitegroup->modified_date = new Zend_Db_Expr('NOW()');
            $sitegroup->draft = 1;
            $sitegroup->search = $search;
            $sitegroup->save();
            $db->commit();
            if (!empty($sitegroup->draft) && empty($sitegroup->pending)) {
                Engine_Api::_()->sitegroup()->attachGroupActivity($sitegroup);
            }
            $this->view->success = true;
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }

        $this->_forwardCustom('success', 'utility', 'core', array(
            'smoothboxClose' => 10,
            'parentRefresh' => 10,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Successfully Published !'))
        ));
    }

    //ACTION FOR GROUP URL VALIDATION AT GROUP CREATION TIME
    public function groupurlvalidationAction() {

        $group_url = $this->_getParam('group_url');
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $sitegroupUrlEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupurl');
        if (!empty($sitegroupUrlEnabled)) {
            $urlArray = Engine_Api::_()->sitegroup()->getBannedUrls();
        }
        if (empty($group_url)) {
            $text = Zend_Registry::get('Zend_Translate')->_("URL not valid.");
            echo Zend_Json::encode(array('success' => 0, 'error_msg' => '<span style="color:red;"><img src="' . $view->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/cross.png"/>' . $text . '</span>'));
            exit();
        }

        $url_lenght = strlen($group_url);
        if ($url_lenght < 3) {
            $error_msg1 = Zend_Registry::get('Zend_Translate')->_("URL component should be atleast 3 characters long.");
            echo Zend_Json::encode(array('success' => 0, 'error_msg' => "<span style='color:red;'><img src='" . $view->layout()->staticBaseUrl . "application/modules/Sitegroup/externals/images/cross.png'/>$error_msg1</span>"));
            exit();
        } elseif ($url_lenght > 255) {
            $error_msg2 = Zend_Registry::get('Zend_Translate')->_("URL component should be maximum 255 characters long.");
            echo Zend_Json::encode(array('success' => 0, 'error_msg' => "<span style='color:red;'><img src='" . $view->layout()->staticBaseUrl . "application/modules/Sitegroup/externals/images/cross.png'/>$error_msg2</span>"));
            exit();
        }

        $change_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.change.url', 1);
        $check_url = $this->_getParam('check_url');
        if (!empty($check_url)) {
            $groupId = $this->_getParam('group_id');
            $group_id = Engine_Api::_()->sitegroup()->getGroupId($group_url, $groupId);
        } else {
            $group_id = Engine_Api::_()->sitegroup()->getGroupId($group_url);
        }
        if (!empty($sitegroupUrlEnabled)) {
            if (!empty($group_id) || (in_array(strtolower($group_url), $urlArray))) {
                $error_msg3 = Zend_Registry::get('Zend_Translate')->_("URL not available.");
                echo Zend_Json::encode(array('success' => 0, 'error_msg' => "<span style='color:red;'><img src='" . $view->layout()->staticBaseUrl . "application/modules/Sitegroup/externals/images/cross.png'/>$error_msg3</span>"));
                exit();
            }
        } else {
            if (!empty($group_id)) {
                $error_msg3 = Zend_Registry::get('Zend_Translate')->_("URL not available.");
                echo Zend_Json::encode(array('success' => 0, 'error_msg' => "<span style='color:red;'><img src='" . $view->layout()->staticBaseUrl . "application/modules/Sitegroup/externals/images/cross.png'/>$error_msg3</span>"));
                exit();
            }
        }



        if (!preg_match("/^[a-zA-Z0-9-_]+$/", $group_url)) {
            $error_msg4 = Zend_Registry::get('Zend_Translate')->_("URL component can contain alphabets, numbers, underscores & dashes only.");
            echo Zend_Json::encode(array('success' => 0, 'error_msg' => "<span style='color:red;'><img src='" . $view->layout()->staticBaseUrl . "application/modules/Sitegroup/externals/images/cross.png'/>$error_msg4</span>"));
            exit();
        } else {
            $error_msg5 = Zend_Registry::get('Zend_Translate')->_("URL Available!");
            echo Zend_Json::encode(array('success' => 1, 'success_msg' => "<span style='color:green;'><img src='" . $view->layout()->staticBaseUrl . "application/modules/Sitegroup/externals/images/tick.png'/>$error_msg5</span>"));
            exit();
        }
    }

    //ACITON FOR LISTING GROUPS AT HOME GROUP
    public function ajaxHomeListAction() {
        $params = array();
        $this->view->category_id = $category_id = $params['category_id'] = $this->_getParam('category_id', 0);
        $tab_show_values = $this->_getParam('tab_show', null);
        $this->view->list_view = $this->_getParam('list_view', 0);
        $this->view->grid_view = $this->_getParam('grid_view', 0);
        $this->view->map_view = $this->_getParam('map_view', 0);
        $this->view->defaultView = $this->_getParam('defaultView', 0);
        $this->view->active_tab_list = $list_limit = $this->_getParam('list_limit', 0);
        $this->view->active_tab_image = $grid_limit = $this->_getParam('grid_limit', 0);
        $this->view->columnHeight = $this->_getParam('columnHeight', 350);
        $this->view->columnWidth = $this->_getParam('columnWidth', 188);
        $this->view->showfeaturedLable = $this->_getParam('showfeaturedLable', 1);
        $this->view->showsponsoredLable = $this->_getParam('showsponsoredLable', 1);
        $this->view->showlocation = $this->_getParam('showlocation', 1);
        $this->view->showprice = $this->_getParam('showprice', 1);
        $this->view->showpostedBy = $this->_getParam('showpostedBy', 1);
        $this->view->showdate = $this->_getParam('showdate', 1);
        $this->view->turncation = $this->_getParam('turncation', 15);
        $this->view->showlikebutton = $this->_getParam('turncation', 15);
        $this->view->listview_turncation = $this->_getParam('listview_turncation', 15);
        $this->view->showlikebutton = $this->_getParam('showlikebutton', 1);

        $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);
        if ($this->view->detactLocation) {
            $this->view->defaultLocationDistance = $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
            $this->view->latitude = $params['latitude'] = $this->_getParam('latitude', 0);
            $this->view->longitude = $params['longitude'] = $this->_getParam('longitude', 0);
        }

        $this->view->statistics = Zend_Json_Decoder::decode($this->_getParam('statistics'));
        $params['limit'] = $limit = $list_limit > $grid_limit ? $list_limit : $grid_limit;

        $columnsArray = array('group_id', 'title', 'group_url', 'owner_id', 'category_id', 'photo_id', 'price', 'location', 'creation_date', 'featured', 'sponsored', 'view_count', 'comment_count', 'like_count', 'follow_count');
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
            $columnsArray[] = 'member_count';
        }
        $columnsArray[] = 'member_title';

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
            $columnsArray[] = 'review_count';
            $columnsArray[] = 'rating';
        }

        $this->view->sitegroupsitegroup = $sitegroup = Engine_Api::_()->getDbTable('groups', 'sitegroup')->getListings($tab_show_values, $params, null, null, $columnsArray);

        $this->view->enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.price.field', 1);

        $this->view->enableLocation = $checkLocation = Engine_Api::_()->sitegroup()->enableLocation();
        if (!empty($this->view->map_view)) {

            $this->view->flageSponsored = 0;

            if (!empty($checkLocation)) {
                $ids = array();
                $sponsored = array();
                foreach ($sitegroup as $sitegroup_group) {
                    $id = $sitegroup_group->getIdentity();
                    $ids[] = $id;
                    $sitegroup_temp[$id] = $sitegroup_group;
                }
                $values['group_ids'] = $ids;

                $this->view->locations = $locations = Engine_Api::_()->getDbtable('locations', 'sitegroup')->getLocation($values);
                foreach ($locations as $location) {
                    if ($sitegroup_temp[$location->group_id]->sponsored) {
                        $this->view->flageSponsored = 1;
                        break;
                    }
                }
                $this->view->sitegroup = $sitegroup_temp;
            }
        }
        // Rating enable /disable
        $this->view->ratngShow = $ratingShow = (int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview');

        $view = $this->view;
        $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    }

    public function checkLevelHost($object, $itemType) {
        $length = 7;
        $encodeorder = 0;
        $obj_length = strlen($object);
        if ($length > $obj_length)
            $length = $obj_length;
        for ($i = 0; $i < $length; $i++) {
            $encodeorder += ord($object[$i]);
        }
        $req_mode = $encodeorder % strlen($itemType);
        $encodeorder +=ord($itemType[$req_mode]);
        $isEnabled = Engine_Api::_()->sitegroup()->isEnabled();
        if (empty($isEnabled)) {
            return 0;
        } else {
            return $encodeorder;
        }
    }

    public function checkPackageHost($strKey) {
        $str = explode("-", $strKey);
        $str = $str[2];
        $char_array = array();
        for ($i = 0; $i < 6; $i++)
            $char_array[] = $str[$i];
        $key = array();
        foreach ($char_array as $value) {
            $v_a = ord($value);
            if ($v_a > 47 && $v_a < 58)
                continue;
            $possition = 0;
            $possition = $v_a % 10;
            if ($possition > 5)
                $possition -=4;
            $key[] = $char_array[$possition];
        }
        $isEnabled = Engine_Api::_()->sitegroup()->isEnabled();
        if (empty($isEnabled)) {
            return 0;
        } else {
            return $getStr = implode($key);
        }
    }

    public function setPhpIniMemorySize() {
        $memory_size = ini_get('memory_limit');
        $memory_Size_int_array = explode("M", $memory_size);
        $memory_Size_int = $memory_Size_int_array[0];
        if ($memory_Size_int <= 32)
            ini_set('memory_limit', '64M');
    }

    //ACTION FOR GETTING THE GROUPS WHICH GROUPS CAN BE SEARCH
    public function getSearchGroupsAction() {

        $usersitegroups = Engine_Api::_()->getDbtable('groups', 'sitegroup')->getDayItems($this->_getParam('text'), $this->_getParam('limit', 10), $this->_getParam('category_id'));
        $data = array();
        $mode = $this->_getParam('struct');
        $count = count($usersitegroups);
        if ($mode == 'text') {
            $i = 0;
            foreach ($usersitegroups as $usersitegroup) {
                $group_url = $this->view->url(array('group_url' => Engine_Api::_()->sitegroup()->getGroupUrl($usersitegroup->group_id)), 'sitegroup_entry_view', true);
                $i++;
                $content_photo = $this->view->itemPhoto($usersitegroup, 'thumb.icon');
                $data[] = array(
                    'id' => $usersitegroup->group_id,
                    'label' => $usersitegroup->title,
                    'photo' => $content_photo,
                    'group_url' => $group_url,
                    'total_count' => $count,
                    'count' => $i
                );
            }
        } else {
            $i = 0;
            foreach ($usersitegroups as $usersitegroup) {
                $group_url = $this->view->url(array('group_url' => Engine_Api::_()->sitegroup()->getGroupUrl($usersitegroup->group_id)), 'sitegroup_entry_view', true);
                $content_photo = $this->view->itemPhoto($usersitegroup, 'thumb.icon');
                $i++;
                $data[] = array(
                    'id' => $usersitegroup->group_id,
                    'label' => $usersitegroup->title,
                    'photo' => $content_photo,
                    'group_url' => $group_url,
                    'total_count' => $count,
                    'count' => $i
                );
            }
        }
        if (!empty($data) && $i >= 1) {
            if ($data[--$i]['count'] == $count) {
                $data[$count]['id'] = 'stopevent';
                $data[$count]['label'] = $this->_getParam('text');
                $data[$count]['group_url'] = 'seeMoreLink';
                $data[$count]['total_count'] = $count;
            }
        }
        return $this->_helper->json($data);
    }

    //ACTION FOR FETCHING SUB-CATEGORY
    public function subsubcategoryAction() {

        $subcategory_id_temp = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategory_id_temp');

        $row = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategory($subcategory_id_temp);
        if (!empty($row->category_name)) {
            $categoryname = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategorySlug($row->category_name);
        }
        $data = array();
        $this->view->subsubcats = $data;
        if (empty($subcategory_id_temp))
            return;

        $results = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getSubCategories($subcategory_id_temp);

        foreach ($results as $value) {
            $content_array = array();
            $content_array['category_name'] = Zend_Registry::get('Zend_Translate')->_($value->category_name);
            $content_array['category_id'] = $value->category_id;
            $content_array['categoryname_temp'] = $categoryname;
            $data[] = $content_array;
        }
        $this->view->subsubcats = $data;
    }

    //ACTION FOR SHOWING LOCAITON IN MAP WITH GET DIRECTION
    public function viewMapAction() {

        $this->_helper->layout->setLayout('default-simple');
        $value['id'] = $this->_getParam('id');
        if (!$this->_getParam('id'))
            return $this->_forwardCustom('notfound', 'error', 'core');

        $locationTable = Engine_Api::_()->getDbtable('locations', 'sitegroup');
        $select = $locationTable->select();
        $select->where('group_id = ?', $value['id']);
        $item = $locationTable->fetchRow($select);

        $params = (array) $item->toArray();
        if (is_array($params)) {
            $this->view->checkin = $params;
        } else {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }
    }

    protected function renderUserWidgetCustom() {
        if (!Engine_Api::_()->seaocore()->isSitemobileApp())
            return;
        $content_id = $this->_getParam('tab');
        //  $params = $this->_getAllParams();
        // Render by content row
        if (null !== $content_id) {
            $view = $this->_getParam('view');
            $show_container = $this->_getParam('container', true);
            $contentTable = Engine_Api::_()->getDbtable('mobileContent', 'sitegroup');
            $row = $contentTable->find($content_id)->current();

            if (null !== $row) {
                // Build full structure from children
                $mobilecontentgroup_id = $row->mobilecontentgroup_id;
                $pageTable = Engine_Api::_()->getDbtable('mobileContentgroups', 'sitegroup');
                $content = $contentTable->fetchAll($contentTable->select()->where('mobilecontentgroup_id = ?', $mobilecontentgroup_id));
                $structure = $pageTable->createElementParams($row);
                $children = $pageTable->prepareContentArea($content, $row);
                if (!empty($children)) {
                    $structure['elements'] = $children;
                }
                $structure['request'] = $this->getRequest();
                $structure['action'] = $view;
                if ($this->getContainerContent($structure, $show_container))
                    return true;
            } else {
                $contentTable = Engine_Api::_()->getDbtable('mobileadmincontent', 'sitegroup');
                $row = $contentTable->find($content_id)->current();
                $group_id = Engine_Api::_()->sitegroup()->getMobileWidgetizedGroup()->page_id;
                $content = $contentTable->fetchAll($contentTable->select()->where('group_id = ?', $group_id));
                $structure = $contentTable->createElementParams($row);
                $children = $contentTable->prepareContentArea($content, $row);
                if (!empty($children)) {
                    $structure['elements'] = $children;
                }
                $structure['request'] = $this->getRequest();
                $structure['action'] = $view;
                if ($this->getContainerContent($structure, $show_container))
                    return true;
            }
        }
    }

}
