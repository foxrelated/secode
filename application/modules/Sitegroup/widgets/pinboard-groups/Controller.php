<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Widget_PinboardGroupsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $this->view->params = $this->_getAllParams();
    if (!isset($this->view->params['noOfTimes']) || empty($this->view->params['noOfTimes']))
      $this->view->params['noOfTimes'] = 1000;
    $this->view->detactLocation = $this->_getParam('detactLocation', 0);
    if ($this->_getParam('autoload', true)) {
      $this->view->autoload = true;
      if ($this->_getParam('is_ajax_load', false)) {
        $this->view->is_ajax_load = true;
        $this->view->autoload = false;
        if ($this->_getParam('contentpage', 1) > 1)
          $this->getElement()->removeDecorator('Title');
        $this->getElement()->removeDecorator('Container');
      } else {
        //  $this->view->layoutColumn = $this->_getParam('layoutColumn', 'middle');
        $this->getElement()->removeDecorator('Title');
        //return;
      }
    } else {
      $this->view->is_ajax_load = $this->_getParam('is_ajax_load', false);
      if ($this->_getParam('contentpage', 1) > 1) {
        $this->getElement()->removeDecorator('Title');
        $this->getElement()->removeDecorator('Container');
      }
    }

    $params = array();
    $params['popularity'] = $this->view->popularity = $this->_getParam('popularity', 'group_id');

    $current_time = date("Y-m-d H:i:s");
    $params['totalgroups'] = $this->_getParam('itemCount', 12);
    $params['category_id'] = $this->_getParam('category_id', 0);
    $fea_spo = $this->_getParam('fea_spo', '');
    if ($fea_spo == 'featured') {
      $params['featured'] = 1;
    } elseif ($fea_spo == 'sponsored') {
      $params['sponsored'] = 1;
    } elseif ($fea_spo == 'fea_spo') {
      $params['sponsored'] = 1;
      $params['featured'] = 1;
    }
    $params['interval'] = $this->_getParam('interval', 'overall');
    if ($this->view->detactLocation) {
      $params['detactLocation'] = $this->view->detactLocation;
      $params['locationmiles'] = $this->_getParam('locationmiles', 0); //in miles
      $params['latitude'] = $this->_getParam('latitude', 0);
      $params['longitude'] = $this->_getParam('longitude', 0);
    }

    $params['paginator'] = 1;   
    
    //GET SITEGROUP SITEGROUP FOR MOST COMMENTED
    $columnsArray = array('body', 'group_id', 'title', 'group_url', 'owner_id', 'category_id', 'photo_id', 'price', 'location', 'creation_date', 'featured', 'sponsored', 'view_count', 'comment_count', 'like_count', 'follow_count');
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
        $columnsArray[] = 'member_count';
    }
    $columnsArray[] = 'member_title';

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
        $columnsArray[] = 'review_count';
        $columnsArray[] = 'rating';
    }      
    $this->view->sitegroups = $paginator = Engine_Api::_()->getDbTable('groups', 'sitegroup')->getListings('Pin Board', $params, null, null, $columnsArray);

    //GET LISTINGS
    $this->view->totalCount = $paginator->getTotalItemCount();
    $group = $this->_getParam('contentpage', 1);
    $paginator->setCurrentPageNumber($group);
    $paginator->setItemCountPerPage($params['totalgroups']);
    $this->view->currentgroup = $group;
    //DON'T RENDER IF RESULTS IS ZERO
    if ($this->view->totalCount <= 0) {
      return $this->setNoRender();
    }

    $this->view->countGroup = $paginator->count();

    $this->view->membersEnabled = $membersEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
    $this->view->postedby = $this->_getParam('postedby', 1);
    $this->view->showOptions = $this->_getParam('showoptions', array("likeCount"));
    $this->view->truncationDescription = $this->_getParam('truncationDescription', 100);
    if ($this->view->params['noOfTimes'] > $this->view->countGroup)
      $this->view->params['noOfTimes'] = $this->view->countGroup;

    $this->view->show_buttons = $this->_getParam('show_buttons', array("wishlist", "compare", "comment", "like", 'share', 'facebook', 'twitter', 'pinit'));
  }

}

