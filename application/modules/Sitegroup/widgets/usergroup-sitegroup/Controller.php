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
class Sitegroup_Widget_UsergroupSitegroupController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//DON'T RENDER THIS IF NOT AUTHORIZED
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //GET SUBJECT AND GROUP ID
    $this->view->sitegroup = $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');

    $params = array();
    $params['totalgroups'] = $this->_getParam('itemCount', 3);
    $params['category_id'] = $this->_getParam('category_id', 0);
    $params['featured'] = $this->_getParam('featured', 0);
    $params['sponsored'] = $this->_getParam('sponsored', 0);
    $params['popularity'] = $this->_getParam('popularity', 'view_count');
    $params['owner_id'] = $sitegroup->owner_id;
    $params['group_id'] = $sitegroup->group_id;

    $this->view->userBusiensses = $userBusiensses =Engine_Api::_()->getDbtable('groups', 'sitegroup')->userBusienss($params);

    if (!(count($this->view->userBusiensses) > 0)) {
      return $this->setNoRender();
    }
  }

}
?>