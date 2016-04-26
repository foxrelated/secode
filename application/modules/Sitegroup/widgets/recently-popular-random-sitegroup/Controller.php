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
class Sitegroup_Widget_RecentlyPopularRandomSitegroupController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
  
//    //$this->view->is_ajax_load = true;
//    if ($this->_getParam('is_ajax_load', false)) {
//      $this->view->is_ajax_load = true;
//      if ($this->_getParam('contentpage', 1) > 1 || $this->_getParam('group', 1) > 1)
//        $this->getElement()->removeDecorator('Title');
//      $this->getElement()->removeDecorator('Container');
//    } else {
//
//      if(!$this->_getParam('detactLocation', 0)){
//        $this->view->is_ajax_load = true;
//      } else {
//				$this->getElement()->removeDecorator('Title');
//      }
//    }
    
    $this->view->titleLink = $this->_getParam('titleLink', null);
    $this->view->titleLinkPosition = $this->_getParam('titleLinkPosition', 'bottom');
//    $this->view->photoHeight = $this->_getParam('photoHeight', 0);
    $this->view->photoWidth = $this->_getParam('photoWidth', 0);
        
    if ($this->_getParam('is_ajax_load', false)) {
      $this->view->is_ajax_load = true;
      if (!$this->_getParam('detactLocation', 0) || $this->_getParam('contentpage', 1) > 1)
        $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    } else {
      if ($this->_getParam('detactLocation', 0))
        $this->getElement()->removeDecorator('Title');

      $this->view->is_ajax_load = !$this->_getParam('loaded_by_ajax', true);
    }     
    
    $params = array();
    
    $this->view->category_id = $params['category_id'] =  $category_id = $this->_getParam('category_id',0);
    $groupmember = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
    if (!empty($groupmember)) {
			$showTabArray = $this->_getParam('layouts_tabs', array("0" => "1", "1" => "2", "2" => "3", "3" => "4", "4" => '5', "5" => '6'));
    } else {
			$showTabArray = $this->_getParam('layouts_tabs', array("0" => "1", "1" => "2", "2" => "3", "3" => "4", "4" => '5'));
    }

    $ShowViewArray = $this->_getParam('layouts_views', array("0" => "1", "1" => "2", "2" => "3"));

    $defaultOrder = $this->_getParam('layouts_oder', 1);
    $this->view->columnWidth = $this->_getParam('columnWidth', 188);
    $this->view->columnHeight = $this->_getParam('columnHeight', 350);
    $this->view->showlikebutton = $this->_getParam('showlikebutton', 1);
    $this->view->showfeaturedLable = $this->_getParam('showfeaturedLable', 1);
    $this->view->showsponsoredLable = $this->_getParam('showsponsoredLable', 1);
    $this->view->showlocation = $this->_getParam('showlocation', 1);
    $this->view->showgetdirection = $this->_getParam('showgetdirection', 1);
    $this->view->showprice = $this->_getParam('showprice', 1);
    $this->view->showpostedBy = $this->_getParam('showpostedBy', 1);
    $this->view->showdate = $this->_getParam('showdate', 1);
    $this->view->turncation = $this->_getParam('turncation', 15);
    $this->view->listview_turncation = $this->_getParam('listview_turncation', 40);
    $statisticsElement = array("likeCount" , "followCount", "viewCount" , "commentCount");
		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
			$statisticsElement['']="reviewCount";
		}
		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
			$statisticsElement['']="memberCount";
		}
    $this->view->statistics = $this->_getParam('statistics', $statisticsElement);
    $this->view->enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.price.field', 1);
    $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);

    if($this->view->detactLocation) {
      $this->view->defaultLocationDistance = $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
      $params['latitude'] = $this->_getParam('latitude', 0);
      $params['longitude'] = $this->_getParam('longitude', 0);
    }

    $sitegroup_most_viewed = Zend_Registry::isRegistered('sitegroup_most_viewed') ? Zend_Registry::get('sitegroup_most_viewed') : null;
    $this->view->list_view = 0;
    $this->view->grid_view = 0;
    $this->view->map_view = 0;
    $this->view->defaultView = -1;
    $list_limit = 0;
    $grid_limit = 0;
    if (in_array("1", $ShowViewArray)) {
      $this->view->list_view = 1;
      $list_limit = $this->_getParam('list_limit', 10);
      if ($this->view->defaultView == -1 || $defaultOrder == 1)
        $this->view->defaultView = 0;
    }
    if (in_array("2", $ShowViewArray)) {
      $this->view->grid_view = 1;
      $grid_limit = $this->_getParam('grid_limit', 15);
      if ($this->view->defaultView == -1 || $defaultOrder == 2)
        $this->view->defaultView = 1;
    }
    if (in_array("3", $ShowViewArray)) {
      $this->view->map_view = 1;
      $list_limit = $this->_getParam('list_limit', 10);
      if ($this->view->defaultView == -1 || $defaultOrder == 3)
        $this->view->defaultView = 2;
    }
    if (empty($sitegroup_most_viewed)) {
      return $this->setNoRender();
    }

    $sitegroupRecently = array();
    $sitegroupViewed = array();
    $sitegroupRandom = array();
    $sitegroupFeatured = array();
    $sitegroupSponosred = array();
    $sitegroupJoined = array();
    $groupTable = Engine_Api::_()->getDbTable('groups', 'sitegroup');
    
    //GROUP-RATING IS ENABLE OR NOT
    $this->view->ratngShow = (int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview');    
    $columnsArray = array('group_id', 'title', 'group_url', 'owner_id', 'category_id', 'photo_id', 'price', 'location', 'creation_date', 'featured', 'sponsored', 'view_count', 'comment_count', 'like_count', 'follow_count');
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
        $columnsArray[] = 'member_count';
    }
    $columnsArray[] = 'member_title';

    if ($this->view->ratngShow) {
        $columnsArray[] = 'review_count';
        $columnsArray[] = 'rating';
    }     
    
    if (in_array("1", $showTabArray)) {
      // GET SITEGROUP SITEGROUP FOR RECENTLY POSTED
      $sitegroupRecently = $groupTable->getListings('Recently Posted', $params, null, null, $columnsArray);
    }
    if (in_array("2", $showTabArray)) {
      // GET SITEGROUP SITEGROUP FOR MOST VIEWES
      $sitegroupViewed = $groupTable->getListings('Most Viewed', $params, null, null, $columnsArray);
    }
    if (in_array("3", $showTabArray)) {
      $sitegroupRandom = $groupTable->getListings('Random', $params, null, null, $columnsArray);
    }

    if (in_array("4", $showTabArray)) {
      $sitegroupFeatured = $groupTable->getListings('Featured', $params, null, null, $columnsArray);
    }

    if (in_array("5", $showTabArray)) {
      $sitegroupSponosred = $groupTable->getListings('Sponosred', $params, null, null, $columnsArray);
    }
    
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
			if (in_array("6", $showTabArray)) {
				$sitegroupJoined = $groupTable->getListings('Most Joined', $params, null, null, $columnsArray);
			}
    }
    
