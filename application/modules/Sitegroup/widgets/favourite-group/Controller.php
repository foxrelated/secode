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

class Sitegroup_Widget_FavouriteGroupController extends Seaocore_Content_Widget_Abstract
{
protected $_childCount;
  public function indexAction()
  {
     
		//GET THE SUBJECT OF GROUP.
    $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
    $sitegroup_id = $sitegroup->group_id;
    $params = array();
    $params['category_id'] = $this->view->category_id = $this->_getParam('category_id', 0);
    $params['featured'] = $this->view->featured = $this->_getParam('featured', 0);
    $params['sponsored'] = $this->view->sponsored = $this->_getParam('sponsored', 0);
    $limit = $this->_getParam('itemCount', 3);

		if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      
      //FUNCTION CALL FORM THE DBTABLE AND PASS GROUP ID OR LIMIT OF GROUPS TO SHOW ON THE WIDGET.
			$this->view->userListings = $userListings = Engine_Api::_()->getDbtable('favourites', 'sitegroup')->linkedGroups($sitegroup_id, 10,$params);
			// Set item count per group and current group number
			$this->view->userListings = $userListings->setItemCountPerPage(5);
			$this->view->userListings = $userListings->setCurrentPageNumber($this->_getParam('group', 1));
		  $this->_childCount = $userListings->getTotalItemCount();
      if ($userListings->getTotalItemCount() <= 0) {
				return $this->setNoRender();
			}
    } else {
             //FUNCTION CALL FORM THE DBTABLE AND PASS GROUP ID OR LIMIT OF GROUPS TO SHOW ON THE WIDGET.
			$this->view->userListings = $userListings = Engine_Api::_()->getDbtable('favourites', 'sitegroup')->linkedGroups($sitegroup_id, $limit,$params);
			//NOT RENDER IF SITEGROUP COUNT ZERO
			if (!(count($this->view->userListings) > 0)) {
				return $this->setNoRender();
			}
    }    
  }

	public function getChildCount() {
    return $this->_childCount;
  }
}