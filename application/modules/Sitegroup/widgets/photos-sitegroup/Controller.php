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
class Sitegroup_Widget_PhotosSitegroupController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  //ACTION FOR GETTING THE ALBUMS AND PHOTOS
  public function indexAction() {

    //HERE WE CHECKING THE SITEGROUP ALBUM IS ENABLED OR NOT
    $sitegroupalbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum');
    if (!$sitegroupalbumEnabled) {
      return $this->setNoRender();
    }
    
    //DON'T RENDER IF SUNJECT IS NOT THERE
    if (!Engine_Api::_()->core()->hasSubject()) {
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

    $sitegroupalbum_hasPackageEnable = Zend_Registry::isRegistered('sitegroupalbum_hasPackageEnable') ? Zend_Registry::get('sitegroupalbum_hasPackageEnable') : null;

    //GET SITEGROUP SUBJECT
    if(Engine_Api::_()->core()->getSubject()->getType() == 'sitegroup_group') {
    	$this->view->sitegroup = $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
    }
    else {
      $this->view->sitegroup = $sitegroup = Engine_Api::_()->core()->getSubject()->getParent();
    }
 
    //START PACKAGE WORK
    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupalbum")) {
        return $this->setNoRender();
      }
    } else {
      $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'spcreate');
      if (empty($isGroupOwnerAllow)) {
        //return $this->setNoRender();
      }
    }
    //END PACKAGE WORK
  
    //TOTAL ALBUMS
    $albumCount = Engine_Api::_()->sitegroup()->getTotalCount($sitegroup->group_id, 'sitegroup', 'albums');     
    $photoCreate = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'spcreate');
    
