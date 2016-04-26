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
class Sitegroupmember_Widget_MostjoinedSitegroupController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $params =array();
    $params['totalgroups'] = $this->_getParam('itemCount', 3);
    $params['category_id'] = $this->_getParam('category_id',0);
    $params['featured'] = $this->_getParam('featured',0);
    $params['sponsored'] = $this->_getParam('sponsored',0);

    //GET SITEGROUP FOR MOST LIKE
    $this->view->sitegroups = Engine_Api::_()->getDbTable('groups', 'sitegroup')->getListings('Most Joined',$params,'', '', array('group_id', 'photo_id','title', 'body', 'group_url', 'owner_id', 'member_count', 'member_title'));
  
    //NOT RENDER IF SITEGROUP COUNT ZERO
    if (!(count($this->view->sitegroups) > 0)) {
      return $this->setNoRender();
    }
  }
}