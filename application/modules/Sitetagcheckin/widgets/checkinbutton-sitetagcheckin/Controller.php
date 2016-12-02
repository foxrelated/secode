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
class Sitetagcheckin_Widget_CheckinbuttonSitetagcheckinController extends Engine_Content_Widget_Abstract {

  //ACTION FOR SHOWING THE CHECKIN BUTTON
  public function indexAction() {

    //DON'T RENDER IF SUBJECT IS NOT THERE
    $subject = Engine_Api::_()->core()->getSubject();
    if (!$subject) {
      return $this->setNoRender();
    }

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //IF THERE IS NO VIEWER THEN SET NO RENDER
    if (empty($viewer_id)) {
      return $this->setNoRender();
    }

    //GET RESOURCE TYPE   
    $this->view->resource_type = $resource_type = $subject->getType();

    //GET RESOURCE ID
    $this->view->resource_id = $resource_id = $subject->getIdentity();
    $sitetagcheckin_button_view = Zend_Registry::isRegistered('sitetagcheckin_button_view') ? Zend_Registry::get('sitetagcheckin_button_view') : null;

    //HOW TO USE THIS WIDGET
    $this->view->checkin_use = $this->_getParam('checkin_use', 1);
    $this->view->checkin_button = $this->_getParam('checkin_button', 1);
    $this->view->checkin_privacy = $this->_getParam('checkin_privacy', 1);
    $this->view->checkin_verb = $this->_getParam('checkin_verb', 'Check-in here');
    $this->view->checkedinto_verb = $this->_getParam('checkedinto_verb', 'checked-into');
    $this->view->checkin_button_link = $this->_getParam('checkin_button_link', 'Check-in here');
    $this->view->checkin_button_sidebar = $this->_getParam('checkin_button_sidebar', 1);
    $this->view->checkin_total = $this->_getParam('checkin_total', 'Total check-ins here');
    $this->view->checkin_your = $this->_getParam('checkin_your', "You have checked-in here");
    $this->view->checkin_icon = $this->_getParam('checkin_icon', 1);
    $this->view->tab = $this->_getParam('tab', null);

    //CHECK-IN IS ENABLED FOR THIS CONTENT OR NOT
    $content_select = Engine_Api::_()->getDbTable('contents', 'sitetagcheckin')->getContentInformation(array('resource_type' => $resource_type, 'enabled' => 1));

    //SET NO RENDER
    if (empty($content_select) || empty($sitetagcheckin_button_view)) {
      return $this->setNoRender();
    }

    //GET ADDLOCATION TABLE
    $addLocationTable = Engine_Api::_()->getDbtable('addlocations', 'sitetagcheckin');

    //GET CHECKIN COUNT
    $this->view->check_in_count = $addLocationTable->getCheckinCount(null, $resource_id, $resource_type, 'checkin', 'parent_id');

    //GET USER CHECKIN COUNT
    $this->view->user_check_in_count = $addLocationTable->getCheckinCount($viewer, $resource_id, $resource_type, 'checkin', 'parent_id');
  }

}

?>