//    //START MANAGE-ADMIN CHECK
//    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'view');
//    if (empty($isManageAdmin)) {
//      return $this->setNoRender();
//    }

    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($isManageAdmin)) {
      $this->view->can_edit = $canEdit = 0;
    } else {
      $this->view->can_edit = $canEdit = 1;
    }

    if (empty($photoCreate) && empty($albumCount) && empty($canEdit) && !(Engine_Api::_()->sitegroup()->showTabsWithoutContent())) {
      return $this->setNoRender();
    }

    if (empty($sitegroupalbum_hasPackageEnable)) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK    
    
    //GET WHICH LAYOUT IS SET BY THE ADMIN
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0);

    //GET THIRD TYPE LAYOUT IS SET OR NOT
    $this->view->widgets = $widgets = Engine_Api::_()->sitegroup()->getwidget($layout, $sitegroup->group_id);

    //GET REQUEST
    $zendRequest = Zend_Controller_Front::getInstance()->getRequest();

    //GET TAB ID
    $this->view->content_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroup.photos-sitegroup', $sitegroup->group_id, $layout);

    //GET CURRENT TAB ID
    $this->view->module_tabid = $currenttabid = $zendRequest->getParam('tab', null);

    //CHECK REQUEST IS ISAJAX OR NOT
    $this->view->isajax = $isajax = $this->_getParam('isajax', null);

    //SHOW TOP TITLE
    $this->view->showtoptitle = Engine_Api::_()->sitegroup()->showtoptitle($layout, $sitegroup->group_id);

    //CHECK REQUEST IS AJAX OR NOT OR CURRENT TAB ID OR LAYOUT
    if (!empty($isajax) || ($currenttabid == $this->view->identity) || ($widgets == 0)) {

      $this->view->identity_temp = $zendRequest->getParam('identity_temp', $currenttabid);
      $this->view->show_content = true;

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
      $this->view->albums_order = $albums_order = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupalbum.albumsorder', 1);

      //GET CURRENT GROUP NUMBER OF ALBUM
      $currentAlbumGroupNumbers = $this->_getParam('group', 1);

      //SEND CURRENT GROUP NUMBER OF ALBUM TO THE TPL
      $this->view->currentAlbumGroupNumbers = $currentAlbumGroupNumbers;

      //SET ALBUMS PARAMS
      $paramsAlbum = array();
      $paramsAlbum['group_id'] = $sitegroup->group_id;

      //GET ALBUM COUNT
      $this->view->album_count = $album_count = Engine_Api::_()->getDbtable('albums', 'sitegroup')->getAlbumsCount($paramsAlbum);
      
      //START ALBUMS PAGINATION
      $groups_vars = Engine_Api::_()->sitegroup()->makeGroup($album_count, $albums_per_group, $currentAlbumGroupNumbers);
      $groups_array = Array();
      for ($y = 0; $y <= $groups_vars[2] - 1; $y++) {
        if ($y + 1 == $groups_vars[1]) {
          $links = "1";
        } else {
          $links = "0";
        }
        $groups_array[$y] = Array('groups' => $y + 1,
            'links' => $links);
      }

      $this->view->groupsarray = $groups_array;
      $this->view->maxgroups = $groups_vars[2];
      $this->view->pstarts = 1;
      //END ALBUMS PAGINATION
      
      //SET ALBUMS PARAMS
      $paramsAlbum['start'] = $albums_per_group;
      $paramsAlbum['end'] = $groups_vars[0];
      if(empty($albums_order)) {
        $paramsAlbum['orderby'] = 'album_id ASC';
      } else {
        $paramsAlbum['orderby'] = 'album_id DESC';
      }
      $paramsAlbum['getSpecialField'] = 0;

      $fecthAlbums = Engine_Api::_()->getDbtable('albums', 'sitegroup')->getAlbums($paramsAlbum);
      if (!empty($fecthAlbums)) {
        $this->view->album = $this->view->paginator = $fecthAlbums;
      }

      //SET PHOTOS PARAMS
      $paramsPhoto = array();
      $paramsPhoto['group_id'] = $sitegroup->group_id;
      $paramsPhoto['user_id'] = $sitegroup->owner_id;
      $paramsPhoto['album_id'] = $this->view->default_album_id = Engine_Api::_()->getItemTable('sitegroup_album')->getDefaultAlbum($sitegroup->group_id)->album_id;
      
      //FETCHING ALL PHOTOS
      $this->view->total_images = $total_photo = Engine_Api::_()->getDbtable('photos', 'sitegroup')->getPhotosCount($paramsPhoto);

      //SEND CURRENT GROUP NUMBER TO THE TPL
      $this->view->currentGroupNumbers = $currentGroupNumbers = $this->_getParam('groups', 1);

      //SEND PHOTOS PER GROUP TO THE TPL
      $this->view->photos_per_group = $photos_per_group = $zendRequest->getParam('itemCount_photo', 100);

      if(empty($photos_per_group)) {
        $this->view->photos_per_group = $photos_per_group = 100;
      }

      //START PHOTOS PAGINATION
      $group_vars = Engine_Api::_()->sitegroup()->makeGroup($total_photo, $photos_per_group, $currentGroupNumbers);
      $paramsPhoto['start'] = $photos_per_group;
      $paramsPhoto['end'] = $group_vars[0];
      $paramsPhoto['widgetName'] = 'Photos By Others';
      if(empty($albums_order)) {
        $paramsPhoto['photosorder'] = 'album_id ASC';
      } else {
        $paramsPhoto['photosorder'] = 'album_id DESC';
      }
      $this->view->paginators = Engine_Api::_()->getDbtable('photos', 'sitegroup')->getPhotos($paramsPhoto);
      $group_array = Array();
      for ($x = 0; $x <= $group_vars[2] - 1; $x++) {
        if ($x + 1 == $group_vars[1]) {
          $link = "1";
        } else {
          $link = "0";
        }
        $group_array[$x] = Array('group' => $x + 1,
            'link' => $link);
      }
      $this->view->grouparray = $group_array;
      $this->view->maxgroup = $group_vars[2];
      $this->view->pstart = 1;
      //END PHOTOS PAGINATION      
    } else {
      $this->view->show_content = false;
      $this->view->identity_temp = $this->view->identity;
    }

    //SET PHOTOS PARAMS
    $paramsPhoto = array();
    $paramsPhoto['group_id'] = $sitegroup->group_id;

    //SET COUNT TO THE TITLE
    $this->_childCount = $this->view->locale()->toNumber(Engine_Api::_()->getDbtable('photos', 'sitegroup')->getPhotosCount($paramsPhoto));

    //if((!$fecthAlbums) && empty($canEdit) && !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupalbum.hide.autogenerated', 1)) {
       //return $this->setNoRender();  
   // }
  }

  public function getChildCount() {
    return $this->_childCount;
  }

}

?>
