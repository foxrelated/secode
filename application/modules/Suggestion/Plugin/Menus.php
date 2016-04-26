<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Suggestion_Plugin_Menus {
  
  //Conditions for showing suggestion link in Sitemobile.
  public function  canViewSuggestions(){
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    if(!empty($viewer_id)){
       return true;
    }  
  }

  public function showSitereview($row) {
    $params = $row->params;
    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $subject = Engine_Api::_()->core()->getSubject();
    $getModId = Engine_Api::_()->suggestion()->getReviewModInfo($params['listing_id']);
    $isEnabled = Engine_Api::_()->suggestion()->getModSettings("sitereview_" . $getModId, "link");
 
    if( !empty($user_id) && !empty($isEnabled) ) {
      $modContentObj = Engine_Api::_()->suggestion()->getSuggestedFriend("sitereview_" . $getModId, $subject->getIdentity(), 1);
      if (!empty($modContentObj)) {
        $contentCreatePopup = @COUNT($modContentObj);
      }
      
      if( !empty($contentCreatePopup) ) {
        $params['params']['modName'] = 'sitereview';
        $params['params']['modContentId'] = $subject->getIdentity();
        $params['params']['listingId'] = $params['listing_id'];
        $params['params']['modError'] = 1;
        unset($params['listing_id']);
        unset($params['type']);
        return $params;
      }
    }
  }
  
  // SHOWING LINK ON "MEMBER HOME PAGE".
  public function onMenuInitialize_SuggestionFindFriend($row) { 
  
  $show_link = Engine_Api::_()->getApi('Invite', 'Seaocore')->canInvite();
  if (empty ($show_link)) return;
	$route = 'friends_suggestions_viewall';
	$viewer = Engine_Api::_()->user()->getViewer();
	$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
	if ($viewer->getIdentity()) {
	  return array(
		  'label' => $row->label,
		  'icon' => $row->params['icon'],
		  'route' => 'friends_suggestions_viewall'
	  );
	}
	return false;
  }

  // SHOWING LINK ON "MEMBER HOME PAGE".
  public function onMenuInitialize_SuggestionExploreSuggestion($row) {
	$route = 'sugg_explore_friend';
	$viewer = Engine_Api::_()->user()->getViewer();
	$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
	if ($viewer->getIdentity()) {
	  return array(
		  'label' => $row->label,
		  'icon' => $row->params['icon'],
		  'route' => 'sugg_explore_friend'
	  );
	}
	return false;
  }

  // SHOWING LINK ON "MEMBER PROFILE PAGE".
  public function onMenuInitialize_SuggestionFriendProfile($row) { 
  $show_link = Engine_Api::_()->getApi('Invite', 'Seaocore')->canInvite();
  if (empty ($show_link)) return;  
	$route = 'friends_suggestions_viewall';
	$viewer = Engine_Api::_()->user()->getViewer();
	$subject = Engine_Api::_()->core()->getSubject();
	if ($subject->authorization()->isAllowed($viewer, 'edit')) {
	  return array(
		  'label' => $row->label,
		  'icon' => $row->params['icon'],
		  'route' => 'friends_suggestions_viewall'
	  );
	}
  }

  // SHOWING LINK ON "GROUP PROFILE PAGE".
  public function onMenuInitialize_GroupSuggestFriend($row) {
	$sugg_check = Engine_Api::_()->suggestion()->getModSettings('group', 'link');
	$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
	if (!empty($sugg_check) && !empty($user_id)) {
	  $viewer = Engine_Api::_()->user()->getViewer();
	  $subject = Engine_Api::_()->core()->getSubject();

	  $modContentObj = Engine_Api::_()->suggestion()->getSuggestedFriend('group', $subject->getIdentity(), 1);
	  if (!empty($modContentObj)) {
		$contentCreatePopup = @COUNT($modContentObj);
	  }

	  if (!empty($contentCreatePopup) && !empty($subject->invite) && !empty($subject->search)) {
		return array(
			'label' => $row->label,
			'icon' => $row->params['icon'],
			'class' => 'smoothbox',
			'route' => 'default',
			'params' => array(
				'module' => 'suggestion',
				'controller' => 'index',
				'action' => 'switch-popup',
				'modName' => 'group',
				'modContentId' => $subject->getIdentity(),
				'modError' => 1,
				'format' => 'smoothbox',
			),
		);
	  }
	}
  }

  // SHOWING LINK ON "EVENT PROFILE PAGE".
  public function onMenuInitialize_EventSuggestFriend($row) {
	$sugg_check = Engine_Api::_()->suggestion()->getModSettings('event', 'link');
	$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
	if (!empty($sugg_check) && !empty($user_id)) {
	  $viewer = Engine_Api::_()->user()->getViewer();
	  $subject = Engine_Api::_()->core()->getSubject();

	  $modContentObj = Engine_Api::_()->suggestion()->getSuggestedFriend('event', $subject->getIdentity(), 1);
	  if (!empty($modContentObj)) {
		$contentCreatePopup = @COUNT($modContentObj);
	  }

	  if (!empty($contentCreatePopup) && !empty($subject->search)) {
		return array(
			'label' => $row->label,
			'class' => 'smoothbox',
			'icon' => $row->params['icon'],
			'route' => 'default',
			'params' => array(
				'module' => 'suggestion',
				'controller' => 'index',
				'action' => 'switch-popup',
				'modName' => 'event',
				'modContentId' => $subject->getIdentity(),
				'modError' => 1,
				'format' => 'smoothbox',
			),
		);
	  }
	}
  }

  // SHOWING LINK ON "MEMBER PROFILE PAGE".
  public function onMenuInitialize_MemberSuggestFriend($row) {
	$sugg_check = Engine_Api::_()->suggestion()->getModSettings('user', 'link');
	if (!empty($sugg_check)) {
	  $viewer = Engine_Api::_()->user()->getViewer();
	  $subject = Engine_Api::_()->core()->getSubject();
	  $isMember = Engine_Api::_()->getApi('coreFun', 'suggestion')->isMember($subject->getIdentity());
	  $user = Engine_Api::_()->getItem('user', $viewer->getIdentity());

	  if (!empty($isMember) && $subject->membership()->isMember($user)) {
		// If "Loggden user" have only one friend and loggden user viewing his friend profile the "Suggest to Friend" link should not be show.
		if (($viewer->member_count == 1) && (!empty($isMember))) {
		  return;
		}
		return array(
			'label' => $row->label,
			'class' => 'smoothbox',
			'icon' => $row->params['icon'],
			'route' => 'default',
			'params' => array(
				'module' => 'suggestion',
				'controller' => 'index',
				'action' => 'switch-popup',
				'modName' => 'friend',
				'modContentId' => $subject->getIdentity(),
				'modError' => 1,
				'format' => 'smoothbox',
			),
		);
	  }
	}
  }

  // SHOWING ON "BLOG VIEW PAFE".
  public function onMenuInitialize_BlogSuggestFriend($row) {
	$sugg_check = Engine_Api::_()->suggestion()->getModSettings('blog', 'link');
	$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
	if (!empty($sugg_check) && !empty($user_id)) {
	  $subject = Engine_Api::_()->core()->getSubject();

	  $modContentObj = Engine_Api::_()->suggestion()->getSuggestedFriend('blog', $subject->getIdentity(), 1);
	  if (!empty($modContentObj)) {
		$contentCreatePopup = @COUNT($modContentObj);
	  }

	  if (!empty($contentCreatePopup) && !empty($subject->search)) {
		$params = $row->params;
		$params['params']['modName'] = 'blog';
		$params['params']['modContentId'] = $subject->getIdentity();
		$params['params']['modError'] = 1;
		return $params;
	  }
	}
  }

  // SHOWING ON "CLASSIFIED VIEW PAFE".
  public function onMenuInitialize_ClassifiedSuggestFriend($row) {
	$sugg_check = Engine_Api::_()->suggestion()->getModSettings('classified', 'link');
	$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
	if (!empty($sugg_check) && !empty($user_id)) {
	  $subject = Engine_Api::_()->core()->getSubject();
	  if (!empty($subject->search) && empty($subject->closed)) {
		$params = $row->params;
		$params['params']['modName'] = 'classified';
		$params['params']['modContentId'] = $subject->getIdentity();
		$params['params']['modError'] = 1;
		return $params;
	  }
	}
  }

  // SHOWING ON "List VIEW PAFE".
  public function onMenuInitialize_ListSuggestFriend($row) {
	$sugg_check = Engine_Api::_()->suggestion()->getModSettings('list', 'link');
	$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
	$subject = Engine_Api::_()->core()->getSubject();

	$modContentObj = Engine_Api::_()->suggestion()->getSuggestedFriend('list', $subject->getIdentity(), 1);
	if (!empty($modContentObj)) {
	  $contentCreatePopup = @COUNT($modContentObj);
	}

	if (!empty($contentCreatePopup) && !empty($sugg_check) && !empty($user_id) && !empty($subject->approved) && empty($subject->closed) && !empty($subject->draft)) {
	  $subject = Engine_Api::_()->core()->getSubject();
	  if (!empty($subject->search)) {
		$params = $row->params;
		$params['params']['modName'] = 'list';
		$params['params']['modContentId'] = $subject->getIdentity();
		$params['params']['modError'] = 1;
		return $params;
	  }
	}
  }

  // SHOWING LINK ON "Sitepage Plugin".
  public function onMenuInitialize_SitepageSuggestFriend($row) {
	$sugg_check = Engine_Api::_()->suggestion()->getModSettings('sitepage', 'link');
	$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
	$subject = Engine_Api::_()->core()->getSubject();
	$page_flag = 0;
	if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.package.enable', 1)) {
	  if ($subject->expiration_date <= date("Y-m-d H:i:s")) {
		$page_flag = 1;
	  }
	}

	$modContentObj = Engine_Api::_()->suggestion()->getSuggestedFriend('sitepage', $subject->getIdentity(), 1);
	if (!empty($modContentObj)) {
	  $contentCreatePopup = @COUNT($modContentObj);
	}

	if (!empty($contentCreatePopup) && !empty($sugg_check) && !empty($user_id) && empty($subject->closed) && !empty($subject->approved) && empty($subject->declined) && !empty($subject->draft) && empty($page_flag)) {
	  $params = $row->params;
	  return array(
		  'label' => $row->label,
		  'class' => $params['class'],
		  'route' => 'default',
		  'params' => array(
			  'module' => 'suggestion',
			  'controller' => 'index',
			  'action' => 'switch-popup',
			  'modName' => 'sitepage',
			  'modContentId' => $subject->getIdentity(),
			  'modError' => 1,
			  'format' => 'smoothbox',
		  ),
	  );
	}
  }

  // Show "Suggest to Friends" link for Sitepage-Evant.
  public function onMenuInitialize_SitepageEventSuggestFriend($row) {
	$sugg_check = Engine_Api::_()->suggestion()->getModSettings('sitepageevent', 'link');
	$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
	if (!empty($sugg_check) && !empty($user_id)) {
	  $viewer = Engine_Api::_()->user()->getViewer();
	  $subject = Engine_Api::_()->core()->getSubject();

	  $modContentObj = Engine_Api::_()->suggestion()->getSuggestedFriend('sitepageevent', $subject->getIdentity(), 1);
	  if (!empty($modContentObj)) {
		$contentCreatePopup = @COUNT($modContentObj);
	  }

	  if (!empty($contentCreatePopup) && !empty($subject->search)) {
		return array(
			'label' => $row->label,
			'class' => 'smoothbox',
			'icon' => $row->params['icon'],
			'route' => 'default',
			'params' => array(
				'module' => 'suggestion',
				'controller' => 'index',
				'action' => 'switch-popup',
				'modContentId' => $subject->getIdentity(),
				'modName' => 'page_event',
				'format' => 'smoothbox',
			),
		);
	  }
	}
  }

  // SHOWING ON "RECIPE VIEW PAFE".
  public function onMenuInitialize_RecipeSuggestFriend($row) {
	$sugg_check = Engine_Api::_()->suggestion()->getModSettings('recipe', 'link');

	$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
	$subject = Engine_Api::_()->core()->getSubject();
	if (!empty($sugg_check) && !empty($user_id) && !empty($subject->approved) && empty($subject->closed) && !empty($subject->draft)) {
	  $subject = Engine_Api::_()->core()->getSubject();

	  $modContentObj = Engine_Api::_()->suggestion()->getSuggestedFriend('recipe', $subject->getIdentity(), 1);
	  if (!empty($modContentObj)) {
		$contentCreatePopup = @COUNT($modContentObj);
	  }

	  if (!empty($contentCreatePopup) && !empty($subject->search)) {
		return array(
			'label' => $row->label,
			'class' => 'smoothbox buttonlink',
			// 'icon' => $row->params['icon'],
			'route' => 'default',
			'params' => array(
				'module' => 'suggestion',
				'controller' => 'index',
				'action' => 'switch-popup',
				'modContentId' => $subject->getIdentity(),
				'modName' => 'recipe',
				'format' => 'smoothbox',
			),
		);
	  }
	}
  }

  // SHOWING LINK ON "Sitebusiness Plugin".
  public function onMenuInitialize_SitebusinessSuggestFriend($row) {
	$sugg_check = Engine_Api::_()->suggestion()->getModSettings('sitebusiness', 'link');
	$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
	$subject = Engine_Api::_()->core()->getSubject();
	$page_flag = 0;
	if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebusiness.package.enable', 1)) {
	  if ($subject->expiration_date <= date("Y-m-d H:i:s")) {
		$page_flag = 1;
	  }
	}

	$modContentObj = Engine_Api::_()->suggestion()->getSuggestedFriend('sitebusiness', $subject->getIdentity(), 1);
	if (!empty($modContentObj)) {
	  $contentCreatePopup = @COUNT($modContentObj);
	}

	if (!empty($contentCreatePopup) && !empty($sugg_check) && !empty($user_id) && empty($subject->closed) && !empty($subject->approved) && empty($subject->declined) && !empty($subject->draft) && empty($page_flag)) {
	  $params = $row->params;
	  return array(
		  'label' => $row->label,
		  'class' => $params['class'],
		  'route' => 'default',
		  'params' => array(
			  'module' => 'suggestion',
			  'controller' => 'index',
			  'action' => 'switch-popup',
			  'modName' => 'sitebusiness',
			  'modContentId' => $subject->getIdentity(),
			  'modError' => 1,
			  'format' => 'smoothbox',
		  ),
	  );
	}
  }

  // Show "Suggest to Friends" link for Sitebusiness-Evant.
  public function onMenuInitialize_SitebusinessEventSuggestFriend($row) {
	$sugg_check = Engine_Api::_()->suggestion()->getModSettings('sitebusiness', 'link');
	$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
	if (!empty($sugg_check) && !empty($user_id)) {
	  $viewer = Engine_Api::_()->user()->getViewer();
	  $subject = Engine_Api::_()->core()->getSubject();

	  $modContentObj = Engine_Api::_()->suggestion()->getSuggestedFriend('sitebusinessevent', $subject->getIdentity(), 1);
	  if (!empty($modContentObj)) {
		$contentCreatePopup = @COUNT($modContentObj);
	  }

	  if (!empty($contentCreatePopup) && !empty($subject->search)) {
		return array(
			'label' => $row->label,
			'class' => 'smoothbox',
			'icon' => $row->params['icon'],
			'route' => 'default',
			'params' => array(
				'module' => 'suggestion',
				'controller' => 'index',
				'action' => 'switch-popup',
				'modContentId' => $subject->getIdentity(),
				'modName' => 'business_event',
				'format' => 'smoothbox',
			),
		);
	  }
	}
  }
  
  
  // SHOWING LINK ON "Sitegroup Plugin".
  public function onMenuInitialize_SitegroupSuggestFriend($row) {
	$sugg_check = Engine_Api::_()->suggestion()->getModSettings('sitegroup', 'link');
	$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
	$subject = Engine_Api::_()->core()->getSubject();
	$page_flag = 0;
	if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.package.enable', 1)) {
	  if ($subject->expiration_date <= date("Y-m-d H:i:s")) {
		$page_flag = 1;
	  }
	}

	$modContentObj = Engine_Api::_()->suggestion()->getSuggestedFriend('sitegroup', $subject->getIdentity(), 1);
	if (!empty($modContentObj)) {
	  $contentCreatePopup = @COUNT($modContentObj);
	}

	if (!empty($contentCreatePopup) && !empty($sugg_check) && !empty($user_id) && empty($subject->closed) && !empty($subject->approved) && empty($subject->declined) && !empty($subject->draft) && empty($page_flag)) {
	  $params = $row->params;
	  return array(
		  'label' => $row->label,
		  'class' => $params['class'],
		  'route' => 'default',
		  'params' => array(
			  'module' => 'suggestion',
			  'controller' => 'index',
			  'action' => 'switch-popup',
			  'modName' => 'sitegroup',
			  'modContentId' => $subject->getIdentity(),
			  'modError' => 1,
			  'format' => 'smoothbox',
		  ),
	  );
	}
  }
  
  // Show "Suggest to Friends" link for Sitebusiness-Evant.
  public function onMenuInitialize_SitegroupEventSuggestFriend($row) {
	$sugg_check = Engine_Api::_()->suggestion()->getModSettings('sitegroup', 'link');
	$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
	if (!empty($sugg_check) && !empty($user_id)) {
	  $viewer = Engine_Api::_()->user()->getViewer();
	  $subject = Engine_Api::_()->core()->getSubject();

	  $modContentObj = Engine_Api::_()->suggestion()->getSuggestedFriend('sitegroupevent', $subject->getIdentity(), 1);
	  if (!empty($modContentObj)) {
		$contentCreatePopup = @COUNT($modContentObj);
	  }

	  if (!empty($contentCreatePopup) && !empty($subject->search)) {
		return array(
			'label' => $row->label,
			'class' => 'smoothbox',
			'icon' => $row->params['icon'],
			'route' => 'default',
			'params' => array(
				'module' => 'suggestion',
				'controller' => 'index',
				'action' => 'switch-popup',
				'modContentId' => $subject->getIdentity(),
				'modName' => 'group_event',
				'format' => 'smoothbox',
			),
		);
	  }
	}
  }
}