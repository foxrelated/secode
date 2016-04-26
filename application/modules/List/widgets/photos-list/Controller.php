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
class List_Widget_PhotosListController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {

		//DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('list_listing')) {
      return $this->setNoRender();
    }

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

    //GET LISTING SUBJECT
    $this->view->list = $list = Engine_Api::_()->core()->getSubject('list_listing');
   
		//GET LEVEL SETTING
    $this->view->allowed_upload_photo = Engine_Api::_()->authorization()->isAllowed($list, $viewer, 'photo');
    
    //GET PAGINATOR
    $this->view->album = $album = $list->getSingletonAlbum();
    $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
    $this->view->total_images = $paginator->getTotalItemCount();
    $paginator->setCurrentPageNumber($this->_getParam('page'));
    $paginator->setItemCountPerPage(20);

    if (empty($this->view->allowed_upload_photo) && empty($this->view->total_images)) {
      return $this->setNoRender();
    }
  
    //ADD COUNT TO TITLE
    if ($this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0) {
      $this->_childCount = $paginator->getTotalItemCount();
    }
  }

  public function getChildCount() {
    return $this->_childCount;
  }
}