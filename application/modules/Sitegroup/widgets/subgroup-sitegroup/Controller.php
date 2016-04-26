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

class Sitegroup_Widget_SubgroupSitegroupController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

		//GET THE SUBJECT OF GROUP.
    $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
    $sitegroup_id = $sitegroup->group_id;
		$limit = $this->_getParam('itemCount', 3);
    $params = array();
    
    //FUNCTION CALL FORM THE DBTABLE AND PASS GROUP ID OR LIMIT OF GROUPS TO SHOW ON THE WIDGET.
		if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      //FUNCTION CALL FORM THE DBTABLE AND PASS GROUP ID OR LIMIT OF GROUPS TO SHOW ON THE WIDGET.
			$this->view->userListings = $userListings = Engine_Api::_()->getDbtable('favourites', 'sitegroup')->linkedGroups($sitegroup_id, $limit,$params, 'subgroup');
			// Set item count per group and current group number
			$this->view->userListings = $userListings->setItemCountPerPage(5);
			$this->view->userListings = $userListings->setCurrentPageNumber($this->_getParam('group', 1));
		  $this->_childCount = $userListings->getTotalItemCount();
      if ($userListings->getTotalItemCount() <= 0) {
				return $this->setNoRender();
			}
    } else {
			$this->view->userListings = $userListings = Engine_Api::_()->getDbtable('favourites', 'sitegroup')->linkedGroups($sitegroup_id, $limit,$params, 'subgroup');
			//NOT RENDER IF SITEGROUP COUNT ZERO
			if (!(count($this->view->userListings) > 0)) {
				return $this->setNoRender();
			}
    }
  }
}