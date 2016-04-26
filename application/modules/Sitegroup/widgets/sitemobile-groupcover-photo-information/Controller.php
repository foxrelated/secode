<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupmember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Widget_SitemobileGroupcoverPhotoInformationController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {
  
    $this->_mobileAppFile = true;
    //DON'T RENDER IF THERE IS NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    $this->view->strachPhoto = $this->_getParam('strachPhoto', 0);
    $this->view->showContent = $this->_getParam('showContent', array("mainPhoto", "title", "category", "subcategory", "subsubcategory",  "likeButton", "followButton", "joinButton", "addButton", "leaveButton", "description", "phone", "email", "website", "badge"));

    if(empty($this->view->showContent)) {
      $this->view->showContent = array();
    }

    //GET VIEWER INFORMATION
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    $this->view->sitegroup = $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
    $this->view->can_edit = $can_edit = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    $this->view->allowGroup = Engine_Api::_()->sitegroup()->allowInThisGroup($sitegroup, "sitegroupmember", 'smecreate');
    $this->view->cover_params = array('top' => 0, 'left' => 0);

    if(Engine_Api::_()->hasModuleBootstrap('sitegroupbadge') && isset($sitegroup->badge_id))  {
			$this->view->sitegroupbadges_value = Engine_Api::_()->getApi('settings', 'core')->sitegroupbadge_badgeprofile_widgets;
			$this->view->sitegroupbadge = Engine_Api::_()->getItem('sitegroupbadge_badge', $sitegroup->badge_id);
    }

    if (Engine_Api::_()->hasModuleBootstrap('sitegroupalbum') && $sitegroup->group_cover) {
      $this->view->photo = $photo = Engine_Api::_()->getItem('sitegroup_photo', $sitegroup->group_cover);
      $tableAlbum = Engine_Api::_()->getDbtable('albums', 'sitegroup');
      $album = $tableAlbum->getSpecialAlbum($sitegroup, 'cover');
      if ($album->cover_params)
        $this->view->cover_params = $album->cover_params;
    }
    $this->view->sitegroupTags = $sitegroup->tags()->getTagMaps();
    $this->view->resource_id = $resource_id = $sitegroup->getIdentity();
    $this->view->resource_type = $resource_type = $sitegroup->getType();
    $this->view->follow_count = Engine_Api::_()->getDbtable('follows', 'seaocore')->numberOfFollow($resource_type, $resource_id);
    $this->view->subcategory_name = '';
    $this->view->subsubcategory_name = '';
    $categoriesTable = Engine_Api::_()->getDbTable('categories', 'sitegroup');
    $this->view->category_name = $categoriesTable->getCategory($sitegroup->category_id)->category_name;
    if(isset($categoriesTable->getCategory($sitegroup->subcategory_id)->category_name))
    $this->view->subcategory_name = $categoriesTable->getCategory($sitegroup->subcategory_id)->category_name;
    if(isset($categoriesTable->getCategory($sitegroup->subsubcategory_id)->category_name))
    $this->view->subsubcategory_name = $categoriesTable->getCategory($sitegroup->subsubcategory_id)->category_name;
    
    //PARAMS
    $this->view->can_share = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.share', 1);
    
    if(isset($viewer->level_id))
    $this->showMessageOwner = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'auth');
    
  }

}