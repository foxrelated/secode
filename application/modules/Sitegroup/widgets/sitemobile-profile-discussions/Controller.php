<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Controller.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

class Sitegroup_Widget_SitemobileProfileDiscussionsController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  //ACTION FOR FETCHING THE DISCUSSIONS FOR THE GROUPS
  public function indexAction() {

    //DON'T RENDER IF THERE IS NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() == 'sitegroup_group') {
      return $this->setNoRender();
    } else {
      if ($subject->getType() == 'sitegroupevent_event') {
        $sitegroup = $subject->getParentPage();
      } elseif ($subject->getType() == 'sitegroupmusic_playlist') {
        $sitegroup = $subject->getParentType();
      } else {
        $sitegroup = $subject->getParent();
      }
    }

    if (!$sitegroup || $sitegroup->getType() !== 'sitegroup_group') {
      return $this->setNoRender();
    }

    //GET SITEGROUP SUBJECT
    $this->view->sitegroup = $sitegroup;

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }

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

    $this->view->canPost = $topicComment = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'sdicreate');

    if (empty($topicComment)) {
      return $this->setNoRender();
    }

    //GET CURRENT GROUP NUMBER     
    $group = $this->_getParam('group', 1);

    //GET PAGINATORS
    $this->view->paginators = $paginators = Engine_Api::_()->getDbtable('topics', 'sitegroup')->getGroupTopics($sitegroup->group_id, array('resource_type' => $subject->getType(), 'resource_id' => $subject->getIdentity()));
    $paginators->setItemCountPerPage(100)->setCurrentPageNumber($group);

    //ADD COUNT TO TITLE IF CONFIGURED
    if ($this->_getParam('titleCount', false) && $paginators->getTotalItemCount() > 0) {
      $this->_childCount = $paginators->getTotalItemCount();
    }
  }

  public function getChildCount() {
    return $this->_childCount;
  }

}