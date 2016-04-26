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

class Sitegroup_Widget_ParentgroupSitegroupController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {

		//GET THE SUBJECT OF GROUP.
    $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
    $sitegroup_id = $sitegroup->parent_id;
		$LIMIT = $this->_getParam('itemCount', 3);
    $params = array();
    
    //FUNCTION CALL FORM THE DBTABLE AND PASS GROUP ID OR LIMIT OF GROUPS TO SHOW ON THE WIDGET.
		$this->view->userListings = $userListings = Engine_Api::_()->getDbtable('favourites', 'sitegroup')->linkedGroups($sitegroup_id, $LIMIT,$params, 'parentgroup');

		//NOT RENDER IF SITEGROUP COUNT ZERO
		if (!(count($this->view->userListings) > 0)) {
      return $this->setNoRender();
    }
  }
}