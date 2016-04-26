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
class Sitegroup_Widget_DiscussionSitegroupController extends Engine_Content_Widget_Abstract {

  protected $_childCount;
  
  //ACTION FOR FETCHING THE DISCUSSIONS FOR THE GROUP
  public function indexAction() { 	
  	
    //DON'T RENDER IF THERE IS NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }
    
    //GET SUBJECT
    if(Engine_Api::_()->core()->getSubject()->getType() == 'sitegroup_group') {
    	$this->view->sitegroup = $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
    }
    else {
      $this->view->sitegroup = $sitegroup = Engine_Api::_()->core()->getSubject()->getParent();
    }
    
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
    $topicCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'sitegroup', 'topics');

		$topicComment = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'sdicreate');
   
    if (empty($topicComment) && empty($topicCount) && !(Engine_Api::_()->sitepage()->showTabsWithoutContent())) {
      return $this->setNoRender();
    }
    
    
//     //START MANAGE-ADMIN CHECK
//     $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'view');
//     if (empty($isManageAdmin)) {
//       return $this->setNoRender();
//     }
    $this->view->canPost =  Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'sdicreate');
    //END MANAGE-ADMIN CHECK
    
    //GET WHICH LAYOUT IS SET BY THE ADMIN
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0);
    
    //GET THIRD TYPE LAYOUT IS SET OR NOT
    $this->view->widgets = $widgets = Engine_Api::_()->sitegroup()->getwidget($layout, $sitegroup->group_id);
    
    //GET TAB ID
    $this->view->content_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroup.discussion-sitegroup', $sitegroup->group_id, $layout);
    
    //GET CURRENT TAB ID
    $this->view->module_tabid = $currenttabid = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
    
    //CHECK REQUEST IS ISAJAX OR NOT
    $this->view->isajax = $isajax = $this->_getParam('isajax', null);
    
    //SHOW TOP TITLE
    $this->view->showtoptitle = Engine_Api::_()->sitegroup()->showtoptitle($layout, $sitegroup->group_id);
    
    //CHECK REQUEST IS AJAX OR NOT OR CURRENT TAB ID OR LAYOUT
    if (!empty($isajax) || ($currenttabid == $this->view->identity) || ($widgets == 0)) {
      $this->view->identity_temp = Zend_Controller_Front::getInstance()->getRequest()->getParam('identity_temp', $currenttabid);
      $this->view->show_content = true;  

      //GET CURRENT GROUP NUMBER
      $group = $this->_getParam('group', 1);
      
      //GET PAGINATORS
      $this->view->paginators = $paginators = Engine_Api::_()->getDbtable('topics', 'sitegroup')->getGroupTopics($sitegroup->group_id);
      $paginators->setItemCountPerPage(10)->setCurrentPageNumber($group);

      //ADD COUNT TO TITLE IF CONFIGURED
      if ($this->_getParam('titleCount', false) && $paginators->getTotalItemCount() > 0) {
        $this->_childCount = $paginators->getTotalItemCount();
      }
    } else {
      $this->view->show_content = false;
      $this->view->identity_temp = $this->view->identity;
      $this->_childCount = Engine_Api::_()->sitegroup()->getTotalCount( $sitegroup->group_id, 'sitegroup', 'topics');
    }
  }

  public function getChildCount() {
    return $this->_childCount;
  }
}

?>