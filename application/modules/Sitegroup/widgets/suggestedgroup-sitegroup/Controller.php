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
class Sitegroup_Widget_SuggestedgroupSitegroupController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    // Get subject and check auth
    if ( !Engine_Api::_()->core()->hasSubject('sitegroup_group') ) {
      return $this->setNoRender();
    }
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
    $featured = $this->_getParam('featured', 0);
    $sponsored = $this->_getParam('sponsored', 0);

    //GETTING THE TAG ID OF THIS SITEGROUP ID.
    $items_count = $this->_getParam('itemCount', 5);
    $table = Engine_Api::_()->getDbtable('groups', 'sitegroup');
    $rName = $table->info('name');

    $select = $table->select();
    $this->view->sitereviewEnabled = $sitegroupreviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview');
    if($sitegroupreviewEnabled) {
        $select->from($rName, array('group_id', 'owner_id', 'title', 'photo_id', 'rating', 'featured', 'sponsored', 'creation_date', 'view_count', 'like_count', 'comment_count', 'review_count'));        
    }
    else {
        $select->from($rName, array('group_id', 'owner_id', 'title', 'photo_id', 'featured', 'sponsored', 'creation_date', 'view_count', 'like_count', 'comment_count'));        
    }
    
    $select->order('RAND() DESC ')
            ->where($rName . '.owner_id <> ?', $viewer_id)
            ->where($rName . '.group_id <> ?', $sitegroup->group_id)
            ->where($rName . '.closed = ?', '0')
            ->where($rName . '.draft = ?', '1')
            ->where($rName . '.approved = ?', '1')
            ->where($rName . ".search = ?", 1)
            ->group($rName . '.group_id')
            ->limit($items_count);

    if ( $featured == '1' ) {
      $select = $select->where($rName . '.	featured =?', '0');
    }
    elseif ( $featured == '2' ) {
      $select = $select->where($rName . '.	featured =?', '1');
    }

    if ( $sponsored == '1' ) {
      $select = $select->where($rName . '.	sponsored =?', '0');
    }
    elseif ( $sponsored == '2' ) {
      $select = $select->where($rName . '.	sponsored =?', '1');
    }
    $sqlStr = '';

    if ( !empty($sitegroup->category_id) ) {
      if ( empty($sqlStr) ) {
        $sqlStr = $rName . '.category_id = ' . "'" . $sitegroup->category_id . "'";
      }
      else {
        $sqlStr.= ' OR ' . $rName . '.category_id = ' . "'" . $sitegroup->category_id . "'";
      }
    } 
    
    if ( !empty($sitegroup->price) ) {
      $price = $sitegroup->price;
      $price_min = $price - (int) abs(($price * 10) / 100);
      $price_max = $price + (int) abs(($price * 10) / 100);
      if ( !empty($sqlStr) ) {
        $sqlStr.= ' OR ' . $rName . ".price  BETWEEN " . $price_min . " AND " . $price_max . "";
      }
      else {
        $sqlStr.= $rName . ".price  BETWEEN " . $price_min . " AND " . $price_max . "";
      }
    }

    if ( !empty($sqlStr) ) {
      $select->where($sqlStr);
    }
    
    $this->view->suggestedsitegroup = $results = $table->fetchAll($select);

    // NOT RENDER IF SITEGROUP COUNT ZERO
    if (count($this->view->suggestedsitegroup) <= 0 ) {
      return $this->setNoRender();
    }
  }

}
?>