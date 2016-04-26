<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupmember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 
class Sitegroupmember_Widget_ProfileSitegroupmembersAnnouncementsController extends Seaocore_Content_Widget_Abstract
{

  protected $_childCount;

  public function indexAction(){
    
    //DONT RENDER THIS IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitegroup_group')) {
      return $this->setNoRender();
    }
    
    $groupannoucement = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'groupmember.announcement' , 1);
    $sitegroupmemberGetAnnouncement = Zend_Registry::isRegistered('sitegroupmemberGetAnnouncement') ? Zend_Registry::get('sitegroupmemberGetAnnouncement') : null;
    if (empty($groupannoucement) || empty($sitegroupmemberGetAnnouncement)) {
			return $this->setNoRender();
    }
    
    //GET VIEWER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
   
    //GET SITEGROUP SUBJECT
    $this->view->sitegroup = $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
    $this->view->user_layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0);

    // PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupmember")) {
        return $this->setNoRender();
      }
    } else {
      $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'smecreate');
      if (empty($isGroupOwnerAllow)) {
        return $this->setNoRender();
      }
    }
    // PACKAGE BASE PRIYACY END    
    $this->view->isajax = $is_ajax = $this->_getParam('isajax', '');
		if( $is_ajax ) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }
		$this->view->identity_temp = $this->view->identity;
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0);
    $this->view->content_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroupmember.profile-sitegroupmembers-announcements', $sitegroup->group_id, $layout);

    $this->view->allowView = false;
    if (!empty($viewer_id) && $viewer->level_id == 1) {
      $this->view->allowView = true;
    } 

    $this->view->announcements = Engine_Api::_()->getDbtable('announcements', 'sitegroup')->announcements(array('group_id' => $sitegroup->group_id, 'limit' => $this->_getParam('itemCount', 3) , 'hideExpired' => 1), array('announcement_id', 'title', 'body', 'creation_date'));
    $this->_childCount = count($this->view->announcements);
		if ($this->_childCount <= 0) {
			return $this->setNoRender();
		}
		

		if(!$this->view->isajax) {
			$this->view->params = $this->_getAllParams();
			if ($this->_getParam('loaded_by_ajax', true)) {
				$this->view->loaded_by_ajax = true;
				if ($this->_getParam('is_ajax_load', false)) {
					$this->view->is_ajax_load = true;
					$this->view->loaded_by_ajax = false;
					if (!$this->_getParam('onloadAdd', false))
						$this->getElement()->removeDecorator('Title');
					$this->getElement()->removeDecorator('Container');
				} else { 
					return;
				}
			}
			$this->view->showContent = true;    
    }
    else {
      $this->view->showContent = true;
    }
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}