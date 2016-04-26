<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitetagcheckin_Widget_SitemobileCheckinuserSitetagcheckinController extends Engine_Content_Widget_Abstract {
  protected $_childCount;
  //ACTION FOR DISPLAY THE CHECKIN USERS
  public function indexAction() { 
   
    //DON'T RENDER IF SUBJECT IS NOT THERE
    $subject = Engine_Api::_()->core()->getSubject();
    $sitetagcheckin_user_view = Zend_Registry::isRegistered('sitetagcheckin_user_view') ? Zend_Registry::get('sitetagcheckin_user_view') : null;
    if ((!$subject) || empty($sitetagcheckin_user_view)) {
      return $this->setNoRender();
    }

    //GET RESOURCE TYPE   
    $this->view->resource_type = $resource_type = $subject->getType();

    //GET RESOURCE ID
    $this->view->resource_id = $resource_id = $subject->getIdentity();

    $this->view->checkedin_heading = $this->_getParam('checkedin_heading', 'People Here');

    //SELECT CHCKIN USERS
    $paginator = Engine_Api::_()->getDbtable('addlocations', 'sitetagcheckin')->getCheckinUsers($subject, $this->view->checkedin_users, null, 'public', null);

    //FETCH RESULTS
    if ($paginator) {

      $paginator->setItemCountPerPage(10);

      //GET COUNT
      $this->_childCount = $this->view->check_in_user_count = $check_in_user_count = $paginator->getTotalItemCount();

      //IF THERE IS NO USER THEN SET NO RENDER
      if (empty($check_in_user_count)) {
        return $this->setNoRender();
      } else {
        $this->view->results = $paginator;
      }
    } else {
      return $this->setNoRender();
    }
  }

  public function getChildCount() {
    return $this->_childCount;
  }

}

?>
