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
class Sitegroup_Widget_GroupCoverInformationSitegroupController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    if (!Engine_Api::_()->core()->hasSubject() || !Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum')) {
      return $this->setNoRender();
    }

    //GET SETTING
    $statisticsElement = $this->_getParam('showContent', array("mainPhoto", "title", "followButton", "likeButton", "followCount", "likeCount"));
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
			$statisticsElement['']="memberCount";
			$statisticsElement[''] = 'addButton';
			$statisticsElement[''] = 'joinButton';
			$statisticsElement[''] = 'leaveButton';
		}
		$this->view->showContent  = $statisticsElement;
	
		 		
    if(empty($this->view->showContent)) {
      $this->view->showContent = array();
    }
	
    //GET VIEWER ID
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    //GET SITEGROUP SUBJECT
    $this->view->sitegroup = $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
    $this->view->photo = $photo = Engine_Api::_()->getItem('sitegroup_photo', $sitegroup->group_cover);
    $this->view->columnHeight = $this->_getParam('columnHeight', '300');
    //START MANAGE-ADMIN CHECK
    $this->view->can_edit = $can_edit = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
			$this->view->allowGroup = Engine_Api::_()->sitegroup()->allowInThisGroup($sitegroup, "sitegroupmember", 'smecreate');
    }

    $this->view->cover_params = array('top' => 0, 'left' => 0);
    if($sitegroup->group_cover) {
			$tableAlbum = Engine_Api::_()->getDbtable('albums', 'sitegroup');
			$album = $tableAlbum->getSpecialAlbum($sitegroup, 'cover');
			
			if($album->cover_params)
				$this->view->cover_params = $album->cover_params;
		}
  }

}

?>