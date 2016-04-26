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
class Sitegroupmember_Widget_MostactiveSitegroupController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
  
    $params = array();
    $params['active_groups'] = $this->_getParam('active_groups', 'member_count');
		$this->view->statistics =$this->_getParam('statistics', 'members');

    //GET SITEGROUP FOR MOST LIKE
    $this->view->sitegroups = Engine_Api::_()->getDbTable('groups', 'sitegroup')->getListings('Most Active Groups',$params,'', '', array('group_id', 'photo_id','title', 'body', 'group_url', 'owner_id', 'view_count', 'like_count', 'comment_count', 'member_count', 'member_title'));
		$this->view->statistics =$this->_getParam('statistics', 'members');
    $this->view->statistics =$this->_getParam('statistics', 'members');

  
    //NOT RENDER IF SITEGROUP COUNT ZERO
    if (!(count($this->view->sitegroups) > 0)) {
      return $this->setNoRender();
    }
  }
}