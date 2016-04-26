<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Dashboardmenus.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Plugin_Dashboardmenus {

  public function onMenuInitialize_SitegroupDashboardGetstarted($row) {

    //GET GROUP ID AND SITEGROUP OBJECT
    $group_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('group_id', null);
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    if ($sitegroup->getType() !== 'sitegroup_group') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }

    return array(
      'label' => $row->label,
      'route' => 'sitegroup_dashboard',
      'action' => 'get-started',
      'class' => 'ajax_dashboard_enabled',
      'params' => array(
          'group_id' => $sitegroup->getIdentity()
      ),
    );
  }

  public function onMenuInitialize_SitegroupDashboardEditinfo($row) {

    //GET GROUP ID AND SITEGROUP OBJECT
    $group_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('group_id', null);
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    if ($sitegroup->getType() !== 'sitegroup_group') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }

    return array(
        'label' => $row->label,
        'route' => 'sitegroup_edit',
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'group_id' => $sitegroup->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitegroupDashboardProfilepicture($row) {

    //GET GROUP ID AND SITEGROUP OBJECT
    $group_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('group_id', null);
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    if ($sitegroup->getType() !== 'sitegroup_group') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }

    return array(
        'label' => $row->label,
        'route' => 'sitegroup_dashboard',
        'action' => 'profile-picture',
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'group_id' => $sitegroup->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitegroupDashboardOverview($row) {

    //GET GROUP ID AND SITEGROUP OBJECT
    $group_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('group_id', null);
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    if ($sitegroup->getType() !== 'sitegroup_group') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }
    $overviewPrivacy = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'overview');
    if (empty($overviewPrivacy)) {
      return false;
    }
    return array(
        'label' => $row->label,
        'route' => 'sitegroup_dashboard',
        'action' => 'overview',
        'params' => array(
            'group_id' => $sitegroup->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitegroupDashboardContact($row) {

    //GET GROUP ID AND SITEGROUP OBJECT
    $group_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('group_id', null);
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    if ($sitegroup->getType() !== 'sitegroup_group') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }
    
    $contactPrivacy = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'contact');
    if (empty($contactPrivacy)) {
      return false;
    }
    
    $contactSpicifyFileds = 0;
    $groupOwner = Engine_Api::_()->user()->getUser($sitegroup->owner_id);
    $view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitegroup_group', $groupOwner, 'contact_detail');
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
        'route' => 'sitegroup_dashboard',
        'action' => 'contact',
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'group_id' => $sitegroup->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitegroupDashboardManagememberroles($row) {

    $sitegroupMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
    if (empty($sitegroupMemberEnabled)) {
      return false;
    }
    
    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupmember.category.settings', 1) == 1) {
      return false;
    }
    
    //GET GROUP ID AND SITEGROUP OBJECT
    $group_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('group_id', null);
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    if ($sitegroup->getType() !== 'sitegroup_group') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }

    return array(
        'label' => $row->label,
        'route' => 'sitegroup_dashboard',
        'action' => 'manage-member-category',
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'group_id' => $sitegroup->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitegroupDashboardAnnouncements($row) {
    
    //$sitegroupMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
    if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
      return false;
    }
    
    $groupannoucement = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.announcement', 1);
    if (empty($groupannoucement)) {
      return false;
    }
    
    $sitegroupmemberGetAnnouucement = Zend_Registry::isRegistered('sitegroupmemberGetAnnouucement') ? Zend_Registry::get('sitegroupmemberGetAnnouucement') : null;
    if (empty($sitegroupmemberGetAnnouucement)) {
      return false;
    }
    
    //GET GROUP ID AND SITEGROUP OBJECT
    $group_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('group_id', null);
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    if ($sitegroup->getType() !== 'sitegroup_group') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }
    
    $allowGroup = Engine_Api::_()->sitegroup()->allowInThisGroup($sitegroup, "sitegroupmember", 'smecreate');
    if (empty($allowGroup)) {
      return false;
    }

    return array(
        'label' => $row->label,
        'route' => 'sitegroup_dashboard',
        'action' => 'announcements',
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'group_id' => $sitegroup->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitegroupDashboardAlllocation($row) {

    //GET GROUP ID AND SITEGROUP OBJECT
    $group_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('group_id', null);
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    if ($sitegroup->getType() !== 'sitegroup_group') {
      return false;
    }
    
    $multipleLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.multiple.location', 0);
    if (empty($multipleLocation)) {
      return false;
    }
    
    $editPrivacy = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }

    if (!Engine_Api::_()->sitegroup()->enableLocation()) {
      return false;
    }
    
    $mapPrivacy = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'map');
    if (empty($mapPrivacy)) {
      return false;
    }
    
    return array(
        'label' => $row->label,
        'route' => 'sitegroup_dashboard',
        'action' => 'all-location',
        'params' => array(
            'group_id' => $sitegroup->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitegroupDashboardEditlocation($row) {

    //GET GROUP ID AND SITEGROUP OBJECT
    $group_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('group_id', null);
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    if ($sitegroup->getType() !== 'sitegroup_group') {
      return false;
    }
    
    $multipleLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.multiple.location', 0);
    if (!empty($multipleLocation)) {
      return false;
    }
    
    $editPrivacy = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }

    if (!Engine_Api::_()->sitegroup()->enableLocation()) {
      return false;
    }
    $mapPrivacy = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'map');
    if (empty($mapPrivacy)) {
      return false;
    }
    
    return array(
        'label' => $row->label,
        'route' => 'sitegroup_dashboard',
        'action' => 'edit-location',
        'params' => array(
            'group_id' => $sitegroup->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitegroupDashboardProfiletype($row) {

    //GET GROUP ID AND SITEGROUP OBJECT
    $group_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('group_id', null);
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    if ($sitegroup->getType() !== 'sitegroup_group') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }
    
    $profileTypePrivacy = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'profile');
    if (empty($profileTypePrivacy)) {
      return false;
    }
    
    return array(
        'label' => $row->label,
        'route' => 'sitegroup_dashboard',
        'action' => 'profile-type',
        'params' => array(
            'group_id' => $sitegroup->getIdentity(),
            'profile_type' => $sitegroup->profile_type
        ),
    );
  }

  public function onMenuInitialize_SitegroupDashboardApps($row) {

    //GET GROUP ID AND SITEGROUP OBJECT
    $group_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('group_id', null);
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    if ($sitegroup->getType() !== 'sitegroup_group') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }

    if (!Engine_Api::_()->sitegroup()->getEnabledSubModules()) {
      return false;
    }
    return array(
        'label' => $row->label,
        'route' => 'sitegroup_dashboard',
        'action' => 'app',
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'group_id' => $sitegroup->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitegroupDashboardMarketing($row) {

    //GET GROUP ID AND SITEGROUP OBJECT
    $group_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('group_id', null);
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    if ($sitegroup->getType() !== 'sitegroup_group') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }

    return array(
        'label' => $row->label,
        'route' => 'sitegroup_dashboard',
        'action' => 'marketing',
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'group_id' => $sitegroup->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitegroupDashboardNotificationsettings($row) {

    //GET GROUP ID AND SITEGROUP OBJECT
    $group_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('group_id', null);
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    if ($sitegroup->getType() !== 'sitegroup_group') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
        $row = $sitegroup->membership()
                    ->getRow($viewer);
        if(!$row)
         return false;
                if (!$row)
           return false;

        if(!$row->active || !$row->user_approved)
				  return false;
    return array(
        'label' => $row->label,
        'route' => 'sitegroup_dashboard',
        'action' => 'notification-settings',
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'group_id' => $sitegroup->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitegroupDashboardInsights($row) {

    //GET GROUP ID AND SITEGROUP OBJECT
    $group_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('group_id', null);
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    if ($sitegroup->getType() !== 'sitegroup_group') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }
    
    $insightPrivacy = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'insight');
    if (empty($insightPrivacy)) {
      return false;
    }
    
    return array(
        'label' => $row->label,
        'route' => 'sitegroup_insights',
        'params' => array(
            'group_id' => $sitegroup->getIdentity(),
						'action' => 'index'
        ),
    );
  }

  public function onMenuInitialize_SitegroupDashboardReports($row) {

    //GET GROUP ID AND SITEGROUP OBJECT
    $group_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('group_id', null);
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    if ($sitegroup->getType() !== 'sitegroup_group') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }
    
    $insightPrivacy = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'insight');
    if (empty($insightPrivacy)) {
      return false;
    }
    
    return array(
        'label' => $row->label,
        'route' => 'sitegroup_reports',
        'params' => array(
            'group_id' => $sitegroup->getIdentity(),
            'action'  => 'export-report',
            'active' => false
        ),
    );
  }

  public function onMenuInitialize_SitegroupDashboardBadge($row) {

    //GET GROUP ID AND SITEGROUP OBJECT
    $group_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('group_id', null);
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    if ($sitegroup->getType() !== 'sitegroup_group') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }
    
    $sitegroupBadgeEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupbadge');
    if (empty($sitegroupBadgeEnabled)) {
      return false;
    }
    
    if (!empty($sitegroupBadgeEnabled)) {
      $badgePrivacy = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'badge');
      if (!empty($badgePrivacy)) {
        $badgeCount = Engine_Api::_()->sitegroupbadge()->badgeCount();
      }
    }
    if (empty($badgeCount)) {
      return false;
    }
    return array(
        'label' => $row->label,
        'route' => 'sitegroupbadge_request',
        //	'action' => 'edit-style',
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'group_id' => $sitegroup->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitegroupDashboardManageadmins($row) {

    //GET GROUP ID AND SITEGROUP OBJECT
    $group_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('group_id', null);
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    if ($sitegroup->getType() !== 'sitegroup_group') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.manageadmin', 1)) {
      return false;
    }
    
    return array(
        'label' => $row->label,
        'route' => 'sitegroup_manageadmins',
        'action' => 'index',
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'group_id' => $sitegroup->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitegroupDashboardFeaturedowners($row) {

    //GET GROUP ID AND SITEGROUP OBJECT
    $group_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('group_id', null);
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    if ($sitegroup->getType() !== 'sitegroup_group') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }
    
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.manageadmin', 1)) {
      return false;
    }
    
    return array(
        'label' => $row->label,
        'route' => 'sitegroup_dashboard',
        'action' => 'featured-owners',
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'group_id' => $sitegroup->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitegroupDashboardEditstyle($row) {

    //GET GROUP ID AND SITEGROUP OBJECT
    $group_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('group_id', null);
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    if ($sitegroup->getType() !== 'sitegroup_group') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }
    
    if (!Engine_Api::_()->sitegroup()->allowStyle()) {
      return false;
    }
    
    return array(
        'label' => $row->label,
        'route' => 'sitegroup_dashboard',
        'action' => 'edit-style',
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'group_id' => $sitegroup->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitegroupDashboardEditlayout($row) {

    //GET GROUP ID AND SITEGROUP OBJECT
    $group_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('group_id', null);
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    if ($sitegroup->getType() !== 'sitegroup_group') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }
    
    if (!Engine_Api::_()->getApi('settings', 'core')->sitegroup_layoutcreate) {
      return false;
    }
    
    return array(
        'label' => $row->label,
        'route' => 'sitegroup_layout',
        'params' => array(
            'group_id' => $sitegroup->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitegroupDashboardUpdatepackages($row) {

    //GET GROUP ID AND SITEGROUP OBJECT
    $group_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('group_id', null);
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    if ($sitegroup->getType() !== 'sitegroup_group') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }
    
    if (!Engine_Api::_()->sitegroup()->hasPackageEnable()) {
      return false;
    }
    return array(
        'label' => $row->label,
        'route' => 'sitegroup_packages',
        'action' => 'update-package',
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'group_id' => $sitegroup->getIdentity()
        ),
    );
  }
}