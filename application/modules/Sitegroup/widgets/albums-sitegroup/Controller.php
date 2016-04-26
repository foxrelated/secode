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
class Sitegroup_Widget_AlbumsSitegroupController extends Engine_Content_Widget_Abstract {

  protected $_childCount;
  
  //ACTION FOR SHOWING THE RANDOM ALBUMS AND PHOTOS BY OTHERS 
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

    //GET SITEGROUP SUBJECT
    $this->view->sitegroup = $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');

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

//    //START MANAGE-ADMIN CHECK
//    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'view');
//    if (empty($isManageAdmin)) {
//      return $this->setNoRender();
//    }
//    //END MANAGE-ADMIN CHECK  
    
    //SET ALBUMS PARAMS
    $paramsAlbum = array();
    $paramsAlbum['group_id'] = $sitegroup->group_id;  

    //GET ALBUMS COUNT
    $this->view->albumcount = Engine_Api::_()->getDbtable('albums', 'sitegroup')->getAlbumsCount($paramsAlbum);

    //GET VIEWER ID
    $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity(); 
    
    //SET ALBUMS PARAMS
    $paramsAlbum['orderby'] = ' RAND()'; 
    $paramsAlbum['start'] = 2; 
    $paramsAlbum['getSpecialField'] = 0;
    
    //FETCH ALBUMS
		$this->view->paginator = $fecthAlbums = Engine_Api::_()->getDbtable('albums', 'sitegroup')->getAlbums($paramsAlbum, null, array('album_id', 'photo_id', 'title', 'creation_date'));           
    
    //IF COUNT IS ZERO THEN NO RENDER
    if ($this->view->albumcount <= 0) {
      return $this->setNoRender();
    }

    //SET PHOTO PARAMS
    $paramsPhoto = array();
    $paramsPhoto['group_id'] = $sitegroup->group_id;
    $paramsPhoto['user_id'] = $sitegroup->owner_id;
    $paramsPhoto['album_id'] = Engine_Api::_()->getItemTable('sitegroup_album')->getDefaultAlbum($sitegroup->group_id)->album_id;;

    //GET TOTAL PHOTOS BY OTHERS
    $this->view->totalphotosothers = Engine_Api::_()->getDbtable('photos', 'sitegroup')->getPhotosCount($paramsPhoto);
    
    //SET PHOTO PARAMS
    $paramsPhoto['orderby'] = 'RAND()'; 
    $paramsPhoto['start'] = 4;  
       
    //GET ALL PHOTOS
    $this->view->paginators = Engine_Api::_()->getDbtable('photos', 'sitegroup')->getPhotos($paramsPhoto);
    
    //SEND CURRENT TAB ID TO THE TPL
    $this->view->tab_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroup.photos-sitegroup', $sitegroup->group_id, Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0));
  }

}

?>