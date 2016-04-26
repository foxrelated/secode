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
class Sitegroup_Widget_SitemobilePhotosSitegroupController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  //ACTION FOR GETTING THE ALBUMS AND PHOTOS
  public function indexAction() {

    //HERE WE CHECKING THE SITEGROUP ALBUM IS ENABLED OR NOT
    $sitegroupalbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum');
    if (!$sitegroupalbumEnabled) {
      return $this->setNoRender();
    }

    //GET HOW MANY ALBUMS DO YOU WANT SHOW ON PER GROUP
    $this->view->itemCount = $albums_per_group = $this->_getParam('itemCount', 10);

    //GET HOW MANY PHOTOS DO YOU WANT SHOW ON PER GROUP
    $this->view->itemCount_photo = $this->_getParam('itemCount_photo', 100);

    //ALBUMS ORDER
    $this->view->albums_order = $this->_getParam('albumsorder', 1);

    //GET VIEWER ID
    $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //DON'T RENDER IF SUNJECT IS NOT THERE
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //GET SITEGROUP SUBJECT
    $this->view->sitegroup = $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');

    //GET GROUP ID
    $group_id = $sitegroup->group_id;

    //START PACKAGE WORK
    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupalbum")) {
        return $this->setNoRender();
      }
    } else {
      $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'spcreate');
      if (empty($isGroupOwnerAllow)) {
        return $this->setNoRender();
      }
    }
    //END PACKAGE WORK

    //TOTAL ALBUMS
    $albumCount = Engine_Api::_()->sitegroup()->getTotalCount($group_id, 'sitegroup', 'albums');     
    $photoCreate = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'spcreate');
    
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }

    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($isManageAdmin)) {
      $this->view->can_edit = $canEdit = 0;
    } else {
      $this->view->can_edit = $canEdit = 1;
    }
    
    if (empty($photoCreate) && empty($albumCount) && empty($canEdit) && !(Engine_Api::_()->sitegroup()->showTabsWithoutContent())) {
      return $this->setNoRender();
    }

    //GET REQUEST
    $zendRequest = Zend_Controller_Front::getInstance()->getRequest();

		$isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'spcreate');
		if ($isManageAdmin || $canEdit) {
			$this->view->allowed_upload_photo = 1;
		} else {
			$this->view->allowed_upload_photo = 0;
		}

    //ALBUMS PER GROUP
		$this->view->albums_per_group = $albums_per_group = $zendRequest->getParam('itemCount', 10);

		if(empty($albums_per_group)) {
			$this->view->albums_per_group = $albums_per_group = 10;
		}

		//ALBUMS ORDER
		$this->view->albums_order = $albums_order = $zendRequest->getParam('albumsorder', 1);

		//GET CURRENT GROUP NUMBER OF ALBUM
		$currentAlbumGroupNumbers = $group = $this->_getParam('group', 1);

		//SEND CURRENT GROUP NUMBER OF ALBUM TO THE TPL
		$this->view->currentAlbumGroupNumbers = $currentAlbumGroupNumbers;

		//SET ALBUMS PARAMS
		$paramsAlbum = array();
		$paramsAlbum['group_id'] = $group_id;

		//GET ALBUM COUNT
		$this->view->album_count = $album_count = Engine_Api::_()->getDbtable('albums', 'sitegroup')->getAlbumsCount($paramsAlbum);
		
		//SET ALBUMS PARAMS
		if(empty($albums_order)) {
			$paramsAlbum['orderby'] = 'album_id ASC';
		} else {
			$paramsAlbum['orderby'] = 'album_id DESC';
		}
		$paramsAlbum['getSpecialField'] = 0;

		$fecthAlbums = Engine_Api::_()->getDbtable('albums', 'sitegroup')->getAlbums($paramsAlbum);

		$album = $this->view->album = $paginator = $this->view->paginator = $fecthAlbums;
		$this->view->paginator = $paginator->setItemCountPerPage($albums_per_group);
		$this->view->paginator->setCurrentPageNumber($this->_getParam('group', 1));

		//SET PHOTOS PARAMS
		$paramsPhoto = array();
		$paramsPhoto['group_id'] = $group_id;
		$paramsPhoto['user_id'] = $sitegroup->owner_id;
		$paramsPhoto['album_id'] = $this->view->default_album_id = Engine_Api::_()->getItemTable('sitegroup_album')->getDefaultAlbum($group_id)->album_id;
		
		//FETCHING ALL PHOTOS
		$this->view->total_images = $total_photo = Engine_Api::_()->getDbtable('photos', 'sitegroup')->getPhotosCount($paramsPhoto);

		//SEND PHOTOS PER GROUP TO THE TPL
		$this->view->photos_per_group = $photos_per_group = $zendRequest->getParam('itemCount_photo', 100);

		if(empty($photos_per_group)) {
			$this->view->photos_per_group = $photos_per_group = 100;
		}

		$this->view->paginators = $paginators = Engine_Api::_()->getDbtable('photos', 'sitegroup')->getPhotos($paramsPhoto);
		$this->view->paginators = $paginators->setItemCountPerPage($photos_per_group);
		$this->view->paginators->setCurrentPageNumber($this->_getParam('groups', 1));
		//SET PHOTOS PARAMS
		$paramsPhoto = array();
		$paramsPhoto['group_id'] = $sitegroup->group_id;

    //SET COUNT TO THE TITLE
    $this->_childCount = $this->view->locale()->toNumber(Engine_Api::_()->getDbtable('photos', 'sitegroup')->getPhotosCount($paramsPhoto));

  }

  public function getChildCount() {
    return $this->_childCount;
  }

}