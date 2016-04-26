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
class Sitegroup_Widget_RecentlypostedSitegroupController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $params = array();
    $params['totalgroups'] = $this->_getParam('itemCount', 3);
    $params['category_id'] = $this->_getParam('category_id', 0);
    $params['featured'] = $this->_getParam('featured', 0);
    $params['sponsored'] = $this->_getParam('sponsored', 0);    
    
    $statisticsElement = array("likeCount" , "followCount", "viewCount" , "commentCount");  
		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
			$statisticsElement[]="reviewCount";
		}
		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
			$statisticsElement[]="memberCount";
			//$this->view->membercalled = $this->_getParam('membercalled', 1);
		}
    $this->view->statistics = $this->_getParam('statistics', $statisticsElement);
    
    $columnsArray = array('group_id', 'title', 'group_url', 'owner_id', 'photo_id', 'view_count', 'comment_count', 'like_count', 'follow_count');
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
        $columnsArray[] = 'member_count';
    }
    $columnsArray[] = 'member_title';

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
        $columnsArray[] = 'review_count';
        $columnsArray[] = 'rating';
    }       
    $this->view->sitegroups = Engine_Api::_()->getDbTable('groups', 'sitegroup')->getListings('Recently Posted List', $params, null, null, $columnsArray);


    if ( !(count($this->view->sitegroups) > 0) ) {
      return $this->setNoRender();
    }
  }

}
?>