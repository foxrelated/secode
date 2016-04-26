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
class Sitegroup_Widget_MostdiscussionSitegroupController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $params = array();
    $params['totalgroups'] = $this->_getParam('itemCount', 3);
    $params['category_id'] = $this->_getParam('category_id', 0);
    $params['featured'] = $this->_getParam('featured', 0);
    $params['sponsored'] = $this->_getParam('sponsored', 0);

    //GET MOST DISCUSSED GROUPS
    $this->view->sitegroups = Engine_Api::_()->getDbTable('groups', 'sitegroup')->getDiscussedGroup($params);

    //IF THERE IS NO SITEGROUP THEN SET NO RENDER
    if ( !(count($this->view->sitegroups) > 0) ) {
      return $this->setNoRender();
    }
  }

}
?>