//    if ((!(count($sitegroupRecently) > 0) && !(count($sitegroupViewed) > 0) && !(count($sitegroupRandom) > 0 ) && !(count($sitegroupFeatured) > 0 ) && !(count($sitegroupSponosred) > 0 )) || ($this->view->defaultView == -1)) {
//      return $this->setNoRender();
//    }
    
    $this->view->paramsLocation = $this->_getAllParams();  

    $tabsOrder = array();
    $tabs = array();
    $menuTabs = array();
    if (count($sitegroupRecently) > 0) {
      $tabs['recent'] = array('title' => 'Recent', 'tabShow' => 'Recently Posted');
      $tabsOrder['recent'] = $this->_getParam('recent_order', 1);
    }
    if (count($sitegroupViewed) > 0) {
      $tabs['popular'] = array('title' => 'Most Popular', 'tabShow' => 'Most Viewed');
      $tabsOrder['popular'] = $this->_getParam('popular_order', 2);
    }
    if (count($sitegroupRandom) > 0) {
      $tabs['random'] = array('title' => 'Random', 'tabShow' => 'Random');
      $tabsOrder['random'] = $this->_getParam('random_order', 3);
    }

    if (count($sitegroupFeatured) > 0) {
      $tabs['featured'] = array('title' => 'Featured', 'tabShow' => 'Featured');
      $tabsOrder['featured'] = $this->_getParam('featured_order', 4);
    }
    if (count($sitegroupSponosred) > 0) {
      $tabs['sponosred'] = array('title' => 'Sponsored', 'tabShow' => 'Sponosred');
      $tabsOrder['sponosred'] = $this->_getParam('sponosred_order', 5);
    }
    
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
			if (count($sitegroupJoined) > 0) {
				$tabs['mostjoined'] = array('title' => 'Most Joined', 'tabShow' => 'Most Joined');
				$tabsOrder['mostjoined'] = $this->_getParam('joined_order', 6);
			}
    }
    
    @asort($tabsOrder);
    $firstIndex = key($tabsOrder);
    foreach ($tabsOrder as $key => $value) {
      $menuTabs[$key] = $tabs[$key];
    }

    $this->view->tabs = $menuTabs;
    $this->view->active_tab_list = $list_limit;
    $this->view->active_tab_image = $grid_limit;
    $params['limit'] = $limit = $list_limit > $grid_limit ? $list_limit : $grid_limit;
    $this->view->identity = $this->_getParam('identity', $this->view->identity); 
    $this->view->sitegroupsitegroup = $sitegroup = $groupTable->getListings($menuTabs[$firstIndex]['tabShow'], $params, null, null, $columnsArray);

    $this->view->enableLocation = $checkLocation = Engine_Api::_()->sitegroup()->enableLocation();
    $this->view->sitegroup = '';

    if (!empty($this->view->map_view)) {

      $this->view->flageSponsored = 0;

      if (!empty($checkLocation)) {
        $sitegroup_temp = $ids = array();
        foreach ($sitegroup as $sitegroup_group) {
          $id = $sitegroup_group->getIdentity();
          $ids[] = $id;
          $sitegroup_temp[$id] = $sitegroup_group;
        }
        $values['group_ids'] = $ids;

        $this->view->locations = $locations = Engine_Api::_()->getDbtable('locations', 'sitegroup')->getLocation($values);
        foreach ($locations as $location) {
          if ($sitegroup_temp[$location->group_id]->sponsored) {
            $this->view->flageSponsored = 1;
            break;
          }
        }
        $this->view->sitegroup = $sitegroup_temp;
      }
    }
  }

}

?>