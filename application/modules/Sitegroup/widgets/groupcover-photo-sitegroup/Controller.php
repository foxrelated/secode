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

class Sitegroup_Widget_GroupcoverPhotoSitegroupController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //DON'T RENDER IF THERE IS NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }
    
		if (!Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitegroupalbum' )) {
			return $this->setNoRender();
		}
		
    $this->view->show_member = $show_member = $this->_getParam('show_member', 1);

    //GET SITEGROUP SUBJECT
    $this->view->sitegroup = $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
		$this->view->photo = $photo = Engine_Api::_()->getItem('sitegroup_photo', $sitegroup->group_cover);
		//CHECK REQUEST IS ISAJAX OR NOT
    $this->view->isAjax = $isAjax = $this->_getParam('isAjax', null);
		if (empty($show_member) && empty($sitegroup->group_cover) && empty($can_edit)) {
			return $this->setNoRender();
		}
		
    $zendRequest = Zend_Controller_Front::getInstance()->getRequest();
    $this->view->identity_temp = $currenttabid = $zendRequest->getParam('tab', null);

    //START MANAGE-ADMIN CHECK
    $this->view->can_edit = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
     $this->view->paginatorCount = 0;
    if($isAjax && Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitegroupmember' )) {
			$this->view->paginator = $paginator = Engine_Api::_()->getDbtable('membership', 'sitegroup')->getJoinMembers($sitegroup->group_id);
    }
  }
}