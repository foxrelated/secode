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
class List_Widget_VideoListController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {

		//DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('list_listing')) {
      return $this->setNoRender();
    }

		//GET VIEWER DETAIL
		$viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

    //GET USER LEVEL ID
//    if (!empty($viewer_id)) {
//      $level_id = Engine_Api::_()->user()->getViewer()->level_id;
//    } else {
//      $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
//    }

    //GET SUBJECT
    $this->view->list = $list = Engine_Api::_()->core()->getSubject('list_listing');

    //VIDEO IS ENABLED OR NOT
    $allowed_upload_videoEnable = Engine_Api::_()->list()->enableVideoPlugin();
    if (!$allowed_upload_videoEnable) {
      return $this->setNoRender();
    }
   
		//AUTHORIZATION CHECK
    $this->view->allowed_upload_video = Engine_Api::_()->list()->allowVideo($list, $viewer);

    //FETCH RESULTS
    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('clasfvideos', 'list')->getListingVideos($list->listing_id, 1);
    $this->view->paginator->setCurrentPageNumber($this->_getParam('page'));
    $this->view->paginator->setItemCountPerPage(10);

    $counter = $paginator->getTotalItemCount();

    if (empty($this->view->allowed_upload_video) && empty($counter)) {
      return $this->setNoRender();
    }

    //ADD VIDEO COUNT
    if ($this->_getParam('titleCount', false)) {
      $this->_childCount = $counter;
    }
    
		//IS SITEVIDEOVIEW MODULE ENABLED
    $this->view->sitevideoviewEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideoview');
  }

  public function getChildCount() {
    return $this->_childCount;
  }

}
