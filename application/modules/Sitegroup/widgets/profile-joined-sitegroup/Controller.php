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
class Sitegroup_Widget_ProfileJoinedSitegroupController extends Seaocore_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {

    //DONT RENDER IF NOT AUTHORIZED
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }
    $this->view->subject_id = Engine_Api::_()->core()->getSubject()->getIdentity();

		//GET VIEWER
		$viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer->getIdentity();
    //GET SUBJECT AND CHECK AUTHENTICATION
    $subject = Engine_Api::_()->core()->getSubject();
    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      return $this->setNoRender();
    }

		$values = array();
    $this->view->isajax = $is_ajax = $this->_getParam('isajax', '');
    $this->view->textShow = $this->_getParam('textShow', 'Verified');
    $this->view->showMemberText = $this->_getParam('showMemberText', 1);
		$this->view->joinMoreGroups = $this->_getParam('joinMoreGroups', 1);

    $this->view->groupAdminJoined = $groupAdminJoined = $this->_getParam('groupAdminJoined', 2);
    $this->view->category_id =  $values['category_id'] = $this->_getParam('category_id',0);
    if( $is_ajax ) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }
    
		if($groupAdminJoined == 1) {

			//GET GROUPS
			$admingroups = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->getManageAdminGroups($subject->getIdentity());
			//GET STUFF
			$ids = array();
			foreach ($admingroups as $admingroup) {
				$ids[] = $admingroup->group_id;
			}
			$values['admingroups'] = $ids;

			$onlymember = Engine_Api::_()->getDbtable('membership', 'sitegroup')->getJoinGroups($subject->getIdentity(), 'onlymember');
	
			$onlymemberids = array();
			foreach ($onlymember as $onlymembers) {
				$onlymemberids[] = $onlymembers->group_id;
			} 
			if (!empty($onlymemberids)) {
			$values['admingroups'] = array_merge($onlymemberids, $values['admingroups']);
			}
		}
		else {
			$onlymember = Engine_Api::_()->getDbtable('membership', 'sitegroup')->getJoinGroups($subject->getIdentity(), 'onlymember');
	
			$onlymemberids = array();
			foreach ($onlymember as $onlymembers) {
				$onlymemberids[] = $onlymembers->group_id;
			} 
			$values['onlymember'] = $onlymemberids;
						if (empty($onlymemberids)) {
			return $this->setNoRender();
			}
		}

		$values['type'] = 'browse';
		$values['orderby'] = 'creation_date';
	//	$values['type_location'] = 'manage';
		if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('sitegroupmember'))  {
			$values['type_location'] = 'profilebrowseGroup';
    }

    $this->view->paginator = $paginator = Engine_Api::_()->sitegroup()->getSitegroupsPaginator($values);
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber($this->_getParam('group', 1));

    //DONT RENDER IF NOTHING TO SHOW
    if ($paginator->getTotalItemCount() <= 0) {
      return $this->setNoRender();
    }

    //ADD COUNT IF CONFIGURED
    if ($paginator->getTotalItemCount() > 0) {
      $this->_childCount = $paginator->getTotalItemCount();
    }

    //GROUP-RATING IS ENABLE OR NOT
    $this->view->ratngShow = (int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview');
    
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

  public function getChildCount() {
    return $this->_childCount;
  }

}
?>
