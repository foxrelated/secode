<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Menus.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Plugin_Menus {

  public function canCreateLists() {

    //MUST BE LOGGED IN USER
		$viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer || !$viewer->getIdentity()) {
      return false;
    }

    //MUST BE ABLE TO VIEW LISTINGS
    if (!Engine_Api::_()->authorization()->isAllowed('list_listing', $viewer, 'view')) {
      return false;
    }

    //MUST BE ABLE TO CRETE LISTINGS
    if (!Engine_Api::_()->authorization()->isAllowed('list_listing', $viewer, 'create')) {
      return false;
    }

    return true;
  }

  public function canViewLists() {

		//GET VIEWER
		$viewer = Engine_Api::_()->user()->getViewer();

    //MUST BE ABLE TO VIEW LISTINGS
    if (!Engine_Api::_()->authorization()->isAllowed('list_listing', $viewer, 'view')) {
      return false;
    }

		$settings = Engine_Api::_()->getApi('settings', 'core');
    $check_result_show = $settings->getSetting('listing.check.var');
    $base_result_time = $settings->getSetting('listing.base.time');
    $get_result_show = $settings->getSetting('listing.get.path');
    $listing_time_var = $settings->getSetting('listing.time.var');
    $controllersettings_result_show = $settings->getSetting('list.lsettings');
    $currentbase_time = time();
    $controller_result_lenght = strlen($controllersettings_result_show);

    if (($currentbase_time - $base_result_time > $listing_time_var) && empty($check_result_show)) {
      if ($controller_result_lenght != 20) {
        $settings->setSetting('list.view.attempt', 1);
        $settings->setSetting('list.flag.info', 1);
        return false;
      } else {
        $settings->setSetting('listing.check.var', 1);
      }
    }

    return true;
  }

  public function onMenuInitialize_ListGutterEdit($row) {

		//RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('list_listing')) {
      return false;
    }

		//GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

		//GET LISTING SUBJECT
    $list = Engine_Api::_()->core()->getSubject('list_listing');

		//AUTHORIZATION CHECK
    if (!$list->authorization()->isAllowed($viewer, 'edit')) {
      return false;
    }

    return array(
        'class' => 'buttonlink icon_lists_edit',
        'route' => 'list_specific',
				'action' => 'edit',
        'params' => array(
            'listing_id' => $list->getIdentity(),
        ),
    );
  }

  public function onMenuInitialize_ListGutterEditoverview($row) {

		//RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('list_listing')) {
      return false;
    }

		//GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

		//GET LISTING SUBJECT
    $list = Engine_Api::_()->core()->getSubject('list_listing');

		//AUTHORIZATION CHECK
    if (!$list->authorization()->isAllowed($viewer, 'edit')) {
      return false;
    }
    
		//OVERVIEW PRIVACY
		$allowOverview = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'list_listing', 'overview');
    if (!$allowOverview) {
      return false;
    }
    
    return array(
        'class' => 'buttonlink',
        'route' => 'list_specific',
				'action' => 'overview',
        'params' => array(
            'listing_id' => $list->getIdentity(),
        ),
    );
  }

  public function onMenuInitialize_ListGutterEditstyle($row) {

		//RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('list_listing')) {
      return false;
    }

		//GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

		//GET LISTING SUBJECT
    $list = Engine_Api::_()->core()->getSubject('list_listing');

		//AUTHORIZATION CHECK
    if (!$list->authorization()->isAllowed($viewer, 'edit')) {
      return false;
    }

		//STYLE PRIVACY
		$style_allow = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('list_listing', $viewer->level_id, 'style');
    if (empty($style_allow)) {
      return false;
    }

    return array(
        'class' => 'buttonlink',
        'route' => 'list_specific',
				'action' => 'editstyle',
        'params' => array(
            'listing_id' => $list->getIdentity(),
        ),
    );
  }

	public function onMenuInitialize_ListGutterShare() {

		//RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('list_listing')) {
      return false;
    }

		//GET SUBJECT
		$list = Engine_Api::_()->core()->getSubject('list_listing');

		//GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

	  //SHARE IS ENABLE OR DISABLE BY ADMIN
    $canShare = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('list.share', 1);

		//RETURN IF VIEWER IS EMPTY
    if (empty($viewer_id) || empty($canShare)) {
      return false;
    }

    return array(
        'class' => 'smoothbox icon_lists_share buttonlink',
        'route' => 'default',
        'params' => array(
            'module' => 'activity',
            'controller' => 'index',
            'action' => 'share',
            'type' => $list->getType(),
            'id' => $list->getIdentity(),
            'format' => 'smoothbox',
        ),
    );
  }

  public function onMenuInitialize_ListGutterMessageowner() {

		//RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('list_listing')) {
      return false;
    }

		//GET VIEWER INFO
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

		//GET SUBJECT
		$list = Engine_Api::_()->core()->getSubject('list_listing');

		//SHOW MESSAGE OWNER LINK TO USER IF MESSAGING IS ENABLED FOR THIS LEVEL
		$showMessageOwner = 0;
		$showMessageOwner = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'auth');
		if($showMessageOwner != 'none') {
			$showMessageOwner = 1;
		}

		//RETURN IF NOT AUTHORIZED
    if ($list->owner_id == $viewer_id || empty($viewer_id) || empty($showMessageOwner)) {
      return false;
    }

    return array(
        'class' => 'smoothbox icon_lists_messageowner buttonlink',
        'route' => 'list_specific',
				'action' => 'messageowner',
        'params' => array(
            'listing_id' => $list->getIdentity(),
        ),
    );
  }

  public function onMenuInitialize_ListGutterTfriend() {

		//RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('list_listing')) {
      return false;
    }

		//CHECK TELL-A-FRIEND IS ENABLE/DISABLE
    $tell_a_friend = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('list.tellafriend', 1);
		if (empty($tell_a_friend)) {
      return false;
    }

		//GET SUBJECT
		$list = Engine_Api::_()->core()->getSubject('list_listing');

    return array(
        'class' => 'smoothbox buttonlink icon_lists_tellafriend',
				'route' => 'list_specific',
				'action' => 'tellafriend',
        'params' => array(
            'listing_id' => $list->getIdentity(),
        ),
    );
  }

  public function onMenuInitialize_ListGutterPrint() {

		//RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('list_listing')) {
      return false;
    }

		//CHECK PRINT IS ENABLE/DISABLE
    $canPrint = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('list.printer', 1);
    if (empty($canPrint)) {
      return false;
    }

		//GET SUBJECT
		$list = Engine_Api::_()->core()->getSubject('list_listing');

    return array(
				'class' => 'buttonlink icon_lists_printer',
        'route' => 'list_specific',
				'action' => 'print',
        'target' => '_blank',
        'params' => array(
            'listing_id' => $list->getIdentity(),
        ),
    );
  }

  public function onMenuInitialize_ListGutterPublish() {

		//RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('list_listing')) {
      return false;
    }

		//GET SUBJECT
		$list = Engine_Api::_()->core()->getSubject('list_listing');

		//GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

		//RETURN IF NOT AUTHORIZED
    if ($list->draft != 0 || ($viewer_id != $list->owner_id)) {
      return false;
    }

    return array(
        'class' => 'buttonlink smoothbox icon_list_publish',
        'route' => 'list_specific',
				'action' => 'publish',
        'params' => array(
						'listing_id' => $list->getIdentity()
        ),
    );
  }

 public function onMenuInitialize_ListGutterOpen() {

		//RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('list_listing')) {
      return false;
    }

		//GET SUBJECT
		$list = Engine_Api::_()->core()->getSubject('list_listing');

		//GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

		//RETURN IF NOT AUTHORIZED
    if ($list->closed != 1 || ($viewer_id != $list->owner_id)) {
      return false;
    }

    return array(
        'class' => 'buttonlink icon_lists_close',
        'route' => 'default',
        'params' => array(
            'module' => 'list',
						'action' => 'close', 
						'listing_id' => $list->getIdentity(),
						'closed' => 0
        ),
    );
  }

  public function onMenuInitialize_ListGutterClose() {

		//RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('list_listing')) {
      return false;
    }

		//GET SUBJECT
		$list = Engine_Api::_()->core()->getSubject('list_listing');

		//GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

		//RETURN IF NOT AUTHORIZED
    if ($list->closed != 0 || ($viewer_id != $list->owner_id)) {
      return false;
    }

    return array(
        'class' => 'buttonlink icon_lists_open',
        'route' => 'default',
        'params' => array(
            'module' => 'list',
						'action' => 'close',
						'listing_id' => $list->getIdentity(),
						'closed' => 1
        ),
    );
  }

  public function onMenuInitialize_ListGutterDelete() {

		//RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('list_listing')) {
      return false;
    }

		//GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

		//GET SUBJECT
		$list = Engine_Api::_()->core()->getSubject('list_listing');

		//LISTING DELETE PRIVACY
    $can_delete = $list->authorization()->isAllowed(null, 'delete');

		//AUTHORIZATION CHECK
		if(empty($can_delete) || empty($viewer_id)) {
			return false;
		}

    return array(
        'class' => 'buttonlink icon_lists_delete',
				'route' => 'default',
        'params' => array(
            'module' => 'list',
						'controller' => 'index',
						'action' => 'delete',
						'listing_id' => $list->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_ListGutterReport() {

		//RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('list_listing')) {
      return false;
    }

		//GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

		//CHECK REPORT IS ENABLE/DISABLE
    $canReport = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('list.report', 1);

    if (empty($canReport) || empty($viewer_id)) {
      return false;
    }

		//GET SUBJECT
		$list = Engine_Api::_()->core()->getSubject('list_listing');

    return array(
        'class' => 'smoothbox buttonlink icon_lists_report',
        'route' => 'default',
        'params' => array(
						'module' => 'core',
						'controller' => 'report',
						'action' => 'create',
						'route' => 'default',
						'subject' => $list->getGuid()
        ),
    );
  }
  
  public function onMenuInitialize_ListGutterChangephoto($row) {

		//RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('list_listing')) {
      return false;
    }

		//GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

		//GET LISTING SUBJECT
    $list = Engine_Api::_()->core()->getSubject('list_listing');

		//AUTHORIZATION CHECK
    if (!$list->authorization()->isAllowed($viewer, 'edit')) {
      return false;
    }

    return array(
        'class' => 'buttonlink icon_list_edit',
        'route' => 'list_specific',
				'action' => 'change-photo',
        'params' => array(
            'listing_id' => $list->getIdentity(),
        ),
    );
  }
}