<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
class Sitegroup_Widget_SitemobileDiscussionSitegroupController extends Engine_Content_Widget_Abstract {

  protected $_childCount;
  
  //ACTION FOR FETCHING THE DISCUSSIONS FOR THE GROUPS
  public function indexAction() { 	

    if (Engine_Api::_()->seaocore()->isSitemobileApp()) {
      return $this->setNoRender();
    }
    
    //DON'T RENDER IF THERE IS NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }
    
    //GET SITEGROUP SUBJECT
    $this->view->sitegroup = $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
    
    //GET GROUP ID
    $this->view->group_id = $sitegroup->group_id;


    //START PACKAGE LEVEL CHECK
    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupdiscussion")) {
        return $this->setNoRender();
      }
    } else {
      $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'sdicreate');
      if (empty($isGroupOwnerAllow)) {
        return $this->setNoRender();
      }
    }
    //END PACKAGE LEVEL CHECK
    //TOTAL TOPICS
    $topicCount = Engine_Api::_()->sitegroup()->getTotalCount($this->view->group_id, 'sitegroup', 'topics');  
    
    $topicComment = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'sdicreate');
    
    
    if (empty($topicComment) && empty($topicCount) && !(Engine_Api::_()->sitegroup()->showTabsWithoutContent())) {
      return $this->setNoRender();
    }
    

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }
    $this->view->canPost =  Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'sdicreate');
    //END MANAGE-ADMIN CHECK
    
		//GET PAGINATORS
		$this->view->paginator = $paginator = Engine_Api::_()->getDbtable('topics', 'sitegroup')->getGroupTopics($sitegroup->group_id);
		$this->view->paginator->setItemCountPerPage(10)->setCurrentPageNumber($this->_getParam('group', 1));
	  $this->_childCount = $paginator->getTotalItemCount();

  }

  public function getChildCount() {
    return $this->_childCount;
  }

}