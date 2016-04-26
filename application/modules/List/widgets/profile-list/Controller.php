<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Controller.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Widget_ProfileListController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {

		//GET VIEWER DETAIL
		$viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

    //GET USER LEVEL ID
    if (!empty($viewer_id)) {
      $level_id = Engine_Api::_()->user()->getViewer()->level_id;
    } else {
      $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
    }

		//WHO CAN VIEW THE LISTINGS
		$can_view = Engine_Api::_()->authorization()->getPermission($level_id, 'list_listing', 'view');
		if(empty($can_view)) {
			return $this->setNoRender();
		}

		//DON'T RENDER IF SUBJECT IS NOT SET
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

		//GET SUBJECT
    $subject = Engine_Api::_()->core()->getSubject();
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

		//FETCH RESULTS
    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('listings', 'list')->getListsPaginator(array(
                'type' => 'browse',
                'orderby' => 'creation_date',
                'user_id' => $subject->getIdentity(),
        ));
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    //DONT RENDER IF RESULTS IS ZERO
    if ($paginator->getTotalItemCount() <= 0) {
      return $this->setNoRender();
    }

    //ADD LISTING COUNT
    if ($this->_getParam('titleCount', false)) {
      $this->_childCount = $paginator->getTotalItemCount();
    }

    //RATING IS ENABLED OR NOT
    $this->view->ratngShow = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('list.rating', 1);
  }

  public function getChildCount() {
    return $this->_childCount;
  }
}