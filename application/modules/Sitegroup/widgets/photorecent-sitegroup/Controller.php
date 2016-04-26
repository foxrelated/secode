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
class Sitegroup_Widget_PhotorecentSitegroupController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  //ACTION FOR GETTING THE PHOTOS IN THE STRIP
  public function indexAction() {  	

  	//HERE WE CHECKING THE SITEGROUP ALBUM IS ENABLED OR NOT
		$sitegroupalbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum');
		if (!$sitegroupalbumEnabled) {
			return $this->setNoRender();
		}

  	//DON'T RENDER IF SUBJECT IS NOT THERE
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }
    
    //GET SITEGROUP SUBJECT
    $this->view->sitegroup_subject = $subject = Engine_Api::_()->core()->getSubject('sitegroup_group');
    
    //PACKAGE WORK START
    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitegroup()->allowPackageContent($subject->package_id, "modules", "sitegroupalbum")) {
        return $this->setNoRender();
      }
    } else {
      $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($subject, 'spcreate');
      if (empty($isGroupOwnerAllow)) {
        return $this->setNoRender();
      }
    }
    //PACKAGE WORK END    
    
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($subject, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }

    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($subject, 'edit');
    if (empty($isManageAdmin)) {
      $this->view->can_edit = 0;
    } else {
      $this->view->can_edit = 1;
    }

    $this->view->sitegroupalbum_isManageAdmin = $sitegroupalbum_isManageAdmin = Zend_Registry::isRegistered('sitegroupalbum_isManageAdmin') ? Zend_Registry::get('sitegroupalbum_isManageAdmin') : null;
    if (empty($sitegroupalbum_isManageAdmin)) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK    
    
    //GET LIMIT
    $this->view->limit = $limit = $this->_getParam('itemCount', 7);
    
    //CHECK REQUEST IS AJAX OR NOT
    $this->view->is_ajax = $is_ajax = $this->_getParam('isajax', '');
    
    //IF REQUEST IS AJAX THEN UPDATE PHOTO TABLE
    $phototable = Engine_Api::_()->getDbtable('photos', 'sitegroup');
    if (!empty($is_ajax)) {
      $phototable->update(array('photo_hide' => 1), array('photo_id = ?' => $this->_getParam('hide_photo_id', null)));
    }
    
  	//SET GROUP PHOTO PARAMS
    $paramsPhoto = array();	    
    $paramsPhoto['group_id'] = $subject->group_id;
    $paramsPhoto['photo_hide'] = 0;
    $paramsPhoto['file_id'] = $subject->photo_id;
    $paramsPhoto['orderby'] = 'creation_date DESC';
    $paramsPhoto['start'] = $limit;

    //MAKE PAGINATOR
    $this->view->paginator = Engine_Api::_()->getDbtable('photos', 'sitegroup')->getPhotos($paramsPhoto);
 
    //SET GROUP PHOTO PARAMS
    $paramsPhoto['photo_hide'] = 1;
    
    //FETCHING PHOTOS
		$this->view->count = Engine_Api::_()->getDbtable('photos', 'sitegroup')->getPhotosCount($paramsPhoto);

    //IF COUNT IS ZERO THEN NO RENDER
    if (!(count($this->view->paginator) > 0) && !($this->view->count) > 0) {
      return $this->setNoRender();
    }

    //SET GROUP PHOTO PARAMS
    $paramsPhoto['photo_hide'] = 0;
    
    //FETCHING PHOTOS
    $this->view->row_count = Engine_Api::_()->getDbtable('photos', 'sitegroup')->getPhotosCount($paramsPhoto);

    //IF COUNT IS ZERO THEN NO RENDER
    if ($this->view->row_count == 0 && $this->view->can_edit == 0) {
      return $this->setNoRender();
    }
    
    //GETTING THE CURRENT TAB ID
    $this->view->currenttabid = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
  }

  public function getChildCount() {
    return $this->_childCount;
  }
}

?>