<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Dashboardmenus.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Plugin_Dashboardmenus {

  public function onMenuInitialize_SitepageDashboardGetstarted($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }

    return array(
      'label' => $row->label,
      'route' => 'sitepage_dashboard',
      'action' => 'get-started',
      'class' => 'ajax_dashboard_enabled',
      'params' => array(
          'page_id' => $sitepage->getIdentity()
      ),
    );
  }

  public function onMenuInitialize_SitepageDashboardEditinfo($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }

    return array(
        'label' => $row->label,
        'route' => 'sitepage_edit',
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'page_id' => $sitepage->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardProfilepicture($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }

    return array(
        'label' => $row->label,
        'route' => 'sitepage_dashboard',
        'action' => 'profile-picture',
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'page_id' => $sitepage->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardOverview($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }
    $overviewPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'overview');
    if (empty($overviewPrivacy)) {
      return false;
    }
    return array(
        'label' => $row->label,
        'route' => 'sitepage_dashboard',
        'action' => 'overview',
        'params' => array(
            'page_id' => $sitepage->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardContact($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }
    
    $contactPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'contact');
    if (empty($contactPrivacy)) {
      return false;
    }
    
    $contactSpicifyFileds = 0;
    $pageOwner = Engine_Api::_()->user()->getUser($sitepage->owner_id);
    $view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $pageOwner, 'contact_detail');
    $availableLabels = array('phone' => 'Phone', 'website' => 'Website', 'email' => 'Email',);
    $options_create = array_intersect_key($availableLabels, array_flip($view_options));
    if (!empty($options_create)) {
      $contactSpicifyFileds = 1;
    }
    
    if (empty($contactSpicifyFileds)) {
      return false;
    }
    
    return array(
        'label' => $row->label,
        'route' => 'sitepage_dashboard',
        'action' => 'contact',
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'page_id' => $sitepage->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardManagememberroles($row) {

    $sitepageMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
    if (empty($sitepageMemberEnabled)) {
      return false;
    }
    
    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagemember.category.settings', 1) == 1) {
      return false;
    }
    
    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }

    return array(
        'label' => $row->label,
        'route' => 'sitepage_dashboard',
        'action' => 'manage-member-category',
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'page_id' => $sitepage->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardAnnouncements($row) {
    
    //$sitepageMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
    if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
      return false;
    }
    
    $pageannoucement = Engine_Api::_()->getApi('settings', 'core')->getSetting('pagemember.announcement', 1);
    if (empty($pageannoucement)) {
      return false;
    }
    
    $sitepagememberGetAnnouucement = Zend_Registry::isRegistered('sitepagememberGetAnnouucement') ? Zend_Registry::get('sitepagememberGetAnnouucement') : null;
    if (empty($sitepagememberGetAnnouucement)) {
      return false;
    }
    
    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }
    
    $allowPage = Engine_Api::_()->sitepage()->allowInThisPage($sitepage, "sitepagemember", 'smecreate');
    if (empty($allowPage)) {
        return false;
    }

    return array(
        'label' => $row->label,
        'route' => 'sitepage_dashboard',
        'action' => 'announcements',
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'page_id' => $sitepage->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardAlllocation($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }
    
    $multipleLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.multiple.location', 0);
    if (empty($multipleLocation)) {
      return false;
    }
    
    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }

    if (!Engine_Api::_()->sitepage()->enableLocation()) {
      return false;
    }
    
    $mapPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'map');
    if (empty($mapPrivacy)) {
      return false;
    }
    
    return array(
        'label' => $row->label,
        'route' => 'sitepage_dashboard',
        'action' => 'all-location',
        'params' => array(
            'page_id' => $sitepage->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardEditlocation($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }
    
    $multipleLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.multiple.location', 0);
    if (!empty($multipleLocation)) {
      return false;
    }
    
    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }

    if (!Engine_Api::_()->sitepage()->enableLocation()) {
      return false;
    }
    $mapPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'map');
    if (empty($mapPrivacy)) {
      return false;
    }
    
    return array(
        'label' => $row->label,
        'route' => 'sitepage_dashboard',
        'action' => 'edit-location',
        'params' => array(
            'page_id' => $sitepage->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardProfiletype($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }
    
    $profileTypePrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'profile');
    if (empty($profileTypePrivacy)) {
      return false;
    }
    
    return array(
        'label' => $row->label,
        'route' => 'sitepage_dashboard',
        'action' => 'profile-type',
        'params' => array(
            'page_id' => $sitepage->getIdentity(),
            'profile_type' => $sitepage->profile_type
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardApps($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }

    if (!Engine_Api::_()->sitepage()->getEnabledSubModules()) {
      return false;
    }
    return array(
        'label' => $row->label,
        'route' => 'sitepage_dashboard',
        'action' => 'app',
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'page_id' => $sitepage->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardMarketing($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }

    return array(
        'label' => $row->label,
        'route' => 'sitepage_dashboard',
        'action' => 'marketing',
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'page_id' => $sitepage->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardNotificationsettings($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }

    return array(
        'label' => $row->label,
        'route' => 'sitepage_dashboard',
        'action' => 'notification-settings',
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'page_id' => $sitepage->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardInsights($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }
    
    $insightPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'insight');
    if (empty($insightPrivacy)) {
      return false;
    }
    
    return array(
        'label' => $row->label,
        'route' => 'sitepage_insights',
        'params' => array(
            'page_id' => $sitepage->getIdentity(),
            'action' => 'index',
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardReports($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }
    
    $insightPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'insight');
    if (empty($insightPrivacy)) {
      return false;
    }
    
    return array(
        'label' => $row->label,
        'route' => 'sitepage_reports',
        'params' => array(
            'page_id' => $sitepage->getIdentity(),
            'action'  => 'export-report',
            'active' => false
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardBadge($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }
    
    $sitepageBadgeEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagebadge');
    if (empty($sitepageBadgeEnabled)) {
      return false;
    }
    
    if (!empty($sitepageBadgeEnabled)) {
      $badgePrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'badge');
      if (!empty($badgePrivacy)) {
        $badgeCount = Engine_Api::_()->sitepagebadge()->badgeCount();
      }
    }
    if (empty($badgeCount)) {
      return false;
    }
    return array(
        'label' => $row->label,
        'route' => 'sitepagebadge_request',
        //	'action' => 'edit-style',
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'page_id' => $sitepage->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardManageadmins($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.manageadmin', 1)) {
      return false;
    }
    
    return array(
        'label' => $row->label,
        'route' => 'sitepage_manageadmins',
        'action' => 'index',
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'page_id' => $sitepage->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardFeaturedowners($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }
    
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.manageadmin', 1)) {
      return false;
    }
    
    return array(
        'label' => $row->label,
        'route' => 'sitepage_dashboard',
        'action' => 'featured-owners',
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'page_id' => $sitepage->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardEditstyle($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }
    
    if (!Engine_Api::_()->sitepage()->allowStyle()) {
      return false;
    }
    
    return array(
        'label' => $row->label,
        'route' => 'sitepage_dashboard',
        'action' => 'edit-style',
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'page_id' => $sitepage->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardEditlayout($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }
    
    if (!Engine_Api::_()->getApi('settings', 'core')->sitepage_layoutcreate) {
      return false;
    }
    
    return array(
        'label' => $row->label,
        'route' => 'sitepage_layout',
        'params' => array(
            'page_id' => $sitepage->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardUpdatepackages($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }
    
    if (!Engine_Api::_()->sitepage()->hasPackageEnable()) {
      return false;
    }
    return array(
        'label' => $row->label,
        'route' => 'sitepage_packages',
        'action' => 'update-package',
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'page_id' => $sitepage->getIdentity()
        ),
    );
  }
}