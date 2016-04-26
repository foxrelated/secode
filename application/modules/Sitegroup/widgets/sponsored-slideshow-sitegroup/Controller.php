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
class Sitegroup_Widget_SponsoredSlideshowSitegroupController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $params = array();
    $params['totalgroups'] = $this->_getParam('itemCount', 10);
    $params['category_id'] = $this->_getParam('category_id', 0);   
    
    //GET GROUP DATAS
    $columnsArray = array('group_id', 'title', 'body', 'group_url', 'owner_id', 'category_id', 'photo_id', 'creation_date', 'price', 'location','featured', 'sponsored', 'view_count', 'comment_count', 'like_count', 'follow_count');
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
        $columnsArray[] = 'member_count';
    }
    $columnsArray[] = 'member_title';

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
        $columnsArray[] = 'review_count';
        $columnsArray[] = 'rating';
    }     
    $this->view->show_slideshow_object = $sitegroup = Engine_Api::_()->getDbTable('groups', 'sitegroup')->getListings('Sponosred Slideshow', $params, null, null, $columnsArray);
    $this->view->sitegroup_sponsored = $sitegroup_sponsored =1;

    $this->view->num_of_slideshow = count($sitegroup);
    if ( !(count($sitegroup) > 0) || empty($sitegroup_sponsored) ) {
      return $this->setNoRender();
    }
  }

}

?>