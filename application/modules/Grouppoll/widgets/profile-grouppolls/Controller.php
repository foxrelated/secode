<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Grouppoll
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2010-12-08 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Grouppoll_Widget_ProfileGrouppollsController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  { 
		//DONT RENDER THIS IF NOT AUTHORIZED
    $viewer = Engine_Api::_()->user()->getViewer();
    if ( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

		//GET VIEWER INFORMATION
		$this->view->viewer_id = $viewer_id = $viewer->getIdentity();
		if (!empty($viewer_id)) {
			$this->view->level_id = $level_id = $viewer->level_id;
		}
		else {
			$this->view->level_id = $level_id = 0;
		}

    //GET SUBJECT AND GROUP ID
		$subject = Engine_Api::_()->core()->getSubject('group');
		$group_subject = $subject;
		$this->view->group_id = $group_id = $subject->group_id;

		//GET SEARCHING PARAMETERS
    $this->view->page = $page = $this->_getParam('page', 1);
    $this->view->search = $search = $this->_getParam('search');
		$this->view->selectbox = $selectbox = $this->_getParam('selectbox');
		$this->view->checkbox = $checkbox = $this->_getParam('checkbox');
		$values = array();
		if (!empty($search)) {
			$values['search'] = $search;
		}
		if (!empty($selectbox)) {
			$values['orderby'] = $selectbox;
		}
		else {
			$values['orderby'] = 'creation_date';
		}
		if (!empty($checkbox) && $checkbox == 1) {
			$values['owner_id'] = $viewer_id;
		}

		//WHO CAN VIEW THE GROUP-POLL
		if ( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

 		//CHECK WHO CAN CREATE POLLS
		$this->view->can_create = Engine_Api::_()->authorization()->isAllowed($subject, $viewer, 'gpcreate');
 
 		//GET GROUP OWNER ID
 		$this->view->group_owner_id = $group_owner_id = $subject->user_id;  

		$values['group_id'] = $group_id;
		if ($viewer_id == $group_owner_id || $level_id == 1) {
			$values['show_poll'] = 0;
			$this->view->paginator = $paginator = Engine_Api::_()->getItemTable('grouppoll_poll')->getGrouppollsPaginator($values);
		}
		else {
			$values['show_poll'] = 1;
			$values['poll_owner_id'] = $viewer_id;
			$this->view->paginator = $paginator = Engine_Api::_()->getItemTable('grouppoll_poll')->getGrouppollsPaginator($values);
		}

		//10 POLLS PER PAGE
    $paginator->setItemCountPerPage(10);
    $this->view->paginator->setCurrentPageNumber($page);

		//ADD NUMBER OF POLLS IN TAB
    if ( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }

    //MAKE PAGINATOR
    $currentPageNumber = $this->_getParam('page', 1);
    $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('grouppoll_poll')->getGrouppollsPaginator($values);
    $paginator->setItemCountPerPage(10)->setCurrentPageNumber($currentPageNumber);
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}
?>