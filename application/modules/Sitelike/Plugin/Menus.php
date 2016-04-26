<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelike_Plugin_Menus {

  public function onMenuInitialize_CoreMainSitelike( $row ) {

    $viewer = Engine_Api::_()->user()->getViewer()->getIdentity() ;
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    if ( !empty( $viewer ) ) {
      return array (
        'label' => $row->label ,
        'icon' => $view->layout()->staticBaseUrl . 'application/modules/Sitelike/externals/images/thumb.png' ,
        'route' => 'like_general' ,
      ) ;
    }
    return false ;
  }

// 	public function canBrowseLikes() {
// 
// 		return true;
//   }

	public function canMyfriendsLike() {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer || !$viewer->getIdentity()) {
      return false;
    }
		return true;
  }

	public function canMycontentLikes() {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer || !$viewer->getIdentity()) {
      return false;
    }
		return true;
  }

	public function canMemberLikes() {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer || !$viewer->getIdentity()) {
      return false;
    }
		$like_profile_show = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'like.profile.show' ) ;
		if ( empty( $like_profile_show ) ) {
			return false;
		}
		return true;
  }

	public function canMyLikes() {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer || !$viewer->getIdentity()) {
      return false;
    }
		return true;
  }

	public function canLikesettings() {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer || !$viewer->getIdentity()) {
      return false;
    }
		$like_profile_show = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'like.profile.show' ) ;
    $like_setting_show = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'like.setting.show' ) ;
		if ( empty( $like_profile_show ) || empty( $like_setting_show ) ) {
				return false;
		}
		return true;
  }

	public function canSendMessages() {
 
    $viewer = Engine_Api::_()->user()->getViewer();
		if (!$viewer || !$viewer->getIdentity()) {
      return false;
    }

    $level_id = Engine_Api::_()->user()->getViewer()->level_id ;
    $can_view = Engine_Api::_()->authorization()->getPermission( $level_id , 'messages' , 'auth' ) ;

		if (!$can_view) {
      return false;
    }

    $like_count = Engine_Api::_()->getApi('like', 'seaocore')->likeCount($viewer->getType() , $viewer->getIdentity());

    if($like_count <= 1) 
      return false;

    return array(
			'label' => 'Message All',
			'route' => 'default',
			'params' => array(
					'module' => 'sitelike',
					'controller' => 'index',
					'action' => 'compose',
					'resource_type' => $viewer->getType(),
					'resource_id' => $viewer->getIdentity()
			)
		);
  }
}