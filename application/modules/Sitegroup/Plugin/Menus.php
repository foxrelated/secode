<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Plugin_Menus {

  public function canCreateSitegroups() {

    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer || !$viewer->getIdentity()) {
      return false;
    }

    // Must be able to view Sitegroups
    if (!Engine_Api::_()->authorization()->isAllowed('sitegroup_group', $viewer, 'view')) {
      return false;
    }

    // Must be able to create Sitegroups
    if (!Engine_Api::_()->authorization()->isAllowed('sitegroup_group', $viewer, 'create')) {
      return false;
    }
    return true;
  }

  
  public function onMenuInitialize_SitegroupSubgroupGutterCreate($row) {

    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer || !$viewer->getIdentity()) {
      return false;
    }
    $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() !== 'sitegroup_group') {
      return false;
    }
    
    if (!empty($subject->subgroup)) {
			return false;
    }
    
    // Must be able to view Sitegroups
    if (!Engine_Api::_()->authorization()->isAllowed('sitegroup_group', $viewer, 'view')) {
      return false;
    }

    $subgroupCreate = Engine_Api::_()->sitegroup()->isManageAdmin($subject, 'sspcreate');
    if (empty($subgroupCreate) ){
			return false;
    }
    
		$isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($subject, 'sspcreate');
		if (empty($isGroupOwnerAllow)) {
			return false;
		}
		
		if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
			return array(
        'label' => $row->label,
        'route' => 'sitegroup_packages',
        'action' => 'index',
        'class' => 'buttonlink item_icon_sitegroup',
        'params' => array(
          'parent_id' =>  $subject->getIdentity()
        ),
      );
		} else {
			return array(
        'label' => $row->label,
        'route' => 'sitegroup_general',
        'action' => 'create',
        'class' => 'buttonlink item_icon_sitegroup',
        'params' => array(
          'parent_id' =>  $subject->getIdentity()
        ),
      );
		}
  }

  public function canViewSitegroups($row) {

    $viewer = Engine_Api::_()->user()->getViewer();

    // Must be able to view Sitegroups
    if (!Engine_Api::_()->authorization()->isAllowed('sitegroup_group', $viewer, 'view')) {
      return false;
    }
    
//     $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.locationfield', 1);
//     if (empty($enableLocation)) { 
// 			return false;
// 	  }

    //Group location work for navigation show.
//     if ($row->params['route'] == 'sitegroup_general' && $row->params['action'] == 'map') {
// 			$results = Engine_Api::_()->getDbtable('groups', 'sitegroup')->getLocationCount();
// 			if (empty($results)) {
// 				return false;
// 			}
// 	  }
	  //End Group location work.

    return true;
  }

  // SHOWING LINK ON "USER HOME GROUP".
  public function onMenuInitialize_CoreMainSitegroup($row) {

    $viewer = Engine_Api::_()->user()->getViewer()->getIdentity();
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    if (!empty($viewer)) {
      return array(
          'label' => $row->label,
          'icon' => $view->layout()->staticBaseUrl.'application/modules/Sitegroup/externals/images/sitegroup.png',
          'route' => 'sitegroup_general',
      );
    }
    return false;
  }

  public function onMenuInitialize_SitegroupGutterShare() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() !== 'sitegroup_group') {
      return false;
    }

    $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
    if(Engine_Api::_()->seaocore()->isSitemobileApp() && !$moduleEnabled){
       return false;
    }
    // Check share is enable/disable
    $can_share = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.share', 1);

    if (!$viewer->getIdentity() || empty($can_share)) {
      return false;
    }

    return array(
        'class' => 'smoothbox icon_sitegroups_share buttonlink',
        'route' => 'default',
        'params' => array(
            'module' => 'activity',
            'controller' => 'index',
            'action' => 'share',
            'type' => $subject->getType(),
            'id' => $subject->getIdentity(),
            'format' => 'smoothbox',
        ),
    );
  }

  public function onMenuInitialize_SitegroupGutterDelete() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() !== 'sitegroup_group') {
      return false;
    }

    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($subject, 'delete');
    if (empty($isManageAdmin)) {
      $can_delete = 0;
    } else {
      $can_delete = 1;
    }

    if (!$viewer->getIdentity() || empty($can_delete)) {
      return false;
    }

    return array(
        'class' => 'buttonlink icon_sitegroups_delete',
        'route' => 'sitegroup_delete',
        'params' => array(
            'group_id' => $subject->getIdentity(),
        ),
    );
  }

  public function onMenuInitialize_SitegroupGutterPublish() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() !== 'sitegroup_group') {
      return false;
    }

    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($subject, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }

    if (!$viewer->getIdentity() || empty($can_edit) || $subject->draft == 1) {
      return false;
    }

    return array(
        'class' => 'buttonlink smoothbox icon_sitegroup_publish',
        'route' => 'sitegroup_publish',
        'params' => array(
            'group_id' => $subject->getIdentity(),
        ),
    );
  }

  public function onMenuInitialize_SitegroupGutterPrint() {
    $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() !== 'sitegroup_group') {
      return false;
    }

    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($subject, 'print');
    if (empty($isManageAdmin)) {
      $can_print = 0;
    } else {
      $can_print = 1;
    }

    if (empty($can_print)) {
      return false;
    }

    return array(
        'class' => 'buttonlink icon_sitegroups_print',
        'target' => '_blank',
        'route' => 'sitegroup_profilegroup',
        'params' => array(
            'action' => 'print',
            'id' => $subject->getIdentity(),
        ),
    );
  }

  public function onMenuInitialize_SitegroupGutterTfriend() {
    $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() !== 'sitegroup_group') {
      return false;
    }

    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($subject, 'tfriend');
    if (empty($isManageAdmin)) {
      $can_tellfriend = 0;
    } else {
      $can_tellfriend = 1;
    }

    if (empty($can_tellfriend)) {
      return false;
    }

     $class = 'smoothbox buttonlink icon_sitegroups_tellafriend';
    $sitemobile = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemobile');
    if ($sitemobile && Engine_Api::_()->sitemobile()->checkMode('mobile-mode'))
      $class = 'buttonlink icon_sitegroups_tellafriend';
    return array(
        'class' => $class,
        'route' => 'sitegroup_profilegroup',
        'params' => array(
            'action' => 'tell-a-friend',
            'id' => $subject->getIdentity(),
        ),
    );
  }

  public function onMenuInitialize_SitegroupGutterClaim() {
    $viewer = Engine_Api::_()->user()->getViewer();

//     if (!Engine_Api::_()->authorization()->isAllowed('sitegroup_group', $viewer, 'view')) {
//       return false;
//     }
    $viewer_id = $viewer->getIdentity();

    if (!empty($viewer_id)) {
      $level_id = $viewer->level_id;
    } else {
      $authorizationTable = Engine_Api::_()->getItemTable('authorization_level');
      $authorization = $authorizationTable->fetchRow(array('type = ?' => 'public', 'flag = ?' => 'public'));
      if (!empty($authorization))
        $level_id = $authorization->level_id;
    }

    $allow_claim = Engine_Api::_()->authorization()->getPermission($level_id, 'sitegroup_group', 'claim');
    if (empty($allow_claim)) {
      return false;
    }
    $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() !== 'sitegroup_group') {
      return false;
    }

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.claimlink', 1)) {
      return false;
    }

    $listmemberclaimsTable = Engine_Api::_()->getDbtable('listmemberclaims', 'sitegroup');
    $listmemberclaimsTablename = $listmemberclaimsTable->info('name');
    $select = $listmemberclaimsTable->select()->from($listmemberclaimsTablename, array('count(*) as total_count'))
            ->where('user_id = ?', $subject->owner_id);
    $row = $listmemberclaimsTable->fetchAll($select);

    if (!empty($row[0]['total_count'])) {
      $total_count = 1;
    }

    if (empty($total_count) || $subject->owner_id == $viewer_id || empty($subject->userclaim) || empty($allow_claim)) {
      return false;
    }

		if($viewer_id){
    return array(
        'class' => 'smoothbox buttonlink icon_sitegroups_claim',
        'route' => 'sitegroup_claimgroups',
        'params' => array(
            'action' => 'claim-group',
            'group_id' => $subject->getIdentity(),
        ),
    );
		} else{
		return array(
						'class' => 'buttonlink icon_sitegroups_claim',
						'route' => 'user_login',
						'params' => array(
									'return_url' => '64-' . base64_encode($subject->getHref()),
						),
				);
		}

  }

  public function onMenuInitialize_SitegroupGutterMessageowner() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
    
    if(Engine_Api::_()->seaocore()->isSitemobileApp() && !$moduleEnabled){
       return false;
    }
    $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() !== 'sitegroup_group') {
      return false;
    }

    if ($subject->owner_id == $viewer_id || empty($viewer_id)) {
      return false;
    }

    $showMessageOwner = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'auth');
    if ($showMessageOwner == 'none') {
      return false;
    }

    return array(
        'class' => 'buttonlink smoothbox icon_sitegroups_invite',
        'route' => 'sitegroup_profilegroup',
        'params' => array(
            'action' => 'message-owner',
            'group_id' => $subject->getIdentity(),
        ),
    );
  }

  public function onMenuInitialize_SitegroupGutterOpen() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() !== 'sitegroup_group') {
      return false;
    }

    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($subject, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }

    if (!$viewer->getIdentity() || $subject->closed != 1 || empty($can_edit)) {
      return false;
    }

    return array(
        'class' => 'buttonlink icon_sitegroups_open',
        'route' => 'sitegroup_close',
        'params' => array(
            'group_id' => $subject->getIdentity(),
            'closed' => 0,
        ),
    );
  }

  public function onMenuInitialize_SitegroupGutterClose() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() !== 'sitegroup_group') {
      return false;
    }

    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($subject, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }

    if (!$viewer->getIdentity() || $subject->closed != 0 || empty($can_edit)) {
      return false;
    }

    return array(
        'class' => 'buttonlink icon_sitegroups_close',
        'route' => 'sitegroup_close',
        'params' => array(
            'group_id' => $subject->getIdentity(),
            'closed' => 1,
        ),
    );
  }

  public function onMenuInitialize_SitegroupGutterReport() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() !== 'sitegroup_group') {
      return false;
    }

    $report = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.report', 1);

    if (!$viewer->getIdentity() || empty($report)) {
      return false;
    }

    return array(
        'class' => 'smoothbox icon_sitegroups_report buttonlink',
        'route' => 'default',
        'params' => array(
            'module' => 'core',
            'controller' => 'report',
            'action' => 'create',
            'subject' => $subject->getGuid(),
            'format' => 'smoothbox',
        ),
    );
  }

  public function onMenuInitialize_SitegroupGutterEditdetail($row) {
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }

    $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($isManageAdmin)) {
      return false;
    }
    //END MANAGE-ADMIN CHECK
    // Modify params
    $params = $row->params;
    $params['params']['group_id'] = $sitegroup->getIdentity();
    return $params;
  }

  public function onMenuInitialize_SitegroupGutterEditoverview($row) {
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }

    $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($isManageAdmin)) {
      return false;
    }

    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'overview');
    if (empty($isManageAdmin)) {
      return false;
    }
    //END MANAGE-ADMIN CHECK
    // Modify params
    $params = $row->params;
    $params['params']['group_id'] = $sitegroup->getIdentity();
    return $params;
  }

  public function onMenuInitialize_SitegroupGutterEditstyle($row) {

    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }
    $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($isManageAdmin)) {
      return false;
    }
    //END MANAGE-ADMIN CHECK
    // Modify params
    $params = $row->params;
    $params['params']['group_id'] = $sitegroup->getIdentity();
    return $params;
  }

  public function onMenuInitialize_SitegroupGutterEditlayout($row) {
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer || !$viewer->getIdentity()) {
      return false;
    }
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($isManageAdmin)) {
      return false;
    }
    //END MANAGE-ADMIN CHECK

    $check = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0);

    // Modify params
    $params = $row->params;
    $params['params']['group_id'] = $sitegroup->getIdentity();

    if (!empty($check)) {
      return $params;
    }
  }

  public function onMenuInitialize_SitegroupMainClaim($row) {
    // Modify params
    $params = $row->params;
    return $params;
  }

  public function canViewClaims() {
    $viewer = Engine_Api::_()->user()->getViewer();
    //Must be able to view Sitegroups
    if (!Engine_Api::_()->authorization()->isAllowed('sitegroup_group', $viewer, 'view')) {
      return false;
    }


    $viewer_id = $viewer->getIdentity();
    if (!empty($viewer_id)) {
      $level_id = $viewer->level_id;
    } else {
      $authorizationTable = Engine_Api::_()->getItemTable('authorization_level');
      $authorization = $authorizationTable->fetchRow(array('type = ?' => 'public', 'flag = ?' => 'public'));
      if (!empty($authorization))
        $level_id = $authorization->level_id;
    }

    $allow_claim = Engine_Api::_()->authorization()->getPermission($level_id, 'sitegroup_group', 'claim');

    if (!Engine_Api::_()->getApi('settings', 'core')->sitegroup_claimlink || empty($allow_claim)) {
      return false;
    }

    $table = Engine_Api::_()->getDbtable('groups', 'sitegroup');
    $tablename = $table->info('name');
    $select = $table->select()->from($tablename, array('count(*) as count'))->where($tablename . '.closed = ?', '0')
            ->where($tablename . '.approved = ?', '1')
            ->where($tablename . '.declined = ?', '0')
            ->where($tablename . '.draft = ?', '1');
    if (Engine_Api::_()->sitegroup()->hasPackageEnable())
      $select->where($tablename . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    $results = $table->fetchAll($select);
    if (!$results[0]['count']) {
      return false;
    }
    return true;
  }

  // START FOR PROMOTE WITH AN AD LINK
  public function onMenuInitialize_SitegroupGutterPromotead($row) {

    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }

    // check if Communityad Plugin is enabled
    $sitegroupcommunityadEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad');
    if (!$sitegroupcommunityadEnabled) {
      return false;
    }

		// check if it is upgraded version
    $communityadmodulemodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('communityad');
		$adversion = $communityadmodulemodule->version;
    if($adversion < '4.1.5') {
				return;
    }

    $sitegroup = Engine_Api::_()->core()->getSubject();
    $ismoduleads_enabled = Engine_Api::_()->getDbtable('modules', 'communityad')->ismoduleads_enabled('sitegroup');
    if (!$ismoduleads_enabled) {
      return false;
    }

    $useradsTable = Engine_Api::_()->getDbtable('userads', 'communityad');
    $useradsName = $useradsTable->info('name');

    $select = $useradsTable->select();
    $select
            ->from($useradsName, array('userad_id'))
            ->where('resource_type = ?', 'sitegroup')
            ->where('resource_id = ?', $sitegroup->group_id)
            ->limit(1);
    $ad_exist = $useradsTable->fetchRow($select);
    if (!empty($ad_exist)) {
      return false;
    }

    //START OWNER CHECK
    $isOwner = Engine_Api::_()->sitegroup()->isGroupOwner($sitegroup);
    if (!$isOwner) {
      return false;
    }
    //END OWNER CHECK

    //Member Level Check 
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->authorization()->isAllowed('communityad', $viewer, 'create')) {
            return false;
        }
        // Modify params
    $params = $row->params;
    $params['params']['type'] = 'sitegroup';
    $params['params']['type_id'] = $sitegroup->getIdentity();
    return $params;
  }

    // START FOR ADD fAVOURITE
  public function onMenuInitialize_SitegroupGutterFavourite($row) { 

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    if (!$viewer || !$viewer->getIdentity()) {
      return false;
    }

    //FOR SHOW ADD FAVOURITE LINK ON THE GROUP PROFILE GROUP
    $show_link = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.addfavourite.show', 0);
    if (empty($show_link)) {
      return false;
    }

    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }

    $sitegroup = Engine_Api::_()->core()->getSubject();
    $group_id = $sitegroup->group_id;
    $table = Engine_Api::_()->getDbtable('groups', 'sitegroup');
    $tablename = $table->info('name');

    $select = $table->select()->from($tablename, array('count(*) as count'))
            ->where('owner_id = ?', $viewer_id)
            ->where($tablename . '.group_id <> ?', $group_id)
            //->where($tablename . '.owner_id  <> ?', $viewer_id)
            ->where($tablename . '.approved = 1')
            ->where($tablename . '.draft = ?', '1')
            //->group($tablename . '.owner_id')
            ->where($tablename . '.closed = ?', '0');
    $results = $table->fetchRow($select);
    $count = $results->count;

    if ($count < 1) {
      return false;
    }

    $check = Engine_Api::_()->getDbtable('favourites', 'sitegroup')->isShow($sitegroup->getIdentity());
    $table_favourites = Engine_Api::_()->getDbtable('favourites', 'sitegroup');
    $tablename = $table->info('name');

    $select_content = $table_favourites->select()->where('owner_id = ?', $viewer_id);
    $content = $select_content->query()->fetchAll();

    if (!empty($content)) {
      //Started the select query
      $select = $table->select()
              ->from($tablename, 'group_id')
              ->where($tablename . '.group_id <> ?', $group_id)
              ->where($tablename . '.owner_id  =?', $viewer_id)
              ->where($tablename . '.approved = 1')
              ->where($tablename . '.draft = ?', '1')
              ->where($tablename . '.closed = ?', '0')
              ->where('NOT EXISTS (SELECT `group_id` FROM `engine4_sitegroup_favourites` WHERE `group_id_for`=' . $group_id . ' AND `group_id` = ' . $tablename . '.`group_id`) ');
      $content_result = $select->query()->fetchAll();
      $count_result1 = count($content_result);

      if (($count_result1 == 0)) {
        return false;
      }
    }

    // Modify params
    $params = $row->params;
    $params['params']['group_id'] = $sitegroup->getIdentity();

    if (!empty($check)) {
      return $params;
    } else {
      return $params;
    }
  }

  // END FOR ADD fAVOURITE
  // START FOR DELETE fAVOURITE
  public function onMenuInitialize_SitegroupGutterFavouritedelete($row) {

    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer || !$viewer->getIdentity()) {
      return false;
    }

    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }
    $viewer_id = $viewer->getIdentity();


    $viewer = Engine_Api::_()->user()->getViewer();
    $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');

    $check = Engine_Api::_()->getDbtable('favourites', 'sitegroup')->isnotShow($sitegroup->getIdentity());

    // Modify params
    $params = $row->params;
    $params['params']['group_id'] = $sitegroup->getIdentity();

    if (!empty($check)) {
      return $params;
    }
  }
  // END FOR DELETE fAVOURITE

	//ADD TO WISHLIST LINK
  public function onMenuInitialize_SitegroupGutterWishlist($row) {
		
		//GET VIEWER DETAIL
    $viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

		if(empty($viewer_id)) {
			return false;
		}

    $canView = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitegroupwishlist_wishlist', 'view');
		if(empty($canView)) {
			return false;
		}

		//RETURN FALSE IF SUBJECT IS NOT SET
    $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() !== 'sitegroup_group') {
      return false;
    }

		//SHOW ADD TO WISHLIST LINK IF SITEPAGWISHLIST MODULES IS ENABLED
		$sitegroupWishlistEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupwishlist');
    if (!$viewer->getIdentity() || empty($sitegroupWishlistEnabled)) {
      return false;
    }

    return array(
        'class' => 'icon_sitegroupwishlist_add buttonlink',
        'route' => 'default',
        'params' => array(
            'module' => 'sitegroupwishlist',
            'controller' => 'index',
            'action' => 'add',
            'group_id' => $subject->getIdentity(),
        ),
    );
  }
  
  public function onMenuInitialize_SitegroupSitegroupGutterCreate($row) {
  
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }

    $moduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitegroupintegration' ) ;
    $sitegroupmoduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitegroup' ) ;
    if (empty($moduleEnabled) || empty($sitegroupmoduleEnabled)) {
			return false;
    }
        
    $viewer = Engine_Api::_()->user()->getViewer();
    $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
    
    $item_enabled = Engine_Api::_()->getDbtable('mixsettings', 'sitegroupintegration')->getItemsEnabled('sitegroup_group', 0);
    if (empty($item_enabled)) {
			return false;
    }
    
    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroup_group_0")) {
        return false;
      }
    } else {
      $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'sitegroup_group_0');
      if (empty($isGroupOwnerAllow)) {
        return false;
      }
    }
    
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($isManageAdmin)) {
      return false;
    }

    if (!Engine_Api::_()->authorization()->isAllowed('sitegroup_group', $viewer, 'create')) {
      return false;
    }

		if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
			return array(
        'label' => $row->label,
        'route' => 'sitegroup_packages',
        'action' => 'index',
        'class' => 'buttonlink item_icon_sitegroup',
        'params' => array(
          'group_id' =>  $sitegroup->group_id
        ),
      );
		} else {
			return array(
        'label' => $row->label,
        'route' => 'sitegroup_general',
        'action' => 'create',
        'class' => 'buttonlink item_icon_sitegroup',
        'params' => array(
          'group_id' =>  $sitegroup->group_id
        ),
      );
		}
  }
     
  public function onMenuInitialize_SitegroupDocumentGutterCreate($row) {
  
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
    $moduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitegroupintegration' ) ;
    $listmoduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'document' ) ;
    if (empty($moduleEnabled) || empty($listmoduleEnabled)) {
			return false;
    }
    
    $item_enabled = Engine_Api::_()->getDbtable('mixsettings', 'sitegroupintegration')->getItemsEnabled('document', 0);
    if (empty($item_enabled)) {
			return false;
    }
    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "document_0")) {
        return false;
      }
    } else {
      $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'document_0');
      if (empty($isGroupOwnerAllow)) {
        return false;
      }
    }
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($isManageAdmin)) {
      return false;
    }

    if (!Engine_Api::_()->authorization()->isAllowed('document', $viewer, 'create')) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['group_id'] = $sitegroup->getIdentity();
    return $params;
  }
  
  public function onMenuInitialize_SitegroupSitepageGutterCreate($row) {
  
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }

    $moduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitegroupintegration' ) ;
    $sitepagemoduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitepage' ) ;
    if (empty($moduleEnabled) || empty($sitepagemoduleEnabled)) {
			return false;
    }
        
    $viewer = Engine_Api::_()->user()->getViewer();
    $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
    
    $item_enabled = Engine_Api::_()->getDbtable('mixsettings', 'sitegroupintegration')->getItemsEnabled('sitepage_page', 0);
    if (empty($item_enabled)) {
			return false;
    }
    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitepage_page_0")) {
        return false;
      }
    } else {
      $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'sitepage_page_0');
      if (empty($isGroupOwnerAllow)) {
        return false;
      }
    }
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($isManageAdmin)) {
      return false;
    }

    if (!Engine_Api::_()->authorization()->isAllowed('sitepage_page', $viewer, 'create')) {
      return false;
    }

		if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
			return array(
        'label' => $row->label,
        'route' => 'sitepage_packages',
        'action' => 'index',
        'class' => 'buttonlink item_icon_sitepage',
        'params' => array(
          'group_id' =>  $sitegroup->group_id
        ),
      );
		} else {
			return array(
        'label' => $row->label,
        'route' => 'sitepage_general',
        'action' => 'create',
        'class' => 'buttonlink item_icon_sitepage',
        'params' => array(
          'group_id' =>  $sitegroup->group_id
        ),
      );
		}
  }
    
  public function onMenuInitialize_SitegroupFolderGutterCreate($row) {
  
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
    $moduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitegroupintegration' ) ;
    $listmoduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'folder' ) ;
    if (empty($moduleEnabled) || empty($listmoduleEnabled)) {
			return false;
    }
    
    $item_enabled = Engine_Api::_()->getDbtable('mixsettings', 'sitegroupintegration')->getItemsEnabled('folder', 0);
    if (empty($item_enabled)) {
			return false;
    }
    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "folder_0")) {
        return false;
      }
    } else {
      $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'folder_0');
      if (empty($isGroupOwnerAllow)) {
        return false;
      }
    }
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($isManageAdmin)) {
      return false;
    }

    if (!Engine_Api::_()->authorization()->isAllowed('folder', $viewer, 'create')) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['group_id'] = $sitegroup->getIdentity();
    return $params;
  }
  
  public function onMenuInitialize_SitegroupQuizGutterCreate($row) {
  
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
    $moduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitegroupintegration' ) ;
    $listmoduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'quiz' ) ;
    if (empty($moduleEnabled) || empty($listmoduleEnabled)) {
			return false;
    }
    
    $item_enabled = Engine_Api::_()->getDbtable('mixsettings', 'sitegroupintegration')->getItemsEnabled('quiz', 0);
    if (empty($item_enabled)) {
			return false;
    }
    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "quiz_0")) {
        return false;
      }
    } else {
      $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'quiz_0');
      if (empty($isGroupOwnerAllow)) {
        return false;
      }
    }
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($isManageAdmin)) {
      return false;
    }

    if (!Engine_Api::_()->authorization()->isAllowed('quiz', $viewer, 'create')) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['group_id'] = $sitegroup->getIdentity();
    return $params;
  }
  
  public function onMenuInitialize_SitegroupListGutterCreate($row) {
  
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
    $moduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitegroupintegration' ) ;
    $listmoduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'list' ) ;
    if (empty($moduleEnabled) || empty($listmoduleEnabled)) {
			return false;
    }

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($isManageAdmin)) {
      return false;
    }
    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "list_listing_0")) {
        return false;
      }
    } else {
      $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'list_listing_0');
      if (empty($isGroupOwnerAllow)) {
        return false;
      }
    }
    if (!Engine_Api::_()->authorization()->isAllowed('list_listing', $viewer, 'create')) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['group_id'] = $sitegroup->getIdentity();
    return $params;
  }
  
  public function sitegroupsitereviewGutterCreate($row) {

    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
    $moduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitegroupintegration' ) ;
    $sitereviewmoduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitereview' ) ;
    if (empty($moduleEnabled) || empty($sitereviewmoduleEnabled)) {
			return false;
    }

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($isManageAdmin)) {
      return false;
    }

    //GET LISTING TYPE ID
    $listingtype_id = $row->params['listing_id'];
    $listingType = Engine_Api::_()->getItem('sitereview_listingtype',$listingtype_id);
    $titleSinUc = ucfirst($listingType->title_singular);
    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitereview_listing_$listingtype_id")) {
        return false;
      }
    } else {
      $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, "sitereview_listing_$listingtype_id");
      if (empty($isGroupOwnerAllow)) {
        return false;
      }
    }

    $item_enabled = Engine_Api::_()->getDbtable('mixsettings', 'sitegroupintegration')->getItemsEnabled('sitereview_listing', $listingtype_id);
    if (empty($item_enabled)) {
			return false;
    }
    
    //MUST BE ABLE TO VIEW LISTINGS
    if (!Engine_Api::_()->authorization()->isAllowed('sitereview_listing', $viewer, "view_listtype_$listingtype_id")) {
      return false;
    }

    //MUST BE ABLE TO CRETE LISTINGS
    if (!Engine_Api::_()->authorization()->isAllowed('sitereview_listing', $viewer, "create_listtype_$listingtype_id")) {
      return false;
    }

   $sitereviewpaidlistingEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereviewpaidlisting');
		if($sitereviewpaidlistingEnabled) {
			$total_packages = Engine_Api::_()->getDbtable('packages', 'sitereviewpaidlisting')->getTotalPackage($listingtype_id, 1);
		}
		
		if($sitereviewpaidlistingEnabled && $listingType->package && $total_packages == 1) {
			$package = Engine_Api::_()->getDbTable('packages', 'sitereviewpaidlisting')->getEnabledPackage($listingtype_id);			
			return array(
        'label' => "Post New $titleSinUc Listings",
        'route' => "sitereview_general_listtype_$listingtype_id",
        'action' => 'create',
        'class' => $row->params['class'],
        'params' => array(
          'id' => $package->package_id,
          'group_id' =>  $sitegroup->group_id,
        ),
      );
		} elseif($sitereviewpaidlistingEnabled && $listingType->package && $total_packages > 1) {
			return array(
        'label' => "Post New $titleSinUc Listings",
        'route' => "sitereview_all_package_listtype_$listingtype_id",
        'action' => 'index',
        'class' => $row->params['class'],
        'params' => array(
          'group_id' =>  $sitegroup->group_id,
        ),
      );
			
		} else {
			return array(
        'label' => "Post New $titleSinUc Listings",
        'route' => "sitereview_general_listtype_$listingtype_id",
        'action' => 'create',
        'class' => $row->params['class'],
        'params' => array(
          'group_id' =>  $sitegroup->group_id,
        ),
      );
		}
  }
  
  public function onMenuInitialize_SitegroupTutorialGutterCreate($row) {
  
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
    $moduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitegroupintegration' ) ;
    $sitetutorialmoduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitetutorial' ) ;
    if (empty($moduleEnabled) || empty($sitetutorialmoduleEnabled)) {
			return false;
    }
    
    $item_enabled = Engine_Api::_()->getDbtable('mixsettings', 'sitegroupintegration')->getItemsEnabled('sitetutorial_tutorial', 0);
    if (empty($item_enabled)) {
			return false;
    }
    
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($isManageAdmin)) {
      return false;
    }
    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitetutorial_tutorial_0")) {
        return false;
      }
    } else {
      $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'sitetutorial_tutorial_0');
      if (empty($isGroupOwnerAllow)) {
        return false;
      }
    }

    if (!Engine_Api::_()->authorization()->isAllowed('sitetutorial_tutorial', $viewer, 'create')) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['group_id'] = $sitegroup->getIdentity();
    return $params;
  }
  
  public function onMenuInitialize_SitegroupFaqGutterCreate($row) {
  
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
    $moduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitegroupintegration' ) ;
    $sitefaqmoduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitefaq' ) ;
    if (empty($moduleEnabled) || empty($sitefaqmoduleEnabled)) {
			return false;
    }
    
    $item_enabled = Engine_Api::_()->getDbtable('mixsettings', 'sitegroupintegration')->getItemsEnabled('sitefaq_faq', 0);
    if (empty($item_enabled)) {
			return false;
    }
    
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($isManageAdmin)) {
      return false;
    }
    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitefaq_faq_0")) {
        return false;
      }
    } else {
      $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'sitefaq_faq_0');
      if (empty($isGroupOwnerAllow)) {
        return false;
      }
    }

    if (!Engine_Api::_()->authorization()->isAllowed('sitefaq_faq', $viewer, 'create')) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['group_id'] = $sitegroup->getIdentity();
    return $params;
  }
    
  public function onMenuInitialize_SitegroupSitestoreproductGutterCreate($row) {
  
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }

    $moduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitegroupintegration' ) ;
    $sitestoreproductmoduleEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitestoreproduct' ) ;
    if (empty($moduleEnabled) || empty($sitestoreproductmoduleEnabled)) {
			return false;
    }
        
    $viewer = Engine_Api::_()->user()->getViewer();
    $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
    
    $item_enabled = Engine_Api::_()->getDbtable('mixsettings', 'sitegroupintegration')->getItemsEnabled('sitestoreproduct_product', 0);
    if (empty($item_enabled)) {
			return false;
    }
    
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($isManageAdmin)) {
      return false;
    }
    
    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitestoreproduct_product_0")) {
        return false;
      }
    } else {
      $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'sitestoreproduct_product_0');
      if (empty($isGroupOwnerAllow)) {
        return false;
      }
    }
    
    if (!Engine_Api::_()->authorization()->isAllowed('sitestoreproduct_product', $viewer, 'create')) {
      return false;
    }
    
    if(!empty($sitegroup->owner_id)) {
			$store = Engine_Api::_()->getDbtable('stores', 'sitestore')->getStoreId($sitegroup->owner_id);
		}
		
	  if(count($store) == 0) {
			return false;
		}

		if($sitestoreproductmoduleEnabled) {
			$authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store[0]['store_id']);
			if(empty($authValue)) {
				return false;
			}
		}
		
		if(count($store) == 1) {
			$store_id = $store[0]['store_id'];
			return array(
        'label' => $row->label,
        'route' => 'sitestoreproduct_general',
        'action' => 'create',
        'class' => 'buttonlink seaocore_icon_add',
        'params' => array(
          'group_id' =>  $sitegroup->group_id,
          'store_id' => $store_id
        ),
      );
		} else {
			return array(
        'label' => $row->label,
        'route' => 'default',
        'module' => 'sitegroupintegration',
        'controller' => 'index',
        'action' => 'storeintegration',
        'class' => 'buttonlink seaocore_icon_add smoothbox',
        'params' => array(
          'resource_id' =>  $sitegroup->group_id
        ),
      );
		}
  }
  
}