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
class Sitegroup_Widget_PhotocommentSitegroupController extends Engine_Content_Widget_Abstract {

	//ACTION FOR SHOWING THE MOST COMMENTED PHOTOS ON GROUP PROFILE GROUP
  public function indexAction() {

  	//HERE WE CHECKING THE SITEGROUP ALBUM IS ENABLED OR NOT
		$sitegroupalbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum');
		if (!$sitegroupalbumEnabled) {
			return $this->setNoRender();
		}
    $is_mostcommentphoto = Zend_Registry::isRegistered('sitegroupalbum_ismostCommentedPhoto') ? Zend_Registry::get('sitegroupalbum_ismostCommentedPhoto') : null;

    //GET SITEGROUP SUBJECT
    $subject = Engine_Api::_()->core()->getSubject('sitegroup_group');

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

    if (empty($is_mostcommentphoto)) {
      return $this->setNoRender();
    }
    
    //SEARCH PARAMETER
    $params = array();
		$params['group_id'] = $subject->group_id;
		$params['orderby'] = 'comment_count DESC';
		$params['zero_count'] = 'comment_count';
		$params['limit'] = $this->_getParam('itemCount', 4);

    $photoTable = Engine_Api::_()->getDbtable('photos', 'sitegroup');
 		//MAKE PAGINATOR
    $this->view->paginator = $paginator = $photoTable->widgetPhotos($params);    
    $this->view->count =  $photoTable->countTotalPhotos($params);

    if (Count($paginator) <= 0) {
      return $this->setNoRender();
    }

    //SHOWS PHOTOS IN THE LIGHTBOX
    //$this->view->showLightBox = Engine_Api::_()->sitegroup()->canShowPhotoLightBox();
  }

}

?>