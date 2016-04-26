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
class List_Widget_OverviewListController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('list_listing')) {
      return $this->setNoRender();
    }

    //GET LISTING SUBJECT
    $this->view->list = $list = Engine_Api::_()->core()->getSubject('list_listing');

		//GET VIEWER DETAIL
		$viewer = Engine_Api::_()->user()->getViewer();
		$this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    //GET USER LEVEL ID
    if (!empty($viewer_id)) {
      $level_id = Engine_Api::_()->user()->getViewer()->level_id;
    } else {
      $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
    }

		//OVERVIEW IS ALLOWED OR NOT
		$allowOverview = Engine_Api::_()->authorization()->getPermission($level_id, 'list_listing', 'overview');
        
		//DON'T RENDER IF NOT AUTHORIZED
    if (empty($list->overview) && (empty($allowOverview) || $list->owner_id != $viewer_id)){    
      return $this->setNoRender();
    }
  }
}