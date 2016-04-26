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
class Sitegroup_Widget_SponsoredSitegroupController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $params = array();

    $this->view->limit = $params['limit'] = $this->_getParam('itemCount', 4);
    $this->view->category_id = $params['category_id'] = $this->_getParam('category_id', 0);
    $this->view->interval = $this->_getParam('interval', 300);
    $this->view->titletruncation = $this->_getParam('truncation', 18); 
    
    //GET SPONSERED GROUPS
    $totalSitegroup = Engine_Api::_()->getDbTable('groups', 'sitegroup')->getListings('Total Sponsored Sitegroup',$params, null, null, array('group_id'));
    $sitegroup_sponcerd = Zend_Registry::isRegistered('sitegroup_sponcerd') ? Zend_Registry::get('sitegroup_sponcerd') : null;

    //NO RENDER IF SPONSERED GROUPS ARE ZERO
    $this->view->totalCount = $totalSitegroup->count();
    if ( !($this->view->totalCount > 0) ) {
      return $this->setNoRender();
    }

    //SEND GROUP DATA TO TPL
    $columnsArray = array('group_id', 'title', 'group_url', 'owner_id', 'category_id', 'photo_id', 'featured', 'sponsored', 'view_count', 'comment_count', 'like_count', 'follow_count');
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
        $columnsArray[] = 'member_count';
    }
    $columnsArray[] = 'member_title';

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
        $columnsArray[] = 'review_count';
        $columnsArray[] = 'rating';
    }    
    $this->view->sitegroups = $sitegroups = Engine_Api::_()->getDbTable('groups', 'sitegroup')->getListings('Sponsored Sitegroup',$params, null, null, $columnsArray);

    $this->view->count = $sitegroups->count();
    if ( empty($sitegroup_sponcerd) ) {
      return $this->setNoRender();
    }
  }

}
?>