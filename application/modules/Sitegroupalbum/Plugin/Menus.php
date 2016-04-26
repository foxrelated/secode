<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupalbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroupalbum_Plugin_Menus {

  public function canViewAlbums() {

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroupalbum.album.show.menu', 1)) {
      return false;
    }

    $table = Engine_Api::_()->getDbtable('albums', 'sitegroup');
    $rName = $table->info('name');
    $table_groups = Engine_Api::_()->getDbtable('groups', 'sitegroup');
    $rName_groups = $table_groups->info('name');
    $select = $table->select()
                    ->setIntegrityCheck(false)
                    ->from($rName_groups, array('photo_id', 'title as sitegroup_title'))
                    ->join($rName, $rName . '.group_id = ' . $rName_groups . '.group_id')
                    ->where($rName .'.search = ?', 1);

    $select = $select
                    ->where($rName_groups . '.closed = ?', '0')
                    ->where($rName_groups . '.approved = ?', '1')
                    ->where($rName_groups . '.search = ?', '1')
                    ->where($rName_groups . '.declined = ?', '0')
                    ->where($rName_groups . '.draft = ?', '1');
    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
      $select->where($rName_groups . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    }
    $row = $table->fetchAll($select);
    $count = count($row);
    if (empty($count)) {
      return false;
    }
    return true;
  }

   //SITEMOBILE GROUP ALBUM MENUS
  public function onMenuInitialize_SitegroupalbumViewAlbums($row) {

    if(Engine_Api::_()->seaocore()->isSitemobileApp()){
     return false; 
    }
    // $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    //GET ALBUM ID
    $album_id = $subject->getIdentity();

    //GET ALBUM ITEM
    $album = Engine_Api::_()->getItem('sitegroup_album', $album_id);

    if (empty($album))
      return false;

    $group_id = $album->group_id;
    //SET ALBUMS PARAMS
    $paramsAlbum = array();
    $paramsAlbum['group_id'] = $group_id;
    //GET ALBUM COUNT
    $album_count = Engine_Api::_()->getDbtable('albums', 'sitegroup')->getAlbumsCount($paramsAlbum);

    //CHECKS
    if ($album_count <= 1) {
      return false;
    }

    return array(
        'label' => 'View Albums',
        'class' => 'ui-btn-action',
        'route' => 'sitegroup_albumphoto_general',
        'params' => array(
            'action' => 'view-album',
            'album_id' => $subject->getIdentity(),
            'group_id' => $group_id,
            'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
  }

  public function onMenuInitialize_SitegroupalbumAdd($row) {

    //$viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $upload_photo = 0;
    $engineApiSitegroup = Engine_Api::_()->sitegroup();
    //GET ALBUM ID
    $album_id = $subject->getIdentity();

    //GET ALBUM ITEM
    $album = Engine_Api::_()->getItem('sitegroup_album', $album_id);

    if (empty($album))
      return false;

    $group_id = $album->group_id;
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

    $isManageAdmin = $engineApiSitegroup->isManageAdmin($sitegroup, 'spcreate');
    if (empty($isManageAdmin)) {
      $canCreatePhoto = 0;
    } else {
      $canCreatePhoto = 1;
    }

    if ($canCreatePhoto == 1 && ($engineApiSitegroup->isGroupOwner($sitegroup) || $album->default_value == 1)) {
      $upload_photo = 1;
    }

    if (empty($upload_photo)) {
      return false;
    }

    return array(
        'label' => 'Add More Photos',
        'class' => 'ui-btn-action',
        'route' => 'sitegroup_photoalbumupload',
        'params' => array(
            'action' => 'upload',
            'album_id' => $subject->getIdentity(),
            'group_id' => $group_id,
            'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
  }

  public function onMenuInitialize_SitegroupalbumEdit() {

    //$viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    //GET ALBUM ID
    $album_id = $subject->getIdentity();

    //GET ALBUM ITEM
    $album = Engine_Api::_()->getItem('sitegroup_album', $album_id);
    if (empty($album))
      return false;
    $group_id = $album->group_id;

    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

    $engineApiSitegroup = Engine_Api::_()->sitegroup();

    $isManageAdmin = $engineApiSitegroup->isManageAdmin($sitegroup, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }

    if (empty($can_edit)) {
      return false;
    }
    return array(
        'label' => 'Edit Album',        
        'route' => 'sitegroup_albumphoto_general',
        'class' => 'ui-btn-action smoothbox',
        'params' => array(
            'action' => 'edit',
            'album_id' => $subject->getIdentity(),
            'group_id' => $group_id
        )
    );
  }

  public function onMenuInitialize_SitegroupalbumDelete() {

    // $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    //GET ALBUM ID
    $album_id = $subject->getIdentity();

    //GET ALBUM ITEM
    $album = Engine_Api::_()->getItem('sitegroup_album', $album_id);
    if (empty($album))
      return false;
    $group_id = $album->group_id;
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

    $engineApiSitegroup = Engine_Api::_()->sitegroup();

    $isManageAdmin = $engineApiSitegroup->isManageAdmin($sitegroup, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }

    if (empty($can_edit)) {
      return false;
    }

    //SET DEFAULT ALBUM VALUE
    $default_value = $album->default_value;
    if ($default_value == 1) {
      return false;
    }

    return array(
        'label' => 'Delete Album',
        'route' => 'sitegroup_albumphoto_general',
        'class' => 'ui-btn-danger smoothbox',
        'params' => array(
            'action' => 'delete',
            'album_id' => $subject->getIdentity(),
            'group_id' => $group_id
        )
    );
  }

  //PHOTO VIEW GROUP OPTIONS

  public function onMenuInitialize_SitegroupalbumPhotoEdit($row) {

    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $subject = Engine_Api::_()->core()->getSubject();
    //GET ALBUM ID
    $album_id = $subject->album_id;

    //GET GROUP ID
    $group_id = $subject->group_id;

    //GET SITEGROUP ITEM
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //PHOTO OWNER, GROUP OWNER AND SUPER-ADMIN CAN EDIT PHOTO
    if ($viewer_id == $subject->user_id || $can_edit == 1) {
      $canEdit = 1;
    } else {
      $canEdit = 0;
    }
    //CHECK FOR EDIT
    if (empty($canEdit)) {
      return false;
    }

    return array(
        'label' => 'Edit',
        'route' => 'sitegroup_imagephoto_specific',
        'class' => 'ui-btn-action smoothbox',
        'params' => array(
            'action' => 'photo-edit',
            'photo_id' => $subject->getIdentity(),
            'album_id' => $album_id,
            'group_id' => $group_id,
            //'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
  }

  public function onMenuInitialize_SitegroupalbumPhotoDelete($row) {

    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $subject = Engine_Api::_()->core()->getSubject();
    //GET ALBUM ID
    $album_id = $subject->album_id;

    //GET GROUP ID
    $group_id = $subject->group_id;

    //GET SITEGROUP ITEM
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //PHOTO OWNER, GROUP OWNER AND SUPER-ADMIN CAN EDIT PHOTO
    if ($viewer_id == $subject->user_id || $can_edit == 1) {
      $canDelete = 1;
    } else {
      $canDelete = 0;
    }
    //CHECK FOR EDIT
    if (empty($canDelete)) {
      return false;
    }
    return array(
        'label' => 'Delete',
        'route' => 'sitegroup_imagephoto_specific',
        'class' => 'ui-btn-danger smoothbox',
        'params' => array(
            'action' => 'remove',
            'photo_id' => $subject->getIdentity(),
            'album_id' => $album_id,
            'group_id' => $group_id,
        )
    );
  }

  public function onMenuInitialize_SitegroupalbumPhotoShare($row) {
    $subject = Engine_Api::_()->core()->getSubject();

    if (!SEA_PHOTOLIGHTBOX_SHARE) {
      return false;
    }
    return array(
        'label' => 'Share',
        'class' => 'ui-btn-action smoothbox',
        'route' => 'default',
        'params' => array(
            'module' => 'activity',
            'action' => 'share',
            'type' => $subject->getType(),
            'id' => $subject->getIdentity(),
        )
    );
  }

  public function onMenuInitialize_SitegroupalbumPhotoReport($row) {
    $subject = Engine_Api::_()->core()->getSubject();

    if (!SEA_PHOTOLIGHTBOX_REPORT) {
      return false;
    }
    return array(
        'label' => 'Report',
        'class' => 'ui-btn-action smoothbox',
        'route' => 'default',
        'params' => array(
            'module' => 'core',
            'controller' => 'report',
            'action' => 'create',
            'subject' => $subject->getGuid(),
        )
    );
  }

// $this->canEdit && SEA_PHOTOLIGHTBOX_MAKEPROFILEPHOTO
  public function onMenuInitialize_SitegroupalbumPhotoProfile($row) {
    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $subject = Engine_Api::_()->core()->getSubject();
    //GET ALBUM ID
    $album_id = $subject->album_id;

    //GET GROUP ID
    $group_id = $subject->group_id;

    //GET SITEGROUP ITEM
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //PHOTO OWNER, GROUP OWNER AND SUPER-ADMIN CAN EDIT PHOTO
    if ($viewer_id == $subject->user_id || $can_edit == 1) {
      $canEdit = 1;
    } else {
      $canEdit = 0;
    }
    //CHECK FOR EDIT
    if (empty($canEdit) || !SEA_PHOTOLIGHTBOX_MAKEPROFILEPHOTO) {
      return false;
    }
    return array(
        'label' => 'Make Group Profile Photo',
        'route' => 'sitegroup_imagephoto_specific',
        'class' => 'ui-btn-action smoothbox',
        'params' => array(
            'module' => 'sitegroup',
            'controller' => 'photo',
            'action' => 'make-group-profile-photo',
            'photo' => $subject->getGuid(),
            'group_id' => $group_id,
        )
    );
  }
}
?>