<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupalbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroupalbum_Widget_AlbumContentController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {
  
    //GET VIEWER
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    //GET VIEWER ID
    $viewer_id = $viewer->getIdentity();
    $photosorder = $this->_getParam('photosorder', 1);

    $request = Zend_Controller_Front::getInstance()->getRequest();

    $engineApiSitegroup = Engine_Api::_()->sitegroup();
    //GET ALBUM ID
    $this->view->album_id = $album_id = $request->getParam('album_id');

    //GET ALBUM ITEM
    $this->view->album = $album = Engine_Api::_()->getItem('sitegroup_album', $album_id);

    //SEND TAB ID TO THE TPL
    $this->view->tab_selected_id = $request->getParam('tab');

    //GET SITEGROUP ID
    $group_id = $request->getParam('group_id');

    //GET SITESITEGROUP ITEM
    $this->view->sitegroup = $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = $engineApiSitegroup->isManageAdmin($sitegroup, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }

    $isManageAdmin = $engineApiSitegroup->isManageAdmin($sitegroup, 'comment');
    if (empty($isManageAdmin)) {
      $this->view->can_comment = 0;
    } else {
      $this->view->can_comment = 1;
    }

    $isManageAdmin = $engineApiSitegroup->isManageAdmin($sitegroup, 'spcreate');
    if (empty($isManageAdmin)) {
      $this->view->canCreatePhoto = $canCreatePhoto = 0;
    } else {
      $this->view->canCreatePhoto = $canCreatePhoto = 1;
    }
    $this->view->is_ajax = $is_ajax = $this->_getParam('isajax', '');
    if( $is_ajax ) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }
    $isManageAdmin = $engineApiSitegroup->isManageAdmin($sitegroup, 'edit');
    if (empty($isManageAdmin)) {
      $this->view->can_edit = 0;
    } else {
      $this->view->can_edit = 1;
    }
    if (empty($viewer->level_id)) {
      $this->view->level_id = $level_id = 0;
    }
    else {
      $this->view->level_id = $level_id = $viewer->level_id;
    }
    $this->view->allowView = false;
    if (!empty($viewer_id) && $level_id == 1) {
      $auth = Engine_Api::_()->authorization()->context;
      $this->view->allowView = $auth->isAllowed($sitegroup, 'everyone', 'view') === 1 ? true : false ||$auth->isAllowed($sitegroup, 'registered', 'view') === 1 ? true : false;
    } 

    //END MANAGE-ADMIN CHECK
    //CHECK THAT USER CAN UPLOAD PHOTO OR NOT
    $this->view->upload_photo = 0;

    if ($canCreatePhoto == 1 && ($engineApiSitegroup->isGroupOwner($sitegroup) || $album->default_value == 1)) {
      $this->view->upload_photo = 1;
    }

    //GET CURRENT SITEGROUP NUMBER
    $currentGroupNumbers = $request->getParam('groups', 1);

    //SEND CURRENT SITEGROUP NUMBER TO THE TPL
    $this->view->currentGroupNumbers = $currentGroupNumbers;

    //SEND PHOTOS PER SITEGROUP TO THE TPL
    $this->view->photos_per_group = $photos_per_group = 20;

    //SET SITEGROUP PHOTO PARAMS
    $paramsPhoto = array();
    $paramsPhoto['group_id'] = $group_id;
    $paramsPhoto['album_id'] = $album_id;
    $paramsPhoto['order'] = 'order ASC';
    $paramsPhoto['viewGroup'] = 1;
    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      $paramsPhoto['albumviewGroup'] = 1;
    }
    //FETCHING ALL PHOTOS
    $total_photo = Engine_Api::_()->getDbtable('photos', 'sitegroup')->getPhotosCount($paramsPhoto);
    if (!empty($total_photo)) {
      if (Engine_Api::_()->core()->hasSubject()) {
        Engine_Api::_()->core()->clearSubject();
      }
      Engine_Api::_()->core()->setSubject($album);
    }

    //SET DEFAULT ALBUM VALUE
    $this->view->default_value = $album->default_value;

    //SET ALBUMS PARAMS
    $paramsAlbum = array();
    $paramsAlbum['group_id'] = $group_id;
    $paramsAlbum['viewGroup'] = 1;
    
    //GET ALBUM COUNT
    $this->view->album_count =  Engine_Api::_()->getDbtable('albums', 'sitegroup')->getAlbumsCount($paramsAlbum);
    
    //MAKING PAGINATION 
    $group_vars = $engineApiSitegroup->makeGroup($total_photo, $photos_per_group, $currentGroupNumbers);
    $group_array = array();
    for ($x = 0; $x <= $group_vars[2] - 1; $x++) {
      if ($x + 1 == $group_vars[1]) {
        $link = "1";
      } else {
        $link = "0";
      }
      $group_array[$x] = array('group' => $x + 1, 'link' => $link);
    }
    $this->view->grouparray = $group_array;
    $this->view->maxgroup = $group_vars[2];
    $this->view->pstart = 1;

    //GET TOTAL IMAGES
    $this->view->total_images = $total_photo;

    //SET SITEGROUP PHOTO PARAMS
    $paramsPhoto['start'] = $photos_per_group;
    $paramsPhoto['end'] = $group_vars[0];
    $paramsPhoto['viewGroup'] = 1;
    $paramsPhoto['photosorder'] = $photosorder;
    $paramsPhoto['widgetName'] = 'Album Content';
    //GETTING THE PHOTOS ACCORDING TO LIMIT
    $this->view->photos = Engine_Api::_()->getDbtable('photos', 'sitegroup')->getPhotos($paramsPhoto);

    //INCREMENT VIEWS
    if ($album && !$album->getOwner()->isSelf(Engine_Api::_()->user()->getViewer())) {
      $album->view_count++;
    }

    //SAVE
    $album->save();

    //START: "SUGGEST TO FRIENDS" LINK WORK
    $group_flag = 0;
    $is_suggestion_enabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');
		if( !empty($is_suggestion_enabled) ) {
			$is_moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroup');
			$isSupport = Engine_Api::_()->getApi('suggestion', 'sitegroup')->isSupport();
			//HERE WE ARE DELETE THIS ALBUM SUGGESTION IF VIEWER HAVE
			if (!empty($is_moduleEnabled)) {
				Engine_Api::_()->getApi('suggestion', 'sitegroup')->deleteSuggestion($viewer_id, 'group_album', $request->getParam('album_id'), 'group_album_suggestion');
			}

			$SuggVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('suggestion')->version;
			$versionStatus = strcasecmp($SuggVersion, '4.1.7p1');
			if( $versionStatus >= 0 ){ 
				$modContentObj = Engine_Api::_()->suggestion()->getSuggestedFriend('sitegroupalbum', $request->getParam('album_id'), 1);
				if (!empty($modContentObj)) {
					$contentCreatePopup = @COUNT($modContentObj);
				}
			}

			if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.package.enable', 1)) {
				if ($sitegroup->expiration_date <= date("Y-m-d H:i:s")) {
					$group_flag = 1;
				}
			}
			if (!empty($contentCreatePopup) && !empty($isSupport) && empty($sitegroup->closed) && !empty($sitegroup->approved) && empty($sitegroup->declined) && !empty($sitegroup->draft) && empty($group_flag) && !empty($viewer_id) && !empty($is_suggestion_enabled)) {
				$this->view->albumSuggLink = Engine_Api::_()->suggestion()->getModSettings('sitegroup', 'album_sugg_link');
			}
		}
    //END: "SUGGEST TO FRIENDS" LINK WORK    
  }

}
?>